<?php
require "include/bittorrent.php";
dbconn();
	
if(isset($_GET['pollId'])){
	
	//require_once("dbConnect.php");
	
	$optionId = false;
	$userID = $CURUSER["id"];
	
	if(isset($_GET['optionId'])){
		$optionId = 0 + $_GET['optionId'];
		$optionId = preg_replace("/[^0-9]/si","",$optionId);
	}
	
	$pollId = 0 + $_GET['pollId'];
	$pollId = preg_replace("/[^0-9]/si","",$pollId);
	
	$res = mysql_query("SELECT `pollerID` from `poller_vote` WHERE `userID` = '$userID' AND `pollerID` = '$pollId';");
	$result = mysql_fetch_assoc($res);
	// Don't let the same user vote twice on the same poll. | ezero
	if($result) {
		echo "You already voted on this poll " . print_r($result);
	} else {

		if($optionId)mysql_query("insert into poller_vote(optionID,ipAddress, userID, pollerID)values('".$optionId."','".getenv("REMOTE_ADDR")."','". $userID ."','" . $pollId . "')") or die(mysql_error());

	}
	// Returning data as xml
	
	echo '<?xml version="1.0" ?>';
	
	$res = mysql_query("select ID,pollerTitle from poller where ID='".$pollId."'");
	if($inf = mysql_fetch_array($res)){
		echo "<pollerTitle>".$inf["pollerTitle"]."</pollerTitle>\n";
		
		$resOptions = mysql_query("select ID,optionText from poller_option where pollerID='".$inf["ID"]."' order by pollerOrder") or die(mysql_error());
		while($infOptions = mysql_fetch_array($resOptions)){
			echo "<option>\n";
			echo "\t<optionText>".$infOptions["optionText"]."</optionText>\n";					
			echo "\t<optionId>".$infOptions["ID"]."</optionId>\n";					
			
			$resVotes = mysql_query("select count(ID) from poller_vote where optionID='".$infOptions["ID"]."'");
			if($infVotes = mysql_fetch_array($resVotes)){
				echo "\t<votes>".$infVotes["count(ID)"]."</votes>\n";							
			}										
			echo "</option>";				
			
		}				
	}
	exit;

}else{
	echo "No success";
	
}

?>