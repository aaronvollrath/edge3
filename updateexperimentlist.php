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
// What experiments are associated w/ this user?
$expList = "";
$filenum = rand(0, 25000);
	$file = "$IMAGESdir/experimentlist$filenum.json";

	$command = "touch $file";
	$fd = fopen($file, 'w');
	rewind($fd);

if(isset($_GET['expListType'])){
	$expListType = $_GET['expListType'];
}else{
	$expListType = "";
}
$privval = 99;

if($expListType == ""){
	if($privval != 99){
		$expSQL = "SELECT expid FROM agilent_experimentsdesc WHERE ownerid=$userid ORDER BY expid";
	}else{
		$expSQL = "SELECT expid,expname FROM agilent_experimentsdesc ORDER BY expid";
	}
		//echo "$expSQL";
		fwrite($fd, $expSQL);
		$expResult = $db->Execute($expSQL);
		$expList = "{\"items\": [";
		$firstexp = 0;
		while($row = $expResult->FetchRow()){
			$expid = $row[0];
			$expName = $row[1];
			
			$arraySQL = "SELECT expid, expname FROM agilent_experimentsdesc WHERE expid=$expid";
			
			fwrite($fd, $arraySQL);
			if($firstexp != 0){
				$expList .= ",";
			}
			$firstexp++;
			
				$expName = str_replace('#', "Num", $expName);
				$exp = 1;  # Need to set a value for the following required code
				require('./phpinc/createdraganddroplist.inc');
		}
	$expList .= "]}";
	fwrite($fd, $expList);
			fclose($fd);
	echo $expList;
}else{
 		if($expListType == 1){

			if($privval != 99){
				$expSQL = "SELECT expid FROM agilent_experimentsdesc WHERE ownerid=$userid ORDER BY expid";
			}else{
				$expSQL = "SELECT expid,expname FROM agilent_experimentsdesc ORDER BY expid";
			}
				//echo "$expSQL";
				fwrite($fd, $expSQL);
				$expResult = $db->Execute($expSQL);
				$expList = "{\"items\": [";
				$firstexp = 0;
				while($row = $expResult->FetchRow()){
					$expid = $row[0];
					$expName = $row[1];
					
					$arraySQL = "SELECT expid, expname FROM agilent_experimentsdesc WHERE expid=$expid";
					
					fwrite($fd, $arraySQL);
					if($firstexp != 0){
						$expList .= ",";
					}
					$firstexp++;
					
						$expName = str_replace('#', "Num", $expName);
						$exp = 1;  # Need to set a value for the following required code
						require('./phpinc/createdraganddroplist.inc');
				}
			$expList .= "]}";
			fwrite($fd, $expList);
			fclose($fd);
			echo $expList;
		}elseif($expListType== 2){
		// We want to display all available experiments owns and that are already in a group they administer
			#echo "this is the username:". $_SESSION['username'];
			$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			//echo $userSQL;
			$idResult = $db->Execute($userSQL);//mysql_query($userSQL, $db);
			$idRow = $idResult->FetchRow();//mysql_fetch_row($idResult);
			$id = $idRow[0];
			# what user groups does this user administer?	
			$groupsSQL = "SELECT usergroupid from usergroupadmins WHERE userid = $userid";
			$groupsResult = $db->Execute($groupsSQL);
			#echo $groupsSQL;
			# for each usergroupid, look up what experiments are associated with it.
				$expList = "{\"items\": [";
			$firstexp = 0;
			while($row=$groupsResult->FetchRow()){
				$expSQL = "SELECT e.expid, i.expname FROM expusergroupassoc as e, agilent_experimentsdesc as i WHERE (e.expid = i.expid) AND e.usergroupid = $row[0]";
				#echo "<br>$expSQL<br>";
				$expResult = $db->Execute($expSQL);
				while($expinfo = $expResult->FetchRow()){
					$expid = $expinfo[0];
					$expName = $expinfo[1];
					if($firstexp != 0){
						$expList .= ",";
					}
					$firstexp++;
					$exp = 1;  # Need to set a value for the following required code
					require('./phpinc/createdraganddroplist.inc');
					//$expList .= "<div class=\"dojoDndItem\" dndType=\"array\" dndData=\"$expid\" title=\"$expName\"><img src=\"./images/mousemicroarrayicon.png\"/>$expName</div>";
				}
			}
				$expList .= "]}";
				fwrite($fd, $expList);
				fclose($fd);
			echo $expList;





		}else{
			/*// We want to display all available experiments that the user "owns" and are not associated w/ any groups
			$userSQL = "SELECT id FROM users WHERE username = \"". $_SESSION['username']."\"";
			//echo $userSQL;
			$idResult = $db->Execute($userSQL);//mysql_query($userSQL, $db);
			$idRow = $idResult->FetchRow();//mysql_fetch_row($idResult);
			$id = $idRow[0];
			$expList = "{\"items\": [";
			$ownerSQL = "SELECT DISTINCT e.expid, e.expname
				FROM agilent_experimentsdesc AS e
				LEFT JOIN expusergroupassoc AS u
				USING ( expid )
				WHERE (e.arrayid IS NULL AND a.ownerid = $id)";
			//echo "$ownerSQL<br>";
			$firstexp = 0;
			$arrayResult = $db->Execute($ownerSQL);//mysql_query($ownerSQL, $db);
				#while($arrayRow = mysql_fetch_row($arrayResult)){
				while($arrayRow = $arrayResult->FetchRow()){
					$expid = $arrayRow[0];
					$expName = $arrayRow[1];
					$arraytype = $arrayRow[2];
					if($firstexp != 0){
						$expList .= ",";
					}
				$firstexp++;
					$exp = 1;  # Need to set a value for the following required code
					require('./phpinc/createdraganddroplist.inc');
					//$expList .= "<div class=\"dojoDndItem\" dndType=\"array\" dndData=\"$expid\" title=\"$expName\"><img src=\"./images/mousemicroarrayicon.png\"/>$expName</div>";
				}
				$expList .= "]}";
				fwrite($fd, $expList);
				fclose($fd);
			echo $expList;
			*/
	#if($privval != 99){
		 $expSQL = "SELECT expid,expname FROM agilent_experimentsdesc WHERE ownerid=$userid ORDER BY expid";
	#}else{
	#	$expSQL = "SELECT expid,expname FROM agilent_experimentsdesc ORDER BY expid";
	#}
		#echo "$expSQL";
		fwrite($fd, $expSQL);
		$expResult = $db->Execute($expSQL);
		$expList = "{\"items\": [";
		$firstexp = 0;
		while($row = $expResult->FetchRow()){
			$expid = $row[0];
			$expName = $row[1];
			
			
			
			
			if($firstexp != 0){
				$expList .= ",";
			}
			$firstexp++;
			
				$expName = str_replace('#', "Num", $expName);
				$exp = 1;  # Need to set a value for the following required code
				require('./phpinc/createdraganddroplist.inc');
		}
	$expList .= "]}";
	fwrite($fd, $expList);
			fclose($fd);
	echo $expList;


		}


}




?>
