
<div dojoType="dijit.layout.ContentPane" style="height: 1800px;">
<?php
$arraytypestring = "agilent";
$arrayclusteringtype = 1;
$debug = 0;
if(!isset($debug)){
	$debug = 0;
}
if($userid == 1 && $debug == 1){
	analyze($_POST);
}
$browserval = 1;
if(!isset($orderedSubmit)){
	$orderedSubmit = false;
}
# strip tags from an array id list
if(isset($_POST['cloneList'])){
	#$_POST['cloneList'] = strip_tags($_POST['cloneList']);
}
if (isset($_POST['submit']) && $orderingMethod == 0 || $orderedSubmit == "true") { // if form has been submitted and it is not being ordered or orderedSubmit is true...
	//analyze($_POST);

		require('./phpinc/commonclusteringsavequerycodeaftersubmittingforclustering.inc.php');
		if($dataset == ""){
			$dataset = 1;
		}

	require('./phpinc/commonfilecreationcode.inc.php');
	if($orderedSubmit != "true"){
	include './phpinc/clusteroutputnoorderingoftreatmentsselectedclonesagilent.inc';
	}
	else{
		/***********************************************************************************
		ORDERED SECTION...............
		***********************************************************************************/
		include './phpinc/clusteroutputORDERINGoftreatmentsselectedclonesagilent.inc';
	}
}
else if(isset($_POST['submit']) && $orderingMethod >= 1) {


require("./phpinc/commonorderingscreencode.inc.php");

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
}
else{
	
	// GETTING THE VALUES TO POPULATE THE SELECTIONS.....
	// THIS ONE IS FOR THE PARTICULAR TREATMENTS....
	
	$privval = $_SESSION['priv_level'];
	
	if($privval == ""){
		$priv = 1;
	}
	else{
		$priv = $privval;
	}
	require('./phpinc/commonclusteringsavequerycode.inc');
	require('./phpinc/commonclusteringoptionscode.inc.php');
	
		#require('./phpinc/commonarraylayoutselectioncode.inc.php');
	require('./displayexperimentsbygroups.php');
		# this include uses a experiments by groups layout that 
	# doesn't use dojo.  uses bootstrap and more modern approach
	#require('./phpinc/newcommonarraylayoutselectioncode.inc.php');
?>
<table class="question" width="700px">  <!-- this is put here due to differences between the different clustering modules....-->

<?php
	require('./phpinc/commonfeaturenumberloadingcode.inc.php');
	require('./phpinc/commonedgethresholdselections.inc.php');
}

?>
</p>
</div>
 </div>
