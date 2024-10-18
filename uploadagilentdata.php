<?php
require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
// Need to check if the user is logged in because this is a restricted area...
// Need to check if the user is logged in because this is a restricted area...
/*echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";*/
 function utime (){
$time = explode( " ", microtime());
$usec = (double)$time[0];
$sec = (double)$time[1];
return $sec + $usec;
}
$start = utime();

for($i = 9; $i < 20; $i++){

	$datafile = "/var/www/edge2/IMAGES/agilentdata/array".$i.".csv";

$arraytable = "agilentdata";
		$sql = "LOAD DATA LOCAL INFILE \"$datafile\" INTO TABLE $arraytable FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n'";
		echo "<br>agilentdata insertion SQL: $sql<br>";
		$insertDataResult = mysql_query($sql, $db);
		if($errNum){
					echo "<strong>Database Error inserting.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
				}

}
		$end = utime(); $run = $end - $start;

				echo "<br><font size=\"1px\"><b>Query results returned in ";
				echo substr($run, 0, 5);
				echo " secs.</b></font>";

?>