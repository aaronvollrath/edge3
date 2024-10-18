<?php



// script to convert threshold values from fold change to log base 10....

if($lowerbound >= 1){
	$lowerbound = log($lowerbound,10);
}
else{
	$lowerbound = log(-1/$lowerbound,10);
}
if(is_numeric($lowerboundmin)){
	if($lowerboundmin >= 1){
		$lowerboundmin = log($lowerboundmin,10);
	}
	else{
		$lowerboundmin = log(-1/$lowerboundmin,10);
	}
}
if($upperbound >= 1){
	$upperbound = log($upperbound,10);
}
else{
	$upperbound = log(-1/$upperbound,10);
}
if(is_numeric($upperboundmax)){
	if($upperboundmax >= 1){
		$upperboundmax = log($upperboundmax,10);
	}
	else{
		$upperboundmax = log(-1/$upperboundmax,10);
	}
}
//echo "Converted Threshold values....<br>";
?>
