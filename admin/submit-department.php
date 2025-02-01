<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}

require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_name = $_POST['department_name'];
    $file = $_FILES['logo'];

    $file_path = '';
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            $target_dir = "../uploads/dept_logo/";
            $target_file = $target_dir . uniqid() . "_" . basename($file['name']);

            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $file_path = $target_file;
            } else {
                $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Failed to upload the file.'];
                header("Location: add-department.php");
                exit();
            }
        } else {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Invalid file type. Allowed types: jpg, jpeg, png.'];
            header("Location: add-department.php");
            exit();
        }
    }
    $query = "INSERT INTO DEPARTMENTS (department_name, logo, date_created) 
              VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $department_name, $file_path);

    if (!$stmt->execute()) {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Failed to add department: ' . $stmt->error];
        header("Location: add-department.php");
        exit();
    }

    $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Your form has been successfully submitted.'];
    header("Location: tw-forms.php");
    exit();
}

?>