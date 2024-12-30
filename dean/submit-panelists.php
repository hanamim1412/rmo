<?php
session_start();
require '../config/connect.php';

function validateInputs($tw_form_id, $panelist_ids, $comments) {
    if (!filter_var($tw_form_id, FILTER_VALIDATE_INT)) {
        return "Invalid form ID.";
    }
    if (count($panelist_ids) !== 4) {
        return "You must select exactly 4 panelists.";
    }
    if (strlen($comments) > 500) {
        return "Comments must be 500 characters or fewer.";
    }
    return null;
}

function assignPanelist($conn, $tw_form_id, $panelist_id, $comments) {
    $query = "INSERT INTO assigned_panelists (tw_form_id, user_id, is_selected, comments, date_created) 
              VALUES (?, ?, 1, ?, NOW())";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Failed to prepare statement: " . $conn->error);
        return false;
    }
    $stmt->bind_param('iis', $tw_form_id, $panelist_id, $comments);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tw_form_id = filter_var($_POST['tw_form_id'], FILTER_VALIDATE_INT);
    $panelist_ids = $_POST['panelist_ids'];
    $form_type = filter_var($_POST['form_type'], FILTER_SANITIZE_STRING);
    $comments = htmlspecialchars(trim($_POST['comments']));

    $validationError = validateInputs($tw_form_id, $panelist_ids, $comments);
    if ($validationError) {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => $validationError];
        header("Location: assign-panelists.php?tw_form_id=$tw_form_id");
        exit();
    }

    foreach ($panelist_ids as $panelist_id) {
        if (!assignPanelist($conn, $tw_form_id, $panelist_id, $comments)) {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'An error occurred while assigning panelists.'];
            header("Location: assign-panelists.php?tw_form_id=$tw_form_id");
            exit();
        }
    }

    $redirectPages = [
        'twform_1' => 'tw-form1-details.php',
        'twform_2' => 'tw-form2-details.php',
        'twform_3' => 'tw-form3-details.php',
        'twform_4' => 'tw-form4-details.php',
        'twform_5' => 'tw-form5-details.php',
    ];
    $redirectPage = $redirectPages[$form_type] ?? 'tw-forms.php';

    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Panelists successfully assigned.'];
    header("Location: $redirectPage?tw_form_id=$tw_form_id");
    exit();
}
?>
