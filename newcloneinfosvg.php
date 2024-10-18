<?php
/***
Location: /edge2
Description: This file is used to display the clone information for the specified clone from an svg image.
	It looks up the information based upon the 'arrayid' and 'cloneid' combination.
POST: none
GET: 'cloneid', 'versionid'
	Notes:  The cloneid and versionid are passed in via GET array.  The versionid is not used, but is in place
		for possible future use
Files include or required: 'edge_db_connect2.php', 'jpgraph.php', 'jpgraph_bar.php', 'jpgraph_line.php', 'header.inc',
		'formcheck.inc', 'cloneinfotable.inc'
***/

require 'edge_db_connect2.php';
include 'edge_update_user_activity.inc';
include 'uncondensedcloneclass.inc';
include 'condensedcloneclass.inc';
include 'treatmentclass.inc';
// Need to check if the user is logged in because this is a restricted area...
/*
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../EDGE/default.php">Click here to go back to the main page</a>');
}*/

require './phpinc/edge3_db_connect.inc';
$db2 = mysql_connect("localhost", "root", "arod678cbc3",TRUE);
mysql_select_db("mygo", $db2);
// Parse out clone arnd arrayids...
$clonearray = explode("_", $_GET['cloneid']);
$cloneid = $clonearray[0];
$versionid = $_GET['versionid'];
$arrayid = $clonearray[1];
// Need to check to see if the cloneid is NEGATIVE...
// If it is, then the queries are different....
$arrayinfo = "SELECT treatment, dose, duration from sampledata where sampleid = $arrayid";
//$prcarray = array();

$arrayinfoResult = mysql_query($arrayinfo, $db);
list($treatment, $dose, $duration) = mysql_fetch_array($arrayinfoResult);

include 'header.inc';
?>
<body onLoad="fixwindow(800,800)">
	<div class="header">
		<img src="./GIFs/EDGE264x30.png" alt="Edge^2" align="left"></img>
		<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression</font></h4>
</div>
<?php
$trx = new Treatment($arrayid, $db);
if($cloneid > 0){

	$uclone = new UncondensedClone($cloneid,$db,$db2);
	$tableclass = 'questionclass';
	$tdclassparameter = "questionparameter";
	$tdclassresult = "questionanswertext";
	$width = 600;
	echo "<br><br>";
?>
	<h3 class="contenthead">Detailed Treatment and Clone Information</h3>
<div class="updatecontent">
<table class="question">
<?php

?>
<tr align="top">
<td>
	<?php
		$trx->dispTreatmentInfo();
	?>
</td>
<td>
<?php
$uclone->displayTable($tableclass, $tdclassparameter, $tdclassresult, $width);
?>
</td>
</tr>
</table>
<?php


	$uclone->displaySignalData($arrayid,$db);
	$uclone->displayGOinfo();
	$uclone->displaySeq();
	$uclone->displayPRC();
	$uclone->displayProbeSeqLink();
}else{
	//echo "condensed clone w/ arrayid = $arrayid<br>";
	$tableclass = 'questionclass';
	$tdclassparameter = "questionparameter";
	$tdclassresult = "questionanswertext";
	$width = 300;
	//echo "condensedclone<br>";
	$cloneid = $cloneid * -1;
	$cclone = new CondensedClone($cloneid, $db, $db2);
	echo "<br><br>";
?>
		<h3 class="contenthead">Detailed Treatment and Clone Information</h3>
<div class="updatecontent">
<table class="question">
<?php

?>
<tr align="top">
<td>
	<?php
		$trx->dispTreatmentInfo();
	?>
</td>
<td>
<?php
$cclone->displayInfo($tableclass, $tdclassparameter, $tdclassresult, $width);
?>
</td>
</tr>
</table>
<?php

	$cclone->displayAssClones($db, $arrayid);
	$cclone->displayStatistics();
	$cclone->displaySeq($db);
	$cclone->displayPRC($db);

}
?>



 <div class="boxfooter"><p></p></div>

</body>
</html>
