<?php
// userfriends.php - by pdq
require "include/bittorrent.php";
//require_once "include/user_functions.php";
//require_once "include/bbcode_functions.php";
//require_once "include/pager_functions.php";
//require_once "include/torrenttable_functions.php";
//require_once "include/html_functions.php";
dbconn(false);
loggedinorreturn();

$userid = isset($_GET['id']) ? (int)$_GET['id'] : '';
$id = isset($CURUSER['id']) ? (int)$CURUSER['id'] : '';

if (!$userid)
	$userid = $CURUSER['id'];

if (!is_valid_id($userid))
	stderr("Error", "Invalid ID.");

$res = mysql_query("SELECT * FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_array($res) or stderr("Error", "No user with ID.");

$r = mysql_query("SELECT id, friendid FROM friends WHERE userid=$userid AND friendid=$id AND confirmed = 'yes'") or sqlerr(__FILE__, __LINE__);
$friend = mysql_num_rows($r);
$r = mysql_query("SELECT id FROM blocks WHERE userid=$userid AND blockid=$id") or sqlerr(__FILE__, __LINE__);
$block = mysql_num_rows($r);
if ((!$friend) || $block)
{
if ($user["showfriends"] != "yes" && $CURUSER["id"] != $user["id"] && (get_user_class() < UC_MODERATOR))
 stderr("<br />Sorry", "This members friends list is private.");
}

stdhead("Friends list for " . $user['username']);

print("<p><table class=main border=0 cellspacing=0 cellpadding=0>".
"<tr><td class=embedded><h1 style='margin:0px'> Friends list for $user[username]</h1></td></tr></table></p>\n");
if ($CURUSER['id'] != $user['id'])
    print('<h3><a href=\'userdetails.php?id='.$userid.'\'>Go to '.$user['username'].'\'s Profile</a></h3><br />');

print("<table class=main width=737 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>");

$fcount = number_format(get_row_count("friends", "WHERE userid='".$userid."' AND confirmed = 'yes'"));

print("<h2 align=left><a name=\"friends\">".$user['username']." has ".$fcount." Friends</a></h2>\n");

print("<table width=737 border=1 cellspacing=0 cellpadding=5><tr><td>");

$i = 0;

$res = mysql_query("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$userid AND f.confirmed='yes' ORDER BY name") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) == 0)
	$friends = "<em>".$user['username']." has no friends.</em>";
else
	while ($friend = mysql_fetch_array($res))
	{
$pm_pic = "<img src=".$pic_base_url."button_pm.gif alt='Send PM' border=0>";
$dt = gmtime() - 180;
$online = ($friend["last_access"] >= ''. get_date_time($dt). '' ? '&nbsp;<img src='.$pic_base_url.'button_online.gif border=0 alt=Online>' : '<img src='.$pic_base_url.'button_offline.gif border=0 alt=Offline>');
    $title = htmlspecialchars($friend["title"]);
		if (!$title)
	    $title = get_user_class_name($friend["class"]);
    $body1 = "<a href=userdetails.php?id=" . $friend['id'] . "><b>" . $friend['name'] . "</b></a>" .
    	get_user_icons($friend) . " ($title) $online<br /><br />last seen on " . $friend['last_access'] .
    	"<br />(" . get_elapsed_time(sql_timestamp_to_unix_timestamp($friend['last_access'])) . " ago)";

		$body2 = (($id == $CURUSER['id'])? "" :"<br /><a href=friends.php?id=$CURUSER[id]&action=add&type=friend&targetid=" . $friend['id'] . ">Add Friend</a>" .
			"<br /><br /><a href=sendmessage.php?receiver=" . $friend['id'] . ">".$pm_pic."</a>");
    $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($friend["avatar"]) : "");
//		if (!$avatar)
//			$avatar = "".$pic_base_url."default_avatar.gif";
    if ($i % 2 == 0)
    	print("<table width=737 style='padding: 0px'><tr><td class=bottom style='padding: 5px' width=50% align=center>");
    else
    	print("<td class=bottom style='padding: 5px' width=50% align=center>");
    print("<table class=main width=737 height=75px>");
    print("<tr valign=top><td width=75 align=center style='padding: 0px'>" .
			($avatar ? "<div style='width:75px;height:75px;overflow: hidden'><img width=75px src=\"$avatar\"></div>" : ""). "</td><td>\n");
    print("<table class=main>");
    print("<tr><td class=embedded style='padding: 5px' width=80%>$body1</td>\n");
    print("<td class=embedded style='padding: 5px' width=20%>$body2</td></tr>\n");
    print("</table>");
		print("</td></tr>");
		print("</td></tr></table>\n");
    if ($i % 2 == 1)
			print("</td></tr></table>\n");
		else
			print("</td>\n");
		$i++;
	}
if ($i % 2 == 1)
	print("<td class=bottom width=50%>&nbsp;</td></tr></table>\n");
if (isset($friends))
print($friends);
print("</td></tr></table>\n");
print("</td></tr></table>\n");
//
print("<h1>Comments for <a href=userdetails.php?id=$userid>" . $user["username"] . "</a></h1>\n");


	print("<p><a name=\"startcomments\"></a></p>\n");

	$commentbar = "<p align=center><a class=index href=usercomment.php?action=add&amp;userid=$userid>Add a comment</a></p>\n";

	$subres = mysql_query("SELECT COUNT(*) FROM usercomments WHERE userid = $userid");
	$subrow = mysql_fetch_array($subres,MYSQL_NUM);
	$count = $subrow[0];

	if (!$count) {
		print("<h2>No comments yet</h2>\n");
	}
	else {
		$pager = pager(20, $count, "userfriends.php?id=$userid&", array('lastpagedefault' => 1));

		$subres = mysql_query("SELECT usercomments.id, text, user, usercomments.added, editedby, editedat, avatar, warned, ".
                  "username, title, class, donor FROM usercomments LEFT JOIN users ON usercomments.user = users.id WHERE userid = " .
                  "$userid ORDER BY usercomments.id ".$pager['limit']) or sqlerr(__FILE__, __LINE__);
		$allrows = array();
		while ($subrow = mysql_fetch_assoc($subres))
			$allrows[] = $subrow;

		print($commentbar);
		print($pager['pagertop']);

		usercommenttable($allrows);

		print($pager['pagerbottom']);
	}

	print($commentbar);
//
print("<p><a href=users.php><b>Find User/Browse User List</b></a></p>");
stdfoot();
?>