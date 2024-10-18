<?php


if(isset($_GET['arrayid'])){
$arrayid = $_GET['arrayid'];
}else{
	$arrayid = "";
}
if($arrayid != ""){
	/*$arraySQL = "SELECT arraydesc FROM agilent_arrayinfo WHERE arrayid = $arrayid";
	$result = $db->Execute($arraySQL);//mysql_query($arraySQL,$db);
	$row = $result->FetchRow();//mysql_fetch_row($result);
	$arrayname = $row[0];
	*/
	# boolean variable used to determine whether the user owns the array value passed into the script...
	$doesown = false;
	# does the current user have access to these arrays?
	# first do they own this array or own an experiment containing this array?
	$arraysql = "SELECT COUNT(*) FROM agilent_arrayinfo WHERE arrayid = $arrayid AND ownerid = $userid";
	$arrayresult = $db->Execute($arraysql);
	$arraycount = $arrayresult->FetchRow();
	$arraycount = $arraycount[0];
	#  if this is the owner or an edge admin then we've an owned array.
	if($arraycount == 1 || $privval==99){
		$doesown = true;
	}


	if($doesown == false){
		# do they have any experiments that may contain this array? 
		#(though if they don't own the array, but own the experiment that owns the array, this should not ever happen...
		$expsql = "SELECT expid FROM agilent_experiments WHERE arrayid = $arrayid";
		$expresult = $db->Execute($expsql);
		while($row = $expresult->FetchRow()){
			$expid = $row[0];
			# does this user own the exp that the array is associated w/?
			$ownsql = "SELECT COUNT(*) FROM agilent_experimentsdesc WHERE expid = $expid AND ownerid = $userid";
			$ownresult = $db->Execute($ownsql);
			$owncount = $ownresult->FetchRow();
			$owncount= $owncount[0];
			if($owncount > 0){
				$doesown = true;
			}
		}
 	}


	
	if($doesown == false){
		# second do they belong to any groups that have access to this array by way of experiments?
	
		$expidarray = array();
		$usergroupsql = "SELECT usergroupid FROM usergroupmembers WHERE userid = $userid";
		$usergroupresult = $db->Execute($usergroupsql);
		$usersgroupexps = array();
		if($usergroupresult->RecordCount() > 0){
			// What are the experiment/user group associations for this user?
			while($row = $usergroupresult->FetchRow()){
				$assocSQL = "SELECT expid FROM expusergroupassoc WHERE usergroupid = $row[0]";
				$assocResult = $db->Execute($assocSQL);
				while($exprow = $assocResult->FetchRow()){
					$expid = $exprow[0];
					$expsql = "SELECT COUNT(expid) FROM agilent_experiments WHERE arrayid = $arrayid AND expid = $expid";
					$expresult = $db->Execute($expsql);
					while($row = $expresult->FetchRow()){
						$expidcount = $row[0];
						if($expidcount > 0){
							$doesown = true;
							break;
						}
					}
					if($doesown == true){
						break;
					}
					
				}
				if($doesown == true){
						break;
				}
			}
		}
	}
	
if($doesown == false){

	die("You do not have access to this array!");
}



	if(isset($_POST['editsubmitcy3submit'])){
			# get the value of the selected rna sample for cy3 and assign it to this array....
	
			if(isset($_POST['cy3name'])){
				$cy3value = $_POST['cy3name'];
				$sql = "UPDATE agilent_arrayinfo SET cy3rnasample = $cy3value WHERE arrayid = $arrayid";
				$result = $db->Execute($sql);
			}
	}
	if(isset($_POST['editsubmitcy5submit'])){
		# get the value of the selected rna sample for cy3 and assign it to this array....

		if(isset($_POST['cy5name'])){
			$cy5value = $_POST['cy5name'];
			$sql = "UPDATE agilent_arrayinfo SET cy5rnasample = $cy5value WHERE arrayid = $arrayid";
			$result = $db->Execute($sql);
		}
	}


	if(isset($_POST['editsubmitcy5yield'])){
		if(isset($_POST['cy5yield'])){
			$cy5yield = $_POST['cy5yield'];
			$sql = "UPDATE agilent_arrayinfo SET cy5yield = $cy5yield WHERE arrayid = $arrayid";
			$result = $db->Execute($sql);

		}
	}
	if(isset($_POST['editsubmitcy3yield'])){
		if(isset($_POST['cy3yield'])){
			$cy3yield = $_POST['cy3yield'];
			$sql = "UPDATE agilent_arrayinfo SET cy3yield = $cy3yield WHERE arrayid = $arrayid";
			$result = $db->Execute($sql);

		}
	}

	if(isset($_POST['editsubmitcy5specificactivity'])){
		if(isset($_POST['cy5specificactivity'])){
			$cy5sa = $_POST['cy5specificactivity'];
			
			$sql = "UPDATE agilent_arrayinfo SET cy5specificactivity = $cy5sa WHERE arrayid = $arrayid";
			$result = $db->Execute($sql);
		}
	}

	if(isset($_POST['editsubmitcy3specificactivity'])){
		if(isset($_POST['cy3specificactivity'])){
			$cy3sa = $_POST['cy3specificactivity'];
			$sql = "UPDATE agilent_arrayinfo SET cy3specificactivity = $cy3sa WHERE arrayid = $arrayid";
			$result = $db->Execute($sql);

		}
	}


	$sql = "SELECT e.arraydesc, a.organism, a.arraydesc, a.version, e.cy3rnasample, e.cy5rnasample, e.ownerid, e.FE_data_file, e.dateprocessed,e.cy3yield, e.cy3specificactivity, e.cy5yield, e.cy5specificactivity FROM agilentarrays AS a, agilent_arrayinfo AS e WHERE e.arrayid = $arrayid and a.id = e.arraytype";

	//echo "$sql<br>";
	$sqlResult = $db->Execute($sql);
	$row = $sqlResult->FetchRow();
	
	$arrayname = $row[0];
	$organism = $row[1];
	$arraydesc = $row[2];
	$version = $row[3];
	$cy3rnasample = $row[4];
	$cy5rnasample = $row[5];
	$ownerid = $row[6];
	$fedatafile = $row[7];
	$dateprocessed = $row[8];
	$cy3yield = $row[9];
	$cy3specificactivity = $row[10];
	$cy5yield = $row[11];
	$cy5specificactivity = $row[12];
	//analyze($row);
	$arrayqueueSQL = "SELECT arrayqueueid FROM arrayqueue WHERE dataid = $arrayid";
	$qresult = $db->Execute($arrayqueueSQL);//mysql_query($arrayqueueSQL, $db);
	$qrow = $qresult->FetchRow();// mysql_fetch_row($qresult);
	$arrayqueueid = $qrow[0];
	/*$cy3rnasample = $qrow[1];
	$cy5rnasample = $qrow[2];
	*/

	if($cy3rnasample != ""){
		$cy3sql = "SELECT samplename FROM agilent_rnasample WHERE sampleid = $cy3rnasample";
		$cy3result = $db->Execute($cy3sql);//mysql_query($cy3sql, $db);
		if(!$cy3result){
			
			# need to get a list of the RNA samples the owner has....
			$sql = "SELECT sampleid, samplename FROM agilent_rnasample";
			$result = $db->Execute($sql);
			
			if($result){
				$cy3select = "<select name='cy3name' id='cy3name'>";
				$cy3select .= "<option label=\"\" value=\"-1\" >No RNA Sample Selected</option>  ";
				while($row= $result->FetchRow()){
					$cy3select .= "<option label='' value='$row[0]'>$row[1]</option>";
				}
				$cy3select .= "</select>";
				#$thispage = $_SERVER['PHP_SELF'];
				# only the owners or an admin can modify this array....
				if($userid == $ownerid || $privval == 99){
					$cy3rnasamplename= "<td>No cy3 rna sample associated with this array.</td><td><form name='updatecy3sample' method='post' action=''><table><tr><td>$cy3select</td><td><input type='submit' name='editsubmitcy3submit' value='Assign Cy3 Sample'></td></tr></table></form></td>";
				}else{
					$cy3rnasamplename = "<td>No cy3 rna sample associated with this array.</td>";
				}
			}else{
				$cy3rnasamplename = "<td>No cy3 rna sample associated with this array.</td>";
			}
		}else{
			$row = $cy3result->FetchRow();//mysql_fetch_row($cy3result);
			$cy3rnasamplename = "<td><a href='./rnasampleinfo.php?sampleid=$cy3rnasample' target='_blank'>$row[0]</a></td>";
		}
	}else{
		# need to get a list of the RNA samples the owner has....
		$sql = "SELECT sampleid, samplename FROM agilent_rnasample";
		$result = $db->Execute($sql);
		if($result){
			$cy3select = "<select name='cy3name' id='cy3name'>";
				$cy3select .= "<option label=\"\" value=\"-1\" >No RNA Sample Selected</option>  ";
				while($row= $result->FetchRow()){
					$cy3select .= "<option label='' value='$row[0]'>$row[1]</option>";
				}
				$cy3select .= "</select>";
			
			if($userid == $ownerid || $privval == 99){
					$cy3rnasamplename= "<td>No cy3 rna sample associated with this array.</td><td><form name='updatecy3sample' method='post' action=''><table><tr><td>$cy3select</td><td><input type='submit' name='editsubmitcy3submit' value='Assign Cy3 Sample'></td></tr></table></form></td>";
				}else{
					$cy3rnasamplename = "<td>No cy3 rna sample associated with this array.</td>";
				}
		}else{
			$cy3rnasamplename = "<td>No cy3 rna sample associated with this array.</td>";
		}
	}
	if($cy5rnasample != ""){
			$cy5sql = "SELECT samplename FROM agilent_rnasample WHERE sampleid = $cy5rnasample";
			$cy5result =  $db->Execute($cy5sql);//mysql_query($cy5sql, $db);
			if(!$cy5result){		
				# need to get a list of the RNA samples the owner has....
				$sql = "SELECT sampleid, samplename FROM agilent_rnasample";
				$result = $db->Execute($sql);
				
				if($result){
					$cy5select = "<select name='cy5name' id='cy5name'>";
					$cy5select .= "<option label=\"\" value=\"-1\" >No RNA Sample Selected</option>  ";
					while($row= $result->FetchRow()){
						$cy5select .= "<option label='' value='$row[0]'>$row[1]</option>";
					}
					$cy5select .= "</select>";
					$thispage = $_SERVER['PHP_SELF'];
					
					if($userid == $ownerid || $privval == 99){
					$cy5rnasamplename= "<td>No cy5 rna sample associated with this array.</td><td><form name='updatecy5sample' method='post' action='$thispage'><table><tr><td>$cy5select</td><td><input type='submit' name='editsubmitcy5submit' value='Assign Cy5 Sample'></td></tr></table></form></td> ";
					}else{
						$cy5rnasamplename = "<td>No cy5 rna sample associated with this array.</td>";
					}
				}else{ 
					$cy5rnasamplename = "<td>No cy5 rna sample associated with this array.</td>";
				}
			}else{
				$row = $cy5result->FetchRow();//mysql_fetch_row($cy5result);
				$cy5rnasamplename = "<td><a href='./rnasampleinfo.php?sampleid=$cy5rnasample' target='_blank'>$row[0]</a></td>";
			}
		}else{
			# need to get a list of the RNA samples the owner has....
				$sql = "SELECT sampleid, samplename FROM agilent_rnasample";
				$result = $db->Execute($sql);
				
				if($result){
					$cy5select = "<select name='cy5name' id='cy5name'>";
					$cy5select .= "<option label=\"\" value=\"-1\" >No RNA Sample Selected</option>  ";
					while($row= $result->FetchRow()){
						$cy5select .= "<option label='' value='$row[0]'>$row[1]</option>";
					}
					$cy5select .= "</select>";
					$thispage = $_SERVER['PHP_SELF'];
					if($userid == $ownerid || $privval == 99){
					$cy5rnasamplename= "<td>No cy5 rna sample associated with this array.</td><td><form name='updatecy5sample' method='post' action='$thispage'><table><tr><td>$cy5select</td><td><input type='submit' name='editsubmitcy5submit' value='Assign Cy5 Sample'></td></tr></table></form></td> ";
					}else{
						$cy5rnasamplename = "<td>No cy5 rna sample associated with this array.</td>";
					}
				}else{
					$cy5rnasamplename = "<td>No cy5 rna sample associated with this array.</td>";
				}
		}
	
		$name = "";
		$sql = "SELECT firstname, lastname FROM users WHERE id = $ownerid";
		$ownerresult = $db->Execute($sql);
		$name = "";
		if($ownerresult){
			$row = $ownerresult->FetchRow();
			$firstname = $row[0];
			$lastname = $row[1];
			$name = $firstname." ".$lastname;
		}
		
	
	
		
}else{
	die("ERROR: Invalid parameter passed to script!");
}

