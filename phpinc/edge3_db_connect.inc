<?php
// Connection file


mysqli_report(MYSQLI_REPORT_OFF);
/* @ is used to suppress warnings */
$db = @mysqli_connect('localhost', 'root', '', 'edge2');
if (!$db) {
    /* Use your preferred error logging method here */
    error_log('Connection error: ' . mysqli_connect_error());
}

//$db = mysqli_connect("localhost", "root", "");
//mysqli_select_db("edge", $db);


?>