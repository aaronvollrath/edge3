<?php

if (!isset($_SESSION['userid'])) {
	echo "<h2>Please log-in to use this feature of EDGE<sup>3</sup></h2>";


?>
			<form name="login" method="post"  action="<?php  $_SERVER['PHP_SELF'] ?>">
<p>Username:
<input name="uname" type="text">
Password:
<input name="passwd" type="password">
<input type="submit" name="Login" value="Login" >
</p>
</form>

<br><br><a href="./edge3.php?register=1">Need to register?</a>

	<br><a href="./edge3.php?pass=1">Forgot your password?</a>
	<br><a href="./edge3.php?username=1">Forgot your username?</a>
	<br><a href="./edge3.php?userandpass=1">Forgot your username and password?</a>

<?php
}else{

	if($_SERVER['PHP_SELF'] == $relativePath."agilentexperiment-useradmin.php"){
	?>

	<br><br><br>


		<h2>Welcome, <font color="red"><i><?php echo $_SESSION['firstname']." ".$_SESSION['lastname']; ?></i></font>, to the EDGE<sup>3</sup> User Submission Area!</h2>
		
	<br><br>
		Using this application will allow you to create a <b>"New Experiment"</b> and to submit <b>"New Array"</b> submissions.
	<br><br>
		This <b>"Welcome"</b> tab will be an area where any news/messages will be posted to keep you informed as to what is going on with EDGE<sup>3</sup>.  We hope that this application will be an easy and straight-forward way to create and manage your experiments within EDGE<sup>3</sup>.

	<br><br>If you've any experiments within EDGE<sup>3</sup>, they will be listed to the left in under <b>"Your Experiments"</b>.

	<br><br>If you've any arrays entered into the queue, they will be listed to the left in under <b>"Your Array Queue"</b>.

	<form name="logout" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>">
	<input name="logout" type="submit" value="Log out">
	</form>
	<br><br>
	<div dojoType='dijit.TitlePane' title='<font color="red" style="font-weight: bold;">Important Information and Instructions</font>' open='false' width="800">
	<a href="http://docs.google.com/Doc?id=dchbhm7f_578fgmk62xk" target="_blank"><h3>Instructions for EDGE<sup>3</sup> Administration</h3></a>
	<a href="http://docs.google.com/Doc?id=dchbhm7f_670g42m7rc8" target="_blank"><h3>Instructions for downloading your data files.</h3></a>
	<a href="http://docs.google.com/Doc?id=dchbhm7f_654d5bsdqdg" target="_blank"><h3>Instructions for changing an array name after it has been added to an experiment.</h3></a>
	<a href="http://docs.google.com/Doc?id=dchbhm7f_681hprp4jcc" target="_blank"><h3>Accessing array images, array data files and array QC reports
</h3></a>

	<a href="../images/edgearrayflowchart.svg" target="_blank"><h3>Click for Flowchart of Microarrays on EDGE<sup>3</sup></h3></a>


	</div>
	<div dojoType='dijit.TitlePane' title='<font color="red" style="font-weight: bold;">Frequently Asked Questions</font>' open='false' width="800">
		<p>
			Link to <a href='http://docs.google.com/Doc?id=dchbhm7f_708g94g7mc3' target='_blank'>EDGE<sup>3</sup> FAQ</a>
		</p>
	</div>

	<!--
	<embed src="<?php //echo "../images/edgearrayflowchart.svg" ?>" width='800' height='800' type="image/svg+xml" />
	-->

	<?php
	}else{ //if($_SERVER['PHP_SELF'] == $relativePath."edge3.php"){
	?>

	<br><br><br>
	<h2>Welcome, <font color="red" style="font-weight: bold;"><i><?php echo $_SESSION['firstname']." ".$_SESSION['lastname']; ?></i></font>, to the EDGE<sup>3</sup> Microarray Data Analysis Area!</h2><br>
	<table width="800">
<tr>
<td>
<div dojoType='dijit.TitlePane' title='<font color="red" style="font-weight: bold;">Instructions/Frequently Asked Questions</font>' open='false' width="800">
<p>
You can access instructions by clicking on the <b>Useful Information/Instructions Section</b> located in the lower left</p>
<a>Link to <a href='http://docs.google.com/Doc?id=dchbhm7f_708g94g7mc3' target='_blank'>EDGE<sup>3</sup> FAQ</a></p>
<br><br>
<b>Flash-based instructions:</b>
<ul>
	<li><a href="./Instructions/basicstandardclustering/basicstandardclustering.htm" target="_blank">Basic Clustering Example</a></li>
	<li><a href="./Instructions/basicstandardclustering-savedqueryandmodification/basicstandardclustering-savedqueryandmodification.htm"  target="_blank">Clustering and modifying a saved query</a></li>
	<li><a href="./Instructions/selectedclustering/selectedclustering.htm" target="_blank">Selected Clustering Example</a></li>
	<li><a href="./Instructions/orderedlist/orderedclusteringexample.htm" target="_blank">Ordered List Example</a></li>
	<li><a href="./Instructions/limmaexample/LimmaInstructions.htm" target="_blank">Limma Example</a></li>
</ul>
</div>
</td>
</tr>
</table>
	<form name="logout" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>">
	<input name="logout" type="submit" value="Log out">
	<br><br><p style="width:400px;">This Welcome page serves as an area to login/logout as well as a location for news and information regarding Edge<sup>3</sup>.<br><br>Please refer to the section "Useful Information" located in the lower left-hand corner of your browser.  By clicking on the caret, you can expand that section to view information on some of the aspects of the user interfaces in Edge<sup>3</sup>.</p>

	<p style="width:400px;">EDGE<sup>3</sup> is currently best viewed with the Firefox browser.  Click on the image below to download a version appropriate for your OS platform.<br><a href="http://www.mozilla.com/en-US/firefox/" target="_blank"><img src="./images/firefox-title.jpg"/></a></p>

	</form>
	<?php
	}
}
?>


