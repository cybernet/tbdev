<?

require_once("include/bittorrent.php");
require_once("include/benc.php");

dbconn();

if (!preg_match(':^/(\d{1,10})/(.+)\.torrent$:', $_SERVER["PATH_INFO"], $matches))
	httperr();

$id = 0 + $matches[1];
if (!$id)
	httperr();

$res = mysql_query("SELECT name FROM torrents WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);

$fn = "$torrent_dir/$id.torrent";

if (!$row || !is_file($fn) || !is_readable($fn))
	httperr();


mysql_query("UPDATE torrents SET hits = hits + 1 WHERE id = $id");


header("Content-Type: application/x-bittorrent");

$dict = bdec_file($fn, filesize($fn));
if(ENA_PASSKEY) {
	verify_passkey($CURUSER['passkey']);
	$dict['value']['announce'] = bdec(benc_str("$BASEURL/". (ENA_ALTANNOUNCE ? "tracker.php/$CURUSER[passkey]/announce":"announce.php?passkey=$CURUSER[passkey]")));
} else if(ENA_ALTANNOUNCE)
	$dict['value']['announce'] = bdec(benc_str("$BASEURL/tracker.php/announce"));

print benc($dict);

?>