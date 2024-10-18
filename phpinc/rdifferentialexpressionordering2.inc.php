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
			foreach(($_POST) as $key=>$val){
			//while(list($key, $val) = each ($_POST)){
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

			// Get the POST values and concatenate them....
			$query1text = "";

			reset ($_POST);
			foreach ($_POST as $key => $val) {
				if ($key !== 'submit') {
					$query1text .= "$key=$val:";
					// echo "$key=$val<br>"; // Uncomment if you want to display the key-value pairs
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




<form  name="order" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>" onsubmit="return checkOrder(<?php echo $length; ?>)">




<?php 
echo "<table style='width:500px;'>";
?>

<thead>
<tr>
<!--<th class="mainheader" colspan="2">Selected Treatments</th>-->
<th style="width:100px;"><font ><strong>Selected Treatments</strong></font></th>
<th style="width:100px;"><font ><strong>Custom Treatment Name</strong></font></th>
<th style="width:100px;"><font color="green"><strong>Cy3 RNA Sample</strong></font></th>
<th style="width:100px;"><font color="red"><strong>Cy5 RNA Sample</strong></font></th>
<th style="width:100px;"><font ><strong><i>limma</i> Parameters</strong></font></th>
</tr>
</thead>
<?php




if(isset($length)){
	if($length > 20){
	?>

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
	echo "<input name=\"numberOfArrays\" type=\"hidden\" value=\"$length\">\n";
	echo "<input name=\"numberOfGroups\" type=\"hidden\" value=\"$numberGroups\">\n";
	if(!isset($querynum)){
		$querynum = "";
	}
	echo "<input name=\"querynum\" type=\"hidden\" value=\"$querynum\">\n";


	foreach ($_POST as $key => $val) {
		if ($key !== 'submit') {
			echo "<input name=\"$key\" type=\"hidden\" value=\"$val\">\n";
			// echo "$key=$val<br>"; // Uncomment if you want to display the key-value pairs
		}
	}

	if(isset($_GET['savedquery']) || $savedquery == ""){
		$isSavedQuery = "";
	}else{
		$isSavedQuery = "true";
	}

// Need to populate the $arrayidarray....

	$arrayidarray = array();
	$arraydescarray = array();
	$arraycy3array = array();
	$arraycy5array = array(); 
	$arrayidSQL = "SELECT DISTINCT arrayid,arraydesc,cy3rnasample, cy5rnasample  FROM agilent_arrayinfo ORDER BY arrayid";
	$arrayidResult = $db->Execute($arrayidSQL);
	if($userid == 1 && $debug == 1){
		echo "$arrayidSQL<br>";

	}
	
	while($row = $arrayidResult->FetchRow()){
		// Check to see which boxes were checked...
		$sampleid = $row[0];
		$thisVal = "array$sampleid";
		if(isset($_POST[$thisVal])){
			$post = $_POST[$thisVal];
			if($post > 0){
				array_push($arrayidarray,$post);
				array_push($arraydescarray, $row[1]);
				# get the rna sample names
				$cy3samplename = "";
				if($row[2] != ""){
					$sql = "SELECT sampleid, samplename FROM agilent_rnasample WHERE sampleid = $row[2]";
					$result = $db->Execute($sql);
					if($result){
						$arow = $result->FetchRow();
						$cy3samplename=$arow[1];
					}
				}else{
					$cy3samplename=$row[2];
				}
				$cy5samplename = "";
				if($row[3] != ""){
					$sql = "SELECT sampleid, samplename FROM agilent_rnasample WHERE sampleid = $row[3]";
					$result = $db->Execute($sql);
					if($result){
						$arow = $result->FetchRow();
						$cy5samplename=$arow[1];
					}
				}
					array_push($arraycy3array, $cy3samplename);
					array_push($arraycy5array, $cy5samplename);
			}
		}
	}
	if($userid == 1){
	#	analyze($savedvals);
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
		$cy3sample = $arraycy3array[$counter];
		$cy5sample = $arraycy5array[$counter];
		
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
		$length = $numberGroups;
	$innercolor = array("lightsteelblue", "DarkKhaki", "salmon", "DarkSeaGreen", "Gainsboro",
			"yellow", "Fuchsia", "LawnGreen", "LightSlateGray", "Olive", "Indigo",
			"PaleVioletRed", "skyblue", "PeachPuff", "Orange", "GoldenRod", "oldlace",
			"pink", "RosyBrown", "green","lightsteelblue","YellowGreen", "salmon",
			"Turquoise", "Thistle", "Peru", "WhiteSmoke");
			$selectMenu = ""; # this is used for the samples in the cy3 channel
			// #################################################
			$selectMenu2 = ""; # this is used for the samples in the cy5 channel
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
				$selectMenu2 = $selectMenu;
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
				$whichchannel = 3;
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
						if($whichchannel == 3){	
							$selectMenu = "<option value=\"$optVal\" $selected style=\"background-color: $innercolor[$optVal];\">$label</option>\r";
							$selectValue = $optVal;
							$whichchannel = 5;
						}else{
							$selectMenu2 = "<option value=\"$optVal\" $selected style=\"background-color: $innercolor[$optVal];\">$label</option>\r";
							$selectValue = $optVal;
							break;
						}
						
						
					}

				}
				for($i = 0; $i < $numberGroups; $i++){
						$aval = $i + 1;
						$label = $aval;
						//if($selectValue != $val){
						// Create the selection menus.....
						
						$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$label</option>\r";
						$selectMenu2 .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$label</option>\r";
						//}
					}

			}
		$size = strlen($savedName);
			if($size < 40){
				$size = 40;
			}
		echo "
		<td class=\"results\">$savedName</td>
		<input name=\"trxidorder$counter\" type=\"hidden\" value=\"$idVal\">
		<td class=\"results\">
		<input name=\"customname$idVal\" type=\"text\" value=\"$savedName\" size=\"$size\" align=\"right\"></td>
		<td class=\"results\"><font color='green'>$cy3sample</font><select name='optioncy3[$idVal]' >$selectMenu</select></td>
		<td class=\"results\"><font color='red'>$cy5sample</font><select name='optioncy5[$idVal]' >$selectMenu2</select></td>";
		
		if($val == 1){
			// Display the pValue input box....
			if($isSavedQuery !=""){
				foreach($savedvals as $nameval){
					$temp = explode("=", $nameval);
					$keyval = "pValue";
					if($temp[0]== $keyval){
						$pValue = $temp[1];
						break;
					}
				}
			}else{
				$pValue = 0.01;
			}
			echo "<td><b>p-Value</b><input name='pValue' type='text' value='$pValue' align='right' size='6'></td>";
		
		}
		if($val == 2){
			if($isSavedQuery !=""){
				foreach($savedvals as $nameval){
					$temp = explode("=", $nameval);
					$keyval = "correction";
					if($temp[0]== $keyval){
						$correctionval = $temp[1];
						break;
					}
				}
			}else{
				$correctionval = 8;
			}
			# now we need to set which one is checked based on $correctionval
			$fdrchecked = "";
			$bhchecked  = "";
			$bychecked  = "";
			$holmchecked = "";
			$hochbergchecked = "";
			$hommelchecked = "";
			$bonferronichecked = "";
			$nonechecked = "";
			switch($correctionval){
				case 1:
					$fdrchecked = "checked";
					break;
				case 2:
					$bhchecked = "checked";
					break;
				case 3: 
					$bychecked = "checked";
					break;
				case 4:
					$holmchecked = "checked";
					break;
				case 5:
					$hochbergchecked = "checked";
					break;
				case 6:
					$hommelchecked = "checked";
					break;
				case 7:
					$bonferronichecked = "checked";
					break;
				default:
					$nonechecked = "checked";
					break;
			}



		?>
			<td><b>Correction Method:</b><br><input type="radio" name="correction" value="1" <?php echo $fdrchecked; ?>>fdr</input><br>
				<input type="radio" name="correction" value="2"  <?php echo $bhchecked; ?>>BH</input> <br>
				<input type="radio" name="correction" value="3"  <?php echo $bychecked; ?>>BY</input><br>
				<input type="radio" name="correction" value="4" <?php echo $holmchecked; ?>>Holm</input><br>
				<input type="radio" name="correction" value="5" <?php echo $hochbergchecked; ?>>Hochberg</input><br>
				<input type="radio" name="correction" value="6" <?php echo $hommelchecked; ?>>Hommel</input> <br>
				<input type="radio" name="correction" value="7" <?php echo $bonferronichecked; ?>>Bonferroni</input><br>
				<input type="radio" name="correction" value="8" <?php echo $nonechecked; ?>>None</input></td>
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


#<input type='checkbox' onclick='toggleLayer("ref")'>Yes</input>
	if($isSavedQuery != ""){
		foreach($savedvals as $nameval){
			$temp = explode("=", $nameval);
			$refchecked = "optionref";
			if($temp[0]== $refchecked){
				$isChecked = $temp[1];
				break;
			}
		}
	}else{
		$isChecked = 1;
	}
	if($isChecked == -1){
		$checkedstr = "selected";
		$optionval = "None";
	}else{
		$checkedstr = "";
		$optionval = $isChecked;
	}

$selectMenu = "<option value=\"$isChecked\" style=\"background-color: white;\" $checkedstr>$optionval</option>";

for($i = 0; $i < $numberGroups; $i++){
	$aval = $i + 1;
	
	$selectMenu .= "<option value='$aval'>$aval</option>";
}
if($isChecked != -1){
	$selectMenu .= "<option value='-1'>None</option>";
}
?>
</table>
<hr>
	<h3 align="center"><font color="blue">ENTER IN YOUR REFERENCE SAMPLE  (FOR REFERENCE DESIGN) AND COMPARISONS (FOR ALL DESIGNS)</font></h3>
<hr>
<table>
<tr><td colspan='2'><b>Reference Sample Associated with these samples?</b></TD><TD><select name='optionref'><?php echo $selectMenu; ?></select></TD><td></td><td></td></tr>
<tr><td colspan='3'><b>Select the comparisons you would like to make betweend the groups:</b></td><td></td><td></td></tr>
</table>
<?php

	$aval = "";
	$bval = "";
	# for now, limit to 10 comparisons....
	for($i = 1; $i <= 10; $i++){
		# create a row...
		if($i != 10){
			$thisrow = $i+1;
			$onChangeStr = "onChange='toggleLayer(\"row$thisrow\")'";
		}else{
			$onChangeStr = "";
		}
		
		$a_val = "1";
		$b_val = "1";
		$thisname = "Enter comparison name";
		if($isSavedQuery != ""){
			foreach($savedoptions as $nameval){
				$temp = explode("=", $nameval);
				$refchecked = $i."a";
				if($temp[0]== $refchecked){
					$a_val = $temp[1];
					break;
				}
			}
			foreach($savedoptions as $nameval){
				$temp = explode("=", $nameval);
				$refchecked = $i."b";
				if($temp[0]== $refchecked){
					$b_val = $temp[1];
					break;
				}
			}
			
			foreach($savedvals as $nameval){
				$temp = explode("=", $nameval);
				$comparisonname = "comparison".$i."name";
				if($temp[0]== $comparisonname){
					$thisname = $temp[1];
					break;
				}
			}
			if($thisname != "Enter comparison name"){
				$style= "";
				$checkval = "true";
			}else{
				$style = "style=\"display:none;\""; 
				$checkval = "false";
			}
		}else{
			if($i > 1){
			$style = "style=\"display:none;\""; 
				$checkval = "false";
			}else{
				$style= "";
				$checkval = "false";
			}
		}
		$a_selectMenu="";
		if($a_val != 1){
			$a_selectMenu = "<option value='$a_val' selected>$a_val</option>";
		}
		for($j = 0; $j < $numberGroups; $j++){
			$aval = $j + 1;
			$a_selectMenu .= "<option value='$aval'>$aval</option>";
		}
		$b_selectMenu="";
		if($b_val != 1){
			$b_selectMenu = "<option value='$b_val' selected>$b_val</option>";
		}
		for($k = 0; $k < $numberGroups; $k++){
			$bval = $k + 1;
			$b_selectMenu .= "<option value='$bval'>$bval</option>";
		} 
		#echo "<tr id='row$i' $style><td >Comparison #$i</td><td><select name='comparison[".$i."a]'>$a_selectMenu</select>Versus<select name='comparison[".$i."b]'>$b_selectMenu</select></td><td colspan='2'><input name='comparison".$i."name' type='text' value='$thisname'></input></td><td><input type='radio' $onChangeStr $checkval>Another?</input></td></tr>";
		echo "<div $style id='row$i'><table style=\"width:600px\"><tr><td >Comparison #$i</td><td><select name='comparison[".$i."a]'>$a_selectMenu</select>Versus<select name='comparison[".$i."b]'>$b_selectMenu</select></td><td colspan='2'><input name='comparison".$i."name' type='text' value='$thisname' style='width:200px;'></input></td><td><input type='radio' $onChangeStr $checkval>Another?</input></td></tr></table></div>";

	}
?>
<hr>
	<h3 align="center"><font color="blue">ENTER IN CUSTOM CODE IF NOT USING A REFERENCE-BASED DESIGN</font></h3>
<hr>

<table>
<tr>
	<td><b>Enter a custom set of commands<br>for more complex comparisons:</b></td>
	<!--<td><textarea name="customcommands" cols=75 rows=10></textarea></td>-->
<?php
$savedcode = "";
if($isSavedQuery != ""){
	foreach($savedvals as $nameval){
		$temp = explode("=", $nameval);
		$customcode = "customlimma";
		if($temp[0]== $customcode){
			$savedcode = $temp[1];
			echo "$savedcode";
			break;
		}
	}

}

?>
	<td>
	<TEXTAREA NAME="customlimma" COLS=70 ROWS=6 ><?php echo $savedcode; ?></TEXTAREA>
	</td>
	<td>
	<img id="targetsfiletooltip" src="./images/dialog-information12x12.png" align="top"/>
	<div dojoType="dijit.Tooltip" connectId="targetsfiletooltip">
		<table width="350px">
			<tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td></tr>
			<tr><td><font color="red"><strong>NOTE:  </strong></font>This custom set of commands will supersede the default generated code for a reference design.  Any value entered for a reference sample above will be ignored.  Click on the Instructions link to the right for more information.</td></tr>
		</table>
	</div>
	</td>
	<td><a href="./Instructions/limmacomplexqueries.php" target="_blank">Instructions</a></td>
	<td></td>
</tr>
<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td><input type="reset" value="Reset Form"></td>
<td></td>

<td></td>

</tr>
</table>
</form>

