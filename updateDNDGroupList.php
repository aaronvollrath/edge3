<?php
/*  NOTE: if you want to update the types of organisms so they're displayed on the icons, utilize this file:
	./phpinc/createdraganddroplist.inc

	also, you'll need to create the appropriate icons
*/
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}
require("globalfilelocations.inc");
#require './phpinc/edge3_db_connect.inc';
//require("fileupload-class.php");
include 'edge_update_user_activity.inc';
// What experiments are associated w/ this user?
$groupList = "";


if(isset($_GET['groupListType'])){
	$groupListType = $_GET['groupListType'];
}else{
	$groupListType = "";
}

if($groupListType == ""){
		die("An error has occured, please contact the edge administrator!");
}else{
 		if($groupListType == 1){
			$idSQL = "SELECT usergroupid, usergroupname FROM usergroups WHERE userid = $userid ORDER BY usergroupid";
			//echo "$expSQL";
			
			$idResult = $db->Execute($idSQL);
			//checkSQLError($groupsResult, $groupsSQL);
			$groupList = "{\"items\": [";
			$firstgroup = 0;
			while($row = $idResult->FetchRow()){
				$idgroup = $row[0];
				$groupName = $row[1];
				if(trim($groupName)==""){
					$groupname = "UNKNOWN";
				}
				if($firstgroup != 0){
					$groupList .= ",";
				}
				$firstgroup++;
				$groupList .= "{\"id\": \"-$idgroup\", \"name\": \"$groupName\", \"img_url\": \"./images/group.png\"}";			
			}
			$groupList .= "]}";
			
			echo $groupList;
		}elseif($groupListType== 2){
			// We want to display all available groups already assigned to a group they administer
			$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			$idResult = $db->Execute($userSQL);
			$idRow = $idResult->FetchRow();
			$userid = $idRow[0];
			# what user groups does this user administer?	
			$groupsSQL = "SELECT usergroupid from usergroupadmins WHERE userid = $userid";
			$groupsResult = $db->Execute($groupsSQL);
			#echo "$groupsSQL<br>";
			//checkSQLError($groupsResult, $groupsSQL);
			$groupList = "{\"items\": [";
			$firstgroup = 0;
			while($row=$groupsResult->FetchRow()){
				$agroup = $row[0];
				# do any of these groups contain groups?
				$groupingroupSQL = "SELECT userid FROM usergroupmembers WHERE usergroupid = $agroup AND userid < 0";
				#echo "$groupingroupSQL<br>";
				$groupingroupResult = $db->Execute($groupingroupSQL);
				//checkSQLError($groupingroupResult, $groupingroupSQL);
				if($groupingroupResult->RecordCount() > 0){
					while($thisgroupid = $groupingroupResult->FetchRow()){
						$thisgroupid = $thisgroupid[0];
						$posgroupid = $thisgroupid * -1;
						$idSQL = "SELECT usergroupname FROM usergroups WHERE usergroupid = $posgroupid";
						$idResult = $db->Execute($idSQL);
						$grouprow = $idResult->FetchRow();
						$groupName = $grouprow[0];
						if($firstgroup != 0){
							$groupList .= ",";
						}
						$firstgroup++;
						$groupList .= "{\"id\": \"$thisgroupid\", \"name\": \"$groupName\", \"img_url\": \"./images/group.png\"}";	
					}
				}
			}
			
			$groupList .= "]}";
			
			echo $groupList;
		}else{
			// We want to display all available groups already assigned to a group they administer
			$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			$idResult = $db->Execute($userSQL);
			$idRow = $idResult->FetchRow();
			$userid = $idRow[0];
			# what user groups does this user administer?	
			$groupsSQL = "SELECT usergroupid from usergroupadmins WHERE userid = $userid";
			$groupsResult = $db->Execute($groupsSQL);
			#echo "$groupsSQL<br>";
			//checkSQLError($groupsResult, $groupsSQL);
			$groupList = "{\"items\": [";
			$allmygroupsarray = array();
			$onlyingroupsarray = array();
			$firstgroup = 0;
			while($row=$groupsResult->FetchRow()){
				$agroup = $row[0];
				array_push($allmygroupsarray, $agroup);
				# do any of these groups contain groups?
				$groupingroupSQL = "SELECT userid FROM usergroupmembers WHERE usergroupid = $agroup AND userid < 0";
				#echo "$groupingroupSQL<br>";
				$groupingroupResult = $db->Execute($groupingroupSQL);
				//checkSQLError($groupingroupResult, $groupingroupSQL);
				if($groupingroupResult->RecordCount() > 0){
					while($thisgroupid = $groupingroupResult->FetchRow()){
						$thisgroupid = $thisgroupid[0];
						$posgroupid = $thisgroupid * -1;
						array_push($onlyingroupsarray, $posgroupid);
					}
				}
			}

			#  now, take the difference between these arrays to get only the groups not assigned to a group already
			$diff = array_diff($allmygroupsarray,$onlyingroupsarray);
			foreach($diff as $different){
				$idSQL = "SELECT usergroupname FROM usergroups WHERE usergroupid = $different";
						$idResult = $db->Execute($idSQL);
						$grouprow = $idResult->FetchRow();
						$groupName = $grouprow[0];
						if($firstgroup != 0){
							$groupList .= ",";
						}
						$firstgroup++;
						$groupList .= "{\"id\": \"-$different\", \"name\": \"$groupName\", \"img_url\": \"./images/group.png\"}";

			}
			$groupList .= "]}";
			echo $groupList;
			
		}


}




?>
