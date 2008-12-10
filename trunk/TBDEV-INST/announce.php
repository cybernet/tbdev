<?php
ob_start("ob_gzhandler");
$h = date("H");
if ($h >= 01 && $h <= 06) //When to save some load.
$announce_interval = 60 * 60; //60 min update in announce - Night
else 
$announce_interval = 60 * 30; // 30 min update in announce - Day
$GLOBALS["TORRENT_RULES"] = "0:0:10:2:12|1.01:5:10:3:13|2.01:20:10:4:14";
$MEMBERSONLY = true;  
$SITE_ONLINE = true;  
define ("UC_VIP", 2);
// secrets.php part //
$mysql_host = 'localhost';
$mysql_db = 'xxxxxxxx';
$mysql_user = 'xxxxxxxx';
$mysql_pass = 'xxxxxxxxx';
// end of secrets.php part //
//=== start functions
//=== bad stuff let's just kill this right off
$headers = getallheaders();
if (isset($headers['Cookie']) || isset($headers['Accept-Language']) || isset($headers['Accept-Charset']))
exit('It takes 46 muscles to frown but only 4 to flip \'em the bird.');
//=== all needed functions
function err($msg){
header('Content-Type: text/plain');
header('Pragma: no-cache');
die('d14:failure reason'.strlen($msg).':'.$msg.'e');
}

    //Function unesc
    //Modified by expert01
    //Adjusts for magic_quotes_sybase, as well as new line/character returns
    //which stripslashes automatically breaks
    function unesc($x) {
        $x = str_replace('\n', "\n", $x);
        $x = str_replace('\r', "\r", $x);
        $x = str_replace('\t', "\t", $x);
        if (get_magic_quotes_gpc()) {
            if (ini_get('magic_quotes_sybase')) {
                $x = str_replace("''", "'", $x);
            } else {
                $x = stripslashes($x);
            }
        }
        return $x;
    }
function mksize($bytes)
{
	if ($bytes < 1000 * 1024)
		return number_format($bytes / 1024, 2) . " kB";
	elseif ($bytes < 1000 * 1048576)
		return number_format($bytes / 1048576, 2) . " MB";
	elseif ($bytes < 1000 * 1073741824)
		return number_format($bytes / 1073741824, 2) . " GB";
	elseif ($bytes < 1000 * 1099511627776)
		return number_format($bytes / 1099511627776, 2) . " TB";
else
return number_format($bytes / 1125899906842624, 2) . " PB";
}
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
function getip()
{
$_SERVER;
if (validip(getenv('HTTP_CLIENT_IP'))) return getenv('HTTP_CLIENT_IP');
elseif (getenv('HTTP_X_FORWARDED_FOR')!="")
{
$forwarded=str_replace(",","",getenv('HTTP_X_FORWARDED_FOR'));
$forwarded_array=split(" ",$forwarded);
foreach($forwarded_array as $value) if (validip($value)) return $value;
}
return $_SERVER['REMOTE_ADDR'];
}
function sqlesc($x) {
return "'".mysql_real_escape_string($x)."'";
}
function hash_pad($hash) {
return str_pad($hash, 20);
}
function hash_where($name, $hash) {
$shhash = preg_replace('/ *$/s', "", $hash);
return "($name = " . sqlesc($hash) . " OR $name = " . sqlesc($shhash) . ")";
}
function portblacklisted($port){
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
// IRC bot based trojans
if ($port == 65535) return true;
return false;
}
// Returns the current time in GMT in MySQL compatible format.
function get_date_time($timestamp = 0)
{
  if ($timestamp)
    return date("Y-m-d H:i:s", $timestamp);
  else
    return date("Y-m-d H:i:s");
}
function gmtime()
{
    return strtotime(get_date_time());
}
function benc($obj) {
if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
return;
$c = $obj["value"];
switch ($obj["type"]) {
case "string":
return benc_str($c);
case "integer":
return benc_int($c);
case "list":
return benc_list($c);
case "dictionary":
return benc_dict($c);
default:
return;
}
}
function benc_str($s) {
return strlen($s) . ":$s";
}
function benc_int($i) {
return "i" . $i . "e";
}
function benc_list($a) {
$s = "l";
foreach ($a as $e) {
$s .= benc($e);
}
$s .= "e";
return $s;
}
function benc_dict($d) {
$s = "d";
$keys = array_keys($d);
sort($keys);
foreach ($keys as $k) {
$v = $d[$k];
$s .= benc_str($k);
$s .= benc($v);
}
$s .= "e";
return $s;
}
function benc_resp($d){
benc_resp_raw(benc(array(type => "dictionary", value => $d)));
}

