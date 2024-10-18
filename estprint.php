<?php
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../edge2/">Click here to go back to the main page</a>');
}

require './phpinc/edge3_db_connect.inc';

?>

<html>
<head>
<title>Edge EST Query</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<?php
require 'edgeheader2.inc';
?>
<table width="800">
<tbody align="left">
<tr>

<?php


// Need to determine the parameters....
	$queryparams = "";
	$whereCheck = 0;
	$primaryname = stripslashes($_POST['primaryname']);
	//used to store this value to pass to next page...need because stupidly reuse name later
	$primarynamestore = $primaryname;
	$refseq = stripslashes($_POST['refseq']);
	//used to store this value to pass to next page...another fine example of poor programming
	$refseqstore = $refseq;
	$library = $_POST['library'];
	$numlibs = count($library);
	$tissueList = $_POST['tissue'];
	$imagefile = $_POST['image'];
	$librarystore = $library;
	$sql = stripslashes($_POST['sql']);
	$totalrows = $_POST['totalrows'];
	$list = $_POST['list'];
	//echo "Here's the imagefile: $imagefile<br>";
	//echo "Here's the list: $list<br>";
	//echo "Here's the sql: $sql<br>";
	//echo "Here's the tissues: $tissueList<br>";
	if($primaryname != ""){
		$whereCheck = 1;
		$queryparams = " WHERE primaryname LIKE '%$primaryname%'";
	}
	if($refseq != ""){
		if($whereCheck == 1){
			//just append to parameter list
			$queryparams = "$queryparams AND refseq LIKE '%$refseq%'";
		}
		else{
			$whereCheck = 1;
			$queryparams = " WHERE refseq LIKE '%$refseq%'";
		}
	}


	//echo "Here are the query parameters: $queryparams";
if($list == "all"){

?>
<td align="left" valign="top" height="10%">
<table width=640>

<tr valign="top">
<td>

	<table width="640" valign="top" height="10%" align="left" border="1" bgcolor="9999FF" >

	<tr>
	<td colspan=10><b>Your query parameters:</b></td>
	</tr>
	<tr bgcolor="FFFF99">
 	<td width="25%" align="right"><b>Primary Name:</b></td><td width="25%"><?php echo $primaryname; ?></td><td width="25%" align="right"><b>Refseq:</b></td><td width="25%"><?php echo $refseq; ?></td>
	</tr>
	<tr bgcolor="FFFF99">
 	<td width="25%" align="right"></td><td width="25%">ALL LIBRARIES</td><td width="25%" align="right"><b>Number Returned:</b></td><td width="25%"><?php echo $totalrows; ?></td>
	</tr>

	</table>
</td>
<tr>
<td>


<?php

		$returnResult = mysql_query($sql, $db);
		//echo "Here is the sql: $sql";
?>
<tr>
<td>
<table width="100%" border="1">
<tr bgcolor="9999FF"><td><CENTER><B>REFSEQ</B></CENTER></td><td colspan=2><CENTER><B>Primary Name</B></CENTER></td>
<?php
	// get all of the library short names.....
	$alllibnamesSQL = "SELECT shortname from estnames where showest = 'Y' order by id";

		$alllibnamesResult = mysql_query($alllibnamesSQL, $db);
		while($row = mysql_fetch_row($alllibnamesResult)){
			$alllibname = $row[0];
			echo "<td><CENTER><B>$alllibname</B></CENTER></td>";
		}
?>
</tr>
<?php
$bgcolorcount = 0;
while(list($refseq, $primaryname, $controlkidneyfreq, $controllungfreq, $ctfreq, $ctrafreq, $ecdmsofreq, $ectcddfreq,
		$fl135freq, $fl175freq, $mcfreq, $mnfreq, $mpfreq, $mtfreq, $mwfreq, $nulllungfreq,
		$shcfreq, $shtfreq, $tc384freq, $tcddlungfreq,
		$thyconfreq, $thytcddfreq, $trafreq, $treatedkidneyfreq)
		= mysql_fetch_array($returnResult)){
		if($bgcolorcount%2){
		// number is odd..
		$bgcolor = "CCCCCC";
		}
		else{
		// number is odd...
		$bgcolor = "FFFF99";
		}
echo "<tr bgcolor=\"$bgcolor\"><td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$refseq\">$refseq</a></td><td colspan=2>$primaryname</td>
<td>$controlkidneyfreq</td><td>$controllungfreq</td><td>$ctfreq</td><td>$ctrafreq</td><td>$ecdmsofreq</td><td>$ectcddfreq</td>
<td>$fl135freq</td><td>$fl175freq</td><td>$mcfreq</td><td>$mnfreq</td><td>$mpfreq</td><td>$mtfreq</td>
<td>$mwfreq</td><td>$nulllungfreq</td><td>$shcfreq</td><td>$shtfreq</td><td>$tc384freq</td>
<td>$tcddlungfreq</td><td>$thyconfreq</td><td>$thytcddfreq</td><td>$trafreq</td><td>$treatedkidneyfreq</td>

</tr>";
	$bgcolorcount++;
	}//end of while(list($refseq,....
	}//end if(library == "")....
	
