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





require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
$db2 = mysql_connect("localhost", "root", "arod678cbc3",TRUE);
mysql_select_db("mygo", $db2);
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	//die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}
include 'header.inc';
require "formcheck2.inc";
include 'edge_update_user_activity.inc';
include 'outputimage.inc';
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
//echo "Dataset = $dataset<br>";
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

<head>
<script type="text/javascript">
 function hideSelsRow1(toHide){

 if(toHide==1){
 	show1=document.getElementById("individualoption1");
	show2=document.getElementById("individualoption2");

	show1.style.display="";
	show2.style.display="";

	hide1=document.getElementById("groupoption1");
	hide3=document.getElementById("groupoption3");

	hide1.style.display="none";
	hide3.style.display="none";

	hide1=document.getElementById("groupoption4");
	hide3=document.getElementById("groupoption5");
	hide1.style.display="none";
	hide3.style.display="none";

	hide1=document.getElementById("groupoption6");
	hide1.style.display="none";
 }
 else if(toHide==0){
 	hide1=document.getElementById("individualoption1");
	hide2=document.getElementById("individualoption2");
	hide1.style.display="none";
  	hide2.style.display="none";

	show1=document.getElementById("groupoption1");
	show3=document.getElementById("groupoption3");
	show4=document.getElementById("groupoption4");
	show5=document.getElementById("groupoption5");
	show6=document.getElementById("groupoption6");

	show1.style.display="";
	show3.style.display="";
	show4.style.display="";
	show5.style.display="";
	show6.style.display="";
 }
 else if(toHide==2){

 	show1=document.getElementById("individualoption1");
	show2=document.getElementById("individualoption2");
	show1.style.display="";
   	show2.style.display="";
	show1=document.getElementById("groupoption1");
	show3=document.getElementById("groupoption3");
	show1.style.display="";
   	show3.style.display="";

	show4=document.getElementById("groupoption4");
	show5=document.getElementById("groupoption5");
	show6=document.getElementById("groupoption6");
	show4.style.display="";
	show5.style.display="";
	show6.style.display="";
 }

}

</script>
</head>

<body onload="return hideTrxRowOnLoad()">

	<?php
		include 'banner.inc';
	?>

 <div class="boxmiddle">
 <?php

include 'questionmenu.inc';
?>

 <h3 class="contenthead"><?php echo $clusterType; ?></h3>

<div>

