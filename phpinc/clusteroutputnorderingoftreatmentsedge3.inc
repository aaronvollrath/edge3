<?php
// Assign the POST vars....
echo "<hr>IN clusteredoutputnoorderingoftreatmentsedge3.inc";


if($dataset == 1){
	$arraydatatable = "agilentdata";
	$countSQL = "SELECT MAX(arrayid) from $arraydatatable";
	$countResult = mysql_query($countSQL, $db);
	$row = mysql_fetch_row($countResult);
	$maxarrayID = $row[0];
}else{
	$arraydatatable = "agilentcondenseddata";
	$countSQL = "SELECT MAX(arrayid) from $arraydatatable";
	$countResult = mysql_query($countSQL, $db);
	$row = mysql_fetch_row($countResult);
	$maxarrayID = $row[0];


}
analyze($_POST);
die('exiting....');
//echo "<br>$countSQL<br>";
$debug = 0;
/*
$countSQL = "SELECT MAX(arrayid) from $arraydatatable";
		$countResult = mysql_query($countSQL, $db);
		$row = mysql_fetch_row($countResult);
		$maxarrayID = $row[0];
		$userid = $_SESSION['userid'];
echo "<br>$countSQL<br>";
*/
//echo "<br>maxarrayid : $maxarrayID<br>";
// Determine what arrays were selected....
//echo "csv file : $csvfd<br>";
$userid = $_SESSION['userid'];
	$arrayurl = "./agilentarrayinfo.php?arrayid=\n";
	for($i = 1; $i <= $maxarrayID;$i++){
		$postval = "array$i";
		$val=$_POST[$postval];
		if($val > 0){
			// look up the organism...
			$organismSQL = "SELECT arrayversion FROM agilentdata WHERE arrayid = $val";
			if($userid == 1 && $debug == 1){
				echo "<br>$organismSQL<br>";
			}
			$organismResult = mysql_query($organismSQL, $db);
			$organismrow = mysql_fetch_row($organismResult);
			$thisorganism = $organismrow[0];
			break;
		}
	}
	if($userid == 1 && $debug == 1){
		// what the hell is this organism?
		echo "thisorganism is a: $thisorganism<br>";
		//analyze($_POST);

	}

