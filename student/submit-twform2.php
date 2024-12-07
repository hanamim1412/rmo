<?php
session_start();
require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Invalid request"];
    header("Location: twform_2.php");
    exit();
}

$user_id = $_POST['user_id'];
$department_id = $_POST['department_id'];
$course_id = $_POST['course_id'];
$adviser_id = $_POST['adviser_id'];
$ir_agenda_id = $_POST['ir_agenda_id'];
$col_agenda_id = $_POST['col_agenda_id'];
$thesis_title = $_POST['thesis_title'];
$defense_date = $_POST['defense_date'];
$defense_time = $_POST['defense_time'];
$defense_place = $_POST['defense_place'];

if (empty($user_id) || empty($department_id) || empty($course_id) || empty($adviser_id) || empty($ir_agenda_id) || empty($col_agenda_id) || empty($thesis_title) || empty($defense_date) || empty($defense_time) || empty($defense_place)) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "All fields are required"];
    header("Location: twform_2.php");
    exit();
}

mysqli_begin_transaction($conn);

try {

    $form_type = 'twform_2'; 
    $query = "INSERT INTO tw_forms (user_id, department_id, course_id, adviser_id, ir_agenda_id, col_agenda_id, form_type, research_adviser_id, submission_date, last_updated) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iiiiisss', $user_id, $department_id, $course_id, $adviser_id, $ir_agenda_id, $col_agenda_id, $form_type, $adviser_id);
    mysqli_stmt_execute($stmt);

    $tw_form_id = mysqli_insert_id($conn);

    $query = "INSERT INTO twform_2 (tw_form_id, thesis_title, defense_date, defense_time, defense_place, form_status, date_created, last_updated) 
              VALUES (?, ?, ?, ?, ?, 'pending', NOW(), NOW())";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'isssss', $tw_form_id, $thesis_title, $defense_date, $defense_time, $defense_place);
    mysqli_stmt_execute($stmt);

    foreach ($_POST['student_firstnames'] as $index => $firstname) {
        $lastname = $_POST['student_lastnames'][$index];
        $receipt_number = $_POST['receipt_number'][$index];
        $receipt_date = $_POST['receipt_date'][$index];
        $receipt_img = $_FILES['receipt_img']['name'][$index];
        
        $upload_dir = '../uploads/receipts/';
        $receipt_path = $upload_dir . basename($receipt_img);
        move_uploaded_file($_FILES['receipt_img']['tmp_name'][$index], $receipt_path);

        $receipt_query = "INSERT INTO receipts (tw_form_id, receipt_number, receipt_date, receipt_img) VALUES (?, ?, ?, ?)";
        $receipt_stmt = mysqli_prepare($conn, $receipt_query);
        mysqli_stmt_bind_param($receipt_stmt, 'isss', $tw_form_id, $receipt_number, $receipt_date, $receipt_path);
        mysqli_stmt_execute($receipt_stmt);
        $receipt_id = mysqli_insert_id($conn);

        $proponent_query = "INSERT INTO proponents (tw_form_id, firstname, lastname, receipt_id) 
                            VALUES (?, ?, ?, ?)";
        $proponent_stmt = mysqli_prepare($conn, $proponent_query);
        mysqli_stmt_bind_param($proponent_stmt, 'isss', $tw_form_id, $firstname, $lastname, $receipt_id);
        mysqli_stmt_execute($proponent_stmt);
    }

    mysqli_commit($conn);

    $_SESSION['messages'][] = ['tags' => 'success', 'content' => "TW Form 2 submitted successfully"];
    header("Location: twform_2.php");
    exit();
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Failed to submit TW Form 2: " . $e->getMessage()];
    header("Location: twform_2.php");
    exit();
}
?>
