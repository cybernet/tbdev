<?
ob_start("ob_gzhandler");

require "include/bittorrent.php";
require "rconpasswords.php";
dbconn(true);
if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST")
{
  $choice = $_POST["choice"];
  if ($CURUSER && $choice != "" && $choice < 256 && $choice == floor($choice))
  {
    $res = mysql_query("SELECT * FROM polls ORDER BY added DESC LIMIT 1") or sqlerr();
    $arr = mysql_fetch_assoc($res) or die("No poll");
    $pollid = $arr["id"];
    $userid = $CURUSER["id"];
    $res = mysql_query("SELECT * FROM pollanswers WHERE pollid=$pollid && userid=$userid") or sqlerr();
    $arr = mysql_fetch_assoc($res);
    if ($arr) die("Dupe vote");
    mysql_query("INSERT INTO pollanswers VALUES(0, $pollid, $userid, $choice)") or sqlerr();
    if (mysql_affected_rows() != 1)
      stderr("Error", "An error occured. Your vote has not been counted.");
    header("Location: $BASEURL/");
    die;
  }
  else
    stderr("Error", "Please select an option.");
}

/*
$a = @mysql_fetch_assoc(@mysql_query("SELECT id,username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 1")) or die(mysql_error());
if ($CURUSER)
  $latestuser = "<a href=userdetails.php?id=" . $a["id"] . ">" . $a["username"] . "</a>";
else
  $latestuser = $a['username'];
*/

$registered = number_format(get_row_count("users"));
//$unverified = number_format(get_row_count("users", "WHERE status='pending'"));
$torrents = number_format(get_row_count("torrents"));
//$dead = number_format(get_row_count("torrents", "WHERE visible='no'"));

$r = mysql_query("SELECT value_u FROM avps WHERE arg='seeders'") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_row($r);
$seeders = 0 + $a[0];
$r = mysql_query("SELECT value_u FROM avps WHERE arg='leechers'") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_row($r);
$leechers = 0 + $a[0];
if ($leechers == 0)
  $ratio = 0;
else
  $ratio = round($seeders / $leechers * 100);
$peers = number_format($seeders + $leechers);
$seeders = number_format($seeders);
$leechers = number_format($leechers);

/*
$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));
$res = mysql_query("SELECT id, username, class, donated FROM users WHERE last_access >= $dt ORDER BY username") or print(mysql_error());
while ($arr = mysql_fetch_assoc($res))
{
  if ($activeusers) $activeusers .= ",\n";
  switch ($arr["class"])
  {
    case UC_SYSOP:
    case UC_ADMINISTRATOR:
    case UC_MODERATOR:
      $arr["username"] = "<font color=#A83838>" . $arr["username"] . "</font>";
      break;
     case UC_UPLOADER:
      $arr["username"] = "<font color=#4040C0>" . $arr["username"] . "</font>";
      break;
  }
  $donator = $arr["donated"] > 0;
  if ($donator)
    $activeusers .= "<nobr>";
  if ($CURUSER)
    $activeusers .= "<a href=userdetails.php?id=" . $arr["id"] . "><b>" . $arr["username"] . "</b></a>";
  else
    $activeusers .= "<b>$arr[username]</b>";
  if ($donator)
    $activeusers .= "<img src=/pic/star.gif alt='Donated $$arr[donated]'></nobr>";
}
if (!$activeusers)
  $activeusers = "There have been no active users in the last 15 minutes.";
*/
stdhead();
//echo "<font class=small>Welcome to our newest member, <b>$latestuser</b>!</font>\n";

print("<table width=737 class=main border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>");
print("<h2>Recent news");
if (get_user_class() >= UC_ADMINISTRATOR)
	print(" - <font class=small>[<a class=altlink href=news.php><b>News page</b></a>]</font>");
print("</h2>\n");
$res = mysql_query("SELECT * FROM news WHERE ADDDATE(added, INTERVAL 45 DAY) > NOW() ORDER BY added DESC LIMIT 10") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0)
{
	print("<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n<ul>");
	while($array = mysql_fetch_array($res))
	{
	  print("<li>" . gmdate("Y-m-d",strtotime($array['added'])) . " - " . $array['body']);
    if (get_user_class() >= UC_ADMINISTRATOR)
    {
    	print(" <font size=\"-2\">[<a class=altlink href=news.php?action=edit&newsid=" . $array['id'] . "&returnto=" . urlencode($_SERVER['PHP_SELF']) . "><b>E</b></a>]</font>");
    	print(" <font size=\"-2\">[<a class=altlink href=news.php?action=delete&newsid=" . $array['id'] . "&returnto=" . urlencode($_SERVER['PHP_SELF']) . "><b>D</b></a>]</font>");
    }
    print("</li>");
  }
  print("</ul></td></tr></table>\n");
}

