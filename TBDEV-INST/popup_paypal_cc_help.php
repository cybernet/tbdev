<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
?>
<html><head>
<title>PayPal info</title>
<link rel="stylesheet" href="default.css" type="text/css">
</head>
<br><br><center><table cellpadding="10">
<tr><td class="clearalt6"><p>You can easily pay by Credit Card using <b><?=$SITENAME?>'s</b> secure and reliable PayPal Payment Portal.<br>
A Paypal account is <i>not</i> required.<p>To pay by Credit Card, look for this link on the main Paypal screen:<br>
<center><img src="pic/paypal/PayPal-ccCheckout.gif"></center><br><br>
Note: if you are a PayPal member, you can either use your account,
or use a Credit Card that is not associated with a PayPal account.
In that case, you would also need to use an email address that is not associated with a PayPal account.
<br>
Please contact us if you have any questions or concerns.<br><br><center>[ <a href="javascript:window.close();">Close This Window</a> ]</center></td>
</tr></table></center>
<?
die();
?>