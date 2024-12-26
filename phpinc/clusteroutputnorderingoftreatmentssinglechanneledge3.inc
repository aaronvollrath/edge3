<?php
$csvgostr = "";
#analyze($_POST);
$gmeansignal = $_POST['gmeansignal'];
$rmeansignal = $_POST['rmeansignal'];
$countSQL = "SELECT MAX(arrayid) from $arraydatatable";
		$countResult = $db->Execute($countSQL);
		$row = $countResult->FetchRow();
		$maxarrayID = $row[0];
		$userid = $_SESSION['userid'];
//echo "<br>$countSQL<br>";
//echo "<br>maxarrayid : $maxarrayID<br>";
// Determine what arrays were selected....
//echo "csv file : $csvfd<br>";
$userid = $_SESSION['userid'];
	$arrayurl = "./agilentarrayinfo.php?arrayid=\n";
	for($i = 1; $i <= $maxarrayID;$i++){
		$postval = "array$i";
		if(isset($_POST[$postval])){
			$val=$_POST[$postval];
			if($val > 0){
				// look up the organism...
				$organismSQL = "SELECT arrayversion FROM agilentdata WHERE arrayid = $val";
				echo "<br>$organismSQL<br>";
				$organismResult = $db->Execute($organismSQL);
				$organismrow = $organismResult->FetchRow();
				$thisorganism = $organismrow[0];
				break;
			}
		}
	}
	if($userid == 1){
		// what the hell is this organism?
		echo "thisorganism is a: $thisorganism<br>";
		//analyze($_POST);

	}
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
				$sqlResult = $db->Execute($sql);
				$row = $sqlResult->FetchRow();
				///echo "$row[0] <br>";
				$desc = $row[0] . "\n";
				$csvdesc = ",$row[0]";
				fwrite($fd, $desc);
				fwrite($csvfd, $csvdesc);
				array_push($arrayidarray, $val);
			}
		}
	}
	$length = count($arrayidarray);
		fwrite($csvfd, "\n");
	// Now need to get the expression values that meet the thresholds set for the arrays selected...
	$arrayidsql = array();
	// this is an array used to store the normalization values....multidimensional w/ key of arrayid...
	$normalizationvalues = array();
				$experimentcounter = 0;
				foreach($arrayidarray as $id){
					$val = " d.arrayid = $id ";
					$idVal = "$id";
					// Initialize each of the arrays.
					//$normalizationvalues["$idVal"] = array();
					// Need to get the normalization value...
					$normSQL = "SELECT AVG(gProcessedSignal) FROM agilentdata WHERE arrayid = $idVal and ProbeUID = 1306";
					$normResult = $db->Execute($normSQL);
					$row = $normResult->FetchRow();
					$normalizationvalues["$idVal"] = $row[0];
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

analyze($normalizationvalues);
echo "<br>";
$median = median($normalizationvalues);
echo "<br>median of normalizationvalues = $median<br>";
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
					$featurenumSQL = "SELECT DISTINCT d.FeatureNum, d.GeneName, d.SystematicName from $arraydatatable as d, agilentcontroltype as c where ($arrayidsqlstring) and (c.ControlType = '0' and d.FeatureNum = c.FeatureNum) and (d.gProcessedSignal >= $gmeansignal and d.rProcessedSignal >= $rmeansignal and d.PValueLogRatio <= $pValue) AND (d.gIsFeatNonUnifOL = '0' and d.rIsFeatNonUnifOL='0') ORDER BY d.FeatureNum";
			}
			else{
				$featurenumSQL = "SELECT DISTINCT d.FeatureNum from agilentcondenseddata as d where ($arrayidsqlstring) and $finalratioconstraint";
			}
				if($userid == 1){
					echo "$featurenumSQL <br>";
				}
					$cloneidarray = array();
				$cloneContainer = array();
				$geneNameContainer = array();
				$systematicNameContainer = array();
				$featureNumResult = $db->Execute($featurenumSQL);
				$cloneCount = 0;
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
						$annoSQL = "SELECT DISTINCT GeneName, SystematicName FROM $arraydatatable WHERE FeatureNum = $cloneid AND arrayid = $arrayid";
						$annoResult = $db->Execute($annoSQL);
						$annos =$annoResult-FetchRow();
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
			$allValues = array();  // this is way inefficient... but testing at the moment....
			// GET ALL THE HYBRIDIZATION INFO FOR THESE ARRAYS.....
			// GET THESE DATA ONE AT A TIME AND ORDER BY CLONE NUMBER......
			$counter = 1;
			foreach($arrayidarray as $arrayid){
				//echo "<br>Counter = $counter<br>";
				if($dataset == 1){
					$hybSQL = "SELECT arrayid, FeatureNum, gProcessedSignal, PValueLogRatio, gIsFeatNonUnifOL, rIsFeatNonUnifOL FROM $arraydatatable where arrayid = $arrayid and ( $cloneidsqlstr ) ORDER BY FeatureNum";
				}else{ //using condensed data
					$hybSQL = "SELECT arrayid, FeatureNum, LogRatio FROM agilentcondenseddata where arrayid = $arrayid and ( $cloneidsqlstr ) ORDER BY FeatureNum";
				}
				//echo "$hybSQL <br>";
				$normalizationvalue =$normalizationvalues[$arrayid];
				echo "$normalizationvalues[$arrayid]<br>";
				$hybResult = $db->Execute($hybSQL);
				while($row = $hybResult->FetchRow()){
					$cloneid = $row[1];  // agilent featurenum...

					$finalratio = strval($row[2]); // agilent logratio
					$thisFeaturePValue= $row[3];
					$thisFeaturegIsFeatNonUnifOLValue = $row[4];
					$thisFeaturerIsFeatNonUnifOLValue = $row[5];

					if($userid == 1){
						//echo "arrayid: $row[0] FeatureNum: $row[1]<br>";
						//echo "gIsFeatNonUnifOLValue : $thisFeaturegIsFeatNonUnifOLValue gIsFeatNonUnifOLValue: $thisFeaturerIsFeatNonUnifOLValue<br>";
					}

					// need to convert the finalratio from log base 10 to fold change...
					$finalratio = $finalratio/$normalizationvalue;
					/*if($finalratio >=0){

					//logb(n) = log(n)/log(b), where log is the neperian (or natural) logarithm.
						//$finalratio = pow(10,$finalratio);
						// normalize value
						//$finalratio = log($finalratio)/log(50);
						if($finalratio <= 1){
							//$finalratio = 1;
						}
					}else{
						$intermediateval = pow(10,$finalratio);
						$finalratio = -1/$intermediateval;
					}
					*/
					//echo "Final ratio for cloneid, $cloneid, is $finalratio<br>";
					if($finalratio == ""){
						echo "Final ratio for cloneid, $cloneid, is missing<br>";
					}

					if($thisFeaturegIsFeatNonUnifOLValue == 1 || $thisFeaturerIsFeatNonUnifOLValue == 1){
						$finalratio = -99999999;
					}
					if($finalratio != -99999999){
						array_push($allValues, $finalratio);
					}
					$hybArray[$cloneid] = $finalratio;
				}
				array_push($totalArray, $hybArray);
				//analyze($hybArray);
				reset($hybArray);
				$hybArray = array();
				$counter++;
			}


			$medval = median($allValues);
			$minval = min($allValues);

			$maxval = max($allValues);


			echo "<br>MEDIAN OF ALL VALUES: ";
			echo "$medval<br>";
			echo "<br>MIN OF ALL VALUES: ";
			echo "$minval<br>";
			echo "<br>MAX OF ALL VALUES: ";
			echo "$maxval<br>";
			$minval += $medval;
			$maxval += $medval;
			$range = $maxval - $minval;
			echo "<br>RANGE OF ADJUSTED VALUES: ";
			echo "$range<br>";
			echo "<br>MIN OF ADJUSTED VALUES: ";
			echo "$minval<br>";
			echo "<br>MAX OF ADJUSTEDS VALUES: ";
			echo "$maxval<br>";

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
					// now add median value to all values to make them < 0?\
					if($value != -99999999){
						$value += $medval;
					}
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
		?>
			<table class="question">
			<thead>
			<tr>
			<th class="mainheader" colspan="4">Clustering Results</th>
			<th class="mainheader" colspan="1">SVG Options</th>
			<th class="mainheader">Save Query?</th>
			</tr>
			</thead>
			<tr class="question">
			<td class="questionparameter"><strong>Number of Arrays Returned:</strong></td>
			<td class="questionanswer"> <?php echo $arrayidCount; ?></td>
			<td class="questionparameter"><strong>Minimal Induction:</strong></td>
			<td class="questionanswer">
			<?php
					if($upperboundmax == ""){
						echo log10ToFoldChange($upperbound);
					}
					else{
						echo "[".log10ToFoldChange($upperbound).",".log10ToFoldChange($upperboundmax)."]";
					}


			?>
			</td>
			<td class="questionparameter">

			<a href="<?php echo "./Instructions/svginstructions.php"; ?>" onClick="return popup(this,'Instructions')">Instructions</a></td>
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
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery&savedquery=$savedquery&submit=true";?>"
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
			?>
			</tr>
			<tr class="question">
			<td class="questionparameter"><strong>Number of genes:</strong></td>
			<td class="questionanswer"> <?php echo $cloneCount; ?></td>
			<td class="questionparameter"><strong>Minimal Repression:</strong></td>
			<td class="questionanswer">
			 <?php
					if($lowerboundmin == ""){
						 echo log10ToFoldChange($lowerbound);
					}
					else{
						echo "[".log10ToFoldChange($lowerbound).",".log10ToFoldChange($lowerboundmin)."]";
					}


			?></td>
			<td class="questionparameter">
			<a href="<?php echo "./IMAGES/svg$filenum.svg"; ?>" onClick="return popup(this,'SVG')">View SVG Heat Map</a>
			</td>
			<?php if($savedquery != "" && $update == "true"){

			$tempsql = "SELECT MAX(query) FROM savedqueries";
			$tempresult = mysql_query($tempsql, $db);
			$temprow = mysql_fetch_row($tempresult);
			$tempquery = $temprow[0];
			?>
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save as new query?</a></td>
			<?php
			}
			?>
			</tr>
			<tr class="question">
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
			</td>
			<?php
			}$cpsvgfile = "./IMAGES/imagesvg$filenum";
			if($_SESSION['priv_level'] >= 0){

   ?>
			<td class="questionparameter"><strong>Tabular format:</strong></td><td class="questionanswer"><?php echo "<a href=\"./edge3tabledisplay.php?tableNum=$filenum\" target=\"_blank\">TABLE</a>"; ?></td><td class="questionparameter"><?php echo "<a href=\"".$cpsvgfile.".png\" target=\"_blank\">View PNG Heat Map</a>";?></td>
			<?php
   }else{echo "<td></td><td></td><td class=\"questionparameter\"><a href=\"".$cpsvgfile.".png\" target=\"_blank\">View PNG Heat Map</a> 		</td>";}

			?>
			</tr>
			<tr><td class="questionparameter"><strong>Data Set:</strong>
			</td><td class="questionanswer"><strong>
			<?php if($dataset == 1){
				echo "Uncondensed";
				}
				else{
				echo "Condensed";
				}
			?></strong></td>



			<td class="questionparameter"><strong>CSV File: </strong></td>
			<td class="questionanswer"><?php echo "<a href=\"../IMAGES/$filenum.csv\" target=\"_blank\">CSV</a>"; ?></td>

   </td>
			<td class="questionparameter"><?php echo "<a href=\"".$cpsvgfile.".jpg\" target=\"_blank\">View JPG Heat Map</a>";?></td></tr>
			</table>
		<?php




			//$algo = 0;   // 0 = kmeans ; 1 = hierarchical
			$clusterNumber = $number;  // for k-means only
			//$colorscheme = 0; 0 = red/green ; 1 = blue/yellow
			//$browserval = 1;
			$randnum = rand(0, 25000);
			/*$command = "java -mx512m -jar EdgeClustering.jar $file $numberofclusters $arrayidCount $svgFile $algo $tableFile $colorscheme 2 $browserval$filenum >> garbagedump.txt";*/
			$command = "java -mx512m -jar EdgeClustering.jar $file $clusterNumber $arrayidCount $svgFile $algo $tableFile $colorscheme 2 $browserval $filenum >> garbagedump.txt";

				$userid = $_SESSION['userid'];
				if($userid == 1){
					echo "userid-based debug<br>";
					echo "$command <br>";
				}
				$str=passthru($command);
				$command = "cp ./IMAGES/svg$filenum.svg ./IMAGES/imagesvg$filenum.svg";
				$str = exec($command);
				$cpsvgfile = "/var/www/IMAGES/imagesvg$filenum.svg";

				$filesize = filesize($cpsvgfile);

				if($filesize > 3169300 && $outputformat == 0){
				echo "<br>LARGE SVG FILE: Displaying the PNG file.";
					$outputformat = 1;
				}
				$command = "gzip --best ./IMAGES/svg$filenum.svg";
				//echo $command;
				//$str=exec($command);

				$command = "mv ./IMAGES/svg$filenum.svg.gz ./IMAGES/svg$filenum.svgz";
				//$str=exec($command);
				createImage("svg$filenum.svg", $number);
			if($outputformat == "0"){
				?>
<embed src="<?php echo "./IMAGES/svg$filenum.svg" ?>" width=<?php echo $w ?> height=<?php echo $h ?> name="heatmap" type="image/svg+xml" />
				<?php
				}
				elseif($outputformat == "2"){
				?><p>
				<img src="<?php echo "./IMAGES/imagesvg$filenum.jpg" ?>" alt="heatmap" align="bottom" usemap="#map1" border=0></img>
				</p>
				<?php
					if($includeimagemap != 0 || $includeimagemap == ""){
					include "./IMAGES/imagemapsvg$filenum";
					}
				}
				else{
				?>
				<p>
				<img src="<?php echo "./IMAGES/imagesvg$filenum.png" ?>" alt="heatmap" align="bottom" usemap="#map1" border=0></img>
				</p>
				<?php
					if($includeimagemap != 0 || $includeimagemap == ""){
						include "./IMAGES/imagemapsvg$filenum";
					}
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
