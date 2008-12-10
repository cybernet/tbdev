<?php
function check_banned_emails ($email) {
    $expl = explode("@", $email);
    $wildemail = "*@".$expl[1];
    /* Ban emails by x0r @tbdev.net */
    $res = mysql_query("SELECT id, comment FROM bannedemails WHERE email = ".sqlesc($email)." OR email = ".sqlesc($wildemail)."") or sqlerr(__FILE__, __LINE__);
    if ($arr = mysql_fetch_assoc($res))
    stderr("Sorry..","This email address is banned!<br /><br /><strong>Reason</strong>: $arr[comment]", false);
}
function validusername($username)
{
	if ($username == "")
	  return false;

	// The following characters are allowed in user names
	$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	for ($i = 0; $i < strlen($username); ++$i)
	  if (strpos($allowedchars, $username[$i]) === false)
	    return false;

	return true;
}
function get_user_icons($arr, $big = false)
{
	global $pic_base_url;
	if ($big)
	{
		$donorpic = "starbig.gif";
		$warnedpic = "warnedbig.gif";
		$disabledpic = "disabledbig.gif";
		$style = "style='margin-left: 4pt'";
	}
	else
	{
		$donorpic = "star.gif";
		$warnedpic = "warned.gif";
		$disabledpic = "disabled.gif";
		$anonymous = "anonymous.gif";
		$style = "style=\"margin-left: 2pt\"";
	}
	$pics = $arr["donor"] == "yes" ? "<img src=\"{$pic_base_url}{$donorpic}\" alt='Donor' border=0 $style>" : "";
	if ($arr["enabled"] == "yes")
		$pics .= $arr["warned"] == "yes" ? "<img src=\"{$pic_base_url}{$warnedpic}\" alt=\"Warned\" border=0 $style>" : "";
	else
		$pics .= "<img src=\"{$pic_base_url}{$disabledpic}\" alt=\"Disabled\" border=0 $style>\n";
	return $pics;
}

  function get_ratio_color($ratio)
  {
    if ($ratio < 0.1) return "#ff0000";
    if ($ratio < 0.2) return "#ee0000";
    if ($ratio < 0.3) return "#dd0000";
    if ($ratio < 0.4) return "#cc0000";
    if ($ratio < 0.5) return "#bb0000";
    if ($ratio < 0.6) return "#aa0000";
    if ($ratio < 0.7) return "#990000";
    if ($ratio < 0.8) return "#880000";
    if ($ratio < 0.9) return "#770000";
    if ($ratio < 1) return "#660000";
    if (($ratio >= 1.0) && ($ratio < 2.0)) return "#006600";
    if (($ratio >= 2.0) && ($ratio < 3.0)) return "#007700";
    if (($ratio >= 3.0) && ($ratio < 4.0)) return "#008800";
    if (($ratio >= 4.0) && ($ratio < 5.0)) return "#009900";
    if (($ratio >= 5.0) && ($ratio < 6.0)) return "#00aa00";
    if (($ratio >= 6.0) && ($ratio < 7.0)) return "#00bb00";
    if (($ratio >= 7.0) && ($ratio < 8.0)) return "#00cc00";
    if (($ratio >= 8.0) && ($ratio < 9.0)) return "#00dd00";
    if (($ratio >= 9.0) && ($ratio < 10.0)) return "#00ee00";
    if ($ratio >= 10) return "#00ff00";
    return "#777777";
  }

  function get_slr_color($ratio)
  {
    if ($ratio < 0.025) return "#ff0000";
    if ($ratio < 0.05) return "#ee0000";
    if ($ratio < 0.075) return "#dd0000";
    if ($ratio < 0.1) return "#cc0000";
    if ($ratio < 0.125) return "#bb0000";
    if ($ratio < 0.15) return "#aa0000";
    if ($ratio < 0.175) return "#990000";
    if ($ratio < 0.2) return "#880000";
    if ($ratio < 0.225) return "#770000";
    if ($ratio < 0.25) return "#660000";
    if ($ratio < 0.275) return "#550000";
    if ($ratio < 0.3) return "#440000";
    if ($ratio < 0.325) return "#330000";
    if ($ratio < 0.35) return "#220000";
    if ($ratio < 0.375) return "#110000";
    if (($ratio >= 1.0) && ($ratio < 2.0)) return "#006600";
    if (($ratio >= 2.0) && ($ratio < 3.0)) return "#007700";
    if (($ratio >= 3.0) && ($ratio < 4.0)) return "#008800";
    if (($ratio >= 4.0) && ($ratio < 5.0)) return "#009900";
    if (($ratio >= 5.0) && ($ratio < 6.0)) return "#00aa00";
    if (($ratio >= 6.0) && ($ratio < 7.0)) return "#00bb00";
    if (($ratio >= 7.0) && ($ratio < 8.0)) return "#00cc00";
    if (($ratio >= 8.0) && ($ratio < 9.0)) return "#00dd00";
    if (($ratio >= 9.0) && ($ratio < 10.0)) return "#00ee00";
    if ($ratio >= 10) return "#00ff00";
    return "#777777";
  }


