<?php
/*  NOTE: if you want to update the types of organisms so they're displayed on the icons, utilize this file:
	./phpinc/createdraganddroplist.inc

	also, you'll need to create the appropriate icons
*/
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
//	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}
require("globalfilelocations.inc");
#require './phpinc/edge3_db_connect.inc';
//require("fileupload-class.php");
include 'edge_update_user_activity.inc';
// What experiments are associated w/ this user?
$expList = "";
$filenum = rand(0, 25000);
	$file = "$IMAGESdir/experimentlist$filenum.json";

	$command = "touch $file";
	$fd = fopen($file, 'w');
	rewind($fd);

if(isset($_GET['groupid'])){
	$groupid = $_GET['groupid'];
}else{
	$groupid = "";
}
$privval = 99;

		
		$expSQL = "SELECT expid FROM expusergroupassoc WHERE usergroupid=$groupid ORDER BY expid";
	
		//echo "$expSQL";
		fwrite($fd, $expSQL);
		$expResult = $db->Execute($expSQL);
		$expList = "{\"items\": [";
		$firstarray = 0;
		while($row = $expResult->FetchRow()){
			$expid = $row[0];
			
			
			$thisExpSQL = "SELECT expid, expname FROM agilent_experimentsdesc WHERE expid=$expid";
			$thisexpResult = $db->Execute($thisExpSQL);
			while($thisrow = $thisexpResult->FetchRow()){
				fwrite($fd, $thisExpSQL);
				if($firstarray != 0){
					$expList .= ",";
				}
				$firstarray++;
				$expid = $thisrow[0];
				$expName = $thisrow[1];
				$expName = str_replace('#', "Num", $expName);
				$exp = 1;  # Need to set a value for the following required code

				require('./phpinc/createdraganddroplist.inc');
			}
		}
	$expList .= "]}";
	fwrite($fd, $expList);
			fclose($fd);
	echo $expList;

?>