<?php

  function server_load ()
  {
    if (strtolower (substr (PHP_OS, 0, 3)) === 'win')
    {
      return 'Unknown';
    }

    if (@file_exists ('/proc/loadavg'))
    {
      $load = @file_get_contents ('/proc/loadavg');
      $serverload = explode (' ', $load);
      $serverload[0] = round ($serverload[0], 4);
      if (!$serverload)
      {
        $load = @exec ('uptime');
        $load = split ('load averages?: ', $load);
        $serverload = explode (',', $load[1]);
      }
    }
    else
    {
      $load = @exec ('uptime');
      $load = split ('load averages?: ', $load);
      $serverload = explode (',', $load[1]);
    }

    $returnload = trim ($serverload[0]);
    if (!$returnload)
    {
      $returnload = 'Unknown';
    }

    return $returnload;
  }

  $rootpath = './../';
  
define ('DEBUGMODE', true);
require_once 'include/bittorrent.php';
dbconn ();
if(!logged_in())
{
header("HTTP/1.0 404 Not Found");
// moddifed logginorreturn by retro//Remember to change the following line to match your server
print("<html><h1>Not Found</h1><p>The requested URL /{$_SERVER['PHP_SELF']} was not found on this server.</p><hr /><address>Apache/1.1.11 (xxxxx) Server at ".$_SERVER['SERVER_NAME']." Port 80</address></body></html>\n");
die();
}

 
  

  if (function_exists ('memory_get_usage'))
  {
    $memory_usage = ' - Memory Usage: ' . mksize (memory_get_usage ());
  }

  stdhead ('DEBUG MODE');
  $queries = $_SESSION['queries'];
  if ((!empty ($queries) AND is_array ($queries)))
  {
    $str = '
	<table width="80%" align="center" cellspacing="5" cellpadding="5" border="0">
		<tr>
			<td class="colhead" width="10%"  align="center">ID</td>
			<td  class="colhead" width="20%" align="center">Query Time</td>
			<td  class="colhead" width="70%" align="left">Query String</td>
		</tr>';
    $id = 1;
    $querytime = 0;
    foreach ($queries as $q => $v)
    {
      $font = (0.0149999999999999994448885 < $v['query_time'] ? '<font color="darkred">(slow query) ' : '<font color="darkgreen">');
      $str .= '
		<tr>
			<td  align="center">' . $id . '</td>
			<td  align="center">' . $font . safechar ($v['query_time']) . '</font></td>
			<td  align="left">' . strip_tags ($v['query']) . '</td>
		</tr>';
      ++$id;
      $querytime += $v['query_time'];
    }

    $phptime = $_SESSION['totaltime'] - $querytime;
    $percentphp = @number_format ($phptime / $_SESSION['totaltime'] * 100, 2);
    $percentsql = @number_format ($querytime / $_SESSION['totaltime'] * 100, 2);
    $str .= '
		<tr>
			<td align="left" colspan="3">
				<strong>
					 Generated in ' . htmlspecialchars ($_SESSION['totaltime']) . ' seconds (' . $percentphp . '% PHP / ' . $percentsql . '% MySQL)<br>
					 MySQL Queries: ' . ($id - 1) . ' / Global Parsing Time: ' . $querytime . $memory_usage . '<br>
					 PHP version: ' . phpversion () . ' / Server Load: ' . server_load () . ' / GZip Compression: ' . ($gzipcompress == 'yes' ? 'Enabled' : 'Disabled') . '
				</strong>
			 </td>
		 </tr>
	 </table>';
    echo $str;
  }
  else
  {
    echo 'There is no query to show..';
  }

  stdfoot ();
?>