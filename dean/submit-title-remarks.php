<?php
//submit-remarks.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require '../config/connect.php';

if (!isset($_POST['tw_form_id'], $_POST['remarks'], $_POST['form_type'], $_POST['proposed_title_id'])) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid request. Missing form ID, comments, or form type.'];
    header("Location: tw-forms.php");
    exit();
}

$tw_form_id = mysqli_real_escape_string($conn, $_POST['tw_form_id']);
$title_id = mysqli_real_escape_string($conn, $_POST['proposed_title_id']);
$remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
$form_type = mysqli_real_escape_string($conn, $_POST['form_type']);


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
    case 'twform_6':
        $redirectPage = "tw-form6-details.php?tw_form_id=" . $tw_form_id;
        break;
    default:
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Unknown form type. Redirecting to the main forms page.'];
        $redirectPage = "tw-forms.php";
        break;
}

$query = "UPDATE proposed_title SET remarks = ? WHERE tw_form_id = ? AND proposed_title_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Failed to prepare the query: ' . $conn->error];
    header("Location: $redirectPage");
    exit();
}

$stmt->bind_param("sii", $remarks, $tw_form_id, $title_id);

if ($stmt->execute()) {
    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Remarks updated successfully!'];
} else {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Failed to update remarks: ' . $stmt->error];
}

header("Location: $redirectPage");
exit();

?>