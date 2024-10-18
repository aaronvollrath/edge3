<?php
///session_start();

require 'edge_db_connect2.php';
//require 'edge3_db_connect.inc';


$genelistschecked = $_GET['lists'];

$genelistschecked = explode(",", $genelistschecked);

$list = "";
$first = 1;

if(count($genelistschecked) >= 1){
	foreach($genelistschecked as $alist){
	
		$sql = "SELECT featurenums FROM genelist WHERE listid = ?";
		$result = $db->Execute($sql, array($alist));//mysql_query($sql, $db);


		if($first == 1){
			$list .= $result->fields[0];
		}else{
			$list .= ",".$result->fields[0];
		}
		$first = -1;
	}
	$list = trim($list);
	//echo $list;
}

$genes = explode(",",$list);
$count = count($genes);
$newgenes = array();
$index = 0;
if(strpos($list," ") !== false){
	for($h = 0; $h < $count; $h++){
		$genes[$h] = trim($genes[$h]);
	}
}
for($i = 0; $i < $count; $i++){
	$duplicate = false;
	$testgene = $genes[$i];
	for($k = $i+1; $k < $count; $k++){
		if(strcmp($testgene,$genes[$k]) == 0){
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
	$list = "";
	$initial = 1;
	for($j = 0; $j < $newcount; $j++){
		if($initial == 1){
			$list .= $newgenes[$j];
			$initial = -1;
		}
		else{
			$list .= ",".$newgenes[$j];
		}
	}
}

echo $list;
?>