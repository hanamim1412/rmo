<?php
// student/update-title-status.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require '../config/connect.php';

if (!isset($_POST['proposed_title_id'], $_POST['tw_form_id'], $_POST['form_type'], $_POST['new_status'])) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid request. Missing required data.'];
    header("Location: tw-forms.php");
    exit();
}

$proposed_title_id = mysqli_real_escape_string($conn, $_POST['proposed_title_id']);
$tw_form_id = mysqli_real_escape_string($conn, $_POST['tw_form_id']);
$form_type = mysqli_real_escape_string($conn, $_POST['form_type']);
$new_status = intval($_POST['new_status']);

$queryCheck = "SELECT * FROM proposed_title WHERE proposed_title_id = ?";
$stmtCheck = $conn->prepare($queryCheck);
$stmtCheck->bind_param("i", $proposed_title_id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows === 0) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Proposed title not found.'];
    header("Location: tw-form1-details.php?tw_form_id=" . $tw_form_id);
    exit();
}

// Update the status of the proposed title
$queryUpdate = "UPDATE proposed_title SET is_selected = ? WHERE proposed_title_id = ?";
$stmtUpdate = $conn->prepare($queryUpdate);
$stmtUpdate->bind_param("ii", $new_status, $proposed_title_id);

if ($stmtUpdate->execute()) {
    $_SESSION['messages'][] = [
        'tags' => 'success',
        'content' => $new_status === 1 
            ? 'The title has been successfully selected.' 
            : 'The title has been successfully deselected.'
    ];
} else {
    $_SESSION['messages'][] = [
        'tags' => 'danger',
        'content' => 'Failed to update the title status. ' . $stmtUpdate->error
    ];
}

switch ($form_type) {
    case 'twform_1':
        $redirectPage = "tw-form1-details.php";
        break;
    case 'twform_2':
        $redirectPage = "tw-form2-details.php";
        break;
    case 'twform_3':
        $redirectPage = "tw-form3-details.php";
        break;
    case 'twform_4':
        $redirectPage = "tw-form4-details.php";
        break;
    case 'twform_5':
        $redirectPage = "tw-form5-details.php";
        break;
    default:
        $redirectPage = "tw-forms.php";
        break;
}

header("Location: $redirectPage?tw_form_id=" . $tw_form_id);
exit();
?>
