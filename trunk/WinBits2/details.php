<?

require_once("include/bittorrent.php");

dbconn(false);

loggedinorreturn();

$BASEURL = "http://localhost/WinBits2";

$id = $_GET["id"];
$id = 0 + $id;
if (!isset($id) || !$id)
	die();

$res = mysql_query("SELECT torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, LENGTH(torrents.nfo) AS nfosz, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(torrents.last_action) AS lastseed, torrents.numratings, torrents.name, IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.numfiles, categories.name AS cat_name, users.username FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id")
	or sqlerr();
$row = mysql_fetch_array($res);

$owned = $moderator = 0;
	if (get_user_class() >= UC_MODERATOR)
		$owned = $moderator = 1;
	elseif ($CURUSER["id"] == $row["owner"])
		$owned = 1;
		
if (!$row || ($row["banned"] == "yes" && !$moderator))
	stderr("Error", "No torrent with ID $id.");
else {
	if ($_GET["hit"]) {
		mysql_query("UPDATE torrents SET views = views + 1 WHERE id = $id");
		header("Location: $BASEURL/details.php?id=$id");
	}
}	
	
stdhead("Details for torrent \"" . $row["name"] . "\"");


		if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
			$owned = 1;
		else
			$owned = 0;

		$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		if (isset($_GET["uploaded"])) {
			print("<h2>Successfully uploaded!</h2>\n");
			print("<p>You can start seeding now. <b>Note</b> that the torrent won't be visible until you do that!</p>\n");
		}
		elseif (isset($_GET["edited"])) {
			print("<h2>Successfully edited!</h2>\n");
			if (isset($_GET["returnto"]))
				print("<p><b>Go back to <a href=\"" . htmlspecialchars($_GET["returnto"]) . "\">whence you came</a>.</b></p>\n");
		}
		elseif (isset($_GET["searched"])) {
			print("<h2>Your search for \"" . htmlspecialchars($_GET["searched"]) . "\" gave a single result:</h2>\n");
		}
		elseif (isset($_GET["rated"]))
			print("<h2>Rating added!</h2>\n");
		
		
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
		
		print("<tr><td class=rowhead width=1%>Download</td><td width=99% align=left><a class=\"index\" href=\"download.php/$id/" . rawurlencode($row["filename"]) . "\">" . htmlspecialchars($row["filename"]) . "</a></td></tr>");
//		tr("Downloads&nbsp;as", $row["save_as"]);

		function hex_esc($matches) {
			return sprintf("%02x", ord($matches[0]));
		}
		tr("Info hash", preg_replace_callback('/./s', "hex_esc", hash_pad($row["info_hash"])));

		if (!empty($row["descr"]))
			tr("Description", str_replace(array("\n", "  "), array("<br>\n", "&nbsp; "), format_urls(htmlspecialchars($row["descr"]))), 1);
			
		if ($row["type"] == "multi") {
		$s = "<tr><td>Filelist</td></tr><tr><td>";
		$s .= "<table class=main border=\"1\" cellspacing=0 cellpadding=\"5\">\n";

				$subres = mysql_query("SELECT * FROM files WHERE torrent = $id ORDER BY id");
$s.="<tr><td class=colhead>Path</td><td class=colhead align=right>Size</td></tr>\n";
				while ($subrow = mysql_fetch_array($subres)) {
					$s .= "<tr><td>" . $subrow["filename"] .
                            "</td><td align=\"right\">" . mksize($subrow["size"]) . "</td></tr>\n";
				}

				$s .= "</td></tr></table>\n";
				}
			else
				{
				print("<tr><td class=\"rowhead\">File Count</td><td>1</td></tr>");
				}
if (get_user_class() >= UC_POWER_USER && $row["nfosz"] > 0)
  print("<tr><td class=rowhead>NFO</td><td align=left><a href=viewnfo.php?id=$row[id]><b>View NFO</b></a> (" .
     mksize($row["nfosz"]) . ")</td></tr>\n");
		if ($row["visible"] == "no")
			tr("Visible", "<b>no</b> (dead)", 1);
		if ($moderator)
			tr("Banned", $row["banned"]);

		if (isset($row["cat_name"]))
			tr("Type", $row["cat_name"]);
		else
			tr("Type", "(none selected)");

		tr("Last seeder", "Last activity " . mkprettytime($row["lastseed"]) . " ago");
		tr("Size",mksize($row["size"]) . " (" . number_format($row["size"]) . " bytes)");
		
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
			$s .= "(<a href=\"login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;nowarn=1\">Log in</a> to rate it)";
		else {
			$ratings = array(
					5 => "Kewl!",
					4 => "Pretty good",
					3 => "Decent",
					2 => "Pretty bad",
					1 => "Sucks!",
			);
			if (!$owned || $moderator) {
				$xres = mysql_query("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
				$xrow = mysql_fetch_array($xres);
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
		
		tr("Added", $row["added"]);
		tr("Views", $row["views"]);
		tr("Hits", $row["hits"]);
		tr("Snatched", $row["times_completed"] . " time(s)");

		$keepget = "";
		$uprow = (isset($row["username"]) ? ("<a href=userdetails.php?id=" . $row["owner"] . "><b>" . htmlspecialchars($row["username"]) . "</b></a>") : "<i>unknown</i>");
		if ($owned)
			$uprow .= " $spacer<$editlink><b>[Edit this torrent]</b></a>";
		tr("Upped by", $uprow, 1);

		
		
		tr("Peers<br /><a href=\"details.php?id=$id&amp;dllist=1$keepget#seeders\" class=\"sublink\">[See full list]</a>", $row["seeders"] . " seeder(s), " . $row["leechers"] . " leecher(s) = " . ($row["seeders"] + $row["leechers"]) . " peer(s) total", 1);
print("</table></p>\n");
stdfoot();

?>