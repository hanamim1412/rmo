<?php
require '../config/connect.php';

if (isset($_GET['action']) && $_GET['action'] === 'get_courses_and_advisers') {
    $department_id = isset($_GET['department_id']) ? (int)$_GET['department_id'] : 0;

    if ($department_id > 0) {
        
        $queryCourses = "SELECT course_id, course_name FROM courses WHERE department_id = ?";
        $stmtCourses = mysqli_prepare($conn, $queryCourses);
        mysqli_stmt_bind_param($stmtCourses, 'i', $department_id);
        mysqli_stmt_execute($stmtCourses);
        $resultCourses = mysqli_stmt_get_result($stmtCourses);

        $courses = [];
        while ($row = mysqli_fetch_assoc($resultCourses)) {
            $courses[] = $row;
        }

        $queryAdvisers = "SELECT user_id, firstname, lastname 
                          FROM accounts 
                          WHERE department_id = ? AND user_type = 'panelist'";
        $stmtAdvisers = mysqli_prepare($conn, $queryAdvisers);
        mysqli_stmt_bind_param($stmtAdvisers, 'i', $department_id);
        mysqli_stmt_execute($stmtAdvisers);
        $resultAdvisers = mysqli_stmt_get_result($stmtAdvisers);

        $advisers = [];
        while ($row = mysqli_fetch_assoc($resultAdvisers)) {
            $advisers[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode(['courses' => $courses, 'advisers' => $advisers]);
    } else {
        echo json_encode(['courses' => [], 'advisers' => []]);
    }

    exit();
}

?>
