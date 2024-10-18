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

		// Create the Experiment Group Builder List
		if($privval == 99){
			$expSQL = "SELECT expgroupid, expgroupname, descrip FROM agilent_experimentgroupsdesc  ORDER BY expgroupid";
		}else{
			$expSQL = "SELECT expgroupid, expgroupname, descrip FROM agilent_experimentgroupsdesc WHERE ownerid=$privval ORDER BY expid";

	}
		$expGroupMenu = "<select name=\"selectedExperiment\" onChange=\"updateexpdesc()\">";
		$expGroupBuilderMenu = "<select name=\"selectedBuilderExperiment\" id=\"selectedBuilderExperiment\" onChange=\"updateArrayList()\">";
		$expGroupBuilderMenu .= "<option label=\"\" value=\"-1\" >No Experiment Selected</option>  ";

		$expResult = mysql_query($expSQL, $db);
		$firstchoice = 1;
		while($row = mysql_fetch_array($expResult))
		{
			$expgroupname = $row[1];
			if($firstchoice == 1){
				$expGroupMenu .= "<option label=\"$expgroupname\" value=\"$row[0]\" checked>$expgroupname</option>  ";
				$expGroupBuilderMenu .= "<option label=\"$expname\" value=\"$row[0]\" >$expgroupname</option>  ";
				$expGroupDesc = $row[2];
				$firstchoice = 0;
			}
			else{
				$expGroupMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\">$expgroupname</option>  ";
				$expGroupBuilderMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\">$expgroupname</option>  ";
			}
		}
		$expGroupBuilderMenu .= "</select>";
		$expGroupMenu .= "<option value=\"-1\">New/Edit Experiment</option></select>";
		$expGroupMenu .= "</select>";

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
        .target {border: 1px dotted gray; width: 300px; height: 300px;padding: 5px; -moz-border-radius:8pt 8pt;radius:8pt;overflow: auto;}
	.source {border: 1px dotted skyblue;height: 300px; width: 300px;-moz-border-radius:8pt 8pt;radius:8pt; overflow:auto;overflow: auto;}
	.dojoDndItemOver {background: #feb;border: 1px dotted gray; }
	 .dojoDndItemSelected {background: #feb;border: 1px solid black; }
	.dojoDndItemBefore {border-left: 2px dotted gray; }
	.dojoDndItemAfter {border-right: 2px dotted gray; }
	.target .dojoDndItemAnchor {background: #ededed;border:1px solid gray;}
	.dojoDndAvatar {font-size: 75%; color: black;}
	.dojoDndAvatar td {padding-left: 20px; padding-right: 4px;height:20px}
	.dojoDndAvatarHeader {background: #ccc; background-repeat: no-repeat;}
	.dojoDndAvatarItem {background: #eee;}

</style>
    <script type="text/javascript">



    </script>

        <style type="text/css">
                @import "./dojo-release-1.0.0/dojo/resources/dojo.css";
                @import "./dojo-release-1.0.0/dijit/themes/soria/soria.css";
                @import "./dojo-release-1.0.0/dijit/demos/mail/mail.css";
        </style>

</head>
<body class="soria" onLoad="loadScript()">
<!-- basic preloader: -->
	<div id="loader"><div id="loaderInner"><h3>Loading EDGE<sup>3</sup> Experiment Builder ... </h3></div></div>
<div dojoType="dijit.layout.SplitContainer"
                orientation="horizontal"
                sizerWidth="7"
                activeSizing="true"
                style="border: 1px solid #bfbfbf; float: left; width: 100%; height: 100%;">
	<div dojoType="dijit.layout.AccordionContainer" sizeMin="20" sizeShare="20" style="float: left; margin-right: 0px; overflow: hidden">
		<div dojoType="dijit.layout.AccordionPane" title="Your Experiments" style="width: 400px">
<?php
if ($logged_in != 0) {
$filenum = rand(0, 25000);
	$file = "/var/www/html/edge2/IMAGES/experiments$filenum.json";

	$command = "touch $file";
	$fd = fopen($file, 'w');
	rewind($fd);
	$filetext = "{ identifier:'name',\nlabel:'name',\nitems:[";
	fwrite($fd, $filetext);
				// Get a list of experiments based on the owner's id....


				//echo "your priv level: $privval<br>";
	if($privval == 99){
		$yourExpSQL = "SELECT expid, expname FROM agilent_experimentsdesc ORDER BY expid";
	}else{
		$yourExpSQL = "SELECT expid, expname FROM agilent_experimentsdesc WHERE ownerid=$privval ORDER BY expid";

	}
			//echo "$yourExpSQL<br>";
		$yourExpResult = mysql_query($yourExpSQL, $db);
		$count = 0;
		while($row = mysql_fetch_row($yourExpResult)){
			$filetext = "";
			$expid = $row[0];
			$expname = $row[1];
			if($count != 0){
				//put a ',' first...
				$filetext = ",";
				fwrite($fd, $filetext);
			}

			$filetext ="{name:'".$expname."',type:'experiment', expid:'".$expid."'";
			fwrite($fd, $filetext);
			//echo "$filetext<br>";
			$yourArraysCountSQL = "SELECT COUNT(arrayid) from agilent_experiments WHERE expid = $expid ORDER BY arrayid";
			//echo "$yourArraysCountSQL<br>";
			$yourArraysCountResult = mysql_query($yourArraysCountSQL, $db);
			$arrayCount = mysql_fetch_row($yourArraysCountResult);
			if($arrayCount[0] > 0){
				$filetext = ", children:[";
				fwrite($fd, $filetext);
				$yourArraysSQL = "SELECT e.arrayid from agilent_experiments as e WHERE e.expid = $expid ORDER BY e.arrayid ASC";
				//echo "$yourArraysSQL<BR>";
			$yourArraysResult = mysql_query($yourArraysSQL, $db);
			$arraycount = 0;
			while($yourArrays = mysql_fetch_row($yourArraysResult)){
				//echo "$yourArraysSQL<br>";
				if($arraycount != 0){
					$filetext = ",";
					fwrite($fd, $filetext);
				}
				$arraydescSQL = "SELECT arraydesc FROM agilent_arrayinfo WHERE arrayid = $yourArrays[0]";
				$arraydescResult = mysql_query($arraydescSQL, $db);
				$arraydescVal = mysql_fetch_row($arraydescResult);
				$filetext = "{_reference:'".$yourArrays[0]." : ".$arraydescVal[0]."'}\n";
				fwrite($fd, $filetext);
				$arraycount++;
				//echo "$filetext<BR>";
			}
			$filetext = "]}";

			fwrite($fd, $filetext);
			}else{

				$filetext = "}";
				fwrite($fd,$filetext);

			}
			if($arrayCount > 0){
				$yourArraysSQL = "SELECT arrayid from agilent_experiments WHERE expid = $expid ORDER BY arrayid";
				//echo "$yourArraysSQL<BR>";
			$yourArraysResult = mysql_query($yourArraysSQL, $db);
			$arraycount = 0;
			while($yourArrays = mysql_fetch_row($yourArraysResult)){
				//echo "$yourArraysSQL<br>";
				//if($arraycount != 0){
					$filetext = ",";
					fwrite($fd, $filetext);
				//}Check 'Edit Treatment Name?' checkbox to modify current treatment name.
				$arraydescSQL = "SELECT arrayid, arraydesc FROM agilent_arrayinfo WHERE arrayid = $yourArrays[0]";
				$arraydescResult = mysql_query($arraydescSQL, $db);
				$arraydescVal = mysql_fetch_row($arraydescResult);
				$filetext = "{name:'".$yourArrays[0]." : ".$arraydescVal[1]."', type:'array', arrayid:'".$arraydescVal[0]."'}\n";
				fwrite($fd, $filetext);
				$arraycount++;
				//echo "$filetext<BR>";
			}
				//$filetext .= "]}";

			//fwrite($fd, $filetext);



			}


			//fwrite($fd, $filetext);
			$count++;
			//echo "incrementing count<br>";
		}
	//fwrite($fd, $filetext);
		fwrite($fd, "]}");
		fflush($fd);
		rewind($fd);

		fclose($fd);
}
//$filenum = 22481;
?>
<div dojoType="dojo.data.ItemFileReadStore" jsId="experimentsStore"
		url="./IMAGES/experiments<?php echo $filenum; ?>.json"></div>
<?php

if ($logged_in == 0) {
	echo "Please login.";
}else{
?>
<div dojoType="dijit.Tree" id="mytree" store="experimentsStore" query="{type:'experiment'}"
		labelAttr="name" typeAttr="type"></div>
<?php

}

?>


		</div>
		<div dojoType="dijit.layout.AccordionPane" title="Your Array Queue">
  Your arrays go here....


		</div>
	</div> <!-- end of Accordion pane -->
       <div id="mainTabContainer" dojoType="dijit.layout.TabContainer" sizeMin="20" sizeShare="80">
	 <!-- main section with tree, table, and preview -->
	 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Welcome!"
				closable="true"

                        >

			<?php

				require('./phpinc/edge3-login-form-welcome.inc');
				//analyze($_POST);

?>


<div dojoType="dijit.Dialog" id="thirdDialog" href="" title="Edge<sup>3</sup> Array Information" style="width: 400px; height: 300px;"></div>

	</div>



	<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="New/Edit Experiment Group"
				<?php
					if($expsubmitted == "true"){
						echo "selected=\"true\"";
					}
				?>
                        >

 				<div id="exp" dojoType="dijit.layout.ContentPane">
				<?php
					require('./phpinc/agilent_new-edit_experimentgroup.inc');
				?>
				</div>
      			</div>
<?php

if ($logged_in != 0) {
	//if($privval == 99){
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Experiment Group Builder"

                        >
			<br><br><br>



			<?php

				require('./phpinc/groupexperimentbuilder2.inc');

?>
			</div>


<?php
	}

?>






?>
	</div>

</div>
</body></html>
