<?php 

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
///////////////mood/////////////////////////
if ($CURUSER)
  {
       foreach($mood as $key => $value)
         $change[$value['id']]=array('id' => $value['id'], 'name' => $value['name'], 'image' => $value['image']);
         $moodname = $change[$CURUSER['mood']]['name'];
         $moodpic = $change[$CURUSER['mood']]['image'];
  }
////////////////////////////////////////////
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="java_klappe.js"></script>
<script language="javascript" src="js/ajax_details.js"></script>
<script type="text/javascript" src="javascripts/ajax.js"></script>
<script type="text/javascript" src="javascripts/ajax-poller.js"></script>
<script language=javascript>
<!--
function Post()
{
document.compose.action = "?action=post"
document.compose.target = "";
document.compose.submit();
return true;
}
-->
</script>
<script type="text/javascript">
<!--
function popitup(url) {
    newwindow=window.open(url,'usermood.php','height=335,width=735,resizable=no,scrollbars=no,toolbar=no,menubar=no');
    if (window.focus) {newwindow.focus()}
    return false;
}
// -->
</script>
<script type="text/javascript">

/***********************************************
* Dynamic Ajax Content- A,© Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

var bustcachevar=1 //bust potential caching of external pages after initial request? (1=yes, 0=no)
var loadedobjects=""
var rootdomain="http://"+window.location.hostname
var bustcacheparameter=""

function ajaxpage(url, containerid){
var page_request = false
if (window.XMLHttpRequest) // if Mozilla, Safari etc
page_request = new XMLHttpRequest()
else if (window.ActiveXObject){ // if IE
try {
page_request = new ActiveXObject("Msxml2.XMLHTTP")
}
catch (e){
try{
page_request = new ActiveXObject("Microsoft.XMLHTTP")
}
catch (e){}
}
}
else
return false
document.getElementById(containerid).innerHTML='<img src="pic/loading.gif" alt="LoadingData" />'
page_request.onreadystatechange=function(){
loadpage(page_request, containerid)
}
if (bustcachevar) //if bust caching of external page
bustcacheparameter=(url.indexOf("?")!=-1)? "&"+new Date().getTime() : "?"+new Date().getTime()
page_request.open('GET', url+bustcacheparameter, true)
page_request.send(null)
}

function loadpage(page_request, containerid){
if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1))
document.getElementById(containerid).innerHTML=page_request.responseText
}

function loadobjs(){
if (!document.getElementById)
return
for (i=0; i<arguments.length; i++){
var file=arguments[i]
var fileref=""
if (loadedobjects.indexOf(file)==-1){ //Check to see if this object has not already been added to page before proceeding
if (file.indexOf(".js")!=-1){ //If object is a js file
fileref=document.createElement('script')
fileref.setAttribute("type","text/javascript");
fileref.setAttribute("src", file);
}
else if (file.indexOf(".css")!=-1){ //If object is a css file
fileref=document.createElement("link")
fileref.setAttribute("rel", "stylesheet");
fileref.setAttribute("type", "text/css");
fileref.setAttribute("href", file);
}
}
if (fileref!=""){
document.getElementsByTagName("head").item(0).appendChild(fileref)
loadedobjects+=file+" " //Remember this object as being already added to page
}
}
}

</script>
<script type="text/javascript" src="FormManager.js">
/****************************************************
* Form Dependency Manager- By Twey- http://www.twey.co.uk
* Visit Dynamic Drive for this script and more: http://www.dynamicdrive.com
****************************************************/
</script>
<script type="text/javascript">
function SelectAll(id)
{
    document.getElementById(id).focus();
    document.getElementById(id).select();
}
</script>
<script type="text/javascript">
<!--
function SetSize(obj, x_size) {
      if (obj.offsetWidth > x_size) {
      obj.style.width = x_size;
  };
};
//-->

<script language="javascript" src="scriptaculous/prototype.js"><\/script>
<script language="javascript" src="scriptaculous/scriptaculous.js"><\/script>
<link rel="shortcut icon" href="favicon.ico" >
      <link rel="icon" href="animated_favicon1.gif" type="image/gif" >
