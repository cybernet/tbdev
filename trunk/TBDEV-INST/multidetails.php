<?php

if(!extension_loaded('zlib')){
	ob_start("ob_gzhandler");
}

require_once("include/bittorrent.php");
dbconn(false);

loggedinorreturn();

stdhead("Multi Upload");
		
		print("<table border=1 cellpadding=4 width=35%> ");
		$ids = array();
		
		$ids[] = 0 + $_GET["id1"];
		$ids[] = 0 + $_GET["id2"];
		$ids[] = 0 + $_GET["id3"];
		$ids[] = 0 + $_GET["id4"];
		$ids[] = 0 + $_GET["id5"];
		//this is the page which is displayed if the uploader has just uplaoded the torrents//
		if (array_key_exists('uploaded', $_GET) && $_GET["uploaded"]) {
			print("<h2>Successfully uploaded!</h2>\n");
			print("<p>You can start downloading them now and start seeding. <b>Note</b> that the torrent won't be visible until you do that!</p>\n");
			
			$res = mysql_query("SELECT torrents.filename FROM torrents WHERE torrents.id=$ids[0] OR torrents.id=$ids[1] OR torrents.id=$ids[2] OR torrents.id=$ids[3] OR torrents.id=$ids[4];") or sqlerr();
			
			$i = 0;
			while($row = mysql_fetch_array($res)){
			print("<tr><td><a class=\"index\" href=\"download.php/$ids[$i]/" . rawurlencode($row["filename"]) . "\">" . htmlspecialchars($row["filename"]) . "</a></td></tr>");
			$i++;
			}
		print("</table>");
		//this is the page which is displayed when a user views the uploaded torrents from the shoutbox link//
		} else {
			print("<h2>New Torrents have been Uploaded!</h2>\n");
			print("<p>Click on the Torrents below to see the full description or alternatively click the 'Download' Button to Download..now</p>\n");
			
			$res = mysql_query("SELECT * FROM torrents WHERE torrents.id=$ids[0] OR torrents.id=$ids[1] OR torrents.id=$ids[2] OR torrents.id=$ids[3] OR torrents.id=$ids[4];") or sqlerr();
			print("<tr><td class='colhead'><b>Torrent Description</b></td><td class='colhead'><img src=\"pic/download.gif\" /></td></tr>");
			$i = 0;
			while($row = mysql_fetch_array($res)){
			print("<tr><td><a class=\"index\" href='http://www.scene-base.net/details.php?id=$ids[$i]'\>" . htmlspecialchars($row["name"]) . "</a></td><td><a class=\"index\" href=\"download.php/$ids[$i]/" . rawurlencode($row["filename"]) . "\"><img src=\"pic/download.gif\" /></a></td></tr>");
			$i++;
			}
		print("</table>");
		print("<br />");
		}
stdfoot();

?>