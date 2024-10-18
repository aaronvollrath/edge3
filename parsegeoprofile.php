<?php
/*  this file will take in a csv file that contains a list of clone numbers and return the PRC of each...

*/
require 'edge_db_connect2.php';
include 'header.inc';
require './phpinc/edge3_db_connect.inc';

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


	$GPLNUM = "GPL1717";
	$file = "/var/www/html/edge2/GeoProfiles/$GPLNUM"."_family.soft";
	$fd = fopen($file, 'r') or die("invalid file...<br>");;
	$file = "/var/www/html/edge2/GeoProfiles/$GPLNUM".".csv";
	$fd2 = fopen($file,'w') or die("invalid file...<br>");
	$cloneid = 1;
	$count = 0;
	$readtable = -1;
	$readsample = -1;
	while(!feof($fd)) {
		$line = fgets($fd);
		$line = trim($line);
		$valsarray = explode("\t", $line);
		//analyze($valsarray);
		if($valsarray[0] == "!platform_table_end"){
		$readtable = -1;
		echo "EXITING...<br>";
			//break;
		}
		if($readtable == -2){
			$readtable == 1;
		}
		if($valsarray[0] == "!platform_table_begin"){
			echo "<hr>ENTERING...";
			//$nextline = fgets($fd);
			$readtable = -2;
		}
		if(substr($valsarray[0], 0,9) == "^SAMPLE ="){
			$sample = substr($valsarray[0],8,strlen($valsarray[0]));
			echo "<hr>sample... $sample<br>";

		}
		if($valsarray[0] == "!sample_table_end"){
			$readsample = -1;
			//echo "</table>";
		}
		if($readsample == 1){
			//echo "<tr><td>$valsarray[0]</td><td>$valsarray[1]</td></tr>";
			//echo "readsample = 1<br>";
			//$outstring = "$valsarray[0]\t$valsarray[1]\n";
		}
		if($valsarray[0] == "!sample_table_begin"){
			$readsample = 1;
			//echo "<table><tr><td>ID</td><td>Value</td></tr>";
		}


		if($readtable == 1){
			$outstring = "$valsarray[0]\t$valsarray[1]\t$valsarray[2]\n";
			$accession = $valsarray[3];
			//echo "$outstring<br>";
			if($accession !=""){
				$sql = "SELECT COUNT(*) FROM annotations WHERE refseq LIKE '%$accession%'";
				//echo "$sql<br>";
				$result = mysql_query($sql, $db);
				$countval = mysql_fetch_row($result);
				if($countval[0] != 0){
					$count++;
					//echo "clone: $cloneid with accession = $accession and gene $valsarray[4] is in edge $countval[0] times<br>";
					//echo "here are the cloneids that correspond:<br>";
					$sql = "SELECT cloneid,annname FROM annotations WHERE refseq LIKE '%$accession%'";
					//echo "$sql<br>";
					$result = mysql_query($sql, $db);
					while($row=mysql_fetch_row($result)){
						//echo "          $row[0]:$row[1]<br>";
					}
			}
			}
			fwrite($fd2, $outstring);
			/*if($cloneid > 1000){
				break;
			}*/
			$cloneid++;
		}
	}
	echo "There were $count genes on this microarray platform that are in EDGE<br>";

?>