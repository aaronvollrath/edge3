<?php
/***
Location: /edge2/admin
Description:  This page is used to view the activity of the userid(s) passed in.
POST: none
GET: 'submit', 'user[number]', 'numrows'
Files include or required: 'edge_db_connect2.php','edge_update_user_activity.inc', 'adminmenu.inc','../adminleftmenu.inc'
***/

require 'edge_db_connect2.php';
// Need to check if the user is logged in because this is a restricted area...

include 'edge_update_user_activity.inc';

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

<body>
	<div class="boxheader">
		<img src="../GIFs/EDGE2128x60.png" alt="Edge^2" align="left"></img>
		<img src="../GIFs/edgebanner.jpg" alt="environment" width="90" height="75" align="left"></img>
		<h2 class="bannerhead" align="bottom">Environment, Drugs and Gene Expression</h2>
	</div>
 <div class="boxmiddle">
  <div style="display: static">
 <?php
include 'adminmenu.inc';
?>
</div>

 <h3 class="contenthead">User Activity</h3>
<?php
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
//analyze($_GET);
$priv_level = $_SESSION['priv_level'];
//echo "your priv level = $priv_level<br>";
if($priv_level != 99){
	echo "Sorry, you are not authorized to access this page.";
}
else{
//echo "in else...";
$idarray= array();
$idcount = 0;
$user = "user$idcount";

$numrows = $_GET['numrows'];
//echo "Here were the number of rows chosen to be shown: $numrows<br>";
while ($idcount < $numrows){
	//echo "in while loop, cloneid=";
	$user = "user$idcount";
	//echo "userstring = $user<br>";
	$userid = $_GET[$user];
	//echo "$userid<br>";
	if($userid != ""){
		//echo "Pushing id on to array: $userid<br>";
		array_push($idarray, $userid);
	}
	$idcount++;
}
$elements = count($idarray);

// Get the list of libraries....
	$idcounter = 0;
	$idList = "";
	while($idcounter < $elements){
		if($idcounter == ($elements - 1)){
			$commaVal = "";
		}
		else{
			$commaVal = " OR ";
		}
		$idList .= "userid = $idarray[$idcounter]$commaVal";
		$idcounter++;
	}
	//echo "idList = $idList<br>";

?>
<table id="results">

  <col width=10 align="center">
  <col width=256 align="left">
  <col width=128 align="left">
  <col width=128 align="left">
  <col width=256 align="left">

  <thead>
    <tr>
      <th scope=col>User ID</th>
      <th scope=col>Date/Time</th>
      <th scope=col>User IP</th>
      <th scope=col>Referring URL</th>
      <th scope=col>URL</th>
    </tr>
  </thead>
  <tbody>

  <?php
  		$countSQL = "Select count(*) from useractivity where $idList";
		$countResult = mysql_query($countSQL, $db);
		//echo "<br>$countSQL<br>";
		$row = mysql_fetch_row($countResult);
		$numrows = $row[0];


		$userSQL = "Select userid, occurred, userip, refurl, url from useractivity where $idList ORDER BY userid";
		$userResult = mysql_query($userSQL, $db);
		$count = 0;
		while($row = mysql_fetch_row($userResult)){
			$userid = $row[0];
			$occurred = $row[1];
			$userip = $row[2];
			$refurl = $row[3];
			$url = $row[4];

			echo "<tr><td>$userid</td><td>$occurred</td><td>$userip</td>
			<td>$refurl</td><td>$url</td></tr>";
			$count++;
		}
}
?>

  </tbody>
</table>
</div>
 <?php
	include '../adminleftmenu.inc';

?>
 <div class="boxclear"> </div>
 <div class="boxclear"> </div>
 <div class="boxfooter"><p></p></div>
</body>
</html>

