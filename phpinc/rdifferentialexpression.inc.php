<div dojoType="dijit.layout.ContentPane" style="height: 1800px;">

<?php

$userid = $_SESSION['userid'];
if($userid == ""){
	$userid = $_SESSION['userid'];
}
$debug = -1;  // set to 1 for debug messages as long as the userid matches debugid....
$debugid = 1;
if($userid == $debugid && $debug == 1){
echo "debug mode: <br>";
analyze($_SESSION);
analyze($_POST);
analyze($_GET);
}
$savedquery = '';
if(isset($_GET['savedquery'])){
	$savedquery = $_GET['savedquery'];

}
$browserval = 1;
if(!isset($_POST['orderedSubmit'])){
#if(!isset($orderedSubmit)){
	$orderedSubmit = false;
	$orderingMethod = 3;
}else{
	$orderedSubmit = $_POST['orderedSubmit'];
}


if (isset($_POST['submit']) && $orderingMethod == 0 || $orderedSubmit == "true") { 
		$filenum = rand(0, 25000);
			
		
		require('./phpinc/commonclusteringsavequerycodeaftersubmittingforclustering.inc.php');

		#require('./phpinc/commonfilecreationcode.inc.php');
		
		# we need to check to see if there has been a targets file submitted....
		/*analyze($_FILES);
		if($_FILES['file']['name']!=""){
			#echo "file entered need to upload...<br>";
			$my_uploader = new uploader('en'); // errors in English
		
			$my_uploader->max_filesize(90000000);
			//$my_uploader->max_image_size(800, 800);
			$my_uploader->upload('file', '', '.txt');
			$my_uploader->save_file($IMAGESdir, 2);
	
			if ($my_uploader->error) {
				$fileError = 1;
				$fileErrorText = $my_uploader->error;
				print($my_uploader->error . "<br><br>\n");
				die("ERROR UPLOADING FILE!");
			}else{
						print("Thanks for uploading " . $my_uploader->file['name'] . "<br><br>\n");
				$inputfilename = $my_uploader->file['name'];
				$targetsfile = $IMAGESdir."/".$inputfilename;
			}
		}else{
		
		*/
			
			# need to create the targets file....
			$targetsFile = "$IMAGESdir/targets$filenum.txt";
			$fdtargets = fopen($targetsFile, 'w');
			# create the header for the file
			$header = "SlideNumber\tLabels\tFileName\tCy3\tCy5\n";
			fwrite($fdtargets, $header);
			
			
			# call rBasedDifferentialExpression function...
			$idArray = array();
			$groupdesignationarray = array();
			$trxCounter = 0;
			$numberOfArrays = $_POST['numberOfArrays'];
			#analyze($_POST);
			#echo "The number of arrays is $numberOfArrays<br>";
			#echo "<HR>";
			
			
			$cy3array = $_POST['optioncy3'];
			$cy5array = $_POST['optioncy5'];
			#print_r($cy3array);
			#echo "<br>";
			#echo $cy5array;
			#echo "<HR>";
			$arraynamearray = array();
			foreach($cy3array as $key=>$value){
				// Get the info for each array....
				array_push($idArray, $key);
				$nameprefix = "customname".$key;
				$name = $_POST[$nameprefix];
				array_push($arraynamearray,$name);
				$cy5val = $cy5array[$key];
				# get the filename for this array...
				$sql = "SELECT FE_data_file FROM agilent_arrayinfo WHERE arrayid = $key"; 
				$result = $db->Execute($sql);
				$arrayfile = $result->FetchRow();
				//echo $sql;
				#$arrayfile = returndatafile($arrayfile[0],$datafilelocation,$edgedata,true,1);
				$arrayfile = returndatafile($arrayfile[0],$datafilelocation,$edgedata,0,1);
				if($arrayfile == ""){
					die("The Feature Extraction file for array, $name, is not present.  Please contact the EDGE administrator.<br>");	
				}
				//echo "$key : $name : $arrayfile : $value : $cy5val<br>";	
				$line = "$key\t$name\t$arrayfile\tsample$value\tsample$cy5val\n";		
				fwrite($fdtargets, $line);
			}
			fclose($fdtargets);
			# Check to see if the array names are unique.
			$dupCheck = checkArrayDuplicates($arraynamearray);
			if($dupCheck == 0){
				die("Exiting algorithm.<br>");
				
			}


		//}
		
		$key = $_POST['trxidorder1']; # this will grab the first arrayid from the list.
		# get the arraytype (organism) associated w/ these arrays...
		$sql = "SELECT arraytype FROM agilent_arrayinfo WHERE arrayid = $key";
		$result = $db->Execute($sql);
		$row = $result->FetchRow();

		$organismtype = $row[0];
		
		if(isset($_POST['pValue'])){
			$pvalue = $_POST['pValue'];
		}else{
			die("pValue is not set!");
		}
		if(isset($_POST['correction'])){
			$RbasedCorrection = $_POST['correction'];
			if($RbasedCorrection == 1){
				$RbasedCorrection="fdr";
			}elseif($RbasedCorrection ==2){
				$RbasedCorrection="BH";
			}elseif($RbasedCorrection == 3){
				$RbasedCorrection="BY";
			}elseif($RbasedCorrection == 4){
				$RbasedCorrection="holm";
			}elseif($RbasedCorrection==5){
				$RbasedCorrection="hochberg";
			}elseif($RbasedCorrection==6){
				$RbasedCorrection="hommel";
			}elseif($RbasedCorrection==7){
				$RbasedCorrection="bonferroni";
			}else{
				$RbasedCorrection = "none";
			}
		}else{
			die("correction value is not set!");
		}
		#echo "correction used: $RbasedCorrection<br>";
		#analyze($_POST);
		if(isset($_POST['customlimma'])){
			$customlimma = $_POST['customlimma'];
			$slashesarray = array("\\", "/");
			$customlimma = str_replace($slashesarray, '', $customlimma); 
			//$customlimma = stripslashes($customlimma); 
			
		}else{
			$customlimma = "";
		}
		if($customlimma == ""){
			# We need to used the inputted reference and comparisons
			$reference = $_POST['optionref'];
		}else{
			$reference = "";
		
		}
			#analyze($_POST);
			# we need to get the comparisons(contrasts)
			$contrasts = $_POST['comparison'];
			$makeContrastsList = "";
			$contrastcount = 0;
			$comparisonnames = array();
			for($i = 1; $i <=10; $i++){
				# go through comparisons until we meet same values....
				$str = "";
				$akey = $i."a";
				$avalue = $contrasts[$akey];
				$bkey = $i."b";
				$bvalue = $contrasts[$bkey];
				if(isset($reference)){
					if($avalue == $reference or $bvalue == $reference){
						if($avalue!=$reference){
							$str .= "sample".$avalue;
						}else{
							$str .= "sample".$bvalue;
						}
					}else{
					$str .= "sample".$avalue."-sample".$bvalue;
					}
				}else{
					$str .= "sample".$avalue."-sample".$bvalue;
				}
				if($contrastcount != 0){
					$str = ",$str";
				}
				if($avalue == $bvalue){	
					#echo "stopped at $i<br>";
					break;
				}else{
					$makeContrastsList .= $str;
				}
				$comparisonname = "comparison".$i."name";
				if(isset($_POST[$comparisonname])){
					array_push($comparisonnames, $_POST[$comparisonname]);
				}
				$contrastcount++;
			}
		
		
		#echo "<br>The number of contrasts is: $contrastcount<br>";
		#echo "reference is $reference[0]<br>";
?>
		<table class="question">
			<thead>
			<tr>
			<th colspan='5'><font ><b><i>limma</i> Results</b></font></th>
			</tr>
			</thead>
			<tr class="question">
			<td class="questionparameter"><b>p-Value:</b></td><td class="questionanswer"><?php echo $pvalue; ?></td><td class="questionparameter"><b>Correction Method:</b></td><td class="questionanswer"><?php echo $RbasedCorrection; ?></td>
			</tr>
		
			<tr class="question">
			<td class="questionparameter"><b>R code</b></td><td class="questionanswer"><a href="./dataoutputfiles/<?php echo "$filenum";?>diffexp.R" target="_blank">Code</a></td>
			<td class="questionparameter"><b>R Output</b></td><td class="questionanswer"><a href="./dataoutputfiles/<?php echo "$filenum";?>diffexp.Rout" target="_blank">R Output</a></td></tr>
			</table>
		<table class="question">
			<thead>
			<tr>
			<th colspan='5'><font ><b>Query Options</b></font></th>
			</tr>
			<tr class="question">
			<?php if($savedquery != ""){
				// Does this query have a name???
				$sql = "SELECT queryname FROM savedqueries WHERE query = $savedquery";
				echo $sql;
				$sqlResult = $db->Execute($sql);
				$row = $sqlResult->FetchRow();
				$name = $row[0];
				//echo "<br>name=$name<br>";
				$update = "true";
				if($name == "" || $name == "NULL"){
					$update = "false";
				}
				if($update == "true"){
			?>
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery&savedquery=$savedquery&submit=true&querytype=2";?>"
				target="<?php echo "_blank"; ?>">Update This Query?</a></td>
			<?php
				}else{
			?>
					<td class="questionanswer">
				<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2";?>"  target="<?php echo "_blank"; ?>">Save this Query?</a>
				</td>
			<?php
				}
			}else{
   			if(isset($update)){
			?>
				<td class="questionanswer">
				<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2";?>"  target="<?php echo "_blank"; ?>">Save This Query?</a>
				</td>
			<?php
				}
				else{
			?>
				<td class="questionanswer">
					<a href="<?php echo "./savequery.php?tempquery=$tempquery&querytype=2";?>"  target="<?php echo "_blank"; ?>">Save Query?</a>
				</td>
			<?php
				}
			}
			?>
			
			<?php if($savedquery != "" && $update == "true"){
			?>
				<td class="questionanswer"> <a href="<?php echo "./savequery.php?tempquery=$tempquery";?>"  target="<?php echo "_blank"; ?>">Save as new query?</a></td>
			<?php
			}
			?>
			</tr>
			</table>
<?php
		#echo "$customlimma";
		#die("exiting");
		# the function below is located in utilityfunctions.inc.
		$rfile = rBasedDifferentialExpression2($filenum, $idArray, $RbasedCorrection,$db,$pvalue,$datafilelocation,$edgedata,$IMAGESdir, $IMAGESreldir,$RPath,$reference,$makeContrastsList,$contrastcount,$comparisonnames,$organismtype,$customlimma);
		//echo '<br>here is the generated R file: '.$rfile."<br>";
		include($rfile);

	$end = utime(); $run = $end - $start;

	echo "<br><font size=\"1px\"><b>Query results returned in ";
	echo substr($run, 0, 5);
	echo " secs.</b></font>";
		
	
} 
else if(isset($_POST['submit']) && $orderingMethod >= 1) {

require("./phpinc/rdifferentialexpressionordering2.inc.php");

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


//analyze($_SESSION);
	require('./phpinc/commonclusteringsavequerycode.inc');


//
	require('./phpinc/commondifferentialexpressionoptionscode.inc.php');
	require('./displayexperimentsbygroups.php');
?>
<table class="question" width="700px">  <!-- this is put here due to differences between the different clustering modules....-->


<tr id="results">
<td><input type="submit" name="submit" value="Submit"></td>
<td></td>
<td><input type="reset" value="Reset Form"></td>
</tr>

</table>

</form>
</p>
</p>
<?php
		#require('./phpinc/commonedgethresholdselections.inc.php');
		
 

}


?>
</div>
