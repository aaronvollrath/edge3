<?php

/***
Location: /edge2/admin
Description:  This page is used to first select the treatment to edit and then to display the treatment and
		its associated values so that they can be modified or so the treatment itself can be deleted
		from the database.
POST:
	FORM NAME: "selecttrx" ACTION: "edittrx.php" METHOD: "post" ONSUBMIT: ""
	FUNCTION: Used to select the treatment to edit.
	ITEMS:  'submitted', 'trx' <radio>

	FORM NAME: "commitchanges" ACTION: "edittrx.php" METHOD: "post" ONSUBMIT: "return checkChangesForm()"
	FUNCTION: Used to modify (or delete) the treatment.
	ITEMS:  'committed', 'submitted', 'trx', 'userid', 'delete' <radio>,
		'samplename1', 'samplename', 'pubinfo1', 'pubinfo', 'treatment1', 'chemical' <radio>,
		'organism1', 'organism' <radio>, 'rnagroupsize1', 'rnagroupsize', 'strain1', 'strain', 'genevariation1',
		'genevariation' <radio>, 'age1', 'age', 'sex1', 'sex' <radio>, 'tissue1', 'tissue' <radio>, 'vehicle1',
		'vehicle' <radio>, 'dose1', 'dose', 'doseunits1', 'doseunit' <radio>, 'route1', 'route' <radio>,
		'control1', 'control' <radio>, 'dosagetime1', 'dosehours', 'doseminutes', 'harvesttime1', 'harvesthours', 'harvestminutes',
		'duration1', 'duration', 'durationunits1', 'durationunit' <radio>, 'visible' <radio>, 'accessnumber1', 'accessnumber',
		'commit'
GET: none
Files include or required: 'edge_db_connect2.php', 'newtreatmentcheck.inc', 'edge_update_user_activity.inc', 'trxmenus.php', 'adminmenu.inc','../adminleftmenu.inc'
***/
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}

$db = mysql_connect("localhost", "vollrath", "arod678cbc3");

mysql_select_db("edge", $db);
include 'edge_update_user_activity.inc';
require "newtreatmentcheck.inc";

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

?>

<?php
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="../css/newlayout.css" title="layout" />
<title>EDGE^2</title>

<script type="text/javascript">
	function changeVal($var, $val)
	{
	$var = $val;
	}
</script>

</head>
<script src="../sorttable.js"></script>
<body>

	<div class="boxheader">
		<img src="../GIFs/EDGE2128x60.png" alt="Edge^2" align="left"></img>
		<img src="../GIFs/edgebanner.jpg" alt="environment" width="90" height="75" align="left"></img>
		<h2 class="bannerhead" align="bottom">Environment, Drugs and Gene Expression</h2>
	</div>

 <div class="boxmiddle">

 <?php
include 'adminmenu.inc';
?>


	<h3 class="contenthead">Edit/Delete a treatment</h3>
<?php
$priv_level = $_SESSION['priv_level'];
if($priv_level != 99){
	echo "Sorry, you are not authorized to access this page.";
	}
