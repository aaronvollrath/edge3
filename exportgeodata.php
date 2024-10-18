<?php
/***
Location: /edge2/admin
Description: We've been using this page to return a file that contains the final ratios for the
		chosen treatments (treatments are selected by manually editing the SQL below)
		Eg. $sql = "SELECT sampleid FROM sampledata WHERE chemid = 14";
		The above SQL query selects all treatments w/ chemid = 14 and creates an
		associated file.
POST: none
GET: none
Files include or required: 'edge_db_connect2.php'
***/
require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
?>

<?php
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
function utime (){
$time = explode( " ", microtime());
$usec = (double)$time[0];
$sec = (double)$time[1];
return $sec + $usec;
}
$start = utime();
?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="../css/newlayout.css" title="layout" />
<title>EDGE^2</title>
</head>


<body>



<?php
$priv_level = $_SESSION['priv_level'];
if($priv_level != 99){
	echo "Sorry, you are not authorized to access this page.";
}
else{
	if($arrayid == ""){
		die("no array id...");
	}
	$filenum = $arrayid;//rand(0, 25000);
	$file = "/var/www/html/edge2/IMAGES/geodata$filenum.csv";
	$command = "touch $file";
	$str=exec($command);
	$fd = fopen($file, 'w');
	rewind($fd);
	$sql = "SELECT cloneid, finalratio FROM hybrids WHERE arrayid = $arrayid";
	$result = mysql_query($sql, $db);
	fwrite($fd, "ID_REF\tVALUE\n");
	$count = 0;
	while($row = mysql_fetch_row($result)){
		$string = "$row[0]\t$row[1]\n";
		fwrite($fd, $string);
		$count++;
	}
	echo "Done writing file...$count clones<br>";
	echo "<a href=\"./IMAGES/geodata$filenum.csv\">data file link</a>";

}
?>