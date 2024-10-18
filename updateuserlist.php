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
$userList = "";
$filenum = rand(0, 25000);
	$file = "$IMAGESdir/userlist$filenum.json";

	$command = "touch $file";
	$fd = fopen($file, 'w');
	rewind($fd);

if(isset($_GET['userListType'])){
	$userListType = $_GET['userListType'];
}else{
	$userListType = "";
}
$privval = 99;

if($userListType == ""){
		$idSQL = "SELECT id, firstname, lastname,organization FROM users ORDER BY id";
		fwrite($fd, $idSQL);
		$idResult = $db->Execute($idSQL);
		$userList = "{\"items\": [";
		$firstuser = 0;
		while($row = $idResult->FetchRow()){
			$iduser = $row[0];
			$userName = $row[1]." ".$row[2]." - ".$row[3];
			if(trim($userName)==""){
				$username = "UNKNOWN";
			}
			if($firstuser != 0){
				$userList .= ",";
			}
			$firstuser++;
			$userList .= "{\"id\": \"$iduser\", \"name\": \"$userName\", \"img_url\": \"./images/user.png\"}";			
		}
		$userList .= "]}";
		fwrite($fd, $userList);
		fclose($fd);
		echo $userList;
}else{
 		if($userListType == 1){
			$idSQL = "SELECT id, firstname, lastname, organization FROM users ORDER BY id";
			//echo "$expSQL";
			fwrite($fd, $idSQL);
			$idResult = $db->Execute($idSQL);
			$userList = "{\"items\": [";
			$firstuser = 0;
			while($row = $idResult->FetchRow()){
				$iduser = $row[0];
				$userName = $row[1]." ".$row[2]." - ".$row[3];
				if(trim($userName)==""){
					$username = "UNKNOWN";
				}
				if($firstuser != 0){
					$userList .= ",";
				}
				$firstuser++;
				$userList .= "{\"id\": \"$iduser\", \"name\": \"$userName\", \"img_url\": \"./images/user.png\"}";			
			}
			$userList .= "]}";
			fwrite($fd, $userList);
			fclose($fd);
			echo $userList;
		}elseif($userListType== 2){
			// We want to display all available users that are already in a group they administer
			$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			$idResult = $db->Execute($userSQL);
			$idRow = $idResult->FetchRow();
			$id = $idRow[0];
			# what user groups does this user administer?	
			$groupsSQL = "SELECT usergroupid from usergroupadmins WHERE userid = $userid";
			$groupsResult = $db->Execute($groupsSQL);
			$userList = "{\"items\": [";
			$usersarray = array();
			while($row=$groupsResult->FetchRow()){
				$expSQL = "SELECT u.id FROM users as u, usergroupmembers as i WHERE (u.id = i.userid) AND i.usergroupid = $row[0]";
				#echo "<br>$expSQL<br>";
				$expResult = $db->Execute($expSQL);
				while($expinfo = $expResult->FetchRow()){
					$iduser = $expinfo[0];
					array_push($usersarray, $iduser);
				}
			}
			$firstexp = 0;
			$usersarray = array_unique($usersarray);
			foreach($usersarray as $anid){
				$usersql = "SELECT firstname, lastname, organization FROM users WHERE id = $anid";
				
				$userresult = $db->Execute($usersql);
				$userrow= $userresult->FetchRow();
				
				$userName = $userrow[0]." ".$userrow[1]." - ".$userrow[2];
					if($firstexp != 0){
						$userList .= ",";
					}
					$firstexp++;
					$userList .= "{\"id\": \"$anid\", \"name\": \"$userName\", \"img_url\": \"./images/user.png\"}";	
			}
			$userList .= "]}";
			fwrite($fd, $userList);
			fclose($fd);
			echo $userList;
		}else{
			# We are getting the difference between the users table and those users already assigned to a group owned by the user making this query
			# Get all of the users from the user table
			$usersSQL = "SELECT id FROM users ORDER BY id";
			$usersResult = $db->Execute($usersSQL);
			$usersarray = array();
			while($userrow = $usersResult->FetchRow()){
				array_push($usersarray, $userrow[0]);
			}
			$groupsSQL = "SELECT usergroupid from usergroupadmins WHERE userid = $userid";
			$groupsResult = $db->Execute($groupsSQL);	
			# Get all of the users from the user table
			$usersingroupsarray = array();
			while($row=$groupsResult->FetchRow()){
				$expSQL = "SELECT u.id FROM users as u, usergroupmembers as i WHERE (u.id = i.userid) AND i.usergroupid = $row[0]";
				#echo "<br>$expSQL<br>";
				$expResult = $db->Execute($expSQL);
				while($expinfo = $expResult->FetchRow()){
					$iduser = $expinfo[0];
					array_push($usersingroupsarray, $iduser);
				}
			}	
			# obtain the difference....
			$usersarray = array_diff($usersarray, $usersingroupsarray);
			$userList = "{\"items\": [";
			$firstexp = 0;
			foreach($usersarray as $anid){
				$usersql = "SELECT firstname, lastname, organization FROM users WHERE id = $anid";
				$userresult = $db->Execute($usersql);
				$userrow= $userresult->FetchRow();
				$userName = $userrow[0]." ".$userrow[1]." - ".$userrow[2];
				if($firstexp != 0){
					$userList .= ",";
				}
				$firstexp++;
				$userList .= "{\"id\": \"$anid\", \"name\": \"$userName\", \"img_url\": \"./images/user.png\"}";	
			}
			$userList .= "]}";
			fwrite($fd, $userList);
			fclose($fd);
			echo $userList;
		}


}




?>
