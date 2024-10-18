<?php
require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...

require './phpinc/edge3_db_connect.inc';
require 'edge_login_form_check.php';

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
<h3 class="contenthead">Login</h3>
	<div class="content">
		<p class="styletext">
			<?php
			require 'edge_login_form.php';
			?>
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
