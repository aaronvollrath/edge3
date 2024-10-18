<?php


if(isset($userid)){
		$arraysql = "SELECT arrayqueueid, arrayname, cy3rnasample, cy5rnasample, status, controlchannel, arraytype FROM  arrayqueue where ownerid = $userid ORDER BY status ASC";
		$sqlResult = $db->Execute($arraysql);//mysql_query($arraysql, $db);
		if(!$sqlResult){
			echo "<strong>Database Error getting arrays. SQL: $arraysql </strong><br>";
		}else{
			echo "<table class='results'><tr><td colspan='2'><b><u>Status Key</u></b></td></tr><tr><td><b>In Queue</b></td><td><img src=\"./images/inqueuestatus.png\" height=\"14px\" width=\"20px\"></td></tr>";
			echo "<tr><td><b>Completed</b></td><td><img src=\"./images/completedstatus.png\" height=\"14px\" width=\"20px\"></td></tr>";
			echo "<tr><td><b>Error in Hybing</b></td><td><img src=\"./images/errorstatus.png\" height=\"14px\" width=\"20px\"></td></tr></table>";
			echo "<br><br><table><tr><td><b>Name</b></td><td><b>Cy3 RNA Sample</b></td><td><b>Cy5 RNA Sample</b></td><td><b>Control Channel</b></td><td align='center'><b>Array Type</b></td><td><b>Status</b></td><td align='center'><b>Action</b></tr>";
			//while($row=mysql_fetch_row($sqlResult)){
			while($row=$sqlResult->FetchRow()){
				$arrayqueueid = $row[0];
				$arrayname = $row[1];
				$cy3rnasample=$row[2];
				$cy3sql = "SELECT samplename from agilent_rnasample WHERE sampleid = $cy3rnasample";
				$cy3result = $db->Execute($cy3sql);
				$cy3rnasamplename = "";
				if(!$cy3result){
						echo "<strong>Database Error getting arrays. SQL: $cy3sql </strong><br>";
				}else{
						$cy3row = $cy3result->FetchRow();
					$cy3rnasamplename = $cy3row[0];
				}
				$cy5rnasample=$row[3];
				$cy5sql = "SELECT samplename from agilent_rnasample WHERE sampleid = $cy5rnasample";
				$cy5result = $db->Execute($cy5sql);
				$cy5rnasamplename = "";
				if(!$cy5result){
						echo "<strong>Database Error getting arrays. SQL: $cy5sql </strong><br>";
				}else{
						$cy5row = $cy5result->FetchRow();
					$cy5rnasamplename = $cy5row[0];
				}
				
				$status = $row[4];
				$controlchannel = $row[5];
				$arraytype = $row[6];
				$action = "";
				switch($status){
					case 0:
						$img = "<img src=\"./images/inqueuestatus.png\" height=\"14px\" width=\"20px\">";
						$action = "<a href='agilentexperiment-useradmin.php?arrayconstruction=1&admin=1&edit=1&arrayqueueid=$arrayqueueid'>
						<img src='./images/edit.png' title='Edit' alt='Edit' ></a>
						<a href='agilentexperiment-useradmin.php?arrayconstruction=1&admin=1&delete=1&arrayqueueid=$arrayqueueid' onclick='return confirm(\"Are you sure you want to continue deleting this array queue object?\");' ><img src='./images/delete.png'  title='Delete' alt='Delete' ></a>";						break;
					case 1: 
						$img = "<img src=\"./images/completedstatus.png\" height=\"14px\" width=\"20px\">";
						
						break;
					case 2:
						$img = "<img src=\"./images/errorstatus.png\" height=\"14px\" width=\"20px\">";
						$action = "<a href='agilentexperiment-useradmin.php?arrayconstruction=1&admin=1&edit=1&arrayqueueid=$arrayqueueid'><img src='./images/edit.png' title='Edit' alt='Edit'></a>";
						break;
				}
				if($controlchannel == 3){
						$fontcolor = "green";
				}else{
						$fontcolor = "red";
				}
				$arraytypesql = "SELECT organism, arraydesc, version FROM agilentarrays WHERE id = $arraytype";
				$arraytyperesult = $db->Execute($arraytypesql);
				if(!$arraytyperesult){
						echo "Error getting array type: $arraytypesql";
				}else{
						$arraytyperow = $arraytyperesult->FetchRow();
						$organism = $arraytyperow[0];
						$arraydesc = $arraytyperow[1];
						$version = $arraytyperow[2];
						$arraydesc = $arraydesc." ".$version." ($organism)";
				}
				echo "<tr><td>$arrayname</td><td><a href='rnasampleinfo.php?sampleid=$cy3rnasample' target='_blank'>$cy3rnasamplename</a></td>
				<td><a href='rnasampleinfo.php?sampleid=$cy5rnasample' target='_blank'>$cy5rnasamplename</a>
				<td align='center'><font color='$fontcolor'><b>cy$controlchannel</b></font></td><td>$arraydesc</td>
				<td align=\"center\">$img</td><td>$action</td></tr>";
	
			}
			echo "</table>";
	
		}
	}else{
		echo "You are not logged in...<br>";
	}
?>