/*
<h2>Active users</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>
<?=$activeusers?>
</td></tr></table>
*/ ?>

<? if ($CURUSER)
{
  // Get current poll
  $res = mysql_query("SELECT * FROM polls ORDER BY added DESC LIMIT 1") or sqlerr();
  $arr = mysql_fetch_assoc($res);
  $pollid = $arr["id"];
  $userid = $CURUSER["id"];
  $question = $arr["question"];
  $o = array($arr["option0"], $arr["option1"], $arr["option2"], $arr["option3"], $arr["option4"],
    $arr["option5"], $arr["option6"], $arr["option7"], $arr["option8"], $arr["option9"],
    $arr["option10"], $arr["option11"], $arr["option12"], $arr["option13"], $arr["option14"],
    $arr["option15"], $arr["option16"], $arr["option17"], $arr["option18"], $arr["option19"]);

  // Check if user has already voted
  $res = mysql_query("SELECT * FROM pollanswers WHERE pollid=$pollid && userid=$userid") or sqlerr();
  $arr2 = mysql_fetch_assoc($res);

  print("<h2>Poll");

  if (get_user_class() >= UC_MODERATOR)
  {
  	print("<font class=small>");
		print(" - [<a class=altlink href=makepoll.php?returnto=main><b>New</b></a>]\n");
  	print(" - [<a class=altlink href=makepoll.php?action=edit&pollid=$arr[id]&returnto=main><b>Edit</b></a>]\n");
		print(" - [<a class=altlink href=polls.php?action=delete&pollid=$arr[id]&returnto=main><b>Delete</b></a>]");
		print("</font>");
	}
	print("</h2>\n");
	print("<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td align=center>\n");
  print("<table class=main border=1 cellspacing=0 cellpadding=0><tr><td class=text>");
  print("<p align=center><b>$question</b></p>\n");
  $voted = $arr2;
  if ($voted)
  {
    // display results
    if ($arr["selection"])
      $uservote = $arr["selection"];
    else
      $uservote = -1;
		// we reserve 255 for blank vote.
    $res = mysql_query("SELECT selection FROM pollanswers WHERE pollid=$pollid AND selection < 20") or sqlerr();

    $tvotes = mysql_num_rows($res);

    $vs = array(); // array of
    $os = array();

    // Count votes
    while ($arr2 = mysql_fetch_row($res))
      $vs[$arr2[0]] += 1;

    reset($o);
    for ($i = 0; $i < count($o); ++$i)
      if ($o[$i])
        $os[$i] = array($vs[$i], $o[$i]);

    function srt($a,$b)
    {
      if ($a[0] > $b[0]) return -1;
      if ($a[0] < $b[0]) return 1;
      return 0;
    }

    // now os is an array like this: array(array(123, "Option 1"), array(45, "Option 2"))
    if ($arr["sort"] == "yes")
    	usort($os, srt);

    print("<table class=main width=100% border=0 cellspacing=0 cellpadding=0>\n");
    $i = 0;
    while ($a = $os[$i])
    {
      if ($i == $uservote)
        $a[1] .= "&nbsp;*";
      if ($tvotes == 0)
      	$p = 0;
      else
      	$p = round($a[0] / $tvotes * 100);
      if ($i % 2)
        $c = "";
      else
        $c = " bgcolor=#ECE9D8";
      print("<tr><td width=1% class=embedded$c><nobr>" . $a[1] . "&nbsp;&nbsp;</nobr></td><td width=99% class=embedded$c>" .
        "<img src=/pic/bar_left.gif><img src=/pic/bar.gif height=9 width=" . ($p * 3) .
        "><img src=/pic/bar_right.gif> $p%</td></tr>\n");
      ++$i;
    }
    print("</table>\n");
	$tvotes = number_format($tvotes);
    print("<p align=center>Votes: $tvotes</p>\n");
  }
  else
  {
    print("<form method=post action=index.php>\n");
    $i = 0;
    while ($a = $o[$i])
    {
      print("<input type=radio name=choice value=$i>$a<br>\n");
      ++$i;
    }
    print("<br>");
    print("<input type=radio name=choice value=255>Blank vote (a.k.a. \"I just want to see the results!\")<br>\n");
    print("<p align=center><input type=submit value='Vote!' class=btn></p>");
  }
?>
</td></tr></table>
<?
if ($voted)
  print("<p align=center><a href=polls.php>Previous polls</a></p>\n");
?>
</td></tr></table>

<?
}

function sortusers($a, $b)
{
	$score_a = trim(substr($a, 4, 5));
	$score_b = trim(substr($b, 4, 5));
	if ($score_a > $score_b)
		return -1;
	elseif ($score_a < $score_b)
		return 1;
	else
		return 0;
}

