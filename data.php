<?php

/*  Description:  This file lists the data file's */


require 'edge_db_connect2.php';
if ($logged_in == 0) {
	//die('Sorry you are not logged in, this area is restricted to registered members. <a href="../edge2/edge.php">Click here to go back to the main page</a>');
}
require './phpinc/edge3_db_connect.inc';
include 'header.inc';
require "formcheck.inc";
//require 'sorttable.inc';
?>
<script src="sorttable.js"></script>
<body>

	<?php
		include 'banner.inc';
	?>
 <div class="boxmiddle">
 <div style="display: static">
 <?php
include 'questionmenu.inc';
?>
</div>
	<h3 class="contenthead">The Data...</h3>
<div class="content">

	<p><h4>ARRAY ANNOTATIONS FILE (APPLICABLE TO BOTH CONDENSED AND UNCONDENSED DATA SETS)</h4></p>
		<ul>
			 <li><a href="./files/data/arrayannotations.txt">Array Annotations</a></li>
		</ul>

<hr>
<p><h2>Clone Annotations</h2></p>

<!--Click this link for the most up to date annotation files  <a href="./cloneannotations.php">Annotations</a> -->

	<p><h4>Uncondensed Clone Annotations File</h4></p>
	<p>
		<ul>

			 <li>The layout of this tab separated file</li>

		<table id="results" title="Data File Organization">
			<thead>
				<tr>
				<th class="colheader" colspan="2">Annotation File Organization</th>
				</tr>
			</thead>
			 <tr><td>CloneID</td><td>string</td></tr>
			 <tr><td>Annotated Name</td><td>string</td></tr>
			 <tr><td>Refseq Number</td><td>string</td></tr>
		</table>
		</ul>
	</p>
	<p><h4>Condensed Clone Annotations File</h4></p>
	<p>
		<ul>

			 <li>The layout of this tab separated file</li>

		<table id="results" title="Data File Organization">
			<thead>
				<tr>
				<th class="colheader" colspan="2">Annotation File Organization</th>
				</tr>
			</thead>
			 <tr><td>CloneID</td><td>string</td></tr>
			 <tr><td>Annotated Name</td><td>string</td></tr>
			 <tr><td>Refseq Hit</td><td>string</td></tr>
		</table>
		</ul>
	</p>
<hr>

<p><h2>CONDENSED DATA</h2></p>

<!--
		<p><h4>CONDENSED CLONE MAPPINGS</h4></p>
		<ul>
			 <li><a href="./admin/createcondensingfile.php">Condensed Clone Mappings</a></li>
			 <li>This file contains a mapping from a condensed clone to the representative clone</li>
			 	<ul>
					<li>The first column is the representative clone id and the subsequent colums are the clones used to generate the value for the representative clone id</li>
					<li>Essentially the representative clone id's value is an averaging of the subsequent clones associated w/ the representative clone.  The values are condensed into one, because they've been determined to have the same annotation.</li>
				</ul>
		</ul>

	<p><h4>ALL CONDENSED DATA DELIVERED IN DYNAMICALLY GENERATED FILES</h4></p>
		<ul>
			 <li><a href="./admin/condensedclonelist.php">Entire Condensed Data Set</a>   (<b><font color="red">NOTE:</font></b> It may take about four to five minutes to generate the files for the Entire Condensed Data Set.)</li>
			 <li>The Condensed Data Set dynamically generates files w/ the number of columns limited to <= 256 so they can be opened in a spreadsheet program.</li>

		</ul>
-->
	<p><h4>CONDENSED TIME SERIES DATA FILES</h4></p>
		<ul>
		 <li><a href="./files/data/condensedlpstimeseriesA.csv">Condensed LPS Time Course Data (Time Series A)</a></li>
		 <li><a href="./files/data/condensedtcddtimeseriesB.csv">Condensed TCDD Time Course(Time Series B)</a></li>
		  <li><a href="./files/data/condensedtcddtimeseriesC.csv">Condensed TCDD Time Course(Time Series C)</a></li>
		<li><a href="./files/data/condensedtcddtimeseriesD.csv">Condensed TCDD Time Course(Time Series D)</a></li>
		<li><a href="./files/data/condensedEstrogenTimeSeries.csv">Condensed Estrogen Time Course</a></li>
		</ul>

	<table id="results" title="Condensed Data File Organization">
	<thead>
		<tr>
		<th class="colheader" colspan="2">Condensed Data File Organization</th>
		</tr>

	</thead>
	</table>
	<ul>

		<li>First column is the cloneid column</li>
		<li>Subsequent columns are array columns w/ values for each respective <b>cloneid(row) x arrayid(col)</b> value being the condensed final ratio value</li>
		<li>These files contain only the annotated clones, ie. any clone that has not been verified is left out.</li>

	</ul>


