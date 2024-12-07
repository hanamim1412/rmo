<?php
require '../config/connect.php';

if (isset($_GET['department_id']) && isset($_GET['search'])) {
    $department_id = (int)$_GET['department_id'];
    $search_term = '%' . $_GET['search'] . '%';

    $query = "SELECT user_id, firstname, lastname
              FROM accounts
              WHERE department_id = ? AND user_type = 'student' AND (firstname LIKE ? OR lastname LIKE ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iss', $department_id, $search_term, $search_term);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($students);
} else {
    echo json_encode([]);
}

?>
