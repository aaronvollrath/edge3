<?php
session_start();
require 'edge_db_connect2.php'; // Database connection setup

// Check if the user is logged in
if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];
    $page = "Your private gene lists";
} else {
    $userid = "";
    $page = "Public gene lists";
}

echo "<p><strong>" . htmlspecialchars($page, ENT_QUOTES, 'UTF-8') . "</strong></p>";
echo "Select one or more lists of the same organism below and click <i><b>Load Gene List(s)</b></i> to import the list. <font color='red'><b>Note:</b></font> Combining lists from different organisms will give unexpected results due to differences in feature numbers.<br><br>";
echo "To save the combination of lists as one new list, give a name, indicate if it is to be public, and click <i><b>Save Gene List(s)</b></i> button at the bottom.<br><br>";

try {
    // Fetch gene lists based on user status
    if ($userid != "") {
        $sql = "SELECT listid, name, listdesc, arraytype FROM genelist WHERE userid = ? ORDER BY arraytype";
        $result = $db->Execute($sql, array($userid));

        if (!$result) {
            throw new Exception("Database error when fetching gene lists: " . $db->ErrorMsg());
        }
    } else {
        $sql = "SELECT listid, name, listdesc, arraytype FROM genelist WHERE public = '1' ORDER BY arraytype";
        $result = $db->Execute($sql);

        if (!$result) {
            throw new Exception("Database error when fetching public gene lists: " . $db->ErrorMsg());
        }
    }

    // Check if any results were returned
    if ($result->RecordCount() == 0) {
        echo "You have no saved gene lists.";
    } else {
        ?>
        <form enctype="multipart/form-data" name="getgenes" action="" method="">
            <input type="hidden" name="importedbyuserid" value="true">
            <table>
            <?php
            $index = 0;
            $organismid = "";
            
            // Iterate through the gene lists
            while (!$result->EOF) {
                $listid = $result->fields[0];
                $name = htmlspecialchars($result->fields[1], ENT_QUOTES, 'UTF-8');
                $listdesc = htmlspecialchars($result->fields[2], ENT_QUOTES, 'UTF-8');
                $arraytype = $result->fields[3];

                // Fetch organism information based on arraytype
                if ($organismid != $arraytype) {
                    $orgSQL = "SELECT organism FROM agilentarrays WHERE id = ?";
                    $orgResult = $db->Execute($orgSQL, array($arraytype));

                    if (!$orgResult) {
                        throw new Exception("Database error when fetching organism: " . $db->ErrorMsg());
                    }

                    // Fetch organism details
                    while ($orgRow = $orgResult->FetchRow()) {
                        $organism = htmlspecialchars($orgRow[0], ENT_QUOTES, 'UTF-8');
                        ?>
                        <thead>
                        <tr>
                            <th colspan="3"><?php echo $organism; ?></th>
                        </tr>
                        </thead>
                        <tr><td><strong>Include?</strong></td><td><strong>List Name</strong></td><td><strong>List Description</strong></td></tr>
                        <?php
                    }
                    $organismid = $arraytype;
                }

                // Alternating row styles
                $csstdclass = ($index % 2 == 0) ? "d1" : "d0";
                ?>
                <tr>
                    <td class="<?php echo $csstdclass; ?>center"><input type="checkbox" name="<?php echo "list" . htmlspecialchars($listid, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars($listid, ENT_QUOTES, 'UTF-8'); ?>"></td>
                    <td class="<?php echo $csstdclass; ?>center"><font color='blue'><b><?php echo $name; ?></b></font></td>
                    <td colspan="2" width="500" class="<?php echo $csstdclass; ?>left"><i><?php echo $listdesc; ?></i></td>
                </tr>
                <?php
                $index++;
                $result->MoveNext();
            }
            ?>
            <tr>
                <td colspan="2"><input type="button" name="importgenelist" value="Load Gene List(s)" onclick="return updatefeaturenums();"></td>
                <td><input type="reset" value="Reset Form"></td>
            </tr>
            </table>

            <br><br>
            <table>
                <tr>
                    <td colspan="2">List Name</td>
                    <td><input name="newlistname" type="text"></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2">List Description</td>
                    <td><input name="newlistdesc" type="text"></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2">Public</td>
                    <td><input name="listispublic" type="radio" value="1"></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="button" name="importgenelist" value="Save Gene List(s)" onclick="return concatenateandsavelists();"></td>
                    <td><input type="reset" value="Reset Form"></td>
                    <td></td>
                </tr>
            </table>
        </form>
        <?php
    }
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
