<?php
require_once("include/bittorrent.php");	
require_once "include/user_functions.php";
require_once ("include/bbcode_functions.php");
dbconn(false); 
maxcoder(); 
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
parked();		 	

function bark($msg) { 
stdhead("Requests Error");
stdmsg("Error!", $msg);
 stdfoot();
 exit;
}

if ($_GET["category"]){
$categ = isset($_GET['category']) ? (int)$_GET['category'] : 0;
if(!is_valid_id($categ))
stderr("Error", "I smell a rat!");
}

if ($_GET["requestorid"]){
$requestorid = 0 + htmlentities($_GET["requestorid"]); 
if (ereg("^[0-9]+$", !$requestorid))
stderr("Error", "I smell a rat!");  
}  

if ($_GET["id"]){
$id = 0 + htmlentities($_GET["id"]); 
if (ereg("^[0-9]+$", !$id))
stderr("Error", "I smell a rat!");  
}

//==== add request
if ($_GET["add_request"]){	

$add_request = 0 + $_GET["add_request"];
if($add_request != '1')
stderr("Error", "I smell a rat!");  

stdhead("Requests Page");

if (get_user_class() < UC_POWER_USER)	 //=== requests for power users and above
{
begin_frame("Sorry",true);
 print("<h1>Oops!</h1><p>You must be Power User or above <b>AND</b> have a ratio above <b>0.5</b> to make a request.<br><br> Please see the ".
 "<a href=faq.php><b>FAQ</b></a> for more information on different user classes and what they can do.<br><br><b></p>" .$SITENAME." staff</b>");
die();
}

//=== only allow users with a ratio of at least .5 who have uploaded at least 10 gigs or VIP and above
if ($CURUSER)
{
  // ratio as a string
	function format_ratio($up,$down, $color = True)
	{
		if ($down > 0)
		{
			$r = number_format($up / $down, 2);
    	if ($color)
				$r = "<font color=".get_ratio_color($r).">$r</font>";
		}
		else
			if ($up > 0)
	  		$r = "'Inf.'";
	  	else
	  		$r = "'---'";
		return $r;
	}

	if ($CURUSER["class"] < UC_VIP)
	{
	$gigsdowned = ($CURUSER["downloaded"]);
	if ($gigsdowned >= 10737418240){
	  $gigs = $CURUSER["uploaded"] / (1024*1024*1024);
	  $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
	  }
	}	  
//=== use this if you are using the Karma point system	
begin_frame("Request Rules",true);
print("To make a request you must have a ratio of at least<b> 0.5</b> AND have uploaded at least <b>10 GB</b>.<br>".
" A request will also cost you <b><a class=altlink href=mybonus.php>5 Karma Points</a></b>....<br><br> In your particular case ".
"<a class=altlink href=userdetails.php?id=" . $CURUSER['id'] . ">" . $CURUSER['username'] . "</a>, ");	
/*
//=== use this if you are NOT using the Karma point system	
begin_frame("Request Rules",true);
 	print("To make a request you must have a ratio of at least<b> 0.5</b> AND have uploaded at least <b>10 GB</b>.<br><br> ".
	"In your particular case <a class=altlink href=userdetails.php?id=" . $CURUSER['id'] . ">" . $CURUSER['username'] . "</a>, ");	
*/	
$gigsupped = ($CURUSER["uploaded"]);
$ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0); 

//===karma	  

if ($CURUSER["seedbonus"] <5.0)
	  print("you do not have enough <a class=altlink href=mybonus.php>Karma Points</a> ...".
	  " you can not make requests.<p>To view all requests, click <a class=altlink href=viewrequests.php><b>here</b></a></p>\n<br><br>");


//=== if you are using the karma mod change this next line too
//elseif ($gigsupped < 10737418240)
if ($gigsupped < 10737418240)
	  print("you have <b>not</b> yet uploaded <b>10 GB</b>... you can not make requests.<p>".
	  "To view all requests, click <a class=altlink href=viewrequests.php><b>here</b></a></p>\n<br><br>");
elseif ($ratio < 0.5){
	$byboth = $byratio && $byul;
	    print(
	      ($byboth ? "both " : "") .
	      ($byratio ? "your ratio of <b>" . format_ratio($CURUSER["uploaded"],$CURUSER["downloaded"]) : "</b>") .
	      ($byboth ? " and " : "") .
	      ($byul ? "your total uploaded of<b> " . round($gigs,2) . " GB</b>" : "") . "" .
	      ($byboth ? "" : "") . " We see that you have <b>not</b> met the minimum requirements." .
	      ($byboth ? "" : " (because your " . ($byratio ? "total uploaded is " . round($gigs,2) . " GB" : "ratio is <b>" . format_ratio($CURUSER["uploaded"],$CURUSER["downloaded"])) . "</b>.)<br><br><p>To view all requests, click <a href=viewrequests.php><b>here</b></a></p>\n<br><br>"));
	}
else
	{
print("you <b>can</b> make requests.<p>To view all requests, click <a class=altlink href=viewrequests.php>here</a></p>\n");

//===end check

print("<table border=1 width=800 cellspacing=0 cellpadding=5><tr><td class=colhead align=left>".
"Please search torrents before adding a request!</td></tr><tr><td align=left class=clearalt6><form method=get action=browse.php>".
"<input type=text name=search size=40 value=\"".safechar($searchstr)."\" />in <select name=cat> <option value=0>(all types)</option>");

$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
   $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
   if ($cat["id"] == $_GET["cat"])
   $catdropdown .= " selected=\"selected\"";
   $catdropdown .= ">" . safechar($cat["name"]) . "</option>\n";
}

$deadchkbox = "<input type=\"checkbox\" name=\"incldead\" value=\"1\"";
if ($_GET["incldead"])
$deadchkbox .= " checked=\"checked\"";
$deadchkbox .= " /> including dead torrents\n";
print(" ".$catdropdown." </select> ".$deadchkbox." <input type=submit value=Search! class=button /></form></td></tr></table><br>\n");

print("<form method=post name=compose action=". $_SERVER[PHP_SELF] ."?new_request=1><a name=add id=add></a>".
"<table border=1 width=800 cellspacing=0 cellpadding=5><tr><td class=colhead align=left colspan=2>".
"Requests are for Users with a good ratio who have uploaded at least 10 gigs Only... Share and you shall recieve!</td></tr>".
"<tr><td align=right class=clearalt6><b>Title:</b></td><td align=left class=clearalt6><input type=text size=40 name=requesttitle>".
"<select name=category><option value=0>(Select a Category)</option>\n");

$res2 = mysql_query("SELECT id, name FROM categories  order by name");
$num = mysql_num_rows($res2);
$catdropdown2 = "";
for ($i = 0; $i < $num; ++$i)
   {
 $cats2 = mysql_fetch_assoc($res2);  
 $catdropdown2 .= "<option value=\"" . $cats2["id"] . "\"";
 $catdropdown2 .= ">" . safechar($cats2["name"]) . "</option>\n";
   }
   
print("".$catdropdown2." </select><br><tr><td align=right class=clearalt6 valign=top><b>Image:</b></td><td align=left class=clearalt6>".
"<input type=text name=picture size=80><br>(Direct link to image, NO TAGS NEEDED! Will be shown in description)</td></tr>".
"<tr><td align=right class=clearalt6><b>Description:</b></td><td align=left class=clearalt6>\n");
print("<textarea name=body rows=10 cols=60></textarea></p>\n");
print("</td></tr><tr><td align=center  class=clearalt6 colspan=2><input type=submit value='Okay' class=button></td></tr></form><br><br></table><br>\n");
}
}

