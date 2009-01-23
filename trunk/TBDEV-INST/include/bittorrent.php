<?php
$stime=array_sum(explode(' ',microtime())); // start execution time
//error_reporting(E_ALL);
define('SQL_DEBUG', 1);
define('DEBUG_MODE', 1);
define ('IN_TRACKER', 'God! Your so sexy...');
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
require ("ctracker.php");
require ("vfunc.php");
$maxloginattempts = 5; // change this whatever u want. if u dont know what is this, leave it default
require_once("secrets.php");
require_once("cleanup.php");
require_once("function_happyhour.php");
require_once("mood.php");
require_once("class.inputfilter_clean.php");
$myFilter = new InputFilter($tags, $attributes, 0, 0); // Invoke it
/////////////// Sits at front of pageload (bittorrent.php)
function unsafeChar($var)
{
    return str_replace(array("&gt;", "&lt;", "&quot;", "&amp;"), array(">", "<", "\"", "&"), $var);
}
function safeChar($var)
{
return htmlspecialchars(unsafeChar($var));
}
function makeSafeText($arr) {
    foreach ($arr as $k => $v) {
        if (is_array($v))
            $arr[$k] = makeSafeText($v);
        else
            $arr[$k] = safeChar($v);
    }
    return $arr;
}
// Makes the data safe
if(!defined('IN_ANNOUNCE')){
    if (!empty($_GET)) $_GET = makeSafeText($_GET);
    if (!empty($_POST)) $_POST = makeSafeText($_POST);
    if (!empty($_COOKIE)) $_COOKIE = makeSafeText($_COOKIE);
}
/////////Strip slashes by system//////////
function cleanquotes(&$in){
    if(is_array($in)) return array_walk($in,'cleanquotes');
    return $in=stripslashes($in);
}
if(get_magic_quotes_gpc()){
    array_walk($_GET,'cleanquotes');
    array_walk($_POST,'cleanquotes');
    array_walk($_COOKIE,'cleanquotes');
    array_walk($_REQUEST,'cleanquotes');
}
if( !defined("TB_INSTALLED") )
{
	header("Location: ./install/install.php");
	exit;
}
if (file_exists('install'))
{
	die('Delete the install directory');
}  
function local_user()
{
  return $_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"];
}

dbconn(false,false);
$sql = "SELECT *
	FROM config";
if( !($result = mysql_query($sql)) )
{
	die("Could not query config information");
}

while ( $row = mysql_fetch_assoc($result) )
{
	$config[$row['name']] = $row['value'];
}
////config start///
$SITE_ONLINE = $config['siteonline'];
$FORUMS_ONLINE = $config['forums_online'];
//$SITE_ONLINE = local_user();
//$SITE_ONLINE = false;
$h = date("H");
if ($h >= 01 && $h <= 06) //When to save some load.
$announce_interval = 60 * 60; //60 min update in announce - Night
else 
$announce_interval = 60 * 30; // 30 min update in announce - Day
$max_torrent_size = 1000000;
$sql_error_log = './logs/sql_err_'.date("M_D_Y").'.log';
$signup_timeout = 86400 * 3;
$minvotes = 1;
$max_dead_torrent_time = 6 * 3600;
$invite_timeout = 21600 * 1;
$invites = 2500;
$READPOST_EXPIRY = 14*86400; // 14 days
// All Torrents Doubleseed
$double_for_all = 0;        // 1=ON/0=OFF
// Free/Doubleseed Message Title
$freetitle = "Sitewide Double upload!";
// Free/Doubleseed Message
$freemessage = "[size=2]All torrents marked double upload![/size]  :w00t:";
// Free Categories
$freecat = array("9");  // ID's of free categories
// Free for Class
$freeclass = array("7");  // ID's for free class (UC_CODER=7)
// Rules for torrent limitation
// Format is Ratio:UpGigs:SeedsMax:LeechesMax:AllMax|...
// Ratio and UpGigs are "minimum" requirements.
$GLOBALS["TORRENT_RULES"] = "0.5:2:10:8:18|1.01:2:30:20:50|2.01:5:40:30:70|5.01:20:50:35:85";
// Max users on site
$maxusers = $config['maxusers'];
////////////Define all rootpaths//////////
$torrent_dir =  ROOT_PATH."torrents";
//////////Directory for dox///////
$DOXPATH = ROOT_PATH."dox";
/////////////Directory for cache//
$CACHE =  ROOT_PATH."cache";
///dictbreaker path//
$dictbreaker = ROOT_PATH."dictbreaker";
////// announce url - the first one will be displayed on the pages
$announce_urls = array();
$announce_urls[] = $config['announce_url'];
if ($_SERVER["HTTP_HOST"] == "")                        // Root Based Installs Comment Out if in Sub-Dir
  $_SERVER["HTTP_HOST"] = $_SERVER["SERVER_NAME"];      // Comment out for Sub-Dir Installs
