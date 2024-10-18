<?php

// Utility functions for use in php scripts...
if(isset($_SESSION['userid'])){
	$userid = $_SESSION['userid'];
}

/*


*/
function checkSQLError($result, $sql){
	if(!$result){
		die("<h3><font color='red'>ERROR: </font>Please contact administrator regarding database error updating table. SQL: $sql </h3>");
	}
}

/**
 * Function: fetchRelatedData
 * Fetch related data by specific column and table.
 * 
 * @param string $table      The table name.
 * @param string $column     The column to select.
 * @param string $idColumn   The ID column to match.
 * @param int    $id         The ID value to use in the WHERE clause.
 * @param object $db         The database connection object.
 * @return string            The result of the query or 'N/A' if not found.
 */
function fetchRelatedData(string $table, string $column, string $idColumn, int $id, $db): string {
    $sql = "SELECT {$column} FROM {$table} WHERE {$idColumn} = ?";
    $result = $db->Execute($sql, [$id]);
    return $result->fields[$column] ?? 'N/A';
}




function returndatafile($dbfilename,$datafilelocation,$edgedata,$url,$filetype){

	
	# $filetype values:
	# 1 = *.txt
	# 2 = *.html
	# 3 = *.jpg
	# 4 = *.tif

	if(!function_exists('fnmatch')) {
		function fnmatch($pattern, $string) {
			return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.'))."$#i", $string);
		} // end
	}
	
	
	
	$filenameparts = explode("_",$dbfilename);
	//analyze($filenameparts);
	# put in condition $url != 0 to compensate for limma file issues....
	if(!isset($filenameparts[7])){
		if($url != 0){
			return "File not found.  Please contact EDGE administrator.<br>";
		}else{
			return "";	
		}
	}
	#analyze($filenameparts);
	
	# get the last index of the array
	$lastindex = count($filenameparts) - 1;
	$filenumber=$filenameparts[$lastindex];
	//echo "<h3>Filenumber=$filenumber</h3>";
	$filenumparts = explode(".",$filenumber);
	if($filetype == 1){
		$filenumber="$filenumparts[0].txt";
	}
	if($filetype == 2){
		$filenumber = "$filenumparts[0].html";
	}elseif($filetype == 3){
		$filenumber = "$filenumparts[0].jpg";
	}elseif($filetype==4){
		$filenumber = "$filenumparts[0].tif";
	}else{
		$filenumber = $filenameparts[$lastindex];
	}
	# the arrays are indexed in the following format:  US22502567_251486817772
	//echo "$filenumber<br>";
	$part1 = $filenameparts[0];
	$part2 = $filenameparts[1];
	$directory = strtoupper($filenameparts[0]."_".$filenameparts[1]);
	$directory = trim($directory);
	$dir = $datafilelocation."/".$directory."/";
	$fileexist = $dir.$dbfilename;
	#echo "$fileexist<br>";
	/* 
	if(file_exists($fileexist)){
		echo "file $dbfilename exists<br>";
	}else{
		echo "file $dbfilename does not exist<br>";
	}
	*/
	//echo "DIRECTORY is $dir <br>";
	$selectedfile = "";
	#echo "filetype is $filetype<br>";
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			//if($filetype == 1){
			while (($file = readdir($dh)) !== false) {
		#	echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
			//echo "$file<br>";
				if($filetype == 1){
					if(fnmatch("*.txt",$file)){
						#echo "foundmatch<br>";
						$filenameparts = explode("_",$file);
						#$filenum=$filenameparts[7];
						$lastindex = count($filenameparts) - 1;
						$filenum=$filenameparts[$lastindex];
						//echo "$filenum<br>";
						if($filenumber == $filenum){
							$selectedfile = $file;
						}
					}
				}elseif($filetype==2){
						//echo "$file<br>";
					if(fnmatch("*.html",$file)){
						$filenameparts = explode("_",$file);
						$lastindex = count($filenameparts) - 1;
						$filenum=$filenameparts[$lastindex];
						if($filenumber == $filenum){
							# this will format the FE html output file so the links to the images are functional.
							$formathtmlfile = " perl htmlfilesearch.pl $dir";
							//echo "$formathtmlfile<br>";
							$str=exec($formathtmlfile);	
							$selectedfile = $file;
						}
					}
				}elseif($filetype==3){
					if(fnmatch("*.jpg",$file)){
						$filenameparts = explode("_",$file);
						$lastindex = count($filenameparts) - 1;
						$filenum=$filenameparts[$lastindex];
						if($filenumber == $filenum){
							$selectedfile = $file;
						}
					}
				}else{
					if(fnmatch("*.tif",$file)){
						$filenameparts = explode("_",$file);
						$lastindex = count($filenameparts) - 1;
						$filenum=$filenameparts[$lastindex];
						if($filenumber == $filenum){
							$selectedfile = $file;
						}
					}
				}
			}
			closedir($dh);
			//}
		}else{
		#	die("unable to open directory");
		}
	}else{
	#	die("Not a valid directory");
	}
	
	if($url > 0){
		if($selectedfile != ""){
			return "<a href='./$edgedata/$directory/$selectedfile' target='_blank'>DOWNLOAD DATA FILE</a>";
		}else{
			return "File not found.  Please contact EDGE administrator.<br>";
		}
	}else{

		if($selectedfile != ""){
			return "$datafilelocation/$directory/$selectedfile";
		}else{
			return "";
		}

	}
}






// The following F-statistic functions adapted from:http://home.ubalt.edu/ntsbarsh/Business-stat/otherapplets/pvalues.htm 
function FishF($f,$n1,$n2) {
	$Pi=M_PI;
	$PiD2=$Pi/2;
	$PiD4=$Pi/4;
	$Pi2=2*$Pi;
	$e = M_E;
	$e10 = 1.105170918075647625;
	$Deg=180/$Pi;



		$x=$n2/($n1*$f+$n2);
		if(($n1%2)==0) { return StatCom(1-$x,$n2,$n1+$n2-4,$n2-2)*pow($x,$n2/2); }
		if(($n2%2)==0){ return 1-StatCom($x,$n1,$n1+$n2-4,$n1-2)*pow(1-$x,$n1/2); }
		$th=atan(sqrt($n1*$f/$n2)); $a=$th/$PiD2; $sth=sin($th); $cth=cos($th);
		if($n2>1) { $a=$a+$sth*$cth*StatCom($cth*$cth,2,$n2-3,-1)/$PiD2; }
		if($n1==1) { return 1-$a; }
		$c=4*StatCom($sth*$sth,$n2+1,$n1+$n2-4,$n2-2)*$sth*pow($cth,$n2)/$Pi;
		if($n2==1) { return 1-$a+$c/2; }
		$k=2; while($k<=($n2-1)/2) {$c=$c*$k/($k-.5); $k=$k+1; }
		return 1-$a+$c;
}
function StatCom($q,$i,$j,$b) {
		$zz=1; $z=$zz; $k=$i; while($k<=$j) { $zz=$zz*$q*$k/($k-$b); $z=$z+$zz; $k=$k+2; }
		return $z;
}




##                ##
##  PHPMultiSort  ##
##                ##
// Takes:
//        $data,  multidim array
//        $keys,  array(array(key=>col1, sort=>desc), array(key=>col2, type=>numeric))

function php_multisort($data,$keys){
	// List As Columns
	foreach ($data as $key => $row) {
		foreach ($keys as $k){
		$cols[$k['key']][$key] = $row[$k['key']];
		}
	}
	// List original keys
	$idkeys=array_keys($data);
	// Sort Expression
	$i=0;
	foreach ($keys as $k){
		if($i>0){$sort.=',';}
		$sort.='$cols['.$k['key'].']';
		if($k['sort']){$sort.=',SORT_'.strtoupper($k['sort']);}
		if($k['type']){$sort.=',SORT_'.strtoupper($k['type']);}
		$i++;
	}
	$sort.=',$idkeys';
	// Sort Funct
	$sort='array_multisort('.$sort.');';
	eval($sort);
	// Rebuild Full Array
	foreach($idkeys as $idkey){
		$result[$idkey]=$data[$idkey];
	}
	return $result;
} 

/**
 * Function: fdrqvalue
 * calculates an FDRQ value
 * 
 * @param number $alpha      The alpha value.
 * @param number $count      The number of values.
 * @return number 
 */
function fdrqvalue($alpha, $count){
	
	$qvalue = $alpha * ($count + 1)/(2 * $count);
	return $qvalue;

}
/**
 * Function: bonferroni
 * calculate a bonferroni value
 * 
 * @param number $alpha      The alpha value.
 * @param number $count      The number of values.
 * @return number 
 */
function bonferroni($alpha, $count){
	

	$bonferroni = $alpha * (1/$count);
	return $bonferroni;
}

/**
 * Function: fdr
 * calculate a fdr value
 * 
 * @param number $alpha      The alpha value.
 * @param number $count      The number of values.
 * @return number 
 */
function fdr($alpha, $count){
	$fdr = (($alpha)*($count + 1))/(2 * $count);
	return $fdr;
}

function ttest_pvalue($t, $df){
	return buzz($t, $df);

}

function buzz($t,$n) {
$pi=M_PI;
$pj2=$pi/2;
$pj4=$pi/4;
$pi2=2*$pi;
$e = M_E;
$exx = 1.10517091807564;
$dgr=180/$pi;
	$t=abs($t);
	$rt=$t/sqrt($n);
	$fk= atan($rt);
    if($n==1) { return 1-$$fk/$pj2; }
    $ek=sin($fk); $dk= cos($fk);
if(($n%2)==1)
        { return 1-($fk+$ek*$dk*zip($dk*$dk,2,$n-3,-1))/$pj2; }
        else
        { return 1-$ek*zip($dk*$dk,1,$n-3,-1); }
}

function zip($q,$i,$j,$b) {
	$zz=1;
	$z=$zz;
	$k=$i;
while($k<=$j) { $zz=$zz*$q*$k/($k-$b); $z=$z+$zz; $k=$k+2; }
    return $z;
}

function ttest($vals, $pvalue, $correction, $correctionval){

		//echo "ttest($vals, $pvalue, $correction, $correctionval)<br>";
		/* 
		$pvalue:
		1 = .10
		2 = .05
		3 = .025
		4 = .01
		5 = .005
		6 = .001
		*/



		if(count($vals) != 2){
			die("ERROR: You may not have designated 2 groups and/or you may not have put separators between groups on the ordering page!<br>");

		}

		$file="./t-statlookuptable.txt.csv";

		$fd = fopen($file,'r');
		$tstatarray = array();
		$returnarray = array();
		$row =0;
		while (($data = fgetcsv($fd, 1000, "\t"))!== FALSE) {
				$num = count($data);
			if($row == 0){
				$row++;
				continue; // skip the first line of pvalues...
			}
			for ($c=1; $c < $num; $c++) {
				$fileindex = $c;
				$val = $data[$fileindex];
				$tstatarray[$row][$c] = $val;
				}
			$row++;
		}

		//analyze($tstatarray);
		//$firstrow = count($tstatarray[1]);

		//die("quitting...");
		//analyze($vals);
			//echo "<hr>";
			//analyze($tstatarray[38]);
			$meanarray = array();
			$sdarray = array();
			$numgroups = count($vals);
			
			if($numgroups != 2){
				die("you can't use t-test w/ groups != 2");
			}

			$controlgroupmean=average($vals[0]);
			$treatmentgroupmean = average($vals[1]);
			$controlgroupsd = deviation($vals[0]);
			$treatmentgroupsd = deviation($vals[1]);

			/*
			echo "mean of controlgroup = $controlgroupmean<br>";
			echo "mean of group 2 = $treatmentgroupmean<br>";
			echo "sd of group1 = $controlgroupsd<br>";
			echo "sd of group 2 = $treatmentgroupsd<br>";
			*/
			$denominator = (sqrt( (($treatmentgroupsd*$treatmentgroupsd)/count($vals[1]))+ (($controlgroupsd*$controlgroupsd)/count($vals[0]))));
			if($denominator == 0){
				$denominator = 0.0000001;

			}
			$t = abs(($treatmentgroupmean-$controlgroupmean))/$denominator;
			//echo "t value is: $t<br>";
		
			$df = count($vals[0]) + count($vals[1]) - $numgroups; 
			//echo "The degrees of freedom: $df<br>";
			//echo "pvalue is : $pvalue<br>";
			$tstat = $tstatarray[$df][$pvalue];
			//echo "t-stat: $tstat<br>";
			
			//$t_effect = ($treatmentgroupmean - $controlgroupmean)/$controlgroups;
			
			//echo "The t-effect is: $t_effect<br>";
			$onesidedpvalue = ttest_pvalue($t, $df)/2;
			//echo "one-sided p-value = $onesidedpvalue<br>";
			
		/*
			if($t > $tstat){
				echo "null hypothesis rejected: there's a difference between the groups<br>";
			}else{
				echo "null hypothesis accepted<br>";
			}
			*/
			$hypval = 0;
			/*if($t > $tstat){
				//echo "There's a signficant difference @ .05!<br>";
				$hypval = 1;
			}*/
			if($t > $tstat){
					
					if($correction != -1){
						if( $onesidedpvalue < $correctionval){
							//echo "There's a signficant difference!<br>";
							$hypval= 1;
						}
						else{
							//echo "rejected due to correction...$correction<br>";
							$hypval=  0;
						}
					}else{
						//echo "There's a signficant difference!<br>";
						$hypval = 1;
					}
					
				}else{
					//echo "There's no difference @ .05.<br>";
					$hypval= 0;
				}
			array_push($returnarray, $hypval);

			array_push($returnarray, $onesidedpvalue);
			
			return $returnarray;
				

		}



