<?php
// Script used to find spots that potentially do not signal....

require 'edge_db_connect2.php';
include 'header.inc';
// update the condensedhybrids table...
require './phpinc/edge3_db_connect.inc';

	$sql = "SELECT DISTINCT cloneid FROM annotations ORDER BY cloneid";
	$result = mysql_query($sql, $db);
	$cloneidarray = array();
	while($row = mysql_fetch_row($result)){
		array_push($cloneidarray, $row[0]);
	}
	/*$sql = "SELECT count( DISTINCT arrayid ) FROM hybrids";
	$result = mysql_query($sql, $db);
	$numarrays = mysql_fetch_row($result);
	$numarrays = $numarrays[0];*/
//$count = 0;
foreach($cloneidarray as $cloneid){
	/*if($count == 100){
		break;
	}*/
	// Loop through and identify clones where there's not much signal across all arrays....
	$sql = "SELECT count(*) FROM hybrids WHERE cloneid = $cloneid AND (finalratio > 1.5 OR finalratio < -1.5    )";
	$result = mysql_query($sql, $db);
	$num = mysql_fetch_row($result);
	if($num[0] == 0){
		echo "$cloneid<br>";
	}
	//$count++;
}
$end = utime(); $run = $end - $start;

		echo "<font size=\"1px\"><b>query finished in ";
		echo substr($run, 0, 5);
		echo " secs.</b></font><br>";

?>