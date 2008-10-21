<?php
require "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/bbcode_functions.php";
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
parked();
$out = $_GET['out'];

if ($out)
$resmes = mysql_query("SELECT COUNT(*) FROM messages WHERE sender=" . $CURUSER["id"] . " AND location IN ('out','both')") or die("barf!");
else
$resmes = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location IN ('in','both')") or die("barf!");
$arrmes = mysql_fetch_array($resmes);
$count = $arrmes[0];
$messperpage = 10;
if ($out)
list($pagertop, $pagerbottom, $limit) = pager($messperpage, $count, "inbox.php?out=1&");
else
list($pagertop, $pagerbottom, $limit) = pager($messperpage, $count, "inbox.php?");

if ($out) // Sentbox
{
stdhead("Sentbox", false);
print("<form method=post action=takedelspm.php>");
print("<table class=main width=750 border=0 cellspacing=0 cellpadding=10><tr><td class=embedded>\n");
print("<h1 align=center>Sentbox</h1>\n");
print("<div align=center>(<a href=" . $_SERVER['PHP_SELF'] . ">Inbox</a>)</div>\n");
$res = mysql_query("SELECT messages.*, users.username, users.avatar FROM messages LEFT JOIN users ON users.id = messages.receiver  WHERE sender=" . $CURUSER["id"] . " AND location IN ('out','both') ORDER BY added DESC $limit") or die("barf!");
if (mysql_num_rows($res) == 0)
stdmsg("Information","Your Sentbox is empty");
else {
print($pagertop);
while ($arr = mysql_fetch_assoc($res))
{
$receiver = "<a href=userdetails.php?id=" . $arr["receiver"] . ">" . $arr["username"] . "</a>";
$elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]));

if ($arr['subject'] == "")
$title = 'No subject';
else
$title = $arr['subject'];
$avatar = ($arr[avatar] ? $arr[avatar] : "pic/default_avatar.gif");
$pw[] = "<p><table width=750 border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n".
"<table border=1 width=750 cellspacing=0 cellpadding=0>
	<tr>
		<td class=colhead align=left>&nbsp;
		<a href=\"javascript: klappe_news('a".$arr['id']."')\"><img src=pic/plus.gif border=0></a>&nbsp;"."&nbsp;From <b>$receiver</b> at\n" . $arr["added"] . " ($elapsed ago) GMT\n".($arr["unread"] == "yes" ? "<b>(<font color=red>Unread!</font>)</b>" : "")."
		</td>
		<td class=colhead align=right width=150>
		<a href=deletemessage.php?id=" . $arr["id"] . "&type=out><font style='vertical-align:super'><b>Delete</b></font></a><input type=\"checkbox\" name=\"delpm[]\" value=\"" . $arr[id] . "\" />
		</td>
	</tr>
<table class=main width=100% border=1 cellspacing=0 cellpadding=10>
	<tr>
		<td class=text>\n".
		"<b>Subject:</b>&nbsp;&nbsp;".safechar($title).
		($mess_flag >= 1 ? "<div id=\"ka".$arr['id']."\" style=\"display: none;\">" : "<div id=\"ka".$arr['id']."\" style=\"display: block;\">")."
			<br><br><img src=$avatar border=0 align=left valign=top>&nbsp;".format_comment($arr["msg"])."
		</div>
		</td>
	</tr>
</table></tr></table></p>\n";

$mess_flag = $mess_flag + 1;

}
echo implode("\n",$pw);
unset($pw);
print($pagerbottom);
}
}
else // Inbox
{
stdhead("Inbox", false);
print("<form method=post action=takedelpms.php>");
print("<table class=main width=750 border=0 cellspacing=0 cellpadding=10><tr><td class=embedded>\n");
print("<h1 align=center>Inbox</h1>\n");
print("<div align=center>(<a href=" . $_SERVER['PHP_SELF'] . "?out=1>Sentbox</a>)</div>\n");
$res = mysql_query("SELECT messages.*, users.username, users.avatar FROM messages LEFT JOIN users ON users.id = messages.sender WHERE receiver=" . $CURUSER["id"] . " AND location IN ('in','both') ORDER BY added DESC $limit") or die("barf!");
if (mysql_num_rows($res) == 0)
stdmsg("Information","Your Inbox is empty");
else {
print($pagertop);
while ($arr = mysql_fetch_assoc($res))
{

if (is_valid_id($arr["sender"]))
{
$sender = "<a href=userdetails.php?id=" . $arr["sender"] . ">" . ($arr["username"]?$arr["username"]:"[Deleted]") . "</a>";
}
else
$sender = "System";
$elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]));

if ($arr['subject'] == "")
$title = 'No subject';
else
$title = $arr['subject'];
$avatar = ($arr[avatar] ? $arr[avatar] : "pic/default_avatar.gif");
if ($arr["unread"] == "yes")
mysql_query("UPDATE messages SET unread='no' WHERE id=" . $arr["id"]) or die("arghh");

$pw2[] = "<p><table width=750 border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n".
"<table border=1 width=750 cellspacing=0 cellpadding=0>
	<tr>
		<td class=colhead align=left>&nbsp;
		<a href=\"javascript: klappe_news('a".$arr['id']."')\"><img src=pic/plus.gif border=0></a>&nbsp;"."&nbsp;From <b>$sender</b> at\n" . $arr["added"] . " ($elapsed ago) GMT".($arr["unread"] == "yes" ? "<b>(<font color=red>New</font>)</b>" : "")."
		</td>
		<td class=colhead align=right width=150>".($arr["sender"] ? "<a href=sendmessage.php?receiver=" . $arr["sender"] . "&replyto=" . $arr["id"] .
"><b><font style='vertical-align:super'>Answer</font></b></a> " : "<font class=gray style='vertical-align:super'><b>Answer</b></font> ").
"<font style='vertical-align:super'>|</font> <a href=deletemessage.php?id=" . $arr["id"] . "&type=in><b><font style='vertical-align:super'><b>Delete</b></font></b></a><input type=\"checkbox\" name=\"delpm[]\" value=\"" . $arr[id] . "\" />
		</td>
	</tr>
</table>
<table class=main width=100% border=1 cellspacing=0 cellpadding=10>
	<tr>
		<td class=text>\n".
		"<b>Subject:</b>&nbsp;&nbsp;".safechar($title).
		($mess_flag >= 1 ? "<div id=\"ka".$arr['id']."\" style=\"display: none;\">" : "<div id=\"ka".$arr['id']."\" style=\"display: block;\">")."
			<br><br><img src=$avatar border=0 align=left valign=top>&nbsp;".format_comment($arr["msg"])."
		</div>
		</td>
	</tr>
</table></tr></table></p>\n";

$mess_flag = $mess_flag + 1;

}
echo implode("\n",$pw2);
unset($pw2);
print($pagerbottom);
}
}
print("</td></tr></table>\n");

print("<p align=center><input type=button value=\"Check all\" onClick=\"this.value=check(form)\"><input type=submit value=\"Delete checked\"></p>");
print("</form>");

print("<p align=center><a href=users.php>Find user</a></p>\n");
stdfoot();
?>