<script type="text/javascript" src="java_klappe.js"></script>
<title>
<?= $title ?>
</title>
<link rel="stylesheet" type="text/css" href="./themes/<?=$ss_uri."/".$ss_uri?>.css">
<link rel="stylesheet" href="css/lightbox.css" type="text/css" media="screen">
<? if ($CURUSER) { ?>
<link rel="alternate" type="application/rss+xml" title="Latest Torrents" href="rss.php?feed=dl&passkey=<?=$CURUSER["passkey"]?>&user=<?=$CURUSER["username"]?>">
<? } ?>
<script type="text/javascript" src="keyboard.js" charset="UTF-8"></script>
<link rel="stylesheet" type="text/css" href="keyboard.css">
<script type="text/javascript" src="js/balloontip.js"></script>
<script type="text/javascript" src="scripts/overlib.js"></script>
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
<script type="text/javascript" src="scripts/cookies.js"></script>
<script type="text/javascript" src="scripts/popup.js"></script>
<script language="javascript" src="js/blendtrans.js"></script>
<script language="javascript" src="js/fade.js"></script>
<script type="text/javascript" src="js/ajax_details.js"></script>
<script type="text/javascript"language="JavaScript1.2">
function log_out()
{
    ht = document.getElementsByTagName("html");
    ht[0].style.filter = "progid:DXImageTransform.Microsoft.BasicImage(grayscale=1)";
    if (confirm(l_logout))
    {
        return true;
    }
    else
    {
        ht[0].style.filter = "";
        return false;
    }
}
var l_logout="Are you sure, you want to logout?";
</script>
<script type="text/javascript">
function closeit(box)
{
document.getElementById(box).style.display="none";
}

function showit(box)
{
document.getElementById(box).style.display="block";
}
</script>
</head>
<html>
<script LANGUAGE="JavaScript">

//<!-- Begin
var checkflag = "false";
function check(field) {
if (checkflag == "false") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = "true";
return "Uncheck All"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = "false";
return "Check All"; }
}
//  End -->
</script>
</head>
<script type="text/javascript" src="ncode_imageresizer.js"></script>
<script type="text/javascript">
<!--
NcodeImageResizer.MODE = 'newwindow';
NcodeImageResizer.MAXWIDTH = "600";
NcodeImageResizer.MAXHEIGHT = "480";

NcodeImageResizer.Msg1 = 'Click this bar to view the full image.';
NcodeImageResizer.Msg2 = 'This image has been resized. Click this bar to view the full image.';
NcodeImageResizer.Msg3 = 'This image has been resized. Click this bar to view the full image.';
NcodeImageResizer.Msg4 = 'Click this bar to view the small image.';
//-->
</script>
<title>
<?= $title ?>
</title>
<link rel="stylesheet" type="text/css" href="./themes/<?=$ss_uri."/".$ss_uri?>.css">
</head><body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<table width=100"%" cellspacing=0 cellpadding=0 style='background: transparent'>
  <?php $w = "width=100%";
