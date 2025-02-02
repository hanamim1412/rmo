<?php
// tw2_update_defense_schedule.php
session_start();
require '../config/connect.php';
include '../messages.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_POST['tw_form_id'], $_POST['form_type'], $_POST['defense_date'], $_POST['time'], $_POST['place'], $_POST['update_schedule'])) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid form submission.'];
    header("Location: tw-forms.php");
    exit();
}

$tw_form_id = mysqli_real_escape_string($conn, $_POST['tw_form_id']);
$form_type = mysqli_real_escape_string($conn, $_POST['form_type']);

$defense_date = mysqli_real_escape_string($conn, $_POST['defense_date']);
$time = mysqli_real_escape_string($conn, $_POST['time']);
$place = mysqli_real_escape_string($conn, $_POST['place']);

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

$query = "
    UPDATE twform_2
    SET defense_date = ?, time = ?, place = ?, last_updated = NOW()
    WHERE tw_form_id = ?
    ";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssi", $defense_date, $time, $place, $tw_form_id);

if ($stmt->execute()) {
    $conn->commit();
    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Defense schedule updated successfully.'];
} else {
    $conn->rollback();
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Failed to update defense schedule.'];
}

header("Location: $redirectPage");
exit();
?>