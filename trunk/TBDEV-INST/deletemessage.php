<?php
require "include/bittorrent.php";
$id = 0+$_GET["id"];
if (!is_numeric($id) || $id < 1 || floor($id) != $id)
die;
$type = $_GET["type"];
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

  if ($type == 'in')
  {
  	// make sure message is in CURUSER's Inbox
	  $res = mysql_query("SELECT receiver, location FROM messages WHERE id=" . sqlesc($id)) or die("barf");
	  $arr = mysql_fetch_assoc($res) or die("Bad message ID");
	  if ($arr["receiver"] != $CURUSER["id"])
	    die("I wouldn't do that if i were you...");
    if ($arr["location"] == 'in')
	  	mysql_query("DELETE FROM messages WHERE id=" . sqlesc($id)) or die('delete failed (error code 1).. this should never happen, contact an admin.');
    else if ($arr["location"] == 'both')
			mysql_query("UPDATE messages SET location = 'out' WHERE id=" . sqlesc($id)) or die('delete failed (error code 2).. this should never happen, contact an admin.');
    else
    	die('The message is not in your Inbox.');
  }
	elseif ($type == 'out')
  {
   	// make sure message is in CURUSER's Sentbox
	  $res = mysql_query("SELECT sender, location FROM messages WHERE id=" . sqlesc($id)) or die("barf");
	  $arr = mysql_fetch_assoc($res) or die("Bad message ID");
	  if ($arr["sender"] != $CURUSER["id"])
	    die("I wouldn't do that if i were you...");
    if ($arr["location"] == 'out')
	  	mysql_query("DELETE FROM messages WHERE id=" . sqlesc($id)) or die('delete failed (error code 3).. this should never happen, contact an admin.');
    else if ($arr["location"] == 'both')
			mysql_query("UPDATE messages SET location = 'in' WHERE id=" . sqlesc($id)) or die('delete failed (error code 4).. this should never happen, contact an admin.');
    else
    	die('The message is not in your Sentbox.');
  }
  else
  	die('Unknown PM type.');
  header("Location: $BASEURL/inbox.php".($type == 'out'?"?out=1":""));
?>