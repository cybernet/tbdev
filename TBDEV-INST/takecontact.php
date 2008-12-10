<?php
require_once("include/bittorrent.php");
require_once("include/user_functions.php");
require_once("include/bbcode_functions.php");


if ($HTTP_SERVER_VARS["REQUEST_METHOD"] != "POST")
 stderr("Error", "Method");

   dbconn();

loggedinorreturn();

       $msg = trim($_POST["msg"]);
       $subject = trim($_POST["subject"]);

       if (!$msg)
    stderr("Error","Please enter something!");

       if (!$subject)
    stderr("Error","You need to define subject!");

     $added = "'" . get_date_time() . "'";
     $userid = $CURUSER['id'];
     $message = sqlesc($msg);
     $subject = sqlesc($subject);

 mysql_query("INSERT INTO staffmessages (sender, added, msg, subject) VALUES($userid, $added, $message, $subject)") or sqlerr(__FILE__, __LINE__);

       if ($_POST["returnto"])
 {
   header("Location: " . $_POST["returnto"]);
   die;
 }

  stdhead();
  stdmsg("Succeeded", "Message was succesfully sent!");
       
       stdfoot();
       exit;
?>