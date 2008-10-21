<?php
/******************************************
* Updated takeeditusercp.php By Bigjoos
* Credits: Djlee's code from takeprofileedit.php - Retro for the original idea
*********************************************************************************/
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
function bark($msg) {
genbark($msg, "Update failed!");
}
dbconn();
loggedinorreturn();
$action = isset($_GET["action"]) ?$_GET["action"] : '';
$updateset = array();
if ($action == "avatar")
{
/////////////avatar check
if(($avatars = ($_POST["avatars"] != "" ? "yes" : "no")) != $CURUSER["avatars"])
$updateset[] = "avatars = '$avatars'";
if(isset($_POST["avatar"]) && (($avatar = $_POST["avatar"]) != $CURUSER["avatar"])) {
if (!preg_match("/^http:\/\/[^\s'\"<>]+\.(jpg|gif|png)$/i", $avatar))
bark("Avatar MUST be in jpg, gif or png format. Make sure you include http:// in the URL.");
$updateset[] = "avatar = " . sqlesc($avatar);
}
////////custom-title check/////////////////
if(isset($_POST["title"]) && $CURUSER["class"] >= UC_VIP && ($title = $_POST["title"]) != $CURUSER["title"]) {
$ctnotallow = array("sysop","administrator","admin","mod","moderator","vip","motherfucker");
if(in_array(strtolower($title),($ctnotallow)))
bark("Error, Invalid custom title!");
$updateset[] = "title = ".sqlesc($title);
}
$action = "avatar";
}
else if ($action == "signature")
{
//---- Signature check
if(($signatures = ($_POST["signatures"] != "" ? "yes" : "no")) != $CURUSER["signatures"])
$updateset[] = "signatures = '$signatures'";
if(isset($_POST["signature"]) && ($signature = $_POST["signature"]) != $CURUSER["signature"])
$updateset[] = "signature = " . sqlesc($signature);
//---- end sig check
//////user-info check////////////
if(isset($_POST["info"]) && (($info = $_POST["info"]) != $CURUSER["info"]))
$updateset[] = "info = " . sqlesc($info);
$action = "signature";
}
else if ($action == "security")
{
////////password////////
if (!mkglobal("email:chpassword:passagain"))
bark("missing form data");
if ($chpassword != "") {
if (strlen($chpassword) > 40)
bark("Sorry, password is too long (max is 40 chars)");
if ($chpassword != $passagain)
bark("The passwords didn't match. Try again.");
$sec = mksecret();
$passhash = md5($sec . $chpassword . $sec);
$updateset[] = "secret = " . sqlesc($sec);
$updateset[] = "passhash = " . sqlesc($passhash);
logincookie($CURUSER["id"], $passhash);
}
///////////email///////////
if ($email != $CURUSER["email"]) {
if (!validemail($email))
bark("That doesn't look like a valid email address.");
$r = mysql_query("SELECT id FROM users WHERE email=" . sqlesc($email)) or sqlerr();
if (mysql_num_rows($r) > 0)
bark("The e-mail address " . htmlspecialchars($email) . " is already in use.");
$changedemail = 1;
}
$urladd = "";
////////////email changed?////////
if ($changedemail) {
$sec = mksecret();
$hash = md5($sec . $email . $sec);
$obemail = urlencode($email);
$updateset[] = "editsecret = " . sqlesc($sec);
$thishost = $_SERVER["HTTP_HOST"];
$thisdomain = preg_replace('/^www\./is', "", $thishost);
$body = <<<EOD
You have requested that your user profile (username {$CURUSER["username"]})
on $thisdomain should be updated with this email address ($email) as
user contact.
If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.
To complete the update of your user profile, please follow this link:
http://$thishost/confirmemail.php/{$CURUSER["id"]}/$hash/$obemail
Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;
mail($email, "$thisdomain profile change confirmation", $body, "From: $SITEEMAIL", "-f$SITEEMAIL");
$urladd .= "&mailsent=1";
}
////passkey///
if ($_POST['resetpasskey'] == 1)
{
$res = mysql_query("SELECT warned, enabled, username, class, passhash, passkey FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res) or puke();
$newpasskey = md5($arr['username'].get_date_time().$arr['passhash']);
$modcomment = gmdate("Y-m-d") . " - Passkey ".$arr['passkey']." Reset to ".$newpasskey." by " . $CURUSER['username'] . ".\n" . $modcomment;
$updateset[] = "passkey=".sqlesc($newpasskey);
}
/////parked/////
if(isset($_POST["parked"]) && ($parked = $_POST["parked"]) != $CURUSER["parked"])
$updateset[] = "parked = " . sqlesc($parked);
////////////anonymous user/////
if(($anonymous = ($_POST["anonymous"] != "" ? "yes" : "no")) != $CURUSER["anonymous"])
$updateset[] = "anonymous = '$anonymous'";
$anonymoustopten = ($_POST["anonymoustopten"] != "" ? "yes" : "no");
$updateset[] = "anonymoustopten = " . sqlesc($anonymoustopten);
$action = "security";
}
else if ($action == "torrents")
{
//////Get default cats- notifs//////
$r = mysql_query("SELECT id FROM categories") or sqlerr();
while ($a = mysql_fetch_assoc($r))
$catnotifs = $catnotifs.($_POST["cat$a[id]"] == 'yes' ? "[cat$a[id]]" : "");
if(($notifs = ($_POST["pmnotif"] == 'yes' ? "[pm]" : "").($catnotifs).($_POST['emailnotif'] == 'yes' ? "[email]" : "")) != $CURUSER['notifs'])
$updateset[] = "notifs = '$notifs'";
/////imagecats//////
$imagecats= (isset($_POST['imagecats']) && $_POST["imagecats"] != "" ? "yes" : "no");
$updateset[] = "imagecats= '$imagecats'";
//////////highlight torrent status on browse////
$ttablehl = ($_POST["ttablehl"] == "yes" ? "yes" : "no");
$updateset[] = "ttablehl = " . sqlesc($ttablehl);
///////split torrents by day///////////
$split = ($_POST["split"] == "yes" ? "yes" : "no");
$updateset[] = "split = " . sqlesc($split);
///////show torrents on homepage///////////
$tohp = ($_POST["tohp"] == "yes" ? "yes" : "no");
$updateset[] = "tohp = " . sqlesc($tohp);
//////////////User class colour on browse///
$view_uclass= (isset($_POST['view_uclass']) && $_POST["view_uclass"] != "" ? "yes" : "no");
$updateset[] = "view_uclass= '$view_uclass'";
$action = "torrents";
}
else if ($action == "personal")
{
if(isset($_POST["stylesheet"]) && (($stylesheet = $_POST["stylesheet"]) != $CURUSER["stylesheet"]) && is_valid_id($stylesheet))
$updateset[] = "stylesheet = '$stylesheet'";
if(isset($_POST["country"]) && (($country = $_POST["country"]) != $CURUSER["country"]) && is_valid_id($country))
$updateset[] = "country = $country";
if(isset($_POST["torrentsperpage"]) && (($torrentspp = min(100, 0 + $_POST["torrentsperpage"])) != $CURUSER["torrentsperpage"]))
$updateset[] = "torrentsperpage = $torrentspp";
if(isset($_POST["topicsperpage"]) && (($topicspp = min(100, 0 + $_POST["topicsperpage"])) != $CURUSER["topicsperpage"]))
$updateset[] = "topicsperpage = $topicspp";
if(isset($_POST["postsperpage"]) && (($postspp = min(100, 0 + $_POST["postsperpage"])) != $CURUSER["postsperpage"]))
$updateset[] = "postsperpage = $postspp";
if(isset($_POST["gender"]) && ($gender = $_POST["gender"]) != $CURUSER["gender"])
$updateset[] = "gender = " . sqlesc($gender);
$shoutboxbg = 0 + $_POST["shoutboxbg"];
$updateset[] = "shoutboxbg = " . sqlesc($shoutboxbg);
///////forum online users as avatar///////////
$forumview= (isset($_POST['forumview']) && $_POST["forumview"] != "" ? "yes" : "no");
$updateset[] = "forumview= '$forumview'";
$action = "personal";
}
else if ($action == "pm")
{
if(isset($_POST["acceptpms"]) && ($acceptpms = $_POST["acceptpms"]) != $CURUSER["acceptpms"])
$updateset[] = "acceptpms = " . sqlesc($acceptpms);
if(($deletepms = ($_POST["deletepms"] != "" ? "yes" : "no")) != $CURUSER["deletepms"])
$updateset[] = "deletepms = '$deletepms'";
if(($savepms = ($_POST["savepms"] != "" ? "yes" : "no")) != $CURUSER["savepms"])
$updateset[] = "savepms = '$savepms'";
///////freinds/////
$showfriends = ($_POST["showfriends"] != "" ? "yes" : "no");
$updateset[] = "showfriends = '$showfriends'";
///pm subscribe////
$subscription_pm = $_POST["subscription_pm"];
$updateset[] = "subscription_pm = " . sqlesc($subscription_pm);
////pm subscribe///////////
$action = "";
}
mysql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]) or sqlerr(__FILE__,__LINE__);
header("Location: $BASEURL/usercp.php?edited=1&action=$action" . $urladd);
?>