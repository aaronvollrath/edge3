<?php
include 'outputimage.inc';
$enteredarraynum = $_POST['numarrays'];
$arrayplatformname = $_POST['arrayplatformname'];
$arrayplatformdesc = $_POST['arrayplatformdesc'];
$submitval = $_POST['submit'];
$userid = 1;
require("fileupload-class.php");

require './phpinc/edge3_db_connect.inc';
//echo "enteredarraynum = $enteredarraynum<br>";
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

function newpow($base, $power)
{
if ($power < 0) {
$npower = $power - $power - $power;
return 1 / pow($base, $npower);
}
else
{
return pow($base, $power);
}
}

function array_insert( &$array, $index, $value ) {
   $cnt = count($array);
 
   for( $i = $cnt-1; $i >= $index; --$i ) {
       $array[ $i + 1 ] = $array[ $i ];
   }
   $array[$index] = $value;
}

function annotationtype($value){
/*
<option value="5" SELECTED>Clone/Probe ID</option>\r
	<option value="0">NONE</option>
	<option value="1">Refseq</option>\r
	<option value="2">Locus Tag (MGI #)</option>\r
	<option value="3">Gene Identification # (NCBI)</option>\r
	<option value="4">Gene Synonym</option>\r


*/
	switch ($value) {
		case 0:
			echo "No Annotation";
			break;
		case 1:
			echo "Refseq";
			break;
		case 2:
			echo "Locus Tag (MGI #)";
			break;
		case 3:
			echo "Gene Identification # (NCBI)";
			break;
		case 4:
			echo "Gene Synonym";
			break;
		case 5:
			echo "Clone/Probe ID";
			break;
		case 6:
			echo "Annotated Name";
			break;
	}
}

