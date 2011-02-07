<?php

// -------- Default action: View forums
            if (isset($_GET["catchup"])) {
                catch_up();

                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            }
            $f_mod='';
            mysql_query("UPDATE users SET forum_access = '" . time() . "' WHERE id={$CURUSER['id']}") or sqlerr(__FILE__, __LINE__);
            $sub_forums = mysql_query(" SELECT f.id, f2.name, f2.id AS subid,f2.postcount,f2.topiccount, p.added, p.anonymous, p.userid, p.id AS pid, u.username, t.subject,t.id as tid,r.lastpostread,t.lastpost
									FROM forums AS f
									LEFT JOIN forums AS f2 ON f2.place = f.id AND f2.minclassread<=" . sqlesc($CURUSER["class"]) . "
									LEFT JOIN posts AS p ON p.id = (SELECT MAX(lastpost) FROM topics WHERE forumid = f2.id )
									LEFT JOIN users AS u ON u.id = p.userid
									LEFT JOIN topics AS t ON t.id = p.topicid
									LEFT JOIN readposts AS r ON r.userid =" . sqlesc($CURUSER["id"]) . " AND r.topicid = p.topicid
									ORDER BY t.lastpost ASC, f2.name , f.id ASC
									");
            while ($a = mysql_fetch_assoc($sub_forums)) {
                if ($a["subid"] == 0)
                    $forums[$a["id"]] = false;
                else {
                    $forums[$a["id"]]["lastpost"] = array("anonymous" => $a["anonymous"],"postid" => $a["pid"], "userid" => $a["userid"], "user" => $a["username"], "topic" => $a["subject"], "topic" => $a["tid"], "tname" => $a["subject"], "added" => $a["added"]);
                    $forums[$a["id"]]["count"][] = array("posts" => $a["postcount"], "topics" => $a["topiccount"]);
                    $forums[$a["id"]]["topics"][] = array ("id" => $a["subid"], "name" => $a["name"], "new" => ($a["lastpost"]) != $a["lastpostread"] ? 1 : 0);
                }
            }
            $r_mod = mysql_query("SELECT f.id,m.user,m.uid FROM forums as f LEFT JOIN forum_mods as m ON f.id = m.fid ORDER BY f.id ") or sqlerr(__FILE__, __LINE__);

            while ($a_mod = mysql_fetch_assoc($r_mod)) {
                if (!isset($a_mod["uid"]))
                    $f[$a_mod["id"]] = false;
                else
                    $f_mod[$a_mod["id"]][] = array("user" => $a_mod["user"], "id" => $a_mod["uid"]);
            }
           

            if ($TBDEV['forums_online'] == 0)
            $HTMLOUT .= stdmsg('Warning', 'Forums are currently in maintainance mode');
            $HTMLOUT .= begin_main_frame();

           $HTMLOUT .="<h1 align='center'><b>{$TBDEV['site_name']} - Forum</b></h1>
	<br />
	<table border='1' cellspacing='0' cellpadding='5' width='{$forum_width}'>";
	$ovf_res = mysql_query("SELECT id, name, minclassview FROM forum_parents ORDER BY sort ASC") or sqlerr(__FILE__, __LINE__);
	while ($ovf_arr = mysql_fetch_assoc($ovf_res))
	{
	if ($CURUSER['class'] < $ovf_arr["minclassview"])
	continue;
  $ovfid = (int)$ovf_arr["id"];
  $ovfname = $ovf_arr["name"];
	$HTMLOUT .="<tr>
      <td align='left' class='colhead' width='100%'><a href='".$_SERVER['PHP_SELF']."?action=forumview&amp;forid=".$ovfid."'>
      <b><font color='white'>".htmlspecialchars($ovfname)."</font></b></a></td>
			<td align='right' class='colhead'><font color='white'><b>Topics</b></font></td>
			<td align='right' class='colhead'><font color='white'><b>Posts</b></font></td>
			<td align='left' class='colhead'><font color='white'><b>Last post</b></font></td>
		</tr>";
    
    $HTMLOUT .= show_forums($ovfid, false, $forums, $f_mod, true);
    }
    $HTMLOUT .= end_table();

            if ($use_forum_stats_mod)
                $HTMLOUT .= forum_stats();

	$HTMLOUT .="<p align='center'>
	<a href='". $_SERVER['PHP_SELF']."?action=search'><b>Search Forums</b></a> | 
	<a href='". $_SERVER['PHP_SELF']."?action=viewunread'><b>New Posts</b></a> | 
	<a href='". $_SERVER['PHP_SELF']."?action=getdaily'><b>Todays Posts (Last 24 h.)</b></a> | 
	<a href='". $_SERVER['PHP_SELF']."?catchup'><b>Mark all as read</b></a>";
	$HTMLOUT .="</p>";
	$HTMLOUT .= end_main_frame(); 
print stdhead("Forum") . $HTMLOUT . stdfoot();


?>