function getgametype($gametype)
{
	switch ($gametype)
	{
		case "bel": $gametype = "Behind Enemy Lines"; break;
		case "dm": $gametype = "Deathmatch"; break;
		case "hq": $gametype = "Headquarters"; break;
		case "re": $gametype = "Retreival"; break;
		case "sd": $gametype = "Search and Destroy"; break;
		case "tdm": $gametype = "Team Deathmatch"; break;
	}
	return $gametype;
}

function getmapname($mapname)
{
	switch ($mapname)
	{
		case "mp_bocage": $mapname = "Bocage"; break;
		case "mp_brecourt": $mapname = "Brecourt"; break;
		case "mp_carentan": $mapname = "Carentan"; break;
		case "mp_chateau": $mapname = "Chateau"; break;
		case "mp_dawnville": $mapname = "Dawnville"; break;
		case "mp_depot": $mapname = "Depot"; break;
		case "mp_harbor": $mapname = "Harbor"; break;
		case "mp_hurtgen": $mapname = "Hurtgen"; break;
		case "mp_neuville": $mapname = "Neuville"; break;
		case "mp_pavlov": $mapname = "Pavlov"; break;
		case "mp_powcamp": $mapname = "POW-Camp"; break;
		case "mp_railyard": $mapname = "Railyard"; break;
		case "mp_rocket": $mapname = "Rocket"; break;
		case "mp_ship": $mapname = "Ship"; break;
		case "mp_stalingrad": $mapname = "Stalingrad"; break;
		case "evreux": $mapname = "Evreux"; break;
		case "mittenwald": $mapname = "Mittenwald"; break;
		case "mp_centerville": $mapname = "Centerville"; break;
	}
	return $mapname;
}

function updateserverinfo($host, $port, $rcon)
{
	$secs = 5;
	$mtime = @filemtime(".cod-$host.cache");
	if (gmtime() - $secs < $mtime)
		return;
	$lfd = @fopen(".cod-$host.lock", "w+");
	if (!$lfd) return;
	if (!flock($lfd, LOCK_EX | LOCK_NB)) return;
	$sd = fsockopen("udp://$host", $port);
	socket_set_timeout($sd, 1);
	fwrite($sd, "\xFF\xFF\xFF\xFFgetstatus");
	$s = fread($sd, 8192);
	if ($s == "")
		$output .= "<p><b>----- Server is offline -----</b></p>\n";
	else
	{
		if (preg_match("/\\\\g_gametype\\\\(.+?)\\\\/", $s, $matches) > 0)
			$gametype = $matches[1];
		if (preg_match("/\\\\mapname\\\\(.+?)\\\\/", $s, $matches) > 0)
			$mapname = $matches[1];
		if (preg_match("/\\\\sv_maxclients\\\\([0-9]+)/", $s, $matches) > 0)
			$maxclients = $matches[1];
		$gametype = getgametype($gametype);
		$mapname = getmapname($mapname);

		$output .= "IP: <b>$host</b> - Port: <b>$port</b> - \n";
		$output .= "Game type: <b>$gametype</b> - Map: <b>$mapname</b>\n";

		fwrite($sd, "\xFF\xFF\xFF\xFFrcon $rcon status");
		$s .= fread($sd, 1500);
		if ($s == "")
		{
			flock($lfd, LOCK_UN);
			return;
		}
		else
			$s .= substr(fread($sd, 1500), 10);

		$orig = $s;
		$x = fopen(".output", "w+");
		fwrite($x,$orig);
		fclose($x);
		$n = 0;
		$s = rtrim(substr($s, strpos($s, "-----\n") + 6));
		$players = explode("\n", $s);

		if ($players[0] != "")
		{
			usort($players, "sortusers");
			for ($i = 0; $i < count($players); ++$i)
			{
				// extract num, score, ping, guid
				$s = trim($players[$i]);
				$ns = strpos($s, " ");
				$num = substr($s, 0, $ns);
				$s = ltrim(substr($s, $ns));
				$ns = strpos($s, " ");
				$score = substr($s, 0, $ns);
				$s = ltrim(substr($s, $ns));
				$ns = strpos($s, " ");
				$ping = substr($s, 0, $ns);
				if ($ping == "999" || $ping == "CNCT") $ping = "---";
				$s = ltrim(substr($s, $ns));
				$ns = strpos($s, " ");
				$s = ltrim(substr($s, $ns));

				// extract ip
				$ns = strrpos($s, " ");
				$s = rtrim(substr($s, 0, $ns));
				$ns = strrpos($s, " ");
				$s = rtrim(substr($s, 0, $ns));
				$ns = strrpos($s, " ");
				$ip = substr($s, $ns + 1);
				$ip = substr($ip, 0, strpos($ip, ":"));
				$s = rtrim(substr($s, 0, $ns));
				$ns = strrpos($s, " ");
				$s = rtrim(substr($s, 0, $ns));

				// extract player name
				$player = $s;

				// strip color codes
				$player = preg_replace("/(\^[0-9])/", "", $s);

				if ($player == "")
					continue;

				$r = mysql_query("SELECT id,username FROM users WHERE username=" . sqlesc($player) . " AND ip='$ip' AND enabled = 'yes'") or sqlerr();
				if (mysql_num_rows($r) != 1)
				{
					$tfd = fopen(".cod-$host.log", "a");
					fwrite($tfd, "[$num] [$player] [$ip]\n$orig\n\n");
					fclose($tfd);
					usleep(500000);
					fwrite($sd, "\xFF\xFF\xFF\xFFrcon $rcon clientkick $num");
					flock($lfd, LOCK_UN);
					fclose($lfd);
					return;
				}
				$a = mysql_fetch_assoc($r);
				$player = htmlspecialchars($a["username"]);
				if ($n == 0)
				{
					$output .= "<p><table class=main border=1 cellspacing=0 cellpadding=5>\n";
					$output .= "<tr><td class=colhead align=right>#</td><td class=colhead align=left>Player</td><td class=colhead>Score</td><td class=colhead>Ping</td></tr>\n";
				}
				++$n;
				$output .= "<tr><td align=right>$n</td><td align=left><a href=userdetails.php?id=$a[id]>$player</a></td><td>$score</td><td>$ping</td></tr>\n";
			}
			if ($n > 0) $output .= "</table></p>";
		}
		if ($n == 0)
			$output .= "<p><b>----- No players -----</b></p>\n";
	}
	$fd = @fopen(".cod-$host.cache", "w");
	if ($fd)
	{
		fwrite($fd, $output);
		fclose($fd);
	}
	flock($lfd, LOCK_UN);
	fclose($lfd);
}

