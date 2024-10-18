<?php

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

		$datafile = "EDGEVerbose.seq";
		$outputfile1 = "v5_3primeedgeseqs.csv";
		$command = "touch $outputfile1";
		$str=exec($command);

		$outputfile2 = "v6_3primeedgeseqs.csv";
		$command = "touch $outputfile2";
		$str=exec($command);

		$outputfile3 = "v6_5primeedgeseqs.csv";
		$command = "touch $outputfile3";
		$str=exec($command);

		$fd = fopen($datafile, 'r');
		$fd_v5_3prime = fopen($outputfile1, 'w');
		$fd_v6_3prime = fopen($outputfile2, 'w');
		$fd_v6_5prime = fopen($outputfile3, 'w');
	$linecount = 0;
	$clonecount = 0;
	while (!feof($handle)) {
	//while($linecount < 1000){
		$buffer = fgets($fd, 4096);
		// check to see if this is a new clone...
		if(substr($buffer, 0,1) == ">"){
			//get the clone #,
			$data = explode("_", $buffer);
			//analyze($data);
			$version = substr($data[0],2);
			$direction = $data[1];
			$clone = $data[2];
			/*$clone = array_pop($data);
			$direction = array_pop($data);
			$version = substr(array_pop($data),0,1);
			*/
			$clone = trim($clone);
			$clone = "\n$clone,";

			echo "$version $direction $clone<br>";

			if($linecount == 0){
				$buffer = trim($clone);
			}
			else{
				$buffer = $clone;
			}

			$clonecount++;
		}else{
			$buffer = trim($buffer);
		}
		//echo $buffer;
		if($version == 5){
			fwrite($fd_v5_3prime, $buffer);
		}elseif($version == 6 && $direction == 3){
			fwrite($fd_v6_3prime, $buffer);
		}
		else{ //v6_5prime
			fwrite($fd_v6_5prime, $buffer);
		}
		$linecount++;
	}
	//fclose($fd);

	echo "Clones = $clonecount";
	echo "Lines = $linecount";
		//fwrite($fd, $svgheader);

?>