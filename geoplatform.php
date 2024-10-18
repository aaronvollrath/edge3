<?php
require 'edge_db_connect2.php';
include 'edge_update_user_activity.inc';
include 'uncondensedcloneclass.inc';
include 'condensedcloneclass.inc';
include 'treatmentclass.inc';
// Need to check if the user is logged in because this is a restricted area...
/*
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../EDGE/default.php">Click here to go back to the main page</a>');
}*/
require './phpinc/edge3_db_connect.inc';

include 'header.inc';

$sql = "SELECT cloneid, annname, refseq, gi FROM annotations ORDER BY cloneid ASC";
$result = mysql_query($sql, $db);
$file = "/var/www/html/edge2/IMAGES/geoplatform.csv";
$command = "touch $file";
$str=exec($command);
$fd = fopen($file, 'w');
rewind($fd);
$string = "ID\tGENE_NAME\tGB_ACC\tGI\n";
fwrite($fd,$string);
while($cloneinfo = mysql_fetch_row($result)){
	$cloneid = trim($cloneinfo[0]);
	$name = trim($cloneinfo[1]);
	$refseq = trim($cloneinfo[2]);
	$gi = trim($cloneinfo[3]);
	$string = "$cloneid\t$name\t$refseq\t$gi\n";
	fwrite($fd, $string);
}
echo "done creating file....<br>";

?>
