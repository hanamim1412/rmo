<?php
session_start();

require '../config/connect.php';
include '../messages.php';
if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ir_agenda_id = mysqli_real_escape_string($conn, $_POST['ir_agenda_id']);
    $ir_agenda_name = mysqli_real_escape_string($conn, $_POST['ir_agenda_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $query = "UPDATE institutional_research_agenda SET ir_agenda_name = ?, sub_areas = ? WHERE ir_agenda_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssi', $ir_agenda_name, $description, $ir_agenda_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Agenda successfully updated!'];
        header("Location: settings.php");
        exit();
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Error updating agenda!'];
        header("Location: edit-ins_agenda.php");
        exit();
    }
}
?>