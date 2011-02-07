<?php


        $ovfid = (isset($_GET["forid"]) ? (int)$_GET["forid"] : 0);
        if (!is_valid_id($ovfid))
            stderr('Error', 'Invalid ID!');

        $res = mysql_query("SELECT name FROM forum_parents WHERE id = $ovfid") or sqlerr(__FILE__, __LINE__);
        $arr = mysql_fetch_assoc($res) or stderr('Sorry', 'No forums with that ID!');

        mysql_query("UPDATE users SET forum_access = " . time() . " WHERE id = {$CURUSER['id']}") or sqlerr(__FILE__, __LINE__);

  
        if ($TBDEV['forums_online'] == 0)
        $HTMLOUT .= stdmsg('Warning', 'Forums are currently in maintainance mode');
        $HTMLOUT .= begin_main_frame();

     
	$HTMLOUT .="<h1 align='center'><b><a href='".$_SERVER['PHP_SELF']."'>Forums</a></b> -> ". htmlspecialchars($arr["name"])."</h1>

	<table border='1' cellspacing='0' cellpadding='5' width='{$forum_width}'>
		<tr>
        	<td class='colhead' align='left'>Forums</td>
            <td class='colhead' align='right'>Topics</td>
		<td class='colhead' align='right'>Posts</td>
		<td class='colhead' align='left'>Last post</td>
	</tr>";


        $HTMLOUT .= show_forums($ovfid);

        $HTMLOUT .= end_table();

        $HTMLOUT .= end_main_frame();
        print stdhead("Forums") . $HTMLOUT . stdfoot();
        exit();
    

    
?>