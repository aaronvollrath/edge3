<?php
/***
Location: /edge2/admin
Description:  This script allows for the editing of genevariation attributes
***/
require 'edge_db_connect2.php';
//$db = mysql_connect("localhost", "root", "arod678cbc3");
//mysql_select_db("edge", $db);

// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}

//require("fileupload-class.php");
//include 'edge_update_user_activity.inc';
include('../utilityfunctions.inc');
if(isset($_POST['submit'])){
	//analyze($_POST);
	$genevariation = $_POST['genevariation'];
	$newgenevariation = $_POST['newgenevariation'];
	$submit = $_POST['submit'];
	$addinfo = $_POST['addinfo'];
	$genevariationsubmit = $_POST['genevariationsubmit'];
}
?>

<?php
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
	<h3 class="contenthead">Edit Genetic Variation Attribute</h3>
<?php
$priv_level = $_SESSION['priv_level'];


if (!isset($_POST['submit'])) { // if form has not been submitted
	$genevariationMenu = "";
?>

  <?php

		$genevariationSQL = "SELECT genevariationid, genevariation FROM genevariation ORDER BY genevariation ASC";

		$genevariationResult = $db->Execute($genevariationSQL);//mysql_query($genevariationSQL, $db);
		$firstchoice = 1;
		$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"0\" checked>No Deletion  ";
		$firstchoice++;
		#while(list($genevariationid, $genevariation) = mysql_fetch_array($genevariationResult))
		while($row = $genevariationResult->FetchRow())
		{
			$genevariationid = $row[0];
			$genevariation = $row[1];
			if($firstchoice%5==0){
				$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"$genevariationid\">$genevariation  <br>";
				$firstchoice++;
			}
			else{
				$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"$genevariationid\">$genevariation  ";
				$firstchoice++;
			}
		}

?>

<form name="updategenevariation" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkSubmissionForm()">
<input type="hidden" name="genevariationsubmit" value="true">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2"></th>
</tr>
</thead>
<tr><td colspan="2" class="questionparameter"><font color="red"><strong>Instructions: </strong></font>To add a genetic variation, enter the genetic variation name in the first text input field, <i><b>Add Genetic Variation</b></i>.  The last field, <b><i>Add. Info. for Added Gen. Var.</i></b> allows you to enter additional info and notes about the new genetic variation.</td></tr>

<?php
	if($priv_level == 99){
?>
	<tr>
	<td class="questionparameter" ><strong>Delete Genetic Variation:</strong></td>
	<td class="results">
	<?php echo $genevariationMenu; ?>
	</td>
	</tr>
<?php
	}
?>
<tr>
<td class="questionparameter" ><strong>Add Genetic Variation:</strong></td>
<td class="results">
<input name="newgenevariation" type="text" align="right"></input>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Add. Info. for Added Gen. Var.:</strong></td>
<td class="results">
<input name="addinfo" type="text" align="right" size="40" maxlength="256"></input>
</td>
</tr>
<tr>
<td><input type="submit" name="submit" value="Submit Genetic Variation Change"></td>
<td><input type="reset" value="Reset Genetic Variation Form"></td>
</tr>
</table>
</form>

<! -- do the actual work in the database here -->
<?php
	}  // end if not submitted....
	else{
		// What value is to be updated.....
		if($genevariationsubmit == "true"){

			// Following 2 vars used for determining whether a genevariation has been added or deleted.
		$genevariationadded = 0;
		$genevariationdeleted = 0;

		if($genevariation != 0){
			$genevariationSQL = "SELECT genevariation FROM genevariation WHERE genevariationid = $genevariation";
			$genevariationResult = $db->Execute($genevariationSQL);//$mysql_query($genevariationSQL, $db);
			$row = $genevariationResult->FetchRow();//mysql_fetch_row($genevariationResult);
			$delgenevariation = $row[0];
			$delgenevariationSQL = "DELETE FROM genevariation WHERE genevariationid = $genevariation";
			$delgenevariationResult = $db->Execute($delgenevariationSQL);//mysql_query($delgenevariationSQL, $db);
			$genevariationdeleted = 1;
		}
		if($newgenevariation != ""){
			$addgenevariationSQL = "INSERT genevariation (genevariation, addinfo) VALUES (\"$newgenevariation\", \"$addinfo\")";
			$row = $db->Execute($addgenevariationSQL);//mysql_query($addgenevariationSQL, $db);
			$genevariationadded = 1;
		}

	?>
			<table class="question" width="400">
				<thead>
				<tr>
				<th class="mainheader" colspan="2">Genetic Variation Changes Made</th>
				</tr>
				</thead>
				<tr>
				<td class="questionparameter" ><strong>Genetic Variation Deleted:</strong></td>
				<td class="results">
	<?php
						if($genevariationdeleted == 1){
							echo "Deleted $delgenevariation";
						}
						else{
							echo "None";
						}
					?>
				</td>
				</tr>
				<td class="questionparameter" ><strong>Gene Variation Added:</strong></td>
				<td class="results">
					<?php
						if($genevariationadded == 1){
							echo "Added $newgenevariation";
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
 <?php
	//include '../adminleftmenu.inc';

?>


 <div class="boxclear"> </div>




 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>

</body>
</html>
