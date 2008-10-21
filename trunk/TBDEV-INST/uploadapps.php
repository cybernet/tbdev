<?php
require_once ("include/bittorrent.php");
require_once ("include/bbcode_functions.php");
require_once ("include/user_functions.php");
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
if (get_user_class() < UC_MODERATOR)
hacker_dork("Upload-Apps - Nosey Cunt !");

$action = $_GET["action"];

// View applications

if (!$action || $action == "show") {
  
  if ($action == "show")
	$hide = "[<a href=/uploadapps.php>Hide accepted/rejected</a>]";
  else {
	$hide = "[<a href=/uploadapps.php?action=show>Show accepted/rejected</a>]";
	$where = "WHERE status = 'pending'";
	$where1 = "WHERE uploadapp.status = 'pending'";
  }
  
  $res = mysql_query("SELECT count(id) FROM uploadapp $where")  or sqlerr(__FILE__, __LINE__);
  $row = mysql_fetch_array($res);
  $url = " .$_SERVER[PHP_SELF]?";
  $count = $row[0];
  $perpage = 25;
  list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $url);

  stdhead("Uploader applications");
  print("<h1 align=center>Uploader applications</h1>");
  if ($count == 0) {
	print("<table class=main width=850 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>\n");
	print("<div align=right><p><font class=small>$hide</font></p></div>");
	print("<table width=100% border=1 cellspacing=0 cellpadding=5><tr><td>");
	print("<p align=center>There are currently no uploader applications</p>");
	print("</td></tr></table>");
	print("</td></tr></table>");
  } else {
	print("<table class=main width=850 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>\n");
	echo $pagertop;
	print("<div align=right><p><font class=small>$hide</font></p></div>");
	print("<table width=100% border=1 cellspacing=0 cellpadding=5 align=center>\n");
	print("<tr>\n");
	print("<td class=colhead align=left>Applied</td>\n");
	print("<td class=colhead align=left>Application</td>\n");
	print("<td class=colhead align=left>Username</td>\n");
	print("<td class=colhead align=left>Member for</td>\n");
	print("<td class=colhead align=left>Class</td>\n");
	print("<td class=colhead align=left>Uploaded</td>\n");
	print("<td class=colhead align=left>Ratio</td>\n");
	print("<td class=colhead align=left>Status</td>\n");
	print("<td class=colhead align=left>Delete</td>\n");
	print("</tr>\n");
	print("<form method=post action=?action=takeappdelete>");

	$res = mysql_query("SELECT uploadapp.*, users.id AS uid, users.username, users.class, users.added, users.uploaded, users.downloaded FROM uploadapp INNER JOIN users on uploadapp.userid = users.id $where1 $limit") or sqlerr(__FILE__, __LINE__);
	while ($arr = mysql_fetch_assoc($res))
	{
	  if ($arr["status"] == "accepted")
		$status = "<font color=green>Accepted</font>";
	  elseif ($arr["status"] == "rejected")
		$status = "<font color=red>Rejected</font>";
	  else
		$status = "<font color=blue>Pending</font>";
	  $membertime = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]));
	  $elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["applied"]));

	  print("<tr>");
	  print("<td>$elapsed ago</td>");
	  print("<td><a href=?action=viewapp&id=$arr[id]>View application</a></td>\n");
	  print("<td><a href=userdetails.php?id=$arr[uid]>$arr[username]</a></td>\n");
	  print("<td>$membertime</td>\n");
	  print("<td>".get_user_class_name($arr["class"])."</td>\n");
	  print("<td>".mksize($arr["uploaded"])."</td>\n");
	  print("<td>".number_format($arr["uploaded"] / $arr["downloaded"], 3)."</td>\n");
	  print("<td>$status</td>\n");
	  print("<td><input type=\"checkbox\" name=\"deleteapp[]\" value=\"" . $arr[id] . "\" /></td>\n");
	  print("</tr>\n");
	}
	print("</table>\n");
	print("<p align=right><input type=submit value=Delete></p>");
	print("</form>");
	echo $pagerbottom;
	print("</td></tr></table>\n");
  }
  stdfoot();
}

