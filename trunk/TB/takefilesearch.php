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
ob_start('ob_gzhandler');

require_once 'include/bittorrent.php';
require_once 'include/user_functions.php';
//require_once "include/torrenttable_functions.php";
//require_once "include/pager_functions.php";

dbconn(false);

loggedinorreturn();

    $lang = array_merge( load_language('global'), load_language('takefilesearch') );
    
    if(isset($_POST['search']) && !empty($_POST['search'])) {
      
      $cleansearchstr = sqlesc(searchfield($_POST['search']));
      print $cleansearchstr;
      }
      else
      stderr($lang['tfilesearch_oops'], $lang['tfilesearch_nuffin']);


    $query = mysql_query("SELECT id, filename, MATCH (filename)
                AGAINST (".$cleansearchstr.") AS score
                FROM files WHERE MATCH (filename)
                AGAINST (".$cleansearchstr." IN BOOLEAN MODE)");

    if(mysql_num_rows($query) == 0)
      stderr($lang['tfilesearch_error'], $lang['tfilesearch_nothing']);
      
      while($row = mysql_fetch_assoc($query)) {
      
        print '<pre>'.$row['id']."-".htmlspecialchars($row['filename'])."-".$row['score'].'</pre>';
      }
?>