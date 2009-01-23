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

$res = mysql_query("SELECT stylesheet FROM users WHERE id = $CURUSER[id]") or sqlerr();
$arr = mysql_fetch_array($res);
$reu = mysql_query("SELECT uri FROM stylesheets users WHERE id = $arr[stylesheet]") or sqlerr();
$aru = mysql_fetch_array($reu);
$stylesheet = $aru['uri'];
?>
<link rel="stylesheet" href="themes/default/default.css" type="text/css">
<?

$search = trim($HTTP_GET_VARS['search']);
$contains = trim($HTTP_GET_VARS['contains']);
$class = $HTTP_GET_VARS['class'];
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

if ($class)
{
$query .= " AND class=$class";
$q .= ($q ? "&amp;" : "") . "class=$class";
}

$res = mysql_query("SELECT * FROM users WHERE $query ORDER BY username") or sqlerr();
$num = mysql_num_rows($res);

print("<table border=1 cellspacing=0 cellpadding=5>\n");

for ($i = 0; $i < $num; ++$i)
{
$arr = mysql_fetch_assoc($res);
if ($arr['country'] > 0)
{
$cres = mysql_query("SELECT name,flagpic FROM countries WHERE id=$arr[country]");
if (mysql_num_rows($cres) == 1)
{
$carr = mysql_fetch_assoc($cres);
$country = "<td width=80 style='padding: 0px' align=center><img src=pic/flag/$carr[flagpic] alt=\"$carr[name]\"></td>";
}
}
else
$country = "<td width=80 align=center>-----</td>";
if ($arr['added'] == '0000-00-00 00:00:00')
$arr['added'] = '-';
if ($arr['last_access'] == '0000-00-00 00:00:00')
$arr['last_access'] = '-';

if ($arr["downloaded"] > 0)
{
$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
if (($arr["uploaded"] / $arr["downloaded"]) > 100)
$ratio = "100+";
$ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
}
else
if ($arr["uploaded"] > 0)
$ratio = "Inf.";
else
$ratio = "------";

if ($arr['class'] == '0')
$class = 'User ';
if ($arr['class'] == '1')
$class = 'Power User ';
if ($arr['class'] == '2')
$class = 'Vip ';
if ($arr['class'] == '3')
$class = 'Uploader ';
if ($arr['class'] == '4')
$class = 'Moderator ';
if ($arr['class'] == '5')
$class = 'Administrator ';
if ($arr['class'] == '6')
$class = 'Sysop ';
if ($arr['class'] == '7')
$class = 'Coder ';

print("<tr><td width=140 align=left><a href=userdetails.php?id=$arr[id]><b>$arr[username]</b></a>" .($arr["donated"] > 0 ? "<img src=/pic/star.gif border=0 alt='Donor'>" : "")."</td>" .
"<td width=50>$ratio</td><td width=155>$arr[added]</td><td width=155>$arr[last_access]</td>".
"<td width=60 align=center>$class</td><td width=70 align=center> $arr[gender]</td>$country</tr>\n");
}
print("</table>\n");

die;

?>
