<?php
require ("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
function bark($msg)
{
  stdhead();
  stdmsg("Error", $msg);
  stdfoot();
  exit;
}

function snatchtable($res) {

$table = "<table class=main border=1 cellspacing=0 cellpadding=5>
<tr>
<td class=colhead>Category</td>
<td class=colhead>Torrent</td>
<td class=colhead>S.id</td>
<td class=colhead>Up.</td>
<td class=colhead>Rate</td>
<td class=colhead>Downl.</td>
<td class=colhead>Rate</td>
<td class=colhead>Ratio</td>
<td class=colhead>Activity</td>
<td class=colhead>Finished</td>
</tr>";

while ($arr = mysql_fetch_assoc($res)) {

$upspeed = ($arr["upspeed"] > 0 ? mksize($arr["upspeed"]) : ($arr["seedtime"] > 0 ? mksize($arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize(0)));
$downspeed = ($arr["downspeed"] > 0 ? mksize($arr["downspeed"]) : ($arr["leechtime"] > 0 ? mksize($arr["downloaded"] / $arr["leechtime"]) : mksize(0)));
$ratio = ($arr["downloaded"] > 0 ? number_format($arr["uploaded"] / $arr["downloaded"], 3) : ($arr["uploaded"] > 0 ? "Inf." : "---"));
$id=$arr[id];
$table .= "<tr>
<td style='padding: 0px'><img src='pic/".safechar($arr["catimg"])."' alt='".safechar($arr["catname"])."' width=42 height=42></td>
<td><a href=details.php?id=$arr[torrentid]><b>".(strlen($arr["name"]) > 50 ? substr($arr["name"], 0, 50 - 3)."..." : $arr["name"])."</b></a></td>
<td>".($arr["id"])."</td>
<td>".mksize($arr["uploaded"])."</td>
<td>$upspeed/s</td>
<td>".mksize($arr["downloaded"])."</td>
<td>$downspeed/s</td>
<td>$ratio</td>
<td>".mkprettytime($arr["seedtime"] + $arr["leechtime"])."</td>
<td>".($arr["complete_date"] <> "0000-00-00 00:00:00" ? "<font color=green><b>Yes</b></font>" : "<font color=red><b>No</b></font>")."</td>
</tr>\n";
}
$table .= "</table>\n";

return $table;
}

function maketable($res)
{
  $ret = "<table class=main border=1 cellspacing=0 cellpadding=5>" .
    "<tr><td class=colhead align=center>Type</td><td class=colhead>Name</td><td class=colhead align=center>Size</td><td class=colhead align=right>Se.</td><td class=colhead align=right>Le.</td><td class=colhead align=center>Upl.</td>\n" .
    "<td class=colhead align=center>Downl.</td><td class=colhead align=center>Ratio</td></tr>\n";
  while ($arr = mysql_fetch_assoc($res))
  {
$ratio = $CURUSER['downloaded'] > 0 ? $CURUSER['uploaded']/$CURUSER['downloaded'] : 0;
$ratio = number_format($ratio, 3);

$color = get_ratio_color($ratio);

if ($color)

$ratio = "<font color=$color>$ratio</font>";
	$catimage = safechar($arr["image"]);
	$catname = safechar($arr["catname"]);
	$size = str_replace(" ", "<br>", mksize($arr["size"]));
	$uploaded = str_replace(" ", "<br>", mksize($arr["uploaded"]));
	$downloaded = str_replace(" ", "<br>", mksize($arr["downloaded"]));
	$seeders = number_format($arr["seeders"]);
	$leechers = number_format($arr["leechers"]);
    $ret .= "<tr><td style='padding: 0px'><img src=\"pic/$catimage\" alt=\"$catname\" width=42 height=42></td>\n" .
		"<td><a href=details.php?id=$arr[torrent]&amp;hit=1><b>" . safechar($arr["torrentname"]) .
		"</b></a></td><td align=center>$size</td><td align=right>$seeders</td><td align=right>$leechers</td><td align=center>$uploaded</td>\n" .
		"<td align=center>$downloaded</td><td align=center>$ratio</td></tr>\n";
  }
  $ret .= "</table>\n";
  return $ret;
}
function usercommenttable($rows)
{
global $CURUSER, $pic_base_url, $userid;
begin_main_frame();
begin_frame();
$count = 0;
foreach ($rows as $row)
{
print("<p class=sub>#" . $row["id"] . " by ");
if (isset($row["username"]))
{
$title = $row["title"];
if ($title == "")
$title = get_user_class_name($row["class"]);
else
$title = htmlspecialchars($title);
print("<a name=comm". $row["id"] .
" href=userdetails.php?id=" . $row["user"] . "><b>" .
htmlspecialchars($row["username"]) . "</b></a>" . ($row["donor"] == "yes" ? "<img src=\"{$pic_base_url}star.gif\" alt='Donor'>" : "") . ($row["warned"] == "yes" ? "<img src=".
"\"{$pic_base_url}warned.gif\" alt=\"Warned\">" : "") . " ($title)\n");
}
else
print("<a name=\"comm" . $row["id"] . "\"><i>(orphaned)</i></a>\n");

print(" at " . $row["added"] . " GMT" .
($userid == $CURUSER["id"] || $row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "- [<a href=usercomment.php?action=edit&amp;cid=$row[id]>Edit</a>]" : "") .
($userid == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? "- [<a href=usercomment.php?action=delete&amp;cid=$row[id]>Delete</a>]" : "") .
($row["editedby"] && get_user_class() >= UC_MODERATOR ? "- [<a href=usercomment.php?action=vieworiginal&amp;cid=$row[id]>View original</a>]" : "") . "</p>\n");
$avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($row["avatar"]) : "");
$text = format_comment($row["text"]);
if ($row["editedby"])
$text .= "<p><font size=1 class=small>Last edited by <a href=userdetails.php?id=$row[editedby]><b>$row[username]</b></a> at $row[editedat] GMT</font></p>\n";
begin_table(true);
print("<tr valign=top>\n");
print("<td align=center width=150 style='padding: 0px'><img width=150 src=\"{$avatar}\"></td>\n");
print("<td class=text>$text</td>\n");
print("</tr>\n");
end_table();
}
end_frame();
end_main_frame();
}
$id = 0 + $_GET["id"];

if (!is_valid_id($id))
  bark("Bad ID $id.");
$res = sql_query("SELECT userid, port, agent FROM peers") or print(mysql_error());
if (mysql_num_rows($res) > 0)
{
while ($arr = mysql_fetch_assoc($res))
{
$agent = sqlesc($arr[agent]);
sql_query("UPDATE users SET port=$arr[port], agent=$agent WHERE id=$arr[userid]") or sqlerr(__FILE__, __LINE__);
}
}
$r = @sql_query("SELECT * FROM users WHERE id=$id") or sqlerr();
$user = mysql_fetch_assoc($r) or bark("No user with ID.");
if ($user["status"] == "pending") die;
$r = sql_query("SELECT id, name, seeders, leechers, category FROM torrents WHERE owner=$id ORDER BY name") or sqlerr();
if (mysql_num_rows($r) > 0)
{
  $torrents = "<table class=main border=1 cellspacing=0 cellpadding=5>\n" .
    "<tr><td class=colhead>Type</td><td class=colhead>Name</td><td class=colhead>Seeders</td><td class=colhead>Leechers</td></tr>\n";
  while ($a = mysql_fetch_assoc($r))
  {
		$r2 = sql_query("SELECT name, image FROM categories WHERE id=$a[category]") or sqlerr(__FILE__, __LINE__);
		$a2 = mysql_fetch_assoc($r2);
		$cat = "<img src=\"/pic/$a2[image]\" alt=\"$a2[name]\">";
      $torrents .= "<tr><td style='padding: 0px'>$cat</td><td><a href=details.php?id=" . $a["id"] . "&hit=1><b>" . safechar($a["name"]) . "</b></a></td>" .
        "<td align=right>$a[seeders]</td><td align=right>$a[leechers]</td></tr>\n";
  }
  $torrents .= "</table>";
}

if ($user["ip"] && (get_user_class() >= UC_MODERATOR || $user["id"] == $CURUSER["id"]))
{
  $ip = $user["ip"];
  $dom = @gethostbyaddr($user["ip"]);
  if ($dom == $user["ip"] || @gethostbyname($dom) != $user["ip"])
    $addr = $ip;
  else
  {
    $dom = strtoupper($dom);
    $domparts = explode(".", $dom);
    $domain = $domparts[count($domparts) - 2];
    if ($domain == "COM" || $domain == "CO" || $domain == "NET" || $domain == "NE" || $domain == "ORG" || $domain == "OR" )
      $l = 2;
    else
      $l = 1;
    $addr = "$ip ($dom)";
  }
}
  if ($user[added] == "0000-00-00 00:00:00")
  $joindate = 'N/A';
  else
  $joindate = "$user[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($user["added"])) . " ago)";
  $lastseen = $user["last_access"];
  if ($lastseen == "0000-00-00 00:00:00")
  $lastseen = "never";
  else
  {
  $lastseen .= " (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($lastseen)) . " ago)";
  }

  $res = sql_query("SELECT COUNT(*) FROM comments WHERE user=" . $user[id]) or sqlerr();
  $arr3 = mysql_fetch_row($res);
  $torrentcomments = $arr3[0];
  $res = sql_query("SELECT COUNT(*) FROM posts WHERE userid=" . $user[id]) or sqlerr();
  $arr3 = mysql_fetch_row($res);
  $forumposts = $arr3[0];

