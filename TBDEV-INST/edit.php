<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
if (!mkglobal("id"))
	die();
$id = 0 + $id;
if (!$id)
	die();
dbconn();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

$res = mysql_query("SELECT * FROM torrents WHERE id = $id");
$row = mysql_fetch_assoc($res);
if (!$row)
	die();

stdhead("Edit torrent \"" . $row["name"] . "\"");

if (!isset($CURUSER) || ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)) {
	print("<h1>Can't edit this torrent</h1>\n");
	print("<p>You're not the rightful owner, or you're not <a href=\"login.php?returnto=" . urlencode(substr($_SERVER["REQUEST_URI"],1)) . "&amp;nowarn=1\">logged in</a> properly.</p>\n");
}
else {
	//print("<form method=post action=takeedit.php enctype=multipart/form-data>\n");
	print("<form name=edit method=post action=takeedit.php enctype=multipart/form-data>\n");
	print("<input type=\"hidden\" name=\"id\" value=\"$id\">\n");
	if (isset($_GET["returnto"]))
		print("<input type=\"hidden\" name=\"returnto\" value=\"" . safechar($_GET["returnto"]) . "\" />\n");
	print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"10\">\n");
	tr("URL", "<input type=text name=url size=80 value='".$row["url"]."'>", 1);
	tr("Poster", "<input type=text name=poster size=80 value='".$row["poster"]."'><br>(Direct link for a poster image to be shown on the details page)\n", 1);
	tr("Trailer", "<input type=text name=tube size=80 value='".$row["tube"]."'><br>(Direct link for youtube trailer)\n", 1);
	tr("Torrent name", "<input type=\"text\" name=\"name\" value=\"" . safechar($row["name"]) . "\" size=\"80\" />", 1);
	tr("NFO file", "<input type=radio name=nfoaction value='keep' checked>Keep current<br>".
	"<input type=radio name=nfoaction value='update'>Update:<br><input type=file name=nfo size=80>", 1);
    if ((strpos($row["ori_descr"], "<") === false) || (strpos($row["ori_descr"], "&lt;") !== false))
    $c = "";
    else
    $c = " checked";
	tr("Description", "<textarea name=\"descr\" rows=\"10\" cols=\"80\">" . safechar($row["ori_descr"]) . "</textarea><br>(HTML is not allowed. <a href=tags.php>Click here</a> for information on available tags.)", 1);
	$s = "<select name=\"type\">\n";

	$cats = genrelist();
	foreach ($cats as $subrow) {
		$s .= "<option value=\"" . $subrow["id"] . "\"";
		if ($subrow["id"] == $row["category"])
			$s .= " selected=\"selected\"";
		$s .= ">" . safechar($subrow["name"]) . "</option>\n";
	}

	$s .= "</select>\n";
	tr("Type", $s, 1);
	$so = "<select name=\"scene\">\n<option value=\"no\"".($row["scene"] == "no" ? " selected" : "").">Non-Scene</option>\n<option value=\"yes\"".($row["scene"] == "yes" ? " selected" : "").">Scene</option>\n</select>\n";
    tr("Release", $so, 1);
    $sp = "<select name=\"request\">\n<option value=\"no\"".($row["request"] == "no" ? " selected" : "").">No</option>\n<option value=\"yes\"".($row["request"] == "yes" ? " selected" : "").">Yes</option>\n</select>\n";
    tr("Requested", $sp, 1);
	tr("Visible", "<input type=\"checkbox\" name=\"visible\"" . (($row["visible"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" /> Visible on main page<br /><table border=0 cellspacing=0 cellpadding=0 width=420><tr><td class=embedded>Note that the torrent will automatically become visible when there's a seeder, and will become automatically invisible (dead) when there has been no seeder for a while. Use this switch to speed the process up manually. Also note that invisible (dead) torrents can still be viewed or searched for, it's just not the default.</td></tr></table>", 1);
    tr("Anonymous uploader", "<input type=\"checkbox\" name=\"anonymous\"" . (($row["anonymous"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" />  Check this box to hide the uploader of the torrent", 1);

    if ($CURUSER["admin"] == "yes")
		tr("Banned", "<input type=\"checkbox\" name=\"banned\"" . (($row["banned"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"1\" /> Banned", 1);
    if(get_user_class() > UC_VIP)
    if ($row["countstats"] == "yes")
    $mess = " yes - this is a normal torrent!";
    else
    $mess = " no - this is a FREE torrent!";     
    ?>
    <tr><td align="right"><font color="red">*</font><b>Count Stats:</b></td>
    <td><select name="countstats">
    <option value="<?=safechar($row[countstats])?>"><?=safechar($row[countstats])?></option>
    <option value="yes"> yes </option><option value="no"> no </option></select> <?=$mess?></td></tr>
     <?php
	if(get_user_class() > UC_MODERATOR)
    tr("Sticky", "<input type='checkbox' name='sticky'" . (($row["sticky"] == "yes") ? " checked='checked'" : "" ) . " value='yes' />Set sticky this torrent!", 1);
	if(get_user_class() >= UC_ADMINISTRATOR){
    tr("Multiplicator",
    "<input type=radio name=multiplicator".(($row["multiplicator"] == "0") ? " checked='checked'" : "" ) . " value=0>No Multiplicator
    <input type=radio name=multiplicator " . (($row["multiplicator"] == "2") ? " checked='checked'" : "" ) . " value=2>Upload x 2
    <input type=radio name=multiplicator " . (($row["multiplicator"] == "3") ? " checked='checked'" : "" ) . " value=3>Upload x 3
    <input type=radio name=multiplicator " . (($row["multiplicator"] == "4") ? " checked='checked'" : "" ) . " value=4>Upload x 4
    <input type=radio name=multiplicator " . (($row["multiplicator"] == "5") ? " checked='checked'" : "" ) . " value=5>Upload x 5"
    ,1);
    }
	tr("Nuked","<input type=radio name=nuked" . ($row["nuked"] == "yes" ? " checked" : "") . " value=yes>Yes <input type=radio name=nuked" . ($row["nuked"] == "no" ? " checked" : "") . " value=no>No <input type=radio name=nuked" . ($row["nuked"] == "unnuked" ? " checked" : "") . " value=unnuked>Unnuked",1);
    tr("Nuke Reason", "<input type=\"text\" name=\"nukereason\" value=\"" . safechar($row["nukereason"]) . "\" size=\"80\" />", 1);
	?>
<script type="text/javascript">
window.onload = function() {
    setupDependencies('edit'); //name of form(s). Seperate each with a comma (ie: 'weboptions', 'myotherform' )
  };
</script>
<tr><td align=right><b>Genre</b><br>(optional)</td><td align=left>
<table><tr><input type=radio name=genre value="keep" checked>Dont touch it (Current: <?=$row["newgenre"]?>)<br>
<td style="border:none"><input type="radio" name="genre" value="movie">Movie</td>
<td style="border:none"><input type="radio" name="genre" value="music">Music</td>
<td style="border:none"><input type="radio" name="genre" value="game">Game</td>
<td style="border:none"><input type="radio" name="genre" value="apps">Apps</td>
<td style="border:none"><input type="radio" name="genre" value="">None</td>
</tr>
<tr><td colspan=4 style="border:none">
<label style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 3px silver groove;">
<input type="hidden" class="DEPENDS ON genre BEING movie OR genre BEING music"></label>
<?
$movie = array ( 'Action', 'Comedy', 'Thriller', 'Adventure', 'Family', 'Adult', 'Sci-fi' );
for ( $x = 0; $x < count ( $movie ); $x++ )
{
echo "<label><input type=\"checkbox\" value=\"$movie[$x]\"  name=\"movie[]\" class=\"DEPENDS ON genre BEING movie\">$movie[$x]</label>";
}
$music = array ( 'Hip Hop', 'Rock', 'Pop', 'House', 'Techno', 'Commercial' );
for ( $x = 0; $x < count ( $music ); $x++ )
{
echo "<label><input type=\"checkbox\" value=\"$music[$x]\" name=\"music[]\" class=\"DEPENDS ON genre BEING music\">$music[$x]</label>";
}
$game = array ( 'Fps', 'Strategy', 'Adventure', '3rd Person', 'Acton' );
for ( $x = 0; $x < count ( $game ); $x++ )
{
echo "<label><input type=\"checkbox\" value=\"$game[$x]\" name=\"game[]\" class=\"DEPENDS ON genre BEING game\">$game[$x]</label>";
}
$apps = array ( 'Burning', 'Encoding', 'Anti-Virus', 'Office', 'Os', 'Misc', 'Image' );
for ( $x = 0; $x < count ( $apps ); $x++ )
{
echo "<label><input type=\"checkbox\" value=\"$apps[$x]\" name=\"apps[]\" class=\"DEPENDS ON genre BEING apps\">$apps[$x]</label>";
}

?>
</td></tr></table>
</td></tr>
<?
	print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value='Edit it!' style='height: 25px; width: 100px'> <input type=reset value='Revert changes' style='height: 25px; width: 100px'></td></tr>\n");
	print("</table>\n");
	print("</form>\n");
	print("<p>\n");
	print("<form method=\"post\" action=\"delete.php\">\n");
  print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");
  print("<tr><td class=embedded style='background-color: #000000;padding-bottom: 5px' colspan=\"2\"><b>Delete torrent.</b> Reason:</td></tr>");
  print("<td><input name=\"reasontype\" type=\"radio\" value=\"1\">&nbsp;Dead </td><td> 0 seeders, 0 leechers = 0 peers total</td></tr>\n");
  print("<tr><td><input name=\"reasontype\" type=\"radio\" value=\"2\">&nbsp;Dupe</td><td><input type=\"text\" size=\"40\" name=\"reason[]\"></td></tr>\n");
  print("<tr><td><input name=\"reasontype\" type=\"radio\" value=\"3\">&nbsp;Nuked</td><td><input type=\"text\" size=\"40\" name=\"reason[]\"></td></tr>\n");
  print("<tr><td><input name=\"reasontype\" type=\"radio\" value=\"4\">&nbsp;Yoursite rules</td><td><input type=\"text\" size=\"40\" name=\"reason[]\">(req)</td></tr>");
  print("<tr><td><input name=\"reasontype\" type=\"radio\" value=\"5\" checked>&nbsp;Other:</td><td><input type=\"text\" size=\"40\" name=\"reason[]\">(req)</td></tr>\n");
	print("<input type=\"hidden\" name=\"id\" value=\"$id\">\n");
	if (isset($_GET["returnto"]))
		print("<input type=\"hidden\" name=\"returnto\" value=\"" . safechar($_GET["returnto"]) . "\" />\n");
  print("<td colspan=\"2\" align=\"center\"><input type=submit value='Delete it!' style='height: 25px'></td></tr>\n");
  print("</table>");
	print("</form>\n");
	print("</p>\n");
}

stdfoot();

?>
