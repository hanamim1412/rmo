<?php
// submit-edit-twform5.php

session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}

require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tw_form_id = isset($_POST['tw_form_id']) ? (int)$_POST['tw_form_id'] : 0;
    $user_id = $_SESSION['user_id'];
    $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : 0;
    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $adviser_id = isset($_POST['adviser_id']) ? (int)$_POST['adviser_id'] : 0;
    $ir_agenda_id = isset($_POST['ir_agenda_id']) ? (int)$_POST['ir_agenda_id'] : 0;
    $col_agenda_id = isset($_POST['col_agenda_id']) ? (int)$_POST['col_agenda_id'] : 0;
    $thesis_title = isset($_POST['thesis_title']) ? $_POST['thesis_title'] : '';
    $defense_date = isset($_POST['defense_date']) ? $_POST['defense_date'] : '';
    $defense_time = isset($_POST['defense_time']) ? $_POST['defense_time'] : '';
    $defense_place = isset($_POST['defense_place']) ? $_POST['defense_place'] : '';
    if (!isset($tw_form_id) || empty($tw_form_id)) {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid tw form id.'];
        header("Location: twform5-edit.php?tw_form_id=" . $_POST['tw_form_id']);
    }    

    $query = "UPDATE tw_forms
              SET ir_agenda_id = ?, col_agenda_id = ?, department_id = ?, course_id = ?, research_adviser_id = ?, last_updated = NOW()
              WHERE tw_form_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iiiiii', $ir_agenda_id, $col_agenda_id, $department_id, $course_id, $adviser_id, $tw_form_id);
    mysqli_stmt_execute($stmt);

    $query = "UPDATE twform_5
              SET thesis_title = ?, defense_date = ?, time = ?, place = ?, last_updated = NOW()
              WHERE tw_form_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssssi', $thesis_title, $defense_date, $defense_time, $defense_place, $tw_form_id);
    mysqli_stmt_execute($stmt);

    if (isset($_FILES['manuscript']) && $_FILES['manuscript']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/manuscripts/';
        $file_tmp_name = $_FILES['manuscript']['tmp_name'];
        $file_name = basename($_FILES['manuscript']['name']);
        $file_path = $upload_dir . $file_name;
    
        if (move_uploaded_file($file_tmp_name, $file_path)) {
            
            $query = "SELECT 1 FROM tw_forms WHERE tw_form_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $tw_form_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
    
            if ($result->num_rows === 0) {
                $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid tw form id.'];
                header("Location: twform5-edit.php?tw_form_id=" . $_POST['tw_form_id']);
                exit();
            }
    
            $query = "SELECT attachment_id FROM attachments WHERE tw_form_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $tw_form_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
    
            if ($result->num_rows > 0) {
                $query = "UPDATE attachments
                          SET file_name = ?, file_path = ?, upload_date = NOW()
                          WHERE tw_form_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'ssi', $file_name, $file_path, $tw_form_id);
                mysqli_stmt_execute($stmt);
            } else {
                $query = "INSERT INTO attachments (tw_form_id, purpose, file_name, file_path, upload_date)
                          VALUES (?, 'Final Defense manuscript', ?, ?, NOW())";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'iss', $tw_form_id, $file_name, $file_path);
                mysqli_stmt_execute($stmt);
            }
        } else {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'File upload failed.'];
            header("Location: twform5-edit.php?tw_form_id=" . $_POST['tw_form_id']);
            exit();
        }
    }
    

    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Form updated successfully.'];
    header("Location: tw-forms.php");
    exit();
}
?>
