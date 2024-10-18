<?php
class UncondensedClone{
		// This class is used to create an object to contain all the values
		// stored in the annotations table of the edge database for the
		// cloneid entered....
		var $cloneid;
		var $name;
		var $refseq;
		var $refseqhit;
		var $hit_def;
		var $locustag;
		var $synonyms;
		var $gi;
		var $blast_source;
		var $blast_source_date;
		var $bit_score;
		var $e_value;
		var $seqdirused;
		var $datemodified;
		var $comments;
		var $prc;
		var $_3primeseqarray;
		var $_5primeseqarray;
		var $numgoids;
		var $goidarray;
		var $goidnamearray;
		var $goidtermtypearray;
		var $iskidney;


		function UncondensedClone($cloneid,$db,$db2){
			if($cloneid > 10000){

			// this is a kidney clone... a temporary stop gap until i fix things....
				$kcount = 0;

			 $cloneid = implode("", $cloneidarray);
			 $this->iskidney = "kidney";
  			}
			// Now get the data from database...
			$sql = "SELECT * FROM ". $this->iskidney."annotations WHERE cloneid = $cloneid";
			//echo $sql;
			$result = mysql_query($sql, $db);
			list($this->cloneid, $this->name, $this->refseq, $this->refseqhit, $this->hit_def, $this->locustag, $this->synonyms, $this->gi, $this->blast_source, $this->blast_source_date,$this->bit_score, $this->e_value, $this->seqdirused, $this->datemodified,$this->comments) = mysql_fetch_array($result);

			// Clean up annotated name if it contains >, < symbols or "
			$this->name = str_replace(">", "", $this->name);
			$this->name = str_replace("<", "", $this->name);
			$this->name = str_replace("\"","", $this->name);
			if($this->iskidney == ""){
			// Get the plate, row and column location of this clone...
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
				$this->prc = "<tr><td>".$this->cloneid."</td><td>".$plate."</td><td>".$row."</td><td>".$col."</td></tr>";

			$this->_3primeseqarray = array();
			$this->_5primeseqarray = array();
			// Get the sequence data associated with this clone...
			$sql = "SELECT 3primeseq FROM edge_v1_liver3primeseq WHERE cloneid = $cloneid";
			$seqResult = mysql_query($sql, $db);
			while($row = mysql_fetch_row($seqResult)){
				$seq = $row[0];
				if($seq != ""){
				array_push($this->_3primeseqarray, $seq);
				}
			}
			$sql = "SELECT 5primeseq FROM edge_v1_liver5primeseq WHERE cloneid = $cloneid";
			$seqResult = mysql_query($sql, $db);
			while($row = mysql_fetch_row($seqResult)){
				$seq = $row[0];
				if($seq != ""){
				array_push($this->_5primeseqarray, $seq);
				}
			}

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
			//echo "Num goids = $this->numgoids<br>";
			}
		}

