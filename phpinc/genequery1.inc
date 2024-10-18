<?php

	if($userid==1){
		#analyze($_POST);
	}
	if(isset($_POST['submitted'])){
	$submitted = $_POST['submitted'];
	$thisorganism = $_POST['thisorganism'];
	$genename = $_POST['genename'];
	$refseq = $_POST['refseq'];
	$locuslink = $_POST['locuslink'];
	$unigene = $_POST['unigene'];
	$ensembl = $_POST['ensembl'];
	$genedesc = $_POST['genedesc'];
	$goidtext = $_POST['goidtext'];
	$goidnum = $_POST['goidnum'];
	$genbank = $_POST['genbank'];

	$featurenumber = $_POST['featurenumber'];
	$symbolsearch = $_POST['symbolsearch'];
	$refseqsearch = $_POST['refseqsearch'];
	$locuslinksearch = $_POST['locuslinksearch'];
	$unigenesearch = $_POST['unigenesearch'];
	$ensemblsearch = $_POST['ensemblsearch'];
	$genedescsearch = $_POST['genedescsearch'];
	$goidtextsearch = $_POST['goidtextsearch'];
	$goidnumsearch = $_POST['goidnumsearch'];
	$genbanksearch = $_POST['genbanksearch'];
	
	$featurenumbersearch = $_POST['featurenumbersearch'];
	}else{
		$submitted = false;
	}

$featurenumArray = array();
//$featurenumslist = "";
//analyze($_POST);

