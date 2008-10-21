<?php
function commenttable($rows) {

global $CURUSER, $HTTP_SERVER_VARS;

begin_main_frame();

begin_frame();

$count = 0;
foreach ($rows as $row)

{
$querie = sql_query("SELECT anonymous FROM comments WHERE id = $row[id]");
$arraya = mysql_fetch_assoc($querie);

print("<p class=sub>#" . $row["id"] . " by ");

$title = "(" . get_user_class_name($row["class"]) . ")";
$title = "";

if ($arraya['anonymous'] == 'no' && isset($row["username"]))
{
$username = $row["username"];
$ratres = sql_query("SELECT uploaded, downloaded from users where username='$username'");
$rat = mysql_fetch_array($ratres);
if ($rat["downloaded"] > 0)

{
$ratio = $rat['uploaded'] / $rat['downloaded'];
$ratio = number_format($ratio, 3);
$color = get_ratio_color($ratio);
if ($color)
$ratio = "<font color=$color>$ratio</font>";
}
else
if ($rat["uploaded"] > 0)
$ratio = "Inf.";
else

$ratio = "---";

print("<a name=comm". $row["id"] . " href=userdetails.php?id=" . $row["user"] . "><b>" . safechar($row["username"]) . "</b></a> " . $title . " " . ($row["donor"] == "yes" ? "<img src=pic/star.gif alt='Donor'>" : "") . ($row["warned"] == "yes" ? "<img src=". "/pic/warned.gif alt=\"Warned\">" : "") . " (Ratio: $ratio)\n");

}

else if (!isset($row["username"])) {
print("<a name=\"comm" . $row["id"] . "\"><i>(orphaned)</i></a>\n");
}

else if ($arraya['anonymous'] == 'yes') {
print("<a name=\"comm" . $row["id"] . "\"><font color=blue><b>Anonymous</b></font></a>\n");
}

print(" at " . $row["added"] . " GMT" .
($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "- [<a href=comment.php?action=edit&amp;cid=$row[id]>".Edit."</a>] " : "") .
(get_user_class() >= UC_VIP ? " - [<a href=report.php?type=Comment&id=$row[id]>Report this Comment</a>]" : "") .
(get_user_class() >= UC_MODERATOR ? "- [<a href=comment.php?action=delete&amp;cid=$row[id]>".Delete."</a>]" : "") .
($row["editedby"] && get_user_class() >= UC_MODERATOR ? " - [<a href=comment.php?action=vieworiginal&amp;cid=$row[id]>".View_original."</a>]" : "") . "</p>\n");
$resa = sql_query("SELECT owner, anonymous FROM torrents WHERE owner = $row[user]");
$array = mysql_fetch_assoc($resa);

if ($row['anonymous'] == 'yes' && $row['user'] == $array['owner']) {
$avatar = "/pic/default_avatar.gif";
}

else {
$avatar = ($CURUSER["avatars"] == "yes" ? safechar($row["avatar"]) : "");
}

if (!$avatar)
$avatar = "/pic/default_avatar.gif";
begin_table(true);
print("<tr valign=top>\n");
print("<td align=center width=100 style='padding: 0px'><img width=100 src=$avatar></td>\n");
print("<td class=text>" . format_comment($row["text"]) . "</td>\n");
print("</tr>\n");
end_table();
}
end_frame();
end_main_frame();
}
?>