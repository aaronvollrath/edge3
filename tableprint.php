<?php

/* Remarks:  This page is responsible for outputting printable tables of the results returned.
*/


require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../EDGE/default.php">Click here to go back to the main page</a>');
}

require './phpinc/edge3_db_connect.inc';

$rslookupSQL = stripslashes($_POST['sql']);
//echo "here's rslookupSQL: $rslookupSQL<br>";
$chemtext = $_POST['chem'];
$sex = $_POST['sex'];
$age = $_POST['age'];
$tissue = $_POST['tissue'];
$route = $_POST['route'];
$dose = $_POST['dose'];
$ordertext = $_POST['ordertext'];
$totalrows = $_POST['totalrows'];
$listtype = $_POST['list'];
//echo "Here's list type: $listtype<br>";
$duration = $_POST['duration'];
$durationlogic = $_POST['durationlogic'];
$chemlogic = $_POST['chemlogic'];
$sexlogic = $_POST['sexlogic'];
$agelogic = $_POST['agelogic'];
$tissuelogic = $_POST['tissuelogic'];
$routelogic = $_POST['routelogic'];



// The following are applicable to sample clone lists....
$checkall = $_POST['all'];
$sampleid = $_POST['sampleid'];
$orderby = $_POST['orderby'];
$sort = $_POST['sort'];
$lcomp = $_POST['lcomp'];
$lval = $_POST['lval'];
$rcomp = $_POST['rcomp'];
$rval = $_POST['rval'];
$filename = $_POST['image'];
$samptype = $_POST['samptype'];
$clones = $_POST['clones'];
$showimage = $_POST['showimage'];
//echo "Here are the clones:$clones";
//echo "Here is showimage: $showimage<br>";
?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout" />
<title>EDGE^2</title>
</head>

<body>

<?php
		include 'banner.inc';
	?>
 <div class="boxleft">

<?php

