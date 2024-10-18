<?php

/*  Description:  This script will create an html table listing the condensed clones and their annotations.  
*/


require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
include 'header.inc';

$sql = "SELECT cloneid, annname, refseq FROM condensedannotations ORDER BY cloneid";
//echo "$sql<br>";
$result = mysql_query($sql, $db);


?>

<body onLoad="fixwindow(800,800)">
	<div class="header">
		<img src="./GIFs/EDGE264x30.png" alt="Edge^2" align="left"></img>
		<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression</font></h2>
	</div>
<br>
<div class="updatecontent">
<?php
echo "<table class=\"question\"><tr><td>Clone ID</td><td>Annotated Name</td><td>Refseq</td></tr>";
while($row = mysql_fetch_row($result)){
$refseqtd="<a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$this->refseq\" target=\"_blank\">$row[2]</a>";
	echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$refseqtd</td></tr>";
}
?>
</table>
</div>


</body>
