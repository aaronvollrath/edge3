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
require("globalfilelocations.inc");
#require './phpinc/edge3_db_connect.inc';
//require("fileupload-class.php");
include 'edge_update_user_activity.inc';
// What arrays are are associated w/ this experiment?
$arrayList = "";
$filenum = rand(0, 25000);
	$file = "$IMAGESdir/arraylist$filenum.json";

	$command = "touch $file";
	$fd = fopen($file, 'w');
	rewind($fd);
if(isset($_GET['expid'])){
	$expid = $_GET['expid'];
}else{
	$expid = "";
}
if(isset($_GET['arrayListType'])){
	$arrayListType = $_GET['arrayListType'];
}else{
	$arrayListType = "";
}

if($arrayListType == ""){
	$expSQL = "SELECT arrayid FROM agilent_experiments WHERE expid=$expid ORDER BY arrayid";
		//echo "$expSQL";
		fwrite($fd, $expSQL);
			$expResult = $db->Execute($expSQL);//mysql_query($expSQL, $db);
	$arrayList = "{\"items\": [";
	$firstarray = 0;
			#while($row = mysql_fetch_row($expResult)){
			while($row = $expResult->FetchRow()){
				$arrayid = $row[0];
				$arraySQL = "SELECT arrayid, arraydesc, arraytype FROM agilent_arrayinfo WHERE arrayid=$arrayid";
				fwrite($fd, $arraySQL);
				if($firstarray != 0){
					$arrayList .= ",";
				}
				$firstarray++;
				$arrayResult = $db->Execute($arraySQL);//mysql_query($arraySQL, $db);
				#while($arrayRow = mysql_fetch_row($arrayResult)){
				while($arrayRow =$arrayResult->FetchRow()){
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
 		if($arrayListType == 1){

			$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			//echo $userSQL;
			$idResult = $db->Execute($userSQL);//mysql_query($userSQL, $db);
			$idRow = $idResult->FetchRow();//mysql_fetch_row($idResult);
			$id = $idRow[0];
			$arrayList = "{\"items\": [";
			//analyze($_SESSION);
			$ownerSQL = "SELECT arrayid, arraydesc, arraytype from agilent_arrayinfo";
			//echo $ownerSQL;
			$firstarray = 0;
			$arrayResult = $db->Execute($ownerSQL);//mysql_query($ownerSQL, $db);
				#while($arrayRow = mysql_fetch_row($arrayResult)){
				while($arrayRow =$arrayResult->FetchRow()){
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
		}elseif($arrayListType== 2){
		// We want to display all available arrays that the user "owns"
			//echo "this is the username:". $_SESSION['username'];
			$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			//echo $userSQL;
			$idResult = $db->Execute($userSQL);//mysql_query($userSQL, $db);
			$idRow = $idResult->FetchRow();//mysql_fetch_row($idResult);
			$id = $idRow[0];
			$arrayList = "{\"items\": [";
			//analyze($_SESSION);
			$ownerSQL = "SELECT arrayid, arraydesc, arraytype from agilent_arrayinfo WHERE (ownerid = $id)";
			//echo $ownerSQL;
			$firstarray = 0;
			$arrayResult = $db->Execute($ownerSQL);//mysql_query($ownerSQL, $db);
				#while($arrayRow = mysql_fetch_row($arrayResult)){
				while($arrayRow = $arrayResult->FetchRow()){
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





		}else{
			// We want to display all available arrays that the user "owns" and are not associated w/ any experiments
			$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			//echo $userSQL;
			$idResult = $db->Execute($userSQL);//mysql_query($userSQL, $db);
			$idRow = $idResult->FetchRow();//mysql_fetch_row($idResult);
			$id = $idRow[0];
			$arrayList = "{\"items\": [";
			$ownerSQL = "SELECT DISTINCT a.arrayid, a.arraydesc, a.arraytype
FROM agilent_arrayinfo AS a
LEFT JOIN agilent_experiments AS e
USING ( arrayid )
WHERE (e.arrayid IS NULL AND a.ownerid = $id)";
			//echo "$ownerSQL<br>";
			$firstarray = 0;
			$arrayResult = $db->Execute($ownerSQL);//mysql_query($ownerSQL, $db);
				#while($arrayRow = mysql_fetch_row($arrayResult)){
				while($arrayRow = $arrayResult->FetchRow()){
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


		}


}




?>
