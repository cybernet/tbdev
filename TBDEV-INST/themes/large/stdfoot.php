<?php
global $SITENAME,$BASEURL,$CURUSER,$queries, $query_stat;    
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
            print("[".safechar(($key+1))."] => <b>".($value["seconds"] > 0.01 ? "<font color=\"red\" title=\"You should optimize this query.\">".safechar($value["seconds"])."</font>" : "<font color=\"green\" title=\"This query doesn't need optimized.\">".safechar($value["seconds"])."</font>" )."</b> [$value[query]]<br />\n");
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
print("</center><td class=\"cHs\" background=\"pic/right.gif\"></td></tr><td class=\"cHs\" height=\"34\" align=\"left\" valign=\"top\"><img src=\"pic/bottom1.gif\"></th><td class=\"cHs\" height=\"34\" background=\"pic/bottom2.gif\"><center><font color=blue>Powered by</font> <a href=\"http://www.tbdev.net\"><font color=blue>TBDev.Net</font></a></center></td><td class=\"cHs\" height=\"34\" align=\"right\" valign=\"top\"><img src=\"pic/bottom3.gif\"></td></tr></table></body><html>");
print("</td></tr></table></center>\n");
?>
