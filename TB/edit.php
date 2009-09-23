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
require_once "include/bittorrent.php" ;
require_once "include/user_functions.php" ;
require_once "include/html_functions.php" ;

if (!mkglobal("id"))
	die();

$id = 0 + $id;
if (!$id)
	die();

dbconn();

loggedinorreturn();

    $res = mysql_query("SELECT * FROM torrents WHERE id = $id");
    $row = mysql_fetch_assoc($res);
    if (!$row)
      stderr('USER ERROR', 'No torrent found');


    
    if (!isset($CURUSER) || ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)) 
    {
      stderr('USER ERROR', "<h1>Can't edit this torrent</h1>\n<p>You're not the rightful owner, or you're not <a href='login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;nowarn=1'>logged in</a> properly.</p>\n");
    }


    $HTMLOUT = '';
    
    $HTMLOUT  .= "<form method='post' action='takeedit.php' enctype='multipart/form-data'>
    <input type='hidden' name='id' value='$id' />";
    
    if (isset($_GET["returnto"]))
      $HTMLOUT  .= "<input type='hidden' name='returnto' value='" . htmlspecialchars($_GET["returnto"]) . "' />\n");
    $HTMLOUT  .=  "<table border='1' cellspacing='0' cellpadding='10'>\n";
    
    $HTMLOUT  .= tr("Torrent name", "<input type='text' name='name' value='" . htmlspecialchars($row["name"]) . "' size='80' />", 1);
    $HTMLOUT  .= tr("NFO file", "<input type='radio' name='nfoaction' value='keep' checked='checked' />Keep current<br />".
	"<input type='radio' name='nfoaction' value='update' />Update:<br /><input type='file' name='nfo' size='80' />", 1);
    if ((strpos($row["ori_descr"], "<") === false) || (strpos($row["ori_descr"], "&lt;") !== false))
    {
      $c = "";
    }
    else
    {
      $c = " checked";
    }
    
    $HTMLOUT  .= tr("Description", "<textarea name='descr' rows='10' cols='80'>" . htmlspecialchars($row["ori_descr"]) . "</textarea><br />(HTML is not allowed. <a href='tags.php'>Click here</a> for information on available tags.)", 1);

    $s = "<select name='type'>\n";

    $cats = genrelist();
    
    foreach ($cats as $subrow) 
    {
      $s .= "<option value='" . $subrow["id"] . "'";
      if ($subrow["id"] == $row["category"])
        $s .= " selected='selected'";
      $s .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
    }

    $s .= "</select>\n";
    $HTMLOUT  .= tr("Type", $s, 1);
    $HTMLOUT  .= tr("Visible", "<input type='checkbox' name='visible'" . (($row["visible"] == "yes") ? " checked='checked'" : "" ) . " value='1' /> Visible on main page<br /><table border='0' cellspacing='0' cellpadding='0' width='420'><tr><td class='embedded'>Note that the torrent will automatically become visible when there's a seeder, and will become automatically invisible (dead) when there has been no seeder for a while. Use this switch to speed the process up manually. Also note that invisible (dead) torrents can still be viewed or searched for, it's just not the default.</td></tr></table>", 1);

    if (get_user_class() >= UC_MODERATOR) //($CURUSER["admin"] == "yes")
    {
      $HTMLOUT  .= tr("Banned", "<input type='checkbox' name='banned'" . (($row["banned"] == "yes") ? " checked='checked'" : "" ) . " value='1' /> Banned", 1);
    }

    $HTMLOUT  .= "<tr><td colspan='2' align='center'><input type='submit' value='Edit it!' class='btn' /> <input type='reset' value='Revert changes' class='btn' /></td></tr>
    </table>
    </form>
    <br />
    <form method='post' action='delete.php'>
    <table border='1' cellspacing='0' cellpadding='5'>
    <tr>
      <td class='embedded' style='background-color: #F5F4EA;padding-bottom: 5px' colspan='2'><b>Delete torrent.</b> Reason:</td>
    </tr>
    <tr>
      <td><input name='reasontype' type='radio' value='1' />&nbsp;Dead </td><td> 0 seeders, 0 leechers = 0 peers total</td>
    </tr>
    <tr>
      <td><input name='reasontype' type='radio' value='2' />&nbsp;Dupe</td><td><input type='text' size='40' name='reason[]' /></td>
    </tr>
    <tr>
      <td><input name='reasontype' type='radio' value='3' />&nbsp;Nuked</td><td><input type='text' size='40' name='reason[]' /></td>
    </tr>
    <tr>
      <td><input name='reasontype' type='radio' value='4' />&nbsp;TB rules</td><td><input type='text' size='40' name='reason[]' />(req)</td>
    </tr>
    <tr>
      <td><input name='reasontype' type='radio' value='5' checked='checked' />&nbsp;Other:</td><td><input type='text' size='40' name='reason[]' />(req)<input type='hidden' name='id' value='$id' /></td>
    </tr>";
    
    if (isset($_GET["returnto"]))
    {
      $HTMLOUT  .= "<input type='hidden' name='returnto' value='" . htmlspecialchars($_GET["returnto"]) . "' />\n";
		}
    
    $HTMLOUT  .= "<tr><td colspan='2' align='center'><input type='submit' value='Delete it!' class='btn' /></td>
    </tr>
    </table>
    </form>";
}

//////////////////////////// HTML OUTPIT ////////////////////////////////
    print stdhead("Edit torrent '{$row["name"]}'") . $HTMLOUT . stdfoot();

?>