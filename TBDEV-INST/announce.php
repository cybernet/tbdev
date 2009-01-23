<?php
define('IN_ANNOUNCE',true);
require_once("include/bittorrent.php");
require_once("include/benc.php");

function err($msg)
{
	benc_resp(array("failure reason" => array(type => "string", value => $msg)));
	exit();
}

function benc_resp($d)
{
	benc_resp_raw(benc(array(type => "dictionary", value => $d)));
}

function benc_resp_raw($x)
{
	header("Content-Type: text/plain");
	header("Pragma: no-cache");
	print($x);
}

foreach (array("passkey","info_hash","peer_id","ip","event") as $x)

$GLOBALS[$x] = "" . $_GET[$x];





foreach (array("port","downloaded","uploaded","left") as $x)

$GLOBALS[$x] = 0 + $_GET[$x];



if (strpos($passkey, "?")) {

  $tmp = substr($passkey, strpos($passkey, "?"));

$passkey = substr($passkey, 0, strpos($passkey, "?"));

$tmpname = substr($tmp, 1, strpos($tmp, "=")-1);

$tmpvalue = substr($tmp, strpos($tmp, "=")+1);

$GLOBALS[$tmpname] = $tmpvalue;

}



foreach (array("passkey","info_hash","peer_id","port","downloaded","uploaded","left") as $x)

if (!isset($x)) err("Missing key: $x");



foreach (array("info_hash","peer_id") as $x)

if (strlen($GLOBALS[$x]) != 20) err("Invalid $x (" . strlen($GLOBALS[$x]) . " - " . urlencode($GLOBALS[$x]) . ")");



if (strlen($passkey) != 32) err("Invalid passkey (" . strlen($passkey) . " - $passkey)");



//if (empty($ip) || !preg_match('/^(d{1,3}.){3}d{1,3}$/s', $ip))

$ip = getip();
$rsize = 50;
foreach(array("num want", "numwant", "num_want") as $k)
{
	if (isset($_GET[$k]))
	{
		$rsize = 0 + $_GET[$k];
		break;
	}
}

$agent = $_SERVER["HTTP_USER_AGENT"];

// Deny access made with a browser...
if (ereg("^Mozilla\\/", $agent) || ereg("^Opera\\/", $agent) || ereg("^Links ", $agent) || ereg("^Lynx\\/", $agent))
	err("torrent not registered with this tracker");

if (!$port || $port > 0xffff)
	err("invalid port");

if (!isset($event))
	$event = "";

$seeder = ($left == 0) ? "yes" : "no";
// Banned Clients - By Petr1fied
$filename = "include/banned_clients.txt";
if (filesize($filename)==0 || !file_exists($filename))
$banned_clients=array();
else
{
$handle = fopen($filename, "r");
$banned_clients = unserialize(fread($handle, filesize($filename)));
fclose($handle);
}

dbconn(false);

$valid = @mysql_fetch_row(@mysql_query("SELECT COUNT(*) FROM users WHERE passkey=" . sqlesc($passkey)));

if ($valid[0] != 1) err("Invalid passkey! Re-download the .torrent from $BASEURL");

$res = mysql_query("SELECT id, added, banned, vip, multiplicator, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts, countstats FROM torrents WHERE " . hash_where("info_hash", $info_hash));

$torrent = mysql_fetch_assoc($res);
if (!$torrent)
	err("torrent not registered with this tracker");

$torrentid = $torrent["id"];
$fields = "seeder, peer_id, ip, port, uploaded, downloaded, userid, last_action, (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(last_action)) AS announcetime, UNIX_TIMESTAMP(last_action) AS ts, UNIX_TIMESTAMP(NOW()) AS nowts, UNIX_TIMESTAMP(prev_action) AS prevts";

$numpeers = $torrent["numpeers"];
$limit = "";
if ($numpeers > $rsize)
	$limit = "ORDER BY RAND() LIMIT $rsize";
// If user is a seeder, then only supply leechers.
// This helps with the zero upload cheat, as it doesn't supply anyone who has
// a full copy.
$wantseeds = "";
if ($seeder == 'yes')
        $wantseeds = "AND seeder = 'no'";

