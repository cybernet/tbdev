<?php
require "include/bittorrent.php";
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
require_once("include/function_mail.php");
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
parked();

$readme = add_get('read').'=';
$unread = false;

if (isset($_REQUEST['compose'])); // This blocks everything until done...
if (isset($_GET['inbox']))
{
 $pagename = "Inbox";
 $tablefmt = "&nbsp;,Sender,Subject,Date";
 $where = "`receiver` = $CURUSER[id] AND `location` IN ('in','both')";
 $type = "Mail";
}
elseif (isset($_GET['outbox']))
{
 $pagename = "Outbox";
 $tablefmt = "&nbsp;,Sent_to,Subject,Date";
 $where = "`sender` = $CURUSER[id] AND `location` IN ('out','both')";
 $type = "Mail";
}
elseif (isset($_GET['draft']))
{
 $pagename = "Draft";
 $tablefmt = "&nbsp;,Sent_to,Subject,Date";
 $where = "`sender` = $CURUSER[id] AND `location` = 'draft'";
 $type = "Mail";
}
elseif (isset($_GET['templates']))
{
 $pagename = "Templates";
 $tablefmt = "&nbsp;,Subject,Date";
 $where = "`sender` = $CURUSER[id] AND `location` = 'template'";
 $type = "Mail";
}
elseif (isset($_GET['friends2']))
{
 $pagename = "Friends2";
 $where = "AND friends2.type = 'friend'";
 $remove = "remove";
 $type = "friends2";
}
elseif (isset($_GET['blocks']))
{
 $pagename = "Blocks";
 $where = "AND friends2.type = 'block'";
 $remove = "unblock";
 $type = "friends2";
}
else
{
 $pagename = "Mail Overview";
 $type = "Overview";
}

//****** Send a message, or save after editing ******
if (isset($_POST['send']) || isset($_POST['draft']) || isset($_POST['template']))
{
 if (!isset($_POST['template']) && !isset($_POST['change']) && (!isset($_POST['userid']) || !is_valid_id($_POST['userid']))) $error = "Unknown recipient";
//  elseif (isset($_POST['send']) && !isset($_POST['msg'])) print("Nothing to send");
 else
 {
   $sendto = (@$_POST['template'] ? $CURUSER['id'] : @$_POST['userid']);
   if (isset($_POST['usetemplate']) && is_valid_id($_POST['usetemplate']))
   {
     $res = mysql_query("SELECT * FROM messages WHERE `id` = $_POST[usetemplate] AND `location` = 'template' LIMIT 1") or sqlerr();
     $arr = mysql_fetch_array($res);
     $subject = $arr['subject'].(@$_POST['oldsubject'] ? " (was ".unesc($_POST['oldsubject']).")" : "");
     $msg = sqlesc($arr['msg']);
   } else {
     $subject = unesc(@$_POST['subject']);
     $msg = sqlesc(unesc(@$_POST['msg']));
   }
   if ($msg)
   {
     $subject = sqlesc($subject);
     if ((isset($_POST['draft']) || isset($_POST['template'])) && isset($_POST['msgid'])) mysql_query("UPDATE messages SET `subject` = $subject, `msg` = $msg WHERE `id` = $_POST[msgid] AND `sender` = $CURUSER[id]") or die("arghh");
     else
     {
       $to = (@$_POST['draft'] ? 'draft' : (@$_POST['template'] ? 'template' : (@$_POST['save'] ? 'both' : 'in')));
       $status = (@$_POST['send'] ? 'yes' : 'no');
       mysql_query("INSERT INTO `messages` (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`) VALUES ('$CURUSER[id]', '$sendto', NOW(), $subject, $msg, '$status', '$to')") or die("Aargh!");
       if (isset($_POST['msgid'])) mysql_query("DELETE FROM messages WHERE `location` = 'draft' AND `sender` = $CURUSER[id] AND `id` = $_POST[msgid]") or die("arghh");
     }
     if (isset($_POST['send'])) $info = "Message sent successfully".(@$_POST['save'] ? ", a copy has been saved in your Outbox" : "");
     else $info = "Message saved successfully";
   }
   else $error = "Unable to send message";
 }
}

