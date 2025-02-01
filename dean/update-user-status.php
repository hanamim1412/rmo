<?php
session_start();
require '../config/connect.php';
include '../messages.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $new_status = intval($_POST['new_status']);
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);

    $query = "UPDATE ACCOUNTS SET is_active = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $new_status, $user_id);

    if ($stmt->execute()) {
        $status = $new_status ? 'activated' : 'deactivated';
        $_SESSION['messages'][] = ['tags' => 'success', 'content' => "Account $firstname $lastname status updated successfully"];
        header("Location: settings.php");
        exit();
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Account $firstname $lastname status failed to update"];
        header("Location: settings.php");
        exit();
    }
}
?>
