<?php
/***
Location: /edge2/admin
Description:  This page is used to submit information regarding an array.  It assigns an unique trxid (an array id) and then the user can
		define the values for the various attributes and the submit the data file.
POST:
	FORM NAME: "newsample-nonadmin" ACTION: "newsample-nonadmin.php" METHOD: "post" ONSUBMIT: "return checkSubmissionForm()"
	FUNCTION: Used to define the array.
	ITEMS: 'submitted', 'trxid', 'userid', 'samplename', 'pubinfo', 'chemical' <radio>, 'organism' <radio>,
	'rnagroupsize' <radio>, 'strain' <radio>, 'genevariation' <radio>, 'age', 'sex' <radio>,
	'tissue' <radio>, 'vehicle' <radio>, 'doseunit' <radio>, 'route' <radio>, 'dosehours', 'doseminutes',
	'harvesthours', 'harvestminutes', 'duration', 'durationunit' <radio>, 'control' <radio>, 'visible' <radio>,
	'accessnumber', 'file' <data file>, 'submit'

	FORM NAME: "commitnewsample-nonadmin" ACTION: "newsample-nonadmin.php" METHOD: "post" ONSUBMIT: ""
	FUNCTION: Used to confirm the data entered.
	ITEMS: 'submitted', 'trxid', 'userid', 'samplename', 'pubinfo', 'chemical', 'treatment',
		'organism', 'rnagroupsize', 'stain', 'genevariation', 'age', 'sex', 'tissue', 'vehicle', 'dose', 'doseunits', 'route',
		'control', 'dosagetime', 'harvesttime', 'duration', 'durationunits', 'accessnumber', 'file', 'commit'
GET: none
Files include or required: 'edge_db_connect2.php', 'fileupload-class.php', 'newtreatmentcheck.in, 'edge_update_user_activity.inc', 'questionmenu.inc','.leftmenu.inc'
***/


require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}

