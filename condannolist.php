<?php
include 'header.inc';
require './phpinc/edge3_db_connect.inc';
$csvfile = "/var/www/html/edge2/IMAGES/annotations.csv";
$command = "touch $csvfile";
$sh = exec($command);
$fd = fopen($csvfile, 'w');
$csvfile = "/var/www/html/edge2/IMAGES/condannotations.csv";
$command = "touch $csvfile";
$sh = exec($command);
$fd2 = fopen($csvfile, 'w');
$sql = "SELECT distinct(cloneid) FROM condensedidlookup order by cloneid ASC";
$result = mysql_query($sql, $db);
//echo "<table class=\"question\">";
echo "<table class=\"question\"><tr><td>CC#</td><td>UCC#</td><td>Ann. Name</td><td class=>Refseq</td></tr>";
while($row = mysql_fetch_row($result)){
	//echo "$row[0]<br>";
	$sql = "SELECT annname, refseq FROM annotations WHERE cloneid = $row[0]";
	$r3 = mysql_query($sql, $db);
	$ids1 = mysql_fetch_row($r3);
	$ann = substr($ids1[0],0,50);
	$ref1 = $ids1[1];
	echo "<tr><td>$row[0]</td><td></td><td>$ann</td><td class=\"questionparameter\">$ref1</td></tr>";
	$string = "$row[0],,$ann,$ref1\n";
	fwrite($fd, $string);
	$string2 = "$row[0], $ann, $ref1\n";
	fwrite($fd2, $string2);
	// Get the associated clones for this condensed clone
	$sql = "SELECT associd FROM condensedidlookup WHERE cloneid = $row[0] ORDER BY associd ASC";
	//echo "$sql<br>";
	$r = mysql_query($sql, $db);
	while($assclone = mysql_fetch_row($r)){
		// Get the annotation for this clone...
		$sql = "SELECT annname, refseq FROM annotations WHERE cloneid = $assclone[0]";
		$r2 = mysql_query($sql, $db);
		$ids = mysql_fetch_row($r2);
		$name = substr($ids[0],0,50);
		$ref = $ids[1];
		if($ref != $ref1){
			$bg = "class=\"questionwronganswer\"";
		}
		else{
			$bg = "class=\"questionparameter\"";
		}
		echo "<tr><td></td><td><a href=\"./probeseqs.php?startclone=$assclone[0]\" target=\"_blank\">$assclone[0]</a></td><td>$name</td><td $bg>$ref</td></tr>";
		$string = ",$assclone[0],$name,$ref\n";
		fwrite($fd,$string);
	}
}

echo "</table><br><br>";
echo "<a href=\"./IMAGES/annotations.csv\" target=\"_blank\">annotations.csv</a>";
?>
