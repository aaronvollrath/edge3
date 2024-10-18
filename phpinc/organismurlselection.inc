<?php
// This allows for the correct selection of a url based on the organism the arrays are being selected for...

	if($thisorganism == 0){
		$featureurl = "./agilentfeatureinfo.php?featurenum=\n";
		$annotationtable = "agilentg4122f_extendedannotations";
	}elseif($thisorganism == 1){
		$featureurl = "./ratagilentfeatureinfo.php?featurenum=\n";
		$annotationtable = "agilentg4131f_extendedannotations";
	}elseif($thisorganism == 2){
		$featureurl = "./humanagilentfeatureinfo.php?featurenum=\n";
		$annotationtable = "agilentg4112f_extendedannotations";

	}elseif($thisorganism == 3){
		$featureurl = "./zebrafishagilentfeatureinfo.php?featurenum=\n";
		$annotationtable = "agilentg2519f_extendedannotations";
	}elseif($thisorganism == 4){
		$featureurl = "./bovineagilentfeatureinfo.php?featurenum=\n";
		$annotationtable = "agilent015354_extendedannotations";
	}
	elseif($thisorganism == 5){
		$featureurl = "./zebrafishV2agilentfeatureinfo.php?featurenum=\n";
		$annotationtable = "agilent019161_extendedannotations";
	}
	elseif($thisorganism == 6){
        $featureurl = "./khsvandebvagilentfeatureinfo.php?featurenum=\n";
        $annotationtable = "agilent024441_extendedannotations";
    	}

?>
