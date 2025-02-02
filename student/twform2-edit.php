<?php
// twform2-edit.php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}

include("student-master.php");
require '../config/connect.php';
include '../messages.php';
ob_start();
$title = "TW Form 2: Approval of Proposal Hearing";

$tw_form_id = isset($_GET['tw_form_id']) ? (int)$_GET['tw_form_id'] : 0;
if ($tw_form_id <= 0) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Invalid Form ID"];
    header("Location: tw-forms.php");
    exit();
}

function getFormData($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            twforms.*, 
            form2.thesis_title,
            form2.defense_date,
            form2.time,
            form2.place,
            ir_agenda.ir_agenda_name, 
            cra.agenda_name AS col_agenda_name,
            cra.agenda_id,
            adviser.firstname AS adviser_firstname,
            adviser.lastname AS adviser_lastname
        FROM tw_forms AS twforms 
        JOIN twform_2 AS form2 ON twforms.tw_form_id = form2.tw_form_id 
        LEFT JOIN institutional_research_agenda AS ir_agenda ON twforms.ir_agenda_id = ir_agenda.ir_agenda_id 
        LEFT JOIN college_research_agenda AS cra ON twforms.col_agenda_id = cra.agenda_id 
        LEFT JOIN accounts AS adviser ON twforms.research_adviser_id = adviser.user_id
        WHERE twforms.tw_form_id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $tw_form_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}


function getDepartments() {
    global $conn;
    $query = "SELECT department_id, department_name FROM departments";
    $result = mysqli_query($conn, $query);
    $departments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }
    return $departments;
}

function getCourses($department_id) {
    global $conn;
    $query = "SELECT course_id, course_name FROM courses WHERE department_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $department_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $courses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
    return $courses;
}

function getAdvisers($department_id, $current_adviser_id) {
    global $conn;
    $query = "
        SELECT user_id, firstname, lastname 
        FROM accounts 
        WHERE department_id = ? AND user_type = 'panelist'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $department_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $advisers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['is_selected'] = ($row['user_id'] == $current_adviser_id);
        $advisers[] = $row;
    }
    return $advisers;
}
function getInstitutionalAgenda() {
    global $conn;
    $query = "SELECT ir_agenda_id, ir_agenda_name FROM institutional_research_agenda";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Database Query Failed: " . mysqli_error($conn));
    }

    $ir_agendas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $ir_agendas[] = $row;
    }

    return $ir_agendas;
}
function getCollegeAgenda($department_id, $current_col_agenda_id) {
    global $conn;
    $query = "
        SELECT agenda_id, agenda_name 
        FROM college_research_agenda 
        WHERE department_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $department_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $col_agendas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['is_selected'] = ($row['agenda_id'] == $current_col_agenda_id);
        $col_agendas[] = $row;
    }
    return $col_agendas;
}
function GetProponents($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            pro.proponent_id,
            pro.tw_form_id,
            pro.firstname,
            pro.lastname,
            rp.receipt_id,
            rp.receipt_num,
            rp.receipt_img,
            rp.date_paid
            FROM PROPONENTS pro
            LEFT JOIN TW_FORMS tw ON pro.tw_form_id = tw.tw_form_id
            LEFT JOIN RECEIPTS rp ON pro.receipt_id = rp.receipt_id
            WHERE pro.tw_form_id = ?
        ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database query failed: " . $conn->error);
    }

    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

$departments = getDepartments();
$form_data = getFormData($tw_form_id);
$courses = getCourses($form_data['department_id']);
$advisers = getAdvisers($form_data['department_id'], $form_data['research_adviser_id']);
$col_agendas = getCollegeAgenda($form_data['department_id'], $form_data['agenda_id']);
$ir_agendas = getInstitutionalAgenda();
$proponents = GetProponents($tw_form_id); 

if (!$form_data) {
    $_SESSION['messages'][] = ['tags' => 'danger', 'content' => "Form data not found"];
    header("Location: tw-forms.php");
    exit();
}
?>

