<?php
require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
$db2 = mysql_connect("localhost", "root", "arod678cbc3",TRUE);
mysql_select_db("mygo", $db2);
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}
include 'header.inc';
require "formcheck2.inc";
include 'edge_update_user_activity.inc';
function analyze(&$array) {
   foreach($array as $key=>$value) {
       if(is_array($value)) {
           echo "<li>Array:<blockquote>";
             analyze($value);
           echo "</blockquote>";
         } elseif(is_object($value)) {
           echo "<li>Object:<blockquote>";
             analyze($value);
           echo "</blockquote>";
         } else {
             echo "<li>[" . $key . "] " . $value;
       }
   }
}
$algo = -1;

//echo "Order Option is: $orderoptions\n";

if (isset($_POST['submit'])) {
$algo = $_POST['clusterAlgo'];
}

$orderingMethod = -1;  // Used to determine how things are ordered when k-means or hierarchical w/ no clustering of trxs....

$clusterType = "Treatment and Gene Clustering";
if($algo > -1){
	if($algo == 0){
			$clusterType= "K-Means Clustering";
			// Need to determine what to set ordering method...
			if($orderoptions!=0){
				//
				//echo "orderoptions != 0...<br>";
				if($orderoptions == 1){
					// Order by individual trxs...

					$orderingMethod = 1;
				}
				else{//$orderoptions == 2...
					$orderingMethod = 2;
				}
			}
			else{
				$orderingMethod = 0;
			}

		}
		else{
			$clusterType=  "Hierarchical Clustering";
			if($trxCluster == 0){// If not clustering by treatments....
				if($orderoptions!=0){
					//
					if($orderoptions == 1){
						// Order by individual trxs...
						$orderingMethod = 1;
					}
					else{//$orderoptions == 2...
						$orderingMethod = 2;
					}
				}
				else{
					$orderingMethod = 0;
				}

			}
			else{
				$orderingMethod = 0;
			}
		}
}

?>

<body onload="return hideTrxRowOnLoad()">

	<?php
		include 'banner.inc';
	?>

 <div class="boxmiddle">
 <?php
include 'questionmenu.inc';
?>

 <h3 class="contenthead"><?php echo $clusterType; ?></h3>

