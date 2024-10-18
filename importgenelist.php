<?php
require 'edge_db_connect2.php'; // Database connection
include "utilityfunctions.inc";

// Sanitize input data from $_POST
$imported = filter_input(INPUT_POST, 'imported', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
$updated = filter_input(INPUT_POST, 'updated', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
$userid = filter_input(INPUT_POST, 'userid', FILTER_SANITIZE_NUMBER_INT);
$thisorganism = filter_input(INPUT_POST, 'thisorganism', FILTER_SANITIZE_NUMBER_INT);
$oldlistid = filter_input(INPUT_POST, 'oldlistid', FILTER_SANITIZE_NUMBER_INT) ?? "";
$featurenums = filter_input(INPUT_POST, 'featurenums', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$public = filter_input(INPUT_POST, 'public', FILTER_SANITIZE_NUMBER_INT) ?? 0;
$listdesc = filter_input(INPUT_POST, 'desc', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$referer = $_SERVER['HTTP_REFERER'] ?? "";

// Trim and sanitize feature numbers
$featurenumarray = array_map('trim', explode(",", $featurenums));
$selectedfeatures = "";

// Process feature numbers that are selected
$first = true;
foreach ($featurenumarray as $feature) {
    $feature = filter_var($feature, FILTER_SANITIZE_NUMBER_INT);
    $isselected = "feature" . $feature;

    if (isset($_POST[$isselected])) {
        $selectedfeatures .= $first ? $feature : ",$feature";
        $first = false;
    }
}

try {
    // Start transaction
    $db->StartTrans();

    // If updating an existing list, delete the old list first
    if (!empty($updated)) {
        $sql = "DELETE FROM genelist WHERE listid = ?";
        $result = $db->Execute($sql, array($oldlistid));

        if (!$result) {
            throw new Exception("Error deleting old gene list. SQL: " . $sql);
        }
        $listid = $oldlistid;
    }

    // Insert the new gene list into the database
    $sql = "INSERT INTO genelist (userid, name, listdesc, arraytype, featurenums, public)
            VALUES (?, ?, ?, ?, ?, ?)";
    $result = $db->Execute($sql, array($userid, $name, $listdesc, $thisorganism, $selectedfeatures, $public));

    if (!$result) {
        throw new Exception("Error inserting new gene list. SQL: " . $sql);
    }

    // Commit the transaction
    $db->CompleteTrans();

    // Success message
    echo "<p>Gene list <strong>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</strong> successfully imported.</p>";

} catch (Exception $e) {
    // Rollback transaction if an error occurred
    $db->FailTrans();
    $db->CompleteTrans();

    // Log and display the error
    error_log($e->getMessage());
    echo "<p>Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
    die(); // Stop further execution
}

// Handling the redirect or close logic
$refererParts = explode("?", $referer);
if ($refererParts[1] == "genequery=1" || isset($_GET['openedit'])) {
    ?>
    <p class="close"><a href="javascript:window.close();"><img src="./GIFs/closebutton.gif" width="70" height="15" border="0"></a></p>
    <?php
} else {
    ?>
    <p class="close"><a href="javascript:window.opener.document.location.href='./savedgenelistedit.php'; window.close();"><img src="./GIFs/closebutton.gif" width="70" height="15" border="0"></a></p>
    <?php
}
?>
</body>
</html>
