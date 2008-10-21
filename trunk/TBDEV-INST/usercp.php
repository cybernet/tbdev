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
//$action = $_GET["action"];
$action = isset($_GET["action"]) ?$_GET["action"] : '';

stdhead ($CURUSER ["username"] . "'s private page", false);

if ($_GET["edited"]) {
print("<h1>Profile updated!</h1>\n");
if ($_GET["mailsent"])
print("<h2>Confirmation email has been sent!</h2>\n");
}
elseif ($_GET["emailch"])
print("<h1>Email address changed!</h1>\n");
else
print("<h1>Welcome, <a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a>!</h1>\n");

print("<table border=1 cellspacing=0 cellpadding=0 align=center><tr>");

print("<td width=502 valign=top>");

print("<table width=502 border=1>");

$maxbox = 100;
$maxpic = "warn";

$res3 = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location>-1") or print(mysql_error());
$arr3 = mysql_fetch_row($res3);
$outmessages = $arr3[0];
$filled = (($outmessages / $maxbox) * 100);
$outpic = get_percent_completed_image(round($filled), $maxpic);
$out = number_format($filled,0);

$res2 = sql_query("SELECT COUNT(*) FROM messages WHERE sender=" . $CURUSER["id"] . " ") or print(mysql_error());
$arr2 = mysql_fetch_row($res2);
$savedmessages = $arr2[0];
$filled = (($savedmessages / $maxbox) * 100);
$savedpic = get_percent_completed_image(round($filled), $maxpic);
$saved = number_format($filled,0);

$res1 = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location>1") or print(mysql_error());
$arr1 = mysql_fetch_row($res1);
$inmessages = $arr1[0];
$filled = (($inmessages / $maxbox) * 100);
$inpic = get_percent_completed_image(round($filled), $maxpic);
$in = number_format($filled,0);

$res = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location=1 AND unread='yes'") or print(mysql_error());
$arr = mysql_fetch_row($res);
$unread = $arr[0];


//---------
// Progress Bar
//-------------
$res1 = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location>0") or print(mysql_error());

$arr1 = mysql_fetch_row($res1);

$mainbox = $arr1[0];

$res1 = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location>2") or print(mysql_error());

$arr1 = mysql_fetch_row($res1);

$abox = $arr1[0];

$res1 = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location>3") or print(mysql_error());
$arr1 = mysql_fetch_row($res1);

$bbox = $arr1[0];

$res1 = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location>4") or print(mysql_error());

$arr1 = mysql_fetch_row($res1);

$cbox = $arr1[0];

$res1 = sql_query("SELECT COUNT(*) FROM messages WHERE sender=" . $CURUSER["id"] . "") or print(mysql_error());

$arr1 = mysql_fetch_row($res1);

$outbox = $arr1[0];


$boo = get_percent_completed_image(floor($mainbox))." ";


$boo2 = get_percent_completed_image(floor($outbox))." ";


$boo3 = get_percent_completed_image(floor($abox))." ";

$boo4 = get_percent_completed_image(floor($bbox))." ";

