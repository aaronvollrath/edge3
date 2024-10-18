<?php

#updategroupmemberschecklist.php

# the checkOwner javascript function in in expbuilderform.js


require 'edge_db_connect2.php';

require("globalfilelocations.inc");

include 'edge_update_user_activity.inc';
include 'utilityfunctions.inc';
$checkboxes = "";
if(isset($_GET['groupid'])){
	$groupid = $_GET['groupid'];
	# Get the owner of the group
	$ownerSQL = "SELECT userid FROM usergroups WHERE usergroupid = $groupid";
	$ownerResult = $db->Execute($ownerSQL);	
	checkSQLError($ownerResult, $ownerSQL);
	$ownerRow = $ownerResult->FetchRow();
	$owner = $ownerRow[0];


	# Get the userid of the querier
	$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
	$idResult = $db->Execute($userSQL);
	checkSQLError($idResult, $userSQL);
	$idRow = $idResult->FetchRow();
	$userid = $idRow[0];


	$countSQL = "SELECT COUNT(*) FROM usergroupmembers WHERE usergroupid = $groupid";
	$countResult = $db->Execute($countSQL);
	checkSQLError($countResult, $countSQL);
	$countRow = $countResult->FetchRow();
	$count = $countRow[0];
	# need to remove the owner from this count
	$count = $count - 1;
	
	$groupMemberSQL = "SELECT userid FROM usergroupmembers WHERE usergroupid = $groupid ORDER BY userid ASC";
	$result = $db->Execute($groupMemberSQL);
	checkSQLError($result, $groupMemberSQL);
	$checkboxcount = 0;

	# we need to get a count of the number of admins in this group.  if it is only one it will be the current user and (s)he can't uncheck himself w/o checking another person as an admin.
	$adminCountSQL = "SELECT COUNT(*) FROM usergroupadmins WHERE usergroupid = $groupid";
	$adminCountResult = $db->Execute($adminCountSQL);
	checkSQLError($adminCountResult, $adminCountSQL);
	$adminCountRow=$adminCountResult->FetchRow();
	$adminCount = $adminCountRow[0];
	
	

	while($row = $result->FetchRow()){
		$anid = $row[0];
		$userSQL = "SELECT firstname, lastname, organization FROM users WHERE id = $anid";
		$userResult = $db->Execute($userSQL);
		checkSQLError($userResult, $userSQL);
		$userRow = $userResult->FetchRow();
		$userName = $userRow[0]." ".$userRow[1]." - ".$userRow[2];
		# is this user the user making this query?  if so, they are an admin and can only remove themselves if there is another admin assigned to this group already...
		if($anid == $userid && $adminCount == 1){
			// is this user an admin?
			$adminCountSQL = "SELECT COUNT(*) FROM usergroupadmins WHERE userid = $anid AND usergroupid = $groupid";
			$adminCountResult = $db->Execute($adminCountSQL);
			checkSQLError($adminCountResult, $adminCountSQL);
			$adminCountRow=$adminCountResult->FetchRow();
			$adminCount = $adminCountRow[0];
			if($anid == $owner){
				$thisid="ownerid";
				$thisname = "ownerid";
				$thiscolor = "green";
			}else{
				$thisid= "member$checkboxcount";
				$thisname="member$checkboxcount";
				$thiscolor = "blue";
				$checkboxcount++;
			}
			
			
				$checkboxes .= "<input type='checkbox' dojoType='dijit.form.CheckBox' name='$thisname' value='$anid' id='$thisid' disabled checked onclick='return checkOwner($checkboxcount,$count)'><font color='$thiscolor'><b>$userName</b></font><br>";
			
		}else{
			# is this user an admin?
			$adminCountSQL = "SELECT COUNT(*) FROM usergroupadmins WHERE userid = $anid AND usergroupid = $groupid";
			$adminCountResult = $db->Execute($adminCountSQL);
			checkSQLError($adminCountResult, $adminCountSQL);
			$adminCountRow=$adminCountResult->FetchRow();
			$adminCount = $adminCountRow[0];
			$checked ="";
			if($adminCount > 0){
				if($anid == $owner){
					$thisid="ownerid";
					$thisname = "ownerid";
					$thiscolor = "green";
					$checked ="checked";
				}elseif($anid == $userid && $anid != $owner){
					$thiscolor ="blue";
					$thisid= "member$checkboxcount";
					$thisname="member$checkboxcount";
					$checkboxcount++;
					$checked ="checked";
				}else{
					$thisid= "member$checkboxcount";
					$thisname="member$checkboxcount";
					$thiscolor="red";
					$checkboxcount++;
					$checked ="checked";
				}


			}else{
				if($anid == $owner){
					$thisid="ownerid";
					$thisname = "ownerid";
					$thiscolor = "green";
				}elseif($anid == $userid && $anid != $owner){
					$thiscolor ="blue";
					$thisid= "member$checkboxcount";
					$thisname="member$checkboxcount";
					$checkboxcount++;
				}else{
					$thisid= "member$checkboxcount";
					$thisname="member$checkboxcount";
					$checkboxcount++;
					$thiscolor="black";
				}
			}
			$checkboxes .= "<input type='checkbox' dojoType='dijit.form.CheckBox' name='$thisname' value='$anid' id='$thisid' $checked onclick='return checkOwner($checkboxcount,$count)'><font color='$thiscolor'><b>$userName</b></font<br>";
			/*
		
			if($adminCount > 0 && $anid != $userid){
				$checkboxes .= "<input type='checkbox' dojoType='dijit.form.CheckBox' name='$thisname' value='$anid' id='$thisid'  checked onclick='return checkOwner($checkboxcount,$count)'><font color='$thiscolor'><b>$userName</b></font<br>";
			}elseif($adminCount > 0 && $anid == $userid){
				$checkboxes .= "<input type='checkbox' dojoType='dijit.form.CheckBox' name='$thisname' value='$anid'  id='$thisid' checked onclick='return checkOwner($checkboxcount,$count)'><font color='blue'><b>$userName</b></font<br>";
			}
			else{
				$checkboxes .= "<input type='checkbox' dojoType='dijit.form.CheckBox' name='$thisname' value='$anid'  id='$thisid' onclick='return checkOwner($checkboxcount,$count)'>$userName<br>";
			}*/
			
		}
		
	}
	$checkboxes .= "<input type='hidden' name='numMembers' value='$count'>";
	$checkboxes .= "<input type='hidden' name='owner' value='$owner'>";
	$checkboxes .= "<input type='hidden' id='thiseditor' name='thiseditor' value='$userid'>";
	echo $checkboxes;


}else{
	die("This script cannot be called w/o a group id.");
}



?>