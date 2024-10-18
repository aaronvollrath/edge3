<?php

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

?>


<script type="text/javascript">



	

	function checkForSelection(action){

		
		var value;
		var ischecked = false;
		var selectedvalue;
		// We need the following, because if only a single sampleid radio button is available
		// the length is undefined....
		if(document.rnasamplequeue.sampleid.checked) { // one element to fetch
			ischecked = true;
			value = document.rnasamplequeue.sampleid.value;
			selectedvalue = value;
				
		}else{
			for(var i=0; i<document.rnasamplequeue.sampleid.length; i++){
				//document.agilentrnasample.pregnant[i].disabled = false;
				//document.agilentrnasample.gestation.disabled = true
				value = document.rnasamplequeue.sampleid[i].value;
				//alert("value: " + value);
				if(document.rnasamplequeue.sampleid[i].checked){
					ischecked = true;
					
					selectedvalue = document.rnasamplequeue.sampleid[i].value;
					
				}
				
					
			}
		}
		if(!ischecked){
			alert("You need to select a RNA sample!");
		}
		if(action == 1 && ischecked){
				myWin = open("./printrnasample.php?sampleid=" + selectedvalue);
		}
		if(action == 2 && ischecked){
				//alert("editing stub....");
				myWin = location.replace("../agilentexperiment-useradmin.php?rnasubmission=1&sampleid=" + selectedvalue);
		}
		return ischecked;
	}
</script>

