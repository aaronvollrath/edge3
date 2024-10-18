<?php
require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
include 'edge3header.inc';
require "formcheck.inc";
require './phpinc/globalfilelocations.inc';

$tableNum = $_GET['tableNum'];

?>
<script src="sorttable.js"></script>

<body>

	
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