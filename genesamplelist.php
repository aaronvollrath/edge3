<?php

require 'edge_db_connect2.php';
//include ("jpgraph.php");
//include ("jpgraph_line.php");
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../edge2/default.php">Click here to go back to the main page</a>');
}
// Get the variables passed through the url...
// Should probably be using sessions....

//$cloneid = $_GET['cloneid'];
$versionid = $_GET['versionid'];
$chemid = $_GET['chemid'];
$sex = $_GET['sex'];
$age = $_GET['age'];
$tissue = $_GET['tissue'];
$route = $_GET['route'];
$dose = $_GET['dose'];
$duration = $_GET['duration'];
$refseq = $_GET['refseq'];
$orderby = $_GET['orderby'];


// Get a connection to the database....
require './phpinc/edge3_db_connect.inc';

//  Assign image name before the table print option stuff so that the filename can be passed through
$imagenum = rand(0, 25000);
$filename = "image".$imagenum."id_".$userid.".png";
$imap = "myimagemap$imagenum";
?>


<?php
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
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

 <div class="boxmiddle">
 <?php
include 'questionmenu.inc';
?>




<?php
	require "colors.inc";



if($chem != ""){
	$whereCheck = 1;
	$params = " WHERE chemid = '$chem'";
}
if($sex != ""){
	if($whereCheck == 1){
		//just append to parameter list
		$params = "$params AND sex = '$sex'";
	}
	else{
		$whereCheck = 1;
		$params = " WHERE sex = '$sex'";
	}
}
if($age != ""){
	if($whereCheck == 1){
		//just append to parameter list
		$params = "$params AND age = '$age'";
	}
	else{
		$whereCheck = 1;
		$params = " WHERE age = '$age'";
	}
}
if($tissue != ""){
	if($whereCheck == 1){
		//just append to parameter list
		$params = "$params AND tissue = '$tissue'";
	}
	else{
		$whereCheck = 1;
		$params = " WHERE tissue = '$tissue'";
	}
}
if($route != ""){
	if($whereCheck == 1){
		//just append to parameter list
		$params = "$params AND route = '$route'";
	}
	else{
		$whereCheck = 1;
		$params = " WHERE route = '$route'";
	}
}
if($duration != ""){
	if($whereCheck == 1){
		//just append to parameter list
		$params = "$params AND duration = '$duration'";
	}
	else{
		$whereCheck = 1;
		$params = " WHERE duration = '$duration'";
	}
}
if($dose != ""){
	if($whereCheck == 1){
		//just append to parameter list
		$params = "$params AND dose = '$dose'";
	}
	else{
		$whereCheck = 1;
		$params = " WHERE dose = '$dose'";
	}
}

if($orderby != ""){
	//echo "here's orderby: $orderby<br>";
	if($orderby == "sampleid"){
		$orderparam = " ORDER BY sampleid";
		$ordertext = "Sample ID";
	}
	elseif($orderby == "organism"){
		$orderparam = " ORDER BY organism";
		$ordertext = "Organism";
	}
	elseif($orderby == "strain"){
		$orderparam = " ORDER BY strain";
		$ordertext = "Strain";
	}
	elseif($orderby == "genevariation"){
		$orderparam = " ORDER BY genevariation";
		$ordertext = "Genetic Variation";
	}
	elseif($orderby == "age"){
		$orderparam = " ORDER BY age";
		$ordertext = "Age";
	}
	elseif($orderby == "sex"){
		$orderparam = " ORDER BY sex";
		$ordertext = "Sex";
	}
	elseif($orderby == "tissue"){
		$orderparam = " ORDER BY tissue";
		$ordertext = "Tissue";
	}
	elseif($orderby == "treatment"){
		$orderparam = " ORDER BY treatment";
		$ordertext = "Treatment";
	}
	elseif($orderby == "vehicle"){
		$orderparam = " ORDER BY vehicle";
		$ordertext = "Vehicle";
	}
	elseif($orderby == "duration"){
		$orderparam = " ORDER BY duration";
		$ordertext = "Duration";
	}
	elseif($orderby == "dose"){
		$orderparam = " ORDER BY dose";
		$ordertext = "Dose";
	}
	elseif($orderby == "route"){
		$orderparam = " ORDER BY route";
		$ordertext = "Route";
	}
	else{
		// Could happen...
		$orderparam = "";
	}
}



	//$returnSQL = "SELECT sampleid, chemid, organism, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, duration from sampledata $params $orderparam";
	
	$returnSQL = "SELECT s.sampleid, s.chemid, organism, strain, genevariation, age, sex, tissue, treatment, vehicle, dose, route, duration
		FROM sampledata AS s, array AS a  $params  AND (a.ownerid = 1 or a.ownerid =1) and s.sampleid = a.arrayid$orderparam";

	//$returnResult = mysql_query($returnSQL, $db);
	//echo "Here is the sql: $returnSQL";