//****** Delete a message ******
if (isset($_POST['remove']) && (isset($_POST['msgs']) || is_array($_POST['remove'])))
{
 if (is_array($_POST['remove'])) $tmp[] = key($_POST['remove']);
 else foreach($_POST['msgs'] as $key => $value) if (is_valid_id($key)) $tmp[] = $key;
 $msgs = implode(', ', $tmp);
 if ($msgs)
 {
   if (isset($_GET['inbox']))
   {
     mysql_query("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = $CURUSER[id] AND `id` IN ($msgs)") or die("arghh");
     mysql_query("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = $CURUSER[id] AND `id` IN ($msgs)") or die("arghh");
   } else {
     if (isset($_GET['outbox'])) mysql_query("UPDATE messages SET `location` = 'in' WHERE `location` = 'both' AND `sender` = $CURUSER[id] AND `id` IN ($msgs)") or die("arghh");
     mysql_query("DELETE FROM messages WHERE `location` IN ('out', 'draft', 'template') AND `sender` = $CURUSER[id] AND `id` IN ($msgs)") or die("arghh");
   }
   $info = count($tmp)." message".add_s(count($tmp))." deleted";
 }
 else $error = "No messages to delete";
}

//****** Mark a message as read - only if you're the recipient ******
if (isset($_POST['mark']) && (isset($_POST['msgs']) || is_array($_POST['mark'])))
{
 if (is_array($_POST['mark'])) $tmp[] = key($_POST['mark']);
 else foreach($_POST['msgs'] as $key => $value) if (is_valid_id($key)) $tmp[] = $key;
 $msgs = implode(', ', $tmp);
 if ($msgs)
 {
   mysql_query("UPDATE messages SET `unread` = 'no' WHERE `id` IN ($msgs) AND `receiver` = $CURUSER[id]") or die("arghh");
   $info = count($tmp)." message".add_s(count($tmp))." marked as read";
 }
 else $error = "No messages marked as read";
}

//****** Add a friend to your list ******
if (isset($_GET['addfriend']) && is_valid_id($_GET['addfriend']))
{
 $res = mysql_query("SELECT 1 FROM friends2 WHERE `user` = $CURUSER[id] AND `friend` = $_GET[addfriend]");
 if (mysql_result($res,0)) mysql_query("UPDATE friends2 SET `type` = 'friend' WHERE `user` = $CURUSER[id] AND `friend` = $_GET[addfriend]") or die("arghh");
 else mysql_query("INSERT INTO `friends` (`user`, `friend`, `type`) VALUES ('$CURUSER[id]', '$_GET[addfriend]', 'friend')") or die("Aargh!");
 $info = "Friend added";
}

//****** Add a block to your list ******
if (isset($_GET['addblock']) && is_valid_id($_GET['addblock']))
{
 $res = mysql_query("SELECT 1 FROM friends2 WHERE `user` = $CURUSER[id] AND `friend` = $_GET[addfriend] AND `type` = 'block'");
 if (mysql_result($res,0)) mysql_query("UPDATE friends2 SET `type` = 'block' WHERE `user` = $CURUSER[id] AND `friend` = $_GET[addblock]") or die("arghh");
 else mysql_query("INSERT INTO `friends` (`user`, `friend`, `type`) VALUES ('$CURUSER[id]', '$_GET[addfriend]', 'block')") or die("Aargh!");
 $info = "Block added";
}

//****** Remove a friend or block from your list ******
if (isset($_GET['remfriend']) && is_valid_id($_GET['remfriend']))
{
 mysql_query("DELETE FROM `friends` WHERE `user` = $CURUSER[id] AND `friend` = $_GET[remfriend]") or die("arghh");
 $info = (isset($_GET['friends2']) ? "Friend" : "Block")." removed";
}

stdhead($pagename, false);
/*
print('<pre>');
print_r($_POST);
print('</pre>');
*/
echo '<script language="javascript" src="mailbox.js"></script>';

