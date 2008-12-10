<?php
require_once("include/function_cache.php");
require_once("include/bbcode_functions.php");
cache_start(6000,moresmilies);
?>
<html><head>
<title>more clickable smilies</title>
</head>
<BODY BGCOLOR="#000000" TEXT="#ffffff" LINK="#ff0000" VLINK="#808080">

<script language=javascript>

function SmileIT(smile,form,text){
window.opener.document.forms[form].elements[text].value = window.opener.document.forms[form].elements[text].value+" "+smile+" ";
window.opener.document.forms[form].elements[text].focus();
window.close();
}
</script>

<table class="lista" width="100%" cellpadding="1" cellspacing="1">
<tr>
<?

while ((list($code, $url) = each($smilies))) {
if ($count % 3==0)
print("\n<tr>");

print("\n\t<td class=\"lista\" align=\"center\"><a href=\"javascript: SmileIT('".str_replace("'","\'",$code)."','".$_GET["form"]."','".$_GET["text"]."')\"><img border=0 src=pic/smilies/".$url."></a></td>");
$count++;

if ($count % 3==0)
print("\n</tr>");
}

while ((list($code, $url) = each($privatesmilies))) {
if ($count % 3==0)
print("\n<tr>");

print("\n\t<td class=\"lista\" align=\"center\"><a href=\"javascript: SmileIT('".str_replace("'","\'",$code)."','".$_GET["form"]."','".$_GET["text"]."')\"><img border=0 src=pic/smilies/".$url."></a></td>");
$count++;

if ($count % 3==0)
print("\n</tr>");
}

?>
</tr>
</table>
<div align="center">
<a href="javascript: window.close()"><? echo CLOSE; ?></a>
</div>
<?
register_shutdown_function("cache_end"); 
