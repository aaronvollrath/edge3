<p class="styletext">

<?php
	#we need to distinguish between selected and regular clustering form....
	$formtype = 0;  # default for standard clustering....
	if(isset($_GET['selectedclonesclusteringmodule'])){
			if($_GET['selectedclonesclusteringmodule']==1  && !isset($_POST['submit'])){
				$formtype = 1; # change to 1 for selected.
			}
	}
?>
<form name="query" method="post" onsubmit="return checkClusteringForm(<?php echo $formtype; ?>)" action="<?php  $_SERVER['PHP_SELF'] ?>">

<table class="question" width="700px">
<thead>
<tr>
<th colspan="2"><b>Query Parameters</b></th>
<th></th>
</tr>
<tr>
<td  class="questionanswer" colspan="2"><strong>Cluster By:</strong></td>
<td  class="questionanswer"><strong>Your Query Options:</strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Clustering Method:</strong></td>

<?php



	// what algo is checked???
	if(isset($_GET['savedquery'])){

		if($savedvals['clusterAlgo'] == 1){
			$hierChecked = "checked";
			$kChecked = "";
		}
		else{
			$hierChecked = "";
			$kChecked = "checked";
		}
	}
	else{
		$hierChecked = "checked";
		$kChecked = "";
	}
?>
<td class="results">
<input dojoType="dijit.form.RadioButton" type="radio" id="hier" name="clusterAlgo" value="1" <?php echo $hierChecked; ?> onclick="return hideTrxRow(0)"> Hierarchical<img id="clusterSelection" src="./images/dialog-information12x12.png" align="right"/><div dojoType="dijit.Tooltip" connectId="clusterSelection"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Choosing your Clustering Method</u></strong></td></tr><tr><td>The <strong><font color="blue">Hierarchical clustering</font></strong> algorithm will independently cluster your genes and treatments based on a correlation-associated metric</td></tr><tr><td>The <strong><font color="red">k-Means</font></strong> algorithm allows you to select the number of clusters to place your genes into based on their similarity in gene expression.</td></tr></table></div>
<br><input dojoType="dijit.form.RadioButton" type="radio" id="kmeans" name="clusterAlgo" value="0" <?php echo $kChecked; ?> onclick="return hideTrxRow(1)">K-Means<br>
</td>
<td valign="top" class="results" rowspan="3">
  <div id="toolbar1" dojoType="dijit.Toolbar" style="width:150px;"><button dojoType="dijit.form.ComboButton" iconClass="queryMenuIcon"
								optionsTitle='load options'
								onClick='' id="loadquery">
								<span><strong><font color="blue">Load Query Menu</font></strong></span>

								<div dojoType="dijit.Menu" id="loadMenu" style="display: none;">
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
<tr><TD class="questionparameter" ><strong>Clustering Output method (Experimental):</strong></TD><td class="results"><input dojoType="dijit.form.RadioButton" type="radio" id="rclustonly" name="clustoption" value="1" <?php echo ""; ?> onclick="">R Clustering Only<img id="clusteroptionsSelection" src="./images/dialog-information12x12.png" align="right"/><div dojoType="dijit.Tooltip" connectId="clusteroptionsSelection"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Choosing your Clustering Output Method</u></strong></td></tr><tr><td>The <strong><font color="blue">R Clustering Only</font></strong> selection will use an R-based clustering method (i.e., heatmap.2 from gplots module).</td></tr><tr><td>The <strong><font color="green">Built-In Only</font></strong> selection will only utilize the built-in clustering algorithm.</td></tr><tr><td><strong><font color="red">Note:</font></strong> Currently this feature only supports Hierarchical clustering.  Choosing both will use both.  The Built-in generated image will be displayed and a link to the R-based image will be provided </td></tr></table></div>
<br><input dojoType="dijit.form.RadioButton" type="radio" id="builtinonly" name="clustoption" value="2" <?php echo "checked"; ?> onclick="">Built-In Only
<br><input dojoType="dijit.form.RadioButton" type="radio" id="bothoptions" name="clustoption" value="0" <?php echo ""; ?> onclick="">Both Options<br></td></tr>
<?php
	// IF THIS IS A SAVED QUERY, WE'VE GOT TO HAVE A VALUE FOR THIS..
	if(!isset($savedquery)){
		$savedquery = "";
	}
	echo "<input name=\"savedquery\" type=\"hidden\" value=\"$savedquery\">\n";
	// IF A TEMP query's involved, gotta have that....
	if($savedquery != ""){
		//$tempquery = $savedquery;

	}
	echo "<input name=\"tempquery\" type=\"hidden\" value=\"$tempquery\">\n";


	if($priv <=-999){

	// what dataset is checked???
	if(isset($_GET['savedquery'])){

		if($savedvals['dataset'] == 1){
			$notcondChecked = "checked";
			$condChecked = "";
		}
		else{
			$notcondChecked = "";
			$condChecked = "checked";
		}
	}
	else{
		$notcondChecked = "checked";
		$condChecked = "";
	}
?>

	<tr>
<td class="questionparameter" ><strong>Data Options:</strong></td>
<td class="results">
<input dojoType="dijit.form.RadioButton" id="notcondensedoption" type="radio" name="dataset" value="1" <?php echo $notcondChecked; ?>>Not Condensed<img id="dataOptions" src="./images/dialog-information12x12.png" align="right"/><br>

<input dojoType="dijit.form.RadioButton" id="condensedoption" type="radio" name="dataset" value="0" <?php echo $condChecked; ?>><strong><font color="red">Condensed</font></strong><br>
<div dojoType="dijit.Tooltip" connectId="dataOptions"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Choosing your Data Options</u></strong></td></tr><tr><td>The <strong><font color="blue">Not Condensed </font></strong> data option allows you to examine all of the probes on the array independently without any consolidation and averaging of multiple probes on the array with the same annnotation.</td></tr><tr><td>The <strong><font color="red">Condensed</font></strong> data option will only utilize data where multiple probes on the array with the same annotation have been consolidated and the average value of their log ratios has been calculated and transformed into a single fold-change value.</td></tr></table></div>
</td>

</tr>
<?php
	}
	else{
?>
		<tr>
<td class="questionparameter" ><strong>Data Options:</strong></td>
<td class="results">
Using All Non-Control Features
</td>
<input type='hidden' name='dataset' value='1'>
</tr>
<?php
	}
