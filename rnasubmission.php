<?php
require 'edge_db_connect2.php';

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
<h3 class="contenthead">RNA Submission</h3>

<p class="styletext">
<h4>Submission of total RNA for Bradfield Lab Microarray</h4>
<ol>

<li>RNA must be greater than <font color="red"><b>.600ug/ul</b></font> in concentration and the total amount of RNA should be greater than <font color="red"><b>60ug/sample</b></font> for each comparison.  Control RNA that is to be used for multiple comparisons, please provide enough control RNA for each comparison (e.g. three dosed individuals against one control; provide 180ug of control) </li>

<ul><li>If unable to generate<font color="red"><b> 60ug</b></font>, please contact to run samples</li></ul>
<li>Each tube should be labeled with lab name, sample name, and RNA
 concentration.</li>
<li>All packages should include a description sheet of each sample along with
proof of RNA quality, either in a gel image or<font color="red"><b> 260/280</b></font> ratio. Please
include a primary contact name on the description sheet, along with							
phone number and address.</li>
<li>Fill out an online submission form for <font color="red"><b>EACH</b></font> (distinct) RNA sample--><a href="./newsample-nonadmin.php"><em><b>Online Form</b></em></a></li>
</ol>

<li><font color="red"><em><b>IMPORTANT:</b></em></font> Microarray comparisons are made between untreated, control animals and animals treated with <font color="red"><em><b>ONE</b></em></font> treatment.  Please make sure the RNA submitted adheres to this experimental design.  If you have any questions, please <em><a href="./contact_info.php"><b>contact us</b></a></em>.

<!--
 <blockquote><ul>
	 <li><a href="./protocols/submissionguidelines.xls">
	 	<font face="Arial">RNA Submission Guidelines and Survey (MS Excel File)</font>
		</a><BR></li>
	 <li><a href="./protocols/submissiontotalrna.doc" target="_blank"><font face="Arial">Submission of total RNA for Bradfield Lab Microarray (MS Word File)</font></a><br>

	 </ul>
         </blockquote>
-->


</p>




 </div>
 <?php
	include 'leftmenu.inc';
?>
 <div class="boxclear"> </div>
 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>
</body>
</html>



