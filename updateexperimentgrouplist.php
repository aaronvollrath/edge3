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
$groupList = "";
if(isset($_GET['expid'])){
	$expid = $_GET['expid'];
}else{
	$expid = "";
}


		// Get the users associated w/ this group.....
		$expSQL = "SELECT usergroupid FROM expusergroup WHERE expid=$expid ORDER BY usergroupid";
		$expResult = $db->Execute($expSQL);
		$memberList = "{\"items\": [";
		$firstmember = 0;
		while($row = $expResult->FetchRow()){
				$groupid = $row[0];
				$image = "./images/group.png";
				$thisExpSQL = "SELECT usergroupname FROM usergroups WHERE usergroupid = $groupid";
				$thisexpResult = $db->Execute($thisExpSQL);
				while($thisrow = $thisexpResult->FetchRow()){
					$groupName = $thisrow[0];
					if($firstmember != 0){
						$memberList .= ",";
					}
					$firstmember++;
					
					$memberList .= "{\"id\": \"$groupid\", \"name\": \"$groupName\", \"img_url\": \"$image\"}";		
				}
			
		}
	$memberList .= "]}";
	echo $memberList;

?>