<?php

// Need to build the user group menu....
$groupBuilderMenu = "";
if($privval == 99){
	$expSQL = "SELECT usergroupid, usergroupname FROM usergroups ORDER BY usergroupid";
}else{
	$expSQL = "SELECT DISTINCT g.usergroupid, g.usergroupname FROM usergroups as g, usergroupadmins as a WHERE g.userid=$userid OR a.userid=$userid AND (g.usergroupid = a.usergroupid)";
}

	$expResult = $db->Execute($expSQL);
	checkSQLError($expResult, $expSQL);
	$firstchoice = 1;
	# the location of the onChange function in the selectBuilderGroup menu is in expbuilderform.js.  
	$groupBuilderMenu = "<select name=\"selectedBuilderGroup\" id=\"selectedBuilderGroup\" onChange=\"updateGroupList()\">";
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





//analyze($_POST);
if(count($_POST) > 1){
	if(isset($_POST['selectedBuilderGroup'])){
		$selectedBuilderGroup = $_POST['selectedBuilderGroup'];
	}else{
		$selectedBuilderGroup = "";
	}
	
	if(isset($_POST['userList'])){
		$userList = $_POST['userList'];
	}else{
		$userList = "";
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
		if(isset($_POST['submit'])){
		
		#echo "userlist: $userList<br>";
		$newlist = explode(",", $userList);
		
		$sizeofarray = count($newlist);
	
		
		
		$previousmembersadminsarray = array();
		$previousSQL = "SELECT userid FROM usergroupmembers WHERE usergroupid = $selectedBuilderGroup";
		$previousMembersResult = $db->Execute($previousSQL);
		$useridselection = " (";
		$admincounter = 0;
		while($prevrow = $previousMembersResult->FetchRow()){
			$prevmem = $prevrow[0];
			$adminSQL = "SELECT COUNT(*) FROM usergroupadmins WHERE userid = $prevmem AND usergroupid = $selectedBuilderGroup";
			$adminResult = $db->Execute($adminSQL);
			checkSQLError($adminResult, $adminSQL);
			$admincount = $adminResult->FetchRow();
			if($admincount[0] != 0){
				#echo "$prevmem is an admin of group $selectedBuilderGroup<br>";
				array_push($previousmembersadminsarray, $prevmem);
				if($admincounter == 0){
					$useridselection .= "userid != $prevmem";
				}else{
					$useridselection .= " AND userid != $prevmem";
				}
				$admincounter++;
			}
		}
		$useridselection .= ")";
		//echo "<hr>";
		# what experiments are associated w/ this group?  we need to know this when we update the expusergroupassoc table....
		$thisgroupexparray = array();
		$groupexpSQL = "SELECT expid FROM expusergroup WHERE usergroupid = $selectedBuilderGroup";
		$groupexpResult = $db->Execute($groupexpSQL);		
		while($thisexprow = $groupexpResult->FetchRow()){
			$anexp = $thisexprow[0];
			array_push($thisgroupexparray,$anexp);
		}

		# find out what groups are associated w/ this group and delete them and this group from the expusergroupassoc table....
		$groupsInGroupSQL = "SELECT userid FROM usergroupmembers WHERE usergroupid = $selectedBuilderGroup AND userid < 0";
		$groupsInGroupResult = $db->Execute($groupsInGroupSQL);
		checkSQLError($groupsInGroupResult, $groupsInGroupSQL);
		while($grouprow = $groupsInGroupResult->FetchRow()){
			$thisgroup = $grouprow[0] * -1; # because group values are stored as negative values in usergroupmembers table....
			$delexpgroupSQL = "DELETE FROM expusergroupassoc WHERE usergroupid = $thisgroup";
			$delexpgroupResult = $db->Execute($delexpgroupSQL);
			checkSQLError($delexpgroupResult, $delexpgroupSQL);
		}

		// Delete the original group members from database where usergroup = $selectedBuilderGroup....
		// Can't delete yourself from here though.  That only occurs when the owner actually deletes the group....
		$groupSQL = "DELETE FROM usergroupmembers WHERE (usergroupid = $selectedBuilderGroup AND $useridselection)";

		$originalResult = $db->Execute($groupSQL);
		checkSQLError($originalResult, $groupSQL);

	



		$currentmembers = array();
		// Insert the new arrays into the database....
		foreach($newlist as $idval){
			if($idval > 0){
				#if($idval == $userid){
				if(in_array($idval, $previousmembersadminsarray)){
					# Skip the user if they've added themself to a group or we encountered an admin.
					continue;
				}
				$selectSQL = "SELECT COUNT(*) FROM usergroupmembers WHERE userid = $idval AND usergroupid=$selectedBuilderGroup";
				#echo $selectSQL;
				$selectResult = $db->Execute($selectSQL);
				checkSQLError($selectResult, $selectSQL);
				$selectRow = $selectResult->FetchRow();
				
				$count = $selectRow[0];
				if($count == 0){
					# first check to make sure that this $idval/$selectedBuilderGroup combo is not already in usergroupmembers table....

					$checkSQL = "SELECT * FROM usergroupmembers WHERE userid = $idval AND usergroupid = $selectedBuilderGroup";
					$result = $db->Execute($checkSQL);
					if($result->RecordCount()== 0){
						$insSQL = "INSERT usergroupmembers(userid, usergroupid) VALUES ($idval,$selectedBuilderGroup)";
						$trxResult = $db->Execute($insSQL);//mysql_query($insSQL, $db);
						checkSQLError($trxResult, $insSQL);
						array_push($currentmembers,$idval);
					}
				}else{
					$thisExpSQL = "SELECT id, firstname, lastname, organization FROM users WHERE id = $idval";
					$thisexpResult = $db->Execute($thisExpSQL);
					checkSQLError($thisexpResult, $thisExpSQL);
					$userRow = $thisexpResult->FetchRow();
					$uname = "<font color='blue'><strong>".$userRow[1]." ".$userRow[2]." - ".$userRow[3]."</strong></font>";
					echo "<font color='red'><strong><em>STATUS:</em></strong></font> <font style=\"background-color: yellow\"> Not adding user #$idval $uname; that user already associatiated with this User Group.</font><br>";
				}
			}else{
				# When inserting a usergroup, a negative number is used for the userid.
				$selectSQL = "SELECT COUNT(*) FROM usergroupmembers WHERE userid = $idval AND usergroupid=$selectedBuilderGroup";
				#echo $selectSQL;
				$selectResult = $db->Execute($selectSQL);
				$selectRow = $selectResult->FetchRow();
				$count = $selectRow[0];
				if($count == 0){
					# MAKE SURE WE ARE NOT ENTERING DUPLICATE AND GETTING AN ERROR DUE TO PRIMARY KEY COMBINATION
					$checkSQL = "SELECT * FROM usergroupmembers WHERE userid = $idval AND usergroupid = $selectedBuilderGroup";
					$result = $db->Execute($checkSQL);
					if($result->RecordCount()== 0){	
						$insSQL = "INSERT usergroupmembers(userid, usergroupid) VALUES ($idval,$selectedBuilderGroup)";
						$trxResult = $db->Execute($insSQL);
						checkSQLError($trxResult, $insSQL);
						array_push($currentmembers,$idval);
					}
					# need to go through the experiments array of associated experiments w/ the $selectedBuilderGroup and associate this group w/ all of those experiments....
					$idval = -1 * $idval;
					foreach($thisgroupexparray as $anexp){
						# MAKE SURE WE ARE NOT ENTERING DUPLICATE AND GETTING AN ERROR DUE TO PRIMARY KEY COMBINATION
						$checkSQL = "SELECT * FROM expusergroupassoc WHERE expid = $anexp AND usergroupid = $idval AND groupidkey = $selectedBuilderGroup";
						$result = $db->Execute($checkSQL);
						if($result->RecordCount()== 0){	
							$expGroupSQL = "INSERT expusergroupassoc (expid, usergroupid, groupidkey) VALUES ($anexp, $idval,$selectedBuilderGroup)";
							$expGroupResult = $db->Execute($expGroupSQL);
							checkSQLError($expGroupResult, $expGroupSQL);
						}
					}
					// Now go through and add the members in this group to the group this group is being added to
					
					// We should not have to deal w/ groups, because their members will be associated when the group is added to any group.....
					/*$groupselectSQL = "SELECT userid FROM usergroupmembers WHERE usergroupid = $idval AND userid > 0";
					$groupselectResult = $db->Execute($groupselectSQL);
					checkSQLError($groupselectResult, $groupselectSQL);
					while($grouprow=$groupselectResult->FetchRow()){
						$id = $grouprow[0];
						$insSQL = "INSERT usergroupmembers(userid, usergroupid) VALUES ($id,$selectedBuilderGroup)";
						$trxResult = $db->Execute($insSQL);
						checkSQLError($trxResult, $insSQL);

						
					}*/
				}else{
					$idval = $idval * -1;
					$thisExpSQL = "SELECT usergroupname FROM usergroups WHERE usergroupid = $idval";
					$thisexpResult = $db->Execute($thisExpSQL);
					checkSQLError($thisexpResult, $thisExpSQL);
					$userRow = $thisexpResult->FetchRow();
					$uname = "<font color='blue'><strong>".$userRow[0]."</strong></font>";
					echo "<font color='red'><strong><em>STATUS:</em></strong></font> <font style=\"background-color: yellow\"> Not adding user #$idval $uname; that Group already associatiated with this User Group.</font><br>";
				}
			}
			
		}

?>
		<h3>Users Added!</h3>
		<h3>Additional Options</h3>
	
<?php
		require('usergroupsoptions.inc.php');	
}else{
?>
<form id="usergroupbuilder" name="usergroupbuilder" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onSubmit="return submitUserGroupBuilderForm()">
<h1>User Group Membership Builder</h1>
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

<input dojoType="dijit.form.RadioButton" id="dispAllExps" name="arrayoption"
           value="1" type="radio"  checked="true" onClick="return GetAllUsers(this.value)"/>
           <label for="dispAllArrays"> Display all available Users </label>
<br>
    <input dojoType="dijit.form.RadioButton" type="radio" id="dispMyExps"  name="arrayoption"
           value="2" onClick="return GetAllUsers(this.value)"/>
           <label for="dispMyArrays"> Display all of Users already in one of my groups </label>
<br>
    <input dojoType="dijit.form.RadioButton"  id="dispMyExpsNoGroup"  name="arrayoption"
           value="3" type="radio" onClick="return GetAllUsers(this.value)"/>
           <label for="dispMyExpsNoGroup"> Display only Users not assigned to one of my User Groups already </label>
<br>
<!--
    <input dojoType="dijit.form.RadioButton"  id="dispAllGroups"  name="arrayoption"
           value="1" type="radio" onClick="return GetAllGroups(this.value)"/>
           <label for="dispMyExpsNoGroup"> Display all my available Groups </label>

<br>
    <input dojoType="dijit.form.RadioButton"  id="dispMyGroups"  name="arrayoption"
           value="2" type="radio" onClick="return GetAllGroups(this.value)"/>
           <label for="dispMyExpsNoGroup">Display all my Groups already in one of my groups</label>

<br>
    <input dojoType="dijit.form.RadioButton"  id="dispMyGroupsNoGroup"  name="arrayoption"
           value="3" type="radio" onClick="return GetAllGroups(this.value)"/>
           <label for="dispMyExpsNoGroup">Display only my Groups not assigned to one of my User Groups already</label>
-->
</td>
<td>
<input type="hidden" name="userList" value="">
</td>
</tr>


<tr valign="top">
<td>
Group: <font color="red"><strong><span id="numGroupItems">0</span></strong></font> Users/Groups Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="cart" class="target" accept="item" id="target1">
-->
<div id="target1" class="target">

</div>
</td>

<td>
Users/Groups: <font color="red"><strong><span id="numUserItems">0</span></strong></font> Users/Groups Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="shelf" class="source" id="source1" accept="item" singular=false>
-->
<div id="source1" class="source">

</div>
</td>


</tr><tbody/></table>
<!--
<button dojoType="dijit.form.Button" type="submit" id="expBuilderSubmit" value="expBuilderSubmit">Submit</button>
-->
<input type="submit" name="submit" value="Update Group"></td>
</form>
<?php
}
?>

