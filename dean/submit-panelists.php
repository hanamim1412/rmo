<?php
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

error_log("Panelist IDs: " . implode(', ', $panelist_ids));  
error_log("Chairman ID: " . $chairman_id);

function assignPanelist($conn, $tw_form_id, $panelist_id) {
    $query = "INSERT INTO assigned_panelists (tw_form_id, user_id, is_selected, date_created) 
              VALUES (?, ?, 1, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $tw_form_id, $panelist_id);  
    if (!$stmt->execute()) {
        error_log("Error executing panelist insert query: " . $stmt->error);
        throw new Exception("Error executing panelist insert query: " . $stmt->error);
    }
}

function assignChairman($conn, $tw_form_id, $chairman_id) {
    $query = "INSERT INTO assigned_chairman (tw_form_id, user_id, is_selected, date_created) 
              VALUES (?, ?, 1, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $tw_form_id, $chairman_id);
    if (!$stmt->execute()) {
        error_log("Error executing chairman insert query: " . $stmt->error);
        throw new Exception("Error executing chairman insert query: " . $stmt->error);
    }
}

$conn->begin_transaction();
try {
    foreach ($panelist_ids as $panelist_id) {
        assignPanelist($conn, $tw_form_id, $panelist_id);  
    }

    assignChairman($conn, $tw_form_id, $chairman_id);  

    $conn->commit();
    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Panelists and Chairman successfully assigned.'];
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'An error occurred: ' . $e->getMessage()];
    header("Location: tw-forms.php?tw_form_id=$tw_form_id");
    exit();
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
