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
if ( ! defined( 'IN_TBDEV_FORUM' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}


    $userid = $CURUSER['id'];

    $maxresults = 25;

    // ..rp..
    $dt = (time() - $TBDEV['readpost_expiry']);

    $res = @mysql_query("SELECT st.id, st.forumid, st.subject, st.lastpost ".
    "FROM topics AS st ".
    "LEFT JOIN posts AS sp ON st.lastpost = sp.id ".
    "WHERE sp.added > $dt ".
    "ORDER BY forumid") or sqlerr(__FILE__, __LINE__);
    //..rp..

    $HTMLOUT = '';

    $HTMLOUT .= "<h1>Topics with unread posts</h1>\n";

    $n = 0;

    $uc = get_user_class();

    while ($arr = mysql_fetch_assoc($res))
    {
      $topicid = $arr['id'];

      $forumid = $arr['forumid'];

      //---- Check if post is read
      $r = @mysql_query("SELECT lastpostread FROM readposts WHERE userid=$userid AND topicid=$topicid") or sqlerr(__FILE__, __LINE__);

      $a = mysql_fetch_row($r);

      if ($a && $a[0] == $arr['lastpost'])
        continue;

      //---- Check access & get forum name
      $r = @mysql_query("SELECT name, minclassread FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

      $a = mysql_fetch_assoc($r);

      if ($uc < $a['minclassread'])
        continue;

      ++$n;

      if ($n > $maxresults)
        break;

      $forumname = $a['name'];

      if ($n == 1)
      {
        $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='5'>\n";

        $HTMLOUT .= "<tr>
          <td class='colhead' align='left'>Topic</td>
          <td class='colhead' align='left'>Forum</td>
        </tr>\n";
      }

      $HTMLOUT .= "<tr>
        <td align='left'>
          <table border=0 cellspacing=0 cellpadding=0>
            <tr>
              <td class=embedded><img src='{$forum_pic_url}unlockednew.gif' style='margin-right: 5px'>
              </td>
              <td class='embedded'><a href='forums.php?action=viewtopic&amp;topicid=$topicid&amp;page=last#last'><b>" . htmlspecialchars($arr["subject"]) . "</b></a>
              </td>
            </tr>
          </table>
        </td>
        <td align='left'><a href='forums.php?action=viewforum&amp;forumid=$forumid'><b>$forumname</b></a>
        </td>
      </tr>\n";
    }
    if ($n > 0)
    {
      $HTMLOUT .= "</table>\n";

      if ($n > $maxresults)
        $HTMLOUT .= "<p>More than $maxresults items found, displaying first $maxresults.</p>\n";

      $HTMLOUT .= "<p><a href='forums.php?catchup'><b>Catch up</b></a></p>\n";
    }
    else
      $HTMLOUT .= "<b>Nothing found</b>";

    print stdhead('Unread Posts') . $HTMLOUT . stdfoot();

    die;
 ?>