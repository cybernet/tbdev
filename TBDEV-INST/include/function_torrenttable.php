<?php
require_once("include/user_functions.php");
function torrenttable($res, $variant = "index") {
	global $pic_base_url, $CURUSER;
    if ($CURUSER["class"] < UC_USER)
  {
	  $gigs = $CURUSER["uploaded"] / (1024*1024*1024);
	  $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
	  if ($ratio < 0.5 || $gigs < 5) $wait = 0;
	  elseif ($ratio < 0.65 || $gigs < 6.5) $wait = 0;
	  elseif ($ratio < 0.8 || $gigs < 8) $wait = 0;
	  elseif ($ratio < 0.95 || $gigs < 9.5) $wait = 0;
      else $wait = 0;
      }
?>
<table border="1" cellspacing=0 cellpadding=5>
<tr>
<?php
// sorting by MarkoStamcar // modified by xuzo :))
$count_get = 0;
foreach ($_GET as $get_name => $get_value)
{
	$get_name = mysql_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_name)));
	$get_value = mysql_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_value)));
	
	if ($get_name != "sort" && $get_name != "type")
	{
		$oldlink = ($count_get > 0 ? $oldlink . '&amp;' . $get_name . '=' . $get_value : $oldlink . $get_name . '=' . $get_value);
		++$count_get;
	}
}
if ($count_get > 0)
	$oldlink = $oldlink . '&amp;';
if ($_GET['sort'] == "1")
	$link1 = ($_GET['type'] == 'desc' ? 'asc' : 'desc');
if ($_GET['sort'] == "2") 
	$link2 = ($_GET['type'] == 'desc' ? 'asc' : 'desc');
if ($_GET['sort'] == "3") 
	$link3 = ($_GET['type'] == 'desc' ? 'asc' : 'desc');
if ($_GET['sort'] == "4") 
	$link4 = ($_GET['type'] == 'desc' ? 'asc' : 'desc');
if ($_GET['sort'] == "5") 
	$link5 = ($_GET['type'] == 'desc' ? 'asc' : 'desc');
if ($_GET['sort'] == "6")
	$link6 = ($_GET['type'] == 'desc' ? 'asc' : 'desc');
if ($_GET['sort'] == "7")
	$link7 = ($_GET['type'] == 'desc' ? 'asc' : 'desc');
if ($_GET['sort'] == "8")
	$link8 = ($_GET['type'] == 'desc' ? 'asc' : 'desc');
if ($_GET['sort'] == "9")
	$link9 = ($_GET['type'] == 'desc' ? 'asc' : 'desc');
if ($_GET['sort'] == "10")
	$link10 = ($_GET['type'] == 'desc' ? 'asc' : 'desc');

if ($link1 == "") { $link1 = "asc"; } // for torrent name
if ($link2 == "") { $link2 = "desc"; }
if ($link3 == "") { $link3 = "desc"; }
if ($link4 == "") { $link4 = "desc"; }
if ($link5 == "") { $link5 = "desc"; }
if ($link6 == "") { $link6 = "desc"; }
if ($link7 == "") { $link7 = "desc"; }
if ($link8 == "") { $link8 = "desc"; }
if ($link9 == "") { $link9 = "desc"; }
if ($link10 == "") { $link10 = "desc"; }
?>
<td class="colhead" align="center">Type</td>
<td class="colhead" align=left><a href="browse.php?<? print $oldlink; ?>sort=1&type=<? print $link1; ?>">Name</a></td>
<?php
echo ($variant == 'index' ? '<td class=colhead align=center><a href="bookmarks.php"><img src="'.$pic_base_url.'bookmark.gif"  border="0" alt="Bookmark" title="Bookmark"></a></td>' : '');
if ($wait)
{
print("<td class=\"colhead\" align=\"center\">Wait</td>\n");
}

