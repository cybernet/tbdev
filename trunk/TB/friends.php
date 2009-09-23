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
require_once "include/bittorrent.php";
require_once "include/user_functions.php";

dbconn(false);
loggedinorreturn();

    $userid = isset($_GET['id']) ? (int)$_GET['id'] : $CURUSER['id'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    //if (!$userid)
    //	$userid = $CURUSER['id'];

    if (!is_valid_id($userid))
      stderr("Error", "Invalid ID.");

    if ($userid != $CURUSER["id"])
      stderr("Error", "Access denied.");


    // action: add -------------------------------------------------------------

    if ($action == 'add')
    {
      $targetid = 0+$_GET['targetid'];
      $type = $_GET['type'];

      if (!is_valid_id($targetid))
        stderr("Error", "Invalid ID.");

      if ($type == 'friend')
      {
        $table_is = $frag = 'friends';
        $field_is = 'friendid';
      }
      elseif ($type == 'block')
      {
        $table_is = $frag = 'blocks';
        $field_is = 'blockid';
      }
      else
        stderr("Error", "Unknown type.");

      $r = mysql_query("SELECT id FROM $table_is WHERE userid=$userid AND $field_is=$targetid") or sqlerr(__FILE__, __LINE__);
      if (mysql_num_rows($r) == 1)
        stderr("Error", "User ID is already in your ".htmlentities($table_is)." list.");

      mysql_query("INSERT INTO $table_is VALUES (0,$userid, $targetid)") or sqlerr(__FILE__, __LINE__);
      header("Location: {$TBDEV['baseurl']}/friends.php?id=$userid#$frag");
      die;
    }

    // action: delete ----------------------------------------------------------

    if ($action == 'delete')
    {
      $targetid = (int)$_GET['targetid'];
      $sure = isset($_GET['sure']) ? htmlentities($_GET['sure']) : false;
      $type = isset($_GET['type']) ? ($_GET['type'] == 'friend' ? 'friend' : 'block') : stderr('Error', 'LoL');

      if (!is_valid_id($targetid))
        stderr("Error", "Invalid ID.");

      if (!$sure)
        stderr("Delete $type","Do you really want to delete a $type? Click\n" .
          "<a href='?id=$userid&amp;action=delete&amp;type=$type&amp;targetid=$targetid&amp;sure=1'>here</a> if you are sure.");

      if ($type == 'friend')
      {
        mysql_query("DELETE FROM friends WHERE userid=$userid AND friendid=$targetid") or sqlerr(__FILE__, __LINE__);
        if (mysql_affected_rows() == 0)
          stderr("Error", "No friend found with ID");
        $frag = "friends";
      }
      elseif ($type == 'block')
      {
        mysql_query("DELETE FROM blocks WHERE userid=$userid AND blockid=$targetid") or sqlerr(__FILE__, __LINE__);
        if (mysql_affected_rows() == 0)
          stderr("Error", "No block found with ID");
        $frag = "blocks";
      }
      else
        stderr("Error", "Unknown type.");

      header("Location: {$TBDEV['baseurl']}/friends.php?id=$userid#$frag");
      die;
    }

    // main body  -----------------------------------------------------------------

    $res = mysql_query("SELECT * FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
    $user = mysql_fetch_assoc($res) or stderr("Error", "No user with ID.");
    
    $HTMLOUT = '';
    
    $donor = ($user["donor"] == "yes") ? "<img src='{$TBDEV['pic_base_url']}starbig.gif' alt='Donor' style='margin-left: 4pt' />" : '';
    $warned = ($user["warned"] == "yes") ? "<img src='{$TBDEV['pic_base_url']}warnedbig.gif' alt='Warned' style='margin-left: 4pt' />" : '';


    
/////////////////////// FRIENDS BLOCK ///////////////////////////////////////
    
    $res = mysql_query("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE userid=$userid ORDER BY name") or sqlerr(__FILE__, __LINE__);
    
    $count = mysql_num_rows($res);
    $friends = '';
    
    if( !$count)
    {
      $friends = "<em>Your friends list is empty.</em>";
    }
    else
    {
      
      while ($friend = mysql_fetch_assoc($res))
      {
        $title = $friend["title"];
        if (!$title)
          $title = get_user_class_name($friend["class"]);
        
        $userlink = "<a href='userdetails.php?id={$friend['id']}'><b>".htmlentities($friend['name'], ENT_QUOTES)."</b></a>";
        $userlink .= get_user_icons($friend) . " ($title)<br />last seen on " . get_date( $friend['last_access'],'');
        
        $delete = "<span class='btn'><a href='friends.php?id=$userid&amp;action=delete&amp;type=friend&amp;targetid={$friend['id']}'>Remove</a></span>";
          
        $pm = "&nbsp;<span class='btn'><a href='sendmessage.php?receiver={$friend['id']}'>Send PM</a></span>";
          
        $avatar = ($CURUSER["avatars"] == "yes" ? htmlspecialchars($friend["avatar"]) : "");
        if (!$avatar)
          $avatar = "{$TBDEV['pic_base_url']}default_avatar.gif";
          
        $friends .= "<div style='border: 1px solid black;padding:5px;'>".($avatar ? "<img width='50px' src='$avatar' style='float:right;' alt='' />" : ""). "<p >{$userlink}<br /><br />{$delete}{$pm}</p></div><br />";
        
      }
      
    }
    
    //if ($i % 2 == 1)
      //$HTMLOUT .= "<td class='bottom' width='50%'>&nbsp;</td></tr></table>\n";
    //print($friends);
   // $HTMLOUT .= "</td></tr></table>\n";

    
/////////////////////// FRIENDS BLOCK END///////////////////////////////////////
    
 
       
//////////////////// ENEMIES BLOCK ////////////////////////////

    $res = mysql_query("SELECT b.blockid as id, u.username AS name, u.donor, u.warned, u.enabled, u.last_access FROM blocks AS b LEFT JOIN users as u ON b.blockid = u.id WHERE userid=$userid ORDER BY name") or sqlerr(__FILE__, __LINE__);
    
    $blocks = '';
    
    if(mysql_num_rows($res) == 0)
    {
      $blocks = "<em>Your blocked users list is empty.</em>";
    }
    else
    {
      //$i = 0;
      //$blocks = "<table width='100%' cellspacing='0' cellpadding='0'>";
      while ($block = mysql_fetch_assoc($res))
      {
        $blocks .= "<div style='border: 1px solid black;padding:5px;'>";
        $blocks .= "<span class='btn' style='float:right;'><a href='friends.php?id=$userid&amp;action=delete&amp;type=block&amp;targetid={$block['id']}'>Delete</a></span><br />";
        $blocks .= "<p><a href='userdetails.php?id={$block['id']}'><b>" . htmlentities($block['name'], ENT_QUOTES) . "</b></a>";
        $blocks .= get_user_icons($block) . "</p></div><br />";
        
      }
      
    }
//////////////////// ENEMIES BLOCK END ////////////////////////////  
  
    $HTMLOUT .= "<table class='main' border='0' cellspacing='0' cellpadding='0'>".
    "<tr><td class='embedded'><h1 style='margin:0px'> Personal lists for ".htmlentities($user['username'], ENT_QUOTES)."</h1>$donor$warned</td></tr></table>";

    $HTMLOUT .= "<table class='main' width='750' border='0' cellspacing='0' cellpadding='0'>
    <tr>
      <td class='colhead'><h2 align='left' style='width:50%;'><a name='friends'>Friends list</a></h2></td>
      <td class='colhead'><h2 align='left' style='width:50%;vertical-align:top;'><a name='blocks'>Blocked list</a></h2></td>
    </tr>
    <tr>
      <td style='padding:10px;background-color:#ECE9D8;width:50%;'>$friends</td>
      <td style='padding:10px;background-color:#ECE9D8' valign='top'>$blocks</td>
    </tr>
    </table>";
    
    $HTMLOUT .= " <p><a href='users.php'><b>Find User/Browse User List</b></a></p>";
    
    print stdhead("Personal lists for {$user['username']}") . $HTMLOUT . stdfoot();
?>