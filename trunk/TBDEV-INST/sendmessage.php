<?php
require "include/bittorrent.php";
require_once "include/user_functions.php";
require_once ("include/bbcode_functions.php");
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
parked();
// Standard Administrative PM Replies
$pm_std_reply[1] = "Read the bloody FAQ and stop bothering me!";
$pm_std_reply[2] = "Die! Die! Die!";

// Standard Administrative PMs
$pm_template['1'] = array("Ratio warning","Hi,\n
You may have noticed, if you have visited the forum, that Yoursite is disabling the accounts of all users with low share ratios.\n
I am sorry to say that your ratio is a little too low to be acceptable. You need to seed [insert amount of data here] in order for your ratio to reach the minimum acceptable level. You have [insert time period here] to get your share ratio to an acceptable level, otherwise, your membership here will be at risk.\n
If you would like your account to remain open, you must ensure that your ratio increases dramatically in the next day or two, to get as close to 1.0 as possible.\n
I am sure that you will appreciate the importance of sharing your downloads.
You may PM any Moderator, if you believe that you are being treated unfairly.\n
Thank you for your cooperation.");
$pm_template['2'] = array("Avatar warning", "Hi,\n
You may not be aware that there are new guidelines on avatar sizes in the rules, in particular \"Resize
your images to a width of 150 px and a size of no more than 150 KB.\"\n
I'm sorry to say your avatar doesn't conform to them. Please change it as soon as possible.\n
We understand this may be an inconvenience to some users but feel it is in the community's best interest.\n
Thanks for the cooperation.");
$pm_template['3'] = array("Port warning", "Hi,\n
Currently you are showing as not being connectable from other peers. This may be due to the ports you are using, or incorrectly configured network involving your firewall and/or router. Please change your port range, or check to ensure that the relevant ports are indeed open in your network AND your bittorrent application. If you use a firewall or a router you will have to apply changes there too.\n
Please read the FAQ regarding this issue! \n
If you cannot find an answer there, do not hesitate to post in the HELP section of the user forum. We understand this may be an inconvenience but feel it is in the community's best interest.\n
Thanks for the cooperation.");
$pm_template['4'] = array("Description warning","Hi,\n
Thank you for contributing to our community by uploading. To make it easier for other members of the tracker we would appreciate it, if you could give the torrent(s) a better/different description. It doesn't have to be a long text. Just the category (like PLUG-IN, GRAPHIC, VIDEO,...) and a little additional information for people that are not sure what this is. You can even display a hyperlink to the company's homepage. It is easy to just copy and paste the description from the developer's site. \n
Please refer to this topic xxxxxxxxxxxxxx Thank you for your cooperation.\n");
$pm_template['5'] = array("Serial warning", "Hi,\n
Please do NOT post questions for serials or cracks in the user forum!!!\n
Please read the FAQ and the Rules regarding this issue!\n
Thanks for the cooperation.");
$pm_template['6'] = array("Requests", "Hi,\n
All requests should be directed to the Requests section. Your post has been deleted to tidy up the forum. If you find that you cannot make a request, it is likely that you have not achieved the required share ratio of .50 or greater.\n
Thanks for the cooperation.");


// Standard Administrative MMs
$mm_template['1'] = $pm_template['1'];
$mm_template['2'] = array("Downtime warning","We'll be down for a few hours");
$mm_template['3'] = array("Change warning","The tracker has been updated. Read
the forums for details.");

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{ //////// MM //
if (get_user_class() < UC_MODERATOR)
stderr("Error", "Permission denied");

$n_pms = $_POST['n_pms'];
$pmees = $_POST['pmees'];
$auto = $_POST['auto'];

if ($auto)
$body=$mm_template[$auto][1];

stdhead("Send message", false);

?>
<table class=main width=750 border=0 cellspacing=0 cellpadding=0>
<tr><td class=embedded><div align=center>
<h1>Mass Message to <?=$n_pms?> user<?=($n_pms>1?"s":"")?>!</h1>
<form method=post action=takemessage.php>
<? if ($_SERVER["HTTP_REFERER"]) { ?>
<input type=hidden name=returnto value=<?=$_SERVER["HTTP_REFERER"]?>>
<? } ?>
<table border=1 cellspacing=0 cellpadding=5>
<TR>
<TD colspan="2"><B>Subject: </B>
<INPUT name="subject" type="text" size="76"></TD>
</TR>
<tr><td colspan="2"><div align="center">
<textarea name=msg cols=80 rows=15><?=safechar($body)?></textarea>
</div></td></tr>
<tr><td colspan="2"><div align="center"><b>Comment: </b>
<input name="comment" type="text" size="70">
</div></td></tr>
<tr><td><div align="center"><b>From: </b>
<?=$CURUSER['username']?>
<input name="sender" type="radio" value="self" checked>
System
<input name="sender" type="radio" value="system">
</div></td>
<td><div align="center"><b>Take snapshot:</b> <input name="snap" type="checkbox" value="1">
</div></td></tr>
<tr><td colspan="2" align=center><input type=submit value="Send it!" class=btn>
</td></tr></table>
<input type=hidden name=pmees value="<?=$pmees?>">
<input type=hidden name=n_pms value=<?=$n_pms?>>
</form><br><br>
<form method=post action=<?=$_SERVER['PHP_SELF']?>>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td>
<b>Templates:</b>
<select name="auto">
<?php
for ($i = 1; $i <= count($mm_template); $i++) {
echo "<option value=$i ".($auto == $i?"selected":"").
">".$mm_template[$i][0]."</option>\n";}
?>
</select>
<input type=submit value="Use" class=btn>
</td></tr></table>
<input type=hidden name=pmees value="<?=$pmees?>">
<input type=hidden name=n_pms value=<?=$n_pms?>>
</form></div></td></tr></table>
<?php
} else { //////// PM //
$receiver = 0+$_GET["receiver"];
    if (!is_valid_id($receiver))
      die;

    $replyto = 0+$_GET["replyto"];
    if ($replyto && !is_valid_id($replyto))
      die;

$auto = $_GET["auto"];
$std = $_GET["std"];

if (($auto || $std ) && get_user_class() < UC_MODERATOR)
die("Permission denied.");

$res = mysql_query("SELECT * FROM users WHERE id=$receiver") or die(mysql_error());
$user = mysql_fetch_assoc($res);
if (!$user)
die("No user with that ID.");

if ($auto)
$body = $pm_std_reply[$auto];
if ($std)
$body = $pm_template[$std][1];

if ($replyto)
{
$res = mysql_query("SELECT * FROM messages WHERE id=$replyto") or sqlerr();
$msga = mysql_fetch_assoc($res);
if ($msga["receiver"] != $CURUSER["id"])
die;
$res = mysql_query("SELECT username FROM users WHERE id=" . $msga["sender"]) or sqlerr();
$usra = mysql_fetch_assoc($res);
$body .= "\n\n\n-------- $usra[username] wrote: --------\n$msga[msg]\n";
$subject = "Re: " . safechar($msga['subject']);
}



stdhead("Send message", false);
?>
<table class=main width=750 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<div align=center>
<h1>Message to <a href=userdetails.php?id=<?=$receiver?>><?=$user["username"]?></a></h1>
<form name=message method=post action=takemessage.php>
<? if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"]) { ?>
<input type=hidden name=returnto value=<?=$_GET["returnto"] ? $_GET["returnto"] : $_SERVER["HTTP_REFERER"]?>>
<? } ?>
<table class=message cellspacing=0 cellpadding=5>
<TR>
<TD colspan="2"><B>Subject: </B>
<INPUT name="subject" type="text" size="76" value="<?=$subject?>"></TD>
</TR>
<tr><td<?=$replyto?" colspan=2":""?>>
<?php
textbbcode("message","msg","$body");
?></td></tr>
<tr>
<? if ($replyto) { ?>
<td align=center><input type=checkbox name='delete' value='yes' <?=$CURUSER['deletepms'] == 'yes'?"checked":""?>>Delete Message you´re reply
<input type=hidden name=origmsg value=<?=$replyto?>></td>
<? } ?>
<td align=center><input type=checkbox name='save' value='yes' <?=$CURUSER['savepms'] == 'yes'?"checked":""?>>Save Message to sentbox</td></tr>
<?

if (get_user_class() >= UC_ADMINISTRATOR)
{
?>
<tr>
<td colspan=1><div align="center"><b>Sender: </b>
<?=$CURUSER['username']?>
<input name="sender" type="radio" value="self" checked>
System
<input name="sender" type="radio" value="system">
<?php
}
?>
</div></td></tr><tr><td<?=$replyto?" colspan=2":""?> align=center><input type=submit value="OK!" class=btn></td></tr>
</table>
<input type=hidden name=receiver value=<?=$receiver?>>
</form>

<?php
if (get_user_class() >= UC_MODERATOR)
{
?>
<br><br>
<form method=get action=<?=$_SERVER['PHP_SELF']?>>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td>
<b>PM Text :</b>
<select name="std"><?
for ($i = 1; $i <= count($pm_template); $i++)
{
echo "<option value=$i ".($std == $i?"selected":"").
">".$pm_template[$i][0]."</option>\n";
}?>
</select>
<? if ($_SERVER["HTTP_REFERER"]) { ?>
<input type=hidden name=returnto value=<?=$_GET["returnto"]?$_GET["returnto"]:$_SERVER["HTTP_REFERER"]?>>
<? } ?>
<input type=hidden name=receiver value=<?=$receiver?>>
<input type=hidden name=replyto value=<?=$replyto?>>
<input type=submit value="Use it!" class=btn>
</td></tr></table></form>
<?php
}
?>

</div></td></tr></table>
<?
}
stdfoot();
?>