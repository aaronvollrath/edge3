<?php

/***
Location: /edge2
Description: This file is used to display the clone information for the specified clone from an svg image.
	It looks up the information based upon the 'cloneid'.
POST: none
GET: 'cloneid', 'versionid'
	Notes:  The cloneid and versionid are passed in via GET array.  The versionid is not used, but is in place
		for possible future use
Files include or required: 'edge_db_connect.php','header.inc','formcheck.inc', 'cloneinfotable.inc'
***/


require 'edge_db_connect2.php';
include 'uncondensedcloneclass.inc';
include 'condensedcloneclass.inc';
include 'header.inc';
// Connect to the edge and mygo databases...
require './phpinc/edge3_db_connect.inc';
$db2 = mysql_connect("localhost", "root", "arod678cbc3",TRUE);
mysql_select_db("mygo", $db2);

$randnum = rand(0, 25000);
?>
<div class="header">
		<img src="./GIFs/EDGE264x30.png" alt="Edge^2" align="left"></img>
		<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression</font></h4>
	</div>
	<br>
	<br>
<?php
if($cloneid > 0){
$uclone = new UncondensedClone($cloneid,$db,$db2);
$tableclass = 'questionclass';
$tdclassparameter = "questionparameter";
$tdclassresult = "questionanswertext";
$width = 600;
$uclone->displayTable($tableclass, $tdclassparameter, $tdclassresult, $width);
$uclone->displayGOinfo();
$uclone->displaySeq();
$uclone->displayPRC();
}else{
$tableclass = 'questionclass';
$tdclassparameter = "questionparameter";
$tdclassresult = "questionanswertext";
$width = 300;
	//echo "condensedclone<br>";
	$cloneid = $cloneid * -1;
	$cclone = new CondensedClone($cloneid, $db, $db2);
	$cclone->displayInfo($tableclass, $tdclassparameter, $tdclassresult, $width);
	$cclone->displayAssClones($db);
}
?>
</body>
</html>