$BASEURL = "http://" . $_SERVER["HTTP_HOST"];           // Comment out for Sub-Dir Installs
//$BASEURL = 'http://domain.com';                       // Uncomment for Sub-Dir Installs - No Ending Slash
// Set url on cofig... No ending slash!
$DEFAULTBASEURL = $config['domain'];
//set this to true to make this a tracker that only registered users may use
$MEMBERSONLY = true;
//maximum number of peers (seeders+leechers) allowed before torrents starts to be deleted to make room...
//set this to something high if you don't require this feature
$PEERLIMIT = $config['peerlimit'];
// Email for sender/return path.
$SITEEMAIL = $config['sitemail'];
$SITENAME = $config['sitename'];
$autoclean_interval = 900;
$pic_base_url = "/pic/";
////define userclasses///
define ('UC_USER', 0);
define ('UC_POWER_USER', 1);
define ('UC_VIP', 2);
define ('UC_UPLOADER', 3);
define ('UC_MODERATOR', 4);
define ('UC_ADMINISTRATOR', 5);
define ('UC_SYSOP', 6);
define ('UC_CODER', 7);
///////see user_functions////////
//Do not modify -- versioning system
//This will help identify code for support issues at tbdev.net
define ('TBVERSION','TBDEV.NET-01-06-08');
//////config end////
/**** validip/getip courtesy of manolete <manolete@myway.com> ****/
// IP Validation
function validip($ip)
{
	if (!empty($ip) && $ip == long2ip(ip2long($ip)))
	{
		// reserved IANA IPv4 addresses
		// http://www.iana.org/assignments/ipv4-address-space
		$reserved_ips = array (
				array('0.0.0.0','2.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
		);

		foreach ($reserved_ips as $r)
		{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	}
	else return false;
}
/////////////Block Unsupported browser///////////////
/*
if(false!==stristr($_SERVER['HTTP_USER_AGENT'],'chrome')){
    header('Location: http://chat2pals.co.uk/reject.html');
    exit();
}
if(false!==stristr($_SERVER['HTTP_USER_AGENT'],'ie')){
    header('Location: http://chat2pals.co.uk/reject.html');
    exit();
}
if(false!==stristr($_SERVER['HTTP_USER_AGENT'],'opera')){
    header('Location: http://chat2pals.co.uk/reject.html');
    exit();
}
*/
//////////////////browser block end///////////
// Patched function to detect REAL IP address if it's valid
function getip() {
   if (isset($_SERVER)) {
     if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
       $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
     } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP'])) {
       $ip = $_SERVER['HTTP_CLIENT_IP'];
     } else {
       $ip = $_SERVER['REMOTE_ADDR'];
     }
   } else {
     if (getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR'))) {
       $ip = getenv('HTTP_X_FORWARDED_FOR');
     } elseif (getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP'))) {
       $ip = getenv('HTTP_CLIENT_IP');
     } else {
       $ip = getenv('REMOTE_ADDR');
     }
   }

   return $ip;
 }

