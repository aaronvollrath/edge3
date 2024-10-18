<?php
require 'edge_db_connect2.php';
include ("jpgraph.php");
include ("jpgraph_bar.php");
// include("jpgraph_log.php" );
 include ("jpgraph_line.php");
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}

require './phpinc/edge3_db_connect.inc';

include 'edge_update_user_activity.inc';

//#############################################
// VALUES ASSOCIATED W/ GRAPH IMAGES
//#############################################
// Global Settings for image size...
$width = 800;
$height = 300;
//  Assign image name before the table print option stuff so that the filename can be passed through
$imagenum = rand(0, 2500000);
$filename = "image".$imagenum."id_".$userid.".png";
$imap = "myimagemap$imagenum";
?>
<?php
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout" />
<title>EDGE^2</title>

<script language="JavaScript">
// check name field
// Checking for username input and email validity on the client side....
function checkLibs_Clones()
{
   var libcheckboxChecked = false;
   var clonecheckboxChecked = false;
   dml = document.forms['estclone'];
   len = dml.elements.length;
   for (var i=0, j=len; i<j; i++) {
       myType = dml.elements[i].type;
       myName = dml.elements[i].name;

       if (myType == 'checkbox') {
       	  if(dml.elements[i].checked){
       	  if(myName.match("lib")){
            libcheckboxChecked = true;
          }
          if(myName.match("check")){
       	    clonecheckboxChecked = true;
          }
	  }
       }
   }

   if (!libcheckboxChecked){
       alert('Please select at least one library.');
       return false;
   }
   if (!clonecheckboxChecked){
       alert('Please select at least one clone.');
       return false;
   }


   return true;
}

function countLibs(){
var clonecheckboxChecked = false;
var clonecount = 0;
   for (var i=0, j=estclone.elements.length; i<j; i++) {
       myType = estclone.elements[i].type;
       myName = estclone.elements[i].name;
		$libname = $row[0];

       if (myType == 'checkbox') {
       	  if(estclone.elements[i].checked){
          	if(myName.match("check")){
       	    		clonecheckboxChecked = true;
	    		clonecount++;
          	}
	  }
       }
   }
   alert("Here's clonecount: " + clonecount);
   document.estclone.numclones = clonecount;

}
</script>
</head>

<body>

	<?php
		include 'banner.inc';
	?>

 <div class="boxmiddle">
  <?php
include 'questionmenu.inc';
?>
 	<h3 class="contenthead">EST Frequencies Query</h3>