?>
<h3 class="contenthead">Selected Genes Across Samples</h3>
<div class="content">
<table>
<tbody align="left">
<tr>

<td align="left" valign="top">
<table>


	<?php
	// Now need to go through checkboxes to see which are on...
$idarray= array();
$idcount = 0;
$clone = "check$idcount";
$clonelist = "";
$clonecount = 0;
//$cloneid = $_GET[$clone];
$rows = $_GET['rows'];
while ($idcount < $rows){

	//echo "in while loop, cloneid=";
	$clone = "check$idcount";
	$cloneid = $_GET[$clone];
	//echo "$cloneid<br>";
	if($cloneid != ""){
	array_push($idarray, $cloneid);
	$clonelist .= "&";
	$clonelist .= "$clone=$cloneid";
	$clonecount++;
	}

	$idcount++;

}
$elements = count($idarray);
$idarrayclone = $idarray;
//$cloneid = array_pop($idarray);
//echo "here are the number of clones in the array: $elements<br>";
?>

<tr valign="top">
<td>
	<table id="cdnaesttable" width="400">
	<tr>
	<td class="bluegray" colspan=4><b>Your query parameters:</b></td><td><b><?php /*PRINT TABLE goes here!!!!!! */ ?></b></td>
	</tr>
	<tr>
 	<td class="questionparameter" align="right"><b>Chemical:</b></td><td  class="questionanswer" ><?php echo $chemtext; ?></td>
	<td  class="questionparameter" align="right"><b>Tissue:</b></td><td   class="questionanswer" ><?php echo $tissue; ?></td>
	<td rowspan=4>	<table><tr bgcolor="FFFF99"><td width="50%">
<?php
/*
<FORM METHOD="POST" ACTION="tableprint.php" target="_blank">
<input name="sql" type="hidden" value="SELECT sampleid, chemid, organism, strain, genevariation, age, sex, tissue, treatment, duration, vehicle, dose, route from sampledata <?php echo $queryparams;?>";
<input name="chem" type="hidden" value="<?php echo "$chemtext"; ?>">
<input name="sex" type="hidden" value="<?php echo "$sex"; ?>">
<input name="age" type="hidden" value="<?php echo "$age"; ?>">
<input name="tissue" type="hidden" value="<?php echo "$tissue"; ?>">
<input name="route" type="hidden" value="<?php echo "$route"; ?>">
<input name="duration" type="hidden" value="<?php echo "$duration"; ?>">
<input name="dose" type="hidden" value="<?php echo "$dose"; ?>">
<input name="ordertext" type="hidden" value="<?php echo "$ordertext"; ?>">
<input name="totalrows" type="hidden" value="<?php echo "$totalrows"; ?>">
<input name="list" type="hidden" value="gsl">
<input name="5prime" type="hidden" value="<?php echo "$fiveprime"; ?>">
<input name="3prime" type="hidden" value="<?php echo "$threeprime"; ?>">
<input name="image" type="hidden" value="<?php echo "$image"; ?>">
<input name="showimage" type="hidden" value=1>
<INPUT TYPE="submit" VALUE="Printable table">
</FORM>
*/
?>
</td></tr></table></td>
	</tr>
	<tr>
 	<td  class="questionparameter" align="right"><b>Sex:</b></td><td   class="questionanswer" ><?php echo $sex; ?></td>
	<td  class="questionparameter" align="right"><b>Route:</b></td><td   class="questionanswer" ><?php echo $route; ?></td>
	</tr>
	<tr>
 	<td  class="questionparameter"  align="right"><b>Age(in weeks):</b></td><td   class="questionanswer" ><?php echo $age; ?></td>
	<td width="25%" align="right"><b>Dosage:</b></td><td   class="questionanswer" ><?php echo $dose; ?></td>
	</tr>
	<tr>
	<td  class="questionparameter"  align="right"><b>Duration:</b></td><td   class="questionanswer" ><?php echo $duration; ?></td>
	<td class="questionparameter"  align="right"><b>Order By:</b></td><td   class="questionanswer" ><?php echo $ordertext; ?></td>
	</tr>
	<tr>
	<td  class="questionparameter"  align="right"><b>Clones selected:</b></td><td   class="questionanswer"  align="left"><?php echo $clonecount; ?></td>
	<td colspan=2></td>
	</tr>
	</table>

