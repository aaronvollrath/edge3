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

//require("fileupload-class.php");
//include 'edge_update_user_activity.inc';
include('../utilityfunctions.inc');
if(isset($_POST['submit'])){
	//echo "submitted....<br>";
	//analyze($_POST);
	if(isset($_POST['chemical'])){
			$chemical = $_POST['chemical'];

	}else{
		$chemical = "";
	}
	if(isset($_POST['newchemical'])){
		$newchemical = $_POST['newchemical'];

	}else{
		$newchemical = "";
	}
	if(isset($_POST['chemsubmit'])){
		$chemsubmit = $_POST['chemsubmit'];

	}else{
		$chemsubmit = "";
	}
	
	if(isset($_POST['type'])){
		$type = $_POST['type'];

	}else{
		$type = "";
	}
	if(isset($_POST['newClass'])){
		$newClass = $_POST['newClass'];

	}else{
		$newClass = "";
	}
	
	if(isset($_POST['class'])){
		$class = $_POST['class'];
	}else{
		$class = "";
	}
	if(isset($_POST['addinfo'])){
		$addinfo = $_POST['addinfo'];
	}else{
		$addinfo = "";
	}
	
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
<title>EDGE^3</title>
</head>
<?php
//require "newtreatmentcheck.inc";
?>
<body>

	

 <div class="boxmiddle">

<?php
$priv_level = $_SESSION['priv_level'];
/*if($priv_level != 99){
	echo "Sorry, you are not authorized to access this page.";
}
else{*/

		if (!isset($_POST['submit'])) { // if form has not been submitted
	//if($submitted != true){
?>

  <?php

		
		$chemMenu = "";
		// GETTING THE VALUES TO POPULATE THE SELECTIONS.....
		// create chem menu
		$chemSQL = "SELECT DISTINCT chemid, chemical FROM chem ORDER BY chemid";

		$chemResult = $db->Execute($chemSQL);//mysql_query($chemSQL, $db);
		$firstchoice = 1;
		$chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"0\" checked>No Deletion  ";
		$firstchoice++;
		#while(list($chemid, $chemical) = mysql_fetch_array($chemResult))
		while($row=$chemResult->FetchRow())
		{
			$chemid = $row[0];
			$chemical = $row[1];
			if($firstchoice%5==0){
				$chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"$chemid\">$chemical  <br>";
				$firstchoice++;
			}
			else{
				$chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"$chemid\">$chemical  ";
				$firstchoice++;
			}
		}


		// create class menu
		$classSQL = "SELECT classid, name FROM class ORDER BY classid";

		$classResult = $db->Execute($classSQL);//mysql_query($classSQL, $db);
		$firstchoice = 1;
		//$classAttMenu .= "<input type=\"radio\" name=\"class\" value=\"0\" checked>No Deletion  ";
		#while(list($classid, $name) = mysql_fetch_array($classResult))
		$classMenu = "";
		$classAttMenu = "";
		while($row = $classResult->FetchRow())
		{
			$classid = $row[0];
			$name = $row[1];
				if($firstchoice == 1){
					$classMenu .= "<input type=\"checkbox\" name=\"newclass$classid\" value=\"$classid\" checked>$name  ";
					$classAttMenu .= "<input type=\"radio\" name=\"class\" value=\"$classid\">$name  ";
					$firstchoice++;
				}
				else{

					if($firstchoice%5==0){
						$classMenu .= "<input type=\"checkbox\" name=\"newclass$classid\" value=\"$classid\">$name <br>";
						$classAttMenu .= "<input type=\"radio\" name=\"class\" value=\"$classid\">$name  <br>";
						$firstchoice++;
					}
					else{
						$classMenu .= "<input type=\"checkbox\" name=\"newclass$classid\" value=\"$classid\">$name ";
						$classAttMenu .= "<input type=\"radio\" name=\"class\" value=\"$classid\">$name  ";
						$firstchoice++;
					}

				}

		}

?>


<form name="editchemicalattribute" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkSubmissionForm()">
<input type="hidden" name="chemsubmit" value="true">
<table class="question" width="400">
<thead>
<tr>
<!-- <th class="mainheader" colspan="2">Edit Chemical Attribute</th> -->
<th class="mainheader" colspan="2">Add Chemical/Condition Attribute</th>
</tr>
</thead>
<tr><td colspan="2" class="questionparameter"><font color="red"><strong>Instructions: </strong></font>To add a chemical or condition, enter the Chemical or Condition name in the first text input field, <i><b>Add Chemical/Condition</b></i>.  You then need to select a treatment type.  A chemical/condition class also needs to be selected or created.  You can create a new class by entering a value in the <i><b>Add New Class</b></i> field.  The last field, <b><i>Add. Info. for Added Chemical/Condition</i></b> allows you to enter additional info and notes about the new chemical/condition. Any new entries will be reviewed.  If you are entering a chemical, please paste the corresponding url for that chemical from the <a href="http://ctd.mdibl.org/" target="_blank">CTD database</a> in the <b><i>Add. Info. for Added Chemical/Condition</i></b> field.</td></tr>
<?php

if($priv_level == 99){
?>
<tr>
<td class="questionparameter" ><strong>Delete Chemical/Condition:</strong></td>
<td class="results">
<?php echo $chemMenu; ?>
</td>
</tr>
<?php
}
?>
<tr>
<td class="questionparameter" ><strong>Add Chemical/Condition:</strong></td>
<td class="results">
<input name="newchemical" type="text" align="right"></input>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Treatment Type:</strong></td>
<td class="results">
<input type="radio" name="type" value="0" checked>Chemical
<input type="radio" name="type" value="1">Condition/Vehicle
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Chemical/Condition Class:</strong><br><a href = "chemclassassociation.php" target="_blank">View all Chemical-Class Associations</a></td>
<td class="results">
<?php echo $classAttMenu; ?>
<br>
Add New Class<input type="textbox" name=newClass value="">
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Add. Info. for Added Chemical/Condition:</strong></td>
<td class="results">
<input name="addinfo" type="text" align="right" size="40" maxlength="256"></input>
</td>
</tr>
<tr>
<td><input type="submit" name="submit" value="Submit Chemical Change"></td>
<td><input type="reset" value="Reset Chemical Form"></td>
</tr>
</table>
</form>

<! -- do the actual work in the database here -->
<?php
	}  // end if not submitted....
	else{
		// What value is to be updated.....
		if($chemsubmit == "true"){


		// Following 2 vars used for determining whether a chemical has been added or deleted.
		$chemadded = 0;
		$chemdeleted = 0;

		if($chemical != 0){
			$chemSQL = "SELECT chemical FROM chem WHERE chemid = $chemical";
			$chemResult = $db->Execute($chemSQL);//mysql_query($chemSQL, $db);
			$row = $chemResult->FetchRow();//mysql_fetch_row($chemResult);
			$delchem = $row[0];
			$delchemSQL = "DELETE FROM chem WHERE chemid = $chemical";
			$delchemResult = $db->Execute($delchemSQL);//mysql_query($delchemSQL, $db);
			$delchemSQL1 = "DELETE FROM chemclass WHERE chemid = $chemical";
			$delchemResult1 = $db->Execute($delchemSQL1);//mysql_query($delchemSQL1, $db);
			$chemdeleted = 1;
		}
		if($newchemical != ""){

			/*
			Check if newly added chemical belongs to new class being added
			If so, create new class first... then add chemical to this new class
			*/
			if($newClass == ""){
				$chemmaxSQL = "SELECT MAX(chemid) FROM chem";
				$chemmaxResult = $db->Execute($chemmaxSQL);//mysql_query($chemmaxSQL, $db);
				$row = $chemmaxResult->FetchRow();//mysql_fetch_row($chemmaxResult);
				$chemmax = $row[0] + 1;
				
				// insert new chemical into chem table
				$addchemSQL = "INSERT chem (chemid, chemical, trx_type, aliases, class) VALUES ($chemmax, \"$newchemical\", $type, \"$addinfo\", \"$class\")";
				$row = $db->Execute($addchemSQL);//mysql_query($addchemSQL, $db);
				// insert new entry in chemclass
				$addchemSQL = "INSERT chemclass (chemid, class) VALUES ($chemmax, \"$class\")";
				$row = $db->Execute($addchemSQL);//mysql_query($addchemSQL, $db);
				$chemadded = 1;
			}
			else{
				// This is the case when a new chemical-class is being added.
				// since the version of mySQL that we are using does not support nested SQL queries,
				// we will get the second max classid using two steps. We are using the second max classid
				// since the maxid:99 has been reserved for the class:others

				$chemmaxSQL = "SELECT MIN(classid) FROM class";
				$chemmaxResult = $db->Execute($chemmaxSQL);//mysql_query($chemmaxSQL, $db);
				$row = $chemmaxResult->FetchRow();//mysql_fetch_row($chemmaxResult);
				$min = $row[0];

				$chemmaxSQL = "SELECT COUNT(classid) FROM class";
				$chemmaxResult = $db->Execute($chemmaxSQL);//mysql_query($chemmaxSQL, $db);
				$row = $chemmaxResult->FetchRow();//mysql_fetch_row($chemmaxResult);
				$count = $row[0] -1;

				$classmax = $min + $count;

				// insert new class into class table
				$addchemSQL = "INSERT class(classid,name) VALUES ($classmax, \"$newClass\")";
				$row = $db->Execute($addchemSQL);//mysql_query($addchemSQL, $db);

				$chemmaxSQL = "SELECT MAX(chemid) FROM chem";
				$chemmaxResult = mysql_query($chemmaxSQL, $db);
				$row = mysql_fetch_row($chemmaxResult);
				$chemmax = $row[0] + 1;
				
				// insert new chemical into chem table
				$addchemSQL = "INSERT chem (chemid, chemical, trx_type, aliases, class) VALUES ($chemmax, \"$newchemical\", $type, \"$addinfo\", \"$classmax\")";
				$row = $db->Execute($addchemSQL);//mysql_query($addchemSQL, $db);
				// insert new entry in chemclass
				$addchemSQL = "INSERT chemclass (chemid, class) VALUES ($chemmax, \"$classmax\")";
				$row = $db->Execute($addchemSQL);//mysql_query($addchemSQL, $db);
				$chemadded = 1;
			}
		}

	?>
			<table class="question" width="400">
				<thead>
				<tr>
				<th class="mainheader" colspan="2">Chemical Changes Made</th>
				</tr>
				</thead>
				<tr>
				<td class="questionparameter" ><strong>Chemical Deleted:</strong></td>
				<td class="results">
					<?php
						if($chemdeleted == 1){
							echo "Deleted $delchem";
						}
						else{
							echo "None";
						}
					?>
				</td>
				</tr>
				<td class="questionparameter" ><strong>Chemical Added:</strong></td>
				<td class="results">
					<?php
						if($chemadded == 1){
							echo "Added $newchemical";
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
//}
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