?>
  <table background="pic/backcen.gif" class="cHs" width="838" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="3" class="cHs2" width="838" height="145" background="pic/logo.jpg"></td>
    </tr>
	<tr>
      <td colspan="3" class="cHs2" height="27" background="pic/menu.jpg"><table class="clear" background="pic/backcen.gif" width:="834" margin-top:="0" align="center" border="0" cellspacing="8" cellpadding="2">
          <? if (!$CURUSER) { ?>
          <? } else { ?>
          <center>
          
          <td align="center" class="navigation" background="pic/backcen.gif">
            <td align="center" class="navigation"><a href=index.php>Home</a></td>
            <td align="center" class="navigation"><a href=browse.php>Browse</a></td>
            <td align="center" class="navigation"><a href=chat.php>Irc</a></td>
            <? 
if (get_user_class() >= UC_POWER_USER) { ?>
            <td align="center" class="navigation"><a href="viewrequests.php">Request</a></td>
            <? } ?>
            <td align="center" class="navigation"><a href=viewoffers.php>Offer</a></td>
            <td align="center" class="navigation"><a href=upload.php>Upload</a></td>
            <td align="center" class="navigation"><a href=usercp.php>Profile</a></td>
            <td align="center" class="navigation"><a href=forums.php>Forum</a></td>
            <td align="center" class="navigation"><a href=helpdesk.php>Help</a></td>
            <? 
if (get_user_class() >= UC_POWER_USER) { ?>
            <td align="center" class="navigation"><a href="dox.php">Dox</a></td>
            <? } ?>
            <? 
if (get_user_class() >= UC_POWER_USER) { ?>
            <td align="center" class="navigation"><a href="topten.php">Top10</a></td>
            <? } ?>
            <td align="center" class="navigation"><a href=rules.php>Rules</a></td>
            <td align="center" class="navigation"><a href=faq.php>Faq</a></td>
            <td align="center" class="navigation"><a href=links.php>Links</a></td>
            <?
echo "<td align=center class=navigation>".($CURUSER['show_shout'] === 'no' ? "<a class=normal href=shoutbox.php?show_shout=1&show=yes>Chat on</a>" : "<a class=normal href=shoutbox.php?show_shout=1&show=no>Chat off</a>")." </td>";
?>
            <td align="center" class="navigation"><a href=staff.php>Staff</a></td>
            <? 
if (get_user_class() >= UC_MODERATOR) { ?>
            <td align="center" class="navigation"><a href="staffpanel.php">Admin</a></td>
            <? } ?>
        </table></td>
    </tr>
    <? } ?>
    <tr>
      <td class="cHs" background="pic/left.gif"></td>
      <td class='cHs' align=center width=838 background="pic/backcen.gif" class=interior valign=top>
      <br />
      <!-- /////// some vars for the statusbar;o) //////// -->
      <? if ($CURUSER) { ?>
      <?php
$datum = getdate();
$datum[hours] = sprintf("%02.0f", $datum[hours]);
$datum[minutes] = sprintf("%02.0f", $datum[minutes]);
$invites = $CURUSER['invites'];
$uped = mksize($CURUSER['uploaded']);
$downed = mksize($CURUSER['downloaded']);
$ratio = $CURUSER['downloaded'] > 0 ? $CURUSER['uploaded']/$CURUSER['downloaded'] : 0;
$ratio = number_format($ratio, 3);
$color = get_ratio_color($ratio);
if ($color)
$ratio = "<font color=$color>$ratio</font>";
if ($CURUSER['donor'] == "yes")
$medaldon = "<img src=pic/star.gif alt=donor title=donor>";
if ($user["webseeder"] == "yes")
$uweb = "<img src=pic/seeder.gif>";
///////////////////check message counts//////////////////////////////////////////
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location=1") or print(mysql_error());
$arr = mysql_fetch_row($res);
$messages = $arr[0];
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " && unread='yes'") or print(mysql_error());
$arr = mysql_fetch_row($res);
$unread = $arr[0];
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE sender=" . $CURUSER["id"] . " AND saved='yes'") or print(mysql_error());
$arr = mysql_fetch_row($res);
$outmessages = $arr[0];
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE sender=" . $CURUSER["id"] . " && unread='yes' AND saved='yes'") or print(mysql_error());
$arr = mysql_fetch_row($res);
$unread2 = $arr[0];

if ($unread)
$inboxpic = "<img height=14px style=border:none alt=inbox title='inbox (new PMs)' src=pic/pn_inboxnew.gif>";
else
$inboxpic = "<img height=14px style=border:none alt=inbox title='inbox (no new PMs)' src=pic/pn_inbox2.gif>";
if ($unread2)
$outboxpic = "<img height=14px style=border:none alt=sentbox title='sentbox (unread sent PMs)' src=pic/pn_sentbox2.gif>";
else
$outboxpic = "<img height=14px style=border:none alt=sentbox title='sentbox (no unread sent PMs)' src=pic/pn_inbox2.gif>";

