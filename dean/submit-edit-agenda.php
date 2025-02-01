<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}

require '../config/connect.php';
include '../messages.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $agenda_name = mysqli_real_escape_string($conn, $_POST['agenda_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $department_id = mysqli_real_escape_string($conn, $_POST['department_id']);
    
    $query = "UPDATE college_research_agenda SET agenda_name = ?, description = ? WHERE agenda_id = ? AND department_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssii', $agenda_name, $description, $id, $department_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Agenda successfully updated!'];
        header("Location: settings.php");
        exit();
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Error updating agenda!'];
    }
}
?>