$res = mysql_query("SELECT users.username, requests.id, requests.userid, requests.request, requests.added, uploaded, downloaded, categories.image, categories.name as cat FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id order by requests.id desc LIMIT 10") or sqlerr();
$num = mysql_num_rows($res);

print("<table border=1 width=800 cellspacing=0 cellpadding=5><tr><td class=colhead align=left width=50>Category</td>".
"<td class=colhead align=left width=425>Request</td><td class=colhead align=center>Added</td>".
"<td class=colhead align=center width=125>Requested By</td></tr>\n");
for ($i = 0; $i < $num; ++$i)
{ 
//=======change colors
		if($count == 0)
{
$count = $count+1;
$class = "clearalt6";
}
else
{
$count = 0;
$class = "clearalt7";
}
		//=======end
 $arr = mysql_fetch_assoc($res);
 {
$addedby = "<td style='padding: 0px' align=center class=$class><b><a href=userdetails.php?id=$arr[userid]>$arr[username]</a></b></td>";
 }

 print("<tr><td align=center class=$class><img src=pic/$arr[image]></td><td align=left class=$class><a href=viewrequests.php?id=$arr[id]&req_details=1><b>$arr[request]</b></a></td>" .
 "<td align=center class=$class>$arr[added]</td>".
   "$addedby</tr>\n");
}
print("<tr><td align=center colspan=4 class=clearalt6><form method=\"get\" action=viewrequests.php>".
"<input type=\"submit\" value=\"Show All\" class=button /></form></td></tr></table>\n");

stdfoot();
die;
}
//=== end requests

