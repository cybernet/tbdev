<?php
function site_error_message($heading, $text, $color="red")
    {
    stdhead();
    print "<table class=main border=0 width=60% cellpadding=0 cellspacing=0><tr><td class=embedded>";
    table_top ($heading);
    print "<table background=pic/site/table_background.jpg width=100% border=0 cellspacing=0 cellpadding=10><tr><td class=embedded align=center><br>";
    print "<center><font size=2 color=$color><b>" . $text . "</b></font></center><br>";
    print "</td></tr></table>";
    print "</td></tr></table><br>";
    stdfoot();
    die;
    }

function page_end()
    {
    print "</td></tr></table><br>\n";
    }

function table_end()
    {
    print "</td></tr></table><br>";
    print "</td></tr></table>";
    }

function table_start($width=98, $cellpadding=0,$table_width=100,$paddingmain=10)
    {
    print("<table align=center background=pic/site/table_background.jpg width=".$table_width."% border=0 cellspacing=0 ,$paddingmain><tr><td class=embedded><br>");
    print("<table align=center class=bottom border=1 width=$width% cellspacing=0 cellpadding=$cellpadding><tr><td class=embedded><div align=center>");
    }

function page_start($width=90)
    {
    print "<table class=bottom width=$width% border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>\n";
    }

function convertdate($date,$time="") { // Date function by XiniX on 14 april 2005 moddified by Bigjoos 08
    if (substr($date,5,1) == "0")
        $month = substr($date,6,1);
    else
        $month = substr($date,5,2);
    if (substr($date,8,1) == "0")
        $day = substr($date,9,1);
    else
        $day = substr($date,8,2);
    $year = substr($date,0,4);
    if (!$time == "no")
        $time = "  At " . substr($date,11,8);
    else
        $time = "";
    if ($month == "1") return $day . " january " . $year . $time;
    if ($month == "2") return $day . " february " . $year . $time;
    if ($month == "3") return $day . " march " . $year . $time;
    if ($month == "4") return $day . " april " . $year . $time;
    if ($month == "5") return $day . " may " . $year . $time;
    if ($month == "6") return $day . " june " . $year . $time;
    if ($month == "7") return $day . " july " . $year . $time;
    if ($month == "8") return $day . " august " . $year . $time;
    if ($month == "9") return $day . " september " . $year . $time;
    if ($month == "10") return $day . " october " . $year . $time;
    if ($month == "11") return $day . " november " . $year . $time;
    if ($month == "12") return $day . " december " . $year . $time;
}

function get_username($id)
    {
    $res = mysql_query("SELECT username FROM users WHERE id = $id");
    $row = mysql_fetch_array($res);
    if ($row)
        return $row['username'];
    }

function table_top($msg, $align="",$width=100)
    {
    print "<table align=center class=site border=0 width=".$width."% cellpadding=0 cellspacing=0>";
    print "<tr><td class=embedded>";
    print "<table width=100% class=bottom cellspacing=0 cellpadding=0><tr>";
    if ($align == "")
        print "<td class=top_site><font size=3 color=white>&nbsp;&nbsp;$msg</td>";
    else
        print "<td class=top_site><font size=3 color=white><div align=" . $align . ">&nbsp;&nbsp;$msg&nbsp;&nbsp;</td>";
    print "</td></tr></table>";
    print "</td></tr></table>";
    }
?>