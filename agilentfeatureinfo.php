<?php
session_start();
require 'edge_db_connect2.php';
//require './phpinc/edge3_db_connect.inc';
$userid = $_SESSION['userid'];
$debug = 0;
//include 'header.inc';
include 'utilityfunctions.inc';
$cssclass = "tundra";

 function utime (){
$time = explode( " ", microtime());
$usec = (double)$time[0];
$sec = (double)$time[1];
return $sec + $usec;
}
$start = utime();
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
</div>
<br>
<br>
<br>
<?php



// Parse out feature and arrayid...
$featurearray = explode("_", $_GET['featurenum']);
$featurenum = $featurearray[0];

 /*if($featurenum == "rat") {
    //echo "this is a rat array<BR>";
    $thisorganism = 1;
    $featurenum = $featurearray[1];
  }else{
*/

if($debug == 1){
    analyze($featurearray);
}
    if(isset($featurearray[1])){
        $arrayid = $featurearray[1];
    }else{
        $arrayid = "";
    }
  //}
if($featurenum < 0){
    // We are dealing w/ a condensed clone....
    $condensedclone = 1;
    $featurenum = $featurenum * -1;
    $arrayid = $featurearray[1];
}else{
    $condensedclone = 0;
   
}

if(isset($_GET['arraytype'])){
    $thisorganism = $_GET['arraytype'];
}else{
    $thisorganism = "";
}

//echo "arrayid: $arrayid<br>";
if($arrayid != ""){
    //echo "Array ID = $arrayid<br>";
    $sql = "SELECT e.arraydesc, a.organism, a.arraydesc, a.version  FROM agilentarrays AS a, agilent_arrayinfo AS e WHERE e.arrayid = ? and a.id = e.arraytype";


$sqlResult = $db->Execute($sql,array($arrayid));
$row = $sqlResult->FetchRow();

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

<?php
    if($condensedclone != 1){

        $sql = 'SELECT a.ProbeUID, a.ProbeName, a.GeneSymbol, a.SystematicName, a.Description,'.
        ' d.LogRatio, d.gProcessedSignal, d.rProcessedSignal, d.PValueLogRatio, d.arrayversion'.
        ' FROM agilentg4122f_extendedannotations as a, agilentdata as d WHERE a.FeatureNum = ?' .
        ' AND (a.FeatureNum=d.FeatureNum) and d.arrayid = ?';
        $sqlResult = $db->Execute($sql, array($featurenum, $arrayid));//mysql_query($sql, $db);
        $row = $sqlResult->FetchRow();//mysql_fetch_row($sqlResult);
        //, LogRatio, gProcessedSignal, rProcessedSignal, PValueLogRatio, arrayversion
        //echo "$sql<br>";
        $assocsql = "SELECT * FROM agilentdata WHERE FeatureNum = ? AND arrayid = ?";
        $assocResult = $db->Execute($assocsql, array($featurenum, $arrayid));//mysql_query($assocsql, $db);


        $assocrow=$assocResult->GetRowAssoc(true);//mysql_fetch_assoc($assocResult);

        //echo $sql;
    }else{
        $sql = "SELECT LogRatio FROM agilentcondenseddata WHERE FeatureNum = ? AND arrayid = ?";
        $sqlResult = $db->Execute($sql, array($featurenum, $arrayid));//mysql_query($sql, $db);
        $row = $sqlResult->FetchRow();
    }

}else{
    //echo "Feature Number = $featurenum<br>";
    // Are we dealing  w/ an uncondensed or a condensed clone?

 
        $sql = "SELECT ProbeUID, ProbeName, GeneSymbol, SystematicName, Description FROM agilentg4122f_extendedannotations WHERE FeatureNum = ?";
        $sqlResult = $db->Execute($sql, array($featurenum));//mysql_query($sql, $db);
        $row = $sqlResult->FetchRow();


}
if($userid == 1 and $debug == 1){
echo "$sql<br>";
}
//echo "$assocsql<br>";

//echo "arrayid : $arrayid<br>";
if($arrayid != ""){
if($condensedclone != 1){
$LogRatio = $row[5];
$gProcessed = $row[6];
$rProcessed = $row[7];
//echo "CY5 = $rProcessed<br>";
$pValue = $row[8];
$arrayversion = $row[9];
}else{
    $LogRatio = $row[0];

}
}
    if($condensedclone != 1){
    $ProbeUID = $row[0];
    $ProbeName = $row[1];
    $GeneName = $row[2];
    $SystematicName = $row[3];
    $Description = $row[4];
    }else{
    // stop gap...  put in place to fix an error on 4 FEB 2008
    // i made arrayid = 1...
    $condensedinfoSQL = "SELECT GeneSymbol, SystematicName, Description from agilentg4122f_extendedannotations WHERE FeatureNum = $featurenum";
    //echo "condensedinfoSQL = $condensedinfoSQL<br>";
    $condsqlResult = $db->Execute($condensedinfoSQL);//mysql_query($condensedinfoSQL, $db);
    $arow = $condsqlResult->FetchRow();//mysql_fetch_row($condsqlResult);

    $GeneName = $arow[0];
    $SystematicName = $arow[1];
    $Description = $arow[2];

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
// get the sequence of the probe and display it; but only if it is a mouse array at the moment 07FEB2007....
    if($thisorganism != 1){
        $seqSQL = "SELECT Sequence, Refseq, GenBankAcc, LocusLinkID, UnigeneID, EnsemblID, TigrID, ChromosomalLocation, Cytoband, GoID FROM agilentg4122f_extendedannotations WHERE ProbeName = \"$ProbeName\"";
        if($userid == 1 and $debug == 1){
            echo "$seqSQL<br>";
        }
        $seqResult = $db->Execute($seqSQL);
        $seqacc = $seqResult->GetRowAssoc(true);

    }

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
<?php    }
}