if ($variant == "mytorrents")
{
print("<td class=\"colhead\" align=\"center\">Edit</td>\n");
print("<td class=\"colhead\" align=\"center\">Visible</td>\n");
}
?>
<td class="colhead" align="left"><a href="browse.php?<? print $oldlink; ?>sort=2&type=<? print $link2; ?>">&nbsp;&nbsp;&nbsp;<img src=pic/files.gif border=none alt=<? print("" .Files. "")?>></a></td>
<td class="colhead" align="left"><a href="browse.php?<? print $oldlink; ?>sort=3&type=<? print $link3; ?>"><img src=pic/comments.gif border=none alt=<? print("" .Comments. "")?>></a></td>
<td class="colhead" align="center"><a href="browse.php?<? print $oldlink; ?>sort=4&type=<? print $link4; ?>"><img src=/pic/download.gif border=none alt=<? print("" .Download. "")?>></a></td>
<td class="colhead" align="center"><a href="browse.php?<? print $oldlink; ?>sort=5&type=<? print $link5; ?>">&nbsp;&nbsp;&nbsp;Progress</a></td>
<td class="colhead" align="center"><a href="browse.php?<? print $oldlink; ?>sort=6&type=<? print $link6; ?>">Size</a></td>
<td class="colhead" align="center"><a href="browse.php?<? print $oldlink; ?>sort=7&type=<? print $link7; ?>"><img src=pic/top2.gif border=none alt=<? print("" .Snatched. "")?>></a></td>
<td class="colhead" align="center"><a href="browse.php?<? print $oldlink; ?>sort=8&type=<? print $link8; ?>">&nbsp;&nbsp;<img src=pic/arrowup2.gif border="0" alt=<? print("" .Seeders. "")?>>&nbsp;&nbsp;</a></td>
<td class="colhead" align="center"><a href="browse.php?<? print $oldlink; ?>sort=9&type=<? print $link9; ?>">&nbsp;&nbsp;<img src=pic/arrowdown2.gif border="0" alt=<? print("" .Leechers. "")?>>&nbsp;&nbsp;</a></td>
<?php
if ($variant == "index")
print("<td class=\"colhead\" align=center><a href=\"browse.php?{$oldlink}sort=9&type={$link9}\"><img border=0 src=/pic/upper.gif alt=Upped By></a></td>\n");
if (get_user_class() >= UC_MODERATOR) {
print("<td class=\"colhead\" align=center>Delete</a></td>\n");
}
print("</tr>\n");
if (get_user_class() >= UC_MODERATOR) {
print("<form method=post action=deltorrent.php?mode=delete>");
}
print("</tr>\n");
while ($row = mysql_fetch_assoc($res)) {
//if ($CURUSER['split'] == "yes") {
$browse = $_SERVER["REQUEST_URI"];
$page = array ("/browse.php","/browse.php?page=1","/browse.php?page=2","/browse.php?page=3","/browse.php?page=4","/browse.php?page=5","/browse.php?page=6");
if (($CURUSER['split'] == "yes")  && (in_array($browse, $page)) ) {
/**
* @author StarionTurbo
* @copyright 2007
* @modname Show torrents by day
* @version v1.0
*/
/** Make some date varibles **/
$day_added = $row['added'];
$day_show = strtotime($day_added);
$thisdate = date('Y-m-d',$day_show);
/** If date already exist, disable $cleandate varible **/
//if($thisdate==$prevdate){
if(isset($prevdate) && $thisdate==$prevdate){
$cleandate = '';
/** If date does not exist, make some varibles **/
}else{
$day_added = 'Upped on '.date('l, j. M', strtotime($row['added'])); // You can change this to something else
$cleandate = "<tr><td colspan=14><b>$day_added</b></td></tr>\n"; // This also...
}
/** Prevent that "torrents added..." wont appear again with the same date **/
$prevdate = $thisdate;
$man = array(
    'Jan' => 'Januar',
    'Feb' => 'February',
    'Mar' => 'March',
    'Apr' => 'April',
    'May' => 'May',
    'Jun' => 'June',
    'Jul' => 'July',
    'Aug' => 'August',
    'Sep' => 'September',
    'Oct' => 'October',
    'Nov' => 'November',
    'Dec' => 'December'
);
foreach($man as $eng => $ger){
    $cleandate = str_replace($eng, $ger,$cleandate);
}
$dag = array(
    'Mon' => 'Monday',
    'Tues' => 'Tuesday',
    'Wednes' => 'Wednesday',
    'Thurs' => 'Thursday',
    'Fri' => 'Friday',
    'Satur' => 'Saturday',
    'Sun' => 'Sunday'
);
foreach($dag as $eng => $ger){
    $cleandate = str_replace($eng.'day', $ger.'',$cleandate);
}
/** If torrents not listed by added date **/
if ($row["sticky"] == "no") // delete this line if you dont have sticky torrents or you want to display the addate for them also
if(!$_GET['sort'] && !$_GET['d']){
   echo $cleandate."\n";
}
} //ends the condition       
/////standard sticky torrent hlight////////
/*
$id = $row["id"];
if ($row["sticky"] == "yes"){
print("<tr class=highlight>\n");
} else {
print("<tr>\n");
}*/ 
///////comment out to disable/////////////////
///////highlight torrenttable/////warning high querys comment out to save your server :)///////////
$id = $row['id'];
if ($CURUSER["ttablehl"] != "yes")
{
echo'<tr>';
}
else
{
$countstatsclr =  ($CURUSER['stylesheet'] == "1"?"teal":"") . ($CURUSER['stylesheet'] == "2"?"teal":"") . ($CURUSER['stylesheet'] == "3"?"teal":"") . ($CURUSER['stylesheet'] == "4"?"teal":"") . ($CURUSER['stylesheet'] == "5"?"teal":"");
$nukedclr =  ($CURUSER['stylesheet'] == "1"?"red":"") . ($CURUSER['stylesheet'] == "2"?"red":"") . ($CURUSER['stylesheet'] == "3"?"red":"") . ($CURUSER['stylesheet'] == "4"?"red":"") . ($CURUSER['stylesheet'] == "5"?"red":"");
$sceneclr =  ($CURUSER['stylesheet'] == "1"?"orange":"") . ($CURUSER['stylesheet'] == "2"?"orange":"") . ($CURUSER['stylesheet'] == "3"?"orange":"") . ($CURUSER['stylesheet'] == "4"?"orange":"") . ($CURUSER['stylesheet'] == "5"?"orange":"");
$requestclr =  ($CURUSER['stylesheet'] == "1"?"#777777":"") . ($CURUSER['stylesheet'] == "2"?"#777777":"") . ($CURUSER['stylesheet'] == "3"?"#777777":"") . ($CURUSER['stylesheet'] == "4"?"#777777":"") . ($CURUSER['stylesheet'] == "5"?"#777777":"");
$stickyclr =  ($CURUSER['stylesheet'] == "1"?"gold":"") . ($CURUSER['stylesheet'] == "2"?"gold":"") . ($CURUSER['stylesheet'] == "3"?"gold":"") . ($CURUSER['stylesheet'] == "4"?"gold":"") . ($CURUSER['stylesheet'] == "5"?"gold":"");
$hl = ($row['countstats'] == "no" && $row['nuked']=="no"?$countstatsclr:"") . ($row['scene'] == "yes" && $row['request'] == "no" && $row['nuked']=="no"?$sceneclr:"") . ($row['request'] == "yes" && $row['scene'] == "no" && $row['nuked']=="no"?$requestclr:"") . ($row['sticky'] == "yes"?$stickyclr:"") . ($row['nuked']=="yes"?$nukedclr:"");
////comment out to use gif indicate for seeding/leeching lower//////
$req = sql_query("SELECT torrent, seeder FROM peers WHERE userid=$CURUSER[id] AND torrent=$id") or sqlerr();
if (mysql_num_rows($req) > 0)
$peerid = mysql_fetch_assoc($req);
if ($peerid['seeder'] == 'yes' && $peerid['torrent'] == $id)
$hl = '#00AB3F';
if ($peerid['seeder'] == 'no' && $peerid['torrent'] == $id)
$hl = '#b22222 ';
$bgc = "bgcolor=".$hl."";
echo'<tr '.$bgc.'>';
}
////////////////////end highlight torrenttable - comment out to use standard or gif indicator code lower/////////
        print("<td align=center style='padding: 0px'>");
        if (isset($row["cat_name"])) 
        {
        print("<a href=\"browse.php?cat=" . $row["category"] . "\">");
        if (isset($row["cat_pic"]) && $row["cat_pic"] != "")
        print("<img border=\"0\" src=\"{$pic_base_url}{$row['cat_pic']}\" alt=\"{$row['cat_name']}\" />");
        else
        print($row["cat_name"]);
        print("</a>");
        }
        else
        print("-");
        print("</td>\n");
        /////////added under torrent name - uncomment out to use////
        //$added = "$row[added] (" . get_elapsed_time(sql_timestamp_to_unix_timestamp($row["added"])) . " ago)";
        //////////////////////////////////////end added///////////   
        $genre = safechar($row["newgenre"]);
        $nukereason = safechar($row["nukereason"]);
        $scene = ($row[scene]=="yes" ? "&nbsp;<img src='pic/scene.gif' border=0 title='Scene' alt='Scene'/>" : "");
        $request = ($row[request]=="yes" ? "&nbsp;<img src='pic/request.gif' border=0 title='Request' alt='Request'/>" : "");
        $nuked = ($row[nuked]=="yes" ? "&nbsp;<img src='pic/nuked.gif' border=0 title='nuked' alt='Nuked'/>" : "");
        $newtag = ((sql_timestamp_to_unix_timestamp($row['added']) >= $_SESSION['browsetime'])? '&nbsp;<img src='.$pic_base_url.'new.gif alt=NEW!>' : '');
        $viponly = ($row[vip]=="yes" ? "<img src='pic/star.gif' border=0 title='Vip Torrent' />" : "");
        //$newtag = ((sql_timestamp_to_unix_timestamp($row['added']) >= $_SESSION['browsetime'])? '&nbsp;<b><font color=red>NEW!</font></b>' : '');
        //if ($row["free"] == "yes" || (happyHour("check") && (happyCheck("check") == 255) || happyCheck("check") == $row["category"]) )
        //$freeicon = "<img src=\"pic/freedownload.gif\" border=\"0\" title=\"Free Leech\"  />";
        /////////freeslot in use on browse//////////      
        $freeimg = '<img src="/pic/freedownload.gif" border=0"/>';
        $doubleimg = '<img src="/pic/doubleseed.gif" border=0"/>';
        $isdlfree = ($row['doubleslot'] == 'yes' ? ' '.$doubleimg.' slot in use' : '');
        $isdouble = ($row['freeslot'] == 'yes' ? ' '.$freeimg.' slot in use' : '');     
        //////uncomment dispname when not using tooltips to reduce querys///       
        //////////////user class color//
        $uclass = mysql_result(sql_query("SELECT uclass FROM torrents WHERE id = '$id'"), 0);
        if ($CURUSER["view_uclass"] == 'no')
        $dispname = safechar($row["name"]);
        else
        $dispname = "<font color='#".get_user_class_color( $uclass)."'>". safechar($row["name"]) . "</font>";
        $checked = ((!empty($row['checked_by']) && $CURUSER['class'] >= UC_MODERATOR) ? "&nbsp;<img src='".$pic_base_url."mod.gif' width='15' border='0' title='Checked - by ".safechar($row['checked_by'])."' />" : "");
        //userclass color mod//
        /////////////////////////////////////////////////////
        $sticky = ($row[sticky]=="yes" ? "<img src='pic/sticky.gif' border='0' alt='sticky'>" : "");
        //////comment out to use balloon tooltips///           
        print("<td align=left><a href=\"details.php?");
        if ($variant == "mytorrents")
        print("returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;");
        print("id=$id");
        if ($variant == "index")
        print("&amp;hit=1");              
        /////////////////////////////////////////////////////
        if($row['afterpre'] == 'N/A') {
        $preres = "";
        } elseif(!empty($row['afterpre'])) {
        $preres = "Uploaded ".safechar($row[afterpre])." after pre";
        } else {
        $preres = "";
        }
        //////////////////////////////////////////////////////////////////////////
        ////////balloon tooltips - higher query counts when in use/// uncomment then comment out and the above code to enable/////      
        /*
        $preparing_baloon=sql_query("SELECT poster FROM torrents WHERE id=$id LIMIT 0, 255") or sqlerr();
        $poster=mysql_fetch_array($preparing_baloon);
        $poster=$poster[poster];
        $nlsubs=$poster[nlsubs];
        $preparing_descr=sql_query("SELECT descr FROM torrents WHERE id=$id LIMIT 0, 100 ") or sqlerr();
        $descr=mysql_fetch_array($preparing_descr);
        $des = mysql_real_escape_string(format_comment($descr[descr]));
        $des = ereg_replace('"',"&quot;",$des);
        //////////////user class color
        $uclass = mysql_result(sql_query("SELECT uclass FROM torrents WHERE id = '$id'"), 0);
        if ($CURUSER["view_uclass"] == 'no')
        $dispname = safechar($row["name"]);
        else
        $dispname = "<font color='#".get_user_class_color( $uclass)."'>". safechar($row["name"]) . "</font>";
        //userclass color mod ==end
        $dispname = ereg_replace('\.', ' ', $dispname);
        $baloon= print("<td align=left><a href=details.php?id=$id onmouseover=\"return overlib('<table width=100%><tr><td><img src=$poster width=128 height=150></td><td>$des</td></tr></table>', VAUTO, BGCOLOR, '#006600', WIDTH, 400, DELAY, 200);\" onmouseout=\"return nd();\";><b>" . CutName($dispname, $char) . " $description</b></a></a>&nbsp;<a href=\"#\" onclick=\"show_details('".$row['id']."', 'details'); return false;\"><img src=\"/pic/plus.gif\" border=\"0\" title=\"Show torrent info in this page\"/></a>&nbsp;$sticky&nbsp;$request&nbsp;$scene&nbsp;$nuked<br />$nukereason&nbsp;$preres".(sql_timestamp_to_unix_timestamp($row['added']) >= $CURUSER['last_browse'] ? " <img src=/pic/new.gif alt=NEW!>" : "")."\n");             
        */
        /////////end balloon tootips///////////            
        //////comment out when tooltips are enabled///
        print("\"><b>" . CutName($dispname, $char) . " $description</b></a>&nbsp;&nbsp;<a href=\"#\" onclick=\"show_details('".$row['id']."', 'details'); return false;\"><img src=\"/pic/plus.gif\" border=\"0\" title=\"Show torrent info in this page\"/></a>&nbsp;&nbsp;$sticky&nbsp;$request&nbsp;$scene&nbsp;$nuked<br />$nukereason&nbsp;$preres&nbsp;$newtag&nbsp;$viponly\n");
        ////////////////////////////////////////////////////          
        ////////////Freeslot indicator//uncomment to use//
        echo ($isdlfree.''.$isdouble);
        if ($row["multiplicator"] == "2")
        $multiplicator = "&nbsp;<img src=\"pic/multi2.gif\" title=\"X2 Upload\">&nbsp;";
        elseif ($row["multiplicator"] == "3")
        $multiplicator = "&nbsp;<img src=\"pic/multi3.gif\" title=\"X3 Upload\">&nbsp;";
        elseif ($row["multiplicator"] == "4")
        $multiplicator = "&nbsp;<img src=\"pic/multi4.gif\" title=\"X4 Upload\">&nbsp;";
        elseif ($row["multiplicator"] == "5")
        $multiplicator = "&nbsp;<img src=\"pic/multi5.gif\" title=\"X5 Upload\">&nbsp;";
        if ($row["multiplicator"] != "0")
        print("".$multiplicator."");
        $resws = sql_query("SELECT p.torrent, p.seeder, p.ip, u.webseeder, u.ip FROM users AS u LEFT JOIN peers AS p ON p.userid = u.id WHERE p.torrent = '$id' AND u.webseeder = 'yes' AND p.seeder = 'yes'") or sqlerr();
        $rowws = mysql_fetch_assoc($resws);
        if ($rowws)
        $isws = "<img border=0 src=pic/seeder.gif>"; // if you dont have a pic use $isws = "Highspeed Torrent";
        else
        $isws = "";
        print("$isws");
        //////torrent added/genre/checked////
        //echo ($added);
        echo ($genre);
        echo $checked;
        ////////end////        
        /*
        ///////// displays you as a seeder or leecher on browse as gif not highlightcolor
        $seedleech = sql_query("SELECT seeder FROM peers WHERE torrent = '$id' and userid='".unsafeChar($CURUSER['id'])."'");
        ////////////// result and output///////////
        if($seedleechdisplay = mysql_fetch_assoc($seedleech)) 
        {
        	///////////// seeding///////////////
    			if($seedleechdisplay['seeder']=="yes")
    			{
    				print("<img border=\"0\" src=\"/pic/arrowup.gif\" alt=\"Seeding\"/>");
    			}
    			/////////////////////Leeching///////
    			else
    			{
    				print("<img border=\"0\" src=\"/pic/arrowdown.gif\" alt=\"Leeching\"/>");
    			}
  			} 
  			print("</td>\n");*/
            $bm = sql_query("SELECT * FROM bookmarks WHERE torrentid=$id && userid=$CURUSER[id]");
            $bms = mysql_fetch_assoc($bm);
            $bookmarked = (empty($bms)?'<a href=\'bookmark.php?torrent='.$id.'&action=add\'><img src=\''.$pic_base_url.'bookmark.gif\' border=\'0\' alt=\'Bookmark it!\' title=\'Bookmark it!\'></a>':'<a href="bookmark.php?torrent='.$id.'&action=delete"><img src=\''.$pic_base_url.'plus2.gif\' border=\'0\' alt=\'Delete Bookmark!\' title=\'Delete Bookmark!\'></a>');
            echo ($variant == 'index' ? '<td align=right>'.$bookmarked.'</td>' : '');
  			if ($wait)
				{
				  $elapsed = floor((gmtime() - strtotime($row["added"])) / 3600);
	        if ($elapsed < $wait)
	        {
	          $color = dechex(floor(127*($wait - $elapsed)/48 + 128)*65536);
	          print("<td align=center><nobr><a href=\"faq.php\"><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></a></nobr></td>\n");
	        }
	        else
	          print("<td align=center><nobr>None</nobr></td>\n");
        }
        if ($variant == "mytorrents")
        print("<td align=\"center\"><a href=\"edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\">edit</a>\n");
        print("</td>\n");
        if ($variant == "mytorrents") {
            print("<td align=\"right\">");
            if ($row["visible"] == "no")
                print("<b>no</b>");
            else
                print("yes");
            print("</td>\n");
           }
            if ($row["type"] == "single")
            print("<td align=\"right\">" . $row["numfiles"] . "</td>\n");
            else {
            if ($variant == "index")
                print("<td align=\"right\"><b><a href=\"details.php?id=$id&amp;hit=1&amp;filelist=1\">" . $row["numfiles"] . "</a></b></td>\n");
            else
                print("<td align=\"right\"><b><a href=\"details.php?id=$id&amp;filelist=1#filelist\">" . $row["numfiles"] . "</a></b></td>\n");
        }

        if (!$row["comments"])
            print("<td align=\"right\">" . $row["comments"] . "</td>\n");
        else {
            if ($variant == "index")
                print("<td align=\"right\"><b><a href=\"details.php?id=$id&amp;hit=1&amp;tocomm=1\">" . $row["comments"] . "</a></b></td>\n");
            else
                print("<td align=\"right\"><b><a href=\"details.php?id=$id&amp;page=0#startcomments\">" . $row["comments"] . "</a></b></td>\n");
        }
        /*rating on browse
        print("<td align=\"center\">");
        if (!isset($row["rating"]))
            print("---");
        else {
            $rating = round($row["rating"] * 2) / 2;
            $rating = ratingpic($row["rating"]);
            if (!isset($rating))
                print("---");
            else
                print($rating);
        }
        print("</td>\n");*/
        //////Hide the quick download if download disabled/////
        if ($CURUSER["downloadpos"] == 'no'){
        print("<td class=embedded><img src=".$pic_base_url."downloadpos.gif alt='no download' style='margin-left: 4pt'></td>\n");
        }
        else
        if ($CURUSER["downloadpos"] == 'yes'){
        print("<td align=\"center\"><a href=\"/download.php/$id/" . rawurlencode($row["filename"]) . "\"><img src=pic/download.gif border=0 alt=Download></a></td>\n");
        }       
        // Progressbar Mod
        ///comment out to remove indicator on browse//////
        $seedersProgressbar = array();
        $leechersProgressbar = array();
        $resProgressbar = sql_query("SELECT p.seeder, p.to_go, t.size FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE  p.torrent = '$id'") or sqlerr();
        $progressPerTorrent = 0;
        $iProgressbar = 0;
        while ($rowProgressbar = mysql_fetch_array($resProgressbar)) {
        $progressPerTorrent += sprintf("%.2f", 100 * (1 - ($rowProgressbar["to_go"] / $rowProgressbar["size"])));    
        $iProgressbar++;
        }
        if ($iProgressbar == 0)
        $iProgressbar = 1;
        $progressTotal = sprintf("%.2f", $progressPerTorrent / $iProgressbar);
        $picProgress = get_percent_completed_image(floor($progressTotal))." (".round($progressTotal)."%)";
        print("<td align=center>$picProgress</td>\n");
        // End Progress Bar mod//////////////////////////
        print("<td align=center>" . str_replace(" ", "<br>", mksize($row["size"])) . "</td>\n");
//      print("<td align=\"right\">" . $row["views"] . "</td>\n");
//      print("<td align=\"right\">" . $row["hits"] . "</td>\n");
        $_s = "";
        if ($row["times_completed"] != 1)
          $_s = "s";
        if (get_user_class() >= UC_MODERATOR) {
        print("<td align=center>".($row["times_completed"] > 0 ? "<a href=snatches.php?id=$id>".number_format($row["times_completed"])."<br>time$_s</a>" : "0 times")."</td>\n");
        }
        else
        print("<td align=center>".($row["times_completed"] > 0 ?"".number_format($row["times_completed"])."<br>time$_s</a>" : "0 times")."</td>\n");

        if ($row["seeders"]) {
            if ($variant == "index")
            {
               if ($row["leechers"]) $ratio = $row["seeders"] / $row["leechers"]; else $ratio = 1;
                print("<td align=right><b><a href=details.php?id=$id&amp;hit=1&amp;toseeders=1><font color=" .
                  get_slr_color($ratio) . ">" . $row["seeders"] . "</font></a></b></td>\n");
            }
            else
                print("<td align=\"right\"><b><a class=\"" . linkcolor($row["seeders"]) . "\" href=\"details.php?id=$id&amp;dllist=1#seeders\">" .
                  $row["seeders"] . "</a></b></td>\n");
        }
        else
            print("<td align=\"right\"><span class=\"" . linkcolor($row["seeders"]) . "\">" . $row["seeders"] . "</span></td>\n");

        if ($row["leechers"]) {
            if ($variant == "index")
                print("<td align=right><b><a href=details.php?id=$id&amp;hit=1&amp;todlers=1>" .
                   number_format($row["leechers"]) . ($peerlink ? "</a>" : "") .
                   "</b></td>\n");
            else
                print("<td align=\"right\"><b><a class=\"" . linkcolor($row["leechers"]) . "\" href=\"details.php?id=$id&amp;dllist=1#leechers\">" .
                  $row["leechers"] . "</a></b></td>\n");
        }
        else
        print("<td align=\"right\">0</td>\n");
        ////Anonymous and delete torrent begin
        if ($variant == "index") {
        if ($row["anonymous"] == "yes") {
        print("<td align=center><i>Anonymous</i></td>\n");
        if (get_user_class() >= UC_MODERATOR) {
        print("<td align=\"center\" bgcolor=\"#FF0000\"><input type=\"checkbox\" name=\"delete[]\" value=\"" .htmlspecialchars( $id) . "\" /></td>\n");
        }
        }
        else {
        if ($variant == "index")                                   
        
        
        if ($CURUSER["view_uclass"] == 'yes')
        print("<td align=center>" . (isset($row["username"]) ? ("<a href=userdetails.php?id=" . $row["owner"] . "><font color='#".get_user_class_color( $uclass)."'>". safechar($row["username"]) . "</font></a>") : "<i>(unknown)</i>") . "</td>\n");
        else
        print("<td align=center>" . (isset($row["username"]) ? ("<a href=userdetails.php?id=" . $row["owner"] . "><b>" . safechar($row["username"]) . "</b></a>") : "<i>(unknown)</i>") . "</td>\n");
        /////////modified Delete torrent with anonymous uploader
        if (get_user_class() >= UC_MODERATOR) {
        print("<td align=\"center\" bgcolor=\"#FF0000\"><input type=\"checkbox\" name=\"delete[]\" value=\"" .htmlspecialchars( $id) . "\" /></td>\n");
        }
        }
        }
        print("</tr>\n");
        print("<tr><td width=737 id=\"id-".$row['id']."\" class=\"toggle_descr\" colspan=\"8\"></td></tr>\n");
        }
        if (get_user_class() >= UC_MODERATOR) {
        print("<td align=\"center\"colspan=15><input type=submit value=Delete></td></tr>\n");
        }
        print("</table></form>\n");
        return $rows;
        }
        //////end annonymous/delete torrent////
    

?>