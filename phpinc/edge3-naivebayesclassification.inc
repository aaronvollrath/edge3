<?php

$algo = -1;

//echo "Order Option is: $orderoptions\n";
$debugid = -1;
if (isset($_POST['submit'])) {
$algo = $_POST['clusterAlgo'];


$nborderedsubmit = $_POST['nborderedsubmit'];
$numberOfArrays = $_POST['numberOfArrays'];
$numberClasses = $_POST['numberClasses'];
$querynum = $_POST['querynum'];
$savedquery = $_POST['savedquery'];
$tempquery = $_POST['tempquery'];
$dataset = $_POST['dataset'];
$orderoptions = $_POST['orderoptions'];
$numberGroups = $_POST['numberGroups'];
$colorScheme = $_POST['colorScheme'];
$rval = $_POST['rval'];
$lval = $_POST['lval'];
$infogain = $_POST['infogain'];
$nbsubmit = $_POST['nbsubmit'];
$orderedIndividually = $_POST['orderedIndividually'];

}

$clusterType = "Classification";

?>

<div>

<?php
#analyze($_POST);
$browserval = 1;
if (isset($_POST['nborderedsubmit']) && $_POST['nborderedsubmit'] == "true"){ // if form has been submitted and it is not being ordered or orderedSubmit is true...
	//analyze($_POST);
	//echo "<hr>in first conditional statemtent...<br>";
	$nborderedsubmit = $_POST['nborderedsubmit'];
		// CHECKING TO SEE IF THE FORM IS BEING SUBMITTED FROM THE ORDERING SCREEN.....
		if($nborderedsubmit == "true"){
				if($userid == $debugid && $debug == 1){
				analyze($_POST);
			}
		}
	$orderedSubmit = true;  // need to set for the commonclusteringsavequerycodeafter
	require('./phpinc/commonclusteringsavequerycodeaftersubmittingforclustering.inc.php');
	
		//echo "Dataset is $dataset<br>";


	$trxidArray = array();
	$trxArray = array();
		$FeatureNumArray = array();// array of clone ids...
	$clonenameArray = array(); // array of clone names....
	$classArray = array(); // array of classes...
	$colorscheme = $_POST['colorScheme'];
	$upperbound = $_POST['rval'];

	$lowerbound = $_POST['lval'];

	//  The following variables are used for the files that are created....

	$filenum = rand(0, 25000);
	$file = "$IMAGESdir/classification/data$filenum.csv";
	$log2file = "$IMAGESdir/classification/log2data$filenum.csv";
	$trxnamesFile = "trxnames$filenum.txt";
	$svgFile = "$filenum.svg";//"$IMAGESdir/classification/$filenum.svg";
	$tableFile = "$IMAGESdir/classification/table$filenum";
	$command = "touch $file";
	//echo $command;
	$str=exec($command);
	$command = "touch $trxnamesFile";
	$str=exec($command);
	$command = "touch $svgFile";
	$str=exec($command);
	$command = "touch $tableFile";
	$str=exec($command);
	$colorscheme = $_POST['colorScheme'];
	//echo "Infogain = $infogain<br>";

	// Open the necessary files for writing....
	$fd = fopen($file, 'w');
	$fdlog2 = fopen($log2file, 'w');
	$fd2 = fopen($trxnamesFile, 'w');



	$arrayidArray = array();
	$arrayDescArray = array();
	if($nborderedsubmit != "true"){
	
	}
	else{
		/***********************************************************************************
		ORDERED SECTION...............
		***********************************************************************************/
		//analyze($_POST);
		//echo "IN ORDERING SECTION....<BR>";
		// For each ordered arrayid, get the description....
		$trxCounter = 0;
		if($orderedIndividually == "true"){
			foreach($option as $opt){
				$val = $opt - 1;
				//echo "$opt\t$trxid[$val];<br>";
				$thisVal = "trxidorder$trxCounter";
				//echo "val = $_POST[$thisVal]";
				$orderedArray[$opt] = $_POST['trxidorder$trxCounter'];
				//$orderedArray[$opt] = $trxid[$val];
				$trxCounter++;
			}
		}
		else{ // Ordered by group.... $orderedIndividually == "false".....

			//echo "Ordered = false";
			$orderedGroupArray = array();
			$numberOfGroups = $numberGroups;
			//echo "<br>The number of groups is: $numberOfGroups<br>";
			for($i = 0; $i < $numberOfGroups; $i++){
				$orderedGroupArray[$i] = array();
				//echo "$orderedGroupArray[$i] test<br>";
			}
			// Create a two-dimensional array w/ the
			$trxCounter = 0;
			foreach($option as $opt){
				$val = $opt - 1;
				$thisVal = "trxidorder$trxCounter";
				//echo "val = $_POST[$thisVal]";
				 //echo "This val = $thisVal<br>";
				//$orderedArray[$opt] = $_POST['trxidorder$trxCounter'];
				//array_push($orderedGroupArray[$val], $trxid[$trxCounter]);
				//echo "$orderedGroupArray[$val]<br>";
				array_push($orderedGroupArray[$val], $_POST[$thisVal]);
				$trxCounter++;
			}
			// Now place the orderedGroupArray into a one dimensional $orderedArray....
			$orderedArray = array();
			for($i = 0; $i<$numberOfGroups; $i++){
				foreach($orderedGroupArray[$i] as $item){
					array_push($orderedArray, $item);
				}
			}
		}
		//echo "The treatments in order: <br>";
		foreach($orderedArray as $order){
			// Now need to determine whether a custom name was entered.....
			if($order != -99){ // If this is not a blank....
				$customid = "customname$order";
				if($_POST[$customid] != ""){
					$newname = $_POST[$customid];
					$newname = str_replace(",", "_", $newname);
					array_push($arrayidArray, $newname);
					array_push($arrayDescArray, $newname);
					$descrip = "$newname";
				}
				else{
				$arraydescSQL = "SELECT arraydesc from agilent_arrayinfo where arrayid = $order ORDER BY arrayid";
				//echo "$arraydescSQL <br>";
				$arraydescResult = mysql_query($arraydescSQL, $db);
				$arrayVal = mysql_fetch_row($arraydescResult);
				
				//echo "$row[0] \t $arrayVal[0]<br>";
				$name = str_replace(",", "_", $arrayVal[0]);
				array_push($arrayidArray, $row[0]);
				array_push($arrayDescArray, $name);
				$descrip = "$name";
				}
			}
			else{
				$descrip = "BLANK";
			}


			$descrip .= "\n";
			fwrite($fd2, $descrip);

		}
		fclose($fd2);
				$arrayidsql = array();
				$experimentcounter = 0;
				foreach($orderedArray as $id){
					$val = " arrayid = $id ";
					$idVal = "$id";
					$idVal .= "\n";
					//fwrite($fd, $idVal);
					array_push($arrayidsql, $val);
					$or = "OR";
					array_push($arrayidsql, $or);
					$experimentcounter++;
				}
				// Pop the last or off...
				array_pop($arrayidsql);
				$arrayidsqlstring = "";
				foreach($arrayidsql as $item){
					$arrayidsqlstring .= $item;
				}
				//$lowerbound = -2;
				//$upperbound = 2;
				$lowerboundmin = false;
				$upperboundmax =false;
				
				include 'convertthresholdvalues.inc';
				//echo "UPPERBOUND = $upperbound<br>";
				//echo "LOWERBOUND = $lowerbound<br>";
				// Now need to deal w/ the possibility that this is a range of values on the induction and/or repression of expression...
				// 4 possibilities...  	1) both values for induction and repression ceilings entered
				//			2) both values for induction and repression ceilings are absent
				//			3) repression ceiling entered and induction ceiling not entered
				// 			4) induction ceiling entered and repression not entered
					$LogRatioconstraint = "";
					if($upperboundmax != "" && $lowerboundmin != ""){
						// either or conditions first... then both entered....
;
						if($upperboundmax != "" && $lowerboundmin == ""){
							// Both rvalmax and lvalmin are blank....
							$LogRatioconstraint = "(LogRatio <= $lowerbound or LogRatio >=
							$upperbound AND LogRatio <= $upperboundmax)";
						}
						else if($upperboundmax == "" && $lowerboundmin != ""){
							$LogRatioconstraint = "(LogRatio <= $lowerbound AND LogRatio >= $lowerboundmin 
							OR LogRatio >= $upperbound)";
						}
						else{ // both of them are entered.....
							$LogRatioconstraint = "(LogRatio <= $lowerbound AND LogRatio >= $lowerboundmin OR 
							LogRatio >= $upperbound AND LogRatio <= $upperboundmax)";
						}

					}else{
						// Both rvalmax and lvalmin are blank....
						$LogRatioconstraint = "(LogRatio <= $lowerbound or LogRatio >= $upperbound)";

					}


			//echo "LogRatioconstraint =$LogRatioconstraint<br>";

			// Create the row of treatment names....
			$counter = 0;
			$item = "Clone#,Gene Name,";
			fwrite($fd, $item);
			//echo "<br> $arrayidsqlstring <br>";
			// SETTING DATA SET TO 1 FOR THE MOMENT...
			$dataset = 1;
			if($dataset == 1){
				$FeatureNumdistinctsql = "SELECT DISTINCT FeatureNum,GeneName from agilentdata where ($arrayidsqlstring) AND ($LogRatioconstraint) AND ControlType != 1 ORDER BY FeatureNum";
			}else{
				$FeatureNumdistinctsql = "SELECT DISTINCT FeatureNum,GeneName from agilentdata where ($arrayidsqlstring)  AND ($LogRatioconstraint) AND ControlType != 1  ORDER BY FeatureNum";
			}
 			$FeatureNumarray = array();
			$cloneContainer = array();
			$cloneNameArray = array();
			$FeatureNumResult = $db->Execute($FeatureNumdistinctsql);  
			$cloneCount = 0;
			while($cloneRow = $FeatureNumResult->FetchRow()){
				$FeatureNum=$cloneRow[0];
				array_push($cloneContainer, $FeatureNum);
				$name = $cloneRow[1];
				$name = str_replace(",", "_", $name);
				$name = str_replace("\"", "", $name);
				$name = str_replace("<", "", $name);
				$name = str_replace(">", "", $name);
				$name = trim($name);
				array_push($clonenameArray, $name);
			}

		// Getting the classes for these particular arrays
		reset($orderedArray);
		//analyze($orderedArray);
		$count = 0;
		reset($option);
		$begin = 0;
		reset($orderedArray);
		$arrayidCount = 0;
		foreach($arrayDescArray as $trx){
			$item = "";
			if($begin != 0){
				$item = ",";
			}
			$item .= "$orderedArray[$begin]:$trx";
			fwrite($fd, $item);
			$begin++;
		}
			$item = "\n";
			fwrite($fd, $item);
			$arrayidCount++;
			$cloneCount = 0;
				$data = "";
				$log2data = "";
				foreach($cloneContainer as $FeatureNum){

					$name = $clonenameArray[$cloneCount];
					$item =  "$FeatureNum,$name,";
					$data .= $item;
					//echo "$cloneCount ==> $name<br>";
					//fwrite($fd, $item);
					$begin = 0;
					foreach($orderedArray as $id){
						$sql = "SELECT LogRatio FROM agilentdata where arrayid = $id and FeatureNum = $FeatureNum";
						$sqlResult = $db->Execute($sql);# or die("Query 2 Failed!!! <br> $sql");
						while($row = $sqlResult->FetchRow()){
							$val = $row[0];
							$item = "";
							$log2item = "";
							if($begin != 0){
								$item = ",";
								$log2item = ",";
							}
							$convertedval = log10ToFoldChange($val);
							$val = $convertedval;
							$item .= "$val";
							$data .= $item;
							if($val < 0){
								$log2val = 1.0/abs($val);
								$log2val = log($log2val)/log(2);
							}else{
								$log2val = log($val)/log(2);
							}
							$log2item .= "$log2val";
							$log2data .=$log2item;
							//fwrite($fd, $item);
							$begin++;
						}
					}
					$item = "\n";
					$data .= $item;
					$log2data .= $item;
					if($cloneCount%25 == 0){
						fwrite($fd, $data);
						fwrite($fdlog2, $log2data);
						$data = "";
						$log2data = "";
     					}
					$cloneCount++;
				}

				//echo "<hr> number of clones: $cloneCount <hr>";
				fwrite($fd, $data);
				fwrite($fdlog2, $log2data);
				//echo "Here's a link to the file in tab-separated format: <a href=\"../IMAGES/log2data$filenum.csv\" target=\"_blank\">Data File</a><br>";

	$begin = 0;
	$item = "CLASS,SPACER";
	fwrite($fd,$item);
	/*
	foreach($classArray as $classval){
		$item = ",$classval";
		fwrite($fd, $item);
	}*/
	// For each arrayid, need to list its class....
	reset($orderedArray);
	reset($option);
	foreach($orderedArray as $id){
		$aval = $id;
		$opt = $option[$id];
		$classval = "class$opt";
		$the_class = ",$_POST[$classval]";
		fwrite($fd, $the_class);
		//echo "$the_class<br>";
	}

	$item = "\n";
	fwrite($fd, $item);
	fclose($fd);