$res = mysql_query("SELECT $fields FROM peers WHERE torrent = $torrentid AND connectable = 'yes' $wantseeds $limit") or err('peers query failure');
////////////////////Compact mode begin/////////////////////////////
if($_GET['compact'] != 1)
{
$resp = "d" . benc_str("interval") . "i" . $announce_interval . "e" . benc_str("private") . 'i1e' . benc_str("peers") . "l";
}
else
{
$resp = "d" . benc_str("interval") . "i" . $announce_interval . "e5:"."peers" ;
}
$peer = array();
$peer_num = 0;
while ($row = mysql_fetch_assoc($res))
{
if($_GET['compact'] != 1)
{
$row["peer_id"] = hash_pad($row["peer_id"]);
if ($row["peer_id"] === $peer_id)
{
$self = $row;
continue;
}
$resp .= "d" .
benc_str("ip") . benc_str($row["ip"]);
if (!$_GET['no_peer_id']) {
$resp .= benc_str("peer id") . benc_str($row["peer_id"]);
}
$resp .= benc_str("port") . "i" . $row["port"] . "e" .
"e";
}
else
{
$peer_ip = explode('.', $row["ip"]);
$peer_ip = pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]);
$peer_port = pack("n*", (int)$row["port"]);
$time = intval((time() % 7680) / 60);
if($_GET['left'] == 0)
{
$time += 128;
}
$time = pack("C", $time);
$peer[] = $time . $peer_ip . $peer_port;
$peer_num++;
}
}
if ($_GET['compact']!=1)
$resp .= "ee";
else
{
$o = "";
for($i=0;$i<$peer_num;$i++)
{
$o .= substr($peer[$i], 1, 6);
}
$resp .= strlen($o) . ':' . $o . 'e';
}
$selfwhere = "torrent = $torrentid AND " . hash_where("peer_id", $peer_id);
///////////////////////////// End compact mode////////////////////////////////
if (!isset($self))
{
	$res = mysql_query("SELECT $fields FROM peers WHERE $selfwhere");
	$row = mysql_fetch_assoc($res);
	if ($row)
	{
		$userid = $row["userid"];
		$self = $row;
	}
}
/////////////up/down stats/////////////
//// Up/down stats ////////////////////////////////////////////////////////////
// Anti Flood Code
// This code is designed to ensure that no more than two announces can occur
// within a 10 second period. This is to ensure that Flooding doesn't happen
$announce_wait = 10;
if( isset($self) && ($self['prevts'] > ($self['nowts'] - $announce_wait )) )
{
err('There is a minimum announce time of ' . $announce_wait . ' seconds');
}
if (!isset($self))
{
$valid = @mysql_fetch_row(@mysql_query("SELECT COUNT(*) FROM peers WHERE torrent=$torrentid AND passkey=" . sqlesc($passkey)));
if ($valid[0] >= 2 && $seeder == 'no') err("Connection limit exceeded! You may only leech from one location at a time.");
if ($valid[0] >= 3 && $seeder == 'yes') err("Connection limit exceeded!");
$rz = mysql_query("SELECT id, tlimitseeds, tlimitleeches, tlimitall, uploaded, downloaded, parked, class FROM users WHERE passkey=".sqlesc($passkey)." AND enabled = 'yes' ORDER BY last_access DESC LIMIT 1") or err("Tracker error 2");
if ($MEMBERSONLY && mysql_num_rows($rz) == 0)
err("Unknown passkey. Please redownload the torrent !");
        $az = mysql_fetch_assoc($rz);
        $userid = $az["id"];
        /////// Torrent-Limit 
        if ($az["tlimitall"] >= 0) {
        $arr = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) AS cnt FROM peers WHERE userid=$userid"));
        $numtorrents = $arr["cnt"];
        $arr = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) AS cnt FROM peers WHERE userid=$userid AND seeder='yes'"));
        $seeds = $arr["cnt"];
        $leeches = $numtorrents - $seeds;
        $limit = get_torrent_limits($az);

        if (   ($limit["total"] > 0)
            &&(($numtorrents >= $limit["total"])
            || ($left == 0 && $seeds >= $limit["seeds"])
            || ($left > 0 && $leeches >= $limit["leeches"])))
                err("Maximum Torrent-Limit reached ($limit[seeds] Seeds, $limit[leeches] Leeches, $limit[total] total)");

    }
        if ($az["vip"] =="yes" && get_user_class() < UC_VIP)
        err("VIP Access Required, You must be a VIP In order to view details or download this torrent! You may become a Vip By Donating to our site. Donating ensures we stay online to provide you more Vip-Only Torrents!");
        //==uncomment to enable wait time
        /* 
        if ($az["class"] < UC_VIP)
	    {
		$gigs = $az["uploaded"] / (1024*1024*1024);
		$elapsed = floor((gmtime() - $torrent["ts"]) / 3600);
		$ratio = (($az["downloaded"] > 0) ? ($az["uploaded"] / $az["downloaded"]) : 1);
		if ($ratio < 0.5 || $gigs < 5) $wait = 0;
		elseif ($ratio < 0.65 || $gigs < 6.5) $wait = 0.;
		elseif ($ratio < 0.8 || $gigs < 8) $wait = 0;
		elseif ($ratio < 0.95 || $gigs < 9.5) $wait = 0;
		else $wait = 0;
		if ($elapsed < $wait)
		err("Not authorized (" . ($wait - $elapsed) . "h) - READ THE FAQ!");
	    }*/
        }
        else
        {
        // Get the last uploaded amount from user account for reference and store it in $last_up
        $rst = mysql_query("SELECT class, highspeed, uploaded FROM users WHERE id = $userid ") or err("Tracker error 5");
        $art = mysql_fetch_array($rst);
        $last_up = $art["uploaded"];
        $class = $art["class"];
        $highspeed = $art["highspeed"];
        $upthis = max(0, $uploaded - $self["uploaded"]);
        $downthis = max(0, $downloaded - $self["downloaded"]);
        $upspeed = ($upthis > 0 ? $upthis / $self["announcetime"] : 0);
        $downspeed = ($downthis > 0 ? $downthis / $self["announcetime"] : 0);
        $announcetime = ($self["seeder"] == "yes" ? "seedtime = seedtime + $self[announcetime]" : "leechtime = leechtime + $self[announcetime]");       
        /// freeslots/free_for_all  
        $resfree = mysql_query("SELECT free_for_all FROM free_download");
        $arrfree = mysql_fetch_assoc($resfree);
        $resfs = mysql_query("SELECT * FROM freeslots WHERE torrentid=$torrentid && userid=$userid");
        $arrfs = mysql_fetch_assoc($resfs);
        $pq = $arrfs["torrentid"] == $torrentid && $arrfs["userid"] == $userid;
        $multiplicator = $torrent['multiplicator'];
        if ($multiplicator == "2")
        $upthis = $upthis *2;
        elseif ($multiplicator == "3")
        $upthis = $upthis *3;
        elseif ($multiplicator == "4")
        $upthis = $upthis *4;
        elseif ($multiplicator == "5")
        $upthis = $upthis *5;
        /////do the math////
        $happy = mysql_query("SELECT id, multiplier from happyhour where userid=".sqlesc($userid)." AND torrentid=".sqlesc($torrentid)." ");
        $happyhour = mysql_num_rows($happy) == 0 ? false : true;
        $happy_multi = mysql_fetch_row($happy);
        $multiplier = $happy_multi["multiplier"];
        if ($happyhour){
        $upthis = $upthis * $multiplier;
        $downthis = 0;
        }
        if ($upthis > 0 || $downthis > 0)               
        if (!($free_for_all || $torrent["countstats"]=='no' || (in_array($az["class"], $freeclass)) ||(in_array($torrent["category"], $freecat)))) // is it a non free torrent
        // is it a non free torrent
        $updq[0]="downloaded = downloaded + $downthis";
        $updq[1]="uploaded = uploaded + " . (($arrfs['doubleup']=='yes' || $double_for_all)?($upthis*2):$upthis);
        $udq=implode(',',$updq);
        mysql_query("UPDATE users SET $udq WHERE id=$userid") or err("Tracker error 3");
		/////// Initial sanity check xMB/s for 1 second
        if($upthis > 2097152)
        {
        //Work out time difference
        $endtime = time();
        $starttime = $self['ts'];
        $diff = ($endtime - $starttime);
        //Normalise to prevent divide by zero.
        $rate = ($upthis / ($diff + 1));
        //Currently 2MB/s. Increase to 5MB/s once finished testing.
        if ($rate > 2097152)
        {
        if ($class < UC_CODER AND $highspeed == "no")
        {
        $rate = mksize($rate);
        $client = $agent;
        $userip = getip();
        auto_enter_cheater($userid, $rate, $upthis, $diff, $torrentid, $client, $userip, $last_up);        
        mysql_query("UPDATE users set enabled='no' WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
		//mysql_query("UPDATE users set warned='yes', downloadpos='no' WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
		}
        }
        }
        }
        ////////////////end abnormal upload speed detection ////	
function portblacklisted($port)
{
	// direct connect
	if ($port >= 411 && $port <= 413) return true;

	// bittorrent
	if ($port >= 6881 && $port <= 6889) return true;

	// kazaa
	if ($port == 1214) return true;

	// gnutella
	if ($port >= 6346 && $port <= 6347) return true;

	// emule
	if ($port == 4662) return true;

	// winmx
	if ($port == 6699) return true;

	return false;
}

if (portblacklisted($port))
			err("Port $port is blacklisted.");
		else
		{
			$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
			if (!$sockres)
				$connectable = "no";
			else
			{
				$connectable = "yes";
				@fclose($sockres);
			}
		}

$updateset = array();
if (isset($self) && $event == "stopped") {
mysql_query("DELETE FROM peers WHERE $selfwhere") or err("D Err");
if (mysql_affected_rows()) {
$updateset[] = ($self["seeder"] == "yes" ? "seeders = seeders - 1" : "leechers = leechers - 1");
mysql_query("UPDATE snatched SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = uploaded + $upthis, downloaded = downloaded + $downthis, to_go = $left, upspeed = $upspeed, downspeed = $downspeed, $announcetime, last_action = '".get_date_time()."', seeder = '$seeder', agent = ".sqlesc($agent)." WHERE torrentid = $torrentid AND userid = $userid") or err("SL Err 1");
}
} elseif (isset($self)) {
$prev_action = sqlesc($self['last_action']);
if ($event == "completed") {
$updateset[] = "times_completed = times_completed + 1";
$finished = ", finishedat = UNIX_TIMESTAMP()";
$finished1 = ", complete_date = '".get_date_time()."', finished = 'yes'";
}

mysql_query("UPDATE peers SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = $uploaded, downloaded = $downloaded, to_go = $left, last_action = NOW(), prev_action = $prev_action, seeder = '$seeder', agent = ".sqlesc($agent)." $finished WHERE $selfwhere") or err("PL Err 1");
if (mysql_affected_rows()) {
if ($seeder <> $self["seeder"])
$updateset[] = ($seeder == "yes" ? "seeders = seeders + 1, leechers = leechers - 1" : "seeders = seeders - 1, leechers = leechers + 1");
$anntime = "timesann = timesann + 1";
mysql_query("UPDATE snatched SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = uploaded + $upthis, downloaded = downloaded + $downthis, to_go = $left, upspeed = $upspeed, downspeed = $downspeed, $announcetime, last_action = '".get_date_time()."', seeder = '$seeder', agent = ".sqlesc($agent)." $finished1, $anntime WHERE torrentid = $torrentid AND userid = $userid") or err("SL Err 2");
}
} else {
if ($az["parked"] == "yes")
err("Your account is parked! (Read the FAQ)");
elseif ($az["downloadpos"] == "no")
err("Your downloading priviledges have been disabled! (Read the rules)");
mysql_query("INSERT INTO peers (torrent, userid, peer_id, ip, port, connectable, uploaded, downloaded, to_go, started, last_action, seeder, agent, downloadoffset, uploadoffset, passkey) VALUES ($torrentid, $userid, ".sqlesc($peer_id).", ".sqlesc($ip).", $port, '$connectable', $uploaded, $downloaded, $left, NOW(), NOW(), '$seeder', ".sqlesc($agent).", $downloaded, $uploaded, ".sqlesc(unesc($passkey)).")") or err("PL Err 2");
if (mysql_affected_rows()) {
$updateset[] = ($seeder == "yes" ? "seeders = seeders + 1" : "leechers = leechers + 1");
$anntime = "timesann = timesann + 1";
mysql_query("UPDATE snatched SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', to_go = $left, last_action = '".get_date_time()."', seeder = '$seeder', agent = ".sqlesc($agent).", $anntime WHERE torrentid = $torrentid AND userid = $userid") or err("SL Err 3");
if (!mysql_affected_rows() && $seeder == "no")
mysql_query("INSERT INTO snatched (torrentid, torrent_name, userid, peer_id, ip, port, connectable, uploaded, downloaded, to_go, start_date, last_action, seeder, agent) VALUES ($torrentid, ".sqlesc($torrent['name']).", $userid, ".sqlesc($peer_id).", ".sqlesc($ip).", $port, '$connectable', $uploaded, $downloaded, $left, '".get_date_time()."', '".get_date_time()."', '$seeder', ".sqlesc($agent).")") or err("SL Err 4");
}
}
if ($seeder == "yes")
{
	if ($torrent["banned"] != "yes")
		$updateset[] = "visible = 'yes'";
	$updateset[] = "last_action = NOW()";
}

if (count($updateset))
	mysql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $torrentid");

benc_resp_raw($resp);

?>