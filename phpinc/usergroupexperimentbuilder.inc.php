<?php
	echo "This section allows you to add Experiments to a User Group!<br>";
// Need to build the user group menu....
$groupExpBuilderMenu = "";
if($privval == 99){
	$expSQL = "SELECT usergroupid, usergroupname FROM usergroups ORDER BY usergroupid";
}else{
	//$expSQL = "SELECT usergroupid, usergroupname FROM usergroups WHERE userid=$userid ORDER BY usergroupid";
	$expSQL = "SELECT DISTINCT g.usergroupid, g.usergroupname FROM usergroups as g, usergroupadmins as a WHERE g.userid=$userid OR a.userid=$userid AND (g.usergroupid = a.usergroupid)";
}

	$expResult = $db->Execute($expSQL);
	$firstchoice = 1;
	# the location of the onChange function in the selectBuilderGroup menu is in expbuilderform.js.  
	$groupExpBuilderMenu = "<select name=\"selectedBuilderGroup\" id=\"selectedBuilderGroup\" onChange=\"updateGroupExpList()\">";
	$groupExpBuilderMenu .= "<option label=\"\" value=\"-1\" >No Experiment Selected</option>  ";
	$location = $_SERVER['PHP_SELF'];
	//echo "SELECTED IS $selected<br>";
		$countexp = 1;
		//while($row = mysql_fetch_array($expResult))

		while($row = $expResult->FetchRow())
		{
			
			$groupname = $row[1];
			
				$groupExpBuilderMenu .= "<option label=\"$groupname\" value=\"$row[0]\" >$groupname</option>  ";
				
			
			
		}
	/*$adminSQL = "SELECT usergroupid FROM usergroupadmins WHERE userid = $userid";
	$adminResult = $db->Execute($adminSQL);
	 while($row = $adminResult->FetchRow())
	{
			$grouplookup = $row[0];
			$groupNameSQL = "SELECT usergroupid, usergroupname FROM usergroups WHERE usergroupid=$grouplookup";
			$groupNameResult = $db->Execute($groupNameSQL);
			$groupnamerow = $groupNameResult->FetchRow();
			$usergroupid = $groupnamerow[0];
			$groupname = $groupnamerow[1];
			
			$groupExpBuilderMenu .= "<option label=\"$groupname\" value=\"$usergroupid\" >$groupname</option>  ";
				
			
			
	}	
	*/

		$groupExpBuilderMenu .= "</select>";





//analyze($_POST);
if(count($_POST) > 1){
	if(isset($_POST['selectedBuilderGroup'])){
		$selectedBuilderGroup = $_POST['selectedBuilderGroup'];
	}else{
		$selectedBuilderGroup = "";
	}
	if(isset($_POST['expList'])){
		$expList = $_POST['expList'];
	}else{
		$expList = "";
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
		

		$newlist = explode(",", $expList);
		
		$sizeofarray = count($newlist);
		//echo "<hr>";
		//echo "<br>Size of new array list: $sizeofarray<br>";
		//echo "new array list values: <br>";
		for($i = 0; $i < $sizeofarray; $i++){

			//echo $newlist[$i]."<br>";

		}

		//echo "<hr>";
		// Delete the original experiments from database where usergroupid = $selectedBuilderGroup....
		$arraySQL = "DELETE FROM expusergroupassoc WHERE usergroupid = $selectedBuilderGroup";

		$originalResult = $db->Execute($arraySQL);//mysql_query($arraySQL, $db);


		// Insert the new arrays into the database....
		foreach($newlist as $expval){
			// We need to check to make sure that we are not duplicating an insertion of an experiment already associated w/ that group

			$selectSQL = "SELECT COUNT(*) FROM expusergroupassoc WHERE expid = $expval AND usergroupid=$selectedBuilderGroup";
			$selectResult = $db->Execute($selectSQL);
			$selectRow = $selectResult->FetchRow();
			$count = $selectRow[0];
			if($count == 0){
				$insSQL = "INSERT expusergroupassoc(expid, usergroupid) VALUES ($expval,$selectedBuilderGroup)";
				//echo "$insSQL<BR>";
				$trxResult = $db->Execute($insSQL);//mysql_query($insSQL, $db);
			}else{
				echo "<font color='red'>Not adding experiment #$expid; it is already associatiated with this User Group.</font><br>";
			}
			
		}


}
?>
<form id="usergroupexpbuilder" name="usergroupexpbuilder" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onSubmit="return submitUserGroupExpBuilderForm()">
<h1>User Group Experiment Assignment Builder</h1>
<table><tbody>
<tr valign="top">
<td>
Which User Group?<br>
<?php
// This menu is built in this script
echo "$groupExpBuilderMenu";
?>
</td>
<td>

<input dojoType="dijit.form.RadioButton" id="dispAllExps" name="arrayoption"
           value="1" type="radio" onClick="return GetAllExperimentItems(this.value)"/>
           <label for="dispAllArrays"> Display all available experiments </label>
<br>
    <input dojoType="dijit.form.RadioButton" type="radio" id="dispMyExps"  name="arrayoption"
           value="2" onClick="return GetAllExperimentItems(this.value)"/>
           <label for="dispMyArrays"> Display all of my experiments already in a group </label>
<br>
    <input dojoType="dijit.form.RadioButton"  id="dispMyExpsNoGroup"  name="arrayoption"
           value="3" type="radio" checked="true" onClick="return GetAllExperimentItems(this.value)"/>
           <label for="dispMyExpsNoGroup"> Display only my experiments not assigned to a User Group already </label>
</td>
<td>
<input type="hidden" name="expList" value="">
</td>
</tr>


<tr valign="top">
<td>
Group: <font color="red"><strong><span id="numGroupItems">0</span></strong></font> Experiments Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="cart" class="target" accept="item" id="target1">
-->
<div id="target1" class="target">

</div>
</td>

<td>
Experiments: <font color="red"><strong><span id="numExpItems">0</span></strong></font> Experiments Listed
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