// END OF CREATING DATA FILE.....
#analyze($_POST);
$upfold = log10ToFoldChange($upfold);
$downfold = log10ToFoldChange($downfold);
if(isset($_POST['numberOfArrays'])){
	$arrayidCount= $_POST['numberOfArrays'];
}else{
	$arrayidCount = "Error getting array count";
}
			?>
			<table class="question">
			<thead>
			<tr>
			<th colspan="4"><font><b>Classification Results</b></font></th>
			<th colspan="1"><font><b>SVG Options</b></font></th>
			<th ><font><b>Save Query?</b></font></th>
			</tr>
			</thead>
			<tr class="question">
			<td class="questionparameter"><strong>Number of Arrays Returned:</strong></td>
			<td class="questionanswer"> <?php echo $arrayidCount; ?></td>
			<td class="questionparameter"><strong>Minimal Induction:</strong></td>
			<td class="questionanswer">
			<?php
					/*if($upperboundmax == ""){
						echo $upperbound;
					}
					else{
						echo "[$upperbound,$upperboundmax]";
					}*/
				if(isset($_POST['rval'])){
					echo $_POST['rval'];
				}


			?>
			</td>
			<td class="questionparameter"></td>
			<?php if($savedquery != ""){
				// Does this query have a name???
				$sql = "SELECT queryname FROM savedqueries WHERE query = $savedquery";
				//echo $sql;
				$sqlResult = $db->Execute($sql);
				$row = $sqlResult->FetchRow();
				$name = $row[0];
				//echo "<br>name=$name<br>";
				$update = "true";
				if($name == "" || $name == "NULL"){
					$update = "false";
				}
				if($update == "true"){
			?>
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery&savedquery=$savedquery&submit=true&querytype=2";?>"
				target="<?php echo "_blank$randnum"; ?>">Update?</a></td>
			<?php
				}else{
			?>
					<td class="questionanswer">
				<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a>
				</td>
			<?php
				}
			}else{
   			if(isset($update)){
			?>
				<td class="questionanswer">
				<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a>
				</td>
			<?php
				}
				else{
			?>
				<td class="questionanswer">
					<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a>
				</td>
			<?php
				}
			}
			?>
			</tr>
			<tr class="question">
			<td class="questionparameter"><strong>Number of genes considered:</strong></td>
			<td class="questionanswer"> <?php echo $cloneCount; ?></td>
			<td class="questionparameter"><strong>Minimal Repression:</strong></td>
			<td class="questionanswer">
			 <?php
				if(isset($_POST['lval'])){
					echo $_POST['lval'];
				}

			?></td>
			<td class="questionparameter">
			<a href="<?php echo ".$IMAGESreldir/classification/$filenum.svg"; ?>" onClick="return popup(this,'SVG')">View entire Heat Map</a>
			</td>
			<?php if($savedquery != "" && $update == "true"){
			?>
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save as new query?</a></td>
			<?php
			}
			?>
			</tr>
			<tr class="question">
			<?php if($algo == 0){
			?>

			<?php
			}
			else{
			?>
			
			<td></td><td></td>
			<?php
			}
			if($_SESSION['priv_level'] >= 99){
   ?>
			<td class="questionparameter"><strong>Tabular format:</strong></td><td class="questionanswer"><?php echo "<a href=\"./tabledisplay.php?tableNum=$filenum\" target=\"_blank\">TABLE</a>"; ?></td>
			<?php
   }else{echo "<td></td>";}

			?>
			</tr>

			</table>
			<?php

				$w = $arrayidCount *20 + 500;
				$h = $cloneCount * 10 + 200;


				?>
				<?php

				$accuracy = "true";
				$image = "true";
				$upfold = $upperbound;
				$downfold = $lowerbound;
				$outputfile = "output".$filenum;
				
				$command = "java -mx1024m TransposeData $filenum \"$file\" \"$outputfile\"  >> $IMAGESdir/classification/outfile.txt";
				#echo "$command <br>";
				$str=passthru($command);

			$infogain = $_POST['infogain'];

				$upfold = $_POST['rval'];
				$downfold = $_POST['lval'];
				$command = "java -mx1024m -jar WekaClassification2.jar \"$outputfile\" $accuracy $image $upfold $downfold $infogain \"$trxnamesFile\" >> $IMAGESdir/classification/outfile.txt";
				$str=exec($command);
				#echo "$command<hr>";
				$outputcsv = "output".$filenum.".csv";
				$outputdestcsv = $IMAGESdir."/classification/output".$filenum.".csv";
				$copyfileresult = copy($outputcsv,$outputdestcsv);
				if(!$copyfileresult){
					die("Error copying $outputcsv file.");
				}
				if(!unlink($outputcsv)){
					die("Error deleting $outputscsv file");
				}
				#$command = "mv output$filenum.csv $IMAGESdir/output$filenum.csv";
				//echo "$command <br>";
				#$str=exec($command);
				$outputsvg = "output".$filenum.".svg";
				$outputdestsvg = $IMAGESdir."/classification/".$filenum.".svg";
				$copyfileresult = copy($outputsvg,$outputdestsvg);
				if(!$copyfileresult){
					die("Error copying $outputsvg file.");
				}
				if(!unlink($outputsvg)){
					die("Error deleting $outputsvg file");
				}
				#$command = "mv output$filenum.svg $IMAGESdir/$filenum.svg";
				//echo "$command <br>";
				#$str=exec($command);
				#$command = "gzip --best ./$filenum.svg";
				//echo "$command <br>";
				#$str=exec($command);

				#$command = "mv $IMAGESdir/$filenum.svg.gz ./$filenum.svgz";
				//echo "$command <br>";
				//$str=exec($command);
				$outputdesttable = $IMAGESdir."/table".$filenum;
				$copyfileresult = copy($outputfile,$outputdesttable);
				#echo "outputfile is $outputfile <br>";
				if(!$copyfileresult){
					die("Error copying $outputfile file.");
				}
				if(!unlink($outputfile)){
					die("Error deleting $outputfile file");
				}
				#Delete the graphoutput file
				$graphoutputfile = "graphoutput".$filenum;
				if(!unlink($graphoutputfile)){
					die("Error deleting $graphoutputfile file");
				}
				#Delete the treatment names file.
				if(!unlink($trxnamesFile)){
					die("Error deleting $trxnamesFile file.");
				}
				
				#$command = "mv $outputfile $IMAGESdir/table$filenum";
				#$str=exec($command);
				$w = 1280;
				$h = 1024;
