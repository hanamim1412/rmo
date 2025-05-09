<?php
// submit_twform2.php

session_start();
require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $required_fields = ['department_id', 'course_id', 'adviser_id', 'ir_agenda_id', 'col_agenda_id', 'thesis_title'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Please fill in all required fields."];
            header("Location: twform_2.php");
            exit();
        }
    }


    $user_id = $_POST['user_id'];
    $department_id = (int) $_POST['department_id'];
    $course_id = (int) $_POST['course_id'];
    $adviser_id = (int) $_POST['adviser_id'];
    $ir_agenda_id = (int) $_POST['ir_agenda_id'];
    $col_agenda_id = (int) $_POST['col_agenda_id'];
    $thesis_title = $_POST['thesis_title'];
    $files = $_FILES['receipt_img'];

    $upload_dir = "../uploads/documents/"; 

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['attachment']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        if (!in_array($file_ext, $allowed_types)) {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Invalid file type. Allowed: JPG, PNG, PDF, DOC, DOCX."];
            header("Location: twform_2.php");
            exit();
        }
        $new_file_name = "twform2_" . time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $upload_dir . $new_file_name;
    
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $file_path = $new_file_name; 
        } else {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "File upload failed. Please try again."];
            header("Location: twform_2.php");
            exit();
        }
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "No file uploaded or upload error occurred."];
        header("Location: twform_2.php");
        exit();
    }

    mysqli_begin_transaction($conn);
    try {

        $query = "INSERT INTO tw_forms (
            form_type, 
            user_id, 
            department_id, 
            course_id, 
            ir_agenda_id, 
            col_agenda_id, 
            research_adviser_id, 
            attachment,
            overall_status, 
            submission_date, 
            last_updated
        ) 
                VALUES ('twform_2', ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 
        'iiiiiis', 
                $user_id, 
                $department_id, 
                    $course_id, 
                    $ir_agenda_id, 
                    $col_agenda_id, 
                    $adviser_id,
                    $file_path
                );
            
        
        mysqli_stmt_execute($stmt);

        $tw_form_id = mysqli_insert_id($conn);
        
        $twform2_query = "INSERT INTO twform_2 (tw_form_id, thesis_title, date_created, last_updated) 
                          VALUES (?, ?, NOW(), NOW())";
                $twform2_stmt = mysqli_prepare($conn, $twform2_query);
                mysqli_stmt_bind_param($twform2_stmt, 'is', $tw_form_id, $thesis_title);
                mysqli_stmt_execute($twform2_stmt);

        foreach ($_POST['student_firstnames'] as $index => $firstname) {
            $lastname = $_POST['student_lastnames'][$index];
            $receipt_number = $_POST['receipt_number'][$index];
            $receipt_date = $_POST['receipt_date'][$index];

            $receipt_path = null;
            if (isset($files['name'][$index]) && $files['error'][$index] === UPLOAD_ERR_OK) {
                $target_dir = "../uploads/receipts/";
                $unique_filename = substr(uniqid(), 0, 4) . "_" . basename($files['name'][$index]);
                $target_file = $target_dir . $unique_filename;

                if (move_uploaded_file($files['tmp_name'][$index], $target_file)) {
                    $receipt_path = $unique_filename; 
                } else {
                    throw new Exception("File upload failed for receipt image at index $index.");
                }
            }

            $receipt_query = "INSERT INTO receipts (tw_form_id, receipt_num, date_paid, receipt_img) VALUES (?, ?, ?, ?)";
                $receipt_stmt = mysqli_prepare($conn, $receipt_query);
                mysqli_stmt_bind_param($receipt_stmt, 'isss', $tw_form_id, $receipt_number, $receipt_date, $receipt_path);
                mysqli_stmt_execute($receipt_stmt);

                $receipt_id = mysqli_insert_id($conn);
                
            $proponent_query = "INSERT INTO proponents (tw_form_id, firstname, lastname, receipt_id) 
                                VALUES (?, ?, ?, ?)";
            $proponent_stmt = mysqli_prepare($conn, $proponent_query);
            mysqli_stmt_bind_param($proponent_stmt, 'issi', $tw_form_id, $firstname, $lastname, $receipt_id);
            mysqli_stmt_execute($proponent_stmt);
        }

        mysqli_commit($conn);

        $_SESSION['messages'][] = ['tags' => 'success', 'content' => "TW Form 2 submitted successfully"];
        header("Location: tw-forms.php");
        exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Failed to submit TW Form 2: " . $e->getMessage()];
            header("Location: twform_2.php");
            exit();
        }
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Form submission failed. Please try again."];
        header("Location: twform_2.php");
        exit();
    } 
?>
