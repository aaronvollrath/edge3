<?php
/***
Location: /edge2
Description: This page is used to do the k-means and hierarchical clustering of the treatments.  Users are given the ability
		to choose individual arrays or specific chemicals.
POST:
FORM NAME: "query" ACTION: "clustering.php" METHOD: "post" ONSUBMIT: ""
	FUNCTION: Used to select the parameters for the clustering algorithms.
	ITEMS:  'clusterAlgo' <radio>, 'savedquery', 'tempquery', 'dataset' <radio>, 'number',
		'trxCluster' <radio>, 'orderoptions' <radio>, 'numberGroups', 'seloptions' <radio>,
		'chem[chemidnumber] <checkbox>, 'trx[arrayidnumber]' <checkbox>, 'colorScheme' <radio>,
		'rval', 'rvalmax', 'lval', 'lvalmin', 'submit'
GET: none
Files included or required: 'edge_db_connect2.php','header.inc','formcheck2.inc','edge_update_user_activity.inc','cloneinfotable.inc'
***/





require 'edge_db_connect2.php';
require './phpinc/edge3_db_connect.inc';
$db2 = mysql_connect("localhost", "root", "arod678cbc3",TRUE);
mysql_select_db("mygo", $db2);
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	//die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}


require "formcheck2.inc";
include 'edge_update_user_activity.inc';
function analyze(&$array) {
   foreach($array as $key=>$value) {
       if(is_array($value)) {
           echo "<li>Array:<blockquote>";
             analyze($value);
           echo "</blockquote>";
         } elseif(is_object($value)) {
           echo "<li>Object:<blockquote>";
             analyze($value);
           echo "</blockquote>";
         } else {
             echo "<li>[" . $key . "] " . $value;
       }
   }
}

function utime (){
$time = explode( " ", microtime());
$usec = (double)$time[0];
$sec = (double)$time[1];
return $sec + $usec;
}
$start = utime();

$algo = -1;

//echo "Order Option is: $orderoptions\n";

if (isset($_POST['submit'])) {
$algo = $_POST['clusterAlgo'];
}

$orderingMethod = -1;  // Used to determine how things are ordered when k-means or hierarchical w/ no clustering of trxs....

$clusterType = "Classification";


			if($orderoptions!=0){
				//
				//echo "orderoptions != 0...<br>";
				if($orderoptions == 1){
					// Order by individual trxs...

					$orderingMethod = 1;
				}
				else{//$orderoptions == 2...
					$orderingMethod = 2;
				}
			}
			else{
				$orderingMethod = 0;
			}




?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="../css/newlayout.css" title="layout" />
<title>EDGE^2</title>
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

<script type="text/javascript">
 function hideSelsRow1(toHide){

 if(toHide==1){
 	show1=document.getElementById("individualoption1");
	show2=document.getElementById("individualoption2");

	show1.style.display="";
	show2.style.display="";

	hide1=document.getElementById("groupoption1");
	hide3=document.getElementById("groupoption3");

	hide1.style.display="none";
	hide3.style.display="none";

	hide1=document.getElementById("groupoption4");
	hide3=document.getElementById("groupoption5");
	hide1.style.display="none";
	hide3.style.display="none";

	hide1=document.getElementById("groupoption6");
	hide1.style.display="none";
 }
 else if(toHide==0){
 	hide1=document.getElementById("individualoption1");
	hide2=document.getElementById("individualoption2");
	hide1.style.display="none";
  	hide2.style.display="none";

	show1=document.getElementById("groupoption1");
	show3=document.getElementById("groupoption3");
	show4=document.getElementById("groupoption4");
	show5=document.getElementById("groupoption5");
	show6=document.getElementById("groupoption6");

	show1.style.display="";
	show3.style.display="";
	show4.style.display="";
	show5.style.display="";
	show6.style.display="";
 }
 else if(toHide==2){

 	show1=document.getElementById("individualoption1");
	show2=document.getElementById("individualoption2");
	show1.style.display="";
   	show2.style.display="";
	show1=document.getElementById("groupoption1");
	show3=document.getElementById("groupoption3");
	show1.style.display="";
   	show3.style.display="";

	show4=document.getElementById("groupoption4");
	show5=document.getElementById("groupoption5");
	show6=document.getElementById("groupoption6");
	show4.style.display="";
	show5.style.display="";
	show6.style.display="";
 }

}

</script>
</head>
<body class="tundra">

	<div class="boxheader">
		<img src="../GIFs/EDGE2128x60.png" alt="Edge^2" align="left"></img>
		<img src="../GIFs/edgebanner.jpg" alt="environment" width="90" height="75" align="left"></img>
		<h2 class="bannerhead" align="bottom">Environment, Drugs and Gene Expression</h2>
	</div>

 <div class="boxmiddle">
 <?php
include 'questionmenu.inc';
?>

 <h3 class="contenthead"><?php echo $clusterType; ?></h3>

<div>

<?php
//analyze($savedvals);
$browser = get_browser();
$q = 0;
foreach ($browser as $name => $value) {
  if($q == 1){
	$bname = $value;
	break;
  }
 $q++;
}
//echo "browser = $bname<br>";
$browserval = 0;  // 0 = IE, 1 = non-IE
if(strcmp($bname, "IE")!=0){
// The browser check is used to set a flag for the clustering algorithm due to adobe svg viewers shortcomings w/ mozilla browsers...
	$browserval = 1;
}

