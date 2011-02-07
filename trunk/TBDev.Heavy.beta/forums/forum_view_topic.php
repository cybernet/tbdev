<?php

// -------- Action: View topic
        $userid = (int)$CURUSER["id"];

    if ($use_poll_mod && $_SERVER['REQUEST_METHOD'] == "POST") {
        $choice = $_POST['choice'];
        $pollid = (int)$_POST["pollid"];
        if (ctype_digit($choice) && $choice < 256 && $choice == floor($choice)) {
            $res = mysql_query("SELECT pa.id " . "FROM postpolls AS p " . "LEFT JOIN postpollanswers AS pa ON pa.pollid = p.id AND pa.userid = " . sqlesc($userid) . " " . "WHERE p.id = " . sqlesc($pollid)) or sqlerr(__FILE__, __LINE__);
            $arr = mysql_fetch_assoc($res) or stderr('Sorry', 'Inexistent poll!');

            if (is_valid_id($arr['id']))
                stderr("Error...", "Dupe vote");

            mysql_query("INSERT INTO postpollanswers VALUES(id, " . sqlesc($pollid) . ", " . sqlesc($userid) . ", " . sqlesc($choice) . ")") or sqlerr(__FILE__, __LINE__);

            if (mysql_affected_rows() != 1)
                stderr("Error...", "An error occured. Your vote has not been counted.");
        } else
            stderr("Error..." , "Please select an option.");
    }

    $topicid = (int)$_GET["topicid"];
    if (!is_valid_id($topicid))
        stderr('Error', 'Invalid topic ID!');

    $page = (isset($_GET["page"]) ? $_GET["page"] : 0);
    // ------ Get topic info
    $res = mysql_query("SELECT " . ($use_poll_mod ? 't.pollid, ' : '') . "t.locked, t.subject, t.sticky, t.userid AS t_userid, t.forumid, f.name AS forum_name, f.minclassread, f.minclasswrite, f.minclasscreate, (SELECT COUNT(id)FROM posts WHERE topicid = t.id) AS p_count " . "FROM topics AS t " . "LEFT JOIN forums AS f ON f.id = t.forumid " . "WHERE t.id = " . sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res) or stderr("Error", "Topic not found");
    mysql_free_result($res);

    ($use_poll_mod ? $pollid = (int)$arr["pollid"] : null);
    $t_userid = (int)$arr['t_userid'];
    $locked = ($arr['locked'] == 'yes' ? true : false);
    $subject = $arr['subject'];
    $sticky = ($arr['sticky'] == "yes" ? true : false);
    $forumid = (int)$arr['forumid'];
    $forum = $arr["forum_name"];
    $postcount = (int)$arr['p_count'];
    if ($CURUSER["class"] < $arr["minclassread"])
        stderr("Error", "You are not permitted to view this topic.");
    // ------ Update hits column
    mysql_query("UPDATE topics SET views = views + 1 WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
    //------ Make page menu
	$pagemenu1 = "<p align='center'>";
	$perpage = $postsperpage;
	$pages = ceil($postcount / $perpage);
	
	if ($page[0] == "p")
	{
		$findpost = substr($page, 1);
		$res = mysql_query("SELECT id FROM posts WHERE topicid=$topicid ORDER BY added") or sqlerr(__FILE__, __LINE__);
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
		if ($page < 1)
			$page = 1;
		else if ($page > $pages)
			$page = $pages;
	}
	
	$offset = ((int)$page * $perpage) - $perpage;
	$offset = ($offset < 0 ? 0 : $offset);
	
	$pagemenu2 = '';
	for ($i = 1; $i <= $pages; ++$i)
		$pagemenu2 .= ($i == $page ? "<b>[<u>$i</u>]</b>" : "<a href='".$_SERVER['PHP_SELF']."?action=viewtopic&amp;topicid=$topicid&amp;page=$i'><b>$i</b></a>");
	
	$pagemenu1 .= ($page == 1 ? "<b>&lt;&lt;&nbsp;Prev</b>" : "<a href='".$_SERVER['PHP_SELF']."?action=viewtopic&amp;topicid=$topicid&amp;page=".($page - 1)."'><b>&lt;&lt;&nbsp;Prev</b></a>");
	$pmlb = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$pagemenu3 = ($page == $pages ? "<b>Next&nbsp;&gt;&gt;</b></p>" : "<a href='".$_SERVER['PHP_SELF']."?action=viewtopic&amp;topicid=$topicid&amp;page=".($page + 1)."'><b>Next&nbsp;&gt;&gt;</b></a></p>");
	
	$HTMLOUT .= begin_main_frame();

	if ($use_poll_mod && is_valid_id($pollid))
	{
		$res = mysql_query("SELECT p.*, pa.id AS pa_id, pa.selection FROM postpolls AS p LEFT JOIN postpollanswers AS pa ON pa.pollid = p.id AND pa.userid = ".$CURUSER['id']." WHERE p.id = ".sqlesc($pollid)) or sqlerr(__FILE__, __LINE__);
	
		if (mysql_num_rows($res) > 0)
		{
			$arr1 = mysql_fetch_assoc($res);
			
			$userid = (int)$CURUSER['id'];
			$question = htmlspecialchars($arr1["question"]);
			$o = array($arr1["option0"], $arr1["option1"], $arr1["option2"], $arr1["option3"], $arr1["option4"],
		  $arr1["option5"], $arr1["option6"], $arr1["option7"], $arr1["option8"], $arr1["option9"],
		  $arr1["option10"], $arr1["option11"], $arr1["option12"], $arr1["option13"], $arr1["option14"],
		  $arr1["option15"], $arr1["option16"], $arr1["option17"], $arr1["option18"], $arr1["option19"]);
			
			$HTMLOUT .="<table cellpadding='5' width='{$forum_width}' align='center'>
			<tr><td class='colhead' align='left'><h2>Poll";
			if ($userid == $t_userid || $CURUSER['class'] >= UC_MODERATOR)
			{
			$HTMLOUT .="<font class='small'> - [<a href='".$_SERVER['PHP_SELF']."?action=makepoll&amp;subaction=edit&amp;pollid=".$pollid."'><b>Edit</b></a>]</font>";
			if ($CURUSER['class'] >= UC_MODERATOR)
			{
			$HTMLOUT .="<font class='small'> - [<a href='".$_SERVER['PHP_SELF']."?action=deletepoll&amp;pollid=".$pollid."'><b>Delete</b></a>]</font>";
			}
			}
			$HTMLOUT .="</h2></td></tr>";

			$HTMLOUT .="<tr><td align='center' class='clearalt7'>";
			$HTMLOUT .="
			<table width='55%'>
			<tr><td class='clearalt6'>
			<div align='center'><b>
			{$question}</b></div>";
			
			
			$voted = (is_valid_id($arr1['pa_id']) ? true : false);
			
			if (($locked && $CURUSER['class'] < UC_MODERATOR) ? true : $voted)
			{
				$uservote = ($arr1["selection"] != '' ? (int)$arr1["selection"] : -1);
				
				$res3 = mysql_query("SELECT selection FROM postpollanswers WHERE pollid = ".sqlesc($pollid)." AND selection < 20");
				$tvotes = mysql_num_rows($res3);
			   				
			$vs = $os = array();
      for($i=0;$i<20;$i++) $vs[$i]=0;

				
				while ($arr3 = mysql_fetch_row($res3))
					$vs[$arr3[0]] += 1;
				
				reset($o);
				for ($i = 0; $i < count($o); ++$i)
					if ($o[$i])
						$os[$i] = array($vs[$i], $o[$i]);
				
				function srt($a,$b)
				{
					if ($a[0] > $b[0])
						return -1;
						
					if ($a[0] < $b[0])
						return 1;
				
					return 0;
				}

				
				if ($arr1["sort"] == "yes")
					usort($os, "srt");
				
				$HTMLOUT .="<br />
			  <table width='100%' style='border:none;' cellpadding='5'>";
			
         foreach($os as $a) 
				{
					if ($i == $uservote)
						$a[1] .= " *";
					
					$p = ($tvotes == 0 ? 0 : round($a[0] / $tvotes * 100));				
					$c = ($i % 2 ? '' : "poll");
					
					$p = ($tvotes == 0 ? 0 : round($a[0] / $tvotes * 100));				
					$c = ($i % 2 ? '' : "poll");
					$HTMLOUT .="<tr>";
	        $HTMLOUT .="<td width='1%' style='padding:3px;white-space:nowrap;' class='embedded".$c."'>".htmlspecialchars($a[1])."</td>";
					$HTMLOUT .="<td width='99%' class='embedded".$c."' align='center'>";
					$HTMLOUT .="<img src='{$TBDEV['pic_base_url']}bar_left.gif' alt='bar_left.gif' />
					<img src='{$TBDEV['pic_base_url']}bar.gif' alt='bar.gif'  height='9' width='". ($p*3)."' />
					<img src='{$TBDEV['pic_base_url']}bar_right.gif'  alt='bar_right.gif' />&nbsp;".$p."%</td>
					</tr>";
				  }
				  $HTMLOUT .="</table>";
				  $HTMLOUT .="<p align='center'>Votes: <b>".number_format($tvotes)."</b></p>";
			    }
		    	else
			    {
				  $HTMLOUT .="<form method='post' action='".$_SERVER['PHP_SELF']."?action=viewtopic&amp;topicid=".$topicid."'>
				  <input type='hidden' name='pollid' value='".$pollid."' />";
				  for ($i=0; $a = $o[$i]; ++$i)
				  $HTMLOUT .="<input type='radio' name='choice' value='$i' />".htmlspecialchars($a)."<br />";
				  $HTMLOUT .="<br />";
				  $HTMLOUT .="<p align='center'><input type='submit' value='Vote!' /></p></form>";
			    }
			    $HTMLOUT .="</td></tr></table>";
			
			    $listvotes = (isset($_GET['listvotes']) ? true : false);
			    if ($CURUSER['class'] >= UC_ADMINISTRATOR)
			    {
			    if (!$listvotes)
			    $HTMLOUT .="<a href='".$_SERVER['PHP_SELF']."?action=viewtopic&amp;topicid=$topicid&amp;listvotes'>List Voters</a>";
				  else
				  {
				  $res4 = mysql_query("SELECT pa.userid, u.username, u.anonymous FROM postpollanswers AS pa LEFT JOIN users AS u ON u.id = pa.userid WHERE pa.pollid = ".sqlesc($pollid)) or sqlerr(__FILE__, __LINE__);
				  $voters = '';
				  while ($arr4 = mysql_fetch_assoc($res4))
				  {
				  if (!empty($voters) && !empty($arr4['username']))
          $voters .= ', ';
 	        if ($arr4["anonymous"] == "yes") {
				  if($CURUSER['class'] < UC_MODERATOR && $arr4["userid"] != $CURUSER["id"])
				  $voters = "<i>Anonymous</i>";
         	else
 	        $voters = "<i>Anonymous</i>(<a href='{$TBDEV['baseurl']}/userdetails.php?id=".(int)$arr4['userid']."'><b>".$arr4['username']."</b></a>)";
 	        }
 	        else
				  $voters .= "<a href='{$TBDEV['baseurl']}/userdetails.php?id=".(int)$arr4['userid']."'><b>".htmlspecialchars($arr4['username'])."</b></a>";
				  }
				  $HTMLOUT .= $voters."<br />(<font class='small'><a href='".$_SERVER['PHP_SELF']."?action=viewtopic&amp;topicid=$topicid'>hide</a></font>)";
				  }
			    }
		      $HTMLOUT .="</td></tr></table>";
		    }
		    else
		    {
			  $HTMLOUT .="<br />";
			  stderr('Sorry', "Poll doesn't exist");
		    }
		    $HTMLOUT .="<br />";
		    }
	      $HTMLOUT .="<a name='top'></a>
        <h1 align='left'><a href='".$_SERVER['PHP_SELF']."?action=viewforum&amp;forumid=".$forumid."'>{$forum}</a> &gt; ".htmlspecialchars($subject)."</h1>";
       $HTMLOUT .="<br /><a href='{$TBDEV['baseurl']}/subscriptions.php?topicid=$topicid&amp;subscribe=1'><b><font color='red'>Subscribe to Forum</font></b></a>";
       $HTMLOUT .="<br /><br />";

    
$HTMLOUT .="
<script  type='text/javascript'>
/*<![CDATA[*/
function confirm_att(id)
{
   if(confirm('Are you sure you want to delete this ?'))
   {
		window.open('".$_SERVER['PHP_SELF']."?action=attachment&amp;subaction=delete&amp;attachmentid='+id,'attachment','toolbar=no, scrollbars=yes, resizable=yes, width=600, height=250, top=50, left=50');
		window.location.reload(true)
   }
}
    function popitup(url) {
    newwindow=window.open(url,'./usermood.php','height=335,width=735,resizable=no,scrollbars=no,toolbar=no,menubar=no');
    if (window.focus) {newwindow.focus()}
    return false;
    }
/*]]>*/
</script>";

    // ------ echo table
    $HTMLOUT .= begin_frame();
    $res = mysql_query("SELECT p.id, p.added, p.userid, p.added, p.body, p.editedby, p.editedat, p.posticon, p.anonymous as p_anon, u.id as uid, u.username as uusername, u.class, u.avatar, u.donor, u.title, u.username, u.reputation, u.mood, u.anonymous, u.country, u.enabled, u.warned, u.uploaded, u.downloaded, u.signature, u.last_access, (SELECT COUNT(id)  FROM posts WHERE userid = u.id) AS posts_count, u2.username as u2_username " . ($use_attachment_mod ? ", at.id as at_id, at.filename as at_filename, at.postid as at_postid, at.size as at_size, at.downloads as at_downloads, at.owner as at_owner " : "") . ", (SELECT lastpostread FROM readposts WHERE userid = " . sqlesc((int)$CURUSER['id']) . " AND topicid = p.topicid LIMIT 1) AS lastpostread " . "FROM posts AS p " . "LEFT JOIN users AS u ON p.userid = u.id " .
        ($use_attachment_mod ? "LEFT JOIN attachments AS at ON at.postid = p.id " : "") . "LEFT JOIN users AS u2 ON u2.id = p.editedby " . "WHERE p.topicid = " . sqlesc($topicid) . " ORDER BY id LIMIT $offset, $perpage") or sqlerr(__FILE__, __LINE__);
    $pc = mysql_num_rows($res);
    $pn = 0;

    while ($arr = mysql_fetch_assoc($res)) {
        ++$pn;

        $lpr = $arr['lastpostread'];
        $postid = (int)$arr["id"];
        $postadd = $arr['added'];
        $posterid = (int)$arr['userid'];
        $posticon = ($arr["posticon"] > 0 ? "<img src=\"pic/post_icons/icon" . $arr["posticon"] . ".gif\" style=\"padding-left:3px;\" alt=\"post icon\" title=\"post icon\" />" : "&nbsp;");
        $added = get_date($arr['added'], 'DATE',1,0) . " GMT <font class='small'>(" . (get_date($arr['added'], 'LONG',1,0)) . ")</font>";
        // ---- Get poster details
        $uploaded = mksize($arr['uploaded']);
        $downloaded = mksize($arr['downloaded']);
        $member_reputation = $arr['uusername'] != '' ? get_reputation($arr, 'posts') : '';
        $last_access = get_date($arr['last_access'],'DATE',1,0);
        if ($arr['downloaded'] > 0) {
 	      $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
 	      $ratio = "<font color='" . get_ratio_color($ratio) . "'>$ratio</font>";
       	} 
       	else if ($arr['uploaded'] > 0)
 	      $ratio = "&infin;";
 	      else
 	      $ratio = "---";
        if (($postid > $lpr) && ($postadd > (time() - $TBDEV['readpost_expiry']))){
            $newp = "&nbsp;&nbsp;<span class='red'>(New)</span>";
        }
        foreach($mood as $key => $value)
        $change[$value['id']] = array('id' => $value['id'], 'name' => $value['name'], 'image' => $value['image']);
        $mooduname = $change[$arr['mood']]['name'];
        $moodupic = $change[$arr['mood']]['image'];
        $title = $arr["title"];
        //$signature = ($CURUSER['signatures'] == 'yes' ? format_comment($arr['signature']) : '');
        $signature = ($CURUSER['signature'] !== NULL ? format_comment($arr['signature']) : '');
        $postername = $arr['uusername'];
        $avatar = ($CURUSER["avatars"] == "all" ? htmlspecialchars($arr["avatar"]) : ($CURUSER["avatars"] == "some" && $arr["offavatar"] == "no" ? htmlspecialchars($arr["avatar"]) : ""));
        $title = (!empty($postername) ? (empty($arr['title']) ? "(" . get_user_class_name($arr['class']) . ")" : "(" . ($arr['title']) . ")") : '');
        $forumposts = (!empty($postername) ? ($arr['posts_count'] != 0 ? $arr['posts_count'] : 'N/A') : 'N/A');
 			  if ($arr["p_anon"] == "yes") {
        if($CURUSER['class'] < UC_MODERATOR && $arr['userid'] != $CURUSER["id"])
        $by = "<i>Anonymous</i>";
        else
        $by = "<i>Anonymous</i>(<a href='{$TBDEV['baseurl']}/userdetails.php?id=$posterid'>".$postername."</a>)".($arr['donor'] == "yes" ? "<img src='".$TBDEV['pic_base_url']."star.gif' alt='Donor' />" : '').($arr['enabled'] == 'no' ? "<img src='".$TBDEV['pic_base_url']."disabled.gif' alt='This account is disabled' style='margin-left: 2px' />" : ($arr['warned'] == 'yes'? "<img src='".$TBDEV['pic_base_url']."warned.gif' alt='Warned' border='0' />" : ''))."$title";	
        }
        else 
        {	
        $by = (!empty($postername) ? "<a href='{$TBDEV['baseurl']}/userdetails.php?id=$posterid'>".$postername."</a>".($arr['donor'] == "yes" ? "<img src='".$TBDEV['pic_base_url']."star.gif' alt='Donor' />" : '').($arr['enabled'] == 'no' ? "<img src='".$TBDEV['pic_base_url']."disabled.gif' alt='This account is disabled' style='margin-left: 2px' />" : ($arr['warned'] == 'yes'? "<img src='".$TBDEV['pic_base_url']."warned.gif' alt='Warned' border='0' />" : '')) : "unknown[".$posterid."]")."$title";	
        }
        if (empty($avatar))
            $avatar = $TBDEV['pic_base_url'].$forum_pics['default_avatar'];
        $HTMLOUT .="". ($pn == $pc ? '<a name=\'last\'></a>' : '');

        $HTMLOUT .= begin_table();

        $HTMLOUT .="<tr><td width='737' colspan='2'><table class='main'><tr><td style='border:none;' width='100%'>{$posticon}<a  id='p".$postid."' name='p{$postid}' href='".$_SERVER['PHP_SELF']."?action=viewtopic&amp;topicid=".$topicid."&amp;page=p".$postid."#p".$postid."'>#".$postid."</a> by ".$by." at ".$added."";
       
        if (isset($newp)) {
            $HTMLOUT .="$newp";
        }
       
        $HTMLOUT .="</td><td style='border:none;'><a href='#top'><img align='right' src='{$TBDEV['pic_base_url']}".$forum_pics['arrow_up']."' alt='Top' /></a></td></tr></table></td></tr>";

        $highlight = (isset($_GET['highlight']) ? $_GET['highlight'] : '');
        $body = (!empty($highlight) ? highlight(htmlspecialchars(trim($highlight)), format_comment($arr['body'])) : format_comment($arr['body']));

       if (is_valid_id($arr['editedby']))
			 $body .= "<p><font size='1' class='small'>Last edited by <a href='{$TBDEV['baseurl']}/userdetails.php?id=".$arr['editedby']."'><b>".$arr['u2_username']."</b></a> at ".get_date($arr['editedat'],'LONG',1,0)." GMT</font></p>";
		
		   if ($use_attachment_mod && ((!empty($arr['at_filename']) && is_valid_id($arr['at_id'])) && $arr['at_postid'] == $postid))
		   {
			 foreach ($allowed_file_extensions as $allowed_file_extension)
				if (substr($arr['at_filename'], -3) == $allowed_file_extension)
					$aimg = $allowed_file_extension;
			
			$body .= "<div style='padding:6px'>
			    <fieldset class='fieldset'>
					<legend>Attached Files</legend>
					<table cellpadding='0' cellspacing='3' border='0'>
					<tr>
					<td><img class='inlineimg' src='{$TBDEV['pic_base_url']}$aimg.gif' alt='' width='16' height='16' border='0' style='vertical-align:baseline' />&nbsp;</td>
					<td><a href='".$_SERVER['PHP_SELF']."?action=attachment&amp;attachmentid=".$arr['at_id']."' target='_blank'>".htmlspecialchars($arr['at_filename'])."</a> (".mksize($arr['at_size']).", ".$arr['at_downloads']." downloads)</td>
					<td>&nbsp;&nbsp;<input type='button' class='none' value='See who downloaded' tabindex='1' onclick=\"window.open('".$_SERVER['PHP_SELF']."?action=whodownloaded&amp;fileid=".$arr['at_id']."','whodownloaded','toolbar=no, scrollbars=yes, resizable=yes, width=600, height=250, top=50, left=50'); return false;\" />".($CURUSER['class'] >= UC_MODERATOR ? "&nbsp;&nbsp;<input type='button' class='gobutton' value='Delete' tabindex='2' onclick=\"window.open('".$_SERVER['PHP_SELF']."?action=attachment&amp;subaction=delete&amp;attachmentid=".$arr['at_id']."','attachment','toolbar=no, scrollbars=yes, resizable=yes, width=600, height=250, top=50, left=50'); return false;\" />" : "")."</td>
					</tr>
					</table>
					</fieldset>
					</div>";
		}
					
		  if (!empty($signature) && $arr["p_anon"] == "no")
			$body .= "<p style='vertical-align:bottom'><br />____________________<br />".$signature."</p>";

      $HTMLOUT .="<tr align='center'><td width='150' align='center' style='padding: 0px'>";
      if ($arr["p_anon"] == "yes") {
      if($CURUSER['class'] < UC_MODERATOR && $posterid != $CURUSER["id"])
      $HTMLOUT .="<img width='150' src='pic/default_avatar.gif' alt='Avatar' /><br />";
      else
      $HTMLOUT .="<img width='150' src='{$avatar}' alt='Avatar' /><br />
 	    <fieldset style='text-align:left;border:none:white-space:nowrap;'>
		  <b>Posts:</b>&nbsp;{$forumposts}<br />
		  <b>Ratio:</b>&nbsp;{$ratio}<br />
		  <b>Uploaded:</b>&nbsp;{$uploaded}<br />
		  <b>Downloaded:</b>&nbsp;{$downloaded}<br />
		  </fieldset>";
      }
      else 
      {
      $HTMLOUT .="<img width='150' src='{$avatar}' alt='Avatar' /><br />
 	    <fieldset style='text-align:left;border:none:white-space:nowrap;'>
		  <b>Posts:</b>&nbsp;{$forumposts}<br />
		  <b>Ratio:</b>&nbsp;{$ratio}<br />
		  <b>Uploaded:</b>&nbsp;{$uploaded}<br />
		  <b>Downloaded:</b>&nbsp;{$downloaded}<br />
		  <div>$member_reputation</div>
		  </fieldset>";
      }
		
		  $HTMLOUT .="</td><td class='text' width='100%'>{$body}</td></tr><tr><td>";
		  if ($arr["p_anon"] == "yes") {
 	    if($CURUSER['class'] < UC_MODERATOR)
		  $HTMLOUT .="";
		  else
 	    $HTMLOUT .="<img src='".$TBDEV['pic_base_url'].$forum_pics[($last_access > (time()-360) || $posterid == $CURUSER['id'] ? 'on' : 'off').'line_btn']."' border='0' alt='' />&nbsp;<a href='{$TBDEV['baseurl']}/sendmessage.php?receiver=".$posterid."'><img src='".$TBDEV['pic_base_url'].$forum_pics['pm_btn']."' border='0' alt='Pm ".htmlspecialchars($postername)."' /></a>";
		  }
      else 
      {
      $HTMLOUT .="<img src='".$TBDEV['pic_base_url'].$forum_pics[($last_access > (time()-360) || $posterid == $CURUSER['id'] ? 'on' : 'off').'line_btn']."' border='0' alt='' />&nbsp;<a href='{$TBDEV['baseurl']}/sendmessage.php?receiver=".$posterid."'><img src='".$TBDEV['pic_base_url'].$forum_pics['pm_btn']."' border='0' alt='Pm ".htmlspecialchars($postername)."' /></a>";
      }
      
      $HTMLOUT.="<a href='{$TBDEV['baseurl']}/report.php?type=Post&amp;id=".$postid."&amp;id_2=".$topicid."&amp;id_3=".$posterid."'><img src='".$TBDEV['pic_base_url'].$forum_pics['p_report_btn']."' border='0' alt='Report Post' /></a>";
		  
		  $mooduser = (isset($arr['username']) ? ("<b>".htmlspecialchars($arr['username'])."</b>") : "(unknown)");
      $moodanon = ($arr['anonymous'] == 'yes' ? ($CURUSER['class'] < UC_MODERATOR && $arr['userid'] != $CURUSER['id'] ? '' : $mooduser.' - ')."<i>Anonymous</i>" : $mooduser);	
      $HTMLOUT .="&nbsp;&nbsp;&nbsp;<a href='{$TBDEV['baseurl']}/usermood.php' onclick=\"return popitup('usermood.php')\">
      <span class='tool'>
      <img border='0' src='{$TBDEV['pic_base_url']}smilies/".htmlspecialchars($moodupic)."' alt='".htmlspecialchars($mooduname)."' />
      <span class='tip'>".$moodanon."&nbsp;".htmlspecialchars($mooduname)."&nbsp;!</span></span></a></td>";
		  
		  
		  $HTMLOUT .="<td align='right'>";
        
        if (!$locked || $CURUSER['class'] >= UC_MODERATOR || isMod($forumid)) {
		    if ($arr["p_anon"] == "yes") {
			  if($CURUSER['class'] < UC_MODERATOR)
		    $HTMLOUT .="";
		    }
		    else
 	      $HTMLOUT .="<a href='".$_SERVER['PHP_SELF']."?action=quotepost&amp;topicid=".$topicid."&amp;postid=".$postid."'><img src='".$TBDEV['pic_base_url'].$forum_pics['p_quote_btn']."' border='0' alt='Quote Post' /></a>"; 
 			  }
        else 
        {
	      $HTMLOUT .="<a href='".$_SERVER['PHP_SELF']."?action=quotepost&amp;topicid=".$topicid."&amp;postid=".$postid."'><img src='".$TBDEV['pic_base_url'].$forum_pics['p_quote_btn']."' border='0' alt='Quote Post' /></a>"; 
		    }

        if ($CURUSER['class'] >= UC_MODERATOR || isMod($forumid)) {
        $HTMLOUT .="<a href='".$_SERVER['PHP_SELF']."?action=deletepost&amp;postid=".$postid."'><img src='".$TBDEV['pic_base_url'].$forum_pics['p_delete_btn']."' border='0' alt='Delete Post' /></a>";
        }

        if (($CURUSER["id"] == $posterid && !$locked) || $CURUSER['class'] >= UC_MODERATOR || isMod($forumid)) {
        $HTMLOUT .="<a href='".$_SERVER['PHP_SELF']."?action=editpost&amp;postid=".$postid."'><img src='".$TBDEV['pic_base_url'].$forum_pics['p_edit_btn']."' border='0' alt='Edit Post' /></a>";
        }

        $HTMLOUT .="</td></tr>";

        $HTMLOUT .= end_table();

        $HTMLOUT .="<br />";
    }

    if ($use_poll_mod && (($userid == $t_userid || $CURUSER['class'] >= UC_MODERATOR || isMod($forumid)) && !is_valid_id($pollid))) {

		$HTMLOUT .="<table cellpadding='5' width='{$forum_width}'>
        <tr>
        	<td align='right'>
            	<form method='post' action='".$_SERVER['PHP_SELF']."'>
                <input type='hidden' name='action' value='makepoll' />
				<input type='hidden' name='topicid' value='".$topicid."' />
				<input type='submit' value='Add a Poll' />
				</form>
			</td>
        </tr>
        </table>
        <br />";
   
    }

    if (($postid > $lpr) && ($postadd > (time() - $TBDEV['readpost_expiry']))) {
        if ($lpr)
            mysql_query("UPDATE readposts SET lastpostread = $postid WHERE userid = $userid AND topicid = $topicid") or sqlerr(__FILE__, __LINE__);
        else
            mysql_query("INSERT INTO readposts (userid, topicid, lastpostread) VALUES($userid, $topicid, $postid)") or sqlerr(__FILE__, __LINE__);
    }
    // ------ Mod options
    if ($CURUSER['class'] >= UC_MODERATOR || isMod($forumid)) {
	  $HTMLOUT .="<form method='post' action='".$_SERVER['PHP_SELF']."'>
	  <input type='hidden' name='action' value='updatetopic' />
		<input type='hidden' name='topicid' value='{$topicid}' />";
	  
	  $HTMLOUT .= begin_table();
		$HTMLOUT .="
		<tr>
		<td colspan='2' class='colhead'>Staff options</td>
		</tr>
		<tr>
		<td class='rowhead' width='1%'>Sticky</td>
		<td>
		<select name='sticky'>
		<option value='yes'". ($sticky ? " selected='selected'" : '').">Yes</option>
		<option value='no' ". (!$sticky ? " selected='selected'" : '').">No</option>
		</select>
		</td>
		</tr>
		<tr>
		<td class='rowhead'>Locked</td>
		<td>
		<select name='locked'>
		<option value='yes'". ($locked ? " selected='selected'" : '').">Yes</option>
		<option value='no'". (!$locked ? " selected='selected'" : '').">No</option>
		</select>
	  </td>
		</tr>
		<tr>
		<td class='rowhead'>Topic name</td>
		<td>
		<input type='text' name='subject' size='60' maxlength='{$maxsubjectlength}' value='".htmlspecialchars($subject)."' />
		</td>
		</tr>
		<tr>
		<td class='rowhead'>Move topic</td>
		<td>
		<select name='new_forumid'>";
		$res = mysql_query("SELECT id, name, minclasswrite FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);
		while ($arr = mysql_fetch_assoc($res))
	  if ($CURUSER['class'] >= $arr["minclasswrite"])
		$HTMLOUT .= '<option value="' . (int)$arr["id"] . '"' . ($arr["id"] == $forumid ? ' selected="selected"' : '') . '>' . htmlspecialchars($arr["name"]) . '</option>';
		
		$HTMLOUT .="</select>
		</td></tr>
		<tr>
	  <td class='rowhead' style='white-space:nowrap;'>Delete topic</td>
	  <td>
	  <select name='delete'>
		<option value='no' selected='selected'>No</option>
		<option value='yes'>Yes</option>
		</select>
		<br />
		<b>Note:</b> Any changes made to the topic won't take effect if you select 'yes'
		</td>
		</tr>
		<tr>
		<td colspan='2' align='center'>
		<input type='submit' value='Update Topic' />
		</td>
		</tr>";
		$HTMLOUT .= end_table();
	  $HTMLOUT .="</form>";
	  
	  }
	  $HTMLOUT .= end_frame();
	
	 $HTMLOUT .= $pagemenu1.$pmlb.$pagemenu2.$pmlb.$pagemenu3;
   $maypost = ($CURUSER['class'] >= $arr["write"] && $CURUSER['class'] >= $arr["create"]);
    if ($locked && $CURUSER['class'] < UC_MODERATOR && !isMod($forumid)) {

      $HTMLOUT .="<p align='center'>This topic is locked; no new posts are allowed.</p>";
       } else {
        $arr = get_forum_access_levels($forumid);

        if ($CURUSER['class'] < $arr["write"]) {

          $HTMLOUT .="<p align='center'><i>You are not permitted to post in this forum.</i></p>";
          
            $maypost = false;
        } else
            $maypost = true;
    }
    // ------ "View unread" / "Add reply" buttons
    
	$HTMLOUT .="<table align='center' class='main' border='0' cellspacing='0' cellpadding='0'><tr>
	<td class='embedded'>
		<form method='get' action='".$_SERVER['PHP_SELF']."'>
		<input type='hidden' name='action' value='viewunread' />
		<input type='submit' value='Show new' />
		</form>
	</td>";
	
	if ($maypost)
	{
	$HTMLOUT .="<td class='embedded' style='padding-left: 10px'>
	<form method='get' action='".$_SERVER['PHP_SELF']."'>
	<input type='hidden' name='action' value='reply' />
	<input type='hidden' name='topicid' value='".$topicid."' />
	<input type='submit' value='Answer' /></form>
	</td>";
	}
    
    $HTMLOUT .="</tr></table>";

    if ($locked)
		{
		$HTMLOUT .= "";
		}
		else
		{
		$HTMLOUT .="<table style='border:1px solid #000000;' align='center'><tr>
		<td style='padding:10px;text-align:center;'>
		<b>Quick Reply</b>
		<form name='compose' method='post' action='".$_SERVER['PHP_SELF']."'>
		<input type='hidden' name='action' value='post' />
		<input type='hidden' name='topicid' value='".$topicid."' />
		<textarea name='body' rows='4' cols='70'></textarea><br />
		<input type='submit' class='btn' value='Submit' /><br />
		Anonymous<input type='checkbox' name='anonymous' value='yes' ".($CURUSER['anonymous'] == 'yes' ? "checked='checked'":'')." />
		</form></td></tr></table>";
	  }
    // ------ Forum quick jump drop-down
    $HTMLOUT .= insert_quick_jump_menu($forumid);

    $HTMLOUT .= end_main_frame();
    
    print stdhead("Forums :: View Topic: $subject") . $HTMLOUT . stdfoot();

    $uploaderror = (isset($_GET['uploaderror']) ? htmlspecialchars($_GET['uploaderror']) : '');

  if (!empty($uploaderror))
	{
	$HTMLOUT .="<script>alert(\"Upload Failed: {$uploaderror}\nHowever your post was successful saved!\n\nClick 'OK' to continue.\");</script>";
	}
	exit();
	


?>