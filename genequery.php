<?php
require 'edge_db_connect2.php';
require("fileupload-class.php");
require './phpinc/edge3_db_connect.inc';
// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	//die('Sorry you are not logged in, this area is restricted to registered members. <a href="./login.php">Click here to go to the login page</a>');
}
include 'header.inc';
include 'utilityfunctions.inc';
require 'globalfilelocations.inc';

$submitted = $_POST['submitted'];
$arraytype = $_POST['arraytype'];
$genename = $_POST['genename'];

if($submitted != true){
$agilentarraytypeSQL = "SELECT id, organism, arraydesc, version FROM agilentarrays";
$arraytypeResult = mysql_query($agilentarraytypeSQL, $db);
while(list($arrayid, $organism, $arraydesc, $version) = mysql_fetch_array($arraytypeResult))
{

    $arraytypeMenu .= "<option value=\"$version\">$organism $arraydesc $version</option>\r";
}
?>


<form enctype="multipart/form-data" name="newagilentsample" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="submitted" value="true">

<table>

<tr>
<td><strong>Array Type/Species</strong></td>
<td><select name="arraytype">
<option SELECTED></option>
<?php echo $arraytypeMenu; ?>
</select></td>
</tr>

<td><strong>Official Gene Symbol:</strong></td>
<td>
<input name="genename" type="text">
</td>
<td>
The official gene symbol of the Entrez Gene record for the gene.
</td>
</tr>

<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td><input type="reset" value="Reset Form"></td>
</tr>

</table>
</form>
<?php
}

else{

$primaryname = $genename;
$queryparams = "";
// Need to look up cloneids for genes entered....
$genesPresent = 0;
if($primaryname != ""){
		$genesPresent = 1;
		//echo "primaryname not null";
		$whereCheck = 1;
		// now need to go through gene name, if there are commas, need to create an array and use to
		// search across entries.
		$pos = strpos($primaryname, ",");
		if ($pos === false) { // note: three equal signs
			$primaryname = trim($primaryname);
   	 		// not found...don't need to create the array...
			$queryparams = " GeneSymbol LIKE '%$primaryname%'";
			$primarylist = $primaryname;
		}
		else{
			// The case where someone is searching on multiple genes...
			$queryparams = " GeneSymbol LIKE";
			// Create the array of primary names to search on...
			$primarynamearray = array();
			$primarynamearray = explode(",", $primaryname);
			$countcheck = 0;
			$primaryarraynum = count($primarynamearray);
			foreach($primarynamearray as $namevalue){
				$namevalue = trim($namevalue);
				if($countcheck < $primaryarraynum - 1){
					$queryparams .= " '%$namevalue%' OR GeneSymbol LIKE ";
					// used to display in query parameters table...
					$primarylist .= "'$namevalue' OR ";
				}
				else{
					$queryparams .= " '%$namevalue%' ";
					// used to display in query parameters table...
					$primarylist .= "'%$namevalue%'";
				}
				$countcheck++;
			}
		}
		//echo "Here's primarylist: $primarylist";
}

$sql = "SELECT GeneSymbol , FeatureNum FROM agilent".$arraytype."_extendedannotations WHERE $queryparams ORDER BY FeatureNum";
//echo "<br>$sql<br>";

$returnResult = mysql_query($sql, $db);
$returnArray = array();
$index = 0;
?>
<table>
<TR><TH>GeneSymbol<TH>FeatureNum
<?php
while($row = mysql_fetch_row($returnResult)){
				$genesymbol = $row[0];
				$featurenum = $row[1];
				//echo "$genesymbol $featurenum<br>";
				?>
				<TR><TD><?php echo "$genesymbol" ?><TD><?php echo "$featurenum" ?>
				<?php
	}

}

?>
</table>