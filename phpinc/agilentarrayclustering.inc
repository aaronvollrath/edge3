<div dojoType="dijit.layout.ContentPane" style="height: 1800px;">

<?php
//analyze($_SESSION);
//analyze($_POST);
$userid = $_SESSION['userid'];
//if($userid == ""){
////	$userid = $_SESSION['userid'];
//}
$debug = -1;  // set to 1 for debug messages as long as the userid matches debugid....
$debugid = 1;
if($userid == $debugid && $debug == 1){
echo "debug mode: <br>";
analyze($_SESSION);
analyze($_POST);
}
$browserval = 1;
if(!isset($_POST['orderedSubmit'])){
#if(!isset($orderedSubmit)){
	$orderedSubmit = false;
}else{
	$orderedSubmit = $_POST['orderedSubmit'];
}
#echo "ordered submit is $orderedSubmit<br>";
if (isset($_POST['submit']) && $orderingMethod == 0 || $orderedSubmit == "true") { // if form has been submitted and it is not being ordered or orderedSubmit is true...

			
		require('./phpinc/commonclusteringsavequerycodeaftersubmittingforclustering.inc.php');


		if($dataset == ""){
			$dataset = 1;
		}
		//echo "tempquery # : $tempquery<br>";
		//echo "Dataset is $dataset<br>";
		/*
		print "Posted variables: <br>";
		reset ($_POST);
		while(list($key, $val) = each ($_POST)){
			if($key == "option"){
				$optionarray = $_POST['option'];
				foreach($optionarray as $key=>$value) {
					echo "<li>[" . $key . "] " . $value;
				}
				echo "<br>";
				//analyze($_POST['option']);
			}else{
				print $key . " = " . $val . "<br>";
			}
		}*/


		require('./phpinc/commonfilecreationcode.inc.php');
	if($orderedSubmit != "true"){
		#if($userid != 1){
			include './phpinc/clusteroutputnoorderingoftreatmentsedge3.inc';
		#}else{
		#	include './phpinc/clusteroutputnoorderingoftreatmentsedge3.inc2.php';
		#}
	}
	else{
		/***********************************************************************************
		ORDERED SECTION...............
		***********************************************************************************/
		#echo "ordered?????????????????????????????????????????????????????<hr>";
		include './phpinc/clusteroutputORDERINGoftreatmentsedge3.inc';
	}
}
else if(isset($_POST['submit']) && $orderingMethod >= 1) {

require("./phpinc/commonorderingscreencode.inc.php");

}
else{// GETTING THE VALUES TO POPULATE THE SELECTIONS.....
// THIS ONE IS FOR THE PARTICULAR TREATMENTS....

$privval = $_SESSION['priv_level'];

if($privval == ""){
	$priv = 1;
}
else{
	$priv = $privval;
}


//analyze($_SESSION);
	require('./phpinc/commonclusteringsavequerycode.inc');


// This will display the appropriate 
	require('./phpinc/commonclusteringoptionscode.inc.php');

	# this include uses a experiments by groups layout that 
	# doesn't use dojo.  uses bootstrap and more modern approach
	//require('./phpinc/newcommonarraylayoutselectioncode.inc.php');

	require('./displayexperimentsbygroups.php');

?>
<table class="question" width="700px">  <!-- this is put here due to differences between the different clustering modules....-->
<?php
		require('./phpinc/commonedgethresholdselections.inc.php');
		


}


?>
</p>
</div>
