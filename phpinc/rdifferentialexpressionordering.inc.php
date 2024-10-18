<?php

$debugid = 1;
$debug = 0;
	$tempquery = $_POST['tempquery'];
	$savedquery = $_POST['savedquery'];

$anovaval = "";
$anovapvalue="";

//analyze($_SESSION);
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
		
			//$sql = "UPDATE savedqueries SET query1= \"$query1text\" WHERE query=$tempquery";
			
			$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($tempquery, $userid, \"$query1text\", NOW()) ON DUPLICATE KEY UPDATE
query=$tempquery";
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
/*
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
			*/
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
			$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($tempquery, $userid, \"$query1text\", NOW()) ON DUPLICATE KEY UPDATE
query=$tempquery";
			if($userid == $debugid && $debug == 1){
				echo "$sql <br>";
			}
			$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);
		}

if(!isset($length)){
	$length = 0;
}

?>




<form  enctype="multipart/form-data" name="order" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>" onsubmit="return checkOrder(<?php echo $length; ?>)">
<?php
echo "<table class=\"results\">";
?>
<thead>
<tr>
<!--<th class="mainheader" colspan="2">Selected Treatments</th>-->
<th class="mainheader" colspan="1"><font color="black"><strong>Selected Treatments</strong></font></th>
<th class="mainheader" ><font color="black"><strong>Chosen Order</strong></font></th>
<th class="mainheader"><font color="black"><strong>Custom Treatment Name</strong></font></th>
<th class="mainheader"><font color="black"><strong>Statistics</strong></font></th>
<!--
<th class="mainheader"><font color="black"><strong>Separator<br>after Group?</strong></font></th>
-->
</tr>
</thead>
<?php




if(isset($length)){
	if($length > 20){
	?>
	<tr>
	<td><input type="submit" name="submit" value="Submit"></td>
	<td></td>
	<td></td>
	<td><input type="reset" value="Reset Form"</td>
	<td></td>
<!--
	<td></td>
-->
	</tr>
	
	<?php
	}
}else{
	$length = "";
}
$counter = 0;
//print "Posted variables: <br>";
//echo "creating form....<br>";
	reset ($_POST);
	echo "<input name=\"orderedSubmit\" type=\"hidden\" value=\"true\">\n";
	echo "<input name=\"orderedformSubmit\" type=\"hidden\" value=\"true\">\n";
	
	echo "<input name=\"numberOfGroups\" type=\"hidden\" value=\"$numberGroups\">\n";
	if(!isset($querynum)){
		$querynum = "";
	}
	echo "<input name=\"querynum\" type=\"hidden\" value=\"$querynum\">\n";

	while(list($key, $val) = each ($_POST)){

		if($key != "submit"){
		echo "<input name=\"$key\" type=\"hidden\" value=\"$val\">\n";
		}
	}
