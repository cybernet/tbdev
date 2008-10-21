<?php
/*****************************************************
*Bonus points manager ripped and modded by Bigjoos *
*                                                 *
*************************************************/
require_once("include/bittorrent.php");
//require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

if (get_user_class() < UC_ADMINISTRATOR)
hacker_dork("Bonus Manage - Nosey Cunt !");


  function whereto ()
  {
    $msg = '<br><div class=success align=center>Go back to <a href="' . $_SERVER['SCRIPT_NAME'] . '?act=bonuspoints">Admin Panel</a> | <a href="' . $_SERVER['SCRIPT_NAME'] . '?act=bonuspoints&action=add">Add Bonus</a> | <a href="' . $_SERVER['SCRIPT_NAME'] . '?act=bonuspoints&action=showlist">Show User List</a> | <a href="' . $_SERVER['SCRIPT_NAME'] . '?act=bonuspoints&action=reset">Reset User Points (ALL)</a> |<a href="' . $_SERVER['HTTP_REFERRER'] . '"> Click here to return to where you were previously.</a></div>';
    return $msg;
  }


 
  $action = (isset ($_POST['action']) ? htmlspecialchars ($_POST['action']) : (isset ($_GET['action']) ? htmlspecialchars ($_GET['action']) : 'adminpanel'));
  $allowed_actions = array ('showlist', 'edituser', 'updateuser', 'updatebonussystem', 'updatebonussystemsave', 'adminpanel', 'add', 'add_save', 'resetall', 'reset');
  if (!in_array ($action, $allowed_actions))
  {
    $action = 'adminpanel';
  }

  $countrows = number_format (get_row_count ('users WHERE seedbonus > 0')) + 1;
  $page = 0 + $_GET['page'];
  $perpage = 10;
   list ($pagertop, $pagerbottom, $limit) = pager ($perpage, $countrows, $_SERVER['SCRIPT_NAME'] . '?act=bonuspoints&action=showlist&');

  if ($action == 'showlist')
  {
    stdhead ();
    echo '<table border=1 cellspacing=0 cellpadding=10 width=100%>';
    echo '<tr><td colspan=6>' . $pagertop . '</td></tr>';
    echo '<tr><td class=colhead align=center>User ID</td><td class=colhead align=center>Username</td><td class=colhead align=center>Bonus Point</td><td class=colhead align=left>Bonus Comment</td><td class=colhead align=left>Total Uploaded</td><td class=colhead align=left>Update</td></tr>';
    ($res = sql_query ('' . 'SELECT id, username, seedbonus, bonuscomment, uploaded FROM users WHERE seedbonus > 0 ORDER by seedbonus DESC ' . $limit) OR sqlerr (__FILE__, 40));
    while ($arr = mysql_fetch_array ($res))
    {
      echo '<tr><td align=center><a href=' . $BASEURL . '/userdetails.php?id=' . $arr['id'] . '>' . $arr['id'] . '</a></td><td align=center><a href=' . $BASEURL . '/userdetails.php?id=' . $arr['id'] . '>' . $arr['username'] . '</a></td><td align=center>' . $arr['seedbonus'] . ' points</td><td><textarea cols=35 rows=5 id=specialboxpp>' . $arr['bonuscomment'] . '</textarea></td><td>' . mksize ($arr['uploaded']) . '</td><td><a href="' . $_SERVER['SCRIPT_NAME'] . '?act=bonuspoints&action=edituser&id=' . $arr['id'] . '">edit user</a></td></tr>';
    }

    echo '<tr><td colspan=6>' . $pagerbottom . '</td></tr>';
    echo '<tr><td colspan=6>' . whereto () . '</td></tr>';
  }
  else
  {
    if ($action == 'edituser')
    {
      $id = (isset ($_POST['id']) ? 0 + $_POST['id'] : (isset ($_GET['id']) ? 0 + $_GET['id'] : ''));
      if (!is_valid_id ($id))
      {
        stderr ('Error', 'Invalid ID');
      }

      $res = sql_query ('SELECT id,username,seedbonus FROM users WHERE id = ' . sqlesc ($id));
      if (mysql_num_rows ($res) == '0')
      {
        stderr ('Error', 'Nothing Found!');
      }

      stdhead ();
      echo '<table border=1 cellspacing=0 cellpadding=10 width=100%>';
      echo '<form method=post action="' . $_SERVER['SCRIPT_NAME'] . '">
	<input type=hidden name=act value=bonuspoints>
	<input type=hidden name=action value=updateuser>';
      echo '<tr><td class=colhead align=center>User ID</td><td class=colhead align=center>User Name</td><td class=colhead align=left>User Points</td><td class=colhead align=center>Update</td></tr>';
      while ($arr = mysql_fetch_array ($res))
      {
        echo '<tr><td align=center valign=top><input type=hidden name=id value="' . $arr['id'] . '"><a href=' . $BASEURL . '/userdetails.php?id=' . $arr['id'] . '>' . $arr['id'] . '</a></td><td align=center valign=top><a href=' . $BASEURL . '/userdetails.php?id=' . $arr['id'] . '>' . $arr['username'] . '</a></td><td align=left valign=top><input id=specialboxn type=text name=seedbonus value="' . $arr['seedbonus'] . '"></td><td align=center valign=top><input type=submit name=edit value="update points" class=btn></td></tr>';
      }

      echo '</form>';
    }
    else
    {
      if ($action == 'updateuser')
      {
        $id = (isset ($_POST['id']) ? 0 + $_POST['id'] : (isset ($_GET['id']) ? 0 + $_GET['id'] : ''));
        if (!is_valid_id ($id))
        {
          stderr ('Error', 'Invalid ID');
        }

        $seedbonus = sqlesc ($_POST['seedbonus']);
        (sql_query ('' . 'UPDATE users SET seedbonus = ' . $seedbonus . ' WHERE id = ' . sqlesc ($id)) OR stderr ('Update User', '' . 'Unable to update: ' . $id));
        stderr ('Update User', '' . 'User Id: ' . $id . ' successfull updated.' . whereto (), false);
      }
      else
      {
        if ($action == 'updatebonussystem')
        {
          $id = (isset ($_POST['id']) ? 0 + $_POST['id'] : (isset ($_GET['id']) ? 0 + $_GET['id'] : ''));
          if (!is_valid_id ($id))
          {
            stderr ('Error', 'Invalid ID');
          }

          $res = sql_query ('SELECT * FROM bonus WHERE id = ' . sqlesc ($id));
          if (mysql_num_rows ($res) == '0')
          {
            stderr ('Error', 'Nothing Found!');
          }

          stdhead ();
          echo '<table border=1 cellspacing=0 cellpadding=10 width=100%>';
          echo '<tr><td class=colhead align=center>ID</td><td class=colhead align=left>Name</td><td class=colhead align=left>Points</td><td class=colhead align=left>Description</td><td class=colhead align=center>Delete?</td><td class=colhead align=center>Menge</td><td class=colhead align=center>Update</td></tr>';
          echo '<form method=post action="' . $_SERVER['SCRIPT_NAME'] . '">
	<input type=hidden name=act value=bonuspoints>
	<input type=hidden name=action value=updatebonussystemsave>';
          while ($arr = mysql_fetch_array ($res))
          {
            echo '<tr><td align=center valign=top><input type=hidden name=id value="' . $arr['id'] . '">' . $arr['id'] . '</td><td align=left valign=top><input type=text name=bonusname value="' . $arr['bonusname'] . '" id="specialboxss"></td><td align=center valign=top><input type=text name=points value="' . $arr['points'] . '" size=5 id="specialboxes"></td><td align=left valign=top><textarea name=description id="specialboxs" cols=20 rows=10>' . $arr['description'] . '</textarea></td><td align=center valign=top><input type=checkbox name=delete value=1></td><td align=center valign=top><input type=text name=menge id="specialboxss" size=9 value="' . $arr['menge'] . '"><br>1 GB = 1073741824<br>2.5GB = 2684354560<br>5GB = 5368709120<br>10GB = 10737418240<br>and so far...</td><td align=center valign=top><input type=submit name=update value=update class=btn></td></tr>';
          }

          echo '</form>';
        }
        else
        {
          if ($action == 'updatebonussystemsave')
          {
            $id = (isset ($_POST['id']) ? 0 + $_POST['id'] : (isset ($_GET['id']) ? 0 + $_GET['id'] : ''));
            if (!is_valid_id ($id))
            {
              stderr ('Error', 'Invalid ID');
            }

            $bonusname = sqlesc ($_POST['bonusname']);
            $points = sqlesc ($_POST['points']);
            $description = sqlesc ($_POST['description']);
            $sure = $_GET['sure'];
            $delete = (isset ($_POST['delete']) ? htmlspecialchars ($_POST['delete']) : (isset ($_GET['delete']) ? htmlspecialchars ($_GET['delete']) : ''));
            if ($delete)
            {
              if (!$sure)
              {
                stderr ('Delete bonus', 'Sanity check: You are about to delete a bonus. Click
' . '<a href=\'' . $_SERVER['SCRIPT_NAME'] . ('' . '?act=bonuspoints&action=updatebonussystemsave&id=' . $id . '&sure=1&delete=1\'>here</a> if you are sure.'), false);
              }
              else
              {
                (sql_query ('DELETE FROM bonus WHERE id = ' . sqlesc ($id)) OR stderr ('Delete bonus', '' . 'Unable to delete: ' . $id));
                stderr ('Delete bonus', '' . 'Bonus Id: ' . $id . ' successfull deleted.' . whereto (), false);
              }
            }
            else
            {
              (sql_query ('' . 'UPDATE bonus SET bonusname = ' . $bonusname . ', points = ' . $points . ', description = ' . $description . ' WHERE id = ' . sqlesc ($id)) OR stderr ('Update bonus', '' . 'Unable to update: ' . $id));
              stderr ('Update bonus', '' . 'Bonus Id: ' . $id . ' successfull updated.' . whereto (), false);
            }
          }
          else
          {
            if ($action == 'adminpanel')
            {
              stdhead ();
              echo '<table border=1 cellspacing=0 cellpadding=10 width=100%>';
              echo '<tr><td class=colhead align=center>ID</td><td class=colhead align=left>Name</td><td class=colhead align=center>Points</td><td class=colhead align=left>Description</td><td class=colhead align=center>Update</td></tr>';
              $res = sql_query ('SELECT * FROM bonus ORDER BY id ASC');
              while ($arr = mysql_fetch_array ($res))
              {
                echo '<tr><td align=center>' . $arr['id'] . '</td><td align=left>' . $arr['bonusname'] . '</td><td align=center>' . $arr['points'] . '</td><td align=left><div align=justify class=success>' . $arr['description'] . '</div></td><td align=center><a href="' . $_SERVER['SCRIPT_NAME'] . '?act=bonuspoints&action=updatebonussystem&id=' . $arr['id'] . '">edit</a></td></tr>';
              }

              echo '<tr><td colspan=5>' . whereto () . '</td></tr>';
            }
            else
            {
              if ($action == 'add')
              {
                stdhead ();
                echo '<table border=1 cellspacing=0 cellpadding=10 width=100%>';
                echo '<tr><td class=colhead align=left>Name</td><td class=colhead align=left>Points</td><td class=colhead align=left>Description</td><td class=colhead align=center>Menge</td><td class=colhead align=center>Update</td></tr>';
                echo '<form method=post action="' . $_SERVER['SCRIPT_NAME'] . '">
	<input type=hidden name=act value=bonuspoints>
	<input type=hidden name=action value=add_save>';
                echo '<tr><td align=left valign=top><input type=text name=bonusname id=specialboxs></td><td align=left valign=top><input type=text name=points size=5 id=specialboxes></td><td align=left valign=top><textarea name=description cols=20 rows=10 id=specialboxs></textarea></td><td align=left valign=top><input type=text name=menge size=10 id=specialboxs><br>1 GB = 1073741824<br>2.5GB = 2684354560<br>5GB = 5368709120<br>10GB = 10737418240<br>and so far...</td><td align=left valign=top><input type=submit name=add value=add class=btn></td>';
                echo '</form>';
              }
              else
              {
                if ($action == 'add_save')
                {
                  $bonusname = sqlesc ($_POST['bonusname']);
                  $points = sqlesc ($_POST['points']);
                  $description = sqlesc ($_POST['description']);
                  $menge = sqlesc ($_POST['menge']);
                  (sql_query ('' . 'INSERT INTO bonus (bonusname, points, description, menge, art) VALUES (' . $bonusname . ', ' . $points . ', ' . $description . ', ' . $menge . ', \'traffic\')') OR stderr ('ADD bonus', 'Unable to add bonus!'));
                  stderr ('ADD bonus', 'New bonus successfull added.' . whereto (), false);
                }
                else
                {
                  if ($action == 'resetall')
                  {
                    $sure = $_GET['sure'];
                    $query = 'WHERE enabled=\'yes\' AND status=\'confirmed\'';
                    $usergroup = (isset ($_POST['usergroup']) ? (int)$_POST['usergroup'] : (isset ($_GET['usergroup']) ? (int)$_GET['usergroup'] : ''));
                    if (($usergroup == '-' OR !is_valid_id ($usergroup)))
                    {
                      $usergroup = '';
                    }

                    if (!empty ($usergroup))
                    {
                      $query .= '' . ' AND usergroup = ' . $usergroup;
                    }

                    if (!$sure)
                    {
                      stderr ('Reset ALL Points', 'Sanity check: You are about to reset all points for following Usergroup: <b>' . ($usergroup ? '[' . get_user_class_name ($usergroup) . ']' : '[ALL Usergroups]') . '</b>. Click <a href=\'' . $_SERVER['SCRIPT_NAME'] . '?act=bonuspoints&action=resetall&sure=1' . ($usergroup ? '' . '&usergroup=' . $usergroup : '') . '\'>here</a> if you are sure.', false);
                    }
                    else
                    {
                      (sql_query ('' . 'UPDATE users SET seedbonus = 0.0 ' . $query) OR stderr ('Reset ALL Points', 'Unable to reset!.' . whereto (), false));
                      stderr ('Reset ALL Points', 'All points have been reset.' . whereto (), false);
                    }
                  }
                  else
                  {
                    if ($action == 'reset')
                    {
                      stdhead ();
                      echo '<table border=1 cellspacing=0 cellpadding=10 width=100%>';
                      echo '<form method=post action="' . $_SERVER['SCRIPT_NAME'] . '">
	<input type=hidden name=act value=bonuspoints>
	<input type=hidden name=action value=resetall>';
                      echo '<tr><td>';
                      echo '<div class=error>Are you sure you want to Reset User Points?</div></td><td><div class=error>';
                      //$checkbox = _checkbox_ ('Usergroup', 'usergroup');
                      echo ' <input type=submit value="Reset Points" class=btn>';
                      echo '</div></td></tr><tr><td colspan=2>' . whereto () . '</td></tr></form>';
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }

  echo '</table>';
  stdfoot ();
?>