<?php
ob_start("ob_gzhandler");
require ("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
dbconn(true);
maxcoder();
loggedinorreturn();

///////////latest user - comment out if not required/////
if ($CURUSER)
{
$a = @mysql_fetch_assoc(@sql_query("SELECT id,username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 1")) or die(mysql_error());
if ($CURUSER)
{
$file2 = "$CACHE/index/newestuser.txt";
$expire = 2*60; // 1 minutes
if (file_exists($file2) && filemtime($file2) > (time() - $expire)) {
    $newestuser = unserialize(file_get_contents($file2));
} else {

$res = sql_query("SELECT id,username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 1") or die(mysql_error());
while ($user = mysql_fetch_array($res) ) {
        $newestuser[] = $user;
    }
    $OUTPUT = serialize($newestuser);
    $fp = fopen($file2,"w");
    fputs($fp, $OUTPUT);
    fclose($fp);
} // end else
foreach ($newestuser as $a)
{
  $latestuser = "<a href=userdetails.php?id=" . safeChar($a["id"]) . ">" .safeChar($a["username"]) . "</a>";
}
}
}
///////end latest user///////////
$file = "$CACHE/index/stats.txt";
$expire = 10*60; // 10 minutes
if (file_exists($file) &&
    filemtime($file) > (time() - $expire)) {
$a=unserialize(file_get_contents($file));
$warnedu = $a[1];
$disabled = $a[2];
$registered = $a[3];
$unverified = $a[4];
$torrents = $a[5];
$ratio = $a[6];
$peers = $a[7];
$seeders = $a[8];
$leechers = $a[9];
$totaldownloaded = $a[10];
$totaluploaded = $a[11];
$totaldata = $a[12];
$male = $a[13];
$female= $a[14];
} else {
$warnedu = number_format(get_row_count("users", "WHERE warned='yes'"));
$disabled = number_format(get_row_count("users", "WHERE enabled='no'"));
$registered = number_format(get_row_count("users"));
$unverified = number_format(get_row_count("users", "WHERE status='pending'"));
$torrents = number_format(get_row_count("torrents"));
$disabled = number_format(get_row_count("users", "WHERE enabled='no'"));
$result = sql_query("SELECT SUM(downloaded) AS totaldl, SUM(uploaded) AS totalul FROM users") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($result);
$totaldownloaded = $row["totaldl"];
$totaluploaded = $row["totalul"];
$totaldata = $totaldownloaded+$totaluploaded;
$male = number_format(get_row_count("users", "WHERE gender='Male'"));  
$female = number_format(get_row_count("users", "WHERE gender='Female'"));
$r = sql_query("SELECT value_u FROM avps WHERE arg='seeders'") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_row($r);
$seeders = 0 + $a[0];
$r = sql_query("SELECT value_u FROM avps WHERE arg='leechers'") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_row($r);
$leechers = 0 + $a[0];
$seeders = get_row_count("peers", "WHERE seeder='yes'");
$leechers = get_row_count("peers", "WHERE seeder='no'");
if ($leechers == 0)
$ratio = 0;
else
$ratio = round($seeders / $leechers * 100);
$peers = number_format($seeders + $leechers);
$seeders = number_format($seeders);
$leechers = number_format($leechers);
$stats1 = array(1 => "$warnedu", "$disabled", "$registered","$unverified","$torrents","$ratio","$peers","$seeders","$leechers","$totaldownloaded","$totaluploaded","$totaldata","$male","$female");
$stats2 = serialize($stats1);
$fh = fopen($file, "w");
fwrite($fh,$stats2);
fclose($fh);
}

$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));
$result = sql_query("SELECT SUM(last_access >= $dt) AS totalol FROM users") or sqlerr(__FILE__, __LINE__);
while ($row = mysql_fetch_assoc($result))
{
$totalonline = 0+$row['totalol'];
}
$rec = unserialize(@file_get_contents('cache/onlinerecord.txt'));
if ($rec['record'] < $totalonline){
$array_rec = array (
'date' => get_date_time(),
'record' => $totalonline
);
$array_rec = serialize($array_rec);
$filenum = fopen ('cache/onlinerecord.txt','w+');
$truncate = ftruncate($filenum, 0);
$write = fwrite($filenum, $array_rec);
fclose($filenum);
}