$country = '';
include 'include/cache/countries.php';
foreach ($countries as $country)
if ($country[id] == $user[country])
{
$country = "<td class=embedded><img src=\"{$pic_base_url}flag/{$country['flagpic']}\" alt=\"". safechar($country['name']) ."\" style='margin-left: 8pt'></td>";
break;
}
if ($user["chatpost"] == "no") $chatpost = "<td class=embedded><img src=".$pic_base_url."chatpos.gif alt='no chat' title=\"Chat Disabled\"/ style='margin-left: 4pt'></td>";
if ($user["downloadpos"] == "no") $downloadpos = "<td class=embedded><img src=".$pic_base_url."downloadpos.gif alt='no download' title=\"Download Disabled\"/ style='margin-left: 4pt'></td>";
if ($user["forumpost"] == "no") $forumpost = "<td class=embedded><img src=".$pic_base_url."forumpost.gif alt='no posting' title=\"Posting Disabled\"/ style='margin-left: 4pt'></td>";
if ($user["uploadpos"] == "no") $uploadpos = "<td class=embedded><img src=".$pic_base_url."uploadpos.gif alt='no uploads' title=\"Upload Disabled\"/ style='margin-left: 4pt'></td>";
if ($user["parked"] == "yes") $parked = "<td class=embedded><img src=".$pic_base_url."parked.gif alt='Account Parked' title=\"User Parked\"/ style='margin-left: 4pt'></td>";
if ($user["anonymous"] == "yes") $anonymous = "<td class=embedded><img src=".$pic_base_url."anonymous.gif alt='Anonymous User' title=\"User Anonymous\"/ style='margin-left: 4pt'></td>";
if ($user["gender"] == "Male") $gender = "<td class=embedded><img src=".$pic_base_url."male.gif alt='Male'  style='margin-left: 4pt'></td>";
if ($user["gender"] == "Female") $gender = "<td class=embedded><img src=".$pic_base_url."female.gif alt='Female' style='margin-left: 4pt'></td>";
if ($user["gender"] == "N/A") $gender = "<td class=embedded><img src=".$pic_base_url."na.gif alt='N/A' style='margin-left: 4pt'></td>";

$res = sql_query("SELECT torrent,added,uploaded,downloaded,torrents.name as torrentname,categories.name as catname,size,image,category,seeders,leechers FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id LEFT JOIN categories ON torrents.category = categories.id WHERE userid=$id AND seeder='no'") or sqlerr();
if (mysql_num_rows($res) > 0)
  $leeching = maketable($res);
$res = sql_query("SELECT torrent,added,uploaded,downloaded,torrents.name as torrentname,categories.name as catname,size,image,category,seeders,leechers FROM peers LEFT JOIN torrents ON peers.torrent = torrents.id LEFT JOIN categories ON torrents.category = categories.id WHERE userid=$id AND seeder='yes'") or sqlerr();
if (mysql_num_rows($res) > 0)
  $seeding = maketable($res);

stdhead("Details for " . $user["username"]);
$enabled = $user["enabled"] == 'yes';
print("<p><table class=main border=0 cellspacing=0 cellpadding=0>".
"<tr><td class=embedded><h1 style='margin:0px'>$user[username]" . get_user_icons($user, true) . "</h1></td>$donor$gender$parked$anonymous$chatpost$downloadpos$uploadpos$forumpost$warned$country</tr></table></p>\n");

if (!$enabled)
  print("<p><b>This account has been disabled</b></p>\n");
elseif ($CURUSER["id"] <> $user["id"])
{
print("<p>(<a href=".$DEFAULTBASEURL."/userfriends.php?id=$id>add comment</a>)");
$r = mysql_query("SELECT id, friendid FROM friends WHERE (userid=$CURUSER[id] OR userid=$id) AND (friendid=$id OR friendid=$CURUSER[id])") or sqlerr(__FILE__, __LINE__);
$friend = mysql_num_rows($r);
$r = mysql_query("SELECT id FROM blocks WHERE userid=$CURUSER[id] AND blockid=$id") or sqlerr(__FILE__, __LINE__);
$block = mysql_num_rows($r);

if ($friend)
print(" - (<a href=".$DEFAULTBASEURL."/friends.php?action=delete&type=friend&targetid=$id>remove from friends</a>)\n");
elseif($block)
print(" - (<a href=".$DEFAULTBASEURL."/friends.php?action=delete&type=block&targetid=$id>remove from blocks</a>)\n");
else
{
$rq = mysql_query("SELECT id, friendid FROM friends WHERE userid=$CURUSER[id] AND friendid=$id AND confirmed='no'") or sqlerr(__FILE__, __LINE__);
$con = mysql_num_rows($rq);
if ($con)
print(" - (<a href=".$DEFAULTBASEURL."/friends.php#friendreqs>friend is pending</a>)");
else
print(" - (<a href=".$DEFAULTBASEURL."/friends.php?action=add&type=friend&targetid=$id>add to friends</a>)");
print(" - (<a href=".$DEFAULTBASEURL."/friends.php?action=add&type=block&targetid=$id>add to blocks</a>)");
}
if ($user["showfriends"] == "yes")
print(" - (<a href=".$DEFAULTBASEURL."/userfriends.php?id=$id>view friends</a>)\n");
print("</p>\n");
}
if ($CURUSER['id'] != $user['id'])
print(" - (<a href=/sharemarks.php?id=$id>view sharemarks</a>)</p>\n");
 
