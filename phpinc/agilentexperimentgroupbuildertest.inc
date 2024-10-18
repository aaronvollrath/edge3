<?php
	echo "This section allows you to build an experiment group by adding your experiments to a pre-defined experiment group!<br>";


?>
<form id="expbuilder" name="expbuilder" action="./testbuilder.php" method="post" onSubmit="return submitExpBuilderForm()">
<h1>Experiment Builder</h1>
<table><tbody>
<tr valign="top">
<td>
Which Experiment?<br>
<?php
echo "$expBuilderMenu";
?>
</td>
<td>

<input dojoType="dijit.form.RadioButton" id="dispAllArrays" name="arrayoption"
           value="1" type="radio" onClick="return GetAllSourceItems(this.value)"/>
           <label for="dispAllArrays"> Display all available arrays? </label>
<br>
    <input dojoType="dijit.form.RadioButton" type="radio" id="dispMyArrays"  name="arrayoption"
           value="2" onClick="return GetAllSourceItems(this.value)"/>
           <label for="dispMyArrays"> Display all of my arrays? </label>
<br>
    <input dojoType="dijit.form.RadioButton"  id="dispMyArraysNoExp"  name="arrayoption"
           value="3" type="radio" checked="true" onClick="return GetAllSourceItems(this.value)"/>
           <label for="dispMyArraysNoExp"> Display only my arrays not assigned to an experiment already? </label>
</td>
<td>
<input type="hidden" name="expArrayList" value="">
</td>
</tr>


<tr valign="top">
<td>
Experiment: <font color="red"><strong><span id="numExpItems">0</span></strong></font> Arrays Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="cart" class="target" accept="item,group" id="target1">
-->
<div id="target1" class="target" height="800">

</div>
</td>

<td>
Arrays: <font color="red"><strong><span id="numArrayItems">0</span></strong></font> Arrays Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="shelf" class="source" id="source1" accept="item,group" singular=false>
-->
<div id="source1" class="source">

</div>
</td>


</tr><tbody/></table>
<!--
<button dojoType="dijit.form.Button" type="submit" id="expBuilderSubmit" value="expBuilderSubmit">Submit</button>
-->
<input type="submit" name="submit" value="expBuilderSubmit"></td>
</form>


