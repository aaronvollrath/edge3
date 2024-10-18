<?php


/*
test to split a fe file name into its parts for querying the database.
*/

if(!function_exists('fnmatch')) {

    function fnmatch($pattern, $string) {
        return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.'))."$#i", $string);
    } // end

}

$filename = "us22502567_251486817772_s02_ge2v5_95_feb07_1_4.txt";

$filenameparts = explode("_",$filename);
$filenumber=$filenameparts[7];
echo "<h3>Filenumber=$filenumber</h3>";

# the arrays are indexed in the following format:  US22502567_251486817772

$part1 = $filenameparts[0];
$part2 = $filenameparts[1];
$directory = strtoupper($filenameparts[0]."_".$filenameparts[1]);
$dir = "/var/www/edgedata/$directory/";

echo "DIRECTORY is $dir <br>";
$selectedfile = "";
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            //echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
		if(fnmatch("*.txt",$file)){
			$filenameparts = explode("_",$file);
			$filenum=$filenameparts[7];
			if($filenumber == $filenum){
				$selectedfile = $file;
			}
		}
        }
        closedir($dh);
    }
}
if($selectedfile != ""){
	echo "<a href='./edgedata/$directory/$selectedfile'>DOWNLOAD DATA FILE</a>";
}else{
	echo "File not found.<br>";
}
?>