?>
<ul id="globalnav">
				<li><a href="#indiv" onclick="show_querydiv('querysection0',2); return false;" tabindex="0">Results Table</a></li>
				<li><a href="#indiv" onclick="show_querydiv('querysection1',2); return false;" tabindex="1">Heatmap</a></li>
				</ul>
				<br>
				<p>
				<div style="display: block;" id="querysection0" class="classifyscroll">
<?php
	$tablefile = ".$IMAGESreldir/table$filenum";

include($tablefile);
?>
				</div>
				<div  style="display: none;" id="querysection1"> <!-- class="classifyscroll"> -->
					<embed src="<?php echo ".$IMAGESreldir/classification/$filenum.svg" ?>" width=<?php echo $w ?> height=<?php echo $h ?> name="heatmap" type="image/svg+xml" />
				</div>
<?php

	}
echo $str; 
				$end = utime(); $run = $end - $start;

				echo "<br><font size=\"1px\"><b>Query results returned in ";
				echo substr($run, 0, 5);
				echo " secs.</b></font>";

}elseif(isset($_POST['nbsubmit'])){
	$tempquery = $_POST['tempquery'];
	$savedquery = $_POST['savedquery'];
	if(isset($_GET['savedquery'])){
		$query1text = "";
		// NEED TO UPDATE THE TEMP QUERY......
		if($userid == $debugid && $debug == 1){
			echo "<hr>this is a saved query<br>";
		}
		reset ($_POST);
		while(list($key, $val) = each ($_POST)){
			if($key != "submit"){
				$query1text .= "$key=$val:";
				if($userid == $debugid && $debug == 1){
					echo "$key=$val<br>";
				}
			}
		}
					
		$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($tempquery, $userid, \"$query1text\", NOW()) ON DUPLICATE KEY UPDATE query=$tempquery";
		if($userid == $debugid && $debug == 1){
			echo "$sql <br>";
		}
		$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);

		//echo "This is a saved query...<br>";
		// Need to populate the current query screen....
		$sql = "SELECT queryname, query2, query2opts FROM savedqueries WHERE query = $savedquery AND userid = $userid";
		if($userid == $debugid && $debug == 1){
			echo "$sql<br>";
		}
		$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);
		$row = $sqlResult->FetchRow();//mysql_fetch_row($sqlResult);
		$queryname = $row[0];
		$query2 = $row[1];
		$query2opts = $row[2];
		if($userid == $debugid && $debug == 1){
			echo "$query2<br>";
			echo "$query2opts<br>";
		}
		// NOW need to explode $query2 into an array, the separator is :
		$savedvals = explode(":", $query2);
		// pop the last value of due to final :
		array_pop($savedvals);
		if($userid == $debugid && $debug == 1){
			analyze($savedvals);
		}
		// GET THE OPTIONS...
		$savedoptions = explode(":", $query2opts);

		array_pop($savedoptions);
		//analyze($savedoptions);

		}else{
			if($userid == $debugid && $debug == 1){
				echo "this is NOT a saved query<br>";
			}
			// Form is being submitted from the first data input screen....
			// BUT THIS IS USING THE *CUSTOM* ORDERING AND NAMING OF THE ARRAYS....
			// Get the value for this query and then update the table accordingly...
			// Later an option will be given so that the user can save the query....
			// Get the POST values and concatenate them....
			$query1text = "";
			reset ($_POST);
			foreach ($_POST as $key => $val) {
				if($key != "submit"){
					$query1text .= "$key=$val:";
					//echo "$key=$val<br>";
				}
			}
			
			//echo "in query 1 submit section<br>";
			$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($tempquery, $userid, \"$query1text\", NOW()) ON DUPLICATE KEY UPDATE
query=$tempquery";
			if($userid == $debugid && $debug == 1){
				echo "$sql <br>";
			}
			$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);
		}

	$trxidarray = array();
	$chemSQL = "SELECT DISTINCT arrayid FROM agilent_arrayinfo ORDER BY arrayid";
	$chemResult = $db->Execute($chemSQL);

	while($row = $chemResult->FetchRow()){
		// Check to see which boxes were checked...
		$sampleid = $row[0];
		$thisVal = "array$sampleid";
		if(isset($_POST[$thisVal])){
			$post = $_POST[$thisVal];
			//$post = substr("$post", 0, -1);
			if($post != ""){
			//	$chemLookUpSQL = "SELECT chemical FROM chem where chemid = $post ORDER BY chemid";
			//	echo $chemLookUPSQL;
			//	$chemLookUpResult = mysql_query($chemLookUpSQL, $db);
			//	$chemrow = mysql_fetch_row($chemLookUpResult);
				array_push($trxidarray, $post);
			}
		}
	}
	$numvals = count($trxidarray);
	$chemArray = array();
	foreach($trxidarray as $arrayid){
		$arrayStr = " arrayid = $arrayid ";
		array_push($chemArray, $arrayStr);
		$or = "OR";
		array_push($chemArray, $or);
	}
	// Pop the last or off...
	array_pop($chemArray);

	$chemidStr = "";
	foreach($chemArray as $item){
		$chemidStr .= $item;
	}
	#echo "$chemidStr<br>";
	$arrayidArray = array();
	$arrayDescArray = array();

	$privval = $_SESSION['priv_level'];

	if($privval == ""){
		$priv = 1;
	}
	else{
		$priv = $privval;
	}


	//echo $chemidStr;
	// NOW NEED TO GET ALL THE TREATMENTS ASSOCIATED W/ THE CHOSEN CHEMICALS....
	// BASICALLY GETTING THE ARRAYIDS BECAUSE SAMPLEID = ARRAYID
	$arrayidSQL = "SELECT arrayid FROM agilent_arrayinfo where $chemidStr ORDER BY arrayid";
	#echo "$arrayidSQL<br>";
	$arrayidResult = $db->Execute($arrayidSQL);
	while($row = $arrayidResult->FetchRow()){
		//echo "<p>Sample #$row[0] chosen</p>";
		if($priv != 99){
			$arraydescSQL = "SELECT arraydesc from agilent_arrayinfo where arrayid = $row[0] AND (ownerid = 1 OR ownerid = $priv) ORDER BY arrayid";

		}
		else{
			$arraydescSQL = "SELECT arraydesc from agilent_arrayinfo where arrayid = $row[0] ORDER BY arrayid";
		}
	//echo $arraydescSQL;
		$arraydescResult = $db->Execute($arraydescSQL);
		$arrayVal = $arraydescResult->FetchRow();
		if($arrayVal != ""){
			//echo "ArrayVal != ''<br>";
			array_push($arrayidArray, $row[0]);
			array_push($arrayDescArray, $arrayVal[0]);
			$descrip = "$arrayVal[0]";
			$descrip .= "\n";
			//fwrite($fd, $descrip);
		}
	}
