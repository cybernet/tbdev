<?php
require_once("include/bittorrent.php");
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
//=== added turn on / off shoutbox
if ((isset($_GET['show_shout'])) && (($show_shout = $_GET['show']) !== $CURUSER['show_shout'])){
sql_query("UPDATE users SET show_shout = ".sqlesc($_GET['show'])." WHERE id = $CURUSER[id]");
header("Location: ".$_SERVER['HTTP_REFERER']);
}
unset($insert);
$insert=false;

// DELETE SHOUT 
if (isset($_GET['del']) && get_user_class() >= UC_MODERATOR && is_valid_id($_GET['del']))
	mysql_query("DELETE FROM shoutbox WHERE id=".sqlesc($_GET['del']));
// Empty shout - coder/owner
if (isset($_GET['delall']) && get_user_class() >= UC_SYSOP)
	$query = "TRUNCATE TABLE shoutbox";
    mysql_query($query);
// Edit shout 
if (isset($_GET['edit']) && get_user_class() >= UC_MODERATOR && is_valid_id($_GET['edit']))
{	
	$sql=sql_query("SELECT id,text FROM shoutbox WHERE id=".sqlesc($_GET['edit']));
	$res=mysql_fetch_array($sql);
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="expires" content="0">
	<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<style type="text/css">
	#specialbox{
	border: 1px solid gray;
	width: 600px;
	background: #FBFCFA;
	font: 11px verdana, sans-serif;
	color: #000000;
	padding: 3px;	outline: none;
	}

	#specialbox:focus{
	border: 1px solid black;
	}
	.btn {
	cursor:pointer;
	border:outset 1px #ccc;
	background:#999;
	color:#666;
	font-weight:bold;
	padding: 1px 2px;
	background: #000000 repeat-x left top;
	}	
	</style>
	</head>
	<body bgcolor=#F5F4EA class="date">
	<?php
	echo '<form method=post action=shoutbox.php>';
	echo '<input type=hidden name=id value='.(int)$res['id'].'>';
	echo '<textarea name=text rows=3 id=specialbox>'.safechar($res['text']).'</textarea>';
	echo '<input type=submit name=save value=save class=btn>';
	echo '</form></body></html>';
	die;
}

