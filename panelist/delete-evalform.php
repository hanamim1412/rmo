<?php
// panelist/delete-evalform.php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}

require '../config/connect.php';

$tw_form_id = isset($_POST['tw_form_id']) ? (int)$_POST['tw_form_id'] : 0;
$form_type = isset($_POST['form_type']) ? $_POST['form_type'] : '';

if ($tw_form_id <= 0 || empty($form_type)) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Invalid evaluation form ID or form type"];
    if ($form_type == 'twform_3') {
        header("Location: tw-form3-details.php?tw_form_id=$tw_form_id");
    } elseif ($form_type == 'twform_5') {
        header("Location: tw-form5-details.php?tw_form_id=$tw_form_id");
    } else {
        header("Location: tw-forms.php?tw_form_id=$tw_form_id");
    }
    exit();
}

try {
    $query = "DELETE FROM eval_criteria WHERE tw_form_id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['messages'][] = ['tags' => 'success', 'content' => "Evaluation form deleted successfully"];
    } else {
        $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "No evaluation form found to delete"];
    }

    $stmt->close();
} catch (Exception $e) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "An error occurred: " . $e->getMessage()];
}

if ($form_type == 'twform_3') {
    header("Location: tw-form3-details.php?tw_form_id=$tw_form_id");
} elseif ($form_type == 'twform_5') {
    header("Location: tw-form5-details.php?tw_form_id=$tw_form_id");
} else {
    header("Location: tw-forms.php?tw_form_id=$tw_form_id");
}
exit();
