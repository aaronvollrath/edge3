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
$memberList = "";
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


		// Get the users associated w/ this group.....
		$expSQL = "SELECT userid FROM usergroupmembers WHERE usergroupid=$groupid ORDER BY userid";
		
		//echo "$expSQL";
		fwrite($fd, $expSQL);
		$expResult = $db->Execute($expSQL);
		$memberList = "{\"items\": [";
		$firstmember = 0;
		while($row = $expResult->FetchRow()){
			$expid = $row[0];
			if($expid > 0){
				$image = "./images/user.png";
				# is this user an admin?
				$adminSQL = "SELECT COUNT(*) FROM usergroupadmins WHERE userid = $expid AND usergroupid = $groupid";
				$countresult = $db->Execute($adminSQL);
				$countrow = $countresult->FetchRow();
				if($countrow[0] > 0){
					$image = "./images/admin.png";
				}
				
				$thisExpSQL = "SELECT id, firstname, lastname, organization FROM users WHERE id = $expid";
				$thisexpResult = $db->Execute($thisExpSQL);
				while($thisrow = $thisexpResult->FetchRow()){
					fwrite($fd, $thisExpSQL);
					if($firstmember != 0){
						$memberList .= ",";
					}
					$iduser = $thisrow[0];
					$userName = $thisrow[1]." ".$thisrow[2]." - ".$thisrow[3];
					if(trim($userName)==""){
						$username = "UNKNOWN";
					}
					$firstmember++;
					
					$memberList .= "{\"id\": \"$iduser\", \"name\": \"$userName\", \"img_url\": \"$image\"}";		
				}
			}else{
				$expid = $expid * -1;
				$idSQL = "SELECT usergroupname FROM usergroups WHERE usergroupid = $expid";
				$idResult = $db->Execute($idSQL);
				
				while($row = $idResult->FetchRow()){
					
					$groupName = $row[0];
					if(trim($groupName)==""){
						$groupname = "UNKNOWN";
					}
					if($firstmember != 0){
						$memberList .= ",";
					}
					$firstmember++;
					$memberList .= "{\"id\": \"-$expid\", \"name\": \"$groupName\", \"img_url\": \"./images/group.png\"}";			
				}
				


			}
		}
	$memberList .= "]}";
	fwrite($fd, $memberList);
			fclose($fd);
	echo $memberList;

?>