if($submitted != true){
	$agilentarraytypeSQL = "SELECT id, organism, arraydesc, version FROM agilentarrays";
	$arraytypeResult = $db->Execute($agilentarraytypeSQL);//mysql_query($agilentarraytypeSQL, $db);
	$arraytypeMenu = "<option value=\"-1\" selected>Please select an organism array</option>\r";
	#while(list($arrayid, $organism, $arraydesc, $version) = mysql_fetch_array($arraytypeResult))
	while($row = $arraytypeResult->FetchRow()){
		$arrayid = $row[0];
		$organism = $row[1];
		$arraydesc = $row[2];
		$version = $row[3];
	$arraytypeMenu .= "<option value=\"$arrayid\">$organism $arraydesc $version</option>\r";
	}
	?>

	<form enctype="multipart/form-data" name="genequery" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkGeneQueryForm()">
	<input type="hidden" name="submitted" value="true">
	<table class="question" width="800">
	
	<tr>
	<td><strong>Array Type/Species</strong></td>
	<td><select name="thisorganism">
	<option SELECTED></option>
	<?php echo $arraytypeMenu; ?>
	</select></td>
	<td></td>
	</tr>
	<tr><td></td><td colspan="2"><font color="red"><strong>NOTE:</strong></font>Multiple values can be entered by separating with commas or one per line</td></tr>
	<tr>
	<td><strong>Official Gene Symbol:</strong><br>
	<input type="radio" name="symbolsearch" value="0" checked>Wildcard search</input><br>
	<input type="radio" name="symbolsearch" value="1" >Exact Match</input>
	</td>
	<td>
	<TEXTAREA NAME="genename" COLS=80 ROWS=6 ></TEXTAREA>
	</td>
	<td>
		<img id="genesymboltip" src="./images/dialog-information12x12.png" align="top"/>
		<div dojoType="dijit.Tooltip" connectId="genesymboltip">
		<table width="350px">
			<tr>
				<td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td>
			</tr>
			<tr>
				<td>The official gene symbol of the Entrez Gene record for the gene.<br></td>
			</tr>
		</table>
		</div>
	
	</td>
	</tr>
	<tr>
	<td><strong>Refseq:</strong><br>
	<input type="radio" name="refseqsearch" value="0" checked>Wildcard search</input><br>
	<input type="radio" name="refseqsearch" value="1" >Exact Match</input>
	</td>
	<td>
	<TEXTAREA NAME="refseq" COLS=80 ROWS=6 ></TEXTAREA>
	</td>
	<td>
		<img id="refseqtip" src="./images/dialog-information12x12.png" align="top"/>
		<div dojoType="dijit.Tooltip" connectId="refseqtip">
		<table width="350px">
			<tr>
				<td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td>
			</tr>
			<tr>
				<td>NCBI Refseq.<br></td>
			</tr>
		</table>
		</div>
	
	</td>
	</tr>
	<tr>
	<td><strong>Locus Link ID:</strong><br>
	<input type="radio" name="locuslinksearch" value="0" checked>Wildcard search</input><br>
	<input type="radio" name="locuslinksearch" value="1" >Exact Match</input>
	</td>
	<td>
	<TEXTAREA NAME="locuslink" COLS=80 ROWS=6 ></TEXTAREA>
	</td>
	<td>
		<img id="locuslinktip" src="./images/dialog-information12x12.png" align="top"/>
		<div dojoType="dijit.Tooltip" connectId="locuslinktip">
		<table width="350px">
			<tr>
				<td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td>
			</tr>
			<tr>
				<td>LocusLink ID is defunct and has been replaced at NCBI by Entrez Gene.<br>  However, you can still search the microarray platforms with it.<br></td>
			</tr>
		</table>
		</div>
	</td>
	<tr>
	<td><strong>Unigene ID:</strong><br>
	<input type="radio" name="unigenesearch" value="0" checked>Wildcard search</input><br>
	<input type="radio" name="unigenesearch" value="1" >Exact Match</input>
	</td>
	<td>
	<TEXTAREA NAME="unigene" COLS=80 ROWS=6 ></TEXTAREA>
	</td>
	<td>
	<img id="unigenetip" src="./images/dialog-information12x12.png" align="top"/>
		<div dojoType="dijit.Tooltip" connectId="unigenetip">
		<table width="350px">
			<tr>
				<td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td>
			</tr>
			<tr>
				<td>A UniGene ID maps to a set of transcript sequences derived from the same transcription locus.<br></td>
			</tr>
		</table>
		</div>
	</td>
	</tr>

	<tr>
	<td><strong>GenBank Accession:</strong><br>
	<input type="radio" name="genbanksearch" value="0" checked>Wildcard search</input><br>
	<input type="radio" name="genbanksearch" value="1" >Exact Match</input>
	</td>
	<td>
	<TEXTAREA NAME="genbank" COLS=80 ROWS=6 ></TEXTAREA>
	</td>
	<td>
	<img id="genbanktip" src="./images/dialog-information12x12.png" align="top"/>
		<div dojoType="dijit.Tooltip" connectId="genbanktip">
		<table width="350px">
			<tr>
				<td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td>
			</tr>
			<tr>
				<td>A GenBank ID maps to DNA sequences.  GenBank is an annotated collection of all publicly available DNA sequences.<br></td>
			</tr>
		</table>
		</div>
	</td>
	</tr>

	<tr>
	<td><strong>Feature Number:</strong><br>
	<input type="radio" name="featurenumbersearch" value="0" checked>Wildcard search</input><br>
	<input type="radio" name="featurenumbersearch" value="1" >Exact Match</input>
	</td>
	<td>
	<TEXTAREA NAME="featurenumber" COLS=80 ROWS=6 ></TEXTAREA>
	</td>
	<td>
	<img id="featurenumbertip" src="./images/dialog-information12x12.png" align="top"/>
		<div dojoType="dijit.Tooltip" connectId="featurenumbertip">
		<table width="350px">
			<tr>
				<td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td>
			</tr>
			<tr>
				<td>The Feature Number maps to a feature on the array platform.<br></td>
			</tr>
		</table>
		</div>
	</td>
	</tr>
	<!--
	<tr>
	<td><strong>Systematic Name:</strong><br>
	<input type="radio" name="systematicnamesearch" value="0" checked>Wildcard search</input><br>
	<input type="radio" name="systematicnamesearch" value="1" >Exact Match</input>
	</td>
	<td>
	<TEXTAREA NAME="systematicname" COLS=80 ROWS=6 ></TEXTAREA>
	</td>
	<td>
	<img id="systematicnametip" src="./images/dialog-information12x12.png" align="top"/>
		<div dojoType="dijit.Tooltip" connectId="systematicnametip">
		<table width="350px">
			<tr>
				<td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td>
			</tr>
			<tr>
				<td>The Feature Number maps to a feature on the array platform.<br></td>
			</tr>
		</table>
		</div>
	</td>
	</tr>
-->
	
	<tr>
	<td><strong>Ensembl ID:</strong><br>
	<input type="radio" name="ensemblsearch" value="0" checked>Wildcard search</input><br>
	<input type="radio" name="ensemblsearch" value="1" >Exact Match</input>
	</td>
	<td>
	<TEXTAREA NAME="ensembl" COLS=80 ROWS=6 ></TEXTAREA>
	</td>
	<td>
	<img id="ensemblidtip" src="./images/dialog-information12x12.png" align="top"/>
		<div dojoType="dijit.Tooltip" connectId="ensemblidtip">
		<table width="350px">
			<tr>
				<td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td>
			</tr>
			<tr>
				<td>This is the ID used by Ensembl (http://www.ensembl.org/).<br></td>
			</tr>
		</table>
		</div>
	</td>
	</tr>
	<tr>
	<td><strong>Gene Description:</strong><br>
	<input type="radio" name="genedescsearch" value="0" checked>Wildcard search</input><br>
	<input type="radio" name="genedescsearch" value="1" >Exact Match</input>
	</td>
	<td>
	<TEXTAREA NAME="genedesc" COLS=80 ROWS=6 ></TEXTAREA>
	</td>
	<td>
	<img id="genedesctip" src="./images/dialog-information12x12.png" align="top"/>
		<div dojoType="dijit.Tooltip" connectId="genedesctip">
		<table width="350px">
			<tr>
				<td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td>
			</tr>
			<tr>
				<td>Enter a search terms to search the gene descriptions.<br></td>
			</tr>
		</table>
		</div>
	</td>
	</tr>
	<tr>
	<td><strong>GoID term (text)</strong><br>
	<input type="radio" name="goidtextsearch" value="0" checked>Wildcard search</input><br>
	<input type="radio" name="goidtextsearch" value="1" >Exact Match</input>
	</td>
	<td>
	<TEXTAREA NAME="goidtext" COLS=80 ROWS=6 ></TEXTAREA>
	</td>
	<td>
	<img id="goidtexttip" src="./images/dialog-information12x12.png" align="top"/>
		<div dojoType="dijit.Tooltip" connectId="goidtexttip">
		<table width="350px">
			<tr>
				<td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td>
			</tr>
			<tr>
				<td>Enter text to search GoID (Gene Onology ID) terms.<br></td>
			</tr>
		</table>
		</div>
	</td>
	</tr>
	<tr>
	<td><strong>GoID (by number)</strong><br>
	<input type="radio" name="goidnumsearch" value="0" checked>Wildcard search</input><br>
	<input type="radio" name="goidnumsearch" value="1" >Exact Match</input>
	</td>
	<td>
	<TEXTAREA NAME="goidnum" COLS=80 ROWS=6 ></TEXTAREA>
	</td>
	<td>
	<img id="goidnumtip" src="./images/dialog-information12x12.png" align="top"/>
		<div dojoType="dijit.Tooltip" connectId="goidnumtip">
		<table width="350px">
			<tr>
				<td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td>
			</tr>
			<tr>
				<td>Enter GoID numbers to search GoID (Gene Onology ID) terms.<br></td>
			</tr>
		</table>
		</div>
	</td>
	</tr>
	<tr>
	<td><input type="submit" name="submit" value="Submit"></td>
	<td><input type="reset" value="Reset Form"></td>
	<td></td>
	</tr>
	
	</table>
	</form>
<?php
}else{
require './phpinc/organismurlselection.inc';
//analyze($_POST);

?>
<form enctype="multipart/form-data" name="genelistimport" action="./importgenelist.php"  target="_blank" method="post">
<?php
$genename = trim($genename);
if($genename != ""){
	
	$genename = str_replace("\n", ",", $genename);
	if($symbolsearch == 0){
		$queryparams = returnQueryParams($genename, "GeneSymbol", "OR", ",", "LIKE");
	}else{
		$queryparams = returnQueryParams($genename, "GeneSymbol", "OR", ",", "=");
	}
$sql = "SELECT GeneSymbol , FeatureNum, SystematicName, SimpleDescription FROM $annotationtable WHERE $queryparams ORDER BY GeneSymbol ASC";
if($userid == 1){
	##echo "<br>$sql<br>";
}

$returnResult = $db->Execute($sql);#mysql_query($sql, $db);
$returnArray = array();
//die("quitting....");

$index = 0;
	$number = $returnResult->RecordCount();
	echo "Your query returned $number hits<br>";
	#if($returnResult->RecordCount() > 0){
	if($returnResult->RecordCount() > 0){
	?>

	<table>
	<tr><TH colspan='3'>Gene Symbol Results</TH></tr>
	<TR><td><b>Include?</b></td><TD><b>Gene Symbol</b></TD><TD><b>Feature Number</b></TD><td><b>Systematic Name</b></td><td><b>Simple Description</b></td></TR>
	<?php
		#while($row = mysql_fetch_row($returnResult)){
		while($row = $returnResult->FetchRow()){
					$genesymbol = $row[0];
					$featurenum = $row[1];
					$systematicnameval = $row[2];
					$simpledesc = $row[3];
					//echo "$genesymbol $featurenum<br>";
					$cssrowclass = "d1";
					if($index%2==0){
						$cssrowclass = "d0";
					}
					?>
					<TR class="<?php echo $cssrowclass; ?>"><TD align='center'><input type="checkbox" name="<?php echo "feature".$featurenum; ?>" checked value="<?php echo $featurenum; ?>"></td><TD><a href='http://www.ncbi.nlm.nih.gov/sites/gquery?term=<?php echo "$genesymbol" ?>' target='_blank'><?php echo "$genesymbol" ?></a></td><TD><a href='<?php echo "$featureurl$featurenum"; ?>' target='_blank'><?php echo $featurenum; ?></a> </TD><TD><a href='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=<?php echo $systematicnameval; ?>' target='_blank'><?php echo $systematicnameval; ?></a> </TD><td><?php echo $simpledesc; ?></td></TR>
					<?php
					array_push($featurenumArray,$featurenum);
					
					$index++;
		}
	?>
	</table>
	<?php
	}else{
		echo "<br>Your Gene Symbol query returned no hits.<br>";
	}
}
$refseq = trim($refseq);
if($refseq != ""){
	//echo "$refseq<hr>";
	
	$refseq = str_replace("\n", ",", $refseq);
	//echo "$refseq<hr>";
	if($refseqsearch == 0){
		
		$queryparams = returnQueryParams($refseq, "Refseq", "OR", ",", "LIKE");
	}else{
		$queryparams = returnQueryParams($refseq, "Refseq", "OR", ",", "=");
	}
$sql = "SELECT GeneSymbol , Refseq, FeatureNum, SimpleDescription FROM $annotationtable WHERE $queryparams ORDER BY GeneSymbol ASC";
if($userid == 1){
	##echo "<br>$sql<br>";
}

$returnResult = $db->Execute($sql);#mysql_query($sql, $db);
$returnArray = array();
$index = 0;
	$number = $returnResult->RecordCount();
	echo "Your query returned $number hits<br>";
	if($returnResult->RecordCount() > 0){
	?>
	<table>
	<tr><TH colspan=2>Refseq Results</TH></tr>
	<TR><td><b>Include?</b></td><TD><b>Gene Symbol</b></td><TD><b>Refseq</b></TD><TD><b>Feature Number</b></TD><td><b>Simple Description</b></td></TR>
	<?php
		#while($row = mysql_fetch_row($returnResult)){
		while($row = $returnResult->FetchRow()){
					$genesymbol = $row[0];
					$refseq = $row[1];
					$featurenum = $row[2];
					$simpledesc = $row[3];
					//echo "$genesymbol $featurenum<br>";
					$cssrowclass = "d1";
					if($index%2==0){
						$cssrowclass = "d0";
					}
					?>
					<TR class="<?php echo $cssrowclass; ?>"><TD align='center'><input type="checkbox" name="<?php echo "feature".$featurenum; ?>" checked value="<?php echo $featurenum; ?>"></td><TD><a href='http://www.ncbi.nlm.nih.gov/sites/gquery?term=<?php echo "$genesymbol" ?>' target='_blank'><?php echo "$genesymbol" ?></td><td><a href='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=<?php echo "$refseq" ?>' target='_blank'><?php echo "$refseq" ?></a></td><TD><a href='<?php echo "$featureurl$featurenum"; ?>' target='_blank'><?php echo $featurenum; ?></a> </TD><td><?php echo $simpledesc; ?></td></tr>
					<?php
					array_push($featurenumArray,$featurenum);
$index++;
		}
	?>
	</table>
	<?php
	}else{
		echo "<br>Your Refseq query returned no hits.<br>";
	}
}
#################################################################3
$genbank= trim($genbank);
if($genbank != ""){
	//echo "$refseq<hr>";
	
	$genbank = str_replace("\n", ",", $genbank);
	//echo "$refseq<hr>";
	if($genbanksearch == 0){
		
		$queryparams = returnQueryParams($genbank, "GenBankAcc", "OR", ",", "LIKE");
	}else{
		$queryparams = returnQueryParams($genbank, "GenBankAcc", "OR", ",", "=");
	}
$sql = "SELECT GeneSymbol , GenBankAcc, FeatureNum, SimpleDescription FROM $annotationtable WHERE $queryparams ORDER BY GeneSymbol ASC";
if($userid == 1){
	#echo "<br>$sql<br>";
}

$returnResult = $db->Execute($sql);#mysql_query($sql, $db);
$returnArray = array();
$index = 0;
	$number = $returnResult->RecordCount();
	echo "Your query returned $number hits<br>";
	if($returnResult->RecordCount() > 0){
	?>
	<table>
	<tr><TH colspan=2>Refseq Results</TH></tr>
	<TR><td><b>Include?</b></td><TD><b>Gene Symbol</b></td><TD><b>GenBankAcc</b></TD><TD><b>Feature Number</b></TD><td><b>Simple Description</b></td></TR>
	<?php
		#while($row = mysql_fetch_row($returnResult)){
		while($row = $returnResult->FetchRow()){
					$genesymbol = $row[0];
					$genbankacc = $row[1];
					$featurenum = $row[2];
					$simpledesc = $row[3];
					//echo "$genesymbol $featurenum<br>";
					$cssrowclass = "d1";
					if($index%2==0){
						$cssrowclass = "d0";
					}
					?>
					<TR class="<?php echo $cssrowclass; ?>"><TD align='center'><input type="checkbox" name="<?php echo "feature".$featurenum; ?>" checked value="<?php echo $featurenum; ?>"></td><TD><a href='http://www.ncbi.nlm.nih.gov/sites/gquery?term=<?php echo "$genesymbol" ?>' target='_blank'><?php echo "$genesymbol" ?></td><td><a href='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=<?php echo "$genbankacc" ?>' target='_blank'><?php echo "$genbankacc" ?></a></td><TD><a href='<?php echo "$featureurl$featurenum"; ?>' target='_blank'><?php echo $featurenum; ?></a> </TD><td><?php echo $simpledesc; ?></td></tr>
					<?php
					array_push($featurenumArray,$featurenum);
$index++;
		}
	?>
	</table>
	<?php
	}else{
		echo "<br>Your GenBank Accession query returned no hits.<br>";
	}
}
$featurenumber = trim($featurenumber);
if($featurenumber != ""){
	//echo "$featurenumber<hr>";
	
	$featurenumber = str_replace("\n", ",", $featurenumber);
	//echo "$featurenumber<hr>";
	if($featurenumbersearch == 0){
		
		$queryparams = returnQueryParams($featurenumber, "FeatureNum", "OR", ",", "LIKE");
	}else{
		$queryparams = returnQueryParams($featurenumber, "FeatureNum", "OR", ",", "=");
	}
$sql = "SELECT GeneSymbol , Refseq, FeatureNum, SimpleDescription FROM $annotationtable WHERE $queryparams ORDER BY GeneSymbol ASC";
//#echo "<br>$sql<br>";
if($userid == 1){
	##echo "<br>$sql<br>";
}
$returnResult = $db->Execute($sql);#mysql_query($sql, $db);
$returnArray = array();
$index = 0;
	$number = $returnResult->RecordCount();
	echo "Your query returned $number hits<br>";
	if($returnResult->RecordCount() > 0){
	?>
	<table>
	<tr><TH colspan=2>Refseq Results</TH></tr>
	<TR><td><b>Include?</b></td><TD><b>Gene Symbol</b></td><TD><b>Refseq</b></TD><TD><b>Feature Number</b></TD><td><b>Simple Description</b></td></TR>
	<?php
		#while($row = mysql_fetch_row($returnResult)){
		while($row = $returnResult->FetchRow()){
					$genesymbol = $row[0];
					$refseq = $row[1];
					$featurenum = $row[2];
					$simpledesc = $row[3];
					//echo "$genesymbol $featurenum<br>";
					$cssrowclass = "d1";
					if($index%2==0){
						$cssrowclass = "d0";
					}
					?>
					<TR class="<?php echo $cssrowclass; ?>"><TD align='center'><input type="checkbox" name="<?php echo "feature".$featurenum; ?>" checked value="<?php echo $featurenum; ?>"></td><TD><a href='http://www.ncbi.nlm.nih.gov/sites/gquery?term=<?php echo "$genesymbol" ?>' target='_blank'><?php echo "$genesymbol" ?></td><td><a href='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=<?php echo "$refseq" ?>' target='_blank'><?php echo "$refseq" ?></a></td><TD><a href='<?php echo "$featureurl$featurenum"; ?>' target='_blank'><?php echo $featurenum; ?></a> </TD><td><?php echo $simpledesc; ?></td></tr>
					<?php
					array_push($featurenumArray,$featurenum);
$index++;
		}
	?>
	</table>
	<?php
	}else{
		echo "<br>Your Feature Number query returned no hits.<br>";
	}
}
/*
$systematicname = trim($systematicname);
if($systematicname != ""){
	//echo "$systematicname<hr>";
	
	$systematicname = str_replace("\n", ",", $systematicname);
	//echo "$systematicname<hr>";
	if($systematicnamesearch == 0){
		
		$queryparams = returnQueryParams($systematicname, "SystematicName", "OR", ",", "LIKE");
	}else{
		$queryparams = returnQueryParams($systematicname, "SystematicName", "OR", ",", "=");
	}
$sql = "SELECT GeneSymbol , Refseq, FeatureNum, SimpleDescription, SystematicName FROM $annotationtable WHERE $queryparams ORDER BY GeneSymbol ASC";
//##echo "<br>$sql<br>";
if($userid == 1){
	##echo "<br>$sql<br>";
}
$returnResult = $db->Execute($sql);#mysql_query($sql, $db);
$returnArray = array();
$index = 0;
	$number = $returnResult->RecordCount();
	echo "Your query returned $number hits<br>";
	if($returnResult->RecordCount() > 0){
	?>
	<table>
	<tr><TH colspan=2>Refseq Results</TH></tr>
	<TR><td><b>Include?</b></td><TD><b>Gene Symbol</b></td><TD><b>Systematic Name</b></td><TD><b>Refseq</b></TD><TD><b>Feature Number</b></TD><td><b>Simple Description</b></td></TR>
	<?php
		#while($row = mysql_fetch_row($returnResult)){
		while($row = $returnResult->FetchRow()){
					$genesymbol = $row[0];
					$refseq = $row[1];
					$featurenum = $row[2];
					$simpledesc = $row[3];
					$systematicnameval = $row[4];
					//echo "$genesymbol $featurenum<br>";
					$cssrowclass = "d1";
					if($index%2==0){
						$cssrowclass = "d0";
					}
					?>
					<TR class="<?php echo $cssrowclass; ?>"><TD align='center'><input type="checkbox" name="<?php echo "feature".$featurenum; ?>" checked value="<?php echo $featurenum; ?>"></td><TD><a href='http://www.ncbi.nlm.nih.gov/sites/gquery?term=<?php echo "$genesymbol" ?>' target='_blank'><?php echo "$genesymbol" ?></td><td><?php echo "$systematicnameval"; ?></td><td><a href='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=<?php echo "$refseq" ?>' target='_blank'><?php echo "$refseq" ?></a></td><TD><a href='<?php echo "$featureurl$featurenum"; ?>' target='_blank'><?php echo $featurenum; ?></a> </TD><td><?php echo $simpledesc; ?></td></tr>
					<?php
					array_push($featurenumArray,$featurenum);
$index++;
		}
	?>
	</table>
	<?php
	}else{
		echo "<br>Your Systematic Name query returned no hits.<br>";
	}
}
########################################################################################################
*/
$locuslink = trim($locuslink);
if($locuslink != ""){
	if($locuslinksearch == 0){
		$locuslink = str_replace("\n", ",", $locuslink);
		$queryparams = returnQueryParams($locuslink, "LocusLinkID", "OR", ",", "LIKE");
	}else{
		$queryparams = returnQueryParams($locuslink, "LocusLinkID", "OR", ",", "=");
	}
	$sql = "SELECT GeneSymbol , LocusLinkID, FeatureNum, SimpleDescription FROM $annotationtable WHERE $queryparams AND LocusLinkID > 0 ORDER BY GeneSymbol ASC";
	if($userid == 1){
		##echo "<br>$sql<br>";
	}
	$returnResult = $db->Execute($sql);
	$returnArray = array();
$index = 0;
$number = $returnResult->RecordCount();
	echo "Your query returned $number hits<br>";

	if($returnResult->RecordCount() > 0){
	?>
	<table>
	<tr><TH colspan=2>LocusLinkID Results</TH></tr>
	<TR><td><b>Include?</b></td><TD><b>Gene Symbol</b></TD><TD><b>LocusLinkID</b></TD><TD><b>Feature Number</b></TD><td><b>Simple Description</b></td></TR>
	<?php
		#while($row = mysql_fetch_row($returnResult)){
		while($row = $returnResult->FetchRow()){
					$genesymbol = $row[0];
					$LocusLinkID = $row[1];
					$featurenum = $row[2];
					$simpledesc = $row[3];
					$cssrowclass = "d1";
					if($index%2==0){
						$cssrowclass = "d0";
					}
					?>
					<TR class="<?php echo $cssrowclass; ?>"><TD align='center'><input type="checkbox" name="<?php echo "feature".$featurenum; ?>" checked value="<?php echo $featurenum; ?>"></td><TD><a href='http://www.ncbi.nlm.nih.gov/sites/gquery?term=<?php echo "$genesymbol" ?>' target='_blank'><?php echo "$genesymbol" ?></td><td><?php echo "$LocusLinkID" ?></td><TD><a href='<?php echo "$featureurl$featurenum"; ?>' target='_blank'><?php echo $featurenum; ?></a> </TD><td><?php echo $simpledesc; ?></td></TR>
					<?php
					array_push($featurenumArray,$featurenum);
$index++;
		}
	?>
	</table>
	<?php
	}else{
		echo "<br>Your Locus Link ID query returned no hits.<br>";
	}
}
$unigene=trim($unigene);
if($unigene != ""){
	$unigene = str_replace("\n", ",", $unigene);
	if($unigenesearch == 0){
		$queryparams = returnQueryParams($unigene, "UnigeneID", "OR", ",", "LIKE");
	}else{
		$queryparams = returnQueryParams($unigene, "UnigeneID", "OR", ",", "=");
	}
	
	$sql = "SELECT GeneSymbol , UnigeneID, FeatureNum, SimpleDescription FROM $annotationtable WHERE $queryparams ORDER BY GeneSymbol ASC";
//#echo "<br>$sql<br>";
if($userid == 1){
	#echo "<br>$sql<br>";
}
$returnResult = $db->Execute($sql);
$returnArray = array();
$index = 0;
	$number = $returnResult->RecordCount();
	echo "Your query returned $number hits<br>";
	if($returnResult->RecordCount() > 0){
	?>
	<table>
	<tr><TH colspan=2>Unigene Results</TH></tr>
	<TR><td><b>Include?</b></td><TD><b>Gene Symbol</b></TD><TD><b>Unigene ID</b></TD><TD><b>Feature Number</b></TD><td><b>Simple Description</b></td></TR>
	<?php
		#while($row = mysql_fetch_row($returnResult)){
		while($row = $returnResult->FetchRow()){
					$genesymbol = $row[0];
					$UnigeneID = $row[1];
					$featurenum = $row[2];
					$simpledesc = $row[3];
					//echo "$genesymbol $featurenum<br>";
					$cssrowclass = "d1";
					if($index%2==0){
						$cssrowclass = "d0";
					}
					?>
					<TR class="<?php echo $cssrowclass; ?>"><TD align='center'><input type="checkbox" name="<?php echo "feature".$featurenum; ?>" checked value="<?php echo $featurenum; ?>"></td><TD><a href='http://www.ncbi.nlm.nih.gov/sites/gquery?term=<?php echo "$genesymbol" ?>' target='_blank'><?php echo "$genesymbol" ?></td><td><?php echo "$UnigeneID" ?></td><TD><a href='<?php echo "$featureurl$featurenum"; ?>' target='_blank'><?php echo $featurenum; ?></a> </TD><td><?php echo $simpledesc; ?></td></TR>
					<?php
					array_push($featurenumArray,$featurenum);
$index++;
		}
	?>
	</table>
	<?php
	}else{
		echo "<br>Your Unigene query returned no hits.<br>";
	}
}
$ensembl = trim($ensembl);
if($ensembl != ""){
	$ensembl = str_replace("\n", ",", $ensembl);
	if($ensemblsearch == 0){
		$queryparams = returnQueryParams($ensembl, 'EnsemblID', "OR", ",", "LIKE");
	}else{
		$queryparams = returnQueryParams($ensembl, 'EnsemblID', "OR", ",", "=");
	}

$sql = "SELECT GeneSymbol , EnsemblID, FeatureNum, SimpleDescription FROM $annotationtable WHERE $queryparams ORDER BY GeneSymbol ASC";
//#echo "<br>$sql<br>";
if($userid == 1){
	#echo "<br>$sql<br>";
}
$returnResult = $db->Execute($sql);
$returnArray = array();
$index = 0;
	$number = $returnResult->RecordCount();
	echo "Your query returned $number hits<br>";
	if($returnResult->RecordCount() > 0){
	?>
	<table>
	<tr><TH colspan=2>Ensembl Results</TH></tr>
	<TR><td><b>Include?</b></td><TD><b>Gene Symbol</b></TD><TD><b>Ensembl ID</b></TD><TD><b>Feature Number</b></TD><td><b>Simple Description</b></td></TR>
	<?php
		#while($row = mysql_fetch_row($returnResult)){
		while($row = $returnResult->FetchRow()){
					$genesymbol = $row[0];
					$EnsemblID = $row[1];
					$featurenum = $row[2];
					$simpledesc = $row[3];
					//echo "$genesymbol $featurenum<br>";
					$cssrowclass = "d1";
					if($index%2==0){
						$cssrowclass = "d0";
					}
					?>
					<TR class="<?php echo $cssrowclass; ?>"><TD align='center'><input type="checkbox" name="<?php echo "feature".$featurenum; ?>" checked value="<?php echo $featurenum; ?>"></td><TD><a href='http://www.ncbi.nlm.nih.gov/sites/gquery?term=<?php echo "$genesymbol" ?>' target='_blank'><?php echo "$genesymbol" ?></td><td><?php echo "$EnsemblID" ?></td><TD><a href='<?php echo "$featureurl$featurenum"; ?>' target='_blank'><?php echo $featurenum; ?></a> </TD><td><?php echo $simpledesc; ?></td></TR>
					<?php
					array_push($featurenumArray,$featurenum);
$index++;
		}
	?>
	</table>
	<?php
	}else{
		echo "<br>Your Ensembl ID query returned no hits.<br>";
	}
}
$genedesc = trim($genedesc);
if($genedesc != ""){
	$genedesc = str_replace("\n", ",", $genedesc);
	if($genedescsearch == 0){
		$queryparams = returnQueryParams($genedesc, "Description", "OR", ",", "LIKE");
	}else{
		$queryparams = returnQueryParams($genedesc, "Description", "OR", ",", "=");
	}

	$sql = "SELECT GeneSymbol , Description, FeatureNum, SimpleDescription FROM $annotationtable WHERE $queryparams ORDER BY GeneSymbol ASC";
	//#echo "<br>$sql<br>";
	if($userid == 1){
	#echo "<br>$sql<br>";
}
	$returnResult = $db->Execute($sql);
	$returnArray = array();
$index = 0;
	$number = $returnResult->RecordCount();
	echo "Your query returned $number hits<br>";
	if($returnResult->RecordCount() > 0){
	?>
	<table>
	<tr>
	<tr><TH colspan=3>Description Results</TH></tr>
	<TR><td><b>Include?</b></td><TD><b>Gene Symbol</b></TD><TD><b>Description</b></TD><TD><b>Feature Number</b></TD><td><b>Simple Description</b></td></tr>
	<?php
		#while($row = mysql_fetch_row($returnResult)){
		while($row = $returnResult->FetchRow()){
		
					$genesymbol = $row[0];
					$genedesc = $row[1];
					$featurenum = $row[2];
					$simpledesc = $row[3];
					$cssrowclass = "d1";
					if($index%2==0){
						$cssrowclass = "d0";
					}
					?>
					<TR class="<?php echo $cssrowclass; ?>"><TD align='center'><input type="checkbox" name="<?php echo "feature".$featurenum; ?>" checked value="<?php echo $featurenum; ?>"></td><TD><a href='http://www.ncbi.nlm.nih.gov/sites/gquery?term=<?php echo "$genesymbol" ?>' target='_blank'><?php echo $genesymbol ?></td><td><?php echo $genedesc ?></td><TD><a href='<?php echo "$featureurl$featurenum"; ?>' target='_blank'><?php echo $featurenum; ?></a> </TD><td><?php echo $simpledesc; ?></td></TR>
					<?php
					array_push($featurenumArray,$featurenum);	
$index++;
		}
	?>
	</table>
	<?php
	}else{
		echo "<br>Your Description query returned no hits.<br>";
	}
}
$goidtext = trim($goidtext);
if($goidtext != ""){
	$goidtext = str_replace("\n", ",", $goidtext);
	if($goidtextsearch == 0){
		$queryparams = returnQueryParams($goidtext, "GoID", "OR", ",", "LIKE");
	}else{
		$queryparams = returnQueryParams($goidtext, "GoID", "OR", ",", "=");
	}

	$sql = "SELECT GeneSymbol , GoID, FeatureNum, SimpleDescription FROM $annotationtable WHERE $queryparams ORDER BY GeneSymbol ASC";
	//#echo "<br>$sql<br>";
	
	$returnResult = $db->Execute($sql);
	$returnArray = array();
$index = 0;
	$number = $returnResult->RecordCount();
	echo "Your query returned $number hits<br>";
	if($returnResult->RecordCount() > 0){
	?>
	<table>
	<tr>
	<tr><TH colspan=3>GoID Text Results</TH></tr>
	<TR><td><b>Include?</b></td><TD><b>Gene Symbol</b></TD><TD align="left"><b>GoID</b></TD><TD><b>Feature Number</b></TD><td><b>Simple Description</b></td></tr>
	<?php
		#while($row = mysql_fetch_row($returnResult)){
		while($row = $returnResult->FetchRow()){
		
					$genesymbol = $row[0];
					$goid = $row[1];
					$golist = explode("|",$goid);
					$goid = "";
					foreach($golist as $item){
						$goid .= "$item<br>";							  
					}
					$featurenum = $row[2];
					$simpledesc = $row[3];
					$cssrowclass = "d1";
					if($index%2==0){
						$cssrowclass = "d0";
					}
					?>
					<TR class="<?php echo $cssrowclass; ?>"><TD align='center'><input type="checkbox" name="<?php echo "feature".$featurenum; ?>" checked value="<?php echo $featurenum; ?>"></td><TD><a href='http://www.ncbi.nlm.nih.gov/sites/gquery?term=<?php echo "$genesymbol" ?>' target='_blank'><?php echo $genesymbol ?></td><td align="left"><?php echo $goid ?></td><TD><a href='<?php echo "$featureurl$featurenum"; ?>' target='_blank'><?php echo $featurenum; ?></a> </TD><td><?php echo $simpledesc; ?></td></TR>
					<?php
					array_push($featurenumArray,$featurenum);
$index++;	
		}
	?>
	</table>
	<?php
	}else{
		echo "<br>Your Description query returned no hits.<br>";
	}
}
$goidnum = trim($goidnum);
if($goidnum != ""){
	$goidnum = str_replace("\n", ",", $goidnum);
	if($goidnumsearch == 0){
		$queryparams = returnQueryParams($goidnum, "GoID", "OR", ",", "LIKE");
	}else{
		$queryparams = returnQueryParams($goidnum, "GoID", "OR", ",", "=");
	}

$sql = "SELECT GeneSymbol , GoID, FeatureNum, SimpleDescription FROM $annotationtable WHERE $queryparams ORDER BY GeneSymbol ASC";
//#echo "<br>$sql<br>";

$returnResult = $db->Execute($sql);
$returnArray = array();
$index = 0;
	$number = $returnResult->RecordCount();
	echo "Your query returned $number hits<br>";
	if($returnResult->RecordCount() > 0){
	?>
	<table>
	<tr>
	<tr><TH colspan=3>GoID Number Results</TH></tr>
	<TR><td><b>Include?</b></td><TD><b>Gene Symbol</b></TD><TD><b>GoID</b></TD><TD><b>Feature Number</b></TD><td><b>Simple Description</b></td></tr>
	<?php
		#while($row = mysql_fetch_row($returnResult)){
		while($row = $returnResult->FetchRow()){
		
					$genesymbol = $row[0];
					$goid = $row[1];
					$golist = explode("|",$goid);
					$goid = "";
					foreach($golist as $item){
						$goid .= "$item<br>";							  
					}
					$featurenum = $row[2];
					$simpledesc = $row[3];
					$cssrowclass = "d1";
					if($index%2==0){
						$cssrowclass = "d0";
					}
					?>
					<TR class="<?php echo $cssrowclass; ?>"><TD align='center'><input type="checkbox" name="<?php echo "feature".$featurenum; ?>" checked value="<?php echo $featurenum; ?>"></td><TD><a href='http://www.ncbi.nlm.nih.gov/sites/gquery?term=<?php echo "$genesymbol" ?>' target='_blank'><?php echo $genesymbol ?></td><td align="left"><?php echo $goid ?></td><TD><a href='<?php echo "$featureurl$featurenum"; ?>' target='_blank'><?php echo $featurenum; ?></a> </TD><td><?php echo $simpledesc; ?></td></TR>
					<?php
					array_push($featurenumArray,$featurenum);	
$index++;
		}
	?>
	</table>
	<?php
	}else{
		echo "<br>Your Description query returned no hits.<br>";
	}
}
if(isset($_POST['thisorganism'])){
	$thisorganism = $_POST['thisorganism'];
}else{
	$thisorganism = "";
}
if(isset($_POST['imported'])){
	$imported = $_POST['imported'];
}else{
	$imported = "";
}
if(isset($_POST['userid'])){
	$userid = $_POST['userid'];
}else{
	$userid = "";
}
;
/*
// $listidresult = mysql_query("SELECT MAX(listid) from genelist",$db);
// $nextlistid = mysql_result($listidresult, 0);
// if($nextlistid != null){ $nextlistid++; }
// else{ $nextlistid = 1; }
// $listid = $_POST['listid'];
*/

if(isset($_POST['featurenums'])){
	$featurenums = $_POST['featurenums'];
}else{
	$featurenums = "";
}
if(isset($_POST['name'])){
	$name = $_POST['name'];
}else{
	$name = "";
}
if(isset($_POST['public'])){
	$public = $_POST['public'];
}else{
	$public = "";
}

$ondev = 1;
if($imported != true  && $ondev == 1){


$first = -1;
foreach($featurenumArray as $feature){
	if($first == -1){
		$featurenumslist = "$feature";
		$first = 1;
	}else{
		$featurenumslist .= ", $feature";
	}

}
?>

<input type="hidden" name="imported" value="true">
<input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>">
<!--
removed this and placed in the importgenelist.php script code.
<input type="hidden" name="listid" value="<?php echo $nextlistid ?>">
-->
<input type="hidden" name="featurenums" value="<?php echo $featurenumslist ?>">
<input type="hidden" name="thisorganism" value="<?php echo $thisorganism ?>">
<table>

<tr>
<td>List Name</td>
<td>
<input name="name" type="text">
</td>
</tr>
<tr>
<td>List Description</td>
<td>
<TEXTAREA NAME="desc" COLS=80 ROWS=6 ></TEXTAREA>
</td>
</tr>
<tr>
<td>Public</td>
<td>
<input name="public" type="radio" value="1">
</td>
</tr>

<tr>
<td><input type="submit" name="import" value="Import"></td>
<td><input type="reset" value="Reset Form"></td>
</tr>

</table>
</form>

<?php
}


}
?>