<section id="edit-form">
    <div class="header-container">
        <h4 class="text-left">Edit TW Form 1</h4>
    </div>
    <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
        <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
        Back
    </a>

    <div class="container">
        <form id="editTwForm2" method="POST" action="submit-edit-tw2.php" class="form-container">
            <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($tw_form_id) ?>">

            <h2>Edit TW Form 2: Approval of Proposal Hearing</h2>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Department</label>
                    <select name="department_id" class="form-control form-select" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= htmlspecialchars($dept['department_id']) ?>"
                                <?= $form_data['department_id'] == $dept['department_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dept['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Course</label>
                    <select name="course_id" class="form-control form-select" required>
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= htmlspecialchars($course['course_id']) ?>"
                                <?= $form_data['course_id'] == $course['course_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($course['course_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Adviser</label>
                        <input type="text" class="form-control" id="adviser" name="adviser_name" 
                        value="<?= htmlspecialchars($form_data['adviser_firstname'] . ' ' . $form_data['adviser_lastname']) ?>" required>
                        <input type="hidden" id="adviser_id" name="adviser_id" value="<?= htmlspecialchars($form_data['adviser_id'] ?? '') ?>">
                        <div id="adviser-suggestions" class="autocomplete-suggestions"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Institutional Research Agenda</label>
                    <select name="ir_agenda_id" class="form-control form-select" required>
                        <option value="">Select Agenda</option>
                        <?php foreach ($ir_agendas as $agenda): ?>
                            <option value="<?= htmlspecialchars($agenda['ir_agenda_id']) ?>"
                                <?= $form_data['ir_agenda_id'] == $agenda['ir_agenda_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($agenda['ir_agenda_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>College Research Agenda</label>
                    <select name="col_agenda_id" class="form-control form-select" required>
                        <?php foreach ($col_agendas as $agenda): ?>
                            <option value="<?= htmlspecialchars($agenda['agenda_id']) ?>" 
                                <?= $agenda['is_selected'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($agenda['agenda_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Scanned Tw form 1</label>
                    <?php if(!empty($form_data['attachment'])) :?>
                        <?php 
                            $filePath = "../uploads/documents/" . htmlspecialchars($form_data['attachment']);
                            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                            ?>
                            <?php if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp'])): ?>
                            <a href="<?= $filePath ?>" target="_blank">
                                <img src="<?= $filePath ?>" alt="Attachment" class="img-fluid" style="max-width: 150px; max-height: 150px;">
                            </a>
                        <?php else: ?>
                            <a href="<?= $filePath ?>" target="_blank" class="btn btn-sm btn-primary">View/Download Attachment (<?= strtoupper($fileExtension) ?>)</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="badge badge-danger badge-sm">No attachment available</span>
                    <?php endif; ?>
                </div>
                <div class="form-group col-md-4">
                    <label for="document">Upload New Scanned TW form 1</label>
                    <input type="file" name="new_attachment" id="attachment" class="form-control">
                </div>
            </div>

            <div id="proponents-container">
                <label>Proponents</label>
                <?php foreach ($proponents as $proponent): ?>
                    <div class="form-row mt-2">
                        <div class="form-group col-md-4">
                            <input type="text" name="student_firstnames[]" class="form-control mb-1 proponent"
                                value="<?= htmlspecialchars($proponent['firstname']) ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" name="student_lastnames[]" class="form-control mb-1 proponent"
                                value="<?= htmlspecialchars($proponent['lastname']) ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                         <input type="text" name="receipt_number[]" class="form-control" 
                            value="<?= htmlspecialchars($proponent['receipt_num']) ?>"  required>   
                        </div>
                        <div class="form-group col-md-4">
                            <input type="date" name="receipt_date[]" class="form-control" 
                                value="<?= htmlspecialchars($proponent['date_paid']) ?>" required>   
                        </div>
                        <div class="form-group col-md-4">
                            <?php if (!empty($proponent['receipt_img'])): ?>
                            <?php 
                                $file_extension = strtolower(pathinfo($proponent['receipt_img'], PATHINFO_EXTENSION)); 
                                $is_image = in_array($file_extension, ['jpg', 'jpeg', 'png']);
                                ?>
                                <?php if ($is_image): ?>
                                    <a href="javascript:void(0);" data-bs-toggle="modal" class="badge btn-primary text-decoration-none" data-bs-target="#receiptImageModal-<?= $proponent['proponent_id'] ?>">View Image</a>
                                    <a href="../uploads/receipts/<?= htmlspecialchars($proponent['receipt_img']) ?>" download class="badge btn-success text-decoration-none">Download File</a>
                                <?php else: ?>
                                    <a href="../uploads/receipts/<?= htmlspecialchars($proponent['receipt_img']) ?>" target="_blank" class="badge btn-primary text-decoration-none">View PDF</a>
                                <?php endif; ?>
                                <?php else: ?>
                                    <span>No receipt image Available</span>
                                <?php endif; ?>
                                <input type="file" name="receipt_img[]" class="form-control" required>   
                        </div>
                    </div>
                <?php endforeach; ?>
                    <?php foreach ($proponents as $proponent): ?>
                    <?php if (!empty($proponent['receipt_img'])): ?>
                        <?php 
                            $file_extension = strtolower(pathinfo($proponent['receipt_img'], PATHINFO_EXTENSION)); 
                            $is_image = in_array($file_extension, ['jpg', 'jpeg', 'png']);
                        ?>
                        <?php if ($is_image): ?>
                            <div class="modal fade" id="receiptImageModal-<?= $proponent['proponent_id'] ?>" tabindex="-1" aria-labelledby="receiptImageModalLabel-<?= $proponent['proponent_id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="receiptImageModalLabel-<?= $proponent['proponent_id'] ?>">Receipt Image</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="../uploads/receipts/<?= htmlspecialchars($proponent['receipt_img']) ?>" alt="Receipt Image" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div id="titles-container">
                <h5>Thesis Title</h5>
                <div class="form-group">
                    <textarea name="thesis_title" class="form-control mb-1" rows="2" style="text-align: justify;" required>
                        <?= htmlspecialchars($form_data['thesis_title']) ?>
                    </textarea>
                    
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Save</button>
        </form>                    
    </div>
</section>
<script>
$(document).ready(function () {
    $('#editTwForm2').on('submit', function () {
        $('#loadingOverlay').removeClass('d-none'); 
    });

    $(window).on('load', function() {
        $('#loadingOverlay').addClass('d-none'); 
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const adviserInput = document.getElementById('adviser');
    const adviserIdInput = document.getElementById('adviser_id'); 
    const adviserSuggestions = document.getElementById('adviser-suggestions');

    adviserInput.addEventListener('input', function() {
        const query = adviserInput.value.trim();

        if (query.length > 2) { 
            fetch(`autocomplete_adviser.php?q=${query}`)
                .then(response => response.json())
                .then(data => {
                    adviserSuggestions.innerHTML = ''; 
                    if (data.length > 0) {
                        data.forEach(adviser => {
                            const suggestionItem = document.createElement('div');
                            suggestionItem.classList.add('autocomplete-item');
                            suggestionItem.textContent = adviser.firstname + ' ' + adviser.lastname;
                            suggestionItem.addEventListener('click', function() {
                                adviserInput.value = adviser.firstname + ' ' + adviser.lastname; 
                                adviserIdInput.value = adviser.user_id;
                                adviserSuggestions.innerHTML = '';
                            });
                            adviserSuggestions.appendChild(suggestionItem);
                        });
                    } else {
                        adviserSuggestions.innerHTML = '<div class="p-2">No advisers found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching advisers:', error);
                });
        } else {
            adviserSuggestions.innerHTML = '';
        }
    });

});

document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        const closeButton = alert.querySelector('.close');
        closeButton.addEventListener('click', function () {
            alert.style.display = 'none';  
        });

        setTimeout(function () {
            alert.style.display = 'none'; 
        }, 5000);
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
    width: 5rem;
    height: 5rem;
    color: #007bff; 
}

.container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh; 
    }

    .form-container {
        width: 100%;
        max-width: 800px; 
        margin: 0 20px; 
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: white;
    }

    .autocomplete-suggestions {
    position: absolute;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: white;
    max-height: 150px;
    overflow-y: auto;
    width: 90%;
    z-index: 1000;
}
.autocomplete-item {
    padding: 10px;
    cursor: pointer;
}
.autocomplete-item:hover {
    background: #f0f0f0;
}

.suggestion-box .no-suggestions {
    color: #888;
    font-style: italic;
    padding: 5px;
    text-align: center;
}
</style>