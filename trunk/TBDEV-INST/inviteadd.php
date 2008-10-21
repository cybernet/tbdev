<?php
// invite add/remove from users by Alex2005 for TBDEV.NET \\
include('include/bittorrent.php');
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
if (get_user_class() < UC_MODERATOR)
hacker_dork("Invite Add - Nosey Cunt !");

if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST"){
$class = $_POST['class'];
if(empty($class) && $class != '0')
stderr("Error","Please select a class.");
if(!is_numeric($class))
stderr("Error","Invalid class number.");
$res = sql_query("SELECT id, invites FROM users WHERE class ".($class == '0' ? ">= '0'" : "= '$class'")." AND enabled = 'yes' AND status = 'confirmed'") or sqlerr(__FILE__, __LINE__);
while ($arr = mysql_fetch_assoc($res)){
$userid = 0+$arr["id"];
$curinvites = 0+$arr['invites'];
$added = sqlesc(get_date_time());
if (!empty($_POST['inviteadd'])){
$toadd = $_POST["inviteadd"];
if (!is_numeric($toadd))
stderr("Error","Invalid invite number.");
sql_query("UPDATE users SET invites = invites + ".sqlesc($toadd)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
if (!empty($_POST['sendpm'])){
$subject = sqlesc("Invite(s) added.");
$msg = sqlesc("We have [b]added[/b] to your class, [u][b]".number_format($toadd)."[/b][/u] invite(s). :madgrin: ");
sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES(0, ".sqlesc($userid).", $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
}}elseif (!empty($_POST['inviteremove'])){
$toremove = $_POST["inviteremove"];
if (!is_numeric($toremove))
stderr("Error","Invalid invite number.");
sql_query("UPDATE users SET invites = ".(($curinvites - $toremove) <= 0 ? "0" : "invites - ".sqlesc($toremove))." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
if (!empty($_POST['sendpm'])){
$subject = sqlesc("Invite(s) removed.");
$msg = sqlesc("We have [b]removed[/b] from your class, [b][u]".number_format($toremove)."[/b][/u] invite(s).");
sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES(0, ".sqlesc($userid).", $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
}}elseif (!empty($_POST['removeallinvites'])){
if (!empty($_POST['sendpmremoveallinvites'])){
$subject = sqlesc("Invites removed from ".($class == '0' ? "all the classes" : "your class").".");
$msg = sqlesc("We have [b]removed[/b] invites ".($class == '0' ? "from all classes" : "your class")." :madgrin:");
sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES(0, ".sqlesc($userid).", $msg, $added, $subject)") or sqlerr(__FILE__, __LINE__);
}
sql_query("UPDATE users SET invites = 0 WHERE class ".($class == '0' ? ">= '0'" : "= '$class'")) or sqlerr(__FILE__, __LINE__);
}else stderr("Error", "Please select something.<br>Go <a href=/inviteadd.php>back</a>.");
}}
stdhead("Add Invites");
?>
<p>
<table align="center" border=0 class=main cellspacing=0 cellpadding=0>
<tr>
<td class=embedded></td>
<td class=embedded style='padding-left: 10px'><font size=3><b>Update User's Invites</b></font></td>
</tr>
</table>
</p>
<form method="POST" action="inviteadd.php">
<table width="60%" border="0" cellpadding="5" cellspacing="0">
<td colspan="2"><h2><b><center>Select Class(es):</center></b></h2></td>
<tr>
<td colspan="2"><table style="border: 0" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td style="border: 0" width="20"><input type="radio" name="class" value="0"><!-- don't change this value --></td>
<td style="border: 0">All Classes</td>

<td style="border: 0" width="20"><input type="radio" name="class" value="<?=UC_USER?>"></td>
<td style="border: 0">Users</td>

<td style="border: 0" width="20"><input type="radio" name="class" value="<?=UC_POWER_USER?>"></td>
<td style="border: 0">Power Users</td>

<td style="border: 0" width="20"><input type="radio" name="class" value="<?=UC_VIP?>"></td>
<td style="border: 0">Vip</td>

</tr>

<tr>
<td style="border: 0" width="20"><input type="radio" name="class" value="<?=UC_UPLOADER?>"></td>
<td style="border: 0">Uploader</td>

<td style="border: 0" width="20"><input type="radio" name="class" value="<?=UC_MODERATOR?>"></td>
<td style="border: 0">Moderator</td>

<td style="border: 0" width="20"><input type="radio" name="class" value="<?=UC_ADMINISTRATOR?>"></td>
<td style="border: 0">Administrator</td>

<td style="border: 0" width="20"><input type="radio" name="class" value="<?=UC_SYSOP?>"></td>
<td style="border: 0">Sysop</td>

<td style="border: 0" width="20"><input type="radio" name="class" value="<?=UC_SYSOP?>"></td>
<td style="border: 0">Coder</td>


</tr>
</table>
</td>
</tr>

<tr>
<td colspan="2" align="center"><b>Number of Invites you want to <u>add</u>:</b><br><input type=text name='inviteadd' size=3></td>
</tr>

<tr>
<td colspan="2" align="center"><b>Number of Invites you want to <u>remove</u>:</b><br><input type=text name='inviteremove' size=3></td>
</tr>

<tr>
<td align=center><input type=submit value="Update" class=button></td>
<td><center><b>Send PMs:</b>&nbsp;<input type="checkbox" name="sendpm"></center></td>
</tr>

<tr>
<td align=center><input type=submit value="Remove all invites" class=button name='removeallinvites'>
<td align="center"><b>Send PMs:</b>&nbsp;<input type="checkbox" name="sendpmremoveallinvites"></td>
</tr>

</td></tr>
</table>
</form>
	
<?
stdfoot();
?>