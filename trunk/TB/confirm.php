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

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $md5 = isset($_GET['secret']) ? $_GET['secret'] : '';

    if (!is_valid_id($id))
      stderr('USER ERROR', 'Sorry, you have an invalid id');
    
    if (! preg_match( "/^(?:[\d\w]){32}$/", $md5 ) )
		{
			stderr('USER ERROR', 'Sorry, you have an invalid key');
		}
		
    dbconn();


    $res = @mysql_query("SELECT passhash, editsecret, status FROM users WHERE id = $id");
    $row = @mysql_fetch_assoc($res);

    if (!$row)
      stderr('USER ERROR', 'Sorry, you have an invalid id');

    if ($row['status'] != 'pending') 
    {
      header("Refresh: 0; url={$TBDEV['baseurl']}/ok.php?type=confirmed");
      exit();
    }

    $sec = hash_pad($row['editsecret']);
    if ($md5 != md5($sec))
      stderr('USER ERROR', 'Sorry, Cannot confirm you');

    @mysql_query("UPDATE users SET status='confirmed', editsecret='' WHERE id=$id AND status='pending'");

    if (!mysql_affected_rows())
      stderr('USER ERROR', 'Sorry, Cannot confirm you');

    logincookie($id, $row['passhash']);

    header("Refresh: 0; url={$TBDEV['baseurl']}/ok.php?type=confirm");

?>