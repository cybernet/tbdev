<?php
require ("include/bittorrent.php");
require ("include/bbcode_functions.php");
dbconn();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

stdhead();
?>

<b>Click the PayPal button below if you wish to make a donation!</b>

<p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_donations">
<input type="hidden" name="business" value="youraddy@live.co.uk">
<input type="hidden" name="item_name" value="Yoursite donation">
<input type="hidden" name="no_shipping" value="0">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="GBP">
<input type="hidden" name="tax" value="0">
<input type="hidden" name="lc" value="GB">
<input type="hidden" name="bn" value="PP-DonationsBF">
<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>
</p>

<p>
<? begin_main_frame(); begin_frame(); ?>
<center><b><font color=green><font size=2>Next server bill is due at the end of the month </b></font>
<p>
<b><font color=WHITE>All Donations are apprecatied NO matter how big or small there are,<br> every little helps and goes towards the server , If enough donations are recieved we can purchase a seedbox </b></font>
<p>
<b><font color=orange>Click the PayPal link below if you wish to make a donation!</b></font>
<p>
<p>
<center><b><font color=white>This is a non profit site and all donations go back into providing upgrades ect</b></font>
<? end_frame(); begin_frame("Other ways to donate"); ?>
No other ways at the moment...
<? end_frame(); end_main_frame(); ?>
</p>

<b>After you have donated -- make sure to <a href=sendmessage.php?receiver=1>send us</a> the <font color=red>transaction id</font> so we can credit your account!</b>
<?
stdfoot();
?>