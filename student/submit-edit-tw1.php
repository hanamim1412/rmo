<?php
// submit-edit-tw1.php
session_start();
require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Invalid request method"];
    header("Location: tw-forms.php");
    exit();
}

$tw_form_id = isset($_POST['tw_form_id']) ? (int)$_POST['tw_form_id'] : 0;
if ($tw_form_id <= 0) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Invalid Form ID"];
    header("Location: tw-forms.php");
    exit();
}

$department_id = $_POST['department_id'] ?? null;
$course_id = $_POST['course_id'] ?? null;
$adviser_id = $_POST['adviser_id'] ?? null;
$ir_agenda_id = $_POST['ir_agenda_id'] ?? null;
$col_agenda_id = $_POST['col_agenda_id'] ?? null;
$year_level = $_POST['year_level'] ?? null;
$proposed_titles = $_POST['proposed_titles'] ?? [];
$rationales = $_POST['rationales'] ?? [];
$student_firstnames = $_POST['student_firstnames'] ?? [];
$student_lastnames = $_POST['student_lastnames'] ?? [];

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
        header("Location: twform1-edit.php?tw_form_id=$tw_form_id");
        exit();
    }

    $new_file_name = "twform1_" . time() . "_" . uniqid() . "." . $file_ext;
    $target_file = $upload_dir . $new_file_name;

    if (move_uploaded_file($_FILES['new_attachment']['tmp_name'], $target_file)) {
        
        if (!empty($current_attachment) && file_exists($upload_dir . $current_attachment)) {
            unlink($upload_dir . $current_attachment);
        }
        $attachment = $new_file_name; 
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "File upload failed. Please try again."];
        header("Location: twform1-edit.php?tw_form_id=$tw_form_id");
        exit();
    }
}

$conn->begin_transaction();

try {
    
    $query = "
        UPDATE tw_forms 
        SET department_id = ?, course_id = ?, research_adviser_id = ?, 
            ir_agenda_id = ?, col_agenda_id = ?, attachment = ?, last_updated = NOW() 
        WHERE tw_form_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiiiisi', $department_id, $course_id, $adviser_id, $ir_agenda_id, $col_agenda_id, $attachment, $tw_form_id);
    $stmt->execute();

    $query = "UPDATE twform_1 SET year_level = ?, last_updated = NOW() WHERE tw_form_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $year_level, $tw_form_id);
    $stmt->execute();

    $query = "DELETE FROM proposed_title WHERE tw_form_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $tw_form_id);
    $stmt->execute();

    $query = "INSERT INTO proposed_title (tw_form_id, title_name, rationale, date_created) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    foreach ($proposed_titles as $index => $title_name) {
        $rationale = $rationales[$index];
        $stmt->bind_param('iss', $tw_form_id, $title_name, $rationale);
        $stmt->execute();
    }

    $query = "DELETE FROM proponents WHERE tw_form_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $tw_form_id);
    $stmt->execute();

    $query = "INSERT INTO proponents (tw_form_id, firstname, lastname, date_created) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    foreach ($student_firstnames as $index => $firstname) {
        $lastname = $student_lastnames[$index];
        $stmt->bind_param('iss', $tw_form_id, $firstname, $lastname);
        $stmt->execute();
    }

    $conn->commit();
    $_SESSION['messages'][] = ['tags' => 'success', 'content' => "Form updated successfully"];
    header("Location: tw-forms.php");
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Failed to update form: " . $e->getMessage()];
    header("Location: twform1-edit.php?tw_form_id=" . $tw_form_id);
}
?>
