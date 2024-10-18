<?php

class CondensedClone{
		// This class is used to create an object to contain all the values
		// stored in the annotations table of the edge database for the
		// cloneid entered....
		var $cloneid;
		var $name;
		var $hitdef;
		var $refseq;
		var $locustag;
		var $synonyms;
		var $gi;
		var $asscloneids;
		var $asscloneidsFinalRats;
		var $ccfinalratio;
		var $finalratiototal;
		var $db;
		var $db2;
		var $numgoids;
		var $goidarray;
		var $goidnamearray;
		var $goidtermtypearray;

	function CondensedClone($cloneid, $db,$db2){
		
		$this->cloneid = $cloneid;
		$this->asscloneids = array();
		$sql = "SELECT associd FROM condensedidlookup WHERE cloneid = $this->cloneid ORDER BY cloneid";
		$cloneResult = mysql_query($sql, $db);
		while($row = mysql_fetch_row($cloneResult)){
			$currentid = $row[0];
			array_push($this->asscloneids, $currentid);

		}
		// Now get the data from database...
			$sql = "SELECT * FROM condensedannotations WHERE cloneid = $cloneid";
			$result = mysql_query($sql, $db);
			list($this->cloneid, $this->name, $this->refseq) = mysql_fetch_array($result);
		//$this->displayassclones();
		$this->refseq = trim($this->refseq);
		// Get the hitdef from annotations...
		$sql = "SELECT hit_def, locustag, synonyms, gi FROM annotations WHERE cloneid = $cloneid";
		$result = mysql_query($sql, $db);
		$hit = mysql_fetch_row($result);
		$this->hitdef = $hit[0];
		$this->locustag = $hit[1];
		$this->synonyms = $hit[2];
		$this->gi = $hit[3];
		// Get GO id information....
			if($this->refseq == ""){
				$refseq = " ";
				$this->numgoids = 0;
			}
			else{
				$query = "SELECT locusid, refaccver from loc2ref where refaccver = \"".$this->refseq."\"";
				//echo $query;
				$result = mysql_query($query, $db2)or die("Query 1 Failed!!!");
				$row  = mysql_fetch_row ($result);
				$locid = $row[0];
				$goIDCounter = 0;
				$this->goidarray = array();
				$this->goidnamearray = array();
				$this->goidtermtypearray = array();
				if($locid != ""){
					$query2 = "select goid from loc2go where locusid = $row[0]";
					//echo "<br>$query2<br>";
					//$query2new = $query2.$locID;
					$result2 = mysql_query($query2, $db2) or die("Query 2 Failed!!! <br> $query2");
					while($newrow = mysql_fetch_row($result2)){
						$goID = $newrow[0];
						array_push($this->goidarray, $goID);
						$goIDCounter++;
						$query3 = "select name, term_type from term where acc = \"".$goID."\"";
						//echo "$query3<br>";
						$result3 = mysql_query($query3)	or die("FAILED: .$query3");
						$goNames = "";
						$query3row = mysql_fetch_row($result3);
						array_push($this->goidnamearray, $query3row[0]);
						//echo "$query3row[1]<br>";
						array_push($this->goidtermtypearray, $query3row[1]);
					}
				}
				else{
					$goID = " ";
				}
				$this->numgoids = count($this->goidarray);
			}
	}

