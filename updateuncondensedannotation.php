<?php
require './phpinc/edge3_db_connect.inc';
function analyze(&$array) {
   foreach($array as $key=>$value) {
       if(is_array($value)) {
           echo "<li>Array:<blockquote>";
             analyze($value);
           echo "</blockquote>";
         } elseif(is_object($value)) {
           echo "<li>Object:<blockquote>";
             analyze($value);
           echo "</blockquote>";
         } else {
             echo "<li>[" . $key . "] " . $value;
       }
   }
}

echo "Update annotations....<br>";
$count = 0;
foreach($_GET as $key=>$value){
	//echo "$_GET[$key]<br>";
	$_GET[$key] = str_replace("\"", "", $_GET[$key]);
	if($count != 0){
		$_GET[$key] = "\"$_GET[$key]\"";
	}
	if($value == ""){
		$_GET[$key] = "NULL";
	}
	$count++;
	echo "$key = $_GET[$key]<br>";
	//echo "$_GET[$key]<br>";
}

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
$archivecomments = "NULL";
$sql = "INSERT INTO provenance(cloneid, annname, refseq, refseqhit, hit_def, locustag, synonyms, gi, blast_source, blast_source_date, bit_score, e_value, seqdirused, lastannomod, datearchived, anncomments, archivecomments) VALUES ($oldvals[0], $oldvals[1], $oldvals[2], $oldvals[3], $oldvals[4], $oldvals[5], $oldvals[6], $oldvals[7], $oldvals[8], $oldvals[9], $oldvals[10], $oldvals[11], $oldvals[12], $oldvals[13], NOW(), $oldvals[14], $archivecomments)";
echo "$sql<br>";
$result = mysql_query($sql,$db);
$_GET[seqdirused] = str_replace("\"", "", $_GET[seqdirused]);
echo "<br>updated old annotations...<br>";

$sql = "DELETE FROM annotations WHERE cloneid = $cloneid";
$result = mysql_query($sql,$db);
//echo "<br>deleted from annotations...<br>";
if($_GET[comments]==""){
	$comments = "NULL";
}
else{
	$comments = "$_GET[comments]";
}
$sql = "INSERT INTO annotations(cloneid, annname, refseq, refseqhit, hit_def, locustag, synonyms, gi, blast_source, blast_source_date, bit_score, e_value, seqdirused,datemodified, comments) VALUES ($_GET[cloneid], $_GET[annname], $_GET[refseq], \"\", $_GET[hit_def], $_GET[locustag], $_GET[synonyms], $_GET[gi], $_GET[blast_source], $_GET[blast_source_date], $_GET[bit_score], $_GET[e_value], $_GET[seqdirused], NOW(), $comments)";
$result = mysql_query($sql,$db);
//echo "$sql<br>";
echo "inserted into annotations<br>";
/*$sql = "UPDATE TABLE annotations SET annname=$_GET[annname], refseq=$_GET[refseq], hit_def=$_GET[hit_def], locustag=$_GET[locustag], synonyms = $_GET[synonyms],
	gi = $_GET[gi], blast_source = $_GET[blast_source], blast_source_date = $_GET[blast_source_date], bit_score = $_GET[bit_score], e_value = $_GET[e_value], seqdirused = $_GET[seqdirused] WHERE cloneid = $_GET[cloneid]";
$result = mysql_query($sql,$db);*/
	//echo "<br><b>$sql</b><br>";




?>
