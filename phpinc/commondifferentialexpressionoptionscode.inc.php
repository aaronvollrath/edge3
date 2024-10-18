<p class="styletext">


<form name="query" method="post" onsubmit="" action="<?php  $_SERVER['PHP_SELF'] ?>">

<table class="question" width="700px">

<tr>
<th colspan="3"><font><b>Query Parameters</b></font></th>
</tr>
<tr>
<td  class="questionanswer" colspan="2"><strong>Cluster By:</strong></td>
<td  class="questionanswer"><strong>Your Query Options:</strong></td>
</tr>

<tr id="ordergroups1">
<td class="questionparameter"><strong>Number of Groups:</strong></td> 
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

?>
<img id="ordergrouphelpicon" src="./images/dialog-information12x12.png" align="right"/ ><input name="numberGroups" type="text" value="<?php echo $oval; ?>" size="3" align="right">
<div dojoType="dijit.Tooltip" connectId="ordergrouphelpicon"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Number of Ordered Groups.</u></strong></td></tr><tr><td><p>This field allows you to select the number of distinct groups (i.e., RNA samples, including a reference if applicable) for the ordering of your arrays. </p></td></tr></table></div>
</td>
<td valign="top" class="results" rowspan="1">
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
										onClick="queryTempLoad()">
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

</table>
