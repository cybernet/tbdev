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
require_once 'include/bittorrent.php';

    if (!mkglobal('username:password:captcha'))
      die();
      
    session_start();
      if(empty($captcha) || $_SESSION['captcha_id'] != strtoupper($captcha)){
          header('Location: login.php');
          exit();
    }

    dbconn();
    
    $lang = load_language('takelogin');
    
    function bark($text = 'Username or password incorrect')
    {
      global $lang;
      stderr($lang['tlogin_failed'], $text);
    }

    $res = mysql_query("SELECT id, passhash, secret, enabled FROM users WHERE username = " . sqlesc($username) . " AND status = 'confirmed'");
    $row = mysql_fetch_assoc($res);

    if (!$row)
      bark();

    if ($row['passhash'] != md5($row['secret'] . $password . $row['secret']))
      bark();

    if ($row['enabled'] == 'no')
      bark($lang['tlogin_disabled']);

    logincookie($row['id'], $row['passhash']);

//$returnto = str_replace('&amp;', '&', htmlspecialchars($_POST['returnto']));
//$returnto = $_POST['returnto'];
    //if (!empty($returnto))
      //header("Location: ".$returnto);
    //else
      header("Location: {$TBDEV['baseurl']}/my.php");

?>