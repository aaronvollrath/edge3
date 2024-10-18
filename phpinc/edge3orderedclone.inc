<div dojoType="dijit.layout.ContentPane" style="height: 1800px;">

<?php
//analyze($_POST);
if(isset($_POST['orderedformSubmit'])){
	$orderedformSubmit = $_POST['orderedformSubmit'];
}else{
	$orderedformSubmit = false;
}
//echo "<br>ordering method = $orderingMethod<br>";
//echo "<br>ordering method = $orderedformSubmit<br>";
$browserval = 1;
if (isset($_POST['orderedsubmit']) && /*$orderingMethod == 0 || */$orderedformSubmit == "true") { // if form has been submitted and it is not being ordered or orderedformSubmit is true...
	require('./phpinc/commonclusteringsavequerycodeaftersubmittingforclustering.inc.php');

		if(!isset($dataset)){
			$dataset = 1;
		}
		

require('./phpinc/commonfilecreationcode.inc.php');
	//if($orderedformSubmit != "true"){
		// This is not even a viable option given that $orderedformSubmit has to be 'true' to get into this section of code...
	//	include 'orderedoutputnoorderingoftreatmentsedge3.inc';
	//}
	//else{
		/***********************************************************************************
		ORDERED SECTION...............
		***********************************************************************************/
		include 'orderedoutputORDEREDoftreatmentsedge3.inc';
	//}
}
else if(isset($_POST['orderedsubmit'])) {
//analyze($_POST);
require("./phpinc/commonorderingscreencode.inc.php");
}
else{// GETTING THE VALUES TO POPULATE THE SELECTIONS.....
// THIS ONE IS FOR THE PARTICULAR TREATMENTS....

$privval = $_SESSION['priv_level'];

if($privval == ""){
	$priv = 1;
}
else{
	$priv = $privval;
}



require('./phpinc/commonclusteringsavequerycode.inc');
#
#

# in the form, the onsubmit function, checkOrderedForm(), is in formcheck2.js
?>
<p class="styletext">
<form name="queryorder" method="post" onsubmit="return checkOrderedForm();" action="<?php  $_SERVER['PHP_SELF'] ?>">

<table class="question" width="700px">
<thead>
<tr>
<th class="mainheader" colspan="2"><font color="black"><b>Query Parameters</b></font></th>
<th class="mainheader" ></th>
</tr>
<tr>
<td  class="questionanswer" colspan="3"><strong>Saved Query Options:</strong></td>
</tr>
<tr>

<td valign="top" class="results" colspan="3">
  <div id="toolbar1order" dojoType="dijit.Toolbar" style="width:150px;"><button dojoType="dijit.form.ComboButton" iconClass="queryMenuIcon"
								optionsTitle='load options'
								onClick='' id="loadqueryorder">
								<span><strong><font color="blue">Load Query Menu</font></strong></span>

								<div dojoType="dijit.Menu" id="loadMenuorder" style="display: none;">
									<div dojoType="dijit.MenuItem"
										 iconClass="mySavedQueryOpen"
										onClick="querySavedLoad(<?php echo $_SESSION['userid'];?>)">
										Load Saved Query
									</div>
									<div dojoType="dijit.MenuItem"
										 iconClass="myTempQueryOpen"
										onClick="queryTempLoad(<?php echo $_SESSION['userid'];?>)">
										Load Recent Query
									</div>
								</div>
							</button>
			</div>
<div dojoType="dijit.Tooltip" connectId="loadquery"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><b>Click to load a previously executed query.</b><p>  You can load a query saved previously by selecting <font color="blue"><strong>Saved Query</strong></font>.  Additionally, the last three queries that you performed are available when you select  <font color="red"><strong>Load Recent Query.</strong></font></p></tr></td></table> </div>












<br>

</p>
</td>
</tr>
<?php
	if(!isset($savedquery)){
		$savedquery = "";
	}
	if(!isset($tempquery)){
		$tempquery = "";
	}
	// IF THIS IS A SAVED QUERY, WE'VE GOT TO HAVE A VALUE FOR THIS..
	echo "<input name=\"savedquery\" type=\"hidden\" value=\"$savedquery\">\n";
	// IF A TEMP query's involved, gotta have that....
	echo "<input name=\"tempquery\" type=\"hidden\" value=\"$tempquery\">\n";


	if($priv <=999){
?>

<?php
	// what dataset is checked???
	if(isset($_GET['savedquery'])){
		if(isset($savedvals['dataset'])){
			if($savedvals['dataset'] == 1){
				$notcondChecked = "checked";
				$condChecked = "";
			}
			else{
				$notcondChecked = "";
				$condChecked = "checked";
			}
		}else{
		$notcondChecked = "checked";
		$condChecked = "";
		}
	}
	else{
		$notcondChecked = "checked";
		$condChecked = "";
	}
?>


<?php
	}

	else{
?>
		<tr>
<td class="questionparameter" ><strong>Data Options:</strong></td>
<td class="results">
Using All Clones
</td>
<?php
	}
?>

<?php
	// what dataset is checked???
	if(isset($_GET['savedquery'])){
		if(isset($savedvals['number'])){
			$kval = $savedvals['number'];
		}else{
			$kval = 4;
		}
	}
	else{
		$kval = 4;
	}
?>




<tr id="orderoption0">
<td  class="questionanswer" colspan="3"><strong>Ordering Of Selections</strong></td>
</tr>
<tr id="ordergroups1">
<td class="questionparameter"><strong>Number of Ordered Groups:</strong></td>
<td class="results">
<?php
	// what dataset is checked???
	if(isset($_GET['savedquery'])){
		$oval = $savedvals['numberGroups'];
	}
	else{
		$oval = 4;
	}

	// what dataset is checked???
	if(isset($_GET['savedquery'])){
		if(isset($savedvals['minuscontrol'])){
			$minuscontrolchecked = $savedvals['minuscontrol'];
		}else{
			$minuscontrolchecked = -1;
		}
	}else{
		$minuscontrolchecked = -1;
	}
	if($minuscontrolchecked == 1){
		$minuscontrolchecked = "checked";
	}else{
		$minuscontrolchecked = "";
	}
?>
<img id="ordergrouphelpicon" src="./images/dialog-information12x12.png" align="right"/ ><input name="numberGroups" type="text" value="<?php echo $oval; ?>" size="3" align="right"><br><p><strong>Subtract Control Values from other groups?:</strong><input name="minuscontrol" type="checkbox" value="1" align="right" <?php echo $minuscontrolchecked; ?>>Yes</input></p>
<div dojoType="dijit.Tooltip" connectId="ordergrouphelpicon"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Number of Ordered Groups.</u></strong></td></tr><tr><td><p>This field allows you to select the number of distinct groups for the ordering of your arrays.  Additionally, if you are working with a reference sample or a common control, you can choose to subtract the mean value from the control group from all groups.</p></td></tr></table></div>
</td>
<td class="results"></td>
</tr></table>
<?php

	#require('./phpinc/commonarraylayoutselectioncode.inc.php');
	require('./displayexperimentsbygroups.php');
		# this include uses a experiments by groups layout that 
	# doesn't use dojo.  uses bootstrap and more modern approach
	#require('./phpinc/newcommonarraylayoutselectioncode.inc.php');

?>
<!--
<table class="question" width="700px"><tr>
<td  class="questionanswer" colspan="3"><strong>Feature Number List:</strong></td>
</tr>
<tr>
-->
<table class="question" width="700px">
<?php

	require('./phpinc/sharedgenelistcode.inc.php');
	require('./phpinc/commonfeaturenumberloadingcode.inc.php');
	require('./phpinc/commonheatmapoptionscode.inc.php');
?>
	


<tr>
<td><input type="submit" name="orderedsubmit" value="Submit"></td>
<td></td>
<td><input type="reset" value="Reset Form"</td>
</tr>

</table>

</form>

<?php

}


?>
</p>
</div>
