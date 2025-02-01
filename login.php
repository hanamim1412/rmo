<?php
session_start();
include "config/connect.php";
include "messages.php";

$error_message = '';
$success_message = '';
$warning_message = '';

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); 
}

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); 
}

if (isset($_SESSION['warning_message'])) {
    $success_message = $_SESSION['warning_message'];
    unset($_SESSION['warning_message']); 
}

if (isset($_SESSION['user_id'])) {
    $user_type = $_SESSION['user_type'];
    if ($user_type == 'student') {
        header("Location: student/tw-forms.php");
        exit();
    } elseif ($user_type == 'dean') {
        header("Location: dean/tw-forms.php");
        exit();
    } elseif ($user_type == 'rmo_staff') {
        header("Location: rmo_staff/reports.php");
        exit();
    } elseif ($user_type == 'panelist') {
        header("Location: panelist/tw-forms.php");
        exit();
    }elseif ($user_type == 'chairman') {
        header("Location: chairman/tw-forms.php");
        exit();
    } else{
        header("login.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; 

    $stmt = $conn->prepare("SELECT user_id, password, user_type, firstname, lastname FROM accounts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['firstname'] = $user['firstname']; 
                $_SESSION['lastname'] = $user['lastname']; 
                $_SESSION['department_id'] = $user['department_id']; 
                
                $_SESSION['messages'][] = ['tags' => 'success', 'content' => "Login successful! Welcome, " . $_SESSION['firstname'] . " " . $_SESSION['lastname'] . "."];

                if ($user['user_type'] == 'student') {
                    header("Location: student/tw-forms.php");
                    exit();
                } elseif ($user['user_type'] == 'dean') {
                    header("Location: dean/tw-forms.php");
                    exit();
                } elseif ($user['user_type'] == 'rmo_staff') {
                    header("Location: rmo_staff/reports.php");
                    exit();
                } elseif ($user['user_type'] == 'panelist') {
                    header("Location: panelist/tw-forms.php");
                    exit();
                }elseif ($user['user_type'] == 'chairman') {
                    header("Location: chairman/tw-forms.php");
                    exit();
                }
            } else {
                $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Invalid credentials."];
                header("Location: login.php");
                exit();
            }
    } else {
        $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Invalid credentials."];
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My SMC</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <div class="col-md-8 d-flex flex-column justify-content-center align-items-center left-container">
                <img src="images/src/portal-logo.png" alt="mysmc" class="img-fluid logo mb-4">
                <h1 class="text-white text-center">Welcome to St. Michael's College Student Portal!</h1>
            </div>

            <div class="col-md-4 col-sm-6 ml-auto right-container">
                <form method="POST" class="form-container m-auto">
                    <div class="text-left">
                            <h3>HEd Student</h3>
                    </div>
                    <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $error_message ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $success_message ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($warning_message): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= $warning_message ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                    <div class="mb-3">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePasswordVisibility('password', this)">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="submit" value="Sign In" class="btn btn-primary btn-block mt-4 mb-2">

                    <div class="mt-4 forgot-password-container text-center">
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>                           
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<script>
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => {
                        const alerts = document.querySelectorAll('.alert');
                        alerts.forEach(alert => {
                            alert.classList.remove('show');
                            alert.classList.add('fade');
                            setTimeout(() => alert.remove(), 500); 
                        });
                    }, 5000);
                });
    function togglePasswordVisibility(passwordFieldId, eyeIcon) {
        const passwordField = document.getElementById(passwordFieldId);
        const icon = eyeIcon.querySelector("i");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>
