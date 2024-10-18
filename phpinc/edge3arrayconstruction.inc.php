<?php

/*
This script is utilized by the end-user to create, edit and delete an array queue object based on the RNA samples they've already submitted.
*/

#  First, we need to see what we are doing.  are we creating, editing or deleting an array queue object
$isedit = 0;  # used to determine if we are editing: 0 is no edit, 1 is edit
if(isset($_GET['create'])){
	# place holder.... bad coding :<
	
}elseif(isset($_GET['edit'])){
	
	$arrayqueueid = $_GET['arrayqueueid'];
	# we need to check to see if this array queue object belongs to the user in question to make sure they aren't entering something
	# into the url....
	if(!isset($userid)){
		die("You need to be logged in to use this function.");
	}
	
	$checksql = "SELECT ownerid FROM arrayqueue WHERE arrayqueueid = $arrayqueueid";
	$checkresult  = $db->Execute($checksql);
	if(!$checkresult){
		echo "Invalid sql call in query: $checksql<br>";
	}else{
		$checkrow = $checkresult->FetchRow();
		$ownerid = $checkrow[0];
		
		if($ownerid != $userid){
			die("You are not the creator of this array queue object. $checksql");
		}
	}
	$isedit = 1;
	#echo "the arrayqueueid is $arrayqueueid <br>";
}elseif(isset($_GET['delete'])){
	
	$arrayqueueid = $_GET['arrayqueueid'];
	# we need to check to see if this array queue object belongs to the user in question to make sure they aren't entering something
	# into the url....
	if(!isset($userid)){
		die("You need to be logged in to use this function.");
	}
	$checksql = "SELECT ownerid FROM arrayqueue WHERE arrayqueueid = $arrayqueueid";
	$checkresult  = $db->Execute($checksql);
	if(!$checkresult){
		echo "Invalid sql call in query: $checksql<br>";
	}else{
		$checkrow = $checkresult->FetchRow();
		$ownerid = $checkrow[0];
		
		if($ownerid != $userid){
			die("You are not the creator of this array queue object. $checksql");
		}
	}
	#echo "the arrayqueueid is $arrayqueueid <br>";
}else{
	die("invalid parameter passed to script.");
}
if(isset($_GET['delete'])){
	$getinfoSQL = "SELECT arrayname FROM arrayqueue WHERE arrayqueueid = $arrayqueueid AND ownerid = $userid";
	$infoResult = $db->Execute($getinfoSQL);
	if(!$infoResult){
		echo "Error getting array information: $getinfoSQL<br>";
		$arrayname = "<b><font color='red'>Error getting array name</font></b>";
	}else{
		$inforow=$infoResult->FetchRow();
		$arrayname = "<b><font color='blue'>$inforow[0]</font></b>";
	}
	$deleteSQL = "DELETE FROM arrayqueue WHERE arrayqueueid = $arrayqueueid AND ownerid = $userid";
	$deleteresult = $db->Execute($deleteSQL);
	if(!$deleteresult){
		echo "Error deleting array, $arraydesc: $deleteSQL";
	}else{
		echo "You have deleted the array, $arrayname, from the queue.<br>";
	}
	
	$submittername = $_SESSION['firstname']." ".$_SESSION['lastname'];
	
			$Name = "EDGE admin"; //senders name
			$email = "www-admin"; //senders e-mail adress
			
			
			$mail_body = "An array queue object, $arrayname, was deleted by $submittername.  Please go to http://$serverhost/agilentexperiment-useradmin.php?arraysubmit=1 to view the change"; //mail body
			$subject = "Array Queue object deleted"; //subject
			
			
			$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields
			# some of the variables used here are in ./phpinc/globalvariables.inc.php

			if (!stristr(PHP_OS, 'WIN')) {
			
				if(ini_get('SMTP') != ''){
					$smtpval = ini_get('SMTP');
					#echo "SMTP = $smtpval";
					# Windows smtp setup
					if(!mail($adminrecipient, $subject, $mail_body, $header)){
						echo "Email notifications not configured correctly on this server.";
					}else{
						echo "Email notification sent to EDGE<sup>3</sup> Administrator.";
					}
				}
			}else{ #assuming linux.....	
				if(ini_get('sendmail_path')!= ""){
					# Unix-based setup
					if(!mail($adminrecipient, $subject, $mail_body, $header)){
						echo "Email notifications not configured correctly on this server.";
					}else{
						echo "Email notification sent to EDGE<sup>3</sup> Administrator.";
					}
				}
			}	
	
}else{
	if (!isset($_POST['submit'])) { // if form has not been submitted
		
		
		if($isedit == 1){
			$editsql = "SELECT arrayname, cy3rnasample, cy5rnasample, controlchannel,arraytype,status,ownerid FROM arrayqueue WHERE arrayqueueid = $arrayqueueid";
			$editresult = $db->Execute($editsql);
			if(!$editresult){
				echo "Error in querying database: $editsql";
			}else{
				$editrow = $editresult->FetchRow();
				$arrayname = $editrow[0];
				$cy3rnasample = $editrow[1];
				$cy5rnasample = $editrow[2];
				$controlchannel = $editrow[3];
				$arraytype = $editrow[4];
				$status = $editrow[5];
				$ownerid = $editrow[6];
			}
		}
		$arraytypeMenu = "";
		// Get the RNA samples that have a queuestatus = 0.  These are newly entered RNA samples that have not been reviewed.
		if($privval == 99){
			$trxSQL = "SELECT sampleid,samplename FROM agilent_rnasample ORDER BY sampleid ASC";
		}else{
			
			$trxSQL = "SELECT sampleid,samplename FROM agilent_rnasample where submitterid = $userid OR submitterid = -1 ORDER BY sampleid ASC";
		}
		//echo $trxSQL;
		$trxResult = $db->Execute($trxSQL);//mysql_query($trxSQL, $db);
		$firstchoice = 1;
		$cy3Menu = "<select name=\"cy3channel\" id=\"cy3channel\" onChange=\"updatecy3info()\">";
		$cy5Menu = "<select name=\"cy5channel\" id=\"cy5channel\" onChange=\"updatecy5info()\">";
		if($isedit != 1){
			$cy3Menu .= "<option label=\"\" value=\"-1\" SELECTED>No RNA Sample Selected</option>  ";
			$cy5Menu .= "<option label=\"\" value=\"-1\" SELECTED>No RNA Sample Selected</option>  ";
		}else{
			$cy3Menu .= "<option label=\"\" value=\"-1\" >No RNA Sample Selected</option>  ";
			$cy5Menu .= "<option label=\"\" value=\"-1\" >No RNA Sample Selected</option>  ";
		}
		#while($row = mysql_fetch_row($trxResult)){
		while($row = $trxResult->FetchRow()){
			$id = $row[0];
			$name = $row[1];
			$cy3selected = "";
			$cy5selected = "";
			if($isedit == 1){
				if($id == $cy3rnasample){
					$cy3selected = "SELECTED";
				}
				if($id == $cy5rnasample){
					$cy5selected = "SELECTED";
				}
				$cy3Menu .= "<option label=\"$name\" value=\"$id\" $cy3selected>$name</option>  ";
				$cy5Menu .= "<option label=\"$name\" value=\"$id\" $cy5selected>$name</option>  ";
			}else{
				$cy3Menu .= "<option label=\"$name\" value=\"$id\">$name</option>  ";
				$cy5Menu .= "<option label=\"$name\" value=\"$id\">$name</option>  ";
			}
			
		}	
		$cy3Menu .= "</select>";
		$cy5Menu .= "</select>";
	
		$agilentarraytypeSQL = "SELECT id, organism, arraydesc, version FROM agilentarrays";
			$arraytypeResult = $db->Execute($agilentarraytypeSQL);//mysql_query($agilentarraytypeSQL, $db);
			#while(list($arrayid, $organism, $arraydesc, $version) = mysql_fetch_array($arraytypeResult))
			while($row = $arraytypeResult->FetchRow())
			{
				$arrayid = $row[0];
				$organism = $row[1];
				$arraydesc = $row[2];
				$version = $row[3];
				if($isedit != 1){
					$arraytypeMenu .= "<option value=\"$arrayid\">$organism $arraydesc $version</option>\r";
				}else{
					$arraytypeselected = "";
					if($arrayid == $arrayqueueid){
						$arraytypeMenu .= "<option value=\"$arrayid\" SELECTED>$organism $arraydesc $version</option>\r";
					}else{
						$arraytypeMenu .= "<option value=\"$arrayid\">$organism $arraydesc $version</option>\r";
					}
					
				}
				
			}
			if($isedit != 1){
				$arraytypeMenu .= "<option label=\"\" value=\"-1\" SELECTED>No Species Array selected</option>";
			}else{
				$arraytypeMenu .= "<option label=\"\" value=\"-1\">No Species Array selected</option>";
			}
			
	?>
	<form name="createarray" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkCreateArrayForm()">
		<table class="question" width="400">
		<thead>
		<tr>
		<!-- <th class="mainheader" colspan="2">Edit Chemical Attribute</th> -->
		<?php
			$arrayfunction = "Create";
			if($isedit==1){
				$arrayfunction = "Edit";
			}
		?>
		<th class="mainheader" colspan="3"><font color="black"><b><?php echo $arrayfunction; ?> an array</b></font></th>
		</tr>
		</thead>
		<?php
			if($isedit != 1){
		?>
		<tr><td colspan="3" class="questionparameter"><font color="black"><strong>Instructions: </strong> This form is used to create an array derived from your previously submitted RNA samples.
		If you've not entered your RNA samples already, please do so by clicking on the <b><i>New RNA Submission</i></b> link under the <b>Administration Menu</b>
		section to the left.  To successfully submit this form, you must choose an RNA sample for the <font color="green"><b>Cy3</b></font> channel
		and the <font color="red"><b>Cy5</b></font> channel.  You also must enter an array name.  Completing this form enters your array into the queue.
		You can see whether your arrays have been completed by looking at <b>Your Array Queue</b> to the left.</font></td></tr>
		<?php
			}else{
		?>
		<tr><td colspan="3" class="questionparameter"><font color="black"><strong>Instructions: </strong> This form is used to edit an array you previously entered.
		If you've not entered your RNA samples already, please do so by clicking on the <b><i>New RNA Submission</i></b> link under the <b>Administration Menu</b>
		section to the left.  To successfully submit this form, you must choose an RNA sample for the <font color="green"><b>Cy3</b></font> channel
		and the <font color="red"><b>Cy5</b></font> channel.  You also must enter an array name.  Completing this form updates your array.</font></td></tr>
		<input type="hidden" name="arrayqueueid" value="<?php echo $arrayqueueid; ?>">
		<input type="hidden" name="isedit" value="1">
		<?php		
			}
		?>	
	
		<tr><TD><strong>Array Type/Species: </strong></TD><td><select name="arraytype">
		<?php echo $arraytypeMenu; ?>
		</select></td><td></td></tr>
		<tr><TD><font color="green"><b>Cy3 RNA Sample: </b></font></TD><td><?php echo "$cy3Menu"; ?></td><td></td></tr>
		<tr><TD><font color="red"><b>Cy5 RNA Sample: </b></font></TD><td><?php echo "$cy5Menu"; ?></td><td></td></tr>
		<td align="top"><strong>Control Sample Channel:</strong></td>
		<td>
	<?php
		$cy3controlselected = "";
		$cy5controlselected = "";
		if($isedit == 1){
			if($controlchannel == 3){
				$cy3controlselected = "SELECTED";
			}else{
				$cy5controlselected = "SELECTED";
			}
			
		}
	?>
		<select name="controlchannel">
		<option value=5 style="background-color: red;" <?php echo $cy5controlselected; ?>>cy5</option>
		<option value=3 style="background-color: green;" <?php echo $cy3controlselected; ?>>cy3</font></option>
		</select>
		
		</td><td><img id="clusterSelection" src="./images/dialog-information12x12.png" align="right"/><div dojoType="dijit.Tooltip" connectId="clusterSelection"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Choosing Control Channel</u></strong></td></tr><tr><td><font color="red">NOTE:</font>This allows you to select which dye you want in the denominator of the ratio
		 (i.e., if you choose <font color="red"><b>Cy5</b></font> here, the ratio will be <font color="green"><b>Cy3</b></font>/<font color="red"><b>Cy5</b></font>).  Normally we label our control or reference samples with cy5.</td></tr></table></div></td>
		</tr>
		<tr><TD><font color="black"><b>Array Name: </b></font></TD><td><input name="arrayname" type="text" align="right" size="75" value="<?php if($isedit == 1){ echo $arrayname;}?>"></input></td><td></td></tr>
		<tr>
		<td><input type="submit" name="submit" value="Submit Array"></td>
		<td><input type="reset" value="Reset Array Form"></td><td></td>
		</tr>
		</table>
	</form>
	<?php
	}else{
		if(isset($_POST['cy3channel'])){
			$cy3channel = $_POST['cy3channel'];
		}else{
			$cy3channel = "";
		}
		if(isset($_POST['cy5channel'])){
			$cy5channel = $_POST['cy5channel'];
		}else{
			$cy5channel = "";
		}
		if(isset($_POST['arrayname'])){
			$arrayname = $_POST['arrayname'];
		}else{
			$arrayname = "";
		}
		if(isset($_POST['controlchannel'])){
			$controlchannel =$_POST['controlchannel'];
		}else{
			$controlchannel = "";
		}
		if(isset($_POST['arraytype'])){
			$arraytype = $_POST['arraytype'];
		}else{
			$arraytype = "";
		}
		if(isset($_POST['isedit'])){
			$isedit = 1;
		}else{
			$isedit = 0;
		}
		if(isset($_POST['arrayqueueid'])){
			$arrayqueueid = $_POST['arrayqueueid'];
		}else{
			$arrayqueueid = "";
		}
		
	
		// need to insert the following into the arrayqueue table:  arrayqueueid, arrayname, cy3rnasample, cy5rnasample, controlchannel, ownerid
		if($isedit != 1){
			$sql = "INSERT arrayqueue (arrayname, cy3rnasample, cy5rnasample, controlchannel, arraytype, ownerid) VALUES (\"$arrayname\", $cy3channel, $cy5channel, $controlchannel, $arraytype, $userid)";
			
		}else{
			$sql = "UPDATE arrayqueue SET arrayname = \"$arrayname\", cy3rnasample = $cy3channel, cy5rnasample = $cy5channel, arraytype = $arraytype, controlchannel = $controlchannel, ownerid = $userid WHERE arrayqueueid = $arrayqueueid";
		}
		$sqlResult = $db->Execute($sql);
		if(!$sqlResult){
			echo "<strong>Database Error updating table. SQL: $sql </strong><br>";
		}else{
		if($isedit != 1){
			echo "<br><br><br>Thanks!  Your array has been entered into the queue.  <br><br>To enter another, please click on the <b><i>Array Construction</i></b> link under the <b>Administration Menu</b> section to the left.<br> <br> <font color=\"red\"><b>Do not refresh this page or you will have duplicate arrays!</b></font>";
		}else{
			echo "<br><br><br>Thanks!  Your array has been edited and re-entered into the queue.";
		}

			$submittername = $_SESSION['firstname']." ".$_SESSION['lastname'];
	
			$Name = "EDGE admin"; //senders name
			$email = "www-admin"; //senders e-mail adress
			
			if($isedit != 1){
				$mail_body = "A new array was created for processing, $arrayname, was submitted by $submittername.  Please go to http://$serverhost/agilentexperiment-useradmin.php?arraysubmit=1 to view the new submission"; //mail body
				$subject = "New Array Created on EDGE"; //subject
			}else{
				$mail_body = "An array queue object, $arrayname, was edited by $submittername.  Please go to http://$serverhost/agilentexperiment-useradmin.php?arraysubmit=1 to view the modified submission"; //mail body
				$subject = "Array Queue object edited"; //subject
			}
			
			$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields
			# some of the variables used here are in ./phpinc/globalvariables.inc.php
			if (!stristr(PHP_OS, 'WIN')) {
			
				if(ini_get('SMTP') != ''){
					$smtpval = ini_get('SMTP');
					#echo "SMTP = $smtpval";
					# Windows smtp setup
					if(!mail($adminrecipient, $subject, $mail_body, $header)){
						echo "Email notifications not configured correctly on this server.";
					}else{
						echo "Email notification sent to EDGE<sup>3</sup> Administrator.";
					}
				}
			}else{ #assuming linux.....	
				if(ini_get('sendmail_path')!= ""){
					# Unix-based setup
					if(!mail($adminrecipient, $subject, $mail_body, $header)){
						echo "Email notifications not configured correctly on this server.";
					}else{
						echo "Email notification sent to EDGE<sup>3</sup> Administrator.";
					}
				}
			}	
	
	
		}
	
	
	
	
	}
}  // end of else of if(isset($_GET['delete']))


?>