if (isset($_POST['submit']) && $orderingMethod == 0 || $orderedSubmit == "true") { // if form has been submitted and it is not being ordered or orderedSubmit is true...
	//analyze($_POST);

		// CHECKING TO SEE IF THE FORM IS BEING SUBMITTED FROM THE ORDERING SCREEN.....
		if($orderedSubmit == "true"){
			if($_GET['savedquery'] == ""){  // If this is not a saved query
       //echo "This is not a saved query......";
				$thisNum = $_POST['querynum'];
			}else{
   // echo "This is a saved query... UPDATEING TEMP...<BR>";
				$thisNum = $_POST['tempquery'];

			}
			// Form is being submitted from the second data input screen....
			// Get the value for this query and then update the table accordingly...
			// Later an option will be given so that the user can save the query....
			//$querynum = $querynum;
			// Get the POST values and concatenate them....
			$query2text = "";
			$query2optstext = "";
			// Put the array pointer at the beginning of the $_POST array...
			reset ($_POST);
			while(list($key, $val) = each ($_POST)){
				if($key == "option"){
					$optionarray = $_POST['option'];
					foreach($optionarray as $key=>$value) {
						$query2optstext .= "$key=$value:";
					}
				}
				else{
					if($key != "submit"){
						// Probably don't want and don't need the submit value....
						$query2text .= "$key=$val:";
					}
				}
			}


			//echo "in query 2 submit section<br>";
			$sql = "UPDATE savedqueries SET query2 = \"$query2text\" WHERE query=$thisNum";
			$sqlResult = mysql_query($sql, $db);
			//echo "$sql <br>";
			$sql = "UPDATE savedqueries SET query2opts = \"$query2optstext\" WHERE query=$thisNum";
			$sqlResult = mysql_query($sql, $db);
			//echo "$sql <br>";



		}else{ // else ($orderedSubmit == "true" IS FALSE....
		
			if($_GET['savedquery'] == ""){  // If this is not a saved query
   				//echo "This is not a saved query......<BR>";
				// Need to get the max number in the savedqueries table and add one to that,
				// because that is the new number for this query....
				if($tempquery == ""){
					$sql = "SELECT MAX(query) FROM savedqueries";
					$sqlResult = mysql_query($sql, $db);
					$row = mysql_fetch_row($sqlResult);
					$querynum = $row[0];
					if($querynum == "NULL"){  // a boundary condition... the table is empty.  the other boundary is 10^10... not checking that...
						$querynum = 1;
					}else{
					// increment...
						$querynum += 1;
					}
					$tempquery = $querynum;
					$thisNum = $querynum;
				}else{
					$thisNum = $tempquery;
				}



			}else{
				//echo "This is a saved query... UPDATEING TEMP... and tempquery = $tempquery<BR>";
				$thisNum = $tempquery;

			}
			// There was no custom ordering or name changes w/ this....
			// Form is being submitted from the first data input screen....
			// Get the value for this query and then update the table accordingly...
			// Later an option will be given so that the user can save the query....


			// Get the POST values and concatenate them....
			$query1text = "";

			reset ($_POST);
			while(list($key, $val) = each ($_POST)){
				//echo "iterating...<br>";
				if($key != "submit"){
						// Probably don't want and don't need the submit value....
						$query1text .= "$key=$val:";
						//echo "$key=$val<br>";
					}


			}
				//echo "in query 1 submit section<br>";
				if($_GET['tempquery'] == ""){
					$tempquery = $_POST['tempquery'];
				}
			$sql = "SELECT query FROM savedqueries WHERE query = $tempquery";
			//echo "$sql<br>";
			$sqlResult = mysql_query($sql, $db);
			$row = mysql_fetch_row($sqlResult);
			if($row[0] != ""){
				$sql = "UPDATE savedqueries SET query1= \"$query1text\" WHERE query=$tempquery";
				$sqlResult = mysql_query($sql, $db);
				//echo "$sql<br>";
			}else{
				$sql = "INSERT savedqueries (query, userid, query1, querydate) VALUES($thisNum, $userid, \"$query1text\", NOW())";
				$sqlResult = mysql_query($sql, $db);
				//echo "$sql <br>";
			}
		}// END else ($orderedSubmit == "true" IS FALSE....


		if($dataset == ""){
			$dataset = 12;
		}
		//echo "Dataset is $dataset<br>";
		/*
		print "Posted variables: <br>";
		reset ($_POST);
		while(list($key, $val) = each ($_POST)){
			if($key == "option"){
				$optionarray = $_POST['option'];
				foreach($optionarray as $key=>$value) {
					echo "<li>[" . $key . "] " . $value;
				}
				echo "<br>";
				//analyze($_POST['option']);
			}else{
				print $key . " = " . $val . "<br>";
			}
		}*/


	/*
	//CLUSTERING FILES......
	$filenum = rand(0, 25000);
	$file = "/var/www/html/edge2/IMAGES/data$filenum.txt";
	$svgFile = "/var/www/html/edge2/IMAGES/svg$filenum.svg";
	$tableFile = "/var/www/html/edge2/IMAGES/table$filenum";
	$command = "touch $file";
	$str=exec($command);
	$command = "touch $svgFile";
	$str=exec($command);
	$command = "touch $tableFile";
	$str=exec($command);
	$colorscheme = $_POST['colorScheme'];
	$upperbound = $_POST['rval'];
	$upperboundmax = $_POST['rvalmax'];
	$lowerbound = $_POST['lval'];
	$lowerboundmin = $_POST['lvalmin'];
	$fd = fopen($file, 'w');

	rewind($fd);
	*/


	$trxidArray = array();
	$trxArray = array();
		$cloneidArray = array();// array of clone ids...
	$clonenameArray = array(); // array of clone names....
	$classArray = array(); // array of classes...
	$colorscheme = $_POST['colorScheme'];
	$upperbound = $_POST['rval'];

	$lowerbound = $_POST['lval'];

	//  The following variables are used for the files that are created....

	$filenum = rand(0, 25000);
	$file = "/var/www/html/edge2/IMAGES/data$filenum.csv";
	$log2file = "/var/www/html/edge2/IMAGES/log2data$filenum.csv";
	$trxnamesFile = "trxnames$filenum.txt";
	$svgFile = "/var/www/html/edge2/IMAGES/$filenum.svg";
	$tableFile = "/var/www/html/edge2/IMAGES/table$filenum";
	$command = "touch $file";
	$str=exec($command);
	$command = "touch $trxnamesFile";
	$str=exec($command);
	$command = "touch $svgFile";
	$str=exec($command);
	$command = "touch $tableFile";
	$str=exec($command);
	$colorscheme = $_POST['colorScheme'];
	//echo "Infogain = $infogain<br>";

	// Open the necessary files for writing....
	$fd = fopen($file, 'w');
	$fdlog2 = fopen($log2file, 'w');
	$fd2 = fopen($trxnamesFile, 'w');



	$arrayidArray = array();
	$arrayDescArray = array();
	if($orderedSubmit != "true"){


				// What chem were selected????
				$chemidarray = array();
				$chemSQL = "SELECT DISTINCT chemid FROM chem ORDER BY chemid";
				$chemResult = mysql_query($chemSQL, $db);

				while($row = mysql_fetch_row($chemResult)){

					// Check to see which boxes were checked...
					$chemid = $row[0];
					$thisVal = "chem$chemid";
					//echo "thisVal = $thisVal<br>";
					$post = $_POST[$thisVal];
					//echo "post = $post<br>";
					if($post != ""){
						$chemLookUpSQL = "SELECT chemical FROM chem where chemid = $post";
						$chemLookUpResult = mysql_query($chemLookUpSQL, $db);
						$chemrow = mysql_fetch_row($chemLookUpResult);
						//echo "<p>$chemrow[0] was chosen</p>";
						array_push($chemidarray, $post);
					}
				}


				// DETERMINE WHAT TREATMENTS WERE SELECTED....
				$trxidarray = array();
				$chemSQL = "SELECT DISTINCT sampleid FROM sampledata ORDER BY sampleid";
				$chemResult = mysql_query($chemSQL, $db);

				while($row = mysql_fetch_row($chemResult)){
				$sampleid = $row[0];
					// Check to see which boxes were checked...
					$sampleid = $row[0];
					$sampleid .=a;
					$thisVal = "trx$sampleid";

					$post = $_POST[$thisVal];
					$post = substr("$post", 0, -1);
					if($post != ""){
						$chemLookUpSQL = "SELECT chemical FROM chem where chemid = $post";
						//echo $chemLookUPSQL;
						$chemLookUpResult = mysql_query($chemLookUpSQL, $db);
						$chemrow = mysql_fetch_row($chemLookUpResult);
						array_push($trxidarray, $post);
					}
				}


				$chemArray = array();
				foreach($chemidarray as $chemid){
					$arrayStr = " chemid = $chemid ";
					array_push($chemArray, $arrayStr);
					$or = "OR";
					array_push($chemArray, $or);
				}

				foreach($trxidarray as $arrayid){
					$arrayStr = " sampleid = $arrayid ";
					array_push($chemArray, $arrayStr);
					$or = "OR";
					array_push($chemArray, $or);
				}
				// Pop the last or off...
				array_pop($chemArray);

				$chemidStr = "";
				foreach($chemArray as $item){
					$chemidStr .= $item;
				}



				$privval = $_SESSION['priv_level'];

				if($privval == ""){
					$priv = 1;
				}
				else{
					$priv = $privval;
				}


				//echo $chemidStr;
				// NOW NEED TO GET ALL THE TREATMENTS ASSOCIATED W/ THE CHOSEN CHEMICALS....
				// BASICALLY GETTING THE ARRAYIDS BECAUSE SAMPLEID = ARRAYID
				$arrayidSQL = "SELECT sampleid FROM sampledata where $chemidStr ORDER BY chemid, sampleid";
				//echo "$arrayidSQL<br>";
				$arrayidResult = mysql_query($arrayidSQL, $db);
				while($row = mysql_fetch_row($arrayidResult)){
					//echo "<p>Sample #$row[0] chosen</p>";
					if($priv != 99){
						$arraydescSQL = "SELECT arraydesc from array where arrayid = $row[0] AND (ownerid = 1 OR ownerid = $priv) ORDER BY arrayid";

					}
					else{
						$arraydescSQL = "SELECT arraydesc from array where arrayid = $row[0] ORDER BY arrayid";
					}
					//echo "$arraydescSQL<br>";
					$arraydescResult = mysql_query($arraydescSQL, $db);
					$arrayVal = mysql_fetch_row($arraydescResult);
					if($arrayVal != ""){
						//echo "ArrayVal != ''<br>";
						//echo "$row[0] \t $arrayVal[0]<br>";
						array_push($arrayidArray, $row[0]);
						array_push($arrayDescArray, $arrayVal[0]);
						$descrip = "$arrayVal[0]";
						$descrip .= "\n";
						//fwrite($fd, $descrip);

					}
				}

				$arrayidsql = array();
				$experimentcounter = 0;
				foreach($arrayidArray as $id){
					//$val = " arrayid = $id ";
					$val = " sampledata.sampleid = $id ";
					$idVal = "$id";
					$idVal .= "\n";
					//fwrite($fd, $idVal);
					array_push($arrayidsql, $val);
					$or = "OR";
					array_push($arrayidsql, $or);
					$experimentcounter++;
				}
				// Pop the last or off...
				array_pop($arrayidsql);
				$arrayidsqlstring = "";
				foreach($arrayidsql as $item){
					$arrayidsqlstring .= $item;
				}

				//echo "$arrayidsqlstring<br><hr>";
				$arrayidlist = str_replace("sampledata.sampleid", "arrayid", $arrayidsqlstring);
				//echo "<br> $arrayidlist<br><hr>";
				// Now need to deal w/ the possibility that this is a range of values on the induction and/or repression of expression...
					// 4 possibilities...  	1) both values for induction and repression ceilings entered
					//			2) both values for induction and repression ceilings are absent
					//			3) repression ceiling entered and induction ceiling not entered
					// 			4) induction ceiling entered and repression not entered
					$finalratioconstraint = "";
					if($upperboundmax != "" && $lowerboundmin != ""){
						// either or conditions first... then both entered....
;
						if($upperboundmax != "" && $lowerboundmin == ""){
							// Both rvalmax and lvalmin are blank....
							$finalratioconstraint = "(finalratio <= $lowerbound or finalratio >=
							$upperbound AND finalratio <= $upperboundmax)";
						}
						else if($upperboundmax == "" && $lowerboundmin != ""){
							$finalratioconstraint = "(finalratio <= $lowerbound AND finalratio >= $lowerboundmin 
							OR finalratio >= $upperbound)";
						}
						else{ // both of them are entered.....
							$finalratioconstraint = "(finalratio <= $lowerbound AND finalratio >= $lowerboundmin OR 
							finalratio >= $upperbound AND finalratio <= $upperboundmax)";
						}

					}else{
						// Both rvalmax and lvalmin are blank....
						$finalratioconstraint = "(finalratio <= $lowerbound or finalratio >= $upperbound)";

					}
				//echo "<br> dataset = $dataset <br><hr>";
				if($dataset == 1){
					$cloneiddistinctsql = "SELECT DISTINCT cloneid from hybrids where ($arrayidlist) ORDER BY cloneid";
					//$clonenamesql = "SELECT cloneid, SUBSTRING(annname,1,75) FROM annotations WHERE cloneid = $cloneid";
				}else{
					$cloneiddistinctsql = "SELECT DISTINCT cloneid from condensedhybrids where ($arrayidlist) ORDER BY cloneid";
					//$clonenamesql = "SELECT cloneid, SUBSTRING(annname,1,75) FROM condensedannotations WHERE cloneid = $cloneid";
				}
				//echo "$cloneiddistinctsql<br>";
				$cloneidarray = array();
				$cloneContainer = array();
				$cloneidResult = mysql_query($cloneiddistinctsql, $db);
				$cloneCount = 0;
				while($cloneRow = mysql_fetch_row($cloneidResult)){
					$cloneid=$cloneRow[0];

					// We've the arrayids.  Now we need to get the cloneids.
					$sql = "SELECT cloneid, SUBSTRING(annname,1,75) FROM condensedannotations WHERE cloneid = $cloneid";
					if($dataset == 1){
						$clonenamesql = "SELECT cloneid, SUBSTRING(annname,1,75) FROM annotations WHERE cloneid = $cloneid";
					}else{
						$clonenamesql = "SELECT cloneid, SUBSTRING(annname,1,75) FROM condensedannotations WHERE cloneid = $cloneid";
					}
					$sqlResult = mysql_query($sql, $db) or die("Query 2 Failed!!! <br> $sql");
					while(list($id,$name) = mysql_fetch_array($sqlResult)){
						array_push($cloneidArray, $id);
						//  Need to replace commas in names w/ underscores...
						$name = str_replace(",", "_", $name);
						$name = str_replace("\"", "", $name);
						$name = str_replace("<", "", $name);
						$name = str_replace(">", "", $name);
						$name = trim($name);
						array_push($clonenameArray, $name);
					}
				}
				/*  REMOVED THIS FROM THE
				while($cloneRow = mysql_fetch_row($cloneidResult)){
					$cloneid=$cloneRow[0];
					$clonestr = " cloneid = $cloneid ";
					array_push($cloneContainer, $cloneid);
					array_push($cloneidarray, $clonestr);
					$cloneor = "OR";
					array_push($cloneidarray, $cloneor);
					$cloneCount++;
				}
				*/
			$number = $_POST['number'];
			reset($trxidArray);
			$trxidArray = array();
			reset($trxArray);
			$trxArray = array();
			// NOW WE NEED TO GET THE ARRAY IDS IN ORDER OF CLASS/SAMPLEID....
			 $sql = "SELECT DISTINCT sampledata.sampleid, chem.class, class.name FROM chem, sampledata, class
			  WHERE ($arrayidsqlstring) AND chem.chemid = sampledata.chemid AND chem.class = class.classid ORDER BY chem.class, sampledata.sampleid ASC";
			/*
			$sql = "SELECT DISTINCT sampledata.sampleid, chem.class, class.name FROM chem, sampledata, class
			  WHERE ($arrayidsqlstring) AND chem.chemid = sampledata.chemid AND chem.class = class.classid ORDER BY sampledata.sampleid ASC";
			*/
			//  echo "$sql<br>";
			$result =  mysql_query($sql, $db) or die("Query $sql<br> Failed");
			while(list($id, $class, $classname) = mysql_fetch_array($result)){
				//echo "$id:$class:$classname<br>";
				// GET THE LIST OF TRX NAMES....
					if($priv != 99){
						$arraydescSQL = "SELECT arraydesc from array where arrayid = $id AND (ownerid = 1 OR ownerid = $priv) ORDER BY arrayid";

					}
					else{
						$arraydescSQL = "SELECT arraydesc from array where arrayid = $id ORDER BY arrayid";
					}
					$arraydescResult = mysql_query($arraydescSQL, $db) or die("ArrayDescSQL failed...");
					$arrayVal = mysql_fetch_row($arraydescResult);
					if($arrayVal != ""){
						//echo "ArrayVal != ''<br>";
						//echo "$id \t $arrayVal[0]<br>";
						array_push($trxidArray, $id);
						array_push($trxArray, $arrayVal[0]);
						$descrip = "$arrayVal[0]";
						$descrip .= "\n";
						fwrite($fd2, $descrip);
					}
					$classinfo = "$class:$classname";
					array_push($classArray,$classinfo);
			}

//  Close the trxdescription file....
				fclose($fd2);
			/// CREATING THE DATA FILE.....
			// Create the row of treatment names....
$counter = 0;
$item = "Clone#,Gene Name,";
fwrite($fd, $item);

