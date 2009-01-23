<?php
require_once("include/bittorrent.php");
require_once "include/user_functions.php";
require_once "include/bbcode_functions.php";
dbconn();
maxcoder();	

if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
stdhead("Run Manual Cleanup");
if (get_user_class() < UC_SYSOP)
hacker_dork("DoCleanUp - TuT TuT... Cheating are we ??");
docleanup();
print("<html><head><link rel=\"stylesheet\" href=\"/themes/default/default.css\" type=\"text/css\" media=\"screen\" /></head><body>\n");
print("<font color=white>Site Manual Cleanup Complete</font>");
stdfoot();
?>