<?php

include "edge3_db_connect.inc";
/*

 This script is utilized to create usergroups.  

A usergroup is a set of users administered by a usergroupadmin.  the usergroupadmin has the ability to grant to other users, as well as non-registered users, the ability to view and analyze their (i.e., usergroupadmin's) data.  A usergroupadmin must "own" their own data before they can create a usergroup.  A usergroupadmin can also grant ownership privileges to another user by making additional users coadministrators.  To assign users, the usergroupadmin must first create the group and then know the usernames of any users they want to add.  they can search for other users by their names.

*/

// What experiments does this user have control of?
$userid = $_SESSION['userid'];
$userid = 1;
if($userid == ""){
	echo "You must login to create usergroups!";
	//exit(0);
}
$expsql = "SELECT expid,expname,usergroupid FROM agilent_experimentsdesc WHERE ownerid = $userid";
echo "$expsql";
$expresult = mysql_query($expsql, $db);
$expidarray = array();
$expnamearray = array();
// if the user doesn't control any experiments, they cannot create any groups....
$num_rows = mysql_num_rows($expresult);
if($num_rows == 0){
	echo "you do not control any experiments, so you cannot create any groups.";
	//exit(0);

}else{
	while($row = mysql_fetch_row($expresult)){
		array_push($expidarray, $row[0]);
		array_push($expnamearray, $row[1]);
	}

}

//else, they can

?>
<form enctype="multipart/form-data" name="usergroupcreation" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>">
<table>

<tr>
<td>User Group Name</td>
<td>
<input name="name" type="text">
</td>
</tr>

<tr>
<td>User Group Description</td>
<td>
<textarea cols="40" rows="5" name="desc">

</textarea>
</td>
</tr>

<tr>
<td><input type="submit" name="import" value="Import"></td>
<td><input type="reset" value="Reset Form"></td>
</tr>

</table>
</form>

