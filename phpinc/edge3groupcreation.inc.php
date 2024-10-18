<?php

#require 'globalfilelocations.inc';
#include '../utilityfunctions.inc'; # general utility functions file used throughout
$userid = 0;
if(isset($_SESSION['userid'])){
	$userid = $_SESSION['userid'];
}else{
	die("You need to log in to utilize this function of EDGE<sup>3</sup>");
}
if($logged_in == 0){
	die("You need to log in to utilize this function of EDGE<sup>3</sup>");
}

if(!isset($_POST['submit'])){

# form onsubmit function checkNewGroup() is in expbuilderform.js
?>



<form name="creategroup" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkNewGroup()">
	<table class="question" width="400">
	<thead>
	<tr>
	<!-- <th class="mainheader" colspan="2">Edit Chemical Attribute</th> -->
	<th class="mainheader" colspan="3"><font color="black"><b>Create a User Group</b></font></th>
	</tr>
	</thead>
	<tr><td colspan="3" class="questionparameter"><font color="black"><strong>Instructions: </strong> This form is used to create a User Group.</td></tr>
	
	<tr><TD><font color="black"><b>New User Group Name: </b></font></TD><td><input name="groupname" type="text" align="right" id="newgroupname" size="50"></input></td><td></td></tr>
	<tr>
	<td><input type="submit" name="submit" value="Submit Group"></td>
	<td></td><td></td>
	</tr>
	</table>
</form>
<?php
}else{
	if(!isset($_POST['groupname'])){
		die("ERROR: No group name entered.");
	}else{
		$groupname = $_POST['groupname'];
	}
	if($groupname == ""){
		die("ERROR: No group name entered.");
	}

# 1- Create an entry in usergroups
	$usergroupsSQL = "INSERT usergroups (usergroupname, userid) VALUES (\"$groupname\",$userid)";
	$usergroupsResult = $db->Execute($usergroupsSQL);
	checkSQLError($usergroupsResult, $usergroupsSQL);
# 1A- Get the groupid of this entry...
	$getIdSQL = "SELECT MAX(usergroupid) FROM usergroups WHERE userid = $userid";
	$idResult = $db->Execute($getIdSQL);
	checkSQLError($idResult, $getIdSQL);
	$idrow=$idResult->FetchRow();
# 2- Create an entry in usergroupmembers
	$usergroupmembersSQL = "INSERT usergroupmembers (usergroupid, userid) VALUES ($idrow[0],$userid)";
	$usergroupmembersresult = $db->Execute($usergroupmembersSQL);
	checkSQLError($usergroupmembersresult, $usergroupmembersSQL);		
# 3- Create an entry in usergroupadmins
	$useradminsSQL = "INSERT usergroupadmins (usergroupid, userid) VALUES ($idrow[0],$userid)";
	$useradminsresult = $db->Execute($useradminsSQL);
	checkSQLError($useradminsresult, $useradminsSQL);

	echo "<h3>You have successfully created a User Group: <font color='red'>$groupname</font></h3><br>You are currently the only administrator of this group.";
?>
	<h3>Additional Options</h3>

<?php	
	require('usergroupsoptions.inc.php');	
}
?>
