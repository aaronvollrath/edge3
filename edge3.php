<?php

session_start();
require 'globalvariables.inc.php';
require 'globalfilelocations.inc';

require 'utilityfunctions.inc'; # general utility functions file used throughout
require 'edge_db_connect2.php';

//die('after db connection script');
//echo "after db connection script<hr>";
$logged_in = 0;
if(isset($_SESSION['userid'])){
	$userid = $_SESSION['userid'];
	$logged_in = 1;
}else{
	// go to the login page.
	//############################################################# */
	echo "No user is logged in.";
}
//echo '<br>after checking to see if session variable userid is set.<br>';
include 'edge_update_user_activity.inc';

 function utime (){
$time = explode( " ", microtime());
$usec = (double)$time[0];
$sec = (double)$time[1];
return $sec + $usec;
}
$start = utime();

//
?>
<!DOCTYPE html>
<!--
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
-->
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout" />
<link rel="stylesheet" type="text/css" href="./css/ui.tabs.css" title="layout" />
<title>EDGE^3</title>
<?php

include 'outputimage.inc'; # used for creating an raster image from svg file

include 'selectclusteringorderingmethod.inc.php';  # located in ./phpinc, checks posted values and also selects what algorithm is used.
$arraytypestring = "agilent";
$arrayclusteringtype = 1;
$arraydatatable = "agilentdata"; # what table are the data coming from?
$thisorganism = 0; // This is a mouse!  (may be a defunct variable....)
$cssclass = "tundra";
$userid = 0;

//analyze($_SESSION);
if($userid == 1){
	$dojodebugval = ", isDebug: true";

}else{
	$dojodebugval = "";
}

if($logged_in == 1 && !isset($_SESSION['userid'])){
$logged_in = 0;
}
?>

<!-- newly added... -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .checkbox-grid {
            display: flex;
            flex-wrap: wrap;
            list-style-type: none;
        }

        .checkbox-grid li {
            flex: 0 0 25%;
        }
    </style>






<link rel="stylesheet" type="text/css" href="./css/tablelayout.css" title="layout" />

<title>EDGE<sup>3</sup> Data Analysis</title>


<?php
	if(!isset($_POST['anova']) && !isset($_POST['ttest'])){
?>

 <script src="./javascript/jquery-1.2.6.min.js"></script>

  <script type="text/javascript" src="./javascript/jquery-uicore-tabs.js"></script> 
  <script type="text/javascript" src="./javascript/formcheck2.js"></script>
<script type="text/javascript" src="./javascript/radiocheckboxvalidation.js"></script>
<link rel="stylesheet" href="./css/ui.tabs.css" type="text/css" media="print, projection, screen">
  <script>


  $(function() {
                $('#tabs > ul').tabs({ fx: { height: 'toggle', opacity: 'toggle' } });
		$('#tabsmenu > ul').tabs({ fx: { height: 'toggle', opacity: 'toggle' } });
	
  });

 </script>
<?php
		
	}
