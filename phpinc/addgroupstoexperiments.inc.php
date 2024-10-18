<?php


if($privval == 99){
			$expSQL = "SELECT expid, expname, descrip FROM agilent_experimentsdesc  ORDER BY expid";
		}else{
			$expSQL = "SELECT expid, expname,descrip FROM agilent_experimentsdesc WHERE ownerid=$privval ORDER BY expid";

	}
		//echo "$expSQL<br>";
		$expResult = $db->Execute($expSQL);//mysql_query($expSQL, $db);
		$firstchoice = 1;
		$expGroupsBuilderMenu = "<select name=\"selectedBuilderExperiment\" id=\"selectedBuilderExperiment\" onChange=\"updateExpGroupBuilderList()\">";
		$expGroupsBuilderMenu .= "<option label=\"\" value=\"-1\" >No Experiment Selected</option>  ";
		$countexp = 1;
		//while($row = mysql_fetch_array($expResult))

		while($row = $expResult->FetchRow())
		{
			
			$expname = $row[1];
			if($firstchoice == 1){
				
				
				$expGroupsBuilderMenu .= "<option label=\"$expname\" value=\"$row[0]\" >$expname</option>  ";
				$expDesc = $row[2];
				$firstchoice = 0;
			}
			else{
				
				$expGroupsBuilderMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\">$expname</option>  ";
			}
			$countexp++;
		}
		
		$expGroupsBuilderMenu .= "</select>";

		

