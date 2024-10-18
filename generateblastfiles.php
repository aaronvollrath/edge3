<?php

// This will go through and create a FASTA file for the agilent 60-mer probes of a specified array....
require './phpinc/edge3_db_connect.inc';

// mouse = G4122F
// rat =G4131F
$arrayid = "G4122F";

//$countSQL = "SELECT FeatureNum, SystematicName, ProbeName, GenBankAcc, GeneSymbol, Sequence FROM agilent".$arrayid."_extendedannotations WHERE Sequence != \"\"";
$countSQL = "SELECT FeatureNum, GenBankAcc, GeneSymbol, Sequence FROM agilent".$arrayid."_extendedannotations WHERE Sequence != \"\"";
$countresult = mysql_query($countSQL, $db);

$featurecount = 0;
$breakcount = 0;
$filecount = 1;
//$numberoffeatures = $countrow[0];
while($countrow = mysql_fetch_row($countresult)){
	if($breakcount == 0/* || $breakcount == 1000*/){
		/*if($breakcount != 0){
			fclose($fd);
			$thisfile = $filecount - 1;
			$command = "./blast-2.2.18/bin/blastall -p blastn -d ./blast-2.2.18/data/mouse.rna.fna -i /var/www/html/edge2/IMAGES/".$arrayid."_file_".$thisfile." -o _".$thisfile."results.csv -m 8 -e .0000001 -I";
			echo "$command<br>";
			$str = exec($command);
			echo "$str<br>";
			echo "blasted file....<hr><br>";

		}*/
		$breakcount = 1;
		$file = "/var/www/html/edge2/IMAGES/".$arrayid."_file";
		$command = "touch $file";
		$fd = fopen($file, 'w');
		rewind($fd);
		$filecount++;
	}
	//$line = ">$countrow[0]|$countrow[1]|$countrow[2]|$countrow[3]|$countrow[4]\n";
	$line = ">$countrow[0]_$countrow[1]_$countrow[2]\n";
	fwrite($fd,$line);
	//
	$seqline = $countrow[3];
	$seqline .= "\n";
	fwrite($fd, $seqline);
	//fwrite($fd, $line);
	$breakcount++;
}
/*
$command = "./blast-2.2.18/bin/blastall -p blastn -d ./blast-2.2.18/data/mouse.rna.fna -i $file -o ".$arrayid."results.csv -m 8 -e .0000001 -I";
			echo "$command<br>";
			$str = exec($command);
			echo "$str<br>";
			echo "blasted file....<hr><br>";
			echo "<a href='".$arrayid."results.csv'>BLAST output</a><br>";
echo "DONE generating data";
*/

?>
