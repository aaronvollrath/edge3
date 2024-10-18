<?php
include 'header.inc';
$command = "../flex_sdk/bin/mxmlc chart20826cluster1.mxml >> garbagedump.txt";
echo "<br>$command<br>";
$str=passthru($command);
?>
<object width="1000" height="1000">
<param name="movie" value="chart20826cluster1.swf">
<embed src="chart20826cluster1.swf" width="1000" height="1000">
</embed>
</object>
<?php
$end = utime(); $run = $end - $start;
	echo "<br><font size=\"1px\"><b>Query results returned in ";echo substr($run, 0, 5);
echo " secs.</b></font>"; 
?>
