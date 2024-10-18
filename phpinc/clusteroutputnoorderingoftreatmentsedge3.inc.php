<?php

$debug = 1;

if($dataset == 1){
	$arraydatatable = "agilentdata";
	$countSQL = "SELECT MAX(arrayid) from $arraydatatable";
	$countResult = $db->Execute($countSQL);//mysql_query($countSQL, $db);
	$row = $countResult->FetchRow();//mysql_fetch_row($countResult);
	$maxarrayID = $row[0];
}else{
	$arraydatatable = "agilentcondenseddata";
	$countSQL = "SELECT MAX(arrayid) from $arraydatatable";
	$countResult = $db->Execute($countSQL);//mysql_query($countSQL, $db);
	$row = $countResult->FetchRow();//mysql_fetch_row($countResult);
	$maxarrayID = $row[0];


}

$userid = $_SESSION['userid'];
	$arrayurl = "./agilentarrayinfo.php?arrayid=\n";
	for($i = 1; $i <= $maxarrayID;$i++){
		$postval = "array$i";
		if(isset($_POST[$postval])){
			$val=$_POST[$postval];
			if($val > 0){
				// look up the organism...
				$organismSQL = "SELECT arrayversion FROM agilentdata WHERE arrayid = $val LIMIT 1";
				if($userid == 1 && $debug == 1){
					echo "<br>$organismSQL<br>";
				}
				$organismResult = $db->Execute($organismSQL);//mysql_query($organismSQL, $db);
				$organismrow = $organismResult->FetchRow();//mysql_fetch_row($organismResult);
				$thisorganism = $organismrow[0];
				break;
			}
		}
	}
	if($userid == 1 && $debug == 1){
		echo "thisorganism is a: $thisorganism<br><hR><HR>";
	} 
require './phpinc/organismurlselection.inc';
	fwrite($fd, $arrayurl);
	fwrite($fd, $featureurl);
	fwrite($csvfd, ",,,");
	$arrayidarray = array();
	$filenamesarray = array();
	$filedescarray = array();
	for($i = 1; $i <= $maxarrayID;$i++){
		$postval = "array$i";
		if(isset($_POST[$postval])){
			$val=$_POST[$postval];
			//echo "$val ...<br>";
			if($val > 0){
				$sql = "SELECT arraydesc,FE_data_file from agilent_arrayinfo where arrayid = $val";
				$sqlResult = $db->Execute($sql);//mysql_query($sql,$db);
				$row = $sqlResult->FetchRow();//mysql_fetch_row($sqlResult);
				///echo "$row[0] <br>";
				$desc = $row[0] . "\n";
				$csvdesc = ",$row[0]";
				fwrite($fd, $desc);
				fwrite($csvfd, $csvdesc);
				array_push($arrayidarray, $val);
				array_push($filedescarray, $row[0]);
				array_push($filenamesarray, $row[1]);
			}
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

	if($filter != 0){
		// Now need to deal w/ the possibility that this is a range of values on the induction and/or repression of expression...
		// 4 possibilities...  	1) both values for induction and repression ceilings entered
		//			2) both values for induction and repression ceilings are absent
		//			3) repression ceiling entered and induction ceiling not entered
		// 			4) induction ceiling entered and repression not entered
		$finalratioconstraint = "";
		if($upperboundmax != "" && $lowerboundmin != ""){
			// either or conditions first... then both entered....
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
	}else{
		# get the standard deviation filtered features....
		$filenamebase = "SDTEST";
		$sdval = 2;
		applystandarddeviationfilterviaR($filenamebase, $filenamesarray,$filedescarray, $sdval);
		die("After making the call....");


	}
				if($_SESSION['userid'] == 1 && $debug == 1){
					echo "$featurenumSQL <br>";
				}
					$cloneidarray = array();
				$cloneContainer = array();
				$geneNameContainer = array();
				$systematicNameContainer = array();
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
						if($primaryname == ""){
							$primaryname = $refseq;
						}

						

					}else{ // using condensed data....
						$annoSQL = "SELECT DISTINCT GeneName, SystematicName FROM agilentdata WHERE FeatureNum = $cloneid AND arrayid = 1";
						//echo "<br>$annoSQL<br>";
						$annoResult = $db->Execute($annoSQL);//mysql_query($annoSQL, $db);
						$annos = $annoResult->FetchRow();//mysql_fetch_row($annoResult);
						$primaryname = str_replace("\"","", $annos[0]);
					$primaryname = trim(ucfirst ( $primaryname));
						$primaryname = substr($primaryname, 0, 50);

						$refseq = trim($annos[1]);


					}
					array_push($geneNameContainer, $primaryname);
					array_push($systematicNameContainer, $refseq);
				}
				// TABLE OF INFORMATION FOR CLUSTERING RESULTS.....
				require('edge3clusteringtableresults.inc');
?>