if ($user["anonymous"] == 'yes' && $CURUSER['class'] < UC_VIP)
{
print("<table width=\"750\" border=1 cellspacing=0 cellpadding=5 class=main>");
print("<tr><td colspan=\"2\" align=\"center\">The users profile is protected, because his/her status is anonymous !</td></tr>");
if ($user["avatar"])
print("<tr><td class=rowhead>Avatar</td><td align=left><a href=\"" . safechar($user["avatar"]) . "\" rel='lightbox' title=\"" . safechar($user["username"]) . "\" class=\"borderimage\" onMouseover=\"borderit(this,'black')\" onMouseout=\"borderit(this,'silver')\"><img src=\"" . safechar($user["avatar"]) . "\" width=150 title=\"" . safechar($user["username"]) . "\"></a></td></tr>\n");
print("<tr><td class=rowhead>Class</td><td align=left><font color='#".get_user_class_color($user['class'])."'> ".get_user_class_name($user['class'])."  <img src=" . get_user_class_image($user["class"]) . " alt=" . get_user_class_name($user["class"]) . "> | ". safechar($user[title]) ."</td></tr>\n");
if ($user["info"])
print("<tr valign=top><td align=left colspan=2 class=text bgcolor=\"#777777\">" . format_comment($user["info"]) . "</td></tr>\n");
print("<tr><td colspan=2 align=center><form method=get action=sendmessage.php><input type=hidden name=receiver value=" .
$user["id"] . "><input type=submit value=\"Send message\" style='height: 23px'></form></td></tr>");
if (get_user_class() < UC_MODERATOR && $user["id"] != $CURUSER["id"])
{
print("</table>");
end_main_frame();
exit;
}
print("</table><br>");
}
//===donor count down
if ($user[donor] && $CURUSER[id] == $user[id] || get_user_class() >= UC_SYSOP)
    {
        $donoruntil = $user['donoruntil'];
        if ($donoruntil == '0000-00-00 00:00:00')
        print("");
        else
        {
        print("<b><p>Donated Status Until - $donoruntil");
        print(" [ " . mkprettytime(strtotime($donoruntil) - gmtime()) . " ] to go...</b><font size=\"-2\"> to re-new donation click <a class=altlink href=donate.php>here</a>.</font></p>\n");
      }
      }
//====end
////Get H&R Total ////
$hit_run_total = number_format($user["hit_run_total"]);
///////////////
if ($CURUSER['id'] == $user['id'])
     print('<h3><a href='.$DEFAULTBASEURL.'/my.php>Edit My Profile</a></h3>'.
'<h3><a href='.$DEFAULTBASEURL.'/friends.php#pending>Unconfirmed Friends</a></h3>'.
        '<h3><a href=\'view_announce_history.php\'>View My Announcements</a></h3>');
begin_main_frame();
?>
<table width=100% border=1 cellspacing=0 cellpadding=5>
<?php /* flush all torrents mod */
$un= $user["username"];
?>
<tr><td class=rowhead width=1%>Flush Torrents</td><td align=left width=99%><?print("<h0>Flush Torrents, <a href=flush.php?id=$id>$un</a>! Please note abuse will be flagged instantly - All flushes are logged !</h0>\n");?></td></tr>
<tr><td class=rowhead width=1%>Join&nbsp;date</td><td align=left width=99%><?=$joindate?></td></tr>
<tr><td class=rowhead>Last&nbsp;seen</td><td align=left><?=$lastseen?></td></tr>
<?
if ($user['port'] != 0) { ?>
<tr><td class=rowhead>Port</font></td><td class=tablea align=left><?=htmlentities($user['port'])?></td></tr>
<tr><td class=rowhead>Client</font></td><td class=tablea align=left><?=htmlentities($user['agent'])?></td></tr>
<? }
if (get_user_class() >= UC_MODERATOR)
  print("<tr><td class=rowhead>Email</td><td align=left><a href=mailto:$user[email]>$user[email]</a></td></tr>\n");
if ($addr)
  print("<tr><td class=rowhead>Address</td><td align=left>$addr</td></tr>\n");
