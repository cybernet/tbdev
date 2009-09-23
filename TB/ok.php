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
require_once "include/bittorrent.php" ;
require_once "include/user_functions.php" ;

dbconn();

    $type = isset($_GET['type']) ? $_GET['type'] : '';

    if ( $type == "signup" && isset($_GET['email']) ) 
    {
      stderr("Signup successful!", "A confirmation email has been sent to the address you specified (" . htmlentities($email, ENT_QUOTES) . "). You need to read and respond to this email before you can use your account. If you don't do this, the new account will be deleted automatically after a few days.");
    }
    elseif ($type == "sysop") 
    {
      $HTMLOUT = stdhead("Sysop Account activation");
      $HTMLOUT .= "<h1>Sysop Account successfully activated!</h1>\n";
      
      if (isset($CURUSER))
      {
        $HTMLOUT .= "<p>Your account has been activated! You have been automatically logged in. You can now continue to the <a href='index.php'><b>main page</b></a> and start using your account.</p>\n";
      }
      else
      {
        $HTMLOUT .= "<p>Your account has been activated! However, it appears that you could not be logged in automatically. A possible reason is that you disabled cookies in your browser. You have to enable cookies to use your account. Please do that and then <a href='login.php'>log in</a> and try again.</p>\n";
      }
      $HTMLOUT .= stdfoot();
      
      print $HTMLOUT;
    }
    elseif ($type == "confirmed") 
    {
      $HTMLOUT .= stdhead("Already confirmed");
      $HTMLOUT .= "<h1>Already confirmed</h1>\n";
      $HTMLOUT .= "<p>This user account has already been confirmed. You can proceed to <a href='login.php'>log in</a> with it.</p>\n";
      $HTMLOUT .= stdfoot();
      print $HTMLOUT;
    }
    elseif ($type == "confirm") 
    {
      if (isset($CURUSER)) 
      {
        $HTMLOUT .= stdhead("Signup confirmation");
        $HTMLOUT .= "<h1>Account successfully confirmed!</h1>\n";
        $HTMLOUT .= "<p>Your account has been activated! You have been automatically logged in. You can now continue to the <a href='{$TBDEV['baseurl']}/index.php'><b>main page</b></a> and start using your account.</p>\n";
        $HTMLOUT .= "<p>Before you start using {$TBDEV['site_name']} we urge you to read the <a href='rules.php'><b>RULES</b></a> and the <a href='faq.php'><b>FAQ</b></a>.</p>\n";
        $HTMLOUT .= stdfoot();
        print $HTMLOUT;
      }
      else 
      {
        $HTMLOUT .= stdhead("Signup confirmation");
        $HTMLOUT .= "<h1>Account successfully confirmed!</h1>\n";
        $HTMLOUT .= "<p>Your account has been activated! However, it appears that you could not be logged in automatically. A possible reason is that you disabled cookies in your browser. You have to enable cookies to use your account. Please do that and then <a href='login.php'>log in</a> and try again.</p>\n";
        $HTMLOUT .= stdfoot();
        print $HTMLOUT;
      }
    }
    else
    {
    stderr('USER ERROR', 'No action to take!');
    }
?>