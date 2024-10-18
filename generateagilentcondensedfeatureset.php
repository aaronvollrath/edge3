<?php
include 'utilityfunctions.inc';
// This script will be used to condense the agilent data...
require './phpinc/edge3_db_connect.inc';

/*$sql = "SELECT DISTINCT GeneName
FROM agilentannotations ORDER BY FeatureNum";

$sqlResult = mysql_query($sql, $db);

$genenamearray = array();

while($row = mysql_fetch_row($sqlResult)){
	array_push($genenamearray, $row[0]);
}
$file = "/var/www/html/edge2/IMAGES/agilentcondensingfile.csv";
	$command = "touch $file";
	$str=exec($command);
$fd = fopen($file, 'w');

	rewind($fd);
$count = 0;
foreach($genenamearray as $name){
	// Get the FeatureNum vals associated w/ that geneName
	$sql = "SELECT FeatureNum FROM agilentannotations WHERE GeneName LIKE \"$name\" ORDER BY FeatureNum";
	$sqlResult = mysql_query($sql,$db);
	$first = 0;

	while($row = mysql_fetch_row($sqlResult)){
		if($first == 0){
			fwrite($fd, $row[0]);

		}
		else{
			$out = "\t$row[0]";
			fwrite($fd, $out);
		}
		$first++;
	}
	$count++;
	echo "$count : $name has $first features associated with it<br>";
	fwrite($fd, "\n");
}
fclose($fd);
/*
$subjects = array('physics', 'chem', 'math', 'bio', 'cs', 'drama', 'classics');
analyze($subjects);

array_splice($subjects, 2,1);
analyze($subjects);
*/

// For each arrayid in the agilentdata table, iterate through and create a condensed file!!!

/*
Steps:  1- Get MaxID from the table
		$sql="SELECT MAX(arrayid) FROM agilentdata"
	2- Loop through and create a condensed file for each array



*/

$sql = "SELECT MAX(arrayid) FROM agilentdata";
$sqlResult = mysql_query($sql, $db);
$row = mysql_fetch_row($sqlResult);
$sql = "SELECT MAX(arrayid) FROM agilentcondenseddata";
$sqlResult = mysql_query($sql, $db);
$maxcondensedid=mysql_fetch_row($sqlResult);
$maxcondensedid = $maxcondensedid[0] + 1;
$maxid = $row[0];
echo "Number of arrays: $maxid<BR>";
for($index = $maxcondensedid; $index <= $maxid; $index++){
	$arrayid = $index;
	$file = "/var/www/html/edge2/IMAGES/condensedagilent/agilentcondensedfile_array_$index.csv";
	echo $file."<br>";

	$command = "touch $file";
	$str=exec($command);
	$fd = fopen($file, 'w');

	rewind($fd);
$file = "/var/www/html/edge2/agilentcondensingfile.csv";

	if (!($fp = fopen($file, "r"))) {

		die("could not open XML input from file $file");
	}
	echo "<h3><font color=\"red\">$arrayid</font></h3>";
	while (!feof($fp)) {
		$buffer = fgets($fp, 4096);
		$buffer = trim($buffer);
		$vals = explode("\t",$buffer);
		if($vals[0] != ""){
			$condensedfeaturenum = $vals[0];
			$sum = 0.0;
			$count = 0;
			foreach($vals as $featurenum){
				//echo "$val<br>";
				$sql = "SELECT LogRatio FROM agilentdata WHERE FeatureNum = $featurenum and arrayid = $arrayid";
				$sqlResult = mysql_query($sql,$db);
				$row = mysql_fetch_row($sqlResult);
				$ratio = $row[0]; //pow(10, $row[0]);
				$sum += $ratio;
				//echo "$featurenum : $row[0]<br>";
				$count++;
			}
			//echo "Sum: $sum<br>";
			//echo "Count: $count<br>";
			$avg = $sum/$count;
			$line = "$arrayid\t$condensedfeaturenum\t$avg\n";
			fwrite($fd,$line);
			//echo "Avg. Value = $avg<br>";

		}
	}
	fclose($fd);
	echo "<hr>";

}


$startingID = $maxcondensedid;
$endingID = $maxid;
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


	echo "<hr>FINISHED!!!<BR>";
?>
