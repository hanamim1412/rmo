<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
        header("Location: ../login.php");
        exit();
    }
    include('dean-master.php');
    require '../config/connect.php';
    include '../messages.php';
    $title = "Edit User";
    ob_start();

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        die("Invalid user ID.");
    }
    
    $user_id = intval($_GET['id']); 
    
    $query = "SELECT * FROM ACCOUNTS WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("User not found.");
    }
    
    $user = $result->fetch_assoc();
    
    $dept_query = "SELECT * FROM DEPARTMENTS";
    $dept_result = $conn->query($dept_query);
?>
<section id="settings" class="pt-4">
    <div class="header-container pt-4">
        <h4>
        <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
                    <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
                </a>
       Update User</h4>
    </div>
        <?php if (!empty($messages)): ?>
            <div class="container mt-3">
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= htmlspecialchars($message['tags']) ?> alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i><?= htmlspecialchars($message['content']) ?>
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="container">
                <form action="submit-edit-user.php" method="post" onsubmit="return validateForm()" class="form-container p-2">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
                    <?php if (!empty($messages)): ?>
                            <?php foreach ($messages as $message): ?>
                                <div class="alert alert-<?= htmlspecialchars($message['tags']) ?> alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i><?= htmlspecialchars($message['content']) ?>
                                    <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                    <?php endif; ?>
                        <div class="text-center">
                                <h4>Update User</h4>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>First Name</label>
                                <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($user['firstname']) ?>" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Last Name</label>
                                <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($user['lastname']) ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Contact</label>
                                <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($user['contact']) ?>" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Department</label>
                                <select name="department_id" class="form-control form-select" required>
                                    <?php while ($dept = $dept_result->fetch_assoc()): ?>
                                        <option value="<?= $dept['department_id'] ?>" <?= ($dept['department_id'] == $user['department_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dept['department_name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    
                        <button type="submit" class="btn btn-primary mb-1">Update</button>
                        <a href="settings.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
</section>

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

            if (!passwordField || !icon) {
                console.error('Password field or icon not found!');
                return;
            }

            console.log('Toggling password visibility...');
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

        function validateForm() {
            const password1 = document.getElementById('password').value;
            const password2 = document.getElementById('password2').value;

            if (password1 !== password2) {
                alert("Passwords do not match.");
                return false;
            }

            return true;
        }
</script>


<?php
$content = ob_get_clean();
include('dean-master.php');
?>
<style>
#loadingOverlay {
    position: fixed; 
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5); 
    display: flex; 
    justify-content: center;
    align-items: center;
    z-index: 1050; 
}

#loadingSpinnerContainer {
    width: 5rem;
    height: 5rem;
    color: #007bff; 
}
.thead-background {
    background-color:rgb(56, 120, 193);
    color: white;
}
</style>