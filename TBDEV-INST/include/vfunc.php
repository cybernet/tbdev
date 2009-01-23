<?php
function deletetorrent($id) {
    global $torrent_dir;
    sql_query("DELETE FROM torrents WHERE id = $id");
    foreach(explode(".","peers.files.comments.ratings.snatched") as $x)
        sql_query("DELETE FROM $x WHERE torrent = $id");
        sql_query("DELETE FROM coins WHERE torrentid = $id");
    unlink("$torrent_dir/$id.torrent");
}
function mysql_fetch_all($query, $default_value=Array()){
        $r=@mysql_query($query);
        $result = Array();
        if($err=mysql_error())return $err;
        if(@mysql_num_rows($r))
            while($row=mysql_fetch_array($r))$result[]=$row;
        if(count($result)==0)
            return $default_value;
        return $result;
    }

/////////////////////////////////////////Abormal upload detection-comment out when using the faster announce///////////////////////////
function auto_enter_cheater($userid, $rate, $upthis, $diff, $torrentid, $client, $ip, $last_up)
{
mysql_query("INSERT INTO cheaters (added, userid, client, rate, beforeup, upthis, timediff, userip, torrentid) VALUES(".sqlesc(get_date_time()).", ".sqlesc($userid).", ".sqlesc($client).", ".sqlesc($rate).", ".sqlesc($last_up).", ".sqlesc($upthis).", ".sqlesc($diff).", ".sqlesc($ip).", ".sqlesc($torrentid).")") or sqlerr(__FILE__, __LINE__);
}
////////////////////////////////////////////////
function pager($rpp, $count, $href, $opts = array()) {
    $pages = ceil($count / $rpp);

    if (!$opts["lastpagedefault"])
        $pagedefault = 0;
    else {
        $pagedefault = floor(($count - 1) / $rpp);
        if ($pagedefault < 0)
            $pagedefault = 0;
    }

    if (isset($_GET["page"])) {
        $page = 0 + $_GET["page"];
        if ($page < 0)
            $page = $pagedefault;
    }
    else
        $page = $pagedefault;

    $pager = "";

    $mp = $pages - 1;
    $as = "<img src='pic/arrow_prev.gif' border='0' alt='Previous'>";
    if ($page >= 1) {
        $pager .= "<a href=\"{$href}page=" . ($page - 1) . "\">";
        $pager .= $as;
        $pager .= "</a>";
    }
    else
        $pager .= $as;
    $pager .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    $as = "<img src='pic/arrow_next.gif' border='0' alt='Next'>";
    if ($page < $mp && $mp >= 0) {
        $pager .= "<a href=\"{$href}page=" . ($page + 1) . "\">";
        $pager .= $as;
        $pager .= "</a>";
    }
    else
        $pager .= $as;

    if ($count) {
        $pagerarr = array();
        $dotted = 0;
        $dotspace = 3;
        $dotend = $pages - $dotspace;
        $curdotend = $page - $dotspace;
        $curdotstart = $page + $dotspace;
        for ($i = 0; $i < $pages; $i++) {
            if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
                if (!$dotted)
                    $pagerarr[] = "...";
                $dotted = 1;
                continue;
            }
            $dotted = 0;
            $start = $i * $rpp + 1;
            $end = $start + $rpp - 1;
            if ($end > $count)
                $end = $count;
            $text = "$start&nbsp;-&nbsp;$end";
            if ($i != $page)
                $pagerarr[] = "<a href=\"{$href}page=$i\"><b>$text</b></a>";
            else
                $pagerarr[] = "<b>$text</b>";
        }
        $pagerstr = join(" | ", $pagerarr);
        $pagertop = "<p align=\"center\">$pager<br />$pagerstr</p>\n";
        $pagerbottom = "<p align=\"center\">$pagerstr<br />$pager</p>\n";
    }
    else {
        $pagertop = "<p align=\"center\">$pager</p>\n";
        $pagerbottom = $pagertop;
    }

    $start = $page * $rpp;

    return array($pagertop, $pagerbottom, "LIMIT $start,$rpp");
}