//echo "$sql";/*
if($listtype == "clone"){
?>

<table>
<tr>
<td>
<table class="results" width="640" valign="top" align="left" border="1">
<tr bgcolor="9999FF">
	<td colspan=5><b>Your query parameters:</b></td>
	</tr>
	<tr bgcolor="FFFF99">
 	<td width="25%" align="right"><b>Chemical:</b></td><td width="25%"><?php echo $chemtext; ?></td><td width="25%" align="right"><b>Tissue:</b></td><td width="25%"><?php echo $tissue; ?></td>
	<td rowspan=4>	</td>
	</tr>
	<tr bgcolor="FFFF99">
 	<td width="25%" align="right"><b>Sex:</b></td><td width="25%"><?php echo $sex; ?></td><td width="25%" align="right"><b>Route:</b></td><td width="25%"><?php echo $route; ?></td>
	</tr>
	<tr bgcolor="FFFF99">
 	<td width="25%" align="right"><b>Age(in weeks):</b></td><td width="25%"><?php echo $age; ?></td><td width="25%" align="right"><b>Dosage:</b></td><td width="25%"><?php echo $dose; ?></td>
	</tr>
	<tr bgcolor="FFFF99">
	<td width="25%" align="right"><b>Duration:</b></td><td width="25%" align="left"><?php echo $duration; ?></td>
	<td width="25%" align="right"><b>Ordered By:</b></td><td width="25%"><?php echo $ordertext; ?></td>
	</tr>
	<tr bgcolor="FFFF99">
	<td width="25%" align="right"><b>Number returned:</b></td><td width="25%" align="left"><?php echo $totalrows; ?></td>
	<td colspan=2"></td>
	</tr>
</table>
</td>
</tr>
<tr>
<td>

<table class="results" width="640" valign="bottom" align="left" border="1"  >

<tr bgcolor="9999FF">
	<td><center><b>Chip<br>Version</b><center></td><td><center><b>Clone</b><br><b>ID</b><center></b></td><td><b>Refseq</b></td><td><b>5' confirm</b></td>
</tr>
<?php

			$returnResult = mysql_query($rslookupSQL, $db);
			//echo "Here is the SQL: $rslookupSQL";
			$bgcolorcount = 0;

				while(list($versionid, $cloneid, $_5refseq, $_5confirm) = mysql_fetch_array($returnResult)){
					if($bgcolorcount%2){
						// number is odd..
						$bgcolor = "CCCCCC";
					}
					else{
						// number is odd...
						$bgcolor = "FFFF99";
					}
					echo "<tr bgcolor=\"$bgcolor\">

<td valign=top><center>$versionid</center></td><td valign=top>$cloneid</td>
<td valign=top>$_5refseq</td>
<td valign=top>$_5confirm</td></tr>";
					$bgcolorcount++;
				}
}
elseif($listtype == "sampleclonelist"){
// Need to check to see if $orderby is blank....
if($orderby == ""){
$orderby = "ratios.cloneid";
}

//}
// Need to save these current values for later...
$lcomptemp = $lcomp;
$rcomptemp = $rcomp;
// Need to convert $lcomp and $rcomp....
if($lcomp == "lt"){
	$lcomp = "<";
	$lcomptext = "less than";
}
elseif ($lcomp == "lte"){
	$lcomp = "<=";
	$lcomptext = "&lt; or =";
}
elseif($lcomp == "gt"){
	$lcomp = ">";
	$lcomptext = "greater than";
}
elseif($lcomp == "gte"){
	$lcomp = ">=";
	$lcomptext = "&gt; or =";
}
elseif ($lcomp == "e"){
	$lcomp = "=";
	$lcomptext = "equals";
}
else{
	$lcomp = "";
	$lcomptext = "";
}
if($rcomp == "gt"){
	$rcomp = ">";
	$rcomptext = "greater than";
}
elseif ($rcomp == "gte"){
	$rcomp = ">=";
	$rcomptext = "&gt; or =";
}
elseif($rcomp == "lt"){
	$rcomp = "<";
	$rcomptext = "less than";
}
elseif($rcomp == "lte"){
	$rcomp = "<=";
	$rcomptext = "&lt; or =";
}
elseif ($rcomp == "e"){
	$rcomp = "=";
	$rcomptext = "equals";
}
else{
	$rcomp = "";
	$rcomptext = "";
}
$returnSQL = "SELECT sampleid, chemid, rnagroupsize, organism, strain, genevariation, age, sex, tissue,
		treatment, vehicle, dose, route, dosagetime, duration,
		harvesttime, control from sampledata where sampleid = '$sampleid'";
$returnResult = mysql_query($returnSQL, $db);
list($sampID, $chemID, $rnagroupsize, $organism, $strain, $genevariation, $age, $sex, $tissue, $treatment,
$vehicle,$dose, $route, $dosagetime, $duration, $harvesttime,$control ) = mysql_fetch_array($returnResult);
?>
<table class="results" width="640" border="1">
<tr bgcolor=CCCCCC>
	<td colspan="4"><label><h2><font color=#0000ff>Sample Information</font></h2></label></td>
</tr>
<tr>
	<td colspan="2"><h3><u>Organism Information</u></h3></td>
	<td colspan="2"><h3><u>Treatment Information</u></h3></td>
</tr>
<tr>
	<td width="20%"><b>Organism</b></td><td width="20%"><?php echo $organism; ?></td>
	<td width="20%"><b>Treatment</b></td><td width="20%"><?php echo $treatment; ?></td>
</tr>
<tr>
	<td width="20%"><b>Strain</b></td><td width="20%"><?php echo $strain; ?></td>
	<td width="20%"><b>Vehicle</b></td><td width="20%"><?php echo $vehicle; ?></td>
</tr>
<tr>
	<td width="20%"><b>Genetic Variation</b></td><td width="20%"><?php echo $genevariation; ?></td>
	<td width="20%"><b>Dose</b></td><td width="20%"><?php echo $dose; ?></td>
</tr>
<tr>
	<td width="20%"><b>Age</b></td><td width="20%"><?php echo $age; ?></td>
	<td width="20%"><b>Route</b></td><td width="20%"><?php echo $route; ?></td>
</tr>
<tr>
	<td width="20%"><b>Sex</b></td><td width="20%"><?php echo $sex; ?></td>
	<td width="20%"><b>Dosage Time</b></td><td width="20%"><?php echo $dosagetime; ?></td>
</tr>
<tr>
	<td width="20%"><b>Tissue</b></td><td width="20%"><?php echo $tissue; ?></td>
	<td width="20%"><b>Duration</b></td><td width="20%"><?php echo $duration; ?></td>
</tr>
<tr>
	<td width="20%"><b>RNA Group Size</b></td><td width="20%"><?php echo $rnagroupsize; ?></td>
	<td width="20%"><b>Harvest Time</b></td><td width="20%"><?php echo $harvesttime; ?></td>
</tr>
<tr>
	<td width="20%"></td><td></td>
	<td width="20%"><b>Control</b></td><td width="20%"><?php echo $control; ?></td>
</tr>
<tr>
	<td width="20%"></td><td></td>

</tr>
</table>

<?php
// Now need to get all of the clone information associated w/ this sample...
// First need to get the arrayid value associated w/ this particular sample...
$arrayidSQL = "SELECT arrayid,versionid from array where sampleid = $sampleid";
$arrayidResult = mysql_query($arrayidSQL, $db);
//echo "Here is the sql: $arrayidSQL";
$row = mysql_fetch_row($arrayidResult);
$arrayid = $row[0];
$versionid = $row[1];
//echo "Here is the arrayid: $row[0]";

if($samptype != "gsl"){

if($checkall == "on"){
$cloneSQL = "Select ratios.cloneid, ratios.finalratio from ratios where ratios.arrayid = $arrayid order by $orderby $sort";
$countSQL = "Select count(*) from ratios where ratos.arrayid = ratios.arrayid and ratios.arrayid = $arrayid order by $orderby $sort";
$countResult = mysql_query($countSQL, $db);
$row = mysql_fetch_row($countResult);
}
else{
 // Let's get specific.....
// Got the arrayid, now need to get the related hybrid and ratios information....
	// need to determine if there's anything for leftbound...
	if($lcomp == "" && $rcomp == ""){
		$leftbound = "";
		$rightbound = "";
		$rangetext = "No range selected, all clones selected.";
	}
	elseif($lcomp != "" && $rcomp == ""){
		// only leftbound....
		$leftbound = "and (ratios.finalratio $lcomp $lval)";
		$rangetext = "Relative expression ratio $lcomptext $lval";
	}
	elseif($lcomp == "" && $rcomp != ""){
		$rightbound = "and (ratios.finalratio $rcomp $rval)";
		$rangetext = "Relative expression ratio $rcomptext $rval";
	}
	else{
		$leftbound = "and (ratios.finalratio $lcomp $lval or ";
		$rightbound = "ratios.finalratio $rcomp $rval)";
		$rangetext = "Relative expression ratio $lcomptext $lval and $rcomptext $rval";
	}
$cloneSQL = "Select ratios.cloneid, ratios.finalratio from ratios where ratios.arrayid = $arrayid $leftbound $rightbound order by $orderby $sort";
//echo "Here's the cloneSQL: $cloneSQL<br>";
$countSQL = "Select Count(*) from ratios where ratios.arrayid = $arrayid $leftbound $rightbound order by $orderby $sort";
//echo "Here's the countSQL: $countSQL<br>";
$countResult = mysql_query($countSQL, $db);
$row = mysql_fetch_row($countResult);

}
} // end if samptype!="gsl"
else{

// Take $clones and extract out all the values into an array...

$idarray = explode(" ", $clones);

$elements = count($idarray);
//$idarrayclone = $idarray;
//echo "# of elements: $elements<br>";
$idcount = 0; //reset and reuse idcount...
$rhSQL = "";
// pop the last space off...
array_pop($idarray);
$idcount = 1;  // because we popped the last space off...
//array_shift($idarray);

while($idcount<$elements){
	// Need to check to see if we need to add the last OR... inefficient...but works...
	if($idcount == ($elements - 1)){
		$orVal = "";
	}
	else{
		$orVal = "OR";
	}
	$thisclone = array_pop($idarray);
	$rhSQL .= "ratios.cloneid = $thisclone and ratios.cloneid = $thisclone $orVal ";
	$idcount++;
}



$cloneSQL = "Select ratios.cloneid, ratios.finalratio from ratios where ratios.arrayid = $arrayid and ($rhSQL) order by $orderby $sort";
//echo "Here's the cloneSQL: $cloneSQL<br>";
$countSQL = "Select Count(*) from ratios where ratios.arrayid = $arrayid and ($rhSQL) order by $orderby $sort";
//echo "Here's the countSQL: $countSQL<br>";
$countResult = mysql_query($countSQL, $db);
$row = mysql_fetch_row($countResult);
$rows = $row[0];



}
?>
<table class="results" width="640">
<tr>
<td width="640">

<?php

if($orderby == "ratios.cloneid"){
		$orderbytext = "Clone ID";
	}
	else{
		$orderbytext = "Relative Expression Ratio";
	}
	if($sort == "asc"){
		$sorttext = "Ascending";
	}
	else{
		$sorttext = "Descending";
	}
//echo "<br>Heres checkall: $checkall<br>";
if($checkall == "on"){

?>

<table class="results" width="640"border="1" >
<tr><td colspan=2 bgcolor=CCCCCC><b><font color=#0000ff>Your Search Criteria:</font></b></td>
</tr>
<tr>
<td ><font color=#ff0000><b>All Clones Selected</b></font></h1></td></tr>
</tr>
<tr>
<td align="left"><b>Ordered By:</b> <?php echo $orderbytext; ?></td>
</tr>
<tr>
<td align="left"><b>Sort:</b> <?php echo $sorttext; ?></td>
</tr>
<tr>
<td align="left"><b>Number Returned:</b> <?php echo $row[0]; ?></td>
</tr>
</table>
</td>
<?php
}
else{

?>
<table class="results" width="640" border="1">
<tr bgcolor=CCCCCC><td colspan=3><b><font color=#0000ff>Your Search Criteria:</font</b></td></tr>
<tr><td></td><td align="right"><b>Relative Expression Ratio Range:</b></td>
<td><?php echo $rangetext; ?></td>
</tr>
<tr>
<td></td><td align="right"><b>Ordered By:</h3></td><td><?php echo $orderbytext; ?></b>
</tr>
<tr>
<td></td><td align="right"><b>Sort:</b></td><td><?php echo $sorttext; ?></td>
</tr>
<tr>
<td></td><td align="right"><b>Number Returned:</h3></td><td><?php echo $row[0]; ?></b>
</tr>
</table>
</td>
<?php


}
//echo "<br> Heres the query: $cloneSQL<br>";
$cloneResult = mysql_query($cloneSQL, $db);

if($showimage == 1){
?>
<table class="results" width="640" border="1">
<tr>
<td>
<img src='<?php echo "./IMAGES/$filename"; ?>'border=0 align=center width=640 height=450>

</td>

</tr>

</table>
<?php
}
?>

<table class="results" width="640" border="1">
<tr bgcolor="9999FF">
	<td width=50>Clone ID</td><td>Annotated<br>Name</td><td>Refseq</td><td>Accession<br>Number</td><td width=60>Final Ratio</td>
</tr>
<?php
$bgcolorcount = 0;
while(list($cloneid, $finalratio)= mysql_fetch_array($cloneResult))
{

	if($bgcolorcount%2){
		// number is odd..
		$bgcolor = "CCCCCC";
		}
		else{
		// number is odd...
		$bgcolor = "FFFF99";
		}

	$infoSQL = "Select cloneid, 5refseq, 5accession from cloneinfo where versionid = $versionid
		and cloneid = $cloneid";
	$infoResult = mysql_query($infoSQL, $db);
	$row = mysql_fetch_row($infoResult);
	$cloneid2 = $row[0];
	$refseq = $row[1];
	$accession = $row[2];
	echo "<tr bgcolor=\"$bgcolor\"><td><a href=\"http://genome.oncology.wisc.edu/EDGE/cloneinfo.php?cloneid=$cloneid&versionid=$versionid&arrayid=$arrayid\">$cloneid</a></td><td>To Be Implemented</td><td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$refseq\">$refseq</a></td><td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Search&db=Nucleotide&dopt=GenBank&term=$accession\">$accession</a></td><td>$finalratio</td></tr>";
	//$rows .= "<tr><td>$cloneid</td><td>$finalratio</td></tr>";
	$bgcolorcount++;
}
?>



<?php
}
else{ // this is a sample list.....
?>
<table>
<tr>
<td>
<tr valign="top">
<td>
	<table class="results" width="640" valign="top" height="10%" align="left" border="1" bgcolor="9999FF" >
	<tr>
	<td colspan=10><b>Your query parameters:</b></td>
	</tr>
	<tr bgcolor="FFFF99">
 	<td width="25%" align="right"><b>Chemical:</b></td><td width="25%"><?php echo $chemtext; ?></td><td width="25%" align="right"><b>Tissue:</b></td><td width="25%"><?php echo $tissue; ?></td>

	</tr>
	<tr bgcolor="FFFF99">
 	<td width="25%" align="right"><b>Sex:</b></td><td width="25%"><?php echo $sex; ?></td><td width="25%" align="right"><b>Route:</b></td><td width="25%"><?php echo $route; ?></td>
	</tr>
	<tr bgcolor="FFFF99">
 	<td width="25%" align="right"><b>Age(in weeks):</b></td><td width="25%"><?php echo $age; ?></td><td width="25%" align="right"><b>Dosage:</b></td><td width="25%"><?php echo $dose; ?></td>
	</tr>
	<tr bgcolor="FFFF99">
	<td width="25%" align="right"><b>Duration:</b></td><td width="25%"><?php echo $duration; ?></td>
	<td width="25% align="right"><b>Number returned:</b></td><td width="25%" align="left"><?php echo $totalrows; ?></td>
	</tr>
	</table>

</td>
</tr>
<tr align="left" valign="top">
<td align="left" valign="top">
<table class="results" class="results" width="640" valign="top" align="left" border="1" bgcolor="9999FF" >
<tr><td><b>SampleID</b></td><td><b>Organism</b></td><td><b>Strain</b></td><td><center><b>Gen.<br>Var.</center></b></td><td><b><center>Age<br>(in weeks)</center></b></td>
<td><b>Sex</b></td><td><b>Tissue</b></td><td><b>Treat-<br>ment</b></td><td><b>Vehicle</b></td><td><b>Dose</b></td>
<td><b>Duration</b></td>
<td><b>Route</b></td>
</tr>
<?php
//echo "<br> $rslookupSQL <br>";
$returnResult = mysql_query($rslookupSQL, $db);
$bgcolorcount = 0;
	while(list($sampID, $chemID, $organism, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle, $dose,
	$route, $duration) = mysql_fetch_array($returnResult)){
	if($bgcolorcount%2){
		// number is odd..
		$bgcolor = "CCCCCC";
		}
		else{
		// number is odd...
		$bgcolor = "FFFF99";
		}
	echo "<tr bgcolor=\"$bgcolor\"><td>Sample #$sampID</td><td>$organism</td><td>$strain</td><td>$genevariation</td>
	<td>$age</td><td>$sex</td><td>$tissue</td><td>$treatment</td><td>$vehicle</td><td>$dose</td>
	<td>$duration</td>
	<td>$route</td></tr>";
	$bgcolorcount++;
	}
?>


<?php
// end else sample list...
*/
}
?>

</td>
</tr>
</table>
</table>
</div>
</body>
</html>
