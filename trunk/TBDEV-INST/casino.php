<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");

dbconn(false);
loggedinorreturn();
parked();
if (get_user_class() < UC_POWER_USER)
{
stdmsg("Sorry...", "You are not authorized to play this game.");
stdfoot();
exit;
}

if ($CURUSER["casinoban"] == 'yes')
{
stdhead();
stdmsg("Sorry...", "You are Banned from playing in the Casino. (See site staff for the reason why !");
stdfoot();
exit;
}
function tr2()
{
$a = func_get_args(tr2);
for($i=0;$i< (func_num_args(tr2)/2)+1;$i++)
$row .= "<td ".$a[$i].">".$a[++$i]."</td>";
echo "<tr>".$row."</tr>";
}

//////////////////////////////////config
//////who is able to play just
//$player = UC_USER;
$player = UC_POWER_USER;
//$player = UC_VIP;
//$player = UC_UPLOADER;
//$player = UC_MODERATOR;
//$player = UC_ADMINISTRATOR;
//$player = UC_SYSOP;
//$player = UC_OWNER;
//$player = UC_CODER;
$mb_basic=1024*1024;
$max_download_user = $mb_basic*1024*255; //// 25 GB
$max_download_global = $mb_basic*$mb_basic*233.5; //// 2.5 TB :-)
$required_ratio = 0.5; ///i think you know this


//////////////////this is the funny part
$user_everytimewin_mb = $mb_basic * 20; ////// means users that wins under 70 mb get a cheat_value of 0 -> win every time
$cheat_value = 8; // higher value -> less winner
$cheat_breakpoint = 10; ////very important value -> if (win MB > max_download_global/cheat_breakpoint)
$cheat_value_max = 2; ////// then cheat_value = cheat_value_max -->> i hope you know what i mean. ps: must be higher as cheat_value.
$cheat_ratio_user = .4; ///if casino_ratio_user > cheat_ratio_user -> $cheat_value = rand($cheat_value,$cheat_value_max)
$cheat_ratio_global = .4; /// same as user just global
$win_amount = 3; // how much do the player win in the first game eg. bet 300, win_amount=3 ---->>> 300*3= 900 win
$win_amount_on_number = 6; // same as win_amount for the number game
$show_real_chance = false; ///shows the user the real chance true or false
$bet_value1 = $mb_basic * 200; ///this is in MB but you can also choose gb or tb :-)
$bet_value2 = $mb_basic * 500;
$bet_value3 = $mb_basic * 1000;
$bet_value4 = $mb_basic * 2500;
$bet_value5 = $mb_basic * 5000;
$bet_value6 = $mb_basic * 10240;
$bet_value7 = $mb_basic * 20480;

////////////config game 3

$minclass = $player; //Lowest class allowed to play
$maxusrbet = '4'; //Amount of bets to allow per person
$maxtotbet = 30; //Amount of total open bets allowed
$alwdebt = 'n'; //Allow users to get into debt
$writelog='y'; //Write a record to the log
$delold='n'; //Clear bets once finished? (cleanup.php will if value isn't 'y')
$sendfrom='0'; //The id of the user which notification PM's are noted as sent from
$casino=$HTTP_SERVER_VARS['PHP_SELF']; //Name of file
//End of Config

// Reset user gamble stats!
$hours = 2; // Hours to wait after using all tries, until they will be restarted
$dt = sqlesc(get_date_time(gmtime() - ($hours * 3600)));
$res = mysql_query("SELECT userid, trys, date, enableplay FROM casino WHERE date < $dt AND trys >= '51' AND enableplay = 'yes'");
while ($arr = mysql_fetch_assoc($res))
{
mysql_query("UPDATE casino SET trys='0' WHERE userid='$arr[userid]'") or sqlerr(__FILE__, __LINE__);
}

//////////////////////////////////config end

