<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");

dbconn();
if (get_user_class() < UC_USER)
stderr("Error", "Access denied.");
$res = mysql_query("SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
if ($arr[0] >= $invites)
stderr("Error", "Sorry, user limit reached. Please try again later.");
if($CURUSER["invites"] == 0)
stderr("Sorry","No invites!");
$mess= unesc($_POST["mess"]);
if (!$mess)
bark("You must enter a message!");
if (!mkglobal("email"))
die();
function bark($text) 
{ 
print("<title>Error!</title>"); 
print("<table width='100%' height='100%' style='border: 8px ridge #000000'><tr><td align='center'>"); 
print("<center><h1 style='color: #CC3300;'>Error:</h1><h2>" . htmlspecialchars($text) . "</h2></center>"); 
print("<center><INPUT TYPE='button' VALUE='Back' onClick=\"history.go(-1)\"></center>"); 
print("</td></tr></table>"); 
die; 
} 
if (!validemail($email))
bark("That doesn't look like a valid email address.");
// check if email addy is already in use
$a = (@mysql_fetch_row(@mysql_query("select count(*) from users where email='$email'"))) or die(mysql_error());
if ($a[0] != 0)
bark("The e-mail address " . htmlspecialchars($email) . " is already in use.");
$secret = mksecret();
$editsecret = mksecret();
$username = rand();
$ret = mysql_query("INSERT INTO users (username, secret, editsecret, email, status, invited_by, added) VALUES (" .
implode(",", array_map("sqlesc", array($username, $secret, $editsecret, $email, 'pending', $CURUSER["id"]))) .
",'" . get_date_time() . "')");
if (!$ret) {
if (mysql_errno() == 1062)
bark("Username already exists!");
bark("borked");
}
$id = mysql_insert_id();
$id2 = $CURUSER["id"];
$invites = $CURUSER["invites"]-1;
$invitees = $CURUSER["invitees"];
$invitees2 = "$id $invitees";
$ret2 = mysql_query("UPDATE users SET invites='$invites', invitees='$invitees2' WHERE id = $id2");
$username=$CURUSER["username"];
$psecret = md5($editsecret);
$message = ($html ? strip_tags($mess) : $mess);
$body = <<<EOD
You have been invited to $SITENAME by $username. They have
specified this address ($email) as your email. If you do not know this person, please ignore this email. Please do not reply.

Message:
-------------------------------------------------------------------------------
$message
-------------------------------------------------------------------------------

This is a private site and you must agree to the rules before you can enter:

$DEFAULTBASEURL/useragreement.php

$DEFAULTBASEURL/rules.php

$DEFAULTBASEURL/faq.php


To confirm your invitation, you have to follow this link:

$DEFAULTBASEURL/confirminvite.php?id=$id&secret=$psecret

After you do this, you will be able to use your new account. If you fail to
do this, your account will be deleted within a few days. We urge you to read
the RULES and FAQ before you start using $SITENAME.
EOD;
mail($email, "$SITENAME user registration confirmation", $body, "From: $SITEEMAIL", "-f$SITEEMAIL");

header("Refresh: 0; url=ok.php?type=invite&email=" . urlencode($email));
?>