function downloaderdata($res) {
    $rows = array();
    $ids = array();
    $peerdata = array();
    while ($row = mysql_fetch_assoc($res)) {
        $rows[] = $row;
        $id = $row["id"];
        $ids[] = $id;
        $peerdata[$id] = array(downloaders => 0, seeders => 0, comments => 0);
    }

    if (count($ids)) {
        $allids = implode(",", $ids);
        $res = sql_query("SELECT COUNT(*) AS c, torrent, seeder FROM peers WHERE torrent IN ($allids) GROUP BY torrent, seeder");
        while ($row = mysql_fetch_assoc($res)) {
            if ($row["seeder"] == "yes")
                $key = "seeders";
            else
                $key = "downloaders";
            $peerdata[$row["torrent"]][$key] = $row["c"];
        }
        $res = sql_query("SELECT COUNT(*) AS c, torrent FROM comments WHERE torrent IN ($allids) GROUP BY torrent");
        while ($row = mysql_fetch_assoc($res)) {
            $peerdata[$row["torrent"]]["comments"] = $row["c"];
        }
    }

    return array($rows, $peerdata);
}

function searchfield($s) {
    return preg_replace(array('/[^a-z0-9]/si', '/^\s*/s', '/\s*$/s', '/\s+/s'), array(" ", "", "", " "), $s);
}

function genrelist() {
global $CURUSER;
$ret = array();
if ($CURUSER["imagecats"] == 'no')
$res = sql_query("SELECT id, name FROM categories ORDER BY name");
else
$res = sql_query("SELECT id, name, image FROM categories ORDER BY name");
while ($row = mysql_fetch_array($res))
$ret[] = $row;
return $ret;
}
/*
function genrelist() {
  $file = "cache/categories.php";
  $ret = array();
  if ($CURUSER["imagecats"] == 'no')
  $res = sql_query("SELECT id, name FROM categories ORDER BY name");
  else
  if (file_exists($file))
  {
  require_once("$file");
  foreach ($categories as $category)
  $ret[] = $category;
  }
  else
  {
  $res = sql_query("SELECT id, name, images FROM categories ORDER BY name");
  while ($row = mysql_fetch_array($res))
  $ret[] = $row;
  }
  return $ret;
  }*/
function linkcolor($num) {
    if (!$num)
        return "red";
//    if ($num == 1)
//        return "yellow";
    return "green";
}

function ratingpic($num) {
    global $pic_base_url;
    $r = round($num * 2) / 2;
    if ($r < 1 || $r > 5)
        return;
    return "<img src=\"{$pic_base_url}{$r}.gif\" border=\"0\" alt=\"rating: $num / 5\" />";
}

function CutName ($txt, $len){
$len = 50;
return (strlen($txt)>$len ? substr($txt,0,$len-1) .'...':$txt);
}

