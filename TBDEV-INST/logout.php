<?php
require_once("include/bittorrent.php");
dbconn();
logoutcookie();
Header("Location: $BASEURL/");
?>