require './phpinc/organismurlselection.inc';
	fwrite($fd, $arrayurl);
	fwrite($fd, $featureurl);
	fwrite($csvfd, ",,,");
	$arrayidarray = array();
	for($i = 1; $i <= $maxarrayID;$i++){
		$postval = "array$i";
		$val=$_POST[$postval];
		//echo "$val ...<br>";
		if($val > 0){
			$sql = "SELECT arraydesc from agilent_arrayinfo where arrayid = $val";
			$sqlResult = mysql_query($sql,$db);
			$row = mysql_fetch_row($sqlResult);
			///echo "$row[0] <br>";
			$desc = $row[0] . "\n";
			$csvdesc = ",$row[0]";
			fwrite($fd, $desc);
			fwrite($csvfd, $csvdesc);
			array_push($arrayidarray, $val);
		}
	}
	$length = count($arrayidarray);
		fwrite($csvfd, "\n");
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

			if($dataset == 1){
					$featurenumSQL = "SELECT DISTINCT d.FeatureNum, a.GeneSymbol, a.SystematicName from $arraydatatable as d, agilentcontroltype as c, $annotationtable as a where ($arrayidsqlstring) and $finalratioconstraint and (c.ControlType = '0' and d.FeatureNum = c.FeatureNum) and (d.gProcessedSignal >= $gprocessedsignal and d.rProcessedSignal >= $rprocessedsignal and d.PValueLogRatio <= $pValue) AND (d.gIsFeatNonUnifOL = '0' and d.rIsFeatNonUnifOL='0') AND (d.FeatureNum = a.FeatureNum) ORDER BY d.FeatureNum";
			}
			else{
				$featurenumSQL = "SELECT DISTINCT d.FeatureNum from agilentcondenseddata as d where ($arrayidsqlstring) and $finalratioconstraint";
			}
				//if($_SESSION['userid'] == 1 && $debug == 1){
					echo "$featurenumSQL <br>";
				//}
					$cloneidarray = array();
				$cloneContainer = array();
				$geneNameContainer = array();
				$systematicNameContainer = array();
				$featureNumResult = mysql_query($featurenumSQL, $db);
				$cloneCount = 0;
				while($cloneRow = mysql_fetch_row($featureNumResult)){
					$cloneid=$cloneRow[0];
					$clonestr = " FeatureNum = $cloneid ";
					array_push($cloneContainer, $cloneid);

					array_push($cloneidarray, $clonestr);
					$cloneor = "OR";
					array_push($cloneidarray, $cloneor);
					$cloneCount++;
					if($dataset == 1){
     						$primaryname = str_replace("\"","", $cloneRow[1]);
						$primaryname = trim(ucfirst ( $primaryname));
						
						$primaryname = substr($primaryname, 0, 50);

						$refseq = trim($cloneRow[2]);
						if($primaryname == ""){
							$primaryname = $refseq;
						}

						

					}else{ // using condensed data....
						$annoSQL = "SELECT DISTINCT GeneName, SystematicName FROM agilentdata WHERE FeatureNum = $cloneid AND arrayid = 1";
						//echo "<br>$annoSQL<br>";
						$annoResult = mysql_query($annoSQL, $db);
						$annos = mysql_fetch_row($annoResult);
						$primaryname = str_replace("\"","", $annos[0]);
					$primaryname = trim(ucfirst ( $primaryname));
						$primaryname = substr($primaryname, 0, 50);

						$refseq = trim($annos[1]);


					}
					array_push($geneNameContainer, $primaryname);
					array_push($systematicNameContainer, $refseq);
				}

			if($cloneCount > 0){
			// Get rid of the last OR....
			array_pop($cloneidarray);

			// Create the cloneid sql string
			$cloneidsqlstr = "";
			foreach($cloneidarray as $item){
				$cloneidsqlstr .= $item;
			}
			$hybArray = array();
			$hybArray2 = array();
			$totalArray = array();
			// GET ALL THE HYBRIDIZATION INFO FOR THESE ARRAYS.....
			// GET THESE DATA ONE AT A TIME AND ORDER BY CLONE NUMBER......
			$counter = 1;
			foreach($arrayidarray as $arrayid){
				//echo "<br>Counter = $counter<br>";
				if($dataset == 1){
					$hybSQL = "SELECT arrayid, FeatureNum, LogRatio, PValueLogRatio, gIsFeatNonUnifOL, rIsFeatNonUnifOL FROM $arraydatatable where arrayid = $arrayid and ( $cloneidsqlstr ) ORDER BY FeatureNum";
				}else{ //using condensed data
					$hybSQL = "SELECT arrayid, FeatureNum, LogRatio FROM agilentcondenseddata where arrayid = $arrayid and ( $cloneidsqlstr ) ORDER BY FeatureNum";
				}
				if($userid==1 && $debug == 1){
					echo "$hybSQL <br>";
				}

				$hybResult = mysql_query($hybSQL, $db);
				while($row = mysql_fetch_row($hybResult)){
					$cloneid = $row[1];  // agilent featurenum...

					$finalratio = strval($row[2]); // agilent logratio
					$thisFeaturePValue= $row[3];
					$thisFeaturegIsFeatNonUnifOLValue = $row[4];
					$thisFeaturerIsFeatNonUnifOLValue = $row[5];

					if($userid == 1 && $debug == 1){
						//echo "arrayid: $row[0] FeatureNum: $row[1]<br>";
						//echo "gIsFeatNonUnifOLValue : $thisFeaturegIsFeatNonUnifOLValue gIsFeatNonUnifOLValue: $thisFeaturerIsFeatNonUnifOLValue<br>";
					}

					// need to convert the finalratio from log base 10 to fold change...
					if($finalratio >=0){
						$finalratio = pow(10,$finalratio);
					}else{
						$intermediateval = pow(10,$finalratio);
						$finalratio = -1/$intermediateval;
					}
					if($userid == 1 && $debug == 1){
						echo "Final ratio for cloneid, $cloneid, is $finalratio<br>";
					}
					if($finalratio == ""){
						echo "Final ratio for cloneid, $cloneid, is missing<br>";
					}
					// TEMPORARY STOP-GAP put in place until fixed
					if($arraytype != 2){
						if($thisFeaturegIsFeatNonUnifOLValue == 1 || $thisFeaturerIsFeatNonUnifOLValue == 1){
							$finalratio = -99999999;
						}
					}
					$hybArray[$cloneid] = $finalratio;
				}
				array_push($totalArray, $hybArray);
				//analyze($hybArray);
				reset($hybArray);
				$hybArray = array();
				$counter++;
			}
			$arrayidCount = count($arrayidarray);
			//echo "ARRAY ID COUNT: $arrayidCount<br>";
			$newArray = array();
			$numclones = count($cloneContainer);
			//echo "The number of clones: $numclones<br>";
			//for($i = 1; $i <= $cloneCount; $i++){
			foreach($cloneContainer as $i){
				for($j = 0; $j < $arrayidCount; $j++){
					//echo "totalArray $i,$j = $totalArray[$j][$i]<br>";
					$newArray[$i][$j] = $totalArray[$j][$i];
				}

			}

			$index = 0;
			foreach($cloneContainer as $i){
				if($dataset != 1){
					$i = $i * -1;
				}
				$primaryname = $geneNameContainer[$index];
				$refseq = $systematicNameContainer[$index];
				// Vestigial GO stuff from edge clustering algo....
				$goIDCounter = 0;
				$goidarray = array();

			$line = "$i\t $primaryname \t$refseq \t$goIDCounter";
			$csvline = "$i,$primaryname,$refseq,$goIDCounter";
			$gostr = "";
				if(count($goidarray) > 0){
					foreach($goidarray as $goid){
						$gostr .= "\t$goid";
						$csvgostr .=",$goid";
					}
				}
				if($i < 0){
					//need to convert back to positive
					// for indexing $newArray array
					$i = $i * -1;
				}
				$line .= $gostr;
				$csvline .= $csvgostr;
				foreach($newArray[$i] as $key=>$value) {
					$line.= "\t$value";
					$csvline.=",$value";
				}
				$line .= "\n";
				$csvline .= "\n";
				//echo "$line <br>";
				fwrite($fd, $line);
				fwrite($csvfd, $csvline);
				fflush($fd);
				fflush($csvfd);
				$index++;
			}

			ftruncate($fd, ftell($fd));
			fclose($fd);
			ftruncate($csvfd, ftell($csvfd));
			fclose($csvfd);




			

				// TABLE OF INFORMATION FOR CLUSTERING RESULTS.....
				require('edge3clusteringtableresults.inc');






				echo $str;
				$end = utime(); $run = $end - $start;

				echo "<br><font size=\"1px\"><b>Query results returned in ";
				echo substr($run, 0, 5);
				echo " secs.</b></font>";
					//unlink($file);
				}
				else{
					echo "<br>There were no genes that met your criteria.<br>";
					$end = utime(); $run = $end - $start;
				echo "<font size=\"1px\"><b>Query results returned in ";
				echo substr($run, 0, 5);
				echo " secs.</b></font>";
				}

?>
