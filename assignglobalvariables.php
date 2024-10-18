<?php

/*
	Description:  This script is used to create the file 'globalvariables.inc.php'.  You must have permissions set to allow apache or whatever web server you are using to write to the ./phpinc directory.

*/


if(!isset($_POST['varssubmit'])){

?>

<p class="styletext">

	<form name="query" method="post" onsubmit="return checkQuestion1Form()" action="<?php  $_SERVER['PHP_SELF'] ?>">
	
	<table class="question" width="400">
	<thead>
	<th colspan='3'>EDGE<sup>3</sup> Global Variables</th>
	</thead>
	<input type='hidden' value="$_SERVER['HTTP_HOST']" name='serverhost'>
	<tr>
	<td class="questionparameter" ><strong>Database User Name:</strong></td>
	<td class="results">
	<input name="edgedbuser" type="text" value="root" align="right"></input>
	</select>
	</td>
	<td class="results">
	Default value is listed.
	</td>
	</tr>
	<td class="questionparameter" ><strong>EDGE<sup>3</sup> Database User Password</strong></td>
	<td class="results">
	<input name="password1" type="text">
	</td>
	<td class="results">
	<input name="password2" type="text">
	</td>
	</tr>
	<tr>
	<td class="questionparameter" ><strong>EDGE<sup>3</sup> Database Server</strong></td>
	<td class="results">
	<input name="dbserver" type="text" value="localhost">
	</td>
	<td class="results">
	Default value is listed.
	</td>
	</tr>
	
	<td class="questionparameter" ><strong>EDGE<sup>3</sup> Database Name</strong></td>
	<td class="results">
	<input name="dbname" type="text" value='edge'>
	</td>
	<td class="results">
	Default is listed.
	</td>
	</tr>
	<tr>
	<td class="questionparameter" ><strong>EDGE<sup>3</sup> Database Type</strong></td>
	<td class="results">
	<input name="db" type="text" value="mysql" align="right"></input>
	</td>
	<td class="results">
	Default value shown.  For others, see <a href='http://phplens.com/adodb/supported.databases.html' target='_blank'>Support DBs</a>
	</td>
	</tr>
	
	<tr>
	<td class="questionparameter" ><strong>Recipient of Admin Emails:</strong></td>
	<td class="results">
	<input name ="adminemailrecip" type="text" value="" align="right"></input>
	</td>
	<td class="results">
	Admin related notices sent to these addresses (separate by commas)
	</td>
	</tr>
	<tr>
	<td class="questionparameter" ><strong>Admin Email Address:</strong></td>
	<td class="results">
	<input name ="adminemail" type="text" value="" align="right"></input>
	</td>
	<td class="results">
	Address that will show up on emails sent from the server.
	</td>
	</tr>
	<tr>
	<td class="questionparameter" ><strong>Your server name:</strong></td>
	<td class="results">
	<input name ="servername" type="text" value="your.server.net" align="right"></input>
	</td>
	<td class="results">
	The name of your server.
	</td>
	</tr>
	<tr>
	<td><input type="submit" name="varssubmit" value="Submit"></td>
	<td></td>
	<td><input type="reset" value="Reset Form"</td>
	</tr>
	
	</table>
</form>
</p>
<?php
}else{
	echo "form submitted!";









}
?>