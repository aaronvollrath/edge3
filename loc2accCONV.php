<?php
/*  this file will take in the loc2acc.csv file and then enter into the edge table, loc2acc, the values in this file...
*/
require 'edge_db_connect2.php';
include 'header.inc';
// update the condensedhybrids table...
require './phpinc/edge3_db_connect.inc';

function analyze(&$array) {
   foreach($array as $key=>$value) {
       if(is_array($value)) {
           echo "<li>Array:<blockquote>";
             analyze($value);
           echo "</blockquote>";
         } elseif(is_object($value)) {
           echo "<li>Object:<blockquote>";
             analyze($value);
           echo "</blockquote>";
         } else {
             echo "<li>[" . $key . "] " . $value;
       }
   }
}

	$file = "/var/www/html/edge2/loc2acc.csv";
	$fd = fopen($file, 'r');
	$count = 0;
	//echo "<table>";
	while (!feof($fd)) {
		$line = fgets($fd);
		$line = trim($line);
		$valsarray = explode("\t", $line);
		//analyze($valsarray);
		$locid = trim($valsarray[0]);
		$accession = trim($valsarray[1]);
		$gi = trim($valsarray[2]);
		$input = 0;
		if($accession == "" || $accession == "none" || $gi == "" || $gi == "-" || $accession == "-"){
			$input = -1;
		}
		else{
		//$sql = "INSERT INTO loc2acc(locid, accession, gi) VALUES ($locid, \"$accession\", $gi)";
		//echo "$sql<br>";
		$sql_query = mysql_query("INSERT INTO loc2acc(locid, accession, gi) VALUES ($locid, \"$accession\", $gi)") or die (mysql_error());
		//echo "<tr><td>$locid</td><td>$accession</td><td>$gi</td></tr>";
		$count++;
		}

		/*if($count > 25){
			break;
		}*/
	}
	//echo "</table>";
	echo "Placed $count entries into loc2acc<br>";

?>