<?php
require_once("include/bittorrent.php");
stdhead("Reveal Happy Hour");
begin_frame();
$file = "$CACHE/happyhour.txt";
$happy = unserialize(file_get_contents($file));
print("<pre>");print_r($happy);print ("</pre>");
end_frame();
?>