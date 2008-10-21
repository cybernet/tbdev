<?php
require_once("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
$action = $_POST['action'];
$nfo = safechar($_POST['nfo']);
stdhead("nforipper",true,false);
echo "<h1>NFO ripper</h1>";
$action = $_POST["action"];
$nfo = $_POST["nfo"];
if ($action == "rip")
{
$parsed_nfo="";
for ($i=0;$i<strlen($nfo);$i++)
{
//echo "$nfo[$i] =>".ord($nfo[$i])."<br>";
if ( ((ord($nfo[$i]) >= 32) && (ord($nfo[$i]) <= 127)) || (ord($nfo[$i]) == 228) || (ord($nfo[$i]) == 229) || (ord($nfo[$i]) == 246) || (ord($nfo[$i]) == 197) || (ord($nfo[$i]) == 196) || (ord($nfo[$i]) == 214) )
{
$parsed_nfo.=$nfo[$i];
}
elseif (ord($nfo[$i]) == 13)
{
$parsed_nfo.="<br>";
}
}
$parsed_nfo = split("<br>",$parsed_nfo);
echo "<table class=embedded><tr><td align=center>";
for ($i=0;$i<count($parsed_nfo);$i++) {
if ( (trim($parsed_nfo[$i]) == "") && (trim($parsed_nfo[$i+1]) == "") ) { } else
{
echo trim($parsed_nfo[$i])."<br>";
}
}
echo "</td></tr></table>";
stdfoot();
exit;
}
?>
<form action="nforipper.php" method="post">
<input type="hidden" name="action" value="rip">
<textarea name="nfo" cols=120 rows=25></textarea>
<p><input type=submit value=Rip!>
<?
stdfoot();
?>