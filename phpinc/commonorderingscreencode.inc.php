<?php

/*

there is now the ability to use rough FDR (false discovery rate) and bonferroni correction. 


bonferroni is by far the most selective of the two since ituses and an adjusted alpha value based on your p-value cut-off and the number of tests being done.  for instance, if you bring back 1000 genes w/ fold-change and microarray-based threshold parameters and use a .01 p-value, the adjusted alpha value would be:

adjusted alpha = .01/1000 = 0.00001
meaning instead of each hypothesis test (i.e., t-test for each gene) being rejected @ .01, the rejection threshold would be .005005.

the Rough fdr is a little less stringent and calculates an adjusted value using the same parameters as above using this formula:

q-value = (.01) * (1000 + 1)/ (2 * 1000) = 0.005005




*/

$userid = $_SESSION['userid'];
$debugid = -1;
$debug = -1;
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
			/* while(list($key, $val) = each ($_POST)){
				if($key != "submit"){
					$query1text .= "$key=$val:";
					if($userid == $debugid && $debug == 1){
						echo "$key=$val<br>";
					}
				}
			} */
			foreach ($_POST as $key => $val) {
				if ($key != "submit") {
					$query1text .= "$key=$val:";
					if ($userid == $debugid && $debug == 1) {
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
			foreach ($_POST as $key => $val) {
				if ($key !== 'submit') {
					$query1text .= "$key=$val:";
					// echo "$key=$val<br>"; // Uncomment if you want to display the key-value pairs
				}
			}
			//echo "in query 1 submit section<br>";
			$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($tempquery, $userid, \"$query1text\", NOW()) ON DUPLICATE KEY UPDATE
query=$tempquery";

			$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES(?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE
query=?";
			if($userid == $debugid && $debug == 1){
				echo "$sql <br>";
			}
			$sqlResult = $db->Execute($sql, array($tempquery, $userid, $query1text, $tempquery));//mysql_query($sql, $db);
			// Check if the record set is valid

			if($sqlResult === false){
				echo 'There was in error inserting the saved query.';
				throw new Exception('Query failed: ' . $db->ErrorMsg());
			}

			
		}

if(!isset($length)){
	$length = 0;
}

?>




<form name="order" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>" onsubmit="return checkOrder(<?php echo $length; ?>)">
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
<th class="mainheader"><font color="black"><strong>Separator<br>after Group?</strong></font></th>
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
	<td></td>
	
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
	echo "<input name=\"numberOfArrays\" type=\"hidden\" value=\"$length\">\n";
	echo "<input name=\"numberOfGroups\" type=\"hidden\" value=\"$numberGroups\">\n";
	if(!isset($querynum)){
		$querynum = "";
	}
	echo "<input name=\"querynum\" type=\"hidden\" value=\"$querynum\">\n";

	foreach ($_POST as $key => $val) {
		if($key != "submit"){
			echo "<input name=\"$key\" type=\"hidden\" value=\"$val\">\n";
			}
	}
	

	/* while(list($key, $val) = each ($_POST)){
		if($key != "submit"){
			echo "<input name=\"$key\" type=\"hidden\" value=\"$val\">\n";
			}
	} */
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


// If the ordering method is by individual treatments....
	if($orderingMethod == 1){
	echo "yes method=1";
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

		$anovapvalues="";
			$ttpvalues="";
			$ttest1checked = "";
			$ttest2checked = "";
			$ttest3checked = "";
			$ttest4checked = "";
			$ttest5checked = "";
			$ttest6checked = "";
			$anova1checked ="";
			$anova2checked ="";
			$anova3checked ="";
			$ttestpvalue = "";
			$correctionval = "";
			
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
						$numcolors = count($innercolor);
						if($i > ($numcolors-2)){
							
							$colorval = $i%$numcolors;
						}else{
							$colorval = $aval;	
						}
						#echo "aval = $aval<br>";
						$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$colorval];\">$label</option>\r";
					
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
							$numcolors = count($innercolor);
							if($optVal > ($numcolors-2)){
								
								$colorval = $optVal%$numcolors;
							}else{
								$colorval = $optVal;	
							}
							$selectMenu = "<option value=\"$optVal\" $selected style=\"background-color: $innercolor[$colorval];\">$label</option>\r";
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
						$numcolors = count($innercolor);
						if($i > ($numcolors-2)){
							
							$colorval = $i%$numcolors;
						}else{
							$colorval = $aval;	
						}
						#echo "aval = $aval<br>";
						$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$colorval];\">$label</option>\r";
					
						#$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$label</option>\r";
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
			
			$size = strlen($savedName);
			if($size < 40){
				$size = 40;
			}
			echo "
			<td class=\"results\">$savedName</td>
			<input name=\"trxidorder$counter\" type=\"hidden\" value=\"$idVal\">

					<td class=\"questionanswer\"><select name='option[$idVal]' >
						$selectMenu
				</select></td>
				<td class=\"results\">
			<input name=\"customname$idVal\" type=\"text\" value=\"$savedName\" size=\"$size\" align=\"right\"></td>";


			//if($val == 1 && $_SESSION['priv_level']==99){
			
			if($_POST['numberGroups'] > 2 && $val == 1){
				$anovachecked = "";
				if($isSavedQuery != ""){
					foreach($savedvals as $nameval){
						$temp = explode("=", $nameval);
						
						if($temp[0]== "anova"){
							$anovaval = $temp[1];
							break;
						}
					}
					
					if($anovaval == 1){
						$anovachecked = "checked";
					}
					
				}
				echo "<td class=\"questionanswer\"><input type=\"checkBox\"  dojoType=\"dijit.form.CheckBox\" name=\"anova\" value=\"1\"   onClick=\"showAnovaOptions()\" $anovachecked>ANOVA</td>";
			}
			elseif($_POST['numberGroups'] == 2 && $val == 1){
				$ttestchecked = "";
				$ttestval = -1;
				if($isSavedQuery != ""){
					foreach($savedvals as $nameval){
						$temp = explode("=", $nameval);
						
						if($temp[0]== "ttest"){
							$ttestval = $temp[1];
							break;
						}
					}
					
					if($ttestval == 1){
						$ttestchecked = "checked";
					}
					
				}
				echo "<td class=\"questionanswer\"><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\" name=\"ttest\" value=\"1\"  onClick=\"showtTestOptions()\" $ttestchecked>t-Test</td>";

			}elseif($_POST['numberGroups'] == 2 && $val == 2){
				/*pvalue:
				1 = .10
				2 = .05
				3 = .025
				4 = .01
				5 = .005
				6 = .001
				*/
				
				if($isSavedQuery != ""){
					foreach($savedvals as $nameval){
						$temp = explode("=", $nameval);
						
						if($temp[0]== "ttestpvalue"){
							$ttestpvalue = $temp[1];
							break;
						}
					}
					
					if($ttestpvalue == 1){
						$ttest1checked = "checked";
					}elseif($ttestpvalue == 2){
						$ttest2checked = "checked";
					}elseif($ttestpvalue == 3){
						$ttest3checked = "checked";
					}elseif($ttestpvalue == 4){
						$ttest4checked = "checked";
					}elseif($ttestpvalue == 5){
						$ttest5checked = "checked";
					}else{
						$ttest6checked = "checked";
					}
				}else{

					if($userid == $debugid && $debug == 1){
						echo "This is not a saved query...<br>";
					}
					

				}
				if($ttestchecked == ""){
					$ttpvalues="hidden";
				}else{
					$ttpvalues="visible";
				}
			?>
			
				<td rowspan='2' class="questionanswer"><div id="ttpvaluecell" style="visibility:<?php echo $ttpvalues; ?>"  ><u><b>p-Value</b></u><br>
					<input type="radio" name="ttestpvalue" value="1" <?php echo $ttest1checked; ?>>0.10</input><br>
					<input type="radio" name="ttestpvalue" value="2" <?php echo $ttest2checked; ?>>0.05</input> <br>
					<input type="radio" name="ttestpvalue" value="3" <?php echo $ttest3checked; ?>>0.025</input><br>
					<input type="radio" name="ttestpvalue" value="4" <?php echo $ttest4checked; ?>>0.01 </input><br>
					<input type="radio" name="ttestpvalue" value="5" <?php echo $ttest5checked; ?>>0.005</input><br>
					<input type="radio" name="ttestpvalue" value="6" <?php echo $ttest6checked; ?>>0.001</input> 
				</div>
				</td>
			<?php
			}elseif($_POST['numberGroups'] > 2 && $val == 2){
				/*pvalue:
				1 = .10
				2 = .05
				3 = .01
				
				*/
				
				if($isSavedQuery != ""){
					foreach($savedvals as $nameval){
						$temp = explode("=", $nameval);
						
						if($temp[0]== "anovapvalue"){
							$anovapvalue = $temp[1];
							break;
						}
					}
					
					if($anovapvalue == 1){
						$anova1checked = "checked";
					}elseif($anovapvalue == 2){
						$anova2checked = "checked";
					}else{
						$anova3checked = "checked";
					}
				}
				if($anovachecked == ""){
					$anovapvalues="hidden";
				}else{
					$anovapvalues="visible";
				}

			?>
				<td rowspan='2' class="questionanswer"><div id="anovapvaluecell"  style="visibility:<?php echo $anovapvalues; ?>"><u><b>p-Value</b></u><br>
					<input type="radio" name="anovapvalue" value="1" <?php echo $anova1checked; ?>>0.10</input><br>
					<input type="radio" name="anovapvalue" value="2" <?php echo $anova2checked; ?>>0.05</input> <br>
					<input type="radio" name="anovapvalue" value="3" <?php echo $anova3checked; ?>>0.01</input><br>
				</div>
				</td>
			<?php
			}elseif($_POST['numberGroups'] >= 2 && $val == 4){
				$nonechecked = "";
				$qvaluechecked ="";
				$bonferronichecked = "";
				if($isSavedQuery != ""){
					foreach($savedvals as $nameval){
						$temp = explode("=", $nameval);
						
						if($temp[0]== "correction"){
							$correctionval = $temp[1];
							break;
						}
					}
					
					if($correctionval == 1){
						$qvaluechecked = "checked";
						
					}elseif($correctionval == 1){
						$bonferronichecked = "checked";
					}else{
						$bonferronichecked = "";
						$qvaluechecked = "";
						$nonechecked  = "checked";
					}
				}
				
				if($anovapvalues != "" || $ttpvalues != ""){
					if($ttpvalues != "hidden" && $anovapvalues != "hidden"){
						$visiblecorrections = "visible";
					//	echo "visible correction : $visiblecorrections<br>anovapvalues = $anovapvalues<br>ttpvalues = $ttpvalues<br>";
					}else{
						$visiblecorrections = "hidden";
					}
					
				}else{
					$visiblecorrections = "hidden";
				}
				
				
			?>
				<td rowspan='1' class="questionanswer"><div id="corrections"  style="visibility:<?php echo $visiblecorrections; ?>"><strong><u>Corrections</u></strong><br>
					<input type="radio" name="correction" value="-1" <?php echo $nonechecked; ?>>None</input><br>
					<input type="radio" name="correction" value="1" <?php echo $qvaluechecked; ?>>rFDR</input><br>
					<input type="radio" name="correction" value="2" <?php echo $bonferronichecked; ?>>Bonferroni</input> <br><img id="correctiontooltip" src="./images/dialog-information12x12.png" align="top"/><div dojoType="dijit.Tooltip" connectId="correctiontooltip"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/>
<b><u>Using FDR (false discovery rate) q-value and bonferroni correction.</u></b> <br>

<b><h3>Bonferroni</h3></b><br>
Bonferroni is by far the most selective of the two since it uses and an adjusted alpha value based on your p-value cut-off and the number of tests being done.  <br>
For instance, if you bring back 1000 genes w/ fold-change and microarray-based threshold parameters and use a .01 p-value, the adjusted alpha value would be:
<br><br>
adjusted alpha = .01/1000 = 0.00001<br><br>
meaning instead of each hypothesis test (i.e., t-test for each gene) being rejected @ .01, the rejection threshold would be 0.00001.
<br>
<b><h3>rFDR</h3></b><br>
the rough False Discovery Rate is a little less stringent and calculates an adjusted value using the same parameters as above using this formula:
<br><br>
revised pValue = (pValue) * (number of probes meeting criteria + 1)/ (2 * number of probes meeting criteria)
</table></div></div>
			<?php
			}

			else{
				if($_POST['numberGroups'] > 2 && ($val != 3)){
				echo "<td width=\"10\"></td>";
				}
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


				echo "<td class=\"questionanswer\"><input type=\"checkBox\" id=\"group$group\" name=\"group$group\" value=\"$group\" $isChecked>Group $group</td>";
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
?>