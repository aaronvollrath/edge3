<?php


/*
	This is the administrator's page for uploading data for a user-entered array....


*/
//$db->debug = true;
if($privval != 99){
	die('Sorry you have inadequate rights to access this page.  If you feel you have reached this page in error, please contact the EDGE administrator');
}
$rnaMenu = "";

if (!isset($_GET['submit'])) { // if form has not been submitted
	$arraysql = "SELECT * FROM  arrayqueue ORDER BY status DESC";
	$sqlResult = $db->Execute($arraysql);// mysql_query($arraysql, $db);
	if(!$sqlResult){
		echo "<strong>Database Error getting arrays. SQL: $arraysql </strong><br>";
	}else{

		
		echo "<table class=\"results\"><tr><td colspan='2'><b><u>Status Key</u></b></td></tr><tr><td><b>In Queue</b></td><td><img src=\"./images/inqueuestatus.png\" height=\"14px\" width=\"20px\"></td></tr>";
		echo "<tr><td><b>Completed</b></td><td><img src=\"./images/completedstatus.png\" height=\"14px\" width=\"20px\"></td></tr>";
		echo "<tr><td><b>Error in Hybing</b></td><td><img src=\"./images/errorstatus.png\" height=\"14px\" width=\"20px\"></td></tr></table>";
		$tabletop = "<table class=\"question\" width=\"700px\"><tr class=\"d1\"><td><b>Array Queue ID#</b></td><td><b>Array Name</b></td><td><b>Array Type</b></td><td><b>Cy3 RNA Sample</b></td><td><b>Cy5 RNA Sample</b></td><td><b>Control Channel</b></td><td><b>Status</b></td><td><b>EDGE Owner</b></td></tr>";
		
		$index = 0;
		#while($row=mysql_fetch_row($sqlResult)){
		# there are three possible status values: in queue = 0,completed = 1 and error = 2
		$inqueuetable = "";
		$completedtable = "";
		$errortable = "";
		while($row=$sqlResult->FetchRow()){
			$id = $row[0];
			$arrayname = $row[1];
			$cy3rnasample = $row[2];
			$cy5rnasample = $row[3];
			$controlchannel = $row[4];
			$arraytype = $row[5];
			$status = $row[6];
			$ownerid = $row[7];
			// Need to get the names of the cy3 and cy5 samples for this array....

			$sampleSQL = "SELECT sampleid, samplename, submitter, submitterid FROM agilent_rnasample WHERE sampleid = $cy3rnasample OR sampleid = $cy5rnasample";
			$sampleresult = $db->Execute($sampleSQL);//mysql_query($sampleSQL);
			if(!$sampleresult){
				echo "<strong>Database Error getting rna samples. SQL: $sampleSQL </strong><br>";
			}else{
				$cy3name = "";
				$cy5name = "";
				
				#while($row=mysql_fetch_row($sampleresult)){
				while($row=$sampleresult->FetchRow()){
					$sampid = $row[0];
					$sampname = $row[1];
					if($sampid == $cy3rnasample){
						$cy3name = $sampname;
					}else{
						$cy5name = $sampname;
					}
				}

			}
			$agilentarraytypeSQL = "SELECT organism, arraydesc, version FROM agilentarrays WHERE id = $arraytype";
			$arraytypeResult = $db->Execute($agilentarraytypeSQL);// mysql_query($agilentarraytypeSQL, $db);
			if(!$arraytypeResult){
				echo "<strong>Database Error getting arraytype. SQL: $sampleSQL </strong><br>";
			}else{
				//list($arrayid, $organism, $arraydesc, $version) = mysql_fetch_array($arraytypeResult);
				$row= $arraytypeResult->FetchRow();
				//$arrayid = $row[0];
				$organism = $row[0];
				$arraydesc = $row[1];
				$version = $row[2];
			}
			// get owner's name
			$ownerSQL = "SELECT firstname, lastname FROM users WHERE id = $ownerid";
			//echo "$ownerSQL<br>";
			$ownerresult = $db->Execute($ownerSQL);//mysql_query($ownerSQL, $db);
			if(!$ownerresult){
				echo "<strong>Database Error getting owner info. SQL: $ownerSQL </strong><br>";
			}else{
				$row = $ownerresult->FetchRow();//mysql_fetch_row($ownerresult);
				$ownername = $row[0]." ".$row[1];
			}
			$cssrowclass = "d1";
			if($index%2==0){
				$cssrowclass = "d0";
			}
			
			switch($status){
				case 0:
					$img = "<img src=\"./images/inqueuestatus.png\" height=\"14px\" width=\"20px\">";
					$inqueuetable .= "<tr class=\"$cssrowclass\"><td><a href=\"agilentexperiment-useradmin.php?arraysubmit=1&submit=true&id=$id\">$id</a></td><td>$arrayname</td><td>$organism $arraydesc $version</td><td>$cy3name</td><td>$cy5name</td><td>cy$controlchannel</td><td align=\"center\">$img</td><td>$ownername</td></tr>";
					break;
				case 1: 
					$img = "<img src=\"./images/completedstatus.png\" height=\"14px\" width=\"20px\">";
					$completedtable .= "<tr class=\"$cssrowclass\"><td><a href=\"agilentexperiment-useradmin.php?arraysubmit=1&submit=true&id=$id\">$id</a></td><td>$arrayname</td><td>$organism $arraydesc $version</td><td>$cy3name</td><td>$cy5name</td><td>cy$controlchannel</td><td align=\"center\">$img</td><td>$ownername</td></tr>";
					break;
				case 2:
					$img = "<img src=\"./images/errorstatus.png\" height=\"14px\" width=\"20px\">";
					$errortable .= "<tr class=\"$cssrowclass\"><td><a href=\"agilentexperiment-useradmin.php?arraysubmit=1&submit=true&id=$id\">$id</a></td><td>$arrayname</td><td>$organism $arraydesc $version</td><td>$cy3name</td><td>$cy5name</td><td>cy$controlchannel</td><td align=\"center\">$img</td><td>$ownername</td></tr>";
					break;
			}
			
			#echo "<tr class=\"$cssrowclass\"><td><a href=\"agilentexperiment-useradmin.php?arraysubmit=1&submit=true&id=$id\">$id</a></td><td>$arrayname</td><td>$organism $arraydesc $version</td><td>$cy3name</td><td>$cy5name</td><td>cy$controlchannel</td><td align=\"center\">$img</td><td>$ownername</td></tr>";
			$index++;
		}

		# output the queues
		echo "<div dojoType='dijit.TitlePane' title='Arrays in Queue to be Processed' open='false'>
		<div style='width : 1000px; height : 600px; overflow : auto; '>";
		echo $tabletop;
		echo $inqueuetable;
		echo "</table></div></div>";
		echo "<div dojoType='dijit.TitlePane' title='Arrays Processed' open='false'>
		<div style='width : 1000px; height : 600px; overflow : auto; '>";
		echo $tabletop;
		echo $completedtable;
		echo "</table></div></div>";
		echo "<div dojoType='dijit.TitlePane' title='Arrays with Errors' open='false'>
		<div style='width : 1000px; height : 600px; overflow : auto; '>";
		echo $tabletop;
		echo $errortable;
		echo "</table></div></div>";

	}
}else{

	if(!isset($_POST['submit'])){

		
	
	
	
		//$command = "java -mx512m -jar Cluster3.jar $file $number $arrayidCount $svgFile $algo $tableFile $colorscheme 2 $browserval";
		
		
		$countSQL = "SELECT MAX(arrayid) from agilent_arrayinfo WHERE arrayid";
		$countResult = $db->Execute($countSQL);//mysql_query($countSQL, $db);
		$row = $countResult->FetchRow();//mysql_fetch_row($countResult);
		$maxarrayID = $row[0];
		$arrayID = 1;
		if($maxarrayID  != "NULL"){
			$arrayID = $maxarrayID + 1;
		}
		// get the arrayqueue array info
		$aqid = $_GET['id'];
		$arraysql = "SELECT * FROM  arrayqueue WHERE arrayqueueid = $aqid";
		$sqlResult = $db->Execute($arraysql);//mysql_query($arraysql, $db);
		if(!$sqlResult){
			echo "<strong>Database Error getting arrays. SQL: $arraysql </strong><br>";
		}else{
			$row=$sqlResult->FetchRow();//mysql_fetch_row($sqlResult);

			$id = $row[0];
			$arrayname = $row[1];
			$cy3rnasample = $row[2];
			$cy5rnasample = $row[3];
			$controlchannel = $row[4];
			$arraytype = $row[5];
			$status = $row[6];
			$ownerid = $row[7];
			// Need to get the names of the cy3 and cy5 samples for this array....

			$sampleSQL = "SELECT sampleid, samplename, submitter, submitterid FROM agilent_rnasample WHERE sampleid = $cy3rnasample OR sampleid = $cy5rnasample";
			$sampleresult = $db->Execute($sampleSQL);//mysql_query($sampleSQL);
			if(!$sampleresult){
				echo "<strong>Database Error getting rna samples. SQL: $sampleSQL </strong><br>";
			}else{
				$cy3name = "";
				$cy5name = "";
				
				#while($row=mysql_fetch_row($sampleresult)){
				while($row=$sampleresult->FetchRow()){
					$sampleid = $row[0];
					$samplename = $row[1];
					if($sampleid == $cy3rnasample){
						$cy3name = $samplename;
						$rnaMenu .= "<option value=\"$sampleid\">$samplename</option>";
					}else{
						$cy5name = $samplename;
						$rnaMenu .= "<option value=\"$sampleid\">$samplename</option>";
					}
				}

			}

			// get owner's name
			$ownerSQL = "SELECT firstname, lastname FROM users WHERE id = $ownerid";
			$ownerresult = $db->Execute($ownerSQL);//mysql_query($ownerSQL, $db);
			if(!$ownerresult){
				echo "<strong>Database Error getting owner info. SQL: $ownerSQL </strong><br>";
			}else{
				$row = $ownerresult->FetchRow();//mysql_fetch_row($ownerresult);
				$ownername = $row[0]." ".$row[1];
			}
			$agilentarraytypeSQL = "SELECT organism, arraydesc, version FROM agilentarrays WHERE id = $arraytype";
			$arraytypeResult = $db->Execute($agilentarraytypeSQL);//mysql_query($agilentarraytypeSQL, $db);
			if(!$arraytypeResult){
				echo "<strong>Database Error getting arraytype. SQL: $sampleSQL </strong><br>";
			}else{
				#list($arrayid, $organism, $arraydesc, $version) = mysql_fetch_array($arraytypeResult);
				//list($arrayid, $organism, $arraydesc, $version) = mysql_fetch_array($arraytypeResult);
				$row= $arraytypeResult->FetchRow();
				
				$organism = $row[0];
				$arraydesc = $row[1];
				$version = $row[2];
			}

		}
	#  the onSubmit function for this form is located in newagilentarrayforcheck.js
?>
	<form enctype="multipart/form-data" name="newedgearray" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkArraySubmit()">
	<input type="hidden" name="submitted" value="true">
	<input type="hidden" name="arrayqueueid" value="<?php echo $aqid; ?>">
	<input type="hidden" name="arrayqueueownerid" value="<?php echo $ownerid; ?>">
	<input type="hidden" name="controlchannel" value="<?php echo $controlchannel; ?>">
	<input type="hidden" name="arraytype" value="<?php echo $arraytype; ?>">
	<input type="hidden" name="cy3rnasample" value="<?php echo $cy3rnasample; ?>">
	<input type="hidden" name="cy5rnasample" value="<?php echo $cy5rnasample; ?>">

	<table>
	<tr>
	<td><strong>Array Type/Species</strong></td>
	<td><?php echo "$organism $arraydesc $version"; ?></td>
	</tr>
	<tr>
	<td><strong>Array ID</strong></td>
	<td>Next consecutive ID:<?php echo $arrayID; ?>    <input name="arrayID" type="text" value="<?php echo $arrayID; ?>" align="right"></input></td>
	</tr>
	<tr>
	<td><strong>Array Description</strong></td>
	<td><input name="arraydesc" type="text" align="right" value="<?php echo $arrayname;?>" size="50"></input></td>
	</tr>
	<tr>
	<td><strong>Control Sample Channel:</strong></td>
	<td>
	<?php
		if($controlchannel == 5){
			echo "<font color=\"red\"><strong>cy5</strong></font>";
		}else{
			echo "<font color=\"green\"><strong>cy3</strong></font>";
		}
	?>
	</td>
	</tr>
	<tr>
	<td><strong><font color="red">Cy5 Sample:</font></strong></td>
	<td>
	<?php echo $cy5name; ?>
	</td>
	</tr>
	<tr>
	<td><strong><font color="red">Cy5 Yield:</font></strong></td>
	<td>
	<input name='cy5yield' type='text'></input>
	</td>
	</tr>
	<tr>
	<td><strong><font color="red">Cy5 Specific Activity:</font></strong></td>
	<td>
	<input name='cy5specificactivity' type='text'></input>
	</td>
	</tr>
	<tr>
	<td><strong><font color="green">Cy3 Sample:</font></strong></td>
	<td>
	<?php echo $cy3name; ?>
	</td>
	</tr>
	<tr>
	<td><strong><font color="green">Cy3 Yield:</font></strong></td>
	<td>
	<input name='cy3yield' type='text'></input>
	</td>
	</tr>
	<tr>
	<td><strong><font color="green">Cy3 Specific Activity:</font></strong></td>
	<td>
	<input name='cy3specificactivity' type='text'></input>
	</td>
	</tr>
	<tr>
	<td><strong>Data File:</strong></td>
	<td>
	<input name="file" type="file" size="50"><font color="red"><strong>*</strong></font>
	</td>
	</tr>
	
	
	<tr>
	<td><input type="submit" name="submit" value="Submit"></td>
	<td><input type="reset" value="Reset Form"></td>
	</tr>
	</table>
	</form>
<?php
	}else{
	
		//analyze($_POST);

	
	if(isset($_POST['submitted'])){
			$submitted = $_POST['submitted'];
	}else{
		$submitted = "";
	}
	if(isset($_POST['arrayqueueid'])){
		$arrayqueueid = $_POST['arrayqueueid'];
	}else{
		$arrayqueueid = "";
	}
		if(isset($_POST['arrayqueueownerid'])){
		$arrayqueueownerid = $_POST['arrayqueueownerid'];
	}else{
		$arrayqueueownerid = "";
	}
	if(isset($_POST['controlchannel'])){
		$controlchannel = $_POST['controlchannel'];
	}else{
		$controlchannel = "";
	}
	if(isset($_POST['arraytype'])){
		$arraytype = $_POST['arraytype'];
	}else{
		$arraytype = "";
	}
	if(isset($_POST['cy3rnasample'])){
		$cy3rnasample = $_POST['cy3rnasample'];
	}else{
		$cy3rnasample = "";
	}
	if(isset($_POST['cy3yield'])){
		$cy3yield=$_POST['cy3yield'];
	}else{
		$cy3yield="";
	}
	if(isset($_POST['cy3specificactivity'])){
		$cy3specificactivity=$_POST['cy3specificactivity'];
	}else{
		$cy3specificactivity="";
	}
	if(isset($_POST['cy5yield'])){
		$cy5yield=$_POST['cy5yield'];
	}else{
		$cy5yield="";
	}
	if(isset($_POST['cy5specificactivity'])){
		$cy5specificactivity=$_POST['cy5specificactivity'];
	}else{
		$cy5specificactivity="";
	}
	if(isset($_POST['cy5rnasample'])){
		$cy5rnasample = $_POST['cy5rnasample'];
	}else{
		$cy5rnasample = "";
	}
	if(isset($_POST['arrayID'])){
		$arrayID = $_POST['arrayID'];
	}else{
		$arrayID = "";
	}
	
	if(isset($_POST['arraydesc'])){
		$arraydesc = $_POST['arraydesc'];
	}else{
		$arraydesc = "";
	}
	if(isset($_POST['file'])){
		$file = $_POST['file'];
	}else{
		$file = "";
	}
	
	

	
	//analyze($_POST);
	if($arraytype != ""){
	//echo "arraytype $arraytype submitted...<br>";

	}else{
		die("<br><br>You did not select an array type.  Please go back and do so. <br><br>");
	}
	$my_uploader = new uploader('en'); // errors in English
	//echo "<br>$IMAGESdir<br>";

//echo "$command<br>";
	//$inputfilename = "$IMAGESdir/$randfilename";
				//echo "<br>$command<br>";
	$my_uploader->max_filesize(90000000);
	//$my_uploader->max_image_size(800, 800);
	$my_uploader->upload('file', '', '.txt');
	$my_uploader->save_file($IMAGESdir, 2);

	if ($my_uploader->error) {
		$fileError = 1;
		$fileErrorText = $my_uploader->error;
		print($my_uploader->error . "<br><br>\n");
		die("ERROR UPLOADING FILE!");
	}else{
				//print("Thanks for uploading " . $my_uploader->file['name'] . "<br><br>\n");
		$inputfilename = $my_uploader->file['name'];
		$newfile = $datafileupload."/".$inputfilename;
		$oldfile = $IMAGESdir."/".$inputfilename;
		/*
		
			Need to figure out how to copy the file into a directory where it can be accessed by the end-user when they want to download it.....
		*/
		#if(!copy($oldfile, $newfile)){
		#	echo "<br>Copying of file to storage directory failed.  Please notify administrator.<br>";
		#}
		/*
		Need to put code here to process the file and insert into database
		
		Pretty straight-forward:  get the file, parse it, insert into database....
		
		*/
		// update the arrayqueue table:  UPDATE `edge`.`arrayqueue` SET `dataid` = '6' WHERE `arrayqueue`.`arrayqueueid` =6 LIMIT 1 ;
		$updateaqSQL = "UPDATE `edge`.`arrayqueue` SET `dataid` = '$arrayID' WHERE `arrayqueue`.`arrayqueueid` =$arrayqueueid";
		$updateaqresult = $db->Execute($updateaqSQL);//mysql_query($updateaqSQL, $db);
		if(!$updateaqresult){
			die("<strong>Database Error updating arrayqueue. SQL: $updateaqSQL </strong><br>");
		}
		// PARSING THE FILE....
		// insert this arrays specific data....
		
		$sql = "INSERT agilent_arrayinfo(arrayid, arraytype, arraydesc, FE_data_file, controlchannel, ownerid, cy3rnasample, cy3yield, cy3specificactivity, cy5rnasample,cy5yield,cy5specificactivity) VALUES($arrayID,$arraytype, \"$arraydesc\", \"$inputfilename\",$controlchannel, $arrayqueueownerid, $cy3rnasample, $cy3yield, $cy3specificactivity, $cy5rnasample, $cy5yield, $cy5specificactivity) ON DUPLICATE KEY UPDATE
arrayid=$arrayID";
		//echo "$sql <BR>";
		$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);
		$arrayVersion = $arraytype;
		$originalfilename = $inputfilename;
		$inputfilename = ".$IMAGESreldir/$inputfilename";
		$filenum = rand(0, 25000);
		$randfilename = $filenum . "agilentdata";
		#$command = "cp $inputfilename .$IMAGESreldir/$randfilename";
		#				$str = exec($command);
		$newfile = ".$IMAGESreldir."/".$randfilename";
		#if(!copy($inputfilename, $newfile)){
		#	echo "<br>Copying of file to storage directory failed.  Please notify administrator.<br>";
		#}
		
	
	
		// PARSE the datafile for entry into 'agilentdata' table of EDGE....
		#$inputfilename = "$IMAGESdir/$randfilename";
		$command = "java -mx512m -jar AgilentTabFile2.jar $arrayID $arrayVersion \"$inputfilename\" FeatureNum 	Row 	Col 	SubTypeMask 	SubTypeName 	ProbeUID 	ControlType 	ProbeName 	GeneName	SystematicName 	Description 	PositionX 	PositionY 	LogRatio 	LogRatioError 	PValueLogRatio 	gSurrogateUsed 	rSurrogateUsed 	gIsFound 	rIsFound 	gProcessedSignal 	rProcessedSignal 	gProcessedSigError 	rProcessedSigError 	gNumPixOLHi 	rNumPixOLHi 	gNumPixOLLo 	rNumPixOLLo 	gNumPix 	rNumPix 	gMeanSignal 	rMeanSignal 	gMedianSignal 	rMedianSignal 	gPixSDev 	rPixSDev 	gPixNormIQR 	rPixNormIQR 	gBGNumPix 	rBGNumPix 	gBGMeanSignal 	rBGMeanSignal 	gBGMedianSignal 	rBGMedianSignal 	gBGPixSDev 	rBGPixSDev 	gBGPixNormIQR 	rBGPixNormIQR 	gNumSatPix 	rNumSatPix 	gIsSaturated 	rIsSaturated 	PixCorrelation 	BGPixCorrelation 	gIsFeatNonUnifOL 	rIsFeatNonUnifOL 	gIsBGNonUnifOL 	rIsBGNonUnifOL 	gIsFeatPopnOL 	rIsFeatPopnOL 	gIsBGPopnOL 	rIsBGPopnOL 	IsManualFlag 	gBGSubSignal 	rBGSubSignal 	gBGSubSigError 	rBGSubSigError 	BGSubSigCorrelation 	gIsPosAndSignif 	rIsPosAndSignif 	gPValFeatEqBG 	rPValFeatEqBG 	gNumBGUsed 	rNumBGUsed 	gIsWellAboveBG 	rIsWellAboveBG 	gBGUsed 	rBGUsed 	gBGSDUsed 	rBGSDUsed 	IsNormalization 	gDyeNormSignal 	rDyeNormSignal 	gDyeNormError 	rDyeNormError 	DyeNormCorrelation 	ErrorModel 	xDev 	gSpatialDetrendIsInFilteredSet 	rSpatialDetrendIsInFilteredSet 	gSpatialDetrendSurfaceValue 	rSpatialDetrendSurfaceValue 	SpotExtentX 	SpotExtentY 	gNetSignal 	rNetSignal 	gMultDetrendSignal 	rMultDetrendSignal 	gProcessedBackground 	rProcessedBackground 	gProcessedBkngError 	rProcessedBkngError 	IsUsedBGAdjust 	gInterpolatedNegCtrlSub 	rInterpolatedNegCtrlSub 	gIsInNegCtrlRange 	rIsInNegCtrlRange 	gIsUsedInMD 	rIsUsedInMD >> garbagedump.txt";
		//$command = "java -jar -mx512m AgilentTabFile.jar $arrayID $arrayVersion $inputfilename";
	
		#echo "<br>$command<br>";
		$str=passthru($command);
		//echo " <br>$str<br>";
		$do = unlink($inputfilename);
		if($do=="1"){
			//echo "The file was deleted successfully.";
		} else { echo "There was an error trying to delete the file."; 
		}
		// Upload the parsed output to 'agilentdata' table...
	
		$datafile = $IMAGESdir."/".$originalfilename."output.csv";
		$arraytable = "agilentdata";
		$sql = "LOAD DATA LOCAL INFILE \"$datafile\" INTO TABLE $arraytable FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n'";
		#echo "<br>agilentdata insertion SQL: $sql<br>";
		$insertDataResult = $db->Execute($sql);//mysql_query($sql, $db);
		if(!$insertDataResult){
			echo "<strong>Database Error inserting.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
		}
		//echo "<br>";
		$affectedrows = mysql_affected_rows();
	/*  04MAY2009 - I commented this section out, because the table 'agilentdata2' is not being used in the edge distribution....
		// Parse the data for entry into 'agilentdata2' table of EDGE....
		$command = "java -mx512m -jar AgilentTabFile2.jar $arrayID $arrayVersion $inputfilename FeatureNum 	PositionX 	PositionY 	LogRatio 	LogRatioError 	PValueLogRatio 	gSurrogateUsed 	rSurrogateUsed 	gIsFound 	rIsFound 	gProcessedSignal 	rProcessedSignal 	gProcessedSigError 	rProcessedSigError 	gNumPixOLHi 	rNumPixOLHi 	gNumPixOLLo 	rNumPixOLLo 	gNumPix 	rNumPix 	gMeanSignal 	rMeanSignal 	gMedianSignal 	rMedianSignal 	gPixSDev 	rPixSDev 	gPixNormIQR 	rPixNormIQR 	gBGNumPix 	rBGNumPix 	gBGMeanSignal 	rBGMeanSignal 	gBGMedianSignal 	rBGMedianSignal 	gBGPixSDev 	rBGPixSDev 	gBGPixNormIQR 	rBGPixNormIQR 	gNumSatPix 	rNumSatPix 	gIsSaturated 	rIsSaturated 	PixCorrelation 	BGPixCorrelation 	gIsFeatNonUnifOL 	rIsFeatNonUnifOL 	gIsBGNonUnifOL 	rIsBGNonUnifOL 	gIsFeatPopnOL 	rIsFeatPopnOL 	gIsBGPopnOL 	rIsBGPopnOL 	IsManualFlag 	gBGSubSignal 	rBGSubSignal 	gBGSubSigError 	rBGSubSigError 	BGSubSigCorrelation 	gIsPosAndSignif 	rIsPosAndSignif 	gPValFeatEqBG 	rPValFeatEqBG 	gNumBGUsed 	rNumBGUsed 	gIsWellAboveBG 	rIsWellAboveBG 	gBGUsed 	rBGUsed 	gBGSDUsed 	rBGSDUsed 	IsNormalization 	gDyeNormSignal 	rDyeNormSignal 	gDyeNormError 	rDyeNormError 	DyeNormCorrelation 	ErrorModel 	xDev 	gSpatialDetrendIsInFilteredSet 	rSpatialDetrendIsInFilteredSet 	gSpatialDetrendSurfaceValue 	rSpatialDetrendSurfaceValue 	SpotExtentX 	SpotExtentY 	gNetSignal 	rNetSignal 	gMultDetrendSignal 	rMultDetrendSignal 	gProcessedBackground 	rProcessedBackground 	gProcessedBkngError 	rProcessedBkngError 	IsUsedBGAdjust 	gInterpolatedNegCtrlSub 	rInterpolatedNegCtrlSub 	gIsInNegCtrlRange 	rIsInNegCtrlRange 	gIsUsedInMD 	rIsUsedInMD >> garbagedump.txt";
		//echo "<br>$command<br>";
		$str=passthru($command);
		//echo " <br>$str<br>";
		
		$datafile = $inputfilename."output.csv";
		$arraytable = "agilentdata2";
		$sql = "LOAD DATA LOCAL INFILE \"$datafile\" INTO TABLE $arraytable FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n'";
		//echo "<br>agilentdata insertion SQL: $sql<br>";
		 $insertDataResult = $db->Execute($sql);//mysql_query($sql, $db);
		if(!$insertDataResult){
			echo "<strong>Database Error inserting.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
		}
	*/
		$trxfiledata = "";
		$pgemdata = "";

		/*
		
		 We need to delete the uploaded file.....
		*/
		//$fileToDel = $IMAGESdir."/".$inputfilename;
		#$do = unlink($oldfile);
		#if($do=="1"){
			//echo "The file was deleted successfully.";
		#} else { echo "There was an error trying to delete the file."; }
		$do = unlink($datafile);
		if($do=="1"){
			//echo "The file was deleted successfully.";
		} else { echo "There was an error trying to delete the file."; }
			//echo "<br>";
		// SETTING THINGS UP FOR CREATING THE CONDENSED DATA FILE....
		/*
		 
		 ##############-----04MAY2009-----##########################
		 I removed this condensed stuff from the edge distribution.  It is not going to be used until further notice.
		 ###########################################################
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
					$sqlResult = $db->Execute($sql);//mysql_query($sql,$db);
					$row = $sqlResult->FetchRow();//mysql_fetch_row($sqlResult);
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
		//echo $sql."<br>";
		$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);
		//echo "$sqlResult<br>";
		//$errNum = mysql_errno($db);
		if(!$sqlResult){
			echo "<strong>Database Error inserting.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
		}
	
	
		*/
		if($affectedrows==-1){
				$trxfiledata .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
				$trxfiledata .=  "<strong><font color=\"red\">".mysql_errno($db) . ": " . mysql_error($db). "</font></strong>\n";
				$trxfiledata .=  "<br>heres sql:  <br>";
				$trxfiledata .=  "$sql";
		}else{
			echo "<font color=\"green\"><strong>Data successfully entered into database!</strong></font><br>";
		}
		// Need to invert the log ratios if the channel for control is cy5....
		if($controlchannel  == 5){
			$invertratiossql = "UPDATE agilentdata SET LogRatio = (-1 * LogRatio) WHERE arrayid = $arrayID and LogRatio != 0";
			$sqlResult = $db->Execute($invertratiossql);//mysql_query($invertratiossql, $db);
			//echo "$sqlResult<br>";
			if(!$sqlResult){
				echo "<strong>Database Error inverting LogRatios for agilentdata.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
			}
			/*
			$invertratiossql = "UPDATE agilentdata2 SET LogRatio = (-1 * LogRatio) WHERE arrayid = $arrayID and LogRatio != 0";
			$sqlResult = $db->Execute($invertratiossql);//mysql_query($invertratiossql, $db);
			//echo "$sqlResult<br>";
			//$errNum = mysql_errno($db);
			if(!$sqlResult){
				echo "<strong>Database Error inverting LogRatios for agilentdata2.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
			}
			$invertratiossql = "UPDATE agilentcondenseddata SET LogRatio = (-1 * LogRatio) WHERE arrayid = $arrayID and LogRatio != 0";
			$sqlResult = $db->Execute($invertratiossql);//mysql_query($invertratiossql, $db);
			//echo "$sqlResult<br>";
			if(!$sqlResult){
				echo "<strong>Database Error inverting LogRatios for condensedagilentdata.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
			}
			*/
		}

		// need to update the status of the arrayqueue array
		$aqupdateSQL = "UPDATE arrayqueue SET status = 1 WHERE arrayqueueid = $arrayqueueid";
		$sqlResult = $db->Execute($aqupdateSQL);//mysql_query($invertratiossql, $db);
			//echo "$sqlResult<br>";
			if(!$sqlResult){
				echo "<strong>Database Error updating status of arrayqueue array.  Error #$errNum: .  SQL: $aqupdateSQL</strong><br>";
			}

		# send an email to the user notifying them their arrays have been completed....
		$sql = "SELECT firstname, lastname, email FROM users where id = $arrayqueueownerid";
		$result = $db->Execute($sql);
		if(!$result){
			echo "<strong>Database Error getting user information for sending array completion notice.  Error #$errNum: .  SQL: $sql</strong><br>";
		}
		$row = $result->FetchRow();
		$name = "EDGE3 admin"; //senders name
		$email = $edge3adminemail; //senders e-mail address this value is set in the <webroot>/phpinc/globalvariables.inc.php file
		$recipient = $row[2]; //$row[0]." ".$row[1]; //recipient
		$serverhost = $_SERVER['HTTP_HOST'];
		$mail_body = "Your array, $arraydesc, has been processed and is now ready to be associated with an experiment. \n"; //mail body
		$subject = "Array processed"; //subject
		$header = "From: ". $name . " <" . $email . ">\r\n"; //optional headerfields
		if(ini_get('SMTP') != ''){
			$smtpval = ini_get('SMTP');
			echo "SMTP = $smtpval";
			# Windows smtp setup
			if(!mail($recipient, $subject, $mail_body, $header)){
				echo "Email notifications not configured correctly on this server.";
			}else{
				echo "Email notification sent to EDGE<sup>3</sup> Administrator.";
			}
		}
		if(ini_get('sendmail_path')!= ''){
			# Unix-based setup
			if(!mail($recipient, $subject, $mail_body, $header)){
				echo "ERROR: Email notifications not configured correctly on this server.<br><br>";
			}else{
				echo "An email notification has been sent to the creator of this array.<br><br>";
			}
		}
		
		
		
		/*
		$sent = mail($recipient, $subject, $mail_body, $header); //mail command :) 
		if($sent){
			echo "An email notification has been sent to the creator of this array.<br><br>";
		}else{
			echo "An email notification has not been sent to the creator of this array.  Please contact them.<br><br>";
		}
		*/
	}
	
	}

}

?>
