<?php
require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
$db2 = mysql_connect("localhost", "root", "arod678cbc3",TRUE);
mysql_select_db("mygo", $db2);
// Need to check if the user is logged in because this is a restricted area...
// Need to check if the user is logged in because this is a restricted area...
/*echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";*/
 function utime (){
$time = explode( " ", microtime());
$usec = (double)$time[0];
$sec = (double)$time[1];
return $sec + $usec;
}
$start = utime();


?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout" />
<title>EDGE^3</title>
<?php
require './phpinc/edge3-login-form-check.inc';
//include 'header.inc';
require "formcheck2.inc";
include 'edge_update_user_activity.inc';
include 'outputimage.inc';
include 'utilityfunctions.inc';
include 'selectclusteringorderingmethod.inc';
$arraytypestring = "agilent";
$arrayclusteringtype = 1;
$arraydatatable = "agilentdata";
$thisorganism = 0; // This is a mouse!
$cssclass = "tundra";
//analyze($_SESSION);
if($userid == 1){
	$dojodebugval = ", isDebug: true";
}else{
	$dojodebugval = "";
}
?>
<link rel="stylesheet" type="text/css" href="./css/tablelayout.css" title="layout" />

<title>Edge layout</title>
	<script type="text/javascript" src="./dojo-release-1.0.0/dojo/dojo.js"
                djConfig="parseOnLoad: true <?php echo $dojodebugval; ?>"></script>
	<script type="text/javascript" src="./javascript/newagilentarrayformcheck.js"
                djConfig="parseOnLoad: true"></script>
		<script src="sorttable.js"></script>
    <script type="text/javascript">
        dojo.require("dojo.parser");

        dojo.require("dijit.Toolbar");

        dojo.require("dijit.layout.LayoutContainer");
        dojo.require("dijit.layout.SplitContainer");
        dojo.require("dijit.layout.AccordionContainer");
        dojo.require("dijit.layout.TabContainer");
        dojo.require("dijit.layout.ContentPane");
	dojo.require("dijit.form.Button");
dojo.require("dijit.Menu");
dojo.require("dijit.Tooltip");
dojo.require("dijit.Dialog");
dojo.require("dijit.form.ComboBox");
dojo.require("dijit.form.CheckBox");
dojo.require("dijit.form.FilteringSelect");
dojo.require("dijit.form.Textarea");
dojo.require("dijit.Tree");
dojo.require("dijit.TitlePane");
       dojo.require("dijit.form.TextBox");
        dojo.require("dijit.Editor");

dojo.require("dijit.InlineEditBox");
dojo.addOnLoad(

function(){

dojo.byId('loaderInner').innerHTML += " done.";
			setTimeout("hideLoader()",250);

});
function hideLoader(){
			var loader = dojo.byId('loader');
			dojo.fadeOut({ node: loader, duration:500,
				onEnd: function(){
					loader.style.display = "none";
				}
			}).play();
}

function queryTempLoad(userid){
	dijit.byId('queryDialog').setHref("./phpinc/unsavedqueryscript.php?userid="+userid);
dijit.byId('queryDialog').show();

}

function querySavedLoad(userid){
	dijit.byId('queryDialog').setHref("./phpinc/savedqueryscript.php?userid="+userid);
dijit.byId('queryDialog').show();

}

function fakequerySave(){
	dijit.byId('queryDialog').setHref("./savequery.php?tempquery=1");
dijit.byId('queryDialog').show();

}

