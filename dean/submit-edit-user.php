<?php
session_start();
require '../config/connect.php';
include '../messages.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $contact = trim($_POST['contact']);
    $department_id = intval($_POST['department_id']);

    if (empty($firstname) || empty($lastname) || empty($contact) || empty($department_id)) {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'All fields are required.'];
        header("Location: settings.php");
        exit();
    }

    $query = "UPDATE ACCOUNTS 
              SET firstname = ?, lastname = ?, contact = ?, department_id = ? 
              WHERE user_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssii", $firstname, $lastname, $contact, $department_id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'User updated successfully!'];
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Failed to update user: ' . $stmt->error];
    }

    $stmt->close();
    $conn->close();

    header("Location: settings.php");
    exit();
}
?>
