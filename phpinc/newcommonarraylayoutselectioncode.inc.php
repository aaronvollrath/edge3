 <!-- Tabs -->
 <ul class="nav nav-tabs" id="myTab" role="tablist">
    
	<?php

        
		// Create tabs based on organisms in database....
		$sql = "SELECT id, organism FROM agilentarrays ORDER BY id ASC LIMIT 11";
		$sqlResult = $db->Execute($sql);
        $tabCount  = 0;
        $setActiveTab = "active";
		while($row = $sqlResult->FetchRow()){
			$id = $row[0];
			$organism = $row[1];
            if($tabCount != 0){
                $setActiveTab = "";
            }
            $tabCount++;
	?>
            <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $setActiveTab; ?>" id="tab<?php echo $id;?>-tab" data-bs-toggle="tab" data-bs-target="#tab<?php echo $id;?>" type="button" role="tab" aria-controls="tab<?php echo $id;?>" aria-selected="true"><?php echo $organism;?></button>
            </li>
            
            
	<?php
		}
	?>
</ul> <!-- end of array types by organism. -->

<!-- Tab Content -->
<div class="tab-content" id="myTabContent">
	   	<?php
        
        // Re-executign the query above.  probably a better option would to 
        // Go through the original ResultSet...  keeping this for the moment
        // as this has how it has been done for a long time.
		$organismarraylist = array();
		//$organismresult = mysql_query($sql, $db);
		$sqlResult = $db->Execute($sql);
		//while($row = mysql_fetch_row($organismresult)){
        $arrayTypeCount = 0;
        //$uniqueExpNum = 1;
        $setActiveTabPane = "active";
		while($row = $sqlResult->FetchRow()){
			$arraytype = $row[0];
			$organism = $row[1];
            if($arrayTypeCount != 0){
                $setActiveTabPane = "";
            }
            $arrayTypeCount++;
            // Each array type has its own tab of associated experiments.
		?>

                <!-- Tab <?php echo $arraytype;?> -->
                <div class="tab-pane  <?php echo $setActiveTabPane; ?>" id="tab<?php echo $arraytype;?>" role="tabpanel" aria-labelledby="tab<?php echo $arraytype;?>-tab">
                        
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
            $accordionCount = 0;
                
			while($orgexprow = $orgexpresult->FetchRow()){
               
				$expid = $orgexprow[0];
                $uniqueId = $expid.'-'.$arraytype;
                if($accordionCount == 0){
                    ?>
                    <div class="accordion" id="accordionTab<?php echo $uniqueId ;?>">
                    <?php
                }
				$expdescSQL = "SELECT expname,ownerid FROM agilent_experimentsdesc WHERE expid = $expid";
				$expdescResult =  $db->Execute($expdescSQL);// mysql_query($expdescSQL, $db);
				
				$expdescVals = $expdescResult->FetchRow();//mysql_fetch_row($expdescResult);
				
				$expdescVal = $expdescVals[0];
				$ownerid = $expdescVals[1];
				
				if($ownerid != $_SESSION['priv_level']  && $_SESSION['priv_level'] != 99){
					continue;
				}
                ?>
				 
                            <div class="accordion-item">
                                <h4 class="accordion-header" id="heading<?php echo $uniqueId ;?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $uniqueId ;?>" aria-expanded="false" aria-controls="collapse<?php echo $uniqueId ;?>">
                                        <?php echo $expdescVal.' ('.$organism.')';?>
                                    </button>
                                </h4>
                                <div id="collapse<?php echo $uniqueId ;?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $uniqueId ;?>" data-bs-parent="#accordionTab<?php echo $uniqueId ;?>">
                                    <div class="accordion-body">
                                        <!--
                                      <table>
                                            <tr> -->
                <?php 
				$arraysql = "SELECT i.arrayid, i.arraydesc
							FROM agilent_arrayinfo AS i, agilent_experiments AS e
							WHERE i.arraytype = $arraytype
							AND i.arrayid = e.arrayid
							AND e.expid = $expid
							ORDER BY e.expid";  // remove the limitation for live.
				//echo "<br>$arraysql<br>";
				$arraysqlresult = $db->Execute($arraysql);//mysql_query($arraysql,$db);
				$currentarraycount = 0;
                echo '<ul class="checkbox-grid">';
				while($arrayrow = $arraysqlresult->FetchRow()){
					$arrayid = $arrayrow[0];
					$arraydesc = $arrayrow[1];
					if($currentarraycount % 5 == 0 && $currentarraycount !=0){
						//echo "</tr><tr>";
						
					}
					if(isset($_GET['savedquery'])){
						// What array needs to be checked?
						if(array_search($arrayid, $savedarrayvals) > -1){
                            $arraydesc = htmlspecialchars($arraydesc);
                            echo '<li><input type="checkbox" name="array' . $arrayid . '" value="' . $arrayid . '" checked ><label>' . $arraydesc . '</label></li>';
                            //  echo '<li><input type="checkbox" id="array' . $arrayid . '" name="array' . $arrayid . '" value="' . $arrayid . '" checked><label for="array' . $arrayid . '">' . $arraydesc . '</label></li>';

							
						}else{
                            echo '<li><input type="checkbox" name="array' . $arrayid . '" value="' . $arrayid . '"><label>' . $arraydesc . '</label></li>';
                            //echo "<input type=\"checkBox\"  name=\"array$arrayid\" value=\"$arrayid\">$arraydesc";
							//echo "<td><input type=\"checkBox\"  name=\"array$arrayid\" value=\"$arrayid\">$arraydesc</td>";
							
			
						}
					}else{
                            echo '<li><input type="checkbox" name="array' . $arrayid . '" value="' . $arrayid . '"><label>' . $arraydesc . '</label></li>';

                            //echo "<input type=\"checkBox\"  name=\"array$arrayid\" value=\"$arrayid\">$arraydesc";
							//echo "<td><input type=\"checkBox\"  name=\"array$arrayid\" value=\"$arrayid\">$arraydesc</td>";
					}
					$currentarraycount++;
					
				}
                echo '</ul>';
				?>
                <!--
                </tr>
                    </table> -->
                                        
                                    </div> <!-- end of <div class="accordion-body"> -->
                                </div> <!-- end of <div class="accordion-body"> -->
                            </div><!-- end of this accordion-item -->
                            


<?php
                //$uniqueExpNum++;
				$accordionCount++;
			}
		?>
            <?php
                if($accordionCount > 0){
            ?>
                </div><!-- end of this accordion: id="accordionTab<?php echo $arraytype;?>"> -->
            <?php
                }
            ?>
            </div> <!-- end of this tabpane: id="tab<?php echo $arraytype;?> -->


            
            <?php
		}
	?>
          			</div> <!--end of <div class="tab-content" id="myTabContent">-->