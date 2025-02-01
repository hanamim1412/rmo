<?php
include '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_id = $_POST['department_id']; 
    $department_name = $_POST['department_name'];
    $new_logo = $_FILES['logo'];

    $logo_path = '';
    if ($new_logo['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($new_logo['type'], $allowed_types)) {
            $logo_name = time() . '_' . basename($new_logo['name']);
            $logo_path = '../uploads/dept_logo/' . $logo_name;
            move_uploaded_file($new_logo['tmp_name'], $logo_path);
        } else {
            $messages[] = ['tags' => 'danger', 'content' => 'Invalid logo file type. Allowed types are JPG, PNG, GIF.'];
        }
    }

    $update_query = "UPDATE departments SET department_name = ?";
    if ($logo_path) {
        $update_query .= ", logo = ?";
    }
    $update_query .= " WHERE department_id = ?";

    if ($stmt = $conn->prepare($update_query)) {
        if ($logo_path) {
            $stmt->bind_param('ssi', $department_name, $logo_path, $department_id);
        } else {
            $stmt->bind_param('si', $department_name, $department_id);
        }
        if ($stmt->execute()) {
            $messages[] = ['tags' => 'success', 'content' => 'Department updated successfully.'];
        } else {
            $messages[] = ['tags' => 'danger', 'content' => 'Error updating department: ' . $stmt->error];
            header('Location: edit-department.php?department_id='. $department_id);
            exit();
        }
    }
}

header('Location: view-departments.php');
exit;
?>
