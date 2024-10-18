<?php
require('utilityfunctions.inc');
require 'edge_db_connect2.php';
//require 'edge3_db_connect.inc';

// Need to check if the user is logged in because this is a restricted area...
if (!$_SESSION['username']) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}



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
<p>
<?php 


	if(isset($_POST['submit'])){
		if(isset($_POST['querynum'])){
			$querynum = $_POST['querynum'];
		}else{
			$querynum = "";
		}
		if(isset($_POST['desc'])){
			$desc = $_POST['desc'];
		}else{
			$desc = "";
		}	
		if(isset($_POST['name'])){
			$name = $_POST['name'];
		}else{
			$name = "";
		}
		if(isset($_POST['public'])){
			$public = $_POST['public'];
		}else{
			$public = "0";
		}
		if(isset($_POST['action'])){
			$action = $_POST['action'];
		}else{
			$action = "";
		}
		
		
	}
	if(isset($_GET['savedquery'])){
		$querynum = $_GET['savedquery'];
	}else{
		die("Invalid GET parameter.  Exiting....");
	}
include "formcheck.inc";
	if(isset($_POST['submit'])){



		//echo "Query num: $querynum<br>";
		if($action == "save"){
			// What query are we "saving" (i.e., assigning a name)
			$sql = "UPDATE `edge`.`savedqueries` SET `queryname` = '$name',
`querydesc` = '$desc',
`public` = '$public' WHERE `savedqueries`.`query` =$querynum";
			//echo $sql;
			$sqlResult = $db->Execute($sql); //mysql_query($sql, $db);

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
			
			<p class="close"><a href="javascript:window.opener.document.location.href='./savequeryeditedge3.php'; window.close();"><img src="./GIFs/closebutton.gif" width="70" height="15" border="0"></a></p>
<?php
	}else{
			// What's the name of this query?????
			$sql = "SELECT queryname, querydesc, public FROM savedqueries WHERE query = $querynum";
			//echo $sql;
			$sqlResult = $db->Execute($sql);//mysql_query($sql, $db);
			$row = $sqlResult->FetchRow();//mysql_fetch_row($sqlResult);
			$queryname = $row[0];
			$querydesc = $row[1];
			$public = $row[2];
			$checked = "";
			if($public == 1){
				$checked = "checked";
			}

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
					<td class="questionparameter2"><strong>Query Name:</strong></td>
					<td class="questionparameter2">
						<input name="name" type="text" value="<?php echo "$queryname"; ?>" size="100" align="right"></input>
					</td>
					<?php
						echo "<input name=\"querynum\" type=\"hidden\" value=\"$querynum\">\n";
						echo "<input name=\"action\" type=\"hidden\" value=\"save\">\n";
					?>
					</tr>
					<tr>
					<td class="questionparameter2"><strong>Query Description</strong></td>
					<td class="questionparameter2">
					<TEXTAREA NAME="desc" COLS=80 ROWS=6 ><?php echo $querydesc; ?></TEXTAREA>
					</td>
					</tr>
					<!--
					<tr>
					<td class="questionparameter2"><strong>Public</strong></td>
					<td class="questionparameter2">
					<input name="public" type="checkbox" value="1" <?php #echo $checked; ?>>
					</td>
					</tr>
					-->
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
