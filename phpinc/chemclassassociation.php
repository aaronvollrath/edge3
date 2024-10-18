<?php

/***
Location: /edge2/admin
Description:   .
POST:
	FORM NAME: "useractivity" ACTION: "useractivity.php" METHOD: "post" ONSUBMIT: ""
	FUNCTION: Used to select the treatment to edit.
	ITEMS:  'submit', 'user[idnumber]' <checkbox>, 'numrows'
GET:
Files include or required: 'edge_db_connect2.php','edge_update_user_activity.inc', 'adminmenu.inc','../adminleftmenu.inc'
***/



require 'edge_db_connect2.php';

// Need to check if the user is logged in because this is a restricted area...
if ($logged_in == 0) {
	die('Sorry you are not logged in, this area is restricted to registered members. <a href="../login.php">Click here to go to the login page</a>');
}

$db = mysql_connect("localhost", "root", "arod678cbc3");

mysql_select_db("edge", $db);

include 'edge_update_user_activity.inc';
function analyze(&$array) {
   foreach($array as $key=>$value) {
       if(is_array($value)) {
           echo "<li>Array:<blockquote>";
             analyze($value);
           echo "</blockquote>";
         } elseif(is_object($value)) {
           echo "<li>Object:<blockquote>";
             analyze($value);
           echo "</blockquote>";
         } else {
             echo "<li>[" . $key . "] " . $value;
       }
   }
}
?>

<?php
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="../css/newlayout.css" title="layout" />
<title>EDGE^2</title>
</head>
<script src="../sorttable.js"></script>

<body>

	

 <div class="boxmiddle">
  <div style="display: static">
 
</div>

	<h3 class="contenthead">Chemical-ChemicalClass Association</h3>
<?php
$priv_level = $_SESSION['priv_level'];

//echo "your priv_level = $priv_level<br>";

?>

<form name="chemclassassociation" action="chemclassassociation.php" method="get">
<table id="results"  class="sortable">

  <col width=10 align="center">
  <col width=10 align="center">
  <col width=128 align="left">
  <col width=128 align="left">
  <col width=128 align="left">
  <col width=256 align="left">
  <col width=256 align="left">
  <thead>
    <tr>
      <!--<th scope=col class="subhead"></th>-->
      <th scope=col class="subhead" abbr="alpha">Chemical</th>
      <th scope=col class="subhead" abbr="alpha">Chemical Class</th>
    </tr>
  </thead>
  <tbody>

  <?php

		$chemSQL = "SELECT chem.chemical, class.name FROM chem, chemclass, class WHERE chem.chemid = 				chemclass.chemid AND 	chemclass.class = class.classid ";
		$chemResult = mysql_query($chemSQL, $db);
		while($row = mysql_fetch_row($chemResult)){
			$chem = $row[0];
			$class = $row[1];

			echo "<tr><td>$chem</td>
				  <td>$class</td>
			      </tr>";
		}
?>

  </tbody>
</table>
</form>
<?php

?>

 </div>



 <div class="boxclear"> </div>




 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>

</body>
</html>
