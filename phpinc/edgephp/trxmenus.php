
<?php
$chemSQL = "SELECT DISTINCT chemid, chemical FROM chem ORDER BY chemid";

		$chemResult = mysql_query($chemSQL, $db);
		$firstchoice = 1;
		$chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"0\" checked>No Change  ";
		$firstchoice++;
		while(list($chemid, $chemical) = mysql_fetch_array($chemResult))
		{
			if($firstchoice%5==0){
				$chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"$chemid\">$chemical  <br>";
				$firstchoice++;
			}
			else{
				$chemMenu .= "<input type=\"radio\" name=\"chemical\" value=\"$chemid\">$chemical  ";
				$firstchoice++;
			}
		}
		
		

		$organismSQL = "SELECT organismid, organism FROM organism ORDER BY organismid ASC";

		$organismResult = mysql_query($organismSQL, $db);
		$firstchoice = 1;
		$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"0\" checked>No Change  ";
		$firstchoice++;
		while(list($organismid, $organism) = mysql_fetch_array($organismResult))
		{
			if($firstchoice%5==0){
				$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"$organismid\">$organism  <br>";
				$firstchoice++;
			}
			else{
				$organismMenu .= "<input type=\"radio\" name=\"organism\" value=\"$organismid\">$organism  ";
				$firstchoice++;
			}
		}

		$strainSQL = "SELECT strainid, strain FROM strain ORDER BY strainid ASC";

		$strainResult = mysql_query($strainSQL, $db);
		$firstchoice = 1;
		$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"0\" checked>No Change  ";
		$firstchoice++;
		while(list($strainid,$strain) = mysql_fetch_array($strainResult))
		{
			if($firstchoice%5==0){
				$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"$strainid\">$strain  <br>";
				$firstchoice++;
			}
			else{
				$strainMenu .= "<input type=\"radio\" name=\"strain\" value=\"$strainid\">$strain  ";
			}
		}

		$genevariationSQL = "SELECT genevariationid, genevariation FROM genevariation ORDER BY genevariationid ASC";

		$genevariationResult = mysql_query($genevariationSQL, $db);
		$firstchoice = 1;
		$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"0\" checked>No Change  ";
		$firstchoice++;
		while(list($genevariationid, $genevariation) = mysql_fetch_array($genevariationResult))
		{
			if($firstchoice%5==0){
				$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"$genevariationid\">$genevariation  <br>";
				$firstchoice++;
			}
			else{
				$genevariationMenu .= "<input type=\"radio\" name=\"genevariation\" value=\"$genevariationid\">$genevariation  ";
				$firstchoice++;
			}
		}

		$tissueSQL = "SELECT tissueid, tissue FROM tissue ORDER BY tissueid ASC";

		$tissueResult = mysql_query($tissueSQL, $db);
		$firstchoice = 1;
		$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"0\" checked>No Change  ";
		$firstchoice++;
		while(list($tissueid, $tissue) = mysql_fetch_array($tissueResult))
		{
			if($firstchoice%5==0){
				$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissueid\">$tissue  <br>";
				$firstchoice++;
			}
			else{
				$tissueMenu .= "<input type=\"radio\" name=\"tissue\" value=\"$tissueid\">$tissue  ";
				$firstchoice++;
			}
		}

		$vehicleSQL = "SELECT vehicleid,vehicle FROM vehicle ORDER BY vehicleid ASC";

		$vehicleResult = mysql_query($vehicleSQL, $db);
		$firstchoice = 1;
		$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"0\" checked>No Change  ";
		$firstchoice++;
		while(list($vehicleid, $vehicle) = mysql_fetch_array($vehicleResult))
		{
			if($firstchoice%5==0){
				$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicleid\">$vehicle  <br>";
				$firstchoice++;
			}
			else{
				$vehicleMenu .= "<input type=\"radio\" name=\"vehicle\" value=\"$vehicleid\">$vehicle  ";
				$firstchoice++;
			}
		}
		
		$doseunitSQL = "SELECT doseunitid, doseunit FROM doseunit ORDER BY doseunitid ASC";

		$doseunitResult = mysql_query($doseunitSQL, $db);
		$firstchoice = 1;
		$doseunitMenu .= "<input type=\"radio\" name=\"doseunit\" value=\"0\" checked>No Change  ";
		$firstchoice++;
		while(list($doseunitid, $doseunit) = mysql_fetch_array($doseunitResult))
		{
			if($firstchoice%5==0){
				$doseunitMenu .= "<input type=\"radio\" name=\"doseunit\" value=\"$doseunitid\">$doseunit  <br>";
				$firstchoice++;
			}
			else{
				$doseunitMenu .= "<input type=\"radio\" name=\"doseunit\" value=\"$doseunitid\">$doseunit  ";
				$firstchoice++;
			}
		}


		$routeSQL = "SELECT routeid, route FROM route ORDER BY routeid ASC";

		$routeResult = mysql_query($routeSQL, $db);
		$firstchoice = 1;
		$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"0\" checked>No Change  ";
		$firstchoice++;
		while(list($routeid, $route) = mysql_fetch_array($routeResult))
		{
			if($firstchoice%5==0){
				$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"$routeid\">$route  <br>";
				$firstchoice++;
			}
			else{
				$routeMenu .= "<input type=\"radio\" name=\"route\" value=\"$routeid\">$route  ";
				$firstchoice++;
			}
		}

		$durationunitSQL = "SELECT durationunitid, durationunit FROM durationunit ORDER BY durationunitid ASC";

		$durationunitResult = mysql_query($durationunitSQL, $db);
		$firstchoice = 1;
		$durationunitMenu .= "<input type=\"radio\" name=\"durationunit\" value=\"0\" checked>No Change  ";
		$firstchoice++;

		while(list($durationunitid, $durationunit) = mysql_fetch_array($durationunitResult))
		{
			if($firstchoice%5==0){
				$durationunitMenu .= "<input type=\"radio\" name=\"durationunit\" value=\"$durationunitid\">$durationunit  <br>";
				$firstchoice++;
			}
			else{
				$durationunitMenu .= "<input type=\"radio\" name=\"durationunit\" value=\"$durationunitid\">$durationunit  ";
				$firstchoice++;
			}

		}



		$controlSQL = "SELECT controlid, control FROM control ORDER BY controlid ASC";

		$controlResult = mysql_query($controlSQL, $db);
		$firstchoice = 1;
		$controlMenu .= "<input type=\"radio\" name=\"control\" value=\"0\" checked>No Change  ";
		$firstchoice++;

		while(list($controlid, $control) = mysql_fetch_array($controlResult))
		{
			if($firstchoice%5==0){
				$controlMenu .= "<input type=\"radio\" name=\"control\" value=\"$controlid\">$control  <br>";
				$firstchoice++;
			}
			else{
				$controlMenu .= "<input type=\"radio\" name=\"control\" value=\"$controlid\">$control  ";
				$firstchoice++;
			}

		}

?>
