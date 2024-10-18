<?php
error_reporting(E_ALL); # pass any error messages triggered to error handler
include('./phpinc/edgephp/adodb5/adodb-errorhandler.inc.php');
include('./phpinc/edgephp/adodb5/adodb.inc.php');
$db = NewADOConnection('mysql');
$db->Connect("localhost", "root", "arod678cbc3", "edge");
?>