?>

</table>
<?php





$width = 600;
$tableid = "results";
if($condensedclone != 1){
?>
<table width="600">
<tr>
<td>
<div dojoType='dijit.TitlePane' title='Probe Sequence and Annotations' open='false' width="600">
<?php
$arraytype="g4122f";

annotationstotable($seqacc, $width, $tableid,$arraytype);
?>
</div>
</td>
</tr>
</table>
<?php
}
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

<?php
if($condensedclone == 1 && $arrayid != ""){
    $asscloneids = array();
    $finalratiototal = 0.0;
    $asscloneidsFinalRats = array();
    $sql = "SELECT associd FROM agilentcondensedidlookup WHERE cloneid = $featurenum ORDER BY associd ASC";
        $cloneResult = $db->Execute($sql);//mysql_query($sql, $db);
        //while($row = mysql_fetch_row($cloneResult)){

        while($row = $cloneResult->FetchRow()){
            $currentid = $row[0];
            array_push($asscloneids, $currentid);
            $logratioSQL = "SELECT LogRatio from agilentdata WHERE FeatureNum = $currentid AND arrayid = $arrayid";
            //echo "$logratioSQL<br>";
            $logratioResult = $db->Execute($logratioSQL);// mysql_query($logratioSQL, $db);
            $logratioResultVal = $logratioResult->FetchRow();//mysql_fetch_row($logratioResult);
            $finalratiototal += $logratioResultVal[0];
            array_push($asscloneidsFinalRats, $logratioResultVal[0]);

        }
?>
<table>
<thead>
<tr>
<th colspan="9">Clones Used To Calculate Condensed Clone Final Ratio</th>
</tr>
</thead>
<tr>
<td class="questionparameter">Feature Number</td>
<td class="questionparameter">ProbeUID</td>
<td class="questionparameter">Probe Name</td>
<td class="questionparameter">Gene Name</td>
<td class="questionparameter">Systematic Name</td>
<td class="questionparameter">Green Processed Signal</td>
<td class="questionparameter">Red Processed Signal</td>
<td class="questionparameter">Base<sub>10</sub> Log Ratio</td>
<td class="questionparameter">Fold Change</td>

</tr>
<?php

    foreach($asscloneids as $id){
        $randnum = rand(0,1000);
        $cloneinfoSQL = "SELECT a.ProbeUID, a.ProbeName, a.GeneSymbol, a.SystematicName, a.Description, d.gProcessedSignal, d.rProcessedSignal, d.LogRatio, d.PValueLogRatio, d.arrayversion FROM agilentg4122f_extendedannotations as a, agilentdata as d WHERE a.FeatureNum = $featurenum AND (a.FeatureNum=d.FeatureNum) and d.arrayid = $arrayid";
        if($userid == 1 and $debug == 1){
            echo "$cloneinfoSQL<br>";
        }
        $cloneinfoResult = $db->Execute($cloneinfoSQL);//mysql_query($cloneinfoSQL, $db);
        $clonerow = $cloneinfoResult->FetchRow();//mysql_fetch_row($cloneinfoResult);
        echo "<tr><td class=\"results\"><a href=\"agilentfeatureinfo.php?featurenum=$id\" target=\"_blank$randnum\">$id</a></td><td class=\"results\">$clonerow[0]</td>
        <td class=\"results\">$clonerow[1]</td>
        <td class=\"results\">$clonerow[2]</td>
        <td class=\"results\">$clonerow[3]</td>
        <td class=\"results\">".round($clonerow[5],2)."</td>
        <td class=\"results\">".round($clonerow[6],2)."</td>
        <td class=\"results\">".round($clonerow[7],2)."</td>
        <td class=\"results\">".round(log10ToFoldChange($clonerow[7]),2)."</td>

        <tr>";

    }

    echo "</table>";

        //echo "$sql<br>";
    $numids = count($asscloneids);
        if($numids > 1){
            $mean = $finalratiototal/$numids;
            //echo "final ratio total = $finalratiototal<br>";
            //echo "number of ids = $numids<br>";
            $meanfoldchangeval = log10ToFoldChange($mean);
            $meanfoldchangeval = round($meanfoldchangeval, 2);
            //echo "mean = $aval<br>";
            $variance = 0.0;
            $subtotal = 0.0;
            foreach($asscloneidsFinalRats as $val){
                $mult = $val - $mean;
                $mult = $mult * $mult;
                $subtotal += $mult;
                //echo "$subtotal<br>";
            }
            //echo $subtotal;
            $variance = (1/($numids-1)) * $subtotal;
            $sd = sqrt($variance);
            /*if($mean < 0){
                $mean = round(-1 * exp(-1 * $mean),2);
            }
            else{
                $mean = round(exp($mean),2);
            }*/
        }
        //echo "mean now = $mean<br>";
        if($numids > 1){
        ?>

        <table class="question">
        <tr>
        <th colspan="2">Simple Statistics Computed From Table Above</th>
        </tr>
        <tr class="question">
        <td class="questionparameter"><strong>Mean base10 log ratio</strong></td>
        <td class="questionanswer"> <?php echo round($mean,2); ?></td>
        </tr>
        <tr class="question">
        <td class="questionparameter"><strong>Mean Fold-Change</strong></td>
        <td class="questionanswer"> <?php echo $meanfoldchangeval; ?></td>
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
}

$end = utime(); $run = $end - $start;
                echo "<font size=\"1px\"><b>Query results returned in ";
                echo substr($run, 0, 5);
                echo " secs.</b></font>";
?>
</div> <!---- content pane -->
</div>  <!---- layout container -->
</body>
</html>
