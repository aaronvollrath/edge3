<?php

// pass by value
function prepare($org){

if ($org == 2) {$file = "PIN_Ce.txt";}
elseif ($org == 3) {$file = "PIN_Dm.txt";}
elseif ($org == 4) {$file = "PIN_Mm.txt";}
elseif ($org == 5) {$file = "PIN_Hs.txt";}
elseif ($org == 1) {
	$file = "PIN_Sc.txt";
	##### get the screen N/A nodes
	 $fr = fopen('screenNA.txt', 'r');
	if(!$fr) {
		echo "Error! Couldn't open the file.";
	} else {
		// $fr now can be used to represent the opened file
		echo "<br>File opened<br>";
		$count = 1;
		while (!feof($fr)) {
   			$buffer = fgets($fr, 1024);
			if($count == 1){
   				echo $buffer;
			}
			$count++;
		}
	}
	if(!fclose($fr)) {
		echo "Error! Couldn't close the file.";
   	}
	/*open (FILEIN, "screenNA.txt") || die "cannot open: screenNA.txt";
	while (<FILEIN>) {
		@line = split (/\s+/, $_);
		$NA{$line[0]} = 1;
	}
	close (FILEIN) || die "cannot close: screenNA.txt";
	*/
}
echo "<br>The file is $file<br>";
/*
### get the DIP interaction table
open (FILEIN, "$file") || die "cannot open: $file";
while (<FILEIN>) {
	chomp($_);
	@line = split(/\t/, $_);

	if (!$projection && $line[4] != 1) {next;} ## when don't use projection, only use "direct interactions"

	$DIP_s{$line[0]} .= $line[1]."\t"; ## %DIP_s record the interactant of each protein
	$DIP_s{$line[1]} .= $line[0]."\t";

	### use %gene to present Graphviz-compatible gene symbols
	$left = $line[2];
	$right = $line[3];
	$left =~ s/\W/_/g;
	$left =~ s/^(\d)/_$1/g;
	$right =~ s/\W/_/g;
	$right =~ s/^(\d)/_$1/g;
	$gene{$line[0]} = $left;
	$gene{$line[1]} = $right;
	$orf{$left} = $line[0];
	$orf{$right} = $line[1];

	$line[2] =~ tr/a-z/A-Z/; ## convert gene names to capital letters for easy compasion with input
	$line[3] =~ tr/a-z/A-Z/;
	$orf{$line[2]} = $line[0];
	$orf{$line[3]} = $line[1];

	if ($org == 1) {
		$orf{$line[0]} = $line[0];
		$orf{$line[1]} = $line[1];
	}

	if ($line[4] == 2) {
		$DIP{"$line[0] $line[1]"} = $DIP{"$line[1] $line[0]"} = 2; ## hs<->mm projection
	}
	if ($line[4] == 3) {
		$DIP{"$line[0] $line[1]"} = $DIP{"$line[1] $line[0]"} = 3; ## projection from Dm
	}
	if ($line[4] == 4) {
		$DIP{"$line[0] $line[1]"} = $DIP{"$line[1] $line[0]"} = 4; ## projection from Ce
	}
	if ($line[4] == 5) {
		$DIP{"$line[0] $line[1]"} = $DIP{"$line[1] $line[0]"} = 5; ## projection from yeast
	}

	$projection{"$line[0] $line[4]"} = "$line[5] $line[7]";
	$projection{"$line[1] $line[4]"} = "$line[6] $line[8]";
}
close (FILEIN);
*/
} ### closing function prepare();
?>