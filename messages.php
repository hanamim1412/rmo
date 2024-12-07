<?php 

$error_message = '';
$success_message = '';
$warning_message = '';

if (isset($error_message) && !empty($error_message)) {
    $_SESSION['messages'][] = ['content' => $error_message, 'tags' => 'danger'];
}

if (isset($success_message) && !empty($success_message)) {
    $_SESSION['messages'][] = ['content' => $success_message, 'tags' => 'success'];
}

if (isset($warning_message) && !empty($warning_message)) {
    $_SESSION['messages'][] = ['content' => $warning_message, 'tags' => 'warning'];
}

$messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : [];

unset($_SESSION['messages']); 
?>

