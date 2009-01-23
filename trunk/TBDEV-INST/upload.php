<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
dbconn(false);
maxcoder();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
stdhead("Upload");
if ($CURUSER["uploadpos"] == 'no')
{
stdmsg("Sorry...", "You are not authorized to upload torrents.  (See <a href=\"rules.php\">Read the site rules</a>)");
stdfoot();
exit;
} 
?>
<div align=Center>
<form name=upload method=post action=takeupload.php enctype=multipart/form-data>
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_torrent_size?>">
<p>The tracker's announce url is <b><?= $announce_urls[0] ?></b></p>
<table border="1" cellspacing="0" cellpadding="10">
<?php
//==== offer dropdown for offer mod
$res = sql_query("SELECT id, name, allowed FROM offers WHERE userid = $CURUSER[id] ORDER BY name ASC") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0) {
$offer = "<select name=offer><option value=0>Your Offers</option>";
while($row = mysql_fetch_array($res)) {
if ($row['allowed'] == 'allowed')
$offer .= "<option value=\"" . $row["id"] . "\">" . safechar($row["name"]) . "</option>";
}
$offer .= "</select>";
tr("Offer", $offer."<br> If you are uploading one of your offers please select it here so the voters will be notified." , 1);
}
tr("Music Uploads", '<a href=upload2.php><font color=yellow>Click For <b>Music Uploads</b></font></a></tr>', 1);
if (get_user_class() >= UC_POWER_USER){    
tr("Multiple Uploads", '<a href=multiupload.php><font color=red>Click For <b>Multi Uploads</b></font></a></tr>', 1);
}
tr("URL", "<input type=text name=url size=80><br />(Taken from IMDB. <b>Please use ONLY for MOVIES.</b>)\n", 1 );
tr("Poster", "<input type=text value=$BASEURL/poster.jpg name=poster size=80><br>(Direct link for a poster image to be shown on the details page - Include full url)\n", 1);
tr("YouTube Sample", "<input type=\"text\" name=\"tube\" size=\"80\" /><br />For Samples Should be in the format of http://www.youtube.com/watch?vxxxx .\n", 1);
tr("Torrent file", "<input type=file name=file size=80>\n", 1);
tr("Torrent name", "<input type=\"text\" name=\"name\" size=\"80\" /><br />(Taken from filename if not specified. <b>Please use descriptive names.</b>)\n", 1);
tr("NFO file", "<input type=file name=nfo size=81><br>(<b>optional.</b> Can only be viewed by power users)\n", 1);
tr("Description", "<textarea name=\"descr\" rows=\"10\" cols=\"80\"></textarea>" . "<br>(HTML/BB code is <b>not</b> allowed.)", 1);
tr("Strip ASCII", "<input type=checkbox name=strip value=strip unchecked />   <a href=\"http://en.wikipedia.org/wiki/ASCII_art\" target=\"_blank\">what is this ?</a><b> Copy And Paste Nfo Contents Into Description And Check Strip</b>", 1);
?>
<script type="text/javascript">
window.onload = function() {
    setupDependencies('upload');
  };
</script>
<tr><td align=right><b>Genre</b><br>(optional)</td><td align=left>
<table><tr>
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
$s = "<select name=\"type\">\n<option value=\"0\">(choose one)</option>\n";
$cats = genrelist();
foreach ($cats as $row)
$s .= "<option value=\"" . $row["id"] . "\">" . safechar($row["name"]) . "</option>\n";
$s .= "</select>\n";
tr("Type", $s, 1);
$so = "<select name=\"scene\">\n<option value=\"no\">Non-Scene</option>\n<option value=\"yes\">Scene</option>\n</select>\n";
tr("Release", $so, 1);
$sp = "<select name=\"request\">\n<option value=\"no\">No</option>\n<option value=\"yes\">Yes</option>\n</select>\n";
tr("Requested", $sp, 1);
tr("Show uploader", "<input type=checkbox name=uplver value=yes>Dont show my username in 'Uploaded By' field in browse.", 1);
tr("Vip Torrent?", "<input type='checkbox' name='vip'" . (($row["vip"] == "yes") ? " checked='checked'" : "" ) . " value='1' /> If this one is checked, only Vip's can download this torrent", 1);
//===free upload or staff only torrent
if (get_user_class() >= UC_VIP){      
tr("Count Stats:","<input type=radio name=countstats value=yes checked=checked /> yes <input type=radio name=countstats value=no /> no - free download only upload is counted.<br /> ",1);
}
//===end free upload
if(get_user_class() >= UC_ADMINISTRATOR){
tr("Multiplicator",
"<input type=radio name=multiplicator checked value=0>No Multiplicator
<input type=radio name=multiplicator value=2>Upload x 2
<input type=radio name=multiplicator value=3>Upload x 3
<input type=radio name=multiplicator value=4>Upload x 4
<input type=radio name=multiplicator value=5>Upload x 5"
,1);
}
?>
<tr><td align="center" colspan="2"><input type="submit" class=btn value="Do it!" /></td></tr>
</table>
</form>
<?php
stdfoot();
?>