$file3 = "$CACHE/index/active.txt";
$expire = 30; // 30 seconds
if (file_exists($file3) && filemtime($file3) > (time() - $expire)) {
    $active3 = unserialize(file_get_contents($file3));
} else {
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));
$active1 = sql_query("SELECT id, username, class, donor FROM users WHERE last_access >= ".unsafeChar($dt)." ORDER BY class DESC") or sqlerr(__FILE__, __LINE__);
while ($active2 = mysql_fetch_array($active1) ) {
        $active3[] = $active2;
    }
    $OUTPUT = serialize($active3);
    $fp = fopen($file3,"w");
    fputs($fp, $OUTPUT);
    fclose($fp);
} // end else

foreach ($active3 as $arr)
{
  if ($activeusers) $activeusers .= ",\n";
  switch ($arr["class"])
  {
case UC_CODER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . safeChar($arr['username'])."</font>";
   break;
case UC_SYSOP:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . safeChar($arr['username'])."</font>";
   break;
case UC_ADMINISTRATOR:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_MODERATOR:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_UPLOADER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_VIP:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_POWER_USER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_USER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
}
$donator = $arr["donor"] === "yes";
if ($donator)
 $activeusers .= "<nobr>";
$warned = $arr["warned"] === "yes";
if ($warned)
 $activeusers .= "<nobr>";
if ($CURUSER)
$activeusers .= "<a href=userdetails.php?id={$arr["id"]}><b>{$arr["username"]}</b></a>";
else
$activeusers .= "<b>{$arr["username"]}</b>";
if ($donator)
$activeusers .= "<img src={$pic_base_url}star.gif alt='Donated {$$arr["donor"]}'></nobr>";
if ($warned)
$activeusers .= "<img src={$pic_base_url}warned.gif alt='Warned {$$arr["warned"]}'></nobr>";
}
if (!$activeusers)
$activeusers = "There have been no active users in the last 15 minutes.";
stdhead();
///////comment-out to disable latest member display/////
echo("<img src='cimage.php'>");
echo("<br />");
echo "<font class=small><b>Welcome to our newest member, <b>$latestuser</b> !</font>\n";
///////////////////////////////////////////////////////
//Start of Last X torrents with poster mod
$query="SELECT id, name, poster FROM torrents WHERE poster <> '' ORDER BY added DESC limit 15";
$result=mysql_query($query);$num = mysql_num_rows($result);
// count rows
if ($CURUSER['tohp'] == "yes") {
echo("<h2><center>Latest Torrents</center></h2>");
echo '<table cellpadding=5 width=735><td colspan=4><td><tr><marquee scrollAmount=3 onMouseover="this.scrollAmount=0" onMouseout="this.scrollAmount=3" scrolldelay="0" direction="right">';
$i=20;
while ($row = mysql_fetch_assoc($result))  {  $id = unsafeChar($row['id']);
$name = safeChar($row['name']);
$poster = safeChar($row['poster']);
$name = str_replace('_', ' ' , $name);
$name = str_replace('.', ' ' , $name);
$name = substr($name, 0, 50);
if($i==0)echo'</marquee></tr></td><td><tr><marquee scrollAmount=3 onMouseover="this.scrollAmount=0" onMouseout="this.scrollAmount=3" scrolldelay="0" direction="right">';
echo "<a href=$BASEURL/details.php?id=$id title=\"$name\"><img src=\"".safeChar($poster)."\" width=\"100\" height=\"120\" title=\"$name\" border=0 /></a>";
$i++;
}
echo "</marquee></tr></td></table>";
}
//////////End poster mod
//////////////recommeded torrents///////////////
$rezfive = mysql_query("SELECT id, recommended, name, poster FROM torrents WHERE recommended = 'yes' ORDER BY added DESC LIMIT 7") or sqlerr(__FILE__, __LINE__);
if ($CURUSER['rohp'] == "yes") {
print '<h2 align=center><font color=silver>Recommend Torrents</font></h2><table width=737 border=0 ><tr>';
if (mysql_num_rows($rezfive) > 0) {
while ($fiverow = mysql_fetch_assoc($rezfive)) { 
$poster = (!empty($fiverow["poster"]) ? (str_replace(" ", "%20", safeChar($fiverow['poster']))) : 'pic/poster.jpg');
//print ("<td><a href=\"".$BASEURL."/details.php?id=".$fiverow['id']."\" title=\"$name\" border=0 /><img src=\"".$poster."\" width=\"100\" height=\"120\"/></a><br /></td>");
print ("<td><a href=\"".$BASEURL."/details.php?id=".$fiverow['id']."\"/><img src=\"".safeChar($poster)."\" width=\"100\" height=\"120\" border=0 /></a><br /></td>");
}
}
echo "</tr></table>";
}
///////////
///////////////Birthday cache///////////////////////////////////
$file8 = "$CACHE/index/birthday.txt";
//$expire = 30; // 30 seconds
$expire = 21600; // 6 hours
if (file_exists($file8) && filemtime($file8) > (time() - $expire)) {
    $res3 = unserialize(file_get_contents($file8));
} else {
$today= date("'%'-m-d");
$current_date = getdate();
list($year1, $month1, $day1) = split('-', $currentdate);
$res1 = sql_query("SELECT id, username, birthday, class, gender, donor FROM users WHERE MONTH(birthday) = '".unsafeChar($current_date['mon'])."' AND DAYOFMONTH(birthday) = '".unsafeChar($current_date['mday'])."' ORDER BY class DESC ") or sqlerr(__FILE__, __LINE__);
while ($res2 = mysql_fetch_array($res1) ) {
        $res3[] = $res2;
    }
    $OUTPUT = serialize($res3);
    $fp = fopen($file8,"w");
    fputs($fp, $OUTPUT);
    fclose($fp);
} // end else

