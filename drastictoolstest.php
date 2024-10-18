<?php
include('utilityfunctions.inc');
define("PATHDRASTICTOOLS", "/var/www/DrasticTools/");
$server="localhost";
$user="root";
$pw="arod678cbc3";
$db="edge";
$val = $_GET['var1'];
$table = "agilent_arrayinfo";//"agilent_arrayinfo";
//$table= $_GET['users'];//
$fuck = $table;
//$table = $fuck;
//$server = $_GET['localhost'];
include (PATHDRASTICTOOLS . "drasticSrcMySQL.class.php");

/*$options = array (
        "add_allowed" => false,
        "delete_allowed" => false,
        "editablecols" => array("tissue", "organism"/*"name", "birthday", "yeartest", "gender",
		"realtest", "numtest", "timetest", "tstest", "dttest", 
		"waffle", "blobtest" ),
        "sortcol" => "arrayid",
        "sort" => "n"
);*/
$src = new drasticSrcMySQL($server, $user, $pw, $db, $table);
//analyze($src->result);
	echo " new drasticSrcMySQL($server, $user, $pw, $db, $table)<br>";
	
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="/DrasticTools/css/grid_default.css"/>
<title><?php echo "$table"; ?></title>
</head>
<body>


<script type="text/javascript" src="/DrasticTools/js/mootools-1.2-core.js"></script>
<script type="text/javascript" src="/DrasticTools/js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="/DrasticTools/js/drasticGrid.js"></script>


<div id="thegrid"></div>
<script type="text/javascript">
var thegrid = new drasticGrid('thegrid', {pathimg:"/DrasticTools/img/", pagelength:100});
</script>

</body>
</html>