	function displayGOinfo(){
			?>
			<table id="results">
			<thead>
			<tr>
				<th class="mainheader" colspan="3" align="left">Gene Ontology Information</th>
			</tr>
			</thead>
			<tr>
				<th class="subhead">Molecular Function</th>
				<th class="subhead">Biological Process</th>
				<th class="subhead">Cellular Component</th>
			</tr>
			<?php

			if(count($this->goidtermtypearray) > 0){
				//echo "goterms exist<br>";
				$counter = 0;
				$idarray = $this->goidarray;
				$namearray = $this->goidnamearray;
				$mfarray = array();
				$bparray = array();
				$ccarray = array();
				foreach($this->goidtermtypearray as $gotermtype){
					//echo "$gotermtype<br>";
					$gostr = "<a href=\"http://www.godatabase.org/cgi-bin/go.cgi?query=$idarray[$counter]\" target=\"_blank$randnum\"  class=\"linkthumb\">$idarray[$counter]:$namearray[$counter]</a>";
					if($gotermtype == "molecular_function"){
						array_push($mfarray,$gostr);
					}
					if($gotermtype == "biological_process"){
						array_push($bparray,$gostr);
					}
					if($gotermtype == "cellular_component"){
						array_push($ccarray,$gostr);
					}

					$counter++;
				}
				$mfcount = count($mfarray);
				$bpcount = count($bparray);
				$cccount = count($ccarray);

				$max = 0;
				if($mfcount >= $bpcount && $mfcount >= $cccount){
					$max = $mfcount;
					//echo "mf = $mfcount<br>";
				}
				elseif($bpcount >= $bpcount && $bpcount >= $cccount){
					$max = $bpcount;
					//echo "bp = $bpcount<br>";
				}else{
					$max = $cccount;
					//echo "cc = $cccount<br>";
				}

				for($it = 0; $it<$max; $it++){
					echo "<tr><td>$mfarray[$it]</td><td>$bparray[$it]</td><td>$ccarray[$it]</td></tr>";
				}





			}
			echo "</table>";
		}


	function setCCFinalRatio($arrayid, $db){
		$sql = "SELECT finalratio FROM condensedhybrids WHERE cloneid = $this->cloneid AND arrayid = $arrayid";
			$result = mysql_query($sql, $db);
			$fr = mysql_fetch_row($result);
			$this->ccfinalratio = round($fr[0],2);
	}

	function displaySeq($db){
			?>
			<table id="results">
			<thead>
			<tr>
				<th class="mainheader" colspan="5" align="left">Sequences of Associated Clones</th>
			</tr>
			</thead>
			<tr>
				<th class="subhead">Clone ID</th>
				<th class="subhead">Direction</th>
				<th class="subhead">MegaBlast against Refseq</th>
				<th class="subhead">MegaBlast against Genbank</th>
				<th class="subhead" align="left">Sequence</th>

			</tr>
			<?php
			foreach($this->asscloneids as $cloneid){
				$_3primeseqarray = array();
				$_5primeseqarray = array();
				// Get the sequence data associated with this clone...
				$sql = "SELECT 3primeseq FROM edge_v1_liver3primeseq WHERE cloneid = $cloneid";
				$seqResult = mysql_query($sql, $db);
				while($row = mysql_fetch_row($seqResult)){
					$seq = $row[0];
					if($seq != ""){
					array_push($_3primeseqarray, $seq);
					}
				}
				$sql = "SELECT 5primeseq FROM edge_v1_liver5primeseq WHERE cloneid = $cloneid";
				$seqResult = mysql_query($sql, $db);
				while($row = mysql_fetch_row($seqResult)){
					$seq = $row[0];
					if($seq != ""){
					array_push($_5primeseqarray, $seq);
					}
				}
				foreach($_3primeseqarray as $seq){
					echo "<tr><td>$cloneid</td><td>3'</td><td align=\"center\"><a href=\"blastthis.php?seq=$seq&annname=$this->name&database=refseq&seqdir=3&cloneid=$this->cloneid\" target=\"_blank\">MegaBlast Refseq?</a></td><td align=\"center\"><a href=\"blastthis.php?seq=$seq&annname=$this->name&database=gbmus&seqdir=3&cloneid=$this->cloneid\" target=\"_blank\">MegaBlast Genbank?</a></td><td>$seq</td></tr>";

				}
				foreach($_5primeseqarray as $seq){
					echo "<tr><td>$cloneid</td><td>5'</td><td align=\"center\"><a href=\"blastthis.php?seq=$seq&annname=$this->name&database=refseq&seqdir=5&cloneid=$this->cloneid\" target=\"_blank\">MegaBlast Refseq?</a></td><td align=\"center\"><a href=\"blastthis.php?seq=$seq&annname=$this->name&database=gbmus&seqdir=5&cloneid=$this->cloneid\" target=\"_blank\">MegaBlast Genbank?</a></td><td>$seq</td></tr>";

				}
			}
			echo "</table>";

		}


