<?php
ini_set('display_errors', 1);
include('globalvariables.inc.php');
error_reporting(E_ALL); # pass any error messages triggered to error handler
include('adodb-errorhandler.inc.php');
include('adodb.inc.php');
 $db = NewADOConnection($edgedbtype);
 $db->Connect($edgedbserver,$edgedbuser, $edgedbuserPW, $edgedb);
 
require 'edge3-login-form-check.inc';
//include('edge_check_login2.php');

?>