function dbconn($autoclean = false, $userlogin=true)
{
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;

    if (!@mysql_connect($mysql_host, $mysql_user, $mysql_pass))
    {
      switch (mysql_errno())
      {
        case 1040:
        case 2002:
            if ($_SERVER[REQUEST_METHOD] == "GET")
                die("<html><head><meta http-equiv=refresh content=\"5 $_SERVER[REQUEST_URI]\"></head><body><table border=0 width=100% height=100%><tr><td><h3 align=center>The server load is very high at the moment. Retrying, please wait...</h3></td></tr></table></body></html>");
            else
                die("Too many users. Please press the Refresh button in your browser to retry.");
        default:
            die("[" . mysql_errno() . "] dbconn: mysql_connect: " . mysql_error());
      }
    }
    mysql_select_db($mysql_db)
        or die('dbconn: mysql_select_db: ' + mysql_error());

    if ($userlogin) userlogin();

    if ($autoclean)
        register_shutdown_function("autoclean");
}
function status_change($id) {
    sql_query('UPDATE announcement_process SET status = 0 WHERE user_id = '.sqlesc($id).' AND status = 1');
}
//////////////////////////////////////////////////////////////////////
//-------------New modified maxcoder+staff account protector
    function maxcoder () {
	global $CURUSER;
	$lmaxclass = 7;
	$filename =  ROOT_PATH."settings/STAFFNAMES";
	$filename2 =  ROOT_PATH."settings/STAFFIDS";	
	if ($CURUSER['class'] >= $lmaxclass) {
	$fp = fopen($filename, 'r');
	while (!feof($fp))
	{ 
	$staffnames= fgets($fp);
	$results = explode(' ', $staffnames); 
	}
		$added = sqlesc(get_date_time());
		if (!in_array($CURUSER['username'], $results, true)) { // true for strict comparison
		sql_query("UPDATE users set enabled='no' WHERE id=$CURUSER[id]"); 
        $subject = sqlesc( "Alert Super User Has been Detected" );
        $body = sqlesc("User " . $CURUSER["username"] . " has attempted to hack the tracker using a super class - the account has been disabled");
        auto_post( $subject , $body );
        $msg = "Hack Attempt Detected : Username: ".$CURUSER["username"]." - UserID: ".$CURUSER["id"]." - UserIP : ".getip();
        //mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES(0, 0, '1', '" . get_date_time() . "', " .sqlesc($msg) . ")") or sqlerr(__FILE__, __LINE__);
		sql_query("INSERT INTO messages (poster, sender, receiver, added, subject, msg) VALUES(0, 0, '1', '" . get_date_time() . "', ".$subject." , " .sqlesc($msg) . ")") or sqlerr(__FILE__, __LINE__);
		write_log($msg);
		fclose($fp);
		stderr("Access Denied!","Ha Ha you retard - Did you honestly think you could pull that one off !");
		}
		fclose($fp);
	}
	        define ('UC_STAFF', 4); // Minumum Staff Level (4=UC_MODERATOR)
            if ($CURUSER['class'] >= UC_STAFF) {
		    $fp2 = fopen($filename2, 'r');
		    while (!feof($fp2))
			{ 
			$staffids = fgets($fp2);
			$results2 = explode(' ', $staffids); 
				}
			    if (!in_array($CURUSER['id'], $results2, true)) { // true for strict comparison				
				sql_query("UPDATE users set enabled='no' WHERE id=$CURUSER[id]"); 
                $subject = sqlesc( "Staff Account Hack Detected" );
                $body = sqlesc("User " . $CURUSER["username"] . " has attempted to hack the tracker using an unauthorized account- the account has been disabled");
                auto_post( $subject , $body );
                $msg = "Fake Account Detected: Username: ".$CURUSER["username"]." - UserID: ".$CURUSER["id"]." - UserIP : ".getip();
                //mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES(0, 0, '1', '" . get_date_time() . "', " .sqlesc($msg) . ")") or sqlerr(__FILE__, __LINE__);
				sql_query("INSERT INTO messages (poster, sender, receiver, added, subject, msg) VALUES(0, 0, '1', '" . get_date_time() . "', ".$subject." , " .sqlesc($msg) . ")") or sqlerr(__FILE__, __LINE__);
				write_log($msg);				 
				fclose($fp2);
				stderr("Access Denied!","Sorry but your not an authorized staff member - nice try your banned !");		
			}
			fclose($fp2);
	}	
	return true;
}
////////////////////Credits to Retro for the original code :)//////////////////////////////////////		
// Returns the current time in GMT in MySQL compatible format.
function get_date_time($timestamp = 0)
{
  if ($timestamp)
    return date("Y-m-d H:i:s", $timestamp);
  else
    return gmdate("Y-m-d H:i:s");
}
function logged_in()
{
    global $CURUSER;
    if (!$CURUSER)return false;
    return true;
    header("Location: login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]));
    exit();
}
function userlogin() {
    global $SITE_ONLINE;
    unset($GLOBALS["CURUSER"]);
    $dt = get_date_time();
    $ip = getip();
	$nip = ip2long($ip);
    require_once "cache/bans_cache.php";
    if(count($bans) > 0)
    {
      foreach($bans as $k) {
        if($nip >= $k['first'] && $nip <= $k['last']) {
        header("HTTP/1.0 403 Forbidden");
        print("<html><body><h1>403 Forbidden</h1>Unauthorized IP address.</body></html>\n");
        exit();
        }
      }
      unset($bans);
    }

    if (!$SITE_ONLINE || empty($_COOKIE["uid"]) || empty($_COOKIE["pass"]) || empty($_COOKIE["hashv"]))
        return;            
    $id = 0 + $_COOKIE["uid"];
    if (!$id OR (strlen($_COOKIE["pass"]) != 32) OR ($_COOKIE["hashv"] != hashit($id,$_COOKIE["pass"])))
        return;
    $res = mysql_query("SELECT * FROM users WHERE id = $id AND enabled='yes' AND status = 'confirmed'") or die(mysql_error());
    $row = mysql_fetch_array($res);
	 ////////////////announcement mod by Retro/////////////////////////
    $res = sql_query("SELECT u.*, ann_main.subject AS curr_ann_subject, ann_main.body AS curr_ann_body ".
        "FROM users AS u ".
        "LEFT JOIN announcement_main AS ann_main ".
        "ON ann_main.main_id = u.curr_ann_id ".
        "WHERE u.id = $id AND u.enabled='yes' AND u.status = 'confirmed'") or sqlerr(__FILE__, __LINE__);
    $row = mysql_fetch_array($res);  
	if (!$row)
        return;
    $sec = hash_pad($row["secret"]);
    if ($_COOKIE["pass"] !== md5($row["passhash"].$_SERVER["REMOTE_ADDR"]))
        return;
    if (($ip != $row["ip"]) && $row["ip"])
    sql_query("INSERT INTO iplog (ip, userid, access) VALUES (" . sqlesc($row["ip"]) . ", " . $row["id"] . ", '" . $row["last_access"] . "')");
    // If curr_ann_id > 0 but curr_ann_body IS NULL, then force a refresh
            if (($row['curr_ann_id'] > 0) AND ($row['curr_ann_body'] == NULL)) {

                $row['curr_ann_id'] = 0;
                $row['curr_ann_last_check']    = '0000-00-00 00:00:00';
            }


            // If elapsed > 10 minutes, force a announcement refresh.
            if (($row['curr_ann_last_check'] != '0000-00-00 00:00:00') AND
                    (strtotime($row['curr_ann_last_check']) < (strtotime($dt) - 300)))
                    $row['curr_ann_last_check'] = '0000-00-00 00:00:00';

if (($row['curr_ann_id'] == 0) AND ($row['curr_ann_last_check'] == '0000-00-00 00:00:00'))
{ // Force an immediate check...
$query = sprintf('SELECT m.*,p.process_id FROM announcement_main AS m '.
'LEFT JOIN announcement_process AS p ON m.main_id = p.main_id '.
'AND p.user_id = %s '.
'WHERE p.process_id IS NULL '.
'OR p.status = 0 '.
'ORDER BY m.main_id ASC '.
'LIMIT 1',
sqlesc($row['id']));

$result = mysql_query($query);

if (mysql_num_rows($result))
{ // Main Result set exists
$ann_row = mysql_fetch_array($result);

$query = $ann_row['sql_query'];

// Ensure it only selects...
if (!preg_match('/\\ASELECT.+?FROM.+?WHERE.+?\\z/', $query)) die();

// The following line modifies the query to only return the current user
// row if the existing query matches any attributes.
$query .= ' AND u.id = '.sqlesc($row['id']).' LIMIT 1';

$result = mysql_query($query);

if (mysql_num_rows($result))
{ // Announcement valid for member
$row['curr_ann_id'] = $ann_row['main_id'];

// Create two row elements to hold announcement subject and body.
$row['curr_ann_subject'] = $ann_row['subject'];
$row['curr_ann_body'] = $ann_row['body'];

// Create additional set for main UPDATE query.
$add_set = ', curr_ann_id = '.sqlesc($ann_row['main_id']);
$status = 2;
}
else
{
// Announcement not valid for member...
$add_set = ', curr_ann_last_check = '.sqlesc($dt);
$status = 1;
}

// Create or set status of process
if ($ann_row['process_id'] === NULL)
{
// Insert Process result set status = 1 (Ignore)
$query = sprintf('INSERT INTO announcement_process (main_id, '.
'user_id, status) VALUES (%s, %s, %s)',
sqlesc($ann_row['main_id']),
sqlesc($row['id']),
sqlesc($status));
}
else
{
// Update Process result set status = 2 (Read)
$query = sprintf('UPDATE announcement_process SET status = %s '.
'WHERE process_id = %s',
sqlesc($status),
sqlesc($ann_row['process_id']));
}
mysql_query($query);
}
else
{
// No Main Result Set. Set last update to now...
$add_set = ', curr_ann_last_check = '.sqlesc($dt);
}
unset($result);
unset($ann_row);
}
    /*$hideids = array('1','69');  
    if ($row['class'] == UC_SYSOP || in_array($row['id'], $hideids))
    $ip = 'Hidden';*/
    //$hideids = array('1','3');  
    //$ip = ($row['class'] != UC_SYSOP || !in_array($row['id'], $hideids)) ? $ip : substr('IPHash-'. md5('random text'.$ip.$row['id'].'more random text'), 0, -24);
    session_cache_limiter('private');
    session_start();
    if ((!isset($_SESSION['browsetime'])) || ($row['ip'] !==$ip))
    $_SESSION['browsetime']=strtotime($row['last_access']);
    sql_query("UPDATE users SET last_access=".sqlesc($dt).", ip=".sqlesc($ip).$add_set.
        " WHERE id=".$row['id']);// or die(mysql_error());
    $row['ip'] = $ip;
    if ($row['override_class'] < $row['class']) $row['class'] = $row['override_class']; // Override class and save in GLOBAL array below.
    $GLOBALS["CURUSER"] = $row;
    }

function autoclean() {
    global $autoclean_interval;

    $now = time();
    $docleanup = 0;

    $res = sql_query("SELECT value_u FROM avps WHERE arg = 'lastcleantime'");
    $row = mysql_fetch_array($res);
    if (!$row) {
        sql_query("INSERT INTO avps (arg, value_u) VALUES ('lastcleantime',$now)");
        return;
    }
    $ts = $row[0];
    if ($ts + $autoclean_interval > $now)
        return;
    sql_query("UPDATE avps SET value_u=$now WHERE arg='lastcleantime' AND value_u = $ts");
    if (!mysql_affected_rows())
        return;

    docleanup();
}

function unesc($x) {
    if (get_magic_quotes_gpc())
        return stripslashes($x);
    return $x;
}

function mksize($bytes)
{
    if ($bytes < 1000 * 1024)
        return number_format($bytes / 1024, 2, ".", ".") . " KB";
	    elseif ($bytes < 1000 * 1048576)
	        return number_format($bytes / 1048576, 2, ".", ".") . " MB";
		    elseif ($bytes < 1000 * 1073741824)
		        return number_format($bytes / 1073741824, 2, ".", ".") . " GB";
			    elseif ($bytes < 1000 * 1099511627776)
			        return number_format($bytes / 1099511627776, 2, ".", ".") . " TB";
				    else
				    return number_format($bytes / 1125899906842624, 2, ".", ".") . " PB";
					}
					
					function mksizeint($bytes)
					{
					$bytes = max(0, $bytes);
					if ($bytes < 1000)
					return number_format(floor($bytes), 0, ",", ".") . " B";
					elseif ($bytes < 1000 * 1024)
					return number_format(floor($bytes / 1024), 0, ",", ".") . " KB";
					elseif ($bytes < 1000 * 1048576)
					return number_format(floor($bytes / 1048576), 0, ",", ".") . " MB";
					elseif ($bytes < 1000 * 1073741824)
					return number_format(floor($bytes / 1073741824), 0, ",", ".") . " GB";
					elseif ($bytes < 1000 * 1099511627776)
					return number_format(floor($bytes / 1099511627776), 0, ",", ".") . " TB";
					else
					return number_format(floor($bytes / 1125899906842624), 0, ".". ".") . " PB";
					}

function deadtime() {
    global $announce_interval;
    return time() - floor($announce_interval * 1.3);
}

function mkprettytime($s) {
    if ($s < 0)
        $s = 0;
    $t = array();
    foreach (array("60:sec","60:min","24:hour","0:day") as $x) {
        $y = explode(":", $x);
        if ($y[0] > 1) {
            $v = $s % $y[0];
            $s = floor($s / $y[0]);
        }
        else
            $v = $s;
        $t[$y[1]] = $v;
    }

    if ($t["day"])
        return $t["day"] . "d " . sprintf("%02d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
    if ($t["hour"])
        return sprintf("%d:%02d:%02d", $t["hour"], $t["min"], $t["sec"]);
//    if ($t["min"])
        return sprintf("%d:%02d", $t["min"], $t["sec"]);
//    return $t["sec"] . " secs";
}

function mkglobal($vars) {
    if (!is_array($vars))
        $vars = explode(":", $vars);
    foreach ($vars as $v) {
        if (isset($_GET[$v]))
            $GLOBALS[$v] = unesc($_GET[$v]);
        elseif (isset($_POST[$v]))
            $GLOBALS[$v] = unesc($_POST[$v]);
        else
            return 0;
    }
    return 1;
}

if (!function_exists("stripos")) {
  function stripos($str,$needle,$offset=0)
  {
      return strpos(strtolower($str),strtolower($needle),$offset);
  }
}
function display_date_time($time) {
  global $CURUSER;
  return date("Y-m-d H:i:s", strtotime($time) + (($CURUSER["timezone"] + $CURUSER["dst"]) * 60));
}
function cpfooter() {
$referring_url = $_SERVER['HTTP_REFERER'];    
print("<table class=bottom width=100% border=0 cellspacing=0 cellpadding=0><tr valign=top>\n");
print("<td class=bottom align=center><p><br><a href=$referring_url>Return to whence you came</a></td>\n");
print("</tr></table>\n");
}

function sql_query($query) {
    global $queries, $query_stat;
    $queries++;
    $mtime = microtime(); // Get Current Time
    $mtime = explode (" ", $mtime); // Split Seconds and Microseconds
    $mtime = $mtime[1] + $mtime[0];  // Create a single value for start time
    $query_start_time = $mtime; // Start time
    $result = mysql_query($query);
    $mtime = microtime();
    $mtime = explode (" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $query_end_time = $mtime; // End time
    $query_time = ($query_end_time - $query_start_time);
    $query_time = substr($query_time, 0, 8);
    $query_stat[] = array("seconds" => $query_time, "query" => $query);
    return $result;
}

function get_torrent_limits($userinfo)
{
    $limit = array("seeds" => -1, "leeches" => -1, "total" => -1);

    if ($userinfo["tlimitall"] == 0) {
        // Auto limit
        $ruleset = explode("|", $GLOBALS["TORRENT_RULES"]);
        $ratio = (($userinfo["downloaded"] > 0) ? ($userinfo["uploaded"] / $userinfo["downloaded"]) : (($userinfo["uploaded"] > 0) ? 1 : 0));
        $gigs = $userinfo["uploaded"] / 1073741824;
        
        $limit = array("seeds" => 0, "leeches" => 0, "total" => 0);
        foreach ($ruleset as $rule) {
            $rule_parts= explode(":", $rule);
            if ($ratio >= $rule_parts[0] && $gigs >= $rule_parts[1] && $limit["total"] <= $rule_parts[4]) {
                $limit["seeds"] = $rule_parts[2];
                $limit["leeches"] = $rule_parts[3];
                $limit["total"] = $rule_parts[4];
            }
        }
    } elseif ($userinfo["tlimitall"] > 0) {
        // Manual limit
        $limit["seeds"] = $userinfo["tlimitseeds"];
        $limit["leeches"] = $userinfo["tlimitleeches"];
        $limit["total"] = $userinfo["tlimitall"];
    }
    
    return $limit;
}
function tr($x,$y,$noesc=0) {
    if ($noesc)
        $a = $y;
    else {
        $a = htmlspecialchars($y);
        $a = str_replace("\n", "<br />\n", $a);
    }
    print("<tr><td class=\"heading\" valign=\"top\" align=\"right\">$x</td><td valign=\"top\" align=left>$a</td></tr>\n");
}

function trala($x,$y,$noesc=0) {
   if ($noesc)
       $a = $y;
   print("<tr><td class=\"heading\" valign=\"top\" align=\"right\">$x</td><td valign=\"top\" align=left>$a</td></tr>\n");
}

function validfilename($name) {
    return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
}

function validemail($email) {
    return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
}
function add_s($i)
{
return ($i == 1 ? "" : "s");
}
/////////moddified sqesc function by retro///
/*
function sqlesc($x) {
   if (get_magic_quotes_gpc()) {
       $x = stripslashes($x);
   }
   if (!is_numeric($x)) {
       $x = "'" . mysql_real_escape_string(UnsafeChar($x)) . "'";
   }
   return $x;
}
*/
///////////////end new sqlesc/////////
//////////modified sqlesc //==putyn@tbdev
function sqlesc($x) {
   if (get_magic_quotes_gpc())
       $x = stripslashes($x);
   return is_numeric($x) ? $x : "'" . mysql_real_escape_string(unsafeChar($x)) . "'";
} 
///////////////////////////////////////////////

function sqlwildcardesc($x) {
    return str_replace(array("%","_"), array("\\%","\\_"), mysql_real_escape_string($x));
}

function urlparse($m) {
    $t = $m[0];
    if (preg_match(',^\w+://,', $t))
        return "<a href=\"$t\">$t</a>";
    return "<a href=\"http://$t\">$t</a>";
}

function parsedescr($d, $html) {
    if (!$html)
    {
      $d = htmlspecialchars($d);
      $d = str_replace("\n", "\n<br>", $d);
    }
    return $d;
}
function safe($var) {

    return str_replace(array('&', '>', '<', '"', '\'' ), array('&amp;', '&gt;', '&lt;', '&quot;', '&#039;' ), str_replace(array('&gt;', '&lt;', '&quot;', '&#039;', '&amp;'), array('>', '<', '"', '\'', '&'), $var));
}
function hashit($var,$addtext="")
{
        return md5("Some ".$addtext.$var.$addtext." sal7 mu55ie5 wat3r.@.");
}
/////////////// Basic MySQL error handler
function sqlerr($file = '', $line = '') {
    global $sql_error_log, $CURUSER;
    
		$the_error    = mysql_error();
		$the_error_no = mysql_errno();

    	if ( SQL_DEBUG == 0 )
    	{
			exit();
    	}
     	else if ( $sql_error_log AND SQL_DEBUG == 1 )
		{
			$_error_string  = "\n===================================================";
			$_error_string .= "\n Date: ". date( 'r' );
			$_error_string .= "\n Error Number: " . $the_error_no;
			$_error_string .= "\n Error: " . $the_error;
			$_error_string .= "\n IP Address: " . $_SERVER['REMOTE_ADDR'];
			$_error_string .= "\n in file ".$file." on line ".$line;
			$_error_string .= "\n URL:".$_SERVER['REQUEST_URI'];
			$_error_string .= "\n Username: {$CURUSER['username']}[{$CURUSER['id']}]";
			
			if ( $FH = @fopen( $sql_error_log, 'a' ) )
			{
				@fwrite( $FH, $_error_string );
				@fclose( $FH );
			}
			
			print "<html><head><title>MySQL Error</title>
					<style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style></head><body>
		    		   <blockquote><h1>MySQL Error</h1><b>There appears to be an error with the database.</b><br />
		    		   You can try to refresh the page by clicking <a href=\"javascript:window.location=window.location;\">here</a>
				  </body></html>";
		}
		else
		{
    		$the_error = "\nSQL error: ".$the_error."\n";
	    	$the_error .= "SQL error code: ".$the_error_no."\n";
	    	$the_error .= "Date: ".date("l dS \of F Y h:i:s A");
    	
	    	$out = "<html>\n<head>\n<title>MySQL Error</title>\n
	    		   <style>P,BODY{ font-family:arial,sans-serif; font-size:11px; }</style>\n</head>\n<body>\n
	    		   <blockquote>\n<h1>MySQL Error</h1><b>There appears to be an error with the database.</b><br />
	    		   You can try to refresh the page by clicking <a href=\"javascript:window.location=window.location;\">here</a>.
	    		   <br /><br /><b>Error Returned</b><br />
	    		   <form name='mysql'><textarea rows=\"15\" cols=\"60\">".htmlentities($the_error, ENT_QUOTES)."</textarea></form><br>We apologise for any inconvenience</blockquote></body></html>";
    		   
    
	       	print $out;
		}
		
        exit();
}

function getrow($id, $value, $arr)
{
foreach($arr as $row)
if ($row[$id] == $value)
return $row;
return false;
}

function stdhead($title = "", $msgalert = true) {
global $CURUSER, $SITE_ONLINE, $FUNDS, $SITENAME, $BASEURL, $CACHE, $mood, $double_for_all, $freetitle, $freemessage;
//////site on/off
$res = sql_query("SELECT * FROM siteonline") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
if ($row["onoff"] !=1){
$my_siteoff = 1;
$my_siteopenfor = $row['class_name'];
}
if (($row["onoff"] !=1) && (!$CURUSER)){ 
die("<title>Site Offline!</title>
<table width='100%' height='100%' bgcolor='orange' style='border: 8px inset #000000'><tr><td align='center'>
<h1 style='color: #000000;'>".htmlspecialchars($row['reason'])."</h1>
<h1 style='color: #000000;'>
Please, try later...</h1>
<img border=0 class=embedded width='800' height='300' src=pic/404.jpg>
<br><center><form method='post' action='takesiteofflogin.php'>
<table border='1' cellspacing='1' id='table1' cellpadding='3' style='border-collapse: collapse'>
<tr><td colspan='2' align='center' bgcolor='orange'>
<font color='black'><u><b>Staff Access Only </b></u></font></td></tr>
<tr><td><font color='black'><b>Name:</b></font></td>
<td><input type='text' size=20 name='username'></td></tr><tr>
<td><font color='black'><b>Password:</b></font></td>
<td><input type='password' size=20 name='password'></td>
</tr><tr>
<td colspan='2' align='center'>
<input type='submit' value='Submit!'></td>
</tr></table>
</form></center>
</td></tr></table>");
if(($CURUSER) && ($double_for_all)) {
$d = (!empty($double_for_all) ? "<img src=".$pic_base_url."doubleseed.gif alt=Doubleseed!>":'');
echo '<table width=50%><tr><td class=colhead colspan=3 align=center>'.unesc($freetitle).'
</td></tr><tr><td width=42 align=center valign=center>'.$d.'</td><td><div align=center>'.format_comment($freemessage).'
</div></td><td width=42 align=center valign=center>'.$d.'</td></tr></table><br />';
}
}
if (($row["onoff"] !=1) and (($CURUSER["class"] < $row["class"]) && ($CURUSER["id"] != 1))){ 
die("<title>Site Offline!</title>
<table width='100%' height='100%' bgcolor='orange' style='border: 8px inset #000000'><tr><td align='center'>
<h1 style='color: #000000;'>".htmlspecialchars($row['reason'])."</h1>
<h1 style='color: #000000;'>
Please, try later...</h1>
<img border=0 class=embedded width='800' height='300' src=pic/404.jpg>
</td></tr></table>");
}
/////////////end on/off
global $ss_uri;
if (!$SITE_ONLINE)
die("Site is down for maintenance, please check back again later... thanks<br/>");
header("Content-Type: text/html; charset=iso-8859-1");
if ($title == "")
$title = $SITENAME .(isset($_GET['tbv'])?" (".TBVERSION.")":'');
else
$title = $SITENAME .(isset($_GET['tbv'])?" (".TBVERSION.")":''). " :: " . safeChar($title);
include_once ("cache/stylesheets.php");
if ($CURUSER)
  {

    $stylesheet = getrow('id',"{$CURUSER['stylesheet']}", $stylesheets);

    $ss_a = $stylesheet['uri'];
    if ($ss_a)
        $ss_uri = $ss_a;
  }
  if (!$ss_uri)
  {
    $stylesheet = getrow('id', '1', $stylesheets);

    $ss_uri = $stylesheet['uri'];
  }

if ($msgalert && $CURUSER)
{
$res = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " && unread='yes'") or die("OopppsY!");
$arr = mysql_fetch_row($res);
$unread = $arr[0];
}
require_once("themes/".$ss_uri."/template.php");
require_once("themes/".$ss_uri."/stdhead.php");
}// stdhead

function stdfoot()
{
global $CURUSER;
global $ss_uri;
require_once("themes/".$ss_uri."/template.php");
require_once("themes/".$ss_uri."/stdfoot.php");
}

function genbark($x,$y) {
    stdhead($y);
    print("<h2>" . safeChar($y) . "</h2>\n");
    print("<p>" . safeChar($x) . "</p>\n");
    stdfoot();
    exit();
}

function mksecret($length = 20) {
$set = array("a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J","k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T","u","U","v","V","w","W","x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9");
$str;
for($i = 1; $i <= $length; $i++) {
$ch = rand(0, count($set)-1);
$str .= $set[$ch];
}
return $str;
}

function httperr($code = 404) {
    header("HTTP/1.0 404 Not found");
    print("<h1>Not Found</h1>\n");
    print("<p>Sorry pal :(</p>\n");
    exit();
}

function gmtime()
{
    return strtotime(get_date_time());
}

function logincookie($id, $passhash, $updatedb = 1, $expires = 0x7fffffff)
{
    setcookie("uid", $id, $expires, "/");
    setcookie("pass", $passhash, $expires, "/");
    setcookie("hashv", hashit($id,$passhash), $expires, "/");

  if ($updatedb)
      sql_query("UPDATE users SET last_login = NOW() WHERE id = $id");
}

function logoutcookie() {
    setcookie("uid", "", 0x7fffffff, "/");
    setcookie("pass", "", 0x7fffffff, "/");
    setcookie("hashv", "", 0x7fffffff, "/");
}

function loggedinorreturn() {
    global $CURUSER;
    if (!$CURUSER) {
        header("Location: login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]));
        exit();
    }
}

function ago($seconds){
$day=date("j",$seconds)-1;
$month=date("n",$seconds)-1;
$year=date("Y",$seconds)-1970;
$hour=date("G",$seconds)-1;
$minute=(int) date("i",$seconds);
$returnvalue=false;
if($year){
if($year==1) $return[]="1 year"; else $return[]="$year years";
}
if($month){
if($month==1) $return[]="1 month"; else $return[]="$month months";
}
if($day){
if($day==1) $return[]="1 day"; else $return[]="$day days";
}
if($hour){
if($hour==1) $return[]="1 hour"; else $return[]="$hour hours";
}
if($minute&&$minute!=00){
if($minute==1){
$return[]="1 minute";
}else{
$return[]="$minute minutes";
}
}
for($i=0;$i<count($return);$i++){
if(!$returnvalue){
$returnvalue=$return[$i];
}elseif($i<count($return)-1){
$returnvalue.= ", ".$return[$i];
}else{
$returnvalue.= " and ".$return[$i];
}
}
return $returnvalue;

}
function getpre($name, $type)
{
$pre['regexp'] = "|<td>(.*)<td>(.*)<td>(.*)</table>|";
$pre['url'] = "http://doopes.com/?cat=454647&lang=0&num=2&mode=0&from=&to=&exc=&inc=" . $name . "&opt=0";
$pre['file'] = @file_get_contents($pre['url']);
preg_match($pre['regexp'], $pre['file'], $pre['matches']);
/**
* Types:
* 1 = Time
* 2 = Category
* 3 = Realesename
*/
return $pre['matches'][$type];
}
?>