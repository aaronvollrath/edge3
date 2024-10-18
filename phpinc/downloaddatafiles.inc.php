<?php

	/*  This page is used to download data files associated w/ an experiment..... */

	if(isset($_GET['expidlist'])){
		$expidlist = $_GET['expidlist'];
	}else{
		die("You may have reached this page by mistake....");
	}

	// Create the download form......


	if(!isset($_POST['submit'])){

		$yourExpSQL = "SELECT expid, expname, ownerid, descrip FROM agilent_experimentsdesc WHERE expid=$expidlist";
		$expresult = $db->Execute($yourExpSQL);
		if(!$expresult){
			die("your query returned no result...");
		}else{
			$row = $expresult->FetchRow();
			$expid = $row[0];
			$expname = $row[1];
			$ownerid = $row[2];
			$desc = $row[3];
			$ownerSQL = "SELECT firstname, lastname FROM users WHERE id = $ownerid";
			$ownerResult = $db->Execute($ownerSQL);
			$ownernamerow = $ownerResult->FetchRow();
			$ownername = $ownernamerow[0]." ".$ownernamerow[1];
		}
		echo "<br><br><div dojoType='dijit.TitlePane' title='$expname Details' open='false' style='width:600px'>$desc</div>";
		echo "<h3>Download Files from Experiment: <font color='red'>$expname</font></h3><em><font color='blue>This experiment is owned by:</font> <b>$ownername</b></em><br>";
		// Get the arrays associated w/ this expid
		$yourArraysCountSQL = "SELECT COUNT(arrayid) from agilent_experiments WHERE expid = $expid ORDER BY arrayid";
			//echo "$yourArraysCountSQL<br>";
			$yourArraysCountResult = $db->Execute($yourArraysCountSQL);//mysql_query($yourArraysCountSQL, $db);
			$arrayCount = $yourArraysCountResult->FetchRow();//mysql_fetch_row($yourArraysCountResult);
			$datafilecount = 0;
			if($arrayCount[0] > 0){
				
?>
				<form name="downloaddata" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
				<input type="hidden" name="expdesc" value="<?php echo $expname; ?>">
<?php
				$yourArraysSQL = "SELECT e.arrayid from agilent_experiments as e WHERE e.expid = $expid ORDER BY e.arrayid ASC";
				//echo "$yourArraysSQL<BR>";
				$yourArraysResult = $db->Execute($yourArraysSQL);//mysql_query($yourArraysSQL, $db);
			echo "<table>";
			
			while($yourArrays = $yourArraysResult->FetchRow()){
				$arrayid = $yourArrays[0];
				$arraydescSQL = "SELECT arraydesc FROM agilent_arrayinfo WHERE arrayid = $arrayid";
				$arraydescResult = $db->Execute($arraydescSQL);//mysql_query($arraydescSQL, $db);
				$arraydescVal = $arraydescResult->FetchRow();//mysql_fetch_row($arraydescResult);
				// Does this array have a data file associated w/ it????
				$sql = "SELECT FE_data_file FROM agilent_arrayinfo WHERE arrayid = $arrayid";
					$fileResult = $db->Execute($sql);
					if($fileResult){
						$row2 = $fileResult->FetchRow();
						$filename = $row2[0];
						$filePresent = "";
						if($filename != ""){
							$filePresent = returndatafile($filename,$datafilelocation,$edgedata,TRUE,1);
							if($filePresent != ""){
								echo "<tr><td align=\"center\"><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\" checked></td><td>$arraydescVal[0]&nbsp&nbsp&nbsp&nbsp</option></td></tr>";
								$datafilecount++;
							}else{
								echo "<tr><td align=\"center\">No Data File for $arrayid</td><td>$arraydescVal[0]&nbsp&nbsp&nbsp&nbsp</option></td></tr>";
							}
						}else{
							echo "<tr><td align=\"center\">No Data File for $arrayid</td><td>$arraydescVal[0]&nbsp&nbsp&nbsp&nbsp</option></td></tr>";
						}
						
					}else{
						die("Error in sql: $sql");
					}
				//$htmllist .= "<li><a href=\"./agilentexperiment-useradmin.php?arrayid=$yourArrays[0]\">$arraydescVal[0]</a></li>";
				
				//echo "$arraydescVal[0]<br>";
			}
			
			}else{
				
				echo "No arrays associated w/ this experiment at this time.<br>";

			}

			if($datafilecount > 0){
							echo "<tr>
					<td><input type=\"submit\" name=\"submit\" value=\"Submit for Download\"></td>
					<td><input type=\"reset\" value=\"Reset Array Form\"></td><td></td>
					</tr>";
			}else{
				echo "<tr><td colspan=2>No data files available for this experiment.  Please notify the EDGE<sup>3</sup> Administrator if you feel this is an error.</td></tr>";
			}
			echo "</table>";
			echo "</form>";


	}else{
		/*
			What boxes were checked?????
		*/
		if(isset($_POST['expdesc'])){
			$expname = $_POST['expdesc'];
		}else{
			$expname = "NoExpNameGiven";
		}
		
		$username = $_SESSION['firstname'].$_SESSION['lastname'];
		$timeinfo = date("DMjGisTY");
		$expname = str_replace(" ", "", $expname);
		$expname = str_replace("/","_", $expname);
		$expname = str_replace("\\","_", $expname);
		$keyfilename = $username."_".$expname."_".$timeinfo.".csv";
		chdir("/var/www/datafiles");
		$command = "touch $keyfilename";
		$str=exec($command);	
		$fd = fopen($keyfilename, 'w');
		fwrite($fd, "Array ID #, Array Description, Filename\n");
		$zipfiles = ""; # used to hold the name of the files....
		$arrayidSQL = "SELECT DISTINCT arrayid,arraydesc FROM agilent_arrayinfo ORDER BY arrayid";
		$arrayidResult = $db->Execute($arrayidSQL);//mysql_query($arrayidSQL, $db);
		$datafilecount = 0;
		while($row = $arrayidResult->FetchRow()){
		// Check to see which boxes were checked...
			$arrayid = $row[0];
			$arraydesc = $row[1];
			$thisVal = "array$arrayid";
			if(isset($_POST[$thisVal])){
				$post = $_POST[$thisVal];
				if($post > 0){
					$sql = "SELECT FE_data_file FROM agilent_arrayinfo WHERE arrayid = $arrayid";
					$fileResult = $db->Execute($sql);
					if($fileResult){
						$row2 = $fileResult->FetchRow();
						$filename = $row2[0];
						$filename = returndatafile($filename,$datafilelocation,$edgedata,FALSE,1);
						if($filename != ""){
							$zipfiles .= $filename." ";
							$datafilecount++;
						}
						
						
					}else{
						die("Error in sql: $sql");
					}
					$line = "$arrayid,$arraydesc,$filename\n";
					fwrite($fd,$line);
				}
			}
		}
					
		fflush($fd);
		fclose($fd);
		// Create a zip archive....file:///var/www/phpinc/downloaddatafiles.inc.php
		$zipfilenamebase = $username."_".$expname."_".$timeinfo.".zip";
		$zipfilename ="$IMAGESdir/$zipfilenamebase";
		$zipcommand = "zip -D $zipfilename";

		$zipcommand .= " ".$zipfiles." ".$keyfilename;
		
		
		
		$str=exec($zipcommand);	
		$url = ".$IMAGESreldir/$zipfilenamebase";
		// Need to delete the keyfile....
		$delkeyfilecommand = "rm $keyfilename";
		#$str=exec($delkeyfilecommand);
		//echo "<br>$zipcommand<br>";
		if($datafilecount > 0){
			$expname = "";
			if(isset($_GET['expidlist'])){
				$expidlist=$_GET['expidlist'];
				$yourExpSQL = "SELECT expid, expname FROM agilent_experimentsdesc WHERE expid=$expidlist";
				$expresult = $db->Execute($yourExpSQL);
				if(!$expresult){
					die("your query returned no result...");
				}else{
					$row = $expresult->FetchRow();
					$expid = $row[0];
					$expname = $row[1];
				}
			}
			
			echo "<br>Right click and choose save as to download the compressed data files for experiment <font color='red'><strong> $expname</strong></font> -> <a href='$url' target='_blank'>Compressed Data Files</a>";
		}else{
			echo "There were no data files returned for this experiment.  Please notify the EDGE<sup>3</sup> Administrator<br>";
		}
	}
?>
