<?php
//EST graph PRINT....

//if($rows <= 20 && $rows > 0){

// #######################################################
//  CREATING THE BAR GRAPH....
// #######################################################

$returnResult = mysql_query($returnSQL, $db);


require "colors.inc";

// Got the result set, now need to create an array of sampleids and freqs....
// Determine the min/max values in the freqs array
$idarray= array();
$freqarray=array();
//$namearray=array();
$targetarray=array();
$altarray=array();
// This is a 2-D array to store the frequencies for each library....
$libsarray = array();

$counter = 0;

// Find out how many libraries we are dealing with...(redundant, i know)
// This will determine how many values are returned in the row array below....
$libcount = count($library);

//echo "here returnSQL: $returnSQL<br>";

$maxvalue = 0;

//while(list($cloneid, $refseq, $primaryname, $freq) = mysql_fetch_array($returnResult))
while($row = mysql_fetch_row($returnResult))
{
	$cloneid = $row[0];
	$refseq = $row[1];
	$primaryname = $row[2];

	if($refseq == ""){
		$refseq = "NULL";
	}
	// Now we need to get the number of freqs....
	$currentrows = 3;
	//  libcount is increased by three to account for the two values preceding the freqs....
	$index = 0;
	while($currentrows < ($libcount + 3)){
			$thisfreq = $row[$currentrows];
			//$thisfreq = $freq * 100;
			//$thisfreq = round($thisfreq, 3);
			if($thisfreq > $maxvalue){
				$maxvalue = $thisfreq;
			}
			if($maxvalue < 1){
				$graphmaxvalue = $maxvalue + .1;
			}
			else{
				$graphmaxvalue = $maxvalue + $maxvalue * .1;
			}
			if($thisfreq <= 0){

				$thisfreq = .005;
			}
		$libsarray[$index][$counter] = $thisfreq;
		// increment the first index of the 2-D array....
		$index++;
		// increment the second index of the 2-D array...
		$currentrows++;
	}

	$name = "GENE: $refseq  ".str_replace("\"", "", $primaryname);
	//$thisfreq = "$thisfreq%";
	$libnameitem = $librarynamesarray[$counter];
	//$name = "Library: ".$libnameitem."  ".$name;
	array_push ($idarray, substr(str_replace("\"", "", $primaryname), 0, 10));
	array_push ($freqarray, $thisfreq);
	array_push ($targetarray, "");
	array_push ($altarray, $name);
	$counter++;
}
//echo "Here's graph max y value: $graphmaxvalue<br>";
/*
foreach($libsarray as $libvalue){
	$count = 0;
	foreach($libvalue as $frequency){
		echo $frequency;
		if($frequency < .006){
			echo "in if";
			if($graphmaxvalue <= .1){
				$libvalue[count] = .02;
				$zeroval = .02;
			}
			elseif($graphmaxvalue >= 1){
				$libvalue[count] = .09;
				$zeroval = .09;
			}
			elseif($graphmaxvalue <= 5){
				$libvalue[count] = .1;
				$zeroval = .1;
			}
			elseif($graphmaxvalue >= 10){
				$libvalue[count] = .3;
				$zeroval = .3;
			}
			else{
				$libvalue[count] = .1;
				$zeroval = .1;
			}
		}

		echo $count++;
	}
}
echo "Here's zeroval: $zeroval<br>";*/

// Setup graph
$graph = new Graph($width,$height,"");
$graph->img->SetMargin(60,180,50,100);
$graph->SetScale("textlin", 0, $graphmaxvalue);
$graph->SetShadow();

//Setup title
$graph->title->Set("EST graph");

// Use built in font
$graph->title->SetFont(FF_FONT1,FS_BOLD);

// Slightly adjust the legend from it's default position
$graph->legend->Pos(0.00,0.0,"right","top");
//$graph->legend->SetFont(FF_FONT1,FS_BOLD);

// Setup X-scale
$datax = $idarray;
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetPos("min");
$graph->xaxis->SetTitleMargin(60);
$graph->xaxis->SetFont(FF_FONT1, FS_NORMAL, 10);
$graph->xaxis->SetTitle('Genes','middle');
$graph->xaxis->title->SetFont(FF_FONT2,FS_BOLD);
$graph->xaxis->SetLabelAngle(90);

$graph->yaxis->title->Set("Frequency (%)");
$graph->yaxis->title->SetFont(FF_FONT2,FS_BOLD);
$graph->yaxis->SetTitleMargin(40);
$graph->yaxis->SetFont(FF_FONT1, FS_BOLD, 12);

//$graph->SetBackgroundImage("Edge2.png");

$xcount = 0;
// Now we are going to have to iterate through the 2-D array, $libsarray
$barplots = array();
foreach($libsarray as $libarrayval){
	//echo "in foreach loop<br>";
	$datay = $libarrayval;
	
	// Create the bar pot
	$bplot = new BarPlot($datay);
	$bplot->SetWidth(0.3);

	// Setup values
	//$bplot->value->Show();

	// Bar values...
	//$bplot->SetValuePos('top');
	//$bplot->value->SetFormat('%01.3f%%');
	//$bplot->value->SetColor("black");
	//$bplot->value->SetFont(FF_FONT1,FS_BOLD,5);
	//$bplot->value->SetAngle(90);
	if($shownumrows < 10 || $totalrows < 10){
		$bplot->SetAbsWidth(20);
	}
	// Setup color for gradient fill style
	$bplot->SetFillColor($outercolor[$xcount]);
	$bplot->SetFillGradient($outercolor[$xcount],$innercolor[$xcount],GRAD_MIDVER);

	$libnamesSQL = "SELECT shortname from estnames where abbrev = '$library[$xcount]'";

		$libnamesResult = mysql_query($libnamesSQL, $db);
		$row = mysql_fetch_row($libnamesResult);
		$libname = $row[0];

	//$bplot->SetLegend($library[$xcount], "", "Library $library[$xcount]");
	$bplot->SetLegend($libname, "", "Information on the library: $libname");
	$bplot->SetCSIMTargets($targetarray, $altarray);

	array_push($barplots, $bplot);
	unset($bplot);
	$xcount++;
}

// Create the grouped bar plot
$gbplot = new GroupBarPlot($barplots);

// Create the  line
/*$linearray = array($zeroval, $zeroval, $zeroval, $zeroval, $zeroval, $zeroval, $zeroval, $zeroval, $zeroval, $zeroval);
$p1 = new LinePlot($linearray);
$p1->SetColor("red");
$p1->SetWeight(2);
$graph->Add($p1);*/
// ...and add it to the graph
$graph->Add($gbplot);
// Finally send the graph to the browser
$graph ->Stroke("/var/www/html/edge2/IMAGES/$filename");
echo $graph ->GetHTMLImageMap ("$imap" );
//}

?>
