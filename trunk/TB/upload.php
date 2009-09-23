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
require_once "include/html_functions.php";

dbconn(false);

loggedinorreturn();

    $HTMLOUT = '';

    if ($CURUSER['class'] < UC_UPLOADER)
    {
      stderr("Sorry...", "You are not authorized to upload torrents.  (See <a href=\"faq.php#up\">Uploading</a> in the FAQ.)");
    }


    $HTMLOUT .= "<div align='center'>
    <form enctype='multipart/form-data' action='takeupload.php' method='post'>
    <input type='hidden' name='MAX_FILE_SIZE' value='{$TBDEV['max_torrent_size']}' />
    <p>The tracker's announce url is <b>{$TBDEV['announce_urls'][0]}</b></p>";


    $HTMLOUT .= "<table border='1' cellspacing='0' cellpadding='10'>
    <tr>
      <td class='heading' valign='top' align='right'>Torrent file</td>
      <td valign='top' align='left'><input type='file' name='file' size='80' /></td>
    </tr>
    <tr>
      <td class='heading' valign='top' align='right'>Torrent name</td>
      <td valign='top' align='left'><input type='text' name='name' size='80' /><br />(Taken from filename if not specified. <b>Please use descriptive names.</b>)</td>
    </tr>
    <tr>
      <td class='heading' valign='top' align='right'>NFO file</td>
      <td valign='top' align='left'><input type='file' name='nfo' size='80' /><br />(<b>Optional.</b> Can only be viewed by power users.)</td>
    </tr>
    <tr>
      <td class='heading' valign='top' align='right'>Description</td>
      <td valign='top' align='left'><textarea name='descr' rows='10' cols='80'></textarea>
      <br />(HTML/BB code is <b>not</b> allowed.)</td>
    </tr>";

    $s = "<select name='type'>\n<option value='0'>(choose one)</option>\n";

    $cats = genrelist();
    
    foreach ($cats as $row)
    {
      $s .= "<option value='{$row["id"]}'>" . htmlspecialchars($row["name"]) . "</option>\n";
    }
    
    $s .= "</select>\n";
    
    $HTMLOUT .= "<tr>
        <td class='heading' valign='top' align='right'>Type</td>
        <td valign='top' align='left'>$s</td>
      </tr>
      <tr>
        <td align='center' colspan='2'><input type='submit' class='btn' value='Do it!' /></td>
      </tr>
    </table>
    </form>
    </div>";

////////////////////////// HTML OUTPUT //////////////////////////

    print stdhead("Upload") . $HTMLOUT . stdfoot();

?>