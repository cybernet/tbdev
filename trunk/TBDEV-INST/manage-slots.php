<?php
require_once("include/bittorrent.php");
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
if (get_user_class() < UC_ADMINISTRATOR)
hacker_dork("manage-slots.php");
//stderr("Permission Denied", "...");
stdhead("Bonus Slots Manager");

?>

<form method="POST" action="take-slots.php">
<h1>Give Slots By Class</h1>

<table>
<tr>
<td class=colhead colspan=3 align=left>All Members</td>
<td class=colhead><input name="class" type="radio" value=">= 0" checked></td>
</tr>
<tr>
<td class=colhead colspan=3 align=left>All Users</td>
<td class=colhead><input name="class" type="radio" value="= 0"></td>
</tr>
<tr>
<td class=colhead colspan=3 align=left>All Power Users</td>
<td class=colhead><input name="class" type="radio" value="= 1"></td></td>
</tr>
<tr>
<td class=colhead colspan=3 align=left>All VIP</td>
<td class=colhead><input name="class" type="radio" value="= 2"></td>
</tr>
<tr>
<td class=colhead colspan=3 align=left>All Staff</td>
<td class=colhead> <input name="class" type="radio" value=">= 3"></td>
</tr>
</table>

<table>
<p> <input type="submit" name="1" value="Give 1 Slot">
<input type="submit" name="2" value="Give 2 Slots">
<input type="submit" name="3" value="Give 3 Slots">
<input type="submit" name="5" value="Give 5 Slots">
<input type="submit" name="10" value="Give 10 Slots">
</p>
<p><input type="submit" name="0" value="Reset Slots to Zero" onClick="return confirm('Are you sure you want to reset this classes slots to zero?')"></p>
</table>

<br>
</form>

<?
stdfoot();
?>