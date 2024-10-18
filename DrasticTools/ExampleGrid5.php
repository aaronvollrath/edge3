<?php
include("Examplemysqlconfig.php");
include("drasticSrcMySQL.class.php");

$options = array(
	"defaultcols" => array("Continent" => "Europe")
);
$src = new drasticSrcMySQL($server, $user, $pw, $db, $table, $options);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<link rel="stylesheet" type="text/css" href="css/grid_default.css"/>
<title>ExampleGrid5</title>
</head><body>
<script type="text/javascript" src="js/mootools-1.2-core.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="js/drasticGrid.js"></script>

<div id="grid1"></div>
<script type="text/javascript">
var thegrid = new drasticGrid('grid1', {
	pathimg:"img/",
	pagelength: 10,	
	showcolsnot: new Array("Code", "Name"),
	renamecols: {"Continent":"Cont", "LocalName":"LN"}
});
</script>

</body></html>