</td>
</tr>

<tr>
<td>
<?php

//###################################################################################

// Got the result set, now need to create an array of sampleids and finalratios....

$width = 600;
$height = 300;
/*
// Setup graph
$graph = new Graph($width,$height,"auto");
$graph->img->SetMargin(50,20,20,60);
$graph->img->SetAntiAliasing();
$graph->SetScale("textlin");
$graph->SetShadow();

//Setup title
$graph->title->Set("Graph across samples");

// Use built in font
$graph->title->SetFont(FF_FONT1,FS_BOLD);

// Slightly adjust the legend from it's default position
$graph->legend->Pos(0.03,0.5,"right","center");
//$graph->legend->SetFont(FF_FONT1,FS_BOLD);


$graph->yaxis->title->Set("Final Ratio");
$graph->yaxis->title->SetFont(FF_FONT2,FS_BOLD);
$graph->yaxis->SetTitleMargin(30);
$graph->yaxis->SetFont(FF_FONT1, FS_BOLD, 12);

$graph->legend->SetLineWeight(4);
$graph->xaxis->SetFont(FF_FONT1,FS_BOLD);




*/


$elements = count($idarray);
$counterclone = 0;

$colorcount = 0;
$markcount = 0;


$counterclone2 = 0; // used to go through $idarrayclone....
$clonesToAdd = "";
// Now need to go through $idarrayclone to get the clones in question....
while ($counterclone2 < $elements){
	$cloneToAdd = array_pop($idarrayclone);
	$clonesToAdd .= htmlspecialchars("&clone$counterclone2=$cloneToAdd", ENT_QUOTES);
	$counterclone2++;
}


