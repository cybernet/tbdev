<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
if(get_user_class() < UC_SYSOP)
hacker_dork("Cleansnatch file");

if( !function_exists('memory_get_usage') )
{
function memory_get_usage()
{
//If its Windows
//Tested on Win XP Pro SP2. Should work on Win 2003 Server too
//Doesn't work for 2000
//If you need it to work for 2000 look at http://us2.php.net/manual/en/function.memo...usage.php#54642
if ( substr(PHP_OS,0,3) == 'WIN')
{
if ( substr( PHP_OS, 0, 3 ) == 'WIN' )
{
$output = array();
exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output );

return preg_replace( '/[\D]/', '', $output[5] ) * 1024;
}
}else
{
//We now assume the OS is UNIX
//Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
//This should work on most UNIX systems
$pid = getmypid();
exec("ps -eo%mem,rss,pid | grep $pid", $output);
$output = explode(" ", $output[0]);
//rss is given in 1024 byte units
return $output[1] * 1024;
}
}
}

stdhead("Cleanup snatchlist");
begin_main_frame();
begin_frame("Cleaned snatchlist", false);

/* Cleanup snatchlist by x0r @ TBDEV */

$sres = mysql_query("SELECT DISTINCT torrentid FROM snatched") or sqlerr(__FILE__, __LINE__);
while($sarr = mysql_fetch_assoc($sres))
{
$ures = mysql_query("SELECT id FROM torrents WHERE id = $sarr[torrentid]") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($ures) == 0)
mysql_query("DELETE FROM snatched WHERE torrentid = $sarr[torrentid]") or sqlerr(__FILE__, __LINE__);
@mysql_free_result($ures);

}
@mysql_free_result($sres);
write_log("Snatched Table Cleaned by " . $CURUSER["username"]);


print("Memory usage:".memory_get_usage()."<br /><br />");

end_frame();
end_main_frame();
stdfoot();
?>