function printserverinfo($host, $port, $rcon)
{
	updateserverinfo($host, $port, $rcon);
	readfile(".cod-$host.cache");
}

?>
<h2>Call of Duty</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td align=center>
<?
print("Use your <b>TorrentBits Username</b> to gain access to this server!<br>\n");
printserverinfo("62.212.84.221", 28960, $rconpassword);
print("<p>Please install <a href=http://www.callofduty.com/patch/><b>this patch</b></a> and <a href=ftp://torrentbits.org/games/callofduty/maps/><b>these maps</b></a> before joing!</p>");
?>
</td></tr></table>

<h2>Stats</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td align=center>
<table class=main border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead>Registered users</td><td align=right><?=$registered?></td></tr>
<!-- <tr><td class=rowhead>Unconfirmed users</td><td align=right><?=$unverified?></td></tr> -->
<tr><td class=rowhead>Torrents</td><td align=right><?=$torrents?></td></tr>
<? if (isset($peers)) { ?>
<tr><td class=rowhead>Peers</td><td align=right><?=$peers?></td></tr>
<tr><td class=rowhead>Seeders</td><td align=right><?=$seeders?></td></tr>
<tr><td class=rowhead>Leechers</td><td align=right><?=$leechers?></td></tr>
<tr><td class=rowhead>Seeder/leecher ratio (%)</td><td align=right><?=$ratio?></td></tr>
<? } ?>
</table>
</td></tr></table>

<? /*
<h2>Server load</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td align=center>
<table class=main border=0 width=402><tr><td style='padding: 0px; background-image: url(/pic/loadbarbg.gif); background-repeat: repeat-x'>
<? $percent = min(100, round(exec('ps ax | grep -c apache') / 256 * 100));
if ($percent <= 70) $pic = "loadbargreen.gif";
elseif ($percent <= 90) $pic = "loadbaryellow.gif";
else $pic = "loadbarred.gif";
$width = $percent * 4;
print("<img height=15 width=$width src=\"/pic/$pic\" alt='$percent%'>"); ?>
</td></tr></table>
</td></tr></table>
*/ ?>

<p><font class=small>Disclaimer: None of the files shown here are actually hosted on this server. The links are provided solely by this site's users.
The administrator of this site (www.torrentbits.org) cannot be held responsible for what its users post, or any other actions of its users.
You may not use this site to distribute or download any material when you do not have the legal rights to do so.
It is your own responsibility to adhere to these terms.</font></p>

<p align=center>
<a href=http://www.downhillbattle.org/defense/><img src=/pic/supportfilesharing.gif border=0 alt="P2P Legal Defense Fund"></a>
</p>


</td></tr></table>

<?
stdfoot();

hit_end();
?>