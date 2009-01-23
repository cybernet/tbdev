<?php
require "include/bittorrent.php";
dbconn();
maxcoder();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

$search = trim($HTTP_GET_VARS['search']);
$class = $HTTP_GET_VARS["class"];

if ($class == '-' || !is_valid_id($class))
$class = '';
$class = 0 + $class;
if ($search != '' AND $contains == '' || $class)
 $query = "username LIKE " . sqlesc("$search%") . " AND status='confirmed'";
else if ($search == '' AND $contains != '' || $class)
 $query = "username LIKE " . sqlesc("%$contains%") . " AND status='confirmed'";
else
{
    $letter = trim($_GET["letter"]);
 if (strlen($letter) > 1)
   die;

 if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false)
   $letter = "a";
 $query = "username LIKE '$letter%' AND status='confirmed'";
}
if ($search != '' AND $contains == '' || $class)
$query = "username LIKE " . sqlesc("$search%") . " AND status='confirmed'";
else if ($search == '' AND $contains != '' || $class)
$query = "username LIKE " . sqlesc("%$contains%") . " AND status='confirmed'";
else
{
$letter = trim($_GET["letter"]);
if (strlen($letter) > 1)
die;

if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false)
$letter = "a";
$query = "username LIKE '$letter%' AND status='confirmed'";
}

stdhead("Users");

print("<h1>Users</h1>\n");

print("<form method=get action=?>\n");
print("Begins with: <input type=text size=30 name=search>\n");
print("Or Contains: <input type=text size=30 name=contains>\n");
print("<select name=class>\n");
print("<option value='-'>(any class)</option>\n");
for ($i = 0;;++$i)
{
if ($c = get_user_class_name($i))
print("<option value=$i" . ($class && $class == $i ? " selected" : "") . ">$c</option>\n");
else
break;
}
print("</select><br>\n");
print("<input type=submit class=btn value='Okay'>\n");
print("</form>\n");

print("<p>\n");

for ($i = 97; $i < 123; ++$i)
{
$l = chr($i);
$L = chr($i - 32);
if ($l == $letter)
print("<b>$L</b>\n");
else
print("<a href=users.php?letter=$l><b>$L</b></a>\n");
}

print("</p>\n");

print("<table width=90% border=0 cellspacing=0 cellpadding=0>\n");
print("<tr><td class=colhead width=140 height=20 align=center>User name</td><td class=colhead width=50 align=center>Ratio</td><td class=colhead width=135 align=center>Registered</td><td class=colhead width=135 align=center>Last access</td><td class=colhead width=60 align=center>Class</td><td class=colhead width=70 align=center>Gender</td><td class=colhead width=100 align=center>Country</td></tr>\n");
print("</table>\n");
?>
<iframe width="90%" height="400" src="users_body.php?letter=<?=$letter?>&search=<?=$search?>&contains=<?=$contains?>&class=<?=$class?>"></iframe>
<?

stdfoot();
die;

?>