function displayClusteringModule(){
	// Need to display the tab for clustering....
	if(dijit.byId('clustering').checked == true){
		//dijit.byId('mainTabContainer').addChild(new dijit.layout.ContentPane( {title:"<b>clustering test</b>", id:"clusteringmodule"}));
		document.load("edge3b.php");
	}else{
		dijit.byId('mainTabContainer').removeChild("clusteringmodule");

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
<?php
if($logged_in == 0){
	$onload = "";
}else{
	if($clusteringmodule == 1){
		$onload = "return hideTrxRowOnLoadAgilentClustering()";
	}

}
?>
<body  onload="<?php echo $onload; ?>" class="<?php echo $cssclass; ?>">
<!-- basic preloader: -->
	<div id="loader"><div id="loaderInner"><h3>Loading EDGE<sup>3</sup> Data Analysis Area ... </h3></div></div>
<div dojoType="dijit.layout.SplitContainer"
                orientation="horizontal"
                sizerWidth="7"
                activeSizing="true"
                style="border: 1px solid #bfbfbf; float: left; width: 100%; height: 100%;">
	<div dojoType="dijit.layout.AccordionContainer" sizeMin="10" sizeShare="10" style="float: left; margin-right: 0px; overflow: auto">
		<div dojoType="dijit.layout.AccordionPane" title="Edge<sup>3</sup> Menu">

		
			<?php
				if($logged_in == 0){
					echo "Please log in!";

				}else{
			?>
				<ul>
			<li><a href="./agilentexperiment-useradmin.php" target="new"> EDGE<sup>3</sup> RNA submission and Experiment Builder</a></li>
		<li> <a href="edge3b.php?clusteringmodule=1">Load Edge<sup>3</sup> Clustering Module</a></li>
		<li> <a href="edge3b.php?knearestmodule=1">Load Edge<sup>3</sup> k-Nearest Module</a></li>
		<li> <a href="edge3b.php?orderedheatmapmodule=1">Load Edge<sup>3</sup> Heatmap of ordered list Module</a></li>
		<li> <a href="edge3b.php?selectedclonesclusteringmodule=1">Load Edge<sup>3</sup> Selected Clone Clustering Module</a></li>
			<?php
			if(isset($_POST['submit'])) {

			?>

			<?php
			}
			if(isset($_POST['knearestsubmitorder']) || isset($_POST['knearestsubmit'])){
			?>

			<?php
			}
			?>
			</ul>



			<?php




				}


			?>


		</div><!-- end Edge3 Menu Accordian Pane -->
		<div dojoType="dijit.layout.AccordionPane" title="Useful Information">
			<table width="200"><tr><td><b><u>What do the icons mean?<b></u></td></tr><tr><td><p><img src="./images/dialog-information12x12.png"/> <b><u>Information Icon</u>:</b> Put the mouse cursor over this icon for more information regarding an available option.</td></tr></table>



		</div> <!-- end Useful Information Accordian Pane -->
	</div> <!-- end of Accordion pane Container -->
	<div id="mainTabContainer" dojoType="dijit.layout.TabContainer" sizeMin="20" sizeShare="90">

			<!-- main section with tree, table, and preview -->
			<div dojoType="dijit.layout.ContentPane"
						orientation="horizontal"
						sizerWidth="5"
						activeSizing="0"
						title="<b>Welcome!</b>"
						<?php
						//closable="true"
						if(isset($_POST['Login'])) {
						?>
						selected="true"
						<?php
						}
						?>
					>

					<?php

						require('./phpinc/edge3-login-form-welcome.inc');



		?>


		<div dojoType="dijit.Dialog" id="queryDialog" href="" title="Edge<sup>3</sup> Array Information" style="width: 400px; height: 300px;">hello</div>


			</div> <!-- End of EDGE3 welcome pane  -->
<?php
	if($clusteringmodule==1){
?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Agilent Array Clustering</b>"
				selected="true"
                        >




				<?php
				//analyze($_POST);
				if($logged_in != 0){
				require('./phpinc/agilentarrayclustering.inc');
				//echo "EDGE clustering is down for maintenance...";
				}else{
					echo "Please login to access clustering.";
				}

				?>
      			</div> <!-- End of Agilent Array Clustering tab.... -->
<?php
}
?>
<?php
	if($orderedheatmapmodule==1){
?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Ordered List Heatmap</b>"
				selected="true"
                        >




				<?php
				//analyze($_POST);
				if($logged_in != 0){
				//analyze($_POST);
				require('./phpinc/edge3orderedclone.inc');
				//echo "EDGE clustering is down for maintenance...";
				}else{
					echo "Please login to access clustering.";
				}

				?>
      			</div> <!-- End of Agilent Array Clustering tab.... -->
<?php
}



	if($selectedclonesclusteringmodule==1){
?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Agilent Selected Clone Clustering</b>"
				selected="true"
                        >




				<?php
				//analyze($_POST);
				if($logged_in != 0){
				//analyze($_POST);
				require('./phpinc/edge3selectedcloneclustering.inc');
				//echo "EDGE clustering is down for maintenance...";
				}else{
					echo "Please login to access clustering.";
				}

				?>
      			</div> <!-- End of Agilent Array Clustering tab.... -->
<?php
}
?>
<?php
	$privval = $_SESSION['priv_level'];

	if($privval == 99){

	if($knearestmodule==1){

?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Agilent k-Nearest Neighbors</b>"
				<?php
				if(isset($_POST['knearestsubmitorder']) || isset($_POST['knearestsubmit'])|| isset($_GET['knearestsubmit']))
				{
				?>
				selected="true"
				<?php
				}
				?>
                        >




				<?php
				//analyze($_POST);
				if($logged_in != 0){
					require('./phpinc/edge3-k-nearestneighbors.inc');
				}else{

					echo "Please login to access clustering.";
				}

				?>
      			</div> <!-- End of Agilent k-nearest neighbors tab.... -->
<?php
}
}

	$userid = $_SESSION['userid'];

	if($nbclassificationmodule==1){

if($userid == 1){
?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Agilent Naive Bayes Classification</b>"
				<?php
				if(isset($_POST['nbsubmitorder']) || isset($_POST['nbsubmit'])|| isset($_GET['nbsubmit']))
				{
				?>
				selected="true"
				<?php
				}
				?>
                        >




				<?php
				//analyze($_POST);
				if($logged_in != 0){
					require('./phpinc/edge3-naivebayesclassification.inc');
				}else{

					echo "Please login to access naive bayes classification.";
				}

				?>
      			</div> <!-- End of Agilent k-nearest neighbors tab.... -->
<?php
}
}
?>




	</div> <!-- end tab container-->
</div> <!-- end split layout container -->
</body></html>
