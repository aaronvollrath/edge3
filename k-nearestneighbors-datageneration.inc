<?php

		$numberOfClasses = $numberOfGroups;

		// For each ordered arrayid, get the description....
		$trxCounter = 0;
		// Now place the orderedGroupArray into a one dimensional
		$orderedArray = array();
		for($i = 0; $i<$numberOfArrays; $i++){
			//foreach($orderedGroupArray[$i] as $item){
			$arrayindexval = "arraynum".$i;
			$item = $_POST[$arrayindexval];
				array_push($orderedArray, $item);
			//}
		}

		//echo "The treatments in order: <br>";
		//echo "Ordered array....<BR>";
		//analyze($orderedArray);
		//echo "#######################<BR>";
		$classcounter = 0;
		$namecount = 0;
		foreach($orderedArray as $order){
			//echo "order = $order<br>";
			// Now need to determine whether a custom name was entered.....
			$customnamenum = $namecount +1;
			//if($order != -99){ // If this is not a blank....
				$customid = "customname$customnamenum";
				#echo $customid."<br>";
				if($_POST[$customid] != ""){
					$newname = $_POST[$customid];
					$newname = str_replace(",", "_", $newname);
					array_push($arrayidArray, $newname);
					array_push($arrayDescArray, $newname);
					$descrip = "$newname";
				}
				else{
				//$thisarrayid = $_POST[$order];
				$arraydescSQL = "SELECT arraydesc from agilent_arrayinfo where arrayid = $order ORDER BY arrayid";
				//echo "$arraydescSQL <br>";
				$arraydescResult = $db->Execute($arraydescSQL);#mysql_query($arraydescSQL, $db);
				$arrayVal = $arraydescResult->FetchRow();
				$name = str_replace(",", "_", $arrayVal[0]);
				array_push($arrayidArray, $row[0]);
				array_push($arrayDescArray, $name);
				$descrip = "$name";
				}
 

			$csvdesc =",$descrip";
			$descrip .= "\n";
			//echo "$descrip<br>";
			$descrip = "#".$descrip;
			fwrite($fd, $descrip);
			//echo "order = $order<br>";
			$order .= "\n";
			fwrite($fd, $order);
			$arrayclass = "arraynum".$classcounter."class";
			$classval = $_POST[$arrayclass];

			//echo "classval = $classval<br>";
			$classval .= "\n";
			fwrite($fd, $classval);
			fwrite($csvfd, $csvdesc);
			$dataSQL = "SELECT LogRatio FROM agilentdata WHERE arrayid = $order ORDER BY FeatureNum";
			//echo "$dataSQL <br>";
			$logratioResult = $db->Execute($dataSQL);#mysql_query($dataSQL, $db);
			while($logratio = $logratioResult->FetchRow()){
				$logratio = $logratio[0];
				$logratio .= "\n";
				//echo "$logratio<br>";
				fwrite($fd, $logratio);
			}
			$classcounter++;
			$namecount++;


		}
?>

<table class="question">
			<thead>
			<tr>
			<th class="mainheader"><font color='black'><b>k-Nearest Neighbors Results</b></font></th>
			</tr>
			</thead>
			<tr class="question">
			
			
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
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery&savedquery=$savedquery&submit=true&querytype=2";?>"
				target="<?php echo "_blank"; ?>">Update This Query?</a></td>
			<?php
				}else{
			?>
					<td class="questionanswer">
				<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2";?>"  target="<?php echo "_blank"; ?>">Save this Query?</a>
				</td>
			<?php
				}
			}else{
   			if(isset($update)){
			?>
				<td class="questionanswer">
				<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2";?>"  target="<?php echo "_blank"; ?>">Save This Query?</a>
				</td>
			<?php
				}
				else{
			?>
				<td class="questionanswer">
					<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2";?>"  target="<?php echo "_blank"; ?>">Save Query?</a>
				</td>
			<?php
				}
			}
			?>
			</tr>
			<tr class="question">
			<?php if($savedquery != "" && $update == "true"){
			?>
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank"; ?>">Save as new query?</a></td>
			<?php
			}
			?>
			</tr>
			</table>
<?php
		$numberKNearestNeighbors = $_POST['numberKNearestNeighbors'];
		$numberOfClasses = $_POST['numberOfGroups'];
/*$command = "java -mx512m -jar EdgeClustering.jar $file $clusterNumber $arrayidCount $svgFile $algo $tableFile $colorscheme 2 $browserval $filenum >> garbagedump.txt";*/
$command = "java -mx512m kNearest \"$file\" $numberKNearestNeighbors $numberOfClasses";

			#echo "<br>$command<br>";
				$str=passthru($command);


?>