	function displayPRC($db){
		// Get the plate, row and column location of this clone...
		$prcstring = "";
		foreach($this->asscloneids as $cloneid){
			$cloneplateSQL = "SELECT prc from cloneinfo WHERE cloneid = $cloneid";
			$cloneplateResult = mysql_query($cloneplateSQL, $db);
			list($prc) = mysql_fetch_array($cloneplateResult);

				$plate = substr($prc, 0, 2);
				//echo "$plate<br>";
				if(!is_numeric($plate)){
					$plate = $prc{0};
					$row = $prc{1};
					$col = substr($prc,2,4);
				}else{
					$row = $prc{2};//substr($prc, 1, 3);
					$col = substr($prc, 3, 4);
				}
				if($cloneid < 0){
					$thiscloneid = $cloneid * -1;
				}
				$prcstring .= "<tr><td>".$cloneid."</td><td>".$plate."</td><td>".$row."</td><td>".$col."</td></tr>";
		}
		?>
			<table id="results">
			<thead>
			<tr>
				<th class="mainheader" colspan="4">Plate Location of Clone</th>
			</tr>
			</thead>
			<tr>
				<th class="subhead">Clone ID</th>
				<th class="subhead">Plate</th>
				<th class="subhead">Row</th>
				<th class="subhead">Column</th>
			</tr>
			<?php
			echo $prcstring;
			?>
			</table>
			<?php


	}
	function displayStatistics(){
		$numids = count($this->asscloneids);
		if($numids > 1){
		$mean = $this->finalratiototal/$numids;
		//echo "final ratio total = $finalratiototal<br>";
		//echo "number of ids = $numids<br>";
		//echo "mean = $mean<br>";
		$variance = 0.0;
		$subtotal = 0.0;
		foreach($this->asscloneidsFinalRats as $val){
			$mult = $val - $mean;
			$mult = $mult * $mult;
			$subtotal += $mult;
			//echo "$subtotal<br>";
		}
		//echo $subtotal;
		$variance = (1/($numids-1)) * $subtotal;
		$sd = sqrt($variance);
		if($mean < 0){
			$mean = round(-1 * exp(-1 * $mean),2);
		}
		else{
			$mean = round(exp($mean),2);
		}
		}else{
			$mean = $this->finalratiototal;
			if($mean < 0){
			$mean = round(-1 * exp(-1 * $mean),2);
			}
			else{
				$mean = round(exp($mean),2);
			}

		}
		//echo "mean now = $mean<br>";
		?>

		<table class="question">
		<tr>
		<th class="mainheader" colspan="2">Simple Statistics Computed From Table Above</th>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Mean</strong></td>
		<td class="questionanswer"> <?php echo $mean; ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Variance:</strong></td>
		<td class="questionanswer"> <?php echo round($variance,2); ?></td>
		</tr>
		<tr class="question">
		<td class="questionparameter"><strong>Standard Deviation:</strong></td>
		<td class="questionanswer"> <?php echo round($sd,2); ?></td>
		</tr>
		<?php

	}

