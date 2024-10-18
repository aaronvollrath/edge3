
<div dojoType="dijit.layout.ContentPane" style="height: 1800px;">

 <h3>On what arrays does my gene of interest respond?</h3>
<div>

<?php
$debug = -1;

if(count($_GET) > 1){
	//echo "Dealing w/ get...";
	$_POST['refseq']= $_GET['refseq'];
	$_POST['rval']= $_GET['rval'];
	$_POST['lval']=$_GET['lval'];
	$dataset = 0;
	$colorscheme = 0;
	$output = 1;
	$_POST['submit'] = "Submit";
	//analyze($_POST);


}


#analyze($_POST);

if (!isset($_POST['question1submit'])) { // if form has not been submitted


/*	if(isset($_SESSION['priv_level'])){
		$ownerid = $_SESSION['priv_level'];
	}else{
		$ownerid = 1;
	}
*/
	#  There might be a more efficient way of doing this, but what this code is doing is looking at what arrays the user is associated w/ and then getting the distinct organisms
	#  that they have arrays for.

	# get the experiments they own....
	$userid = $_SESSION['userid'];
	$expidSQL = "SELECT expid FROM agilent_experimentsdesc WHERE ownerid = $userid";
	//echo $expidSQL;
	$expidResult =  $db->Execute($expidSQL);
	$expidarray = array();
	while($row = $expidResult->FetchRow()){
		$expid = $row[0];
		array_push($expidarray,$expid);
	}
	
	# are there any groups that this user is in? if so, get the expids
	$usergroupsql = "SELECT usergroupid FROM usergroupmembers WHERE userid = $userid";
	$usergroupresult = $db->Execute($usergroupsql);
	$usersgroupexps = array();
	if($usergroupresult->RecordCount() > 0){
		// What are the experiment/user group associations for this user?
		while($row = $usergroupresult->FetchRow()){
			$assocSQL = "SELECT expid FROM expusergroupassoc WHERE usergroupid = $row[0]";
			$assocResult = $db->Execute($assocSQL);
			while($exprow = $assocResult->FetchRow()){
				$expid = $exprow[0];
				array_push($expidarray,$expid);
				
			}
		}
	}
	
	# get the array ids associated w/ these experiments.
	$arrayidarray = array();
	foreach($expidarray as $anexpid){
		$sql = "SELECT arrayid FROM agilent_experiments WHERE expid = $anexpid";
		$result = $db->Execute($sql);
		while($row = $result->FetchRow()){
			array_push($arrayidarray, $row[0]);
		}
	}
	

/*
	if($ownerid != 99){
		$arrayvaluesql = "SELECT ae.arrayid FROM agilent_experiments AS ae, agilent_experimentsdesc AS aed WHERE aed.ownerid = $ownerid AND aed.expid=ae.expid";
	}else{
		$arrayvaluesql = "SELECT ae.arrayid FROM agilent_experiments AS ae, agilent_experimentsdesc AS aed WHERE aed.expid=ae.expid";
	
	}
	
	echo $arrayvaluesql;
	$arrayvaluesqlresult = $db->Execute($arrayvaluesql);
	
	$arrayidarray = array();
*/
	$arraycount = 0;
	#while($row = $arrayvaluesqlresult->FetchRow()){
	$arrayidarraysql = array();
	foreach($arrayidarray as $anid){
		array_push($arrayidarraysql, "arrayid = ");
		array_push($arrayidarraysql, $anid);
		array_push($arrayidarraysql, " OR ");
		$arraycount++;
	}

	// pop off the last comma.... 
	array_pop($arrayidarraysql);
	
	$arrayparams = "";
	if($arraycount != 0){
		foreach($arrayidarraysql as $val){
			$arrayparams.= "$val";
		}
	}
	
	//echo "<br>$arrayparams<br>";
	$arraytypesql = "SELECT DISTINCT arraytype FROM agilent_arrayinfo WHERE ($arrayparams) ORDER BY arraytype"; 
	
	//echo "<br>Array Type SQL: $arraytypesql<br>";
	
	$arraytypesqlresult = $db->Execute($arraytypesql);
	$arraytypeMenu = "";
	$firstval = "1";
	while($type = $arraytypesqlresult->FetchRow()){
		$arraydesclookupsql = "SELECT id, organism, arraydesc, version FROM agilentarrays WHERE id = $type[0]";
		$arraydescresult = $db->Execute($arraydesclookupsql);#mysql_query($arraydesclookupsql, $db);
		$arow=$arraydescresult->FetchRow();
		$arrayid = $arow[0];
		$organism = $arow[1];
		$arraydesc = $arow[2];
		$version = $arow[3];
		#list($arrayid, $organism, $arraydesc, $version) = mysql_fetch_array($arraydescresult);
		$version = strtolower($version);
		if($firstval == 1){
			$arraytypeMenu .= "<option value=\"$version\" SELECTED>$organism $arraydesc $version</option>\r";
		}else{
			$arraytypeMenu .= "<option value=\"$version\">$organism $arraydesc $version</option>\r";
		}
		$firstval = "0";
	}

?>


<p class="styletext">

	<form name="query" method="post" onsubmit="return checkQuestion1Form()" action="<?php  $_SERVER['PHP_SELF'] ?>">
	
	<table class="question" width="400">
	<thead>
	<tr>
	<th colspan='3'>Query Parameters</th>
	
	</thead>
	</tr>
	<tr>
	<td class="questionanswer" colspan="3" ><strong>Data Options:</strong></td>
	</tr>
	<tr>
	<td class="questionparameter" ><strong>Data Set:</strong></td>
	<td class="results">
	<!--<input type="radio" name="dataset" value="0" checked><strong><font color="red">Condensed</font></strong><br>-->
		Using All Non-Control Features <input type="hidden" name="dataset" value="1">
	</td>
	<td class="results">
	</td>
	</tr>
	<tr>
	<td class="questionparameter" ><strong>Array Platform:</strong></td>
	<td class="results">
	<select name="arraytype">
	<?php echo $arraytypeMenu; ?>
	</select>
	</td>
	<td class="results">
	Array platforms available to you.
	</td>
	</tr>
	<tr>
	<td  class="questionanswer" colspan="3"><strong>Search By:</strong></td>
	</tr>
	<tr>
	<td class="questionparameter" ><strong>Official Gene Symbol:</strong></td>
	<td class="results">
	<input name="genename" type="text" size='50'>
	</td>
	<td class="results">
	The official gene symbol of the Entrez Gene record for the gene.
	</td>
	</tr>
	<tr>
	<td class="questionparameter" ><strong>Agilent Feature Number:</strong></td>
	<td class="results">
	<input name="featurenumber" type="text" size='50'>
	</td>
	<td class="results">
	Eg. 7965, 22761
	</td>
	</tr>
	
	<td class="questionparameter" ><strong>REFSEQ:</strong></td>
	<td class="results">
	<input name="refseq" type="text" size='50'>
	</td>
	<td class="results">
	Partial REFSEQ numbers can be entered
	</td>
	</tr>
	<!--
	<tr>
	<td class="questionparameter" ><strong>Agilent Probe Number:</strong></td>
	<td class="results">
	<input name="probenumber" type="text">
	</td>
	<td class="results">
	Eg. A_52_P288284, A_51_P101506
	</td>
	</tr>
	
	
	
	
	
	<tr>
	<td class="questionparameter" ><strong>GO ID:</strong></td>
	<td class="results">
	<input name="goid" type="text">
	</td>
	<td class="results">
	Partial GOIDs can be entered
	</td>
	</tr>
	
	<tr>
	<td class="questionparameter" ><strong>GO Term:</strong></td>
	<td class="results">
	<input name="goterm" type="text" >
	</td>
	<td class="results">
	Molecular functions, biological processes <br>and cellular components can be entered.
	</td>
	
	
	</tr>
	-->
	<tr>
	<td  class="questionanswer" colspan="3"><strong>Fold-Change Threshold Values:</strong></td>
	</tr>
	<tr>
	<td class="questionparameter" ><strong>Minimum Induction:</strong></td>
	<td class="results">
	<input name="rval" type="text" value="2" align="right"></input>
	</td>
	<td class="results">
	<font color="red"><b>NOTE: </b></font>Value must be positive.
	</td>
	</tr>
	
	<tr>
	<td class="questionparameter" ><strong>Minimum Repression:</strong></td>
	<td class="results">
	<input name ="lval" type="text" value="-2" align="right"></input>
	</td>
	<td class="results">
	<font color="red"><b>NOTE: </b></font>Value must be negative.
	</td>
	</tr>
	
	<tr>
	<td><input type="submit" name="question1submit" value="Submit"></td>
	<td></td>
	<td><input type="reset" value="Reset Form"</td>
	</tr>
	
	</table>
</form>
</p>
<?php
}else{
	if(isset($_POST['genename'])){
		$primaryname = $_POST['genename'];
	}else{
		$primaryname = "";
	}
	if(isset($_POST['featurenumber'])){
		$featurenumber = $_POST['featurenumber'];
	}else{
		$featurenumber = "";
	}
	if(isset($_POST['probenumber'])){
		$probenumber = $_POST['probenumber'];
	}else{
		$probenumber = "";
	}
	if(isset($_POST['refseq'])){
		$refseq = $_POST['refseq'];
	}else{
		$refseq = "";
	}
	if(isset($_POST['goid'])){
		$goid = $_POST['goid'];
	}else{
		$goid = "";
	}
	if(isset($_POST['goterm'])){
		$goterm = $_POST['goterm'];
	}else{
		$goterm = "";
	}
	if(isset($_POST['rval'])){
		$upperbound = $_POST['rval'];
	}else{
		$upperbound = "";
	}
	if(isset($_POST['lval'])){
		$lowerbound = $_POST['lval'];
	}else{
		$lowerbound = "";
	}
	if(isset($_SESSION['priv_level'])){
		$privval = $_SESSION['priv_level'];
	}else{
		$privval = "";
	}
	if(isset($_POST['arraytype'])){
		$arraytype = $_POST['arraytype'];
	}else{
		$arraytype = "";
	}



if($lowerbound >= 1){
	$lowerbound = log($lowerbound,10);
	$lowerbound = round($lowerbound,2);
}
else{
	$lowerbound = log(-1/$lowerbound,10);
	$lowerbound = round($lowerbound,2);
}
if($upperbound >= 1){
	$upperbound = log($upperbound,10);
	$upperbound = round($upperbound,2);
}
else{
	$upperbound = log(-1/$upperbound,10);
	$upperbound = round($upperbound,2);
}
$queryparams = "";
// Need to look up cloneids for genes entered....
$genesPresent = 0;
$primarylist = "";
if($primaryname != ""){
		$genesPresent = 1;
		//echo "primaryname not null";
		$whereCheck = 1;
		// now need to go through gene name, if there are commas, need to create an array and use to
		// search across entries.
		$pos = strpos($primaryname, ",");
		if ($pos === false) { // note: three equal signs
			$primaryname = trim($primaryname);
   	 		// not found...don't need to create the array...
			$queryparams = " GeneSymbol LIKE '%$primaryname%'";
			$primarylist = $primaryname;
		}
		else{
			// The case where someone is searching on multiple genes...
			$queryparams = " GeneSymbol LIKE";
			// Create the array of primary names to search on...
			$primarynamearray = array();
			$primarynamearray = explode(",", $primaryname);
			$countcheck = 0;
			#$primaryarraynum = count($primarynamearray);
			$primaryarraynum = 0;
			# inefficient, but simpler than modifying code below...
			foreach($primarynamearray as $value){
				$name = trim($value);
				if($name != ""){
					$primaryarraynum++;
				}
			}
			


			foreach($primarynamearray as $namevalue){

				$namevalue = trim($namevalue);
				if($namevalue != ""){
					if($countcheck < $primaryarraynum - 1){
						$queryparams .= " '%$namevalue%' OR GeneSymbol LIKE ";
						// used to display in query parameters table...
						$primarylist .= "'$namevalue' OR ";
					}
					else{
						$queryparams .= " '%$namevalue%' ";
						// used to display in query parameters table...
						$primarylist .= "'$namevalue'";
					}
					$countcheck++;
				}
			}
		}
		//echo "Here's primarylist: $primarylist";
}



	// Now need to get the FeatureNums...
	if($genesPresent == 1){
		if($dataset == 1){
			$sql = "SELECT FeatureNum, GeneSymbol FROM agilent".$arraytype."_extendedannotations WHERE $queryparams"; /* AND (LogRatio >= $upperbound OR LogRatio <= $lowerbound)";*/
		}
		else{
			$sql = "SELECT arrayid, FeatureNum, GeneName FROM agilentcondenseddata WHERE $queryparams";
		}
		#die("$sql<br>");
		#echo "$sql<br>";
		$returnResult = $db->Execute($sql);
	}



$featuresPresent = 0;
if($featurenumber != ""){
		$featuresPresent = 1;
		//echo "primaryname not null";
		$whereCheck = 1;
		// now need to go through gene name, if there are commas, need to create an array and use to
		// search across entries.
		$pos = strpos($featurenumber, ",");
		if ($pos === false) { // note: three equal signs
			$featurenumber = trim($featurenumber);
   	 		// not found...don't need to create the array...
			if(is_numeric($featurenumber)){
				$queryparams = " FeatureNum = $featurenumber";
				$featurenumlist = $featurenumber;
			}
		}
		else{
			// The case where someone is searching on multiple genes...
			$queryparams = " FeatureNum =";
			// Create the array of primary names to search on...
			$featurenumberarray = array();
			$featurenumberarray = explode(",", $featurenumber);
			$countcheck = 0;
			#$featurenumberarraynum = count($featurenumberarray);
			$featurenumberarraynum = 0;
			# inefficient, but simpler than modifying code below...
			foreach($featurenumberarray as $value){
				$fnum = trim($value);
				if($fnum != ""){
					$featurenumberarraynum++;
				}
			}
			foreach($featurenumberarray as $featurenumbervalue){
				$featurenumbervalue = trim($featurenumbervalue);
				if($featurenumbervalue != ""){
					if(is_numeric($featurenumbervalue)){
						// Should I put in a check here to make sure the value is a valid agilent
						// FeatureNumber???
						if($countcheck < $featurenumberarraynum - 1){
							$queryparams .= " $featurenumbervalue OR FeatureNum = ";
							// used to display in query parameters table...
							$primarylist .= "$featurenumbervalue OR ";
						}
						else{
							$queryparams .= " $featurenumbervalue ";
							// used to display in query parameters table...
							$primarylist .= "$featurenumbervalue";
						}
						$countcheck++;
					}
				}
			}
		}
		//echo "Here's primarylist: $primarylist";
}



	// Now need to get the FeatureNums...
	if($featuresPresent == 1){
		if($dataset == 1){
			$sql = "SELECT FeatureNum, GeneSymbol FROM agilent".$arraytype."_extendedannotations WHERE $queryparams"; /* AND (LogRatio >= $upperbound OR LogRatio <= $lowerbound)";*/
		}
		else{
			$sql = "SELECT arrayid, FeatureNum, GeneName FROM agilentcondenseddata WHERE $queryparams";
		}
		#echo "$sql <br>";

		$returnFeatureNumResult = $db->Execute($sql);#mysql_query($sql, $db);
	}

//echo "LINE 355: $sql<hr>";

/*
$probesPresent = 0;
if($probenumber != ""){

		$probesPresent = 1;
		//echo "primaryname not null";
		$whereCheck = 1;
		// now need to go through gene name, if there are commas, need to create an array and use to
		// search across entries.
		$pos = strpos($probenumber, ",");
		if ($pos === false) { // note: three equal signs
			$probenumber = trim($probenumber);
   	 		// not found...don't need to create the array...
			//if(is_numeric($probenumber)){
				$queryparams = " ProbeName = '$probenumber'";
				$probenumlist = $probenumber;
			//}
		}
		else{
			// The case where someone is searching on multiple genes...
			$queryparams = " ProbeName =";
			// Create the array of primary names to search on...
			$probenumberarray = array();
			$probenumberarray = explode(",", $probenumber);
			$countcheck = 0;
			$probenumberarraynum = count($probenumberarray);
			foreach($probenumberarray as $probenumbervalue){
				$probenumbervalue = trim($probenumbervalue);
				//if(is_numeric($probenumbervalue)){
					// Should I put in a check here to make sure the value is a valid agilent
					// FeatureNumber???
					if($countcheck < $probenumberarraynum - 1){
						$queryparams .= " '$probenumbervalue' OR ProbeName = ";
						// used to display in query parameters table...
						$primarylist .= "'$probenumbervalue' OR ";
					}
					else{
						$queryparams .= " '$probenumbervalue' ";
						// used to display in query parameters table...
						$primarylist .= "'$probenumbervalue'";
					}
					$countcheck++;
				//}
			}
		}
		//echo "Here's primarylist: $primarylist";
}



	// Now need to get the FeatureNums...
	if($probesPresent == 1){
		if($dataset == 1){
			$sql = "SELECT FeatureNum, ProbeName FROM agilentdata WHERE $queryparams AND arrayid = 1";
		}
		else{
			$sql = "SELECT arrayid, FeatureNum, GeneName FROM agilentcondenseddata WHERE $queryparams";
		}


		$returnProbeNameResult = mysql_query($sql, $db);
	}

*/
// REFSEQ STUFF

//echo "LINE 355: $sql<hr>";
$refseqsPresent = 0;
if($refseq != ""){

		$refseqsPresent = 1;
		//echo "primaryname not null";
		$whereCheck = 1;
		// now need to go through gene name, if there are commas, need to create an array and use to
		// search across entries.
		$pos = strpos($refseq, ",");
		if ($pos === false) { // note: three equal signs
			$refseq = trim($refseq);
   	 		// not found...don't need to create the array...
			//if(is_numeric($probenumber)){
				$queryparams = " Refseq = '$refseq'";
				$probenumlist = $probenumber;
			//}
		}
		else{
			// The case where someone is searching on multiple genes...
			$queryparams = " Refseq =";
			// Create the array of primary names to search on...
			$refseqnumberarray = array();
			$refseqnumberarray = explode(",", $refseq);
			$countcheck = 0;
			#$refseqnumberarraynum = count($refseqnumberarray);
			$refseqnumberarraynum = 0;
			# inefficient, but simpler than modifying code below...
			foreach($refseqnumberarray as $value){
				$ref = trim($value);
				if($ref != ""){
					$refseqnumberarraynum++;
				}
			}
			foreach($refseqnumberarray as $refseqnumbervalue){
				$refseqnumbervalue = trim($refseqnumbervalue);
				if($refseqnumbervalue != ""){
					if($countcheck < $refseqnumberarraynum - 1){
						$queryparams .= " '$refseqnumbervalue' OR Refseq = ";
						// used to display in query parameters table...
						$primarylist .= "'$refseqnumbervalue' OR ";
					}
					else{
						$queryparams .= " '$refseqnumbervalue' ";
						// used to display in query parameters table...
						$primarylist .= "'$refseqnumbervalue'";
					}
					$countcheck++;
				}
			}
		}
	
}



	// Now need to get the FeatureNums...
	if($refseqsPresent == 1){
		if($dataset == 1){
			$sql = "SELECT FeatureNum, ProbeName FROM agilent".$arraytype."_extendedannotations WHERE $queryparams"; /* AND (LogRatio >= $upperbound OR LogRatio <= $lowerbound)";*/
		}
		else{
			$sql = "SELECT arrayid, FeatureNum, GeneName FROM agilentcondenseddata WHERE $queryparams";
		}
		#echo "$sql<br>";

		$returnRefseqResult = $db->Execute($sql);//mysql_query($sql, $db);
	}



//echo " LINE 418: $sql<hr>";

$arraydesclookupsql = "SELECT id, organism, arraydesc, version FROM agilentarrays WHERE version LIKE \"%$arraytype%\"";
//echo $arraydesclookupsql;
	$arraydescresult = $db->Execute($arraydesclookupsql);//mysql_query($arraydesclookupsql, $db);
	#list($thisorganism, $organism, $arraydesc, $version) = mysql_fetch_array($arraydescresult);
	$thisrow = $arraydescresult->FetchRow();
	$thisorganism = $thisrow[0];
	$organism = $thisrow[1];
	$arraydesc = $thisrow[2];
	$version = $thisrow[3];
	$arrayurl = "./agilentarrayinfo.php?arrayid=\n";
	require './phpinc/organismurlselection.inc';

?>

<p>
<table class="question">
<thead>
<tr>
<th colspan="2">Query Parameters</th>
</tr>

</thead>
<tr>
<td class="questionparameter"><strong>Array Platform:</strong></td>
<td class="questionanswer"> <?php echo $arraydesc." ".$version; ?></td>
</tr>
<tr>
<td class="questionparameter"><strong>Gene Name(s):</strong></td>
<td class="questionanswer"> <?php echo $primaryname; ?></td>
</tr>
<!--
<tr>
<td class="questionparameter"><strong>Agilent Probe Number Value(s):</strong></td>
<td class="questionanswer"> <?php echo $probenumber; ?></td>
</tr>
-->
<tr>
<td class="questionparameter"><strong>Agilent Feature Number Value(s):</strong></td>
<td class="questionanswer"> <?php echo $featurenumber; ?></td>
</tr>
<tr>
<td class="questionparameter"><strong>Refseq Value(s):</strong></td>
<td class="questionanswer"> <?php echo $refseq; ?></td>
</tr>
<td class="questionparameter"><strong>Minimal Induction:</strong></td>
<td class="questionanswer"> <?php echo $_POST['rval']; ?></td>
</tr>
<tr>
<td class="questionparameter"><strong>Minimal Repression:</strong></td>
<td class="questionanswer"> <?php echo $_POST['lval']; ?></td>
</tr>
</table>
</p>
<?php

$clonearray = array();
if($genesPresent == 1){
	//echo "From genes <br>";
	#while($row = mysql_fetch_row($returnResult)){
	while($row = $returnResult->FetchRow()){
				$cloneid = $row[0];
				array_push($clonearray, $cloneid);
				//echo "$cloneid <br>";
				//$primaryname = $row[1];
				//$name = str_replace("\"","", $primaryname);
				//echo "<p>$cloneid</p>";
	}
}
if($featuresPresent == 1){
	//echo "From REFSEQ <br>";
	while($row = $returnFeatureNumResult->FetchRow()){
				$cloneid = $row[0];
				//echo "$cloneid <br>";
				array_push($clonearray, $cloneid);
	}
}
/*
if($probesPresent == 1){
	//echo "From GOID <br>";
	while($row = mysql_fetch_row($returnProbeNameResult)){

				$cloneid = $row[0];
				//echo "$cloneid <br>";
				array_push($clonearray, $cloneid);
	}
}
*/
if($refseqsPresent == 1){
	//echo "From GOID <br>";
	while($row = $returnRefseqResult->FetchRow()){

				$cloneid = $row[0];
				//echo "$cloneid <br>";
				array_push($clonearray, $cloneid);
	}
}

if(isset($_SESSION['priv_level'])){
	$privval = $_SESSION['priv_level'];
}else{
	$privval = "";
}

if($privval == ""){
	$privval = 1;
}
else{
	$privval = $privval;
}

/*if($priv != 99){

		// Get the array ids they can access.....
		$arrayidsql = "SELECT arrayid FROM array WHERE ownerid = 1 OR ownerid = $priv ORDER BY arrayid";
		$arrayResult = mysql_query($arrayidsql, $db);
		$arrayArray = array();
		while($row = mysql_fetch_row($arrayResult)){
			$arrayStr = "arrayid = $row[0]";
			array_push($arrayArray, $arrayStr);
			$or = "OR";
			array_push($arrayArray, $or);
		}
		// Pop the last or off...
		array_pop($arrayArray);
		$arrayidsqlstring = " AND (";
		foreach($arrayArray as $item){
			$arrayidsqlstring .= " $item ";
		}
		$arrayidsqlstring .= ")";
		//echo $arrayidsqlstring;

	}

*/

//echo "Clone list <br>";
$clonesql = "";
sort($clonearray);
$clonearraynum = count($clonearray);
//echo "<p>The number of clones meeting the criteria: $clonearraynum</p>";
$cloneparams = "";
$clonelist = "";
if(count($clonearray) > 0){
	if(!isset($arrayidsqlstring)){
		$arrayidsqlstring = "";
	}
		$countcheck = 0;
		$cloneparams = "FeatureNum =";
		foreach($clonearray as $clonevalue){
			//echo "$clonevalue <br>";
			if($countcheck < $clonearraynum - 1){
				//$cloneparams .= " $clonevalue $arrayidsqlstring and (LogRatio <= $lowerbound or LogRatio >= $upperbound)) OR (FeatureNum = ";
				$cloneparams .= " $clonevalue $arrayidsqlstring OR FeatureNum = ";
				// used to display in query parameters table...
				//$clonelist .= "$clonevalue $arrayidsqlstring and (LogRatio <= $lowerbound or LogRatio >= $upperbound) OR ";
				$clonelist .= "$clonevalue $arrayidsqlstring OR ";
			}
			else{
				$cloneparams .= " $clonevalue $arrayidsqlstring";
				// used to display in query parameters table...
				if(isset($namevalue)){
					$clonelist .= "$namevalue";
				}
			}
			$countcheck++;
		}
	
	//$ownerid = $_SESSION['userid'];
	$arrayvaluesql = "SELECT ae.arrayid FROM agilent_experiments AS ae, agilent_experimentsdesc AS aed WHERE aed.ownerid = $privval AND aed.expid=ae.expid";
	//echo "$arrayvaluesql<br>";
	$arrayvaluesqlresult = $db->Execute($arrayvaluesql);//mysql_query($arrayvaluesql, $db);
	
	$arrayidarray = array();
	$arraycount = 0;
	while($row = $arrayvaluesqlresult->FetchRow()){
		array_push($arrayidarray, "arrayid = ");
		array_push($arrayidarray, $row[0]);
		array_push($arrayidarray, " OR ");
		$arraycount++;
	}
	
	// pop off the last comma.... 
	array_pop($arrayidarray);
	
	$arrayparams = "";
	if($arraycount != 0){
		foreach($arrayidarray as $val){
			$arrayparams.= "$val";
		}
	}

	//echo "<p>here are the query params: $cloneparams</p>";

	//echo "<br>there are $countcheck clones<br><hr>";
	// Get the arrayids that have an ownerid of 1 or that of the user's priv_level....  if priv_level = 99 then bring back all...
	$getarraytype = "SELECT id FROM agilentarrays WHERE version LIKE \"%$arraytype%\"";
	//echo "<br> $getarraytype<br>";
	$getarraytyperesult = $db->Execute($getarraytype);//mysql_query($getarraytype,$db);
	$arraytypeval = $getarraytyperesult->FetchRow();
	$arraytypeval = $arraytypeval[0];
	if($privval == 99){
	
		if($dataset == 1){
			$sql = "SELECT FeatureNum, arrayid, LogRatio, arrayversion FROM agilentdata WHERE ($cloneparams) AND (LogRatio <= $lowerbound or LogRatio >= $upperbound) AND arrayversion = $arraytypeval ORDER BY LogRatio DESC";
			$countsql = "SELECT COUNT(*) FROM agilentdata $cloneparams ORDER BY FeatureNum";
		}
		else{
			$sql = "SELECT cloneid, arrayid, ROUND(finalratio, 3) FROM condensedhybrids $cloneparams ORDER BY finalratio DESC";
			$countsql = "SELECT COUNT(*) FROM agilentdata $cloneparams ORDER BY FeatureNum";
		}
		//echo "priv = 99<br>";
		//echo "LINE 559: $sql<hr>";

	}
	else{
		#  There might be a more efficient way of doing this, but what this code is doing is looking at what arrays the user is associated w/ and then getting the distinct organisms
		#  that they have arrays for.

			# get the experiments they own....
			$expidSQL = "SELECT expid FROM agilent_experimentsdesc WHERE ownerid = $userid";
			$expidResult =  $db->Execute($expidSQL);
			$expidarray = array();
			while($row = $expidResult->FetchRow()){
				$expid = $row[0];
				array_push($expidarray,$expid);
			}
			
			# are there any groups that this user is in? if so, get the expids
			$usergroupsql = "SELECT usergroupid FROM usergroupmembers WHERE userid = $userid";
			$usergroupresult = $db->Execute($usergroupsql);
			$usersgroupexps = array();
			if($usergroupresult->RecordCount() > 0){
				// What are the experiment/user group associations for this user?
				while($row = $usergroupresult->FetchRow()){
					$assocSQL = "SELECT expid FROM expusergroupassoc WHERE usergroupid = $row[0]";
					$assocResult = $db->Execute($assocSQL);
					while($exprow = $assocResult->FetchRow()){
						$expid = $exprow[0];
						array_push($expidarray,$expid);
						
					}
				}
			}
			
			# get the array ids associated w/ these experiments.
			$arrayidarray = array();
			foreach($expidarray as $anexpid){
				$sql = "SELECT arrayid FROM agilent_experiments WHERE expid = $anexpid";
				$result = $db->Execute($sql);
				while($row = $result->FetchRow()){
					array_push($arrayidarray, $row[0]);
				}
			}
			
		
		/*
			if($ownerid != 99){
				$arrayvaluesql = "SELECT ae.arrayid FROM agilent_experiments AS ae, agilent_experimentsdesc AS aed WHERE aed.ownerid = $ownerid AND aed.expid=ae.expid";
			}else{
				$arrayvaluesql = "SELECT ae.arrayid FROM agilent_experiments AS ae, agilent_experimentsdesc AS aed WHERE aed.expid=ae.expid";
			
			}
			
			echo $arrayvaluesql;
			$arrayvaluesqlresult = $db->Execute($arrayvaluesql);
			
			$arrayidarray = array();
		*/
			$arraycount = 0;
			#while($row = $arrayvaluesqlresult->FetchRow()){
			$arrayidarraysql = array();
			foreach($arrayidarray as $anid){
				array_push($arrayidarraysql, "arrayid = ");
				array_push($arrayidarraysql, $anid);
				array_push($arrayidarraysql, " OR ");
				$arraycount++;
			}
		
			// pop off the last comma.... 
			array_pop($arrayidarraysql);
			
			$arrayparams = "";
			if($arraycount != 0){
				foreach($arrayidarraysql as $val){
					$arrayparams.= "$val";
				}
			}
	
		if($dataset == 1){
			$sql = "SELECT FeatureNum, arrayid, LogRatio FROM agilentdata WHERE ($cloneparams)  AND ($arrayparams) AND (LogRatio <= $lowerbound or LogRatio >= $upperbound)  AND arrayversion = $arraytypeval ORDER BY LogRatio DESC";
			$countsql = "SELECT COUNT(*) FROM agilentdata $cloneparams  AND $arrayparams ORDER BY FeatureNum";
		}
		else{
			$sql = "SELECT cloneid, arrayid, ROUND(finalratio, 3) FROM condensedhybrids $cloneparams AND $arrayparams  ORDER BY finalratio DESC";
			$countsql = "SELECT COUNT(*) FROM condensedhybrids $cloneparams  AND $arrayparams ORDER BY cloneid";
		}
	}

	//echo "LINE 624:  $sql</p>";
	if($userid == $debug){
		echo "<p>here's sql: $sql</p>";
	}
	//echo "<p>$countsql</p>";
	//echo "<br>$countsql<br>";
	//exit(0);
	$returnResult2 = $db->Execute($sql);#mysql_query($sql, $db);
	//$returnResult3 = mysql_query($countsql, $db);

	//$row = mysql_fetch_row($returnResult3);
	$countValue = $returnResult2->RecordCount();

	?>
	
	

<?php echo "<h3>$countValue Gene/Treatment Combinations Satisfy your Search Criteria</h3>"; ?>


<table id="results">
	<thead>
<col width=10 align="center">
  <col width=128 align="left">
  <col width=20 align="left">
  <col width=20 align="left">
  <col width=20 align="left">
  <col width=20 align="left">
  <col width=20 align="left">
  <col width=20 align="left">
<tr>
<th scope="col" abbr="number">Feature Number</th>
<th scope="col"  abbr="alpha">Gene Name</th>
<th scope="col"  abbr="alpha">Refseq</th>
<th scope="col"  abbr="number">Experiment</th>
<th scope="col"  abbr="number">Fold Change</th>
</tr>
</thead>

	<?php
	$arrayidarray = array();
	$counter = 0;
	while($row = $returnResult2->FetchRow()){
			
			echo"<tr>";
			
			$cloneid = $row[0];
			$arrayid = $row[1];
			array_push($arrayidarray, $arrayid);
			$finalratio = $row[2];
			// SQL to get the primaryname...
			if($dataset == 1){

//SELECT FeatureNum, GeneSymbol FROM agilent".$arraytype."_extendedannotations WHERE $queryparams
				$pnsql = "SELECT GeneSymbol,Refseq FROM agilent".$arraytype."_extendedannotations WHERE FeatureNum = $cloneid";
			}
			else{
				$pnsql = "SELECT GeneSymbol,Refseq FROM agilent".$arraytype."_extendedannotations WHERE FeatureNum = $cloneid";
			}
			$pnResult = $db->Execute($pnsql);#mysql_query($pnsql, $db);
			$pnRow = $pnResult->FetchRow();
			$primaryname = str_replace("\"","", $pnRow[0]);
			$refseq = $pnRow[1];

			if($dataset == 1){
				
				echo "<td style=align=\"center\"><a href='$featureurl$cloneid' target='_blank'>$cloneid</a></td>";

			}else{
				echo "<td style=align=\"center\">$cloneid</td>";
			}
			
			$arraynamesql = "SELECT arrayid, arraydesc FROM agilent_arrayinfo WHERE arrayid = $arrayid";
			$arraynameResult = $db->Execute($arraynamesql);#mysql_query($arraynamesql, $db);
			$arrayname = $arraynameResult->FetchRow();
			#echo "$arraynamesql<br>";
			echo "<td class=\"results\" width=\"100px\">$primaryname</td>
			<td style=align=\"center\"><a href='http://www.ncbi.nlm.nih.gov/sites/entrez?db=gene&cmd=search&term=$refseq' target='_blank'>$refseq</a></td>
   			<td style=\"align=\"center\"><a href='$arrayurl$arrayname[0]' target='_blank'>

				$arrayname[0] : $arrayname[1]
			</a></td>";
			$finalratio = log10ToFoldChange($finalratio);
			$finalratio = round($finalratio,2);
			$featurevals = "<a href='$featureurl$cloneid"."_"."$arrayname[0]' target='_blank'>";
			if($finalratio > 1){

				echo "<td class=\"gtzero\">$featurevals$finalratio</a></td>";
			}
			elseif($finalratio < 1){

				echo "<td  id=\"ltzero\" class=\"ltzero\">$featurevals$finalratio</a></td>";
			}
			else{

				echo "<td class=\"nochange\">$featurevals$finalratio</a></td>";
			}
			echo "</tr>";
			$counter++;
	}
	echo "</table>";
	
}
if(!isset($countValue)){
		$noresults = "There were no results returned";
	}else{
		$noresults = "";
	}
  $end = utime(); $run = $end - $start;
	   echo "<font size=\"3px\" color=\"red\"><b>$noresults</b></font><br>";
	 echo "<font size=\"1px\"><b>Query results returned in ";
	 echo substr($run, 0, 5);
	 echo " secs.</b></font>";

?>
</p>
<?php
}
?>
</div>
