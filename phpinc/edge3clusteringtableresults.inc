<?php
$debug = -1;
$anova = "";
$ttest = "";
$ttestpvalue ="";
$anovapvalue = "";
$correction = "";
$mincontrol = "";
$randnum = rand(0, 25000);
if(isset($_POST['anova'])){
	$anova = $_POST['anova'];
}
if(isset($_POST['ttest'])){
	$ttest = $_POST['ttest'];
}
if(isset($_POST['ttestpvalue'])){
	$ttestpvalue = $_POST['ttestpvalue'];
	//echo "ttestpvalue = $ttestpvalue<br>";
}
if(isset($_POST['anovapvalue'])){
	$anovapvalue = $_POST['anovapvalue'];
}
if(isset($_POST['correction'])){
	$correction = $_POST['correction'];
}
if(isset($_POST['minuscontrol'])){
	$mincontrol = $_POST['minuscontrol'];
}
if(isset($_POST['clustoption'])){
	$clustoption = $_POST['clustoption']; # Used for determining whether or not to use R-based code or built-in algorithm....
	#echo "clustering option $clustoption selected<br>";
}

if($dataset == 0){
	//die("condensed set chosen....<br>");
	
}
if($userid == 1){
	//analyze($_POST);
}
$filter = 1; # this variable is set to make it such that the fold-change filter is utilized.
//analyze($_POST);
if($cloneCount > 0){
			// Get rid of the last OR....
			array_pop($cloneidarray);
			$cloneidsqlstr = "";
			// Create the cloneid sql string
			$cloneidsqlstr = "";
			foreach($cloneidarray as $item){
				$cloneidsqlstr .= $item;
			}
			$hybArray = array(); // Stores an array of LogRatio converted to fold-change values....

			
			
			$totalArray = array();
			if($anova == 1 || $ttest == 1 || $mincontrol == 1){
				$logratioarray = array(); // stores an array of LogRatio values....
				$totalLogRatioMatrix = array();  // this is used for anova purposes...
			}
			// the following four arrays are used for the purpose of creating the cy3/cy5 values table....
			$cy3Array = array();
			$cy5Array = array();
			$cy3TotalArray = array();
			$cy5TotalArray = array();
			// GET ALL THE HYBRIDIZATION INFO FOR THESE ARRAYS.....
			// GET THESE DATA ONE AT A TIME AND ORDER BY CLONE NUMBER......
			$arraycounter = 0;  // used to count actual arrays (i.e., not -99 vals)
			$groupcounter = 0;
			$separatorarray = array();
			foreach($arrayidarray as $arrayid){
				

				if($arrayid != -99){
					$arraycounter++;
					if($dataset == 1){
						$hybSQL = "SELECT arrayid, FeatureNum, LogRatio, PValueLogRatio, gIsFeatNonUnifOL, rIsFeatNonUnifOL, gProcessedSignal, rProcessedSignal FROM $arraydatatable where arrayid = $arrayid and ( $cloneidsqlstr ) ORDER BY FeatureNum";
					}else{ //using condensed data
						$hybSQL = "SELECT arrayid, FeatureNum, LogRatio FROM agilentcondenseddata where arrayid = $arrayid and ( $cloneidsqlstr ) ORDER BY FeatureNum";
					}
					if($userid==1 && $debug == 1){
						echo "$hybSQL <br>";
						#die('quitting....');
					}
					
					$hybResult = $db->Execute($hybSQL);//mysql_query($hybSQL, $db);
					//while($row = mysql_fetch_row($hybResult)){
					while($row = $hybResult->FetchRow()){
						$cloneid = $row[1];  // agilent featurenum...
						
						$finalratio = strval($row[2]); // agilent logratio
						$thisFeaturePValue= $row[3];
						$thisFeaturegIsFeatNonUnifOLValue = $row[4];
						$thisFeaturerIsFeatNonUnifOLValue = $row[5];
						$thisFeaturegProcessedSignal = $row[6];
						$thisFeaturerProcessedSignal = $row[7];
						//echo "$row[4]<br>";
						
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
						/*if($arraytype != 2){
							if($thisFeaturegIsFeatNonUnifOLValue == 1 || $thisFeaturerIsFeatNonUnifOLValue == 1){
								//$finalratio = -99999999;
							}
						}*/
						$hybArray[$cloneid] = $finalratio;
						if($filter == 1){
							if($anova == 1 || $ttest == 1 || $mincontrol == 1){
								$logratioarray[$cloneid] =$row[2];  // use the log ratio values instead of fold change....
							}
							$cy3Array[$cloneid] = $thisFeaturegProcessedSignal;
							$cy5Array[$cloneid] = $thisFeaturerProcessedSignal;
						}
					}
				
				}
				else{
					// how many clones? Go through cloneContainer and fill this hybArray...
					// just fill $hybArray w/ -9999
					$groupcounter++;
					foreach($cloneContainer as $aclone){
						$hybArray[$aclone] = -9999;
						if($anova == 1 || $ttest == 1 || $mincontrol == 1){
							$logratioarray[$aclone] = -9999;
						}
					}
					array_push($separatorarray,$arraycounter);

				}
				if($filter == 1){	
					if($anova==1 || $ttest == 1 || $mincontrol == 1){
						array_push($totalLogRatioMatrix, $logratioarray);
					}
					
					//analyze($cy3Array);
					array_push($cy3TotalArray, $cy3Array);
					array_push($cy5TotalArray, $cy5Array);
					//analyze($hybArray);
					
					reset($cy3Array);
					reset($cy5Array);
					$cy3Array = array();
					$cy5Array = array();
				}
				array_push($totalArray, $hybArray);
				reset($hybArray);
				$hybArray = array();
				
				
			}
			$arrayidCount = count($arrayidarray);
			
			#echo "ARRAY ID COUNT: $arrayidCount<br>";
			$newArray = array();
			//$logarray = array();
			if($filter == 1){
				if($anova == 1 || $ttest == 1 || $mincontrol == 1){
					$invertedLogRatioMatrix = array();
				}
				// put in to create a table for displaying the cy3/cy5 values for a particular clone...
				$cy3vals = array();
				$cy5vals = array();
			}
			$numclones = count($cloneContainer);
			#echo "The number of clones: $numclones<br>";
			#die("exiting...");
			//for($i = 1; $i <= $cloneCount; $i++){
			foreach($cloneContainer as $i){
				for($j = 0; $j < $arrayidCount; $j++){
					//echo "totalArray $i,$j = $totalArray[$j][$i]<br>";
					if(isset($totalArray[$j][$i])){
						$newArray[$i][$j] = $totalArray[$j][$i];
						#$logarray[$i][$j] = $totalArray[$j][$i];
					}
				}

			}
			reset($cloneContainer);
			foreach($cloneContainer as $i){

				for($j = 0; $j < $arrayidCount; $j++){
					//echo "totalArray $i,$j = $totalArray[$j][$i]<br>";
					if(isset($totalLogRatioMatrix[$j][$i])){
						$invertedLogRatioMatrix[$i][$j] = $totalLogRatioMatrix[$j][$i];
					}
				}

			}
// 			reset($cloneContainer);die(
			foreach($cloneContainer as $i){
				for($j = 0; $j < $arrayidCount; $j++){
					//echo "cy3Array $i,$j = $cy3TotalArray[$j][$i]<br>";
					if(isset($cy3TotalArray[$j][$i])){
						$cy3vals[$i][$j] = $cy3TotalArray[$j][$i];
					}
				}

			}
			reset($cloneContainer);
			foreach($cloneContainer as $i){
				for($j = 0; $j < $arrayidCount; $j++){
					//echo "totalArray $i,$j = $totalArray[$j][$i]<br>";
					if(isset($cy5TotalArray[$j][$i])){
						$cy5vals[$i][$j] = $cy5TotalArray[$j][$i];
					}
				}

			}
			$index = 0;
			
			// a foreach loop to write the arraydesc to processVals files....
			$processedValsline = ",,,";
			$processedValsTableline = "<html><body><table><tr><th bgcolor='green'>cy3</th><th bgcolor='red'>cy5</th><th></th>";
			$processedValscy3cy5line = ",,,";
			$logfileheader="";
			foreach($arrayidarray as $anID){
				if($anID > 0){
					$idSQL = "SELECT arraydesc FROM agilent_arrayinfo WHERE arrayid = $anID";
					$result = $db->Execute($idSQL);//mysql_query($idSQL, $db);
					$iddesc = $result->FetchRow();//mysql_fetch_row($result);
					$processedValsline .= "$iddesc[0],,";
					$processedValscy3cy5line .= "cy3,cy5,";
					$processedValsTableline .= "<th colspan='2'>$iddesc[0]</th>";
					$logfileheader.="\t\"$iddesc[0]\"";
				}
			}
			$logfileheader.="\n";
			$processedValsTableline .= "</tr>";
			$processedValsline .= "\n";
			$processedValscy3cy5line .= "\n";
			fwrite($processedfd, $processedValsline);
			fflush($processedfd);
			fwrite($processedfd, $processedValscy3cy5line);
			fflush($processedfd);
			fwrite($processedtablefd, $processedValsTableline);
			fflush($processedtablefd);
			$cloneCount = 0;
			$sigFeatureNames = array();
			$sigCloneNumbers = array();
			$sigPvalues = array();
			$allPvalues = array();
			$groupfilefd = array();
			//echo "before filecreation: groupcounter = $groupcounter<br>";
			$sigFeatureGroupArrays = array();
			for($groupfdcount = 0; $groupfdcount <= $groupcounter; $groupfdcount++){
					$sigFeatureGroupArrays[$groupfdcount] = array();
					$groupexpfile = "$IMAGESdir/$filenum"."groupexp".$groupfdcount.".csv";
					$command = "touch $groupexpfile";
					//echo "$command<br>";
					$str=exec($command);
					$groupfilefd[$groupfdcount] = fopen($groupexpfile, 'w');	
			}
				
			//analyze($groupfilefd);
			fwrite($logfd,$logfileheader);
			$numclones = count($cloneContainer);
			
			#die("# of clones = $numclones  exiting...");
			
			foreach($cloneContainer as $i){
				//echo "Clone = $i<br>";
				
				if($dataset != 1){
					$i = $i * -1;
				}
				$primaryname = $geneNameContainer[$index];
				
				$refseq = $systematicNameContainer[$index];
				// Vestigial GO stuff from edge clustering algo....
				$goIDCounter = 0;
				$goidarray = array();
				
				//echo "$primaryname<br>";
				$line = "$i\t $primaryname \t$refseq \t$goIDCounter";
				$logline = $i."_".$primaryname;
				$csvline = "$i,$primaryname,$refseq,$goIDCounter";
				$processedValsline = "$i,$primaryname,$refseq";
				$processedValsTableline = "<tr><td>$i</td><td>$primaryname</td><td>$refseq</td>";
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
				if(isset($cvsgostr)){
					$csvline .= $csvgostr;
				}
				// if we are doing the anova gene selection process....
				
				if($ttest == 1){
					// We need to see if this gene is differentially expressed.

					// How many groups are we dealing w/
					$grouparray = array();
					$groupnum = 0;
					
					
					foreach($invertedLogRatioMatrix[$i] as $key=>$value) {
						//echo "key = $key : value = $value<br>";
						
						if($value != -9999){
							$grouparray[$groupnum][$key] = $value;
							
							
						}else{
							$groupnum++;
							
						}
					}
					
					//die("exiting...");
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
					
					//echo "$primaryname<br>";
					//analyze($grouparray);
					
					//echo "Correction = $correction<BR>";
					if(!isset($correction)  || $correction == -1){
						$correction = -1;
						$correctionval = -1;
					}else{
						
						
						$numfeatures = count($cloneContainer);
						if($correction==1){
							//echo "bonferroni selected w/ p-value initially @ $pval<br>";
							
							#echo "numfeatures = $numfeatures<br>";
							$correctionval = fdrqvalue($pval, $numfeatures);
							#echo "the fdrqvalue is $correctionval<br>";
							//echo "bonferroni corrected p-value = $pval<br>";;
						}elseif($correction == 2){
							
							$correctionval = bonferroni($pval, $numfeatures);
						}
							

					}
					if(!isset($correctionval)){
						$correctionval = fdrqvalue($pval,$numfeatures);
					}
					$ttestarray = ttest($grouparray,$ttestpvalue, $correction, $correctionval);
					//analyze($ttestarray);
					
					if($ttestarray[0] == 0){
						// if not significant, then continue....i.e., check next cloneid....
						$allPvalues[$i] = round($ttestarray[1],8);
						$index++;
						continue;
					}else{
						// push on to featurenum list of significant values
						$sigFeatureNames[$i] = $primaryname;
						$sigPvalues[$i] = round($ttestarray[1],8);
						$allPvalues[$i] = round($ttestarray[1],8);
						array_push($sigCloneNumbers,$i);
						$numpops = count($grouparray);
						$arraycount = 0;
						for($idx = 0; $idx < $numpops; $idx++){
							$cnum = "$i";
							fwrite($groupfilefd[$idx],$cnum);
							array_push($sigFeatureGroupArrays[$idx], $grouparray[$idx]);
							$numelements = count($grouparray[$idx]);
							if($arraycount != 0){
						
								$arraycount++;  // we need to do this because of the problem w/ separators...
							}
							//echo "number of elements in group $i: $numelements<br>";
							for($j=0; $j < $numelements; $j++){
								
								$item = ",".$grouparray[$idx][$arraycount];
								fwrite($groupfilefd[$idx], $item);
								
								$arraycount++;
							}
							//for($g = 0; $g < $groupfdcount; $g++){
								fwrite($groupfilefd[$idx], "\n");
								fflush($groupfilefd[$idx]);
							//}
							
						}
						
			
						
					}
					
					
				}

				
				if($anova == 1){
					// We need to see if this gene is differentially expressed.
					
					// How many groups are we dealing w/
					$grouparray = array();
					$groupnum = 0;
					foreach($invertedLogRatioMatrix[$i] as $key=>$value) {
						//echo "key = $key : value = $value<br>";
						if($value != -9999){
							//if($i == 0){
								//echo "$value <br>";
							//}
							$grouparray[$groupnum][$key] = $value;
						}else{
							//echo "<hr>";
							$groupnum++;
						}
					}
					//echo "$primaryname<br>";
					//analyze($grouparray);
					
					// Get the mean of the 1st group (i.e., control group)
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
					
						if($anovapvalue == 1){
							$pval = 0.10;
						}elseif($anovapvalue == 2){
							$pval = 0.05;
						}else{
							$pval = .01;
						}
					if($correction == ""){
						$correction = -1;
						$correctionval = -1;
					}else{
						
					
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
					if(!isset($correctionval)){
						$correctionval = fdrqvalue($pval,$numfeatures);
					}
					$anovaarray = anova($grouparray,$anovapvalue,$correction, $correctionval);
					//echo "anova($grouparray,$anovapvalue,$correction, $correctionval)<BR>";
					//echo "after = $anovaarray[1]<br>";
					
					if($anovaarray[0] == 0){
						// if not significant, then continue....i.e., check next cloneid....
						$allPvalues[$i] = round($anovaarray[1],8);
						$index++;
						continue;
					}else{
						// push on to featurenum list of significant values
						$sigFeatureNames[$i] = $primaryname;
						$sigPvalues[$i] = round($anovaarray[1],8);
						$allPvalues[$i] = round($anovaarray[1],8);
						array_push($sigCloneNumbers,$i);
						$numpops = count($grouparray);
						$arraycount = 0;
						for($idx = 0; $idx < $numpops; $idx++){
							$cnum = "$i";
							fwrite($groupfilefd[$idx],$cnum);
							$numelements = count($grouparray[$idx]);
							if($arraycount != 0){
						
								$arraycount++;  // we need to do this because of the problem w/ separators...
							}
							//echo "number of elements in group $i: $numelements<br>";
							for($j=0; $j < $numelements; $j++){
								
								$item = ",".$grouparray[$idx][$arraycount];
								fwrite($groupfilefd[$idx], $item);
								
								$arraycount++;
							}
							//for($g = 0; $g < $groupfdcount; $g++){
								fwrite($groupfilefd[$idx], "\n");
								fflush($groupfilefd[$idx]);
							//}
							
						}
					}
					
				}
				// this assignment/conditional check is used to determine whether or not to do a mean of the control subtraction....
				$nocorrection = -1;
				if($anova == 1 || $ttest == 1){
					$nocorrection = 1;

				}
				/*if($userid == 1){
						if($_POST['minuscontrol'] == 1){
							echo "minuscontrol == 1<br>";
						}
					}
				*/
				if(!isset($_POST['minuscontrol'])){
					$minuscontrolisset = -1;
				}else{
					$minuscontrolisset = $_POST['minuscontrol'];
					//$minuscontrolisset = 1;
				}
				if($minuscontrolisset==1 && $nocorrection == -1){
					//  modified this to subtract control when not using anova or ttest....but we're grouping.
					
					//echo "<hr><hr>";
					//analyze($grouparray);
					if($userid == 1){

					//echo "in minuscontrol = 1 and nocorrection = -1<br>";
					//analyze($invertedLogRatioMatrix[$i]);
					}
					// How many groups are we dealing w/
					$grouparray = array();
					$groupnum = 0;
					//if(isset($invertedLogRatioMatrix[$i])){
					foreach($invertedLogRatioMatrix[$i] as $key=>$value) {
						//echo "key = $key : value = $value<br>";
						if($value != -9999){
							if($i == 0){
								if($userid == 1){
									echo "$value <br>";
								}
							}
							$grouparray[$groupnum][$key] = $value;
						}else{
							if($userid == 1){
							//	echo "<hr>";
							}
							$groupnum++;
						}
					}
				//	}
					$numpops = count($grouparray);
					if($userid == 1){
	
						//echo "numpops = $numpops<br>";
					}
					$arraycount = 0;
					for($k = 0; $k < $numpops; $k++){
						
						$numelements = count($grouparray[$k]);
						if($_POST['minuscontrol'] == 1){
							$controlmean = average($grouparray[0]);	
						}
						if($arraycount != 0){
							
						$line .="\t-9999";
						$csvline .= ",-9999";
							$arraycount++;  // we need to do this because of the problem w/ separators...
						}
						//echo "number of elements in group $i: $numelements<br>";
						for($j=0; $j < $numelements; $j++){
							//$currentval = $grouparray[$k][$arraycount] - $controlmean;
							//$grouparray[$k][$arraycount] = $currentval;
							$value = $grouparray[$k][$arraycount];							
							// need to convert the finalratio from log base 10 to fold change...
							if($_POST['minuscontrol'] == 1){
								$value -= $controlmean;	
							}
							$logvalue=$value;
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
							

							$logline.="\t$logvalue";
							//echo "WTF????<BR>";
							$line.= "\t$value";
							$csvline.=",$value";
							$arraycount++;
						}
						
						//$popsmeanarray[$i] = $popelementsum/$popelementcount;
					}
					/*
					foreach($newArray[$i] as $key=>$value) {
						
						$line.= "\t$value";
						$csvline.=",$value";
					}
					*/
				}elseif($mincontrol !=1 && $nocorrection == 1){
					//echo "$mincontrol !=1 && $nocorrection == 1){<br>";
					foreach($newArray[$i] as $key=>$value) {
						if($value !=-9999){
							if($value >=0){
									$logratio = log10($value);
							}else{
								$logratiointermediate = -1/$value;
								$logratio = log10($logratiointermediate);
								
							}
							$logline.="\t$logratio";
						}
						
						$line.= "\t$value";
						$csvline.=",$value";
					}
					/*foreach($logarray[$i] as $key=>$value) {
						
						if($value != -9999){
							$logline.= "\t$value";
						}
						
					}*/
				}elseif($mincontrol !=1 && $nocorrection == -1){
					//echo "elseif($mincontrol !=1 && $nocorrection == -1){<br>";
					foreach($newArray[$i] as $key=>$value) {
						if($value !=-9999){
							if($value >=0){
									$logratio = log10($value);
							}else{
								$logratiointermediate = -1/$value;
								$logratio = log10($logratiointermediate);
								
							}
							$logline.="\t$logratio";
						}
						
						$line.= "\t$value";
						$csvline.=",$value";
					}
					/*foreach($logarray[$i] as $key=>$value) {
						
						if($value != -9999){
							$logline.= "\t$value";
						}
						
					}*/
				}else{
					
					$numpops = count($grouparray);
					$arraycount = 0;
					//echo "<hr><hr>";
					//analyze($grouparray);
					for($k = 0; $k < $numpops; $k++){
						
						$numelements = count($grouparray[$k]);
						if($_POST['minuscontrol'] == 1){
							$controlmean = average($grouparray[0]);	
						}
						if($arraycount != 0){
							
						$line .="\t-9999";
						$csvline .= ",";
							$arraycount++;  // we need to do this because of the problem w/ separators...
						}
						//echo "number of elements in group $i: $numelements<br>";
						for($j=0; $j < $numelements; $j++){
							//$currentval = $grouparray[$k][$arraycount] - $controlmean;
							//$grouparray[$k][$arraycount] = $currentval;
							$value = $grouparray[$k][$arraycount];							
							// need to convert the finalratio from log base 10 to fold change...
							if($_POST['minuscontrol'] == 1){
								$value -= $controlmean;	
							}
							$logvalue=$value;
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
								
							$logline.="\t$logvalue";
							
							$line.= "\t$value";
							$csvline.=",$value";
							$arraycount++;
						}
						
						//$popsmeanarray[$i] = $popelementsum/$popelementcount;
					}		
			
				}
				$cy3list = array();
				$cy5list = array();
				foreach($cy3vals[$i] as $key=>$value){
					//echo "$value<hr>";
					if($value != ""){
						array_push($cy3list, $value);
					}
				}
				foreach($cy5vals[$i] as $key=>$value){
					if($value != ""){
						array_push($cy5list, $value);
					}
				}
				for($z = 0; $z < $arrayidCount; $z++){
					if(isset($cy3list[$z])){
					$processedValsline .= ",$cy3list[$z],$cy5list[$z]";
					$processedValsTableline .= "<td bgcolor='green'>$cy3list[$z]</td><td bgcolor='red'>$cy5list[$z]</td>";
					}
				}
				$logline.="\n";
				$line .= "\n";
				$csvline .= "\n";
				$processedValsline .= "\n";
				$processedValsTableline .= "</tr>";
				//echo "$line <br>";
				
				fwrite($fd, $line);
				fwrite($logfd,$logline);
				fwrite($csvfd, $csvline);
				fwrite($processedfd, $processedValsline);
				fwrite($processedtablefd, $processedValsTableline);
				fflush($fd);
				fflush($logfd);
				fflush($csvfd);
				fflush($processedfd);
				fflush($processedtablefd);
				$cloneCount++;
				$index++;
			}
			
			$processedValsTableline = "</table></body></html>";
			fwrite($processedtablefd, $processedValsTableline);
			fflush($fd);
			ftruncate($fd, ftell($fd));
			fclose($fd);
			fflush($logfd);
			ftruncate($logfd, ftell($logfd));
			fclose($logfd);
			ftruncate($csvfd, ftell($csvfd));
			fclose($csvfd);
			ftruncate($processedfd, ftell($processedfd));
			fclose($processedfd);
			ftruncate($processedtablefd, ftell($processedtablefd));
			fclose($processedtablefd);

			$groupfdcount = count($groupfilefd);
			for($g = 0; $g < $groupfdcount; $g++){
						fclose($groupfilefd[$g]);
			}

			
			//analyze($sigFeatureGroupArrays);
/*
			echo "stats test....<br>";
			analyze($sigFeatureGroupArrays[0][0]);
			$cov = covariance($sigFeatureGroupArrays[0][0],$sigFeatureGroupArrays[0][0]);
			echo "covariance  = $cov<br>";
			$cor = pearsoncorrelation($sigFeatureGroupArrays[0][0],$sigFeatureGroupArrays[0][0]);
			echo "correlation = $cor<br>";
*/
			$cormatrix = array();
			for($h = 1; $h < count($sigFeatureGroupArrays); $h++){
				for($y = 0; $y < count($sigFeatureGroupArrays[$h]); $y++){
					for($z = 0; $z < count($sigFeatureGroupArrays[$h]); $z++){
						$cormatrix[$y][$z] = abs(pearsoncorrelation($sigFeatureGroupArrays[$h][$y],$sigFeatureGroupArrays[$h][$z]));
					}
				}
			}

			$corfilebase = "$IMAGESdir/$filenum";



			if($clustoption==1 ||$clustoption==0){
				//echo "clustering option 1 or 0 selected<br>";
				createclusteringheatmap($corfilebase, $logratiovalues,$cloneCount,$arraycounter,$separatorarray);
			//echo "$corefilebase<br>";
				//createcorrelationheatmap($cormatrix, $corfilebase,$sigFeatureNames);
			}
			//analyze($cormatrix);
			/*
			analyze($sigPvalues);
			
			echo "<p>";
			foreach($sigPvalues as $key=>$value){
				echo "$key => $value<br>";
			}
			echo "</p>";
			*/
			// Sort the pValues returned....
			if(count($allPvalues) > 0){
				$allpvals = "$IMAGESdir/allpvals$filenum.csv";
				$command = "touch $allpvals";
				$str=exec($command);
				$allpvalcsvfd = fopen($allpvals, 'w');
				foreach($allPvalues as $key2=>$value2){
					$allpvalcsvline = "$value2\n";
					fwrite($allpvalcsvfd, $allpvalcsvline);
					fflush($allpvalcsvfd);	
				}
				fclose($allpvalcsvfd);
			}
			if(count($sigPvalues) > 0){
				//echo "sigpvalues > 0 <BR>";
				asort($sigPvalues);
				//analyze($sigPvalues);
				$sortedpvalstable = "$IMAGESdir/pvaluetable$filenum.html";
				$command = "touch $sortedpvalstable";
				
				$str=exec($command);
				
				$sortedpvalscsv = "$IMAGESdir/pvaluetable$filenum.csv";
				$command = "touch $sortedpvalscsv";
				$str=exec($command);

				$pvaltablefd = fopen($sortedpvalstable, 'w');
				$pvalcsvfd = fopen($sortedpvalscsv, 'w');
				fwrite($pvaltablefd, "<html>");
				fwrite($pvaltablefd, "<p><strong><font color='blue'>Genes ranked by ascending p-values</font></strong></p>");
				fwrite($pvaltablefd, "<table>");
				
					fwrite($pvaltablefd, "<tr><td><b>Feature #</b></td><td><b>Feature Name</b><td><b>p-Value</b></td></tr>");
				
				foreach($sigPvalues as $key2=>$value2){
					//$genenamesql = "SELECT arraytype FROM agilentdata WHERE arrayid = $thisorganism";
						require './phpinc/organismurlselection.inc';
							$pvaltableline = "<tr><td><a href='"."."."$featureurl"."$key2' target='_blank'>$key2</a></td><td>$sigFeatureNames[$key2]</td><td>$value2</td></tr>";
							$pvalcsvline = "$key2,$sigFeatureNames[$key2],$value2\n";
					
					fwrite($pvaltablefd, $pvaltableline);
					fflush($pvaltablefd);
					fwrite($pvalcsvfd, $pvalcsvline);
					fflush($pvalcsvfd);
				}
				fwrite($pvaltablefd, "</table>");
				fwrite($pvaltablefd, "</html>");
				fflush($pvaltablefd);
				fclose($pvaltablefd);
				fclose($pvalcsvfd);
				
			}
			/*
			echo "<p>";
			foreach($sigPvalues as $key=>$value){
				echo "$key => $value<br>";
			}
			echo "</p>";
			*/
$cpsvgfile = ".$IMAGESreldir/imagesvg$filenum";
//$algo = 0;   // 0 = kmeans ; 1 = hierarchical
if($algo == 0){

	$clusteringmethod = "k-Means";
}else{
	$clusteringmethod = "Hierarchical";
}
?>

<div dojoType='dijit.TitlePane' title='<font color="blue" style="font-weight: bold;"><?php echo "$clusteringmethod"; ?> Clustering Results/Input Parameters</font>' open='true' width="800">
<h3>Results:</h3>
<table class='question'><tr class="question">
			<td class="questionparameter"><strong>Number of Arrays Returned:</strong></td>
			<td class="questionanswer"> <?php echo $arraycounter; ?></td>
			
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
				if($trxCluster != 0){
					echo "Yes";
				}
				else{
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
			<tr><td class="questionparameter"><strong>Minimal Repression:</strong></td>
			<td class="questionanswer">
			 <?php
					if($lowerboundmin == ""){
						 echo log10ToFoldChange($lowerbound);
					}
					else{
						echo "[".log10ToFoldChange($lowerbound).",".log10ToFoldChange($lowerboundmin)."]";
					}


			?></td><td class="questionparameter"><strong>Minimal Induction:</strong></td>
			<td class="questionanswer">
			<?php
					if($upperboundmax == ""){
						echo log10ToFoldChange($upperbound);
					}
					else{
						echo "[".log10ToFoldChange($upperbound).",".log10ToFoldChange($upperboundmax)."]";
					}
					
			?>
			</td></tr>
			<tr>
			<?php
				if(($anova == 1 || $ttest == 1)){
						echo "<td class=\"questionparameter\"><strong>Control Mean Subtracted</strong></td>";
						if($mincontrol == 1){
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
			?>
			</tr>
			




</table>
</div>

<div dojoType='dijit.TitlePane' title='<font color="blue" style="font-weight: bold;">Associated Data and Image Files</font>' open='false' width="800">
<h3>Data Files:</h3>
<table class="question">
	<tr class="question"><td class="questionparameter"><strong>Fold-Change Table (HTML):</strong></td><td class="questionanswer"><?php echo "<a href=\"./edge3tabledisplay.php?tableNum=$filenum\" target=\"_blank\">Fold-Change table</a>"; ?></td><td class="questionparameter"><strong>Fold-Change CSV File: </strong></td>
			<td class="questionanswer"><?php echo "<a href=\".$IMAGESreldir/$filenum.csv\" target=\"_blank\">Fold Change CSV</a>"; ?></td></tr>
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
				
				 
			?>


</table>
<h3>Image Files:</h3>
<table class="question">
	<tr class="question">
<td class="questionparameter">
	<?php
	if($clustoption!=1){
?>
			<a href="<?php echo ".$IMAGESreldir/svg$filenum.svg"; ?>" onClick="return popup(this,'SVG')">View SVG Heat Map</a>
			</td></tr>
			<tr><td class="questionparameter">
			<?php echo "<a href=\"".$cpsvgfile.".png\" target=\"_blank\">View PNG Heat Map</a>";?></td></tr>
			<tr><td class="questionparameter"><?php echo "<a href=\"".$cpsvgfile.".jpg\" target=\"_blank\">View JPG Heat Map</a>";?></td></tr>
<?php
	}

?>
<?php
	if($clustoption!=2){
?>
		
				<tr><td class="questionparameter"><a href='<?php echo ".$IMAGESreldir/".$filenum."clustering.png"; ?>' target='_blank'>R-generated heatmap PNG</a></td></tr>
				<tr><td class="questionparameter"><a href='<?php echo ".$IMAGESreldir/".$filenum."clusteringdev.svg"; ?>' target='_blank'>R-generated heatmap SVG</a></td></tr>
				
<?php
	}

?>

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
				$sqlResult = $db->Execute($sql);// mysql_query($sql, $db);
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

if($clustoption!=1){  # if only R-based algorithm is not used....
	if($arrayidCount == 1 && $clusterAlgo == 1){
					$trxCluster = 0;
					$algo = 0;
					$number = 1;
				}

    				//echo "Trx Cluster = $trxCluster <br>";
				if($trxCluster != 0){
					// NOTES:  Replaced $trxCluster w/ 2 so that dendrogram is displayed at top and treatments on the bottom. 01MAY2004
					$trxCluster = 2;
				}

			//$algo = 0;   // 0 = kmeans ; 1 = hierarchical
			//$clusterNumber = $number;  // for k-means only
			//$colorscheme = 0; 0 = red/green ; 1 = blue/yellow
			//$browserval = 1;
//analyze($_POST);
	
			if(($ttest == 1 || $anova == 1) &&  $cloneCount == 0){
				echo "No genes met your criteria.<br>";

			}else{
			
			$command = "touch $filenum"."garbagedump.txt";
			$str = exec($command);
			/*$command = "java -mx512m -jar EdgeClustering.jar $file $numberofclusters $arrayidCount $svgFile $algo $tableFile $colorscheme 2 $browserval$filenum >> garbagedump.txt";*/
			$command = "java -mx512m -jar EdgeClustering.jar \"$file\" $clusterNumber $arrayidCount \"$svgFile\" $algo \"$tableFile\" $colorscheme $trxCluster $browserval $filenum \"$IMAGESreldir\" >> $filenum"."garbagedump.txt";
			//java -mx512m -jar EdgeClustering.jar /var/www/edge2/IMAGES/data7099.txt 6 /var/www/edge2/IMAGES/svg7099.svg 1 /var/www/edge2/IMAGES/table7099 0 2 1 7099 >> garbagedump.txt

//$command = "java -mx512m -jar EdgeClustering.jar $file $clusterNumber $arrayidCount $svgFile $algo $tableFile $colorscheme $trxCluster $browserval $filenum >> garbagedump.txt";
				$userid = $_SESSION['userid'];
				if($userid == 1 ){
					echo "userid-based debug<br>";
					echo "$command <br>";
				}
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
				$command = "gzip --best $IMAGESdir/svg$filenum.svg";
				//echo $command;
				//$str=exec($command);
				$command = "rm -f $filenum"."garbagedump.txt";
				$str = exec($command);
				
				$command = "mv $IMAGESdir/svg$filenum.svg.gz $IMAGESdir/svg$filenum.svgz";
				//$str=exec($command);
				//echo "number is: $number<br>";
				// SETTING $imagesizeexceeded = 0.  need to figure out how i was using it.  27may2008
				$imagesizeexceeded = 0;
				//echo "CloneContainer count = ".count($cloneContainer)."<br>";
				if($cloneCount > 2400){
					$imagesizeexceeded = 1;
				}
				//echo "imagesizeexceeded = $imagesizeexceeded<br>";
				#createImage is located in:outputimage.inc
				createImage("svg$filenum.svg", $number,$imagesizeexceeded,$IMAGESreldir);
				
					if($outputformat == "0"){
						?>
						<embed src="<?php echo "$IMAGESreldir/svg$filenum.svg" ?>" width=800 height=1000 name="heatmap" type="image/svg+xml" />
						<?php
					}
					elseif($outputformat == "2"){
					?><p>
					<img src="<?php echo "$IMAGESreldir/imagesvg$filenum.jpg" ?>" alt="heatmap" align="bottom" usemap="#map1" border=0></img>
					</p>
					<?php
						if($includeimagemap != 0 || $includeimagemap == ""){
						include "$IMAGESdir/imagemapsvg$filenum";
						}
					}
					else{
					?>
					<p>
					<img src="<?php echo ".$IMAGESreldir/imagesvg$filenum.png" ?>" alt="heatmap" align="bottom" usemap="#map1" border=0></img>
					</p>
		
					<?php
		
						if($includeimagemap != 0 || $includeimagemap == ""){
							include "$IMAGESdir/imagemapsvg$filenum";
						}
					}
				
					# displaying R clustering heatmap
				?>
					
				<?php	
			
			
	// end table include....
			}
}else{
	?>
				<p>
				<img src="<?php echo ".$IMAGESreldir/".$filenum."clustering.png" ?>" alt="R-based heatmap" align="bottom" border=0></img>
				</p>
<?php

}
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
