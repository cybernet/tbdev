<?php
include ('include/bittorrent.php');
require_once ("cacher.php");
dbconn(true);
loggedinorreturn();
if (get_user_class() < UC_SYSOP)
hacker_dork("Cache Sheets - Nosey Cunt !");


$fileinformation = array (
    'table' => 'stylesheets',
    'arrayname' => 'stylesheets',
    'filename' => 'stylesheets'
    );
    
query_wphpfile ($fileinformation);

?>