$begin = 0;
foreach($trxArray as $trx){
	$item = "";
	if($begin != 0){
		$item = ",";
	}
	$item .= "$trxidArray[$begin]:$trx";
	fwrite($fd, $item);
	$begin++;
}
	$item = "\n";
	fwrite($fd, $item);

	$cloneCount = 0;
		$data = "";
		$log2data = "";
		foreach($cloneidArray as $cloneid){


			$name = $clonenameArray[$cloneCount];
			$item =  "$cloneid,$name,";
			$data .= $item;
			//echo "$cloneCount ==> $name<br>";
			//fwrite($fd, $item);
			$begin = 0;
			foreach($trxidArray as $id){
				$sql = "SELECT finalratio FROM condensedhybrids where arrayid = $id and cloneid = $cloneid";
				$sqlResult = mysql_query($sql, $db) or die("Query 2 Failed!!! <br> $sql");
				while(list($val) = mysql_fetch_array($sqlResult)){
					$item = "";
					$log2item = "";

					if($begin != 0){
						$item = ",";
						$log2item = ",";
					}
					$item .= "$val";
					$data .= $item;
					if($val < 0){
						$log2val = 1.0/abs($val);
					}
					$log2val = log($log2val, 2);
					$log2item .= "$log2val";
					$log2data .=$log2item;
					//fwrite($fd, $item);
					$begin++;
				}
			}
			$item = "\n";
			$data .= $item;
			$log2data .= $item;
			/*if($cloneCount%25 == 0){
				fwrite($fd, $data);
				$data = "";
			}*/
			$cloneCount++;
		}

		fwrite($fd, $data);
		fwrite($fdlog2, $log2data);
		//echo "Here's a link to the file in tab-separated format: <a href=\"../IMAGES/log2data$filenum.csv\" target=\"_blank\">Data File</a><br>";

	$begin = 0;
	$item = "CLASS,SPACER";
	fwrite($fd,$item);
	foreach($classArray as $classval){
		//echo "$classval<br>";
		$item = ",$classval";
		fwrite($fd, $item);
	}
	$item = "\n";
	fwrite($fd, $item);
	fclose($fd);
// END OF CREATING DATA FILE.....

?>

<table class="question">
			<thead>
			<tr>
			<th class="mainheader" colspan="4">Clustering Results</th>
			<th class="mainheader" colspan="1">SVG Options</th>
			<th class="mainheader">Save Query?</th>
			</tr>
			</thead>
			<tr class="question">
			<td class="questionparameter"><strong>Number of Arrays Returned:</strong></td>
			<td class="questionanswer"> <?php echo $arrayidCount; ?></td>
			<td class="questionparameter"><strong>Minimal Induction:</strong></td>
			<td class="questionanswer">
			<?php
					if($upperboundmax == ""){
						echo $upperbound;
					}
					else{
						echo "[$upperbound,$upperboundmax]";
					}
			?>
			</td>
			<td class="questionparameter">

			<a href="<?php echo "./Instructions/svginstructions.php"; ?>" onClick="return popup(this,'Instructions')">Instructions</a></td>




			</tr>
			<tr class="question">
			<td class="questionparameter"><strong>Number of genes:</strong></td>
			<td class="questionanswer"> <?php echo $cloneCount; ?></td>
			<td class="questionparameter"><strong>Minimal Repression:</strong></td>

			<td class="questionanswer">
			 <?php
					if($lowerboundmin == ""){
						 echo $lowerbound;
					}
					else{
						echo "[$lowerbound,$lowerboundmin]";
					}


			?></td>
			<td class="questionparameter">
			<a href="<?php echo "./IMAGES/$svgFile"; ?>" onClick="return popup(this,'SVG<?php echo $filenum; ?>')">View entire Heat Map</a>
			</td>
			<?php
			if($savedquery != ""){

					if($update == "true"){
			?>
					<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save as new query?</a></td>

			<?php
					}
			}
			?>
			</tr>
			</table>

<?php

				$w = $arrayidCount *20 + 500;
				$h = $cloneCount * 10 + 200;

				// If there's only one treatment, don't cluster by treatments!!!!!
				if($arrayidCount == 1 && $clusterAlgo == 1){
					$trxCluster = 0;
					$algo = 0;
					$number = 1;
				}

    				//echo "Trx Cluster = $trxCluster <br>";
				if($trxCluster != 0){
					// NOTES:  Replaced $trxCluster w/ 2 so that dendrogram is displayed at top and treatments on the bottom. 01MAY2004
					$trxCluster = 2;
				}



				$accuracy = "true";
				$image = "true";
				$upfold = $upperbound;
				$downfold = $lowerbound;
		$command = "java -mx512m TransposeData $filenum $file >> outfile.txt";
				echo "$command <br>";
				$str=passthru($command);
				$outputfile = "output$filenum";
			$infogain = $_POST['infogain'];
			//echo "Here's a link to the file in csv format for use in WEKA: <a href=\"../IMAGES/$outputfile\" target=\"_blank\">WEKA File</a><br></p>";
			// TEMPORARILY IN PLACE
			//$infogain = 20;
		$command = "java -mx1024m -jar WekaClassification2.jar $outputfile $accuracy $image $upfold $downfold $infogain $trxnamesFile >> outfile.txt";
				//echo "$command <br>";
				//$str=passthru($command);
				//$command = "rm -f output$filenum.csv";
				//echo "$command <br>";
				//$str=exec($command);
				$command = "mv output$filenum.csv /var/www/html/edge2/IMAGES/output$filenum.csv";
				//echo "$command <br>";
				$str=exec($command);
				$command = "cp output$filenum.svg /var/www/html/edge2/IMAGES/$filenum.svg";
				//echo "$command <br>";
				$str=exec($command);
				$command = "gzip --best ../IMAGES/$filenum.svg";
				//echo "$command <br>";
				//$str=exec($command);

				$command = "mv ./IMAGES/$filenum.svg.gz ./IMAGES/$filenum.svgz";
				//echo "$command <br>";
				//$str=exec($command);
				$w = 1024;
				$h = 768;
				//

?>
				<ul id="globalnav">
				<li><a href="#indiv" onclick="show_querydiv('querysection0',2); return false;" tabindex="0">Results Table</a></li>
				<li><a href="#indiv" onclick="show_querydiv('querysection1',2); return false;" tabindex="1">Heatmap</a></li>
				</ul>
				<br>
				<p>
				<div style="display: block;" id="querysection0" class="classifyscroll">
<?php
	$table = "output$filenum";

include($table);
?>
				</div>
				<div  style="display: none;" id="querysection1" class="classifyscroll">
					<embed src="<?php echo "./IMAGES/$filenum.svgz" ?>" width=<?php echo $w ?> height=<?php echo $h ?> name="heatmap" type="image/svg+xml" />
				</div>





				<?php
				//echo "<br>$filename.svg<br>";
				//echo $str;
				$end = utime(); $run = $end - $start;

				echo "<br><font size=\"1px\"><b>Query results returned in ";
				echo substr($run, 0, 5);
				echo " secs.</b></font>";
					//unlink($file);






//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%



	}
	else{
		/***********************************************************************************
		ORDERED SECTION...............
		***********************************************************************************/
		//analyze($_POST);
		// For each ordered arrayid, get the description....
		$trxCounter = 0;
		if($orderedIndividually == "true"){
			foreach($option as $opt){
				$val = $opt - 1;
				//echo "$opt\t$trxid[$val];<br>";
				$thisVal = "trxidorder$trxCounter";
				//echo "val = $_POST[$thisVal]";
				$orderedArray[$opt] = $_POST['trxidorder$trxCounter'];
				//$orderedArray[$opt] = $trxid[$val];
				$trxCounter++;
			}
		}
		else{ // Ordered by group.... $orderedIndividually == "false".....

			//echo "Ordered = false";
			$orderedGroupArray = array();
			$numberOfGroups = $numberGroups;
			//echo "<br>The number of groups is: $numberOfGroups<br>";
			for($i = 0; $i < $numberOfGroups; $i++){
				$orderedGroupArray[$i] = array();
				//echo "$orderedGroupArray[$i] test<br>";
			}
			// Create a two-dimensional array w/ the
			$trxCounter = 0;
			//echo "<br> options: <hr>";
			//analyze($option);
			foreach($option as $opt){
				$val = $opt - 1;
				$thisVal = "trxidorder$trxCounter";
				//echo "val = $_POST[$thisVal]";
				 //echo "This val = $thisVal<br>";
				//$orderedArray[$opt] = $_POST['trxidorder$trxCounter'];
				//array_push($orderedGroupArray[$val], $trxid[$trxCounter]);
				//echo "$orderedGroupArray[$val]<br>";
				array_push($orderedGroupArray[$val], $_POST[$thisVal]);
				$trxCounter++;
			}

			// What groups where to have blanks after them????
			// If they do, we need to put a -99 at the end of the respective array...
			//echo "before checking...<br>";
			for($i = 1; $i <= $numberOfGroups; $i++){
				//echo "checking....<br>";
				$groupVal = "group$i";
				$val = $i - 1;
				$groupChecked = $_POST[$groupVal];
				if($groupChecked != ""){
					// This group was checked to have a blank at the end...
					array_push($orderedGroupArray[$val], "-99");
				}
			}

			//analyze($orderedGroupArray);
			// Now place the orderedGroupArray into a one dimensional $orderedArray....
			$orderedArray = array();
			for($i = 0; $i<$numberOfGroups; $i++){
				foreach($orderedGroupArray[$i] as $item){
					array_push($orderedArray, $item);
				}
			}
		}
		//echo "The treatments in order: <br>";
		foreach($orderedArray as $order){
			// Now need to determine whether a custom name was entered.....
			if($order != -99){ // If this is not a blank....
				$customid = "customname$order";
				if($_POST[$customid] != ""){
					$newname = $_POST[$customid];
					array_push($arrayidArray, $newname);
					array_push($arrayDescArray, $newname);
					$descrip = "$newname";
				}
				else{
				$arraydescSQL = "SELECT arraydesc from array where arrayid = $order ORDER BY arrayid";
				//echo "$arraydescSQL <br>";
				$arraydescResult = mysql_query($arraydescSQL, $db);
				$arrayVal = mysql_fetch_row($arraydescResult);
				//echo "$row[0] \t $arrayVal[0]<br>";
				array_push($arrayidArray, $row[0]);
				array_push($arrayDescArray, $arrayVal[0]);
				$descrip = "$arrayVal[0]";
				}
			}
			else{
				$descrip = "BLANK";
			}


			$descrip .= "\n";
			fwrite($fd2, $descrip);

		}
				$arrayidsql = array();
				$experimentcounter = 0;
				foreach($orderedArray as $id){
					$val = " arrayid = $id ";
					$idVal = "$id";
					$idVal .= "\n";
					//fwrite($fd, $idVal);
					array_push($arrayidsql, $val);
					$or = "OR";
					array_push($arrayidsql, $or);
					$experimentcounter++;
				}
				// Pop the last or off...
				array_pop($arrayidsql);
				$arrayidsqlstring = "";
				foreach($arrayidsql as $item){
					$arrayidsqlstring .= $item;
				}
				//$lowerbound = -2;
				//$upperbound = 2;

				// Now need to deal w/ the possibility that this is a range of values on the induction and/or repression of expression...
				// 4 possibilities...  	1) both values for induction and repression ceilings entered
				//			2) both values for induction and repression ceilings are absent
				//			3) repression ceiling entered and induction ceiling not entered
				// 			4) induction ceiling entered and repression not entered
					$finalratioconstraint = "";
					if($upperboundmax != "" && $lowerboundmin != ""){
						// either or conditions first... then both entered....
;
						if($upperboundmax != "" && $lowerboundmin == ""){
							// Both rvalmax and lvalmin are blank....
							$finalratioconstraint = "(finalratio <= $lowerbound or finalratio >=
							$upperbound AND finalratio <= $upperboundmax)";
						}
						else if($upperboundmax == "" && $lowerboundmin != ""){
							$finalratioconstraint = "(finalratio <= $lowerbound AND finalratio >= $lowerboundmin 
							OR finalratio >= $upperbound)";
						}
						else{ // both of them are entered.....
							$finalratioconstraint = "(finalratio <= $lowerbound AND finalratio >= $lowerboundmin OR 
							finalratio >= $upperbound AND finalratio <= $upperboundmax)";
						}

					}else{
						// Both rvalmax and lvalmin are blank....
						$finalratioconstraint = "(finalratio <= $lowerbound or finalratio >= $upperbound)";

					}