//=== take new request 
if ($_GET["new_request"]){	

$new_request = 0 + $_GET["new_request"];
if($new_request != '1')
stderr("Error", "I smell a rat!");

$userid = 0 + $CURUSER["id"];
if (ereg("^[0-9]+$", !$userid))
stderr("Error", "I smell a rat!");

$request = htmlentities($_POST["requesttitle"]);
if ($request == "")
 bark("You must enter a title!");	
	   
$cat = (0 + $_POST["category"]);
if (!is_valid_id($cat))
 bark("You must select a category to put the request in!");	   

$descrmain = unesc($_POST["body"]);
if (!$descrmain)
 bark("You must enter a description!");	

if (!empty($_POST['picture'])){
$picture = unesc($_POST["picture"]);
if(!preg_match("/^http:\/\/[^\s'\"<>]+\.(jpg|gif|png)$/i", $picture))
stderr("Error", "Image MUST be in jpg, gif or png format.");
$pic = "[img]".$picture."[/img]\n";
}
$descr = "$pic";
$descr .= "$descrmain";

$userid = sqlesc($userid);
$request2 = sqlesc($request);
$descr = sqlesc($descr);
$cat = sqlesc($cat);

mysql_query("INSERT INTO requests (hits,userid, cat, request, descr, added) VALUES(1,$CURUSER[id], $cat, $request2, $descr, '" . get_date_time() . "')") or sqlerr(__FILE__,__LINE__);
$id = mysql_insert_id();
@mysql_query("INSERT INTO addedrequests VALUES(0, $id, $CURUSER[id])") or sqlerr();

//===add karma 	 
mysql_query("UPDATE users SET seedbonus = seedbonus-5.0 WHERE id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);
//===end	
   
write_log("Request ($request) was added to the Request section by $CURUSER[username]");

header("Refresh: 0; url=viewrequests.php?id=$id&req_details=1");
} 
//===end take new request 

//=== request details 
if ($_GET["req_details"]){

$req_details = 0 + $_GET["req_details"];
if($req_details != '1')
stderr("Error", "I smell a rat!");

$id = 0+$_GET["id"]; 

stdhead("Request Details");

$res = mysql_query("SELECT *,UNIX_TIMESTAMP(added) as utadded FROM requests WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$num = mysql_fetch_array($res);	 

//$timezone = display_date_time($num["utadded"] , $CURUSER[tzoffset] );	 
$timezone = get_date_time($num["utadded"]);	

$s = $num["request"];

begin_frame("Details Of Request: $s",true);
print("<table width=\"80%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr><td align=center colspan=2 class=colhead>".
"<font size=\"+2\"><b>$s</b></font></td></tr>");
if ($num["descr"]){
$req_bb = format_comment($num["descr"]);
print("<tr><td align=left colspan=2 class=clearalt7 valign=top>$req_bb</td></tr>");
}
print("<tr><td align=right class=clearalt6><b>Added:</b></td><td align=left class=clearalt6>$timezone</td></tr>");

$cres = mysql_query("SELECT username FROM users WHERE id=$num[userid]");
   if (mysql_num_rows($cres) == 1)
   {
     $carr = mysql_fetch_assoc($cres);
     $username = "$carr[username]";
   }
   
if ($CURUSER[id] == $num[userid] || get_user_class() >= UC_MODERATOR){
$edit = "[ <a class=altlink href=". $_SERVER[PHP_SELF] ."?id=$id&edit_request=1>Edit Request</a> ]";
$delete = "[ <a class=altlink href=". $_SERVER[PHP_SELF] ."?id=$id&del_req=1&sure=0>Delete Request</a> ]"; 
if ($num["filled"] == yes)
$reset = "[ <a class=altlink href=". $_SERVER[PHP_SELF] ."?id=$id&req_reset=1>Re-set Request</a> ]";
} 

//=== chances are you have some sort of "report" function in your site... 
//=== if so, use the below bit and adjust it to work with your report script...

print("<tr><td align=right class=clearalt6><b>Requested&nbsp;By:</b></td><td align=left class=clearalt6>".
"<a class=altlink href=userdetails.php?id=$num[userid]>$username</a>  $edit  $delete </td></tr><tr><td align=right class=clearalt6>".
"<b>Vote for this request:</b></td><td align=left class=clearalt6><a href=". $_SERVER[PHP_SELF] ."?id=$id&req_vote=1><b>Vote</b></a>".
"</td></tr><tr><td align=right class=clearalt6><b>Report Request:</b></td><td align=left class=clearalt6>".
"<form action=report.php?type=Request&id=$id method=post> for breaking the rules <input class=button type=submit name=submit value=\"Report Request\"></form></td></tr>"); 


	

if ($num["filled"] == no)
{
print("<form method=post action=". $_SERVER[PHP_SELF] ."?requestid=$id&req_filled=1><tr><td align=right class=clearalt6 valign=top><b>Fill This Request:</b></td>".
"<td class=clearalt6><input type=text size=80 name=filledurl value=''><br>".
"Enter the <b>full</b> URL of the torrent i.e. <b>$BASEURL/details.php?id=</b> <br>[ just copy/paste from another window/tab or modify the existing URL to have the correct ID number ]</td>".
"</tr></table><input type=submit value=\"Fill Request\" class=button></form>\n");
}
if ($num["filled"] == yes)
print("<tr><td align=right class=clearalt6 valign=top><b>This Request was filled:</b></td><td class=clearalt6><a class=altlink href=$num[filledurl]><b>$num[filledurl]</b></a></td></tr></table>");	

//--- added comments
function reqcommenttable($rows)
{
       global $CURUSER, $HTTP_SERVER_VARS;
       begin_main_frame();
       begin_frame();
       $count = 0;
 
       foreach ($rows as $row)
       {	
//=======change colors
		if($count2 == 0)
{
$count2 = $count2+1;
$class = "clearalt6";
}
else
{
$count2 = 0;
$class = "clearalt7";
}	   
print("<br>");
		begin_table(true);
		print("<tr><td class=colhead colspan=2><p class=sub><a name=comment_" . $row["id"] . ">#" . $row["id"] . "</a> by: ");
   if (isset($row["username"]))
 {
 $username = $row["username"];
 $ratres = mysql_query("SELECT uploaded, downloaded from users where username='$username'");
       $rat = mysql_fetch_array($ratres);
 if ($rat["downloaded"] > 0)
{
$ratio = $rat['uploaded'] / $rat['downloaded'];
$ratio = number_format($ratio, 3);
$color = get_ratio_color($ratio);
if ($color)
$ratio = "<font color=$color>$ratio</font>";
}
else
if ($rat["uploaded"] > 0)
    $ratio = "Inf.";
else
$ratio = "---";
  
         $title = $row["title"];
         if ($title == "")
   $title = get_user_class_name($row["class"]);
         else
   $title = safechar($title);
       print("<a name=comm". $row["id"] .
               " href=userdetails.php?id=" . $row["user"] . "><b>" .
               safechar($row["username"]) . "</b></a>" . ($row["donor"] == "yes" ? "<img src=pic/star.gif alt='Donor'>" : "") . ($row["warned"] == "yes" ? "<img src=".
             "pic/warned.gif alt=\"Warned\">" : "") . "<font size=\"-3\"> ($title) (ratio: $ratio)\n");
 }
 else
 print("<a name=\"comm" . $row["id"] . "\"><i>(orphaned)</i></a>\n"); 
 
 //=== if using report mod uncomment the next bit and change to your report system 
 	
 print(" at " . $row["added"] . " GMT</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .																					
         ($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "[ <a href=reqcomment.php?action=edit&amp;cid=$row[id]>Edit</a> ]" : "") .																																																						
         (get_user_class() >= UC_MODERATOR ? "  [ <a href=reqcomment.php?action=delete&amp;cid=$row[id]>Delete</a> ] " : "") .
         ($row["editedby"] && get_user_class() >= UC_MODERATOR ? "" : "") . " [ <a href=userdetails.php?id=" . $row["user"] . ">Profile</a> ] [ <a href=sendmessage.php?receiver=" . $row["user"] . ">PM</a> ] [ <a href=report.php?reqcommentid=$row[id]>Report</a> ]</p>\n");
 
  
 $avatar = ($CURUSER["avatars"] == "yes" ? safechar($row["avatar"]) : "");
 if (!$avatar)
         $avatar = "pic/default_avatar.gif"; 
		 
 $text = format_comment($row["text"]);
   if ($row["editedby"])
$text .= "<p><font size=1 class=small>Edited by <a href=userdetails.php?id=$row[editedby]><b>$row[username]</b></a>  $row[editedat] GMT</font></p>\n";
print("</td></tr><tr valign=top><td align=center width=150 class=$class><img width=150 src=$avatar></td><td class=$class>$text</td></tr>\n");
end_table();
}
end_frame();
end_main_frame();
}
//=== end request comment

print("<tr><td class=embedded colspan=2><p><a name=startcomments></a></p>\n");

       $commentbar = "<p align=center><a class=index href=reqcomment.php?action=add&amp;tid=$id>Add Comment</a></p>\n";
       $subres = mysql_query("SELECT COUNT(*) FROM comments WHERE request = $id");
       $subrow = mysql_fetch_array($subres);
       $count = $subrow[0];
print("</td></tr></table>"); 

if (!$count)
print("<h2>No comments</h2>\n");
else {
 list($pagertop, $pagerbottom, $limit) = pager(20, $count, "viewrequests.php?id=$id&req_details=1&", array(lastpagedefault => 1));
$subres = mysql_query("SELECT comments.id, text, user, comments.added, editedby, editedat, avatar, warned, ".
                 "username, title, class, donor FROM comments LEFT JOIN users ON comments.user = users.id WHERE request = " .
                 "$id ORDER BY comments.id $limit") or sqlerr(__FILE__, __LINE__);
			 
 $allrows = array();
 while ($subrow = mysql_fetch_array($subres))
         $allrows[] = $subrow;

 print($commentbar);
 print($pagertop);
 reqcommenttable($allrows);
 print($pagerbottom);
}
 print($commentbar); 
 
end_frame(); 
die;  
}
//=== end request details 

//=== added edit request
if ($_GET["edit_request"]) {

$edit_request = 0 + $_GET["edit_request"];
if($edit_request != '1')
stderr("Error", "I smell a rat!");

$id = 0+$_GET["id"]; 

$res = mysql_query("SELECT *,UNIX_TIMESTAMP(added) as utadded FROM requests WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$num = mysql_fetch_array($res);	

//$timezone = display_date_time($num["utadded"] , $CURUSER[tzoffset] );	 //=== use this line if you have timezone mod
$timezone = get_date_time($num["utadded"]);

$s = $num["request"];
$id2 = $num["cat"];

if ($CURUSER["id"] != $num["userid"] && get_user_class() < UC_MODERATOR)
stderr("Error!", "This is not your Request to edit.");

$request = sqlesc($s);
$body = safechar(unesc($num["descr"])); 
$res2 = mysql_query("SELECT name FROM categories WHERE id=$id2")or sqlerr(__FILE__, __LINE__);
$num2 = mysql_fetch_array($res2);
$name = $num2["name"];
$s2 = "<select name=\"category\"><option value=$id2> $name </option>\n";

$cats = genrelist();

foreach ($cats as $row)
$s2 .= "<option value=\"" . $row["id"] . "\">" . safechar($row["name"]) . "</option>\n";
$s2 .= "</select>\n";	

stdhead("Edit Request");

print("<form method=post name=compose action=". $_SERVER[PHP_SELF] ."?id=$id&take_req_edit=1><a name=add id=add></a>".
"<table border=1 width=800 cellspacing=0 cellpadding=5><tr><td class=colhead align=left colspan=2><h1>Edit Request ".
"<img src=pic/arrow_next.gif alt=\":\"> $s</h1></td><tr><tr><td align=right class=clearalt6><b>Title:</b></td>".
"<td align=left class=clearalt6><input type=text size=40 name=requesttitle value=$request><b> Type:</b> $s2<br><tr>".
"<td align=right class=clearalt6 valign=top><b>Image:</b></td><td align=left class=clearalt6>".
"<input type=text name=picture size=80 value=''><br>(Direct link to image. NO TAG NEEDED! Will be shown in description)".
"<tr><td align=right class=clearalt6><b>Description:</b></td><td align=left class=clearalt6>\n");
print("<textarea name=body rows=10 cols=60></textarea></p>\n");
print("</td></tr>\n"); 
//=== if staff 
if (get_user_class() >= UC_MODERATOR){
print("<tr><td class=colhead align=left colspan=2>Staff only:</td></tr><tr><td align=right class=clearalt6><b>Filled:</b>".
"</td><td class=clearalt6><input type=checkbox name=filled" . ($num[filled]  == "yes" ? " checked" : "") . "></td></tr><tr>".
"<td align=right class=clearalt6><b>Filled by id:</b></td><td class=clearalt6>".
"<input type=text size=40 value=$num[filledby] name=filledby></td></tr><tr><td align=right class=clearalt6>".
"<b>Torrent url:</b></td><td class=clearalt6><input type=text size=80 name=filledurl value=$num[filledurl]></td></tr>");
}
//===end  if staff
print("<tr><td align=center  class=clearalt6 colspan=2><input type=submit value='Edit Request' class=button></td></tr></form><br><br></table><br>\n"); 

stdfoot(); 
die;
}  
//===end added edit request	

//==== take req edit
if ($_GET["take_req_edit"]){

$take_req_edit = 0 + $_GET["take_req_edit"];
if($take_req_edit != '1')
stderr("Error", "I smell a rat!");

$id = 0 + $_GET["id"]; 

$res = mysql_query("SELECT userid FROM requests WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$num = mysql_fetch_array($res);

if ($CURUSER["id"] != $num["userid"] && get_user_class() < UC_MODERATOR)
stderr("Error", "Access denied.");

$request = htmlentities($_POST["requesttitle"]);
if (!empty($_POST['picture'])){
$picture = unesc($_POST["picture"]);
if(!preg_match("/^http:\/\/[^\s'\"<>]+\.(jpg|gif|png)$/i", $picture))
stderr("Error", "Image MUST be in jpg, gif or png format.");
$pic = "[img]".$picture."[/img]\n";
}
$descr = "$pic";
$descr .= unesc($_POST["body"]);
if (!$descr)
  bark("You must enter a description!");
$cat = (0 + $_POST["category"]);
if (!is_valid_id($cat))
	bark("You must select a category to put the request in!");
	
$request = sqlesc($request);
$descr = sqlesc($descr);
$cat = sqlesc($cat);
$filledby = htmlentities( 0 + $_POST["filledby"]);
$filled = $_POST["filled"];
if ($filled)
{
if (!is_valid_id($filledby))
	bark("Not a valid id!");
$res = mysql_query("SELECT id FROM users WHERE id=".$filledby."");
if (mysql_num_rows($res) == 0)
       bark("ID doesn't match any users, try again");
	   
$filledurl = htmlentities($_POST['filledurl']);	
if(!preg_match("#^".preg_quote("$BASEURL/details.php?id=")."([0-9]{1,6})$#", $filledurl))
stderr("Error", "Something is wrong with that url.<br> URL <u>must</u> be: <b>$BASEURL/details.php?id=(torrent id)</b>"); 

if (!$filledurl)
	bark("No torrent url");
mysql_query("UPDATE requests SET cat=$cat, request=$request, descr=$descr, filledby=$filledby, filled ='yes', filledurl='$filledurl' WHERE id = $id") or sqlerr(__FILE__,__LINE__);
}
else
mysql_query("UPDATE requests SET cat=$cat, filledby = 0, request=$request, descr=$descr, filled = 'no' WHERE id = $id") or sqlerr(__FILE__,__LINE__);

header("Refresh: 0; url=viewrequests.php?id=$id&req_details=1");
}
//=== end take req edit	

//=== request filled 
if ($_GET["req_filled"]){ 

$req_filled = 0 + $_GET["req_filled"];
if($req_filled != '1')
stderr("Error", "I smell a rat!");	 

if ($_GET["requestid"]){
$requestid = 0 + htmlentities($_GET["requestid"]); 
if (ereg("^[0-9]+$", !$requestid))
stderr("Error", "I smell a rat!");  
}

$filledurl = htmlentities($_POST['filledurl']);	
if(!preg_match("#^".preg_quote("$BASEURL/details.php?id=")."([0-9]{1,6})$#", $filledurl))
stderr("Error", "Something is wrong with that url.<br> URL <u>must</u> be: <b>$BASEURL/details.php?id=(torrent id)</b>");

stdhead("Request Filled");

begin_main_frame();

$res = mysql_query("SELECT users.username, requests.userid, requests.filled, requests.request FROM requests inner join users on requests.userid = users.id where requests.id = $requestid") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res);

$res2 = mysql_query("SELECT username FROM users where id =" . $CURUSER[id]) or sqlerr(__FILE__, __LINE__);
$arr2 = mysql_fetch_assoc($res2);

if ($arr['filled']==no){
$msg = "Your request, [b]" . $arr[request] . "[/b] has been filled by [b]" . $arr2[username] . "[/b]. You can download your request from [b][url=" . $filledurl. "]" . $filledurl. "[/url][/b].  Please do not forget to leave thanks where due.  If for some reason this is not what you requested, please reset your request so someone else can fill it by following [b][url=$BASEURL/viewrequests.php?id=$requestid&req_reset=1]this[/url][/b] link.  Do [b]NOT[/b] follow this link unless you are sure that this does not match your request.";

mysql_query ("UPDATE requests SET filled = 'Yes', filledurl = '$filledurl', filledby = $CURUSER[id] WHERE id = $requestid") or sqlerr(__FILE__, __LINE__);

//=== remove the next query if you DON'T have subject in your PM system and use the other one
mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg, subject, location) VALUES(0, 0, $arr[userid], '" . get_date_time() . "', " . sqlesc($msg) . ", 'Request Filled', 1)") or sqlerr(__FILE__, __LINE__);
//mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg, location) VALUES(0, 0, $arr[userid], '" . get_date_time() . "', " . sqlesc($msg) . ", 1)") or sqlerr(__FILE__, __LINE__); //=== use this line if you don't have subject in your PM system

//===add karma	uncomment if you have the karma system
mysql_query("UPDATE users SET seedbonus = seedbonus+10.0 WHERE id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);
//===end 

//===notify people who voted on request thanks CoLdFuSiOn :)
$res = mysql_query("SELECT `userid` FROM `addedrequests` WHERE `requestid` = $requestid AND userid != $arr[userid]") or sqlerr(__FILE__, __LINE__);
$pn_msg = "The Request you voted for [b]" . $arr[request] . "[/b] has been filled by [b]" . $arr2[username] . "[/b]. You can download your request from [b][url=" . $filledurl. "]" . $filledurl. "[/url][/b].  Please do not forget to leave thanks where due.";
$some_variable = '';
while($row = mysql_fetch_assoc($res)) {
//=== use this if you DO have subject in your PMs 
$some_variable .= "(0, 0, 'Request " . $arr[request] . " was just uploaded', $row[userid], '" . get_date_time() . "', " . sqlesc($pn_msg) . ")";
//=== use this if you DO NOT have subject in your PMs 
//$some_variable .= "(0, 0, $row[userid], '" . get_date_time() . "', " . sqlesc($pn_msg) . ")";
}
//=== use this if you DO have subject in your PMs 
if (mysql_num_rows($res) < 0)
mysql_query("INSERT INTO messages (poster, sender, subject, receiver, added, msg) VALUES ".$some_variable."") or sqlerr(__FILE__, __LINE__);
//=== use this if you do NOT have subject in your PMs
//mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ".$some_variable."") or sqlerr(__FILE__, __LINE__);
//===end

print("<table width=600><tr><td class=colhead align=left><h1>Success!</h1></td></tr><tr><td class=clearalt6 align=left>".
"Request $requestid successfully filled with <a class=altlink href=$filledurl>$filledurl</a>.  <br><br>".
"User <a class=altlink href=userdetails.php?id=$arr[userid]><b>$arr[username]</b></a> automatically PMd.  <br><br>".
"If you have made a mistake in filling in the URL or have realised that your torrent does not actually satisfy this request".
", please reset the request so someone else can fill it by clicking <a class=altlink href=". $_SERVER[PHP_SELF] ."?id=$requestid&req_reset=1>HERE</a>".
"  <br><br>Do <b>NOT</b> follow this link unless you are sure there is a problem.<br><br></td></tr></table>");
}
else
{
print("<table width=600><tr><td class=colhead align=left><h1>Success!</h1></td></tr><tr><td class=clearalt6 align=left>".
"Request $requestid successfully filled with <a class=altlink href=$filledurl>$filledurl</a>.  <br><br>User ".
"<a class=altlink href=userdetails.php?id=$arr[userid]><b>$arr[username]</b></a> automatically PMed.  <br><br>".
"If you have made a mistake in filling in the URL or have realised that your torrent does not actually satisfy this request".
", please reset the request so someone else can fill it by clicking <a class=altlink href=". $_SERVER[PHP_SELF] ."?id=$requestid&req_reset=1>HERE</a>".
"  <br><br>Do <b>NOT</b> follow this link unless you are sure there is a problem.<br><br></td></tr></table>");
}

end_main_frame();
stdfoot();
die; 
}
//===end req filled	

//=== request reset
if ($_GET["req_reset"]){ 

$req_reset = 0 + $_GET["req_reset"];
if($req_reset != '1')
stderr("Error", "I smell a rat!");

$requestid = htmlentities($_GET["id"]);
$requestid = 0 + $requestid; 

stdhead("Reset Request");

begin_main_frame();

$res = mysql_query("SELECT userid, filledby,filled FROM requests WHERE id =$requestid") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res);

if (($CURUSER[id] == $arr[userid]) || (get_user_class() >= UC_MODERATOR) || ($CURUSER[id] == $arr[filledby]))
{
//===remove karma remove if not using karma system
 if ($arr['filled']=='yes')
 mysql_query("UPDATE users SET seedbonus = seedbonus-10.0 WHERE id = $arr[filledby]") or sqlerr(__FILE__, __LINE__);
 //===end
 @mysql_query("UPDATE requests SET filled='no', filledurl='', filledby='0' WHERE id =$requestid") or sqlerr(__FILE__, __LINE__);
 
print("<table width=600><tr><td class=colhead align=left><h1>Success!</h1></td></tr>".
"<tr><td class=clearalt6 align=left>Request $requestid successfully reset.<br><br></td></tr></table>");
}
else{
print("<table width=600><tr><td class=colhead align=left><h1>Error!</h1></td></tr><tr><td class=clearalt6 align=left>".
"Sorry, cannot reset a request when you are not the owner, staff or person filling it.<br><br></td></tr></table>");
}

end_main_frame(); 
stdfoot(); 
die;
}
//===end request reset

//=== vote for request
if ($_GET["req_vote"]){ 

$req_vote = 0 + $_GET["req_vote"];
if($req_vote != '1')
stderr("Error", "I smell a rat!");

$requestid = 0 + $_GET["id"];
	
$userid = 0 + $CURUSER["id"];
if (!is_valid_id($userid))
stderr("Error", "I smell a rat!"); 
	
stdhead("Vote");
	
$res = mysql_query("SELECT * FROM addedrequests WHERE requestid=$requestid and userid = $userid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_assoc($res);
$voted = $arr;

if ($voted) {
print("<table width=600><tr><td class=colhead align=left><h1>You've Already Voted</h1></td></tr><tr><td class=clearalt6 align=left>".
"<p>You've already voted for this request, only 1 vote for each request is allowed</p>".
"<p>Back to <a class=altlink href=viewrequests.php?id=$requestid&req_details=1><b>request details</b></a></p><br><br></td></tr></table>");
}
else
{ 
mysql_query("UPDATE requests SET hits = hits + 1 WHERE id=$requestid") or sqlerr(__FILE__,__LINE__);
@mysql_query("INSERT INTO addedrequests VALUES(0, $requestid, $userid)") or sqlerr(__FILE__,__LINE__);
print("<table width=600><tr><td class=colhead align=left><h1>Vote accepted</h1></td></tr><tr><td class=clearalt6 align=left>".
"<p>Successfully voted for request $requestid</p><p>Back to <a class=altlink href=viewrequests.php?id=$requestid&req_details=1>".
"<b>request details</b></a></p><br><br></td></tr></table>");
}  
stdfoot(); 
die;
}
//=== end vote for request

//===  votes_view	
if ($_GET["votes_view"]){

$votes_view = 0 + $_GET["votes_view"];
if($votes_view != '1')
stderr("Error", "I smell a rat!");

$requestid = 0 + $_GET["requestid"]; 
if (!is_valid_id($requestid))
stderr("Error", "I smell a rat!");

$res2 = mysql_query("select count(addedrequests.id) from addedrequests inner join users on addedrequests.userid = users.id inner join requests on addedrequests.requestid = requests.id WHERE addedrequests.requestid =$requestid") or die(mysql_error());
$row = mysql_fetch_array($res2);
$count = $row[0];


$perpage = 25;

 list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" );

$res = mysql_query("select users.id as userid,users.username, users.downloaded,users.uploaded, requests.id as requestid, requests.request from addedrequests inner join users on addedrequests.userid = users.id inner join requests on addedrequests.requestid = requests.id WHERE addedrequests.requestid =$requestid $limit") or sqlerr();

stdhead("Voters");

$res2 = mysql_query("select request from requests where id=$requestid");
$arr2 = mysql_fetch_assoc($res2);

print("<h1>Voters for <a class=altlink href=viewrequests.php?id=$requestid&req_details=1><b>$arr2[request]</b></a></h1>");
print("<p>Vote for this <a class=altlink href=viewrequests.php?id=$requestid&req_vote=1><b>request</b></a></p>");

echo $pagertop;

if (mysql_num_rows($res) == 0)
 print("<p align=center><b>Nothing found</b></p>\n");
else
{
 print("<table border=1 cellspacing=0 cellpadding=5>\n");
 print("<tr><td class=colhead>Username</td><td class=colhead align=left>Uploaded</td><td class=colhead align=left>Downloaded</td>".
   "<td class=colhead align=left>Share Ratio</td>\n");

 while ($arr = mysql_fetch_assoc($res))
 {
//=======change colors
		if($count2 == 0)
{
$count2 = $count2+1;
$class = "clearalt6";
}
else
{
$count2 = 0;
$class = "clearalt7";
}
if ($arr["downloaded"] > 0)
{
       $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
       $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
    }
    else
       if ($arr["uploaded"] > 0)
         $ratio = "Inf.";
 else
  $ratio = "---";
$uploaded =mksize($arr["uploaded"]);
$joindate = "$arr[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"])) . " ago)";
$downloaded = mksize($arr["downloaded"]);
if ($arr["enabled"] == 'no')
 $enabled = "<font color = red>No</font>";
else
 $enabled = "<font color = green>Yes</font>";

 print("<tr><td class=$class><a href=userdetails.php?id=$arr[userid]><b>$arr[username]</b></a></td><td align=left class=$class>$uploaded</td><td align=left class=$class>$downloaded</td><td align=left class=$class>$ratio</td></tr>\n");
 }
 print("</table>\n");
}

echo $pagerbottom;	

stdfoot();
die;
}
//===end votes_view	