function writecomment($userid, $comment) {
    $res = sql_query("SELECT modcomment FROM users WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res);
    
       $modcomment = gmdate("d-m-Y") . " - " . $comment . "" . ($arr[modcomment] != "" ? "\n\n" : "") . "$arr[modcomment]";    
    $modcom = sqlesc($modcomment);
    return sql_query("UPDATE users SET modcomment = $modcom WHERE id = '$userid'") or sqlerr(__FILE__, __LINE__);
   }


function hash_pad($hash) {
    return str_pad($hash, 20);
}

function hash_where($name, $hash) {
    $shhash = preg_replace('/ *$/s', "", $hash);
    return "($name = " . sqlesc($hash) . " OR $name = " . sqlesc($shhash) . ")";
}

//---------------------------------
//---- Login Attempts 
//---------------------------------

function failedloginscheck () {
global $maxloginattempts;
$total = 0;
$ip = sqlesc(getip());
$Query = sql_query("SELECT SUM(attempts) FROM loginattempts WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
list($total) = mysql_fetch_array($Query);
if ($total >= $maxloginattempts) {
sql_query("UPDATE loginattempts SET banned = 'yes' WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
stderr("Login Locked!", "You have been <b>exceed maximum login attempts</b>, therefore your ip address <b>(".htmlspecialchars($ip).")</b> has been banned.");
}
}
function failedlogins () {
$ip = sqlesc(getip());
$added = sqlesc(get_date_time());
$a = (@mysql_fetch_row(@sql_query("select count(*) from loginattempts where ip=$ip"))) or sqlerr(__FILE__, __LINE__);
if ($a[0] == 0)
sql_query("INSERT INTO loginattempts (ip, added, attempts) VALUES ($ip, $added, 1)") or sqlerr(__FILE__, __LINE__);
else
sql_query("UPDATE loginattempts SET attempts = attempts + 1 where ip=$ip") or sqlerr(__FILE__, __LINE__);

stderr("Login failed!","<b>Error</b>: Username or password incorrect<br>Don't remember your password? <b><a href=recover.php>Recover</a></b> your password!");
}

function remaining () {
global $maxloginattempts;
$total = 0;
$ip = sqlesc(getip());
$Query = sql_query("SELECT SUM(attempts) FROM loginattempts WHERE ip=$ip") or sqlerr(__FILE__, __LINE__);
list($total) = mysql_fetch_array($Query);
$remaining = $maxloginattempts - $total;
if ($remaining <= 2 )
$remaining = "<font color=red size=4>".$remaining."</font>";
else
$remaining = "<font color=green size=4>".$remaining."</font>";

return $remaining;
}

//---------------------------------
//---- Login Attempts 
//---------------------------------

function parked()
{
       global $CURUSER;
       if ($CURUSER["parked"] == "yes")
stderr("Error", "your account is parked.");
}

//=== report, disable etc hackers
function hacker_dork($hacked_what)
{
$ip=getip();
$ban_ip = sqlesc(trim($_SERVER['REMOTE_ADDR']));
$res = sql_query("SELECT id, username, modcomment FROM users WHERE ip = $ban_ip AND class < ".UC_ADMINISTRATOR);
if (mysql_num_rows($res) > 0){
$arr = mysql_fetch_assoc($res);
$subject = sqlesc($arr['username']." tried to hack $hacked_what");
$body = sqlesc("user: [url=userdetails.php?id=".$arr['id']."]".$arr['username']."[/url] \n with IP: $ban_ip\n tried to hack $hacked_what.\n ");
$modcomment = gmdate("Y-m-d") . " Banned for trying to hack $hacked_what...\n". $arr['modcomment'];
sql_query("UPDATE users set enabled='no', modcomment = ".sqlesc($modcomment)." where id=".$arr['id']);
} else {
$subject = sqlesc("attempt to hack $hacked_what");
$body = sqlesc("user with IP: $ban_ip \n tried to hack $hacked_what.\n ");
}
auto_post( $subject , $body );
//sql_query("INSERT INTO messages (sender, subject, receiver, added, msg) VALUES (0, $subject, 1, '".get_date_time()."', $body)") or sqlerr(__FILE__, __LINE__);
stderr("Wtf You Aint Staff", "Tut Tut..Nice try... You've just gone and banned yourself n00b !!");
die;
}

//=== auto post by retro
  function auto_post($subject = "Error - Subject Missing",$body = "Error - No Body") // Function to use the special system message forum
{
  $forumid = 1;  // Remember to change this if the forum is recreated for some reason.

  $res = sql_query("SELECT id FROM topics WHERE forumid = ".$forumid." AND subject = ".$subject);

  if(mysql_num_rows($res)==1) { // Topic already exists in the system forum.
  $arr = mysql_fetch_array($res);
  $topicid = $arr['id'];
  }
  else { // Create new topic.
  sql_query( "INSERT INTO topics (userid, forumid, subject) VALUES(2, $forumid, $subject)") or sqlerr(__FILE__, __LINE__);
  $topicid = @mysql_insert_id();
  }

  $added = "'" . get_date_time() . "'";

  sql_query( "INSERT INTO posts (topicid, userid, added, body) " .
               "VALUES($topicid, 2, $added, $body)") or sqlerr(__FILE__, __LINE__);

  $res = sql_query("SELECT id FROM posts WHERE topicid=$topicid ORDER BY id DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
  $arr = mysql_fetch_row($res) or die("No post found");
  $postid = $arr[0];
  sql_query("UPDATE topics SET lastpost=$postid WHERE id=$topicid") or sqlerr(__FILE__, __LINE__);
}

//=== flush log
function write_flush_log($text)
{
  $text = sqlesc($text);
  $added = sqlesc(get_date_time());
  sql_query("INSERT INTO flush_log (added, txt) VALUES($added, $text)") or sqlerr(__FILE__, __LINE__);
}

// Set this to the line break character sequence of your system
$linebreak = "\r\n";

function get_row_count($table, $suffix = "")
{
  if ($suffix)
    $suffix = " $suffix";
  ($r = sql_query("SELECT COUNT(*) FROM $table$suffix")) or die(mysql_error());
  ($a = mysql_fetch_row($r)) or die(mysql_error());
  return $a[0];
}

function stdmsg($heading, $text, $htmlstrip = FALSE)
{
    if ($htmlstrip) {
        $heading = htmlspecialchars($heading);
        $text = htmlspecialchars($text);
    }
    print("<table class=main width=750 border=0 cellpadding=0 cellspacing=0><tr><td class=embedded>\n");
        if ($heading)
            print("<h2>$heading</h2>\n");
    print("<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text>\n");
    print($text . "</td></tr></table></td></tr></table>\n");
}


function stderr($heading, $text, $htmlstrip = FALSE)
{
  stdhead();
  stdmsg($heading, $text, $htmlstrip);
  stdfoot();
  die;
}

function findURLtitles($url){
    if($url[2]==']' || $url[2]=='['){
        return $url[0];
    }else{
        $url[1] = (substr($url[1],0,4)=='http') ? $url[1] : 'http://' . $url[1];
        if(preg_match('#<title>(.*?)</title>#is', @file_get_contents($url[1]), $matches)){
            return '[url='.$url[1].']'.$matches[1].'[/url]'.$url[2];
        }else{
            return '[url='.$url[1].']'.$url[1].'[/url]'.$url[2];
        }
    }
}

function URLtitles($message){
    $URLpattern = '#((?:https?://|www\.)(?:[\w\#\$%&~/\\-;:=,\?@\+][\w\#\$%&~/\.\\-;:=,\?@\+]*[\w\#\$%&~/\\-;:=,\?@\+]|[\w\#\$%&~/\\-;:=,\?@\+])\.[a-z]{2,6}(?:[\w\#\$%&~/\.\\-;:=,\?@\+]*))(.|$)#i';
    $message = preg_replace_callback ($URLpattern, findURLtitles, $message);
    return "$message";
}

function encodehtml($s, $linebreaks = true)
{
  $s = str_replace("<", "&lt;", str_replace("&", "&amp;", $s));
  if ($linebreaks)
    $s = nl2br($s);
  return $s;
}

function get_dt_num()
{
  return gmdate("YmdHis");
}

function xss_detect( $html )
    {
        /*
        * check for any nastiness < S r I p T > <    / sc  r  IP t> etc
        * If you wanted, you can quitely log any finds;)
        */
        
        if (preg_match( "#<(\s+?)?s(\s+?)?c(\s+?)?r(\s+?)?i(\s+?)?p(\s+?)?t#is", $html ))
            return true;
        if (preg_match( "#<(\s+?)?/(\s+?)?s(\s+?)?c(\s+?)?r(\s+?)?i(\s+?)?p(\s+?)?t#is", $html ))
            return true;
        
        /*
        * look for the usual candidates
        * feel free to add what you need
        */
        if( preg_match("/javascript|alert|about|onmouseover|onclick|onload|onsubmit|<body|<html|document\./i" , $html ))
            return true;
        
        /* still here? Must be sort of ok, maybe... */
        return false;
    }

    /*
    * check the image url for dynamic stuff, image number, ext, etc, etc.
    */
    
    function check_image($url="")
    {
        static $image_count = 0; // do not alter this!
        
        $allow_dynamic_img = 0; //You alter this value at your own peril!
        
        $max_images = 195; //Maximum number of images allowed, after which the raw string is returned.
        
        $img_ext = 'jpg,gif,png'; //image extension. Careful what you put here!
        
        if (!$url) return; //empty? send it back!
        
        $url = trim($url);
        
        $default = "[img]".$url."[/img]"; //this is what is returned after images are exceeded
        
        $image_count++;
        
        /*
        * is this true and have we exceeded it?
        */
        
        if ($max_images)
        {
            if ($image_count > $max_images)
            {
                
                return $default;
            }
        }
        
        /*
        * Check for any dynamic stuff!
        */
        
        if ($allow_dynamic_img != 1)
        {
            if (preg_match( "/[?&;]/", $url))
            {
                return "<img src='$BASEURL/warn.jpg' border='0' alt='image not found' />";
            }
            
            if (preg_match( "/javascript(\:|\s)/i", $url ))
            {
                return "<img src=''$BASEURL/warn.jpg' border='0' alt='image not found' />";
            }
        }
        
        /*
        * Check the extension
        */
        
        if ($img_ext)
        {
            $extension = preg_replace( "#^.*\.(\S+)$#", "\\1", $url );
            
            $extension = strtolower($extension);
            
            if ( (! $extension) OR ( preg_match( "#/#", $extension ) ) )
            {
                return "<img src=''$BASEURL/warn.jpg' border='0' alt='image not found' />";
            }
            
            $img_ext = strtolower($img_ext);
            
            if ( ! preg_match( "/".preg_quote($extension, '/')."(,|$)/", $img_ext ))
            {
                return "<img src=''$BASEURL/warn.jpg' border='0' alt='image not found' />";
            }
            
            //$url = xss_detect($url);
            if (xss_detect($url))
                return 'OOPS!!'; //do what ever you want to return here
        }
        
        /*
        * Take a stab at getting a good image url
        */
        
        if (!preg_match( "/^(http|https|ftp):\/\//i", $url )) {
            return "<img src=''$BASEURL/warn.jpg' border='0' alt='image not found' />";
        }
        
        /*
        * done all we can at this point!
        */
        
        $url = str_replace( " ", "%20", $url );
        
        return "<img src='$url' border='0' alt='Does my bum look big in this image?' onload='NcodeImageResizer.createOn(this);' />";
    }


function sql_timestamp_to_unix_timestamp($s)
{
  return mktime(substr($s, 11, 2), substr($s, 14, 2), substr($s, 17, 2), substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4));
}

function autoshout($msg = '') {
    $message = $msg;
sql_query("INSERT INTO shoutbox (date, text, userid, username) VALUES (".implode(", ", array_map("sqlesc", array(time(), $message, '2','System'))).")") or sqlerr(__FILE__,__LINE__);
}

function write_log($text)
{
  $text = sqlesc($text);
  $added = sqlesc(get_date_time());
  sql_query("INSERT INTO sitelog (added, txt) VALUES($added, $text)") or sqlerr(__FILE__, __LINE__);
}

function write_info($text)
{
  $text = sqlesc($text);
  $added = sqlesc(get_date_time());
  mysql_query("INSERT INTO infolog (added, txt) VALUES($added, $text)") or sqlerr(__FILE__, __LINE__);
}

function get_elapsed_time($ts)
{
  $mins = floor((gmtime() - $ts) / 60);
  $hours = floor($mins / 60);
  $mins -= $hours * 60;
  $days = floor($hours / 24);
  $hours -= $days * 24;
  $weeks = floor($days / 7);
  $days -= $weeks * 7;
  $t = "";
  if ($weeks > 0)
    return "$weeks week" . ($weeks > 1 ? "s" : "");
  if ($days > 0)
    return "$days day" . ($days > 1 ? "s" : "");
  if ($hours > 0)
    return "$hours hour" . ($hours > 1 ? "s" : "");
  if ($mins > 0)
    return "$mins min" . ($mins > 1 ? "s" : "");
  return "< 1 min";
}
?>