<?php
session_start();
include '../config/connect.php';
include '../messages.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $firstname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lastname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $department_id = mysqli_real_escape_string($conn, $_POST['department_id']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    if (!$firstname || !$lastname || !$department_id || !$contact) {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'All fields are required.'];
        header("Location: add-panelist.php");
        exit();
    }

    if ($password1 !== $password2) {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Passwords do not match.'];
        header("Location: add-panelist.php");
        exit();
    } 
        $hashed_password = password_hash($password1, PASSWORD_DEFAULT);
        $email = strtolower(str_replace(' ', '', $firstname) . '.' . str_replace(' ', '', $lastname) . '@my.smciligan.edu.ph');
        
        $current_year = date("Y");
        $last_two_digits = substr($current_year, -2);
        
        $query = "SELECT COUNT(*) as total FROM accounts WHERE username LIKE 'C$last_two_digits-%'";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        $incremental_value = $row['total'] + 1;

        $username = sprintf("C%s-%04d", $last_two_digits, $incremental_value);
        $insert_query = "
        INSERT INTO accounts 
                (firstname, lastname, contact, username, password, smc_email, department_id, user_type, is_active, date_created) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'panelist', 1, NOW())
            ";
            $stmt = $conn->prepare($insert_query);
            if (!$stmt) {
                $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Failed to prepare statement.'];
                header("Location: add-panelist.php");
                exit();
            }

            $stmt->bind_param(
                'ssssssi',
                $firstname,
                $lastname,
                $contact,
                $username,
                $hashed_password,
                $email,
                $department_id
            );

        if ($stmt->execute()) {
                $_SESSION['messages'][] = ['tags' => 'success', 'content' => 'Panelist Added successfully.'];
                header("Location: settings.php");
                exit();
            } else {
                $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Failed to Add Panelist. Please try again.'];
                header("Location: settings.php");
                exit();
            }

}
?>
