<?php
/*
*   filename: commonclusteringsavedquerycode.inc
*   refactoring status: 10OCT2024
*
*/
$debug = -1;
$userid = $_SESSION['userid'] ?? null;

//############# ARE WE DEALING W/ A SAVED QUERY? ############################
if (isset($_GET['savedquery'])) {
    // CREATE A TEMP QUERY TO STORE THE UPDATED SAVED QUERY

    // Get the max query number in savedqueries table
    try {
        $sql = "SELECT MAX(query) FROM savedqueries";
        $sqlResult = $db->Execute($sql);
        $row = $sqlResult->FetchRow();
        $tempquery = $row[0] ?? 1; // Initialize if NULL
        if ($tempquery !== "NULL") {
            $tempquery += 1;
        }
    } catch (Exception $e) {
        echo "Error fetching max query: " . $e->getMessage();
    }

    // Debugging
    if ($userid == 1 && $debug == 1) {
        echo "$sql<br>";
        echo "In commonclusteringsavedquerycode.inc and tempquery= $tempquery<br>";
    }

    // Retrieve information for the saved query
    $savedquery = filter_input(INPUT_GET, 'savedquery', FILTER_SANITIZE_NUMBER_INT);
    try {
        $sql = "SELECT queryname, query1, query2opts FROM savedqueries WHERE query = ? AND userid = ?";
        if ($userid == 1 && $debug == 1) {
            echo "$sql<br>";
        }
        $sqlResult = $db->Execute($sql, [$savedquery, $userid]);
        if ($sqlResult === false) {
            echo "Error getting value for the query: $sql<br>";
            echo "Database error message: " . $db->ErrorMsg();
        } else {
            $row = $sqlResult->FetchRow();
            $queryname = $row[0];
            $query1 = $row[1];
            $query2opts = $row[2];

            // Determine if this is a two-pager query
            $is2pager = ($query2opts !== "NULL") ? 1 : 0;

            // Explode query1 into an array by the ':' separator
            $vals = explode(":", $query1);
            array_pop($vals); // Remove last empty value

            $savedvals = [];
            $savedchemvals = [];
            $savedarrayvals = [];

            foreach ($vals as $val) {
                $temp = explode("=", $val);
                $findme = 'chem';
                $pos = strpos($temp[0], $findme);

                if ($pos === false) {
                    // Not a 'chem' value
                    $savedvals[$temp[0]] = $temp[1];
                    // Check for 'array' keyword
                    if (strpos($temp[0], 'array') !== false) {
                        if ($temp[0] === 'trxCluster') {
                            $savedvals[$temp[0]] = $temp[1];
                        } else {
                            $savedarrayvals[] = $temp[1];
                        }
                    }
                } else {
                    // Handling 'chem' values
                    if ($temp[0] === 'colorScheme') {
                        $savedvals[$temp[0]] = $temp[1];
                    } else {
                        $savedchemvals[] = $temp[1];
                    }
                }
            }

            // Debugging
            if ($userid == 1 && $debug == 1) {
                analyze($savedarrayvals);
            }
        }
    } catch (Exception $e) {
        echo "Error fetching saved query: " . $e->getMessage();
    }

} else {
    // This is not a saved query

    // Get the max query number in savedqueries table
    try {
        $sql = "SELECT MAX(query) FROM savedqueries";
        $sqlResult = $db->Execute($sql);
        $row = $sqlResult->FetchRow();
        $tempquery = $row[0] ?? 1;
        if ($tempquery !== "NULL") {
            $tempquery += 1;
        }
    } catch (Exception $e) {
        echo "Error fetching max query: " . $e->getMessage();
    }
}

?>
