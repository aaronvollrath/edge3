<?php

/* check login script, included in db_connect.php. */

session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
	$logged_in = 0;
	return;
} else {

	// remember, $_SESSION['password'] will be encrypted.

	if(!get_magic_quotes_gpc()) {
		$_SESSION['username'] = addslashes($_SESSION['username']);
	}


	// addslashes to session username before using in a query.
	$pass = $db_object->query("SELECT password FROM users WHERE username = '".$_SESSION['username']."'");

	if(DB::isError($pass)) {
		$logged_in = 0;
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		// kill incorrect session variables.
	}

	$db_pass = $pass->fetchRow();

	// now we have encrypted pass from DB in 
	//$db_pass['password'], stripslashes() just incase:

	$db_pass['password'] = stripslashes($db_pass['password']);
	$_SESSION['password'] = stripslashes($_SESSION['password']);



	//compare:



	if($_SESSION['password'] == $db_pass['password']) { 
		// valid password for username
		$logged_in = 1; // they have correct info
					// in session variables.
	} else {
		$logged_in = 0;
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		// kill incorrect session variables.
	}
}


// clean up
unset($db_pass['password']);

$_SESSION['username'] = stripslashes($_SESSION['username']);

?>