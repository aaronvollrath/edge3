<?php


require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';

$userid = $_SESSION['userid'];
//include 'header.inc';
include 'utilityfunctions.inc';
$cssclass = "tundra";


?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout" />
<title>EDGE^3</title>
<script type="text/javascript" src="./dojo-release-1.0.0/dojo/dojo.js"
                djConfig="parseOnLoad: true"></script>

    <script type="text/javascript">
        dojo.require("dojo.parser");


        dojo.require("dijit.layout.LayoutContainer");
       dojo.require("dijit.TitlePane");

	</script>

        <style type="text/css">
                @import "./dojo-release-1.0.0/dojo/resources/dojo.css";
                @import "./dojo-release-1.0.0/dijit/themes/<?php echo $cssclass; ?>/<?php echo $cssclass; ?>.css";
                @import "./dojo-release-1.0.0/dijit/demos/mail/mail.css";
		/* pre-loader specific stuff to prevent unsightly flash of unstyled content */
		#loader {
			padding:0;
			margin:0;
			position:absolute;
			top:0; left:0;
			width:100%; height:100%;
			background:#ededed;
			z-index:999;
			vertical-align:center;
		}
		#loaderInner {
			padding:5px;
			position:relative;
			left:0;
			top:0;
			width:400px;
			height:800px;
			background:#3c3;
			color:#fff;

		}

        </style>
<body class="<?php echo $cssclass; ?>" height="1800px">
<div dojoType="dijit.layout.LayoutContainer" id="mainDiv"
style="border: 1px solid #bfbfbf; float: left; width: 100%; height: 100%;overflow:auto;">
	<div dojoType="dijit.layout.ContentPane"
		orientation="horizontal"
		sizerWidth="5"
		activeSizing="0"
		selected="true"
	>
	<div class="header">

		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression Feature Information</font></h4>
</div><br>
<br>
<br>
<?php
// Parse out feature and arrayid...
$featurearray = explode("_", $_GET['featurenum']);
$featurenum = $featurearray[0];
if(isset($featurearray[1])){
	$arrayid = $featurearray[1];
}else{
	$arrayid = "";
}

if($arrayid != ""){
	//echo "Array ID = $arrayid<br>";
	$sql = "SELECT e.arraydesc, a.organism, a.arraydesc, a.version  FROM agilentarrays AS a, agilent_arrayinfo AS e WHERE e.arrayid = $arrayid and a.id = e.arraytype";

		$assocsql = "SELECT * FROM agilentdata WHERE FeatureNum = $featurenum AND arrayid = $arrayid";
		//if($userid==1){
			//echo "<br>$assocsql<br>";
		//}
		$assocResult = mysql_query($assocsql, $db);


		$assocrow=mysql_fetch_assoc($assocResult);
//echo "$sql<br>";
$sqlResult = mysql_query($sql, $db);
$row = mysql_fetch_row($sqlResult);

$expdesc = $row[0];
$organism = $row[1];
$arraydesc = $row[2];
$version = $row[3];
?>
<table id="results">
<tr><td>Array ID#</td><td><?php echo $arrayid; ?></td></tr>
<tr><td>Array Name</td><td><?php echo $expdesc; ?></td></tr>
<tr><td>Array Type</td><td><?php echo $arraydesc; ?></td></tr>
<tr><td>Organism</td><td><?php echo $organism; ?></td></tr>
<tr><td>Version</td><td><?php echo $version; ?></td></tr>


</table>

<?
	$sql = "SELECT ProbeUID, ProbeName, GeneName, SystematicName, Description, LogRatio, gProcessedSignal, rProcessedSignal, PValueLogRatio FROM agilentdata WHERE FeatureNum = $featurenum AND arrayid = $arrayid";

}else{
	//echo "Feature Number = $featurenum<br>";
	//$sql = "SELECT ProbeUID, ProbeName, GeneName, SystematicName, Description FROM agilentdata WHERE FeatureNum = $featurenum AND arrayid = 355"
	$sql = "SELECT ProbeUID, ProbeName, GeneSymbol, SystematicName, Description FROM agilentg2519f_extendedannotations WHERE FeatureNum = $featurenum";
}
#echo "<br>$sql<br>";
$sqlResult = mysql_query($sql, $db);
$row = mysql_fetch_row($sqlResult);

