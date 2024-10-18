<?php
function utime (){
$time = explode( " ", microtime());
$usec = (double)$time[0];
$sec = (double)$time[1];
return $sec + $usec;
}
$start = utime();

require('./phpinc/edge3_db_connect.inc');

$file = "nonsignaling.txt";
$command = "touch $file";
	
	$str=exec($command);
$fd = fopen($file,'w');
$featureNumArray = array();

$sql = "SELECT FeatureNum
FROM agilentdata
WHERE arrayid = 10 AND ControlType =0
ORDER BY FeatureNum ASC";
$result = mysql_query($sql, $db);

$count = 0;
while($row = mysql_fetch_row($result)){
	if($count == 11){
		break;
	}
	$avgSQL = "SELECT AVG(LogRatio) FROM agilentdata WHERE arrayversion = 0 AND FeatureNum = $row[0] AND arrayid < 1000";
	echo "$avgSQL<br>";
	$avgresult = mysql_query($avgSQL, $db);
	$avgval = mysql_fetch_row($avgresult);
	$featureNumArray[$row[0]][0] = $avgval[0];
	$count++;
}
$count = 0;
foreach($featureNumArray as $key => $value){
	if($count == 11){
		break;
	}
	echo "$key is $value[0]<br>";
	$line = "$key,$value[0]\n";
	fwrite($fd,$line);
	fflush($fd);
	$count++;
}
fclose($fd);

$end = utime(); $run = $end - $start;
	echo "<font size=\"1px\"><b>Query results returned in ";
	echo substr($run, 0, 5);
	echo " secs.</b></font>";
?>