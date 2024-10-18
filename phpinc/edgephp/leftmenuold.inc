<div id="leftmenu">
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
<div><b><font class="welcome">Welcome <br>
<?php echo $_SESSION['firstname'] ?>
<?php echo $_SESSION['lastname'] ?>!</b></font></div>
<ul id="navlist">
	<li><a href="./login.php" title="Login">Logout/Update</a></li>

<?php
}
?>

	<li><a href="./edge.php" title="The main EDGE page">Home</a></li>
	<li><a href="./about_edge.php" title="The basis for EDGE">About EDGE</a></li>
	<li><a href="./contact_info.php" title="Who to contact about EDGE">Contact Information</a></li>
	<li><a href="./flow_chart.php" title="How things work...">Project Flow Chart</a></li>
	<li><a href="./publications.php" title="Published Papers">Publications Related to EDGE</a></li>
	<li><a href="./blast/blast.html" title="BLAST">EDGE BLAST</a></li>
	<li><a href="./protocols.php" title="Protocols used for generating these data">Protocols</a></li>
	<li><a href="http://mcardle.oncology.wisc.edu/bradfield" title="Bradfield Lab Homepage">Bradfield Laboratory</a></li>



<?php
	$priv = $_SESSION['priv_level'];
	if($priv == 99){
?>
	<li><a href="./admin/users.php" title="">Admin Stuff</a></li>
	<li><a href="estquerysvg.php" title="This is pre-production">SVG EST Query</a></li>

<?php
	}
?>
</ul>

</div>
