<?
define ('ConfigFN','include/config.php');
define ('TBVERSION','XTBDev 0.10 Beta');

require_once('include/global.php');

function stdhead($title = "") {
    header("Content-Type: text/html; charset=iso-8859-1");
    //header("Pragma: No-cache");
    if ($title == "")
        $title = $SITENAME .(isset($_GET['tbv'])?" (".TBVERSION.")":'');
    else
        $title = $SITENAME .(isset($_GET['tbv'])?" (".TBVERSION.")":''). " :: " . htmlspecialchars($title);
    $ss_uri = 'default.css';
?>
<html><head>
<title><?= $title ?></title>
<link rel="stylesheet" href="/<?=$ss_uri?>" type="text/css">
</head>
<body>

<table width=100% cellspacing=0 cellpadding=0 style='background: transparent'>
<tr>
<td class=clear width=49%>
<!--
<table border=0 cellspacing=0 cellpadding=0 style='background: transparent'>
<tr>

<td class=clear>
<img src=/pic/star20.gif style='margin-right: 10px'>
</td>
<td class=clear>
</td>
</tr>
</table>
-->

</td>
<td class=clear>
<div align=center>
<img src=/pic/logo.gif align=center>
</div>
</td>
<td class=clear width=49% align=right>
</td>
</tr></table>
<?php

$w = "width=100%";

?>
<table class=mainouter <?=$w; ?> border="1" cellspacing="0" cellpadding="10">

<!------------- MENU ------------------------------------------------------------------------>

<? $fn = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1); ?>
<tr><td class=outer align=center>
<table class=main width=700 cellspacing="0" cellpadding="5" border="0">
<tr>

<td align="center" class="navigation"><a href=/>Home</a></td>
<td align="center" class="navigation"><a href=/browse.php>Browse</a></td>
<td align="center" class="navigation"><a href=/search.php>Search</a></td>
<td align="center" class="navigation"><a href=/upload.php>Upload</a></td>
<td align="center" class="navigation">
<a href=login.php>Login</a> / <a href=/signup.php>Signup</a>
</td>
<td align="center" class="navigation"><a href=/chat.php>Chat</a></td>
<td align="center" class="navigation"><a href=/forums.php>Forums</a></td>
<td align="center" class="navigation"><a href=/misc/dox.php>DOX</a></td>
<td align="center" class="navigation"><a href=/topten.php>Top 10</a></td>
<td align="center" class="navigation"><a href=/log.php>Log</a></td>
<td align="center" class="navigation"><a href=/rules.php>Rules</a></td>
<td align="center" class="navigation"><a href=/faq.php>FAQ</a></td>
<td align="center" class="navigation"><a href=/links.php>Links</a></td>
<td align="center" class="navigation"><a href=/staff.php>Staff</a></td>
</tr>
</table>
</td>
</tr>
<tr><td align=center class=outer style="padding-top: 20px; padding-bottom: 20px">
<?


} // stdhead

function stdfoot() {
  print("</td></tr></table>\n");
  print("<table class=bottom width=100% border=0 cellspacing=0 cellpadding=0><tr valign=top>\n");
  print("<td class=bottom align=left width=49%><img src=/pic/bottom_left.gif></td><td width=49% align=right class=bottom><img src=/pic/bottom_right.gif></td>\n");
  print("</tr></table>\n");
  print("</body></html>\n");
}

function tr($x,$y,$noesc=0) {
    if ($noesc)
        $a = $y;
    else {
        $a = htmlspecialchars($y);
        $a = str_replace("\n", "<br />\n", $a);
    }
    print("<tr><td class=\"heading\" valign=\"top\" align=\"left\">$x</td><td valign=\"top\" align=left>$a</td></tr>\n");
}
function utime()
{
  return (float) preg_replace('/^0?(\S+) (\S+)$/X', '$2$1', microtime());
}
	$pgs=utime();
	stdhead('Admin Options');
