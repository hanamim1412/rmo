<?php
// dean/assign-panelists.php
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
$title = "Assign Panelists";

$tw_form_id = isset($_GET['tw_form_id']) ? (int) $_GET['tw_form_id'] : 0;
function getFormTypeByTwFormId($tw_form_id) {
    global $conn; 

    $query = "SELECT form_type FROM tw_forms WHERE tw_form_id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Database query failed: " . $conn->error);
    }

    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['form_type'];
    }

    return null;
}
function getDepartmentIdFromTwForm($tw_form_id) {
    global $conn;
    
    if ($tw_form_id == 0) {
        echo "Invalid tw_form_id.";
        return 0;
    }
    
    $query = "SELECT department_id FROM tw_forms WHERE tw_form_id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $tw_form_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row['department_id'];
    } else {
        echo "No department found for tw_form_id: $tw_form_id";
        return 0;  
    }
}

function getPanelists($department_id) {
    global $conn;
    
    $query = "SELECT user_id, firstname, lastname 
          FROM accounts 
          WHERE department_id = ? AND user_type = 'panelist'
          ORDER BY firstname, lastname";
              
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $department_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $panelists = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $panelists[] = $row;
    }

    return $panelists;
}


if ($tw_form_id > 0) {
    $department_id = getDepartmentIdFromTwForm($tw_form_id);
    $panelists = ($department_id > 0) ? getPanelists($department_id) : [];
    $form_type = getFormTypeByTwFormId($tw_form_id);
} else {
    $department_id = 0;
    $panelists = [];
}
?>
<section id="assign-panelists-form">
    <div class="header-container">
        <h4 class="text-left">Assign Panelists Form</h4>
    </div>
    <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
        <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
        Back
    </a>

    <div class="container">
        <div class="register-box">
            <form id="assign_panelists" method="POST" action="submit-panelists.php" class="form-container">
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="alert alert-<?= htmlspecialchars($message['tags']) ?> alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i><?= htmlspecialchars($message['content']) ?>
                            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($tw_form_id) ?>">
                <input type="hidden" name="form_type" value="<?= htmlspecialchars($form_type) ?>">

                <?php if (!empty($panelists)): ?>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="form-group">
                            <label for="panelist_id_<?= $i ?>">Panelist <?= $i ?></label>
                            <select name="panelist_ids[]" id="panelist_id_<?= $i ?>" class="form-control form-select" required>
                                <option value="">Select Panelist</option>
                                <?php foreach ($panelists as $panelist): ?>
                                    <option value="<?= htmlspecialchars($panelist['user_id']) ?>">
                                        <?= htmlspecialchars($panelist['firstname'] . ' ' . $panelist['lastname']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endfor; ?>
                    <div class="form-group">
                        <label for="assignment_notes">Notes (Optional)</label>
                        <textarea name="comments" id="comment" class="form-control" rows="3" placeholder="Enter Additional notes"></textarea>
                    </div>               
                    <button type="submit" class="btn btn-primary">Assign Panelists</button>
                <?php else: ?>
                    <p>No panelists are available for this department. Please contact the administration.</p>
                <?php endif; ?>
            </form>

        </div>
    </div>
</section>

<script>
    document.getElementById('assign_panelists').addEventListener('submit', function(event) {
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