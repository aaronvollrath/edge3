<?php

require './phpinc/edge3_db_connect.inc';
// This script will go through and modify the annotations for UNCONDENSED clones that do not have any sequence associated with them...


// what range of clones are we dealing with?


$startclone = 3000;

$endclone = 4608;

for($i = $startclone; $i <=$endclone; $i++){
	$cloneid = $i;
	echo "The clone is: $cloneid<br>";
	$gotseq = 0;
	// is there any sequence for this clone???
	$sql = "SELECT count(*) FROM edge_v1_liver3primeseq WHERE cloneid = $cloneid";
	$seqResult = mysql_query($sql, $db);
	$_3count = mysql_fetch_row($seqResult);
	if($_3count[0] > 0){
		$sql = "SELECT 3primeseq FROM edge_v1_liver3primeseq WHERE cloneid = $cloneid";
			$seqResult = mysql_query($sql, $db);
			while($row = mysql_fetch_row($seqResult)){
				$seq = $row[0];
				if($seq != ""){
				$gotseq = 1;
				}
			}
	}
	$sql = "SELECT count(*) FROM edge_v1_liver5primeseq WHERE cloneid = $cloneid";
	$seqResult = mysql_query($sql, $db);
	$_5count = mysql_fetch_row($seqResult);
	if($_5count[0] > 0){
		$sql = "SELECT 5primeseq FROM edge_v1_liver5primeseq WHERE cloneid = $cloneid";
			$seqResult = mysql_query($sql, $db);
			while($row = mysql_fetch_row($seqResult)){
				$seq = $row[0];
				if($seq != ""){
				$gotseq = 1;
				}
			}
	}

	if($gotseq == 0){
		echo "#3prime is: $_3count[0]<br>";
		echo "#5prime is: $_5count[0]<br>";

		// Get the old values for this cloneid from the annotations table....
		$sql = "SELECT * FROM annotations WHERE cloneid = $cloneid";
		//echo "$sql<br>";
		$result = mysql_query($sql,$db);
		$oldvals = mysql_fetch_row($result);
		$count = 0;

		foreach($oldvals as $val){
			$oldvals[$count] = str_replace("\"", "", $oldvals[$count]);
			if($count != 0){
				$oldvals[$count] = "\"$oldvals[$count]\"";
			}
			if($val == ""){
				$oldvals[$count] = "NULL";
			}
			$count++;
		}

		$sql = "DELETE FROM annotations WHERE cloneid = $cloneid";
		$result = mysql_query($sql,$db);
		$comments = "\"No sequence.  Will revisit this annotation.\"";

		$sql = "INSERT INTO annotations(cloneid, annname, refseq, refseqhit, hit_def, locustag, synonyms, gi, blast_source, blast_source_date, bit_score, e_value, seqdirused,datemodified, comments) VALUES ($oldvals[0], $oldvals[1], $oldvals[2], $oldvals[3], $oldvals[4], $oldvals[5], $oldvals[6], $oldvals[7], $oldvals[8], $oldvals[9], $oldvals[10], $oldvals[11], $oldvals[12], NOW(), $comments)";

		$result = mysql_query($sql,$db);
		echo "$sql<br>";


	}




}
