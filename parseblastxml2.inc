<?php

function createTable2($file){

	$file = "./IMAGES/$file";


	echo "<table id=\"results\">";
	echo "<tr><th>Hit #</th><th>ID</th><th>Name</th><th>Accession</th><th>Length</th><th>Bit Score</th><th>E-value</th></tr>";


	function startElement2($parser, $name, $attrs)
	{
	global $depth, $current_tag, $dataout, $hspnum;
		$current_tag = $name;
  		if($name == "HIT_NUM"){
			echo "<tr><td>";
		}
		if($name == "HIT_ID"){
			//echo "<tr>";
			echo "<td>";
		}
		if($name == "HIT_DEF"){
			echo "<td>";
		}
		if($name == "HIT_ACCESSION"){
			echo "<td>";
		}
		if($name == "HIT_LEN"){
			echo "<td>";
		}
		if($name=="HSP_BIT-SCORE"){
		if($hspnum == 1){
					echo "<td>";
				}

		}
		if($name=="HSP_EVALUE"){
			if($hspnum == 1){
					echo "<td>";
				}
		}


	$depth[$parser]++;
	}

	function endElement2($parser, $name)
	{
		global $depth,$current_tag, $dataout, $hspnum;
		global $scale;
		////echo "/$name<br>";
		if($name == "HIT_NUM"){
			echo "</td>";
			//echo "</tr>";
		}
		if($name == "HIT_ID"){
			//echo "<tr>";
			echo "</td>";
		}
		if($name == "HIT_DEF"){
			echo "</td>";
		}
		if($name == "HIT_ACCESSION"){
			echo "</td>";
		}
		if($name == "HIT_LEN"){
			echo "</td>";
			$dataout = "";
		}
		if($name=="HSP_BIT-SCORE"){
			if($hspnum == 1){
					echo "</td>";
				}
		}
		if($name=="HSP_EVALUE"){
			if($hspnum == 1){
					echo "</td></tr>";
				}
		}


		$depth[$parser]--;
	}


	function characterData2($parser, $data) {
		global $name, $current_tag,$dataout, $hspnum;
		if($current_tag == "HIT_NUM"){
			$data = trim($data);
			if($data != ""){
				//echo "<td>$data</td>";
				//$dataout .= "$data";
				echo "$data";
			}
		}
		if($current_tag == "HIT_ID"){
				$data = trim($data);
			if($data != ""){
				$idarray = explode("|", $data);

				//echo "<td>$data</td>";
				echo "<a href=\"http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=Nucleotide&list_uids=$idarray[1]&dopt=GenBank\" target=\"_blank\">$data</a>";
			}
		}
		if($current_tag == "HIT_DEF"){
				$data = trim($data);
			if($data != ""){
				//echo "<td>$data</td>";
				echo "$data";
			}
		}
		if($current_tag == "HIT_ACCESSION"){
			$data = trim($data);
			if($data != ""){
				//echo "<td>$data</td>";
				echo "$data";
			}
		}
		if($current_tag == "HIT_LEN"){
			$data = trim($data);
			if($data != ""){
				//echo "<td>$data</td>";
				echo "$data";
			}
		}
		if($current_tag == "HSP_NUM"){
			$data = trim($data);
			//echo "xxxx<br>";
			if($data != ""){
				if($data != 1){
					$hspnum = 2;
					//echo "$data<br>";
				}
				else{
					$hspnum = 1;
				}
			}
		}
		if($current_tag == "HSP_BIT-SCORE"){
			$data = trim($data);
			if($data != ""){
				//echo "<td>$data</td>";
				if($hspnum == 1){
					echo "$data";
				}
				//echo "$data";
			}
		}
		if($current_tag == "HSP_EVALUE"){
			$data = trim($data);
			if($data != ""){
				//echo "<td>$data</td>";
				if($hspnum == 1){
					echo "$data";
				}
				//echo "$data";
			}
		}
	}





	if (!($fp = fopen($file, "r"))) {
	die("could not open XML input");
	}

	while ($data = fread($fp, 4096)) {
	if (!xml_parse($xml_parser, $data, feof($fp))) {
	die(sprintf("XML error: %s at line %d",
			xml_error_string(xml_get_error_code($xml_parser)),
			xml_get_current_line_number($xml_parser)));
	}
	}
	

}
?>
