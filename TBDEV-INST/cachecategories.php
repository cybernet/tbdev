<?php
include 'include/bittorrent.php';
include 'cacher.php';
dbconn();
$fileinformation = array (
    'table' => 'categories',
    'arrayname' => 'categories',
    'filename' => 'categories'
    );
    
query_wphpfile ($fileinformation);

?>