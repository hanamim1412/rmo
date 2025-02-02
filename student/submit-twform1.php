<?php
// submit_twform1.php
session_start();
require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $required_fields = ['department_id', 'course_id', 'adviser_id', 'ir_agenda_id', 'col_agenda_id', 'student_firstnames', 'student_lastnames', 'proposed_titles', 'rationales'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Please fill in all required fields."];
            header("Location: twform_1.php");
            exit();
        }
    }

    $user_id = $_SESSION['user_id']; 
    $department_id = (int) $_POST['department_id'];
    $course_id = (int) $_POST['course_id'];
    $adviser_id = (int) $_POST['adviser_id'];
    $ir_agenda_id = (int) $_POST['ir_agenda_id'];
    $col_agenda_id = (int) $_POST['col_agenda_id'];
    $student_firstname = $_POST['student_firstnames'];
    $student_lastname = $_POST['student_lastnames'];
    $year_level = $_POST['year_level'];
    $proposed_titles = $_POST['proposed_titles'];
    $rationales = $_POST['rationales'];

    if (count($proposed_titles) !== count($rationales)) {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Mismatch between titles and rationales."];
        header("Location: twform_1.php");
        exit();
    }

    $upload_dir = "../uploads/documents/"; 

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['attachment']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        if (!in_array($file_ext, $allowed_types)) {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Invalid file type. Allowed: JPG, PNG, PDF, DOC, DOCX."];
            header("Location: twform_1.php");
            exit();
        }
        $new_file_name = "twform1_" . time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $upload_dir . $new_file_name;
    
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $file_path = $new_file_name; 
        } else {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "File upload failed. Please try again."];
            header("Location: twform_1.php");
            exit();
        }
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "No file uploaded or upload error occurred."];
        header("Location: twform_1.php");
        exit();
    }
    
    mysqli_begin_transaction($conn);

    try {
        
        $query = "INSERT INTO tw_forms (form_type, user_id, ir_agenda_id, col_agenda_id, department_id, course_id, research_adviser_id, attachment, overall_status, submission_date)
                  VALUES ('twform_1', ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'iiiiiis', $user_id, $ir_agenda_id, $col_agenda_id, $department_id, $course_id, $adviser_id, $file_path);
        mysqli_stmt_execute($stmt);

        $tw_form_id = mysqli_insert_id($conn);

        $query = "INSERT INTO twform_1 (tw_form_id, year_level, date_created)
                  VALUES (?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $query);
        
        mysqli_stmt_bind_param($stmt, 'is', $tw_form_id, $year_level);
        mysqli_stmt_execute($stmt);

        if (isset($_POST['student_firstnames']) && isset($_POST['student_lastnames'])) {
            
            $count = count($_POST['student_firstnames']);
            if ($count !== count($_POST['student_lastnames'])) {
                $_SESSION['messages'][] = [
                    'tags' => 'danger',
                    'content' => "The number of first names does not match the number of last names."
                ];
                header("Location: twform_1.php");
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
                    header("Location: twform_1.php");
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
                    header("Location: twform_1.php");
                    exit();
                }
            }
        }
        
        
        foreach ($proposed_titles as $index => $title) {
            $rationale = $rationales[$index];
            $query = "INSERT INTO proposed_title (tw_form_id, title_name, rationale, is_selected, date_created)
                      VALUES (?, ?, ?, 0, NOW())";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'iss', $tw_form_id, $title, $rationale);
            mysqli_stmt_execute($stmt);
        }

        mysqli_commit($conn);

        $_SESSION['messages'][] = ['tags' => 'success', 'content' => "Form submitted successfully."];
        header("Location: tw-forms.php");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log($e->getMessage());
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Error submitting form. Please try again."];
        header("Location: twform_1.php");
        exit();
    }
} else {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Form submission failed. Please try again."];
    header("Location: twform_1.php");
    exit();
}
?>
