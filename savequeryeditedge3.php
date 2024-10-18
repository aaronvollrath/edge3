<?php
//require 'edge_db_connect.php';
session_start();
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...

#require './phpinc/edge3_db_connect.inc';
require "formcheck.inc";

include 'edge_update_user_activity.inc';
if (!isset($_SESSION['userid'])) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="./edge3.php">Click here to go to the login page</a>');
}
$userid = $_SESSION['userid'];

?>

<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout" />
<title>EDGE^3</title>
<script type="text/javascript" src="./dojo-release-1.0.0/dojo/dojo.js"
                djConfig="parseOnLoad: true <?php echo $dojodebugval; ?>"></script>

    <script type="text/javascript">
        dojo.require("dojo.parser");


        dojo.require("dijit.layout.LayoutContainer");
       dojo.require("dijit.TitlePane");

	</script>
</head>
<body>
<div class="header">

			<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
			<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression</font></h4>
	</div>
	<br>


 <h3 class="contenthead">Edit your saved clustering queries.</h3>
<div class="content">

<?php
if (isset($_POST['submit'])) { // if form has not been submitted
// Delete the necessary stuff from the database....
$queryid = $_POST['queryid'];
$sql = "DELETE FROM savedqueries WHERE query = $queryid";
$result = $db->Execute($sql);//mysql_query($sql, $db);

}
?>


<p class="styletext">



<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="3">Saved Clustering Queries</th>
</thead>
</tr>

<?php


	// What saved queries does this user have????
	$sql = "select query, queryname from savedqueries where queryname != \"\" and userid = $userid";
	$result = $db->Execute($sql);//mysql_query($sql, $db);
	//while($row = mysql_fetch_row($result)){
	while($row=$result->FetchRow()){
		echo "<tr>";
		?>

		<?php
			echo "<td class=\"questionanswer\"><button name=\"Edit\" value=\"$row[0]\" onclick=\"window.open('./updatequeryedge3.php?savedquery=$row[0]')\">Edit?</button></td>
			<td class=\"questionparameter\">$row[1]</td>
			<td class=\"questionanswer\">";
		?>
			<form name="query<?php echo $row[0]; ?>" method="post" onsubmit="return confirmDelete('<?php echo $row[1]; ?>');" action="<?php  $_SERVER['PHP_SELF'] ?>">
		<?php

		echo "\n<input name=\"queryid\" type=\"hidden\" value=\"$row[0]\">\n
		<input type=\"submit\" name=\"submit\" value=\"Delete?\">
			</form>
			</td>
		</tr>\n";
	}

?>


</table>
</p>

</div>

</body>
</html>