function benc_resp_raw($x){
header("Content-Type: text/plain");
header("Pragma: no-cache");
print($x);
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
//=== end functions
global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;   
mysql_connect($mysql_host, $mysql_user, $mysql_pass);
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
if (strlen($GLOBALS[$x]) != 20)
err("Invalid $x (" . strlen($GLOBALS[$x]) . " - " . urlencode($GLOBALS[$x]) . ")");
if (strlen($passkey) != 32)
err("Invalid passkey (" . strlen($passkey) . " - $passkey)");
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
foreach($banned_clients as $k => $v)
{
if(substr(bin2hex($peer_id), 0, 16) == $v["peer_id"] || substr(bin2hex($peer_id), 0, 6) == $v["peer_id"])
{
$client_ban=array($v["client_name"], $v["reason"]);
}
}
if($client_ban)
err("I'm sorry, $client_ban[0] is banned from this tracker (".stripslashes($client_ban[1]).")");
// Banned Clients - By Petr1fied
mysql_select_db($mysql_db) or die('dbconn: mysql_select_db: ' + mysql_error()); //=== old dbconn(false);  
$valid = @mysql_fetch_row(@mysql_query("SELECT COUNT(*) FROM users WHERE passkey=" . sqlesc($passkey)));
if ($valid[0] != 1) err("Invalid passkey! Re-download the .torrent from $BASEURL");
$res = mysql_query("SELECT id, added, banned, vip, seeders + leechers AS numpeers, UNIX_TIMESTAMP(added) AS ts, countstats FROM torrents WHERE " . hash_where("info_hash", $info_hash));
$torrent = mysql_fetch_assoc($res);
if (!$torrent)
	err("torrent not registered with this tracker");
$torrentid = $torrent["id"];
$fields = "seeder, peer_id, ip, port, uploaded, downloaded, userid, (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(last_action)) AS announcetime, UNIX_TIMESTAMP(last_action) AS ts";

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
if($_GET['compact'] != 1)
{
$resp = "d" . benc_str("interval") . "i" . $announce_interval . "e" . benc_str("private") . 'i1e' . benc_str("peers") . "l";
}
else
{
$resp = "d" . benc_str("interval") . "i" . $announce_interval . "e5:"."peers" ;
}
$peer = array();
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
for($i=0;$i<$peer_num;$i++)
 {
  $o .= substr($peer[$i], 1, 6);
}
$resp .= strlen($o) . ':' . $o . 'e';
}
$selfwhere = "torrent = $torrentid AND " . hash_where("peer_id", $peer_id);
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
if (!isset($self))
{
$valid = @mysql_fetch_row(@mysql_query("SELECT COUNT(*) FROM peers WHERE torrent=$torrentid AND passkey=" . sqlesc($passkey)));
if ($valid[0] >= 2 && $seeder == 'no') err("Connection limit exceeded! You may only leech from one location at a time.");
if ($valid[0] >= 3 && $seeder == 'yes') err("Connection limit exceeded!");
$rz = mysql_query("SELECT id, tlimitseeds, tlimitleeches, tlimitall, uploaded, downloaded, parked, class FROM users WHERE passkey=".sqlesc($passkey)." AND enabled = 'yes' ORDER BY last_access DESC LIMIT 1") or err("Tracker error 2");
if ($MEMBERSONLY && mysql_num_rows($rz) == 0)
err("Unknown passkey. Please redownload the torrent from $BASEURL.");
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
        if ($az["class"] < UC_USER)
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
	    }
        }
        else
        {
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
        if ($upthis > 0 || $downthis > 0)
        if (!($arrfree["free_for_all"] == 'yes' || $torrent["countstats"]=='no' || ($pq && $arrfs["free"] == 'yes'))) // is it a non free torrent
        $updq[0]="downloaded = downloaded + $downthis";
        $updq[1]="uploaded = uploaded + " . (($arrfs['doubleup']=='yes')?($upthis*2):$upthis);
        $udq=implode(',',$updq);
        mysql_query("UPDATE users SET $udq WHERE id=$userid") or err("Tracker error 3");
        }
        //===end
        ///////////////////////////////////////////////////////////////////////////////       
  
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
if ($event == "completed") {
$updateset[] = "times_completed = times_completed + 1";
$finished = ", finishedat = UNIX_TIMESTAMP()";
$finished1 = ", complete_date = '".get_date_time()."', finished = 'yes'";

}
mysql_query("UPDATE peers SET ip = ".sqlesc($ip).", port = $port, connectable = '$connectable', uploaded = $uploaded, downloaded = $downloaded, to_go = $left, last_action = NOW(), seeder = '$seeder', agent = ".sqlesc($agent)." $finished WHERE $selfwhere") or err("PL Err 1");
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