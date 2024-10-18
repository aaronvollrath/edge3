<?php
require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';

/// This file will load condensed agilent data files into edge


$startingID = 48;
$endingID = 204;
$condensedfileroot = "/var/www/html/edge2/IMAGES/condensedagilent/";
$filebasename = "agilentcondensedfile_array_";
for($i = $startingID; $i <= $endingID; $i++){

	$thiscondensedfile = $condensedfileroot.$filebasename.$i;
	$thiscondensedfile .= ".csv";
	echo "$thiscondensedfile<br>";
	$sql = "LOAD DATA LOCAL INFILE \"$thiscondensedfile\" INTO TABLE agilentcondenseddata";
	echo "$sql<br>";
	$sqlResult = mysql_query($sql, $db);
	echo "$sqlResult<br>";

}





?>