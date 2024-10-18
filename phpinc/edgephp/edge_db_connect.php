<?php

//require the PEAR::DB classes.
// This may allow us to be database agnostic...

require_once 'DB.php';


$db_engine = 'mysql';
$db_user = 'vollrath';
$db_pass = 'arod678cbc3'; //'3d4fual780';
$db_host = 'localhost';
$db_name = 'edge';

$datasource = $db_engine.'://'.
			  $db_user.':'.
			  $db_pass.'@'.
		 	  $db_host.'/'.
	  		  $db_name;


$db_object = DB::connect($datasource, TRUE);

/* assign database object in $db_object,

if the connection fails $db_object will contain

the error message. */

// If $db_object contains an error:

// error and exit.

if(DB::isError($db_object)) {
	die($db_object->getMessage());
}

$db_object->setFetchMode(DB_FETCHMODE_ASSOC);

// we write this later on, ignore for now.

include('edge_check_login.php');

?>

