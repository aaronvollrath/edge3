<?php

/*  Description:  This file will output data from a particular arrayid into csv or tab-delimited files... */


require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../EDGE/default.php">Click here to go back to the main page</a>');
}

$format = $_GET['format'];
$sql = $_GET['sql'];
$versionid = $_GET['versionid'];
$arrayid = $_GET['arrayid'];
require './phpinc/edge3_db_connect.inc';

//echo "heres sql: $sql";

$cloneResult = mysql_query($sql, $db);

if($format == "csv"){
	// display in csv format....
	while(list($cloneid, $finalratio)= mysql_fetch_array($cloneResult))
	{


	$infoSQL = "Select cloneid, 5refseq, 5accession from cloneinfo where versionid = $versionid
		and cloneid = $cloneid";
	$infoResult = mysql_query($infoSQL, $db);
	$row = mysql_fetch_row($infoResult);
	$cloneid2 = $row[0];
	$refseq = $row[1];
	$accession = $row[2];
	echo "$cloneid,To Be Implemented,$refseq,$accession,$finalratio\n";
	}
}
else{ // format is tabbed delimited....
// display in tabbed format....
	while(list($cloneid, $finalratio)= mysql_fetch_array($cloneResult))
	{


	$infoSQL = "Select cloneid, 5refseq, 5accession from cloneinfo where versionid = $versionid
		and cloneid = $cloneid";
	$infoResult = mysql_query($infoSQL, $db);
	$row = mysql_fetch_row($infoResult);
	$cloneid2 = $row[0];
	$refseq = $row[1];
	$accession = $row[2];
	echo "$cloneid\tTo Be Implemented\t$refseq\t$accession\t$finalratio\n";
	}
}
?>
