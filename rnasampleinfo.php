<?php
/**
 * rnasampleinfo.php
 * 
 * This script takes in from GET a sampleid and queries the database to get information
 * and puts it in an html table
 * Functions used fro utilityfunctions.inc: fetchRelatedData
 * @author Aaron Vollrath <aaron.vollrath@gmail.com>
 * @version 1.0
 * @package not defined
 */

declare(strict_types=1); // Enable strict typing for type safety
session_start();

// Include necessary files and database connection
require 'edge_db_connect2.php';
include 'edge3header.inc';
include 'utilityfunctions.inc';

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    die("You need to login to use this function.");
}

// Ensure a valid sample ID is passed and sanitize the input
$sampleid = $_GET['sampleid'] ?? null;
if ($sampleid === null || !is_numeric($sampleid)) {
    die("Not a valid sample ID. </body>");
}

// Main logic wrapped in a try-catch block for error handling
try {
    // Fetch RNA sample data securely
    $sampleSQL = "SELECT * FROM agilent_rnasample WHERE sampleid = ?";
    $sampleResult = $db->Execute($sampleSQL, [$sampleid]);
    $sampleData = $sampleResult->FetchRow();

    // Check if sample data exists
    if (!$sampleData) {
        die("Sample not found.");
    }

    // Extract sample data into individual variables
    extract($sampleData);

    // Fetch file count associated with the sample
    $fileCountSQL = "SELECT COUNT(*) AS file_count FROM datafiles WHERE edgeobject = 0 AND edgeid = ?";
    $fileCountResult = $db->Execute($fileCountSQL, [$sampleid]);
    $fileCount = $fileCountResult->fields['file_count'] ?? 0;

    // Fetch related data (strain, gene variation, organism, tissue, etc.)
    $strain = fetchRelatedData('strain', 'strain', 'strainid', $strain, $db);
    $genevariation = fetchRelatedData('genevariation', 'genevariation', 'genevariationid', $genevariation, $db);
    $organism = fetchRelatedData('organism', 'organism', 'organismid', $organism, $db);
    $tissue = fetchRelatedData('tissue', 'tissue', 'tissueid', $tissue, $db);
    $treatment = fetchRelatedData('chem', 'chemical', 'chemid', $treatment, $db);
    $vehicle = fetchRelatedData('vehicle', 'vehicle', 'vehicleid', $vehicle, $db);
    $ageunit = fetchRelatedData('ageunit', 'ageunit', 'ageunitid', $ageunits, $db);
    $durationunits = fetchRelatedData('durationunit', 'durationunit', 'durationunitid', $durationunits, $db);
    $route = fetchRelatedData('route', 'route', 'routeid', $route, $db);
    $doseunits = fetchRelatedData('doseunit', 'doseunit', 'doseunitid', $doseunits, $db);

} catch (Exception $e) {
    die("Error fetching data: " . htmlspecialchars($e->getMessage() ?? '', ENT_QUOTES));
}

?>
<body onload="fixwindow(800, 800)">
    <div class="header">
        <img src="./GIFs/edgebanner.jpg" alt="environment" width="45" height="38" align="left">
        <h4><font face="arial">Environment, Drugs and Gene Expression^3</font></h4>
    </div>
    <br><br><br>

    <h4>RNA Sample Details</h4>

    <p>Edit this RNA Sample?  
        <a href="agilentexperiment-useradmin.php?rnasubmission=1&sampleid=<?php echo htmlspecialchars((string)$sampleid, ENT_QUOTES); ?>">EDIT</a>
    </p>

    <table id="results">
        <tr><td>RNA Sample ID#</td><td><?php echo htmlspecialchars((string)$sampleid, ENT_QUOTES); ?></td></tr>
        <tr><td>RNA Sample Name</td><td><?php echo htmlspecialchars($samplename ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Organism</td><td><?php echo htmlspecialchars($organism ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>RNA Group Size</td><td><?php echo htmlspecialchars((string)$rnagroupsize ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>RNA Concentration</td><td><?php echo htmlspecialchars((string)$concentration ?? '', ENT_QUOTES); ?> ng/ul</td></tr>
        <tr><td>Strain</td><td><?php echo htmlspecialchars($strain ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Genetic Variation</td><td><?php echo htmlspecialchars($genevariation ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Age</td><td><?php echo htmlspecialchars((string)$age ?? '', ENT_QUOTES); ?> <?php echo htmlspecialchars($ageunit ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Sex</td><td><?php echo htmlspecialchars($sex ?? '', ENT_QUOTES); ?></td></tr>
        <?php if ($sex === 'F'): ?>
        <tr><td>Pregnant</td><td><?php echo htmlspecialchars($pregnant ?? '', ENT_QUOTES); ?></td></tr>
        <?php endif; ?>
        <?php if ($gestationperiod != -1): ?>
        <tr><td>Gestation Period</td><td><?php echo htmlspecialchars((string)$gestationperiod ?? '', ENT_QUOTES); ?></td></tr>
        <?php endif; ?>
        <tr><td>Tissue</td><td><?php echo htmlspecialchars($tissue ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Condition/Treatment</td><td><?php echo htmlspecialchars($treatment ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Vehicle</td><td><?php echo htmlspecialchars($vehicle ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Dose</td><td><?php echo htmlspecialchars((string)$dose ?? '', ENT_QUOTES); ?> <?php echo htmlspecialchars($doseunits ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Route</td><td><?php echo htmlspecialchars($route ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Experiment Start/Dosage Time</td><td><?php echo htmlspecialchars($dosagetime ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Experiment End/Harvest Time</td><td><?php echo htmlspecialchars($harvesttime ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Experiment Duration</td><td><?php echo htmlspecialchars((string)$duration ?? '', ENT_QUOTES); ?> <?php echo htmlspecialchars($durationunits ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Date Submitted</td><td><?php echo htmlspecialchars($datesubmitted ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Date Processed</td><td><?php echo htmlspecialchars($dateprocessed ?? '', ENT_QUOTES); ?></td></tr>
        <tr><td>Submitter</td><td><?php echo htmlspecialchars($submitter ?? '', ENT_QUOTES); ?></td></tr>
		<tr><td>Queue Status</td><td><?php echo htmlspecialchars(strval($queuestatus), ENT_QUOTES); ?></td></tr>
        <tr><td>Sample Information</td><td><?php echo htmlspecialchars($info ?? '', ENT_QUOTES); ?></td></tr>
    </table>

    <?php if ($fileCount > 0): ?>
    <h4>Associated Files</h4>
    <table id="results">
        <tr><td><b>File</b></td><td><b>File type</b></td><td><b>Description</b></td></tr>
        <?php
        // Fetch file details and display them
        $fileSQL = "SELECT fileid, filetype, description FROM datafiles WHERE edgeobject = 0 AND edgeid = ?";
        $fileResult = $db->Execute($fileSQL, [$sampleid]);
        $fileNum = 0;
        while ($fileRow = $fileResult->FetchRow()) {
            $fileNum++;
            echo "<tr><td><a href='downloadfile.php?filenum=" . htmlspecialchars((string)$fileRow['fileid'], ENT_QUOTES) . "' target='_blank'>File #{$fileNum}</a></td>
                  <td>" . htmlspecialchars($fileRow['filetype'] ?? '', ENT_QUOTES) . "</td>
                  <td>" . htmlspecialchars($fileRow['description'] ?? '', ENT_QUOTES) . "</td></tr>";
        }
        ?>
    </table>
    <?php endif; ?>
</body>

<?php


?>
