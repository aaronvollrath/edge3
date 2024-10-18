
<?php 
// This includes a javascript validation file....
require('formvalidation.inc');
?>


<h3>Register</h3>


<?php
if (isset($_POST['submit'])) { // if form has been submitted
	/* check they filled in what they supposed to,
	passwords matched, username
	isn't already taken, etc. */
	// not needed... done in javascript....
	if (!$_POST['uname'] | !$_POST['passwd'] | !$_POST['passwd_again'] | !$_POST['email']) {
		die('You did not fill in a required field.');
	}

	// check if username exists in database.
	if (!get_magic_quotes_gpc()) {
		$_POST['uname'] = addslashes($_POST['uname']);
	}
	// check if username exists in database.
	if (!get_magic_quotes_gpc()) {
		$_POST['email'] = addslashes($_POST['email']);
	}


	//$name_check = $db_object->query("SELECT username FROM users WHERE username = '".$_POST['uname']."'");
	$result = $db->Execute("SELECT username FROM users WHERE username = '".$_POST['uname']."'");
/*	if (DB::isError($name_check)) {
		die($name_check->getMessage());
	}
*/	
	$name_checkk = $result->RecordCount();
	//$url=$HTTP_REFERER;
	if ($name_checkk != 0) {
		$str2 = $_POST['uname'];
		//<a href=$url>
		$str = "Sorry, the username <strong>$str2</strong> is already taken, please pick another one. <br>Click on the back button.";
		$_POST['uname'] = "";
		die($str);
	}

	// check passwords match

	if ($_POST['passwd'] != $_POST['passwd_again']) {
		$str = "Sorry.  The passwords you entered did not match.  <br>Please click on the back button.";
		die($str);
	}

	#analyze($_POST);



	// check e-mail format
	// don't need done in javascript....
	//if (!preg_match("/.*@.*..*/", $_POST['email']) | preg_match("/(<|>)/", $_POST['email'])) {
	//	die('Invalid e-mail address.');
	//}

	// no HTML tags in username, password, etc.

	$_POST['uname'] = strip_tags($_POST['uname']);
	$_POST['passwd'] = strip_tags($_POST['passwd']);
	$_POST['firstname'] = strip_tags($_POST['firstname']);
	$_POST['lastname'] = strip_tags($_POST['lastname']);
	$_POST['org'] = strip_tags($_POST['org']);
	$_POST['address1'] = strip_tags($_POST['address1']);
	$_POST['address2'] = strip_tags($_POST['address2']);
	$_POST['phone'] = strip_tags($_POST['phone']);

	// now we can add them to the database.
	// encrypt password

	$_POST['passwd'] = md5($_POST['passwd']);

	if (!get_magic_quotes_gpc()) {
		// uname already done above...
		$_POST['passwd'] = addslashes($_POST['passwd']);
		$_POST['email'] = addslashes($_POST['email']);
		$_POST['firstname'] = addslashes($_POST['firstname']);
		$_POST['lastname'] = addslashes($_POST['lastname']);
		$_POST['org'] = addslashes($_POST['org']);
		$_POST['address1'] = strip_tags($_POST['address1']);
		$_POST['address2'] = strip_tags($_POST['address2']);
		$_POST['phone'] = addslashes($_POST['phone']);
	}



	//$regdate = date('m d, Y');

	$insert = "INSERT INTO users (
			username,
			password,
			firstname,
			lastname,
			organization,
			phone,
			streetadd1,
			streetadd2,
			city,
			state,
			zipcode,
			country,
			priv_level,
			regdate,
			email)
			VALUES (
			'".$_POST['uname']."',
			'".$_POST['passwd']."',
			'".$_POST['firstname']."',
			'".$_POST['lastname']."',
			'".$_POST['org']."',
			'".$_POST['phone']."',
			'".$_POST['address1']."',
			'".$_POST['address2']."',
			'".$_POST['city']."',
			'".$_POST['state']."',
			'".$_POST['zip']."',
			'".$_POST['country']."',
			'1',
			NOW(),
			'".$_POST['email']."')";

	$add_member = $db->Execute($insert);
	
	/*if (DB::isError($add_member)) {
		$str2 = $_POST['email'];
		$str = "Sorry, the email address <strong>$str2</strong> is already taken, please enter another one. <br>Please click on the back button";
		$_POST['email'] = "";
		die($str);
		//die($add_member->getMessage());
	}

	$db_object->disconnect();
	*/
