<?php
// submit-edit-tw2.php
session_start();
require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $required_fields = ['tw_form_id', 'department_id', 'course_id', 'adviser_id', 'ir_agenda_id', 'col_agenda_id', 'thesis_title', 'defense_date', 'defense_time', 'defense_place'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Please fill in all required fields."];
            header("Location: twform2-edit.php?tw_form_id=" . $_POST['tw_form_id']);
            exit();
        }
    }

    $tw_form_id = (int)$_POST['tw_form_id'];
    $department_id = (int)$_POST['department_id'];
    $course_id = (int)$_POST['course_id'];
    $adviser_id = (int)$_POST['adviser_id'];
    $ir_agenda_id = (int)$_POST['ir_agenda_id'];
    $col_agenda_id = (int)$_POST['col_agenda_id'];
    $thesis_title = $_POST['thesis_title'];
    $defense_date = $_POST['defense_date'];
    $defense_time = $_POST['defense_time'];
    $defense_place = $_POST['defense_place'];
    $files = $_FILES['receipt_img'];

    mysqli_begin_transaction($conn);

    try {
        $query = "
            UPDATE tw_forms 
            SET 
                department_id = ?, 
                course_id = ?, 
                ir_agenda_id = ?, 
                col_agenda_id = ?, 
                research_adviser_id = ?, 
                last_updated = NOW() 
            WHERE tw_form_id = ?
        ";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'iiiiii', $department_id, $course_id, $ir_agenda_id, $col_agenda_id, $adviser_id, $tw_form_id);
        mysqli_stmt_execute($stmt);

        $twform2_query = "
            UPDATE twform_2 
            SET 
                thesis_title = ?, 
                Defense_date = ?, 
                time = ?, 
                place = ?, 
                last_updated = NOW() 
            WHERE tw_form_id = ?
        ";
        $twform2_stmt = mysqli_prepare($conn, $twform2_query);
        mysqli_stmt_bind_param($twform2_stmt, 'ssssi', $thesis_title, $defense_date, $defense_time, $defense_place, $tw_form_id);
        mysqli_stmt_execute($twform2_stmt);

        if (isset($_POST['student_firstnames'])) {
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

                $receipt_query = "
                    UPDATE receipts 
                    SET 
                        receipt_num = ?, 
                        date_paid = ?, 
                        receipt_img = ? 
                    WHERE tw_form_id = ? AND receipt_num = ?
                ";
                $receipt_stmt = mysqli_prepare($conn, $receipt_query);
                mysqli_stmt_bind_param($receipt_stmt, 'ssssi', $receipt_number, $receipt_date, $receipt_path, $tw_form_id, $receipt_number);
                mysqli_stmt_execute($receipt_stmt);

                $proponent_query = "
                    UPDATE proponents 
                    SET 
                        firstname = ?, 
                        lastname = ? 
                    WHERE tw_form_id = ? AND receipt_num = ?
                ";
                $proponent_stmt = mysqli_prepare($conn, $proponent_query);
                mysqli_stmt_bind_param($proponent_stmt, 'ssii', $firstname, $lastname, $tw_form_id, $receipt_number);
                mysqli_stmt_execute($proponent_stmt);
            }
        }

        mysqli_commit($conn);

        $_SESSION['messages'][] = ['tags' => 'success', 'content' => "TW Form 2 updated successfully"];
        header("Location: tw-forms.php");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Failed to update TW Form 2: " . $e->getMessage()];
        header("Location: twform2-edit.php?tw_form_id=" . $_POST['tw_form_id']);
        exit();
    }
} else {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Invalid request method"];
    header("Location: twform2-edit.php?tw_form_id=" . $_POST['tw_form_id']);
    exit();
}
