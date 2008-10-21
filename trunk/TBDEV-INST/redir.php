<?php
include_once('include/bittorrent.php');
dbconn();
if (!$_GET) die();
if (!isset($CURUSER)) die();
$url = '';
foreach($_GET as $name => $val)
{
$url .= "&$name=$val";
}
$i = strpos($url, "&url=");
if ($i !== false)
$url = substr($url, $i + 5);
if (substr($url, 0, 4) == "www.")
$url = "http://" . $url;
?>
<html><head><meta http-equiv="refresh" content="0; url=<?=safechar($url);?>"></head><body>
<table border="0" width="100%" height="100%"><tr><td><h2 align="center">Please Wait....Redirecting you to :<br />
<?=safechar($url);?></h2></td></tr></table></body></html>