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
<CENTER><span class="H2"><FONT SIZE="+2" FACE="Arial">Environmental
Database For Gene Expression</FONT></span><BR>
<P>
</P></CENTER>
</TD>
</TR>
</TABLE>
</P>
</TD>
</TR><!-- the search bar-->

Instructions go here.....


</TABLE>
