<?php
require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
include 'header.inc';
require "formcheck.inc";
require './phpinc/globalfilelocations.inc';

$tableNum = $_GET['tableNum'];

?>
<script src="sorttable.js"></script>

<body>

	<div class="header">

		<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression^3</font></h4>
	</div>
<br>
<br>
 <h3 class="contenthead">Clustering Table</h3>
<div class="content">


<p class="styletext">

<?php
$tableFile = ".$IMAGESreldir/table$tableNum";
include $tableFile;
?>
</p>

 </div>

 <div class="boxfooter"></div>
</body>
</html>
