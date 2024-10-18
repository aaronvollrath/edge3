<?php

?>



<tr>

<td  class="questionanswer" colspan="3"><strong>Feature Number List:</strong></td>
</tr>
<tr>

<?php

require('./phpinc/sharedgenelistcode.inc.php');

?>
	<td class="questionparameter"><strong>Selected Feature Numbers:</strong></td>
	<td class="results" style="height:250px">	
	<textarea dojoType="dijit.form.Textarea" id="cloneList" name="cloneList" style="height:250px;overflow-y: scroll !important;min-height:100px !important;
        max-height:250px !important"><?php echo $cloneList; /*echo "$cloneList"."$genelist"; */?></textarea>
	<br>
	<font color="red"><b>NOTE: </b></font>Delimit the clone ids by comma <br>or by entering one cloneid per line.

<td valign="top" class="results" rowspan="2">
  <div id="toolbar2" dojoType="dijit.Toolbar" style="width:150px;"><button dojoType="dijit.form.ComboButton" iconClass="queryMenuIcon"
								optionsTitle='load gene list'
								onClick='' id="loadgenes">
								<span><strong><font color="blue">Load Gene List Menu</font></strong></span>

								<div dojoType="dijit.Menu" id="loadMenu2" style="display: none;">
									<div dojoType="dijit.MenuItem"
										 iconClass="mySavedQueryOpen"
										onClick="queryGenesByUserid(<?php echo $_SESSION['userid'];?>)">
										Load Own Gene Lists
									</div>
									<div dojoType="dijit.MenuItem"
										 iconClass="myTempQueryOpen"
										onClick="queryGenesByPublic()">
										Load Public Gene Lists
									</div>
								</div>
							</button>
			</div>

<div dojoType="dijit.Tooltip" connectId="loadgenes"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><b>Click to import a saved gene list.</b><p>  You can load a gene list that you (your account) saved by selecting <font color="blue"><strong>Load Own Gene Lists</strong></font>, or you can load any public gene list by selecting <font color="red"><strong>Load Public Gene Lists</strong></font>.</p></td></tr></table></div>
</td>
</td>
</tr>