// now need to get finalratio for each cloneid in the idarray ($idarray)
while($counterclone < $elements){
//echo "in counterclone while: counterclone = $counterclone and elements = $elements<br>";
$samplearray=array();
$ratioarray=array();
$targetarray=array();
$altarray=array();
$counter = 0;

$cloneid = array_pop($idarray);
$returnResult = mysql_query($returnSQL, $db);
while(list($_sampID, $_chemID, $_organism, $_strain, $_genevariation, $_age, $_sex, $_tissue, $_treatment, $_vehicle,
	$_dose, $_route, $_duration) = mysql_fetch_array($returnResult)){
		// Need to get the arrayid....
		$arrayidSQL = "Select arrayid from array where sampleid = $_sampID";
		$arrayidResult = mysql_query($arrayidSQL, $db);
		$row = mysql_fetch_row($arrayidResult);
		$arrayid = $row[0];
		//echo "here's the arrayid: $arrayid<br>";
		//echo "here's the cloneid: $cloneid<br>";
		// Need to use $arrayid and $cloneid to lookup finalratio in ratios table.
		$finalratioSQL = "Select finalratio from hybrids where arrayid = $arrayid and cloneid = $cloneid";
		//echo "heres finalratioSQL: $finalratioSQL<br>";
		$finalRatioResult = mysql_query($finalratioSQL, $db);
		$row = mysql_fetch_row($finalRatioResult);

		$finalratio = $row[0];
		array_push ($samplearray, "$_treatment\n$_dose");
		//$idarray[counter] = "S$sampID";
		//$sampleURL = "http://genome.oncology.wisc.edu/edge2/sample.php?samptype='gsl'&sampleid=$sampID$clonesToAdd&orderby=hybrids.cloneid&sort=asc";
		//$ratioarray[counter] = $finalratio;
		array_push ($ratioarray, $row[0]);
  //array_push ($targetarray, "http://genome.oncology.wisc.edu/edge2/sample.php?sampleid=$_sampID&orderby=hybrids.finalratio&sort=asc&lcomp=lte&lval=-2&rcomp=gte&rval=2");
		array_push ($targetarray,"./sample.php?samptype=1&sampleid=$_sampID$clonesToAdd&orderby=hybrids.finalratio&sort=asc&rows=$elements");

		array_push ($altarray, "Click here to view the genes in this graph across this sample: $_treatment@$_dose.");

		$counter++;

}

$datay = $ratioarray;

/*
// Create the  line
$p1 = new LinePlot($datay);



if($colorcount/25 == 1){
	//reset colorcount...
	$colorcount = 0;
$p1->SetColor($colorarray[$colorcount]);
$p1->mark->SetFillColor($colorarray[$colorcount]);
}
else{
$p1->SetColor($colorarray[$colorcount]);
$p1->mark->SetFillColor($colorarray[$colorcount]);
	$colorcount++;
}
if($markcount/12 == 1){
$markcount = 0;
$p1->mark->SetType($markstyle[$markcount]);
}
else{
$p1->mark->SetType($markstyle[$markcount]);
$markcount++;
}
$p1->SetWeight(2);

$p1->mark->SetWidth(5);

$p1->SetCenter();
$p1->SetLegend($cloneid, "", "Clone $cloneid");
// Assign the target links...
$p1->SetCSIMTargets($targetarray, $altarray);
$graph->Add($p1);
unset($p1);

*/

unset($ratioarray);
unset($targetarray);
unset($altarray);
$counterclone++;

} // End of loop that goes through cloneids........##########################
/*
// Some data
$datax = $samplearray;

// Setup X-scale
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetPos("min");
$graph->xaxis->SetTitleMargin(15);
$graph->xaxis->SetFont(FF_FONT1, FS_BOLD, 12);
$graph->xaxis->SetTitle('Samples','middle');
$graph->xaxis->title->SetFont(FF_FONT2,FS_BOLD);



// Output line
$graph ->Stroke("/var/www/html/edge2/IMAGES/$filename" );
echo $graph ->GetHTMLImageMap ("$imap" );
echo "<img src=\"./IMAGES/$filename\" ISMAP USEMAP=\"#$imap\" border=0 align =center width=$width height=$height>" ;
*/
//  Need to recall the query....to use below....
$returnResult = mysql_query($returnSQL, $db);
?>

</td>
</tr>