if ( is_array( $res3 ) )
foreach ($res3 as $arr)
{
$birthday = date($arr["birthday"]);
   if ($birthdayusers) $birthdayusers .= ",\n";
   switch ($arr["class"])
  {
case UC_CODER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_SYSOP:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_ADMINISTRATOR:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_MODERATOR:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_UPLOADER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_VIP:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_POWER_USER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
case UC_USER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . SafeChar($arr['username'])."</font>";
   break;
}

$donator = $arr["donor"] === "yes";
if ($donator)
 $birthdayusers .= "<nobr>";
$warned = $arr["warned"] === "yes";
if ($warned)
 $birthdayusers .= "<nobr>";

if ($CURUSER)
$birthdayusers .= "<a href=userdetails.php?id={$arr["id"]}><b>{$arr["username"]}</b></a>";
else
$birthdayusers .= "<b>{$arr["username"]}</b>";
if ($donator)
$birthdayusers .= "<img src={$pic_base_url}star.gif alt='Donated {$$arr["donor"]}'></nobr>";
if ($warned)
$birthdayusers .= "<img src={$pic_base_url}warned.gif alt='Warned {$$arr["warned"]}'></nobr>";
}
if (!$birthdayusers)
$birthdayusers = "There Is No Member Birthdays Today.";
////////////////////////////
//////////////////news cache/////////////
$cachefile = "cache/news".($CURUSER['class'] >= UC_ADMINISTRATOR ? 'staff' : '').".html";
if (file_exists($cachefile))
{
include($cachefile);
}
else {
ob_start(); // start the output buffer
?>
<table width='737' class='main' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'><?php
?><h2 align="center">News<?php
if ($CURUSER['class'] >= UC_ADMINISTRATOR)
{
?>&nbsp;-&nbsp;<font class='small'>[<a class='altlink' href='/news.php'><b>News</b></a>]</font><?php
}
?></h2><?php
$res = mysql_query("SELECT n.id, n.added, n.title, n.body, n.sticky, u.username ".
"FROM news AS n ".
"LEFT JOIN users AS u ON u.id = n.userid ".
"WHERE ADDDATE(n.added, INTERVAL 45 DAY) > NOW() ".
"ORDER BY sticky, n.added DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
?><table width='100%' border='1' cellspacing='0' cellpadding='10'><tr><td class='text'><ul><?php
for ($i = 0; $arr = mysql_fetch_assoc($res); ++$i)
{
?><a href="javascript: klappe_news('a<?php echo $arr['id']; ?>')"><br><img border="0" src="pic/<?php echo ($i > 0 ? 'plus' : 'minus'); ?>.gif" id="pica<?php echo $arr['id']; ?>" alt="Show/Hide">&nbsp;<?php echo gmdate("Y-m-d", strtotime($arr['added'])); ?> - <b><?php echo ($arr['sticky']=="yes" ? "<img src='pic/sticky.gif' border='0' alt='sticky'>" : ""); ?>&nbsp;&nbsp;<?php echo safeChar($arr['title']); ?></b><?php echo ($i > 0 ? "&nbsp;&nbsp;Posted by&nbsp;".safeChar($arr['username']) : ''); ?></a><?php
if ($CURUSER['class'] >= UC_ADMINISTRATOR)
{
?>&nbsp;<font size="-2"> &nbsp; [<a class='altlink' href='/news.php?action=edit&newsid=<?php echo $arr['id']; ?>&returnto=<?php echo urlencode($_SERVER['PHP_SELF']); ?>'><b>E</b></a>]</font><?php
?>&nbsp;<font size="-2">[<a class='altlink' href='/news.php?action=delete&newsid=<?php echo $arr['id']; ?>&returnto=<?php echo urlencode($_SERVER['PHP_SELF']); ?>'><b>D</b></a>]</font><?php
}
?><div id="ka<?php echo $arr['id']; ?>" style="display: none;"><?php echo format_comment($arr["body"], false); ?></div><?php
}
?></ul></td></tr></table>
<?php
}
$fp = fopen($cachefile, 'w');
// save the contents of output buffer to the file
fwrite($fp, ob_get_contents());
// close the file
fclose($fp);
// Send the output to the browser
ob_flush();
}
?><?php
///////////////news end////////////////////////
////////////////changelog cache////////////////
$cachefile = "cache/changelog".($CURUSER['class'] >= UC_ADMINISTRATOR ? 'staff' : '').".html";
if (file_exists($cachefile))
{
include($cachefile);
}
else {
ob_start(); // start the output buffer
?><table width='737' class='main' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'><?php
?><h2 align="center">Change Log<?php
if ($CURUSER['class'] >= UC_SYSOP)
{
?>&nbsp;-&nbsp;<font class='small'>[<a class='altlink' href='/changelog.php'><b>Change Log</b></a>]</font><?php
}
?></h2><?php
$res = mysql_query("SELECT cl.id, cl.added, cl.title, cl.body, cl.sticky, u.username ".
"FROM changelog AS cl ".
"LEFT JOIN users AS u ON u.id = cl.userid ".
"WHERE ADDDATE(cl.added, INTERVAL 30 DAY) > NOW() ".
"ORDER BY sticky, cl.added DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
?><table width='100%' border='1' cellspacing='0' cellpadding='10'><tr><td class='text'><ul><?php

for ($i = 0; $arr = mysql_fetch_assoc($res); ++$i)
{
?><a href="javascript: klappe_news('a<?php echo $arr['id']; ?>')"><br><img border="0" src="pic/<?php echo ($i > 0 ? 'plus' : 'minus'); ?>.gif" id="pica<?php echo $arr['id']; ?>" alt="Show/Hide">&nbsp;<?php echo gmdate("Y-m-d", strtotime($arr['added'])); ?> - <b><?php echo ($arr['sticky']=="yes" ? "<img src='pic/sticky.gif' border='0' alt='sticky'>" : ""); ?>&nbsp;&nbsp;<?php echo safeChar($arr['title']); ?></b><?php echo ($i > 0 ? "&nbsp;&nbsp;Posted by&nbsp;".safeChar($arr['username']) : ''); ?></a><?php
if ($CURUSER['class'] >= UC_SYSOP)
{
?>&nbsp;<font size="-2"> &nbsp; [<a class='altlink' href='/changelog.php?action=edit&changelogid=<?php echo $arr['id']; ?>&returnto=<?php echo urlencode($_SERVER['PHP_SELF']); ?>'><b>E</b></a>]</font><?php
?>&nbsp;<font size="-2">[<a class='altlink' href='/changelog.php?action=delete&changelogid=<?php echo $arr['id']; ?>&returnto=<?php echo urlencode($_SERVER['PHP_SELF']); ?>'><b>D</b></a>]</font><?php
}
?><div id="ka<?php echo $arr['id']; ?>" style="display: none;"><?php echo format_comment($arr["body"], false); ?></div><?php
}
?></ul></td></tr></table><?php
}
$fp = fopen($cachefile, 'w');
// save the contents of output buffer to the file
fwrite($fp, ob_get_contents());
// close the file
fclose($fp);
// Send the output to the browser
ob_flush();
}
?><?php
/////////////////changelog end///////////////
/////////cached theme selector//////////////////
$stylesheets = "<option value=0>---- None selected ----</option>\n";
$stylesheet ='';
include 'include/cache/stylesheets.php';
foreach ($stylesheets as $stylesheet)
$stylesheets .= "<option value=$stylesheet[id]" . ($CURUSER["stylesheet"] == $stylesheet['id'] ? " selected" : "") . ">$stylesheet[name]</option>\n";
begin_table("Theme");?>
<table width='100%' class='main' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
<tr id="theme">
<form action="take_theme.php" method="post">
	<td>
	<p align="center">Theme Changer</p>
	<tr>
	<td align="center">
	<select name="stylesheet" onchange="this.form.submit();" size="1" style="font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #ececec">
	<?=$stylesheets?></select></td></tr>
	<tr>
	<td align="center">
	<input name="Submit" style="font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #ececec" type="hidden" value="ok">
	</td>
	</tr>
