<?php
ob_start("ob_gzhandler");
require_once("include/bittorrent.php");
require_once("include/function_torrenttable.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");

dbconn(false);
maxcoder();	

if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

if ($CURUSER['update_new'] != 'no' && $_GET['clear_new'])
$_SESSION['browsetime']=gmtime();

include 'include/cache/categories.php';
$cats = $categories;
////////////sort start///////////
$searchstr = ($_GET['search'] ? unesc($_GET['search']) : '');
$cleansearchstr = searchfield($searchstr);
if (empty($cleansearchstr))
    unset($cleansearchstr);
if (isset($_GET['sort']) && isset($_GET['type'])) {

$column = '';
$ascdesc = '';

switch($_GET['sort']) {
    case '1': $column = "name"; break;
    case '2': $column = "numfiles"; break;
    case '3': $column = "comments"; break;
    case '4': $column = "downloaded"; break;

    case '5': $column = "progress"; break;
    case '6': $column = "size"; break;
    case '7': $column = "times_completed"; break;
    case '8': $column = "seeders"; break;
    case '9': $column = "leechers"; break;
    case '10': $column = "owner"; break;
    default: $column = "id"; break;
}

switch($_GET['type']) {
    case 'asc': $ascdesc = "ASC"; $linkascdesc = "asc"; break;
    case 'desc': $ascdesc = "DESC"; $linkascdesc = "desc"; break;
    default: $ascdesc = "DESC"; $linkascdesc = "desc"; break;
}

$orderby = "ORDER BY torrents." . $column . " " . $ascdesc;
$pagerlink = "sort=" . intval($_GET['sort']) . "&type=" . $linkascdesc . "&";

} else {
    $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC";
    $pagerlink = "";
    }

$addparam = "";
$wherea = array();
$wherecatina = array();

if ($_GET["incldead"] == 1)
{
	$addparam .= "incldead=1&amp;";
	if (!isset($CURUSER) || get_user_class() < UC_ADMINISTRATOR)
		$wherea[] = "banned != 'yes'";
}
elseif ($_GET["incldead"] == 2)
{
	$addparam .= "incldead=2&amp;";
		$wherea[] = "visible = 'no'";
}
	elseif ($_GET["incldead"] == 3)
{
$addparam .= "incldead=3&amp;";
$wherea[] = "sticky = 'yes'";
       $wherea[] = "visible = 'yes'";
}
	else
		$wherea[] = "visible = 'yes'";

$category = (int)$_GET["cat"];

$all = $_GET["all"];
$blah = $_GET['blah'];
if (!$all)
	if (!$_GET && $CURUSER["notifs"])
	{
	  $all = True;
	  foreach ($cats as $cat)
	  {
	    $all &= $cat[id];
	    if (strpos($CURUSER["notifs"], "[cat" . $cat[id] . "]") !== False)
	    {
	      $wherecatina[] = $cat[id];
	      $addparam .= "c$cat[id]=1&amp;";
	    }
	  }
	}
	elseif ($category)
	{
	  if (!is_valid_id($category))
	    stderr("Error", "Invalid category ID.");
	  $wherecatina[] = $category;
	  $addparam .= "cat=$category&amp;";
	}
	else
	{
	  $all = True;
	  foreach ($cats as $cat)
	  {
	    $all &= $_GET["c$cat[id]"];
	    if ($_GET["c$cat[id]"])
	    {
	      $wherecatina[] = $cat[id];
	      $addparam .= "c$cat[id]=1&amp;";
	    }
	  }
	}

if ($all)
{
	$wherecatina = array();
if ($_GET["blah"] == 1)
{
$addparam .= "blah=1&amp;";
$wherea[] = "MATCH (search_text, ori_descr) AGAINST (" . sqlesc($searchstr) . ")";

}

elseif ($_GET["blah"] == 2)
{
$addparam .= "blah=2&amp;";
$wherea[] = "MATCH (search_text, ori_descr) AGAINST (" . sqlesc($searchstr) . ")";
}
  $addparam = "";
}