if (get_user_class() < $player)
stderr("Sorry ".$CURUSER["username"], "The MODERATOR do not allow your class (".$player.") to play casino");

$query ="select * from casino where userid = '".$CURUSER["id"]."'";
$result = mysql_query($query) or die (mysql_error());
if(mysql_affected_rows()!=1)
{
mysql_query("INSERT INTO casino (userid, win, lost, trys, date, started) VALUES(" . $CURUSER["id"] . ",0,0,0, '" . get_date_time() . "1')") or mysql_error();
//stderr("Hi ".$CURUSER["username"], "This is the first time you try to play at the Casino please refresh the site");
$result = mysql_query($query); ///query another time to get the new user, if the stderr is uncomment
}

$row = mysql_fetch_array($result);
$user_win = $row["win"];
$user_lost = $row["lost"];
$user_trys = $row["trys"];
$user_date = $row["date"];
$user_deposit = $row["deposit"];
$user_enableplay = $row["enableplay"];

if($user_enableplay=="no")
stderr("Sorry ".$CURUSER["username"],"your banned from casino");



if(($user_win - $user_lost) > $max_download_user)
stderr("Sorry ".$CURUSER["username"],"you have reached the max download for a single user");

if ($CURUSER["downloaded"] > 0)
$ratio = number_format($CURUSER["uploaded"] / $CURUSER["downloaded"], 2);
else
if ($CURUSER["uploaded"] > 0)
$ratio = 999;
else
$ratio = 0;
if($ratio < $required_ratio)
stderr("Sorry ".$CURUSER["username"],"your ratio is under ".$required_ratio);


$global_down2 = mysql_query(" select (sum(win)-sum(lost)) as globaldown,(sum(deposit)) as globaldeposit, sum(win) as win, sum(lost) as lost from casino") or die (mysql_error());
$row = mysql_fetch_array($global_down2);
$global_down = $row["globaldown"];
$global_win = $row["win"];
$global_lost = $row["lost"];
$global_deposit = $row["globaldeposit"];

if ($user_win > 0)
$casino_ratio_user = number_format($user_lost / $user_win, 2);
else
if ($user_lost > 0)
$casino_ratio_user = 999;
else
$casino_ratio_user = 0.00;

if ($global_win > 0)
$casino_ratio_global = number_format($global_lost / $global_win, 2);
else
if ($global_lost > 0)
$casino_ratio_global = 999;
else
$casino_ratio_global = 0.00;

//get users that bet the first time or win very less :-)
if($user_win < $user_everytimewin_mb)
$cheat_value = 8;
else
{
//i think this is a god idea GLOBAL
if($global_down > ($max_download_global / $cheat_breakpoint))
$cheat_value = $cheat_value_max;
if($casino_ratio_global < $cheat_ratio_global)
$cheat_value = rand($cheat_value,$cheat_value_max);

//i think this is a god idea for EACH USER
if(($user_win - $user_lost) > ($max_download_user / $cheat_breakpoint))
$cheat_value = $cheat_value_max;
if($casino_ratio_user < $cheat_ratio_user)
$cheat_value = rand($cheat_value,$cheat_value_max);
}

if($global_down > $max_download_global)
stderr("Sorry ".$CURUSER["username"],"but global max win is above ".mksize($max_download_global));


////////////////////////////////////////////////////////////////////////////////////////////////

