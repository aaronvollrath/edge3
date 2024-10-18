<div id="navcontainer">

<ul id="globalnav">
	<li><a href="./clustering.php" title="What other treatments does my treatment act like?">Clustering</a></li>
	<li><a href="./question3.php" title="What genes respond to my treatment?">Genes?</a></li>
	<li><a href="./question1.php" title="What treatments does my favorite gene respond to?">Treatments?</a></li>
	<!--
	<li><a href="./question2.php" title="What tissues respond to my treatment?">Tissues?</a></li>
	-->
	<li><a href="./question4.php" title="How do my selected genes change in response to my selected treatments?">Genes By Treatments?</a></li>

	<li><a href="./savequeryedit.php" title="Edit you're save queries">Edit Queries</a></li>
	<li><a href="./classification.php" title="Classification">Classification</a></li>
	<?php
	if($priv_level == 99){
	?>
	<li><a href="./newsample-nonadmin.php" title="Create New Treatment">New Treatment</a></li>

	<?php
	}

	if($logged_in ==1){
	?>
	<!--
	<li><a href="./estquery.php" title="EST Query">EST Query</a></li>

	<li><a href="./query.php" title="Microarray">Microarray</a></li>
	-->
	<?php
	}
	?>

</ul>
<p>
</p>
</div>

