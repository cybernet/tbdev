<?php
require "include/bittorrent.php";
dbconn();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
if (!$CURUSER)
{
header("Location: ".$BASEURL);
die;
}
$filename = substr($_SERVER["PATH_INFO"], strrpos($_SERVER["PATH_INFO"], "/") + 1);
if (!$filename)
  stderr( _("Error"), _("Filename missing") );
  
if (get_user_class() < UC_POWER_USER && filesize("$DOXPATH/$filename") > 1024*1024)
  stderr( _("Error"), _("Sorry, you need to be a power user or higher to download files larger than 1.00 MB.") );
  

$filename = sqlesc($filename);
$res = mysql_query("SELECT * FROM dox WHERE filename=$filename") or sqlerr();
$arr = mysql_fetch_assoc($res);
if (!$arr)
stderr( _("Error"), _("File Not found"));
mysql_query("UPDATE LOW_PRIORITY dox SET hits=hits+1 WHERE id=$arr[id]") or sqlerr();
$file = "$DOXPATH/$arr[filename]";
header("Content-Length: " . filesize($file));
header("Content-Type: application/octet-stream");
readfile($file);
?>