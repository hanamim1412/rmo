<?php
// submit-edit-tw2.php
session_start();
require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $required_fields = ['tw_form_id', 'department_id', 'course_id', 'adviser_id', 'ir_agenda_id', 'col_agenda_id', 'thesis_title'];

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
                attachment = ?, 
                last_updated = NOW() 
            WHERE tw_form_id = ?
        ";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'iiiiisi', $department_id, $course_id, $ir_agenda_id, $col_agenda_id, $adviser_id, $attachment, $tw_form_id);
        mysqli_stmt_execute($stmt);

        $twform2_query = "
            UPDATE twform_2 
            SET 
                thesis_title = ?, 
                last_updated = NOW() 
            WHERE tw_form_id = ?
        ";
        $twform2_stmt = mysqli_prepare($conn, $twform2_query);
        mysqli_stmt_bind_param($twform2_stmt, 'si', $thesis_title, $tw_form_id);
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
