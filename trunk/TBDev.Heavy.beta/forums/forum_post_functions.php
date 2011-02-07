<?php

function post_icons($s = 0)
{
    $body = "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"8\" >
				<tr><td width=\"20%\" valign=\"top\" align=\"right\"><strong>Post Icons</strong> <br/>
				<font class=\"small\">(Optional)</font></td>\n";
    $body .= "<td width=\"80%\" align=\"left\">\n";

    for($i = 1; $i < 15;$i++) {
        $body .= "<input type=\"radio\" value=\"" . $i . "\" name=\"iconid\" " . ($s == $i ? "checked=\"checked\"" : "") . " />\n<img align=\"middle\" alt=\"\" src=\"pic/post_icons/icon" . $i . ".gif\"/>\n";
        if ($i == 7)
            $body .= "<br/>";
    }

    $body .= "<br/><input type=\"radio\" value=\"0\" name=\"iconid\"  " . ($s == 0 ? "checked=\"checked\"" : "") . " />[Use None]\n";
    $body .= "</td></tr></table>\n";

    return $body;
}


// -------- Inserts a quick jump menu
function insert_quick_jump_menu($currentforum = 0){

	global $CURUSER, $TBDEV;
	$htmlout='';
	$htmlout .="
	<form method='get' action='".$_SERVER['PHP_SELF']."' name='jump'>
	<input type='hidden' name='action' value='viewforum' />
	<div align='center'><b>Quick jump:</b>
	<select name='forumid' onchange=\"if(this.options[this.selectedIndex].value != -1){ forms['jump'].submit() }\">";
	$res = mysql_query("SELECT id, name, minclassread FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);
	while ($arr = mysql_fetch_assoc($res))
	if ($CURUSER['class'] >= $arr["minclassread"])
	$htmlout .="<option value='".$arr["id"].($currentforum == $arr["id"] ? " selected" : "")."'>".$arr["name"]."</option>";
  $htmlout .="</select>
	<input type='submit' value='Go!' class='gobutton' />
	</div>
	</form>";
  return $htmlout;
}


//-------- Inserts a compose frame
function insert_compose_frame($id, $newtopic = true, $quote = false, $attachment = false) {

    global $maxsubjectlength, $CURUSER, $TBDEV, $maxfilesize,  $use_attachment_mod, $forum_pics;
    
    $htmlout='';
    if ($newtopic) {
        $res = mysql_query("SELECT name FROM forums WHERE id = " . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
        $arr = mysql_fetch_assoc($res) or die("Bad forum ID!");

        $htmlout .="<h3>New topic in <a href='". $_SERVER['PHP_SELF']."?action=viewforum&amp;forumid=".$id."'>".htmlspecialchars($arr["name"])."</a> forum</h3>";
        } else {
        $res = mysql_query("SELECT t.forumid, t.subject, t.locked, f.minclassread FROM topics AS t LEFT JOIN forums AS f ON f.id = t.forumid WHERE t.id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
        $arr = mysql_fetch_assoc($res) or die("Forum error, Topic not found.");
  
        if ($arr['locked'] == 'yes') {
            stderr("Sorry", "The topic is locked.");

            $htmlout .= end_table();
            $htmlout .= end_main_frame();
            print stdhead("Compose") . $htmlout . stdfoot();
            exit();
        }
        
        if($CURUSER["class"] < $arr["minclassread"]){
		    $htmlout .= stdmsg("Sorry", "You are not allowed in here.");
				$htmlout .= end_table(); 
				$htmlout .= end_main_frame(); 
				print stdhead("Compose") . $htmlout . stdfoot();
		    exit();
		    }
        $htmlout .="<h3 align='center'>Reply to topic: <a href='".$_SERVER['PHP_SELF']."action=viewtopic&amp;topicid=".$id."'>". htmlspecialchars($arr["subject"])."</a></h3>";

    }
     
    $htmlout .="
    <script  type='text/javascript'>
    /*<![CDATA[*/
    function Preview()
    {
    document.compose.action = './forums.php?action=preview&forumid=$id'
    //document.compose.target = '_blank';
    document.compose.submit();
    return true;
    }
    /*]]>*/
    </script>";
      
    $htmlout .= begin_frame("Compose", true);
    $htmlout .="<form method='post' name='compose' action='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data'>
	  <input type='hidden' name='action' value='post' />
	  <input type='hidden' name='". ($newtopic ? 'forumid' : 'topicid')."' value='".$id."' />";

    $htmlout .= begin_table(true);

    if ($newtopic) {

       
		$htmlout .="<tr>
			<td class='rowhead' width='10%'>Subject</td>
			<td align='left'>
				<input type='text' size='100' maxlength='".$maxsubjectlength."' name='subject' style='height: 19px' />
			</td>
		</tr>";
    }

    if ($quote) {
        $postid = (int)$_GET["postid"];
        if (!is_valid_id($postid)) {
            stderr("Error", "Invalid ID!");

            $htmlout .= end_table();
            $htmlout .= end_main_frame();
            print stdhead("Compose") . $htmlout . stdfoot();
            exit();
        }

        $res = mysql_query("SELECT posts.*, users.username FROM posts JOIN users ON posts.userid = users.id WHERE posts.id = $postid") or sqlerr(__FILE__, __LINE__);

        if (mysql_num_rows($res) == 0) {
            stderr("Error", "No post with this ID");

            $htmlout .= end_table();
            $htmlout .= end_main_frame();
            print stdhead("Error - No post with this ID") . $htmlout . stdfoot();
            exit();
        }

        $arr = mysql_fetch_assoc($res);
    }

    $htmlout .="<tr>
		<td class='rowhead' width='10%'>Body</td>
		<td>";
		$qbody = ($quote ? "[quote=".htmlspecialchars($arr["username"])."]".htmlspecialchars(unesc($arr["body"]))."[/quote]" : "");
		if (function_exists('textbbcode'))
		$htmlout .= textbbcode("compose", "body", $qbody);
		else
		{
		$htmlout .="<textarea name='body' style='width:99%' rows='7'>{$qbody}</textarea>";
		}
		$htmlout .="</td></tr>";
		if ($use_attachment_mod && $attachment)
		{
		$htmlout .="<tr>
				<td colspan='2'><fieldset class='fieldset'><legend>Add Attachment</legend>
				<input type='checkbox' name='uploadattachment' value='yes' />
				<input type='file' name='file' size='60' />
        <div class='error'>Allowed Files: rar, zip<br />Size Limit ".mksize($maxfilesize)."</div></fieldset>
				</td>
			</tr>";
		  }
		  
		  $htmlout .="<tr>
   	  <td align='center' colspan='2'>".(post_icons())."</td>
 	    </tr><tr>
 		  <td colspan='2' align='center'>
 	    <input type='submit' value='Submit' /><input type='button' value='Preview' name='button2' onclick='return Preview();' />\n";
      if ($newtopic){
      $htmlout .= "Anonymous Topic<input type='checkbox' name='anonymous' value='yes'/>\n";
      }
      else
      {
      $htmlout .= "Anonymous Post<input type='checkbox' name='anonymous' value='yes'/>\n";
      }
      $htmlout .= "</td></tr>\n";


    $htmlout .= end_table();

    $htmlout .="</form>";
    
    $htmlout .= end_frame();
    // ------ Get 10 last posts if this is a reply
    
    if (!$newtopic) {
        $postres = mysql_query("SELECT p.id, p.added, p.body, p.anonymous, u.id AS uid, u.username, u.avatar, u.offavatar " . "FROM posts AS p " . "LEFT JOIN users AS u ON u.id = p.userid " . "WHERE p.topicid = " . sqlesc($id) . " " . "ORDER BY p.id DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
        if (mysql_num_rows($postres) > 0) {

            $htmlout .="<br />";
            $htmlout .= begin_frame("10 last posts, in reverse order");

            while ($post = mysql_fetch_assoc($postres)) {
                $avatar = ($CURUSER["avatars"] == "all" ? htmlspecialchars($post["avatar"]) : ($CURUSER["avatars"] == "some" && $post["offavatar"] == "no" ? htmlspecialchars($post["avatar"]) : ""));
             
             if ($post['anonymous'] == 'yes') {
             $avatar = $TBDEV['pic_base_url'] . $forum_pics['default_avatar'];
             }
             else {
             $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($post["avatar"]) : '');
             }

             if (empty($avatar))
             $avatar = $TBDEV['pic_base_url'] . $forum_pics['default_avatar'];

             if ($post["anonymous"] == "yes")
             if($CURUSER['class'] < UC_MODERATOR && $post["uid"] != $CURUSER["id"]){	
             $htmlout .= "<p class='sub'>#" . $post["id"] . " by <i>Anonymous</i> at ".get_date($post["added"], 'LONG',1,0)."</p>";
             }
             else{	
             $htmlout .= "<p class='sub'>#" . $post["id"] . " by <i>Anonymous</i> (<b>" . $post["username"] . "</b>) at ".get_date($post["added"], 'LONG',1,0)."</p>"; 
             }
             else
             $htmlout .="<p class='sub'>#".$post["id"]." by ". (!empty($post["username"]) ? $post["username"] : "unknown[{$post['uid']}]")." at ".get_date($post["added"], 'LONG',1,0)."</p>";

                $htmlout .= begin_table(true);

                
					$htmlout .="<tr>
						<td height='100' width='100' align='center' style='padding: 0px' valign='top'><img height='100' width='100' src='".$avatar."' alt='User avvy' /></td>
						<td class='comment' valign='top'>". format_comment($post["body"])."</td>
					</tr>";
           $htmlout .= end_table();
            }

            $htmlout .= end_frame();
        }
    }
    $htmlout .= insert_quick_jump_menu();
    return $htmlout;
}



//-------- Insert A Fast Reply Frame
  
function insert_fastreply($ids, $pkey = '') {
	
    global $TBDEV;
    
    $htmlout = "<div style='display: none;' id='fastreply'>
    <div class='tb_table_inner_wrap'>
    <span style='color:#ffffff;'>Fast Reply</span>
    </div>

    <form name='bbcode2text' method='post' action='{$TBDEV['baseurl']}/forums.php?action=post'>\n";
    
    if ( !empty($pkey) )
    {
        $htmlout .= "<input type='hidden' name='postkey' value='$pkey' />\n";
    }
    
    $htmlout .= "<input type='hidden' name='topicid' value='{$ids['topicid']}' />
    
    <input type='hidden' name='forumid' value='{$ids['forumid']}' />
    
    <textarea name='body' cols='50' rows='10'></textarea>

    <br /><input type='submit' class='btn' value='Submit' />
    
    <input onclick=\"showhide('fastreply'); return(false);\" value='Close Fast Reply' type='button' class='btn' />

    </form>
    </div><br />\n";
    
    return $htmlout;
}

?>