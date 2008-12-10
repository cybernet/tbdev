<?php
require_once("include/benc.php");
require_once("include/bittorrent.php");
require_once "include/user_functions.php";
require_once ("include/bbcode_functions.php");
ini_set("upload_max_filesize",$max_torrent_size);
function bark($msg) {
genbark($msg, "Upload failed!");
}
dbconn(); 
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

if ($CURUSER["uploadpos"] == 'no')
die;


foreach(explode(":","descr:type:name") as $v) {
	if (!isset($_POST[$v]))
		bark("missing form data");
}

if (!isset($_FILES["file"]))
	bark("missing form data");

$f = $_FILES["file"];
$fname = unesc($f["name"]);

if (!empty($_POST['tube']))
$tube = unesc($_POST['tube']);

if(get_user_class() >= UC_ADMINISTRATOR)
$multiplicator = $_POST["multiplicator"];
else
$multiplicator = "0";

if (empty($fname))
    bark("Empty filename!");
if ($_POST['uplver'] == 'yes') {
$anonymous = "yes";
$anon = "Anonymous";
}
else {
$anonymous = "no";
$anon = $CURUSER["username"];
}

$nfofile = $_FILES['nfo'];
//if ($nfofile['name'] == '')
  /*bark("No NFO!");*/

//if ($nfofile['size'] == 0)
/* bark("0-byte NFO");*/

if ($nfofile['size'] > 65535)
  bark("NFO is too big! Max 65,535 bytes.");

$nfofilename = $nfofile['tmp_name'];

//if (@!is_uploaded_file($nfofilename))
// bark("NFO upload failed");

//AUTO VIEWNFO
$descr = unesc($_POST["descr"]);
if (!$descr && $nfofile['name'] == '')
  bark("You must enter a description or NFO!");
//AUTO VIEWNFO

if($_POST['strip'] == 'strip')
{
    include 'include/strip.php';
        $descr = preg_replace("/[^\\x20-\\x7e\\x0a\\x0d]/", " ", $descr);
    strip($descr);
}

$scene = ($_POST["scene"] != "no" ? "yes" : "no");
$request = ($_POST["request"] != "no" ? "yes" : "no");
$catid = (0 + $_POST["type"]);
if (!is_valid_id($catid))
	bark("You must select a category to put the torrent in!");
	
if (!validfilename($fname))
	bark("Invalid filename!");
if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
	bark("Invalid filename (not a .torrent).");
$shortfname = $torrent = $matches[1];
if (!empty($_POST["name"]))
	$torrent = unesc($_POST["name"]);

$tmpname = $f["tmp_name"];
if (!is_uploaded_file($tmpname))
	bark("eek");
if (!filesize($tmpname))
	bark("Empty file!");

if (isset($_POST["music"]))
$genre = implode(",", $_POST['music']);
elseif (isset($_POST["movie"]))
$genre = implode(",", $_POST['movie']);
elseif (isset($_POST["game"]))
$genre = implode(",", $_POST['game']);
elseif (isset($_POST["apps"]))
$genre = implode(",", $_POST['apps']);

$dict = bdec_file($tmpname, $max_torrent_size);
if (!isset($dict))
	bark("What the hell did you upload? This is not a bencoded file!");

function dict_check($d, $s) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$a = explode(":", $s);
	$dd = $d["value"];
	$ret = array();
	$t='';
	foreach ($a as $k) {
		unset($t);
		if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
			$k = $m[1];
			$t = $m[2];
		}
		if (!isset($dd[$k]))
			bark("dictionary is missing key(s)");
		if (isset($t)) {
			if ($dd[$k]["type"] != $t)
				bark("invalid entry in dictionary");
			$ret[] = $dd[$k]["value"];
		}
		else
			$ret[] = $dd[$k];
	}
	return $ret;
}

function dict_get($d, $k, $t) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$dd = $d["value"];
	if (!isset($dd[$k]))
		return;
	$v = $dd[$k];
	if ($v["type"] != $t)
		bark("invalid dictionary entry type");
	return $v["value"];
}

