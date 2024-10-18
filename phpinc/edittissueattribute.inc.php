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
	$tissuesubmit = $_POST['tissuesubmit'];
	$tissue = $_POST['tissue'];
	$newtissue = $_POST['newtissue'];
	$addinfo = $_POST['addinfo'];
	$submit = $_POST['submit'];
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



	<h3 class="contenthead">Edit Tissue Attribute</h3>
<?php
$priv_level = $_SESSION['priv_level'];


if (!isset($_POST['submit'])) { // if form has not been submitted
	
?>

  <?php
	$tissueMenu = "";
		$tissueSQL = "SELECT tissueid, tissue FROM tissue ORDER BY tissue ASC";

		$tissueResult = mysql_query($tissueSQL, $db);
		$firstchoice = 1;
		$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"0\" checked>No Deletion  ";
		$firstchoice++;
		while(list($tissueid, $tissue) = mysql_fetch_array($tissueResult))
		{
			if($firstchoice%5==0){
				$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissueid\">$tissue  <br>";
				$firstchoice++;
			}
			else{
				$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissueid\">$tissue  ";
				$firstchoice++;
			}
		}
?>


<form name="updatetissue" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkSubmissionForm()">
<input type="hidden" name="tissuesubmit" value="true">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2"></th>
</tr>
</thead>
<tr><td colspan="2" class="questionparameter"><font color="red"><strong>Instructions: </strong></font>To add a tissue or cell type, enter the tissue/cell type name in the first text input field, <i><b>Add Tissue/Cell Type</b></i>.  The last field, <b><i>Add. Info. for Added Tissue/Cell Type</i></b> allows you to enter additional info and notes about the new Tissue/Cell Type. Any new entries will be reviewed.</td></tr>
	<tr>
<?php if($priv_level == 99){
?>
<tr>
<td class="questionparameter" ><strong>Delete Tissue/Cell Type:</strong></td>
<td class="results">
<?php echo $tissueMenu; ?>
</td>
</tr>
<?php
}
?>
<tr>
<td class="questionparameter" ><strong>Add Tissue/Cell Type:</strong></td>
<td class="results">
<input name="newtissue" type="text" align="right"></input>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Add. Info. for Added Tissue/Cell Type:</strong></td>
<td class="results">
<input name="addinfo" type="text" align="right" size="40" maxlength="256"></input>
</td>
</tr>
<tr>
<td><input type="submit" name="submit" value="Submit Tissue Change"></td>
<td><input type="reset" value="Reset Tissue Form"></td>
</tr>
</table>
</form>

<! -- do the actual work in the database here -->
<?php
	}  // end if not submitted....
	else{
		// What value is to be updated.....
		if($tissuesubmit == "true"){

			// Following 2 vars used for determining whether a tissue has been added or deleted.
		$tissueadded = 0;
		$tissuedeleted = 0;

		if($tissue != 0){
			$tissueSQL = "SELECT tissue FROM tissue WHERE tissueid = $tissue";
			$tissueResult = $db->Execute($tissueSQL);//mysql_query($tissueSQL, $db);
			$row = $tissueResult->FetchRow();//mysql_fetch_row($tissueResult);
			$deltissue = $row[0];
			$deltissueSQL = "DELETE FROM tissue WHERE tissueid = $tissue";
			$deltissueResult = $db->Execute($deltissueSQL);//mysql_query($deltissueSQL, $db);
			$tissuedeleted = 1;
		}
		if($newtissue != ""){
			$addtissueSQL = "INSERT tissue (tissue, addinfo) VALUES (\"$newtissue\", \"$addinfo\")";
			$row = $db->Execute($addtissueSQL);//mysql_query($addtissueSQL, $db);
			$tissueadded = 1;
		}
		?>

			<table class="question" width="400">
				<thead>
				<tr>
				<th class="mainheader" colspan="2">Tissue Changes Made</th>
				</tr>
				</thead>
				<tr>
				<td class="questionparameter" ><strong>Tissue Deleted:</strong></td>
				<td class="results">

	<?php
						if($tissuedeleted == 1){
							echo "Deleted $deltissue";
						}
						else{
							echo "None";
						}
					?>
				</td>
				</tr>
				<td class="questionparameter" ><strong>Tissue Added:</strong></td>
				<td class="results">
					<?php
						if($tissueadded == 1){
							echo "Added $newtissue";
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
