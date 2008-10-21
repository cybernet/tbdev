<?php
/////////////////updated modtask by Retro//////////////////
require "include/bittorrent.php";
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

function puke($text = "w00t")
{
stderr("w00t", $text);
}

if ($CURUSER['class'] < UC_MODERATOR) die();

// Correct call to script
if ((isset($_POST['action'])) && ($_POST['action'] == "edituser"))
{
// Set user id
if (isset($_POST['userid'])) $userid = $_POST['userid'];
else die();

// and verify...
if (!is_valid_id($userid)) stderr("Error", "Bad user ID.");

// Fetch current user data...
$res = sql_query("SELECT * FROM users WHERE id=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_assoc($res) or sqlerr(__FILE__, __LINE__);

$updateset = array();

if ((isset($_POST['modcomment'])) && ($modcomment = $_POST['modcomment'])) ;
else $modcomment = "";

// Set class

if ((isset($_POST['class'])) && (($class = $_POST['class']) != $user['class']))
{
if (($CURUSER['class'] < UC_SYSOP) && ($user['class'] >= $CURUSER['class'])) die();

// Notify user
$what = ($class > $user['class'] ? "promoted" : "demoted");
$msg = sqlesc("You have been $what to '" . get_user_class_name($class) . "' by ".$CURUSER['username']);
$added = sqlesc(get_date_time());
sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES(0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);

$updateset[] = "class = ".sqlesc($class);

$modcomment = gmdate("Y-m-d") . " - $what to '" . get_user_class_name($class) . "' by $CURUSER[username].\n". $modcomment;
}

///////////////////Modified auto-leech warn sys/////////////////
$warned = $_POST["warned"];
$warnlength = 0 + $_POST["warnlength"];
$warnpm = $_POST["warnpm"];

if (isset($_POST['warned']) && (($warned = $_POST['warned']) != $user['warned']))
{
$updateset[] = "warned = " . sqlesc($warned);
$updateset[] = "warneduntil = '0000-00-00 00:00:00'";

if ($warned == 'no')
{
$modcomment = gmdate("Y-m-d") . " - Warning removed by " . $CURUSER['username'] . ".\n". $modcomment;
$msg = sqlesc("Your warning have been removed by" . $CURUSER['username'] . ".");
}

$added = sqlesc(get_date_time());
sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
elseif ($warnlength)
{

if ($warnlength == 255)
{
$modcomment = gmdate("Y-m-d") . " - Warned by " . $CURUSER['username'] . ".\nReason: $warnpm\n" . $modcomment;
$msg = sqlesc("You have been warned by $CURUSER[username]." . ($warnpm ? "\n\nReason: $warnpm" : ""));
$updateset[] = "warneduntil = '0000-00-00 00:00:00'";
}else{
$warneduntil = get_date_time(gmtime() + $warnlength * 604800);
$dur = $warnlength . " week" . ($warnlength > 1 ? "s" : "");
$msg = sqlesc("You have beenwarned for $dur by " . $CURUSER['username'] . "." . ($warnpm ? "\n\nReason: $warnpm" : ""));
$modcomment = gmdate("Y-m-d") . " - Warned for $dur by " . $CURUSER['username'] . ".\nReason: $warnpm\n" . $modcomment;
$updateset[] = "warneduntil = '$warneduntil'";
}

$added = sqlesc(get_date_time());
sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
$updateset[] = "warned = 'yes', timeswarned = timeswarned+1, lastwarned=$added, warnedby=$CURUSER[id]";
}
//////////////end leechwarn section
/////////////////disable download with duration///////////////////////////////
$downloadpos = $_POST["downloadpos"];
$disablelength = 0 + $_POST["disablelength"];
$disablepm = $_POST["disablepm"];
if (isset($_POST['downloadpos']) && (($downloadpos = $_POST['downloadpos']) != $user['downloadpos']))
{
$updateset[] = "downloadpos = " . sqlesc($downloadpos);
$updateset[] = "disableuntil = '0000-00-00 00:00:00'";
if ($downloadpos == 'yes')
{
$modcomment = gmdate("Y-m-d") . " - Download rights enabled by" . $CURUSER['username'] . ".\n". $modcomment;
$msg = sqlesc("Your download rights have been enabled by " . $CURUSER['username'] . ".");
}
$added = sqlesc(get_date_time());
$subject = sqlesc("Download rights.");
sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES (0, $userid, $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
}
elseif ($downloadpos && ($disablelength))
{
if ($downloadpos == no && ($disablelength == 255))
{
$modcomment = gmdate("Y-m-d") . " - Disabled by  " . $CURUSER['username'] . ".\nReason: $disablepm\n" . $modcomment;
$msg = sqlesc("Your downloads have been disabled by $CURUSER[username]." . ($disablepm ? "\n\nReason: $disablepm" : ""));
$updateset[] = "disableuntil = '0000-00-00 00:00:00'";
}else{
$disableuntil = get_date_time(gmtime() + $disablelength * 604800);
$dur = $disablelength . " week" . ($disablelength > 1 ? "s" : "");
$msg = sqlesc("Your downloads have been disabled for $dur by " . $CURUSER['username'] . "." . ($disablepm ? "\n\nReason: $disablepm" : ""));
$modcomment = gmdate("Y-m-d") . " - Downloads disabled for $dur by " . $CURUSER['username'] . ".\nReason: $disablepm\n" . $modcomment;
$updateset[] = "disableuntil = '$disableuntil'";
}
$added = sqlesc(get_date_time());
$subject = sqlesc("Download rights.");
sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES (0, $userid, $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
$updateset[] = "downloadpos = 'no'";
}
///////////////////End download disablement with duration///////////////
//=== add donated amount to user and to funds table
if ((isset($_POST['donated'])) && (($donated = $_POST['donated']) != $user['donated']))
{
$added = sqlesc(get_date_time());
sql_query("INSERT INTO funds (cash, user, added) VALUES ($donated, $userid, $added)") or sqlerr(__FILE__, __LINE__);
$updateset[] = "donated = " . sqlesc($donated);
$updateset[] = "total_donated = $user[total_donated] + " . sqlesc($donated);
}
//====end

//=== Set donor - Time based
if ((isset($_POST['donorlength'])) && ($donorlength = 0 + $_POST['donorlength']))
{
if ($donorlength == 255)
{
$modcomment = gmdate("Y-m-d") . " - Donor status set by " . $CURUSER['username'] . ".\n" . $modcomment;
$msg = sqlesc("You have received donor status from ".$CURUSER['username']);
$subject = sqlesc("Thank You for Your Donation!");
$updateset[] = "donoruntil = '0000-00-00 00:00:00'";
}
else
{
$donoruntil = get_date_time(gmtime() + $donorlength * 604800);
$dur = $donorlength . " week" . ($donorlength > 1 ? "s" : "");
$msg = sqlesc("Dear " . $user['username'] ."
:wave:
Thanks for your support to $SITENAME!
Your donation helps us in the costs of running the site!
As a donor, you are given some bonus gigs added to your uploaded amount, the status of VIP, and the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

so, thanks again, and enjoy!
cheers,
$SITENAME Staff

PS. Your donator status will last for $dur and can be found on your user details page and can only be seen by you smile.gif It was set by " . $CURUSER['username']);

$subject = sqlesc("Thank You for Your Donation!");
$modcomment = gmdate("Y-m-d") . " - Donator status set for $dur by " . $CURUSER['username']."\n".$modcomment;
$updateset[] = "donoruntil = ".sqlesc($donoruntil);
}
$added = sqlesc(get_date_time());
sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
$updateset[] = "donor = 'yes'";
$res = mysql_query("SELECT class FROM users WHERE id = $userid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if ($arr['class'] < UC_UPLOADER)
$updateset[] = "class = '2'"; //=== set this to the number for vip on your server
}

//=== add to donor length // thanks to CoLdFuSiOn & ShadowLeader
if ((isset($_POST['donorlengthadd'])) && ($donorlengthadd = 0 + $_POST['donorlengthadd'])){
$donoruntil = $user["donoruntil"];
$dur = $donorlengthadd . " week" . ($donorlengthadd > 1 ? "s" : "");
$msg = sqlesc("Dear " . $user['username'] ."
:wave:
Thanks for your continued support to $SITENAME!
Your donation helps us in the costs of running the site. Everything above the current running costs will go towards next months costs!
As a donor, you are given some bonus gigs added to your uploaded amount, and, you have the the status of VIP, and the warm fuzzy feeling you get inside for helping to support this site that we all know and love :smile:

so, thanks again, and enjoy!
cheers,
$SITENAME Staff

PS. Your donator status will last for an extra $dur on top of your current donation status, and can be found on your user details page and can only be seen by you. It was set by " . $CURUSER['username']);

$subject = sqlesc("Thank You for Your Donation... Again!");
$modcomment = gmdate("Y-m-d") . " - Donator status set for another $dur by " . $CURUSER['username']."\n".$modcomment;
$donorlengthadd = $donorlengthadd * 7;
sql_query("UPDATE users donoruntil = IF(donoruntil='0000-00-00 00:00:00', ADDDATE(NOW(), INTERVAL $donorlengthadd DAY ), ADDDATE( donoruntil, INTERVAL $donorlengthadd DAY)) WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
$added = sqlesc(get_date_time());
sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
$updateset[] = "donated = $user[donated] + " . sqlesc($_POST['donated']);
$updateset[] = "total_donated = $user[total_donated] + " . sqlesc($_POST['donated']);
}
//=== end add to donor length

//=== Clear donor if they were bad
if (isset($_POST['donor']) && (($donor = $_POST['donor']) != $user['donor']))
{
$updateset[] = "donor = " . sqlesc($donor);
$updateset[] = "donoruntil = '0000-00-00 00:00:00'";
$updateset[] = "donated = '0'";
if ($arr['class'] < UC_UPLOADER)
$updateset[] = "class = '1'";

if ($donor == 'no')
{
$modcomment = gmdate("Y-m-d") . " - Donor status removed by ".$CURUSER['username'].".\n". $modcomment;
$msg = sqlesc("Your donator status has expired.");
$added = sqlesc(get_date_time());
$subject = sqlesc("Donator status expired.");
sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES (0, $subject, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
}
//===end

// Enable / Disable
if ((isset($_POST['enabled'])) && (($enabled = $_POST['enabled']) != $user['enabled']))
{
if ($enabled == 'yes')
$modcomment = gmdate("Y-m-d") . " - Enabled by " . $CURUSER['username'] . ".\n" . $modcomment;
else
$modcomment = gmdate("Y-m-d") . " - Disabled by " . $CURUSER['username'] . ".\n" . $modcomment;

$updateset[] = "enabled = " . sqlesc($enabled);
}

// Change Uploaded Amount
    if (((isset($_POST['uploaded'])) && (isset($_POST['uploadbase'])) && (($uploaded = $_POST['uploaded']) != ($uploadbase = $_POST['uploadbase']))))
        {
                if ($uploaded < $uploadbase)
                        $modcomment = gmdate("Y-m-d") . " - Upload reduced to ".$uploaded." from ".$uploadbase." by " . $CURUSER['username'] . ".\n" . $modcomment;
                else
                        $modcomment = gmdate("Y-m-d") . " - Upload increased to ".$uploaded." from ".$uploadbase." by " . $CURUSER['username'] . ".\n" . $modcomment;

        $updateset[] = "uploaded = " . sqlesc($uploaded);
    }

    // Change Downloaded Amount
    if (((isset($_POST['downloaded'])) && (isset($_POST['downloadbase'])) && (($downloaded = $_POST['downloaded']) != ($downloadbase = $_POST['downloadbase']))))
        {
                if ($downloaded < $downloadbase)
                        $modcomment = gmdate("Y-m-d") . " - Download reduced to ".$downloaded." from ".$downloadbase." by " . $CURUSER['username'] . ".\n" . $modcomment;
                else
                        $modcomment = gmdate("Y-m-d") . " - Download increased to ".$downloaded." from ".$downloadbase." by " . $CURUSER['username'] . ".\n" . $modcomment;

        $updateset[] = "downloaded = " . sqlesc($downloaded);
    }

//=== Enable / Disable chat box rights
if ((isset($_POST['chatpost'])) && (($chatpost = $_POST['chatpost']) != $user['chatpost'])){
$modcomment = gmdate("Y-m-d") . " - Chat post rights set to ".sqlesc($chatpost)." by " . $CURUSER['username'] . ".\n" . $modcomment;
$updateset[] = "chatpost = " . sqlesc($chatpost);
}

// Forum Post Enable / Disable
if ((isset($_POST['forumpost'])) && (($forumpost = $_POST['forumpost']) != $user['forumpost']))
{
if ($forumpost == 'yes')
{
$modcomment = gmdate("Y-m-d")." - Posting enabled by ".$CURUSER['username'].".\n" . $modcomment;
$msg = sqlesc("Your Posting rights have been given back by ".$CURUSER['username'].". You can post to forum again.");
$added = sqlesc(get_date_time());
sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
else
{
$modcomment = gmdate("Y-m-d")." - Posting disabled by ".$CURUSER['username'].".\n" . $modcomment;
$msg = sqlesc("Your Posting rights have been removed by ".$CURUSER['username'].", Please PM ".$CURUSER['username']." for the reason why.");
$added = sqlesc(get_date_time());
sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
$updateset[] = "forumpost = " . sqlesc($forumpost);
} 
/////////////Casino ban 
if ((isset($_POST['casinoban'])) && (($casinoban = $_POST['casinoban']) != $user['casinoban']))
{
if ($casinoban == 'no')
{
$modcomment = gmdate("Y-m-d")." - Casino ban removed by ".$CURUSER['username'].".\n" . $modcomment;
$msg = sqlesc("Your Casino rights have been given back by ".$CURUSER['username'].". You can use the casino again.");
$added = sqlesc(get_date_time());
$subject = sqlesc("Casino Ban.");
//mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
mysql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES (0, $userid, $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
}
else
{
$modcomment = gmdate("Y-m-d")." - You have been banned from Casino use by ".$CURUSER['username'].".\n" . $modcomment;
$msg = sqlesc("Your Casino rights have been removed by ".$CURUSER['username'].", Please PM ".$CURUSER['username']." for the reason why.");
$added = sqlesc(get_date_time());
$subject = sqlesc("Casino Ban.");
//mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
mysql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES (0, $userid, $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
}
$updateset[] = "casinoban = " . sqlesc($casinoban);
} 
/////////////Blackjack ban 
if ((isset($_POST['blackjackban'])) && (($blackjackban = $_POST['blackjackban']) != $user['blackjackban']))
{
if ($blackjackban == 'no')
{
$modcomment = gmdate("Y-m-d")." - Blackjack ban removed by ".$CURUSER['username'].".\n" . $modcomment;
$msg = sqlesc("Your Blackjack rights have been given back by ".$CURUSER['username'].". You can use Blackjack again.");
$added = sqlesc(get_date_time());
$subject = sqlesc("BlackJack Ban.");
//mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
mysql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES (0, $userid, $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
}
else
{
$modcomment = gmdate("Y-m-d")." - You have been banned from Blackjack use by ".$CURUSER['username'].".\n" . $modcomment;
$msg = sqlesc("Your Blackjack rights have been removed by ".$CURUSER['username'].", Please PM ".$CURUSER['username']." for the reason why.");
$added = sqlesc(get_date_time());
$subject = sqlesc("BlackJack Ban.");
//mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
mysql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES (0, $userid, $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
}
$updateset[] = "blackjackban = " . sqlesc($blackjackban);
} 

// Change Custom Title
if ((isset($_POST['title'])) && (($title = $_POST['title']) != ($curtitle = $user['title'])))
{
$modcomment = gmdate("Y-m-d") . " - Custom Title changed to '".$title."' from '".$curtitle."' by " . $CURUSER['username'] . ".\n" . $modcomment;

$updateset[] = "title = " . sqlesc($title);
}

// The following code will place the old passkey in the mod comment and create
// a new passkey. This is good practice as it allows usersearch to find old
// passkeys by searching the mod comments of members.

$res = sql_query("SELECT warned, enabled, username, class, passhash, passkey FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

// Reset Passkey
    if ((isset($_POST['resetkey'])) && ($_POST['resetkey']))
    {
        $newpasskey = md5($arr['username'].get_date_time().$arr['passhash']);
        $modcomment = gmdate("Y-m-d") . " - Passkey ".$arr['passkey']." Reset to ".$newpasskey." by " . $CURUSER['username'] . ".\n" . $modcomment;
        $updateset[] = "passkey=".sqlesc($newpasskey);
    }

//=== karma bonus
if ((isset($_POST['seedbonus'])) && (($seedbonus = $_POST['seedbonus']) != $user['seedbonus']))
{
$modcomment = gmdate("Y-m-d") . " - seeding bonus set to $seedbonus  by " . $CURUSER['username'] . ".\n" . $modcomment;
$updateset[] = "seedbonus = " . sqlesc($seedbonus);
}

// Add Comment to ModComment
if ((isset($_POST['addcomment'])) && ($addcomment = trim($_POST['addcomment'])))
{
$modcomment = gmdate("Y-m-d") . " - ".$addcomment." - " . $CURUSER['username'] . ".\n" . $modcomment;
} 

// change freeslots
if ((isset($_POST['freeslots'])) && (($freeslots = $_POST['freeslots']) != ($curfreeslots = $user['freeslots'])))
{
$modcomment = gmdate("Y-m-d") . " - freeslots amount changed to '".$freeslots."' from '".$curfreeslots."' by " . $CURUSER['username'] . ".\n" . $modcomment;
}
$updateset[] = "freeslots = " . sqlesc($freeslots);

// Set Upload Enable / Disable
if ((isset($_POST['uploadpos'])) && (($uploadpos = $_POST['uploadpos']) != $user['uploadpos']))
{
if ($uploadpos == 'yes')
{
$modcomment = gmdate("Y-m-d") . " - Upload enabled by " . $CURUSER['username'] . ".\n" . $modcomment;
$msg = sqlesc("You have been given upload rights by " . $CURUSER['username'] . ". You can now upload torrents.");
$added = sqlesc(get_date_time());
sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
elseif ($uploadpos == 'no')
{
$modcomment = gmdate("Y-m-d") . " - Upload disabled by " . $CURUSER['username'] . ".\n" . $modcomment;
$msg = sqlesc("Your upload rights have been removed by " . $CURUSER['username'] . ". Please PM ".$CURUSER['username']." for the reason why.");
$added = sqlesc(get_date_time());
sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
else
die(); // Error

$updateset[] = "uploadpos = " . sqlesc($uploadpos);
} 

// Avatar Changed
if ((isset($_POST['avatar'])) && (($avatar = $_POST['avatar']) != ($curavatar = $user['avatar'])))
{
$modcomment = gmdate("Y-m-d") . " - Avatar changed from ".htmlspecialchars($curavatar)." to ".htmlspecialchars($avatar)." by " . $CURUSER['username'] . ".\n" . $modcomment;

$updateset[] = "avatar = ".sqlesc($avatar);
}

// Signature Changed
if ((isset($_POST['signature'])) && (($signature = $_POST['signature']) != ($cursignature = $user['signature'])))
{
$modcomment = gmdate("Y-m-d") . " - Signature changed from ".htmlspecialchars($cursignature)." to ".htmlspecialchars($signature)." by " . $CURUSER['username'] . ".\n" . $modcomment;

$updateset[] = "signature = ".sqlesc($signature);
}

// Set Parking Enable / Disable
if ((isset($_POST['parked'])) && (($parked = $_POST['parked']) != $user['parked']))
{
if ($parked == 'yes')
{
$modcomment = gmdate("Y-m-d") . " - Parked  by " . $CURUSER['username'] . ".\n" . $modcomment;
    $msg = sqlesc("Your account has set to be in parked mode by " . $CURUSER['username'] . ". You can remove this in your profile when you are ready to use your account again.");
$added = sqlesc(get_date_time());
mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
elseif ($parked == 'no')
{
$modcomment = gmdate("Y-m-d") . " - Parking removed by " . $CURUSER['username'] . ".\n" . $modcomment;
    $msg = sqlesc("Your account has been removed from parked status by " . $CURUSER['username'] . ", propably because you requested it or did it on accident.");
$added = sqlesc(get_date_time());
mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
else
die(); // Error

$updateset[] = "parked = " . sqlesc($parked);
}

// remove users anonymous status 
if ((isset($_POST['anonymous'])) && (($anonymous = $_POST['anonymous']) != $user['anonymous']))
{
if ($anonymous == 'yes')
{
$modcomment = gmdate("Y-m-d") . " - Anonymous status set by " . $CURUSER['username'] . ".\n" . $modcomment;
    $msg = sqlesc("Your account has been set to anonymous by " . $CURUSER['username'] . ". You can remove this in your profile when you want.");
$added = sqlesc(get_date_time());
mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
elseif ($anonymous == 'no')
{
$modcomment = gmdate("Y-m-d") . " - Anonymous removed by " . $CURUSER['username'] . ".\n" . $modcomment;
    $msg = sqlesc("Your account has been removed from Anonymous status by " . $CURUSER['username'] . ", Your rights will be given back once you contact staff.");
$added = sqlesc(get_date_time());
mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);
}
else
die(); // Error

$updateset[] = "anonymous = " . sqlesc($anonymous);
}


//=== allow invites
if ((isset($_POST['invite_on'])) && (($invite_on = $_POST['invite_on']) != $user['invite_on'])){  
$modcomment = gmdate("Y-m-d") . " - Invites allowed changed from $user[invite_on] to $invite_on by " . $CURUSER['username'] . ".\n" . $modcomment;
$updateset[] = "invite_on = " . sqlesc($invite_on);
}

//=== webseeeder
if ((isset($_POST['webseeder'])) && (($webseeder = $_POST['webseeder']) != $user['webseeder'])){  
$modcomment = gmdate("Y-m-d") . " - User is a seedbox user - set by " . $CURUSER['username'] . ".\n" . $modcomment;
$updateset[] = "webseeder = " . sqlesc($webseeder);
}


// change invites
if ((isset($_POST['invites'])) && (($invites = $_POST['invites']) != ($curinvites = $user['invites'])))
{
$modcomment = gmdate("Y-m-d") . " - invite amount changed to '".$invites."' from '".$curinvites."' by " . $CURUSER['username'] . ".\n" . $modcomment;

$updateset[] = "invites = " . sqlesc($invites);
}

// Support
if ((isset($_POST['support'])) && (($support = $_POST['support']) != $user['support']))
{
if ($support == 'yes')
{
$modcomment = gmdate("Y-m-d") . " - Promoted to FLS by " . $CURUSER['username'] . ".\n" . $modcomment;
}
elseif ($support == 'no')
{
$modcomment = gmdate("Y-m-d") . " - Demoted from FLS by " . $CURUSER['username'] . ".\n" . $modcomment;
}
else
die();

$supportfor = $_POST['supportfor'];

$updateset[] = "support = " . sqlesc($support);
$updateset[] = "supportfor = ".sqlesc($supportfor);
} 

// Add ModComment to the update set...
$updateset[] = "modcomment = " . sqlesc($modcomment);
sql_query("UPDATE users SET " . implode(", ", $updateset) . " WHERE id=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
status_change($userid);
$returnto = $_POST["returnto"];
header("Location: $DEFAULTBASEURL/$returnto");

die();
}

puke();

?>