/// CREATING THE DATA FILE.....
			// Create the row of treatment names....
			$counter = 0;
			$item = "Clone#,Gene Name,";
			fwrite($fd, $item);
			//echo "<br> $arrayidsqlstring <br>";
			if($dataset == 1){
				$cloneiddistinctsql = "SELECT DISTINCT cloneid from hybrids where ($arrayidsqlstring) ORDER BY cloneid";
			}
			else{
				$cloneiddistinctsql = "SELECT DISTINCT cloneid from condensedhybrids where ($arrayidsqlstring) ORDER BY cloneid";
			}
			//echo "$cloneiddistinctsql<br>";
			$cloneidarray = array();
			$cloneContainer = array();
			$cloneNameArray = array();
			$cloneidResult = mysql_query($cloneiddistinctsql, $db);
			$cloneCount = 0;
			while($cloneRow = mysql_fetch_row($cloneidResult)){
				$cloneid=$cloneRow[0];
				$clonestr = " cloneid = $cloneid ";
				array_push($cloneContainer, $cloneid);
				array_push($cloneidarray, $clonestr);
				//$cloneor = "OR";
				///array_push($cloneidarray, $cloneor);
				$cloneCount++;
				// We've the arrayids.  Now we need to get the cloneids.
				//$sql = "SELECT cloneid, SUBSTRING(annname,1,75) FROM condensedannotations WHERE cloneid = $cloneid";
				if($dataset == 1){
					$clonenamesql = "SELECT cloneid, SUBSTRING(annname,1,75) FROM annotations WHERE cloneid = $cloneid";
				}else{
					$clonenamesql = "SELECT cloneid, SUBSTRING(annname,1,75) FROM condensedannotations WHERE cloneid = $cloneid";
				}
				$sqlResult = mysql_query($clonenamesql, $db) or die("Query 2 Failed!!! <br> $clonenamesql");
				while(list($id,$name) = mysql_fetch_array($sqlResult)){
					array_push($cloneidArray, $id);
					//  Need to replace commas in names w/ underscores...
					$name = str_replace(",", "_", $name);
					$name = str_replace("\"", "", $name);
					$name = str_replace("<", "", $name);
					$name = str_replace(">", "", $name);
					$name = trim($name);
					array_push($clonenameArray, $name);
				}

			}

		// Getting the classes for these particular arrays
		reset($orderedArray);
		//analyze($orderedArray);
		$count = 0;
		reset($option);

		foreach($orderedArray as $sampleid){
			$sql = "SELECT DISTINCT sampledata.sampleid, chem.class, class.name FROM chem, sampledata, class
				WHERE (sampledata.sampleid = $sampleid) AND chem.chemid = sampledata.chemid AND chem.class = class.classid ORDER BY chem.class, 					sampledata.sampleid ASC";
			//echo "<br>$sql<br>";
			//echo "ordered array: $sampleid class: $option[$sampleid]<br>";
			$sqlResult = mysql_query($sql, $db) or die("Query 2 Failed!!! <br> $sql");
			list($thisid, $class, $classname) = mysql_fetch_array($sqlResult);
			$classinfo = "$class:$classname";
			array_push($classArray,$classinfo);
			$count++;
		}
		$begin = 0;
		reset($orderedArray);
		foreach($arrayDescArray as $trx){
			$item = "";
			if($begin != 0){
				$item = ",";
			}
			$item .= "$orderedArray[$begin]:$trx";
			fwrite($fd, $item);
			$begin++;
		}
			$item = "\n";
			fwrite($fd, $item);

			$cloneCount = 0;
				$data = "";
				$log2data = "";
				foreach($cloneContainer as $cloneid){

					$name = $clonenameArray[$cloneCount];
					$item =  "$cloneid,$name,";
					$data .= $item;
					//echo "$cloneCount ==> $name<br>";
					//fwrite($fd, $item);
					$begin = 0;
					foreach($orderedArray as $id){
						$sql = "SELECT finalratio FROM condensedhybrids where arrayid = $id and cloneid = $cloneid";
						$sqlResult = mysql_query($sql, $db) or die("Query 2 Failed!!! <br> $sql");
						while(list($val) = mysql_fetch_array($sqlResult)){
							$item = "";
							$log2item = "";
							if($begin != 0){
								$item = ",";
								$log2item = ",";
							}
							$item .= "$val";
							$data .= $item;
							if($val < 0){
								$log2val = 1.0/abs($val);
								$log2val = log($log2val)/log(2);
							}else{
								$log2val = log($val)/log(2);
							}
							$log2item .= "$log2val";
							$log2data .=$log2item;
							//fwrite($fd, $item);
							$begin++;
						}
					}
					$item = "\n";
					$data .= $item;
					$log2data .= $item;
					if($cloneCount%25 == 0){
						fwrite($fd, $data);
						fwrite($fdlog2, $log2data);
						$data = "";
						$log2data = "";
     					}
					$cloneCount++;
				}
				//echo "<hr> number of clones: $cloneCount <hr>";
				fwrite($fd, $data);
				fwrite($fdlog2, $log2data);
				//echo "Here's a link to the file in tab-separated format: <a href=\"../IMAGES/log2data$filenum.csv\" target=\"_blank\">Data File</a><br>";

	$begin = 0;
	$item = "CLASS,SPACER";
	fwrite($fd,$item);
	/*
	foreach($classArray as $classval){
		$item = ",$classval";
		fwrite($fd, $item);
	}*/
	// For each arrayid, need to list its class....
	reset($orderedArray);
	reset($option);
	foreach($orderedArray as $id){
		$aval = $id;
		$opt = $option[$id];
		$classval = "class$opt";
		$the_class = ",$_POST[$classval]";
		fwrite($fd, $the_class);
		//echo "$the_class<br>";
	}

	$item = "\n";
	fwrite($fd, $item);
	fclose($fd);
// END OF CREATING DATA FILE.....

			?>
			<table class="question">
			<thead>
			<tr>
			<th class="mainheader" colspan="4">Clustering Results</th>
			<th class="mainheader" colspan="1">SVG Options</th>
			<th class="mainheader">Save Query?</th>
			</tr>
			</thead>
			<tr class="question">
			<td class="questionparameter"><strong>Number of Arrays Returned:</strong></td>
			<td class="questionanswer"> <?php echo $arrayidCount; ?></td>
			<td class="questionparameter"><strong>Minimal Induction:</strong></td>
			<td class="questionanswer">
			<?php
					if($upperboundmax == ""){
						echo $upperbound;
					}
					else{
						echo "[$upperbound,$upperboundmax]";
					}


			?>
			</td>
			<td class="questionparameter">

			<a href="<?php echo "./Instructions/svginstructions.php"; ?>" onClick="return popup(this,'Instructions')">Instructions</a></td>
			<?php if($savedquery != ""){
				// Does this query have a name???
				$sql = "SELECT queryname FROM savedqueries WHERE query = $savedquery";
				//echo $sql;
				$sqlResult = mysql_query($sql, $db);
				$row = mysql_fetch_row($sqlResult);
				$name = $row[0];
				//echo "<br>name=$name<br>";
				$update = "true";
				if($name == "" || $name == "NULL"){
					$update = "false";
				}
				if($update == "true"){
			?>
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery&savedquery=$savedquery&submit=true&querytype=2";?>"
				target="<?php echo "_blank$randnum"; ?>">Update?</a></td>
			<?php
				}else{
			?>
					<td class="questionanswer">
				<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a>
				</td>
			<?php
				}
			}else{
   			if($update == "true"){
			?>
				<td class="questionanswer">
				<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a>
				</td>
			<?php
				}
				else{
			?>
				<td class="questionanswer">
					<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2s";?>"  target="<?php echo "_blank$randnum"; ?>">Save?</a>
				</td>
			<?php
				}
			}
			?>
			</tr>
			<tr class="question">
			<td class="questionparameter"><strong>Number of genes:</strong></td>
			<td class="questionanswer"> <?php echo $cloneCount; ?></td>
			<td class="questionparameter"><strong>Minimal Repression:</strong></td>
			<td class="questionanswer">
			 <?php
					if($lowerboundmin == ""){
						 echo $lowerbound;
					}
					else{
						echo "[$lowerbound,$lowerboundmin]";
					}


			?></td>
			<td class="questionparameter">
			<a href="<?php echo "./IMAGES/$filenum.svg"; ?>" onClick="return popup(this,'SVG')">View entire Heat Map</a>
			</td>
			<?php if($savedquery != "" && $update == "true"){
			?>
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank$randnum"; ?>">Save as new query?</a></td>
			<?php
			}
			?>
			</tr>
			<tr class="question">
			<?php if($algo == 0){
			?>

			<?php
			}
			else{
			?>
			<td class="questionparameter"><strong>Treatments Clustered:</strong></td>
			<td class="questionanswer">
			<?php
				if($trxCluster != 0){
					echo "Yes";
				}
				else{
					echo "No";
				}
			?>
			</td>
			<?php
			}
			if($_SESSION['priv_level'] >= 99){
   ?>
			<td class="questionparameter"><strong>Tabular format:</strong></td><td class="questionanswer"><?php echo "<a href=\"./tabledisplay.php?tableNum=$filenum\" target=\"_blank\">TABLE</a>"; ?></td>
			<?php
   }else{echo "<td></td>";}

			?>
			</tr>

			</table>
			<?php

				$w = $arrayidCount *20 + 500;
				$h = $cloneCount * 10 + 200;


				?>
				<?php

				$accuracy = "true";
				$image = "true";
				$upfold = $upperbound;
				$downfold = $lowerbound;
				$command = "java -mx512m TransposeData $filenum $file >> outfile.txt";
				//echo "$command <br>";
				$str=passthru($command);
				$outputfile = "output$filenum";
			$infogain = $_POST['infogain'];
			// TEMPORARILY IN PLACE
			//$infogain = 20;
//echo "Here's a link to the file in csv format for use in WEKA: <a href=\"./IMAGES/$outputfile\" target=\"_blank\">WEKA File</a><br>";
				$command = "java -mx1024m -jar WekaClassification2.jar $outputfile $accuracy $image $upfold $downfold $infogain $trxnamesFile >> outfile.txt";
				//echo "$command <br>";
				//$str=passthru($command);
				//$command = "rm -f output$filenum.csv";
				//echo "$command <br>";
				$str=exec($command);
				$command = "mv output$filenum.csv /var/www/html/edge2/IMAGES/output$filenum.csv";
				//echo "$command <br>";
				$str=exec($command);
				$command = "mv output$filenum.svg /var/www/html/edge2/IMAGES/$filenum.svg";
				//echo "$command <br>";
				$str=exec($command);
				$command = "gzip --best ./IMAGES/$filenum.svg";
				//echo "$command <br>";
				//$str=exec($command);

				$command = "mv ./IMAGES/$filenum.svg.gz ./IMAGES/$filenum.svgz";
				//echo "$command <br>";
				//$str=exec($command);
				$w = 1280;
				$h = 1024;
?>
<ul id="globalnav">
				<li><a href="#indiv" onclick="show_querydiv('querysection0',2); return false;" tabindex="0">Results Table</a></li>
				<li><a href="#indiv" onclick="show_querydiv('querysection1',2); return false;" tabindex="1">Heatmap</a></li>
				</ul>
				<br>
				<p>
				<div style="display: block;" id="querysection0" class="classifyscroll">
<?php
	$table = "output$filenum";

