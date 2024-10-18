<?php

//echo "<hr>in edge3-login-form-check.inc<hr>";

header("Cache-control: private");
// This script checks to see what form events have taken place....
// Currently checks for logout and submit (login)...
//analyze($_POST);
//analyze($_SESSION);
if(isset($_POST['logout'])) { // if user has chosen to logout...

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

elseif(isset($_POST['Login'])) {  // if form has been submitted...

	/* check to see if they filled in what they were supposed to and authenticate...*/
	if(!$_POST['uname'] | !$_POST['passwd']) {
		die('You did not fill in a required field.');
		//header('Location: default.php');
	}
       // authenticate.

	$checkUserNameSQL = 'SELECT id, password, firstname, lastname, priv_level FROM users WHERE username = ?';
	$check = $db->Execute($checkUserNameSQL, array($_POST['uname']));
	if ($check === false){
		echo "database error when checking user name.  please contact administrator.<br>";
		echo "here is the error message: ".$db->ErrorMsg();
		die();
	}
	$info = $check->FetchRow();
	//die('after sql call: ' .$checkUserNameSQL.$_POST['uname']);
	if($check->RecordCount() == 0){
		die('You entered an invalid user name.  Please press the back button and try again.');
	}
	// check passwords match
	//echo "<hr>here is the password in the database: ". $info['password']. "<hr>";
	$_POST['passwd'] = stripslashes($_POST['passwd']);
	$info['password'] = stripslashes($info['password']);
	$_POST['passwd'] = md5($_POST['passwd']);
	if(isset($_POST['priv_level'])){
		$_POST['priv_level'] = $info['priv_level'];
	}
	

	if ($_POST['passwd'] != $info['password']) {

		die('Incorrect password entered. Please press the back button and try again. ');
	}

	// Assign firstname and lastname to personalize site....
	$_SESSION['firstname'] = $info['firstname'];
	$_SESSION['lastname']= $info['lastname'];

	// Assign privilege level...
	$_SESSION['priv_level'] = $info['priv_level'];
	$_SESSION['userid'] = $info['id'];

	//$checkUserNameSQL = 'SELECT id, password, firstname, lastname, priv_level FROM users WHERE username = ?';
	//$check = $db->Execute($checkUserNameSQL, array($_POST['uname']));

	$update_loginSQL = "UPDATE users SET last_login = NOW() WHERE username = ?";
	$update_login = $db->Execute($update_loginSQL, array($_POST['uname']));
	if ($update_login === false){
		echo "database error when updating login status.  please contact administrator.<br>";
		echo "here is the error message: ".$db->ErrorMsg();
		die();
	}

	$logged_in = 1;
	$_POST['uname'] = stripslashes($_POST['uname']);
	$_SESSION['username'] = $_POST['uname'];
	$_SESSION['password'] = $_POST['passwd'];
	//echo "AT end of login-form-check....<br>";
	$_SESSION['logged_in'] = 1;

}else{
	// if the session variables are not set,then it means the user's session has timed out.
	// redirect to the login page.
	//$url = ".\login.php";
	//if(!isset($_SESSION['userid'])){
		//header('Location: '.$url);
    	//exit();
	//}
}
?>
