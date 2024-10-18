<?php
session_start();
$debug = 0;  // if 1 and debugid is the user id being used, then display messages...
$debugid = 1;  // set the debugid to whatever userid you want it to be.  default set to 1.
//Script to obtain unsaved queries....
require 'edge_db_connect2.php';
#require 'edge3_db_connect.inc';
$userid = $_SESSION['userid'];
echo "<p><strong><font color=\"blue\">Click a link below to load a saved query.</font></strong><br></p>";
	if($userid !=""){// GET THEIR SAVED QUERIES.....
		$sql = "SELECT query, queryname,queryurltype FROM savedqueries WHERE userid = $userid AND queryname IS NOT NULL ORDER BY querydate DESC";
		if($userid == $debugid && $debug == 1){
			echo "$sql<br>";
		}
		$sqlResult = $db->Execute($sql);
		while($row = $sqlResult->FetchRow()){
			$queryurl = $row[2];
			if($userid == $debugid && $debug == 1){
				echo "queryurl: $queryurl<br>";
			}
			if(


			// This has to be checked for first given the fact 'clusteringmodule' is not unique....
			substr_count($queryurl, 'selectedclonesclusteringmodule=1') >=1) {
				echo "<a href=\"./edge3.php?selectedclonesclusteringmodule=1&savedquery=$row[0]\">$row[1]</a><br>";


			}elseif(substr_count($queryurl, 'nbclassificationmodule=1') >=1) {
				echo "<a href=\"./edge3.php?nbclassificationmodule=1&savedquery=$row[0]\">$row[1]</a><br>";
			}elseif(substr_count($queryurl, 'orderedheatmapmodule=1') >=1) {
				echo "<a href=\"./edge3.php?orderedheatmapmodule=1&savedquery=$row[0]\">$row[1]</a><br>";
			}elseif(substr_count($queryurl, 'knearestmodule=1') >=1) {
				echo "<a href=\"./edge3.php?knearestmodule=1&savedquery=$row[0]\">$row[1]</a><br>";
			}elseif(substr_count($queryurl, 'diffexprmodule=1') >=1) {
				echo "<a href=\"./edge3.php?diffexprmodule=1&savedquery=$row[0]\">$row[1]</a><br>";
			}
			else{
				echo "<a href=\"./edge3.php?clusteringmodule=1&savedquery=$row[0]\">$row[1]</a><br>";
			}
		}
	}else{
			echo "<b>Create a login in order <br>to use the save queries feature!</b>";
	}

?>
