<?php

/*
	printrnasample.php


*/
require("./phpinc/edge3_db_connect.inc");
require("utilityfunctions.inc");





$sampleid=$_GET['sampleid'];


$samplesql = "SELECT * FROM agilent_rnasample WHERE sampleid = $sampleid";

$sampleresult = mysql_query($samplesql,$db);
$assocrow = mysql_fetch_assoc($sampleresult);

//list($sampleid, $array_platform, $organism, $rnagroupsize, $concentration, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose, $route, $dosagetime, $duration, $harvesttime, $control, $doseunits, $durationunits, $pregnant, $samplename, $gestationperiod, $expid, $experiment, $processed, $datesubmitted, $submitter, $reference_sample, $submitterid, $queuestatus) = mysql_fetch_array($sampleresult);
		$tablerows ="<table>";
			$tablerows .= "<tr>";
				$tablerows .= "<td class=\"questionparameter\">Sample ID#</td><td class=\"results\">".$assocrow['sampleid']."</td>";
				$tablerows .="</tr><tr>";
				$orgsql = "SELECT organism FROM agilentarrays WHERE id = ".$assocrow['organism'];
				$orgresult = mysql_query($orgsql, $db);
				$organism = mysql_fetch_row($orgresult);
				sqlerrorcheck($orgsql, $db);
				$tissuesql = "SELECT tissue FROM tissue WHERE tissueid = ".$assocrow['tissue'];
				$tissueresult = mysql_query($tissuesql, $db);
				$tissue = mysql_fetch_row($tissueresult);
				sqlerrorcheck($tissuesql, $db);
				$treatmentsql = "SELECT chemical FROM chem WHERE chemid=".$assocrow['treatment'];
				$treatmentresult = mysql_query($treatmentsql, $db);
				$treatment = mysql_fetch_row($treatmentresult);
				sqlerrorcheck($treatmentsql, $db);
				$dosesql = "SELECT doseunit from doseunit WHERE doseunitid =".$assocrow['doseunits'] ;
				$doseresult = mysql_query($dosesql, $db);
				$doserow = mysql_fetch_row($doseresult);
				sqlerrorcheck($dosesql, $db);
				$vehiclesql = "SELECT vehicle FROM vehicle WHERE vehicleid =".$assocrow['vehicle'];
				$vehicleresult = mysql_query($vehiclesql, $db);
				$vehicle = mysql_fetch_row($vehicleresult);
				sqlerrorcheck($vehiclesql, $db);
				$routesql = "SELECT route FROM route WHERE routeid =".$assocrow['route'];
				$routeresult = mysql_query($routesql, $db);
				$route = mysql_fetch_row($routeresult);
				sqlerrorcheck($routesql, $db);
				//echo "$routesql<br>";
				$durationsql = "SELECT durationunit from durationunit where durationunitid =".$assocrow['durationunits']; 
				$durationresult = mysql_query($durationsql, $db);
				$durationunits = mysql_fetch_row($durationresult);
				sqlerrorcheck($durationsql, $db);
				$durationunits = $durationunits[0];
				$strainsql = "SELECT strain FROM strain WHERE strainid = ".$assocrow['strain'];
				$strainresult = mysql_query($strainsql, $db);
				$strain = mysql_fetch_row($strainresult);
				sqlerrorcheck($strainsql, $db);
				$strain = $strain[0];
				$variationsql = "SELECT genevariation FROM genevariation WHERE genevariationid = ".$assocrow['genevariation'];
				$variationresult = mysql_query($variationsql, $db);
				$variation = mysql_fetch_row($variationresult);
				$variation = $variation[0];
				sqlerrorcheck($variationsql, $db);
				$ageunitsql = "SELECT ageunit FROM ageunit WHERE ageunitid = ".$assocrow['ageunits'];
				$ageunitresult = mysql_query($ageunitsql, $db);
				$ageunitstr = mysql_fetch_row($ageunitresult);
				$ageunitstr = $ageunitstr[0];
				sqlerrorcheck($ageunitsql, $db);

				if($queuestatus == 0){

					$queuestatus = "RNA form submitted.  Waiting for review...";
				}elseif($queuestatus == 1){

					$queuestaus = "RNA sample hybridized to array.";
				}elseif($queuestatus == 2){
					$queuestatus = "RNA sample in queue to be hybridized.";
				}else{
					$queuestaus = "RNA sample has an error.";
				}


				$tablerows .= "<td class=\"questionparameter\">Sample Name</td><td class=\"results\">".$assocrow['samplename']."</td>";
$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Organism</td><td class=\"results\">$organism[0]</td>";
				$tablerows .="</tr><tr>";
				
				$tablerows .= "<td class=\"questionparameter\">Tissue</td><td class=\"results\">$tissue[0]</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">RNA Group Size</td><td class=\"results\">".$assocrow['rnagroupsize']."</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">RNA Concentration</td><td class=\"results\">".$assocrow['concentration']." micrograms/microliter</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Organism Strain</td><td class=\"results\">$strain</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Genetic Variation</td><td class=\"results\">$variation</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Organism Age</td><td class=\"results\">".$assocrow['age']." $ageunitstr</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Organism Sex</td><td class=\"results\">".$assocrow['sex']."</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Pregnant</td><td class=\"results\">".$assocrow['pregnant']."</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Gestation Length</td><td class=\"results\">".$assocrow['gestationperiod']." days</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Tissue</td><td class=\"results\">$tissue[0]</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Treatment</td><td class=\"results\">$treatment[0]</td>";
					$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Time Point</td><td class=\"results\">".$assocrow['duration']." $durationunits</td>";
$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Dosing Info</td><td class=\"results\">".$assocrow['dose']." $doserow[0]</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Vehicle</td><td class=\"results\">$vehicle[0]</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Route of Administration</td><td class=\"results\">".$route[0]."</td>";
				$tablerows .="</tr><tr>";
				
				
				$tablerows .= "<td class=\"questionparameter\">Submitter</td><td class=\"results\">".$assocrow['submitter']."</td>";
				$tablerows .= "</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Date Submitted</td><td class=\"results\">".$assocrow['datesubmitted']."</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">Additional Information</td><td class=\"results\">".$assocrow['info']."</td>";
				$tablerows .="</tr><tr>";
				$tablerows .= "<td class=\"questionparameter\">RNA Sample Queue Status</td><td class=\"results\">$queuestatus</td>";
				$tablerows .="</tr><tr>";
				
			$tablerows .= "</tr>\n";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/html4/strict.dtd">
<body>
<head>

<link rel="stylesheet" type="text/css" href="./css/tablelayout.css" title="layout" />

<title>EDGE User Administration</title>

</head>
<body>
<?php
	echo "$tablerows";
?>
</body>
</html>