<?php
session_start();
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
require './phpinc/edge3-login-form-check.inc';
/* if ($logged_in == 0) {
	//die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
	//echo "You are not logged!";
} */
require('./phpinc/globalvariables.inc.php');
/*
$db = mysql_connect("localhost", $edgedbuser, $edgedbuserPW);
mysql_select_db("edge", $db);
*/
$logged_in = 0;
require("fileupload-class.php");
include 'edge_update_user_activity.inc';
 //require './lib/dsrte.php';
require("globalfilelocations.inc");
include 'utilityfunctions.inc';
if(isset($_SESSION['userid'])){
	
	$userid = $_SESSION['userid'];
	$logged_in = 1;

}
//analyze($_SESSION);
if(isset($_SESSION['priv_level'])){


$privval = $_SESSION['priv_level'];
}else{
	$privval = "";
}
#analyze($_SESSION);

#echo "before first exp call<hr>";
function sendcompressedcontent( $content )
{
    header( "Content-Encoding: gzip" );
    return gzencode( $content, 9 );
}
$cssclass = "tundra";

if(isset($_SESSION['userid'])){
// Get the data to populate the experiment name field:

	if($privval == 99){
			$expSQL = "SELECT expid, expname, descrip FROM agilent_experimentsdesc  ORDER BY expid";
		}else{
			$expSQL = "SELECT expid, expname,descrip FROM agilent_experimentsdesc WHERE ownerid=$userid ORDER BY expid";

	}
		//echo "$expSQL<br>";
		$expResult = $db->Execute($expSQL);//mysql_query($expSQL, $db);
		$firstchoice = 1;
		$expBuilderMenu = "<select name=\"selectedBuilderExperiment\" id=\"selectedBuilderExperiment\" onChange=\"updateArrayList()\">";
		$expBuilderMenu .= "<option label=\"\" value=\"-1\" >No Experiment Selected</option>  ";
		$location = $_SERVER['PHP_SELF'];
		//echo "$location<br>";
		$expMenu = "<select name=\"selectedExperiment\" onChange=\"updateexpdesc(this.value, '$location')\">";
		$arrayExpMenu = "<select name=\"selectedExperimentArray\">";
		$expDesc = "none chosen";
		if(isset($_GET['editid'])){
			$selected = $_GET['editid'];
		}else{
			$selected = "";
		}
		//echo "SELECTED IS $selected<br>";
		$countexp = 1;
		//while($row = mysql_fetch_array($expResult))

		while($row = $expResult->FetchRow())
		{
			//echo "$countexp,";
			if($selected == $row[0]){
				//echo "found $selected<br>";
				$chosen = "SELECTED";
			}else{

				$chosen = "";
			}
			$expname = $row[1];
			if($firstchoice == 1){
				
				$expMenu .= "<option label=\"$expname\" value=\"$row[0]\" $chosen>$expname</option>  ";
				$arrayExpMenu .=  "<option label=\"$expname\" value=\"$row[0]\" checked>$expname</option>  ";
				$expBuilderMenu .= "<option label=\"$expname\" value=\"$row[0]\" >$expname</option>  ";
				$expDesc = $row[2];
				$firstchoice = 0;
			}
			else{
				$expMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\" $chosen>$expname</option>  ";
				$arrayExpMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\">$expname</option>  ";
				$expBuilderMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\">$expname</option>  ";
			}
			$countexp++;
		}
		if($selected == -1){
			$chosen = "SELECTED";
		}else{
			$chosen = "";
		}
		$expMenu .= "<option value=\"-1\" $chosen>New Experiment</option></select>";
		$arrayExpMenu .= "</select>";
		$expBuilderMenu .= "</select>";

		// Create the Experiment Group Builder List
		if($privval == 99){
			$expSQL = "SELECT expgroupid, expgroupname, descrip FROM agilent_experimentgroupsdesc  ORDER BY expgroupid";
		}else{
			$expSQL = "SELECT expgroupid, expgroupname, descrip FROM agilent_experimentgroupsdesc WHERE ownerid=$privval ORDER BY expgroupid";

	}
	//echo "<br>$expSQL<br>";
		$expGroupBuilderMenu = "<select name=\"selectedBuilderExperiment\" id=\"selectedBuilderExperiment\" onChange=\"updateArrayList()\">";
		$expGroupBuilderMenu .= "<option label=\"\" value=\"-1\" >No Experiment Selected</option>  ";

		$expResult = $db->Execute($expSQL);//mysql_query($expSQL, $db);
		$firstchoice = 1;
		//while($row = mysql_fetch_array($expResult))
		while($row = $expResult->FetchRow())
		{
			$expgroupname = $row[1];
			if($firstchoice == 1){

				$expGroupBuilderMenu .= "<option label=\"$expname\" value=\"$row[0]\" >$expgroupname</option>  ";
				$expGroupDesc = $row[2];
				$firstchoice = 0;
			}
			else{

				$expGroupBuilderMenu .= "<option value=\"$row[0]\"  onSelect= \"return drawbox('otherExp',true)\">$expname</option>  ";
			}
		}
		$expGroupBuilderMenu .= "</select>";

}



