<?php
require ("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
dbconn();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
parked();
stdhead("YourSite Staff");
begin_main_frame();
begin_frame("");
?>
<?
$act = $_GET["act"];
if (!$act) {
// Get current datetime
$dt = gmtime() - 60;
$dt = sqlesc(get_date_time($dt));
// Search User Database for Moderators and above and display in alphabetical order
$res = mysql_query("SELECT * FROM users WHERE class>=".UC_MODERATOR.
" AND status='confirmed' ORDER BY username" ) or sqlerr();

while ($arr = mysql_fetch_assoc($res))
{

$staff_table[$arr['class']]=$staff_table[$arr['class']].
"<td class=staffembedded><a class=altlink href=userdetails.php?id=".$arr['id'].">".
$arr['username']."</a></td><td class=staffembedded> ".("'".$arr['last_access']."'">$dt?"<img src=".$pic_base_url."user_online.gif border=0 alt=\"online\">":"<img src=".$pic_base_url."user_offline.gif border=0 alt=\"offline\">" )."</td>".
"<td class=staffembedded><a href=sendmessage.php?receiver=".$arr['id'].">".
"<img src=".$pic_base_url."pm.gif border=0></a></td>".
" ";



// Show 3 staff per row, separated by an empty column
++ $col[$arr['class']];
if ($col[$arr['class']]<=2)
$staff_table[$arr['class']]=$staff_table[$arr['class']]."<td class=staffembedded>&nbsp;</td>";
else
{
$staff_table[$arr['class']]=$staff_table[$arr['class']]."</tr><tr height=15>";
$col[$arr['class']]=0;
}
}
begin_frame("Staff");
?>

<style type="text/css">
#fieldset_CODER { border:1px solid #FF0000 }
#fieldset_FLS { border:1px solid purple }
#fieldset_SYSOP { border:1px solid blue }
#fieldset_ADMIN { border:1px solid lightblue }
#fieldset_MOD { border:1px solid orange }
#fieldset_STAFF { border:1px solid red }
#fieldset { border:1px solid green }
</style>
<fieldset id="fieldset CODER">
<legend id="fieldset CODER">Site Staff</legend>

<table width=725 cellspacing=0>
<tr>
<tr><td class=staffembedded colspan=11>All software support questions and those already answered in the FAQ will be ignored.</td></tr>
<!-- Define table column widths -->
<td class=staffembedded width="105">&nbsp;</td>
<td class=staffembedded width="25">&nbsp;</td>
<td class=staffembedded width="35">&nbsp;</td>
<td class=staffembedded width="85">&nbsp;</td>
<td class=staffembedded width="105">&nbsp;</td>
<td class=staffembedded width="25">&nbsp;</td>
<td class=staffembedded width="35">&nbsp;</td>
<td class=staffembedded width="85">&nbsp;</td>
<td class=staffembedded width="105">&nbsp;</td>
<td class=staffembedded width="25">&nbsp;</td>
<td class=staffembedded width="35">&nbsp;</td>
</tr>
<tr><td class=staffembedded colspan=10>&nbsp;</td></tr>
<tr><td class=staffembedded colspan=10><b>Coder</b></td></tr>
<tr><td class=staffembedded colspan=10><hr color="#A83838" size=1></td></tr>
<tr height=15>
<?=$staff_table[UC_CODER]?>

<tr><td class=staffembedded colspan=10>&nbsp;</td></tr>
<tr><td class=staffembedded colspan=10><b>SySop's</b></td></tr>
<tr><td class=staffembedded colspan=10><hr color="#A83838" size=1></td></tr>
<tr height=15>
<?=$staff_table[UC_SYSOP]?>

<tr><td class=staffembedded colspan=10>&nbsp;</td></tr>
<tr><td class=staffembedded colspan=10><b>Administrator's</b></td></tr>
<tr><td class=staffembedded colspan=10><hr color="#A83838" size=1></td></tr>
<tr height=15>
<?=$staff_table[UC_ADMINISTRATOR]?>


<tr><td class=staffembedded colspan=10>&nbsp;</td></tr>
<tr><td class=staffembedded colspan=10><b>Moderator's</b></td></tr>
<tr><td class=staffembedded colspan=10><hr color="#A83838" size=1></td></tr>
<tr height=15>
<?=$staff_table[UC_MODERATOR]?>

<tr><td class=staffembedded colspan=10>&nbsp;</td></tr>
<tr><td class=staffembedded colspan=10><b>Uploader's</b></td></tr>
<tr><td class=staffembedded colspan=10><hr color="#A83838" size=1></td></tr>
<tr height=15>
<?=$staff_table[UC_UPLOADERS]?>
</fieldset>

</table>
<?
end_frame();
}
?>
<?

if (!$act) {
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));
// LIST ALL FIRSTLINE SUPPORTERS
// Search User Database for Firstline Support and display in alphabetical order
$res = sql_query("SELECT * FROM users WHERE support='yes' AND status='confirmed' ORDER BY username LIMIT 20") or sqlerr();
while ($arr = mysql_fetch_assoc($res))
{
$land = sql_query("SELECT name,flagpic FROM countries WHERE id=$arr[country]") or sqlerr();
$arr2 = mysql_fetch_assoc($land);
$firstline .= "<tr height=15><td class=embedded><a class=altlink href=userdetails.php?id=".$arr['id'].">".$arr['username']."</a></td>
<td class=embedded> ".("'".$arr['last_access']."'">$dt?"<img src=".$pic_base_url."user_online.gif border=0 alt=\"online\">":"<img src=".$pic_base_url."user_offline.gif border=0 alt=\"offline\">" )."</td>".
"<td class=embedded><a href=sendmessage.php?receiver=".$arr['id'].">"."<img src=".$pic_base_url."pm.gif border=0></a></td>".
"<td class=embedded><img src=".$pic_base_url."/flag/$arr2[flagpic] border=0 width=19 height=12></td>".
"<td class=embedded>".$arr['supportfor']."</td></tr>\n";
}


begin_frame("Firstline Support");
?>
<style type="text/css">
#fieldset_CODER { border:1px solid #FF0000 }
#fieldset_FLS { border:1px solid purple }
#fieldset_SYSOP { border:1px solid blue }
#fieldset_ADMIN { border:1px solid lightblue }
#fieldset_MOD { border:1px solid orange }
#fieldset_STAFF { border:1px solid red }
#fieldset { border:1px solid teal }
</style>

<fieldset id="fieldset_FLS">
<legend id="fieldset_FLS">Support</legend>
<table cellspacing="0" width="725">
	<tr>
		<td class="embedded" colspan="11">General support questions should directed 
		to these users.<br />
		Note that they are volunteers, giving away their time and effort to help 
		you. Treat them accordingly. (Languages listed are those besides English.)<br>
		<br><br></td>
	</tr>
	<tr>
		<td class="embedded" width="30"><b>Username&nbsp; </b></td>
		<td class="embedded" width="5"><b>Active&nbsp;&nbsp;&nbsp; </b></td>
		<td class="embedded" width="5"><b>Contact&nbsp;&nbsp;&nbsp;&nbsp; </b>
		</td>
		<td class="embedded" width="85"><b>Language</b></td>
		<td class="embedded" width="200"><b>Support for:</b></td>
	</tr>
	<tr>
	</tr>
	<tr>
		<td class="embedded" colspan="11"><hr color="#000000" size="1"></td>
	</fieldset>
	</tr>
	<?=$firstline?>
	</tr>
</table>
<?
end_frame();
}

?><?
end_frame();
end_main_frame();
stdfoot();
?>