if (get_user_class() >= UC_MODERATOR) {
    $resip = sql_query("SELECT ip FROM iplog WHERE userid =$id GROUP BY ip") or sqlerr(__FILE__, __LINE__);
    $iphistory = mysql_num_rows($resip);

    if ($iphistory > 0)
        print("<tr><td class=rowhead>IP History</td><td align=left>This user has earlier used <b><a href=iphistory.php?id=" . $user['id'] . ">" . $iphistory. " different IP addresses</a></b></td></tr>\n");
}
if ($user["id"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR)
	{
$bonuslog = explode(" ", $user["bonuscomment"]);
$total = 0;
for($i=0; $i<=count($bonuslog); $i++){
//print $bonuslog[$i];

if($bonuslog[$i] == "upload"){
//if you found something about "uploading"
// then go back a couple of steps and get their amount
// from position of the array where you found something about "upload"
// to X-3 steps in the array.
$points = $bonuslog[$i-3];
// now i have all the figures now time to do some maths biggrin.gif
// These figures might be different on your tracker
// On my tracker they are something like this:
// 150 points = 1 gig
// 250 points = 2 gigs
// 500 points = 5 gigs
//Note: I should really be getting these values of database!
// So if i change those these work accordingly! @TODO
if($points == "150"){
$total = $total + 1;
}
if($points == "250") {
$total = $total + 2;
}
if($points == "500") {
$total = $total + 5;
}
}
// This will take care of how much love they shared with other users
//i.e. gave away their karma
//TODO later just an idea for others to work on
if($bonuslog[$i] == "gift"){
$love = $bonuslog[$i-3];
//echo "<pre>Love= " . $love . "\n</pre>";
}
//=== karma for gifts
if($bonuslog[$i] == 'to'){
$love = $bonuslog[$i-4];
switch ($love) {
case 100:
$gift = $gift + 100;
break;
case 200:
$gift = $gift + 200;
break;
case 300:
$gift = $gift + 300;
break;
case 400:
$gift = $gift + 400;
break;
case 500:
$gift = $gift + 500;
break;
case 1000:
$gift = $gift + 1000;
break;
}
}
//=== karma recieved gifts
if($bonuslog[$i] == 'from'){
$got_love = $bonuslog[$i-4];
switch ($got_love) {
case 100:
$got_gift = $got_gift + 100;
break;
case 200:
$got_gift = $got_gift + 200;
break;
case 300:
$got_gift = $got_gift + 300;
break;
case 400:
$got_gift = $got_gift + 400;
break;
case 500:
$got_gift = $got_gift + 500;
break;
case 1000:
$got_gift = $got_gift + 1000;
break;
}
}
}
//convert it to bytes so we can fuck with ratio
$total = 1073741824 * $total;
$pureupload = mksize($user["uploaded"] - $total);
$realratio = $CURUSER['downloaded'] > 0 ? $CURUSER['uploaded']/$CURUSER['downloaded'] : 0;
$realratio = number_format($realratio, 3);
$dayUpload   = $user["uploaded"];
$dayDownload = $user["downloaded"];
$seconds = mkprettytime(strtotime("now") - strtotime($user["added"]));
$days = explode("d ", $seconds);
if(sizeof($days) > 1) {
$dayUpload   = $user["uploaded"] / $days[0];
$dayDownload = $user["downloaded"] / $days[0];
}
?>
<tr><td class=rowhead>Uploaded</td><td align=left><?=mksize($user["uploaded"])?>&nbsp;<b>[</b>Daily: <?=mksize($dayUpload)?><b>]</b></td></tr><tr><td class=rowhead>Downloaded</td><td align=left><?=mksize($user["downloaded"])?>&nbsp;<b>[</b>Daily: <?=mksize($dayDownload)?><b>]</b></td></tr><tr><td class=rowhead>Karma Upload</td><td align=left><?=mksize($total)?></td></tr><tr><td class=rowhead>Pure Uploaded</td><td align=left><?=$pureupload?></td></td><tr><td class=rowhead>Real ratio </td><td align=left><?=$realratio?></td></tr><tr><td class=rowhead>Total Hit And Runs</td><td align=left><?=$hit_run_total?></td></tr><?
if ($user["downloaded"] > 0)
{
  $sr = $user["uploaded"] / $user["downloaded"];
  if ($sr >= 4)
    $s = "w00t";
  else if ($sr >= 2)
    $s = "grin";
  else if ($sr >= 1)
    $s = "smile1";
  else if ($sr >= 0.5)
    $s = "noexpression";
  else if ($sr >= 0.25)
    $s = "sad";
  else
    $s = "cry";
  $sr = floor($sr * 1000) / 1000;
  $sr = "<table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><font color=" . get_ratio_color($sr) . ">" . number_format($sr, 3) . "</font></td><td class=embedded>&nbsp;&nbsp;<img src=/pic/smilies/$s.gif></td></tr></table>";
print("<tr><td class=rowhead style='vertical-align: middle'>Share ratio</td><td align=left valign=center style='padding-top: 1px; padding-bottom: 0px'>$sr</td></tr>\n");
}
}
$res5 = sql_query("SELECT connectable FROM peers WHERE userid=$user[id]")or sqlerr(__FILE__, __LINE__);
if($row = mysql_fetch_row($res5)){
$connect = $row[0];
if($connect == "yes"){
$connectable = "<a title='well done good connection'><b><font color=green>Sorted - Your Port Is Open</a></font></b>";
}else{
$connectable = "<b><a title='need to fix this goto irc for help'><font color=red>No - Your Unconnectable Contact Site Admin</a></font></b>";
}
}else{
$connectable ="<b><a title='Unknown connection still'><font color=blue>Waiting</font></a></b>";
}
?><tr><td class=rowhead>connectable</font></td><td align=left><?=$connectable?></td></tr>
<?
//=== Karma bonus points
print("<tr><td align=left><b>Karma Points</b></td><td colspan=2 align=left>" . safechar($user[seedbonus]) . "</tr>\n");
//===end
print("<tr><td class=rowhead>Freeleech Slots</td><td align=left>" . safechar($user['freeslots']) . "</td></tr>\n");
if ($user["avatar"])
print("<tr><td class=rowhead>Avatar</td><td align=left><a href=\"" . htmlspecialchars($user["avatar"]) . "\" rel='lightbox' title=\"" . htmlspecialchars($user["username"]) . "\" class=\"borderimage\" onMouseover=\"borderit(this,'black')\" onMouseout=\"borderit(this,'silver')\"><img src=\"" . htmlspecialchars($user["avatar"]) . "\" width=150 title=\"" . htmlspecialchars($user["username"]) . "\"></a></td></tr>\n");
if ($user["signature"])
print("<tr><td class=rowhead>Signature</td><td align=left>". format_comment($user["signature"]) ."</td></tr>\n");
if ($user[title])
print("<tr><td class=rowhead>Class</td><td align=left><font color='#".get_user_class_color($user['class'])."'> ".get_user_class_name($user['class'])."  <img src=" . get_user_class_image($user["class"]) . " alt=" . get_user_class_name($user["class"]) . "> | ". safechar($user[title]) ."</td></tr>\n");
else
print("<tr><td class=rowhead>Class</td><td align=left><font color='#".get_user_class_color($user['class'])."'> ".get_user_class_name($user['class'])." <img src=" . get_user_class_image($user["class"]) . " alt=" . get_user_class_name($user["class"]) . "></td></tr>\n");
if ($user["showfriends"] == "yes" || $CURUSER["id"] == $user["id"] || $friend)
{
$fcount = number_format(get_row_count("friends", "WHERE userid='".$id."' AND confirmed = 'yes'"));
if ($fcount >= 1)
{
$fr = mysql_query("SELECT f.friendid as id, u.username AS name FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$id AND f.confirmed='yes' ORDER BY name LIMIT 100") or sqlerr(__FILE__, __LINE__);

    while ($friend = mysql_fetch_array($fr))
    {
  
  $frnd = (isset($frnd))."<a href=".$DEFAULTBASEURL."/userdetails.php?id=" . $friend['id'] . ">" . $friend['name'] . "</a>, ";
}
         tr("Friends","<a href=".$DEFAULTBASEURL."/userfriends.php?id=$id>".$fcount." Friends</a> - ".$frnd,1);                    
}

if ($CURUSER['comments'])
tr("Comments","<a href=".$DEFAULTBASEURL."/userfriends.php?id=$id>".$user['username']." has ".$CURUSER['comments']." Comments</a>",1);

}
print("<tr><td class=rowhead>Torrent&nbsp;comments</td>");
if ($torrentcomments && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || get_user_class() >= UC_MODERATOR))
{
$comments =sql_query("SELECT sum(comments) AS comments FROM torrents");
$seconds = round(($comments)/($torrentcomments), 3);
print("<td align=left><a href=userhistory.php?action=viewcomments&id=$user[id]>$torrentcomments</a> ($seconds% of total comments)</td></tr>\n");
}
else
    print("<td align=left>$torrentcomments</td></tr>\n");
print("<tr><td class=rowhead>Forum&nbsp;posts</td>");
if ($forumposts && (($user["class"] >= UC_USER && $user["id"] == $CURUSER["id"]) || get_user_class() >= UC_MODERATOR))
{
$postcount = sql_query("SELECT sum(postcount) AS postcount FROM forums");
$seconds = round(($postcount)/($forumposts), 3);
print("<td align=left><a href=userhistory.php?action=viewposts&id=$user[id]>$forumposts</a> ($seconds% of total posts)</td></tr>\n");
}
else
    print("<td align=left>$forumposts</td></tr>\n");