if (count($wherecatina) > 1)
	$wherecatin = implode(",",$wherecatina);
elseif (count($wherecatina) == 1)
	$wherea[] = "category = $wherecatina[0]";

$wherebase = $wherea;

if (isset($cleansearchstr)) {
  if ($blah == 0) {
    $wherea[] = "torrents.name LIKE (" . sqlesc($searchstr) . ")";
  } elseif ($blah == 1) {
    $wherea[] = "MATCH (search_text, ori_descr) AGAINST (" . sqlesc($searchstr) . ")";
  } elseif ($blah == 2) {
    $wherea[] = "MATCH (search_text, ori_descr) AGAINST (" . sqlesc($searchstr) . ")";
  }
    $addparam .= "search=" . urlencode($searchstr) . "&";
}

$where = implode(" AND ", $wherea);
if ($wherecatin)
	$where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";

if ($where != "")
	$where = "WHERE $where";

$res = mysql_query("SELECT COUNT(*) FROM torrents $where") or die(mysql_error());
$row = mysql_fetch_array($res,MYSQL_NUM);
$count = $row[0];

if (!$count && isset($cleansearchstr)) {
	$wherea = $wherebase;
	$orderby = "ORDER BY id DESC";
	$searcha = explode(" ", $cleansearchstr);
	$sc = 0;
	foreach ($searcha as $searchss) {
		if (strlen($searchss) <= 1)
			continue;
		$sc++;
		if ($sc > 5)
			break;
		$ssa = array();
		if ($blah == 0) {
          foreach (array("torrents.name") as $sss)
            $ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
            $wherea[] = "(" . implode(" OR ", $ssa) . ")";
        } elseif ($blah == 1) {
          foreach (array("search_text", "ori_descr") as $sss)
            $ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
            $wherea[] = "(" . implode(" OR ", $ssa) . ")";
        } elseif ($blah == 2) {
          foreach (array("search_text", "ori_descr") as $sss)
            $ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
            $wherea[] = "(" . implode(" OR ", $ssa) . ")";
   }
	}
	if ($sc) {
		$where = implode(" AND ", $wherea);
		if ($where != "")
			$where = "WHERE $where";
		$res = mysql_query("SELECT COUNT(*) FROM torrents $where");
		$row = mysql_fetch_array($res,MYSQL_NUM);
		$count = $row[0];
	}
}

$torrentsperpage = $CURUSER["torrentsperpage"];
if (!$torrentsperpage)
	$torrentsperpage = 15;

if ($count)
{
if ($addparam != "") {
 if ($pagerlink != "") {
  if ($addparam{strlen($addparam)-1} != ";") { // & = &amp;
    $addparam = $addparam . "&" . $pagerlink;
  } else {
    $addparam = $addparam . $pagerlink;
  }
 }
    } else {
 $addparam = $pagerlink;
    }
	
	list($pagertop, $pagerbottom, $limit) = pager($torrentsperpage, $count, "browse.php?" . $addparam);
                if (!$searchstr && $torrentsperpage && !isset($_GET["page"]) && !isset($_GET["incldead"]) && !isset($_GET["cat"]) && !$addparam)
                $file = "cache/browse/type-".$orderby."-".$torrentsperpage.".txt"; //Modify this line to fit your current configuration
                else
                $file = false;
                $expire = 30; // Yes, I mean 30 secs :P
                if (file_exists($file) && filemtime($file) > (time() - $expire)) {
                $records = unserialize(file_get_contents($file));
                 } else {
                 $query = "SELECT torrents.id, torrents.category, torrents.leechers, torrents.seeders, torrents.request, torrents.scene, torrents.nuked, torrents.nukereason, torrents.newgenre, torrents.checked_by,  torrents.afterpre, torrents.countstats, torrents.name, torrents.vip, torrents.sticky, torrents.times_completed, torrents.size, torrents.added, torrents.comments,torrents.numfiles,torrents.filename,torrents.multiplicator,torrents.anonymous,torrents.owner,IF(torrents.nfo <> '', 1, 0) as nfoav," .
//	"IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, categories.name AS cat_name, categories.image AS cat_pic, users.username FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
	"freeslots.free AS freeslot, freeslots.doubleup AS doubleslot, categories.name AS cat_name, categories.image AS cat_pic, users.username FROM torrents LEFT JOIN freeslots ON (torrents.id=freeslots.torrentid AND freeslots.userid={$CURUSER[id]}) LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
	$res = mysql_query($query) or die(mysql_error());
                while ($record = mysql_fetch_array($res) ) {
                 $records[] = $record;
                 }
                 if (!$searchstr && !isset($_GET["page"]) && !isset($_GET["incldead"]) && !isset($_GET["cat"]) && !$addparam) {
                 $OUTPUT = serialize($records);
                 $fp = fopen($file,"w");
                 fputs($fp, $OUTPUT);
                 fclose($fp);
                 }
                 }
}
else
	unset($res);
