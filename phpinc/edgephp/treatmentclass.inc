<?php
class Treatment{

	var $treatmentid;
	var $chemid;
	var $originator;
	var $originatorOrg;
	var $organism;
	var $rnagroupsize;
	var $concentration;
	var $strain;
	var $genevariation;
	var $age;
	var $sex;
	var $tissue;
	var $treatment;
	var $structureimage;
	var $structurelink;
	var $vehicle;
	var $dose;
	var $route;
	var $dosagetime;
	var $duration;
	var $harvesttime;
	var $control;
	var $doseunits;
	var $durationunits;
	var $pregnant;
	var $gestationperiod;
	var $hybridization;
	var $dateofcompletion;
	var $iskidney;



	function Treatment($arrayid, $db){

		if($arrayid > 10000){
			if($arrayid > 100000){

				// this is a kidney clone... a temporary stop gap until i fix things....
				$arrayidarray = preg_split('//', $arrayid, -1, PREG_SPLIT_NO_EMPTY);
				for($k = 0; $k < 6; $k++){
					// remove the seven zeroes attached to the end...
					array_pop($arrayidarray);
				}
				$arrayid = implode("", $arrayidarray);
				$this->iskidney = "kidney";
  			}



		}
		if($this->iskidney == ""){
			$arrayinfo = "SELECT * from sampledata where sampleid = $arrayid";
		}else{

			$arrayinfo = "SELECT * from kidneyarraydata where sampleid = $arrayid";
		}
		//echo "$arrayinfo<br>";
		$arrayinfoResult = mysql_query($arrayinfo, $db);
		list($this->treatmentid, $this->$chemid, $this->originator, $this->originatorOrg, $this->organism, $this->rnagroupsize, $this->concentration, $this->strain, $this->genevariation, $this->age, $this->sex, $this->tissue, $this->treatment, $this->structureimage, $this->structurelink, $this->vehicle, $this->dose, $this->route, $this->dosagetime, $this->duration, $this->harvesttime, $this->control, $this->doseunits, $this->durationunits, $this->pregnant, $this->gestationperiod, $this->hybridization, $this->dateofcompletion) = mysql_fetch_array($arrayinfoResult);
	}
	function dispTreatmentInfo(){
	?>
		<table>
		<thead>
		<th class="mainheader" colspan="2">Treatment Attributes</th>
		</thead>
		<tr class="question">
		<td class="questionparameter"><strong>Treatment:</strong></td>
		<td class="questionanswer"> <?php echo $this->treatment; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Organism:</strong></td>
		<td class="questionanswer"> <?php echo $this->organism; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Strain:</strong></td>
		<td class="questionanswer"> <?php echo $this->strain; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Genetic Variation:</strong></td>
		<td class="questionanswer"> <?php echo $this->genevariation; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Tissue:</strong></td>
		<td class="questionanswer"> <?php echo "$this->tissue"; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Age:</strong></td>
		<td class="questionanswer"> <?php echo "$this->age"; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Sex:</strong></td>
		<td class="questionanswer"> <?php echo "$this->sex"; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>RNA Group Size:</strong></td>
		<td class="questionanswer"> <?php echo "$this->rnagroupsize"; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Dose:</strong></td>
		<td class="questionanswer"> <?php echo "$this->dose $this->doseunits"; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Route:</strong></td>
		<td class="questionanswer"> <?php echo "$this->route"; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Dosage Time:</strong></td>
		<td class="questionanswer"> <?php echo $this->dosagetime; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Duration:</strong></td>
		<td class="questionanswer"> <?php echo "$this->duration $this->durationunits"; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Harvest Time:</strong></td>
		<td class="questionanswer"> <?php echo $this->harvesttime; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Vehicle:</strong></td>
		<td class="questionanswer"> <?php echo $this->vehicle; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Completed:</strong></td>
		<td class="questionanswer"> <?php echo "$this->dateofcompletion"; ?></td>
		</tr>
		</table>
	<?php
	}
}
?>
