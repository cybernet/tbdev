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
if ( ! defined( 'IN_TBDEV_ADMIN' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}

require_once "include/html_functions.php";
require_once "include/user_functions.php";




    $HTMLOUT = '';

    $HTMLOUT .= "<br />

    <br />
		<table width='75%' cellpadding='10px'>
		<tr><td class='colhead'>Staff Tools</td></tr>
		<!-- row 1 -->
		<tr><td>
		
			
			<span class='btn'><a href='admin.php?action=bans'>Bans</a></span>
			
			<span class='btn'><a href='admin.php?action=adduser'>Add New User</a></span>
			
			<span class='btn'><a href='admin.php?action=log'>View Log</a></span>
			
			<span class='btn'><a href='admin.php?action=docleanup'>Manual CleanUp</a></span>
			
			<span class='btn'><a href='users.php'>Users List</a></span>
			
			</td></tr>
			<!-- row 2 -->
			<tr><td>
			
			<span class='btn'><a href='tags.php'>BBCode Tags</a></span>
			

			<span class='btn'><a href='smilies.php'>Smilies/Emoticons</a></span>
			
			<span class='btn'><a href='admin.php?action=delacct'>Delete Account</a></span>
			

			<span class='btn'><a href='admin.php?action=stats'>Tracker Statistics</a></span>
			
			</td></tr>
			<!-- roow 3 -->
			<tr><td>
			
			<span class='btn'><a href='admin.php?action=testip'>Test IP</a></span>
			

			<span class='btn'><a href='admin.php?action=usersearch'>User Search</a></span>
			

			<span class='btn'><a href='admin.php?action=mysql_overview'>MySQL Overview</a></span>
			

			<span class='btn'><a href='admin.php?action=mysql_stats'>MySQL Statistics</a></span>
			
			
			</td></tr>
			<!-- row 4 -->
			<tr><td>
			
			<span class='btn'><a href='forummanage.php'>Manage Forum</a></span>
			

			<span class='btn'><a href='admin.php?action=categories'>Categories</a></span>
			
			<span class='btn'><a href='admin.php?action=newusers'>Newest Users</a></span>
			
			<span class='btn'><a href='admin.php?action=resetpassword'>Reset Password </a></span>
			
			</td></tr>
			<!-- row 5 -->
			<tr><td>
			
			<span class='btn'><a href='reputation_ad.php'>Rep System</a></span>
			
			<span class='btn'><a href='reputation_settings.php'>Rep Settings</a></span>
			
			<span class='btn'><a href='admin.php?action=news'>Add/Edit News</a></span>
			
			
		</td></tr></table>";
 

    print stdhead("Staff") . $HTMLOUT . stdfoot();

?>