<?php
require ("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
dbconn(false);
maxcoder();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
// delete items older than a week
$secs = 60 * 60 * 60;
if (get_user_class() < UC_MODERATOR)
stderr("Error", "Permission denied!");
stdhead("Site log");
mysql_query("DELETE FROM sitelog WHERE " . gmtime() . " - UNIX_TIMESTAMP(added) > $secs") or sqlerr(__FILE__, __LINE__);
mysql_query("REPAIR TABLE sitelog");
mysql_query("OPTIMIZE TABLE sitelog");
$res = mysql_query("SELECT COUNT(*) FROM sitelog");
$row = mysql_fetch_array($res);
$count = $row[0];
$perpage = 30;
list($pagertop, $pagerbottom, $limit) = pager(100, $count, "log.php?");

$res = mysql_query("SELECT added, txt FROM sitelog ORDER BY added DESC $limit") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 0)

  print("<b>Log is empty.</b>\n");
else
{

//echo $pagertop;
print("<table align=center cellpadding=5><tr><td class=pager>$pagertop</td></tr></table>");

  print("<table width=700 class=detail border=1 cellspacing=0 cellpadding=5>\n");
  print("<tr><td class=tabletitle align=left><b>Date</b></td><td class=tabletitle align=left><b>Time</b></td><td class=tabletitle align=left><b>Logged event</b></td></tr>\n");
  while ($arr = mysql_fetch_assoc($res))
  {
$color = 'white';
if (strpos($arr['txt'],'done')) $color = "#dedede";
if (strpos($arr['txt'],'Uploaded by')) $color = "#2e8b21";
if (strpos($arr['txt'],'was created')) $color = "#CC9966";
if (strpos($arr['txt'],'was invited by')) $color = "#CC9966";
if (strpos($arr['txt'],'was invited to the site.')) $color = "#CC9966";
if (strpos($arr['txt'],'was deleted by')) $color = "#CC6666";
if (strpos($arr['txt'],'Deleted')) $color = "#CC6666";
if (strpos($arr['txt'],'was updated by')) $color = "#1dab20";
if (strpos($arr['txt'],'Edited by')) $color = "#e8c938";
    $date = substr($arr['added'], 0, strpos($arr['added'], " "));
    $time = substr($arr['added'], strpos($arr['added'], " ") + 1);

    print("<tr class=tableb><td class=detail style=background-color:$color><font color=black>$date</td><td class=detail style=background-color:$color><font color=black>$time</td><td style=background-color:$color class=detail align=left><font color=black>".$arr['txt']."</font></font></font></td></tr>\n");
  }
  print("</table>");
}
//echo $pagerbottom;
print("<table align=center cellpadding=5><tr><td class=pager>$pagerbottom</td></tr></table>");

 print("<p>Sitelog</p>\n");
 stdfoot();
?>
