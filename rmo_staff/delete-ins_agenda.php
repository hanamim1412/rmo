<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
        header("Location: ../login.php");
        exit();
    }
    include('../config/connect.php');
    include('../messages.php');
    
    if (isset($_GET['ir_agenda_id'])) {
        $id = $_GET['ir_agenda_id'];
        $query = "DELETE FROM institutional_research_agenda WHERE ir_agenda_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Agenda successfully deleted!'];
        } else {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Error deleting agenda!'];
        }
    }
    header("Location: settings.php");
    exit();
?>
