<?php
/***
Location: /edge2
Description: This page is used to do the k-means and hierarchical clustering of the treatments.  Users are given the ability
		to choose individual arrays or specific chemicals.
POST:
FORM NAME: "query" ACTION: "clustering.php" METHOD: "post" ONSUBMIT: ""
	FUNCTION: Used to select the parameters for the clustering algorithms.
	ITEMS:  'clusterAlgo' <radio>, 'savedquery', 'tempquery', 'dataset' <radio>, 'number',
		'trxCluster' <radio>, 'orderoptions' <radio>, 'numberGroups', 'seloptions' <radio>,
		'chem[chemidnumber] <checkbox>, 'trx[arrayidnumber]' <checkbox>, 'colorScheme' <radio>,
		'rval', 'rvalmax', 'lval', 'lvalmin', 'submit'
GET: none
Files included or required: 'edge_db_connect2.php','header.inc','formcheck2.inc','edge_update_user_activity.inc','cloneinfotable.inc'
***/



$debugid = -1;
$debug = -1;
$arraytypestring = "agilent";
$arrayclusteringtype = 1;
$arraydatatable = "agilentdata";
$thisorganism = 0; // This is a mouse!
?>





<?php
$orderingMethod = 2;
$browserval = 1;
if (isset($_POST['knearestsubmitorder'])) { // if form has been submitted and it is not being ordered or orderedSubmit is true...


$orderedSubmit = true;  // need to set for the commonclusteringsavequerycodeafter

require('./phpinc/commonclusteringsavequerycodeaftersubmittingforclustering.inc.php');
	
	$filenum = rand(0, 25000);
	$file = "$IMAGESdir/data$filenum.txt";
	$svgFile = "$IMAGESdir/svg$filenum.svg";
	$tableFile = "$IMAGESdir/table$filenum";
	$csvFile = "$IMAGESdir/$filenum.csv";
	$command = "touch $file";
	$str=exec($command);
	$command = "touch $svgFile";
	$str=exec($command);
	$command = "touch $tableFile";
	$str=exec($command);
	/*$colorscheme = $_POST['colorScheme'];
	$upperbound = $_POST['rval'];
	$upperboundmax = $_POST['rvalmax'];
	$lowerbound = $_POST['lval'];
	$lowerboundmin = $_POST['lvalmin'];
	*/
	//analyze($_POST);
	// NEED TO CONVERT THESE VALUES TO FOLD CHANGE....
	//include 'convertthresholdvalues.inc';

	$fd = fopen($file, 'w');
	$csvfd = fopen($csvFile, 'w');
	rewind($fd);
	rewind($csvfd);
	$arrayidArray = array();
	$arrayDescArray = array();

		/***********************************************************************************
		ORDERED SECTION...............
		***********************************************************************************/
		include 'k-nearestneighbors-datageneration.inc';


}
else if(isset($_POST['knearestsubmit']) && $orderingMethod >= 1) {




	$tempquery = $_POST['tempquery'];
	$savedquery = $_POST['savedquery'];
	if(isset($_GET['savedquery'])){
		$query1text = "";
		// NEED TO UPDATE THE TEMP QUERY......
		if($userid == $debugid && $debug == 1){
			echo "<hr>this is a saved query<br>";
		}
		reset ($_POST);
		while(list($key, $val) = each ($_POST)){
			if($key != "submit"){
				$query1text .= "$key=$val:";
				if($userid == $debugid && $debug == 1){
					echo "$key=$val<br>";
				}
			}
		}
					
		$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($tempquery, $userid, \"$query1text\", NOW()) ON DUPLICATE KEY UPDATE query=$tempquery";
		if($userid == $debugid && $debug == 1){
			echo "$sql <br>";
		}
		$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);

		//echo "This is a saved query...<br>";
		// Need to populate the current query screen....
		$sql = "SELECT queryname, query2, query2opts FROM savedqueries WHERE query = $savedquery AND userid = $userid";
		if($userid == $debugid && $debug == 1){
			echo "$sql<br>";
		}
		$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);
		$row = $sqlResult->FetchRow();//mysql_fetch_row($sqlResult);
		$queryname = $row[0];
		$query2 = $row[1];
		$query2opts = $row[2];
		if($userid == $debugid && $debug == 1){
			echo "$query2<br>";
			echo "$query2opts<br>";
		}
		// NOW need to explode $query2 into an array, the separator is :
		$savedvals = explode(":", $query2);
		// pop the last value of due to final :
		array_pop($savedvals);
		if($userid == $debugid && $debug == 1){
			analyze($savedvals);
		}
		// GET THE OPTIONS...
		$savedoptions = explode(":", $query2opts);

		array_pop($savedoptions);
		//analyze($savedoptions);

		}else{
			if($userid == $debugid && $debug == 1){
				echo "this is NOT a saved query<br>";
			}
			// Form is being submitted from the first data input screen....
			// BUT THIS IS USING THE *CUSTOM* ORDERING AND NAMING OF THE ARRAYS....
			// Get the value for this query and then update the table accordingly...
			// Later an option will be given so that the user can save the query....
			// Get the POST values and concatenate them....
			$query1text = "";
			reset ($_POST);
			foreach ($_POST as $key => $val) {
				if ($key != "submit") {
					$query1text .= "$key=$val:";
					//echo "$key=$val<br>";
				}
			}
			//echo "in query 1 submit section<br>";
			$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($tempquery, $userid, \"$query1text\", NOW()) ON DUPLICATE KEY UPDATE
query=$tempquery";
			if($userid == $debugid && $debug == 1){
				echo "$sql <br>";
			}
			$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);
		}

