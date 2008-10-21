<?php
require ("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
if (get_user_class() < UC_USER)
stderr( _("Error"), ("Permission denied."));

$file = $_FILES['file'];

if (!$file || $file["size"] == 0 || $file["name"] == "")
stderr( ("Error"), ("Nothing received! The selected file may have been too large.") );

if (file_exists("$DOXPATH/$file[name]"))
stderr( _("Error"), sprintf( ("A file with the name %s already exists!"), "<b>".safechar($file['name'])."</b>") );

$title = trim($_POST["title"]);
if ($title == "")
{
$title = substr($file["name"], 0, strrpos($file["name"], "."));
if (!$title)
$title = $file["name"];
}

$r = mysql_query("SELECT id FROM dox WHERE title=" . sqlesc($title)) or sqlesc();
if (mysql_num_rows($r) > 0)
stderr( ("Error"), sprintf( ("A file with the title %s already exists!"), "<b>".safechar($title)."</b>") );

$url = $_POST["url"];

if ($url != "")
if (substr($url, 0, 7) != "http://" && substr($url, 0, 6) != "ftp://")
stderr( _("Error"), sprintf( ("The URL %s does not seem to be valid."), "<b>" . safechar($url) . "</b>") );

if (!move_uploaded_file($file["tmp_name"], "$DOXPATH/$file[name]"))
stderr( ("Error"), ("Failed to move uploaded file. You should contact an administrator about this error.") );

setcookie("doxurl", $url, 0x7fffffff);

$title = sqlesc($title);
$filename = sqlesc($file["name"]);

$uppedby = $CURUSER["id"];
$size = $file["size"];
$url = sqlesc($url);

mysql_query("INSERT INTO dox (title, filename, added, uppedby, size, url) VALUES($title, $filename, NOW(), $uppedby, $size, $url)") or sqlerr();

header("Location: dox.php");
die;
}

if (get_user_class() >= UC_USER)
{
$delete = $HTTP_GET_VARS["delete"];
if (is_valid_id($delete))
{
$r = mysql_query("SELECT filename,uppedby FROM dox WHERE id=$delete") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($r) == 1)
{
$a = mysql_fetch_assoc($r);
if (get_user_class() >= UC_MODERATOR || $a["uppedby"] == $CURUSER["id"])
{
mysql_query("DELETE FROM dox WHERE id=$delete") or sqlerr(__FILE__, __LINE__);
if (!unlink("$DOXPATH/$a[filename]"))
stderr( _("Warning"), sprintf( _("Unable to unlink file: %s. You should contact an administrator about this error."), "<b>".$a['filename']."</b>") );
}
}
}
}

stdhead( ("Dox") );

print("<h1>".("Dox")."</h1>\n");

$res = mysql_query("SELECT * FROM dox ORDER BY added DESC") or sqlerr();
if (mysql_num_rows($res) == 0)
print("<p>".('Filename')."</p>");
else
{
print("<p><table border=1 cellspacing=0 width=750 cellpadding=5>\n");
print("<tr><td class=colhead align=left>".('Filename')."</td><td class=colhead>".('Date')."</td><td class=colhead>".('Time')."</td>" .
"<td class=colhead>".('Size')."</td><td class=colhead>".('Hits')."</td><td class=colhead>".('Upped by')."</td></tr>\n");

$mod = get_user_class() >= UC_MODERATOR;

while ($arr = mysql_fetch_assoc($res))
{
$r = mysql_query("SELECT username FROM users WHERE id=$arr[uppedby]") or sqlerr();
$a = mysql_fetch_assoc($r);
$title = "<td align=left><a href=".$GLOBALS['DEFAULTBASEURL']."/getdox.php/$arr[filename]><b>" . safechar($arr["title"]) . "</b></a>" .
($mod || $arr["uppedby"] == $CURUSER["id"] ? " <font size=1 class=small><a href=?delete=$arr[id]>[Delete]</a></font>" : "") ."</td>\n";
$added = "<td>" . substr($arr["added"], 0, 10) . "</td><td>" . substr($arr["added"], 10) . "</td>\n";
$size = "<td>" . mksize($arr['size']) . "</td>\n";
$hits = "<td>" . number_format($arr['hits']) . "</td>\n";
$uppedby = "<td><a href=".$GLOBALS['DEFAULTBASEURL']."/userdetails.php?id=$arr[uppedby]><b>$a[username]</b></a></td>\n";
print("<tr>$title$added$size$hits$uppedby</tr>\n");
}
print("</table></p>\n");
print("<p>".('Files are automatically deleted after 14 days')."</p>\n");
}

if (get_user_class() >= UC_USER)
{
$url = $HTTP_COOKIE_VARS["doxurl"];
$maxfilesize = ini_get("upload_max_filesize");
begin_main_frame();
begin_frame("Upload", true);
print("<form enctype=multipart/form-data method=post action=?>\n");
print("<table class=main border=1 cellspacing=0 width=700 cellpadding=5>\n");
print("<tr><td class=rowhead>".('File')."</td><td align=left><input type=file name=file size=60><br>(".('Maximum file size').": ".$maxfilesize.")</td></tr>\n");
print("<tr><td class=rowhead>".('Filename')."</td><td align=left><input type=text name=title size=60><br>(".('Optional, taken from file name if not specified.').")</td></tr>\n");
print("<tr><td colspan=2 align=center><input type=submit value='".('Upload file')."' class=btn></td></tr>\n");
print("</table>\n");
print("</form>\n");
end_frame();
end_main_frame();
}

stdfoot();
?>