if($submitval == ""){

echo "Testing";
?>
<form name="extarrays" action="externaldata.php" method="post">
<table>
<tr>
<td  class="questionanswer" colspan="3"><strong>Number of Treatments/Samples:</strong></td>
<td><input name="numarrays" type="text" align="right"></input>
</td>
</tr>
<tr>
<td  class="questionanswer" colspan="3"><strong>Array Platform Name</strong></td>
<td><input name="arrayplatformname" type="text" align="right"></input>
</td>
</tr>
<tr>
<td  class="questionanswer" colspan="3"><strong>Array Platform Description</strong></td>
<td><input name="arrayplatformdesc" type="text" align="right"></input>
</td>
</tr>
<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td><input type="reset" value="Reset Form"></td>
</tr>
</table>
</form>
The first row of your data file should be a Header row.
Subsequent rows should be the clones, optional annotations and the expression data for each array.
<table border="1"><th colspan="6">Expected Layout of Data File (Columns delimited by comma, tab, semicolon, colon)</th>
<tr><td>Column #1</td><td>Up to 4 optional Annotation columns</td><td>Any number of Array Data columns</td></tr>
<tr>
<td>Clone ID #s (Numerals)</td><td>Annotations(Text)</td>
<td>Expression Data (Numerals in Fold Change, Log2 or Log10 format)</td>
</tr>
</table>
<br>

<table border="1"><th colspan="6">Example Data file (Columns delimited by comma, tab, semicolon, colon)</th>
<tr><td>Clone Number</td><td>Refseq</td><td>Annotated Name</td><td>GI</td><td>TCDD 48hr treatment</td>
<td>Aroclor 24hr treatment</td></tr>
<tr><td>1</td><td>NM_013464</td><td>Mus musculus aryl-hydrocarbon receptor (Ahr), mRNA.</td><td>27753975</td><td>3.29</td><td>1.98</td></tr>



</table>


<?php
}else{
if($submitval == "Submit"){
echo "Array platform name: $arrayplatformname<br>";
	echo "Array platform Desc: $arrayplatformdesc<br>";
?>




Please enter up to 6 annotation columns:<br>
<font color="red">NOTE: You must enter at least a Clone/Probe ID!</font>
<form enctype="multipart/form-data" name="extarrayssumbit" action="externaldata.php" method="post">
<table>
<?php


for($i = 1; $i <= 6; $i++){
?>
<tr><td>Column <?php echo $i; ?></td><td>
<select name="column<?php echo $i; ?>data">
<?php
	if($i == 1){
?>
	<option value="5" SELECTED>Clone/Probe ID</option>\r
<?php
	}else{
?>
	<option value="0">NONE</option>

	<option value="6">Annotated Name</option>\r
	<option value="1">Refseq</option>\r
	<option value="2">Locus Tag (MGI #)</option>\r
	<option value="3">Gene Identification # (NCBI)</option>\r
	<option value="4">Gene Synonym</option>\r
<?php
	}
?>
</select>
</td>
</tr>
<?php

}
?>


<tr>
<td>Please select column separator:</td>
<td>
<select name="columnseparator">
<option value="0">Comma (,)</option>\r
<option value="1" SELECTED>Tab (/t)</option>\r
<option value="2">Semicolon (;)</option>\r
<option value="3">Colon (:)</option>\r
</select>
</td>
</tr>
</table>
If necessary, please enter a description for each of the arrays.  Each array should have an appropriately named column header in your data file.  The description is utilized for any  additional information you may want to associate w/ the arrays.
<table>
<?php

$arraynumSQL = "SELECT MAX(arrayid) from extarray";
$arraynumResult = mysql_query($arraynumSQL, $db);
list($arraynum) = mysql_fetch_array($arraynumResult);
$arrayid = $arraynum + 1;
//echo "<br>arraynumber = $arraynum<br>";
for($id = 0; $id < $enteredarraynum; $id++){
	echo "<tr><td>Assigned Array #$arrayid Description</td><td><input name=\"array$id\" type=\"text\" align=\"right\" value=\"External Array : array$arrayid\" cols=\"75\"></input></td>

	<input type=\"hidden\" name=\"extarraynum$id\" value=\"$arrayid\"></tr>";
	$arrayid++;
}

?>
</table>

<table>

<tr>
<td><strong>Data format:</strong></td>
<td>
<select name="dataformat">
<option value="0">log 10</option>
<option value="1">log 2</option>\r
<option value="2">Fold Change</option>\r
<option value="3">Natural log</option>\r
</select>
</td>
</tr>
<tr>
<td class="questionparameter" ><strong>Data File:</strong></td>
<td class="results">
<input name="filename" type="file"><font color="red"><strong>*</strong></font>
</td>
</tr>
<tr>
<input type="hidden" name="numarrays" value="<?php echo $enteredarraynum; ?>">
<input type="hidden" name="arrayplatformname" value="<?php echo $arrayplatformname; ?>">
<input type="hidden" name="arrayplatformdesc" value="<?php echo $arrayplatformdesc; ?>">
<td><input type="submit" name="submit" value="Submit Data"></td>
<td><input type="reset" value="Reset Form"></td>
</tr>
</table>
</form>

<?php
} // end if($submitval == "Submit")
else{ //$submitval == "Submit Data"
	echo "file submitted....";
	//analyze($_POST);

	// The array data....
	echo "<table>";
	//for($count = 0; $count < $enteredarraynum; $count++){


	//}

	echo "Array platform name: $arrayplatformname<br>";
	echo "Array platform Desc: $arrayplatformdesc<br>";

	echo "The 6 annotation columns: <br>";
	$annocount = 0;
	// At some point need to check to make sure annotation columns are continuous.
	$annocontinuous = 1;
	for($i = 1; $i <=6; $i++){
		$str = "column".$i."data";
		if($_POST[$str] != 0){
			$annocount++;
		}
		echo "Column $i:";annotationtype($_POST[$str]);
		echo "<br>";
	}
	echo "Number of annotation columns: ".$annocount."<br>";

	echo "Data Format: ";
	if($_POST['dataformat'] == 0){
		echo "Log 10<br>";
	}else if($_POST['dataformat'] == 1){
		echo "Log 2<br>";
	}else{
		echo "Fold Change<br>";
	}


	//echo "<br>$_POST['file']";
	$fileError = 0;
	// Stuff to deal w/ the file to upload....
	$my_uploader = new uploader('en'); // errors in English

	$my_uploader->max_filesize(30000000);
	//$my_uploader->max_image_size(800, 800);
	$my_uploader->upload('filename', '', '.txt');
	$my_uploader->save_file('/var/www/html/edge2/IMAGES', 1);

	if ($my_uploader->error) {
		$fileError = 1;
		$fileErrorText = $my_uploader->error;
		print($my_uploader->error . "<br><br>\n");
	} else {
		//print("Thanks for uploading " . $my_uploader->file['name'] . "<br><br>\n");
		$filename = $my_uploader->file['name'];
		// Now need to go through and create a data file for clustering....
		//echo "enteredarraynum = $enteredarraynum<br>";

	$arrayidArray = array();
	$arrayNameArray = array();
	$arrayDescArray = array();

	$fout = fopen("/var/www/html/edge2/IMAGES/outfile.txt", "w");
	for($i = 0; $i<$enteredarraynum;$i++){

		$str = "array".$i;

		echo $_POST[$str]."<br>";
		array_push($arrayDescArray, $_POST[$str]);
		array_push($arrayNameArray, $_POST[$str]);
		fwrite($fout, "$_POST[$str]\n");
	}

	// What are the array numbers????
	for($i = 0; $i < $enteredarraynum; $i++){
		$str = "extarraynum".$i;
		//echo "$str<b>";
		echo $_POST[$str]."<br>";
		array_push($arrayidArray, $_POST[$str]);
		fwrite($fout, "$_POST[$str]\n");

	}

	// Assume that this a new array type....
	$arraynumSQL = "SELECT MAX(arraytype) from extarraytype";
$arraynumResult = mysql_query($arraynumSQL, $db);
list($arraynum) = mysql_fetch_array($arraynumResult);
$arraytype = $arraynum + 1;
	// Insert new array type....
	$sql = "INSERT extarraytype(arraytype, name, descr) VALUES ($arraytype, \"$arrayplatformname\", \"$arrayplatformdesc\")";
	$insertResult = mysql_query($sql, $db);
	if(mysql_errno($db)){
			echo "Error inserting array type: $arraytype with sql:<br>$sql<br>";
		}

	// Insert these arrays into the database
	for($i = 0; $i < $enteredarraynum; $i++){
		$sql = "INSERT extarray(arrayid, arrayname, arraydesc, arraytype, userid)
			VALUES ($arrayidArray[$i], \"$arrayNameArray[$i]\", \"$arrayDescArray[$i]\", $arraytype, 1)";
		$insertResult = mysql_query($sql, $db);

		if(mysql_errno($db)){
			echo "Error inserting array: $arrayidArray[$i] with sql:<br>$sql<br>";
		}

	}



	// Open the input file and output results....

	$handle = fopen("/var/www/html/edge2/IMAGES/".$filename, "r");


	if ($handle) {
		$linenum  = 0;
  		 while (!feof($handle)) {
       			$buffer = fgets($handle, 4096);
			$values = explode("\t", $buffer);
			//array_pop($values);
       			//echo $buffer."<br>";
			$k = 0;
			if($linenum != 0){
				if(count($values) >= 3){
					array_insert($values, 3, "0");
				}
					//array_pop($values);
				$cloneinfocount = 0;
				$cloneid = "";
				$accession = "";
				$annname = "";
				$arraynumindex = 0;
				foreach($values as $val){
					$val = trim($val);

					if($k > $annocount /*&& $_POST['dataformat'] != 2*/){
						$arrayid = $arrayidArray[$arraynumindex];

						if($_POST['dataformat'] == 0){
							// convert log10 to fold-change
							if($val < 0){
								$val = -1/newpow(10, $val);
							}else{
								$val = newpow(10, $val);
							}

						}
						if($_POST['dataformat'] == 1){
							// convert log10 to fold-change
							if($val < 0){
								$val = -1/newpow(2, $val);
							}else{
								$val = newpow(2, $val);
							}

						}
						if($_POST['dataformat'] == 3){
							// convert log10 to fold-change
							if($val < 0){
								$val = -1/newpow(exp(1.0), $val);
							}else{
								$val = newpow(exp(1.0), $val);
							}
						}
						$arraynumindex++;
						// Insert into exthybrids table....
						$sql = "INSERT exthybrids(arrayid, cloneid, finalratio, arraytype) VALUES($arrayid, $cloneid, $val, $arraytype)";
						//echo "$sql <BR>";
						$insertResult = mysql_query($sql, $db);

		if(mysql_errno($db)){
			echo "Error inserting exthybrids: with sql:<br>$sql<br>";
		}



					}
						if($cloneinfocount == 0){
							$cloneid = $val;
							$val = "e".$val;
						}elseif($cloneinfocount == 1){
							$annname = $val;
						}elseif($cloneinfocount == 2){
							$accession = $val;
						}
						$cloneinfocount++;

							echo "$val". "    ";
							fwrite($fout, "$val\t");

					$k++;
				}
				echo "<br>";
				fwrite($fout, "\n");
				// is this clone in edge????
				// first need to drop the version extension from the refseq...
				$searchval = explode(".", $accession);
				$searchval = $searchval[0];
				$sql = "SELECT count(*) FROM annotations WHERE refseq LIKE \"$accession%\"";
				//echo "$sql<br>";
				$countresult = mysql_query($sql,$db);
				$row = mysql_fetch_row($countresult);
				$countval = $row[0];

				$cloneinedge = 'N';
				if($countval >= 1){
					$cloneinedge = 'Y';
				}

				// Get rid of the

				// update db table w/ clone info....
				$sql = "INSERT extarrayannotations(arraytype, cloneid, accession, annname, cloneinedge)
			VALUES ($arraytype, $cloneid, \"$annname\", \"$accession\",'$cloneinedge' )";
		$insertResult = mysql_query($sql, $db);

		if(mysql_errno($db)){
			echo "Error inserting array: $arrayidArray[$i] with sql:<br>$sql<br>";
		}

			}
			$linenum++;
   		}
   		fclose($handle);
	}
	fclose($fout);
	$file = "/var/www/html/edge2/IMAGES/outfile.txt";
	//cho "$file<br>";
	$number = 4;
	$arrayidCount = $enteredarraynum;
	$svgFile = "/var/www/html/edge2/IMAGES/extoutput.svg";
	$algo = 1;
	$tableFile = "/var/www/html/edge2/IMAGES/exttable";
	$colorscheme = 0;
	$browserval = 1;
//java -mx512m -jar Cluster3.jar /var/www/html/edge2/IMAGES/data11083.txt 4 3 /var/www/html/edge2/IMAGES/svg11083.svg 1 /var/www/html/edge2/IMAGES/table11083 0 2 1
	$command = "java -mx512m -jar Cluster3.jar $file $number $arrayidCount $svgFile $algo $tableFile $colorscheme 2 $browserval";
				//if($_SESSION['priv_level'] >= 99){
					//echo "$command <br>";
				//}
				///if($priv_level == 99){
				//$command = "java -jar Cluster.jar";
				//	echo "<hr>$command<hr>";
				//}
				$str=passthru($command);
				$command = "cp ./IMAGES/extoutput.svg ./IMAGES/imageextoutput.svg";
				$str = exec($command);
				$cpsvgfile = "/var/www/html/edge2/IMAGES/imageextoutput.svg";

				$filesize = filesize($cpsvgfile);
				//$str = exec($command);
				if($filesize > 3169300 && $outputformat == 0){
				echo "<br>LARGE SVG FILE: Displaying the PNG file.";
					$outputformat = 1;
				}

				$command = "gzip --best ./IMAGES/svg$filenum.svg";
				//echo $command;
				$str=exec($command);

				$command = "mv ./IMAGES/svg$filenum.svg.gz ./IMAGES/svg$filenum.svgz";
				$str=exec($command);
				createImage("extoutput.svg", $number);
include "./IMAGES/imagemapextoutput";
?>
<img src="<?php echo "./IMAGES/imageextoutput.png" ?>" alt="heatmap" align="bottom" usemap="#map1" border=0></img>
<?php
	}
}

}// End else enteredarraynumber...


?>

