<?php

// -------- Action: New topic
    $forumid = (int)$_GET["forumid"];
    
    if (!is_valid_id($forumid))
        stderr('Error', 'Invalid ID!');
        
    $res = mysql_query("SELECT name FROM forums WHERE id = " . sqlesc($forumid)) or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res) or die("Bad forum ID!");

    $body = '';
    
    $HTMLOUT .= begin_main_frame();
    
    if ($TBDEV['forums_online'] == 0)
    $HTMLOUT .= stdmsg('Warning', 'Forums are currently in maintainance mode'); 
       
    if( isset($_POST['body']) )
    {
      $body = trim($_POST["body"]);

      $HTMLOUT .= begin_frame("Preview Post", true);

      $HTMLOUT .="
      <div align='center' style='border: 0;'>
      <div align='center'>
      <p>".format_comment($body)."</p>
      </div>
      </div> ";

      $HTMLOUT .= end_frame();
    }
   
    $HTMLOUT .="<h3>New topic in <a href='". $_SERVER['PHP_SELF']."?action=viewforum&amp;forumid=".$forumid."'>".htmlspecialchars($arr["name"])."</a> forum</h3>

    <script  type='text/javascript'>
    /*<![CDATA[*/
    function Preview()
    {
    document.compose.action = './forums.php?action=preview&forumid=$forumid'
    //document.compose.target = '_blank';
    document.compose.submit();
    return true;
    }
    /*]]>*/
    </script>";
      
    $HTMLOUT .= begin_frame("Compose", true);
    $HTMLOUT .="<form method='post' name='compose' action='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data'>
	  <input type='hidden' name='action' value='post' />
	  <input type='hidden' name='forumid' value='$forumid' />";

    $HTMLOUT .= begin_table(true);

    $HTMLOUT .="<tr>
			<td class='rowhead' width='10%'>Subject</td>
			<td align='left'>
				<input type='text' size='100' maxlength='".$maxsubjectlength."' name='subject' style='height: 19px' />
			</td>
		</tr>
    <tr>
		<td class='rowhead' width='10%'>Body</td>
		<td>";
		
		if (function_exists('textbbcode'))
      $HTMLOUT .= textbbcode("compose", "body", $qbody);
		else
		{
		$HTMLOUT .="<textarea name='body' style='width:99%' rows='7'>{$body}</textarea>";
		}
      $HTMLOUT .="</td></tr>";
		if($use_attachment_mod)
		{
      $HTMLOUT .="<tr>
				<td colspan='2'><fieldset class='fieldset'><legend>Add Attachment</legend>
				<input type='checkbox' name='uploadattachment' value='yes' />
				<input type='file' name='file' size='60' />
        <div class='error'>Allowed Files: rar, zip<br />Size Limit ".mksize($maxfilesize)."</div></fieldset>
				</td>
			</tr>";
		}
    
    $HTMLOUT .="<tr>
   	  <td align='center' colspan='2'>".(post_icons())."</td>
 	    </tr><tr>
 		  <td colspan='2' align='center'>
 	    <input type='submit' value='Submit' /><input type='button' value='Preview' name='button2' onclick='return Preview();' />
      Anonymous Topic<input type='checkbox' name='anonymous' value='yes'/>
      </td></tr>\n";


    $HTMLOUT .= end_table();

    $HTMLOUT .="</form>";
    
    $HTMLOUT .= end_frame();
    
    $HTMLOUT .= end_main_frame();
    print stdhead("New Topic") . $HTMLOUT . stdfoot();
    exit();

?>