<?php
// Start session
session_start();

// Secure session handling - regenerate session to avoid session fixation attacks
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Include necessary files
require 'edge_db_connect2.php';
require 'utilityfunctions.inc';
include 'edge_update_user_activity.inc';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    die('Sorry, you are not logged in. This area is restricted to registered members. 
        <a href="./login.php">Click here to go to the login page</a>');
}

$cssclass = "tundra";
$userid = $_SESSION['userid'] ?? ""; // Null coalescing operator in case 'userid' is not set

// Set debugging option for admin users
$dojodebugval = ($userid == 1) ? ", isDebug: true" : "";

// Sanitize inputs for later use (Updated for PHP 8)
$listid = filter_input(INPUT_GET, 'listid', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$openedit = filter_input(INPUT_GET, 'openedit', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "false";

// Initialize variables
$genelist = "";
$version = "";

// Enable ADOdb exception handling (Optional)
$db->SetFetchMode(ADODB_FETCH_ASSOC);  // Fetch associative array for better readability

try {
    // Ensure $listid is valid
    if ($listid) {
        // Use prepared statement to safely query the database
        $getgenelistSQL = "SELECT featurenums, public, userid, arraytype FROM genelist WHERE listid = ?";
        $getgenelistResult = $db->Execute($getgenelistSQL, array($listid));

        if (!$getgenelistResult) {
            // Throw an exception if no result is returned
            throw new Exception("No result returned for listid: $listid");
        }

        while ($genelistRow = $getgenelistResult->FetchRow()) {
            $genelistFeaturenums = $genelistRow['featurenums'];
            $genelistIsPublic = $genelistRow['public'];
            $genelistUserid = $genelistRow['userid'];
            $arraytype = $genelistRow['arraytype'];

            if ($genelistIsPublic == 1 || $genelistUserid == $userid) {
                $genelist = $genelistFeaturenums;
            } else {
                die("You have entered an inappropriate list ID.");
            }
        }

        // Get version from the array type
        $sql = "SELECT version FROM agilentarrays WHERE id = ?";
        $Result = $db->Execute($sql, array($arraytype));

        if (!$Result) {
            // Throw an exception if no result is returned
            throw new Exception("No version found for arraytype: $arraytype");
        }

        if ($row = $Result->FetchRow()) {
            $version = strtolower($row['version']);
        }
    }

    // Handle case where no listid was provided
    if (!$genelist) {
        throw new Exception("No valid genelist found for the provided listid.");
    }

    $genes = explode(",", $genelist);

} catch (Exception $e) {
    // Handle exceptions (logging can be added here)
    die("Error: " . $e->getMessage());
}

// HTML Starts Here
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
    <link rel="stylesheet" type="text/css" href="./css/newlayout.css" title="layout" />
    <title>EDGE^3</title>
    <script type="text/javascript" src="./dojo-release-1.0.0/dojo/dojo.js"
            djConfig="parseOnLoad: true<?php echo $dojodebugval; ?>"></script>
    <script type="text/javascript">
        dojo.require("dojo.parser");
        dojo.require("dijit.layout.LayoutContainer");
        dojo.require("dijit.TitlePane");
    </script>
</head>
<body>
    <div class="header">
        <img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left">
        <h4><font face="arial">Environment, Drugs, and Gene Expression</font></h4>
    </div>
    <div>
        <br> Here is the list ID: <?php echo htmlspecialchars($listid, ENT_QUOTES, 'UTF-8'); ?><br>

        <form enctype="multipart/form-data" name="genelistupdate" action="./importgenelist.php?openedit=<?php echo htmlspecialchars($openedit, ENT_QUOTES, 'UTF-8'); ?>" method="post">
            <table width="600">
                <tr>
                    <td><b>Include?</b></td>
                    <td><b>Gene Symbol</b></td>
                    <td><b>Feature Number</b></td>
                    <td><b>Systematic Name</b></td>
                    <td><b>Simple Description</b></td>
                </tr>

                <?php
                // Loop through genes and retrieve data from database
                foreach ($genes as $gene) {
                    $gene = trim($gene);
                    $sql = "SELECT GeneSymbol, SystematicName, SimpleDescription FROM agilent{$version}_extendedannotations WHERE FeatureNum = ?";
                    $Result = $db->Execute($sql, array($gene));

                    if (!$Result) {
                        throw new Exception("No result returned for FeatureNum: $gene");
                    }
					$i = 0;
                    while ($row = $Result->FetchRow()) {
                        $class = ($i % 2 == 0) ? "class=\"questionparameter\"" : "class=\"questionparameter2\"";
                        ?>
                        <tr>
                            <td <?php echo $class; ?>>
                                <input type="checkbox" name="<?php echo "feature" . htmlspecialchars($gene, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars($gene, ENT_QUOTES, 'UTF-8'); ?>" checked>
                            </td>
                            <td <?php echo $class; ?>><?php echo htmlspecialchars($row['GeneSymbol'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td <?php echo $class; ?>><?php echo htmlspecialchars($gene, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td <?php echo $class; ?>><?php echo htmlspecialchars($row['SystematicName'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td <?php echo $class; ?>><?php echo htmlspecialchars($row['SimpleDescription'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <?php
						$i++;
                    }
                }

                // Get list details
                $getgenelistSQL = "SELECT name, listdesc, public, arraytype FROM genelist WHERE listid = ?";
                $getgenelistResult = $db->Execute($getgenelistSQL, array($listid));

                if (!$getgenelistResult) {
                    throw new Exception("No list details returned for listid: $listid");
                }

                $rowinfo = $getgenelistResult->FetchRow();
                $name = htmlspecialchars($rowinfo['name'], ENT_QUOTES, 'UTF-8');
                $listdesc = htmlspecialchars($rowinfo['listdesc'], ENT_QUOTES, 'UTF-8');
                $public = $rowinfo['public'];
                $arraytype = $rowinfo['arraytype'];
                $checked = ($public == 1) ? "checked" : "";
                $size = max(strlen($name), 100);
                ?>
            </table>

            <table>
                <tr>
                    <td class="questionparameter2"><strong>Organism</strong></td>
                    <td class="questionparameter2">
                        <?php
                        $orgsql = "SELECT organism FROM agilentarrays WHERE id = ?";
                        $orgresult = $db->Execute($orgsql, array($arraytype));

                        if (!$orgresult) {
                            throw new Exception("No organism information returned for arraytype: $arraytype");
                        }

                        $org = $orgresult->FetchRow();
                        echo htmlspecialchars($org['organism'], ENT_QUOTES, 'UTF-8');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="questionparameter2"><strong>List Name</strong></td>
                    <td class="questionparameter2">
                        <input name="name" type="text" value="<?php echo $name; ?>" size='<?php echo $size; ?>'>
                    </td>
                </tr>
                <tr>
                    <td class="questionparameter2"><strong>List Description</strong></td>
                    <td class="questionparameter2">
                        <textarea name="desc" cols="80" rows="6"><?php echo $listdesc; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="questionparameter2"><strong>Public</strong></td>
                    <td class="questionparameter2">
                        <input name="public" type="checkbox" value="1" <?php echo $checked; ?>>
                    </td>
                </tr>

                <input type="hidden" name="imported" value="true">
                <input type="hidden" name="thisorganism" value="<?php echo htmlspecialchars($arraytype, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="updated" value="true">
                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="oldlistid" value="<?php echo htmlspecialchars($listid, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="featurenums" value="<?php echo htmlspecialchars($genelist, ENT_QUOTES, 'UTF-8'); ?>'">

                <tr>
                    <td class="questionparameter2"><input type="submit" name="update" value="Update"></td>
                    <td class="questionparameter2"><input type="reset" value="Reset Form"></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>
