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
	<h3 class="contenthead">Project Flow Chart</h3>
<div class="flowchart">
<p class="styletext" align="center">Treat mice with chemical</p>
<p align="center"><img src="./GIFs/roundbluedown.gif" alt="->" width="25" height="25" align="bottom"></img></p>
<p class="styletext" align="center">Identify and count clones by hybridization<br>
         with probes from most common cDNAs.<br>
         Remove clones using robotics.
</p>
<p align="center"><img src="./GIFs/roundbluedown.gif" alt="->" width="25" height="25" align="bottom"></img></p>
<p class="styletext" align="center">
	Begin high throughput sequencing<BR>
 	of unidentified clones.
</p>
<p align="center"><img src="./GIFs/roundbluedown.gif" alt="->" width="25" height="25" align="bottom"></img></p>
<p class="styletext" align="center">
	Determine EST frequency by counting clones<BR>
 	identified by sequencing and hybridization.
</p>
<p align="center"><img src="./GIFs/roundbluedown.gif" alt="->" width="25" height="25" align="bottom"></img></p>
<p class="styletext" align="center">
	Make cDNA microarrays using unique clones.
</p>
<p class="styletext" align="center">
	Search for gene expression patterns.<BR>
 	Test models of toxicity.
</p>
<p class="styletext" align="center">
	Apply custom microarrays to<BR>
	additional chemical exposures.
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











