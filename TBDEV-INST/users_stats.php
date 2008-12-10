<?php
require_once ("include/bittorrent.php");
require_once ("include/user_functions.php");

dbconn(false);

loggedinorreturn();
stdhead();

$uid = (int) 0 + $_GET["uid"];

if ($CURUSER["class"] < UC_MODERATOR) die("WIP");

if (!$uid || $CURUSER["id"] != $uid) die("Error, Bad/Unauthorised id");

$time_offset = 7;
$today = getdate();
$date = date("Y-m-d");
$date2 = date("Y-m-d", ($today[0] - $time_offset * 86400));
//$query = "SELECT * FROM users_stats WHERE uid=$CURUSER[id] AND time > '$date2%' ORDER BY time ASC";
$query = "SELECT * FROM users_stats WHERE uid=" . sqlesc($uid) ." AND time > '$date2%' ORDER BY time ASC";
$res = mysql_query($query);


$count = 0;

while ($arr = mysql_fetch_assoc($res)) {
$h = explode(":",$arr["time"]);

if ($cur_date != $h[0]) {
$count++;
$d[$count] = $cur_date = $h[0];
}

$hours[$count][$h[1]] = $arr["timeon"];
}

$html .= "<h3>Minutes online in last $time_offset days.</h3>".
"<table width=400>".
"<tr>".
"<td colspan=26 align=center>Total Time Online in last $time_offset Days</td>".
"</tr><tr>".
"<td>Time</td>";

for ($x=1; $x <= 24; $x++)
$html .= "<td>".($x == 24 ? "00" : $x).":00</td>";

$html .= "<td>Total(s)</td>".
"</tr>";

for ($count = 1; $count <= sizeof($hours); $count++) {
$tot_hours = 0;
$html .="<tr>".
"<td>".$d[$count]."</td>";

for ($x=1; $x <= 24; $x++) {
$html .= "<td align=center>".
($hours[$count][$x] ?
"<font color=green>".mkprettytime($hours[$count][$x] + ($hours[$count][$x] ?
($d[$count] == $date && $today["hours"] == $x ?
(int) $_SESSION["timeon"] :
"") :
""))."</font>" :
"<font color=red>".mkprettytime($hours[$count][$x])."</font>").
"</td>";
$tot_hours += $hours[$count][$x];
}
$html .= "<td>".mkprettytime($tot_hours)."</td>".
"</tr>";
}

$html .= "</table><br/><br/>";

///////////////////////////////////////////////////////

$html .= "<h3>Total online time in last $time_offset days.</h3>".
"<table width=400>".
"<tr>".
"<td colspan=120 align=center>Minutes</td>".
"</tr><tr>".
"<td>Date</td>";

for ($x=0; $x <= 60; $x=$x+2)
$html .= "<td>".$x.":00</td>";

$html .= "</tr>";

for ($count = 1; $count <= sizeof($hours); $count++) {
$tot_hours = 0;
$html .= "<tr>".
"<td>".$d[$count]."<br/></td>";

for ($x=1; $x <= 24; $x++) {
$td = ($hours[$count][$x] ?
"<td class=embedded bgcolor=red> </td>" :
"<td class=embedded bgcolor=green> </td>");
$tot_hours += $hours[$count][$x];
}

$tot_mins = explode(":", mkprettytime($tot_hours));
$tot_mins[0] = (sizeof($tot_mins) == 3 ? $tot_mins[1] + ($tot_mins[0] * 60) : $tot_mins[0]);

for($z=1; $z <= $tot_mins[0]; $z=$z+2)
$html .= ($z == $tot_mins[0] ? "<td class=embedded bgcolor=green>".mkprettytime($tot_mins[0])."</td>" : $td);

$html .= "</tr>";
}

$html .= "</table>";


echo $html;

stdfoot();

?>