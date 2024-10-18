<?php

require 'edge_db_connect2.php';
include 'header.inc';
include 'edge_update_user_activity.inc';
include 'uncondensedcloneclass.inc';
require './phpinc/edge3_db_connect.inc';
$db2 = mysql_connect("localhost", "root", "arod678cbc3",TRUE);
mysql_select_db("mygo", $db2);

?>
<body>
	<div class="header">
		<img src="./GIFs/EDGE264x30.png" alt="Edge^2" align="left"></img>
		<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression</font></h4>
</div>

<?php

	if($submit == ""){
	$uclone = new UncondensedClone($cloneid,$db,$db2);
	$tableclass = 'questionclass';
	$tdclassparameter = "questionparameter";
	$tdclassresult = "questionanswertext";
	$width = 600;
	echo "<br><br>";


	$uclone->displayCloneEditForm($tableclass, $tdclassparameter, $tdclassresult, $width);
	}else{

	echo "<br><br>";
	echo "<table>";
		$urlstring = "updateuncondensedannotation.php?cloneid=$cloneid";
		foreach($_POST as $key=>$val){
			if($key != "submit"){
				echo "<tr><td>$key</td><td>$val</td></tr>";
				$urlstring .= "&$key=$val";
			}
		}
	echo "</table>";
	$nostring = "updateuncondensedclone.php?cloneid=$cloneid";
	echo "<br>$nostring<br>";
	echo "Update with the values in the table above for clone ID: $cloneid ?";
	echo "<br><a href=\"$urlstring\">Yes!</a>   <a href=\"./updateuncondensedclone.php?cloneid=$cloneid\">NO!</a><br>";

	}

?>