// Templates
//	Options Type Templates
//	current format:
//		Key => array(Numeric,DisplayInput,ValidationRule,PPFilter)
//  where:
//    Key: is the Options Menu Type
//    Numeric: dictates if the code shud be enclosed in quotes, or shud be left as is (eval possibly)
//    DisplayInput: is the code for the Input box on the form
//    ValidationRule: is php code that $val is passed thru, must set $ok, 0=failed 1=success
//    PrePFilter: is php code that is applied before the the value is saved to the config
//    PstPFilter: is php code that is applied after retrieving it from the config
//    
//    may contain:
//          $key: FormName from Options Menu
//          $val: Value from either config.php or DefaultValue from Options Menu

	$templates=array(
			'hidden' => array(0,'<input name="$key" type="hidden" id="$key" value="$val" size="83" maxlength="80" readonly>',NULL,NULL,NULL),
			'string' => array(0,'<input name="$key" type="text" id="$key" value="$val" size="83" maxlength="80">',NULL,NULL,NULL),
			'password' => array(0,'<input name="$key" type="text" id="$key" value="$val" size="83" maxlength="80">',NULL,NULL,NULL),
			'path' => array(0,'<input name="$key" type="text" id="$key" value="$val" size="83" maxlength="80">','is_valid_path',NULL,NULL),
			'url' => array(0,'<input name="$key" type="text" id="$key" value="$val" size="83" maxlength="80">','is_valid_url',NULL,NULL),
			'rurl' => array(0,'<input name="$key" type="text" id="$key" value="$val" size="83" maxlength="80">','is_valid_rurl',NULL,NULL),
			'aurl' => array(0,'<textarea name="annurl" cols="80" rows="3" wrap="off" id="annurl">$val</textarea>','is_valid_urls',NULL,NULL),
			'email' => array(0,'<input name="$key" type="text" id="$keyid" value="$val" size="83" maxlength="80">','is_valid_email',NULL,NULL),
			'tf' => array(1,'<input name="$key" type="checkbox" value="true" $checked>','is_tf',NULL,NULL),
			'int' => array(1,'<input name="$key" type="text" id="$key" value="$val" size="43" maxlength="40">','is_numformula',NULL,NULL),
			'bytes' => array(1,'<input name="$key" type="text" id="$key" value="$val" size="43" maxlength="40">','is_numformula',NULL,NULL),
			'sec' => array(1,'<input name="$key" type="text" id="$key" value="$val" size="43" maxlength="40">','is_numformula',NULL,NULL),
			'float' => array(1,'<input name="$key" type="text" id="$key" value="$val" size="11" maxlength="8">','is_floatformula',NULL,NULL),
		);


