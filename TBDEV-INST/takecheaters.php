<?php
include("include/bittorrent.php");
dbconn();
loggedinorreturn();

if (get_user_class() < UC_ADMINISTRATOR)
die();

if($_POST["nowarned"] == "nowarned")
{

if (empty($_POST["desact"]) && empty($_POST["remove"]))
stderr("Error...", "You must select a user.");

if (!empty($_POST["remove"]))
{
mysql_query("DELETE FROM cheaters WHERE id IN (" . implode(", ", $_POST["remove"]) . ")") or sqlerr(__FILE__, __LINE__);
}

if (!empty($_POST["desact"]))
{
mysql_query("UPDATE users SET enabled = 'no' WHERE id IN (" . implode(", ", $_POST["desact"]) . ")") or sqlerr(__FILE__, __LINE__);
}
}

header("Refresh: 0; url=/cheaters.php");

?>