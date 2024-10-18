<?php
include 'header.inc';
$command = "../flex_sdk/bin/mxmlc chart4633cluster3.mxml >> garbagedump.txt";
echo "<br>$command<br>";
$str=passthru($command);
?>
<object width="1000" height="1000">
<param name="movie" value="chart4633cluster3.swf">
<embed src="chart4633cluster3.swf" width="1000" height="1000">
</embed>
</object>
<?php
$end = utime(); $run = $end - $start;
	echo "<br><font size=\"1px\"><b>Query results returned in ";echo substr($run, 0, 5);
echo " secs.</b></font>"; 
?>
