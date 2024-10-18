<?php

// This script checks to see what form events have taken place....
// Currently checks for logout and submit (login)...


if(isset($_POST['logout'])) { // if user has chosen to logout...
if ($logged_in == 0) {
	die('You are not logged in so you cannot log out.');
	return;
}

//kill session variables...
unset($_SESSION['username']);
unset($_SESSION['password']);
unset($_SESSION['firstname']);
unset($_SESSION['lastname']);
unset($_SESSION['priv_level']);


unset($_GET[session_name()]);



$_SESSION = array();  // reset session array
session_destroy(); 	// destroy session.
//remove the client session

$logged_in = 0;
return;
//***************************
//exit;
//****************************
//header('Location: default.php');  // a redirection....
}

elseif(isset($_POST['submit'])) { // if form has been submitted...

	/* check to see if they filled in what they were supposed to and authenticate...*/
	if(!$_POST['uname'] | !$_POST['passwd']) {
		die('You did not fill in a required field.');
		//header('Location: default.php');
	}
       // authenticate.

	if (!get_magic_quotes_gpc()) {
		$_POST['uname'] = addslashes($_POST['uname']);
	}
	// get the username::password combo....
	$check = $db_object->query("SELECT username, password, firstname, lastname, priv_level FROM users WHERE username = '".$_POST['uname']."'");

	if (DB::isError($check)) {
		die('That username does not exist in our database.');
	}

	$info = $check->fetchRow();

	// check passwords match

	$_POST['passwd'] = stripslashes($_POST['passwd']);
	$info['password'] = stripslashes($info['password']);
	$_POST['passwd'] = md5($_POST['passwd']);
	$_POST['priv_level'] = $info['priv_level'];

	if ($_POST['passwd'] != $info['password']) {

		die('Incorrect password, please try again. ');
	}

	// Assign firstname and lastname to personalize site....
	$_SESSION['firstname'] = $info['firstname'];
	$_SESSION['lastname']= $info['lastname'];

	// Assign privilege level...
	$_SESSION['priv_level'] = $info['priv_level'];

	// if we get here username and password are correct,
	//register session variables and set last login time.

	//$date = date('m d, Y');

	$update_login = $db_object->query("UPDATE users SET last_login = NOW() WHERE username = '".$_POST['uname']."'");
	$logged_in = 1;
	$_POST['uname'] = stripslashes($_POST['uname']);
	$_SESSION['username'] = $_POST['uname'];
	$_SESSION['password'] = $_POST['passwd'];
$logged_in = 1;
	//$db_object->disconnect();
}
?>
