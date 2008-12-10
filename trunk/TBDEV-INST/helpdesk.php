<?php
ob_start("ob_gzhandler");
require_once("include/bittorrent.php");
require_once("include/function_help.php");
require_once("include/bbcode_functions.php");
dbconn();
loggedinorreturn();

    if(isset($_POST['action']))
	$action = $_POST['action'];
    else
	$action = "";

    if ($action == "close_sure")
	{
	$ticket = 0 + $_POST['ticket'];
	mysql_query("UPDATE helpdesk SET closed='Yes' WHERE id=$ticket") or sqlerr(__FILE__, __LINE__);
	mysql_query("UPDATE helpdesk SET closed='Yes' WHERE ticket=$ticket") or sqlerr(__FILE__, __LINE__);
	}
	
    if ($action == "close")
	{
	stdhead("Helpdesk");
	page_start(98);
	table_top("Ticket Close","center");
	table_start();
	$ticket = 0 + $_POST['ticket'];
        $count = get_row_count("helpdesk", "WHERE closed='no' AND ticket='0'");
	print "<font size=3 color=lightblue>You have " . $count . " help ticket's to close.";
	print "<br><br>";
	print "<table class=bottom width=30% border=0 cellspacing=0 cellpadding=4>";
	print "<tr>";
	print "<td class=embedded><div align=center>";
	print "<form method=post action=helpdesk.php>";
	print "<input type=hidden name=action value=close_sure>";
	print  "<input type=hidden name=ticket value=".$ticket.">";
	print "<input type=submit class=btn style='height: 20px; width: 100px' value='Yes'>";
	print "</form>";
	print "</td>";
	print "<td class=embedded><div align=center>";
	print "<form method=post action=helpdesk.php>";
	print "<input type=submit class=btn style='height: 20px; width: 100px' value='No'>";
	print "</form>";
	print "</td>";
	print "</tr></table>";

	table_end();
	page_end();
	stdfoot();
	die;
 }

    if ($action == "save_new")
	{
	$message = $_POST['message'];
	if (!$message)
	site_error_message("Error", "You must enter a description of your problem.");
	$user_id = 0 + $_POST['user_id'];
	$message = sqlesc($message);
	$added = sqlesc(get_date_time());
	$res = mysql_query("SELECT * FROM helpdesk WHERE message=$message AND userid=$user_id") or sqlerr(__FILE__, __LINE__);
	$row = mysql_fetch_array($res);
	if ($row)
	site_error_message("Error", "You must enter a description of your problem.");
	mysql_query("INSERT INTO helpdesk (added, userid, message) VALUES ($added, $user_id, $message)");
	}

    if ($action == "save_edit")
	{
	$message = $_POST['message'];
	if (!$message)
	site_error_message("Error", "You must enter a description of your problem.");
	$ticket = 0 + $_POST['ticket'];
	$user_id = 0 + $CURUSER['id'];
	$message = sqlesc($message);
	$added = sqlesc(get_date_time());
	mysql_query("UPDATE helpdesk SET edit_by = $user_id, edit_date = $added, message = $message WHERE id=$ticket") or sqlerr(__FILE__, __LINE__);
	}

    if ($action == "save_anwser")
	{
	$message = $_POST['message'];
	$receiver = 0 + $_POST['receiver'];
	if (!$message)
    site_error_message("Error", "You must enter a description of your problem.");
	$user_id = 0 + $_POST['user_id'];
	$ticket = 0 + $_POST['ticket'];
	$message = sqlesc($message);
	$added = sqlesc(get_date_time());
	$res = mysql_query("SELECT * FROM helpdesk WHERE message=$message AND userid=$user_id AND ticket=$ticket") or sqlerr(__FILE__, __LINE__);
	$row = mysql_fetch_array($res);
	if ($row)
	site_error_message("Error", "You can not have the same answer again send a new request.");
	mysql_query("INSERT INTO helpdesk (ticket, added, userid, message) VALUES ($ticket, $added, $user_id, $message)") or sqlerr(__FILE__, __LINE__);
	$message = "Hello " . get_username($receiver) . ",\n\n";
	$message .=	"A staff member has responded to your help request ticket.\n\n";
	$message .=	"Use the provided link to see the answer.\n\n";
	$message .=	$BASEURL."/helpdesk.php\n\n";
	$message .=	"Regards\n". $SITE_NAME ;
	$message =	sqlesc($message);
	if ($receiver <> $user_id)
	mysql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $receiver, $message, $added)") or sqlerr(__FILE__, __LINE__);
	}

    if ($action == "edit")
	{
	$ticket = $_POST['ticket'];
	
	stdhead("Helpdesk");
	page_start(98);
	table_top("Ticket Edit","center");
	table_start();

	$res = mysql_query("SELECT * FROM helpdesk WHERE id=$ticket") or sqlerr(__FILE__, __LINE__);
	$row = mysql_fetch_array($res);
	if ($row)
		{
		$message = $row['message'];
		print "<font color=lightblue size=2><b>All question's which are already covered in our <a class=altlink_yellow href='faq.php'>Faq</a> Or our <a class=altlink_yellow href='forums.php'>Forum's</a> will be<font color=red><b> Ignored !</b></font></font>";
        print "<hr>";
        print "<font color=lightblue size=2><b>You should provide as much information as possible about your problem.</font>";
        print "<hr>";
		print "<table width=70% border=1 cellspacing=0 cellpadding=5>";
		print "<form method=post action=helpdesk.php>";
		print "<input type=hidden name=action value=save_edit>";
		print  "<input type=hidden name=ticket value=".$ticket.">";
		print "<tr><td bgcolor=#242424><textarea name=message cols=225 rows=15>".htmlspecialchars($message)."</textarea></td></tr>";
		print "<tr><td bgcolor=#242424 align=center><input type=submit class=btn style='height: 25px; width: 120px' value='Submit'></td></tr>";
		print "</table>";
		print "</form>";
		
		}
	table_end();
	page_end();
	stdfoot();
	die;
	}
	
    if ($action == "anwser")
	{
	$ticket = 0 + $_POST['ticket'];
	$receiver = 0 + $_POST['receiver'];
	
	stdhead("Helpdesk");
	page_start(98);
	table_top("Ticket answer","center");
	table_start();
	print "<table width=70% border=1 cellspacing=0 cellpadding=5>";
	
	print "<form name=hd_anwser method=post action=helpdesk.php>";
	print "<input type=hidden name=action value=save_anwser>";
	print  "<input type=hidden name=ticket value=".$ticket.">";
	print  "<input type=hidden name=receiver value=".$receiver.">";
	print  "<input type=hidden name=user_id value=".$CURUSER['id'].">";
	print "<tr><td bgcolor=#242424><textarea name=message cols=225 rows=15></textarea></td></tr>";
	print "<tr><td bgcolor=#242424 align=center><input type=submit class=btn style='height: 25px; width: 120px' value='Submit'></td></tr>";
	print "</table>";
	print "</form>";
?>
<script LANGUAGE="JavaScript" type="text/javascript">
<!--
document.hd_anwser.message.focus();
//-->
</script>
<?
	print "<br>";

	$res = mysql_query("SELECT * FROM helpdesk WHERE id=$ticket") or sqlerr(__FILE__, __LINE__);
	while ($row = mysql_fetch_assoc($res))
		{
		print "<table class=sitetable width=98% border=0 cellspacing=0 cellpadding=4>";
		print "<tr><td class=colheadsite>";
		print "Ticket - " . $row['id'] . " - Summary of answers (old to new from bottom to top)";
		print "</td></tr><tr><td bgcolor=gray>";


	$ticket = $row['id'];
	$res2 = mysql_query("SELECT * FROM helpdesk WHERE closed='no' AND ticket=$ticket ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);
	while ($row2 = mysql_fetch_assoc($res2))
		{
		print "<br>";
		print "<table class=sitetable width=100% border=0 cellspacing=0 cellpadding=4>";
		print "<tr>";
		print "<td class=colheadsite width=150>";
		print "Added By: <a href=userdetails.php?id=".$row2['userid']."><font color=blue>".get_username($row2['userid'])."</font></a>";
		print "</td>";
		print "<td class=colheadsite>";
		print "Date: " . convertdate($row2['added']);
		print "</td>";
		print "</tr><tr>";
		print "<td class=test colspan=2>";
		$message = $row2['message'];
		if ($row2['edit_by'] > 0)
			$message .= "\n\n<font color=red> Last edited by " . get_username($row2['edit_by']) . "  " . convertdate($row2['edit_date']);
		print stripslashes(nl2br($message));
		print "</td></tr></table>";
		}
	
		print "<br><table class=sitetable width=100% border=0 cellspacing=0 cellpadding=4>";
		print "<tr>";
		print "<td class=colheadsite width=150>";
		print "Added by: <a href=userdetails.php?id=".$row['userid']."><font color=blue>".get_username($row['userid'])."</font></a>";
		print "</td>";
		print "<td class=colheadsite>";
		print "Date: " . convertdate($row['added']);
		print "</td>";
		print "</tr><tr>";
		print "<td class=test colspan=2>";
		$message = $row['message'];
		if ($row['edit_by'] > 0)
			$message .= "\n\n<font color=red>Last edited by " . get_username($row['edit_by']) . "  " . convertdate($row['edit_date']);
		print stripslashes(nl2br($message));
		print "</td></tr></table>";
		print "</td></tr></table><br>";
		}

	table_end();
	page_end();
	stdfoot();
	die;
	}

                if ($action == "new")
	{
	stdhead("Helpdesk");
	page_start(98);
	table_top("Ask new question","center");
	table_start();
	print "<font color=lightblue size=2><b>All question's which are already covered in our <a class=altlink_yellow href='faq.php'>Faq</a> Or our <a class=altlink_yellow href='forums.php'>Forum's</a> will be<font color=red><b> Ignored !</b></font></font>";
    print "<hr>";
    print "<font color=lightblue size=2><b>You should provide as much information as possible about your problem.</font>";
    print "<hr>";
	print "<table width=70% border=1 cellspacing=0 cellpadding=5>";
	print "<form name=hd_new method=post action=helpdesk.php>";
	print "<input type=hidden name=action value=save_new>";
	print  "<input type=hidden name=user_id value=".$CURUSER['id'].">";
	print "<tr><td bgcolor=#242424><center><textarea name=message cols=125 rows=15></textarea></center></td></tr>";
	print "<tr><td bgcolor=#242424 align=center><input type=submit class=btn style='height: 25px; width: 120px' value='Send'></td></tr>";
	print "</table>";
	print "</form>";
?>
<script LANGUAGE="JavaScript" type="text/javascript">
<!--
document.hd_new.message.focus();
//-->
</script>
<?
	table_end();
	page_end();
	stdfoot();
	die;
	}

    if ($action == "view")
	{
	$ticket = 0 + $_POST['ticket'];

	stdhead("Helpdesk");
	print "<table width=98% class=bottom border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><center>";
	table_top("Helpdesk","center");
	print "<table background=pic/site/table_background.gif width=100% border=0 cellspacing=0 cellpadding=0>";
	print "<tr>";
	print "<td class=embedded align=center><center><br>";

	if ($CURUSER['class'] > UC_UPLOADER)
		{
		print "<form method=post action=helpdesk.php>";
		print "<input type=hidden name=action value=view_list>";
		print "<input type=submit class=btn style='height: 24px; width: 150px' value='Overview'>";
		print "</form><br>";
		}

	$current_user = 0 + $CURUSER['id'];
	
	$count = get_row_count("helpdesk", "WHERE closed='no' AND userid=$current_user");
	
	if ($CURUSER['support'] == "yes")
		$count = 1;
	
	if ($CURUSER['class'] > UC_UPLOADER)
		$count = 1;
		
	if ($count > 0)
		{
		$res = mysql_query("SELECT * FROM helpdesk WHERE closed='no' AND id=$ticket") or sqlerr(__FILE__, __LINE__);
		while ($row = mysql_fetch_assoc($res))
			{
			print "<table class=sitetable width=98% border=0 cellspacing=0 cellpadding=4>";
			print "<tr><td class=colheadsite>";
			print "Ticket - " . $row['id'] . " - made by: <a href=userdetails.php?id=".$row['userid']."><font color=yellow>".get_username($row['userid'])."</font></a>  ".convertdate($row['added']);
			if ($row['userid'] == $CURUSER['id'])
				{
				print " edit";
				}
			print "</td></tr><tr><td bgcolor=gray>";
		
			print "<table class=sitetable width=100% border=0 cellspacing=0 cellpadding=4>";
			print "<tr>";
			print "<td class=test width=150>";
			print "Sent By: <a href=userdetails.php?id=".$row['userid']."><font color=lime>".get_username($row['userid'])."</font></a>";
			print "</td>";
			if ($row['userid'] == $CURUSER['id'])
				{
				print "<td class=test>";
				print "<form method=post action=helpdesk.php>";
				print "<input type=hidden name=ticket value=".$row['id'].">";
				print "<input type=hidden name=action value=edit>";
				print "Date: " . convertdate($row['added']);
				print "&nbsp;&nbsp;&nbsp;<input type=submit class=btn style='height: 18px; width: 80px' value='edit'>";
				print "</form>";
				print "</td>";
				}
			else
				{
				print "<td class=test>";
				print "Date: " . convertdate($row['added']);
				print "</td>";
				}
			print "</tr><tr>";
			print "<td colspan=2 class=test>";
			$message = $row['message'];
			if ($row['edit_by'] > 0)
				$message .= "\n\n<font color=red>Last edited by " . get_username($row['edit_by']) . "   " . convertdate($row['edit_date']);
			print stripslashes(nl2br($message));
			print "</td></tr></table>";
			
			$ticket = $row['id'];
			$res2 = mysql_query("SELECT * FROM helpdesk WHERE closed='no' AND ticket=$ticket") or sqlerr(__FILE__, __LINE__);
			while ($row2 = mysql_fetch_assoc($res2))
				{
				if ($row2['read_date'] == "0000-00-00 00:00:00")
					$extra_text = "&nbsp;&nbsp;&nbsp;<font color=red>unread</font>";
				else
					$extra_text = "&nbsp;&nbsp;&nbsp;<font color=red>read by " . get_username($row['userid']) . "  " . convertdate($row2['read_date']) . " </font>";
				print "<br>";
				print "<table class=sitetable width=100% border=0 cellspacing=0 cellpadding=4>";
				print "<tr>";
				print "<td class=test width=150>";
				print "Sent by: <a href=userdetails.php?id=".$row2['userid']."><font color=lime>".get_username($row2['userid'])."</font></a>";
				print "</td>";
				if ($row2['userid'] == $CURUSER['id'])
					{
					print "<td class=test>";
					print "<form method=post action=helpdesk.php>";
					print "<input type=hidden name=ticket value=".$row2['id'].">";
					print "<input type=hidden name=action value=edit>";
					print "Date: " . convertdate($row2['added']) . $extra_text;
					print "&nbsp;&nbsp;&nbsp;<input type=submit class=btn style='height: 18px; width: 80px' value='edit'>";
					print "</form>";
					print "</td>";
					}
				else
					{
					print "<td class=test>";
					print "Date: " . convertdate($row2['added']) . $extra_text;
					print "</td>";
					}
				print "</tr><tr>";
				print "<td class=test colspan=2>";
				$message = $row2['message'];
				if ($row2['edit_by'] > 0)
					$message .= "\n\n<font color=red>Last edited by " . get_username($row2['edit_by']) . "  " . convertdate($row2['edit_date']);
				print stripslashes(nl2br($message));
				print "</td></tr></table>";
				}
			
			print "<table class=bottom width=100% border=0 cellspacing=0 cellpadding=4>";
			print "<tr>";
			print "<td class=embedded width=99%>&nbsp;</td><td class=embedded>";
			print "<form method=post action=helpdesk.php>";
			print "<input type=hidden name=action value=anwser>";
			print  "<input type=hidden name=receiver value=".$row['userid'].">";
			print  "<input type=hidden name=ticket value=".$row['id'].">";
			print "<input type=submit class=btn style='height: 20px; width: 100px' value='Answer'>";
			print "</form>";
			print "</td>";
	
			if (get_user_class() >= UC_MODERATOR)
				{
				print "<td class=embedded>";
				print "<form method=post action=helpdesk.php>";
				print "<input type=hidden name=action value=close>";
				print  "<input type=hidden name=ticket value=".$row['id'].">";
				print "<input type=submit class=btn style='height: 20px; width: 100px' value='Close'>";
				print "</form>";
				print "</td>";
				}
			
			print "</tr></table>";
			print "</td></tr></table><br>";
			}
		}
	
		print "<form method=post action=helpdesk.php>";
		print "<input type=hidden name=action value=new>";
		print "<input type=submit class=btn style='height: 24px; width: 150px' value='Ask a new question'>";
		print "</form>";
	
		print "<br>";
	
	print "</td></tr></table>";
	print "</td></table><br>";
	
	stdfoot();
	die;
	}

                if ($CURUSER['support'] == "yes" || $CURUSER['class'] > UC_UPLOADER)
	$action = "view_list";

                if ($action == "view_list")
	{
	stdhead("Helpdesk");
	print "<table width=98% class=bottom border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><center>";
	table_top("Ticket overview","center");
	print "<table background=pic/site/table_background.gif width=100% border=0 cellspacing=0 cellpadding=0>";
	print "<tr>";
	print "<td class=embedded align=center><center><br>";
	$current_user = $CURUSER['id'];
	
	if ($CURUSER['support'] == "yes")
		$count = 1;
	
	if ($CURUSER['class'] > UC_UPLOADER)
		$count = 1;
		
	if ($count > 0)
		{
		print "<font size=3 color=yellow><b>Old to new is from bottom to top..</b></font><br><br>";
		print "<table class=sitetable width=98% border=0 cellspacing=0 cellpadding=4>";
		print "<tr>";
		print "<td class=colheadsite width=30 align=center>##</td>";
		print "<td class=colheadsite width=80>By</td>";
		print "<td class=colheadsite width=180>Date</td>";
		print "<td class=colheadsite>Question</td>";
		print "<td class=colheadsite width=50>Answers</td>";
		print "<td class=colheadsite width=50>Open</td>";
		print "</tr>";
		$res = mysql_query("SELECT * FROM helpdesk WHERE closed='no' AND ticket='0' ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);
		while ($row = mysql_fetch_assoc($res))
			{
			$reacties = get_row_count("helpdesk", "WHERE closed='no' AND ticket=".$row['id']);
			$reacties_read = get_row_count("helpdesk", "WHERE read_date<>'0000-00-00 00:00:00' AND closed='no' AND ticket=".$row['id']);
			print "<tr>";
			print "<td class=test width=30 align=center>";
			print $row['id'];
			print "</td>";
			print "<td class=test width=80>";
			print get_username($row['userid']);
			print "</td>";
			print "<td class=test width=180>";
			print convertdate($row['added']);
			print "</td>";
			print "<td class=test>";

//print("<a href=\"javascript: klappe('a".$row['id']."')\"><img border=\"0\" src=\"pic/java/plus.gif\" id=\"pica".$row['id']."\" alt=\"Show/Hide\"></a>&nbsp;&nbsp;&nbsp;");
// .stripslashes(substr($row['message'],0,100))
//print("<div id=\"ka".$row['id']."\" style=\"display: block;\"> 22".stripslashes($row['message'])." </div><br> ");
//print("<div id=\"ka".$row['id']."\" style=\"display: block;\"> 33".stripslashes($row['message'])." </div><br> ");

			print htmlspecialchars(stripslashes(substr($row['message'],0,400))) . " -=>";
			print "</td>";
			print "<td class=test align=center>";
			print $reacties . "&nbsp;&nbsp;<font color=red>(" . $reacties_read . ")</font>";
			print "</td>";
			print "<td class=test>";

			print "<form method=post action=helpdesk.php>";
			print "<input type=hidden name=action value=view>";
			print  "<input type=hidden name=ticket value=".$row['id'].">";
			print "<input type=submit class=btn style='height: 20px; width: 50px;background: blue;color: yellow' value='Open'>";
			print "</form>";

			print "</td></tr>";
			}
		print "</table>";
		}

	print "<br></td></tr></table>";
	print "</td></table><br>";
	
	stdfoot();
	die;
	}

stdhead("Helpdesk");
print "<table width=98% class=bottom border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><center>";
table_top("Helpdesk","center");
print "<table background=pic/site/table_background.gif width=100% border=0 cellspacing=0 cellpadding=0>";
print "<tr>";
print "<td class=embedded align=center><center><br>";
$current_user = 0 + $CURUSER['id'];

$res = mysql_query("SELECT id FROM helpdesk WHERE read_date='0000-00-00 00:00:00' AND ticket='0' AND closed='no' AND userid=$current_user") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
if ($row)
	{
	$ticket = $row['id'];
	$read_date = sqlesc(get_date_time());
	mysql_query("UPDATE helpdesk SET read_date = $read_date WHERE read_date='0000-00-00 00:00:00' AND ticket=$ticket AND closed='no'") or sqlerr(__FILE__, __LINE__);
	}

$count = get_row_count("helpdesk", "WHERE ticket='0' AND closed='no'");

if ($CURUSER['support'] == "yes")
	$count = 1;

if ($CURUSER['class'] > UC_ADMINISTRATOR)
	$count = 1;
	if ($count > 0)
	{
                if ($CURUSER['support'] == "yes" || $CURUSER['class'] > UC_ADMINISTRATOR)
		                if ($CURUSER['class'] > UC_UPLOADER)
			{
			print "<form method=post action=helpdesk.php>";
			print "<input type=hidden name=action value=view_list>";
			print "<input type=submit class=btn style='height: 24px; width: 150px' value='Overview'>";
			print "</form><br>";
			}
	                               $res = mysql_query("SELECT * FROM helpdesk WHERE closed='no' AND ticket='0'") or sqlerr(__FILE__, __LINE__);
	                               while ($row = mysql_fetch_assoc($res))
		                {
		                if ($CURUSER['id'] == $row['userid'] || $CURUSER['support'] == "yes" || $CURUSER['class'] > UC_UPLOADER)
			{
			print "<table class=sitetable width=98% border=0 cellspacing=0 cellpadding=4>";
			print "<tr><td class=colheadsite>";
			print  "Ticket - " . $row['id'] . " - Posted by: <a href=userdetails.php?id=".$row['userid']."><font color=yellow>".get_username($row['userid'])."</font></a>  ".convertdate($row['added']);
			print "</td></tr><tr><td bgcolor=gray>";
		
			print "<table class=sitetable width=100% border=0 cellspacing=0 cellpadding=4>";
			print "<tr>";
			print "<td class=colheadsite width=250>";
			print "Sent by: <a href=userdetails.php?id=".$row['userid']."><font color=lime>".get_username($row['userid'])."</font></a>";
			print "</td>";
			if ($row['userid'] == $CURUSER['id'])
				{
				print "<td class=colheadsite>";
				print "<form method=post action=helpdesk.php>";
				print "<input type=hidden name=ticket value=".$row['id'].">";
				print "<input type=hidden name=action value=edit>";
				print "date: " . convertdate($row['added']);
				print "&nbsp;&nbsp;&nbsp;<input type=submit class=btn style='height: 18px; width: 80px' value='Edit'>";
				print "</form>";
				print "</td>";
				}
			else
				{
				print "<td class=colheadsite>";
				print "Date: " . convertdate($row['added']);
				print "</td>";
				}
			print "</tr><tr>";
			print "<td colspan=2 bgcolor=#242424>";
			$message = $row['message'];
			if ($row['edit_by'] > 0)
				$message .= "\n\n<font color=red>Last edited by " . get_username($row['edit_by']) . "  " . convertdate($row['edit_date']);
			print stripslashes(nl2br($message));
			print "</td></tr></table>";
			
			$ticket = $row['id'];
			$res2 = mysql_query("SELECT * FROM helpdesk WHERE closed='no' AND ticket=$ticket") or sqlerr(__FILE__, __LINE__);
			while ($row2 = mysql_fetch_assoc($res2))
				{
				if ($row2['read_date'] == "0000-00-00 00:00:00")
					$extra_text = "&nbsp;&nbsp;&nbsp;<font color=red>Unread</font>";
				else
					$extra_text = "&nbsp;&nbsp;&nbsp;<font color=red>viewed by " . get_username($row['userid']) . "  " . convertdate($row2['read_date']) . " </font>";
				print "<br>";
				print "<table class=sitetable width=100% border=0 cellspacing=0 cellpadding=4>";
				print "<tr>";
				print "<td class=colheadsite width=250>";
				print "Sent by: <a href=userdetails.php?id=".$row2['userid']."><font color=blue>".get_username($row2['userid'])."</font></a>";
				print "</td>";
				if ($row2['userid'] == $CURUSER['id'])
					{
					print "<td class=colheadsite>";
					print "<form method=post action=helpdesk.php>";
					print "<input type=hidden name=ticket value=".$row2['id'].">";
					print "<input type=hidden name=action value=edit>";
					print "Date: " . convertdate($row2['added']);
					print "&nbsp;&nbsp;&nbsp;<input type=submit class=btn style='height: 18px; width: 80px' value='Edit'>" . $extra_text;
					print "</form>";
					print "</td>";
					}
				else
					{
					print "<td class=colheadsite>";
					print "Date: " . convertdate($row2['added']) . $extra_text;
					print "</td>";
					}
				print "</tr><tr>";
				print "<td class=test colspan=2>";
				$message = $row2['message'];
				if ($row2['edit_by'] > 0)
					$message .= "\n\n<font color=red>Last edited by " . get_username($row2['edit_by']) . "  " . convertdate($row2['edit_date']);
				print stripslashes(($message));
				print "</td></tr></table>";
				}
			 if (get_user_class() >= UC_MODERATOR)
				{
			print "<table class=bottom width=100% border=0 cellspacing=0 cellpadding=4>";
			print "<tr>";
			print "<td class=embedded width=99%>&nbsp;</td><td class=embedded>";
			print "<form method=post action=helpdesk.php>";
			print "<input type=hidden name=action value=anwser>";
			print  "<input type=hidden name=receiver value=".$row['userid'].">";
			print  "<input type=hidden name=ticket value=".$row['id'].">";
			print "<input type=submit class=btn style='height: 20px; width: 100px' value='Answer'>";
			print "</form>";
			print "</td>";
	            }
			if (get_user_class() >= UC_SYSOP)
				{
				print "<td class=embedded>";
				print "<form method=post action=helpdesk.php>";
				print "<input type=hidden name=action value=close>";
				print  "<input type=hidden name=ticket value=".$row['id'].">";
				print "<input type=submit class=btn style='height: 20px; width: 100px' value='Close'>";
				print "</form>";
				print "</td>";
				}
			
			print "</tr></table>";
			print "</td></tr></table><br>";
			}
		}
	}

	print "<form method=post action=helpdesk.php>";
	print "<input type=hidden name=action value=new>";
	print "<input type=submit class=btn style='height: 30px; width: 200px' value='Ask a new question'>";
	print "</form>";
	print "<br>";

print "</td></tr></table>";
print "</td></table><br>";

stdfoot();

?>
