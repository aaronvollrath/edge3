<?php
	/*****
		Script: emailpassusername.inc.php
		Purpose: This script will return a new password or the username of a user if they've forgotten theirs.


	*****/
#analyze($_GET);
// #analyze($_POST);
function generatePassword ($length = 8)
{

  // start with a blank password
  $password = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
    
  // set up a counter
  $i = 0; 
    
  // add random characters to $password until $length is reached
  while ($i < $length) { 

    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        
    // we don't want this character if it's already in the password
    if (!strstr($password, $char)) { 
      $password .= $char;
      $i++;
    }

  }

  // done!
  return $password;

}

	// the info to return is passed in the get value
	// there are three possible things the enduser is looking for:
	# 1: password reset (they forgot it) - $_GET['pass'] = 1
	# 2: username request (they forgot it, but know the email they registered with) - $_GET['username'] = 1
	# 3: username & password request (they forgot everything, but they know the email they registered with) - $_GET['userandpass'] = 1
	if(!isset($_POST['emailpassuser'])){
		if(isset($_GET['pass'])){
			# display password only form (just ask for a username)
?>
			<h4>Please enter your username and a new password will be sent to the email address associated with your username.</h4>
			<form enctype="multipart/form-data" name="userpass" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
			<input type="hidden" name="emailpassuser" value="true">
			<input type="hidden" name="password" value="true">
			<table>
				<TR>
					<TD>Username:</TD>
					<td><input name='username' type="text" ></input></td>
				</TR>
				<tr>
					<td><input type="submit" name="submit" value="Submit"></td>
					<td><input type="reset" value="Reset Form"></td>
				</tr>
			</table>

<?php
		}elseif(isset($_GET['username'])){
			?>
			<h4>Please enter the email you registered with and your username will be sent to that email address.</h4>
			<form enctype="multipart/form-data" name="userpass" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
			<input type="hidden" name="user" value="true">
						<input type="hidden" name="emailpassuser" value="true">

			<table>
				<TR>
					<TD>Email:</TD>
					<td><input name='email' type="text" ></input></td>
				</TR>
				<tr>
					<td><input type="submit" name="submit" value="Submit"></td>
					<td><input type="reset" value="Reset Form"></td>
				</tr>
			</table>

<?php
		}elseif(isset($_GET['userandpass'])){
			# display username and password form (ask for email)
			?>
			<h4>Please enter the email you registered with and your username and new password will be sent to that email address.</h4>
			<form enctype="multipart/form-data" name="userpass" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
			<input type="hidden" name="userandpass" value="true">
						<input type="hidden" name="emailpassuser" value="true">

			<table>
				<TR>
					<TD>Email</TD>
					<td><input name='email' type="text" ></input></td>
				</TR>
				<tr>
					<td><input type="submit" name="submit" value="Submit"></td>
					<td><input type="reset" value="Reset Form"></td>
				</tr>
			</table>

<?php
		}else{
			echo "You have reached this page on error.  If you feel this is incorrect, please contact the EDGE<sup>3</sup> administrator<br>";
		}
	}else{
		// validate the form values entered
		//echo "there was a post....<BR>";
		# for each respective form, check to make sure a valid value was entered....
		if(isset($_POST['emailpassuser'])){
			//what form are we using?
			if(isset($_POST['password'])){
				# check to see if the username entered is in the database
				if(isset($_POST['username'])){
					$thisusername = $_POST['username'];
					if($thisusername != ""){
						$sql = "SELECT username,firstname,lastname,email,id FROM users WHERE username = \"$thisusername\"";
						$result = $db->Execute($sql);
						if($result->RecordCount() == 1){
							$row = $result->FetchRow();
							#analyze($row);
							$thisuser = $row[1]." ".$row[2];
							$name = "EDGE3 admin"; //senders name
							$email = $edge3adminemail; //senders e-mail adress
							$recipient = $row[3]; //recipient"aaron.vollrath@gmail.com";#$row[3]; //recipient
							$serverhost = $_SERVER['HTTP_HOST'];
							# need to generate a new password....
							$newpassword = generatePassword();
							$mail_body = "Your new password is: $newpassword\n\nFeel free to change it to one more easily remembered."; //mail body
							$subject = "EDGE password reset"; //subject
							$header = "From: ". $name . " <" . $email . ">\r\n"; //optional headerfields
							mail($recipient, $subject, $mail_body, $header); //mail command :) 
							echo "An email containing your new password has been sent to the email account you registered with.<br><br>";
							echo "<a href='./edge3.php'>Login</a>";
							$id = $row[4];
							# now we need to update the users table w/ the new password....
							$newpassword = md5($newpassword);
							$sql = "UPDATE users SET `password`=\"$newpassword\" WHERE id = $id";
							$result = $db->Execute($sql);
							#echo "$sql<br>";
						}else{
							echo "ERROR: You did not enter a valid EDGE<sup>3</sup> username.<br><br>";
							echo "<a href='./edge3.php'>Login</a>";
						}
					}else{
						echo "ERROR: You did not enter a username!  Click on the <em>Login</em> link below.<br><br>";
						echo "<a href='./edge3.php'>Login</a>";
					}
				}else{
					echo "ERROR: You did not enter a username!   Click on the <em>Login</em> link below.<br><br>";
						echo "<a href='./edge3.php'>Login</a>";
				}
				
			}elseif(isset($_POST['user'])){
					# check to see if the email entered is in the database
				if(isset($_POST['email'])){
					$thisemail = $_POST['email'];
					if($thisemail != ""){
						$sql = "SELECT username,firstname,lastname,email FROM users WHERE email = \"$thisemail\"";
						$result = $db->Execute($sql);
						if($result->RecordCount() == 1){
							$row = $result->FetchRow();
							#analyze($row); 
							$thisuser = $row[1]." ".$row[2];
							$name = "EDGE3 admin"; //senders name
							$email = $edge3adminemail; //senders e-mail adress
							$recipient = $row[3]; //recipient"aaron.vollrath@gmail.com";#$
							$serverhost = $_SERVER['HTTP_HOST'];
							$mail_body = "Here is your username for EDGE3: $row[0]"; //mail body
							$subject = "EDGE User Name"; //subject
							$header = "From: ". $name . " <" . $email . ">\r\n"; //optional headerfields
							mail($recipient, $subject, $mail_body, $header); //mail command :) 
							echo "An email containing your username has been sent to the email account you registered with.<br><br>";
							echo "<a href='./edge3.php'>Login</a>";
						}else{
							echo "ERROR: You did not enter an email address that is attributable to an EDGE<sup>3</sup> user.<br><br>";
							echo "<a href='./edge3.php'>Login</a>";
						}
					}else{
						echo "ERROR: You did not enter an email address!  Click on the <em>Login</em> link below.<br><br>";
						echo "<a href='./edge3.php'>Login</a>";
					}
				}else{
					echo "ERROR: You did not enter an email address!    Click on the <em>Login</em> link below.<br><br>";
						echo "<a href='./edge3.php'>Login</a>";
				}
			}elseif(isset($_POST['userandpass'])){
					# check to see if the email entered is in the database
				if(isset($_POST['email'])){
					$thisemail = $_POST['email'];
					if($thisemail != ""){
						$sql = "SELECT username,firstname,lastname,email,id FROM users WHERE email = \"$thisemail\"";
						$result = $db->Execute($sql);
						if($result->RecordCount() == 1){
							$row = $result->FetchRow();
							#analyze($row);
							$thisuser = $row[1]." ".$row[2];
							$name = "EDGE3 admin"; //senders name
							$email = $edge3adminemail; //senders e-mail adress
							$recipient = $row[3]; //recipient
							$serverhost = $_SERVER['HTTP_HOST'];
							$mail_body = "Here is your username for EDGE3: $row[0]\n"; //mail body
							$newpassword = generatePassword();
							$mail_body .= "Your new password is: $newpassword\n\nFeel free to change it to one more easily remembered."; //mail body"; //mail body
							$subject = "EDGE password reset"; //subject
							$header = "From: ". $name . " <" . $email . ">\r\n"; //optional headerfields
							mail($recipient, $subject, $mail_body, $header); //mail command :) 
						
							echo "An email containing your username and new password has been sent to the email account you registered with.<br><br>";
							echo "<a href='./edge3.php'>Login</a>";
							$id = $row[4];
							# now we need to update the users table w/ the new password....
							$newpassword = md5($newpassword);
							$sql = "UPDATE users SET `password`=\"$newpassword\" WHERE id = $id";
							$result = $db->Execute($sql);
							#echo "$sql<br>";
						}else{
							echo "ERROR: You did not enter an email address that is attributable to an EDGE<sup>3</sup> user.<br><br>";
							echo "<a href='./edge3.php'>Login</a>";
						}
					}else{
						echo "ERROR: You did not enter an email address!  Click on the <em>Login</em> link below.<br><br>";
						echo "<a href='./edge3.php'>Login</a>";
					}
				}else{
					echo "ERROR: You did not enter an email address!    Click on the <em>Login</em> link below.<br><br>";
						echo "<a href='./edge3.php'>Login</a>";
				}
			}else{
				echo "You have reached this page on error.  If you feel this is incorrect, please contact the EDGE<sup>3</sup> administrator<br>";
			}
				
		}else{
			echo "You have reached this page on error.  If you feel this is incorrect, please contact the EDGE<sup>3</sup> administrator<br>";
		}
	}


?>