if($arrayid != ""){
$LogRatio = $row[5];
$gProcessed =$row[6];
$rProcessed =$row[7];
$pValue =$row[8];
}

$ProbeUID = $row[0];
$ProbeName = $row[1];
$GeneName = $row[2];
$SystematicName = $row[3];
$Description = $row[4];
if(isset($condensedclone)){
	$condensedclone = 1;
}else{
	$condensedclone = -1;
}

?>

<table id="results">
<tr><td>Gene Name</td><td><?php echo $GeneName; ?></td></tr>
<tr><td>Systematic Name</td><td><?php echo $SystematicName; ?></td></tr>
<tr><td>Description</td><td><?php echo $Description; ?></td></tr>

<?php
	if($condensedclone != 1){
?>
	<tr><td>Feature Number</td><td><?php echo $featurenum; ?></td></tr> 
	<tr><td>Probe UID</td><td><?php echo $ProbeUID; ?></td></tr>


	<tr><td>Probe Name</td><td><?php echo $ProbeName; ?></td></tr>
<?php
$ProbeName = str_replace('"', "", $ProbeName);
		$seqSQL = "SELECT Sequence, Refseq, GenBankAcc, LocusLinkID, UnigeneID, EnsemblID, TigrID, ChromosomalLocation, Cytoband, GoID FROM agilentg2519f_extendedannotations WHERE FeatureNum = $featurenum";
		#echo "$seqSQL<br>";
		$seqResult = mysql_query($seqSQL, $db);
		$seqacc = mysql_fetch_assoc($seqResult);
		//echo "Sequence: $seqrow[0]";
		//echo "<tr><td>Probe Sequence</td><td>$seqrow[0]</td></tr>";
	//}

	}
if($arrayid != ""){
	$FoldChange = log10ToFoldChange($LogRatio);
	if($condensedclone != 1){
?>
<tr><td>Fold Change</td><td><?php echo round($FoldChange, 2); ?></td></tr>
<tr><td>Cy3 Processed Signal</td><td><font color="green"><?php echo round($gProcessed, 2); ?></font></tr>
<tr><td>Cy5 Processed Signal</td><td><font color="red"><?php echo round($rProcessed, 2); ?></font></tr>
<tr><td>pValue</td><td><font color="red"><?php echo $pValue; ?></font></tr>
<?php
	}
	else{
?>
		<tr><td>Condensed Fold Change</td><td><?php echo round($FoldChange, 2); ?></td></tr>
<?php	}
}
?>
<tr><td>Probe UID</td><td><?php echo $ProbeUID; ?></td></tr>
<tr><td>Probe Name</td><td><?php echo $ProbeName; ?></td></tr>
<?php
if($arrayid != ""){
	$FoldChange = log10ToFoldChange($LogRatio);
?>
<tr><td>Fold Change</td><td><?php echo $FoldChange; ?></td></tr>
<?php
}
?>
</table>
<?php



$width = 600;
$tableid = "results";
?>
<table width="600">
<tr>
<td>
<div dojoType='dijit.TitlePane' title='Probe Sequence and Annotations' open='false' width="600">
<?php
$arraytype="g2519f";
annotationstotable($seqacc, $width, $tableid,$arraytype);
//echo "Coming soon!";

?>
</div>
</td>
</tr>
</table>
<?php
if($condensedclone != 1 && $arrayid != ""){
?>
<table width="600">
<tr>
<td>
<div dojoType='dijit.TitlePane' title='All Data Values' open='false' width="600">
<?php
array2table($assocrow, $width, $tableid);
?>
</div>
</td>
</tr>
</table>
<?php




}

?>

</body>
