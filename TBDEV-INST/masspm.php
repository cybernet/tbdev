<?php
require ("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
dbconn();
maxcoder();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
if (get_user_class() < UC_ADMINISTRATOR)
hacker_dork("Mass Pm - Nosey Cunt !");

stdhead("Mass PM");
if ($_POST['message'] != "")
{
$num=0;
foreach( array_keys($_POST) as $x)
{
 if (substr($x,0,3) == "UC_"){
  $querystring .= " OR class = ".constant($x);
  $classnames .= substr($x,3).", ";
  $num++;
 }
}

if ($num == $_POST["numclasses"]){
 $res = mysql_query("SELECT id FROM users");
 $msg = $_POST['message']  . "\n\nNOTICE: This is a mass pm, it has been sent to everyone";
}else{
 $res = mysql_query("SELECT id FROM users where id = 1".unsafeChar($querystring)) or sqlerr(__FILE__, __LINE__);
 $msg = $_POST['message']  . "\n\nNOTICE: This is a mass pm, it has been sent to the following classes: " . substr($classnames,0,(strlen($classnames)-2));
}

if ($_POST["fromsystem"] == "yes"){ $sender_id="0";}else{$sender_id = $CURUSER["id"];}

while($arr = mysql_fetch_row($res))
{
 mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster) VALUES ($sender_id, $arr[0], '" . get_date_time() . "', " . sqlesc($msg) . ", $sender_id)") or sqlerr(__FILE__, __LINE__);
}

print("<b>Mass Private Message Dispatched.</b><br>");
}


?>
<h1>Mass PM</h1>
<form method=post action="masspm.php">
<table border=1 cellspacing=0 cellpadding=5 class="main" >
<tr><td colspan=2 class="rowhead"><div align=left>Send To (check all that apply):</div></td></tr>
<tr><td colspan=2>
<?
$numclasses=0;
$constants = get_defined_constants ();
foreach( array_keys($constants) as $x)
{
if (substr($x,0,3) == "UC_"){
 echo "<input name=\"".$x."\" type=\"checkbox\" value=1 checked>".substr($x,3)."<br>";
 $numclasses++;
}
}
?>
<input type="hidden" name="numclasses" value="<? echo $numclasses; ?>" />
</td></tr>
<tr><td class="rowhead">Message</td><td><textarea cols=60 rows=6 name="message"></textarea></td></tr>
<tr><td class="rowhead">System</td><td><input type="checkbox" name="fromsystem" value="yes" />Say the message was sent by 'system'</td>
<tr><td align="center" colspan=2><input type="submit" value="Okay" class="btn" /></td></tr>
</table>
</form>
<? stdfoot(); ?>
