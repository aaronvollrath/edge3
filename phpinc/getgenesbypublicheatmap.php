<?php

require 'edge_db_connect2.php';
require 'edge3_db_connect.inc';

echo "Select a list below.<br><br>";

if($userid != ""){
	$sql = "SELECT listid , name , listdesc , userid FROM genelist WHERE public = '1'";
	$sqlResult = mysql_query ($sql,$db);
	while($row = mysql_fetch_row($sqlResult)){
		$listid = $row[0];
		$name = $row[1];
		$listdesc = $row[2];
		$userid = $row[3];
		
		echo "<a href=\"./edge3.php?orderedheatmapmodule=1&listid=$listid\">"."(Userid $userid".") "."$name".": "."$listdesc</a><br>";
	}
}
else{
	echo "Please login to use this feature.";
}

?>