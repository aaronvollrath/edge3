<?php
session_start();
require 'edge_db_connect2.php';
$_SESSION['userid'] = 1;


function utime (){
    $time = explode( " ", microtime());
    $usec = (double)$time[0];
    $sec = (double)$time[1];
    return $sec + $usec;
    }
    $start = utime();
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Layout</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .checkbox-grid {
            display: flex;
            flex-wrap: wrap;
            list-style-type: none;
        }

        .checkbox-grid li {
            flex: 0 0 25%;
        }
    </style>
</head>
<body>
<!--
				<ul>
					<li> <a href="edge3.php?diffexprmodule=1"><i>limma</i> Differential Expression</a></li>
				<li> <a href="edge3.php?clusteringmodule=1">Standard Clustering</a></li>
				<li> <a href="edge3.php?selectedclonesclusteringmodule=1">Selected Clone Clustering</a></li>
				<li> <a href="edge3.php?orderedheatmapmodule=1">Ordered List</a></li>
				<li> <a href="edge3.php?agilentquestion1=1">Feature Query Module</a></li>

				<li> <a href="edge3.php?knearestmodule=1">k-Nearest Neighbors</a></li>
				<li> <a href="edge3.php?nbclassificationmodule=1">Naive Bayes Classification</a></li>

    -->



<div class="container-fluid">
        <div class="row">
            <!-- Left Hand Panel -->
            <strong>Data Analysis</strong>
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="edge3.php?diffexprmodule=1"><i>limma</i> Differential Expression</a>
                            <!--
                            <a class="nav-link active" aria-current="page" href="#">Dashboard</a>
                            -->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="edge3.php?clusteringmodule=1">Standard Clustering</a>
                            <!--
                            <a class="nav-link" href="#">Menu Item 1</a>
                            -->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="edge3.php?selectedclonesclusteringmodule=1">Selected Clone Clustering</a>
                            <!--
                            <a class="nav-link" href="#">Menu Item 2</a>
    -->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="edge3.php?orderedheatmapmodule=1">Ordered List</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="edge3.php?agilentquestion1=1">Feature Query Module</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="edge3.php?knearestmodule=1">k-Nearest Neighbors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="edge3.php?knearestmodule=1">k-Nearest Neighbors</a>
                        </li>

                    </ul>
                </div>
            </nav>
 <!-- Main Panel -->
 <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
                <h2>Main Panel</h2>

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
                                <h2 class="accordion-header" id="heading<?php echo $uniqueId ;?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $uniqueId ;?>" aria-expanded="false" aria-controls="collapse<?php echo $uniqueId ;?>">
                                        <?php echo $expdescVal.' ('.$organism.')';?>
                                    </button>
                                </h2>
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
                            echo '<li><input type="checkbox" id= name="array' . $arrayid . '" value="' . $arrayid . '" checked ><label>' . $arraydesc . '</label></li>';
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
                		
                        </main>
        </div> <!-- end of row div -->

        </div> <!-- end of <div class="container-fluid">-->

   <!-- Bootstrap JS and dependencies -->
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>