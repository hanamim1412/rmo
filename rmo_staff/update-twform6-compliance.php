<?php
session_start();
require '../config/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'rmo_staff') {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Unauthorized access.'];
    header("Location: tw-form6-details.php");
    exit();
}

if (!isset($_POST['tw_form_id']) || !isset($_POST['documents'])) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid request. Missing data.'];
    header("Location: tw-form6-details.php?tw_form_id=" . ($_POST['tw_form_id'] ?? ''));
    exit();
}

$tw_form_id = intval($_POST['tw_form_id']);
$checked_by = $_SESSION['user_id']; 
$selected_documents = $_POST['documents'];
$all_documents = [
    "Certificate of Conformity",
    "Certificate of Data Gathering",
    "Certificate of Similarity",
    "Article Repository"
];

$checkQuery = "SELECT tw_form_id FROM twform_6 WHERE tw_form_id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("i", $tw_form_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Error: TW Form 6 does not exist.'];
    header("Location: tw-form6-details.php");
    exit();
}

foreach ($all_documents as $doc) {
    $is_checked = in_array($doc, $selected_documents) ? 1 : 0;

    $query = "
        INSERT INTO twform_6_compliance (tw_form_id, document_name, is_checked, checked_by) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE is_checked = VALUES(is_checked), checked_by = VALUES(checked_by)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isii", $tw_form_id, $doc, $is_checked, $checked_by);
    $stmt->execute();
}

$_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Compliance documents updated successfully.'];
header("Location: tw-form6-details.php?tw_form_id=" . $tw_form_id);
exit();
?>
