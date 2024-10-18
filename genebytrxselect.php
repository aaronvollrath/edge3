<?php
/***
Location: /edge2
Description: This page is used to do the k-means and hierarchical clustering of the treatments.  Users are given the ability
		to choose individual arrays or specific chemicals.
POST:
FORM NAME: "query" ACTION: "genebytrxselect.php" METHOD: "post" ONSUBMIT: ""
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
include 'header.inc';
require "formcheck2.inc";
include 'edge_update_user_activity.inc';
include 'outputimage.inc';
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



?>

<head>
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

<body onload="return hideTrxRowOnLoad()">

	<?php
		include 'banner.inc';
	?>

 <div class="boxmiddle">
 <?php
include 'questionmenu.inc';
?>

 <h3 class="contenthead">How do my selected genes change in response to my selected treatments?</h3>

<div>

<?php
if(isset($_POST['submit'])){

//analyze($_POST);
//echo "<br>";
$cloneidArray = array();
foreach($_POST['condgenes'] as $cid){

	//echo "gene: $gene<br>";
	if($cid != ""){
		array_push($cloneidArray, "cloneid = $cid");
	}

}

// Need to get the treatments and the genes selected and then make the necessary queries....
// What treatments were selected????
// What chem were selected????

	$colorscheme = $_POST['colorScheme'];
	$upperbound = $_POST['rval'];
	$upperboundmax = $_POST['rvalmax'];
	$lowerbound = $_POST['lval'];
	$lowerboundmin = $_POST['lvalmin'];


	$arrayidArray = array();
	$arrayDescArray = array();
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

//echo "Creating the svg file...<br>";
		// SVG GLOBAL VARIABLES...
		$squareheight = 10;
		$squarewidth = 20;
		$xmargin = 100;

		// CODE FOR THE FILES THAT WILL BE CREATED....
		$filenum = rand(0, 25000);
		$num = $filenum;
		$svgFile = "/var/www/html/edge2/IMAGES/svg$filenum.svg";
		$tableFile = "/var/www/html/edge2/IMAGES/table$filenum";
		$command = "touch $svgFile";
		$str=exec($command);
		$command = "touch $tableFile";
		$str=exec($command);
		$fd = fopen($svgFile, 'w');
		$ftable = fopen($tableFile, 'w');
		// THIS IS THE HEADER FOR THE SVG FILE.....
		$svgheader = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"no\"?>
		<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 20010904//EN\"
		\"http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd\" [
		<!ATTLIST svg
		xmlns:a3 CDATA #IMPLIED
		a3:scriptImplementation CDATA #IMPLIED>
		<!ATTLIST script
		a3:scriptImplementation CDATA #IMPLIED>
		]>\n";
		fwrite($fd, $svgheader);


	$chemid = $_POST['chem'];
	$upperbound = $_POST['upperbound'];
	$lowerbound = $_POST['lval'];


	?>



	<?php

		$privval = $_SESSION['priv_level'];
		fwrite($ftable, "<table class=\"sortable\" id=\"results\">");
		fwrite($ftable, "<thead>");
		if($privval == ""){
			$priv = 1;
		}
		else{
			$priv = $privval;
		}
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
		$accesscount = 0;  // This is used to count the number of arrays they can access....
		$sampleidarray = array();
		$arrayidarray = array();
		$arraynamearray = array();
		$arrayidsqlstring2 = str_replace("arrayid", "sampleid", $arrayidsqlstring);
		$sampleidsql = "SELECT DISTINCT sampleid from sampledata where $arrayidsqlstring2 ORDER BY sampleid";
		//echo "sampleidsql is: $sampleidsql";
		$sampleResult = mysql_query($sampleidsql, $db);
		$treatments = "<g id=\"treatments\" transform=\"translate($xmargin,145)\">";
		$xVal = 15;
		while($row = mysql_fetch_row($sampleResult)){
			array_push($sampleidarray, $row[0]);
			$arrayidsql = "SELECT arrayid, arraydesc from array where sampleid = $row[0]";
			if($_SESSION['username'] == "aaronv" ){
				//echo "<br> arrayidsql is: $arrayidsql<hr>";
			}

			$arrayidResult = mysql_query($arrayidsql, $db);
			$arrayRow = mysql_fetch_row($arrayidResult);
			$arrayid = $arrayRow[0];
			array_push($arrayidarray, $arrayid);
			$accesscount++;
			$random = rand(0, 25000);
			$xlink = "<a xlink:href=\"http://edge.oncology.wisc.edu/sample.php?sampleid=$arrayid&amp;orderby=hybrids.finalratio&amp;sort=asc&amp;lcomp=lte&amp;lval=-3&amp;rcomp=gte&amp;rval=3\" target=\"_blank$random\" alt=\"\">";
			$treatments .= $xlink;
			$name = $arrayRow[1];
			array_push($arraynamearray, $name);
			$trxtext = "<text x=\"$xVal\" y=\"0\" transform=\"rotate(270, $xVal, 0)\" style=\"font-family: arial; font-size:8pt; font-weight:bold;\">$name</text>";
			$treatments .= $trxtext;
			$xVal+=$squarewidth;
			$xlink = "</a>";
			$treatments .= $xlink;

		}

	$treatments .= "</g>";

	$tabledata = "<tr>
	<th class=\"subhead\" abbr=\"number\">Clone ID</th>
	<th class=\"subhead\" abbr=\"alpha\" width=100>Gene Name</th>
	<th class=\"subhead\" abbr=\"alpha\">REFSEQ</th>";
	fwrite($ftable, $tabledata);
	?>



	<?php

	$arrayidsql = array();
	$experimentcounter = 0;
	foreach($arrayidarray as $id){
		$val = " arrayid = $id ";
		$name = $arraynamearray[$experimentcounter];
		fwrite($ftable, "<th class=\"subhead\" abbr=\"number\">$name</th>");
		array_push($arrayidsql, $val);
		$or = "OR";
		array_push($arrayidsql, $or);
		$experimentcounter++;
	}

	$tabledata = "</tr></thead>";
	fwrite($ftable, $tabledata);
	?>


	<?php

	// Pop the last or off...
	array_pop($arrayidsql);
	$arrayidsqlstring = "";
	foreach($arrayidsql as $item){
		$arrayidsqlstring .= $item;
	}
	//echo "<br> $arrayidsqlstring </br>";

	// What were the cloneid's selected????



	$upperbound = 3;

	// Getting the distinct clones.....
	$cloneidarray = array();
	//echo "ACCESS count = $accesscount <br>";
	//if($accesscount != 0){
		//$cloneidResult = mysql_query($cloneiddistinctsql, $db);
			//while($cloneRow = mysql_fetch_row($cloneidResult)){
			foreach($cloneidArray as $clonestr){

				$clonestr = " $clonestr ";
				array_push($cloneidarray, $clonestr);
				$cloneor = "OR";
				array_push($cloneidarray, $cloneor);
				//echo "<br>$clonestr</br>";
			}
	//}
	// If we've any clones then we can display them... If not, then we just skip this and go to the </table> tag....
	if(count($cloneidarray) != 0){


		// Get rid of the last OR....
		array_pop($cloneidarray);

		// Create the cloneid sql string
		$cloneidsqlstr = "";
		foreach($cloneidarray as $item){
			$cloneidsqlstr .= $item;
		}
		//echo "<br> $cloneidsqlstr </br>";



		// Using the first arrayid to get the cloneid order.....
		$firstarrayid= array_shift($arrayidarray); // $arrayidarray[0];

		//echo "<br>first id = $firstarrayid</br>";
		if($dataset == 1){
			$cloneidsql = "SELECT cloneid, ROUND(finalratio,3) from hybrids where arrayid = $firstarrayid and ( $cloneidsqlstr ) ORDER BY finalratio DESC";
		}
		else{
			$cloneidsql = "SELECT cloneid, ROUND(finalratio,3) from condensedhybrids where arrayid = $firstarrayid and ( $cloneidsqlstr ) ORDER BY finalratio DESC";
		}

		if($_SESSION['username'] == "aaronv" ){
			//echo "<br>$cloneidsql</br>";;
		}



		//echo "<br>$cloneiddistinctsql</br>";
		$firstarrayclones = array();
		$firstarrayratios = array();
		$cloneidResult = mysql_query($cloneidsql, $db);
			while($cloneRow = mysql_fetch_row($cloneidResult)){
				$cloneid = $cloneRow[0];
				$finalratio = $cloneRow[1];
				array_push($firstarrayclones, $cloneid);
				array_push($firstarrayratios, $finalratio);
			}
		// Got the first array's values.... now need to get the rest of the values from the other arrays....
		// We've essentially got all the sql already; just need to shift the first off.... and the OR...
		//echo "There are $accesscount arrays<br>";
		$maxX = $accesscount * $squarewidth;
		$clonecount = count($firstarrayclones);
		//echo "There are $clonecount clones<br>";
		$height = $clonecount * $squareheight + 300;
		$width = $maxX + 400;
		//echo "Colorscheme = $colorScheme<br>";
		$svgdeclaration = "<svg preserveAspectRatio=\"xMinYMin meet\" viewBox=\"0 0 $width $height\" id=\"svgObject\" xmlns=\"http://www.w3.org/2000/svg\"
		xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:a3=\"http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/\"
		a3:scriptImplementation=\"Adobe\"> <script type=\"text/ecmascript\" a3:scriptImplementation=\"Adobe\"><![CDATA[]]></script>));";
		//$svgdeclaration="<svg width=\"$width\" height=\"$height\" viewBox=\"0 0 $width $height\">";
		fwrite($fd, $svgdeclaration);
		$svgdata = "<g id=\"graphic\" transform=\"translate(0,0)\">\n";
		fwrite($fd, $svgdata);
		// CREATING THE SCALE LEGEND.....
		if($colorScheme == 1){ // We're dealing w/ yellow/blue colorscheme....
		$svgdata = "<g id=\"legend\"  transform=\"translate(40,25)\"><rect x=\"0\" y=\"6\" width=\"10\" height=\"6\" style=\"fill: rgb(224,224,0);\"/><text x=\"15\" y=\"11\" style=\"stroke: black; font-size: 6pt;\">>= 8</text><rect x=\"0\" y=\"12\" width=\"10\" height=\"6\" style=\"fill: rgb(219,219,0);\"/><rect x=\"0\" y=\"18\" width=\"10\" height=\"6\" style=\"fill: rgb(213,213,0);\"/><text x=\"15\" y=\"23\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">6</text>
<rect x=\"0\" y=\"24\" width=\"10\" height=\"6\" style=\"fill: rgb(204,204,0);\"/><rect x=\"0\" y=\"30\" width=\"10\" height=\"6\" style=\"fill: rgb(192,192,0);\"/><text x=\"15\" y=\"35\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">4</text>
<rect x=\"0\" y=\"36\" width=\"10\" height=\"6\" style=\"fill: rgb(170,170,0);\"/><rect x=\"0\" y=\"42\" width=\"10\" height=\"6\" style=\"fill: rgb(128,128,0);\"/><text x=\"15\" y=\"47\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">2</text>
<rect x=\"0\" y=\"48\" width=\"10\" height=\"6\" style=\"fill: rgb(0,0,0);\"/><text x=\"-20\" y=\"53\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">Key</text>
<rect x=\"0\" y=\"54\" width=\"10\" height=\"6\" style=\"fill: rgb(0,0,128);\"/><text x=\"15\" y=\"59\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">-2</text>
<rect x=\"0\" y=\"60\" width=\"10\" height=\"6\" style=\"fill: rgb(0,0,170);\"/><rect x=\"0\" y=\"66\" width=\"10\" height=\"6\" style=\"fill: rgb(0,0,192);\"/><text x=\"15\" y=\"71\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">-4</text>
<rect x=\"0\" y=\"72\" width=\"10\" height=\"6\" style=\"fill: rgb(0,0,204);\"/><rect x=\"0\" y=\"78\" width=\"10\" height=\"6\" style=\"fill: rgb(0,0,213);\"/><text x=\"15\" y=\"83\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">-6</text>
<rect x=\"0\" y=\"84\" width=\"10\" height=\"6\" style=\"fill: rgb(0,0,219);\"/><rect x=\"0\" y=\"90\" width=\"10\" height=\"6\" style=\"fill: rgb(0,0,224);\"/><text x=\"15\" y=\"95\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">&lt;= -8</text>
<rect x=\"-25\" y=\"0\" width=\"70\" height=\"100\" style=\"fill: none; stroke: red;\"/></g>";
		}
		else{ // this is a red/green color scheme...
		$svgdata = "<g id=\"legend\"  transform=\"translate(40,25)\"><rect x=\"0\" y=\"6\" width=\"10\" height=\"6\" style=\"fill: rgb(224,0,0);\"/><text x=\"15\" y=\"11\" style=\"stroke: black; font-size: 6pt;\">>= 8</text>
<rect x=\"0\" y=\"12\" width=\"10\" height=\"6\" style=\"fill: rgb(219,0,0);\"/><rect x=\"0\" y=\"18\" width=\"10\" height=\"6\" style=\"fill: rgb(213,0,0);\"/><text x=\"15\" y=\"23\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">6</text>
<rect x=\"0\" y=\"24\" width=\"10\" height=\"6\" style=\"fill: rgb(204,0,0);\"/><rect x=\"0\" y=\"30\" width=\"10\" height=\"6\" style=\"fill: rgb(192,0,0);\"/><text x=\"15\" y=\"35\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">4</text>
<rect x=\"0\" y=\"36\" width=\"10\" height=\"6\" style=\"fill: rgb(170,0,0);\"/><rect x=\"0\" y=\"42\" width=\"10\" height=\"6\" style=\"fill: rgb(128,0,0);\"/><text x=\"15\" y=\"47\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">2</text>
<rect x=\"0\" y=\"48\" width=\"10\" height=\"6\" style=\"fill: rgb(0,0,0);\"/><text x=\"-20\" y=\"53\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">Key</text>
<rect x=\"0\" y=\"54\" width=\"10\" height=\"6\" style=\"fill: rgb(0,128,0);\"/><text x=\"15\" y=\"59\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">-2</text>
<rect x=\"0\" y=\"60\" width=\"10\" height=\"6\" style=\"fill: rgb(0,170,0);\"/><rect x=\"0\" y=\"66\" width=\"10\" height=\"6\" style=\"fill: rgb(0,192,0);\"/><text x=\"15\" y=\"71\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">-4</text>
<rect x=\"0\" y=\"72\" width=\"10\" height=\"6\" style=\"fill: rgb(0,204,0);\"/><rect x=\"0\" y=\"78\" width=\"10\" height=\"6\" style=\"fill: rgb(0,213,0);\"/><text x=\"15\" y=\"83\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">-6</text>
<rect x=\"0\" y=\"84\" width=\"10\" height=\"6\" style=\"fill: rgb(0,219,0);\"/><rect x=\"0\" y=\"90\" width=\"10\" height=\"6\" style=\"fill: rgb(0,224,0);\"/><text x=\"15\" y=\"95\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">&lt;= -8</text>
<rect x=\"-25\" y=\"0\" width=\"70\" height=\"100\" style=\"fill: none; stroke: red;\"/></g>";

		}
		fwrite($fd, $svgdata);
		$svgdata = "<g id=\"heatmap\"  cursor=\"crosshair\" transform=\"translate($xmargin,150)\">\n";
		fwrite($fd, $svgdata);


		array_shift($arrayidsql);
		array_shift($arrayidsql);

		if(count($arrayidsql) == 0){
		//echo "In count(arrayidsql) == 0 <br>";
		// There was only one arrayid....
			//echo "There was only one array...<br>";
			$arrayid = $firstarrayid;
			$acounter = 0;

			foreach($firstarrayclones as $cloneid){
				//echo "Here's firstarrayid: $firstarrayid";
				$tabledata =  "<tr>";
				fwrite ($ftable, $tabledata);
				if($dataset == 1){
					$primarynamesql = "SELECT annname,refseq from annotations where cloneid = $cloneid";
				}
				else{
					$primarynamesql = "SELECT annname,refseq from condensedannotations where cloneid = $cloneid";
				}
				$pnResult = mysql_query($primarynamesql, $db);
				$pnRow = mysql_fetch_row($pnResult);
				$primaryname = str_replace("\"","", $pnRow[0]);
				$primaryname = ucfirst ( $primaryname);
				$refseq = trim($pnRow[1]);
				$random = rand(0, 25000);
				if($dataset != 1){
					$thiscloneidval = "-$cloneid";
				}
				else{
					$thiscloneidval = $cloneid;
				}
				$tabledata = "<td class=\"nochange\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\">
				<a href=\"http://edge.oncology.wisc.edu/cloneinfosvgname.php?cloneid=$thiscloneidval\" target=\"_blank$random\" alt=\"\">$cloneid</a></td>";
				$tabledata .=  "<td class=\"results\" width=100>$primaryname</td>
					<td class=\"results\">
					<a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$refseq\" target=\"_blank$random\">$refseq</a>
					</td>";
				fwrite($ftable, $tabledata);
				// the first cell is for the first clone.....
				$finalratio = array_shift($firstarrayratios);
				if($dataset == 1){
					$onclick = "viewcloneinfo($cloneid, $firstarrayid)";
				}else{
					$onclick = "ci(-$cloneid, $firstarrayid)";
				}


				if($colorScheme == 0){
					if($finalratio > 1){
						$tabledata =  "<td class=\"gtzero\"  style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					elseif($finalratio < 1){
						$tabledata =  "<td class=\"ltzero\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					else{
						$tabledata =  "<td class=\"nochange\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
				}
				else{
					if($finalratio > 1){
						$tabledata =  "<td class=\"gtzeroyellow\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					elseif($finalratio < 1){
						$tabledata =  "<td class=\"ltzeroblue\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					else{
						$tabledata =  "<td class=\"nochange\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
				}
				//echo "$tabledata <hr>";
				fwrite($ftable, $tabledata);
				fwrite($ftable, "</tr>");
				$y = $acounter * $squareheight;
				//echo "HELLO!!!!!!!!!!";
				$x = 0;
				$acounter++;
				//echo "$cloneid"."_"."$arrayid<br>";
				$random = rand(0, 25000);


				$xlink = "<a xlink:href=\"http://edge.oncology.wisc.edu/cloneinfosvg.php?cloneid=$thiscloneidval"."_"."$arrayid\" target=\"_blank$random\">";

				fwrite($fd, $xlink);

				// ####################
				// THIS CODE DOES THE NECESSARY CONVERTING OF THE FINALRATIO VALUES
				// SO THAT THEY CAN BE DISPLAYED.....
				if ($colorScheme == 0) {
					$redValue = 0;
					$greenValue = 0;
					if ($finalratio < 0) {
						$negMult = ( -1 / $finalratio);
						$greenValue = 255 - (255 * $negMult);
					}
					else {
						$posMult = 1 / $finalratio;
						$redValue = 255 - (255 * $posMult);
					}
					$red = intval($redValue);
					$green = intval($greenValue);
					$style = "style=\"fill: rgb($red,$green,0);\"";
				}
				else {
					//echo "yellow/blue colorscheme <BR>";
					$yellowValue = 0;
					$blueValue = 0;
					if ($finalratio < 0) {
						$negMult = ( -1 / $finalratio);
						$blueValue = 255 - (255 * $negMult);
					}
					else {
						$posMult = 1 / $finalratio;
						$yellowValue = 255 - (255 * $posMult);
					}
					$yellow = intval($yellowValue);
					$blue = intval($blueValue);
					$style = "style=\"fill: rgb($yellow,$yellow,$blue);\"";

				}
				$xloc = $x;
				// ####################
				$rect = "<rect x=\"$x\" y=\"$y\" width=\"$squarewidth\" height=\"$squareheight\" $style/>\n";
				fwrite($fd, $rect);
				$xlink = "</a>";
				fwrite($fd, $xlink);
				$random = rand(0, 25000);
				if($dataset != 1){
					$thiscloneidval = "-$cloneid";
				}
				else{
					$thiscloneidval = $cloneid;
				}
				$xlink = "<a xlink:href=\"http://edge.oncology.wisc.edu/cloneinfosvgname.php?cloneid=$thiscloneidval\" target=\"_blank$random\" alt=\"\">\n";
				$genes .= $xlink;
				//fwrite($fd, $xlink);
				$textlocx = $x + 40;
				$textlocy = $y;
				$primaryname=substr($primaryname, 0, 50);
				$text = "<text x=\"0\" y = \"$textlocy\" style=\"font-family: arial; font-size:6pt;\">$primaryname</text>\n";
				$genes .= $text;
				//fwrite($fd, $text);
				$xlink = "</a>";
				$genes .= $xlink;
				//fwrite($fd, $xlink);
			}


		}
		else{
		//echo "In count(arrayidsql) NOT EQUAL 0 <br>";
		$arrayidsqlstring = "";
		foreach($arrayidsql as $item){
			$arrayidsqlstring .= $item;
		}
		//echo "<br> $arrayidsqlstring </br>";
		$acounter = 0;
				foreach($firstarrayclones as $cloneid){
				// FOR CLONEID, CREATE A ROW IN THE TABLE AND IN THE SVG FILE....
				//echo "<br>This cloneid = $cloneid<br>";
				$tabledata =  "<tr>";
				fwrite ($ftable, $tabledata);

				//$finalratio = $cloneRow[1];
				if($dataset == 1){
					$primarynamesql = "SELECT annname,refseq from annotations where cloneid = $cloneid";
				}
				else{
					$primarynamesql = "SELECT annname,refseq from condensedannotations where cloneid = $cloneid";
				}
				$pnResult = mysql_query($primarynamesql, $db);
				$pnRow = mysql_fetch_row($pnResult);
				$primaryname = str_replace("\"","", $pnRow[0]);
				$primaryname = ucfirst ( $primaryname);
				$refseq = trim($pnRow[1]);
				$random = rand(0, 25000);
				if($dataset != 1){
					$thiscloneidval = "-$cloneid";
				}else{
					$thiscloneidval = $cloneid;
				}
				$tabledata =  "<td class=\"nochange\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\">
				<a href=\"http://edge.oncology.wisc.edu/cloneinfosvgname.php?cloneid=$thiscloneidval\" target=\"_blank$random\" alt=\"\">$cloneid</a></td>";
				$tabledata .=  "<td class=\"results\" width=\"100px\">$primaryname</td>
					<td class=\"results\">
					<a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$refseq\" target=\"_blank$random\">$refseq</a>
					</td>";
				// the first cell is for the first clone.....
				fwrite($ftable, $tabledata);
				$finalratio = array_shift($firstarrayratios);
				if($dataset == 1){
					$onclick = "viewcloneinfo($cloneid, $firstarrayid)";
				}else{
					$onclick = "ci(-$cloneid, $firstarrayid)";
				}
				if($colorScheme == 0){
					if($finalratio > 1){
						$tabledata =  "<td class=\"gtzero\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					elseif($finalratio < 1){
						$tabledata =  "<td class=\"ltzero\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					else{
						$tabledata =  "<td class=\"nochange\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
				}
				else{
					if($finalratio > 1){
						$tabledata =  "<td class=\"gtzeroyellow\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					elseif($finalratio < 1){
						$tabledata =  "<td class=\"ltzeroblue\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					else{
						$tabledata =  "<td class=\"nochange\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
				}
				fwrite($ftable, $tabledata);

				$y = $acounter * $squareheight;
				$x = 0;
				$acounter++;
				$random = rand(0, 25000);


				$xlink = "<a xlink:href=\"http://edge.oncology.wisc.edu/cloneinfosvg.php?cloneid=$thiscloneidval"."_"."$firstarrayid\" target=\"_blank$random\">";
				fwrite($fd, $xlink);

				// ####################
				// THIS CODE DOES THE NECESSARY CONVERTING OF THE FINALRATIO VALUES
				// SO THAT THEY CAN BE DISPLAYED.....
				if ($colorScheme == 0) {
					$redValue = 0;
					$greenValue = 0;
					if ($finalratio < 0) {
						$negMult = ( -1 / $finalratio);
						$greenValue = 255 - (255 * $negMult);
					}
					else {
						$posMult = 1 / $finalratio;
						$redValue = 255 - (255 * $posMult);
					}
					$red = intval($redValue);
					$green = intval($greenValue);
					$style = "style=\"fill: rgb($red,$green,0);\"";
				}
				else {
					$yellowValue = 0;
					$blueValue = 0;
					if ($finalratio < 0) {
						$negMult = ( -1 / $finalratio);
						$blueValue = 255 - (255 * $negMult);
					}
					else {
						$posMult = 1 / $finalratio;
						$yellowValue = 255 - (255 * $posMult);
					}
					$yellow = intval($yellowValue);
					$blue = intval($blueValue);
					$style = "style=\"fill: rgb($yellow,$yellow,$blue);\"";

				}
				if($dataset != 1){
					$thiscloneidval = "-$cloneid";
				}
				else{
					$thiscloneidval = $cloneid;
				}
				// ####################
				$xloc = $x * $squarewidth;
				$rect = "<rect x=\"$xloc\" y=\"$y\" width=\"$squarewidth\" height=\"$squareheight\" $style/>\n";
				fwrite($fd, $rect);
				$xlink = "</a>";
				fwrite($fd, $xlink);
				$random = rand(0, 25000);
				$xlink = "<a xlink:href=\"http://edge.oncology.wisc.edu/cloneinfosvgname.php?cloneid=$thiscloneidval\" target=\"_blank$random\" alt=\"\">\n";
				$genes .= $xlink;
				//fwrite($fd, $xlink);
				$textlocx = $maxX;
				$textlocy = $y;
				$primaryname=substr($primaryname, 0, 50);
				$text = "<text x=\"0\" y = \"$textlocy\" style=\"font-family: arial; font-size:6pt;\">$primaryname</text>\n";
				$genes .= $text;
				//fwrite($fd, $text);
				$xlink = "</a>";
				$genes .= $xlink;



					$count = 1;
					$x = 1;
			if($dataset == 1){
				$cloneidsql = "SELECT ROUND(finalratio,3), arrayid from hybrids where ($arrayidsqlstring) and cloneid = $cloneid ORDER BY arrayid ASC";
			}
			else{
				$cloneidsql = "SELECT ROUND(finalratio,3), arrayid from condensedhybrids where ($arrayidsqlstring) and cloneid = $cloneid ORDER BY arrayid ASC";
			}
			//echo "cloneidsql: $cloneidsql<br>";
			$cloneidResult = mysql_query($cloneidsql, $db);

			while($row =	mysql_fetch_row($cloneidResult)){
				//echo "$cloneid final ratios: ";
				$finalratio = $row[0];
				$arrayid = $row[1];
					//echo "$finalratio";
				if($dataset == 1){
					$onclick = "viewcloneinfo($cloneid, $arrayid)";
				}else{
					$onclick = "ci(-$cloneid, $arrayid)";
				}
				if($colorScheme == 0){
					if($finalratio > 1){
						$tabledata =  "<td class=\"gtzero\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					elseif($finalratio < 1){
						$tabledata =  "<td class=\"ltzero\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					else{
						$tabledata =  "<td class=\"nochange\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
				}
				else{
					if($finalratio > 1){
						$tabledata =  "<td class=\"gtzeroyellow\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					elseif($finalratio < 1){
						$tabledata =  "<td class=\"ltzeroblue\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
					else{
						$tabledata =  "<td class=\"nochange\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\" onclick=\"$onclick\"; return false;\">$finalratio</td>";
					}
				}
				fwrite($ftable, $tabledata);
				$random = rand(0, 25000);
				if($dataset != 1){
					$thiscloneidval = "-$cloneid";
				}else{
					$thiscloneidval = $cloneid;
				}

				$xlink = "<a xlink:href=\"http://edge.oncology.wisc.edu/cloneinfosvg.php?cloneid=$thiscloneidval"."_"."$arrayid\" target=\"_blank$random\">";
				fwrite($fd, $xlink);

				// ####################
				// THIS CODE DOES THE NECESSARY CONVERTING OF THE FINALRATIO VALUES
				// SO THAT THEY CAN BE DISPLAYED.....
				if ($colorScheme == 0) {
					$redValue = 0;
					$greenValue = 0;
					if ($finalratio < 0) {
						$negMult = ( -1 / $finalratio);
						$greenValue = 255 - (255 * $negMult);
					}
					else {
						$posMult = 1 / $finalratio;
						$redValue = 255 - (255 * $posMult);
					}
					$red = intval($redValue);
					$green = intval($greenValue);
					$style = "style=\"fill: rgb($red,$green,0);\"";
				}
				else {
					$yellowValue = 0;
					$blueValue = 0;
					if ($finalratio < 0) {
						$negMult = ( -1 / $finalratio);
						$blueValue = 255 - (255 * $negMult);
					}
					else {
						$posMult = 1 / $finalratio;
						$yellowValue = 255 - (255 * $posMult);
					}
					$yellow = intval($yellowValue);
					$blue = intval($blueValue);
					$style = "style=\"fill: rgb($yellow,$yellow,$blue);\"";

				}

				// ####################
				$xloc = $x * $squarewidth;
				$rect = "<rect x=\"$xloc\" y=\"$y\" width=\"$squarewidth\" height=\"$squareheight\" $style/>\n";
				fwrite($fd, $rect);
				$xlink = "</a>";
				fwrite($fd, $xlink);

				$count++;
				$x++;
			}

			fwrite($ftable, "</tr>");
		}


	} // End of else where there were greater than 1 arrays....
	$svgdata = "</g>"; // Close the heatmap group....
			fwrite($fd, $svgdata);
			$xloc = $maxX + $xmargin + 10;
			$svgdata = "<g id=\"genes\" transform=\"translate($xloc,158)\">";
			fwrite($fd, $svgdata);
			fwrite($fd, $genes);
			$svgdata = "</g>"; // Close the genes group...
			fwrite($fd, $svgdata);
			fwrite($fd, $treatments);  // Write the treatments to the file...
			fwrite($fd, $svgdata);// close the graphic group...
			$svgdata = "</svg>";
			fwrite($fd, $svgdata);
			fflush($fd);
			fclose($fd);

			$command = "cp ./IMAGES/svg$filenum.svg ./IMAGES/imagesvg$filenum.svg";
				$str = exec($command);
				$cpsvgfile = "/var/www/html/edge2/IMAGES/imagesvg$filenum.svg";

				$filesize = filesize($cpsvgfile);

				if($filesize > 3169300  && $output == 1){
				echo "<br>LARGE SVG FILE: Displaying the PNG file.";
					// Change the format to png....
					$output = 3;
				}

				createImage("svg$filenum.svg");
			// Compressing svg file....
			$command = "gzip --best ./IMAGES/svg$filenum.svg";
			//echo $command;
			$str=exec($command);




			// Moving the file into a different file that can be identified as svg....
			$command = "mv ./IMAGES/svg$filenum.svg.gz ./IMAGES/svg$filenum.svgz";
			//echo $command;
			$str = exec($command);
			fwrite($ftable, "</table>");
			fflush($ftable);
			fclose($ftable);
			
			$w = $width;
			$h = $height;
		}// End if(there are actually some clones....
			else{
			$noresults = "There were no results returned";
		}
		// $ac and $ac are the number of arrays and the number of clones, respectively...
		$ac = $accesscount;
		$cc = $clonecount;
		} // End if num == ""



		  //End else submitted....
if($outputformat != ""){
//echo "OUTPUT is set <br>";
?>
<p class="styletext">

	<table class="question">
	<thead>
	<tr>
	<th class="mainheader" colspan="2">Query Parameters</th>
	<th class="mainheader" colspan="2">Display Data As</th>
	</tr>
	</thead>
	<tr class="question">
	<td class="questionparameter"><strong>Arrays:</strong></td>
	<td class="questionanswer"> <?php echo $ac; ?></td>
	<?php
	//if($output==1){
	$upperbound = $_POST['upperbound'];
	$lowerbound = $_POST['lval'];
	?>
	<td class="questionparameter"><a href="./question3table.php?output=0&num=<?php echo $filenum; ?>&chemname=<?php echo $chemname; ?>
	&rval=<?php echo $upperbound; ?>&lval=<?php echo $lowerbound; ?>&w=<?php echo $w; ?>&h=<?php echo $h; ?>&ac=<?php echo $ac; ?>&cc=<?php echo $cc; ?>" target=\"_blank\"><strong>Table</strong></a></td>
	<?php
	//}else{
	?>
 <td class="questionparameter"><a href="<?php echo "./IMAGES/svg$num.svgz"; ?>" onClick="return popup(this,'SVG<?php echo $num; ?>')">View  SVG Heat Map</a></td>
	<?php
	/*<a href="./question3svg.php?output=1&num=<?php echo $num; ?>&chemname=<?php echo $chemname; ?>
	//&rval=<?php echo $upperbound; ?>&lval=<?php echo $lowerbound; ?>&w=<?php echo $w; ?>&h=<?php echo $h; ?>&ac=<?php echo $ac; //?>&cc=<?php echo $cc; ?>"><strong>SVG</strong></a>*/
	//}
	?>

	</tr>
	<tr class="question">
	<td class="questionparameter"><strong>Clones:</strong></td>
	<td class="questionanswer"> <?php echo $cc; ?></td>
			<td class="questionparameter">
			<a href="<?php echo "./IMAGES/imagesvg$num.jpg"; ?>" onClick="return popup(this,'SVG<?php echo $num; ?>')">View  JPG Heat Map</a>
			</td>
			<td class="questionparameter">
			<a href="<?php echo "./IMAGES/imagesvg$num.png"; ?>" onClick="return popup(this,'SVG<?php echo $num; ?>')">View  PNG Heat Map</a>
	</td>
	</tr>
	<tr class="question">
	<td class="questionparameter"></td>


	<td class="questionparameter">

			<a href="<?php echo "./Instructions/svginstructions.php"; ?>" onClick="return popup(this,'Instructions')">SVG Instructions</a></td>

	</tr>




	</table>
	</p>
	<h3>
	<?php
	echo "Genes that respond to $chemname";
	?>
	</h3>