//=== delete request user / staff
if ($_GET["del_req"]){

$del_req = 0 + $_GET["del_req"];
if($del_req != '1')
stderr("Error", "I smell a rat!");

$requestid = 0 + $_GET["id"];
	
$userid = 0 + $CURUSER["id"];
if (!is_valid_id($userid))
stderr("Error", "I smell a rat!");

$res = mysql_query("SELECT * FROM requests WHERE id = $requestid") or sqlerr(__FILE__, __LINE__);
$num = mysql_fetch_array($res);

if ($userid != $num["userid"] && get_user_class() < UC_MODERATOR)
stderr("Error", "This is not your Request to delete!");	

$sure = 0 + $_GET["sure"];

 if ($sure == 0)
 stderr("Delete Request", "You`re about to delete this request. Click\n <a class=altlink href=". $_SERVER[PHP_SELF] ."?id=$requestid&del_req=1&sure=1>here</a>, if you`re sure.");
elseif ($sure == 1){
mysql_query("DELETE FROM requests WHERE id=$requestid") or sqlerr(__FILE__,__LINE__);
mysql_query("DELETE FROM addedrequests WHERE requestid = $requestid") or sqlerr(__FILE__,__LINE__);
mysql_query("DELETE FROM comments WHERE request=$requestid") or sqlerr(__FILE__,__LINE__);
write_log("Request: $request ($num[request]) was deleted from the Request section by $CURUSER[username]");
header("Refresh: 0; url=viewrequests.php");
}
else
stderr("Error", "I smell a rat!");
}
//===end delete request user / staff 

