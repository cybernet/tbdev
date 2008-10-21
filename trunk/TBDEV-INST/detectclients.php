<?php
    require ("include/bittorrent.php");
    require_once ("include/user_functions.php");
    require_once ("include/bbcode_functions.php");
    dbconn(false);
    if(!logged_in())
    {
    header("HTTP/1.0 404 Not Found");
    // moddifed logginorreturn by retro//Remember to change the following line to match your server
    print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
    die();
    }
    if (get_user_class() < UC_MODERATOR)
        stderr("Error", "Permission denied.");
    $res2 = mysql_query("SELECT agent,peer_id FROM peers  GROUP BY " . unsafechar(agent) . " ") or sqlerr();
    stdhead("All Clients");
    print("<table align=center border=3 cellspacing=0 cellpadding=5>\n");
    print("<tr><td class=colhead>Client</td><td class=colhead>Peer ID</td></tr>\n");
    while($arr2 = mysql_fetch_assoc($res2))
    {
        print("</a></td><td align=left>" . safechar($arr2[agent]) . "</td><td align=left>$arr2[peer_id]</td></tr>\n");
    }
    print("</table>\n");
    stdfoot();
?>
