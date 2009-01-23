<?php
$string = $_SERVER['QUERY_STRING'];
if($string=='404') { $page = 'Page Not Found - 404'; $text = 'Sorry, The page you are looking for cannot be found.'; }
if($string=='401') { $page = 'Authorization Required - 401'; $text = 'You need to be Authorized to access this page. You do not have the correct credentials.'; }
if($string=='403') { $page = 'Forbidden - 403'; $text = 'You do not have full permission to access this page.'; }
if($string=='500') { $page = 'Internal Server Error - 500'; $text = 'There seems to have been an error on this server. Please notify the webmaster of the site.'; }
if($string=='400') { $page = 'Bad Request - 400'; $text = 'There has been an error with the page you are trying to view. Please try again later.'; }
?>
<?php
$domain = $_SERVER['HTTP_HOST'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="5; url=http://<?php echo $domain; ?>">
<title><?php echo $page; ?></title>
<style type="text/css">
<!--
body
{
margin: 4;
background-color: white;
}
p
{
margin: 0;
font-family: Arial, Arial, Helvetica, sans-serif;
color: #000000;
font-size: 14px;
}
.style1 {	color: #666666;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
-->
</style>
<body>
    <div align="center">
      <table width="300" border="0" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr>

          <td width="300" height="50" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
        </tr>
        <tr>
          <td height="520" valign="top"><img src="pic/error404.png" width="300" height="520" border="0" usemap="#Map"></td>
        </tr>
        <tr>
          <td height="14" valign="top"><div align="center"><span class="style1">copyright &copy; Tbdev Installer</span></div></td>

        </tr>
          </table>
    </div>
<map name="Map"><area shape="rect" coords="99,425,203,481" href="http://yoursite.org">
</map></tr>
<p><b><center><?php echo $page; ?></b></p>
<p><?php echo $text; ?></p><br />
<p>You will be redirected back to <?php echo $domain; ?> in 5 seconds</p></center>
</body>
<html>