// compress HTML
//ob_start( 'sendcompressedcontent' );
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>

<link rel="stylesheet" type="text/css" href="./css/tablelayout.css" title="layout" />
 <link rel="stylesheet" href="./lib/dsrte.css" type="text/css" />
<title>EDGE User Administration</title>
	<script type="text/javascript" src="./dojo-release-1.0.0/dojo/dojo.js"
                djConfig="parseOnLoad: true"></script>
	<script type="text/javascript" src="./javascript/newagilentarrayformcheck.js"
                djConfig="parseOnLoad: true, usePlainJson: true"></script>
<script type="text/javascript" src="./javascript/radiocheckboxvalidation.js"></script>

		<script type="text/javascript" src="./javascript/agilentexperiment-useradmin.js"
                djConfig="parseOnLoad: true, usePlainJson: true"></script>
	
	<script type="text/javascript" src="./javascript/expbuilderform.js"
                djConfig="parseOnLoad: true, usePlainJson: true"></script>
 <script src="./javascript/jquery-1.2.6.min.js"></script>
    <script type="text/javascript"><!--
        // keyboard shortcut keys for current language
        var ctrlb='b',ctrli='i',ctrlu='u';
        //-->
    </script>

<script type="text/javascript">

/* makeCollapsible - makes a list have collapsible sublists
 * 
 * listElement - the element representing the list to make collapsible
 */
function makeCollapsible(listElement){
	//alert("in makecollapsible");
  // removed list item bullets and the sapce they occupy
  listElement.style.listStyle='none';
  listElement.style.marginLeft='0';
  listElement.style.paddingLeft='0';

  // loop over all child elements of the list
  var child=listElement.firstChild;
  while (child!=null){

    // only process li elements (and not text elements)
    if (child.nodeType==1){

      // build a list of child ol and ul elements and hide them
      var list=new Array();
      var grandchild=child.firstChild;
      while (grandchild!=null){
        if (grandchild.tagName=='OL' || grandchild.tagName=='UL'){
          grandchild.style.display='none';
          list.push(grandchild);
        }
        grandchild=grandchild.nextSibling;
      }

      // add toggle buttons
	
      var node=document.createElement('img');
      node.setAttribute('src',"./images/go-next.png");
      node.setAttribute('class','collapsibleClosed');
      node.onclick=createToggleFunction(node,list);
      child.insertBefore(node,child.firstChild);
	
    }

    child=child.nextSibling;
  }

}

/* createToggleFunction - returns a function that toggles the sublist display
 * 
 * toggleElement  - the element representing the toggle gadget
 * sublistElement - an array of elements representing the sublists that should
 *                  be opened or closed when the toggle gadget is clicked
 */
