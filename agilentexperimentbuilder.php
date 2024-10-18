<?php
	echo "This section allows you to build an experiment by adding your arrays to a pre-defined experiment!<br>";


?>
<form dojoType="dijit.form.Form" id="expbuilder" name="expbuilder" action="shopping_cart_1.html" method="post" onSubmit="return testexpbuildform()">
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
</td>
</tr>


<tr valign="top">
<td>
Experiment: <span id="numExpItems">0</span> Arrays Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="cart" class="target" accept="item" id="target1">
-->
<div id="target1" class="target">

</div>
</td>

<td>
Arrays: <span id="numArrayItems">0</span> Arrays Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="shelf" class="source" id="source1" accept="item" singular=false>
-->
<div id="source1" class="source">

</div>
</td>


</tr><tbody/></table>

<input type="submit" value="SubmitExp"/>
</form>


