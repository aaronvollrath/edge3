<?php
/*  this file will take an array and try to determine what clones are missing from the hybrids table for that arrayid.

*/


require 'edge_db_connect2.php';
include 'header.inc';
// update the condensedhybrids table...
require './phpinc/edge3_db_connect.inc';


	$cloneidarray = array();
	$sql = "SELECT DISTINCT cloneid FROM annotations ORDER BY cloneid";
	$result = mysql_query($sql, $db);

	while($row = mysql_fetch_row($result)){
		array_push($cloneidarray, $row[0]);
	}

       // need to check to see what clones are missing for the array passed thru url...
       foreach($cloneidarray as $cloneid){
		$sql = "SELECT finalratio FROM hybrids WHERE cloneid = $cloneid AND arrayid = $arrayid";
		$result = mysql_query($sql,$db);
		$row = mysql_fetch_row($result);
		if($row[0] == ""){
			echo "missing cloneid = $cloneid <br>$sql<br>";
		}
		//echo "cloneid = $cloneid  :  finalratio = $row[0]<br>";
       }


?>