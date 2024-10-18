<?php


require 'edge_db_connect2.php';


$db = mysql_connect("localhost", "vollrath", "arod678cbc3");


mysql_select_db("edge", $db);


include 'edge_update_user_activity.inc';


?>

<html>
<BODY BGCOLOR="#FFFFFF" LINK="#000099" VLINK="#6666CC">
<head>

<STYLE TYPE="text/css">
<!--
 #mybutton   {border-style: inset;
        border-color: #D7DDDF;
        background-color: #344FFF;
        text-decoration: none;
        width: 120px;
        text-align: center;}

  A.buttontext {color: white;
                text-decoration: none;
                font: bold 10pt Verdana;
                cursor: hand;}

  .buttonover  {color: yellow;
                text-decoration: none;
                font: bold 10pt Verdana;
                cursor: hand;}
  TABLE.texttype {color: black;
  		text-decoration: none;
		font: 10pt Arial;
		}
-->
</STYLE>
</head>
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=600>
<TR>
<TD WIDTH=255>
<P><IMG SRC="../GIFs/EDGE2128x60.png" border=0 WIDTH=128 HEIGHT=60 ALIGN=top></P>
</TD>
<TD VALIGN=bottom align=bottom WIDTH=460>
<CENTER><span class="H2"><FONT SIZE="+2" FACE="Arial">Environment, Drugs
and Gene Expression</FONT></span><BR>
<P>
</P></CENTER>
</TD>
</TR>
</TABLE>
<table align=center border=1 class="texttype">
  <caption>
     <b><h2><font face="Arial" >EST Library Information</font></h2></b>
  </caption>
  <col width=250 align="left">
  <col width=64 align="left">
  <col width=300 align="left">
  <thead>
    <tr>
      <th scope=col>Library</th>
      <th scope=col>Tissue</th>
      <th scope=col>Description</th>
    </tr>
  </thead>
  <tbody>

  <?php 

	// get all of the library short names.....
	$alllibnamesSQL = "SELECT estname, esttissue, descr from estnames where showest = 'Y' order by id";

		$alllibnamesResult = mysql_query($alllibnamesSQL, $db);
		while($row = mysql_fetch_row($alllibnamesResult)){
			$libname = $row[0];
			$libtissue = $row[1];
			$libdesc = $row[2];
			echo "<tr><td>$libname</td><td>$libtissue</td><td>$libdesc</td></tr>";
		}
?>

  </tbody>
</table>

<p class="close"><a href="javascript:window.close();"><img src="./GIFs/closebutton.gif" width="70" height="15" border="0"></a></p>

</body>
</html>