// View application

if ($action == "viewapp") {

  $id = $_GET["id"];
  $res = mysql_query("SELECT uploadapp.*, users.id AS uid, users.username, users.class, users.added, users.uploaded, users.downloaded FROM uploadapp INNER JOIN users on uploadapp.userid = users.id WHERE uploadapp.id=$id") or sqlerr(__FILE__, __LINE__);
  $arr = mysql_fetch_assoc($res);
  $membertime = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]));
  $elapsed = get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["applied"]));

  stdhead("Uploader applications");
  print("<h1 align=center>Uploader application</h1>");
  print("<table width=750 border=1 cellspacing=0 cellpadding=5>");
  print("<tr><td class=rowhead width=25%>My username is</td><td><a href=userdetails.php?id=$arr[uid]>$arr[username]</a></td></tr>");
  print("<tr><td class=rowhead>I have joined at</td><td>$arr[added] ($membertime ago)</td></tr>");
  print("<tr><td class=rowhead>My upload amount is</td><td>".mksize($arr["uploaded"])."</td></tr>");
  print("<tr><td class=rowhead>My download amount is</td><td>".mksize($arr["downloaded"])."</td></tr>");
  print("<tr><td class=rowhead>My ratio is </td><td>".number_format($arr["uploaded"] / $arr["downloaded"], 3)."</td></tr>");
  print("<tr><td class=rowhead>I am connectable</td><td>$arr[connectable]</td></tr>");
  print("<tr><td class=rowhead>My current userclass is</td><td>".get_user_class_name($arr["class"])."</td></tr>");
  print("<tr><td class=rowhead>I have applied at</td><td>$arr[applied] ($elapsed ago)</td></tr>");
  print("<tr><td class=rowhead>My upload speed is</td><td>".htmlspecialchars($arr["speed"])."</td></tr>");
  print("<tr><td class=rowhead>What I have to offer</td><td>".htmlspecialchars($arr["offer"])."</td></tr>");
  print("<tr><td class=rowhead>Why I should be promoted</td><td>".htmlspecialchars($arr["reason"])."</td></tr>");
  print("<tr><td class=rowhead>I am uploader at other sites</td><td>$arr[sites]</td></tr>");
  if ($arr["sitenames"] != "")
	print("<tr><td class=rowhead>Those sites are</td><td>".htmlspecialchars($arr["sitenames"])."</td></tr>");

  print("<tr><td class=rowhead>I have scene access</td><td>$arr[scene]</td></tr>");
  print("<tr><td colspan=2>I know how to create, upload and seed torrents: <b>$arr[creating]</b><br>I understand that I have to keep seeding my torrents until there are at least two other seeders: <b>$arr[seeding]</b></td></tr>");
  if ($arr["status"] == "pending")
	print("<tr><td align=center colspan=2><form method=post action=?action=acceptapp><input name=id type=hidden value=$arr[id]><b>Note: (optional)</b><br><input type=text name=note size=40> <input type=submit value=Accept style='height: 20px'></form><br><form method=post action=?action=rejectapp><input name=id type=hidden value=$arr[id]><b>Reason: (optional)</b><br><input type=text name=reason size=40> <input type=submit value=Reject style='height: 20px'></form></td></tr>");
  else
  print("<tr><td colspan=2 align=center>Application ".($arr["status"] == "accepted" ? "accepted" : "rejected")." by <b>$arr[moderator]</b><br>Comment: $arr[comment]</td></tr>");
  print("</table>");
  print("<p align=center><a href=uploadapps.php>Return to uploader applications page</a></p>");
  stdfoot();
}

// Accept application

