<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
dbconn();
maxcoder();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
function bark($msg) {
stdhead();
stdmsg("Thanks failed!", $msg);
stdfoot();
exit;
}

if (!isset($CURUSER))
die();

if (!mkglobal("id"))
die();

$id = 0 + $id;
if (!$id)
die();

$res = mysql_query("SELECT 1 FROM torrents WHERE id =".unsafeChar($id)."");
$row = mysql_fetch_array($res);
if (!$row)
die();

$ras = mysql_query("select 1 from thanks WHERE torid='$id' AND uid =" .unsafeChar($CURUSER["id"]). " ") or die(mysql_error());
$raw = mysql_fetch_array($ras);
if ($raw)
bark("You already thanked.");

$text = ":thankyou:";

mysql_query("INSERT INTO thanks (uid, torid, thank_date) VALUES (" .unsafeChar($CURUSER["id"]). ",$id, '" . unsafeChar(get_date_time()) . "')");
 
mysql_query("INSERT INTO comments (user, torrent, added, text, ori_text) VALUES (" .
     unsafeChar($CURUSER["id"]) . ",$id, '" . unsafeChar(get_date_time()) . "', " . unsafeChar($text) .
      "," . unsafeChar($text) . ")");


$newid = mysql_insert_id();

mysql_query("UPDATE torrents SET thanks = thanks + 1 WHERE id = $id");

header("Refresh: 0; url=details.php?id=$id&viewcomm=$newid#comm$newid");
?>