</form>
<?php
end_table();
?>
<?php
/////// best film of the week by dokty - tbdev.net///////////
$resbest = sql_query("SELECT torrents.id, torrents.leechers, torrents.seeders, categories.image, categories.name as catname, torrents.name, torrents.times_completed FROM torrents INNER JOIN categories ON categories.id=torrents.category INNER JOIN avps ON torrents.id=avps.value_s WHERE avps.arg='bestfilmofweek' LIMIT 1") or sqlerr(__FILE__,__LINE__);
$arrbest = mysql_fetch_assoc($resbest);
if ($arrbest)
print("<h2><center>&nbsp;Best Film of the Week</h2></center><table width=100% border=1 cellspacing=0 cellpadding=10><tr><td align=center><table border=1 cellspacing=0 cellpadding=5><tr><td class=colhead align=left>Type</td><td class=colhead align=left>Name</td><td class=colhead align=left>Snatched</td><td class=colhead align=left>Seeders</td><td class=colhead align=left>Leechers</td></tr><tr><td align=center><img border='0' src='pic/".htmlentities($arrbest["image"])."' alt='".htmlentities($arrbest["catname"])."' title='".htmlentities($arrbest["catname"])."' /></td><td style='padding-right: 5px'><a href=details.php?id=".$arrbest["id"]."><b>".htmlentities($arrbest["name"])."</b></td><td align=center>".$arrbest["times_completed"]."</td><td align=center>".$arrbest["seeders"]."</td><td align=center>".$arrbest["leechers"]."</td></tr></table></td></tr></table>");
?>
<?php
require_once("include/function_forumpost.php");
latestforumposts();
?>
<h2 align="center">Polls
<?php
if ($CURUSER['class'] >= UC_SYSOP)
{
?>
&nbsp;-&nbsp;<font class='small'>[<a class='altlink' href='/poller-admin.php'><b>Poll Admin</b></a>]</font><?php
}
?></h2>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return false" method="post">
<?php
$res = mysql_query("select * from poller ORDER by ID DESC LIMIT 1");
if (mysql_num_rows($res))
$inf = mysql_fetch_array($res);
$pollerId = (int)$inf['ID'];
?>
<!-- START OF POLLER -->
<div id="poller">
<div class="poller_question" id="poller_question<? echo $pollerId; ?>">
<?php
// Retreving poll from database
$res = mysql_query("select * from poller where ID='$pollerId'");
if($inf = mysql_fetch_assoc($res)){
echo "<p class=\"pollerTitle\">".$inf["pollerTitle"]."</p>"; // Output poller title
$resOptions = mysql_query("select * from poller_option where pollerID='$pollerId' order by pollerOrder") or die(mysql_error()); // Find poll options, i.e. radio buttons
while($infOptions = mysql_fetch_array($resOptions)){
if($infOptions["defaultChecked"])$checked=" checked"; else $checked = "";
echo "<p class=\"pollerOption\"><input$checked type=\"radio\" value=\"".$infOptions["ID"]."\" name=\"vote[".$inf["ID"]."]\" id=\"pollerOption".$infOptions["ID"]."\"><label for=\"pollerOption".$infOptions["ID"]."\" id=\"optionLabel".$infOptions["ID"]."\">".$infOptions["optionText"]."</label></p>";
}
}
?>
<a href="#polls" onclick="castMyVote(<? echo $pollerId; ?>,document.forms[2])"><img src="pic/vote_button.gif"></a><a name="polls"></a>
</div>
<div class="poller_waitMessage" id="poller_waitMessage<? echo $pollerId; ?>">
Getting poll results. Please wait...
</div>
<div class="poller_results" id="poller_results<? echo $pollerId; ?>">
<!-- This div will be filled from Ajax, so leave it empty -->
</div>
</div>
<!-- END OF POLLER -->
<script type="text/javascript">
//if(useCookiesToRememberCastedVotes){
var cookieValue = Poller_Get_Cookie('dhtmlgoodies_poller_<? echo $pollerId; ?>');
if(cookieValue && cookieValue.length>0)displayResultsWithoutVoting(<? echo $pollerId; ?>); // This is the code you can use to prevent someone from casting a vote. You should check on cookie or ip address

