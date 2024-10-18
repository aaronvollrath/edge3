<?php

require 'edge_db_connect2.php';
include 'header.inc';
require './phpinc/edge3_db_connect.inc';

//if(is_int($arrayid)){
	$file = "/var/www/html/edge2/IMAGES/geosample$arrayid.csv";
	$command = "touch $file";
	$str=exec($command);
	$fd = fopen($file, 'w');

	fwrite($fd, "ID_REF\tVALUE\n");
	$sql = "SELECT cloneid, finalratio FROM hybrids WHERE arrayid = $arrayid ORDER BY cloneid ASC";
	echo "$sql<br>";
	$cloneResult = mysql_query($sql, $db);
	while($row = mysql_fetch_row($cloneResult)){
		$cloneid = $row[0];
		$finrat = $row[1];

		if($finrat >= 1){
			$finrat = log($finrat);
		}
		else{
			$finrat = log(-1/$finrat);
		}
		$ratio = $finrat;

		fwrite($fd, "$cloneid\t$ratio\n");
	}

//}

?>