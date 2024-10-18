<?php

	# this script is used to delete an rna sample that has not been already associated w/ a previous array.

	
	if(!isset($_GET['sampleid'])){
		die("Invalid or missing parameter.");
		
	}
	$sampleid = $_GET['sampleid'];
	$hybridized = false;
	if(!isset($_POST['commit'])){
		# is this rna sample already associated with an array? if so, the rna sample cannot be deleted.
		$rnasampSQL = "SELECT COUNT(*) FROM agilent_arrayinfo WHERE cy3rnasample = $sampleid OR cy5rnasample = $sampleid";
		$rnasampResult = $db->Execute($rnasampSQL);
		$rnasampcount = $rnasampResult->FetchRow();
		$rnasampcount = $rnasampcount[0];
		
		if($rnasampcount >= 1){
			echo "<br><br><b>You cannot delete this RNA sample because it is associated with one or more arrays.<b>";
			$hybridized = true;
		}else{
			# put the rna sample into provenance table and give the option to restore at this moment.
			$getsampSQL = "SELECT * from agilent_rnasample WHERE sampleid = $sampleid";
			$sampResult = $db->Execute($getsampSQL);
			$samparray = $sampResult->FetchRow();
			$samplename = $samparray[20];
			
			
			$insertSQL = "INSERT INTO agilent_rnasample_provenance (sampleid, organism, rnagroupsize, concentration, strain, genevariation, age, ageunits, sex, tissue, treatment, vehicle, dose, route, dosagetime, duration, harvesttime, doseunits, durationunits, pregnant, samplename, gestationperiod, processed, datesubmitted, dateprocessed, submitter, info, submitterid, queuestatus) VALUES ($samparray[0], $samparray[1], $samparray[2], $samparray[3], $samparray[4], $samparray[5], $samparray[6], $samparray[7], '$samparray[8]', $samparray[9], $samparray[10], $samparray[11], $samparray[12], $samparray[13], \"$samparray[14]\", $samparray[15], \"$samparray[16]\", $samparray[17], $samparray[18], \"$samparray[19]\", \"$samparray[20]\", $samparray[21], $samparray[22], \"$samparray[23]\", \"$samparray[24]\", \"$samparray[25]\", \"$samparray[26]\", $samparray[27], $samparray[28])";
			$db->Execute($insertSQL);
			$delSQL = "DELETE FROM agilent_rnasample WHERE sampleid = $sampleid";
			$db->Execute($delSQL);
		}

		if(!$hybridized){
?>
	<br><br>You've chosen to delete sample,<b><font color='blue'> <?php echo $samplename;?></font></b><br><br>
	
	<form name="deleternasample" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
		<input type="hidden" name="sampleid" value='<?php echo $sampleid; ?>' >
		<table><TR><TD>Second thoughts? Click button to restore.</TD><td><input type='submit' name='commit' value='Restore'></td></TR></table>
	</form> 
<?php
		}
	}else{
		
		
		$sampleid = $_POST['sampleid'];
		$restoreSQL = "INSERT INTO agilent_rnasample SELECT * FROM agilent_rnasample_provenance WHERE sampleid = $sampleid";
		$db->Execute($restoreSQL);
		$delfromprov = "DELETE FROM agilent_rnasample_provenance WHERE sampleid = $sampleid";
		$db->Execute($delfromprov);
		echo "<br><br><h3>Sample restored.</h3><br>";
	}

?>