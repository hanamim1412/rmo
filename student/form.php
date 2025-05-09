<?php
require '../config/connect.php';

if (isset($_GET['action']) && $_GET['action'] === 'get_courses_and_agenda') {
    $department_id = isset($_GET['department_id']) ? (int) $_GET['department_id'] : 0;

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
        
        $queryAgenda = "SELECT agenda_id, agenda_name FROM college_research_agenda WHERE department_id = ?";
        $stmtAgenda = mysqli_prepare($conn, $queryAgenda);
        mysqli_stmt_bind_param($stmtAgenda, 'i', $department_id);
        mysqli_stmt_execute($stmtAgenda);
        $resultAgenda = mysqli_stmt_get_result($stmtAgenda);

        $col_agendas = [];
        while ($row = mysqli_fetch_assoc($resultAgenda)) {
            $col_agendas[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'courses' => $courses,
            'col_agendas' => $col_agendas
        ]);
    } else {
        echo json_encode([
            'courses' => [],
            'col_agendas' => []
        ]);
    }
    exit();
}
?>
