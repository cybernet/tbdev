<?php
/**
 * @Author Neptune 
 * @Credits All credit to Retro for the great idea of his updated modtask.php
 * @Project TBDev.net
 * @Category Addon Mods
 * @Date Monday, Jan 5, 2009
 */
require 'include/bittorrent.php';
require 'include/bbcode_functions.php';
define('MIN_CLASS', UC_MODERATOR);
define('NFO_SIZE', 65535);
$possible_extensions = array('nfo', 'txt');

if (!mkglobal('id:name:descr:type')) die();

$id = 0 + $id;

if(!is_valid_id($id))
stderr('Error', 'Invalid ID!');

dbconn();
maxcoder();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
/**
 * @Function valid_torrent_name
 * @Notes only safe characters are allowed..
 * @Begin
 */
 function valid_torrent_name($torrent_name) {
	
	$allowedchars = 'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.-_';
	for ($i = 0; $i < strlen($torrent_name); ++$i)
	if (strpos($allowedchars, $torrent_name[$i]) === false)
		return false;	
		return true;

}
/**
 * @Function valid_torrent_name
 * @Notes only safe characters are allowed..
 * @End
 */
/**
 * @Function is_valid_url 
 * @Begin
 */
if (!function_exists('is_valid_url')) {
	
	function is_valid_url($link) {
	
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $link);

 }
}

/**
 * @Function is_valid_url
 * @End
 */
 
