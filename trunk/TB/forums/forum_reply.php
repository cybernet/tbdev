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


  //-------- Action: Reply
if ($action == "reply")
  {
    $topicid = isset($_GET["topicid"]) ? (int)$_GET["topicid"] : 0;

    if (!is_valid_id($topicid))
      header("Location: {$TBDEV['baseurl']}/forums.php");
    
    $q = @mysql_query( "SELECT t.id, f.minclassread, f.minclasswrite 
                        FROM topics t
                        LEFT JOIN forums f ON t.forumid = f.id
                        WHERE t.id = $topicid");

    if( mysql_num_rows($q) != 1 )
      stderr('USER ERROR', 'You didn\'t specify a topic!');
    
    $check = @mysql_fetch_assoc($q);
    
    if( $CURUSER['class'] < $check['minclassread'] OR $CURUSER['class'] < $check['minclasswrite'] )
      stderr('USER ERROR', 'You don\'t have correct permissions for this topic!');
    
    $HTMLOUT = '';

    $HTMLOUT .= begin_main_frame();

    $HTMLOUT .= insert_compose_frame($topicid, false);

    $HTMLOUT .= end_main_frame();

    print stdhead("Post reply") . $HTMLOUT . stdfoot();

    die;
}

  //-------- Action: Quote

if ($action == "quotepost")
	{
		$topicid = isset($_GET["topicid"]) ? (int)$_GET["topicid"] : 0;

		if (!is_valid_id($topicid))
			header("Location: {$TBDEV['baseurl']}/forums.php");

    $q = @mysql_query( "SELECT t.id, f.minclassread, f.minclasswrite 
                        FROM topics t
                        LEFT JOIN forums f ON t.forumid = f.id
                        WHERE t.id = $topicid");

    if( mysql_num_rows($q) != 1 )
      stderr('USER ERROR', 'You didn\'t specify a topic!');
    
    $check = @mysql_fetch_assoc($q);
    
    if( $CURUSER['class'] < $check['minclassread'] OR $CURUSER['class'] < $check['minclasswrite'] )
      stderr('USER ERROR', 'You don\'t have correct permissions for this topic!');
    
    $HTMLOUT = '';

    $HTMLOUT .= begin_main_frame();

    $HTMLOUT .= insert_compose_frame($topicid, false, true);

    $HTMLOUT .= end_main_frame();

    print stdhead("Post reply") .  . stdfoot();

    die;
}

?>