

<?php
$user = $_SESSION['username'];
$first =  $_SESSION['firstname'];
$last = $_SESSION['lastname'];
if (isset($_POST['submit'])) { // if form has been submitted
$updatelist = "";
	// Probably inefficient, but make a call to get the potential "old" info....
	$user = $_SESSION['username'];
	

$query_info = "SELECT firstname, lastname, password, organization, phone, streetadd1, streetadd2, city, state, zipcode, country, email from users where username = '$user'";
$get_info = $db->Execute($query_info);
$get_infoRow = $get_info->FetchRow();

	$uname = $user;#strip_tags($_POST['uname']);
	$newpass1 = strip_tags($_POST['newpass1']);
	$newpass2 = strip_tags($_POST['newpass2']);
	$firstname = strip_tags($_POST['firstname']);
	$lastname = strip_tags($_POST['lastname']);
	//$_POST['email'] = strip_tags($_POST['email']);
	$org = strip_tags($_POST['org']);
	$address1 = strip_tags($_POST['address1']);
	$address2 = strip_tags($_POST['address2']);
	$city = strip_tags($_POST['city']);
	$state = strip_tags($_POST['state']);
	$zip = strip_tags($_POST['zip']);
	$country = strip_tags($_POST['country']);
	$phone = strip_tags($_POST['phone']);



// Need to see if there were changes made....
if($_POST['newpass1'] == ""){
	// No need to update....
	//echo "not updating password<br>";
	$pass = $get_infoRow['password'];
}
else{
	// need to update and encrypt.....
	$updatelist.= "Password updated. <br>";
	$newpassword = $_POST['newpass1'];
	$newpassword2 = $_POST['newpass2'];
	
	$pass = md5($_POST['newpass1']);
}
$strEmail = trim($_POST['email']);
if($strEmail == ""){
	$email = $get_infoRow['email'];
	//echo "email is blank";
}
else{
	$email = $_POST['email'];
	//echo "email not blank";
}




// At some point need to check to make sure that the email address entered is not already taken.....
//pseudocode if email entered= email in db then give an error.  this will require a call to db.

if($firstname == ""){
	$first = $get_infoRow['firstname'];
}
/*
else{
	$first = $_POST['firstname'];
}*/
if($lastname == ""){
	$last = $get_infoRow['lastname'];
}
/*
else{
	$last = $_POST['lastname'];
}*/
if($_POST['address1'] == ""){
	$address1 = $get_infoRow['streetadd1'];
}
/*
else{
	$address1 = $_POST['address1'];
}*/
if($_POST['address2'] == ""){
	$address2 = $get_infoRow['streetadd2'];
}
/*
else{
	$address2 = $_POST['address2'];
}*/
if($_POST['city'] == ""){
	$city = $get_infoRow['city'];
}
/*
else{
	$city = $_POST['city'];
}*/
if($_POST['state'] == ""){
	$state = $get_infoRow['state'];
}
/*
else{
	$state = $_POST['state'];
}*/
if($country == ""){
	$country = $get_infoRow['country'];
}

if($_POST['zip'] == ""){
	$zip = $get_infoRow['zipcode'];
}/*
else{
	$zip = $_POST['zip'];
}*/

if($_POST['org'] == ""){
	$org = $get_infoRow['organization'];
}/*
else{
	$org = $_POST['org'];
}*/
if($_POST['phone'] == ""){
	$phone = $get_infoRow['phone'];
}



$insert = "UPDATE users SET
			firstname = '$first',
			lastname = '$last',
			organization = '$org',
			phone = '$phone',
			streetadd1 = '$address1',
			streetadd2 = '$address2',
			city = '$city',
			state = '$state',
			zipcode = '$zip',
			country = '$country',
			email = '$email',
			password = '$pass'
			WHERE username = '$user'";

	//echo "here's query: $insert";
	$update_member = $db->Execute($insert);
?>

<h3>Account Updated</h3>
<div class="content">
<?php
	echo "$updatelist<br>";
?>
<p class="styletext">Thank you, your information has been updated</p>
</div>



<?php

} else {	// if form hasn't been submitted

$query_info = "SELECT organization, phone, streetadd1, streetadd2, city, state, zipcode, country, email from users where username = '$user'";
$get_info = $db->Execute($query_info);
$get_infoRow = $get_info->FetchRow();


$org = $get_infoRow['organization'];
$phone = $get_infoRow['phone'];
$address1 = $get_infoRow['streetadd1'];
$address2 = $get_infoRow['streetadd2'];
$city = $get_infoRow['city'];
$state = $get_infoRow['state'];
$zip = $get_infoRow['zipcode'];
$country = $get_infoRow['country'];
$email = $get_infoRow['email'];
?>
<?php
# onSubmit="return checkUpdateForm()"
?>
<h3>Update Your Account</h3>
<div>

<form enctype="multipart/form-data" name="useraccount" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
<table align="left" cellspacing="0" cellpadding="0">

<tr><td>Username:</td><td>
<font><font color="red"><b><?php echo "$user" ?></b></font></label>
</td></tr>
<tr><td colspan="2">If you'd like to change your password fill out the fields below:</td></tr>
<td>Current password:</td><td><input name="oldpass" type="password" maxlength="50"></td>
<tr><td>New password:</td><td><input name="newpass1" type="password" maxlength="50"></td></tr>
<tr><td>Confirm password:</td><td><input name="newpass2" type="password" maxlength="50"></td></tr>
<tr><td >E-Mail address: </td><td><label><font color="blue"><b><?php echo "$email"?></b></font> </label></td>
<tr><td>If you'd like to change your email, enter a new address: </td><td>
<input type="text" name="email" maxlength="100"></td>
</tr>
<tr><td colspan="2"><B><font size="5">Please update the values below as needed</font></font></td></tr>
<tr><td><strong><u>Current Values</u></strong></td><td><strong><u>New Values</u></strong></td>
</tr>
<tr><td>First Name: <label><font color="blue"><b><?php echo "$first" ?></b></font> </label></td><td>
<input type="text" name="firstname" maxlength="64" >
</td></tr>
<tr><td>Last Name: <label><font color="blue"><b><?php echo "$last" ?></b></font> </label></td><td>
<input type="text" name="lastname" maxlength="64">
</td></tr>
<tr><td>Organization: <label><font color="blue"><b><?php echo "$org" ?></b></font> </label></td><td>
<input type="text" name="org" maxlength="100">
</td></tr>
<tr><td>Current Street Address: <label><font color="blue"><b><?php echo "$address1<br>$address2" ?></b></font> </label></td>
<td>
<input type="text" name="address1" maxlength="128"><br>
<input type="text" name="address2" maxlength="128">
</td></tr>
<tr><td>City: <label><font color="blue"><b><?php echo "$city" ?></b></font> </label></td><td>
<input type="text" name="city" maxlength="64">
</td></tr>
<tr><td>State: <label><font color="blue"><b><?php echo "$state" ?></b></font> </label></td><td>
<input type="text" name="state" maxlength="2">
</td></tr>
<tr><td>Zip Code: <label><font color="blue"><b><?php echo "$zip" ?></b></font> </label></td><td>
<input type="text" name="zip" maxlength="16">
</td></tr>
<tr><td>Country: <label><font color="blue"><b><?php echo "$country" ?></b></font> </label></td><td>
<input type="text" name="country" maxlength="96">
</td></tr>
<tr><td>Phone: <label><font color="blue"><b><?php echo "$phone" ?></b></font> </label></td><td>
<input type="text" name="phone" maxlength="32">
</td></tr>
<tr><td colspan="2" align="right">
<input type="submit" name="submit" value="Update">
</td></tr>
</table>
</form>

</div>

<?php

}

?>
