<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}

include('rmo-master.php');
require '../config/connect.php';
include '../messages.php';
$title = "Analytics";
ob_start();

$user_type = $_SESSION['user_type'];

$is_rmo_staff = ($user_type === 'rmo_staff');

function fetchAnalysisData($conn) {
    
    $departmentsQuery = "SELECT COUNT(*) AS total_departments FROM DEPARTMENTS";
    $departmentsResult = mysqli_query($conn, $departmentsQuery);
    $totalDepartments = mysqli_fetch_assoc($departmentsResult)['total_departments'];

    $coursesQuery = "SELECT COUNT(*) AS total_courses FROM COURSES";
    $coursesResult = mysqli_query($conn, $coursesQuery);
    $totalCourses = mysqli_fetch_assoc($coursesResult)['total_courses'];

    $twFormsQuery = "SELECT COUNT(*) AS total_forms FROM TW_FORMS";
    $twFormsResult = mysqli_query($conn, $twFormsQuery);
    $totalForms = mysqli_fetch_assoc($twFormsResult)['total_forms'];

    $formsByTypeQuery = "SELECT form_type, COUNT(*) AS total FROM TW_FORMS GROUP BY form_type";
    $formsByTypeResult = mysqli_query($conn, $formsByTypeQuery);
    $formsByType = [];
    while ($row = mysqli_fetch_assoc($formsByTypeResult)) {
        $formsByType[] = $row;
    }

    $statusDistributionQuery = "SELECT overall_status, COUNT(*) AS total FROM TW_FORMS GROUP BY overall_status";
    $statusDistributionResult = mysqli_query($conn, $statusDistributionQuery);
    $statusDistribution = [];
    while ($row = mysqli_fetch_assoc($statusDistributionResult)) {
        $statusDistribution[] = $row;
    }

    $formsByDepartmentQuery = "
        SELECT dep.department_name, COUNT(*) AS total
        FROM TW_FORMS tw
        JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
        GROUP BY dep.department_name";
    $formsByDepartmentResult = mysqli_query($conn, $formsByDepartmentQuery);
    $formsByDepartment = [];
    while ($row = mysqli_fetch_assoc($formsByDepartmentResult)) {
        $formsByDepartment[] = $row;
    }

    $attachmentsQuery = "
        SELECT purpose, COUNT(*) AS total
        FROM ATTACHMENTS
        GROUP BY purpose";
    $attachmentsResult = mysqli_query($conn, $attachmentsQuery);
    $attachments = [];
    while ($row = mysqli_fetch_assoc($attachmentsResult)) {
        $attachments[] = $row;
    }

    $missingAttachmentsQuery = "
        SELECT tw.tw_form_id
        FROM TW_FORMS tw
        LEFT JOIN ATTACHMENTS att ON tw.tw_form_id = att.tw_form_id
        WHERE att.attachment_id IS NULL";
    $missingAttachmentsResult = mysqli_query($conn, $missingAttachmentsQuery);
    $missingAttachments = mysqli_num_rows($missingAttachmentsResult);

    $receiptsQuery = "
        SELECT DATE_FORMAT(date_paid, '%Y-%m') AS month, COUNT(*) AS total
        FROM RECEIPTS
        GROUP BY DATE_FORMAT(date_paid, '%Y-%m')";
    $receiptsResult = mysqli_query($conn, $receiptsQuery);
    $receiptsSummary = [];
    while ($row = mysqli_fetch_assoc($receiptsResult)) {
        $receiptsSummary[] = $row;
    }
    $formsByIRQuery = "
    SELECT ira.ir_agenda_name, COUNT(*) AS total
    FROM TW_FORMS tw
    JOIN institutional_research_agenda ira ON tw.ir_agenda_id = ira.ir_agenda_id
    GROUP BY ira.ir_agenda_name";
    $formsByIRResult = mysqli_query($conn, $formsByIRQuery);

    $formsByIR = [];
    while ($row = mysqli_fetch_assoc($formsByIRResult)) {
        $formsByIR[] = $row;
    }

    $formsByCRQuery = "
        SELECT cra.agenda_name, COUNT(*) AS total
        FROM TW_FORMS tw
        JOIN college_research_agenda cra ON tw.col_agenda_id = cra.agenda_id
        GROUP BY cra.agenda_name";
    $formsByCRResult = mysqli_query($conn, $formsByCRQuery);

    $formsByCR = [];
    while ($row = mysqli_fetch_assoc($formsByCRResult)) {
        $formsByCR[] = $row;
    }

    $formsByTypeQuery = "
        SELECT form_type, COUNT(*) AS total
        FROM TW_FORMS
        GROUP BY form_type";
    $formsByTypeResult = mysqli_query($conn, $formsByTypeQuery);
    $formsByType = [];
    while ($row = mysqli_fetch_assoc($formsByTypeResult)) {
        $formsByType[] = $row;
    }

    return [
        'totalDepartments' => $totalDepartments,
        'totalCourses' => $totalCourses,
        'totalForms' => $totalForms,
        'formsByType' => $formsByType,
        'statusDistribution' => $statusDistribution,
        'formsByDepartment' => $formsByDepartment,
        'attachments' => $attachments,
        'missingAttachments' => $missingAttachments,
        'receiptsSummary' => $receiptsSummary,
        'formsByIR' => $formsByIR,
        'formsByCR' => $formsByCR
    ];
}

$dashboardData = fetchAnalysisData($conn);
?>


