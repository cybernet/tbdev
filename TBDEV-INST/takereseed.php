<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
dbconn();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}


stdhead("Reseed request");

begin_main_frame();

$reseedid = 0 + $_GET["reseedid"];


$res = mysql_query("SELECT snatched.userid, snatched.torrentid, users.id FROM snatched inner join users on snatched.userid = users.id  AND snatched.torrentid = $reseedid") or sqlerr();
$pn_msg = "Hell-Oh... this is a system generated re-seed request message: \n\n User [b][url=$BASEURL/userdetails.php?id=" . $CURUSER["id"] . "]" . $CURUSER["username"] . "[/url][/b] has asked for a reseed on torrent:\n [b][url=$BASEURL/details.php?id=" . $reseedid . "&karma=1]$arr2[name][/url]! [/b]\n\n[b][url=$BASEURL/userdetails.php?id=" . $CURUSER["id"] . "]" . $CURUSER["username"] . "[/url][/b] is a $class, has a ratio of [b]" .$ratio."[/b], and Karma rating of [b]" . $CURUSER["seedbonus"] . "[/b]\n\nIf you can help out, it would be greatly appreciated, \n\nThank You!";
while($row = mysql_fetch_assoc($res)) {
mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES(0, 0, $row[userid], '" . get_date_time() . "', " . sqlesc($pn_msg) . ")") or sqlerr(__FILE__, __LINE__);
//===take karma
mysql_query("UPDATE users SET seedbonus = seedbonus-5.0 WHERE id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);
//===end
}
//=== add to reseed table
mysql_query("INSERT INTO reseed (torrent, user, added)   VALUES ($reseedid, $CURUSER[id], '" . get_date_time() . "')");    
//===end
print("It worked :madgrin:");
end_main_frame();
stdfoot();
?>