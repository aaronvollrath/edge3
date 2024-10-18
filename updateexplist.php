<?php
/*  NOTE: if you want to update the types of organisms so they're displayed on the icons, utilize this file:
	./phpinc/createdraganddroplist.inc

	also, you'll need to create the appropriate icons
*/
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}
require './phpinc/edge3_db_connect.inc';
//require("fileupload-class.php");
include 'edge_update_user_activity.inc';
// What arrays are are associated w/ this experiment?
$expList = "";
$filenum = rand(0, 25000);
	$file = "/var/www/html/edge2/IMAGES/explist$filenum.json";
	$file2 = "/var/www/html/edge2/IMAGES/OUT$filenum.json";

	$command = "touch $file";
	$fd = fopen($file, 'w');
	rewind($fd);
	$command = "touch $file2";
	$fd2 = fopen($file2, 'w');
	rewind($fd2);
	fwrite($fd2, "testing....");
	fclose($fd2);

if($expListType == ""){
	$expSQL = "SELECT expid FROM agilent_experiments ORDER BY expid";
		//echo "$expSQL";
		fwrite($fd, $expSQL);
			$expResult = mysql_query($expSQL, $db);
	$arrayList = "{\"items\": [";
	$firstarray = 0;
			while($row = mysql_fetch_row($expResult)){
				$arrayid = $row[0];
				$arraySQL = "SELECT arrayid, arraydesc, arraytype FROM agilent_arrayinfo WHERE arrayid=$arrayid";
				fwrite($fd, $arraySQL);
				if($firstarray != 0){
					$arrayList .= ",";
				}
				$firstarray++;
				$arrayResult = mysql_query($arraySQL, $db);
				while($arrayRow = mysql_fetch_row($arrayResult)){
					$arrayid = $arrayRow[0];
					$arrayDesc = $arrayRow[1];
					$arraytype = $arrayRow[2];
					$arrayDesc = str_replace('#', "Num", $arrayDesc);
					require('./phpinc/createdraganddroplist.inc');
					//$arrayList .= "<div class=\"dojoDndItem\" dndType=\"array\" dndData=\"$arrayid\" title=\"$arrayDesc\"><img src=\"./images/mousemicroarrayicon.png\"/>$arrayDesc</div>";
				}



			}
		$arrayList .= "]}";
		fwrite($fd, $arrayList);
				fclose($fd);
	echo $arrayList;
}else{
 		if($expListType == 1){

			$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			//echo $userSQL;
			$idResult = mysql_query($userSQL, $db);
			$idRow = mysql_fetch_row($idResult);
			$id = $idRow[0];
			$expList = "{\"items\": [";
			//analyze($_SESSION);
			$ownerSQL = "SELECT expid, expname from agilent_experimentsdesc";
			//echo $ownerSQL;
			$firstexp = 0;
			$expResult = mysql_query($ownerSQL, $db);
				while($expRow = mysql_fetch_row($expResult)){
					$expid = $expRow[0];
					$expName = $expRow[1];
					//$arraytype = $arrayRow[2];
					if($firstexp != 0){
					$expList .= ",";
					}
				$firstexp++;
				$exp = 1;
					require('./phpinc/createdraganddroplist.inc');
					//echo "<div class=\"dojoDndItem\" dndType=\"exp\" dndData=\"$expid\" title=\"$expName\">$expName</div>";
				}
				$expList .= "]}";
				fwrite($fd, $expList);
				fclose($fd);
			echo $expList;
		}elseif($expListType== 2){
		// We want to display all available arrays that the user "owns"
			//echo "this is the username:". $_SESSION['username'];
			$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			//echo $userSQL;
			$idResult = mysql_query($userSQL, $db);
			$idRow = mysql_fetch_row($idResult);
			$id = $idRow[0];
			$expList = "{\"items\": [";
			//analyze($_SESSION);
			$ownerSQL = "SELECT expid, expname from agilent_experimentsdesc WHERE (ownerid = $id)";
			//echo "$ownerSQL<br>";
			$firstexp = 0;
			$expResult = mysql_query($ownerSQL, $db);
				while($expRow = mysql_fetch_row($expResult)){
					$expid = $expRow[0];
					$expName = $expRow[1];
					//$arraytype = $arrayRow[2];
					if($firstexp != 0){
					$expList .= ",";
					}
				$firstexp++;
				$exp = 1;
					require('./phpinc/createdraganddroplist.inc');
					//echo "<div class=\"dojoDndItem\" dndType=\"exp\" dndData=\"$expid\" title=\"$expName\">$expName</div>";
				}
				$expList .= "]}";
				fwrite($fd, $expList);
				fclose($fd);
			echo $expList;






		}else{
		/*
			// We want to display all available arrays that the user "owns" and are not associated w/ any experiment groups
			$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			//echo $userSQL;
			$idResult = mysql_query($userSQL, $db);
			$idRow = mysql_fetch_row($idResult);
			$id = $idRow[0];
			$arrayList = "{\"items\": [";
			$ownerSQL = "SELECT DISTINCT a.arrayid, a.arraydesc, a.arraytype
FROM agilent_arrayinfo AS a
LEFT JOIN agilent_experiments AS e
USING ( arrayid )
WHERE (e.arrayid IS NULL AND a.ownerid = $id)";
			$firstarray = 0;
			$arrayResult = mysql_query($ownerSQL, $db);
				while($arrayRow = mysql_fetch_row($arrayResult)){
					$arrayid = $arrayRow[0];
					$arrayDesc = $arrayRow[1];
					$arraytype = $arrayRow[2];
					if($firstarray != 0){
						$arrayList .= ",";
					}
				$firstarray++;
					require('./phpinc/createdraganddroplist.inc');
					//$arrayList .= "<div class=\"dojoDndItem\" dndType=\"array\" dndData=\"$arrayid\" title=\"$arrayDesc\"><img src=\"./images/mousemicroarrayicon.png\"/>$arrayDesc</div>";
				}
				$arrayList .= "]}";
				fwrite($fd, $arrayList);
				fclose($fd);
			echo $arrayList;

*/
		}



}




?>
