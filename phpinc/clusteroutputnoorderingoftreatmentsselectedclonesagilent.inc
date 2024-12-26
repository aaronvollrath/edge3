<?php

$debug = 0; // when debug == 1, debug messages show up.  otherwise, they don't.


$countSQL = "SELECT MAX(arrayid) from agilentdata";
		$countResult = $db->Execute($countSQL);//mysql_query($countSQL, $db);
		$row = $countResult->FetchRow();//mysql_fetch_row($countResult);
		$maxarrayID = $row[0];

$userid = $_SESSION['userid'];

// this is very inefficient, but we need to get the arrayversion....
for($i = 1; $i <= $maxarrayID;$i++){

	$postval = "array$i";
	if(isset($_POST[$postval])){
		$val=$_POST[$postval];
		//echo "$val ...<br>";
		if($val > 0){
			$organismarraycheck = $val;
			break;
		}
	}

}
$orgSQL = "SELECT arrayversion FROM agilentdata WHERE arrayid=$organismarraycheck";
	//echo $orgSQL;
	$orgresult = $db->Execute($orgSQL);//mysql_query($orgSQL, $db);
	$orgrow= $orgresult->FetchRow();//mysql_fetch_row($orgresult);
	$thisorganism = $orgrow[0];



 
	$arrayurl = "./agilentarrayinfo.php?arrayid=\n";
	require './phpinc/organismurlselection.inc';
	fwrite($fd, $arrayurl);

	fwrite($fd, $featureurl);
	fwrite($csvfd, ",,,");
	$arrayidarray = array();
	for($i = 1; $i <= $maxarrayID;$i++){
		$postval = "array$i";
		if(isset($_POST[$postval])){
			$val=$_POST[$postval];
			//echo "$val ...<br>";
			if($val > 0){
				$sql = "SELECT arraydesc from agilent_arrayinfo where arrayid = $val";
				$sqlResult = $db->Execute($sql);//mysql_query($sql,$db);
				$row = $sqlResult->FetchRow();//mysql_fetch_row($sqlResult);
				//echo "$row[0] <br>";
				$desc = $row[0] . "\n";
	
				fwrite($fd, $desc);
				$csvdesc = ",$row[0]";
				fwrite($csvfd, $csvdesc);
				array_push($arrayidarray, $val);
			}
		}
	}
	$userid = $_SESSION['userid'];
	fwrite($csvfd, "\n");
				if($userid == 1){
					//analyze($arrayidarray);
				}
