<?php
include("Examplemysqlconfig.php");
include("drasticSrcMySQL.class.php");
$src = new drasticSrcMySQL($server, $user, $pw, $db, $table);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<link rel="stylesheet" type="text/css" href="css/grid_default.css"/>
<title>ExampleGrid7</title>
</head><body>
<script type="text/javascript" src="js/mootools-1.2-core.js"></script>
<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
<script type="text/javascript" src="js/drasticGrid.js"></script>

<div id="grid1"></div>
<div id="grid2"></div>
<script type="text/javascript">
var thegrid1 = new drasticGrid('grid1', {pathimg:"img/", pagelength:15});
var thegrid2 = new drasticGrid('grid2', {pathimg:"img/", pagelength:8, showcolsnot: new Array("Code")});
</script>

</body></html>