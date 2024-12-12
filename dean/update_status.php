<?php
// dean/update_status.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require '../config/connect.php';

if (!isset($_POST['tw_form_id'], $_POST['overall_status'], $_POST['form_type'])) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid request. Missing required data.'];
    header("Location: tw-forms.php");
    exit();
}
$tw_form_id = mysqli_real_escape_string($conn, $_POST['tw_form_id']);
$status = mysqli_real_escape_string($conn, $_POST['overall_status']);
$form_type = mysqli_real_escape_string($conn, $_POST['form_type']);

$valid_statuses = ['pending', 'approved', 'rejected'];
switch ($form_type) {
    case 'twform_1':
        $redirectPage = "tw-form1-details.php?tw_form_id=" . $tw_form_id;
        break;
    case 'twform_2':
        $redirectPage = "tw-form2-details.php?tw_form_id=" . $tw_form_id;
        break;
    case 'twform_3':
        $redirectPage = "tw-form3-details.php?tw_form_id=" . $tw_form_id;
        break;
    case 'twform_4':
        $redirectPage = "tw-form4-details.php?tw_form_id=" . $tw_form_id;
        break;
    case 'twform_5':
        $redirectPage = "tw-form5-details.php?tw_form_id=" . $tw_form_id;
        break;
    default:
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Unknown form type. Redirecting to the main forms page.'];
        $redirectPage = "tw-forms.php";
        break;
}

if (!in_array($status, $valid_statuses)) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid request status.'];
    header("Location: $redirectPage");
    exit();
}

$query = "UPDATE tw_forms  SET overall_status = ?, last_updated = NOW() WHERE tw_form_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status, $tw_form_id);

if ($stmt->execute()) {
    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Status updated successfully!'];
} else {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Failed to update status: ' . $stmt->error];
}

header("Location: $redirectPage");
exit();
?>