require './phpinc/edge3_db_connect.inc';
require("fileupload-class.php");
include 'edge_update_user_activity.inc';
include 'utilityfunctions.inc';

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
<script type="text/javascript">

	function sendemail()
	{

	}

	function drawbox($element,$onoff) // is used to enable/disable a textbox based on radio-buttons clicked
	{
		document.getElementById($element).disabled = $onoff;
	}

	//function disableRadioGroup($element, $onoff) to be generized for any element soon
	function disableRadioGroup($ele,$onoff)
	{
		if($onoff=="true"){
			document.getElementById('gestation').disabled = true;
			for(var i=0; i<document.forms[0].pregnant.length; i++){
			document.forms[0].pregnant[i].disabled = true;
			}
		}
		else{
			//document.getElementById('gestation').disabled = false;
			for(var i=0; i<document.forms[0].pregnant.length; i++){
			document.forms[0].pregnant[i].disabled = false;
			}
		}
	}

	function checkForm() // validates form data entered by user
	{
	var samplename = document.forms[0].samplename.value;
	var rnasize = document.forms[0].rnagroupsize.value;
	var age = document.forms[0].age.value;
	var dose = document.forms[0].dose.value;
	var dosetimehrs = document.forms[0].dosehours.value;
	var dosetimemins = document.forms[0].doseminutes.value;
	var harvesthrs = document.forms[0].harvesthours.value;
	var harvestmins = document.forms[0].harvestminutes.value;
	var notPregnant = document.forms[0].gestation.disabled;
	var gestationPeriod = document.forms[0].gestation.value;
	var concentration = document.forms[0].concentration.value;
	var duration = document.forms[0].duration.value;



	//document.write(notPregnant);

	// Validating that gestation period has a non-blank and numeric value when organism is pregnant

	if(notPregnant == false){
		if(isBlank(gestationPeriod)){
			alert("You've left a required field blank!");
			return false;
		}
		if(!isNumber(gestationPeriod)){
			alert("You need to enter a numerical value in one of the fields that require one.  Please rectify this situation.");
			return false;
		}
		if(gestationPeriod<=0){
			alert("Please enter a valid gestation period (>0)");
			return false;
		}
	}

	// Validating all the 'other' input fields... when enabled, text must be filled
	if(document.forms[0].otherChemical.disabled == false && document.forms[0].otherChemical.value== ''){
		alert("You've left a required field blank!");
		return false;
	}

	if(document.forms[0].otherOrganism.disabled == false && document.forms[0].otherOrganism.value== ''){
		alert("You've left a required field blank!");
		return false;
	}

	if(document.forms[0].otherGenevariation.disabled == false && document.forms[0].otherGenevariation.value== ''){
		alert("You've left a required field blank!");
		return false;
	}

	if(document.forms[0].otherTissue.disabled == false && document.forms[0].otherTissue.value== ''){
		alert("You've left a required field blank!");
		return false;
	}

	if(document.forms[0].otherVehicle.disabled == false && document.forms[0].otherVehicle.value== ''){
		alert("You've left a required field blank!");
		return false;
	}

	if(document.forms[0].otherRoute.disabled == false && document.forms[0].otherRoute.value== ''){
		alert("You've left a required field blank!");
		return false;
	}

	if(document.forms[0].otherControl.disabled == false && document.forms[0].otherControl.value== ''){
		alert("You've left a required field blank!");
		return false;
	}

	if(document.forms[0].otherStrain.disabled == false && document.forms[0].otherStrain.value== ''){
		alert("You've left a required field blank!");
		return false;
	}

	if(isBlank(rnasize) || isBlank(age) || isBlank(dose) || isBlank(dosetimehrs) || isBlank(dosetimemins) || 					isBlank(harvesthrs) || isBlank(harvestmins) || isBlank(samplename) || isBlank(concentration)){
		alert("You've left a required field blank!");
		return false;
	}



	if(isNumber(rnasize) && isNumber(age)  && isNumber(dose) && isNumber(dosetimehrs) && isNumber(dosetimemins) && isNumber(harvesthrs) && 		isNumber(harvestmins)  && isNumber(concentration)&& isNumber(duration)){
		if(rnasize > 0 && age > 0 && dose >= 0 && dosetimehrs >= 0 && dosetimemins >= 0 && harvesthrs >= 0 && harvestmins >= 0){
			if(dosetimehrs < 24 && dosetimemins < 60 && harvesthrs < 24 && harvestmins < 60){
				return true;
			}
			else{
				/*
				if(accessnum < 1){
					alert("Please enter a valid access number.  ie. >= 1");
					return false;
				}
				else{
				*/
				//if{
					alert("Please enter a valid time value.  Hours >= 00 and <=23, Minutes >= 00 and <=59");
					return false;
				//}
			}
		}
		else{
			alert("Please enter a valid numerical value in the fields that require one.");
			return false;
		}
	}
	else{
		alert("You need to enter a numerical value in one of the fields that require one.  Please rectify this situation.");
		return false;
	}



	}

	function isBlank(val){ //returns true if value contains only spaces
	//alert("in isBlank..");
	if(val==null){return true;}
	for(var i=0;i<val.length;i++) {
		if ((val.charAt(i)!=' ')&&(val.charAt(i)!="\t")&&(val.charAt(i)!="\n")&&(val.charAt(i)!="\r")){return false;}
		}
	return true;
	}

	function isNumber(num1) { // returns true if num1 is a number
	//alert("in isNumber!!!");
	if ((num1 / 2 >= 0)||(num1 / 2 < 0))
   		return true
  	else
   		return false
	}


</script>
</head>
<body>

	<div class="boxheader">
		<img src="../GIFs/EDGE2128x60.png" alt="Edge^2" align="left"></img>
		<img src="../GIFs/edgebanner.jpg" alt="environment" width="90" height="75" align="left"></img>
		<h2 class="bannerhead" align="bottom">Environment, Drugs and Gene Expression</h2>
	</div>

 <div class="boxmiddle">

 <?php
include 'questionmenu.inc';
?>


	<h3 class="contenthead">Enter a new treatment</h3>
<?php
//$priv_level = $_SESSION['priv_level']; // Commenting out these checks for admin priviledges...
//if($priv_level != 99){
//	echo "Sorry, you are not authorized to access this page.";
//}
//else{

analyze($_POST);
	//if (!isset($_POST['submit'])) { // if form has been submitted
