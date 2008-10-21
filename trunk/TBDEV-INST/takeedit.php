<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
function bark($msg) {
	genbark($msg, "Edit failed!");
}

if (!mkglobal("id:name:descr:type"))
	bark("missing form data");

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
$res = sql_query("SELECT owner, filename, save_as FROM torrents WHERE id = $id");
$row = mysql_fetch_assoc($res);
if (!$row)
	die();

if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
	bark("You're not the owner! How did that happen?\n");

$updateset = array();

if (!empty($_POST['poster']))
$poster = unesc($_POST['poster']);

if (!empty($_POST['tube']))
$tube = unesc($_POST['tube']);

$fname = $row["filename"];
preg_match('/^(.+)\.torrent$/si', $fname, $matches);
$shortfname = $matches[1];
$dname = $row["save_as"];

$genreaction = $_POST['genre'];
if ($genreaction != "keep")
{
if (isset($_POST["music"]))
$genre = implode(",", $_POST['music']);
elseif (isset($_POST["movie"]))
$genre = implode(",", $_POST['movie']);
elseif (isset($_POST["game"]))
$genre = implode(",", $_POST['game']);
elseif (isset($_POST["apps"]))
$genre = implode(",", $_POST['apps']);
$updateset[] = "newgenre = " .sqlesc($genre);
}

$nfoaction = $_POST['nfoaction'];
if ($nfoaction == "update")
{
  $nfofile = $_FILES['nfo'];
  if (!$nfofile) die("No data " . var_dump($_FILES));
  if ($nfofile['size'] > 65535)
    bark("NFO is too big! Max 65,535 bytes.");
  $nfofilename = $nfofile['tmp_name'];
  if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0)
    $updateset[] = "nfo = " . sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", file_get_contents($nfofilename)));
}
else
  if ($nfoaction == "remove")
    $updateset[] = "nfo = ''";

$updateset[] = "tube = " . sqlesc($tube);
$url = $_POST['url'];
$updateset[] = "url = " . sqlesc($url);
$nuked = $_POST["nuked"];
$nukereason = $_POST["nukereason"];
$updateset[] = "nuked = " . sqlesc($nuked);
$updateset[] = "nukereason = " . sqlesc($nukereason);
//===count stats / free download
if ((isset($_POST['countstats'])) && (($countstats = $_POST['countstats']) != $row['countstats'])){    
if(get_user_class() >= UC_MODERATOR)    
$updateset[] = "countstats = " . sqlesc($countstats);
}
else
$updateset[] = "countstats = 'yes'";

$updateset[] = "name = " . sqlesc($name);
$updateset[] = "scene = '" . ($_POST["scene"] == "no" ? "no" : "yes") . "'";
$updateset[] = "request = '" . ($_POST["request"] == "no" ? "no" : "yes") . "'";
$updateset[] = "search_text = " . sqlesc(searchfield("$shortfname $dname $torrent"));
$updateset[] = "descr = " . sqlesc($descr);
$updateset[] = "ori_descr = " . sqlesc($descr);
$updateset[] = "category = " . (0 + $type);
if ($CURUSER["admin"] == "yes") {
	if ($_POST["banned"]) {
		$updateset[] = "banned = 'yes'";
		$_POST["visible"] = 0;
	}
	else
		$updateset[] = "banned = 'no'";
}
$updateset[] = "visible = '" . ($_POST["visible"] ? "yes" : "no") . "'";
$updateset[] = "anonymous = '" . ($_POST["anonymous"] ? "yes" : "no") . "'";
$updateset[] = "poster = " . sqlesc($poster);
if(get_user_class() >= UC_MODERATOR){
$updateset[] = "sticky = '" . ($_POST["sticky"] ? "yes" : "no") . "'";
}

if(get_user_class() >= UC_ADMINISTRATOR)
$multiplicator = $_POST["multiplicator"];
else
$multiplicator = "0";
$updateset[] = "multiplicator = " . sqlesc($multiplicator);


sql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id");

if ($CURUSER["anonymous"]=='yes')
write_log("Torrent $id ($name) was edited by Anonymous");
else
write_log("Torrent $id ($name) was edited by $CURUSER[username]");

$returl = "details.php?id=$id&edited=1";
if (isset($_POST["returnto"]))
	$returl .= "&returnto=" . urlencode($_POST["returnto"]);
header("Refresh: 0; url=$returl");


?>
