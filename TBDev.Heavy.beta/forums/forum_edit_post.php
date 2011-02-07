<?php

// -------- Action: Edit post
        $postid = (int)$_GET["postid"];
    if (!is_valid_id($postid))
        stderr('Error', 'Invalid ID!');

    $res = mysql_query("SELECT p.userid, p.topicid, p.posticon, p.body, t.locked,t.forumid  " . "FROM posts AS p " . "LEFT JOIN topics AS t ON t.id = p.topicid " . "WHERE p.id = " . sqlesc($postid)) or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) == 0)
        stderr("Error", "No post with that ID!");

    $arr = mysql_fetch_assoc($res);

    if (($CURUSER["id"] != $arr["userid"] || $arr["locked"] == 'yes') && $CURUSER['class'] < UC_MODERATOR && !isMod($arr["forumid"]))
        stderr("Error", "Access Denied!");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $body = trim($_POST['body']);
        $posticon = (isset($_POST["iconid"]) ? 0 + $_POST["iconid"] : 0);
        if (empty($body))
            stderr("Error", "Body cannot be empty!");

        if(!isset($_POST['lasteditedby']))
	      mysql_query("UPDATE posts SET body = " . sqlesc($body) . ", editedat = " . time() . ", editedby = {$CURUSER['id']}, posticon = $posticon WHERE id = $postid") or sqlerr(__FILE__, __LINE__);
        else
	      mysql_query("UPDATE posts SET body = " . sqlesc($body) . ", posticon = $posticon WHERE id = $postid") or sqlerr(__FILE__, __LINE__);

        header("Location: {$_SERVER['PHP_SELF']}?action=viewtopic&topicid={$arr['topicid']}&page=p$postid#p$postid");
        exit();
    }

    if ($TBDEV['forums_online'] == 0)
    $HTMLOUT .= stdmsg('Warning', 'Forums are currently in maintainance mode');
    $HTMLOUT .= begin_main_frame();
	  $HTMLOUT .="<h3>Edit Post</h3>";
	  $HTMLOUT .="<form name='compose' method='post' action='".$_SERVER['PHP_SELF']."?action=editpost&amp;postid=".$postid."'>
	  <table border='1' cellspacing='0' cellpadding='5' width='100%'>
	  <tr>
		<td class='rowhead' width='10%'>Body</td>
		<td align='left' style='padding: 0px'>";
    $ebody = htmlspecialchars(unesc($arr["body"]));
    if (function_exists('textbbcode'))
    $HTMLOUT .= textbbcode("compose", "body", $ebody);
    else {
    $HTMLOUT .="<textarea name='body' style='width:99%' rows='7'>{$ebody}</textarea>";
    }
    
		$HTMLOUT .="</td></tr>";
	  if ($CURUSER["class"] >= UC_MODERATOR)
    $HTMLOUT.="<tr><td colspan='1' align='center'><input type='checkbox' name='lasteditedby' /></td><td align='left' colspan='1'>Don't show the Last edited by <font class='small'>(Staff Only)</font></td></tr>";
	  $HTMLOUT.="<tr>
		<td align='center' colspan='2'>
		".(post_icons($arr["posticon"]))."
		</td>
	</tr>
	<tr>
		<td align='center' colspan='2'>
		<input type='submit' value='Update post' class='gobutton' />
	</td>
	</tr>
	</table>
	</form>";
	
    $HTMLOUT .= end_main_frame();
    print stdhead("Edit Post") . $HTMLOUT . stdfoot();
    exit();


?>