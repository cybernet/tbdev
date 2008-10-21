<?php
ob_start("ob_gzhandler");
require "include/bittorrent.php";
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
stdhead("Delete Torrent");
begin_main_frame();
?>
<?php
if($_GET[mode] == "delete"){
if (get_user_class() >= UC_MODERATOR) {
//echo"" . implode(", ", $_POST[delete]) . "";
$table = "torrents";
$table2 = "sitelog";
$res = mysql_query("SELECT id, name,owner,seeders FROM torrents WHERE id IN (" . implode(", ", $_POST[delete]) . ")");
echo"The following torrents has been deleted:<br><br>";
while($row = mysql_fetch_array($res)) {
echo"ID: $row[id] - $row[name]<br>";
$reasonstr = "Dead: 0 seeders, 0 leechers = 0 peers total";
$text = "Torrent $row[id] ($row[name]) was deleted by $CURUSER[username] ($reasonstr)\n";
$added = sqlesc(get_date_time());
write_log("Torrent $row[id] ($row[name]) was deleted by $CURUSER[username] ($reasonstr)\n");
}
mysql_query("DELETE FROM $table where id IN (" . implode(", ", $_POST[delete]) . ")") or die(mysql_error());
}else{
echo"You are not allowed to view this page";
}}
?>
<?php 
end_main_frame();
stdfoot(); 
?>