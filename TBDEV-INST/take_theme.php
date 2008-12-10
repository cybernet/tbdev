<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
dbconn();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
$set = array();

$updateset = array();
$stylesheet = $_POST["stylesheet"];

if (is_valid_id($stylesheet))
  $updateset[] = "stylesheet = '$stylesheet'";

mysql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]);

header("Location: index.php" . $urladd);

?>