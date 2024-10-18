<?php
/*  this file will take in a csv file that contains a list of clone numbers and return the PRC of each...

*/
require 'edge_db_connect2.php';
include 'header.inc';
// update the condensedhybrids table...
require './phpinc/edge3_db_connect.inc';
require("fileupload-class.php");
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

function die_script($sqlerrmsg, $stage, $arraytype, $arrayid, $sql)
{
	// We need this function to remove any changes made before the sql error....
	// $errormsg is the SQL error
	// $stage is the point in which the external array submission failed...
	if($stage == 4){
		// Need to delete from exthybrids.
		echo "failed while entering data into external hybrids table...<br>";

	}
	elseif($stage ==3){
		echo "failed while entering data into external annotations table...<br>";

	}
	elseif($stage ==2){
		echo "failed while entering data into external array table...<br>";
	}
	elseif($stage == 1){
		echo "failed while entering data into external array type table...<br>";
	}else{
		echo "failed...not sure where....<br>";
	}
	echo "$sql<br>";

 print $sqlerrmsg . "</body></html>";
 die;
}

function arrayannotationinsert(&$annofile, $userid, $newplatform, $newplatformdesc, $arraytype){
$db = mysql_connect("localhost", "vollrath", "arod678cbc3");

mysql_select_db("edge", $db);
		// uploading the cy3 file
			$my_annouploader = new uploader('en'); // errors in English

			$my_annouploader->max_filesize(30000000);

			$tempfile = "annofile";
			$my_annouploader->upload($tempfile, '', '.txt');

			$prefix = '/var/www/html/edge2/IMAGES/';
			$my_annouploader->save_file($prefix, 2);

			if ($my_annouploader->error) {
				$fileError = 1;
				$fileErrorText = $my_annouploader->error;
				print($my_annouploader->error . "<br><br>\n");
			} else {
				print("Thanks for uploading " . $my_annouploader->file['name'] . "<br><br>\n");
				$file = $prefix.$my_annouploader->file['name'];


			$sql ="INSERT INTO extarraytype(arraytype, name, descr) VALUES ($arraytype, \"$newplatform\", \"$newplatformdesc\")";
			//echo "$sql<br>";
			$sql_query = mysql_query("INSERT INTO extarraytype(arraytype, name, descr) VALUES ($arraytype, \"$newplatform\", \"$newplatformdesc\")") or die_script (mysql_error(), 1, $arraytype, $arrayid, $sql);

				// Take this file and determine whether or not any of the clones are in edge....
				$fd = fopen($file, 'r');
				$clonecount = 0;
				$dupcount = 0;
				$accessionarray = array();
				// Read in the cy3 file...
				while (!feof($fd)) {
					$line = fgets($fd);
					$line = trim($line);
					$valsarray = explode("\t", $line);
					$insert = 0;
					//analyze($valsarray);
					$cloneid = $valsarray[0];
					if($cloneid != ""){
					$accession = $valsarray[1];
					$annname = $valsarray[2];
					//$vals = array('\"', '\'');
					$annname = ereg_replace("\'", '`', $annname); //str_replace($vals, "`", $annname);
					if($accession != ""){
						// is this annotation in the database????
						$sql = "SELECT COUNT(*) FROM annotations WHERE refseq like '%$accession%'";
						//echo "$sql<br>";
						$result = mysql_query($sql, $db);
						$countval = mysql_fetch_row($result);
						$present = -1;
						//echo "after annotations count...<br>";
						if($countval[0] != 0){
							$present = 1;

							// is this accession currently in the array????
							if (in_array($accession, $accessionarray)) {
								//echo "this accession: $accession is already in the array<br>";
								$dupcount++;
							}else{
								array_push($accessionarray,$accession);
								$clonecount++;
							}

						}else{
							// Convert accession to gi and then lookup...
							$sql = "SELECT gi FROM loc2acc WHERE accession = '$accession'";
							$result = mysql_query($sql, $db);
							$gival = mysql_fetch_row($result);
							if($gival[0] != ""){
								//echo "gi found with gival = $gival[0]<br>";
								$sql = "SELECT COUNT(*) FROM annotations WHERE gi = $gival[0]";
								$result = mysql_query($sql, $db);
								$countval = mysql_fetch_row($result);
								if($countval[0] != 0){
									$present = 2;
								}
							}

						}
					}
					if($present >= 1){
						//echo "clone: $cloneid with accession = $accession is in edge $countval[0] times<br>";
						//echo "here are the cloneids that correspond:<br>";
						$sql = "SELECT cloneid FROM annotations WHERE refseq like '%$accession%'";
						$result = mysql_query($sql, $db);
						while($row=mysql_fetch_row($result)){
							//echo "$row[0]<br>";
						}
						//$sql = "INSERT INTO extarrayannotations(arraytype, cloneid, accession, annname, cloneinedge, userid) VALUES ($arraytype, $cloneid, \"$accession\", \"$annname\", 'N', $userid)<br>";
						$sql_query = mysql_query("INSERT INTO extarrayannotations(arraytype, cloneid, accession, annname, cloneinedge) VALUES ($arraytype, $cloneid, \"$accession\", \"$annname\", 'Y')") or die_script (mysql_error(), 3, $arraytype, $arrayid, "INSERT INTO extarrayannotations(arraytype, cloneid, accession, annname, cloneinedge) VALUES ($arraytype, $cloneid, \"$accession\", \"$annname\", 'Y')");
					}
					else{
					//echo "before insertion...<br>";
					//$sql = "INSERT INTO extarrayannotations(arraytype, cloneid, accession, annname, cloneinedge, userid) VALUES ($arraytype, $cloneid, \"$accession\", \"$annname\", 'N', $userid)";
					//echo "$sql<br>";
					$sql_query = mysql_query("INSERT INTO extarrayannotations(arraytype, cloneid, accession, annname, cloneinedge) VALUES ($arraytype, $cloneid,  \"$accession\", \"$annname\", 'N')") or die_script (mysql_error(), 3, $arraytype, $arrayid, "INSERT INTO extarrayannotations(arraytype, cloneid, accession, annname, cloneinedge) VALUES ($arraytype, $cloneid,  \"$accession\", \"$annname\", 'N')");
					//echo "after insertion...<br>";
					}
					}



				}
			}
}