<tr align="left" valign="top">
<td align="left" valign="top">
<table id="results">
	<tr bgcolor="9999FF">
	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>
	&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=sampleid<?php echo "$clonelist"; ?>">
	<center><b>SampleID</b></center></a></td>

	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>
	&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=organism<?php echo "$clonelist"; ?>">
	<center><b>Organism</b></center></a></td>

	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>
	&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=strain<?php echo "$clonelist"; ?>">
	<center><b>Strain</b></center></a></td>

	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>
	&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=genevariation<?php echo "$clonelist"; ?>">
	<center><b>Genetic<br>Variation</b></center></a></td>

	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>
	&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=age<?php echo "$clonelist"; ?>">
	<center><b>Age<br>(in wks)</b></center></a></td>

	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>
	&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=sex<?php echo "$clonelist"; ?>">
	<center><b>Sex</b></center></a></td>
	
	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>
	&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=tissue<?php echo "$clonelist"; ?>">
	<center><b>Tissue</b></center></a></td>
	
	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>
	&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=treatment<?php echo "$clonelist"; ?>">
	<center><b>Treatment</b></center></a></td>

	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>
	&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=vehicle<?php echo "$clonelist"; ?>">
	<center><b>Vehicle</b></center></a></td>

	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>
	&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=dose<?php echo "$clonelist"; ?>">
	<center><b>Dose</b></center></a></td>
	
	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=duration<?php echo "$clonelist"; ?>">
	<center><b>Duration</b></center></a></td>

	<td class="colhead"><a href="genesamplelist.php?currentRow=<?php echo "$currentrow";?>&shownumrows=<?php echo "$shownumrows"; ?>&chem=<?php echo "$chem"; ?>
	&sex=<?php echo "$sex"; ?>&age=<?php echo "$age"; ?>&tissue=<?php echo "$tissue"; ?>
	&duration=<?php echo "$duration"; ?>&durationlogic=<?php echo "$durationlogic"; ?>&route=<?php echo "$route"; ?>
	&rows=<?php echo "$rows"; ?>&dose=<?php echo "$dose"; ?>&chemlogic=<?php echo "$chemlogic"; ?>&sexlogic=<?php echo "$sexlogic"; ?>
	&agelogic=<?php echo "$agelogic"; ?>&tissuelogic=<?php echo "$tissuelogic"; ?>&routelogic=<?php echo "$routelogic"; ?>
	&searchby=<?php echo "$searchby"; ?>&searchval=<?php echo "$searchval"; ?>&colsort=true
	&5prime=<?php echo "$fiveprime"; ?>&3prime=<?php echo "$threeprime"; ?>&orderby=route<?php echo "$clonelist"; ?>">
	<center><b>Route</b></center></a></td>
	</tr>
<?php

$bgcolorcount = 0;
	while(list($sampID, $chemID, $organism, $strain, $genevariation, $age, $sex, $tissue, $treatment, $vehicle,
	$dose, $route, $duration) = mysql_fetch_array($returnResult)){
		// Need to get the arrayid....
		$arrayidSQL = "Select arrayid from array where sampleid = $sampID";
		$arrayidResult = mysql_query($arrayidSQL, $db);
		$row = mysql_fetch_row($arrayidResult);
		$arrayid = $row[0];
		// Need to use $arrayid and $cloneid to lookup finalratio in ratios table.
		$finalratioSQL = "Select finalratio from hybrids where arrayid = $arrayid and cloneid = $cloneid";
		$finalRatioResult = mysql_query($finalratioSQL, $db);
		$row = mysql_fetch_row($finalRatioResult);
		$finalratio = $row[0];
	if($bgcolorcount%2){
		// number is odd..
		$bgcolor = "CCCCCC";
	}
	else{
		// number is odd...
		$bgcolor = "FFFF99";
	}

	echo "<tr bgcolor=\"$bgcolor\"><td><a href=\"./sample.php?sampleid=$sampID&orderby=hybrids.finalratio&sort=asc&lcomp=lte&lval=-2&rcomp=gte&rval=2\">Sample #$sampID</a></td>

	<td>$organism</td><td>$strain</td><td>$genevariation</td>
	<td>$age</td><td>$sex</td><td>$tissue</td><td>$treatment</td><td>$vehicle</td><td>$dose</td>
	<td>$duration</td><td>$route</td></tr>";
$bgcolorcount++;
	}
?>
	</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>

 </div>
 <?php
	include 'leftmenu.inc';

?>

<div class="boxclear"> </div>
 <div class="boxfooter"></div>
 <div class="boxclear"> </div>
</div>



 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>
</body>
</html>

