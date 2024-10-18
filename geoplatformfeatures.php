<?php
	require 'edge_db_connect2.php';
	include 'header.inc';
?>
<div class="header">
		<img src="./GIFs/EDGE264x30.png" alt="Edge^2" align="left"></img>
		<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression</font></h2>
	</div>
	<br>
	<br>
<?php

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

	require './phpinc/edge3_db_connect.inc';
	$file = "/var/www/html/edge2/IMAGES/geoplatformfeatures.txt";
	$command = "touch $file";
	$str=exec($command);
	$fd = fopen($file, 'w');
	$sql = "SELECT featurenumber, prc FROM edgefeature2prc ORDER BY featurenumber ASC";
	fwrite($fd,"ID\tPRC\tCLONENUM\tGI\tGB_ACC\tGENE\n");
	$result = mysql_query($sql, $db);
	while($row = mysql_fetch_row($result)){
		// GET THE CLONE NAME PRINTED AT THAT PRC location....
		$sql = "SELECT cloneid from cloneinfo where prc = \"$row[1]\"";
		$result2 = mysql_query($sql, $db);
		$cloneid = mysql_fetch_row($result2);
		$sql = "SELECT annname, refseq, gi FROM annotations where cloneid = $cloneid[0]";
		$result3 = mysql_query($sql, $db);
		$anns = mysql_fetch_row($result3);
		$annname = trim($anns[0]);
		fwrite($fd, "$row[0]\t$row[1]\t$cloneid[0]\t$anns[2]\t$anns[1]\t$annname\n");
	}


	?>