<?php
/*
$browser = get_browser();
$q = 0;
foreach ($browser as $name => $value) {
  if($q == 1){
	$bname = $value;
	break;
  }
 $q++;
}*/
//echo "browser = $bname<br>";
$browserval = 0;  // 0 = IE, 1 = non-IE
if(strcmp($bname, "IE")!=0){
// The browser check is used to set a flag for the clustering algorithm due to adobe svg viewers shortcomings w/ mozilla browsers...
	$browserval = 1;
}
// Just set to mozilla based...
$browserval = 1;
if (isset($_POST['submit']) && $orderingMethod == 0 || $orderedSubmit == "true") { // if form has been submitted and it is not being ordered or orderedSubmit is true...
	//analyze($_POST);
		if($orderedSubmit == "true"){
			if($_GET['savedquery'] == ""){  // If this is not a saved query
       //echo "This is not a saved query......";
				$thisNum = $_POST['querynum'];
			}else{
   // echo "This is a saved query... UPDATEING TEMP...<BR>";
				$thisNum = $_POST['tempquery'];

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
				if($_GET['tempquery'] == ""){
					$tempquery = $_POST['tempquery'];
				}
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

	$arrayidArray = array();
	$arrayDescArray = array();
	if($orderedSubmit != "true"){
		include 'clusteroutputnoorderingoftreatments_EDGE.inc';
	}
	else{

		include 'clusteroutputORDERINGoftreatments_EDGE.inc';

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
	echo "ordered individually...<BR>";
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
		ORDER BY c.chemical";
//print $chemSQL;
}
else{
 	$chemSQL = "SELECT DISTINCT chemid, chemical, trx_type FROM chem ORDER BY chemical";
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
		$savedquery = $_GET['savedquery'];
		$sql = "SELECT queryname, query1, query2opts FROM savedqueries WHERE query = $savedquery AND userid = $userid";
		//echo "$sql<br>";
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
//echo $chemSQL;
$chemCount = 1;
$envCount = 1;
$chemCount2 = 1;
$chemCount3 = 1;
$chemCount4 = 1;
$chemCount5 = 1;
$chemCount6 = 1;
$chemCount7 = 1;
$chemCount8 = 1;

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

		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		/*
		if($chemCount%3 == 0){

			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuChem .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuChem .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font size=1>$chemical</font><a 							href=\"$row[0]\" 												target=\"_blank\"><font size=1>CTD</a></font><br></td></tr>";
				}

			$chemCount=0;
			$chemCount++;
			}
		}
		else
		*/
		if($chemCount%2 == 0){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuChem .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td></tr>";
				}
				else{
					$chemMenuChem .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a href=\"$row[0]\" 						target=\"_blank\"><font size=1>CTD</font></a></td></tr>";
				}
			$chemCount++;
			}
		}
		else{
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuChem .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuChem .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount++;
			}
		}

	}
	else if($trx_type == 1){ // $trx_type != 0 and it's an environmental condition....
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuEnv .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuEnv .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}
	        }
	}

	else if($trx_type == 2){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		if($chemCount2%3 == 0){

			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuRats .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuRats .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}
			$chemCount2=0;
			$chemCount2++;
			}
		}
		else if($chemCount2%2 == 0){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuRats .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuRats .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a href=\"$row[0]\" 						target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount2++;
			}
		}
		else if($chemCount2%2 == 1){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuRats .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuRats .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font<a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount2++;
			}
		}
	}

	else if($trx_type == 3){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		if($chemCount3%2 == 0){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuPhyStates .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuPhyStates .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><a href=\"$row[0]\" 										target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}

			$chemCount3=0;
			$chemCount3++;
			}
		}
		else{
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuPhyStates .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuPhyStates .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount3++;
			}
		}
	}

	else if($trx_type == 4){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		if($chemCount4%2 == 0){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuMutantMice .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuMutantMice .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><a href=\"$row[0]\" 										target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}

			$chemCount4=0;
			$chemCount4++;
			}
		}
		else{
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuMutantMice .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuMutantMice .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount4++;
			}
		}
	}
	else if($trx_type == 5){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuTransgenicMice .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuTransgenicMice .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><a href=\"$row[0]\" 										target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}
	        }
	}

	else if($trx_type == 6){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		if($chemCount6%2 == 0){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuCellLines .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuCellLines .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><a href=\"$row[0]\" 										target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}

			$chemCount6=0;
			$chemCount6++;
			}
		}
		else{
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuCellLines .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuCellLines .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount6++;
			}
		}
	}

	else if($trx_type == 7){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuTumors .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuTumors .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}
	        }
	}

	else if($trx_type == 8){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuMixtures .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuMixtures .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><a href=\"$row[0]\" 										target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}
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

// For a person without admin priviledges, dont show the 'drinkwater' tab... hence dont show class with classid=99
if($priv != 99){
$classSQL = "SELECT DISTINCT(class.classid), class.name FROM class,chemclass WHERE chemclass.class = class.classid AND  class.classid!=99 ORDER BY class.name ASC";
}
else{
$classSQL = "SELECT DISTINCT(class.classid), class.name FROM class,chemclass WHERE chemclass.class = class.classid ORDER BY class.name ASC";
}

$classResult = mysql_query($classSQL, $db);
$maxClassID = 0;
$i = 0;
$uniqueClassArray = array();  // This array stores the classes returned from the above query (in alphabetical order)....

while(list($classid, $name) = mysql_fetch_array($classResult)){

	$uniqueClassArray[$i] = $classid;
	if($i == 0){
		//$divArray[$i][0] = "<div style=\"display: block;\" id=\"section$i\">"; // dont show chem for default opt
		$divArray[$i][0] = "<div style=\"display: none;\"  min-height:100px;  id=\"section$i\"><br><br><br>";
	}
	else{
		$divArray[$i][0] = "<div style=\"display: none;\" min-height:100px;   id=\"section$i\"><br><br><br>";
	}

	$chemDivList .= "<li><a href=\"#indiv\" onclick=\"show_div('section$i',$divVal); return false;\" tabindex=\"$i\"><font size=2>$name</font></a></li>";
	$i++;

}

// The following variable is used to keep track of what div we're at....
$i = 0;
foreach($uniqueClassArray as $thisClassID){
	$acounter = 1;
	// Get all of the chemids associated w/ the current class...
	$sql = "SELECT chemid FROM chemclass WHERE class = $thisClassID";
	//echo "$sql <br>";
	$result = mysql_query($sql, $db);
	// ITERATE THROUGH EACH CHEMICAL ID BELONGING TO THIS CLASS.....
		while($row = mysql_fetch_row($result)){
			// GET all entries from sampledata that correspond to this chemid....
			// Order by arrayid
			$id = $row[0];


				// GET THE ARRAYS NOT ASSIGNED TO ANY EXPERIMENTS..................
				if($priv != 99){
					$chemSQL = "SELECT s.sampleid, s.treatment, s.chemid, a.arraydesc, c.trx_type  FROM sampledata AS s, chem as c, array AS a LEFT JOIN experiments AS e ON s.sampleid = e.arrayid WHERE
					(a.ownerid = $priv OR a.ownerid = 1) AND c.chemid = s.chemid AND s.chemid = $id AND a.arrayid = s.sampleid AND e.arrayid IS NULL ORDER BY a.arraydesc";
				//print $chemSQL;
				}
				else{
		$chemSQL = "SELECT s.sampleid, s.treatment, s.chemid, a.arraydesc, c.trx_type  FROM sampledata AS s, chem as c, array AS a LEFT JOIN experiments AS e ON s.sampleid = e.arrayid WHERE
									c.chemid = s.chemid AND s.chemid = $id AND a.arrayid = s.sampleid AND e.arrayid IS NULL ORDER BY a.arraydesc";

				}
					$chemResult = mysql_query($chemSQL, $db);
					
					

					echo "<br>$chemSQL<br>";
					$arraycount = 1;
					$oldtreat = "";
					$counter = 0;
					while(list($trxid1, $treat, $chemid1, $trxdesc1, $type) = mysql_fetch_array($chemResult)){
						$counter++;

					$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid1";
					//print $chemurlSQL;
					$chemurlResult = mysql_query($chemurlSQL, $db);
	
					if($acounter == 1){
						// need to set the first treatment type...
						$oldtreat = $treat;
						//*** Get the info links associated with this chem from chemURL
						//$link = mysql_fetch_array($chemurlResult);
					    while($row = mysql_fetch_row($chemurlResult)){
						if(strcmp($row[0],"NULL")==0){
						$divArray[$i][$acounter] = "<br><fieldset><legend><strong>$treat</strong> 					       </legend>";
						}
						else{
						$divArray[$i][$acounter] = "<fieldset><legend><strong>$treat</strong> 					       <a href=\"$row[0]\" target=\"_blank\"><strong>CTD</strong></a></legend>";
						}
					    }
						$acounter++;
					}

					if($oldtreat != $treat){
						$oldtreat = $treat;
						//*** Get the info links associated with this chem from chemURL
						$divArray[$i][$acounter] = "</fieldset>";
						$acounter++;
						while($row = mysql_fetch_row($chemurlResult)){
						if(strcmp($row[0],"NULL")==0){
						$divArray[$i][$acounter] = "<br><fieldset><legend><strong>$treat</strong> 					       </legend>";
						}
						else{
						$divArray[$i][$acounter] = "<fieldset><legend><strong>$treat</strong>   	<a href=\"$row[0]\" target=\"_blank\"><strong>CTD</strong></a></legend>";
						}
						}
						$acounter++;
					}
					$checked = "";
					$trxid1 .= a;
					if($_GET['savedquery'] != ""){
						foreach($savedtrxvals as $trxval){
							if($trxval == $trxid1){
								$checked = "checked";
							}
						}
					}
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

				if($arraycount == 1){
					// There are no arrays that are not assigned to an experiment (ie. all arrays assigned to an exeriment...
					$allassigned = 1;
				}
				else{
					$allassigned = 0;
				}
				// Check to see if there are experiments...
				if($priv != 99){
				//echo "<br>THE CHEMICAL ID IS: $id<br>";
				$sql = "SELECT DISTINCT(e.arrayid), a.arraydesc, ed.expname FROM experiments AS e, experimentsdesc AS ed, array AS a
							WHERE e.arrayid = a.arrayid AND (a.ownerid = $priv OR a.ownerid = 1) AND e.chemid = $id AND ed.expid = e.expid ORDER BY ed.expname";
				}
				else{
				//echo "<br>THE CHEMICAL ID IS: $id<br>";
					$sql = "SELECT DISTINCT(e.arrayid), a.arraydesc, ed.expname FROM experiments AS e, experimentsdesc AS ed, array AS a WHERE e.arrayid = a.arrayid AND e.chemid = $id AND ed.expid = e.expid ORDER BY ed.expname";
				}
				$expResult = mysql_query($sql, $db);


				// ITERATE THROUGH EACH EXPERIMENT...... BASED ON THE ARRAY DESCRIPTION....
				$oldexpname = "";
				while(list($trxid1, $arraydesc, $expname) = mysql_fetch_array($expResult)){
				//echo "<br>$sql<br>";
				if($oldexpname == ""){
				// need to set the first treatment type...
					if($allassigned == 1){
					// If all arrays are assigned to an experiment, need to close the previous chemical and
					// create a new fieldset w/ legen of s.treatment for this particular chemical
					$sql = "SELECT s.treatment FROM sampledata AS s WHERE s.chemid = $id";
					$aResult = mysql_query($sql, $db);
					$row = mysql_fetch_row($aResult);
					$divArray[$i][$acounter] = "</fieldset>";
					$acounter++;

					$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$id";
					$chemurlResult = mysql_query($chemurlSQL, $db);


					while($row1 = mysql_fetch_row($chemurlResult)){
						if(strcmp($row1[0],"NULL")==0){
						$divArray[$i][$acounter] = "<fieldset><legend><strong>$row[0]</strong></legend>";
						}
						else{
						$divArray[$i][$acounter] = "<fieldset><legend><strong>$row[0]</strong><a href=\"$row1[0]\" target=\"_blank\"><strong>CTD</strong></a></legend>";
						}
					}

					$acounter++;
					$allassigned = 0;
					}
				$oldexpname = $expname;
	$divArray[$i][$acounter] = "<fieldset><legend><strong><font color=\"gray\">$expname</font></strong></legend>";
				$acounter++;
				}
						if($oldexpname != $expname){
							$oldexpname = $expname;
							$divArray[$i][$acounter] = "</fieldset>";
							$acounter++;
							$divArray[$i][$acounter] = "<fieldset><legend><strong><font color=\"gray\">$expname</font></strong></legend>";
							$acounter++;
						}
						$checked = "";
						$trxid1 .= a;
						if($_GET['savedquery'] != ""){
							foreach($savedtrxvals as $trxval){
								if($trxval == $trxid1){
									$checked = "checked";
								}
							}
						}

						$trxMenu = "$arraydesc  <input type=\"checkbox\" name=\"trx$trxid1\" value=\"$trxid1\" $checked>  ";
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

				$noclose = 0;

				if($oldexpname != ""){
						$divArray[$i][$acounter] = "</fieldset>";  // NEED TO CLOSE THE LAST FIELDSET FOR THE CURRENT experiment
						$acounter++;
						$noclose = 1;
				}
				$thisCount = $arraycount - 1;
				if($oldexpname == ""){
					//$divArray[$i][$acounter] = "</fieldset>";  // NEED TO CLOSE THE LAST FIELDSET FOR THE CURRENT CHEMICAL
					//$acounter++;
					$nofieldset = 1;
				}


		}


		if($nofieldset != 1){
			//$divArray[$i][$acounter] = "</fieldset>";  // NEED TO CLOSE THE LAST FIELDSET FOR THE CURRENT CLASS DIV
		}

	$acounter++;
	$divArray[$i][$acounter] = "</div>";
	$i++; // increment the divArray counter....

}


?>
<p class="styletext">
<form name="query" method="post" onsubmit="return checkClusteringForm()" action="<?php  $_SERVER['PHP_SELF'] ?>">

<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2">Query Parameters</th>
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
<input type="radio" name="clusterAlgo" value="1" <?php echo $hierChecked; ?> onclick="return hideTrxRowEdge2(0)"> Hierarchical<br>
<input type="radio" name="clusterAlgo" value="0" <?php echo $kChecked; ?> onclick="return hideTrxRowEdge2(1)">K-Means<br>
</td>
<td valign="top" class="results" rowspan="2">
<ul id="globalnav">
	<li><a href="#indiv" onclick="show_querydiv('querysection0',2); return false;" tabindex="0">Recent Queries</a></li>
	<li><a href="#indiv" onclick="show_querydiv('querysection1',2); return false;" tabindex="1">Saved Queries</a></li>
</ul>
<br>
<p>
<div style="display: block;" id="querysection0" class="scroll">
<?php
	if($userid != ""){
	// GET THE THREE MOST RECENT QUERIES.....
	$sql = "SELECT query FROM savedqueries WHERE userid = $userid AND (queryname IS NULL AND (query1 IS NOT NULL AND query1 != \"\"))  ORDER BY querydate DESC LIMIT 3";
	$sqlResult = mysql_query($sql, $db);
	$recentCount=1;
	while($row = mysql_fetch_row($sqlResult)){
		echo "<a href=\"./clustering.php?savedquery=$row[0]\">Unsaved #$recentCount</a><br>";
		$recentCount++;
	}
	}else{
		echo "<b>Create a login in order <br>to use the save queries feature!</b>";
	}
?>
</div>
<div  style="display: none;" id="querysection1" class="scroll">
<?php
	if($userid !=""){// GET THEIR SAVED QUERIES.....
	$sql = "SELECT query, queryname FROM savedqueries WHERE userid = $userid AND queryname IS NOT NULL ORDER BY querydate DESC";
	$sqlResult = mysql_query($sql, $db);
	while($row = mysql_fetch_row($sqlResult)){
		echo "<a href=\"./clustering.php?savedquery=$row[0]\">$row[1]</a><br>";
	}
	}else{
			echo "<b>Create a login in order <br>to use the save queries feature!</b>";
	}

?>
</div>
<br>

</p>
</td>
</tr>
<?php
	// IF THIS IS A SAVED QUERY, WE'VE GOT TO HAVE A VALUE FOR THIS..
	echo "<input name=\"savedquery\" type=\"hidden\" value=\"$savedquery\">\n";
	// IF A TEMP query's involved, gotta have that....
	echo "<input name=\"tempquery\" type=\"hidden\" value=\"$tempquery\">\n";


	if($priv <=999){
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
		$notcondChecked = "";
		$condChecked = "checked";
	}
?>

	<tr>
<td class="questionparameter" ><strong>Data Options:</strong></td>
<td class="results">
<input type="radio" name="dataset" value="1" <?php echo $notcondChecked; ?>>Not Condensed<br>
<input type="radio" name="dataset" value="0" <?php echo $condChecked; ?>><strong><font color="red">Condensed</font></strong><br>
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
<input type="radio" name="trxCluster" value="1" <?php echo $clusterChecked; ?> onclick="return hideOrderRowsEdge2(0)">Cluster Treatments</input><br>
<?php
/*
<input type="radio" name="trxCluster" value="2" onclick="return hideOrderRows(0)">Cluster, top dendrogram only</input><br>
<input type="radio" name="trxCluster" value="3" onclick="return hideOrderRows(0)">Cluster, bottom dendrogram only</input><br>
*/
?>
<input type="radio" name="trxCluster" value="0" <?php echo $noclusterChecked; ?> onclick="return hideOrderRowsEdge2(1)">Custom Order/Name Treatments (No Clustering)</input><br>
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
		$chemcon = "";
		$indiv = "";
		$both = "checked";
	}
?>

<input type="radio" name="seloptions" value="1" <?php echo $chemcon; ?> onclick="return hideSelsRow1(0) "> Chemical(s)/Condition(s) Group<br>
<input type="radio" name="seloptions" value="0" <?php echo $indiv; ?> onclick="return hideSelsRow1(1) ">Treatment Groups <br>
<input type="radio" name="seloptions" value="2" <?php echo $both; ?> onclick="return hideSelsRow1(2) "> Both Options<br>

</td>
<td class="results">

</td>
</tr>


<tr id="groupoption1">
<td  class="questionanswer" colspan="3"><strong>Chemical(s)/Condition(s)</strong></td>
</tr>

<tr id="groupoption3">
<td class="questionparameter" colspan="3">
<fieldset align="top">
  <legend><strong><i>Chemical Treatments</i></strong></legend>
  <table>
  	<?php echo $chemMenuChem; ?>
  </table>
</fieldset>
</td>
</tr>

<tr id="groupoption4">
<td class="questionparameter" align="top">
  <fieldset>
  <legend><strong><i>Conditions/Vehicle Treatments</i></strong></legend>
  <table>
	<?php echo $chemMenuEnv; ?>
  </table>
  </fieldset>
</td>
<td class="questionparameter" align="top" colspan="1">
<fieldset>
  <legend><strong><i>Physiologic States</i></strong></legend>
  <table>
	<?php echo $chemMenuPhyStates; ?>
  </table>
</fieldset>
</td>
<td class="questionparameter" colspan="1">
<fieldset align="top">
  <legend><strong><i>Rats</i></strong></legend>
  <table>
  	<?php echo $chemMenuRats; ?>
  </table>
</fieldset>
</td>
</tr>

<tr id="groupoption5">
<td class="questionparameter" colspan="2">
<fieldset align="top">
  <legend><strong><i>Mutant Mice</i></strong></legend>
  <table>
  	<?php echo $chemMenuMutantMice; ?>
  </table>
</fieldset>
</td>
<td class="questionparameter" align="top">
<fieldset>
  <legend><strong><i>Transgenic Mice</i></strong></legend>
  <table>
	<?php echo $chemMenuTransgenicMice; ?>
  </table>
</fieldset>
</td>
</tr>

<tr id="groupoption6">
<td class="questionparameter" colspan="1">
<fieldset align="top">
  <legend><strong><i>Cell Lines</i></strong></legend>
  <table>
  	<?php echo $chemMenuCellLines; ?>
  </table>
</fieldset>
</td>
<td class="questionparameter" align="top">
<fieldset>
  <legend><strong><i>Mixtures</i></strong></legend>
  <table>
	<?php echo $chemMenuMixtures; ?>
  </table>
</fieldset>
</td>
<td class="questionparameter" align="top">
<fieldset>
  <legend><strong><i>Tumors</i></strong></legend>
  <table>
	<?php echo $chemMenuTumors; ?>
  </table>
</fieldset>
</td>
</tr>


<tr id="individualoption1">
<td  class="questionanswer" colspan="3"><strong>Treatment Groups</strong> (Click on a group to pick chemicals listed under it)</td>
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
			echo "	$val\n";

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
<td class="questionparameter" ><strong>Heat Map Image Output:</strong></td>
<td class="results">
<?php
// what colorscheme is checked???
if($_GET['savedquery'] != ""){

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


<tr>
<td  class="questionanswer" colspan="3"><strong>Threshold Values:</strong></td>
</tr>
<tr>
<tr>
<td class="questionparameter" ><strong>Minimum Induction:</strong></td>
<td class="results">
<?php

	if($_GET['savedquery'] != ""){
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

	if($_GET['savedquery'] != ""){
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
<?php
if($_SESSION['priv_level'] >= 99){
	if($_GET['savedquery'] != ""){
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
}
?>

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
