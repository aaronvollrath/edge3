<?php
	/*  This file contains some common file creation code that is pretty much universal to all of the modules... 
	This will hopefully make things simpler by providing a central location for this stuff.
		


	*/
	#echo "in common file creation code....<br>";
	$filenum = rand(0, 25000);
	$file = "$IMAGESdir/data$filenum.txt";
	$logratiovalues = "$IMAGESdir/datalogvalues$filenum.txt";
	$svgFile = "$IMAGESdir/svg$filenum.svg";
	$tableFile = "$IMAGESdir/table$filenum";
	$csvFile = "$IMAGESdir/$filenum.csv";
	$processedValsFile = "$IMAGESdir/$filenum"."processedVals.csv";
	$processedValsTableFile = "$IMAGESdir/$filenum"."processedValsTable.html";
		
	$command = "touch $file";
	$handle = fopen($file, 'w') or die("can't open $file file.  Check permissions.");
	fclose($handle);
	#$str=exec($command);
	
	$command = "touch $csvFile";
	#$str=exec($command);
	$handle = fopen($csvFile, 'w') or die("can't open $csvFile file.  Check permissions.");
	fclose($handle);

	$command = "touch $logratiovalues";
	#$str=exec($command);
	$handle = fopen($logratiovalues, 'w') or die("can't open $logratiovalues file.  Check permissions.");
	fclose($handle);
	
	$command = "touch $svgFile";
	#$str=exec($command);
	$handle = fopen($svgFile, 'w') or die("can't open $svgFile file.  Check permissions.");
	fclose($handle);
	
	
	$command = "touch $tableFile";
	#$str=exec($command);
	$handle = fopen($tableFile, 'w') or die("can't open $tableFile file.  Check permissions.");
	fclose($handle);
	
	$command = "touch $processedValsFile";
	$handle = fopen($processedValsFile, 'w') or die("can't open $processedValsFile file.  Check permissions.");
	fclose($handle);
	#$str=exec($command);
	$command = "touch $processedValsTableFile";
	#$str=exec($command);
	$handle = fopen($processedValsTableFile, 'w') or die("can't open $processedValsTableFile file.  Check permissions.");
	fclose($handle);
	
	if(isset($_POST['colorScheme'])){
		$colorscheme = $_POST['colorScheme'];
	}
	if(isset($_POST['rval'])){
		$upperbound = $_POST['rval'];
	}
	if(isset($_POST['rvalmax'])){
		$upperboundmax = $_POST['rvalmax'];
	}
	if(isset($_POST['lval'])){
		$lowerbound = $_POST['lval'];
	}
	if(isset($_POST['lvalmin'])){
		$lowerboundmin = $_POST['lvalmin'];
	}
	//analyze($_POST);
	// NEED TO CONVERT THESE VALUES TO LOG BASE 10....
	if(!isset($_GET['orderedheatmapmodule'])){  // put in place, because getting errors....
		include 'convertthresholdvalues.inc.php';
	}

	$fd = fopen($file, 'w');
	$logfd = fopen($logratiovalues,'w');
	$csvfd = fopen($csvFile, 'w');
	$processedfd = fopen($processedValsFile, 'w');
	$processedtablefd = fopen($processedValsTableFile, 'w');
	rewind($fd);
	rewind($logfd);
	rewind($csvfd);
	rewind($processedfd);
	rewind($processedtablefd);
	$arrayidarray = array();
	$arrayDescArray = array();
?>