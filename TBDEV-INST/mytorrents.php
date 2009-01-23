<?php
///////////////Updated mytorrents.php by Bigjoos////////////
require_once("include/bittorrent.php");
require_once("include/bbcode_functions.php");
require_once("include/user_functions.php");
dbconn();
maxcoder();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
stdhead("".safechar($CURUSER["username"])."'s Completed torrent's ");

$rescount = mysql_query("SELECT COUNT(*) FROM torrents WHERE owner = ". unsafeChar($CURUSER[id]) ." $limit") or sqlerr(__FILE__, __LINE__);
$rowcount = mysql_fetch_array($rescount);
$count = $rowcount[0];
$mytorrentsperpage = 15;
list($pagertop, $pagerbottom, $limit) = pager($mytorrentsperpage, $count, "mytorrents.php?");
$res = mysql_query("SELECT * FROM torrents WHERE owner = ". unsafeChar($CURUSER[id]) ." $limit") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res)) {
  print("$pagerbottom");
  print("<table width=80% border=0 cellspacing=0 cellpadding=3 align=center>");
  print("<tr>");
  print("<td class=colhead align=center>Cat.</td>");
  print("<td class=colhead align=center>Torrentname</td>");
  print("<td class=colhead align=center>Visible</td>");
  print("<td class=colhead align=center>Free</td>");
  print("<td class=colhead align=center>Edit</td>");
  print("<td class=colhead align=center>Files</td>");
  print("<td class=colhead align=center>Comm.</td>");
  print("<td class=colhead align=center>Views</td>");
  print("<td class=colhead align=center>Hits</td>");
  print("<td class=colhead align=center>Added</td>");
  print("<td class=colhead align=center>Last Act.</td>");
  print("<td class=colhead align=center>Size</td>");
  print("<td class=colhead align=center>Progress</td>");
  print("<td class=colhead align=center>Snatched</td>");
  print("<td class=colhead align=center>Seeders</td>");
  print("<td class=colhead align=center>Leechers</td>");
  print("</tr>");

  While ($row = mysql_fetch_assoc($res)) {
    print("<tr>");
    $cat = mysql_query("SELECT image FROM categories WHERE id = ". unsafeChar($row["category"]) ."") or sqlerr(__FILE__, __LINE__);
    while ($catrow = mysql_fetch_assoc($cat)) {
      print("<td width=5%><img src=pic/$catrow[image]></td>");
    }

    //// smallname mytorrents
    $smallname =substr(safechar($row["name"]) , 0, 40);
    if ($smallname != safechar($row["name"])) {
 	  $smallname .= '...';
    }
    #$smallname = safechar($row["name"]);
    //// smallname mytorrents end

    print("<td><a href=details.php?id=". $row[id] ."><b>". safeChar($smallname) ."</b></a></td>");

    //// colored yes/no for visible
    if (safeChar($row["visible"]) == 'yes') {
      $visible = "<font color=green>Yes</font>";
    }
    else {
      $visible = "<font color=red>No</font>";
    }
    //// colored yes/no for visible end
    print("<td align=center>".$visible."</td>");
       //// colored yes/no for golden torrents
    if (safeChar($row["countstats"]) == 'no')
      $countstats = "<font color=green>Yes</font>";
    else
      $countstats = "<font color=red>No</font>";
    //// colored yes/no for golden torrents end
    print("<td align=center>".($countstats)."</td>");
    print("<td align=center><a href=edit.php?id=". safeChar($row[id]) .">Edit</a></td>");
    print("<td align=center><a href=details.php?id=". safeChar($row[id]) ."&filelist=1#filelist>". safeChar($row["numfiles"]) ."</a></td>");
    print("<td align=center><a href=details.php?id=". safeChar($row[id]) ."&page=0#startcomments>". safeChar($row["comments"]) ."</a></td>");
    print("<td align=center>". safeChar($row["views"]) ."</td>");
    print("<td align=center>". safeChar($row["hits"]) ."</td>");
    print("<td align=center>". safeChar($row["added"]) ."</td>");
    print("<td align=center>". safeChar($row["last_action"]) ."</td>");
    print("<td align=center>". safeChar(mksize($row["size"])) ."</td>");

    //// Progress Bar
	$seedersProgressbar = array();
	$leechersProgressbar = array();
	$resProgressbar = mysql_query("SELECT p.seeder, p.to_go, t.size FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE  p.torrent = ". unsafeChar($row[id]) ."") or sqlerr(__FILE__, __LINE__);
	$progressPerTorrent = 0;
	$iProgressbar = 0;
	while ($rowProgressbar = mysql_fetch_array($resProgressbar)) {
	  $progressPerTorrent += sprintf("%.2f", 100 * (1 - ($rowProgressbar["to_go"] / $rowProgressbar["size"])));
	  $iProgressbar++;
	}
	if ($iProgressbar == 0)
	  $iProgressbar = 1;
	$progressTotal = sprintf("%.2f", $progressPerTorrent / $iProgressbar);
	$picProgress = get_percent_completed_image(floor($progressTotal))." <br>(".round($progressTotal)."%)";
    //// End Progress Bar

    print("<td align=center>".$picProgress."</td>");

    //// red color by 0 times complete
    if ($row["times_completed"] == '0')
      $times_completed = "<font color=red>". safeChar($row["times_completed"]) ." x</font>";
    elseif($row["times_completed"] < '2')
      $times_completed = "<font color=darkred>". safeChar($row["times_completed"]) ." x</font>";
    elseif($row["times_completed"] < '5')
      $times_completed = "<font color=green>". safeChar($row["times_completed"]) ." x</font>";
    else
      $times_completed = "<font color=#FFFFFF>". safeChar($row["times_completed"]) ." x</font>";
    //// red color by 0 seeders end

    print("<td align=center><a href=snatches.php?id=". $row[id] .">".$times_completed."</a></td>");

    //// red color by 0 times complete
    if ($row["seeders"] == '0')
      $seeders = "<font color=red>". safeChar($row["seeders"]) ."</font>";
    elseif($row["seeders"] < '2')
      $seeders = "<font color=darkred>". safeChar($row["seeders"]) ."</font>";
    elseif($row["seeders"] < '5')
      $seeders = "<font color=green>". safeChar($row["seeders"]) ."</font>";
    else
      $seeders = "<font color=#FFFFFF>". safeChar($row["seeders"]) ."</font>";
    //// red color by 0 seeders end

    print("<td align=center><a href=details.php?id=". $row[id] ."&dllist=1#seeders>". $seeders ."</a></td>");
    print("<td align=center><a href=details.php?id=". $row[id] ."&dllist=1#leechers>". $row["leechers"] ."</a></td>");
    print("</tr>");
  }
  print("</table>");
  print("$pagertop");
}
else {
  print("<center>Nothings here!</center>");
}

stdfoot();
?>
