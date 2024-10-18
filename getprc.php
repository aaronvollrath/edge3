<?php
/*  this file will take in a csv file that contains a list of clone numbers and return the PRC of each...

*/
require 'edge_db_connect2.php';
include 'header.inc';
require './phpinc/edge3_db_connect.inc';
$file = "drinkwaterclones.csv";
$fd = fopen($file, 'r');
$fileout = "drinkwatercloneprc.csv";
$command = "touch $fileout";
$str = exec($command);
$fout = fopen($fileout,'w');

  while (!feof($fd)) {
	$line = fgets($fd);
	$line = trim($line);
	//$line = strtoupper($line);
	if($line != ""){
	$sql = "SELECT prc FROM cloneinfo WHERE cloneid = $line";
	$result = mysql_query($sql, $db);
	$prc = mysql_fetch_row($result);
	fwrite($fout, "$line\t$prc[0]"."\n");
	echo "$line : $prc[0] <br>";
	}
  }

?>