include($table);
?>
				</div>
				<div  style="display: none;" id="querysection1" class="classifyscroll">
					<embed src="<?php echo "./IMAGES/$filenum.svg" ?>" width=<?php echo $w ?> height=<?php echo $h ?> name="heatmap" type="image/svg+xml" />
				</div>
<?php

	}


}
else if(isset($_POST['submit']) && $orderingMethod >= 1) {
		if($savedquery != ""){
			// NEED TO UPDATE THE TEMP QUERY......
			reset ($_POST);
			while(list($key, $val) = each ($_POST)){
				if($key != "submit"){
					$query1text .= "$key=$val:";
					//echo "$key=$val<br>";
				}
			}
			$sql = "UPDATE savedqueries SET query1= \"$query1text\" WHERE query=$tempquery";
			//$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($tempquery, $userid, \"$query1text\", NOW())";
			//echo "$sql <br>";
			$sqlResult = mysql_query($sql, $db);

			//echo "This is a saved query...<br>";
			// Need to populate the current query screen....
			$sql = "SELECT queryname, query2, query2opts FROM savedqueries WHERE query = $savedquery AND userid = $userid";
			//echo "$sql<br>";
			$sqlResult = mysql_query($sql, $db);
			$row = mysql_fetch_row($sqlResult);
			$queryname = $row[0];
			$query2 = $row[1];
			$query2opts = $row[2];
			//echo "$query2<br>";
			//echo "$query2opts<br>";
			// NOW need to explode $query2 into an array, the separator is :
			$savedvals = explode(":", $query2);
			// pop the last value of due to final :
			array_pop($savedvals);
			//analyze($savedvals);
			// GET THE OPTIONS...
			$savedoptions = explode(":", $query2opts);

			array_pop($savedoptions);
			//analyze($savedoptions);

		}else{
			// Form is being submitted from the first data input screen....
			// BUT THIS IS USING THE *CUSTOM* ORDERING AND NAMING OF THE ARRAYS....
			// Get the value for this query and then update the table accordingly...
			// Later an option will be given so that the user can save the query....

			// Need to get the max number in the savedqueries table and add one to that,
			// because that is the new number for this query....
			$sql = "SELECT MAX(query) FROM savedqueries";
			$chemResult = mysql_query($sql, $db);
			$row = mysql_fetch_row($chemResult);
			$querynum = $row[0];
			if($querynum == "NULL"){  // a boundary condition... the table is empty.  the other boundary is 10^10... not checking that...
				$querynum = 1;
			}else{
			// increment...
				$querynum += 1;
			}
			// Get the POST values and concatenate them....
			$query1text = "";

			reset ($_POST);
			while(list($key, $val) = each ($_POST)){
				if($key != "submit"){
					$query1text .= "$key=$val:";
					//echo "$key=$val<br>";
				}
			}
			//echo "in query 1 submit section<br>";
			$sql = "INSERT savedqueries (query, userid, query1,querydate) VALUES($querynum, $userid, \"$query1text\", NOW())";
			//echo "$sql <br>";
			$sqlResult = mysql_query($sql, $db);
		}
	//  Get the arrays select, lay them out in a table and then, for each, create
	// What chem were selected????
	$chemidarray = array();
	$chemSQL = "SELECT DISTINCT chemid FROM chem ORDER BY chemid";
	$chemResult = mysql_query($chemSQL, $db);
	//echo "<p>$chemSQL</p>";

	while($row = mysql_fetch_row($chemResult)){

		// Check to see which boxes were checked...
		$chemid = $row[0];
		$thisVal = "chem$chemid";
		$post = $_POST[$thisVal];
		if($post != ""){
			$chemLookUpSQL = "SELECT chemical FROM chem where chemid = $post";
			$chemLookUpResult = mysql_query($chemLookUpSQL, $db);
			$chemrow = mysql_fetch_row($chemLookUpResult);
			//echo "<p>$chemrow[0] was chosen</p>";
			array_push($chemidarray, $post);
		}
	}


	$trxidarray = array();
	$chemSQL = "SELECT DISTINCT sampleid FROM sampledata ORDER BY sampleid";
	$chemResult = mysql_query($chemSQL, $db);

	while($row = mysql_fetch_row($chemResult)){

		// Check to see which boxes were checked...
		$sampleid = $row[0];
		$sampleid .=a;
		$thisVal = "trx$sampleid";
		$post = $_POST[$thisVal];
		$post = substr("$post", 0, -1);
		if($post != ""){
			$chemLookUpSQL = "SELECT chemical FROM chem where chemid = $post ORDER BY chemid";
			echo $chemLookUPSQL;
			$chemLookUpResult = mysql_query($chemLookUpSQL, $db);
			$chemrow = mysql_fetch_row($chemLookUpResult);
			array_push($trxidarray, $post);
		}
	}


		$chemArray = array();
		foreach($chemidarray as $chemid){
			$arrayStr = " chemid = $chemid ";
			array_push($chemArray, $arrayStr);
			$or = "OR";
			array_push($chemArray, $or);
		}

		foreach($trxidarray as $arrayid){
			$arrayStr = " sampleid = $arrayid ";
			array_push($chemArray, $arrayStr);
			$or = "OR";
			array_push($chemArray, $or);
		}
		// Pop the last or off...
		array_pop($chemArray);

		$chemidStr = "";
		foreach($chemArray as $item){
			$chemidStr .= $item;
		}

		$arrayidArray = array();
		$arrayDescArray = array();

		$privval = $_SESSION['priv_level'];

	if($privval == ""){
		$priv = 1;
	}
	else{
		$priv = $privval;
	}


	//echo $chemidStr;
	// NOW NEED TO GET ALL THE TREATMENTS ASSOCIATED W/ THE CHOSEN CHEMICALS....
	// BASICALLY GETTING THE ARRAYIDS BECAUSE SAMPLEID = ARRAYID
	$arrayidSQL = "SELECT sampleid FROM sampledata where $chemidStr ORDER BY chemid, sampleid";
	//echo "$arrayidSQL<br>";
	$arrayidResult = mysql_query($arrayidSQL, $db);
	while($row = mysql_fetch_row($arrayidResult)){
		//echo "<p>Sample #$row[0] chosen</p>";
		if($priv != 99){
			$arraydescSQL = "SELECT arraydesc from array where arrayid = $row[0] AND (ownerid = 1 OR ownerid = $priv) ORDER BY arrayid";

		}
		else{
			$arraydescSQL = "SELECT arraydesc from array where arrayid = $row[0] ORDER BY arrayid";
		}
	//echo $arraydescSQL;
		$arraydescResult = mysql_query($arraydescSQL, $db);
		$arrayVal = mysql_fetch_row($arraydescResult);
		if($arrayVal != ""){
			//echo "ArrayVal != ''<br>";
			array_push($arrayidArray, $row[0]);
			array_push($arrayDescArray, $arrayVal[0]);
			$descrip = "$arrayVal[0]";
			$descrip .= "\n";
			//fwrite($fd, $descrip);
		}
	}
$length = count($arrayidArray);
//analyze($_POST);
?>




<form name="order" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>" onsubmit="return checkOrder(<?php echo $length; ?>)">
<?php
echo "<table class=\"results\">";
?>
<thead>
<tr>
<th class="mainheader" colspan="2">Selected Treatments</th>
<th class="mainheader" >Assign Class</th>
<th class="mainheader">Custom Treatment Name</th>
<th width="10"></th>
<th class="mainheader">Class Designations</th>
</tr>
</thead>
<?php





if($length > 20){
?>
<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td></td>
<td><input type="reset" value="Reset Form"</td>
<td></td>
<td></td>

</tr>

<?php
}
$counter = 0;
//print "Posted variables: <br>";
  // How many classes are we dealing w/??? Assigned to $numberClasses

	$arrayidsql = array();
	foreach($arrayidArray as $id){
		//$val = " arrayid = $id ";
		$val = " sampledata.sampleid = $id ";
		$idVal = "$id";
		$idVal .= "\n";
		//fwrite($fd, $idVal);
		array_push($arrayidsql, $val);
		$or = "OR";
		array_push($arrayidsql, $or);
		$experimentcounter++;
	}
	// Pop the last or off...
	array_pop($arrayidsql);
	$arrayidsqlstring = "";
	foreach($arrayidsql as $item){
		$arrayidsqlstring .= $item;
	}
	//echo "<br>$arrayidsqlstring<br><hr>";
	$sql = "SELECT DISTINCT class.name FROM chem, sampledata, class
			  WHERE ($arrayidsqlstring) AND chem.chemid = sampledata.chemid AND chem.class = class.classid ORDER BY chem.class ASC";

	$classResult = mysql_query($sql, $db);
	$classCount = 1;
	$classArray = array();
	while($aval = mysql_fetch_row($classResult)){
		$classVal = "$classCount:$aval[0]";
		//echo "$classVal<br>";
		array_push($classArray, $classVal);
		$classCount++;
	}
	$classCount--;
	//echo "<br>The number of classes: $classCount<br><hr>";
	$numberClasses = $classCount;

	reset ($_POST);
	echo "<input name=\"orderedSubmit\" type=\"hidden\" value=\"true\">\n";
	echo "<input name=\"numberOfArrays\" type=\"hidden\" value=\"$length\">\n";
	echo "<input name=\"numberClasses\" type=\"hidden\" value=\"$numberClasses\">\n";
	
 	echo "<input name=\"querynum\" type=\"hidden\" value=\"$querynum\">\n";

	while(list($key, $val) = each ($_POST)){
		if($key != "submit"){
		echo "<input name=\"$key\" type=\"hidden\" value=\"$val\">\n";
		}
	}

	$colorscheme = $_POST['colorScheme'];
	$upperbound = $_POST['rval'];
	$lowerbound = $_POST['lval'];
	$clusterAlgo = $_POST['clusterAlgo'];
// If the ordering method is by individual treatments....
	if($orderingMethod == 1){
	echo "<input name=\"orderedIndividually\" type=\"hidden\" value=\"true\">\n";
		for($i = 0; $i < $length; $i++){
			$val = $i + 1;
			// Create the selection menus.....
			$selectMenu .= "<option value=\"$val\">$val</option>\r";
		}
		foreach($arrayidArray as $idVal){
		echo "<tr>";
		$val = $counter + 1;
		echo "<td class=\"questionparameter\">$idVal</td><td class=\"results\">$arrayDescArray[$counter]</td>
			<input name=\"trxidorder$counter\" type=\"hidden\" value=\"$idVal\">
				<td class=\"questionanswer\"><select name='option[$counter]'>
				<option value=\"$val\" selected>$val</option>\r;
					$selectMenu
			</select></td>";
		echo "</tr>";
		$counter++;
		}
		$counter=0;
	}
	else{
	
	$innercolor = array("lightsteelblue", "DarkKhaki", "salmon", "DarkSeaGreen", "Gainsboro",
			"yellow", "Fuchsia", "LawnGreen", "LightSlateGray", "Olive", "Indigo",
			"PaleVioletRed", "skyblue", "PeachPuff", "Orange", "GoldenRod", "oldlace",
			"pink", "RosyBrown", "green","lightsteelblue","YellowGreen", "salmon",
			"Turquoise", "Thistle", "Peru", "WhiteSmoke");


		echo "<input name=\"orderedIndividually\" type=\"hidden\" value=\"false\">\n";
		// We've got the length and the number of groups....
		if($length <= $numberClasses){
			$numberClasses = $length;
		}
		if($_GET['savedquery'] == "" || $savedquery == ""){
			$isSavedQuery = "";
		
		}else{
				$isSavedQuery = "true";
			}

		foreach($arrayidArray as $idVal){
			echo "<tr>";
			$val = $counter + 1;
			/*
			//#################################################33
			*/
			// Do we've any name changes here??????
			$savedName = "";
			if($isSavedQuery != ""){
				foreach($savedvals as $nameval){
					$temp = explode("=", $nameval);
					$customname = "customname$idVal";
					if($temp[0]== $customname){
						$savedName = $temp[1];
						break;
					}
				}
			}
			if($savedName == ""){
				$savedName = $arrayDescArray[$counter];

			}
			$selectMenu = "";
			// #################################################
			if($_GET['savedquery'] == "" || $savedquery == ""){
				//echo "This is not a saved query...";

				for($i = 0; $i < $numberGroups; $i++){
					$aval = $i + 1;
					// Create the selection menus.....
					$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$aval</option>\r";
				}
			}
			else{
				$isSavedQuery = "true";
				// What group is it in.... REBUILDING SELECT MENU....
				// Is this an id we had before
				$selectMenu = "";
				$selectValue = "";
				foreach($savedoptions as $arrayval){
					$temp = explode("=", $arrayval);
					//echo "temp[0] = $temp[0] idVal = $idVal temp[1]=$temp[1]<br>";
					if($temp[0] == $idVal){
						$optVal = $temp[1];
							//echo "valC = $valC and optVal = $optVal numberGroups = $numberGroups<br>";
							$selected = "selected";
							if($optVal > $numberGroups){
								// If the number of groups is updated and is less than the previous value, set to the number of groups...
								$optVal = $numberGroups;
							}
							$selectMenu = "<option value=\"$optVal\" $selected style=\"background-color: $innercolor[$optVal];\">$optVal</option>\r";
							$selectValue = $optVal;
						break;
					}

				}
				for($i = 0; $i < $numberGroups; $i++){
						$aval = $i + 1;
						//if($selectValue != $val){
						// Create the selection menus.....
						//echo "inloop...";
						$selectMenu .= "<option value=\"$aval\" style=\"background-color: $innercolor[$i];\">$aval</option>\r";
						//}
					}

			}
			

			// Do we've any groups selected and what group is this trx in???????????
			$savedgroup = "";
			if($isSavedQuery != ""){
				foreach($savedvals as $nameval){
					$temp = explode("=", $nameval);
					$customname = "customname$idVal";
					if($temp[0]== $customname){
						$savedName = $temp[1];
						break;
					}
				}

			}
			if($savedName == ""){
				$savedName = $arrayDescArray[$counter];

			}

			echo "<td class=\"questionparameter\">$idVal</td><td class=\"results\">$savedName</td>
			<input name=\"trxidorder$counter\" type=\"hidden\" value=\"$idVal\">

					<td class=\"questionanswer\"><select name='option[$idVal]' >
						$selectMenu
				</select></td>
				<td class=\"results\">
			<input name=\"customname$idVal\" type=\"text\" value=\"$savedName\" size=\"25\" align=\"right\"></td><td width=\"10\"></td>";
			//echo "val = $val  NumberOfGroups=$numberGroups<br>";
			// How many classes are there????
			if($val <= $numberGroups){

				$group = $val;
				$isChecked = "";
				if($isSavedQuery != ""){
					foreach($savedvals as $nameval){
						$temp = explode("=", $nameval);
						$customclasslabel = "class$group";
						if($temp[0] == $customclasslabel){
							$classlabel = $temp[1];
							break;
						}
					}
				}

				// Need to get the classes for the selected arrays.....
				$currentClass = $val - 1;
				//if($val <= $numberClasses){
				if($isSavedQuery != ""){
					echo "<td class=\"questionanswer\"><input style=\"background-color: $innercolor[$currentClass];\" type=\"text\" name=\"class$group\" value=\"$classlabel\"></td>";
				}else{
				if($val <= $numberClasses){
					echo "<td class=\"questionanswer\"><input style=\"background-color: $innercolor[$currentClass];\" type=\"text\" name=\"class$group\" value=\"$classArray[$currentClass]\"></td>";
				}else{
					echo "<td class=\"questionanswer\"><input style=\"background-color: $innercolor[$currentClass];\" type=\"text\" name=\"class$group\" value=\"$val Class $val\"></td>";
				}
				}
			}
			else{
				echo "<td></td>";
			}
			if($isSavedQuery != ""){
				$selectMenu = "";
			}
			echo "</tr>";
			$counter++;
		}
		$counter=0;


	}

?>
<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td></td>
<td><input type="reset" value="Reset Form"</td>
<td></td>

</tr>
<?php
echo "</table>";
echo "</form>";
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
}
else{// GETTING THE VALUES TO POPULATE THE SELECTIONS.....
// THIS ONE IS FOR THE PARTICULAR TREATMENTS....

$privval = $_SESSION['priv_level'];

if($privval == ""){
	$priv = 1;
}
else{
	$priv = $privval;
}
// This is the sql required to get the list of chemicals...
//$chemSQL = "SELECT DISTINCT chemid, chemical, trx_type FROM chem ORDER BY chemid";
if($priv != 99){
$chemSQL = "SELECT DISTINCT s.chemid, c.chemical, c.trx_type FROM array AS a, sampledata AS s, chem AS c
		WHERE (a.ownerid = $priv OR a.ownerid = 1) AND s.sampleid = a.arrayid AND c.chemid = s.chemid
		ORDER BY c.chemical";
//print $chemSQL;
}
else{
 	$chemSQL = "SELECT DISTINCT chemid, chemical, trx_type FROM chem ORDER BY chemical";
}

//#############ARE WE DEALING W/ A SAVED QUERY????############################
	if($_GET['savedquery'] != ""){

		// CREATE A TEMP QUERY TO STORE THE UPDATED SAVED QUERY!!!!!!
		//############################################################
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// Need to get the max number in the savedqueries table and add one to that,
			// because that is the new number for this query....s
			$sql = "SELECT MAX(query) FROM savedqueries";
			$sqlResult = mysql_query($sql, $db);
			$row = mysql_fetch_row($sqlResult);
			//if($tempquery != ""){
				$tempquery = $row[0];
			//}
			if($tempquery == "NULL"){  // a boundary condition... the table is empty.  the other boundary is 10^10... not checking that...
				$tempquery = 1;
			}else{
			// increment...
				$tempquery += 1;
			}
			// Get the POST values and concatenate them....
			$query1text = "";

			reset ($_POST);
			while(list($key, $val) = each ($_POST)){

				if($key != "submit"){
						// Probably don't want and don't need the submit value....
						$query1text .= "$key=$val:";
						//echo "$key=$val<br>";
					}


			}
			//echo "in query 1 TEMPORARY submit section<br>";
			$sql = "INSERT savedqueries (query, userid, query1, querydate) VALUES($tempquery, $userid, \"$query1text\", NOW())";
			$sqlResult = mysql_query($sql, $db);
			//echo "$sql <br>";
			//echo "<br>#################END TEMP QUERY##################################<br>";
		//############################################################
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

		// Since $savedquery is the number of the query, we need to get the information for that query....
		// Specifically, is this only a one page or a two pager?
		$savedquery = $_GET['savedquery'];
		$sql = "SELECT queryname, query1, query2opts FROM savedqueries WHERE query = $savedquery AND userid = $userid";
		//echo "$sql<br>";
		$sqlResult = mysql_query($sql, $db);
		$row = mysql_fetch_row($sqlResult);
		$queryname = $row[0];
		$query1 = $row[1];
		$query2opts = $row[2];
		if($query2opts != "NULL"){
			// this is a two pagers...
			$is2pager = 1;
		}else{
			$is2pager = 0;
		}

		// NOW need to explode $query1 into an array, the separator is :
		$vals = explode(":", $query1);
		// pop the last value of due to final :
		array_pop($vals);
		//analyze($vals);
		// This is used to store the chem numbers....
		$savedvals = array();
		$savedchemvals = array();
		$savedtrxvals = array();
		foreach($vals as $val){
			$temp = explode("=", $val);
			$findme  = 'chem';
			$pos = strpos($temp[0], $findme);

			// Note our use of ===.  Simply == would not work as expected
			// because the position of 'a' was the 0th (first) character.
			if ($pos === false) {
				$savedvals[$temp[0]]=$temp[1];
				// Now need to check to see if we're dealing w/ an individual treatment....
				$findme  = 'trx';
				$pos = strpos($temp[0], $findme);
				if($pos === false){
					$savedvals[$temp[0]]=$temp[1];
				}
				else{
					// check for exception....
					if($temp[0] == "trxCluster"){
						$savedvals[$temp[0]]=$temp[1];
					}
					else{
						array_push($savedtrxvals, $temp[1]);
					}

				}
			} else {
				if($temp[0] == "colorScheme"){
					$savedvals[$temp[0]]=$temp[1];
				}
				else{
					array_push($savedchemvals, $temp[1]);
				}
			}
			//echo "$temp[0]=>$temp[1]";
			//array_push($savedvals, $temp[0]=>$temp[1]);
		}

		//echo "<br>here's savedvals<br>";
		//analyze($savedvals);
		//echo "<br>here's savedchemvals<br>";
		//analyze($savedchemvals);
		//echo "<br>here's savedtrxvals<br>";

		//array_push($savedtrxvals, -1);
		//analyze($savedtrxvals);
	}else{
		// This is not a saved query.....
		// because that is the new number for this query....
			$sql = "SELECT MAX(query) FROM savedqueries";
			$sqlResult = mysql_query($sql, $db);
			$row = mysql_fetch_row($sqlResult);
			//if($tempquery != ""){
				$tempquery = $row[0];
			//}
			if($tempquery == "NULL"){  // a boundary condition... the table is empty.  the other boundary is 10^10... not checking that...
				$tempquery = 1;
			}else{
			// increment...
				$tempquery += 1;
			}

	}


$chemResult = mysql_query($chemSQL, $db);
//echo $chemSQL;
$chemCount = 1;
$envCount = 1;
$chemCount2 = 1;
$chemCount3 = 1;
$chemCount4 = 1;
$chemCount5 = 1;
$chemCount6 = 1;
$chemCount7 = 1;
$chemCount8 = 1;

while(list($chemid, $chemical, $trx_type) = mysql_fetch_array($chemResult))
{
	$checked = "";
	if($_GET['savedquery'] != ""){
		foreach($savedchemvals as $chemval){
			if($chemval == $chemid){
				$checked = "checked";
			}
		}
	}
   // $chemMenu .= "<option value=\"$chemid\">$chemical</option>\r";
	if($trx_type == 0){

		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		/*
		if($chemCount%3 == 0){

			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuChem .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuChem .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font size=1>$chemical</font><a 							href=\"$row[0]\" 												target=\"_blank\"><font size=1>CTD</a></font><br></td></tr>";
				}

			$chemCount=0;
			$chemCount++;
			}
		}
		else
		*/
		if($chemCount%2 == 0){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuChem .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td></tr>";
				}
				else{
					$chemMenuChem .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a href=\"$row[0]\" 						target=\"_blank\"><font size=1>CTD</font></a></td></tr>";
				}
			$chemCount++;
			}
		}
		else{
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuChem .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuChem .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount++;
			}
		}

	}
	else if($trx_type == 1){ // $trx_type != 0 and it's an environmental condition....
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuEnv .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuEnv .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}
	        }
	}

	else if($trx_type == 2){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		if($chemCount2%3 == 0){

			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuRats .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuRats .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}
			$chemCount2=0;
			$chemCount2++;
			}
		}
		else if($chemCount2%2 == 0){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuRats .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuRats .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a href=\"$row[0]\" 						target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount2++;
			}
		}
		else if($chemCount2%2 == 1){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuRats .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuRats .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font<a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount2++;
			}
		}
	}

	else if($trx_type == 3){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		if($chemCount3%2 == 0){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuPhyStates .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuPhyStates .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><a href=\"$row[0]\" 										target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}

			$chemCount3=0;
			$chemCount3++;
			}
		}
		else{
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuPhyStates .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuPhyStates .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount3++;
			}
		}
	}

	else if($trx_type == 4){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		if($chemCount4%2 == 0){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuMutantMice .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuMutantMice .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><a href=\"$row[0]\" 										target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}

			$chemCount4=0;
			$chemCount4++;
			}
		}
		else{
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuMutantMice .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuMutantMice .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount4++;
			}
		}
	}
	else if($trx_type == 5){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuTransgenicMice .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuTransgenicMice .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><a href=\"$row[0]\" 										target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}
	        }
	}

	else if($trx_type == 6){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		if($chemCount6%2 == 0){
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuCellLines .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuCellLines .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><a href=\"$row[0]\" 										target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}

			$chemCount6=0;
			$chemCount6++;
			}
		}
		else{
			while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuCellLines .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font></td>";
				}
				else{
					$chemMenuCellLines .= "<tr><td><input type=\"checkbox\" name=\"chem$chemid\" 						value=\"$chemid\" align=\"left\"$checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a></td>";
				}
			$chemCount6++;
			}
		}
	}

	else if($trx_type == 7){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuTumors .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuTumors .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font size=1>$chemical</font><a 						href=\"$row[0]\" target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}
	        }
	}

	else if($trx_type == 8){
		// Fetch the chemURL associated with this chem... display only if exists
		$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid";                                  			$chemurlResult = mysql_query($chemurlSQL, $db);

		while($row = mysql_fetch_row($chemurlResult)){
	                       if(strcmp($row[0],"NULL")==0){
					$chemMenuMixtures .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><br></td></tr>";
				}
				else{
					$chemMenuMixtures .= "<td><input type=\"checkbox\" name=\"chem$chemid\" 							value=\"$chemid\" align=\"right\" $checked><font 									size=1>$chemical</font><a href=\"$row[0]\" 										target=\"_blank\"><font size=1>CTD</font></a><br></td></tr>";
				}
	        }

	}
}




