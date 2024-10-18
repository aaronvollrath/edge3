<?php


?>

<tr>
<td  class="questionanswer" colspan="3"><strong>Gene Expression Filters:</strong></td>
</tr>
<!--
<tr>
	<td class="results" colspan='3'>
<input dojoType="dijit.form.RadioButton" type="radio" id="foldchange" name="filter" value="1" checked onclick="hideFilter(0)"> Fold Change Filter<img id="foldChangeFilter" src="./images/dialog-information12x12.png" align="right"/><div dojoType="dijit.Tooltip" connectId="foldChangeFilter"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Choosing your Filtering Method</u></strong></td></tr><tr><td>The <strong><font color="blue">Fold-Change Filter</font></strong> allows you to filter out genes that fall within your selected range.</td></tr><tr><td>The <strong><font color="red">Standard Deviation</font></strong> filter allows you to filter out those genes that fall within the number of standard deviations you select.</td></tr></table></div>
<br><input dojoType="dijit.form.RadioButton" type="radio" id="sd" name="filter" value="0" onclick="hideFilter(1)">Standard Deviation Filter<br>
</td>
</tr>
-->
<tr id="foldchangerow1">
<td class="questionparameter" ><strong>Minimum Induction:</strong></td>
<td class="results">
<?php

	if(isset($_GET['savedquery'])){
		$oval = $savedvals['rval'];
		$mval = $savedvals['rvalmax'];
	}
	else{
		$oval = 3;
		$mval = "";
	}
?>
<input size="4" name="rval" type="text" value="<?php echo $oval; ?>" align="right"></input> to maximum of
<input size="4" name="rvalmax" type="text" value="<?php echo $mval; ?>" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be positive.
</td>
</tr>


<tr id="foldchangerow2">
<td class="questionparameter" ><strong>Minimum Repression:</strong></td>
<td class="results">
<?php

	if(isset($_GET['savedquery'])){
		$oval = $savedvals['lval'];
		$mval = $savedvals['lvalmin'];
	}
	else{
		$oval = -3;
		$mval = "";
	}
?>
<input size="4" name ="lval" type="text" value="<?php echo $oval; ?>" align="right"></input>
to minimum of
<input size="4" name="lvalmin" type="text" value="<?php echo $mval; ?>" align="right"></input>


</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be negative.
</td>
</tr>
<!--
<tr id="sdrow1">
<td class="questionparameter" ><strong>Standard Deviation:</strong></td>
<td class="results">
<?php

	if(isset($_GET['savedquery'])){
		#$oval = $savedvals['rval'];
		#$mval = $savedvals['rvalmax'];
	}
	else{
		#$oval = 3;
		#$mval = "";
	}
?>
<input size="4" name="sdval" type="text" value="2" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be positive.
</td>
</tr>

-->
<tr>
<td  class="questionanswer" colspan="3"><strong>Signal Threshold Values:</strong></td>
</tr>
<tr>
<tr>
<td class="questionparameter" ><strong>Green Processed Signal:</strong></td>
<td class="results">
<?php

	if(isset($_GET['savedquery'])){
		$gprocessedval = $savedvals['gprocessedsignal'];
		//$mval = $savedvals['rvalmax'];
	}
	else{
		$gprocessedval = 100;
	}

?>
<input size="4" name="gprocessedsignal" type="text" value="<?php echo $gprocessedval; ?>" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be positive.
</td>
<tr>
<td class="questionparameter" ><strong>Red Processed Signal:</strong></td>
<td class="results">
<?php
	if(isset($_GET['savedquery'])){
		$rprocessedval = $savedvals['rprocessedsignal'];
		//$mval = $savedvals['rvalmax'];
	}
	else{
		$rprocessedval = 100;
	}
?>
<input size="4" name="rprocessedsignal" type="text" value="<?php echo $rprocessedval; ?>" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be positive.
</td>
</tr>

<td class="questionparameter" ><strong>p-Value Cutoff:</strong></td>
<td class="results">

<?php
	if(isset($_GET['savedquery'])){
		$pval = $savedvals['pValue'];
		//$mval = $savedvals['rvalmax'];
	}
	else{
		$pval = .01;
	}
?>

<input size="4" name="pValue" type="text" value="<?php echo $pval; ?>" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>1 &le p-Value &lt 0.
</td>
</tr>
<?php
	require('./phpinc/commonheatmapoptionscode.inc.php');

?>

<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td><input type="reset" value="Reset Form"></td>
</tr>

</table>

</form>
</p>

