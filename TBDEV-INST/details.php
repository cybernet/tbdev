<?php
ob_start("ob_gzhandler");
require_once("include/bittorrent.php");
require ("imdb/imdb.class.php");
require_once("include/function_torrenttable.php");
require_once("include/commenttable.php");
require_once "include/user_functions.php";
require_once ("include/bbcode_functions.php");
#========================================
#getAgent function by deliopoulos
#========================================
function StdDecodePeerId($id_data, $id_name){
$version_str = "";
for ($i=0; $i<=strlen($id_data); $i++){
$c = $id_data[$i];
if ($id_name=="BitTornado" || $id_name=="ABC") {
if ($c!='-' && ctype_digit($c)) $version_str .= "$c.";
elseif ($c!='-' && ctype_alpha($c)) $version_str .= (ord($c)-55).".";
else break;
}
elseif($id_name=="BitComet"||$id_name=="BitBuddy"||$id_name=="Lphant"||$id_name=="BitPump"||$id_name=="BitTorrent Plus! v2") {
if ($c != '-' && ctype_alnum($c)){
$version_str .= "$c";
if($i==0) $version_str = intval($version_str) .".";
}
else{
$version_str .= ".";
break;
}
}
else {
if ($c != '-' && ctype_alnum($c)) $version_str .= "$c.";
else break;
}
}
$version_str = substr($version_str,0,strlen($version_str)-1);
return "$id_name $version_str";
}
function MainlineDecodePeerId($id_data, $id_name){
$version_str = "";
for ($i=0; $i<=strlen($id_data); $i++){
$c = $id_data[$i];
if ($c != '-' && ctype_alnum($c)) $version_str .= "$c.";
}
$version_str = substr($version_str,0,strlen($version_str)-1);
return "$id_name $version_str";
}
function DecodeVersionString ($ver_data, $id_name){
$version_str = "";
$version_str .= intval(ord($ver_data[0]) + 0).".";
$version_str .= intval(ord($ver_data[1])/10 + 0);
$version_str .= intval(ord($ver_data[1])%10 + 0);
return "$id_name $version_str";
}
function getagent($httpagent, $peer_id="") {
// if($peer_id!="") $peer_id=hex2bin($peer_id);
if(substr($peer_id,0,3)=='-AX') return StdDecodePeerId(substr($peer_id,4,4),"BitPump"); # AnalogX BitPump
if(substr($peer_id,0,3)=='-BB') return StdDecodePeerId(substr($peer_id,3,5),"BitBuddy"); # BitBuddy
if(substr($peer_id,0,3)=='-BC') return StdDecodePeerId(substr($peer_id,4,4),"BitComet"); # BitComet
if(substr($peer_id,0,3)=='-BS') return StdDecodePeerId(substr($peer_id,3,7),"BTSlave"); # BTSlave
if(substr($peer_id,0,3)=='-BX') return StdDecodePeerId(substr($peer_id,3,7),"BittorrentX"); # BittorrentX
if(substr($peer_id,0,3)=='-CT') return "Ctorrent $peer_id[3].$peer_id[4].$peer_id[6]"; # CTorrent
if(substr($peer_id,0,3)=='-KT') return StdDecodePeerId(substr($peer_id,3,7),"KTorrent"); # KTorrent
if(substr($peer_id,0,3)=='-LT') return StdDecodePeerId(substr($peer_id,3,7),"libtorrent"); # libtorrent
if(substr($peer_id,0,3)=='-LP') return StdDecodePeerId(substr($peer_id,4,4),"Lphant"); # Lphant
if(substr($peer_id,0,3)=='-MP') return StdDecodePeerId(substr($peer_id,3,7),"MooPolice"); # MooPolice
if(substr($peer_id,0,3)=='-MT') return StdDecodePeerId(substr($peer_id,3,7),"Moonlight"); # MoonlightTorrent
if(substr($peer_id,0,3)=='-PO') return StdDecodePeerId(substr($peer_id,3,7),"PO Client"); #unidentified clients with versions
if(substr($peer_id,0,3)=='-QT') return StdDecodePeerId(substr($peer_id,3,7),"Qt 4 Torrent"); # Qt 4 Torrent
if(substr($peer_id,0,3)=='-RT') return StdDecodePeerId(substr($peer_id,3,7),"Retriever"); # Retriever
if(substr($peer_id,0,3)=='-S2') return StdDecodePeerId(substr($peer_id,3,7),"S2 Client"); #unidentified clients with versions
if(substr($peer_id,0,3)=='-SB') return StdDecodePeerId(substr($peer_id,3,7),"Swiftbit"); # Swiftbit
if(substr($peer_id,0,3)=='-SN') return StdDecodePeerId(substr($peer_id,3,7),"ShareNet"); # ShareNet
if(substr($peer_id,0,3)=='-SS') return StdDecodePeerId(substr($peer_id,3,7),"SwarmScope"); # SwarmScope
if(substr($peer_id,0,3)=='-SZ') return StdDecodePeerId(substr($peer_id,3,7),"Shareaza"); # Shareaza
if(preg_match("/^RAZA ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches)) return "Shareaza $matches[1]";
if(substr($peer_id,0,3)=='-TN') return StdDecodePeerId(substr($peer_id,3,7),"Torrent.NET"); # Torrent.NET
if(substr($peer_id,0,3)=='-TR') return StdDecodePeerId(substr($peer_id,3,7),"Transmission"); # Transmission
if(substr($peer_id,0,3)=='-TS') return StdDecodePeerId(substr($peer_id,3,7),"TorrentStorm"); # Torrentstorm
if(substr($peer_id,0,3)=='-UR') return StdDecodePeerId(substr($peer_id,3,7),"UR Client"); # unidentified clients with versions
if(substr($peer_id,0,3)=='-UT') return StdDecodePeerId(substr($peer_id,3,7),"uTorrent"); # uTorrent
if(substr($peer_id,0,3)=='-XT') return StdDecodePeerId(substr($peer_id,3,7),"XanTorrent"); # XanTorrent
if(substr($peer_id,0,3)=='-ZT') return StdDecodePeerId(substr($peer_id,3,7),"ZipTorrent"); # ZipTorrent
if(substr($peer_id,0,3)=='-bk') return StdDecodePeerId(substr($peer_id,3,7),"BitKitten"); # BitKitten
if(substr($peer_id,0,3)=='-lt') return StdDecodePeerId(substr($peer_id,3,7),"libTorrent"); # libTorrent
if(substr($peer_id,0,3)=='-pX') return StdDecodePeerId(substr($peer_id,3,7),"pHoeniX"); # pHoeniX
if(substr($peer_id,0,2)=='BG') return StdDecodePeerId(substr($peer_id,2,4),"BTGetit"); # BTGetit
if(substr($peer_id,2,2)=='BM') return DecodeVersionString(substr($peer_id,0,2),"BitMagnet"); # BitMagnet
if(substr($peer_id,0,2)=='OP') return StdDecodePeerId(substr($peer_id,2,4),"Opera"); # Opera
if(substr($peer_id,0,4)=='270-') return "GreedBT 2.7.0"; # GreedBT
if(substr($peer_id,0,4)=='271-') return "GreedBT 2.7.1"; # GreedBT 2.7.1
if(substr($peer_id,0,4)=='346-') return "TorrentTopia"; # TorrentTopia
if(substr($peer_id,0,3)=='-AR') return "Arctic Torrent"; # Arctic (no way to know the version)
if(substr($peer_id,0,3)=='-G3') return "G3 Torrent"; # G3 Torrent
if(substr($peer_id,0,6)=='BTDWV-') return "Deadman Walking"; # Deadman Walking
if(substr($peer_id,5,7)=='Azureus') return "Azureus 2.0.3.2"; # Azureus 2.0.3.2
if(substr($peer_id,0,8 )=='PRC.P---') return "BitTorrent Plus! II"; # BitTorrent Plus! II
if(substr($peer_id,0,8 )=='S587Plus') return "BitTorrent Plus!"; # BitTorrent Plus!
if(substr($peer_id,0,7)=='martini') return "Martini Man"; # Martini Man
if(substr($peer_id,4,6)=='btfans') return "SimpleBT"; # SimpleBT
if(substr($peer_id,3,9)=='SimpleBT?') return "SimpleBT"; # SimpleBT
if(ereg("MFC_Tear_Sample", $httpagent)) return "SimpleBT";
if(substr($peer_id,0,5)=='btuga') return "BTugaXP"; # BTugaXP
if(substr($peer_id,0,5)=='BTuga') return "BTuga"; # BTugaXP
if(substr($peer_id,0,5)=='oernu') return "BTugaXP"; # BTugaXP
if(substr($peer_id,0,10)=='DansClient') return "XanTorrent"; # XanTorrent
if(substr($peer_id,0,16)=='Deadman Walking-') return "Deadman"; # Deadman client
if(substr($peer_id,0,8 )=='XTORR302') return "TorrenTres 0.0.2"; # TorrenTres
if(substr($peer_id,0,7)=='turbobt') return "TurboBT ".(substr($peer_id,7,5)); # TurboBT
if(substr($peer_id,0,7)=='a00---0') return "Swarmy"; # Swarmy
if(substr($peer_id,0,7)=='a02---0') return "Swarmy"; # Swarmy
if(substr($peer_id,0,7)=='T00---0') return "Teeweety"; # Teeweety
if(substr($peer_id,0,7)=='rubytor') return "Ruby Torrent v".ord($peer_id[7]); # Ruby Torrent
if(substr($peer_id,0,5)=='Mbrst') return MainlineDecodePeerId(substr($peer_id,5,5),"burst!"); # burst!
if(substr($peer_id,0,4)=='btpd') return "BT Protocol Daemon ".(substr($peer_id,5,3)); # BT Protocol Daemon
if(substr($peer_id,0,8 )=='XBT022--') return "BitTorrent Lite"; # BitTorrent Lite based on XBT code
if(substr($peer_id,0,3)=='XBT') return StdDecodePeerId(substr($peer_id,3,3), "XBT"); # XBT Client
if(substr($peer_id,0,4)=='-BOW') return StdDecodePeerId(substr($peer_id,4,5),"Bits on Wheels"); # Bits on Wheels
if(substr($peer_id,1,2)=='ML') return MainlineDecodePeerId(substr($peer_id,3,5),"MLDonkey"); # MLDonkey
if(substr($peer_id,0,8 )=='AZ2500BT') return "AzureusBitTyrant 1.0/1";
if($peer_id[0]=='A') return StdDecodePeerId(substr($peer_id,1,9),"ABC"); # ABC
if($peer_id[0]=='R') return StdDecodePeerId(substr($peer_id,1,5),"Tribler"); # Tribler
if($peer_id[0]=='M'){
if(preg_match("/^Python/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
return MainlineDecodePeerId(substr($peer_id,1,7),"Mainline"); # Mainline BitTorrent with version
}
if($peer_id[0]=='O') return StdDecodePeerId(substr($peer_id,1,9),"Osprey Permaseed"); # Osprey Permaseed
if($peer_id[0]=='S'){
if(preg_match("/^BitTorrent\/3.4.2/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
return StdDecodePeerId(substr($peer_id,1,9),"Shad0w"); # Shadow's client
}
if($peer_id[0]=='T'){
if(preg_match("/^Python/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
return StdDecodePeerId(substr($peer_id,1,9),"BitTornado"); # BitTornado
}
if($peer_id[0]=='U') return StdDecodePeerId(substr($peer_id,1,9),"UPnP"); # UPnP NAT Bit Torrent
# Azureus / Localhost
if(substr($peer_id,0,3)=='-AZ') {
if(preg_match("/^Localhost ([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches)) return "Localhost $matches[1]";
if(preg_match("/^BitTorrent\/3.4.2/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
if(preg_match("/^Python/", $httpagent, $matches)) return "Spoofing BT Client"; # Spoofing BT Client
return StdDecodePeerId(substr($peer_id,3,7),"Azureus");
}
if(ereg("Azureus", $peer_id)) return "Azureus 2.0.3.2";
# BitComet/BitLord/BitVampire/Modded FUTB BitComet
if(substr($peer_id,0,4)=='exbc' || substr($peer_id,1,3)=='UTB'){
if(substr($peer_id,0,4)=='FUTB') return DecodeVersionString(substr($peer_id,4,2),"BitComet Mod1");
elseif(substr($peer_id,0,4)=='xUTB') return DecodeVersionString(substr($peer_id,4,2),"BitComet Mod2");
elseif(substr($peer_id,6,4)=='LORD') return DecodeVersionString(substr($peer_id,4,2),"BitLord");
elseif(substr($peer_id,6,3)=='---' && DecodeVersionString(substr($peer_id,4,2),"BitComet")=='BitComet 0.54') return "BitVampire";
else return DecodeVersionString(substr($peer_id,4,2),"BitComet");
}
# Rufus
if(substr($peer_id,2,2)=='RS'){
for ($i=0; $i<=strlen(substr($peer_id,4,9)); $i++){
$c = $peer_id[$i+4];
if (ctype_alnum($c) || $c == chr(0)) $rufus_chk = true;
else break;
}
if ($rufus_chk) return DecodeVersionString(substr($peer_id,0,2),"Rufus"); # Rufus
}
# BitSpirit
if(substr($peer_id,14,6)=='HTTPBT' || substr($peer_id,16,4)=='UDP0') {
if(substr($peer_id,2,2)=='BS') {
if($peer_id[1]==chr(0)) return "BitSpirit v1";
if($peer_id[1]== chr(2)) return "BitSpirit v2";
}
return "BitSpirit";
}
#BitSpirit
if(substr($peer_id,2,2)=='BS') {
if($peer_id[1]==chr(0)) return "BitSpirit v1";
if($peer_id[1]==chr(2)) return "BitSpirit v2";
return "BitSpirit";
}
# eXeem beta
if(substr($peer_id,0,3)=='-eX') {
$version_str = "";
$version_str .= intval($peer_id[3],16).".";
$version_str .= intval($peer_id[4],16);
return "eXeem $version_str";
}
if(substr($peer_id,0,2)=='eX') return "eXeem"; # eXeem beta .21
if(substr($peer_id,0,12)==(chr(0)*12) && $peer_id[12]==chr(97) && $peer_id[13]==chr(97)) return "Experimental 3.2.1b2"; # Experimental 3.2.1b2
if(substr($peer_id,0,12)==(chr(0)*12) && $peer_id[12]==chr(0) && $peer_id[13]==chr(0)) return "Experimental 3.1"; # Experimental 3.1
//if(substr($peer_id,0,12)==(chr(0)*12)) return "Mainline (obsolete)"; # Mainline BitTorrent (obsolete)
//return "$httpagent [$peer_id]";
return "Unknown client";
}
#========================================
#getAgent function by deliopoulos
#========================================
function dltable($name, $arr, $torrent)
{

	global $CURUSER;
	$s = "<b>" . count($arr) . " $name</b>\n";
	if (!count($arr))
		return $s;
	$s .= "\n";
	$s .= "<table width=100% class=main border=1 cellspacing=0 cellpadding=5>\n";
	$s .= "<tr><td class=colhead>User/IP</td>" .
          "<td class=colhead align=center>Connectable</td>".
          "<td class=colhead align=right>Uploaded</td>".
          "<td class=colhead align=right>Rate</td>".
          "<td class=colhead align=right>Downloaded</td>" .
          "<td class=colhead align=right>Rate</td>" .
          "<td class=colhead align=right>Ratio</td>" .
          "<td class=colhead align=right>Complete</td>" .
          "<td class=colhead align=right>Connected</td>" .
          "<td class=colhead align=right>Idle</td>" .
          "<td class=colhead align=left>Client</td></tr>\n";
	$now = time();
	$moderator = (isset($CURUSER) && get_user_class() >= UC_MODERATOR);
    $mod = get_user_class() >= UC_MODERATOR;
	foreach ($arr as $e) {
// user/ip/port
// check if anyone has this ip
($unr = mysql_query("SELECT id, username, privacy, warned, donor, anonymous FROM users WHERE id=$e[userid] ORDER BY last_access DESC LIMIT 1")) or die;
$una = mysql_fetch_array($unr);
if ($una["privacy"] == "strong") continue;
++$num;
$highlight = $CURUSER["id"] == $una["id"] ? " bgcolor=#555555" : "";
$s .= "<tr$highlight>\n";
//$s .= "<tr>\n";
if ($una["username"]) {
if (get_user_class() < UC_MODERATOR && $una[anonymous] == 'yes' && $e[userid] != $CURUSER[id]) {
$s .= "<td class=\"row1\"><i>Anonymous</i></td>\n";
} else {
if (get_user_class() >= UC_UPLOADER || $torrent['anonymous'] != 'yes' || $e['userid'] != $torrent['owner']) {
$s .= "<td class=\"row1\"><a href=userdetails.php?id=$e[userid]><b>$una[username]</b></a>" . ($una["donor"] == "yes" ? "<img src=".
 "/pic/star.gif alt='Donor'>" : "") . ($una["enabled"] == "no" ? "<img src=".
 "/pic/disabled.gif alt=\"This account is disabled\" style='margin-left: 2px'>" : ($una["warned"] == "yes" ? "<a href=rules.php#warning class=altlink><img src=/pic/warned.gif alt=\"Warned\" border=0></a>" : ""));
}
elseif (get_user_class() >= UC_UPLOADER || $torrent['anonymous'] = 'yes') {
$s .= "<td class=\"row1\"><i>Anonymous</i></a></td>\n";
}
}
}
else
$s .= "<td>(unknown)</td>\n";
 

		$secs = max(1, ($now - $e["st"]) - ($now - $e["la"]));
		$revived = $e["revived"] == "yes";
        $s .= "<td align=center>" . ($e[connectable] == "yes" ? "Yes" : "<font color=red>No</font>") . "</td>\n";
		$s .= "<td align=right>" . mksize($e["uploaded"]) . "</td>\n";
		$s .= "<td align=right><nobr>" . mksize(($e["uploaded"] - $e["uploadoffset"]) / $secs) . "/s</nobr></td>\n";
		$s .= "<td align=right>" . mksize($e["downloaded"]) . "</td>\n";
		if ($e["seeder"] == "no")
			$s .= "<td align=right><nobr>" . mksize(($e["downloaded"] - $e["downloadoffset"]) / $secs) . "/s</nobr></td>\n";
		else
			$s .= "<td align=right><nobr>" . mksize(($e["downloaded"] - $e["downloadoffset"]) / max(1, $e["finishedat"] - $e[st])) .	"/s</nobr></td>\n";
                if ($e["downloaded"])
				{
                  $ratio = floor(($e["uploaded"] / $e["downloaded"]) * 1000) / 1000;
                    $s .= "<td align=\"right\"><font color=" . get_ratio_color($ratio) . ">" . number_format($ratio, 3) . "</font></td>\n";
				}
	               else
                  if ($e["uploaded"])
                    $s .= "<td align=right>Inf.</td>\n";
                  else
                    $s .= "<td align=right>---</td>\n";
		$s .= "<td align=right>" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</td>\n";
		$s .= "<td align=right>" . mkprettytime($now - $e["st"]) . "</td>\n";
		$s .= "<td align=right>" . mkprettytime($now - $e["la"]) . "</td>\n";
		$s .= "<td align=left>" . safeChar(getagent($e["agent"], $e["peer_id"])) . ((get_user_class() >= UC_ADMINISTRATOR) ? "<a href='ban_client.php?agent=".$e["agent"]."&peer_id=".bin2hex(substr($e["peer_id"], 0, 8 ))."&returnto=".urlencode("details.php?id=".intval($_GET["id"]))."'><img src='pic/smilies/thumbsdown.gif' border='0' alt='Ban client?'></a>" : "")."</td>\n";
		$s .= "</tr>\n";
	}
	$s .= "</table>\n";
	return $s;
}

dbconn(false);
maxcoder();	

if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

$id = 0 + $_GET["id"];

if (!isset($id) || !$id)
	die();

$res = sql_query("SELECT torrents.seeders, torrents.banned, torrents.nuked, torrents.nukereason, torrents.newgenre, torrents.checked_by, torrents.leechers, torrents.info_hash, torrents.filename, torrents.points, LENGTH(torrents.nfo) AS nfosz, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(torrents.last_action) AS lastseed, torrents.numratings, torrents.name, IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.tube, torrents.type, torrents.numfiles, torrents.vip, torrents.url, torrents.countstats, torrents.anonymous, torrents.poster, freeslots.free AS freeslot, freeslots.doubleup AS doubleslot, freeslots.addedfree AS addedfree, freeslots.addedup AS addedup, freeslots.torrentid AS slotid, freeslots.userid AS slotuid, categories.name AS cat_name, categories.id AS cat_id, users.username FROM torrents LEFT JOIN freeslots ON (torrents.id=freeslots.torrentid AND freeslots.userid={$CURUSER[id]}) LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id")
	or sqlerr();
$row = mysql_fetch_assoc($res);

$owned = $moderator = 0;
	if (get_user_class() >= UC_MODERATOR)
		$owned = $moderator = 1;
	elseif ($CURUSER["id"] == $row["owner"])
		$owned = 1;
if ($row["vip"] =="yes" && get_user_class() < UC_VIP)
stderr("VIP Access Required", "You must be a VIP In order to view details or download this torrent! You may become a Vip By Donating to our site. Donating ensures we stay online to provide you more Vip-Only Torrents!");

if (!$row || ($row["banned"] == "yes" && !$moderator))
	stderr("Error", "No torrent with ID.");
else {
	if ($_GET["hit"]) {
		sql_query("UPDATE torrents SET views = views + 1 WHERE id = $id");
		if ($_GET["tocomm"])
			header("Location: $BASEURL/details.php?id=$id&page=0#startcomments");
		elseif ($_GET["filelist"])
			header("Location: $BASEURL/details.php?id=$id&filelist=1#filelist");
		elseif ($_GET["toseeders"])
			header("Location: $BASEURL/details.php?id=$id&dllist=1#seeders");
		elseif ($_GET["todlers"])
			header("Location: $BASEURL/details.php?id=$id&dllist=1#leechers");
		else
			header("Location: $BASEURL/details.php?id=$id");
		exit();
	    }
          if(isset($_GET["ajax"])) {
          print("<tr><td class=\"index\" style=\"padding: 10px;\">" . str_replace(array("\n", "  "), array("<br />\n", "&nbsp; "), format_comment(safeChar($row["descr"]))) . "</td></tr>");
          die();
         }
	    if (!isset($_GET["page"])) {
		stdhead("Details for torrent \"" . $row["name"] . "\"");

		if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
			$owned = 1;
		else
			$owned = 0;

		$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        //=== free download?
        if ($row["countstats"] == "no")
        echo("<h1><img src=pic/cat_free.gif alt=FREE> This Torrent Is Currently Set To Free! <img src=pic/cat_free.gif alt=FREE></h1>\n");
		if ($_GET["uploaded"]) {
			print("<h2>Successfully uploaded!</h2>\n");
			print("<p><b>Please wait - Your torrent will download automatically </b> <b>Note : that the torrent won't be visible until you start seeding! </b></p>\n");
		                print("<meta http-equiv=\"refresh\" content=\"1;url=download.php/$id/" . rawurlencode($row["filename"]). "\"/>");
                                }
		elseif ($_GET["edited"]) {
			print("<h2>Successfully edited!</h2>\n");
			if (isset($_GET["returnto"]))
				print("<p><b>Go back to <a href=\"" . safeChar("{$BASEURL}/{$_GET['returnto']}") . "\">whence you came</a>.</b></p>\n");
		}
		elseif (isset($_GET["searched"])) {
			print("<h2>Your search for \"" . safeChar($_GET["searched"]) . "\" gave a single result:</h2>\n");
		}
		elseif ($_GET["rated"])
			print("<h2>Rating added!</h2>\n");
// start torrent check mod
if ($CURUSER['class'] >= UC_MODERATOR)
{
if (isset($_GET["checked"]) &&  $_GET["checked"] == 1)
{
mysql_query("UPDATE torrents SET checked_by = ".sqlesc($CURUSER['username'])." WHERE id =$id LIMIT 1");
write_log("Torrent <a href=$BASEURL/details.php?id=$id>($row[name])</a> was checked by $CURUSER[username]");
header("Location: $BASEURL/details.php?id=$id&checked=done#Success");        
}
elseif (isset($_GET["rechecked"]) &&  $_GET["rechecked"] == 1)
{
mysql_query("UPDATE torrents SET checked_by = ".sqlesc($CURUSER['username'])." WHERE id =$id LIMIT 1");
write_log("Torrent <a href=$BASEURL/details.php?id=$id>($row[name])</a> was re-checked by $CURUSER[username]");
header("Location: $BASEURL/details.php?id=$id&rechecked=done#Success");        
}
elseif (isset($_GET["clearchecked"]) &&  $_GET["clearchecked"] == 1)
{
mysql_query("UPDATE torrents SET checked_by = '' WHERE id =$id LIMIT 1");
write_log("Torrent <a href=$BASEURL/details.php?id=$id>($row[name])</a> was un-checked by $CURUSER[username]");
header("Location: $BASEURL/details.php?id=$id&clearchecked=done#Success");        
}
if (isset($_GET["checked"]) &&  $_GET["checked"] == 'done')
{
?>
<h2><a name='Success'>Successfully checked <?php echo $CURUSER['username']?>!</a></h2>
<?php
}
if (isset($_GET["rechecked"]) &&  $_GET["rechecked"] == 'done')
{
?>
<h2><a name='Success'>Successfully re-checked <?php echo $CURUSER['username']?>!</a></h2>
<?php
}
if (isset($_GET["clearchecked"]) &&  $_GET["clearchecked"] == 'done')
{
?>
<h2><a name='Success'>Successfully un-checked <?php echo $CURUSER['username']?>!</a></h2>
<?php
}
}
// end
        $s=$row["name"];
		print("<h1>$s</h1>\n");
                print("<table width=750 border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");

		$url = "edit.php?id=" . $row["id"];
		if (isset($_GET["returnto"])) {
			$addthis = "&amp;returnto=" . urlencode($_GET["returnto"]);
			$url .= $addthis;
			$keepget .= $addthis;
		}
		$editlink = "a href=\"$url\" class=\"sublink\"";

//		$s = "<b>" . safeChar($row["name"]) . "</b>";
//		if ($owned)
//			$s .= " $spacer<$editlink>[Edit torrent]</a>";
//		tr("Name", $s, 1);

/// freeleech/doubleseed slots
$clr = '#EAFF08'; /// font color
$duration ="+14 days"; /// slots in use get deleted in 14 days
$freeimg = '<img src="'.$pic_base_url.'freedownload.gif" border=0"/>';
$doubleimg = '<img src="'.$pic_base_url.'doubleseed.gif" border=0"/>';
$addedup = strtotime($row["addedup"]);
$expires = strtotime("$duration",$addedup);
$addup = date('F j, Y',$expires);
$addedfree = strtotime($row["addedfree"]);
$expires2 = strtotime("$duration",$addedfree);
$addfree = date('F j, Y',$expires2);
$iq =  strtotime(get_date_time($iq));
$xp = strtotime("$duration",$iq);
$idk = date('F j, Y',$xp);
$pq = $row["slotid"] == $id && $row["slotuid"] == $CURUSER["id"];
$frees = $row["freeslot"];
$doubleup = $row["doubleslot"];
if ($pq && $frees == 'yes' && $doubleup == 'no'){
echo '<tr><td align=right class=rowhead>Slots</td><td align=left>'.$freeimg.'  <b><font color="'.$clr.'">Freeleech Slot In Use!</font></b> (only upload stats are recorded) - Expires:  12:01AM '.$addfree.'</td></tr>';
$freeslot = ($CURUSER['freeslots']>="1" ? "  <b>Use:</b> <a class=\"index\" href=\"doubleseed.php/".$id."/" . rawurlencode($row['filename']) . "\" rel=balloon2 onClick=\"return confirm('Are you sure you want to use a doubleseed slot?')\"><font color=".$clr."><b>Doubleseed Slot</a></font></b> - " . safeChar($CURUSER[freeslots]) . " Slots Remaining. " : "");
}
elseif ($pq && $frees == 'no' && $doubleup == 'yes'){    
echo '<tr><td align=right class=rowhead>Slots</td><td align=left>'.$doubleimg.'  <b><font color="'.$clr.'">Doubleseed Slot In Use!</font></b> (upload stats x2) - Expires: 12:01AM '.$addup.'</td></tr>';
$freeslot = ($CURUSER['freeslots']>="1" ? "  <b>Use:</b> <a class=\"index\" href=\"downloadfree.php/".$id."/" . rawurlencode($row['filename']) . "\" rel=balloon1 onClick=\"return confirm('Are you sure you want to use a freeleech slot?')\"><font color=".$clr."><b>Freeleech Slot</a></font></b> - " . safeChar($CURUSER[freeslots]) . " Slots Remaining. " : "");
}
elseif ($pq && $doubleup == 'yes' && $frees == 'yes'){
echo '<tr><td align=right class=rowhead>Slots</td><td align=left>'.$freeimg.' '.$doubleimg.'  <b><font color="'.$clr.'">Freeleech and Doubleseed Slots In Use!</font></b> (upload stats x2 and no download stats are recorded)<p>Freeleech Expires: 12:01AM '.$addfree.' and Doubleseed Expires: 12:01AM '.$addup.'</p></td></tr>';
}
else
$freeslot = ($CURUSER['freeslots']>="1" ? "  <b>Use:</b> <a class=\"index\" href=\"downloadfree.php/".$id."/" . rawurlencode($row['filename']) . "\" rel=balloon1 onClick=\"return confirm('Are you sure you want to use a freeleech slot?')\"><font color=".$clr."><b>Freeleech Slot</a></font></b>   <b>Use:</b> <a class=\"index\" href=\"doubleseed.php/".$id."/" . rawurlencode($row['filename']) . "\" rel=balloon2  onClick=\"return confirm('Are you sure you want to use a doubleseed slot?')\"><font color=".$clr."><b>Doubleseed Slot</a></font></b> - " . safeChar($CURUSER[freeslots]) . " Slots Remaining. " : "");
?>
<div id="balloon1" class="balloonstyle">
Once chosen this torrent will be Freeleech <?php echo $freeimg?> until <?php echo $idk?> and can be resumed or started over using the regular download link. Doing so will result in one Freeleech Slot being taken away from your total.</div>
<div id="balloon2" class="balloonstyle">
Once chosen this torrent will be Doubleseed <?php echo $doubleimg?> until <?php echo $idk?> and can be resumed or started over using the regular download link. Doing so will result in one Freeleech Slot being taken away from your total.</div>
<?php
if ($CURUSER["id"] == $row["owner"]) $CURUSER["downloadpos"] = "yes";
       if ($CURUSER["downloadpos"] != "no")
    {
     $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
$percentage = ($ratio * 100);
print("<tr><td class=rowhead width=1%>Download</td><td width=99% align=left>");
if (get_user_class() >= UC_VIP) {
print("<a class=\"index\" href=\"download.php/$id/" . rawurlencode($row["filename"]) . "\">" . safeChar($row["filename"]) . "</a>".$freeslot."");
}
else {
$usid = $CURUSER["id"];
$rs = mysql_query("SELECT * FROM users WHERE id='$usid'") or sqlerr();
$ar = mysql_fetch_assoc($rs);
$gigs = $ar["downloaded"] / (1024*1024*1024);
if (($gigs > "10")&&($ratio <= 0.3 and (!$owned|| 0) and ($CURUSER["downloaded"] <> 0)))
{
print("<p align=\"center\">");
print("<font color=red><b><u>Download Privileges Removed Please Restart A Old Torrent To Improve Your Ratio!!</font><border=\"1\" cellpadding=\"10\" cellspacing=\"10\"></u></b>");
print("<p><font color=green><b>Your ratio is $ratio</b></font> - meaning that you have only uploaded ");
print("$percentage % ");
print("of the amount you downloaded<p>It's important to maintain a good ");
print("ratio because it helps to make downloads faster for all members </p>");
print("<p><font color=red><b>Tip: </b></font>You can improve your ratio by leaving your torrent ");
print("running after the download completes.<p>You must maintain a minimum ");
print("ratio of 0.3 or your download privileges will be removed<p align=\"center\">");
print("</td></tr>");
}
else
if ($ratio <= 0.6 and (!$owned|| 0) and ($CURUSER["downloaded"] <> 0))
{
print("<p align=\"center\">");
print("<font color=red><b><u>Pay  Attention To Your Ratio</font><border=\"1\" cellpadding=\"10\" cellspacing=\"10\"></u></b>");
print("<p><font color=green><b>Your ratio is $ratio</b></font> - meaning that you have only uploaded ");
print("$percentage % ");
print("of the amount you downloaded<p>It's important to maintain a good ");
print("ratio because it helps to make downloads faster for all members</p>");
print("<p><font color=red><b>Tip: </b></font>You can improve your ratio by leaving your torrent ");
print("running after the download completes.<p>You must maintain a minimum ");
print(" ratio of 0.3 or your download privileges will be removed<p align=\"center\">");
print("<a class=\"index\" href=\"download.php/$id/" . rawurlencode($row["filename"]) . "\">");
print("<font color=green>Click Here To Continue With Your Download</a></font>");
print("</td></tr>");
}
else
{
print("<a class=\"index\" href=\"download.php/$id/" . rawurlencode($row["filename"]) . "\">" . safeChar($row["filename"]) . "</a>".$freeslot."");
}
print("<td></tr>");
}

if ($CURUSER["id"] == $row["owner"]) $CURUSER["downloadpos"] = "yes";
if ($CURUSER["downloadpos"] != "no")
{     
$ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
$percentage = ($ratio * 100);
print("<tr><td class=rowhead width=1%>Download Zip</td><td width=99% align=left>");
if (get_user_class() >= UC_VIP) {
print("<a class=\"index\" href=\"download_zip.php/$id/" . rawurlencode($row["filename"]) . "\">" . safeChar($row["filename"]) . "</a>".$freeslot."");
}
else {
$usid = $CURUSER["id"];
$rs = mysql_query("SELECT * FROM users WHERE id='$usid'") or sqlerr();
$ar = mysql_fetch_assoc($rs);
$gigs = $ar["downloaded"] / (1024*1024*1024);
if (($gigs > "10")&&($ratio <= 0.3 and (!$owned|| 0) and ($CURUSER["downloaded"] <> 0)))
{
print("<p align=\"center\">");
print("<font color=red><b><u>Download Privileges Removed Please Restart A Old Torrent To Improve Your Ratio!!</font><border=\"1\" cellpadding=\"10\" cellspacing=\"10\"></u></b>");
print("<p><font color=green><b>Your ratio is $ratio</b></font> - meaning that you have only uploaded ");
print("$percentage % ");
print("of the amount you downloaded<p>It's important to maintain a good ");
print("ratio because it helps to make downloads faster for all members </p>");
print("<p><font color=red><b>Tip: </b></font>You can improve your ratio by leaving your torrent ");
print("running after the download completes.<p>You must maintain a minimum ");
print("ratio of 0.3 or your download privileges will be removed<p align=\"center\">");
print("</td></tr>");
}
else
if ($ratio <= 0.6 and (!$owned|| 0) and ($CURUSER["downloaded"] <> 0))
{
print("<p align=\"center\">");
print("<font color=red><b><u>PAY ATTENTION TO YOUR RATIO</font><border=\"1\" cellpadding=\"10\" cellspacing=\"10\"></u></b>");
print("<p><font color=green><b>Your ratio is $ratio</b></font> - meaning that you have only uploaded ");
print("$percentage % ");
print("of the amount you downloaded<p>It's important to maintain a good ");
print("ratio because it helps to make downloads faster for all members</p>");
print("<p><font color=red><b>Tip: </b></font>You can improve your ratio by leaving your torrent ");
print("running after the download completes.<p>You must maintain a minimum ");
print(" ratio of 0.3 or your download privileges will be removed<p align=\"center\">");
print("<a class=\"index\" href=\"download.php/$id/" . rawurlencode($row["filename"]) . "\">");
print("<font color=green>Click Here To Continue With Your Download</a></font>");
print("</td></tr>");
}
else
{
print("<a class=\"index\" href=\"download_zip.php/$id/" . rawurlencode($row["filename"]) . "\">" . htmlspecialchars($row["filename"]) . "</a>".$freeslot."");
}
print("<td></tr>");
}
}
     if (get_user_class() >= UC_MODERATOR)
     print("<tr><td class=rowhead width=10>Download For Dump sites</td><td width=99% align=left><a class=\"index\" href=\"downloaddump.php/$id/" . rawurlencode($row["filename"]) . "\">" . safeChar($row["filename"]) . "</a></td></tr>");
     /// Mod by dokty - tbdev.net
        $blasd = sql_query("SELECT points FROM coins WHERE torrentid=$id AND userid=".unsafeChar($CURUSER["id"]));
        $sdsa = mysql_fetch_assoc($blasd) or $sdsa["points"] = 0;
        tr("Points","<b>In total ".safeChar($row["points"])." Points given to this torrent of which ".safeChar($sdsa["points"])." from you.<br /><br />By clicking on the coins you can give points to the uploader of this torrent.</b><br /><br /><a href=coins.php?id=$id&points=10><img src=pic/10coin.jpg border=0></a>&nbsp;&nbsp;<a href=coins.php?id=$id&points=20><img src=pic/20coin.jpg border=0></a>&nbsp;&nbsp;<a href=coins.php?id=$id&points=50><img src=pic/50coin.jpg border=0></a>&nbsp;&nbsp;<a href=coins.php?id=$id&points=100><img src=pic/100coin.jpg border=0></a>&nbsp;&nbsp;<a href=coins.php?id=$id&points=200><img src=pic/200coin.gif border=0></a>&nbsp;&nbsp;<a href=coins.php?id=$id&points=500><img src=pic/500coin.gif border=0></a>&nbsp;&nbsp;<a href=coins.php?id=$id&points=1000><img src=pic/1000coin.gif border=0></a>", 1);
		////////////end modified bonus points for uploader///////
		function hex_esc($matches) {
			return sprintf("%02x", ord($matches[0]));
		}
		tr("Info hash", preg_replace_callback('/./s', "hex_esc", hash_pad($row["info_hash"])));
        }
        else {
        tr("Download", "You are not allowed to download");
        }        
		tr("Picture", "<a href='".safeChar($row["poster"])."' rel='lightbox' title='".CutName(safeChar($row["name"]), 35)."'><img src='".safeChar($row["poster"])."' border=0 width=150></a><br>Click Image For Full Size", 1);   
	          	          
              if (($row["url"] != "")AND(strpos($row["url"], imdb))AND(strpos($row["url"], title)))
              {
              $thenumbers = ltrim(strrchr($row["url"],'tt'),'tt');
              $thenumbers = ereg_replace("[^A-Za-z0-9]", "", $thenumbers);
              $movie = new imdb ($thenumbers);
              $movieid = $thenumbers;
              $movie->setid ($movieid);
              $country = $movie->country ();
              $director = $movie->director();
              $write = $movie->writing();
              $produce = $movie->producer();
              $cast = $movie->cast();
              $plot = $movie->plot ();
              $compose = $movie->composer();
              $gen = $movie->genres();
              
              if (($photo_url = $movie->photo_localurl() ) != FALSE) {
              $smallth = '<img src="'.$photo_url.'">';
              }

            
              $autodata .= "<font color=\"red\" size=\"3\">Information:</font><br />\n";
              $autodata .= "<br />\n";
              $autodata .= "<strong><font color=\"red\"> Title: </font></strong>" . "".$movie->title ()."<br />\n";
              $autodata .= "<strong><font color=\"red\"> Also known as: </font></strong>";

              foreach ( $movie->alsoknow() as $ak){
              $autodata .= "".$ak["title"]."" . "".$ak["year"].""  . "".$ak["country"]."" . " (" . "".$ak["comment"]."" . ")" . ", ";
              }
              $autodata .= "<br />\n<strong><font color=\"red\"> Year: </font></strong>" . "".$movie->year ()."<br />\n";
              $autodata .= "<strong><font color=\"red\"> Runtime: </font></strong>" . "".$movie->runtime ()."" . " mins<br />\n";
              $autodata .= "<strong><font color=\"red\"> Votes: </font></strong>" . "".$movie->votes ()."<br />\n";
              $autodata .= "<strong><font color=\"red\"> Rating: </font></strong>" . "".$movie->rating ()."<br />\n";
              $autodata .= "<strong><font color=\"red\"> Language: </font></strong>" . "".$movie->language ()."<br />\n";
              $autodata .= "<strong><font color=\"red\"> Country: </font></strong>";
                      
              for ($i = 0; $i + 1 < count ($country); $i++) {
              $autodata .="$country[$i], ";
              }
              $autodata .= "$country[$i]";
              $autodata .= "<br />\n<strong><font color=\"red\"> All Genres: </font></strong>";
              for ($i = 0; $i + 1 < count($gen); $i++) {
              $autodata .= "$gen[$i], ";
              }
              $autodata .= "$gen[$i]";
              $autodata .= "<br />\n<strong><font color=\"red\"> Tagline: </font></strong>" . "".$movie->tagline ()."<br />\n";
              $autodata .= "<strong><font color=\"red\"> Director: </font></strong>";

              for ($i = 0; $i < count ($director); $i++) {
              $autodata .= "<a target=\"_blank\" href=\"http://us.imdb.com/Name?" . "".$director[$i]["imdb"]."" ."\">" . "".$director[$i]["name"]."" . "</a> ";
              }
        
              $autodata .= "<br />\n<strong><font color=\"red\"> Writing By: </font></strong>";
              for ($i = 0; $i < count ($write); $i++) {
              $autodata .= "<a target=\"_blank\" href=\"http://us.imdb.com/Name?" . "".$write[$i]["imdb"]."" ."\">" . "".$write[$i]["name"]."" . "</a> ";
              }
        
              $autodata .= "<br />\n<strong><font color=\"red\"> Produced By: </font></strong>";
              for ($i = 0; $i < count ($produce); $i++) {
              $autodata .= "<a target=\"_blank\" href=\"http://us.imdb.com/Name?" . "".$produce[$i]["imdb"]."" ." \">" . "".$produce[$i]["name"]."" . "</a> ";
              }
              
              $autodata .= "<br />\n<strong><font color=\"red\"> Music: </font></strong>";              
              for ($i = 0; $i < count($compose); $i++) {
              $autodata .= "<a target=\"_blank\" href=\"http://us.imdb.com/Name?" . "".$compose[$i]["imdb"]."" ." \">" . "".$compose[$i]["name"]."" . "</a> ";     
              }

              $autodata .= "<br /><br />\n\n<br />\n";
              $autodata .= "<font color=\"red\" size=\"3\"> Description:</font><br />\n";
              for ($i = 0; $i < count ($plot); $i++) {
              $autodata .= "<br />\n<font color=\"red\">•</font> ";
              $autodata .= "$plot[$i]";
              }      
    
              $autodata .= "<br /><br />\n\n<br />\n";
              $autodata .= "<font color=\"red\" size=\"3\"> Cast:</font><br />\n";
              $autodata .= "<br />\n";

              for ($i = 0; $i < count ($cast); $i++) {
              if ($i > 9) {
                break;
              }
              $autodata .= "<font color=\"red\">•</font> " . "<a target=\"_blank\" href=\"http://us.imdb.com/Name?" . "".$cast[$i]["imdb"]."" ."\">" . "".$cast[$i]["name"]."" . "</a> " . " as <strong><font color=\"red\">" . "".$cast[$i]["role"]."" . " </font></strong><br />\n";
              
               }

               trala("Imdb Info $smallth",$autodata,1);
                 }
                //end auto imdb
                /////////////youtube sample scriptulicious style//////////
                if (!empty($row["tube"]))
                tr("Sample", "<font size=1 align=center onclick=\"Effect.toggle('samp','blind'); return false\"><img border=\"0\"src=\"pic/show.gif\" id=\"pica".$array['id']."\" alt=\"[Hide/Show]\"></font><div id=\"samp\" style=\"display:none;\"><br><embed src='". str_replace("watch?v=", "v/", safeChar($row["tube"])) ."' type=\"application/x-shockwave-flash\" width=\"500\" height=\"410\"></embed></div>", 1);
                else
                tr("Sample", "<font size=1 align=center onclick=\"Effect.toggle('nosamp','blind'); return false\"><img border=\"0\"src=\"pic/show.gif\" id=\"pica".$array['id']."\" alt=\"[Hide/Show]\"></font><div id=\"nosamp\" style=\"display:none;\"><br>Currently No Sample Available.</div>", 1);
                /////////////////end youtube///////
				/////////script
                if (!empty($row["descr"]))
                tr("Description", "<font size=1 align=center onclick=\"Effect.toggle('descr','blind'); return false\"><img border=\"0\"src=\"pic/show.gif\" id=\"pica".$array['id']."\" alt=\"[Hide/Show]\"></font><div id=\"descr\" style=\"display:none;\"><br />".format_comment($row["descr"])."</div>", 1, 1);
                //AUTO VIEWNFO
                if (empty($row["descr"])){
                $r = sql_query("SELECT name,nfo FROM torrents WHERE id=$id") or sqlerr();
                $a = mysql_fetch_assoc($r) or die("Puke");
                $nfo = safeChar($a["nfo"]);
                print("<h1>NFO for <a href=details.php?id=$id>$a[name]</a></h1>\n");
                print("<tr><td valign=top alighn=center><b>Description</b></td><td class=text>\n");
                print("<pre><font face='MS Linedraw' size=2 style='font-size: 10pt; line-height: 10pt'>" . format_urls($nfo) . "</font></pre>\n");print("</td></tr>\n");}
                //AUTO VIEWNFO
                if (get_user_class() >= UC_POWER_USER && $row["nfosz"] > 0)
                print("<tr><td class=rowhead>NFO</td><td align=left><a href=viewnfo.php?id=$row[id]><b>View NFO</b></a> (" .
                mksize($row["nfosz"]) . ")</td></tr>\n");
                if ($row["visible"] == "no")
			    tr("Visible", "<b>no</b> (dead)", 1);
		        if ($moderator)
			    tr("Banned", $row["banned"]);
                if ($row["nuked"] == "yes")
                tr("Nuked", $row["nukereason"]);
                elseif ($row["nuked"] == "unnuked")
                tr("Un-nuked", $row["nukereason"]);
                else
                if ($row["nuked"] == "no");
		        if (isset($row["cat_name"]))
			    tr("Type", $row["cat_name"]);
		        else
			    tr("Type", "(none selected)");
                tr("Genre", $row["newgenre"], 1);
   		        tr("Last&nbsp;seeder", "Last activity " . safeChar(mkprettytime($row["lastseed"])) . " ago");
		        tr("Size",mksize($row["size"]) . " (" . safeChar(number_format($row["size"])) . " bytes)");

		        $s = "";
		        $s .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td valign=\"top\" class=embedded>";
		        if (!isset($row["rating"])) {
			    if ($minvotes > 1) {
				$s .= "none yet (needs at least $minvotes votes and has got ";
				if ($row["numratings"])
					$s .= "only " . $row["numratings"];
				else
			    $s .= "none";
				$s .= ")";
			    }
			    else
				$s .= "No votes yet";
		        }
		        else {
			    $rpic = ratingpic($row["rating"]);
			    if (!isset($rpic))
				$s .= "invalid?";
			    else
				$s .= "$rpic (" . $row["rating"] . " out of 5 with " . $row["numratings"] . " vote(s) total)";
		        }
		        $s .= "\n";
		        $s .= "</td><td class=embedded>$spacer</td><td valign=\"top\" class=embedded>";
		        if (!isset($CURUSER))
			    $s .= "(<a href=\"login.php?returnto=" . urlencode(substr($_SERVER["REQUEST_URI"],1)) . "&amp;nowarn=1\">Log in</a> to rate it)";
		        else {
			    $ratings = array(
				5 => "Kewl!",
				4 => "Pretty good",
				3 => "Decent",
				2 => "Pretty bad",
				1 => "Sucks!",
			    );
			    if (!$owned || $moderator) {
				$xres = sql_query("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
				$xrow = mysql_fetch_assoc($xres);
				if ($xrow)
					$s .= "(you rated this torrent as \"" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "\")";
				else {
					$s .= "<form method=\"post\" action=\"takerate.php\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
					$s .= "<select name=\"rating\">\n";
					$s .= "<option value=\"0\">(add rating)</option>\n";
					foreach ($ratings as $k => $v) {
						$s .= "<option value=\"$k\">$k - $v</option>\n";
					}
					$s .= "</select>\n";
					$s .= "<input type=\"submit\" value=\"Vote!\" />";
					$s .= "</form>\n";
				}
			}
		}
		$s .= "</td></tr></table>";
		tr("Rating", $s, 1);
		if (get_user_class() >= UC_MODERATOR)
{ 
if (!$_GET["ratings"])

tr("Get Ratings<br /><a href=\"details.php?id=$id&amp;ratings=1$keepget#ratings\" class=\"sublink\">[See full list]</a>", $row["numratings"] . " ratings", 1);

else {
tr("Get Ratings", $row["numratings"] . " ratings", 1);
$s = "<table class=main border=\"1\" cellspacing=0 cellpadding=\"5\">\n";
$ratings = sql_query("SELECT r.rating, r.added, u.username,u.id
FROM ratings AS r
INNER JOIN users AS u ON r.user = u.id
WHERE r.torrent =$id
ORDER BY u.username DESC");
$s.="<tr><td class=colhead>User</td><td class=colhead align=right>rate</td><td class=colhead align=right>Date</td></tr>\n";

while ($r_row = mysql_fetch_assoc($ratings)) {

$s .= "<tr><td><a href=userdetails.php?id=".$r_row["id"].">".htmlspecialchars($r_row["username"])."</a></td><td align=\"right\">" . $r_row["rating"] . "</td><td align=\"right\">" . date("d-m-Y",strtotime($r_row["added"])) . "</td></tr>\n";

}
$s .= "</table>\n";

tr("<a name=\"filelist\">Rating's</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[Hide list]</a>", $s, 1);

}
}
		/////////////Vote For FreeLeech////////
   if ($CURUSER["class"] < UC_VIP)
   {
    $ratio1 = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
    if ($ratio1 < 0.55) $wait1 = 5;
    elseif ($ratio1 < 0.45) $wait1 = 10;
    elseif ($ratio1 < 0.35) $wait1 = 15;
    elseif ($ratio1 < 0.25) $wait1 = 20;
    elseif ($ratio1 < 0.15) $wait1 = 25;
    else $wait1 = 0;
    }
    $elapsed1 = floor((time() - strtotime($row["added"])) / 3600);
    $torrentid = 0 + $row["id"];
    $freepoll_sql = mysql_query("SELECT userid FROM freepoll where torrentid=".unsafeChar($torrentid)."");
    $freepoll_all = mysql_numrows($freepoll_sql);
    if ($freepoll_all) {
    while($rows_t = mysql_fetch_array($freepoll_sql)) {
    $freepoll_userid = $rows_t["userid"];
    $user_sql = mysql_query("SELECT id, username FROM users where id=".unsafeChar($freepoll_userid)."");
    $rows_a = mysql_fetch_array($user_sql);
    $username_t = $rows_a["username"];
    $freepollby1 =  $freepollby1."<a href='userdetails.php?id=$freepoll_userid'>$username_t</a>, ";
    }   
    $t_userid = 0 + $CURUSER["id"];
    $tsqlcount = mysql_query("SELECT COUNT(*) as tcount FROM freepoll where torrentid=".unsafeChar($torrentid)."");
    $tass = mysql_fetch_assoc($tsqlcount);
    $freepollcount = $tass["tcount"];   
    $tsql = mysql_query("SELECT COUNT(*) FROM freepoll where torrentid=".unsafeChar($torrentid)." and userid=".unsafeChar($t_userid)."");
    $trows = mysql_fetch_array($tsql);
    $t_ab = $trows[0];    
    if ($t_ab == "0") {
    $freepollby = $freepollby." <form action=\"freepoll.php\" method=\"post\">
    <br />
    <input type=\"submit\" name=\"submit\" value=\"Vote\">
    <input type=\"hidden\" name=\"torrentid\" value=\"$torrentid\">
    </form>";
    } else {
    $t_userid == $row["owner"];
    $freepollby = $freepollby." <form action=\"freepoll.php\" method=\"post\">
    <br />    
    <input type=\"submit\" name=\"submit\" value=\"Already voted\" disabled>
    <input type=\"hidden\" name=\"torrentid\" value=\"$torrentid\">
    </form>";
    }
    } else {
    $freepollcount = "0";
    $freepollby = "
    <form action=\"freepoll.php\" method=\"post\">
    <br />
    <input type=\"submit\" name=\"submit\" value=\"Vote\">
    <input type=\"hidden\" name=\"torrentid\" value=\"$torrentid\">
    </form>
    ";
    }        
    $votesrequired= "15";
    $count = $votesrequired-$freepollcount;    
    if ($row["countstats"] == 'yes'){
    tr("Free Leech Vote<b></b>","".safechar($freepollcount)." member(s) would like this torrent to be free leech. ".safeChar($count)." vote(s) required still.",1);
    }     
    if ($elapsed < $wait AND ($row["countstats"]) == 'yes')        
    if ($t_ab == "0" AND ($row["countstats"]) == 'yes'){
    if($freepollcount < $votesrequired )
    print("<tr><td class=rowhead><div align='right'>Free Leech Vote</div></td><td align=left>$freepollby");
    }
    else
    print("<tr><td class=rowhead><div align='right'>Sorry</div></td><td align=left>Your ratio is poor, you have to wait for it because of this <b><a href=rules.php><font color=red>" . number_format($wait1 - $elapsed1) . " hours</font></b></a>!");
    elseif($row["countstats"] == 'yes')
    print("<tr><td class=rowhead><div align='right'>Vote</div></td><td align=left>$freepollby");    
    $tid = $row["id"];
    if($freepollcount == $votesrequired || $row["countstats"] == 'no'){
    print("<tr><td class=rowhead><div align='right'>Free Poll</div></td><td align=left>This torrent is currently free leech");
    mysql_query("UPDATE torrents SET countstats = 'no' WHERE torrents.id=".unsafeChar($tid)."") or sqlerr(__FILE__, __LINE__);}
    if($freepollcount < $votesrequired AND $row["countstats"] == 'yes')
    print("<tr><td class=rowhead><div align='right'>Free Poll</div></td><td align=left>This torrent is not free leech");
    ///////////////////end vote for freeleech//////////////////////
       $doubles = ($double_for_all ? '<tr><td align=right class=rowhead>Doubleseed</td>
                 <td align=left><img src='.$pic_base_url.'doubleseed.gif title=Doubleseed alt=Doubleseed />
                 <b><font size="2" color="#FF0000">DoubleSeed Torrent</font></b> <small><b>(upload stats count double)</b></small></td></tr>' : '');
				 echo $doubles;
				 $freebies= ((in_array($row["cat_id"], $freecat)) ? '<tr><td align=right class=rowhead>Free</td>
                  <td align=left><img src='.$pic_base_url.'freedownload.gif title=Free alt=Free />
                  <b><font size="2" color="#FF0000">Free Category Torrent</font></b> <small><b>(only upload stats are recorded)</b></small></td></tr>' : '');
                 echo $freebies;
	 tr("Added", $row["added"]);
		tr("Views", $row["views"]);
		tr("Hits", $row["hits"]);
		if (get_user_class() >= UC_MODERATOR) {
		tr("Snatched", ($row["times_completed"] > 0 ? "<a href=snatches.php?id=$id>".safeChar($row[times_completed])." time(s)</a>" : "0 times"), 1);
        }
        else
        tr("Snatched", ($row["times_completed"] > 0 ? "".safeChar($row[times_completed])." time(s)</a>" : "0 times"), 1);
                                // Totaltraffic mod
                                $data = sql_query("SELECT (t.size * t.times_completed + SUM(p.downloaded) + t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.uploaded > '0' AND p.downloaded > '0' AND p.torrent = '$id' AND times_completed > 0 GROUP BY t.id ORDER BY added ASC LIMIT 15") or sqlerr(__FILE__, __LINE__);
                                $a = mysql_fetch_assoc($data);
                                $data = mksize($a["data"]) . "";  
                                 tr("Totaltraffic", $data);
                                // Progressbar Mod
                                $seedersProgressbar = array();
                                $leechersProgressbar = array();
                                $resProgressbar = sql_query("SELECT p.seeder, p.to_go, t.size FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.torrent = '$id'") or sqlerr();
                                $progressPerTorrent = 0;
                                $iProgressbar = 0;
                                while ($rowProgressbar = mysql_fetch_array($resProgressbar)) {
                                $progressPerTorrent += sprintf("%.2f", 100 * (1 - ($rowProgressbar["to_go"] / $rowProgressbar["size"])));
                                $iProgressbar++;
                                 }
                                 if ($iProgressbar == 0)
                                 $iProgressbar = 1;
                                 $progressTotal = sprintf("%.2f", $progressPerTorrent / $iProgressbar);
                                 tr("Progress", get_percent_completed_image(floor($progressTotal))." (".round($progressTotal)."%)", 1);
                                 //Progressbar Mod End
 		 $keepget = "";
                                 if($row['anonymous'] == 'yes') {
                                 if (get_user_class() < UC_UPLOADER)
                                 $uprow = "<i>Anonymous</i>";
                                 else
                                 $uprow = "<i>Anonymous</i> (<a href=userdetails.php?id=$row[owner]><b>$row[username]</b></a>)";
                                 }
                                 else {
                                 $uprow = (isset($row["username"]) ? ("<a href=userdetails.php?id=" . $row["owner"] . "><b>" . safeChar($row["username"]) . "</b></a>") : "<i>(unknown)</i>");
                                 }
                                 if ($owned)
			                     $uprow .= " $spacer<$editlink><b>[Edit this torrent]</b></a>";
		                         tr("Upped by", $uprow, 1);
                                 // start torrent mod check
                                 if ($CURUSER['class'] >= UC_MODERATOR)
                                 {
                                 if (!empty($row['checked_by']))
                                 {
                                 $checked_by = mysql_query("SELECT id FROM users WHERE username='$row[checked_by]'");
                                 $checked = mysql_fetch_array($checked_by);
                                 ?>
                                 <tr><td class='rowhead'>Checked by</td><td align='left'><a href='userdetails.php?id=<?php echo $checked['id']?>'><strong><?php echo $row['checked_by']?></strong></a>&nbsp;
                                 <img src='<?php echo $pic_base_url?>mod.gif' width='15' border='0' alt='Checked' title='Checked - by <?php echo safe($row['checked_by'])?>' />
                                 <a href='details.php?id=<?php echo $row['id']?>&amp;rechecked=1'><small><em><strong>[Re-Check this torrent]</strong></em></small></a> &nbsp;<a href='details.php?id=<?php echo $row['id']?>&amp;clearchecked=1'><small><em><strong>[Un-Check this torrent]</strong></em></small></a> &nbsp;* STAFF Eyes Only *</td></tr>
                                 <?php
                                 }
                                 else
                                 {
                                 ?>
                                 <tr><td class='rowhead'>Checked by</td><td align='left'><font color='#ff0000'><strong>NOT CHECKED!</strong></font>&nbsp;<a href='details.php?id=<?php echo $row['id']?>&amp;checked=1'><small><em><strong>[Check this torrent]</strong></em></small></a> &nbsp;* STAFF Eyes Only *</td></tr>
                                 <?php    
                                 }
                                 }
                                 ////////////////////// torrent check end - pdq
                                 $bookmarks = get_row_count("bookmarks","WHERE torrentid=".$id." AND private ='no'");
                                 if ($bookmarks > 0)
                                 tr("Bookmarked", "<a href=\"viewbookmarks.php?id=".$id."\">$bookmarks".($bookmarks == 1  ? " time</a>" : " times</a>"),1);
                                 else
                                 tr("Bookmarked", "not yet");

                if ($row["type"] == "multi") {
                if (!$_GET["filelist"])
                tr("Num files<br /><a href=\"details.php?id=$id&amp;filelist=1$keepget#filelist\" class=\"sublink\">[See full list]</a>", $row["numfiles"] . " files", 1);
                else {
                tr("Num files", $row["numfiles"] . " files", 1);

                $s = "<table width=500 class=colorss class=main border=\"1\" cellspacing=0 cellpadding=\"5\">\n";

                $subres = sql_query("SELECT * FROM files WHERE torrent = $id ORDER BY id");

                $s.="<tr><td width=500 class=colhead>Type</td><td class=colhead>Path</td><td class=colhead align=right>Size</td></tr>\n";
                while ($subrow = mysql_fetch_array($subres)) {
                     preg_match('/\\.([A-Za-z0-9]+)$/', $subrow["filename"], $ext);
                        $ext = strtolower($ext[1]);
                        if (!file_exists("pic/icons/".$ext.".png")) $ext = "Unknown";
                $s .= "<tr><td align\"center\"><img align=center src=\"pic/icons/".$ext.".png\" alt=\"$ext file\"></td><td class=tableb2 width=700>" . safeChar($subrow["filename"]) ."</td><td align=\"right\">" . mksize($subrow["size"]) . "</td></tr>\n";
                }

                $s .= "</table>\n";
                tr("<a name=\"filelist\">File list</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[Hide list]</a>", $s, 1);
                }
                }
            if (!$_GET["dllist"]) {
			tr("Report Torrent:", "<form action=report.php?type=Torrent&id=$id method=post><input class=button type=submit name=submit value=\"Report This Torrent\"> for breaking the <a href=rules.php>rules</a></form>", 1);
            tr("Peers<br /><a href=\"details.php?id=$id&amp;dllist=1$keepget#seeders\" class=\"sublink\">[See full list]</a>", $row["seeders"] . " seeder(s), " . $row["leechers"] . " leecher(s) = " . ($row["seeders"] + $row["leechers"]) . " peer(s) total", 1);
		    if ($row["seeders"] == 0){
            print("<form method=post action=takereseed.php?reseedid=$id><tr><td align=center class=clearalt4 colspan=2><table><tr><td align=center class=clearalt4>".
            "<input class=button type=submit value='Request Reseed'></td></form></td></tr></table></td></tr>");
            }
            }
		    else {
			$downloaders = array();
			$seeders = array();
			$subres = sql_query("SELECT seeder, finishedat, downloadoffset, uploadoffset, ip, port, uploaded, downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable, agent, UNIX_TIMESTAMP(last_action) AS la, userid, peer_id FROM peers WHERE torrent =$id") or sqlerr();
			while ($subrow = mysql_fetch_assoc($subres)) {
				if ($subrow["seeder"] == "yes")
					$seeders[] = $subrow;
				else
					$downloaders[] = $subrow;
			}

			function leech_sort($a,$b) {
                                if ( isset( $_GET["usort"] ) ) return seed_sort($a,$b);				
                                $x = $a["to_go"];
				$y = $b["to_go"];
				if ($x == $y)
					return 0;
				if ($x < $y)
					return -1;
				return 1;
			}
			function seed_sort($a,$b) {
				$x = $a["uploaded"];
				$y = $b["uploaded"];
				if ($x == $y)
					return 0;
				if ($x < $y)
					return 1;
				return -1;
			}

			usort($seeders, "seed_sort");
			usort($downloaders, "leech_sort");

			tr("<a name=\"seeders\">Seeders</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[Hide list]</a>", dltable("Seeder(s)", $seeders, $row), 1);
			tr("<a name=\"leechers\">Leechers</a><br /><a href=\"details.php?id=$id$keepget\" class=\"sublink\">[Hide list]</a>", dltable("Leecher(s)", $downloaders, $row), 1);
            }
if (get_user_class() >= UC_ADMINISTRATOR)
{
$filename = "include/banned_clients.txt";
if (filesize($filename)==0 || !file_exists($filename))
$banned_clients=array();
else
{
$handle = fopen($filename, "r");
$banned_clients = unserialize(fread($handle, filesize($filename)));
fclose($handle);
}
if(!empty($banned_clients))
print("<tr><td class=rowhead>Banned Clients</td><td align=left><a href='client_clearban.php?returnto=".urlencode("details.php?id=".$row["id"])."'><b>Click Here to remove client bans</b></a></td></tr>");
}
print("</table></p>\n");
	}
	else {
		stdhead("Comments for torrent \"" . safeChar($row["name"]) . "\"");
		print("<h1>Comments for <a href=details.php?id=$id>" . safeChar($row["name"]) . "</a></h1>\n");
//		print("<p><a href=\"details.php?id=$id\">Back to full details</a></p>\n");
	}

	print("<p><a name=\"startcomments\"></a></p>\n");

        $postallowed = 1;
        if ($CURUSER['comment_max'] == 0) $postallowed = 0;
        if ($postallowed AND (!($CURUSER['comment_count'] < $CURUSER['comment_max']))) $postallowed = 2;

    switch ($postallowed) {

        case 0:
            $commentbar = "<p align=center>Your posting privilege has been revoked!</p>\n";
            break;
        case 1:
            $commentbar = "<p align=center><a class=index href=comment.php?action=add&tid=$id>Add a comment</a></p>\n <a class=index href=takethankyou.php?id=$id> <img src=".$pic_base_url."thankyou.gif border=0></a></p>";
            break;
        case 2:
            $commentbar = "<p align=center>You have reached your Comment limit. Please wait 15 minutes before retrying.</p>\n";
        default:
            die('Contact Administrator');
            break;
    }

$subres = sql_query("SELECT COUNT(*) FROM comments WHERE torrent = ".unsafeChar($id)."");
$subrow = mysql_fetch_array($subres);
$count = $subrow[0];



$tures = sql_query("SELECT id,username FROM users,thanks WHERE users.id = thanks.uid AND thanks.torid = ".unsafeChar($id)."");


begin_main_frame();
end_main_frame();


if (!$count) {
		print("<h2>No comments yet</h2>\n");
	}
	else {
		list($pagertop, $pagerbottom, $limit) = pager(20, $count, "details.php?id=$id&", array(lastpagedefault => 1));

		$subres = sql_query("SELECT comments.id, text, user, comments.added, comments.anonymous, editedby, editedat, avatar, warned, ".
                  "username, title, class, signature, signatures, donor FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = " .
                  "$id ORDER BY comments.id $limit") or sqlerr(__FILE__, __LINE__);
		$allrows = array();
		while ($subrow = mysql_fetch_assoc($subres))
			$allrows[] = $subrow;

		print($commentbar);
		print($pagertop);

		commenttable($allrows);

		print($pagerbottom);
	}

	print($commentbar);
}

stdfoot();

?>
