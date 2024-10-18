<?php

/* check login script, included in db_connect.php. */
session_start();
header("Cache-control: private");
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
	$logged_in = 0;

	return;
} else {

	// remember, $_SESSION['password'] will be encrypted.

	if(!get_magic_quotes_gpc()) {
		$_SESSION['username'] = addslashes($_SESSION['username']);
	}


	// addslashes to session username before using in a query.
	$pass = $db_object->query("SELECT id, password, firstname, lastname, priv_level FROM users WHERE username = '".$_SESSION['username']."'");

	if(DB::isError($pass)) {
		$logged_in = 0;
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		unset($_SESSION['priv_level']);
		// kill incorrect session variables.
		//**********************************************************
		 //remove the local session
		 $_SESSION=array();
session_destroy();
//remove the client session
//setcookie("SES_NAME","","","/");
//redirect user with header
header("default.php");
//exit;
//***********************************************
//return;
	}

	$db_pass = $pass->fetchRow();

	// now we have encrypted pass from DB in
	//$db_pass['password'], stripslashes() just incase:

	$db_pass['password'] = stripslashes($db_pass['password']);
	$_SESSION['password'] = stripslashes($_SESSION['password']);
	//compare entered password w/ password in database...
	if($_SESSION['password'] == $db_pass['password']) {
		// valid password for username
		$logged_in = 1; // they have correct info
					// in session variables.
		// assign firstname and lastname to session variables...
		//$id = stripslashes($db_pass['id']);
		session_register('firstname');
		session_register('lastname');
		session_register('priv_level');
		$db_pass['firstname'] = stripslashes($db_pass['firstname']);
		$_SESSION['firstname'] = $db_pass['firstname'];
		$db_pass['lastname'] = stripslashes($db_pass['lastname']);
		$_SESSION['lastname'] = $db_pass['lastname'];
		$_SESSION['priv_level'] = $db_pass['priv_level'];

		if(getenv("HTTP_CLIENT_IP")) {
			$ip = getenv("HTTP_CLIENT_IP");
		} elseif(getenv("HTTP_X_FORWARDED_FOR")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		} else {
			$ip = getenv("REMOTE_ADDR");
		}
		$url=$HTTP_HOST.$PHP_SELF;
		$ref_url=$HTTP_REFERER;
		// now need to get a global timestamp for this page...
		$time_query = $db_object->query("SELECT NOW() as time");
		if(DB::isError($time_query)){
			die($time_query->getMessage());
		}
		$db_time = $time_query->fetchRow();
		$sess = session_id();
		$global_page_time = $db_time['time'];
		session_register('time');
		$_SESSION['time'] = $global_page_time;
		$userid = $db_pass['id'];




		$update_login = $db_object->query("UPDATE users SET last_login = NOW() WHERE username = '".$_POST['uname']."'");
		if (DB::isError($update_login)) {
		die($update_login->getMessage());
		}


	} else {
		$logged_in = 0;
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		unset($_SESSION['priv_level']);
		// kill incorrect session variables.
	}
}


// clean up
unset($db_pass['password']);

$_SESSION['username'] = stripslashes($_SESSION['username']);

?>
