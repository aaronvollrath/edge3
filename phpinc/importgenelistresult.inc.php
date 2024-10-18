<?php
// Start session securely
session_start();
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Include necessary files
require 'edge_db_connect2.php'; // Database connection
require '../utilityfunctions.inc'; // Utility functions
require 'globalfilelocations.inc'; // Global file locations

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    die("You must be logged in to use this functionality!");
}

$userid = $_SESSION['userid'];

// Sanitize and validate GET parameters securely for PHP 8
$arraytype = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_NUMBER_INT);
$featurefilenumber = filter_input(INPUT_GET, 'featurefilenumber', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$querytype = filter_input(INPUT_GET, 'querytype', FILTER_SANITIZE_NUMBER_INT);
$contrastnumber = filter_input(INPUT_GET, 'contrastnumber', FILTER_SANITIZE_NUMBER_INT);
$name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "Saved gene list.";

// Ensure that all required parameters are present
# 10OCT2024 - this was causing an error...
/* if (!$arraytype || !$featurefilenumber || !$querytype) {
   die("Required parameters are missing!");
} */

// Determine the filename based on the query type
if ($querytype == 1) {
    $filename = $IMAGESdir . "/" . $featurefilenumber . "genelist_" . $contrastnumber . ".txt";
} else {
    $filename = $IMAGESdir . "/" . $featurefilenumber . ".csv";
}

// Open the file and handle errors properly
if (!file_exists($filename)) {
    die("The file $filename could not be found!");
}

$featurenumbersarray = [];
if (($fd = fopen($filename, 'r')) !== false) {
    $linecount = 0;
    while (($buffer = fgets($fd)) !== false) {
        // Skip the first line (header)
        if ($linecount == 0) {
            $linecount++;
            continue;
        }
        
        if ($querytype == 1) {
            $linearray = explode("\t", $buffer);
            $num_name = $linearray[0];
            $number = explode("_", $num_name)[0];
        } else {
            $linearray = explode(",", $buffer);
            $number = $linearray[0];
        }

        $featurenumbersarray[] = trim($number);
    }
    fclose($fd);
} else {
    die("The file $filename could not be opened!");
}

// Combine the feature numbers into a single string
$selectedfeatures = implode(',', $featurenumbersarray);

// Insert the gene list into the database securely with prepared statements
try {
    $sql = "INSERT INTO genelist (userid, name, listdesc, arraytype, featurenums, public) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $public = 0;
    $listdesc = ""; // Default value
    $params = [$userid, $name, $listdesc, $arraytype, $selectedfeatures, $public];

    $db->StartTrans(); // Begin a transaction
    $result = $db->Execute($sql, $params);

    if (!$result) {
        throw new Exception("Error inserting gene list: " . $db->ErrorMsg());
    }

    $listnum = $db->Insert_ID();
    $db->CompleteTrans(); // Commit the transaction

    echo "Your gene list was imported and is ready to be used in subsequent analyses.";
    echo "<br><br>Click on 'Update' to change the name of this list and modify other parameters: 
          <a href='../updategenelist.php?listid=$listnum&openedit=false'>Update</a>";

} catch (Exception $e) {
    $db->FailTrans(); // Rollback the transaction on error
    die("An error occurred: " . $e->getMessage());
}

?>
<p class="close"><a href="javascript:window.close();"><img src="../GIFs/closebutton.gif" width="70" height="15" border="0"></a></p>
</div>
</body>
</html>
