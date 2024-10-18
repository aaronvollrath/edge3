<?php

/*echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";*/
 function utime (){
$time = explode( " ", microtime());
$usec = (double)$time[0];
$sec = (double)$time[1];
return $sec + $usec;
}
$start = utime();
?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout" />
<title>EDGE^3</title>
<script language="JavaScript" type="Text/JavaScript" src="./websitethumbnail/arc90_linkthumb.js"></script>
</head>