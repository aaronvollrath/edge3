<?php
session_start();
//require 'edge_db_connect.php';
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if (!isset($_SESSION['userid'])) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="./edge3.php">Click here to go to the login page</a>');
}
#require './phpinc/edge3_db_connect.inc';
//require "formcheck.inc";

include 'edge_update_user_activity.inc';
$cssclass = "tundra";
$organismid = "";
/*
if($userid == 1){
	$dojodebugval = ", isDebug: true";

}else{
	$dojodebugval = "";
}
*/
$dojodebugval = "";
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
	dojo.require("dijit.layout.ContentPane");
       dojo.require("dijit.TitlePane");
function confirmDelete(listname)
{
 var doDelete= confirm("Do you really want to delete this gene list, " + listname + "?");
 if (doDelete== true)
 {
   return true;
 }
 else
 {
  return false;
  }
}

	</script>
  <style type="text/css">
                @import "./dojo-release-1.0.0/dojo/resources/dojo.css";
                @import "./dojo-release-1.0.0/dijit/themes/<?php echo $cssclass; ?>/<?php echo $cssclass; ?>.css";
                @import "./dojo-release-1.0.0/dijit/demos/mail/mail.css";
		/* pre-loader specific stuff to prevent unsightly flash of unstyled content */
		#loader {
			padding:0;
			margin:0;
			position:absolute;
			top:0; left:0;
			width:100%; height:100%;
			background:#ededed;
			z-index:999;
			vertical-align:center;
		}
		#loaderInner {
			padding:5px;
			position:relative;
			left:0;
			top:0;
			width:400px;
			height:800px;
			background:#3c3;
			color:#fff;

		}

        </style>
</head>
<body  class="<?php echo $cssclass; ?>">
<div id="mainContainer" dojoType="dijit.layout.ContentPane"  style="float: left; width: 63%; height: 100%;">
<div class="header">

			<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
			<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression</font></h4>
</div>
	<br>


<h3>Edit your saved gene lists.</h3>

<?php
/************************************************************************

Michael, here is where you'll want to modify the sql query below to delete
from the genelist table based on the posted gene list id.
*************************************************************************/



if (isset($_POST['submit'])) { // if form has been submitted
// Delete the necessary stuff from the database....
$listid = $_POST['listid'];
$sql = "DELETE FROM genelist WHERE listid = $listid";
$result = $db->Execute($sql);//mysql_query($sql, $db);

}
?>

<div dojoType='dijit.TitlePane' title='Your gene lists' open='true'>
<table class="results" width="600">



<?php


	// What saved gene lists does this user have????
	$userid = $_SESSION['userid'];
	$sql = "SELECT listid, name, listdesc, arraytype FROM genelist WHERE userid = '$userid' && public = '0' ORDER BY arraytype ASC";
	$result = $db->Execute($sql);//mysql_query($sql, $db);
	
	#while($row = mysql_fetch_row($result)){
	while($row = $result->FetchRow()){
		echo "<tr>";


			$listid = $row[0];
			$name = $row[1];
			$listdesc = $row[2];
			$arraytype = $row[3];
			
			if($organismid != $arraytype){
				$orgSQL = "SELECT organism FROM agilentarrays WHERE id='$arraytype'";
				$orgResult = $db->Execute($orgSQL);//mysql_query($orgSQL,$db);
				while($orgRow = $orgResult->FetchRow()){
					$organism = $orgRow[0];?>					
					<thead>
					<tr>
					<th colspan="4"><?php echo $organism; ?></th></tr></thead>
					<tr><TD></TD><td><strong><i>List Name</i></strong></td><td><strong><i>List Description</i></strong></td><td></td></tr>
 <?php
				}
				$organismid = $arraytype;
			}
			//echo "<a href=\"./edge3.php?selectedclonesclusteringmodule=1&listid=$listid\">$name".": "."$listdesc</a><br>";
			?>
		<td class="questionanswer"><button name="Edit" value="<?php echo $row[0]; ?>" onclick="window.open('./updategenelist.php?listid=<?php echo $listid; ?>')">Edit List</button></td>
			<td class="questionparameter"><?php echo $name; ?></td>
			
			<td class="questionparameter"><?php echo $listdesc; ?></td>
			<td class="questionanswer">
		
			<form name="query<?php echo $listid; ?>" method="post" onsubmit="return confirmDelete('<?php echo $name; ?>');" action="<?php  $_SERVER['PHP_SELF'] ?>">

		<input name="listid" type="hidden" value="<?php echo $row[0]; ?>">
		<input type="submit" name="submit" value="Delete?">
			</form>
			</td>
		</tr>
	<?php
	}
	?>
</table>
</div>
<!--
<div dojoType='dijit.TitlePane' title='Public gene lists you created' open='false'>

<table class="question" width="600">

<?php

/*
	// What saved gene lists does this user have????
	$sql = "SELECT listid, name,listdesc,arraytype FROM genelist WHERE public = '1' AND userid = '$userid' ORDER BY arraytype ASC";
	$result = $db->Execute($sql);//mysql_query($sql, $db);
	while($row = $result->FetchRow()){
		echo "<tr>";
		$listid = $row[0];
		$name = $row[1];
		$listdesc = $row[2];
		$arraytype = $row[3];
		
			if($organismid != $arraytype){
				$orgSQL = "SELECT organism FROM agilentarrays WHERE id='$arraytype'";
				$orgResult = $db->Execute($orgSQL);//mysql_query($orgSQL,$db);
				#while($orgRow = mysql_fetch_row($orgResult)){
				while($orgRow = $orgResult->FetchRow()){
					$organism = $orgRow[0];
?>					
					<thead>
					<tr>
					<th colspan="4"><?php echo $organism; ?></th></tr></thead>
					<tr><TD></TD><td><strong><i>List Name</i></strong></td><td><strong><i>List Description</i></strong></td><td></td></tr>
<?php
				}
				$organismid = $arraytype;
			}
			
?>
		<td class="questionanswer"><button name="Edit" value="<?php echo $row[0]; ?>" onclick="window.open('./updategenelist.php?listid=<?php echo $listid; ?>')">Edit List</button></td>
			<td class="questionparameter"><?php echo $name; ?></td>
			
			<td class="questionparameter"><?php echo $listdesc; ?></td>
			<td class="questionanswer">
		
			<form name="query<?php echo $listid; ?>" method="post" onsubmit="return confirmDelete('<?php echo $name; ?>');" action="<?php  $_SERVER['PHP_SELF'] ?>">

		<input name="listid" type="hidden" value="<?php echo $row[0]; ?>">
		<input type="submit" name="submit" value="Delete?">
			</form>
			</td>
		</tr>
	<?php
	}
	
*/
	?>


</table>
</div>
-->
</div>

</body>
</html>
