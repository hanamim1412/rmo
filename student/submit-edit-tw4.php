<?php
// submit-edit-tw4.php

session_start();
require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tw_form_id = (int) $_POST['tw_form_id'];
    $department_id = (int) $_POST['department_id'];
    $course_id = (int) $_POST['course_id'];
    $adviser_id = (int) $_POST['adviser_id'];
    $ir_agenda_id = (int) $_POST['ir_agenda_id'];
    $col_agenda_id = (int) $_POST['col_agenda_id'];
    $thesis_title = $_POST['thesis_title'];
    $files = $_FILES['receipt_img'];

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
        header("Location: twform4-edit.php?tw_form_id=$tw_form_id");
        exit();
    }

    $new_file_name = "twform4_" . time() . "_" . uniqid() . "." . $file_ext;
    $target_file = $upload_dir . $new_file_name;

    if (move_uploaded_file($_FILES['new_attachment']['tmp_name'], $target_file)) {
        
        if (!empty($current_attachment) && file_exists($upload_dir . $current_attachment)) {
            unlink($upload_dir . $current_attachment);
        }
        $attachment = $new_file_name; 
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "File upload failed. Please try again."];
        header("Location: twform4-edit.php?tw_form_id=$tw_form_id");
        exit();
    }
}

    mysqli_begin_transaction($conn);
    try {
        
        $update_twforms_query = "
            UPDATE tw_forms
            SET 
                department_id = ?, 
                course_id = ?, 
                ir_agenda_id = ?, 
                col_agenda_id = ?, 
                research_adviser_id = ?, 
                attachment = ?,
                last_updated = NOW()
            WHERE tw_form_id = ?";
        $stmt = mysqli_prepare($conn, $update_twforms_query);
        mysqli_stmt_bind_param($stmt, 'iiiiisi', $department_id, $course_id, $ir_agenda_id, $col_agenda_id, $adviser_id, $attachment, $tw_form_id);
        mysqli_stmt_execute($stmt);

        $update_twform4_query = "
            UPDATE twform_4
            SET 
                thesis_title = ?, 
                last_updated = NOW()
            WHERE tw_form_id = ?";
        $stmt = mysqli_prepare($conn, $update_twform4_query);
        mysqli_stmt_bind_param($stmt, 'si', $thesis_title, $tw_form_id);
        mysqli_stmt_execute($stmt);

        if (isset($_POST['student_firstnames'])) {
            mysqli_begin_transaction($conn);
            try {
                foreach ($_POST['student_firstnames'] as $index => $firstname) {
                    $lastname = $_POST['student_lastnames'][$index];
                    $receipt_number = $_POST['receipt_number'][$index];
                    $receipt_date = $_POST['receipt_date'][$index];
                    $receipt_id = $_POST['receipt_ids'][$index]; 
        
                    $receipt_path = null;
                    if (isset($_FILES['receipt_img']['name'][$index]) && $_FILES['receipt_img']['error'][$index] === UPLOAD_ERR_OK) {
                        $target_dir = "../uploads/receipts/";
                        $unique_filename = substr(uniqid(), 0, 4) . "_" . basename($_FILES['receipt_img']['name'][$index]);
                        $target_file = $target_dir . $unique_filename;

                        if (move_uploaded_file($_FILES['receipt_img']['tmp_name'][$index], $target_file)) {
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
                            receipt_img = CASE 
                                WHEN ? IS NOT NULL THEN ? 
                                ELSE receipt_img 
                            END
                        WHERE receipt_id = ?
                    ";
                    $receipt_stmt = mysqli_prepare($conn, $receipt_query);
                    mysqli_stmt_bind_param($receipt_stmt, 'ssssi', $receipt_number, $receipt_date, $receipt_path, $receipt_path, $receipt_id);
                    if (!mysqli_stmt_execute($receipt_stmt)) {
                        throw new Exception("Failed to update receipt at index $index.");
                    }

                    $proponent_query = "
                        UPDATE proponents 
                        SET 
                            firstname = ?, 
                            lastname = ? 
                        WHERE tw_form_id = ? AND receipt_id = ?
                    ";
                    $proponent_stmt = mysqli_prepare($conn, $proponent_query);
                    mysqli_stmt_bind_param($proponent_stmt, 'ssii', $firstname, $lastname, $tw_form_id, $receipt_id);
                    if (!mysqli_stmt_execute($proponent_stmt)) {
                        throw new Exception("Failed to update proponent at index $index.");
                    }
                }
                mysqli_commit($conn);
            } catch (Exception $e) {
                mysqli_rollback($conn);
                die("Error: " . $e->getMessage());
            }
        }
        
        mysqli_commit($conn);
        $_SESSION['messages'][] = ['tags' => 'success', 'content' => "Form updated successfully."];
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "An error occurred: " . $e->getMessage()];
    }

    header("Location: tw-forms.php");
    exit();
}
?>
