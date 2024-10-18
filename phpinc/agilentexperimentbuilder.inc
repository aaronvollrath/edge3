<?php
	echo "This section allows you to build an experiment by adding your arrays to a pre-defined experiment!<br>";

//analyze($_POST);
if(count($_POST) > 1){
	if(isset($_POST['selectedBuilderExperiment'])){
		$selectedBuilderExperiment = $_POST['selectedBuilderExperiment'];
	}else{
		$selectedBuilderExperiment = "";
	}
	if(isset($_POST['arrayoption'])){
		$arrayoption = $_POST['arrayoption'];
	}else{
		$arrayoption = "";
	}
	if(isset($_POST['arrayoption'])){
		$arrayoption = $_POST['arrayoption'];
	}else{
		$arrayoption = "";
	}
	if(isset($_POST['expArrayList'])){
		$expArrayList = $_POST['expArrayList'];
	}else{
		$expArrayList = "";
	}
	if(isset($_POST['submit'])){
		$submit = $_POST['submit'];
	}else{
		$submit = "";
	}



}
else{
	$submit = "";

}
		if($submit == "expBuilderSubmit"){


		$newlist = explode(",", $expArrayList);
		$newlist2 =explode(",", $expArrayList);
		$sizeofarray = count($newlist);
		//echo "<hr>";
		//echo "<br>Size of new array list: $sizeofarray<br>";
		//echo "new array list values: <br>";
		for($i = 0; $i < $sizeofarray; $i++){

			//echo $newlist[$i]."<br>";

		}

		//echo "<hr>";
		// Delete the original arrays from database where expid = $selectedBuilderExperiment....
		$arraySQL = "DELETE FROM agilent_experiments WHERE expid = $selectedBuilderExperiment";

		$originalResult = $db->Execute($arraySQL);//mysql_query($arraySQL, $db);


		// Insert the new arrays into the database....
		foreach($newlist as $arrayval){
			$insSQL = "INSERT agilent_experiments(chemid, arrayid, expid) VALUES (0, $arrayval, $selectedBuilderExperiment)";
			//echo "$insSQL<BR>";
			$trxResult = $db->Execute($insSQL);//mysql_query($insSQL, $db);
		}


}
?>
<form id="expbuilder" name="expbuilder" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onSubmit="return submitExpBuilderForm()">
<h1>Experiment Builder</h1>
<table><tbody>
<tr valign="top">
<td>
Which Experiment?<br>
<?php
// This menu is built in agilentexperiment-useradmin.php file
echo "$expBuilderMenu";

# the function GetAllSourceItems is in the file: expbuilderform.js
?>
</td>
<td>

<input dojoType="dijit.form.RadioButton" id="dispAllArrays" name="arrayoption"
           value="1" type="radio" onClick="return GetAllSourceItems(this.value)"/>
           <label for="dispAllArrays"> Display all available arrays </label>
<br>
    <input dojoType="dijit.form.RadioButton" type="radio" id="dispMyArrays"  name="arrayoption"
           value="2" onClick="return GetAllSourceItems(this.value)"/>
           <label for="dispMyArrays"> Display all of my arrays </label>
<br>
    <input dojoType="dijit.form.RadioButton"  id="dispMyArraysNoExp"  name="arrayoption"
           value="3" type="radio" checked="true" onClick="return GetAllSourceItems(this.value)"/>
           <label for="dispMyArraysNoExp"> Display only my arrays not assigned to an experiment already </label>
</td>
<td>
<input type="hidden" name="expArrayList" value="">
</td>
</tr>


<tr valign="top">
<td>
Experiment: <font color="red"><strong><span id="numExpItems">0</span></strong></font> Arrays Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="cart" class="target" accept="item" id="target1">
-->
<div id="target1" class="target">

</div>
</td>

<td>
Arrays: <font color="red"><strong><span id="numArrayItems">0</span></strong></font> Arrays Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="shelf" class="source" id="source1" accept="item" singular=false>
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


