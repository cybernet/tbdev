<?php
require ("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
dbconn();
maxcoder();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

if (get_user_class() < UC_CODER)
hacker_dork("Manage Db - Nosey Cunt !");

$allowed_ids = array(1);
if (!in_array($CURUSER['id'],$allowed_ids))
	stderr('Error', 'Access Denied!');

stdhead("DataBase Dump");
////////////add your details from secrets.php
$host = "localhost"; // hostname
$login = "xxxxxxxx"; // phpmyadmin login
$password = "xxxxxx"; // phpmyadmin pass
$db = "xxxxxxxxx"; // Database Name
$dir = "backup"; // Dir to save file

echo"<h2>Database Dump</h2><table width=780 cellpading=5 cellspacing=5><tr><td><center><a href=?m=dump><font color=red size=3><b>Save Database</b></font></a></center></td></tr></table>";
if ($_GET['m'] == "dump") {
$conn = mysql_pconnect($host,$login,$password);
if (!$conn){
echo "<font color=\"red\">Error (".mysql_error().") : Incorrect Databe Details<br> Recheck Your Submitted Info And Try Again.</font>";
}
elseif ($conn){
mysql_select_db($db);
if ($dir == "/" || $dir == "") $dir = "";
$date = date("d-m-Y_H-i");
$file = $db.'_'.$date.'.sql';
$real_path = realpath ("./bak.php");
$real = substr($real_path,0,-8);
if ($dir == ""){
$fp = fopen($file,"w");
}
elseif ($dir !== ""){
if (is_dir($dir) == false){
mkdir("$real".$dir."/",0777);
}
$fp = fopen($dir."/".$file,"w");
}
if ($fp) echo "<font size=4 color=green>Succesful! <img src=pic/smilies/thumbsup.gif></font><br>";
$tables = array();
$q = mysql_query("SHOW TABLES");
$num = mysql_num_rows($q);
echo "Tables Saved : <b>".$num."</b><br>";
$all = 0;
while ($res = mysql_fetch_array($q)){
$status = 0;
if (!empty($tbls)) {
foreach($tbls AS $table){
$exclude = preg_match("/^\^/", $table) ? true : false;
if (!$exclude) {
if (preg_match("/^{$table}$/i", $row[0])) {
$status = 1;
}
$all = 1;
}
if ($exclude && preg_match("/{$table}$/i", $row[0])) {
$status = -1;
}
}
}
else {
$status = 1;
}
if ($status >= $all) {
$tables[] = $res[0];
}
}
$qu = mysql_query("SHOW TABLE STATUS");
$tabinfo = array();
$tabinfo[0] = 0;
$info = '';

for ($i = 0;$i < $num;$i++){
$result = mysql_query("SHOW CREATE TABLE {$tables[$i]}");
$tab = mysql_fetch_array($result);
$tab = preg_replace('/(default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP|DEFAULT CHARSET=\w+|character set \w+|collate \w+)/i', '/*!40101 \\1 */', $tab);
fwrite($fp, "DROP TABLE IF EXISTS {$tables[$i]};\n{$tab[1]};\n\n");
}
for ($i = 0;$i < $num;$i++){
if (empty($dir)){
$f = fopen($file,"a");
}
elseif (!empty($dir)){
$f = fopen($dir."/".$file,"a");
}
$result = mysql_query("SHOW COLUMNS FROM {$tables[$i]}");
fwrite($f,"INSERT INTO `{$tables[$i]}` VALUES \n");
$limit = 1000;
$from = 0;
$r123 = mysql_query("select * from $tables[$i]");
$ii = $i;
$ii++;
print("<b>$ii</b> $tables[$i]<br/>");
while ($row = mysql_fetch_array($r123)) {
fwrite($f, "INSERT INTO `{$tables[$i]}` VALUES (");
for ($ib = 0; $ib<mysql_num_fields($r123); $ib++) {
fwrite($f, ($ib == 0? "":", ")."'$row[$ib]'");
}
fwrite($f, ");\n");
}
}
echo "<br><font size=2 color=green>Database <b>".$db."</b> has been saved in <?php echo $BASEURL?> ".$dir."/".$file."</b></font><br><br><br><br><br>";

if ($tables == false){
echo "There's been an error...";
}
}
}
stdfoot();
?>