//=== delete multi requests for staff
if ($_GET["staff_delete"]){

$staff_delete = 0 + $_GET["staff_delete"];
if($staff_delete != '1')
stderr("Error", "I smell a rat!");

if (get_user_class() >= UC_MODERATOR)
{
if (empty($_POST["delreq"]))
   bark("Don't leave any fields blank.");

$do="DELETE FROM requests WHERE id IN (" . implode(", ", $_POST[delreq]) . ")";
$do2="DELETE FROM addedrequests WHERE requestid IN (" . implode(", ", $_POST[delreq]) . ")";
$do3="DELETE FROM comments WHERE request IN (" . implode(", ", $_POST[delreq]) . ")";
$res=mysql_query($do);
$res2=mysql_query($do2); 
$res3=mysql_query($do3);
}
else
{ 
bark("You're not staff, bugger off");}
header("Refresh: 0; url=viewrequests.php");
}
// end delete multi requests

//=== prolly not needed, but what the hell... basically stopping the page getting screwed up
if ($_GET["sort"]){
$sort = $_GET["sort"];
if($sort == 'votes' || $sort == 'cat' || $sort == 'request' || $sort == 'added')
$sort = $_GET["sort"]; 
else
stderr("Error", "I smell a rat!");  
}
if ($_GET["filter"]){
$filter = $_GET["filter"];
if($filter == 'true' || $filter == 'false')
$filter = $_GET["filter"];
else
stderr("Error", "I smell a rat!");  
}
//=== end of prolly not needed, but what the hell :P

