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
//echo "session data<br>";
//analyze($_SESSION);



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

require('./phpinc/newagilentrnasample-nonadmin.inc');

?>
</body>
</html>


