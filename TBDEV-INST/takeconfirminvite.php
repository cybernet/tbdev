<?php
require_once("include/bittorrent.php");

$id = 0 + $_GET["id"];
$md5 = $_GET["secret"];
if (!$id)
httperr();

dbconn();

$res = mysql_query("SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
if ($arr[0] >= $invites)
stderr("Error", "Sorry, user limit reached. Please try again later.");

$res = mysql_query("SELECT editsecret, status FROM users WHERE id = $id");
$row = mysql_fetch_array($res);

if (!$row)
httperr();

if ($row["status"] != "pending") {
header("Refresh: 0; url=../../ok.php?type=confirmed");
exit();
}

$sec = hash_pad($row["editsecret"]);
if ($md5 != md5($sec))
httperr();

if (!mkglobal("wantusername:wantpassword:passagain"))
die();

function bark($msg) {
stdhead();
stdmsg("Signup failed!", $msg);
stdfoot();
exit;
}

function validusername($username)
{
if ($username == "")
return false;

// The following characters are allowed in user names
$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

for ($i = 0; $i < strlen($username); ++$i)
if (strpos($allowedchars, $username[$i]) === false)
return false;

return true;
}

function isportopen($port)
{
global $HTTP_SERVER_VARS;
$sd = @fsockopen($HTTP_SERVER_VARS["REMOTE_ADDR"], $port, $errno, $errstr, 1);
if ($sd)
{
fclose($sd);
return true;
}
else
return false;
}

if (empty($wantusername) || empty($wantpassword) || empty($gender))
//bark("Don't leave any fields blank.");

if (strlen($wantusername) > 12)
bark("Sorry, username is too long (max is 12 chars)");

if ($wantpassword != $passagain)
bark("The passwords didn't match! Must've typoed. Try again.");

if (strlen($wantpassword) < 6)
bark("Sorry, password is too short (min is 6 chars)");

if (strlen($wantpassword) > 40)
bark("Sorry, password is too long (max is 40 chars)");

if ($wantpassword == $wantusername)
bark("Sorry, password cannot be same as user name.");

if (!validusername($wantusername))
bark("Invalid username.");

// make sure user agrees to everything...
if ($HTTP_POST_VARS["rulesverify"] != "yes" || $HTTP_POST_VARS["faqverify"] != "yes" || $HTTP_POST_VARS["ageverify"] != "yes")
stderr("Signup failed", "Sorry, you're not qualified to become a member of this site.");



$secret = mksecret();
$wantpasshash = md5($secret . $wantpassword . $secret);

$ret = mysql_query("UPDATE users SET username='$wantusername', passhash='$wantpasshash', status='confirmed', editsecret='', secret='$secret' WHERE id=$id");

if (!$ret) {
if (mysql_errno() == 1062)
bark("Username already exists!");
bark("Database Update Failed");

}

logincookie($id, $wantpasshash);

header("Refresh: 0; url=../../ok.php?type=confirm");
?>