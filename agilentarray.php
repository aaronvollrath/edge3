<?php
/**test */
require 'edge_db_connect2.php';
require("fileupload-class.php");
require './phpinc/edge3_db_connect.inc';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	//die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}
include 'header.inc';
include 'utilityfunctions.inc';
require 'globalfilelocations.inc';
if(isset($_POST['submitted'])){
	$submitted = $_POST['submitted'];
	$arrayID = $_POST['arrayID'];
	$arraytype = $_POST['arraytype'];
	$arraydesc = $_POST['arraydesc'];
	$file = $_POST['file'];
	$controlchannel = $_POST['controlchannel'];
	$controlsample = $_POST['controlsample'];
	$testsample = $_POST['testsample'];
}

//analyze($_POST);
die("<h1>THIS PAGE IS NO LONGER USED TO PUT ARRAY DATA INTO EDGE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!please, please go to: <a href=\"./agilentexperiment-useradmin.php?arraysubmit=1\">Here</a></h1>");
if($submitted != true){
	$agilentarraytypeSQL = "SELECT id, organism, arraydesc, version FROM agilentarrays";
	$arraytypeResult = mysql_query($agilentarraytypeSQL, $db);
	while(list($arrayid, $organism, $arraydesc, $version) = mysql_fetch_array($arraytypeResult))
	{
	
	$arraytypeMenu .= "<option value=\"$arrayid\">$organism $arraydesc $version</option>\r";
	}



	//$command = "java -mx512m -jar Cluster3.jar $file $number $arrayidCount $svgFile $algo $tableFile $colorscheme 2 $browserval";
	
	// Made the < 1000 limit because we uploaded data that is not associated w/ any (valid) arrayid number in agilent_arrayinfo
	$countSQL = "SELECT MAX(arrayid) from agilent_arrayinfo WHERE arrayid < 1000";
	$countResult = mysql_query($countSQL, $db);
	$row = mysql_fetch_row($countResult);
	$maxarrayID = $row[0];
	$arrayID = 1;
	if($maxsampleID  != "NULL"){
		$arrayID = $maxarrayID + 1;
	}
	//echo "array id : $arrayID <br>";
	//$arrayID = 1000;
	
	$rnaSQL = "SELECT sampleid, samplename FROM agilent_rnasample WHERE queuestatus=1 || queuestatus=2";
	$rnaResult = mysql_query($rnaSQL, $db);
	while($rnaRow = mysql_fetch_row($rnaResult)){
		$sampleid = $rnaRow[0];
		$samplename = $rnaRow[1];
		$rnaMenu .= "<option value=\"$sampleid\">$samplename</option>";
	}

?>


	<form enctype="multipart/form-data" name="newagilentsample" action="agilentarray.php" method="post">
	<input type="hidden" name="submitted" value="true">
	
	<table>
	<tr>
	<td><strong>Array Type/Species</strong></td>
	<td><select name="arraytype">
	<option SELECTED></option>
	<?php echo $arraytypeMenu; ?>
	</select></td>
	</tr>
	<tr>
	<td><strong>Array ID</strong></td>
	<td>Next consecutive ID:<?php echo $arrayID; ?>    <input name="arrayID" type="text" value="<?php echo $arrayID; ?>" align="right"></input></td>
	</tr>
	<tr>
	<td><strong>Array Description</strong></td>
	<td><input name="arraydesc" type="text" value="" align="right"></input></td>
	</tr>
	<tr>
	<td><strong>Control Sample</strong></td>
	<td>
	Channel:
	<select name="controlchannel">
	<option value=5>cy5</option>
	<option value=3>cy3</option>
	</select>
	&nbsp;
	Name: 
	<select name="controlsample">
	<?php echo $rnaMenu; ?>
	</select></td>
	</tr>
	<tr>
	<td><strong>Test Sample</strong></td>
	<td><select name="testsample">
	<?php echo $rnaMenu; ?>
	</select></td>
	</tr>
	<tr>
	<td><strong>Data File:</strong></td>
	<td>
	<input name="file" type="file"><font color="red"><strong>*</strong></font>
	</td>
	</tr>
	
	
	<tr>
	<td><input type="submit" name="submit" value="Submit"></td>
	<td><input type="reset" value="Reset Form"></td>
	</tr>
	</table>
	</form>
<?php
}
else{

	$rnaUpdateSQL = "UPDATE agilent_rnasample SET queuestatus=1 WHERE sampleid='$controlsample' || sampleid='$testsample'";
	$rnaUpdateResult = mysql_query($rnaUpdateSQL,$db);

//  Need to check to see if the name already exists in the database...
	$namesql = "SELECT * FROM agilent_arrayinfo WHERE arraydesc LIKE \"$arraydesc\"";
	$nameResult = mysql_query($namesql, $db);
	$namerow = mysql_fetch_row($nameResult);
	/*if($namerow[0] != ""){

	//echo "$namesql<br>";
		echo "<b><font color=\"red\">this name already exists in the database.  please reenter the array</font><br>link: <a href='agilentarray.php'>go back to entry page</a>";
		exit(0);
	}*/


//$nameResult = mysql_query($namesql, $db);

	echo "arraytype $arraytype submitted...<br>";
	$my_uploader = new uploader('en'); // errors in English
	echo "<br>$IMAGESdir<br>";

//echo "$command<br>";
	$inputfilename = "$IMAGESdir/$randfilename";
				//echo "<br>$command<br>";
	$my_uploader->max_filesize(90000000);
	//$my_uploader->max_image_size(800, 800);
	$my_uploader->upload('file', '', '.txt');
	$my_uploader->save_file($IMAGESdir, 2);

	if ($my_uploader->error) {
		$fileError = 1;
		$fileErrorText = $my_uploader->error;
		print($my_uploader->error . "<br><br>\n");
	}else{
				//print("Thanks for uploading " . $my_uploader->file['name'] . "<br><br>\n");
		$inputfilename = $my_uploader->file['name'];
					/*
	
		Need to put code here to process the file and insert into database
		
		Pretty straight-forward:  get the file, parse it, insert into database....
		
		*/
		// PARSING THE FILE....
		// insert this arrays specific data....
		
		$sql = "INSERT agilent_arrayinfo(arrayid, arraytype, arraydesc, FE_data_file, controlchannel) VALUES($arrayID,$arraytype, \"$arraydesc\", \"$inputfilename\",$controlchannel)";
						$sqlResult = mysql_query($sql, $db);
		echo "$sql <BR>";
		$arrayVersion = $arraytype;
	
		// The update statements below are put in place to compensate for issues w/ restoring data.  they'll need to be removed once all data has been restored.
		$updatesql = "UPDATE agilent_arrayinfo SET FE_data_file = '$inputfilename' WHERE arrayid = $arrayID";
		echo "<br>$updatesql<br>";
		$updateResult = mysql_query($updatesql, $db);
		$updatesql = "UPDATE agilent_arrayinfo SET controlchannel = $controlchannel WHERE arrayid = $arrayID";
		echo "<br>$updatesql<br>";
		$updateResult = mysql_query($updatesql, $db);
		$inputfilename = "./IMAGES/$inputfilename";
		$filenum = rand(0, 25000);
		$randfilename = $filenum . "agilentdata";
		$command = "cp $inputfilename ./IMAGES/$randfilename";
						$str = exec($command);
		
		echo "$command<br>";
	
	
		// PARSE the datafile for entry into 'agilentdata' table of EDGE....
		$inputfilename = "$IMAGESdir/$randfilename";
		$command = "java -mx512m -jar AgilentTabFile2.jar $arrayID $arrayVersion $inputfilename FeatureNum 	Row 	Col 	SubTypeMask 	SubTypeName 	ProbeUID 	ControlType 	ProbeName 	GeneName	SystematicName 	Description 	PositionX 	PositionY 	LogRatio 	LogRatioError 	PValueLogRatio 	gSurrogateUsed 	rSurrogateUsed 	gIsFound 	rIsFound 	gProcessedSignal 	rProcessedSignal 	gProcessedSigError 	rProcessedSigError 	gNumPixOLHi 	rNumPixOLHi 	gNumPixOLLo 	rNumPixOLLo 	gNumPix 	rNumPix 	gMeanSignal 	rMeanSignal 	gMedianSignal 	rMedianSignal 	gPixSDev 	rPixSDev 	gPixNormIQR 	rPixNormIQR 	gBGNumPix 	rBGNumPix 	gBGMeanSignal 	rBGMeanSignal 	gBGMedianSignal 	rBGMedianSignal 	gBGPixSDev 	rBGPixSDev 	gBGPixNormIQR 	rBGPixNormIQR 	gNumSatPix 	rNumSatPix 	gIsSaturated 	rIsSaturated 	PixCorrelation 	BGPixCorrelation 	gIsFeatNonUnifOL 	rIsFeatNonUnifOL 	gIsBGNonUnifOL 	rIsBGNonUnifOL 	gIsFeatPopnOL 	rIsFeatPopnOL 	gIsBGPopnOL 	rIsBGPopnOL 	IsManualFlag 	gBGSubSignal 	rBGSubSignal 	gBGSubSigError 	rBGSubSigError 	BGSubSigCorrelation 	gIsPosAndSignif 	rIsPosAndSignif 	gPValFeatEqBG 	rPValFeatEqBG 	gNumBGUsed 	rNumBGUsed 	gIsWellAboveBG 	rIsWellAboveBG 	gBGUsed 	rBGUsed 	gBGSDUsed 	rBGSDUsed 	IsNormalization 	gDyeNormSignal 	rDyeNormSignal 	gDyeNormError 	rDyeNormError 	DyeNormCorrelation 	ErrorModel 	xDev 	gSpatialDetrendIsInFilteredSet 	rSpatialDetrendIsInFilteredSet 	gSpatialDetrendSurfaceValue 	rSpatialDetrendSurfaceValue 	SpotExtentX 	SpotExtentY 	gNetSignal 	rNetSignal 	gMultDetrendSignal 	rMultDetrendSignal 	gProcessedBackground 	rProcessedBackground 	gProcessedBkngError 	rProcessedBkngError 	IsUsedBGAdjust 	gInterpolatedNegCtrlSub 	rInterpolatedNegCtrlSub 	gIsInNegCtrlRange 	rIsInNegCtrlRange 	gIsUsedInMD 	rIsUsedInMD";
		//$command = "java -jar -mx512m AgilentTabFile.jar $arrayID $arrayVersion $inputfilename";
	
		echo "<br>$command<br>";
		$str=passthru($command);
		echo " <br>$str<br>";
	
	
		// Upload the parsed output to 'agilentdata' table...
	
		$datafile = $inputfilename."output.csv";
		$arraytable = "agilentdata";
		$sql = "LOAD DATA LOCAL INFILE \"$datafile\" INTO TABLE $arraytable FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n'";
		echo "<br>agilentdata insertion SQL: $sql<br>";
		$insertDataResult = mysql_query($sql, $db);
		if($errNum){
			echo "<strong>Database Error inserting.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
		}
		//echo "<br>";
		$affectedrows = mysql_affected_rows();
	
		// Parse the data for entry into 'agilentdata2' table of EDGE....
		$command = "java -mx512m -jar AgilentTabFile2.jar $arrayID $arrayVersion $inputfilename FeatureNum 	PositionX 	PositionY 	LogRatio 	LogRatioError 	PValueLogRatio 	gSurrogateUsed 	rSurrogateUsed 	gIsFound 	rIsFound 	gProcessedSignal 	rProcessedSignal 	gProcessedSigError 	rProcessedSigError 	gNumPixOLHi 	rNumPixOLHi 	gNumPixOLLo 	rNumPixOLLo 	gNumPix 	rNumPix 	gMeanSignal 	rMeanSignal 	gMedianSignal 	rMedianSignal 	gPixSDev 	rPixSDev 	gPixNormIQR 	rPixNormIQR 	gBGNumPix 	rBGNumPix 	gBGMeanSignal 	rBGMeanSignal 	gBGMedianSignal 	rBGMedianSignal 	gBGPixSDev 	rBGPixSDev 	gBGPixNormIQR 	rBGPixNormIQR 	gNumSatPix 	rNumSatPix 	gIsSaturated 	rIsSaturated 	PixCorrelation 	BGPixCorrelation 	gIsFeatNonUnifOL 	rIsFeatNonUnifOL 	gIsBGNonUnifOL 	rIsBGNonUnifOL 	gIsFeatPopnOL 	rIsFeatPopnOL 	gIsBGPopnOL 	rIsBGPopnOL 	IsManualFlag 	gBGSubSignal 	rBGSubSignal 	gBGSubSigError 	rBGSubSigError 	BGSubSigCorrelation 	gIsPosAndSignif 	rIsPosAndSignif 	gPValFeatEqBG 	rPValFeatEqBG 	gNumBGUsed 	rNumBGUsed 	gIsWellAboveBG 	rIsWellAboveBG 	gBGUsed 	rBGUsed 	gBGSDUsed 	rBGSDUsed 	IsNormalization 	gDyeNormSignal 	rDyeNormSignal 	gDyeNormError 	rDyeNormError 	DyeNormCorrelation 	ErrorModel 	xDev 	gSpatialDetrendIsInFilteredSet 	rSpatialDetrendIsInFilteredSet 	gSpatialDetrendSurfaceValue 	rSpatialDetrendSurfaceValue 	SpotExtentX 	SpotExtentY 	gNetSignal 	rNetSignal 	gMultDetrendSignal 	rMultDetrendSignal 	gProcessedBackground 	rProcessedBackground 	gProcessedBkngError 	rProcessedBkngError 	IsUsedBGAdjust 	gInterpolatedNegCtrlSub 	rInterpolatedNegCtrlSub 	gIsInNegCtrlRange 	rIsInNegCtrlRange 	gIsUsedInMD 	rIsUsedInMD";
		echo "<br>$command<br>";
		$str=passthru($command);
		echo " <br>$str<br>";
		
		$datafile = $inputfilename."output.csv";
		$arraytable = "agilentdata2";
		$sql = "LOAD DATA LOCAL INFILE \"$datafile\" INTO TABLE $arraytable FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n'";
		echo "<br>agilentdata insertion SQL: $sql<br>";
		$insertDataResult = mysql_query($sql, $db);
		if($errNum){
			echo "<strong>Database Error inserting.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
		}
		$trxfiledata = "";
		$pgemdata = "";
			//echo "<br>";
		// SETTING THINGS UP FOR CREATING THE CONDENSED DATA FILE....
		$file = "$IMAGESdir/condensedagilent/agilentcondensedfile_array_$arrayID.csv";
		//echo $file."<br>";
	
		$command = "touch $file";
		$str=exec($command);
		$fd = fopen($file, 'w');
	
		rewind($fd);
		$file = "/var/www/agilentcondensingfile.csv";
	
		if (!($fp = fopen($file, "r"))) {
			die("could not open XML input from file $file");
		}
		echo "<h3><font color=\"red\">$arrayID</font></h3>";
		while (!feof($fp)) {
			$buffer = fgets($fp, 4096);
			$buffer = trim($buffer);
			$vals = explode("\t",$buffer);
			if($vals[0] != ""){
				$condensedfeaturenum = $vals[0];
				$sum = 0.0;
				$count = 0;
				foreach($vals as $featurenum){
					//echo "$val<br>";
					$sql = "SELECT LogRatio FROM agilentdata WHERE FeatureNum = $featurenum and arrayid = $arrayID";
					$sqlResult = mysql_query($sql,$db);
					$row = mysql_fetch_row($sqlResult);
					$ratio = $row[0]; //pow(10, $row[0]);
					$sum += $ratio;
					//echo "$featurenum : $row[0]<br>";
					$count++;
				}
				//echo "Sum: $sum<br>";
				//echo "Count: $count<br>";
				$avg = $sum/$count;
				$line = "$arrayID\t$condensedfeaturenum\t$avg\n";
				fwrite($fd,$line);
				//echo "Avg. Value = $avg<br>";
	
			}
		}
		fclose($fd);
		echo "<hr>";
	
		$condensedfileroot = "$IMAGESdir/condensedagilent/";
		$filebasename = "agilentcondensedfile_array_";
	
	
		$thiscondensedfile = $condensedfileroot.$filebasename.$arrayID;
		$thiscondensedfile .= ".csv";
		//echo "$thiscondensedfile<br>";
		$sql = "LOAD DATA LOCAL INFILE \"$thiscondensedfile\" INTO TABLE agilentcondenseddata";
		echo $sql."<br>";
		$sqlResult = mysql_query($sql, $db);
		//echo "$sqlResult<br>";
		$errNum = mysql_errno($db);
		if($errNum){
			echo "<strong>Database Error inserting.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
		}
	
	
	
		if($affectedrows==-1){
				$trxfiledata .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
				$trxfiledata .=  "<strong><font color=\"red\">".mysql_errno($db) . ": " . mysql_error($db). "</font></strong>\n";
				$trxfiledata .=  "<br>heres sql:  <br>";
				$trxfiledata .=  "$sql";
		}else{
			echo "<font color=\"green\"><strong>Data successfully entered into database!</strong></font><br><a href='agilentarray.php'>Click to enter another array</a>";
		}
		// Need to invert the log ratios if the channel for control is cy5....
		if($controlchannel  == 5){
			$invertratiossql = "UPDATE agilentdata SET LogRatio = (-1 * LogRatio) WHERE arrayid = $arrayID and LogRatio != 0";
			$sqlResult = mysql_query($invertratiossql, $db);
			//echo "$sqlResult<br>";
			$errNum = mysql_errno($db);
			if($errNum){
				echo "<strong>Database Error inverting LogRatios for agilentdata.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
			}
			$invertratiossql = "UPDATE agilentdata2 SET LogRatio = (-1 * LogRatio) WHERE arrayid = $arrayID and LogRatio != 0";
			$sqlResult = mysql_query($invertratiossql, $db);
			//echo "$sqlResult<br>";
			$errNum = mysql_errno($db);
			if($errNum){
				echo "<strong>Database Error inverting LogRatios for agilentdata2.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
			}
			$invertratiossql = "UPDATE agilentcondenseddata SET LogRatio = (-1 * LogRatio) WHERE arrayid = $arrayID and LogRatio != 0";
			$sqlResult = mysql_query($invertratiossql, $db);
			//echo "$sqlResult<br>";
			$errNum = mysql_errno($db);
			if($errNum){
				echo "<strong>Database Error inverting LogRatios for condensedagilentdata.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
			}
		}
	}
} // end of else (i.e., submitted == true

?>
