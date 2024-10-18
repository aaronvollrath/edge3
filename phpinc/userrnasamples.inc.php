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
// Function to get field values
function getFieldValue($db, $field, $table, $whereField, $whereValue) {
    $sql = "SELECT $field FROM $table WHERE $whereField = ?";
    $result = $db->Execute($sql, array($whereValue));
    return $result ? $result->FetchRow() : null;
}

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
		if(action == 3 && ischecked){
				//alert("editing stub....");
				myWin = location.replace("../agilentexperiment-useradmin.php?deleternasample=1&sampleid=" + selectedvalue);
		}
		return ischecked;
	}
</script>

<h3 class="contenthead">Your Submitted RNA Samples</h3>
<?php
$submitted ="";
$sampleid = "";
$printed = "";
$trx = "";
$sampleid = "";
$priv_level = $_SESSION['priv_level'];


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

		// Get the RNA samples that have not been hybridized to an array.
		

// Prepare SQL with placeholders for parameterized queries
$trxSQL = "SELECT s.sampleid, s.samplename, s.organism, s.concentration, s.tissue, s.treatment, s.vehicle, s.dose, s.route, s.submitter, s.doseunits 
           FROM agilent_rnasample as s 
           WHERE s.submitterid = ? 
           ORDER BY sampleid ASC";
// Execute the query with parameter binding
$trxResult = $db->Execute($trxSQL, array($userid));
if (!$trxResult) {
    throw new Exception("Database error: " . $db->ErrorMsg());
}

$firstchoice = true;
$tablerows = "";
$tablerows1 = "";

while ($row = $trxResult->FetchRow()) {
    $sampleid = $row['sampleid'];
    $samplename = $row['samplename'];
    $organismId = $row['organism'];
    $concentration = $row['concentration'] . " micrograms/microliter";
    $tissueId = $row['tissue'];
    $treatmentId = $row['treatment'];
    $vehicleId = $row['vehicle'];
    $dose = $row['dose'];
    $routeId = $row['route'];
    $submitter = $row['submitter'];
    $doseunitsId = $row['doseunits'];

    // Check if the sample has been hybridized
    $hybSQL = "SELECT COUNT(*) FROM agilent_arrayinfo WHERE cy3rnasample = ? OR cy5rnasample = ?";
    $hybResult = $db->Execute($hybSQL, array($sampleid, $sampleid));
	if (!$hybResult) {
		throw new Exception("Database error: " . $db->ErrorMsg());
	}
    $arow = $hybResult->FetchRow();
    $hybridized = ($arow[0] >= 1);

    // Fetch additional information
    $organism = getFieldValue($db, 'organism', 'agilentarrays', 'id', $organismId);
    $tissue = getFieldValue($db, 'tissue', 'tissue', 'tissueid', $tissueId);
    $treatment = getFieldValue($db, 'chemical', 'chem', 'chemid', $treatmentId);
    $doseUnit = getFieldValue($db, 'doseunit', 'doseunit', 'doseunitid', $doseunitsId);
    $vehicle = getFieldValue($db, 'vehicle', 'vehicle', 'vehicleid', $vehicleId);
    $route = getFieldValue($db, 'route', 'route', 'routeid', $routeId);

    // Construct table rows based on hybridization status
    $rowContent = "
    <td class=\"questionparameter\"><input type=\"radio\" name=\"sampleid\" value=\"$sampleid\"" . ($firstchoice ? "checked" : "") . ">$sampleid</td>
    <td class=\"results\">$samplename</td>
    <td class=\"results\">$submitter</td>
    <td class=\"results\">$concentration</td>
    <td class=\"results\">" . (isset($organism['organism']) && $organism['organism'] ? $organism['organism'] : 'N/A') . "</td>
    <td class=\"results\">" . (isset($tissue['tissue']) && $tissue['tissue'] ? $tissue['tissue'] : 'N/A') . "</td>
    <td class=\"results\">" . (isset($treatment['chemical']) && $treatment['chemical'] ? $treatment['chemical'] : 'N/A') . "</td>
    <td class=\"results\">$dose " . (isset($doseUnit['doseunit']) && $doseUnit['doseunit'] ? $doseUnit['doseunit'] : 'N/A') . "</td>
    <td class=\"results\">" . (isset($vehicle['vehicle']) && $vehicle['vehicle'] ? $vehicle['vehicle'] : 'N/A') . "</td>
    <td class=\"results\">" . (isset($route['route']) && $route['route'] ? $route['route'] : 'N/A') . "</td>";

    if ($hybridized) {
        $tablerows1 .= "<tr>$rowContent</tr>\n";
    } else {
        $tablerows .= "<tr>$rowContent</tr>\n";
    }

    $firstchoice = false;
}



	

	
?>


		<form name="rnasamplequeue" action="agilentexperiment-useradmin.php?rnasamplequeue=1" method="post" onsubmit="return checkForSelection(0)">
		<div dojoType='dijit.TitlePane' title='New RNA samples' open='false'>
		<div style="width : 800px; height : 600px; overflow : auto; ">
		<table class="question" width="400">
		<thead>
		<tr>
		<th class="mainheader" colspan="14">Unhybridized RNA Samples</th>
		</tr>
		</thead>
<tr>
		<td></td>
		<td><input type="button" name="Print" value="Print this Sample" onclick="checkForSelection(1)"></td>
		<td><input type="button" name="Edit" value="Edit this Sample" onclick="checkForSelection(2)"></td>
		<td><input type="button" name="Delete" value="Delete this Sample" onclick="checkForSelection(3)"></td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		
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

		

<div dojoType='dijit.TitlePane' title='RNA samples already hybridized to arrays' open='false'>
		<div style="width : 800px; height : 600px; overflow : auto; ">
		<table class="question" width="400">
		<thead>

		<tr>
		<th class="mainheader" colspan="14">Previously Hybridized RNA Samples</th>
		</tr>
		</thead>
<tr>
		<td></td><td><input type="button" name="Print2" value="Print this Sample" onclick="checkForSelection(1)"></td>
		<td><input type="button" name="Edit2" value="Edit this Sample" onclick="checkForSelection(2)"></td><td></td><td></td><td></td><td></td><td></td><td></td>
		
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


?>


