<h3 class="contenthead">Submitted RNA Samples</h3>
<?php
$submitted ="";
$sampleid = "";
$printed = "";
$trx = "";
$sampleid = "";
$priv_level = $_SESSION['priv_level'];
if($priv_level != 99){
	echo "Sorry, you are not authorized to access this page.";
}
else{

	//analyze($_POST);
	if(isset($_POST['Submit'])){
		$submitted = $_POST['Submit'];
	}
	if(isset($_POST['sampleid'])){
		$sampleid =$_POST['sampleid'];
	}
	if(isset($_POST['sampleid'])){
		$printed = $_POST['Print'];
	}
	
		

	if(isset($_POST['queuethis'])){
		
		
		// update the processed field to 1 to indicate that it has been submitted
		$trxSQL = "UPDATE agilent_rnasample set queuestatus=2 where sampleid = $sampleid";
                $trxResult = mysql_query($trxSQL, $db);
		
	}
	
	if(isset($_POST['errorthis'])){
		echo " Moving to error list....<br>";
	
		// change the queuestatus field to 3 to signify that there is an error in this rna sample submission
		$trxSQL = "UPDATE agilent_rnasample set queuestatus=3 where sampleid = $sampleid";
                $trxResult = mysql_query($trxSQL, $db);
	
	}

	if(isset($_POST['sampleid'])){
		$trx = $_POST['sampleid'];
	}
	if(isset($_POST['sampleid'])){
		$sampleid = $_POST['sampleid'];
	}
	//if($submitted != true && $printed != true) { // if the trx form has not been submitted/printed yet...
	if($printed != true) 	{ // if the trx form has not been submitted/printed yet...


?>

  <?php

		// Get the RNA samples that have a queuestatus = 0.  These are newly entered RNA samples that have not been reviewed.
		$trxSQL = "SELECT sampleid, samplename, organism, concentration, tissue, treatment, vehicle, dose, route, submitter, doseunits FROM agilent_rnasample where queuestatus=0 ORDER BY sampleid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$firstchoice = 1;
		$tablerows = "";
		#while(list($sampleid, $samplename, $organism, $concentration, $tissue, $treatment, $vehicle, $dose, $route, $submitter, $doseunits) = mysql_fetch_array($trxResult))
		while($row=$trxResult->FetchRow())
		{

			$sampleid = $row[0];
			$samplename = $row[1];
			$organism = $row[2];
			$concentration = $row[3]." micrograms/microliter";
			$tissue = $row[4];
			$treatment = $row[5];
			$vehicle = $row[6];
			$dose = $row[7];
			$route = $row[8];
			$submitter = $row[9];
			$doseunits = $row[10];
			
			$tablerows .= "<tr>";
			if($firstchoice == 1){

				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"sampleid\" value=\"$sampleid\" checked>$sampleid</td>";
				$firstchoice = 0;
			}else{

				$tablerows .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"sampleid\" value=\"$sampleid\">$sampleid</td>";
			}

				$orgsql = "SELECT organism FROM agilentarrays WHERE id = $organism";
				$orgresult = $db->Execute($orgsql);//mysql_query($orgsql, $db);
				$organism = $orgresult->FetchRow();//mysql_fetch_row($orgresult);
				$tissuesql = "SELECT tissue FROM tissue WHERE tissueid = $tissue";
				$tissueresult = $db->Execute($tissuesql);//mysql_query($tissuesql, $db);
				$tissue = $tissueresult->FetchRow();//mysql_fetch_row($tissueresult);
				$treatmentsql = "SELECT chemical FROM chem WHERE chemid=$treatment";
				$treatmentresult = $db->Execute($treatmentsql);//mysql_query($treatmentsql, $db);
				$treatment = $treatmentresult->FetchRow();//mysql_fetch_row($treatmentresult);
				$dosesql = "SELECT doseunit from doseunit WHERE doseunitid = $doseunits";
				$doseresult = $db->Execute($dosesql);//mysql_query($dosesql, $db);
				$doserow = $doseresult->FetchRow();//mysql_fetch_row($doseresult);
				$vehiclesql = "SELECT vehicle FROM vehicle WHERE vehicleid = $vehicle";
				$vehicleresult = $db->Execute($vehiclesql);//mysql_query($vehiclesql, $db);
				$vehicle = $vehicleresult->FetchRow();//mysql_fetch_row($vehicleresult);
				$routesql = "SELECT route FROM route WHERE routeid = $route";
				$routeresult = $db->Execute($routesql);//mysql_query($routesql, $db);
				$route = $routeresult->FetchRow();//mysql_fetch_row($routeresult);
				$tablerows .= "<td class=\"results\">$samplename</td>";
				$tablerows .= "<td class=\"results\">$submitter</td>";
				$tablerows .= "<td class=\"results\">$concentration</td>";
				$tablerows .= "<td class=\"results\">$organism[0]</td>";
				
				$tablerows .= "<td class=\"results\">$tissue[0]</td>";
				
				$tablerows .= "<td class=\"results\">$treatment[0]</td>";
					
				$tablerows .= "<td class=\"results\">$dose $doserow[0]</td>";
				
				$tablerows .= "<td class=\"results\">$vehicle[0]</td>";
				
				$tablerows .= "<td class=\"results\">$route[0]</td>";
			
				
			$tablerows .= "</tr>\n";
		}


		// Get the RNA samples that have a queuestatus = 1.  These are rna samples that have already been hybridized to arrays.  They cannot be moved or edited....

		$trxSQL = "SELECT sampleid, samplename, organism, concentration, tissue, treatment, vehicle, dose, route, submitter, doseunits FROM agilent_rnasample where queuestatus=1 ORDER BY sampleid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows1 = "";
		#while(list($sampleid, $samplename, $organism, $concentration, $tissue, $treatment, $vehicle, $dose, $route, $submitter, $doseunits) = mysql_fetch_array($trxResult))
		while($row=$trxResult->FetchRow())
		{

			$sampleid = $row[0];
			$samplename = $row[1];
			$organism = $row[2];
			$concentration = $row[3];
			$tissue = $row[4];
			$treatment = $row[5];
			$vehicle = $row[6];
			$dose = $row[7];
			$route = $row[8];
			$submitter = $row[9];
			$doseunits = $row[10];
			if($firstchoice == 1){

				$tablerows1 .= "<td class=\"questionparameter\">$sampleid</td>";
				$firstchoice = 0;
			}else{

				$tablerows1 .= "<td class=\"questionparameter\">$sampleid</td>";
			}

				$orgsql = "SELECT organism FROM agilentarrays WHERE id = $organism";
				$orgresult = $db->Execute($orgsql);//mysql_query($orgsql, $db);
				$organism = $orgresult->FetchRow();//mysql_fetch_row($orgresult);
				$tissuesql = "SELECT tissue FROM tissue WHERE tissueid = $tissue";
				$tissueresult = $db->Execute($tissuesql);//mysql_query($tissuesql, $db);
				$tissue = $tissueresult->FetchRow();//mysql_fetch_row($tissueresult);
				$treatmentsql = "SELECT chemical FROM chem WHERE chemid=$treatment";
				$treatmentresult = $db->Execute($treatmentsql);//mysql_query($treatmentsql, $db);
				$treatment = $treatmentresult->FetchRow();//mysql_fetch_row($treatmentresult);
				$dosesql = "SELECT doseunit from doseunit WHERE doseunitid = $doseunits";
				$doseresult = $db->Execute($dosesql);//mysql_query($dosesql, $db);
				$doserow = $doseresult->FetchRow();//mysql_fetch_row($doseresult);
				$vehiclesql = "SELECT vehicle FROM vehicle WHERE vehicleid = $vehicle";
				$vehicleresult = $db->Execute($vehiclesql);//mysql_query($vehiclesql, $db);
				$vehicle = $vehicleresult->FetchRow();//mysql_fetch_row($vehicleresult);
				$routesql = "SELECT route FROM route WHERE routeid = $route";
				$routeresult = $db->Execute($routesql);//mysql_query($routesql, $db);
				$route = $routeresult->FetchRow();//mysql_fetch_row($routeresult);
				$tablerows1 .= "<td class=\"results\">$samplename</td>";
				$tablerows1 .= "<td class=\"results\">$submitter</td>";
				$tablerows1 .= "<td class=\"results\">$concentration</td>";
				$tablerows1 .= "<td class=\"results\">$organism[0]</td>";
				
				$tablerows1 .= "<td class=\"results\">$tissue[0]</td>";
				
				$tablerows1 .= "<td class=\"results\">$treatment[0]</td>";
					
				$tablerows1 .= "<td class=\"results\">$dose $doserow[0]</td>";
				
				$tablerows1 .= "<td class=\"results\">$vehicle[0]</td>";
				
				$tablerows1 .= "<td class=\"results\">$route[0]</td>";
				
			$tablerows1 .= "</tr>\n";
		}

		
		// Get the RNA samples that have a queuestatus = 2.  These are arrays that have been reviewed and put into the queue for processing.

		$trxSQL = "SELECT sampleid, samplename, organism, concentration, tissue, treatment, vehicle, dose, route, submitter, doseunits FROM agilent_rnasample where queuestatus=2 ORDER BY sampleid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows2 = "";
		#while(list($sampleid, $samplename, $organism, $concentration, $tissue, $treatment, $vehicle, $dose, $route, $submitter, $doseunits) = mysql_fetch_array($trxResult))
		while($row=$trxResult->FetchRow())
		{

			$sampleid = $row[0];
			$samplename = $row[1];
			$organism = $row[2];
			$concentration = $row[3];
			$tissue = $row[4];
			$treatment = $row[5];
			$vehicle = $row[6];
			$dose = $row[7];
			$route = $row[8];
			$submitter = $row[9];
			$doseunits = $row[10];
			if($firstchoice == 1){

				$tablerows2 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"sampleid\" value=\"$sampleid\" checked>$sampleid</td>";
				$firstchoice = 0;
			}else{

				$tablerows2 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"sampleid\" value=\"$sampleid\">$sampleid</td>";
			}
				
				$orgsql = "SELECT organism FROM agilentarrays WHERE id = $organism";
				$orgresult = $db->Execute($orgsql);//mysql_query($orgsql, $db);
				$organism = $orgresult->FetchRow();//mysql_fetch_row($orgresult);
				$tissuesql = "SELECT tissue FROM tissue WHERE tissueid = $tissue";
				$tissueresult = $db->Execute($tissuesql);//mysql_query($tissuesql, $db);
				$tissue = $tissueresult->FetchRow();//mysql_fetch_row($tissueresult);
				$treatmentsql = "SELECT chemical FROM chem WHERE chemid=$treatment";
				$treatmentresult = $db->Execute($treatmentsql);//mysql_query($treatmentsql, $db);
				$treatment = $treatmentresult->FetchRow();//mysql_fetch_row($treatmentresult);
				$dosesql = "SELECT doseunit from doseunit WHERE doseunitid = $doseunits";
				$doseresult = $db->Execute($dosesql);//mysql_query($dosesql, $db);
				$doserow = $doseresult->FetchRow();//mysql_fetch_row($doseresult);
				$vehiclesql = "SELECT vehicle FROM vehicle WHERE vehicleid = $vehicle";
				$vehicleresult = $db->Execute($vehiclesql);//mysql_query($vehiclesql, $db);
				$vehicle = $vehicleresult->FetchRow();//mysql_fetch_row($vehicleresult);
				$routesql = "SELECT route FROM route WHERE routeid = $route";
				$routeresult = $db->Execute($routesql);//mysql_query($routesql, $db);
				$route = $routeresult->FetchRow();//mysql_fetch_row($routeresult);
				$tablerows2 .= "<td class=\"results\">$samplename</td>";
				$tablerows2 .= "<td class=\"results\">$submitter</td>";
				$tablerows2 .= "<td class=\"results\">$concentration</td>";
				$tablerows2 .= "<td class=\"results\">$organism[0]</td>";
				
				$tablerows2 .= "<td class=\"results\">$tissue[0]</td>";
				
				$tablerows2 .= "<td class=\"results\">$treatment[0]</td>";
					
				$tablerows2 .= "<td class=\"results\">$dose $doserow[0]</td>";
				
				$tablerows2 .= "<td class=\"results\">$vehicle[0]</td>";
				
				$tablerows2 .= "<td class=\"results\">$route[0]</td>";
				
			$tablerows2 .= "</tr>\n";
		}

				// Get the RNA samples that have a queuestatus = 3.  These are arrays that have been reviewed and deemed to have errors.

		$trxSQL = "SELECT sampleid, samplename, organism, concentration, tissue, treatment, vehicle, dose, route, submitter, doseunits FROM agilent_rnasample where queuestatus=3 ORDER BY sampleid ASC";
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$tablerows3 = "";
		#while(list($sampleid, $samplename, $organism, $concentration, $tissue, $treatment, $vehicle, $dose, $route, $submitter, $doseunits) = mysql_fetch_array($trxResult))
		while($row=$trxResult->FetchRow())
		{

			$sampleid = $row[0];
			$samplename = $row[1];
			$organism = $row[2];
			$concentration = $row[3];
			$tissue = $row[4];
			$treatment = $row[5];
			$vehicle = $row[6];
			$dose = $row[7];
			$route = $row[8];
			$submitter = $row[9];
			$doseunits = $row[10];
			if($firstchoice == 1){

				$tablerows3 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"sampleid\" value=\"$sampleid\" checked>$sampleid</td>";
				$firstchoice = 0;
			}else{

				$tablerows3 .= "<td class=\"questionparameter\"><input type=\"radio\" name=\"sampleid\" value=\"$sampleid\">$sampleid</td>";
			}
				$orgsql = "SELECT organism FROM agilentarrays WHERE id = $organism";
				$orgresult = $db->Execute($orgsql);//mysql_query($orgsql, $db);
				$organism = $orgresult->FetchRow();//mysql_fetch_row($orgresult);
				$tissuesql = "SELECT tissue FROM tissue WHERE tissueid = $tissue";
				$tissueresult = $db->Execute($tissuesql);//mysql_query($tissuesql, $db);
				$tissue = $tissueresult->FetchRow();//mysql_fetch_row($tissueresult);
				$treatmentsql = "SELECT chemical FROM chem WHERE chemid=$treatment";
				$treatmentresult = $db->Execute($treatmentsql);//mysql_query($treatmentsql, $db);
				$treatment = $treatmentresult->FetchRow();//mysql_fetch_row($treatmentresult);
				$dosesql = "SELECT doseunit from doseunit WHERE doseunitid = $doseunits";
				$doseresult = $db->Execute($dosesql);//mysql_query($dosesql, $db);
				$doserow = $doseresult->FetchRow();//mysql_fetch_row($doseresult);
				$vehiclesql = "SELECT vehicle FROM vehicle WHERE vehicleid = $vehicle";
				$vehicleresult = $db->Execute($vehiclesql);//mysql_query($vehiclesql, $db);
				$vehicle = $vehicleresult->FetchRow();//mysql_fetch_row($vehicleresult);
				$routesql = "SELECT route FROM route WHERE routeid = $route";
				$routeresult = $db->Execute($routesql);//mysql_query($routesql, $db);
				$route = $routeresult->FetchRow();//mysql_fetch_row($routeresult);
				$tablerows3 .= "<td class=\"results\">$samplename</td>";
				$tablerows3 .= "<td class=\"results\">$submitter</td>";
				$tablerows3 .= "<td class=\"results\">$concentration</td>";
				$tablerows3 .= "<td class=\"results\">$organism[0]</td>";
				
				$tablerows3 .= "<td class=\"results\">$tissue[0]</td>";
				
				$tablerows3 .= "<td class=\"results\">$treatment[0]</td>";
					
				$tablerows3 .= "<td class=\"results\">$dose $doserow[0]</td>";
				
				$tablerows3 .= "<td class=\"results\">$vehicle[0]</td>";
				
				$tablerows3 .= "<td class=\"results\">$route[0]</td>";
				
			$tablerows3 .= "</tr>\n";
		}

	
?>


		<form name="rnasamplequeue" action="agilentexperiment-useradmin.php?rnasamplequeue=1" method="post" onsubmit="return checkForSelection(0)">
		<div dojoType='dijit.TitlePane' title='New RNA samples' open='false'>
		<div style="width : 800px; height : 600px; overflow : auto; ">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="14">New RNA Samples</th>
		</tr>
		</thead>
<tr>
		<td><!--
			<input type="submit" name="Submit" value="Move to 'Done' List"></td>
		--></td>
		<td><input type="submit" name="queuethis" value="Move to In-queue List"></td>
		<td><input type="submit" name="errorthis" value="Move to Error List"></td>
		<td><input type="button" name="Print" value="Print this Sample" onclick="checkForSelection(1)"></td>
		<td><input type="button" name="Print" value="Edit this Sample" onclick="checkForSelection(2)"></td>
		</tr>
		<tr>

		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer"><b>Submitter</b></td>
		<td class="questionanswer"><b>Sample Concentration</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Dose</b></td>

		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Route</b></td>
		
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			echo $tablerows;
		?>

		
		</table>
		</div>
		</div>

		<div dojoType='dijit.TitlePane' title='RNA samples evaluated and put in array queue' open='false'>
<div style="width : 800px; height : 600px; overflow : auto; ">
		<table class="question" width="400">
		<thead>

		<tr>
		<th class="mainheader" colspan="14">In-queue RNA Samples</th>
		</tr>
		</thead>
		<tr>
		<td><!--
			<input type="submit" name="Submit" value="Move to 'Done' List"></td>
		--></td>
		<td><input type="submit" name="queuethis" value="Move to In-queue List"></td>
		<td><input type="submit" name="errorthis" value="Move to Error List"></td>
		<td><input type="button" name="Print" value="Print this Sample" onclick="checkForSelection(1)"></td>
		</tr>
		<tr>

		
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer"><b>Submitter</b></td>
		<td class="questionanswer"><b>Sample Concentration</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Dose</b></td>

		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Route</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows2;
			//echo "after echo";
		?>

		
		</table>
		</div>
		</div>
		
		<div dojoType='dijit.TitlePane' title='RNA samples with Errors' open='false'>
		<div style="width : 800px; height : 600px; overflow : auto; ">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="14">RNA Samples with Errors</th>
		</tr>
		</thead>
<tr>
		<td><!--
			<input type="submit" name="Submit" value="Move to 'Done' List"></td>
		--></td>
		<td><input type="submit" name="queuethis" value="Move to In-queue List"></td>
		<td><input type="button" name="Print" value="Edit this Sample" onclick="checkForSelection(2)"></td>
		<td><input type="button" name="Print" value="Print this Sample" onclick="checkForSelection(1)"></td>
		</tr>
		<tr>

		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer"><b>Submitter</b></td>
		<td class="questionanswer"><b>Sample Concentration</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Dose</b></td>

		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Route</b></td>
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows3;
			//echo "after echo";
		?>

		</table>
		</div>
		</div>
<div dojoType='dijit.TitlePane' title='RNA samples already hybridized to arrays' open='false'>
		<div style="width : 800px; height : 600px; overflow : auto; ">
		<table class="question" width="400">
		<thead>

		<tr>
		<th class="mainheader" colspan="14">Previously Hybridized RNA Samples</th>
		</tr>
		</thead>
<tr>
		<td><!--
			<input type="submit" name="Submit" value="Move to 'Done' List"></td>
		--></td>
		<td></td>
		<td></td>
		<td></td>
		</tr>
		<tr>

		
		<td class="questionanswer" ><b>Samp ID#</b></td>
		<td class="questionanswer" ><b>Sample Name</b></td>
		<td class="questionanswer"><b>Submitter</b></td>
		<td class="questionanswer"><b>Sample Concentration</b></td>
		<td class="questionanswer" ><b>Organism</b></td>
		<td class="questionanswer" ><b>Tissue</b></td>
<td class="questionanswer" ><b>Treatment</b></td>
		<td class="questionanswer" ><b>Dose</b></td>

		<td class="questionanswer" ><b>Vehicle</b></td>
		<td class="questionanswer" ><b>Route</b></td>	
		</tr>

		<?php
		// We created the rows above, now insert them into the table...
			//echo "before echo";
			echo $tablerows1;
			//echo "after echo";
		?>
		</table>
		</div>
		</div>
<?php
	} //end of submitted=false




}
?>


























