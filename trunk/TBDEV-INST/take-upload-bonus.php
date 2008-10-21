<?php
//-------------------------------------------------------------------------
// Manual Ratio Bonus by Ashley
// Version: 1.1
//
// - 1GB, 2GB, 5GB or 10GB bonus/deduction
// - Automatic PM to all members when ADDING bonus upload
// - Choose which usergroups to add too - All, VIP, Powers Users or Staff
//
// ENJOY!
//-------------------------------------------------------------------------
require ("include/bittorrent.php");
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
if (get_user_class() < UC_SYSOP)
stderr("Error", "Permission denied.");

stdhead("Manual Ratio Bonus");

$class = $_POST['class'];

?>

<?php

//-----------------------------------------------
// + 1GB
//-----------------------------------------------

if($_POST['1gig'] == "+1 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded+1073741824 WHERE class $class");

if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have added 1GB to your usergroups upload total!!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}


}

//-----------------------------------------------
// + 2GB
//-----------------------------------------------

if($_POST['2gig'] == "+2 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded+2147483648 WHERE class $class");

if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have added 2GB to your usergroups upload total!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}


}

//-----------------------------------------------
// + 5GB
//-----------------------------------------------

if($_POST['5gig'] == "+5 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded+5368709120 WHERE class $class");

if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have added 5GB to your usergroups upload total!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// + 10GB
//-----------------------------------------------

if($_POST['10gig'] == "+10 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded+10737418240 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have added 10GB to your usergroups upload total!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// + 15GB
//-----------------------------------------------

if($_POST['15gig'] == "+15 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded+16106127360 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have added 15GB to your usergroups upload total!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// + 25GB
//-----------------------------------------------

if($_POST['25gig'] == "+25 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded+26843545600 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have added 25GB to your usergroups upload total!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// + 50GB
//-----------------------------------------------

if($_POST['50gig'] == "+50 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded+53687091200 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have added 50GB to your usergroups upload total!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}


//-----------------------------------------------
// + 75GB
//-----------------------------------------------

if($_POST['75gig'] == "+75 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded+80530636800 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have added 75GB to your usergroups upload total!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// + 100GB
//-----------------------------------------------

if($_POST['100gig'] == "+100 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded+107374182400 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have added 100GB to your usergroups upload total!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// - 1GB
//-----------------------------------------------

if($_POST['1gig2'] == "-1 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded-1073741824 WHERE class $class");

}

//-----------------------------------------------
// - 2GB
//-----------------------------------------------

if($_POST['2gig2'] == "-2 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded-2147483648 WHERE class $class");

}

//-----------------------------------------------
// - 5GB
//-----------------------------------------------

if($_POST['5gig2'] == "-5 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded-5368709120 WHERE class $class");

}

//-----------------------------------------------
// - 10GB
//-----------------------------------------------

if($_POST['10gig2'] == "-10 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded-10737418240 WHERE class $class");

}

//-----------------------------------------------
// - 15GB
//-----------------------------------------------

if($_POST['15gig2'] == "-15 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded-16106127360 WHERE class $class");

}

//-----------------------------------------------
// - 25GB
//-----------------------------------------------

if($_POST['25gig2'] == "-25 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded-26843545600 WHERE class $class");

}


//-----------------------------------------------
// - 50GB
//-----------------------------------------------

if($_POST['50gig2'] == "-50 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded-53687091200 WHERE class $class");

}


//-----------------------------------------------
// - 75GB
//-----------------------------------------------

if($_POST['75gig2'] == "-75 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded-80530636800 WHERE class $class");

}

//-----------------------------------------------
// - 100GB
//-----------------------------------------------

if($_POST['100gig2'] == "-100 GB"){
$res = mysql_query("UPDATE users SET uploaded = uploaded-107374182400 WHERE class $class");

}


//-----------------------------------------------
// REPLACE 10GB
//-----------------------------------------------

