
<link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout"/>
<?php
$db = mysql_connect("localhost", "root", "arod678cbc3");

mysql_select_db("edge", $db);

$sql = "SELECT idnum, id, title, notes,book, pagenum,  investigator
FROM `bradfieldlabplasmids`
ORDER BY `id` ASC ";

$result = mysql_query($sql, $db);

echo "<table class=\"question\">";
echo "<tr class=\"question\"><td></td><td>ID#</td><td>PLASMID</td><td>TITLE</td><td>NOTES</td><td>BOOK</td><td>PAGE #</td><td>INVESTIGATOR</td></tr>";

while(list($idnum, $id, $title, $notes, $book, $pagenum, $investigator) = mysql_fetch_row($result)){

	echo "<tr class=\"question\"><td class=\"questionparameter\">$idnum</td><td class=\"questionparameter\">$id</td><td class=\"questionparameter\">$title</td><td class=\"questionparameter\">$notes</td><td class=\"questionparameter\">$book</td><td class=\"questionparameter\">$pagenum</td><td class=\"questionparameter\">$investigator</td></tr>";



}

echo "</table>";

?>