<?php
require 'edge_db_connect2.php';

# 23APR2009 - this file was used to create a condensing file some time ago.  keeping it around for possible future use.
# at the moment, it is non-functional.


$db = mysql_connect("localhost", "root", "arod678cbc3");

mysql_select_db("edge", $db);

	// Read in the clones file line by line and insert the values into the cloneinfo table...
	$file = "/var/www/html/edge2/agilentcondensingfile.csv";
	// open the file/create the file descriptor
	$fd = fopen($file, 'r');
	// place the file pointer at the beginning of the file...
	rewind($fd);
	$count = 0;

	while (!feof($fd)) {
		$buffer = fgets($fd, 4096);
		$buffer = trim($buffer);
		$values = explode("\t",$buffer);
		if($values[0] == ""){
			continue;
		}

		$size = count($values);
		$condensedclonenum = $values[0];

		echo "cond clone $values[0]";
		echo "size of values array : $size<br>";
		for($i = 0; $i < $size; $i++){

			if($i == 0){
				$sql = "INSERT INTO agilentcondensedidlookup (cloneid, associd) VALUES ($condensedclonenum, $condensedclonenum)";
				echo "$condensedclonenum:$condensedclonenum<br>";

			}else{
				if(trim($values[$i]) != ""){
				$sql = "INSERT INTO agilentcondensedidlookup (cloneid, associd) VALUES
				 ($condensedclonenum, ".$values[$i].")";
				echo "$condensedclonenum:$values[$i]<br>";
				//echo "<br>";
				 }
			}
			//echo " $sql<br> ";

			$result = mysql_query($sql, $db);

		}
		echo "<hr>";
		$count++;
	}

echo "Done entering $count values into agilentcondensedidlookup<br>";

?>