<hr>
<p><h2>UNCONDENSED DATA</h2></p>

	<p><h4>UNCONDENSED DATA FILES</h4></p>



	<p>
		<ol>
			<li>Don't be fooled by the ".csv" extension, these files are tab separated.</li>
			<li>The table below delineates the organization of a data file.</li>
			<li>Each line of tab separated values represents data for an individual clone, so basically transpose the table below and take off the data type line and you've a line in the file.</li>
			<li>My guess is that you may be concerned only w/ the final ratio value.</li>

		</ol>
		<ul>

			 <li><a HREF="./files/data/time_series_A_LPS.tar.gz">LPS Time Course Data (Time Series A)</a></li>
			 <li><a HREF="./files/data/time_series_B_TCDD.tar.gz">TCDD Time Course(Time Series B)</a></li>
			  <li><a HREF="./files/data/time_series_C_TCDD.tar.gz">TCDD Time Course(Time Series C)</a></li>
			   <li><a href="./files/data/time_series_D_TCDD.tar.gz">TCDD Time Course(Time Series D)</a></li>
<li><a href="./files/data/EstrogenTimeCourse.tar.gz">Estrogen Time Course</a></li>

		</ul>


	<table id="results" title="Data File Organization">
	<thead>
		<tr>
		<th class="colheader" colspan="2">Data File Organization</th>
		</tr>

	</thead>
		<tr>
			<td>arrayid</td><td>integer</td>
		</tr>
		<tr>
			<td>cloneid</td><td>integer</td>
		</tr>
		<tr>
			<td>spot1cy3</td><td>float</td>
		</tr>
		<tr>
			<td>spot1cy5</td><td>float</td>
		</tr>
		<tr>
			<td>spot2cy3</td><td>float</td>
		</tr>
		<tr>
			<td>spot2cy5</td><td>float</td>
		</tr>
		<tr>
			<td>spot3cy3</td><td>float</td>
		</tr>
		<tr>
			<td>spot3cy5</td><td>float</td>
		</tr>
		<tr>
			<td>spot4cy3</td><td>float</td>
		</tr>
		<tr>
			<td>spot4cy5</td><td>float</td>
		</tr>
		<tr>
			<td>spot5cy3</td><td>float</td>
		</tr>
		<tr>
			<td>spot5cy5</td><td>float</td>
		</tr>
		<tr>
			<td>spot6cy3</td><td>float</td>
		</tr>
		<tr>
			<td>spot6cy5</td><td>float</td>
		</tr>
		<tr>
			<td>revspot1cy3</td><td>float</td>
		</tr>
		<tr>
			<td>revspot1cy5</td><td>float</td>
		</tr>
		<tr>
			<td>revspot2cy3</td><td>float</td>
		</tr>
		<tr>
			<td>revspot2cy5</td><td>float</td>
		</tr>
		<tr>
			<td>revspot3cy3</td><td>float</td>
		</tr>
		<tr>
			<td>revspot3cy5</td><td>float</td>
		</tr>
		<tr>
			<td>revspot4cy3</td><td>float</td>
		</tr>
		<tr>
			<td>revspot4cy5</td><td>float</td>
		</tr>
		<tr>
			<td>revspot5cy3</td><td>float</td>
		</tr>
		<tr>
			<td>revspot5cy5</td><td>float</td>
		</tr>
		<tr>
			<td>revspot6cy3</td><td>float</td>
		</tr>
		<tr>
			<td>revspot6cy5</td><td>float</td>
		</tr>
		<tr>
			<td>trimmean</td><td>float</td>
		</tr>
		<tr>
			<td>revtrimmean</td><td>float</td>
		</tr>
		<tr>
			<td>finalratio</td><td>float</td>
		</tr>

        </table>
		</ul>


	</p>


</div>
 </div>
 <?php
	include 'leftmenu.inc';

?>
 <div class="boxclear"> </div>
 <div class="boxclear"> </div>
 <div class="boxfooter"></div>
</body>
</html>