// Options Menu Array
// A little more complicates
// each entry is either an string or an array
// if it's a string, than it's a Header that contains arrays below it
// the array for menu items is under this format
//  DisplayName, Type, FormName, ConfigName, Description, DefaultValue
//     DisplayName: Display name on the Form
//     Type: The type of input expected, used in validating user/config input
//     FormName: the name that appears on the form, and the variable name used in php (global)
//     ConfigName: the variable name (preceded with $) or constant name (defined) in config.php
//     Description: brief description displayed next/under the input button on the form
//     Default/Value: Default value used if not found in config.php or on POST
	$options=array(
		'Site Info',
			array('TBVersion','hidden','tbv','TBVERSION','TBDevnet Versioning info',TBVERSION),
			array('Site Name','string','sitename','$SITENAME','Name of your torrent tracker','TBDev.Net Tracker'),
			array('Site Url','url','siteurl','$BASEURL','Your site url, used in page links (no ending slash)','http://tracker.tbdev.net'),
			array('Base Url','url','baseurl','$DEFAULTBASEURL','Sites base path, used in emails (no ending slash)','http://tracker.tbdev.net'),
			array('Site Email','email','siteemail','$SITEEMAIL','Email for sender/return path','noreply@tracker.tbdev.net'),
			array('Announce Urls','aurl','annurl','$announce_urls[]','Announce urls','http://tracker.tbdev.net/announce'),
		'Database',
			array('Host','string','dhost','$mysql_host','Database host (domain or ip)','localhost'),
			array('User','string','duser','$mysql_user','Database username','tb'),
			array('Password','password','dpass','$mysql_pass','Database password',''),
			array('Database','string','ddb','$mysql_db','Database name','bittorrent'),
		'Switches',
			array('Site Online','tf','bonline','$SITE_ONLINE','Site Open for business?','true'),
			array('Members Only','tf','bmembers','$MEMBERSONLY','Only registered users may use','true'),
			array('Alternate Announce','tf','baltann','ENA_ALTANNOUNCE','Enable Alternate Announce/scrape urls','true'),
			array('Passkey System','tf','bpasskey','ENA_PASSKEY','Enable Passkey System','true'),
			array('--- &nbsp;Limit Connections','tf','bplc','ENA_PASSKEYLIMITCONNECTIONS','Limit Amount of connections (Required: Passkey System)','false'),
		'Limits',
			array('Users','int','limitusers','$maxusers','Max Users before signups close','75000'),
			array('Peers','int','limitpeers','$PEERLIMIT','Max Peers allowed, not implemented','50000'),
			array('Torrent Size','bytes','limittsize','$max_torrent_size','Max torrent filesize that can be uploaded','10000000'),
			array('Votes','int','limitminvotes','$minvotes','Minimum # of votes for rating display','1'),
			array('Max File Size','bytes','maxfilesize','$maxfilesize','Max filesize that can be uploaded into bitbucket','256 * 1024'),
		'Paths',
			array('Torrents','path','dirtorrents','$torrent_dir','Server path to torrent folder (complete or relative, no ending slash)','torrents'),
			array('BitBucket','rurl','dirbucket','$bitbucket_dir','Relatiive Server/url path to BitBucket folder (no beginning,no ending slash)','bitbucket'),
			array('Images','rurl','urlpics','$pic_base_url','Relative Image url path (with beginning & ending slash)','/pic/'),
		'Timed',
			array('Announce Interval','sec','tannounce','$announce_interval','Time between announces to give to user clients.','60 * 30'),
			array('Autoclean Interval','sec','taclean','$autoclean_interval','How long between autoclean runs.','900'),
			array('Signup Timeout','sec','tsignupto','$singup_timeout','How long to wait before deleting unconfirmed accts.','86400 * 3'),
			array('Dead Torrent Time','sec','tdeadtorrent','$max_dead_torrent_time','How long to wait to make torrents invisible (no seeds/no peers)..','6 * 3600'),
			array('Dead User Time','sec','tdeaduser','$max_dead_user_time','How long to wait before deleting inactive user accounts..','42*86400'),
			array('Dead Topic Time','sec','tdeadtopic','$max_dead_topic_time','How long to wait before deleting inactive user accounts..','7*86400'),
			array('Torrent TTL','sec','ttorrentttl','$torrent_ttl','How long do torrents live for.','28*86400'),
		'Auto Promote Users',
			array('Transfer Limit','bytes','aplimit','$ap_limit','Uploaded amount for promotion','25*1024*1024*1024'),
			array('Minimum Ratio','float','apratio','$ap_ratio','Minimum ratio for promotion','1.05'),
			array('Time Limit','sec','aptime','$ap_time','Offer expires after how long a user joined','28*86400'),
		'Auto Demote Power Users',
			array('Minimum Ratio','float','adratio','$ad_ratio','Minimum ratio required to keep Power User','.95'),
	);

	function is_valid_email($val)
	{
		return preg_match('/^([a-zA-Z0-9_\-\.]+@(?:[a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,4}|\x22.+\x22\s\x3c[a-zA-Z0-9_\-\.]+@(?:[a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,4}\x3e)$/',$val);
	}
	function is_valid_url($val)
	{
		$pp=parse_url($val);
		return (("$pp[scheme]://$pp[host]".(isset($pp["port"]) ? ":$pp[port]":"").(isset($pp["path"]) ? "$pp[path]":"")==$val) ? true:false);
	}
	function is_valid_path($val)
	{
		return is_valid_rurl($val,1);
	}
	function is_valid_rurl($val,$rta=0)
	{
		GLOBAL $submission;
		$pp=parse_url($val);
		if(($ok= ( $pp["path"]==$val ? true:false)) && $submission)
		{
			$val=(($val[0]=='/' && $rta==0) ? substr($val,1):$val);
			if(!is_dir($val))
				mkdir($val,0777);
			else if(!$rta)
				chmod($val,0777);
			$ok=is_dir($val);
		}
		return $ok;
	}
	function is_valid_urls($val)
	{
		if($ok=is_array($val))
		{
			foreach($val as $value)
			{
				if(($ok=is_valid_url($value))===false)
					break;
        }					
		}
		return ($ok);
	}		
	function is_tf($val)
	{
		return (in_array($val,array("true","false",1,0)) ? true:false);
	}
	function is_numformula($val)
	{
		return ((preg_match("/^[0-9\s-\x2b\x28\x29\x2a]+$/",$val)==1) ? true:false);
	}
	function is_floatformula($val)
	{
		return ((preg_match("/^[0-9\s-\x2b\x28\x29\x2a\x2e]+$/",$val)==1) ? true:false);
	}
	function check_aurl($val)
	{
		$arr=array();
		foreach($val as $value)
		{ $value=fixup($value);
			if(!empty($value))
				$arr[]=$value;
		}
		return $arr;
	}
	function fixup($val)
	{
		if($val[0]=='"')
			$val=substr($val,1,strlen($val)-2);
		return stripslashes(trim($val));
	}
		
	function calctime($val)
	{
		$days=intval($val / 86400);
		$val-=$days*86400;
		$hours=intval($val / 3600);
		$val-=$hours*3600;
		$mins=intval($val / 60);
		$secs=$val-($mins*60);
		return "<br>&nbsp;&nbsp;&nbsp;$days days, $hours hrs, $mins minutes, $secs Seconds";
	}
	function calcbytes($val)
	{
		$tb=intval($val / ($ml=1073741824));
		$val-=$tb*$ml;
		$gb=intval($val / ($ml/=1024));
		$val-=$gb*$ml;
		$mb=intval($val / ($ml/=1024));
		$val-=($mb*$ml);
		$kb=intval($val / ($ml/=1024));
		$bytes=$val-($kb*$ml);
		return "<br>&nbsp;&nbsp;&nbsp;$tb TB, $gb GB, $mb MB, $kb KB, $bytes Bytes";
	}
	function addqslashes($val)
	{
		return str_replace('"','\"',$val);
	}
