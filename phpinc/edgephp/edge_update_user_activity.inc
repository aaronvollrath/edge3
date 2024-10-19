<?php

if(isset($_SESSION['userid'])){

	if(getenv("HTTP_CLIENT_IP")) {
		$ip = getenv("HTTP_CLIENT_IP");
	} elseif(getenv("HTTP_X_FORWARDED_FOR")) {
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	} else {
		$ip = getenv("REMOTE_ADDR");
	}

	$HTTP_HOST = $_SERVER['HTTP_HOST'];
	$PHP_SELF = $_SERVER['PHP_SELF'];
	//echo "testing .... ". $_SERVER['HTTP_REFERER'] . "<hr>";
	
	$url=$HTTP_HOST.$PHP_SELF;
		
	// now need to get a global timestamp for this page...
	$time_query = $db->Execute("SELECT NOW() as time");
	if ($time_query === false){
		die('unable to access mysql database to get time!');
	}
		
	$db_time = $time_query->FetchRow();
	
	$sess = session_id();
	$global_page_time = $db_time['time'];
	//session_register('time');
	$_SESSION['time'] = $global_page_time;
	$userid = $_SESSION['userid'];



	$url=$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	if(isset($_SESSION['time'])){
		$gpt = $_SESSION['time'];
	}
	if(isset($_SERVER['HTTP_REFERER'])){
		$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
		$ref_url=$HTTP_REFERER;
	}else{
		$ref_url = "NONE";
	}
	if(!isset($sql)){
		$sql = "NONE";
	}
	// update useractivity table....w/ sql.....
	$updateActivitySQL = "INSERT INTO useractivity (
				userid,
				sessionid,
				occurred,
				userip,
				url,
				refurl,
				sqltext)
				VALUES (
				?,
				?,
				?,
				?,
				?,
				?,
				?
				)";
			$insert = "INSERT INTO useractivity (
				userid,
				sessionid,
				occurred,
				userip,
				url,
				refurl,
				sqltext)
				VALUES (
				'$userid',
				'$sess',
				'$gpt',
				'$ip',
				'$url',
				'$ref_url',
				\"$sql\"
				)";
			$add_row = $db->Execute($updateActivitySQL, array($userid, $sess, $gpt, $ip, $url, $ref_url, $sql) );
			if ($add_row === false){
				echo "database error when updating activity.  please contact administrator.<br>";
				echo "here is the error message: ".$db->ErrorMsg();
				die();
			}

}
?>