if ($action == "acceptapp") {

  $id = 0 + $_POST["id"];
  if (!is_valid_id($id))
  	stderr("Error", "It appears that there is no uploader application with that ID.");

  $res = mysql_query("SELECT uploadapp.id, users.username, users.modcomment, users.id AS uid FROM uploadapp INNER JOIN users on uploadapp.userid = users.id WHERE uploadapp.id = $id") or sqlerr(__FILE__, __LINE__);
  $arr = mysql_fetch_assoc($res);

  $note = $_POST["note"];

  $msg = sqlesc("Congratulations, your uploader application has been accepted! You have been promoted to Uploader and you are now able to upload torrents. Please make sure you have read the [url=$BASEURL/rules.php]guidlines on uploading[/url] before you do.\n\nNote: $note");
  $msg1 = sqlesc("User [url=$BASEURL/userdetails.php?id=$arr[uid]][b]$arr[username][/b][/url] has been promoted to Uploader by $CURUSER[username].");
  $modcomment = gmdate("d-m-Y") . " - Promoted to 'Uploader' by ".$CURUSER["username"].".".($arr["modcomment"] != "" ? "\n" : "")."$arr[modcomment]";
  $dt = sqlesc(get_date_time());
  mysql_query("UPDATE uploadapp SET status = 'accepted', comment = ".sqlesc($note).", moderator = ".sqlesc($CURUSER["username"])." WHERE id=$id") or sqlerr(__FILE__, __LINE__);
  mysql_query("UPDATE users SET class = 3, modcomment = ".sqlesc($modcomment)." WHERE id=$arr[uid]") or sqlerr(__FILE__, __LINE__);
  mysql_query("INSERT INTO messages(sender, receiver, added, msg, poster) VALUES(0, $arr[uid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
  $subres = mysql_query("SELECT id FROM users WHERE class = 7") or sqlerr(__FILE__, __LINE__);
  while ($subarr = mysql_fetch_assoc($subres))
    mysql_query("INSERT INTO messages(sender, receiver, added, msg, poster) VALUES(0, $subarr[id], $dt, $msg1, 0)") or sqlerr(__FILE__, __LINE__);
  stderr("Application accepted", "The application was succesfully accepted. The user has been promoted and has been sent a PM notification. Click <a href=uploadapps.php>here</a> to return to the upload applications page.");
}

// Reject application

if ($action == "rejectapp") {

  $id = 0 + $_POST["id"];
  if (!is_valid_id($id))
  	stderr("Error", "It appears that there is no uploader application with that ID.");

  $res = mysql_query("SELECT uploadapp.id, users.id AS uid FROM uploadapp INNER JOIN users on uploadapp.userid = users.id WHERE uploadapp.id=$id") or sqlerr(__FILE__, __LINE__);
  $arr = mysql_fetch_assoc($res);

  $reason = $_POST["reason"];

  $msg = sqlesc("Sorry, your uploader application has been reject. It appears that you are not qualified enough to become uploader.\n\nReason: $reason");
  $dt = sqlesc(get_date_time());
  mysql_query("UPDATE uploadapp SET status = 'rejected', comment = ".sqlesc($reason).", moderator = ".sqlesc($CURUSER["username"])." WHERE id=$id") or sqlerr(__FILE__, __LINE__);
  mysql_query("INSERT INTO messages(sender, receiver, added, msg, poster) VALUES(0, $arr[uid], $dt, $msg, 0)") or sqlerr(__FILE__, __LINE__);
  stderr("Application rejected", "The application was succesfully rejected. The user has been sent a PM notification. Click <a href=uploadapps.php>here</a> to return to the upload applications page.");
}

// Delete applications

if ($action == "takeappdelete") {

  $res = mysql_query("SELECT id FROM uploadapp WHERE id IN (" . implode(", ", $_POST[deleteapp]) . ")") or sqlerr(__FILE__, __LINE__);
  while ($arr = mysql_fetch_assoc($res))
	mysql_query("DELETE FROM uploadapp WHERE id=$arr[id]") or sqlerr(__FILE__, __LINE__);
  stderr("Deleted", "The upload applications were succesfully deleted. Click <a href=uploadapps.php>here</a> to return to the upload applications page.");
}

?>