	function displayAssClones($db, $arrayid){
		if($arrayid > 0){
			$this->asscloneidsFinalRats = array();
		}
		foreach($this->asscloneids as $thisid){


				$cloneinfoSQL = "SELECT annname, refseq from annotations where cloneid = $thisid";
				//echo "$cloneinfoSQL<br>";
				$cloneinfoResult = mysql_query($cloneinfoSQL, $db);
				list($name, $ref) = mysql_fetch_array($cloneinfoResult);
				$name = substr($name, 0, 50);
				if($ref != $this->refseq){
					$reftdclass = "questionwronganswer";
					//echo "not equal<br>";
				}else{
					$reftdclass = "results";
					//echo "equal<br>";
				}
				if($arrayid > 0){
					$sql = "SELECT finalratio FROM hybrids WHERE arrayid = $arrayid AND cloneid = $thisid";
				//echo "$sql<br>";
				$cloneResult = mysql_query($sql, $db);
				$row = mysql_fetch_row($cloneResult);
				$finrat = $row[0];
				$log10finrat = round($finrat,2);
				$ratio = $finrat;
				if($finrat >= 1){
					$finrat = log($finrat);
				}
				else{
					$finrat = log(-1/$finrat);
				}
				//echo "finrat = $finrat<br>";
				array_push($this->asscloneidsFinalRats, $finrat);
				$finalratiototal += $finrat;
				}


				$clonelist .= "<tr><td class=\"results\"><a href=\"cloneinfosvgname.php?cloneid=$thisid\" target=\"_blank$randnum\">$thisid</a></td><td class=\"results\">$name</td>
				<td class=\"$reftdclass\"><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$ref\" target=\"_blank$randnum\">$ref</a></td>";
				if($arrayid > 0){
					$clonelist .= "<td class=\"results\">$log10finrat</td>";

				}
				if($_SESSION['username'] == "aaronv"){
					$numcols = 6;
					$clonelist .= "<td class=\"results\"><a href=\"changeclone.php?cloneid=".$thisid."\" target=\"_blank\">".$thisid."</a></td><td class=\"results\"><a href=\"./probeseqs.php?startclone=".$thisid."\" target=\"_blank\">".$thisid."</a></td></tr>";
				}else{
					$clonelist .= "</tr>";
					$numcols = 4;
				}
		}
			$this->finalratiototal = $finalratiototal;
		?>
		<table class="questionclass">
<thead>
<tr>
<th class="mainheader" colspan="<?php echo $numcols; ?>">Clones Used To Calculate Condensed Clone Final Ratio</th>
</tr>
</thead>
<tr>
<td class="questionparameter">Clone ID</td>
<td class="questionparameter">Name</td>
<td class="questionparameter">Refseq</td>
<?php
if($arrayid > 0){
echo "<td class=\"questionparameter\">Final Ratio</td>";
}
if($_SESSION['username'] == "aaronv"){
?>
<td class="questionparameter">Edit Clone Association</td><td class="questionparameter">Probe this Sequence?</td>
<?php
}
?>
</tr>

<?php
echo $clonelist;

?>
</table>

<?php
}


	function displayInfo($tableclass, $tdclassparameter, $tdclassresult, $width){
		echo "<table class=\"$tableclass\" width=\"$width\">";
			echo "<tr>
<th class=\"mainheader\" colspan=\"2\" ><strong>Condensed Clone Information:</strong></th>
</tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Clone ID</strong></td><td class=\"$tdclassresult\">$this->cloneid</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Name</strong></td><td class=\"$tdclassresult\">$this->name</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Refseq</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$this->refseq\" target=\"_blank\">$this->refseq</a></td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Description</strong></td><td class=\"$tdclassresult\">$this->hitdef</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Locus Tag</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.informatics.jax.org/searches/accession_report.cgi?id=$this->locustag\" target=\"_blank\">$this->locustag</a></td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Synonyms</strong></td><td class=\"$tdclassresult\">$this->synonyms</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>GI</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=Nucleotide&list_uids=$this->gi&dopt=graph\"  target=\"_blank\">$this->gi</a></td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Final Ratio</strong></td><td class=\"$tdclassresult\">$this->ccfinalratio</td></tr>";
			echo "</table>";

	}
	function displaynameInfo($tableclass, $tdclassparameter, $tdclassresult, $width){
		echo "<table class=\"$tableclass\" width=\"$width\">";
			echo "<tr>
<th class=\"mainheader\" colspan=\"2\" ><strong>Condensed Clone Information:</strong></th>
</tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Clone ID</strong></td><td class=\"$tdclassresult\">$this->cloneid</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Name</strong></td><td class=\"$tdclassresult\">$this->name</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Refseq</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$this->refseq\" target=\"_blank\">$this->refseq</a></td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Description</strong></td><td class=\"$tdclassresult\">$this->hitdef</td></tr>";

			echo "<tr><td class=\"$tdclassparameter\"><strong>Locus Tag</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.informatics.jax.org/searches/accession_report.cgi?id=$this->locustag\" target=\"_blank\">$this->locustag</a></td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Synonyms</strong></td><td class=\"$tdclassresult\">$this->synonyms</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>GI</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=Nucleotide&list_uids=$this->gi&dopt=graph\"  target=\"_blank\">$this->gi</a></td></tr>";
			echo "</table>";

	}

}
?>