function createToggleFunction(toggleElement,sublistElements){
	//alert('in create togglefunction');
  return function(){

    // toggle status of toggle gadget
    if (toggleElement.getAttribute('class')=='collapsibleClosed'){
      toggleElement.setAttribute('class','collapsibleOpen');
      toggleElement.setAttribute('src',"./images/go-down.png");
    }else{
      toggleElement.setAttribute('class','collapsibleClosed');
      toggleElement.setAttribute('src',"./images/go-next.png");
    }

    // toggle display of sublists
    for (var i=0;i<sublistElements.length;i++){
      sublistElements[i].style.display=
          (sublistElements[i].style.display=='block')?'none':'block';
    }

  }

}

</script>

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
			background:#A4CAF1;
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
   

        <style type="text/css">
                @import "./dojo-release-1.0.0/dojo/resources/dojo.css";
                 @import "./dojo-release-1.0.0/dijit/themes/<?php echo $cssclass; ?>/<?php echo $cssclass; ?>.css";
                @import "./dojo-release-1.0.0/dijit/demos/mail/mail.css";
        </style>

</head>
<?php
if(isset($_GET['neweditexp'])){
	$neweditexpisset = 1;
}else{
	$neweditexpisset = -1;
}
if(isset($_GET['expbuilder'])){
	$expbuilderisset = 1;
}else{
	$expbuilderisset = -1;
}

if(isset($_GET['rnasubmission'])){
	$rnasubmission = 1;
}else{
	$rnasubmission = -1;
}

if(isset($_GET['addgroupstoexps'])){
	$addgroupstoexpsisset = 1;
}else{
	$addgroupstoexpsisset = -1;
}
if(isset($_GET['buildusergroups'])){
	$buildusergroupsisset = 1;
}else{
	$buildusergroupsisset = -1;
}
if($neweditexpisset == 1){
	$loadscript = "loadScript()";
}elseif($expbuilderisset == 1){
	//alert('we're in experiment builder');
	//dojo.addOnLoad(init);
	$loadscript = "init(1);GetAllSourceItems(3);";
}elseif($rnasubmission == 1){
	$organismSQL = "SELECT id, organism FROM agilentarrays ORDER BY id ASC";
	$organismResult = $db->Execute($organismSQL);//mysql_query($organismSQL, $db);
	$numOrganisms = $organismResult->RecordCount();
	$loadscript = "initializeNewRNASampleForm($numOrganisms)";
}elseif($addgroupstoexpsisset == 1){
	$loadscript = "init(2);GetAllGroups(1);";
}elseif($buildusergroupsisset == 1){
	$loadscript = "init(3);GetAllUsers(1);";
}else{
	$loadscript = "";
}

$adminselected = "false";
$managegroupsselected="false";
$experimentssselected="false";
if(isset($_GET['admin'])){
	$adminselected = "true";
}
if(isset($_GET['groups'])){
	$managegroupsselected="true";
}
if(isset($_GET['experiments'])){
	$experimentssselected="true";

}

$makecollapsiblefunction = "";
if(isset($userid)){

	$makecollapsiblefunction = "makeCollapsible(document.getElementById('experiments'))";
}

?>

<body class="<?php echo $cssclass; ?>" onLoad="<?php echo $loadscript.";".$makecollapsiblefunction;?>">
<!-- basic preloader: -->
	<div id="loader"><div id="loaderInner"><h3>Loading EDGE<sup>3</sup> Experiment Builder ... </h3></div></div>