// Setup array of Reference Values for quicker lookups	
	foreach($options as $key => $value)
	{
		if(is_array($value))
		{
			$plkp[$value[2]]=$key;
			$ptyp[$key]=$value[1];
			$pnum[$key]=$templates[$value[1]][0];
			$prep[$key]=$value[2];
			$pvar[$key]=$value[3];
			$pdef[$key]=($value[1]=='aurl' ? explode('\n',$value[5]):$value[5]);
		}
	}
	// If this is a submitted form, fill in our form defaults
	if($_POST['action']=='submit') 
	{
		$submission=true;
  	foreach($_POST as $pkey => $pvalue)
  		if(isset($plkp[$pkey])) 
  		{
  			$key=$plkp[$pkey];
  		  $pdef[$key] = ($ptyp[$key]=='aurl' ? explode("\n",$pvalue) : $pvalue);
  		}
  } else
	
	// Read our config.php file and get valid contents
	// replace form defaults if option exists
	if($fh=fopen(ConfigFN,'r'))
	{
		$config=fread($fh,filesize(ConfigFN));
		fclose($fh);
		$haveconfig=true;
		preg_match_all("/^define\s*\(\s*[\x22\x27](.+)[\x22\x27]\s*,\s*(\d+|.+)\s*\)\s*;$/m",$config,$defines);
  	preg_match_all("/^([$][a-zA-Z0-9\x5f]+)\s*=\s*(\d+|[\x22\x27].+[\x22\x27])\s*;$/m",$config,$vars);
  	unset ($config);
  	$config[0]=array_merge($defines[1],$vars[1]);
  	$config[1]=array_merge($defines[2],$vars[2]);
  	foreach($config[0] as $ck => $val)
  		if(!(($key=array_search($val,$pvar))==FALSE))
  		{
  			if($config[1][$ck][0]!='"')
  				$pdef[$key]=$config[1][$ck];
  			else if($ptyp[$key]!='aurl')
  				$pdef[$key]=substr($config[1][$ck],1,strlen($config[1][$ck])-2);
  			else
  				$pdef[$key][]=substr($config[1][$ck],1,strlen($config[1][$ck])-2);
  		}
  }
  
  // Validate the form entries
  foreach($pdef as $key => $val)
  {
		if(!empty($templates[$ptyp[$key]][2]))
		{
			if($pnum[$key])
				eval("\$val = (". ($ptyp[$key]=='float'?'float':'int') .")($val);");
			else
				$val=($ptyp[$key]=='aurl' ? check_aurl($val):fixup($val));
			// Use the defaults if validation fails
			$pdef[$key]=(!call_user_func($templates[$ptyp[$key]][2],$val) ? ($ptyp[$key]=='aurl' ? explode("\n",$options[$key][5]):$options[$key][5]) : $val);
		}
  }
  // Simple login validation check
  if($haveconfig)
  {
  	$key=$plkp['duser'];
  	$key2=$plkp['dpass'];
  	if(!(empty($pdef[$key])) && !(empty($pdef[$key2])))
  	{
	  	$validlogin=($_POST['luser']==$pdef[$key] && $_POST['lpass']==$pdef[$key2]);
	  	if(!$validlogin) {
				begin_main_frame();
				begin_frame("Admin Control Panel Login");
  			begin_table(1);
  			echo '<form action="" method="post" enctype="application/x-www-form-urlencoded" name="login">';
  			tr($options[$key][4],'<input name="luser" type="text" size="83" maxlength="80">',1);
  			tr($options[$key2][4],'<input name="lpass" type="password" size="83" maxlength="80">',1);
				end_table();
				echo '	<center><input type="submit" name="Submit" value="Submit">	</center>';
				end_frame();
				end_main_frame();
				stdfoot();
				die();
			}
		}
  }
  		  	
	if($submission)
	{
  	if($fh=fopen(ConfigFN,'w'))
  	{
  		$config="<?php\n//\n// Generated by admincp.php on ". gmdate("M d Y H:i:s") ."\n// XTBDevnet\n//\n\n";
  		foreach($options as $okey => $oval)
  			if(is_array($oval))
  			{
  					$val=$pdef[$okey];
  					$config.="// ". $oval[4] ."\n";
						if($pnum[$okey])
							$q='';
						else
							$q='"';
						if($oval[3][0]!='$')
							$add=true;
						else
							$add=false;
						if(!is_array($pdef[$okey]))
							$config.= ($add ? "define ('":''). $oval[3] .($add ? "',":' = '). $q .($pnum[$okey] ? $pdef[$okey]:addqslashes($pdef[$okey])). $q .($add ? ')':'') .";\n";
						else
							foreach($pdef[$okey] as $val)
								$config.=($add ? "define ('":''). $oval[3] .($add ? "',":' = ') . $q. ($pnum[$okey] ? $val:addqslashes($val)) .$q .($add ? ')':'') .";\n";
  			}
  		$config.="?>\n";
  		fwrite($fh,$config);	
  		fclose($fh);
  	}
  }

 // add some extra info to some options
 // final processing for form display
 foreach($pdef as $key => $val)
 {
  	switch($ptyp[$key])
  	{
  		case 'sec':
  			$options[$key][4].=calctime($pdef[$key]);
  			break;
  		case 'bytes':
  			$options[$key][4].=calcbytes($pdef[$key]);
  			break;
  		case 'aurl':
  			$options[$key][4].='<br>&nbsp;<strong>One per line.</strong>';
  			$pdef[$key] = implode("\n",$pdef[$key]);
  			
  	}
  }
	

	
