<?php
require './phpinc/edge3_db_connect.inc';

mysql_select_db("edge", $db);
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	//die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}
include 'header.inc';
require "formcheck.inc";
include 'edge_update_user_activity.inc';
//require 'sorttable.inc';
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
<script src="sorttable.js"></script>

<body>
	<?php
		include 'banner.inc';
	?>
 <div class="boxmiddle">
 <div style="display: static">
<?php
include 'questionmenu.inc';
?>
</div>
<h3 class="contenthead">What genes respond to my treatment?</h3>
<div class="content">

<?php
//analyze($_POST);
// Check to see if we're dealing w/ GET variables....
if(count($_GET) > 0){
	//echo "Dealing w/ get...";
	$_POST['chem']= $_GET['chem'];
	$_POST['upperbound']= $_GET['rval'];
	$_POST['lval']=$_GET['lval'];
	$dataset = 0;
	$colorscheme = 0;
	$output = 1;
	$_POST['submit'] = "Submit";
	//analyze($_POST);

}

$filenum = $num;
if (!isset($_POST['submit']) && $num == "") { // if form has not been submitted
$privval = $_SESSION['priv_level'];
//echo "<br> form not submitted...<br>";
if($privval == ""){
	$priv = 1;
}
else{
	$priv = $privval;
}
// This is the sql required to get the list of chemicals...

if($priv != 99){
$chemSQL = "SELECT DISTINCT s.chemid, c.chemical FROM array AS a, sampledata AS s, chem AS c
		WHERE (a.ownerid = $priv OR a.ownerid = 1) AND s.sampleid = a.arrayid AND c.chemid = s.chemid
		ORDER BY s.chemid";
		//echo $chemSQL;
}
else{
 	$chemSQL = "SELECT DISTINCT chemid, chemical FROM chem";
}
$chemResult = mysql_query($chemSQL, $db);
while(list($chemid, $chemical) = mysql_fetch_array($chemResult))

{
    $chemMenu .= "<option value=\"$chemid\">$chemical</option>\r";
}
?>

<p class="styletext">
<form name="query" method="post" onsubmit="return checkQuestion3Form()" action="<?php  $_SERVER['PHP_SELF'] ?>">
<table class="question" width="400">
<thead>
<tr>
<th class="mainheader" colspan="2">Query Parameters</th>
<th class="mainheader" ><a href="<?php echo "./Instructions/genes.php"; ?>"  onclick="return popup(this,'Instructions')"><font size="0">Instructions?</font></a></th>
</tr>
<td class="questionanswer" colspan="3" ><strong>Data Options:</strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Data Set:</strong></td>
<td class="results">
<input type="radio" name="dataset" value="0" checked><strong><font color="red">Condensed</font></strong><br>
<input type="radio" name="dataset" value="1" <?php echo $notcondChecked; ?>>Not Condensed<br>
</td>
<td class="results">
</td>
</tr>
<tr>
<td  class="questionanswer" colspan="3"><strong>Search By:</strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Treatment:</strong></td>
<td class="results"><select name="chem">
<option SELECTED></option>
<?php echo $chemMenu; ?>
</select>
</td>
<td class="results">
A single treatment may be chosen.
</td>
</tr>


<tr>
<td  class="questionanswer" colspan="3"><strong>Threshold Values:</strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Minimum Induction:</strong></td>
<td class="results">
<input name="upperbound" type="text" value="2" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be positive.
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Minimum Repression:</strong></td>
<td class="results">
<input name ="lval" type="text" value="-2" align="right"></input>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>Value must be negative.
</td>
</tr>

<tr>
<td  class="questionanswer" colspan="3"><strong>Output Options:</strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Output:</strong></td>
<td class="results">
<input type="radio" name="output" value="1" checked>SVG<br>
<input type="radio" name="output" value="0">Table<br>
</td>
<td class="results">
<font color="red"><b>NOTE: </b></font>You need the <a href="http://www.adobe.com/svg/viewer/install/main.html" target="_blank">
<strong>SVG Viewer</strong></a> by Adobe<br>to view SVG files
</td>
</tr>

<tr>
<td class="questionparameter" ><strong>Output Color Scheme:</strong></td>
<td class="results">
<input type="radio" name="colorScheme" value="0" checked><font color="red"><strong>Red</font>/<font color="green">Green</font></strong><br>
<input type="radio" name="colorScheme" value="1"><font color="yellow"><strong>Yellow</font>/<font color="blue">Blue</font></strong><br>
</td>
<td class="results">
<font color="red"><strong>NOTE:</strong></font> You can change the color of the output.
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
}else{  // Form has been submitted....
//echo "<br>The form has been submitted...<br>";
//echo "<br> num = $num <br>";
	if($num == ""){
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

	$chemnamesql = $chemSQL = "SELECT DISTINCT chemical FROM chem where chemid = $chemid";
	$chemResult = mysql_query($chemSQL, $db);
	$chemRow = mysql_fetch_row($chemResult);
	$chemname = $chemRow[0];
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
		if($priv != 99){

				// Get the array ids they can access.....
				$arrayidsql = "SELECT arrayid FROM array WHERE ownerid = 1 OR ownerid = $priv ORDER BY arrayid";
				$arrayResult = mysql_query($arrayidsql, $db);
				$arrayArray = array();
				while($row = mysql_fetch_row($arrayResult)){
					$arrayStr = "sampleid = $row[0]";
					array_push($arrayArray, $arrayStr);
					$or = "OR";
					array_push($arrayArray, $or);
				}
				// Pop the last or off...
				array_pop($arrayArray);
				$arrayidsqlstring = " AND (";
				foreach($arrayArray as $item){
					$arrayidsqlstring .= " $item ";
				}
				$arrayidsqlstring .= ")";
				//echo $arrayidsqlstring;

		}

		$accesscount = 0;  // This is used to count the number of arrays they can access....
		$sampleidarray = array();
		$arrayidarray = array();
		$arraynamearray = array();
		$sampleidsql = "SELECT DISTINCT sampleid from sampledata where chemid = $chemid $arrayidsqlstring ORDER BY sampleid";
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

	if($dataset == 1){
	$cloneidsql = "SELECT cloneid, ROUND(finalratio,3) from hybrids where $arrayidstring and (finalratio <= $lowerbound or finalratio >= $upperbound) ORDER BY cloneid ASC";

	$cloneiddistinctsql = "SELECT DISTINCT cloneid from hybrids where ($arrayidsqlstring) and (finalratio <= $lowerbound or finalratio >= $upperbound) ORDER BY cloneid asc";
	}
	else{
$cloneidsql = "SELECT cloneid, ROUND(finalratio,3) from condensedhybrids where $arrayidstring and (finalratio <= $lowerbound or finalratio >= $upperbound) ORDER BY cloneid ASC";

	$cloneiddistinctsql = "SELECT DISTINCT cloneid from condensedhybrids where ($arrayidsqlstring) and (finalratio <= $lowerbound or finalratio >= $upperbound) ORDER BY cloneid ASC";
	}
	if($_SESSION['username'] == "aaronv" ){
		echo "<br>$cloneiddistinctsql<br>";

	}
	//echo "<br>$cloneiddistinctsql</br>";
	// Getting the distinct clones.....
	$cloneidarray = array();
	//echo "ACCESS count = $accesscount <br>";
	if($accesscount != 0){
		$cloneidResult = mysql_query($cloneiddistinctsql, $db);
			while($cloneRow = mysql_fetch_row($cloneidResult)){
				$cloneid=$cloneRow[0];
				$clonestr = " cloneid = $cloneid ";
				array_push($cloneidarray, $clonestr);
				$cloneor = "OR";
				array_push($cloneidarray, $cloneor);
				//echo "<br>$cloneid</br>";
			}
	}
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
			$cloneidsql = "SELECT cloneid, ROUND(finalratio,3) from hybrids where arrayid = $firstarrayid and ( $cloneidsqlstr ) ORDER BY cloneid ASC";
		}
		else{
			$cloneidsql = "SELECT cloneid, ROUND(finalratio,3) from condensedhybrids where arrayid = $firstarrayid and ( $cloneidsqlstr ) ORDER BY cloneid ASC";
		}

		if($_SESSION['username'] == "aaronv" ){
			echo "<br>$cloneidsql</br>";;
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
				$tabledata = "<td class=\"nochange\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\">
				<a href=\"http://edge.oncology.wisc.edu/cloneinfosvgname.php?cloneid=$cloneid\" target=\"_blank$random\" alt=\"\">$cloneid</a></td>";
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
				if($dataset != 1){
					$thiscloneidval = "-$cloneid";
				}
				else{
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
				$xlink = "<a xlink:href=\"http://edge.oncology.wisc.edu/cloneinfosvgname.php?cloneid=$cloneid\" target=\"_blank$random\" alt=\"\">\n";
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
				$tabledata =  "<td class=\"nochange\" style=\"text-decoration:underline; cursor: hand;\" align=\"center\">
				<a href=\"http://edge.oncology.wisc.edu/cloneinfosvgname.php?cloneid=$cloneid\" target=\"_blank$random\" alt=\"\">$cloneid</a></td>";
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
				if($dataset != 1){
					$thiscloneidval = "-$cloneid";
				}else{
					$thiscloneidval = $cloneid;
				}

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

				// ####################
				$xloc = $x * $squarewidth;
				$rect = "<rect x=\"$xloc\" y=\"$y\" width=\"$squarewidth\" height=\"$squareheight\" $style/>\n";
				fwrite($fd, $rect);
				$xlink = "</a>";
				fwrite($fd, $xlink);
				$random = rand(0, 25000);
				$xlink = "<a xlink:href=\"http://edge.oncology.wisc.edu/cloneinfosvgname.php?cloneid=$cloneid\" target=\"_blank$random\" alt=\"\">\n";
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



		}  //End else submitted....
if($output != ""){
//echo "OUTPUT is set <br>";
?>
<p class="styletext">

	<table class="question">
	<thead>
	<tr>
	<th class="mainheader" colspan="4">Query Parameters</th>
	<th class="mainheader" colspan="2">Display Data As</th>
	</tr>
	</thead>
	<tr class="question">
	<td class="questionparameter"><strong>Treatment:</strong></td>
	<td class="questionanswer"> <?php echo $chemname; ?></td>
	<td class="questionparameter"><strong>Arrays:</strong></td>
	<td class="questionanswer"> <?php echo $ac; ?></td>
	<?php
	if($output==1){
	$upperbound = $_POST['upperbound'];
	$lowerbound = $_POST['lval'];
	?>
	<td class="questionparameter"><a href="./question3svg.php?output=0&num=<?php echo $filenum; ?>&chemname=<?php echo $chemname; ?>
	&rval=<?php echo $upperbound; ?>&lval=<?php echo $lowerbound; ?>&w=<?php echo $w; ?>&h=<?php echo $h; ?>&ac=<?php echo $ac; ?>&cc=<?php echo $cc; ?>"><strong>Table</strong></a></td>
	<?php
	}else{
	?>
	<td class="questionparameter"><a href="./question3svg.php?output=1&num=<?php echo $num; ?>&chemname=<?php echo $chemname; ?>
	&rval=<?php echo $upperbound; ?>&lval=<?php echo $lowerbound; ?>&w=<?php echo $w; ?>&h=<?php echo $h; ?>&ac=<?php echo $ac; ?>&cc=<?php echo $cc; ?>"><strong>SVG</strong></a></td>
	<?php
	}
	?>

	</tr>
	<tr class="question">
	<td class="questionparameter"><strong>Minimal Induction:</strong></td>
	<td class="questionanswer"> <?php echo $upperbound; ?></td>
	<td class="questionparameter"><strong>Clones:</strong></td>
	<td class="questionanswer"> <?php echo $cc; ?></td>
	<td class="questionparameter">
			<a href="<?php echo "./IMAGES/svg$num.svgz"; ?>" onClick="return popup(this,'SVG<?php echo $num; ?>')">View  entire Heat Map</a>
			</td>
	</tr>
	<tr class="question">
	<td class="questionparameter"><strong>Minimal Repression:</strong></td>
	<td class="questionanswer"> <?php echo $lowerbound; ?></td>
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
	if($output == 1){

	?>


	<?php
	}else{
	$tableFile = "/var/www/html/edge2/IMAGES/table$num";
	//include $tableFile;
	}
		// Put in the query time.....
		$end = utime(); $run = $end - $start;
		echo "<font size=\"3px\" color=\"red\"><b>$noresults</b></font><br>";
		echo "<font size=\"1px\"><b>Query results returned in ";
		echo substr($run, 0, 5);
		echo " secs.</b></font><br>";
}else{

	//echo "output = $output";
}
?>
</p>
</div>
 </div>
 <?php
	include 'leftmenu.inc';
?>
 <div class="boxclear"> </div>
 <div class="boxclear"> </div>
 <div class="boxfooter">
</body>
</html>