function get_user_class()
{
  global $CURUSER;
  return $CURUSER["class"];
}

function get_user_class_name($class)
{
  switch ($class)
  {
    case UC_USER: return "User";

    case UC_POWER_USER: return "Power User";

    case UC_VIP: return "VIP";

    case UC_UPLOADER: return "Uploader";

    case UC_MODERATOR: return "Moderator";

    case UC_ADMINISTRATOR: return "Administrator";

    case UC_SYSOP: return "SysOp";
    
    case UC_CODER: return "Coder";
  }
  return "";
}

function is_valid_user_class($class)
{
  return is_numeric($class) && floor($class) == $class && $class >= UC_USER && $class <= UC_CODER;
}

function is_valid_id($id)
{
  return is_numeric($id) && ($id > 0) && (floor($id) == $id);
}

function get_user_class_color($class)
{
    switch ($class)
    {        
        case UC_USER: return "8E35EF";
        case UC_POWER_USER: return "9923CD";
        case UC_VIP: return "009900";
        case UC_UPLOADER: return "6400EF";
        case UC_MODERATOR: return "FF7F00";
        case UC_ADMINISTRATOR: return "00FFFF";
        case UC_SYSOP: return "007FFF";
        case UC_CODER: return "FF0000";
    }
    return "";
}
function get_user_class_image($class)
{
  switch ($class)
  {
    
    case UC_USER: return "pic/class/user.gif";

    case UC_POWER_USER: return "pic/class/pu.gif";

    case UC_VIP: return "pic/class/vip.gif";

    case UC_UPLOADER: return "pic/class/uploader.gif";

    case UC_MODERATOR: return "pic/class/mod.gif";

    case UC_ADMINISTRATOR: return "pic/class/admin.gif";

    case UC_SYSOP: return "pic/class/sysop.gif";
  
    case UC_CODER: return "pic/class/coder.gif";

    }
  return "";
}

///////////progress indicator
function get_percent_completed_image($p) {
$maxpx = "40"; // Maximum amount of pixels for the progress bar

if ($p == 0) $progress = "<img src=\"/pic/progbar-rest.gif\" height=9 width=" . ($maxpx) . " />";
if ($p == 100) $progress = "<img src=\"/pic/progbar-green.gif\" height=9 width=" . ($maxpx) . " />";
if ($p >= 1 && $p <= 30) $progress = "<img src=\"/pic/progbar-red.gif\" height=9 width=" . ($p*($maxpx/100)) . " /><img src=\"/pic/progbar-rest.gif\" height=9 width=" . ((100-$p)*($maxpx/100)) . " />";
if ($p >= 31 && $p <= 65) $progress = "<img src=\"/pic/progbar-yellow.gif\" height=9 width=" . ($p*($maxpx/100)) . " /><img src=\"/pic/progbar-rest.gif\" height=9 width=" . ((100-$p)*($maxpx/100)) . " />";
if ($p >= 66 && $p <= 99) $progress = "<img src=\"/pic/progbar-green.gif\" height=9 width=" . ($p*($maxpx/100)) . " /><img src=\"/pic/progbar-rest.gif\" height=9 width=" . ((100-$p)*($maxpx/100)) . " />";
return "<img src=\"/pic/bar_left.gif\" />" . $progress ."<img src=\"/pic/bar_right.gif\" />";
}
function get_percent_donated_image($d) {
       $img = "progress-";
       if ($p == 100)
$img .= "5";
       elseif (($d >= 0) && ($d <= 10))
$img .= "0";
       elseif (($d >= 11) && ($d <= 40))
$img .= "1";
       elseif (($d >= 41) && ($d <= 60))
$img .= "2";
       elseif (($d >= 61) && ($d <= 80))
$img .= "3";
       elseif (($d >= 81) && ($d <= 99))
$img .= "4";
       return "<img src=\""."pic/".$img.".gif\"/>";
}
?>