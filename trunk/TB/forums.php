<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2009 TBDev.Net
|   http://www.tbdev.net
|   =============================================
|   svn: http://sourceforge.net/projects/tbdevnet/
|   Licence Info: GPL
+------------------------------------------------
|   $Date$
|   $Revision$
|   $Author$
|   $URL$
+------------------------------------------------
*/
	define('IN_TBDEV_FORUM', TRUE);

  require_once "include/bittorrent.php";
  require_once "include/user_functions.php";
  require_once "include/html_functions.php";
  //require_once "include/bbcode_functions.php";
  require_once "forums/forum_functions.php";

  dbconn(false);

  loggedinorreturn();

  $action = isset($_GET["action"]) ? $_GET["action"] : '';
  $forum_pic_url = $TBDEV['pic_base_url'] . 'forumicons/';
    //-------- Global variables

  $maxsubjectlength = 40;
  $postsperpage = $CURUSER["postsperpage"];
	if (!$postsperpage) $postsperpage = 25;

  switch($action) {
  
    case 'viewforum':
      require_once "forums/forum_view.php";
      exit();
      break;
      
    case 'viewtopic':
      require_once "include/bbcode_functions.php";
      require_once "forums/forum_topicview.php";
      exit();
      break;
      
    case 'reply':
    case 'quotepost':
      require_once "include/bbcode_functions.php";
      require_once "forums/forum_reply.php";
      exit();
      break;
      
    case 'post':
      require_once "forums/forum_post.php";
      exit();
      break;
      
    case 'newtopic':
      require_once "forums/forum_new_topic.php";
      exit();
      break;
    
    case 'deletepost':
    case 'editpost':
      require_once "forums/forum_user_options.php";
      exit();
      break;
      
    case 'locktopic':
    case 'unlocktopic':
    case 'setlocked':
    case 'renametopic':
    case 'setsticky':
    case 'deletetopic':
    case 'movetopic':
      require_once "forums/forum_mod_options.php";
      exit();
      break;
      
    case 'viewunread':
      require_once "forums/forum_view_unread.php";
      exit();
      break;
      
    case 'search':
      require_once "forums/forum_search.php";
      exit();
      break;
      
    default:
      std_view();
      break;
  }


function std_view() {

  global $TBDEV, $CURUSER, $forum_pic_url;
  
  $forums_res = mysql_query("SELECT * FROM forums ORDER BY sort, name") or sqlerr(__FILE__, __LINE__);

  stdhead("Forums");

  $htmlout = "<h1>Forums</h1>\n";
  
  $htmlout .= "<div style='width:80%'><p style='text-align:right;'><span class='btn'><a href='forums.php?action=search'>Search</a></span>&nbsp;<span class='btn'><a href='forums.php?action=viewunread'>View unread</a></span>&nbsp;<span class='btn'><a href='forums.php?action=catchup'>Catch up</a></span></p></div>";
  
  $htmlout .="<table border='1' cellspacing='0' cellpadding='5' width='80%'>\n";

  $htmlout .= "<tr><td class='colhead' style='text-align:left;'>Forum</td><td class='colhead' style='text-align:right;'>Topics</td>" .
  "<td class='colhead' style='text-align:right;'>Posts</td>" .
  "<td class='colhead' style='text-align:left;'>Last post</td></tr>\n";

  while ($forums_arr = mysql_fetch_assoc($forums_res))
  {
    if (get_user_class() < $forums_arr["minclassread"])
      continue;

    $forumid = $forums_arr["id"];

    $forumname = htmlspecialchars($forums_arr["name"]);

    $forumdescription = htmlspecialchars($forums_arr["description"]);

    $topiccount = number_format($forums_arr["topiccount"]);

    $postcount = number_format($forums_arr["postcount"]);

    $lastpostid = get_forum_last_post($forumid);

    // Get last post info

    $post_res = mysql_query("SELECT p.added, p.topicid, p.userid, u.username, t.subject
							FROM posts p
							LEFT JOIN users u ON p.userid = u.id
							LEFT JOIN topics t ON p.topicid = t.id
							WHERE p.id = $lastpostid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($post_res) == 1)
    {
      $post_arr = mysql_fetch_assoc($post_res) or die("Bad forum last_post");

      $lastposterid = $post_arr["userid"];

      $lastpostdate = get_date( $post_arr["added"],'' );

      $lasttopicid = $post_arr["topicid"];

      //$user_res = mysql_query("SELECT username FROM users WHERE id=$lastposterid") or sqlerr(__FILE__, __LINE__);

      //$user_arr = mysql_fetch_assoc($user_res);

      $lastposter = htmlspecialchars($post_arr['username']);

      //$topic_res = mysql_query("SELECT subject FROM topics WHERE id=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      //$topic_arr = mysql_fetch_assoc($topic_res);

      $lasttopic = htmlspecialchars($post_arr['subject']);

      $lastpost = "<span style='white-space: nowrap;'>$lastpostdate</span><br />" .
      "by <a href='userdetails.php?id=$lastposterid'><b>$lastposter</b></a><br />" .
      "in <a href='forums.php?action=viewtopic&amp;topicid=$lasttopicid&amp;page=p$lastpostid#$lastpostid'><b>$lasttopic</b></a>";

      $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid={$CURUSER['id']} AND topicid=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      $a = mysql_fetch_row($r);

	//..rp..
	$npostcheck = ($post_arr['added'] > (time() - $TBDEV['readpost_expiry'])) ? (!$a OR $lastpostid > $a[0]) : 0;
	
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
    $htmlout .= "<tr><td style='text-align:left;'>".
    "<img src=\"{$forum_pic_url}$img.gif\" alt='' title='' />".
    "<a href='forums.php?action=viewforum&amp;forumid=$forumid'><b>$forumname</b></a><br />\n" .
    "$forumdescription</td>".
    "<td style='text-align:right;'>$topiccount</td>".
    "<td style='text-align:right;'>$postcount</td>" .
    "<td style='text-align:left;'>$lastpost</td></tr>\n";
  }

  $htmlout .= "</table>\n<br />\n";

  $htmlout .= "<div style='width:80%'><p style='text-align:right;'><span class='btn'><a href='forums.php?action=search'>Search</a></span>&nbsp;<span class='btn'><a href='forums.php?action=viewunread'>View unread</a></span>&nbsp;<span class='btn'><a href='forums.php?action=catchup'>Catch up</a></span></p></div>";
  
  echo $htmlout;

  stdfoot();
  exit();
}
?>