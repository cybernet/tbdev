<?php

/* Bigjoos, ColdFuSiOn */

require_once("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/html_functions.php");
require_once("include/bbcode_functions.php");

dbconn(false);
loggedinorreturn();

    $lang = array_merge( load_language('global') );
    
    $HTMLOUT = "";

$HTMLOUT .="<script type='text/javascript'>
/*<![CDATA[*/
  function toggle2(showHideDiv, switchTextDiv) {
    var ele = document.getElementById(showHideDiv);
    var text = document.getElementById(switchTextDiv);
    if(ele.style.display == 'block') {
          ele.style.display = 'none';
          text.innerHTML = '<img src=\'{$TBDEV['pic_base_url']}panel_on.gif\' alt=\'Read More\' />';
      }
    else {
      ele.style.display = 'block';
      text.innerHTML = '<img src=\'{$TBDEV['pic_base_url']}panel_off.gif\' alt=\'Read Less\' />';
    }
  }
/*]]>*/
</script>";
    
    $res = mysql_query("SELECT r. * , c.rcat_name, IF( (".TIME_NOW." > ctime ) , IF( (".TIME_NOW." - mtime ) < ( 3600 *48 ) , 1, 0), 2) AS updated FROM rules r LEFT JOIN rules_categories c ON c.cid = r.cid WHERE min_class_read <= ".$CURUSER['class']." GROUP BY cid, id");

    $cat_placeholder = '';

    $HTMLOUT .= "
                     <div class='cblock'>
                         <div class='cblock-header'>{$TBDEV['site_name']} Rules</div>
                         <div class='cblock-content'>
                             <div style='text-align:left;width:729px;border:1px solid #cecece;padding:5px;'>";

    while ($arr = mysql_fetch_assoc($res))
    {

      $arr['rcat_name'] = htmlsafechars( $arr['rcat_name'] );
      $arr['heading'] = htmlsafechars( $arr['heading'] );
      $arr['id'] = intval( $arr['id'] );

      $updated = ($arr['updated'] == 1 ? "&nbsp;<img src='{$TBDEV['pic_base_url']}updated.png' alt='Panel' />" : (($arr['updated'] == 2) ? "&nbsp;<img src='{$TBDEV['pic_base_url']}new.png' alt='panel' />": ""));

      if ($arr['cid'] != $cat_placeholder)
      $HTMLOUT .= "             <div style='background:#cecece;padding:5px;'>
                                    <span style='font-weight:bold;font-size:12pt;'>{$arr['rcat_name']}</span>
                                </div>";
      
      $HTMLOUT .= "             <div style='padding: 5px;'>
                                    <span style='font-weight:bold;font-size:10pt;'>{$arr['heading']}</span>&nbsp;<a id='myHeader_{$arr['id']}' href=\"javascript:toggle2('myContent_{$arr['id']}','myHeader_{$arr['id']}');\" ><img src='{$TBDEV['pic_base_url']}panel_on.gif' alt='Read More' /></a>
                                </div>
                                <div id='myContent_{$arr['id']}' style='display: none;'>
                                    <div class='clear'></div>
                                    <div><p>" . format_comment($arr['body']) . "</p></div>
                                </div>";

      $cat_placeholder = $arr['cid'];

    }

    $HTMLOUT .= "           </div>
                        </div>
                    </div>";

print stdhead("".$TBDEV['site_name']." Rules") . $HTMLOUT . stdfoot();
?>