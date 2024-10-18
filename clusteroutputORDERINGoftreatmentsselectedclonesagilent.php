<?php
		/***********************************************************************************
		ORDERED SECTION...............
		***********************************************************************************/
		//echo "This is ordered!";

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
		//}
		//echo "The treatments in order: <br>";
		foreach($orderedArray as $order){
			// Now need to determine whether a custom name was entered.....
			if($order != -99){ // If this is not a blank....
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

		$text = $_POST['cloneList'];

		$text = str_replace("\n", ",", $text);
		$pieces = explode(",", $text);
		//echo(implode(" ", $pieces));
		$text = "";
		//echo "You entered ".count($pieces)." Clone ID numbers";
		$cloneidarray = array();
		$cloneContainer = array();
		$cloneCount = 0;
		while(count($pieces) >= 1){
			$cloneid = trim(array_shift($pieces));

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
		$arrayidArray = $orderedArray;

   //echo "<br>CloneCount = $cloneCount<br>";
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
			$geneNameContainer = array();
			$systematicNameContainer = array();
			foreach($arrayidarray as $arrayid){
				//echo "<br>Counter = $counter<br>";
				// Do we need to make a call to get the genename and systematic name?
				if($counter == 1){

				$hybSQL = "SELECT arrayid, FeatureNum, LogRatio, GeneName, SystematicName FROM agilentdata where arrayid = $arrayid and ( $cloneidsqlstr ) ORDER BY FeatureNum";
				//echo "$hybSQL <br>";
				}
				else{
					$hybSQL = "SELECT arrayid, FeatureNum, LogRatio FROM agilentdata where arrayid = $arrayid and ( $cloneidsqlstr ) ORDER BY FeatureNum";
				}
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
					if($counter==1){
						$primaryname = str_replace("\"","", $row[3]);
						$primaryname = trim(ucfirst ( $primaryname));
						$primaryname = substr($primaryname, 0, 50);
						array_push($geneNameContainer, $primaryname);
						$refseq = trim($cloneRow[4]);
						array_push($systematicNameContainer, $refseq);
					}
					//echo "Final ratio for cloneid, $cloneid, is $finalratio<br>";
					if($finalratio == ""){
						echo "Final ratio for cloneid, $cloneid, is missing<br>";
					}
					}							$hybArray[$cloneid] = $finalratio;
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
			$arrayidCount = count($arrayidArray);
			//echo "ARRAY ID COUNT: $arrayidCount<br>";
			$newArray = array();

			//for($i = 1; $i <= $cloneCount; $i++){
			foreach($cloneContainer as $i){
				for($j = 0; $j < $arrayidCount; $j++){
					$newArray[$i][$j] = $totalArray[$j][$i];
				}
			}
			//analyze($newArray);
			$cloneCount = count($cloneContainer);
			foreach($cloneContainer as $i){

				$primaryname = $geneNameContainer[$index];
				$refseq = $systematicNameContainer[$index];
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
			}$cpsvgfile = "./IMAGES/imagesvg$filenum";
			if($_SESSION['priv_level'] >= 99){

   ?>
			<td class="questionparameter"><strong>Tabular format:</strong></td><td class="questionanswer"><?php echo "<a href=\"./tabledisplay.php?tableNum=$filenum\" target=\"_blank\">TABLE</a>"; ?></td><td class="questionparameter"><?php echo "<a href=\"".$cpsvgfile.".png\" target=\"_blank\">View PNG Heat Map</a>";?></td>
			<?php
   }else{echo "<td></td><td></td><td class=\"questionparameter\"><a href=\"".$cpsvgfile.".png\" target=\"_blank\">View PNG Heat Map</a> 		</td>";}

			?>
			</tr>
			<tr><td></td><td></td><td></td><td></td>
			<td class="questionparameter"><?php echo "<a href=\"".$cpsvgfile.".jpg\" target=\"_blank\">View JPG Heat Map</a>";?></td></tr>
			</table>
		<?php




			//$algo = 0;   // 0 = kmeans ; 1 = hierarchical
			$numberofclusters = $number;  // for k-means only
			//$colorscheme = 0; 0 = red/green ; 1 = blue/yellow
			//$browserval = 1;
			$randnum = rand(0, 25000);
			$command = "java -mx512m -jar Cluster3.jar $file $numberofclusters $arrayidCount $svgFile $algo $tableFile $colorscheme 0 $browserval";

			//echo "<br>$command<br>";
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
				//$str=exec($command);

				$command = "mv ./IMAGES/svg$filenum.svg.gz ./IMAGES/svg$filenum.svgz";
				//$str=exec($command);
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
}
else{
	echo "<br>There were no genes that met your criteria.<br>";
	$end = utime(); $run = $end - $start;
echo "<font size=\"1px\"><b>Query results returned in ";
echo substr($run, 0, 5);
echo " secs.</b></font>";
}
?>