<div dojoType="dijit.layout.SplitContainer"
                orientation="horizontal"
                sizerWidth="7"
                activeSizing="true"
                style="border: 1px solid #bfbfbf; float: left; width: 100%; height: 100%;">
	<div dojoType="dijit.layout.AccordionContainer" sizeMin="20" sizeShare="20" style="float: left; margin-right: 0px; overflow: hidden">
		<div dojoType="dijit.layout.AccordionPane" title="Administration Menu" style="width: 400px" selected="<?php echo $adminselected; ?>">
			<ul>
				
				<li> <a href="agilentexperiment-useradmin.php?neweditexp=1&editid=-1&admin=1">New/Edit Experiment</a></li>
				<li> <a href="agilentexperiment-useradmin.php?rnasubmission=1&admin=1">New RNA Submission</a></li>
				<li> <a href="agilentexperiment-useradmin.php?arrayconstruction=1&admin=1&create=1">Array Construction</a></li>
				<li> <a href="agilentexperiment-useradmin.php?expbuilder=1&admin=1">Experiment Builder</a></li>
				<li> <a href="agilentexperiment-useradmin.php?userrnasamples=1&admin=1">Manage Your Submitted RNA Samples</a></li>
				<li> <a href="agilentexperiment-useradmin.php?arrayqueuestatus=1&admin=1">Manage Your Array Queue</a></li>
				
<?php
				if($privval == 99){
?>				
				<li> <a href="agilentexperiment-useradmin.php?rnasubmissionqueue=1&admin=1">RNA Submission Queue</a></li>
				<li><a href="agilentexperiment-useradmin.php?arraysubmit=1&admin=1">Submit Completed Array</a></li>
				<li><a href="agilentexperiment-useradmin.php?edittables=1&admin=1">EDGE<sup>3</sup> DB table edit</a></li>
				<li><a href="agilentexperiment-useradmin.php?arraydataupload=1&admin=1">EDGE<sup>3</sup> Feature Extraction Files Upload</a></li>
<?php
				}
?>
				
				
			</ul>
			 &nbsp;&nbsp;<b>Data Analysis</b>
			<ul><li> <a href="edge3.php" target="_blank">Analyze Your Data</a></li></ul>
		</div>
<?php
				#if($privval == 99){
?>

		<div dojoType="dijit.layout.AccordionPane" title="Manage Your User Groups" style="width: 400px" selected="<?php echo $managegroupsselected; ?>">
		<?php
			if(isset($userid)){
				require('usergroupsoptions.inc.php');	
			}else{
				echo "Please login.";
			}
		?>
		</div>
<?php
				#}
?>
		<div dojoType="dijit.layout.AccordionPane" title="Your Experiments" style="width: 400px" selected="<?php echo $experimentssselected; ?>">