if (isset($_REQUEST['compose']))
{
 begin_frame("Compose");
 $userid = @$_REQUEST['id'];
 $subject = ''; $msg = ''; $to = ''; $hidden = ''; $output = ''; $reply = false;
 if (is_array($_REQUEST['compose'])) // In reply or followup to another msg
 {
   $msgid = key($_REQUEST['compose']);
   if (is_valid_id($msgid))
   {
     $res = mysql_query("SELECT * FROM `messages` WHERE `id` = $msgid AND '$CURUSER[id]' IN (`sender`,`receiver`) LIMIT 1") or sqlerr();
     if ($arr = mysql_fetch_assoc($res))
     {
       $subject = $arr['subject'];
       $msg .= htmlspecialchars($arr['msg']);
       if (current($_REQUEST['compose']) == 'Reply')
       {
         if ($arr['unread'] == 'yes' && $arr['receiver'] == $CURUSER['id']) mysql_query("UPDATE messages SET `unread` = 'no' WHERE `id` = $arr[id]") or die("arghh");
         $reply = true;
         $userid = $arr['sender'];
         if (substr($arr['subject'],0,4) != 'Re: ') $subject = "Re: $subject";
       }
       else $userid = $arr['receiver'];
       $hidden .= "<input type=\"hidden\" name=\"msgid\" value=\"$msgid\">";
     }
   }
 }
 if (isset($_GET['templates'])) $to = 'who cares';
 elseif (is_valid_id($userid))
 {
   $res = mysql_query("SELECT `username` FROM `users` WHERE `id` = $userid") or sqlerr();
   if (mysql_num_rows($res))
   {
     $to = mysql_result($res, 0);
     if ($reply) $msg = "\n\n-------- $to wrote: --------\n$msg";
     $hidden .= "<input type=\"hidden\" name=\"userid\" value=\"$userid\">";
     $to = "<b>$to</b>";
   }
 }
 else
 {
   $res = mysql_query("SELECT users.id, users.username FROM users, friends2 WHERE users.id = friends2.friend AND friends2.user = $CURUSER[id] ORDER BY users.username");
   if (mysql_num_rows($res))
   {
     $to = "<select name=\"userid\">\n";
     while ($arr = mysql_fetch_assoc($res)) $to .= "<option value=\"$arr[id]\">$arr[username]</option>\n";
     $to .= "</select>\n";
   }
 }
 if (isset($_GET['id']) && !$to) print("Invalid user ID");
 elseif (!isset($_GET['id']) && !$to) print("No friends2");
 else
 {
   begin_form(rem_get('compose'));
   if ($subject) $hidden .= "<input type=\"hidden\" name=\"oldsubject\" value=\"$subject\">";
   if ($hidden) print($hidden);
   begin_table();
   if (!isset($_GET['templates']))
   {
     tr("To:", $to, 1);
     $res = mysql_query("SELECT * FROM `messages` WHERE `sender` = $CURUSER[id] AND `location` = 'template' ORDER BY `subject`") or sqlerr();
     if (mysql_num_rows($res))
     {
       $tmp = "<select name=\"usetemplate\" onChange=\"toggleTemplate(this);\">\n<option name=\"0\">---</option>\n";
       while ($arr = mysql_fetch_assoc($res)) $tmp .= "<option value=\"$arr[id]\">$arr[subject]</option>\n";
       $tmp .= "</select><br>\n";
       tr("Template:", $tmp, 1);
     }
   }
   tr("Subject:", "<input name=\"subject\" type=\"text\" size=\"60\" value=\"$subject\">", 1);
   tr("Message","<textarea name=\"msg\" cols=\"80\" rows=\"15\">$msg</textarea>", 1);
   if (!isset($_GET['templates'])) $output .= "<input type=\"submit\" name=\"send\" value=\"Send\">&nbsp;<label><input type=\"checkbox\" name=\"save\" checked>Save copy</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"draft\" value=\"Save Draft\">&nbsp;";
   trm($output."<input type=\"submit\" name=\"template\" value=\"Save Template\">");
   end_table();
   end_form();
   end_frame();
   stdfoot();
   die;
 }
 end_frame();
}

begin_frame($pagename);

print(submenu('overview,inbox,outbox,draft,templates,friends2,blocks','overview'));

