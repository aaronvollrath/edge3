<div id="left">
<ul id="navlist">
<?php

if(strcmp($_SESSION['username'], "") == 0){
	// Display Login.....
?>
<ul id="navlist">
	<li><a href="./login.php" title="Login">Login</a></li>
<?php

}
else{ // Display logout...
?>
<div><p class="welcome">Welcome <br>
<?php
$name = $_SESSION['firstname']." ".$_SESSION['lastname'];
	echo "$name";
?>!</p></div>
<ul id="navlist">
	<li><a href="./login.php" title="Login">Login/Logout</a></li>

<?php
}
?>

	<li><a href="./edge.php" title="The main EDGE page">Home</a></li>
	<li><a href="./about_edge.php" title="The basis for EDGE">About EDGE</a></li>
	<li><a href="./contact_info.php" title="Who to contact about EDGE">Contact Information</a></li>
	<li><a href="./rnasubmission.php" title="How to submit your RNA...">RNA Submission</a></li>
	<li><a href="./flow_chart.php" title="How things work...">Project Flow Chart</a></li>
	<li><a href="./publications.php" title="Published Papers">Publications Related to EDGE</a></li>
	<?php
	$priv = $_SESSION['priv_level'];
	if($priv == 99){

	?>
	<li><a href="../blast/blast.html" title="BLAST" target="_blank">EDGE BLAST</a></li>
	<?php
	}
	?>
	<li><a href="./protocols.php" title="Protocols used for generating these data">Protocols</a></li>
	<li><a href="http://mcardle.oncology.wisc.edu/bradfield" title="Bradfield Lab Homepage" target="_blank">Bradfield Laboratory</a></li>



<?php
	$priv = $_SESSION['priv_level'];
	if($userid == 1 || $userid == 9 || $userid == 8 || $userid == 134 || $userid == 145 || $userid == 189  || $userid == 149 || $userid == 55){
?>
	<li><a href="./admin/users.php" title="">Admin Stuff</a></li>
	<li><a href="estquerysvg.php" title="This is pre-production">SVG EST Query</a></li>

<?php
	}
	if($userid == 1 || $userid == 9 || $userid == 8 || $userid == 32 || $userid == 36){
?>
	<li><a href="./data.php" title="">Adam's Data</a></li>
<?php
}
?>

</ul>
<p>
<font color="red">Note:</font><br><font style="font-size: 6pt;">Best viewed at 1024x768 resolution or greater</font><br><br>
<font color="red">Mac Users:</font><br>
<font  style="font-size: 6pt;">EDGE is best viewed with:</font>
<a href="http://www.apple.com/safari/download/" target="_blank"><img src="./GIFs/safari.gif" width="50" height="50" align="left" alt="Download Safari Browser" border="0"></img></a>
<br clear="all">
<font  style="font-size: 7pt;">Click to download!</font>
</p>
</div>
