<?php
// submit-edit-twform3.php

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

    if (!isset($tw_form_id) || empty($tw_form_id)) {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid tw form id.'];
        header("Location: twform3-edit.php?tw_form_id=" . $_POST['tw_form_id']);
    } 
    $query = "SELECT attachment FROM tw_forms WHERE tw_form_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $tw_form_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $current_attachment = $row['attachment'];

    $upload_dir = "../uploads/documents/";
    $attachment = $current_attachment; 

    if (isset($_FILES['new_attachment']) && $_FILES['new_attachment']['error'] == UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['new_attachment']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        if (!in_array($file_ext, $allowed_types)) {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Invalid file type. Allowed: JPG, PNG, PDF, DOC, DOCX."];
            header("Location: twform2-edit.php?tw_form_id=$tw_form_id");
            exit();
        }

        $new_file_name = "twform2_" . time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $upload_dir . $new_file_name;

        if (move_uploaded_file($_FILES['new_attachment']['tmp_name'], $target_file)) {
            
            if (!empty($current_attachment) && file_exists($upload_dir . $current_attachment)) {
                unlink($upload_dir . $current_attachment);
            }
            $attachment = $new_file_name; 
        } else {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "File upload failed. Please try again."];
            header("Location: twform2-edit.php?tw_form_id=$tw_form_id");
            exit();
        }
    }   

    $query = "UPDATE tw_forms
              SET ir_agenda_id = ?, col_agenda_id = ?, department_id = ?, course_id = ?, research_adviser_id = ?, attachment = ?, last_updated = NOW()
              WHERE tw_form_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iiiiisi', $ir_agenda_id, $col_agenda_id, $department_id, $course_id, $adviser_id, $attachment, $tw_form_id);
    mysqli_stmt_execute($stmt);

    $query = "UPDATE twform_3
              SET thesis_title = ?, last_updated = NOW()
              WHERE tw_form_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $thesis_title,  $tw_form_id);
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
                header("Location: twform3-edit.php?tw_form_id=" . $_POST['tw_form_id']);
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
                          VALUES (?, 'proposal_manuscript', ?, ?, NOW())";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'iss', $tw_form_id, $file_name, $file_path);
                mysqli_stmt_execute($stmt);
            }
        } else {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'File upload failed.'];
            header("Location: twform3-edit.php?tw_form_id=" . $_POST['tw_form_id']);
            exit();
        }
    }
    

    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Form updated successfully.'];
    header("Location: tw-forms.php");
    exit();
}
?>