stdhead("Requests Page");

begin_main_frame();

print("<div align=center><table border=1 width=600 cellspacing=0 cellpadding=5><tr><td class=colhead align=center><h1>Requests Section</h1>\n</td></tr>".
"<tr><td align=center class=clearalt6><p><a class=altlink href=". $_SERVER[PHP_SELF] ."?add_request=1>Make a request</a>&nbsp;&nbsp;<a class=altlink href=viewrequests.php?requestorid=$CURUSER[id]>View my requests</a></p>".
"<p><a class=altlink href=". $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&sort=" . $_GET[sort] . "&filter=true>Hide Filled</a>");

//==== for mods only to make deleting filled requests simple... yeah, I'm lazy :P
if (get_user_class() >= UC_MODERATOR)
print(" - <a class=altlink href=". $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&sort=" . $_GET[sort] . "&filter=false>Only Filled</a>");
print("</p><p>Look in the <a class=altlink href=viewoffers.php><b>Offers</b></a> Section before you make a Request</p>");

$search = $_GET["search"];
$search = " AND requests.request like ".sqlesc('%'.$search.'%');

if ($sort == "votes")			  
$sort = " ORDER BY hits DESC";
elseif ($sort == "cat")
$sort = " ORDER BY cat ";
else if ($sort == "request")
$sort = " ORDER BY request ";
else if ($sort == "added")
$sort = " ORDER BY added ASC";
else
$sort = " ORDER BY added DESC";

if ($filter == "true")
$filter = " AND requests.filledby = '0' ";
elseif ($filter == "false")
$filter = " AND requests.filled = 'yes' ";
else
$filter = "";

if ($requestorid <> NULL)
       {
       if (($categ <> NULL) && ($categ <> 0))
 $categ = "WHERE requests.cat = " . $categ . " AND requests.userid = " . $requestorid;
       else
 $categ = "WHERE requests.userid = " . $requestorid;
       }

else if ($categ == 0)
       $categ = '';
else
       $categ = "WHERE requests.cat = " . $categ;

$res = mysql_query("SELECT count(requests.id) FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id  $categ $filter $search") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
$count = $row[0];

$perpage = 25;

list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] ."?" . "category=" . $_GET[category] . "&sort=" . $_GET["sort"] . "&" );
print("<center>");

