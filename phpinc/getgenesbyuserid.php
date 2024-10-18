<?php


require 'edge_db_connect2.php';
require 'edge3_db_connect.inc';
?>


<?php
echo "Select one or more lists of the same organism below and click <i><b>Load Gene List(s)</b></i> to import the list.  <font color='red'><b>Note:</b></font> Combining lists from different organisms will give unexpected results due to differences in feature numbers.<br><br>";
echo "To save the combination of lists as one new list, give a name, indicate if it is to be public, and click <i><b>Save Gene List(s)</b></i> button at the bottom.  <br><br>";

if($userid != ""){
	$sql = "SELECT listid, name, listdesc, arraytype FROM genelist WHERE userid='$userid' ORDER BY arraytype";
	$Result = mysql_query($sql,$db);

	if(mysql_num_rows($Result) == 0){
		echo "You have no saved gene lists.";
	}
	else{
		?>
<!--
<form enctype="multipart/form-data" name="getgenes" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post">
-->
		<form enctype="multipart/form-data" name="getgenes" action="" method="">
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
?>
		
		<table>
<?php
		$index = 0;
		while($row = mysql_fetch_row($Result)){
			$listid = $row[0];
			$name = $row[1];
			$listdesc = $row[2];
			$arraytype = $row[3];
			
			if($organismid != $arraytype){
				$orgSQL = "SELECT organism FROM agilentarrays WHERE id='$arraytype'";
				$orgResult = mysql_query($orgSQL,$db);
				while($orgRow = mysql_fetch_row($orgResult)){
					$organism = $orgRow[0];?>					
					<thead>
					<tr>
					<th colspan="3"><?php echo $organism; ?></th></tr></thead>
<tr><TD><strong>Include?</strong></TD><td><strong>List Name</strong></td><td><strong>List Description</strong></td></tr>
 <?php
				}
				$organismid = $arraytype;
				
			}
			//echo "<a href=\"./edge3.php?selectedclonesclusteringmodule=1&listid=$listid\">$name".": "."$listdesc</a><br>";
					$csstdclass="d0";
					if($index%2==0){
						$csstdclass="d1";
					}
?>
					<TR><TD class="<?php echo "$csstdclass"."center"; ?>"><input type="checkbox" name="<?php echo "list" . $listid; ?>" value="<?php echo $listid; ?>"></td><td class="<?php echo "$csstdclass"."center"; ?>"><font color='blue'><b><?php echo $name; ?></b></font></TD><td colspan='2' width="500" class="<?php echo "$csstdclass"."left"; ?>"><i><?php echo $listdesc; ?></i></td></tr>
			<?php
			$index++;
		}
		?>	
		
		<!--
		<td><input type="submit" name="importgenelist" value="Submit"></td>
		-->
		
		
		<tr><td colspan='2'><input type="button" name="importgenelist" value="Load Gene List(s)" onclick="return updatefeaturenums();"></td>
		<td><input type="reset" value="Reset Form"></td><td></td></tr>
		</table>

		
		<br><br>
		<table>
		<tr>
		<td colspan='2'>List Name</td>
		<td>
		<input name="newlistname" type="text">
		</td><td></td></tr>
		<tr>
		<td colspan='2'>List Description</td>
		<td>
		<input name="newlistdesc" type="text">
		</td><td></td></tr>
		<tr>
		<td colspan='2'>Public</td>
		<td>
		<input name="listispublic" type="radio" value="1">
		</td><td colspan='2'></td></tr>
		
		<tr>
		<td colspan='2'><input type="button" name="importgenelist" value="Save Gene List(s)" onclick="return concatenateandsavelists();"></td>
		
		<td><input type="reset" value="Reset Form"></td>
		<td></td></tr>
		
		
		</table>
		</form>
<!--
		</form>
-->
		<?php
		
		}
	}
else{
	echo "Please login to use this feature.";
}

?>