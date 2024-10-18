<?php

require 'edge_db_connect2.php';
$db = mysql_connect("localhost", "vollrath", "arod678cbc3");

mysql_select_db("edge", $db);

include 'edge_update_user_activity.inc';

?>
<html>
<BODY BGCOLOR="#FFFFFF" LINK="#000099" VLINK="#6666CC">
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=600>
<TR>
<TD WIDTH=255>
<P><A HREF="http://genome.oncology.wisc.edu/edge2/edge.php"><IMG SRC="../GIFs/EDGE2128x60.png" border=0 WIDTH=128 HEIGHT=60 ALIGN=top></A></P>
</TD>
<TD VALIGN=bottom align=bottom WIDTH=460>
<CENTER><span class="H2"><FONT SIZE="+2" FACE="Arial">Environment, Drugs and Gene Expression</FONT></span><BR>
<P>
</P></CENTER>
</TD>
</TR>
</TABLE>
</P>
</TD>
</TR><!-- the search bar-->
<tr>
<td width=600>
<center><img src="../GIFs/estqueryinstr292x28.png" width=292 height=28></center>
<p>You can search the libraries by <b>Primary Name</b> and <b>5' Refseq</b>.  You can search across all libraries by not selecting a specific library from the list.  You can search across specific libraries by selecting them individually by holding down the control key and clicking on each respective library or you can click on one, hold the shift key and scroll up or down, and then click a library to span from the initial library clicked to the last library clicked.
</p>
<p>
It is also possible to enter multiple values in the <b>Primary Name</b> and <b>5' Refseq</b> fields.  This is done by using "," to delimit the values, eg. for <b>Primary Name</b>, albumin,4A1,4A2 and for <b>5' Refseq</b>, NM_0169174,NM_133749,XM_132149.
</p>
<p>
You also have the option to limit the number of rows returned by your query.  If you select "<b>All</b>" rows you will get the entire result set associated with the query parameters specified.  If the limit you set is less than the total rows of the result set returned you can page through the results at the number of rows you specified.
</p>
<p>
A graphical representation of the data is available for queries where 10 or fewer rows are specified and you do not choose to return across all libraries.  The main reason for limiting the graphical representation to 10 or fewer rows is due to limitations in representing the data effectively given a finite dimensional space, namely your screen size.  There will be an option to override this limitation at some point in the future.
</p>
</td>
</tr>
</TABLE>
<p class="close"><a href="javascript:window.close();"><img src="./GIFs/closebutton.gif" width="70" height="15" border="0"></a></p>
</body>
</html>