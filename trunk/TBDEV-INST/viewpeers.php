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
stdhead("Peerlist");

if (get_user_class() < UC_MODERATOR) {
stdmsg("Sorry", "No permissions.");
stdfoot();
exit;
}

$count1 = number_format(get_row_count("peers"));
print("<h2 align=center>Peerlist</h2>");
print("<center><font class=small>We have $count1 peers</font></center><br>");
print("<table width=737 border=1 cellspacing=0 cellpadding=10><tr><td class=text align=center>\n");
$res4 = sql_query("SELECT COUNT(*) FROM peers $limit") or sqlerr();
$row4 = mysql_fetch_array($res4);
$count = $row4[0];
$peersperpage = 15;
list($pagertop, $pagerbottom, $limit) = pager($peersperpage, $count, "viewpeers.php?");
print("$pagertop");
$sql = "SELECT * FROM peers ORDER BY started DESC $limit";
$result = mysql_query($sql);
if( mysql_num_rows($result) != 0 ) {
print'<table width=737 border=1 cellspacing=0 cellpadding=5 align=center>';
print'<tr>';
print'<td class=colhead align=center>User</td>';
print'<td class=colhead align=center>Torrent</td>';
//print'<td class=colhead align=center>IP</td>';
print'<td class=colhead align=center>Port</td>';
print'<td class=colhead align=center>Upl.</td>';
print'<td class=colhead align=center>Downl.</td>';
//print'<td class=colhead align=center>Peer-ID</td>';
print'<td class=colhead align=center>Conn.</td>';
print'<td class=colhead align=center>Seeding</td>';
print'<td class=colhead align=center>Started</td>';
print'<td class=colhead align=center>Last<br>Action</td>';
print'<td class=colhead align=center>Prev.<br>Action</td>';
print'<td class=colhead align=center>Upload<br>Offset</td>';
print'<td class=colhead align=center>Download<br>Offset</td>';
print'<td class=colhead align=center>To<br>Go</td>';
print'</tr>';
while($row = mysql_fetch_assoc($result)) {
$sql1 = "SELECT * FROM users WHERE id = $row[userid]";
$result1 = mysql_query($sql1);
while ($row1 = mysql_fetch_assoc($result1)) {
print'<tr>';
print'<td><a href="userdetails.php?id=' . $row['userid'] . '">' . $row1['username'] . '</a></td>';
$sql2 = "SELECT * FROM torrents WHERE id = $row[torrent]";
$result2 = mysql_query($sql2);
while ($row2 = mysql_fetch_assoc($result2)) {
$smallname =substr(safechar($row2["name"]) , 0, 20);
if ($smallname != safechar($row2["name"])) {
$smallname .= '...';
}
#$smallname = safechar($row2["name"]);
print'<td><a href="details.php?id=' . $row['torrent'] . '">' . $smallname . '</td>';
//print'<td align=center>' . $row['ip'] . '</td>';
print'<td align=center>' . $row['port'] . '</td>';
if ($row['uploaded'] < $row['downloaded'])
print'<td align=center><font color=red>' . mksize($row['uploaded']) . '</font></td>';
else
if ($row['uploaded'] == '0')
print'<td align=center>' . mksize($row['uploaded']) . '</td>';
else
print'<td align=center><font color=green>' . mksize($row['uploaded']) . '</font></td>';
print'<td align=center>' . mksize($row['downloaded']) . '</td>';
//print'<td align=center>' . $row['peer_id'] . '</td>';
if ($row['connectable'] == 'yes')
print'<td align=center><font color=green>' . $row['connectable'] . '</font></td>';
else
print'<td align=center><font color=red>' . $row['connectable'] . '</font></td>';
if ($row['seeder'] == 'yes')
print'<td align=center><font color=green>' . $row['seeder'] . '</font></td>';
else
print'<td align=center><font color=red>' . $row['seeder'] . '</font></td>';
print'<td align=center>' . $row['started'] . '</td>';
print'<td align=center>' . $row['last_action'] . '</td>';
print'<td align=center>' . $row['prev_action'] . '</td>';
print'<td align=center>' . mksize($row['uploadoffset']) . '</td>';
print'<td align=center>' . mksize($row['downloadoffset']) . '</td>';
print'<td align=center>' . mksize($row['to_go']) . '</td>';
print'</tr>';
}
}
}
print'</table>';
print("$pagerbottom");
}
else {
print'Nothing here sad.gif';
}
print("</td></tr></table>\n");

stdfoot();
?>