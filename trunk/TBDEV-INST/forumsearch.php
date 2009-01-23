<?php
ob_start("ob_gzhandler");
require ("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");

dbconn(false);
maxcoder();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

stdhead("Forum Search");
print("<h1>Forum Search</h1>\n");
$keywords = trim($_GET["keywords"]);
if ($keywords != "")
{
$perpage = 50;
$page = max(1, 0 + $_GET["page"]);
$ekeywords = sqlesc($keywords);
print("<p><b>Searched for \"" . safechar($keywords) . "\"</b></p>\n");
$res = sql_query("SELECT COUNT(*) FROM posts WHERE MATCH (body) AGAINST ($ekeywords)") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_row($res);
$hits = 0 + $arr[0];
if ($hits == 0)
print("<p><b>Sorry, nothing found!</b></p>");
else
{
$pages = 0 + ceil($hits / $perpage);
if ($page > $pages) $page = $pages;
for ($i = 1; $i <= $pages; ++$i)
if ($page == $i)
$pagemenu1 .= "<font class=gray><b>$i</b></font>\n";
else
$pagemenu1 .= "<a href=\"/forums.php?action=search&keywords=" . safechar($keywords) . "&page=$i\"><b>$i</b></a>\n";
if ($page == 1)
$pagemenu2 = "<font class=gray><b><< Prev</b></font>\n";
else
$pagemenu2 = "<a href=\"/forums.php?action=search&keywords=" . safechar($keywords) . "&page=" . ($page - 1) . "\"><b><< Prev</b></a>\n";
$pagemenu2 .= " \n";
if ($page == $pages)
$pagemenu2 .= "<font class=gray><b>Next >></b></font>\n";
else
$pagemenu2 .= "<a href=\"/forums.php?action=search&keywords=" . safechar($keywords) . "&page=" . ($page + 1) . "\"><b>Next >></b></a>\n";
$offset = ($page * $perpage) - $perpage;
$res = sql_query("SELECT id, topicid,userid,added FROM posts WHERE MATCH (body) AGAINST ($ekeywords) LIMIT $offset,$perpage") or sqlerr(__FILE__, __LINE__);
$num = mysql_num_rows($res);
print("<p>$pagemenu1<br>$pagemenu2</p>");
print("<table border=1 cellspacing=0 cellpadding=5>\n");
print("<tr><td class=colhead>Post</td><td class=colhead align=left>Topic</td><td class=colhead align=left>Forum</td><td class=colhead align=left>Posted by</td></tr>\n");
for ($i = 0; $i < $num; ++$i)
{
$post = mysql_fetch_assoc($res);
$res2 = sql_query("SELECT forumid, subject FROM topics WHERE id=$post[topicid]") or
sqlerr(__FILE__, __LINE__);
$topic = mysql_fetch_assoc($res2);
$res2 = sql_query("SELECT name,minclassread FROM forums WHERE id=$topic[forumid]") or
sqlerr(__FILE__, __LINE__);
$forum = mysql_fetch_assoc($res2);
if ($forum["name"] == "" || $forum["minclassread"] > $CURUSER["class"])
{
--$hits;
continue;
}
$res2 = sql_query("SELECT username FROM users WHERE id=$post[userid]") or
sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_assoc($res2);
if ($user["username"] == "")
$user["username"] = "[$post[userid]]";
print("<tr><td>$post[id]</td><td align=left><a href=forums.php?action=viewtopic&topicid=$post[topicid]&page=p$post[id]#$post[id]><b>" . safechar($topic["subject"]) . "</b></a></td><td align=left><a href=forums.php?action=viewforum&forumid=$topic[forumid]><b>" . safechar($forum["name"]) . "</b></a><td align=left><a href=userdetails.php?id=$post[userid]><b>$user[username]</b></a><br>at $post[added]</tr>\n");
}
print("</table>\n");
print("<p>$pagemenu2<br>$pagemenu1</p>");
print("<p>Found $hits post" . ($hits != 1 ? "s" : "") . ".</p>");
print("<p><b>Search again</b></p>\n");
}
}
print("<form method=get action=/forums.php?>\n");
print("<input type=hidden name=action value=search>\n");
print("<table border=1 cellspacing=0 cellpadding=5>\n");
print("<tr><td class=rowhead>Key words</td><td align=left><input type=text size=55 name=keywords value=\"" . safechar($keywords) .
"\"><br>\n" .
"<font class=small size=-1>Enter one or more words to search for.<br>Very common words and words with less than 3 characters are ignored.</font></td></tr>\n");
print("<tr><td align=center colspan=2><input type=submit value='Search' class=btn></td></tr>\n");
print("</table>\n</form>\n");
stdfoot();
die;

stdfoot();
?>