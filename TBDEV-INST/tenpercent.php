<?php
include_once("include/bittorrent.php");
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

$uploaded = $CURUSER["uploaded"];
$downloaded = $CURUSER["downloaded"];
$newuploaded = ($uploaded * 1.1);

if ($downloaded > 0) {
$ratio = number_format($uploaded / $downloaded, 3);
$newratio = number_format($newuploaded / $downloaded, 3);
$ratiochange = number_format(($newuploaded / $downloaded) - ($uploaded / $downloaded), 3);
} elseif ($uploaded > 0)
$ratio = $newratio = $ratiochange = "Inf.";
else
$ratio = $newratio = $ratiochange = "---";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

if ($CURUSER["tenpercent"] == "yes")
stderr("Used", "It appears that you have already used your 10% addition.");

$sure = $_POST["sure"];

if (!$sure)
stderr("Are you sure?", "It appears that you are not yet sure whether you want to add 10% to your upload or not. Once you are sure you can <a href=tenpercent.php>return</a> to the 10% page.");

$time = date("F j Y");
$subject = "10% Addition";
$msg = "Today, $time, you have increased your total upload amount by 10% from ".mksize($uploaded)." to ".mksize($newuploaded).", which brings your ratio to ".$newratio.".";

$res = mysql_query("UPDATE users SET uploaded = uploaded * 1.1, tenpercent = 'yes' WHERE id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);
$res1 = mysql_query("INSERT INTO messages (sender, poster, receiver, subject, msg, added) VALUES (0, 0, $CURUSER[id], ".sqlesc($subject).", ".sqlesc($msg).", '".get_date_time()."')") or sqlerr(__FILE__, __LINE__);

if (!$res)
stderr("Error", "It appears that something went wrong while trying to add 10% to your upload amount.");
else
stderr("10% Added", "Your total upload amount has been increased by 10% from <b>".mksize($uploaded)."</b> to <b>".mksize($newuploaded)."</b>, which brings your ratio to <b>$newratio</b>.");
}


stdhead("");
print("<h1>10%</h1>\n");

if ($CURUSER["tenpercent"] == "yes")
print("<h2>It appears that you have already used your 10% addition</h2>\n");

print("<p><table width=700 border=0 cellspacing=0 cellpadding=5><tr><td\n");
print("<table width=700 border=0 cellspacing=0 cellpadding=10><tr><td style='padding-bottom: 0px'>\n");
print("<p><b>How it works:</b></p>");
print("<p class=sub>From this page you can <b>add 10%</b> of your current upload amount to your upload amount bringing it it to <b>110%</b> of its current amount. More details about how this would work out for you can be found in the tables below.</p>");
print("<br><p><b>However, there are some things you should know first:</b></p>");
print("<li>This can only be done <b>once</b>, so chose your moment wisely.");
print("<li>The staff will <b>not</b> reset your 10% addition for any reason.");
print("</td></tr></table>\n");
print("</td></tr></table></p>\n");
print("<p><table width=630 class=main align=center border=0 cellspacing=0 cellpadding=5>\n");
print("<tr><td class=normalrowhead>Current&nbsp;upload&nbsp;amount:</td><td class=normal>".str_replace(" ", "&nbsp;", mksize($uploaded))."</td><td class=embedded width=5%></td><td class=normalrowhead>Increase:</td><td class=normal>".str_replace(" ", "&nbsp;", mksize($newuploaded - $uploaded))."</td><td class=embedded width=5%></td><td class=normalrowhead>New&nbsp;upload&nbsp;amount:</td><td class=normal>".str_replace(" ", "&nbsp;", mksize($newuploaded))."</td></tr>\n");
print("<tr><td class=normalrowhead>Current&nbsp;download&nbsp;amount:</td><td class=normal>".str_replace(" ", "&nbsp;", mksize($downloaded))."</td><td class=embedded width=5%></td><td class=normalrowhead>Increase:</td><td class=normal>".str_replace(" ", "&nbsp;", mksize(0))."</td><td class=embedded width=5%></td><td class=normalrowhead>New&nbsp;download&nbsp;amount:</td><td class=normal>".str_replace(" ", "&nbsp;", mksize($downloaded))."</td></tr>\n");
print("<tr><td class=normalrowhead>Current&nbsp;ratio:</td><td class=normal>$ratio</td><td class=embedded width=5%></td><td class=normalrowhead>Increase:</td><td class=normal>$ratiochange</td><td class=embedded width=5%></td><td class=normalrowhead>New&nbsp;ratio:</td><td class=normal>$newratio</td></tr>\n");
print("</table></p>\n");
print("<p><table align=center border=0 cellspacing=0 cellpadding=5><form name=tenpercent method=post action=tenpercent.php>\n");
print("<tr><td align=center><b>Yes please </b><input type=checkbox name=sure value=yes onclick='if (this.checked) enablesubmit(); else disablesubmit();'></td></tr>\n");
print("<tr><td align=center><input type=submit name=submit value='Add 10%' class=btn disabled></td></tr>\n");
print("</form></table></p>\n");
stdfoot();
?>