<?php
// submit_twform3.php

session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}

require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $user_id = $_SESSION['user_id'];
    $department_id = isset($_POST['department_id']) ? (int) $_POST['department_id'] : 0;
    $course_id = isset($_POST['course_id']) ? (int) $_POST['course_id'] : 0;
    $adviser_id = isset($_POST['adviser_id']) ? (int) $_POST['adviser_id'] : 0;
    $ir_agenda_id = isset($_POST['ir_agenda_id']) ? (int) $_POST['ir_agenda_id'] : 0;
    $col_agenda_id = isset($_POST['col_agenda_id']) ? (int) $_POST['col_agenda_id'] : 0;
    $student_name = isset($_POST['student']) ? $_POST['student'] : '';
    $thesis_title = isset($_POST['thesis_title']) ? $_POST['thesis_title'] : '';
    $attachment = isset($_POST['attachment']) ? $_POST['attachment'] : '';
    

    if (empty($department_id) || empty($course_id) || empty($adviser_id) || empty($ir_agenda_id) || empty($col_agenda_id) || empty($thesis_title)) {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Please fill out all required fields.'];
        header("Location: twform_3.php");
        exit();
    }

    $query = "INSERT INTO tw_forms (form_type, user_id, ir_agenda_id, col_agenda_id, department_id, course_id, research_adviser_id, attachment, overall_status, submission_date, last_updated) 
              VALUES ('twform_3', ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iiiiiis', $user_id, $ir_agenda_id, $col_agenda_id, $department_id, $course_id, $adviser_id, $attachment);
    mysqli_stmt_execute($stmt);
    $tw_form_id = mysqli_insert_id($conn); 

    $query = "INSERT INTO twform_3 (tw_form_id, student_id, thesis_title, status, last_updated) 
              VALUES (?, ?, ?, 'pending', NOW())";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iis', $tw_form_id, $user_id, $thesis_title);
    mysqli_stmt_execute($stmt);

    if (isset($_FILES['manuscript']) && $_FILES['manuscript']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/manuscripts/';
        $file_tmp_name = $_FILES['manuscript']['tmp_name'];
        $file_name = basename($_FILES['manuscript']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp_name, $file_path)) {
            
            $query = "INSERT INTO attachments (tw_form_id, purpose, file_name, file_path, upload_date) 
                      VALUES (?, 'proposal_manuscript', ?, ?, NOW())";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'iss', $tw_form_id, $file_name, $file_path);
            mysqli_stmt_execute($stmt);
        } else {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'File upload failed.'];
        }
    }

    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Your proposal has been successfully submitted.'];
    header("Location: tw-forms.php");
    exit();
}
?>
