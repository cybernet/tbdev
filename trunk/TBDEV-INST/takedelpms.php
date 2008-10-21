<?php
require_once("include/bittorrent.php");
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

if(isset($_POST["delpm"]))
{

$res = mysql_query ("SELECT location, receiver,id FROM messages WHERE id IN (" . implode(", ", $_POST["delpm"]) . ") AND location IN ('in','both')");

while ($arr = mysql_fetch_assoc($res))
{
if ($arr[receiver] == $CURUSER[id])
 if ($arr[location] == 'both')
  mysql_query("UPDATE messages SET location = 'out' WHERE id=$arr[id]");
 else
  mysql_query ("DELETE from messages WHERE id = $arr[id]") or sqlerr();
}
header("Refresh: 0; url=inbox.php");
}
if ((!isset($_POST["delpm"])) && (!isset($_POST["delpmout"])))
{
header("Refresh: 0; url=inbox.php");
die();
}
?>