$res9 = sql_query(
"SELECT ".
    "(SELECT COUNT(peers.id) FROM peers WHERE userid=" . $CURUSER["id"] . " AND seeder='yes') AS activeseed_count, ".
    "(SELECT COUNT(peers.id) FROM peers WHERE userid=" . $CURUSER["id"] . " AND seeder='no') AS activeleech_count, ".
    "(SELECT connectable FROM peers WHERE userid=" . $CURUSER["id"] . " LIMIT 1) AS connectable");
$arr9 = mysql_fetch_assoc($res9);	
$activeseed = $arr9['activeseed_count'];
$activeleech = $arr9['activeleech_count'];
// check if user is connectable or not
       $connect = $arr9['connectable'];
       if($connect == "yes"){
         $connectable = "<font color=green><b>Yes</b></font>";
       }elseif($connect == "no"){
         $connectable = "<b><font color=red>No</font></b>";
       }else{
         $connectable ="---";
            }

//////////////////// Kommentera modd ////////////////////////
$res = sql_query("SELECT torrent FROM peers WHERE userid='$CURUSER[id]'");
while($row = mysql_fetch_array($res)){
    $kom = mysql_fetch_array(sql_query("SELECT count(*) FROM comments WHERE user='$CURUSER[id]' AND torrent='$row[torrent]'"));
    $tor = mysql_fetch_array(sql_query("SELECT name,owner FROM torrents WHERE id='$row[torrent]'"));
    if(!$kom[0] && $tor[owner] != $CURUSER[id]){
 $komment .= "<a href=details.php?id=".$row[torrent].">".$tor[name]."</a><br>";
    }
}

$time = date("H");
     if(($time >= 6) && ($time < 12)){        $hi = "<font color=blue>Morning</font>"; }
     if(($time >= 11) && ($time < 12)){      $hi = "<font color=blue>Munchtime</font>"; }
     if(($time >= 12) && ($time < 18)){      $hi = "<font color=blue>Afternoon</font>"; }
     if(($time >= 17) && ($time < 18)){      $hi = "<font color=blue>Teatime</font>"; }
     if(($time >= 18) && ($time < 24)){      $hi = "<font color=blue>Evening</font>"; }
     if(($time >= 23) && ($time < 0)){        $hi = "<font color=blue>Bedtime</font>"; }
     if(($time >= 0) && ($time < 6)){          $hi = "<font color=blue>Goodnight</font>"; }
?>
      <? 
