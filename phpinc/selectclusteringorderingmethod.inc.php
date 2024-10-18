<?php

$algo = -1;
//echo "Dataset = $dataset<br>";
//echo "Order Option is: $orderoptions\n";
if(isset($_POST)){
	if(isset($_POST['clusterAlgo'])){
		$clusteralgo = $_POST['clusterAlgo'];
	}
	if(isset($_POST['savedquery'])){
		$savedquery = $_POST['savedquery'];
	}
	if(isset($_POST['tempquery'])){
		$tempquery = $_POST['tempquery'];
	}
	if(isset($_POST['dataset'])){
		$dataset = $_POST['dataset'];
	}
	if(isset($_POST['number'])){
		$number = $_POST['number'];
	}
	if(isset($_POST['trxCluster'])){
		$trxCluster = $_POST['trxCluster'];
	}
	if(isset($_POST['trxCluster'])){
		$trxCluster = $_POST['trxCluster'];
	}
	if(isset($_POST['orderoptions'])){
		$orderoptions = $_POST['orderoptions'];
	}
	if(isset($_POST['numberGroups'])){
		$numberGroups = $_POST['numberGroups'];
	}
	if(isset($_POST['rval'])){
		$rval = $_POST['rval'];
	}
	if(isset($_POST['rvalmax'])){
		$rvalmax = $_POST['rvalmax'];
	}
	if(isset($_POST['lval'])){
		$lval = $_POST['lval'];
	}
	if(isset($_POST['lvalmin'])){
		$lvalmin = $_POST['lvalmin'];
	}
	if(isset($_POST['gprocessedsignal'])){
		$gprocessedsignal = $_POST['gprocessedsignal'];
	}
	if(isset($_POST['rprocessedsignal'])){
		$rprocessedsignal = $_POST['rprocessedsignal'];
	}
	if(isset($_POST['pValue'])){
		$pValue = $_POST['pValue'];
	}	
	if(isset($_POST['colorScheme'])){
		$colorScheme = $_POST['colorScheme'];
	}
	if(isset($_POST['outputformat'])){
		$outputformat = $_POST['outputformat'];
	}
	if(isset($_POST['includeimagemap'])){
		$includeimagemap = $_POST['includeimagemap'];
	}
	if(isset($_POST['submit'])){
		$submit = $_POST['submit'];
	}
	if(isset($_POST['number'])){
		$clusterNumber = $_POST['number'];
	}
	if(isset($_POST['orderedSubmit'])){
		$orderedSubmit = $_POST['orderedSubmit'];
	}
	if(isset($_POST['orderedIndividually'])){
		$orderedIndividually = $_POST['orderedIndividually'];
	}
	if(isset($_POST['numberOfArrays'])){
		$numberOfArrays = $_POST['numberOfArrays'];
	}
	if(isset($_POST['numberOfGroups'])){
		$numberOfGroups = $_POST['numberOfGroups'];
	}
	if(isset($_POST['querynum'])){
		$querynum = $_POST['querynum'];
	}
	if(isset($_POST['option'])){	
		$option = $_POST['option'];
	}
	if(isset($_POST['clusterAlgo'])){	
		$clusterAlgo = $_POST['clusterAlgo'];
	}
	if(isset($_POST['filter'])){
		$filter = $_POST['filter'];
	}
	if(isset($_POST['sdval'])){
		$sdval = $_POST['sdval'];
	}

}
if (isset($_POST['submit'])) {
	if(isset($_POST['clusterAlgo'])){
		$algo = $_POST['clusterAlgo'];
	}
}

$orderingMethod = -1;  // Used to determine how things are ordered when k-means or hierarchical w/ no clustering of trxs....

$clusterType = "Treatment and Gene Clustering";

// This will set the values for the the ordering options based on the clustering
// method chosen and the ordering method chosen.
if($algo > -1){
	if($algo == 0){
			$clusterType= "K-Means Clustering";
			// Need to determine what to set ordering method...
			if($orderoptions!=0){
				//
				//echo "orderoptions != 0...<br>";
				/*if($orderoptions == 1){
					// Order by individual trxs...
					echo "ordered individually...<BR>";
					$orderingMethod = 1;
				}
				else{//$orderoptions == 2...
				*/
					$orderingMethod = 2;
					//echo "ordered another way...<BR>";
				//}
			}
			else{
				$orderingMethod = 0;
			}

		}
		else{
			$clusterType=  "Hierarchical Clustering";
			if($trxCluster == 0){// If not clustering by treatments....
				if($orderoptions!=0){
					//
				//	if($orderoptions == 1){
						// Order by individual trxs...
				//		$orderingMethod = 1;
				//	}
				//	else{//$orderoptions == 2...
						$orderingMethod = 2;
					//}
				}
				else{
					$orderingMethod = 0;
				}

			}
			else{
				$orderingMethod = 0;
			}
		}
}

?>
