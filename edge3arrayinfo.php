<?php

include 'utilityfunctions.inc';
require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
// Parse out feature and arrayid...
$arrayid=$_GET['arrayid'];
$userid = $_SESSION['userid'];
//analyze($_SESSION);
?>
<body onLoad="fixwindow(800,800)">
	<div class="header">
		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression</font></h4>
</div>
<script type="text/javascript" src="./dojo-release-1.0.0/dojo/dojo.js"
                djConfig="parseOnLoad: true"></script>
    <script type="text/javascript">
       dojo.require("dojo.parser");
       dojo.require("dijit.form.Button");
	function openDeleteArray(){
		
	//alert ("TESTING...");
	window.opener=self;
  	 window.close();
	window.open('edge3deletearray.php?arrayid=<?php echo $arrayid; ?>','Delete Array', 'fullscreen');
  	// window.open('test.htm','Test','fullscreen'); 

	}
     </script>
<br>
<br>
<br>
<?php




$sql = "SELECT e.arraydesc, a.organism, a.arraydesc, a.version  FROM agilentarrays AS a, agilent_arrayinfo AS e WHERE e.arrayid = $arrayid and a.id = e.arraytype";


$sqlResult = mysql_query($sql, $db);
$row = mysql_fetch_row($sqlResult);

$expdesc = $row[0];
$organism = $row[1];
$arraydesc = $row[2];
$version = $row[3];


?>

<?php


if($userid == ""){
echo "You are not logged in.<br>";
echo "</body>";
exit(0);

}


echo "<input name=\"arrayid\" type=\"hidden\" value=\"$arrayid\">\n";
?>
<table id="results">
<tr><td>Array ID#</td><td><?php echo $arrayid; ?></td></tr>
<tr><td>Array Name</td><td><?php echo $expdesc; ?></td></tr>
<tr><td>Array Type</td><td><?php echo $arraydesc; ?></td></tr>
<tr><td>Organism</td><td><?php echo $organism; ?></td></tr>
<tr><td>Version</td><td><?php echo $version; ?></td></tr>
</table>

<?php

	//if($userid == 1){
?>
    <button dojoType="dijit.form.Button" onclick="window.open('edge3editarrayinfo.php?arrayid=<?php echo $arrayid; ?>')">
                Click to edit this array.
        </button>

<?php
	// if this is an administrator, this individual can delete the array and associated data....
	$privsql = "SELECT priv_level FROM users WHERE id = $userid";
//echo "<br>$privsql<br>";
	$result = mysql_query($privsql, $db);
	$row = mysql_fetch_row($result);
	$priv_level = $row[0];
	if($priv_level == 99){
?>
		 <button dojoType="dijit.form.Button" onclick="window.opener=self;
  	// window.close();
	window.open('edge3deletearray.php?arrayid=<?php echo $arrayid; ?>','Delete Array', 'fullscreen');
	self.close();
  	// window.open('test.htm','Test','fullscreen'); ">
                Click to <font color="red"><strong>DELETE</strong></font> this array.</button>
<?php
	}

?>

<?php
//}
?>

</body>
