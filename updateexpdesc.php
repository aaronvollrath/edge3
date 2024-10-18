<?php
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}

require './phpinc/edge3_db_connect.inc';
include 'utilityfunctions.inc';
require("fileupload-class.php");
include 'edge_update_user_activity.inc';
$expid = $_GET['expid'];
$expSQL = "SELECT descrip FROM agilent_experimentsdesc WHERE expid=$expid";

		$expResult = mysql_query($expSQL, $db);

		$row = mysql_fetch_array($expResult);
		$desc = trim($row[0]);
		echo $desc;
?>