<?php
$sub = $_POST['submit'];
$est = $_POST['estclone'];
//echo "Here is post[submit]: $sub<br>";
//echo "Here is post[estclone]: $est<br>";
// &&$sub == "Run Query"
if (isset($_POST['submit'])) { // if form has been submitted
//echo " in submit section...<br>";
	// Need to determine the parameters....
	$queryparams = "";
	$whereCheck = 0;
	$primaryname = $_POST['primaryname'];
	//echo "Here's $primaryname";
	//used to store this value to pass to next page...need because stupidly reuse name later
	$primarynamestore = $primaryname;
	$refseq = $_POST['refseq'];
	//used to store this value to pass to next page...another fine example of poor programming
	$refseqstore = $refseq;
	$library = $_POST['library'];
	$tissue = $_POST['tissue'];
	$numlibs = count($library);
	$numtissues = count($tissue);
	//echo "here's the # of elements in library: $numlibs<br>";
	//echo "here's the # of elements in tissue: $numtissues<br>";
	$librarystore = $library;
	// This is what row we are on....
	$currentrow = $_POST['currentRow'];
	//echo "After posting here's currentrow: $currentrow <br>";
	$shownumrows = $_POST['shownumrows'];



$startrow=$currentrow + 1;
$lastrow="";

	$primarylist = "";
	$refseqlist = "";

	if($primaryname != ""){
		//echo "primaryname not null";
		$whereCheck = 1;
		// now need to go through primary name, if there are colons, need to create an array and use to
		// search across entries.
		$pos = strpos($primaryname, ",");
		if ($pos === false) { // note: three equal signs
   	 		// not found...don't need to create the array...
			$queryparams = " WHERE primaryname LIKE '%$primaryname%'";
			$primarylist = $primaryname;
		}
		else{
			$queryparams = " WHERE primaryname LIKE";
			// Create the array of primary names to search on...
			$primarynamearray = array();
			$primarynamearray = explode(",", $primaryname);
			$countcheck = 0;
			$primaryarraynum = count($primarynamearray);
			foreach($primarynamearray as $namevalue){
				if($countcheck < $primaryarraynum - 1){
					$queryparams .= " '%$namevalue%' OR primaryname LIKE ";
					// used to display in query parameters table...
					$primarylist .= "'$namevalue' OR ";
				}
				else{
					$queryparams .= " '%$namevalue%' ";
					// used to display in query parameters table...
					$primarylist .= "'$namevalue'";
				}
				$countcheck++;
			}
		}
		//echo "Here's primarylist: $primarylist";
	}

	if($refseq != ""){
		if($whereCheck == 1){

			$pos = strpos($refseq, ",");
			if ($pos === false) { // note: three equal signs
   	 			// not found...don't need to create the array...
				//just append to parameter list
				$queryparams = "$queryparams OR refseq LIKE '%$refseq%'";
				$refseqlist = $refseq;
			}
			else{
				$queryparams = "$queryparams OR refseq LIKE";
				// Create the array of primary names to search on...
				$refseqlistarray = array();
				$refseqlistarray = explode(",", $refseq);
				$countcheck = 0;
				$refseqcount = count($refseqlistarray);
				foreach($refseqlistarray as $namevalue){
					if($countcheck < $refseqcount - 1){
						$queryparams .= " '%$namevalue%' OR refseq LIKE ";
						$refseqlist .= "'$namevalue' OR ";
					}
					else{
						$queryparams .= " '%$namevalue%' ";
						$refseqlist .= "'$namevalue'";
					}
					$countcheck++;
				}
			}
		}
		else{
			$whereCheck = 1;

			$pos = strpos($refseq, ",");
			if ($pos === false) { // note: three equal signs
   	 			// not found...don't need to create the array...
				$queryparams = " WHERE refseq LIKE '%$refseq%'";
				$refseqlist = $refseq;
			}
			else{
				$queryparams = " WHERE refseq LIKE ";
				// Create the array of primary names to search on...
				$refseqlistarray = array();
				$refseqlistarray = explode(",", $refseq);
				$countcheck = 0;
				$refseqcount = count($refseqlistarray);
				foreach($refseqlistarray as $namevalue){
					if($countcheck < $refseqcount - 1){
						$queryparams .= " '%$namevalue%' OR refseq LIKE ";
						$refseqlist .= "'$namevalue' OR ";
					}
					else{
						$queryparams .= " '%$namevalue%' ";
						$refseqlist .= "'$namevalue'";
					}
					$countcheck++;
				}
			}
		}

	}

if($library[0] == "" && $tissue[0]== ""){


		$rslookupSQL = "Select count(*) from est $queryparams ";
		//echo "Here's the query: $rslookupSQL<br>";
		$returnResult = mysql_query($rslookupSQL, $db);
		$row = mysql_fetch_array($returnResult);
		$totalrows = $row[0];

?>

<?php
	//echo "in all libraries...";
	$librarytext="ALL LIBRARIES";
	$params = ""; // reset the params variable
	$rslookupSQL = "Select count(*) from est $queryparams ";
		$returnResult = mysql_query($rslookupSQL, $db);
		$row = mysql_fetch_row($returnResult);
	if($shownumrows != "all"){
		// *****************************************************
			require "nextprev.inc";
		// *****************************************************
	}
	$returnSQL = "SELECT cloneid, refseq, primaryname, round(controlkidneyfreq,5), round(controllungfreq,5),round(ctfreq,5), round(ctrafreq,5), round(ecdmsofreq,5), round(ectcddfreq,5), ";
		$returnSQL .= "round(fl135freq,5), round(fl175freq,5), round(mcfreq,5), round(mnfreq,5), round(mpfreq,5), round(mtfreq,5), round(mwfreq,5), ";
		$returnSQL .= "round(nulllungfreq,5), round(shcfreq,5), round(shtfreq,5), round(tc384freq,5), round(tcddlungfreq,5), ";
		$returnSQL .= "round(thyconfreq,5), round(thytcddfreq,5), round(trafreq,5), round(treatedkidneyfreq,5) ";
		$returnSQL .= "from est $queryparams ORDER BY cloneid ASC $params";
	$printSQL = "SELECT refseq, primaryname, round(controllungfreq,5),round(ctfreq,5),round(ctfreq,5), round(ctrafreq,5), round(ecdmsofreq,5), round(ectcddfreq,5), ";
		$printSQL .= "round(fl135freq,5), round(fl175freq,5), round(mcfreq,5), round(mnfreq,5), round(mpfreq,5), round(mtfreq,5), round(mwfreq,5), ";
		$printSQL .= "round(nulllungfreq,5), round(shcfreq,5), round(shtfreq,5), round(tc384freq,5), round(tcddlungfreq,5), ";
		$printSQL .= "round(thyconfreq,5), round(thytcddfreq,5), round(trafreq,5), round(treatedkidneyfreq,5) ";
		$printSQL .= "from est $queryparams ORDER BY cloneid ASC";
?>

	<p>
	<table id="cdnaesttable" width="600">
		<tr>
		<td class="bluegray" colspan="100%"><b>Your query parameters:</b></td>
		</tr>
		<tr>
		<td class="questionparameter" width="40" align="right"><b>Primary Name:</b></td>
		<td class="questionanswer" width="300"><?php echo $primarylist; ?></td>
		<td class="questionparameter" width="40" align="right"><b>Refseq:</b></td>
		<td class="questionanswer" width="100"><?php echo $refseqlist; ?></td>
		<td rowspan=2>	<table><tr bgcolor="FFFF99"><td width="50%"><FORM METHOD="POST" ACTION="estprint.php" target="_blank">
		<input name="primaryname" type="hidden" value="<?php echo "$primaryname"; ?>">
		<input name="refseq" type="hidden" value="<?php echo "$refseq"; ?>">
		<input name="sql" type="hidden" value="<?php echo "$printSQL"; ?>">
		<input name="library" type="hidden" value="<?php echo "$library"; ?>">
		<input name="tissue" type="hidden" value="<?php echo $tissueList; ?>">
		<input name="totalrows" type="hidden" value="<?php echo "$totalrows"; ?>">
		<input name="list" type="hidden" value="all">
		<INPUT TYPE="submit" VALUE="Printable Table">
		</FORM></td></tr></table></td>

		</tr>
		<tr>
		<td class="questionparameter" width="40" align="right"><b>Libraries:</b></td>
		<td class="questionanswer" width="100"><font color="red"><b><?php echo $librarytext; ?></b></font></td>
		<td class="questionparameter" width="40" align="right">
		<b>Number Returned:</b></td><td class="questionanswer" width="100"><?php echo $totalrows; ?></td>
		</tr>

	</table>
	</p>
	<p>
	<table id="results" >
		<tr class="nextprev">
<?php
//############################################################################
		//echo "Here is the sql: $returnSQL";
	//****************************************************************
	//**#####################################################################
		if($showprev == 1){

?>

			<form action="<?PHP echo($PHP_SELF); ?>" METHOD="post">
			<td><center><input type="submit" name="submit" value="<--PREV"></center></td>
			<input name="currentRow" type="hidden" value="<?php echo "$previousrow";?>">
			<input name="shownumrows" type="hidden" value="<?php echo "$shownumrows"; ?>">
			<input name="primaryname" type="hidden" value="<?php echo "$primarynamestore"; ?>">
			<input name="refseq" type="hidden" value="<?php echo "$refseqstore"; ?>">

			</form>
<?php
		} // End show previous button
		if($shownext == 1){
			echo "<TD>";
?>
			<form action="<?PHP echo($PHP_SELF); ?>" METHOD="post">
			<center><input type="submit" name="submit" value="NEXT-->"></center>
			<input name="currentRow" type="hidden" value="<?php echo "$currentrow";?>">
			<input name="shownumrows" type="hidden" value="<?php echo "$shownumrows"; ?>">
			<input name="primaryname" type="hidden" value="<?php echo "$primarynamestore"; ?>">
			<input name="refseq" type="hidden" value="<?php echo "$refseqstore"; ?>">

			</form>
<?php
		echo "</TD>";
		}
if($totalrows < $currentrow){
			$currentrow = $totalrows;
		}
if($totalrows < $shownumrows){
	$currentrow = $totalrows;
}
if($shownumrows != "all"){
	if($startrow == $currentrow){
		echo "<td colspan=6><b>Currently displaying row $currentrow</b></td>";
	}
	elseif($totalrows==0){
		echo "<td colspan=6><b><font color=\"red\">Your selection criteria returned nothing.</font></b></td>";
	}
	else{
	echo "<td colspan=6><b>Currently displaying rows $startrow through $currentrow</b></td>";
	}

}
else{
	if($totalrows > 1){
		echo "<td colspan=5><b>Currently displaying all $currentrow rows.</b></td>";
	}
	elseif($totalrows == 0){
		echo "<td colspan=6><b><font color=\"red\">Your selection criteria returned nothing.</font></b></td>";
	}
	else{
		echo "<td colspan=6><b>Displaying the single selection matching your criteria.</b></td>";
	}
	$shownumrows = $totalrows;
}

?>



</tr>
<?php

	// Get the number of libs for the estclone form....
	$countlibsSQL = "SELECT count(*) from estnames where showest = 'Y'";
	$numlibsReturn = mysql_query($countlibsSQL,$db);
	$row = mysql_fetch_row($numlibsReturn);
	$numlibs = $row[0];
?>


<form name="estclone" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return checkLibs_Clones()">
<input type="hidden" name="libcount" value="<?php echo $numlibs; ?>">
<tr>
<td class="colhead"><center>View<br>Selected<br>Clones/Libs?</center>
<input type="submit" value="Click Here" name="estclone"></td>
<td class="colhead"><CENTER>REFSEQ</CENTER></td><td  class="colhead" width=100 colspan=2><CENTER>Primary Name</CENTER></td>
<?php
	// get all of the library short names.....
	$alllibnamesSQL = "SELECT shortname, abbrev from estnames where showest = 'Y' order by id";
		$libcounter = 0;
		$alllibnamesResult = mysql_query($alllibnamesSQL, $db);
		while($row = mysql_fetch_row($alllibnamesResult)){
			$alllibname = $row[0];
			$abbrev = $row[1];
			echo "<td  class=\"colhead\" valign=\"bottom\"><CENTER>$alllibname
			<br><input type=checkbox CHECKED name=\"lib$libcounter\" value=\"$abbrev\">
			</CENTER></td>";
		$libcounter++;
		}
?>

</tr>
<?php

$returnResult = mysql_query($returnSQL, $db);
$bgcolorcount = 0;
while(list($cloneid, $refseq, $primaryname, $controlkidneyfreq, $controllungfreq, $ctfreq, $ctrafreq, $ecdmsofreq, $ectcddfreq,
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
		$bgcolor = "nextp";
		}
		$name = str_replace("\"", "", $primaryname);
echo "<tr bgcolor=\"$bgcolor\">
<td><center><input type=checkbox name=\"check$bgcolorcount\" value=\"$cloneid\"></center></td>
<td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$refseq\" target=\"_blank\">$refseq</a></td><td  width=100 colspan=2>$name</td>";
if($controlkidneyfreq > 0.0){
	$class="gtzero";
}
else{
	$class="number";
}
echo "<td class=$class>";
echo round($controlkidneyfreq,5)*100;
echo"%</td>";
if($controllungfreq > 0.0){
	$class="gtzero";
}
else{
	$class="number";
}
echo "<td class=$class>";
echo round($controllungfreq,5)*100;
echo "%</td>";
if($ctfreqfreq > 0.0){
	$class="gtzero";
}
else{
	$class="number";
}
echo "<td class=$class>";
echo round($ctfreq,5)*100;
echo "%</td>";
if($ctrafreq > 0.0){
	$class="gtzero";
}
else{
	$class="number";
}
echo "<td class=$class>";
echo round($ctrafreq,5)*100;
echo "%</td>";
if($ecdmsofreq > 0.0){
	$class="gtzero";
}
else{
	$class="number";
}
echo "<td class=$class>";
echo round($ecdmsofreq,5)*100;
echo "%</td>";

if($ectcddfreq > 0.0){
	$class="gtzero";
}
else{
	$class="number";
}
echo "<td class=$class>";
echo round($ectcddfreq,5)*100;
echo "%</td>";
if($fl135freq > 0.0){
	$class="gtzero";
}
else{
	$class="number";
}
echo "<td class=$class>";
echo round($fl135freq,5)*100;
echo "%</td>";
if($fl175freq > 0.0){
	$class="gtzero";
}
else{
	$class="number";
}
echo "<td class=$class>";
echo round($fl175freq,5)*100;
echo "%</td>";
if($mcfreq > 0.0){
	$class="gtzero";
}
else{
	$class="number";
}
echo "<td class=$class>";
echo round($mcfreq,5)*100;
echo "%</td>";
if($mnfreq > 0.0){
	$class="gtzero";
}
else{
	$class="number";
}
echo "<td class=$class>";
echo round($mnfreq,5)*100;
echo "%</td>";
if($mpfreq > 0.0){
	$class="gtzero";
}
else{
	$class="number";
}
echo "<td class=$class>";
echo round($mpfreq,5)*100;
echo "%</td><td class=\"number\">";
echo round($mtfreq,5)*100;
echo "%</td><td class=\"number\">";
echo round($mwfreq,5)*100;
echo "%</td><td class=\"number\">";
echo round($nulllungfreq,5)*100;
echo "%</td><td class=\"number\">";
echo round($shcfreq,5)*100;
echo "%</td><td class=\"number\">";
echo round($shtfreq,5)*100;
echo "%</td><td class=\"number\">";
echo round($tc384freq,5)*100;
echo "%</td><td class=\"number\">";
echo round($tcddlungfreq,5)*100;
echo "%</td><td class=\"number\">";
echo round($thyconfreq,5)*100;
echo "%</td><td class=\"number\">";
echo round($thytcddfreq,5)*100;
echo "%</td><td class=\"number\">";
echo round($trafreq,5)*100;
echo "%</td><td class=\"number\">";
echo round($treatedkidneyfreq,5)*100;
echo "%</td></tr>";
	$bgcolorcount++;
	}//end of while(list($refseq,....

echo "<input name=\"shownumrows\" type=\"hidden\" value=\"$shownumrows\">
<input name=\"library\" type=\"hidden\" value=$librarystore>";
echo "</form>";
?>


<?php
	}//end if(library == "") || (tissue = "")....



// #################################################################################
// #################################################################################
// ##############          SELECTED LIBRARIES OR TISSUES.....
// #################################################################################
// #################################################################################



else{ //library != ""
?>

<?php
		$rslookupSQL = "Select count(*) from est $queryparams ";
		//echo "Here's the query: $rslookupSQL<br>";
		$returnResult = mysql_query($rslookupSQL, $db);
		$row = mysql_fetch_array($returnResult);
		$totalrows = $row[0];
		//echo "<br>total rows = $totalrows<br>";

		// Used for passing image filename to the print table script...
	if($shownumrows <= 20 && $shownumrows > 0 || $totalrows <=20){
		$image = $filename;
	}
	else{
		$image = "";
	}
		$params = ""; // reset the params variable
	//if($shownumrows != "all"){
		//echo "<br>shownumrows != all <br>";

		// *****************************************************
			require "nextprev.inc";
		// *****************************************************
	//}
	// Check to see if there are any tissues selected.  if so, get the libraries
	// associated.  if so, check to see if this library exists in the library array
	// already.  if not, add to array, else don't add.
	if($numtissues != 0){
		// Variable which stores tissue list...
		$tissueList = "";
		foreach($tissue as $tissuetype){
			$tissueList .= "$tissuetype  ";
			$tissuelibnamesSQL = "SELECT abbrev from estnames where esttissue = '$tissuetype'";
			$tissuelibnamesResult = mysql_query($tissuelibnamesSQL, $db);
			if($numlibs == 0){
					$library = array();
			}
			while($row = mysql_fetch_row($tissuelibnamesResult)){
				$tissuelib = $row[0];
				// is tissue lib already present in library???
				if(!in_array($tissuelib, $library)){
					//echo "$tissuelib not in library<br>";
					//$library[$numlibs] = $tissuelib;
					array_push($library, $tissuelib);
					// increment libs....
					$numlibs++;
				}
			}


		}

	}
	// reaquire numlibs...
	$numlibs = count($library);
	// Get the list of libraries....
	$libcounter = 0;
	$libList = "";
	while($libcounter < $numlibs){
		if($libcounter == ($numlibs - 1)){
			$commaVal = "";
		}
		else{
			$commaVal = ",";
		}
		$libList .= "round($library[$libcounter],5)*100$commaVal";
		$libcounter++;
	}
	// Always order by the first library......for now...
	$returnSQL = "SELECT cloneid, refseq, primaryname, $libList from est $queryparams ORDER by $library[0] DESC $params";
	$printSQL = "SELECT refseq, primaryname, $libList from est $queryparams ORDER by $library[0] DESC";
		//$returnResult = mysql_query($returnSQL, $db);
//****************************************************************
	//**#####################################################################
?>


	<table width="400" id="cdnaesttable">

	<tr>
	<td class="bluegray" colspan="100%"><b>Your query parameters:</b></td>
	</tr>
	<tr>
 	<td class="questionparameter" width="40" align="right"><b>Primary Name:</b></td>
	<td class="questionanswer" width="350"><?php echo $primarylist; ?></td>
	<td class="questionparameter" width="40" align="right"><b>Refseq:</b></td>
	<td class="questionanswer" width="150"><?php echo $refseqlist; ?></td>
<td rowspan=2 width="30">	<table><tr bgcolor="FFFF99"><td width="50%"><FORM METHOD="POST" ACTION="estprint.php" target="_blank">
<input name="primaryname" type="hidden" value="<?php echo "$primarylist"; ?>">
<input name="refseq" type="hidden" value="<?php echo "$refseqlist"; ?>">
<?php
				// This is used to pass the library list as an array....
				$counter = 0;
				while($counter < $numlibs){
				echo"<input name=\"library[$counter]\" type=\"hidden\" value=\"$library[$counter]\">";
				$counter++;
				}
			?>
<input name="image" type="hidden" value="<?php echo "$image"; ?>">
<input name="totalrows" type="hidden" value="<?php echo "$totalrows"; ?>">
<input name="sql" type="hidden" value="<?php echo "$printSQL"; ?>">
<input name="tissue" type="hidden" value="<?php echo $tissueList; ?>">
<input name="list" type="hidden" value="one">
<INPUT TYPE="submit" VALUE="Print">
</FORM></td></tr></table></td>
	</tr>
	<tr>
 	<td class="questionparameter" width="40" align="right"><b>Libraries:</b></td><td class="questionanswer" width="350">
	<?php
	$librarynamesarray = array();
	foreach($library as $libabbrev)
	{
		$libnamesSQL = "SELECT estname from estnames where abbrev = '$libabbrev'";

		$libnamesResult = mysql_query($libnamesSQL, $db);
		$row = mysql_fetch_row($libnamesResult);
		$libname = $row[0];
		echo "$libname<br>";
		array_push ($librarynamesarray, $libname);
	}
	?>
	</td>
	<td class="questionparameter" width="40" align="right"><b>Number Returned:</b></td>
	<td class="questionanswer" width="100"><?php echo $totalrows; ?></td>
	</tr>
	<tr bgcolor="FFFF99">
	<td class="questionparameter" ><b>Tissues:</b></td>
	<td class="questionanswer" ><?php echo $tissueList; ?></td>
	</tr>

</table>


<?php
if($totalrows > 0){
if(($shownumrows <= 10 && $shownumrows > 0) || ($totalrows <= 10 && $totalrows > 0)){
//echo "Here is currentrow: $currentrow<br>";
?>
<?php require 'estgraph2.inc'; ?>
<?php
//#############################################################################
// GRAPH STUFF....
//#############################################################################

//echo "Here's the filename: $filename<br>"; ?>

<img src='<?php echo "./IMAGES/$filename"; ?>' ISMAP USEMAP="#<?php echo $imap; ?>" border=0 align =center width=<?php echo $width; ?> height=<?php echo $height; ?>>
<?php
}
else{

echo "<tr><td><b>Graph will not be displayed.</b></td></tr>";
}
}
else{
echo "<tr><td><b>Graph will not displayed.</b></td></tr>";
}
?>

<table id="results" width="600">
<?php
	//echo "Here is totalrows: $totalrows<br>";
	//echo "Here is currentrow: $currentrow<br>";
	if($totalrows < $currentrow){
			$currentrow = $totalrows;
		}
	if($totalrows < $shownumrows){
	$currentrow = $totalrows;
}
echo "<tr>";
if($shownumrows != "all"){
	if($startrow == $currentrow){
		echo "<td colspan=5><b>Currently displaying row $currentrow</b></td>";
	}
	elseif($totalrows == 0){
		echo "<td colspan=5><b>Your selection criteria returned nothing.</b></td>";
	}
	else{
	echo "<td colspan=5><b>Currently displaying rows $startrow through $currentrow</b></td>";
	}
}
else{
	if($totalrows > 1){
		echo "<td colspan=5><b>Currently displaying all $totalrows rows.</b></td>";
	}
	elseif($totalrows == 0){
		echo "<td colspan=5><b>Your selection criteria returned nothing.</b></td>";
	}
	else{
		echo "<td colspan=5><b>Displaying the single selection matching your criteria.</b></td>";
	}
	// SEt this to be total rows....
	$shownumrows = $totalrows;
}
?>
<tr>

<?php

		if($showprev == 1){

  	//echo "<BR><BR>HERE is the value of currentrow: $currentrow<BR>";
			//echo "<bR>Here is the value of previousrow: $previousrow<br>";
			echo "<td width=50>";
?>

			<form action="<?PHP echo($PHP_SELF); ?>" METHOD="post">

			<input type="submit" name="submit" value="<--PREV">
			<input name="currentRow" type="hidden" value="<?php echo "$previousrow";?>">
			<input name="shownumrows" type="hidden" value="<?php echo "$shownumrows"; ?>">
			<input name="primaryname" type="hidden" value="<?php echo "$primarynamestore"; ?>">
			<input name="refseq" type="hidden" value="<?php echo "$refseqstore"; ?>">
			<?php
				// This is used to pass the library list as an array....
				$counter = 0;
				while($counter < $numlibs){
				echo"<input name=\"library[$counter]\" type=\"hidden\" value=\"$library[$counter]\">";
				$counter++;
				}
				// This is used to pass the tissue list as an array....
				$counter = 0;
				while($counter < $numtissues){
				echo "<input name=\"tissue[$counter]\" type=\"hidden\" value=\"$tissue[$counter]\">";
				$counter++;
				}
			?>
			</form>
<?php
		//echo "</TD>";
		} // End show previous button

		if($shownext == 1){
			if($showprev==1){
				echo "<TD width=50>";
			}
			else{
				echo "<td width=50>";
			}
?>
			<form action="<?PHP echo($PHP_SELF); ?>" METHOD="post">
			<input type="submit" name="submit" value="NEXT-->">
			<input name="currentRow" type="hidden" value="<?php echo "$currentrow";?>">
			<input name="shownumrows" type="hidden" value="<?php echo "$shownumrows"; ?>">
			<input name="primaryname" type="hidden" value="<?php echo "$primarynamestore"; ?>">
			<input name="refseq" type="hidden" value="<?php echo "$refseqstore"; ?>">
			<?php
				$counter = 0;
				while($counter < $numlibs){
				echo"<input name=\"library[$counter]\" type=\"hidden\" value=\"$library[$counter]\">";
				$counter++;
				}
				// This is used to pass the tissue list as an array....
				$counter = 0;
				while($counter < $numtissues){
				echo "<input name=\"tissue[$counter]\" type=\"hidden\" value=\"$tissue[$counter]\">";
				$counter++;
				}
			?>
			</form>

<?php

		echo "</TD>";
		echo "</tr>";
		}

//############################################################################

		//$returnSQL = "SELECT refseq, primaryname, $library from est $queryparams ORDER by $library DESC $params";
		$returnResult = mysql_query($returnSQL, $db);
		//echo "Here is the query: $returnSQL";
?>

</tr>
<form name="estclone" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return checkLibs_Clones()">
<input type="hidden" name="libcount" value="<?php echo $numlibs; ?>">
<input type="hidden" name="clonecount" value="">
<tr><td class="colhead"><center><b>View<br>Selected<br>Clones/Libs?<br></center>
<input type="submit" value="Click Here" name="estclone"></td><td class="colhead"><CENTER>REFSEQ</CENTER></td>
<td colspan=2 class="colhead"><CENTER>Primary Name</CENTER></td>

<?php
	// Now need to loop through the library list...
	
	$libcounter = 0;
	foreach($library as $libabbrev)
	{
		$libnamesSQL = "SELECT shortname from estnames where abbrev = '$libabbrev'";

		$libnamesResult = mysql_query($libnamesSQL, $db);
		$row = mysql_fetch_row($libnamesResult);
		$libname = $row[0];
		echo "<td class=\"colhead\"><CENTER>$libname
		<br><input type=checkbox CHECKED name=\"lib$libcounter\" value=\"$libabbrev\"></CENTER></td>";
		$libcounter++;
	}



?>
</tr>
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
		$cloneid = $row[0];
		$refseq = $row[1];
		$primaryname = $row[2];
		$name = str_replace("\"", "", $primaryname);
		echo "<tr bgcolor=\"$bgcolor\"><td><center><input type=checkbox name=\"check$bgcolorcount\" value=\"$cloneid\"></center></td>
		<td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$refseq\" target=\"_blank\">$refseq</a></td>
			<td colspan=2>$name</td>";

		// Now need to loop through the library list...
		$countz = 3;
		// have to add 2 two $numlibs to compensate for the two indices preceding the first frequency...
		while($countz < ($numlibs+3)){
			$freq = $row[$countz];
			//$freq = round($freq,5);
			//$freq = $freq * 100;
			if($freq > 0.0){
				$class="gtzero";
			}
			else{
				$class="number";
			}
			$freq = "$freq%";
			echo "<td class=$class>$freq</td>";
			$countz++;
		}
		echo "</tr>";

		$bgcolorcount++;
		}
?>
<input name="shownumrows" type="hidden" value="<?php echo "$shownumrows"; ?>">
<input name="primaryname" type="hidden" value="<?php echo "$primarynamestore"; ?>">
			<input name="refseq" type="hidden" value="<?php echo "$refseqstore"; ?>">
<?php
				// This is used to pass the library list as an array....
				$counter = 0;
				while($counter < $numlibs){
				echo"<input name=\"library[$counter]\" type=\"hidden\" value=\"$library[$counter]\">";
				$counter++;
				}
				//This is used to pass the tissue list as an array...
				$counter = 0;
				while($counter < $numtissues){
				echo"<input name=\"tissue[$counter]\" type=\"hidden\" value=\"$tissue[$counter]\">";
				$counter++;
				}

?>

</form>
<tr>


<?php
	} //end else library != ""
		if($showprev == 1){
			echo "<TD>";
			//echo "<BR><BR>HERE is the value of currentrow: $currentrow<BR>";
			//echo "<bR>Here is the value of previousrow: $previousrow<br>";
?>

			<form action="<?PHP echo($PHP_SELF); ?>" METHOD="post">
			<table>
			<input type="submit" name="submit" value="<--PREV">
			<input name="currentRow" type="hidden" value="<?php echo "$previousrow";?>">
			<input name="shownumrows" type="hidden" value="<?php echo "$shownumrows"; ?>">
			<input name="primaryname" type="hidden" value="<?php echo "$primarynamestore"; ?>">
			<input name="refseq" type="hidden" value="<?php echo "$refseqstore"; ?>">
			<?php
				// This is used to pass the library list as an array....
				$counter = 0;
				while($counter < $numlibs){
				echo"<input name=\"library[$counter]\" type=\"hidden\" value=\"$library[$counter]\">";
				$counter++;
				}

				// This is used to pass the tissue list as an array....
				$counter = 0;
				while($counter < $numtissues){
				echo "<input name=\"tissue[$counter]\" type=\"hidden\" value=\"$tissue[$counter]\">";
				$counter++;
				}
			?>
			</table>
			</form>
<?php
		echo "</TD>";
		} // End show previous button

		if($shownext == 1){
			echo "<TD align=\"left\">";
?>
			<form action="<?PHP echo($PHP_SELF); ?>" METHOD="post">
			<table>
			<input type="submit" name="submit" value="NEXT-->">
			<input name="currentRow" type="hidden" value="<?php echo "$currentrow";?>">
			<input name="shownumrows" type="hidden" value="<?php echo "$shownumrows"; ?>">
			<input name="primaryname" type="hidden" value="<?php echo "$primarynamestore"; ?>">
			<input name="refseq" type="hidden" value="<?php echo "$refseqstore"; ?>">
			<?php
				$counter = 0;
				while($counter < $numlibs){
				echo"<input name=\"library[$counter]\" type=\"hidden\" value=\"$library[$counter]\">";
				$counter++;
				}
				// This is used to pass the tissue list as an array....
				$counter = 0;
				while($counter < $numtissues){
				echo "<input name=\"tissue[$counter]\" type=\"hidden\" value=\"$tissue[$counter]\">";
				$counter++;
				}
			?>
			</table>
			</form>

<?php
		echo "</TD>";
		}

