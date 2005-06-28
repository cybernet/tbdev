<?
        ob_start("ob_gzhandler");

  require "include/bittorrent.php";

  dbconn(false);

  loggedinorreturn();

  ////////////////////start functions////////////////////
  
  function get_forum_last_post($forumid)
  {
    $res = mysql_query("SELECT lastpost FROM topics WHERE forumid=$forumid ORDER BY lastpost DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_row($res);

    $postid = $arr[0];

    if ($postid)
      return $postid;

    else
      return 0;
  }
  
  //-------- Get forums

//---------Cf Mod
mysql_query("UPDATE users SET forum_access='" . get_date_time() . "' WHERE id={$CURUSER["id"]}");// or die(mysql_error());
//---------END Cf Mod

  $forums_res = mysql_query("SELECT * FROM forums ORDER BY sort, name") or sqlerr(__FILE__, __LINE__);

  stdhead("Forums");

  print("<h1>Forums</h1>\n");

  print("<table border=1 cellspacing=0 cellpadding=5>\n");

  print("<tr><td class=colhead align=left>Forum</td><td class=colhead align=right>Topics</td>" .
  "<td class=colhead align=right>Posts</td>" .
  "<td class=colhead align=left>Last post</td></tr>\n");

        $color1 = "#F9F9F9";
        $color2 = "#EFF3FF";
        $row_count = 0;

  while ($forums_arr = mysql_fetch_assoc($forums_res))
  {
    if (get_user_class() < $forums_arr["minclassread"])
      continue;

        $row_color = ($row_count % 2) ? $color1 : $color2;

    $forumid = $forums_arr["id"];

    $forumname = htmlspecialchars($forums_arr["name"]);

    $forumdescription = htmlspecialchars($forums_arr["description"]);

    $topiccount = number_format($forums_arr["topiccount"]);

    $postcount = number_format($forums_arr["postcount"]);

    // Find last post ID

    $lastpostid = get_forum_last_post($forumid);

    // Get last post info

    $post_res = mysql_query("SELECT added,topicid,userid FROM posts WHERE id=$lastpostid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($post_res) == 1)
    {
      $post_arr = mysql_fetch_assoc($post_res) or die("Bad forum last_post");

      $lastposterid = $post_arr["userid"];

      $lastpostdate = $post_arr["added"];

      $lasttopicid = $post_arr["topicid"];

      $user_res = mysql_query("SELECT username FROM users WHERE id=$lastposterid") or sqlerr(__FILE__, __LINE__);

      $user_arr = mysql_fetch_assoc($user_res);

      $lastposter = htmlspecialchars($user_arr['username']);

      $topic_res = mysql_query("SELECT subject FROM topics WHERE id=$lasttopicid") or sqlerr(__FILE__, __LINE__);

      $topic_arr = mysql_fetch_assoc($topic_res);

      $lasttopic = htmlspecialchars($topic_arr['subject']);

      $lastpost = "<nobr>$lastpostdate<br>" .
      "by <a href=userdetails.php?id=$lastposterid><b>$lastposter</b></a><br>" .
      "in <a href=?action=viewtopic&topicid=$lasttopicid&amp;page=p$lastpostid#$lastpostid><b>$lasttopic</b></a></nobr>";

      $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=$CURUSER[id] AND topicid=$lasttopicid") or sqlerr(__FILE__, __LINE__);

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
    print("<tr><td bgcolor=$row_color align=left><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded style='padding-right: 5px'><img src=".
    "$forum_pics/$img.gif></td><td bgcolor=$row_color class=embedded><a href=forumstopics.php?action=viewforum&forumid=$forumid><b>$forumname</b></a><br>\n" .
    "$forumdescription</td></tr></table></td><td align=right>$topiccount</td></td><td align=right>$postcount</td>" .
    "<td bgcolor=$row_color align=left>$lastpost</td></tr>\n");
 $row_count++; }

  print("</table>\n");

  print("<p align=center><a href=forumstools.php?action=search><b>Search</b></a> | <a href=forumstools.php?action=viewunread><b>View unread</b></a> | <a href=forumstools.php?action=catchup><b>Catch up</b></a></p>");

/* Cf Mod */
$forum_t = gmtime() - 600;
$forum_t = sqlesc(get_date_time($forum_t));

$res = mysql_query("SELECT id, username, class FROM users WHERE forum_access >= $forum_t ORDER BY forum_access DESC") or print(mysql_error());
while ($arr = mysql_fetch_assoc($res))
{
  if (!isset($forumusers)) $forumusers = "";
  else
  $forumusers .= ",\n";
  switch ($arr["class"])
  {
    case UC_SYSOP:
    case UC_ADMINISTRATOR:
    case UC_MODERATOR:
      $arr["username"] = "<font color=orange>{$arr["username"]}</font>";
      break;
     case UC_UPLOADER:
      $arr["username"] = "<font color=#4040C0>{$arr["username"]}</font>";
      break;
  }

  if ($CURUSER)
		if (get_user_class() >= UC_MODERATOR) {
	
    $forumusers .= "<a href=userdetails.php?id={$arr["id"]}><b>{$arr["username"]}</b>{$arr["class"]}</a>";
		}else{
		if ($arr["class"] < 4)
		{
		$forumusers .= "<a href=userdetails.php?id={$arr["id"]}><b>{$arr["username"]}</b>{$arr["class"]}</a>";
		}
		}
}
if (!$forumusers)
  $forumusers = "There have been no active users in the last 15 minutes.";
?>
<br>
<table width=50% border=1 cellspacing=0 cellpadding=5><tr>
<td class="colhead" align="left">Active Forum Users</td></tr>
</tr><td class=text>
<?=$forumusers?>
</td></tr></table>
<?

/*End Cf Mod */


echo "<p>Generated in " . ( number_format( getmicrotime( ) - $generated, 4 ) ) . " seconds.</p>\n";
  stdfoot();
?>