//}
</script>
</form>
<br>
<table align="center" border="1" width="760">
	<h2><center>User&#39;s Online</center></h2>
	<font class="small"><center>Active users- <?=$totalonline?> ( Active On Site <?=$uniqpeer?>
	Presently )</center></font>
</table>
<table align="center" border="1" width="760">
	</tr>
</table>
<div id="div2" style="display: none;">
	<table border="1" cellpadding="10" cellspacing="0" width="760">
		<tr class="ttable">
			<td class="text"><?=$activeusers?></td>
		</tr>
	</table>
</div>
<center><b><a href="#activeusers" onclick="closeit('div2');"><font color="red">[
Hide</font></a></b> | <b><a href="#activeusers" onclick="showit('div2');">
<font color="red">Show ]</font></a></b></center>
<table align="center" border="1" width="760">
	</tr>
</table>
<?
if ($CURUSER['bohp'] == "yes") { ?>
	<h2><center>Member's Birthday's</center></h2>
<div id="div7" style="display: none;">
	<table border="1" cellpadding="10" cellspacing="0" width="760">
		<tr class="ttable">
			<td class="text"><?=$birthdayusers?></td>
		</tr>
	</table>
</div>
<center><b><a href="#activeusers" onclick="closeit('div7');"><font color="red">[
Hide</font></a></b> | <b><a href="#birthdayusers" onclick="showit('div7');">
<font color="red">Show ]</font></a></b></center>
<? } ?>
<h2></h2>
<center><b>Tracker Statistics </b></center>
<div id="div3" style="display: none;">
	<table border="1" cellpadding="10" cellspacing="0" width="760">
		<tr>
			<td align="center">
			<table border="1" cellpadding="5" cellspacing="0" class="main">
				<b>
				<tr>
					<td class="rowhead">Online Since</td>
					<td align="right"><b><?=$config['onlinesince']?></b></td>
				</tr>
                	<tr>
					<td class="rowhead">Online </td>
					<td align="right"><b><?php echo $totalonline;?></b></td>
				</tr>
                	<tr>
					<td class="rowhead">Record</td>
					<td align="right"><b><?php echo $rec['record'].' at '.$rec['date'];?></b></td>
				</tr>
				<tr>
					<td class="rowhead">Max Users <img src="pic/buddylist.gif"></td>
					<td align="right"><?=$maxusers?></td>
				</tr>
				<tr>
					<td class="rowhead">Registered users
					<img src="pic/buddylist.gif"></td>
					<td align="right"><?=$registered?></td>
				</tr>
				<tr>
					<td class="rowhead">Unconfirmed users
					<img src="pic/buddylist.gif"></td>
					<td align="right"><?=$unverified?></td>
				</tr>
                                                                 <tr>
					<td class="rowhead">Male Users
					<img src="pic/male.gif"></td>
					<td align="right"><?=$male?></td>
				</tr>
                                                                 <tr>
					<td class="rowhead">Female Users
					<img src="pic/female.gif"></td>
					<td align="right"><?=$female?></td>
				</tr>
				<?php 
                if (get_user_class() >= UC_MODERATOR) { ?>
                <tr>
					<td class="rowhead">Warned Users <img src="pic/warned8.gif"></td>
					<td align="right"><?=$warnedu?></td>
				</tr>
				<tr>
					<td class="rowhead">Banned Users <img src="pic/warned1.gif"></td>
					<td align="right"><?=$disabled?></td>
				</tr>
                <? } ?>
				<?php 
                if (get_user_class() >= UC_POWER_USER) { ?>
                <tr>
					<td class="rowhead">Torrents <img src="pic/torrents.gif"></td>
					<td align="right"><?=$torrents?></td>
				</tr>
				<? if (isset($peers)) { ?>
				<tr>
					<td class="rowhead">Seeders <img src="pic/arrowup.gif"></td>
					<td align="right"><?=$seeders?></td>
				</tr>
				<tr>
					<td class="rowhead">Total uploaded
					<img src="pic/arrowup.gif"></td>
					<td align="right"><?=mksize($totaluploaded)?></td>
				</tr>
				<tr>
					<td class="rowhead">Leechers <img src="pic/arrowdown.gif"></td>
					<td align="right"><?=$leechers?></td>
				</tr>
				<tr>
					<td class="rowhead">Total downloaded
					<img src="pic/arrowdown.gif"></td>
					<td align="right"><?=mksize($totaldownloaded)?></td>
				</tr>
				<tr>
					<td class="rowhead">Seeder/leecher ratio (%)
					<img src="pic/arrowup.gif"><img src="pic/arrowdown.gif"></td>
					<td align="right"><?=$ratio?></td>
				</tr>
				<? } ?>
				<? } ?>
			</table>
			</b></td>
		</tr>
	</table>
