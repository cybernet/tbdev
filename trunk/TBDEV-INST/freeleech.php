<?php
require ("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
////////////////snuggles freedownload for staff.php by Bigjoos////////////////
dbconn();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
parked();
//=== set all torrents to free
if ($_GET['free_for_all1']) {
if (get_user_class() < UC_ADMINISTRATOR)
    stderr("Error", "Permission denied.");
    
$resfree = sql_query("SELECT * FROM free_download");
$arrfree = mysql_fetch_assoc($resfree);
$free_for_all = $arrfree["free_for_all"] == 'yes';
$title = $arrfree["title"];
$message = $arrfree["message"];
    
if ($_GET['do_it']) {

$free = sqlesc($_POST['free_for_all']);
$title = $_POST["title"];
if ($title == "")
stderr("Error", "You must enter a title!");    

$message = $_POST["body"];
if (!$message)
stderr("Error", "You must enter a message!");

$title = sqlesc($title);
$message = sqlesc($message);

sql_query("UPDATE free_download SET free_for_all=$free, title=$title, message=$message WHERE free=free") or sqlerr(__FILE__,__LINE__);
header("Refresh: 0; url=freeleech.php?free_for_all1=1");
}    

stdhead("Set Torrents Free");
begin_main_frame();
begin_frame("Set Torrents Free!",true);        

    if ($free_for_all)
        echo("<H1>ALL Torrents are Free!</H1>");
        else
        echo("<H1>ALL Torrents Are Not Free!</H1>");
$title = unesc($title);
if (!$free_for_all){
$title = 'enter title';
$message = 'enter message';
}
echo"<table><tr><td class=colhead colspan=2>Free Torrents For All</td></tr><form name=compose action=freeleech.php?free_for_all1=1&do_it=1 method=post>".
"<tr><td align=right><b>Set Torrents Free:</b></td><td colspan=2 align=left><input type=radio name=free_for_all value=yes" .($arrfree["free_for_all"]=="yes" ? " checked" : "") . ">Yes <input type=radio name=free_for_all value=no" .($arrfree["free_for_all"]=="no" ? " checked" : "") . ">No</td></tr>\n".
"<tr><td align=right><b>Title:</b></td><td colspan=2 align=left><input type=text size=60 name=title value=\"$title\">";
echo("<tr><td align=right><b>Message:</b></td><td align=left>\n");
$body = unesc($message);
textbbcode("compose","body","$body");
print("</td></tr><tr><td align=center colspan=2><input type=hidden name=do_it value=1>".
"<input class=button type=submit value=\"set us free!\"><br><br></td></tr></form></table><br>\n");
end_frame();
end_main_frame();
stdfoot();
die();
}
stdhead("Extra Staff Tools");
if (get_user_class() < UC_ADMINISTRATOR)
{
  stdmsg("Access Denied", "Access to this page has been denied.");
  stdfoot();
  exit;
}
?>

<?
?>
	<table cellspacing="3" width="640">

	<tr>
		<td class="embedded">
		<a class="altlink" href="<?=$PHP_SELF;?>?free_for_all1=1">Set all torrents 
		free?</a></td>
		<td class="embedded">setting all torrents free or not free... this will 
		not effect the regular &quot;free&quot; torrents.</td>
	</tr>
</table>
<?

stdfoot();
?>