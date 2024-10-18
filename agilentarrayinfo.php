<?php

session_start();
require 'edge_db_connect2.php';
//require './phpinc/edge3_db_connect.inc';


include 'edge3header.inc';
require("globalfilelocations.inc");

include 'utilityfunctions.inc';
if(!isset($_SESSION['userid'])){
	die("You need to login to use this function.");
}
?>
<body onLoad="fixwindow(800,800)">
	<div class="header">

		<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression^3</font></h4>
</div>
<br>
<br>
<br>
<?php
// Parse out feature and arrayid...

$arrayid=$_GET['arrayid'];


$sql = "SELECT e.arraydesc, a.organism, a.arraydesc, a.version, e.cy3rnasample, e.cy5rnasample, e.ownerid, e.FE_data_file, e.dateprocessed  FROM agilentarrays AS a, agilent_arrayinfo AS e WHERE e.arrayid = $arrayid and a.id = e.arraytype";

//echo "$sql<br>";
$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);
$row = $sqlResult->FetchRow();//mysql_fetch_row($sqlResult);

$expdesc = $row[0];
$organism = $row[1];
$arraydesc = $row[2];
$version = $row[3];
$cy3rnasample = $row[4];
$cy5rnasample = $row[5];
$ownerid = $row[6];
$fedatafile = $row[7];
$dateprocessed = $row[8];



?>

<table id="results">
<tr><td>Array ID#</td><td><?php echo $arrayid; ?></td></tr>
<tr><td>Array Name</td><td><?php echo $expdesc; ?></td></tr>
<tr><td>Array Type</td><td><?php echo $arraydesc; ?></td></tr>
<tr><td>Organism</td><td><?php echo $organism; ?></td></tr>
<tr><td>Version</td><td><?php echo $version; ?></td></tr>
<?php
if($cy3rnasample != ""){
$sql = "SELECT samplename FROM agilent_rnasample WHERE sampleid = $cy3rnasample";
$cy3result = $db->Execute($sql);
$cy3row=$cy3result->FetchRow();
$cy3name = $cy3row[0];
$cy3name = "<a href='rnasampleinfo.php?sampleid=$cy3rnasample'>$cy3name</a>";
}else{
	$cy3name = "Currently no RNA sample is associated with this array.";
}
if($cy5rnasample != ""){
$sql = "SELECT samplename FROM agilent_rnasample WHERE sampleid = $cy5rnasample";
$cy5result = $db->Execute($sql);
$cy5row=$cy5result->FetchRow();
$cy5name = $cy5row[0];
$cy5name = "<a href='rnasampleinfo.php?sampleid=$cy5rnasample'>$cy5name</a>";
}else{
	$cy5name = "Currently no RNA sample is associated with this array.";
}


?>

<tr><td><font color="green"><strong>Cy3 RNA sample</strong></font></td><td><?php echo "$cy3name"; ?></td></tr>
<tr><td><font color="red"><strong>Cy5 RNA sample</strong></font></td><td><?php echo "$cy5name"; ?></td></tr>
<?php

	$sql = "SELECT firstname, lastname FROM users WHERE id = $ownerid";
	$ownerresult = $db->Execute($sql);
	$name = "";
	if($ownerresult){
		$row = $ownerresult->FetchRow();
		$firstname = $row[0];
		$lastname = $row[1];
		$name = $firstname." ".$lastname;
	}
	


?>


<tr><td>Array Owner</td><td><?php echo $name; ?></td></tr>
<tr><td>Feature Extraction Data File</td><td><?php echo returndatafile($fedatafile,$datafilelocation,$edgedata,TRUE,1); ?></td></tr>
<tr><td>Feature Extraction Quality Control Info:</td><td><?php echo returndatafile($fedatafile,$datafilelocation,$edgedata,TRUE, 2); ?></td></tr>
<tr><td>JPG Image of Array:</td><td><?php echo returndatafile($fedatafile,$datafilelocation,$edgedata,TRUE, 3); ?></td></tr>
<tr><td>Processed Date</td><td><?php echo $dateprocessed; ?></td></tr>
</table>



</body>
