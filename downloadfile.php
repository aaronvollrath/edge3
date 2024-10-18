<?php
/* This script is used to download a file from datafiles table.....*/


require 'edge_db_connect2.php';
include 'edge3header.inc';
include 'utilityfunctions.inc';
require 'globalfilelocations.inc';

if(!isset($_SESSION['userid'])){
	die("You need to login to use this function.");
}
?>
<body>
	<div class="header">

		<img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left"></img>
		<h4 align="bottom"><font face="arial">Environment, Drugs and Gene Expression^3</font></h4>
	</div>
<br>
<br>
<br>
<div>

<br>
<br>
<br>
<?php

	if(isset($_GET['filenum'])){
		$id = $_GET['filenum'];
		$sql = "SELECT * FROM datafiles WHERE fileid = $id";
		$fileresult = $db->Execute($sql);
		if($filerow = $fileresult->FetchRow()){
			$filetype = $filerow['filetype'];
			$encodeddata = $filerow['data'];
			$file_contents = base64_decode($encodeddata);
			$filenum = rand(0, 25000);
			$nameoffile = $filenum."data.".$filetype;
			$randfilename = $IMAGESdir."/".$filenum . "data.".$filetype;
			$handle = fopen($randfilename, 'w');
			fwrite($handle, $file_contents);
			fclose($handle);

			echo "Click <a href='./IMAGES/$nameoffile'>HERE</a> to download the file";
				
		}else{
			echo "ERROR: There are not any files!<br>";
		}
	}else{
		echo "You've reached this page by error!<br>";
	}
?>

</div>