		function segmentString($string, $sublength){
			$length = strlen($string);
			//$sublength = 50;
			$rows = $length/$sublength;
			$stringout = "";
			$lastval = 0;
			for($i = 0; $i < $rows; $i++){
				$seqstr = "";
				$seqstr = substr($string, $lastval, $sublength);
				//echo "substr ($, $lastval, $sublength)";
				$stringout .= "$seqstr"."<br>";
				//echo "$seqout";
				$lastval = $lastval + $sublength;
			}
			return $stringout;
		}
		function displayProvenance($db){
		//echo "in displayProvenance()...<br>";
			if($this->iskidney == ""){
			// This function will display the annotation history of this clone.
			$display = 0;
			$sql = "SELECT COUNT(*) FROM provenance WHERE cloneid = $this->cloneid";
			//echo "$sql<br>";
			$result = mysql_query($sql,$db);
			$countval = mysql_fetch_row($result);
  			 if($countval[0] > 0){
			?>
			<table id="results">
			<thead>
			<tr>
				<th class="mainheader" colspan="3" align="left">Annotation History</th>
			</tr>
			</thead>
			<tr>
				<th class="subhead">Cloneid</th>
				<th class="subhead">Annotated Name</th>
				<th class="subhead">Refseq</th>
				<th class="subhead">Refseq Hit</th>
				<th class="subhead">Hit Def</th>
				<th class="subhead">Locus Tag</th>
				<th class="subhead">Synonyms</th>
				<th class="subhead">GI</th>
				<th class="subhead">Blast Source</th>
				<th class="subhead">Blast Source<br>Date</th>
				<th class="subhead">Bit Score</th>
				<th class="subhead">E value</th>
				<th class="subhead">Sequence<br>Direction Used</th>
				<th class="subhead">Last Annotation<br>Modification</th>
				<th class="subhead">Date Archived</th>
				<th class="subhead">Annotation<br>Comments</th>
				<th class="subhead">Archive<br>Comments</th>

			</tr>
			<?php
				$sql = "SELECT * FROM provenance WHERE cloneid = $this->cloneid";
				$result = mysql_query($sql,$db);
				while(list($cloneid, $annname, $refseq, $refseqhit, $hit_def, $locustag,$synonyms, $gi, $blast_source, $blast_source_date, $bit_score, $e_value, $seqdirused, $lastannomod, $datearchived, $anncomments, $archivecomments) = mysql_fetch_array($result)){
					// Display the table....

			$annnameout = $this->segmentString($annname, 50);
			$refseqhitout = $this->segmentString($refseqhit,50);
			$hitdefout = $this->segmentString($hit_def,50);
			$synonymsout = $this->segmentString($synonyms,30);
			$anncommentsout = $this->segmentString($anncomments,50);
			$archivecommentsout = $this->segmentString($archivecomments, 50);



					echo "<tr><td>$cloneid</td><td>$annnameout</td><td>$refseq</td><td>$refseqhitout</td><td>$hitdefout</td><td>$locustag</td><td>$synonymsout</td><td>$gi</td><td>$blast_source</td><td>$blast_source_date</td><td>$bit_score</td><td>$e_value</td><td>$seqdirused</td><td>$lastannomod</td><td>$datearchived</td><td>$anncommentsout</td><td>$archivecommentsout</td></tr>";
				}
				echo "</table>";
			}
			}

		}
		function displayGOinfo(){
			if($this->iskidney == ""){
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
					$gostr = "<a href=\"http://www.godatabase.org/cgi-bin/go.cgi?query=$idarray[$counter]\" target=\"_blank$randnum\">$idarray[$counter]:$namearray[$counter]</a>";
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
			echo "</table>";
			}
		}
		}
		function displaySeq(){
		if($this->iskidney == ""){
			?>
			<table id="results">
			<thead>
			<tr>
				<th class="mainheader" colspan="4" align="left">Sequence Associated With This Clone</th>
			</tr>
			</thead>
			<tr>
				<th class="subhead">Direction</th>
				<th class="subhead">MegaBlast against Refseq</th>
				<th class="subhead">MegaBlast against Genbank</th>
				<th class="subhead" align="left">Sequence</th>

			</tr>
			<?php
			foreach($this->_3primeseqarray as $seq){
				echo "<tr><td>3'</td><td align=\"center\"><a href=\"blastthis.php?seq=$seq&annname=$this->name&database=refseq&seqdir=3&cloneid=$this->cloneid\" target=\"_blank\">MegaBlast Refseq?</a></td><td align=\"center\"><a href=\"blastthis.php?seq=$seq&annname=$this->name&database=gbmus&seqdir=3&cloneid=$this->cloneid\" target=\"_blank\">MegaBlast Genbank?</a></td><td>$seq</td></tr>";

			}
			foreach($this->_5primeseqarray as $seq){
				echo "<tr><td>5'</td><td align=\"center\"><a href=\"blastthis.php?seq=$seq&annname=$this->name&database=refseq&seqdir=5&cloneid=$this->cloneid\" target=\"_blank\">MegaBlast Refseq?</a></td><td align=\"center\"><a href=\"blastthis.php?seq=$seq&annname=$this->name&database=gbmus&seqdir=5&cloneid=$this->cloneid\" target=\"_blank\">MegaBlast Genbank?</a></td><td>$seq</td></tr>";

			}

		}
		}


		function displaySignalData($arrayid,$db){
			$clonehybridSQL = "Select spot1cy3, spot1cy5, spot2cy3, spot2cy5, spot3cy3, spot3cy5, spot4cy3, spot4cy5, spot5cy3, spot5cy5, spot6cy3, spot6cy5,
		revspot1cy3, revspot1cy5, revspot2cy3, revspot2cy5, revspot3cy3, revspot3cy5, revspot4cy3, revspot4cy5, revspot5cy3, revspot5cy5,
		revspot6cy3, revspot6cy5, trimmean, revtrimmean, finalratio from ". $this->iskidney."hybrids where arrayid = $arrayid and cloneid =$this->cloneid";

		//echo $clonehybridSQL;
$clonehybridResult = mysql_query($clonehybridSQL, $db);
list($spot1cy3, $spot1cy5, $spot2cy3, $spot2cy5, $spot3cy3, $spot3cy5,  $spot4cy3, $spot4cy5, $spot5cy3, $spot5cy5, $spot6cy3, $spot6cy5,
		$revspot1cy3, $revspot1cy5, $revspot2cy3, $revspot2cy5, $revspot3cy3, $revspot3cy5,
		$revspot4cy3, $revspot4cy5, $revspot5cy3, $revspot5cy5, $revspot6cy3, $revspot6cy5, $trimmean, $revtrimmean, $finalratio)
		= mysql_fetch_array($clonehybridResult);
		include 'cloneinfotable.inc';

		}

		function displayPRC(){
			if($this->iskidney == ""){
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
				echo $this->prc;
				?>
				</table>
			<?php
			}
		}

		function displayProbeSeqLink(){
			echo "<br><a href=\"./probeseqs.php?startclone=$this->cloneid\" target=\"_blank\">Probe this seq?</a><br>";

		}

		function displayTable($tableclass,$tdclassparameter, $tdclassresult, $width){

			echo "<table class=\"$tableclass\" width=\"$width\">";
			echo "<tr>
<th class=\"mainheader\" colspan=\"2\" ><strong>Clone Information:</strong></th>
</tr>";
			$priv = $_SESSION['priv_level'];
			if($priv==99){
				$editstr = "<a href=\"updateuncondensedclone.php?cloneid=$this->cloneid\" target=\"_blank\"><strong>Edit?</strong></a>";
			}
			echo "<tr><td class=\"$tdclassparameter\"><strong>Clone ID</strong></td><td class=\"$tdclassresult\">$this->cloneid    $editstr</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Name</strong></td><td class=\"$tdclassresult\">$this->name</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Refseq</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$this->refseq\" target=\"_blank\">$this->refseq</a></td></tr>";
			if($this->refseqhit != ""){
				$refhitout = $this->segmentString($this->refseqhit, 50);
			echo "<tr><td class=\"$tdclassparameter\"><strong>Refseq Hit</strong></td><td class=\"$tdclassresult\">$refhitout</td></tr>";
			}
			$hitdefout = $this->segmentString($this->hit_def, 50);
			echo "<tr><td class=\"$tdclassparameter\"><strong>Hit Def</strong></td><td class=\"$tdclassresult\">$hitdefout</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Locus Tag</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.informatics.jax.org/searches/accession_report.cgi?id=$this->locustag\" target=\"_blank\">$this->locustag</a></td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Synonyms</strong></td><td class=\"$tdclassresult\">$this->synonyms</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>GI</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=Nucleotide&list_uids=$this->gi&dopt=graph\"  target=\"_blank\">$this->gi</a></td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Blast Source</strong></td><td class=\"$tdclassresult\">$this->blast_source</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Blast Source Date</strong></td><td class=\"$tdclassresult\">$this->blast_source_date</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Bit Score</strong></td><td class=\"$tdclassresult\">$this->bit_score</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>E Value</strong></td><td class=\"$tdclassresult\">$this->e_value</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Seq Direction Used</strong></td><td class=\"$tdclassresult\">$this->seqdirused</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Date Modified</strong></td><td class=\"$tdclassresult\">$this->datemodified</td></tr>";

			$length = strlen($this->comments);
			$sublength = 100;
			$rows = $length/$sublength;
			///$seqstr = "";
			$commentsout = "";
			$lastval = 0;
			for($i = 0; $i < $rows; $i++){
				$seqstr = "";
				//$sublength = (($i + 1) * 3);
				$seqstr = substr($this->comments, $lastval, $sublength);
				//echo "substr ($seq, $lastval, $sublength)";
				$commentsout .= "$seqstr"."<br>";
				//echo "$seqout";
				$lastval = $lastval + $sublength;
			}

			echo "<tr><td class=\"$tdclassparameter\"><strong>Comments</strong></td><td class=\"$tdclassresult\">$commentsout</td></tr>";
			echo "</table>";
		}
		function displayCloneEditForm($tableclass,$tdclassparameter, $tdclassresult, $width){
			?>
			<form name="query" method="post" onsubmit="" action="<?php  $_SERVER['PHP_SELF'] ?>">
			<?php
			echo "<table class=\"$tableclass\" width=\"$width\">";
			echo "<tr>
<th class=\"mainheader\" colspan=\"4\" ><strong>Clone Information:</strong></th>
</tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Clone ID</strong></td><td class=\"$tdclassresult\">$this->cloneid</td><td class=\"results\">$this->cloneid</td>



<td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Can't Modify!
</td></tr>";
//

		$annnameout = $this->segmentString($this->name, 50);
			echo "<tr><td class=\"$tdclassparameter\"><strong>Name</strong></td><td class=\"$tdclassresult\">$annnameout</td><td class=\"results\">
<input name=\"annname\" type=\"text\" value=\"$this->name\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";


			echo "<tr><td class=\"$tdclassparameter\"><strong>Refseq</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$this->refseq\" target=\"_blank\">$this->refseq</a></td>	<td class=\"results\">
<input name=\"refseq\" type=\"text\" value=\"$this->refseq\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";
			//if($this->refseqhit != ""){
			$this->refseqhit = str_replace("\"","",$this->refseqhit);
			echo "<tr><td class=\"$tdclassparameter\"><strong>Refseq Hit</strong></td><td class=\"$tdclassresult\">$this->refseqhit</td><td class=\"results\">
<input name=\"refseqhit\" type=\"text\" value=\"$this->refseqhit\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";
			//}
			$hitdefout = $this->segmentString($this->hit_def, 50);
			echo "<tr><td class=\"$tdclassparameter\"><strong>Hit Def</strong></td><td class=\"$tdclassresult\">$this->hit_def</td><td class=\"results\">
<input name=\"hit_def\" type=\"text\" value=\"$hitdefout\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";

			echo "<tr><td class=\"$tdclassparameter\"><strong>Locus Tag</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.informatics.jax.org/searches/accession_report.cgi?id=$this->locustag\" target=\"_blank\">$this->locustag</a></td><td class=\"results\">
<input name=\"locustag\" type=\"text\" value=\"$this->locustag\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>Synonyms</strong></td><td class=\"$tdclassresult\">$this->synonyms</td><td class=\"results\">
<input name=\"synonyms\" type=\"text\" value=\"$this->synonyms\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";
			echo "<tr><td class=\"$tdclassparameter\"><strong>GI</strong></td><td class=\"$tdclassresult\"><a href=\"http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=Nucleotide&list_uids=$this->gi&dopt=graph\"  target=\"_blank\">$this->gi</a></td><td class=\"results\">
<input name=\"gi\" type=\"text\" value=\"$this->gi\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";

			echo "<tr><td class=\"$tdclassparameter\"><strong>Blast Source</strong></td><td class=\"$tdclassresult\">$this->blast_source</td><td class=\"results\">
<input name=\"blast_source\" type=\"text\" value=\"$this->blast_source\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";

			echo "<tr><td class=\"$tdclassparameter\"><strong>Blast Source Date</strong></td><td class=\"$tdclassresult\">$this->blast_source_date</td><td class=\"results\">
<input name=\"blast_source_date\" type=\"text\" value=\"$this->blast_source_date\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";

			echo "<tr><td class=\"$tdclassparameter\"><strong>Bit Score</strong></td><td class=\"$tdclassresult\">$this->bit_score</td><td class=\"results\">
<input name=\"bit_score\" type=\"text\" value=\"$this->bit_score\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";

			echo "<tr><td class=\"$tdclassparameter\"><strong>E Value</strong></td><td class=\"$tdclassresult\">$this->e_value</td><td class=\"results\">
<input name=\"e_value\" type=\"text\" value=\"$this->e_value\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";

			echo "<tr><td class=\"$tdclassparameter\"><strong>Seq Direction Used</strong></td><td class=\"$tdclassresult\">$this->seqdirused</td><td class=\"results\">
<input name=\"seqdirused\" type=\"text\" value=\"$this->seqdirused\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";

			echo "<tr><td class=\"$tdclassparameter\"><strong>Date Modified</strong></td><td class=\"$tdclassresult\">$this->datemodified</td><td class=\"results\">
<input name=\"datemodified\" type=\"text\" value=\"$this->datemodified\" align=\"right\"></input>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";

			echo "<tr><td class=\"$tdclassparameter\"><strong>Comments</strong></td><td class=\"$tdclassresult\">$this->comments</td><td class=\"results\"><textarea name=\"comments\" type=\"text\" align=\"right\" cols=\"50\" rows=\"10\">$this->comments</textarea>
</td><td class=\"results\">
<font color=\"red\"><b>NOTE: </b></font>Text value.
</td></tr>";

			echo "<tr>
<td><input type=\"submit\" name=\"submit\" value=\"Submit\"></td>
<td></td>
<td></td>
<td><input type=\"reset\" value=\"Reset Form\"></td>
</tr>";
			echo "</table>";
			echo "</form>";
		}


	}
?>
