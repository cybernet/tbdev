<?php
include("include/bittorrent.php");
//include("include/user_functions.php");
include("include/bbcode_functions.php");
dbconn();
loggedinorreturn();
$xmasday= mktime(0,0,0,12,25,date("Y"),date("m"));
$today = mktime(date("G"), date("i"), date("s"), date("m"),date("d"),date("Y"));
$gifts = array("upload", "bonus", "invites","bonus2");
$randgift = array_rand($gifts);
$gift = $gifts[$randgift];
$userid = 0 + $CURUSER["id"];
if(!is_valid_id($userid))
stderr("Error", "Invalid ID");
$open = 0 + $_GET["open"];
if($open != 1){
stderr("Error","Invalid url");
}
if($open == 1){
if($today >= $xmasday ){
if($CURUSER["gotgift"] == 'no'){
if($gift == "upload"){
sql_query("UPDATE users SET invites=invites+1, uploaded=uploaded+1024*1024*1024*10, freeslots=freeslots+1, gotgift='yes' WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
stderr("Congratulations!","<img src=\"/pic/gift.png\" style=\"float: left; padding-right:10px;\"> <h2> You just got  1 invite 10 GB upload and bonus 1 freeslot !</h2>
Thanks for your support and sharing through year 2008 ! <br> Merry Christmas and a happy New Year from ".$SITENAME." Crew !");
}
if($gift == "bonus"){
sql_query("UPDATE users SET invites=invites+3,  seedbonus = seedbonus + 1750, gotgift='yes' WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
stderr("Congratulations!","<img src=\"/pic/gift.png\" style=\"float: left; padding-right:10px;\"> <h2> You just got 3 invites 1750 karma bonus points !</h2>
Thanks for your support and sharing through year 2008 ! <br> Merry Christmas and a happy New Year from ".$SITENAME." Crew !");
}
if($gift == "invites") {
sql_query("UPDATE users SET invites=invites+2, seedbonus = seedbonus + 2000,  freeslots=freeslots+3, gotgift='yes' WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
stderr("Congratulations!","<img src=\"/pic/gift.png\" style=\"float: left; padding-right:10px;\"> <h2> You just got 2 invites and 2000 bonus points and a bonus 3 freeslots !</h2>
Thanks for your support and sharing through year 2008 ! <br> Merry Christmas and a happy New Year from ".$SITENAME." Crew !");
}
if($gift == "bonus2"){
sql_query("UPDATE users SET invites=invites+3,  seedbonus = seedbonus + 2500, freeslots=freeslots+5, gotgift='yes' WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
stderr("Congratulations!","<img src=\"/pic/gift.png\" style=\"float: left; padding-right:10px;\"> <h2> You just got 3 invites 1750 karma bonus points !</h2>
Thanks for your support and sharing through year 2008 ! <br> Merry Christmas and a happy New Year from ".$SITENAME." Crew !");
}
} else {
stderr("Sorry...", "You already got your gift !");
}

} else {
stderr("Doh...", "Be patient ! You can't open your present until xmas !  <b>" . date("j", ($xmasday - $today)) . "</b> day(s) to go. <br> today:" . date('l dS \of F Y G:i:s A', $today) . "<br>xmas day:" . date('l dS \of F Y G:i:s A', $xmasday));
}
}
?>