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
    $title = "TW forms";
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
$dean_department_id = $dean_data['department_id'];

function getTWForms($dean_department_id, $twform_type = null, $overall_status = null,  $school_year = null) {
    global $conn;
    
    $query = "
        SELECT 
            tw.tw_form_id, 
            tw.form_type,
            tw.user_id,
            ira.ir_agenda_name AS ir_agenda,
            col_agenda.agenda_name AS col_agenda,
            tw.department_id AS department,
            tw.course_id AS course,
            tw.research_adviser_id AS adviser,
            tw.comments,
            tw.overall_status,
            tw.submission_date,
            tw.last_updated,
            tw.attachment,
            YEAR(tw.last_updated) AS last_updated_year,
            u.firstname AS student_firstname, 
            u.lastname AS student_lastname,
            dep.department_name AS department_name,
            cou.course_name AS course_name,
            advisor.firstname AS adviser_firstname,
            advisor.lastname AS adviser_lastname
        FROM TW_FORMS tw
        LEFT JOIN ACCOUNTS u ON tw.user_id = u.user_id
        LEFT JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
        LEFT JOIN COURSES cou ON tw.course_id = cou.course_id
        LEFT JOIN institutional_research_agenda ira ON tw.ir_agenda_id = ira.ir_agenda_id
        LEFT JOIN college_research_agenda col_agenda ON tw.col_agenda_id = col_agenda.agenda_id
        LEFT JOIN ACCOUNTS advisor ON tw.research_adviser_id = advisor.user_id AND advisor.user_type = 'research_adviser'
        WHERE tw.department_id = ? ";

    if ($twform_type) {
        $query .= " AND tw.form_type = ? ";
    }
    if ($overall_status) {
        $query .= " AND LOWER(tw.overall_status) = ? ";
    }
    if ($school_year) {
        list($start_year, $end_year) = explode('-', $school_year);
    
        $query .= " AND (YEAR(tw.last_updated) BETWEEN ? AND ?) ";
    }
    $query .= " ORDER BY tw.last_updated DESC";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Database Query Failed: " . mysqli_error($conn));
    }

    $params = [$dean_department_id];
    if ($twform_type) {
        $params[] = $twform_type;
    }
    if ($overall_status) {
        $params[] = strtolower($overall_status);  
    }
    if ($school_year) {
        list($start_year, $end_year) = explode('-', $school_year);
        
        $start_year = (int)$start_year;
        $end_year = (int)$end_year;
        
        $query .= " AND (YEAR(tw.last_updated) BETWEEN ? AND ?) ";
    }
    mysqli_stmt_bind_param($stmt, str_repeat('s', count($params)), ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Database Query Failed: " . mysqli_error($conn));
    }
    $twform_details = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $twform_details[] = $row;
    }

    return $twform_details;
}


$twform_type = $_GET['twform_type'] ?? null;
$overall_status = $_GET['overall_status'] ?? null;
$school_year = $_GET['school_year'] ?? null;
$twform_details = getTWForms($dean_department_id, $twform_type, $overall_status);

$status = ($twform_type || $overall_status || $school_year) ? ucfirst($overall_status) : 'All';

?>