// Start PHP
if ($CURUSER['override_class'] != 255) $usrclass = "&nbsp;<b>(".get_user_class_name($CURUSER['class']).")</b>&nbsp;";
elseif(get_user_class() >= UC_MODERATOR) $usrclass = "&nbsp;<a href=setclass.php><b>(".get_user_class_name($CURUSER['class']).")</b></a>&nbsp;";
?>
      <table align="center" background="pic/backcen.gif" cellpadding="4" cellspacing="1" border=hidden style="width:100%">
        <tr>
          <td class="statusbar"><table align="center" background="pic/backcen.gif" style="width:834" cellspacing="0" cellpadding="0" border="0">
              <tr>
                <td class="bottom" background="pic/backcen.gif" align="left"><span class="smallfont"><font color=gray><b>
                  <?=$hi?>
                  </b> </font><b><a href="userdetails.php?id=<?=$CURUSER['id']?>">
                  <?=$CURUSER['username']?>
                  </a></b>
                  <?=$usrclass?>
                  <?=$medaldon?>
                  <?=$warn?>
                  <? if ($CURUSER['webseeder'] == 'yes') { ?><img src="pic/seeder.gif" title="WebSeeder"><? } ?>
                  <font color=black><b>Bonus :<a href="mybonus.php">
                  <?=number_format($CURUSER['seedbonus'], 1)?>
                  </a> &nbsp;<img src='/pic/freedownload.gif' width='10' height='10' border='0' alt='Free Slots : <?=number_format($CURUSER['freeslots'])?>' title='Free Slots : <?=number_format($CURUSER['freeslots'])?>'><font color='#EAFF08'>
                  <?=number_format($CURUSER['freeslots'])?>
                  </font>&nbsp;<a href='<?=$DEFAULTEBASEURL?>bookmarks.php?id=<?=$CURUSER['id']?>'> <img src='pic/bookmark.gif' width='10' height='10' border='0' alt='My Bookmarks' title='Total Bookmarks: <?php echo  number_format(get_row_count("bookmarks", "WHERE userid=$CURUSER[id]"))?>'><font color='#000000'></a> &nbsp;<?php echo  number_format(get_row_count("bookmarks", "WHERE userid=$CURUSER[id]"))?></font>&nbsp;<font color=black>Connectable :</font>
                  <?=$connectable?>
                  </b>&nbsp
                  <?if($invites>0){?>
                  [Invites(<a href=invite.php>
                  <?=$invites?>
                  </a>)]
                  <?}?>
                  &nbsp;
                  [<a href="logout.php" onClick="return log_out()"><b>Logout</b></a>]
                  <br />
                  <b><font color=black>Ratio : </font>
                  <?=$ratio?>
                  &nbsp;&nbsp;<font color=green>Up : </font><font color=#777777>
                  <?=$uped?>
                  </font> &nbsp;&nbsp;<font color=darkred>Down : </font> <font color=#777777>
                  <?=$downed?>
                  </font> &nbsp;&nbsp;<font color=black>Active :&nbsp;</font></span> <img alt="Torrents seeding" title="Torrents seeding" src="pic/arrowup.gif">&nbsp;<font color=#777777><span class="smallfont">
                  <?=$activeseed?>
                  </span></font>&nbsp;&nbsp;<img alt="Torrents leeching" title="Torrents leeching" src="pic/arrowdown.gif">&nbsp;<font color=#777777><span class="smallfont">
                  <?=$activeleech?>
                  </span></font>
                <td class="bottom" align="right"><span class="smallfont"><?=$clock?><span id="clock"></span><br>
                <script type="text/javascript">
function refrClock(){
var d=new Date();
var s=d.getSeconds();
var m=d.getMinutes();
var h=d.getHours();
var day=d.getDay();
var date=d.getDate();
var month=d.getMonth();
var year=d.getFullYear();
var am_pm;
if (s<10) {s="0" + s}
if (m<10) {m="0" + m}
if (h>12) {h-=12;am_pm = "Pm"}
else {am_pm="Am"}
if (h<10) {h="0" + h}
document.getElementById("clock").innerHTML=h + ":" + m + ":" + s + " " + am_pm;
setTimeout("refrClock()",1000);
}
refrClock();
</script>
                  </font>
<?
if ($messages){
print("<span class=smallfont><a href=messages.php?action=viewmailbox>$inboxpic</a> $messages ($unread New)</span>");
if ($outmessages)
print("<span class=smallfont>&nbsp;&nbsp;<a href=messages.php?action=viewmailbox&box=-1><img height=14px style=border:none alt=sentbox title=sentbox src=pic/pn_sentbox2.gif></a> $outmessages</span>");
else
print("<span class=smallfont>&nbsp;&nbsp;<a href=messages.php?action=viewmailbox&box=-1><img height=14px style=border:none alt=sentbox title=sentbox src=pic/pn_sentbox2.gif></a> 0</span>");
}
else
{
print("<span class=smallfont><a href=messages.php?action=viewmailbox><img height=14px style=border:none alt=inbox title=inbox src=pic/pn_inbox2.gif></a> 0</span>");
if ($outmessages)
print("<span class=smallfont>&nbsp;&nbsp;<a href=messages.php?action=viewmailbox&box=-1><img height=14px style=border:none alt=sentbox title=sentbox src=pic/pn_sentbox2.gif></a> $outmessages</span>");
else
print("<span class=smallfont>&nbsp;&nbsp;<a href=messages.php?action=viewmailbox&box=-1><img height=14px style=border:none alt=sentbox title=sentbox src=pic/pn_sentbox2.gif></a> 0</span>");
}
print("&nbsp;<a href=friends.php><img height=12px style=border:none alt=Buddylist title=Buddylist src=pic/buddylist.gif></a>");
print("&nbsp;<a href=users.php><img height=12px style=border:none alt=Buddylist title=Userlist src=pic/buddylist1.gif></a>");
print("&nbsp;<a href=\"rss.php?userid=" . $CURUSER[id] . "\"><img height=12px src=\"pic/rss.png\" alt=\"Subscribe to the site RSS feed\" title=\"RSS Feed\" border=\"none\"></a>");

