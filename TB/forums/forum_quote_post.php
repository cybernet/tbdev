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
if ( ! defined( 'IN_TBDEV_FORUM' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}


  //-------- Action: Quote

		$topicid = (int)$_GET["topicid"];

		if (!is_valid_id($topicid))
			stderr("Error", "Invalid topic ID.");

    $HTMLOUT = stdhead("Post reply");

    $HTMLOUT .= begin_main_frame();

    $HTMLOUT .= insert_compose_frame($topicid, false, true);

    $HTMLOUT .= end_main_frame();

    $HTMLOUT .= stdfoot();
    
    print $HTMLOUT;

    die;

  
?>