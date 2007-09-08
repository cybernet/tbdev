<?php
	if (!preg_match(':^/'.(ENA_PASSKEY?'([a-fA-F0-9]{32})/':'') .'(announce|scrape)$:', $_SERVER["PATH_INFO"], $matches))
	{
    header('HTTP/1.0 404 Not found');
    print('<h1>Not Found</h1>\n');
    exit();
	}
	
	if(ENA_PASSKEY)
			$_GET['passkey']=$matches[1];
	$epk=ENA_PASSKEY?2:1;
	include "$matches[$epk].php";
?>