<?php
/***
Location: /edge2/admin
Description:  This script allows for the editing of chemical attributes
***/
require 'edge_db_connect2.php';
#$db = mysql_connect("localhost", "root", "arod678cbc3");
#mysql_select_db("edge", $db);

// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}

include('../utilityfunctions.inc');

if(isset($_POST['submit'])){
	$vehiclesubmit = $_POST['vehiclesubmit'];
	$vehicle = $_POST['vehicle'];
	$newvehicle = $_POST['newvehicle'];
	$addinfo = $_POST['addinfo'];
	$submit = $_POST['submit'];
}



echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="../css/newlayout.css" title="layout" />
<title>EDGE^2</title>
</head>
<?php
//require "newtreatmentcheck.inc";
?>
<body>

	

 <div class="boxmiddle">


	<h3 class="contenthead">Edit Vehicle Attribute</h3>
<?php
$priv_level = $_SESSION['priv_level'];


		if (!isset($_POST['submit'])) { // if form has not been submitted
	//if($submitted != true){
?>

  <?php

		// Get the new sample id....
		$vehicleMenu="";
		// GETTING THE VALUES TO POPULATE THE SELECTIONS.....
		$vehicleSQL = "SELECT vehicleid,vehicle FROM vehicle ORDER BY vehicle ASC";

		$vehicleResult = $db->Execute($vehicleSQL);//mysql_query($vehicleSQL, $db);
		$firstchoice = 1;
		$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"0\" checked>No Deletion  ";
		$firstchoice++;
		#while(list($vehicleid, $vehicle) = mysql_fetch_array($vehicleResult))
		while($row = $vehicleResult->FetchRow())
		{
			$vehicleid = $row[0];
			$vehicle = $row[1];
			if($firstchoice%5==0){
				$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicleid\">$vehicle  <br>";
				$firstchoice++;
			}
			else{
				$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicleid\">$vehicle  ";
				$firstchoice++;
			}
		}

?>


<form name="updatevehicle" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkSubmissionForm()">
	<input type="hidden" name="vehiclesubmit" value="true">
	<table class="question" width="400">
	<thead>
	<tr>
	<th class="mainheader" colspan="2"></th>
	</tr>
	</thead>
	<tr><td colspan="2" class="questionparameter"><font color="red"><strong>Instructions: </strong></font>To add a vehicle, enter the Vehicle name in the first text input field, <i><b>Add Vehicle</b></i>.  The last field, <b><i>Add. Info. for Added Vehicle</i></b> allows you to enter additional info and notes about the new vehicle. Any new entries will be reviewed.</td></tr>
	<tr>
<?php

if($priv_level == 99){
?>
	<tr>
	<td class="questionparameter" ><strong>Delete Vehicle:</strong></td>
	<td class="results">
	<?php echo $vehicleMenu; ?>
	</td>
	</tr>
<?php
}

?>
<tr>
<td class="questionparameter" ><strong>Add Vehicle:</strong></td>
<td class="results">
<input name="newvehicle" type="text" align="right"></input>
</td>
</tr>
<tr>

<tr>
<td class="questionparameter" ><strong>Add. Info. for Added Vehicle:</strong></td>
<td class="results">
<input name="addinfo" type="text" align="right" size="40" maxlength="256"></input>
</td>
</tr>
<td><input type="submit" name="submit" value="Submit Vehicle Change"></td>
<td><input type="reset" value="Reset Vehicle Form"></td>
</tr>
</table>
</form>


<!-- do the actual work in the database here -->
<?php
	}  // end if not submitted....
	else{
		// What value is to be updated.....
		if($vehiclesubmit == "true"){
	// Following 2 vars used for determining whether a vehicle has been added or deleted.
		$vehicleadded = 0;
		$vehicledeleted = 0;

		if($vehicle != 0){
			$vehicleSQL = "SELECT vehicle FROM vehicle WHERE vehicleid = $vehicle";
			$vehicleResult = $db->Execute($vehicleSQL);//mysql_query($vehicleSQL, $db);
			$row = $vehicleResult->FetchRow();//mysql_fetch_row($vehicleResult);
			$delvehicle = $row[0];
			$delvehicleSQL = "DELETE FROM vehicle WHERE vehicleid = $vehicle";
			$delvehicleResult = $db->Execute($delvehicleSQL);//mysql_query($delvehicleSQL, $db);
			$vehicledeleted = 1;
		}
		if($newvehicle != ""){
			$addvehicleSQL = "INSERT vehicle (vehicle, addinfo) VALUES (\"$newvehicle\", \"$addinfo\")";
			$row = $db->Execute($addvehicleSQL);//mysql_query($addvehicleSQL, $db);
			$vehicleadded = 1;
		}
		?>


			<table class="question" width="400">
				<thead>
				<tr>
				<th class="mainheader" colspan="2">Vehicle Changes Made</th>
				</tr>
				</thead>
				<tr>
				<td class="questionparameter" ><strong>Vehicle Deleted:</strong></td>
				<td class="results">
<?php
						if($vehicledeleted == 1){
							echo "Deleted $delvehicle";
						}
						else{
							echo "None";
						}
					?>
				</td>
				</tr>
				<td class="questionparameter" ><strong>Vehicle Added:</strong></td>
				<td class="results">
					<?php
						if($vehicleadded == 1){
							echo "Added $newvehicle";
						}
						else{
							echo "None";
						}
					?>
				</td>
				</tr>
			</table>



<?php
	}
	}// end of all submits....
?>

 </div>



 <div class="boxclear"> </div>




 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>

</body>
</html>