if(!isset($_POST['editsubmit'])){
?>
	<br>

	<h2>Array Information</h2>
	
		<input type="hidden" name="arrayid" value="<?php echo $arrayid; ?>">
		<input type="hidden" name="arrayqueueid" value="<?php echo $arrayqueueid; ?>">
		<table>
			<tr>
			<td><strong>EDGE<sup>3</sup> Array ID #</strong></td>
			<td><?php echo $arrayid; ?>
			</td>
			</tr>
			<tr>
			<td><strong>Array Name:</strong></td>
			<td><?php echo $arrayname; ?></td><td>
			<?php
			if($userid == $ownerid || $privval == 99){
			?>
			<form name="displayarray" method="post" action=""><input type="submit" name="editsubmit" value="Edit Array Name" ></form>
			<?php
			}
			?>
			</td>
			</tr>
			<tr>
			<td><strong>Organism:</strong></td>
			<td><?php echo $organism; ?></td>
			</tr>
			<tr>
			<tr>
			<td><strong>Array Version:</strong></td>
			<td><?php echo $version; ?></td>
			</tr>
			<tr>
			<td><strong>Array Owner:</strong></td>
			<td><?php echo $name; ?></td>
			</tr>
			
			<tr>
			<td><strong>Feature Extraction Data File:</strong></td>
			<td><?php echo returndatafile($fedatafile,$datafilelocation,$edgedata,TRUE, 1); ?></td>
			</tr>
			<tr>
			<td><strong>Feature Extraction Quality Control Info:</strong></td>
			<td><?php echo returndatafile($fedatafile,$datafilelocation,$edgedata,TRUE, 2); ?></td>
			</tr>
			<tr>
			<tr><td><strong>JPG Image of Array:</strong></td><td><?php echo returndatafile($fedatafile,$datafilelocation,$edgedata,TRUE, 3); ?></td></tr>
			<tr>
			<td><strong><font color="green">Cy3 RNA Sample:</font></strong></td>
			<?php echo $cy3rnasamplename; ?> 
			</tr>
			<tr>
			<td><strong><font color="green">Cy3 RNA Sample Yield:</font></strong></td>
			<td><?php echo $cy3yield; ?></td>
			<?php
			if($privval == 99){
			?>
				<td><form name='updatecy3yield' method='post' action='' onsubmit="return checkCy3Yield()"><table><tr><td><input name='cy3yield' type='text' value='0.0'></input></td><td><input type='submit' name='editsubmitcy3yield' value='Assign Cy3 Yield'></td></tr></table></form></td>
			<?php
			}
			?>
			</tr>
			<tr>
			<td><strong><font color="green">Cy3 RNA Sample Specific Activity:</font></strong></td>
			<td><?php echo $cy3specificactivity; ?></td>
			<?php
			if($privval == 99){
			?>
				<td><form name='updatecy3specificactivity' method='post' action='' onsubmit="return checkCy3SpecificActivity()"><table><tr><td><input name='cy3specificactivity' type='text' value='0.0'></input></td><td><input type='submit' name='editsubmitcy3specificactivity' value='Assign Cy3 Specific Activity'></td></tr></table></form></td>
			<?php
			}
			?>
			</tr>
			<tr>
			<td><strong><font color="red">Cy5 RNA Sample:</font></strong></td>
			<?php echo $cy5rnasamplename; ?>
			</tr>
			<tr>
			<td><strong><font color="red">Cy5 RNA Sample Yield:</font></strong></td>
			<td><?php echo $cy5yield; ?></td>
			<?php
			if($privval == 99){
			?>
				<td><form name='updatecy5yield' method='post' action='' onsubmit="return checkCy5Yield()"><table><tr><td><input name='cy5yield' type='text' value='0.0'></input></td><td><input type='submit' name='editsubmitcy5yield' value='Assign Cy5 Yield'></td></tr></table></form></td>
			<?php
			}
			?>
			</tr>
			<tr>
			<td><strong><font color="red">Cy5 RNA Sample Specific Activity:</font></strong></td>
			<td><?php echo $cy5specificactivity; ?></td>
			<?php
			if($privval == 99){
			?>
				<td><form name='updatecy5specificactivity' method='post' action='' onsubmit="return checkCy5SpecificActivity()"><table><tr><td><input name='cy5specificactivity' type='text' value='0.0'></input></td><td><input type='submit' name='editsubmitcy5specificactivity' value='Assign Cy5 Specific Activity'></td></tr></table></form></td>
			<?php
			}
			?>
			</tr>
			<tr>
			<td><strong>Date Processed:</strong></td>
			<td><?php echo $dateprocessed; ?></td>
			</tr>
		</table>
	
<?php
}else{
	if (!isset($_POST['submit'])){
?>
<br>
<br>
	<h3>Edit Array Name</h3>
	<form name="editarray"  method="post"  action="<?php  $_SERVER['PHP_SELF'] ?>">
		<input type="hidden" name="arrayid" value="<?php echo $arrayid; ?>">
		<input type="hidden" name="arrayqueueid" value="<?php echo $arrayqueueid; ?>">
		<input type="hidden" name="editsubmit" value="true">
		<table>
			<tr>
			<td><strong>Array Name:</strong></td>
			<td><input name="arrayname" type="text" value="<?php echo $arrayname; ?>" size="64"></input></td>
			</tr>
			<tr><TD><input type="submit" name="submit" value="Submit" ></tr><td><input type="reset" name="reset" value="Reset" ></td></tr>
		</table>
	</form>
<?php
	}else{
	
	$arrayid = $_POST['arrayid'];
	$newname = $_POST['arrayname'];
	$arrayqueueid = $_POST['arrayqueueid'];
	

	// Need to update the necessary tables....

	$updatesql = "UPDATE agilent_arrayinfo SET arraydesc = \"$newname\" WHERE arrayid = $arrayid";
	$updateResult = $db->Execute($updatesql);//mysql_query($updatesql, $db);

		
		$updateerrormsg = "";
		if(!$updateResult){
			echo "uh-oh db error";
			$updateerrormsg .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
			
			$updateerrormsg .= "<br>heres sql:  <br>";
			$updateerrormsg .= "$updateSQL";
			
		}
		else{
				$updateerrormsg .= "<strong><font color=\"green\">Array Name Update Successful!</font><br>";
		}
		if($arrayqueueid != ""){
			$updatesql = "UPDATE arrayqueue SET arrayname = \"$newname\" WHERE arrayqueueid = $arrayqueueid";
			$updateResult =  $db->Execute($updatesql);//mysql_query($updatesql, $db);
			if(!$updateResult){
				echo "uh-oh db error";
				$updateerrormsg .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
				
				$updateerrormsg .= "<br>heres sql:  <br>";
				$updateerrormsg .= "$updateSQL";
				
			}
		}
		echo "$updateerrormsg<br>";


	
	}
}
?>