function anova($obs, $pvaluecutoff, $correction, $correctionval){

if(count($obs) <= 2){
	die("ERROR: You may not have designated more than 2 groups and/or you may not have put separators between groups on the ordering page!<br>");

}
//echo "file # is: $pvaluecutoff<br>";
if($pvaluecutoff == 1){
$file="./foutdist1-20by1-100.pvalue-.10.csv";
}elseif($pvaluecutoff == 2){
$file="./foutfdist1-20by1-100.pvalue-.05.csv";
}elseif($pvaluecutoff == 3){
$file="./foutdist1-20by1-100.pvalue-.01.csv";

}else{
	die("Invalid table for fstat!<BR>");
}
//$file="./foutfdist1-20by1-100.pvalue-.05.csv";
	//echo "file: $file<br>";
//return 1;

// an array to store the value of the hypothesis test and associated calculate p-value based on the F-statistic
$returnarray = array(); 

$fd = fopen($file,'r');
if(!$fd){
	die("Error opening file $file");	
}
$fstatarray = array();
//$fstatarray[0][0] = -99999999;
//echo "fstatarray[0][0] = $fstatarray[0][0]<br>";
$row =0;
while (($data = fgetcsv($fd, 1000, ","))!== FALSE) {
    	$num = count($data) + 1;
	for ($c=1; $c < $num; $c++) {
		$fileindex = $c-1;
		$val = $data[$fileindex];
		$fstatarray[$row][$c] = $val;

		//echo "$fstatarray[$row][$c]"."  ";
			
	
    	}
	//echo "<br>";
	$row++;
}

//analyze($fstatarray);

// Get the global mean
$numpops = count($obs);
//echo "the number of groups = $numpops<BR>";
$elementcount = 0;
$grandsum = 0;

$popsmeanarray = array();
$popelementcount = 0;
$popelementsum = 0;
$SSobs = 0;
$arraycount = 0;
for($i = 0; $i < $numpops; $i++){
	$popelementcount = 0;
	$popelementsum = 0;
	$numelements = count($obs[$i]);
	if($arraycount != 0){

		$arraycount++;  // we need to do this because of the problem w/ separators...
	}
	//echo "number of elements in group $i: $numelements<br>";
	for($j=0; $j < $numelements; $j++){
		$elementcount++;
		$popelementcount++;
		//$grandsum += $obs[$i][$j];
		$grandsum += $obs[$i][$arraycount];
		//echo "grandsum = obs[".$i."][".$arraycount."]<br>";
		//echo "$grandsum<br>";
		$popelementsum += $obs[$i][$arraycount];
		$SSobs += ($obs[$i][$arraycount] * $obs[$i][$arraycount]);
		$arraycount++;
	}
	$popsmeanarray[$i] = $popelementsum/$popelementcount;
}
/*
echo "<hr> population mean array<br>";
analyze($popsmeanarray);
echo "<hr>";
*/
$globalmean = $grandsum/$elementcount;

//echo "GLOBAL MEAN = $globalmean";
//analyze($obs);


$meanmatrix = array();
$treatmenteffectmatrix = array();
$residualmatrix = array();
$SSmean = 0;
$SStr = 0;
$SSres = 0;
$SScor = 0;
$arraycount = 0;
for($i = 0; $i < $numpops; $i++){

	$numelements = count($obs[$i]);
	if($arraycount != 0){

		$arraycount++;  // we need to do this because of the problem w/ separators...
	}
	for($j=0; $j < $numelements; $j++){
		
		$meanmatrix[$i][$j] = $globalmean;
		$SSmean += ($globalmean * $globalmean);
		$treatmenteffectmatrix[$i][$j] = $popsmeanarray[$i] - $globalmean;
		$SStr += ($treatmenteffectmatrix[$i][$j]*$treatmenteffectmatrix[$i][$j]);
		$residualmatrix[$i][$j] = $obs[$i][$arraycount] - $popsmeanarray[$i];
		$x = $residualmatrix[$i][$j]*1;
		//echo "$x<br>";
		$SSres += ($residualmatrix[$i][$j] * $residualmatrix[$i][$j]);
		
		$SScor += (($obs[$i][$arraycount] - $globalmean) * ($obs[$i][$arraycount] - $globalmean));
		$arraycount++;
	}
}
/*
echo "<hr>mean matrix<br>";
analyze($meanmatrix);
echo "<hr>";

echo "<hr>treatment effect matrix<br>";
analyze($treatmenteffectmatrix);
echo "<hr>";
echo "<hr>residual matrix<br>";
analyze($residualmatrix);
echo "<hr>";

echo "SS<sub>obs</sub> = $SSobs<br>";
echo "SS<sub>mean</sub> = $SSmean<br>";
echo "SS<sub>tr</sub> = $SStr<br>";
echo "SS<sub>res</sub> = $SSres<br>";
echo "SS<sub>cor</sub> = $SScor<br>";
*/
$DFtrx = count($popsmeanarray) - 1;
$DFres = 0;
$DFcor = 0;
for($i = 0; $i < count($popsmeanarray); $i++){
	$DFres += count($obs[$i]);
}
$DFcor = $DFres - 1;

$DFres += (-1 * count($popsmeanarray));
/*
echo "DF<sub>trx</sub> = $DFtrx<br>";
echo "DF<sub>res</sub> = $DFres<br>";
echo "DF<sub>cor</sub> = $DFcor<br>";
*/

if($SSres == 0){
	// Can't divide by 0 or will get an error....
	$SSres = .00000000001;

}
$Fstat = ($SStr/$DFtrx)/($SSres/$DFres);
//echo "Fstat = $Fstat<br>";
$x = $DFtrx - 1;
$y = $DFres - 1;
$z = $fstatarray[$y][$x] * 1;
//echo "fstatarray[$y][$x] = $z<br>";
$Fpvalue = FishF($Fstat, $x,$y);
//echo "Fpvalue = $Fpvalue<br>";

$hypval = 0;
if($Fstat > $z){
	
	$hypval = 1;
	//echo "correction = $correction<br>";
	if($correction != -1){
		//echo "correctionval = $correctionval<br>";
		if( $Fpvalue < $correctionval){
		//echo "There's a signficant difference @ $correctionval!<br>";
			$hypval= 1;
		}
		else{
			//echo "rejected due to correction...$correction<br>";
			$hypval=  0;
		}
	}else{
		//echo "There's a signficant difference @ $pvalue!<br>";
		$hypval = 1;
	}
}else{
	//echo "There's no difference @ .05.<br>";
	$hypval = 0;
}

	array_push($returnarray, $hypval);

	array_push($returnarray, $Fpvalue);
	
	return $returnarray;
		

}


/**
 * Function: analyze
 * takes in an array and outputs key value pairs.
 * 
 * @param array &$array      The array
 * @return number 
 */

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


/**
 * Function: log10ToFoldChange
 * calculate fold change value from a log10 value
 * 
 * @param number &$log10val      The log10 value to be converted
 * @return number 

 */
// Converting a log base-10 value to fold change ratio

function log10ToFoldChange(&$log10val){

	if($log10val >= 0){

		$value = pow(10, $log10val);

	}else{
		$intermediate = pow(10,$log10val);
		$value = -1/$intermediate;
	}
	return $value;

}
function my_array_unset($array,$index) {
  // unset $array[$index], shifting others values
  $res=array();
  $i=0;
  foreach ($array as $item) {
    if ($i!=$index)
      $res[]=$item;
    $i++;
  }


  return $res;
}

function sqlerrorcheck($sql, $db){

		if(mysql_errno($db)){
			echo "uh-oh db error<br>";
			$updateerrormsg .= "<strong><font color=\"red\">Unsuccessful!</font></strong><br>";
			$updateerrormsg .= "<strong><font color=\"red\">".mysql_errno($db) . ": " . mysql_error($db). "</font></strong>\n";
			$updateerrormsg .= "<br>heres sql:  <br>";
			$updateerrormsg .= "$sql";
			echo $updateerrormsg;
		}

}



// This is a simple function to create a table from an associative array returned from a sql query
function array2table($arr, $width, $tableid){
   
       reset($arr);
       //$num = count(current($arr));
       echo "<table width=\"$width\">\n";

       foreach($arr as $key => $value){
           echo "<tr>\n";
           echo "<td class=$tableid>$key</td><td class=$tableid>$value</td>";
           echo "</tr>\n";
           }

       echo "</table>\n";
       
}