<div class="content">
<?php
if (isset($_POST['submit']) && $orderingMethod == 0 || $orderedSubmit == "true") { // if form has been submitted and it is not being ordered or orderedSubmit is true...
	//analyze($_POST);
		if($orderedSubmit == "true"){
			if($_GET['savedquery'] == ""){  // If this is not a saved query
       //echo "This is not a saved query......";
				$thisNum = $querynum;
			}else{
   // echo "This is a saved query... UPDATEING TEMP...<BR>";
				$thisNum = $tempquery;

			}
			// Form is being submitted from the second data input screen....
			// Get the value for this query and then update the table accordingly...
			// Later an option will be given so that the user can save the query....
			//$querynum = $querynum;
			// Get the POST values and concatenate them....
			$query2text = "";
			$query2optstext = "";

			reset ($_POST);
			while(list($key, $val) = each ($_POST)){
				if($key == "option"){
					$optionarray = $_POST['option'];
					foreach($optionarray as $key=>$value) {
						$query2optstext .= "$key=$value:";
					}
				}
				else{
					if($key != "submit"){
						// Probably don't want and don't need the submit value....
						$query2text .= "$key=$val:";
					}
				}
			}
			

			//echo "in query 2 submit section<br>";
			$sql = "UPDATE savedqueries SET query2 = \"$query2text\" WHERE query=$thisNum";
			$sqlResult = mysql_query($sql, $db);
			//echo "$sql <br>";
			$sql = "UPDATE savedqueries SET query2opts = \"$query2optstext\" WHERE query=$thisNum";
			$sqlResult = mysql_query($sql, $db);
			//echo "$sql <br>";



		}else{
		
			if($_GET['savedquery'] == ""){  // If this is not a saved query
   				//echo "This is not a saved query......<BR>";
				// Need to get the max number in the savedqueries table and add one to that,
				// because that is the new number for this query....
				if($tempquery == ""){
					$sql = "SELECT MAX(query) FROM savedqueries";
					$sqlResult = mysql_query($sql, $db);
					$row = mysql_fetch_row($sqlResult);
					$querynum = $row[0];
					if($querynum == "NULL"){  // a boundary condition... the table is empty.  the other boundary is 10^10... not checking that...
						$querynum = 1;
					}else{
					// increment...
						$querynum += 1;
					}
					$tempquery = $querynum;
					$thisNum = $querynum;
				}else{
					$thisNum = $tempquery;
				}



			}else{
				//echo "This is a saved query... UPDATEING TEMP... and tempquery = $tempquery<BR>";
				$thisNum = $tempquery;

			}
			// There was no custom ordering or name changes w/ this....
			// Form is being submitted from the first data input screen....
			// Get the value for this query and then update the table accordingly...
			// Later an option will be given so that the user can save the query....


			// Get the POST values and concatenate them....
			$query1text = "";

			reset ($_POST);
			while(list($key, $val) = each ($_POST)){
				//echo "iterating...<br>";
				if($key != "submit"){
						// Probably don't want and don't need the submit value....
						$query1text .= "$key=$val:";
						//echo "$key=$val<br>";
					}


			}
				//echo "in query 1 submit section<br>";
			$sql = "SELECT query FROM savedqueries WHERE query = $tempquery";
			//echo "$sql<br>";
			$sqlResult = mysql_query($sql, $db);
			$row = mysql_fetch_row($sqlResult);
			if($row[0] != ""){
				$sql = "UPDATE savedqueries SET query1= \"$query1text\" WHERE query=$tempquery";
				$sqlResult = mysql_query($sql, $db);
				//echo "$sql<br>";
			}else{
				$sql = "INSERT savedqueries (query, userid, query1, querydate) VALUES($thisNum, $userid, \"$query1text\", NOW())";
				$sqlResult = mysql_query($sql, $db);
				//echo "$sql <br>";
			}
		}
 

		if($dataset == ""){
			$dataset = 1;
		}
		//echo "Dataset is $dataset<br>";
		/*
		print "Posted variables: <br>";
		reset ($_POST);
		while(list($key, $val) = each ($_POST)){
			if($key == "option"){
				$optionarray = $_POST['option'];
				foreach($optionarray as $key=>$value) {
					echo "<li>[" . $key . "] " . $value;
				}
				echo "<br>";
				//analyze($_POST['option']);
			}else{
				print $key . " = " . $val . "<br>";
			}
		}*/


	$filenum = rand(0, 25000);
	$file = "/var/www/html/edge2/IMAGES/data$filenum.txt";

	$svgFile = "/var/www/html/edge2/IMAGES/svg$filenum.svg";
	$tableFile = "/var/www/html/edge2/IMAGES/table$filenum";
	$command = "touch $file";
	$str=exec($command);
	$command = "touch $svgFile";
	$str=exec($command);
	$command = "touch $tableFile";
	$str=exec($command);
	$colorscheme = $_POST['colorScheme'];
	$upperbound = $_POST['rval'];
	$upperboundmax = $_POST['rvalmax'];
	$lowerbound = $_POST['lval'];
	$lowerboundmin = $_POST['lvalmin'];
	$fd = fopen($file, 'w');

	rewind($fd);
	//echo "HERES ORDEREDSUBMIT : $orderedSubmit<br>";
	$arrayidArray = array();
	$arrayDescArray = array();
	if($orderedSubmit != "true"){


				// What chem were selected????
				$chemidarray = array();
				$chemSQL = "SELECT DISTINCT chemid FROM chem ORDER BY chemid";
				$chemResult = mysql_query($chemSQL, $db);
				//echo "<p>$chemSQL</p>";

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
					$clonestr = " cloneid = $cloneid ";
					array_push($cloneContainer, $cloneid);
					array_push($cloneidarray, $clonestr);
					$cloneor = "OR";
					array_push($cloneidarray, $cloneor);
					$cloneCount++;
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
				//echo "<p>$hybSQL</p>";
				$hybResult = mysql_query($hybSQL, $db);
				while($row = mysql_fetch_row($hybResult)){
					$cloneid = $row[1];
					$finalratio = strval($row[2]);
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
			<th class="mainheader" colspan="1">SVG Options</th>
			<th class="mainheader"><!--Save Query?--></th>
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

			<a href="<?php echo "./Instructions/svginstructions.php"; ?>" onClick="return popup(this,'Instructions')">Instructions</a></td>
			<?php
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
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery&savedquery=$savedquery&submit=true";?>"  target="<?php echo "_blank$randnum"; ?>"><!--Update?--></a></td>
				<?php
					}else{
				?>
						<td class="questionanswer">
					<a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>"><!--Save?--></a>
					</td>
				<?php
				}
			}else{
    				if($update == "true" || $update == ""){
			?>
				<td class="questionanswer">
				<a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>"><!--Save?--></a>
				</td>
			<?php
   				}
				else{
			?>
				<td></td>
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
						 echo $lowerbound;
					}
					else{
						echo "[$lowerbound,$lowerboundmin]";
					}


			?></td>
			<td class="questionparameter">
			<a href="<?php echo "./IMAGES/svg$filenum.svgz"; ?>" onClick="return popup(this,'SVG<?php echo $filenum; ?>')">View entire Heat Map</a>
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
			?>
			<td class="questionparameter"></td>
			</tr>

			</table>
			<?php

				$w = $arrayidCount *20 + 500;
				$h = $cloneCount * 10 + 200;
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
				$command = "java -mx512m -jar Cluster3.jar $file $number $arrayidCount $svgFile $algo $tableFile $colorscheme $trxCluster 1";
				echo "$command <br>";
				//$command = "java -jar Cluster3.java";
				$str=passthru($command);

				$command = "gzip --best ./IMAGES/svg$filenum.svg";
				//echo $command;
				$str=exec($command);

				$command = "mv ./IMAGES/svg$filenum.svg.gz ./IMAGES/svg$filenum.svgz";
				$str=exec($command);
				?>
				<embed src="<?php echo "./IMAGES/svg$filenum.svgz" ?>" width=<?php echo $w ?> height=<?php echo $h ?> name="heatmap" type="image/svg+xml" />
				<?php
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
	}
	else{
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
		}
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
				$arraydescSQL = "SELECT arraydesc from array where arrayid = $order ORDER BY arrayid";
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
					$clonestr = " cloneid = $cloneid ";
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
			foreach($arrayidArray as $arrayid){
				//echo "<br>Counter = $counter<br>";
				if($arrayid != -99){
					if($dataset == 1){
					$hybSQL = "SELECT arrayid, cloneid, ROUND(finalratio,1) FROM hybrids where arrayid = $arrayid and ( $cloneidsqlstr )  ORDER BY cloneid";
					//echo "$hybSQL <br>";
					}else{
					$hybSQL = "SELECT arrayid, cloneid, ROUND(finalratio,1) FROM condensedhybrids where arrayid = $arrayid and ( $cloneidsqlstr )  ORDER BY cloneid";
					}
					//echo "<p>$hybSQL</p>";
					$hybResult = mysql_query($hybSQL, $db);
					while($row = mysql_fetch_row($hybResult)){
							$cloneid = $row[1];
							$finalratio = strval($row[2]);
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
				fwrite($fd, $line);
				fflush($fd);
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
						echo $upperbound;
					}
					else{
						echo "[$upperbound,$upperboundmax]";
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
						 echo $lowerbound;
					}
					else{
						echo "[$lowerbound,$lowerboundmin]";
					}


			?></td>
			<td class="questionparameter">
			<a href="<?php echo "./IMAGES/svg$filenum.svgz"; ?>" onClick="return popup(this,'SVG')">View entire Heat Map</a>
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
			if($priv >= 99){
   ?>
			<td class="questionparameter"><strong>Tabular format:</strong></td><td class="questionanswer"><?php echo "<a href=\"./tabledisplay.php?tableNum=$filenum\" target=\"_blank\">TABLE</a>"; ?></td>
			<?php
			}
			?>
			</tr>

			</table>
			<?php

				$w = $arrayidCount *20 + 500;
				$h = $cloneCount * 10 + 200;

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
				$command = "java -mx512m -jar Cluster3.jar $file $number $arrayidCount $svgFile $algo $tableFile $colorscheme $trxCluster 1";
				//echo "$command <br>";
				//$command = "java -jar Cluster3.java";
				$str=passthru($command);

				$command = "gzip --best ./IMAGES/svg$filenum.svg";
				//echo $command;
				$str=exec($command);

				$command = "mv ./IMAGES/svg$filenum.svg.gz ./IMAGES/svg$filenum.svgz";
				$str=exec($command);
				?>
				<embed src="<?php echo "./IMAGES/svg$filenum.svgz" ?>" width=<?php echo $w ?> height=<?php echo $h ?> name="heatmap" type="image/svg+xml" />
				<?php
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


	}


}
else if(isset($_POST['submit']) && $orderingMethod >= 1) {
		if($savedquery != ""){
			// NEED TO UPDATE THE TEMP QUERY......
			reset ($_POST);
			while(list($key, $val) = each ($_POST)){
				if($key != "submit"){
					$query1text .= "$key=$val:";
					//echo "$key=$val<br>";
				}
			}
			$sql = "UPDATE savedqueries SET query1= \"$query1text\" WHERE query=$tempquery";
			//$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($tempquery, $userid, \"$query1text\", NOW())";
			//echo "$sql <br>";
			$sqlResult = mysql_query($sql, $db);

			//echo "This is a saved query...<br>";
			// Need to populate the current query screen....
			$sql = "SELECT queryname, query2, query2opts FROM savedqueries WHERE query = $savedquery AND userid = $userid";
			//echo "$sql<br>";
			$sqlResult = mysql_query($sql, $db);
			$row = mysql_fetch_row($sqlResult);
			$queryname = $row[0];
			$query2 = $row[1];
			$query2opts = $row[2];
			//echo "$query2<br>";
			//echo "$query2opts<br>";
			// NOW need to explode $query2 into an array, the separator is :
			$savedvals = explode(":", $query2);
			// pop the last value of due to final :
			array_pop($savedvals);
			//analyze($savedvals);
			// GET THE OPTIONS...
			$savedoptions = explode(":", $query2opts);

			array_pop($savedoptions);
			//analyze($savedoptions);

		}else{
			// Form is being submitted from the first data input screen....
			// BUT THIS IS USING THE *CUSTOM* ORDERING AND NAMING OF THE ARRAYS....
			// Get the value for this query and then update the table accordingly...
			// Later an option will be given so that the user can save the query....

			// Need to get the max number in the savedqueries table and add one to that,
			// because that is the new number for this query....
			$sql = "SELECT MAX(query) FROM savedqueries";
			$chemResult = mysql_query($sql, $db);
			$row = mysql_fetch_row($chemResult);
			$querynum = $row[0];
			if($querynum == "NULL"){  // a boundary condition... the table is empty.  the other boundary is 10^10... not checking that...
				$querynum = 1;
			}else{
			// increment...
				$querynum += 1;
			}
			// Get the POST values and concatenate them....
			$query1text = "";

			reset ($_POST);
			while(list($key, $val) = each ($_POST)){
				if($key != "submit"){
					$query1text .= "$key=$val:";
					//echo "$key=$val<br>";
				}
			}
			//echo "in query 1 submit section<br>";
			$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($querynum, $userid, \"$query1text\", NOW())";
			//echo "$sql <br>";
			$sqlResult = mysql_query($sql, $db);
		}
	//  Get the arrays select, lay them out in a table and then, for each, create
	// What chem were selected????
	$chemidarray = array();
	$chemSQL = "SELECT DISTINCT chemid FROM chem ORDER BY chemid";
	$chemResult = mysql_query($chemSQL, $db);
	//echo "<p>$chemSQL</p>";

	while($row = mysql_fetch_row($chemResult)){

		// Check to see which boxes were checked...
		$chemid = $row[0];
		$thisVal = "chem$chemid";
		$post = $_POST[$thisVal];
		if($post != ""){
			$chemLookUpSQL = "SELECT chemical FROM chem where chemid = $post";
			$chemLookUpResult = mysql_query($chemLookUpSQL, $db);
			$chemrow = mysql_fetch_row($chemLookUpResult);
			//echo "<p>$chemrow[0] was chosen</p>";
			array_push($chemidarray, $post);
		}
	}


	$trxidarray = array();
	$chemSQL = "SELECT DISTINCT sampleid FROM sampledata ORDER BY sampleid";
	$chemResult = mysql_query($chemSQL, $db);

	while($row = mysql_fetch_row($chemResult)){

		// Check to see which boxes were checked...
		$sampleid = $row[0];
		$sampleid .=a;
		$thisVal = "trx$sampleid";
		$post = $_POST[$thisVal];
		$post = substr("$post", 0, -1);
		if($post != ""){
			$chemLookUpSQL = "SELECT chemical FROM chem where chemid = $post ORDER BY chemid";
			echo $chemLookUPSQL;
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

		$arrayidArray = array();
		$arrayDescArray = array();

		$privval = $_SESSION['priv_level'];

	if($privval == ""){
		$priv = 1;
	}
	else{
		$priv = $privval;
	}


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
	//echo $arraydescSQL;
		$arraydescResult = mysql_query($arraydescSQL, $db);
		$arrayVal = mysql_fetch_row($arraydescResult);
		if($arrayVal != ""){
			//echo "ArrayVal != ''<br>";
			array_push($arrayidArray, $row[0]);
			array_push($arrayDescArray, $arrayVal[0]);
			$descrip = "$arrayVal[0]";
			$descrip .= "\n";
			//fwrite($fd, $descrip);
		}
	}
$length = count($arrayidArray);

?>




<form name="order" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>" onsubmit="return checkOrder(<?php echo $length; ?>)">
<?php
echo "<table class=\"results\">";
?>
<thead>
<tr>
<th class="mainheader" colspan="2">Selected Treatments</th>
<th class="mainheader" >Chosen Order</th>
<th class="mainheader">Custom Treatment Name</th>
<th width="10"></th>
<th class="mainheader">Separator<br>after Group?</th>
</tr>
</thead>
<?php





if($length > 20){
?>
<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td></td>
<td><input type="reset" value="Reset Form"</td>
<td></td>
<td></td>

</tr>

<?php
}
$counter = 0;
//print "Posted variables: <br>";
	reset ($_POST);
	echo "<input name=\"orderedSubmit\" type=\"hidden\" value=\"true\">\n";
	echo "<input name=\"numberOfArrays\" type=\"hidden\" value=\"$length\">\n";
	echo "<input name=\"numberOfGroups\" type=\"hidden\" value=\"$numberGroups\">\n";
 echo "<input name=\"querynum\" type=\"hidden\" value=\"$querynum\">\n";

	while(list($key, $val) = each ($_POST)){

		if($key != "submit"){
		echo "<input name=\"$key\" type=\"hidden\" value=\"$val\">\n";
		}
	}

$colorscheme = $_POST['colorScheme'];
	$upperbound = $_POST['rval'];
	$lowerbound = $_POST['lval'];
$clusterAlgo = $_POST['clusterAlgo'];
// If the ordering method is by individual treatments....
	if($orderingMethod == 1){
	echo "<input name=\"orderedIndividually\" type=\"hidden\" value=\"true\">\n";
		for($i = 0; $i < $length; $i++){
			$val = $i + 1;
			// Create the selection menus.....
			$selectMenu .= "<option value=\"$val\">$val</option>\r";
		}
		foreach($arrayidArray as $idVal){
		echo "<tr>";
		$val = $counter + 1;
		//<input name=\"trxid[$counter]\" type=\"hidden\" value=\"$idVal\">
		echo "<td class=\"questionparameter\">$idVal</td><td class=\"results\">$arrayDescArray[$counter]</td>
			<input name=\"trxidorder$counter\" type=\"hidden\" value=\"$idVal\">
				<td class=\"questionanswer\"><select name='option[$counter]'>
				<option value=\"$val\" selected>$val</option>\r;
					$selectMenu
			</select></td>";
		echo "</tr>";
		$counter++;
		}
		$counter=0;
	}
	else{
	
	$innercolor = array("lightsteelblue", "DarkKhaki", "salmon", "DarkSeaGreen", "Gainsboro",
			"yellow", "Fuchsia", "LawnGreen", "LightSlateGray", "Olive", "Indigo",
			"PaleVioletRed", "skyblue", "PeachPuff", "Orange", "GoldenRod", "oldlace",
			"pink", "RosyBrown", "green","lightsteelblue","YellowGreen", "salmon",
			"Turquoise", "Thistle", "Peru", "WhiteSmoke");


		echo "<input name=\"orderedIndividually\" type=\"hidden\" value=\"false\">\n";
		// We've got the length and the number of groups....
		if($length <= $numberGroups){
			$numberGroups = $length;
		}
		if($_GET['savedquery'] == "" || $savedquery == ""){
			$isSavedQuery = "";
		
		}else{
				$isSavedQuery = "true";
			}

		foreach($arrayidArray as $idVal){
			echo "<tr>";
			$val = $counter + 1;
			/*
			//#################################################33
			*/
			// Do we've any name changes here??????
			$savedName = "";
			if($isSavedQuery != ""){
				foreach($savedvals as $nameval){
					$temp = explode("=", $nameval);
					$customname = "customname$idVal";
					if($temp[0]== $customname){
						$savedName = $temp[1];
						break;
					}
				}
			}
			if($savedName == ""){
				$savedName = $arrayDescArray[$counter];

			}
			$selectMenu = "";
			// #################################################
			if($_GET['savedquery'] == "" || $savedquery == ""){
				//echo "This is not a saved query...";

				for($i = 0; $i < $numberGroups; $i++){
					$aval = $i + 1;
					// Create the selection menus.....
					$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$aval</option>\r";
				}
			}
			else{
				$isSavedQuery = "true";
				// What group is it in.... REBUILDING SELECT MENU....
				// Is this an id we had before
				$selectMenu = "";
				$selectValue = "";
				foreach($savedoptions as $arrayval){
					//echo "ARRAYVAL = $arrayval<br>";
					$temp = explode("=", $arrayval);
					//echo "temp[0] = $temp[0] idVal = $idVal temp[1]=$temp[1]<br>";
					if($temp[0] == $idVal){
						$optVal = $temp[1];
							//echo "valC = $valC and optVal = $optVal<br>";
							$selected = "selected";
							if($optVal > $numberGroups){
								// If the number of groups is updated and is less than the previous value, set to the number of groups...
								$optVal = $numberGroups;
							}
							$selectMenu = "<option value=\"$optVal\" $selected style=\"background-color: $innercolor[$optVal];\">$optVal</option>\r";
							$selectValue = $optVal;
						break;
					}

				}
				for($i = 0; $i < $numberGroups; $i++){
						$aval = $i + 1;
						if($selectValue != $val){
						// Create the selection menus.....
						$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$aval</option>\r";
						}
					}

			}
			

			// Do we've any groups selected and what group is this trx in???????????
			$savedgroup = "";
			if($isSavedQuery != ""){
				foreach($savedvals as $nameval){
					$temp = explode("=", $nameval);
					$customname = "customname$idVal";
					if($temp[0]== $customname){
						$savedName = $temp[1];
						break;
					}
				}

			}
			if($savedName == ""){
				$savedName = $arrayDescArray[$counter];

			}

			echo "<td class=\"questionparameter\">$idVal</td><td class=\"results\">$savedName</td>
			<input name=\"trxidorder$counter\" type=\"hidden\" value=\"$idVal\">

					<td class=\"questionanswer\"><select name='option[$idVal]' >
						$selectMenu
				</select></td>
				<td class=\"results\">
			<input name=\"customname$idVal\" type=\"text\" value=\"$savedName\" size=\"25\" align=\"right\"></td><td width=\"10\"></td>";
			//echo "val = $val  NumberOfGroups=$numberGroups<br>";

			if($val < $numberGroups){

				$group = $val;
				$isChecked = "";
				if($isSavedQuery != ""){
					foreach($savedvals as $nameval){
						$temp = explode("=", $nameval);
						$customname = "group$group";
						if($temp[0]== $customname){
							$isChecked = "checked";
							break;
						}
					}
				}


				echo "<td class=\"questionanswer\"><input type=\"checkbox\" name=\"group$group\" value=\"$group\" $isChecked>Group $group</td>";
			}
			else{
				echo "<td></td>";
			}
			if($isSavedQuery != ""){
				$selectMenu = "";
			}
			echo "</tr>";
			$counter++;
		}
		$counter=0;


	}

?>
<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td></td>
<td><input type="reset" value="Reset Form"</td>
<td></td>

</tr>
<?php
echo "</table>";
echo "</form>";
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
}
else{// GETTING THE VALUES TO POPULATE THE SELECTIONS.....
// THIS ONE IS FOR THE PARTICULAR TREATMENTS....

$privval = $_SESSION['priv_level'];

if($privval == ""){
	$priv = 1;
}
else{
	$priv = $privval;
}
// This is the sql required to get the list of chemicals...
//$chemSQL = "SELECT DISTINCT chemid, chemical, trx_type FROM chem ORDER BY chemid";
if($priv != 99){
$chemSQL = "SELECT DISTINCT s.chemid, c.chemical, c.trx_type FROM array AS a, sampledata AS s, chem AS c
		WHERE (a.ownerid = $priv OR a.ownerid = 1) AND s.sampleid = a.arrayid AND c.chemid = s.chemid
		ORDER BY s.chemid";
}
else{
 	$chemSQL = "SELECT DISTINCT chemid, chemical, trx_type FROM chem ORDER BY chemid";
}

//#############ARE WE DEALING W/ A SAVED QUERY????############################
	if($_GET['savedquery'] != ""){
	
		// CREATE A TEMP QUERY TO STORE THE UPDATED SAVED QUERY!!!!!!
		//############################################################
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Need to get the max number in the savedqueries table and add one to that,
			// because that is the new number for this query....s
			$sql = "SELECT MAX(query) FROM savedqueries";
			$sqlResult = mysql_query($sql, $db);
			$row = mysql_fetch_row($sqlResult);
			//if($tempquery != ""){
				$tempquery = $row[0];
			//}
			if($tempquery == "NULL"){  // a boundary condition... the table is empty.  the other boundary is 10^10... not checking that...
				$tempquery = 1;
			}else{
			// increment...
				$tempquery += 1;
			}
			// Get the POST values and concatenate them....
			$query1text = "";

			reset ($_POST);
			while(list($key, $val) = each ($_POST)){

				if($key != "submit"){
						// Probably don't want and don't need the submit value....
						$query1text .= "$key=$val:";
						//echo "$key=$val<br>";
					}


			}
			//echo "in query 1 TEMPORARY submit section<br>";
			$sql = "INSERT savedqueries (query, userid, query1, querydate) VALUES($tempquery, $userid, \"$query1text\", NOW())";
			$sqlResult = mysql_query($sql, $db);
			//echo "$sql <br>";
			//echo "<br>#################END TEMP QUERY##################################<br>";
		//############################################################
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

		// Since $savedquery is the number of the query, we need to get the information for that query....
		// Specifically, is this only a one page or a two pager?
		$sql = "SELECT queryname, query1, query2opts FROM savedqueries WHERE query = $savedquery AND userid = $userid";
		$sqlResult = mysql_query($sql, $db);
		$row = mysql_fetch_row($sqlResult);
		$queryname = $row[0];
		$query1 = $row[1];
		$query2opts = $row[2];
		if($query2opts != "NULL"){
			// this is a two pagers...
			$is2pager = 1;
		}else{
			$is2pager = 0;
		}

		// NOW need to explode $query1 into an array, the separator is :
		$vals = explode(":", $query1);
		// pop the last value of due to final :
		array_pop($vals);
		//analyze($vals);
		// This is used to store the chem numbers....
		$savedvals = array();
		$savedchemvals = array();
		$savedtrxvals = array();
		foreach($vals as $val){
			$temp = explode("=", $val);
			$findme  = 'chem';
			$pos = strpos($temp[0], $findme);

			// Note our use of ===.  Simply == would not work as expected
			// because the position of 'a' was the 0th (first) character.
			if ($pos === false) {
				$savedvals[$temp[0]]=$temp[1];
				// Now need to check to see if we're dealing w/ an individual treatment....
				$findme  = 'trx';
				$pos = strpos($temp[0], $findme);
				if($pos === false){
					$savedvals[$temp[0]]=$temp[1];
				}
				else{
					// check for exception....
					if($temp[0] == "trxCluster"){
						$savedvals[$temp[0]]=$temp[1];
					}
					else{
						array_push($savedtrxvals, $temp[1]);
					}

				}
			} else {
				if($temp[0] == "colorScheme"){
					$savedvals[$temp[0]]=$temp[1];
				}
				else{
					array_push($savedchemvals, $temp[1]);
				}
			}
			//echo "$temp[0]=>$temp[1]";
			//array_push($savedvals, $temp[0]=>$temp[1]);
		}

		//echo "<br>here's savedvals<br>";
		//analyze($savedvals);
		//echo "<br>here's savedchemvals<br>";
		//analyze($savedchemvals);
		//echo "<br>here's savedtrxvals<br>";

		//array_push($savedtrxvals, -1);
		//analyze($savedtrxvals);
	}else{
		// This is not a saved query.....
		// because that is the new number for this query....
			$sql = "SELECT MAX(query) FROM savedqueries";
			$sqlResult = mysql_query($sql, $db);
			$row = mysql_fetch_row($sqlResult);
			//if($tempquery != ""){
				$tempquery = $row[0];
			//}
			if($tempquery == "NULL"){  // a boundary condition... the table is empty.  the other boundary is 10^10... not checking that...
				$tempquery = 1;
			}else{
			// increment...
				$tempquery += 1;
			}

	}


$chemResult = mysql_query($chemSQL, $db);
$chemCount = 1;
$envCount = 1;

while(list($chemid, $chemical, $trx_type) = mysql_fetch_array($chemResult))
{
	$checked = "";
	if($_GET['savedquery'] != ""){
		foreach($savedchemvals as $chemval){
			if($chemval == $chemid){
				$checked = "checked";
			}
		}
	}
   // $chemMenu .= "<option value=\"$chemid\">$chemical</option>\r";
	if($trx_type == 0){
		if($chemCount%4 == 0){
			$chemMenuChem .= "$chemical  <input type=\"checkbox\" name=\"chem$chemid\" value=\"$chemid\" $checked><br>";
			//
			$chemCount=0;
			$chemCount++;
		}
		else{
			$chemMenuChem .= "$chemical  <input type=\"checkbox\" name=\"chem$chemid\" value=\"$chemid\" $checked>";
			$chemCount++;
		}

	}else{ // $trx_type != 0 and it's an environmental condition....
		if($envCount%2 == 0){
			$chemMenuEnv .= "$chemical  <input type=\"checkbox\" name=\"chem$chemid\" value=\"$chemid\" $checked><br>";
			$envCount++;
		}
		else{
			$chemMenuEnv .= "$chemical  <input type=\"checkbox\" name=\"chem$chemid\" value=\"$chemid\" $checked>";
			$envCount++;
		}
	}

}




// Need to create an array to store the divs based on class.....
$classSQL = "SELECT COUNT(DISTINCT class) FROM chemclass";
$classResult = mysql_query($classSQL, $db);
$row = mysql_fetch_row($classResult);
$numDivs = $row[0];
$divVal = $numDivs - 1;
$divArray = array();


$chemIDArray = array();
$classIDArray = array();
$classSQL = "SELECT chemid, class FROM chemclass ORDER BY class";
$classResult = mysql_query($classSQL, $db);
while($row = mysql_fetch_row($classResult)){
	$thisChemID = $row[0];
	array_push($chemIDArray, $thisChemID);
	$thisClassID = $row[1];
	array_push($classIDArray, $thisClassID);
}

$classSQL = "SELECT DISTINCT(class.classid), class.name FROM class,chemclass WHERE chemclass.class = class.classid ORDER BY class.name ASC";
$classResult = mysql_query($classSQL, $db);
$maxClassID = 0;
$i = 0;
while(list($classid, $name) = mysql_fetch_array($classResult)){

	$uniqueClassArray[$i] = $classid;
	if($i == 0){
			$divArray[$i][0] = "<div style=\"display: block;\" id=\"section$i\">";
	}
	else{
		$divArray[$i][0] = "<div style=\"display: none;\" id=\"section$i\">";
	}

	$chemDivList .= "<li><a href=\"#indiv\" onclick=\"show_div('section$i',$divVal); return false;\" tabindex=\"$i\">$name</a></li>";
	$i++;

}

// The following variable is used to keep track of what div were at....
$i = 0;
foreach($uniqueClassArray as $thisClassID){
	$acounter = 1;
	//echo "Creating div[$i]....<br>";
	// Get all of the chemids associated w/ the current class...
	$sql = "SELECT chemid FROM chemclass WHERE class = $thisClassID";
	//echo "$sql <br>";
	$result = mysql_query($sql, $db);
	while($row = mysql_fetch_row($result)){
		//echo "\tchemid = $row[0]...<br>";
		// GET all entries from sampledata that correspond to this chemid....
		// Order by arrayid
		$id = $row[0];

	if($priv != 99){
		$chemSQL = "SELECT  DISTINCT ( s.sampleid ),  s.treatment, s.chemid, a.arraydesc, c.trx_type
		FROM sampledata AS s, array AS a, chem as c
		WHERE  (a.ownerid = $priv OR a.ownerid = 1) AND s.chemid = $id AND s.sampleid = a.arrayid AND c.chemid = s.chemid
		ORDER  BY a.arraydesc";
	}
	else{
		$chemSQL = "SELECT  DISTINCT ( s.sampleid ),  s.treatment, s.chemid, a.arraydesc, c.trx_type
		FROM sampledata AS s, array AS a, chem as c
		WHERE s.chemid = $id AND s.sampleid = a.arrayid AND c.chemid = s.chemid
		ORDER  BY a.arraydesc";
	
	}
		$chemResult = mysql_query($chemSQL, $db);
		$arraycount = 1;
		$oldtreat = "";
		while(list($trxid1, $treat, $chemid1, $trxdesc1, $type) = mysql_fetch_array($chemResult)){
			if($acounter == 1){
				// need to set the first treatment type...
				$oldtreat = $treat;
				$divArray[$i][$acounter] = "<fieldset><legend>$treat</legend>";
				$acounter++;
			}
			if($oldtreat != $treat){
				$oldtreat = $treat;
				$divArray[$i][$acounter] = "</fieldset>";
				$acounter++;
				$divArray[$i][$acounter] = "<fieldset><legend>$treat</legend>";
				$acounter++;
			}
			$trxid1 .= "a";

			$trxMenu = "$trxdesc1  <input type=\"checkbox\" name=\"trx$trxid1\" value=\"$trxid1\" $checked>  ";
			// The following conditional is put in place, because the treatment descriptions for TCDD optimization and Thioacetamide are too long...
			if($thisClassID != 19 && $thisClassID != 15){
				if($arraycount%4 == 0){
					$trxMenu .="<br>";
				}
			}
			else{
				if($arraycount%2 == 0){
					$trxMenu .="<br>";
				}
			}
			$divArray[$i][$acounter] = $trxMenu;
			$acounter++;
			$trxMenu = "";
			$arraycount++;
		}

		$acounter++;
	}
	$divArray[$i][$acounter] = "</div>";
	$i++; // increment the divArray counter....

}


?>
<p class="styletext">
<form name="query" method="post" onsubmit="return checkClusteringSelectClonesForm()" action="<?php  $_SERVER['PHP_SELF'] ?>">

<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2">Query Parameters</th>
<th class="mainheader" ><a href="<?php echo "./Instructions/clustering.php"; ?>"  onclick="return popup(this,'Instructions')"><font size="0">Instructions?</font></a></th>
</tr>
<tr>
<td  class="questionanswer" colspan="2"><strong>Cluster By:</strong></td>
<td  class="questionanswer"><strong><!--Your Query Options:--></strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Clustering Method:</strong></td>
<td class="results">
<?php



	// what algo is checked???
	if($_GET['savedquery'] != ""){

		if($savedvals['clusterAlgo'] == 1){
			$hierChecked = "checked";
			$kChecked = "";
		}
		else{
			$hierChecked = "";
			$kChecked = "checked";
		}
	}
	else{
		$hierChecked = "checked";
		$kChecked = "";
	}
?>
<input type="radio" name="clusterAlgo" value="1" <?php echo $hierChecked; ?> onclick="return hideTrxRow(0)"> Hierarchical<br>
<input type="radio" name="clusterAlgo" value="0" <?php echo $kChecked; ?> onclick="return hideTrxRow(1)">K-Means<br>
</td>
<td align="top" class="results" rowspan="2">
<!--
<ul id="globalnav">
	<li><a href="#indiv" onclick="show_querydiv('querysection0',2); return false;" tabindex="0">Recent Queries</a></li>
	<li><a href="#indiv" onclick="show_querydiv('querysection1',2); return false;" tabindex="1">Saved Queries</a></li>
</ul>
<br>
<p>
<div style="display: block;" id="querysection0" class="scroll">
-->
<?php
/*
	// GET THE THREE MOST RECENT QUERIES.....
	$sql = "SELECT query FROM savedqueries WHERE userid = $userid AND (queryname IS NULL AND (query1 IS NOT NULL AND query1 != \"\"))  ORDER BY querydate DESC LIMIT 3";
	$sqlResult = mysql_query($sql, $db);
	$recentCount=1;
	while($row = mysql_fetch_row($sqlResult)){
		echo "<a href=\"./clustering.php?savedquery=$row[0]\">Unsaved #$recentCount</a><br>";
		$recentCount++;
	}
*/
?>
<!--
</div>
<div  style="display: none;" id="querysection1" class="scroll">
<?php
/*
	// GET THEIR SAVED QUERIES.....
	$sql = "SELECT query, queryname FROM savedqueries WHERE userid = $userid AND queryname IS NOT NULL ORDER BY querydate DESC";
	$sqlResult = mysql_query($sql, $db);
	while($row = mysql_fetch_row($sqlResult)){
		echo "<a href=\"./clustering.php?savedquery=$row[0]\">$row[1]</a><br>";
	}
*/
?>
</div>
<br>

</p>
-->
</td>
</tr>

<?php
	// IF THIS IS A SAVED QUERY, WE'VE GOT TO HAVE A VALUE FOR THIS..
	echo "<input name=\"savedquery\" type=\"hidden\" value=\"$savedquery\">\n";
	// IF A TEMP query's involved, gotta have that....
	echo "<input name=\"tempquery\" type=\"hidden\" value=\"$tempquery\">\n";


	if($priv >=99){
?>

<?php
	// what dataset is checked???
	if($_GET['savedquery'] != ""){

		if($savedvals['dataset'] == 1){
			$notcondChecked = "checked";
			$condChecked = "";
		}
		else{
			$notcondChecked = "";
			$condChecked = "checked";
		}
	}
	else{
		$notcondChecked = "checked";
		$condChecked = "";
	}
?>

	<tr>
<td class="questionparameter" ><strong>Data Options:</strong></td>
<td class="results">
<input type="radio" name="dataset" value="0" checked><strong><font color="red">Condensed</font></strong><br>
<input type="radio" name="dataset" value="1">Not Condensed<br>

</td>

</tr>
<?php
	}
	
	else{
?>
		<tr>
<td class="questionparameter" ><strong>Data Options:</strong></td>
<td class="results">
Using All Clones
</td>
<?php
	}
?>

<?php
	// what dataset is checked???
	if($_GET['savedquery'] != ""){
		$kval = $savedvals['number'];
	}
	else{
		$kval = 4;
	}
?>
<tr id="kmeansoption">
<td class="questionparameter"><strong>Number of Clusters:</strong></td>
<td class="results">
<input name="number" type="text" value="<?php echo $kval; ?>" size="5" align="right">
</td>
<td class="results"><br></td>
</tr>

<tr id="hierarchicaloption">
<td class="questionparameter" ><strong>Cluster Treatments?:</strong></td>
<td class="results">
<?php
// what cluster option is checked???
	if($_GET['savedquery'] != ""){

		if($savedvals['trxCluster'] == 1){
			$clusterChecked = "checked";
			$noclusterChecked = "";
		}
		else{
			$clusterChecked = "";
			$noclusterChecked = "checked";
		}
	}
	else{
		$clusterChecked = "checked";
		$noclusterChecked = "";
	}
?>
<input type="radio" name="trxCluster" value="1" <?php echo $clusterChecked; ?> onclick="return hideOrderRows(0)">Cluster Treatments</input><br>
<?php
/*
<input type="radio" name="trxCluster" value="2" onclick="return hideOrderRows(0)">Cluster, top dendrogram only</input><br>
<input type="radio" name="trxCluster" value="3" onclick="return hideOrderRows(0)">Cluster, bottom dendrogram only</input><br>
*/
?>
<input type="radio" name="trxCluster" value="0" <?php echo $noclusterChecked; ?> onclick="return hideOrderRows(1)">Custom Order/Name Treatments (No Clustering)</input><br>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Allows you to select whether the <br>treatments should be clustered<br> or manually ordered and/or named..
</td>
</tr>

<tr id="orderoption0">
<td  class="questionanswer" colspan="3"><strong>Ordering Of Selections</strong></td>
</tr>
<tr id="orderoption1">
<?php
// what cluster option is checked???
	if($_GET['savedquery'] != ""){

		if($savedvals['orderoptions'] == 0){
			$defaultChecked = "checked";
			$nodefaultChecked = "";
		}
		else{
			$defaultChecked = "";
			$nodefaultChecked = "checked";
		}
	}
	else{
		$defaultChecked = "checked";
		$nodefaultChecked = "";
	}
?>
<td class="questionparameter"><strong>Ordering/Naming Options:</strong></td>
<td class="results">
<input type="radio" name="orderoptions" value="0" <?php echo $defaultChecked; ?> onclick="return hideNumGroups(0)">Default Ordering (by Array ID) w/o Custom Names<br>
<?php
//<input type="radio" name="orderoptions" value="1" onclick="return hideNumGroups(0)">Individually Order/Name Selections<br>

?>
<input type="radio" name="orderoptions" value="2" <?php echo $nodefaultChecked; ?> onclick="return hideNumGroups(1)">Custom Order/Name
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>This option gives you the ability to order<br> your Chemical(s)/Condition(s) selections on a subsequent <br>screen.
The number of groups entered will allow you to allot <br>the treatments to separate groups, thereby segregating them<br> based on your
own discretion.
</td>
</tr>

<tr id="ordergroups1">
<td class="questionparameter"><strong>Number of Ordered Groups:</strong></td>
<td class="results">
<?php
	// what dataset is checked???
	if($_GET['savedquery'] != ""){
		$oval = $savedvals['numberGroups'];
	}
	else{
		$oval = 4;
	}
?>
<input name="numberGroups" type="text" value="<?php echo $oval; ?>" size="3" align="right">
</td>
<td class="results"></td>
</tr>

<tr>
<td  class="questionanswer" colspan="3"><strong>Selection Options</strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Selection Options:</strong></td>
<td class="results">
<?php
	// what dataset is checked???
	if($_GET['savedquery'] != ""){
		$oval = $savedvals['seloptions'];
		if($oval == 1){
			$chemcon = "checked";
			$indiv = "";
			$both = "";
		}
		else if($oval == 0){
			$chemcon = "";
			$indiv = "checked";
			$both = "";
		}
		else{
			$chemcon = "";
			$indiv = "";
			$both = "checked";
		}
	}
	else{
		$chemcon = "checked";
		$indiv = "";
		$both = "";
	}
?>
<input type="radio" name="seloptions" value="1" <?php echo $chemcon; ?> onclick="return hideSelsRow(0) "> Chemical(s)/Condition(s) Group<br>
<input type="radio" name="seloptions" value="0" <?php echo $indiv; ?> onclick="return hideSelsRow(1) ">Individual Chemical(s)/Condition(s)<br>
<input type="radio" name="seloptions" value="2" <?php echo $both; ?> onclick="return hideSelsRow(2) "> Both Options<br>

</td>
<td class="results">

</td>
</tr>


<tr id="groupoption1">
<td  class="questionanswer" colspan="3"><strong>Chemical(s)/Condition(s)</strong></td>
</tr>

<tr id="groupoption3">
<td class="questionparameter" colspan="2">
<fieldset align="top">
  <legend>Chemical Treatments</legend>
  <?php echo $chemMenuChem; ?>
</fieldset>

</td>
<td class="questionparameter" align="top">
<fieldset>
  <legend>Conditions/Vehicle Treatments</legend>
<?php echo $chemMenuEnv; ?>
</fieldset>
</td>
</tr>
<tr id="individualoption1">
<td  class="questionanswer" colspan="3"><strong>Individual Chemical(s)/Condition(s)</strong></td>
</tr>

<tr id="individualoption2">
<td class="questionparameter" colspan="3">
<div id="navcontainer">
<a name="#indiv"></a>
<ul id="globalnav">
	<?php echo $chemDivList; ?>
</ul>
</div>
<p><br></p>

<?php 
	//echo $trxMenu;
	foreach($divArray as $divArrayItem){
		foreach($divArrayItem as $anItem){
			$val = $anItem;
			echo "$val\n";
		
		}

	}




?>

</td>
</tr>
<tr>
<td  class="questionanswer" colspan="3"><strong>Heat Map Options</strong></td>
</tr>

<tr>
<td class="questionparameter" ><strong>Heat Map Color Scheme:</strong></td>
<td class="results">
<?php
// what colorscheme is checked???
if($_GET['savedquery'] != ""){

		if($savedvals['colorScheme'] == 0){
			$gr = "checked";
			$yb = "";
		}
		else{
			$gr = "";
			$yb = "checked";
		}
	}
	else{
		$gr = "checked";
			$yb = "";
	}
?>
<input type="radio" name="colorScheme" <?php echo $gr; ?> value="0"><font color="red"><strong>Red</font>/<font color="green">Green</font></strong><br>
<input type="radio" name="colorScheme" <?php echo $yb; ?> value="1"><font color="yellow"><strong>Yellow</font>/<font color="blue">Blue</font></strong><br>
</td>
<td class="results">
</td>
</tr>


<tr>
<td  class="questionanswer" colspan="3"><strong>Clone List:</strong></td>
</tr>
<tr>
	<td class="questionparameter"><strong>Selected Clones:</strong></td>
	<td class="results"><textarea  name="cloneList" rows="10" cols="10"></textarea></td>
	<td class="results">
	<font color="red"><b>NOTE: </b></font>Delimit the clone ids by comma <br>or by entering one cloneid per line.

	</td>
</tr>

<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td><input type="reset" value="Reset Form"</td>
</tr>

</table>

</form>
</p>
<?php


}


?></div>
 </div>
 <?php
	include 'leftmenu.inc';

?>
 <div class="boxclear"> </div>
 <div class="boxclear"> </div>
 <div class="boxfooter"></div>
</body>
</html>
