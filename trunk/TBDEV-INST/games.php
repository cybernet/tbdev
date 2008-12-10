<?
require_once("include/bittorrent.php");

dbconn(false);

loggedinorreturn();
parked();
$action = $_GET["action"];

stdhead("Games");

if ($_GET["edited"]) {
print("<h1>Profile updated!</h1>\n");
if ($_GET["mailsent"])
print("<h2>Confirmation email has been sent!</h2>\n");
}
elseif ($_GET["emailch"])
print("<h1>Email address changed!</h1>\n");
else
print("<h1>Welcome, <a href=userdetails.php?id=$CURUSER[id]>$CURUSER[username]</a>!</h1>\n");

print("<table border=1 cellspacing=0 cellpadding=0 align=center><tr>");

print("<td width=502 valign=top>");

print("<table width=502 border=1>");
?>
<style type="text/css">
<!--
.style3 {font-size: 12px}
-->
</style>




<center>
<p>Games<br>
<span class="style3">Please Choose A Game From The Right</span> </p>
</center>
<tr><td

<br>
<tr><td>
</form> <iframe src="" name="games" width="570" height="570" scrolling="no"></iframe></td></tr>
<marquee onmouseover=this.stop() onmouseout=this.start() scrollAmount=2.0 direction=across width='100%' height='75'>
</table></td>

<?

//print("</table></td>");
print("<td width=150 valign=top><table border=1>");

print("<tr><td class=colhead width=150 height=18>$CURUSER[username]'s Avatar</td></tr>");
if ($CURUSER[avatar])
print("<tr><td><img width=125 src=" . htmlspecialchars($CURUSER["avatar"]) . "></td></tr>");
else
print("<tr><td><img width=125 src=pic/default_avatar.gif></td></tr>");
print("<tr><td class=colhead width=150 height=18>Games</td></tr>");
print("<tr><td align=left>&nbsp;&nbsp;<a href=http://www.onemorelevel.com/games3/archer.swf target=games>Play Archer</td></tr>");
print("<tr><td align=left>&nbsp;&nbsp;<a href=http://209.200.250.151/manual/topgun6.swf target=games>Play Top Gun</td></tr>");
print("<tr><td align=left>&nbsp;&nbsp;<a href=http://www.onemorelevel.com/games/Pacman.swf target=games>Play PacMan</td></tr>");
print("<tr><td align=left>&nbsp;&nbsp;<a href=http://www.onemorelevel.com/games/snakes5.dcr target=games>Play Snake 3D</td></tr>");
print("<tr><td align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/wone2.swf target=games>Play Wone2</td></tr>");
print("<tr><td align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/detonate.swf target=games>Play Detonate</td></tr>");
print("<tr><td align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/roadies.swf target=games>Play Roadies</td></tr>");
print("<tr><td align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/motorbike.swf target=games>Play MotorBike</td></tr>");
print("<tr><td align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/warthoglaunch.swf target=games>Play WarthogLaunch</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/commando.swf target=games>Play commando</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/sonic.swf target=games>Play sonic</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/battlefield2.swf target=games>Play battlefield2</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/dirtbike.swf target=games>Play dirtbike</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/kittencannon.swf target=games>Play kittencannon</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/supermariobros.swf target=games>Play supermariobros</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/miniputt.swf target=games>Play MiniPutt</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/basejumping.swf target=games>Play Base Jumping</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/gridlock.swf target=games>Play Gridlock</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/flashpoker.swf target=games>Play Poker</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/3dworm.swf target=games>Play 3D Worm</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/classicbreakout.swf target=games>Play Breakout</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/hangingaround.swf target=games>Play Hanging Around</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/drawplay.swf target=games>Play Drawplay</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/drawplay2.swf target=games>Play Drawplay2</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/driversed2.swf target=games>Play Driver's Ed 2</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/prisonbreak.swf target=games>Play Prison Break</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/matrixrampage.swf target=games>Play Matrix Rampage</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/dodgeballtournament.swf target=games>Play Dodgeball</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/missle3d.swf target=games>Play Missle3d</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/shadowfactory.swf target=games>Play Shadowfactory</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/cursorrun.swf target=games>Play Cursorrun</td></tr>");
print("<tr><td class=colhead align=left>&nbsp;&nbsp;<a href=http://gamesloth.us/hosted/insanitytest.swf target=games>Play Insanitytest</td></tr>");
print("</table>");
print("</td></tr></table>");
?>

<?
stdfoot();

?> 