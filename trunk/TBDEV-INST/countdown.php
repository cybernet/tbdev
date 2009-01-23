<?php
require_once ("include/bittorrent.php");
//original ideea from hellix modified by sonebreth later by putyn :)
dbconn();
loggedinorreturn();
if (get_user_class() < UC_SYSOP)
stderr('Error', 'Permission denied');

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
$err = "";
if(isset($_POST['removeit']) && $_POST['removeit']=='Remove'){

$filenum = fopen ($CACHE."/countdown.txt",'w');
$truncate = ftruncate($filenum, 0);
fclose($filenum);
$err .= ($truncate ? "File was deleted" : "There was a problem!");
}
else
{
$day = isset($_POST['day']) ? 0 + $_POST['day'] : '';
$month = isset($_POST['month']) ? 0 + $_POST['month'] : '';
$year = isset($_POST['year']) ? 0 + $_POST['year'] : '';
$comment = isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : '';

if (!checkdate($month,$day,$year) || !$comment)
stderr('Error', 'Missing form data');
$countdown = array('day'=> $day,
'month'=> $month,
'year'=> $year,
'comment'=> $comment);

$filenum = fopen ($CACHE."/countdown.txt",'w+');
$write = fwrite($filenum, serialize($countdown));
fclose($filenum);
$err .= $write ? "Event saved!" : "Something happned, and the event was not saved";
}
}
stdhead('Countdown');
$cur = unserialize(@file_get_contents($CACHE."/countdown.txt"));
?><h2>Create Countdown</h2>
<!--original idea from hellix modified by stonebreth alter by putyn :)-->
<form method='post' action='countdown.php'>
<table border='1' cellspacing='0' cellpadding='5'>
<?
if($err){
?>
<tr><td class="colhead" align="center" colspan="2"><?=$err?></td></tr>
<?}?>
<tr><td align="center" colspan="2">Day<input type='text' name='day' value="<?=$cur["day"]?>" size='5' />&nbsp;Month<input type='text' value="<?=$cur["month"]?>" name='month' size='5' />&nbsp;Year<input type='text' name='year' value="<?=$cur["year"]?>" size='5' /></td></tr>
<tr><td class='rowhead'>Comment</td><td><textarea name="comment" rows="5" cols="60"><?=$cur["comment"]?></textarea></td>
<tr><td colspan='2' align='center'><input type='submit' name='okay' value='Add' class='btn' />&nbsp;&nbsp;<input type='submit' name='removeit' value='Remove' class='btn' /></td></tr>
</table></form>
<?php stdfoot(); ?>