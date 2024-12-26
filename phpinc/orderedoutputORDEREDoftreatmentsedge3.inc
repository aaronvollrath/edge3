<?php
//analyze($_POST);
$tempquery = $_POST['tempquery'];
$debug =-1; // set debug =1 and the debugid to your id if you want to display debug messages.
$debugid = 0;
		/***********************************************************************************
		ORDERED SECTION...............
		***********************************************************************************/
		//echo "This is ordered!";
		// need to get array type...
	$trxidorder0 = $_POST['trxidorder0'];
	$organismarraycheck = $trxidorder0;
	if($userid == $debugid  && $debug == 1){
		analyze($_POST);
		echo "organism array check = $trxidorder0<br>";
		echo "tempquery value = $tempquery<br>";
	}
	$orgSQL = "SELECT arrayversion FROM agilentdata WHERE arrayid=$organismarraycheck";
	if($userid == $debugid  && $debug == 1){
		analyze($_POST);
		echo "organism array check = $trxidorder0<br>";
		echo $orgSQL;
	}
	//echo "<br>$orgSQL<br>";
	//analyze($_POST);
	$orgresult = $db->Execute($orgSQL);//mysql_query($orgSQL, $db);
	$orgrow= $orgresult->FetchRow();//mysql_fetch_row($orgresult);
	$thisorganism = $orgrow[0];

	$arrayurl = "./agilentarrayinfo.php?arrayid=\n";
	require './phpinc/organismurlselection.inc';
	fwrite($fd, $arrayurl);
	fwrite($fd, $featureurl);
	fwrite($csvfd, ",,,");
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
				}else{
					$groupChecked = "";
				}
				if($groupChecked != ""){
					// This group was checked to have a blank at the end...
					array_push($orderedGroupArray[$val], "-99");
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
		$csvdesc = "";		
		
		foreach($orderedArray as $order){
			// Now need to determine whether a custom name was entered.....
			if($order != -99){ // If this is not a blank....
				$number_of_arrays++;
				$customid = "customname$order";
				if($_POST[$customid] != ""){
					$newname = $_POST[$customid];
					array_push($arrayidarray, $newname);
					array_push($arrayDescArray, $newname);
					$csvdesc = "$newname";
					$descrip = "$newname";
					#echo "$csvdesc <br>";
				}
				else{
				$arraydescSQL = "SELECT arraydesc from agilent_arrayinfo where arrayid = $order ORDER BY arrayid";
				//echo "$arraydescSQL <br>";
				$arraydescResult =  $db->Execute($arraydescSQL);//mysql_query($arraydescSQL, $db);
				$arrayVal = $arraydescResult->FetchRow();//mysql_fetch_row($arraydescResult);
				//echo "$row[0] \t $arrayVal[0]<br>";
				array_push($arrayidarray, $row[0]);
				array_push($arrayDescArray, $arrayVal[0]);
				$descrip = "$arrayVal[0]";
				$csvdesc = "$arrayVal[0]";
				#echo "$csvdesc <br>";
				}
			}
			else{
				$descrip = "BLANK";
				$csvdesc = "\t";
			}
				$csvdesc =",$csvdesc";
			fwrite($csvfd, $csvdesc);
			$descrip .= "\n";
			fwrite($fd, $descrip);
			

		}
			$csvdesc = "\n";
			fwrite($csvfd, $csvdesc);
/*
			$csvdesc =",$cvsdescr";
			$descrip .= "\n";
			fwrite($fd, $descrip);
			fwrite($csvfd, $csvdesc);

		}
		fwrite($csvfd, "\n");
*/
		$arrayid = $order; // set the arrayid to the last arrayid so we can get the descrips later....
		
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
					if(!is_numeric($cloneid)){
						continue;
					}
					$clonestr = " FeatureNum = $cloneid ";
					// Is this a valid clone id?
					$validsql = "SELECT COUNT(FeatureNum) FROM agilentdata WHERE FeatureNum = $cloneid AND arrayid = $arrayid";
					//echo "$validsql <br>";
					$validResult = $db->Execute($validsql);//mysql_query($validsql, $db);
					$validRow = $validResult->FetchRow();//mysql_fetch_row($validResult);
					if($validRow[0] != 0){
						array_push($cloneContainer, $cloneid);
						array_push($cloneidarray, $clonestr);
						$cloneor = "OR";
						array_push($cloneidarray, $cloneor);
						$cloneCount++;
					}else{
						continue;

					}
				}

				// Get rid of the last OR....
			array_pop($cloneidarray);

		$arrayidarray = $orderedArray;