// Need to create an array to store the divs based on class.....
$classSQL = "SELECT COUNT(DISTINCT class) FROM chemclass";
$classResult = mysql_query($classSQL, $db);
$row = mysql_fetch_row($classResult);
$numDivs = $row[0];
$divVal = $numDivs - 1;
$divArray = array();


$chemIDArray = array();
$classIDArray = array();
$classSQL = "SELECT chemid, class FROM chemclass ORDER BY class";
$classResult = mysql_query($classSQL, $db);
while($row = mysql_fetch_row($classResult)){
	$thisChemID = $row[0];
	array_push($chemIDArray, $thisChemID);
	$thisClassID = $row[1];
	array_push($classIDArray, $thisClassID);
}

// For a person without admin priviledges, dont show the 'drinkwater' tab... hence dont show class with classid=99
if($priv != 99){
$classSQL = "SELECT DISTINCT(class.classid), class.name FROM class,chemclass WHERE chemclass.class = class.classid AND  class.classid!=99 ORDER BY class.name ASC";
}
else{
$classSQL = "SELECT DISTINCT(class.classid), class.name FROM class,chemclass WHERE chemclass.class = class.classid ORDER BY class.name ASC";
}

$classResult = mysql_query($classSQL, $db);
$maxClassID = 0;
$i = 0;
$uniqueClassArray = array();  // This array stores the classes returned from the above query (in alphabetical order)....

