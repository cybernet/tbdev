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
      $res1 = @mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND unread='yes' AND location = 1") or sqlerr(__LINE__,__FILE__);
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
      {$css}\n
      <!-- move all this stuff to footer asap -->
      <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js'></script>
      {$js}\n
      <script type='text/javascript'>
        $(document).ready(function(){
        
        $('#ff').click(function (event) { 
        event.preventDefault();
        $('#fastsearch').slideToggle('fast');
        });
        
      });
      </script>

    </head>
     <body>

          <!-- Begin Wrapper -->
          <div id='wrapper'>";

    $htmlout .= StatusBar();

    $htmlout .= "
              <!-- Begin Header -->
              <div id='header'>
                  <div id='menu'>
                      <ul>";

    if ($CURUSER)
    {
      $htmlout .= "
                         <li><a href='index.php'>{$lang['gl_home']}</a></li>
                         <li><a href='browse.php'>{$lang['gl_browse']}</a></li>
                         <li><a href='upload.php'>{$lang['gl_upload']}</a></li>
                         <li><a href='chat.php'>{$lang['gl_chat']}</a></li>
                         <li><a href='forums.php'>{$lang['gl_forums']}</a></li>
                         <li><a href='topten.php'>{$lang['gl_top_10']}</a></li>
                         <li><a href='links.php'>{$lang['gl_links']}</a></li>
                         <li><a href='faq.php'>{$lang['gl_faq']}</a></li>
                         <li><a href='staff.php'>{$lang['gl_staff']}</a></li>";

      if( $CURUSER['class'] >= UC_MODERATOR )
      {
        $htmlout .= "
                         <li><a href='admin.php'>{$lang['gl_admin']}</a></li>";
      }

    }
    else
    {
      $htmlout .= "
                         <li><a href='login.php'>{$lang['gl_login']}</a></li>
                         <li><a href='signup.php'>{$lang['gl_signup']}</a></li>
                         <li><a href='recover.php'>{$lang['gl_recover']}</a></li>";
    }

    $htmlout .= "
                      </ul>
                  </div>
              </div>
              <!-- End Header -->

		      <!-- Begin Content -->
		      <div id='content'>";

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
		      <!-- End Content -->

              <!-- Begin Footer -->
		      <div id='footer'></div>
		      <!-- End Footer -->

              <!-- Begin Ext-links -->
              <div id='ext'>
                  <div class='links'>
                     <a href='#'>Home</a> |
                     <a href='#'>Links</a> |
                     <a href='#'>Faqs</a> |
                     <a href='#'>Rules</a>
                  </div>
                  <div class='copyright'>
                      Powered by&nbsp;<a href='http://www.templateworld.com'>Tbdev</a>
                  </div>
              </div>
              <!-- End Ext-links -->

          </div>
          <!-- End Wrapper -->
     </body>
</html>
";

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
              <!-- Begin Statusbar -->
              <div id='statusbar'>
                  <div style='float:left;'>
                      $IsDonor$warn&nbsp;
                      $member_reputation, {$lang['gl_ratio']}:&nbsp;$ratio &nbsp;&nbsp;{$lang['gl_uploaded']}:&nbsp;$upped
		              &nbsp;&nbsp;{$lang['gl_downloaded']}:&nbsp;$downed
                      &nbsp;&nbsp;{$lang['gl_act_torrents']}:&nbsp;<img alt='{$lang['gl_seed_torrents']}' title='{$lang['gl_seed_torrents']}' src='pic/arrowup.gif' />&nbsp;{$seedleech['yes']}
                      &nbsp;&nbsp;<img alt='{$lang['gl_leech_torrents']}' title='{$lang['gl_leech_torrents']}' src='pic/arrowdown.gif' />&nbsp;{$seedleech['no']}
                  </div>
                  <p style='text-align:right;'>".date(DATE_RFC822)."</p>
              </div>
		      <!-- End Statusbar -->";

	return $StatusBar;

}

?>