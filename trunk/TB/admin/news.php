<?php
/*
+------------------------------------------------
|   TBDev.net BitTorrent Tracker PHP
|   =============================================
|   by CoLdFuSiOn
|   (c) 2003 - 2009 TBDev.Net
|   http://www.tbdev.net
|   =============================================
|   svn: http://sourceforge.net/projects/tbdevnet/
|   Licence Info: GPL
+------------------------------------------------
|   $Date$
|   $Revision$
|   $Author$
|   $URL$
+------------------------------------------------
*/

if ( ! defined( 'IN_TBDEV_ADMIN' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}

//require_once "include/bittorrent.php";
require_once "include/user_functions.php";
require_once "include/bbcode_functions.php";
require_once "include/html_functions.php";

    $input = array_merge( $_GET, $_POST);

    $mode = isset($input['mode']) ? $input['mode'] : '';

    $warning = '';
    
    $HTMLOUT = '';
    
    //   Delete News Item    //////////////////////////////////////////////////////
    if ($mode == 'delete')
    {
      $newsid = isset($input['newsid']) ? (int)$input["newsid"] : 0;
      
      if (!is_valid_id($newsid))
        stderr("Error","Invalid news item ID - Code 1.");

      $returnto = htmlentities($input["returnto"]);

      $sure = isset($input["sure"]) ? (int)$input['sure'] : 0;
      
      if (!$sure)
      {
        stderr("Delete news item","Do you really want to delete a news item? Click\n" .
          "<a href='admin.php?action=news&amp;mode=delete&amp;newsid=$newsid&amp;returnto=news&amp;sure=1'>here</a> if you are sure.");
      }
      
      @mysql_query("DELETE FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

      if ($returnto != "")
        header("Location: {$TBDEV['baseurl']}/admin.php?action=news");
      else
        $warning = "News item was deleted successfully.";
    }


    //   Add News Item    /////////////////////////////////////////////////////////
    if ($mode == 'add')
    {

      $body = isset($input["body"]) ? (string)$input["body"] : 0;
      
      if ( !$body OR strlen($body) < 4 )
        stderr("Error","The news item cannot be empty!");

      $added = isset($input["added"]) ? $input['added'] : 0;
      
      if (!$added)
        $added = time();

      @mysql_query("INSERT INTO news (userid, added, body) VALUES (".
        $CURUSER['id'] . ", $added, " . sqlesc($body) . ")") or sqlerr(__FILE__, __LINE__);
        
      if (mysql_affected_rows() == 1)
        $warning = "News item was added successfully.";
      else
        stderr("Error","Something weird just happened.");
    }

    
    //   Edit News Item    ////////////////////////////////////////////////////////
    if ($mode == 'edit')
    {

      $newsid = isset($input["newsid"]) ? (int)$input["newsid"] : 0;

      if (!is_valid_id($newsid))
        stderr("Error","Invalid news item ID - Code 2.");

      $res = @mysql_query("SELECT * FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

      if (mysql_num_rows($res) != 1)
        stderr("Error", "No news item with ID.");

      $arr = mysql_fetch_assoc($res);

      if ($_SERVER['REQUEST_METHOD'] == 'POST')
      {
        $body = isset($_POST['body']) ? $_POST['body'] : '';

        if ($body == "" OR strlen($_POST['body']) < 4)
          stderr("Error", "Body cannot be empty!");

        $body = sqlesc($body);

        $editedat = time();

        mysql_query("UPDATE news SET body=$body WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

        $returnto = isset($_POST['returnto']) ? htmlentities($_POST['returnto']) : '';

        if ($returnto != "")
          header("Location: {$TBDEV['baseurl']}/admin.php?action=news");
        else
          $warning = "News item was edited successfully.";
      }
      else
      {
        //$returnto = isset($_POST['returnto']) ? htmlentities($_POST['returnto']) : $TBDEV['baseurl'].'/news.php';
        $HTMLOUT .= "<h1>Edit News Item</h1>
        
        <form method='post' action='admin.php?action=news'>
        
        <input type='hidden' name='newsid' value='$newsid' />
        
        <input type='hidden' name='mode' value='edit' />
        
        <table border='1' cellspacing='0' cellpadding='5'>
        
        <tr><td style='padding: 0px'><textarea name='body' cols='145' rows='5'>" . htmlentities($arr['body'], ENT_QUOTES) . "</textarea></td></tr>
        
        <tr><td align='center'><input type='submit' value='Okay' class='btn' /></td></tr>
        
        </table>
        
        </form>\n";
        
        print  stdhead('Edit News Item') . $HTMLOUT . stdfoot();
        exit();
      }
    }

    
    
    //   Other Actions and followup    ////////////////////////////////////////////
    $HTMLOUT .= "<h1>Submit News Item</h1>\n";
    
    if (!empty($warning))
      $HTMLOUT .= "<p><font size='-3'>($warning)</font></p>";
    
    $HTMLOUT .= "<form method='post' action='admin.php?action=news'>
    <input type='hidden' name='mode' value='add' />
    <table border='1' cellspacing='0' cellpadding='5'>
      <tr>
        <td style='padding: 10px'>
          <textarea name='body' cols='141' rows='5' style='border: 0px'></textarea>
          <br /><br />
          <div align='center'>
          <input type='submit' value='Okay' class='btn' />
          </div>
        </td>
      </tr>
    </table>
    </form><br /><br />";

    $res = @mysql_query("SELECT * FROM news ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

    if (mysql_num_rows($res) > 0)
    {


      $HTMLOUT .= begin_main_frame();
      $HTMLOUT .= begin_frame();

      while ($arr = mysql_fetch_assoc($res))
      {
        $newsid = $arr["id"];
        $body = format_comment($arr["body"]);
        $userid = $arr["userid"];
        $added = get_date( $arr['added'],'');

        $res2 = @mysql_query("SELECT username, donor FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
        $arr2 = mysql_fetch_assoc($res2);

        $postername = $arr2["username"];

        if ($postername == "")
          $by = "unknown[$userid]";
        else
          $by = "<a href='userdetails.php?id=$userid'><b>$postername</b></a>" .
            ($arr2["donor"] == "yes" ? "<img src=\"{$TBDEV['pic_base_url']}star.gif\" alt='Donor' />" : "");
            
        $HTMLOUT .= begin_table(true);
        $HTMLOUT .= "<tr>
          <td class='embedded'>{$added}&nbsp;&nbsp;by&nbsp$by
            <div style='float:right;'>[<a href='admin.php?action=news&amp;mode=edit&amp;newsid=$newsid'><b>Edit</b></a>] - [<a href='admin.php?action=news&amp;mode=delete&amp;newsid=$newsid'><b>Delete</b></a>]
            </div>
          </td>
        </tr>
        <tr valign='top'>
          <td class='comment'>$body</td>
        </tr>\n";
        
        $HTMLOUT .= end_table();
        $HTMLOUT .= '<br />';
      }
      $HTMLOUT .= end_frame();
      $HTMLOUT .= end_main_frame();
    }
    else
      stdmsg("Sorry", "No news available!");
      
    print stdhead("Site news") . $HTMLOUT . stdfoot();
    die;
?>