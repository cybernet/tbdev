<?
        ob_start("ob_gzhandler");

  require "include/bittorrent.php";

  dbconn(false);

  loggedinorreturn();
  
    //-------- Global variables

  $maxsubjectlength = 40;
  $postsperpage = $CURUSER["postsperpage"];
        if (!$postsperpage) $postsperpage = 25;
  
  ////////////////topic functions////////////////
    //-------- Returns the minimum read/write class levels of a forum

  function get_forum_access_levels($forumid)
  {
    $res = mysql_query("SELECT minclassread, minclasswrite, minclasscreate FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) != 1)
      return false;

    $arr = mysql_fetch_assoc($res);

    return array("read" => $arr["minclassread"], "write" => $arr["minclasswrite"], "create" => $arr["minclasscreate"]);
  }

  //-------- Inserts a quick jump menu

  function insert_quick_jump_menu($currentforum = 0)
  {
    print("<p align=center><form method=get action=? name=jump>\n");

    print("<input type=hidden name=action value=viewforum>\n");

    print("Quick jump: ");

    print("<select name=forumid onchange=\"if(this.options[this.selectedIndex].value != -1){ forms['jump'].submit() }\">\n");

    $res = mysql_query("SELECT * FROM forums ORDER BY name") or sqlerr(__FILE__, __LINE__);

    while ($arr = mysql_fetch_assoc($res))
    {
      if (get_user_class() >= $arr["minclassread"])
        print("<option value=" . $arr["id"] . ($currentforum == $arr["id"] ? " selected>" : ">") . $arr["name"] . "\n");
    }

    print("</select>\n");

    print("<input type=submit value='Go!'>\n");

    print("</form>\n</p>");
  }

////////////////end topic functions///////////////

  $action = $HTTP_GET_VARS["action"];

//-------- Action: View forum

  if (!$action == "viewforum")
  die;
  
    $forumid = $_GET["forumid"];

    if (!is_valid_id($forumid))
      die;

    //if(isset($_GET["page"])) $page = $_GET["page"];
    $page = isset( $_GET['page'] ) ? $_GET['page'] : $_GET['page']=0;

    $userid = $CURUSER["id"];

    //------ Get forum name

    $res = mysql_query("SELECT name, minclassread FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    $arr = mysql_fetch_assoc($res) or die;

    $forumname = $arr["name"];

    if (get_user_class() < $arr["minclassread"])
      die("Not permitted");

    //------ Page links

    //------ Get topic count

    $perpage = $CURUSER["topicsperpage"];
        if (!$perpage) $perpage = 17;

    $res = mysql_query("SELECT COUNT(*) FROM topics WHERE forumid=$forumid") or sqlerr(__FILE__, __LINE__);

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

    $menu = "<p align=center><b>\n";

    $lastspace = false;

    for ($i = 1; $i <= $pages; ++$i)
    {
            if ($i == $page)
        $menu .= "<font class=gray>$i</font>\n";

      elseif ($i > 3 && ($i < $pages - 2) && ($page - $i > 3 || $i - $page > 3))
            {
                    if ($lastspace)
                      continue;

                    $menu .= "... \n";

                     $lastspace = true;
            }

      else
      {
        $menu .= "<a href=?action=viewforum&forumid=$forumid&page=$i>$i</a>\n";

        $lastspace = false;
      }
      if ($i < $pages)
        $menu .= "</b>|<b>\n";
    }

    $menu .= "<br>\n";

    if ($page == 1)
      $menu .= "<font class=gray>&lt;&lt; Prev</font>";

    else
      $menu .= "<a href=?action=viewforum&forumid=$forumid&page=" . ($page - 1) . ">&lt;&lt; Prev</a>";

    $menu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    if ($last == $num)
      $menu .= "<font class=gray>Next &gt;&gt;</font>";

    else
      $menu .= "<a href=?action=viewforum&forumid=$forumid&page=" . ($page + 1) . ">Next &gt;&gt;</a>";

    $menu .= "</b></p>\n";

    $offset = $first - 1;

    //------ Get topics data

    $topicsres = mysql_query("SELECT * FROM topics WHERE forumid=$forumid ORDER BY sticky, lastpost DESC LIMIT $offset,$perpage") or
      stderr("SQL Error", mysql_error());

    stdhead("Forum");

    $numtopics = mysql_num_rows($topicsres);

    print("<h1>$forumname</h1>\n");

    if ($numtopics > 0)
    {
      print($menu);

      print("<table border=1 cellspacing=0 cellpadding=5>");

      print("<tr><td class=colhead align=left>Topic</td><td class=colhead>Replies</td><td class=colhead>Views</td>\n" .
        "<td class=colhead align=left>Author</td><td class=colhead align=left>Last&nbsp;post</td>\n");

      print("</tr>\n");

        $color1 = "#F9F9F9";
        $color2 = "#EFF3FF";
        $row_count = 0;

      while ($topicarr = mysql_fetch_assoc($topicsres))
      {
        $row_color = ($row_count % 2) ? $color1 : $color2;

        $topicid = $topicarr["id"];

        $topic_userid = $topicarr["userid"];

        $topic_views = $topicarr["views"];

                $views = number_format($topic_views);

        $locked = $topicarr["locked"] == "yes";

        $sticky = $topicarr["sticky"] == "yes";

        //---- Get reply count

        $res = mysql_query("SELECT COUNT(*) FROM posts WHERE topicid=$topicid") or sqlerr(__FILE__, __LINE__);

        $arr = mysql_fetch_row($res);

        $posts = $arr[0];

        $replies = max(0, $posts - 1);

        $tpages = floor($posts / $postsperpage);

        if ($tpages * $postsperpage != $posts)
          ++$tpages;

        if ($tpages > 1)
        {
          $topicpages = " (<img src=$forum_pics/multipage.gif>";

          for ($i = 1; $i <= $tpages; ++$i)
            $topicpages .= " <a href=forumsposts.php?action=viewtopic&topicid=$topicid&page=$i>$i</a>";

          $topicpages .= ")";
        }
        else
          $topicpages = "";

        //---- Get userID and date of last post

        $res = mysql_query("SELECT * FROM posts WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);

        $arr = mysql_fetch_assoc($res);

        $lppostid = 0 + $arr["id"];

        $lpuserid = 0 + $arr["userid"];

        $lpadded = "<nobr>" . $arr["added"] . "</nobr>";

        //------ Get name of last poster

        $res = mysql_query("SELECT * FROM users WHERE id=$lpuserid") or sqlerr(__FILE__, __LINE__);

        if (mysql_num_rows($res) == 1)
        {
          $arr = mysql_fetch_assoc($res);

          $lpusername = "<a href=userdetails.php?id=$lpuserid><b>$arr[username]</b></a>";
        }
        else
          $lpusername = "unknown[$topic_userid]";

        //------ Get author

        $res = mysql_query("SELECT username FROM users WHERE id=$topic_userid") or sqlerr(__FILE__, __LINE__);

        if (mysql_num_rows($res) == 1)
        {
          $arr = mysql_fetch_assoc($res);

          $lpauthor = "<a href=userdetails.php?id=$topic_userid><b>$arr[username]</b></a>";
        }
        else
          $lpauthor = "unknown[$topic_userid]";

        //---- Print row

        $r = mysql_query("SELECT lastpostread FROM readposts WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);

        $a = mysql_fetch_row($r);

        $new = !$a || $lppostid > $a[0];

        $topicpic = ($locked ? ($new ? "lockednew" : "locked") : ($new ? "unlockednew" : "unlocked"));

        $subject = ($sticky ? "Sticky: " : "") . "<a href=forumsposts.php?action=viewtopic&topicid=$topicid><b>" .
        encodehtml($topicarr["subject"]) . "</b></a>$topicpages";

        print("<tr><td bgcolor=$row_color align=left><table border=0 cellspacing=0 cellpadding=0><tr>" .
        "<td class=embedded style='padding-right: 5px'><img src=$forum_pics/$topicpic.gif>" .
        "</td><td bgcolor=$row_color class=embedded align=left>\n" .
        "$subject</td></tr></table></td><td align=right>$replies</td>\n" .
        "<td align=right>$views</td><td bgcolor=$row_color align=left>$lpauthor</td>\n" .
        "<td bgcolor=$row_color align=left>$lpadded<br>by&nbsp;$lpusername</td>\n");

        print("</tr>\n");

        $row_count++;
      } // while

      print("</table>\n");

      print($menu);

    } // if
    else
      print("<p align=center>No topics found</p>\n");

    print("<p><table class=main border=0 cellspacing=0 cellpadding=0><tr valing=center>\n");

    print("<td class=embedded><img src=$forum_pics/unlockednew.gif style='margin-right: 5px'></td><td class=embedded>New posts</td>\n");

    print("<td class=embedded><img src=$forum_pics/locked.gif style='margin-left: 10px; margin-right: 5px'>" .
    "</td><td class=embedded>Locked topic</td>\n");

    print("</tr></table></p>\n");

    $arr = get_forum_access_levels($forumid) or die;

    $maypost = get_user_class() >= $arr["write"] && get_user_class() >= $arr["create"];

    if (!$maypost)
      print("<p><i>You are not permitted to start new topics in this forum.</i></p>\n");

    print("<p><table border=0 class=main cellspacing=0 cellpadding=0><tr>\n");

    print("<td class=embedded><form method=get action=?><input type=hidden " .
    "name=action value=viewunread><input type=submit value='View unread' class=btn></form></td>\n");

    if ($maypost)
      print("<td class=embedded><form method=get action=?><input type=hidden " .
      "name=action value=newtopic><input type=hidden name=forumid " .
      "value=$forumid><input type=submit value='New topic' class=btn style='margin-left: 10px'></form></td>\n");

    print("</tr></table></p>\n");

    insert_quick_jump_menu($forumid);

echo "<p>Generated in " . ( number_format( getmicrotime( ) - $generated, 4 ) ) . " seconds.</p>\n";

    stdfoot();
?>