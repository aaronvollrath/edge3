<?php
/*
// All Verification in Process:
SELECT cloneid
FROM `annotations`
WHERE annname
LIKE "Verification%"

// All Verification in Process where there is no 5prime consensus:
SELECT cloneid
FROM `annotations`
WHERE annname
LIKE "Verification%" AND comments
LIKE "%5prime no consensus%"

// Chimeras
SELECT cloneid
FROM `annotations`
WHERE comments
LIKE "%chimera%" LIMIT 0 , 30
*/
$csvfile = "/var/www/html/edge2/IMAGES/newcondensedcloneidassocid.csv";
$command = "touch $csvfile";
$sh = exec($command);
$fd = fopen($csvfile, 'w');
$csvfile = "/var/www/html/edge2/IMAGES/newcondensedannotations.csv";
$command = "touch $csvfile";
$sh = exec($command);
//$fd2 = fopen($csvfile, 'w');



// for each clone, find clones that have the same id.
// Create an array that contains all the clone #'s.
// Start w/ first clone.  look for clones w/ same gene name or refseq
require './phpinc/edge3_db_connect.inc';

$clonelist = array();

$sql = "SELECT cloneid FROM annotations ORDER BY cloneid ASC";
$result = mysql_query($sql,$db);
while($id = mysql_fetch_row($result)){
	array_push($clonelist, $id[0]);
}

$matchidarray = array();
$count = 0;
foreach($clonelist as $cloneid){
	if(in_array($cloneid, $matchidarray)){
		//echo "<font color=\"red\">$cloneid is in the matchidarray...</font><br>";
		continue;
	}
	$sql = "SELECT cloneid, annname, locustag, refseq FROM annotations WHERE cloneid = $cloneid";
	$result = mysql_query($sql,$db);
	$row = mysql_fetch_row($result);
	//echo "<font color=\"blue\">$row[0],$row[1],$row[2], $row[3]</font><br>";
	$ann = $row[1];$loctag = $row[2];$ref = $row[3];
	// now go through the clonelist again to see if they've the same clone info...
	//foreach($clonelist as $subcloneid){
		$params = "";
		//echo "Annotated Name: $ann<br>";
		//if(substr($ann, 0, 7)=="Verifica"){
		//$val = substr($ann, 0, 3);
		//echo "$val<br>";
		if(substr($ann, 0, 3) == "Ver"|| substr($ann,0,3) == "ver"){
			//echo "<font color=\"red\">$cloneid is a VIP...</font><br>";
			continue;
		}
		if($loctag != ""){
			if($loctag != "-"){
				if(substr($loctag, 0,4) != "NULL"){
					$params = "OR locustag LIKE \"$loctag\" ";
				}
			}
		}
		if($ref != ""){
			if(substr($ref, 0,4) != "NULL"){
				$params .= "OR refseq LIKE \"$ref\" ";
			}
		}
		$ann = str_replace("\"", "", $ann);

		$sql = "SELECT cloneid FROM annotations WHERE annname LIKE \"$ann\" $params ORDER BY cloneid";
		//echo "$sql<br>";

		$result = mysql_query($sql,$db);
		if(!$result){
			echo "ERROR: $sql<br>";
		}
		while($row2 = mysql_fetch_row($result)){
			$matchid = $row2[0];
			array_push($matchidarray, $matchid);

			//echo "$sql<br>";
			//if($ref == "" && $loctag == "" && $ann != "mitochondrial"){
			//echo "$cloneid, $matchid, $ann, $ref, $loctag<br>";
			fwrite($fd, "$cloneid, $matchid\n");
			//$count++;
			//}
		}
	if($count == 100){
		//break;

	}

	//}
}
echo "NUMBER TO GO: $count<br>";


?>
