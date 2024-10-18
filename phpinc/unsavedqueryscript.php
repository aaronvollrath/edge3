<?php
session_start();
//Script to obtain unsaved queries....
require 'edge_db_connect2.php';
#require 'edge3_db_connect.inc';
$userid = $_SESSION['userid'];
echo "<p><strong><font color=\"blue\">Click a link below to load a recent query.</font></strong><br></p>";
if($userid != ""){
	// GET THE THREE MOST RECENT QUERIES.....

	/*$sql = "SELECT query FROM savedqueries WHERE userid = $userid AND (queryname IS NULL AND (query1 IS NOT NULL AND query1 != \"\"))  ORDER BY querydate DESC LIMIT 3";
	//echo "$sql<br>";
	$sqlResult = mysql_query($sql, $db);
	$recentCount=1;
	while($row = mysql_fetch_row($sqlResult)){
		echo "<a href=\"./edge3.php?savedquery=$row[0]\">Unsaved #$recentCount</a><br>";
		$recentCount++;
	}*/
	#$sql = "SELECT query, queryname,queryurltype FROM savedqueries WHERE userid = $userid AND queryname IS NOT NULL ORDER BY querydate DESC";
	$sql = "SELECT query,queryname, queryurltype FROM savedqueries WHERE userid = $userid AND (queryname IS NULL AND (query1 IS NOT NULL AND query1 != \"\"))  ORDER BY querydate DESC LIMIT 3";
		$recentCount = 1;
		$sqlResult = $db->Execute($sql);
		while($row = $sqlResult->FetchRow()){
			$queryurl = $row[2];
			
			$recentname= "Unsaved #$recentCount";
			if(


			// This has to be checked for first given the fact 'clusteringmodule' is not unique....
			substr_count($queryurl, 'selectedclonesclusteringmodule=1') >=1) {
				echo "<a href=\"./edge3.php?selectedclonesclusteringmodule=1&savedquery=$row[0]\">$recentname</a><br>";


			}elseif(substr_count($queryurl, 'nbclassificationmodule=1') >=1) {
				echo "<a href=\"./edge3.php?nbclassificationmodule=1&savedquery=$row[0]\">$recentname</a><br>";
			}elseif(substr_count($queryurl, 'orderedheatmapmodule=1') >=1) {
				echo "<a href=\"./edge3.php?orderedheatmapmodule=1&savedquery=$row[0]\">$recentname</a><br>";
			}elseif(substr_count($queryurl, 'knearestmodule=1') >=1) {
				echo "<a href=\"./edge3.php?knearestmodule=1&savedquery=$row[0]\">$recentname</a><br>";
			}elseif(substr_count($queryurl, 'diffexprmodule=1') >=1) {
				echo "<a href=\"./edge3.php?diffexprmodule=1&savedquery=$row[0]\">$recentname</a><br>";
			}
			else{
				echo "<a href=\"./edge3.php?clusteringmodule=1&savedquery=$row[0]\">$recentname</a><br>";
			}
			$recentCount++;
		}
	}else{
		echo "<b>Create a login in order <br>to use the save queries feature!</b>";
	}
?>
