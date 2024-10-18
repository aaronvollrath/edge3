<?php
/***
Location: /edge2/admin
Description:  This page is kind of a monolithic conglomeration of forms.  This page should probably be subdivided, meaning
		that each one of the forms should be a separate page.  Though, please not that this works as it is....
POST:
	FORM NAME: "updatechem" ACTION: "updatevals.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to delete or add a new chemical.
	ELEMENTS: 'chemsubmit', 'chemical' <radio>, 'newchemical', 'type' <radio>, 'newclass[classnumber]' <checkbox>, 'addinfo', 'submit'

	FORM NAME: "updateclass" ACTION: "updatevals.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to delete or add a new chemical class.
	ELEMENTS: 'classsubmit', 'class' <radio>, 'newclass', 'submit'

	FORM NAME: "updateorganism" ACTION: "updatevals.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to delete or add a new chemical class.
	ELEMENTS: 'organismsubmit', 'organism' <radio>, 'neworganism', 'addinfo', 'submit'

	FORM NAME: "updatestrains" ACTION: "updatevals.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to delete or add a new strain.
	ELEMENTS: 'strainsubmit', 'strain' <radio>, 'newstrain', 'addinfo', 'submit'

	FORM NAME: "updategenevariation" ACTION: "updatevals.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to delete or add a new genetic background.
	ELEMENTS: 'genevariationsubmit', 'genevariation' <radio>, 'newgenevariation', 'addinfo', 'submit'

	FORM NAME: "updatetissue" ACTION: "updatevals.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to delete or add a new tissue.
	ELEMENTS: 'tissuesubmit', 'tissue' <radio>, 'newtissue', 'addinfo', 'submit'

	FORM NAME: "updatevehicle" ACTION: "updatevals.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to delete or add a new vehicle.
	ELEMENTS: 'vehiclesubmit', 'vehicle' <radio>, 'newvehicle', 'addinfo', 'submit'

	FORM NAME: "updatedoseunits" ACTION: "updatevals.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to delete or add a new dose unit.
	ELEMENTS: 'doseunitsubmit', 'doseunit' <radio>, 'newdoseunit', 'addinfo', 'submit'

	FORM NAME: "updateroute" ACTION: "updatevals.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to delete or add a new strain.
	ELEMENTS: 'routesubmit', 'route' <radio>, 'newroute', 'addinfo', 'submit'

	FORM NAME: "updatedurationunits" ACTION: "updatevals.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to delete or add a new duration unit.
	ELEMENTS: 'durationunitsubmit', 'durationunit' <radio>, 'newdurationunit', 'addinfo', 'submit'

	FORM NAME: "updatecontrol" ACTION: "updatevals.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to delete or add a new control.
	ELEMENTS: 'controlsubmit', 'control' <radio>, 'newcontrol', 'addinfo', 'submit'

GET: none
Files include or required: 'edge_db_connect2.php', 'fileupload-class.php', 'newtreatmentcheck.inc', 'edge_update_user_activity.inc', 'adminmenu.inc','../adminleftmenu.inc'
***/



require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}

$db = mysql_connect("localhost", "vollrath", "arod678cbc3");

mysql_select_db("edge", $db);
//require("fileupload-class.php");
include 'edge_update_user_activity.inc';

?>

<?php
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
require "newtreatmentcheck.inc";
?>
<body>

	<div class="boxheader">
		<img src="../GIFs/EDGE2128x60.png" alt="Edge^2" align="left"></img>
		<img src="../GIFs/edgebanner.jpg" alt="environment" width="90" height="75" align="left"></img>
		<h2 class="bannerhead" align="bottom">Environment, Drugs and Gene Expression</h2>
	</div>

 <div class="boxmiddle">

 <?php
include 'adminmenu.inc';
?>


	<h3 class="contenthead">Edit Treatment Attributes-> Edit Vehicle Attribute</h3>
<?php
$priv_level = $_SESSION['priv_level'];
if($priv_level != 99){
	echo "Sorry, you are not authorized to access this page.";
}
else{

		if (!isset($_POST['submit'])) { // if form has not been submitted
	//if($submitted != true){
?>

  <?php

		// Get the new sample id....

		// GETTING THE VALUES TO POPULATE THE SELECTIONS.....
		$vehicleSQL = "SELECT vehicleid,vehicle FROM vehicle ORDER BY vehicle ASC";

		$vehicleResult = mysql_query($vehicleSQL, $db);
		$firstchoice = 1;
		$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"0\" checked>No Deletion  ";
		$firstchoice++;
		while(list($vehicleid, $vehicle) = mysql_fetch_array($vehicleResult))
		{
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


<form name="updatevehicle" action="editvehicleattribute.php" method="post" onsubmit="return checkSubmissionForm()">
<input type="hidden" name="vehiclesubmit" value="true">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2"></th>
</tr>
<tr>
<tr>
<td class="questionparameter" ><strong>Delete Vehicle:</strong></td>
<td class="results">
<?php echo $vehicleMenu; ?>
</td>
</tr>
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


<! -- do the actual work in the database here -->
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
			$vehicleResult = mysql_query($vehicleSQL, $db);
			$row = mysql_fetch_row($vehicleResult);
			$delvehicle = $row[0];
			$delvehicleSQL = "DELETE FROM vehicle WHERE vehicleid = $vehicle";
			$delvehicleResult = mysql_query($delvehicleSQL, $db);
			$vehicledeleted = 1;
		}
		if($newvehicle != ""){
			$addvehicleSQL = "INSERT vehicle (vehicle, addinfo) VALUES (\"$newvehicle\", \"$addinfo\")";
			$row = mysql_query($addvehicleSQL, $db);
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
}
?>

 </div>
 <?php
	include '../adminleftmenu.inc';

?>


 <div class="boxclear"> </div>




 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>

</body>
</html>
