<?php

include "edge3_db_connect.inc";
include "utilityfunctions.inc";

analyse($_POST);
$got = $_POST['got'];
$userid = $_POST['userid'];
$name = $_POST['name'];

if($got != true){
/*
?>

<form enctype="multipart/form-data" name="getgenelist" action="./getgenelist.php" method="post">
<input type="hidden" name="submitted" value="true">
<input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>">
<table>
<TR>
<td><strong>Enter Gene List name</strong></td>
<TD>
<input type="text" name="name">
</TD>
</TR>
<tr>
<td><input type="submit" value="Get Gene List"></td>
<td><input type="reset" value="Reset Form"></td>
</tr>
</table>
</form>

<?php
*/
}
else{
	$sql = "SELECT featurenums FROM genelist WHERE name = '$name' && userid = '$userid'";
	$result = mysql_query($sql,$db);
	$featurenums = mysql_result($result, 0);
	echo $featurenums;
}
?>