<?php
	echo "This section allows you to build an experiment by adding your arrays to a pre-defined experiment!<br>";



		if($submit == "expGroupBuilderSubmit"){
			echo "Experimntal Group Submitted";
	/*
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

		$originalResult = mysql_query($arraySQL, $db);


		// Insert the new arrays into the database....
		foreach($newlist as $arrayval){
			$insSQL = "INSERT agilent_experiments(chemid, arrayid, expid) VALUES (0, $arrayval, $selectedBuilderExperiment)";
			//echo "$insSQL<BR>";
			$trxResult = mysql_query($insSQL, $db);
		}

		*/
}
?>
<form id="expgroupbuilder" name="expgroupbuilder" action="./agilentexperiment-useradmin.php" method="post" onSubmit="return submitExpGroupBuilderForm()">
<h1>Experiment Builder</h1>
<table><tbody>
<tr valign="top">
<td>
Which Experiment?<br>
<?php
// This menu is built in agilentexperiment-useradmin.php file
echo "$expGroupBuilderMenu";
?>
</td>
<td>

<input dojoType="dijit.form.RadioButton" id="dispAllExperiments" name="expoption"
           value="1" type="radio" onClick="return GetAllExpSourceItems(this.value)"/>
           <label for="dispAllExperiments"> Display all available experiments? </label>
<br>
    <input dojoType="dijit.form.RadioButton" type="radio" id="dispMyExperiments"  name="expoption"
           value="2" onClick="return GetAllExpSourceItems(this.value)"/>
           <label for="dispMyExperiments"> Display all of my experiments? </label>
<br>
<!--
    <input dojoType="dijit.form.RadioButton"  id="dispMyExperimentsNoExp"  name="expoption"
           value="3" type="radio" checked="true" onClick="return GetAllSourceItems(this.value)"/>
           <label for="dispMyExperimentsNoExp"> Display only my experiments not assigned to an experiment group already? </label>
-->
</td>
<td>
<input type="hidden" name="expList" value="">
</td>
</tr>


<tr valign="top">
<td>
Experiment: <font color="red"><strong><span id="numExpGroupItems">0</span></strong></font> Experiments Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="Expcart" class="target" accept="item" id="exptarget1">
-->
<div id="target1" class="target">

</div>
</td>

<td>
Experiments: <font color="red"><strong><span id="numExps">0</span></strong></font> Experiments Listed
<!--
<div dojoType="dojo.dnd.Source" jsId="shelf" class="source" id="expsource" accept="item" singular=false>

<div class="dojoDndItem" dndType="exp" dndData="1" title="Mop3 (also termed as Bmal1 or Arnt3) Conditional (hepatocytes) knock-out Circadian Experiment #1">Mop3 (also termed as Bmal1 or Arnt3) Conditional (hepatocytes) knock-out Circadian Experiment #1</div>

-->

<div id="source1" class="source" dojoType="dojo.dnd.Source">
</div>
</td>


</tr><tbody/></table>
<!--
<button dojoType="dijit.form.Button" type="submit" id="expGroupBuilderSubmit" value="expBuilderSubmit">Submit</button>
-->
<input type="submit" name="submit" value="expGroupBuilderSubmit"></td>
</form>


