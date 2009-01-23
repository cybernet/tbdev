<?php

require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

if ($CURUSER['class'] < UC_MODERATOR)
hacker_dork("banip.php");

$doUpdate = false;

$remove = isset($_GET['remove']) ? (int)$_GET['remove'] : 0;
if (is_valid_id($remove))
{
$res = @sql_query("SELECT first FROM bans WHERE id = ".mysql_real_escape_string($remove)) or sqlerr();
$arr = mysql_fetch_assoc($res);
if($arr)
{
$ipban = long2ip($arr['first']);
  unlink(''.$banpath.'/'.$ipban.'');
sql_query("DELETE FROM bans WHERE id=".mysql_real_escape_string($remove)) or sqlerr();
write_log("Ban ".safe($remove)." was removed by $CURUSER[id] ($CURUSER[username])");
$doUpdate = true;
}
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{

if(isset($_POST['cacheit']))
$doUpdate = true;

else
{
$ipban = (isset($_POST['first']) ? trim($_POST['first']):'');
$comment = (isset($_POST['comment']) ? trim($_POST['comment']):'');
if (!$ipban || !$comment)
stderr("Error", "Missing form data.");
$ip = ip2long($ipban);
$ipban = long2ip($ip);
if ($ip == -1)
stderr("Error", "Bad IP address.");
$comment = sqlesc($comment);
$added = sqlesc(get_date_time());
sql_query("INSERT INTO bans (added, addedby, first, comment) VALUES($added, $CURUSER[id], $ip, $comment)") or sqlerr(__FILE__, __LINE__);
@fclose(@fopen(''.$banpath.'/'.$ipban,'w'));
	$doUpdate = true;
	//header("Location: $BASEURL/bans.php");
	//die;
	}
}

ob_start("ob_gzhandler");

$res = sql_query("SELECT b.*, u.username FROM bans b LEFT JOIN users u on b.addedby = u.id ORDER BY added DESC") or sqlerr(__FILE__,__LINE__);

$configfile="<"."?php\n\n\$bans = array(\n";

stdhead("Bans");

print("<h1>Current Bans</h1>\n");

if (mysql_num_rows($res) == 0)
  print("<p align='center'><b>Nothing found</b></p>\n");
else
{
  print("<table border='1' cellspacing='0' cellpadding='5'>\n");
  print("<tr><td class='colhead'>Added</td><td class='colhead' align='left'>First IP</td>".
    "<td class='colhead' align='left'>By</td><td class='colhead' align=l'eft'>Comment</td><td class='colhead'>Remove</td></tr>\n");

  while ($arr = mysql_fetch_assoc($res))
  {
  if($doUpdate) {

       $configfile .= "array('id'=> '{$arr['id']}', 'first'=> '{$arr['first']}', 'last'=> '{$arr['last']}'),\n";

    }
	$arr["first"] = long2ip($arr["first"]);
print("<tr><td>$arr[added]</td><td align='left'>$arr[first]</td><td align='left'><a href='userdetails.php?id=$arr[addedby]'>$arr[username]".
"</a></td><td align='left'>$arr[comment]</td><td><a href='banip.php?remove=$arr[id]'>Remove</a></td></tr>\n");
}
print("</table>\n");
}
      if($doUpdate) {
      $configfile .= "\n);\n\n?".">";
      $filenum = fopen ("cache/bans_cache.php","w");
      ftruncate($filenum, 0);
      fwrite($filenum, $configfile);
      fclose($filenum);
      }

if ($CURUSER['class'] >= UC_ADMINISTRATOR)
{
$first = (isset($_POST['first']) ? trim($_POST['first']):'');
$comment = (isset($_POST['comment']) ? trim($_POST['comment']):'');

print("<h2>Add ban</h2>\n");
print("<form method='post' action='banip.php'>\n");
print("<table border='1' cellspacing='0' cellpadding='5'>\n");
print("<tr><td class='rowhead'>IP</td><td><input type='text' value=\"".$first."\" name='first' size='40' /></td></tr>\n");
print("<tr><td class='rowhead'>Comment</td><td><input type='text' value=\"".$comment."\" name='comment' size='40' /></td></tr>\n");
print("<tr><td colspan='2' align='center'><input type='submit' value='Okay' class='btn' /></td></tr>\n");
print("<tr><td colspan='2' align='center'><input type='submit' name='cacheit' value='Cache' class='btn' /></td></tr>\n");

print("</table></form>\n");
}

stdfoot();
?>