<?php
//analyze($_POST);
if(isset($_POST['expsubmitted'])){
	$expsubmitted = $_POST['expsubmitted'];
}else{
	$expsubmitted = "";
}
if(isset($_POST['selectedExperiment'])){
	$selectedExperiment = $_POST['selectedExperiment'];
}else{
	$selectedExperiment = "";
}

if(isset($_POST['nameExp'])){
	$nameExp = $_POST['nameExp'];
}else{
	$nameExp = "";
}
if(isset($_POST['info'])){
	$expDesc = $_POST['info'];
}else{
	$expDesc = "";
}
if(isset($_POST['submit'])){
	$submit = $_POST['submit'];
}else{
	$submit = "";
}
if(isset($_POST['reviewed'])){
	$reviewed = $_POST['reviewed'];
}else{
	$reviewed = "";
}
if(isset($_POST['expID'])){
	$expID = $_POST['expID'];
}else{
	$expID = "";
}

if(isset($_POST['expNew'])){
	$expNew = $_POST['expNew'];
}else{
	$expNew = "";
}
if(isset($_POST['expDescName'])){
	$expDescName = $_POST['expDescName'];
}else{
	$expDescName = "";
}
if(isset($_POST['expDescVal'])){
	$expDescVal = $_POST['expDescVal'];
}else{
	$expDescVal = "";
}
if(isset($_POST['editDesc'])){
	$editDesc = $_POST['editDesc'];
}else{
	$editDesc = "";
}
if(isset($_POST['editName'])){
	$editName = $_POST['editName'];
}else{
	$editName = "";
}
if(isset($_POST['expDescChanged'])){
	$expDescChanged = $_POST['expDescChanged'];
}else{
	$expDescChanged = "";
}
if(isset($_POST['expNameChanged'])){
	$expNameChanged = $_POST['expNameChanged'];
}else{
	$expNameChanged = "";
}
if(isset($_POST['expDescValue'])){
	$expDescValue = $_POST['expDescValue'];
}else{
	$expDescValue = "";
}

//analyze($_POST);

