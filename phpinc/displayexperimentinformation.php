<?php
require 'edge_db_connect2.php';

$expid = $_GET['expid'];
$yourExpSQL = "SELECT expid, expname, ownerid, descrip FROM agilent_experimentsdesc WHERE expid=$expid";

		$expresult = $db->Execute($yourExpSQL);
		if(!$expresult){
			die("your query returned no result...");
		}else{
			$row = $expresult->FetchRow();
			$expid = $row[0];
			$expname = $row[1];
			$ownerid = $row[2];
			$desc = $row[3];
			$ownerSQL = "SELECT firstname, lastname FROM users WHERE id = $ownerid";
			$ownerResult = $db->Execute($ownerSQL);
			$ownernamerow = $ownerResult->FetchRow();
			$ownername = $ownernamerow[0]." ".$ownernamerow[1];
		}
		echo "<br><br><div style='width:600px'><p>$desc</p></div>";

?>
