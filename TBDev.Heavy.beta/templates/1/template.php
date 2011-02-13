<?php

/*
**Template Mod By AronTh for TBDEV.NET source code 2009, theme made by AronTh
**Special Thanks to CoLdFuSiOn for providing the source code and KiD for the motivation to me(AronTh) to make themes for TbDev.net
*/

function stdhead( $title = "", $js='', $css='' ) {
    global $CURUSER, $TBDEV, $lang, $msgalert;

    if (!$TBDEV['site_online'])
      die("Site is down for maintenance, please check back again later... thanks<br />");

    //header("Content-Type: text/html; charset=iso-8859-1");
    //header("Pragma: No-cache");
    if ($title == "")
        $title = $TBDEV['site_name'] .(isset($_GET['tbv'])?" (".TBVERSION.")":'');
    else
        $title = $TBDEV['site_name'].(isset($_GET['tbv'])?" (".TBVERSION.")":''). " :: " . htmlsafechars($title);

  /* Deprecate this.
    if ($TBDEV['msg_alert'] && $msgalert && $CURUSER)
    {
      $res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " && unread='yes'") or sqlerr(__FILE__,__LINE__);
      $arr = mysql_fetch_row($res);
      $unread = $arr[0];
    }
  */

    if ($CURUSER)
    {
      $res1 = @mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND unread='yes'") or sqlerr(__LINE__,__FILE__);
      $arr1 = mysql_fetch_row($res1);

      $unread = ($arr1[0] > 0 ? "<span class='msgalert'><small>{$arr1[0]}</small></span>" : $arr1[0]);
      $msgalert = $arr1[0];
      $inbox = ($unread == 1 ? "$unread" : "$unread");
    }

	$FILE = isset($CURUSER) ? $CURUSER['stylesheet'] : $TBDEV['stylesheet'] ;

    $htmlout = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">

		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>

			<meta name='generator' content='TBDev.net' />
			<meta http-equiv='Content-Language' content='en-us' />
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />

          <title>{$title}</title>
          <link rel='stylesheet' type='text/css' href='{$TBDEV['baseurl']}/templates/$FILE/{$FILE}.css' />
          {$js}\n
    </head>

    <body>

    <!-- Begin Wrapper -->
    <div id='wrapper'>

        <!-- Begin Header -->
        <div id='header'>
            <div class='statusbar'>";
    $htmlout .= StatusBar();
    $htmlout .= "
            </div>
            <div class='subheader'>
                <div class='logo'>";

    if ($CURUSER)
    {
      $htmlout .= "
                    <div class='profile'>
                        <div class='status_avatar'>";

    if (!empty($CURUSER['avatar']))
    {
      $avatar = "<a href='userdetails.php?id={$CURUSER['id']}'><img src='{$CURUSER['avatar']}' width='50' height='50' alt='' /></a>";
    }
    else
    {
      $avatar = "<a href='userdetails.php?id={$CURUSER['id']}'><img src='{$TBDEV['baseurl']}/templates/1/images/default_thumb.png' alt='' /></a>";
    }

    $htmlout .= $avatar;

    $htmlout .= "
                        </div>
                            <div class='username'>
                                <p><a href='userdetails.php?id={$CURUSER['id']}'>{$CURUSER['username']}</a></p>
                            </div>
                            <div class='messagesbox'>
                                <p><a href='messages.php'>$inbox</a></p>
                            </div>
                            <ul>
                               <li><a class='bold' href='logout.php'>{$lang['gl_logout']}</a></li>
                            </ul>
                            <div class='rlink'>
                                <a class='bold' href='my.php'>{$lang['gl_profile']}</a>&nbsp;&nbsp;&nbsp;
                                <a class='bold' href='rules.php'>{$lang['gl_rules']}</a>
                            </div>
                    </div>";
    }
    else
    {
      $htmlout .= "
                    <div class='profile'>
                        <div class='sign_in'>
                            <div style='padding:8px 0 0 5px;'>
                            <img src='{$TBDEV['baseurl']}/templates/1/images/key.png' alt='{$lang['gl_login']}' />&nbsp;<a style='color:#fff;' href='login.php'>Sign In »</a>
                            </div>
                        </div>
	                   <ul>
                          <li>New user?&nbsp;</li>
                          <li><a class='bold' href='signup.php'>Register Now!</a></li>
                       </ul>
                    </div>";
    }

    $htmlout .= "
                </div>
                <!-- Begin Navigation -->
                <div id='navigation'>
                    <div id='nav'>
                        <ul>";

    if ($CURUSER)
    {
      $htmlout .= "
                           <li><a href='index.php'><span>{$lang['gl_home']}</span></a></li>
                           <li><a href='browse.php'><span>{$lang['gl_browse']}</span></a></li>
                           <li><a href='upload.php'><span>{$lang['gl_upload']}</span></a></li>
                           <li><a href='chat.php'><span>{$lang['gl_chat']}</span></a></li>
                           <li><a href='forums.php'><span>{$lang['gl_forums']}</span></a></li>
                           <li><a href='topten.php'><span>{$lang['gl_top_10']}</span></a></li>
                           <li><a href='links.php'><span>{$lang['gl_links']}</span></a></li>
                           <li><a href='faq.php'><span>{$lang['gl_faq']}</span></a></li>
                           <li><a href='staff.php'><span>{$lang['gl_staff']}</span></a></li>";

      if( $CURUSER['class'] >= UC_MODERATOR )
      {
        $htmlout .= "
                           <li><a href='admin.php'><span>{$lang['gl_admin']}</span></a></li>";
      }

    }
    else
    {
      $htmlout .= "
                           <li><a href='login.php'><span>{$lang['gl_login']}</span></a></li>
                           <li><a href='signup.php'><span>{$lang['gl_signup']}</span></a></li>
                           <li><a href='recover.php'><span>{$lang['gl_recover']}</span></a></li>";
    }

    $htmlout .= "
                        </ul>
                    </div>
                </div>
                <!-- End Navigation -->
            </div>
        </div>
        <!-- End Header -->

        <div class='clear'></div>

        <!-- Start Container -->
        <div id='container'>

            <!-- Start Maincolumn -->
            <div id='maincolumn'>";

            if ( $TBDEV['msg_alert'] && $msgalert )
            {
             $htmlout .= "
                <div class='alert'>
                    <a href='messages.php'><span>".sprintf($lang['gl_msg_alert'], $msgalert) ."&nbsp;". ($msgalert > 1 ? $lang['gl_msg_plural'] : $lang['gl_msg_singular']) . "!</span></a>
                </div>\n";
}


    return $htmlout;

} // stdhead