list($ann, $info) = dict_check($dict, "announce(string):info");
list($dname, $plen, $pieces) = dict_check($info, "name(string):piece length(integer):pieces(string)");

if (strlen($pieces) % 20 != 0)
	bark("invalid pieces");

$filelist = array();
$totallen = dict_get($info, "length", "integer");
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
	$type = "single";
}
else {
	$flist = dict_get($info, "files", "list");
	if (!isset($flist))
		bark("missing both length and files");
	if (!count($flist))
		bark("no files");
	$totallen = 0;
	foreach ($flist as $fn) {
		list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
		$totallen += $ll;
		$ffa = array();
		foreach ($ff as $ffe) {
			if ($ffe["type"] != "string")
				bark("filename error");
			$ffa[] = $ffe["value"];
		}
		if (!count($ffa))
			bark("filename error");
		$ffe = implode("/", $ffa);
		$filelist[] = array($ffe, $ll);
	}
	$type = "multi";
}
$poster = unesc($_POST['poster']);
$tube = unesc($_POST['tube']);
$url = unesc($_POST['url']);
$dict['value']['announce']=bdec(benc_str( $announce_urls[0]));  // change announce url to local
$dict['value']['info']['value']['private']=bdec('i1e');  // add private tracker flag
$dict['value']['info']['value']['source']=bdec(benc_str( "[$DEFAULTBASEURL] $SITENAME")); // add link for bitcomet users
unset($dict['value']['announce-list']); // remove multi-tracker capability
unset($dict['value']['nodes']); // remove cached peers (Bitcomet & Azareus)
$dict=bdec(benc($dict)); // double up on the becoding solves the occassional misgenerated infohash
$dict['value']['comment']=bdec(benc_str( "In using this torrent you are bound by the '$SITENAME' Confidentiality Agreement By Law")); // change torrent comment
list($ann, $info) = dict_check($dict, "announce(string):info");
unset($dict['value']['created by']); //Null the created_by field///
$infohash = pack("H*", sha1($info["string"]));
$uclass = $CURUSER["class"] ;
// Replace punctuation characters with spaces
$torrent = str_replace("_", " ", $torrent);
///////////pretime////////
$pre = getpre($torrent,1);
$timestamp = strtotime($pre);
$tid = time();
if (empty($pre)) {
$predif = "N/A";
}else{
$predif = ago($tid - $timestamp);
}
//=== free download?
if (get_user_class() >= UC_VIP)
$countstats = unesc($_POST["countstats"]);
else
$countstats = "yes";
//===end
$nfo = sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", @file_get_contents($nfofilename)));
$ret = mysql_query("INSERT INTO torrents (search_text, filename, owner, visible, tube, multiplicator, uclass, anonymous, request, scene, info_hash, name, size, numfiles, url, poster, countstats, newgenre, type, vip, descr, ori_descr, category, save_as, added, last_action, nfo, afterpre) VALUES (" .implode(",", array_map("sqlesc", array(searchfield("$shortfname $dname $torrent"), $fname, $CURUSER["id"], "no", $tube, $multiplicator, $uclass, $anonymous, $request, $scene, $infohash, $torrent, $totallen, count($filelist), $url, $poster, $countstats, $genre, $type, $vip, $descr, $descr, 0 + $_POST["type"], $dname))) . ", '" . get_date_time() . "', '" . get_date_time() . "', $nfo, '" . $predif . "')") or sqlerr(__FILE__, __LINE__); 
if ($CURUSER["anonymous"]=='yes')
$message = "New Torrent : ($torrent) Uploaded - Anonymous User";
else
$message = "New Torrent : ($torrent) Uploaded by " . safechar($CURUSER["username"]) . "";
if (!$ret) {
	if (mysql_errno() == 1062)
		bark("torrent already uploaded!");
	bark("mysql puked: ".mysql_error());
}
$id = mysql_insert_id();