<?php
if (isset($_SESSION['userid'])) {
	#echo "userid is set<br>";
	$filenum = rand(0, 25000);
	$file = "$IMAGESdir/experiments$filenum.json";

	//$command = "touch $file";
	//$fd = fopen($file, 'w');
	//rewind($fd);
	$filetext = "{ identifier:'name',\nlabel:'name',\nitems:[";
	//fwrite($fd, $filetext);
				// Get a list of experiments based on the owner's id....

#echo "before second exp call<hr>";
				//echo "your priv level: $privval<br>";
	if($privval == 99){
		$yourExpSQL = "SELECT expid, expname FROM agilent_experimentsdesc ORDER BY expid";
	}else{
		$yourExpSQL = "SELECT expid, expname FROM agilent_experimentsdesc WHERE ownerid=$userid ORDER BY expid";

	}
			#echo "$yourExpSQL<br>";
		$yourExpResult = $db->Execute($yourExpSQL);//mysql_query($yourExpSQL, $db);
		$count = 0;
		$htmllist = "";
		if($count == 0){
				$cl= "id=\"experiments\"";
			}
			$htmllist .= "<ul $cl>";
		//while($row = mysql_fetch_row($yourExpResult)){
		while($row = $yourExpResult->FetchRow()){
			$filetext = "";
			$expid = $row[0];
			$expname = $row[1];
			if($count != 0){
				//put a ',' first...
				$filetext = ",";
				//fwrite($fd, $filetext);
			}

			$filetext ="{name:'".$expname."',type:'experiment', expid:'".$expid."'";
			$htmllist .= "<li><a href=\"./agilentexperiment-useradmin.php?expidlist=$expid&experiments=1\">$expid : $expname</a>";
			//fwrite($fd, $filetext);
			//echo "$filetext<br>";
			$yourArraysCountSQL = "SELECT COUNT(arrayid) from agilent_experiments WHERE expid = $expid ORDER BY arrayid";
			#echo "$yourArraysCountSQL<br>";
			$yourArraysCountResult = $db->Execute($yourArraysCountSQL);//mysql_query($yourArraysCountSQL, $db);
			$arrayCount = $yourArraysCountResult->FetchRow();//mysql_fetch_row($yourArraysCountResult);
			if($arrayCount[0] > 0){
				$filetext = ", children:[";
				//fwrite($fd, $filetext);
				$yourArraysSQL = "SELECT e.arrayid from agilent_experiments as e WHERE e.expid = $expid ORDER BY e.arrayid ASC";
				#echo "$yourArraysSQL<BR>";
				$yourArraysResult = $db->Execute($yourArraysSQL);//mysql_query($yourArraysSQL, $db);
				$arraycount = 0;
				$htmllist .= "<ul>";
			//while($yourArrays = mysql_fetch_row($yourArraysResult)){
			while($yourArrays = $yourArraysResult->FetchRow()){
				//echo "$yourArraysSQL<br>";
				if($arraycount != 0){
					$filetext = ",";
					//fwrite($fd, $filetext);
				}
				$arraydescSQL = "SELECT arraydesc FROM agilent_arrayinfo WHERE arrayid = $yourArrays[0]";
				#echo "$arraydescSQL<br>";
				$arraydescResult = $db->Execute($arraydescSQL);//mysql_query($arraydescSQL, $db);
				$arraydescVal = $arraydescResult->FetchRow();//mysql_fetch_row($arraydescResult);
				$filetext = "{_reference:'Exp#".$expid."-".$yourArrays[0]." : ".$arraydescVal[0]."'}\n";
				$htmllist .= "<li><a href=\"./agilentexperiment-useradmin.php?arrayid=$yourArrays[0]&experiments=1\">$arraydescVal[0]</a></li>";
				//fwrite($fd, $filetext);
				$arraycount++;
				//echo "$filetext<BR>";
			}
			$filetext = "]}";
				$htmllist .= "</ul>";
			//fwrite($fd, $filetext);
			}else{
				
				$filetext = "}";
				//fwrite($fd,$filetext);

			}
			if($arrayCount > 0){
				$yourArraysSQL = "SELECT arrayid from agilent_experiments WHERE expid = $expid ORDER BY arrayid";
				#echo "$yourArraysSQL<BR>";
			$yourArraysResult = $db->Execute($yourArraysSQL);//mysql_query($yourArraysSQL, $db);
			$arraycount = 0;
			//while($yourArrays = mysql_fetch_row($yourArraysResult)){
			while($yourArrays = $yourArraysResult->FetchRow()){
				//echo "$yourArraysSQL<br>";
				//if($arraycount != 0){
					$filetext = ",";
					//fwrite($fd, $filetext);
				//}Check 'Edit Treatment Name?' checkbox to modify current treatment name.
				$arraydescSQL = "SELECT arrayid, arraydesc FROM agilent_arrayinfo WHERE arrayid = $yourArrays[0]";
				$arraydescResult = $db->Execute($arraydescSQL);
				#echo "$arraydescSQL<br>";
				//mysql_query($arraydescSQL, $db);
				$arraydescVal = $arraydescResult->FetchRow();//mysql_fetch_row($arraydescResult);
				$filetext = "{name:'Exp#".$expid."-".$yourArrays[0]." : ".$arraydescVal[1]."', type:'array', arrayid:'".$arraydescVal[0]."'}\n";
				//fwrite($fd, $filetext);
				$arraycount++;
				//echo "$filetext<BR>";
			}
				//$filetext .= "]}";

			//fwrite($fd, $filetext);

			$htmllist .= "</li>";

			}

			
			//fwrite($fd, $filetext);
			$count++;
			//echo "incrementing count<br>";
		}
	//fwrite($fd, $filetext);
		$htmllist .= "</ul>";
		//fwrite($fd, "]}");
		//fflush($fd);
		//rewind($fd);

		//fclose($fd);
}//$filenum = 22481;
?>
<!--
<div dojoType="dojo.data.ItemFileReadStore" jsId="experimentsStore"
		url="./IMAGES/experiments<?php echo $filenum; ?>.json"></div>
