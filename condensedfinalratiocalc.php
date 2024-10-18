<?php
/*
	Given an array id this file will compute the composite final ratios for the condensed dataset for this array id

	1.  Get the cloneids from the cloneidlookup table.
	2.  For each of the cloneids get the respective associds
	3.  For each associd, get its value from the hybrids table
	4.  Perform the necessary calculations for this cloneid and assign the condensed final ratio
*/

require 'edge_db_connect2.php';
include 'edge_update_user_activity.inc';
include 'header.inc';
$thiscloneid = $_GET['cloneid'];
require './phpinc/edge3_db_connect.inc';



?>



<body onLoad="fixwindow(800,800)">
	<div class="header">
		<img src="./GIFs/EDGE264x30.png" alt="Edge^2" align="left"></img>
		<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression</font></h2>
</div>
<div class="updatecontent">
<br>
<br>

<?php
	//1.  Get the cloneids from the cloneidlookup table.
	$idlookuparray = array();
	$sql = "SELECT DISTINCT cloneid FROM condensedidlookup WHERE cloneid ORDER BY cloneid";
	$result = mysql_query($sql, $db);
	while($row = mysql_fetch_row($result)){
		array_push($idlookuparray, $row[0]);
	}

	foreach($idlookuparray as $cloneid){
		// 2.  For each of the cloneids get the respective associds
		//$associdarray = array();
		$sql = "SELECT associd FROM condensedidlookup WHERE cloneid = $cloneid ORDER BY associd";
		$result = mysql_query($sql, $db);
		$finalratiototal = 0;
		$numids = 0;
		while($row = mysql_fetch_row($result)){

				$thisid = $row[0];
				// Get the final ratio for this particular clone.....
				$sql = "SELECT finalratio FROM hybrids WHERE arrayid = $arrayid AND cloneid = $thisid";
				$cloneResult = mysql_query($sql, $db);
				$ratio = mysql_fetch_row($cloneResult);
				$finrat = $ratio[0];
				$ratio = $finrat;
				if($finrat >= 1){
					$finrat = log($finrat);
				}
				else{
					$finrat = log(-1/$finrat);
				}
				//echo "finrat = $finrat<br>";
				//array_push($finalratioarray, $finrat);
				$finalratiototal += $finrat;
				$numids++;
		}
		// add one to account for the cloneid being added....
		$mean = $finalratiototal/($numids);


		if($mean < 0){
			$mean = round(-1 * exp(-1 * $mean),6);
		}
		else{
			$mean = round(exp($mean),6);
		}
		$sql = "SELECT finalratio FROM hybrids WHERE arrayid = $arrayid AND cloneid = $thisid";
				$cloneResult = mysql_query($sql, $db);
				$row = mysql_fetch_row($cloneResult);
				$thisclonevalue = $row[0];
		$sql = "SELECT finalratio FROM condensedhybrids WHERE arrayid = $arrayid AND cloneid = $cloneid";
		$result = mysql_query($sql,$db);
		$row = mysql_fetch_row($result);
		$oldfinalratio = $row[0];
		//echo "<table><tr><td>arrayid</td><td>cloneid</td><td>final ratio total</td><td># ids</td><td>old final ratio</td><td>mean</td><td>clone value</td></tr><tr><td>$arrayid</td><td>$cloneid</td><td>$finalratiototal</td><td>$numids</td><td>$oldfinalratio</td><td>$mean</td><td>$thisclonevalue</td></tr></table>";

		// NOW NEED TO UPDATE CONDENSED FINALRATIO FOR THIS ARRAY ID for $thiscloneid...
		$sql = "INSERT INTO condensedhybrids (arrayid, cloneid, finalratio) VALUES($arrayid, $cloneid, $thisclonevalue)";
		//echo "$sql<hr>";
		$result = mysql_query($sql, $db);


	}



echo "Done updating arrayid $arrayid ... <br>";





?>