while(list($classid, $name) = mysql_fetch_array($classResult)){

	$uniqueClassArray[$i] = $classid;
	if($i == 0){
		//$divArray[$i][0] = "<div style=\"display: block;\" id=\"section$i\">"; // dont show chem for default opt
		$divArray[$i][0] = "<div style=\"display: none;\" id=\"section$i\">";
	}
	else{
		$divArray[$i][0] = "<div style=\"display: none;\" id=\"section$i\">";
	}

	$chemDivList .= "<li><a href=\"#indiv\" onclick=\"show_div('section$i',$divVal); return false;\" tabindex=\"$i\"><font size=2>$name</font></a></li>";
	$i++;

}

// The following variable is used to keep track of what div we're at....
$i = 0;
foreach($uniqueClassArray as $thisClassID){
	$acounter = 1;
	// Get all of the chemids associated w/ the current class...
	$sql = "SELECT chemid FROM chemclass WHERE class = $thisClassID";
	//echo "$sql <br>";
	$result = mysql_query($sql, $db);
	// ITERATE THROUGH EACH CHEMICAL ID BELONGING TO THIS CLASS.....
		while($row = mysql_fetch_row($result)){
			// GET all entries from sampledata that correspond to this chemid....
			// Order by arrayid
			$id = $row[0];


				// GET THE ARRAYS NOT ASSIGNED TO ANY EXPERIMENTS..................
				if($priv != 99){
					$chemSQL = "SELECT s.sampleid, s.treatment, s.chemid, a.arraydesc, c.trx_type  FROM sampledata AS s, chem as c, array AS a LEFT JOIN experiments AS e ON s.sampleid = e.arrayid WHERE
					(a.ownerid = $priv OR a.ownerid = 1) AND c.chemid = s.chemid AND s.chemid = $id AND a.arrayid = s.sampleid AND e.arrayid IS NULL ORDER BY a.arraydesc";
				//print $chemSQL;
				}
				else{
		$chemSQL = "SELECT s.sampleid, s.treatment, s.chemid, a.arraydesc, c.trx_type  FROM sampledata AS s, chem as c, array AS a LEFT JOIN experiments AS e ON s.sampleid = e.arrayid WHERE
									c.chemid = s.chemid AND s.chemid = $id AND a.arrayid = s.sampleid AND e.arrayid IS NULL ORDER BY a.arraydesc";

				}
					$chemResult = mysql_query($chemSQL, $db);
					


					//echo "<br>$chemSQL<br>";
					$arraycount = 1;
					$oldtreat = "";
					$counter = 0;
					while(list($trxid1, $treat, $chemid1, $trxdesc1, $type) = mysql_fetch_array($chemResult)){
						$counter++;

					$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$chemid1";
					//print $chemurlSQL;
					$chemurlResult = mysql_query($chemurlSQL, $db);
	
					if($acounter == 1){
						// need to set the first treatment type...
						$oldtreat = $treat;
						//*** Get the info links associated with this chem from chemURL
						//$link = mysql_fetch_array($chemurlResult);
					    while($row = mysql_fetch_row($chemurlResult)){
						if(strcmp($row[0],"NULL")==0){
						$divArray[$i][$acounter] = "<br><fieldset><legend><strong>$treat</strong> 					       </legend>";
						}
						else{
						$divArray[$i][$acounter] = "<fieldset><legend><strong>$treat</strong> 					       <a href=\"$row[0]\" target=\"_blank\"><strong>CTD</strong></a></legend>";
						}
					    }
						$acounter++;
					}

					if($oldtreat != $treat){
						$oldtreat = $treat;
						//*** Get the info links associated with this chem from chemURL
						$divArray[$i][$acounter] = "</fieldset>";
						$acounter++;
						while($row = mysql_fetch_row($chemurlResult)){
						if(strcmp($row[0],"NULL")==0){
						$divArray[$i][$acounter] = "<br><fieldset><legend><strong>$treat</strong> 					       </legend>";
						}
						else{
						$divArray[$i][$acounter] = "<fieldset><legend><strong>$treat</strong>   	<a href=\"$row[0]\" target=\"_blank\"><strong>CTD</strong></a></legend>";
						}
						}
						$acounter++;
					}
					$checked = "";
					$trxid1 .= a;
					if($_GET['savedquery'] != ""){
						foreach($savedtrxvals as $trxval){
							if($trxval == $trxid1){
								$checked = "checked";
							}
						}
					}
					$trxMenu = "$trxdesc1  <input type=\"checkbox\" name=\"trx$trxid1\" value=\"$trxid1\" $checked>  ";
					// The following conditional is put in place, because the treatment descriptions for TCDD optimization and Thioacetamide are too long...
					if($thisClassID != 19 && $thisClassID != 15){
						if($arraycount%4 == 0){
							$trxMenu .="<br>";
						}
					}
					else{
						if($arraycount%2 == 0){
							$trxMenu .="<br>";
						}
					}
					$divArray[$i][$acounter] = $trxMenu;
					$acounter++;
					$trxMenu = "";
					$arraycount++;
				}

				if($arraycount == 1){
					// There are no arrays that are not assigned to an experiment (ie. all arrays assigned to an exeriment...
					$allassigned = 1;
				}
				else{
					$allassigned = 0;
				}
				// Check to see if there are experiments...
				if($priv != 99){
				//echo "<br>THE CHEMICAL ID IS: $id<br>";
				$sql = "SELECT DISTINCT(e.arrayid), a.arraydesc, ed.expname FROM experiments AS e, experimentsdesc AS ed, array AS a
							WHERE e.arrayid = a.arrayid AND (a.ownerid = $priv OR a.ownerid = 1) AND e.chemid = $id AND ed.expid = e.expid ORDER BY ed.expname";
				}
				else{
				//echo "<br>THE CHEMICAL ID IS: $id<br>";
					$sql = "SELECT DISTINCT(e.arrayid), a.arraydesc, ed.expname FROM experiments AS e, experimentsdesc AS ed, array AS a WHERE e.arrayid = a.arrayid AND e.chemid = $id AND ed.expid = e.expid ORDER BY ed.expname";
				}
				$expResult = mysql_query($sql, $db);


				// ITERATE THROUGH EACH EXPERIMENT...... BASED ON THE ARRAY DESCRIPTION....
				$oldexpname = "";
				while(list($trxid1, $arraydesc, $expname) = mysql_fetch_array($expResult)){
				//echo "<br>$sql<br>";
				if($oldexpname == ""){
				// need to set the first treatment type...
					if($allassigned == 1){
					// If all arrays are assigned to an experiment, need to close the previous chemical and
					// create a new fieldset w/ legen of s.treatment for this particular chemical
					$sql = "SELECT s.treatment FROM sampledata AS s WHERE s.chemid = $id";
					$aResult = mysql_query($sql, $db);
					$row = mysql_fetch_row($aResult);
					$divArray[$i][$acounter] = "</fieldset>";
					$acounter++;

					$chemurlSQL = "SELECT url1 from chemURL WHERE chemid=$id";
					$chemurlResult = mysql_query($chemurlSQL, $db);


					while($row1 = mysql_fetch_row($chemurlResult)){
						if(strcmp($row1[0],"NULL")==0){
						$divArray[$i][$acounter] = "<fieldset><legend><strong>$row[0]</strong></legend>";
						}
						else{
						$divArray[$i][$acounter] = "<fieldset><legend><strong>$row[0]</strong><a href=\"$row1[0]\" target=\"_blank\"><strong>CTD</strong></a></legend>";
						}
					}

					$acounter++;
					$allassigned = 0;
					}
				$oldexpname = $expname;
	$divArray[$i][$acounter] = "<fieldset><legend><strong><font color=\"gray\">$expname</font></strong></legend>";
				$acounter++;
				}
						if($oldexpname != $expname){
							$oldexpname = $expname;
							$divArray[$i][$acounter] = "</fieldset>";
							$acounter++;
							$divArray[$i][$acounter] = "<fieldset><legend><strong><font color=\"gray\">$expname</font></strong></legend>";
							$acounter++;
						}
						$checked = "";
						$trxid1 .= a;
						if($_GET['savedquery'] != ""){
							foreach($savedtrxvals as $trxval){
								if($trxval == $trxid1){
									$checked = "checked";
								}
							}
						}

						$trxMenu = "$arraydesc  <input type=\"checkbox\" name=\"trx$trxid1\" value=\"$trxid1\" $checked>  ";
						// The following conditional is put in place, because the treatment descriptions for TCDD optimization and Thioacetamide are too long...
						if($thisClassID != 19 && $thisClassID != 15){
							if($arraycount%4 == 0){
								$trxMenu .="<br>";
							}
						}
						else{
							if($arraycount%2 == 0){
								$trxMenu .="<br>";
							}
						}
						$divArray[$i][$acounter] = $trxMenu;
						$acounter++;
						$trxMenu = "";
						$arraycount++;
					}

				$noclose = 0;

				if($oldexpname != ""){
						$divArray[$i][$acounter] = "</fieldset>";  // NEED TO CLOSE THE LAST FIELDSET FOR THE CURRENT experiment
						$acounter++;
						$noclose = 1;
				}
				$thisCount = $arraycount - 1;
				if($oldexpname == ""){
					//$divArray[$i][$acounter] = "</fieldset>";  // NEED TO CLOSE THE LAST FIELDSET FOR THE CURRENT CHEMICAL
					//$acounter++;
					$nofieldset = 1;
				}


		}


		if($nofieldset != 1){
			//$divArray[$i][$acounter] = "</fieldset>";  // NEED TO CLOSE THE LAST FIELDSET FOR THE CURRENT CLASS DIV
		}

	$acounter++;
	$divArray[$i][$acounter] = "</div>";
	$i++; // increment the divArray counter....

}


