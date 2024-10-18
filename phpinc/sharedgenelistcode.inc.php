<?php
 /* 	Filename: sharedgenelistcode.inc.php
	Description:  Shared location of code used for importing/saving a gene list.

*/

//analyze($_POST);
//echo $savedvals['cloneList'];
if(isset($_GET['savedquery'])){
	$cloneList=$savedvals['cloneList'];

}else{
	$cloneList = "";
}
if(isset($_POST['importgenelist'])){
	$importgenelist = $_POST['importgenelist'];
}
if(isset($importgenelist)){
$genelistids = "";
$importedbyuserid = $_POST['importedbyuserid'];
$importedbypublic = $_POST['importedbypublic'];
$genelistcount = 0;
if($importedbyuserid == true){
	$genesbyidSQL = "SELECT listid FROM genelist WHERE userid='$userid'";
	$genesbyidResult = mysql_query($genesbyidSQL,$db);
	while($row = mysql_fetch_row($genesbyidResult)){
		$glid = $row[0];
		$testval = 'list'.$glid;
		$imported = $_POST[$testval];
		if($imported != "" && $genelistcount > 0){
			$genelistids .= ",".$glid;
			$genelistcount++;
		}
		else if($imported !="" && $genelistcount == 0){
			$genelistids .= $glid;
			$genelistcount++;
		}
	}
}
else if($importedbypublic == true){
	$genesbypubSQL = "SELECT listid FROM genelist WHERE public='1'";
	$genesbypubResult = mysql_query($genesbypubSQL,$db);
	while($row = mysql_fetch_row($genesbypubResult)){
		$glid = $row[0];
		$testval = 'list'.$glid;
		$imported = $_POST[$testval];
		if($imported !="" && $genelistcount > 0){
			$genelist .= ",".$glid;
			$genelistcount++;
		}
		else if($imported !="" && $genelistcount == 0){
			$genelist .= $glid;
			$genelistcount++;
		}
	}
}

$genelist = "";
$validlists = true;
if($genelistcount > 0){
	$listids = explode (",",$genelistids);
	$listtypeSQL = "SELECT arraytype FROM genelist WHERE listid='$listids[0]'";
	$listtypeResult = mysql_query($listtypeSQL,$db);
	$listtype;
	while($row = mysql_fetch_row($listtypeResult)){
		$listtype = $row[0];
	}
	for($i = 0; $listids[$i] != null; $i++){
		$getgenelistSQL = "SELECT featurenums, arraytype FROM genelist WHERE listid='$listids[$i]'";
		$getgenelistResult = mysql_query($getgenelistSQL,$db);
		while($genelistRow = mysql_fetch_row($getgenelistResult)){
			$genelistFeaturenums = $genelistRow[0];
			$genelistType = $genelistRow[1];
			if($genelistType == $listtype){
				if($genelistcount > 1){
					$genelist .= $genelistFeaturenums . ", ";
					$genelistcount--;
				}
				else if($genelistcount > 0){
					$genelist .= $genelistFeaturenums;
					$genelistcount--;
				}
			}
			else{
			echo "Please only select lists of the same organism.";
			$genelist = "";
			$validlists = false;
			break 2;
			}
		}
	}
}
if($importgenelist == "Save" && $validlists){
	$listidresult = mysql_query("SELECT MAX(listid) from genelist",$db);
	$nextlistid = mysql_result($listidresult, 0);
	if($nextlistid != null){ $nextlistid++; }
	else{ $nextlistid = 1; }
	$newlistname = $_POST['newlistname'];
	$listispublic = $_POST['listispublic'];
	
	$saveSQL = "INSERT INTO `edge`.`genelist` (
`userid` ,
`name` ,
`arraytype`,
`featurenums` ,
`public` ,
`listid`
)
VALUES (
'$userid', '$newlistname', $listtype, '$genelist', '$listispublic', '$nextlistid'
)";
	$saveResult = mysql_query($saveSQL, $db);
	if(!$saveResult){
		echo "An error occured and the new gene list was not saved:" . mysql_error();
	}
}
}


?>