?>
                  </span></td>
              </tr>
            </table>
      </table>
      <p>
        <? } else { ?>
        <?

if (isset($returnto))
print("<input type=\"hidden\" name=\"returnto\" value=\"" . safeChar($returnto) . "\" />\n");

?>
        </center>
  </table>
  </tr>
  </table>
<p>
<? }?>
<?php
if (isset($unread) && !empty($unread))
{
  print("<table border=0 cellspacing=5 cellpadding=1 bgcolor=red  width='150' height='85'><tr><td class='cHs3'style=\"padding: 5px; background-image: url(pic/moonie.gif)\">\n");
  print("<b><a href=$BASEURL/messages.php?action=viewmailbox><font color=black>You have $unread new message" . ($unread > 1 ? "s" : "") . " !<br /><br /><br /><br /></font></a></b>");
  print("</td></tr></table></p>\n");
}

//happy hour
if ($CURUSER){
if (happyHour("check")) {
print("<table border=0 cellspacing=0 cellpadding=10  ><tr><td align=center style=\"background:#CCCCCC;color:#222222; padding:10px\">\n");
print("<b>Hey its now happy hour ! ".((happyCheck("check") == 255) ? "Every torrent downloaded in the happy hour is free" : "Only <a href=\"browse.php?cat=".happyCheck("check")."\">this category</a> is free this happy hour" )."<br/><font color=red>".happyHour("time")." </font> remaining from this happy hour!</b>");
print("</td></tr></table>\n");
}
}
//=== report link for big red box thanks carphunter18 :)
if (get_user_class() >= UC_MODERATOR) {
$res_reports = mysql_query("SELECT COUNT(*) FROM reports WHERE delt_with = '0'");
$arr_reports = mysql_fetch_row($res_reports);
$num_reports = $arr_reports[0];
if ($num_reports > 0)
echo"<p><table border=0 cellspacing=0 cellpadding=10 bgcolor=red><tr><td style='padding: 10px; background: #A60A15' align=center><b>Hey $CURUSER[username]! $num_reports Report" . ($num_reports > 1 ? "s" : "") . " to be dealt with<br>click <a href=reports.php>HERE</a> to view reports</b></td></tr></table></p>\n";
}
//=== help desk message
if (get_user_class() >= UC_MODERATOR){
$resa = mysql_query("select count(id) as problems from helpdesk WHERE solved = 'no'");
$arra = mysql_fetch_assoc($resa);
$problems = $arra['problems'];
if ($problems > 0)
echo("<p><table border=0 cellspacing=0 cellpadding=10 bgcolor=red><tr><td style='padding: 10px; background: #A60A15'>\n".
"Hi <b>$CURUSER[username]</b>, there ".($problems == 1 ? 'is' : 'are')." <b>$problems question".($problems == 1 ? '' : 's')."</b> at the help desk that needs a reply.<br>please click <b><a href=$BASEURL/helpdesk.php?action=problems>HERE</a></b> to deal with it.".
"</td></tr></table></p>\n");
}
//////////////running at a lower class/////////
if ($CURUSER['override_class'] != 255 && $CURUSER) // Second condition needed so that this box isn't displayed for non members/logged out members.
  {
      print("<table border=0 cellspacing=0 cellpadding=10 bgcolor=red><tr><td  style=\"padding: 10px; background-image: url(pic/back_newpm.gif)\">\n");
      print("<b><a href=restoreclass.php><font color=black>You are running under a lower class. Click here to restore.</font></a></b>");
      print("</td></tr></table></p>\n");
  }
