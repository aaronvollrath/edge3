<?php
define("PATHDRASTICTOOLS", "/var/www/DrasticTools/");
$server="localhost";
$user="root";
$pw="arod678cbc3";
$db="edge";
$table = "agilent_experimentsdesc";
include (PATHDRASTICTOOLS . "drasticSrcMySQL.class.php");
//if(isset($_POST['submit'])){
	
	//$table= $_POST['table'];
	//$table = $_POST['table'];
	$options = array (
		"add_allowed" => false,
		"delete_allowed" => false
	);
	$src = new drasticSrcMySQL($server, $user, $pw, $db, $table);
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
require '../edge3-login-form-check.inc';
if ($logged_in == 0) {
	//die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
	//echo "You are not logged!";
}

$db = mysql_connect("localhost", "root", "arod678cbc3");

mysql_select_db("edge", $db);
require("fileupload-class.php");
include 'edge_update_user_activity.inc';
 //require './lib/dsrte.php';
//include 'utilityfunctions.inc';
require("globalfilelocations.inc");
//analyze($_SESSION);
$privval = $_SESSION['priv_level'];





	//echo "$src";
//}
//analyze($_SESSION);

?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="/DrasticTools/css/grid_default.css"/>
<title>Edit <?php echo $table;?> table</title>
</head>
<body>

<?php

if($privval == 99){
?>
<script type="text/javascript" src="/DrasticTools/js/mootools-1.2-core.js"></script>
<script type="text/javascript" src="/DrasticTools/js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="/DrasticTools/js/drasticGrid.js"></script>

<div id="thegrid"></div>
<script type="text/javascript">
var thegrid = new drasticGrid('thegrid', {pathimg:"/DrasticTools/img/", pagelength:100});
</script>
<?php
}else{
die("you are not authorized to view this page");
}
?>
</body>
</html>