if((($_POST["color"]=="red"||$_POST["color"]=="black")||($_POST["number"]=="1"||$_POST["number"]=="2"||$_POST["number"]=="3"||$_POST["number"]=="4"||$_POST["number"]=="5"||$_POST["number"]=="6"))&&($_POST["betmb"]==$bet_value1||$_POST["betmb"]==$bet_value2||$_POST["betmb"]==$bet_value3||$_POST["betmb"]==$bet_value4||$_POST["betmb"]==$bet_value5||$_POST["betmb"]==$bet_value6||$_POST["betmb"]==$bet_value7))
{
$betmb=$_POST["betmb"];
if($_POST["number"])
{
$win_amount = $win_amount_on_number;
$cheat_value = $cheat_value+5;
$winner_was = $_POST["number"];
}
else
$winner_was = $_POST["color"];

$win = $win_amount*$betmb;

if($CURUSER["uploaded"] < $betmb)
stderr("Sorry ".$CURUSER["username"],"but you have not uploaded ".mksize($betmb));

stdhead();


if(rand(0,$cheat_value)==$cheat_value)
{
stdmsg("Yes ".$winner_was." is the result ".$CURUSER["username"],"you got it and win ".mksize($win));
mysql_query("UPDATE users SET uploaded = uploaded + ".$win." WHERE id=".$CURUSER["id"]) or sqlerr();
mysql_query("UPDATE casino SET date = '".get_date_time()."', trys = trys + 1, win = win + ".$win." WHERE userid=".$CURUSER["id"]) or sqlerr();
}
else
{
if($_POST["number"])
{
do
{
$fake_winner = rand(1,6);
}
while($_POST["number"]==$fake_winner);
}
else
{
if($_POST["color"]=="black")
$fake_winner= "red";
else
$fake_winner= "black";
}


stdmsg("Sorry ".$fake_winner." is winner and not ".$winner_was.", ".$CURUSER["username"],"you lost ".mksize($betmb));
mysql_query("UPDATE users SET uploaded = uploaded - ".$betmb." WHERE id=".$CURUSER["id"]) or sqlerr();
mysql_query("UPDATE casino SET date = '".get_date_time()."', trys = trys + 1 ,lost = lost + ".$betmb." WHERE userid=".$CURUSER["id"]) or sqlerr();
}

}
else
{




//////////////////////////////////////////////////////////////////////////////////////// game 3

///////////////////notice game 3 is NOT counted with the trys in casino !!!!!!!!!

//get user stats
$betsp = mysql_query("SELECT challenged FROM casino_bets WHERE proposed = '".$CURUSER['username']."'");
$openbet = 0;
while($tbet2 = mysql_fetch_assoc($betsp))
{
if($tbet2[challenged]=='empty')
$openbet++;
}

//Convert bet amount into bits

if(isset($_POST['unit'])){
if ($_POST["unit"] =='1')
$nobits = $amnt*$mb_basic;
else
$nobits = $amnt*$mb_basic*1024;
}

if($CURUSER[uploaded] ==0|| $CURUSER[downloaded]==0)
$ratio='0';
else
$ratio = number_format(($CURUSER['uploaded']-$nobits)/$CURUSER['downloaded'],2);

$time = strtotime("now");
$time = date('Y-n-j G:i:s',$time);
$goback = "<a href=$casino>Go back</a>";

//Take Bet

if (isset($_GET["takebet"]))
{
$betid=$_GET["takebet"];
$random = rand(0,1);
$loc = mysql_query("SELECT * FROM casino_bets WHERE id = '$betid'");
$tbet= mysql_fetch_assoc($loc);
$nogb = mksize($tbet[amount]);

if($CURUSER['id']==$tbet['userid'])
stderr("Sorry","You want to bet yourself?&nbsp;&nbsp;&nbsp;$goback");
elseif($tbet['challenged']!="empty")
stderr("Sorry","Someone has already taken that bet!&nbsp;&nbsp;&nbsp;$goback");

if($CURUSER[uploaded] < $tbet['amount'])
{
$debt = $tbet['amount']-$CURUSER['uploaded'];
$newup = $CURUSER['uploaded']-$debt;
}

if(isset($debt) && $alwdebt !='y')
stderr("Sorry","<h2>You are ".mksize(($nobits-$CURUSER[uploaded]))." short of making that bet!</h2>&nbsp;&nbsp;&nbsp;$goback");

if($random==1)
{

mysql_query("UPDATE users SET uploaded = uploaded+".$tbet['amount']." WHERE id = '".$CURUSER['id']."'") or sqlerr();
mysql_query("UPDATE casino SET deposit = deposit-".$tbet['amount']." WHERE userid = '".$tbet['userid']."'") or sqlerr();
if(mysql_affected_rows()==0)
mysql_query("INSERT INTO casino (userid, date, deposit) VALUES (".$tbet['userid'].", '$time', '-".$tbet['amount']."')") or sqlerr();

mysql_query("UPDATE casino_bets SET challenged = '".$CURUSER['username']."' WHERE id = $betid") or sqlerr();
mysql_query("INSERT INTO messages (id, sender, receiver, added, msg, unread, poster) VALUES ('', '$sendfrom', '".$tbet['userid']."', '$time','You lost a bet! ".$CURUSER[username]." just won $nogb of your upload credit!' , 'yes', '$sendfrom')") or sqlerr();

if($delold=='y')
mysql_query("DELETE * FROM casino_bets WHERE id = $tbet[id]") or sqlerr();

stderr("you got it","<h2>You won the bet, $nogb has been credited to your account, at <a href=userdetails.php?id=$tbet[userid]>$tbet[proposed]'s</a> expense!</h2>&nbsp;&nbsp;&nbsp;$goback");
//exit();
}
else
{
if(empty($newup))
$newup = $CURUSER['uploaded'] - $tbet['amount'];
$newup2 = $tbet['amount']*2;

mysql_query("UPDATE users SET uploaded = $newup WHERE id = '".$CURUSER['id']."'") or sqlerr();
mysql_query("UPDATE users SET uploaded = uploaded + $newup2 WHERE id = '".$tbet['userid']."'") or sqlerr();
mysql_query("UPDATE casino SET deposit = deposit-".$tbet['amount']." WHERE userid = '".$tbet['userid']."'");
if(mysql_affected_rows()==0)
mysql_query("INSERT INTO casino (userid, date, deposit) VALUES (".$tbet['userid'].", '$time', '-".$tbet['amount']."')") or sqlerr();
mysql_query("UPDATE casino_bets SET challenged = '".$CURUSER['username']."' WHERE id = $betid") or sqlerr();
mysql_query("INSERT INTO messages (id, sender, receiver, added, msg, unread, poster) VALUES ('', '$sendfrom', '".$tbet['userid']."', '$time','You just won $nogb of upload credit from ".$CURUSER[username]."!', 'yes', '$sendfrom')") or sqlerr();
if($delold=='y')
mysql_query("DELETE * FROM casino_bets WHERE id = $tbet[id]") or sqlerr();

stderr("damn it","<h2>You lost the bet, <a href=userdetails.php?id=$tbet[userid]>$tbet[proposed]</a> has won $nogb of your hard earnt upload credit!</h2> &nbsp;&nbsp;&nbsp;$goback");
}

exit(); //// the user should not reach this code but for security :-)
}

//Add a new bet
$loca = mysql_query("SELECT * FROM casino_bets WHERE challenged ='empty'") or sqlerr();
$totbets = mysql_num_rows($loca);

if(isset($_POST['unit'])){
if ($_POST["unit"] =='1')
$nobits = $_POST["amnt"]*$mb_basic;
else
$nobits = $_POST["amnt"]*$mb_basic*1024;
}

if (isset($_POST["unit"]))
{
if($openbet >= $maxusrbet)
stderr ("Sorry","There are already $openbet bets open, take an open bet and don´t *tsk tsk... bad language!* with the system!");
if($nobits==0)
stderr ("Sorry","If you win a amount of 0, zero, nada, niente or nichts you are very unhappy. so please don´t add bets without a win!");
if($nobits==-0)
stderr ("Sorry","If you win a amount of 0, zero, nada, niente or nichts you are very unhappy. so please don´t add bets without a win!");
if($nobits<1)
stderr ("Sorry"," This won't work enter a positive value, are you trying to cheat?");
$newup = $CURUSER['uploaded'] - $nobits;
$debt = $nobits-$CURUSER['uploaded'];
if($CURUSER['uploaded'] < $nobits)
{
if($alwdebt!='y')
stderr("Sorry","<h2>Thats ".mksize($debt)." more than you got!</h2>$goback");
}
$betsp = mysql_query("SELECT id, amount FROM casino_bets WHERE userid = ".$CURUSER['id']." ORDER BY time ASC") or sqlerr();
$tbet2 = mysql_fetch_row($betsp);


$dummy = "<H2>Bet added, you will receive a PM notifying you of the results when someone has taken it</H2>";
mysql_query("INSERT INTO casino_bets ( userid, proposed, challenged, amount, time) VALUES ('".$CURUSER['id']."','".$CURUSER['username']."', 'empty', '$nobits', '$time')") or sqlerr();
mysql_query("UPDATE users SET uploaded = $newup WHERE id = ".$CURUSER['id']) or sqlerr();
mysql_query("UPDATE casino SET deposit = deposit + $nobits WHERE userid = ".$CURUSER['id']) or sqlerr();
if(mysql_affected_rows()==0)
mysql_query("INSERT INTO casino (userid, date, deposit) VALUES (".$CURUSER['id'].", '$time', '".$nobits."')") or sqlerr();


}

$loca = mysql_query("SELECT * FROM casino_bets WHERE challenged ='empty'");
$totbets = mysql_num_rows($loca);

////////////////////////////////////////////////standard html begin
stdhead(Casino);

echo("<h1>bet P2P with other users:</h1>");
print("<table class=message width=650 cellspacing=0 cellpadding=5>\n");
print("<tr><td align=center >");
echo($dummy);

//Place bet table

if ($openbet < $maxusrbet)
{
if($totbets >= $maxtotbet)
echo "<br>There are already $maxtotbet bets open, take an open bet!<br>";
else
{
echo "<br><table width=60% cellspacing=0 cellpadding=3>";
tr2('align=center colspan=2 class=colhead','Place bet');
tr2('align=center','<b>Amount to bet</b>',
'align=center','<form name=p2p method=post action='.$phpself.'><input type=text name=amnt size=20 value=1>
<select name=unit>
<option value=1>MB</option>
<option value=2>GB</option>
</select>');
tr2('align=center colspan=2','<input type=submit value=Gamble!>');
echo"</table></form>";
}
}
else
echo "<br>You already have $maxusrbet open bets, wait until they are completed before you start another.<br>";


//Open Bets table

echo ("<table width=90% cellspacing=0 cellpadding=3><br>");
tr2('align=center class=colhead colspan=4','Open Bets');
echo("<tr>");
echo("<td align=center width=15%><b>Name</b></td><td width=15% align=center><b>Amount</b></td>");
echo("<td width=45% align=center><b>Time</b></td><td align=center><b>Take Bet</b></td>");
echo("</tr>");


while ($res = mysql_fetch_assoc($loca))
{
echo (" <tr>");
echo("<td align=center>$res[proposed]</td>");
echo("<td align=center>".mksize($res['amount'])."</td>");
echo("<td align=center>$res[time]</td>");
echo("<td align=center><b><a href=".$casino."?takebet=$res[id]>this</a></b></td>");
echo("</tr>");
$abcdefgh=1;
}
if($abcdefgh==false)
echo("<tr><td align=center colspan=4>Sorry no Bets</td></tr>");
echo "</table>";
echo('</table><br>');

//////////////////////////////////////////////////////////////////////////////////////// game 3


echo("<h1>bet on a color:</h1>");

print("<form name=casino method=post action=$phpself>");
print("<table class=message width=650 cellspacing=0 cellpadding=5>\n");
tr("bet on color:","<input type=submit value='Do it!' >",1);
tr("black",'<input name="color" type="radio" checked value="black">',1);
tr("red",'<input name="color" type="radio" value="red">',1);
tr("how much",'
<select name="betmb">
<option value="'.$bet_value1.'">'.mksize($bet_value1).'</option>
<option value="'.$bet_value2.'">'.mksize($bet_value2).'</option>
<option value="'.$bet_value3.'">'.mksize($bet_value3).'</option>
<option value="'.$bet_value4.'">'.mksize($bet_value4).'</option>
<option value="'.$bet_value5.'">'.mksize($bet_value5).'</option>
<option value="'.$bet_value6.'">'.mksize($bet_value6).'</option>
<option value="'.$bet_value7.'">'.mksize($bet_value7).'</option>
</select>',1);

if($show_real_chance)
$real_chance=$cheat_value+1;
else
$real_chance=2;
tr("your chance","1 : ".$real_chance,1);
tr("you can win",$win_amount." * stake",1);
echo('</table><br>');
print("</form>");

echo("<h1>bet on a number:</h1>");
print("<form name=casino2 method=post action=$phpself>");
print("<table class=message width=650 cellspacing=0 cellpadding=5>\n");
tr("bet on number:","<input type=submit value='Do it!' >",1);
tr("number",'<input name="number" type="radio" checked value="1">1&nbsp;&nbsp;<input name="number" type="radio" value="2">2&nbsp;&nbsp;<input name="number" type="radio" value="3">3',1);
tr("",'<input name="number" type="radio" value="4">4&nbsp;&nbsp;<input name="number" type="radio" value="5">5&nbsp;&nbsp;<input name="number" type="radio" value="6">6',1);
tr("how much",'
<select name="betmb">
<option value="'.$bet_value1.'">'.mksize($bet_value1).'</option>
<option value="'.$bet_value2.'">'.mksize($bet_value2).'</option>
<option value="'.$bet_value3.'">'.mksize($bet_value3).'</option>
<option value="'.$bet_value4.'">'.mksize($bet_value4).'</option>
<option value="'.$bet_value5.'">'.mksize($bet_value5).'</option>
<option value="'.$bet_value6.'">'.mksize($bet_value6).'</option>
<option value="'.$bet_value7.'">'.mksize($bet_value7).'</option>
</select>',1);

if($show_real_chance)
$real_chance=$cheat_value+5;
else
$real_chance=6;
tr("your chance","1 : ".$real_chance,1);
tr("you can win",$win_amount_on_number." * stake",1);
echo('</table><br>');
print("</form>");

echo("<h1>$CURUSER[username]'s details:</h1>");
print("<table cellspacing=0 width=650 cellpadding=3>\n");
print("<tr><td align=center>");
print("<h1>user @ casino</h1>\n");
print("<table class=message cellspacing=0 cellpadding=5>\n");
tr("you can win",mksize($max_download_user),1);
tr("won",mksize($user_win),1);
tr("lost",mksize($user_lost),1);
tr("ratio",$casino_ratio_user,1);
tr('deposit on P2P', mksize($user_deposit+$nobits));

echo('</table>');
print("</td><td align=center>");
print("<h1>global stats</h1>\n");
print("<table class=message cellspacing=0 cellpadding=5>\n");
tr("users can win",mksize($max_download_global),1);
tr("won",mksize($global_win),1);
tr("lost",mksize($global_lost),1);
tr("ratio",$casino_ratio_global,1);
tr("deposit",mksize($global_deposit));
echo('</table>');
print("</td><td align=center>");
print("<h1>user stats</h1>\n");
print("<table class=message cellspacing=0 cellpadding=5>\n");
tr('Uploaded',mksize($CURUSER['uploaded'] - $nobits));
tr('Downloaded',mksize($CURUSER['downloaded']));
tr('Ratio',$ratio);
tr("&nbsp;","");
tr("&nbsp;","");
echo('</table>');
print("</td></tr>");
echo('</table>');

}
stdfoot();
?>