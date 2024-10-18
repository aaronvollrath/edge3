<?php
session_start();
$debug = -1; // set to 1 for debug statements
require 'edge_db_connect2.php';
require 'utilityfunctions.inc';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    die('Sorry, you are not logged in. This area is restricted to registered users. <a href="./login.php">Click here to go to the login page</a>');
}

$userid = $_SESSION['userid'];

// Get the username from the session
$username = $_SESSION['username'] ?? '';

// Use prepared statements to fetch the user ID securely
$sql = "SELECT id FROM users WHERE username = ?";
$sqlResult = $db->Execute($sql, array($username));

if ($sqlResult === false) {
    echo "Database error when checking username. Please contact the administrator.<br>";
    echo "Error message: " . $db->ErrorMsg();
    die();
}

$row = $sqlResult->FetchRow();
$userid = $row['id'] ?? null;

if ($userid === null) {
    die("User not found.");
}

if ($userid == 1 && $debug == 1) {
    echo "SQL: $sql<br>";
    echo "The user ID is: $userid<br>";
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
    <link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout" />
    <title>EDGE^3</title>
</head>
<body>
    <div class="header">
        <img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left" />
        <h4><font face="arial">Environment, Drugs and Gene Expression</font></h4>
    </div>
    <br>
<p>
<?php
include "formcheck.inc";

// Debugging
if ($userid == 1 && $debug == 1) {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    echo "<hr>Referer: $referer<br>";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Sanitize inputs
    $tempquery = filter_input(INPUT_POST, 'tempquerynum', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $querynum = filter_input(INPUT_POST, 'querynum', FILTER_SANITIZE_NUMBER_INT);
    $queryurltype = filter_input(INPUT_POST, 'queryurltype', FILTER_SANITIZE_URL);
    $desc = filter_input(INPUT_POST, 'desc', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $public = filter_input(INPUT_POST, 'public', FILTER_SANITIZE_NUMBER_INT) ?? 0;

    // Start a transaction
    $db->StartTrans();

    try {
        // If the action is to save the query
        if ($_POST['action'] === "save") {
            $sql = "UPDATE savedqueries SET queryname = ?, queryurltype = ?, public = ?, querydesc = ? WHERE query = ?";
            $sqlResult = $db->Execute($sql, array($name, $queryurltype, $public, $desc, $querynum));

            if (!$sqlResult) {
                throw new Exception("Failed to save the query.");
            }

            $name .= " Saved";

        } else { // Replacing an existing query
            $tempquery = filter_input(INPUT_POST, 'tempquerynum', FILTER_SANITIZE_NUMBER_INT);

            // Fetch the query details for update
            $sql = "SELECT * FROM savedqueries WHERE query = ?";
            $sqlResult = $db->Execute($sql, array($tempquery));
            $row = $sqlResult->FetchRow();

            if (!$row) {
                throw new Exception("Could not retrieve the query details.");
            }

            $query1 = $row['query1'];
            $query2 = $row['query2'];
            $query2opts = $row['query2opts'];
            $public = $row['public'];

            $savedquery = filter_input(INPUT_POST, 'querynum', FILTER_SANITIZE_NUMBER_INT);

            // Update the existing query
            $sql = "UPDATE savedqueries SET queryname = ?, query1 = ?, query2 = ?, query2opts = ?, queryurltype = ?, public = ?, querydesc = ? WHERE query = ?";
            $sqlResult = $db->Execute($sql, array($name, $query1, $query2, $query2opts, $queryurltype, $public, $desc, $savedquery));

            if (!$sqlResult) {
                throw new Exception("Failed to update the saved query.");
            }

            // Fetch the updated query name
            $sql = "SELECT queryname FROM savedqueries WHERE query = ?";
            $sqlResult = $db->Execute($sql, array($savedquery));
            $row = $sqlResult->FetchRow();
            $name = "Query " . $row['queryname'] . " Updated";
        }

        // Commit the transaction
        $db->CompleteTrans();

    } catch (Exception $e) {
        // Rollback transaction in case of error
        $db->FailTrans();
        $db->CompleteTrans();
        echo "An error occurred: " . $e->getMessage();
        die();
    }
?>
    <table class="question" width="300">
        <thead>
        <tr>
            <th class="mainheader" colspan="2">Save Query</th>
        </tr>
        </thead>
        <tr class="question">
            <td class="questionparameter"><strong>Results:</strong></td>
            <td class="questionanswer"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    </table>
    <p class="close"><a href="javascript:window.close();"><img src="./GIFs/closebutton.gif" width="70" height="15" border="0"></a></p>

<?php
} else {
    $savedquery = filter_input(INPUT_GET, 'savedquery', FILTER_SANITIZE_NUMBER_INT);
    $tempquery = filter_input(INPUT_GET, 'tempquery', FILTER_SANITIZE_NUMBER_INT);
    $queryurltype = $_SERVER['HTTP_REFERER'] ?? '';

    if ($userid == 1 && $debug == 1) {
        echo "Query URL Type: $queryurltype<br>";
    }

    if (empty($savedquery)) {
        if ($userid == 1 && $debug == 1) {
            echo "You've chosen to save query# $tempquery<br>";
        }
?>
        <form name="order" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>">
            <table class="question" width="300">
                <thead>
                <tr>
                    <th class="mainheader" colspan="2">Save Query</th>
                </tr>
                </thead>
                <input name="queryurltype" type="hidden" value="<?php echo htmlspecialchars($queryurltype, ENT_QUOTES, 'UTF-8'); ?>">
                <tr class="question">
                    <td class="questionparameter"><strong>Query Name:</strong></td>
                    <td class="questionanswer">
                        <input name="name" type="text" value="<?php echo "Query" . htmlspecialchars($tempquery, ENT_QUOTES, 'UTF-8'); ?>" size="75" align="right" />
                    </td>
                    <input name="querynum" type="hidden" value="<?php echo htmlspecialchars($tempquery, ENT_QUOTES, 'UTF-8'); ?>">
                    <input name="action" type="hidden" value="save">
                </tr>
                <tr class="question">
                    <td class="questionparameter"><strong>Query Description:</strong></td>
                    <td class="questionanswer">
                        <textarea name="desc" cols="75" rows="10"></textarea>
                    </td>
                </tr>
                <tr>
                    <td><input type="submit" align="right" name="submit" value="Submit"></td>
                    <td><input type="reset" align="right" value="Reset Form"></td>
                </tr>
            </table>
        </form>
<?php
    } else {
        // Updating an existing query
        $sql = "SELECT queryname, querydesc, public FROM savedqueries WHERE query = ?";
        $result = $db->Execute($sql, array($savedquery));
        $row = $result->FetchRow();

        if (!$row) {
            die("Failed to retrieve query details.");
        }

        $queryname = $row['queryname'];
        $public = $row['public'];
        $desc = $row['querydesc'];
?>
        <form name="order" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>">
            <table class="question">
                <thead>
                <tr>
                    <th class="mainheader" colspan="2">Update Query</th>
                </tr>
                </thead>
                <tr class="question">
                    <input name="queryurltype" type="hidden" value="<?php echo htmlspecialchars($queryurltype, ENT_QUOTES, 'UTF-8'); ?>">
                    <td class="questionparameter"><strong>Query Name:</strong></td>
                    <td class="questionanswer">
                        <input name="name" type="text" value="<?php echo htmlspecialchars($queryname, ENT_QUOTES, 'UTF-8'); ?>" size="75" align="right" />
                    </td>
                    <input name="querynum" type="hidden" value="<?php echo htmlspecialchars($savedquery, ENT_QUOTES, 'UTF-8'); ?>">
                    <input name="tempquerynum" type="hidden" value="<?php echo htmlspecialchars($tempquery, ENT_QUOTES, 'UTF-8'); ?>">
                    <input name="action" type="hidden" value="replace">
                </tr>
                <tr class="question">
                    <td class="questionparameter"><strong>Query Description:</strong></td>
                    <td class="questionanswer">
                        <textarea name="desc" cols="75" rows="10"><?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><input type="submit" align="right" name="submit" value="Submit"></td>
                    <td><input type="reset" align="right" value="Reset Form"></td>
                </tr>
            </table>
        </form>
<?php
    }
}
?>
</p>
</body>
</html>
