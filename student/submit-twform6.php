<?php
// submit_twform6.php

session_start();
require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $required_fields = ['department_id', 'course_id', 'adviser_id', 'ir_agenda_id', 'col_agenda_id', 'thesis_title', 'student_firstnames', 'student_lastnames'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Please fill in all required fields."];
            header("Location: twform_6.php");
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
    $manuscript = $_FILES['manuscript'];

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
            overall_status, 
            submission_date, 
            last_updated
        ) 
                VALUES ('twform_6', ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 
        'iiiiii', 
                $user_id, 
                $department_id, 
                    $course_id, 
                    $ir_agenda_id, 
                    $col_agenda_id, 
                    $adviser_id
                );
            
        
        mysqli_stmt_execute($stmt);

        $tw_form_id = mysqli_insert_id($conn);
        
        $twform6_query = "INSERT INTO twform_6 (tw_form_id, thesis_title, date_created, last_updated) 
                          VALUES (?, ?, NOW(), NOW())";
                $twform6_stmt = mysqli_prepare($conn, $twform6_query);
                mysqli_stmt_bind_param($twform6_stmt, 'is', $tw_form_id, $thesis_title);
                mysqli_stmt_execute($twform6_stmt);
        
                if (isset($_FILES['manuscript']) && $_FILES['manuscript']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = '../uploads/manuscripts/';
                    $file_tmp_name = $_FILES['manuscript']['tmp_name'];
                    $file_name = basename($_FILES['manuscript']['name']);
                    $file_path = $upload_dir . $file_name;
            
                    if (move_uploaded_file($file_tmp_name, $file_path)) {
                        
                        $query = "INSERT INTO attachments (tw_form_id, purpose, file_name, file_path, upload_date) 
                                  VALUES (?, 'Approval for Binding', ?, ?, NOW())";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, 'iss', $tw_form_id, $file_name, $file_path);
                        mysqli_stmt_execute($stmt);
                    } else {
                        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'File upload failed.'];
                    }
                }

                if (isset($_POST['student_firstnames']) && isset($_POST['student_lastnames'])) {
            
                    $count = count($_POST['student_firstnames']);
                    if ($count !== count($_POST['student_lastnames'])) {
                        $_SESSION['messages'][] = [
                            'tags' => 'danger',
                            'content' => "The number of first names does not match the number of last names."
                        ];
                        header("Location: twform_6.php");
                        exit();
                    }
                
                    for ($i = 0; $i < $count; $i++) {
                        $firstname = $_POST['student_firstnames'][$i];
                        $lastname = $_POST['student_lastnames'][$i];
                
                        if (empty($firstname) || empty($lastname)) {
                            $_SESSION['messages'][] = [
                                'tags' => 'danger',
                                'content' => "First name or last name cannot be empty."
                            ];
                            header("Location: twform_6.php");
                            exit();
                        }
                
                        $query = "INSERT INTO proponents (tw_form_id, firstname, lastname, date_created)
                                  VALUES (?, ?, ?, NOW())";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, 'iss', $tw_form_id, $firstname, $lastname);
                        mysqli_stmt_execute($stmt);
                
                        if (mysqli_stmt_affected_rows($stmt) == 0) {
                            $_SESSION['messages'][] = [
                                'tags' => 'danger',
                                'content' => "Failed to add proponent with name $firstname $lastname."
                            ];
                            header("Location: twform_6.php");
                            exit();
                        }
                    }
                }

        mysqli_commit($conn);

        $_SESSION['messages'][] = ['tags' => 'success', 'content' => "TW Form 6 submitted successfully"];
        header("Location: tw-forms.php");
        exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Failed to submit TW Form 6: " . $e->getMessage()];
            header("Location: twform_6.php");
            exit();
        }
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Form submission failed. Please try again."];
        header("Location: twform_6.php");
        exit();
    } 
?>
