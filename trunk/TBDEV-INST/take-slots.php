<?php
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
if (get_user_class() < UC_ADMINISTRATOR)
hacker_dork("take-slots");

stdhead("Bonus Slots Manager");

$class = $_POST['class'];

if($_POST['1'] == "Give 1 Slot"){
$res = mysql_query("UPDATE users SET freeslots = freeslots + 1 WHERE class $class");
}
if($_POST['2'] == "Give 2 Slots"){
$res = mysql_query("UPDATE users SET freeslots = freeslots + 2 WHERE class $class");
}
if($_POST['3'] == "Give 3 Slots"){
$res = mysql_query("UPDATE users SET freeslots = freeslots + 3 WHERE class $class");
}
if($_POST['5'] == "Give 5 Slots"){
$res = mysql_query("UPDATE users SET freeslots = freeslots + 5 WHERE class $class");
}
if($_POST['10'] == "Give 10 Slots"){
$res = mysql_query("UPDATE users SET freeslots = freeslots + 10 WHERE class $class");
}
if($_POST['0'] == "Reset Slots to Zero"){
$res = mysql_query("UPDATE users SET freeslots = 0 WHERE class $class");
}
?>
<br />
<p><a href="manage-slots.php"><strong>Return</strong></a></p>
<?
stdfoot();
?>