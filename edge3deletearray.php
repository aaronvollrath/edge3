<?php

require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';


	$arrayid = $_GET['arrayid'];
	// a check to make sure this is in fact a user that can delete this array....
	$userid = $_SESSION['userid'];

	// if this is an administrator, this individual can delete the array and associated data....
	$privsql = "SELECT priv_level FROM users WHERE id = $userid";
	$result = mysql_query($privsql, $db);
	$row = mysql_fetch_row($result);
	$priv_level = $row[0];
	if($priv_level != 99){
		die("You don't have the required privileges to delete this array!");
	}
include 'utilityfunctions.inc';
if($submitted != true){

	$arrayid=$_GET['arrayid'];

	
	if($arrayid != ""){
		//echo "Editing....<br>";
		// We need to treat this form as an edit form, not as a new array form.
		// get the values associated with it in the agilent_arrayinfo table.
		$sql = "SELECT * FROM agilent_arrayinfo WHERE arrayid = $arrayid";

		
		$assocResult = mysql_query($sql,$db);
		
		$assocrow = mysql_fetch_assoc($assocResult);
	$width = 600;
	$tableid = "results";
	}
}
	
$cssclass = "tundra";


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
	function deletearray(){

		var answer = confirm("Are you sure?  All associated data with this array will be deleted!")
	if (answer){

		<?php
			$sql = "DELETE FROM agilentdata WHERE arrayid = $arrayid";
			$sql = mysql_query($sql, $db);

			$sql = "DELETE FROM agilent_arrayinfo WHERE arrayid = $arrayid";
			$sql = mysql_query($sql, $db);
			
			$sql = "DELETE FROM agilent_experiments WHERE arrayid = $arrayid";
			$sql = mysql_query($sql, $db);
			
		?>
		alert("Array Deleted.")
		window.close();
	}
	else{
		alert("Thanks for sticking around!")
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
<body class="<?php echo $cssclass; ?>" height="1800px">
<div dojoType="dijit.layout.LayoutContainer" id="mainDiv"
style="border: 1px solid #bfbfbf; float: left; width: 100%; height: 100%;overflow:auto;">
	<div dojoType="dijit.layout.ContentPane"
		orientation="horizontal"
		sizerWidth="5"
		activeSizing="0"
		selected="true"
	>
	<div class="header">

		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression Feature Information</font></h4>
	</div>
<br>
<br>
<br>
<?php 

if(!isset($_POST['submit'])){
?>
Do you wish to delete the array,<font color="Red"><strong> <?php echo "#".$arrayid." : ".$assocrow['arraydesc']." "; ?> </strong></font>with the values listed in the table below?<br>
This action will result in the deletion of all associated data and experiment associations for this array.
<form name="order" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>" onsubmit="deletearray()">
<input type="submit" name="submit" value="YES!">
</form>

<div dojoType='dijit.TitlePane' title='All Data Values' open='false' width="600">
<?php
	array2table($assocrow, $width, $tableid);
?>
</div>

<?php
}
?>
</div>
</body>
</html>