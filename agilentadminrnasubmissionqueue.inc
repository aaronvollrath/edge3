b <?php

/***
Location: /edge2/admin
Description:  This page lists rna sample forms that have been filled out.
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

require './phpinc/edge3_db_connect.inc';
include 'edge_update_user_activity.inc';
?>


<script type="text/javascript">
	//function createPrintableForm($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod)
	function createPrintableForm()
	{
		myWin = open();
		myWin.document.open();
		myWin.document.write("<html><head><title>User Treatment Printable 							      Form</title></head>");
		myWin.document.write("<body>");
		//myWin.document.write("<p1>Treatment ID:</p1> $queue_arrayid");
		myWin.document.write($trx);
		myWin.document.write("</body>");
		myWin.document.write("</html>");
		//var dummy = document.forms[0].elements[4].value;



	}
</script>


 <?php
include 'adminmenu.inc';
?>
<h3 class="contenthead">User Submitted RNA Samples</h3>
<?php
$priv_level = $_SESSION['priv_level'];
if($priv_level != 99){
	echo "Sorry, you are not authorized to access this page.";
}
else{
	if(!isset($_POST['Submit'])){
		$submitted = $_POST['Submit'];
	}else{
		$submitted = "";
	}
	if(!isset($_POST['Print'])){
		$printed = $_POST['Print'];
	}else{
		$printed = "";
	}
	if(!isset($_POST['Submit12'])){
		$submitted12 = $_POST['Submit12'];
	}else{
		$Submit12 = "";
	}
	if(!isset($_POST['Submit13'])){
		$submitted13 = $_POST['Submit13'];
	}else{
		$Submit13 = "";
	}
	if(!isset($_POST['Submit21'])){
		$submitted21 = $_POST['Submit21'];
	}else{
		$Submit21 = "";
	}
	if(!isset($_POST['Submit22'])){
		$submitted22 = $_POST['Submit22'];
	}else{
		$Submit22 = "";
	}
	if(!isset($_POST['Submit3'])){
		$submitted3 = $_POST['Submit3'];
	}else{
		$Submit3 = "";
	}
	if(!isset($_POST['queue_arrayid'])){
		$trx = "";
	}else{
		$trx = $_POST['queue_arrayid'];
	}
	
	//if($submitted != true && $printed != true) { // if the trx form has not been submitted/printed yet...
	if($submitted != true && $submitted12 != true && $submitted13 != true && $submitted21 != true && $submitted22 != 	true && $submitted3 != true && $printed != true) 	{ // if the trx form has not been submitted/printed yet...


?>

  <?php

		// Get the treatment id's and treatment names....<new case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=0 ORDER BY queue_arrayid ASC";
		///echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$firstchoice = 1;
		$tablerows = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];

			$tablerows .= "<tr>";
			if($firstchoice == 1){

				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\" checked>$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
				$firstchoice = 0;
			}
			else{
				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
			}
			$tablerows .= "</tr>\n";
		}


		// Get the treatment id's and treatment names....<done case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=1 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		
		$tablerows1 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];

			$tablerows1 .= "<tr>";
			$tablerows1 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows1 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows1 .= "<td class=\"results\">$treatment</td>";
			$tablerows1 .= "<td class=\"results\">$organism</td>";
			$tablerows1 .= "<td class=\"results\">$strain</td>";
			$tablerows1 .= "<td class=\"results\">$genevariation</td>";
			$tablerows1 .= "<td class=\"results\">$age</td>";
			$tablerows1 .= "<td class=\"results\">$sex</td>";
			$tablerows1 .= "<td class=\"results\">$tissue</td>";
			$tablerows1 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows1 .= "<td class=\"results\">$vehicle</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows1 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows1 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<in queue case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=2 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows2 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows2 .= "<tr>";
			$tablerows2 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows2 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows2 .= "<td class=\"results\">$treatment</td>";
			$tablerows2 .= "<td class=\"results\">$organism</td>";
			$tablerows2 .= "<td class=\"results\">$strain</td>";
			$tablerows2 .= "<td class=\"results\">$genevariation</td>";
			$tablerows2 .= "<td class=\"results\">$age</td>";
			$tablerows2 .= "<td class=\"results\">$sex</td>";
			$tablerows2 .= "<td class=\"results\">$tissue</td>";
			$tablerows2 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows2 .= "<td class=\"results\">$vehicle</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows2 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows2 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<error case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=3 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows3 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows3 .= "<tr>";
			$tablerows3 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows3 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows3 .= "<td class=\"results\">$treatment</td>";
			$tablerows3 .= "<td class=\"results\">$organism</td>";
			$tablerows3 .= "<td class=\"results\">$strain</td>";
			$tablerows3 .= "<td class=\"results\">$genevariation</td>";
			$tablerows3 .= "<td class=\"results\">$age</td>";
			$tablerows3 .= "<td class=\"results\">$sex</td>";
			$tablerows3 .= "<td class=\"results\">$tissue</td>";
			$tablerows3 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows3 .= "<td class=\"results\">$vehicle</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows3 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows3 .= "</tr>\n";
		}
?>


		<form name="selectsample1" action="agilentexperimentlayouttest.php" method="post">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="14">New RNA Samples</th>
		</tr>
		</thead>
		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			echo $tablerows;
		?>

		<tr>
		<td><input type="submit" name="Submit" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit12" value="Move to In-queue List"></td>
		<td><input type="submit" name="Submit13" value="Move to Error List"></td>
		<td><input type="submit" name="Print" value="Print this Sample"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>

		<tr>
		<th class="mainheader" colspan="14">In-queue RNA Samples</th>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows2;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit21" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit22" value="Move to Error List"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>

		<tr>
		<th class="mainheader" colspan="14">Viewed (Done) RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows1;
			//echo "after echo";
		?>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>

		<tr>
		<th class="mainheader" colspan="14">RNA Samples with Errors</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows3;
			//echo "after echo";
		?>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>

		<tr>
		<td><input type="submit" name="Submit3" value="Move to 'Done' List"></td>
		</tr>

		</table>

<?php
	} //end of submitted=false

	else if($_POST['Submit']){

		// update the processed field to 1 to indicate that it has been submitted
		$trxSQL = "UPDATE agilent_array_queue set processed=1 where queue_arrayid = $queue_arrayid";
                $trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);

		// now display the unsubmitted and submitted treatments as before
		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=0 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$firstchoice = 1;
		$tablerows = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows .= "<tr>";
			if($firstchoice == 1){

				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\" checked>$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
				$firstchoice = 0;
			}
			else{
				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
			}
			$tablerows .= "</tr>\n";
		}



		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=1 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows1 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows1 .= "<tr>";
			$tablerows1 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows1 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows1 .= "<td class=\"results\">$treatment</td>";
			$tablerows1 .= "<td class=\"results\">$organism</td>";
			$tablerows1 .= "<td class=\"results\">$strain</td>";
			$tablerows1 .= "<td class=\"results\">$genevariation</td>";
			$tablerows1 .= "<td class=\"results\">$age</td>";
			$tablerows1 .= "<td class=\"results\">$sex</td>";
			$tablerows1 .= "<td class=\"results\">$tissue</td>";
			$tablerows1 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows1 .= "<td class=\"results\">$vehicle</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows1 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows1 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<in queue case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=2 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows2 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows2 .= "<tr>";
			$tablerows2 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows2 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows2 .= "<td class=\"results\">$treatment</td>";
			$tablerows2 .= "<td class=\"results\">$organism</td>";
			$tablerows2 .= "<td class=\"results\">$strain</td>";
			$tablerows2 .= "<td class=\"results\">$genevariation</td>";
			$tablerows2 .= "<td class=\"results\">$age</td>";
			$tablerows2 .= "<td class=\"results\">$sex</td>";
			$tablerows2 .= "<td class=\"results\">$tissue</td>";
			$tablerows2 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows2 .= "<td class=\"results\">$vehicle</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows2 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows2 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<error case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=3 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows3 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows3 .= "<tr>";
			$tablerows3 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows3 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows3 .= "<td class=\"results\">$treatment</td>";
			$tablerows3 .= "<td class=\"results\">$organism</td>";
			$tablerows3 .= "<td class=\"results\">$strain</td>";
			$tablerows3 .= "<td class=\"results\">$genevariation</td>";
			$tablerows3 .= "<td class=\"results\">$age</td>";
			$tablerows3 .= "<td class=\"results\">$sex</td>";
			$tablerows3 .= "<td class=\"results\">$tissue</td>";
			$tablerows3 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows3 .= "<td class=\"results\">$vehicle</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows3 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows3 .= "</tr>\n";
		}
	?>

		<form name="selectsample2" action="agilentexperimentlayouttest.php" method="post">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="14">New RNA Samples</th>
		</tr>
		</thead>
		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>
		<?php
		// We created the rows above, now insert them into the table...
			echo $tablerows;
		?>

		<tr>
		<td><input type="submit" name="Submit" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit12" value="Move to In-queue List"></td>
		<td><input type="submit" name="Submit13" value="Move to Error List"></td>
		<td><input type="submit" name="Print" value="Print"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr>
		</tr>

		<tr>
		<th class="mainheader" colspan="14">In-queue RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows2;
			//echo "after echo";
		?>


		<tr>
		<td><input type="submit" name="Submit21" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit22" value="Move to Error List"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>

		<tr>
		<th class="mainheader" colspan="14">Viewed (Done) RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows1;
			//echo "after echo";
		?>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>


		<tr>
		<th class="mainheader" colspan="14">RNA Samples with Errors</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows3;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit3" value="Move to 'Done' List"></td>
		</tr>

		</table>

<?php

	}

	else if($_POST['Submit12']){

		// update the processed field to 1 to indicate that it has been submitted
		$trxSQL = "UPDATE agilent_array_queue set processed=2 where queue_arrayid = $queue_arrayid";
                $trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);

		// now display the unsubmitted and submitted treatments as before
		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=0 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$firstchoice = 1;
		$tablerows = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows .= "<tr>";
			if($firstchoice == 1){

				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\" checked>$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
				$firstchoice = 0;
			}
			else{
				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
			}
			$tablerows .= "</tr>\n";
		}



		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=1 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows1 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows1 .= "<tr>";
			$tablerows1 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows1 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows1 .= "<td class=\"results\">$treatment</td>";
			$tablerows1 .= "<td class=\"results\">$organism</td>";
			$tablerows1 .= "<td class=\"results\">$strain</td>";
			$tablerows1 .= "<td class=\"results\">$genevariation</td>";
			$tablerows1 .= "<td class=\"results\">$age</td>";
			$tablerows1 .= "<td class=\"results\">$sex</td>";
			$tablerows1 .= "<td class=\"results\">$tissue</td>";
			$tablerows1 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows1 .= "<td class=\"results\">$vehicle</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows1 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows1 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<in queue case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=2 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult =$db->Execute($trxSQL);// mysql_query($trxSQL, $db);
		$tablerows2 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows2 .= "<tr>";
			$tablerows2 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows2 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows2 .= "<td class=\"results\">$treatment</td>";
			$tablerows2 .= "<td class=\"results\">$organism</td>";
			$tablerows2 .= "<td class=\"results\">$strain</td>";
			$tablerows2 .= "<td class=\"results\">$genevariation</td>";
			$tablerows2 .= "<td class=\"results\">$age</td>";
			$tablerows2 .= "<td class=\"results\">$sex</td>";
			$tablerows2 .= "<td class=\"results\">$tissue</td>";
			$tablerows2 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows2 .= "<td class=\"results\">$vehicle</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows2 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows2 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<error case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=3 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows3 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows3 .= "<tr>";
			$tablerows3 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows3 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows3 .= "<td class=\"results\">$treatment</td>";
			$tablerows3 .= "<td class=\"results\">$organism</td>";
			$tablerows3 .= "<td class=\"results\">$strain</td>";
			$tablerows3 .= "<td class=\"results\">$genevariation</td>";
			$tablerows3 .= "<td class=\"results\">$age</td>";
			$tablerows3 .= "<td class=\"results\">$sex</td>";
			$tablerows3 .= "<td class=\"results\">$tissue</td>";
			$tablerows3 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows3 .= "<td class=\"results\">$vehicle</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows3 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows3 .= "</tr>\n";
		}
	?>

		<form name="selectsample2" action="agilentexperimentlayouttest.php" method="post">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="14">New RNA Samples</th>
		</tr>
		</thead>
		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>
		<?php
		// We created the rows above, now insert them into the table...
			echo $tablerows;
		?>

		<tr>
		<td><input type="submit" name="Submit" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit12" value="Move to In-queue List"></td>
		<td><input type="submit" name="Submit13" value="Move to Error List"></td>
		<td><input type="submit" name="Print" value="Print"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr>
		</tr>

		<tr>
		<th class="mainheader" colspan="14">In-queue RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows2;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit21" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit22" value="Move to Error List"></td>
		</tr>


		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>


		<tr>
		<th class="mainheader" colspan="14">Viewed (Done) RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows1;
			//echo "after echo";
		?>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>


		<tr>
		<th class="mainheader" colspan="14">RNA Samples with Errors</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows3;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit3" value="Move to 'Done' List"></td>
		</tr>

		</table>

<?php

	}

	else if($_POST['Submit13']){

		// update the processed field to 1 to indicate that it has been submitted
		$trxSQL = "UPDATE agilent_array_queue set processed=3 where queue_arrayid = $queue_arrayid";
                $trxResult = $db->Execute();//mysql_query($trxSQL, $db);

		// now display the unsubmitted and submitted treatments as before
		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=0 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$firstchoice = 1;
		$tablerows = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows .= "<tr>";
			if($firstchoice == 1){

				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\" checked>$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
				$firstchoice = 0;
			}
			else{
				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
			}
			$tablerows .= "</tr>\n";
		}



		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=1 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows1 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows1 .= "<tr>";
			$tablerows1 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows1 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows1 .= "<td class=\"results\">$treatment</td>";
			$tablerows1 .= "<td class=\"results\">$organism</td>";
			$tablerows1 .= "<td class=\"results\">$strain</td>";
			$tablerows1 .= "<td class=\"results\">$genevariation</td>";
			$tablerows1 .= "<td class=\"results\">$age</td>";
			$tablerows1 .= "<td class=\"results\">$sex</td>";
			$tablerows1 .= "<td class=\"results\">$tissue</td>";
			$tablerows1 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows1 .= "<td class=\"results\">$vehicle</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows1 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows1 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<in queue case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=2 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows2 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows2 .= "<tr>";
			$tablerows2 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows2 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows2 .= "<td class=\"results\">$treatment</td>";
			$tablerows2 .= "<td class=\"results\">$organism</td>";
			$tablerows2 .= "<td class=\"results\">$strain</td>";
			$tablerows2 .= "<td class=\"results\">$genevariation</td>";
			$tablerows2 .= "<td class=\"results\">$age</td>";
			$tablerows2 .= "<td class=\"results\">$sex</td>";
			$tablerows2 .= "<td class=\"results\">$tissue</td>";
			$tablerows2 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows2 .= "<td class=\"results\">$vehicle</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows2 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows2 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<error case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=3 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows3 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows3 .= "<tr>";
			$tablerows3 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows3 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows3 .= "<td class=\"results\">$treatment</td>";
			$tablerows3 .= "<td class=\"results\">$organism</td>";
			$tablerows3 .= "<td class=\"results\">$strain</td>";
			$tablerows3 .= "<td class=\"results\">$genevariation</td>";
			$tablerows3 .= "<td class=\"results\">$age</td>";
			$tablerows3 .= "<td class=\"results\">$sex</td>";
			$tablerows3 .= "<td class=\"results\">$tissue</td>";
			$tablerows3 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows3 .= "<td class=\"results\">$vehicle</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows3 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows3 .= "</tr>\n";
		}
	?>

		<form name="selectsample2" action="agilentexperimentlayouttest.php" method="post">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="14">New RNA Samples</th>
		</tr>
		</thead>
		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>
		<?php
		// We created the rows above, now insert them into the table...
			echo $tablerows;
		?>

		<tr>
		<td><input type="submit" name="Submit" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit12" value="Move to In-queue List"></td>
		<td><input type="submit" name="Submit13" value="Move to Error List"></td>
		<td><input type="submit" name="Print" value="Print"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr>
		</tr>

		<tr>
		<th class="mainheader" colspan="14">In-queue RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows2;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit21" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit22" value="Move to Error List"></td>
		</tr>

				<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>


		<tr>
		<th class="mainheader" colspan="14">Viewed (Done) RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows1;
			//echo "after echo";
		?>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>


		<tr>
		<th class="mainheader" colspan="14">RNA Samples with Errors</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows3;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit3" value="Move to 'Done' List"></td>
		</tr>

		</table>

<?php

	}



		else if($_POST['Submit21']){

		// update the processed field to 1 to indicate that it has been submitted
		$trxSQL = "UPDATE agilent_array_queue set processed=1 where queue_arrayid = $queue_arrayid";
                $trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);

		// now display the unsubmitted and submitted treatments as before
		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=0 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$firstchoice = 1;
		$tablerows = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows .= "<tr>";
			if($firstchoice == 1){

				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\" checked>$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
				$firstchoice = 0;
			}
			else{
				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
			}
			$tablerows .= "</tr>\n";
		}



		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=1 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows1 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows1 .= "<tr>";
			$tablerows1 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows1 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows1 .= "<td class=\"results\">$treatment</td>";
			$tablerows1 .= "<td class=\"results\">$organism</td>";
			$tablerows1 .= "<td class=\"results\">$strain</td>";
			$tablerows1 .= "<td class=\"results\">$genevariation</td>";
			$tablerows1 .= "<td class=\"results\">$age</td>";
			$tablerows1 .= "<td class=\"results\">$sex</td>";
			$tablerows1 .= "<td class=\"results\">$tissue</td>";
			$tablerows1 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows1 .= "<td class=\"results\">$vehicle</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows1 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows1 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<in queue case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=2 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows2 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows2 .= "<tr>";
			$tablerows2 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows2 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows2 .= "<td class=\"results\">$treatment</td>";
			$tablerows2 .= "<td class=\"results\">$organism</td>";
			$tablerows2 .= "<td class=\"results\">$strain</td>";
			$tablerows2 .= "<td class=\"results\">$genevariation</td>";
			$tablerows2 .= "<td class=\"results\">$age</td>";
			$tablerows2 .= "<td class=\"results\">$sex</td>";
			$tablerows2 .= "<td class=\"results\">$tissue</td>";
			$tablerows2 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows2 .= "<td class=\"results\">$vehicle</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows2 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows2 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<error case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=3 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows3 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows3 .= "<tr>";
			$tablerows3 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows3 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows3 .= "<td class=\"results\">$treatment</td>";
			$tablerows3 .= "<td class=\"results\">$organism</td>";
			$tablerows3 .= "<td class=\"results\">$strain</td>";
			$tablerows3 .= "<td class=\"results\">$genevariation</td>";
			$tablerows3 .= "<td class=\"results\">$age</td>";
			$tablerows3 .= "<td class=\"results\">$sex</td>";
			$tablerows3 .= "<td class=\"results\">$tissue</td>";
			$tablerows3 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows3 .= "<td class=\"results\">$vehicle</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows3 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows3 .= "</tr>\n";
		}
	?>

		<form name="selectsample2" action="agilentexperimentlayouttest.php" method="post">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="14">New RNA Samples</th>
		</tr>
		</thead>
		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>
		<?php
		// We created the rows above, now insert them into the table...
			echo $tablerows;
		?>

		<tr>
		<td><input type="submit" name="Submit" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit12" value="Move to In-queue List"></td>
		<td><input type="submit" name="Submit13" value="Move to Error List"></td>
		<td><input type="submit" name="Print" value="Print"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr>
		</tr>

		<tr>
		<th class="mainheader" colspan="14">In-queue RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows2;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit21" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit22" value="Move to Error List"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>


		<tr>
		<th class="mainheader" colspan="14">Viewed (Done) RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows1;
			//echo "after echo";
		?>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>

		<tr>
		<th class="mainheader" colspan="14">RNA Samples with Errors</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows3;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit3" value="Move to 'Done' List"></td>
		</tr>

		</table>


<?php

	}

		else if($_POST['Submit22']){

		// update the processed field to 1 to indicate that it has been submitted
		$trxSQL = "UPDATE agilent_array_queue set processed=3 where queue_arrayid = $queue_arrayid";
                $trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);

		// now display the unsubmitted and submitted treatments as before
		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=0 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$firstchoice = 1;
		$tablerows = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows .= "<tr>";
			if($firstchoice == 1){

				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\" checked>$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
				$firstchoice = 0;
			}
			else{
				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
			}
			$tablerows .= "</tr>\n";
		}



		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=1 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows1 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows1 .= "<tr>";
			$tablerows1 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows1 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows1 .= "<td class=\"results\">$treatment</td>";
			$tablerows1 .= "<td class=\"results\">$organism</td>";
			$tablerows1 .= "<td class=\"results\">$strain</td>";
			$tablerows1 .= "<td class=\"results\">$genevariation</td>";
			$tablerows1 .= "<td class=\"results\">$age</td>";
			$tablerows1 .= "<td class=\"results\">$sex</td>";
			$tablerows1 .= "<td class=\"results\">$tissue</td>";
			$tablerows1 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows1 .= "<td class=\"results\">$vehicle</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows1 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows1 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<in queue case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=2 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows2 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows2 .= "<tr>";
			$tablerows2 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows2 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows2 .= "<td class=\"results\">$treatment</td>";
			$tablerows2 .= "<td class=\"results\">$organism</td>";
			$tablerows2 .= "<td class=\"results\">$strain</td>";
			$tablerows2 .= "<td class=\"results\">$genevariation</td>";
			$tablerows2 .= "<td class=\"results\">$age</td>";
			$tablerows2 .= "<td class=\"results\">$sex</td>";
			$tablerows2 .= "<td class=\"results\">$tissue</td>";
			$tablerows2 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows2 .= "<td class=\"results\">$vehicle</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows2 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows2 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<error case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=3 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows3 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows3 .= "<tr>";
			$tablerows3 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows3 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows3 .= "<td class=\"results\">$treatment</td>";
			$tablerows3 .= "<td class=\"results\">$organism</td>";
			$tablerows3 .= "<td class=\"results\">$strain</td>";
			$tablerows3 .= "<td class=\"results\">$genevariation</td>";
			$tablerows3 .= "<td class=\"results\">$age</td>";
			$tablerows3 .= "<td class=\"results\">$sex</td>";
			$tablerows3 .= "<td class=\"results\">$tissue</td>";
			$tablerows3 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows3 .= "<td class=\"results\">$vehicle</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows3 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows3 .= "</tr>\n";
		}
	?>

		<form name="selectsample2" action="agilentexperimentlayouttest.php" method="post">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="14">New RNA Samples</th>
		</tr>
		</thead>
		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>
		<?php
		// We created the rows above, now insert them into the table...
			echo $tablerows;
		?>

		<tr>
		<td><input type="submit" name="Submit" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit12" value="Move to In-queue List"></td>
		<td><input type="submit" name="Submit13" value="Move to Error List"></td>
		<td><input type="submit" name="Print" value="Print"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr>
		</tr>

		<tr>
		<th class="mainheader" colspan="14">In-queue RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows2;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit21" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit22" value="Move to Error List"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>


		<tr>
		<th class="mainheader" colspan="14">Viewed (Done) RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows1;
			//echo "after echo";
		?>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>

		<tr>
		<th class="mainheader" colspan="14">RNA Samples with Errors</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows3;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit3" value="Move to 'Done' List"></td>
		</tr>

		</table>


<?php

	}

		else if($_POST['Submit3']){

		// update the processed field to 1 to indicate that it has been submitted
		$trxSQL = "UPDATE agilent_array_queue set processed=1 where queue_arrayid = $queue_arrayid";
                $trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);

		// now display the unsubmitted and submitted treatments as before
		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=0 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$firstchoice = 1;
		$tablerows = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows .= "<tr>";
			if($firstchoice == 1){

				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\" checked>$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
				$firstchoice = 0;
			}
			else{
				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
				$tablerows .= "<td class=\"results\">$array_queue_samplename</td>";
				$tablerows .= "<td class=\"results\">$treatment</td>";
				$tablerows .= "<td class=\"results\">$organism</td>";
				$tablerows .= "<td class=\"results\">$strain</td>";
				$tablerows .= "<td class=\"results\">$genevariation</td>";
				$tablerows .= "<td class=\"results\">$age</td>";
				$tablerows .= "<td class=\"results\">$sex</td>";
				$tablerows .= "<td class=\"results\">$tissue</td>";
				$tablerows .= "<td class=\"results\">$rnagroupsize</td>";
				$tablerows .= "<td class=\"results\">$vehicle</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
				$tablerows .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
				$tablerows .= "<td class=\"results\">$gestationperiod</td>";
			}
			$tablerows .= "</tr>\n";
		}



		// Get the treatment id's and treatment names....
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=1 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows1 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows1 .= "<tr>";
			$tablerows1 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows1 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows1 .= "<td class=\"results\">$treatment</td>";
			$tablerows1 .= "<td class=\"results\">$organism</td>";
			$tablerows1 .= "<td class=\"results\">$strain</td>";
			$tablerows1 .= "<td class=\"results\">$genevariation</td>";
			$tablerows1 .= "<td class=\"results\">$age</td>";
			$tablerows1 .= "<td class=\"results\">$sex</td>";
			$tablerows1 .= "<td class=\"results\">$tissue</td>";
			$tablerows1 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows1 .= "<td class=\"results\">$vehicle</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows1 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows1 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows1 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<in queue case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=2 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows2 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows2 .= "<tr>";
			$tablerows2 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows2 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows2 .= "<td class=\"results\">$treatment</td>";
			$tablerows2 .= "<td class=\"results\">$organism</td>";
			$tablerows2 .= "<td class=\"results\">$strain</td>";
			$tablerows2 .= "<td class=\"results\">$genevariation</td>";
			$tablerows2 .= "<td class=\"results\">$age</td>";
			$tablerows2 .= "<td class=\"results\">$sex</td>";
			$tablerows2 .= "<td class=\"results\">$tissue</td>";
			$tablerows2 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows2 .= "<td class=\"results\">$vehicle</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows2 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows2 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows2 .= "</tr>\n";
		}

		// Get the treatment id's and treatment names....<error case>
		$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where processed=3 ORDER BY queue_arrayid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows3 = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$tablerows3 .= "<tr>";
			$tablerows3 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"queue_arrayid\" value=\"$queue_arrayid\">$queue_arrayid</td>";
			$tablerows3 .= "<td class=\"results\">$array_queue_samplename</td>";
			$tablerows3 .= "<td class=\"results\">$treatment</td>";
			$tablerows3 .= "<td class=\"results\">$organism</td>";
			$tablerows3 .= "<td class=\"results\">$strain</td>";
			$tablerows3 .= "<td class=\"results\">$genevariation</td>";
			$tablerows3 .= "<td class=\"results\">$age</td>";
			$tablerows3 .= "<td class=\"results\">$sex</td>";
			$tablerows3 .= "<td class=\"results\">$tissue</td>";
			$tablerows3 .= "<td class=\"results\">$rnagroupsize</td>";
			$tablerows3 .= "<td class=\"results\">$vehicle</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$dose $doseunits</td>";
			$tablerows3 .= "<td class=\"results\" align=\"right\">$duration $durationunits</td>";
			$tablerows3 .= "<td class=\"results\">$gestationperiod</td>";
			$tablerows3 .= "</tr>\n";
		}
	?>

		<form name="selectsample2" action="agilentexperimentlayouttest.php" method="post">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="14">New RNA Samples</th>
		</tr>
		</thead>
		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>
		<?php
		// We created the rows above, now insert them into the table...
			echo $tablerows;
		?>

		<tr>
		<td><input type="submit" name="Submit" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit12" value="Move to In-queue List"></td>
		<td><input type="submit" name="Submit13" value="Move to Error List"></td>
		<td><input type="submit" name="Print" value="Print"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr>
		</tr>

		<tr>
		<th class="mainheader" colspan="14">In-queue RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows2;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit21" value="Move to 'Done' List"></td>
		<td><input type="submit" name="Submit22" value="Move to Error List"></td>
		</tr>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>


		<tr>
		<th class="mainheader" colspan="14">Viewed (Done) RNA Samples</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows1;
			//echo "after echo";
		?>

		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>
		<tr> </tr>


		<tr>
		<th class="mainheader" colspan="14">RNA Samples with Errors</th>
		</tr>

		<tr>
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Strain</b></td>
		<td class="questionanswer" ><b>Genetic Variation</b></td>
		<td class="questionanswer" ><b>Age</b></td>
		<td class="questionanswer" ><b>Sex</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
		<td class="questionanswer" ><b>RNA Groupsize</b></td>
		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Dose/Units</b></td>
		<td class="questionanswer" ><b>Duration/Units</b></td>
		<td class="questionanswer" ><b>Gestation Period</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows3;
			//echo "after echo";
		?>

		<tr>
		<td><input type="submit" name="Submit3" value="Move to 'Done' List"></td>
		</tr>

		</table>

<?php

	}

	elseif ($_POST['Print']){

	// query the database to get attributes of selected user treatment. Then display it using html form
	$trxSQL = "SELECT queue_arrayid, organism, rnagroupsize,concentration, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, control, doseunits, durationunits, pregnant, array_queue_samplename, gestationperiod FROM agilent_array_queue where queue_arrayid = $queue_arrayid";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows = "";
		#while(list($queue_arrayid, $organism, $rnagroupsize, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $array_queue_samplename, $gestationperiod) = mysql_fetch_array($trxResult))
		while($row = $trxResult->FetchRow())
		{
			$queue_arrayid = $row[0];
			$organism = $row[1];
			$rnagroupsize = $row[2];
			$strain = $row[3];
			$genevariation = $row[4];
			$age = $row[5];
			$sex = $row[6];
			$tissue = $row[7];
			$treatment = $row[8];
			$vehicle = $row[9];
			$dose = $row[10];
			$route = $row[11];
			$dosagetime=$row[12];
			$duration=$row[13];
			$harvesttime = $row[14];
			$control = $row[15];
			$doseunits = $row[16];
			$durationunits = $row[17];
			$pregnant = $row[18];
			$array_queue_samplename = $row[19];
			$gestationperiod = $row[20];
			$ageunits = "weeks";
			$tablerows .= "<table border=\"2\" width=\"600\">";
			$tablerows .= "<thead>";
			$tablerows .= "<tr>";
			$tablerows .= "<th class=\"mainheader\" colspan=\"2\">New Treatment 								Details</th></tr></thead>";

			$tablerows .= "<tr>
					<td width=\"50%\"><font size=\"3\"><strong>Sample ID</strong></font></td>
					<td><font size=\"3\"><strong>$queue_arrayid</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Sample Name</strong></font></td>
					<td><font size=\"3\"><strong>$array_queue_samplename</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Treatment</strong></font></td>
					<td><font size=\"3\"><strong>$treatment</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Organism</strong></font></td>
					<td><font size=\"3\"><strong>$organism</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Strain</strong></font></td>
					<td><font size=\"3\"><strong>$strain</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Gene Variation</strong></font></td>
					<td><font size=\"3\"><strong>$genevariation</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Age</strong></font></td>
					<td><font size=\"3\"><strong>$age $ageunits</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Sex</strong></font></td>
					<td><font size=\"3\"><strong>$sex</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Tissue</strong></font></td>
					<td><font size=\"3\"><strong>$tissue</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>RNA Group Size</strong></font></td>
					<td><font size=\"3\"><strong>$rnagroupsize</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Concentration</strong></font></td>
					<td><font size=\"3\"><strong>$concentration</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Vehicle</strong></font></td>
					<td><font size=\"3\"><strong>$vehicle</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Dosage</strong></font></td>
					<td><font size=\"3\"><strong>$dose $doseunits</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Dosage Time</strong></font></td>
					<td><font size=\"3\"><strong>$dosagetime</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Duration</strong></font></td>
					<td><font size=\"3\"><strong>$duration $durationunits</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Harvest Time</strong></font></td>
					<td><font size=\"3\"><strong>$harvesttime</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Pregnant?</strong></font></td>
					<td><font size=\"3\"><strong>$pregnant</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Gestation Period</strong></font></td>
					<td><font size=\"3\"><strong>$gestationperiod</strong></font></td>
				       ";
			$tablerows .= "<tr>
					<td><font size=\"3\"><strong>Control</strong></font></td>
					<td><font size=\"3\"><strong>$control</strong></font></td>
				       ";
			$tablerows .= "</table>";
		}
	?>


	<form name="selectsample3" action="agilentexperimentlayouttest.php" method="post">

	<?php
	echo $tablerows;
	}

}
?>



























