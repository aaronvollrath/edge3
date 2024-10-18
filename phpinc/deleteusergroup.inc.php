<?php
	
// Need to build the user group menu....
$groupBuilderMenu = "";
if($privval == 99){
	$expSQL = "SELECT usergroupid, usergroupname FROM usergroups ORDER BY usergroupid";
}else{
	$expSQL = "SELECT usergroupid, usergroupname FROM usergroups WHERE userid=$userid";
}

	$expResult = $db->Execute($expSQL);
	checkSQLError($expResult, $expSQL);
	# the location of the onChange function in the selectBuilderGroup menu is in expbuilderform.js.  
	$groupBuilderMenu = "<select name=\"selectedBuilderGroup\" id=\"selectedBuilderGroup\">";
	$groupBuilderMenu .= "<option label=\"\" value=\"-1\" >No Group Selected</option>  ";
	
	
		while($row = $expResult->FetchRow())
		{
			
			$groupname = $row[1];
			
				$groupBuilderMenu .= "<option label=\"$groupname\" value=\"$row[0]\" >$groupname</option>  ";
			
		}
		
		$groupBuilderMenu .= "</select>";



$status="";

//analyze($_POST);
if(count($_POST) > 1){
	if(isset($_POST['selectedBuilderGroup'])){
		$selectedBuilderGroup = $_POST['selectedBuilderGroup'];
	}else{
		$selectedBuilderGroup = "";
	}
	if(isset($_POST['submit'])){
		$submit = $_POST['submit'];
	}else{
		$submit = "";
	}
}
else{
	$submit = "";

}
$status = "";
if(isset($selectedBuilderGroup)){
	if($selectedBuilderGroup == -1){
		$status= "<br><font color='red'><strong><em>STATUS:</em></strong></font> <font style=\"background-color: yellow\"> You did not select a group and/or there are no groups to delete!</font><br>";
		$submit = "";
	}
}
if($submit != ""){
		$infoSQL = "SELECT usergroupname FROM usergroups WHERE usergroupid = $selectedBuilderGroup";
		$infoResult = $db->Execute($infoSQL);
		checkSQLError($infoResult, $infoSQL);
		$namerow = $infoResult->FetchRow();
		$name= $namerow[0];
		
	# DELETE THE CURRENT MEMBERS FOR THIS GROUP....
		$delSQL = "DELETE FROM usergroupadmins WHERE usergroupid = $selectedBuilderGroup";
		$delResult = $db->Execute($delSQL);
		checkSQLError($delResult, $delSQL);
		$delSQL = "DELETE FROM usergroupmembers WHERE usergroupid = $selectedBuilderGroup";
		$delResult=$db->Execute($delSQL);
		checkSQLError($delResult, $delSQL);
		$delSQL = "DELETE FROM usergroups WHERE usergroupid = $selectedBuilderGroup";
		$delResult = $db->Execute($delSQL);
		checkSQLError($delResult, $delSQL);
		$delSQL = "DELETE FROM expusergroupassoc WHERE usergroupid = $selectedBuilderGroup";
		$delResult = $db->Execute($delSQL);
		checkSQLError($delResult, $delSQL);
	$status.= "<br><font color='red'><strong><em>STATUS:</em></strong></font> <font style=\"background-color: yellow\"> Deleted the User Group, <font color='blue'><b><i>$name</i></b></font>, from the database.</font><br>";


	echo $status;
?>
	<h3>Additional Options</h3>

<?php	
	require('usergroupsoptions.inc.php');	

}else{

?>
<form id="usergroupassignadmin" name="usergroupassignadmin" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onSubmit="">
<h1>Delete Your User Groups</h1>
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
</td>
</tr><tbody/></table>
<input type="submit" name="submit" value="Delete Group"></td>
</form>
<?php
}
?>