?>

<?php
	// what dataset is checked???
	if(isset($_GET['savedquery'])){
		$kval = $savedvals['number'];
	}
	else{
		$kval = 4;
	}
?>
<tr id="kmeansoption">
<td class="questionparameter"><strong>Number of Clusters:</strong></td>
<td class="results">
<img id="numclustershelpicon" src="./images/dialog-information12x12.png" align="right"/ ><input id="number" name="number" type="text" value="<?php echo $kval; ?>" size="5" align="right">
<div dojoType="dijit.Tooltip" connectId="numclustershelpicon"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Number of Clusters.</u></strong></td></tr><tr><td><p>This field allows you to select the number of clusters you want your genes to be organized into when performing k-Means Clustering.</p></td></tr></table></div>
</td>
<td class="results"><br></td>
</tr>

<tr id="hierarchicaloption">
<td class="questionparameter" ><strong>Cluster Treatments?:</strong></td>
<td class="results">
<?php
// what cluster option is checked???
	#echo "is this a saved query?<br>";
	if(isset($_GET['savedquery'])){
		#echo "trxCluster = ".$savedvals['trxCluster']."<br>";
		if($savedvals['trxCluster'] == 1){
			$clusterChecked = "checked";
			$noclusterChecked = "";
		}
		else{
			$clusterChecked = "";
			$noclusterChecked = "checked";
		}
	}
	else{
		$clusterChecked = "checked";
		$noclusterChecked = "";
	}

