<?php
//EST svg graph PRINT....
// Generate the svg filename to be displayed.....
	//$filenum = rand(0, 2500000);
	$filename = "./IMAGES/"."graph".$filenum.".svg";
	$command = "touch $filename";
	echo $command;
	$str = exec($command);
	// open that file...
	$fh = fopen($filename, "a");

	//$bytes = fwrite($fh, "Content-type: image/svg+xml");
	$string = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
	$bytes = fwrite($fh, $string);
	$string = "<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.0//EN\"
	\"http://www.w3.org/tr/2001/rec-svg-20010904/dtd/svg10.dtd\" [\n<!ATTLIST svg xmlns:xlink CDATA #FIXED 
	\"http://www.w3.org/1999/xlink\">\n]>\n";
	$bytes = fwrite($fh, $string);

	// NOTE:  SOME OF THESE VALUES MAY NEED TO BE MULTIPLIED BY A VALUE WHEN SIZE OF GRAPH
	//        IS INCREASED OR DECREASED BY THE USER.... TO BE IMPLEMENTED LATER....

	$barx = 0; // barx initial
	$bary = 0;// bary initial
	$w = 5;   // bar width
	// The following two variables determine the size of the graph (essentially x-y dimensions of plot)
	$ch = 200; // canvas height
	$cw = 800; // canvas width
	$markstart = 75; // This is the x-coordinate value for the start of the horizontal freq lines...
	$topmargin = 75;
	// Find out how many libraries we are dealing with...(redundant, i know)
// This will determine how many values are returned in the row array below....
	$libcount = count($library);
	$gaps = 5;
	$titleanchorx = $svgwidth/2;
	$titleanchory = $topmargin/2;
	//$clones = 3;
	require "colors.inc";  // bring in $svgcolorarray.....
	$string = "<svg width=\"$svgwidth\" height=\"$svgheight\">\n";
	$bytes = fwrite($fh, $string);





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
$bararray = array();

$counter = 0;



//echo "here returnSQL: $returnSQL<br>";

$maxvalue = 0;


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


		$bararray[$index][$counter] = $thisfreq;
		// increment the first index of the 2-D array....
		$index++;
		// increment the second index of the 2-D array...
		$currentrows++;
	}

	$name = "$refseq: ".str_replace("\"", "", $primaryname);
	//$thisfreq = "$thisfreq%";
	array_push ($idarray, substr(str_replace("\"", "", $primaryname), 0, 10));
	array_push ($freqarray, $thisfreq);
	array_push ($targetarray, "");
	array_push ($altarray, $name);
	$counter++;
}

// Need to increase the size of $ch to compensate for top margin where graph title is located...



