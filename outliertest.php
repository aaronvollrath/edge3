<?php


//  Script: outliertest.php

//  This script takes in a agilent array barcode number and then deterimines for each FeatureNum any outliers that are present in the Cy5 channel (i.e., the labeled control

$db = mysql_connect("localhost", "root", "arod678cbc3");
mysql_select_db("edge", $db);
include 'utilityfunctions.inc';
require 'globalfilelocations.inc';

if(!isset($_POST['submit'])){
/*
$featVals = array();
$featVals[1] = 6.85;
$featVals[0] = 6.18;
$featVals[2] = 6.28;
$featVals[3] = 6.49;
$featVals[4] = 9.69;
dixonQoutliertest($featVals);

$sd = deviation($featVals);

$outliers = sdoutliertest($featVals, $sd, 1.0);

analyze($outliers);
if(count($outliers) > 0){
				foreach($outliers as $outlier){
					echo "<br>array, $arrayidarray[$outlier], is an outlier with value of $featVals[$outlier]<br>";
					$outliersfound++;
				}
			}
*/
?>
<form enctype="multipart/form-data" name="newagilentsample" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="submitted" value="true">
<table class="question" width="800">

<tr>
<td><strong>Microarray Barcode:</strong></td>
<td>
<input name="barcode" type="text">
</td>
<td>
Enter the last 5 digits of the barcode.
</td>
</tr>


</tr>
<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td><input type="reset" value="Reset Form"></td>
<td></td>
</tr>

</table>
</form>
<?php
}else{

	$barcode = $_POST['barcode'];

	$file = "$IMAGESdir/dixonoutliers$bardode.txt";
	
	$command = "touch $file";
	$str=exec($command);
	$fd = fopen($file, 'w');
	rewind($fd);

	// Get the arrayids and arraynames for the entered barcode
	$execOutlierTest = 1;
	$arrayidarray = array();
	$arraydescarray=array();
	$arrayinfosql = "SELECT arrayid, arraydesc, FE_data_file FROM agilent_arrayinfo WHERE FE_data_file LIKE '%$barcode%' ORDER BY arrayid";
	//echo "$arrayinfosql<br>";
	$result = mysql_query($arrayinfosql, $db);
	if(mysql_num_rows($result) == 4){
		
		while($row=mysql_fetch_row($result)){
			//echo "$row[0] $row[1] $row[2]<br>";
			array_push($arrayidarray, $row[0]);
			array_push($arraydescarray, $row[0]);
		}
		
	}else{
		echo "This slide has been utilized or entered into the database multiple times...or the number you entered is invalid";
		$execOutlierTest = -1;
	}

	$outliersfound = 0;
	$dixonoutliersfound = 0;
	if($execOutlierTest == 1){
		$arrayidsql = "";
		$firstor = 1;
		foreach($arrayidarray as $id){
			if($firstor == 1){
				$arrayidsql .= "arrayid = $id ";
				$firstor = -1;
			}else{
				$arrayidsql .= " OR arrayid = $id";
			}

		}
		// Get the FeatureNum and put in an array....use the first array is to get these vals...
		$sql = "SELECT FeatureNum from agilentdata WHERE arrayid = $arrayidarray[0] ORDER BY FeatureNum ASC";
		$fnresult = mysql_query($sql, $db);
		$count = 0;

		// Go through each FeatureNum across the four arrays and determine outliers....
		while($row = mysql_fetch_row($fnresult)){
			$fnum = $row[0];
			$featVals = array();
			foreach($arrayidarray as $id){
				$featsql = "SELECT rProcessedSignal FROM agilentdata WHERE arrayid = $id and FeatureNum = $fnum";
				$resultfeat = mysql_query($featsql, $db);
				$rsig = mysql_fetch_row($resultfeat);
				array_push($featVals,$rsig[0]);
				//echo "<br>$rsig[0]<br>";
				
			}
			//$mean = mean($featVals[0], $featVals[1], $featVals[2], $featVals[3]);
			//echo "<br>mean = $mean<br>";
			$sd = deviation($featVals);
			//echo "<br> s.d. = $sd<br>";
//($featarray, $sd, $num)
			$outliers = sdoutliertest($featVals, $sd, 2);
			if(count($outliers) > 0){
				foreach($outliers as $outlier){
					//echo "<br>array, $arrayidarray[$outlier], is an outlier at FeatureNum $fnum with value of $featVals[$outlier]<br>";
					$outliersfound++;
				}
			}
			fwrite($fd, "<table><tr><td>FeatureNum</td><td>Gene Symbol</td>");
			for($j = 0; $j < count($arrayidarray); $j++){
				fwrite($fd, "<td>$arrayidarray[$j] : $arraydescarray[$j]</td>");	
			}
			fwrite($fd, "</tr>");
			$dixonoutlier = dixonQoutliertest($featVals);
			if(count($dixonoutlier) > 0){
				foreach($dixonoutlier as $doutlier){
					echo "<br>array, $arrayidarray[$doutlier], is an outlier at FeatureNum $fnum with value of $featVals[$doutlier]<br>";
					$sql = "SELECT GeneName, rProcessedSignal FROM agilentdata WHERE FeatureNum = $fnum and $arrayidsql";
					//echo "<br>$sql<br>";
					//exit(0);
					$thisresult = mysql_query($sql, $db);
					$tablerow = "";
					$first = 1;
					while($row = mysql_fetch_row($thisresult)){
						if($first == 1){
							$tablerow .= "<tr><td>$fnum</td><td>$row[0]</td><td>$row[1]</td>";
							$first = -1;
						}else{
							$tablerow .= "<td>$row[1]</td>";
						}
					}
					$tablerow .= "</tr>";
					fwrite($fd, $tablerow);
					fflush($fd);
					$dixonoutliersfound++;
				}
			}
			$count++;
			//if($count >10000){
			//	exit(0);
			//}
		}
		$fwrite($fd, "</table>");
		
		
	}
	echo "<hr>s.d.-based: $outliersfound outliers found<br>";

	echo "<hr>dixon q-test: $dixonoutliersfound outliers found<br>";
}
?>