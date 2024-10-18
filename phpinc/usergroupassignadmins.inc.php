<?php
	echo "<h3>This section allows you to assign/remove Administrators to a User Group!</h3>";
// Need to build the user group menu....
$groupBuilderMenu = "";
if($privval == 99){
	$expSQL = "SELECT usergroupid, usergroupname FROM usergroups ORDER BY usergroupid";
}else{
	$expSQL = "SELECT DISTINCT g.usergroupid, g.usergroupname FROM usergroups as g, usergroupadmins as a WHERE g.userid=$userid OR a.userid=$userid AND (g.usergroupid = a.usergroupid)";
}
	$expResult = $db->Execute($expSQL);
	$firstchoice = 1;
	# the location of the onChange function in the selectBuilderGroup menu is in expbuilderform.js.  
	$groupBuilderMenu = "<select name=\"selectedBuilderGroup\" id=\"selectedBuilderGroup\" onChange=\"updateMemberCheckList()\">";
	$groupBuilderMenu .= "<option label=\"\" value=\"-1\" >No Group Selected</option>  ";
	$location = $_SERVER['PHP_SELF'];
	//echo "SELECTED IS $selected<br>";
		$countexp = 1;
		//while($row = mysql_fetch_array($expResult))

		while($row = $expResult->FetchRow())
		{
			//echo "$countexp,";
			if($selected == $row[0]){
				//echo "found $selected<br>";
				$chosen = "SELECTED";
			}else{

				$chosen = "";
			}
			$groupname = $row[1];
			if($firstchoice == 1){
				$groupBuilderMenu .= "<option label=\"$groupname\" value=\"$row[0]\" >$groupname</option>  ";
				
				$firstchoice = 0;
			}
			else{
				$groupBuilderMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\">$groupname</option>  ";
			}
			$countexp++;
		}
		if($selected == -1){
			$chosen = "SELECTED";
		}else{
			$chosen = "";
		}
		$groupBuilderMenu .= "</select>";



$status = "";

if(count($_POST) > 1){
	if(isset($_POST['selectedBuilderGroup'])){
		$selectedBuilderGroup = $_POST['selectedBuilderGroup'];
	}else{
		$selectedBuilderGroup = "";
	}
	
	if(isset($_POST['numMembers'])){
		$numMembers = $_POST['numMembers'];
	}else{
		$numMembers = "";
	}
	if(isset($_POST['submit'])){
		$submit = $_POST['submit'];
	}else{
		$submit = "";
	}
	if(isset($_POST['owner'])){
		$owner = $_POST['owner'];	
	}else{
		$owner = "";
	}
	if(isset($_POST['ownerid'])){
		$ownerid = $_POST['owner'];	
	}else{
		$ownerid = "";
	}
	$checkedcount = 0;
	for($i = 0; $i < $numMembers; $i++){
		$member = "member".$i;
		if(isset($_POST[$member])){
			$checkedcount++;
		}
	}
	$statusvalue = 0;  # this is used to determine whether or not to say changes were made or were not made.
	if($selectedBuilderGroup!= -1){
	# Get the userid of the querier
	$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			$idResult = $db->Execute($userSQL);
			$idRow = $idResult->FetchRow();
			$userid = $idRow[0];
	if($checkedcount == 0){
		$status .= "<br><font color='red'><strong><em>STATUS:</em></strong></font> <font style=\"background-color: yellow\"> There were no admins checked or there was only one admin (i.e., the owner) listed.  Deleting all admins and reinserting the group owner as the admin.  If you want to delete this group, go to the 'Delete Groups Page'.</font><br>";
		# DELETE THE CURRENT MEMBERS FOR THIS GROUP....
		$delSQL = "DELETE FROM usergroupadmins WHERE usergroupid = $selectedBuilderGroup AND userid != $owner";
		$delResult = $db->Execute($delSQL);
		
		$insertSQL = "INSERT usergroupadmins(userid, usergroupid) VALUES ($owner,$selectedBuilderGroup)";
		$insertResult = $db->Execute($insertSQL);

		$submit = "";

	}else{
		$statusvalue = 1;  # setting to 1 because there were changes made....
		# DELETE THE CURRENT MEMBERS FOR THIS GROUP....
		$delSQL = "DELETE FROM usergroupadmins WHERE usergroupid = $selectedBuilderGroup";
		$delResult = $db->Execute($delSQL);
		
	
		# Determining which boxes were checked....
		for($i = 0; $i < $numMembers; $i++){
			$member = "member".$i;
			#echo "member: $member<br>";
			if(isset($_POST[$member])){
				$idval = $_POST[$member];
				# make these individuals administrators....
				$insSQL = "INSERT usergroupadmins(userid, usergroupid) VALUES ($idval,$selectedBuilderGroup)";
				#echo "<br>$insSQL<br>";
				$trxResult = $db->Execute($insSQL);
			}
		}
	}	
		if($ownerid != ""){
			$insSQL = "INSERT usergroupadmins(userid, usergroupid) VALUES ($ownerid,$selectedBuilderGroup)";
					$trxResult = $db->Execute($insSQL);
	
		}
		if($statusvalue == 1){
			$status .= "<br><font color='red'><strong><em>STATUS:</em></strong></font> <font style=\"background-color: yellow\"> The changes have been made.</font><br>";
		}else{
			$status .= "<br><font color='red'><strong><em>STATUS:</em></strong></font> <font style=\"background-color: yellow\"> No changes were made.</font><br>";
		}
		$submit = "submit";
	}else{
		$status .= "<br><font color='red'><strong><em>STATUS:</em></strong></font> <font style=\"background-color: yellow\"> You did not select a group!</font><br>";

		$submit = "";


	}




}
else{
	$submit = "";

}
if($submit !=""){
echo $status;
	
?>




<h3>Additional Options</h3>

<?php	
	require('usergroupsoptions.inc.php');		

}else{

?>
<form id="usergroupassignadmin" name="usergroupassignadmin" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onSubmit="">
<h1>User Group Admin Administration</h1>
<table><tbody>
<tr valign="top">
<td>
Which User Group?<br>
<?php
// This menu is built in this file.
echo "$groupBuilderMenu";
?>
</td>
<td>
<?php
echo $status;

?>
</td>
</tr>


<tr valign="top">
<td>
<div id="groupList"></div>
</td>
<td></td>
</tr><tbody/></table>
<input type="submit" name="submit" value="Update Group Admins"></td>
</form>
<?php


}
?>
