
					<?php

	if($expsubmitted != "true"){

?>
	<p>
<b>Instructions:</b> This form is used to create an experimental group to better organize your individual experiments composed of multiple arrays.<br>
</p>
<form enctype="multipart/form-data" name="agilentexp" action="groupexperimentbuilder.php" method="post" onSubmit="return submitStuff()">

<input type="hidden" name="expsubmitted" value="true">
<p><h3>New Experiment Group Form</h3></p>
<p><span><strong>Experiment Group Name:</strong><?php
echo "$expGroupMenu";

?><br></span><br><input type='checkbox' name='editName' id='editName' enabled onClick="return editExpName()">Edit Experiment Group Name?</input><br>
<input name='nameExp' id='nameExp' type="text" value="Check 'Edit Experiment Group Name?' checkbox to modify current treatment name." size='90' align="left" disabled='true'></input><br> (<b><font color='red'>NOTE:</font></b> If you make changes to the Experiment Group Name and uncheck the <i>"Edit Experiment Group Name?"</i> checkbox, the changes will not be acknowledged.)
</p>
<p>
<input type='checkbox' name='editDesc' id='editDesc' onClick="return editExpDesc('editDesc')">Edit Description?</input><br> (<b><font color='red'>NOTE:</font></b> <i>If you make changes to the Experiment Description and uncheck the <i>"Edit Description?"</i> checkbox, the changes will not be acknowledged.)
</p>
<strong>Experiment Group Description:</strong>
<div width="200px">
<textarea width="200px" dojoType="dijit.Editor" name="expDesc" id="expDesc" align="left">
<?php
echo $expDesc;
?>
</textarea>
</div>
<div id="hiddendiv">
<textarea rows="0" cols="0" id="expDesc" name="expDesc">
<?php
echo $expDesc;
?>
</textarea>
</div>
</span></p>
<p><span>


<p><span><input type="submit" name="submit" value="ExpSubmit">
<input type="reset" value="Reset Form"></span></p>
</form>

<?php
}else{

	//echo "Form submitted...";

	//analyze($_POST);
	$experimentID = "";
	$experimentName = "";
	$experimentDesc = "";

if($selectedExperiment == -1){
	// A new experiment has been entered...
	//echo "<br>$_POST[expDesc]<br>";
	$experimentName = $nameExp;
	$experimentDesc = $_POST[expDesc];
	// get a new id based on the next id available in agilent_experimentsdesc
	$countSQL = "Select MAX(expgroupid) from agilent_experimentgroupsdesc";
		$countResult = mysql_query($countSQL, $db);
		$row = mysql_fetch_row($countResult);
		$maxsampleID = $row[0];
		if(is_nan($maxsampleID)){
			$maxsampleID = 0;
		}
		$experimentgroupID = $maxsampleID + 1;
?>
<p>
<b>Instructions:</b> This form is used to create an experimental group to better organize your individual experiments composed of multiple arrays.<br>
</p>
<form enctype="multipart/form-data" name="agilentexp" action="groupexperimentbuilder.php" method="post">

<input type="hidden" name="expsubmitted" value="true">
<input type="hidden" name="reviewed" value="true">
<input type="hidden" name="expID" value="<?php echo $experimentgroupID; ?>">
<input type="hidden" name="expNew" value="true">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2"><font color="red"><strong>Please check for errors!</strong></font></th>
</tr>
</thead>
<tr>
<td class="questionparameter" ><strong>New Experiment Group ID:</strong></td>
<td class="results">
<?php
	echo "$experimentgroupID";
?>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Experiment Group Name:</strong></td>
<input type="hidden" name="expDescName" value="<?php echo "$experimentName"; ?>">
<td class="results">
<?php
	echo "$experimentName";
?>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Experiment Description:</strong></td>
<td class="results">
<input type="hidden" name="expDescValue" value="<?php echo $_POST[expDesc]; ?>">
<div dojoType="dijit.layout.ContentPane" style="width:800px;height:300px">
<?php
echo $experimentDesc
?>
</div>
</td>
</tr>
<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td><input type="button" value="Incorrect Values...Fix Them!" onClick="history.go(-1)"></td>
</tr>


</table>
</form>


<?php
}else{

	if($reviewed != true){
		$expDescChanged = false;
		$expNameChanged = false;
		// check to see if any changes were made to a previous description....
		$expSQL = "SELECT * FROM agilent_experimentgroupssdesc WHERE expid = $selectedExperiment";
		$countResult = mysql_query($expSQL, $db);
		$row = mysql_fetch_row($countResult);
		$experimentID = $row[0];
		$experimentName = $row[1];
		$oldExperimentDesc = $row[2];

		if($editDesc == on){
			$expDescChanged = true;
			$expDescVal = $_POST['expDesc'];
		}else{
			$expDescVal = $oldExperimentDesc;
		}
		if($editName == on){
			$expNameChanged = true;
			$expNameVal = $_POST['nameExp'];
		}else{
			$expNameVal = $experimentName;

		}
?>
		<p>
		<b>Instructions:</b> This form is used to associate an experimental basis for your RNA sample submission and must be completed for <font color="red"><b>EACH</b></font> (distinct) RNA sample that you send.  Thanks!<br>
		</p>
		<form enctype="multipart/form-data" name="agilentexp" action="groupexperimentbuilder.php" method="post">

		<input type="hidden" name="expsubmitted" value="true">
		<input type="hidden" name="expID" value="<?php echo $experimentID; ?>">
		<input type="hidden" name="expDescChanged" value="<?php echo $expDescChanged; ?>">
		<input type="hidden" name="expNameChanged" value="<?php echo $expNameChanged; ?>">
		<input type="hidden" name="reviewed" value="true">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="3"><font color="red"><strong>Please check for errors!</strong></font></th>
		</tr>
		</thead>

		<tr>
		<td class="questionparameter" ><strong>Experiment Group ID:</strong></td>
		<td class="results" colspan='2'>
		<?php
			echo "$experimentID";
		?>
		</td>
		</tr>
		<tr>
		<input type="hidden" name="expNameValue" value="<?php echo $expNameVal; ?>">
<?php
		if($expNameChanged == false){
?>
		<td class="questionparameter" ><strong>Experiment Group Name:</strong></td>
		<td class="results" colspan='2'>

		<?php
			echo "$experimentName";
		?>
		</td>
		</tr>
<?php
		}else{
?>
		<td class="questionparameter" ><strong> Old Experiment Group Name:</strong></td>
		<td class="results" colspan='2'>
		<?php
			echo "$experimentName";
		?>
		</td>
		</tr>
		<tr>
		<td class="questionparameter" ><strong> New Experiment Group Name:</strong></td>
		<td class="results" colspan='2'>
		<?php
			echo "$expNameVal";
		?>
		</td>
		</tr>
<?php
		}
?>

		<tr>
		<td class="questionparameter" ><strong>Experiment Group Description:</strong></td>
		<input type="hidden" name="expDescValue" value="<?php echo $expDescVal; ?>">

		<td class="results">
		<strong>Current version:<br></strong>
		<div dojoType="dijit.layout.ContentPane" style="width:400px;height:300px">
		<?php
		echo $oldExperimentDesc;
		?>
		</div>
		</td>
		<?php
		if($expDescChanged == true){
		?>
		<td class="results">
		<strong><font color="red">New version:</font></strong><br>
		<div dojoType="dijit.layout.ContentPane" style="width:400px;height:300px">
		<?php
		echo $_POST[expDesc];
		?>
		</div>
		</td>
		</tr>
<?php

		}else{

			echo "<td></td>";
		}
?>
		<tr>
		<td><input type="submit" name="submit" value="Submit"></td>
		<td><input type="button" value="Incorrect Values...Fix Them!" onClick="history.go(-1)"></td>
		</tr>


		</table>
		</form>


<?php



		}
		else{

			if($expNew == true){

				// We have to update the `agilent_experimentsdesc` table....
				// add new entry in chem table for this new chemical entered by user
				$insertSQL = "INSERT agilent_experimentsdesc(expid, expname,descrip) VALUES($expID, \"$expDescName\",\"$expDescValue\")";
				$insertResult = mysql_query($insertSQL, $db);

				if(mysql_errno($db)){
					echo "<strong>Database Error inserting.  SQL: $insertSQL</strong><br>";
				}else{
?>
<script type="text/javascript">
					alert("You've successfully created your experiment.")
window.location = "http://edge.oncology.wisc.edu/groupexperimentbuilder.php"
</script>
<?php
				}
			}
			if($expDescChanged == true || $expNameChanged == true){
				$updateSQL= "UPDATE agilent_experimentsdesc SET expname = \"$_POST[expNameValue]\", descrip = \"$_POST[expDescValue]\" WHERE expid = $_POST[expID]";
				$sqlResult = mysql_query($updateSQL, $db);
				if(mysql_errno($db)){
					echo "<strong>Database Error updating table. SQL: $updateSQL </strong><br>";
				}else{

?>

<script type="text/javascript">
alert("You've successfully updated the description of your experiment.")
window.location = "http://edge.oncology.wisc.edu/groupexperimentbuilder.php"

</script>

<?php				}

			}



		}
	}
}
?>
				
