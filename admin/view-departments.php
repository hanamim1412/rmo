<?php
include '../config/connect.php';
include '../messages.php';

// Fetch all departments from the database
$query = "SELECT * FROM departments";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$departments = [];

while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Departments</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-5">
        <div class="table-responsive bg-white p-3" style="border-radius: 10px;">
           <div class="d-flex justify-content-between">
               <h2 class="text-center">Departments List</h2>
               <a href="add-department.php" class="btn btn-danger"><i class="fas fa-plus"></i> Add</a>
           </div>
            <table id="departmentsTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Department Name</th>
                        <th>Logo</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $department): ?>
                        <tr>
                            <td><?= htmlspecialchars($department['department_name']) ?></td>
                            <td>
                                <?php if ($department['logo']): ?>
                                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#logoModal-<?= $department['department_id'] ?>">
                                        View Logo
                                    </button>
                                <?php else: ?>
                                    No Logo
                                <?php endif; ?>
                            </td>
                            <td class="d-flex justify-content-center align-items-center" style="gap: 10px;">
                                <a href="edit-department.php?department_id=<?= $department['department_id'] ?>" class="btn btn-warning">Edit</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="logoModal-<?= $department['department_id'] ?>" tabindex="-1" aria-labelledby="logoModalLabel-<?= $department['department_id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="logoModalLabel-<?= $department['department_id'] ?>">Department Logo</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php if ($department['logo']): ?>
                                            <img src="../uploads/<?= htmlspecialchars($department['logo']) ?>" alt="Department Logo" class="img-fluid">
                                        <?php else: ?>
                                            <p>No logo available</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="../script.js"></script>

    <script>
        $(document).ready(function() {
            $('#departmentsTable').DataTable();
        });
    </script>
</body>
</html>
