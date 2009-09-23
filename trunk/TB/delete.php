<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2009 TBDev.Net
|   http://www.tbdev.net
|   =============================================
|   svn: http://sourceforge.net/projects/tbdevnet/
|   Licence Info: GPL
+------------------------------------------------
|   $Date$
|   $Revision$
|   $Author$
|   $URL$
+------------------------------------------------
*/
require_once "include/bittorrent.php";
require_once "include/user_functions.php";


if (!mkglobal("id"))
	stderr("Delete failed!", "missing form data");

$id = 0 + $id;
if (!is_valid_id($id))
	stderr("Delete failed!", "missing form data");
	
dbconn();

loggedinorreturn();

function deletetorrent($id) {
    global $TBDEV;
    mysql_query("DELETE FROM torrents WHERE id = $id");
    foreach(explode(".","peers.files.comments.ratings") as $x)
        mysql_query("DELETE FROM $x WHERE torrent = $id");
    unlink("{$TBDEV['torrent_dir']}/$id.torrent");
}

$res = mysql_query("SELECT name,owner,seeders FROM torrents WHERE id = $id");
$row = mysql_fetch_assoc($res);
if (!$row)
	stderr("Delete failed!", "Torrent does not exist");

if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
	stderr("Delete failed!", "You're not the owner! How did that happen?\n");

$rt = 0 + $_POST["reasontype"];

if (!is_int($rt) || $rt < 1 || $rt > 5)
	bark("Invalid reason");

//$r = $_POST["r"]; // whats this
$reason = $_POST["reason"];

if ($rt == 1)
	$reasonstr = "Dead: 0 seeders, 0 leechers = 0 peers total";
elseif ($rt == 2)
	$reasonstr = "Dupe" . ($reason[0] ? (": " . trim($reason[0])) : "!");
elseif ($rt == 3)
	$reasonstr = "Nuked" . ($reason[1] ? (": " . trim($reason[1])) : "!");
elseif ($rt == 4)
{
	if (!$reason[2])
		stderr("Delete failed!", "Please describe the violated rule.");
  $reasonstr = $TBDEV['site_name']." rules broken: " . trim($reason[2]);
}
else
{
	if (!$reason[3])
		stderr("Delete failed!", "Please enter the reason for deleting this torrent.");
  $reasonstr = trim($reason[3]);
}

    deletetorrent($id);

    write_log("Torrent $id ({$row['name']}) was deleted by {$CURUSER['username']} ($reasonstr)\n");



    if (isset($_POST["returnto"]))
      $ret = "<a href='" . htmlspecialchars($_POST["returnto"]) . "'>Go back to whence you came</a>";
    else
      $ret = "<a href='{$TBDEV['baseurl']}'>Back to index</a>";

    $HTMLOUT = '';
    $HTMLOUT .= "<h2>Torrent deleted!</h2>
    <p><$ret</p>";


    print stdhead("Torrent deleted!") . $HTMLOUT . stdfoot();

?>