if ($type == "Overview")
{
 begin_table();
 $res = mysql_query("SELECT COUNT(*), COUNT(`unread` = 'yes') FROM messages WHERE `receiver` = $CURUSER[id] AND `location` IN ('in','both')") or die("barf!");
 $inbox = mysql_result($res, 0);
   $res = mysql_query("SELECT COUNT(*) FROM messages WHERE `receiver` = $CURUSER[id] AND `location` IN ('in','both') AND `unread` = 'yes'") or die("barf!");
   $unread = mysql_result($res, 0);
 $res = mysql_query("SELECT COUNT(*) FROM messages WHERE `sender` = $CURUSER[id] AND `location` IN ('out','both')") or die("barf!");
 $outbox = mysql_result($res, 0);
 $res = mysql_query("SELECT COUNT(*) FROM messages WHERE `sender` = $CURUSER[id] AND `location` = 'draft'") or die("barf!");
 $draft = mysql_result($res, 0);
 $res = mysql_query("SELECT COUNT(*) FROM messages WHERE `sender` = $CURUSER[id] AND `location` = 'template'") or die("barf!");
 $template = mysql_result($res, 0);
 $res = mysql_query("SELECT COUNT(*) FROM friends2 WHERE `user` = $CURUSER[id] AND `type` = 'friend'");
 $friends2 = mysql_result($res, 0);
 $res = mysql_query("SELECT COUNT(*) FROM friends2 WHERE `user` = $CURUSER[id] AND `type` = 'block'");
 $blocks = mysql_result($res, 0);
 tr('<a href="'.substr($_SERVER['PHP_SELF'], 1).'?inbox">Inbox</a>', "$inbox message".add_s($inbox)." ($unread unread)");
 tr('<a href="'.substr($_SERVER['PHP_SELF'], 1).'?outbox">Outbox</a>', "$outbox message".add_s($outbox));
 tr('<a href="'.substr($_SERVER['PHP_SELF'], 1).'?draft">Draft</a>', "$draft message".add_s($draft));
 tr('<a href="'.substr($_SERVER['PHP_SELF'], 1).'?templates">Templates</a>', "$template message".add_s($template));
 tr('<a href="'.substr($_SERVER['PHP_SELF'], 1).'?friends2">friends2</a>', "$friends2 friend".add_s($friends2));
 tr('<a href="'.substr($_SERVER['PHP_SELF'], 1).'?blocks">Blocks</a>', "$blocks block".add_s($blocks));
 end_table();
}
elseif ($type == "friends2")
{
 $order = order("user,class,last_access", "last_access", true);
 $res = mysql_query("SELECT COUNT(*) FROM friends2 WHERE friends2.user = $CURUSER[id] $where");
 list($pagermtop, $pagermbottom, $limit) = pagerm(20, mysql_result($res, 0));
 print($pagermtop);

 begin_table(0,"list");
 $table[] = th_left("User", 'user', "padding-left:30px");
 $table[] = th_left("Class",'class');
 $table[] = th_left("Message");
 $table[] = th_left("Last seen",'last_access');
 $table[] = th_right(ucfirst($remove), 0, "padding-right:30px");
 table($table);
 $res = mysql_query("SELECT u.id, u.class, u.username, u.last_access, TIME_TO_SEC(TIMEDIFF(NOW(),u.last_access)) AS age FROM friends2, users AS u WHERE u.id = friends2.friend AND friends2.user = $CURUSER[id] $where $order $limit");
 while ($arr = mysql_fetch_array($res))
 {
   unset($table);
   $table[] = td_left("<a href=\"userdetails.php?id=$arr[id]\">$arr[username]</a>", 1, "padding-left:30px");
   $table[] = td_left(get_user_class_name($arr['class']));
   $table[] = td_left("<a href=\"".substr($_SERVER['PHP_SELF'], 1)."?compose&id=$arr[id]\">send</a>", 1);
   $table[] = td_left("$arr[last_access] (".get_elapsed_time($arr["age"])." ago)", 1);
   $table[] = td_right("<a href=\"".add_get('remfriend',$arr['id'])."\">$remove</a>", 1, "padding-right:30px");
   table($table);
 }
 end_table();
 print($pagermbottom);
}
elseif ($type == "Mail")
{
 $order = order("added,sender,sendto,subject", "added", true);
 $res = mysql_query("SELECT COUNT(*) FROM messages WHERE $where") or sqlerr(__FILE__, __LINE__);
 $count = mysql_result($res, 0);
 list($pagermtop, $pagermbottom, $limit) = pagerm(20, $count);

 print($pagermtop);
 begin_form();
 begin_table(0,"list");
 $table['&nbsp;']  = th("<input type=\"checkbox\" onClick=\"toggleChecked(this.checked);this.form.del.disabled=true;\">", 1);
 $table['Sender']  = th_left("Sender",'sender');
 $table['Sent_to'] = th_left("Sent To",'sendto');
 $table['Subject'] = th_left("Subject",'subject');
 $table['Date']    = th_left("Date",'added');
 table($table, $tablefmt);
 
//  $res = mysql_query("SELECT messages.*, users.username AS sendername, TIME_TO_SEC(TIMEDIFF(NOW(),added)) AS age FROM messages LEFT JOIN users ON (messages.sender = users.id) WHERE $where $order $limit") or die("barf!");
//  $res = mysql_query("SELECT *, TIME_TO_SEC(TIMEDIFF(NOW(),added)) AS age FROM messages WHERE $where $order $limit") or die("barf!");
 $res = mysql_query("SELECT *, TIME_TO_SEC(TIMEDIFF(NOW(),added)) AS age FROM messages WHERE $where $order $limit") or sqlerr(__FILE__, __LINE__);
 while ($arr = mysql_fetch_assoc($res))
 {
   unset($table);
   $userid = 0;
   $format = '';
   $reading = false;

   if ($arr["sender"] == $CURUSER['id']) $sender = "Yourself";
   elseif (is_valid_id($arr["sender"]))
   {
     $res2 = mysql_query("SELECT username FROM users WHERE `id` = $arr[sender]") or sqlerr();
     $arr2 = mysql_fetch_assoc($res2);
     $sender = "<a href=\"userdetails.php?id=$arr[sender]\">".($arr2["username"] ? $arr2["username"] : "[Deleted]")."</a>";
   }
   else $sender = "System";
//    $sender = $arr['sendername'];

   if ($arr["receiver"] == $CURUSER['id']) $sentto = "Yourself";
   elseif (is_valid_id($arr["receiver"]))
   {
     $res2 = mysql_query("SELECT username FROM users WHERE `id` = $arr[receiver]") or sqlerr();
     $arr2 = mysql_fetch_assoc($res2);
     $sentto = "<a href=\"userdetails.php?id=$arr[receiver]\">".($arr2["username"] ? $arr2["username"] : "[Deleted]")."</a>";
   }
   else $sentto = "System";
 
   $subject = ($arr['subject'] ? $arr['subject'] : "no subject");
 
   if (@$_GET['read'] == $arr['id'])
   {
     $reading = true;
     if (isset($_GET['inbox']) && $arr["unread"] == "yes") mysql_query("UPDATE messages SET `unread` = 'no' WHERE `id` = $arr[id] AND `receiver` = $CURUSER[id]") or die("arghh");
   }
   if ($arr["unread"] == "yes")
   {
     $format = "font-weight:bold;";
     $unread = true;
   }
 
   $table['&nbsp;']  = td("<input type=\"checkbox\" name=\"msgs[$arr[id]]\" ".($reading ? "checked" : "")." onClick=\"this.form.del.disabled=true;\">", 1);
   $table['Sender']  = td_left("$sender", 1, $format);
   $table['Sent_to'] = td_left("$sentto", 1, $format);
   $table['Subject'] = td_left("<img src=\"/pic/plus.gif\" id=\"img_$arr[id]\" class=\"read\">&nbsp;<a href=\"javascript:read($arr[id]);\">$subject</span>", 1, $format);
   $table['Date']    = td_left("$arr[added] ", 1, $format);
 
   table($table, $tablefmt);
 
   $display = "<div>".format_comment($arr['msg'])."<br><br>";
   if (isset($_GET['inbox']) && is_valid_id($arr["sender"]))   $display .= "<input type=\"submit\" name=\"compose[$arr[id]]\" value=\"Reply\">&nbsp;\n";
   elseif (isset($_GET['draft']) || isset($_GET['templates'])) $display .= "<input type=\"submit\" name=\"compose[$arr[id]]\" value=\"Edit\">&nbsp;";
   if (isset($_GET['inbox']) && $arr['unread'] == 'yes') $display .= "<input type=\"submit\" name=\"mark[$arr[id]]\" value=\"Mark as Read\">&nbsp;\n";
   $display .= "<input type=\"submit\" name=\"remove[$arr[id]]\" value=\"Delete\">&nbsp;\n";
   $display .= "</div>";
   table(td_left($display, 1, "padding:0 6px 6px 6px"), $tablefmt, "id=\"msg_$arr[id]\" style=\"display:none;\"");
 }
 
 if ($count)
 {
   $buttons = "<input type=\"button\" value=\"Delete Selected\" onClick=\"this.form.remove.disabled=!this.form.remove.disabled;\">";
   $buttons .= "<input type=\"submit\" name=\"remove\" value=\"...confirm\" disabled>";
   if (isset($_GET['inbox']) && $unread) $buttons .= "&nbsp;<input type=\"button\" value=\"Mark Selected as Read\" onClick=\"this.form.mark.disabled=!this.form.mark.disabled;\"><input type=\"submit\" name=\"mark\" value=\"...confirm\" disabled>";
   if (isset($_GET['templates'])) $buttons .= "&nbsp;<input type=\"submit\" name=\"compose\" value=\"Create New Template\">";
   table(td_left($buttons, 1, "border:0"), $tablefmt);
 }
 end_table();
 end_form();
 print($pagermbottom);
}
end_frame();

stdfoot();
?>