<section id="analytics" class="pt-4">
    <div class="header-container pt-4">
        <h4 class="text-left">Analytics</h4>
    </div>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="col-md-3 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Departments</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $dashboardData['totalDepartments'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Courses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $dashboardData['totalCourses'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">TW Forms Submitted</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $dashboardData['totalForms'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <div>
                <h5 class="text-center">Forms Categorized by Department and Status</h5>
                <canvas id="formsByDepartmentChart"></canvas>
            </div>
            <div>
                <h5 class="text-center">Forms Categorized by Institutional Research Agenda</h5>
                <canvas id="formsByIRChart"></canvas>
            </div>
            <div>
                <h5 class="text-center">Forms Categorized by College Research Agenda</h5>
                <canvas id="formsByCRChart"></canvas>
            </div>
            <div>
                <h5 class="text-center">Submission Status Distribution</h5>
                <canvas id="statusDistributionChart"></canvas>
            </div>
            <div>
                <h5 class="text-center">Forms Submitted by Type</h5>
                <canvas id="formsByTypeChart"></canvas>
            </div>

            <div>
                <h5 class="text-center">Attachments Analysis</h5>
                <canvas id="attachmentsChart"></canvas>
            </div>
            <div>
                <h5 class="text-center">Receipts Summary</h5>
                <canvas id="receiptsChart"></canvas>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formsByDepartment = <?= json_encode($dashboardData['formsByDepartment']) ?>;
    const formsByIR = <?= json_encode($dashboardData['formsByIR']) ?>;
    const formsByCR = <?= json_encode($dashboardData['formsByCR']) ?>;
    const formsByType = <?= json_encode($dashboardData['formsByType']) ?>;
    const statusDistribution = <?= json_encode($dashboardData['statusDistribution']) ?>;
    const attachments = <?= json_encode($dashboardData['attachments']) ?>;
    const receiptsSummary = <?= json_encode($dashboardData['receiptsSummary']) ?>;

    const ctxDept = document.getElementById('formsByDepartmentChart').getContext('2d');
    new Chart(ctxDept, {
        type: 'bar',
        data: {
            labels: [...new Set(formsByDepartment.map(f => f.department_name))],
            datasets: formsByDepartment.reduce((acc, curr) => {
                let ds = acc.find(d => d.label === curr.overall_status);
                if (!ds) {
                    ds = { label: curr.overall_status, data: [], backgroundColor: 'rgba(75, 192, 192, 0.6)' };
                    acc.push(ds);
                }
                ds.data.push(curr.total);
                return acc;
            }, [])
        },
    });

    const ctxIR = document.getElementById('formsByIRChart').getContext('2d');
    new Chart(ctxIR, {
        type: 'bar',
        data: {
            labels: formsByIR.map(f => f.ir_agenda_name),
            datasets: [{
                label: 'TW Forms',
                data: formsByIR.map(f => f.total),
                backgroundColor: 'rgba(153, 102, 255, 0.6)',
            }],
        },
    });

    const ctxCR = document.getElementById('formsByCRChart').getContext('2d');
    new Chart(ctxCR, {
        type: 'bar',
        data: {
            labels: formsByCR.map(f => f.agenda_name),
            datasets: [{
                label: 'TW Forms',
                data: formsByCR.map(f => f.total),
                backgroundColor: 'rgba(255, 159, 64, 0.6)',
            }],
        },
    });


    const ctxStatus = document.getElementById('statusDistributionChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'pie',
        data: {
            labels: statusDistribution.map(f => f.overall_status),
            datasets: [{
                data: statusDistribution.map(f => f.total),
                backgroundColor: ['rgba(75, 192, 192, 0.6)', 'rgba(255, 99, 132, 0.6)', 'rgba(255, 206, 86, 0.6)'],
            }],
        },
    });

    const ctxAttachments = document.getElementById('attachmentsChart').getContext('2d');
    new Chart(ctxAttachments, {
        type: 'bar',
        data: {
            labels: attachments.map(f => f.purpose),
            datasets: [{
                label: 'Attachments',
                data: attachments.map(f => f.total),
                backgroundColor: 'rgba(153, 102, 255, 0.6)',
            }],
        },
    });

    const ctxReceipts = document.getElementById('receiptsChart').getContext('2d');
    new Chart(ctxReceipts, {
        type: 'line',
        data: {
            labels: receiptsSummary.map(f => f.month),
            datasets: [{
                label: 'Receipts',
                data: receiptsSummary.map(f => f.total),
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: true,
            }],
        },
    });

    const ctxType = document.getElementById('formsByTypeChart').getContext('2d');
    new Chart(ctxType, {
        type: 'bar',
        data: {
            labels: formsByType.map(f => f.form_type),
            datasets: [{
                label: 'Forms Submitted',
                data: formsByType.map(f => f.total),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return `${tooltipItem.dataset.label}: ${tooltipItem.raw}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Form Type',
                    },
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Submissions',
                    },
                },
            },
        },
    });
});
</script>

<?php
$content = ob_get_clean();
include('rmo-master.php');
?>


<style>
    .chart-container {
    width: 100%;
    margin: 0 auto;
    margin-top: 30px;
    display: flex;
    flex-wrap: wrap; 
    justify-content: space-around;
    gap: 20px;
}

canvas {
    width: 400px !important;
    height: 400px !important;
    border-radius: 8px;
    border: solid 1px blue;
}


    .card {
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 1rem;
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #343a40;
        margin-bottom: 10px;
    }

    .card-body {
        padding: 1.5rem;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        gap: 20px;
    }

    .card.border-left-primary {
        border-left: 5px solid #007bff;
    }

    .card.border-left-danger {
        border-left: 5px solid #dc3545;
    }

    .card-body .h5 {
        font-size: 1.75rem;
        font-weight: bold;
        color: #007bff;
    }

</style>