$upperbound = 0;
$lowerbound = 0;
   //echo "<br>CloneCount = $cloneCount<br>";
if(isset($_POST['anova'])){
	$anova = $_POST['anova'];
}else{
	$anova = -1;
}
if(isset($_POST['ttest'])){
	$ttest = $_POST['ttest'];
}else{
	$ttest = -1;
}
if(isset($_POST['ttestpvalue'])){
	$ttestpvalue = $_POST['ttestpvalue'];
}else{
	$ttestpvalue = -1;
}
if(isset($_POST['anovapvalue'])){
	$anovapvalue = $_POST['anovapvalue'];
}else{
	$anovapvalue = -1;
}
if(isset($_POST['minuscontrol'])){
	$minuscontrol = $_POST['minuscontrol'];
}else{
	$minuscontrol = -1;
}
if(isset($_POST['correctionval'])){
	$correctionval = $_POST['correctionval'];
}else{
	$correctionval = -1;
}
		if($cloneCount > 1){
			$cloneCount = 0; //reset cloneCount....
			$arrayidCount = count($arrayidarray);
			//echo "ARRAY ID COUNT: $arrayidCount<br>";
			$newArray = array();

		
// the following four arrays are used for the purpose of creating the cy3/cy5 values table....
			$cy3Array = array();
			$cy5Array = array();
			$cy3TotalArray = array();
			$cy5TotalArray = array();

			// a foreach loop to write the arraydesc to processVals files....
			$processedValsline = ",,,";
			$logfileheader = ",,,";
			$processedValsTableline = "<html><body><table><tr><th bgcolor='green'>cy3</th><th bgcolor='red'>cy5</th><th></th>";
			foreach($arrayidarray as $anID){
				if($anID > 0){
					$idSQL = "SELECT arraydesc FROM agilent_arrayinfo WHERE arrayid = $anID";
					$result = $db->Execute($idSQL);//mysql_query($idSQL, $db);
					$iddesc = $result->FetchRow(); //mysql_fetch_row($result);
					$processedValsline .= "$iddesc[0],,";
					$processedValsTableline .= "<th colspan='2'>$iddesc[0]</th>";
					$logfileheader .="$iddesc[0],";
				}
			}
			$processedValsTableline .= "</tr>";
			$processedValsline .= "\n";
			$logfileheader .= "\n";
			fwrite($logfd,$logfileheader);
			fwrite($processedfd, $processedValsline);
			fflush($processedfd);
			fwrite($processedtablefd, $processedValsTableline);
			fflush($processedtablefd);






			//$cloneCount = count($cloneContainer);
			foreach($cloneContainer as $i){
				$primarynamesql = "SELECT d.GeneName, d.SystematicName from agilentdata as d where d.FeatureNum = $i and arrayid = $organismarraycheck";
				$pnResult = $db->Execute($primarynamesql);//mysql_query($primarynamesql, $db);
				$pnRow = $pnResult->FetchRow();//mysql_fetch_row($pnResult);
				$primaryname = str_replace("\"","", $pnRow[0]);
				$primaryname = trim(ucfirst ( $primaryname));
				$primaryname = substr($primaryname, 0, 50);
				$refseq = trim($pnRow[1]);
				// Vestigial GO stuff from edge clustering algo....
				$goIDCounter = 0;
				$goidarray = array();
				
			$line = "$i\t $primaryname \t$refseq \t$goIDCounter";
			$csvline ="$i,$primaryname,$refseq,$goIDCounter";
			$logfileline = "$i,$primaryname,$refseq";
			$processedValsline = "$i,$primaryname,$refseq";
			$processedValsTableline = "<tr><td>$i</td><td>$primaryname</td><td>$refseq</td>";
			$gostr = "";

				$ratiovalsline = "";
				$ratiovalscsvline = "";
				if(count($goidarray) > 0){
					foreach($goidarray as $goid){
						$gostr .= "\t$goid";
					}
				}
				$line .= $gostr;
				if($anova == 1 || $ttest == 1 || $minuscontrol == 1){
						$logratioarray = array(); // stores an array of LogRatio values....
				}
				$index = 0;
				foreach($arrayidarray as $arrayidval){
					
					
					if($arrayidval != -99){
						$featuresql = "SELECT d.LogRatio, d.gProcessedSignal, d.rProcessedSignal from agilentdata as d where d.FeatureNum = $i and arrayid = $arrayidval";
						//echo "$featuresql<br>";
						$aResult = $db->Execute($featuresql);//mysql_query($featuresql, $db);
						$pnRow = $aResult->FetchRow(); //mysql_fetch_row($aResult);
						$value = $pnRow[0];
						if($value >=0){
								$value = pow(10,$value);
							}else{
								$intermediateval = pow(10,$value);
								$value = -1/$intermediateval;
							}
						
						if($anova == 1 || $ttest == 1 || $minuscontrol==1){
								$logratioarray[$index] =$pnRow[0];  // use the log ratio values instead of fold change....
								//echo "$pnRow[0]<br>";
						}
						//foreach($newArray[$i] as $key=>$value) {
							$ratiovalsline.= "\t$value";
							$ratiovalscsvline .=",$value";
							$processedValsline .= ",$pnRow[1],$pnRow[2]";
							$processedValsTableline .= "<td bgcolor='green'>$pnRow[1]</td><td bgcolor='red'>$pnRow[2]</td>";
						//}
					}else{
						$ratiovalsline .= "\t-9999";
						$ratiovalscsvline .= ",";
						if($anova == 1 || $ttest == 1 || $minuscontrol == 1){
							$logratioarray[$index] = -9999;
						}
					}
					
					$index++;


				}
				//analyze($logratioarray);
				$skipgene = 0;
				if($ttest == 1){
					// We need to see if this gene is differentially expressed.
					$index = 0;
					// How many groups are we dealing w/
					$grouparray = array();
					$groupnum = 0;
					foreach($logratioarray as $value) {
						//echo "key = $key : value = $value<br>";
						if($value != -9999){
							$grouparray[$groupnum][$index] = $value;
						}else{
							$groupnum++;
						}
						$index++;
					}

					if($_POST['minuscontrol'] == 1){
						$controlmean = average($grouparray[0]);	
						//echo "control mean : $controlmean<br>";
						$numpops = count($grouparray);
						$arraycount = 0;
						//echo "<hr><hr>";
						//analyze($grouparray);
						
						for($k = 0; $k < $numpops; $k++){
							
							$numelements = count($grouparray[$k]);
							if($arraycount != 0){
						
								$arraycount++;  // we need to do this because of the problem w/ separators...
							}
							//echo "number of elements in group $i: $numelements<br>";
							for($j=0; $j < $numelements; $j++){
								
								$currentval = $grouparray[$k][$arraycount] - $controlmean;
								$grouparray[$k][$arraycount] = $currentval;
								$arraycount++;
							}
							//$popsmeanarray[$i] = $popelementsum/$popelementcount;
						}		
						//analyze($grouparray);
						//echo "<hr><hr>";			
						
					}
					//echo "$primaryname<br>";
					//analyze($grouparray);
					//echo "$primaryname<br>";
					$correction = $_POST['correction'];
					if($correction == ""){
						$correction = -1;
						$correctionval = -1;
					}else{
						
						if($ttestpvalue == 1){
							$pval = 0.10;
						}elseif($ttestpvalue == 2){
							$pval = 0.05;
						}elseif($ttestpvalue == 3){
							$pval = .025;
						}elseif($ttestpvalue == 4){
							$pval = .01;
						}elseif($ttestpvalue == 5){
							$pval = .005;
						}else{
							$pval = .001;
						}
						$numfeatures = count($cloneContainer);
						if($correction==1){
							//echo "bonferroni selected w/ p-value initially @ $pval<br>";
							
							//echo "numfeatures = $numfeatures<br>";
							$correctionval = fdrqvalue($pval, $numfeatures);
							//echo "bonferroni corrected p-value = $pval<br>";;
						}elseif($correction == 2){
							
							$correctionval = bonferroni($pval, $numfeatures);
						}
							

					}
					
					
					
					//echo "$primaryname<br>";
					//analyze($grouparray);
					$correction = $_POST['correction'];
					if($correction == ""){
						$correction = -1;
						$correctionval = -1;
					}else{
						
						if($ttestpvalue == 1){
							$pval = 0.10;
						}elseif($ttestpvalue == 2){
							$pval = 0.05;
						}elseif($ttestpvalue == 3){
							$pval = .025;
						}elseif($ttestpvalue == 4){
							$pval = .01;
						}elseif($ttestpvalue == 5){
							$pval = .005;
						}else{
							$pval = .001;
						}
						$numfeatures = count($cloneContainer);
						if($correction==1){
							//echo "bonferroni selected w/ p-value initially @ $pval<br>";
							
							//echo "numfeatures = $numfeatures<br>";
							$correctionval = fdrqvalue($pval, $numfeatures);
							//echo "bonferroni corrected p-value = $pval<br>";;
						}elseif($correction == 2){
							
							$correctionval = bonferroni($pval, $numfeatures);
						}
							

					}
					$ttestarray = ttest($grouparray,$ttestpvalue, $correction, $correctionval);
					//analyze($ttestarray);
					
					if($ttestarray[0] == 0){
						$skipgene = 1;
					}else{
						// push on to featurenum list of significant values
						$sigFeatureNames[$i] = $primaryname;
						$sigPvalues[$i] = round($ttestarray[1],8);
					}
					
					
				}
				if($anova == 1){
					// We need to see if this gene is differentially expressed.
					$index = 0;
					// How many groups are we dealing w/
					$grouparray = array();
					$groupnum = 0;
					foreach($logratioarray as $value) {
						#echo "key = $key : value = $value<br>";
						if($value != -9999){
							$grouparray[$groupnum][$index] = $value;
						}else{
							$groupnum++;
						}
						$index++;
					}
					//echo "$primaryname<br>";
					//analyze($grouparray);
					if($_POST['minuscontrol'] == 1){
						$controlmean = average($grouparray[0]);	
						//echo "control mean : $controlmean<br>";
						$numpops = count($grouparray);
						$arraycount = 0;
						//echo "<hr><hr>";
						//analyze($grouparray);
						
						for($k = 0; $k < $numpops; $k++){
							
							$numelements = count($grouparray[$k]);
							if($arraycount != 0){
						
								$arraycount++;  // we need to do this because of the problem w/ separators...
							}
							//echo "number of elements in group $i: $numelements<br>";
							for($j=0; $j < $numelements; $j++){
								
								$currentval = $grouparray[$k][$arraycount] - $controlmean;
								$grouparray[$k][$arraycount] = $currentval;
								$arraycount++;
							}
							//$popsmeanarray[$i] = $popelementsum/$popelementcount;
						}		
						//analyze($grouparray);
						//echo "<hr><hr>";			
						
					}
					$correction = $_POST['correction'];
					if(!isset($correction)){
						$correction = "";
					}
					if($correction == ""){
						$correction = -1;
						$correctionval = -1;
					}else{
						
						if($anovapvalue == 1){
							$pval = 0.10;
						}elseif($ttestpvalue == 2){
							$pval = 0.05;
						}else{
							$pval = .01;
						}
						$numfeatures = count($cloneContainer);
						if($correction==1){
							//echo "bonferroni selected w/ p-value initially @ $pval<br>";
							
							//echo "numfeatures = $numfeatures<br>";
							$correctionval = fdrqvalue($pval, $numfeatures);
							//echo "bonferroni corrected p-value = $pval<br>";;
						}elseif($correction == 2){
							
							$correctionval = bonferroni($pval, $numfeatures);
						}
							

					}

					$anovaarray = anova($grouparray,$anovapvalue,$correction, $correctionval);
					if($anovaarray[0] == 0){
						$skipgene = 1;
					}else{
						// push on to featurenum list of significant values
						$sigFeatureNames[$i] = $primaryname;
						$sigPvalues[$i] = round($anovaarray[1],8);
					}
					
				}
				if($skipgene == 1){
					// the gene is not significantly differentially expressed.
					continue;
				}else{
					$cloneCount++;
				}
				$subtractedvalsline = "";
				$subtractedcsvline = "";
				if($_POST['minuscontrol']==1  && ($anova == 1 || $ttest == 1)){
					
					$numpops = count($grouparray);
					$arraycount = 0;
					//echo "<hr><hr>";
					//analyze($grouparray);
					for($k = 0; $k < $numpops; $k++){
						
						$numelements = count($grouparray[$k]);
						if($arraycount != 0){
							
						$subtractedvalsline .="\t-9999";
						$subtractedcsvline .= ",,";
						//$csvline .= ",-9999";
							$arraycount++;  // we need to do this because of the problem w/ separators...
						}
						//echo "number of elements in group $i: $numelements<br>";
						for($j=0; $j < $numelements; $j++){
							//$currentval = $grouparray[$k][$arraycount] - $controlmean;
							//$grouparray[$k][$arraycount] = $currentval;
							$value = $grouparray[$k][$arraycount];							
							// need to convert the finalratio from log base 10 to fold change...
							$logfileline .= ",$value";
							if($value >=0){
								$value = pow(10,$value);
							}else{
								$intermediateval = pow(10,$value);
								$value = -1/$intermediateval;
							}
							if($userid == 1 && $debug == 1){
								echo "Final ratio for cloneid, $i, is $value<br>";
							}
							if($value == ""){
								echo "Final ratio for cloneid, $cloneid, is missing<br>";
							}


							$subtractedvalsline.= "\t$value";
							$subtractedcsvline .= ",$value";
							//$csvline.=",$value";
							$arraycount++;
						}
						
						//$popsmeanarray[$i] = $popelementsum/$popelementcount;
					}		
			
				}
				if($_POST['minuscontrol'] == 1  && ($anova == 1 || $ttest == 1)){
					$line .= $subtractedvalsline;
					$csvline .= $subtractedcsvline;
					
			
				}else{
					if($_POST['minuscontrol'] == 1){
						#echo "NO STATISTICAL TEST, BUT NEED TO SUBTRACT MEAN....<br>";
							
						$index = 0;
						// How many groups are we dealing w/
						$grouparray = array();
						$groupnum = 0;
						foreach($logratioarray as $value) {
							//echo "key = $key : value = $value<br>";
							if($value != -9999){
								$grouparray[$groupnum][$index] = $value;
							}else{
								$groupnum++;
							}
							$index++;
						}
						$controlmean = average($grouparray[0]);	
						#echo "controlmean = $controlmean<br>";
						$numpops = count($grouparray);
						$arraycount = 0;
						for($k = 0; $k < $numpops; $k++){
							
							$numelements = count($grouparray[$k]);
							if($arraycount != 0){
						
								$arraycount++;  // we need to do this because of the problem w/ separators...
							}
							//echo "number of elements in group $i: $numelements<br>";
							for($j=0; $j < $numelements; $j++){
								
								$currentval = $grouparray[$k][$arraycount] - $controlmean;
								$grouparray[$k][$arraycount] = $currentval;
								$arraycount++;
							}
						}
						$numpops = count($grouparray);
					$arraycount = 0;
					//echo "<hr><hr>";
					//analyze($grouparray);
					for($k = 0; $k < $numpops; $k++){
						
						$numelements = count($grouparray[$k]);
						if($arraycount != 0){
							
						$subtractedvalsline .="\t-9999";
						$subtractedcsvline .= ",,";
						//$csvline .= ",-9999";
							$arraycount++;  // we need to do this because of the problem w/ separators...
						}
						//echo "number of elements in group $i: $numelements<br>";
						for($j=0; $j < $numelements; $j++){
							//$currentval = $grouparray[$k][$arraycount] - $controlmean;
							//$grouparray[$k][$arraycount] = $currentval;
							$value = $grouparray[$k][$arraycount];							
							// need to convert the finalratio from log base 10 to fold change...
							$logfileline .= ",$value";
							if($value >=0){
								$value = pow(10,$value);
							}else{
								$intermediateval = pow(10,$value);
								$value = -1/$intermediateval;
							}
							if($userid == 1 && $debug == 1){
								echo "Final ratio for cloneid, $i, is $value<br>";
							}
							if($value == ""){
								echo "Final ratio for cloneid, $cloneid, is missing<br>";
							}


							$subtractedvalsline.= "\t$value";
							$subtractedcsvline .= ",$value";
							//$csvline.=",$value";
							$arraycount++;
						}
						

						
					}						
						$line .= $subtractedvalsline;
						$csvline .= $subtractedcsvline;
						
						
					}else{
						$line .= $ratiovalsline;
						$csvline .= $ratiovalscsvline;
					}
				}
				$logfileline .= "\n";
				$line .= "\n";
				$csvline .= "\n";
				$processedValsline .= "\n";
				$processedValsTableline .= "</tr>";
				//echo "$line <br>";
				fwrite($fd, $line);
				fflush($fd);
				fwrite($logfd, $logfileline);
				fflush($logfd);
				fwrite($csvfd, $csvline);
				#fwrite($logfd, $csvline);
				fflush($csvfd);
				fwrite($processedtablefd, $processedValsTableline);
				fflush($processedtablefd);
				fwrite($processedfd, $processedValsline);
				fflush($processedfd);

				
			}

			$processedValsTableline = "</table></body></html>";
			fwrite($processedtablefd, $processedValsTableline);
   			ftruncate($fd, ftell($fd));
			fclose($fd);
			ftruncate($csvfd, ftell($csvfd));
			fclose($csvfd);
			ftruncate($processedfd, ftell($processedfd));
			fclose($processedfd);
			ftruncate($logfd, ftell($logfd));
			fclose($logfd);
			ftruncate($processedtablefd, ftell($processedtablefd));
			fclose($processedtablefd);
			if(isset($_POST['number'])){
				$number = $_POST['number'];
			}else{
				$number = 0;
			}


			?>

<div dojoType='dijit.TitlePane' title='<font color="blue" style="font-weight: bold;">Ordered List Results/Input Parameters</font>' open='true' width="800">
<h3>Results:</h3>
<table class='question'><tr class="question">
			<td class="questionparameter"><strong>Number of Arrays Returned:</strong></td>
			<td class="questionanswer"> <?php echo $arrayidCount; ?></td>
			
			<td class="questionparameter"><strong>Number of genes:</strong></td>
			<td class="questionanswer"> <?php echo $cloneCount; ?></td>
			</tr>
		
</table>

<h3>Input Parameters:</h3>
<table class='question'><tr class="question">			
			<?php if($algo == 0){
			?>
			<td class="questionparameter"><strong>Number of clusters:</strong></td>
			<td class="questionanswer"> <?php echo $number; ?></td>
			<?php
			}
			else{
			?>
			<td class="questionparameter"><strong>Treatments Clustered:</strong></td>
			<td class="questionanswer">
			<?php
				if(isset($trxCluster)){
					if($trxCluster != 0){
						echo "Yes";
					}
					else{
						echo "No";
					}
				}else{
					echo "No";
				}
			?>
			</td><td class="questionparameter"><strong>Data Set:</strong>
			</td><td class="questionanswer"><strong>
			<?php if($dataset == 1){
				echo "Uncondensed";
				}
				else{
				echo "Condensed";
				}
			?></strong></td>
			<?php
			} ?></tr>
			
			<tr>
			<?php
				if(($anova == 1 || $ttest == 1 || $minuscontrol == 1)){
						echo "<td class=\"questionparameter\"><strong>Control Mean Subtracted</strong></td>";
						if($minuscontrol == 1){
							echo "<td class=\"questionanswer\"><strong>Yes</strong></td>";
						}else{
							echo "<td class=\"questionanswer\"><strong>No</strong></td>";
						}
						
				} 
				if($ttest == 1){
					echo "<td class=\"questionparameter\"><strong>t-Test pValue:</strong></td>";
					echo "<td class=\"questionanswer\"><strong>$pval</strong></td>";
				}elseif($anova == 1){
					echo "<td class=\"questionparameter\"><strong>ANOVA pValue:</strong></td>";
					echo "<td class=\"questionanswer\"><strong>$pval</strong></td>";


				}

	$cpsvgfile = "$IMAGESreldir/imagesvg$filenum";
			?>
			</tr>
			




</table>
</div>

<div dojoType='dijit.TitlePane' title='<font color="blue" style="font-weight: bold;">Associated Data and Image Files</font>' open='false' width="800">
<h3>Data Files:</h3>
<table class="question">
	<tr class="question"><td class="questionparameter"><strong>Fold-Change Table (HTML):</strong></td><td class="questionanswer"><?php echo "<a href=\"./edge3tabledisplay.php?tableNum=$filenum\" target=\"_blank\">Fold-Change table</a>"; ?></td><td class="questionparameter"><strong>Fold-Change CSV File: </strong></td>
			<td class="questionanswer"><?php echo "<a href=\"..$IMAGESreldir/$filenum.csv\" target=\"_blank\">Fold Change CSV</a>"; ?></td></tr>
	<tr><td class="questionparameter"><strong>Processed Values Table (HTML):</strong></td>
			<td class="questionanswer">
			<?php
				$proctable = ".$IMAGESreldir/".$filenum."processedValsTable.html";
				echo "<a href=\"$proctable\" target=\"_blank\">Processed table</a></td>";
			?>
			</td><td class="questionparameter"><strong>Processed CSV File</strong></td>
			<td class="questionanswer">
			<?php
				$proccsv = ".$IMAGESreldir/".$filenum."processedVals.csv";
				echo "<a href=\"$proccsv\" target=\"_blank\"> Processed CSV</a></td>";
			?>
			</td></tr>
			<tr><td class="questionparameter"><strong>Base-10 Log Ratio Values:</strong></td>
			<td class="questionanswer">
			<?php 
				$logvals = ".$IMAGESreldir/datalogvalues$filenum.txt";
				echo "<a href=\"$logvals\" target=\"_blank\">Log Ratios</a></td><td></td></tr>";
				if($anova == 1){
					echo "<tr><td class=\"questionparameter\"><strong>ANOVA Significant p-Values (HTML)</strong></td>";
					if(count($sigPvalues) >0 ){
						echo "<td class=\"questionanswer\"><a href='..$IMAGESreldir/pvaluetable$filenum.html' target='_blank'>Significant pValues table (HTML)</a></td><td class=\"questionparameter\"><strong>ANOVA Significant p-Values (CSV)</strong></td><td class=\"questionanswer\"><a href='..$IMAGESreldir/pvaluetable$filenum.csv' target='_blank'>ANOVA Significant pValues CSV</a></td></tr>";
					}
				}elseif($ttest == 1){
					echo "<tr><td class=\"questionparameter\"><strong>t-Test Significant p-Values (HTML)</strong></td>";
					if(count($sigPvalues) >0 ){
						echo "<td class=\"questionanswer\"><a href='..$IMAGESreldir/pvaluetable$filenum.html' target='_blank'>Significant pValues table</a></td><td class=\"questionparameter\"><strong>t-Test Significant p-Values (CSV)</strong></td><td class=\"questionanswer\"><a href='..$IMAGESreldir/pvaluetable$filenum.csv' target='_blank'>t-Test Significant pValues CSV</a></td></tr>";
					}
					
				}
				
				$cpsvgfile = ".$IMAGESreldir/imagesvg$filenum"; 
			?>


</table>
<h3>Image Files:</h3>
<table class="question">
	<tr class="question">
<td class="questionparameter">
			<a href="<?php echo ".$IMAGESreldir/svg$filenum.svg"; ?>" onClick="return popup(this,'SVG')">View SVG Heat Map</a>
			</td></tr><tr><td class="questionparameter"><?php echo "<a href=\"".$cpsvgfile.".png\" target=\"_blank\">View PNG Heat Map</a>";?></td></tr><tr><td class="questionparameter"><?php echo "<a href=\"".$cpsvgfile.".jpg\" target=\"_blank\">View JPG Heat Map</a>";?></td></tr>

</table>


</div>
<div dojoType='dijit.TitlePane' title='<font color="blue" style="font-weight: bold;">Save/Update Query and Save Gene List</font>' open='false' width="800">
<?php
	if($savedquery != ""){
		$sql = "SELECT queryname FROM savedqueries WHERE query = $savedquery";
		$querynameresult = $db->Execute($sql);//mysql_query($sql,$db);
		$querynamerow = $querynameresult->FetchRow();//mysql_fetch_row($querynameresult);
		$queryname = $querynamerow[0];
		echo "<strong>Query Name: $queryname</strong><br>";
	}

	

?>

<table>
<tr>
<?php

			if($savedquery != ""){
				// Does this query have a name???
				$sql = "SELECT queryname FROM savedqueries WHERE query = $savedquery";
				if($userid == 1 ){
					//echo $sql;
				}
				$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);
				$row = $sqlResult->FetchRow();//mysql_fetch_row($sqlResult);
				$name = $row[0];
				if($userid == 1 ){
					//echo "<br>name=$name<br>";
				}

				if($name == "" || $name == "NULL"){
					$update = "false";
				}else{
					$update = "true";
				}
				if($update == "true"){
			?>
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery&savedquery=$savedquery";?>"
				target="<?php echo "_blank$randnum"; ?>">Update?</a></td>
			<?php
				}else{
			?>
					<td class="questionanswer">
				<a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a><td class="questionanswer">

				</td>
			<?php
				}
			}else{
				if(!isset($update)){
					$update = false;
				}
   				if($update == "true"){
			?>





				<a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a>
				</td>
			<?php
				}
				else{
			?>
				<td class="questionanswer">
					<a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a>
				</td>
			<?php
				}
			}
			if($savedquery != "" && $update == "true"){

			/*$tempsql = "SELECT MAX(query) FROM savedqueries";
			$tempresult = mysql_query($tempsql, $db);
			$temprow = mysql_fetch_row($tempresult);
			$tempquery = $temprow[0];
			*/
			?>
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save as new query?</a></td>
			<?php
			}
			?>
			</tr>
			<tr><td class="questionanswer" colspan='2'><a href='<?php echo "./phpinc/importgenelistresult.inc.php?type=$thisorganism&featurefilenumber=$filenum&querytype=0&contrastnumber=-1&name=Saved Gene List"; ?>' target='_blank'>Save Genes Returned as Edge<sup>3</sup> Gene list?</a></td></tr>
			</table>
