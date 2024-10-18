<?php
require 'edge_db_connect2.php';

?>

<?php
	include 'header.inc';
?>

<body>
	<?php
		include 'banner.inc';
	?>

 <div class="boxmiddle">
  <?php
include 'questionmenu.inc';
?>
 	<h3 class="contenthead">Brief Description</h3>
 	<div class="content">
		<p class="styletext">EDGE is a scientific resource for
		toxicology-related gene expression information. The site
		contains databases and analyses of gene expression studies
		following exposure to a variety of chemicals or
		physiological changes. The ultimate goal of the EDGE is to
		map transcriptional changes from chemical exposure that will
		someday be used as a diagnostic "fingerprint" to predict
		toxicity as well as provide valuable insights into the basic
		molecular changes responsible.
		</p>
 	</div>
	<h3 class="contenthead">Fundamental Questions</h3>
	<div class="content">
		<p class="styletext">
		<acronym title="Environment, Drugs and Gene Expression">EDGE</acronym>
 		gives you the ability to easily answer the following fundamental questions about your data
		<ol>
			<li class="question-item">
			<a href="./clustering.php">Can I compare transcriptional profiles across treatments?</a>
			</li>
			<li class="question-item">
			<a href="./question3.php">What genes respond to my treatment?</a>
			</li>
			<li class="question-item">
			<a href="./question1.php">What influences my favorite gene(s)?</a>
			</li>
			<!--
			<li class="question-item">
			<a href="./question2.php">What tissues respond to my treatment?
			</a>
			</li>
			-->


			<!--
			<li class="question-item">
			How do I best discriminate my treatment from other treatments?
			</li>
			-->
		</ol>
		</p>

	</div>
	<h3 class="contenthead">People Involved</h3>
	<div class="content">
		<p class="styletext">
			<a href="./people_behind_bradfield.php">Bradfield Laboratory</a>
		</p>
	</div>
 </div>
 <?php
	include 'leftmenu.inc';

?>


 <div class="boxclear"> </div>
 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>

</body>
</html>
