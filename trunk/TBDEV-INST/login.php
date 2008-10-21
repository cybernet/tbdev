<?php
require_once ("include/bittorrent.php");
require_once ("include/user_functions.php");
require_once ("include/bbcode_functions.php");


//ini_set('session.use_trans_sid', '0');

// Begin the session
session_start();
(time() - $_SESSION['captcha_time'] < 10) ? exit('No Spam - 10 Sec Delay - Stop Hammering !') : NULL;

stdhead("Login");

unset($returnto);
if (!empty($_GET["returnto"])) {
	$returnto = $_GET["returnto"];
	if (!isset($_GET["nowarn"])) {
		print("<center><h1><font color=white>Not logged in!</font></h1></center>\n");
		print("<center><p><b><font color=white>Error:</b> The page you tried to view can only be used when you're logged in.</font></p><center>\n");
	}
}

?>
<script type="text/javascript" src="captcha/captcha.js"></script>

<form method="post" action="takelogin.php">
<table align="center" border="0" cellpadding=5>
  <tr><center><font color="white">
    	<p><b>Note:</b> You need cookies enabled to log in.<b>[<?=$maxloginattempts;?>]</b> 
		failed logins in a row will result in banning your ip</p>
	<p>You have <b><?=remaining ();?></b> login attempt(s).</p></center>

    <td class="rowhead">Username:</td>
    <td align="left"><input type="text" size=40 name="username" /></td>
  </tr>
  <tr>
    <td class="rowhead">Password:</td>
    <td align="left"><input type="password" size=40 name="password" /></td>
  </tr>
<!--<tr><td class=rowhead>Duration:</td><td align=left><input type=checkbox name=logout value='yes' checked>Log me out after 15 minutes inactivity</td></tr>-->
  <tr>
    <td>&nbsp;</td>
    <td>
      <div id="captchaimage">
      <a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="refreshimg(); return false;" title="Click to refresh image">
      <img class="cimage" src="captcha/GD_Security_image.php?<?php echo time(); ?>" alt="Captcha image" />
      </a>
      </div>
     </td>
  </tr>
  <tr>
      <td class="rowhead">PIN:</td>
      <td>
        <input type="text" maxlength="6" name="captcha" id="captcha" onBlur="check(); return false;"/>
      </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" value="Log in!" class=btn>
    </td>
  </tr>
</table>
    </td>
  </tr>
</table>

<?

if (isset($returnto))
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . safechar($returnto) . "\" />\n");

?>
</form>
<center>
	<p>Forget password? <a href="recover.php">Click here</a> to resend!</p><p>
	New Member? <a href="signup.php">Sign-Up</a></p>
	<a href="http://www.mozilla.com" />
	<img alt="Get Firefox" border="0" src="/pic/firefox.png"></a>
	<a href="http://www.utorrent.com" />
	<img alt="Get Utorrent" border="0" src="/pic/utorrent.png"></a>
	<a href="http://tbdev.net" />
	<img alt="Powered By TBDEV" border="0" src="/pic/tbdev.png"></a> </center>
</font>
<?
?>