?>
 

	<script type="text/javascript" src="./dojo-release-1.0.0/dojo/dojo.js"
                djConfig="parseOnLoad: true"></script>
	<script type="text/javascript" src="./javascript/newagilentarrayformcheck.js"
                djConfig="parseOnLoad: true"></script>
		<script src="sorttable.js"></script>
    <script type="text/javascript">
        dojo.require("dojo.parser");
        dojo.require("dijit.layout.LayoutContainer");
        dojo.require("dijit.layout.SplitContainer");
        dojo.require("dijit.layout.AccordionContainer");
        dojo.require("dijit.layout.TabContainer");
        dojo.require("dijit.layout.ContentPane");
		dojo.require("dijit.form.Button");
		dojo.require("dijit.Toolbar");
		dojo.require("dijit.Menu");
		dojo.require("dijit.Tooltip");
		dojo.require("dijit.Dialog");
		dojo.require("dijit.form.ComboBox");
		dojo.require("dijit.form.CheckBox");
		dojo.require("dijit.form.Textarea");
		dojo.require("dijit.TitlePane");
		dojo.require("dijit.form.TextBox");

		dojo.require("dijit.InlineEditBox");

		dojo.addOnLoad(

			function(){

			//dojo.byId('loaderInner').innerHTML += " done.";
						//setTimeout("hideLoader()",250);

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



		function queryGenesByUserid(userid){
			dijit.byId('queryDialog').setHref("./genelistloader.php?userid="+userid);
		dijit.byId('queryDialog').show();

		}

		function queryGenesByPublic(userid){
			dijit.byId('queryDialog').setHref("./genelistloader.php");
		dijit.byId('queryDialog').show();

		}

		function fakequerySave(){
			dijit.byId('queryDialog').setHref("./savequery.php?tempquery=1");
		dijit.byId('queryDialog').show();

		}

		function displayClusteringModule(){
			// Need to display the tab for clustering....
			if(dijit.byId('clustering').checked == true){
				document.load("edge3.php");
			}else{
				dijit.byId('mainTabContainer').removeChild("clusteringmodule");

			}
		}

		function updatefeaturenums() { // 

		/*
		need to check to make sure that a button has been pressed.....
		*/
		var genelistboxChecked = false;

		var dml = document.forms['getgenes'];
		var query = document.forms['query'];
		len = dml.elements.length;
		var errormessage = "";
		var genelistschecked = "";
		for (var i=0, j=len; i<j; i++) {
			myType = dml.elements[i].type;
			myName = dml.elements[i].name;

			if (myType == 'checkbox') {
				if(dml.elements[i].checked){
				if(genelistboxChecked != true){
					// FIRST LIST TO ADD...
					genelistschecked += dml.elements[i].value;
				}else{
					genelistschecked += "," + dml.elements[i].value;
				}
					genelistboxChecked = true;
				
			}
			}
		}
			
			if (!genelistboxChecked){
			alert('Please select at least 1 gene list.');
			return false;
			}else{
				dojo.xhrGet( { // �
							// The following URL must match that used to test the server.
				url: "./phpinc/generatelist.inc.php?lists=" + genelistschecked,
				handleAs: "text",

				timeout: 5000, // Time in milliseconds

				// The LOAD function will be called on a successful response.
				load: function(response, ioArgs) { // �
				//dml.getElementById("cloneList").setValue("")
				//query.getElementById("cloneList").setValue(response);
					dijit.byId("cloneList").setValue(response); // �
				return response; // �
				},

				// The ERROR function will be called in an error case.
				error: function(response, ioArgs) { // �
				console.error("HTTP status code: ", ioArgs.xhr.status); // �
			errormessage = "HTTP status code: " + ioArgs.xhr.status;
			alert("error: " + errormessage);
				return response; // �
				}
				});

			}
			
		alert("gene list(s) loaded.  close selection window to continue...");

		return true;

		}

function concatenateandsavelists() { 

/*
need to check to make sure that a button has been pressed.....
*/
var genelistboxChecked = false;

var dml = document.forms['getgenes'];
   len = dml.elements.length;
var errormessage = "";
var genelistsparams = "";
var genelistschecked = "";
var publicval = dml.listispublic.checked;
if(publicval){
	genelistparams = "&listispublic=1";

}else{
	genelistparams = "&listispublic=0";
}

//newlistname
if(dml.newlistname.value != ""){
	genelistparams += "&newlistname=" + dml.newlistname.value;

}else{

	alert("Please enter a name value for the list(s) you want to save.");
	return false;
}
if(dml.newlistdesc.value != ""){
	genelistparams += "&newlistdesc=" +dml.newlistdesc.value;
}
//alert("public val = " + publicval);
//alert("listname = " + dml.newlistname.value);


   for (var i=0, j=len; i<j; i++) {
       myType = dml.elements[i].type;
       myName = dml.elements[i].name;

       if (myType == 'checkbox') {
       	  if(dml.elements[i].checked){
		if(genelistboxChecked != true){
			// FIRST LIST TO ADD...
			genelistschecked += dml.elements[i].value;
		}else{
			genelistschecked += "," + dml.elements[i].value;
		}
            genelistboxChecked = true;
	    
	  }
       }
   }
	alert("url: ./phpinc/concatenateandsavegenelists.inc.php?lists=" + genelistschecked + genelistparams);
	//return false;
	//alert("genelistschecked : " + genelistschecked);  
	//return false;
	if (!genelistboxChecked){
	alert('Please select at least 1 gene list.');
	return false;
	}else{
		dojo.xhrGet( { // �
					// The following URL must match that used to test the server.
        url: "./phpinc/concatenateandsavegenelists.inc.php?lists=" + genelistschecked + genelistparams,
        handleAs: "text",

        timeout: 5000, // Time in milliseconds

        // The LOAD function will be called on a successful response.
        load: function(response, ioArgs) { // �
		//dml.getElementById("cloneList").setValue("")
             //dijit.byId("cloneList").setValue(response); // �
		//dijit.byId("liststatus").setValue(response);
		//alert(response);
             return response; // �
        },

        // The ERROR function will be called in an error case.
        error: function(response, ioArgs) { // �
          console.error("HTTP status code: ", ioArgs.xhr.status); // �
	 // errormessage = "HTTP status code: " + ioArgs.xhr.status;
	  //alert("error: " + errormessage);
          return response; // �
          }
        });

	}
	
alert("Gene list(s) saved as new.  Close selection window to continue...");

return true;

}

//  This function is used to see whether or not the ttest or anova box is checked.  if it is, it makes the pvalue and correction options visible.  if it is unchecked, it hides them....  these functions are associated with the order form in the file commonorderingscreencode.inc.php
function showtTestOptions(){

	var ttpvaluecell = document.getElementById('ttpvaluecell');
	var corrections = document.getElementById('corrections');
	if(ttpvaluecell.style.visibility == "hidden"){
		
		ttpvaluecell.style.visibility = 'visible';
		corrections.style.visibility = 'visible';
	}else{
		ttpvaluecell.style.visibility = 'hidden';
		corrections.style.visibility = 'hidden';
	}

}
function showAnovaOptions(){

	var anovapvaluecell = document.getElementById('anovapvaluecell');
	var corrections = document.getElementById('corrections');
	if(anovapvaluecell.style.visibility == "hidden"){
		
		anovapvaluecell.style.visibility = 'visible';
		corrections.style.visibility = 'visible';
	}else{
		anovapvaluecell.style.visibility = 'hidden';
		corrections.style.visibility = 'hidden';
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
 /* Not required for Tabs, just to make this demo look better... */
            body, h1, h2 {
                font-family: "Trebuchet MS", Trebuchet, Verdana, Helvetica, Arial, sans-serif;
            }
            h1 {
                margin: 1em 0 1.5em;
                font-size: 18px;
            }
            h2 {
                margin: 2em 0 1.5em;
                font-size: 16px;
            }
            p {
                margin: 0;
            }
            pre, pre+p, p+p {
                margin: 1em 0 0;
            }
            code {
                font-family: "Courier New", Courier, monospace;
            }


        </style>

</head>
<?php
if($logged_in == 0){
	$onload = "";
}else{
	if(isset($_GET['clusteringmodule']) || isset($_GET['selectedclonesclusteringmodule']) || isset($_GET['orderedheatmapmodule'])){
		if(isset($_GET['clusteringmodule'])){
			if($_GET['clusteringmodule']== 1  && !isset($_POST['submit'])){
				$onload = "return hideTrxRowOnLoadAgilentClustering()"; # what js file is this in?
			}else{
				$onload = "";
			}
		}
		elseif(isset($_GET['selectedclonesclusteringmodule'])){
			if($_GET['selectedclonesclusteringmodule']==1  && !isset($_POST['submit'])){
				$onload = "return hideTrxRowOnLoadSelectedCloneClustering()";
			}else{
				$onload = "";
			}
		}elseif(isset($_GET['orderedheatmapmodule'])){
			if($_GET['orderedheatmapmodule'] ==1  && !isset($_POST['submit'])){
				#$onload = "return hideTrxRowOnLoadSelectedCloneClustering()";
				$onload = "";
			}else{
				$onload = "";
			}
		}else{
			$onload = "";
		}
	}else{
		$onload = "";
	}

}
?>
<body  onload="<?php echo $onload; ?>" class="<?php echo $cssclass; ?>">
<!-- basic preloader:
	<div id="loader"><div id="loaderInner"><h3>Loading EDGE<sup>3</sup> Data Analysis Area ... </h3></div></div>
-->
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
				<strong>Data Analysis</strong>
				<ul>

					<li> <a href="edge3.php?diffexprmodule=1"><i>limma</i> Differential Expression</a></li>
				<li> <a href="edge3.php?clusteringmodule=1">Standard Clustering</a></li>
				<li> <a href="edge3.php?selectedclonesclusteringmodule=1">Selected Clone Clustering</a></li>
				<li> <a href="edge3.php?orderedheatmapmodule=1">Ordered List</a></li>
				<!--
				<li> <a href="edge3.php?agilentquestion1=1">Feature Query Module</a></li>
				-->

				<li> <a href="edge3.php?knearestmodule=1">k-Nearest Neighbors</a></li>
				<li> <a href="edge3.php?nbclassificationmodule=1">Naive Bayes Classification</a></li>
				<?php
		//analyze($_GET);
				
				
				if($userid ==1){
				?>
				<!--
				NOTE:  removing these for now....
				
				<li> <a href="edge3.php?singlechannelclustering=1">Single Channel Clustering</a></li>
				<li> <a href="edge3.php?layouttest=1">Layout test</a></li>
				
				-->
				<?php
				}
				?>
				</ul>
			
				<strong>Query and List Management</strong>
				<ul>
				<li> <a href="savequeryeditedge3.php" target="_blank">Manage saved queries.</a></li>
				<li> <a href="edge3.php?genequery=1">Build Gene List/Query Platform for genes</a><img id="genequerytooltip" src="./images/dialog-information12x12.png" align="top"/><div dojoType="dijit.Tooltip" connectId="genequerytooltip"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td></tr><tr><td>For more information and instructions, please see the <b>Useful Information/Instructions</b> section below.</td></tr></table></div></li>
				<li> <a href="savedgenelistedit.php" target="_blank">Manage saved gene lists.</a><img id="managelisttooltip" src="./images/dialog-information12x12.png" align="top"/><div dojoType="dijit.Tooltip" connectId="managelisttooltip"><table width="350px"><tr><td><img src="./images/dialog-information12x12.png"/><strong><u>Quick Tip</u></strong></td></tr><tr><td>For more information and instructions, please see the <b>Useful Information/Instructions</b> section below.</td></tr></table></div></li>
				</ul>
				<!--
				<strong>Experiment Management</strong>
				<ul>
				<li><a href="./agilentexperiment-useradmin.php" target="new">RNA submission and Experiment Builder</a></li>
				</ul>
				-->
				<strong>Your Profile</strong>
				<ul>
				<li><a href="edge3.php?profile=1">Update/Modify your profile</a></li>
				</ul>



<?php

} // end of if logged in .....


?>


		</div><!-- end Edge3 Menu Accordian Pane -->
		<div dojoType="dijit.layout.AccordionPane" title="Useful Information/Instructions">
			<table width="200"><tr><td><b><u>What do the icons mean?<b></u></td></tr><tr><td><p><img src="./images/dialog-information12x12.png"/> <b><u>Information Icon</u>:</b> Put the mouse cursor over this icon for more information regarding an available option.</td></tr></table><p><br /><br />
			<table width="200" class="results"><thead><font color="blue"><b>Instructions</b></font></thead>
			<tr><td class="questionparameter2"> <a href="http://docs.google.com/Doc?id=dchbhm7f_603xgt8mxct" target="_blank">Introduction to EDGE<sup>3</sup> Data Analysis</a></td></tr>
			<tr><td class="questionparameter2"> <a href="http://docs.google.com/Doc?id=dchbhm7f_509gvzsw7ft" target="_blank">Build and use a Gene List/Query Platform for genes</a></td></tr>
			<tr class="questionparameter"><td class="questionparameter"> <a href="http://docs.google.com/Doc?id=dchbhm7f_566hfxdctf3" target="_blank">Manage saved gene lists.</a></td></tr></table></p>


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

					if(isset($_GET['pass']) || isset($_GET['username']) || isset($_GET['userandpass'])){
						require('emailpassusername.inc.php');
					}elseif(isset($_GET['profile'])){
						require('updateaccount.inc.php');
					}else{
						require('edge3-login-form-welcome.inc');
					}
					?>



		<div dojoType="dijit.Dialog" id="queryDialog" href="" title="" style="max-width:600px; height: 600px;word-wrap: break-word;">hello</div>
		</div> <!-- End of EDGE3 welcome pane  -->
		<?php
			
if(isset($_GET['register'])){
	//echo "Register is set...";
	if($_GET['register']==1){
?>
	<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Register</b>"
				selected="true"
                        >


			

				<?php
				
				
				require('./phpinc/edge3registration.inc.php');
				

				?>
      			</div> <!-- End of Registration tab.... -->

<?php
	}
}
?>
			
<?php
if(isset($_GET['clusteringmodule'])){
	if($_GET['clusteringmodule']==1){
?>
		
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Standard Clustering</b>"
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
      			</div> <!-- End of  Array Clustering tab.... -->
<?php
	}
}#diffexprmodule=1
if(isset($_GET['diffexprmodule'])){
	if($_GET['diffexprmodule']==1){
?>
		
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b><i>limma</i>-based Differential Expression</b>"
				selected="true"
                        >


			

				<?php
				//analyze($_POST);
				if($logged_in != 0){
				require('./phpinc/rdifferentialexpression.inc.php');
				//echo "EDGE clustering is down for maintenance...";
				}else{
					echo "Please login to access differential expression.";
				}

				?>
      			</div> <!-- End of  Array Clustering tab.... -->
<?php
	}
}

if(isset($_GET['orderedheatmapmodule'])){
	if($_GET['orderedheatmapmodule']==1){
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
      			</div> <!-- End of  Array Clustering tab.... -->
<?php
	}
}
if(isset($_GET['agilentquestion1'])){
	if($_GET['agilentquestion1']==1){
?>
<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Feature Query</b>"
				selected="true"
                        >
			<?php
				if($logged_in != 0){
				require('./phpinc/agilentquestion1.inc');
				}else{
					echo "Please login to access feature query.";
				}
				?>
			</div> <!-- End of  Array Clustering tab.... -->
<?php
	}
}
if(isset($_GET['genequery'])){
	if($_GET['genequery']==1){
?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Gene Query</b>"
				selected="true"
                        >
				<?php
				if($logged_in !=0){
					require('./phpinc/genequery1.inc');
				}
				else{
					echo "Please login to access gene query.";
				}
				?>
			</div>
<?php
	}
}
if(isset($_GET['selectedclonesclusteringmodule'])){
	if($_GET['selectedclonesclusteringmodule']==1){
?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Selected Feature Clustering</b>"
				selected="true"
                        >
				<?php
				//analyze($_POST);
				if($logged_in != 0){
				
				require('./phpinc/edge3selectedcloneclustering.inc');
				//echo "EDGE clustering is down for maintenance...";
				}else{
					echo "Please login to access clustering.";
				}

				?>
      			</div> <!-- End of  Array Clustering tab.... -->
<?php
	}
}
?>
<?php
	if(isset($_SESSION['priv_level'])){
		$privval = $_SESSION['priv_level'];
	}else{
		$privval = -1;
	}


	if(isset($_GET['knearestmodule'])){
		if($_GET['knearestmodule']==1){

?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>k-Nearest Neighbors</b>"
				
				selected="true"
				
                        >




				<?php
				//analyze($_POST);
				if($logged_in != 0){
					require('./phpinc/edge3-k-nearestneighbors.inc');
					
				}else{

					echo "Please login to access clustering.";
				}

				?>
      			</div> <!-- End of  k-nearest neighbors tab.... -->
<?php
		}
	}

if(isset($_SESSION['userid'])){
	$userid = $_SESSION['userid'];
}else{
	$userid= -1;
}
if(isset($_GET['nbclassificationmodule'])){
	if($_GET['nbclassificationmodule']==1){

//if($userid == 1){
?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Naive Bayes Classification</b>"
				<?php
				//if(isset($_POST['nbsubmitorder']) || isset($_POST['nbsubmit'])|| isset($_GET['nbsubmit']))
				//{
				?>
				selected="true"
				<?php
				//}
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
      			</div> <!-- End of  k-nearest neighbors tab.... -->
<?php
	}
}
/*
if(isset($_GET['singlechannelclustering'])){
	if($_GET['singlechannelclustering']==1){
		if($userid == 1){
	?>
		<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Single Channel Clustering</b>"
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
			require('./phpinc/agilentsinglechannelclustering.inc');

			?>
			</div>
			<?php

		}
	}
}

if($_GET['layouttest']==1){
		if($userid == 1){
	?>
		<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="<b>Layout test</b>"
				
				selected="true"
				
				
                        >
			
			<?php require('./phpinc/newedgelayouttest.php'); ?>
			</div>
			<?php

		}
	}
*/
?>





	</div> <!-- end tab container-->
</div> <!-- end split layout container -->
<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body></html>