-->

<?php
if(isset($htmllist)){
	echo "$htmllist";
}
if (!isset($userid)) {
	echo "Please login.";
}else{
?>
<!--
<div dojoType="dijit.Tree" id="mytree" store="experimentsStore" query="{type:'experiment'}"
		labelAttr="name" typeAttr="type"></div>
-->
<?php

}

?>


		</div>
		<div dojoType="dijit.layout.AccordionPane" title="Your Array Queue">
<?php

	if(isset($userid)){
		$arraysql = "SELECT arrayname, cy3rnasample, cy5rnasample, status FROM  arrayqueue where ownerid = $userid ORDER BY status DESC";
		$sqlResult = $db->Execute($arraysql);//mysql_query($arraysql, $db);
		if(!$sqlResult){
			echo "<strong>Database Error getting arrays. SQL: $arraysql </strong><br>";
		}else{
			echo "<table><tr><td colspan='2'><b><u>Status Key</u></b></td></tr><tr><td><b>In Queue</b></td><td><img src=\"./images/inqueuestatus.png\" height=\"14px\" width=\"20px\"></td></tr>";
			echo "<tr><td><b>Completed</b></td><td><img src=\"./images/completedstatus.png\" height=\"14px\" width=\"20px\"></td></tr>";
			echo "<tr><td><b>Error in Hybing</b></td><td><img src=\"./images/errorstatus.png\" height=\"14px\" width=\"20px\"></td></tr>";
			echo "<table class=\"results\"><tr><td><b>Name</b></td><td><b>Status</b></td></tr>";
			//while($row=mysql_fetch_row($sqlResult)){
			while($row=$sqlResult->FetchRow()){
				switch($row[3]){
					case 0:
						$img = "<img src=\"./images/inqueuestatus.png\" height=\"14px\" width=\"20px\">";
						break;
					case 1: 
						$img = "<img src=\"./images/completedstatus.png\" height=\"14px\" width=\"20px\">";
						break;
					case 2:
						$img = "<img src=\"./images/errorstatus.png\" height=\"14px\" width=\"20px\">";
						break;
				}
				echo "<tr><td>$row[0]</td><td align=\"center\">$img</td></tr>";
	
			}
			echo "</table>";
	
		}
	}else{
		echo "You are not logged in...<br>";
	}
?>


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
				
?>


<div dojoType="dijit.Dialog" id="thirdDialog" href="" title="Edge<sup>3</sup> Array Information" style="width: 400px; height: 300px;"></div>

	</div>
<?php

	if(isset($_GET['neweditexp'])){
		$neweditexpisset = 1;
	}else{
		$neweditexpisset = -1;
	}
	if($neweditexpisset==1){
		// Generate editor instance
		//$dsrte = new dsRTE( 'dsrte' );
?>
                        <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="New/Edit Experiment"
				<?php
				//$expsubmitted = $_POST['expsubmitted'];
				
					//if($expsubmitted == "true"){
						echo "selected=\"true\"";
				//	}

				?>
                        ><!--<div id="exp" dojoType="dijit.layout.ContentPane"> -->
			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
 				
				

					require('./phpinc/agilent_new-edit_experiment.inc');
				
				}
			?>
				<!-- </div> -->
      			</div>
<?php
	}