<?php
	if($outputformat == 1){

	?>

		<embed src="<?php echo "./IMAGES/svg$filenum.svgz" ?>" width=<?php echo $w ?> height=<?php echo $h ?> name="heatmap" type="image/svg+xml" />
	<?php
	}elseif($outputformat == "2"){
				?><p>
				<img src="<?php echo "./IMAGES/imagesvg$filenum.jpg" ?>" alt="heatmap" align="bottom" usemap="#map1"></img>
				</p>
				<?php
				include "./IMAGES/imagemapsvg$filenum";
	}elseif($outputformat == "3"){
				?><p>
				<img src="<?php echo "./IMAGES/imagesvg$filenum.png" ?>" alt="heatmap" align="bottom" usemap="#map1"></img>
				</p>
				<?php
				include "./IMAGES/imagemapsvg$filenum";
	}
	else{
	$tableFile = "/var/www/html/edge2/IMAGES/table$num";
	include $tableFile;
	}
		// Put in the query time.....
		$end = utime(); $run = $end - $start;
		echo "<font size=\"3px\" color=\"red\"><b>$noresults</b></font><br>";
		echo "<font size=\"1px\"><b>Query results returned in ";
		echo substr($run, 0, 5);
		echo " secs.</b></font><br>";

}else{
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
// Just set to mozilla based...
$browserval = 1;
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
// Get the list of  genes for the uncondensed and condensed data sets....
$uncondensedgenelistsql = "SELECT cloneid, annname FROM condensedannotations ORDER BY annname ASC";
$uncondgeneResult = mysql_query($uncondensedgenelistsql, $db);
while($row = mysql_fetch_row($uncondgeneResult))

{
	$cloneid = $row[0];
	$annname = $row[1];
	$annname = substr(str_replace("\"","", $annname),0,50);
	//$annname = quotemeta($annname);
    $condgeneMenu .= "<option value=\"$cloneid\">$annname</option>\r";
}

?>
<p class="styletext">
<form name="query" method="post" onsubmit="return checkClusteringForm()" action="<?php  $_SERVER['PHP_SELF'] ?>">

<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2">Query Parameters</th>
<th class="mainheader" ><a href="<?php echo "./Instructions/clustering.php"; ?>"  onclick="return popup(this,'Instructions')"><font size="0">Instructions?</font></a></th>
</tr>
<tr>
<td  class="questionanswer" colspan="2"><strong>Select Genes:</strong></td>
<td  class="questionanswer"><strong>Your Query Options:</strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Dataset:</strong></td>
<td class="results">
<strong><font color="red">Using Condensed Dataset</font></strong><br>
</td>
<td align="top" class="results" rowspan="2">
<ul id="globalnav">
	<li><a href="#indiv" onclick="show_querydiv('querysection0',2); return false;" tabindex="0">Recent Queries</a></li>
	<li><a href="#indiv" onclick="show_querydiv('querysection1',2); return false;" tabindex="1">Saved Queries</a></li>
</ul>
<br>
<p>
<div style="display: block;" id="querysection0" class="scroll">
<?php
	if($userid != ""){
	// GET THE THREE MOST RECENT QUERIES.....
	$sql = "SELECT query FROM savedqueries WHERE userid = $userid AND (queryname IS NULL AND (query1 IS NOT NULL AND query1 != \"\"))  ORDER BY querydate DESC LIMIT 3";
	$sqlResult = mysql_query($sql, $db);
	$recentCount=1;
	while($row = mysql_fetch_row($sqlResult)){
		echo "<a href=\"./genebytrxselect.php?savedquery=$row[0]\">Unsaved #$recentCount</a><br>";
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
	$sql = "SELECT query, queryname FROM savedqueries WHERE userid = $userid AND queryname IS NOT NULL ORDER BY querydate DESC";
	$sqlResult = mysql_query($sql, $db);
	while($row = mysql_fetch_row($sqlResult)){
		echo "<a href=\"./genebytrxselect.php?savedquery=$row[0]\">$row[1]</a><br>";
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


?>




	<tr>
<td class="questionparameter" ><strong>Genes:</strong></td>
<td class="results">
<select name="condgenes[]" multiple size="15">

<?php echo $condgeneMenu; ?>
</select>
</td>
</tr>
<tr>
<td  class="questionanswer" colspan="3"><strong>Selection Options</strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Selection Options:</strong></td>
<td class="results">
<?php
	// what dataset is checked???
	if($_GET['savedquery'] != ""){
		$oval = $savedvals['seloptions'];

		if($oval == 1){
			$chemcon = "checked";
			$indiv = "";
			$both = "";
		}
		else if($oval == 0){
			$chemcon = "";
			$indiv = "checked";
			$both = "";
		}
		else{
			$chemcon = "";
			$indiv = "";
			$both = "checked";
		}
	}
	else{
		$chemcon = "";
		$indiv = "";
		$both = "checked";
	}
?>

<input type="radio" name="seloptions" value="1" <?php echo $chemcon; ?> onclick="return hideSelsRow1(0) "> Chemical(s)/Condition(s) Group<br>
<input type="radio" name="seloptions" value="0" <?php echo $indiv; ?> onclick="return hideSelsRow1(1) ">Treatment Groups <br>
<input type="radio" name="seloptions" value="2" <?php echo $both; ?> onclick="return hideSelsRow1(2) "> Both Options<br>

</td>
<td class="results">

</td>
</tr>


<tr id="groupoption1">
<td  class="questionanswer" colspan="3"><strong>Chemical(s)/Condition(s)</strong></td>
</tr>

<tr id="groupoption3">
<td class="questionparameter" colspan="3">
<fieldset align="top">
  <legend><strong><i>Chemical Treatments</i></strong></legend>
  <table>
  	<?php echo $chemMenuChem; ?>
  </table>
</fieldset>
</td>
</tr>

<tr id="groupoption4">
<td class="questionparameter" align="top">
  <fieldset>
  <legend><strong><i>Conditions/Vehicle Treatments</i></strong></legend>
  <table>
	<?php echo $chemMenuEnv; ?>
  </table>
  </fieldset>
</td>
<td class="questionparameter" align="top" colspan="1">
<fieldset>
  <legend><strong><i>Physiologic States</i></strong></legend>
  <table>
	<?php echo $chemMenuPhyStates; ?>
  </table>
</fieldset>
</td>
<td class="questionparameter" colspan="1">
<fieldset align="top">
  <legend><strong><i>Rats</i></strong></legend>
  <table>
  	<?php echo $chemMenuRats; ?>
  </table>
</fieldset>
</td>
</tr>

<tr id="groupoption5">
<td class="questionparameter" colspan="2">
<fieldset align="top">
  <legend><strong><i>Mutant Mice</i></strong></legend>
  <table>
  	<?php echo $chemMenuMutantMice; ?>
  </table>
</fieldset>
</td>
<td class="questionparameter" align="top">
<fieldset>
  <legend><strong><i>Transgenic Mice</i></strong></legend>
  <table>
	<?php echo $chemMenuTransgenicMice; ?>
  </table>
</fieldset>
</td>
</tr>

<tr id="groupoption6">
<td class="questionparameter" colspan="1">
<fieldset align="top">
  <legend><strong><i>Cell Lines</i></strong></legend>
  <table>
  	<?php echo $chemMenuCellLines; ?>
  </table>
</fieldset>
</td>
<td class="questionparameter" align="top">
<fieldset>
  <legend><strong><i>Mixtures</i></strong></legend>
  <table>
	<?php echo $chemMenuMixtures; ?>
  </table>
</fieldset>
</td>
<td class="questionparameter" align="top">
<fieldset>
  <legend><strong><i>Tumors</i></strong></legend>
  <table>
	<?php echo $chemMenuTumors; ?>
  </table>
</fieldset>
</td>
</tr>


<tr id="individualoption1">
<td  class="questionanswer" colspan="3"><strong>Treatment Groups</strong> (Click on a group to pick chemicals listed under it)</td>
</tr>

<tr id="individualoption2">
<td class="questionparameter" colspan="3">
<div id="navcontainer">
<a name="#indiv"></a>
<ul id="globalnav">
	<?php echo $chemDivList; ?>
</ul>
</div>
<p><br></p>

<?php
	//echo $trxMenu;
	foreach($divArray as $divArrayItem){
		foreach($divArrayItem as $anItem){
			$val = $anItem;
			echo "	$val\n";

		}

	}

?>

</td>
</tr>
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
<td class="questionparameter" ><strong>Heat Map Image Output:</strong></td>
<td class="results">
<?php
// what colorscheme is checked???
if($_GET['savedquery'] != ""){

		if($savedvals['outputformat'] == 0){
			$svg = "";
			$png = "";
			$jpg = "";
			$table = "checked";
		}
		elseif($savedvals['outputformat'] == 1){
			$svg = "checked";
			$png = "";
			$jpg = "";
			$table = "";
		}
		elseif($savedvals['outputformat'] == 2){
			$svg = "";
			$png = "";
			$jpg = "checked";
			$table = "";
		}
		else{
			$svg = "";
			$png = "checked";
			$jpg = "";
			$table = "";
		}
	}
	else{
			$png = "checked";
			$svg = "";
			$jpg = "";
			$table = "";
	}
?>
<input type="radio" name="outputformat" <?php echo $svg; ?> value="1"><font color="black"><strong>SVG</font><br>
<input type="radio" name="outputformat" <?php echo $png; ?> value="3"><font color="black"><strong>PNG</font><br>
<input type="radio" name="outputformat" <?php echo $jpg; ?> value="2"><font color="black"><strong>JPG</font><br>
<input type="radio" name="outputformat" <?php echo $table; ?> value="0"><font color="black"><strong>Table</font><br>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>PNG format will be automatically selected for large queries!
</td>
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
?>
</div>
 </div>
 <?php
	include 'leftmenu.inc';

?>
 <div class="boxclear"> </div>
 <div class="boxclear"> </div>
 <div class="boxfooter"></div>
</body>
</html>