//echo "expsubmitted = $expsubmitted<br>";
if($expsubmitted != "true"){
		
	$expid = $_GET['editid'];
	if($expid != "" && $expid != -1){
			$expSQL = "SELECT descrip FROM agilent_experimentsdesc WHERE expid=$expid";
	
			$expResult = $db->Execute($expSQL);//mysql_query($expSQL, $db);
	
			$row = $expResult->FetchRow();//mysql_fetch_array($expResult);
			$desc = trim($row[0]);
			//echo $desc;
			$expDesc = $desc;
	}else{
		if(isset($_POST['expDesc'])){
			$expDesc = $_POST['expDesc'];
		}else{
			$expDesc = "";
		}
	}

	if($expid == -1){
	
		// if we're creating a new experiment....
		$checkboxstatus = "checked";
		$disabled = "false";
	
	}else{
		$disabled = "true";
		$checkboxstatus = "";
	
	}

?>

	<p>
<b>Instructions:</b> This form is used to associate an experimental basis with your RNA sample submission.  An experiment must be associated <font color="red"><b>EACH</b></font> (distinct) RNA sample that you send.  However, more than one RNA sample can be associated with one experiment.  Thanks!<br>
</p>
<form enctype="multipart/form-data" name="agilentexp" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onSubmit="return submitStuff()">

<input type="hidden" name="expsubmitted" value="true">
<p><h3>New Experiment Form</h3></p>
<p><span><strong>Experiment Name:</strong><?php
#  this menu is built in the script: aglientexperiment-useradmin.php
echo "$expMenu";

?><br></span><br><input type='checkbox' name='editName' id='editName' <?php echo $checkboxstatus;?> onClick="return editExpName()">Edit Experiment Name?</input><br>
<input name='nameExp' id='nameExp' type="text" value="Check 'Edit Experiment Name?' checkbox to modify current treatment name." size='90' align="left" disabled='<?php echo $disabled; ?>'></input><br> (<b><font color='red'>NOTE:</font></b> If you make changes to the Experiment Name and uncheck the <i>"Edit Experiment Name?"</i> checkbox, the changes will not be acknowledged.)
</p>
<p>
<input type='checkbox' name='editDesc' id='editDesc' <?php echo $checkboxstatus;?> onClick="return editExpDesc('info')">Edit Description?</input><br> (<b><font color='red'>NOTE:</font></b> <i>If you make changes to the Experiment Description and uncheck the <i>"Edit Description?"</i> checkbox, the changes will not be acknowledged.)
</p>
<strong>Experiment Description:</strong>
<div width="800px" id="editordiv">
<TEXTAREA NAME="info" id="info" align="left" cols=100 rows=20>
<?php
echo $expDesc;
?>
</TEXTAREA>
</div>
<div id="hiddendiv">
<TEXTAREA rows="0" cols="0" id="expDeschidden" name="expDesc">
<?php
echo $expDesc;
?>
</TEXTAREA>
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
	$experimentDesc = $_POST['expDesc'];
	// get a new id based on the next id available in agilent_experimentsdesc
	$countSQL = "Select MAX(expid) from agilent_experimentsdesc";
		$countResult = $db->Execute($countSQL);//mysql_query($countSQL, $db);
		$row = $countResult->FetchRow();//mysql_fetch_row($countResult);
		$maxsampleID = $row[0];
		if(is_nan($maxsampleID)){
			$maxsampleID = 0;
		}
		$experimentID = $maxsampleID + 1;
?>
<p>
<b>Instructions:</b> This form is used to associate an experimental basis for your RNA sample submission and must be completed for <font color="red"><b>EACH</b></font> (distinct) RNA sample that you send.  Thanks!<br>
</p>
<form enctype="multipart/form-data" name="agilentexp" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">

<input type="hidden" name="expsubmitted" value="true">
<input type="hidden" name="reviewed" value="true">
<input type="hidden" name="expID" value="<?php echo $experimentID; ?>">
<input type="hidden" name="expNew" value="true">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2"><font color="red"><strong>Please check for errors!</strong></font></th>
</tr>
</thead>
<tr>
<td class="questionparameter" ><strong>New Experiment ID:</strong></td>
<td class="results">
<?php
	echo "$experimentID";
?>
</td>
</tr>
<tr>

<td class="questionparameter" ><strong>Experiment Name:</strong></td>
<input type="hidden" name="expDescName" value="<?php echo "$experimentName"; ?>">
<td class="results">
<?php
	echo "$experimentName";
?>
</td>
</tr>
<?php
$expDescww = wordwrap($expDesc, 70);

?>
<tr>
<td class="questionparameter" ><strong>Experiment Description:</strong></td>
<td class="results">
<input type="hidden" name="expDescValue" value="<?php echo $expDesc; ?>">
<div dojoType="dijit.layout.ContentPane" style="width:800px;height:300px">
<?php
echo "<TEXTAREA align='left' cols='100' rows='15'>$expDescww</TEXTAREA>";
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
		$expSQL = "SELECT * FROM agilent_experimentsdesc WHERE expid = $selectedExperiment";
		$countResult = $db->Execute($expSQL);//mysql_query($expSQL, $db);
		$row = $countResult->FetchRow();//mysql_fetch_row($countResult);
		$experimentID = $row[0];
		$experimentName = $row[1];
		$oldExperimentDesc = $row[2];

		if($editDesc == "on"){
			$expDescChanged = true;
			$expDescVal = $_POST['info'];
		}else{
			$expDescVal = $oldExperimentDesc;
		}
		if($editName == "on"){
			$expNameChanged = true;
			$expNameVal = $_POST['nameExp'];
		}else{
			$expNameVal = $experimentName;

		}
?>
		<p>
		<b>Instructions:</b> This form is used to associate an experimental basis for your RNA sample submission and must be completed for <font color="red"><b>EACH</b></font> (distinct) RNA sample that you send.  Thanks!<br>
		</p>
		<form enctype="multipart/form-data" name="agilentexp" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">

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
		<td class="questionparameter" ><strong>Experiment ID:</strong></td>
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
		<td class="questionparameter" ><strong>Experiment Name:</strong></td>
		<td class="results" colspan='2'>

		<?php
			echo "$experimentName";
		?>
		</td>
		</tr>
<?php
		}else{
?>
		<td class="questionparameter" ><strong>Experiment Name:</strong></td>
		<td class="results"><strong>Current Experiment Name:</strong>
		<?php
			echo "$experimentName";
		?>
		</td>
		
		<td class="results" ><strong><font color="red"> New Experiment Name:</font></strong>
	
		<?php
			echo "$expNameVal";
		?>
		</td>
		</tr>
<?php
		}
?>

		<tr>
		<td class="questionparameter" ><strong>Experiment Description:</strong></td>
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
		echo $_POST[info];
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
				$insertSQL = "INSERT agilent_experimentsdesc(expid, expname,descrip, ownerid) VALUES($expID, \"$expDescName\",\"$expDescValue\",$userid)";
				$insertResult = $db->Execute($insertSQL);//($insertSQL, $db);

				if(!insertResult){
					echo "<strong>Database Error inserting.  SQL: $insertSQL</strong><br>";
				}else{
?>
<script type="text/javascript">
					alert("You've successfully created your experiment.")
window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>';;
</script>
<?php
				}
			}
			if($expDescChanged == true || $expNameChanged == true){
				$updateSQL= "UPDATE agilent_experimentsdesc SET expname = \"$_POST[expNameValue]\", descrip = \"$_POST[expDescValue]\" WHERE expid = $_POST[expID]";
				//echo "$updateSQL<hr>";
				$sqlResult = $db->Execute($updateSQL);//mysql_query($updateSQL, $db);
				if(!sqlResult){
					echo "<strong>Database Error updating table. SQL: $updateSQL </strong><br>";
				}else{

?>

<script type="text/javascript">
alert("You've successfully updated the description of your experiment.")
window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>';

</script>

<?php				}

			}



		}
	}
}
?>
				