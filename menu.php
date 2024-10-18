<?php
session_start();
?>

<html>
<head>
<SCRIPT LANGUAGE="JavaScript">
  function imgswap(name, type) 
  {
   eval("document." + name + ".src = 'GIFs/AboutEDGE" + type + ".png'");
  }
  </SCRIPT>
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
-->
</STYLE>

</head>
<body >
<table>
<tr>
<td>
<?php

if(strcmp($_SESSION['username'], "") == 0){
	// Display Login.....
?>
<A HREF="login.php" target="display" CLASS="buttontext"
   onMouseOver="this.className='buttonover';"
   onMouseOut="this.className='buttontext';">
<DIV ID="mybutton">Login</DIV></A>
<?php

}
else{ // Display logout...
?>
<DIV><b>Welcome <?php echo $_SESSION['firstname'] ?> <?php echo $_SESSION['lastname'] ?>!</b></DIV>
<A HREF="login.php" target="display" CLASS="buttontext"
   onMouseOver="this.className='buttonover';"
   onMouseOut="this.className='buttontext';">
<DIV ID="mybutton">Logout/Update</DIV></A>

<?php
}
?>

<A HREF="default.php" target="display" CLASS="buttontext"
   onMouseOver="this.className='buttonover';"
   onMouseOut="this.className='buttontext';">
<DIV ID="mybutton">Home</DIV></A>

<A HREF="about_edge.php" target="display" CLASS="buttontext"
   onMouseOver="this.className='buttonover';"
   onMouseOut="this.className='buttontext';">
<DIV ID="mybutton">About Edge</DIV></A>

<A HREF="contact_info.php" target="display" CLASS="buttontext"
   onMouseOver="this.className='buttonover';"
   onMouseOut="this.className='buttontext';">
<DIV ID="mybutton">Contact Information</DIV></A>
<A HREF="flow_chart.php" target="display" CLASS="buttontext"
   onMouseOver="this.className='buttonover';"
   onMouseOut="this.className='buttontext';">
<DIV ID="mybutton">
Project Flow Chart
</DIV></A>

<A HREF="publications.php" target="display" CLASS="buttontext"
   onMouseOver="this.className='buttonover';"
   onMouseOut="this.className='buttontext';">
<DIV ID="mybutton">
Publications Related To EDGE
</DIV></A>

<A HREF="./blast/blast.html" target="display" CLASS="buttontext"
   onMouseOver="this.className='buttonover';"
   onMouseOut="this.className='buttontext';">
<DIV ID="mybutton">
EDGE BLAST
</DIV></A>

<A HREF="protocols.php" target="display" CLASS="buttontext"
   onMouseOver="this.className='buttonover';"
   onMouseOut="this.className='buttontext';">
<DIV ID="mybutton">
Protocols
</DIV></A>

<A HREF="http://mcardle.oncology.wisc.edu/bradfield" target="_blank" CLASS="buttontext"
   onMouseOver="this.className='buttonover';"
   onMouseOut="this.className='buttontext';">
<DIV ID="mybutton">
Bradfield Laboratory
</DIV></A>



<?php
	$priv = $_SESSION['priv_level'];
	if($priv == 99){
?>

	<A HREF="./admin/users.php" target="display" CLASS="buttontext"
   	onMouseOver="this.className='buttonover';"
   	onMouseOut="this.className='buttontext';">
	<DIV ID="mybutton">
	Admin Stuff
	</DIV></A>


	<A HREF="./estquerysvg.php" target="display" CLASS="buttontext"
   	onMouseOver="this.className='buttonover';"
   	onMouseOut="this.className='buttontext';">
	<DIV ID="mybutton">
	SVG EST Query
	</DIV></A>
<?php
	}
?>


</td>
</tr>
</table>
</body>
</html>