/*	$length = count($arrayidarray);
	$end = utime();
	$run = $end - $start;
	echo "<font size=\"1px\"><b>Got array descriptions in  ";
	echo substr($run, 0, 5);
	echo " secs.</b></font>";
*/
	// Now need to get the expression values that meet the thresholds set for the arrays selected...
	$arrayidsql = array();
				$experimentcounter = 0;
				foreach($arrayidarray as $id){
					$val = " d.arrayid = $id ";
					$idVal = "$id";
					$idVal .= "\n";
					fwrite($fd, $idVal);
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
// Now need to deal w/ the possibility that this is a range of values on the induction and/or repression of expression...
					// 4 possibilities...  	1) both values for induction and repression ceilings entered
					//			2) both values for induction and repression ceilings are absent
					//			3) repression ceiling entered and induction ceiling not entered
					// 			4) induction ceiling entered and repression not entered





					$finalratioconstraint = "";
					if($upperboundmax != "" && $lowerboundmin != ""){
						// either or conditions first... then both entered....
;
						if($upperboundmax != "" && $lowerboundmin == ""){
							// Both rvalmax and lvalmin are blank....
							$finalratioconstraint = "(d.LogRatio <= $lowerbound or d.LogRatio >=
							$upperbound AND LogRatio <= $upperboundmax)";
						}
						else if($upperboundmax == "" && $lowerboundmin != ""){
							$finalratioconstraint = "(d.LogRatio <= $lowerbound AND d.LogRatio >= $lowerboundmin
							OR LogRatio >= $upperbound)";
						}
						else{ // both of them are entered.....
							$finalratioconstraint = "(d.LogRatio <= $lowerbound AND d.LogRatio >= $lowerboundmin OR
							d.LogRatio >= $upperbound AND d.LogRatio <= $upperboundmax)";
						}

					}else{


						// Both rvalmax and lvalmin are blank....
						$finalratioconstraint = "(d.LogRatio <= $lowerbound or d.LogRatio >= $upperbound)";

					}

				$text = $_POST['cloneList'];

				$text = str_replace("\n", ",", $text);
				$pieces = explode(",", $text);
				//echo(implode(" ", $pieces));
				$text = "";
				//echo "You entered ".count($pieces)." Clone ID numbers";
				$cloneidarray = array();
				$cloneContainer = array();
				$discardedFeatures = array();
				$cloneCount = 0;

				//  Need to create the code for generating the sql query list of clones

				while(count($pieces) >= 1){
					$cloneid = trim(array_shift($pieces));
					// Skipped lines need to be bypassed....
					if($cloneid == ""){
						continue;
					}
					$clonestr = " d.FeatureNum = $cloneid ";
					array_push($cloneContainer, $cloneid);
					array_push($cloneidarray, $clonestr);
					$cloneor = "OR";
					array_push($cloneidarray, $cloneor);
					$cloneCount++;
				}

				// Get rid of the last OR....
			array_pop($cloneidarray);
			// Create the cloneid sql string
			$cloneidsqlstr = "";
			foreach($cloneidarray as $item){
				$cloneidsqlstr .= $item;
			}
				
				if($dataset == 1){
					/*$thresholdsql = "SELECT DISTINCT d.FeatureNum FROM agilentdata AS d WHERE ($arrayidsqlstring)  and $finalratioconstraint and ( $cloneidsqlstr ) and (d.gMeanSignal >= $gmeansignal and d.rMeanSignal >= $rmeansignal and d.PValueLogRatio <= $pValue) ORDER BY d.FeatureNum";*/
					$featurenumSQL = "SELECT DISTINCT d.FeatureNum/*, d.GeneName, d.SystematicName*/ from $arraydatatable as d where ($arrayidsqlstring) and $finalratioconstraint and (d.ControlType = '0') and (d.gProcessedSignal >= $gprocessedsignal and d.rProcessedSignal >= $rprocessedsignal and d.PValueLogRatio <= $pValue) and ($cloneidsqlstr) ORDER BY d.FeatureNum";
					#  on 6JUN2009 i (aaron vollrath) took this out of the $featurenumSQL : AND (d.gIsFeatNonUnifOL = '0' and d.rIsFeatNonUnifOL='0')
					}else{ 
					$thresholdsql = "SELECT DISTINCT d.FeatureNum FROM agilentcondenseddata AS d WHERE ($arrayidsqlstring)  and $finalratioconstraint and ( $cloneidsqlstr ) ORDER BY d.FeatureNum";
					$featurenumSQL = "SELECT DISTINCT d.FeatureNum from agilentcondenseddata as d where ($arrayidsqlstring) and $finalratioconstraint";

					}

//echo "$featurenumSQL <br>";
				if($userid == 1 && $debug == 1){
					echo "$featurenumSQL <br>";
				}
					$cloneidarray = array();
					$cloneContainer = array();
					$discardedFeatures2 = array();
					$cloneCount = 0;
					$geneNameContainer = array();
					$systematicNameContainer = array();

					$fnResult = $db->Execute($featurenumSQL);//mysql_query($featurenumSQL, $db);
					#while($row = mysql_fetch_row($fnResult)){

					while($row = $fnResult->FetchRow()){
						$featuresql = "SELECT GeneSymbol, SystematicName FROM $annotationtable WHERE FeatureNum = $row[0]";
						$featureresult=$db->Execute($featuresql);
						$featurerow = $featureresult->FetchRow();
						$clonestr = " FeatureNum = $row[0] ";
						array_push($cloneContainer, $row[0]);
						array_push($geneNameContainer, $featurerow[0]);
						array_push($systematicNameContainer,$featurerow[1]);
						array_push($cloneidarray, $clonestr);
						$cloneor = "OR";
						array_push($cloneidarray, $cloneor);
						$cloneCount++;
						//echo "$clonestr<br>";
					}

				
			// TABLE OF INFORMATION FOR CLUSTERING RESULTS.....
				require('edge3clusteringtableresults.inc');
?>