?>
<img id="clusteroption" src="./images/dialog-information12x12.png" align="right"/ ><input dojoType="dijit.form.RadioButton" id="clustertrx"  type="radio" name="trxCluster" value="1" onclick="return hideOrderRows(0)" <?php echo $clusterChecked; ?> >Cluster Treatments</input><br><div dojoType="dijit.Tooltip" connectId="clusteroption"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Choosing how to order your treatments when Hierarchically clustering.</u></strong></td></tr><tr><td>The <strong><font color="blue">Cluster Treatments</font></strong> option will cluster the treatments and display the associated dendrogram.</td></tr><tr><td>The <strong><font color="red">Custom Order/Name Treatments (No Clustering)</font></strong> option allows you to bypass the clustering of treatments and will give you the option of giving the treatments custom names of your choosing.</td></tr></table></div>
<?php
/*
<input type="radio" name="trxCluster" value="2" onclick="return hideOrderRows(0)">Cluster, top dendrogram only</input><br>
<input type="radio" name="trxCluster" value="3" onclick="return hideOrderRows(0)">Cluster, bottom dendrogram only</input><br>
*/
?>
<input  dojoType="dijit.form.RadioButton" id="noclustertrx" type="radio" name="trxCluster" value="0" <?php echo $noclusterChecked; ?> onclick="return hideOrderRows(1)">Custom Order/Name Treatments (No Clustering)</input><br>
</td>
<td class="results">

</td>
</tr>

<tr id="orderoption0">
<td  class="questionanswer" colspan="3"><strong>Ordering Of Selections</strong></td>
</tr>
<tr id="orderoption1">
<?php
// what cluster option is checked???
	if(isset($_GET['savedquery'])){

		if($savedvals['orderoptions'] == 0){
			$defaultChecked = "checked";
			$nodefaultChecked = "";
		}
		else{
			$defaultChecked = "";
			$nodefaultChecked = "checked";
		}
	}
	else{
		$defaultChecked = "checked";
		$nodefaultChecked = "";
	}
?>
<td class="questionparameter"><strong>Ordering/Naming Options:</strong></td>
<td class="results">
<img id="orderoption" src="./images/dialog-information12x12.png" align="right"/ ><br><input dojoType="dijit.form.RadioButton"  type="radio" id="defaultordering" name="orderoptions" value="0" <?php echo $defaultChecked; ?> onclick="return hideNumGroups(0)">Default Ordering (by Array ID) w/o Custom Names<br>
<?php
//<input dojoType="dijit.form.RadioButton" type="radio" id="customordering" name="orderoptions" value="1" onclick="return hideNumGroups(0)">Individually Order/Name Selections<br>

?>
<input dojoType="dijit.form.RadioButton" type="radio" id="customordering" type="radio" name="orderoptions" value="2" <?php echo $nodefaultChecked; ?> onclick="return hideNumGroups(1)">Custom Order/Name

<div dojoType="dijit.Tooltip" connectId="orderoption"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Ordering of Selections.</u></strong></td></tr><tr><td><p>The <font color='blue'><strong>Default Ordering (by Array ID) w/o Custom Names</strong></font> option will negate the clustering of treatments (i.e, no dendrogram to give an indication of the similarity of treatments) and will order the treatments by their array id #.</p><p>The <font color='red'><strong>Custom Order/Name</strong></font> gives you the ability to order
your Chemical(s)/Condition(s) selections on a subsequent
screen. The number of groups entered will allow you to allot
the treatments to separate groups, thereby segregating them
based on your own discretion.</p></td></tr></table></div>
</td>
<td class="results">

</td>
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
	$minuscontrolchecked = -1;
	// what dataset is checked???
	if(isset($_GET['savedquery'])){
		if(isset($savedvals['minuscontrol'])){
			$minuscontrolchecked = $savedvals['minuscontrol'];
		}else{
			$minuscontrolchecked = -1;
		}
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
</tr>

</table>
