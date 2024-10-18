<?php

/*
	This form will be used when a user enters a RNA submission.  It will precede the actual
	RNA submission form and will force them to define what the experimental basis for their
	RNA sample is.
*/

require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}

require './phpinc/edge3_db_connect.inc';
require("fileupload-class.php");
include 'edge_update_user_activity.inc';
include 'utilityfunctions.inc';
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
?>


<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="../css/newlayout.css" title="layout" />
<title>EDGE^2</title>
<script type="text/javascript">

String.prototype.trim=function(){
    return this.replace(/^\s*|\s*$/g,'');
}

function updateexpdesc()
  {
	if(document.agilentexp.selectedExperiment.value != -1){
  var xmlHttp;

  // keep the editable boxes disabled for the time being...
  drawbox('newExp',true);
  drawbox('expDesc',true);
	document.agilentexp.editDesc.checked = false;
	document.agilentexp.editDesc.disabled = false;
	document.agilentexp.newExp.value = "";
  try
    {
    // Firefox, Opera 8.0+, Safari
    xmlHttp=new XMLHttpRequest();
    }
  catch (e)
    {
    // Internet Explorer
    try
      {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
      }
    catch (e)
      {
      try
        {
        xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
      catch (e)
        {
        alert("Your browser does not support AJAX!");
        return false;
        }
      }
    }
xmlHttp.onreadystatechange=function()
      {
      if(xmlHttp.readyState==4)
        {
	//alert("Value = " + document.agilentexp.selectedExperiment.value);
	var expDescText = xmlHttp.responseText.trim();
        document.agilentexp.expDesc.value=expDescText;
        }
      }
    xmlHttp.open("GET","updateexpdesc.php?expid=" + document.agilentexp.selectedExperiment.value,true);
    xmlHttp.send(null);
    }else{
    	//alert("newExp selected");
		drawbox('newExp',false);
		drawbox('expDesc',false);
		document.agilentexp.expDesc.value="Enter new experiment information here!";
		document.agilentexp.editDesc.checked = true;
		document.agilentexp.editDesc.disabled = true;

    }

  }




  function drawbox($element,$onoff) // is used to enable/disable a textbox based on radio-buttons clicked
	{
		//alert("in drawbox");
		document.getElementById($element).disabled = $onoff;
	}

 function editExpDesc($element) // is used to enable/disable a textbox based on radio-buttons clicked
	{
		//alert("in editDesc");
		if(document.getElementById($element).checked == true){
			// Turn off editing of description...
			document.agilentexp.expDesc.disabled = false;

		}else{
			document.agilentexp.expDesc.disabled = true;
		}
	}
</script>
</head>

<body>

	<div class="boxheader">
		<img src="../GIFs/EDGE2128x60.png" alt="Edge^2" align="left"></img>
		<img src="../GIFs/edgebanner.jpg" alt="environment" width="90" height="75" align="left"></img>
		<h2 class="bannerhead" align="bottom">Environment, Drugs and Gene Expression</h2>
	</div>

 <div class="boxmiddle">

 <?php
include 'questionmenu.inc';



// Get the data to populate the experiment name field:

$expSQL = "SELECT expid, expname, descrip FROM agilent_experimentsdesc ORDER BY expid";
		$expResult = mysql_query($expSQL, $db);
		$firstchoice = 1;
		$expMenu = "<select name=\"selectedExperiment\" onChange=\"updateexpdesc()\">";
		$expDesc = "none chosen";
		while($row = mysql_fetch_array($expResult))
		{
			$expname = $row[1];
			if($firstchoice == 1){
				$expMenu .= "<option value=\"$row[0]\" checked>$expname</option>  ";
				$expDesc = $row[2];
				$firstchoice = 0;
			}
			else{
				$expMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\">$expname</option>  ";
			}
		}
		$expMenu .= "<option value=\"-1\">New Experiment</option></select>";





?>


	<h3 class="contenthead">Enter a new treatment</h3>
<?php

if($expsubmitted != true){
?>
<p>
<b>Instructions:</b> This form is used to associate an experimental basis for your RNA sample submission and must be completed for <font color="red"><b>EACH</b></font> (distinct) RNA sample that you send.  Thanks!<br>
</p>
<form enctype="multipart/form-data" name="agilentexp" action="newagilentexperiment.php" method="post" onsubmit="return checkForm()">

<input type="hidden" name="expsubmitted" value="true">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2">New Treatment Form <font color="red"><strong>&nbsp&nbsp &nbsp&nbsp(*=Text Required)</strong></font><font color="blue"><strong>&nbsp&nbsp &nbsp&nbsp(#= Number Required)</strong></font></th>
</tr>
</thead>
<tr>
<td class="questionparameter" ><strong>Experiment Name:</strong></td>
<td class="results">
<?php
echo "$expMenu";

?>
<input name='newExp' id='newExp' type="text" value="" align="left" disabled=true></input>
<font color="red"><strong>*</strong></font>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Experiment Description:</strong></td>
<td class="results">

<textarea name="expDesc" id="expDesc" cols='100' rows='30' align="left" disabled=true>
<?php
echo $expDesc;
?>
</textarea>
<font color="red"><strong>*</strong></font>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Edit Description:</strong></td>
<td class="results">
<input type='checkbox' name='editDesc' id='editDesc' enabled onClick="return editExpDesc('editDesc')">Edit Description?</input><br> (NOTE: If you make changes and uncheck the box, the changes will not be acknowledged.)
</td>
</tr>

<tr>
<td><input type="submit" name="submit" value="ExpSubmit"></td>
<td><input type="reset" value="Reset Form"></td>
</tr>


</table>
</form>
<?php
}else{

//echo "Form submitted...";

analyze($_POST);
$experimentID = "";
$experimentName = "";
$experimentDesc = "";

if($selectedExperiment == -1){
	// A new experiment has been entered...
	//echo "<br>$_POST[expDesc]<br>";
	$experimentName = $newExp;
	$experimentDesc = $_POST[expDesc];
	// get a new id based on the next id available in agilent_experimentsdesc
	$countSQL = "Select MAX(expid) from agilent_experimentsdesc";
		$countResult = mysql_query($countSQL, $db);
		$row = mysql_fetch_row($countResult);
		$maxsampleID = $row[0];
		if(is_nan($maxsampleID)){
			$maxsampleID = 0;
		}
		$experimentID = $maxsampleID + 1;
?>
<p>
<b>Instructions:</b> This form is used to associate an experimental basis for your RNA sample submission and must be completed for <font color="red"><b>EACH</b></font> (distinct) RNA sample that you send.  Thanks!<br>
</p>
<form enctype="multipart/form-data" name="agilentexp" action="agilentqueueentry-nonadmin.php" method="post">

<input type="hidden" name="expsubmitted" value="true">
<input type="hidden" name="expID" value="<?php echo $experimentID; ?>">
<input type="hidden" name="expNew" value="true">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2"><font color="red"><strong>Please check for errors!</strong></font></th>
</tr>
</thead>
<tr>
<td class="questionparameter" ><strong>New Experiment ID:</strong></td>
<td class="results">
<?php
	echo "$experimentID";
?>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Experiment Name:</strong></td>
<input type="hidden" name="expDescName" value="<?php echo "$experimentName"; ?>">
<td class="results">
<?php
	echo "$experimentName";
?>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Experiment Description:</strong></td>
<td class="results">
<input type="hidden" name="expDescValue" value="<?php echo "$_POST[expDesc]"; ?>">
<textarea cols='40' rows='6' align="left" readonly>
<?php
echo $experimentDesc
?>
</textarea>
</td>
</tr>
<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td><input type="button" value="Incorrect Values...Fix Them!" onClick="history.go(-1)"></td>
</tr>


</table>
</form>


<?php
}else{
	$expDescChanged = false;
	// check to see if any changes were made to a previous description....
	$expSQL = "SELECT * FROM agilent_experimentsdesc WHERE expid = $selectedExperiment";
	$countResult = mysql_query($expSQL, $db);
	$row = mysql_fetch_row($countResult);
	$experimentID = $row[0];
	$experimentName = $row[1];
	$oldExperimentDesc = $row[2];

	if($editDesc == on){
		$expDescChanged = true;
		$expDescVal = $_POST[expDesc];
	}else{
		$expDescVal = $oldExperimentDesc;

	}
?>
<p>
<b>Instructions:</b> This form is used to associate an experimental basis for your RNA sample submission and must be completed for <font color="red"><b>EACH</b></font> (distinct) RNA sample that you send.  Thanks!<br>
</p>
<form enctype="multipart/form-data" name="agilentexp" action="agilentqueueentry-nonadmin.php" method="post">

<input type="hidden" name="expsubmitted" value="true">
<input type="hidden" name="expID" value="<?php echo $experimentID; ?>">
<input type="hidden" name="expDescChanged" value="<?php echo $expDescChanged; ?>">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="3"><font color="red"><strong>Please check for errors!</strong></font></th>
</tr>
</thead>

<tr>
<td class="questionparameter" ><strong>New Experiment ID:</strong></td>
<td class="results" colspan='2'>
<?php
	echo "$experimentID";
?>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Experiment Name:</strong></td>
<td class="results" colspan='2'>
<?php
	echo "$experimentName";
?>
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Experiment Description:</strong></td>
<input type="hidden" name="expDescValue" value="<?php echo $expDescVal; ?>">

<td class="results">
<strong>Current version:<br></strong>
<textarea cols='40' rows='6' align="left" readonly>
<?php
echo $oldExperimentDesc;
?>
</textarea>
</td>
<?php
if($expDescChanged == true){
?>
<td class="results">
<strong>New version:<br></strong>
<textarea cols='40' rows='6' align="left" readonly>
<?php
echo $_POST[expDesc];
?>
</textarea>
</td>
</tr>
<?php

}else{

	echo "<td></td>";
}
?>
<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td><input type="button" value="Incorrect Values...Fix Them!" onClick="history.go(-1)"></td>
</tr>


</table>
</form>


<?php



}




}
?>
</div>
<?php
	include './leftmenu.inc';

 ?>


 <div class="boxclear"> </div>




 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>

</body>
</html>





