if($_POST['r10gig'] == "10 GB"){
$res = mysql_query("UPDATE users SET uploaded = 10737418240 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have REPLACED your old upload with 10GB!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// REPLACE 15GB
//-----------------------------------------------

if($_POST['r15gig'] == "15 GB"){
$res = mysql_query("UPDATE users SET uploaded = 16106127360 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have REPLACED your old upload with 15GB!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// REPLACE 25GB
//-----------------------------------------------

if($_POST['r25gig'] == "25 GB"){
$res = mysql_query("UPDATE users SET uploaded = 26843545600 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have REPLACED your old upload with 25GB!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}


//-----------------------------------------------
// REPLACE 50GB
//-----------------------------------------------

if($_POST['r50gig'] == "50 GB"){
$res = mysql_query("UPDATE users SET uploaded = 53687091200 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have REPLACED your old upload with 50GB!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// REPLACE 75GB
//-----------------------------------------------

if($_POST['r75gig'] == "75 GB"){
$res = mysql_query("UPDATE users SET uploaded = 80530636800 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have REPLACED your old upload with 75GB!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// REPLACE 100GB
//-----------------------------------------------

if($_POST['r100gig'] == "100 GB"){
$res = mysql_query("UPDATE users SET uploaded = 107374182400 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have REPLACED your old upload with 100GB!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// REPLACE 200GB
//-----------------------------------------------

if($_POST['r200gig'] == "200 GB"){
$res = mysql_query("UPDATE users SET uploaded = 214748364800 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have REPLACED your old upload with 200GB!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// MULTIPLY X 2
//-----------------------------------------------

if($_POST['x2'] == "X 2"){
$res = mysql_query("UPDATE users SET uploaded = uploaded*2 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have MULTIPLIED your upload by 2!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}

//-----------------------------------------------
// MULTIPLY X 4
//-----------------------------------------------

if($_POST['x4'] == "X 4"){
$res = mysql_query("UPDATE users SET uploaded = uploaded*4 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have MULTIPLIED your upload by 4!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}


//-----------------------------------------------
// MULTIPLY X 5
//-----------------------------------------------

if($_POST['x5'] == "X 5"){
$res = mysql_query("UPDATE users SET uploaded = uploaded*5 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have MULTIPLIED your upload by 5!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}


//-----------------------------------------------
// MULTIPLY X 6
//-----------------------------------------------

if($_POST['x6'] == "X 6"){
$res = mysql_query("UPDATE users SET uploaded = uploaded*6 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have MULTIPLIED your upload by 6!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}


//-----------------------------------------------
// MULTIPLY X 8
//-----------------------------------------------

if($_POST['x8'] == "X 8"){
$res = mysql_query("UPDATE users SET uploaded = uploaded*8 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have MULTIPLIED your upload by 8!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}


//-----------------------------------------------
// MULTIPLY X 10
//-----------------------------------------------

if($_POST['x10'] == "X 10"){
$res = mysql_query("UPDATE users SET uploaded = uploaded*10 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have MULTIPLIED your upload by 10!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}


//-----------------------------------------------
// MULTIPLY X 15
//-----------------------------------------------

if($_POST['x15'] == "X 15"){
$res = mysql_query("UPDATE users SET uploaded = uploaded*15 WHERE class $class");
if ($num == $_POST["numclasses"]){
$res = mysql_query("SELECT id FROM users WHERE class $class");
$msg = $_POST['message'] . "Hey,

Just you let you know...we have MULTIPLIED your upload by 15!

Please do not think this is a time not too seed! The same rules as always apply - We hate leachers who have no intention of seeding!

So, if your NOT a leacher, download a nice game !

$SITENAME Staff";
}else{
$res = mysql_query("SELECT id FROM users where id = 1".$querystring) or sqlerr(__FILE__, __LINE__);
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}



}


//-----------------------------------------------
// DIVIDE by 2
//-----------------------------------------------

if($_POST['d2'] == "/ 2"){
$res = mysql_query("UPDATE users SET uploaded = uploaded/2 WHERE class $class");

}

//-----------------------------------------------
// DIVIDE by 4
//-----------------------------------------------

if($_POST['d4'] == "/ 4"){
$res = mysql_query("UPDATE users SET uploaded = uploaded/4 WHERE class $class");

}

//-----------------------------------------------
// DIVIDE by 5
//-----------------------------------------------

if($_POST['d5'] == "/ 5"){
$res = mysql_query("UPDATE users SET uploaded = uploaded/5 WHERE class $class");

}

//-----------------------------------------------
// DIVIDE by 6
//-----------------------------------------------

if($_POST['d6'] == "/ 6"){
$res = mysql_query("UPDATE users SET uploaded = uploaded/6 WHERE class $class");

}

//-----------------------------------------------
// DIVIDE by 8
//-----------------------------------------------

if($_POST['d8'] == "/ 8"){
$res = mysql_query("UPDATE users SET uploaded = uploaded/8 WHERE class $class");

}

//-----------------------------------------------
// DIVIDE by 10
//-----------------------------------------------

if($_POST['d10'] == "/ 10"){
$res = mysql_query("UPDATE users SET uploaded = uploaded/10 WHERE class $class");

}

//-----------------------------------------------
// DIVIDE by 15
//-----------------------------------------------

if($_POST['d15'] == "/ 15"){
$res = mysql_query("UPDATE users SET uploaded = uploaded/15 WHERE class $class");

}

//-----------------------------------------------
// END
//-----------------------------------------------

?>



<META HTTP-EQUIV="Refresh"
CONTENT="1; URL=upload-bonus.php">
<style type="text/css">
<!--
.style1 {
font-size: 16px;
font-weight: bold;
}
-->
</style>
<br>
<span class="style1">Done! ....Redirecting!</span>