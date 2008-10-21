<?php
require "include/bittorrent.php";
require_once "include/user_functions.php";
dbconn();
loggedinorreturn();
//parked();//////////uncomment if you have the parked mod installed//////////delete snatched by Manboo - recoded and trimmed by Bigjoos//special thanxs to snuggs and pdq///////
if (get_user_class() < UC_SYSOP)
hacker_dork("Change Log - Nosey Cunt !");
$action = isset($_GET["action"]) ?$_GET["action"] : '';
////////////////Delete snatch entry//////////////////////////////////////////////////////
if ($action == 'delete')
{
$id = (int)$_GET['id'];
$sure = (int)$_GET['sure'];
if (!is_valid_id($id))
stderr("Error", "Invalid ID.");

$hash = md5('the salt to'.$id.'add'.'mu55y');
if (!$sure)
stderr("Confirm Delete","Do you really want to delete this snatch entry? Click\n" .
"<a href=?id=$id&action=delete&sure=1&h=$hash>here</a> if you are sure.", FALSE);
if ($_GET['h'] != $hash)
stderr('Error','what are you doing?');
function deleteid($id) {
global $CURUSER;
sql_query("DELETE FROM snatched WHERE id = $id");
}
deleteid($id);
stdhead("Snatch deleted!");
echo '<h2>Snatched entry deleted!</h2>';
}
stdfoot();
die;
?>