@mysql_query("DELETE FROM files WHERE torrent = $id");
foreach ($filelist as $file) {
	@mysql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".$file[1].")");
}
$fp = fopen("$torrent_dir/$id.torrent", "w");
if ($fp)
{
        @fwrite($fp, benc($dict), strlen(benc($dict)));
    fclose($fp);
}
//===add karma
sql_query("UPDATE users SET seedbonus = seedbonus+15.0 WHERE id = $CURUSER[id]") or sqlerr(__FILE__, __LINE__);
//===end
if ($CURUSER["anonymous"]=='yes')
write_log("Torrent $id ($torrent) was uploaded by Anonymous");
else
write_log("Torrent $id ($torrent) was uploaded by $CURUSER[username]");
////////new torrent upload detail sent to shoutbox//////////
autoshout($message);
/////////////////////////////end///////////////////////////////////

/* RSS feeds */

if (($fd1 = @fopen("rss.xml", "w")) && ($fd2 = fopen("rssdd.xml", "w")))
{
	$cats = "";
	$res = mysql_query("SELECT id, name FROM categories");
	while ($arr = mysql_fetch_assoc($res))
		$cats[$arr["id"]] = $arr["name"];
	$s = "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n<rss version=\"0.91\">\n<channel>\n" .
		"<title>TorrentBits</title>\n<description>0-week torrents</description>\n<link>$DEFAULTBASEURL/</link>\n";
	@fwrite($fd1, $s);
	@fwrite($fd2, $s);
	$r = mysql_query("SELECT id,name,descr,filename,category FROM torrents ORDER BY added DESC LIMIT 15") or sqlerr(__FILE__, __LINE__);
	while ($a = mysql_fetch_assoc($r))
	{
		$cat = $cats[$a["category"]];
		$s = "<item>\n<title>" . safechar($a["name"] . " ($cat)") . "</title>\n" .
			"<description>" . safechar($a["descr"]) . "</description>\n";
		@fwrite($fd1, $s);
		@fwrite($fd2, $s);
		@fwrite($fd1, "<link>$DEFAULTBASEURL/details.php?id=$a[id]&amp;hit=1</link>\n</item>\n");
		$filename = safechar($a["filename"]);
		@fwrite($fd2, "<link>$DEFAULTBASEURL/download.php/$a[id]/$filename</link>\n</item>\n");
	}
	$s = "</channel>\n</rss>\n";
	@fwrite($fd1, $s);
	@fwrite($fd2, $s);
	@fclose($fd1);
	@fclose($fd2);
}

/* Email notifs */
/*******************

$res = mysql_query("SELECT name FROM categories WHERE id=$catid") or sqlerr();
$arr = mysql_fetch_assoc($res);
$cat = $arr["name"];
$res = mysql_query("SELECT email FROM users WHERE enabled='yes' AND notifs LIKE '%[cat$catid]%'") or sqlerr();
$uploader = $CURUSER['username'];

$size = mksize($totallen);
$description = ($html ? strip_tags($descr) : $descr);

$body = <<<EOD
A new torrent has been uploaded.

Name: $torrent
Size: $size
Category: $cat
Uploaded by: $uploader

Description
-------------------------------------------------------------------------------
$description
-------------------------------------------------------------------------------

You can use the URL below to download the torrent (you may have to login).

$DEFAULTBASEURL/details.php?id=$id&hit=1

-- 
$SITENAME
EOD;
$to = "";
$nmax = 100; // Max recipients per message
$nthis = 0;
$ntotal = 0;
$total = mysql_num_rows($res);
while ($arr = mysql_fetch_row($res))
{
  if ($nthis == 0)
    $to = $arr[0];
  else
    $to .= "," . $arr[0];
  ++$nthis;
  ++$ntotal;
  if ($nthis == $nmax || $ntotal == $total)
  {
    if (!mail("Multiple recipients <$SITEEMAIL>", "New torrent - $torrent", $body,
    "From: $SITEEMAIL\r\nBcc: $to", "-f$SITEEMAIL"))
	  stderr("Error", "Your torrent has been been uploaded. DO NOT RELOAD THE PAGE!\n" .
	    "There was however a problem delivering the e-mail notifcations.\n" .
	    "Please let an administrator know about this error!\n");
    $nthis = 0;
  }
}
*******************/
header("Location: $BASEURL/details.php?id=$id&uploaded=1");

?>
