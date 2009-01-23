<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
function bark($msg) {
  stdhead();
  stdmsg("Delete failed!", $msg);
  stdfoot();
  exit;
}
if (!mkglobal("id"))
	bark("missing form data");
$id = 0 + $id;
if (!$id)
	die();
dbconn();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
$res = sql_query("SELECT name,owner,seeders FROM torrents WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
	die();
if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
	bark("You're not the owner! How did that happen?\n");
$rt = 0 + $_POST["reasontype"];
if (!is_int($rt) || $rt < 1 || $rt > 5)
	bark("Invalid reason $rt.");
$r = $_POST["r"];
$reason = $_POST["reason"];
if ($rt == 1)
	$reasonstr = "Dead: 0 seeders, 0 leechers = 0 peers total";
elseif ($rt == 2)
	$reasonstr = "Dupe" . ($reason[0] ? (": " . trim($reason[0])) : "!");
elseif ($rt == 3)
	$reasonstr = "Nuked" . ($reason[1] ? (": " . trim($reason[1])) : "!");
elseif ($rt == 4)
{
	if (!$reason[2])
		bark("Please describe the violated rule.");
  $reasonstr = "TB rules broken: " . trim($reason[2]);
}
else
{
	if (!$reason[3])
		bark("Please enter the reason for deleting this torrent.");
  $reasonstr = trim($reason[3]);
}
deletetorrent($id);
sql_query("UPDATE avps SET value_d='0000-00-00 00:00:00', value_s='' WHERE arg='bestfilmofweek' AND value_s=".$id);
if ($CURUSER["anonymous"]=='yes')
write_log("Torrent $id ($torrent) was deleted by Anonymous $CURUSER[username] ($reasonstr)");
else
write_log("Torrent $id ($row[name]) was deleted by $CURUSER[username] ($reasonstr)");
//===remove karma
sql_query("UPDATE users SET seedbonus = seedbonus-15.0 WHERE id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);
//===end
stdhead("Torrent deleted!");
if (isset($_POST["returnto"]))
	$ret = "<a href=\"" . safechar($_POST["returnto"]) . "\">Go back to whence you came</a>";
else
	$ret = "<a href=\"./\">Back to index</a>";
?>
<h2>Torrent deleted!</h2>
<p><?= $ret ?></p>
<?
stdfoot();
?>