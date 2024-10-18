<?php


require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
?>
<body onLoad="fixwindow(800,800)">
	<div class="header">
		<img src="./GIFs/EDGE264x30.png" alt="Edge^2" align="left"></img>
		<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression</font></h4>
</div>
<br>
<br>
<br>
<?php
// Parse out experiment id

$expid=$_GET['expid'];


$sql = "SELECT *  FROM agilent_experimentsdesc WHERE expid = $expid";


$sqlResult = mysql_query($sql, $db);
$row = mysql_fetch_row($sqlResult);

$expid = $row[0];
$expname = $row[1];
$expdesc = $row[2];
$ownerid = $row[3];


?>
<p>
<b>Experiment ID#:</b><?php echo $expid; ?>
</p>
<p><b>Experiment Name:</b><?php echo $expname; ?></p>
<p><b>Experiment Description:</b><?php echo $expdesc; ?></p>
<p><b>Owner ID:</b><?php echo $ownerid; ?></p>




</body>
