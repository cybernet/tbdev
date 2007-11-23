<?php

require_once("include/bittorrent.php");
require_once("include/benc.php");


if(!ENA_ALTANNOUNCE && isset($_GET['id']))
{
	$matches[1]=0+$_GET['id'];
	$matches[0]=$_GET['name'];
} elseif (!preg_match(':^/(\d{1,10})/(.+)\.torrent$:', $_SERVER["PATH_INFO"], $matches))
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
if(ENA_PASSKEY) 
	verify_passkey($CURUSER['passkey']);
$dict['announce']['value'] = "$BASEURL/". (ENA_ALTANNOUNCE? 
	("tracker.php/". (ENA_PASSKEY?"{$CURUSER['passkey']}/":'') . "announce"):
	("announce.php". (ENA_PASSKEY?"?{$CURUSER['passkey']}":'')));

print benc($dict);

?>