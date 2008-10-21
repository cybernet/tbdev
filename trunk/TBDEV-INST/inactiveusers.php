<?php
require ("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
dbconn();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
if (get_user_class() < UC_MODERATOR)
hacker_dork("Inactive Users - Nosey Cunt !");

stdhead("Inactive users");

begin_main_frame("&nbsp;Inactive users");
$days = 100;
$ttl = sqlesc(get_date_time(gmtime() - ($days * 86400)));
$res = mysql_query("SELECT COUNT(*) FROM users WHERE last_access < $ttl AND enabled = 'yes' AND status = 'confirmed' ORDER BY id ASC");
$row = mysql_fetch_array($res);
$count = $row[0];

$perpage = 100;

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "?");
echo '<form method="post" action="email-inac.php?" /><center><h1>Users not logged in last 30 days</h1></center>';
echo $pagertop;
echo '<table width="622" border="0" align="center" cellpadding="5" cellspacing="0">';
echo '<tr><td class=colhead align=left>Username</td><td class=colhead align=left>Class</td><td class=colhead align=center>PM</td><td class=colhead>IP</td><td class=colhead colspan=2 align=center>Joined</td><td class=colhead colspan=2 align=center>Last Access</td><td class=colhead>Download</td><td class=colhead>Upload</td><td class=colhead>Ratio</td><td width=1% class=colhead>&nbsp;&nbsp;</TD></tr>';
$result = mysql_query("SELECT downloaded, uploaded, class, added, last_access, username, ip, id FROM users WHERE last_access < $ttl AND enabled = 'yes' AND status = 'confirmed' ORDER BY id ASC  $limit") or sqlerr(__FILE__, __LINE__);
while($row = mysql_fetch_assoc($result)) {
if ($row["downloaded"] > 0)
{
$ratio = number_format($row["uploaded"] / $row["downloaded"], 3);
$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
}
elseif ($row["uploaded"] > 0)
$ratio = "Inf.";
else
$ratio = "---";
if ($row["ip"] == "")
$row["ip"] = "---";
$date1 = substr($row['added'], 0, strpos($row['added'], " "));
$time1 = substr($row['added'], strpos($row['added'], " ") + 1);
$date2 = substr($row['last_access'], 0, strpos($row['last_access'], " "));
$time2 = substr($row['last_access'], strpos($row['last_access'], " ") + 1);
print("<tr><td><a href=userdetails.php?id=".$row["id"]."><b>".$row["username"]."</b></a></td><td>".get_user_class_name($row["class"])."</td><td><a href=sendmessage.php?receiver=".$row["id"]."><img src=/pic/pm.jpg border=0></a></td><td>".$row["ip"]."</td><td>".$date1."</td><td>".$time1."</td><td>".$date2."</td><td>".$time2."</td><td>".mksize($row["downloaded"])."</td><td>".mksize($row["uploaded"])."</td><td>".$ratio."</td><td><INPUT type=checkbox name='inact[]' value='" . $row["id"] . "'></td></tr>");
}
echo "</table>";
echo $pagerbottom;
echo "<br/><br/><center><INPUT type='button' value='Check all' onClick='this.value=check(form)' />";
if (get_user_class() >= UC_SYSOP)
echo "<br/><br/><br/><input type=submit value='Email Selected Users'></center></form>";
end_main_frame();
stdfoot();
?>