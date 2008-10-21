<?php
require_once("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
dbconn();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

header("Content-Type: text/html; charset=iso-8859-1");
if ($CURUSER["class"] < UC_USER)
stderr("Access denied", "You must have be registered User in order to send invite.");

if ($CURUSER["invite_on"] == 'no')
stderr("Denied", "Your invite sending privileges has been disabled by the Staff!");

$minratio = 0.8;
$ratio = $CURUSER['downloaded'] > 0 ? $CURUSER['uploaded']/$CURUSER['downloaded'] : 0;
if (($ratio < $minratio) && $CURUSER["class"] < UC_ADMINISTRATOR)
stderr("Denied", "Your share ratio is below $minratio , You cannot invite anyone untill your ratio is above 0.8.");

if ($CURUSER["uploaded"] > 0 && $CURUSER["downloaded"] == 0)
	stderr("Denied", "You have an infinite ratio please download something in order to invite.");

if (get_user_class() < UC_USER)
stderr("Denied", "You must be User+ in order to send an invite.");
$res = mysql_query("SELECT COUNT(*) FROM users") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
if ($arr[0] >= $invites)
stderr("Denied", "We reached our user limit (" . number_format($invites) . ") . Try again later...");
$rez = $CURUSER["invites"];

if($CURUSER["invites"] == 0)
    stderr("Denied","You don't have any invites!");
    stdhead("Invites")

?>
<p>
  <form method="post" action="takeinvite.php">
    <h2>Attention!</h2>
    Think of the invitations you are making as if to your wedding, you would invite only people you know, people you trust.<br>
    This is what you should do here, don.t waste your invitation on someone you barely know or that is not a good sharer.<br>
    You don't want to be associated with him.Be discreet, invite people you trust.<br>
    It's for your safety, and that of the entire community. Thanks for your cooperation.<br><br>
    <table border="0" class=detail width=655 cellspacing=0 cellpadding="10">
    <tr valign=top><td align="right" class="heading">Information</td><td class=detail align=left><b>  - You have  <?print("<font color=red>$rez</font>"); ?> invite(s) left</b></td></tr>
    <tr valign=top><td align="right" class="heading">Recipient's email:</td><td class=detail align=left><input type="text" size="40" name="email" />
    <tr><td align="right" class="heading">Message: </td><td class=detail align=left><textarea name="mess" rows="10" cols="80">
Hello,
I am inviting you to join Yoursite. This is a private community which has very knowledgable members. If you are interested in joining the community please read over the rules and confirm the invite.
Regards,
<?print($CURUSER[username]);?></textarea>
    </td></tr> 
    <tr><td colspan="2" align="center"><input type=submit  class="button"  value="Send&nbsp;invite! (PRESS ONLY ONCE)"></td></tr>
    </table>
    </form>

<?
stdfoot();
?>