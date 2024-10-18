<p>
	<div id="tabs" style="width: 700px; float:left">
            <ul>

	<?php
		// Create tabs based on organisms in database....
		$sql = "SELECT id, organism FROM agilentarrays ORDER BY id ASC";
		$sqlResult = $db->Execute($sql);
		//$row = $sqlResult->FetchRow();
		//$organismresult = mysql_query($sql, $db);
		//while($row = mysql_fetch_row($organismresult)){
		while($row = $sqlResult->FetchRow()){
			$id = $row[0];
			$organism = $row[1];
	?>
                	<li><a href="#fragment-<?php echo $id;?>"><span><?php echo $organism;?></span></a></li>
		<!--
                <li class="ui-tabs-nav-item"><a href="#fragment-2">Second Section</a></li>
                <li class="ui-tabs-nav-item"><a href="#fragment-3"><span>Third Section</span></a></li>
		-->
	<?php
		}
	?>
            </ul>
	   	<?php
		
		$organismarraylist = array();
		//$organismresult = mysql_query($sql, $db);
		$sqlResult = $db->Execute($sql);
		//while($row = mysql_fetch_row($organismresult)){
		while($row = $sqlResult->FetchRow()){
			$arraytype = $row[0];
			$organism = $row[1];
		?>
			<div id="fragment-<?php echo $arraytype;?>">
		<?php
			// What arrays/experiments are associated w/ this organism???
			//$orgexpsql= "select e.expid, i.arraytype, i.arrayid, i.arraydesc from agilent_arrayinfo as i, agilent_experiments as e where i.arraytype = $id and i.arrayid = e.arrayid ORDER BY e.expid";
			$orgexpsql= "SELECT DISTINCT e.expid
					FROM agilent_arrayinfo AS i, agilent_experiments AS e
					WHERE i.arraytype = $arraytype
					AND i.arrayid = e.arrayid
					ORDER BY e.expid";
			$orgexpresult = $db->Execute($orgexpsql);//mysql_query($orgexpsql, $db);
			
			//while($orgexprow = mysql_fetch_row($orgexpresult)){
			while($orgexprow = $orgexpresult->FetchRow()){
				$expid = $orgexprow[0];
				$expdescSQL = "SELECT expname,ownerid FROM agilent_experimentsdesc WHERE expid = $expid";
				$expdescResult =  $db->Execute($expdescSQL);// mysql_query($expdescSQL, $db);
				
				$expdescVals = $expdescResult->FetchRow();//mysql_fetch_row($expdescResult);
				
				$expdescVal = $expdescVals[0];
				$ownerid = $expdescVals[1];
				
				if($ownerid != $_SESSION['priv_level']  && $_SESSION['priv_level'] != 99){
					continue;
				}
				echo "<tr><td colspan='3'><div dojoType='dijit.TitlePane' title='$expdescVal' open='false'><table><tr>";
				$arraysql = "SELECT i.arrayid, i.arraydesc
							FROM agilent_arrayinfo AS i, agilent_experiments AS e
							WHERE i.arraytype = $arraytype
							AND i.arrayid = e.arrayid
							AND e.expid = $expid
							ORDER BY e.expid";
				//echo "<br>$arraysql<br>";
				$arraysqlresult = $db->Execute($arraysql);//mysql_query($arraysql,$db);
				$currentarraycount = 0;
				while($arrayrow = $arraysqlresult->FetchRow()){
					$arrayid = $arrayrow[0];
					$arraydesc = $arrayrow[1];
					if($currentarraycount % 5 == 0){
						echo "</tr>";
						
					}
					if(isset($_GET['savedquery'])){
						// What array needs to be checked?
						if(array_search($arrayid, $savedarrayvals) > -1){
							echo "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\" checked>$arraydesc&nbsp&nbsp&nbsp&nbsp</option></td>";
							
						}else{
							echo "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\">$arraydesc&nbsp&nbsp&nbsp&nbsp</td>";
							
			
						}
					}else{
							echo "<td><input type=\"checkBox\" dojoType=\"dijit.form.CheckBox\"    name=\"array$arrayid\" value=\"$arrayid\">$arraydesc&nbsp&nbsp&nbsp&nbsp</td>";
					}
					$currentarraycount++;
					
				}
				echo "</tr></table></div></td></tr>";
				
			}
		?>
			
                		
            		</div>

	
            <?php
		}
	?>
          
        </div>

</p>