if(isset($_POST['colorScheme'])){
	$colorscheme = $_POST['colorScheme'];
}else{
	$colorscheme = "";
}
if(isset($_POST['rval'])){
	$upperbound = $_POST['rval'];
}else{
	$upperbound = "";
}
if(isset($_POST['lval'])){
	$lowerbound = $_POST['lval'];
}else{
	$lowerbound = "";
}	
if(isset($_POST['clusterAlgo'])){
	$clusterAlgo = $_POST['clusterAlgo'];
}else{
	$clusterAlgo = "";
}		



	// Changed line below at 20:27 on 17NOV2007
	$length = $numberGroups;
	$innercolor = array("lightsteelblue", "DarkKhaki", "salmon", "DarkSeaGreen", "Gainsboro",
			"yellow", "Fuchsia", "LawnGreen", "LightSlateGray", "Olive", "Indigo",
			"PaleVioletRed", "skyblue", "PeachPuff", "Orange", "GoldenRod", "oldlace",
			"pink", "RosyBrown", "green","lightsteelblue","YellowGreen", "salmon",
			"Turquoise", "Thistle", "Peru", "WhiteSmoke");


		echo "<input name=\"orderedIndividually\" type=\"hidden\" value=\"false\">\n";
		if(isset($_POST['minuscontrol'])){
			$minuscontrolval = $_POST['minuscontrol'];
		}else{
			$minuscontrolval = "";
		}
		echo "<input name=\"minuscontrol\" type=\"hidden\" value=\"$minuscontrolval\">\n";
		// We've got the length and the number of groups....
		if($length <= $numberGroups){
			$numberGroups = $length;
		}
		
		if(isset($_GET['savedquery']) || $savedquery == ""){
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
	$arrayidResult = $db->Execute($arrayidSQL);//mysql_query($arrayidSQL, $db);
	if($userid == 1 && $debug == 1){
		echo "$arrayidSQL<br>";

	}
	//while($row = mysql_fetch_row($arrayidResult)){
	while($row = $arrayidResult->FetchRow()){
	// Check to see which boxes were checked...
		$sampleid = $row[0];
		//$sampleid .=a;
		$thisVal = "array$sampleid";
		//echo "thisval =$thisVal<br>";
		if(isset($_POST[$thisVal])){
			$post = $_POST[$thisVal];
			if($post > 0){
				array_push($arrayidarray,$post);
				array_push($arraydescarray, $row[1]);
			}
			//echo "postval = $post<br>";
		}
		//$post = substr("$post", 0, -1);
		//echo "postval = $post<br>";
		
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
			if(!isset($_GET['savedquery']) || $savedquery == ""){
				//echo "This is not a saved query...";
				
				$numberGroups = $_POST['numberGroups'];
				for($i = 0; $i < $numberGroups; $i++){
					$aval = $i + 1;
					//echo "a value=$aval<br>";
					// Create the selection menus.....
					$label = $aval;
						//if($selectValue != $val){
						// Create the selection menus.....
						if(isset($_POST['minuscontrol'])){
							$minuscontrolisset = $_POST['minuscontrol'];
						}else{
							$minuscontrolisset = -1;
						}
						if($minuscontrolisset == 1 && $aval == 1){
							$label = "Control";
						}
						$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$label</option>\r";
					
				}
			}
			else{
				$isSavedQuery = "true";
				// What group is it in.... REBUILDING SELECT MENU....
				// Is this an id we had before
				if($userid == 1 || $userid == 1){
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
							$label = $optVal;
							if(isset($_POST['minuscontrol'])  && $label == 1){
								$label = "Control";
							}
							$selectMenu = "<option value=\"$optVal\" $selected style=\"background-color: $innercolor[$optVal];\">$label</option>\r";
							$selectValue = $optVal;
						break;
					}

				}
				for($i = 0; $i < $numberGroups; $i++){
						$aval = $i + 1;
						$label = $aval;
						//if($selectValue != $val){
						// Create the selection menus.....
						if(isset($_POST['minuscontrol']) && $aval == 1){
							$label = "Control";
						}
						$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$label</option>\r";
						//}
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

			
			echo "
			<td class=\"results\">$savedName</td>
			<input name=\"trxidorder$counter\" type=\"hidden\" value=\"$idVal\">

					<td class=\"questionanswer\"><select name='option[$idVal]' >
						$selectMenu
				</select></td>
				<td class=\"results\">
			<input name=\"customname$idVal\" type=\"text\" value=\"$savedName\" size=\"25\" align=\"right\"></td>";


			//if($val == 1 && $_SESSION['priv_level']==99){
			
			
			if($val == 1){
				// Display the pValue input box....
				echo "<td><input name='pValue' type='text' value='0.001' align='right'></td>";
			
			}
			if($val == 2){
			?>
				<td><input type="radio" name="correction" value="1" checked>fdr</input><br>
					<input type="radio" name="correction" value="2" >BH</input> <br>
					<input type="radio" name="correction" value="3" >BY</input><br>
					<input type="radio" name="correction" value="4">Holm</input><br>
					<input type="radio" name="correction" value="5">Hochberg</input><br>
					<input type="radio" name="correction" value="6">Hommel</input> <br>
					<input type="radio" name="correction" value="7">Bonferroni</input><br>
					<input type="radio" name="correction" value="8">None</input></td>
			<?php
			}
			
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
				echo "<!--<td class=\"questionanswer\"><input type=\"checkBox\" id=\"group$group\" name=\"group$group\" value=\"$group\" $isChecked>Group $group</td>-->";
			}
			else{
				echo "<!--<td></td>-->";
			}
			if($isSavedQuery != ""){
				$selectMenu = "";
			}
			
			echo "</tr>";
			$counter++;
		}
		$counter=0;


echo "<input name=\"numberOfArrays\" type=\"hidden\" value=\"$val\">\n";


?>
<tr>
	<td>Enter a custom targets file<br>for more complex comparisons</td>
	<td><input name="customtargetsfile" type="file" size="50"></td>
	<td>NOTE: This will supercede any modifications above.</td>
	<td></td>
	<td></td>
</tr>
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
?>