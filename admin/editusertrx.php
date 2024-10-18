<?php

/***
Location: /edge2/admin
Description:  This page is used to first select the user treatment to edit and then to display the treatment and
		its associated values so that they can be modified or so the treatment itself can be deleted
		from the database.
POST:
	FORM NAME: "selecttrx" ACTION: "editusertrx.php" METHOD: "post" ONSUBMIT: ""
	FUNCTION: Used to select the treatment to edit.
	ITEMS:  'submitted', 'trx' <radio>

	FORM NAME: "commitchanges" ACTION: "editusertrx.php" METHOD: "post" ONSUBMIT: "return checkChangesForm()"
	FUNCTION: Used to modify (or delete) the treatment.
	ITEMS:  'committed', 'submitted', 'trx', 'userid', 'delete' <radio>,
		'samplename1', 'samplename', 'pubinfo1', 'pubinfo', 'treatment1', 'chemical' <radio>,
		'organism1', 'organism' <radio>, 'rnagroupsize1', 'rnagroupsize', 'strain1', 'strain', 'genevariation1',
		'genevariation' <radio>, 'age1', 'age', 'sex1', 'sex' <radio>, 'tissue1', 'tissue' <radio>, 'vehicle1',
		'vehicle' <radio>, 'dose1', 'dose', 'doseunits1', 'doseunit' <radio>, 'route1', 'route' <radio>,
		'control1', 'control' <radio>, 'dosagetime1', 'dosehours', 'doseminutes', 'harvesttime1', 'harvesthours', 'harvestminutes',
		'duration1', 'duration', 'durationunits1', 'durationunit' <radio>, 'visible' <radio>, 'accessnumber1', 'accessnumber',
		'commit'
GET: none
Files include or required: 'edge_db_connect2.php', 'newtreatmentcheck.inc', 'edge_update_user_activity.inc', 'trxmenus.php', 'adminmenu.inc','../adminleftmenu.inc'
***/
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}

$db = mysql_connect("localhost", "root", "arod678cbc3");

mysql_select_db("edge", $db);
include 'edge_update_user_activity.inc';
require "newtreatmentcheck.inc";
require "fileupload-class.php";
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


	<h3 class="contenthead">Edit/Delete a user treatment</h3>
