<?php

/*
scriptname: newagilentrnasample-nonadmin.inc
purpose:  This script is used to submit an rna sample into the queue.



*/
if($userid == 1){
	/*echo "POST vars<BR>";
	analyze($_POST);
	echo "FILES vars<BR>";
	analyze($_FILES);
	*/
}
$debug=-1;
if(!isset($_SESSION['userid'])){
	die("You need to login to use this function.");
}
$userid = $_SESSION['userid'];
//
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
	if(isset($_POST['sampleidedit'])){
		$sampleidedit = $_POST['sampleidedit'];
	}else{
		$sampleidedit = "";
	}
	#echo "sampleidedit = $sampleidedit @ line 36<BR>";
	if(isset($_POST['userid'])){
		$userid = $_POST['userid'];
	}else{
		$userid = "";
	}
	if(isset($_POST['sampleid'])){
		$sampleid = $_POST['sampleid'];
	}else{
		$sampleid = "";
	}
	if(isset($_POST['samplename'])){
		$samplename = $_POST['samplename'];
	}else{
		$samplename = "";
	}
	if(isset($_POST['treatment'])){
		$treatment = $_POST['treatment'];
	}else{
		$treatment = "";
	}
	if(isset($_POST['organism'])){
		$organism = $_POST['organism'];
	}else{
		$organism = "";
	}
	if(isset($_POST['rnagroupsize'])){
		$rnagroupsize = $_POST['rnagroupsize'];
	}else{
		$rnagroupsize = "";
	}
	
	if(isset($_POST['concentration'])){
		$concentration = $_POST['concentration'];
	}else{
		$concentration = "";
	}
	if(isset($_POST['strain'])){
		$strain = $_POST['strain'];
	}else{
		$strain = "";
	}
	if(isset($_POST['genevariation'])){
		$genevariation = $_POST['genevariation'];
	}else{
		$genevariation = "";
	}
	if(isset($_POST['age'])){
		$age = $_POST['age'];
	}else{
		$age = "";
	}
	if(isset($_POST['ageunit'])){
		$ageunit = $_POST['ageunit'];
	}else{
		$ageunit = "";
	}
	if(isset($_POST['sex'])){
		$sex = $_POST['sex'];
	}else{
		$sex = "";
	}
	if(isset($_POST['tissue'])){
		$tissue = $_POST['tissue'];
	}else{
		$tissue = "";
	}
	if(isset($_POST['vehicle'])){
		$vehicle = $_POST['vehicle'];
	}else{
		$tissue = "";
	}
	if(isset($_POST['dose'])){
		$dose = $_POST['dose'];
	}else{
		$dose = "";
	}
	if(isset($_POST['doseunitid'])){
		$doseunitid = $_POST['doseunitid'];
	}else{
		$doseunitid = "";
	}
	if(isset($_POST['route'])){
		$route = $_POST['route'];
	}else{
		$route = "";
	}
	if(isset($_POST['dosehours'])){
		$dosehours = $_POST['dosehours'];
	}else{
		$dosehours = "";
	}
	if(isset($_POST['doseminutes'])){
		$doseminutes = $_POST['doseminutes'];
	}else{
		$doseminutes = "";
	}
	if(isset($_POST['harvesthours'])){
		$harvesthours = $_POST['harvesthours'];
	}else{
		$harvesthours = "";
	}
	if(isset($_POST['harvestminutes'])){
		$harvestminutes = $_POST['harvestminutes'];
	}else{
		$harvestminutes = "";
	}
	if(isset($_POST['duration'])){
		$duration = $_POST['duration'];
	}else{
		$duration = "";
	}
	if(isset($_POST['durationunit'])){
		$durationunit = $_POST['durationunit'];
	}else{
		$durationunit = "";
	}
	if(isset($_POST['control'])){
		$control = $_POST['control'];
	}else{
		$control = "";
	}
	if(isset($_POST['reference'])){
		$reference = $_POST['reference'];
	}else{
		$reference = "";
	}
	if(isset($_POST['submit'])){
		$submit = $_POST['submit'];
	}else{
		$submit = "";
	}
	if(isset($_POST['expID'])){
		$expID = $_POST['expID'];
	}else{
		$expID = "";
	}
	if(isset($_POST['control'])){
		$control = $_POST['control'];
	}else{
		$control = "";
	}
	if(isset($_POST['pregnant'])){
		$pregnant = $_POST['pregnant'];
	}else{
		$pregnant = "";
	}
	if(isset($_POST['gestation'])){
		$gestation = $_POST['gestation'];
	}else{
		$gestation = "";
	}
	if(isset($_POST['reference'])){
		$reference = $_POST['reference'];
	}else{
		$reference = "";
	}
	if(isset($_POST['queuestatus'])){
		$queuestatus = $_POST['queuestatus'];
	}else{
		$queuestatus = "";
	}
	if(isset($_POST['dosagetime'])){
		$dosagetime = $_POST['dosagetime'];
	}else{
		$dosagetime = "";
	}
	if(isset($_POST['harvesttime'])){
		$harvesttime = $_POST['harvesttime'];
	}else{
		$harvesttime = "";
	}
	if(isset($_POST['info'])){
		$info = $_POST['info'];
	}else{
		$info = "";
	}
	if(isset($_POST['file1desc'])){
		$file1desc = $_POST['file1desc'];
		if($file1desc != ""){
			if(isset($_FILES['file1'])){
				$firstfile = $_FILES['file1'];
			}
			
		}else{
			$firstfile = "";
		$file1desc = "";
		}
	}else{
		$firstfile = "";
		$file1desc = "";
	}
	if(isset($_POST['file2desc'])){
		$file2desc = $_POST['file2desc'];
		if($file2desc != ""){
			if(isset($_FILES['file2'])){
				$secondfile = $_FILES['file2'];
			}
		}else{
			$secondfile = "";
			$file2desc = "";
		}
		
	}else{
		$secondfile = "";
		$file2desc = "";
	}
	if(isset($_POST['file3desc'])){
		$file3desc = $_POST['file3desc'];
		if($file3desc != ""){
			if(isset($_FILES['file3'])){
				$thirdfile = $_FILES['file3'];
			}
		}else{
			$thirdfile = "";
			$file3desc = "";
		}
		
	}else{
		$thirdfile = "";
		$file3desc = "";
	}
	if($pregnant == ""){
		$pregnant = "No";
	}
	if($gestation == ""){
		$gestation = -1;
	}
	if($queuestatus == ""){
		$queuestatus = 0;
	}

}else{ # POST is not set....

	#$sampleidedit = "true";  // this just tells us that the sample is being edited....
}
#analyze($_GET);
$sampleidedit = false;

	#echo "sampleidedit = $sampleidedit @ line 248<BR>";
