<?php
require_once("bittorrent.php");
require_once("user_functions.php");
function GetDayDiff($ts_1, $ts_2, $decimals = 1) {
        if ($ts_1 > $ts_2) {
                $var_days = ($ts_1 - $ts_2) / 86400;
        } elseif ($ts_1 < $ts_2) {
                $var_days = ($ts_2 - $ts_1) / 86400;        
        } else {
                $var_days = 0;
        }
        
        if (is_float($var_days)) {
            return(sprintf('%.'.$decimals.'f ', $var_days));
        } else {
            return($var_days);
        }
}
function docleanup() {
	global $torrent_dir, $signup_timeout, $max_dead_torrent_time, $autoclean_interval, $READPOST_EXPIRY , $CACHE;
	set_time_limit(0);
	ignore_user_abort(1);
	do {
		$res = sql_query("SELECT id FROM torrents");
		$ar = array();
		while ($row = mysql_fetch_array($res)) {
			$id = $row[0];
			$ar[$id] = 1;
		                        }
if (!count($ar))
			break;

		$dp = @opendir($torrent_dir);
		if (!$dp)
			break;

		$ar2 = array();
		while (($file = readdir($dp)) !== false) {
			if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
				continue;
			$id = $m[1];
			$ar2[$id] = 1;
			if (isset($ar[$id]) && $ar[$id])
				continue;
			$ff = $torrent_dir . "/$file";
			unlink($ff);
		}
		closedir($dp);

		if (!count($ar2))
			break;

		$delids = array();
		foreach (array_keys($ar) as $k) {
			if (isset($ar2[$k]) && $ar2[$k])
				continue;
			$delids[] = $k;
			unset($ar[$k]);
		}
		if (count($delids))
			sql_query("DELETE FROM torrents WHERE id IN (" . join(",", $delids) . ")");

		$res = sql_query("SELECT torrent FROM peers GROUP BY torrent");
		$delids = array();
		while ($row = mysql_fetch_array($res)) {
			$id = $row[0];
			if (isset($ar[$id]) && $ar[$id])
				continue;
			$delids[] = $id;
		}
		if (count($delids))
			sql_query("DELETE FROM peers WHERE torrent IN (" . join(",", $delids) . ")");

		$res = sql_query("SELECT torrent FROM files GROUP BY torrent");
		$delids = array();
		while ($row = mysql_fetch_array($res)) {
			$id = $row[0];
			if ($ar[$id])
				continue;
			$delids[] = $id;
		}
		if (count($delids))
			sql_query("DELETE FROM files WHERE torrent IN (" . join(",", $delids) . ")");
	} while (0);

	$deadtime = deadtime();
	sql_query("DELETE FROM peers WHERE last_action < FROM_UNIXTIME($deadtime)");

	$deadtime -= $max_dead_torrent_time;
	sql_query("UPDATE torrents SET visible='no' WHERE visible='yes' AND last_action < FROM_UNIXTIME($deadtime)");
    
    $deadtime = time() - $signup_timeout;
	mysql_query("DELETE FROM users WHERE status = 'pending' AND added < FROM_UNIXTIME($deadtime) AND last_login < FROM_UNIXTIME($deadtime) AND last_access < FROM_UNIXTIME($deadtime)");

	$torrents = array();
	$res = sql_query("SELECT torrent, seeder, COUNT(*) AS c FROM peers GROUP BY torrent, seeder");
	while ($row = mysql_fetch_assoc($res)) {
		if ($row["seeder"] == "yes")
			$key = "seeders";
		else
			$key = "leechers";
		$torrents[$row["torrent"]][$key] = $row["c"];
	}

	$res = sql_query("SELECT torrent, COUNT(*) AS c FROM comments GROUP BY torrent");
	while ($row = mysql_fetch_assoc($res)) {
		$torrents[$row["torrent"]]["comments"] = $row["c"];
	}

	$fields = explode(":", "comments:leechers:seeders");
	$res = sql_query("SELECT id, seeders, leechers, comments FROM torrents");
	while ($row = mysql_fetch_assoc($res)) {
		$id = $row["id"];
		$torr = $torrents[$id];
		foreach ($fields as $field) {
			if (!isset($torr[$field]))
				$torr[$field] = 0;
		}
		$update = array();
		foreach ($fields as $field) {
			if ($torr[$field] != $row[$field])
				$update[] = "$field = " . $torr[$field];
		}
		if (count($update))
			sql_query("UPDATE torrents SET " . implode(",", $update) . " WHERE id = $id");
	}
//=== Update karma seeding bonus
    /******************************************************    
    you will have to play with how much bonus you want to give...
    ie: seedbonus+0.0225 = 0.25 bonus points per hour
        seedbonus+0.125 = 0.5 bonus points per hour
        seedbonus+0.225 = 1 bonus point per hour
    *****************************************************/      
    //======seeding bonus per torrent    
   $res = sql_query("SELECT DISTINCT userid FROM peers WHERE seeder = 'yes'") or sqlerr(__FILE__, __LINE__);
    
   if (mysql_num_rows($res) > 0)
   {
       while ($arr = mysql_fetch_assoc($res))
       {
       $work = sql_query("select count(*) from peers WHERE seeder ='yes' AND userid = $arr[userid]");
       $row_count = mysql_result($work,0,"count(*)");
       sql_query("UPDATE users SET seedbonus = seedbonus+0.250*$row_count WHERE id = $arr[userid]") or sqlerr(__FILE__, __LINE__);
       }
   }     
    //delete old login attempts
    $secs = 1*86400; // Delete failed login attempts per one day.
    $dt = sqlesc(get_date_time(gmtime() - $secs)); // calculate date.
    sql_query("DELETE FROM loginattempts WHERE banned='no' AND added < $dt"); // do job.        
//Delete inactive accounts after 50 days
$secs = 150*86400;
$dt = sqlesc(get_date_time(gmtime() - $secs));
$maxclass = UC_POWER_USER;
$res = sql_query("SELECT * FROM users WHERE status='confirmed' AND parked='no' AND enabled='yes' AND class <= $maxclass AND last_access < $dt");
while ($arr = mysql_fetch_assoc($res))
{
sql_query("DELETE FROM users WHERE id=$arr[id]");
write_log("User : <b>$arr[username]</b> | ID: <b>$arr[id]</b> | Deleted. Reason: <b>User not logged in for over 150 days -- Bye Bye .</b>");
sql_query("DELETE FROM messages WHERE receiver=$arr[id]");
sql_query("DELETE FROM friends WHERE userid=$arr[id]");
sql_query("DELETE FROM snatched WHERE uid=$arr[id]");
}
//remove expired warnings
  $res = sql_query("SELECT id FROM users WHERE warned='yes' AND warneduntil < NOW() AND warneduntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__, __LINE__);
  if (mysql_num_rows($res) > 0)
  {
    $dt = sqlesc(get_date_time());
    $msg = sqlesc("Your warning has been removed. Please keep in your best behaviour from now on.\n");
    while ($arr = mysql_fetch_assoc($res))
    {
      sql_query("UPDATE users SET warned = 'no', warneduntil = '0000-00-00 00:00:00' WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);
      sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
    }
  }
//=== remove VIP status if time up===//
//=== change class to whatever is under your vip class number
  $res = sql_query("SELECT id, modcomment FROM users WHERE vip_added='yes' AND vip_until < NOW() AND donoruntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__, __LINE__);
  if (mysql_num_rows($res) > 0)
  {
    $dt = sqlesc(get_date_time());
    $subject = sqlesc("VIP status removed by system."); //=== comment out this line if you DO NOT have subject in your PM system and change SITE NAME HERE to your site name duh :P
    $msg = sqlesc("Your VIP status has timed out and has been auto-removed by the system. Become a VIP again by donating to $SITENAME, or exchanging some Karma Bonus Points. Cheers!\n");
    while ($arr = mysql_fetch_assoc($res))
    {
        ///---AUTOSYSTEM MODCOMMENT---//
     $modcomment = htmlspecialchars($arr["modcomment"]);
     $modcomment =  gmdate("Y-m-d") . " - VIP status removed by -AutoSystem.\n". $modcomment;
     $modcom =  sqlesc($modcomment);
     ///---end
      sql_query("UPDATE users SET class = '1', vip_added = 'no', vip_until = '0000-00-00 00:00:00', modcomment = $modcom WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);
      sql_query("INSERT INTO messages (sender, receiver, added, msg, subject, poster) VALUES(0, $arr[id], $dt, $msg, $subject, 0)") or sqlerr(__FILE__, __LINE__);
      status_change($arr['id']);
      // sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); //=== use this line (and comment out the above line) if you DO NOT have subject in your PM system
    }
  }
//===end===//
//===clear funds after one month
$secs = 28*86400;
$dt = sqlesc(get_date_time(gmtime() - $secs));
sql_query("DELETE FROM funds WHERE added < $dt");
//===end
//=== remove donor status if time up AND set class back to power user... remember to set the class number for your system===//
$res = sql_query("SELECT id, modcomment FROM users WHERE donor='yes' AND donoruntil < NOW() AND donoruntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
$dt = sqlesc(get_date_time());
$subject = sqlesc("Donor status removed by system.");
$msg = sqlesc("Your Donor status has timed out and has been auto-removed by the system, and your VIP status has been removed. We would like to thank you once again for your support to $SITENAME. If you wish to re-new your donation,Visit the site paypal link. Cheers!\n");
while ($arr = mysql_fetch_assoc($res))
{
///---AUTOSYSTEM MODCOMMENT---//
$modcomment = htmlspecialchars($arr["modcomment"]);
$modcomment = gmdate("Y-m-d") . " - Donor status removed by -AutoSystem.\n". $modcomment;
$modcom = sqlesc($modcomment);
///---AUTOSYSTEM MODCOMMENT---//
sql_query("UPDATE users SET class = '1', donor = 'no', donoruntil = '0000-00-00 00:00:00', modcomment = $modcom WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);
sql_query("INSERT INTO messages (sender, receiver, added, msg, subject, poster) VALUES(0, $arr[id], $dt, $msg, $subject, 0)") or sqlerr(__FILE__, __LINE__);
status_change($arr['id']);
}
}
//===end===//
//=== remove custom smilies if time up
  $res = sql_query("SELECT id FROM users WHERE smile_until < NOW() AND smile_until <> '0000-00-00 00:00:00'") or sqlerr(__FILE__, __LINE__);
  if (mysql_num_rows($res) > 0)
  {
    $dt = sqlesc(get_date_time());
    $subject = sqlesc("Custom smilies removed by system."); //=== comment out this line if you DO NOT have subject in your PM system and change SITE NAME HERE to your site name duh :P
    $msg = sqlesc("Your Custom smilies have timed out and has been auto-removed by the system. If you would like to have them again, exchange some Karma Bonus Points again. Cheers!\n");
    while ($arr = mysql_fetch_assoc($res))
    {
      sql_query("UPDATE users SET  smile_until = '0000-00-00 00:00:00' WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);
      sql_query("INSERT INTO messages (sender, receiver, added, msg, subject, poster) VALUES(0, $arr[id], $dt, $msg, $subject, 0)") or sqlerr(__FILE__, __LINE__);
     // sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__); //=== use this line (and comment out the above line) if you DO NOT have subject in your PM system
    }
  }
// promote power users
	$limit = 25*1024*1024*1024;
	$minratio = 1.05;
	$maxdt = sqlesc(get_date_time(gmtime() - 86400*28));
	$res = sql_query("SELECT id FROM users WHERE class = 0 AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) > 0)
	{
		$dt = sqlesc(get_date_time());
		$msg = sqlesc("Congratulations, you have been auto-promoted to [b]Power User[/b]. :)\nYou can now download dox over 1 meg and view torrent NFOs.\n");
		while ($arr = mysql_fetch_assoc($res))
		{
			sql_query("UPDATE users SET class = 1 WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);
			sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
		        status_change($arr['id']);
                      }
	}
	// demote power users
	$minratio = 0.95;
	$res = sql_query("SELECT id FROM users WHERE class = 1 AND uploaded / downloaded < $minratio") or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($res) > 0)
	{
		$dt = sqlesc(get_date_time());
		$msg = sqlesc("You have been auto-demoted from [b]Power User[/b] to [b]User[/b] because your share ratio has dropped below $minratio.\n");
		while ($arr = mysql_fetch_assoc($res))
		{
			sql_query("UPDATE users SET class = 0 WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);
			sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
		        status_change($arr['id']);
                }
	}
	 // promote user to mod
	 /*
$limit = 2000*1024*1024*1024;
$minratio = 5.30;
$maxdt = sqlesc(get_date_time(gmtime() - 86400*365));
$res = mysql_query("SELECT id FROM users WHERE class = 0 AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
$dt = sqlesc(get_date_time());
$msg = sqlesc("Congratulations, you have been auto-promoted to [b]Moderator[/b] .");
while ($arr = mysql_fetch_assoc($res))
{
mysql_query("UPDATE users SET class = 4 WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES(0, $arr[id], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
}
}
*/
////autoleech warning script and disable downloads////
$minratio = 0.5; // ratio < 0.5
$downloaded = 4*1024*1024*1024; // + 4 GB
$length = 3*7; // Give 3 weeks to let them sort there shit
$res = sql_query("SELECT id FROM users WHERE class >= 0 AND leechwarn = 'no' AND uploaded / downloaded < $minratio AND downloaded >= $downloaded") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
    $dt = sqlesc(get_date_time());
    $subject = sqlesc("Auto Leech Warn."); 
    $msg = sqlesc("You have been warned because of having low ratio. You need to get a ratio 0.7 within the next 3 weeks or your downloads will be disabled.");
    $until = sqlesc(get_date_time(gmtime() + ($length*86400)));
    while ($arr = mysql_fetch_assoc($res))
    {
        writecomment($arr[id],"LeechWarned by System - Low Ratio.");    
        sql_query("UPDATE users SET leechwarn = 'yes', leechwarnuntil = $until WHERE id=$arr[id]") or sqlerr(__FILE__, __LINE__);
        sql_query("INSERT INTO messages (sender, receiver, added, subject, msg, poster) VALUES(0, $arr[id], $dt, $subject,  $msg, 0)") or sqlerr(__FILE__, __LINE__);
    }
}
//end//
//remove warn section 
$minratio = 0.7; // ratio > 0.7
$res = sql_query("SELECT id FROM users WHERE leechwarn = 'yes' AND uploaded / downloaded >= $minratio") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
    $dt = sqlesc(get_date_time());
    $subject = sqlesc("Auto Leech Warn."); 
     $msg = sqlesc("Your warning for a low ratio has been removed. We highly recommend you to keep your ratio positive to avoid being automatically warned again.\n");
    while ($arr = mysql_fetch_assoc($res))
    {
        writecomment($arr[id],"LeechWarning removed by System.");    
        sql_query("UPDATE users SET leechwarn = 'no',leechwarnuntil = '0000-00-00 00:00:00' WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);
        sql_query("INSERT INTO messages (sender, receiver, added, subject, msg, poster) VALUES(0, $arr[id], $dt, $subject, $msg, 0)") or sqlerr(__FILE__, __LINE__);
    }
}
//end//
//remove warn section re-enable downloads
$minratio = 0.7; // ratio > 0.7
$res = sql_query("SELECT id FROM users WHERE downloadpos = 'no' AND leechwarn = 'yes' AND uploaded / downloaded >= $minratio") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
    $dt = sqlesc(get_date_time());
    $subject = sqlesc("Auto Leech Warn."); 
     $msg = sqlesc("Your warning for a low ratio has been removed and your downloads enabled. We highly recommend you to keep your ratio positive to avoid being automatically warned again.\n");
    while ($arr = mysql_fetch_assoc($res))
    {
        writecomment($arr[id],"LeechWarning removed and downloads re-enabled by System.");    
        sql_query("UPDATE users SET leechwarn = 'no', downloadpos = 'yes', leechwarnuntil = '0000-00-00 00:00:00' WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);
        sql_query("INSERT INTO messages (sender, receiver, added, subject, msg, poster) VALUES(0, $arr[id], $dt, $subject, $msg, 0)") or sqlerr(__FILE__, __LINE__);
    }
}
//end//
//Disable download of low ratio users
$dt = sqlesc(get_date_time()); // take date time
$res = sql_query("SELECT id FROM users WHERE enabled = 'yes' AND leechwarn = 'yes' AND leechwarnuntil < $dt") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
    while ($arr = mysql_fetch_assoc($res))
    {
        writecomment($arr[id],"Download disabled by System because of LeechWarning expired contact site staff for advice.");
        sql_query("UPDATE users SET downloadpos = 'no', leechwarnuntil = '0000-00-00 00:00:00' WHERE id = $arr[id]") or sqlerr(__FILE__, __LINE__);
    }
}
//end auto-disable downloads//
/////////////Delete old pms////////////
$secs = 15*86400; //change this to fit your needs
$dt = sqlesc(get_date_time(gmtime() - $secs));
sql_query("DELETE FROM messages WHERE added < $dt");
//delete from shoutbox after 2 days
        $secs = 2*86400;
        $dt = sqlesc(get_date_time(gmtime() - $secs));
        sql_query("DELETE FROM shoutbox WHERE " . time() . " - date > $secs") or sqlerr(__FILE__, __LINE__);
      // Update stats
	$seeders = get_row_count("peers", "WHERE seeder='yes'");
	$leechers = get_row_count("peers", "WHERE seeder='no'");
	sql_query("UPDATE avps SET value_u=$seeders WHERE arg='seeders'") or sqlerr(__FILE__, __LINE__);
	sql_query("UPDATE avps SET value_u=$leechers WHERE arg='leechers'") or sqlerr(__FILE__, __LINE__);
// update forum post/topic count
    $forums = @sql_query("SELECT t.forumid, count( DISTINCT p.topicid ) AS topics, count( * ) AS posts FROM posts p LEFT JOIN topics t ON t.id = p.topicid LEFT JOIN forums f ON f.id = t.forumid GROUP BY t.forumid");
    while ($forum = mysql_fetch_assoc($forums))
    {/*
        $postcount = 0;
        $topiccount = 0;
        $topics = sql_query("select id from topics where forumid=$forum[id]");
        while ($topic = mysql_fetch_assoc($topics))
        {
            $res = sql_query("select count(*) from posts where topicid=$topic[id]");
            $arr = mysql_fetch_row($res);
            $postcount += $arr[0];
            ++$topiccount;
        } */
        @sql_query("update forums set postcount={$forum['posts']}, topiccount={$forum['topics']} where id={$forum['forumid']}");
    } 
// Delete Orphaned announcement_processors
    sql_query("DELETE announcement_process FROM announcement_process LEFT JOIN users ON announcement_process.user_id = users.id WHERE users.id IS NULL");
// Delete expired announcements and processors
    sql_query("DELETE FROM announcement_main WHERE expires < ".sqlesc(get_date_time()));
    sql_query("DELETE announcement_process FROM announcement_process LEFT JOIN announcement_main ON announcement_process.main_id = announcement_main.main_id WHERE announcement_main.main_id IS NULL");
////////auto-delete old torrents////////     
$days = 5;
$dt = sqlesc(get_date_time(gmtime() - ($days * 86400)));
$days_la = 7;
$dt_la = sqlesc(get_date_time(gmtime() - ($days_la * 86400)));
$res = sql_query("SELECT id, name FROM torrents WHERE added < $dt AND seeders=0 AND leechers=0 AND last_action < $dt_la ");
while ($arr = mysql_fetch_assoc($res))
{
@unlink("$torrent_dir/$arr[id].torrent");
sql_query("DELETE FROM torrents WHERE id=$arr[id]");
sql_query("DELETE FROM peers WHERE torrent=$arr[id]");
sql_query("DELETE FROM snatched WHERE torrent=$arr[id]");
sql_query("UPDATE avps SET value_d='0000-00-00 00:00:00', value_s='' WHERE arg='bestfilmofweek' AND value_s=".$arr["id"]);
sql_query("DELETE FROM comments WHERE torrent=$arr[id]");
sql_query("DELETE FROM files WHERE torrent=$arr[id]");
write_log("Torrent $arr[id] ($arr[name]) was deleted by system (older than $days days and no seeders or leechers in 7 day's)");
$message = "Torrent $arr[id] ($arr[name]) was deleted by system (older than $days days and no seeders or leechers in 7 day's)";
autoshout($message);
}
// delete old dox
     $days = 15;
     $dt = sqlesc(get_date_time(gmtime() - ($days * 86400)));
     $res = sql_query("SELECT id, filename FROM dox WHERE added < $dt");
     while ($arr = mysql_fetch_assoc($res))
     {
     @unlink("/home/mullruss/public_html/dox/$arr[filename]");
     sql_query("DELETE FROM dox WHERE id=$arr[id]");
     }
/// freeslots
$dt = sqlesc(get_date_time(gmtime() - (14 * 86400))); /// is set to expire in 14 days
sql_query("UPDATE freeslots SET doubleup = 'no' WHERE addedup<$dt") or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE freeslots SET free = 'no' WHERE addedfree<$dt") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM freeslots WHERE doubleup = 'no' AND free = 'no'") or sqlerr(__FILE__, __LINE__);
// best film of the week by dokty - tbdev.net
$catmovieids = "3,5,6,10,11"; // change this to ur movies category ids with format of x,x,x
$dt = sqlesc(get_date_time(gmtime() - 604800));
$res = sql_query("SELECT value_s FROM avps WHERE value_d < $dt AND avps.arg='bestfilmofweek'");
if (mysql_affected_rows() > 0) {
$res = sql_query("SELECT id, name FROM torrents WHERE times_completed > 0 AND category IN (".$catmovieids.") ORDER BY times_completed DESC LIMIT 1");
$arr = mysql_fetch_assoc($res);
sql_query("UPDATE avps SET value_d='" . get_date_time() . "', value_s=".$arr["id"]." WHERE arg='bestfilmofweek'");
write_log("Torrent ".$arr["id"]." (".htmlentities($arr["name"]).") was set 'Best Film of the Week' by system");
}
/////////////////////////happyhour////
$f = "$CACHE/happyhour.txt";                  
    $happy = unserialize(file_get_contents($f));
    $happyHour = strtotime($happy["time"]);
    
    $curDate = time();
    $happyEnd = $happyHour + 3600;
    
    if ($happy["status"] == 0){
    write_log("Happy hour was @ ".date("Y-m-d H:i" ,$happyHour)." and Catid ".$happy["catid"]." ");
    happyFile("set");
    }
    elseif (($curDate > $happyEnd) && $happy["status"] == 1 )
    happyFile("reset");
//////////////end///////    
/////cddvd's fully automatic hitrun script--modified for new and improved snatchlist by Bigjoos/////////
$pwsecs = 7*86400;
$pwdt = sqlesc(get_date_time(time() + $pwsecs));
$now = sqlesc(get_date_time());
//Remove warning if seeding//
$res = mysql_query("SELECT userid, torrentid, torrent_name, hit_run FROM snatched WHERE hit_run='2'");
if (mysql_num_rows($res) > 0) {
while ($arr = mysql_fetch_assoc($res)) {
$user=$arr['userid'];
$torrent=$arr['torrentid'];
$hit_run=$arr['hit_run'];
$torrentname=$arr['torrent_name'];
$res1 = mysql_query("SELECT * FROM peers WHERE seeder='yes' AND userid=$user AND torrent = $torrent") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res1) > 0) {
$res3=mysql_query("SELECT class,hit_run_total FROM users WHERE id = $user");
$arr3 = mysql_fetch_array($res3);
$hrtotal = $arr3['hit_run_total'];
$hrtotal = $hrtotal - 1;
if ($hrtotal < 0) {
$hrtotal=0;
}
$class = $arr3['class'];
if ($class >= '4') {
mysql_query("UPDATE snatched SET hit_run = '3' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
write_log("Checking Take Warn Off Script UserId : $user Class : $class Hit-Run Script : $hit_run");
} else {
mysql_query("UPDATE snatched SET hit_run = '0' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
$msg = sqlesc("Your Warning Has Been Removed for Snatched Id : $torrent - $torrentname - Thank You For Re-seeding.If you fail to Re-Seed to a 0.8 Ratio or 36 hours it will be re-instated - Hit-Run Total=$hrtotal\n");
$subject = sqlesc("Warning Removal.");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__);
$modcomment = date("Y-m-d") . " - Your Warning Has Been Removed for Snatched Id : $torrent - $torrentname - Re-Seed to a 0.8 Ratio or 36 hours or it will be re-instated.\n";
$modcom = sqlesc($modcomment);
if ($hrtotal<6) {
mysql_query("UPDATE users SET warned = 'no', downloadpos='yes', warneduntil = '0000-00-00 00:00:00', hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Take pre warn off & Re-enable Downloads (Reseed to 0.8 or 36 hours) $user Snatched Id : ($torrent) - ($torrentname) - Lookup:$pwdt Now:$now Hit-Run Script:$hit_run");
$msg = sqlesc("Your HitRun Total Is now below limit - downloads are enabled again.Thanks for seeding!\n");
$subject = sqlesc("Hit Run total updated.");
} else {
mysql_query("UPDATE users SET warned = 'no', warneduntil = '0000-00-00 00:00:00', hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Take warn off but still disabled Dloads (Reseeded to 0.8 or 36 hours) $user Snatched Id ($torrent) - ($torrentname) - Lookup : $pwdt Now : $now Hit-Run Script : $hit_run");
$msg = sqlesc("Your HitRun Total Is still over the allowed limit - downloads are still Disabled\n");
$subject = sqlesc("Hit Run total updated.");
}
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__);
}}}}
//Remove warning if 0.8 ratio but stopped seeding before next check on cleanup//
$res = mysql_query("SELECT userid, torrentid, torrent_name, seedtime, downloaded, uploaded, hit_run FROM snatched WHERE hit_run='2'");
if (mysql_num_rows($res) > 0) {
while ($arr = mysql_fetch_assoc($res)) {
$user=$arr['userid'];
$torrent=$arr['torrentid'];
$hit_run=$arr['hit_run'];
$torrentname=$arr['torrent_name'];
$sdtime = $arr['seedtime'] / 86400;
$uploaded = (($arr['downloaded']/100)*80);
if ($sdtime >= '1.5' OR $arr['uploaded'] >= $uploaded) {
$res1=mysql_query("SELECT hit_run_total FROM users WHERE id = $user");
$arr1 = mysql_fetch_array($res1);
$hrtotal = $arr1['hit_run_total'];
$hrtotal =$hrtotal - 1;
if ($hrtotal < 0) {
$hrtotal=0;
}
mysql_query("UPDATE snatched SET hit_run = '3' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
$subject = sqlesc("Warning Removed");
$msg = sqlesc("Your Warning Has Been Removed for Snatch Id : $torrent - $torrentname - Thank You For Re-seeding to a 0.8 Ratio OR 36 hours H&R Total=$hrtotal\n");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__);
$modcomment = date("Y-m-d") . " - Your Warning Has Been Removed for Snatched Id : $torrent - $torrentname - User Re-Seeded to a 0.8 Ratio or 36 hours\n";
$modcom = sqlesc($modcomment);
If ($hrtotal<6) {
mysql_query("UPDATE users SET warned = 'no', downloadpos='yes', warneduntil = '0000-00-00 00:00:00', hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Take warn off (Reseeded) $user Snatched Id : ($torrent) - ($torrentname) Lookup : $pwdt Now : $now Hit-Run Script : $hit_run");
} else {
mysql_query("UPDATE users SET warned = 'no', warneduntil = '0000-00-00 00:00:00', hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Take warn off (Reseeded) $user Snatched Id : ($torrent) - ($torrentname) - Lookup : $pwdt Now : $now Hit-Run Script : $hit_run");
$msg = sqlesc("Your Hit-Run Total Is still over the allowed limit downloads are still Disabled - Hit-Run Total=$hrtotal\n");
$subject = sqlesc("Hit run limit exceeded.");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__); //== uncomment then comment out above query to use subject in pm's
}}}}
///warn after re-seed request from tracker if not re-seeded////
$res = mysql_query("SELECT userid, torrentid, torrent_name, seedtime, uploaded, downloaded, hit_run FROM snatched WHERE prewarn<NOW() AND hit_run='1' AND finished='yes'");
if (mysql_num_rows($res) > 0) {
while ($arr = mysql_fetch_assoc($res)) {
$user=$arr['userid'];
$torrent=$arr['torrentid'];
$torrentname = $arr['torrent_name'];
$hit_run=$arr['hit_run'];
$sdtime = $arr['seedtime'] / 86400;
$uploaded = (($arr['downloaded']/100)*80);
$up=mksize($arr['uploaded']);
$down=mksize($arr['downloaded']);
$ratio=number_format((100/$down)*$up);
$res2=mysql_query("SELECT class,hit_run_total FROM users WHERE id =$user");
$arr2 = mysql_fetch_array($res2);
$class = $arr2['class'];
$hrtotal = $arr2['hit_run_total'];
$hrtotal = $hrtotal +1;
$rescheck = mysql_query("SELECT * FROM peers WHERE seeder='yes' AND userid = $user AND torrent=$torrent") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($rescheck) < 1) {
if ($class >= '4' OR $sdtime >= '1.5' OR $arr['uploaded'] >= $uploaded) {
write_log("Did not warn UserId :$user Class : $class Lookup : $pwdt Now : $now Hit-Run Script : $hit_run");
mysql_query("UPDATE snatched SET hit_run = '3' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
} else {
$msg = sqlesc("Warning You Refused Or Were Unable To Re-Seed Snatched Id : ($torrent) - ($torrentname) - within the 3 hours so you have received an automatic 7 days warning If you Re-seed it Now or at a later Time/Date this warning will be automatically removed from your account Hit and run Total= $hrtotal Stats : Up : $up Down : $down Ratio : $ratio\n");
$subject = sqlesc("Reseed Warning.");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__);
mysql_query("UPDATE snatched SET hit_run = '2' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
$modcomment = date("Y-m-d") . " - Warning You Refused To Re-Seed Snatched Id : $torrent - $torrentname - after 3 hours. If you Now Re-Seed It At Any Time It Will Still Be automatically Removed From Your Account - Stats : Up: $up Down : $down Ratio : $ratio\n";
$modcom = sqlesc($modcomment);
If ($hrtotal<=5){
mysql_query("UPDATE users SET warned = 'yes', warneduntil = $pwdt,hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Warned user - $user Snatch Id : ($torrent) - ($torrentname) Stats : Lookup : $pwdt Now : $now Up : $up Down : $down Ratio : $ratio Hit-Run Script : $hit_run");
} else {
mysql_query("UPDATE users SET warned = 'yes', downloadpos='no', warneduntil = $pwdt,hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Warned User & Disabled Dloads $user Snatched Id : ($torrent) - ($torrentname) - Stats : Lookup : $pwdt Now : $now Up : $up Down : $down Ratio : $ratio Hit-Run Script : $hit_run");
$msg = sqlesc("Your Hit and Run Total Is Now Over The Allowed Limit. All Downloads are Now Disabled.\n");
$subject = sqlesc("Download Disable Warning.");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__);
}}}}}
//HitRun - Send a request to reseed if ratio less then 0.8 on torrent and stopped seeding//
$secs = 0.0416*86400;
$dt = sqlesc(get_date_time(time() - $secs));
$wsecs = 0.125*86400;
$wdt = sqlesc(get_date_time(time() + $wsecs));
$res = mysql_query("SELECT userid, torrentid, torrent_name, seedtime, uploaded, downloaded, hit_run FROM snatched WHERE uploaded < ((downloaded/100)*80) and UNIX_TIMESTAMP($dt)<UNIX_TIMESTAMP(last_action) and hit_run='0' and finished='yes'");
if (mysql_num_rows($res) > 0) {
while ($arr = mysql_fetch_assoc($res)) {
$user=$arr['userid'];
$torrent=$arr['torrentid'];
$hit_run=$arr['hit_run'];
$torrentname=$arr['torrent_name'];
$sdtime = $arr['seedtime'] / 86400;
$uploaded = (($arr['downloaded']/100)*80);
$up=mksize($arr['uploaded']);
$down=mksize($arr['downloaded']);
$ratio=number_format((100/$down)*$up);
$res2=mysql_query("SELECT class FROM users WHERE id = $user");
$arr2 = mysql_fetch_array($res2);
$class = $arr2['class'];
$res3 = mysql_query("SELECT * FROM peers WHERE seeder='yes' AND userid = $user AND torrent=$torrent") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res3) < 1) {
if ($class >= '4' OR $sdtime >= '1.5' OR $arr['uploaded'] >= $uploaded) {
mysql_query("UPDATE snatched SET hit_run = '3' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
} else {
$msg = sqlesc("Seeding Request - Please Re-Seed Snatched Id : $torrent - $torrentname - If you fail to do so after 3 hours you will get an automatic warning Unless -- You Re-Seed, If you do Re-seed it Now Or At Anytime (Even After A Warning Has Been GIven) The Warning Is automatically removed from your account $dt Up : $up Down : $down \n.");
$subject = sqlesc("HnR Reseed Request.");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__); //== uncomment then comment out above query to use subject in pm's
mysql_query("UPDATE snatched SET prewarn = $wdt, hit_run = '1' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
$modcomment = date("Y-m-d") . " - Re-Seed Request Sent for Snatched Id : $torrent - $torrentname - Up : $up Down : $down Ratio : $ratio\n";
$modcom = sqlesc($modcomment);
mysql_query("UPDATE users SET modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Pre-Warn user UserId : $user Snatched Id : ($torrent) - ($torrentname) - Lookup : $dt Now : $now Up : $up Down : $down Ratio : $ratio Hit-Run Script : $hit_run");
}}}}
///////end hitrun script////////
$secs = 24 * 60 * 60; //  24Hours * 60 minutes * 60 seconds...
$dt = sqlesc(get_date_time(gmtime() - $secs));
mysql_query("UPDATE users SET ip = '' WHERE last_access < $dt");
write_log("---------------------------------------Site Auto Clean up Complete----------------------------------------");
$message = "Code Run Test Message - Site Auto Clean Up Complete";
autoshout($message);
}
?>