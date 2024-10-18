<?php
/*
This script allows you to add a condensed cloneid that is not in the condensedidlookup table.
1.  Check to see if this cloneid is in the condensedidlookup table.  if it is, stop...
2.  for each arrayid, update final ratio in condensedhybrids table by just transferring from hybrids table.
3.  update condensedannotations file.
4.  update condensedidlookup table.  cloneid/associd
5.  update the condensing file!
*/


require 'edge_db_connect2.php';
include 'edge_update_user_activity.inc';
include 'header.inc';
$thiscloneid = $_GET['cloneid'];
require './phpinc/edge3_db_connect.inc';

function updatemappingfile() {
$db = mysql_connect("localhost", "root", "arod678cbc3");
mysql_select_db("edge", $db);
// Get the unique list of cloneids (reference ids) from the condensedidlookup table

$sql = "SELECT DISTINCT(cloneid) FROM condensedidlookup ORDER BY cloneid";
//echo "<br>$sql<br>";
$result = mysql_query($sql, $db);

$fd = fopen("/var/www/html/edge2/condensed/condensing_file", "w");


// For each of the ids we need to look up the associated ids in the table...
while($row = mysql_fetch_row($result)){
	$refid = $row[0];
	// Query for the associds
	$countsql = "SELECT COUNT(*) FROM condensedidlookup WHERE cloneid = $refid";
	$countresult = mysql_query($countsql, $db);
	$count = mysql_fetch_row($countresult);
	fwrite($fd, "$refid\t");
	if($count[0] == 1){
		// Just print out the single clone...
		//echo "$refid  ";
	}
	else{
		// Print out the refid and corresponding associds...
		//echo "$refid  ";
		$sql = "SELECT associd FROM condensedidlookup WHERE cloneid = $refid ORDER BY associd";
		$associdresult = mysql_query($sql,$db);
		while($associd = mysql_fetch_row($associdresult)){
			if($associd[0] != $refid){
				//echo "$associd[0]   ";
				fwrite($fd, "$associd[0]\t");
			}
		}
	}
	//echo "<br>";
	fwrite($fd, "\n");
}


}


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

// 1.  is this clone in the condensedidlookup table?
$sql = "SELECT * from condensedidlookup where cloneid = $cloneid or associd = $cloneid";
//echo "<br>$sql<br>";
$result = mysql_query($sql,$db);
$row=mysql_fetch_row($result);
if($row[0] != ""){
	die("This cloneid is already in the condensedidlookup table!<br>No changes will be made!<br>");
}
	echo "Inserting the cloneid, $cloneid, into condensed tables....<br>";
//2.  for each arrayid, update final ratio in condensedhybrids table by just transferring from hybrids table.
$sql = "SELECT DISTINCT arrayid FROM condensedhybrids ORDER BY arrayid";
	$result = mysql_query($sql, $db);
	$arrayidarray = array();
	while($row = mysql_fetch_row($result)){
		array_push($arrayidarray, $row[0]);
	}

foreach($arrayidarray as $arrayid){
	$sql = "SELECT finalratio FROM hybrids WHERE arrayid = $arrayid AND cloneid = $cloneid";
	$result = mysql_query($sql,$db);
	// insert this finalratio into the condensedhybrids table....
	$row = mysql_fetch_row($result);
	$finalratio = $row[0];
	// NOW NEED TO UPDATE CONDENSED FINALRATIO FOR THIS ARRAY ID for cloneid...
		$sql = "INSERT INTO condensedhybrids (arrayid, cloneid, finalratio) VALUES($arrayid, $cloneid, $finalratio)";
		//echo "<br>$sql<br>";
		$result = mysql_query($sql,$db);


}
//3.  update condensedannotations file.
$sql = "SELECT annname, refseq FROM annotations WHERE cloneid = $cloneid";
$result = mysql_query($sql, $db);
$row = mysql_fetch_row($result);

//echo "<br>the name to update condensedannotations: $row[0] and refseq: $row[1]";

$sql = "INSERT INTO condensedannotations (cloneid, annname, refseq) VALUES ($cloneid, \"$row[0]\", \"$row[1]\")";
$result = mysql_query($sql, $db);
//echo "<br>$sql<br>";

//4.  update condensedidlookup table.  cloneid/associd

$sql = "INSERT INTO condensedidlookup(cloneid, associd) VALUES ($thiscloneid, $thiscloneid)";
//echo "<BR>$sql<BR><hr>";
$result = mysql_query($sql,$db);

//5.  update the condensing file!
//updatemappingfile();
echo "done....<br>";
?>













