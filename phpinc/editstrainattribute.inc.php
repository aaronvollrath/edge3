<?php
/***
Location: /edge2/admin
Description:  This script allows for the editing of chemical attributes
***/
require 'edge_db_connect2.php';
#$db = mysql_connect("localhost", "root", "arod678cbc3");
#mysql_select_db("edge", $db);

// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}

include('../utilityfunctions.inc');
//analyze($_POST);
if(isset($_POST['submit'])){
	if(isset($_POST['strainsubmit'])){
		$strainsubmit = $_POST['strainsubmit'];
	}else{
		$strainsubmit = "";
	}
	if(isset($_POST['strain'])){
		$strain = $_POST['strain'];
	}else{
		$strain = "";
	}
	if(isset($_POST['newstrain'])){
		$newstrain = $_POST['newstrain'];
	}else{
		$newstrain = "";
	}
	if(isset($_POST['organism'])){
		$organism = $_POST['organism'];
	}else{
		$organism = "";
	}
	if(isset($_POST['addinfo'])){
		$addinfo = $_POST['addinfo'];
	}else{
		$addinfo = "";
	}
	if(isset($_POST['submit'])){
		$submit = $_POST['submit'];
	}else{
		$submit = "";
	}
}
if(!isset($sampleid)){
	$sampleid = "";

}


echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="../css/newlayout.css" title="layout" />
<title>EDGE^2</title>
</head>

<body>

	

 <div class="boxmiddle">

<h3 class="contenthead">Edit Strain Attribute </h3>
<?php
$priv_level = $_SESSION['priv_level'];

if (!isset($_POST['submit'])) { // if form has not been submitted
	//if($submitted != true){
?>

  <?php
	$strainMenu = "";
	$organismMenu = "";
		$strainSQL = "SELECT strainid, strain FROM strain ORDER BY strain ASC";

		$strainResult = $db->Execute($strainSQL);//mysql_query($strainSQL, $db);
		$firstchoice = 1;
		$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"0\" checked>No Deletion  ";
		$firstchoice++;
		#while(list($strainid,$strain) = mysql_fetch_array($strainResult))
		while($row=$strainResult->FetchRow()){
			$strainid = $row[0];
			$strain = $row[1];
			if($firstchoice%5==0){
				$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"$strainid\">$strain  <br>";
				
			}
			else{
				$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"$strainid\">$strain  ";
			}
			$firstchoice++;
		}



		
		$organismSQL = "SELECT id, organism FROM agilentarrays ORDER BY id ASC";
		$organismResult = $db->Execute($organismSQL);//mysql_query($organismSQL, $db);
		$firstchoice = 1;
		$numOrganisms = $organismResult->RecordCount();//mysql_num_rows($organismResult);
		//echo "Array organism: $sampleidorganism<br>";
		#while(list($id, $organism) = mysql_fetch_array($organismResult))
		while($row = $organismResult->FetchRow())
		{
			$id = $row[0];
			$organism = $row[1];
			if($sampleid != "" && $id == $sampleidorganism){
				$checked = "checked";
			}else{
				$checked = "";
			}
			if($firstchoice == 1){
				if($sampleid == ""){
					$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"$id\" checked\">$organism  ";
				}else{
					$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"$id\" $checked\">$organism  ";

				}
				$firstchoice = 0;
			}
			else{
				$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"$id\" $checked\">$organism  ";
			}
		}
		


?>


<form name="updatestrains" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkSubmissionForm()">
<input type="hidden" name="strainsubmit" value="true">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2">Edit Strain Attribute</th>
</tr>
</thead>
<tr><td colspan="2" class="questionparameter"><font color="red"><strong>Instructions: </strong></font>To add a strain, enter the strain name in the first text input field, <i><b>Add Strain</b></i>.  The last field, <b><i>Add. Info. for Strain</i></b> allows you to enter additional info and notes about the new strain. Any new entries will be reviewed.</td></tr>
	<tr>
<?php
if($priv_level == 99){
?>
	<tr>
	<td class="questionparameter" ><strong>Delete Strain:</strong></td>
	<td class="results">
	<?php echo $strainMenu; ?>
	</td>
	</tr>
<?php
}
?>
<tr>
<td class="questionparameter" ><strong>Organism:</strong></td>
<td class="results">
<?php echo $organismMenu; ?>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Add Strain:</strong></td>
<td class="results">
<input name="newstrain" type="text" align="right"></input>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Add. Info. for Added Strain:</strong></td>
<td class="results">
<input name="addinfo" type="text" align="right" size="40" maxlength="256"></input>
</td>
</tr>
<tr>
<td><input type="submit" name="submit" value="Submit Strain Change"></td>
<td><input type="reset" value="Reset Strain Form"></td>
</tr>
</table>
</form>

<! -- do the actual work in the database here -->
<?php
	}  // end if not submitted....
	else{
		if($strainsubmit == "true"){
		// Following 2 vars used for determining whether a strain has been added or deleted.
		$strainadded = 0;
		$straindeleted = 0;

		if($strain != 0){
			$strainSQL = "SELECT strain FROM strain WHERE strainid = $strain";
			$strainResult = $db->Execute($strainSQL);//mysql_query($strainSQL, $db);
			$row = $strainResult->FetchRow();//mysql_fetch_row($strainResult);
			$delstrain = $row[0];
			$delstrainSQL = "DELETE FROM strain WHERE strainid = $strain";
			$delstrainResult = $db->Execute($delstrainSQL);//mysql_query($delstrainSQL, $db);
			$straindeleted = 1;
		}
		if($newstrain != ""){
			$addstrainSQL = "INSERT strain (strain, organismid, addinfo) VALUES (\"$newstrain\", $organism, \"$addinfo\")";
			$row = $db->Execute($addstrainSQL);//mysql_query($addstrainSQL, $db);
			$strainadded = 1;
		}


	?>
			<table class="question" width="400">
				<thead>
				<tr>
				<th class="mainheader" colspan="2">Strain Changes Made</th>
				</tr>
				</thead>
				<tr>
				<td class="questionparameter" ><strong>Strain Deleted:</strong></td>
				<td class="results">
					<?php
						if($straindeleted == 1){
							echo "Deleted $delstrain";
						}
						else{
							echo "None";
						}
					?>
				</td>
				</tr>
				<td class="questionparameter" ><strong>Strain Added:</strong></td>
				<td class="results">
					<?php
						if($strainadded == 1){
							echo "Added $newstrain";
						}
						else{
							echo "None";
						}
					?>
				</td>
				</tr>
			</table>


<?php
	}
}// end of all submits....
?>

 </div>



 <div class="boxclear"> </div>




 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>

</body>
</html>