function convertBASE10LOGtoBASE10($val){
	$base10 = $val;
	if($base10 < 0){

		$base10 = round(pow(10, $base10),6);
		$base10 = -1 * round(1/$base10, 6);
	}else{
		$base10 = round(pow(10, $base10), 6);
	}
	return $base10;
}


if (!isset($_POST['submit'])){


	// Look up arraytypes in database to populate a pull-down list...
	$sql = "SELECT  DISTINCT e.arraytype, e.descr
FROM extarraytype AS e, extarray AS a
WHERE a.userid =1 AND e.arraytype = a.arraytype
ORDER  BY e.arraytype";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_row($result);
	//$platformarray = array();
	//array_push($platformarray,"New");
	$platformMenu = "<option value=\"0\">New</option>\r";
	if($row[0] != ""){
		$platformMenu .= "<option value=\"$row[0]\">$row[1]</option>\r";
		while($row = mysql_fetch_row($result)){
			//array_push($platformarray,$row[0]);
			$platformMenu .= "<option value=\"$row[0]\">$row[1]</option>\r";
		}
	}
?>
<script language="JavaScript">

function hideRows(){

	var selectval = document.query.platform.value;
	if(selectval != 0){



	hide1 = document.getElementById("platformnamerow");
	hide2 = document.getElementById("platformdescrow");
	hide3 = document.getElementById("platformannofile");
	hide1.style.display="none";
  	hide2.style.display="none";
	hide3.style.display="none";
	}
	return true;
}

function changeRows() {
	var selectval = document.query.platform.value;
	if(selectval != 0){
		return hideRows();

	}
	hide1 = document.getElementById("platformnamerow");
	hide2 = document.getElementById("platformdescrow");
	hide3 = document.getElementById("platformannofile");
	hide1.style.display="";
  	hide2.style.display="";
	hide3.style.display="";
	return true;




}


</script>
<body onLoad="hideRows()">
<form enctype="multipart/form-data"  name="query" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>">
<table class="question" width="400">

<tr>
<td class="questionanswer" colspan="2" ><strong>External Data File:</strong></td>
</tr>
<tr>
<td class="questionparameter" ><strong>Type of Array:</strong></td>
<td class="results">
<!-- this array type is set to indicate that this is a dye-swap... -->
<input type="hidden" name="arraytype" value="1">
Dual-Channel w/ Dye-Swap<font color="red"><strong>*</strong></font>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Platform:</strong></td>
<td class="results">
<input type="hidden" name="arrayinedge" value="1">
<select name="platform" onchange="changeRows()">
<?php echo $platformMenu; ?><font color="red"><strong>*</strong></font>
</td>
</tr>
<tr id="platformnamerow">
<td class="questionparameter"><strong>Platform Name:</strong></td>
<td class="results">
<input name="newplatform" type="text"></input>
</td>
</tr>
<tr id="platformdescrow">
<td class="questionparameter"><strong>Platform Description:</strong></td>
<td class="results">
<textarea name="newplatformdesc" rows="5" cols="25"></textarea>
</td>
</tr>
<tr id="arrayname">
<td class="questionparameter"><strong>Array Name:</strong></td>
<td class="results">
<input name="arrayname" type="text"></input>
</td>
</tr>
<tr id="arraydesc">
<td class="questionparameter"><strong>Array Description:</strong></td>
<td class="results">
<textarea name="arraydesc" rows="5" cols="25"></textarea>
</td>
</tr>

<tr>
<td class="questionparameter" colspan="2"><strong>Data Files:</strong></td>
</tr>
<tr id="platformannofile">
<td class="results" colspan="2">
Annotations file:<input name="annofile" type="file"><font color="red"><strong>*</strong></font>
</td>
</tr>
<tr>
<td class="results" colspan="2">
Cy3-labeled data file:<input name="extcy3file" type="file"><font color="red"><strong>*</strong></font>
</td>
</tr>
<tr>
<td class="results" colspan="2">
Cy5-labeled data file:<input name="extcy5file" type="file"><font color="red"><strong>*</strong></font>
</td>
</tr>

</tr>
<tr>
<td class="results"><input type="submit" name="submit" value="Submit"></td>

<td class="results"><input type="reset" value="Reset Form"</td>
</tr>
</table>

	</form>

</body>


<?php


}
else{
//echo "form submitted...<br>";
analyze($_POST);
$fileError[$counter] = 0;
echo "$newplatform = newplatform<br>";


			// uploading the cy3 labeled treatment file....
			$my_cy3uploader = new uploader('en'); // errors in English

			$my_cy3uploader->max_filesize(30000000);

			$tempfile = "extcy3file";
			$my_cy3uploader->upload($tempfile, '', '.txt');

			$prefix = '/var/www/html/edge2/IMAGES/';
			$my_cy3uploader->save_file($prefix, 2);

			if($arraytype == 1){
				// uploading the cy5 labeled treatment file....
				$my_cy5uploader = new uploader('en'); // errors in English

				$my_cy5uploader->max_filesize(30000000);

				$tempfile = "extcy5file";
				$my_cy5uploader->upload($tempfile, '', '.txt');

				$prefix = '/var/www/html/edge2/IMAGES/';
				$my_cy5uploader->save_file($prefix, 2);
				if ($my_cy5uploader->error) {
					$fileError = 1;
					$fileErrorText = $my_cy5uploader->error;
					print($my_cy5uploader->error . "<br><br>\n");
					die();
				}
				else{

					echo "thanks for uploading cy5 labeled file...<br>";
				}
				$cy5file = $prefix.$my_cy5uploader->file['name'];

			}

			if ($my_cy3uploader->error) {
				$fileError = 1;
				$fileErrorText = $my_cy3uploader->error;
				print($my_cy3uploader->error . "<br><br>\n");
			} else {
				print("Thanks for uploading " . $my_cy3uploader->file['name'] . "<br><br>\n");
				$cy3file = $prefix.$my_cy3uploader->file['name'];


				// now need to go through the file and insert into the externalhybrids table...
				//  Need to get MAX + 1 value from the externalhybrids table...
				$sql = "SELECT COUNT(*) FROM extarray";
				$result = mysql_query($sql, $db);
				$countval = mysql_fetch_row($result);
				if($countval[0] != 0){
					$sql = "SELECT MAX(arrayid) FROM extarray";
					$result = mysql_query($sql, $db);
					$maxval = mysql_fetch_row($result);
					$arrayid = $maxval[0] + 1;
				}else{
					$arrayid = 1;
				}
			if($platform == 0){
				$sql = "SELECT COUNT(*) FROM extarraytype";
				$result = mysql_query($sql, $db);
				$countval = mysql_fetch_row($result);
				if($countval[0] != 0){
					$sql = "SELECT MAX(arraytype) FROM extarraytype";
					$result = mysql_query($sql, $db);
					$maxval = mysql_fetch_row($result);
					$arraytype = $maxval[0] + 1;
				}else{
					$arraytype = 1;
				}
			}else{
				$arraytype = $platform;
			}
				// create a new array....
				$sql_query = mysql_query("INSERT INTO extarray(arrayid, arrayname, arraydesc, arraytype, userid) VALUES ($arrayid, \"$arrayname\", \"$arraydesc\", $arraytype, 1)") or die_script (mysql_error(), 3, $arraytype, $arrayid, "INSERT INTO extarray(arrayid, arrayname, arraydesc, arraytype, userid) VALUES ($arrayid, \"$arrayname\", \"$arraydesc\", $arraytype, 1)");

				// Is this an existing array platform (ie, are there annotations for this array in edge already)
				if($platform == 0){
					// We need to put the annotations into edge w/ the uploaded annotations file...
					echo "this array is not in edge.... inputing annotations file....";
					arrayannotationinsert($annofile,$userid, $newplatform, $newplatformdesc, $arraytype);

				}
				$fd = fopen($cy3file, 'r');
				$fd2 = fopen($cy5file, 'r');
				$clonecount = 0;
				$dupcount = 0;
				$accessionarray =  array();
				while (!feof($fd)) {
					$cy3line = fgets($fd);
					$cy5line = fgets($fd2);
					$cy3line = trim($cy3line);
					$cy5line = trim($cy5line);
					//echo "$cy5line<br>";
					$valsarraycy3 = explode("\t", $cy3line);
					$valsarraycy5 = explode("\t", $cy5line);
					//echo "$cy3line<br>";
					$cloneidcy3 = $valsarraycy3[0];
					$cloneidcy5 = $valsarraycy5[0];
					/*if($cloneidcy3 != $cloneidcy5){
						// We've a problem here....
						echo "clones no match...dying...<br>";
						die();
					}*/
					//echo "CY3 cloneid = $cloneidcy3 : CY5cloneid = $cloneidcy5<br>";
					if($cloneidcy3 != $cloneidcy5){
						// We've a problem here....
						echo "NOT A MATCH!!!!<br>";

					}

					// The cy3ratio is cy3-Treated/Cy5-Control...
					// We need to invert this...


					$cy3log10ratio = /*-1 * */$valsarraycy3[1];
					$cy5log10ratio = $valsarraycy5[1];

					//analyze($valsarraycy3);
					$cy3foldchange = convertBASE10LOGtoBASE10($valsarraycy3[1]);
					$cy5foldchange = convertBASE10LOGtoBASE10($valsarraycy5[1]);

					//analyze($valsarraycy5);
					//$cy3ratio = convertBASE10LOGtoBASE10($cy3log10ratio);
					//$cy5ratio = convertBASE10LOGtoBASE10($valsarraycy5[1]);
					$averagelog10ratio = ($cy3log10ratio + $cy5log10ratio)/2;
					//echo "<br>avg ratio = $averagelog10ratio<br>";
					if($cloneidcy3 != "" && cloneidcy5 != ""){
					$foldchange = convertBASE10LOGtoBASE10($averagelog10ratio);
					if($foldchange > 3){
						echo "<b>clone = $cloneidcy3<br>";
						echo "cy3 fold change:  $cy3foldchange<br>";
						echo "cy5 fold change: $cy5foldchange<br>";
						echo "average foldchange: $foldchange<br>";
					}

					$insertsql = "INSERT INTO externalhybrids(arrayid, cloneid, finalratio, arraytype) VALUES ($arrayid, $cloneidcy3, $foldchange, $arraytype)";
					//echo "$sql<br>";

						$sql_query = mysql_query("INSERT INTO externalhybrids(arrayid, cloneid, finalratio, arraytype) VALUES ($arrayid, $cloneidcy3, $foldchange, $arraytype)") or die_script (mysql_error(), 4, $arraytype, $arrayid, $insertsql);

				}
			}
			}
			$end = utime(); $run = $end - $start;
echo "<font size=\"1px\"><b>script took ";
		echo substr($run, 0, 5);
		echo " seconds to run</b></font><br>";
}

?>