<section id="tw-forms" class="pt-4">
    <div class="header-container pt-4">
        <h4 class="text-left">Reports</h4>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
            <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
            Back
        </a>
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

    <div class="row mb-3">
        <div class="col-md-3">
            <select id="twform_type" class="form-select">
                <option value="">Select Form Type</option>
                <option value="twform_1">TW Form 1</option>
                <option value="twform_2">TW Form 2</option>
                <option value="twform_3">TW Form 3</option>
                <option value="twform_4">TW Form 4</option>
                <option value="twform_5">TW Form 5</option>
                <option value="twform_6">TW Form 6</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="overall_status" class="form-select">
                <option value="">Select Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        <div class="col-md-3">
            <select id="school_year" name="school_year" class="form-select">
                <option value="">Select School Year</option>
                <option value="2023-2024" <?= ($school_year === '2023-2024') ? 'selected' : '' ?>>2023-2024</option>
                <option value="2024-2025" <?= ($school_year === '2024-2025') ? 'selected' : '' ?>>2024-2025</option>
                <option value="2025-2026" <?= ($school_year === '2025-2026') ? 'selected' : '' ?>>2025-2026</option>
            </select>
        </div>
        <div class="col-md-2">
            <button id="apply-filters" class="btn btn-success btn-sm w-100">Apply Filters</button>
        </div>
    </div>

    <div class="table-responsive">
        <table id="items-table" class="table table-bordered table-sm display">
            <thead class="thead-background">
                <tr>
                    <th>#</th>
                    <th>Form Type</th>
                    <th>College</th>
                    <th>Course</th>
                    <th>Institutional Research Agenda</th>
                    <th>College Research Agenda</th>
                    <th>Submitted By</th>
                    <th>Research Adviser</th>
                    <th>Attachment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($twform_details as $form): ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= $form['form_type'] ?></td>
                        <td><?= $form['department_name'] ?></td> 
                        <td><?= $form['course_name'] ?></td> 
                        <td><?= $form['ir_agenda'] ?></td> 
                        <td><?= $form['col_agenda'] ?></td>
                        <td><?= $form['student_firstname'] . ' ' . $form['student_lastname'] ?></td> 
                        <td><?= $form['adviser_firstname'] . ' ' . $form['adviser_lastname'] ?></td> 
                        <td>
                                        <?php if (!empty($form['attachment'])): ?>

                                            <?php 
                                                $filePath = "../uploads/documents/" . htmlspecialchars($form['attachment']);
                                                $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                                            ?>
                                            
                                            <?php if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp'])): ?>
                                                <a href="<?= $filePath ?>" target="_blank" class="btn btn-sm btn-success">View Attachment (<?= strtoupper($fileExtension) ?>)</a>
                                            <?php else: ?>
                                                <a href="<?= $filePath ?>" target="_blank" class="btn btn-sm btn-success">View Attachment (<?= strtoupper($fileExtension) ?>)</a>
                                            <?php endif; ?>

                                        <?php else: ?>
                                            <span class="badge badge-danger badge-sm">No attachment available.</span>
                                        <?php endif; ?>
                                    </td>
                        <td><?= ucfirst($form['overall_status']) ?></td>
                        <td><?= $form['last_updated_year'] ?></td>
                        <td>
                            <?php 
                                switch ($form['form_type']) {
                                    case 'twform_1':
                                        $printPage = 'print_twform1.php';
                                        break;
                                    case 'twform_2':
                                        $printPage = 'print_twform2.php';
                                        break;
                                    case 'twform_3':
                                        $printPage = 'print_twform3.php';
                                        break;
                                    case 'twform_4':
                                        $printPage = 'print_twform4.php';
                                        break;
                                    case 'twform_5':
                                        $printPage = 'print_twform5.php';
                                        break;
                                    case 'twform_6':
                                        $printPage = 'print_twform6.php';
                                        break;
                                    default:
                                        $_SESSION['messages'][] = [
                                            'tags' => 'danger', 
                                            'content' => "Unknown form type encountered for Form ID: {$form['tw_form_id']}."
                                        ];
                                        $printPage = '';  
                                        break;
                                }
                            ?>
                            <div class="d-flex justify-content-between align-items-center mb-1" style="gap: 5px">
                                <?php if ($printPage): ?>
                                    <a href="../<?= $printPage ?>?tw_form_id=<?= $form['tw_form_id'] ?>" class="btn btn-success btn-sm"><i class="fa-solid fa-print"></i></a>
                                <?php else: ?>
                                    <span class="text-muted">Print not available for this form type.</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>

</section>


<script>
$(document).ready(function () {
    var table = $('#items-table').DataTable({
        scrollX: true,
        autoWidth: false,
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 5,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: "Prev",
                next: "Next"
            }
        }
    });

    $('#apply-filters').click(function () {
        var twform_type = $('#twform_type').val();
        var overall_status = $('#overall_status').val();
        var school_year = $('#school_year').val();

        var url = window.location.href.split('?')[0] + 
            "?twform_type=" + twform_type + 
            "&overall_status=" + overall_status + 
            "&school_year=" + school_year;
        window.location.href = url;
    });

    $('#twform_type').on('change', function () {
        table.column(1).search(this.value).draw();
    });

    $('#overall_status').on('change', function () {
        table.column(9).search(this.value).draw();
    });
    $('#school_year').on('change', function () {
        
        var selectedYear = this.value;
        if (selectedYear) {
            table.column(10).search(selectedYear.split('-')[0]).draw(); 
        } else {
            table.column(10).search('').draw();
        }
    });
});


        
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