// OMG, Finally the Output Portion of the script
	begin_main_frame();
	begin_frame('Configuration Settings');
?>
	<CENTER><H1><BOLD><?= TBVERSION ?></BOLD></H1></CENTER>
	<form action="" method="post" enctype="application/x-www-form-urlencoded" name="config">
<?
	begin_table(1);
	foreach($options as $value)
	{
		if(is_string($value))
			echo "<tr><td colspan=2 class='title' bgcolor='#6699CC'><CENTER>$value</CENTER></td></tr>";
		else if(is_array($value)) {
			$key=$value[2];
			$val=htmlspecialchars(stripslashes($pdef[$plkp[$key]]));
			if($value[1])
				$checked=$val ? ' checked':'';
			eval('$opt="'. addslashes( $templates[$value[1]][1] ) .($value[1]=='tf'?'':'<br>') .'";');
			if($value[1]!='hidden')	
				tr($value[0],"&nbsp;$opt&nbsp;$value[4]",1);
			else 
				echo $opt;
		}
	}
	end_table();
?>
	<br>
	<center>
	<input name="action" type="hidden" value="submit" readonly>
	<input type="submit" name="Submit" value="Submit">
	&nbsp;&nbsp;&nbsp;
  <input type="reset" name="Reset" value="Reset">
	</center>
	</form>
<?
	end_frame();
	
	end_main_frame();
	$pgt=utime()-$pgs;
	echo "<CENTER>Page Generated in $pgt Seconds</CENTER>";
	stdfoot();
	die();
?>