#analyze($_POST);
if(count($_POST) > 1){
	if(isset($_POST['selectedBuilderExperiment'])){
		$selectedBuilderExperiment = $_POST['selectedBuilderExperiment'];
	}else{
		$selectedBuilderExperiment = "";
	}
	
	if(isset($_POST['expGroupList'])){
		$expGroupList = $_POST['expGroupList'];
	}else{
		$expGroupList = "";
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

		$expGroupList = trim($expGroupList);
		$newlist2 =explode(",", $expGroupList);
		
		// Delete the original groups from database where expid = $selectedBuilderExperiment....
		$delSQL = "DELETE FROM expusergroupassoc WHERE expid = $selectedBuilderExperiment";

		$originalResult = $db->Execute($delSQL);
		$delSQL = "DELETE FROM expusergroup WHERE expid = $selectedBuilderExperiment";

		$originalResult = $db->Execute($delSQL);
		$sizeofarray = count($newlist2);
		for($i = 0; $i < $sizeofarray; $i++){

			if($newlist2[$i] < 0){
				$newlist2[$i] *= -1;
			}

		}
		$newlist2 =array_unique($newlist2);
		#analyze($newlist2);
		$groupstack = array();  # a stack to add and remove groups....
		$grouparrays = array(); # an array used for holding ALL groups associated w/ each group.....
		$keygroups = array();
		foreach($newlist2 as $groupval){
			if(!is_numeric($groupval)){
				continue;
			}

			array_push($keygroups,$groupval);
			# we need to insert this group intio expusergroup table and then find out what groups are associated w/ this group....
			$checkSQL = "SELECT * FROM expusergroup WHERE expid = $selectedBuilderExperiment AND usergroupid = $groupval";
			$result = $db->Execute($checkSQL);
			if($result->RecordCount() == 0){
				$insSQL = "INSERT expusergroup(expid,usergroupid) VALUES ($selectedBuilderExperiment,$groupval)";
				$trxResult = $db->Execute($insSQL);
			}
			$checkSQL = "SELECT * FROM expusergroupassoc WHERE expid = $selectedBuilderExperiment AND usergroupid = $groupval AND groupidkey = $groupval";
			$result = $db->Execute($checkSQL);
			if($result->RecordCount() == 0){
				$insSQL = "INSERT expusergroupassoc(expid,usergroupid,groupidkey) VALUES ($selectedBuilderExperiment,$groupval,$groupval)";
				$trxResult = $db->Execute($insSQL);
			}

			
			$groupMembersSQL = "SELECT userid FROM usergroupmembers WHERE usergroupid = $groupval AND userid < 0";
			#echo "$groupMembersSQL<br>";
			$result = $db->Execute($groupMembersSQL);
			$groupstack[$groupval] = array();
			#$grouparray[$groupval] = array();
			while($row=$result->FetchRow()){
				$groupmember = $row[0] * -1;
				/*$insSQL = "INSERT expusergroupassoc(expid,usergroupid,groupidkey) VALUES ($selectedBuilderExperiment,$groupmember, $groupval)";
				$trxResult = $db->Execute($insSQL);
				*/
				# push onto the stack....
				array_push($groupstack[$groupval], $groupmember);
				#array_push($grouparrays[$groupval], $groupmember);
			}
		}
		#echo "groupstack:";
		#analyze($groupstack);
		$numofkeygroups = count($keygroups);
		#echo "keygroups:";
		#analyze($keygroups);
		for($i = 0; $i<$numofkeygroups; $i++){
				
			$key = $keygroups[$i];
			#echo "this key is: $key<br>";
			$stackcount = count($groupstack[$key]);
			#echo "the stack count is $stackcount<br>";
			#for($j = 0; $j < $stackcount; $j++){
				$grouparray = array();
				while($thisval = array_pop($groupstack[$key])){
					
					
					array_push($grouparray, $thisval);
					$sql = "SELECT usergroupid, userid FROM usergroupmembers WHERE usergroupid = $thisval AND userid < 0";
					#echo "<hr>$sql<br>";
					$result = $db->Execute($sql);
					if($result->RecordCount() > 0){
						while($row = $result->FetchRow()){
							$groupval = $row[1]*-1;
							array_push($groupstack[$key], $groupval);
							array_push($grouparray, $groupval);
						}
					}
		
					
				}
				#echo "grouparray after $i key<br>";
				#analyze($grouparray);
				$grouparray = array_unique($grouparray);
				foreach($grouparray as $groupval){
					# is there already an entry for this?
					$checkSQL = "SELECT * FROM expusergroupassoc WHERE expid = $selectedBuilderExperiment AND usergroupid = $groupval AND groupidkey = $key";
					#echo "checkSQL: $checkSQL<br>";
					$result = $db->Execute($checkSQL);
					if($result->RecordCount() == 0){
						$insSQL = "INSERT expusergroupassoc(expid,usergroupid,groupidkey) VALUES ($selectedBuilderExperiment,$groupval,$key)";
						$trxResult = $db->Execute($insSQL);
					}		
				}
			#}
			# now need to put the values in grouparray into the expgroupassoc table...
				
		}


?>
		<h3>The changes have been made</h3>
		<h3>Additional Options</h3>

	
<?php	
	require('usergroupsoptions.inc.php');		
}else{
#  the onSubmit function in this form is located in expbuilderform.js
?>
<form id="expbuilder" name="expbuilder" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onSubmit="return submitExpGroupBuilderForm()">
<h1>Experiment Builder</h1>
<table><tbody>
<tr valign="top">
<td>
Which Experiment?<br>
<?php
// This menu is built in this file....
echo "$expGroupsBuilderMenu";
?>
</td>
<td>
<input dojoType="dijit.form.RadioButton"  id="dispAllGroups"  name="arrayoption"
           value="1" type="radio" checked onClick="return GetAllGroups(this.value)"/>
           <label for="dispMyExpsNoGroup"> Display all my available Groups </label>

<br>
    <input dojoType="dijit.form.RadioButton"  id="dispMyGroups"  name="arrayoption"
           value="2" type="radio" onClick="return GetAllGroups(this.value)"/>
           <label for="dispMyExpsNoGroup">Display all my Groups already in one of my groups</label>

<br>
    <input dojoType="dijit.form.RadioButton"  id="dispMyGroupsNoGroup"  name="arrayoption"
           value="3" type="radio" onClick="return GetAllGroups(this.value)"/>
           <label for="dispMyExpsNoGroup">Display only my Groups not assigned to one of my User Groups already</label>

</td>
<td>
<input type="hidden" name="expGroupList" value="">
</td>
</tr>


<tr valign="top">
<td>
Groups: <font color="red"><strong><span id="numExpGroups">0</span></strong></font> Groups Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="cart" class="target" accept="item" id="target1">
-->
<div id="target1" class="target">

</div>
</td>

<td>
Groups: <font color="red"><strong><span id="numUserItems">0</span></strong></font> Groups Listed
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
<input type="submit" name="submit" value="Update Experiment Groups"></td>
</form>
<?php

}
?>

