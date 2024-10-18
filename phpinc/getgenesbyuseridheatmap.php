<?php

require 'edge_db_connect2.php';
require 'edge3_db_connect.inc';

echo "Select a list below.<br><br>";

if($userid != ""){
	$sql = "SELECT listid , name , listdesc, arraytype FROM genelist WHERE userid = $userid";
	$sqlResult = mysql_query ($sql,$db);
	
	if(mysql_num_rows($sqlResult) == 0){
		echo "You have no saved gene lists.";
	}else{
		while($row = mysql_fetch_row($sqlResult)){
			$listid = $row[0];
			$name = $row[1];
			$listdesc = $row[2];
			$arraytype = $row[3];
			$agilentarraytypeSQL = "SELECT id, organism, arraydesc, version FROM agilentarrays WHERE id = $arraytype";
			$typeResult = mysql_query($agilentarraytypeSQL,$db);
			$type = mysql_fetch_row($typeResult);
			$organism = $type[1];
			echo "$organism : <a href=\"./edge3.php?orderedheatmapmodule=1&listid=$listid\">$name".": "."$listdesc</a><br>";
		}
	}
}
else{
	echo "Please login to use this feature.";
}

?>