<?php

/*
scriptname: newagilent-nonadmin.inc
purpose:  This script has a few functions: 1) editing an existing array, 2) submitting an array into the database queue, or 3) committing an array from the queue into the database.



*/

$debug=-1;
if(isset($_SESSION['userid']){
	$userid = $_SESSION['userid'];
}else{
	die("You need to login to use this feature. <BR>";
}
//echo "$userid <hr>";
if($debug==1 && $userid == 1){
	analyze($_POST);
}
if(count($_POST) > 0){
if(isset($_POST['submitted'])){
	$submitted = $_POST['submitted'];
}else{
	$submitted = "";
}
if(isset($_POST['arrayedit'])){
	$arrayedit = $_POST['arrayedit'];
}else{
	$arrayedit = "";
}
if(isset($_POST['userid'])){
	$userid = $_POST['userid'];
}else{
	$userid = "";
}
if($isset($_POST['arrayid'])){
	$arrayid = $_POST['arrayid'];
}else{
	$arrayid = "";
}
if($isset($_POST['samplename'])){
	$samplename = $_POST['samplename'];
}else{
	$samplename = "";
}
if($isset($_POST['chemical'])){
	$chemical = $_POST['chemical'];
}else{
	$chemical = "";
}
if($isset($_POST['organism'])){
	$organism = $_POST['organism'];
}else{
	$organism = "";
}
if($isset($_POST['rnagroupsize'])){
	$rnagroupsize = $_POST['rnagroupsize'];
}else{
	$rnagroupsize = "";
}

if($isset($_POST['concentration'])){
	$concentration = $_POST['concentration'];
}else{
	$concentration = "";
}
if($isset($_POST['strain'])){
	$strain = $_POST['strain'];
}else{
	$strain = "";
}
if($isset($_POST['genevariation'])){
	$genevariation = $_POST['genevariation'];
}else{
	$genevariation = "";
}
if($isset($_POST['age'])){
	$age = $_POST['age'];
}else{
	$age = "";
}
if($isset($_POST['sex'])){
	$sex = $_POST['sex'];
}else{
	$sex = "";
}
if($isset($_POST['tissue'])){
	$tissue = $_POST['tissue'];
}else{
	$tissue = "";
}
if($isset($_POST['vehicle'])){
	$vehicle = $_POST['vehicle'];
}else{
	$tissue = "";
}
if($isset($_POST['dose'])){
	$dose = $_POST['dose'];
}else{
	$dose = "";
}
if($isset($_POST['route'])){
	$route = $_POST['route'];
}else{
	$route = "";
}
if($isset($_POST['dosehours'])){
	$dosehours = $_POST['dosehours'];
}else{
	$dosehours = "";
}
if($isset($_POST['doseminutes'])){
	$doseminutes = $_POST['doseminutes'];
}else{
	$doseminutes = "";
}
if($isset($_POST['harvesthours'])){
	$harvesthours = $_POST['harvesthours'];
}else{
	$harvesthours = "";
}
if($isset($_POST['harvestminutes'])){
	$harvestminutes = $_POST['harvestminutes'];
}else{
	$harvestminutes = "";
}
if($isset($_POST['duration'])){
	$duration = $_POST['duration'];
}else{
	$duration = "";
}
if($isset($_POST['control'])){
	$control = $_POST['control'];
}else{
	$control = "";
}
if($isset($_POST['reference'])){
	$reference = $_POST['reference'];
}else{
	$reference = "";
}
if($isset($_POST['submit'])){
	$submit = $_POST['submit'];
}else{
	$submit = "";
}
if($isset($_POST['expID'])){
	$expID = $_POST['expID'];
}else{
	$expID = "";
}
}
if($submitted != true){

	$arrayid=$_GET['arrayid'];

	// need to determine whether we are editing an existing array, submitting an array into the database queue, or committing an array from the queue into the database....
	if($arrayid != ""){
		//echo "Editing....<br>";
		// We need to treat this form as an edit form, not as a new array form.
		// get the values associated with it in the agilent_arrayinfo table.
		$sql = "SELECT * FROM agilent_arrayinfo WHERE arrayid = $arrayid";

		//echo "<br>$sql<br>";
		$arrayResult = $db->Execute($sql);//mysql_query($sql,$db);
		$assocResult = $db->Execute($sql);;//mysql_query($sql,$db);
		$arrayinfo = $arrayResult->FetchRow();//mysql_fetch_row($arrayResult);
		//$assocrow = mysql_fetch_assoc($assocResult);
//analyze($arrayinfo);
		$arraytype = $arrayinfo[1];
		$arrayname = $arrayinfo[2];
		//echo "$arrayname<br>";
		$arrayorganism = $arrayinfo[3];
		$arrayrnagroupsize = $arrayinfo[4];
		$arrayrnaconcentration = $arrayinfo[5];
		$arraystrain = $arrayinfo[6];
		$arraygenevariation = $arrayinfo[7];
		$arrayage = $arrayinfo[8];
		$arraysex = $arrayinfo[9];
		$arraytissue = $arrayinfo[10];
		$arraytreatment = $arrayinfo[11];
		$arrayvehicle = $arrayinfo[12];
		$arraydose = $arrayinfo[13];
		$arrayroute = $arrayinfo[14];
		$arraydosagetime = $arrayinfo[15];
		$arrayduration = $arrayinfo[16];
		$arrayharvesttime = $arrayinfo[17];
		$arraycontrol = $arrayinfo[18];
		$arraydoseunits = $arrayinfo[19];
		$arraydurationunits = $arrayinfo[20];
		$arraypregnant = $arrayinfo[21];
		$arraygestationperiod = $arrayinfo[22];
		$arrayexpid = $arrayinfo[23];
		$arrayexperiment = $arrayinfo[24];
		$arraydateprocessed = $arrayinfo[25];
		$arraysubmitterid = $arrayinfo[26];
		$arrayreferencesample = $arrayinfo[27];
		$arrayownerid = $arrayinfo[28];
		//analyze($arrayinfo);
		$width = 600;
		$tableid = "results";

		//array2table($assocrow, $width, $tableid);
		$arrayeditvalue = "true";  // this just tells us that the array is being edited....
	}
	$queuenumber=$_GET['queuenumber'];
	if($queuenumber != ""){

		// Get the new array id....

  		$countSQL = "Select MAX(queue_arrayid) from agilent_array_queue";
		$countResult = $db->Execute($countSQL);//mysql_query($countSQL, $db);
		$row = $countResult->FetchRow();//mysql_fetch_row($countResult);
		$maxarrayID = $row[0];
		if(is_nan($maxarrayID)){
			$maxarrayID = 0;
		}
		$newarrayid = $maxarrayID + 1;
		$arrayeditvalue = "false";
	}

		// GETTING THE VALUES TO POPULATE THE SELECTIONS.....
		$chemSQL = "SELECT DISTINCT chemid, chemical FROM chem ORDER BY chemid";
		$chemResult = $db->Execute($chemSQL);//mysql_query($chemSQL, $db);
		$firstchoice = 1;
		#while(list($chemid, $chemical) = mysql_fetch_array($chemResult))
		while($row=$chemResult->FetchRow())
		 {
			$chemid = $row[0]; 
			$chemical = $row[1];
		 	if($arrayid != "" && $chemical == $arraytreatment){
				$checked = "checked";
			}else{
				$checked = "";
			}
		 if($firstchoice == 1){
			if($arrayid == ""){
		 		$chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"$chemid\" checked onClick= \"return drawbox('otherChemical',true)\">$chemical  ";
			}else{
				$chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"$chemid\" $checked onClick= \"return drawbox('otherChemical',true)\">$chemical  ";
			}
		 $firstchoice++;
		 }
		elseif($firstchoice%5==0){
		 $chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"$chemid\" $checked onClick= \"return 											drawbox('otherChemical',true)\">$chemical "."<br>";
		 $firstchoice++;
		 }
		else{
		 $chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"$chemid\" $checked onClick= \"return 											drawbox('otherChemical',true)\">$chemical  ";
		 $firstchoice++;
		 }
		}
		// Lets add an entry for "other" to enable user to enter a chemical not found in list
		$chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"\" onClick= \"return drawbox('otherChemical',false)\">Other ";


		$organismSQL = "SELECT id, organism FROM agilentarrays ORDER BY id ASC";
		$organismResult = $db->Execute($organismSQL);//mysql_query($organismSQL, $db);
		$firstchoice = 1;
		//echo "Array organism: $arrayorganism<br>";
		//while(list($id, $organism) = mysql_fetch_array($organismResult))
		while($row = $organismResult->FetchRow())
		{
			$id = $row[0];
			$organism = row[1];
			//echo "organism = $organism<br>";
			if($arrayid != "" && $id == $arrayorganism){
				$checked = "checked";
			}else{
				$checked = "";
			}
			//echo "checked = $checked<br>";
			if($firstchoice == 1){
				if($arrayid == ""){
					$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"$id\" checked onClick= \"return drawbox('otherOrganism',true)\">$organism  ";
				}else{
					$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"$id\" $checked onClick= \"return drawbox('otherOrganism',true)\">$organism  ";

				}
				$firstchoice = 0;
			}
			else{
				$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"$id\" $checked onClick= \"return drawbox('otherOrganism',true)\">$organism  ";
			}
		}
		$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"\" onClick= \"return drawbox('otherOrganism',false)\">Other  ";

		$strainSQL = "SELECT strain FROM strain ORDER BY strain ASC";
		$strainResult = $db->Execute($strainSQL);//mysql_query($strainSQL, $db);
		$firstchoice = 1;
		$counter = 0;
		//while(list($strain) = mysql_fetch_array($strainResult))
		while($row = $strainResult->FetchRow())
		{
			$strain = $row[0];
			if($arrayid != "" && $strain == $arraystrain){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($arrayid == ""){
					$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"$strain\" checked onClick= \"return drawbox('otherStrain',true)\">$strain  ";
				}else{
					$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"$strain\" $checked onClick= \"return drawbox('otherStrain',true)\">$strain  ";
				}
				$firstchoice = 0;
			}
			else{
				$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"$strain\" $checked onClick= \"return drawbox('otherStrain',true)\">$strain  ";
				if($counter%5 == 0){
					$strainMenu .= "<br>";
				}
			}
			$counter++;
		}
		$strainMenu .= "<br>";
		$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"\" onClick= \"return drawbox('otherStrain',false)\">Other  ";

		$genevariationSQL = "SELECT genevariation FROM genevariation ORDER BY genevariationid ASC";
		$genevariationResult = $db->Execute($genevariationSQL);//mysql_query($genevariationSQL, $db);
		$firstchoice = 1;
		$counter = 0;
		#while(list($genevariation) = mysql_fetch_array($genevariationResult))
		
		while($row = $genevariationResult->FetchRow())
		{
			$genevariation = $row[0];
			if($arrayid != "" && $genevariation == $arraygenevariation){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($arrayid == ""){
					$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"$genevariation\" checked onClick= \"return drawbox('otherGenevariation',true)\">$genevariation  ";
				}else{
					$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"$genevariation\" $checked onClick= \"return drawbox('otherGenevariation',true)\">$genevariation  ";

				}
				$firstchoice = 0;
			}
			else{
				$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" $checked value=\"$genevariation\" onClick= \"return drawbox('otherGenevariation',true)\">$genevariation  ";
				if($counter%5 == 0){
					$genevariationMenu .= "<br>";
				}
			}
			$counter++;
		}
		$genevariationMenu .= "<br>";
		$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"\" onClick= \"return 											drawbox('otherGenevariation',false)\">Other  ";


		$tissueSQL = "SELECT tissue FROM tissue ORDER BY tissueid ASC";
		$tissueResult = $db->Execute($tissueSQL);//mysql_query($tissueSQL, $db);
		$firstchoice = 1;
		$counter = 0;
		while($row = $tissueResult->FetchRow())
		{
			$tissue = $row[0];
			if($arrayid != "" && $tissue == $arraytissue){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($arrayid == ""){
					$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissue\" checked onClick= \"return drawbox('otherTissue',true)\">$tissue  ";
				}else{
					$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissue\" $checked onClick= \"return drawbox('otherTissue',true)\">$tissue  ";
				}
				$firstchoice = 0;
			}
			else{
				$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissue\" $checked onClick= \"return 										drawbox('otherTissue',true)\">$tissue  ";
				if($counter%5 == 0){
					$tissueMenu .= "<br>";
				}
			}
			$counter++;
		}
		$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"\" onClick= \"return drawbox('otherTissue',false)\"> Other ";

		$vehicleSQL = "SELECT vehicle FROM vehicle ORDER BY vehicleid ASC";
		$vehicleResult = $db->Execute($vehicleSQL);//mysql_query($vehicleSQL, $db);
		$firstchoice = 1;
		$counter = 0;
		//while(list($vehicle) = mysql_fetch_array($vehicleResult))
		while($row = $vehicleResult->FetchRow())
		{
			$vehicle = $row[0];
			if($arrayid != "" && $vehicle == $arrayvehicle){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($arrayid == ""){
					$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicle\" checked onClick= \"return drawbox('otherVehicle',true)\">$vehicle  ";
				}else{
					$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicle\" $checked onClick= \"return drawbox('otherVehicle',true)\">$vehicle  ";
				}
				$firstchoice = 0;
			}
			else{
				$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicle\" $checked onClick= \"return drawbox('otherVehicle',true)\">$vehicle  ";
				if($counter%5 == 0){
					$vehicleMenu .= "<br>";
				}
			}
			$counter++;
		}
		$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"\" onClick= \"return drawbox('otherVehicle',false)\">Other ";

		$doseunitSQL = "SELECT doseunit FROM doseunit ORDER BY doseunitid ASC";
		$doseunitResult = $db->Execute($doseunitSQL);//mysql_query($doseunitSQL, $db);
		$firstchoice = 1;
		#while(list($doseunit) = mysql_fetch_array($doseunitResult))
		
		while($row = $doseunitResult->FetchRow())
		{
			$doseunit = $row[0];
			if($arrayid != "" && $doseunit == $arraydoseunit){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($arrayid == ""){
					$doseunitMenu .= "<input type=\"radio\" name=\"doseunit\" value=\"$doseunit\" checked>$doseunit  ";
			}else{
						$doseunitMenu .= "<input type=\"radio\" name=\"doseunit\" value=\"$doseunit\" $checked>$doseunit  ";
			}
							$firstchoice = 0;
			}
			else{
				$doseunitMenu .= "<input type=\"radio\" name=\"doseunit\" $checked value=\"$doseunit\" >$doseunit  ";
			}
		}


		$routeSQL = "SELECT route FROM route ORDER BY routeid";
		$routeResult = $db->Execute($routeSQL);//mysql_query($routeSQL, $db);
	
		$firstchoice = 1;
		//while(list($route) = mysql_fetch_array($routeResult))
		
		while($row=$routeResult->FetchRow())
		{
			$route = $row[0];
			if($arrayid != "" && $route == $arrayroute){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($arrayid == ""){
					$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"$route\" checked onClick= \"return drawbox('otherRoute',true)\">$route  ";
				}else{
					$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"$route\" $checked onClick= \"return drawbox('otherRoute',true)\">$route  ";
				}
				$firstchoice = 0;
			}
			else{
				$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"$route\" $checked onClick= \"return drawbox('otherRoute',true)\">$route  ";
			}
		}
		$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"\" onClick= \"return drawbox('otherRoute',false)\">Other  ";

		$durationunitSQL = "SELECT durationunit FROM durationunit ORDER BY durationunitid";
		$durationunitResult = $db->Execute($durationunitSQL);// mysql_query($durationunitSQL, $db);
		$firstchoice = 1;
		//while(list($durationunit) = mysql_fetch_array($durationunitResult))
		while($row = $durationunitResult->FetchRow())
		{
			$durationunit = $row[0];
			if($arrayid != "" && $durationunit == $arraydurationunits){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($arrayid == ""){
					$durationunitMenu .= "<input type=\"radio\" name=\"durationunit\" value=\"$durationunit\" checked>$durationunit  ";
				}else{
					$durationunitMenu .= "<input type=\"radio\" name=\"durationunit\" value=\"$durationunit\" $checked>$durationunit  ";
				}
				$firstchoice = 0;
			}
			else{
				$durationunitMenu .= "<input type=\"radio\" name=\"durationunit\" value=\"$durationunit\" $checked>$durationunit  ";
			}
		}


		$controlSQL = "SELECT control FROM control ORDER BY controlid";
		$controlResult = $db->Execute($controlSQL);//mysql_query($controlSQL, $db);
		$firstchoice = 1;
		#while(list($control) = mysql_fetch_array($controlResult))
		while($row = $controlResult->FetchRow())
		{
			$control = $row[0];
			if($arrayid != "" && $control == $arraycontrol){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($arrayid == ""){
					$controlMenu .= "<input type=\"radio\" name=\"control\" value=\"$control\" checked onClick= \"return drawbox('otherControl',true)\">$control  ";
				}else{
					$controlMenu .= "<input type=\"radio\" name=\"control\" value=\"$control\" $checked onClick= \"return drawbox('otherControl',true)\">$control  ";
				}
				$firstchoice = 0;
			}
			else{
				$controlMenu .= "<input type=\"radio\" name=\"control\" value=\"$control\" $checked onClick= \"return drawbox('otherControl',true)\">$control  ";
			}

		}

		$controlMenu .= "<input type=\"radio\" name=\"control\" value=\"\" onClick= \"return drawbox('otherControl',false)\">Other  ";


		$referenceSQL = "SELECT reference_name FROM agilent_reference_sample ORDER BY reference_id";
		$referenceResult = $db->Execute($referenceSQL);//mysql_query($referenceSQL, $db);
		$firstchoice = 1;
		//while(list($reference) = mysql_fetch_array($referenceResult))
		while($row = $referenceResult->FetchRow())
		{
			$reference = $row[0];
			if($arrayid != "" && $reference == $arrayreferencesample){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($arrayid == ""){
					$referenceMenu .= "<input type=\"radio\" name=\"reference\" value=\"$reference\" checked onClick= \"return drawbox('otherReference',true)\">$reference  ";
				}else{
					$referenceMenu .= "<input type=\"radio\" name=\"reference\" value=\"$reference\" $checked onClick= \"return drawbox('otherReference',true)\">$reference  ";
				}
				$firstchoice = 0;
			}
			else{
				$referenceMenu .= "<input type=\"radio\" name=\"reference\" $checked value=\"$reference\" onClick= \"return drawbox('otherReference',true)\">$reference  ";
			}

		}
		$referenceMenu .= "<input type=\"radio\" name=\"reference\" value=\"\" onClick= \"return drawbox('otherReference',false)\">Other  ";


if($arrayeditvalue == "true"){
	$arrayformstring = "Edit";
	$instructions = "This form is used to change the values of an already existing array.  The current values will be archived in the event that you have made a mistake and have forgotten what the original values are.  If this happens, please contact the Bradfield lab to rectify things.";

}else{
	$arrayformstring = "New";
	$instructions = "Please completely fill out the form below.  Your RNA samples cannot be processed unless this form has been filled out completely for <font color=\"red\"><b>EACH</b></font> (distinct) RNA sample that you send.  Thanks!<br>";

}



?>
<p>
<b>Instructions:</b>  <?php echo $instructions; ?>
</p>
<form enctype="multipart/form-data" name="agilentarray" action="./agilentexperiment-useradmin.php?new-edit_array=1" method="post" onsubmit="return checkForm2()">

<input type="hidden" name="submitted" value="true">
<input type="hidden" name="arrayedit" value="<?php echo $arrayeditvalue; ?>">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2"><?php echo "$arrayformstring"; ?> Edge<sup>3</sup> Array Form <font color="red"><strong>&nbsp&nbsp &nbsp&nbsp(*=Text Required)</strong></font><font color="blue"><strong>&nbsp&nbsp &nbsp&nbsp(#= Number Required)</strong></font></th>
</tr>
</thead>
<?php
	if($arrayid != ""){
?>
		<tr>
		<td class="questionparameter" ><strong><?php echo "$arrayformstring"; ?> Array ID:</strong></td>
		<td class="results">
		<?php echo $arrayid; ?>
		<input type="hidden" name="userid" value="<?php echo $userid; ?>">
		<input type="hidden" name="arrayid" value="<?php echo $arrayid; ?>">
		</td>
		</tr>
<?php
}elseif($queuenumber != ""){

// this is an array submitted into the queue....
?>

<tr>
<td class="questionparameter" ><strong>New Array ID:</strong></td>
<td class="results">
<?php echo $newarrayid; ?>
<input type="hidden" name="trxid" value="<?php echo $newarrayid; ?>">
<input type="hidden" name="userid" value="<?php echo $userid; ?>">
</td>
</tr>
<?php
}
?>


<tr>
<td class="questionparameter" ><strong>Array Name:</strong></td>
<td class="results">
<input name="samplename" type="text" align="left" size="100" value="<?php echo $arrayname; ?>"></input><font color="red"><strong>*</strong></font>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Experiment(s) Associated:</strong></td>
<td class="results">
<?php
	if($arrayid != ""){
	$sql = "SELECT expid FROM agilent_experiments WHERE arrayid = $arrayid";
//echo $sql;
	$result = $db->Execute($sql);//mysql_query($sql, $db);
	
	#while($row = mysql_fetch_row($result)){
	while($row = $result->FetchRow()){
		$sql = "SELECT expname FROM agilent_experimentsdesc WHERE expid = $row[0]";
		//echo $sql;
		$expresult = $db->Execute($sql);//mysql_query($sql,$db);
		$expname = $expresult->FetchRow();//mysql_fetch_row($expresult);
 		echo "$expname[0]<br>";
	}
	}

?>
</td>
</tr>
<!--
<tr>
<td class="questionparameter" ><strong>Publication Information:</strong></td>
<td class="results">
<input name="pubinfo" type="text" value="" align="left"></input>
</td>
</tr>
-->

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
<input name="concentration" type="text" value="<?php echo $arrayrnaconcentration; ?>" align="right"></input><font color="blue"><strong>microGram/microLitre</strong></font>
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
<input name="age" type="text" value="<?php echo $arrayage; ?>" align="right"></input><font color="blue"><strong>#</strong></font>
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
<input name="dose" type="text" value="<?php echo $arraydose; ?>" align="right"></input><font color="blue"><strong>#</strong></font>
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
<?php
	// Need to create hour and minute vars for time of day dosage....
	if($arrayid != ""){
		$dosagetimearray = explode(":", $arraydosagetime);
		$dosagehours = $dosagetimearray[0];
		$dosagemins = $dosagetimearray[1];
	}else{
		$dosagehours = "12";
		$dosagemins = "00";
	}

?>
<td class="results">
<input name="dosehours" type="text" value="<?php echo $dosagehours; ?>" align="right" size="2"></input>:
<input name="doseminutes" type="text" value="<?php echo $dosagemins; ?>" align="right" size="2"></input><font color="blue"><strong>#</strong></font><strong><font color="red">&nbsp&nbsp &nbsp&nbsp Note:</font>&nbsp 01:59 = 1:59am  12:00 = noon  23:59 = 11:59pm  00:00 = midnight</strong>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Time of Day at Harvest:</strong></td>
<?php
	// Need to create hour and minute vars for time of day dosage....
	if($arrayid != ""){
		$harvesttimearray = explode(":", $arrayharvesttime);
		$harvesthours = $harvesttimearray[0];
		$harvestmins = $harvesttimearray[1];
	}else{
		$harvesthours = "00";
		$harvestmins = "00";
	}


?>
<td class="results">
<input name="harvesthours" type="text" value="<?php echo $harvesthours; ?>" align="right" size="2"></input>:
<input name="harvestminutes" type="text" value="<?php echo $harvestmins; ?>" align="right" size="2"></input><font color="blue"><strong>#</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Time After Dosing Of Harvest (Duration):</strong></td>
<td class="results">
<input name="duration" type="text" value="<?php echo $arrayduration; ?>" align="right"></input><font color="blue"><strong># Enter a zero if no value.</strong></font>
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

<!-- commenting these fields out since they are not useful to the user
<tr>
<td class="questionparameter" ><strong>Visible to Public:</strong></td>
<td class="results">
<input type="radio" name="visible" value="1" checked onclick="changeAccessNumber()">Yes
<input type="radio" name="visible" value="0" onclick="changeAccessNumber()"> No
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Access Number:</strong></td>
<td class="results">
<input name="accessnumber" type="text" value="1" align="right"></input><font color="blue"><strong>#</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Data File:</strong></td>
<td class="results">
<input name="file" type="file"><font color="red"><strong>*</strong></font>
</td>
</tr>
-->

<tr>
<td>
<?php

if($arrayid == ""){
?>
<input type="submit" name="submit" value="Submit">
<!--
<button dojoType="dijit.form.Button" type="submit" name="submit" value="Submit">Submit</button>
-->
<?php
}else{
?>
<!--
<button dojoType="dijit.form.Button" type="submit" name="submit" value="updatearrayinfo">Update Array Info</button>
-->
	<input type="submit" name="submit" value="Submit">
<?php
}
?>



</td>
<td>


<input type="reset" value="Reset Form"></td>
</tr>

</table>


</form>
<?php
}  // end if not submitted....
	else{
		//analyze($_POST);
		if (!isset($_POST['commit']) && $arrayedit == "true") { // if this has been sent for confirmation and this is an array being edited....
			//echo "This array is being edited...<br>";
			$fileError = 0;
			/*
	// Stuff to deal w/ the file to upload....
	$my_uploader = new uploader('en'); // errors in English

			$my_uploader->max_filesize(30000000);
			//$my_uploader->max_image_size(800, 800);
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
			*/
	// Fetch chemical name from db only when other is not selected... the other button has no value assigned to it and works for ''
	if($chemical != '')
	{
		//echo "yes, it's a number";
		//echo $chemical;
		$chemSQL = "SELECT DISTINCT chemical FROM chem WHERE chemid = $chemical";

		$chemResult = $db->Execute($chemSQL);//mysql_query($chemSQL, $db);

		$thischemical= $chemResult->FetchRow();//mysql_fetch_row($chemResult);
		$treatment = $thischemical[0];
	}

	?>
		<form name="commitnewsample" action="./agilentexperiment-useradmin.php?new-edit_array=1" method="post">
		<input type="hidden" name="submitted" value="true">
		<input type="hidden" name="arrayedit" value="<?php echo $arrayedit; ?>">
		<input type="hidden" name="userid" value="<?php echo $userid; ?>">
		<input type="hidden" name="arrayid" value="<?php echo $arrayid; ?>">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="2">Array Form Entries <br>Please check for accuracy!</th>
		</tr>

		<!--
		<tr>
		<td class="questionparameter" ><strong>Treatment ID:</strong></td>
		<td class="results">
		<input type="hidden" name="trxid" value="<?php echo $trxid; ?>">
		<input type="hidden" name="userid" value="<?php echo $userid; ?>">
		<?php echo $trxid; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Publication Info:</strong></td>
		<td class="results">
		<input type="hidden" name="pubinfo" value="<?php echo $pubinfo; ?>">
		<?php echo $pubinfo; ?>
		</td>
		</tr>
		-->
<?php
	//  Need to check to see if the name already exists in the database...
	$namesql = "SELECT * FROM agilent_arrayinfo WHERE arraydesc LIKE \"$samplename\"";
	$nameResult = $db->Execute($namesql);//mysql_query($namesql, $db);
	$namerow = $nameResult->FetchRow();//mysql_fetch_row($nameResult);
	if($namerow[0] != ""){
	//echo "number: ".count($namerow);
	//echo "$namesql<br>";
		echo "<b><font color=\"red\">this name already exists in the database.  renaming this array to $samplename (replicate)</font><br>";
		$replicate = "(replicate)";

	}





?>
		<tr>
		<td class="questionparameter" ><strong>Sample Name:</strong></td>
		<td class="results">
		<input type="hidden" name="samplename" value="<?php echo "$samplename$replicate"; ?>">
		<?php echo "$samplename$replicate"; ?>
		</td>
		</tr>
<?php

			$expidSQL = "SELECT expid FROM agilent_experiments WHERE arrayid = $arrayid";
			//echo "$expidSQL<br>";
			$expidResult = $db->Execute($expidSQL);//mysql_query($expidSQL, $db);
			$row = $expidResult->FetchRow();//mysql_fetch_row($expidResult);
			$selectedExperimentArray = $row[0];
?>
		<tr>
		<td class="questionparameter" ><strong>Associated Experiment Name:</strong></td>
		<td class="results">
		<input type="hidden" name="expID" value="<?php echo $selectedExperimentArray; ?>">
		<?php



			$expSQL = "SELECT * FROM agilent_experimentsdesc WHERE expid = $selectedExperimentArray";
			//echo "$expSQL<br>";
			$countResult = $db->Execute($expSQL);//mysql_query($expSQL, $db);
			$row = $countResult->FetchRow();//mysql_fetch_row($countResult);
			$experimentID = $row[0];
			$experimentName = $row[1];

		?>
		<?php echo "Experiment #$experimentID ".":".$experimentName; ?>
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

		/* Dont do this since this adds new entry to chem table
		if($otherChemical != ''){

			// Get the new sample id....
  			$countSQL = "Select MAX(chemid) from chem";
			$countResult = mysql_query($countSQL, $db);
			$row = mysql_fetch_row($countResult);
			$maxchemID = $row[0];
			$newchemid = $maxchemID + 1;

			// add new entry in chem table for this new chemical entered by user
			$insertSQL = "INSERT chem(chemid, chemical) VALUES($newchemid, \"$otherChemical\")";
			$insertResult = mysql_query($insertSQL, $db);

			if(mysql_errno($db)){
			echo "<strong>Database Error adding new chemical to chem table </strong><br>";
			}
		}
		*/

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
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Access Number:</strong></td>
		<td class="results">
		<input type="hidden" name="accessnumber" value="<?php echo $accessnumber; ?>">
		<?php echo $accessnumber; ?>
		</td>
		</tr>


		<tr>
		<td class="questionparameter" ><strong>File:</strong></td>
		<td class="results">
		<input type="hidden" name="file" value="<?php echo "$filename"; ?>">
		//<?php
		//if($fileError != 1){
			//echo $filename;
		//}
		//else{
			//echo "<strong><font color=\"red\">$fileErrorText</font></strong>";
		//}
		//?>
		</td>
		</tr>
		-->
	</table>
	<tr>
	<?php
		//if($fileError != 1){
			echo "<td><input type=\"submit\" name=\"commit\" value=\"Correct? Commit to Database!\"></td>";
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
		// Do we have to edit a previously entered array?  do we have to add an array to the arrayqueue?  do we have to create a new array?
		if($arrayedit == "true"){

			//echo "Your array has been updated... only a TEST!!!!";
			/*
				# [samplename] 238-A7
# [expID] 18
# [chemical] 108
# [treatment] NONE
# [otherChemical]
# [organism] Mouse
# [otherOrganism]
# [rnagroupsize] 1
# [concentration] 4.5
# [strain] C57BL/6
# [otherStrain]
# [genevariation] None
# [otherGenevariation]
# [age] 9
# [sex] M
# [tissue] Liver
# [otherTissue]
# [vehicle] None
# [otherVehicle]
# [dose] 0
# [doseunit]
# [route] None
# [otherRoute]
# [control] None
# [otherControl]
# [reference]
# [otherReference]
# [dosagetime] 00:00:00
# [harvesttime] 15:00:00
# [duration] 0
# [durationunit] None


			*/
// Need to deal w/ an organism changing and what type of array we are dealing w/
//analyze($_SESSION);
// For now i'm taking out organism and arraytype from the update values list...
$userid = $_SESSION['userid'];
if($userid == ""){
	$userid = $_SESSION['userid'];
}
		// Take the old values associated w/ this array and insert them into a provenance table....

		$provenanceSQL = "INSERT INTO agilent_arrayinfo_provenance (arrayid, arraytype, arraydesc, organism, rnagroupsize, rna_concentration, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, gestationperiod, expid, experiment, submitterid, referencesample) SELECT arrayid, arraytype, arraydesc, organism, rnagroupsize, rna_concentration, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, gestationperiod, expid, experiment, submitterid, referencesample FROM agilent_arrayinfo WHERE arrayid = $arrayid";
		//echo "$provenanceSQL<br>";
		$provenanceResult = $db->Execute($provenanceSQL);//mysql_query($provenanceSQL, $db);
		if(!$provenanceResult){
			echo "uh-oh db error";
			echo " trxid is " . $trxid;
			echo $provenanceResult;
		}


			// Need to get the POST values and update the agilent_arrayinfo table accordingly...
			// Add the treatment to sample....
		$sql = "UPDATE agilent_arrayinfo SET arraydesc = \"$samplename\", organism = \"$organism\", rnagroupsize = $rnagroupsize, rna_concentration = $concentration, strain=\"$strain $otherStrain\", genevariation=\"$genevariation $otherGenevariation\", age=$age, sex='$sex', tissue=\"$tissue $otherTissue\", treatment=\"$treatment $otherChemical\", vehicle=\"$vehicle $otherVehicle\", dose=$dose, route=\"$route $otherRoute\", dosagetime=\"$dosagetime\", duration=$duration, harvesttime=\"$harvesttime\", control=\"$control $otherControl\", doseunits=\"$doseunit\", durationunits=\"$durationunit\", pregnant=\"$pregnant\", gestationperiod=\"$gestation\", expid=$expID, experiment=\"$expName\", dateprocessed=NOW(), submitterid=$userid, referencesample=\"$reference $otherReference\" WHERE arrayid = $arrayid";
		//echo "$sql<br>";


		$newTreatmentResult = $db->Execute($sql);//mysql_query($sql, $db);

		$trxsampledata = "";
		if(!$newTreatmentResult){
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
		}
		?>


			<table class="question" width="400">
		  	<thead>
		 	<tr>
		  	<th class="mainheader" colspan="2">Array Information Update Results</th>
			</tr>
			<tr>
			<td class="questionparameter" ><strong>Array Update:</strong></td>
			<td class="results">
			<?php echo $trxsampledata; ?>
			</td>
			</tr>
			</table>

<?php

		}else{

		

		// Now insert data into the agilent_array_queue table
		$countSQL = "Select MAX(queue_arrayid) from agilent_array_queue";
		$countResult = $db->Execute($countSQL);//mysql_query($countSQL, $db);
		$row = $countResult->FetchRow();//mysql_fetch_row($countResult);

		$maxarrayID = $row[0];
		if(is_nan($maxarrayID)){
			$maxarrayID = 0;
		}
		$newarrayid = $maxarrayID + 1;
		$name = $_SESSION['firstname']." ".$_SESSION['lastname'];
		// Add the treatment to sample....
		$sql = "INSERT agilent_array_queue (queue_arrayid, array_queue_samplename, organism, rnagroupsize, concentration, strain, genevariation, age, sex, tissue, 	treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, gestationperiod, expid, experiment, datesubmitted, submitter, reference_sample)
			VALUES ($newarrayid, \"$samplename\", \"$organism $otherOrganism\", $rnagroupsize, $concentration, \"$strain $otherStrain\", \"$genevariation $otherGenevariation\", $age, '$sex',\"$tissue $otherTissue\", \"$treatment $otherChemical\", \"$vehicle $otherVehicle\", $dose, \"$route $otherRoute\", \"$dosagetime\", $duration, \"$harvesttime\", \"$control $otherControl\", \"$doseunit\", \"$durationunit\",  \"$pregnant\",  \"$gestation\", \"$expID\", \"$expName\", NOW(),\"$name\", \"$reference $otherReference\")";


		$newTreatmentResult = $db->Execute($sql);//mysql_query($sql, $db);
		//echo $sql;
		$trxsampledata = "";
		if(!$newTreatmentResult){
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
}
		?>


			<table class="question" width="400">
		  	<thead>
		 	<tr>
		  	<th class="mainheader" colspan="2">RNA Sample Submission Results</th>
			</tr>
			<tr>
			<td class="questionparameter" ><strong>New RNA Sample Creation:</strong></td>
			<td class="results">
			<?php echo $trxsampledata; ?>
			</td>
			</tr>
			</table>
		<a href="./agilentexperiment-useradmin.php??new-edit_array=1">Enter another RNA sample?</a>
<script type="text/javascript">
alert("You've successfully entered your array.")
<!--
//window.location = "http://edge.oncology.wisc.edu/agilentexperiment-useradmin.php"
-->

</script>


		<?php



	}// end of else where we are checking to see what table to update/add to....
	}// end of initial submit....
}
?>