// UPDATE SHOUT?
if (isset($_POST['text']) && get_user_class() >= UC_MODERATOR && is_valid_id($_POST['id']))
{
	$text = trim($_POST['text']);
	$id = (int)$_POST['id'];
	if (isset($text) && isset($id) && is_valid_id($id))
		sql_query("UPDATE shoutbox SET text = ".sqlesc($text)." WHERE id=".sqlesc($id));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>ShoutBox</title>
<META HTTP-EQUIV=REFRESH CONTENT="60; URL=shoutbox.php">
<style type="text/css" class="error">
A {color: #356AA0; font-weight: bold; font-size: 9pt; }
A:hover {color: #FF0000;}
.small {color: #000000; font-size: 9pt; font-family: arial; }
.date {color: #000000; font-size: 9pt;}
.error {
	color: #990000;
	background-color: #FFF0F0;
	padding: 7px;
	margin-top: 5px;
	margin-bottom: 10px;
	border: 1px dashed #990000;
}
<?

// default Theme
if ($CURUSER['shoutboxbg'] == "1") {
?>
<style type="text/css">
A {color: #000000; font-weight: bold; }
A:hover {color: #FF273D;}
.small {font-size: 10pt; font-family: arial; }
.date {font-size: 8pt;}
</style>
<?
$bg = 'bgcolor=#ffffff';
$fontcolor = '#000000';
$dtcolor = '#356AA0';

}

// large text Theme
if ($CURUSER['shoutboxbg'] == "2") {
?>
<style type="text/css">
A {color: #ffffff; font-weight: bold; }
A:hover {color: #FF273D;}
.small {font-size: 10pt; font-family: arial; }
.date {font-size: 8pt;}
</style>
<?
$bg = 'bgcolor=#777777';
$fontcolor = '#000000';
$dtcolor = '#356AA0';

}

// Klima Theme
if ($CURUSER['shoutboxbg'] == "3") {
?>
<style type="text/css">
A {color: #FFFFFF; font-weight: bold; }
A:hover {color: #FFFFFF;}
.small {font-size: 10pt; font-family: arial; }
.date {font-size: 8pt;}
</style>
<?
$bg = 'bgcolor=#000000';
$fontcolor = '#FFFFFF';
$dtcolor = '#FFFFFF';
}


if ($CURUSER["chatpost"] == 'no')
{
	print("<div class=error align=center><br>Sorry, you are not authorized to Shout.  (<a href=\"rules.php\" target=\"_blank\">Contact Site Admin For Reason Why</a>)<br><br></div>");
	exit;
}

if($_GET["sent"]=="yes")
	if(!$_GET["shbox_text"])
	{
		$userid=0+$CURUSER["id"];
	}
else
{
	$userid=0+$CURUSER["id"];
	
if (get_user_class() < UC_USER) {
   if (strtotime($CURUSER['last_shout']) > (strtotime($CURUSER['ctime']) - 30))
   {
       $secs = 30 - (strtotime($CURUSER['ctime']) - strtotime($CURUSER['last_shout']));
       print("<div class=error align=center><b>Sorry, shout flooding not allowed. Please wait [$secs] second".($secs == 1 ? '' : 's')." before making another shout.</div></b>");
       $insert = false;       
   }else
	   $insert = true;
}else
   	$insert=true;

	$username=safechar(trim($CURUSER["username"]));
	$date=time();
	$text=trim($_GET["shbox_text"]);
	if ($insert) {
		mysql_query("INSERT INTO shoutbox (id, userid, username, date, text) VALUES ('id'," . sqlesc($userid) . ", " . sqlesc($username) . ", " . sqlesc($date) . ", " . sqlesc($text) . ")") or sqlerr(__FILE__, __LINE__);		
		print "<script type=\"text/javascript\">parent.document.forms[0].shbox_text.value='';</script>";		
	}
}

$res = sql_query("SELECT * FROM shoutbox ORDER BY date DESC LIMIT 30") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 0)
	print("\n");
else
{
	print("<table border=0 cellspacing=0 cellpadding=2 width='100%' align='left' class='small'>\n");

	
	while ($arr = mysql_fetch_assoc($res))
	{
		$res2 = sql_query("SELECT username,class,donor,warned,downloadpos,chatpost,forumpost,uploadpos,parked FROM users WHERE id=$arr[userid]") or sqlerr(__FILE__, __LINE__);
		$arr2 = mysql_fetch_array($res2);
		$resowner = sql_query("SELECT id, username, class FROM users WHERE id=$arr[userid]") or print(mysql_error());
		$rowowner = mysql_fetch_array($resowner); 
        
    
if ($rowowner["class"] == "7")
$usercolor= " <font color='#".get_user_class_color($rowowner['class'])."'>".safechar($rowowner['username'])."</font>";
if ($rowowner["class"] == "6")
$usercolor= " <font color='#".get_user_class_color($rowowner['class'])."'>".safechar($rowowner['username'])."</font>";
if ($rowowner["class"] == "5")
$usercolor= " <font color='#".get_user_class_color($rowowner['class'])."'>".safechar($rowowner['username'])."</font>";
if ($rowowner["class"] == "4")
$usercolor= " <font color='#".get_user_class_color($rowowner['class'])."'>".safechar($rowowner['username'])."</font>";
if ($rowowner["class"] == "3")
$usercolor= " <font color='#".get_user_class_color($rowowner['class'])."'>".safechar($rowowner['username'])."</font>";
if ($rowowner["class"] == "2")
$usercolor= " <font color='#".get_user_class_color($rowowner['class'])."'>".safechar($rowowner['username'])."</font>";
if ($rowowner["class"] == "1")
$usercolor= " <font color='#".get_user_class_color($rowowner['class'])."'>".safechar($rowowner['username'])."</font>";
if ($rowowner["class"] == "0")
$usercolor= " <font color='#".get_user_class_color($rowowner['class'])."'>".safechar($rowowner['username'])."</font>";
			
			$edit= (get_user_class() >= UC_MODERATOR ? "<a href=/shoutbox.php?edit=".$arr['id']."><img src=".$pic_base_url."button_edit2.gif border=0 title=\"Edit Shout\"/></a> " : "");
		    $del= (get_user_class() >= UC_MODERATOR ? "<a href=/shoutbox.php?del=".$arr['id']."><img src=".$pic_base_url."button_delete2.gif border=0 title=\"Delete Single Shout\"/></a> " : "");
		    $delall= (get_user_class() >= UC_SYSOP ? "<a href=/shoutbox.php?delall><img src=".$pic_base_url."button_delete2.gif border=0 title=\"Empty Shout\"/></a> " : "");
		    $pm = "<span class='date' color=$dtcolor><a target=_blank href=sendmessage.php?receiver=$arr[userid]><img src=".$pic_base_url."button_pm2.gif border=0 title=\"Pm User\"/></a></span>\n";

         print("<tr $bg><td><span class='date'><b><font color=$fontcolor>[".strftime("%d.%m %H:%M",$arr["date"])."]</font></b></span>\n$del $delall $edit $pm <a href='userdetails.php?id=".$arr["userid"]."' target='_blank'>$usercolor</a>\n" .
		($arr2["donor"] == "yes" ? "<img src=pic/star.gif alt='DONOR'>\n" : "") .
		($arr2["warned"] == "yes" ? "<img src="."pic/warned.gif alt='Warned'>\n" : "") .
		($arr2["chatpost"] == "no" ? "<img src=pic/chatpos.gif alt='No Chat'>\n" : "") .
        ($arr2["downloadpos"] == "no" ? "<img src=pic/downloadpos.gif alt='No Downloads'>\n" : "") .
        ($arr2["forumpost"] == "no" ? "<img src=pic/forumpost.gif alt='No Posting'>\n" : "") .
        ($arr2["uploadpos"] == "no" ? "<img src=pic/uploadpos.gif alt='No upload'>\n" : "") .
        ($arr2["parked"] == "yes" ? "<img src=pic/parked.gif alt='Account Parked'>\n" : "") ."<font color=$fontcolor> ".format_comment($arr["text"])."\n</font></td></tr>\n");
	    
	}
	print("</table>");
}
?>
</body>
</html>
