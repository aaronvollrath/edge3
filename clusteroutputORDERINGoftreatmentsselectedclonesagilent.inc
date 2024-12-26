<?php
		/***********************************************************************************
		ORDERED SECTION...............
		***********************************************************************************/
		echo "This is ordered!";
	$arrayurl = "./agilentarrayinfo.php?arrayid=\n";
	$featureurl = "./agilentfeatureinfo.php?featurenum=\n";
	fwrite($fd, $arrayurl);
	fwrite($fd, $featureurl);
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
				$groupChecked = $_POST[$groupVal];
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
		foreach($orderedArray as $order){
			// Now need to determine whether a custom name was entered.....
			if($order != -99){ // If this is not a blank....
				$number_of_arrays++;
				$customid = "customname$order";
				if($_POST[$customid] != ""){
					$newname = $_POST[$customid];
					array_push($arrayidArray, $newname);
					array_push($arrayDescArray, $newname);
					$descrip = "$newname";
				}
				else{
				$arraydescSQL = "SELECT arraydesc from agilentexperiments where arrayid = $order ORDER BY arrayid";
				//echo "$arraydescSQL <br>";
				$arraydescResult = mysql_query($arraydescSQL, $db);
				$arrayVal = mysql_fetch_row($arraydescResult);
				//echo "$row[0] \t $arrayVal[0]<br>";
				array_push($arrayidArray, $row[0]);
				array_push($arrayDescArray, $arrayVal[0]);
				$descrip = "$arrayVal[0]";
				}
			}
			else{
				$descrip = "BLANK";
			}


			$descrip .= "\n";
			fwrite($fd, $descrip);

		}
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
					$clonestr = " FeatureNum = $cloneid ";
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
					$thresholdsql = "SELECT DISTINCT d.FeatureNum FROM agilentdata AS d WHERE ($arrayidsqlstring)  and $finalratioconstraint and ( $cloneidsqlstr ) and (d.gMeanSignal >= $gmeansignal and d.rMeanSignal >= $rmeansignal and d.PValueLogRatio <= $pValue) ORDER BY d.FeatureNum";
					}else{
					$thresholdsql = "SELECT DISTINCT d.FeatureNum FROM agilentcondenseddata AS d WHERE ($arrayidsqlstring)  and $finalratioconstraint and ( $cloneidsqlstr ) ORDER BY d.FeatureNum";

					}
					//echo "$thresholdsql<br>";
					$cloneidarray2 = array();
				$cloneContainer2 = array();
				$discardedFeatures2 = array();
				$cloneCount = 0;


					$fnResult = mysql_query($thresholdsql, $db);
					while($row = mysql_fetch_row($fnResult)){
						$clonestr = " FeatureNum = $row[0] ";
						array_push($cloneContainer2, $row[0]);
						array_push($cloneidarray2, $clonestr);
						$cloneor = "OR";
						array_push($cloneidarray2, $cloneor);
						$cloneCount++;
						//echo "$clonestr<br>";
					}

				/*$end = utime();
				$run = $end - $start;
				echo "<br><font size=\"1px\"><b>Got Features in  ";
				echo substr($run, 0, 5);
				echo " secs.</b></font>";
				//echo "CloneContainer2 contains".count($cloneContainer2)."items";
				*/

		$arrayidArray = $orderedArray;

   //echo "<br>CloneCount = $cloneCount<br>";
		if($cloneCount > 1){
			// Get rid of the last OR....
			array_pop($cloneidarray2);

			// Create the cloneid sql string
			$cloneidsqlstr = "";
			foreach($cloneidarray2 as $item){
				$cloneidsqlstr .= $item;
			}
			$hybArray = array();
			$hybArray2 = array();
			$totalArray = array();
			// GET ALL THE HYBRIDIZATION INFO FOR THESE ARRAYS.....
			// GET THESE DATA ONE AT A TIME AND ORDER BY CLONE NUMBER......
			$counter = 1;
			foreach($arrayidArray as $arrayid){
				//echo "<br>Counter = $counter<br>";
				if($arrayid != -99){
     					if($dataset == 1){
					$hybSQL = "SELECT arrayid, FeatureNum, LogRatio FROM agilentdata where arrayid = $arrayid and ( $cloneidsqlstr )  ORDER BY FeatureNum";
					//echo "$hybSQL <br>";
					}else{
					$hybSQL = "SELECT arrayid, FeatureNum, LogRatio FROM agilentcondenseddata where arrayid = $arrayid and ( $cloneidsqlstr )  ORDER BY FeatureNum";
					}
					//echo "<p>$hybSQL</p>";
					$hybResult = mysql_query($hybSQL, $db);
					while($row = mysql_fetch_row($hybResult)){
							$cloneid = $row[1];
							$finalratio = strval($row[2]);
							// need to convert the finalratio from log base 10 to fold change...
					if($finalratio >=0){
						$finalratio = pow(10,$finalratio);
					}else{
						$intermediateval = pow(10,$finalratio);
						$finalratio = -1/$intermediateval;
					}

					//echo "Final ratio for cloneid, $cloneid, is $finalratio<br>";
					if($finalratio == ""){
						echo "Final ratio for cloneid, $cloneid, is missing<br>";
					}
							$hybArray[$cloneid] = $finalratio;
					}
				}
				else{
					// how many clones? Go through cloneContainer and fill this hybArray...
					// just fill $hybArray w/ -9999
					foreach($cloneContainer as $aclone){
						$hybArray[$aclone] = -9999;
					}


				}
				array_push($totalArray, $hybArray);
				//analyze($hybArray);
				reset($hybArray);
				$hybArray = array();
				$counter++;
			}
			$arrayidCount = count($arrayidArray);
			//echo "ARRAY ID COUNT: $arrayidCount<br>";
			$newArray = array();

			//for($i = 1; $i <= $cloneCount; $i++){
			reset($cloneContainer2);
			foreach($cloneContainer2 as $i){
				for($j = 0; $j < $arrayidCount; $j++){
					$newArray[$i][$j] = $totalArray[$j][$i];
				}
			}
			//analyze($newArray);
			$cloneCount = count($cloneContainer2);
			foreach($cloneContainer2 as $i){

				$primarynamesql = "SELECT d.GeneName, d.SystematicName from agilentdata as d where d.FeatureNum = $i and arrayid = 1";

				$pnResult = mysql_query($primarynamesql, $db);
				$pnRow = mysql_fetch_row($pnResult);
				$primaryname = str_replace("\"","", $pnRow[0]);
				$primaryname = trim(ucfirst ( $primaryname));
				$primaryname = substr($primaryname, 0, 50);
				$refseq = trim($pnRow[1]);
				// Vestigial GO stuff from edge clustering algo....
				$goIDCounter = 0;
				$goidarray = array();

			$line = "$i\t $primaryname \t$refseq \t$goIDCounter";
			$gostr = "";
				if(count($goidarray) > 0){
					foreach($goidarray as $goid){
						$gostr .= "\t$goid";
					}
				}
				$line .= $gostr;
				foreach($newArray[$i] as $key=>$value) {
					$line.= "\t$value";
				}
				$line .= "\n";
				//echo "$line <br>";
				fwrite($fd, $line);
				fflush($fd);
				$index++;
			}
   			ftruncate($fd, ftell($fd));
			fclose($fd);
			$number = $_POST['number'];


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
			<td class="questionanswer"> <?php echo $number_of_arrays; ?></td>
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
				$sqlResult = mysql_query($sql, $db);
				$row = mysql_fetch_row($sqlResult);
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
				<a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a>
				</td>
			<?php
				}
			}else{
   			if($update == "true"){
			?>
				<td class="questionanswer">
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
			<a href="<?php echo "./IMAGES/svg$filenum.svgz"; ?>" onClick="return popup(this,'SVG')">View SVG Heat Map</a>
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
			}
			
			$cpsvgfile = "./IMAGES/imagesvg$filenum";
			//if($_SESSION['priv_level'] > 0){

   ?>
			<td class="questionparameter"><strong>Tabular format:</strong></td><td class="questionanswer"><?php echo "<a href=\"./tabledisplay.php?tableNum=$filenum\" target=\"_blank\">TABLE</a>"; ?></td><td class="questionparameter"><?php echo "<a href=\"".$cpsvgfile.".png\" target=\"_blank\">View PNG Heat Map</a>";?></td>
			<?php
   /*}else{echo "<td></td><td></td><td class=\"questionparameter\"><a href=\"".$cpsvgfile.".png\" target=\"_blank\">View PNG Heat Map</a> 		</td>";}
   */

			?>
			</tr>
			<tr><td></td><td></td><td></td><td></td>
			<td class="questionparameter"><?php echo "<a href=\"".$cpsvgfile.".jpg\" target=\"_blank\">View JPG Heat Map</a>";?></td></tr>
			</table>
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

			//$algo = 0;   // 0 = kmeans ; 1 = hierarchical
			$numberofclusters = $number;  // for k-means only
			//$colorscheme = 0; 0 = red/green ; 1 = blue/yellow
			//$browserval = 1;
			$randnum = rand(0, 25000);
			$command = "java -mx512m -jar EdgeClustering.jar $file $numberofclusters $arrayidCount $svgFile $algo $tableFile $colorscheme 0 $browserval $filenum >> garbagedump.txt";

			echo "<br>$command<br>";
				$str=passthru($command);
				$command = "cp ./IMAGES/svg$filenum.svg ./IMAGES/imagesvg$filenum.svg";
				$str = exec($command);
				$cpsvgfile = "/var/www/html/edge2/IMAGES/imagesvg$filenum.svg";

				$filesize = filesize($cpsvgfile);

				if($filesize > 3169300 && $outputformat == 0){
				echo "<br>LARGE SVG FILE: Displaying the PNG file.";
					$outputformat = 1;
				}
				$command = "gzip --best ./IMAGES/svg$filenum.svg";
				//echo $command;
				$str=exec($command);

				$command = "mv ./IMAGES/svg$filenum.svg.gz ./IMAGES/svg$filenum.svgz";
				$str=exec($command);
				createImage("svg$filenum.svg", $number);
			if($outputformat == "0"){
				?>
<embed src="<?php echo "./IMAGES/svg$filenum.svgz" ?>" width=<?php echo $w ?> height=<?php echo $h ?> name="heatmap" type="image/svg+xml" />
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