$boo5 = get_percent_completed_image(floor($cbox))." ";
//---------
// END Progress Bar
//-----------------
print("<tr><td class=colhead width=166 height=18><a href=inbox.php>Inbox</a></td><td class=colhead width=166><a href=inbox.php?out=1>
Sentbox</a></td></tr>");
print("<tr><td>$boo</td><td>$boo2</td></tr>");
print("<tr align=center><td> ($mainbox)</td><td> ($outbox)</td></tr>");
print("<tr><td colspan=3 height=25><b>You have $unread new messages</b></td></tr>");
print("<tr><td colspan=3 height=25><a href=users.php><b>Find User/Browse User List</b></a></td></tr>");

print("</table>");
print("<table width=502 border=1>");

if ($action == "avatar")
{
?>
<form method="post" action="takeeditusercp.php?action=avatar">
<?
print("<tr><td class=colhead colspan=2 height=18>Avatar Options</td></tr>");
print("<tr><td colspan=2> </td></tr>");
if (get_user_class() >= UC_VIP ) {tr("My Title",
"<input size=50 value=\"" . htmlspecialchars($CURUSER["title"]) . "\" name=title><br>",1);
}
tr("Avatar URL ", "<input name=avatar size=50 value=\"" . htmlspecialchars($CURUSER["avatar"]) .
"\"><br><br>\nWidth should be 150 pixels (will be resized if necessary)\n<br>If you need a host for the picture, try the <a href=photo_gallery.php>Site Image Host</a>.",1);
tr("View Avatars ", "<input type=checkbox name=avatars" . ($CURUSER["avatars"] == "yes" ? " checked" : "") . "> (Low bandwidth users might want to turn this off)",1);
}
else if ($action == "signature")
{
?>
<form method="post" action="takeeditusercp.php?action=signature">
<?
print("<tr><td class=colhead colspan=2 height=18>Signature Options</td></tr>");
print("<tr><td colspan=2>" . format_comment($CURUSER[signature]) . "</td></tr>");
print("<tr><td colspan=2> </td></tr>");
tr("Signature ", "<textarea name=signature cols=50 rows=4>" . safechar($CURUSER[signature]) . "</textarea><br><font class=small size=1>Max 225 characters. Max Image Size 500x100.</font>\n<br> May contain <a href=tags.php target=_new>BB codes</a>.", 1);
tr("View Signatures ", "<input type=checkbox name=signatures" . ($CURUSER["signatures"] == "yes" ? " checked" : "") . "> (Low bandwidth users might want to turn this off)",1);
tr("Info ", "<textarea name=info cols=50 rows=4>" . $CURUSER["info"] . "</textarea><br>Displayed on your public page. May contain <a href=tags.php target=_new>BB codes</a>.", 1);
}

else if ($action == "security")
{
?>
<form method="post" action="takeeditusercp.php?action=security">
<?
print("<tr><td class=colhead colspan=2 height=18>Security Options</td></tr>");
///parked mod////
tr("Account parked",
"<input type=radio name=parked" . ($CURUSER["parked"] == "yes" ? " checked" : "") . " value=yes>yes
<input type=radio name=parked" .  ($CURUSER["parked"] == "no" ? " checked" : "") . " value=no>no
<br><font class=small size=1>You can park your account to prevent it from being deleted because of inactivity if you go away on for example a vacation.<br> When the account has been parked limits are put on the account, for example you cannot use the tracker and browse some of the pages.</font>"
,1);
///parked mod//// comment out of not required//
////annonymous mod//////
tr("Anonymous", "<input type=checkbox name=anonymous" . ($CURUSER["anonymous"] == "yes" ? " checked" : "") . "> (Anonymous Status - You profile is protected!)",1);
tr("Anonymous in Top10", "<input type=checkbox name=anonymoustopten" . ($CURUSER["anonymoustopten"] == "yes" ? " checked" : "") . "> Check this not to be shown in Top10",1);
////annonymous mod////comment out if not required
////////////// Passkey //////////////////
if (get_user_class() >= UC_VIP ) {
tr("Reset passkey ","<input type=checkbox name=resetpasskey value=1 /><br><font class=small>Any active torrents must be downloaded again to continue leeching/seeding.</font>", 1);
}
//////////////end passkey//////////////////
tr("Email address ", "<input type=\"text\" name=\"email\" size=50 value=\"" . safechar($CURUSER["email"]) . "\" />", 1);
print("<tr><td class=rowhead>*Note:</td><td align=left>In order to change your email address, you will receive another<br>confirmation email to your new address.</td></tr>\n");
?>
<tr><td class="heading" valign="top" align="right" width="20%">Change Password:</td><td valign="top" align="left" width="80%"><input type="password" name="chpassword" size="30" class="keyboardInput" onkeypress="showkwmessage();return false;" /></td></tr>
<tr><td class="heading" valign="top" align="right" width="20%">Type Password Again:</td><td valign="top" align="left" width="80%"><input type="password" name="passagain" size="30" class="keyboardInput" onkeypress="showkwmessage();return false;" /> <font class=small size=1></font></td></tr>
<?
}

else if ($action == "torrents")
{
?>
<form method="post" action="takeeditusercp.php?action=torrents">
<?
print("<tr><td class=colhead colspan=2 height=18>Torrents Options</td></tr>");
print("<tr><td colspan=2> </td></tr>");
$r = mysql_query("SELECT id,name FROM categories ORDER BY name") or sqlerr();
//$categories = "Default browsing categories:<br>\n";
if (mysql_num_rows($r) > 0)
{
$categories .= "<table><tr>\n";
$i = 0;
while ($a = mysql_fetch_assoc($r))
{
$categories .= ($i && $i % 2 == 0) ? "</tr><tr>" : "";
$categories .= "<td class=bottom style='padding-right: 5px'><input name=cat$a[id] type=\"checkbox\" " . (strpos($CURUSER['notifs'], "[cat$a[id]]") !== false ? " checked" : "") . " value='yes'> " . safechar($a["name"]) . "</td>\n";
++$i;
}
$categories .= "</tr></table>\n";
}
tr("Email notification ", "<input type=checkbox name=pmnotif" . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked" : "") . " value=yes> Notify me when I have received a PM<br>\n" .
"<input type=checkbox name=emailnotif" . (strpos($CURUSER['notifs'], "[email]") !== false ? " checked" : "") . " value=yes> Notify me when a torrent is uploaded in one of <br> my default browsing categories.\n"
, 1);
tr("Browse default <br>categories ",$categories,1);
tr("Categories as Images", "<input type=checkbox name=imagecats" . ($CURUSER["imagecats"] == "yes" ? " checked" : "") . "> (Enable Category Images on Browse.)",1);
if (get_user_class() >= UC_VIP)
tr("Highlight Torrenttable?", "<input type=radio name=ttablehl" . ($CURUSER["ttablehl"] == "yes" ? " checked" : "") . " value=yes>yes
<input type=radio name=ttablehl" . ($CURUSER["ttablehl"] == "no" ? " checked" : "") . " value=no>no"
,1);
tr("Split torrents by days",
"<input type=radio name=split" . ($CURUSER["split"] == "yes" ? " checked" : "") . " value=yes>yes
<input type=radio name=split" .  ($CURUSER["split"] == "no" ? " checked" : "") . " value=no>no"
,1);
tr("Torrents On Homepage",
"<input type=radio name=tohp" . ($CURUSER["tohp"] == "yes" ? " checked" : "") . " value=yes>yes
<input type=radio name=tohp" .  ($CURUSER["tohp"] == "no" ? " checked" : "") . " value=no>no"
,1);
tr("User Class Colour On Browse", "<input type=checkbox name=view_uclass" . ($CURUSER["view_uclass"] == "yes" ? " checked" : "") . "> Select to display uploaders user class colour on <a href=browse.php>browse</a>",1);
print("<tr><td class=colhead colspan=2 height=18><a href=mytorrents.php>My Torrents</a></td></tr>");
}

else if ($action == "personal")
{
?>
<form method="post" action="takeeditusercp.php?action=personal">
<?
print("<tr><td class=colhead colspan=2 height=18>Personal Options</td></tr>");
print("<tr><td colspan=2> </td></tr>");
////////torrents per page
$tor_per_pg = $CURUSER["torrentsperpage"];
$tor_opt .= "<select name=torrentsperpage>";
$tor_opt .= "<option value=5".($tor_per_pg == 5 ? " selected" : "") .">5</option>";
$tor_opt .= "<option value=10".($tor_per_pg == 10 ? " selected" : "") .">10</option>";
$tor_opt .= "<option value=15".($tor_per_pg == 15 ? " selected" : "") .">15</option>";
$tor_opt .= "<option value=50".($tor_per_pg == 50 ? " selected" : "") .">50</option>";
$tor_opt .= "<option value=100".($tor_per_pg == 100 ? " selected" : "") .">100</option>";
$tor_opt .= "<option value=150".($tor_per_pg == 150 ? " selected" : "") .">150</option>";
$tor_opt .= "<option value=200".($tor_per_pg == 200 ? " selected" : "") .">200</option>";
$tor_opt .= "</select>";
echo "Torrents per Page: ".$tor_opt."";
///////////////////////////////////////////////////////////////////////////////////////
////////topics per page
$top_per_pg = $CURUSER["topicsperpage"];
$top_opt .= "<select name=topicsperpage>";
$top_opt .= "<option value=5".($top_per_pg == 5 ? " selected" : "") .">5</option>";
$top_opt .= "<option value=10".($top_per_pg == 10 ? " selected" : "") .">10</option>";
$top_opt .= "<option value=15".($top_per_pg == 15 ? " selected" : "") .">15</option>";
$top_opt .= "<option value=50".($top_per_pg == 50 ? " selected" : "") .">50</option>";
$top_opt .= "<option value=100".($top_per_pg == 100 ? " selected" : "") .">100</option>";
$top_opt .= "<option value=150".($top_per_pg == 150 ? " selected" : "") .">150</option>";
$top_opt .= "<option value=200".($top_per_pg == 200 ? " selected" : "") .">200</option>";
$top_opt .= "</select>";
echo "Topics per Page: ".$top_opt."";
///////////////////////////////////////////////////////////////////////////////////////
////////posts per page
$pos_per_pg = $CURUSER["postsperpage"];
$pos_opt .= "<select name=postsperpage>";
$pos_opt .= "<option value=5".($pos_per_pg == 5 ? " selected" : "") .">5</option>";
$pos_opt .= "<option value=10".($pos_per_pg == 10 ? " selected" : "") .">10</option>";
$pos_opt .= "<option value=15".($pos_per_pg == 15 ? " selected" : "") .">15</option>";
$pos_opt .= "<option value=50".($pos_per_pg == 50 ? " selected" : "") .">50</option>";
$pos_opt .= "<option value=100".($pos_per_pg == 100 ? " selected" : "") .">100</option>";
$pos_opt .= "<option value=150".($pos_per_pg == 150 ? " selected" : "") .">150</option>";
$pos_opt .= "<option value=200".($pos_per_pg == 200 ? " selected" : "") .">200</option>";
$pos_opt .= "</select>";
echo "Posts per Page: ".$pos_opt."";
///////////////////////////////////////////////////////////////////////////////////////
$stylesheets = "<option value=0>---- None selected ----</option>\n";
$stylesheet ='';
include 'include/cache/stylesheets.php';
foreach ($stylesheets as $stylesheet)
$stylesheets .= "<option value=$stylesheet[id]" . ($CURUSER["stylesheet"] == $stylesheet['id'] ? " selected" : "") . ">$stylesheet[name]</option>\n";
$countries = "<option value=0>---- None selected ----</option>\n";
$country = '';
include 'include/cache/countries.php';
foreach ($countries as $country)
$countries .= "<option value=$country[id]" . ($CURUSER["country"] == $country['id'] ? " selected" : "") . ">$country[name]</option>\n";
tr("Stylesheet", "<select name=stylesheet>\n$stylesheets\n</select>",1);
tr("Country", "<select name=country>\n$countries\n</select>",1);
tr("Gender",
"<input type=radio name=gender" . ($CURUSER["gender"] == "N/A" ? " checked" : "") . " value=N/A>N/A
<input type=radio name=gender" . ($CURUSER["gender"] == "Male" ? " checked" : "") . " value=Male>Male
<input type=radio name=gender" .  ($CURUSER["gender"] == "Female" ? " checked" : "") . " value=Female>Female"
,1);
tr("Shoutbox Color", "<input type=radio name=shoutboxbg" . ($CURUSER["shoutboxbg"] == "1" ? " checked" : "") . " value=1>white
<input type=radio name=shoutboxbg" . ($CURUSER["shoutboxbg"] == "2" ? " checked" : "") . " value=2>Grey<input type=radio name=shoutboxbg" . ($CURUSER["shoutboxbg"] == "3" ? " checked" : "") . " value=3>black"
,1);
tr("Userbar", "<img src=\"bar.php/".$CURUSER["id"].".png\" border=\"0\"><br />This is your userbar.You can place it in the signature on the forum.<br />your ratings will be visible<br /><br />Here's the  <b>BB- code</b> for the insert into the signature on the forums:<br /><input type=\"text\" size=65 value=\"[url=$DEFAULTBASEURL][img]$DEFAULTBASEURL/bar.php/".$CURUSER["id"].".png[/img][/url]\" readonly />",1);
tr("Forum online user's ", "<input type=checkbox name=forumview" . ($CURUSER["forumview"] == "yes" ? " checked" : "") . "> (View the forum online user's as either username or avatar !)",1);
}
else
{
?>
<form method="post" action="takeeditusercp.php?action=pm">
<?
print("<tr><td class=colhead colspan=2 height=18>Private Message Options</td></tr>");
print("<tr><td colspan=2> </td></tr>");
tr("Accept PMs ",
"<input type=radio name=acceptpms" . ($CURUSER["acceptpms"] == "yes" ? " checked" : "") . " value=yes>All (except blocks)
<input type=radio name=acceptpms" . ($CURUSER["acceptpms"] == "friends" ? " checked" : "") . " value=friends>Friends only
<input type=radio name=acceptpms" . ($CURUSER["acceptpms"] == "no" ? " checked" : "") . " value=no>Staff only"
,1);
tr("Delete PMs ", "<input type=checkbox name=deletepms" . ($CURUSER["deletepms"] == "yes" ? " checked" : "") . "> (Default value for \"Delete PM on reply\")",1);
tr("Save PMs ", "<input type=checkbox name=savepms" . ($CURUSER["savepms"] == "yes" ? " checked" : "") . "> (Default value for \"Save PM to Sentbox\")",1);
tr("PM on Subscriptions ", "<input type=radio name=subscription_pm" . ($CURUSER["subscription_pm"] == "yes" ? " checked" : "") . " value=yes>yes <input type=radio name=subscription_pm" .  ($CURUSER["subscription_pm"] == "no" ? " checked" : "") . " value=no>no<br> When someone posts in a subscribed thread, you will be PMed.",1);
tr("Make Friends Public", "<input type=checkbox name=showfriends" . ($CURUSER["showfriends"] == "yes" ? " checked" : "") . "> (Allow my friends to be publicly shown?)",1);
tr("Email notification ", " Select under Torrents Option.",1);
}

?>
<tr><td colspan="2" height="30" align="center"><input type="submit" value="Submit changes!" style='height: 25px'> <input type="reset" value="Revert changes!" style='height: 25px'></td></tr>
</table></td>
</form>
<?

//print("</table></td>");
print("<td width=150 valign=top><table border=1>");

print("<tr><td class=colhead width=150 height=18>$CURUSER[username]'s Avatar</td></tr>");
if ($CURUSER[avatar])
print("<tr><td><img width=150 src=" . safechar($CURUSER["avatar"]) . "></td></tr>");
else
print("<tr><td><img width=150 src=pic/default_avatar.gif></td></tr>");
print("<tr><td class=colhead width=150 height=18>$CURUSER[username]'s Menu</td></tr>");
print("<tr><td> </td></tr>");

print("<tr><td align=left> <a href=usercp.php?action=avatar>Avatar</td></tr>");
print("<tr><td align=left> <a href=usercp.php?action=signature>Signature</td></tr>");
//print("<tr><td align=left> <a href=usercp.php>Contacts</td></tr>");
print("<tr><td align=left> <a href=usercp.php>Private Messages</td></tr>");
print("<tr><td align=left> <a href=usercp.php?action=security>Security</td></tr>");
print("<tr><td align=left> <a href=usercp.php?action=torrents>Torrents</td></tr>");
print("<tr><td align=left> <a href=usercp.php?action=personal>Personal</td></tr>");
print("<tr><td align=left> <a href=invite.php>Invites</td></tr>");

print("<tr><td align=left>  <a href=tenpercent.php>Lifesaver</td></tr>");

if (get_user_class() >= UC_POWER_USER)

print("<tr><td class=colhead width=150 height=18>$CURUSER[username]'s Entertainment</td></tr>");

if (get_user_class() >= UC_POWER_USER)

print("<tr><td align=left>  <a href=blackjack.php>BlackJack</td></tr>");

if (get_user_class() >= UC_VIP)

print("<tr><td align=left>  <a href=hangman.php>Hangman</td></tr>");


if (get_user_class() >= UC_POWER_USER)

print("<tr><td align=left>  <a href=casino.php>Casino</td></tr>");


if (get_user_class() > UC_MODERATOR)

print("<tr><td class=colhead width=150 height=18>$CURUSER[username]'s Staff Tools</td></tr>");

if (get_user_class() > UC_MODERATOR)

print("<tr><td align=left>  <a href=usersearch1.php>Find User</td></tr>");

if (get_user_class() > UC_MODERATOR)

print("<tr><td align=left>  <a href=news.php>Add & Edit News</td></tr>");

if (get_user_class() > UC_MODERATOR)

print("<tr><td align=left>  <a href=ipcheck.php>Check Banned IP</td></tr>");

if (get_user_class() > UC_SYSOP)

print("<tr><td align=left>  <a href=bans.php>Add/See Banned</td></tr>");

if (get_user_class() > UC_ADMINISTRATOR)

print("<tr><td align=left>  <a href=usersearch.php>Add/Edit Announcement's</td></tr>");

if (get_user_class() > UC_MODERATOR)

print("<tr><td align=left>  <a href=adduser.php>Create New User</td></tr>");

if (get_user_class() > UC_ADMINISTRATOR)

print("<tr><td align=left>  <a href=moforums.php>Forum Overlay</td></tr>");

if (get_user_class() > UC_MODERATOR)

print("<tr><td align=left>  <a href=forummanage.php>Forum Manage</td></tr>");
print("</table>");
print("</td></tr></table>");
?>

<?
stdfoot();
?>