</div>

		<?php
			$numDiscarded = 0;
			foreach($discardedFeatures as $discarded){
				if($numDiscarded == 0){
					echo "Features not making signaling cutoff threshholds: $discarded";
				}else{
				echo ", $discarded ";
				}
				$numDiscarded++;
			}
			if($numDiscarded > 0){
				echo "<br>";
			}

			$algo = -1;   // 0 = kmeans ; 1 = hierarchical -1 = custom ordered genes
			$numberofclusters = 4;  // for k-means only
			//$colorscheme = 0; 0 = red/green ; 1 = blue/yellow
			//$browserval = 1;
			
			if(($ttest == 1 || $anova == 1) &&  $cloneCount == 0){
				echo "No genes met your criteria.<br>";

			}else{
			$randnum = rand(0, 25000);
			$browserval = 1;
			$command = "java -mx512m -jar EdgeClustering.jar \"$file\" $numberofclusters $arrayidCount \"$svgFile\" $algo \"$tableFile\" $colorscheme 0 $browserval $filenum \"$IMAGESreldir\" >> garbagedump.txt";
			#$command = "java -mx512m -jar EdgeClustering.jar \"$file\" $clusterNumber $arrayidCount \"$svgFile\" $algo \"$tableFile\" $colorscheme $trxCluster $browserval $filenum >> $filenum"."garbagedump.txt";
			if($userid == 1 && $debug==1){
				echo "<br>$command<br>";
			}
			#echo "<br>$command<br>";
				$str=passthru($command);
				/*
				$command = "cp .$IMAGESreldir/svg$filenum.svg $IMAGESdir/imagesvg$filenum.svg";
				if($userid == 1 && $debug == 1){
					echo "userid-based debug<br>";
					echo "$command <br>";
				}
				$str = exec($command);
				echo "<hr>$str</hr>";
				*/
				if(copy("$IMAGESdir/svg$filenum.svg","$IMAGESdir/imagesvg$filenum.svg")){
					$cpsvgfile = ".$IMAGESreldir/imagesvg$filenum.svg";
				}else{
					
					die("Error copying file!");
				}
				$filesize = filesize($cpsvgfile);

				if($filesize > 3169300 && $outputformat == 0){
				echo "<br>LARGE SVG FILE: Displaying the PNG file.";
					$outputformat = 1;
				}
				//$command = "gzip --best .$IMAGESreldir/svg$filenum.svg";
				//echo $command;
				//$str=exec($command);

				//$command = "mv .$IMAGESreldir/svg$filenum.svg.gz .$IMAGESreldir/svg$filenum.svgz";
				//$str=exec($command);
				$imagesizeexceeded = -1;
				if($cloneCount > 2400){
					//echo "imagesizeexceeded = $imagesizeexceeded<br>";
					$imagesizeexceeded = 1;
				}
				//echo "imagesizeexceeded = $imagesizeexceeded<br>";
				createImage("svg$filenum.svg", $number,$imagesizeexceeded,$IMAGESreldir);
				//createImage("svg$filenum.svg", $number,0);
			if($outputformat == "0"){
				?>
<embed src="<?php echo ".$IMAGESreldir/svg$filenum.svg" ?>" width=<?php echo $w ?> height=<?php echo $h ?> name="heatmap" type="image/svg+xml" />
				<?php
				}
				elseif($outputformat == "2"){
				?><p>
				<img src="<?php echo ".$IMAGESreldir/imagesvg$filenum.jpg" ?>" alt="heatmap" align="bottom" usemap="#map1" border=0></img>
				</p>
				<?php
					if($includeimagemap != 0 || $includeimagemap == ""){
					include ".$IMAGESreldir/imagemapsvg$filenum";
					}
				}
				else{
				?>
				<p>
				<img src="<?php echo ".$IMAGESreldir/imagesvg$filenum.png" ?>" alt="heatmap" align="bottom" usemap="#map1" border=0></img>
				</p>
				<?php
					if($includeimagemap != 0 || $includeimagemap == ""){
						include ".$IMAGESreldir/imagemapsvg$filenum";
					}
				}
			}
				echo $str;
				$end = utime(); $run = $end - $start;

				echo "<br><font size=\"1px\"><b>Query results returned in ";
				echo substr($run, 0, 5);
				echo " secs.</b></font>";
					//unlink($file);
				}
				elseif($cloneCount == 1){
					echo "<br>There was only one gene that met your criteria:
					$cloneContainer[0] <br>";
				}
				else{
					echo "<br>There were no genes that met your criteria.<br>";
					$end = utime(); $run = $end - $start;
				echo "<font size=\"1px\"><b>Query results returned in ";
				echo substr($run, 0, 5);
				echo " secs.</b></font>";
				}


?>
