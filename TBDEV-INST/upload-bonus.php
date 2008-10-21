<?php
require_once("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");
dbconn(false);
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
if (get_user_class() < UC_SYSOP)
hacker_dork("Upload-Apps - Nosey Cunt !");

stdhead("Manual Ratio Bonus");

?>
<style type="text/css">
<!--
.style1 {
	font-size: 16px;
	font-weight: bold;
}
.style5 {color: #FFFFFF; font-size: 9px; }
.style6 {font-size: 18px; font-weight: bold; }
-->
</style>

<form method="POST" action="take-upload-bonus.php">
  <p class="style6">Upload Tools</p>
  <table width="50%" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td colspan="2"><table width="38%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="76%"><div align="right">EVERYONE!&nbsp;&nbsp;</div></td>
          <td width="24%"> &nbsp;<input name="class" type="radio" value=">= 0" checked></td>
        </tr>
        <tr>
          <td><div align="right">All Power Users&nbsp;&nbsp;</div></td>
          <td>&nbsp;<input name="class" type="radio" value="= 1"></td>
        </tr>
        <tr>
          <td><div align="right">All VIP Members&nbsp;&nbsp;</div></td>
          <td>&nbsp;<input name="class" type="radio" value="= 2"></td>
        </tr>
        <tr>
          <td><div align="right">All Staff Members&nbsp;&nbsp;</div></td>
          <td>&nbsp;<input name="class" type="radio" value=">= 3"></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td width="50%"><div align="center"><strong>ADD</strong> to upload </div></td>
      <td width="50%"><div align="center"><strong>DEDUCT</strong> from upload </div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input type="submit" name="1gig" value="+1 GB">
      </div></td>
      <td><div align="center">
        <input type="submit" name="1gig2" value="-1 GB">
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input type="submit" name="2gig" value="+2 GB">
      </div></td>
      <td><div align="center">
        <input type="submit" name="2gig2" value="-2 GB">
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input type="submit" name="5gig" value="+5 GB">
      </div></td>
      <td><div align="center">
        <input type="submit" name="5gig2" value="-5 GB">
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input type="submit" name="10gig" value="+10 GB">
      </div></td>
      <td><div align="center">
        <input type="submit" name="10gig2" value="-10 GB">
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input name="15gig" type="submit" id="15gig" value="+15 GB" />
      </div></td>
      <td><div align="center">
        <input name="15gig2" type="submit" id="15gig2" value="-15 GB" />
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input name="25gig" type="submit" id="25gig" value="+25 GB" />
      </div></td>
      <td><div align="center">
        <input name="25gig2" type="submit" id="25gig2" value="-25 GB" />
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input name="50gig" type="submit" id="50gig" value="+50 GB" />
      </div></td>
      <td><div align="center">
        <input name="50gig2" type="submit" id="50gig2" value="-50 GB" />
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input name="75gig" type="submit" id="75gig" value="+75 GB" />
      </div></td>
      <td><div align="center">
        <input name="75gig2" type="submit" id="75gig2" value="-75 GB" />
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
        <input name="100gig" type="submit" id="100gig" value="+100 GB" />
      </div></td>
      <td><div align="center">
        <input name="100gig2" type="submit" id="100gig2" value="-100 GB" />
      </div></td>
    </tr>
  </table>
  <br>
  <table width="580" border="0" cellspacing="0" cellpadding="5">
    <tr>
      <td width="33%"><div align="center"><strong>REPLACE</strong> upload <br><span class="style5">(Can <strong>NOT</strong> be undone!!)</span></div></td>
      <td width="33%"><div align="center">
        <p><strong>MULTIPLY</strong> current upload<br>
        </p>
        </div></td>
      <td width="33%"><div align="center"><strong>DIVIDE</strong> current upload</div></td>
    </tr>
    <tr>
      <td><div align="center">
          <input name="r10gig" type="submit" id="r10gig" value="10 GB" />
      </div></td>
      <td><div align="center">
          <input name="x2" type="submit" id="x2" value="X 2" />
      </div></td>
      <td><div align="center">
        <input name="d2" type="submit" id="d2" value="/ 2" />
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
          <input name="r15gig" type="submit" id="r15gig" value="15 GB" />
      </div></td>
      <td><div align="center">
          <input name="x4" type="submit" id="x4" value="X 4" />
      </div></td>
      <td><div align="center">
        <input name="d4" type="submit" id="d4" value="/ 4" />
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
          <input name="r25gig" type="submit" id="r25gig" value="25 GB" />
      </div></td>
      <td><div align="center">
          <input name="x5" type="submit" id="x5" value="X 5" />
      </div></td>
      <td><div align="center">
        <input name="d5" type="submit" id="d5" value="/ 5" />
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
          <input name="r50gig" type="submit" id="r50gig" value="50 GB" />
      </div></td>
      <td><div align="center">
          <input name="x6" type="submit" id="x6" value="X 6" />
      </div></td>
      <td><div align="center">
        <input name="d6" type="submit" id="d6" value="/ 6" />
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
          <input name="r75gig" type="submit" id="r75gig" value="75 GB" />
      </div></td>
      <td><div align="center">
          <input name="x8" type="submit" id="x8" value="X 8" />
      </div></td>
      <td><div align="center">
        <input name="d8" type="submit" id="d8" value="/ 8" />
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
          <input name="r100gig" type="submit" id="r100gig" value="100 GB" />
      </div></td>
      <td><div align="center">
          <input name="x10" type="submit" id="x10" value="X 10" />
      </div></td>
      <td><div align="center">
        <input name="d10" type="submit" id="d10" value="/ 10" />
      </div></td>
    </tr>
    <tr>
      <td><div align="center">
          <input name="r200gig" type="submit" id="r200gig" value="200 GB" />
      </div></td>
      <td><div align="center">
          <input name="x15" type="submit" id="x15" value="X 15" />
      </div></td>
      <td><div align="center">
        <input name="d15" type="submit" id="d15" value="/ 15" />
      </div></td>
    </tr>
  </table>
  <p>Automatic PM's are sent when: <strong>Adding,</strong> <strong>Replacing</strong> and <strong>Multiplying</strong> ONLY! <span class="style1"><img src="pic/smilies/grin.gif" alt=":D" /></span></p>
  <p>&#3665;&#1769;&#1758;&#1769;&#3665; Mod by Ashley &#3665;&#1769;&#1758;&#1769;&#3665; </p>
</form>
<p>
  <? stdfoot(); ?>
</p>

