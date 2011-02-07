<?php

// -------- Action: Quote
        $topicid = (int)$_GET["topicid"];
    if (!is_valid_id($topicid))
        stderr('Error', 'Invalid ID!');

    $HTMLOUT .= begin_main_frame();
    if ($TBDEV['forums_online'] == 0)
    $HTMLOUT .= stdmsg('Warning', 'Forums are currently in maintainance mode');
    $HTMLOUT .= insert_compose_frame($topicid, false, true);
    $HTMLOUT .= end_main_frame();
    print stdhead("Post quote") . $HTMLOUT . stdfoot();
    exit();


?>