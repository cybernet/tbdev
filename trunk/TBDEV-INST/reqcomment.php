<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once "include/bbcode_functions.php";
$action = $_GET["action"];
dbconn(false);
maxcoder();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
parked(); //=== uncomment if you use the parked mod

if ($action == "add")
{
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
$reqid = 0 + $_POST["tid"];
if (!is_valid_id($reqid))
stderr("Error", "Wrong ID.");

$res = mysql_query("SELECT request FROM requests WHERE id = $reqid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Error", "No request with that ID.");

$text = trim($_POST["body"]);
if (!$text)
stderr("Error", "Don't leave any fields blank!");

mysql_query("INSERT INTO comments (user, request, added, text, ori_text) VALUES (" .
$CURUSER["id"] . ",$reqid, '" . get_date_time() . "', " . sqlesc($text) .
"," . sqlesc($text) . ")");

$newid = mysql_insert_id();

mysql_query("UPDATE requests SET comments = comments + 1 WHERE id = $reqid");

header("Refresh: 0; url=viewrequests.php?id=$reqid&req_details=1&$newid#comm$newid");

die;
}

$reqid = 0 + $_GET["tid"];
if (!is_valid_id($reqid))
stderr("Error", "Wrong ID.");

$res = mysql_query("SELECT request FROM requests WHERE id = $reqid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Error", "Wrong ID.");

stdhead("Add comment to \"" . $arr["request"] . "\"");

print("<p><form method=post name=compose action=reqcomment.php?action=add><input type=hidden name=tid value=$reqid/>".
"<table border=1 cellspacing=0 cellpadding=10><tr><td class=colhead align=center colspan=2><b>".
"Comment on Request: " . safechar($arr["request"]) . "</b></td><tr><tr><td align=right class=clearalt6><b>comment:</b>".
"</td><td align=left class=clearalt6>\n");
print("<textarea name=body rows=10 cols=60></textarea></p>\n");
print("</td></tr><tr><td align=center colspan=2 class=clearalt6><input type=submit value='".Okay."' class=button></td></tr><br><br><br>\n");

$res = mysql_query("SELECT comments.id, text, UNIX_TIMESTAMP(comments.added) as utadded, UNIX_TIMESTAMP(editedat) as uteditedat, comments.added, username, users.id as user, users.class, users.avatar FROM comments LEFT JOIN users ON comments.user = users.id WHERE request = $reqid ORDER BY comments.id DESC LIMIT 5");
$allrows = array();
while ($row = mysql_fetch_array($res))
$allrows[] = $row;

if (count($allrows)) {
commenttable($allrows);
}

stdfoot();

die;
}
elseif ($action == "edit")
{
$commentid = 0 + $_GET["cid"];
if (!is_valid_id($commentid))
stderr("Error", "Wrong ID.");

$res = mysql_query("SELECT * FROM comments WHERE id=$commentid") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Error", "Wrong ID.");

if ($arr["user"] != $CURUSER["id"] && get_user_class() < UC_MODERATOR)
stderr("Error", "Access denied.");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
$text = $_POST["body"];

if ($text == "")
stderr("Error", "Don't leave any fields blank!");

$text = sqlesc($text);

$editedat = sqlesc(get_date_time());

mysql_query("UPDATE comments SET text=$text, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$commentid") or sqlerr(__FILE__, __LINE__);
$reqid = $arr["request"];
header("Refresh: 0; url=viewrequests.php?id=$reqid&req_details=1");

die;
}

//===edit request comment

stdhead("Edit comment");
print("<h1>Edit comment</h1><form method=post name=compose action=reqcomment.php?action=edit&cid=$commentid>".
"<input type=hidden name=returnto value=\"" . $_SERVER["HTTP_REFERER"] . "\" /><input type=hidden name=cid value=$commentid />".
"<p align=center><table border=1 cellspacing=1><tr><td align=center>\n");
$body = $arr['text'];
print("<textarea name=body rows=10 cols=60></textarea></p>\n");
print("</td></tr><tr><td align=center colspan=2><p><input type=\"submit\" class=button value=\"Edit!\" /></p></form></td></tr><br></table>\n");
stdfoot();
die;
}
elseif ($action == "delete")
{
if (get_user_class() < UC_MODERATOR)
stderr("Error", "Access denied.");

$commentid = 0 + $_GET["cid"];

if (!is_valid_id($commentid))
stderr("Error", "Invalid ID.");

$sure = $_GET["sure"];

if (!$sure)
{
$referer = $_SERVER["HTTP_REFERER"];
stderr("Delete comment", "You`re about to delete this comment. Click\n" .
"<a href=?action=delete&cid=$commentid&sure=1" .
($referer ? "&returnto=" . urlencode($referer) : "") .
">here</a>, if you`re sure.");
}


$res = mysql_query("SELECT request FROM comments WHERE id=$commentid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if ($arr)
$reqid = $arr["request"];

mysql_query("DELETE FROM comments WHERE id=$commentid") or sqlerr(__FILE__,__LINE__);
if ($reqid && mysql_affected_rows() > 0)
mysql_query("UPDATE requests SET comments = comments - 1 WHERE id = $reqid");

$returnto = safechar($_GET["returnto"]);

if ($returnto)
header("Location: $returnto");
else
header("Location: $BASEURL/");

die;
}
elseif ($action == "vieworiginal")
{
if (get_user_class() < UC_MODERATOR)
stderr("Error", "Access denied.");

$commentid = 0 + $_GET["cid"];

if (!is_valid_id($commentid))
stderr("Error", "Invalid ID.");

$res = mysql_query("SELECT c.*, t.name FROM comments AS c JOIN requests AS t ON c.request = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Error", "Invalid ID.");

stdhead("Original");
print("<h1>Original content of comment #$commentid</h1><p>\n");
print("<table width=500 border=1 cellspacing=0 cellpadding=5>");
print("<tr><td class=comment>\n");
echo safechar($arr["ori_text"]);
print("</td></tr></table>\n");

$returnto = $_SERVER["HTTP_REFERER"];

if ($returnto)
print("<p><font size=small>(<a href=$returnto>Back</a>)</font></p>\n");

stdfoot();
die;
}
elseif ($action == "edit")
{
$commentid = 0 + $_GET["cid"];
if (!is_valid_id($commentid))
stderr("Error", "Invalid ID.");

$res = mysql_query("SELECT * FROM comments WHERE id=$commentid") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Error", "Invalid ID.");

if ($arr["user"] != $CURUSER["id"] && get_user_class() < UC_MODERATOR)
stderr("Error", "Permission denied.");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
$text = $_POST["text"];
$returnto = safechar($_POST["returnto"]);

if ($text == "")
stderr("Error", "Comment body cannot be empty!");

$text = sqlesc($text);

$editedat = sqlesc(get_date_time());

mysql_query("UPDATE comments SET text=$text, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$commentid") or sqlerr(__FILE__, __LINE__);

if ($returnto)
header("Location: $returnto");
}
}
else
stderr("Error", "Unknown action");

die;
?>