if(isset($_GET['sampleid'])){
	$sampleid = $_GET['sampleid'];
	
	$sampleidedit = true;
	
	#echo "sampleidedit = $sampleidedit @ line 254<BR>";
}else{
	$sampleid = "";
	
	
}
if(isset($_GET['previous'])){
	$sampleid = $_GET['previous'];
	$previous = 1;
	# need to check to make sure that a value was not manually entered into the url and that sample is owned by the user logged in or the user is an admin
	if($privval != 99){
		$checkSQL = "SELECT sampleid FROM agilent_rnasample WHERE sampleid = $sampleid AND submitterid = $userid";
	}else{
		$checkSQL = "SELECT sampleid FROM agilent_rnasample WHERE sampleid = $sampleid";
	}
		$checkResult = $db->Execute($checkSQL);
		$recordcount = $checkResult->RecordCount();
		if($recordcount < 1){
			die("<h3><font color='red'>ERROR:</font> Invalid ID in url.  If you feel you've reached this page in error, contact the administrator.</h3>");
		}

}else{
	//$sampleid = "";
	$previous = -1;
}
$val = count($_POST);
# if we are dealing w/ 
if(($sampleid != "" || $previous != -1) && $val==0){
		#echo "Editing....<br>";
		// We need to treat this form as an edit form, not as a new array form.
		// get the values associated with it in the agilent_arrayinfo table.
		$sql = "SELECT * FROM agilent_rnasample WHERE sampleid = $sampleid";

		#echo "<br>$sql<br>";
		//$db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sampleidResult = $db->Execute($sql);//mysql_query($sql,$db);
		$assocResult = $sampleidResult->FetchRow();//mysql_query($sql,$db);
		//$sampleidinfo = mysql_fetch_row($sampleidResult);
		$sampleidResult = $db->Execute($sql);
		$assocrow = $sampleidResult->FetchRow(); //mysql_fetch_assoc($assocResult);
//analyze($sampleidinfo);
		
		//$sampleidtype = $sampleidinfo[1];
		//analyze($assocrow);
		//echo "$sampleidname<br>";
		$sampleidorganism = $assocrow['organism'];//$sampleidinfo[2];
		$sampleidrnagroupsize = $assocrow['rnagroupsize'];//$sampleidinfo[3];
		$sampleidrnaconcentration = $assocrow['concentration'];//$sampleidinfo[4];
		$sampleidstrain = $assocrow['strain'];//$sampleidinfo[5];
		
		$sampleidgenevariation = $assocrow['genevariation'];//$sampleidinfo[6];
		$sampleidage = $assocrow['age'];//$sampleidinfo[7];
		$sampleidageunits = $assocrow['ageunits'];
		$sampleidsex = $assocrow['sex'];//$sampleidinfo[8];
		$sampleidtissue = $assocrow['tissue'];//$sampleidinfo[9];
		$sampleidtreatment = $assocrow['treatment'];//$sampleidinfo[10];
		$sampleidvehicle = $assocrow['vehicle'];//$sampleidinfo[11];
		$sampleiddose = $assocrow['dose'];// $sampleidinfo[12];
		$sampleidroute = $assocrow['route'];//$sampleidinfo[13];
		$sampleiddosagetime = $assocrow['dosagetime'];//$sampleidinfo[14];
		$sampleidduration = $assocrow['duration'];//$sampleidinfo[15];
		$sampleidharvesttime = $assocrow['harvesttime'];//$sampleidinfo[16]
		$sampleiddoseunits = $assocrow['doseunits'];//$sampleidinfo[18];
		$sampleiddurationunits = $assocrow['durationunits'];//$sampleidinfo[19];
		$sampleidpregnant = $assocrow['pregnant'];//$sampleidinfo[20];
		$sampleidname = $assocrow['samplename'];//$sampleidinfo[21];
		$sampleidgestationperiod = $assocrow['gestationperiod'];//$sampleidinfo[22];
		$sampleiddatesubmitted = $assocrow['datesubmitted'];//$sampleidinfo[25];
		$sampleiddateprocessed = $assocrow['dateprocessed'];//
		$sampleidsubmitterid = $assocrow['submitterid'];//$sampleidinfo[26];
		$sampleidinfo = $assocrow['info'];
		//$sampleidreferencesample = $assocrow[''];//$sampleidinfo[27];
		//$sampleidownerid = $sampleidinfo[28];
		$status = $assocrow['queuestatus'];//$sampleidinfo[29];
		//analyze($sampleidinfo);
		$width = 600;
		$tableid = "results";
		#echo "$assocrow<br>";
		//array2table($assocrow, $width, $tableid);
		#if($previous != 1){
			#$sampleidedit = "true";  // this just tells us that the array is being edited....
		#}else{
		#	$sampleidedit = "false";
		#}
	}

