<?php
	// Code for unordered arrays....


				// What chem were selected????
				$chemidarray = array();
				$chemSQL = "SELECT DISTINCT chemid FROM chem ORDER BY chemid";
				$chemResult = mysql_query($chemSQL, $db);

				while($row = mysql_fetch_row($chemResult)){

					// Check to see which boxes were checked...
					$chemid = $row[0];
					$thisVal = "chem$chemid";
					//echo "thisVal = $thisVal<br>";
					$post = $_POST[$thisVal];
					//echo "post = $post<br>";
					if($post != ""){
						$chemLookUpSQL = "SELECT chemical FROM chem where chemid = $post";
						$chemLookUpResult = mysql_query($chemLookUpSQL, $db);
						$chemrow = mysql_fetch_row($chemLookUpResult);
						//echo "<p>$chemrow[0] was chosen</p>";
						array_push($chemidarray, $post);
					}
				}


				// DETERMINE WHAT TREATMENTS WERE SELECTED....
				$trxidarray = array();
				$chemSQL = "SELECT DISTINCT sampleid FROM sampledata ORDER BY sampleid";
				$chemResult = mysql_query($chemSQL, $db);

				while($row = mysql_fetch_row($chemResult)){
				$sampleid = $row[0];
					// Check to see which boxes were checked...
					$sampleid = $row[0];
					$sampleid .=a;
					$thisVal = "trx$sampleid";

					$post = $_POST[$thisVal];
					$post = substr("$post", 0, -1);
					if($post != ""){
						$chemLookUpSQL = "SELECT chemical FROM chem where chemid = $post";
						//echo $chemLookUPSQL;
						$chemLookUpResult = mysql_query($chemLookUpSQL, $db);
						$chemrow = mysql_fetch_row($chemLookUpResult);
						array_push($trxidarray, $post);
					}
				}


				$chemArray = array();
				foreach($chemidarray as $chemid){
					$arrayStr = " chemid = $chemid ";
					array_push($chemArray, $arrayStr);
					$or = "OR";
					array_push($chemArray, $or);
				}

				foreach($trxidarray as $arrayid){
					$arrayStr = " sampleid = $arrayid ";
					array_push($chemArray, $arrayStr);
					$or = "OR";
					array_push($chemArray, $or);
				}
				// Pop the last or off...
				array_pop($chemArray);

				$chemidStr = "";
				foreach($chemArray as $item){
					$chemidStr .= $item;
				}



				$privval = $_SESSION['priv_level'];

				if($privval == ""){
					$priv = 1;
				}
				else{
					$priv = $privval;
				}

	$arrayurl = "http://edge.oncology.wisc.edu/sample.php?sampleid=\n";
	$featureurl = "http://edge.oncology.wisc.edu/cloneinfosvg.php?cloneid=\n";
	fwrite($fd, $arrayurl);
	fwrite($fd, $featureurl);
				//echo $chemidStr;
				// NOW NEED TO GET ALL THE TREATMENTS ASSOCIATED W/ THE CHOSEN CHEMICALS....
				// BASICALLY GETTING THE ARRAYIDS BECAUSE SAMPLEID = ARRAYID
				$arrayidSQL = "SELECT sampleid FROM sampledata where $chemidStr ORDER BY chemid, sampleid";
				//echo "$arrayidSQL<br>";
				$arrayidResult = mysql_query($arrayidSQL, $db);
				while($row = mysql_fetch_row($arrayidResult)){
					//echo "<p>Sample #$row[0] chosen</p>";
					if($priv != 99){
						$arraydescSQL = "SELECT arraydesc from array where arrayid = $row[0] AND (ownerid = 1 OR ownerid = $priv) ORDER BY arrayid";

					}
					else{
						$arraydescSQL = "SELECT arraydesc from array where arrayid = $row[0] ORDER BY arrayid";
					}
					//echo "$arraydescSQL<br>";
					$arraydescResult = mysql_query($arraydescSQL, $db);
					$arrayVal = mysql_fetch_row($arraydescResult);
					if($arrayVal != ""){
						//echo "ArrayVal != ''<br>";
						//echo "$row[0] \t $arrayVal[0]<br>";
						array_push($arrayidArray, $row[0]);
						array_push($arrayDescArray, $arrayVal[0]);
						$descrip = "$arrayVal[0]";
						$descrip .= "\n";
						fwrite($fd, $descrip);
					}
				}

				$arrayidsql = array();
				$experimentcounter = 0;
				foreach($arrayidArray as $id){
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
							$finalratioconstraint = "(finalratio <= $lowerbound or finalratio >=
							$upperbound AND finalratio <= $upperboundmax)";
						}
						else if($upperboundmax == "" && $lowerboundmin != ""){
							$finalratioconstraint = "(finalratio <= $lowerbound AND finalratio >= $lowerboundmin
							OR finalratio >= $upperbound)";
						}
						else{ // both of them are entered.....
							$finalratioconstraint = "(finalratio <= $lowerbound AND finalratio >= $lowerboundmin OR
							finalratio >= $upperbound AND finalratio <= $upperboundmax)";
						}

					}else{
						// Both rvalmax and lvalmin are blank....
						$finalratioconstraint = "(finalratio <= $lowerbound or finalratio >= $upperbound)";

					}

				if($dataset == 1){
					$cloneiddistinctsql = "SELECT DISTINCT cloneid from hybrids where ($arrayidsqlstring) and $finalratioconstraint ORDER BY cloneid";
				}else{
				$cloneiddistinctsql = "SELECT DISTINCT cloneid from condensedhybrids where ($arrayidsqlstring) and $finalratioconstraint ORDER BY cloneid";
				}
				if($priv_level == 99){
				//echo "$cloneiddistinctsql<br>";
				}
				$cloneidarray = array();
				$cloneContainer = array();
				$cloneidResult = mysql_query($cloneiddistinctsql, $db);
				$cloneCount = 0;
				while($cloneRow = mysql_fetch_row($cloneidResult)){
					$cloneid=$cloneRow[0];
					$clonestr = " cloneid = $cloneid ";
					array_push($cloneContainer, $cloneid);

					array_push($cloneidarray, $clonestr);
					$cloneor = "OR";
					array_push($cloneidarray, $cloneor);
					$cloneCount++;
				}

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
			foreach($arrayidArray as $arrayid){
				//echo "<br>Counter = $counter<br>";
				if($dataset == 1){
				$hybSQL = "SELECT arrayid, cloneid, ROUND(finalratio,1) FROM hybrids where arrayid = $arrayid and ( $cloneidsqlstr )  ORDER BY cloneid";
				//echo "$hybSQL <br>";
				}else{
				$hybSQL = "SELECT arrayid, cloneid, ROUND(finalratio,1) FROM condensedhybrids where arrayid = $arrayid and ( $cloneidsqlstr )  ORDER BY cloneid";
				}
				if($priv_level == 99){
					//echo "<p>$hybSQL</p>";
				}
				$hybResult = mysql_query($hybSQL, $db);
				while($row = mysql_fetch_row($hybResult)){
					$cloneid = $row[1];

					$finalratio = strval($row[2]);
					if($finalratio == ""){
						echo "Final ratio for cloneid, $cloneid, is missing<br>";
					}
					$hybArray[$cloneid] = $finalratio;
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
			foreach($cloneContainer as $i){
				for($j = 0; $j < $arrayidCount; $j++){
					$newArray[$i][$j] = $totalArray[$j][$i];
				}
			}
			//analyze($newArray);
			$cloneCount = count($cloneContainer);
			foreach($cloneContainer as $i){
				if($dataset == 1){
					$primarynamesql = "SELECT annname,refseq from annotations where cloneid = $i";
				}
				else{
					$primarynamesql = "SELECT annname,refseq from condensedannotations where cloneid = $i";
				}
				$pnResult = mysql_query($primarynamesql, $db);
				$pnRow = mysql_fetch_row($pnResult);
				$primaryname = str_replace("\"","", $pnRow[0]);
				$primaryname = trim(ucfirst ( $primaryname));
				$primaryname = substr($primaryname, 0, 50);
				$refseq = trim($pnRow[1]);
				if($refseq == ""){
					$refseq = " ";
					$goIDCounter = 0;
					$goidarray = array();
				}
				else{
					$query = "SELECT locusid, refaccver from loc2ref where refaccver = \"".$refseq."\"";
					//echo $query;
					$result = mysql_query($query, $db2)or die("Query 1 Failed!!!");
					$row  = mysql_fetch_row ($result);
					$locid = $row[0];
					$goIDCounter = 0;
					$goidarray = array();
					if($locid != ""){
						$query2 = "select goid from loc2go where locusid = $row[0]";
						//echo "<br>$query2<br>";
						//$query2new = $query2.$locID;
						$result2 = mysql_query($query2, $db2) or die("Query 2 Failed!!! <br> $query2");
						while($newrow = mysql_fetch_row($result2)){
							$goID = $newrow[0];
							array_push($goidarray, $goID);
							$goIDCounter++;
							$query3 = "select name from term where acc = \"".$goID."\"";
							$result3 = mysql_query($query3)
								or die("FAILED: .$query3");
							$goNames = "";
							while($query3row = mysql_fetch_row($result3)){
								$goNames .= "$query3row[0] <br>";
							}
						}
					}
					else{
						$goID = " ";
					}
						//$refseq = $row->refaccver;
				}
				if($dataset == 1){
				$line = "$i\t $primaryname \t$refseq \t$goIDCounter";
				}else{
					 // PUT IN A NEGATIVE SIGN BEFORE THE CLONE ID SO THAT WE KNOW IT'S
					 // FROM CONDENSED DATA SET....
					 $line = "-$i\t $primaryname \t$refseq \t$goIDCounter";
				}
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
			}
   			ftruncate($fd, ftell($fd));
			fclose($fd);
			$number = $_POST['number'];

			$randnum = rand(0, 25000);
			?>
			<table class="question">
			<thead>
			<tr>
			<th class="mainheader" colspan="4">Clustering Results</th>
			<th class="mainheader" colspan="1">Image Options</th>
			<?php
				if($userid != ""){
			?>
			<th class="mainheader">Save Query?</th>
			<?php
				}
			?>
			</tr>
			</thead>
			<tr class="question">
			<td class="questionparameter"><strong>Number of Arrays Returned:</strong></td>
			<td class="questionanswer"> <?php echo $arrayidCount; ?></td>
			<td class="questionparameter"><strong>Minimal Induction:</strong></td>
			<td class="questionanswer">
			<?php
					if($upperboundmax == ""){
						echo $upperbound;
					}
					else{
						echo "[$upperbound,$upperboundmax]";
					}
			?></td>
			<td class="questionparameter">
			<a href="<?php echo "./Instructions/svginstructions.php"; ?>" onClick="return popup(this,'Instructions')">SVG Instructions</a></td>
			<?php
				$savedquery = $_GET['savedquery'];
				if($savedquery != ""){
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
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery&savedquery=$savedquery&submit=true";?>"  target="<?php echo "_blank$randnum"; ?>">Update?</a></td>
				<?php
					}else{

					if($userid != ""){
				?>
						<td class="questionanswer">
					<a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a>
					</td>
				<?php
					}
				}
			}else{
				if($userid != ""){
					if($update == "true" || $update == ""){
				?>
					<td class="questionanswer">
					<a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a>
					</td>
				<?php
					}
					else{
				?>
					<td></td>
				<?php
					}
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
						 echo $lowerbound;
					}
					else{
						echo "[$lowerbound,$lowerboundmin]";
					}


			?></td>
			<td class="questionparameter">

			<a href="<?php echo "./IMAGES/svg$filenum.svgz"; ?>" onClick="return popup(this,'SVG<?php echo $filenum; ?>')">View SVG Heat Map</a>

			</td>
			<?php
			if($savedquery != ""){

					if($update == "true"){
			?>
					<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save as new query?</a></td>

			<?php
					}
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
			if($priv >= 99){
			?>

			<td class="questionparameter"><strong>Tabular format:</strong></td><td class="questionanswer"><?php echo "<a href=\"./tabledisplay.php?tableNum=$filenum\" target=\"_blank\">TABLE</a>"; ?></td>

			<?php
			}else{
			?>
			<td></td><td></td>
			<?php
			}
			// The name of the svg file....
   $cpsvgfile = "./IMAGES/imagesvg$filenum";
			?>
			<td class="questionparameter"><?php echo "<a href=\"".$cpsvgfile.".png\" target=\"_blank\">View PNG Heat Map</a>";?> 			</td>


			</tr>
			<tr>
			<?php
			if($dataset == 1){
				echo "<td class=\"questionparameter\"><strong>Data:</strong></td><td class=\"questionanswer\">All Clones</td>";
			}else{
				echo "<td class=\"questionparameter\"><strong>Data:</strong></td><td class=\"questionanswer\">Condensed</td><td></td><td></td><td class=\"questionparameter\"><a href=\"".$cpsvgfile.".jpg\" target=\"_blank\">View JPEG Heat Map</a></td>";

			}
			?>

			</tr>

			</table>
			<?php

				$w = $arrayidCount *20 + 500;
				$h = $cloneCount * 10 + 200;
				if($outputformat == 0){
				echo "<p>Can't see the heat map?  Click the image to download the SVG viewer.
				<A HREF=\"http://www.adobe.com/svg/viewer/install/main.html\" target=\"_blank\"><IMG SRC=\"./GIFs/svgdownload.gif\" BORDER=\"0\" HEIGHT=\"25\" WIDTH=\"70\"></A>
				</p>";
				}
				?>
				<?php
				// If there's only one treatment, don't cluster by treatments!!!!!
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
				// Temporarily setting browserval to 1, because of script probs w/ ie.
				//$browserval = 1;
				$command = "java -mx512m -jar EdgeClustering.jar $file $number $arrayidCount $svgFile $algo $tableFile $colorscheme 2 $browserval $filenum >> garbagedump.txt";
				if($_SESSION['priv_level'] >= 99){
				//	echo "$command <br>";
				}
				//if($priv_level == 99){
				//$command = "java -jar Cluster.jar";
				//	echo "<hr>$command<hr>";
				//}
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
				if($_SESSION['priv_level'] >= 99){
					//echo "createImage(\"svg$filenum.svg\", $number);<br>";
				}
				//
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
				else{
					echo "<br>There were no genes that met your criteria.<br>";
					$end = utime(); $run = $end - $start;
				echo "<font size=\"1px\"><b>Query results returned in ";
				echo substr($run, 0, 5);
				echo " secs.</b></font>";
				}

?>
