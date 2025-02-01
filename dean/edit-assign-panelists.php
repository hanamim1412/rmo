<?php
// dean/edit-assign-panelists.php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}

include("dean-master.php");
require '../config/connect.php';
include '../messages.php';
ob_start();
$title = "Edit Assigned Panelists";

$tw_form_id = isset($_GET['tw_form_id']) ? (int)$_GET['tw_form_id'] : 0;

$form_type = '';
$department_id = 0; 

$query = "SELECT form_type, department_id FROM tw_forms WHERE tw_form_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Database query failed: " . $conn->error);
}

$stmt->bind_param("i", $tw_form_id);
$stmt->execute();
$stmt->bind_result($form_type, $department_id);
$stmt->fetch();
$stmt->close();

if (!$form_type || !$department_id) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid TW form ID. Redirecting to the main forms page.'];
    header("Location: tw-forms.php");
    exit();
}
function getAssignedPanelists($tw_form_id)
{
    global $conn;

    $query = "SELECT ap.user_id, a.firstname, a.lastname
              FROM assigned_panelists ap
              JOIN accounts a ON ap.user_id = a.user_id
              WHERE ap.tw_form_id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Database query failed: " . $conn->error);
    }

    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $assigned_panelists = [];
    while ($row = $result->fetch_assoc()) {
        $assigned_panelists[] = $row;
    }

    return $assigned_panelists;
}

function getAvailablePanelists($department_id)
{
    global $conn;

    $query = "SELECT user_id, firstname, lastname 
              FROM accounts 
              WHERE department_id = ? AND user_type = 'panelist'
              ORDER BY firstname, lastname";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Database query failed: " . $conn->error);
    }

    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $panelists = [];
    while ($row = $result->fetch_assoc()) {
        $panelists[] = $row;
    }

    return $panelists;
}

$assigned_panelists = getAssignedPanelists($tw_form_id);
$available_panelists = getAvailablePanelists($department_id);
?>

<section id="edit-panelists-form">
    <div class="header-container">
        <h4 class="text-left">Edit Assigned Panelists</h4>
    </div>
    <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
        <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
        Back
    </a>

    <div class="container">
        <div class="register-box">
            <form id="edit_assigned_panelists" method="POST" action="submit-edit-panelists.php" class="form-container">
                <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($tw_form_id) ?>">
                <input type="hidden" name="form_type" value="<?= htmlspecialchars($form_type) ?>">

                <?php if (!empty($available_panelists)): ?>
                    <?php foreach ($assigned_panelists as $index => $panelist): ?>
                        <div class="form-group">
                            <label for="panelist_id_<?= $index + 1 ?>">Panelist <?= $index + 1 ?></label>
                            <select name="panelist_ids[]" id="panelist_id_<?= $index + 1 ?>" class="form-control form-select" required>
                                <option value="">Select Panelist</option>
                                <?php foreach ($available_panelists as $available_panelist): ?>
                                    <option value="<?= htmlspecialchars($available_panelist['user_id']) ?>" 
                                        <?= $panelist['user_id'] == $available_panelist['user_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($available_panelist['firstname'] . ' ' . $available_panelist['lastname']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endforeach; ?>


                    <button type="submit" class="btn btn-primary">Update Panelists</button>
                <?php else: ?>
                    <p>No panelists are available for this department. Please contact the administration.</p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>

<script>
    document.getElementById('edit_assigned_panelists').addEventListener('submit', function(event) {
        const selectedOptions = Array.from(document.querySelectorAll('select[name="panelist_ids[]"]')).map(select => select.value);
        const uniqueOptions = new Set(selectedOptions);

        if (selectedOptions.length !== uniqueOptions.size) {
            event.preventDefault();
            alert('Each panelist must be unique. Please review your selections.');
        }
    });
</script>
<?php
$content = ob_get_clean();
include('dean-master.php');
?>
<style>
    .register-box {
        display: flex;
        background: white;
        padding: 20px 10px;
        border: 2px black solid;
        border-radius: 10px;
        width: 100%;
        max-width: 600px;
        display: flex;
        flex-direction: column;
        justify-content: center; 
        margin: auto;
    }
</style>