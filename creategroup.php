<?php

require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
require './phpinc/edge3-login-form-check.inc';
if ($logged_in == 0) {
	//die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
	//echo "You are not logged!";
}

require './phpinc/edge3_db_connect.inc';
require("fileupload-class.php");
include 'edge_update_user_activity.inc';
include 'utilityfunctions.inc';
$userid = $_SESSION['userid'];
if (isset($_POST['submit'])) {

echo "form submitted....<br>";
analyze($_POST);
  // we need to check to see if this group is already present...

  $groupcheck = "SELECT COUNT(*) FROM usergroups WHERE usergroupname LIKE \"$groupname\"";
  echo "$groupcheck<br>";
  $checkresult = mysql_query($groupcheck, $db);
  $row = mysql_fetch_row($checkresult);
  if($row[0] > 0){
	echo "the group name already exists, please choose another....";

  }else{
	$newgroupSQL = "INSERT usergroups(usergroupname) VALUES (\"$groupname\")";
	echo "$newgroupSQL<br>";
	$newgroupresult = mysql_query($newgroupSQL, $db);
	echo "new group inserted...";

}

			//$trxResult = mysql_query($insSQL, $db);

}else{
if($userid == ""){
	$userSQL = "SELECT id FROM users WHERE username LIKE \"".$_SESSION['username']."\"";
	$userresult = mysql_query($userSQL, $db);
	$useridrow = mysql_fetch_row($userresult);
	$userid = $useridrow[0];

}

//analyze($_SESSION);
$sql = "SELECT firstname, lastname FROM users WHERE id = $userid";
//echo "<br>$sql<br>";
$result = mysql_query($sql,$db);
$row = mysql_fetch_row($result);

$firstname = $row[0];
$lastname = $row[1];

?>


<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout" />
<title>EDGE^3</title>
</head>
<FORM action="<?php  $_SERVER['PHP_SELF'] ?>" method="POST" target="_self">
<?php
	echo "<input name=\"groupadmin\" type=\"hidden\" value=\"$userid\">\n";
?>
<table width="500" align="left">
  <tbody>
    <tr>
      <td colspan="2">Create a User Group</td>
    </tr>
    <tr>
      <td>Group Name:</td>
      <td><INPUT type="text" name="groupname" id="groupname"></td>
    </tr>
    <tr>
      <td>Group Administrator:</td>
      <td><?php echo "$firstname $lastname";?></td>
    </tr>
    <tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td><input type="reset" value="Reset Form"</td>
</tr>
  </tbody>
</table>  
</FORM>
</html>

<?php
}
?>