?>

<h1>Registered</h1>

<p>Thank you, your information has been added to the database, you may now log in by clicking on the <b><i>"Welcome!"</i></b> tab.</p>
<?php
$Name = "EDGE admin"; //senders name
		$email = "aaron.vollrath@gmail.com"; //senders e-mail adress
		$serverhost = $_SERVER['HTTP_HOST'];
		$mail_body = "A new user,". $_POST['firstname']." ".$_POST['lastname'].", was added to the database."; //mail body
		$subject = "New EDGE User"; //subject
		$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields
		# some of the variables used here are in ./phpinc/globalvariables.inc.php
		#mail($adminrecipient, $subject, $mail_body, $header); //mail command :) 
		if(ini_get('SMTP') != ''){
			#$smtpval = ini_get('SMTP');
			#echo "SMTP = $smtpval";
			# Windows smtp setup
			if(!mail($adminrecipient, $subject, $mail_body, $header)){
				
				echo "Email notifications not configured correctly on this server.";
			}else{
				echo "Email notification sent to EDGE<sup>3</sup> Administrator.";
			}
		}
		if(ini_get('sendmail_path')!= ''){
			# Unix-based setup
			if(!mail($adminrecipient, $subject, $mail_body, $header)){
				echo "ERROR: Email notifications not configured correctly on this server.<br><br>";
			}else{
				echo "An email notification has been sent to the creator of this array.<br><br>";
			}
		}

} else {	// if form hasn't been submitted

?>
<form name="register" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onSubmit="return checkForm()">
<table  class="question">
<tr><td>Username<font color="red" size="+2"><b>*</b></font>:</td><td>
<input type="text" name="uname" maxlength="40">
</td></tr>
<tr><td>Password<font color="red" size="+2"><b>*</b></font>:</td><td>
<input type="password" name="passwd" maxlength="50">
</td></tr>
<tr><td>Confirm Password<font color="red" size="+2"><b>*</b></font>:</td><td>
<input type="password" name="passwd_again" maxlength="50">
</td></tr>
<tr><td>E-Mail<font color="red" size="+2"><b>*</b></font>:</td><td>
<input type="text" name="email" maxlength="100">
</td></tr>
<tr><td>First Name:<font color="red" size="+2"><b>*</b></font></td><td>
<input type="text" name="firstname" maxlength="64">
</td></tr>
<tr><td>Last Name:<font color="red" size="+2"><b>*</b></font></td><td>
<input type="text" name="lastname" maxlength="64">
</td></tr>
<tr><td>Organization:<font color="red" size="+2"><b>*</b></font></td><td>
<input type="text" name="org" maxlength="100">
</td></tr>
<tr><td>Address:<font color="red" size="+2"><b>*</b></font></td>
<td>
<input type="text" name="address1" maxlength="128"><br>
<input type="text" name="address2" maxlength="128">
</td></tr>
<tr><td>City:<font color="red" size="+2"><b>*</b></font></td><td>
<input type="text" name="city" maxlength="64">
</td></tr>
<tr><td>State:<font color="red" size="+2"><b>*</b></font></td><td>
<input type="text" name="state" maxlength="2">
</td></tr>
<tr><td>Zip Code:<font color="red" size="+2"><b>*</b></font></td><td>
<input type="text" name="zip" maxlength="16">
</td></tr>
<tr><td>Country<font color="red" size="+2"><b>*</b></font></td><td>
 <select name="country">
  . <option>Afghanistan</option>
    <option>Albania</option>
    <option>Algeria</option>
    <option>American Samoa</option>
    <option>Andorra</option>
    <option>Angola</option>
    <option>Anguilla</option>
    <option>Antarctica</option>
   <option>Antigua and Barbuda</option>
   <option>Argentina</option>
   <option>Armenia</option>
   <option>Aruba</option>
   <option>Australia</option>
   <option>Austria</option>
   <option>Azerbaijan</option>
   <option>Bahamas</option>
   <option>Bahrain</option>
   <option>Bangladesh</option>
   <option>Barbados</option>
   <option>Belarus</option>
   <option>Belgium</option>
   <option>Belize</option>
   <option>Benin</option>
   <option>Bermuda</option>
   <option>Bhutan</option>
   <option>Bolivia</option>
   <option>Bosnia and Herzegovina</option>
   <option>Botswana</option>
   <option>Bouvet Island</option>
   <option>Brazil</option>
   <option>British Indian Ocean Territory</option>
   <option>Brunei Darussalam</option>
   <option>Bulgaria</option>
   <option>Burkina Faso</option>
   <option>Burundi</option>
   <option>Cambodia</option>
   <option>Cameroon</option>
   <option>Canada</option>
   <option>Cape Verde</option>
   <option>Cayman Islands</option>
   <option>Central African Republic</option>
   <option>Chad</option>
   <option>Chile</option>
   <option>China</option>
   <option>Christmas Island</option>
   <option>Cocos Islands</option>
   <option>Colombia</option>
   <option>Comoros</option>
   <option>Congo</option>
   <option>Congo, Democratic Republic of the</option>
   <option>Cook Islands</option>
   <option>Costa Rica</option>
   <option>Cote d'Ivoire</option>
   <option>Croatia</option>
   <option>Cuba</option>
   <option>Cyprus</option>
   <option>Czech Republic</option>
   <option>Denmark</option>
   <option>Djibouti</option>
   <option>Dominica</option>
   <option>Dominican Republic</option>
   <option>Ecuador</option>
   <option>Egypt</option>
   <option>El Salvador</option>
   <option>Equatorial Guinea</option>
   <option>Eritrea</option>
   <option>Estonia</option>
   <option>Ethiopia</option>
   <option>Falkland Islands</option>
   <option>Faroe Islands</option>
   <option>Fiji</option>
   <option>Finland</option>
   <option>France</option>
   <option>French Guiana</option>
   <option>French Polynesia</option>
   <option>Gabon</option>
   <option>Gambia</option>
   <option>Georgia</option>
   <option>Germany</option>
   <option>Ghana</option>
   <option>Gibraltar</option>
   <option>Greece</option>
   <option>Greenland</option>
   <option>Grenada</option>
   <option>Guadeloupe</option>
   <option>Guam</option>
   <option>Guatemala</option>
   <option>Guinea</option>
   <option>Guinea-Bissau</option>
   <option>Guyana</option>
   <option>Haiti</option>
   <option>Heard Island and McDonald Islands</option>
   <option>Honduras</option>
   <option>Hong Kong</option>
   <option>Hungary</option>
   <option>Iceland</option>
   <option>India</option>
   <option>Indonesia</option>
  <option>Iran</option>
  <option>Iraq</option>
  <option>Ireland</option>
  <option>Israel</option>
  <option>Italy</option>
  <option>Jamaica</option>
  <option>Japan</option>
  <option>Jordan</option>
  <option>Kazakhstan</option>
  <option>Kenya</option>
  <option>Kiribati</option>
  <option>Kuwait</option>
  <option>Kyrgyzstan</option>
  <option>Laos</option>
  <option>Latvia</option>
  <option>Lebanon</option>
  <option>Lesotho</option>
  <option>Liberia</option>
  <option>Libya</option>
  <option>Liechtenstein</option>
  <option>Lithuania</option>
  <option>Luxembourg</option>
  <option>Macao</option>
 <option>Madagascar</option>
  <option>Malawi</option>
  <option>Malaysia</option>
  <option>Maldives</option>
  <option>Mali</option>
  <option>Malta</option>
  <option>Marshall Islands</option>
  <option>Martinique</option>
  <option>Mauritania</option>
  <option>Mauritius</option>
  <option>Mayotte</option>
  <option>Mexico</option>
  <option>Micronesia</option>
  <option>Moldova</option>
  <option>Monaco</option>
  <option>Mongolia</option>
  <option>Montenegro</option>
  <option>Montserrat</option>
  <option>Morocco</option>
  <option>Mozambique</option>
  <option>Myanmar</option>
  <option>Namibia</option>
  <option>Nauru</option>
  <option>Nepal</option>
  <option>Netherlands</option>
  <option>Netherlands Antilles</option>
  <option>New Caledonia</option>
  <option>New Zealand</option>
  <option>Nicaragua</option>
  <option>Niger</option>
  <option>Nigeria</option>
  <option>Norfolk Island</option>
  <option>North Korea</option>
  <option>Norway</option>
  <option>Oman</option>
  <option>Pakistan</option>
  <option>Palau</option>
  <option>Palestinian Territory</option>
  <option>Panama</option>
  <option>Papua New Guinea</option>
 <option>Paraguay</option>
 <option>Peru</option>
 <option>Philippines</option>
 <option>Pitcairn</option>
 <option>Poland</option>
  <option>Portugal</option>
  <option>Puerto Rico</option>
  <option>Qatar</option>
  <option>Romania</option>
  <option>Russian Federation</option>
  <option>Rwanda</option>
  <option>Saint Helena</option>
  <option>Saint Kitts and Nevis</option>
  <option>Saint Lucia</option>
  <option>Saint Pierre and Miquelon</option>
  <option>Saint Vincent and the Grenadines</option>
  <option>Samoa</option>
  <option>San Marino</option>
  <option>Sao Tome and Principe</option>
  <option>Saudi Arabia</option>
  <option>Senegal</option>
  <option>Serbia</option>
  <option>Seychelles</option>
  <option>Sierra Leone</option>
  <option>Singapore</option>
  <option>Slovakia</option>
  <option>Slovenia</option>
  <option>Solomon Islands</option>
  <option>Somalia</option>
  <option>South Africa</option>
  <option>South Georgia</option>
  <option>South Korea</option>
  <option>Spain</option>
  <option>Sri Lanka</option>
  <option>Sudan</option>
  <option>Suriname</option>
  <option>Svalbard and Jan Mayen</option>
  <option>Swaziland</option>
  <option>Sweden</option>
  <option>Switzerland</option>
  <option>Syrian Arab Republic</option>
  <option>Taiwan</option>
  <option>Tajikistan</option>
  <option>Tanzania</option>
  <option>Thailand</option>
  <option>The Former Yugoslav Republic of Macedonia</option>
  <option>Timor-Leste</option>
  <option>Togo</option>
  <option>Tokelau</option>
  <option>Tonga</option>
  <option>Trinidad and Tobago</option>
  <option>Tunisia</option>
  <option>Turkey</option>
  <option>Turkmenistan</option>
 <option>Tuvalu</option>
 <option>Uganda</option>
 <option>Ukraine</option>
  <option>United Arab Emirates</option>
  <option>United Kingdom</option>
  <option SELECTED>United States</option>
  <option>United States Minor Outlying Islands</option>
  <option>Uruguay</option>
  <option>Uzbekistan</option>
  <option>Vanuatu</option>
  <option>Vatican City</option>
  <option>Venezuela</option>
  <option>Vietnam</option>
  <option>Virgin Islands, British</option>
  <option>Virgin Islands, U.S.</option>
  <option>Wallis and Futuna</option>
  <option>Western Sahara</option>
  <option>Yemen</option>
  <option>Zambia</option>
 <option>Zimbabwe</option>
  </select>
</td></tr>
<tr><td>Phone:</td><td>
<input type="text" name="phone" maxlength="32">
</td></tr>
<tr><td colspan="2"><font color="red" size="+2"><b>*</b></font>=Required Field</td></tr>
<tr><td colspan="2" align="right">
<input type="submit" name="submit" value="Sign Up">
</td></tr>
</table>
</form>

<?php

}

?>