if ($CURUSER && $CURUSER['country']==0)  {
print("<table border=0 cellspacing=0 cellpadding=10 bgcolor=red><tr><td style=\"padding: 10px; background-image: url(pic/back_newpm.gif)\">\n");
print("<b><a href=\"usercp.php\"><font color=black>Please choose your country in your profile !</font></a></b>");
print("</td></tr></table></p>\n");
}
//=== free download???
if ($CURUSER){
$resfree = sql_query("SELECT * FROM free_download");
$arrfree = mysql_fetch_assoc($resfree);
$free_for_all = $arrfree["free_for_all"] == 'yes';
if ($free_for_all){
$title = unesc($arrfree["title"]);
$message = format_comment($arrfree["message"]);
?>
<table width=400>
  <tr>
    <td class=colhead colspan=3 align=center><?=$title?></td>
  </tr>
  <tr>
    <td width=42 align=center valign=center><img src=pic/cat_free.gif alt=FREE!></td>
    <td><?=$message?></td>
    <td width=42 align=center valign=center><img src=pic/cat_free.gif alt=FREE!></td>
  </tr>
</table>
<br>
<?
}
}
//===end

if ($komment){
print("<table border=0 cellspacing=0 cellpadding=10 bgcolor=red><tr><td style=\"padding: 10px; background:lightgreen\">\n");
 print("<font color=black>Please leave a comment on:<br>$komment</font>");
 print("</td></tr></table></p>\n");
}

 // Announcement Code...
      $ann_subject = trim($CURUSER['curr_ann_subject']);
      $ann_body = trim($CURUSER['curr_ann_body']);

      if ((!empty($ann_subject)) AND (!empty($ann_body)))
      {
          ?>
<!-- <table width=756 class=main border=0 cellspacing=0 cellpadding=0><tr><td class=embedded> -->
<p>
<table width=760 border=1 cellspacing=0 cellpadding=5>
  <tr>
    <td bgcolor=#466248><b><font color=white>Announcement: <?php print(safe($ann_subject));?></font></b></td>
  </tr>
  <tr>
    <td style='padding: 10px; background: #FFFFFF'><?php print(format_comment($ann_body));?> <br />
      <hr />
      <br />
      Click <a href=<?php print(safe($DEFAULTBASEURL))?>/clear_announcement.php> <i><b>here</b></i></a> to clear this announcement.</td>
  </tr>
</table>
</p>
<?php
      }