$res = mysql_query("SELECT users.downloaded, users.uploaded, users.username, requests.filled, requests.filledby, requests.id, requests.userid, requests.request, requests.added, requests.hits, requests.filledurl, categories.image, categories.name as cat FROM requests inner join categories on requests.cat = categories.id inner join users on requests.userid = users.id  $categ $filter $search $sort $limit") or sqlerr(__FILE__, __LINE__);
$num = mysql_num_rows($res);

print("<div align=center><form method=get action=viewrequests.php><select name=category><option value=0>(Show All)</option>");

$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
   $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
   $catdropdown .= ">" . safechar($cat["name"]) . "</option>\n";
}
print("$catdropdown</select><input type=submit align=center value=\"view only selected\" class=button>\n");
print("</form><br><form method=get action=viewrequests.php><b>Search Requests: </b><input type=text size=40 name=search>".
"<input class=button type=submit align=center value=Search></form></td></tr></table><br /><br>");

echo $pagertop;
?>
<script language = "Javascript">
<!-- 

var form='viewreq'

function SetChecked(val,chkName) {
dml=document.forms[form];
len = dml.elements.length;
var i=0;
for( i=0 ; i<len ; i++) {
if (dml.elements[i].name==chkName) {
dml.elements[i].checked=val;
}
}
}

