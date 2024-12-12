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
    
    if ($department_id == 0) {
        echo "Invalid department_id.";
        return [];
    }

    $query = "SELECT user_id, firstname, lastname 
              FROM accounts 
              WHERE department_id = ? AND user_type = 'panelist'";

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
        <form id="assign_panelists" method="POST" action="submit-assign-panelists.php" class="form-container">
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

            <h2>Assign Panelists</h2>
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">
            <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($tw_form_id) ?>">
            <input type="hidden" name="department_id" value="<?= htmlspecialchars($department_id) ?>">

            <?php if ($department_id > 0): ?>
                <!-- Panelist Dropdowns for Panels -->
                <div class="form-group col-md-6">
                    <label for="panelist_id">Panel 1</label>
                    <select name="panelist_id[]" class="form-control form-select" required>
                        <option value="">Select Panelist</option>
                        <?php foreach ($panelists as $panelist): ?>
                            <option value="<?= htmlspecialchars($panelist['user_id']) ?>">
                                <?= htmlspecialchars($panelist['firstname'] . ' ' . $panelist['lastname']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="panelist_id">Panel 2</label>
                    <select name="panelist_id[]" class="form-control form-select" required>
                        <option value="">Select Panelist</option>
                        <?php foreach ($panelists as $panelist): ?>
                            <option value="<?= htmlspecialchars($panelist['user_id']) ?>">
                                <?= htmlspecialchars($panelist['firstname'] . ' ' . $panelist['lastname']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="panelist_id">Panel 3</label>
                    <select name="panelist_id[]" class="form-control form-select" required>
                        <option value="">Select Panelist</option>
                        <?php foreach ($panelists as $panelist): ?>
                            <option value="<?= htmlspecialchars($panelist['user_id']) ?>">
                                <?= htmlspecialchars($panelist['firstname'] . ' ' . $panelist['lastname']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="panelist_id">Panel 4</label>
                    <select name="panelist_id[]" class="form-control form-select" required>
                        <option value="">Select Panelist</option>
                        <?php foreach ($panelists as $panelist): ?>
                            <option value="<?= htmlspecialchars($panelist['user_id']) ?>">
                                <?= htmlspecialchars($panelist['firstname'] . ' ' . $panelist['lastname']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Assign Panelists</button>
            <?php else: ?>
                <p>No department found for the selected form. Please check the form details.</p>
            <?php endif; ?>
        </form>
        </div>
    </div>
</section>

<script>
    // document.addEventListener('DOMContentLoaded', () => {
    //     const departmentSelect = document.getElementById('department_select');
        
    //     departmentSelect.addEventListener('change', () => {
    //         const departmentId = departmentSelect.value;
    //         if (departmentId) {
    //             window.location.href = `assign-panelists.php?department_id=${departmentId}`;
    //         }
    //     });
    // });
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