<?php
require_once("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
dbconn(false);

loggedinorreturn();

stdhead("Contact Staff", false);
?>
<table class=main width=450 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<div align=center>
<h1>Send message to Staff</h1>

<form method=post name=message action=takecontact.php>
<? if ($_GET["returnto"] || $_SERVER["HTTP_REFERER"]) { ?>
<input type=hidden name=returnto value=<?=$_GET["returnto"] ? $_GET["returnto"] : $_SERVER["HTTP_REFERER"]?>>
<? } ?>
<table class=message cellspacing=0 cellpadding=5>
<tr><td<?=$replyto?" colspan=2":""?>>
<b>&nbsp;&nbsp;Subject: </b><br><input type=text size=83 name=subject style='margin-left: 5px'>
<?
textbbcode("message","msg","$body");
?></td></tr>

<tr><td align=center><input type=submit value="Send it!" class=btn></td></tr>

</table>
</form>
</div></td></tr></table>

<?
stdfoot();
?>