if (isset($cleansearchstr))
	stdhead("Search results for \"$searchstr\"");
else

	stdhead();

?>

<STYLE TYPE="text/css" MEDIA=screen>

  a.catlink:link, a.catlink:visited{
		text-decoration: none;
	}

	a.catlink:hover {
		color: #A83838;
	}

</STYLE>

<!-- check all -->
<script type="text/javascript">
function checkAll(field) {
   if (field.CheckAll.checked == true) {
      for (i = 0; i < field.length; i++) {
      
            field[i].checked = true;
         }
      
   }
   else {
      for (i = 0; i < field.length; i++) {
      
            field[i].checked = false;
         }
      
   }
}
</script>
<!-- check all -->
<form method="get" name="browse" action="browse.php">
<table class=bottom>
<tr>
<td class=bottom>
	<table class=bottom>
	<tr>

<?php
$i = 0;
if ($CURUSER["imagecats"] == 'no')
foreach ($cats as $cat)
{
    $countcats = get_row_count('torrents', "WHERE category = $cat[id]");
    $catsperrow = 5;
    print(($i && $i % $catsperrow == 0) ? "</tr><tr>" : "");
    print("<td class=bottom style=\"padding-bottom: 2px;padding-left: 7px\"><input name=c$cat[id] type=\"checkbox\" " . (in_array($cat[id],$wherecatina) ? "checked " : "") . "value=1><a class=catlink href=browse.php?cat=$cat[id]>" . safeChar($cat[name]) . "</a>&nbsp;($countcats)</td>\n");
    $i++;
}
else
$i = 0;
if ($CURUSER["imagecats"] == 'yes')
foreach ($cats as $cat)
{
   $catsperrow = 5;
   $catz = ($CURUSER['imagecats']=='yes' ? '<img border=0 src='.$pic_base_url.'' . safeChar($cat['image']) . '>' : safeChar($cat['name']));
   print(($i && $i % $catsperrow == 0) ? "</tr><tr>" : "");
   print("<td align=center class=bottom style=\"padding-bottom: 2px;padding-left: 7px\"><input name=c$cat[id] type=\"checkbox\" " . (in_array($cat[id],$wherecatina) ? "checked " : "") . "value=1><a class=catlink href=browse.php?cat=$cat[id]>".$catz."</a></td>\n");
   $i++;
}
$alllink = '<div align=left><input type=\'checkbox\' name=\'CheckAll\' id=\'CheckAll\' value=\'1\' onClick=\'checkAll(browse)\'> Check All</div>';

$ncats = count($cats);
$nrows = ceil($ncats/$catsperrow);
$lastrowcols = $ncats % $catsperrow;

if ($lastrowcols != 0)
{
	if ($catsperrow - $lastrowcols != 1)
		{
			print("<td class=bottom rowspan=" . ($catsperrow  - $lastrowcols - 1) . ">&nbsp;</td>");
		}
	print("<td class=bottom style=\"padding-left: 5px\">$alllink</td>\n");
}
?>
	</tr>
	</table>
</td>

<td class=bottom>
<table class=main>
	<tr>
		<td class=bottom style="padding: 1px;padding-left: 10px">
			<select name=incldead>
