<?php
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	//die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}

require './phpinc/edge3_db_connect.inc';
include 'edge_update_user_activity.inc';
// GET TABLE FOR CTD....

$sql = "SELECT  DISTINCT ( c.chemid ), c.chemical
FROM array AS a, sampledata AS s, chem AS c
WHERE a.ownerid =1 AND c.chemid = s.chemid AND a.arrayid = s.sampleid order by chemid";

$result = mysql_query($sql, $db);

$file = "/var/www/html/edge2/IMAGES/ctdchemdata.csv";
	$command = "touch $file";
	$str=exec($command);
$fd = fopen($file, 'w');

while($row = mysql_fetch_row($result)){
	$chemid = $row[0];
	$chemname = $row[1];
	// how many public arrays are available for this particular chemid?
	$sql = "SELECT count( a.arrayid )
FROM array AS a, sampledata AS s
WHERE s.chemid = $chemid AND a.ownerid =1 AND a.arrayid = s.sampleid";
	$countresult = mysql_query($sql, $db);
	$countval = mysql_fetch_row($countresult);
	// what is the MeSH term associated w/ this chemid?
	$sql = "SELECT meshterm FROM chemmeshlookup WHERE chemid = $chemid";
	$meshresult = mysql_query($sql, $db);
	$meshval = mysql_fetch_row($meshresult);
	if($meshval[0] == ""){
		$meshval[0] = "No term in MeSH";
	}
	$string = "$chemid, $chemname, $countval[0], $meshval[0]\n";
	//echo "$chemid, $chemname, $countval[0], $meshval[0]<br>";
	fwrite($fd, $string);

}
echo "<hr>CSV data file: <a href=\"./IMAGES/ctdchemdata.csv\">ctddata.csv</a><br>";
$file = "/var/www/html/edge2/IMAGES/ctdrefseqdata.csv";
	$command = "touch $file";
	$str=exec($command);
$fd = fopen($file, 'w');
$sql = "SELECT DISTINCT (refseq) FROM condensedannotations WHERE refseq != \"NULL\" ORDER BY refseq";
$result = mysql_query($sql, $db);
while($row = mysql_fetch_row($result)){
	if($row[0] != ""){
		fwrite($fd, "$row[0]\n");
	}

}
echo "<hr>Refseq data file: <a href=\"./IMAGES/ctdrefseqdata.csv\">ctdrefseqdata.csv</a></br>";
?>