//###########################################################################################

else{ //list != "all"

?>


<td align="left" valign="top" height="10%">
<table width=640>

<tr valign="top">
<td>

	<table width="640" valign="top" height="10%" align="left" border="1" bgcolor="9999FF" >

	<tr>
	<td colspan=10><b>Your query parameters:</b></td>
	</tr>
	<tr bgcolor="FFFF99">
 	<td width="25%" align="right"><b>Primary Name:</b></td><td width="25%"><?php echo $primaryname; ?></td>
	<td width="25%" align="right"><b>Refseq:</b></td><td width="25%"><?php echo $refseq; ?></td>
	</tr>
	<tr bgcolor="FFFF99">
 	<td width="25%" align="right"><b>Libraries:</b></td><td width="25%">
	<?php
	foreach($library as $libabbrev)
	{
		$libnamesSQL = "SELECT estname from estnames where abbrev = '$libabbrev'";

		$libnamesResult = mysql_query($libnamesSQL, $db);
		$row = mysql_fetch_row($libnamesResult);
		$libname = $row[0];
		echo "$libname<br>";
	}
?></td><td width="25%" align="right"><b>Number Returned:</b></td><td width="25%"><?php echo $totalrows; ?></td>
	</tr>
	<tr bgcolor="FFFF99">
	<td align="right">
	<b>Tissues</b>
	<td><?php
	echo $tissueList;
	?></td>
	<td colspan=3></td>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>

<table width="640" border="1">
<?php
	if($image != ""){
?>
		<tr>
<td>
<img src='<?php echo "./IMAGES/$image"; ?>'border=0 align=center width=700 height=400>

</td>

</tr>
</table>

<table align="left" valign="top" width="600">
<?php
	}
		$returnResult = mysql_query($sql, $db);
?>

<tr bgcolor="9999FF"><td><CENTER><B>REFSEQ</B></CENTER></td><td colspan=2><CENTER><B>Primary Name</B></CENTER></td>
<?php
	// Now need to loop through the library list...
	/*$countz = 0;
	while($countz < $numlibs){
		echo "<td><CENTER><B>$library[$countz]</B></CENTER></td>";
		$countz++;
	}*/
	foreach($library as $libabbrev)
	{
		$libnamesSQL = "SELECT shortname from estnames where abbrev = '$libabbrev'";

		$libnamesResult = mysql_query($libnamesSQL, $db);
		$row = mysql_fetch_row($libnamesResult);
		$libname = $row[0];
		echo "<td><CENTER><B>$libname</B></CENTER></td>";
	}
	echo "</tr>";
?>

<?php
$bgcolorcount = 0;
		while($row = mysql_fetch_row($returnResult)){
		if($bgcolorcount%2){
		// number is odd..
		$bgcolor = "CCCCCC";
		}
		else{
		// number is odd...
		$bgcolor = "FFFF99";
		}
		$refseq = $row[0];
		$primaryname = $row[1];
		$name = str_replace("\"", "", $primaryname);
		echo "<tr bgcolor=\"$bgcolor\"><td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$refseq\">$refseq</a></td>
			<td colspan=2>$name</td>";

		// Now need to loop through the library list...
		$countz = 2;
		// have to add 2 two $numlibs to compensate for the two indices preceding the first frequency...
		while($countz < ($numlibs+2)){
			$freq = $row[$countz];
			//$freq = round($freq,5);
			//$freq = $freq * 100;
			$freq = "$freq%";
			echo "<td>$freq</td>";
			$countz++;
		}
		echo "</tr>";

		$bgcolorcount++;
		}
?>

<?php
} //end else list != "all"

?>


</td>
</tr>
</table>
</body>
</html>