</div>
<center><b><a href="#TrackerStatistics" onclick="closeit('div3');">
<font color="red">[ Hide</font></a></b> | <b>
<a href="#Tracker Statistics" onclick="showit('div3');"><font color="red">Show ]</font></a></b></center>
</td>
</tr>
</table>
<table border="1" cellpadding="10" cellspacing="0" width="760">
	<tr>
		<td align="center">
		<p>Donations</p>
		<a href="donate.php">
		<img border="0" alt="Make A Donation" src="pic/makedonation.gif"></a><?php
//====donation progress bar by snuggles enjoy
$total_funds1 = sql_query("SELECT sum(cash) as total_funds FROM funds");
$arr_funds = mysql_fetch_array($total_funds1);
$funds_so_far = $arr_funds["total_funds"];
$totalneeded = "264";    //=== set this to your monthly wanted amount
$funds_difference = $totalneeded - $funds_so_far;
$Progress_so_far = number_format($funds_so_far / $totalneeded * 100, 1);
if($Progress_so_far >= 100)
$Progress_so_far = "100";
echo"<table width=160 height=17 border=2><tr><td bgcolor=blue align=center valign=middle width=$Progress_so_far%>$Progress_so_far%</td><td bgcolor=grey align=center valign=middle</td></tr></table><br />";
//end
?>
<?php
if ($CURUSER["gotgift"] == 'no') {
?>
		<div align="right">
			<a href="gift.php?open=1">
			<img alt="Gift" src="pic/gift.png" style="float: right; border-style: none;"></a></div>
		<?php
}
?>

		<table align="center" border="1" cellpadding="10" cellspacing="5" width="738">
					<center>DiSCLAiMER</center>
		<marquee behavior="scroll" direction="up" height="60" onmouseout="this.start()" onmouseover="this.stop()" scrollamount="1" width="100%">
		<br><br><br><br>
		<p><font class="small">None of the files shown here are actually hosted
		on this server. The links are provided solely by this sites users. These
		BitTorrent files are meant for the distribution of backup files. By downloading
		the BitTorrent file, you are claiming that you own the original file. The
		administrator of this site <?php echo $BASEURL?> holds NO Responsibility
		if these files are misused in any way and cannot be held responsible for
		what its users post, or any other actions of its users. For controversial
		reasons, if you are affiliated with any government, Anti-Piracy group or
		any other related group, or were formally a worker of one you Cannot download
		any of these BitTorrent files. You may not use this site to distribute or
		download any material when you do not have the legal rights to do so. It
		is your own responsibility to adhere to these terms.<br><br><br><br><br>
		<br></font></p></marquee> </td>
	</tr>
</table>
</td>
	</tr>
</table>
</td></tr></table>
<?
print("<p align=center><font class=small>Updated ".date('Y-m-d H:i:s', filemtime($file))."</font></p>");
stdfoot();
?>