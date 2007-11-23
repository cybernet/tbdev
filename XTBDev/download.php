<?php

require_once("include/bittorrent.php");
require_once("include/benc.php");

loggedinorreturn();


if(!ENA_ALTANNOUNCE && isset($_GET['id']))
{
	$matches[1]=0+$_GET['id'];
	$matches[2]=$_GET['name'];
} else {
	if(!isset($_SERVER['PATH_INFO']))
		$_SERVER['PATH_INFO']=(isset($_SERVER['ORIG_PATH_INFO']))?$_SERVER['ORIG_PATH_INFO']:$_SERVER['REQUEST_URI'];
	if (!preg_match(':^/(\d{1,10})/(.+)\.torrent$:', $_SERVER["PATH_INFO"], $matches))
	httperr();
}

$id = 0 + $matches[1];
if (!$id)
	httperr();

$res = mysql_query("SELECT name FROM torrents WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_assoc($res);

$fn = "$torrent_dir/$id.torrent";

if (!$row || !is_file($fn) || !is_readable($fn))
	httperr();


mysql_query("UPDATE torrents SET hits = hits + 1 WHERE id = $id");


$dict = bdec_file($fn, filesize($fn));
if(ENA_PASSKEY) 
	verify_passkey($CURUSER['passkey']);
$dict['value']['announce']=bdec(benc_str(  "$BASEURL/". (ENA_ALTANNOUNCE? 
	("tracker.php/". (ENA_PASSKEY?"{$CURUSER['passkey']}/":'') . "announce"):
	("announce.php". (ENA_PASSKEY?"?passkey={$CURUSER['passkey']}":'')))));
	
$dict = benc(bdec(benc($dict)));

header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . strlen($dict));
header("Content-Type: application/x-bittorrent");
header("Content-Disposition: attachment; filename=\"{$matches[2]}\";" );

print $dict;

?>