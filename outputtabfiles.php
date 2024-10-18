<?php
/*
This script will create tab-delimited files for all the arrays in the database using the uncondensed data.
1.  Get all the unique array ids in the uncondensed set.
2.  Get all the data in the hybrids table for each array id and output to a file.
3.  Compress all these files into a zip archive
4.  Display a link for the download of these files.
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


//2.  for each arrayid, update final ratio in condensedhybrids table by just transferring from hybrids table.
$sql = "SELECT DISTINCT arrayid FROM condensedhybrids ORDER BY arrayid";
	$result = mysql_query($sql, $db);
	$arrayidarray = array();
	while($row = mysql_fetch_row($result)){
		array_push($arrayidarray, $row[0]);
	}
$filecount = 0;
foreach($arrayidarray as $arrayid){
//while($filecount < 5){
	$filenumber = $arrayid; // $arrayidarray[$filecount];
	// Create the file for this arrayid.....
	$file = "/var/www/html/edge2/IMAGES/".$filenumber.".TAB";
	$command = "touch $file";
	//echo "$file<br>";
	$str=exec($command);
	$fd = fopen($file, 'w');
	//rewind($fd);



	$hybridSQL = "Select cloneid, spot1cy3, spot1cy5, spot2cy3, spot2cy5, spot3cy3, spot3cy5, spot4cy3, spot4cy5, spot5cy3, spot5cy5, spot6cy3, spot6cy5,
		revspot1cy3, revspot1cy5, revspot2cy3, revspot2cy5, revspot3cy3, revspot3cy5, revspot4cy3, revspot4cy5, revspot5cy3, revspot5cy5,
		revspot6cy3, revspot6cy5, trimmean, revtrimmean, finalratio from hybrids where arrayid = $arrayidarray[$filecount]";

		//echo $hybridSQL;
$hybridResult = mysql_query($hybridSQL, $db);
while(list($cloneid, $spot1cy3, $spot1cy5, $spot2cy3, $spot2cy5, $spot3cy3, $spot3cy5,  $spot4cy3, $spot4cy5, $spot5cy3, $spot5cy5, $spot6cy3, $spot6cy5,
		$revspot1cy3, $revspot1cy5, $revspot2cy3, $revspot2cy5, $revspot3cy3, $revspot3cy5,
		$revspot4cy3, $revspot4cy5, $revspot5cy3, $revspot5cy5, $revspot6cy3, $revspot6cy5, $trimmean, $revtrimmean, $finalratio)
		= mysql_fetch_array($hybridResult)){
		$line = "$arrayid\t$cloneid\t$spot1cy3\t$spot1cy5\t$spot2cy3\t$spot2cy5\t$spot3cy3\t$spot3cy5\t$spot4cy3\t$spot4cy5\t$spot5cy3\t$spot5cy5\t$spot6cy3\t$spot6cy5\t$revspot1cy3\t$revspot1cy5\t$revspot2cy3\t$revspot2cy5\t$revspot3cy3\t$revspot3cy5\t$revspot4cy3\t$revspot4cy5\t$revspot5cy3\t$revspot5cy5\t$revspot6cy3\t$revspot6cy5\t$trimmean\t$revtrimmean\t$finalratio\n";
		fwrite($fd, $line);


		}
echo "$arrayid...";
$filecount++;
}

echo "<br>$filecount<br>";


// need to zip these files....
$command = "zip -j /var/www/html/edge2/IMAGES/alltabfiles.zip /var/www/html/edge2/IMAGES/*.TAB";
$str=exec($command);
echo "<br>creating zip file.... <br>$command<br><a href=\"./IMAGES/alltabfiles.zip\">ALL TAB FILES</a><br>";
echo "done....<br>";
?>













