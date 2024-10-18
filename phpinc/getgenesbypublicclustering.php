<?php

require 'edge_db_connect2.php';
require 'edge3_db_connect.inc';

echo "Select a list below.<br><br>";

if($userid != ""){
	$sql = "SELECT listid, name, listdesc, arraytype FROM genelist WHERE public='1' ORDER BY arraytype";
	$Result = mysql_query($sql,$db);

	if(mysql_num_rows($Result) == 0){
		echo "You have no saved gene lists.";
	}
	else{
		?>
		<form enctype="multipart/form-data" name="getgenes" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
		<input type="hidden" name="importedbyuserid" value="true">
		<?php
		/*
		while($row = mysql_fetch_row($sqlResult)){
		$listid = $row[0];
		$name = $row[1];
		$listdesc = $row[2];
		$arraytype = $row[3];
		$agilentarraytypeSQL = "SELECT id, organism, arraydesc, version FROM agilentarrays WHERE id = $arraytype";
			$typeResult = mysql_query($agilentarraytypeSQL,$db);
			$type = mysql_fetch_row($typeResult);
			$organism = $type[1];
			echo "$organism : <a href=\"./edge3.php?selectedclonesclusteringmodule=1&listid=$listid\">$name".": "."$listdesc</a><br>";
		}
		*/
		$organismid;

		while($row = mysql_fetch_row($Result)){
			$listid = $row[0];
			$name = $row[1];
			$listdesc = $row[2];
			$arraytype = $row[3];
			
			if($organismid != $arraytype){
				$orgSQL = "SELECT organism FROM agilentarrays WHERE id='$arraytype'";
				$orgResult = mysql_query($orgSQL,$db);
				while($orgRow = mysql_fetch_row($orgResult)){
					$organism = $orgRow[0];
					?> <u><b><?php echo $organism; ?></b></u><br> <?php
				}
				$organismid = $arraytype;
			}
			//echo "<a href=\"./edge3.php?selectedclonesclusteringmodule=1&listid=$listid\">$name".": "."$listdesc</a><br>";
			?>
			<input type="checkbox" name="<?php echo "list" . $listid; ?>" value="true"><?php echo $name . ": " . $listdesc; ?><br>
			<?php
			
		}
		
		?>
		<br>	
		<td><input type="submit" value="Submit"></td>
		<td><input type="reset" value="Reset Form"></td>
		</form>
	
		<?php
		
		}
	}
else{
	echo "Please login to use this feature.";
}

?>