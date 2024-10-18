<?php
		/***********************************************************************************
		ORDERED SECTION...............
		**********************************************************************************/
	$debug = -1;
		if($debug ==1){
	analyze($_POST);
		//echo "organism array check = $trxidorder0<br>";
	}
	
	$organismarraycheck = $_POST['trxidorder0'];
	$orgSQL = "SELECT arrayversion FROM agilentdata WHERE arrayid=$organismarraycheck";
	if($debug ==1){
		echo $orgSQL;
	}
	$orgresult = $db->Execute($orgSQL);//mysql_query($orgSQL, $db);
	$orgrow=$orgresult->FetchRow();//mysql_fetch_row($orgresult);
	$thisorganism = $orgrow[0];
	
	$arrayurl = "http://edge.oncology.wisc.edu/agilentarrayinfo.php?arrayid=\n";
	require './phpinc/organismurlselection.inc';
	fwrite($fd, $arrayurl);
	fwrite($fd, ".".$featureurl);
	fwrite($csvfd, ",,,");
	$userid = $_SESSION['userid'];
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

			// What groups where to have blanks after them????
			// If they do, we need to put a -99 at the end of the respective array...
			//echo "before checking...<br>";
			for($i = 1; $i <= $numberOfGroups; $i++){
				//echo "checking....<br>";
				$groupVal = "group$i";
				$val = $i - 1;
				if(isset($_POST[$groupVal])){
					$groupChecked = $_POST[$groupVal];
					if($groupChecked != ""){
						// This group was checked to have a blank at the end...
						array_push($orderedGroupArray[$val], "-99");
					}
				}
			}

			//analyze($orderedGroupArray);
			// Now place the orderedGroupArray into a one dimensional $orderedArray....
			$orderedArray = array();
			for($i = 0; $i<$numberOfGroups; $i++){
				foreach($orderedGroupArray[$i] as $item){
					array_push($orderedArray, $item);
				}
			}
		}
		//echo "The treatments in order: <br>";
		$number_of_arrays = 0; // used to keep track of legitimate arrays (ie.not blanks)
		foreach($orderedArray as $order){
			// Now need to determine whether a custom name was entered.....
			if($order != -99){ // If this is not a blank....
				$number_of_arrays++;
				$customid = "customname$order";
				if($_POST[$customid] != ""){
					$newname = $_POST[$customid];
					array_push($arrayidarray, $newname);
					array_push($arrayDescArray, $newname);
					$descrip = "$newname";
				}
				else{
				$arraydescSQL = "SELECT arraydesc from agilentexperiments where arrayid = $order ORDER BY arrayid";
				//echo "$arraydescSQL <br>";
				$arraydescResult = mysql_query($arraydescSQL, $db);
				$arrayVal = mysql_fetch_row($arraydescResult);
				//echo "$row[0] \t $arrayVal[0]<br>";
				array_push($arrayidarray, $row[0]);
				array_push($arrayDescArray, $arrayVal[0]);
				$descrip = "$arrayVal[0]";
				}
			}
			else{
				$descrip = "BLANK";
			}

			$csvdesc =",$descrip";
			fwrite($csvfd, $csvdesc);
			$descrip .= "\n";
			fwrite($fd, $descrip);
			

		}
			$csvdesc = "\n";
			fwrite($csvfd, $csvdesc);
				$arrayidsql = array();
				$experimentcounter = 0;
				foreach($orderedArray as $id){
					$val = " arrayid = $id ";
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
				//$lowerbound = -2;
				//$upperbound = 2;

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
				if($userid == 1){
				//echo "You entered ".count($pieces)." Clone ID numbers";
				}
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
					$clonestrd = " d.FeatureNum = $cloneid ";
					array_push($cloneContainer, $cloneid);
					array_push($cloneidarray, $clonestrd);
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
					// what is the first array # that's all we need to query for names...
					$val = " arrayid = $orderedArray[0] ";
					$featurenumSQL = "SELECT DISTINCT d.FeatureNum/*, d.GeneName, d.SystematicName*/ FROM agilentdata AS d WHERE ($arrayidsqlstring)  and $finalratioconstraint  and (d.ControlType = '0') and (d.gProcessedSignal >= $gprocessedsignal and d.rProcessedSignal >= $rprocessedsignal and d.PValueLogRatio <= $pValue) and ($cloneidsqlstr) ORDER BY d.FeatureNum";
					#$featurenumSQL = "SELECT d.FeatureNum, d.GeneName, d.SystematicName from $arraydatatable as d where $val and $finalratioconstraint and (d.ControlType = '0') and (d.gProcessedSignal >= $gprocessedsignal and d.rProcessedSignal >= $rprocessedsignal and d.PValueLogRatio <= $pValue) /*AND (d.gIsFeatNonUnifOL = '0' and d.rIsFeatNonUnifOL='0')*/ and ($cloneidsqlstr) ORDER BY d.FeatureNum";
					//$featurenumSQL = "SELECT d.FeatureNum, d.GeneName, d.SystematicName from $arraydatatable as d, agilentcontroltype as c where ($arrayidsqlstring) and $finalratioconstraint and (c.ControlType = '0' and d.FeatureNum = c.FeatureNum) and (d.gProcessedSignal >= $gprocessedsignal and d.rProcessedSignal >= $rprocessedsignal and d.PValueLogRatio <= $pValue) AND (d.gIsFeatNonUnifOL = '0' and d.rIsFeatNonUnifOL='0') and ($cloneidsqlstr) ORDER BY d.FeatureNum";
				}else{
					$featurenumSQL = "SELECT DISTINCT d.FeatureNum FROM agilentcondenseddata AS d WHERE ($arrayidsqlstring)  and $finalratioconstraint and ( $cloneidsqlstr ) ORDER BY d.FeatureNum";
					//$featurenumSQL = "SELECT DISTINCT d.FeatureNum from agilentcondenseddata as d where ($arrayidsqlstring) and $finalratioconstraint";

				}
					if($debug == 1){

						echo "<br>$featurenumSQL<br><hr>";
					}	
				$cloneidarray2 = array();
				$cloneContainer = array(); // Reset cloneContainer
				$discardedFeatures2 = array();
				$cloneCount = 0;
				$cloneidarray = array();
					// reset cloneidsqlstr...
					$cloneidsqlstr = "";
					$geneNameContainer = array();
					$systematicNameContainer = array();
					$fnResult = $db->Execute($featurenumSQL);#mysql_query($featurenumSQL, $db);
					#while($row = mysql_fetch_row($fnResult)){
					
					while($row = $fnResult->FetchRow()){
						$featuresql = "SELECT GeneSymbol, SystematicName FROM $annotationtable WHERE FeatureNum = $row[0]";
						$featureresult=$db->Execute($featuresql);
						$featurerow = $featureresult->FetchRow();
						$clonestr = " FeatureNum = $row[0] ";
						array_push($cloneContainer, $row[0]);
						array_push($cloneidarray, $clonestr);
						array_push($geneNameContainer, $featurerow[0]);
						array_push($systematicNameContainer,$featurerow[1]);
						$cloneor = "OR";
						array_push($cloneidarray, $cloneor);
						$cloneCount++;
						//echo "$clonestr<br>";
					}
			
			
		$arrayidarray = $orderedArray;
// TABLE OF INFORMATION FOR CLUSTERING RESULTS.....
				require('edge3clusteringtableresults.inc');

?>