if(!isset($submitted)){
	$submitted = false;
}
if($submitted != true){

	
	$chemMenu = "";
	$organismMenu = "";
	if(!isset($sampleidstrain)){
		$sampleidstrain = "";
	}
	if(!isset($sampleidgenevariation)){
		$sampleidgenevariation = "";
	}
	$genevariationMenu = "";
	$tissueMenu = "";
	$vehicleMenu = "";
	$doseunitMenu = "";
	$routeMenu = "";
	$durationunitMenu = "";
	$ageunitMenu = "";
	$referenceMenu = "";
		// GETTING THE VALUES TO POPULATE THE SELECTIONS.....
		$chemSQL = "SELECT DISTINCT chemid, chemical FROM chem ORDER BY chemical ASC";
		$chemResult = $db->Execute($chemSQL);// mysql_query($chemSQL, $db);
		$firstchoice = 1;
		
		#while(list($chemid, $chemical) = mysql_fetch_array($chemResult))
		while($row=$chemResult->FetchRow())
		 {
			$chemid = $row[0];
			$chemical = $row[1];
		 	if(is_numeric($sampleid) && $chemid == $sampleidtreatment){
				$checked = "selected";
				
				#echo "checked with chemid = $chemid and sampleid = $sampleid<br>";
			}else{
				$checked = "";
				
			}
		 if($firstchoice == 1){
			if(!is_numeric($sampleid)){
		 		$chemMenu .= "<option value='$chemid' selected>$chemical</option>";#"<input type=\"radio\" name=\"treatment\" value=\"$chemid\" checked onClick= \"return drawboxOtherRNASample('otherChemical',true)\">$chemical  ";
			}else{
				
				$chemMenu .= "<option value='$chemid' $checked>$chemical</option>";#$chemMenu .= "<input type=\"radio\" name=\"treatment\" value=\"$chemid\" $checked onClick= \"return drawboxOtherRNASample('otherChemical',true)\">$chemical  ";
			}
		 $firstchoice++;
		 }
		elseif($firstchoice%5==0){
		 	$chemMenu .= "<option value='$chemid' $checked>$chemical</option>";#$chemMenu .= "<input type=\"radio\" name=\"treatment\" value=\"$chemid\" $checked onClick= \"return 											drawboxOtherRNASample('otherChemical',true)\">$chemical "."<br>";
		 $firstchoice++;
		 }
		else{
		 	$chemMenu .= "<option value='$chemid' $checked>$chemical</option>";#$chemMenu .= "<input type=\"radio\" name=\"treatment\" value=\"$chemid\" $checked onClick= \"return 											drawboxOtherRNASample('otherChemical',true)\">$chemical  ";
		 $firstchoice++;
		 }
		}
		// Lets add an entry for "other" to enable user to enter a chemical not found in list
		//$chemMenu .= "<input type=\"radio\" name=\"treatment\" value=\"-99\" onClick= \"return drawboxOtherRNASample('otherChemical',false)\">Other ";


		$organismSQL = "SELECT organismid, organism FROM organism ORDER BY organismid ASC";
		$organismResult = $db->Execute($organismSQL);//mysql_query($organismSQL, $db);
		$firstchoice = 1;
		$numOrganisms = $organismResult->RecordCount();//mysql_num_rows($organismResult);
		//echo "Array organism: $sampleidorganism<br>";
		//while(list($id, $organism) = mysql_fetch_array($organismResult))
		#while(list($id, $organism) = mysql_fetch_array($organismResult))
		while($row = $organismResult->FetchRow())
		{
			$id = $row[0];
			$organism = $row[1];
		
			if($sampleid != "" && $id == $sampleidorganism){
				$checked = "selected";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($sampleid == ""){
					$organismMenu .= "<option value='$id' selected onClick= \"toggleLayerOn('strain', $numOrganisms, $id); toggleLayerOn('variation', $numOrganisms, $id);\">$organism</option>";#"<input type=\"radio\" name=\"organism\" value=\"$id\" checked onClick= \"toggleLayerOn('strain', $numOrganisms, $id); toggleLayerOn('variation', $numOrganisms, $id);return drawboxOtherRNASample('otherOrganism',true)\">$organism  ";
				}else{
					$organismMenu .=  "<option value='$id' $checked onClick=\"toggleLayerOn('strain', $numOrganisms, $id); toggleLayerOn('variation', $numOrganisms, $id);\">$organism</option>";#"<input type=\"radio\" name=\"organism\" value=\"$id\" $checked onClick= \"toggleLayerOn('strain', $numOrganisms, $id); toggleLayerOn('variation', $numOrganisms, $id);return drawboxOtherRNASample('otherOrganism',true)\">$organism  ";

				}
				$firstchoice = 0;
			}
			else{
				$organismMenu .=  "<option value='$id' $checked onClick= \"toggleLayerOn('strain', $numOrganisms, $id); toggleLayerOn('variation', $numOrganisms, $id);\">$organism</option>";#"<input type=\"radio\" name=\"organism\" value=\"$id\" $checked onClick= \"toggleLayerOn('strain', $numOrganisms, $id); toggleLayerOn('variation', $numOrganisms, $id);return drawboxOtherRNASample('otherOrganism',true)\">$organism  ";
			}
		}
	
		$straindivarray = array();  // This will be an array of strings.....
		$counterarray = array(); // This will hold the counts of the different organism ids....
		$straindivsql = "SELECT DISTINCT organismid FROM organism ORDER BY organismid ASC";
		$straindivresult = $db->Execute($straindivsql);//mysql_query($straindivsql,$db);
		#while($row = mysql_fetch_row($straindivresult)){

		while($row = $straindivresult->FetchRow()){
			$newdivstring = "";
			
			$newdivstring .= "<div id='strain$row[0]'>";
			//$newdivstring .= "<div id='strain$row[0]' style=\"display: none\">";
			array_push($straindivarray,$newdivstring);
			array_push($counterarray, 0);
		}
		$strainSQL = "SELECT strainid, strain, organismid FROM strain ORDER BY strainid ASC";
		$strainResult = $db->Execute($strainSQL);//mysql_query($strainSQL, $db);
		$firstchoice = 1;
		//$counter = 0;
		#echo $strainSQL."<br>";
		$strainMenu = "";
		$checked = 0;
		$checkedstr = "";
		#while(list($strainid, $strain, $organismid) = mysql_fetch_array($strainResult))
		while($row = $strainResult->FetchRow())
		{
			$strainid = $row[0];
			$strain = $row[1];
			$organismid = $row[2];
			
			if($sampleid != "" && $strainid == $sampleidstrain){
				#echo "Sample id is set with a value: $sampleid<br>";
				#echo "strainid is : $strainid and sampleidstrain is $sampleidstrain<br>";
				#echo "The strain is $strain<br>";
				$checked = 1;
				$checkedstr = "checked";
			}
			if($sampleid == ""){
				if($strainid != 0){  // 0 is the value of NONE
				$straindivarray[$organismid] .= "<input type=\"radio\" name=\"strain\" value=\"$strainid\" $checkedstr onClick= \"return drawboxOtherRNASample('otherStrain',true)\">$strain";
				}
			}else{
				if($strainid != 0){  // 0 is the value of NONE
				$straindivarray[$organismid] .= "<input type=\"radio\" name=\"strain\" value=\"$strainid\" $checkedstr onClick= \"return drawboxOtherRNASample('otherStrain',true)\">$strain  ";
				}
			}
		

			if($counterarray[$organismid]%5 == 0 && $counterarray[$organismid]!=0){
				$straindivarray[$organismid] .= "<br>";
			}
			if($strainid == 0){	// 0 is the value of NONE
				if($sampleidstrain == ""){
					$checkedstr = "checked";
				}
					$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"$strainid\" $checkedstr onClick= \"return drawboxOtherRNASample('otherStrain',true)\">$strain  ";
				
			}
			$counterarray[$organismid]++;
			$checkedstr = "";
		}
		

		
		$variationdivarray = array();  // This will be an array of strings.....
		$counterarray = array(); // This will hold the counts of the different organism ids....
		$variationdivsql = "SELECT DISTINCT organismid FROM organism ORDER BY organismid ASC";
		$variationdivresult = $db->Execute($variationdivsql);//mysql_query($variationdivsql,$db);
		#while($row = mysql_fetch_row($variationdivresult)){
		while($row = $variationdivresult->FetchRow()){
			$newdivstring = "";
			
			$newdivstring .= "<div id='variation$row[0]'>";
			//$newdivstring .= "<div id='strain$row[0]' style=\"display: none\">";
			array_push($variationdivarray,$newdivstring);
			array_push($counterarray, 0);
		}

		$genevariationSQL = "SELECT genevariationid, genevariation, organismid FROM genevariation ORDER BY genevariationid ASC";
		$genevariationResult = $db->Execute($genevariationSQL);//mysql_query($genevariationSQL, $db);
		$checkedstr="";
		#while(list($genevariationid, $genevariation, $organismid) = mysql_fetch_array($genevariationResult))
		while($row=$genevariationResult->FetchRow())
		{
			$genevariationid = $row[0];
			$genevariation = $row[1];
			$organismid = $row[2];
			//echo " $genevariationid : $sampleidgenevariation<br>";
			if($sampleid != "" && $genevariationid == $sampleidgenevariation){
				//echo "gene variation is $genevariation<br>";
				$checked = 1;
				$checkedstr = "checked";
			}
		
			if($sampleid == ""){ // 0 is the value of NONE
				if($genevariationid != 0){
				$variationdivarray[$organismid] .= "<input type=\"radio\" name=\"genevariation\" value=\"$genevariationid\" $checkedstr onClick= \"return drawboxOtherRNASample('otherGenevariation',true)\">$genevariation  ";
				}
			}else{
				if($genevariationid != 0){// 0 is the value of NONE
				$variationdivarray[$organismid] .= "<input type=\"radio\" name=\"genevariation\" value=\"$genevariationid\" $checkedstr onClick= \"return drawboxOtherRNASample('otherGenevariation',true)\">$genevariation  ";
				}
			}
			if($counterarray[$organismid]%5 == 0 && $counterarray[$organismid]!=0){
				$variationdivarray[$organismid] .= "<br>";
			}
			if($genevariationid == 0){// 0 is the value of NONE
				if($sampleidgenevariation == ""){
					$checkedstr = "checked";
				}
					$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" $checkedstr value=\"$genevariationid\" onClick= \"return drawboxOtherRNASample('otherGenevariation',true)\">$genevariation  ";
			}
			$counterarray[$organismid]++;	
			$checkedstr = "";
		}
		
	


		$tissueSQL = "SELECT tissueid, tissue FROM tissue ORDER BY tissueid ASC";
		$tissueResult = $db->Execute($tissueSQL);//mysql_query($tissueSQL, $db);
		$firstchoice = 1;
		$counter = 0;
		#while(list($tissueid, $tissue) = mysql_fetch_array($tissueResult))
		while($row=$tissueResult->FetchRow())
		{
			$tissueid = $row[0];
			$tissue = $row[1];
			if($sampleid != "" && $tissueid == $sampleidtissue){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($sampleid == ""){
					$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissueid\" checked onClick= \"return drawboxOtherRNASample('otherTissue',true)\">$tissue  ";
				}else{
					$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissueid\" $checked onClick= \"return drawboxOtherRNASample('otherTissue',true)\">$tissue  ";
				}
				$firstchoice = 0;
			}
			else{
				$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissueid\" $checked onClick= \"return 										drawboxOtherRNASample('otherTissue',true)\">$tissue  ";
				if($counter%5 == 0){
					$tissueMenu .= "<br>";
				}
			}
			$counter++;
		}
	
		$vehicleSQL = "SELECT vehicleid, vehicle FROM vehicle ORDER BY vehicleid ASC";
		$vehicleResult = $db->Execute($vehicleSQL);//mysql_query($vehicleSQL, $db);
		$firstchoice = 1;
		$counter = 0;
		#while(list($vehicleid, $vehicle) = mysql_fetch_array($vehicleResult))
		while($row = $vehicleResult->FetchRow())
		{
			$vehicleid = $row[0];
			$vehicle = $row[1];
			#echo "vehicleid = $vehicleid and sampleidvehicle = $sampleidvehicle<br>";
			if($sampleid != "" && $vehicleid == $sampleidvehicle){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($sampleid == ""){
					$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicleid\" checked onClick= \"return drawboxOtherRNASample('otherVehicle',true)\">$vehicle  ";
				}else{
					$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicleid\" $checked onClick= \"return drawboxOtherRNASample('otherVehicle',true)\">$vehicle  ";
				}
				$firstchoice = 0;
			}
			else{
				$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicleid\" $checked onClick= \"return drawboxOtherRNASample('otherVehicle',true)\">$vehicle  ";
				if($counter%5 == 0){
					$vehicleMenu .= "<br>";
				}
			}
			$counter++;
		}
		
		$doseunitSQL = "SELECT doseunitid, doseunit FROM doseunit ORDER BY doseunitid ASC";
		$doseunitResult = $db->Execute($doseunitSQL);//mysql_query($doseunitSQL, $db);
		$firstchoice = 1;
		#while(list($doseunitid, $doseunit) = mysql_fetch_array($doseunitResult))
		while($row= $doseunitResult->FetchRow())
		{
			$doseunitid = $row[0];
			$doseunit = $row[1];
			if($sampleid != "" && $doseunitid == $sampleiddoseunits){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($sampleid == ""){
					$doseunitMenu .= "<input type=\"radio\" name=\"doseunitid\" value=\"$doseunitid\" checked>$doseunit  ";
				}else{
						$doseunitMenu .= "<input type=\"radio\" name=\"doseunitid\" value=\"$doseunitid\" $checked>$doseunit  ";
				}
				$firstchoice = 0;
			}
			else{
				$doseunitMenu .= "<input type=\"radio\" name=\"doseunitid\" $checked value=\"$doseunitid\" >$doseunit  ";
			}
		}


		$routeSQL = "SELECT routeid, route FROM route ORDER BY routeid";
		$routeResult = $db->Execute($routeSQL);//mysql_query($routeSQL, $db);
		$firstchoice = 1;
		#while(list($routeid, $route) = mysql_fetch_array($routeResult))
		while($row = $routeResult->FetchRow())
		{
			$routeid = $row[0];
			$route = $row[1];
			
			if($sampleid != "" && $routeid == $sampleidroute){
				$checked = "checked";
			}else{
				$checked = "";
			}
			//if($firstchoice == 1){
			if($firstchoice ==1){
				if($sampleid == ""){
					
						$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"$routeid\" checked onClick= \"return drawboxOtherRNASample('otherRoute',true)\">$route  ";
				}else{
					
					$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"$routeid\" $checked onClick= \"return drawboxOtherRNASample('otherRoute',true)\">$route  ";
				}
				$firstchoice = 0;
			}else{
				
				//if($routeid == 0){
				//echo "ROUTE ID = $routeid : $route<br>";
				$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"$routeid\" $checked onClick= \"return drawboxOtherRNASample('otherRoute',true)\">$route  ";
			}
			
		}
	

		$durationunitSQL = "SELECT durationunitid, durationunit FROM durationunit ORDER BY durationunitid";
		$durationunitResult = $db->Execute($durationunitSQL);//mysql_query($durationunitSQL, $db);
		$firstchoice = 1;
		#while(list($durationunitid, $durationunit) = mysql_fetch_array($durationunitResult))
		while($row=$durationunitResult->FetchRow())
		{
			$durationunitid = $row[0];
			$durationunit = $row[1];
			if($sampleid != "" && $durationunitid == $sampleiddurationunits){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($sampleid == ""){
					$durationunitMenu .= "<input type=\"radio\" name=\"durationunit\" value=\"$durationunitid\" checked>$durationunit  ";
				}else{
					$durationunitMenu .= "<input type=\"radio\" name=\"durationunit\" value=\"$durationunitid\" $checked>$durationunit  ";
				}
				$firstchoice = 0;
			}
			else{
				$durationunitMenu .= "<input type=\"radio\" name=\"durationunit\" value=\"$durationunitid\" $checked>$durationunit  ";
			}
		}


		$ageSQL = "SELECT ageunitid, ageunit FROM ageunit ORDER BY ageunitid ASC";
		$ageunitResult = $db->Execute($ageSQL);//mysql_query($ageSQL, $db);
		$firstchoice = 1;
		#while(list($ageunitid, $ageunit) = mysql_fetch_array($ageunitResult))
		while($row = $ageunitResult->FetchRow())
		{
			$ageunitid = $row[0];
			$ageunit = $row[1];
			if($sampleid != "" && $ageunitid == $sampleidageunits){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($sampleid == ""){
					$ageunitMenu .= "<input type=\"radio\" name=\"ageunit\" value=\"$ageunitid\" checked >$ageunit  ";
				}else{
					$ageunitMenu .= "<input type=\"radio\" name=\"ageunit\" value=\"$ageunitid\" $checked >$ageunit  ";
				}
				$firstchoice = 0;
			}
			else{
				$ageunitMenu .= "<input type=\"radio\" name=\"ageunit\" value=\"$ageunitid\" $checked >$ageunit  ";
			}

		}

		$arrayformstring = "New";
		$instructions = "Please completely fill out the form below.  Your RNA samples cannot be processed unless this form has been filled out completely for <font color=\"red\"><b>EACH</b></font> (distinct) RNA sample (including control RNA) that you send.  Thanks!a<br>";
if(!isset($sampleidname)){
	$sampleidname = "";
}

	#echo "sampleidedit = $sampleidedit @ line 733<BR>";
?>
<p>
<b>Instructions:</b>  <?php echo $instructions; ?>
</p>
<form enctype="multipart/form-data" name="agilentrnasample" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkForm2()">
<input type="hidden" name="userid" value="<?php echo $userid; ?>">
<input type="hidden" name="submitted" value="true">
<input type="hidden" name="sampleidedit" value="<?php echo "$sampleidedit"; ?>">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2">Edge<sup>3</sup> RNA Sample Form <font color="red"><strong>&nbsp&nbsp &nbsp&nbsp(*=Text Required)</strong></font><font color="blue"><strong>&nbsp&nbsp &nbsp&nbsp(#= Number Required)</strong></font></th>
</tr>
</thead>
<?php

$userid = $_SESSION['userid'];
	if($sampleid != ""  && !isset($_GET['previous'])){
		// If there's a sampleid var in GET, display this
	
?>
		<tr>
		<td class="questionparameter" ><strong>RNA Sample ID:</strong></td>
		<td class="results">
		<?php echo $sampleid; ?>
		
		<input type="hidden" name="sampleid" value="<?php echo $sampleid; ?>">
		</td>
		</tr>
<?php
	}

	// NOT SURE WHY THIS CODE IS HERE (AT LEAST THE CHECK OF THE FIRST CONDITION....
	if(!isset($queuenumber)){
		$queuenumber = "";
	}
	if($queuenumber != ""  && !isset($_GET['previous'])){
		// If there's a queuenumber in GET then....
		// this is an array submitted into the queue....
?>
		
		<tr>
		<td class="questionparameter" ><strong>New RNA Sample ID:</strong></td>
		<td class="results">
		<?php echo $newsampleid; ?>
		<input type="hidden" name="trxid" value="<?php echo $newsampleid; ?>">
		
		<input type="hidden" name="queuestatus" value="$queuenumber">
		</td>
		</tr>
<?php
		}else{
?>
		<input type="hidden" name="queuestatus" value="0">
<?php
		}



?>


<tr>
<td class="questionparameter" ><strong>RNA Sample Name:</strong></td>
<td class="results">
<input name="samplename" type="text" align="left" size="100" value="<?php echo $sampleidname; ?>"></input><font color="red"><strong>*</strong></font>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Treatment/Condition:</strong></td>
<td class="results">
<select name='treatment'><?php echo $chemMenu; ?></select>

<!--<input name='otherChemical' id='otherChemical' type="text" value="" align="left" disabled=true></input>-->
<br>
<a href="./phpinc/editchemicalattribute.php" target="_blank" onclick="alert('After adding a chemical/condition, you must refresh this page to see your new chemical/condition')">Click here to add chemical or condition if not shown</a>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Organism:</strong></td>
<td class="results">
<select name='organism'><?php echo $organismMenu; ?></select>
</td>
</tr>
<?php
	
	if(isset($sampleidrnagroupsize)){
		$thisrnagroupsize = $sampleidrnagroupsize;
	}else{
		$thisrnagroupsize = 1;
	}

?>
<tr>
<td class="questionparameter" ><strong>RNA Group Size</strong><br>(If RNA is pooled,<br>then enter number<br> of animals, else enter 1):</td>
<td class="results">
<input name="rnagroupsize" type="text" value="<?php echo $thisrnagroupsize; ?>" align="right"></input><font color="blue"><strong>#</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>RNA Concentration of RNA sample Submitted to us.</strong></td>
<td class="results">
<?php

if(!isset($sampleidrnaconcentration)){
	$sampleidrnaconcentration = 0.15;
}
?>
<input name="concentration" type="text" value="<?php echo $sampleidrnaconcentration; ?>" align="right"></input><font color="blue"><strong>microGram/microlitre</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Strain:</strong></td>
<td class="results">
<?php 
	foreach($straindivarray as $div){

			echo $div."</div>";;
	}


	echo $strainMenu; 
?>
<!-- <input name='otherStrain' id='otherStrain' type="text" value="" align="left" disabled=true></input> -->
<br>
<a href="./phpinc/editstrainattribute.inc.php" target="_blank" onclick="alert('After adding a strain, you must refresh this page to see your new strain')">Click here to add strain if not shown</a>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Gene Variation:</strong></td>
<td class="results">
<?php 
foreach($variationdivarray as $div){

			echo $div."</div>";;
	}

echo $genevariationMenu; ?>
<br>
<a href="./phpinc/editgeneticvariationattribute.inc.php" target="_blank" onclick="alert('After adding a genetic variation, you must refresh this page to see your entry')">Click here to add genetic variation if not shown</a>
<font color="red"><strong>*</strong></font>

</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Age:</strong></td>
<td class="results">
<?php
if(!isset($sampleidage)){

	$sampleidage = 0;
}
?>
<input name="age" type="text" value="<?php echo $sampleidage; ?>" align="right"></input><font color="blue"><strong>Enter a number (e.g., 1, 2.5)  If the age can be describe by a range of values enter an average (e.g., 6-7 weeks, enter 6.5) and note in Sample Information field below.</strong></font>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Age Units:</strong></td>
<td class="results">
<?php echo $ageunitMenu; ?>
<font color="red"><strong>*</strong></font>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Sex:</strong></td>
<td class="results">
<?php
$malechecked = "";
$pregnantenabled = "";
$gestationdisabled = "";
$pregnantnochecked = "";
$femalechecked = "";
$unknownchecked = "";
$pregnantyeschecked = "";
if(!isset($sampleidsex)){
	$sampleidsex = "";
}
#echo "sampleidsex = $sampleidsex<hr>";
if($sampleidsex == 'M' || $sampleidsex == ""){
	$malechecked = "checked";
	$pregnantenabled = "disabled";
	$gestationdisabled = "disabled='true'";
	$pregnantnochecked = "checked";
}elseif($sampleidsex == 'F'){
	#echo "in elseif<br>";
	$femalechecked = "checked";
	$pregnantenabled = "enabled";
	if($sampleidpregnant == "yes"){
		$pregnantyeschecked = "checked";
		$pregnantnochecked = "";
		//$gestationdisabled = "false";
	}else{
		$pregnantyeschecked = "";
		$pregnantnochecked = "checked";
		$gestationdisabled = "disabled='true'";
	}
}else{
	$unknownchecked = "checked";
	$pregnantnochecked = "checked";
	$gestationdisabled="disabled='true'";
}	



?>



<input type="radio" name="sex" value="M" <?php echo $malechecked; ?> onClick= "return disablePregnantRadioGroup('pregnant','true')">Male
<input type="radio" name="sex" value="F" <?php echo $femalechecked; ?> onClick= "return disablePregnantRadioGroup('pregnant','false')"> Female
<input type="radio" name="sex" value="U" <?php echo $unknownchecked; ?> onClick= "return disablePregnantRadioGroup('pregnant','false')"> Unknown
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Pregnant/Embryo?</strong></td>
<td class="results">

<input type="radio" name="pregnant" value="no" <?php echo $pregnantnochecked; ?> <?php echo $pregnantenabled; ?>  onClick= "return drawbox('gestation',true)" > no
<input type="radio" name="pregnant" value="yes" <?php echo $pregnantyeschecked; ?>  <?php echo $pregnantenabled; ?>  onClick= "return drawbox('gestation',false)"> yes
<input name='gestation' id='gestation' type="text" size=5 value="<?php 
if(!isset($sampleidgestationperiod)){
	$sampleidgestationperiod = "";
}

echo $sampleidgestationperiod; ?>" align="left" <?php echo $gestationdisabled; ?>> Gestation Period (days)</input>
<font color="blue"><strong>#</strong></font>
</td>
</tr>



<tr>
<td class="questionparameter" ><strong>Tissue/Cell Type:</strong></td>
<td class="results">
<?php echo $tissueMenu; ?>
<!-- <input name='otherTissue' id='otherTissue' type="text" value="" align="left" disabled=true></input> -->
<br>
<a href="./phpinc/edittissueattribute.inc.php" target="_blank" onclick="alert('After adding a tissue/cell type, you must refresh this page to see your new tissue/cell type')">Click here to add tissue/cell type if not shown</a>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Vehicle:</strong></td>
<td class="results">
<?php echo $vehicleMenu; ?>
<br>
<a href="./phpinc/editvehicleattribute.inc.php" target="_blank" onclick="alert('After adding a vehicle, you must refresh this page to see your new vehicle')">Click here to add vehicle if not shown</a>
<font color="red"><strong>*</strong></font>
<!--<input name='otherVehicle' id='otherVehicle' type="text" value="" align="left" disabled=true></input>-->

</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Dose:</strong></td>
<td class="results">
<?php
	if(!isset($sampleiddose)){
		$sampleiddose = 0;
	}

?>
<input name="dose" type="text" value="<?php echo $sampleiddose; ?>" align="right"></input><font color="blue"><strong>Enter a number such as (1, 2.5)</strong></font>
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
<!--<input name='otherRoute' id='otherRoute' type="text" value="" align="left" disabled=true></input>-->
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Time of Day of Dose:</strong></td>
<?php
	// Need to create hour and minute vars for time of day dosage....
	if($sampleid != ""){
		$dosagetimearray = explode(":", $sampleiddosagetime);
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
	if($sampleid != ""){
		$harvesttimearray = explode(":", $sampleidharvesttime);
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
<?php
	if(!isset($sampleidduration)){
		$sampleidduration = 0;
	}
?>
<input name="duration" type="text" value="<?php echo $sampleidduration; ?>" align="right"></input><font color="blue"><strong># Enter a zero if no value.</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Duration Units:</strong></td>
<td class="results">
<?php echo $durationunitMenu; ?>
</td>
</tr>
<!--
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
<?php echo $referenceMenu; 
if(!isset($sampleidinfo)){
	$sampleidinfo = "";
}
?>
<input name='otherReference' id='otherReference' type="text" value="" align="left" disabled=true></input>
<font color="red"><strong>*</strong></font>
</td>
</tr>
-->
<tr>
<td class="questionparameter" ><strong>Sample Information:</strong></td>
<td class="results">
<TEXTAREA NAME="info" COLS=80 ROWS=6 ><?php echo $sampleidinfo; ?></TEXTAREA>
</td>
</tr>

<?php
#if($userid == 1){
?>
<tr>
<td colspan='2'><font color="red"><strong>Associate Additional Info/Quality Control Files with this sample</strong></font><br></td>
</tr>
<tr>
<td><strong>Associated<BR>Data Files:</strong>
<img id="assdatafiles" src="./images/dialog-information12x12.png" align="top"/><div dojoType="dijit.Tooltip" connectId="assdatafiles"><table width="350px"><tr><td><img src="../images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td></tr><tr><td>Up to 3 individual files can be uploaded or multiple files in compressed (zipped) files.<br>  Examples include gel images or microfluidics-based analysis files (i.e, those generated by Agilent Bioanalyzer 2100<br>The description should adequately describe the contents of the file.</td></tr></table></div>


</td>
<td>
<input name="file1" type="file"></input>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>File Description: </b><input name='file1desc' type='text' size='50'></input><br>
<input name="file2" type="file"></input>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>File Description: </b> <input name='file2desc' type='text' size='50'></input><br>
<input name="file3" type="file"></input>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>File Description: </b><input name='file3desc' type='text' size='50'></input><br>
</td>
</tr>
<?php
#}
?>
<tr>
<td>
<?php 

if($sampleid == ""){
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
	if (!isset($_POST['commit']) /* && $sampleidedit == "true"*/){ // if this has been sent for confirmation and this is an array being edited....
			#echo "This array is being edited...<br>";
			$fileError = 0;

			//analyze($_POST);
		$orgsql = "SELECT organism FROM organism WHERE organismid = ".$_POST['organism'];
				$orgresult = $db->Execute($orgsql);//mysql_query($orgsql, $db);
				$organismstr = $orgresult->FetchRow();//mysql_fetch_row($orgresult);
				//sqlerrorcheck($orgsql, $db);
				$organismstr = $organismstr[0];
				$tissuesql = "SELECT tissue FROM tissue WHERE tissueid = ".$_POST['tissue'];
				$tissueresult = $db->Execute($tissuesql);//mysql_query($tissuesql, $db);
				$tissuestr = $tissueresult->FetchRow();//mysql_fetch_row($tissueresult);
				//sqlerrorcheck($tissuesql, $db);
				$tissuestr = $tissuestr[0];
				$treatmentsql = "SELECT chemical FROM chem WHERE chemid=".$_POST['treatment'];
				$treatmentresult = $db->Execute($treatmentsql);//mysql_query($treatmentsql, $db);
				$treatmentstr = $treatmentresult->FetchRow();//mysql_fetch_row($treatmentresult);
				#sqlerrorcheck($treatmentsql, $db);
				$treatmentstr = $treatmentstr[0];
				$dosesql = "SELECT doseunit from doseunit WHERE doseunitid =".$_POST['doseunitid'] ;
				$doseresult = $db->Execute($dosesql);//mysql_query($dosesql, $db);
				$doseunitidstr = $doseresult->FetchRow();//mysql_fetch_row($doseresult);
				#sqlerrorcheck($dosesql, $db);
				$doseunitidstr = $doseunitidstr[0];
				$vehiclesql = "SELECT vehicle FROM vehicle WHERE vehicleid =".$_POST['vehicle'];
				$vehicleresult = $db->Execute($vehiclesql);//mysql_query($vehiclesql, $db);
				$vehiclestr = $vehicleresult->FetchRow();// mysql_fetch_row($vehicleresult);
				#sqlerrorcheck($vehiclesql, $db);
				$vehiclestr = $vehiclestr[0];
				$routesql = "SELECT route FROM route WHERE routeid =".$_POST['route'];
				$routeresult = $db->Execute($routesql);//mysql_query($routesql, $db);
				$routestr = $routeresult->FetchRow();//mysql_fetch_row($routeresult);
				#sqlerrorcheck($routesql, $db);
				$routestr = $routestr[0];
				//echo "$routesql<br>";
				$durationsql = "SELECT durationunit from durationunit where durationunitid =".$_POST['durationunit']; 
				$durationresult = $db->Execute($durationsql);//mysql_query($durationsql, $db);
				$durationunitsstr = $durationresult->FetchRow();//mysql_fetch_row($durationresult);
				#sqlerrorcheck($durationsql, $db);
				$durationunitsstr = $durationunitsstr[0];
				$strainsql = "SELECT strain FROM strain WHERE strainid = ".$_POST['strain'];
				$strainresult = $db->Execute($strainsql);//mysql_query($strainsql, $db);
				$strainstr = $strainresult->FetchRow();//mysql_fetch_row($strainresult);
				#sqlerrorcheck($strainsql, $db);
				$strainstr = $strainstr[0];
				$variationsql = "SELECT genevariation FROM genevariation WHERE genevariationid = ".$_POST['genevariation'];
				$variationresult = $db->Execute($variationsql);//mysql_query($variationsql, $db);
				$variationstr = $variationresult->FetchRow();//mysql_fetch_row($variationresult);
				$variationstr = $variationstr[0];
				#sqlerrorcheck($variationsql, $db);
				$ageunitsql = "SELECT ageunit FROM ageunit WHERE ageunitid = ".$_POST['ageunit'];
				$ageunitresult = $db->Execute($ageunitsql);// mysql_query($ageunitsql, $db);
				$ageunitstr = $ageunitresult->FetchRow();//mysql_fetch_row($ageunitresult);
				$ageunitstr = $ageunitstr[0];
				#sqlerrorcheck($ageunitsql, $db);

	
	#if($userid == 1){
	
		

		if($firstfile != ""){
			
			$uploader1 = new uploader('en');
			$uploader1->max_filesize(90000000);
					
			$uploader1->upload('file1', '', '');
			$uploader1->save_file($IMAGESdir, 2);
			
			if ($uploader1->error) {
				$fileError = 1;
				$fileErrorText = $uploader1->error;
				print($uploader1->error . "<br><br>\n");
				$filename1 = $uploader1->error;
			}else{
				$filename1 = $uploader1->file['name']." Uploaded.";
			}
		}
		if($secondfile != ""){
			$uploader2 = new uploader('en');
			$uploader2->max_filesize(90000000);
					
			$uploader2->upload('file2', '', '');
			$uploader2->save_file($IMAGESdir, 2);
		 
			if ($uploader2->error) {
				$fileError = 1;
				$fileErrorText = $uploader2->error;
				print($uploader2->error . "<br><br>\n");
				$filename2 = $uploader2->error;
			} else{
				$filename2 = $uploader2->file['name']." Uploaded.";
			}
		}

		if($thirdfile != ""){
			$uploader3 = new uploader('en');
			$uploader3->max_filesize(90000000);
					
			$uploader3->upload('file3', '', '');
			$uploader3->save_file($IMAGESdir, 2);
		
			if ($uploader3->error) {
				$fileError = 1;
				$fileErrorText = $uploader3->error;
				print($uploader3->error . "<br><br>\n");
				$filename3=$uploader3->error;
			} else{
				$filename3 = $uploader3->file['name']." Uploaded.";
			}
		}
	#}
	?>
		<form name="commitnewsample" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
		<input type="hidden" name="submitted" value="true">
		<input type="hidden" name="sampleidedit" value="<?php echo $sampleidedit; ?>">
		<input type="hidden" name="userid" value="<?php echo $userid; ?>">
<?php
	if($previous != 1){
?>
		<input type="hidden" name="sampleid" value="<?php echo $sampleid; ?>">
<?php
	}
?>
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="2">RNA Sample Form Entries <br>Please check for accuracy!</th>
		<tr>
		<td class="questionparameter" ><strong>Sample Name:</strong></td>
		<td class="results">
		<input type="hidden" name="samplename" value="<?php echo "$samplename"; ?>">
		<?php echo "$samplename"; ?>
		</td>
		</tr>
		<tr>
		<td class="questionparameter" ><strong>Treatment/Condition:</strong></td>
		<td class="results">
		<input type="hidden" name="treatment" value="<?php echo $treatment; ?>">
		<?php
		// Fetch chemical name from db only when other is not selected... the other button has no value assigned to it and works for ''
			if($treatment != '')
			{
				//echo "yes, it's a number";
				//echo treatment;
				$chemSQL = "SELECT DISTINCT chemical FROM chem WHERE chemid = $treatment";
		
				$chemResult = $db->Execute($chemSQL);//mysql_query($chemSQL, $db);
		
				$thischemical= $chemResult->FetchRow();//mysql_fetch_row($chemResult);
				$treatment = $thischemical[0];
			}
		echo $treatment; // when 'other' is disabled, $treatment gets printed... if enabled, $otherChemical gets printed

		?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Organism:</strong></td>
		<td class="results">
		<input type="hidden" name="organism" value="<?php echo $organism; ?>">
		<?php echo $organismstr; ?>
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
		<?php echo $concentration; ?> micrograms/microliter
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Strain:</strong></td>
		<td class="results">
		<input type="hidden" name="strain" value="<?php echo $strain; ?>">
		<?php echo $strainstr; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Genetic Variation:</strong></td>
		<td class="results">
		<input type="hidden" name="genevariation" value="<?php echo $genevariation; ?>">
		<?php echo $variationstr; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Age:</strong></td>
		<td class="results">
		<input type="hidden" name="age" value="<?php echo $age; ?>">
		<input type="hidden" name="ageunit" value="<?php echo $ageunit; ?>">
		<?php echo "$age $ageunitstr"; ?>
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
		<td class="questionparameter" ><strong>Tissue/Cell Type:</strong></td>
		<td class="results">
		<input type="hidden" name="tissue" value="<?php echo $tissue; ?>">
		<?php echo $tissuestr; ?>
		</td>
		</tr>


		<tr>
		<td class="questionparameter" ><strong>Vehicle:</strong></td>
		<td class="results">
		<input type="hidden" name="vehicle" value="<?php echo $vehicle; ?>">
		<?php echo $vehiclestr; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Dose:</strong></td>
		<td class="results">
		<input type="hidden" name="dose" value="<?php echo $dose; ?>">
		<input type="hidden" name="doseunitid" value="<?php echo $doseunitid; ?>">
		<?php echo "$dose $doseunitidstr"; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Route of Administration:</strong></td>
		<td class="results">
		<input type="hidden" name="route" value="<?php echo $route; ?>">
		<?php echo $routestr; ?>
		</td>
		</tr>
<!--
		<tr>
		<td class="questionparameter" ><strong>Control Treatment:</strong></td>
		<td class="results">
		<input type="hidden" name="control" value="<?php echo $control; ?>">
		<?php echo $control.$otherControl; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Reference Sample:</strong></td>
		<td class="results">
		<input type="hidden" name="reference" value="<?php echo $reference; ?>">
		<?php echo $reference; ?>
		</td>
		</tr>

-->

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
		<?php echo "$duration $durationunitsstr"; ?>
		</td>
		</tr>
		<tr>
		<td class="questionparameter" ><strong>Additional information:</strong></td>
		<td class="results">
		<input type="hidden" name="info" value="<?php echo $_POST['info']; ?>">
		<?php echo $_POST['info']; ?>
		</td>
		</tr>

	<?php
		#if($userid==1){
#
			if($firstfile != ""){
				$name1 = $uploader1->file['name'];
				echo "<tr><td class=\"questionparameter\" ><strong>File #1 Results:</strong></td><td class=\"results\">$filename1</td></tr>
				<input type=\"hidden\" name=\"filename1\" value=\"$name1\">";
				echo "<input type=\"hidden\" name=\"file1desc\" value=\"$file1desc\">";
			}
			if($secondfile != ""){
				$name2 = $uploader2->file['name'];
				echo "<tr><td class=\"questionparameter\" ><strong>File #2 Results:</strong></td><td class=\"results\">$filename2</td></tr>
				<input type=\"hidden\" name=\"filename2\" value=\"$name2\">";
				echo "<input type=\"hidden\" name=\"file2desc\" value=\"$file2desc\">";
			}
			if($thirdfile != ""){
				$name3 = $uploader3->file['name'];
				echo "<tr><td class=\"questionparameter\" ><strong>File #3 Results:</strong></td><td class=\"results\">$filename3</td></tr>
				<input type=\"hidden\" name=\"filename3\" value=\"$name3\">";
				echo "<input type=\"hidden\" name=\"file3desc\" value=\"$file3desc\">";
			}
		#}	
	?>
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
	}else{
		
		
		if($sampleidedit == "true"){
			#echo "Sample Edit = true!<br>";
			// Need to deal w/ an organism changing and what type of array we are dealing w/
			//analyze($_SESSION);
			// For now i'm taking out organism and arraytype from the update values list...
			$userid = $_SESSION['userid'];
			if($userid == ""){
				$userid = $_SESSION['userid'];
			}
	
			
			$name = $_SESSION['firstname']." ".$_SESSION['lastname'];
			$provenanceSQL = "INSERT agilent_rnasample_provenance (sampleid, samplename, organism, rnagroupsize, concentration, strain, genevariation, age, ageunits, sex, tissue, 	treatment, vehicle, dose, route, dosagetime, duration, harvesttime, doseunits, durationunits, pregnant, gestationperiod, datesubmitted, submitter, submitterid,info, queuestatus) SELECT sampleid, samplename, organism, rnagroupsize, concentration, strain, genevariation, age, ageunits, sex, tissue, 	treatment, vehicle, dose, route, dosagetime, duration, harvesttime, doseunits, durationunits, pregnant, gestationperiod, datesubmitted, submitter, submitterid,info, queuestatus FROM agilent_rnasample WHERE sampleid = $sampleid";
			$provenanceResult = $db->Execute($provenanceSQL);//mysql_query($provenanceSQL, $db);
	
			$updateerrormsg = "";
			if(!$provenanceResult){
				echo "uh-oh db error";
				$updateerrormsg .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
				$updateerrormsg .= "<strong><font color=\"red\">".mysql_errno($db) . ": " . mysql_error($db). "</font></strong>\n";
				$updateerrormsg .= "<br>heres sql:  <br>";
				$updateerrormsg .= "$provenanceSQL";
				
			}
			else{
				echo "<strong><font color=\"red\">This treatment information has been committed to the database....</font><br>";
				$updateerrormsg .= "<strong><font color=\"green\">Archival Successful!</font><br>";
			}
			//echo "$provenanceSQL<br>";
	
			$updateSQL = "UPDATE agilent_rnasample SET sampleid=$sampleid, samplename= \"$samplename\", organism=$organism, rnagroupsize=$rnagroupsize, concentration=$concentration, strain=$strain, genevariation=$genevariation, age=$age, ageunits=$ageunit, sex='$sex', tissue=$tissue, treatment=$treatment, vehicle=$vehicle, dose=$dose, route=$route, dosagetime=\"$dosagetime\", duration=$duration, harvesttime=\"$harvesttime\", doseunits=$doseunitid, durationunits=$durationunit, pregnant=\"$pregnant\", gestationperiod=$gestation, datesubmitted=NOW(), submitter=\"$name\", submitterid=$userid,info=\"$info\", queuestatus=$queuestatus WHERE sampleid = $sampleid";
			
			//echo "$updateSQL<br>";
			$updateResult = $db->Execute($updateSQL);//mysql_query($updateSQL, $db);

		
		
			if(!$updateResult){
				echo "uh-oh db error";
				$updateerrormsg .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
				$updateerrormsg .= "<br>heres sql:  <br>";
				$updateerrormsg .= "$updateSQL";
				
			}
			else{
			//echo "<strong><font color=\"red\">This treatment information has been committed to the database....</font><br>";
			$updateerrormsg .= "<strong><font color=\"green\">Update Successful!</font><br>";
			}

		# Insert ANY ASSOCIATED FILES into database....
			#if($_FILES['filename1'] != ""){
			if(isset($_FILES['filename1'])){
				
				#echo "need to insert first file!<br>";
			$firstfile = $_FILES['filename1'];
			$file1desc = $_POST['file1desc'];
			$inputfilename = $IMAGESdir."/".$firstfile;
			#echo "<br>".$inputfilename."<br>";
			$handle = fopen($inputfilename,'r');
			$file_content = fread($handle,filesize($inputfilename));
			
			fclose($handle);

			$namearray = explode('.',$firstfile);
			$num = count($namearray);
			$lastindex = $num - 1;
			$filetype = $namearray[$lastindex];
			#echo "filetype is: $filetype<br>";
			// PARSING THE FILE....
			// insert this arrays specific data....
			$encodedfile = chunk_split(base64_encode($file_content)); 
			//echo $encodedfile;
			$sql = "INSERT datafiles(filetype,data,description, edgeobject,edgeid) VALUES('$filetype',\"$encodedfile\",\"$file1desc\", 0,$sampleid )";
			#echo $sql;
			$sqlResult = $db->Execute($sql);


			}
			if(isset($_FILES['filename2'])){
				#echo "need to insert second file<br>";
				$secondfile = $_FILES['filename2'];
				$file2desc = $_POST['file2desc'];
				$inputfilename = $IMAGESdir."/".$secondfile;
				$handle = fopen($inputfilename,'r');
				$file_content = fread($handle,filesize($inputfilename));
				
				fclose($handle);
	
				$namearray = explode('.',$secondfile);
				$num = count($namearray);
				$lastindex = $num - 1;
				$filetype = $namearray[$lastindex];
				#echo "filetype is: $filetype<br>";
				// PARSING THE FILE....
				// insert this arrays specific data....
				$encodedfile = chunk_split(base64_encode($file_content)); 
				//echo $encodedfile;
				$sql = "INSERT datafiles(filetype,data,description, edgeobject,edgeid) VALUES('$filetype',\"$encodedfile\",\"$file2desc\", 0,$sampleid )";
				#echo $sql;
				$sqlResult = $db->Execute($sql);
			}
			if(isset($_FILES['filename3'])){
				$thirdfile = $_FILES['filename3'];
				$file3desc = $_POST['file3desc'];
				$inputfilename = $IMAGESdir."/".$thirdfile;
				#echo "$inputfilename<br>";
				$handle = fopen($inputfilename,'r');
				$file_content = fread($handle,filesize($inputfilename));
				
				fclose($handle);
	
				$namearray = explode('.',$thirdfile);
				$num = count($namearray);
				$lastindex = $num - 1;
				$filetype = $namearray[$lastindex];
				#echo "filetype is: $filetype<br>";
				// PARSING THE FILE....
				// insert this arrays specific data....
				$encodedfile = chunk_split(base64_encode($file_content)); 
				//echo $encodedfile;
				$sql = "INSERT datafiles(filetype,data,description, edgeobject,edgeid) VALUES('$filetype',\"$encodedfile\",\"$file3desc\", 0,$sampleid )";
				#echo $sql;
				$sqlResult = $db->Execute($sql);
			}







		
		?>


			<table class="question" width="400">
		  	<thead>
		 	<tr>
		  	<th class="mainheader" colspan="2">RNA Sample Update Results</th>
			</tr>
			<tr>
			<td class="questionparameter" ><strong>Update:</strong></td>
			<td class="results">
			<?php echo $updateerrormsg; ?>
			</td>
			</tr>
			</table>

<?php

		}else{

		

		// Now insert data into the agilent_array_queue table
		/*$countSQL = "Select MAX(sampleid) from agilent_rnasample";
		$countResult = mysql_query($countSQL, $db);
		$row = mysql_fetch_row($countResult);
		
		$maxsampleID = $row[0];
		if(is_nan($maxsampleID)){
			$maxsampleID = 0;
		}
		$newsampleid = $maxsampleID + 1;
		*/
		$userid = $_SESSION['userid'];
		
		$name = $_SESSION['firstname']." ".$_SESSION['lastname'];
		// Add the treatment to sample....
		$sql = "INSERT agilent_rnasample (samplename, organism, rnagroupsize, concentration, strain, genevariation, age, ageunits, sex, tissue, 	treatment, vehicle, dose, route, dosagetime, duration, harvesttime, doseunits, durationunits, pregnant, gestationperiod, datesubmitted, submitter, submitterid,info, queuestatus)
			VALUES (\"$samplename\", $organism, $rnagroupsize, $concentration, $strain, $genevariation, $age, $ageunit, '$sex',$tissue, $treatment, $vehicle, $dose,$route, \"$dosagetime\", $duration, \"$harvesttime\", $doseunitid, $durationunit,  \"$pregnant\",  $gestation, NOW(),\"$name\", $userid,\"$info\", $queuestatus)";


		$newTreatmentResult = $db->Execute($sql);//mysql_query($sql, $db);

		
		//echo $sql;
		$updateerrormsg = "";
		if(!$newTreatmentResult){
			echo "uh-oh db error";
			echo " trxid is " . $trxid;
			echo $newTreatmentResult;
			$updateerrormsg .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
			$updateerrormsg .= "<br>heres sql:  <br>";
			$updateerrormsg .= "$sql";
		}
		else{
		//echo "<strong><font color=\"red\">This treatment information has been committed to the database....</font><br>";
			$updateerrormsg .= "<strong><font color=\"green\">Successful!</font><br>";
		}
		$countSQL = "Select MAX(sampleid) from agilent_rnasample";
		$countResult = $db->Execute($countSQL);#mysql_query($countSQL, $db);
		$row = $countResult->FetchRow();
		
		$maxsampleID = $row[0];
		if(is_nan($maxsampleID)){
			$maxsampleID = 1;
		}
		
		$prevsampleid=$maxsampleID;

		?>


			<table class="question" width="400">
		  	<thead>
		 	<tr>
		  	<th class="mainheader" colspan="2">RNA Sample Submission Results</th>
			</tr>
			<tr>
			<td class="questionparameter" ><strong>New RNA Sample Creation:</strong></td>
			<td class="results">
			<?php echo $updateerrormsg; ?>
			</td>
			</tr>
			</table>
		<a href="./agilentexperiment-useradmin.php?rnasubmission=1">Enter a New RNA sample?</a><br>
		<a href="./agilentexperiment-useradmin.php?rnasubmission=1&previous=<?php echo $prevsampleid ?>">Enter a New RNA sample with the same choices as the most recent one submitted?</a>
		
		

		<?php
# Insert ANY ASSOCIATED FILES into database....
		#if($userid == 1){
			# Insert ANY ASSOCIATED FILES into database....
			if(isset($_FILES['filename1'])){
			#if($_FILES['filename1'] != ""){
				#echo "need to insert first file!<br>";
			$firstfile = $_FILES['filename1'];
			$file1desc = $_POST['file1desc'];
			$inputfilename = $IMAGESdir."/".$firstfile;
			#echo "<br>".$inputfilename."<br>";
			$handle = fopen($inputfilename,'r');
			$file_content = fread($handle,filesize($inputfilename));
			
			fclose($handle);

			$namearray = explode('.',$firstfile);
			$num = count($namearray);
			$lastindex = $num - 1;
			$filetype = $namearray[$lastindex];
			#echo "filetype is: $filetype<br>";
			// PARSING THE FILE....
			// insert this arrays specific data....
			$encodedfile = chunk_split(base64_encode($file_content)); 
			//echo $encodedfile;
			$sql = "INSERT datafiles(filetype,data,description, edgeobject,edgeid) VALUES('$filetype',\"$encodedfile\",\"$file1desc\", 0,$sampleid )";
			#echo $sql;
			$sqlResult = $db->Execute($sql);


			}
			#if($_FILES['filename2'] != ""){
			if(isset($_FILES['filename2'])){
				#echo "need to insert second file<br>";
				$secondfile = $_FILES['filename2'];
				$file2desc = $_POST['file2desc'];
				$inputfilename = $IMAGESdir."/".$secondfile;
				$handle = fopen($inputfilename,'r');
				$file_content = fread($handle,filesize($inputfilename));
				
				fclose($handle);
	
				$namearray = explode('.',$secondfile);
				$num = count($namearray);
				$lastindex = $num - 1;
				$filetype = $namearray[$lastindex];
				#echo "filetype is: $filetype<br>";
				// PARSING THE FILE....
				// insert this arrays specific data....
				$encodedfile = chunk_split(base64_encode($file_content)); 
				//echo $encodedfile;
				$sql = "INSERT datafiles(filetype,data,description, edgeobject,edgeid) VALUES('$filetype',\"$encodedfile\",\"$file2desc\", 0,$sampleid )";
				#echo $sql;
				$sqlResult = $db->Execute($sql);
			}
			#if($_FILES['filename3'] != ""){
			if(isset($_FILES['filename3'])){
				$thirdfile = $_FILES['filename3'];
				$file3desc = $_POST['file3desc'];
				$inputfilename = $IMAGESdir."/".$thirdfile;
				#echo "$inputfilename<br>";
				$handle = fopen($inputfilename,'r');
				$file_content = fread($handle,filesize($inputfilename));
				
				fclose($handle);
	
				$namearray = explode('.',$thirdfile);
				$num = count($namearray);
				$lastindex = $num - 1;
				$filetype = $namearray[$lastindex];
				#echo "filetype is: $filetype<br>";
				// PARSING THE FILE....
				// insert this arrays specific data....
				$encodedfile = chunk_split(base64_encode($file_content)); 
				//echo $encodedfile;
				$sql = "INSERT datafiles(filetype,data,description, edgeobject,edgeid) VALUES('$filetype',\"$encodedfile\",\"$file3desc\", 0,$sampleid )";
				#echo $sql;
				$sqlResult = $db->Execute($sql);
			}

		#}
		
	
		$Name = "EDGE admin"; //senders name
		$email = "www-admin"; //senders e-mail adress
		
		$mail_body = "A new RNA submission, $samplename, was submitted by $name.  Please go to http://$serverhost/agilentexperiment-useradmin.php?rnasubmissionqueue=1 to view the new submission"; //mail body
		$subject = "New RNA submission from $name"; //subject
		$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields
		# some of the variables used here are in ./phpinc/globalvariables.inc.php
		if(ini_get('SMTP') != ''){
			$smtpval = ini_get('SMTP');
			#echo "SMTP = $smtpval";
			# Windows smtp setup
			if(!mail($adminrecipient, $subject, $mail_body, $header)){
				echo "Email notifications not configured correctly on this server.";
			}else{
				echo "Email notification sent to EDGE<sup>3</sup> Administrator.";
			}
		}
		if(ini_get('sendmail_path')!= ""){
			# Unix-based setup
			if(!mail($adminrecipient, $subject, $mail_body, $header)){
				echo "Email notifications not configured correctly on this server.";
			}else{
				echo "Email notification sent to EDGE<sup>3</sup> Administrator.";
			}
		}
		


	}// end of else where we are checking to see what table to update/add to....
	}// end of initial submit....
}
?>
