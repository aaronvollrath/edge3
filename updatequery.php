<?php
require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';

// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}



?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout" />
<title>EDGE^2</title>
</head>
<body>
	<div class="header">
			<img src="./GIFs/EDGE264x30.png" alt="Edge^2" align="left"></img>
			<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
			<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression</font></h4>
	</div>
	<br>
<p>
<?php $querynum = $_POST['querynum'];
	$name = $_POST['name'];
	$action = $_POST['action'];
	$querynum = $_GET['savedquery'];
include "formcheck.inc";
	if($_POST['submit'] != ""){



		//echo "Query num: $querynum<br>";
		if($action == "save"){
			// What query are we "saving" (i.e., assigning a name)
			$sql = "UPDATE savedqueries SET queryname = \"$name\" WHERE query=$querynum";
			//echo $sql;
			$sqlResult = mysql_query($sql, $db);

			$name = "$name Updated";
	}
?>
		<table class="question" width="300">
				<thead>
				<tr>
				<th class="mainheader" colspan="2">Update Query</th>
				</tr>
				</thead>
				<tr class="question">
				<td class="questionparameter"><strong>Results:</strong></td>
				<td class="questionanswer">
					<?php echo $name; ?>
				</td>
				</tr>
			</table>
			
			<p class="close"><a href="javascript:window.opener.document.location.href='./savequeryedit.php'; window.close();"><img src="./GIFs/closebutton.gif" width="70" height="15" border="0"></a></p>
<?php
	}else{
			// What's the name of this query?????
			$sql = "SELECT queryname FROM savedqueries WHERE query = $querynum";
			//echo $sql;
			$sqlResult = mysql_query($sql, $db);
			$row = mysql_fetch_row($sqlResult);
			$queryname = $row[0];


			//echo "You've chosen to update query# $savedquery w/ temp query# $tempquery<br>";
		?>
			<form name="order" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>")">
				<table class="question">
					<thead>
					<tr>
					<th class="mainheader" colspan="2">Update Query</th>
					</tr>
					</thead>
					<tr class="question">
					<td class="questionparameter"><strong>Query Name:</strong></td>
					<td class="questionanswer">
						<input name="name" type="text" value="<?php echo "$queryname"; ?>" size="25" align="right"></input>
					</td>
					<?php
						echo "<input name=\"querynum\" type=\"hidden\" value=\"$savedquery\">\n";
						echo "<input name=\"action\" type=\"hidden\" value=\"save\">\n";
					?>
					</tr>
					<tr>
					<td><input type="submit" align="right" name="submit" value="Submit"></td>
					<td><input type="reset" align="right" value="Reset Form"</td>
					</tr>
				</table>
			</form>
		<?php
	}
?>
</p>

</body>
</html>
