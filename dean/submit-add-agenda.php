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
    
    $query = "INSERT INTO college_research_agenda (agenda_name, description, department_id) VALUES ('$agenda_name', '$description', '$department_id')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Agenda successfully added!'];
        header("Location: settings.php");
        exit();
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Error adding agenda!'];
    }
}
?>