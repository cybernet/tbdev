<?php
require ("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
parked();
$action = htmlspecialchars(trim($_GET["action"]));
$maxfilesize = 2048 * 2048;
ini_set("upload_max_filesize",$maxfilesize);
$attachment_dir = ROOT_PATH."forumattaches";
$allowed_file_extensions = array('rar', 'zip','jpg','png','gif');

function catch_up($id = 0)
{
global $CURUSER, $READPOST_EXPIRY;

$userid = (int)$CURUSER['id'];

$res = sql_query("SELECT t.id, t.lastpost, r.id AS r_id, r.lastpostread ".
"FROM topics AS t ".
"LEFT JOIN posts AS p ON p.id = t.lastpost ".
"LEFT JOIN readposts AS r ON r.userid=".sqlesc($userid)." AND r.topicid=t.id ".
"WHERE p.added > ".sqlesc(get_date_time(gmtime() - $READPOST_EXPIRY)).
(!empty($id) ? ' AND t.id '.(is_array($id) ? 'IN ('.implode(', ', $id).')' : '= '.sqlesc($id)) : '')) or sqlerr(__FILE__, __LINE__);

while ($arr = mysql_fetch_assoc($res))
{
$postid = (int)$arr['lastpost'];

if (!is_valid_id($arr['r_id']))
sql_query("INSERT INTO readposts (userid, topicid, lastpostread) VALUES($userid, ".(int)$arr['id'].", $postid)") or sqlerr(__FILE__, __LINE__);
else if ($arr['lastpostread'] < $postid)
sql_query("UPDATE readposts SET lastpostread=$postid WHERE id = ".$arr['r_id']) or sqlerr(__FILE__, __LINE__);
}
mysql_free_result($res);
}
 
  //-------- Returns the minimum read/write class levels of a forum

  function get_forum_access_levels($forumid)
  {
    $res = sql_query("SELECT minclassread, minclasswrite, minclasscreate FROM forums WHERE id=".sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      return false;

    $arr = mysql_fetch_assoc($res);

    return array("read" => $arr["minclassread"], "write" => $arr["minclasswrite"], "create" => $arr["minclasscreate"]);
  }

  //-------- Returns the forum ID of a topic, or false on error

  function get_topic_forum($topicid)
  {
    $res = sql_query("SELECT forumid FROM topics WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      return false;

    $arr = mysql_fetch_row($res);

    return $arr[0];
  }

  //-------- Returns the ID of the last post of a forum

  function update_topic_last_post($topicid)
  {
    $res = sql_query("SELECT id FROM posts WHERE topicid=".sqlesc($topicid)." ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res) or die("No post found");

    $postid = $arr[0];

    sql_query("UPDATE topics SET lastpost=$postid WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
  }

  function get_forum_last_post($forumid)
  {
    $res = sql_query("SELECT lastpost FROM topics WHERE forumid=".sqlesc($forumid)." ORDER BY lastpost DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $postid = $arr[0];

    if ($postid)
      return $postid;

    else
      return 0;
  }


  //-------- Inserts a quick jump menu

  function insert_quick_jump_menu($currentforum = 0)
  {
    print("<p align=center><form method=get action=? name=jump>\n");

    print("<input type=hidden name=action value=viewforum>\n");

    print("<div align=right class=success>Quick jump: ");

    print("<select name=forumid onchange=\"if(this.options[this.selectedIndex].value != -1){ forms['jump'].submit() }\">\n");

    $res = sql_query("SELECT * FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);

    while ($arr = mysql_fetch_assoc($res))
    {
      if (get_user_class() >= $arr["minclassread"])
        print("<option value=" . $arr["id"] . ($currentforum == $arr["id"] ? " selected>" : ">") . $arr["name"] . "\n");
    }

    print("</select>\n");

    print("<input type=submit value='Go!' class='gobutton'>\n");

    print("</form>\n</div>");
  }

  //-------- Inserts a compose frame

  function insert_compose_frame($id, $newtopic = true, $quote = false, $attachment = false)
  {
    global $maxsubjectlength, $CURUSER, $max_torrent_size,$maxfilesize;

    if ($newtopic)
    {
      $res = sql_query("SELECT name FROM forums WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_assoc($res) or die("Bad forum id");

      $forumname = $arr["name"];

      print("<h3>New topic in <a href=?action=viewforum&forumid=$id>$forumname</a> forum</h3>\n");
    }
    else
    {
      $res = sql_query("SELECT * FROM topics WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_assoc($res) or die("Forum error, Topic not found.");

      $subject = $arr["subject"];

      print("<p align=center><h3>Reply to topic: <a href=?action=viewtopic&topicid=$id>".htmlspecialchars($subject)."</a></h3></p>");
    }

    print ("<span name=\"preview\" id=\"preview\"></span>");
    begin_frame("Compose", true);

    print("<form method='post' name='compose' action='?action=post' enctype='multipart/form-data'>\n");

    if ($newtopic)
      print("<input type=hidden name=forumid value=$id>\n");

    else
      print("<input type=hidden name=topicid value=$id>\n");

    begin_table();

    if ($newtopic)
      print("<tr><td class=rowhead>Subject</td>" .
        "<td align=left style='padding: 0px'><input type=text size=100 maxlength=$maxsubjectlength name=subject " .
        "style='border: 0px; height: 19px'></td></tr>\n");

    if ($quote)
    {
		$postid = 0+$_GET["postid"];
		if (!is_valid_id($postid)) {
			stdmsg("Error", "No post with this ID");
			end_table();
			stdfoot();
			print("</table>");
			die;
	}
	   $res = sql_query("SELECT posts.*, users.username FROM posts JOIN users ON posts.userid = users.id WHERE posts.id=$postid") or sqlerr(__FILE__, __LINE__);

	   if (mysql_num_rows($res) != 1) {
	     stdmsg("Error", "No post with this ID");
	     end_table();
	     stdfoot();
	     print("</table>");
	     die;
     }

	   $arr = mysql_fetch_assoc($res);
    }

    print("<tr><td class=rowhead>Body</td><td align=left style='padding: 0px'>");
   textbbcode("compose","body",($quote?(("[quote=".htmlspecialchars($arr["username"])."]".htmlspecialchars(unesc($arr["body"]))."[/quote]")):""));
   if ($attachment) {
	   print("<tr><td colspan=2><fieldset class=\"fieldset\"><legend>Add Attachment</legend>");
		print("<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$maxfilesize\" />");
		print("<input type=checkbox name=uploadattachment value=yes> <input type=\"file\" name=\"file\" size=\"60\"><div class=errorAllowed Files: rar, zip<br>Size Limit ".mksize($maxfilesize)."</div></fieldset></td></tr>\n");
	}


   print("<tr><td colspan=2 align=center><input type=button value=Submit name=button1 onclick='return Post();'><input type=button value=Preview name=button2 onclick='return Preview();'></td></tr>\n");
   //print("<input type=button class=gobutton name=button value=Preview  onclick=\"javascript:get(this.parentNode);\">");
   //print("<img id=\"loading\" style=\"visibility: hidden\" src=\"pic/ajax-loader.gif\">\n");
   print("</td></tr>");   
   print("</td></tr>");

   end_table();

   print("</form>\n");

   //print("<p align=center><a href=tags.php target=_blank>Tags</a> | <a href=smilies.php target=_blank>Smilies</a></p>\n");

   end_frame();
       
       print("</form>\n");

		//print("<p align=center><a href=tags.php target=_blank>Tags</a> | <a href=smilies.php target=_blank>Smilies</a></p>\n");

    end_frame();

    //------ Get 10 last posts if this is a reply

    if (!$newtopic)
    {
      $postres = sql_query("SELECT * FROM posts WHERE topicid=".sqlesc($id)." ORDER BY id DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);

      begin_frame("10 last posts, in reverse order");

      while ($post = mysql_fetch_assoc($postres))
      {
        //-- Get poster details

        $userres = sql_query("SELECT * FROM users WHERE id=" . $post["userid"] . " LIMIT 1") or sqlerr(__FILE__, __LINE__);

        $user = mysql_fetch_assoc($userres);

      	$avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($user["avatar"]) : "");
//	    $avatar = $user["avatar"];

        if (!$avatar)
          $avatar = "pic/default_avatar.gif";

        print("<p class=sub>#" . $post["id"] . " by " . $user["username"] . " at " . $post["added"] . " GMT</p>");

        begin_table(true);

        print("<tr ><td height=100 width=100 align=center style='padding: 0px'>" . ($avatar ? "<img height=100 width=100 src=$avatar>" : "").
          "</td><td class=comment valign=top>" . format_comment($post["body"]) . "</td></tr>\n");

        end_table();

      }

      end_frame();

    }

  insert_quick_jump_menu();

  }

  //-------- Global variables

  $maxsubjectlength = 60;
  $postsperpage = $CURUSER["postsperpage"];
	if (!$postsperpage) $postsperpage = 25;
	
//-------- Action: Edit Forum

    if ($action == "editforum")
{
if (get_user_class() <= UC_MODERATOR) {
stderr("Forum Error", "Not yet implemented.");
die();
}
    stdhead("Edit forum");
    begin_main_frame();
    begin_frame("Edit Forum", "center");

    $forumid = 0 + $_GET["forumid"];
    $res = sql_query("SELECT * FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);
    $forum = mysql_fetch_assoc($res);

    print("<form method=post action=?action=updateforum&forumid=$forumid>\n");
    begin_table();
    print("<tr><td class=rowhead>Forum name</td>" .
        "<td align=left style='padding: 0px'><input type=text size=60 maxlength=$maxsubjectlength name=name " .
        "style='border: 0px; height: 19px' value=\"$forum[name]\"></td></tr>\n".
        "<tr><td class=rowhead>Description</td>" .
        "<td align=left style='padding: 0px'><textarea name=description cols=68 rows=3 style='border: 0px'>$forum[description]</textarea></td></tr>\n".
        "<tr><td class=rowhead></td><td align=left style='padding: 0px'>&nbsp;Minimum <select name=readclass>");
    for ($i = 0; $i <= UC_SYSOP; ++$i)
    	print("<option value=$i" . ($i == $forum['minclassread'] ? " selected" : "") . ">" . get_user_class_name($i) . "</option>\n");
	print("</select> Class required to View<br>\n&nbsp;Minimum <select name=writeclass>");
    for ($i = 0; $i <= UC_SYSOP; ++$i)
    	print("<option value=$i" . ($i == $forum['minclasswrite'] ? " selected" : "") . ">" . get_user_class_name($i) . "</option>\n");
	print("</select> Class required to Post<br>\n&nbsp;Minimum <select name=createclass>");
    for ($i = 0; $i <= UC_SYSOP; ++$i)
    	print("<option value=$i" . ($i == $forum['minclasscreate'] ? " selected" : "") . ">" . get_user_class_name($i) . "</option>\n");
	print("</select> Class required to Create Topics</td></tr>\n".
    	"<tr><td colspan=2 align=center><input type=submit class=gobutton value='Submit'></td></tr>\n");
    end_table();
    print("</form>\n");
    
    stdfoot();
    end_frame();
    end_main_frame();
    die;
  }

//-------- Action: Update Forum

if ($action == "updateforum")
{
if (get_user_class() <= UC_MODERATOR) {
stderr("Forum Error", "Not Allowed.");
die();
}
    $forumid = $_GET["forumid"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $minclassread = 0 + $_POST["readclass"];
    $minclasswrite = 0 + $_POST["writeclass"];
    $minclasscreate = 0 + $_POST["createclass"];

    if(!$forumid)
    	stderr("Error", "Forum ID not found.");
    if(!$name)
    	stderr("Error", "You must specify a name for the forum.");
    if(!$description)
    	stderr("Error", "You must provide a description for the forum.");

    $name = sqlesc($name);
    $description = sqlesc($description);

    sql_query("UPDATE forums SET ".
    	"name=$name, ".
        "description=$description, ".
        "minclassread=$minclassread, ".
        "minclasswrite=$minclasswrite, ".
        "minclasscreate=$minclasscreate ".
      	"WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    header("Location: $BASEURL/forums.php");
  }

////////Action delete forum
if ($action == 'deleteforum' && get_user_class() == UC_SYSOP)
{
$forumid = 0 + $_GET['forumid'];

//int_check($forumid, true);
if (!is_valid_id($forumid))
die("Invalid id!");

if (!$forumid)
stderr("Error", "Forum ID not found.");

$confirmed = 0 + $_GET['confirmed'];
if (!$confirmed)
{
$rt = sql_query("SELECT topics.id, forums.name ".
"FROM topics ".
"LEFT JOIN forums ON forums.id=topics.forumid ".
"WHERE topics.forumid = ".sqlesc($forumid));
$topics = mysql_num_rows($rt);
$posts = 0;

if ($topics > 0)
{
while ($topic = mysql_fetch_assoc($rt))
{
$ids[] = $topic['id'];
$forum = $topic['name'];
}

$rp = sql_query("SELECT COUNT(id) FROM posts WHERE topicid IN (".join(', ', $ids).")");
foreach ($ids as $id)
if ($a = mysql_fetch_row($rp))
$posts += $a[0];
}

$res = sql_query("SELECT COUNT(attachments.id) AS attachments, COUNT(polls.id) AS polls ".
"FROM topics ".
"LEFT JOIN posts ON topics.id=posts.topicid ".
"LEFT JOIN attachments ON attachments.postid = posts.id ".
"LEFT JOIN polls ON polls.id=topics.pollid ".
"WHERE topics.forumid=".sqlesc($forumid));

$attachments = $polls = 0;

if ($arr = mysql_fetch_assoc($res))
{
$attachments = $arr['attachments'];
$polls = $arr['polls'];
}

stderr("** WARNING! **", "Deleting forum with id=$forumid (".$forum.") will also delete ".$posts." post".($posts != 1 ? 's' : '').", ".$attachments." attachment".($attachments != 1 ? 's' : '')." and ".($polls-$attachments)." poll".(($polls-$attachments) != 1 ? 's' : '')." in ".$topics." topic".($topics != 1 ? 's' : '').". [<a href=/forums.php?action=deleteforum&forumid=$forumid&confirmed=1>ACCEPT</a>] [<a href=/forums.php?action=viewforum&forumid=$forumid>CANCEL</a>]");
}

$rt = sql_query("SELECT topics.id, attachments.filename ".
"FROM topics ".
"LEFT JOIN posts ON topics.id = posts.topicid ".
"LEFT JOIN attachments ON attachments.postid = posts.id ".
"WHERE topics.forumid = ".sqlesc($forumid));

while ($topic = mysql_fetch_assoc($rt))
{
$tids[] = $topic['id'];

if (!empty($topic['filename']))
@unlink($attachment_dir."/".$topic['filename']);
}

sql_query("DELETE posts.*, attachments.*, attachmentdownloads.*, topics.*, forums.*, polls.*, pollanswers.* ".
"FROM posts ".
"LEFT JOIN attachments ON attachments.postid = posts.id ".
"LEFT JOIN attachmentdownloads ON attachmentdownloads.fileid = attachments.id ".
"LEFT JOIN topics ON topics.id = posts.topicid ".
"LEFT JOIN forums ON forums.id = topics.forumid ".
"LEFT JOIN polls ON polls.id = topics.pollid ".
"LEFT JOIN pollanswers ON pollanswers.pollid = polls.id ".
"WHERE posts.topicid IN (".join(', ', $tids).")");

header("Location: $BASEURL/forums.php");
exit;
}

  //-------- Action: New topic

  if ($action == "newtopic")
  {
    $forumid = 0+$_GET["forumid"];

    stdhead("New topic");

    begin_main_frame();

    insert_compose_frame($forumid,true,false,true);
    stdfoot();
    end_main_frame();

    die;
  }

  //-------- Action: Post

  if ($action == "post")
  {
    $forumid = 0 + $_POST["forumid"];
    $topicid = 0 + $_POST["topicid"];

    if (!is_valid_id($forumid) && !is_valid_id($topicid))
      stderr("Error", "Bad forum or topic ID.");

    $newtopic = $forumid > 0;

    $subject = $_POST["subject"];

    if ($newtopic)
    {
      $subject = trim($subject);

      if (!$subject)
        stderr("Error", "You must enter a subject.");

      if (strlen($subject) > $maxsubjectlength)
        stderr("Error", "Subject is limited to ??? characters.");
    }
    else
      $forumid = get_topic_forum($topicid) or die("Bad topic ID");
	  if ($CURUSER["forumpost"] == 'no')
  {
stdhead();
  stdmsg("Sorry...", "Your posting rights are disbled,you received already a PM about this.</a>)");
  stdfoot();
				exit;
		}

    //------ Make sure sure user has write access in forum

    $arr = get_forum_access_levels($forumid) or die("Bad forum ID");

    if (get_user_class() < $arr["write"] || ($newtopic && get_user_class() < $arr["create"]))
      stderr("Error", "Permission denied.");

    $body = trim($_POST["body"]);

    if ($body == "")
      stderr("Error", "No body text.");

    $userid = 0+$CURUSER["id"];
    
/*
    // Anti Flood Code
   // To ensure that posts are not entered within 60 seconds limiting posts
   // to a maximum of 60 per hour.
if (get_user_class() < UC_MODERATOR) {
   if (strtotime($CURUSER['last_post']) > (strtotime($CURUSER['ctime']) - 60))
   {
       $secs = 60 - (strtotime($CURUSER['ctime']) - strtotime($CURUSER['last_post']));
       stderr("Error","Post Flooding Not Allowed. Please wait $secs second".($secs == 1 ? '' : 's')." before making another post.",false);
   }
}
   /////////////////////////////////////////////////////////////////////////
*/


    if ($newtopic)
    {
      //---- Create topic 
      //===add karma
      sql_query("UPDATE users SET seedbonus = seedbonus+2.0 WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
      //===end
      $subject = sqlesc($subject);

      sql_query("INSERT INTO topics (userid, forumid, subject) VALUES($userid, $forumid, $subject)") or sqlerr(__FILE__, __LINE__);

      $topicid = mysql_insert_id() or stderr("Error", "No topic ID returned");

    }
    else
    {
      //---- Make sure topic exists and is unlocked

      $res = sql_query("SELECT * FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

      $arr = mysql_fetch_assoc($res) or die("Topic id n/a");

      if ($arr["locked"] == 'yes' && get_user_class() < UC_MODERATOR)
        stderr("Error", "This topic is locked.");
//=== PM subscribed peeps
$res_sub = sql_query("SELECT userid FROM subscriptions  WHERE topicid = $topicid") or sqlerr(__FILE__, __LINE__);
while($row = mysql_fetch_assoc($res_sub)) {
$res_yes = sql_query("SELECT subscription_pm, username FROM users WHERE id = $row[userid]") or sqlerr(__FILE__, __LINE__);
$arr_yes = mysql_fetch_array($res_yes);
$msg = "Hey there!!! \n a thread you subscribed to: [b]".$arr["subject"]."[/b] has had a new post!\n click [url=".$BASEURL."/forums.php?action=viewtopic&topicid=".$topicid."&page=last][b]HERE[/b][/url] to read it!\n\nTo view your subscriptions, or un-subscribe, click [url=".$BASEURL."/subscriptions.php][b]HERE[/b][/url].\n\ncheers.";
if ($arr_yes["subscription_pm"] == 'yes' && $row["userid"] != $CURUSER["id"])
sql_query("INSERT INTO messages (sender, subject, receiver, added, msg) VALUES(0, 'New post in subscribed thread!', $row[userid], '" . get_date_time() . "', " . sqlesc($msg) . ")") or sqlerr(__FILE__, __LINE__);
}
//===end
      //---- Get forum ID

      $forumid = $arr["forumid"];
    }	 
    
    //------ Insert post
    $added = "'" . get_date_time() . "'";
    $body = sqlesc($body);
	$secsdp = 1*86400;
	$dtdp = sqlesc(get_date_time(gmtime() - $secsdp)); // calculate date.
    
     //------ Check double post     
     $doublepost = sql_query("SELECT posts.id, posts.added, posts.userid, posts.body, topics.lastpost, topics.id FROM posts INNER JOIN topics on posts.id = topics.lastpost WHERE topics.id=$topicid AND posts.userid = $userid AND posts.added > $dtdp ORDER BY added DESC	LIMIT 1") or sqlerr(__FILE__, __LINE__);
     $results = mysql_fetch_assoc($doublepost);
     if (!$results) {
			sql_query("INSERT INTO posts (topicid, userid, added, body) VALUES($topicid, $userid, $added, $body)") or sqlerr(__FILE__, __LINE__);
			$postid = mysql_insert_id() or die("Post id n/a");
			update_topic_last_post($topicid);
                                                //===add karma
                                                sql_query("UPDATE users SET seedbonus = seedbonus+1.0 WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
	                                }
	                                else {
			$oldbody = trim($results['body']);
			$newbody =  trim($_POST["body"]);
			$updatepost = sqlesc("$oldbody\n\n$newbody");
			$editedat = sqlesc(get_date_time());
	      	                sql_query("UPDATE posts SET body=$updatepost, editedat=$editedat, editedby=$userid WHERE id=$results[lastpost]") or sqlerr(__FILE__, __LINE__);
	}	
 
    // Update last post sent
    sql_query("UPDATE users SET last_post = NOW() WHERE id = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
    
    		if ($_POST['uploadattachment'] == 'yes') {

				unset($uploaderror);
				$fname = trim(stripslashes($_FILES['file']['name']));
				$size= $_FILES['file']['size'];
				$tmpname = $_FILES['file']['tmp_name'];
				$tgtfile = "$attachment_dir/$fname";				
				$pp=pathinfo($fname = $_FILES['file']['name']);

				if (empty($fname))
					$uploaderror = "Invalid Filename!";	

				if (!validfilename($fname))
					$uploaderror = "Invalid Filename!";		
				
				foreach ($allowed_file_extensions as $allowed_file_extension);
                if (!preg_match('/^(.+)\.['.join(']|[', $allowed_file_extensions).']$/si', $fname, $matches))
                $uploaderror = 'Only files with the following extensions are allowed: '.join(', ', $allowed_file_extensions).'.';
				
				if ($size > $maxfilesize)
					$uploaderror = "Sorry, that file is too large.";		
				
				if($pp['basename'] != $fname)
					$uploaderror = "Bad file name.";				

				if (file_exists($tgtfile))
					$uploaderror = "Sorry, a file with the name already exists.";	
				
				if (!is_uploaded_file($tmpname))
					$uploaderror = "Can't Upload file!";

				if (!filesize($tmpname))
					$uploaderror = "Empty file!";

				if (!$uploaderror) {
					sql_query("INSERT INTO attachments (topicid,postid,filename,size,owner,added) VALUES ('$topicid','$postid',".sqlesc($fname).", ".sqlesc($size).", '$userid', $added)") or sqlerr(__FILE__, __LINE__);
					$id = mysql_insert_id();
					move_uploaded_file($tmpname, "$attachment_dir/$fname");
				}
			}
//------ All done, redirect user to the post

    $headerstr = "Location: $BASEURL/forums.php?action=viewtopic&topicid=$topicid&page=last".($uploaderror ? "&uploaderror=$uploaderror" : "");
    		
    if ($newtopic)
      header($headerstr);

    else
      header("$headerstr#$postid");

    die;
  }

  //-------- Action: View topic

  if ($action == "viewtopic")
  {
		unset($count);
		if ( $HTTP_SERVER_VARS[ 'REQUEST_METHOD' ] == "POST" ) {
			$choice = $_POST[ 'choice' ];
			$pollid = (int)$_POST["pollid"];
			if ( $choice != "" && $choice < 256 && $choice == floor( $choice ) ){
			$res = sql_query("SELECT * FROM polls WHERE id = ".sqlesc($pollid)) or sqlerr(__FILE__, __LINE__);
			$arr = mysql_fetch_assoc($res) or die("No poll");
			$userid = (int)$CURUSER["id"];
			$res = sql_query("SELECT * FROM pollanswers WHERE pollid=".sqlesc($pollid)." && userid=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			$arr = mysql_fetch_assoc($res);
			if ($arr) die("Dupe vote");
			sql_query("INSERT INTO pollanswers VALUES(0, ".sqlesc($pollid).", ".sqlesc($userid).", ".sqlesc($choice).")") or sqlerr(__FILE__, __LINE__);

			if (mysql_affected_rows() != 1)
				stderr("Error", "An error occured. Your vote has not been counted.");
			}
			else
				stderr( "Error" , "Please select an option." );
		}
		if ($_GET['uploaderror']) {
			$msg = htmlspecialchars($_GET['uploaderror']);
?>
<script>alert("Upload Failed: <?=$msg;?>\nHowever your post successful saved!\n\nClick 'ok' to see your post.")</script>
<?
		}
		//---------------------------------
		//---- Search Highlight v0.1 by xam
		//---------------------------------	
		$highlight = htmlspecialchars(trim($_GET["highlight"]));
		//---------------------------------
		//---- Search Highlight v0.1 by xam
		//---------------------------------
		
    $topicid = 0+$_GET["topicid"];

    $page = 0+$_GET["page"];

    $userid = 0+$CURUSER["id"];

    //------ Get topic info

    $res = sql_query("SELECT * FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res) or stderr("Forum error", "Topic not found");
	if ($arr["numratings"] != 0)
    $rating =  ROUND($arr["ratingsum"] / $arr["numratings"], 1);
    $rpic = ratingpic($rating);
	$pollid = (int)$arr["pollid"];
    $locked = ($arr["locked"] == 'yes');
    $subject = $arr["subject"];
    $sticky = $arr["sticky"] == "yes";
    $forumid = $arr["forumid"];

	//------ Update hits column

    sql_query("UPDATE topics SET views = views + 1 WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    //------ Get forum

    $res = sql_query("SELECT * FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res) or die("Forum = NULL");

    $forum = $arr["name"];

    if ($CURUSER["class"] < $arr["minclassread"])
		stderr("Error", "You are not permitted to view this topic.");

    //------ Get post count

    $res = sql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $postcount = $arr[0];

    //------ Make page menu

    $pagemenu1 = "<p class=success align=center>\n";
    $perpage = $postsperpage;
    $pages = ceil($postcount / $perpage);
    if ($page[0] == "p")
  	{
	    $findpost = substr($page, 1);
	    $res = sql_query("SELECT id FROM posts WHERE topicid=$topicid ORDER BY added") or sqlerr(__FILE__, __LINE__);
	    $i = 1;
	    while ($arr = mysql_fetch_row($res))
	    {
	      if ($arr[0] == $findpost)
	        break;
	      ++$i;
	    }
	    $page = ceil($i / $perpage);
	  }

    if ($page == "last")
      $page = $pages;
    else
    {
      if($page < 1)
        $page = 1;
      elseif ($page > $pages)
        $page = $pages;
    }

    $offset = $page * $perpage - $perpage;

    for ($i = 1; $i <= $pages; ++$i)
    {
      if ($i == $page)
        $pagemenu2 .= "<b>[<u>$i</u>]</b>\n";

      else
        $pagemenu2 .= "<a href=?action=viewtopic&topicid=$topicid&page=$i><b>$i</b></a>\n";
    }

    if ($page == 1)
      $pagemenu1 .= "<img src='pic/arrow_prev.gif' border='0' alt='Previous'>";

    else
      $pagemenu1 .= "<a href=?action=viewtopic&topicid=$topicid&page=" . ($page - 1) .
        "><img src='pic/arrow_prev.gif' border='0' alt='Previous'></a>";

    $pmlb = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    if ($page == $pages)
      $pagemenu3 .= "<img src='pic/arrow_next.gif' border='0' alt='Next'>";

    else
      $pagemenu3 .= "<a href=?action=viewtopic&topicid=$topicid&page=" . ($page + 1) .
        "><img src='pic/arrow_next.gif' border='0' alt='Next'></a></p>\n";


    stdhead("Forums :: View Topic: $subject");    
        
    if ($pollid) // Display Poll
{
$res1 = sql_query("SELECT * FROM polls WHERE id = ".sqlesc($pollid)) or sqlerr(__FILE__, __LINE__);
if( $arr1 = mysql_fetch_assoc($res1) )
{
$userid = (int)$CURUSER["id"];
$question = htmlspecialchars($arr1["question"]);
$o = array($arr1["option0"], $arr1["option1"], $arr1["option2"], $arr1["option3"], $arr1["option4"],
$arr1["option5"], $arr1["option6"], $arr1["option7"], $arr1["option8"], $arr1["option9"],
$arr1["option10"], $arr1["option11"], $arr1["option12"], $arr1["option13"], $arr1["option14"],
$arr1["option15"], $arr1["option16"], $arr1["option17"], $arr1["option18"], $arr1["option19"]);

// Check if user has already voted
$res2 = sql_query("SELECT * FROM pollanswers WHERE pollid=".sqlesc($pollid)." && userid=".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
$arr2 = mysql_fetch_assoc($res2);

print("<table border=1 cellspacing=0 cellpadding=10 width=737><tr><td class=colhead2 align=left><h2>Poll");

if (get_user_class() >= UC_MODERATOR)
{
print("<font class=small>");
print(" - [<a class=altlink href=makepostpoll.php?action=edit&pollid=$arr1[id]&returnto=forums.php?action=viewtopic&topicid=$topicid><b>Edit</b></a>]\n");
print("</font>");
}
print("</h2></td></tr>\n");
print("<tr><td align=center class=clearalt7>\n");
print("<table class=main border=1 width=737 cellspacing=0 cellpadding=0><tr><td class=clearalt6>");
print("<p align=center><b>".($question)."</b></p>\n");
$voted = $arr2;
if ($voted)
{
// display results
if ($arr1["selection"]){
$uservote = $arr1["selection"];
}
else{
$uservote = -1;
}


$res3 = sql_query("SELECT selection FROM pollanswers WHERE pollid=".sqlesc($pollid)." AND selection < 20") or sqlerr(__FILE__, __LINE__);
$tvotes = mysql_num_rows($res3);

$vs = array(); // array of
$os = array();

// Count votes
while ($arr2 = mysql_fetch_row($res3)){
$vs[$arr2[0]] += 1;
}

reset($o);
for ($i = 0; $i < count($o); ++$i){
if ($o[$i]){
$os[$i] = array($vs[$i], $o[$i]);
}
}

function srt($a,$b)
{
if ($a[0] > $b[0]) return -1;
if ($a[0] < $b[0]) return 1;
return 0;
}

// now os is an array like this: array(array(123, "Option 1"), array(45, "Option 2"))
if ($arr1["sort"] == "yes"){
usort($os, "srt");
}

print("<table class=main width=737 border=0 cellspacing=0 cellpadding=0>\n");
$i = 0;
while ($a = $os[$i])
{
if ($i == $uservote){
$a[1] .= " *";
}
if ($tvotes == 0){
$p = 0;
}
else{
$p = round($a[0] / $tvotes * 100);
}
if ($i % 2){
$c = " bgcolor=#ffffff";
}
else{
$c = " bgcolor=#fffff0";
}
print("<tr><td width=1% class=embedded$c>&nbsp;&nbsp;<nobr>" . htmlspecialchars($a[1]) . " </nobr></td><td width=99% class=embedded$c>" .
"<img src=pic/bar_end.gif><img src=pic/bar.gif height=10 width=" . ($p * 3) .
"> $p%</td></tr>\n");
++$i;
}
print("</table>\n");
$tvotes = number_format($tvotes);
print("<p align=center>Votes: $tvotes</p>\n");
}
else
{
print("<form method=post action=forums.php?action=viewtopic&topicid=$topicid>\n");
print("<input type=hidden name=pollid value=$pollid>");
$i = 0;
while ($a = $o[$i])
{
print("<input type=radio name=choice value=$i>".htmlspecialchars($a)."<br>\n");
++$i;
}
print("<br>");
print("<p align=center><input type=submit value='Vote!' class=gobutton></p>");
}
print( "</td></tr></table>\n" );

$listvotes = 0 + $_GET['listvotes'];

if ((get_user_class() > UC_MODERATOR) && !$listvotes)
print("<a href=forums.php?action=viewtopic&topicid=$topicid&listvotes=1>List Voters</a>");

if ((get_user_class() > UC_MODERATOR) && $listvotes)
{
$res4 = sql_query("SELECT p.userid AS id, u.username AS name FROM pollanswers AS p LEFT JOIN users AS u ON u.id = p.userid WHERE p.pollid = $pollid") or sqlerr(__FILE__, __LINE__);


while ( $arr4 = mysql_fetch_assoc($res4) )
print(" <a href=userdetails.php?id=".$arr4['id']."><b>".$arr4['name']."</b></a> ");

}

print( "</td></tr></table>\n" );

}
else
stderr("Error","Poll doesn't exist. Contact SysOp");

}

    print("<a name=top><h2><a href=?action=viewforum&forumid=$forumid>$forum</a> &gt; ".htmlspecialchars($subject)."</h2>\n");

    print("<br><a href=subscriptions.php?topicid=$topicid&subscribe=1><b><font color=green>Subscribe to Forum</font></b></a>");
    
    //------ Get posts

    $res = sql_query("SELECT * FROM posts WHERE topicid=$topicid ORDER BY id LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__);
    
    //------ Print table

    begin_main_frame();
?>
<script language='javascript'>
/***********************************************
* Pop-it menu- © Dynamic Drive (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/

var defaultMenuWidth="90px" //set default menu width.

var linkset=new Array()
//SPECIFY MENU SETS AND THEIR LINKS. FOLLOW SYNTAX LAID OUT
linkset[0]='<p align=center><b>Rate Topic!</b></p>'
linkset[0]+='<a class=altlink href=takerate.php?topic_id=<?php echo $topicid?>&rate_me=5> 5 <img src="pic/5.gif" alt="5 - tops"></a>'
linkset[0]+='<a class=altlink href=takerate.php?topic_id=<?php echo $topicid?>&rate_me=4> 4 <img src="pic/4.gif" alt="4 - great"></a>'
linkset[0]+='<a class=altlink href=takerate.php?topic_id=<?php echo $topicid?>&rate_me=3> 3 <img src="pic/3.gif" alt="3 - ok"></a>'
linkset[0]+='<a class=altlink href=takerate.php?topic_id=<?php echo $topicid?>&rate_me=2> 2 <img src="pic/2.gif" alt="2 - eh"></a>'
linkset[0]+='<a class=altlink href=takerate.php?topic_id=<?php echo $topicid?>&rate_me=1> 1 <img src="pic/1.gif" alt="1 - bad"></a>'

////No need to edit beyond here

var ie5=document.all && !window.opera
var ns6=document.getElementById

if (ie5||ns6)
document.write('<div id="popitmenu" onMouseover="clearhidemenu();" onMouseout="dynamichide(event)"></div>')

function iecompattest(){
return (document.compatMode && document.compatMode.indexOf("CSS")!=-1)? document.documentElement : document.body
}

function showmenu(e, which, optWidth){
if (!document.all&&!document.getElementById)
return
clearhidemenu()
menuobj=ie5? document.all.popitmenu : document.getElementById("popitmenu")
menuobj.innerHTML=which
menuobj.style.width=(typeof optWidth!="undefined")? optWidth : defaultMenuWidth
menuobj.contentwidth=menuobj.offsetWidth
menuobj.contentheight=menuobj.offsetHeight
eventX=ie5? event.clientX : e.clientX
eventY=ie5? event.clientY : e.clientY
//Find out how close the mouse is to the corner of the window
var rightedge=ie5? iecompattest().clientWidth-eventX : window.innerWidth-eventX
var bottomedge=ie5? iecompattest().clientHeight-eventY : window.innerHeight-eventY
//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<menuobj.contentwidth)
//move the horizontal position of the menu to the left by it's width
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX-menuobj.contentwidth+"px" : window.pageXOffset+eventX-menuobj.contentwidth+"px"
else
//position the horizontal position of the menu where the mouse was clicked
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX+"px" : window.pageXOffset+eventX+"px"
//same concept with the vertical position
if (bottomedge<menuobj.contentheight)
menuobj.style.top=ie5? iecompattest().scrollTop+eventY-menuobj.contentheight+"px" : window.pageYOffset+eventY-menuobj.contentheight+"px"
else
menuobj.style.top=ie5? iecompattest().scrollTop+event.clientY+"px" : window.pageYOffset+eventY+"px"
menuobj.style.visibility="visible"
return false
}

function contains_ns6(a, B) {
//Determines if 1 element in contained in another- by Brainjar.com
while (b.parentNode)
if ((b = b.parentNode) == a)
return true;
return false;
}

function hidemenu(){
if (window.menuobj)
menuobj.style.visibility="hidden"
}

function dynamichide(e){
if (ie5&&!menuobj.contains(e.toElement))
hidemenu()
else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
hidemenu()
}

function delayhidemenu(){
delayhide=setTimeout("hidemenu()",500)
}

function clearhidemenu(){
if (window.delayhide)
clearTimeout(delayhide)
}

if (ie5||ns6)
document.onclick=hidemenu

</script>
<?
    echo"<a class= altlink href=\"#\" onMouseover=\"showmenu(event,linkset[0])\" onMouseout=\"delayhidemenu()\"><b>Topic Rating : </b> $rpic</a>";
    begin_frame();

    $pc = mysql_num_rows($res);

    $pn = 0;
    
    $r = sql_query("SELECT lastpostread FROM readposts WHERE userid=" . $CURUSER["id"] . " AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);

    $a = mysql_fetch_row($r);

    $lpr = $a[0];

    //..rp..
/* if (!$lpr)
sql_query("INSERT INTO readposts (userid, topicid) VALUES($userid, $topicid)") or sqlerr(__FILE__, __LINE__);
*/
//..rp..

      while ($arr = mysql_fetch_assoc($res))
    {
      ++$pn;

      $postid = $arr["id"];
      $postadd = $arr['added']; // ..rp..
      $posterid = $arr["userid"];

      $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . ")";
      
      $at = sql_query("SELECT * FROM attachments WHERE topicid=$topicid AND postid=$postid") or sqlerr(__FILE__, __LINE__);
	  if (mysql_num_rows($at) == 0) {
			unset($at);
			$at = sql_query("SELECT * FROM attachments WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);
	  }
	  $resat = mysql_fetch_assoc($at);
      //---- Get poster details	
	
	$res22 = sql_query("SELECT * FROM users WHERE id=$posterid") or sqlerr(__FILE__, __LINE__);
	
	$arr2 = mysql_fetch_assoc($res22);	
	$uploaded = mksize($arr2["uploaded"]);
	$downloaded = mksize($arr2["downloaded"]);
	unset($last_access,$invisible);
	$last_access = $arr2["last_access"];
	$invisible = $arr2["invisible"];
if ($arr2["downloaded"] > 0)

{

$ratio = $arr2['uploaded'] / $arr2['downloaded'];

$ratio = number_format($ratio, 3);

$color = get_ratio_color($ratio);

if ($color)

 $ratio = "<font color=$color>$ratio</font>";

}

else

if ($arr2["uploaded"] > 0)

    $ratio = "Inf.";

else

$ratio = "---";

$rem = sql_query("SELECT COUNT(*) FROM posts WHERE userid=" . $posterid) or sqlerr(__FILE__, __LINE__);
 $arr25 = mysql_fetch_row($rem);
 $forumposts = $arr25[0];
      
      $signature = $arr2[signature];
	  $signature = ($CURUSER["signatures"] == "yes" ? $arr2["signature"] : "");

      $postername = $arr2["username"];
      $showgroup =  ($arr2["usergroup"] <> "" ? "<center>Gruppe:<br>" . $arr2["usergroup"] . "</center><br>" : "");
      //$showgroup = "<center>Gruppe:<br>" . $arr2["usergroup"] . "</center><br>";
      if ($postername == "")
      {
        $by = "unknown[$posterid]";

        $avatar = "";
      }
      else
      {
//		if ($arr2["enabled"] == "yes")
	        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($arr2["avatar"]) : "");
//	    else
//			$avatar = "pic/disabled_avatar.gif";

        $title = $arr2["title"];

        if (!$title)
          $title = get_user_class_name($arr2["class"]);
          
        $uclass = $UC[get_user_class_name($arr2["class"])];
//////////////forum rep mod//////////////////////        
$resrep = sql_query("SELECT id FROM posts WHERE userid=$posterid");
$numberofposts = mysql_num_rows($resrep);
if ($numberofposts >=1 && $numberofposts <= 100)
$forumrep = "No Rep Yet";
if($numberofposts >=101 && $numberofposts <= 200)
$forumrep = "<img src=pic/1.gif border=0 alt='Rep 1'>";
if($numberofposts >=201 && $numberofposts <= 300)
$forumrep = "<img src=pic/2.gif border=0 alt='Rep 2'>";
if($numberofposts >=301 && $numberofposts <= 400)
$forumrep = "<img src=pic/3.gif border=0 alt='Rep 3'>";
if($numberofposts >=401 && $numberofposts <= 500)
$forumrep = "<img src=pic/4.gif border=0 alt='Rep 4'>";
if($numberofposts >=501 && $numberofposts <= 600)
$forumrep = "<img src=pic/5.gif border=0 alt='Rep 5'>";
if($numberofposts >=601 && $numberofposts <= 700)
$forumrep = "<img src=pic/6.gif border=0 alt='Rep 6'>";
if($numberofposts >=701 && $numberofposts <= 800)
$forumrep = "<img src=pic/7.gif border=0 alt='Rep 7'>";
if($numberofposts >=801 && $numberofposts <= 900)
$forumrep = "<img src=pic/8.gif border=0 alt='Rep 8'>";
if($numberofposts >=901 && $numberofposts <= 1000)
$forumrep = "<img src=pic/9.gif border=0 alt='Rep 9'>";
if ($numberofposts >=1001)
$forumrep = "<img src=pic/10.gif border=0 alt='Rep 10'>";
/////////////////forum reputation mod////////////////////
$by = "<a href=userdetails.php?id=$posterid><b>$postername</b></a>" . ($arr2["donor"] == "yes" ? "<img src=".
"pic/star.gif alt='Donor'>" : "") . ($arr2["enabled"] == "no" ? "<img src=".
"pic/disabled.gif alt=\"This account is disabled\" style='margin-left: 2px'>" : ($arr2["warned"] == "yes" ? "<a href=rules.php#warning class=altlink><img src=pic/warned.gif alt=\"Warned\" border=0></a>" : "")) . " ";
      }

      if (!$avatar)
        $avatar = "pic/default_avatar.gif";

      print("<a name=$postid>\n");

      if ($pn == $pc)
      {
        print("<a name=last>\n");
        //..rp..
/* if ($postid > $lpr)
sql_query("UPDATE readposts SET lastpostread=$postid WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);
*/
//..rp..
      }

      print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded width=737>#$postid by $by $added");
    
      print("</td><td class=embedded width=1%><a href=#top><img src=pic/p_up.gif border=0 alt='Top'></a></td></tr>");

      print("</table></p>\n");

      begin_table(true);

      $body = format_comment($arr["body"]);
      
		//---------------------------------
		//---- Search Highlight v0.1 by xam
		//---------------------------------
      	if ($highlight){
	      	$body = highlight($highlight,$body);
	      	}
		//---------------------------------
		//---- Search Highlight v0.1 by xam
		//---------------------------------

      if (is_valid_id($arr['editedby']))
      {
        $res2 = sql_query("SELECT username, class FROM users WHERE id=$arr[editedby]");
        if (mysql_num_rows($res2) == 1)
        {
          $arr2 = mysql_fetch_assoc($res2);
          $body .= '<p><font size=1 class=small>Last edited by <a href=userdetails.php?id='.$arr['editedby'].'><b>'.$arr2['username'].'</b></a> at '.$arr['editedat'].' GMT '.((get_user_class() >= UC_MODERATOR && $row['post_history'] != '') ? ' <a class=altlink href='.$DEFAULTBASEURL.'/forums.php?action=view_post_history&post='.$postid.'>read post history</a>' : '').'</font></p>';
        }
      }
	  
      if ($resat['filename'] AND $resat['id']){
		   if ($resat['postid'] == $postid) {			  
				foreach ($allowed_file_extensions as $allowed_file_extension)
                if (substr($arr['at_filename'], -3) == $allowed_file_extension)
                $aimg = $allowed_file_extension;
				$body .= "<div style=\"padding:6px\"><fieldset class=\"fieldset\">
					<legend>Attached Files</legend>

					<table cellpadding=\"0\" cellspacing=\"3\" border=\"0\" class=\"none\">
					<tr class=\"none\">
					<td class=\"none\"><img class=\"inlineimg\" src=\"pic/$aimg.gif\" width=\"16\" height=\"16\" border=\"0\" style=\"vertical-align:baseline\" />&nbsp;</td>
					<td class=\"none\"><a href=\"attachment.php?attachmentid=$resat[id]\">".htmlspecialchars($resat[filename])."</a> (".mksize($resat[size]).", $resat[downloads] views)</td>
					<td class=\"none\">&nbsp;&nbsp;<input type=\"button\" class=\"none\" value=\"See who downloaded\" tabindex=\"1\" onclick=\"window.open('whodownloaded.php?fileid=$resat[id]','whodownloaded','toolbar=no, scrollbars=yes, resizable=yes, width=600, height=250, top=50, left=50'); return false;\" />".(get_user_class() >= UC_MODERATOR ? "&nbsp;&nbsp;<input type=\"button\" class=\"gobutton\" value=\"Delete\" tabindex=\"2\" onclick=\"window.open('attachment.php?action=delete&attachmentid=$resat[id]','attachment','toolbar=no, scrollbars=yes, resizable=yes, width=600, height=250, top=50, left=50'); return false;\"\" />" : "")."</td>
					</tr>
					</table>
					</fieldset>
					</div>";
		   }
      }
      	
      if ($signature)
 	  $body .= "<p style='vertical-align:bottom'><br>____________________<br>" . format_comment($signature,false) . "</p>";
    
      "</td>";


      //$stats = "<br>"."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Posts: $forumposts<br>"."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;UL: $uploaded <br>"."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DL: $downloaded<br>"."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ratio.: $ratio";
      	//unset($onoffpic,$dt);
        $stats = "<font class=small>Class: ".get_user_class_name($arr2["class"])."<br>"."Posts: $forumposts<br>"."Uploaded: $uploaded <br>"."Downloaded: $downloaded<br>"."Ratio: $ratio<br>";



       $dt = get_date_time(gmtime() - 180);
		if ($invisible == "yes" AND get_user_class() < UC_MODERATOR AND $posterid != $CURUSER[id])
			$onoffpic = "<img src=pic/user_offline.gif border=0>";
		elseif ($last_access > $dt OR $posterid == $CURUSER[id])
			$onoffpic = "<img src=pic/user_online.gif border=0>";
		else
			$onoffpic = "<img src=pic/user_offline.gif border=0>";
     print("<tr valign=top><td width=150 align=left style='padding: 0px'><br>"."&nbsp; " .
       ($avatar ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img width=100 src=\"$avatar\">": ""). "<br><br><br>"."$showgroup Reputation: $forumrep<br>$stats<br></td><td class=comment>$body</td></tr>\n");
	print("<tr><td> $onoffpic <a href=\"sendmessage.php?receiver=".htmlspecialchars($posterid)."\"> <img src=\"pic/pm.gif\" border=\"0\" alt=\"Nachricht an ".htmlspecialchars($postername)."\"></a> <a href=report.php?type=Post&id=$postid&id_2=$topicid><img src=\"pic/report.gif\" border=\"0\" alt=\"Report this post\"></a></td>");

	print("<td align=right>");
	if (!$locked || get_user_class() >= UC_MODERATOR)				
		print("<a href=?action=quotepost&topicid=$topicid&postid=$postid><img src=\"pic/p_quote.gif\" border=\"0\" alt=\"Reply with Quote\"></a>");

$arr = get_forum_access_levels($forumid) or die;
if (get_user_class() >= $arr["write"])
	$maypost = true;

	if ($maypost)
	    {
		      //print("<a href='#fastreply' onclick=\"return toggleMe('fastreply')\"><img src='pic/p_fastreply.gif' border='0'  alt='Fast Reply' /></a>");
		      print("<a href=?action=reply&topicid=$topicid><img src=\"pic/p_reply.gif\" border=\"0\" alt=\"Reply directly to this post\"></a>");
	    }	
	if (get_user_class() >= UC_MODERATOR)
        print("<a href=?action=deletepost&postid=$postid><img src=\"pic/p_delete.gif\" border=\"0\" alt=\"Delete Post\"></a>");
	
	if (($CURUSER["id"] == $posterid && !$locked) || get_user_class() >= UC_MODERATOR)
        print("<a href=?action=editpost&postid=$postid><img src=\"pic/p_edit.gif\" border=\"0\" alt=\"Edit Post\"></a>");
	print("</td></tr>");
	
end_table();
}
//--- if owner / mod or above add poll options---//
$res999 = sql_query("SELECT userid FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
$arr999 = mysql_fetch_array($res999);
$userid999 = $arr999["userid"];    
if (($CURUSER["id"] == $userid999 || get_user_class() >= UC_MODERATOR)){
if(!$pollid) { // Only create a poll if is doesn't exist
print("<table style='border:1px solid #000000;' width=737><tr><td colspan=2><form method=post action=makepostpoll.php>\n");
print("<input type=hidden name=topicid value=".htmlspecialchars($topicid).">\n");
print("<input type=hidden name=returnto value=\"".htmlspecialchars($HTTP_SERVER_VARS["REQUEST_URI"])."\">\n");
print("<p align=right class=success><img src=pic/poll.gif>   <b>Add Poll: </b>\n");
print("<input  type=submit class=gobutton value='Make Poll'>");
print("</form>\n");
print("</p></tr></td></table>");
}
}
?>
<div id="fastreply" style="display:none">
<table style='border:1px solid #000000;' width=737><tr>
<td colspan=2 class=colhead><b>Fast Reply</b></td></tr>
<tr><td>
<form name=compose method=post action=?action=post>
<input type=hidden name=topicid value=<?=$topicid;?>>
<textarea name="body" id="body" rows="7" cols="72"></textarea>
<center><input type=submit class=gobutton value="Add Reply">
<input type=button class=gobutton name=button value=Preview  onclick="javascript:get(this.parentNode);">
<img id="loading" style=" visibility: hidden" src="pic/ajax-loader.gif">
</center>
</td>
<td>
<div align=center class=success>
<a href="javascript: SmileIT(';-)','compose','body')"><img src=pic/smilies/wink.gif width="20" height="20" border=0></a><a href="javascript: SmileIT(':-P','compose','body')"><img src=pic/smilies/tongue.gif width="20" height="20" border=0></a><a href="javascript: SmileIT(':-)','compose','body')"><img border=0 src=pic/smilies/smile1.gif></a><a href="javascript: SmileIT(':w00t:','compose','body')"><img border=0 src=pic/smilies/w00t.gif></a><br><a href="javascript: SmileIT(':-D','compose','body')"><img border=0 src=pic/smilies/grin.gif></a><a href="javascript: SmileIT(':lol:','compose','body')"><img border=0 src=pic/smilies/laugh.gif></a><a href="javascript: SmileIT(':-/','compose','body')"><img border=0 src=pic/smilies/confused.gif></a><a href="javascript: SmileIT(':-(','compose','body')"><img border=0 src=pic/smilies/sad.gif></a><br><a href="javascript: SmileIT(':-O','compose','body')"><img src=pic/smilies/ohmy.gif border=0></a><a href="javascript: SmileIT('8-)','compose','body')"><img src=pic/smilies/cool1.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':sly:','compose','body')"><img src=pic/smilies/sly.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':greedy:','compose','body')"><img src=pic/smilies/greedy.gif width="18" height="18" border=0></a><br><a href="javascript: SmileIT(':weirdo:','compose','body')"><img src=pic/smilies/weirdo.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':sneaky:','compose','body')"><img src=pic/smilies/sneaky.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':wacko:','compose','body')"><img src=pic/smilies/wacko.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':?:','compose','body')"><img src=pic/smilies/question.gif width="18" height="18" border=0></a><br><a href="javascript: SmileIT(':!:','compose','body')"><img src=pic/smilies/excl.gif width="18" height="18" border=0></a><a href="javascript: SmileIT(':unsure:','compose','body')"><img src=pic/smilies/unsure.gif width="20" height="20" border=0></a>
</div>
<div align=center class=success><a href="javascript:winop();">More Smiles</a></div>
</td>
</form></tr></table>
</div>
<span name="preview" id="preview"></span>
<?
    
    //..rp..
if (($postid > $lpr) AND ($postadd > (get_date_time(gmtime() - $READPOST_EXPIRY)))) {

if ($lpr)
sql_query("UPDATE readposts SET lastpostread=$postid ".
"WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);
else
sql_query("INSERT INTO readposts (userid, topicid, lastpostread) ".
"VALUES($userid, $topicid, $postid)") or sqlerr(__FILE__, __LINE__);

}
//..rp..
    
    //------ Mod options

	  if (get_user_class() >= UC_MODERATOR)
	  {
	    attach_frame();

	    $res = sql_query("SELECT id,name,minclasswrite FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);
	    print("<table border=0 cellspacing=0 cellpadding=0>\n");

	    print("<form method=post action=?action=setsticky>\n");
	    print("<input type=hidden name=topicid value=$topicid>\n");
	    print("<input type=hidden name=returnto value=$_SERVER[REQUEST_URI]>\n");
	    print("<tr><td class=embedded align=right>Sticky:</td>\n");
	    print("<td class=embedded><input type=radio name=sticky value='yes' " . ($sticky ? " checked" : "") . "> Yes <input type=radio name=sticky value='no' " . (!$sticky ? " checked" : "") . "> No\n");
	    print("<input type=submit value='Ok' class=gobutton></td></tr>");
	    print("</form>\n");

	    print("<form method=post action=?action=setlocked>\n");
	    print("<input type=hidden name=topicid value=$topicid>\n");
	    print("<input type=hidden name=returnto value=$_SERVER[REQUEST_URI]>\n");
	    print("<tr><td class=embedded align=right>Locked:</td>\n");
	    print("<td class=embedded><input type=radio name=locked value='yes' " . ($locked ? " checked" : "") . "> Yes <input type=radio name=locked value='no' " . (!$locked ? " checked" : "") . "> No\n");
	    print("<input type=submit value='Ok' class=gobutton></td></tr>");
	    print("</form>\n");

	    print("<form method=post action=?action=renametopic>\n");
	    print("<input type=hidden name=topicid value=$topicid>\n");
	    print("<input type=hidden name=returnto value=$_SERVER[REQUEST_URI]>\n");
	    print("<tr><td class=embedded align=right>Rename topic:</td><td class=embedded><input type=text name=subject size=60 maxlength=$maxsubjectlength value=\"" . htmlspecialchars($subject) . "\">\n");
	    print("<input type=submit value='Ok' class=gobutton></td></tr>");
	    print("</form>\n");

	    print("<form method=post action=?action=movetopic&topicid=$topicid>\n");
	    print("<tr><td class=embedded>Move to:&nbsp;</td><td class=embedded><select name=forumid>");

	    while ($arr = mysql_fetch_assoc($res))
	      if ($arr["id"] != $forumid && get_user_class() >= $arr["minclasswrite"])
	        print("<option value=" . $arr["id"] . ">" . $arr["name"] . "\n");

	    print("</select> <input type=submit value='Okay' class=gobutton></form></td></tr>\n");
	    print("<tr><td class=embedded>Delete Topic</td><td class=embedded>\n");
        print("<form method=post action=forums.php?action=deletetopic>\n");
        print("<input type=hidden name=topicid value=$topicid>\n");
        print("<input type=hidden name=forumid value=$forumid>\n");
        print("<input type=checkbox name=sure value=1>Im sure\n");
        print("<input type=submit value='Ok' class=gobutton>\n");
        print("</form>\n");
        print("</td></tr>\n");
	    print("</table>\n");
	    }

  	end_frame();

  	end_main_frame();

  	print("$pagemenu1 $pmlb $pagemenu2 $pmlb $pagemenu3");

if ($locked && get_user_class() < UC_MODERATOR)
  		print("<p>This topic is locked; no new posts are allowed.</p>\n");

  	else
  	{
	    $arr = get_forum_access_levels($forumid) or die;

	    if (get_user_class() < $arr["write"])
	      print("<p><i>You are not permitted to post in this forum.</i></p>\n");

	    else
	      $maypost = true;
	  }

	  //------ "View unread" / "Add reply" buttons

	  print("<p><table class=main border=0 cellspacing=0 cellpadding=0><tr>\n");
	  print("<td class=embedded><form method=get action=?>\n");
	  print("<input type=hidden name=action value=viewunread>\n");
	  print("<input type=submit value='Show new' class=btn>\n");
	  print("</form></td>\n");

    if ($maypost)
    {
      print("<td class=embedded style='padding-left: 10px'><form method=get action=?>\n");
      print("<input type=hidden name=action value=reply>\n");
      print("<input type=hidden name=topicid value=$topicid>\n");
      print("<input type=submit value='Answer' class=btn>\n");
      print("</form></td>\n");
    }
    print("</tr></table></p>\n");
    if ($maypost)
{
print("<table style='border:1px solid #000000;'><tr>");
print("<td style='padding:10px;text-align:center;'><p><b>Quick Reply</b></p>");
print("<form name=compose method=post action=?action=post>");
print("<input type=hidden name=topicid value=$topicid>");
print("<textarea name=\"body\" rows=\"4\" cols=\"70\"></textarea><br />");
print("<input type=submit class=btn value=\"Answer\">");
print("</form></td></tr></table>\n");
}


    //------ Forum quick jump drop-down

    insert_quick_jump_menu($forumid);

    stdfoot();

    die;
  }

  //-------- Action: Quote

	if ($action == "quotepost")
	{
		$topicid = 0+$_GET["topicid"];

      stdhead("Post reply");

    begin_main_frame();

    insert_compose_frame($topicid, false, true);
   
     stdfoot();
   
    end_main_frame();

    die;
  }

  //-------- Action: Reply

  if ($action == "reply")
  {
    $topicid = 0+$_GET["topicid"];

    stdhead("Post reply");

    begin_main_frame();

    insert_compose_frame($topicid, false, false, true);

    stdfoot();

    end_main_frame();

    die;
  }

  //-------- Action: Move topic

  if ($action == "movetopic")
  {
    $forumid = 0+$_POST["forumid"];
    $topicid = 0+$_GET["topicid"];

    if (!is_valid_id($forumid) || !is_valid_id($topicid) || get_user_class() < UC_MODERATOR)
      die;

    // Make sure topic and forum is valid

    $res = @sql_query("SELECT minclasswrite FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      stderr("Error", "Forum not found.");

    $arr = mysql_fetch_row($res);

    if (get_user_class() < $arr[0])
      die;

    $res = @sql_query("SELECT forumid FROM topics WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
   if (mysql_num_rows($res) != 1)
     stderr("Error", "Topic not found.");
   $arr = mysql_fetch_row($res);
   $old_forumid=$arr[0];

   // get posts count
   $res = sql_query("SELECT COUNT(id) AS nb_posts FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);
   if (mysql_num_rows($res) != 1)
     stderr("Error", "Couldn't get posts count.");
   $arr = mysql_fetch_row($res);
   $nb_posts = $arr[0];

   // move topic
   if ($old_forumid != $forumid)
   {
     @sql_query("UPDATE topics SET forumid=$forumid WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
	 
     // update counts
     @sql_query("UPDATE forums SET topiccount=topiccount-1, postcount=postcount-$nb_posts WHERE id=$old_forumid") or sqlerr(__FILE__, __LINE__);
     @sql_query("UPDATE forums SET topiccount=topiccount+1, postcount=postcount+$nb_posts WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);
   }

    // Redirect to forum page

    header("Location: $BASEURL/forums.php?action=viewforum&forumid=$forumid");

    die;
  }

  //-------- Action: Delete topic
if ($action == 'deletetopic' && get_user_class() >= UC_MODERATOR) //-------- Action: Delete topic
{
    $forumid = (int)(isset($_GET['forumid']) ? $_GET['forumid'] : $_POST['forumid']);
    //int_check($forumid, true); //use this if you have this function and comment the if and the stderr
    if (!is_valid_id($forumid))
        stderr('Error', 'Invalid ID');
    
    $topicid = (int)(isset($_GET['topicid']) ? $_GET['topicid'] : $_POST['topicid']);
    //int_check($topicid, true); //use this if you have this function and comment the if and the stderr
    if (!is_valid_id($topicid))
        stderr('Error', 'Invalid ID');
    
    $sure = (int)(isset($_GET['sure']) ? $_GET['sure'] : $_POST['sure']);
    if (!$sure)
        stderr("Sanity check...", "You are about to delete a topic. Click <a href=/forums.php?action=deletetopic&forumid=$forumid&topicid=$topicid&sure=1>here</a> if you are sure.");

    $res = sql_query("SELECT attachments.filename ".
                       "FROM posts ".
                       "LEFT JOIN attachments ON attachments.postid = posts.id ".
                       "WHERE posts.topicid = ".sqlesc($topicid));
    while ($arr = mysql_fetch_assoc($res))
        if (!empty($arr['filename']))
            @unlink($attachment_dir."/".$arr['filename']);
    
    sql_query("DELETE posts.*, attachments.*, attachmentdownloads.*, topics.*, polls.*, pollanswers.* ".
                "FROM posts ".
                "LEFT JOIN attachments ON attachments.postid = posts.id ".
                "LEFT JOIN attachmentdownloads ON attachmentdownloads.fileid = attachments.id ".
                "LEFT JOIN topics ON topics.id = posts.topicid ".
                "LEFT JOIN polls ON polls.id = topics.pollid ".
                "LEFT JOIN pollanswers ON pollanswers.pollid = polls.id ".
                "WHERE posts.topicid = ".sqlesc($topicid));
    
    header('Location: forums.php?action=viewforum&forumid='.$forumid);
    exit;
}
//-------- Action: Edit post

  if ($action == "editpost")
  {
    $postid = 0+$_GET["postid"];

    $res = sql_query("SELECT * FROM posts WHERE id=$postid") or sqlerr(__FILE__, __LINE__);

		if (mysql_num_rows($res) != 1)
			stderr("Error", "No post with this ID");

		$arr = mysql_fetch_assoc($res);

    $res2 = sql_query("SELECT locked FROM topics WHERE id = " . $arr["topicid"]) or sqlerr(__FILE__, __LINE__);
		$arr2 = mysql_fetch_assoc($res2);

 		if (mysql_num_rows($res) != 1)
			stderr("Error", "No topic associated with this post ID");

		$locked = ($arr2["locked"] == 'yes');

    if (($CURUSER["id"] != $arr["userid"] || $locked) && get_user_class() < UC_MODERATOR)
      stderr("Error", "Denied!");

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
    	$body = $_POST['body'];

    	if ($body == "")
    	  stderr("Error", "Body cannot be empty!");

      $body = sqlesc($body);

      $editedat = sqlesc(get_date_time());

      sql_query("UPDATE posts SET body=$body, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$postid") or sqlerr(__FILE__, __LINE__);

		$returnto = $_POST["returnto"];

			if ($returnto != "")
			{
				$returnto .= "&page=p$postid#$postid";
				header("Location: $returnto");
			}
			else
				stderr("Success", "Post was edited successfully.");
    }

    stdhead();

    print("<h3>Edit Post</h3>\n");
       
   print("<form name=edit method=post action=?action=editpostamp;postid=$postid>\n");
       
   print("<input type=hidden name=returnto value=\"" . htmlspecialchars($HTTP_SERVER_VARS["HTTP_REFERER"]) . "\">\n");

   print("<p align=center><table class=main border=1 cellspacing=0 cellpadding=5 width=737>\n");

   print("<tr><td class=rowhead>Body</td><td align=left style='padding: 0px'>");
       
   textbbcode("edit","body",htmlspecialchars(unesc($arr["body"])));
       
   print("</td></tr>\n");
       
  print("<tr><td align=center colspan=2><input type=submit value='Update post' class=gobutton></td></tr>\n");

   print("</table>\n</p>");

   print("</form>\n");
       
       stdfoot();

  	die;
  }

  
  if ($action == 'deletepost' && get_user_class() >= UC_MODERATOR) //-------- Action: Delete post
{
    $postid = 0 + $_GET['postid'];
    if (!is_valid_id($postid))
        stderr('Error', 'Invalid ID');

    $res = sql_query(
    "SELECT p.topicid, a.filename, (SELECT COUNT(id) FROM posts WHERE topicid=p.topicid) AS posts_count, ".
    "(SELECT MAX(id) FROM posts WHERE topicid=p.topicid AND id < p.id) AS p_id ".
    "FROM posts AS p ".
    "LEFT JOIN attachments AS a ON a.postid = p.id ".
    "WHERE p.id=".sqlesc($postid));
    $arr = mysql_fetch_assoc($res) or stderr("Error", "Post not found");
    
    $topicid = 0+$arr['topicid'];

    if ($arr['posts_count'] < 2)
      stderr("Error", "Can't delete post; it is the only post of the topic. You should\n<a href=/forums.php?action=deletetopic&topicid=$topicid>delete the topic</a> instead.\n");

    $redirtopost = (is_valid_id($arr['p_id']) ? "&page=p".$arr['p_id']."#".$arr['p_id'] : '');

    $sure = 0 + $_GET['sure'];
    if (!$sure)
        stderr("Sanity check...", "You are about to delete a post. Click <a href=/forums.php?action=deletepost&postid=$postid&sure=1>here</a> if you are sure.");

    sql_query("DELETE posts.*, attachments.*, attachmentdownloads.* ".
                "FROM posts ".
                "LEFT JOIN attachments ON attachments.postid = posts.id ".
                "LEFT JOIN attachmentdownloads ON attachmentdownloads.fileid = attachments.id ".
                "WHERE posts.id = ".sqlesc($postid));

    if (!empty($arr['filename']))
        @unlink($attachment_dir."/".$arr['filename']);

    update_topic_last_post($topicid);

    header("Location: /forums.php?action=viewtopic&topicid=".$topicid.$redirtopost);
    die;
}
  
  //-------- Action: Lock topic

  if ($action == "locktopic")
  {
    $forumid = 0+$_GET["forumid"];
    $topicid = 0+$_GET["topicid"];
    $page = 0+$_GET["page"];

    if (!is_valid_id($topicid) || get_user_class() < UC_MODERATOR)
      die;

    sql_query("UPDATE topics SET locked='yes' WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    header("Location: $BASEURL/forums.php?action=viewforum&forumid=$forumid&page=$page");

    die;
  }

  //-------- Action: Unlock topic

  if ($action == "unlocktopic")
  {
    $forumid = 0+$_GET["forumid"];

    $topicid = 0+$_GET["topicid"];

    $page = 0+$_GET["page"];

    if (!is_valid_id($topicid) || get_user_class() < UC_MODERATOR)
      die;

    sql_query("UPDATE topics SET locked='no' WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    header("Location: $BASEURL/forums.php?action=viewforum&forumid=$forumid&page=$page");

    die;
  }

  //-------- Action: Set locked on/off

  if ($action == "setlocked")
  {
    $topicid = 0 + $_POST["topicid"];

    if (!$topicid || get_user_class() < UC_MODERATOR)
      die;

	$locked = sqlesc($_POST["locked"]);
    sql_query("UPDATE topics SET locked=$locked WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    header("Location: $_POST[returnto]");

    die;
  }

  //-------- Action: Set sticky on/off

  if ($action == "setsticky")
  {
    $topicid = 0 + $_POST["topicid"];

    if (!topicid || get_user_class() < UC_MODERATOR)
      die;

	$sticky = sqlesc($_POST["sticky"]);
    sql_query("UPDATE topics SET sticky=$sticky WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

    header("Location: $_POST[returnto]");

    die;
  }

  //-------- Action: Rename topic

  if ($action == 'renametopic')
  {
  	if (get_user_class() < UC_MODERATOR)
  	  die;

  	$topicid = 0+$_POST['topicid'];

   $subject = $_POST['subject'];

  	if ($subject == '')
  	  stderr('Error', 'You must enter a new title!');

  	$subject = sqlesc($subject);

  	sql_query("UPDATE topics SET subject=$subject WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);

  	$returnto = $_POST['returnto'];

  	if ($returnto)
  	  header("Location: $returnto");

  	die;
  }

  //-------- Action: View forum

  if ($action == "viewforum")
  {
    $forumid = 0+$_GET["forumid"];

    $page = 0+$_GET["page"];

    $userid = 0+$CURUSER["id"];

    //------ Get forum name

    $res = sql_query("SELECT name, minclassread FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res) or die;

    $forumname = $arr["name"];

    if (get_user_class() < $arr["minclassread"])
      die("Not permitted");

    //------ Page links

    //------ Get topic count

    $perpage = $CURUSER["topicsperpage"];
	if (!$perpage) $perpage = 20;

    $res = sql_query("SELECT COUNT(*) FROM topics WHERE forumid=$forumid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $num = $arr[0];

    if ($page == 0)
      $page = 1;

    $first = ($page * $perpage) - $perpage + 1;

    $last = $first + $perpage - 1;

    if ($last > $num)
      $last = $num;

    $pages = floor($num / $perpage);

    if ($perpage * $pages < $num)
      ++$pages;

    //------ Build menu

    $menu1 = "<p class=success align=center>\n";

    $lastspace = false;

    for ($i = 1; $i <= $pages; ++$i)
    {
    	if ($i == $page)
        $menu2 .= "<b>[<u>$i</u>]</b>\n";

      elseif ($i > 3 && ($i < $pages - 2) && ($page - $i > 3 || $i - $page > 3))
    	{
    		if ($lastspace)
    		  continue;

  		  $menu2 .= "... \n";

     		$lastspace = true;
    	}

      else
      {
        $menu2 .= "<a href=?action=viewforum&forumid=$forumid&page=$i><b>$i</b></a>\n";

        $lastspace = false;
      }
      if ($i < $pages)
        $menu2 .= "</b>|<b>\n";
    }    

    if ($page == 1)
      $menu1 .= "<img src='pic/arrow_prev.gif' border='0' alt='Previous'>";

    else
      $menu1 .= "<a href=?action=viewforum&forumid=$forumid&page=" . ($page - 1) . "><b>&lt;&lt;&nbsp;Prev</b></a>";

    $mlb .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    if ($last == $num)
      $menu3 .= "<img src='pic/arrow_next.gif' border='0' alt='Next'></p>\n";

    else
      $menu3 .= "<a href=?action=viewforum&forumid=$forumid&page=" . ($page + 1) . "><b>Next&nbsp;&gt;&gt;</b></a></p>";
    

    $offset = $first - 1;

    //------ Get topics data

    $topicsres = sql_query("SELECT * FROM topics WHERE forumid=$forumid ORDER BY sticky, lastpost DESC LIMIT $offset,$perpage") or
      stderr("SQL Error", mysql_error());

    stdhead("Forum");





    $numtopics = mysql_num_rows($topicsres);

    print("<h1>$forumname</h1>\n");

    if ($numtopics > 0)
    {      

      print("<table border=1 cellspacing=0 cellpadding=5 width=737>");

      print("<tr><td class=colhead align=left>Topic</td><td class=colhead>Replies</td><td class=colhead>Views</td>\n" .
        "<td class=colhead align=left>Author</td><td class=colhead align=left>Last&nbsp;post</td>\n");

      print("</tr>\n");

      while ($topicarr = mysql_fetch_assoc($topicsres))
      {
        $topicid = $topicarr["id"];

        $topic_userid = $topicarr["userid"];

        $topic_views = $topicarr["views"];

		$views = number_format($topic_views);

        $locked = $topicarr["locked"] == "yes";

        $sticky = $topicarr["sticky"] == "yes";
        
        $topicpoll = $topicarr["pollid"] > 0;

        //---- Get reply count

        $res = sql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

        $arr = mysql_fetch_row($res);

        $posts = $arr[0];

        $replies = max(0, $posts - 1);

        $tpages = floor($posts / $postsperpage);

        if ($tpages * $postsperpage != $posts)
          ++$tpages;

        if ($tpages > 1)
        {
          $topicpages = " (<img src=pic/multipage.gif>";

          for ($i = 1; $i <= $tpages; ++$i)
            $topicpages .= " <a href=?action=viewtopic&topicid=$topicid&page=$i>$i</a>";

          $topicpages .= ")";
        }
        else
          $topicpages = "";

        //---- Get userID and date of last post

        $res = sql_query("SELECT * FROM posts WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

        $arr = mysql_fetch_assoc($res);

        $lppostid = 0 + $arr["id"];
        //..rp..
        $lppostadd = $arr["added"];
        // ..rp..
        $lpuserid = 0 + $arr["userid"];

        $lpadded = "<nobr>" . $arr["added"]  . "</nobr>";

        //------ Get name of last poster

        $res = sql_query("SELECT * FROM users WHERE id=$lpuserid") or sqlerr(__FILE__, __LINE__);

        if (mysql_num_rows($res) == 1)
        {
          $arr = mysql_fetch_assoc($res);

          $lpusername = "<a href=userdetails.php?id=$lpuserid><b>$arr[username]</b></a>";
        }
        else
          $lpusername = "unknown[$topic_userid]";

        //------ Get author

        $res = sql_query("SELECT username FROM users WHERE id=$topic_userid") or sqlerr(__FILE__, __LINE__);

        if (mysql_num_rows($res) == 1)
        {
          $arr = mysql_fetch_assoc($res);

          $lpauthor = "<a href=userdetails.php?id=$topic_userid><b>$arr[username]</b></a>";
        }
        else
          $lpauthor = "unknown[$topic_userid]";

        //---- Print row

        $r = sql_query("SELECT lastpostread FROM readposts WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);

        $a = mysql_fetch_row($r);

        $new = !$a || $lppostid > $a[0];
        // ..rp..
        $new = ($lppostadd > (get_date_time(gmtime() - $READPOST_EXPIRY))) ? (!$a || $lppostid > $a[0]) : 0;
        //..rp.
        $topicpic = ($locked ? ($new ? "lockednew" : "locked") : ($new ? "unlockednew" : "unlocked"));
        $topicpic = ($sticky ? ($new ? "forum_stickynew" : "forum_sticky") : $topicpic);
        $subject = ($sticky ? "Sticky: " : "") . ($topicpoll ? "<img src=pic/poll.gif alt=\"Poll:\"> " : ""). "<a href=?action=viewtopic&topicid=$topicid><b>" .
        encodehtml($topicarr["subject"]) . "</b></a>$topicpages";

        print("<tr><td align=left><table border=0 cellspacing=0 cellpadding=0><tr>" .
        "<td class=embedded style='padding-right: 5px'><img src=pic/$topicpic.gif>" .
        "</td><td class=embedded align=left>\n" .
        "$subject</td></tr></table></td><td align=right>$replies</td>\n" .
        "<td align=right>$views</td><td align=left>$lpauthor</td>\n" .
        "<td align=left>$lpadded<br>by&nbsp;$lpusername</td>\n");

        print("</tr>\n");
      } // while

      print("</table>\n");

      print($menu);

    } // if
    else
      print("<p align=center>No topics found</p>\n");
	print("$menu1 $mlb $menu2 $mlb $menu3");
    print("<p><table class=main border=0 cellspacing=0 cellpadding=0 align=center><tr valing=center>\n");

    print("<td class=embedded><img src=pic/unlockednew.gif style='margin-right: 5px'></td><td class=embedded>New posts</td>\n");

    print("<td class=embedded><img src=pic/locked.gif style='margin-left: 10px; margin-right: 5px'>" .
    "</td><td class=embedded>Locked topic</td>\n");

    print("</tr></table></p>\n");

    $arr = get_forum_access_levels($forumid) or die;

    $maypost = get_user_class() >= $arr["write"] && get_user_class() >= $arr["create"];

    if (!$maypost)
      print("<p><i>You are not permitted to start new topics in this forum.</i></p>\n");

    print("<p><table border=0 class=main cellspacing=0 cellpadding=0 align=center><tr>\n");

    print("<td class=embedded><form method=get action=?><input type=hidden " .
    "name=action value=viewunread><input type=submit value='View unread' class=gobutton></form></td>\n");

    if ($maypost)
      print("<td class=embedded><form method=get action=?><input type=hidden " .
      "name=action value=newtopic><input type=hidden name=forumid " .
      "value=$forumid><input type=submit value='New topic' class=gobutton style='margin-left: 10px'></form></td>\n");

    print("</tr></table></p>\n");

    insert_quick_jump_menu($forumid);

    stdfoot();

    die;
  }

elseif ($action == 'viewunread') //-------- Action: View unread posts
{
if ((isset($_POST[$action."_action"]) ? $_POST[$action."_action"] : '') == 'clear')
{
$topic_ids = (isset($_POST['topic_id']) ? $_POST['topic_id'] : array());

if (empty($topic_ids))
{
header('Location: '.$_SERVER['PHP_SELF'].'?action='.$action);
exit();
}

foreach ($topic_ids as $topic_id)
if (!is_valid_id($topic_id))
stderr('Error...', 'Invalid ID!');

catch_up($topic_ids);

header('Location: '.$_SERVER['PHP_SELF'].'?action='.$action);
exit();
}
else
{
$added = sqlesc(get_date_time(gmtime() - $READPOST_EXPIRY));

$res = sql_query('SELECT t.lastpost, r.lastpostread, f.minclassread '.
'FROM topics AS t '.
'LEFT JOIN posts AS p ON t.lastpost=p.id '.
'LEFT JOIN readposts AS r ON r.userid='.sqlesc((int)$CURUSER['id']).' AND r.topicid=t.id '.
'LEFT JOIN forums AS f ON f.id=t.forumid '.
'WHERE p.added > '.$added) or sqlerr(__FILE__, __LINE__);
$count = 0;
while($arr = mysql_fetch_assoc($res))
{
if ($arr['lastpostread'] >= $arr['lastpost'] || $CURUSER['class'] < $arr['minclassread'])
continue;

$count++;
}
mysql_free_result($res);

if ($count)
{
list($pagertop, $pagerbottom, $limit) = pager(25, $count, $_SERVER['PHP_SELF'].'?action='.$action.'&');

stdhead(); begin_main_frame();

echo '<h1 align=center>Topics with unread posts</h1>';

echo '<p>'.$pagertop.'</p>';

?>
<script language="javascript">
var checkflag = "false";

function check(a)
{
if (checkflag == "false")
{
for(i=0; i < a.length; i++)
a[i].checked = true;

checkflag = "true";

value = "Uncheck";
}
else
{
for(i=0; i < a.length; i++)
a[i].checked = false;

checkflag = "false";

value = "Check";
}

return value + " All";
};
</script>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?action='.$action; ?>">
<input type="hidden" name="<?php echo $action.'_action'; ?>" value="clear" />
<?php

begin_table(true);

?>
<tr align="left">
<td class="colhead" colspan="2">Topic</td>
<td class="colhead" width="1%">Clear</td>
</tr>
<?php

$res = sql_query('SELECT t.id, t.forumid, t.subject, t.lastpost, r.lastpostread, f.name, f.minclassread '.
'FROM topics AS t '.
'LEFT JOIN posts AS p ON t.lastpost=p.id '.
'LEFT JOIN readposts AS r ON r.userid='.sqlesc((int)$CURUSER['id']).' AND r.topicid=t.id '.
'LEFT JOIN forums AS f ON f.id=t.forumid '.
'WHERE p.added > '.$added.' '.
'ORDER BY t.forumid '.$limit) or sqlerr(__FILE__, __LINE__);

while($arr = mysql_fetch_assoc($res))
{
if ($arr['lastpostread'] >= $arr['lastpost'] || $CURUSER['class'] < $arr['minclassread'])
continue;

?>
<tr>
<td align="center" width="1%">
<img src='<?php echo $pic_base_url; ?>/unlockednew.gif'>
</td>
<td align="left">
<!--<a href='<?php echo $_SERVER['PHP_SELF']; ?>?action=viewtopic&topicid=<?php echo (int)$arr['id']; ?>&page=last#last'><?php echo htmlspecialchars($arr['subject']); ?></a><br />in&nbsp;<font class="small"><a href='<?php echo $_SERVER['PHP_SELF']; ?>?action=viewforum&forumid=<?php echo (int)$arr['forumid']; ?>'><?php echo htmlspecialchars($arr['name']); ?></a></font>--->
<a href='forums.php?action=viewtopicamp;topicid=<?php echo (int)$arr['id']; ?>amp;page=last#last'><?php echo htmlspecialchars($arr['subject']); ?></a><br />in&nbsp;<font class="small"><a href='<?php echo $_SERVER['PHP_SELF']; ?>?action=viewforum&forumid=<?php echo (int)$arr['forumid']; ?>'><?php echo htmlspecialchars($arr['name']); ?></a></font>

</td>                                                                      
<td align="center">
<input type="checkbox" name="topic_id[]" value="<?php echo (int)$arr['id']; ?>" />
</td>
</tr>
<?php
}
mysql_free_result($res);

?>
<tr>
<td align="center" colspan="3">
<input type='button' value="Check All" onClick="this.value = check(form);">&nbsp;<input type="submit" value="Clear selected" />
</td>
</tr>
<?php

end_table();

?>
</form>
<?php


echo '<p>'.$pagerbottom.'</p>';

echo '<div align="center"><a href="'.$_SERVER['PHP_SELF'].'?catchup">Mark all posts as read</a></div>';

end_main_frame(); stdfoot();die();
}
else
stderr("Sorry...", "There are no unread posts.<br /><br />Click <a href=".$_SERVER['PHP_SELF']."?action=getdaily>here</a> to get today's posts (last 24h).");
}
}
  if ($action == "getdaily") {
	stdhead("Today Posts (Last 24 Hours)");
	$page = 0 + $_GET["page"];
	$perpage = 10;
	$r = sql_query("SELECT posts.id AS pid, posts.topicid, posts.userid AS userpost, posts.added, topics.id AS tid, topics.subject, topics.forumid, topics.lastpost, topics.views, forums.name, forums.minclassread, forums.topiccount, users.username
		FROM posts, topics, forums, users, users AS topicposter
		WHERE posts.topicid = topics.id AND posts.added >= DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) AND topics.forumid = forums.id AND posts.userid = users.id AND topics.userid = topicposter.id AND minclassread <=" . $CURUSER["class"] . "
		ORDER BY posts.added DESC") or sqlerr(__FILE__,__LINE__);
	$countrows = number_format(mysql_num_rows($r)) + 1;
	mysql_free_result($r);
	list($pagertop, $pagerbottom, $limit) = pager($perpage, $countrows, "forums.php?action=getdaily&");
	print("<table width=100% id=torrenttable border=1 cellspacing=0 cellpadding=5><tr><br><h2>Today Posts (Last 24 Hours)</h2>".
	"<td class=colhead align=left>Topic Title</td>".
	"<td class=colhead align=center>Views</td>".
	"<td class=colhead align=center>Author</td>".
	"<td class=colhead align=left>Posted At</td>".
	"</tr>");
	$res = sql_query("SELECT posts.id AS pid, posts.topicid, posts.userid AS userpost, posts.added, topics.id AS tid, topics.subject, topics.forumid, topics.lastpost, topics.views, forums.name, forums.minclassread, forums.topiccount, users.username
	FROM posts, topics, forums, users, users AS topicposter
	WHERE posts.topicid = topics.id AND posts.added >= DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) AND topics.forumid = forums.id AND posts.userid = users.id AND topics.userid = topicposter.id AND minclassread <=" . $CURUSER["class"] . "
	ORDER BY posts.added DESC $limit") or sqlerr(__FILE__,__LINE__);
	while ($getdaily = mysql_fetch_assoc($res))
	{		
		print("<tr><td><a href=\"forums.php?action=viewtopic&topicid={$getdaily["tid"]}&page=p{$getdaily["pid"]}#{$getdaily["pid"]}\"><b>".htmlspecialchars($getdaily["subject"])."</b></a><br />in <a href=\"forums.php?action=viewforum&forumid={$getdaily["forumid"]}\">{$getdaily["name"]}</a></td>".
		"<td align=center>{$getdaily["views"]}</td>".
		"<td align=center><a href=userdetails.php?id={$getdaily["userpost"]}><b>{$getdaily["username"]}</b></a></td>".
		"<td>{$getdaily["added"]}</td></tr>");
	}
	print("</table>");
	print("$pagerbottom");
	stdfoot();
	die;	
	}

if ($action == "search")
{
	stdhead("Forum Search");
	unset($error);
	$error= false;
	$keywords = htmlspecialchars(trim($_GET["keywords"]));
	if ($keywords != "")
	{
		$perpage = 5;
		$page = max(1, 0 + $_GET["page"]);
		$extraSql 	= "body LIKE '%".mysql_real_escape_string($keywords)."%'";	
		$res = sql_query("SELECT COUNT(*) FROM posts WHERE $extraSql") or sqlerr(__FILE__, __LINE__);
		$arr = mysql_fetch_row($res);
		$hits = 0 + $arr[0];
		if ($hits == 0)
			$error = true;
		else
		{
			$pages = 0 + ceil($hits / $perpage);
			if ($page > $pages) $page = $pages;
			for ($i = 1; $i <= $pages; ++$i)
				if ($page == $i)
					$pagemenu2 .= "<font class=gray><b>[$i]</b></font>\n";
				else
					$pagemenu2 .= "<a href=\"forums.php?action=search&keywords=$keywords&page=$i\"><b>$i</b></a>\n";
			if ($page == 1)
				$pagemenu1 = "<font class=gray><b>&lt;&lt; Prev</b></font>\n";
			else
				$pagemenu1 = "<a href=\"forums.php?action=search&keywords=$keywords&page=" . ($page - 1) . "\"><b>&lt;&lt; Prev</b></a>\n";
			$pagemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
			if ($page == $pages)
				$pagemenu3 .= "<font class=gray><b>Next &gt;&gt;</b></font>\n";
			else
				$pagemenu3 .= "<a href=\"forums.php?action=search&keywords=$keywords&page=" . ($page + 1) . "\"><b>Next &gt;&gt;</b></a>\n";
			$offset = ($page * $perpage) - $perpage;
			$res = sql_query("SELECT id, topicid,userid,added FROM posts WHERE  $extraSql LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__);
			$num = mysql_num_rows($res);
			print("<p>$pagemenu1 $pagemenu $pagemenu2 $pagemenu $pagemenu3</p>");
			print("<table border=1 cellspacing=0 cellpadding=5 width=100%>\n");
			print("<tr><td class=colhead>Post</td><td class=colhead align=left>Topic</td><td class=colhead align=left>Forum</td><td class=colhead align=left>Posted by</td></tr>\n");
			for ($i = 0; $i < $num; ++$i)
			{
				$post = mysql_fetch_assoc($res);
				$res2 = sql_query("SELECT forumid, subject FROM topics WHERE id=$post[topicid]") or
					sqlerr(__FILE__, __LINE__);
				$topic = mysql_fetch_assoc($res2);
				$res2 = sql_query("SELECT name, minclassread FROM forums WHERE id=$topic[forumid]") or
					sqlerr(__FILE__, __LINE__);
				$forum = mysql_fetch_assoc($res2);
				if ($forum["name"] == "" || $forum["minclassread"] > $CURUSER["class"])
				{
					--$hits;
					continue;
				}
				$res2 = sql_query("SELECT username FROM users WHERE id=$post[userid]") or
					sqlerr(__FILE__, __LINE__);
				$user = mysql_fetch_assoc($res2);
				if ($user["username"] == "")
					$user["username"] = "[$post[userid]]";
				//---------------------------------
				//---- Search Highlight v0.1 by xam
				//---------------------------------	
				print("<tr><td>$post[id]</td><td align=left><a href=?action=viewtopic&highlight=$keywords&topicid=$post[topicid]&page=p$post[id]#$post[id]><b>" . htmlspecialchars($topic["subject"]) . "</b></a></td><td align=left><a href=?action=viewforum&forumid=$topic[forumid]><b>" . htmlspecialchars($forum["name"]) . "</b></a><td align=left><b><a href=userdetails.php?id=$post[userid]>$user[username]</a></b><br>at $post[added]</tr>\n");
				//---------------------------------
				//---- Search Highlight v0.1 by xam
				//---------------------------------
			}
			print("</table>\n");
			print("<p>$pagemenu1 $pagemenu $pagemenu2 $pagemenu $pagemenu3</p>");
			$found ="[<b><font color=red> Found $hits post" . ($hits != 1 ? "s" : "")." </font></b> ]";
			
		}
	}
?>
<style type="text/css">
<!--
.search{
	background-image:url(pic/search.gif);
	background-repeat:no-repeat;
	width:579px;
	height:95px;
	margin:5px 0 5px 0;
	text-align:left;
}
.search_title{
	color:#555555;
	background-color:#777777;
	font-size:12px;
	font-weight:bold;
	text-align:left;
	padding:7px 0 0 15px;
}

.search_table {
  border-collapse: collapse;
  border: none;
   background-color: #ffffff; 
}
-->
</style>
<div class="search">
  <div class="search_title">Search on Forums <?=($error ? "[<b><font color=red> Nothing Found</font></b> ]" : $found)?></div>
  <div style="margin-left: 53px; margin-top: 13px;">
<form method="get" action="forums.php" id="search_form" style="margin: 0pt; padding: 0pt; font-family: Tahoma,Arial,Helvetica,sans-serif; font-size: 11px;">
<input type="hidden" name="action" value="search">
      <table border="0" cellpadding="0" cellspacing="0" width="512" class="search_table">
        <tbody>
          <tr>
          <td style="padding-bottom: 3px; border: 0;" valign="top">by keyword</td>
          </tr>
          <tr>
          <td style="padding-bottom: 3px; border: 0;" valign="top">			
			<input name="keywords" type="text" value="<?=$keywords?>" size="65" /></td>
            <td style="padding-bottom: 3px; border: 0;" valign="top"><input type=submit value=search class=gobutton></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?
	stdfoot();
	die;
}
if ($action == 'forumview')
{

  $forid = 0+$_GET["forid"];
// - Bleaches Edits
  sql_query("UPDATE users SET forum_access='" . get_date_time() . "' WHERE id={$CURUSER["id"]}");// or sqlerr(__FILE__, __LINE__);
  $forums_res = sql_query("SELECT * FROM forums WHERE forid=$forid ORDER BY name") or sqlerr(__FILE__, __LINE__);


  //------ Get forum name

    $res = sql_query("SELECT name FROM overforums WHERE id=$forid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res) or die;

    $forumname = $arr["name"];

  stdhead("Forums");


  print("<h1><b><a href=forums.php>Forums</a></b> ->".$forumname."</h1>\n");

  print("<table border=1 cellspacing=0 cellpadding=5 width=737>\n");

  print("<tr><td class=colhead align=left>Forums</td><td class=colhead align=right>Topics</td>" .
  "<td class=colhead align=right>Posts</td>" .
  "<td class=colhead align=left>Last post</td></tr>\n");

  while ($forums_arr = mysql_fetch_assoc($forums_res))
  {
    if (get_user_class() < $forums_arr["minclassread"])
      continue;


    $forumid = $forums_arr["id"];

    $forumname = htmlspecialchars($forums_arr["name"]);

    $forumdescription = htmlspecialchars($forums_arr["description"]);

    $topiccount = number_format($forums_arr["topiccount"]);

    $postcount = number_format($forums_arr["postcount"]);

    // Find last post ID

    $lastpostid = get_forum_last_post($forumid);

    // Get last post info

    $post_res = sql_query("SELECT added,topicid,userid FROM posts WHERE id=$lastpostid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($post_res) == 1)
    {
      $post_arr = mysql_fetch_assoc($post_res) or die("Bad forum last_post");

      $lastposterid = $post_arr["userid"];

      $lastpostdate = $post_arr["added"];

      $lasttopicid = $post_arr["topicid"];

      $user_res = sql_query("SELECT username FROM users WHERE id=$lastposterid") or sqlerr(__FILE__, __LINE__);

      $user_arr = mysql_fetch_assoc($user_res);

      $lastposter = htmlspecialchars($user_arr['username']);

      $topic_res = sql_query("SELECT subject FROM topics WHERE id=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      $topic_arr = mysql_fetch_assoc($topic_res);

      $lasttopic = htmlspecialchars($topic_arr['subject']);

      $lastpost = "<nobr>$lastpostdate<br>" .
      "by <a href=userdetails.php?id=$lastposterid><b>$lastposter</b></a><br>" .
      "in <a href=?action=viewtopic&topicid=$lasttopicid&page=p$lastpostid#$lastpostid><b>$lasttopic</b></a></nobr>";

      $r = sql_query("SELECT lastpostread FROM readposts WHERE userid=$CURUSER[id] AND topicid=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      $a = mysql_fetch_row($r);

      //..rp..
$npostcheck = ($post_arr['added'] > (get_date_time(gmtime() - $READPOST_EXPIRY))) ? (!$a OR $lastpostid > $a[0]) : 0;

/* if ($a && $a[0] >= $lastpostid)
$img = "unlocked";
else
$img = "unlockednew";
*/

if ($npostcheck)
$img = "unlockednew";
else
$img = "unlocked";

// ..rp..
    }
    else
    {
      $lastpost = "N/A";
      $img = "unlocked";
    }
    print("<tr><td align=left><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded style='padding-right: 5px'><img src=".
    "/pic/$img.gif></td><td class=embedded><a href=?action=viewforum&forumid=$forumid><b>$forumname</b></a>\n" .
    ($CURUSER['class']>=UC_ADMINISTRATOR ? "<font class=small> ".
    	"[<a class=altlink href=forums.php?action=editforum&forumid=$forumid>Edit</a>] ".
        "[<a class=altlink href=forums.php?action=deleteforum&forumid=$forumid>Delete</a>]</font>" : "").
    "<br>\n$forumdescription</td></tr></table></td><td align=right>$topiccount</td></td><td align=right>$postcount</td>" .
    "<td align=left>$lastpost</td></tr>\n");
  }
// End Table Mod
print("</table>");
stdfoot();
die();
}
 
//=== action view post history
if ($action === 'view_post_history'){
$post_history = 0 + $_GET['post'];

if (get_user_class() < UC_MODERATOR)
stderr("Forum Error", "What the hell did you do?");

//=== Get info
$res = mysql_query("SELECT post_history FROM posts WHERE id=".sqlesc($post_history)) or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res);

stdhead("Post History");

echo'<br><h1>Post History</h1><a class=altlink href="javascript:history.back()">go back to thread</a><br><br>'.
'<table align=center width=80%><tr><td class=colhead>text of post with all edits</td></tr><tr>'.
'<td>'.format_comment($arr['post_history']).'</td></tr></table><br>'.
'<a class=altlink href="javascript:history.back()">go back to thread</a><br>';

stdfoot();
die;  
}

 //-------- Handle unknown action

  if ($action != "")
    stderr("Forum Error", "Unknown action");

  //-------- Default action: View forums

  if (isset($_GET["catchup"]))
    catch_up();

//-------- Get overforums --- being tested
sql_query("UPDATE users SET forum_access='" . get_date_time() . "' WHERE id={$CURUSER["id"]}");
$forums2_res = sql_query("SELECT * FROM overforums ORDER BY sort ASC") or sqlerr(__FILE__, __LINE__);
//////////////////new forum code
stdhead("Forums");
/*
$cachefile = "cache/forums".($CURUSER['class'] >= UC_ADMINISTRATOR ? 'staff' : '').".html";
if (file_exists($cachefile))
{
include($cachefile);
}
else {
ob_start();
*/
print("<h1><b>$SITENAME - Forum</b></h1>\n");
sql_query("UPDATE users SET forum_access='" . get_date_time() . "' WHERE id={$CURUSER["id"]}");// or die(mysql_error());
$forum_t = gmtime() - 180; //you can change this value to whatever span you want
$forum_t = sqlesc(get_date_time($forum_t));
$res = sql_query("SELECT id, username, avatar, donor, warned, class FROM users WHERE forum_access >= $forum_t ORDER BY forum_access DESC") or print(mysql_error());
while ($arr = mysql_fetch_assoc($res))
{
/////////////////view online users as avatars in forum////////////
	    if ($CURUSER["forumview"] == 'yes')
    	{
		if($arr["avatar"])  
		{
		  $forumusers .= "<a href=\"userdetails.php?id={$arr["id"]}\" target=\"_blank\"> <img src=" . htmlspecialchars($arr["avatar"]) . " width=\"78\" height=\"130\" alt=\"{$arr["username"]}\" title=\"{$arr["username"]}\"/> </a>";
		}
		else
		{
			$forumusers .= "<a href=\"userdetails.php?id={$arr["id"]}\" target=\"_blank\"> <img src=\"$BASEURL/pic/default_avatar.gif\" width=\"78\" height=\"130\" alt=\"{$arr["username"]}\" title=\"{$arr["username"]}\"/> </a>";
		}
	}
	else
	{
if ($forumusers) $forumusers .= ",\n";
switch ($arr["class"])
{
case UC_GOD:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_OWNER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_LEADER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_CODER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_SYSOP:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_FOUNDER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_STAFFTRAINER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_ADMINISTRATOR:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_MODERATOR:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_UPLOADER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_SUPER_VIP:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_COMMUNITY_VIP:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_VIP:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_POWER_USER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
case UC_USER:
   $arr["username"] = " <font color='#".get_user_class_color($arr['class'])."'> " . htmlspecialchars($arr['username'])."</font>";
   break;
}
$donator = $arr["donor"] === "yes";
if ($donator)
 $forumusers .= "<nobr>";
$warned = $arr["warned"] === "yes";
if ($warned)
 $forumusers .= "<nobr>";
if ($CURUSER)
$forumusers .= "<a href=userdetails.php?id={$arr["id"]}><b>{$arr["username"]}</b></a>";
else
$forumusers .= "<b>{$arr["username"]}</b>";
if ($donator)
$forumusers .= "<img src={$pic_base_url}star.gif alt='Donated {$arr["donor"]}'></nobr>";
if ($warned)
$forumusers .= "<img src={$pic_base_url}warned.gif alt='Warned {$arr["warned"]}'></nobr>";
}
}
if (!$forumusers)
$forumusers = "Nobody online ATM";

$topiccount = sql_query("select sum(topiccount) as topiccount from forums");
$row1 = mysql_fetch_array($topiccount);
$topiccount = $row1[topiccount];

$postcount = sql_query("select sum(postcount) as postcount from forums");
$row2 = mysql_fetch_array($postcount);
$postcount = $row2[postcount];
 
?>
<br>
<table width=50% border=0 cellspacing=0 cellpadding=3><tr>
<td class="colhead" align="center">Now active in Forums:</td></tr>
</tr><td class=text>
<?=$forumusers?>
<tr>
<? print("<td class=colhead align=center><h2>Our members wrote <b>" . $postcount . "</b> Posts in <b>" . $topiccount . "</b> Threads</h2></td></tr>");
/*
$fp = fopen($cachefile, 'w');
// save the contents of output buffer to the file
fwrite($fp, ob_get_contents());
// close the file
fclose($fp);
// Send the output to the browser
ob_flush();
}
*/
?>
</td></tr></table><br>

<?
print("<table border=1 cellspacing=0 cellpadding=5 width=737>\n");
while ($a = mysql_fetch_assoc($forums2_res))
        {
			$npost = 0;

			if (get_user_class() < $a["minclassview"])
				continue;

			$forid = $a["id"];
			$overforumname = $a["name"];

			print("<tr><td align=left class=colhead><a href=?action=forumview&forid=$forid><b><font color=white>".$overforumname."</font></b></a></td><td align=right class=colhead><font color=white><b>Topics</b></td>" .
			"<td align=right class=colhead><font color=white><b>Posts</b></font></td>" .
			"<td align=left class=colhead><font color=white><b>Last post</b></font></td></tr>\n");

			$forums_res = sql_query("SELECT * FROM forums WHERE forid=$forid ORDER BY forid ASC") or sqlerr(__FILE__, __LINE__);

			while ($forums_arr = mysql_fetch_assoc($forums_res))
			{
				if (get_user_class() < $forums_arr["minclassread"])
					continue;

				$forumid = $forums_arr["id"];
				$forumname = htmlspecialchars($forums_arr["name"]);
				$forumdescription = htmlspecialchars($forums_arr["description"]);
				$topiccount = number_format($forums_arr["topiccount"]);
				$postcount = number_format($forums_arr["postcount"]);

				// Find last post ID

				$lastpostid = get_forum_last_post($forumid);

				// Get last post info

				$post_res = sql_query("SELECT added, topicid,userid FROM posts WHERE id=$lastpostid") or sqlerr(__FILE__, __LINE__);

				if (mysql_num_rows($post_res) == 1)
				{
					$post_arr = mysql_fetch_assoc($post_res) or die("Bad forum last_post");

					$lastposterid = $post_arr["userid"];
					$lastpostdate = $post_arr["added"];
					$lasttopicid = $post_arr["topicid"];

					$user_res = sql_query("SELECT username FROM users WHERE id=$lastposterid") or sqlerr(__FILE__, __LINE__);
					$user_arr = mysql_fetch_assoc($user_res);

					$lastposter = htmlspecialchars($user_arr['username']);

					$topic_res = sql_query("SELECT subject FROM topics WHERE id=$lasttopicid") or sqlerr(__FILE__, __LINE__);
					$topic_arr = mysql_fetch_assoc($topic_res);

					$lasttopic = htmlspecialchars($topic_arr['subject']);
					$lastpost = "<nobr>$lastpostdate<br>" .
					"by <a href=userdetails.php?id=$lastposterid><b>$lastposter</b></a><br>" .
					"in <a href=?action=viewtopic&topicid=$lasttopicid&page=p$lastpostid#$lastpostid><b>$lasttopic</b></a></nobr>";

					$r = sql_query("SELECT lastpostread FROM readposts WHERE userid=$CURUSER[id] AND topicid=$lasttopicid") or sqlerr(__FILE__, __LINE__);
					$a = mysql_fetch_row($r);

					if ($a && $a[0] >= $lastpostid)
						$img = "unlocked";
					else
						$img = "unlockednew";
				}
				else
				{
					$lastpost = "N/A";
					$img = "unlocked";
				}
				print("<tr><td align=left><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded style='padding-right: 5px'><img src=".
				"/pic/$img.gif></td><td class=embedded><a href=?action=viewforum&forumid=$forumid><b>$forumname</b></a>\n" .
				($CURUSER['class']>=UC_ADMINISTRATOR ? "<font class=small> ".
				"[<a class=altlink href=forums.php?action=editforum&forumid=$forumid>Edit</a>] ".
				"[<a class=altlink href=forums.php?action=deleteforum&forumid=$forumid>Delete</a>]</font>" : "").
				"<br>\n$forumdescription</td></tr></table></td><td align=right>$topiccount</td></td><td align=right>$postcount</td>" .
				"<td align=left>$lastpost</td></tr>\n");


			}

  }
// End Table Mod
print("</table>");
//forum_stats();



print("<p align=center class=success><a href=?action=search><b>Search Forums</b></a> | <a href=?action=viewunread><b>New Posts</b></a> | <a href=?action=getdaily><b>Todays Posts (Last 24 h.)</b></a> | <a href=?catchup><b>Mark all as read</b></a> ".($CURUSER['class'] >= UC_STAFFLEADER ? "| <a href=forummanage.php#add><b>Forum-Manager</b></a>":"")."</p>");
stdfoot();
?>