// -->
</script>
<?
print("<form method=post name=viewreq action=viewrequests.php?staff_delete=1 onSubmit=\"return ValidateForm(this,'delreq')\">".
"<table border=1 width=100% cellspacing=0 cellpadding=5><tr><td class=colhead align=left width=50><a class=altlink href=". $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&filter=" . $_GET[filter] . "&sort=cat>Type</a></td>".
"<td class=colhead align=center><a class=altlink href=". $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&filter=" . $_GET[filter] . "&sort=request>Name</a></td>".
"<td class=colhead align=center width=150><a class=altlink href=" . $_SERVER[PHP_SELF] ."?category=" . $_GET[category] . "&filter=" . $_GET[filter] . "&sort=added>Added</a></td>".
"<td class=colhead align=center>Requested by</td><td class=colhead align=center>Filled?</td><td class=colhead align=center>Filled By</td>".
"<td class=colhead align=center><a class=altlink href=" . $_SERVER[PHP_SELF] . "?category=" . $_GET[category] . "&filter=" . $_GET[filter] . "&sort=votes>Votes</a></td>");
if (get_user_class() >= UC_MODERATOR)
print("<td class=colhead align=center>Del</td>");

print("</tr>\n");
for ($i = 0; $i < $num; ++$i)
{
//=======change colors
		if($count2 == 0)
{
$count2 = $count2+1;
$class = "clearalt6";
}
else
{
$count2 = 0;
$class = "clearalt7";
}

$arr = mysql_fetch_assoc($res);

if ($arr["downloaded"] > 0)
   {
     $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
     $ratio = "<font color=" . get_ratio_color($ratio) . "><b>$ratio</b></font>";
   }
   else if ($arr["uploaded"] > 0)
       $ratio = "Inf.";
   else
       $ratio = "---";

$res2 = mysql_query("SELECT username from users where id=" . $arr[filledby]);
$arr2 = mysql_fetch_assoc($res2);  
if ($arr2[username])
       $filledby = $arr2[username];
else
       $filledby = " ";      
$addedby = "<td  class=$class align=center><a href=userdetails.php?id=$arr[userid]><b>$arr[username] ($ratio)</b></a></td>";
$filled = $arr[filled];
if ($filled =="yes")
       $filled = "<a href=$arr[filledurl]><font color=green><b>Yes</b></font></a>";
else
       $filled = "<a href=viewrequests.php?id=$arr[id]&req_details=1><font color=red><b>No</b></font></a>";
 print("<tr><td align=center class=$class><img src=pic/$arr[image]></td>" .
 "<td align=left class=$class><a href=". $_SERVER[PHP_SELF] ."?id=$arr[id]&req_details=1><b>$arr[request]</b></a></td>".
 "<td align=center class=$class>$arr[added]</td>$addedby<td class=$class>$filled</td>".
 "<td class=$class><a href=userdetails.php?id=$arr[filledby]><b>$arr2[username]</b></a></td>".
 "<td class=$class><a href=viewrequests.php?requestid=$arr[id]&votes_view=1><b>$arr[hits]</b></a></td>");
 if (get_user_class() >= UC_MODERATOR)
 print("<td class=$class><input type=checkbox name=\"delreq[]\" value=\"" . $arr[id] . "\" /></td>");
 print("</tr>\n");
}

if (get_user_class() >= UC_MODERATOR)
print("<tr><td class=colhead colspan=8 align=right><a class=altlink href=\"javascript:SetChecked(1,'delreq[]')\">".
"select all</a> - <a class=altlink href=\"javascript:SetChecked(0,'delreq[]')\">un-select all</a>".
" <input type=submit value=\"Delete Selected\" class=button></td></tr>");

print("</table>\n");

echo $pagerbottom;	
 
print("</center>");

end_main_frame();
stdfoot();
die;
?>