if (get_user_class() >= UC_MODERATOR && $user[invites] > 0 || $user["id"] == $CURUSER["id"] && $user[invites] > 0)
print("<tr><td class=rowhead>Invites</td><td align=left><a href=invite.php>$user[invites]</a></td></tr>\n");
if (get_user_class() >= UC_MODERATOR && $user[invited_by] > 0 || $user["id"] == $CURUSER["id"] && $user[invited_by] > 0)
{
$invited_by = sql_query("SELECT username FROM users WHERE id=$user[invited_by]");
$invited_by2 = mysql_fetch_array($invited_by);
print("<tr><td class=rowhead>Invited by</td><td align=left><a href=userdetails.php?id=$user[invited_by]>$invited_by2[username]</a></td></tr>\n");
}
if (get_user_class() >= UC_MODERATOR && $user[invitees] > 0 || $user["id"] == $CURUSER["id"] && $user[invitees] > 0)
{
$compl = $user["invitees"];
$compl_list = explode(" ", $compl);
$arr = array();

foreach($compl_list as $array_list)
$arr[] = $array_list;

$compl_arr = array_reverse($arr, TRUE);
$f=0;
foreach($compl_arr as $user_id)
{

$compl_user = sql_query("SELECT id, username FROM users WHERE id='$user_id' and status='confirmed'");
$compl_users = mysql_fetch_array($compl_user);

if ($compl_users["id"] > 0)
{
echo("<tr><td class=rowhead width=1%>Invitees</td><td>");

$compl = $user["invitees"];
$compl_list = explode(" ", $compl);
$arr = array();

foreach($compl_list as $array_list)
$arr[] = $array_list;

$compl_arr = array_reverse($arr, TRUE);

$i = 0;
foreach($compl_arr as $user_id)
{

$compl_user = sql_query("SELECT id, username FROM users WHERE id='$user_id' and status='confirmed' ORDER BY username");
$compl_users = mysql_fetch_array($compl_user);
echo("<a href=userdetails.php?id=" . $compl_users["id"] . ">" . $compl_users["username"] . "</a>&nbsp;");

if ($i == "9")
break;
$i++;
}
echo ("</td></tr>");
$f = 1;
}
if ($f == "1")
break;
}
}

if ($torrents)
echo"<tr valign=top><td class=rowhead>Uploaded Torrents<a href=\"javascript: klappe_news('a1')\"><br><img border=\"0\" src=\"pic/show.gif\" id=\"pica".$array['id']."\" alt=\"[Hide/Show]\"></td><td align=left><div id=\"ka1\" style=\"display: none;\">$torrents</div></td></tr>";
if ($seeding)
echo"<tr valign=top><td class=rowhead>Seeding torrents<a href=\"javascript: klappe_news('a2')\"><br><img border=\"0\" src=\"pic/show.gif\" id=\"pica".$array['id']."\" alt=\"[Hide/Show]\"></td><td align=left><div id=\"ka2\" style=\"display: none;\">$seeding</div></td></tr>";
if ($leeching)
echo"<tr valign=top><td class=rowhead>Leeching torrents<a href=\"javascript: klappe_news('a3')\"><br><img border=\"0\" src=\"pic/show.gif\" id=\"pica".$array['id']."\" alt=\"[Hide/Show]\"></td><td align=left><div id=\"ka3\" style=\"display: none;\">$leeching</div></td></tr>";
$res = sql_query("SELECT s.*, t.name AS name, c.name AS catname, c.image AS catimg FROM snatched AS s INNER JOIN torrents AS t ON s.torrentid = t.id LEFT JOIN categories AS c ON t.category = c.id WHERE s.userid = $user[id]") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
  $snatches = snatchtable($res);
if ($snatches)
echo"<tr valign=top><td class=rowhead>Recently Snatched<a href=\"javascript: klappe_news('a4')\"><br><img border=\"0\" src=\"pic/show.gif\" id=\"pica".$array['id']."\" alt=\"[Hide/Show]\"></td><td align=left><div id=\"ka4\" style=\"display: none;\">$snatches</div></td></tr>";
//=== start snatched
if (get_user_class() >= UC_MODERATOR){
if ($_GET["snatched_table"]){
echo "<tr><td class=clearalt6 align=right valign=top><b>Snatched stuff:</b><br>[ <a class=altlink href=\"userdetails.php?id=$id\" class=\"sublink\">Hide list</a> ]</td><td class=clearalt6>";
$res = mysql_query(
"SELECT UNIX_TIMESTAMP(sn.start_date) AS s, UNIX_TIMESTAMP(sn.complete_date) AS c, UNIX_TIMESTAMP(sn.last_action) AS l_a, UNIX_TIMESTAMP(sn.seedtime) AS s_t, sn.seedtime, UNIX_TIMESTAMP(sn.leechtime) AS l_t, sn.leechtime, sn.downspeed, sn.upspeed, sn.uploaded, sn.downloaded, sn.torrentid, sn.tamount, sn.start_date, sn.complete_date, sn.seeder, sn.last_action, sn.connectable, sn.agent, sn.seedtime, sn.port, cat.name, cat.image, t.size, t.seeders, t.leechers, t.owner, t.name AS torrent_name ".
"FROM snatched AS sn ".
"LEFT JOIN torrents AS t ON t.id = sn.torrentid ".
"LEFT JOIN categories AS cat ON cat.id = t.category ".
"WHERE sn.userid=$id ORDER BY sn.start_date DESC"
) or die(mysql_error());
echo "<table border=1 cellspacing=0 cellpadding=5 align=center><tr><td class=colhead2 align=center>Category</td><td class=colhead2 align=left>Torrent</td>".
"<td class=colhead2 align=center>S / L</td><td class=colhead2 align=center>Up / Down</td><td class=colhead2 align=center>Torrent Size</td>".
"<td class=colhead2 align=center>Ratio</td><td class=colhead2 align=center>Client</td><td class=colhead2 align=center>Total Snatched</td></tr>";
while ($arr = mysql_fetch_assoc($res)){
//=======change colors
$count2= (++$count2)%2;
$class = 'clearalt'.($count2==0?'6':'7');
//=== speed color red fast green slow ;)
if ($arr["upspeed"] > 0)
$ul_speed = ($arr["upspeed"] > 0 ? mksize($arr["upspeed"]) : ($arr["seedtime"] > 0 ? mksize($arr["uploaded"] / ($arr["seedtime"] + $arr["leechtime"])) : mksize(0)));
else
$ul_speed = mksize(($arr["uploaded"] / ( $arr['l_a'] - $arr['s'] + 1 )));
if ($arr["downspeed"] > 0)
$dl_speed = ($arr["downspeed"] > 0 ? mksize($arr["downspeed"]) : ($arr["leechtime"] > 0 ? mksize($arr["downloaded"] / $arr["leechtime"]) : mksize(0)));
else
$dl_speed = mksize(($arr["downloaded"] / ( $arr['c'] - $arr['s'] + 1 )));
switch (true){
case ($dl_speed > 600):
$dlc = 'red';
break;
case ($dl_speed > 300 ):
$dlc = 'orange';
break;
case ($dl_speed > 200 ):
$dlc = 'yellow';
break;
case ($dl_speed < 100 ):
$dlc = 'Chartreuse';
break;
}
if ($arr["downloaded"] > 0){
$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
$ratio = "<font color=" . get_ratio_color($ratio) . "><b>Ratio:</b><br>$ratio</font>";
}
else
if ($arr["uploaded"] > 0)
$ratio = "Inf.";
else
$ratio = "N/A";
/// smallname seeding/leeching torrents
$smallname2 =substr(safechar($arr["torrent_name"]) , 0, 30);
if ($smallname2 != safechar($arr["torrent_name"])) {
$smallname2 .= '...';
}
echo "<tr><td class=$class align=center>".($arr['owner'] == $id ? "<b><font color=orange>torrent owner</font></b><br>" : "".($arr['complete_date'] != '0000-00-00 00:00:00' ? "<b><font color=lightgreen>Finished</font></b><br>" : "<b><font color=red>Not Finished</font><br>")."")."<img src=pic/$arr[image] alt=$arr[name]></td>".
"<td class=$class><a class=altlink href=details.php?id=$arr[torrentid]><b>$smallname2
</b></a> ".($arr['complete_date'] != '0000-00-00 00:00:00' ? "<br>".
"<font color=yellow>started: ".$arr['start_date']."</font><br><font color=pink>finished: ".$arr['complete_date']."</font>" : "".
"<br><font color=yellow>started: ".$arr['start_date']."</font><br><font color=orange>Last Action: ".$arr['last_action']."</font> ".
"".($arr['complete_date'] == '0000-00-00 00:00:00' ? "".($arr['owner'] == $id ? "" : "[ ".mksize($arr["size"] - $arr["downloaded"])." still to go ]")."" : "")."")."".($arr['complete_date'] != '0000-00-00 00:00:00' ? "<br>".
"<font color=silver>time to download: ".($arr['leechtime'] != '0' ? mkprettytime($arr['leechtime']) : mkprettytime($arr['c'] - $arr['s'])."")."</font> <font color=$dlc>[ DLed at: $dl_speed ]<font>".
"<br>" : "<br>")."<font color=lightblue>".($arr['seedtime'] != '0' ? "total seeding time: ".mkprettytime($arr['seedtime'])." <font color=$dlc> " : "total seeding time: N/A")."".
"</font><font color=lightgreen> [ up speed: ".$ul_speed." ] </font>".($arr['complete_date'] == '0000-00-00 00:00:00' ? "<br><font color=$dlc>Download speed: $dl_speed</font>" : "")."</td>".
"<td align=center class=$class>Seeds: ".$arr['seeders']."<br>Leech: ".$arr['leechers']."</td><td align=center class=$class><font color=lightgreen>Uploaded:<br>".
"<b>".$uploaded =mksize($arr["uploaded"])."</b></font><br><font color=orange>Downloaded:<br><b>".$downloaded = mksize($arr["downloaded"])."</b></font></td>".
"<td align=center class=$class>".mksize($arr["size"])."<br>difference of:<br><font color=orange><b>".mksize($arr['size'] - $arr["downloaded"])."</b></font></td>".
"<td align=center class=$class>$ratio<br>".($arr['seeder'] == 'yes' ? "<font color=lightgreen><b>seeding</b></font>" : "<font color=red><b>not seeding</b></font>")."".
"</td><td align=center class=$class>".$arr["agent"]."<br><td align=center class=$class>".$arr["tamount"]."<br>port: ".$arr["port"]."<br>".($arr["connectable"] == 'yes' ? "<b>connectable: <font color=lightgreen>yes</font>".
"</b>" : "<b>connectable: <font color=red><b>no</b></font>")."</td></tr>\n";
}
echo "</table></td></tr>\n";
}
else
tr("Snatched stuff:<br>","[ <a class=altlink href=\"userdetails.php?id=$id&snatched_table=1\" class=\"sublink\">Show</a> ]  - $count_snatched <font color=red><b>staff only!!!</font></b>", 1);
}
//=== end snatched 
if ($user["info"])
 print("<tr valign=top><td align=left colspan=2 class=text bgcolor=#777777>" . format_comment($user["info"]) . "</td></tr>\n");
