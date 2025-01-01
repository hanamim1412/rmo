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

    function getTWForms() {
        global $conn;
        $currentUserId = $_SESSION['user_id'];
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
            LEFT JOIN ACCOUNTS advisor ON tw.research_adviser_id = advisor.user_id AND advisor.user_type = 'panelist'
            WHERE tw.user_id = ? 
            ORDER BY tw.last_updated DESC
        ";
    
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            die("Database Query Failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, 'i', $currentUserId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    
        if (!$result) {
            die("Database Query Failed: " . mysqli_error($conn));
        }
    
        $requests = [
            'pending' => [],
            'approved' => [],
            'rejected' => []
        ];
    
        while ($row = mysqli_fetch_assoc($result)) {
            $status = strtolower($row['overall_status']); 
            if (isset($requests[$status])) {
                $requests[$status][] = $row;
            }
        }
    
        return $requests;
    }
    

    $tw_form_id = $_GET['tw_form_id'] ?? null;

    $twforms_by_status = getTWForms();
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
                <div class="d-flex justify-content-end align-items-center" style="gap: 10px">
                        <a href="javascript:void(0);" data-bs-toggle="modal" class="btn btn-success btn-sm text-decoration-none" data-bs-target="#formTypeModal"></i> <strong>Request Form</strong></a>
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
                                <a href="twform_1.php" class="list-group-item list-group-item-action">TW Form 1</a>
                                <a href="twform_2.php" class="list-group-item list-group-item-action">TW Form 2</a>
                                <a href="twform_3.php" class="list-group-item list-group-item-action">TW Form 3</a>
                                <a href="twform_4.php" class="list-group-item list-group-item-action">TW Form 4</a>
                                <a href="twform_5.php" class="list-group-item list-group-item-action">TW Form 5</a>
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

        <div class="tab-container">
            <ul class="nav nav-tabs mb-3 d-flex justify-content-center justify-content-md-start">
                <li class="nav-item">
                    <a class="nav-link active" id="pendingTab" href="javascript:void(0);">Pending</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="approvedTab" href="javascript:void(0);">Approved</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="rejectedTab" href="javascript:void(0);">Rejected</a>
                </li>
            </ul>
        </div>
    
        <div class="row">
        <?php foreach (['pending', 'approved', 'rejected'] as $status): ?>
            <div id="<?= $status ?>Forms" class="form-section w-100" style="display: <?= $status === 'pending' ? 'block' : 'none'; ?>;">
                <?php if (!empty($twforms_by_status[$status])): ?>
                    <table class="table table-bordered display">
                        <thead class="thead-background">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Form Type</th>
                                <th scope="col">College</th>
                                <th scope="col">Course</th>
                                <th scope="col">Submitted By</th>
                                <th scope="col">Research Adviser</th>
                                <th scope="col">Status</th>
                                <th scope="col">Date</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($twforms_by_status[$status] as $form): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= $form['form_type'] ?></td>
                                    <td><?= $form['department_name'] ?></td> 
                                    <td><?= $form['course_name'] ?></td> 
                                    <td><?= $form['student_firstname'] . ' ' . $form['student_lastname'] ?></td> 
                                    <td><?= $form['adviser_firstname'] . ' ' . $form['adviser_lastname'] ?></td> 
                                    <td><?= ucfirst($form['overall_status']) ?></td>
                                    <td><?= $form['submission_date'] ?></td>
                                    <td>
                                    <?php 
                                        switch ($form['form_type']) {
                                            case 'twform_1':
                                                $viewPage = 'tw-form1-details.php';
                                                $editPage = 'twform1-edit.php';
                                                $pdfPage = 'generate_twform1_pdf.php';
                                                break;
                                            case 'twform_2':
                                                $viewPage = 'tw-form2-details.php';
                                                $editPage = 'twform2-edit.php';
                                                $pdfPage = 'generate_twform2_pdf.php';
                                                break;
                                            case 'twform_3':
                                                $viewPage = 'tw-form3-details.php';
                                                $editPage = 'twform3-edit.php';
                                                $pdfPage = 'generate_twform3_pdf.php';
                                                break;
                                            case 'twform_4':
                                                $viewPage = 'tw-form4-details.php';
                                                $editPage = 'twform4-edit.php';
                                                $pdfPage = 'generate_twform4_pdf.php';
                                                break;
                                            case 'twform_5':
                                                $viewPage = 'tw-form5-details.php';
                                                $editPage = 'twform5-edit.php';
                                                $pdfPage = 'generate_twform5_pdf.php';
                                                break;
                                            default:
                                                $_SESSION['messages'][] = [
                                                    'tags' => 'danger', 
                                                    'content' => "Unknown form type encountered for Form ID: {$form['tw_form_id']}."
                                                ];
                                                $viewPage = 'tw-forms.php'; 
                                                $pdfPage = '';  
                                                break;
                                        }
                                        ?>
                                            <div class="d-flex justify-content-between align-items-center mb-1" style="gap: 5px">
                                                <a href="<?= $viewPage ?>?tw_form_id=<?= $form['tw_form_id'] ?>" class="btn btn-warning btn-sm" id="view">View</a>
                                                <?php if ($form['overall_status'] == 'pending'): ?>
                                                    <a href="<?= $editPage ?>?tw_form_id=<?= $form['tw_form_id'] ?>" class="btn btn-primary btn-sm" id="edit">Edit</a>
                                                    <form action="delete-twform.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this form?');">
                                                        <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($form['tw_form_id']); ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                    </form>
                                                <?php endif; ?>
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
                    <?php else: ?>
                    <p class="text-center text-muted">No <?= ucfirst($status) ?> forms available.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
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
        function showTab(tabId, contentId) {
            document.querySelectorAll('.form-section').forEach(section => {
                section.style.display = 'none';
            });
            document.querySelectorAll('.nav-link').forEach(tab => {
                tab.classList.remove('active');
            });

            const contentElement = document.getElementById(contentId);
            const tabElement = document.getElementById(tabId);

            if (contentElement && tabElement) {
                contentElement.style.display = 'block';
                tabElement.classList.add('active');
            } else {
                console.error(`Element(s) not found: ${contentId} or ${tabId}`);
            }
        }


        document.getElementById('pendingTab').addEventListener('click', function () {
            showTab('pendingTab', 'pendingForms');
        });
        document.getElementById('approvedTab').addEventListener('click', function () {
            showTab('approvedTab', 'approvedForms');
        });
        document.getElementById('rejectedTab').addEventListener('click', function () {
            showTab('rejectedTab', 'rejectedForms');
        });


        showTab('pendingTab', 'pendingForms');
        
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
$(document).ready(function () {
    $('.table.table-bordered').each(function () {
        $(this).DataTable({
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
        }).columns.adjust();
    });
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