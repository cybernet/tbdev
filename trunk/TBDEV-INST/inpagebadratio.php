<?
require "include/bittorrent.php";
require_once ("include/user_functions.php");
header('Content-type: text/html; charset=ISO-8859-1');
dbconn(false);
loggedinorreturn();

function bark($msg) {
stdhead("Error");
stdmsg("Error", $msg);
stdfoot();
exit;
}

$id = 0 + $_GET["id"];
if (!is_valid_id($id))
bark($tracker_lang['invalid_id']);

$r = @sql_query("SELECT * FROM users WHERE id=$id") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_array($r) or bark("No User with this ID.");
if ($user["status"] == "pending") die;

if (get_user_class() >= UC_MODERATOR && $user["class"] < get_user_class())
{

print("<form method=\"post\" action=\"inpageratioedit.php\">\n");
print("<input type=\"hidden\" name=\"action\" value=\"edituser\">\n");
print("<input type=\"hidden\" name=\"userid\" value=\"$id\">\n");
print("<input type=\"hidden\" name=\"class\" value=\"$user[class]\">\n");
print("<input type=\"hidden\" name=\"returnto\" value=\"badratio.php?done=no\">\n");
print("<br /><table class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");

print("<tr><td class=colhead colspan=3 align=center>Quick-Edit <a target=_blank href=userdetails.php?id=".$user["id"].">".$user["username"]."</a></td></tr>");

?>

<?
if($user["immun"] == "no"){
$modcomment = htmlspecialchars($user["modcomment"]);
if ($user["downloaded"] > 0) {
$uratio = $user["uploaded"] / $user["downloaded"];
$uratio = number_format($uratio, 3);
}
$timeto = get_date_time(gmtime() + 14 * 86400);
$frist = get_date_time(gmtime() + 8 * 86400);
$bookmcomment = "".htmlspecialchars($user["bookmcomment"])."";
$enabled = $user["enabled"] == 'yes';
print("<form action=\"\" target=bookmcomment name=bookmcomment><tr><td class=rowhead>Add to Bookmarks?</td><td colspan=2 class=tablea align=left><input type=radio name=addbookmark value=yes" .($user["addbookmark"] == "yes" ? " checked" : "").">Yes - One to watch<input type=radio onClick=\"fuellen(this.form,'text1','Bad Ratio (".$uratio.") Time until ".date("d.m.Y", strtotime($timeto))."')\" name=addbookmark value=ratio" .($user["addbookmark"] == "ratio" ? " checked" : "").">Yes - Bad Ratio <input type=radio onClick=\"fuellen(this.form,'text1','".$bookmcomment." / Time until because Ratio ($uratio) extended to ".date("d.m.Y", strtotime($frist))." ')\" name=addbookmark value=frist>Extend time until <input type=radio name=addbookmark onClick=\"fuellen(this.form,'text1','')\" value=no" .($user["addbookmark"] == "no" ? " checked" : "").">No</td></tr>\n");
print("<tr><td class=rowhead>Bookmark Reason:</td><td class=tablea colspan=2 align=left><textarea cols=90 rows=6 name=bookmcomment>$bookmcomment</textarea></td></tr>\n");
//print("<tr><td class=rowhead>(last) bookmarked</td><td class=tablea colspan=2 align=left>".$user["bookmarkadded"]."</td></tr>\n");
print("<tr><td class=rowhead>Teamcomment:</td><td colspan=2><textarea cols=90 rows=4 readonly>".$modcomment."</textarea></td></tr>");
print("<tr><td class=rowhead>Warnstatus</td><td align=left colspan=2>".$user["warns"]."%</td></tr>\n");
print("<tr><td class=\"rowhead\" rowspan=\"2\">Enabled</td><td colspan=\"2\" align=\"left\"><input name=\"enabled\" onClick=\"fuellen2(this.form,'text1','')\" value=\"yes\" type=\"radio\"" . ($enabled ? " checked" : "") . ">Yes <input name=\"enabled\" onClick=\"fuellen2(this.form,'text1','Bad Ratio (".$uratio.") ')\" value=\"no\" type=\"radio\"" . (!$enabled ? " checked" : "") . ">No</td></tr>\n");
print("<tr><td colspan=\"2\" align=\"left\">Disable Reason:&nbsp;<input type=\"text\" name=\"disreason\" size=\"60\" /></td></tr>");


print("<tr><td colspan=\"3\" align=\"center\"><input type=\"submit\" class=\"btn\" value=\"OK\"></td></tr>\n");

print("</table>\n");
print("</form>\n");

print("<br><table><tr><td class=colhead colspan=2 align=center>Depending on the action the member will receive either:</td></tr>");
print("<tr><td>Message 1 (info for bad ratio warn</td>");
print("<td>Message 2 (info for extend time ratio warn)</td></tr>");
print("</table>");

}
else{
if ($user["immun"] == "yes")
$whynot = "This Member is immune";
elseif ($user["addbookmark"] == "ratio")
$whynot = "Already bookmarked";

print("<tr><td colspan=\"3\" align=\"center\">".$whynot."</td></tr></table>");
}
}