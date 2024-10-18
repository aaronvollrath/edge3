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
//analyze($_SESSION);
$privval = $_SESSION['priv_level'];
if($privval != ""){
// Get the data to populate the experiment name field:
	if($privval == 99){
			$expSQL = "SELECT expid, expname, descrip FROM agilent_experimentsdesc  ORDER BY expid";
		}else{
			$expSQL = "SELECT expid, expname,descrip FROM agilent_experimentsdesc WHERE ownerid=$privval ORDER BY expid";

	}
		$expResult = mysql_query($expSQL, $db);
		$firstchoice = 1;
		$expBuilderMenu = "<select name=\"selectedBuilderExperiment\" id=\"selectedBuilderExperiment\" onChange=\"updateArrayList()\">";
		$expBuilderMenu .= "<option label=\"\" value=\"-1\" >No Experiment Selected</option>  ";
		$expMenu = "<select name=\"selectedExperiment\" onChange=\"updateexpdesc()\">";
		$arrayExpMenu = "<select name=\"selectedExperimentArray\">";
		$expDesc = "none chosen";
		while($row = mysql_fetch_array($expResult))
		{
			$expname = $row[1];
			if($firstchoice == 1){
				$expMenu .= "<option label=\"$expname\" value=\"$row[0]\" checked>$expname</option>  ";
				$arrayExpMenu .=  "<option label=\"$expname\" value=\"$row[0]\" checked>$expname</option>  ";
				$expBuilderMenu .= "<option label=\"$expname\" value=\"$row[0]\" >$expname</option>  ";
				$expDesc = $row[2];
				$firstchoice = 0;
			}
			else{
				$expMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\">$expname</option>  ";
				$arrayExpMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\">$expname</option>  ";
				$expBuilderMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\">$expname</option>  ";
			}
		}
		$expMenu .= "<option value=\"-1\">New/Edit Experiment</option></select>";
		$arrayExpMenu .= "</select>";
		$expBuilderMenu .= "</select>";

}




?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>

<link rel="stylesheet" type="text/css" href="./css/tablelayout.css" title="layout" />

<title>EDGE User Administration</title>
	<script type="text/javascript" src="./dojo-release-1.0.0/dojo/dojo.js"
                djConfig="parseOnLoad: true"></script>
	<script type="text/javascript" src="./javascript/newagilentarrayformcheck.js"
                djConfig="parseOnLoad: true, usePlainJson: true"></script>
		<script type="text/javascript" src="./javascript/agilentexperiment-useradmin.js"
                djConfig="parseOnLoad: true, usePlainJson: true"></script>

	<script type="text/javascript" src="./javascript/expbuilderform.js"
                djConfig="parseOnLoad: true, usePlainJson: true"></script>
		<style type="text/css">

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
    <script type="text/javascript">



    </script>

        <style type="text/css">
                @import "./dojo-release-1.0.0/dojo/resources/dojo.css";
                @import "./dojo-release-1.0.0/dijit/themes/soria/soria.css";
                @import "./dojo-release-1.0.0/dijit/demos/mail/mail.css";
        </style>

</head>
<body class="soria" onLoad="loadScript()">
<?php
require('./phpinc/newagilentarray-nonadmin.inc');

?>
</body>
</html>


