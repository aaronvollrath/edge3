<?php
require 'edge_db_connect2.php';
require 'edge3_db_connect.inc';
// Need to check to make sure the gene lists are all from the same organism....
$genelistschecked = $_GET['lists'];

$genelistschecked = explode(",", $genelistschecked);
$featurenums = "";
$currentorganism = -1;
$insert = 1;
foreach($genelistschecked as $alist){
	$organismcheckSQL = "SELECT arraytype,featurenums FROM genelist WHERE listid = $alist";
	$checkresult = mysql_query($organismcheckSQL, $db);
	$checkrow = mysql_fetch_row($checkresult);
	if($currentorganism == -1){
		$currentorganism = $checkrow[0];
		$featurenums .= $checkrow[1];
	}else{
		if($currentorganism != $checkrow[0]){

			//return an error message....
			echo "You've selected lists from different organisms. Please only select gene lists from the same organism.";
			$insert = -1;
		}
		$featurenums .= ",".$checkrow[1];
	}
}


$genes = explode(",",$featurenums);
$count = count($genes);
$newgenes = array();
$index = 0;
if(strpos($featurenums," ") !== false){
	for($h = 0; $h < $count; $h++){
		$genes[$h] = trim($genes[$h]);
	}
}
for($i = 0; $i < $count; $i++){
	$duplicate = false;
	$testgene = $genes[$i];
	for($k = $i+1; $k < $count; $k++){
		if(strcmp($testgene,$genes[$k]) == 0){
			//unset($genes[$i]);
			//$changed = true;
			$duplicate = true;
			break;
		}
	}
	if(!$duplicate){
		$newgenes[$index] = $testgene;
		$index++;
	}
}
$newcount = count($newgenes);
$changed = false;
if($newcount < $count){
	$changed = true;
}
if($changed){
	$featurenums = "";
	$initial = 1;
	//$featurenums .= current($genes);
	//while(next($genes)){
	//	$featurenums .= ",".current($genes);
	//}
	for($j = 0; $j < $newcount; $j++){
		if($initial == 1){
			$featurenums .= $newgenes[$j];
			$initial = -1;
		}
		else{
			$featurenums .= ",".$newgenes[$j];
		}
	}
	/*$sql = "UPDATE genelist SET featurenums = '$newgenelist' WHERER listid = '$listid'";
	$result = mysql_query($sql,$db);
	if (!$result){
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $sql;
		die($message);
	}*/
}


$list = "";
$first = 1;

if($insert != -1){
	$listidresult = mysql_query("SELECT MAX(listid) from genelist",$db);
	$nextlistid = mysql_result($listidresult, 0);
	if($nextlistid != null){ $nextlistid++; }
	else{ $nextlistid = 1; }
	$userid = $_SESSION['userid'];
	$newlistname = $_GET['newlistname'];
	$listispublic = $_GET['listispublic'];
	$newlistdesc = $_GET['newlistdesc'];
	
	$saveSQL = "INSERT INTO `edge`.`genelist` (
		`userid` ,
		`name` ,
		`listdesc`,
		`arraytype`,
		`featurenums` ,
		`public` ,
		`listid`
		)
		VALUES (
		'$userid', '$newlistname', '$newlistdesc', $currentorganism, '$featurenums', '$listispublic', '$nextlistid'
		)";
	//echo "<br>$saveSQL<br>";
	$saveResult = mysql_query($saveSQL, $db);
	if(!$saveResult){
		echo "An error occured and the new gene list was not saved:" . mysql_error();
	}else{
		echo "Your query has been saved.";
	}
}




?>