#
if ($logged_in != 0) {
	//if($privval == 99){
	if(isset($_GET['createusergroup'])){
		$createusergroupisset = 1;
	}else{
		$createusergroupisset = -1;
	}
	if($createusergroupisset==1){
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Create User Group"
				selected=true
                        >
			<br><br><br>



			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/edge3groupcreation.inc.php');
				}
?>
			</div>
<?php
	} // end if($_GET['expbuilder'] ==1
}

if ($logged_in != 0) {
	//if($privval == 99){
	if(isset($_GET['buildusergroups'])){
		$buildusergroupexpsisset = 1;
	}else{
		$buildusergroupexpsisset = -1;
	}
	if($buildusergroupexpsisset==1){
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Create User Group"
				selected=true
                        >
			<br><br><br>



			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/usergroupbuilder.inc.php');
				}
?>
			</div>
<?php
	} // end if($_GET['expbuilder'] ==1
}

if ($logged_in != 0) {
	//if($privval == 99){
	if(isset($_GET['assignusergroupadmins'])){
		$assignusergroupadminsisset = 1;
	}else{
		$assignusergroupadminsisset = -1;
	}
	if($assignusergroupadminsisset==1){
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Assign Group Admins"
				selected=true
                        >
			<br><br><br>



			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/usergroupassignadmins.inc.php');
				}
?>
			</div>
<?php
	} // end if($_GET['expbuilder'] ==1
}
#
if ($logged_in != 0) {
	//if($privval == 99){
	if(isset($_GET['addgroupstoexps'])){
		$addgroupstoexpsisset = 1;
	}else{
		$addgroupstoexpsisset = -1;
	}
	if($addgroupstoexpsisset==1){
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Add Groups to Experiments"
				selected=true
                        >
			<br><br><br>



			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/addgroupstoexperiments.inc.php');
				}
?>
			</div>
<?php
	} // end if($_GET['expbuilder'] ==1
}
if ($logged_in != 0) {
	//if($privval == 99){
	if(isset($_GET['deleteusergroups'])){
		$deleteusergroupsisset = 1;
	}else{
		$deleteusergroupsisset = -1;
	}
	if($deleteusergroupsisset==1){
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Delete User Groups"
				selected=true
                        >
			<br><br><br>



			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/deleteusergroup.inc.php');
				}
?>
			</div>
<?php
	} // end if($_GET['expbuilder'] ==1
}
if ($logged_in != 0) {
	//if($privval == 99){
	if(isset($_GET['expbuilder'])){
		$expbuilderisset = 1;
	}else{
		$expbuilderisset = -1;
	}
	if($expbuilderisset==1){
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Experiment Builder"
				selected=true
                        >
			<br><br><br>



			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/agilentexperimentbuilder.inc');
				}
?>
			</div>
<?php
	} // end if($_GET['expbuilder'] ==1
?>
<!--
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Experiment Group Builder"

                        >
			<br><br><br>
				Work in progress....


			<?php

				//require('./phpinc/agilentexperimentgroupbuilder.inc');

?>
			</div>
-->
<?php

	if(isset($_GET['rnasubmission'])){
		$rnasubmissionisset = 1;
	}else{
		$rnasubmissionisset = -1;
	}
	if($rnasubmissionisset ==1){
		echo "rna submission is set. <hr>";
?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="New RNA Submission"
				<?php
					//if($_GET['rnasample'] == 1){
						echo "selected=\"true\"";
					//}
				?>
                        >	
 				<!--<div id="array" dojoType="dijit.layout.ContentPane"> -->

<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/newagilentrnasample-nonadmin.inc');
				}
?>
				<!-- </div> -->
      			</div>

<?php
	} // END if($_GET['rnasubmission']==1){

	if(isset($_GET['arraydataupload'])){
		$arraydataupload=1;
	}else{
		$arraydataupload=-1;
	}

	if($arraydataupload ==1){
?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Array Data Upload"
				<?php
					//if($_GET['rnasample'] == 1){
						echo "selected=\"true\"";
					//}
				?>
                        >	
 				<!--<div id="array" dojoType="dijit.layout.ContentPane"> -->

<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/uploadarrayfiles.inc.php');
				}
