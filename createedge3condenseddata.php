<?php
require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
include 'header.inc';
include 'utilityfunctions.inc';
require 'globalfilelocations.inc';
/*
	create condensed data from agilentdata table....





$sql = "SELECT DISTINCT arrayid FROM agilent_arrayinfo WHERE arraytype = 0 and arrayid >= 543 and arrayid <=549 ORDER BY arrayid ASC";
$result = mysql_query($sql, $db);

while($arow = mysql_fetch_row($result)){
		$arrayID = $arow[0];


		$countSQL = "SELECT COUNT(*) FROM agilentdata WHERE arrayid = $arrayID";
		$countresult = mysql_query($countSQL,$db);
		$countrow = mysql_fetch_row($countresult);
		if($countrow[0] == 0){
			continue;
		}
		// SETTING THINGS UP FOR CREATING THE CONDENSED DATA FILE....
		$file = "$IMAGESdir/condensedagilent/agilentcondensedfile_array_$arrayID.csv";
			//echo $file."<br>";
		
			$command = "touch $file";
			$str=exec($command);
			$fd = fopen($file, 'w');
		
			rewind($fd);
		$file = "/var/www/agilentcondensingfile.csv";
		
			if (!($fp = fopen($file, "r"))) {
		
				die("could not open XML input from file $file");
			}
			echo "<h3><font color=\"red\">$arrayID</font></h3>";
			while (!feof($fp)) {
				$buffer = fgets($fp, 4096);
				$buffer = trim($buffer);
				$vals = explode("\t",$buffer);
				if($vals[0] != ""){
					$condensedfeaturenum = $vals[0];
					$sum = 0.0;
					$count = 0;
					foreach($vals as $featurenum){
						//echo "$val<br>";
						$sql = "SELECT LogRatio FROM agilentdata WHERE FeatureNum = $featurenum and arrayid = $arrayID";
						
						$sqlResult = mysql_query($sql,$db);
						$row = mysql_fetch_row($sqlResult);
						$ratio = $row[0]; //pow(10, $row[0]);
						$sum += $ratio;
						//echo "$featurenum : $row[0]<br>";
						$count++;
					}
					//echo "Sum: $sum<br>";
					//echo "Count: $count<br>";
					$avg = $sum/$count;
					$line = "$arrayID\t$condensedfeaturenum\t$avg\n";
					fwrite($fd,$line);
					//echo "Avg. Value = $avg<br>";
		
				}
			}
			fclose($fd);
			echo "<hr>";
		
		$condensedfileroot = "$IMAGESdir/condensedagilent/";
		$filebasename = "agilentcondensedfile_array_";
		
		
			$thiscondensedfile = $condensedfileroot.$filebasename.$arrayID;
			$thiscondensedfile .= ".csv";
			//echo "$thiscondensedfile<br>";
			$sql = "LOAD DATA LOCAL INFILE \"$thiscondensedfile\" INTO TABLE agilentcondenseddata";
			echo $sql."<br>";
			$sqlResult = mysql_query($sql, $db);
			//echo "$sqlResult<br>";
			$errNum = mysql_errno($db);
			if($errNum){
							echo "<strong>Database Error inserting.  Error #$errNum: .  SQL: $insertSQL</strong><br>";
						}
		
		
		
				if($affectedrows==-1){
					$trxfiledata .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
					$trxfiledata .=  "<strong><font color=\"red\">".mysql_errno($db) . ": " . mysql_error($db). "</font></strong>\n";
					$trxfiledata .=  "<br>heres sql:  <br>";
					$trxfiledata .=  "$sql";
				}else{
		
					echo "<font color=\"green\"><strong>Data successfully entered into database!</strong></font><br><a href='agilentarray.php'>Click to enter another array</a>";
				}
		
					
}
*/
?>