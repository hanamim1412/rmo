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

function getTWForms($overall_status = null, $dean_department_id = null) {
    global $conn;

    $query = "
        SELECT 
            tw.tw_form_id, 
            tw.form_type,
            tw.user_id,
            tw.ir_agenda_id,
            tw.col_agenda_id,
            tw.department_id AS department,
            tw.course_id AS course,
            tw.research_adviser_id AS adviser,
            tw.comments,
            tw.overall_status,
            tw.attachment,
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
        LEFT JOIN ACCOUNTS advisor ON tw.research_adviser_id = advisor.user_id AND advisor.user_type = 'research_adviser'
    ";
    $whereClauses = [];
    $params = [];
    $types = '';

    if ($overall_status) {
        $whereClauses[] = "LOWER(tw.overall_status) = ?";
        $params[] = strtolower($overall_status);
        $types .= 's';
    }
    if ($dean_department_id) {
        $whereClauses[] = "tw.department_id = ?";
        $params[] = intval($dean_department_id);
        $types .= 'i';
    }
    if (!empty($whereClauses)) {
        $query .= " WHERE " . implode(" AND ", $whereClauses);
    }
    $query .= " ORDER BY tw.last_updated DESC";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Database Query Failed: " . mysqli_error($conn));
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

$overall_status = $_GET['overall_status'] ?? null;
$twform_details = getTWForms($overall_status);

$status = ($overall_status) ? ucfirst($overall_status) : 'All';

?>

<section id="tw-forms" class="pt-4">
    <div class="header-container pt-4">
        <h4 class="text-left">Submitted Forms</h4>
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

        <div class="row">
                <div class="table-responsive">
                    <table id="items-table" class="table table-bordered table-sm display">
                        <thead class="thead-background">
                            <tr>
                                <th>#</th>
                                <th>Form Type</th>
                                <th>College</th>
                                <th>Course</th>
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
                                    <td><?= $form['submission_date'] ?></td>
                                    <td>
                                        <?php 
                                        switch ($form['form_type']) {
                                            case 'twform_1':
                                                $viewPage = 'tw-form1-details.php';
                                                break;
                                            case 'twform_2':
                                                $viewPage = 'tw-form2-details.php';
                                                break;
                                            case 'twform_3':
                                                $viewPage = 'tw-form3-details.php';
                                                break;
                                            case 'twform_4':
                                                $viewPage = 'tw-form4-details.php';
                                                break;
                                            case 'twform_5':
                                                $viewPage = 'tw-form5-details.php';
                                                break;
                                            case 'twform_6':
                                                $viewPage = 'tw-form6-details.php';
                                                break;
                                            default:
                                                    $_SESSION['messages'][] = [
                                                        'tags' => 'danger', 
                                                        'content' => "Unknown form type encountered for Form ID: {$form['tw_form_id']}."
                                                    ];
                                                $viewPage = 'tw-forms.php'; 
                                                break;
                                        }
                                        ?>
                                        <a href="<?= $viewPage ?>?tw_form_id=<?= $form['tw_form_id'] ?>" class="btn btn-warning btn-sm" id="view">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <div id="loadingOverlay" class="d-none">
            <div id="loadingSpinnerContainer" class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

</section>


<script>
$(document).ready(function () {
    $('#view').on('click', function () {
        
        $('#loadingOverlay').removeClass('d-none');

    });
});

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
        var overall_status = $('#overall_status').val();

        var url = window.location.href.split('?')[0] +"&overall_status=" + overall_status;
        window.location.href = url;
    });

    $('#overall_status').on('change', function () {
        const value = $(this).val().toLowerCase();
        table.column(7).search(this.value).draw();
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

