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
    $title = "TW forms";
    ob_start();

    $user_type = $_SESSION['user_type'];

    $is_rmo_staff = ($user_type === 'rmo_staff');
    function getTWForms($twform_type = null, $overall_status = null) {
        global $conn;
        
        $query = "
            SELECT 
                tw.tw_form_id, 
                tw.form_type,
                tw.user_id,
                tw.ir_agenda_id,
                tw.col_agenda_id,
                ira.ir_agenda_name AS ir_agenda,
                col_agenda.agenda_name AS col_agenda,
                tw.department_id AS department,
                tw.course_id AS course,
                tw.research_adviser_id AS adviser,
                tw.comments,
                tw.overall_status,
                tw.submission_date,
                tw.last_updated,
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
            LEFT JOIN ACCOUNTS advisor ON tw.research_adviser_id = advisor.user_id
        ";
    
        if ($twform_type) {
            $query .= " AND tw.form_type = ? ";
        }
        if ($overall_status) {
            $query .= " AND LOWER(tw.overall_status) = ? ";
        }
    
        $query .= " ORDER BY tw.last_updated DESC";
    
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            die("Database Query Failed: " . mysqli_error($conn));
        }
    
        $params = [];
        $types = '';
        
        if ($twform_type) {
            $params[] = $twform_type;
            $types .= 's'; 
        }
        if ($overall_status) {
            $params[] = strtolower($overall_status);  
            $types .= 's'; 
        }
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
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

$twform_details = getTWForms($twform_type, $overall_status);
$status = ($twform_type || $overall_status) ? ucfirst($overall_status) : 'All';

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
            <select id="twform_type" name="twform_type" class="form-select">
                <option value="">Select Form Type</option>
                <option value="twform_1" <?= ($twform_type === 'twform_1') ? 'selected' : '' ?>>TW Form 1</option>
                <option value="twform_2" <?= ($twform_type === 'twform_2') ? 'selected' : '' ?>>TW Form 2</option>
                <option value="twform_3" <?= ($twform_type === 'twform_3') ? 'selected' : '' ?>>TW Form 3</option>
                <option value="twform_4" <?= ($twform_type === 'twform_4') ? 'selected' : '' ?>>TW Form 4</option>
                <option value="twform_5" <?= ($twform_type === 'twform_5') ? 'selected' : '' ?>>TW Form 5</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="overall_status" name="overall_status" class="form-select">
                <option value="">Select Status</option>
                <option value="pending" <?= ($overall_status === 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= ($overall_status === 'approved') ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= ($overall_status === 'rejected') ? 'selected' : '' ?>>Rejected</option>
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
                        <td><?= ucfirst($form['overall_status']) ?></td>
                        <td><?= $form['submission_date'] ?></td>
                        <td>
                            <?php 
                                switch ($form['form_type']) {
                                    case 'twform_1':
                                        $pdfPage = 'generate_twform1_pdf.php';
                                        break;
                                    case 'twform_2':
                                        $pdfPage = 'generate_twform2_pdf.php';
                                        break;
                                    case 'twform_3':
                                        $pdfPage = 'generate_twform3_pdf.php';
                                        break;
                                    case 'twform_4':
                                        $pdfPage = 'generate_twform4_pdf.php';
                                        break;
                                    case 'twform_5':
                                        $pdfPage = 'generate_twform5_pdf.php';
                                        break;
                                    default:
                                        $_SESSION['messages'][] = [
                                            'tags' => 'danger', 
                                            'content' => "Unknown form type encountered for Form ID: {$form['tw_form_id']}."
                                        ];
                                        $pdfPage = '';  
                                        break;
                                }
                            ?>
                            <div class="d-flex justify-content-between align-items-center mb-1" style="gap: 5px">
                                <?php if ($pdfPage): ?>
                                    <a href="../<?= $pdfPage ?>?tw_form_id=<?= $form['tw_form_id'] ?>&action=I" class="btn btn-success btn-sm" target="_blank">Print</a>
                                    <a href="../<?= $pdfPage ?>?tw_form_id=<?= $form['tw_form_id'] ?>&action=D" class="btn btn-primary btn-sm" target="_blank">Download</a>
                                <?php else: ?>
                                    <span class="text-muted">PDF generation not available for this form type.</span>
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

        var url = window.location.href.split('?')[0] + "?twform_type=" + twform_type + "&overall_status=" + overall_status;
        window.location.href = url;
    });

    $('#twform_type').on('change', function () {
        table.column(1).search(this.value).draw();
    });

    $('#overall_status').on('change', function () {
        table.column(8).search(this.value).draw();
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
include('rmo-master.php');
?>

<style>
.thead-background {
    background-color:rgb(56, 120, 193);
    color: white;
}
</style>