?>

</tr>
</table>
<?php
}
elseif($est == "Click Here"){
?>

<?php
// *************************************************************
// #############################################################
// ELSE IF (INDIVIDUAL CLONES SELECTED
// #############################################################
// *************************************************************
//echo "In elseif...";
$clonecount = $_POST['clonecount'];
//echo "Here's clonecount: $clonecount<br>";
$primaryname = $_POST['primaryname'];
	//echo "Here's $primaryname";
	//used to store this value to pass to next page...need because stupidly reuse name later
	$primarynamestore = $primaryname;
	$refseq = $_POST['refseq'];
	//used to store this value to pass to next page...another fine example of poor programming
	$refseqstore = $refseq;


$primarylist = "";
$refseqlist = "";

	if($primaryname != ""){
		//echo "primaryname not null";
		$whereCheck = 1;
		// now need to go through primary name, if there are colons, need to create an array and use to
		// search across entries.
		$pos = strpos($primaryname, ",");
		if ($pos === false) { // note: three equal signs
   	 		// not found...don't need to create the array...

			$primarylist = $primaryname;
		}
		else{

			$primarynamearray = array();
			$primarynamearray = explode(",", $primaryname);
			$countcheck = 0;
			$primaryarraynum = count($primarynamearray);
			foreach($primarynamearray as $namevalue){
				if($countcheck < $primaryarraynum - 1){

					// used to display in query parameters table...
					$primarylist .= "'$namevalue' OR ";
				}
				else{

					// used to display in query parameters table...
					$primarylist .= "'$namevalue'";
				}
				$countcheck++;
			}
		}
		//echo "Here's primarylist: $primarylist";
	}

	if($refseq != ""){
		if($whereCheck == 1){

			$pos = strpos($refseq, ",");
			if ($pos === false) { // note: three equal signs
   	 			// not found...don't need to create the array...
				//just append to parameter list

				$refseqlist = $refseq;
			}
			else{
				$queryparams = "$queryparams OR refseq LIKE";
				// Create the array of primary names to search on...
				$refseqlistarray = array();
				$refseqlistarray = explode(",", $refseq);
				$countcheck = 0;
				$refseqcount = count($refseqlistarray);
				foreach($refseqlistarray as $namevalue){
					if($countcheck < $refseqcount - 1){

						$refseqlist .= "'$namevalue' OR ";
					}
					else{

						$refseqlist .= "'$namevalue'";
					}
					$countcheck++;
				}
			}
		}
		else{
			$whereCheck = 1;

			$pos = strpos($refseq, ",");
			if ($pos === false) { // note: three equal signs
   	 			// not found...don't need to create the array...

				$refseqlist = $refseq;
			}
			else{
				$queryparams = " WHERE refseq LIKE ";
				// Create the array of primary names to search on...
				$refseqlistarray = array();
				$refseqlistarray = explode(",", $refseq);
				$countcheck = 0;
				$refseqcount = count($refseqlistarray);
				foreach($refseqlistarray as $namevalue){
					if($countcheck < $refseqcount - 1){

						$refseqlist .= "'$namevalue' OR ";
					}
					else{

						$refseqlist .= "'$namevalue'";
					}
					$countcheck++;
				}
			}
		}

	}

// The immmediately following value is used when the user clicks individual libraries....
$clickedlibs = $_POST['libcount'];

$numtissues = count($tissue);
if($numtissues != 0){
		//echo "Tissue selected!!!<br>";
		//echo "number of libs: $numlibs<br>";
		// Variable which stores tissue list...
		$tissueList = "";
		foreach($tissue as $tissuetype){

			$tissueList .= "$tissuetype  ";
			$tissuelibnamesSQL = "SELECT abbrev from estnames where esttissue = '$tissuetype'";

			$tissuelibnamesResult = mysql_query($tissuelibnamesSQL, $db);
			if($numlibs == 0){

					$library = array();

			}
			while($row = mysql_fetch_row($tissuelibnamesResult)){
				$tissuelib = $row[0];
				// is tissue lib already present in library???

				if(!in_array($tissuelib, $library)){
					//echo "$tissuelib not in library<br>";
					//$library[$numlibs] = $tissuelib;
					array_push($library, $tissuelib);
					// increment libs....
					$numlibs++;
				}
			}


		}

	}
$idarray= array();
$idcount = 0;

$rows = $_POST['shownumrows'];

while ($idcount < $rows){
	//echo "in while loop, cloneid=";
	$clone = "check$idcount";
	$cloneid = $_POST[$clone];
	//echo "$cloneid<br>";
	if($cloneid != ""){
	array_push($idarray, $cloneid);
	}
	$idcount++;
}


$library = array();
$libcount = 0;

while($libcount < $clickedlibs){

	$lib = "lib$libcount";
	$libid = $_POST[$lib];

	if(isset($libid)){
		array_push($library, $libid);
	}
	$libcount++;
}

	$numlibs = count($library);
	//echo "here's the # of elements in library: $numlibs<br>";




$elements = count($idarray);

// Get the list of clones
	$clonecounter = 0;
	$cloneList = "";
	while($clonecounter < $elements){
		if($clonecounter == ($elements - 1)){
			$commaVal = "";
		}
		else{
			$commaVal = " OR ";
		}


			$cloneList .= "cloneid = $idarray[$clonecounter]$commaVal";
		$clonecounter++;
	}




// Get the list of libraries....
	$libcounter = 0;
	$libList = "";
	while($libcounter < $numlibs){
		if($libcounter == ($numlibs - 1)){
			$commaVal = "";
		}
		else{
			$commaVal = ",";
		}


			$libList .= "round($library[$libcounter],5)*100$commaVal";
		$libcounter++;
	}
$returnSQL = "SELECT cloneid, refseq, primaryname, $libList from est where $cloneList ORDER by $library[0] DESC";
$printSQL = "SELECT refseq, primaryname, $libList from est where $cloneList ORDER by $library[0] DESC";
//echo "Here's returnSQL: $returnSQL<br>";
$returnResult = mysql_query($returnSQL, $db);
?>


	<p>
	<table id="cdnaesttable" width="400">

	<tr>
	<td class="bluegray" colspan="100%"><b>Your query parameters:</b></td>
	</tr>
	<tr>
 	<td class="questionparameter" width="40" align="right"><b>Primary Name:</b></td>
	<td class="questionanswer" width="350"><?php echo $primarylist; ?></td>
	<td  class="questionparameter" width="40" align="right"><b>Refseq:</b></td>
	<td  class="questionanswer" width="150"><?php echo $refseqlist; ?></td>
<td rowspan=2 width="30">
<table><tr><td width="50%">
<FORM METHOD="POST" ACTION="estprint.php" target="_blank">
<input name="primaryname" type="hidden" value="<?php echo "$primarylist"; ?>">
<input name="refseq" type="hidden" value="<?php echo "$refseqlist"; ?>">
<?php
				// This is used to pass the library list as an array....
				$counter = 0;
				while($counter < $numlibs){
				echo"<input name=\"library[$counter]\" type=\"hidden\" value=\"$library[$counter]\">";
				$counter++;
				}

			?>
<input name="image" type="hidden" value="<?php echo "$filename"; ?>">
<input name="tissue" type="hidden" value="<?php echo $tissueList; ?>">
<input name="totalrows" type="hidden" value="<?php echo "$totalrows"; ?>">
<input name="sql" type="hidden" value="<?php echo "$printSQL"; ?>">
<input name="list" type="hidden" value="one">
<INPUT TYPE="submit" VALUE="Print">
</FORM>
</td></tr></table></td>
	</tr>
	<tr>
 	<td  class="questionparameter" width="40" align="right"><b>Libraries:</b></td>
	<td  class="questionanswer" width="350">
	<?php
	foreach($library as $libabbrev)
	{
		$libnamesSQL = "SELECT estname from estnames where abbrev = '$libabbrev'";

		$libnamesResult = mysql_query($libnamesSQL, $db);
		$row = mysql_fetch_row($libnamesResult);
		$libname = $row[0];
		echo "$libname<br>";
	}
	?>
	</td>
	<td  class="questionparameter" width="40" align="right"><b>Number Returned:</b></td>
	<td  class="questionanswer" width="100"><?php echo $clonecounter; ?></td>
	</tr>
	<tr>
	<td  class="questionparameter" align="right">
	<b>Tissues</b>
	<td class="questionanswer" ><?php
	echo $tissueList;
	?></td>
	<td colspan=3></td>
	</td>
	</tr>


</table>
</p>


<?php if($clonecounter <= 10){
?>
<?php require 'estgraph2.inc'; ?>
<?php
//#############################################################################
// GRAPH STUFF....
//#############################################################################
$returnResult = mysql_query($returnSQL, $db);
//echo "Here's the filename: $filename<br>"; ?>
<p>
<img src='<?php echo "./IMAGES/$filename"; ?>' ISMAP USEMAP="#<?php echo $imap; ?>" border=0 align =center width=<?php echo $width; ?> height=<?php echo $height; ?>>
</p>
<?php //end if($idcount < 10) {...
}
?>
<p>
<table id="results">
<form name="estclone" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return checkLibs_Clones()">
<input type="hidden" name="libcount" value="<?php echo $numlibs; ?>">
<tr bgcolor="9999FF"><td class="colhead"><center>View<br>Selected<br>Clones/Libs?<br></center><input type="submit" value="Click Here" name="estclone"></td><td class="colhead"><CENTER>REFSEQ</CENTER></td>
<td class="colhead" colspan=2><CENTER>Primary Name</CENTER></td>

<?php
	// Now need to loop through the library list...

	$libcounter = 0;
	foreach($library as $libabbrev)
	{
		$libnamesSQL = "SELECT shortname from estnames where abbrev = '$libabbrev'";

		$libnamesResult = mysql_query($libnamesSQL, $db);
		$row = mysql_fetch_row($libnamesResult);
		$libname = $row[0];
		echo "<td class=\"colhead\"><CENTER><B>$libname</B>
		<br><input type=checkbox CHECKED name=\"lib$libcounter\" value=\"$libabbrev\"></CENTER></td>";
		$libcounter++;
	}



?>
</tr>
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
		$cloneid = $row[0];
		$refseq = $row[1];
		$primaryname = $row[2];
		$name = str_replace("\"", "", $primaryname);
		echo "<tr bgcolor=\"$bgcolor\"><td><center><input type=checkbox name=\"check$bgcolorcount\" value=\"$cloneid\"></center></td>
		<td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$refseq\" target=\"_blank\">$refseq</a></td>
			<td colspan=2>$name</td>";

		// Now need to loop through the library list...
		$countz = 3;
		// have to add 2 two $numlibs to compensate for the two indices preceding the first frequency...
		while($countz < ($numlibs+3)){
			$freq = $row[$countz];
			//$freq = round($freq,5);
			//$freq = $freq * 100;
			if($freq > 0.0){
				$class="gtzero";
			}
			else{
				$class="number";
			}
			$freq = "$freq%";
			echo "<td class=$class>$freq</td>";
			$countz++;
		}
		echo "</tr>";

		$bgcolorcount++;
		}