?>
<p class="styletext">
<form name="query" method="post" onsubmit="return checkClassificationForm()" action="<?php  $_SERVER['PHP_SELF'] ?>">

<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2">Query Parameters</th>
<th class="mainheader" ><a href="<?php echo "./Instructions/clustering.php"; ?>"  onclick="return popup(this,'Instructions')"><font size="0">Instructions?</font></a></th>
</tr>
<tr>
<td  class="questionanswer" colspan="2"><strong>Data Options:</strong></td>
<td  class="questionanswer"><strong>Your Query Options:</strong></td>
</tr>
<tr>
<td class="questionparameter" ><!--<strong>Clustering Method:</strong>--></td>
<td class="results">
</td>
<td align="top" class="results" rowspan="2">
<ul id="globalnav">
	<li><a href="#indiv" onclick="show_querydiv('querysection0',2); return false;" tabindex="0">Recent Queries</a></li>
	<li><a href="#indiv" onclick="show_querydiv('querysection1',2); return false;" tabindex="1">Saved Queries</a></li>
</ul>
<br>
<br>
<p>
<div style="display: block;" id="querysection0" class="scroll">
<?php
	if($userid != ""){
	// GET THE THREE MOST RECENT QUERIES.....
	$sql = "SELECT query FROM savedqueries WHERE userid = $userid AND (queryname IS NULL AND (query1 IS NOT NULL AND query1 != \"\")) AND querytype != 1 ORDER BY querydate DESC LIMIT 3";
	$sqlResult = mysql_query($sql, $db);
	$recentCount=1;
	while($row = mysql_fetch_row($sqlResult)){
		echo "<a href=\"./classification.php?savedquery=$row[0]\">Unsaved #$recentCount</a><br>";
		$recentCount++;
	}
	}else{
		echo "<b>Create a login in order <br>to use the save queries feature!</b>";
	}
?>
</div>
<div  style="display: none;" id="querysection1" class="scroll">
<?php
	if($userid !=""){// GET THEIR SAVED QUERIES.....
	$sql = "SELECT query, queryname FROM savedqueries WHERE userid = $userid AND queryname IS NOT NULL AND querytype != 1 ORDER BY querydate DESC";
	$sqlResult = mysql_query($sql, $db);
	while($row = mysql_fetch_row($sqlResult)){
		echo "<a href=\"./classification.php?savedquery=$row[0]\">$row[1]</a><br>";
	}
	}else{
			echo "<b>Create a login in order <br>to use the save queries feature!</b>";
	}

?>
</div>
<br>

</p>
</td>
</tr>
<?php
	// IF THIS IS A SAVED QUERY, WE'VE GOT TO HAVE A VALUE FOR THIS..
	echo "<input name=\"savedquery\" type=\"hidden\" value=\"$savedquery\">\n";
	// IF A TEMP query's involved, gotta have that....
	echo "<input name=\"tempquery\" type=\"hidden\" value=\"$tempquery\">\n";


	if($priv <=99){
?>

	<tr>
<td class="questionparameter" ><strong>Data Options:</strong></td>
<td class="results">

<input type="radio" name="dataset" value="0" checked><strong><font color="red">Condensed</font></strong><br>
</td>

</tr>
<?php
	}

	else{
?>
		<tr>
<td class="questionparameter" ><strong>Data Options:</strong></td>
<td class="results">
Using All Clones
</td>
<?php
	}
?>

<?php
	// what dataset is checked???
	if($_GET['savedquery'] != ""){
		$kval = $savedvals['numberGroups'];

	}
	else{
		$kval = 4;
	}
?>
<tr id="kmeansoption">
<input type="hidden" name="orderoptions" value="2"></input>
<td class="questionparameter"><strong>Number of Defined Classes:</strong></td>
<td class="results">
<input name="numberGroups" type="text" value="<?php echo $kval; ?>" size="5" align="right">
</td>
<td class="results"><br></td>
</tr>
<?php
// Seperate the arrays by experiment ID

$exparray = array();

$sql = "SELECT DISTINCT(expid) FROM agilent_experiments ORDER BY expid";
$result = mysql_query($sql, $db);
$row = mysql_fetch_row($result);

$numexps = $row[0];


$sql = "SELECT expid, arrayid FROM agilent_experiments ORDER BY expid";

$result = mysql_query($sql, $db);

$currentexp = -1;
$currentarraycount = 0;
$firstloop = -1;
$notinSQL = " ";
while($row = mysql_fetch_row($result)){
	$expid = $row[0];
	$arrayid = $row[1];

	if($currentexp < $expid){
		// close the Titlepane... on last experiment...
		if($currentexp != -1){
		$exparray[$expid][$currentarrayid] ="</div></td></tr>";
		echo "</tr></table></div></td></tr>";
		$notinSQL .= " AND ";
		}
		$currentarraycount = 0;
		$currentexp = $expid;
		$expdescSQL = "SELECT expname FROM agilent_experimentsdesc WHERE expid = $expid";
		$expdescResult = mysql_query($expdescSQL, $db);

		$expdescVal = mysql_fetch_row($expdescResult);
		$expdescVal = $expdescVal[0];
		//echo "descVal = $expdescVal<br>";
		$exparray[$currentexp][$currentarraycount] = "<tr id='groupoption3'><td colspan='3'><div dojoType='dijit.TitlePane' title='$expdescVal' open='false'>";
		echo "<tr><td colspan='3'><div dojoType='dijit.TitlePane' title='$expdescVal' open='false'><table><tr>";
		$arraySQL = "SELECT arraydesc FROM agilent_arrayinfo WHERE arrayid = $arrayid";
		$notinSQL .= " arrayid != $arrayid ";
		$arrayResult = mysql_query($arraySQL, $db);
		$arrayResultRow=mysql_fetch_row($arrayResult);
		$arraydesc = $arrayResultRow[0];
		if($_GET['savedquery'] != ""){
			// What array needs to be checked?
			if(array_search($arrayid, $savedarrayvals) > -1){
				echo "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\" checked>$arraydesc&nbsp&nbsp&nbsp&nbsp</option></td>";
			}else{
				echo "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\">$arraydesc&nbsp&nbsp&nbsp&nbsp</td>";

			}
		}else{
				echo "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\">$arraydesc&nbsp&nbsp&nbsp&nbsp</td>";

		}




	}else{
	$arraySQL = "SELECT arraydesc FROM agilent_arrayinfo WHERE arrayid = $arrayid";
		$arrayResult = mysql_query($arraySQL, $db);
		$arrayResultRow=mysql_fetch_row($arrayResult);
		$arraydesc = $arrayResultRow[0];
		$notinSQL .= " AND arrayid != $arrayid";
		if($currentarraycount % 5 == 0){
			echo "</tr>";
		}
		if($_GET['savedquery'] != ""){
			// What array needs to be checked?
			if(array_search($arrayid, $savedarrayvals) > -1){
				echo "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\" checked>$arraydesc&nbsp&nbsp&nbsp&nbsp</option></td>";
			}else{
				echo "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\">$arraydesc&nbsp&nbsp&nbsp&nbsp</td>";

			}
		}else{
				echo "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\">$arraydesc&nbsp&nbsp&nbsp&nbsp</td>";

		}

	}
	$currentarraycount++;


}
$exparray[$expid][$currentarrayid] ="</tr></table></div></td></tr>";

echo "</tr></table></div></td></tr>";

for($i = 0; $i < $numexps; $i++){
	$thisexpcount = count($exparray[$i]);
	echo "$exparray[$i]<br>";
	for($j = 0; $j < $thisexpcount; $j++){
		echo "<tr><td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\">An array</option></td></tr>";
	}

}

//echo "<br>$notinSQL<BR><BR>";
// The form has not been submitted....
	$sql = "SELECT a.arrayid, a.arraytype, a.arraydesc FROM `agilent_arrayinfo` as a WHERE ($notinSQL) AND arraytype = 0 ORDER BY arrayid";
	//echo "$sql <br>";
	$arraytypeResult = mysql_query($sql, $db);
while(list($arrayid, $arraytype, $arraydesc) = mysql_fetch_array($arraytypeResult))
{

    $arraytypeMenu .= "<tr><td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\">$organism $arraydesc $version</option></td></tr>";
}
 echo $arraytypeMenu;

?>







<tr>
<td  class="questionanswer" colspan="3"><strong>Heat Map Options</strong></td>
</tr>

<tr>
<td class="questionparameter" ><strong>Heat Map Color Scheme:</strong></td>
<td class="results">
<?php
// what colorscheme is checked???
if($_GET['savedquery'] != ""){

		if($savedvals['colorScheme'] == 0){
			$gr = "checked";
			$yb = "";
		}
		else{
			$gr = "";
			$yb = "checked";
		}
	}
	else{
		$gr = "checked";
			$yb = "";
	}
?>
<input type="radio" name="colorScheme" <?php echo $gr; ?> value="0"><font color="red"><strong>Red</font>/<font color="green">Green</font></strong><br>
<input type="radio" name="colorScheme" <?php echo $yb; ?> value="1"><font color="yellow"><strong>Yellow</font>/<font color="blue">Blue</font></strong><br>
</td>
<td class="results">
</td>
</tr>


<tr>
<td  class="questionanswer" colspan="3"><strong>Threshold Values:</strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Minimum Induction:</strong></td>
<td class="results">
<?php

	if($_GET['savedquery'] != ""){
		$oval = $savedvals['rval'];
		$mval = $savedvals['rvalmax'];
	}
	else{
		$oval = 3;
		$mval = "";
	}
?>
<input size="4" name="rval" type="text" value="<?php echo $oval; ?>" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be positive.
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Minimum Repression:</strong></td>
<td class="results">
<?php

	if($_GET['savedquery'] != ""){
		$oval = $savedvals['lval'];
		$mval = $savedvals['lvalmin'];
	}
	else{
		$oval = -3;
		$mval = "";
	}
?>
<input size="4" name ="lval" type="text" value="<?php echo $oval; ?>" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be negative.
</td>
</tr>
<tr>
<td class="questionparameter"><strong>Information Gain:</strong></td>
<td class="results"><input size="4" name ="infogain" type="text" value="20" align="right"></input></td>
<td></td>

</tr>

<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td><input type="reset" value="Reset Form"</td>
</tr>

</table>

</form>
</p>
<?php


}


?></div>
 </div>
  <?php
	include 'leftmenu.inc';

?>
 <div class="boxclear"> </div>
 <div class="boxclear"> </div>
 <div class="boxfooter"></div>
</body>
</html>