if ($CURUSER["tenpercent"] == "no") {
?>
<script language=javascript>
function enablesubmit() {
document.tenpercent.submit.disabled = document.tenpercent.submit.checked;
}
function disablesubmit() {
document.tenpercent.submit.disabled = !document.tenpercent.submit.checked;
}
</script>
<?
}
//=== shoutbox
if ($CURUSER['show_shout'] === "yes") {
?>
<script language=javascript>
function SmileIT(smile,form,text){
document.forms[form].elements[text].value = document.forms[form].elements[text].value+" "+smile+" ";
document.forms[form].elements[text].focus();
}
function PopMoreSmiles(form,name) {
link='moresmiles.php?form='+form+'&text='+name
newWin=window.open(link,'moresmile','height=500,width=450,resizable=no,scrollbars=yes');
if (window.focus) {newWin.focus()}
}
function PopCustomSmiles(form,name) {
link='moresmilies_custom.php?form='+form+'&text='+name
newWin=window.open(link,'moresmile','height=600,width=400,resizable=yes,scrollbars=yes');
if (window.focus) {newWin.focus()}
}
</script>
<script LANGUAGE="JavaScript"><!--
function mySubmit() 
setTimeout('document.shbox.reset()',100);
}
//--></SCRIPT>
<table width='758' border='0' cellspacing='0' cellpadding='1'>
  <tr>
    <td class=colhead><h2>
        <center>
          ShoutBox [ <a href=shoutbox.php?show_shout=1&show=no>close</a> ]
        </center>
      </h2></td>
  </tr>
  <tr>
  <td >
  
  <iframe src='shoutbox.php' width='756' height='200' frameborder='0' name='sbox' marginwidth='0' marginheight='0'></iframe>
  <br>
  <br>
  <form action='shoutbox.php' method='get' target='sbox' name='shbox' onSubmit="mySubmit()">
  
  <center>
    <b>Shout!:</b>
    <input type='text' maxlength=180 name='shbox_text' size='100'>
  </center>
  <center>
    <input class=button type='submit' value='Send'>
    <input type='hidden' name='sent' value='yes'>
  </center>
  <br />
  <center>
    <a href="javascript: SmileIT(':-)','shbox','shbox_text')"><img border=0 src=pic/smilies/smile1.gif></a> <a href="javascript: SmileIT(':smile:','shbox','shbox_text')"><img border=0 src=pic/smilies/smile2.gif></a> <a href="javascript: SmileIT(':-D','shbox','shbox_text')"><img border=0 src=pic/smilies/grin.gif></a> <a href="javascript: SmileIT(':lol:','shbox','shbox_text')"><img border=0 src=pic/smilies/laugh.gif></a> <a href="javascript: SmileIT(':w00t:','shbox','shbox_text')"><img border=0 src=pic/smilies/w00t.gif></a> <a href="javascript: SmileIT(':blum:','shbox','shbox_text')"><img border=0 src=pic/smilies/blum.gif></a> <a href="javascript: SmileIT(';-)','shbox','shbox_text')"><img border=0 src=pic/smilies/wink.gif></a> <a href="javascript: SmileIT(':devil:','shbox','shbox_text')"><img border=0 src=pic/smilies/devil.gif></a> <a href="javascript: SmileIT(':yawn:','shbox','shbox_text')"><img border=0 src=pic/smilies/yawn.gif></a> <a href="javascript: SmileIT(':-/','shbox','shbox_text')"><img border=0 src=pic/smilies/confused.gif></a> <a href="javascript: SmileIT(':o)','shbox','shbox_text')"><img border=0 src=pic/smilies/clown.gif></a> <a href="javascript: SmileIT(':innocent:','shbox','shbox_text')"><img border=0 src=pic/smilies/innocent.gif></a> <a href="javascript: SmileIT(':whistle:','shbox','shbox_text')"><img border=0 src=pic/smilies/whistle.gif></a> <a href="javascript: SmileIT(':unsure:','shbox','shbox_text')"><img border=0 src=pic/smilies/unsure.gif></a> <a href="javascript: SmileIT(':blush:','shbox','shbox_text')"><img border=0 src=pic/smilies/blush.gif></a> <a href="javascript: SmileIT(':hmm:','shbox','shbox_text')"><img border=0 src=pic/smilies/hmm.gif></a> <a href="javascript: SmileIT(':hmmm:','shbox','shbox_text')"><img border=0 src=pic/smilies/hmmm.gif></a> <a href="javascript: SmileIT(':huh:','shbox','shbox_text')"><img border=0 src=pic/smilies/huh.gif></a> <a href="javascript: SmileIT(':look:','shbox','shbox_text')"><img border=0 src=pic/smilies/look.gif></a> <a href="javascript: SmileIT(':rolleyes:','shbox','shbox_text')"><img border=0 src=pic/smilies/rolleyes.gif></a> <a href="javascript: SmileIT(':kiss:','shbox','shbox_text')"><img border=0 src=pic/smilies/kiss.gif></a> <a href="javascript: SmileIT(':blink:','shbox','shbox_text')"><img border=0 src=pic/smilies/blink.gif></a> <a href="javascript: SmileIT(':baby:','shbox','shbox_text')"><img border=0 src=pic/smilies/baby.gif></a> <a href="javascript: SmileIT(':\'-(','shbox','shbox_text')"><img border=0 src=pic/smilies/cry.gif></a> <br>
    [ <a href='shoutbox.php' target='sbox'>Refresh</a> ]
  </center>
  <p>
    <center>
      <a href="javascript: PopMoreSmiles('shbox','shbox_text')"><font color=red>
[ More Smilies ]
      <font></a>
    </center>
    <br />
  </td>
  </tr>
</table>
</form>
<?
}//===endshout
print("
<table background='pic/backcen.gif'>
");