$length = count($arrayidArray);
//analyze($_POST);
?>

<form name="order" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>" onsubmit="return checkOrder(<?php echo $length; ?>)">
<?php
echo "<table class=\"results\">";
?>
<thead>
<tr>
<th colspan="2"><font><b>Selected Treatments</b></font></th>
<th ><font><b>Assign Class</b></font></th>
<th ><font><b>Custom Treatment Name</b></font></th>
<th width="10"></th>
<th ><font><b>Class Designations</b></font></th>
</tr>
</thead>
<?php
		if($length > 20){
		?>
		<tr>
		<td><input type="submit" name="nbsubmitorder" value="Submit"></td>
		<td></td>
		<td></td>
		<td><input type="reset" value="Reset Form"</td>
		<td></td>
		<td></td>
		
		</tr>
		
		<?php
		}
$counter = 0;
//print "Posted variables: <br>";
  // How many classes are we dealing w/??? Assigned to $numberClasses
	$experimentcounter=0;
	$arrayidsql = array();
	foreach($arrayidArray as $id){
		//$val = " arrayid = $id ";
		$val = " sampledata.sampleid = $id ";
		$idVal = "$id";
		$idVal .= "\n";
		//fwrite($fd, $idVal);
		array_push($arrayidsql, $val);
		$or = "OR";
		array_push($arrayidsql, $or);
		$experimentcounter++;
	}
	// Pop the last or off...
	array_pop($arrayidsql);
	$arrayidsqlstring = "";
	foreach($arrayidsql as $item){
		$arrayidsqlstring .= $item;
	}

	reset ($_POST);
	echo "<input name=\"nborderedsubmit\" type=\"hidden\" value=\"true\">\n";
	echo "<input name=\"numberOfArrays\" type=\"hidden\" value=\"$length\">\n";
	if(isset($_POST['numberGroups'])){
		$numberClasses = $_POST['numberGroups'];
	}else{
		die("An error has occurred.  There should be a value set for numberGroups.");
	} 
	echo "<input name=\"numberClasses\" type=\"hidden\" value=\"$numberClasses\">\n";
	
 	#echo "<input name=\"querynum\" type=\"hidden\" value=\"$querynum\">\n";
	foreach ($_POST as $key => $val) {
		if($key != "submit"){
		echo "<input name=\"$key\" type=\"hidden\" value=\"$val\">\n";
		}
	}


	$colorscheme = $_POST['colorScheme'];
	$upperbound = $_POST['rval'];
	$lowerbound = $_POST['lval'];
	