?>
<input name="shownumrows" type="hidden" value="<?php echo "$shownumrows"; ?>">
<input name="primaryname" type="hidden" value="<?php echo "$primarynamestore"; ?>">
			<input name="refseq" type="hidden" value="<?php echo "$refseqstore"; ?>">
<?php
				// This is used to pass the library list as an array....
				$counter = 0;
				while($counter < $numlibs){
				echo"<input name=\"library[$counter]\" type=\"hidden\" value=\"$library[$counter]\">";
				$counter++;
				// This is used to pass the tissue list as an array....
				}
				$counter = 0;
				while($counter < $numtissues){
				echo "<input name=\"tissue[$counter]\" type=\"hidden\" value=\"$tissue[$counter]\">";
				$counter++;
				}
?>

</form>
<tr>

<td>
</td>
</tr>
</table>
</p>
<?php

} // End elseif($est == "view")


else{
?>


<?php
// Nothing here to populate library menu....YET
$libnamesSQL = "SELECT abbrev, estname, esttissue from estnames where showest = 'Y'";

$libnamesResult = mysql_query($libnamesSQL, $db);
$libsMenu = "";
$tissueMenu = "";
while(list($abbrev, $estname, $esttissue) = mysql_fetch_array($libnamesResult))

{
	$libsMenu .= "<option value=\"$abbrev\">$estname</option>\r";
}

// Tissue menu....
	$distincttissues = "Select distinct esttissue from estnames where showest ='Y'";
	$distincttissueResult = mysql_query($distincttissues, $db);
	while($row = mysql_fetch_row($distincttissueResult)){
		// For each tissue get a list of abbrev....
		$tissue = $row[0];
		//$libsfortissue = "SELECT abbrev from estnames where esttissue = $row[0];
		$tissueMenu .= "<option value=\"$tissue\">$tissue</option>\r";
	}


mysql_close($db);
?>


<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table id="cdnaesttable" width="400">

<thead>
<th class="header" colspan=3><a href="./Instructions/estinstr.php" target="_blank"><img  align="right"  border="0" src="./GIFs/roundbluequestion.gif" alt="Click here for instructions on how to use this query page." width="20" height="20"></a></th>
<input name="currentRow" type="hidden" value="0">
</thead>
<tr>
<td>Primary Name:</td>
<td width="40"><input  name="primaryname" type="text" size="40" ></td>
</tr>
<tr>
<td>Refseq:</td>
<td><input name="refseq" type="text" size="25">
</td>
</tr>


<tr>
<td>Tissue</td>
<td valign="top">
<select name='tissue[]' multiple size="3">
<?php echo $tissueMenu; ?>
</select>
</tr>


<tr>
<td>Library</td>
<td valign="top">
<select name='library[]' multiple size="4">

<?php echo $libsMenu; ?>
</select>
<a href="./Instructions/estinfo.php" target="_blank" ><img border="0" src="./GIFs/roundblueinfo.gif" width="20" height="20" alt="Click here for detailed information on the libraries!"></a>
</td>
</tr>
<tr>
<td colspan=4 height=10><hr size="3" ></td>
</tr>
<tr>
<td>Number to return:</td>
<td width=20>
<select name="shownumrows">
<option value="all">All</option>
<option value="1">1</option>
<option SELECTED value="10">10</option>
<option value="20">20</option>
<option	value="50">50</option>
<option value="100">100</option>
<option value="200">200</option>
</select>
</td>
</tr>
<tr>
<td class="bluegray"><center><input type="submit" name="submit" value="Run Query"></center></td>
<td class="bluegray"><center><input type="reset" value="Reset Form!"</center></td>

</tr>

</table>
</form>
<?php }

?>
 </div>
 <?php
	include 'leftmenu.inc';

?>


 <div class="boxclear"> </div>




 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>

</body>
</html>