$select_torrent = mysql_query('SELECT * FROM torrents WHERE id = ' . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
$fetch_assoc = mysql_fetch_assoc($select_torrent) or stderr('Error', 'No torrent with this ID!');

if ($CURUSER['id'] != $fetch_assoc['owner'] && get_user_class() < MIN_CLASS)
stderr('You\'re not the owner!', 'How did that happen?');

$updateset = array();

$fname = $fetch_assoc['filename'];
preg_match('/^(.+)\.torrent$/si', $fname, $matches);
$shortfname = $matches[1];
$dname = $fetch_assoc['save_as'];

if ((isset($_POST['nfoaction'])) && ($_POST['nfoaction'] == 'update')) {
	
	if (empty($_FILES['nfo']['name']))
	stderr('Updated failed', 'No NFO!');
	
	if ($_FILES['nfo']['size'] == 0)
	stderr('Updated failed', '0-byte NFO!');
	
	if (!preg_match('/^(.+)\.['.join(']|[', $possible_extensions).']$/si', $_FILES['nfo']['name']))
	stderr('Updated failed', 'Invalid extension. <b>' . join(', ', $possible_extensions).'</b> only!', FALSE);
	
	if (!empty($_FILES['nfo']['name']) && $_FILES['nfo']['size'] > NFO_SIZE)
    stderr('Updated failed', 'NFO is too big! Max ' . number_format(NFO_SIZE) . ' bytes!');
    
    if (@is_uploaded_file($_FILES['nfo']['tmp_name']) && @filesize($_FILES['nfo']['tmp_name']) > 0)
    $updateset[] = "nfo = " . sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", file_get_contents($_FILES['nfo']['tmp_name'])));
    
}

else
$updateset[] = 'nfo = ""';

// Make sure they do not forget to fill these fields :D
foreach(explode('||',"$descr||$type||$name") as $cannotbeblank) {
	
	if (empty($cannotbeblank))
	stderr('Updated failed', 'Missing form data!');
	
}
// Make sure they do not forget to fill these fields :D

if(isset($_POST['name']) && (($name = $_POST['name']) != $fetch_assoc['name']) && valid_torrent_name($name))
$updateset[] = 'name = ' . sqlesc($name);
$updateset[] = 'search_text = ' . sqlesc(searchfield("$shortfname $dname $torrent"));

if(isset($_POST['descr']) && ($descr = $_POST['descr']) != $fetch_assoc['descr'])
$updateset[] = 'descr = ' . sqlesc($descr);
$updateset[] = 'ori_descr = ' . sqlesc($descr);

if(isset($_POST['type']) && (($category = 0 + $_POST['type']) != $fetch_assoc['category']) && is_valid_id($category))
$updateset[] = 'category = ' . sqlesc($category);

if(($visible = ($_POST['visible'] != ''?'yes':'no')) != $fetch_assoc['visible'])
$updateset[] = 'visible = ' . sqlesc($visible);
/**
 * @Custom Mods
 * @Notes Uncomment the mods you want..
 */
 //==Sticky torrents by tony
if(($sticky = ($_POST['sticky'] != ''?'yes':'no')) != $fetch_assoc['sticky'])
$updateset[] = 'sticky = ' . sqlesc($sticky);
//==Simple nuke/reason mod by BIGBOSS
if(isset($_POST['nuked']) && ($nuked = $_POST['nuked']) != $fetch_assoc['nuked'])
$updateset[] = 'nuked = ' . sqlesc($nuked);
if(isset($_POST['nukereason']) && ($nukereason = $_POST['nukereason']) != $fetch_assoc['nukereason'])
$updateset[] = 'nukereason = ' . sqlesc($nukereason);
//== Poster Mod by johim and EnzoF1
if(isset($_POST['poster']) && (($poster = $_POST['poster']) != $fetch_assoc['poster'] && !empty($poster)))
if (!preg_match("/^http:\/\/[^\s'\"<>]+\.(jpg|gif|png)$/i", $poster))
stderr('Updated failed', 'Poster MUST be in jpg, gif or png format. Make sure you include http:// in the URL.');
$updateset[] = 'poster = ' . sqlesc($poster);
//==Genre Mod without mysql table by Traffic
$genreaction = $_POST['genre'];
if ($genreaction != "keep") {
	
	if (isset($_POST["music"]))
	$genre = implode(",", $_POST['music']);
	elseif (isset($_POST["movie"]))
	$genre = implode(",", $_POST['movie']);
	elseif (isset($_POST["game"]))
	$genre = implode(",", $_POST['game']);
	$updateset[] = "newgenre = " .sqlesc($genre);
	
}
if(($recommended = isset($_POST['recommended']) ? ($_POST['recommended'] == 'yes' ? 'yes' : 'no') : 'no') != $fetch_assoc['recommended'])
$updateset[] = 'recommended = ' . sqlesc($recommended);
//===count stats / free download
if ((isset($_POST['countstats'])) && (($countstats = $_POST['countstats']) != $row['countstats'])){    
if(get_user_class() >= UC_MODERATOR)    
$updateset[] = "countstats = " . sqlesc($countstats);
}
else
$updateset[] = "countstats = 'yes'";
//==vip 
if(($vip = ($_POST['vip'] == 1?'yes':'no')) != $fetch_assoc['vip'])
$updateset[] = 'vip = ' . sqlesc($vip);
//==Requested & Released Type by dokty
if(($scene = ($_POST['scene'] == 'no'?'no':'yes')) != $fetch_assoc['scene'])
$updateset[] = 'scene = ' . sqlesc($scene);

if(($request = ($_POST['request'] == 'no'?'no':'yes')) != $fetch_assoc['request'])
$updateset[] = 'request = ' . sqlesc($request);
/////////////////////imdb mod///////////////////
if(isset($_POST['url']) && (($url = $_POST['url']) != $fetch_assoc['url'] && !empty($url)))
if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url))
stderr('Updated failed', 'Make sure you include http:// in the URL.');
$updateset[] = 'url = ' . sqlesc($url);
/////////////////////utube mod///////////////////
if(isset($_POST['tube']) && (($tube = $_POST['tube']) != $fetch_assoc['tube'] && !empty($tube)))
if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $tube))
stderr('Updated failed', 'Make sure you include http:// in the URL.');
$updateset[] = 'tube = ' . sqlesc($tube);
//==Anonymous mod
if(($anonymous = ($_POST['anonymous'] != ''?'yes':'no')) != $fetch_assoc['anonymous'])
$updateset[] = 'anonymous = ' . sqlesc($anonymous);
/////////////torrent mulplier//////////////
if(get_user_class() >= UC_ADMINISTRATOR)
$multiplicator = (isset($_POST['multiplicator']) ? $_POST['multiplicator'] : '');    
$valid_inputs = array(2,3,4,5);
$multiplicator = (($multiplicator && in_array($multiplicator,$valid_inputs)) ? $multiplicator : 0);
if($multiplicator != $fetch_assoc['multiplicator'])
$updateset[] = 'multiplicator = ' . sqlesc($multiplicator);
mysql_query('UPDATE torrents SET ' . implode(',', $updateset) . ' WHERE id = ' . sqlesc($id)) or sqlerr(__FILE__, __LINE__);

write_log(htmlspecialchars($name) . ' was edited by ' . (($fetch_assoc['anonymous'] == 'yes') ? 'Anonymous' : htmlspecialchars($CURUSER['username'])));

$returl = (isset($_POST['returnto']) ? '&returnto=' . urlencode($_POST['returnto']) : 'details.php?id=' . $id . '&edited=1');
header("Refresh: 0; url=$returl");
?>