else{
	//$submitted = $_POST['submitted'];
	$submitted = $_POST['submitted'];
	//echo "SUBMITTED = $submitted<br>";
	///analyze($_POST);
	$submitted2 = false; //$_POST['submitMultiple'];
	$trx = $_POST['trx'];
	// Nandita: I removed the second conditional check, because it was causing things to not work. -aaron
	if($submitted != true /*&& $submitted2 != true*/){ // if the trx selection form has not been submitted....
?>

  <?php

		// Get the treatment id's and treatment names....

		$trxSQL = "SELECT sampleid, treatment, tissue, dose, doseunits, duration, durationunits FROM sampledata ORDER BY sampleid ASC";

		$trxResult = mysql_query($trxSQL, $db);
		$firstchoice = 1;
		$tablerows = "";
		while(list($trx, $trxname, $tissue, $dose, $doseunits, $duration, $durationunits) = mysql_fetch_array($trxResult))
		{
			$tablerows .= "<tr>";
			if($firstchoice == 1){

				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"trx\" value=\"$trx\" checked>$trx</td>";
				$tablerows .= "<td class=\"results\">$trxname</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$firstchoice = 0;
			}
			else{
				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"trx\" value=\"$trx\">$trx</td>";
				$tablerows .= "<td class=\"results\">$trxname</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			}
			$tablerows .= "</tr>\n";
		}




?>

<form name="selecttrx" action="edittrx.php" method="post">
<input type="hidden" name="submitted" value="true">
<input type="hidden" name="submitted2" value="true">
<table id="results" class="sortable">
  <col width=10 align="center">
  <col width=10 align="center">
  <col width=128 align="left">
  <col width=128 align="left">
  <col width=128 align="left">
  <col width=256 align="left">

<thead>
<!--<tr>
<th class="mainheader" colspan="5">Edit/Delete Treatment Form</th>
</tr>
-->
<tr>
  <!--<th scope=col class="subhead"></th>-->
  <th scope=col class="subhead" abbr="number">Trx ID#</th>
  <th scope=col class="subhead" abbr="alpha">Treatment</th>
  <th scope=col class="subhead" abbr="alpha">Tissue</th>
  <th scope=col class="subhead" abbr="number">Dose</th>
  <th scope=col class="subhead" abbr="number">Duration</th>
</tr>
</thead>

<!--
<tr>
<td class="questionanswer" >Trx ID#</td>
<td class="questionanswer" >Treatment</td>
<td class="questionanswer" >Tissue</td>
<td class="questionanswer" >Dose</td>
<td class="questionanswer" >Duration</td>
</tr>
-->

<?php
// We created the rows above, now insert them into the table...
	//echo "before echo";
	echo $tablerows;
	//echo "after echo";
?>


<tr>
<td><input type="submit" name="submit" value="Edit"></td>
<td></td>
<!--<td><input type="submit" name="submitMultiple" value="Edit Multiple Treatments at once"></td>-->
<td></td>
<td></td>
<td></td>
</tr>

</table>
</form>
<?php
	}  // end if not submitted....
	else if($submitted == true){ // The treatment form has been submitted for single treatment edit....

	//analyze($_POST);
	$committed = $_POST['committed'];
	$trx = $_POST['trx'];
	$userid = $_POST['userid'];
	$delete = $_POST['delete'];
	$samplename1 = $_POST['samplename1'];
	$samplename = $_POST['samplename'];
	$originator1 = $_POST['originator1'];
	$originator = $_POST['originator'];
	$organization1 = $_POST['organization1'];
	$organization = $_POST['organization'];
	$treatment1 = $_POST['treatment1'];
	$chemical = $_POST['chemical'];
	$organism1 = $_POST['organism1'];
	$organism = $_POST['organism'];
	$rnagroupsize1 = $_POST['rnagroupsize1'];
	$rnagroupsize = $_POST['rnagroupsize'];
	$strain1 = $_POST['strain1'];
	$strain = $_POST['strain'];
	$genevariation1 = $_POST['genevariation1'];
	$genevariation = $_POST['genevariation'];
	$age1 = $_POST['age1'];
	$age = $_POST['age'];
	$sex1 = $_POST['sex1'];
	$sex = $_POST['sex'];
	$tissue1 = $_POST['tissue1'];
	$tissue = $_POST['tissue'];
	$vehicle1 = $_POST['vehicle1'];
	$vehicle = $_POST['vehicle'];
	$dose1 = $_POST['dose1'];
	$dose = $_POST['dose'];
	$doseunits1 = $_POST['doseunits1'];
	$doseunit = $_POST['doseunit'];
	$route1 = $_POST['route1'];
	$route = $_POST['route'];
	$control1 = $_POST['control1'];
	$control = $_POST['control'];
	$dosagetime1 = $_POST['dosagetime1'];
	$dosehours = $_POST['dosehours'];
	$doseminutes = $_POST['doseminutes'];
	$harvesttime1 = $_POST['harvesttime1'];
	$harvesthours = $_POST['harvesthours'];
	$harvestminutes = $_POST['harvestminutes'];
	$duration1 = $_POST['duration1'];
	$duration = $_POST['duration'];
	$durationunits1 = $_POST['durationunits1'];
	$durationunit = $_POST['durationunit'];
	$visible = $_POST['visible'];
	$accessnumber1 = $_POST['accessnumber1'];
	$accessnumber = $_POST['accessnumber'];
	$hybrid1 = $_POST['hybrid1'];
	$hybrid = $_POST['hybrid'];


		if($committed != "true"){
		// Need to get the values associated w/ this treatment....
		$sql = "SELECT chemid, organism, originator, originatorOrg, rnagroupsize, strain, genevariation, age, 				sex, tissue, treatment,vehicle, dose, doseunits, route, dosagetime, duration, durationunits, 				harvesttime, control, hybrid
			FROM sampledata WHERE sampleid = $trx";
		$trxResult = mysql_query($sql, $db);
		list($chemid1, $organism1, $originator1, $organization1,$rnagroupsize1, $strain1, $genevariation1, $age1, 		$sex1,$tissue1, $treatment1, $vehicle1, $dose1, $doseunits1, $route1, $dosagetime1, $duration1, 			$durationunits1, $harvesttime1, $control1, $hybrid1) = mysql_fetch_array($trxResult);
  //echo "The tissue is: $tissue1<br>";

		$sql = "SELECT arraydesc, ownerid FROM array WHERE arrayid = $trx";
		$trxResult = mysql_query($sql, $db);
		list($samplename1, $accessnumber1) = mysql_fetch_array($trxResult);

		if($accesnumber > 1){
			$visible = 0;
		}
		else{
			$visible = 1;
		}

		$sql = "SELECT pubinfo FROM sampleinfo WHERE sampleid = $trx";
		$trxResult = mysql_query($sql, $db);
		list($pubinfo1) = mysql_fetch_array($trxResult);



		// SQL calls for menus.......
		/********************************************************************/
		require("trxmenus.php");
?>
	<form name="commitchanges" action="edittrx.php" method="post" onsubmit="return checkChangesForm()">
	<input type="hidden" name="committed" value="true">
	<input type="hidden" name="submitted" value="true">
	<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="2">Original Values</th>
		<th class="mainheader" colspan="2">Delete/Change this Treatment?</th>
		</tr>
		<tr>
		<td class="questionparameter" ><strong>Treatment ID:</strong></td>
		<td class="results">
		<input type="hidden" name="trx" value="<?php echo $trx; ?>">
		<input type="hidden" name="userid" value="<?php echo $userid; ?>">
		<?php echo $trx; ?>
		</td>
		<td bgcolor="red">
			<input type="radio" name="delete" value="N" checked>No
			<input type="radio" name="delete" value="Y">Yes
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Sample Name:</strong></td>
		<td class="results">
		<input type="hidden" name="samplename1" value="<?php echo $samplename1; ?>">
		<?php echo $samplename1; ?>
		</td>
		<td class="questionanswertext">
			<input name="samplename" type="text" value="<?php echo $samplename1;?>" align="left"></input><font color="red"><strong>*</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Originator's ID:</strong></td>
		<td class="results">
		<input type="hidden" name="originator1" value="<?php echo $originator1; ?>">
		<?php echo $originator1; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="originator" type="text" value="<?php echo $originator1; ?>" align="left"></input>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Originator's Organization:</strong></td>
		<td class="results">
		<input type="hidden" name="organization1" value="<?php echo $organization1; ?>">
		<?php echo $organization1; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="organization" type="text" value="<?php echo $organization1; ?>" align="left"></input>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Chemical:</strong></td>
		<td class="results">
		<input type="hidden" name="treatment1" value="<?php echo $treatment1; ?>">
		<?php echo $treatment1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $chemMenu; ?>
		</td>
		</tr>
		<tr>
		<td class="questionparameter" ><strong>Organism:</strong></td>
		<td class="results">
		<input type="hidden" name="organism1" value="<?php echo $organism1; ?>">
		<?php echo $organism1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $organismMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>RNA Group Size:</strong></td>
		<td class="results">
		<?php echo $rnagroupsize1; ?>
		</td>
		<td  class="questionanswertext" >
			<input type="hidden" name="rnagroupsize1" value="<?php echo $rnagroupsize1; ?>">
			<input name="rnagroupsize" type="text" value="<?php echo $rnagroupsize1; ?>" align="right"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Strain:</strong></td>
		<td class="results">
		<input type="hidden" name="strain" value="<?php echo $strain; ?>">
		<?php echo $strain1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $strainMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Genetic Variation:</strong></td>
		<td class="results">
		<input type="hidden" name="genevariation1" value="<?php echo $genevariation1; ?>">
		<?php echo $genevariation1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $genevariationMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Age:</strong></td>
		<td class="results">
		<?php echo $age1; ?>
		</td>
		<td  class="questionanswertext" >
			<input type="hidden" name="age1" value="<?php echo $age1; ?>">
			<input name="age" type="text" value="<?php echo $age1; ?>" align="right"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Sex:</strong></td>
		<td class="results">
		<input type="hidden" name="sex1" value="<?php echo $sex1; ?>">
		<?php echo $sex1; ?>
		</td>
		<td  class="questionanswertext" >
			<input type="radio" name="sex" value="0" checked>No Change
			<input type="radio" name="sex" value="M">Male
			<input type="radio" name="sex" value="F"> Female
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Tissue:</strong></td>
		<td class="results">
		<input type="hidden" name="tissue1" value="<?php echo $tissue1; ?>">
		<?php echo $tissue1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $tissueMenu; ?>
		</td>
		</tr>


		<tr>
		<td class="questionparameter" ><strong>Vehicle:</strong></td>
		<td class="results">
		<input type="hidden" name="vehicle1" value="<?php echo $vehicle1; ?>">
		<?php echo $vehicle1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $vehicleMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Dose:</strong></td>
		<td class="results">
		<input type="hidden" name="dose1" value="<?php echo $dose1; ?>">

		<?php echo "$dose1"; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="dose" type="text" value="<?php echo $dose1; ?>" align="right"></input><font color="blue"><strong>#</strong></font>

		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Dose Units:</strong></td>
		<td class="results">
		<input type="hidden" name="doseunits1" value="<?php echo $doseunits1; ?>">
		<?php echo $doseunits1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $doseunitMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Route:</strong></td>
		<td class="results">
		<input type="hidden" name="route1" value="<?php echo $route1; ?>">
		<?php echo $route1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $routeMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Control:</strong></td>
		<td class="results">
		<input type="hidden" name="control1" value="<?php echo $control1; ?>">
		<?php echo $control1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $controlMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Dosage Time:</strong></td>
		<td class="results">
		<input type="hidden" name="dosagetime1" value="<?php echo "$dosagetime1"; ?>">
		<?php echo "$dosagetime1"; ?>
		</td>
		<?php
			// Get the hours and mins for dosage time from $dosagetime1....
			$pieces = explode(":", $dosagetime1);
			$hours = $pieces[0];
			$minutes = $pieces[1];
		?>
		<td  class="questionanswertext" >
			<input name="dosehours" type="text" value="<?php echo $hours; ?>" align="right" size="2"></input>:
			<input name="doseminutes" type="text" value="<?php echo $minutes; ?>" align="right" size="2"></input><font color="blue"><strong>#</strong></font><strong><font color="red">&nbsp&nbsp &nbsp&nbsp Note:</font>&nbsp 01:59 = 1:59am  12:00 = noon  23:59 = 11:59pm  00:00 = midnight</strong>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Harvest Time:</strong></td>
		<td class="results">
		<input type="hidden" name="harvesttime1" value="<?php echo "$harvesttime1"; ?>">
		<?php echo "$harvesttime1"; ?>
		</td>
		<?php
			// Get the hours and mins for dosage time from $dosagetime1....
			$pieces = explode(":", $harvesttime1);
			$hours = $pieces[0];
			$minutes = $pieces[1];
		?>
		<td  class="questionanswertext" >
			<input name="harvesthours" type="text" value="<?php echo $hours; ?>" align="right" size="2"></input>:
			<input name="harvestminutes" type="text" value="<?php echo $minutes; ?>" align="right" size="2"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Duration:</strong></td>
		<td class="results">
		<input type="hidden" name="duration1" value="<?php echo "$duration1"; ?>">
		<?php echo "$duration1"; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="duration" type="text" value="<?php echo $duration1; ?>" align="right"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>

		<td class="questionparameter" ><strong>Duration Units:</strong></td>
		<input type="hidden" name="durationunits1" value="<?php echo "$durationunits1"; ?>">
		<td class="results"><?php echo $durationunits1; ?></td>
		<td  class="questionanswertext" >
			<?php echo $durationunitMenu; ?>
			</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Hybridized against:</strong></td>
		<td class="results">
		<input type="hidden" name="hybrid1" value="<?php echo $hybrid1; ?>">
		<?php echo $hybrid1; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="hybrid" type="text" value="<?php echo $hybrid1; ?>" align="left"></input>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Visible to Public:</strong></td>
		<td class="results">
		<?php
			$visiblestr = "No";
			if($visible == 1){
				$visiblestr = "Yes";
			}
		?>
		<?php echo "$visiblestr"; ?>
		<br><font color="red"><strong>You must select "NO" in order for any values entered<br> in the Access Number field below to take effect when<br> entering a specific user's access number!!!!<br>  If you don't, they won't be able to access their data<br> and Aaron will have to fix things!</strong></font>
		</td>
		<td class="questionanswertext" >
			<input type="radio" name="visible" value="-1" checked>No Change
			<input type="radio" name="visible" value="1" onchange="changeAccessNumberEditTrx()">Yes
			<input type="radio" name="visible" value="0" onchange="changeAccessNumberEditTrx()"> No
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Access Number:</strong></td>
		<td class="results">
		<input type="hidden" name="accessnumber1" value="<?php echo $accessnumber1; ?>">
		<?php echo $accessnumber1; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="accessnumber" type="text" value="<?php echo $accessnumber1; ?>" align="right"></input><font color="blue"><strong>#</strong>&nbsp&nbsp<font color="red"><strong>Change valid only if Visible to Public value is changed!</strong></font>
		</td>
		</tr>


	</table>
	<tr>
	<?php
			echo "<td><input type=\"submit\" name=\"commit\" value=\"Commit changes to Database?\"></td>";
	?>
		<td></td>
	<td><INPUT TYPE="button" VALUE="Do nothing!" onClick="history.go(-1)"></td>
	</tr>

	</form>
<?php
	}else{ // has been committed....
		// Need to determine whether or not anything has been changed.....
		if($delete == "Y"){
			$sql = "DELETE FROM sampledata WHERE sampleid = $trx";
			$delResult = mysql_query($sql, $db);
			$sql = "DELETE FROM sampleinfo WHERE sampleid = $trx";
			$delResult = mysql_query($sql, $db);
			$sql = "DELETE FROM array WHERE arrayid = $trx";
			$delResult = mysql_query($sql, $db);
			$sql = "DELETE FROM hybrids WHERE arrayid = $trx";
			$delResult = mysql_query($sql, $db);
			$sql = "DELETE FROM condensedhybrids WHERE arrayid = $trx";
			$delResult = mysql_query($sql,$db);
		}
		else{
			// Has anything been changed????
			// Check the status of each radio button or field...
			// This is used to hold the changes made to the sampledata table for this trx id
			$sdchangesarray = array();
			// This is used to hold the changes made to the sampleinfo table for this trx id
			$sichangesarray = array();
			// This is used to hold the changes made to the sampleinfo table for this trx id
			$arraychangesarray = array();
			// This is used to hold the changes made to the sampleinfo table for this trx id
			$hybchangesarray = array();
			$changesstr = "";
			if(strcmp($samplename1, $samplename) != 0){
				// need to update the sample name
				$changesstr .= "Treatment name changed from <font color=\"green\"><strong>$samplename1</strong></font> to <font color=\"red\"><strong>$samplename</strong></font> <br>";
				$sql = "samplename = \"$samplename\"";
				array_push($sichangesarray, $sql);
				$sql = "arraydesc = \"$samplename\"";
				array_push($arraychangesarray, $sql);
			}
			if($chemical != 0){
				// Get the chemid, look up and assign the new treatment...
				$chemSQL = "SELECT chemical FROM chem WHERE chemid=$chemical";
				$chemResult = mysql_query($chemSQL, $db);
				$row = mysql_fetch_row($chemResult);
				$treatment = $row[0];
				$changesstr .= "Treatment changed from <font color=\"green\"><strong>$treatment1</strong></font> to <font color=\"red\"><strong>$treatment</strong></font> <br>";
				$chemidsql = "chemid = $chemical";
				$treatmentsql = "treatment = \"$treatment\"";
				array_push($sdchangesarray, $chemidsql, $treatmentsql);
			}
			
   // CHECKING TO SEE IF THE CLASSES HAVE CHANGED.....
			// First loop through and see what previous classes were checked....
			$prevcount = 0;
			$prevclassArray = array();
			/*foreach($_POST as $postval=>$item){
				echo "$postval = $item<br>";
			}*/
			while($prevcount < $prevclasscount){
				// Get the value.....
				$prevclass = "prevclass$prevcount";
				//echo "prevclass = $prevclass<br>";
				$prevclassToAdd = $_POST[$prevclass];
				array_push($prevclassArray, $prevclassToAdd);
				//echo "Here's prev class...: $prevclassToAdd";
				$prevcount++;
			}

			// Are the new values for classes different than the previous values....
			//
			$classSQL = "SELECT classid, name from class ORDER BY classid ASC";
			$classResult = mysql_query($classSQL, $db);
			// For each class, need to check to see if it is different than the other classes....
			$newclassArray = array();
			$newclassNames = array();
			while($row = mysql_fetch_row($classResult)){
				$newid = $row[0];
				$newclassid = "newclass$newid";
				$newclassvalToCheck = $_POST[$newclassid];
				if($newclassvalToCheck != ""){
					// add to the new class array...
					array_push($newclassArray, $newclassvalToCheck);
					array_push($newclassNames, $row[1]);
				}
			}



			if($organism != 0){

				// Get the respective id, look up and assign the new value...
				$sql = "SELECT organism FROM organism WHERE organismid=$organism";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Organism changed from <font color=\"green\"><strong>$organism1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$organismsql = "organism = \"$name\"";
				array_push($sdchangesarray, $organismsql);
			}
			if(strcmp($originator1,$originator) != 0){
				// need to update the pub info
				$changesstr .= "Originator ID changed from <font color=\"green\"><strong>$originator1</strong></font> to <font color=\"red\"><strong>$originator</strong></font> <br>";
				$sql = "originator = \"$originator\"";
				array_push($sdchangesarray, $sql);
			}
			if(strcmp($organization1,$organization) != 0){
				// need to update the pub info
				$changesstr .= "Originator's Organization changed from <font color=\"green\"><strong>$organization1</strong></font> to <font color=\"red\"><strong>$organization</strong></font> <br>";
				$sql = "originatorOrg = \"$organization\"";
				array_push($sdchangesarray, $sql);
			}

			if(strcmp($rnagroupsize1, $rnagroupsize) != 0){
				$changesstr .= "RNA Group Size changed from <font color=\"green\"><strong>$rnagroupsize1</strong></font> to <font color=\"red\"><strong>$rnagroupsize</strong></font> <br>";
				$rnasql = "rnagroupsize = $rnagroupsize";
				array_push($sdchangesarray, $rnasql);
			}
			if($strain != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT strain FROM strain WHERE strainid=$strain";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Strain changed from <font color=\"green\"><strong>$strain1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$strainsql = "strain = \"$name\"";
				array_push($sdchangesarray, $strainsql);
			}
			if($genevariation != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT genevariation FROM genevariation WHERE genevariationid=$genevariation";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Gene Variation changed from <font color=\"green\"><strong>$genevariation1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$genevariationsql = "genevariation = \"$name\"";
				array_push($sdchangesarray, $genevariationsql);
			}
			if(strcmp($age1, $age) != 0){
				$agesql = "age = $age";
				$changesstr .= "Age changed from <font color=\"green\"><strong>$age1</strong></font> to <font color=\"red\"><strong>$age</strong></font> <br>";
				array_push($sdchangesarray, $agesql);
			}
			if($sex == "M" || $sex == "F"){
				$sexsql = "sex = '$sex'";
				$changesstr .= "Sex changed from <font color=\"green\"><strong>$sex1</strong></font> to <font color=\"red\"><strong>$sex</strong></font> <br>";
				array_push($sdchangesarray, $sexsql);
			}
			if($tissue != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT tissue FROM tissue WHERE tissueid=$tissue";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Tissue changed from <font color=\"green\"><strong>$tissue1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$tissuesql = "tissue = \"$name\"";
				array_push($sdchangesarray, $tissuesql);
			}
			if($vehicle != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT vehicle FROM vehicle WHERE vehicleid=$vehicle";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Vehicle changed from <font color=\"green\"><strong>$vehicle1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$vehiclesql = "vehicle = \"$name\"";
				array_push($sdchangesarray, $vehiclesql);
			}
			if(strcmp($dose1, $dose) != 0){
				$dosesql = "dose = $dose";
				$changesstr .= "Dose changed from <font color=\"green\"><strong>$dose1</strong></font> to <font color=\"red\"><strong>$dose</strong></font> <br>";
				array_push($sdchangesarray, $dosesql);
			}
			if($doseunit != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT doseunit FROM doseunit WHERE doseunitid=$doseunit";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Dose Unit changed from <font color=\"green\"><strong>$doseunits1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$doseunitssql = "doseunits = \"$name\"";
				array_push($sdchangesarray, $doseunitssql);
			}
			if($route != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT route FROM route WHERE routeid=$route";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Route changed from <font color=\"green\"><strong>$route1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$routesql = "route = \"$name\"";
				array_push($sdchangesarray, $routesql);
			}
			if($control != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT control FROM control WHERE controlid=$control";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Control changed from <font color=\"green\"><strong>$control1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$controlsql = "control = \"$name\"";
				array_push($sdchangesarray, $controlsql);
			}
			$dosagetime = "$dosehours:$doseminutes:00";
			$harvesttime = "$harvesthours:$harvestminutes:00";
			if(strcmp($dosagetime1, $dosagetime) != 0){
				$dosagetimesql = "dosagetime = \"$dosagetime\"";
				$changesstr .= "Dosage Time changed from <font color=\"green\"><strong>$dosagetime1</strong></font> to <font color=\"red\"><strong>$dosagetime</strong></font> <br>";
				array_push($sdchangesarray, $dosagetimesql);
			}
			if(strcmp($harvesttime1, $harvesttime) != 0){
				$harvesttimesql = "harvesttime = \"$harvesttime\"";
				$changesstr .= "Harvest Time changed from <font color=\"green\"><strong>$harvesttime1</strong></font> to <font color=\"red\"><strong>$harvesttime</strong></font> <br>";
				array_push($sdchangesarray, $harvesttimesql);
			}
			if(strcmp($duration1, $duration) != 0){
				$changesstr .= "Duration changed from <font color=\"green\"><strong>$duration1</strong></font> to <font color=\"red\"><strong>$duration</strong></font> <br>";
				$durationsql = "duration = $duration";
				array_push($sdchangesarray, $durationsql);
			}
			if($durationunit != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT durationunit FROM durationunit WHERE durationunitid=$durationunit";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Duration Unit changed from <font color=\"green\"><strong>$durationunits1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$durationunitsql = "durationunits = \"$name\"";
				array_push($sdchangesarray, $durationunitsql);
			}
			if(strcmp($hybrid1,$hybrid) != 0){
				// need to update the pub info
				$changesstr .= "Hybridized against changed from <font color=\"green\"><strong>$hybrid1</strong></font> to <font color=\"red\"><strong>$hybrid</strong></font> <br>";
				$sql = "hybrid = \"$hybrid\"";
				array_push($sdchangesarray, $sql);
			}
			if($visible != -1){
				// Only change acceess number if visible has been changed!!!
				if(strcmp($accessnumber1, $accessnumber) != 0){
				$changesstr .= "Access Number changed from <font color=\"green\"><strong>$accessnumber1</strong></font> to <font color=\"red\"><strong>$accessnumber</strong></font> <br>";
				$sql = "ownerid = $accessnumber";
				array_push($arraychangesarray, $sql);
			}
			}


			if(count($sdchangesarray) > 0){

				if(count($sdchangesarray) > 1){
					$sdchanges = implode(",", $sdchangesarray);
				}
				else{
					$sdchanges = $sdchangesarray[0];
				}
				$sql = "UPDATE sampledata SET $sdchanges WHERE sampleid = $trx";
				$result = mysql_query($sql, $db);
				//echo "<h3>$sql</h3>";
			}
			if(count($sichangesarray) > 0){

				if(count($sichangesarray) > 1){
					$sichanges = implode(",", $sichangesarray);
				}
				else{
					$sichanges = $sichangesarray[0];
				}
				$sql = "UPDATE sampleinfo SET $sichanges WHERE sampleid = $trx";
				$result = mysql_query($sql, $db);
				///echo "<h3>$sql</h3>";
			}
			if(count($arraychangesarray) > 0){

				if(count($arraychangesarray) > 1){
					$arraychanges = implode(",", $arraychangesarray);
				}
				else{
					$arraychanges = $arraychangesarray[0];
				}
				$sql = "UPDATE array SET $arraychanges WHERE arrayid = $trx";
				$result = mysql_query($sql, $db);
				//echo "<h3>$sql</h3>";
			}


		}

?>
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="2">Treatment Update Results</th>
		</tr>
		<?php 
			if($delete=="Y"){
		?>
			<tr>
			<td class="questionparameter" ><strong>Treatment Info Deleted?:</strong></td>
			<td class="results">
				<font color="green"><strong>Deleted Treatment #<?php echo $trx; ?></strong></font>
			</td>
			</tr>
		<?php
			}
			else{
		?>
			<tr>
			<td class="questionparameter" ><strong>Treatment Info Updated for #<?php echo $trx;?>:</strong></td>
			<td class="results">
			<?php 
			if(strlen($changesstr) > 0){
				echo $changesstr;
			}
			else{
				echo "<font color=\"red\"><strong>No changes made.</strong</font>";
			}
			?>
			</td>
			</tr>

		<?php
			}
		?>
		</table>
<?php
	}
}// end of initial submit....
/**

	else if($submitted2 == true){ // The treatment form has been submitted for multiple treatment edits....



	$committed = $_POST['committed'];
	$trx = $_POST['trx'];
	$userid = $_POST['userid'];
	$delete = $_POST['delete'];
	$samplename1 = $_POST['samplename1'];
	$samplename = $_POST['samplename'];
	$pubinfo1 = $_POST['pubinfo1'];
	$pubinfo = $_POST['pubinfo'];
	$treatment1 = $_POST['treatment1'];
	$chemical = $_POST['chemical'];
	$organism1 = $_POST['organism1'];
	$organism = $_POST['organism'];
	$rnagroupsize1 = $_POST['rnagroupsize1'];
	$rnagroupsize = $_POST['rnagroupsize'];
	$strain1 = $_POST['strain1'];
	$strain = $_POST['strain'];
	$genevariation1 = $_POST['genevariation1'];
	$genevariation = $_POST['genevariation'];
	$age1 = $_POST['age1'];
	$age = $_POST['age'];
	$sex1 = $_POST['sex1'];
	$sex = $_POST['sex'];
	$tissue1 = $_POST['tissue1'];
	$tissue = $_POST['tissue'];
	$vehicle1 = $_POST['vehicle1'];
	$vehicle = $_POST['vehicle'];
	$dose1 = $_POST['dose1'];
	$dose = $_POST['dose'];
	$doseunits1 = $_POST['doseunits1'];
	$doseunit = $_POST['doseunit'];
	$route1 = $_POST['route1'];
	$route = $_POST['route'];
	$control1 = $_POST['control1'];
	$control = $_POST['control'];
	$dosagetime1 = $_POST['dosagetime1'];
	$dosehours = $_POST['dosehours'];
	$doseminutes = $_POST['doseminutes'];
	$harvesttime1 = $_POST['harvesttime1'];
	$harvesthours = $_POST['harvesthours'];
	$harvestminutes = $_POST['harvestminutes'];
	$duration1 = $_POST['duration1'];
	$duration = $_POST['duration'];
	$durationunits1 = $_POST['durationunits1'];
	$durationunit = $_POST['durationunit'];
	$visible = $_POST['visible'];
	$accessnumber1 = $_POST['accessnumber1'];
	$accessnumber = $_POST['accessnumber'];



		if($committed != "true"){
		// Need to get the values associated w/ this treatment....
		$sql = "SELECT chemid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment,
			vehicle, dose, doseunits, route, dosagetime, duration, durationunits, harvesttime, control
			FROM sampledata WHERE sampleid = $trx";
		$trxResult = mysql_query($sql, $db);
		list($chemid1, $organism1, $rnagroupsize1, $strain1, $genevariation1, $age1, $sex1,
			$tissue1, $treatment1, $vehicle1, $dose1, $doseunits1, $route1, $dosagetime1, $duration1, $durationunits1,
			$harvesttime1, $control1) = mysql_fetch_array($trxResult);
		$sql = "SELECT arraydesc, ownerid FROM array WHERE arrayid = $trx";
		$trxResult = mysql_query($sql, $db);
		list($samplename1, $accessnumber1) = mysql_fetch_array($trxResult);

		if($accesnumber > 1){
			$visible = 0;
		}
		else{
			$visible = 1;
		}

		$sql = "SELECT pubinfo FROM sampleinfo WHERE sampleid = $trx";
		$trxResult = mysql_query($sql, $db);
		list($pubinfo1) = mysql_fetch_array($trxResult);




		require("trxmenus.php");
?>
	<form name="commitchanges" action="edittrx.php" method="post" onsubmit="return checkChangesForm()">
	<input type="hidden" name="committed" value="true">
	<input type="hidden" name="submitted" value="true">
	<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="2">Original Values</th>
		<th class="mainheader" colspan="2">Delete/Change this Treatment?</th>
		</tr>
		<tr>
		<td class="questionparameter" ><strong>Treatment ID:</strong></td>
		<td class="results">
		<input type="hidden" name="trx" value="<?php echo $trx; ?>">
		<input type="hidden" name="userid" value="<?php echo $userid; ?>">
		<?php echo $trx; ?>
		</td>
		<td bgcolor="red">
			<input type="radio" name="delete" value="N" checked>No
			<input type="radio" name="delete" value="Y">Yes
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Sample Name:</strong></td>
		<td class="results">
		<input type="hidden" name="samplename1" value="<?php echo $samplename1; ?>">
		<?php echo $samplename1; ?>
		</td>
		<td class="questionanswertext">
			<input name="samplename" type="text" value="<?php echo $samplename1;?>" align="left"></input><font color="red"><strong>*</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Publication Info:</strong></td>
		<td class="results">
		<input type="hidden" name="pubinfo1" value="<?php echo $pubinfo1; ?>">
		<?php echo $pubinfo1; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="pubinfo" type="text" value="<?php echo $pubinfo1; ?>" align="left"></input>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Chemical:</strong></td>
		<td class="results">
		<input type="hidden" name="treatment1" value="<?php echo $treatment1; ?>">
		<?php echo $treatment1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $chemMenu; ?>
		</td>
		</tr>
		<tr>
		<td class="questionparameter" ><strong>Organism:</strong></td>
		<td class="results">
		<input type="hidden" name="organism1" value="<?php echo $organism1; ?>">
		<?php echo $organism1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $organismMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>RNA Group Size:</strong></td>
		<td class="results">
		<?php echo $rnagroupsize1; ?>
		</td>
		<td  class="questionanswertext" >
			<input type="hidden" name="rnagroupsize1" value="<?php echo $rnagroupsize1; ?>">
			<input name="rnagroupsize" type="text" value="<?php echo $rnagroupsize1; ?>" align="right"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Strain:</strong></td>
		<td class="results">
		<input type="hidden" name="strain" value="<?php echo $strain; ?>">
		<?php echo $strain1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $strainMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Genetic Variation:</strong></td>
		<td class="results">
		<input type="hidden" name="genevariation1" value="<?php echo $genevariation1; ?>">
		<?php echo $genevariation1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $genevariationMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Age:</strong></td>
		<td class="results">
		<?php echo $age1; ?>
		</td>
		<td  class="questionanswertext" >
			<input type="hidden" name="age1" value="<?php echo $age1; ?>">
			<input name="age" type="text" value="<?php echo $age1; ?>" align="right"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Sex:</strong></td>
		<td class="results">
		<input type="hidden" name="sex1" value="<?php echo $sex1; ?>">
		<?php echo $sex1; ?>
		</td>
		<td  class="questionanswertext" >
			<input type="radio" name="sex" value="0" checked>No Change
			<input type="radio" name="sex" value="M">Male
			<input type="radio" name="sex" value="F"> Female
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Tissue:</strong></td>
		<td class="results">
		<input type="hidden" name="tissue1" value="<?php echo $tissue1; ?>">
		<?php echo $tissue1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $tissueMenu; ?>
		</td>
		</tr>


		<tr>
		<td class="questionparameter" ><strong>Vehicle:</strong></td>
		<td class="results">
		<input type="hidden" name="vehicle1" value="<?php echo $vehicle1; ?>">
		<?php echo $vehicle1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $vehicleMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Dose:</strong></td>
		<td class="results">
		<input type="hidden" name="dose1" value="<?php echo $dose1; ?>">

		<?php echo "$dose1"; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="dose" type="text" value="<?php echo $dose1; ?>" align="right"></input><font color="blue"><strong>#</strong></font>

		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Dose Units:</strong></td>
		<td class="results">
		<input type="hidden" name="doseunits1" value="<?php echo $doseunits1; ?>">
		<?php echo $doseunits1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $doseunitMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Route:</strong></td>
		<td class="results">
		<input type="hidden" name="route1" value="<?php echo $route1; ?>">
		<?php echo $route1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $routeMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Control:</strong></td>
		<td class="results">
		<input type="hidden" name="control1" value="<?php echo $control1; ?>">
		<?php echo $control1; ?>
		</td>
		<td  class="questionanswertext" >
			<?php echo $controlMenu; ?>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Dosage Time:</strong></td>
		<td class="results">
		<input type="hidden" name="dosagetime1" value="<?php echo "$dosagetime1"; ?>">
		<?php echo "$dosagetime1"; ?>
		</td>
		<?php
			// Get the hours and mins for dosage time from $dosagetime1....
			$pieces = explode(":", $dosagetime1);
			$hours = $pieces[0];
			$minutes = $pieces[1];
		?>
		<td  class="questionanswertext" >
			<input name="dosehours" type="text" value="<?php echo $hours; ?>" align="right" size="2"></input>:
			<input name="doseminutes" type="text" value="<?php echo $minutes; ?>" align="right" size="2"></input><font color="blue"><strong>#</strong></font><strong><font color="red">&nbsp&nbsp &nbsp&nbsp Note:</font>&nbsp 01:59 = 1:59am  12:00 = noon  23:59 = 11:59pm  00:00 = midnight</strong>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Harvest Time:</strong></td>
		<td class="results">
		<input type="hidden" name="harvesttime1" value="<?php echo "$harvesttime1"; ?>">
		<?php echo "$harvesttime1"; ?>
		</td>
		<?php
			// Get the hours and mins for dosage time from $dosagetime1....
			$pieces = explode(":", $harvesttime1);
			$hours = $pieces[0];
			$minutes = $pieces[1];
		?>
		<td  class="questionanswertext" >
			<input name="harvesthours" type="text" value="<?php echo $hours; ?>" align="right" size="2"></input>:
			<input name="harvestminutes" type="text" value="<?php echo $minutes; ?>" align="right" size="2"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Duration:</strong></td>
		<td class="results">
		<input type="hidden" name="duration1" value="<?php echo "$duration1"; ?>">
		<?php echo "$duration1"; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="duration" type="text" value="<?php echo $duration1; ?>" align="right"></input><font color="blue"><strong>#</strong></font>
		</td>
		</tr>

		<tr>

		<td class="questionparameter" ><strong>Duration Units:</strong></td>
		<input type="hidden" name="durationunits1" value="<?php echo "$durationunits1"; ?>">
		<td class="results"><?php echo $durationunits1; ?></td>
		<td  class="questionanswertext" >
			<?php echo $durationunitMenu; ?>
			</td>
		</tr>
		<tr>
		<td class="questionparameter" ><strong>Visible to Public:</strong></td>
		<td class="results">
		<?php
			$visiblestr = "No";
			if($visible == 1){
				$visiblestr = "Yes";
			}
		?>
		<?php echo "$visiblestr"; ?>
		</td>
		<td class="questionanswertext" >
			<input type="radio" name="visible" value="-1" checked>No Change
			<input type="radio" name="visible" value="1" onchange="changeAccessNumberEditTrx()">Yes
			<input type="radio" name="visible" value="0" onchange="changeAccessNumberEditTrx()"> No
		</td>
		</tr>

		<tr>
		<td class="questionparameter" ><strong>Access Number:</strong></td>
		<td class="results">
		<input type="hidden" name="accessnumber1" value="<?php echo $accessnumber1; ?>">
		<?php echo $accessnumber1; ?>
		</td>
		<td  class="questionanswertext" >
			<input name="accessnumber" type="text" value="<?php echo $accessnumber1; ?>" align="right"></input><font color="blue"><strong>#</strong>&nbsp&nbsp<font color="red"><strong>Change valid only if Visible to Public value is changed!</strong></font>
		</td>
		</tr>


	</table>
	<tr>
	<?php
			echo "<td><input type=\"submit\" name=\"commit\" value=\"Commit changes to Database?\"></td>";
	?>
		<td></td>
	<td><INPUT TYPE="button" VALUE="Do nothing!" onClick="history.go(-1)"></td>
	</tr>

	</form>
<?php
	}else{ // has been committed....
		// Need to determine whether or not anything has been changed.....
		if($delete == "Y"){
			$sql = "DELETE FROM sampledata WHERE sampleid = $trx";
			$delResult = mysql_query($sql, $db);
			$sql = "DELETE FROM sampleinfo WHERE sampleid = $trx";
			$delResult = mysql_query($sql, $db);
			$sql = "DELETE FROM array WHERE arrayid = $trx";
			$delResult = mysql_query($sql, $db);
			$sql = "DELETE FROM hybrids WHERE arrayid = $trx";
			$delResult = mysql_query($sql, $db);
		}
		else{
			// Has anything been changed????
			// Check the status of each radio button or field...
			// This is used to hold the changes made to the sampledata table for this trx id
			$sdchangesarray = array();
			// This is used to hold the changes made to the sampleinfo table for this trx id
			$sichangesarray = array();
			// This is used to hold the changes made to the sampleinfo table for this trx id
			$arraychangesarray = array();
			// This is used to hold the changes made to the sampleinfo table for this trx id
			$hybchangesarray = array();
			$changesstr = "";
			if(strcmp($samplename1, $samplename) != 0){
				// need to update the sample name
				$changesstr .= "Treatment name changed from <font color=\"green\"><strong>$samplename1</strong></font> to <font color=\"red\"><strong>$samplename</strong></font> <br>";
				$sql = "samplename = \"$samplename\"";
				array_push($sichangesarray, $sql);
				$sql = "arraydesc = \"$samplename\"";
				array_push($arraychangesarray, $sql);
			}
			if(strcmp($pubinfo1,$pubinfo) != 0){
				// need to update the pub info
				$changesstr .= "Publication info changed from <font color=\"green\"><strong>$pubinfo1</strong></font> to <font color=\"red\"><strong>$pubinfo</strong></font> <br>";
				$sql = "pubinfo = \"$pubinfo\"";
				array_push($sichangesarray, $sql);
			}
			if($chemical != 0){
				// Get the chemid, look up and assign the new treatment...
				$chemSQL = "SELECT chemical FROM chem WHERE chemid=$chemical";
				$chemResult = mysql_query($chemSQL, $db);
				$row = mysql_fetch_row($chemResult);
				$treatment = $row[0];
				$changesstr .= "Treatment changed from <font color=\"green\"><strong>$treatment1</strong></font> to <font color=\"red\"><strong>$treatment</strong></font> <br>";
				$chemidsql = "chemid = $chemical";
				$treatmentsql = "treatment = \"$treatment\"";
				array_push($sdchangesarray, $chemidsql, $treatmentsql);
			}
			
   // CHECKING TO SEE IF THE CLASSES HAVE CHANGED.....
			// First loop through and see what previous classes were checked....
			$prevcount = 0;
			$prevclassArray = array();

			while($prevcount < $prevclasscount){
				// Get the value.....
				$prevclass = "prevclass$prevcount";
				//echo "prevclass = $prevclass<br>";
				$prevclassToAdd = $_POST[$prevclass];
				array_push($prevclassArray, $prevclassToAdd);
				//echo "Here's prev class...: $prevclassToAdd";
				$prevcount++;
			}

			// Are the new values for classes different than the previous values....
			//
			$classSQL = "SELECT classid, name from class ORDER BY classid ASC";
			$classResult = mysql_query($classSQL, $db);
			// For each class, need to check to see if it is different than the other classes....
			$newclassArray = array();
			$newclassNames = array();
			while($row = mysql_fetch_row($classResult)){
				$newid = $row[0];
				$newclassid = "newclass$newid";
				$newclassvalToCheck = $_POST[$newclassid];
				if($newclassvalToCheck != ""){
					// add to the new class array...
					array_push($newclassArray, $newclassvalToCheck);
					array_push($newclassNames, $row[1]);
				}
			}

			



			if($organism != 0){

				// Get the respective id, look up and assign the new value...
				$sql = "SELECT organism FROM organism WHERE organismid=$organism";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Organism changed from <font color=\"green\"><strong>$organism1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$organismsql = "organism = \"$name\"";
				array_push($sdchangesarray, $organismsql);
			}
			if(strcmp($rnagroupsize1, $rnagroupsize) != 0){
				$changesstr .= "RNA Group Size changed from <font color=\"green\"><strong>$rnagroupsize1</strong></font> to <font color=\"red\"><strong>$rnagroupsize</strong></font> <br>";
				$rnasql = "rnagroupsize = $rnagroupsize";
				array_push($sdchangesarray, $rnasql);
			}
			if($strain != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT strain FROM strain WHERE strainid=$strain";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Strain changed from <font color=\"green\"><strong>$strain1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$strainsql = "strain = \"$name\"";
				array_push($sdchangesarray, $strainsql);
			}
			if($genevariation != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT genevariation FROM genevariation WHERE genevariationid=$genevariation";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Gene Variation changed from <font color=\"green\"><strong>$genevariation1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$genevariationsql = "genevariation = \"$name\"";
				array_push($sdchangesarray, $genevariationsql);
			}
			if(strcmp($age1, $age) != 0){
				$agesql = "age = $age";
				$changesstr .= "Age changed from <font color=\"green\"><strong>$age1</strong></font> to <font color=\"red\"><strong>$age</strong></font> <br>";
				array_push($sdchangesarray, $agesql);
			}
			if($sex == "M" || $sex == "F"){
				$sexsql = "sex = '$sex'";
				$changesstr .= "Sex changed from <font color=\"green\"><strong>$sex1</strong></font> to <font color=\"red\"><strong>$sex</strong></font> <br>";
				array_push($sdchangesarray, $sexsql);
			}
			if($tissue != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT tissue FROM tissue WHERE tissueid=$tissue";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Tissue changed from <font color=\"green\"><strong>$tissue1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$tissuesql = "tissue = \"$name\"";
				array_push($sdchangesarray, $tissuesql);
			}
			if($vehicle != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT vehicle FROM vehicle WHERE vehicleid=$vehicle";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Vehicle changed from <font color=\"green\"><strong>$vehicle1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$vehiclesql = "vehicle = \"$name\"";
				array_push($sdchangesarray, $vehiclesql);
			}
			if(strcmp($dose1, $dose) != 0){
				$dosesql = "dose = $dose";
				$changesstr .= "Dose changed from <font color=\"green\"><strong>$dose1</strong></font> to <font color=\"red\"><strong>$dose</strong></font> <br>";
				array_push($sdchangesarray, $dosesql);
			}
			if($doseunit != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT doseunit FROM doseunit WHERE doseunitid=$doseunit";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Dose Unit changed from <font color=\"green\"><strong>$doseunits1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$doseunitssql = "doseunits = \"$name\"";
				array_push($sdchangesarray, $doseunitssql);
			}
			if($route != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT route FROM route WHERE routeid=$route";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Route changed from <font color=\"green\"><strong>$route1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$routesql = "route = \"$name\"";
				array_push($sdchangesarray, $routesql);
			}
			if($control != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT control FROM control WHERE controlid=$control";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Control changed from <font color=\"green\"><strong>$control1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$controlsql = "control = \"$name\"";
				array_push($sdchangesarray, $controlsql);
			}
			$dosagetime = "$dosehours:$doseminutes:00";
			$harvesttime = "$harvesthours:$harvestminutes:00";
			if(strcmp($dosagetime1, $dosagetime) != 0){
				$dosagetimesql = "dosagetime = \"$dosagetime\"";
				$changesstr .= "Dosage Time changed from <font color=\"green\"><strong>$dosagetime1</strong></font> to <font color=\"red\"><strong>$dosagetime</strong></font> <br>";
				array_push($sdchangesarray, $dosagetimesql);
			}
			if(strcmp($harvesttime1, $harvesttime) != 0){
				$harvesttimesql = "harvesttime = \"$harvesttime\"";
				$changesstr .= "Harvest Time changed from <font color=\"green\"><strong>$harvesttime1</strong></font> to <font color=\"red\"><strong>$harvesttime</strong></font> <br>";
				array_push($sdchangesarray, $harvesttimesql);
			}
			if(strcmp($duration1, $duration) != 0){
				$changesstr .= "Duration changed from <font color=\"green\"><strong>$duration1</strong></font> to <font color=\"red\"><strong>$duration</strong></font> <br>";
				$durationsql = "duration = $duration";
				array_push($sdchangesarray, $durationsql);
			}
			if($durationunit != 0){
				// Get the respective id, look up and assign the new value...
				$sql = "SELECT durationunit FROM durationunit WHERE durationunitid=$durationunit";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_row($result);
				$name = $row[0];
				$changesstr .= "Duration Unit changed from <font color=\"green\"><strong>$durationunits1</strong></font> to <font color=\"red\"><strong>$name</strong></font> <br>";
				$durationunitsql = "durationunits = \"$name\"";
				array_push($sdchangesarray, $durationunitsql);
			}
			if($visible != -1){
				// Only change acceess number if visible has been changed!!!
				if(strcmp($accessnumber1, $accessnumber) != 0){
				$changesstr .= "Access Number changed from <font color=\"green\"><strong>$accessnumber1</strong></font> to <font color=\"red\"><strong>$accessnumber</strong></font> <br>";
				$sql = "ownerid = $accessnumber";
				array_push($arraychangesarray, $sql);
			}
			}


			if(count($sdchangesarray) > 0){

				if(count($sdchangesarray) > 1){
					$sdchanges = implode(",", $sdchangesarray);
				}
				else{
					$sdchanges = $sdchangesarray[0];
				}
				$sql = "UPDATE sampledata SET $sdchanges WHERE sampleid = $trx";
				$result = mysql_query($sql, $db);
				//echo "<h3>$sql</h3>";
			}
			if(count($sichangesarray) > 0){

				if(count($sichangesarray) > 1){
					$sichanges = implode(",", $sichangesarray);
				}
				else{
					$sichanges = $sichangesarray[0];
				}
				$sql = "UPDATE sampleinfo SET $sichanges WHERE sampleid = $trx";
				$result = mysql_query($sql, $db);
				//echo "<h3>$sql</h3>";
			}
			if(count($arraychangesarray) > 0){

				if(count($arraychangesarray) > 1){
					$arraychanges = implode(",", $arraychangesarray);
				}
				else{
					$arraychanges = $arraychangesarray[0];
				}
				$sql = "UPDATE array SET $arraychanges WHERE arrayid = $trx";
				$result = mysql_query($sql, $db);
				//echo "<h3>$sql</h3>";
			}


		}

?>
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="2">Treatment Update Results</th>
		</tr>
		<?php 
			if($delete=="Y"){
		?>
			<tr>
			<td class="questionparameter" ><strong>Treatment Info Deleted?:</strong></td>
			<td class="results">
				<font color="green"><strong>Deleted Treatment #<?php echo $trx; ?></strong></font>
			</td>
			</tr>
		<?php
			}
			else{
		?>
			<tr>
			<td class="questionparameter" ><strong>Treatment Info Updated for #<?php echo $trx;?>:</strong></td>
			<td class="results">
			<?php 
			if(strlen($changesstr) > 0){
				echo $changesstr;
			}
			else{
				echo "<font color=\"red\"><strong>No changes made.</strong</font>";
			}
			?>
			</td>
			</tr>

		<?php
			}
		?>
		</table>
<?php
	}

	}
*/
}
?>

 </div>
 <?php
	include '../adminleftmenu.inc';

?>


 <div class="boxclear"> </div>




 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>

</body>
</html>
