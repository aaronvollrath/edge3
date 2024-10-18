<div dojoType="dijit.layout.ContentPane" style="height: 1800px;">

<?php
$userid = $_SESSION['userid'];

$debug = 0;  // set to 1 for debug messages as long as the userid matches debugid....
$debugid = 1;
if($userid == $debugid && $debug == 1){
echo "debug mode: <br>";
analyze($_SESSION);
analyze($_POST);
}
$browserval = 1;
if(!isset($_POST['orderedSubmit'])){
#if(!isset($orderedSubmit)){
	$orderedSubmit = false;
}else{
	$orderedSubmit = $_POST['orderedSubmit'];
}
if(isset($_GET['savedquery'])){
	$savedquery = $_GET['savedquery'];
}else{
	$savedquery = "";
}

if (isset($_POST['submit']) && $orderingMethod == 0 || $orderedSubmit == "true") { // if form has been submitted and it is not being ordered or orderedSubmit is true...
		if($userid == 247){
			analyze($_POST);
		}
		if($orderedSubmit == "true"){
			if($userid == 247){
				analyze($_POST);
			}
			if($savedquery == ""){  // If this is not a saved query
			if($userid == 247){
				echo "This is not a saved query and orderedSumbit == true......";
			}
				$thisNum = $_POST['querynum'];
			}else{
			if($userid == 247){
   				echo "This is a saved query... UPDATING TEMP...<BR>";
				analyze($_POST);
  			 }
				$thisNum = $savedquery; //$_POST['tempquery'];

			}

			// Form is being submitted from the second data input screen....
			// Get the value for this query and then update the table accordingly...
			// Later an option will be given so that the user can save the query....
			//$querynum = $querynum;
			// Get the POST values and concatenate them....
			$query2text = "";
			$query2optstext = "";
			// Put the array pointer at the beginning of the $_POST array...
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

			if($userid==247){
				echo "in query 2 submit section<br>";
			}
			$sql = "UPDATE savedqueries SET query2 = \"$query2text\" WHERE query=$thisNum";
			$sqlResult = $db->Execute($sql);
			if($userid==247){
				echo "$sql <br>";
			}
			$sql = "UPDATE savedqueries SET query2opts = \"$query2optstext\" WHERE query=$thisNum";
			$sqlResult = $db->Execute($sql);
			if($userid==247){
				echo "$sql <br>";
			}



		}else{

			if($savedquery == ""){  // If this is not a saved query
				if($userid==$debugid && $debug==1){
					echo "This is not a saved query line 76......<BR>";
				}
				// Need to get the max number in the savedqueries table and add one to that,
				// because that is the new number for this query....
				if($tempquery == ""){
					$sql = "SELECT MAX(query) FROM savedqueries";
					if($userid==247){
					//echo "$sql<BR>";
					}
					$sqlResult = $db->Execute($sql);
					$row = $sqlResult->FetchRow();
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
				if($savedquery != ""){
					$tempquery = $savedquery;
				}else{
					$tempquery = $_POST['tempquery'];

				}

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
				/*if($_GET['tempquery'] == ""){
					$tempquery = $savedquery; //$_POST['tempquery'];
				}else{
					$tempquery = $_POST['tempquery'];

				}*/
			$tempquery = $thisNum;
			$sql = "SELECT query FROM savedqueries WHERE query = $tempquery";
			if($userid == 247){
				echo "$sql<br>";
			}
			$sqlResult = $db->Execute($sql);
			$row = $sqlResult->FetchRow();
			if($row[0] != ""){
				$sql = "UPDATE savedqueries SET query1= \"$query1text\" WHERE query=$tempquery";
				$sqlResult = $db->Execute($sql);
				//echo "$sql<br>";
			}else{
				$sql = "INSERT savedqueries (query, userid, query1, querydate) VALUES($thisNum, $userid, \"$query1text\", NOW())";
				$sqlResult = $db->Execute($sql);
				//echo "$sql <br>";
			}
		}


		if($dataset == ""){
			$dataset = 1;
		}
		//echo "tempquery # : $tempquery<br>";
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
	$file = "/var/www/IMAGES/data$filenum.txt";
	$svgFile = "/var/www/IMAGES/svg$filenum.svg";
	$tableFile = "/var/www/IMAGES/table$filenum";
	$csvFile = "/var/www/IMAGES/$filenum.csv";
	$command = "touch $csvFile";
	$str=exec($command);
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
	//analyze($_POST);
	// NEED TO CONVERT THESE VALUES TO LOG BASE 10....
	include 'convertthresholdvalues.inc';

	$fd = fopen($file, 'w');
	$csvfd = fopen($csvFile, 'w');
	rewind($fd);
	rewind($csvfd);
	$arrayidArray = array();
	$arrayDescArray = array();
	if($orderedSubmit != "true"){
		include './phpinc/clusteroutputnorderingoftreatmentssinglechanneledge3.inc';
	}
	else{
		/***********************************************************************************
		ORDERED SECTION...............
		***********************************************************************************/
		include './phpinc/clusteroutputORDERINGoftreatmentssinglechanneledge3.inc';
	}
}
else if(isset($_POST['submit']) && $orderingMethod >= 1) {
//analyze($_POST);
if($userid == 1){

echo "ordering...<br>";

}
		if($savedquery != ""){
			// NEED TO UPDATE THE TEMP QUERY......
			if($userid == 247){
				echo "<hr>this is a saved query<br>";
			}
			reset ($_POST);
			while(list($key, $val) = each ($_POST)){
				if($key != "submit"){
					$query1text .= "$key=$val:";
					//echo "$key=$val<br>";
				}
			}
			$sql = "UPDATE savedqueries SET query1= \"$query1text\" WHERE query=$tempquery";
			//$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($tempquery, $userid, \"$query1text\", NOW())";
			if($userid == 247){
				echo "$sql <br>";
			}
			$sqlResult = $db->Execute($sql);

			//echo "This is a saved query...<br>";
			// Need to populate the current query screen....
			$sql = "SELECT queryname, query2, query2opts FROM savedqueries WHERE query = $savedquery AND userid = $userid";
			if($userid == 247){
				echo "$sql<br>";
			}
			$sqlResult = $db->Execute($sql);
			$row = $sqlResult->FetchRow();
			$queryname = $row[0];
			$query2 = $row[1];
			$query2opts = $row[2];
			if($userid == 247){
				echo "$query2<br>";
				echo "$query2opts<br>";
			}
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
			if($userid == 247){
				echo "this is NOT a saved query<br>";
			}
			// Form is being submitted from the first data input screen....
			// BUT THIS IS USING THE *CUSTOM* ORDERING AND NAMING OF THE ARRAYS....
			// Get the value for this query and then update the table accordingly...
			// Later an option will be given so that the user can save the query....

			// Need to get the max number in the savedqueries table and add one to that,
			// because that is the new number for this query....
			$sql = "SELECT MAX(query) FROM savedqueries";
			$chemResult = $db->Execute($sql);
			$row = $chemResult->FetchRow();
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
			if($userid == 247){
				echo "$sql <br>";
			}
			$sqlResult = $db->Execute($sql);
		}

?>




<form name="order" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>" onsubmit="return checkOrder(<?php echo $length; ?>)">
<?php
echo "<table class=\"results\">";
?>
<thead>
<tr>
<!--<th class="mainheader" colspan="2">Selected Treatments</th>-->
<th class="mainheader" colspan="1">Selected Treatments</th>
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
	//echo "yes method=1";
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
		echo
			"<td class=\"questionparameter\">$idVal
			</td><td class=\"results\">$arrayDescArray[$counter]</td>
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
	// Changed line below at 20:27 on 17NOV2007
	$length = $numberGroups;
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
		if($savedquery == "" || $savedquery == ""){
			$isSavedQuery = "";

		}else{
				$isSavedQuery = "true";
			}
//analyze($_POST);
// Need to populate the $arrayidarray....

	$arrayidarray = array();
	$arraydescarray = array();
	//$arrayidSQL = "SELECT DISTINCT arrayid,arraydesc FROM agilent_arrayinfo where arraytype = 0 ORDER BY arrayid";
	$arrayidSQL = "SELECT DISTINCT arrayid,arraydesc FROM agilent_arrayinfo ORDER BY arrayid";
	$arrayidResult = mysql_query($arrayidSQL, $db);
	if($userid == 1){
		echo "$arrayidSQL<br>";

	}
	while($row = mysql_fetch_row($arrayidResult)){
	// Check to see which boxes were checked...
		$sampleid = $row[0];
		//$sampleid .=a;
		$thisVal = "array$sampleid";
		//echo "thisval =$thisVal<br>";
		$post = $_POST[$thisVal];
		//$post = substr("$post", 0, -1);
		//echo "postval = $post<br>";
		if($post > 0){
			array_push($arrayidarray,$post);
			array_push($arraydescarray, $row[1]);
		}
	}


		foreach($arrayidarray as $idVal){
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
				$savedName = $arraydescarray[$counter];

			}
			$selectMenu = "";
			// #################################################
			if($savedquery == "" || $savedquery == ""){
				//echo "This is not a saved query...";

				$numberGroups = $_POST[numberGroups];
				for($i = 0; $i < $numberGroups; $i++){
					$aval = $i + 1;
					//echo "a value=$aval<br>";
					// Create the selection menus.....
					$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$aval</option>\r";
				}
			}
			else{
				$isSavedQuery = "true";
				// What group is it in.... REBUILDING SELECT MENU....
				// Is this an id we had before
				if($userid == 1 || $userid == 247){
					//echo "We need to order by the previous saved query<br>";
				}
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

			// no need to display treatment id to user... hence deleted.
			//echo "<td class=\"questionparameter\">$idVal</td>
			//echo "<td class=\"questionparameter\"></td>
			//echo "<td class=\"results\"></td>
			echo "
			<td class=\"results\">$savedName</td>
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


				echo "<td class=\"questionanswer\"><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\" name=\"group$group\" value=\"$group\" $isChecked>Group $group</td>";
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


//analyze($_SESSION);
	require('./phpinc/commonclusteringsavequerycode.inc');





?>
<p class="styletext">
<form name="query" method="post" onsubmit="return checkClusteringForm()" action="<?php  $_SERVER['PHP_SELF'] ?>">

<table class="question" width="700px">
<thead>
<tr>
<th class="mainheader" colspan="2"><font color="black">Query Parameters</font></th>
<th class="mainheader" ><a href="<?php echo "./Instructions/clustering.php"; ?>"  onclick="return popup(this,'Instructions')"><font size="0">Instructions?</font></a></th>
</tr>
<tr>
<td  class="questionanswer" colspan="2"><strong>Cluster By:</strong></td>
<td  class="questionanswer"><strong>Your Query Options:</strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Clustering Method:</strong></td>
<td class="results">
<?php



	// what algo is checked???
	if($savedquery != ""){

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
<input dojoType="dijit.form.RadioButton" type="radio" id="hier" name="clusterAlgo" value="1" <?php echo $hierChecked; ?> onclick="return hideTrxRow(0)"> Hierarchical<img id="clusterSelection" src="./images/dialog-information12x12.png" align="right"/><div dojoType="dijit.Tooltip" connectId="clusterSelection"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Choosing your Clustering Method</u></strong></td></tr><tr><td>The <strong><font color="blue">Hierarchical clustering</font></strong> algorithm will independently cluster your genes and treatments based on a correlation-associated metric</td></tr><tr><td>The <strong><font color="red">k-Means</font></strong> algorithm allows you to select the number of clusters to place your genes into based on their similarity in gene expression.</td></tr></table></div>
<br><input dojoType="dijit.form.RadioButton" type="radio" id="kmeans" name="clusterAlgo" value="0" <?php echo $kChecked; ?> onclick="return hideTrxRow(1)">K-Means<br>
</td>
<td valign="top" class="results" rowspan="2">
  <div id="toolbar1" dojoType="dijit.Toolbar" style="width:150px;"><button dojoType="dijit.form.ComboButton" iconClass="queryMenuIcon"
								optionsTitle='load options'
								onClick='' id="loadquery">
								<span><strong><font color="blue">Load Query Menu</font></strong></span>

								<div dojoType="dijit.Menu" id="loadMenu" style="display: none;">
									<div dojoType="dijit.MenuItem"
										 iconClass="mySavedQueryOpen"
										onClick="querySavedLoad(<?php echo $_SESSION['userid'];?>)">
										Load Saved Query
									</div>
									<div dojoType="dijit.MenuItem"
										 iconClass="myTempQueryOpen"
										onClick="queryTempLoad()">
										Load Recent Query
									</div>
								</div>
							</button>
			</div>
<div dojoType="dijit.Tooltip" connectId="loadquery"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><b>Click to load a previously executed query.</b><p>  You can load a query saved previously by selecting <font color="blue"><strong>Saved Query</strong></font>.  Additionally, the last three queries that you performed are available when you select  <font color="red"><strong>Load Recent Query.</strong></font></p></tr></td></table> </div>

<br>

</p>
</td>
</tr>
<?php
	// IF THIS IS A SAVED QUERY, WE'VE GOT TO HAVE A VALUE FOR THIS..
	echo "<input name=\"savedquery\" type=\"hidden\" value=\"$savedquery\">\n";
	// IF A TEMP query's involved, gotta have that....
	if($savedquery != ""){
		$tempquery = $savedquery;

	}
	echo "<input name=\"tempquery\" type=\"hidden\" value=\"$tempquery\">\n";


	if($priv <=999){
?>

<?php
	// what dataset is checked???
	if($savedquery != ""){

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
<input dojoType="dijit.form.RadioButton" id="notcondensedoption" type="radio" name="dataset" value="1" <?php echo $notcondChecked; ?>>Not Condensed<img id="dataOptions" src="./images/dialog-information12x12.png" align="right"/><br>

<input dojoType="dijit.form.RadioButton" id="condensedoption" type="radio" name="dataset" value="0" <?php echo $condChecked; ?>><strong><font color="red">Condensed</font></strong><br>
<div dojoType="dijit.Tooltip" connectId="dataOptions"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Choosing your Data Options</u></strong></td></tr><tr><td>The <strong><font color="blue">Not Condensed </font></strong> data option allows you to examine all of the probes on the array independently without any consolidation and averaging of multiple probes on the array with the same annnotation.</td></tr><tr><td>The <strong><font color="red">Condensed</font></strong> data option will only utilize data where multiple probes on the array with the same annotation have been consolidated and the average value of their log ratios has been calculated and transformed into a single fold-change value.</td></tr></table></div>
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
	if($savedquery != ""){
		$kval = $savedvals['number'];
	}
	else{
		$kval = 4;
	}
?>
<tr id="kmeansoption">
<td class="questionparameter"><strong>Number of Clusters:</strong></td>
<td class="results">
<img id="numclustershelpicon" src="./images/dialog-information12x12.png" align="right"/ ><input id="number" name="number" type="text" value="<?php echo $kval; ?>" size="5" align="right">
<div dojoType="dijit.Tooltip" connectId="numclustershelpicon"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Number of Clusters.</u></strong></td></tr><tr><td><p>This field allows you to select the number of clusters you want your genes to be organized into when performing k-Means Clustering.</p></td></tr></table></div>
</td>
<td class="results"><br></td>
</tr>

<tr id="hierarchicaloption">
<td class="questionparameter" ><strong>Cluster Treatments?:</strong></td>
<td class="results">
<?php
// what cluster option is checked???
	if($savedquery != ""){

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
<img id="clusteroption" src="./images/dialog-information12x12.png" align="right"/ ><input dojoType="dijit.form.RadioButton" id="clustertrx"  type="radio" name="trxCluster" value="1" onclick="return hideOrderRows(0)" <?php echo $clusterChecked; ?> >Cluster Treatments</input><br><div dojoType="dijit.Tooltip" connectId="clusteroption"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Choosing how to order your treatments when Hierarchically clustering.</u></strong></td></tr><tr><td>The <strong><font color="blue">Cluster Treatments</font></strong> option will cluster the treatments and display the associated dendrogram.</td></tr><tr><td>The <strong><font color="red">Custom Order/Name Treatments (No Clustering)</font></strong> option allows you to bypass the clustering of treatments and will give you the option of giving the treatments custom names of your choosing.</td></tr></table></div>
<?php
/*
<input type="radio" name="trxCluster" value="2" onclick="return hideOrderRows(0)">Cluster, top dendrogram only</input><br>
<input type="radio" name="trxCluster" value="3" onclick="return hideOrderRows(0)">Cluster, bottom dendrogram only</input><br>
*/
?>
<input  dojoType="dijit.form.RadioButton" id="noclustertrx" type="radio" name="trxCluster" value="0" <?php echo $noclusterChecked; ?> onclick="return hideOrderRows(1)">Custom Order/Name Treatments (No Clustering)</input><br>
</td>
<td class="results">

</td>
</tr>

<tr id="orderoption0">
<td  class="questionanswer" colspan="3"><strong>Ordering Of Selections</strong></td>
</tr>
<tr id="orderoption1">
<?php
// what cluster option is checked???
	if($savedquery != ""){

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
<img id="orderoption" src="./images/dialog-information12x12.png" align="right"/ ><br><input dojoType="dijit.form.RadioButton"  type="radio" id="defaultordering" name="orderoptions" value="0" <?php echo $defaultChecked; ?> onclick="return hideNumGroups(0)">Default Ordering (by Array ID) w/o Custom Names<br>
<?php
//<input dojoType="dijit.form.RadioButton" type="radio" id="customordering" name="orderoptions" value="1" onclick="return hideNumGroups(0)">Individually Order/Name Selections<br>

?>
<input dojoType="dijit.form.RadioButton" type="radio" id="customordering" type="radio" name="orderoptions" value="2" <?php echo $nodefaultChecked; ?> onclick="return hideNumGroups(1)">Custom Order/Name

<div dojoType="dijit.Tooltip" connectId="orderoption"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Ordering of Selections.</u></strong></td></tr><tr><td><p>The <font color='blue'><strong>Default Ordering (by Array ID) w/o Custom Names</strong></font> option will negate the clustering of treatments (i.e, no dendrogram to give an indication of the similarity of treatments) and will order the treatments by their array id #.</p><p>The <font color='red'><strong>Custom Order/Name</strong></font> gives you the ability to order
your Chemical(s)/Condition(s) selections on a subsequent
screen. The number of groups entered will allow you to allot
the treatments to separate groups, thereby segregating them
based on your own discretion.</p></td></tr></table></div>
</td>
<td class="results">

</td>
</tr>

<tr id="ordergroups1">
<td class="questionparameter"><strong>Number of Ordered Groups:</strong></td>
<td class="results">
<?php
	// what dataset is checked???
	if($savedquery != ""){
		$oval = $savedvals['numberGroups'];
	}
	else{
		$oval = 4;
	}
?>
<img id="ordergrouphelpicon" src="./images/dialog-information12x12.png" align="right"/ ><input name="numberGroups" type="text" value="<?php echo $oval; ?>" size="3" align="right">
<div dojoType="dijit.Tooltip" connectId="ordergrouphelpicon"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Number of Ordered Groups.</u></strong></td></tr><tr><td><p>This field allows you to select the number of distinct groups for the ordering of your arrays.</p></td></tr></table></div>
</td>
<td class="results"></td>
</tr>
</table>
<?php

	require('./phpinc/commonarraylayoutselectioncode.inc.php');
?>
<table class="question" width="700px"><tr>
<td  class="questionanswer" colspan="3"><strong>Fold-Change Threshold Values:</strong></td>
</tr>
<tr>
<tr>
<td class="questionparameter" ><strong>Minimum Induction:</strong></td>
<td class="results">
<?php

	if($savedquery != ""){
		$oval = $savedvals['rval'];
		$mval = $savedvals['rvalmax'];
	}
	else{
		$oval = 3;
		$mval = "";
	}
?>
<input size="4" name="rval" type="text" value="<?php echo $oval; ?>" align="right"></input> to maximum of
<input size="4" name="rvalmax" type="text" value="<?php echo $mval; ?>" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be positive.
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Minimum Repression:</strong></td>
<td class="results">
<?php

	if($savedquery != ""){
		$oval = $savedvals['lval'];
		$mval = $savedvals['lvalmin'];
	}
	else{
		$oval = -3;
		$mval = "";
	}
?>
<input size="4" name ="lval" type="text" value="<?php echo $oval; ?>" align="right"></input>
to minimum of
<input size="4" name="lvalmin" type="text" value="<?php echo $mval; ?>" align="right"></input>


</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be negative.
</td>
</tr>

<tr>
<td  class="questionanswer" colspan="3"><strong>Signal Threshold Values:</strong></td>
</tr>
<tr>
<tr>
<td class="questionparameter" ><strong>Green Processed Signal:</strong></td>
<td class="results">
<?php

	if($savedquery != ""){
		$gmeanval = $savedvals['gmeansignal'];
		//$mval = $savedvals['rvalmax'];
	}
	else{
		$gmeanval = 100;
	}

?>
<input size="4" name="gmeansignal" type="text" value="<?php echo $gmeanval; ?>" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be positive.
</td>
<tr>
<td class="questionparameter" ><strong>Red Processed Signal:</strong></td>
<td class="results">
<?php
	if($savedquery != ""){
		$rmeanval = $savedvals['rmeansignal'];
		//$mval = $savedvals['rvalmax'];
	}
	else{
		$rmeanval = 100;
	}
?>
<input size="4" name="rmeansignal" type="text" value="<?php echo $rmeanval; ?>" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be positive.
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>p-Value Cutoff:</strong></td>
<td class="results">

<?php
	if($savedquery != ""){
		$pval = $savedvals['pValue'];
		//$mval = $savedvals['rvalmax'];
	}
	else{
		$pval = .01;
	}
?>

<input size="4" name="pValue" type="text" value="<?php echo $pval; ?>" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>1 <= p-Value > 0.
</td>
<tr>

<tr>
<td  class="questionanswer" colspan="3"><strong>Heat Map Options</strong></td>
</tr>

<tr>
<td class="questionparameter" ><strong>Heat Map Color Scheme:</strong></td>
<td class="results">
<?php
// what colorscheme is checked???
if($savedquery != ""){

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
<td class="questionparameter" ><strong>Heat Map Image Output:</strong></td>
<td class="results">
<?php
// what colorscheme is checked???
if($savedquery != ""){

		if($savedvals['outputformat'] == 0){
			$svg = "checked";
			$png = "";
			$jpg = "";
		}
		elseif($savedvals['outputformat'] == 1){
			$svg = "";
			$png = "checked";
			$jpg = "";
		}
		else{
			$svg = "";
			$png = "";
			$jpg = "checked";
		}
	}
	else{
			$png = "checked";
			$svg = "";
			$jpg = "";
	}
?>
<input type="radio" name="outputformat" <?php echo $svg; ?> value="0"><font color="black"><strong>SVG</font><br>
<input type="radio" name="outputformat" <?php echo $png; ?> value="1"><font color="black"><strong>PNG</font><br>
<input type="radio" name="outputformat" <?php echo $jpg; ?> value="2"><font color="black"><strong>JPG</font><br>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>PNG format will be automatically selected for large queries!
</td>
</tr>
<?php
//if($_SESSION['priv_level'] >= 99){
	if($savedquery != ""){
		if($savedvals['includeimagemap'] == 0){
			$nomap = "checked";
			$showmap = "";
		}
		else{
			$showmap = "checked";
			$nomap = "";
		}
	}
	else{
		$showmap = "checked";
			$nomap = "";

	}
?>
	<tr>
	<td class="questionparameter" ><strong>Include image map?</strong></td>
	<td class="results"><input type="radio" name="includeimagemap" <?php echo $nomap; ?> value="0"><font color="black"><strong>No</font><br>
<input type="radio" name="includeimagemap" <?php echo $showmap; ?> value="1"><font color="black"><strong>Yes</font><br></td>
	<td class="results"></td>
	</tr>
<?php
//}
?>

<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td><input type="reset" value="Reset Form"</td>
</tr>

</table>

</form>

<?php

}


?>
</p>
</div>