<?php
session_start();
require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tw_form_id'])) {
    $tw_form_id = (int) $_POST['tw_form_id'];

    $query = "SELECT overall_status FROM TW_FORMS WHERE tw_form_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $form = $result->fetch_assoc();
        if ($form['overall_status'] === 'pending') {
            
            $deleteQuery = "DELETE FROM TW_FORMS WHERE tw_form_id = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $tw_form_id);
            if ($deleteStmt->execute()) {
                $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Form deleted successfully.'];
            } else {
                $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Failed to delete form.'];
            }
        } else {
            $_SESSION['messages'][] = ['tags' => 'warning', 'content' => 'Form cannot be deleted as it is not pending.'];
        }
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Form not found.'];
    }
}
header("Location: tw-forms.php");
exit();
