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
$title = "Analytics";
ob_start();
$user_id = $_SESSION['user_id'];
$query = "SELECT department_id FROM ACCOUNTS WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Database Query Failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (!$result || mysqli_num_rows($result) === 0) {
    die("Unable to fetch department information for the logged-in user.");
}
$dean_data = mysqli_fetch_assoc($result);
$department_id = $dean_data['department_id'];  

function fetchAnalysisData($conn, $department_id) { 
    
    $twFormsQuery = "
        SELECT COUNT(*) AS total_forms 
        FROM TW_FORMS 
        WHERE department_id = ?";
    $stmt = mysqli_prepare($conn, $twFormsQuery);
    mysqli_stmt_bind_param($stmt, 'i', $department_id);  
    mysqli_stmt_execute($stmt);
    $twFormsResult = mysqli_stmt_get_result($stmt);
    $totalForms = mysqli_fetch_assoc($twFormsResult)['total_forms'];

    $formsByTypeQuery = "
        SELECT form_type, COUNT(*) AS total 
        FROM TW_FORMS tw
        JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
        WHERE tw.department_id = ?
        GROUP BY tw.form_type";
    $stmt = mysqli_prepare($conn, $formsByTypeQuery);
    mysqli_stmt_bind_param($stmt, 'i', $department_id);
    mysqli_stmt_execute($stmt);
    $formsByTypeResult = mysqli_stmt_get_result($stmt);
    $formsByType = [];
    while ($row = mysqli_fetch_assoc($formsByTypeResult)) {
        $formsByType[] = $row;
    }

    $statusDistributionQuery = "
        SELECT overall_status, COUNT(*) AS total 
        FROM TW_FORMS tw
        JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
        WHERE tw.department_id = ?
        GROUP BY tw.overall_status";
    $stmt = mysqli_prepare($conn, $statusDistributionQuery);
    mysqli_stmt_bind_param($stmt, 'i', $department_id);
    mysqli_stmt_execute($stmt);
    $statusDistributionResult = mysqli_stmt_get_result($stmt);
    $statusDistribution = [];
    while ($row = mysqli_fetch_assoc($statusDistributionResult)) {
        $statusDistribution[] = $row;
    }

    $formsByCRQuery = "
        SELECT cra.agenda_name, COUNT(*) AS total
        FROM TW_FORMS tw
        JOIN college_research_agenda cra ON tw.col_agenda_id = cra.agenda_id
        LEFT JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
        WHERE tw.department_id = ?
        GROUP BY cra.agenda_name";
    $stmt = mysqli_prepare($conn, $formsByCRQuery);
    mysqli_stmt_bind_param($stmt, 'i', $department_id);
    mysqli_stmt_execute($stmt);
    $formsByCRResult = mysqli_stmt_get_result($stmt);
    $formsByCR = [];
    while ($row = mysqli_fetch_assoc($formsByCRResult)) {
        $formsByCR[] = $row;
    }

    $formsByIRQuery = "
        SELECT ira.ir_agenda_name, COUNT(*) AS total
        FROM TW_FORMS tw
        JOIN institutional_research_agenda ira ON tw.ir_agenda_id = ira.ir_agenda_id
        LEFT JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
        WHERE tw.department_id = ?
        GROUP BY ira.ir_agenda_name";
    $stmt = mysqli_prepare($conn, $formsByIRQuery);
    mysqli_stmt_bind_param($stmt, 'i', $department_id);
    mysqli_stmt_execute($stmt);
    $formsByIRResult = mysqli_stmt_get_result($stmt);
    $formsByIR = [];
    while ($row = mysqli_fetch_assoc($formsByIRResult)) {
        $formsByIR[] = $row;
    }

    $attachmentsQuery = "
        SELECT purpose, COUNT(*) AS total
        FROM ATTACHMENTS
        JOIN TW_FORMS tw ON ATTACHMENTS.tw_form_id = tw.tw_form_id
        LEFT JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
        WHERE tw.department_id = ?
        GROUP BY ATTACHMENTS.purpose";
    $stmt = mysqli_prepare($conn, $attachmentsQuery);
    mysqli_stmt_bind_param($stmt, 'i', $department_id); 
    mysqli_stmt_execute($stmt);
    $attachmentsResult = mysqli_stmt_get_result($stmt);
    $attachments = [];
    while ($row = mysqli_fetch_assoc($attachmentsResult)) {
        $attachments[] = $row;
    }

    $missingAttachmentsQuery = "
        SELECT tw.tw_form_id
        FROM TW_FORMS tw
        LEFT JOIN ATTACHMENTS att ON tw.tw_form_id = att.tw_form_id
        LEFT JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
        WHERE tw.department_id = ? AND att.attachment_id IS NULL";
    $stmt = mysqli_prepare($conn, $missingAttachmentsQuery);
    mysqli_stmt_bind_param($stmt, 'i', $department_id); 
    mysqli_stmt_execute($stmt);
    $missingAttachmentsResult = mysqli_stmt_get_result($stmt);
    $missingAttachments = mysqli_num_rows($missingAttachmentsResult);

    $receiptsQuery = "
        SELECT DATE_FORMAT(date_paid, '%Y-%m') AS month, COUNT(*) AS total
        FROM RECEIPTS
        LEFT JOIN TW_FORMS tw ON RECEIPTS.tw_form_id = tw.tw_form_id
        LEFT JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
        WHERE tw.department_id = ?
        GROUP BY DATE_FORMAT(date_paid, '%Y-%m')";
    $stmt = mysqli_prepare($conn, $receiptsQuery);
    mysqli_stmt_bind_param($stmt, 'i', $department_id);  
    mysqli_stmt_execute($stmt);
    $receiptsResult = mysqli_stmt_get_result($stmt);
    $receiptsSummary = [];
    while ($row = mysqli_fetch_assoc($receiptsResult)) {
        $receiptsSummary[] = $row;
    }

    return [
        'totalForms' => $totalForms,
        'formsByType' => $formsByType,
        'statusDistribution' => $statusDistribution,
        'formsByIR' => $formsByIR,
        'formsByCR' => $formsByCR,
        'attachments' => $attachments,
        'missingAttachments' => $missingAttachments,
        'receiptsSummary' => $receiptsSummary,
    ];
}

$dashboardData = fetchAnalysisData($conn, $department_id);
?>

<section id="analytics" class="pt-4">
    <div class="header-container pt-4">
        <h4 class="text-left">Analytics</h4>
    </div>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            
            <div class="col-md-5 mb-4">
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
                <h5 class="text-center">Forms Submitted by Type</h5>
                <canvas id="formsByTypeChart"></canvas>
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
    const formsByType = <?= json_encode($dashboardData['formsByType']) ?>;
    const statusDistribution = <?= json_encode($dashboardData['statusDistribution']) ?>;
    const formsByIR = <?= json_encode($dashboardData['formsByIR']) ?>;
    const formsByCR = <?= json_encode($dashboardData['formsByCR']) ?>;
    const attachments = <?= json_encode($dashboardData['attachments']) ?>;
    const receiptsSummary = <?= json_encode($dashboardData['receiptsSummary']) ?>;
    
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

    
});
</script>


<?php
$content = ob_get_clean();
include('dean-master.php');
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