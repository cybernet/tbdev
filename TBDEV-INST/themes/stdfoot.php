<?php
global $queries, $query_stat, $SITENAME;
  if (!defined('REVISION'))
    {
    if (file_exists('.svn/entries'))
    {
        $svn = file('.svn/entries');
        if (is_numeric(trim($svn[3])))
            $version = $svn[3];
         else
        {
            $version = explode('"', $svn[4]);
            $version = $version[1];    
        }
        $version = trim($version);
        define ('REVISION', trim($version));
        unset ($svn);
        unset ($version);
    }
    else     
        define ('REVISION', 0);
}
?>
<div align="center"><p align="center">Page generated in <?php echo number_format(array_sum(explode(' ',microtime())) - $GLOBALS['stime'],5)?> seconds using <?php echo $queries?> queries</p></div>
<? 
if (get_user_class() >= UC_SYSOP) { ?><h2><font size=1pt><?php echo $SITENAME?> Query's</font></h2>
<? } ?>
<div id="div6" style="display: none;">
<?
if (get_user_class() >= UC_SYSOP) {
if (DEBUG_MODE && $query_stat) {
        foreach ($query_stat as $key => $value) {
            print("[".($key+1)."] => <b>".($value["seconds"] > 0.01 ? "<font color=\"red\" title=\"I suggest you should optimize this query.\">".$value["seconds"]."</font>" : "<font color=\"green\" title=\"This query doesn't need's optimization.\">".$value["seconds"]."</font>" )."</b> [$value[query]]<br />\n");
        }
    }
   }
?>
</div>
<?
if (get_user_class() >= UC_SYSOP) {
?>
<center><b><a href="#querywatch" onclick="closeit('div6');">
<font color="red">[ Hide</font></a></b> | <b>
<a href="#querywatch" onclick="showit('div6');"><font color="red">Show ]</font></a></b></center>
<?
}
?>
          <p align="center"><a href=""><?php echo $SITENAME;?></a> &copy;
          <?php echo gmdate("Y");?> Powered by
               <a href="http://tbdev.net">
           <?php echo TBVERSION;?></a> sourcecode
               <a href="https://tbdevnet.svn.sourceforge.net/svnroot/tbdevnet/trunk/">Revision
           <?php echo REVISION;?></a></p>
  <?php
print("</center><td class=\"cHs\" background=\"pic/right.gif\"></td></tr><td class=\"cHs\" height=\"34\" align=\"left\" valign=\"top\"><img src=\"pic/bottom1.gif\"></th><td class=\"cHs\" height=\"34\" background=\"pic/bottom2.gif\"></a><center>Powered by <a href=\"http://www.tbdev.net\">TBDev.Net</a></center></td><td class=\"cHs\" height=\"34\" align=\"right\" valign=\"top\"><img src=\"pic/bottom3.gif\"></td></tr></table></body><html>");
print("</td></tr></table></center>\n");
//////////////////////end stdfoot
?>
