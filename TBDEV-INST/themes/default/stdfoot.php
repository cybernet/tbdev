<?php
////////////new modified function stdfoot designed by Bigjoos for tbdev installer////////////////////
global $SITENAME,$BASEURL,$CURUSER;    
include('include/footer.php');
 if (!defined('REVISION')) {
    if (file_exists('.svn/entries')) {
        $svn = file('.svn/entries');
        if (is_numeric(trim($svn[3]))) {
            $version = $svn[3];
        } else { 
            $version = explode('"', $svn[4]);
            $version = $version[1];    
        }
        $version = trim($version);
        define ('REVISION', trim($version));
        unset ($svn);
        unset ($version);
    } else {
    	
        define ('REVISION', 0); // default if no svn data avilable
    }
}
print("</center><td class=\"cHs\" background=\"pic/right.gif\"></td></tr><td class=\"cHs\" height=\"34\" align=\"left\" valign=\"top\"><img src=\"pic/bottom1.gif\"></th><td class=\"cHs\" height=\"34\" background=\"pic/bottom2.gif\"><center>Powered by <a href=\"http://www.tbdev.net\">TBDev.Net</a></center></td><td class=\"cHs\" height=\"34\" align=\"right\" valign=\"top\"><img src=\"pic/bottom3.gif\"></td></tr></table></body><html>");
print("</td></tr></table></center>\n");
//////////////////////end stdfoot
?>