/*
This function will print out a table using data from the `agilent<ARRAYVERSION>_extendedannotations` table
Sequence	CTTCAGTGGAAAGTAGGACTTTCTGTGCAGCTCAGGGAGACCATAAGCTGAGAAGCAGCT
Refseq	XM_908550
GenBankAcc	BC023116
LocusLinkID	68567
UnigeneID	Mm.45127
EnsemblID	ENSMUST00000031051
TigrID	NP432704
ChromosomalLocation	chr5:29965766-29965707
Cytoband	mm|5qB1
GoID	GO:0005509(calcium ion binding)|GO:0005615(extracellular space)|GO:0007049(cell cycle)|GO:0007050(cell cycle arrest)

*/
function annotationstotable($arr, $width, $tableid, $arrayversion){


	if(gettype($arr) != "array"){
		echo $arr;
		die('arr is not an array; in annotationstotable function in utilityfunctions.inc.');
	}
       reset($arr);
       echo "<table width=\"$width\">\n";
		
       foreach($arr as $key => $value){

       		if(strcmp($key,"[REFSEQ]") == 0){
			 echo "<tr>\n";
           		echo "<td class=$tableid>$key</td><td class=$tableid><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$value\" target=\"_blank\">$value</a></td>";
			 echo "</tr>\n";
		}elseif(strcmp($key,"GENBANKACC") == 0){
			 echo "<tr>\n";
           		echo "<td class=$tableid>$key</td><td class=$tableid><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=$value\" target=\"_blank\">$value</a></td>";
			 echo "</tr>\n";
		}elseif(strcmp($key, "LOCUSLINKID") == 0){

			echo "<tr>\n";
           		echo "<td class=$tableid>$key</td><td class=$tableid><a href=\" http://www.ncbi.nlm.nih.gov/sites/entrez?db=gene&cmd=Retrieve&dopt=Graphics&list_uids=$value\" target=\"_blank\">$value</a></td>";
			 echo "</tr>\n";
		}
		elseif(strcmp($key,"UNIGENEID") == 0){
			 echo "<tr>\n";
           		echo "<td class=$tableid>$key</td><td class=$tableid><a href=\"http://www.ncbi.nlm.nih.gov/sites/entrez?db=unigene&cmd=&term=$value\" target=\"_blank\">$value</a></td>";
			 echo "</tr>\n";
		}elseif(strcmp($key,"ENSEMBLID") == 0){

		if($arrayversion == "g4122f"){
				$organism = "Mus_musculus";
			}elseif($arrayversion == "g4131f"){
				$organism = "Rattus_norvegicus";
			}
			echo "<tr>\n";
           		echo "<td class=$tableid>$key</td><td class=$tableid><a href=\"http://www.ensembl.org/$organism/geneview?gene=$value\" target=\"_blank\">$value</a></td>";
			 echo "</tr>\n";

		}elseif(strcmp($key,"GOID") == 0){
			echo "<tr>\n";
           		echo "<td class=$tableid>$key</td><td class=$tableid>";
			$goidarray = array();
			$goidarray=explode("|",$value);
			foreach($goidarray as $goid){
				$id = explode("(",$goid);
				$idval=$id[0];
			echo "<a href=\"http://amigo.geneontology.org/cgi-bin/amigo/go.cgi?view=details&search_constraint=terms&depth=0&query=$idval\" target=\"_blank\">$goid</a><br>";
			}
			 echo "</td></tr>\n";

		}elseif(strcmp($key,"CHROMOSOMALLOCATION") == 0){
			if($arrayversion == "G4122F"){
				$organism = "Mouse";
			}elseif($arrayversion == "G4131F"){
				$organism = "Rat";
			}

			// Taking out for now.... doesn't seem to be correctly reporting location for mouse....
			/*echo "<tr>\n";
           		echo "<td class=$tableid>$key</td><td class=$tableid>";
			echo "<a href=\"http://genome.ucsc.edu/cgi-bin/hgTracks?clade=vertebrate&org=$organism&db=mm8&position=$value&ensGene=pack&knownGene=pack&refGene=pack&xenoRefGene=pack&mrna=pack&multiz17way=pack&jaxRepTranscript=pack\" target=\"_blank\">$value</a><br>";

			 echo "</td></tr>\n";*/

		}


		else{

			echo "<tr>\n";
			echo "<td class=$tableid>$key</td><td class=$tableid>$value</td>";
			echo "</tr>\n";
		}
           }

       echo "</table>\n";
}




function median()
{
    $args = func_get_args();

    switch(func_num_args())
    {
        case 0:
            trigger_error('median() requires at least one parameter',E_USER_WARNING);
            return false;
            break;

        case 1:
            $args = array_pop($args);
            // fallthrough

        default:
            if(!is_array($args)) {
                trigger_error('median() requires a list of numbers to operate on or an array of numbers',E_USER_NOTICE);
                return false;
            }

            sort($args);

            $n = count($args);
            $h = intval($n / 2);

            if($n % 2 == 0) {
                $median = ($args[$h] + $args[$h-1]) / 2;
            } else {
                $median = $args[$h];
            }

            break;
    }
   
    return $median;
}


function mean()
{
//func_get_args to load the parameters into an array
  $sum = 0;
  $vars = func_get_args();
//analyze($vars);
  for ($i = 0; $i < count($vars); $i++)
  {
    $sum += $vars[$i];
  }
  $mean = $sum / count($vars);
  //echo "Mean: {$mean}";
  return $mean;
}


function average($array){
    $sum   = array_sum($array);
    $count = count($array);
    return $sum/$count;
}

//The average function can be use independantly but the deviation function uses the average function.

function deviation ($array){
   
    $avg = average($array);
	$n = count($array);
	$var = 0.0;
    foreach ($array as $value) {
        $variance[] = pow($value-$avg, 2);
	$var += pow($value-$avg, 2);
    }
	//analyze($variance);

   // $deviation = sqrt(average($variance));
	
	$val = $var/($n-1);
	$deviation = sqrt($val);
	//$deviation = sqrt($variance);
    return $deviation;
}

function covariance ($array1, $array2){
	
	if(count($array1) != count($array2)){
		echo "in covariance function: array sizes are not equal!<br>";
		return 0;
	}
	$numelements = count($array1);
	$avg1 = average($array1);
	$avg2 = average($array2);
	$sum = 0.0;
	for($i = 0; $i < $numelements; $i++){
		$val1 = array_pop($array1);
		$val2 = array_pop($array2);
		$sum += ($val1 - $avg1)*($val2-$avg2);
	}
	return $sum/($numelements-1);
}

/*
	function: pearsoncorrelation
	parameters: $array1,$array2
	returns: the pearson correlation coefficient between the two arrays.

*/
function pearsoncorrelation($array1, $array2){

	$deviation1 = deviation($array1);
	$deviation2 = deviation($array2);
	
	$cov12 = covariance($array1, $array2);
	if($deviation1 == 0 || $deviation2 == 0){
		if($deviation1 == 0){
			$deviation1 = 0.0000001;
		}
		if($deviation2 == 0){
			$deviation2 = 0.0000001;
		}
		
	}
	$denominator = $deviation1 * $deviation2;
	if($denominator == 0){
		$denominator = 0.0000001;
	}
	return $cov12/($denominator);
}

