<?php

require 'edge_db_connect2.php';
$db = mysql_connect("localhost", "root", "arod678cbc3");
mysql_select_db("edge", $db);;
require './phpinc/edge3-login-form-check.inc';
require 'globalfilelocations.inc';


if($logged_in == 0  && $userid == 0){
	echo "Please log in!";

}else{
	echo "you're logged in!<BR>";
	$sql = "SHOW TABLES FROM edge";
	$result = mysql_query($sql,$db);
	if (!$result) {
	echo "DB Error, could not list tables\n";
	echo 'MySQL Error: ' . mysql_error();
	exit;
	}
	while ($row = mysql_fetch_row($result)) {
	$val = $row[0];
	$tablelist .= "<option value=\"$val\" selected>$val</option>\r";
	//echo "Table: {$row[0]}\n";
	}	
	mysql_free_result($result);
?>

<form name="tableselect" method="post" action="drastictoolstest.php">
<select name='table'><?php echo $tablelist; ?></select>
<input type="submit" name="submit" value="Submit">
</form>

<?php
}


?>