?>
				<!-- </div> -->
      			</div>

<?php
	} // END if($_GET['rnasubmission']==1){
	if(isset($_GET['arrayid'])){
		$arrayidisset = 1;
	}else{
		$arrayidisset = "";
	}
	if($arrayidisset!= ""){
?>
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Array Info/Edit"
				<?php
					//if($_GET['rnasample'] == 1){
						echo "selected=\"true\"";
					//}
				?>
                        >	
 				<!--<div id="array" dojoType="dijit.layout.ContentPane"> -->

<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/edgeeditarray.php');
				}
?>
				<!-- </div> -->
      			</div>

<?php
	} // END if($_GET['rnasubmission']==1){
	
	if(isset($_GET['arrayconstruction'])){

?>
	
			<div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="New Array Construction"
				<?php
					//if($_GET['rnasample'] == 1){
						echo "selected=\"true\"";
					//}
				?>
                        >	
 				<!--<div id="array" dojoType="dijit.layout.ContentPane"> -->

<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/edge3arrayconstruction.inc.php');
				}
?>
				<!-- </div> -->
      			</div>

<?php


}
if(isset($_GET['expidlist'])){
	// This is only available to administrators....
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="EDGE<sup>3</sup> Download Data Files"
				
				selected="true"
                        >
			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/downloaddatafiles.inc.php');
				}	
?>
			</div>
<?php
}

} // end of if($logged_in != 0){



if($privval == 99){
	if(isset($_GET['rnasubmissionqueue'])){
	// This is only available to administrators....
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="EDGE<sup>3</sup> RNA submission queue"
				
				selected="true"
                        >
			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/agilentadminrnasubmissionqueue.inc');
				}
?>
			</div>
<?php
	} // END if($_GET['rnasubmissionqueue']==1){
}
#deleternasample
	if(isset($_GET['userrnasamples'])){
	// This is only available to administrators....
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Manage RNA Samples"
				
				selected="true"
                        >
			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/userrnasamples.inc.php');
				}
?>
			</div>
<?php
	} // END if($_GET['rnasubmissionqueue']==1){
#arrayqueuestatus
if(isset($_GET['arrayqueuestatus'])){
	// This is only available to administrators....
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="EDGE<sup>3</sup> Array queue"
				
				selected="true"
                        >
			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/arrayqueuestatus.php');
				}
?>
			</div>
<?php
	} // END 
	if(isset($_GET['deleternasample'])){
	// This is only available to administrators....
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="Delete RNA Sample"
				
				selected="true"
                        >
			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/deleternasample.inc.php');
				}
?>
			</div>
<?php
	} // END if($_GET['rnasubmissionqueue']==1){
	if(isset($_GET['arraysubmit'])){
	// This is only available to administrators....
?>
			 <div dojoType="dijit.layout.ContentPane"
                                orientation="horizontal"
                                sizerWidth="5"
                                activeSizing="0"
                                title="EDGE<sup>3</sup> Submit Completed Array"
				
				selected="true"
                        >
			<?php
				if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
				}else{
				require('./phpinc/edgearraysubmit.inc.php');
				}	
?>
			</div>
<?php
	} // END if($_GET['arraysubmit']==1){

	if($privval == 99){
		if(isset($_GET['edittables'])){
		// This is only available to administrators....
	?>
				<div dojoType="dijit.layout.ContentPane"
					orientation="horizontal"
					sizerWidth="5"
					activeSizing="0"
					title="EDGE<sup>3</sup> Database table edit"
					
					selected="true"
				>
				<?php
					if(!isset($_SESSION['userid'])){
					die("You need to login to use this function.");
					}else{
					require('./phpinc/edittables.inc.php');
					}
	?>
				</div>
	<?php
		} // END if($_GET['arraysubmit']==1){
	}
 // END if($privval == 99){


?>
	</div>

</div>
</body></html>
