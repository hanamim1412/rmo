<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
        header("Location: ../login.php");
        exit();
    }
    include('student-master.php');
    require '../config/connect.php';
    include '../messages.php';
    $title = "TW forms";
    ob_start();

    $user_id = $_SESSION['user_id'] ?? null; 
    function getTWForms($overall_status = null, $user_id = null) {
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
        if ($user_id) {
            $whereClauses[] = "tw.user_id = ?";
            $params[] = intval($user_id);
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
    $twform_details = getTWForms($overall_status, $user_id);
    
    $status = ($overall_status) ? ucfirst($overall_status) : 'All';
?>

<section id="tw-forms" class="pt-4">
    
            <div class="d-flex justify-content-start align-items-center mb-4">
                <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
                    <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
                </a>
                <div class="header-container pt-4">
                    <h4 class="text-left">Submitted Forms</h4>
                </div>
            </div>
            

            <div class="modal fade" id="formTypeModal" tabindex="-1" aria-labelledby="formTypeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="formTypeModalLabel">Select Form Type</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Please select the type of form you want to request:</p>
                            <div class="list-group">
                                <a href="twform_1.php" class="list-group-item list-group-item-action">TW Form 1: Approval of Thesis Title</a>
                                <a href="twform_2.php" class="list-group-item list-group-item-action">TW Form 2: Approval for Proposal Hearing</a>
                                <a href="twform_3.php" class="list-group-item list-group-item-action">TW Form 3: Rating for Proposal Hearing</a>
                                <a href="twform_4.php" class="list-group-item list-group-item-action">TW Form 4: Approval for Oral Examination</a>
                                <a href="twform_5.php" class="list-group-item list-group-item-action">TW Form 5: Rating for Final Defense</a>
                                <a href="twform_6.php" class="list-group-item list-group-item-action">TW Form 6: Approval for Binding</a>
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
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

        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="overall_status" name="overall_status" class="form-select form-select-sm">
                            <option value="">Select Status</option>
                            <option value="pending" <?= ($overall_status === 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= ($overall_status === 'approved') ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= ($overall_status === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                            
                    <div class="col-md-2">
                        <button id="apply-filters" class="btn btn-success btn-sm w-100">Apply Filters</button>
                    </div>
                    <div class="col-md-2">
                            <a href="javascript:void(0);" data-bs-toggle="modal" class="btn btn-success btn-sm text-decoration-none" data-bs-target="#formTypeModal"></i> <strong>Request Form</strong></a>
                    </div>

                </div>
            
                <div class="row">
                        <div class="table-responsive">
                            <table id="items-table" class="table table-bordered table-sm display">
                                <thead class="thead-background">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Form Type</th>
                                        <th scope="col">College</th>
                                        <th scope="col">Course</th>
                                        <th scope="col">Submitted By</th>
                                        <th scope="col">Research Adviser</th>
                                        <th scope="col">attachment</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
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
                                            <td class="text-center">
                                                <?php if (!empty($form['attachment'])): ?>

                                                    <?php 
                                                        $filePath = "../uploads/documents/" . htmlspecialchars($form['attachment']);
                                                        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                                                    ?>
                                                    
                                                    <?php if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp'])): ?>
                                                        <a href="<?= $filePath ?>" target="_blank" class="btn btn-lg btn-outline-success"><i class="fa-regular fa-image"></i></a>
                                                    <?php else: ?>
                                                        <a href="<?= $filePath ?>" target="_blank" class="btn btn-lg btn-outline-success"><i class="fa-regular fa-file"></i></a>
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
                                                        $editPage = 'twform1-edit.php';
                                                        break;
                                                    case 'twform_2':
                                                        $viewPage = 'tw-form2-details.php';
                                                        $editPage = 'twform2-edit.php';
                                                        break;
                                                    case 'twform_3':
                                                        $viewPage = 'tw-form3-details.php';
                                                        $editPage = 'twform3-edit.php';
                                                        break;
                                                    case 'twform_4':
                                                        $viewPage = 'tw-form4-details.php';
                                                        $editPage = 'twform4-edit.php';
                                                        break;
                                                    case 'twform_5':
                                                        $viewPage = 'tw-form5-details.php';
                                                        $editPage = 'twform5-edit.php';
                                                        break;
                                                    case 'twform_6':
                                                        $viewPage = 'tw-form6-details.php';
                                                        $editPage = 'twform6-edit.php';
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
                                                    <div class="d-flex justify-content-between align-items-center mb-1" style="gap: 5px">
                                                        <a href="<?= $viewPage ?>?tw_form_id=<?= $form['tw_form_id'] ?>" class="btn btn-warning btn-sm" id="view"><i class="fa-solid fa-circle-info"></i></a>
                                                        <?php if ($form['overall_status'] == 'pending'): ?>
                                                            <a href="<?= $editPage ?>?tw_form_id=<?= $form['tw_form_id'] ?>" class="btn btn-primary btn-sm" id="edit"><i class="fas fa-edit"></i></a>
                                                            <form action="delete-twform.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this form?');">
                                                                <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($form['tw_form_id']); ?>">
                                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                            </form>
                                                        <?php endif; ?>
                                                        
                                                    </div>
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
include('student-master.php');
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
    width: 10rem;
    height: 10rem;
    color: #007bff; 
}
.thead-background {
    background-color:rgb(56, 120, 193);
    color: white;
}
</style>