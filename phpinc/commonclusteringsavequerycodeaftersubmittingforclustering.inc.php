<?php
/**
 * Filename: commonclusteringsavequerycodeaftersubmittingforclustering.inc.php
 * The purpose of this code fragment is to create a central location for code related
 * to saving queries that is executed after the submission of the clustering form for clustering.
 */

// Get the referring URL
$queryurltype = $_SERVER['HTTP_REFERER'] ?? '';

// Set debug variables
$debugid = $debugid ?? -1;
$debug = $debug ?? -1;


// Debugging information
if ($userid == $debugid && $debug == 1) {
    analyze($_POST);
	echo 'in commonclusteringsavequerycodeaftersubmittingforclustering.inc.php<hr>';
}
$nborderedsubmit = false;
if(isset($_POST['nborderedsubmit'])){
    $nborderedsubmit = $_POST['nborderedsubmit'];
}

// Handle ordered submission
if ($orderedSubmit === "true" || $nborderedsubmit == "true") {

    if (isset($_GET['savedquery'])) {
        // Debugging information
        if ($userid == $debugid && $debug == 1) {
            echo "This is a saved query... UPDATING TEMP...<BR>";
            analyze($_POST);
        }
    } else {
        // Debugging information
        if ($userid == $debugid && $debug == 1) {
            echo "This is not a saved query and orderedSubmit == true...";
        }
    }

    // Initialize query text variables
    $query2text = '';
    $query2optstext = '';

    // Iterate through POST data
    foreach ($_POST as $key => $val) {
        if ($key !== "submit") { // Skip the submit button value
            if (is_array($val)) {
                foreach ($val as $optKey => $optVal) {
                    $query2optstext .= "$optKey=" . trim($optVal) . ":";
                }
            } else {
                $query2text .= "$key=" . trim($val) . ":";
            }
        }
    }

    // Fetch the temp query and saved query from POST
    $tempquery = filter_input(INPUT_POST, 'tempquery', FILTER_SANITIZE_NUMBER_INT);
    $savedquery = filter_input(INPUT_POST, 'savedquery', FILTER_SANITIZE_NUMBER_INT);

    // Debugging information
    if ($userid == $debugid && $debug == 1) {
        echo "In query 2 submit section<br>";
    }

    // Try-catch for database updates
    try {
        // Update the saved query in the database
        $sql = "UPDATE savedqueries SET query2 = ?, queryurltype = ? WHERE query = ?";
        $db->Execute($sql, [$query2text, $queryurltype, $tempquery]);

        if ($userid == $debugid && $debug == 1) {
            echo "$sql <br>";
        }

        $sql = "UPDATE savedqueries SET query2opts = ?, queryurltype = ? WHERE query = ?";
        $db->Execute($sql, [$query2optstext, $queryurltype, $tempquery]);

        if ($userid == $debugid && $debug == 1) {
            echo "$sql <br>";
        }

    } catch (Exception $e) {
        echo "Error updating saved queries: " . $e->getMessage();
    }

} else {

    // Handle non-ordered submission
    $query1text = '';
    analyze($_POST);
    // Iterate through POST data
    foreach ($_POST as $key => $val) {
        if ($key !== "submit") {
            $query1text .= "$key=" . trim($val) . ":";
        }
    }

    $tempquery = filter_input(INPUT_POST, 'tempquery', FILTER_SANITIZE_NUMBER_INT);

    // Try-catch for database insertion
    try {
        // Insert or update the saved query in the database
        $sql = "INSERT INTO savedqueries (query, userid, query1, querydate, queryurltype)
                VALUES (?, ?, ?, NOW(), ?)
                ON DUPLICATE KEY UPDATE query = ?";
        $db->Execute($sql, [$tempquery, $userid, $query1text, $queryurltype, $tempquery]);

        if ($userid == $debugid && $debug == 1) {
            echo "$sql <br>";
        }

    } catch (Exception $e) {
        echo "Error inserting/updating saved queries: " . $e->getMessage();
    }
}

?>
