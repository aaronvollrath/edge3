<?php
$debug = 0;
$countSQL = "SELECT MAX(arrayid) from $arraydatatable";
//echo "<br>$countSQL<br>";
		$countResult = $db->Execute($countSQL);// mysql_query($countSQL, $db);
		$row = $countResult->FetchRow();//mysql_fetch_row($countResult);
		$maxarrayID = $row[0];
//echo "<br>the max array id = $maxarrayID<BR>";
		$userid = $_SESSION['userid'];
		//echo "This is ordered!";
		//analyze($_POST);
		// For each ordered arrayid, get the description....
		$trxCounter = 0;

//echo "analyzing option <br>";
//analyze($option);
$tempquery = $_POST['tempquery'];
$savedquery = $_POST['savedquery'];
$userid = $_SESSION['userid'];
$arrayurl = "http://edge.oncology.wisc.edu/agilentarrayinfo.php?arrayid=\n";
	for($i = 1; $i <= $maxarrayID;$i++){
		$postval = "array$i";
		if(isset($_POST[$postval])){
			$val=$_POST[$postval];
			//echo "value = $val<br>";
			if($val > 0){
				// look up the organism...
				$organismSQL = "SELECT DISTINCT arrayversion FROM agilentdata WHERE arrayid = $val";
				//if($userid == 1){
					//echo "<br>$organismSQL<br>";
				//}
				$organismResult = $db->Execute($organismSQL);//mysql_query($organismSQL, $db);
				$organismrow = $organismResult->FetchRow();//mysql_fetch_row($organismResult);
				$thisorganism = $organismrow[0];
				break;
			}
		}
	}
	$arrayurl = "http://edge.oncology.wisc.edu/agilentarrayinfo.php?arrayid=\n";
	require './phpinc/organismurlselection.inc';
	fwrite($fd, $arrayurl);
	fwrite($fd, $featureurl);
fwrite($csvfd, ",,,");

//echo "<br>orderedIndividually = $orderedIndividually<br>";
		if($orderedIndividually == "true"){
//echo "Ordered = true<hr>";
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

		//	echo "Ordered = false";

			
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
			// Now place the orderedGroupArray into a one dimensional
			$orderedArray = array();
			for($i = 0; $i<$numberOfGroups; $i++){
				foreach($orderedGroupArray[$i] as $item){
					array_push($orderedArray, $item);
				}
			}
		}
		//echo "The treatments in order: <br>";
		//echo "Ordered array....<BR>";
		//analyze($orderedArray);
		//echo "#######################<BR>";
		$csvdesc = "";
		foreach($orderedArray as $order){
			// Now need to determine whether a custom name was entered.....
			if($order != -99){ // If this is not a blank....
				$customid = "customname$order";
				if($_POST[$customid] != ""){
					$newname = $_POST[$customid];
					array_push($arrayidarray, $newname);
					array_push($arrayDescArray, $newname);
					$descrip = "$newname";
					$csvdesc = "$newname";
				}
				else{
				$arraydescSQL = "SELECT arraydesc from agilent_arrayinfo where arrayid = $order ORDER BY arrayid";
				//echo "$arraydescSQL <br>";
				$arraydescResult = $db->Execute($arraydescSQL);//mysql_query($arraydescSQL, $db);
				$arrayVal = $arraydescResult->FetchRow(); //mysql_fetch_row($arraydescResult);
				//echo "$row[0] \t $arrayVal[0]<br>";
				array_push($arrayidarray, $row[0]);
				array_push($arrayDescArray, $arrayVal[0]);
				$descrip = "$arrayVal[0]";
				$csvdesc = "$arrayVal[0]";
				}
			} 
			else{
				$descrip = "BLANK";
				$csvdesc = "\t";
			}
				
/*
			$csvdesc =",$descrip";
			$descrip .= "\n";

			fwrite($fd, $descrip);
			fwrite($csvfd, $csvdesc);
		}
		fwrite($csvfd, "\n");
*/				$csvdesc =",$csvdesc";
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
							$finalratioconstraint = "(LogRatio <= $lowerbound or LogRatio >=
							$upperbound AND LogRatio <= $upperboundmax)";
						}
						else if($upperboundmax == "" && $lowerboundmin != ""){
							$finalratioconstraint = "(LogRatio <= $lowerbound AND LogRatio >= $lowerboundmin
							OR LogRatio >= $upperbound)";
						}
						else{ // both of them are entered.....
							$finalratioconstraint = "(LogRatio <= $lowerbound AND LogRatio >= $lowerboundmin OR
							LogRatio >= $upperbound AND LogRatio <= $upperboundmax)";
						}

					}else{
						// Both rvalmax and lvalmin are blank....
						$finalratioconstraint = "(LogRatio <= $lowerbound or LogRatio >= $upperbound)";

					}



				if($dataset == 1){
					$featurenumSQL = "SELECT DISTINCT d.FeatureNum, d.GeneName, d.SystematicName from $arraydatatable as d where ($arrayidsqlstring) and $finalratioconstraint and (d.ControlType = '0') and (d.gProcessedSignal >= $gprocessedsignal and d.rProcessedSignal >= $rprocessedsignal and d.PValueLogRatio <= $pValue)  AND (d.gIsFeatNonUnifOL = '0' and d.rIsFeatNonUnifOL='0') ORDER BY d.FeatureNum";
			}
			else{
				$featurenumSQL = "SELECT DISTINCT d.FeatureNum from agilentcondenseddata as d where ($arrayidsqlstring) and $finalratioconstraint";
			}
				if($userid == 1 && $debug == 1){
					echo "userid-based debug<br>";
					echo "$featurenumSQL <br>";
				}
				#die($featurenumSQL);
				$cloneidarray = array();
				$cloneContainer = array();
				$geneNameContainer = array();
				$systematicNameContainer = array();
				$cloneidarray = array();
				$cloneContainer = array();
				$featureNumResult = $db->Execute($featurenumSQL);//mysql_query($featurenumSQL, $db);
				$cloneCount = 0;
				//while($cloneRow = mysql_fetch_row($featureNumResult)){
				while($cloneRow = $featureNumResult->FetchRow()){
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

					}else{ // using condensed data....
						$annoSQL = "SELECT DISTINCT GeneName, SystematicName FROM $arraydatatable WHERE FeatureNum = $cloneid AND arrayid = 1";
						$annoResult = $db->Execute($annoSQL);//mysql_query($annoSQL, $db);
						$annos = $annoResult->FetchRow();//mysql_fetch_row($annoResult);
						$primaryname = str_replace("\"","", $annos[0]);
						$primaryname = trim(ucfirst ( $primaryname));
						$primaryname = substr($primaryname, 0, 50);

						$refseq = trim($annos[1]);


					}
					if($userid == 1){
						//echo "$primaryname<br>";
					}
					array_push($geneNameContainer, $primaryname);
					array_push($systematicNameContainer, $refseq);
				}

		$arrayidarray = $orderedArray;

   //echo "<br>CloneCount = $cloneCount<br>";
		

				// TABLE OF INFORMATION FOR CLUSTERING RESULTS.....
				require('edge3clusteringtableresults.inc');



?>
