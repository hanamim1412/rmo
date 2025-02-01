<?php
include '../config/connect.php';
include '../messages.php';

if (isset($_GET['department_id'])) {
    $department_id = $_GET['department_id'];
    $query = "SELECT * FROM departments WHERE department_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $department = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 500px;">
            <div class="register-box">
                <form action="submit-edit-department.php" method="post" class="form-container p-2" enctype="multipart/form-data">
                    <input type="hidden" name="department_id" value="<?= htmlspecialchars($department['department_id']) ?>">
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
                            <h4>Edit Department</h4>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="department_name" value="<?= htmlspecialchars($department['department_name']) ?>" placeholder="Department Name" required>
                    </div>

                    <div class="form-group">
                        <label for="current-logo">Current Logo:</label>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#logoModal">
                            View
                        </button>
                    </div>

                    <div class="modal fade" id="logoModal" tabindex="-1" aria-labelledby="logoModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="logoModalLabel">Current Logo</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img src="../uploads/<?= htmlspecialchars($department['logo']) ?>" alt="Department Logo" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="logo">Upload New Logo (Optional):</label>
                        <input type="file" class="form-control mt-1" name="logo">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Update Department</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="../script.js"></script>

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
</script>
