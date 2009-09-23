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

require_once "include/user_functions.php";

    $HTMLOUT = '';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
      $ip = isset($_POST["ip"]) ? $_POST["ip"] : false;
    }
    else
    {
      $ip = isset($_GET["ip"]) ? $_GET["ip"] : false;
    }
    
    if ($ip)
    {
      $nip = ip2long($ip);
      if ($nip == -1)
        stderr("Error", "Bad IP.");
      
      $res = mysql_query("SELECT * FROM bans WHERE $nip >= first AND $nip <= last") or sqlerr(__FILE__, __LINE__);
      
      if (mysql_num_rows($res) == 0)
      {
        stderr("Result", "The IP address <b>".htmlentities($ip, ENT_QUOTES)."</b> is not banned.");
      }
      else
      {
        $HTMLOUT .= "<table class='main' border='0' cellspacing='0' cellpadding='5'>
        <tr>
          <td class='colhead'>First</td>
          <td class='colhead'>Last</td>
          <td class='colhead'>Comment</td>
        </tr>\n";
        
        while ($arr = mysql_fetch_assoc($res))
        {
          $first = long2ip($arr["first"]);
          $last = long2ip($arr["last"]);
          $comment = htmlspecialchars($arr["comment"]);
          $HTMLOUT .= "<tr><td>$first</td><td>$last</td><td>$comment</td></tr>\n";
        }
        
        $HTMLOUT .= "</table>\n";
        
        stderr("Result", "<table border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded' style='padding-right: 5px'><img src='{$TBDEV['pic_base_url']}smilies/excl.gif' alt='' /></td><td class='embedded'>The IP address <b>$ip</b> is banned:</td></tr></table><p>$HTMLOUT</p>");
      }
    }
    

    $HTMLOUT .= "
    <h1>Test IP address</h1>
    <form method='post' action='admin.php?action=testip'>
    <table border='1' cellspacing='0' cellpadding='5'>
    <tr><td class='rowhead'>IP address</td><td><input type='text' name='ip' /></td></tr>
    <tr><td colspan='2' align='center'><input type='submit' class='btn' value='OK' /></td></tr>
    </table>
    </form>";


    print stdhead() . $HTMLOUT . stdfoot();
?>