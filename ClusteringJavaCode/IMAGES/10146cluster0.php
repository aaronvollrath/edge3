<?php
include 'header.inc';
$command = "../flex_sdk/bin/mxmlc chart10146cluster0.mxml >> garbagedump.txt";
echo "<br>$command<br>";
$str=passthru($command);
?>
<object width="1000" height="1000">
<param name="movie" value="chart10146cluster0.swf">
<embed src="chart10146cluster0.swf" width="1000" height="1000">
</embed>
</object>
<?php
$end = utime(); $run = $end - $start;
	echo "<br><font size=\"1px\"><b>Query results returned in ";echo substr($run, 0, 5);
echo " secs.</b></font>"; 
?>
