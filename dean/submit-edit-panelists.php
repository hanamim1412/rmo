<?php
// submit-edit-panelists.php
session_start();
require '../config/connect.php';
include '../messages.php';

if (!isset($_POST['tw_form_id'], $_POST['form_type'], $_POST['panelist_ids'], $_POST['chairman_id'])) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid form submission.'];
    header("Location: tw-forms.php");
    exit();
}

$tw_form_id = (int)$_POST['tw_form_id']; 
$form_type = $_POST['form_type'];         
$panelist_ids = $_POST['panelist_ids']; 
$chairman_id = $_POST['chairman_id'];  

function updateAssignedPanelist($conn, $tw_form_id, $panelist_id) {
    
    $query = "SELECT * FROM assigned_panelists WHERE tw_form_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $tw_form_id, $panelist_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    
    if ($result->num_rows > 0) {
        
        $updateQuery = "UPDATE assigned_panelists SET is_selected = 1, date_created = NOW() WHERE tw_form_id = ? AND user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $tw_form_id, $panelist_id);
        $updateStmt->execute();
    } else {
        $insertQuery = "INSERT INTO assigned_panelists (tw_form_id, user_id, is_selected, date_created) 
                        VALUES (?, ?, 1, NOW())";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ii", $tw_form_id, $panelist_id);
        $insertStmt->execute();
    }
}

function updateAssignedChairman($conn, $tw_form_id, $chairman_id) {
    
    $query = "SELECT * FROM assigned_chairman WHERE tw_form_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $tw_form_id, $chairman_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        
        $updateQuery = "UPDATE assigned_chairman SET is_selected = 1, date_created = NOW() WHERE tw_form_id = ? AND user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $tw_form_id, $chairman_id);
        $updateStmt->execute();
    } else {
        
        $insertQuery = "INSERT INTO assigned_chairman (tw_form_id, user_id, is_selected, date_created) 
                        VALUES (?, ?, 1, NOW())";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ii", $tw_form_id, $chairman_id);
        $insertStmt->execute();
    }
}

// Begin transaction
$conn->begin_transaction();
try {
    foreach ($panelist_ids as $panelist_id) {
        updateAssignedPanelist($conn, $tw_form_id, $panelist_id);  
    }

    updateAssignedChairman($conn, $tw_form_id, $chairman_id);  

    $conn->commit();
    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Panelists and Chairman successfully updated.'];
} catch (Exception $e) {

    $conn->rollback();
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'An error occurred while updating panelists and chairman: ' . $e->getMessage()];
}

$redirectPages = [
    'twform_1' => 'tw-form1-details.php',
    'twform_2' => 'tw-form2-details.php',
    'twform_3' => 'tw-form3-details.php',
    'twform_4' => 'tw-form4-details.php',
    'twform_5' => 'tw-form5-details.php',
    'twform_6' => 'tw-form6-details.php'
];
$redirectPage = $redirectPages[$form_type] ?? 'tw-forms.php';

header("Location: $redirectPage?tw_form_id=$tw_form_id");
exit();
?>