<option value="0">active</option>
<option value="1"<? print($_GET["incldead"] == 1 ? " selected" : ""); ?>>including dead</option>
<option value="2"<? print($_GET["incldead"] == 2 ? " selected" : ""); ?>>only dead</option>
			</select>
            <select name=blah>
<option value="0">Name</option>
<option value="1"<? print($blah == 1 ? " selected" : ""); ?>>Description</option>
<option value="2"<? print($blah == 2 ? " selected" : ""); ?>>Both</option>
                        </select></td>
<?php
if ($ncats % $catsperrow == 0)
	print("<td class=bottom style=\"padding-left: 15px\" rowspan=$nrows valign=center align=right>$alllink</td>\n");
?>
  </tr>
  <tr>
  	<td class=bottom style="padding: 1px;padding-left: 10px">
  	<div align=center>
  		<input type="submit" class=btn value="Go!"/>
  	</div>
  	</td>
  </tr>
  </table>
</td>
</tr>
</table>
</form>
<p><p>
<table width=750 class=main border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<form method="get" action=browse.php>
<p align="center">
Search:
<input type="text" id="searchinput" name="search" autocomplete="off" style="width: 240px;" ondblclick="suggest(event.keyCode,this.value);" onkeyup="suggest(event.keyCode,this.value);" onkeypress="return noenter(event.keyCode);" value="<?= safeChar($searchstr) ?>" />
in
<select name="cat">
<option value="0">(all types)</option>
<?php
$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
$catdropdown .= "<option value=\"" . $cat["id"] . "\"";
if ($cat["id"] == $_GET["cat"])
$catdropdown .= " selected=\"selected\"";
$catdropdown .= ">" . safeChar($cat["name"]) . "</option>\n";
}
$deadchkbox = "<input type=\"checkbox\" name=\"incldead\" value=\"1\"";
if ($_GET["incldead"])
$deadchkbox .= " checked=\"checked\"";
$deadchkbox .= " /> including dead torrents\n";
?>
<?= $catdropdown ?>
</select>
<?= $deadchkbox ?>
<input type="submit" value="Search!" />
</form>
<script language="JavaScript" src="suggest.js" type="text/javascript"></script>
<div id="suggcontainer" style="text-align: center; width: 520px; display: none;">
<div id="suggestions" style="cursor: default; position: absolute; background-color: #ff0000; border: 1px solid #777777;"></div>
</div>
</td></tr></table>

<center>
<table border="1" cellpadding="5" cellspacing="0" width="760">
	   	<p><center>[ Highlight colors = <font color="#00AB3F">Seeding </font>|<font color="#b22222"> Leeching</font> | <font color="teal">Free</font> |<font color="orange"> Scene</font> |<font color="#777777"> Request</font> | <font color="red">Nuked</font> | <font color="gold">Sticky</font> ]
        </center>
	    <center><font color="white">NeedSeed Link :[</font><a href="needseed.php"><font color="white"> 
		View All Torrents Needing Seed Help ]</font></a><p></p>
		</center></td>
	</tr>
</table>
<br><br />
</center>


<?php
if (isset($cleansearchstr))
print("<h2>Search results for \"" . safeChar($searchstr) . "\"</h2>\n");
if ($CURUSER['update_new'] != 'no')
{
//=== if you want a button
echo'<a href="?clear_new=1"><input type=submit value="clear new tag" class=button></a>';
//=== if you want a link
//echo'<p><a href="?clear_new=1">clear new tag</a></p>';
}
if ($count) {
	print($pagertop);

	torrenttable($records);

	print($pagerbottom);
}
else {
	if (isset($cleansearchstr)) {
		print("<h2>Nothing found!</h2>\n");
		print("<p>Try again with a refined search string.</p>\n");
	}
	else {
		print("<h2>Nothing here!</h2>\n");
		print("<p>Sorry pal :(</p>\n");
	}
}
if ($CURUSER['update_new'] != 'yes')
$_SESSION['browsetime']=gmtime();
stdfoot();
?>