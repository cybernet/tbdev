<?php

// -------- Action: Reply
  $topicid = (int)$_GET["topicid"];
  
  if (!is_valid_id($topicid))
      stderr('Error', 'Invalid ID!');

  $res = mysql_query("SELECT t.forumid, t.subject, t.locked, f.minclassread FROM topics AS t LEFT JOIN forums AS f ON f.id = t.forumid WHERE t.id = ".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
  $arr = mysql_fetch_assoc($res) or stderr($lang['forum_functions_error'], $lang['forum_functions_topic']);
  
  if ($arr['locked'] == 'yes') 
  {
    stderr("Sorry", "The topic is locked.");

    $HTMLOUT .= end_table();
    $HTMLOUT .= end_main_frame();
    print stdhead("Compose", '', $fcss) . $HTMLOUT . stdfoot();
    exit();
  }

  if($CURUSER["class"] < $arr["minclassread"])
  {
    $HTMLOUT .= stdmsg("Sorry", "You are not allowed in here.");
    $HTMLOUT .= end_table(); 
    $HTMLOUT .= end_main_frame(); 
    print stdhead("Compose", '', $fcss) . $HTMLOUT . stdfoot();
    exit();
  }

  $subject = htmlsafechars($arr["subject"]);

  $HTMLOUT .= "<p style='text-align:center;'>{$lang['forum_functions_reply']}<a href='forums.php?action=viewtopic&amp;topicid=$topicid'>$subject</a></p>";
  
  $HTMLOUT .="
  <script  type='text/javascript'>
  /*<![CDATA[*/
  function Preview()
  {
  document.bbcode2text.action = './forums.php?action=reply&topicid=$topicid'
  //document.bbcode2text.target = '_blank';
  document.bbcode2text.submit();
  return true;
  }
  /*]]>*/
  </script>";
  
  $body = isset($_POST["body"]) ? strip_tags( trim($_POST["body"]) ) : '';
  $title = '';
  
  $HTMLOUT .= begin_main_frame();
  
  if ($TBDEV['forums_online'] == 0)
  $HTMLOUT .= stdmsg('Warning', 'Forums are currently in maintainance mode');
  // prolly no longer need this
  if( $body != '' )
  {
    $HTMLOUT .= begin_frame("Preview Post", true);
    $HTMLOUT .="
    <div style='text-align:left;border: 0;'>
    <p>".format_comment($body)."</p>
    </div>";
    $HTMLOUT .= end_frame();
  }
  // nope
  
  
  $HTMLOUT .= begin_frame("Compose", true);
  $HTMLOUT .="<form name='bbcode2text' method='post' action='forums.php?action=post' enctype='multipart/form-data'>
  <input type='hidden' name='action' value='post' />
  <input type='hidden' name='topicid' value='{$topicid}' />";
  
  $HTMLOUT .= bbcode2textarea( $lang['forum_functions_submit'], $body, $title );
  
  $HTMLOUT .="<div>".(post_icons())."</div>
  <div>
  <input type='button' value='Preview' name='button2' onclick='return Preview();' />
  Anonymous Post<input type='checkbox' name='anonymous' value='yes'/>
  </div>
  </form>";

  $HTMLOUT .= end_frame();
  
  $HTMLOUT .= end_main_frame();
  
  $js = "<script type='text/javascript' src='scripts/bbcode2text.js'></script>";
  
  print stdhead($lang['forum_reply_reply'], $js, $fcss) . $HTMLOUT . stdfoot();
  exit();


?>