?>




<form name="knearest-order" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>" onsubmit="return checkOrder(<?php echo $length; ?>)">
<?php
echo "<table class=\"results\">";
?>
<thead>
<tr>
<th class="mainheader"><font color='black'><b>Selected Treatments</b></font></th>
<th class="mainheader" ><font color='black'><b>Assign Class</b></font></th>
<th class="mainheader"><font color='black'><b>Custom Treatment Name</b></font></th>

<th class="mainheader"><font color='black'><b>Class Designations</b></font></th>
</tr>
</thead>
<?php



$counter = 0;
//print "Posted variables: <br>";
	reset ($_POST);
	//echo "<input name=\"orderedSubmit\" type=\"hidden\" value=\"true\">\n";
	$numberGroups = $_POST['numberGroups'];
	$numberKNearestNeighbors = $_POST['numberKNearestNeighbors'];
	echo "<input name=\"numberOfGroups\" type=\"hidden\" value=\"$numberGroups\">\n";
	echo "<input name=\"numberKNearestNeighbors\" type=\"hidden\" value=\"$numberKNearestNeighbors\">\n";

# echo "<input name=\"querynum\" type=\"hidden\" value=\"$querynum\">\n";

	foreach ($_POST as $key => $val) {
		if($key != "submit"){
		echo "<input name=\"$key\" type=\"hidden\" value=\"$val\">\n";
		}
	}


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
			<td class=\"questionanswer\"><select name='array$valclass'>
				<option value=\"$val\" selected>$val</option>\r;
					$selectMenu
			</select></td>";
		echo "</tr>";
		$counter++;
		}
		$counter=0;
	}
	else{
	//echo "building table...<br>";
	$innercolor = array("lightsteelblue", "DarkKhaki", "salmon", "DarkSeaGreen", "Gainsboro",
			"yellow", "Fuchsia", "LawnGreen", "LightSlateGray", "Olive", "Indigo",
			"PaleVioletRed", "skyblue", "PeachPuff", "Orange", "GoldenRod", "oldlace",
			"pink", "RosyBrown", "green","lightsteelblue","YellowGreen", "salmon",
			"Turquoise", "Thistle", "Peru", "WhiteSmoke");


		echo "<input name=\"orderedIndividually\" type=\"hidden\" value=\"false\">\n";

		if(!isset($_GET['savedquery'])){
			$isSavedQuery = "";

		}else{
				$isSavedQuery = "true";
		}
//analyze($_POST);
// Need to populate the $arrayidarray....

	$arrayidarray = array();
	$arraydescarray = array();
	#$arrayidSQL = "SELECT arrayid,arraydesc FROM agilent_arrayinfo where arraytype = 0 ORDER BY arrayid";
	$arrayidSQL = "SELECT arrayid,arraydesc FROM agilent_arrayinfo where 1 ORDER BY arrayid";
	$arrayidResult = $db->Execute($arrayidSQL);
	$classidcounter = 0;
	while($row = $arrayidResult->FetchRow()){
	// Check to see which boxes were checked...
		$sampleid = $row[0];
		//$sampleid .=a;
		$thisVal = "array$sampleid";
		#if($userid == 1){
		#	echo "thisval =$thisVal<br>";
		#}
		if(isset($_POST[$thisVal])){
			$post = $_POST[$thisVal];
			//$post = substr("$post", 0, -1);
		#if($userid == 1){
			#	echo "postval = $post<br>";
			#}
			if($post > 0){
			//echo "postval = $post<br>";
				array_push($arrayidarray,$post);
				array_push($arraydescarray, $row[1]);
			}
		}
	}
$length = count($arrayidarray);
echo "<input name=\"numberOfArrays\" type=\"hidden\" value=\"$length\">\n";
		$idarraysize = count($arrayidarray);
		$jindex = 0;
		while($jindex < $idarraysize){
			$idVal = $arrayidarray[$jindex];
			$key = "arraynum".$jindex;
			$keyval = $idVal;
			echo "<input name=\"$key\" type=\"hidden\" value=\"$keyval\">\n";
			$jindex++;
			echo "<tr>";
			$val = $counter + 1;
			//$idVal = array_pop($arrayidarray);
			//echo "idVAl = $idVal<br>";
			/*
			//#################################################33
			*/
			// Do we've any name changes here??????
			$savedName = "";
			if($isSavedQuery != ""){
				foreach($savedvals as $nameval){
					$temp = explode("=", $nameval);
					$customname = "customname$idVal";
					//echo "customname = $customname<br>";
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
			if(!isset($_GET['savedquery'])){
				//echo "This is not a saved query...";

				$numberGroups = $_POST['numberGroups'];
				// For k-nearest neighbors, add an additional option after the loop!
				for($i = 0; $i < $numberGroups; $i++){
					$aval = $i + 1;
					//echo "a value=$aval<br>";
					// Create the selection menus.....
					$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$aval</option>\r";
				}
				$selectMenu .= "<option value=\"TEST\" style=\"background-color: $innercolor[$i];\">TEST</option>\r";
			}
			else{
	#analyze($savedvals);
				$isSavedQuery = "true";
				// What group is it in.... REBUILDING SELECT MENU....
				// Is this an id we had before
				$selectMenu = "";
				$selectValue = "";
				foreach($savedvals as $arrayval){
					//echo "ARRAYVAL = $arrayval<br>";
					$temp = explode("=", $arrayval);
					//echo "temp[0] = $temp[0] idVal = $idVal temp[1]=$temp[1]<br>";
					$assignedclass = "arraynum".$classidcounter."class";
					if($temp[0] == $assignedclass){
						$optVal = $temp[1];
							//echo "valC = $valC and optVal = $optVal<br>";
							$selected = "selected";
							if($optVal > $numberGroups && $optVal != "TEST"){
								// If the number of groups is updated and is less than the previous value, set to the number of groups...
								$optVal = $numberGroups;
							}
							
							$selectMenu = "<option value=\"$optVal\" $selected style=\"background-color: $innercolor[$classidcounter];\">$optVal</option>\r";
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
					$selectMenu .= "<option value=\"TEST\" style=\"background-color: $innercolor[$i];\">TEST</option>\r";

			}
			$classidcounter++;

			// Do we've any groups selected and what group is this trx in???????????
			$savedgroup = "";
			if($isSavedQuery != ""){
				foreach($savedvals as $nameval){
					$temp = explode("=", $nameval);
					$customname = "customname$idVal";
					//echo "customname = $customname<br>";
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
			$classindexnum = $jindex - 1;
			$arrayclass = "arraynum".$classindexnum."class";
			$size = strlen($savedName);
			if($size < 40){
				$size = 40;
			}
			echo "
			<td class=\"results\">$savedName</td>
			<input name=\"trxidorder$counter\" type=\"hidden\" value=\"$idVal\">

					<td class=\"questionanswer\"><select name='$arrayclass'>
						$selectMenu
				</select></td>
				<td class=\"results\">

			<input name=\"customname$jindex\" type=\"text\" value=\"$savedName\" size=\"$size\" align=\"right\"></td>";
			//echo "val = $val  NumberOfGroups=$numberGroups<br>";

			if($val <= $numberGroups){
				#echo "val is <= $numberGroups<br>";
				$group = $val;
				$isChecked = "";
				if($isSavedQuery != ""){
					foreach($savedvals as $nameval){
						$temp = explode("=", $nameval);
						$customname = "class$group";

						if($temp[0]== $customname){
							$classlabel = $temp[1];
							#echo "classlabel = $classlabel<br>";
							break;
						}
					}
				}
$currentClass = $val - 1;
$numberClasses = $_POST['numberGroups'];
	#echo "$numberClasses<br>";

				//if($val <= $numberClasses){
				if($isSavedQuery != ""){
					echo "<td class=\"questionanswer\"><input style=\"background-color: $innercolor[$currentClass];\" type=\"text\" name=\"class$group\" value=\"$classlabel\"></input></td>";
				}else{
			/*	if($val <= $numberClasses){
					echo "<td class=\"questionanswer\"><input style=\"background-color: $innercolor[$currentClass];\" type=\"text\" name=\"class$group\" value=\"$classArray[$currentClass]\"></td>";
				}else{
			*/
					echo "<td class=\"questionanswer\"><input style=\"background-color: $innercolor[$currentClass];\" type=\"text\" name=\"class$group\" value=\"$val Class $val\"></td>";
			#	}
			
				}
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
<td><input type="submit" name="knearestsubmitorder" value="K-nearest Ordering Submit"></td>
<td></td>
<td><input type="reset" value="Reset Form"</td>
<td></td>

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
require('./phpinc/commonclusteringsavequerycode.inc');
/*
//#############ARE WE DEALING W/ A SAVED QUERY????############################
	if(isset($_GET['savedquery'])){

		// CREATE A TEMP QUERY TO STORE THE UPDATED SAVED QUERY!!!!!!
		//############################################################
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Need to get the max number in the savedqueries table and add one to that,
			// because that is the new number for this query....s
			$sql = "SELECT MAX(query) FROM savedqueries";
			$sqlResult = $db->Execute($sql); 
			$row = $sqlResult->FetchRow();
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
			$sqlResult = $db->Execute($sql);
			//echo "$sql <br>";
			//echo "<br>#################END TEMP QUERY##################################<br>";
		//############################################################
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

		// Since $savedquery is the number of the query, we need to get the information for that query....
		// Specifically, is this only a one page or a two pager?
		$savedquery = $_GET['savedquery'];
		$sql = "SELECT queryname, query1, query2opts FROM savedqueries WHERE query = $savedquery AND userid = $userid";
		//echo "$sql<br>";
		$sqlResult = $db->Execute($sql);
		$row = $sqlResult->FetchRow();
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
			$sqlResult = $db->Execute($sql);
			$row = $sqlResult->FetchRow();
			//if($tempquery != ""){
				$tempquery = $row[0];
			//}
			if($tempquery == "NULL"){  // a boundary condition... the table is empty.  the other boundary is 10^10... not checking that...
				$tempquery = 1;
			}else{
			// increment...
				$tempquery += 1;
			}
			
		$savedquery = "";

	}

*/




?>
<p class="styletext">
<form name="knearest" method="post" onsubmit="return check_kNearestForm()" action="<?php  $_SERVER['PHP_SELF'] ?>">

<table class="question" width="800px">
<thead>
<tr>
<th class="mainheader" colspan="2"><font color='black'><b>Query Parameters</b></font></th>
<th class="mainheader" ></th>
</tr>

<?php
	// IF THIS IS A SAVED QUERY, WE'VE GOT TO HAVE A VALUE FOR THIS..
	if(isset($_GET['savedquery'])){
		$savedquery = $_GET['savedquery'];
	}else{
		$savedquery = "";
	}
		echo "<input name=\"savedquery\" type=\"hidden\" value=\"$savedquery\">\n";
	// IF A TEMP query's involved, gotta have that....
	echo "<input name=\"tempquery\" type=\"hidden\" value=\"$tempquery\">\n";


	
?>
<tr id="ordergroups1">
<td class="questionparameter"><strong>Number of K-Nearest Neighbors?:</strong></td>
<td class="results">
<?php
	// what dataset is checked???
	if(isset($_GET['savedquery'])){
		$oval = $savedvals['numberKNearestNeighbors'];
	}
	else{
		$oval = 2;
	}
?>
<input name="numberKNearestNeighbors" type="text" value="<?php echo $oval; ?>" size="3" align="right">
</td>
<td valign="top" class="results">
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
		<div dojoType="dijit.Tooltip" connectId="loadquery"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><b>Click to load a previously executed query.</b><p>  You can load a query saved previously by selecting <font color="blue"><strong>Saved Query</strong></font>.  Additionally, the last three queries that you performed are available when you select  <font color="red"><strong>Load Recent Query.</strong></font></p></tr></td></table>
		
		<br>
		
		</p>
		 </div>
		</td>
</tr>
<tr id="ordergroups1">
<td class="questionparameter"><strong>Number of Distinct Classes?:</strong></td>
<td class="results">
<?php
	// what dataset is checked???
	if(isset($_GET['savedquery'])){
		$oval = $savedvals['numberGroups'];
	}
	else{
		$oval = 2;
	}
?>
<input name="numberGroups" type="text" value="<?php echo $oval; ?>" size="3" align="right">
</td>
<td class="results"></td>
</tr>
</table>
<?php

	//require('./phpinc/commonarraylayoutselectioncode.inc.php');
	require('./displayexperimentsbygroups.php');
		# this include uses a experiments by groups layout that 
	# doesn't use dojo.  uses bootstrap and more modern approach
	#require('./phpinc/newcommonarraylayoutselectioncode.inc.php');
?>
<table class="question" width="700px"><tr>

<td class="results"><input type="submit" name="knearestsubmit" value="k-Nearest Submit"></td>
<td class="results"></td>
<td class="results"><input type="reset" value="Reset Form"></td>
</tr>

</table>

</form>
</p>
<?php

}


?>


