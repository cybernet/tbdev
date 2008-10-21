<?php
include './include/secrets.php';
include './include/bittorrent.php';
//include '../include/config.php';

$errors = array();
$messages = array();


$link = @mysql_connect($mysql_host, $mysql_user, $mysql_pass);
if( !$link )
{
$errors[] = "Could not connect to mysql, check your secrets.php (".mysql_error().")";
}
else
{
$messages[] = "Database connection ok";

$db = mysql_select_db($mysql_db);
if(!$db)
{
$errors[] = "Could not select database(".$mysql_db.") (".mysql_error().")";
}
else
{
$messages[] = "Database selection ok";

$stylesheets = mysql_result( mysql_query('SELECT COUNT(*) FROM stylesheets'), 0 );
if( $stylesheets == 0 )
{
$errors[] = "You don't have any stylesheets in your database, make sure you have at least stylesheet available";
}
else
{
$messages[] = "Stylesheets are available in the database :)";
}
}

}

if( is_dir($CACHE) )
{

if( !is_writable($CACHE) )
$errors[] = "Could not write to ".$CACHE.", make sure it is chmodded 777";
else
{
$messages[] = "Cache dir exists and is writable :)";
}
}
else
{
$errors[] = "Your cachedir doesn't exist (".$CACHE.")";
}

if( is_dir($torrent_dir) )
{

if( !is_writable($torrent_dir) )
$errors[] = "Could not write to ".$torrent_dir.", make sure it is chmodded 777";
else
{
$messages[] = "Torrent dir exists and is writable :)";
}
}
else
{
$errors[] = "Your torrent dir doesn't exist (".$torrent_dir.")";
}


if(get_magic_quotes_gpc() === 1 || get_magic_quotes_runtime() === 1)
{
$errors[] = "Disable magic_quotes in your php.ini";
}
else
{
$messages[] = "Magic quotes are off \o/";
}


if( count($errors) > 0 )
{
echo '<h1>Errors</h1>';
echo '<p style="color: red">'.implode('<br />', $errors).'</p>';
}

if(count($messages) > 0)
{
echo '<h1>Success</h1>';
echo '<p style="color: green">'.implode('<br />', $messages).'</p>';
}
?>