if($submitted != true){






	if($expNew == true){

		// We have to update the `agilent_experimentsdesc` table....
		// add new entry in chem table for this new chemical entered by user
				$insertSQL = "INSERT agilent_experimentsdesc(expid, expname,descrip) VALUES($expID, \"$expDescName\",\"$expDescValue\")";
				$insertResult = mysql_query($insertSQL, $db);

				if(mysql_errno($db)){
				echo "<strong>Database Error inserting.  SQL: $insertSQL</strong><br>";
				}
	}
	if($expDescChanged == true){
		$updateSQL= "UPDATE agilent_experimentsdesc SET descrip = \"$_POST[expDescValue]\" WHERE expid = $_POST[expID]";
		$sqlResult = mysql_query($updateSQL, $db);
		if(mysql_errno($db)){
				echo "<strong>Database Error updating table. SQL: $updateSQL </strong><br>";
				}



	}

		// Get the new sample id....

  		$countSQL = "Select MAX(queue_arrayid) from agilent_array_queue";
		$countResult = mysql_query($countSQL, $db);
		$row = mysql_fetch_row($countResult);
		$maxsampleID = $row[0];
		if(is_nan($maxsampleID)){
			$maxsampleID = 0;
		}
		$newsampleid = $maxsampleID + 1;

		// GETTING THE VALUES TO POPULATE THE SELECTIONS.....
		$chemSQL = "SELECT DISTINCT chemid, chemical FROM chem ORDER BY chemid";
		$chemResult = mysql_query($chemSQL, $db);
		$firstchoice = 1;
		while(list($chemid, $chemical) = mysql_fetch_array($chemResult))
		 {
		 if($firstchoice == 1){
		 $chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"$chemid\" checked onClick= \"return 										drawbox('otherChemical',true)\">$chemical  ";
		 $firstchoice++;
		 }
		elseif($firstchoice%5==0){
		 $chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"$chemid\" onClick= \"return 											drawbox('otherChemical',true)\">$chemical "."<br>";
		 $firstchoice++;
		 }
		else{
		 $chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"$chemid\" onClick= \"return 											drawbox('otherChemical',true)\">$chemical  ";
		 $firstchoice++;
		 }
		}
		// Lets add an entry for "other" to enable user to enter a chemical not found in list
		$chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"\" onClick= \"return drawbox('otherChemical',false)\">Other ";


		$organismSQL = "SELECT organism FROM organism ORDER BY organism ASC";
		$organismResult = mysql_query($organismSQL, $db);
		$firstchoice = 1;
		while(list($organism) = mysql_fetch_array($organismResult))
		{
			if($firstchoice == 1){
				$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"$organism\" checked onClick= \"return 								drawbox('otherOrganism',true)\">$organism  ";
				$firstchoice = 0;
			}
			else{
				$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"$organism\" onClick= \"return 									drawbox('otherOrganism',true)\">$organism  ";
			}
		}
		$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"\" onClick= \"return drawbox('otherOrganism',false)\">Other  ";

		$strainSQL = "SELECT strain FROM strain ORDER BY strain ASC";
		$strainResult = mysql_query($strainSQL, $db);
		$firstchoice = 1;
		while(list($strain) = mysql_fetch_array($strainResult))
		{
			if($firstchoice == 1){
				$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"$strain\" checked onClick= \"return 									drawbox('otherStrain',true)\">$strain  ";
				$firstchoice = 0;
			}
			else{
				$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"$strain\" onClick= \"return 										drawbox('otherStrain',true)\">$strain  ";
			}
		}
		$strainMenu .= "<br>";
		$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"\" onClick= \"return drawbox('otherStrain',false)\">Other  ";

		$genevariationSQL = "SELECT genevariation FROM genevariation ORDER BY genevariationid ASC";
		$genevariationResult = mysql_query($genevariationSQL, $db);
		$firstchoice = 1;
		while(list($genevariation) = mysql_fetch_array($genevariationResult))
		{
			if($firstchoice == 1){
				$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"$genevariation\" 										checked onClick= \"return drawbox('otherGenevariation',true)\">$genevariation  ";
				$firstchoice = 0;
			}
			else{
				$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"$genevariation\" onClick= \"return 								drawbox('otherGenevariation',true)\">$genevariation  ";
			}
		}
		$genevariationMenu .= "<br>";
		$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"\" onClick= \"return 											drawbox('otherGenevariation',false)\">Other  ";


		$tissueSQL = "SELECT tissue FROM tissue ORDER BY tissueid ASC";
		$tissueResult = mysql_query($tissueSQL, $db);
		$firstchoice = 1;
		while(list($tissue) = mysql_fetch_array($tissueResult))
		{
			if($firstchoice == 1){
				$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissue\" checked onClick= \"return 									drawbox('otherTissue',true)\">$tissue  ";
				$firstchoice = 0;
			}
			else{
				$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissue\" onClick= \"return 										drawbox('otherTissue',true)\">$tissue  ";
			}
		}
		$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"\" onClick= \"return drawbox('otherTissue',false)\"> Other ";

		$vehicleSQL = "SELECT vehicle FROM vehicle ORDER BY vehicleid ASC";
		$vehicleResult = mysql_query($vehicleSQL, $db);
		$firstchoice = 1;
		while(list($vehicle) = mysql_fetch_array($vehicleResult))
		{
			if($firstchoice == 1){
				$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicle\" checked onClick= \"return 									drawbox('otherVehicle',true)\">$vehicle  ";
				$firstchoice = 0;
			}
			else{
				$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicle\" onClick= \"return 										drawbox('otherVehicle',true)\">$vehicle  ";
			}
		}
		$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"\" onClick= \"return drawbox('otherVehicle',false)\">Other ";

		$doseunitSQL = "SELECT doseunit FROM doseunit ORDER BY doseunitid ASC";
		$doseunitResult = mysql_query($doseunitSQL, $db);
		$firstchoice = 1;
		while(list($doseunit) = mysql_fetch_array($doseunitResult))
		{
			if($firstchoice == 1){
				$doseunitMenu .= "<input type=\"radio\" name=\"doseunit\" value=\"$doseunit\" checked>$doseunit  ";
				$firstchoice = 0;
			}
			else{
				$doseunitMenu .= "<input type=\"radio\" name=\"doseunit\" value=\"$doseunit\" >$doseunit  ";
			}
		}


		$routeSQL = "SELECT route FROM route ORDER BY routeid";
		$routeResult = mysql_query($routeSQL, $db);
		$firstchoice = 1;
		while(list($route) = mysql_fetch_array($routeResult))
		{
			if($firstchoice == 1){
				$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"$route\" checked onClick= \"return 										drawbox('otherRoute',true)\">$route  ";
				$firstchoice = 0;
			}
			else{
				$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"$route\" onClick= \"return 											drawbox('otherRoute',true)\">$route  ";
			}
		}
		$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"\" onClick= \"return drawbox('otherRoute',false)\">Other  ";

		$durationunitSQL = "SELECT durationunit FROM durationunit ORDER BY durationunitid";
		$durationunitResult = mysql_query($durationunitSQL, $db);
		$firstchoice = 1;
		while(list($durationunit) = mysql_fetch_array($durationunitResult))
		{
			if($firstchoice == 1){
				$durationunitMenu .= "<input type=\"radio\" name=\"durationunit\" value=\"$durationunit\" checked>$durationunit  ";
				$firstchoice = 0;
			}
			else{
				$durationunitMenu .= "<input type=\"radio\" name=\"durationunit\" value=\"$durationunit\">$durationunit  ";
			}
		}


		$controlSQL = "SELECT control FROM control ORDER BY controlid";
		$controlResult = mysql_query($controlSQL, $db);
		$firstchoice = 1;
		while(list($control) = mysql_fetch_array($controlResult))
		{
			if($firstchoice == 1){
				$controlMenu .= "<input type=\"radio\" name=\"control\" value=\"$control\" checked onClick= \"return 									drawbox('otherControl',true)\">$control  ";
				$firstchoice = 0;
			}
			else{
				$controlMenu .= "<input type=\"radio\" name=\"control\" value=\"$control\" onClick= \"return 										drawbox('otherControl',true)\">$control  ";
			}

		}

		$controlMenu .= "<input type=\"radio\" name=\"control\" value=\"\" onClick= \"return drawbox('otherControl',false)\">Other  ";


		$referenceSQL = "SELECT reference_name FROM agilent_reference_sample ORDER BY reference_id";
		$referenceResult = mysql_query($referenceSQL, $db);
		$firstchoice = 1;
		while(list($reference) = mysql_fetch_array($referenceResult))
		{
			if($firstchoice == 1){
				$referenceMenu .= "<input type=\"radio\" name=\"reference\" value=\"$reference\" checked onClick= \"return 									drawbox('otherReference',true)\">$reference  ";
				$firstchoice = 0;
			}
			else{
				$referenceMenu .= "<input type=\"radio\" name=\"reference\" value=\"$reference\" onClick= \"return 										drawbox('otherReference',true)\">$reference  ";
			}

		}
		$referenceMenu .= "<input type=\"radio\" name=\"reference\" value=\"\" onClick= \"return drawbox('otherReference',false)\">Other  ";






?>
<p>
<b>Instructions:</b> Please completely fill out the form below.  Your RNA samples cannot be processed unless this form has been filled out completely for <font color="red"><b>EACH</b></font> (distinct) RNA sample that you send.  Thanks!<br>
</p>
<form enctype="multipart/form-data" name="agilentqueueentry-nonadmin" action="agilentqueueentry-nonadmin.php" method="post" onsubmit="return checkForm()">

<input type="hidden" name="submitted" value="true">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2">New Treatment Form <font color="red"><strong>&nbsp&nbsp &nbsp&nbsp(*=Text Required)</strong></font><font color="blue"><strong>&nbsp&nbsp &nbsp&nbsp(#= Number Required)</strong></font></th>
</tr>
</thead>


<tr>
<td class="questionparameter" ><strong>Treatment Name:</strong></td>
<td class="results">
<input name="samplename" type="text" value="" align="left"></input><font color="red"><strong>*</strong></font>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Experiment Associated:</strong></td>
<td class="results">
<input type="hidden" name="expID" value="<?php echo $expID; ?>">
<input type="hidden" name="expName" value="<?php echo $expDescName; ?>">
<?php
	if($expDescName == ""){
		$sql = "SELECT expname FROM agilent_experimentsdesc WHERE expid = $expID";
		$result = mysql_query($sql, $db);

		$row = mysql_fetch_row($result);
		$expDescName = $row[0];

	}
	echo "Experiment #$expID".":".$expDescName;

?>
</td>
</tr>


<tr>
<td class="questionparameter" ><strong>Chemical:</strong></td>
<td class="results">
<?php echo $chemMenu; ?>
<input name='otherChemical' id='otherChemical' type="text" value="" align="left" disabled=true></input>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Organism:</strong></td>
<td class="results">
<?php echo $organismMenu; ?>
<input name='otherOrganism' id='otherOrganism' type="text" value="" align="left" disabled=true></input>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>RNA Group Size</strong><br>(If RNA is pooled,<br>then enter number<br> of animals, else enter 1):</td>
<td class="results">
<input name="rnagroupsize" type="text" value="1" align="right"></input><font color="blue"><strong>#</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Concentration</strong></td>
<td class="results">
<input name="concentration" type="text" value="" align="right"></input><font color="blue"><strong>microGram/microLitre</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Strain:</strong></td>
<td class="results">
<?php echo $strainMenu; ?>
<input name='otherStrain' id='otherStrain' type="text" value="" align="left" disabled=true></input>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Gene Variation:</strong></td>
<td class="results">
<?php echo $genevariationMenu; ?>
<input name='otherGenevariation' id='otherGenevariation' type="text" value="" align="left" disabled=true></input>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Age (in weeks):</strong></td>
<td class="results">
<input name="age" type="text" value="6" align="right"></input><font color="blue"><strong>#</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Sex:</strong></td>
<td class="results">
<input type="radio" name="sex" value="M" checked onClick= "return disableRadioGroup('pregnant','true')">Male
<input type="radio" name="sex" value="F" onClick= "return disableRadioGroup('pregnant','false')"> Female
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Pregnant?</strong></td>
<td class="results">
<input type="radio" name="pregnant" value="no" checked onClick= "return drawbox('gestation',true)" DISABLED> no
<input type="radio" name="pregnant" value="yes"  onClick= "return drawbox('gestation',false)" DISABLED> yes
<input name='gestation' id='gestation' type="text" size=5 value="" align="left" disabled=true> Gestation Period (days)</input>
<font color="blue"><strong>#</strong></font>
</td>
</tr>



<tr>
<td class="questionparameter" ><strong>Tissue:</strong></td>
<td class="results">
<?php echo $tissueMenu; ?>
<input name='otherTissue' id='otherTissue' type="text" value="" align="left" disabled=true></input>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Vehicle:</strong></td>
<td class="results">
<?php echo $vehicleMenu; ?>
<input name='otherVehicle' id='otherVehicle' type="text" value="" align="left" disabled=true></input>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Dose:</strong></td>
<td class="results">
<input name="dose" type="text" value="10" align="right"></input><font color="blue"><strong>#</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Dose Units:</strong></td>
<td class="results">
<?php echo $doseunitMenu; ?>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Route of administration:</strong></td>
<td class="results">
<?php echo $routeMenu; ?>
<input name='otherRoute' id='otherRoute' type="text" value="" align="left" disabled=true></input>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Time of Day of Dose:</strong></td>
<td class="results">
<input name="dosehours" type="text" value="12" align="right" size="2"></input>:
<input name="doseminutes" type="text" value="00" align="right" size="2"></input><font color="blue"><strong>#</strong></font><strong><font color="red">&nbsp&nbsp &nbsp&nbsp Note:</font>&nbsp 01:59 = 1:59am  12:00 = noon  23:59 = 11:59pm  00:00 = midnight</strong>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Time of Day at Harvest:</strong></td>
<td class="results">
<input name="harvesthours" type="text" value="00" align="right" size="2"></input>:
<input name="harvestminutes" type="text" value="00" align="right" size="2"></input><font color="blue"><strong>#</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Time After Dosing Of Harvest (Duration):</strong></td>
<td class="results">
<input name="duration" type="text" value="12" align="right"></input><font color="blue"><strong># Enter a zero if no value.</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Duration Units:</strong></td>
<td class="results">
<?php echo $durationunitMenu; ?>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Control Treatment:</strong></td>
<td class="results">
<?php echo $controlMenu; ?>
<input name='otherControl' id='otherControl' type="text" value="" align="left" disabled=true></input>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Reference/Control Sample<br>Competitively Hybridized w/:</strong></td>
<td class="results">
<?php echo $referenceMenu; ?>
<input name='otherReference' id='otherReference' type="text" value="" align="left" disabled=true></input>
<font color="red"><strong>*</strong></font>
</td>
</tr>


<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td><input type="reset" value="Reset Form"></td>
</tr>

</table>


</form>
<?php
	}  // end if not submitted....
	else{
		if (!isset($_POST['commit'])) { // if form has not been committed....

			$fileError = 0;
			
	// Fetch chemical name from db only when other is not selected... the other button has no value assigned to it and works for ''
	if($chemical != '')
	{
		//echo "yes, it's a number";
		//echo $chemical;
		$chemSQL = "SELECT DISTINCT chemical FROM chem WHERE chemid = $chemical";

		$chemResult = mysql_query($chemSQL, $db);

		$thischemical= mysql_fetch_row($chemResult);
		$treatment = $thischemical[0];
	}

	?>
		<form name="commitnewsample" action="agilentqueueentry-nonadmin.php" method="post">
		<input type="hidden" name="submitted" value="true">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="2">New Treatment Form Entries <br>Please check for accuracy!</th>
		</tr>

		

		<tr>
		<td class="questionparameter" ><strong>Sample Name:</strong></td>
		<td class="results">
		<input type="hidden" name="samplename" value="<?php echo $samplename; ?>">
		<?php echo $samplename; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Sample Name:</strong></td>
		<td class="results">
		<input type="hidden" name="expID" value="<?php echo $expID; ?>">
		<input type="hidden" name="expName" value="<?php echo $expName; ?>">
		<?php echo "Experiment #$expID".":".$expName; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Treatment:</strong></td>
		<td class="results">
		<input type="hidden" name="chemical" value="<?php echo $chemical; ?>">
		<input type="hidden" name="treatment" value="<?php echo $treatment; ?>">
		<input type="hidden" name="otherChemical" value="<?php echo $otherChemical; ?>">
		<?php
		echo $treatment.$otherChemical; // when 'other' is disabled, $treatment gets printed... if enabled, $otherChemical gets printed

	

		?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Organism:</strong></td>
		<td class="results">
		<input type="hidden" name="organism" value="<?php echo $organism; ?>">
		<input type="hidden" name="otherOrganism" value="<?php echo $otherOrganism; ?>">
		<?php echo $organism.$otherOrganism; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>RNA Group Size:</strong></td>
		<td class="results">
		<input type="hidden" name="rnagroupsize" value="<?php echo $rnagroupsize; ?>">
		<?php echo $rnagroupsize; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Concentration:</strong></td>
		<td class="results">
		<input type="hidden" name="concentration" value="<?php echo $concentration; ?>">
		<?php echo $concentration; ?>
		</td>
		</tr>


		<tr>
		<td class="questionparameter" ><strong>Strain:</strong></td>
		<td class="results">
		<input type="hidden" name="strain" value="<?php echo $strain; ?>">
		<input type="hidden" name="otherStrain" value="<?php echo $otherStrain; ?>">
		<?php echo $strain.$otherStrain; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Genetic Variation:</strong></td>
		<td class="results">
		<input type="hidden" name="genevariation" value="<?php echo $genevariation; ?>">
		<input type="hidden" name="otherGenevariation" value="<?php echo $otherGenevariation; ?>">
		<?php echo $genevariation.$otherGenevariation; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Age:</strong></td>
		<td class="results">
		<input type="hidden" name="age" value="<?php echo $age; ?>">
		<?php echo $age; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Sex:</strong></td>
		<td class="results">
		<input type="hidden" name="sex" value="<?php echo $sex; ?>">
		<?php echo $sex; ?>
		</td>
		</tr>

		<?php
		if($pregnant){
		?>
			<tr>
			<td class="questionparameter" ><strong>Pregnant?</strong></td>
			<td class="results">
			<input type="hidden" name="pregnant" value="<?php echo $pregnant; ?>">
			<input type="hidden" name="gestation" value="<?php echo $gestation; ?>">
			<?php
			if($pregnant=="yes"){
				echo $pregnant . ", since " . $gestation . " day(s)";
			}
			else{
				echo $pregnant;
			}
			?>
			</td>
			</tr>
		<?php
		}
		?>

		<tr>
		<td class="questionparameter" ><strong>Tissue:</strong></td>
		<td class="results">
		<input type="hidden" name="tissue" value="<?php echo $tissue; ?>">
		<input type="hidden" name="otherTissue" value="<?php echo $otherTissue; ?>">
		<?php echo $tissue.$otherTissue; ?>
		</td>
		</tr>


		<tr>
		<td class="questionparameter" ><strong>Vehicle:</strong></td>
		<td class="results">
		<input type="hidden" name="vehicle" value="<?php echo $vehicle; ?>">
		<input type="hidden" name="otherVehicle" value="<?php echo $otherVehicle; ?>">
		<?php echo $vehicle.$otherVehicle; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Dose:</strong></td>
		<td class="results">
		<input type="hidden" name="dose" value="<?php echo $dose; ?>">
		<input type="hidden" name="doseunit" value="<?php echo $doseunit; ?>">
		<?php echo "$dose $doseunit"; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Route of Administration:</strong></td>
		<td class="results">
		<input type="hidden" name="route" value="<?php echo $route; ?>">
		<input type="hidden" name="otherRoute" value="<?php echo $otherRoute; ?>">
		<?php echo $route.$otherRoute; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Control Treatment:</strong></td>
		<td class="results">
		<input type="hidden" name="control" value="<?php echo $control; ?>">
		<input type="hidden" name="otherControl" value="<?php echo $otherControl; ?>">
		<?php echo $control.$otherControl; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Reference Sample:</strong></td>
		<td class="results">
		<input type="hidden" name="reference" value="<?php echo $reference; ?>">
		<input type="hidden" name="otherReference" value="<?php echo $otherReference; ?>">
		<?php echo $reference.$otherReference; ?>
		</td>
		</tr>



		<tr>
		<td class="questionparameter" ><strong>Time of Day of Dose:</strong></td>
		<td class="results">
		<input type="hidden" name="dosagetime" value="<?php echo "$dosehours:$doseminutes:00"; ?>">
		<?php echo "$dosehours:$doseminutes"; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Time of Day at Harvest:</strong></td>
		<td class="results">
		<input type="hidden" name="harvesttime" value="<?php echo "$harvesthours:$harvestminutes:00"; ?>">
		<?php echo "$harvesthours:$harvestminutes"; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Time After Dosing of Harvest (Duration):</strong></td>
		<td class="results">
		<input type="hidden" name="duration" value="<?php echo "$duration"; ?>">
		<input type="hidden" name="durationunit" value="<?php echo "$durationunit"; ?>">
		<?php echo "$duration $durationunit"; ?>
		</td>
		</tr>


	</table>
	<tr>
	<?php
		//if($fileError != 1){
			echo "<td><input type=\"submit\" name=\"commit\" value=\"Correct? Commit to Database!\" onClick=\"return sendemail()\"></td>";
		//}
		//else{
			//echo "<td></td>";
		//}
	?>
	<td><INPUT TYPE="button" VALUE="Incorrect Values...Fix Them!" onClick="history.go(-1)"></td>
	</tr>

	</form>

<?php
	} // end of not committed....
	// Here's where the newly created treatment needs to be written to db... in a separate sample table maintained for user created trx
	else{



		// Now insert data into the user_sampledata table
		$countSQL = "Select MAX(queue_arrayid) from agilent_array_queue";
		$countResult = mysql_query($countSQL, $db);
		$row = mysql_fetch_row($countResult);

		$maxsampleID = $row[0];
		if(is_nan($maxsampleID)){
			$maxsampleID = 0;
		}
		$newsampleid = $maxsampleID + 1;
		$name = $_SESSION['firstname']." ".$_SESSION['lastname'];
		// Add the treatment to sample....
		$sql = "INSERT agilent_array_queue (queue_arrayid, array_queue_samplename, organism, rnagroupsize, concentration, strain, genevariation, age, sex, tissue, 	treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, gestationperiod, experiment_id, experiment, datesubmitted, submitter, reference_sample)
			VALUES ($newsampleid, \"$samplename\", \"$organism $otherOrganism\", $rnagroupsize, $concentration, \"$strain $otherStrain\", \"$genevariation $otherGenevariation\", $age, '$sex',\"$tissue $otherTissue\", \"$treatment $otherChemical\", \"$vehicle $otherVehicle\", $dose, \"$route $otherRoute\", \"$dosagetime\", $duration, \"$harvesttime\", \"$control $otherControl\", \"$doseunit\", \"$durationunit\",  \"$pregnant\",  \"$gestation\", \"$expID\", \"$expName\", NOW(),\"$name\", \"$reference $otherReference\")";


		$newTreatmentResult = mysql_query($sql, $db);
		echo $sql;
		$trxsampledata = "";
		if(mysql_errno($db)){
			echo "uh-oh db error";
			echo " trxid is " . $trxid;
			echo $newTreatmentResult;
			$trxsampledata .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
			$trxsampledata .= "<strong><font color=\"red\">".mysql_errno($db) . ": " . mysql_error($db). "</font></strong>\n";
			$trxsampledata .= "<br>heres sql:  <br>";
			$trxsampledata .= "$sql";
			$trxfiledata = "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
			$pgemdata .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
			$sampleinfo .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
		}
		else{
		//echo "<strong><font color=\"red\">This treatment information has been committed to the database....</font><br>";
			$trxsampledata .= "<strong><font color=\"green\">Successful!</font><br>";

		?>


			<table class="question" width="400">
		  	<thead>
		 	<tr>
		  	<th class="mainheader" colspan="2">Treatment Submission Results</th>
			</tr>
			<tr>
			<td class="questionparameter" ><strong>New Treatment Creation:</strong></td>
			<td class="results">
			<?php echo $trxsampledata; ?>
			</td>
			</tr>
			</table>



		<?php
	
	}

	// Commenting out the datafile deletion code since at this point datafile is not yet created.
	//$command = "rm -f $datafile";
	//$str=exec($command);
	}// end of initial submit....
}
?>

 </div>
 <?php
	include './leftmenu.inc';

 ?>


 <div class="boxclear"> </div>




 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>

</body>
</html>
