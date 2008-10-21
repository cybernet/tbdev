<?php
require_once("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");
dbconn();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}
stdhead("Preview post");

$body = $HTTP_POST_VARS['body'];

begin_main_frame();

begin_frame("Preview Post", true);
?>
<table width="60%" border="1" cellspacing="0">
<tr>
<td align="center" style='border: 0;'>
<div align="left">
<p><?=format_comment($body)?></p>
</div>
<table class="main" width="300" cellspacing="0" cellpadding="5">
<form method="post" action="?">
<textarea name="body" cols="100" rows="10"><?=$body?></textarea><br>
<div align="left">
<input type="submit" class="btn" value="Preview">
</div>
</form>
</table></table>
</tr></td>
<?php

end_frame();

end_main_frame();

stdfoot();


?>