// Determine the max-min values....
// Find min-max across all libraries...
	$count = 0;
	$clones = count($bararray[$count]);
	$maxvalue = max($bararray[$count]);
	$minvalue = min($bararray[$count]);
	$count++;
	while($count < $libcount){
		$max = max($bararray[$count]);
		$min = min($bararray[$count]);
		if($max > $maxvalue){
			$maxvalue = $max;
		}
		if($min < $minvalue){
			$minvalue = $min;
		}
		$count++;
	}


	if($maxvalue != 0){
	$mult = ($ch-10)/$maxvalue;
	}
	else{
		$mult = 1;
	}
	$count = 0;
	$libcounter = 0;
	$colorcount = 0;
	$maxcolorcount = count($svgcolorarray);

	$clonecount = 0;
	$libcounter = 0;
	$labelloc = $ch + $topmargin + 10;
	while($clonecount < $clones){
		// iterate through each clone column...
		while($libcounter < $libcount){

			// iterate through each library row...
			if($maxvalue < 1){
				$y = $ch * ($bararray[$libcounter][$clonecount]/$maxvalue);
				echo "   y$libcounter = $y";
				$fy = ($ch + $topmargin + 10) - $y;// + $topmargin;
				echo " fy$libcounter = $fy";
			}
			else{
				$fy = $ch - ($bararray[$libcounter][$clonecount] * $mult) + $topmargin;
			}
			if($clonecount == 0 && $libcounter == 0){
				$gap = $markstart+30;
			}
			elseif($libcounter == 0){
				$gap = 10;
			}
			else{
				$gap = $gaps;
			}
			$height = $ch - $fy + $topmargin;
			$fx = $fx + $w + $gap;
			if($libcounter == 0){
				// Now need to write the library.....
				$string = "<text x=\"$fx\" y=\"$labelloc\" transform=\"rotate(45, $fx, $labelloc)\">$idarray[$clonecount]</text>\n";
				$bytes = fwrite($fh, $string);
			}

			$val = $bararray[$libcounter][$clonecount];
			$string = "<a xlink:href=\"$val\" target=\"_blank\" alt=\"$fy\">\n<rect x=\"$fx\" y=\"$fy\" width=\"$w\" height=\"$height\" style=\"fill:$svgcolorarray[$colorcount];\"/>\n</a>\n";
			$bytes = fwrite($fh, $string);
			$libcounter++;

			$colorcount++;
			if($colorcount >= $libcount){
				$colorcount = 0;
			}
		}
		// Need to demarcate the library....
		$xloc1 = $fx + $width + $gaps + $gaps/2;
		$yloc1 = $ch + $topmargin + 10;
		$yloc2 = $yloc1 - 10;
		$string = "<line x1=\"$xloc1\" y1=\"$yloc1\" x2=\"$xloc1\" y2=\"$yloc2\" style=\"stroke-width: 3; stroke: black;\"/>\n";
		$bytes = fwrite($fh, $string);
		$clonecount++;
		$libcounter = 0;
	}

	// Marking the y-axis....for now place black line across x-dimension of graph...
	$maxymark = $topmargin + 10;
	$markmult = ($ch - 10)/10;
	$freqmult = $maxvalue/10;
	$markinit = $maxymark;

	$mark = $markinit;
	$freq = $maxvalue;
	$ycount = 0;
	$valcount = 0;
	while($ycount <= 10){
		$loc = $mark + ($ycount * $markmult);
		$val = $freq -($valcount * $freqmult);
		$val = round($val, 2);
		$string = "<line x1=\"$markstart\" y1=\"$loc\" x2=\"$cw\" y2=\"$loc\"/>\n";
		$bytes = fwrite($fh, $string);
		// Now marking the values...
		$string = "<text x=\"$markstart\" y=\"$loc\">$val</text>\n";
		$bytes = fwrite($fh, $string);
		$ycount++;
		$valcount++;
	}
	
	// Placing the y-axis label....
	$freqposition = ($ch + 75)/1.33;
	$string = "<text x=\"40\" y=\"$freqposition\" transform=\"rotate(270, 40, $freqposition)\" style=\"stroke: blue; font-size: 14pt;\">Frequency (%)</text>\n";
	$bytes = fwrite($fh, $string);

	// Write the title....
	$string = "<text x=\"$titleanchorx\" y=\"$titleanchory\" style=\"text-anchor: middle; stroke: gray; font-size: 14pt;\">EST Graph</text>\n";
	$bytes = fwrite($fh, $string);
	/*
	$string = "<text x=\"500\" y=\"95\">Max = $maxvalue</text>\n";
	$bytes = fwrite($fh, $string);
	$string = "<text x=\"500\" y=\"105\">Min = $minvalue</text>\n";
	$bytes = fwrite($fh, $string);
	$string = "<text x=\"500\" y=\"115\">Mult = $mult</text>\n";
	$bytes = fwrite($fh, $string);
	$string = "<text x=\"500\" y=\"125\">Clones = $clones</text>\n";
	$bytes = fwrite($fh, $string);
	$string = "<text x=\"500\" y=\"135\">Libraries = $libcount</text>\n";
	$bytes = fwrite($fh, $string);
	*/



	$string = "</svg>";
	$bytes = fwrite($fh, $string);
	fclose($fh);
?>