function applystandarddeviationfilterviaR($filenamebase, $filenamesarray,$filedescarray, $sdval){
	//echo "in createclusteringheatmap<br>";

	$Rfilename = $filenamebase."sdfilter.R";

	//echo "Rfilename : $Rfilename<br>";
	$command = "touch $Rfilename";
	$str=exec($command);
	$fd4 = fopen($Rfilename,'w');
	fwrite($fd4, "# LOAD THE REQUIRED LIBRARIES\n");
	fwrite($fd4,"library(limma)\n");
	fwrite($fd4,"library(amap)\n");
	fwrite($fd4,"library(gplots)\n");
	fwrite($fd4,"library(RColorBrewer)\n");
	fwrite($fd4,"library(RSvgDevice)\n");
	fwrite($fd4, "library(genefilter)\n");
	# create the filename list....
	fwrite($fd4, "# THE GENERATED LIST OF FILES\n");
	$filelist = "filelist<-c(";
	$index = 0;
	foreach($filenamesarray as $filename){
		if($index == 0){
			$filelist .= "\"$filename\"";
		}else{
			$filelist .= ",\"$filename\"";
		}
		$index++;	
	}
	$filelist .= ")\n";
	#echo "$filelist<br>";
	fwrite($fd4, $filelist);
	fwrite($fd4,"# FOR EASE OF USE, USE MAIMAGES TO LOAD THE DATA FILES\n");
	$dat = "dat<-read.maimages(filelist,source=\"agilent\", columns=list(G=\"gMeanSignal\", Gb=\"gBGMedianSignal\", R=\"gMeanSignal\", Rb=\"rBGMedianSignal\",logratio=\"LogRatio\",control=\"ControlType\"),annotation=c(\"FeatureNum\",\"GeneName\", \"SystematicName\"))\n";
	fwrite($fd4, $dat);
	fwrite($fd4, "# ASSIGN COLUMN NAMES TO THE ARRAYS\n");
	$index = 0;
	
	fwrite($fd4, "colnames(dat)<-c(");
	
	foreach($filedescarray as $desc){
		if($index == 0){
			fwrite($fd4, "\"$desc\"");
		}else{
			fwrite($fd4, ",\"$desc\"");
		}
		$index++;

	}
	fwrite($fd4, ")\n");
	fwrite($fd4, "# EXTRACT OUT THE LOG RATIOS\n");
	
	$numarrays = count($filenamesarray);
	$dat3dataframe = "dat3<-data.frame(";
	$noncontrolremoval = "";
	$dat5dataframe = "dat5<-data.frame(";
	for($i = 1; $i<=$numarrays; $i++){
		$arraynum = "array$i";
		$arrayvals = $arraynum." <-dat\$logratio[,".$i."]\n";
		$realarrayvals = $arraynum."real<-dat3\$".$arraynum."[dat3\$controls != 1]\n";
		if($i == 1){
			$dat3dataframe .= $arraynum;
			$dat5dataframe .= $arraynum."real";
		}else{
			$dat3dataframe .= ", $arraynum";
			$dat5dataframe .= ", ".$arraynum."real";
		}
		$noncontrolremoval .= $realarrayvals;
		fwrite($fd4, $arrayvals);
	}
	$dat3dataframe .= ", controls = dat\$control[,1])\n";
	$dat5dataframe .= ")\n";
	fwrite($fd4, "# CREATE A DATA FRAME W/ THE LOG RATIOS AND THE CONTROLS\n");
	fwrite($fd4, $dat3dataframe);
	fwrite($fd4, "# EXTRACT OUT THE NON-CONTROL VALUES\n");
	fwrite($fd4, $noncontrolremoval);
	fwrite($fd4, "# CREATE A DATA FRAME WITH THE NON-CONTROL VALUES, THE ARRAY NAMES ARE TO THE LEFT OF THE '=' SIGN\n");
	fwrite($fd4, $dat5dataframe);
	fwrite($fd4, "# ASSIGN ROW NAMES\n");
	fwrite($fd4, "rownames(dat5)<-paste(dat\$genes\$FeatureNum[dat\$control[,1] != 1],dat\$genes\$GeneName[dat\$control[,1]!=1],sep=\"_\")\n");
	fwrite($fd4, "# GET THE ROW MEANS OF THE CONTROLS\n");
	fwrite($fd4, "controlrowmeans <- rowMeans(dat5[1:nrow(dat5),1:3])\n");
	fwrite($fd4, "# SUBTRACT OUT THE ROW MEANS OF THE CONTROLS FROM THE CONTROLS AND THE TREATED\n");
	fwrite($fd4, "dat5<-dat5-controlrowmeans\n");
	fwrite($fd4, "# CONVERT TO LOG-BASE 2\n");
	fwrite($fd4, "dat5<-dat5/log10(2)\n");
	fwrite($fd4, "# CREATE A DAT.M MATRIX BY CONVERTING DAT5 TO A MATRIX\n");
	fwrite($fd4, "dat.m = as.matrix(dat5)\n");
	fwrite($fd4, "# ASSIGN COLUMN NAMES TO THE dat.m\n");
	$index = 0;
	
	fwrite($fd4, "colnames(dat.m)<-c(");
	
	foreach($filedescarray as $desc){
		if($index == 0){
			fwrite($fd4, "\"$desc\"");
		}else{
			fwrite($fd4, ",\"$desc\"");
		}
		$index++;

	}
	fwrite($fd4, ")\n");
	fwrite($fd4, "# INVERT THE LOG RATIOS, BECAUSE CY3/CY5, NOT CY5/CY3 AS IMPORTED\n");
	fwrite($fd4, "dat.m <- -dat.m\n");
	fwrite($fd4, "# SET UP A GENE FILTER BASED ON STANDARD DEVIATION\n");
	fwrite($fd4, "rsd<-rowSds(dat.m)\n");
	fwrite($fd4, "i<-rsd >=$sdval \n");
	fwrite($fd4, "dat.f<-dat.m[i,]\n");

	# now we need to get the filtered genes and write them to a file and then put them into a php array to return....
	fwrite($fd4, "# WRITE THE FILTERED GENES\n");
	$sdlist = $filenamebase."sdfilterered.txt";
	$command = "touch $sdlist";
	$str=exec($command);
	fwrite($fd4, "sink(\"$sdlist\")\n");
	fwrite($fd4, "write.table(rownames(dat.f),sep=\"\\t\", row.names=FALSE,col.names=FALSE)\n");
	fwrite($fd4, "sink()\n");
	fflush($fd4);
	fclose($fd4);
	
	$command = "R CMD BATCH $Rfilename";
	#echo "$command<br>";
	$str=exec($command);
#die("after R CMD<hr>");
	# Now read in the file just created and place the results into a php array and return....
	$featurenumarray = array();
	$fd5 = fopen($sdlist, 'r');
	while (!feof($fd5)) {
		$buffer = fgets($fd5, 4096);
		$buffer = trim($buffer);
		if($buffer != ""){
			// Replace any quotes with blanks...
			$item = str_replace("\"", '', $buffer);
			$array = explode("_", $item);
			#echo "$array[0] <br>";
			array_push($featurenumarray, $array[0]);
		}
	}
	return $featurenumarray;
}
/*
	Function: rBasedDifferentialExpression - this function creates the .R file that is subsequently used to identify differentially expresse genes.
	Parameters: 	$filenamebase : this variable is used to assign a file name to the created .R file
			$idArray:  an array containing the array ids of the arrays to be acted on
			$groupdesignationarray:  an array which contains the group designations of the arrays passed in.  this array is
				ordered based on the arrays passed in
			$RbasedCorrection: this is simply the string value of the correction method
			$db:  this is a connection to the underlying database
			$pvalue:  the pvalue cut-off for being designated as differentially expressed.

*/
function rBasedDifferentialExpression($filenamebase, $idArray, $groupdesignationarray, $RbasedCorrection,$db,$pvalue,$datafilelocation,$edgedata){

	#echo "enterint rBasedDifferentialExpression<hr>";
	$subtractcontrols = -1;
	#$height=$featureCount*10; 
	#$width=$arrayCount*180;
	$Rfilename = $filenamebase."diffexp.R";
	$colsep = "";
	#set default correction to fdr
	$correction = "$RbasedCorrection";
	#$correction = $RbasedCorrection;
	/*if(count($sep) >0){
		$colsep = "c(";
		$count = 0;
		foreach($sep as $separatorvalue){
			if($count == 0){
				$colsep .= "$separatorvalue";
			}else{
				$colsep .= ",$separatorvalue";
			}
			$count++;
		}
		$colsep.=")";
	}*/
	//echo "Rfilename : $Rfilename<br>";
	$command = "touch $Rfilename";
	$str=exec($command);
	$fd4 = fopen($Rfilename,'w');
	fwrite($fd4, "# LOAD THE REQUIRED LIBRARIES\n");
	fwrite($fd4,"library(limma)\n");
	fwrite($fd4,"library(amap)\n");
	fwrite($fd4,"library(gplots)\n");
	fwrite($fd4,"library(RColorBrewer)\n");
	fwrite($fd4,"library(RSvgDevice)\n");
	fwrite($fd4,"library(genefilter)\n");
	fwrite($fd4,"library(multtest)\n");
	fwrite($fd4, "# THE GENERATED LIST OF FILES\n");
	$fileliststr = "filelist<-c(";
	$arraynamesstr = "colnames(dat)<-c(";
	$colnamesdatm = "colnames(dat.m)<-c(";
	$logratioextractionstr = "";
	$dat3str = "dat3<-data.frame(";
	$dat5str = "dat5 <-data.frame(";
	$controlextractionstr = "";
	$loopcount = 0;
	$idcount = 1;
	
	foreach($idArray as $id){
		$idSQL = "SELECT FE_data_file,arraydesc FROM agilent_arrayinfo WHERE arrayid = $id";
		$idresult = $db->Execute($idSQL);
		$filerow = $idresult->FetchRow();
		$filename = $filerow[0];
		$arrayname = $filerow[1];
		$logratioextractionstr .= "array".$idcount."<-dat\$logratio[,$idcount]\n";
		$controlextractionstr .= "array".$idcount."real<-dat3\$array".$idcount."[dat3\$controls != 1]\n";
		$thisfile = returndatafile($filename,$datafilelocation,$edgedata,TRUE,1);
		if($loopcount == 0){			
			$fileliststr .= "\"$thisfile\"";
			$arraynamesstr .= "\"$arrayname\"";
			$colnamesdatm .=  "\"$arrayname\"";
			$dat3str .= "array$idcount";
			$dat5str .= "array".$idcount."="."array".$idcount."real";
		}else{
			$fileliststr .= ",\"$thisfile\"";
			$arraynamesstr .= ",\"$arrayname\"";	
			$colnamesdatm .= ",\"$arrayname\"";
			$dat3str .= ",array$idcount";
			$dat5str .= ",array".$idcount."="."array".$idcount."real";
		}
		$loopcount++;
		$idcount++;
	}
	$fileliststr .= ")\n";
	$arraynamesstr .= ")\n";
	$colnamesdatm .= ")\n";
	$dat5str .= ")\n";
	$dat3str .= ",controls = dat\$control[,1])\n";
	fwrite($fd4, $fileliststr);
	fwrite($fd4, "# FOR EASE OF USE, USE MAIMAGES TO LOAD THE DATA FILES\n");
	fwrite($fd4, "dat<-read.maimages(filelist,source=\"agilent\", columns=list(G=\"gMeanSignal\", Gb=\"gBGMedianSignal\", R=\"gMeanSignal\", Rb=\"rBGMedianSignal\",logratio=\"LogRatio\",control=\"ControlType\"),annotation=c(\"FeatureNum\",\"GeneName\", \"SystematicName\"))\n");
	fwrite($fd4, "# ASSIGN COLUMN NAMES TO THE ARRAYS\n");
	fwrite($fd4, $arraynamesstr);
	fwrite($fd4, "# EXTRACT OUT THE LOG RATIOS\n");
	fwrite($fd4, $logratioextractionstr);
	fwrite($fd4, "# CREATE A DATA FRAME W/ THE LOG RATIOS AND THE CONTROLS\n");
	fwrite($fd4, $dat3str);
	fwrite($fd4, "# EXTRACT OUT THE NON-CONTROL VALUES\n");
	fwrite($fd4, $controlextractionstr);
	fwrite($fd4, "controls<-dat3\$controls[dat3\$controls != 1]\n");
	fwrite($fd4, "# CREATE A DATA FRAME W/ THE NON-CONTROL VALUES, THE ARRAY NAMES ARE TO THE LEFT OF THE '=' SIGN\n");
	fwrite($fd4, $dat5str);
	fwrite($fd4, "# ASSIGN ROW NAMES\n");
	fwrite($fd4, "rownames(dat5)<-paste(dat\$genes\$FeatureNum[dat\$control[,1] != 1],dat\$genes\$GeneName[dat\$control[,1]!=1],sep=\"_\")\n");

	if($subtractcontrols == 1){
		fwrite($fd4, "# GET THE ROW MEANS OF THE CONTROLS\n");
		fwrite($fd4, "controlrowmeans <- rowMeans(dat5[1:nrow(dat5),1:numcontrols])\n");
		fwrite($fd4, "# SUBTRACT OUT THE ROW MEANS OF THE CONTROLS FROM THE CONTROLS AND THE TREATED\n");
		fwrite($fd4, "dat5<-dat5-controlrowmeans\n");
	}
	fwrite($fd4, "# CONVERT TO LOG-BASE 2\n");
	fwrite($fd4, "dat5<-dat5/log10(2)\n");
	fwrite($fd4, "# CREATE A DAT.M MATRIX BY CONVERTING DAT5 TO A MATRIX\n");
	fwrite($fd4, "dat.m = as.matrix(dat5)\n");
	fwrite($fd4, $colnamesdatm);

	# this is something that needs to be addressed, because doesn't need to be this way now
	# that we're using limma....w/ a targets file, anyway.
	fwrite($fd4, "# INVERT THE LOG RATIOS, BECAUSE CY3/CY5, NOT CY5/CY3 AS IMPORTED\n");
	fwrite($fd4, "dat.m <- -dat.m\n");
	# This is if a filter has been selected....  right now only fold-change filter is implemented....
	/*fwrite($fd4, "# SET UP A GENE FILTER BASED ON EXPRESSION LEVELS\n");
	fwrite($fd4, "ff<-pOverA(A=3,p=0.5)\n");
	i<-genefilter(dat.m,ff)
	dat.fo<-dat.m[i,]
	i<-genefilter(-dat.m,ff)
	dat.fu<-dat.m[i,]
	dat.f<-rbind(dat.fo,dat.fu)
	# GET THE FILTERED GENES TO WRITE TO A FILE
	rn<-rownames(dat.f)
	*/
	fwrite($fd4, "heatcol<-rev(colorRampPalette(brewer.pal(10,\"Spectral\"))(1024))\n");
	fwrite($fd4, "# STATISTICAL ANALYSES\n");
	#this is where i need to create the group designations based on the $groupdesignationarray 
	fwrite($fd4, "# ASSIGN THE GROUP LABELS (C=CONTROL, T= TREATED)\n");
	$groupstr = "groups<-c(";
	$groupcount = 0;
	foreach($groupdesignationarray as $group){
		if($groupcount == 0){
			$groupstr .= "\"$group\"";
		}else{
			$groupstr .= ",\"$group\"";
		}
		$groupcount++;
	}
	$groupstr .= ")\n";
	fwrite($fd4, $groupstr);
	fwrite($fd4, "groups<-as.factor(groups)\n");
	fwrite($fd4, "design<-model.matrix(~groups)\n");
	
	fwrite($fd4, "# EMPERICAL BAYES W/ UNFILTERED DATA\n");
	fwrite($fd4, "# BUILD THE LINEAR MODEL\n");
	fwrite($fd4, "fit<-lmFit(dat.m,design)\n");
	fwrite($fd4, "# PERFORM EMPERICAL BAYES\n");
	fwrite($fd4, "eb<-eBayes(fit)\n");
	fwrite($fd4, "# GET THE TOP TABLE\n");
	fwrite($fd4, "options(digits=3)\n");
	fwrite($fd4, "tt<-topTable(eb,coef=2,n=nrow(dat.m), adjust=\"$correction\",genelist=fit\$genes)\n");
	fwrite($fd4, "rn<-rownames(tt)[tt\$P.Value<=$pvalue]\n");
	fwrite($fd4, "rn<-as.numeric(rn)\n");
	fwrite($fd4, "dat.s<-dat.m[rn,]\n");
	
	fwrite($fd4, "# CLUSTERING OF THE UNFILTERED DATA\n");
	fwrite($fd4, "clust.genes<-hcluster(x=dat.s, method=\"pearson\",link=\"average\")\n");
	fwrite($fd4, "clust.arrays<-hcluster(x=t(dat.s), method=\"pearson\", link=\"average\")\n");
	fwrite($fd4, "# CREATE A PNG OF THE HEATMAP\n");
	fwrite($fd4, "png(file=\"".$filenamebase."myplot2a.png\",bg=\"white\",pointsize=12,width=1024,height=1024)\n");
	fwrite($fd4, "heatmap.2(x=dat.s, Rowv=as.dendrogram(clust.genes),Colv=as.dendrogram(clust.arrays),col=heatcol,margins=c(20,20),scale=\"none\")\n");
	fwrite($fd4, "dev.off()\n");
	fwrite($fd4, "png(file=\"".$filenamebase."ttestcomparison.png\",width=800,height=800)\n");
	fwrite($fd4, "#  Ordinary t-statistic\n");
	fwrite($fd4, "par(mfrow=c(1,2))\n");
	fwrite($fd4, "ordinary.t <- fit\$coef / fit\$stdev.unscaled / fit\$sigma\n");
	fwrite($fd4, "qqt(ordinary.t,df=fit\$df.residual,main=\"Ordinary t\")\n");
	fwrite($fd4, "abline(0,1)\n");
	fwrite($fd4, "ord <- order(eb\$lods[,2],decreasing=TRUE)\n");
	fwrite($fd4, "top <- ord[1:43532]\n");
	fwrite($fd4, "qqt(eb\$t,df=eb\$df.prior+eb\$df.residual,main=\"Moderated t\")\n");
	fwrite($fd4, "abline(0,1)\n");
	fwrite($fd4, "#  Points off the line may be differentially expressed\n");
	fwrite($fd4, "par(mfrow=c(1,1))\n");
	fwrite($fd4, "dev.off()\n");
	fwrite($fd4, "#Colored Volcano plot\n");
 	fwrite($fd4, "significants = tt\$P.Value <=$pvalue\n");
	fwrite($fd4, "nonsignificants = tt\$P.Value > $pvalue\n");
	fwrite($fd4, "upregulated = tt\$logFC > 0 & tt\$P.Value <=$pvalue\n");
	fwrite($fd4, "downregulated = tt\$logFC < 0 & tt\$P.Value<=$pvalue\n");
 	fwrite($fd4, "maxAbsM <- max(abs(tt\$logFC), na.rm=TRUE)\n");
	fwrite($fd4, "logP <-log10(tt\$P.Value)\n");
	fwrite($fd4, "maxY <- min(c(-min(logP, na.rm=TRUE),10))\n");
	fwrite($fd4, "midpoint <--log10($pvalue)\n");
	fwrite($fd4, "createLegend <- function(names, colors, location=\"bottomright\"){\n");
  	fwrite($fd4, "\tlegend(location, names, bty=\"n\",cex=0.8, pt.bg=\"white\",lty=1,col=colors,lwd=5)\n");
	fwrite($fd4, "}\n");
	fwrite($fd4, "bcount <- function(x,na.rm=F) {\n");
	fwrite($fd4, "# Performs a \"boolean count\", that is, counts the number of TRUE's in a vector.\n");
	fwrite($fd4, "# NJ Barrowman, May 24, 1993. Modified by J. Hoenig, 1 June 2007 so that, if there \n");
	fwrite($fd4, "# are any NAs, the result is NA unless na.rm is set to TRUE\n");
	fwrite($fd4, "\tif (length(x)==0) {return(0)}\n");
	fwrite($fd4, "if (!is.logical(x)) {stop(\"Not a logical vector.\")  }\n");
	fwrite($fd4, "if (na.rm == F & sum(is.na(x))>0) {return(NA)}\n");
	fwrite($fd4, "return(length(x[!is.na(x) & (x==T)]))}\n");
	fwrite($fd4, "numberofsignificantvalues = bcount(significants)\n");
	fwrite($fd4, "bitmap(file=\"".$filenamebase."coloredvolcanoplot.png\", type = \"png16m\", height = 12, width = 12,res=72,)\n");
	#plot(fit$coef[,2],eb$lods[,2], pch=16, cex=0.1, xlab="Log Fold Change", ylab="Log Odds")
	fwrite($fd4, "plot(tt\$logFC[nonsignificants], -logP[nonsignificants] , pch=16, cex=0.2, xlab=\"log2(fold-change)\",\n"); fwrite($fd4, "ylab=\"-log10(p-value)\",ylim=c(0, maxY), xlim=c(-maxAbsM, maxAbsM), main=\"Volcano Plot\",col=\"grey\")\n");
	fwrite($fd4, "abline(midpoint,0, col=\"grey\", lty=2)\n");
	fwrite($fd4, "abline(v=0,col=\"grey\", lty=2)\n");

	fwrite($fd4, "points(tt\$logFC[upregulated], -logP[upregulated] , pch=16, cex=0.6, col=\"red\")\n");
	fwrite($fd4, "points(tt\$logFC[downregulated], -logP[downregulated] , pch=16, cex=0.6, col=\"green\")\n");
	fwrite($fd4, "text(maxAbsM-1,-log10(.001) + .05,\"p-Value = $pvalue\", cex=.6,col=\"blue\")\n");
	fwrite($fd4, "createLegend(c(\"Upregulated & Differentially Expressed\",\"Downregulated & Differentially Expressed\"), c(\"red\",\"green\"), location=\"bottomleft\")\n");
	#text(tt$logFC[significants],-logP[significants] ,labels=as.character(tt$ID[significants]),cex=0.5,col="blue")
	fwrite($fd4, "dev.off()\n");
	
	fwrite($fd4, "ttcolnames <- names(tt)\n");
	#fwrite($fd4, "cat(\"<html><body>\", file=\"".$filenamebase.".html\")\n");
	fwrite($fd4, "cat(\"<h1>Results</h1>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	fwrite($fd4, "cat(\"The \",numberofsignificantvalues, \" genes designated as being differentially expressed; sorted by B-statistics. A complete list can be found in the file <a href=\\\"Rankedgenes.txt\\\">Rankedgenes.txt</a><p>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	fwrite($fd4, "cat(\"<table width=600 border='1' frame='border' rules='none'>\", file=\"".$filenamebase.".html\", append=TRUE)\n");

	fwrite($fd4, "cat(\"<tr bgcolor='ddddff'>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	fwrite($fd4, "for(j in 1:ncol(tt)){\n");
	fwrite($fd4, "\tcat(\"<td align='center'><b>\",ttcolnames[j],\"</b></td>\",file=\"".$filenamebase.".html\",append=TRUE)\n");
	fwrite($fd4, "\t}\n");
	fwrite($fd4, "cat(\"</tr>\",file=\"".$filenamebase.".html\",append=TRUE)\n");

	
		fwrite($fd4, "#Get the rownames (i.e., genes and feature numbers) from tt\n");
		fwrite($fd4, "ttrownames = rownames(tt)\n");
		fwrite($fd4, "for(i in 1:numberofsignificantvalues){\n");
		fwrite($fd4, "cat(\"<tr bgcolor=''>\", file=\"$htmlfile\", append=TRUE)\n");
		fwrite($fd4, "featurevals <- unlist(strsplit(ttrownames[i], \"_\"))\n");
		fwrite($fd4, "featurenum <- featurevals[1]\n"); 
		fwrite($fd4, "featurename <- featurevals[2]\n");
		fwrite($fd4, "\t\tcat(\"<td><a href='$featureurl\",featurenum,\"' target='_blank'>\",featurename,\"</a></td>\",file=\"$htmlfile\",append=TRUE)\n");
		fwrite($fd4, "for(j in 1:ncol(tt)){\n");
		fwrite($fd4, "\t\tcat(\"<td>\",tt[i,j],\"</td>\",file=\"$htmlfile\",append=TRUE)\n");
		fwrite($fd4, "}\n");
		fwrite($fd4, "cat(\"</tr>\",file=\"$htmlfile\",append=TRUE)\n");
		fwrite($fd4, "}\n");
		#fwrite($fd4, "for(i in 1:numberofsignificantvalues){\n");	
		#fwrite($fd4, "for(j in 1:ncol(tt)){\n");
		#fwrite($fd4, "\tif(j == 1){\n");
		#fwrite($fd4, "featurevals <- unlist(strsplit(tt[i,j], \"_\"))\n");
		#fwrite($fd4, "featurenum <- featurevals[1]\n"); 
		#fwrite($fd4, "featurename <- featurevals[2]\n");
		#fwrite($fd4, "\t\tcat(\"<td><a href='$featureurl\",featurenum,\"' target='_blank'>\",featurename,\"</a></td>\",file=\"$htmlfile\",append=TRUE)\n");
		#fwrite($fd4, "\t}else{\n");
		#fwrite($fd4, "\t\tcat(\"<td>\",tt[i,j],\"</td>\",file=\"$htmlfile\",append=TRUE)\n");
		#fwrite($fd4, "}\n");
		#fwrite($fd4, "\t}\n");
		#fwrite($fd4, "cat(\"</tr>\",file=\"$htmlfile\",append=TRUE)\n");
	fwrite($fd4, "cat(\"</tr>\",file=\"".$filenamebase.".html\",append=TRUE)\n");
	fwrite($fd4, "}\n");
	fwrite($fd4, "cat(\"</table>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	fwrite($fd4, "cat(\"<h2>Student's t Quantile-Quantile Plot</h2>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	fwrite($fd4, "cat(\"<img src='".$filenamebase."ttestcomparison.png'><p>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	fwrite($fd4, "cat(\"<h2>Volcano Plot</h2>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	fwrite($fd4, "cat(\"<img src='".$filenamebase."coloredvolcanoplot.png'><p>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	fwrite($fd4, "cat(\"<h2>Clustering</h2>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	fwrite($fd4, "cat(\"<img src='".$filenamebase."myplot2a.png'><p>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	fwrite($fd4, "cat(\"<p><hr><p>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	#fwrite($fd4, "cat(\"</body></html>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	$command = "R CMD BATCH $Rfilename";
	//echo "$command<br>";
	$str=exec($command);	
	return "$filenamebase.html";
}

/*
	Function: rBasedDifferentialExpression2 - this function creates the .R file that is subsequently used to identify differentially expresse genes.
						  it uses a targets file....
	Parameters: 	$filenamebase : this variable is used to assign a file name to the created .R file
			$idArray:  an array containing the array ids of the arrays to be acted on
			$groupdesignationarray:  an array which contains the group designations of the arrays passed in.  this array is
				ordered based on the arrays passed in
			$RbasedCorrection: this is simply the string value of the correction method
			$db:  this is a connection to the underlying database
			$pvalue:  the pvalue cut-off for being designated as differentially expressed.
	
	Returns: an html file showing the comparisons; differentially expressed genes and volcano plots.
			

*/
function rBasedDifferentialExpression2($filenamebase, $idArray, $RbasedCorrection,$db,$pvalue,$datafilelocation,$edgedata,$IMAGESdir,$IMAGESreldir,$RPath, $reference, $contrastList, $contrastcount,$comparisonnames,$organismselected,$customlimma){

	$thisorganism = $organismselected;
	require './phpinc/organismurlselection.inc';
	#echo "enterint rBasedDifferentialExpression<hr>";
	$subtractcontrols = -1;
	#$height=$featureCount*10; 
	#$width=$arrayCount*180;
	$Rfilename = $IMAGESdir."/".$filenamebase."diffexp.R"; # provides variable $featureurl which selects the info page for a gene based on the organism...
	$colsep = "";
	#set default correction to fdr
	$correction = "$RbasedCorrection";
	//echo "filenamebase = $filenamebase<br>";
	#$correction = $RbasedCorrection;
	/*if(count($sep) >0){
		$colsep = "c(";
		$count = 0;
		foreach($sep as $separatorvalue){
			if($count == 0){
				$colsep .= "$separatorvalue";
			}else{
				$colsep .= ",$separatorvalue";
			}
			$count++;
		}
		$colsep.=")";
	}*/
	//echo "Rfilename : $Rfilename<br>";
	$command = "touch $Rfilename";
	$str=exec($command);
	$fd4 = fopen($Rfilename,'w');
	fwrite($fd4, "# LOAD THE REQUIRED LIBRARIES\n");
	fwrite($fd4,"library(limma)\n");
	fwrite($fd4,"library(amap)\n");
	fwrite($fd4,"library(gplots)\n");
	fwrite($fd4,"library(RColorBrewer)\n");
	fwrite($fd4,"library(genefilter)\n");
	fwrite($fd4,"library(multtest)\n");
	fwrite($fd4, "targets <- readTargets(file=\"$IMAGESdir/targets$filenamebase.txt\")\n");
	fwrite($fd4, "filelist<-targets\$FileName\n");
	fwrite($fd4, "# FOR EASE OF USE, USE MAIMAGES TO LOAD THE DATA FILES\n");
	fwrite($fd4, "dat<-read.maimages(filelist,source=\"agilent\", columns=list(G=\"gMeanSignal\", Gb=\"gBGMedianSignal\", R=\"gMeanSignal\", Rb=\"rBGMedianSignal\",logratio=\"LogRatio\",control=\"ControlType\"),annotation=c(\"FeatureNum\",\"GeneName\", \"SystematicName\"))\n");
	fwrite($fd4, "colnames(dat)<-targets\$Labels\n");
	$logratioextractionstr = "";
	$dat3str = "dat3<-data.frame(";
	$dat5str = "dat5 <-data.frame(";
	$controlextractionstr = "";
	$loopcount = 0;
	$idcount = 1;
	
	foreach($idArray as $id){		
		$logratioextractionstr .= "array".$idcount."<-dat\$logratio[,$idcount]\n";
		$controlextractionstr .= "array".$idcount."real<-dat3\$array".$idcount."[dat3\$controls != 1]\n";
		if($loopcount == 0){			
			$dat3str .= "array$idcount";
			$dat5str .= "array".$idcount."="."array".$idcount."real";
		}else{
			$dat3str .= ",array$idcount";
			$dat5str .= ",array".$idcount."="."array".$idcount."real";
		}
		$loopcount++;
		$idcount++;
	}
	$dat5str .= ")\n";
	$dat3str .= ",controls = dat\$control[,1])\n";
	fwrite($fd4, "# EXTRACT OUT THE LOG RATIOS\n");
	fwrite($fd4, $logratioextractionstr);
	fwrite($fd4, "# CREATE A DATA FRAME W/ THE LOG RATIOS AND THE CONTROLS\n");
	fwrite($fd4, $dat3str);
	fwrite($fd4, "# EXTRACT OUT THE NON-CONTROL VALUES\n");
	fwrite($fd4, $controlextractionstr);
	fwrite($fd4, "controls<-dat3\$controls[dat3\$controls != 1]\n");
	fwrite($fd4, "# CREATE A DATA FRAME W/ THE NON-CONTROL VALUES, THE ARRAY NAMES ARE TO THE LEFT OF THE '=' SIGN\n");
	fwrite($fd4, $dat5str);
	fwrite($fd4, "# ASSIGN ROW NAMES\n");
	fwrite($fd4, "rownames(dat5)<-paste(dat\$genes\$FeatureNum[dat\$control[,1] != 1],dat\$genes\$GeneName[dat\$control[,1]!=1],sep=\"_\")\n");

	
	fwrite($fd4, "# CONVERT TO LOG-BASE 2\n");
	fwrite($fd4, "dat5<-dat5/log10(2)\n");
	fwrite($fd4, "# CREATE A DAT.M MATRIX BY CONVERTING DAT5 TO A MATRIX\n");
	fwrite($fd4, "dat.m = as.matrix(dat5)\n");
	fwrite($fd4, "colnames(dat.m)<-targets\$Labels\n");
	fwrite($fd4, "heatcol<-rev(colorRampPalette(brewer.pal(10,\"Spectral\"))(1024))\n");
	
	
	if($customlimma != ""){
		# Explode the customlimma text into an array w/ /n as the separator....
		$customcodearray=explode("\n", $customlimma);
		foreach($customcodearray as $aline){
			fwrite($fd4, "$aline\n");
		}
		
	}else{
		fwrite($fd4, "# STATISTICAL ANALYSES\n");
		if($reference != -1){
			$reference = "sample".$reference;
			$string = "design<-modelMatrix(targets, ref='$reference')\n";
			fwrite($fd4, $string);
		}
		fwrite($fd4, "# EMPERICAL BAYES W/ UNFILTERED DATA\n");
		fwrite($fd4, "# BUILD THE LINEAR MODEL\n");
		fwrite($fd4, "fit<-lmFit(dat.m,design)\n");
		# Make the contrasts....
		$contrasts = "contrasts.matrix<-makeContrasts($contrastList,levels=design)\n";
		fwrite($fd4, $contrasts);
		fwrite($fd4, "fit2<-contrasts.fit(fit,contrasts.matrix)\n");
		/*
		fwrite($fd4, "groups<-as.factor(groups)\n");
		write($fd4, "design<-model.matrix(~groups)\n");
		*/
		
		fwrite($fd4, "# PERFORM EMPERICAL BAYES\n");
		fwrite($fd4, "eb<-eBayes(fit2)\n");
		
		
	}
	
	$htmlfile = $IMAGESdir."/".$filenamebase.".html";
	fwrite($fd4, "cat(\"<h1>Results</h1>\", file=\"$htmlfile\", append=TRUE)\n");
	fwrite($fd4, "cat(\"<div id='tabs' style='width: 700px; float:left'><ul>\", file=\"$htmlfile\", append=TRUE)\n");
	for($j= 1; $j<=$contrastcount; $j++){
		$index = $j -1;
		$thisname = $comparisonnames[$index];
		fwrite($fd4, "cat(\"<li><a href=\\\"#fragment-$j\\\"><span>$thisname</span></a></li>\", file=\"$htmlfile\", append=TRUE)\n");
	}
	fwrite($fd4, "cat(\"</ul>\", file=\"$htmlfile\", append=TRUE)\n");

	
	for($j = 1; $j<=$contrastcount; $j++){
		
		fwrite($fd4, "cat(\"<div id=\\\"fragment-$j\\\">\", file=\"$htmlfile\", append=TRUE)\n");
		fwrite($fd4, "# GET THE TOP TABLE FOR coef #$\n");
		fwrite($fd4, "options(digits=3)\n");
		fwrite($fd4, "tt<-topTable(eb,coef=$j,n=nrow(dat.m), adjust=\"$correction\",genelist=fit\$genes)\n");
		$rankedgenesfile = $IMAGESdir."/".$filenamebase."orderedgenes_".$j.".txt";
		$genelistfile = $IMAGESdir."/".$filenamebase."genelist_".$j.".txt";
		fwrite($fd4, "write.table(tt,sep=\"\t\",file=\"$rankedgenesfile\",quote=FALSE,row.names=FALSE)\n");
		fwrite($fd4, "rn<-rownames(tt)[tt\$P.Value<=$pvalue]\n");
		#fwrite($fd4, "rn<-as.numeric(rn)\n");
		fwrite($fd4, "dat.s<-dat.m[rn,]\n");
		#  write dat.s to a file.....  this file will contain the differentially expressed genes and be used for saving a gene list....
		fwrite($fd4, "write.table(dat.s,sep=\"\t\",file=\"$genelistfile\",quote=FALSE,row.names=TRUE,col.names=TRUE)\n");
		#fwrite($fd4, "# CLUSTERING OF THE UNFILTERED DATA\n");
		#fwrite($fd4, "clust.genes<-hcluster(x=dat.s, method=\"pearson\",link=\"average\")\n");
		#fwrite($fd4, "clust.arrays<-hcluster(x=t(dat.s), method=\"pearson\", link=\"average\")\n");
		#fwrite($fd4, "# CREATE A PNG OF THE HEATMAP\n");
		#fwrite($fd4, "png(file=\"./".$IMAGESreldir."/".$filenamebase."myplot2a".$j.".png\",bg=\"white\",pointsize=12,width=1024,height=1024)\n");
		#fwrite($fd4, "heatmap.2(x=dat.s, Rowv=as.dendrogram(clust.genes),Colv=as.dendrogram(clust.arrays),col=heatcol,margins=c(20,20),scale=\"none\")\n");
		#fwrite($fd4, "dev.off()\n");
		/*fwrite($fd4, "png(file=\"".$filenamebase."ttestcomparison".$j.".png\",width=800,height=800)\n");
		
		fwrite($fd4, "#ORDINARY VS MODIFIED t-statistics");
		fwrite($fd4, "#  Ordinary t-statistic\n");
		fwrite($fd4, "par(mfrow=c(1,2))\n");
		fwrite($fd4, "ordinary.t <- fit\$coef / fit\$stdev.unscaled / fit\$sigma\n");
		fwrite($fd4, "qqt(ordinary.t,df=fit\$df.residual,main=\"Ordinary t\")\n");
		fwrite($fd4, "abline(0,1)\n");
		fwrite($fd4, "ord <- order(eb\$lods[,2],decreasing=TRUE)\n");
		fwrite($fd4, "top <- ord[1:43532]\n");
		fwrite($fd4, "qqt(eb\$t,df=eb\$df.prior+eb\$df.residual,main=\"Moderated t\")\n");
		fwrite($fd4, "abline(0,1)\n");
		fwrite($fd4, "#  Points off the line may be differentially expressed\n");
		fwrite($fd4, "par(mfrow=c(1,1))\n");
		fwrite($fd4, "dev.off()\n");
		*/
		fwrite($fd4, "#Colored Volcano plot\n");
		fwrite($fd4, "significants = tt\$P.Value <=$pvalue\n");
		fwrite($fd4, "nonsignificants = tt\$P.Value > $pvalue\n");
		fwrite($fd4, "upregulated = tt\$logFC > 0 & tt\$P.Value <=$pvalue\n");
		fwrite($fd4, "downregulated = tt\$logFC < 0 & tt\$P.Value<=$pvalue\n");
		fwrite($fd4, "maxAbsM <- max(abs(tt\$logFC), na.rm=TRUE)\n");
		fwrite($fd4, "logP <-log10(tt\$P.Value)\n");
		fwrite($fd4, "maxY <- min(c(-min(logP, na.rm=TRUE),10))\n");
		fwrite($fd4, "midpoint <--log10($pvalue)\n");
		fwrite($fd4, "# Declare the createLegend function\n");
		fwrite($fd4, "createLegend <- function(names, colors, location=\"bottomright\"){\n");
		fwrite($fd4, "\tlegend(location, names, bty=\"n\",cex=0.8, pt.bg=\"white\",lty=1,col=colors,lwd=5)\n");
		fwrite($fd4, "}\n");
		fwrite($fd4, "# END createLegend function\n");
		fwrite($fd4, "bcount <- function(x,na.rm=F) {\n");
		fwrite($fd4, "# Performs a \"boolean count\", that is, counts the number of TRUE's in a vector.\n");
		fwrite($fd4, "# NJ Barrowman, May 24, 1993. Modified by J. Hoenig, 1 June 2007 so that, if there \n");
		fwrite($fd4, "# are any NAs, the result is NA unless na.rm is set to TRUE\n");
		fwrite($fd4, "\tif (length(x)==0) {return(0)}\n");
		fwrite($fd4, "if (!is.logical(x)) {stop(\"Not a logical vector.\")  }\n");
		fwrite($fd4, "if (na.rm == F & sum(is.na(x))>0) {return(NA)}\n");
		fwrite($fd4, "return(length(x[!is.na(x) & (x==T)]))}\n");
		fwrite($fd4, "numberofsignificantvalues = bcount(significants)\n");
		#fwrite($fd4, "bitmap(file=\"".$filenamebase."coloredvolcanoplot".$j.".png\", type = \"png16m\", height = 12, width = 12,res=72,)\n");
		#plot(fit$coef[,2],eb$lods[,2], pch=16, cex=0.1, xlab="Log Fold Change", ylab="Log Odds")
		fwrite($fd4, "png(file=\"".$IMAGESdir."/".$filenamebase."coloredvolcanoplot".$j.".png\", width=800,height=800)\n");
		fwrite($fd4, "plot(tt\$logFC[nonsignificants], -logP[nonsignificants] , pch=16, cex=0.2, xlab=\"log2(fold-change)\",\n"); fwrite($fd4, "ylab=\"-log10(p-value)\",ylim=c(0, maxY), xlim=c(-maxAbsM, maxAbsM), main=\"Volcano Plot\",col=\"grey\")\n");
		fwrite($fd4, "abline(midpoint,0, col=\"grey\", lty=2)\n");
		fwrite($fd4, "abline(v=0,col=\"grey\", lty=2)\n");
	
		fwrite($fd4, "points(tt\$logFC[upregulated], -logP[upregulated] , pch=16, cex=0.6, col=\"red\")\n");
		fwrite($fd4, "points(tt\$logFC[downregulated], -logP[downregulated] , pch=16, cex=0.6, col=\"green\")\n");
		fwrite($fd4, "text(maxAbsM-1,-log10(.001) + .05,\"p-Value = $pvalue\", cex=.6,col=\"blue\")\n");
		fwrite($fd4, "createLegend(c(\"Upregulated & Differentially Expressed\",\"Downregulated & Differentially Expressed\"), c(\"red\",\"green\"), location=\"bottomleft\")\n");
		#text(tt$logFC[significants],-logP[significants] ,labels=as.character(tt$ID[significants]),cex=0.5,col="blue")
		fwrite($fd4, "dev.off()\n");
		
		fwrite($fd4, "ttcolnames <- names(tt)\n");
		#fwrite($fd4, "cat(\"<html><body>\", file=\"$htmlfile\")\n");
		
		fwrite($fd4, "cat(\"The \",numberofsignificantvalues, \" genes designated as being differentially expressed; sorted by B-statistics.<br> \", file=\"$htmlfile\", append=TRUE)\n");
		$index = $j - 1;
		$thisname = $comparisonnames[$index];
		$genelistfilename = $comparisonnames[$index]." differentially expressed";
		fwrite($fd4, "cat(\"Here is the file containing all of the ranked genes: <a href='.$IMAGESreldir/".$filenamebase."orderedgenes_".$j.".txt' target='_blank'>$thisname</a><br>\", file=\"$htmlfile\", append=TRUE)\n");
		fwrite($fd4, "cat(\"Here is a file containing the differentially expressed genes: <a href='.$IMAGESreldir/".$filenamebase."genelist_".$j.".txt' target='_blank'>$genelistfilename</a><br>\",file=\"$htmlfile\", append=TRUE)\n");
		fwrite($fd4, "cat(\"Save the set of differentially expressed genes as a EDGE<sup>3</sup> gene list? <a href='./phpinc/importgenelistresult.inc.php?type=$organismselected&featurefilenumber=$filenamebase&querytype=1&contrastnumber=$j&name=$thisname' target='_blank'>Save gene list</a><br>\", file=\"$htmlfile\",append=TRUE)\n");
		fwrite($fd4, "cat(\"<table width=600 border='1' frame='border' rules='none'>\", file=\"$htmlfile\", append=TRUE)\n");
	
		fwrite($fd4, "cat(\"<tr bgcolor='ddddff'>\", file=\"$htmlfile\", append=TRUE)\n");
		fwrite($fd4, "for(j in 1:ncol(tt)){\n");
		fwrite($fd4, "\tcat(\"<td align='center'><b>\",ttcolnames[j],\"</b></td>\",file=\"$htmlfile\",append=TRUE)\n");
		fwrite($fd4, "\t}\n");
		fwrite($fd4, "cat(\"</tr>\",file=\"$htmlfile\",append=TRUE)\n");
	
		fwrite($fd4, "#Get the rownames (i.e., genes and feature numbers) from tt\n");
		fwrite($fd4, "ttrownames = rownames(tt)\n");
		fwrite($fd4, "for(i in 1:numberofsignificantvalues){\n");
		fwrite($fd4, "cat(\"<tr bgcolor=''>\", file=\"$htmlfile\", append=TRUE)\n");
		fwrite($fd4, "featurevals <- unlist(strsplit(ttrownames[i], \"_\"))\n");
		fwrite($fd4, "featurenum <- featurevals[1]\n"); 
		fwrite($fd4, "featurename <- featurevals[2]\n");
		fwrite($fd4, "\t\tcat(\"<td><a href='$featureurl\",featurenum,\"' target='_blank'>\",featurename,\"</a></td>\",file=\"$htmlfile\",append=TRUE)\n");
		fwrite($fd4, "for(j in 1:ncol(tt)){\n");
		fwrite($fd4, "\t\tcat(\"<td>\",tt[i,j],\"</td>\",file=\"$htmlfile\",append=TRUE)\n");
		fwrite($fd4, "}\n");
		fwrite($fd4, "cat(\"</tr>\",file=\"$htmlfile\",append=TRUE)\n");
		fwrite($fd4, "}\n");
		#fwrite($fd4, "for(i in 1:numberofsignificantvalues){\n");	
		#fwrite($fd4, "for(j in 1:ncol(tt)){\n");
		#fwrite($fd4, "\tif(j == 1){\n");
		#fwrite($fd4, "featurevals <- unlist(strsplit(tt[i,j], \"_\"))\n");
		#fwrite($fd4, "featurenum <- featurevals[1]\n"); 
		#fwrite($fd4, "featurename <- featurevals[2]\n");
		#fwrite($fd4, "\t\tcat(\"<td><a href='$featureurl\",featurenum,\"' target='_blank'>\",featurename,\"</a></td>\",file=\"$htmlfile\",append=TRUE)\n");
		#fwrite($fd4, "\t}else{\n");
		#fwrite($fd4, "\t\tcat(\"<td>\",tt[i,j],\"</td>\",file=\"$htmlfile\",append=TRUE)\n");
		#fwrite($fd4, "}\n");
		#fwrite($fd4, "\t}\n");
		#fwrite($fd4, "cat(\"</tr>\",file=\"$htmlfile\",append=TRUE)\n");
		#fwrite($fd4, "}\n");
		fwrite($fd4, "cat(\"</table>\", file=\"$htmlfile\", append=TRUE)\n");
		#fwrite($fd4, "cat(\"<h2>Student's t Quantile-Quantile Plot</h2>\", file=\"$htmlfile\", append=TRUE)\n");
		#fwrite($fd4, "cat(\"<img src='".$filenamebase."ttestcomparison".$j.".png'><p>\", file=\"$htmlfile\", append=TRUE)\n");
		fwrite($fd4, "cat(\"<h2>Volcano Plot</h2>\", file=\"$htmlfile\", append=TRUE)\n");
		fwrite($fd4, "cat(\"<img src='.".$IMAGESreldir."/".$filenamebase."coloredvolcanoplot".$j.".png'><p>\", file=\"$htmlfile\", append=TRUE)\n");
		#fwrite($fd4, "cat(\"<h2>Clustering</h2>\", file=\"$htmlfile\", append=TRUE)\n");
		#fwrite($fd4, "cat(\"<img src='.".$IMAGESreldir."/".$filenamebase."myplot2a".$j.".png'><p>\", file=\"$htmlfile\", append=TRUE)\n");
		#fwrite($fd4, "cat(\"<p><hr><p>\", file=\"$htmlfile\", append=TRUE)\n");
		fwrite($fd4, "cat(\"</div>\", file=\"$htmlfile\", append=TRUE)\n");
	}
	#fwrite($fd4, "cat(\"</body></html>\", file=\"".$filenamebase.".html\", append=TRUE)\n");
	# need to check the OS.  for some reason windows outputs the rout file to the directory where the code is located, but linux outputs to the directory where R is called.
	# assuming if not windows than a unix-based os (i.e., linux)
	
	$command = $RPath."R CMD BATCH \"$Rfilename\"";

	//echo "$command<br>";
	$str=exec($command);
		if (!stristr(PHP_OS, 'WIN')) {
			$routfilename = $filenamebase."diffexp.Rout";
			$routdestfilename = $Rfilename."out";
			# first copy the file
			if(!copy($routfilename, $routdestfilename)){
				echo "Error copying over Rout file.<br>";
			}
			# then delete the original
			if(!unlink($routfilename)){
				echo "Error deleting Rout file.<br>";
			}
		}
	//echo "<hr>STR: $str<br>";
	
	return $htmlfile;
	
	
}






function createclusteringheatmap($filenamebase, $logfilename,$featureCount,$arrayCount,$sep){
	echo "in createclusteringheatmap with R<br>";

	$height=$featureCount*10; 
	$width=$arrayCount*180;
	$Rfilename = $filenamebase."clustering.R";
	$colsep = "";
	if(count($sep) >0){
		$colsep = "c(";
		$count = 0;
		foreach($sep as $separatorvalue){
			if($count == 0){
				$colsep .= "$separatorvalue";
			}else{
				$colsep .= ",$separatorvalue";
			}
			$count++;
		}
		$colsep.=")";
	}
	//echo "Rfilename : $Rfilename<br>";
	$command = "touch $Rfilename";
	$str=exec($command);
	$fd4 = fopen($Rfilename,'w');
	fwrite($fd4, "#Load Required Libraries\n");
	fwrite($fd4,"library(limma)\n");
	//fwrite($fd4,"library(amap)\n");
	fwrite($fd4,"library(gplots)\n");
	fwrite($fd4,"library(RColorBrewer)\n");
	//fwrite($fd4,"library(RSvgDevice)\n");
	fwrite($fd4, "# Define the file path\n");
	$datapath = "cluster.datapath <- file.path('$logfilename')\n";
	fwrite($fd4, $datapath);
	fwrite($fd4, "# Read the data into a matrix\n");
	
	$readcsv = "exprsData<-as.matrix(read.table(cluster.datapath,header=TRUE,sep=\"\\t\",row.names=1,as.is=TRUE))\n";
	fwrite($fd4, $readcsv);
	//$line = "exampleSet<-new(\"ExpressionSet\",exprs=exprsData)\n";
	//	fwrite($fd4, $line);

	
	fwrite($fd4, "# Define colors for the heatmap\n");
	$colormap= "heatcol<-rev(colorRampPalette(brewer.pal(10,\"Spectral\"))(1024))\n";
	fwrite($fd4, $colormap);
	
	fwrite($fd4, "# Perform hierarchical clustering using the base hclust method with Pearson correlation\n");
	$line = "clust.arrays <- hclust(dist(t(exprsData), method=\"euclidean\"),method=\"average\")\n";
	fwrite($fd4, $line);


	$line = "clust.genes <- hclust(dist(exprsData, method=\"euclidean\"),method=\"average\")\n";
	fwrite($fd4, $line);
	//$line = "clust.arrays<-hcluster(x=t(exprs(exampleSet)),method=\"pearson\",link=\"average\")\n";	
	//fwrite($fd4, $line);
	
	//$line = "clust.genes<-hcluster(x=exprs(exampleSet),method=\"pearson\",link=\"average\")\n";
	//fwrite($fd4, $line);
	# width=$width, height=$height, 
	$line = "png(file=\"".$filenamebase."clustering.png\", width=$width, height=$height, bg=\"white\",units=\"px\", pointsize=12)\n";
	//$line = "png(file=\"".$filenamebase."clustering.png\", bg=\"white\", units=\"px\")\n";
	//$line = "png(file=\"".$filenamebase."clustering.png\", bg=\"white\",units=\"px\", pointsize=12)\n";
	fwrite($fd4, $line);
	#cexCol=1,

	if($colsep != ""){
		$line = "heatmap.2(x=exprsData, Rowv=as.dendrogram(clust.genes),Colv=as.dendrogram(clust.arrays), col=heatcol, margins=c(20,20),colsep=$colsep , scale=\"none\")\n";
		$line = "heatmap.2(x=exprsData, Rowv=as.dendrogram(clust.genes),Colv=as.dendrogram(clust.arrays), col=heatcol, margins=c(20,20),colsep=$colsep , scale=\"none\")\n";

	}else{
		$line = "heatmap.2(x=exprsData, Rowv=as.dendrogram(clust.genes),Colv=as.dendrogram(clust.arrays), col=heatcol, margins=c(20,20),scale=\"none\")\n";
	}
	fwrite($fd4, $line);
	fwrite($fd4, "dev.off()\n");
	$line = "devSVG(file=\"".$filenamebase."clusteringdev.svg\",bg=\"white\")\n";
	fwrite($fd4, $line);
	if($colsep == ""){
		$line = "heatmap.2(x=exprsData, Rowv=as.dendrogram(clust.genes),Colv=as.dendrogram(clust.arrays), col=heatcol, margins=c(20,20),cexCol=.5,cexRow=.5)\n";
	}else{
		$line = "heatmap.2(x=exprsData, Rowv=as.dendrogram(clust.genes),Colv=as.dendrogram(clust.arrays), col=heatcol, margins=c(20,20),colsep=$colsep ,cexCol=.5,cexRow=.5)\n";

	}
	fwrite($fd4, $line);
	fwrite($fd4, "dev.off()\n");
	/*$line = "svg(file=\"".$filenamebase."clustering.svg\",bg=\"white\",pointsize=4)\n";
	fwrite($fd4, $line);
	if($colsep == ""){
		$line = "heatmap.2(x=exprsData, Rowv=as.dendrogram(clust.genes),Colv=as.dendrogram(clust.arrays), col=heatcol, margins=c(20,20))\n";
	}else{
		$line = "heatmap.2(x=exprsData, Rowv=as.dendrogram(clust.genes),Colv=as.dendrogram(clust.arrays), col=heatcol,colsep=$colsep , margins=c(20,20))\n";
	}
	fwrite($fd4, $line);
	fwrite($fd4, "dev.off()\n");
	*/
	fflush($fd4);
	fclose($fd4);
	$command = "R CMD BATCH \"$Rfilename\" >> $Rfilename.out";
	//echo "$command<br>";
	$str=exec($command);
}

function createcorrelationheatmap($corarray, $filenamebase,$sigFeatureNames){
	$squarewidth = 20;
	$squareheight = 10;
	$numelements = count($corarray);
	$viewx = $numelements * $squarewidth + 300;
	$viewy = $numelements * $squareheight + 300;
	//echo "num elements = $numelements<br>";
	// Create the file from the matrix...
	//echo "in createcorrelationheatmap<br>";

	$svgfilename = $filenamebase."correlation.svg";
					$command = "touch $svgfilename";
					//echo "$command<br>";
					$str=exec($command);
	 $fd = fopen($svgfilename,'w');
	$tablefilename = $filenamebase."correlation.html";
					$command = "touch $tablefilename";
					//echo "$command<br>";
					$str=exec($command);
	 $fd2 = fopen($tablefilename,'w');

	$csvfilename = $filenamebase."correlation.csv";
					$command = "touch $csvfilename";
					//echo "$command<br>";
					$str=exec($command);
	 $fd3 = fopen($csvfilename,'w');


	// Create R file....
	$Rfilename = $filenamebase."correlation.R";
	$command = "touch $Rfilename";
	$str=exec($command);
	 $fd4 = fopen($Rfilename,'w');
	fwrite($fd4,"library(gplots)\n");
	$datapath = "cluster.datapath <- file.path('$csvfilename')\n";
	fwrite($fd4, $datapath);
	$readcsv = "cluster.data <- read.csv(cluster.datapath, header=TRUE, quote=\"\\\"\")\n";
	fwrite($fd4, $readcsv);
	//cluster.data = t(cluster.data)
	$lastcol = $numelements+1;
	$line = "cluster.data <- cluster.data[,2:$lastcol]\n";
	fwrite($fd4, $line);

	fwrite($fd4,"cluster.data <- t(cluster.data)\n");
	fwrite($fd4, "colnames(cluster.data) <- rownames(cluster.data)\n");
	
	$line = "png(file=\"".$filenamebase."corr.png\", bg=\"white\", width=$viewy, height=$viewy, units=\"px\", pointsize=20)\n";
	fwrite($fd4, $line);
	$line = "heatmap.2(cluster.data,col=greenred(75),scale=\"none\", margins = c(5, 5),key=TRUE)\n";
	fwrite($fd4, $line);
	fwrite($fd4, "dev.off()\n");
	$line = "png(file=\"".$filenamebase."corr.png\", bg=\"white\", width=$viewy, height=$viewy, units=\"px\", pointsize=20)\n";
	fwrite($fd4, $line);
	$line = "heatmap.2(cluster.data,col=greenred(75),scale=\"none\", margins = c(5, 5),key=TRUE)\n";
	fwrite($fd4, $line);
	fwrite($fd4, "dev.off()\n");
	fflush($fd4);
	fclose($fd4);

	


	fwrite($fd2, "<html><body>");
	//echo "<table>";
	fwrite($fd2, "<table>");
	$viewx = $numelements * $squarewidth + 300;
	$viewy = $numelements * $squareheight + 300;
	fwrite($fd, "<svg preserveAspectRatio=\"xMinYMin meet\" viewBox=\"0 0 $viewx $viewy\" id=\"svgObject\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
	fwrite($fd, "<g id=\"x-axisgenes\" transform=\"translate(50,145)\" >");

	
	
	$namesarray = array();

	foreach($sigFeatureNames as $name){
		array_push($namesarray,$name);

	}

	//<rect x="105" y="10" width="20" height="10" style="fill: rgb(0,110,0 );" />

	//echo 
	fwrite($fd2,"<tr><td></td>");
		$x = 15;
		$csvnamearray = array();
		
		foreach($namesarray as $name){
			//echo 
			fwrite($fd2, "<td>$name</td>");
			array_push($csvnamearray, ",$name");
			//array_push($csvnamearray, ",");
			$xtext = "<text x=\"$x\" y=\"0\" transform=\"rotate(270, $x,0)\" style=\" font-family: arial;font-size:8pt; font-weight:bold;\">$name</text>";
			fwrite($fd, $xtext);
			$x += $squarewidth;
		}
		//array_pop($csvnamearray);
		foreach($csvnamearray as $item){
			fwrite($fd3, $item);

		}
		
		fwrite($fd3, "\n");
		fwrite($fd, "</g>");
		fflush($fd);
		$x=0;
		$y = 14;
		$transx = ($numelements * $squarewidth) +60;
		fwrite($fd, "<g id=\"y-axisgenes\" transform=\"translate($transx,145)\" >");
		foreach($namesarray as $name){
			
			
			$ytext = "<text x=\"0\" y=\"$y\" style=\" font-family: arial;font-size:8pt; font-weight:bold;\">$name</text>";
			fwrite($fd, $ytext);
			$y += $squareheight;
		}
		fflush($fd);
		fwrite($fd, "</g>");
		//echo 
		fwrite($fd2,"</tr>");
		$heatmaptext = "<g id=\"heatmap\"  cursor=\"crosshair\" transform=\"translate(50,150)\">";
		fwrite($fd, $heatmaptext);
		//fwrite($fd, "</g>");
		//fclose($fd);
		$y = 0;
	for($i = 0; $i < $numelements; $i++){
		fwrite($fd3, "$namesarray[$i]");
		//echo
		fwrite($fd2, "<tr><td>$namesarray[$i]</td>");
		$x = 0;
		for($j = 0; $j < $numelements; $j++){
			
			$value = $corarray[$i][$j];
			if($value == 0){
				$value = .0000001;
			}
			$red = 0;
			$green = 0;
			if($value < .5){
				$red = 0;
				$green = 255 - (255*(($value*100)/50));
				$green = round($green);
				
			}elseif($value > .5){
				$red = 0 + (255*(($value*100)/100));
				$red = round($red);
				$green = 0;
			}else{
				$red = 0;
				$green = 0;
			}
			$heatmaptext = "<rect x=\"$x\" y=\"$y\" width=\"20\" height=\"10\" style=\"fill: rgb($red,$green,0 );\" />";
			fwrite($fd, $heatmaptext);
			
			
			$value = round($value,4);
			fwrite($fd3, ",$value");
			fwrite($fd2,"<td style=\"background-color:rgb( $red,$green,0);padding: 2px 0.5em;white-space:nowrap;text-align: center;\">$value</td>");
			?>
			<?php
			$x += $squarewidth;
		}	
		$y += $squareheight;
		fflush($fd);
		//echo "</tr>";
		fwrite($fd2, "</tr>");
		fwrite($fd3, "\n");
		fflush($fd2);
		fflush($fd3);
	}
	fwrite($fd,"</g></svg>");
	fclose($fd);
	fwrite($fd2, "</table></body></html>");
	fclose($fd2);
	fclose($fd3);
	$command = "R CMD BATCH \"$Rfilename\"";
	//echo "$command<br>";
	$str=exec($command);
	//echo "$str";
	?>
	
<?php
}




function sdoutliertest($featarray, $sd, $num){
	// given an array of values, a sd deviation, and the number of sds to consider, returns an array (outliers) of indices that are deemed to be outliers.
	$outliers = array();
	$mean = average($featarray);
	$outlierright = $mean + ($num * $sd);
	$outlierleft = $mean - ($num * $sd);
//echo "right outlier = $outlierright<br>";
//echo "left outlier = $outlierleft<br>";
	$index = 0;
	foreach($featarray as $feature){
		//echo "$feature<br>";
		if($feature <= $outlierleft || $feature >= $outlierright){
		//	echo "outlier found<br>";
			array_push($outliers, $index);
		}
		$index++;
	}
	return $outliers;
}

function dixonQoutliertest($featarray){
	$unsortedlookuparray = $featarray;
	$outliers = array();
	$Qcrit = 0.926;
	// First, sort the array....
	if(sort($featarray)){
		$x1 = $featarray[0];
		$x2 = $featarray[1];
		$lastindex = count($featarray) - 1;
		$xnminus1 = $featarray[$lastindex - 1];
		$xn = $featarray[$lastindex];
		//echo "x1 = $x1<br>x2 = $x2<br>x<sub>n-1</sub>=$xnminus1<br>xn = $xn<br>";
		// Test low value...
		$qexplow = ($x2 - $x1)/($xn-$x1);
		//echo "Qexplow = $qexplow<br>";
		if($qexplow > $Qcrit){
				$key = array_search($x1, $unsortedlookuparray); 
			//echo "arrayindex $key is an outlier<br>";
			array_push($outliers, $key);
		}

		// Test high value....
		$qexphigh = ($xn - $xnminus1)/($xn-$x1);
		//echo "Qexphigh = $qexphigh<br>";

		if($qexphigh > $Qcrit){
			$key = array_search($xn, $unsortedlookuparray); // $key = 2;
			array_push($outliers, $key);
			//echo "arrayindex $key is an outlier<br>";
		}
	}else{
		echo "ERROR: ARRAY IS NOT NUMERIC IN dixonQoutliertest<br>";
	}

	return $outliers;
	

}

function returnQueryParams($stringToParse, $databaseField, $logicalOperator, $separator, $comparisonOperator){
/* Parameters:
	$stringToParse - the string we want to parse values out of...
	$databaseField - the field in the table we are querying...
	$logicalOperator - either AND or OR....
	$separator - the separator character utilized....
	$comparisonOperator - the type of comparison we're doing, eg. LIKE, =, <=, >=, <, >, etc...

 This function takes in a string of values that may be separated by $separator and creates a sql string consisting of the database field we're querying and, if more than one value is to be checked, the logical operator used 

*/

	$queryparams = "";
	// Need to look up cloneids for genes entered....
	$valuesPresent = 0;
	// remove whitespace and extraneous commas from front and back of string
	$stringToParse = trim($stringToParse);
	$stringToParse = rtrim($stringToParse,",");
	$stringToParse = rtrim($stringToParse,",");
	if($stringToParse != ""){
			$valuesPresent = 1;
			//echo "primaryname not null";
			$whereCheck = 1;
			// now need to go through gene name, if there are commas, need to create an array and use to
			// search across entries.
			$pos = strpos($stringToParse, "$separator");
			if ($pos === false) { // note: three equal signs
				$stringToParse = trim($stringToParse);
				// not found...don't need to create the array...
				if($comparisonOperator == "LIKE"){
					$stringToParse = "%$stringToParse%";
				}
				$queryparams = " $databaseField $comparisonOperator '$stringToParse'";
				//$primarylist = $stringToParse;
			}
			else{
				// The case where someone is searching on multiple genes...
				$queryparams = " $databaseField $comparisonOperator";
				// Create the array of primary names to search on...
				$stringToParsearray = array();
				$stringToParsearray = explode("$separator", $stringToParse);
				// go through the array and remove all blanks!!!
				$cleanStringToParsearray = array();
				foreach($stringToParsearray as $namevalue){
					$namevalue = trim($namevalue);
					if($namevalue != ""){
						array_push($cleanStringToParsearray,$namevalue);
					}
				}
				$countcheck = 0;
				$stringToParsearraynum = count($cleanStringToParsearray);
				foreach($cleanStringToParsearray as $namevalue){
					$namevalue = trim($namevalue);
					if($countcheck < $stringToParsearraynum - 1){
						if($comparisonOperator == "LIKE"){
							$namevalue = "%$namevalue%";
						}
						$queryparams .= " '$namevalue' $logicalOperator $databaseField $comparisonOperator ";
						// used to display in query parameters table...
						//$primarylist .= "'$namevalue' OR ";
					}
					else{
						if($comparisonOperator == "LIKE"){
							$queryparams .= " '%$namevalue%' ";
						}else{
							$queryparams .= " '$namevalue' ";
						}	
						// used to display in query parameters table...
						//$primarylist .= "'%$namevalue%'";
					}
					$countcheck++;
				}
			}
	}
	return($queryparams);
}




# this function is used to check for duplicate names when submitting for limma analysis.
# returns 0 if duplicate names exist and 1 if they do not.  Case insensitive....
function checkArrayDuplicates($lcArray)
{
	array_walk($lcArray, 'lcArrayVal'); //lowercase all the values first
	$uniqueArray = array_unique($lcArray);//only unique values
	$dupArray = array_diff_assoc($lcArray, $uniqueArray);//duplicated values
	
	if(count($dupArray) > 0){
		echo "Array names must be unique (case-insensitive).<br>";
		foreach ($dupArray as $value)
		{
			echo "ERROR: Duplicate Array Name: $value <br>  Please go back and change. ";
		}
		return 0;
	}else{
		return 1;
	}
	
	

}

function lcArrayVal(&$item, $key)
{
	$item = strtolower($item);
}




?>
