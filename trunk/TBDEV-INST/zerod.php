<?php
require ("include/bittorrent.php");
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
// Zero Download BUt they have Uploaded and not owner of the torrents
if (get_user_class() < UC_MODERATOR)
stderr("Error", "Permission denied.");
stdhead("None Owners But Zero Download-100% Upload");
begin_frame("None Owners But Zero Download-100% Upload:", true);
begin_table();
if ($CURUSER["id"] != $user["id"])
{
if (get_user_class() >= UC_MODERATOR)
{
$res = mysql_query("SELECT * FROM snatched WHERE downloaded='0' AND uploaded>'734003200' ORDER BY userid") or sqlerr();//uploaded>'734003200'=only show users uploadomh over 700mb change if needed
$num = mysql_num_rows($res);
print("<tr align=center><td class=colhead width=90>User</td>
<td class=colhead width=45>Torrent</td>
<td class=colhead width=45>Torrent U/Load</td>
<td class=colhead width=45>Site U/Load</td>
<td class=colhead width=45>Site D/Load</td>
<td class=colhead width=45>Finished</td>
<td class=colhead width=125>Cleint</td></tr>\n");
while($ras=mysql_fetch_assoc($res))
{
$rds = mysql_query("SELECT * FROM torrents WHERE owner='".$ras['userid']."' AND torrentname=$torrentname") or sqlerr();
$num3 = mysql_num_rows($rds);
if ($num3 < 1)
{
$ros = mysql_query("SELECT * FROM users WHERE id='".$ras['userid']."' and class<3") or sqlerr();
$num2 = mysql_num_rows($ros);
while($arr = mysql_fetch_assoc($ros))

{
$uploaded = mksize($ras["uploaded"]);
$sdownloaded = mksize($arr["downloaded"]);
$suploaded = mksize($arr["uploaded"]);
$dispname = htmlspecialchars($ras["torrentname"]);
print("<td align=left><b><a href='/userdetails.php?id=" . $arr['id'] . "'>" . $arr['username']."</b></a>" . get_user_icons($arr) . "</td>
<td align=center><a href=\"details.php?id=$id\">$dispname</a></td>
<td align=center>$uploaded</td>
<td align=center>$suploaded</td>
<td align=center>$sdownloaded</td>
<td align=center>$ras[finished]</td>
<td align=center>$ras[agent]</td></tr>\n");
}}
}
}
else
{
print("<br><table width=60% border=1 cellspacing=0 cellpadding=9><tr><td align=center>");
print("<h2>You are not able to view this page.</h2></table></td></tr>");
}
}
stdfoot();
?>