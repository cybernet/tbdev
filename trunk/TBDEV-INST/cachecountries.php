<?php
include ('include/bittorrent.php');
include ('cacher.php');
dbconn(true);

$fileinformation = array (
    'table' => 'countries',
    'arrayname' => 'countries',
    'filename' => 'countries'
    );
    
query_wphpfile ($fileinformation);

?>