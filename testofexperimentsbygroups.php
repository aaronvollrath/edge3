<?php


/*  NOTE: if you want to update the types of organisms so they're displayed on the icons, utilize this file:
	./phpinc/createdraganddroplist.inc

	also, you'll need to create the appropriate icons

require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}
require("globalfilelocations.inc");
#require './phpinc/edge3_db_connect.inc';
//require("fileupload-class.php");
include 'edge_update_user_activity.inc';

include 'utilityfunctions.inc';

$priv_level = 99;
$userid = 1;
*/
echo 'in testofexperimentbygroups.php <hr>';
?>



<div id="tabs" style="width: 700px; float:left">
            <ul>

	<?php

		// Create tabs based on organisms in database....
		$sql = "SELECT id, organism FROM agilentarrays ORDER BY id ASC";
		$sqlResult = $db->Execute($sql);
		$orgdisplayarray = array();
		$organismcount = 0;
		while($row = $sqlResult->FetchRow()){
			$id = $row[0];
			$organism = $row[1];
			$orgdisplayarray[$organismcount] = "";
			$organismcount++;
	?>
                	<li><a href="#fragment-<?php echo $id;?>"><span><?php echo $organism;?></span></a></li>
	<?php
		}
	?>
            </ul>
	   	<?php
		
		$sqlResult = $db->Execute($sql);
		$organismcount = 0;
		$organismtypearray = array();
		
		while($row = $sqlResult->FetchRow()){ # iterating through organisms......
			$arraytype = $row[0];
			array_push($organismtypearray, $arraytype);
			$organism = $row[1];
			$expidarray = array();
			#echo "the organism is $organism<br>";	
			#analyze($expidarray);
		?>
			<div id="fragment-<?php echo $arraytype;?>">
		<?php
			

			// What arrays/experiments are associated w/ this organism???
			$orgexpsql= "SELECT DISTINCT e.expid
					FROM agilent_arrayinfo AS i, agilent_experiments AS e
					WHERE i.arraytype = $arraytype
					AND i.arrayid = e.arrayid
					ORDER BY e.expid";
			
			$orgexpresult = $db->Execute($orgexpsql);
			$thisorgexps = array(); # stores the exps for later comparison for group checks....
			$thisorgstr = "";
			#echo "$orgexpsql<br>";
			while($orgexprow = $orgexpresult->FetchRow()){
				
			
				$expid = $orgexprow[0];
				$expdescSQL = "SELECT expname,ownerid FROM agilent_experimentsdesc WHERE expid = $expid AND ownerid = $userid";
				
				
				$expdescResult =  $db->Execute($expdescSQL);// mysql_query($expdescSQL, $db);
				
				$expdescVals = $expdescResult->FetchRow();//mysql_fetch_row($expdescResult);
				if($expdescResult->RecordCount() > 0){
					array_push($expidarray,$expid);
				}
				
			}
			
			# are there any groups that this user is in?
			# we need to find out and find the difference between thisorgexps array
			$usergroupsql = "SELECT usergroupid FROM usergroupmembers WHERE userid = $userid";
			$usergroupresult = $db->Execute($usergroupsql);
			$usersgroupexps = array();
			if($usergroupresult->RecordCount() > 0){
				// What are the experiment/user group associations for this user?
				while($row = $usergroupresult->FetchRow()){
					$assocSQL = "SELECT expid FROM expusergroupassoc WHERE usergroupid = $row[0]";
					$assocResult = $db->Execute($assocSQL);
					while($exprow = $assocResult->FetchRow()){
						$expid = $exprow[0];
						array_push($expidarray,$expid);
						
					}

				}
				$orgdisplayarray[$organismcount] = $thisorgstr;
			}
			#echo "before unique array....<br>";
			#analyze($expidarray);
			$expidarray = array_unique($expidarray);
			#echo "after unique array,....<br>";
			#analyze($expidarray);
			foreach($expidarray as $expid){
				$expdescSQL = "SELECT expname,descrip FROM agilent_experimentsdesc WHERE expid = $expid";
							//echo "$expdescSQL<br>";
				
				$expdescResult =  $db->Execute($expdescSQL);// mysql_query($expdescSQL, $db);
				
				$expdescVals = $expdescResult->FetchRow();//mysql_fetch_row($expdescResult);

				// we need to check to see if there is an actual description fort this id
				if($expdescVals){
					$expdescVal = $expdescVals[0];

					
							
							$arraysql = "SELECT i.arrayid, i.arraydesc
							FROM agilent_arrayinfo AS i, agilent_experiments AS e
							WHERE i.arraytype = $arraytype
							AND i.arrayid = e.arrayid
							AND e.expid = $expid
							ORDER BY e.expid";
							
							$currentarraycount = 0;
							$arraysqlresult = $db->Execute($arraysql);//mysql_query($arraysql,$db);
#   displayExperimentInformation(expid)
								if($arraysqlresult->RecordCount() > 0){
								echo "<tr><td colspan='3'><div dojoType='dijit.TitlePane' title='$expdescVal' open='false'><table><tr><td colspan='5'><a href='./phpinc/displayexperimentinformation.php?expid=$expid' target='_blank'>Experiment Information</a></tr><tr>";
								$currentarraycount = 0;
								while($arrayrow = $arraysqlresult->FetchRow()){
									$arrayid = $arrayrow[0];
									$arraydesc = $arrayrow[1];
									if($currentarraycount % 5 == 0){
										echo "</tr>";
										
									}
										
									if(isset($_GET['savedquery'])){
										// What array needs to be checked?
										if(array_search($arrayid, $savedarrayvals) > -1){
											echo  "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\" checked>$arraydesc&nbsp&nbsp&nbsp&nbsp</option></td>";
											
										}else{
											echo  "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\">$arraydesc&nbsp&nbsp&nbsp&nbsp</td>";
											
							
										}
									}else{
											echo  "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\">$arraydesc&nbsp&nbsp&nbsp&nbsp</td>";
									}
									$currentarraycount++;
									
								}
								echo  "</tr></table></div></td></tr>";	
							}
						} // end of if conditional check;
			}

?>
			</div>
<?php
			#echo "<hr>organism count is $organismcount<hr>";
			$organismcount++;
			}
?>

</div>
