<?php
session_start();
require '../config/connect.php';
include '../messages.php';

if (!isset($_POST['tw_form_id'], $_POST['form_type'], $_POST['panelist'], $_POST['chairman'])) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid form submission.'];
    header("Location: assign-panelists.php");
    exit();
}

$tw_form_id = (int)$_POST['tw_form_id'];
$form_type = $_POST['form_type'];
$panelist_names = $_POST['panelist'];
$chairman_name = $_POST['chairman'];

function getUserIdByName($conn, $name, $user_type) {
    $query = "SELECT user_id FROM accounts WHERE CONCAT(firstname, ' ', lastname) = ? AND user_type = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $name, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['user_id'];
    }
    return null;
}

$panelist_ids = [];
foreach ($panelist_names as $panelist_name) {
    $panelist_id = getUserIdByName($conn, $panelist_name, 'panelist');
    if ($panelist_id) {
        $panelist_ids[] = $panelist_id;
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Panelist '$panelist_name' not found."];
        header("Location: assign-panelists.php?tw_form_id=$tw_form_id");
        exit();
    }
}

$chairman_id = getUserIdByName($conn, $chairman_name, 'chairman');
if (!$chairman_id) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Chairman '$chairman_name' not found."];
    header("Location: assign-panelists.php?tw_form_id=$tw_form_id");
    exit();
}

$conn->begin_transaction();
try {
    foreach ($panelist_ids as $panelist_id) {
        $query = "INSERT INTO assigned_panelists (tw_form_id, user_id, is_selected, date_created) VALUES (?, ?, 1, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $tw_form_id, $panelist_id);
        $stmt->execute();
    }
    
    $query = "INSERT INTO assigned_chairman (tw_form_id, user_id, is_selected, date_created) VALUES (?, ?, 1, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $tw_form_id, $chairman_id);
    $stmt->execute();
    
    $conn->commit();
    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Panelists and Chairman successfully assigned.'];
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'An error occurred while assigning panelists and chairman.'];
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