tr("Report User:", "<form action=report.php?type=User&id=$id method=post><input class=button type=submit name=submit value=\"Report User\"> Click to Report this user for Breaking the rules.</form>", 1);
if ($CURUSER["id"] != $user["id"])
	if (get_user_class() >= UC_MODERATOR)
  	$showpmbutton = 1;
	elseif ($user["acceptpms"] == "yes")
	{
		$r = sql_query("SELECT id FROM blocks WHERE userid=$user[id] AND blockid=$CURUSER[id]") or sqlerr(__FILE__,__LINE__);
		$showpmbutton = (mysql_num_rows($r) == 1 ? 0 : 1);
	}
	elseif ($user["acceptpms"] == "friends")
	{
		$r = sql_query("SELECT id FROM friends WHERE userid=$user[id] AND friendid=$CURUSER[id]") or sqlerr(__FILE__,__LINE__);
		$showpmbutton = (mysql_num_rows($r) == 1 ? 1 : 0);
	}
if ($showpmbutton)
	print("<tr><td colspan=2 align=center><form method=get action=sendmessage.php><input type=hidden name=receiver value=" .
		$user["id"] . "><input type=submit value=\"Send message\" style='height: 23px'></form></td></tr>");

print("</table>\n");

if (get_user_class() >= UC_MODERATOR && $user["class"] < get_user_class())
{
  begin_frame("Edit User", true);
  print("<form method=post action=modtask.php>\n");
  print("<input type=hidden name='action' value='edituser'>\n");
  print("<input type=hidden name='userid' value='$id'>\n");
  print("<input type=hidden name='returnto' value='userdetails.php?id=$id'>\n");
  print("<table class=main border=1 cellspacing=0 cellpadding=5>\n");
  print("<tr><td class=rowhead>Title</td><td colspan=2 align=left><input type=text size=60 name=title value=\"" . safechar($user[title]) . "\"></tr>\n");
  $avatar = safechar($user["avatar"]);
  print("<tr><td class=rowhead>Avatar&nbsp;URL</td><td colspan=2 align=left><input type=text size=60 name=avatar value=\"$avatar\"></tr>\n");
  $signature = safechar($user["signature"]);
  print("<tr><td class=rowhead>Signature&nbsp;URL</td><td colspan=2 align=left><input type=text size=60 name=signature value=\"$signature\"></tr>\n");
  // we do not want mods to be able to change user classes or amount donated...
  if ($CURUSER["class"] >= UC_ADMINISTRATOR)
  print("<tr><td class=rowhead>Donor</td><td colspan=2 align=left><input type=radio name=donor value=yes" .($user["donor"] == "yes" ? " checked" : "").">Yes <input type=radio name=donor value=no" .($user["donor"] == "no" ? " checked" : "").">No</td></tr>\n");
   //=== donor mod time based by snuggles
   if ($CURUSER["class"] == UC_CODER) {
   $donor = $user["donor"] == "yes";
   print("<tr><td class=clearalt6 align=right><b>Donor:</b></td><td colspan=2 align=left class=clearalt6>");

   if ($donor)
   {
   $donoruntil = $user['donoruntil'];
   if ($donoruntil == '0000-00-00 00:00:00')
   print("arbitrary duration");
   else
   {
   print("<b>Donated Status Until</b> $donoruntil");
   print(" [ " . mkprettytime(strtotime($donoruntil) - gmtime()) . " ] to go\n");
   }
   }
   else
   {
   print("Donor for <select name=donorlength><option value=0>------</option><option value=4>1 month</option>".
   "<option value=6>6 weeks</option><option value=8>2 months</option><option value=10>10 weeks</option>".
   "<option value=12>3 months</option><option value=255>Unlimited</option></select>\n");
   }
   print("<br /><b>Current Donation:</b> <input type=text size=6 name=donated value=\"" . safechar($user[donated]) . "\">".
   "<b>Total Donations:</b> £" . safechar($user[total_donated]) . "");
   if ($donor){
   print("<br><b>Add to donor time:</b> <select name=donorlengthadd><option value=0>------</option><option value=4>1 month</option>".
   "<option value=6>6 weeks</option><option value=8>2 months</option><option value=10>10 weeks</option>".
   "<option value=12>3 months</option><option value=255>Unlimited</option></select>\n");
   print("<br><b>Remove Donor Status:</b> <input name=donor value=no type=checkbox> [if they were bad ]");
   }
   print("</td></tr>\n");
   }
   //====end
	if (get_user_class() == UC_MODERATOR && $user["class"] > UC_VIP)
	  printf("<input type=hidden name=class value=$user[class]\n");
	else
	{
	  print("<tr><td class=rowhead>Class</td><td colspan=2 align=left><select name=class>\n");
	  if (get_user_class() == UC_MODERATOR)
	    $maxclass = UC_VIP;
	  else
	    $maxclass = get_user_class() - 1;
	  for ($i = 0; $i <= $maxclass; ++$i)
	    print("<option value=$i" . ($user["class"] == $i ? " selected" : "") . ">$prefix" . get_user_class_name($i) . "\n");
	  print("</select></td></tr>\n");
	}
    //===Fls mod
    $supportfor = safechar($user["supportfor"]);
    print("<tr><td class=rowhead>Support</td><td colspan=2 align=left><input type=radio name=support value=yes" .($user["support"] == "yes" ? " checked" : "").">Yes <input type=radio name=support value=no" .($user["support"] == "no" ? " checked" : "").">No</td></tr>\n");
    print("<tr><td class=rowhead>Support for:</td><td colspan=2 align=left><textarea cols=60 rows=6 name=supportfor>$supportfor</textarea></td></tr>\n");
    //=== bonus comment      
    $bonuscomment = safechar($user["bonuscomment"]);
    print("<tr><td class=clearalt6 align=right><b>Seeding Karma:</b></td><td colspan=2 align=left class=clearalt6><textarea cols=60 rows=10 name=modcomment READONLY style=\"background: purple; color: yellow;\">$bonuscomment</textarea></td></tr>\n");
    //==end
	//=== bonus comment      
    $bonuscomment = safechar($user["bonuscomment"]);
    print("<tr><td class=clearalt6 align=right><b>Seeding Karma:</b></td><td colspan=2 align=left class=clearalt6><textarea cols=60 rows=10 name=bonuscomment READONLY style=\"background-color:silver\">$bonuscomment</textarea></td></tr>\n");
    //==end
    //=== Karma bonus
    if (get_user_class() >= UC_SYSOP)
    print("<tr><td align=right><b>Karma Bonus:</b></td><td colspan=2 align=left><input type=text size=5 name=seedbonus value=\"" . safechar($user[seedbonus]) . "\"></tr>\n");
    
    ///////////Freeslots by pdq
    if (get_user_class() >= UC_ADMINISTRATOR)
    print("<tr><td class=rowhead>Freeleech Slots:</td><td colspan=2 align=left> <input type=text size=6 name=freeslots value=\"" . safechar($user[freeslots]) . "\"></td></tr>\n");
		$modcomment = safechar($user["modcomment"]);

        if (get_user_class() < UC_SYSOP) {
        print("<tr><td class=rowhead>Comment</td><td colspan=2 align=left><textarea cols=60 rows=6 name=modcomment READONLY>$modcomment</textarea></td></tr>\n");
        }
        else {
        print("<tr><td class=rowhead>Comment</td><td colspan=2 align=left><textarea cols=60 rows=6 name=modcomment >$modcomment</textarea></td></tr>\n");
        }
        print("<tr><td class=rowhead>Add&nbsp;Comment</td><td colspan=2 align=left><textarea cols=60 rows=2 name=addcomment ></textarea></td></tr>\n");
	    ////////////Modified fedepecco's auto-leech warn system 	    
        $warned = $user["warned"] == "yes";
        print("<tr><td class=rowhead" . (!$warned ? " rowspan=4" : " rowspan=2") . ">Warning<br>System<br><br><font size=1><i>(Bad behavior)</i></font></td><td align=left width=20% class=\"row1\">" . ( $warned ? "<input name=warned value='yes' type=radio checked>Yes<input name=warned value='no' type=radio>No" : "Not warned." ) ."</td>");
        if ($warned)
        {
        $warneduntil = $user['warneduntil'];
        if ($warneduntil == '0000-00-00 00:00:00')
        print("<td align=center class=\"row1\">(Arbitrary duration)</td></tr>\n");
        else
        {
        print("<td align=left class=\"row1\">Until $warneduntil");
        print("<br>(" . mkprettytime(strtotime($warneduntil) - gmtime()) . " to go)</td></tr>\n");
        }
        }else{
        print("<td class=\"row1\">Warn for <select name=warnlength>\n");
        print("<option value=0>------</option>\n");
        print("<option value=1>1 week</option>\n");
        print("<option value=2>2 weeks</option>\n");
        print("<option value=4>4 weeks</option>\n");
        print("<option value=8>8 weeks</option>\n");
        print("<option value=255>Unlimited</option>\n");
        print("</select></td></tr>\n");
        print("<tr><td align=left class=\"row1\">Reason of warning:</td><td class=\"row1\"><input type=text size=60 name=warnpm></td></tr>");
        }      
        //Times warned and Last warning
        $elapsedlw = get_elapsed_time(sql_timestamp_to_unix_timestamp($user["lastwarned"]));
        print("<tr><td class=\"row1\">Times Warned</td><td align=left class=\"row1\">$user[timeswarned]</td></tr>\n");
        if ($user["timeswarned"] == 0)
        {
        print("<tr><td class=\"row1\">Last Warning</td><td align=left class=\"row1\">This user hasn't been warned yet.</td></tr>\n");
        }else{
        if ($user["warnedby"] != "System")
        {
        $res = sql_query("SELECT id, username, warnedby FROM users WHERE id = " . $user['warnedby'] . "") or sqlerr(__FILE__,__LINE__);
        $arr = mysql_fetch_assoc($res);
        $warnedby = "<br>[by <u><a href=userdetails.php?id=".$arr['id'].">".$arr['username']."</u></a>]";
        }else{
        $warnedby = "<br>[by System]";
        print("<tr><td class=\"row1\">Last Warning</td><td align=left class=\"row1\"$user[lastwarned] (until $elapsedlw)   $warnedby</td></tr>\n");
        }
        }
        //LeechWarning (Low Ratio)  
        $leechwarn = $user["leechwarn"] == "yes";
        print("<tr><td class=rowhead>Auto-Warning<br><font size=1><i>(Low Ratio)</i></font></td>");
        if ($leechwarn)
        {
        print("<td align=left class=\"row1\"><font color=red>¡ Warned !</font></td>\n");
        $leechwarnuntil = $user['leechwarnuntil'];
        if ($leechwarnuntil != '0000-00-00 00:00:00')
        {
        print("<td align=left class=\"row1\">Until $leechwarnuntil");
        print("<br>(" . mkprettytime(strtotime($leechwarnuntil) - gmtime()) . " to go)</td></tr>\n");
        }else{
        print("<td align=left class=\"row1\"><i>Full warning no duration...</i></td></tr>\n");
        }
        }else{
        print("<td class=\"row1\" colspan=\"2\">Not warned.</td></tr>\n");
        }
        //End//////////////////////////////////////////////////////////////////   
        ////////////webseeder
        print("<tr><td class=rowhead>Webseeder?</td><td colspan=2 align=left><input type=radio name=webseeder value=yes" .($user["webseeder"]=="yes" ? " checked" : "") . ">Yes <input type=radio name=webseeder value=no" .($user["webseeder"]=="no" ? " checked" : "") . ">No<br>This is needed, if a peer has a seedbox it will show highspeed torrent image on browse once you set it!</td></tr>\n");
        /////////////////////// 
    ///////////////disable downloads with duration
    if (get_user_class() >= UC_ADMINISTRATOR)
    {
    $downloadpos = $user["downloadpos"] == "no";
    print("<tr><td class=rowhead>Disable Downloads ?</td><td colspan=2 align=left width=20><input type=radio name=downloadpos value=yes" .($user["downloadpos"]=="no" ? " checked" : "") . ">Yes <input type=radio name=downloadpos value=yes" .($user["downloadpos"]=="yes" ? " checked" : "") . ">No</td></tr>\n");
    if ($downloadpos)
    {
    $disableuntil = $user['disableuntil'];
    if ($disableuntil == '0000-00-00 00:00:00')
    print("<td align=center>(arbitrary duration)</td></tr>\n");
    else
    {
    print("<td align=center>Until $disableuntil");
    print(" (" . mkprettytime(strtotime($disableuntil) - gmtime()) . " to go)</td></tr>\n");
    }
    }
    else
    {
    print("<td>Disable for <select name=disablelength>\n");
    print("<option value=0>------</option>\n");
    print("<option value=1>1 week</option>\n");
    print("<option value=2>2 weeks</option>\n");
    print("<option value=4>4 weeks</option>\n");
    print("<option value=8>8 weeks</option>\n");
    print("<option value=255>Unlimited</option>\n");
    print("</select></td></tr>\n");
    print("<tr><td align=left class=\"row1\">Reason:</td><td class=\"row1\"><input type=text size=60 name=disablepm></td></tr>");
    }
    }
    ///////////end download disable////////////////////
  /////Admin Tools////////////
  echo"<tr><td align=right class=clearalt6><b>Invite Rights:</b></td><td colspan=2 align=left class=clearalt6><input type=radio name=invite_on value=yes" .($user["invite_on"]=="yes" ? " checked" : "") . ">Yes <input type=radio name=invite_on value=no" .($user["invite_on"]=="no" ? " checked" : "") . ">No</td></tr>\n";
  print("<tr><td class=clearalt6 align=right><b>Invites:</b></td><td colspan=2 align=left class=clearalt6><input type=text size=3 name=invites value=\"" . safechar($user[invites]) . "\"></tr>\n");
  echo'<tr><td class=rowhead>Love Sent:</td><td align=left><b>'.($gift > 0 ? "<font color=yellow>$gift</font>" : '<font color=red>0</font>').'</b> points given as karma gifts</td></tr>'.
  '<tr><td class=rowhead>Love Recieved:</td><td align=left><b>'.($got_gift > 0 ? "<font color=yellow>$got_gift</font>" : '<font color=red>0</font>').'</b> points recieved as karma gifts</td></tr>';
  print("<tr><td class=rowhead>Enabled?</td><td colspan=2 align=left><input name=enabled value='yes' type=radio" . ($enabled ? " checked" : "") . ">Yes <input name=enabled value='no' type=radio" . (!$enabled ? " checked" : "") . ">No</td></tr>\n");
  print("<tr><td class=rowhead>Reset Passkey</td><td colspan=2 align=left><input name=resetkey value=1 type=checkbox> Reset passkey</td></tr>\n");
  print("<tr><td class=rowhead>Chat?</td><td colspan=2 align=left><input type=radio name=chatpost value=yes" .($user["chatpost"] === "yes" ? " checked" : "").">Yes <input type=radio name=chatpost value=no" .($user["chatpost"] === "no" ? " checked" : "").">No</td></tr>\n");
  print("<tr><td class=rowhead>Forum Post possible?</td><td colspan=2 align=left><input type=radio name=forumpost value=yes" .($user["forumpost"]=="yes" ? " checked" : "") . ">Yes <input type=radio name=forumpost value=no" .($user["forumpost"]=="no" ? " checked" : "") . ">No</td></tr>\n");
  print("<tr><td class=rowhead>Upload possible?</td><td colspan=2 align=left><input type=radio name=uploadpos value=yes" .($user["uploadpos"]=="yes" ? " checked" : "") . ">Yes <input type=radio name=uploadpos value=no" .($user["uploadpos"]=="no" ? " checked" : "") . ">No</td></tr>\n");
  print("<tr><td class=rowhead>Casino ban?</td><td colspan=2 align=left><input type=radio name=casinoban value=yes" .($user["casinoban"] === "yes" ? " checked" : "").">Yes <input type=radio name=casinoban value=no" .($user["casinoban"] === "no" ? " checked" : "").">No</td></tr>\n");
  print("<tr><td class=rowhead>BlackJack ban?</td><td colspan=2 align=left><input type=radio name=blackjackban value=yes" .($user["blackjackban"] === "yes" ? " checked" : "").">Yes <input type=radio name=blackjackban value=no" .($user["blackjackban"] === "no" ? " checked" : "").">No</td></tr>\n");
  print("<tr><td class=rowhead>Parked?</td><td colspan=2 align=left><input type=radio name=parked value=yes" .($user["parked"]=="yes" ? " checked" : "") . ">Yes <input type=radio name=parked value=no" .($user["parked"]=="no" ? " checked" : "") . ">No</td></tr>\n");
  print("<tr><td class=rowhead>Anonymous?</td><td colspan=2 align=left><input type=radio name=anonymous value=yes" .($user["anonymous"]=="yes" ? " checked" : "") . ">Yes <input type=radio name=anonymous value=no" .($user["anonymous"]=="no" ? " checked" : "") . ">No</td></tr>\n");
  print("<tr><td class=rowhead>Amount Uploaded</td><td colspan=2 align=left>".
                "<input type=text size=40 name=uploaded value=\"".safechar($user["uploaded"]).
                "\" />&nbsp;bytes<input type=hidden name=uploadbase value=\"".safechar($user["uploaded"]).
                "\" /></td></tr>\n");
  print("<tr><td class=rowhead>Amount Downloaded</td><td colspan=2 align=left>".
                "<input type=text size=40 name=downloaded value=\"".safechar($user["downloaded"]).
                "\" />&nbsp;bytes<input type=hidden name=downloadbase value=\"".safechar($user["downloaded"]).
                "\" /></td></tr>\n");
  if ($CURUSER['class'] >=  UC_MODERATOR){
  $check_if_theyre_shitty = mysql_query("SELECT suspect FROM shit_list WHERE userid=$CURUSER[id] AND suspect=".$id) or sqlerr(__FILE__, __LINE__);
  if (mysql_num_rows($check_if_theyre_shitty) !== 0)
  echo'<br>this member is on your shit list click <a class=altlink href=shit_list.php>HERE</a> to see your shit list';
  else        
  echo'<br><a class=altlink href=shit_list.php?action=new&shit_list_id='.$id.'&return_to=userdetails.php?id='.$id.'>add member to your shit list?</a>';
  }
  print("</td></tr>");
  print("<tr><td colspan=3 align=center><input type=submit class=btn value='Okay'></td></tr>\n");
  print("</table>\n");
  print("</form>\n");
  end_frame();
}
end_main_frame();
stdfoot();

?>