<?php
$priv_level = $_SESSION['priv_level'];
if($priv_level != 99){
	echo "Sorry, you are not authorized to access this page.";
}
else{
	$submitted = $_POST['submitted'];
	$trx = $_POST['trx'];
	if($submitted != true){ // if the trx selection form has not been submitted....
?>

  <?php

		// Get the treatment id's and treatment names....

		$trxSQL = "SELECT user_sampleid, treatment, tissue, dose, doseunits, duration, durationunits FROM user_sampledata ORDER BY user_sampleid ASC";

		$trxResult = mysql_query($trxSQL, $db);
		$firstchoice = 1;
		$tablerows = "";
		while(list($trx, $trxname, $tissue, $dose, $doseunits, $duration, $durationunits) = mysql_fetch_array($trxResult))
		{
			$tablerows .= "<tr>";
			if($firstchoice == 1){

				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"trx\" value=\"$trx\" 																checked>$trx</td>";
				$tablerows .= "<td class=\"results\">$trxname</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$firstchoice = 0;
			}
			else{
				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"trx\" value=\"$trx\">$trx</td>";
				$tablerows .= "<td class=\"results\">$trxname</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			}
			$tablerows .= "</tr>\n";
		}




?>

<form name="selecttrx" action="editusertrx.php" method="post">
<input type="hidden" name="submitted" value="true">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="5">Edit/Delete Treatment Form</th>
</tr>
</thead>
<tr>
<td class="questionanswer" >Trx ID#</td>
<td class="questionanswer" >Treatment</td>
<td class="questionanswer" >Tissue</td>
<td class="questionanswer" >Dose</td>
<td class="questionanswer" >Duration</td>
</tr>
<?php
// We created the rows above, now insert them into the table...
	//echo "before echo";
	echo $tablerows;
	//echo "after echo";
?>


<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

</table>
</form>
<?php
	}  // end if not submitted....
	else{ // The treatment form has been submitted....

	$committed = $_POST['committed'];
	$trx = $_POST['trx'];
	//$userid = $_POST['userid'];
	$delete = $_POST['delete'];
	$samplename1 = $_POST['samplename1'];
	$samplename = $_POST['samplename'];
	//$pubinfo1 = $_POST['pubinfo1'];
	//$pubinfo = $_POST['pubinfo'];
	$treatment1 = $_POST['treatment1'];
	$chemical = $_POST['chemical'];
	$organism1 = $_POST['organism1'];
	$organism = $_POST['organism'];
	$rnagroupsize1 = $_POST['rnagroupsize1'];
	$rnagroupsize = $_POST['rnagroupsize'];
	$concentration1 = $_POST['concentration1'];
	$concentration = $_POST['concentration'];
	$strain1 = $_POST['strain1'];
	$strain = $_POST['strain'];
	$genevariation1 = $_POST['genevariation1'];
	$genevariation = $_POST['genevariation'];
	$age1 = $_POST['age1'];
	$age = $_POST['age'];
	$sex1 = $_POST['sex1'];
	$sex = $_POST['sex'];
	$pregnant1 = $_POST['pregnant1'];
	$pregnant = $_POST['pregnant'];
	$gestationperiod1 = $_POST['gestationperiod1'];
	$gestationperiod = $_POST['gestationperiod'];
	$tissue1 = $_POST['tissue1'];
	$tissue = $_POST['tissue'];
	$vehicle1 = $_POST['vehicle1'];
	$vehicle = $_POST['vehicle'];
	$dose1 = $_POST['dose1'];
	$dose = $_POST['dose'];
	$doseunits1 = $_POST['doseunits1'];
	$doseunit = $_POST['doseunit'];
	$route1 = $_POST['route1'];
	$route = $_POST['route'];
	$control1 = $_POST['control1'];
	$control = $_POST['control'];
	$dosagetime1 = $_POST['dosagetime1'];
	$dosehours = $_POST['dosehours'];
	$doseminutes = $_POST['doseminutes'];
	$harvesttime1 = $_POST['harvesttime1'];
	$harvesthours = $_POST['harvesthours'];
	$harvestminutes = $_POST['harvestminutes'];
	$duration1 = $_POST['duration1'];
	$duration = $_POST['duration'];
	$durationunits1 = $_POST['durationunits1'];
	$durationunit = $_POST['durationunit'];

	//$visible = $_POST['visible'];
	//$accessnumber1 = $_POST['accessnumber1'];
	//$accessnumber = $_POST['accessnumber'];



		if($committed != "true"){
		// Need to get the values associated w/ this treatment....
		$sql = "SELECT user_samplename,treatment, organism, rnagroupsize, concentration,strain, genevariation, age, sex, pregnant, gestationperiod, tissue, treatment, vehicle, dose, doseunits, route, dosagetime, duration, durationunits, harvesttime, control FROM user_sampledata WHERE user_sampleid = $trx";
		//echo $sql;
		$trxResult = mysql_query($sql, $db);
		list($samplename1, $chemid1, $organism1, $rnagroupsize1, $concentration1, $strain1, $genevariation1, $age1, $sex1, $pregnant1, $gestationperiod1, $tissue1, $treatment1, $vehicle1, $dose1, $doseunits1, $route1, $dosagetime1, $duration1, $durationunits1, $harvesttime1, $control1) = mysql_fetch_array($trxResult);
		/*
		$sql = "SELECT arraydesc, ownerid FROM array WHERE arrayid = $trx";
		$trxResult = mysql_query($sql, $db);
		list($samplename1, $accessnumber1) = mysql_fetch_array($trxResult);

		if($accesnumber > 1){
			$visible = 0;
		}
		else{
			$visible = 1;
		}


		$sql = "SELECT pubinfo FROM sampleinfo WHERE sampleid = $trx";
		$trxResult = mysql_query($sql, $db);
		list($pubinfo1) = mysql_fetch_array($trxResult);
		*/

		// SQL calls for menus.......
		/********************************************************************/
		require("trxmenus.php");
?>
	<form name="commitchanges" action="editusertrx.php" method="post" enctype="multipart/form-data" onsubmit="return checkChangesForm()">
	<input type="hidden" name="committed" value="true">
	<input type="hidden" name="submitted" value="true">
	<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="2">Original Values</th>
		<th class="mainheader" colspan="2">Delete/Change this Treatment?</th>
		</tr>
		<tr>
		<td class="questionparameter" ><strong>Treatment ID:</strong></td>
		<td class="results">

		<?php
		// Get max id to use for next record insertion
			$maxSQL = "Select MAX(sampleid) from sampledata";
			$maxResult = mysql_query($maxSQL, $db);
			$row = mysql_fetch_row($maxResult);
			$maxSampleID = $row[0];
			$newMaxSampleID = $maxSampleID + 1;
		?>
		<input type="hidden" name="trx" value="<?php echo $trx; ?> ">
		<input type="hidden" name="userid" value="<?php echo $userid; ?>">
			<?php echo $newMaxSampleID; ?>
		</td>
		<td bgcolor="red">
			<input type="radio" name="delete" value="N" checked>No
			<input type="radio" name="delete" value="Y">Yes
		</td>
		</tr>


		<tr>
		<td class="questionparameter" ><strong>Sample Name:</strong></td>
		<td class="results">
		<input type="hidden" name="samplename1" value="<?php echo $samplename1; ?>">
		<?php echo $samplename1; ?>
		</td>
		<td class="questionanswertext">
			<input name="samplename" type="text" value="<?php echo $samplename1;?>" align="left"></input><font 						color="red"><strong>*</strong></font>
		</td>
		</tr>

		<!--
		<tr>
		<td class="questionparameter" ><strong>Publication Info:</strong></td>
		<td class="results">
		<input type="hidden" name="pubinfo1" value="<?php echo $pubinfo1; ?>">
		<?php echo $pubinfo1; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="pubinfo" type="text" value="<?php echo $pubinfo1; ?>" align="left"></input>
		</td>
		</tr>
		-->

		<tr>
		<td class="questionparameter" ><strong>Chemical:</strong></td>
		<td class="results">
		<input type="hidden" name="treatment1" value="<?php echo $treatment1; ?>">
		<?php
			echo $treatment1 ;
			// Check if submittedrna.phpname of this chemical appears in chem table... else prompt to add new entry
			$existsSQL = "Select chemid from chem where chemical=\"$treatment1\"";
			$existsResult = mysql_query($existsSQL, $db);
			$row = mysql_fetch_row($existsResult);
			if($row[0]<=0){ ?> <br><font color="red"><strong>New chemical!<br>Please add new entry <br>in 'chem' table</strong></font>
			<?php } ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $chemMenu; ?>
		</td>
		</tr>


		<tr>
		<td class="questionparameter" ><strong>Organism:</strong></td>
		<td class="results">
		<input type="hidden" name="organism1" value="<?php echo $organism1; ?>">
		<?php echo $organism1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $organismMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>RNA Group Size:</strong></td>
		<td class="results">
		<?php echo $rnagroupsize1; ?>
		</td>
		<td  class="questionanswertext" >
			<input type="hidden" name="rnagroupsize1" value="<?php echo $rnagroupsize1; ?>">
			<input name="rnagroupsize" type="text" value="<?php echo $rnagroupsize1; ?>" 						align="right"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Concentration:</strong></td>
		<td class="results">
		<?php echo $concentration1; ?>
		</td>
		<td  class="questionanswertext" >
			<input type="hidden" name="concentration1" value="<?php echo $concentration1; ?>">
			<input name="concentration" type="text" value="<?php echo $concentration1; ?>" 						align="right"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Strain:</strong></td>
		<td class="results">
		<input type="hidden" name="strain1" value="<?php echo $strain1; ?>">
		<?php echo $strain1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $strainMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Genetic Variation:</strong></td>
		<td class="results">
		<input type="hidden" name="genevariation1" value="<?php echo $genevariation1; ?>">
		<?php echo $genevariation1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $genevariationMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Age:</strong></td>
		<td class="results">
		<?php echo $age1; ?>
		</td>
		<td  class="questionanswertext" >
			<input type="hidden" name="age1" value="<?php echo $age1; ?>">
			<input name="age" type="text" value="<?php echo $age1; ?>" align="right"></input><font 								color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Sex:</strong></td>
		<td class="results">
		<input type="hidden" name="sex1" value="<?php echo $sex1; ?>">
		<?php echo $sex1; ?>
		</td>
		<td  class="questionanswertext" >
			<input type="radio" name="sex" value="0" checked>No Change
			<input type="radio" name="sex" value="M">Male
			<input type="radio" name="sex" value="F"> Female
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Pregnant?:</strong></td>
		<td class="results">
		<input type="hidden" name="pregnant1" value="<?php echo $pregnant1; ?>">
		<?php echo $pregnant1; ?>
		</td>
		<td  class="questionanswertext" >
			<input type="radio" name="pregnant" value="0" checked>No Change
			<input type="radio" name="pregnant" value="no">No
			<input type="radio" name="pregnant" value="yes">Yes
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Gestation Period:</strong></td>
		<td class="results">
		<input type="hidden" name="gestationperiod1" value="<?php echo $gestationperiod1; ?>">
		<?php echo "$gestationperiod1"; ?>
		</td>

		<td  class="questionanswertext" >
			<input name="gestationperiod" type="text" value="<?php echo $gestationperiod1; ?>" align="right"></input>
			<font color="blue"><strong>#</strong></font>

		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Tissue:</strong></td>
		<td class="results">
		<input type="hidden" name="tissue1" value="<?php echo $tissue1; ?>">
		<?php echo $tissue1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $tissueMenu; ?>
		</td>
		</tr>


		<tr>
		<td class="questionparameter" ><strong>Vehicle:</strong></td>
		<td class="results">
		<input type="hidden" name="vehicle1" value="<?php echo $vehicle1; ?>">
		<?php echo $vehicle1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $vehicleMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Dose:</strong></td>
		<td class="results">
		<input type="hidden" name="dose1" value="<?php echo $dose1; ?>">
		<?php echo "$dose1"; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="dose" type="text" value="<?php echo $dose1; ?>" align="right"></input><font 							color="blue"><strong>#</strong></font>

		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Dose Units:</strong></td>
		<td class="results">
		<input type="hidden" name="doseunits1" value="<?php echo $doseunits1; ?>">
		<?php echo $doseunits1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $doseunitMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Route:</strong></td>
		<td class="results">
		<input type="hidden" name="route1" value="<?php echo $route1; ?>">
		<?php echo $route1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $routeMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Control:</strong></td>
		<td class="results">
		<input type="hidden" name="control1" value="<?php echo $control1; ?>">
		<?php echo $control1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $controlMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Dosage Time:</strong></td>
		<td class="results">
		<input type="hidden" name="dosagetime1" value="<?php echo "$dosagetime1"; ?>">
		<?php echo "$dosagetime1"; ?>
		</td>
		<?php
			// Get the hours and mins for dosage time from $dosagetime1....
			$pieces = explode(":", $dosagetime1);
			$hours = $pieces[0];
			$minutes = $pieces[1];
		?>
		<td  class="questionanswertext" >
			<input name="dosehours" type="text" value="<?php echo $hours; ?>" align="right" size="2"></input>:
			<input name="doseminutes" type="text" value="<?php echo $minutes; ?>" align="right" size="2"></input><font color="blue"><strong>#</strong></font><strong><font color="red">&nbsp&nbsp &nbsp&nbsp Note:</font>&nbsp 01:59 = 1:59am  12:00 = noon  23:59 = 11:59pm  00:00 = midnight</strong>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Harvest Time:</strong></td>
		<td class="results">
		<input type="hidden" name="harvesttime1" value="<?php echo "$harvesttime1"; ?>">
		<?php echo "$harvesttime1"; ?>
		</td>
		<?php
			// Get the hours and mins for dosage time from $dosagetime1....
			$pieces = explode(":", $harvesttime1);
			$hours = $pieces[0];
			$minutes = $pieces[1];
		?>
		<td  class="questionanswertext" >
			<input name="harvesthours" type="text" value="<?php echo $hours; ?>" align="right" size="2"></input>:
			<input name="harvestminutes" type="text" value="<?php echo $minutes; ?>" align="right" size="2"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Duration:</strong></td>
		<td class="results">
		<input type="hidden" name="duration1" value="<?php echo "$duration1"; ?>">
		<?php echo "$duration1"; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="duration" type="text" value="<?php echo $duration1; ?>" align="right"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>

		<td class="questionparameter" ><strong>Duration Units:</strong></td>
		<input type="hidden" name="durationunits1" value="<?php echo "$durationunits1"; ?>">
		<td class="results"><?php echo $durationunits1; ?></td>
		<td  class="questionanswertext" >
			<?php echo $durationunitMenu; ?>
			</td>
		</tr>

		<!--
		<tr>
		<td class="questionparameter" ><strong>Visible to Public:</strong></td>
		<td class="results">
		<?php
			$visiblestr = "No";
			if($visible == 1){
				$visiblestr = "Yes";
			}
		?>
		<?php echo "$visiblestr"; ?>
		</td>
		<td class="questionanswertext" >
			<input type="radio" name="visible" value="-1" checked>No Change
			<input type="radio" name="visible" value="1" onchange="changeAccessNumberEditTrx()">Yes
			<input type="radio" name="visible" value="0" onchange="changeAccessNumberEditTrx()"> No
		</td>
		</tr>


		<tr>
		<td class="questionparameter" ><strong>Access Number:</strong></td>
		<td class="results">
		<input type="hidden" name="accessnumber1" value="<?php echo $accessnumber1; ?>">
		<?php echo $accessnumber1; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="accessnumber" type="text" value="<?php echo $accessnumber1; ?>" align="right"></input><font color="blue"><strong>#</strong>&nbsp&nbsp<font color="red"><strong>Change valid only if Visible to Public value is changed!</strong></font>
		</td>
		</tr>
		-->

		<tr>
		<td class="questionparameter" ><strong>Data File:</strong></td>
		<td class="results">
		<input name="file" type="file"><font color="red"></font>
		</td>
		</tr>

	</table>
	<tr>
	<?php
		echo "<td><input type=\"submit\" name=\"commit\" value=\"Commit changes to Database?\"></td>";
	?>
		<td></td>
	<td><INPUT TYPE="button" VALUE="Do nothing!" onClick="history.go(-1)"></td>
	</tr>

	</form>
<?php
	}else{ // has been committed....


		// upload the file into the server... then add entries to the HYBRIDS table
		$my_uploader = new uploader('en'); // errors in English

		$my_uploader->max_filesize(30000000);
		$my_uploader->upload('file', '', '.txt');
		$my_uploader->save_file('/var/www/html/edge2/IMAGES/', 2);

		if ($my_uploader->error) {
			$fileError = 1;
			$fileErrorText = $my_uploader->error;
			print($my_uploader->error . "<br><br>\n");
		} else {
			//print("Thanks for uploading " . $my_uploader->file['name'] . "<br><br>\n");
			$filename = $my_uploader->file['name'];
		}

		/*
		$datafile = "/var/www/html/edge2/IMAGES/$file";
		$sql = "LOAD DATA LOCAL INFILE \"$datafile\" INTO TABLE hybrids";
		$insertDataResult = mysql_query($sql, $db);
		$affectedrows = mysql_affected_rows();
		$trxfiledata = "";
		$pgemdata = "";
		echo "<br>";
		if($affectedrow==-1){
			$trxfiledata .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
			$trxfiledata .=  "<strong><font color=\"red\">".mysql_errno($db) . ": " . mysql_error($db). "</font></strong>\n";
			$trxfiledata .=  "<br>heres sql:  <br>";
			$trxfiledata .=  "$sql";
		}
		else{
			//echo "$affectedrows of data were added to the database<br>";
			$trxfiledata .= "<strong><font color=\"green\">Successful!</font><br>";
			$sql = "DELETE FROM hybrids WHERE cloneid = 1171 or cloneid = 1172 or cloneid = 1173 or cloneid = 1195 or cloneid = 1196 or cloneid = 1197 or cloneid = 1219 or cloneid = 1220 or cloneid = 1221 or cloneid = 1243 or cloneid = 1244 or cloneid = 1245 or cloneid = 1267 or cloneid = 1268 or cloneid = 1269 or cloneid = 1291 or cloneid = 1292 or cloneid = 1293 or cloneid = 1315 or cloneid = 1316 or cloneid = 1317 or cloneid = 1339 or cloneid = 1340 or cloneid = 1341 or cloneid = 1363 or cloneid = 1364 or cloneid = 1365 or cloneid = 1387 or cloneid = 1388 or cloneid = 1389 or cloneid = 1411 or cloneid = 1412 or cloneid = 1413 or cloneid = 1435 or cloneid = 1436 or cloneid = 1437";
			$deletePGEMResult = mysql_query($sql, $db);
			if(mysql_errno($db)){
				$pgemdata .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
				$pgemdata .= "<strong><font color=\"red\">".mysql_errno($db) . ": " . mysql_error($db). "</font></strong>\n";
				$pgemdata .= "<br>heres sql:  <br>";
				$pgemdata .= "$sql";
			}
			else{
			$affectedrows = mysql_affected_rows();
			//echo "<strong><font color=\"green\">$affectedrows PGEM clones where removed from the database....</font><br>";
			$pgemdata .= "<strong><font color=\"green\">Successful!</font><br>";
			//echo "<strong><font color=\"red\">This treatment data file has been committed to the database....</font><br>";
			}
		}

		*/



  		// Need to determine whether or not anything has been changed.....
		if($delete == "Y"){
			$sql = "DELETE FROM user_sampledata WHERE user_sampleid = $trx";
			$delResult = mysql_query($sql, $db);
			/*
			$sql = "DELETE FROM sampleinfo WHERE sampleid = $trx";
			$delResult = mysql_query($sql, $db);
			$sql = "DELETE FROM array WHERE arrayid = $trx";
			$delResult = mysql_query($sql, $db);
			$sql = "DELETE FROM hybrids WHERE arrayid = $trx";
			$delResult = mysql_query($sql, $db);
			*/
		}
		else{
			// This is used to hold the changes made to the user_sampledata table for this trx id
			$sdchangesarray = array();
			// This is used to hold the changes made to the sampleinfo table for this trx id
			//$sichangesarray = array();
			// This is used to hold the changes made to the sampleinfo table for this trx id
			//$arraychangesarray = array();
			// This is used to hold the changes made to the sampleinfo table for this trx id
			//$hybchangesarray = array();
			$changesstr = "";

			/*
			if(strcmp($pubinfo1,$pubinfo) != 0){
				// need to update the pub info
				$changesstr .= "Publication info changed from <font color=\"green\"><strong>$pubinfo1</strong></font> to <font 					color=\"red\"><strong>$pubinfo</strong></font> <br>";
				$sql = "pubinfo = \"$pubinfo\"";
				array_push($sichangesarray, $sql);
			}
			*/

				// Get the chemid, look up and assign the new treatment...
				if($chemical != 0){
				$chemSQL = "SELECT chemical FROM chem WHERE chemid=$chemical";
				$chemResult = mysql_query($chemSQL, $db);
				$row = mysql_fetch_row($chemResult);
				$treatment = $row[0];
				if(strcmp($treatment1, $treatment) != 0){
					$changesstr .= "Treatment changed from <font color=\"green\"><strong>$treatment1</strong></font> to <font 							color=\"red\"><strong>$treatment</strong></font> <br>";
					$chemidsql = "chemid = $chemical";
					$treatmentsql = "treatment = \"$treatment\"";
					//array_push($sdchangesarray, $chemidsql, $treatmentsql);
					array_push($sdchangesarray, $treatmentsql);
				}
				}



   // CHECKING TO SEE IF THE CLASSES HAVE CHANGED.....
			// First loop through and see what previous classes were checked....
			/*
			$prevcount = 0;
			$prevclassArray = array();
			foreach($_POST as $postval=>$item){
				echo "$postval = $item<br>";
			}
			while($prevcount < $prevclasscount){
				// Get the value.....
				$prevclass = "prevclass$prevcount";
				//echo "prevclass = $prevclass<br>";
				$prevclassToAdd = $_POST[$prevclass];
				array_push($prevclassArray, $prevclassToAdd);
				//echo "Here's prev class...: $prevclassToAdd";
				$prevcount++;
			}
			*/

			// Are the new values for classes different than the previous values....
			//
			/*
			$classSQL = "SELECT classid, name from class ORDER BY classid ASC";
			$classResult = mysql_query($classSQL, $db);
			// For each class, need to check to see if it is different than the other classes....
			$newclassArray = array();
			$newclassNames = array();
			while($row = mysql_fetch_row($classResult)){
				$newid = $row[0];
				$newclassid = "newclass$newid";
				$newclassvalToCheck = $_POST[$newclassid];
				if($newclassvalToCheck != ""){
					// add to the new class array...
					array_push($newclassArray, $newclassvalToCheck);
					array_push($newclassNames, $row[1]);
				}
			}
			*/

			// At this point, user treatment values may have been updated. Save this updated treatment in sampledata and delete the
			// same from user_sampledata after attaching datafile, to indicate that treatment has been completed.
			// $insertstr contains the concatenation of fields of the record

			/*
			$insertstr = "insert user_sampledata(user_sampleid, treatment, organism, rnagroupsize, strain, genevariation, age, sex, 					pregnant, gestationperiod, tissue, vehicle, dose, doseunits, route, control, dosagetime, harvesttime, 						duration, durationunits) values(";

				// First create a new entry for this sample in the sampleinfo table

			$maxSQL = "Select MAX(user_sampleid) from user_sampledata";
			$maxResult = mysql_query($maxSQL, $db);
			$row = mysql_fetch_row($maxResult);
			$maxSampleID = $row[0];
			$newMaxSampleID = $maxSampleID + 1;

			$insertstr .= $newMaxSampleID.", ";
			$insertstr .= $treatment. ", ";
			*/

			if($organism != 0){

				// Get the respective id, look up and assign the new value...
				$sql = "SELECT organism FROM organism WHERE organismid=$organism";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				if(strcmp($organism1, $name) != 0){
					$changesstr .= "Organism changed from <font color=\"green\"><strong>$organism1</strong></font> to <font 					color=\"red\"><strong>$name</strong></font> <br>";
					$organismsql = "organism = \"$name\"";
					//$insertstr .= "\"$name\"".", ";
					array_push($sdchangesarray, $organismsql);
				}
			}

			if(strcmp($samplename1, $samplename) != 0){
				$changesstr .= "Samplename changed from <font color=\"green\"><strong>$samplename1</strong></font> to <font 					color=\"red\"><strong>$samplename</strong></font> <br>";
				$samplesql = "user_samplename = \"$samplename\"";
				array_push($sdchangesarray, $samplesql);
			}
			if(strcmp($rnagroupsize1, $rnagroupsize) != 0){
				$changesstr .= "RNA Group Size changed from <font color=\"green\"><strong>$rnagroupsize1</strong></font> to <font 				color=\"red\"><strong>$rnagroupsize</strong></font> <br>";
				$rnasql = "rnagroupsize = $rnagroupsize";
				//$insertstr .= "$rnagroupsize".", ";
				array_push($sdchangesarray, $rnasql);
			}

			if(strcmp($concentration1, $concentration) != 0){
				$concentrationsql = "concentration = $concentration";
				$changesstr .= "Concentration changed from <font color=\"green\"><strong>$concentration1</strong></font> to <font 						color=\"red\"><strong>$concentration</strong></font> <br>";
				//$insertstr .= "$dose". ", ";
				array_push($sdchangesarray, $concentrationsql);
			}

			if($strain != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT strain FROM strain WHERE strainid=$strain";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				if(strcmp($strain1, $name) != 0){
					$changesstr .= "Strain changed from <font color=\"green\"><strong>$strain1</strong></font> to 							<font color=\"red\"><strong>$name</strong></font> <br>";
					$strainsql = "strain = \"$name\"";
					//$insertstr .= "\"$name\"". ", ";
					array_push($sdchangesarray, $strainsql);
				}
			}
			if($genevariation != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT genevariation FROM genevariation WHERE genevariationid=$genevariation";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				if(strcmp($genevariation1, $name) != 0){
					$changesstr .= "Gene Variation changed from <font color=\"green\"><strong>$genevariation1</strong></font> 					to <font color=\"red\"><strong>$name</strong></font> <br>";
					$genevariationsql = "genevariation = \"$name\"";
					//$insertstr .= "\"$name\"". ", ";
					array_push($sdchangesarray, $genevariationsql);
				}
			}
			if(strcmp($age1, $age) != 0){
				$agesql = "age = $age";
				$changesstr .= "Age changed from <font color=\"green\"><strong>$age1</strong></font> to <font 							color=\"red\"><strong>$age</strong></font> <br>";
				//$insertstr .= "$age". ", ";
				array_push($sdchangesarray, $agesql);
			}
			if(strcmp($sex1, $sex) != 0){
			if($sex == "M" || $sex == "F"){
				$sexsql = "sex = '$sex'";
				$changesstr .= "Sex changed from <font color=\"green\"><strong>$sex1</strong></font> to <font 							color=\"red\"><strong>$sex</strong></font> <br>";
				//$insertstr .= "\"$sex\"". ", ";
				array_push($sdchangesarray, $sexsql);
			}
			}
			if(strcmp($pregnant1, $pregnant) != 0){
			if($pregnant == "yes" || $pregnant == "no"){
				$pregnantsql = "pregnant = '$pregnant'";
				$changesstr .= "Pregnant changed from <font color=\"green\"><strong>$pregnant1</strong></font> to <font 					color=\"red\"><strong>$pregnant</strong></font> <br>";
				//$insertstr .= "\"$pregnant\"". ", ";
				array_push($sdchangesarray, $pregnantsql);
			}
			}
			if(strcmp($gestationperiod1, $gestationperiod) != 0){
				if(!($gestationperiod>=1)) $gestationperiod = 0;
				$gestationsql = "gestationperiod = $gestationperiod";
				$changesstr .= "Gestation period changed from <font color=\"green\"><strong>$gestationperiod1</strong></font> to 				<font color=\"red\"><strong>$gestationperiod</strong></font> <br>";
				//$insertstr .= "$gestationperiod". ", ";
				array_push($sdchangesarray, $gestationsql);
			}
			if($tissue != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT tissue FROM tissue WHERE tissueid=$tissue";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				if(strcmp($tissue1, $name) != 0){
					$changesstr .= "Tissue changed from <font color=\"green\"><strong>$tissue1</strong></font> to <font 						color=\"red\"><strong>$name</strong></font> <br>";
					$tissuesql = "tissue = \"$name\"";
				//$insertstr .= "\"$name\"" . ", ";
					array_push($sdchangesarray, $tissuesql);
				}
			}
			if($vehicle != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT vehicle FROM vehicle WHERE vehicleid=$vehicle";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				if(strcmp($vehicle1, $name) != 0){
					$changesstr .= "Vehicle changed from <font color=\"green\"><strong>$vehicle1</strong></font> to <font 						color=\"red\"><strong>$name</strong></font> <br>";
					$vehiclesql = "vehicle = \"$name\"";
					//$insertstr .= "\"$name\"". ", ";
					array_push($sdchangesarray, $vehiclesql);
				}
			}
			if(strcmp($dose1, $dose) != 0){
				$dosesql = "dose = $dose";
				$changesstr .= "Dose changed from <font color=\"green\"><strong>$dose1</strong></font> to <font 						color=\"red\"><strong>$dose</strong></font> <br>";
				//$insertstr .= "$dose". ", ";
				array_push($sdchangesarray, $dosesql);
			}
			if($doseunit != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT doseunit FROM doseunit WHERE doseunitid=$doseunit";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				if(strcmp($doseunits1, $name) != 0){
					$changesstr .= "Dose Unit changed from <font color=\"green\"><strong>$doseunits1</strong></font> to <font 					color=\"red\"><strong>$name</strong></font> <br>";
					$doseunitssql = "doseunits = \"$name\"";
					//$insertstr .= "\"$name\"". ", ";
					array_push($sdchangesarray, $doseunitssql);
				}
			}
			if($route != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT route FROM route WHERE routeid=$route";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				if(strcmp($route1, $name) != 0){
					$changesstr .= "Route changed from <font color=\"green\"><strong>$route1</strong></font> to <font 						color=\"red\"><strong>$name</strong></font> <br>";
					$routesql = "route = \"$name\"";
					//$insertstr .= "\"$name\"". ", ";
					array_push($sdchangesarray, $routesql);
				}
			}
			if($control != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT control FROM control WHERE controlid=$control";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				if(strcmp($control1, $name) != 0){
					$changesstr .= "Control changed from <font color=\"green\"><strong>$control1</strong></font> to <font 						color=\"red\"><strong>$name</strong></font> <br>";
					$controlsql = "control = \"$name\"";
					//$insertstr .= "\"$name\"". ", ";
					array_push($sdchangesarray, $controlsql);
				}
			}
			$dosagetime = "$dosehours:$doseminutes:00";
			$harvesttime = "$harvesthours:$harvestminutes:00";
			if(strcmp($dosagetime1, $dosagetime) != 0){
				$dosagetimesql = "dosagetime = \"$dosagetime\"";
				$changesstr .= "Dosage Time changed from <font color=\"green\"><strong>$dosagetime1</strong></font> to <font 					color=\"red\"><strong>$dosagetime</strong></font> <br>";
				//$insertstr .= "$dosagetime". ", ";
				array_push($sdchangesarray, $dosagetimesql);
			}
			if(strcmp($harvesttime1, $harvesttime) != 0){
				$harvesttimesql = "harvesttime = \"$harvesttime\"";
				$changesstr .= "Harvest Time changed from <font color=\"green\"><strong>$harvesttime1</strong></font> to <font 					color=\"red\"><strong>$harvesttime</strong></font> <br>";
				//$insertstr .= "\"$harvesttime\"". ", ";
				array_push($sdchangesarray, $harvesttimesql);
			}
			if(strcmp($duration1, $duration) != 0){
				$changesstr .= "Duration changed from <font color=\"green\"><strong>$duration1</strong></font> to <font 					color=\"red\"><strong>$duration</strong></font> <br>";
				$durationsql = "duration = $duration";
				//$insertstr .= "$duration". ", ";
				array_push($sdchangesarray, $durationsql);
			}
			if($durationunit != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT durationunit FROM durationunit WHERE durationunitid=$durationunit";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				if(strcmp($durationunits1, $name) != 0){
					$changesstr .= "Duration Unit changed from <font color=\"green\"><strong>$durationunits1</strong></font> to <font 				color=\"red\"><strong>$name</strong></font> <br>";
					$durationunitsql = "durationunits = \"$name\"";
					//$insertstr .= "\"$name\"";
					array_push($sdchangesarray, $durationunitsql);
				}
			}

			// insert the closing bracket to insertstr
			//$insertstr .= ")";
			//echo $insertstr;
			/*
			if($visible != -1){
				// Only change acceess number if visible has been changed!!!
				if(strcmp($accessnumber1, $accessnumber) != 0){
				$changesstr .= "Access Number changed from <font color=\"green\"><strong>$accessnumber1</strong></font> to <font 				color=\"red\"><strong>$accessnumber</strong></font> <br>";
				$sql = "ownerid = $accessnumber";
				array_push($arraychangesarray, $sql);
			}
			}
			*/



			/*
			$sql = "INSERT sampleinfo(sampleid, samplename) VALUES ($newMaxSampleID, \"$samplename\")";
			$newSampleResult = mysql_query($sql,$db);

			if(mysql_errno($db)){
			echo "<strong>Database Error adding new sample to sampleinfo table </strong><br>";
			}
			*/
			/*
			// Check if any numeric field was empty... if so, set to "" to avoid db error.
			if($gestationperiod=="") $gestationperiod = "0";
			if($rnagroupsize=="") $rnagroupsize="0";
			if($age=="") $age="0";
			if($dose=="") $dose="0";
			if($duration=="") $duration="0";
			// Now create new entry in sampledata table for this sample
			//echo "$organism. $rnagroupsize. $organism";
			$newSQL = "INSERT user_sampledata(user_sampleid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, 					treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, 				gestationperiod)
			VALUES ($newMaxSampleID, \"$organism\", $rnagroupsize, \"$strain\", \"$genevariation\", $age, \"$sex\", \"$tissue\", 					\"$treatment\", \"$vehicle\", $dose, \"$route\", \"$dosagetime\", $duration, \"$harvesttime\", \"$control\", 					\"$doseunits\", \"$durationunits\", \"$pregnant\", $gestationperiod)";
			//echo $newSQL;
			$newResult = mysql_query($newSQL, $db);
			if(mysql_errno($db)){
			echo "<strong>Database Error adding new sample to user_sampledata table </strong><br>";
			}
			*/


			// Update values in user_sampledata
			if(count($sdchangesarray) > 0){

				if(count($sdchangesarray) > 1){
					$sdchanges = implode(",", $sdchangesarray);
				}
				else{
					$sdchanges = $sdchangesarray[0];
				}
				$sql = "UPDATE user_sampledata SET $sdchanges WHERE user_sampleid = $trx";
				//echo $sql;
				$result = mysql_query($sql, $db);
				if(mysql_errno($db)){
					echo "Database error trying to update user_sampledata table";
				}
			}

			// Now delete the datafile from the IMAGES directory since already in database
			$command = "rm -f $datafile";
			$str=exec($command);

			// Now add a new record for this in the sampledata table, before deleting from user_sampledata...
			$user_samplename1 = null;

			$sql = "SELECT organism, rnagroupsize, concentration, strain, genevariation, age, sex, tissue, treatment,
			vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, gestationperiod,
			user_samplename FROM user_sampledata WHERE user_sampleid = $trx";
			$trxResult = mysql_query($sql, $db);
			list($organism1, $rnagroupsize1, $concentration1, $strain1, $genevariation1, $age1, $sex1, $tissue1, $treatment1, $vehicle1, $dose1, $route1, $dosagetime1, $duration1, $harvesttime1, $control1, $doseunits1, $durationunits1, $pregnant1, $gestationperiod1, $samplename1) = mysql_fetch_array($trxResult);

			// Get max id to use for next record insertion
			$maxSQL = "Select MAX(sampleid) from sampledata";
			$maxResult = mysql_query($maxSQL, $db);
			$row = mysql_fetch_row($maxResult);
			$maxSampleID = $row[0];
			$newMaxSampleID = $maxSampleID + 1;
			/*
			$newSQL = "INSERT user_sampledata(user_sampleid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, 					treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, 				gestationperiod, user_samplename)
			VALUES ($newMaxSampleID, \"$organism1\", $rnagroupsize1, \"$strain1\", \"$genevariation1\", $age1, \"$sex1\", 					\"$tissue1\", \"$treatment1\", \"$vehicle1\", $dose1, \"$route1\", \"$dosagetime1\", $duration1, \"$harvesttime1\", 				\"$control1\",\"$doseunits1\", \"$durationunits1\", \"$pregnant1\", $gestationperiod1, \"$samplename1\")";
			*/

			// get chemid... if not in table, set to 0
			$chemsql = "SELECT chemid from chem where chemical = \"$treatment1\"";
			$chemresult = mysql_query($chemsql, $db);
			$chemrow = mysql_fetch_row($chemresult);
			$chemid1 = $chemrow[0];
			if(!($chemid1>=1)){
				$chemid1 = 0;
			}

			// insert this user treatment into the main sampledata table
			$newSQL = "INSERT sampledata(sampleid, chemid, organism, rnagroupsize, concentration, strain, genevariation, age, sex, tissue, 					treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, 				gestationperiod)
			VALUES ($newMaxSampleID, $chemid1, \"$organism1\", $rnagroupsize1, $concentration1, \"$strain1\", \"$genevariation1\", $age1, \"$sex1\", 					\"$tissue1\", \"$treatment1\", \"$vehicle1\", $dose1, \"$route1\", \"$dosagetime1\", $duration1, 						\"$harvesttime1\", \"$control1\",\"$doseunits1\", \"$durationunits1\", \"$pregnant1\", 								$gestationperiod1)";
			$newResult = mysql_query($newSQL, $db);
			if(mysql_errno($db)){
			echo "<strong>Database Error adding new sample to sampledata table </strong><br>";
			}

			// Previously, we were simply deleting this user treatment from user_sampledata table
			// Now instead, we are retaining this record with the processed field set to 1 to indicate that
			// the treatment has already been processed/viewed. Note that when we commit these changes, a new 			// record is automatically created in the main sampledata table. Hence no need to manually enter 			// this data as was being done previously

			$updatesql = "update user_sampledata set processed=1 where user_sampleid = $trx";
			$result = mysql_query($updatesql, $db);
			if(mysql_errno($db)){
			echo "<strong>Database Error updating sample from user_sampledata table </strong><br>";
			}

			// now add entry into sampleinfo about new user treatment
			$samplesql = "insert sampleinfo(sampleid, samplename) values ($newMaxSampleID, \"$samplename\")";
			$sampleresult = mysql_query($samplesql, $db);
			if(mysql_errno($db)){
			echo "<strong>Database Error adding new sample to sampleinfo table </strong><br>";
			}

			// now add new entry to array table as well
			$arraysql = "insert array(arrayid, sampleid, arraydesc, versionid) values ($newMaxSampleID, $newMaxSampleID, \"$samplename\", 1)";
			$sampleresult = mysql_query($arraysql, $db);
			if(mysql_errno($db)){
			echo "<strong>Database Error adding new sample to array table </strong><br>";
			}




			/*
			if(count($sichangesarray) > 0){

				if(count($sichangesarray) > 1){
					$sichanges = implode(",", $sichangesarray);
				}
				else{
					$sichanges = $sichangesarray[0];
				}
				$sql = "UPDATE sampleinfo SET $sichanges WHERE sampleid = $trx";
				$result = mysql_query($sql, $db);
				//echo "<h3>$sql</h3>";
			}
			if(count($arraychangesarray) > 0){

				if(count($arraychangesarray) > 1){
					$arraychanges = implode(",", $arraychangesarray);
				}
				else{
					$arraychanges = $arraychangesarray[0];
				}
				$sql = "UPDATE array SET $arraychanges WHERE arrayid = $trx";
				$result = mysql_query($sql, $db);
				//echo "<h3>$sql</h3>";
			}
			*/
		// load the data file into the hybrids table
		$datafile = "/var/www/html/edge2/IMAGES/$filename";
		$sql = "LOAD DATA LOCAL INFILE \"$datafile\" INTO TABLE hybrids";
		echo "<br> $sql <br>";
		$insertDataResult = mysql_query($sql, $db);
		$affectedrows = mysql_affected_rows();
		$trxfiledata = "";
		$pgemdata = "";
		echo "<br>";
		if($affectedrow==-1){
			$trxfiledata .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
			$trxfiledata .=  "<strong><font color=\"red\">".mysql_errno($db) . ": " . mysql_error($db). "</font></strong>\n";
			$trxfiledata .=  "<br>heres sql:  <br>";
			$trxfiledata .=  "$sql";
		}
		else{
			//echo "$affectedrows of data were added to the database<br>";
			$trxfiledata .= "<strong><font color=\"green\">Successful!</font><br>";
			$sql = "DELETE FROM hybrids WHERE cloneid = 1171 or cloneid = 1172 or cloneid = 1173 or cloneid = 1195 or cloneid = 1196 or cloneid = 1197 or cloneid = 1219 or cloneid = 1220 or cloneid = 1221 or cloneid = 1243 or cloneid = 1244 or cloneid = 1245 or cloneid = 1267 or cloneid = 1268 or cloneid = 1269 or cloneid = 1291 or cloneid = 1292 or cloneid = 1293 or cloneid = 1315 or cloneid = 1316 or cloneid = 1317 or cloneid = 1339 or cloneid = 1340 or cloneid = 1341 or cloneid = 1363 or cloneid = 1364 or cloneid = 1365 or cloneid = 1387 or cloneid = 1388 or cloneid = 1389 or cloneid = 1411 or cloneid = 1412 or cloneid = 1413 or cloneid = 1435 or cloneid = 1436 or cloneid = 1437";
			echo "pgem sql<br> $sql <br>";
			$deletePGEMResult = mysql_query($sql, $db);
			if(mysql_errno($db)){
				$pgemdata .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
				$pgemdata .= "<strong><font color=\"red\">".mysql_errno($db) . ": " . mysql_error($db). "</font></strong>\n";
				$pgemdata .= "<br>heres sql:  <br>";
				$pgemdata .= "$sql";
			}
			else{
			$affectedrows = mysql_affected_rows();
			//echo "<strong><font color=\"green\">$affectedrows PGEM clones where removed from the database....</font><br>";
			$pgemdata .= "<strong><font color=\"green\">Successful!</font><br>";
			echo "PGEM deletion successful...<br>";
			//echo "<strong><font color=\"red\">This treatment data file has been committed to the database....</font><br>";
			}
		}




		}

?>
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="2">Treatment Update Results</th>
		</tr>
		<?php 
			if($delete=="Y"){
		?>
			<tr>
			<td class="questionparameter" ><strong>Treatment Info Deleted?:</strong></td>
			<td class="results">
				<font color="green"><strong>Deleted Treatment #<?php echo $trx; ?></strong></font>
			</td>
			</tr>
		<?php
			}
			else{
		?>
			<tr>
			<td class="questionparameter" ><strong>Treatment Info Updated for #<?php echo $trx;?>:</strong></td>
			<td class="results">
			<?php 
			if(strlen($changesstr) > 0){
				echo $changesstr;
			}
			else{
				echo "<font color=\"red\"><strong>No changes made.</strong</font>";
			}
			?>
			</td>
			</tr>

		<?php
			}
		?>
		</table>
<?php
	}
	// Now delete datafile from disk since we have saved it onto database
		$command = "rm -f $datafile";
		$str=exec($command);


}// end of initial submit....
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
