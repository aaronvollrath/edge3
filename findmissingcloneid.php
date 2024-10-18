<?php
require './phpinc/edge3_db_connect.inc';


$hybSQL = "SELECT cloneid FROM condensedhybrids where arrayid = 755 ORDER BY cloneid";



$hybResult = mysql_query($hybSQL, $db);
	while($row = mysql_fetch_row($hybResult)){
		$cloneid = $row[0];
		$hybSQL2 = "SELECT cloneid FROM condensedhybrids where arrayid = 90 and cloneid = $cloneid";
		$result = mysql_query($hybSQL2, $db);
		$id = mysql_fetch_row($result);
			if($id[0] != ""){
			echo "id = $id[0]<br>";
			}else{
				echo "Missing id = $cloneid<br>";
			}

	}

?>
