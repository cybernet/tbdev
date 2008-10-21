<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");

$sha=sha1($_SERVER['REMOTE_ADDR']);
if(is_file(''.$dictbreaker.'/'.$sha) && filemtime(''.$dictbreaker.'/'.$sha)>(time()-8)){
@fclose(@fopen(''.$dictbreaker.'/'.$sha,'w'));
die('Minimum 8 seconds between login attempts :)');
}

if (!mkglobal("username:password:captcha"))
	die();
	
session_start();
  if(empty($captcha) || $_SESSION['captcha_id'] != strtoupper($captcha)){
      header('Location: login.php');
      exit();
}

dbconn();

function bark($text) 
{ 
print("<title>Error!</title>"); 
print("<table width='100%' height='100%' style='border: 8px ridge #000000'><tr><td align='center'>"); 
print("<center><h1 style='color: orange;'>Error:</h1><h2>" . htmlspecialchars($text) . "</h2></center>"); 
print("<center><INPUT TYPE='button' VALUE='Back' onClick=\"history.go(-1)\"></center>"); 
print("</td></tr></table>"); 
die; 
} 

failedloginscheck ();

$res = sql_query("SELECT id, passhash, secret, enabled FROM users WHERE username = " . sqlesc($username) . " AND status = 'confirmed'");
$row = mysql_fetch_assoc($res);

if (!$row) {
$ip = sqlesc(getip());
$added = sqlesc(get_date_time());
$a = (@mysql_fetch_row(@mysql_query("select count(*) from loginattempts where ip=$ip"))) or sqlerr(__FILE__, __LINE__);
if ($a[0] == 0)
sql_query("INSERT INTO loginattempts (ip, added, attempts) VALUES ($ip, $added, 1)") or sqlerr(__FILE__, __LINE__);
else
sql_query("UPDATE loginattempts SET attempts = attempts + 1 where ip=$ip") or sqlerr(__FILE__, __LINE__);
@fclose(@fopen(''.$dictbreaker.'/'.sha1($_SERVER['REMOTE_ADDR']),'w'));
bark("Incorrect User Name !");

}

if ($row["passhash"] != md5($row["secret"] . $password . $row["secret"])) {
$ip = sqlesc(getip());
$added = sqlesc(get_date_time());
$a = (@mysql_fetch_row(@sql_query("select count(*) from loginattempts where ip=$ip"))) or sqlerr(__FILE__, __LINE__);
if ($a[0] == 0)
sql_query("INSERT INTO loginattempts (ip, added, attempts) VALUES ($ip, $added, 1)") or sqlerr(__FILE__, __LINE__);
else
sql_query("UPDATE loginattempts SET attempts = attempts + 1 where ip=$ip") or sqlerr(__FILE__, __LINE__);
@fclose(@fopen(''.$dictbreaker.'/'.sha1($_SERVER['REMOTE_ADDR']),'w'));
$to = ($row["id"]);
$msg = "[color=red]SECURITY[/color]\n Account: ID=".$row['id']." Somebody (probably you, ".$username."!) tried to login but failed!". "\nTheir [b]IP ADDRESS [/b] was : ". $ip . " (". @gethostbyaddr($ip) . ")". "\n If this wasn't you please report this event to a staff \n - Thank you.\n";
$sql = "INSERT INTO messages (sender, receiver, msg, added) VALUES('$from', '$to', ". sqlesc($msg).", $added);";
$res = sql_query($sql) or sqlerr(__FILE__, __LINE__);
bark("Incorrect Password !");
}

if ($row["passhash"] != md5($row["secret"] . $password . $row["secret"]))
	bark("Incorrect Passhash - Cheating Are We ?");

if ($row["enabled"] == "no")
	bark("This account has been disabled.");

$passh = md5($row["passhash"].$_SERVER["REMOTE_ADDR"]);
logincookie($row["id"], $passh);

$ip = sqlesc(getip());
sql_query("DELETE FROM loginattempts WHERE ip = $ip");

if (!empty($_POST["returnto"]))
	header("Location: $_POST[returnto]");
else
	header("Location: index.php");
stdfoot();
?>