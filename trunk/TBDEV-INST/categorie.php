<?php
ob_start();
require_once("include/bittorrent.php");
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
if (get_user_class() < UC_MODERATOR)
hacker_dork("Category Manage - Nosey Cunt !");
mysql_connect($mysql_host,$mysql_user,$mysql_pass);
mysql_select_db($mysql_db);
stdhead("Categories");
begin_frame("Categories", center);

///////////////////// D E L E T E C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\

$sure = $_GET['sure'];
if($sure == "yes") {
$delid = $_GET['delid'];
$query = "DELETE FROM categories WHERE id='$delid' LIMIT 1";
$sql = mysql_query($query);
echo("Category succesfully deleted! [ <a href='categorie.php'>Back</a> ]");
end_frame();
stdfoot();
die();
}
$delid = $_GET['delid'];
$name = $_GET['cat'];
if($delid > 0) {
echo("Are you sure you would like to delete this category? ($name) ( <strong><a href='". $_SERVER['PHP_SELF'] . "?delid=$delid&cat=$name&sure=yes'>Yes</a></strong> / <strong><a href='$BASEURL'>No</a></strong> )");
end_frame();
stdfoot();
die();

}

///////////////////// E D I T A C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$edited = $_GET['edited'];
if($edited == 1) {
$id = $_GET['id'];
$cat_name = $_GET['cat_name'];
$cat_img = $_GET['cat_img'];
$query = "UPDATE categories SET
name = '$cat_name',
image = '$cat_img' WHERE id='$id'";
$sql = mysql_query($query);
if($sql) {
echo("<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td><div align='center'>Well done! Your category has been edited <strong>succesfully!</strong> [ <a href='categorie.php'>Back</a> ]</div></tr>");
echo("</table>");
end_frame();
stdfoot();
die();
}
}

$editid = $_GET['editid'];
$name = $_GET['name'];
$img = $_GET['img'];
if($editid > 0) {
echo("<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>");
echo("<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<div align='center'><input type='hidden' name='edited' value='1'>Now editing category <strong>&quot;$name&quot;</strong></div>");
echo("<br>");
echo("<input type='hidden' name='id' value='$editid'<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td>Category Name: </td><td align='right'><input type='text' size=50 name='cat_name' value='$name'></td></tr>");
echo("<tr><td>Category Image Name: </td><td align='right'><input type='text' size=50 name='cat_img' value='$img'></td></tr>");
echo("<tr><td></td><td><div align='right'><input type='Submit'></div></td></tr>");
echo("</table></form>");
end_frame();
stdfoot();
die();
}

///////////////////// A D D A N E W C A T E G O R Y \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$add = $_GET['add'];
if($add == 'true') {
$cat_name = $_GET['cat_name'];
$cat_img = $_GET['cat_img'];
$query = "INSERT INTO categories SET
name = '$cat_name',
image = '$cat_img'";
$sql = mysql_query($query);
if($sql) {
$success = TRUE;
} else {
$success = FALSE;
}
}
print("<strong>Add A New Category!</strong>");
print("<br />");
print("<br />");
echo("<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>");
echo("<table class=main cellspacing=0 cellpadding=5 width=50%>");
echo("<tr><td>Category Name: </td><td align='right'><input type='text' size=50 name='cat_name'></td></tr>");
echo("<tr><td>Category Image Name: </td><td align='right'><input type='text' size=50 name='cat_img'><input type='hidden' name='add' value='true'></td></tr>");
echo("<tr><td></td><td><div align='right'><input type='Submit'></div></td></tr>");
echo("</table>");
if($success == TRUE) {
print("<strong>Success!</strong>");
}
echo("<br>");
echo("</form>");

///////////////////// E X I S T I N G C A T E G O R I E S \\\\\\\\\\\\\\\\\\\\\\\\\\\\

print("<strong>Existing Categories:</strong>");
print("<br />");
print("<br />");
echo("<table class=main cellspacing=0 cellpadding=5>");
echo("<td>ID:</td><td>Name:</td><td>Picture:</td><td>Browse Category:</td><td>Edit:</td><td>Delete:</td>");
$query = "SELECT * FROM categories WHERE 1=1";
$sql = mysql_query($query);
while ($row = mysql_fetch_array($sql)) {
$id = $row['id'];
$name = $row['name'];
$img = $row['image'];
echo("<tr><td><strong>$id</strong> </td> <td><strong>$name</strong></td> <td><img src='$BASEURL/pic/$img' border='0' /></td><td><div align='center'><a href='browse.php?cat=$id'><img src='$BASEURL/pic/viewnfo.gif' border='0' class=special /></a></div></td> <td><a href='" . $_SERVER['PHP_SELF'] . "?editid=$id&name=$name&img=$img'><div align='center'><img src='$BASEURL/pic/multipage.gif' border='0' class=special /></a></div></td> <td><div align='center'><a href='" . $_SERVER['PHP_SELF'] . "?delid=$id&cat=$name'><img src='$BASEURL/pic/warned2.gif' border='0' class=special align='center' /></a></div></td></tr>");
}

echo("</table>");
end_frame();
stdfoot();

?>
