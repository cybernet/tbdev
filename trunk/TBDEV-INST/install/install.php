<html><head>
<title>TBDEV.NET Pre-Coded Installer</title>
<link rel="stylesheet" href="themes/default/default.css" type="text/css">
<style type="text/css">
p {
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	padding:0px 0px 0px 0px;
}

</style>
<script type="text/javascript">
 
var ClassName = "color"; 
var FocusColor = "#0099FF"; 
 
window.onload = function() {
    var inputfields = document.getElementsByTagName("input");
    for(var x = 0 ; x < inputfields.length ; x++ ) {
        if(inputfields[x].getAttribute("class") == ClassName) {
            inputfields[x].onfocus = function() {
                OriginalColor = this.style.border;
                this.style.border = "2px solid "+FocusColor;
            }
            inputfields[x].onblur = function() {
                this.style.border = OriginalColor;
            }
        }
    }   
}
</script>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
<?php
////////////magic quotes check//////////////////////////////
if (get_magic_quotes_gpc() === 1 || get_magic_quotes_runtime() === 1) {
    $error = 1;
    $message = "Disable magic_quotes in your php.ini before commencing with this install !";
} else {
    $error = 0;
    $message = "Magic quotes are off \o/";
}
require "functions.php";  

// Form
function step_1()
{
	global $message, $error;
	echo'
    <form method="post" action="install.php">
  <center><img src=/pic/logo.gif></center>
  <table width="700px" border="0" align="center">
    <tr>
      <td colspan="2"><center><strong>Database Configuration</strong></center></td>
    </tr>
    <tr>
      <td>Database Server (use localhost if not sure)</td>
      <td><input class="color" name="server" type="text" id="server" value="localhost" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Database Name</td>
      <td><input class="color" name="dbname" type="text" id="dbname" value="Installerv1" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Database User</td>
      <td><input class="color" name="dbuser" type="text" id="dbuser" value="tb" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Database Password</td>
      <td><input class="color" name="dbpass" type="password" id="dbpass" size="40" maxlength="40" /></td>
    </tr>

    <tr>
      <td colspan="2"><center><strong>Coder User Configuration</strong></center></td>
    </tr>
    <tr>
      <td>Coder Username - Do not change this</td>
      <td><input class="color" name="coderuser" type="text" id="coderuser" value="Admin" size="40" maxlength="40" /></td>
     </tr>
    <tr>
      <td>Coder Password</td>
      <td><input class="color" name="coderpass" type="password" id="coderpass" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Coder Password Confirm</td>
      <td><input class="color" name="coderpass2" type="password" id="coderpass2" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Coder Email</td>
      <td><input class="color" name="codermail" type="text" id="codermail" size="40" maxlength="40" /></td>
    </tr>

    <tr>
      <td colspan="2"><center><strong>Basic Site Configuration</strong></center></td>
    </tr>
    <tr>
      <td>Site Name</td>
      <td><input class="color" name="sitename" type="text" id="sitename" value="::InstallerV1::" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Domain (no ending slash)</td>
      <td><input class="color" name="domain" type="text" id="domain" value="http://domain.com" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Site Email Address</td>
      <td><input class="color" name="sitemail" type="text" id="sitemail" value="noreply@domain.com" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Announce Url ( ..../announce.php)</td>
      <td><input class="color" name="announce" type="text" id="announce" value="http://domain.com/announce.php" size="50" maxlength="50" /></td>
    </tr>
    <tr>
      <td>Max users</td>
      <td><input class="color" name="maxusers" type="text" id="maxusers" value="5000" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Peer limit</td>
      <td><input class="color" name="peerlimit" type="text" id="peerlimit" value="50000" size="40" maxlength="40" /></td>
    </tr>
       <tr>
      <td colspan="2"><br /><strong>Quick notes for installing this Pre-Coded Tbdev Release By Bigjoos - Make sure you follow the next on-screen instructions very carefully after install</strong></td>
    </tr>
      <tr>
      <td colspan="2"><br /><strong>Ensure include/secrets.php is CHMOD666 for Linux Install</strong></td>
    </tr>
     ' . (isset($error) && $error == 0?'<p align="center" style="color: green">' . $message . '</p>':'<p align="center" style="color: red">' . $message . '</p>') . '
	<tr>
      <td colspan="2"><div align="center">
        <input name="install" type="submit" class="red" value="Install" />
      </div></td>
    </tr>
  </table>
</form>
	';  
}

include('../include/secrets.php');
if( defined("TB_INSTALLED") )
{
	die('Already installed <a href="../index.php">INDEX</a>');
	exit;
}	
if (isset($_POST['install'])) { 
if( $_POST['install'] || $_GET['install'] )
{
	update_config();
	basic_query();
	insert_coder();
	config();
	finale();
}
}
else
{
	step_1();
}
?>