function stdfoot() {
  global $TBDEV;

    $htmlout = '';
    $htmlout .= "
            </div>
            <!-- End Maincolumn -->

            <div class='clear'></div>
        </div>
        <!-- End Container -->

        <!-- Begin Footer -->
        <div id='footer'>
            <div class='footerbg'>
                <p>Remember, if you see any specific instance of this software running publicly, it's within your rights under gpl to garner a copy of that derivative from the person responsible for that webserver.<br />
    <a href='http://www.tbdev.net'><img src='{$TBDEV['pic_base_url']}tbdev_btn_red.png' border='0' alt='Powered By TBDev &copy;2010' title='Powered By TBDev &copy;2010' /></a></p>
            </div>
        </div>
        <!-- End Footer -->

    </div>
    <!-- End Wrapper -->

</body>
</html>";

    return $htmlout;
}

function stdmsg($heading, $text)
{
    $htmlout = "<table class='main' width='750' border='0' cellpadding='0' cellspacing='0'>
    <tr><td class='embedded'>\n";
    
    if ($heading)
      $htmlout .= "<h2>$heading</h2>\n";

    $htmlout .= "<table width='100%' border='1' cellspacing='0' cellpadding='10'><tr><td class='text'>\n";
    $htmlout .= "{$text}</td></tr></table></td></tr></table>\n";

    return $htmlout;
}

function StatusBar() {

	global $CURUSER, $TBDEV, $lang, $msgalert;

	if (!$CURUSER)
		return "&nbsp;";


	$upped = mksize($CURUSER['uploaded']);

	$downed = mksize($CURUSER['downloaded']);

	$ratio = $CURUSER['downloaded'] > 0 ? $CURUSER['uploaded']/$CURUSER['downloaded'] : 0;

	$ratio = number_format($ratio, 2);

	$IsDonor = '';
	if ($CURUSER['donor'] == "yes")

	$IsDonor = "<img src='pic/star.gif' alt='donor' title='donor' />";


	$warn = '';
	if ($CURUSER['warned'] == "yes")

	$warn = "<img src='pic/warned.gif' alt='warned' title='warned' />";

	$res2 = @mysql_query("SELECT seeder, COUNT(*) AS pCount FROM peers WHERE userid=".$CURUSER['id']." GROUP BY seeder") or sqlerr(__LINE__,__FILE__);

	$seedleech = array('yes' => '0', 'no' => '0');

	while( $row = mysql_fetch_assoc($res2) ) {
		if($row['seeder'] == 'yes')
			$seedleech['yes'] = $row['pCount'];
		else
			$seedleech['no'] = $row['pCount'];

	}

/////////////// REP SYSTEM /////////////
//$CURUSER['reputation'] = 49;

	$member_reputation = get_reputation($CURUSER, 1);
////////////// REP SYSTEM END //////////

	$StatusBar = '';

		$StatusBar .= "
            <div style='float:left;'>
                $IsDonor$warn&nbsp;
                $member_reputation, {$lang['gl_ratio']}:&nbsp;$ratio &nbsp;&nbsp;{$lang['gl_uploaded']}:&nbsp;$upped
		        &nbsp;&nbsp;{$lang['gl_downloaded']}:&nbsp;$downed
                &nbsp;&nbsp;{$lang['gl_act_torrents']}:&nbsp;<img alt='{$lang['gl_seed_torrents']}' title='{$lang['gl_seed_torrents']}' src='pic/arrowup.gif' />&nbsp;{$seedleech['yes']}
                &nbsp;&nbsp;<img alt='{$lang['gl_leech_torrents']}' title='{$lang['gl_leech_torrents']}' src='pic/arrowdown.gif' />&nbsp;{$seedleech['no']}
            </div>
                <p style='text-align:right;'>".get_date(TIME_NOW, 'LONG', 1)."</p>";

	return $StatusBar;

}

?>