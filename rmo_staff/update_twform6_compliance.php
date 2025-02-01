<?php
session_start();
require '../config/connect.php';
include '../messages.php';

if (!isset($_POST['tw_form_id'], $_POST['documents'])) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid form submission.'];
    header("Location: twform-6-details.php?tw_form_id=" . $_POST['tw_form_id']);
    exit();
}

$tw_form_id = (int) $_POST['tw_form_id'];
$checked_by = $_SESSION['user_id']; 
$selected_documents = $_POST['documents'];

$conn->begin_transaction();
try {
    $conn->query("DELETE FROM twform_6_compliance WHERE tw_form_id = $tw_form_id");

    foreach ($selected_documents as $doc) {
        $query = "INSERT INTO twform_6_compliance (tw_form_id, document_name, is_checked, checked_by) VALUES (?, ?, 1, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isi", $tw_form_id, $doc, $checked_by);
        $stmt->execute();
    }

    $conn->commit();
    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Compliance documents updated successfully.'];
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Failed to update compliance documents.'];
}

header("Location: twform-6-details.php?tw_form_id=$tw_form_id");
exit();
?>
