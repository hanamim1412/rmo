<?php
require '../config/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tw_form_id = isset($_POST['tw_form_id']) ? (int)$_POST['tw_form_id'] : 0;
    $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : null;
    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : null;
    $adviser_id = isset($_POST['adviser_id']) ? (int)$_POST['adviser_id'] : null;
    $ir_agenda_id = isset($_POST['ir_agenda_id']) ? (int)$_POST['ir_agenda_id'] : null;
    $col_agenda_id = isset($_POST['col_agenda_id']) ? (int)$_POST['col_agenda_id'] : null;
    $thesis_title = isset($_POST['thesis_title']) ? trim($_POST['thesis_title']) : null;
    $student_firstnames = $_POST['student_firstnames'] ?? [];
    $student_lastnames = $_POST['student_lastnames'] ?? [];
    $manuscript_file = $_FILES['manuscript'] ?? null;

    if ($tw_form_id <= 0) {
        die("Invalid Form ID");
    }

    mysqli_begin_transaction($conn);

    try {
        $update_tw_forms_query = "
            UPDATE tw_forms 
            SET 
                department_id = ?, 
                course_id = ?, 
                research_adviser_id = ?, 
                ir_agenda_id = ?, 
                col_agenda_id = ?, 
                last_updated = NOW()
            WHERE tw_form_id = ?
        ";
        $stmt = mysqli_prepare($conn, $update_tw_forms_query);
        mysqli_stmt_bind_param($stmt, 'iiiiii', $department_id, $course_id, $adviser_id, $ir_agenda_id, $col_agenda_id, $tw_form_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to update TW Forms.");
        }
        $update_twform6_query = "
            UPDATE twform_6 
            SET 
                thesis_title = ?, 
                last_updated = NOW()
            WHERE tw_form_id = ?
        ";
        $stmt = mysqli_prepare($conn, $update_twform6_query);
        mysqli_stmt_bind_param($stmt, 'si', $thesis_title, $tw_form_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to update TW Form 6.");
        }

        $delete_proponents_query = "DELETE FROM proponents WHERE tw_form_id = ?";
        $stmt = mysqli_prepare($conn, $delete_proponents_query);
        mysqli_stmt_bind_param($stmt, 'i', $tw_form_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to delete existing proponents.");
        }

        foreach ($student_firstnames as $index => $firstname) {
            $lastname = $student_lastnames[$index];
            $insert_proponent_query = "
                INSERT INTO proponents (tw_form_id, firstname, lastname, date_created) 
                VALUES (?, ?, ?, NOW())
            ";
            $stmt = mysqli_prepare($conn, $insert_proponent_query);
            mysqli_stmt_bind_param($stmt, 'iss', $tw_form_id, $firstname, $lastname);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to insert new proponent.");
            }
        }

        if ($manuscript_file && $manuscript_file['error'] === UPLOAD_ERR_OK) {
            $file_name = basename($manuscript_file['name']);
            $upload_dir = "../uploads/";
            $unique_filename = uniqid() . "_" . $file_name;
            $file_path = $upload_dir . $unique_filename;

            if (!move_uploaded_file($manuscript_file['tmp_name'], $file_path)) {
                throw new Exception("Failed to upload manuscript.");
            }

            $delete_attachment_query = "DELETE FROM attachments WHERE tw_form_id = ?";
            $stmt = mysqli_prepare($conn, $delete_attachment_query);
            mysqli_stmt_bind_param($stmt, 'i', $tw_form_id);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to delete existing attachment.");
            }

            $insert_attachment_query = "
                INSERT INTO attachments (tw_form_id, purpose, file_name, file_path, upload_date) 
                VALUES (?, 'manuscript', ?, ?, NOW())
            ";
            $stmt = mysqli_prepare($conn, $insert_attachment_query);
            mysqli_stmt_bind_param($stmt, 'iss', $tw_form_id, $file_name, $unique_filename);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to insert new attachment.");
            }
        }

        mysqli_commit($conn); 
        $_SESSION['messages'][] = ['tags' => 'success', 'content' => "Form updated successfully."];
        header("Location: tw-forms.php");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn); 
        die("Error: " . $e->getMessage());
    }
}
?>