// If the ordering method is by individual treatments....
	if($orderingMethod == 1){
	echo "<input name=\"orderedIndividually\" type=\"hidden\" value=\"true\">\n";
		for($i = 0; $i < $length; $i++){
			$val = $i + 1;
			// Create the selection menus.....
			$selectMenu .= "<option value=\"$val\">$val</option>\r";
		}
		foreach($arrayidArray as $idVal){
		echo "<tr>";
		$val = $counter + 1;
		echo "<td class=\"questionparameter\">$idVal</td><td class=\"results\">$arrayDescArray[$counter]</td>
			<input name=\"trxidorder$counter\" type=\"hidden\" value=\"$idVal\">
				<td class=\"questionanswer\"><select name='option[$counter]'>
				<option value=\"$val\" selected>$val</option>\r;
					$selectMenu
			</select></td>";
		echo "</tr>";
		$counter++;
		}
		$counter=0;
	}else{
	
	$innercolor = array("lightsteelblue", "DarkKhaki", "salmon", "DarkSeaGreen", "Gainsboro",
			"yellow", "Fuchsia", "LawnGreen", "LightSlateGray", "Olive", "Indigo",
			"PaleVioletRed", "skyblue", "PeachPuff", "Orange", "GoldenRod", "oldlace",
			"pink", "RosyBrown", "green","lightsteelblue","YellowGreen", "salmon",
			"Turquoise", "Thistle", "Peru", "WhiteSmoke");


		echo "<input name=\"orderedIndividually\" type=\"hidden\" value=\"false\">\n";
		// We've got the length and the number of groups....
		if($length <= $numberClasses){
			$numberClasses = $length;
		}
		if(!isset($_GET['savedquery']) || !isset($savedquery)){
			$isSavedQuery = "";
		
		}else{
				$isSavedQuery = "true";
			}

		foreach($arrayidArray as $idVal){
			echo "<tr>";
			$val = $counter + 1;
			// Do we've any name changes here??????
			$savedName = "";
			if($isSavedQuery != ""){
				foreach($savedvals as $nameval){
					$temp = explode("=", $nameval);
					$customname = "customname$idVal";
					if($temp[0]== $customname){
						$savedName = $temp[1];
						break;
					}
				}
			}
			if($savedName == ""){
				$savedName = $arrayDescArray[$counter];
			}
			$selectMenu = "";
			// #################################################
			if(!isset($_GET['savedquery']) || !isset($savedquery)){
				for($i = 0; $i < $numberGroups; $i++){
					$aval = $i + 1;
					// Create the selection menus.....
					$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$aval</option>\r";
				}
			}else{
				$isSavedQuery = "true";
				// What group is it in.... REBUILDING SELECT MENU....
				// Is this an id we had before
				$selectMenu = "";
				$selectValue = "";
				foreach($savedoptions as $arrayval){
					$temp = explode("=", $arrayval);
					//echo "temp[0] = $temp[0] idVal = $idVal temp[1]=$temp[1]<br>";
					if($temp[0] == $idVal){
						$optVal = $temp[1];
							//echo "valC = $valC and optVal = $optVal numberGroups = $numberGroups<br>";
							$selected = "selected";
							if($optVal > $numberGroups){
								// If the number of groups is updated and is less than the previous value, set to the number of groups...
								$optVal = $numberGroups;
							}
							$selectMenu = "<option value=\"$optVal\" $selected style=\"background-color: $innercolor[$optVal];\">$optVal</option>\r";
							$selectValue = $optVal;
						break;
					}
				}
				for($i = 0; $i < $numberGroups; $i++){
						$aval = $i + 1;
						//if($selectValue != $val){
						// Create the selection menus.....
						//echo "inloop...";
						$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$aval</option>\r";
						//}
				}

			}
			

			// Do we've any groups selected and what group is this trx in???????????
			$savedgroup = "";
			if($isSavedQuery != ""){
				foreach($savedvals as $nameval){
					$temp = explode("=", $nameval);
					$customname = "customname$idVal";
					if($temp[0]== $customname){
						$savedName = $temp[1];
						break;
					}
				}

			}
			if($savedName == ""){
				$savedName = $arrayDescArray[$counter];
			}
			$size = strlen($savedName);
			if($size < 40){
				$size = 40;
			}
			echo "<td class=\"questionparameter\">$idVal</td><td class=\"results\">$savedName</td>
			<input name=\"trxidorder$counter\" type=\"hidden\" value=\"$idVal\">

					<td class=\"questionanswer\"><select name='option[$idVal]' >
						$selectMenu
				</select></td>
				<td class=\"results\">
			<input name=\"customname$idVal\" type=\"text\" value=\"$savedName\" size=\"$size\" align=\"right\"></td><td width=\"10\"></td>";
			//echo "val = $val  NumberOfGroups=$numberGroups<br>";
			// How many classes are there????
			if($val <= $numberGroups){

				$group = $val;
				$isChecked = "";
				if($isSavedQuery != ""){
					foreach($savedvals as $nameval){
						$temp = explode("=", $nameval);
						$customclasslabel = "class$group";
						if($temp[0] == $customclasslabel){
							$classlabel = $temp[1];
							break;
						}
					}
				}
				// Need to get the classes for the selected arrays.....
				$currentClass = $val - 1;
				//if($val <= $numberClasses){
				if($isSavedQuery != ""){
					echo "<td class=\"questionanswer\"><input style=\"background-color: $innercolor[$currentClass];\" type=\"text\" name=\"class$group\" value=\"$classlabel\"></td>";
				}else{
					echo "<td class=\"questionanswer\"><input style=\"background-color: $innercolor[$currentClass];\" type=\"text\" name=\"class$group\" value=\"$val Class $val\"></td>";
				}
			}
			else{
				echo "<td></td>";
			}
			if($isSavedQuery != ""){
				$selectMenu = "";
			}
			echo "</tr>";
			$counter++;
		}
		$counter=0;
	}

?>
<tr>
<td><input type="submit" name="nbsubmitorder" value="Submit"></td>
<td></td>
<td></td>
<td><input type="reset" value="Reset Form"</td>
<td></td>

</tr>
<?php
echo "</table>";
echo "</form>";
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
}else{
		$privval = $_SESSION['priv_level'];
		
		if($privval == ""){
			$priv = 1;
		}
		else{
			$priv = $privval;
		}
		// This is the sql required to get the list of chemicals...
		//$chemSQL = "SELECT DISTINCT chemid, chemical, trx_type FROM chem ORDER BY chemid";
		if($priv != 99){
		$chemSQL = "SELECT DISTINCT s.chemid, c.chemical, c.trx_type FROM array AS a, sampledata AS s, chem AS c
				WHERE (a.ownerid = $priv OR a.ownerid = 1) AND s.sampleid = a.arrayid AND c.chemid = s.chemid
				ORDER BY c.chemical";
		//print $chemSQL;
		}
		else{
			$chemSQL = "SELECT DISTINCT chemid, chemical, trx_type FROM chem ORDER BY chemical";
		}
		
		
		require('./phpinc/commonclusteringsavequerycode.inc');
		
		
		?>
		<p class="styletext">
		<form name="query" method="post" onsubmit="return checkClassificationForm()" action="<?php  $_SERVER['PHP_SELF'] ?>">
		
		<table class="question" width="400">
		<table>
		<tr>
		<th class="mainheader" colspan="2"><font color='black'><b>Query Parameters</b></font></th>
		<th class="mainheader" ></th>
		</tr>
		<tr>
		<td  class="questionanswer" colspan="2"><strong>Data Options:</strong></td>
		<td  class="questionanswer"><strong>Your Query Options:</strong></td>
		</tr>
		
		
		
		
		<?php
			// IF THIS IS A SAVED QUERY, WE'VE GOT TO HAVE A VALUE FOR THIS..
		
		if(isset($_GET['savedquery'])){
			$savedquery = $_GET['savedquery'];
		}else{
			$savedquery = "";
		}
			echo "<input name=\"savedquery\" type=\"hidden\" value=\"$savedquery\">\n";
			// IF A TEMP query's involved, gotta have that....
			echo "<input name=\"tempquery\" type=\"hidden\" value=\"$tempquery\">\n";
		
		
		
			// what dataset is checked???
		
			if($savedquery != ""){
				$kval = $savedvals['numberGroups'];
		
			}
			else{
				$kval = 4;
			}
		?>
		
		</tr>
		<tr id="kmeansoption">
		<input type="hidden" name="orderoptions" value="2"></input>
		<td class="questionparameter"><strong>Number of Defined Classes:</strong></td>
		<td class="results">
		<input name="numberGroups" type="text" value="<?php echo $kval; ?>" size="5" align="right">
		</td>
		<td valign="top" class="results">
		<div id="toolbar1" dojoType="dijit.Toolbar" style="width:150px;"><button dojoType="dijit.form.ComboButton" iconClass="queryMenuIcon"
										optionsTitle='load options'
										onClick='' id="loadquery">
										<span><strong><font color="blue">Load Query Menu</font></strong></span>
		
										<div dojoType="dijit.Menu" id="loadMenu" style="display: none;">
											<div dojoType="dijit.MenuItem"
												iconClass="mySavedQueryOpen"
												onClick="querySavedLoad(<?php echo $_SESSION['userid'];?>)">
												Load Saved Query
											</div>
											<div dojoType="dijit.MenuItem"
												iconClass="myTempQueryOpen"
												onClick="queryTempLoad()">
												Load Recent Query
											</div>
										</div>
									</button>
					</div>
		<div dojoType="dijit.Tooltip" connectId="loadquery"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><b>Click to load a previously executed query.</b><p>  You can load a query saved previously by selecting <font color="blue"><strong>Saved Query</strong></font>.  Additionally, the last three queries that you performed are available when you select  <font color="red"><strong>Load Recent Query.</strong></font></p></tr></td></table>
		
		<br>
		
		</p>
		 </div>
		</td>
		</tr>
		</table>
		<?php
		require('./displayexperimentsbygroups.php');
		?>
		<table>
		<tr>
		<td  class="questionanswer" colspan="3"><strong>Heat Map Options</strong></td>
		</tr>
		
		<tr>
		<td class="questionparameter" ><strong>Heat Map Color Scheme:</strong></td>
		<td class="results">
		<?php
		// what colorscheme is checked???
		if(isset($_GET['savedquery'])){
		
				if($savedvals['colorScheme'] == 0){
					$gr = "checked";
					$yb = "";
				}
				else{
					$gr = "";
					$yb = "checked";
				}
			}
			else{
				$gr = "checked";
					$yb = "";
			}
		?>
		<input type="radio" name="colorScheme" <?php echo $gr; ?> value="0"><font color="red"><strong>Red</font>/<font color="green">Green</font></strong><br>
		<input type="radio" name="colorScheme" <?php echo $yb; ?> value="1"><font color="yellow"><strong>Yellow</font>/<font color="blue">Blue</font></strong><br>
		</td>
		<td class="results">
		</td>
		</tr>
		
		
		<tr>
		<td  class="questionanswer" colspan="3"><strong>Threshold Values:</strong></td>
		</tr>
		<tr>
		<td class="questionparameter" ><strong>Minimum Induction:</strong></td>
		<td class="results">
		<?php
		
			if(isset($_GET['savedquery'])){
				$oval = $savedvals['rval'];
				
			}
			else{
				$oval = 3;
				$mval = "";
			}
		?>
		<input size="4" name="rval" type="text" value="<?php echo $oval; ?>" align="right"></input>
		</td>
		<td class="results">
		<font color="red"><b>NOTE: </b></font>Value must be positive.
		</td>
		</tr>
		
		<tr>
		<td class="questionparameter" ><strong>Minimum Repression:</strong></td>
		<td class="results">
		<?php
		
			if(isset($_GET['savedquery'])){
				$oval = $savedvals['lval'];
				
			}
			else{
				$oval = -3;
				$mval = "";
			}
		?>
		<input size="4" name ="lval" type="text" value="<?php echo $oval; ?>" align="right"></input>
		</td>
		<td class="results">
		<font color="red"><b>NOTE: </b></font>Value must be negative.
		</td>
		</tr>
		<tr>
		<td class="questionparameter"><strong>Information Gain:</strong></td>
		<td class="results"><input size="4" name ="infogain" type="text" value="20" align="right"></input></td>
		<td></td>
		
		</tr>
		
		<tr>
		<td><input type="submit" name="nbsubmit" value="Submit"></td>
		<td></td>
		<td><input type="reset" value="Reset Form"</td>
		</tr>
		
		</table>
		
		</form>
		</p>
<?php

}


?>
