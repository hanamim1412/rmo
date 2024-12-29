<?php
//twform_2.php
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
$title = "TW Form 2: Approval for Proposal Hearing";

$department_id = isset($_GET['department_id']) ? (int) $_GET['department_id'] : 0;

function getDepartments() {
    global $conn;
    $query = "SELECT department_id, department_name FROM departments";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Database Query Failed: " . mysqli_error($conn));
    }

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
function getCollegeAgenda($department_id) {
    global $conn;
    $query = "SELECT agenda_id, agenda_name FROM college_research_agenda";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Database Query Failed: " . mysqli_error($conn));
    }

    $col_agendas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $col_agendas[] = $row;
    }

    return $col_agendas;
}
function getAdvisers($department_id) {
    global $conn;
    $query = "SELECT user_id, firstname, lastname 
              FROM accounts 
              WHERE department_id = ? AND user_type = 'panelist'";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $department_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $advisers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $advisers[] = $row;
    }

    return $advisers;
}

$departments = getDepartments();
$courses = getCourses($department_id);
$col_agendas = getCollegeAgenda($department_id);
$advisers = getAdvisers($department_id);
$ir_agendas = getInstitutionalAgenda();
?>
<section id="request-form">
        <div class="header-container">
            <h4 class="text-left">Tw Form 2</h4>
        </div>
                <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
                    <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
                    Back
                </a>
    
    <div class="container">
        <form id="twform2" method="POST" action="submit-twform2.php" enctype="multipart/form-data" class="form-container">
            <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="alert alert-<?= htmlspecialchars($message['tags']) ?> alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i><?= htmlspecialchars($message['content']) ?>
                            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endforeach; ?>
            <?php endif; ?>

            <h2>TW Form 2: Approval for Proposal Hearing</h2>
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Department</label>
                    <select name="department_id" class="form-control form-select" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= htmlspecialchars($dept['department_id']) ?>">
                                <?= htmlspecialchars($dept['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Course</label>
                    <select name="course_id" class="form-control form-select" required>
                        <option value="">Select Course</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Adviser</label>
                    <select name="adviser_id" class="form-control form-select" required>
                        <option value="">Select Adviser</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Institutional Research Agenda</label>
                    <select name="ir_agenda_id" class="form-control form-select select-sm" required>
                        <option value="">Select Agenda</option>
                        <?php if (!empty($ir_agendas)): ?>
                            <?php foreach ($ir_agendas as $agenda): ?>
                                <option value="<?= htmlspecialchars($agenda['ir_agenda_id']) ?>">
                                    <?= htmlspecialchars($agenda['ir_agenda_name']) ?>
                                </option>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <option value="">No agendas found</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>College Research Agenda</label>
                    <select name="col_agenda_id" class="form-control form-select select-sm" required>
                        <option value="">Select Agenda</option>
                        <?php if (!empty($col_agendas)): ?>
                            <?php foreach ($col_agendas as $agenda): ?>
                                <option value="<?= htmlspecialchars($agenda['agenda_id']) ?>">
                                    <?= htmlspecialchars($agenda['agenda_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No agendas found</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div id="proponents-container">
                <h5>Proponents and receipt details</h5>
                <div class="form-row mt-2 align-items-center">
                    <div class="form-group col-md-4">
                        <input type="text" name="student_firstnames[]" class="form-control mb-1 proponent" placeholder="Enter firstname" required>
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" name="student_lastnames[]" class="form-control mb-1 proponent" placeholder="Enter lastname" required>
                    </div>
                    <div class="form-group col-md-4">
                         <input type="text" name="receipt_number[]" class="form-control" placeholder="Enter receipt #" required>   
                    </div>
                    <div class="form-group col-md-4">
                         <input type="file" name="receipt_img[]" class="form-control" required>   
                    </div>
                    <div class="form-group col-md-2">
                         <input type="date" name="receipt_date[]" class="form-control"required>   
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-sm add-proponent">Add</button>
            </div>

            <div id="titles-container">
                <h5>Thesis Title</h5>
                <div class="form-group">
                    <textarea name="thesis_title" class="form-control mb-1" rows="2" placeholder="Enter Thesis title" required></textarea>
                </div>
            </div>

            <h5>Proposal Hearing Details</h5>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Defense Date</label>
                    <input type="date" name="defense_date" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Defense Time</label>
                    <input type="time" name="defense_time" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Defense Place</label>
                    <input type="text" name="defense_place" class="form-control" placeholder="Enter place" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Submit</button>   
            <button type="button" id="reset-button" class="btn btn-secondary btn-sm">Reset</button>        

        </form>
        <div id="loadingOverlay" class="d-none">
            <div id="loadingSpinnerContainer" class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>                
</section>

<script>
$(document).ready(function () {
    $('#twform2').on('submit', function () {
        $('#loadingOverlay').removeClass('d-none'); 
    });

    $(window).on('load', function() {
        $('#loadingOverlay').addClass('d-none'); 
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

document.addEventListener('DOMContentLoaded', () => {
    const departmentSelect = document.querySelector('select[name="department_id"]');
    const courseSelect = document.querySelector('select[name="course_id"]');
    const adviserSelect = document.querySelector('select[name="adviser_id"]');
    const proponentsContainer = document.getElementById('proponents-container');

        function addProponent() {
        const newProponent = document.createElement('div');
        newProponent.classList.add('form-row', 'mt-2', 'align-items-center');

        newProponent.innerHTML = `
            <div class="form-group col-md-4">
                <input type="text" name="student_firstnames[]" class="form-control mb-1 proponent" placeholder="Enter firstname" required>
            </div>
            <div class="form-group col-md-4">
                <input type="text" name="student_lastnames[]" class="form-control mb-1 proponent" placeholder="Enter lastname" required>
            </div>
            <div class="form-group col-md-4">
                <input type="text" name="receipt_number[]" class="form-control" placeholder="Enter receipt #" required>   
            </div>
            <div class="form-group col-md-4">
                <input type="file" name="receipt_img[]" class="form-control" required>   
            </div>
            <div class="form-group col-md-2">
                <input type="date" name="receipt_date[]" class="form-control" required>   
            </div>
            <div class="form-group col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-proponent">Remove</button>
            </div>
        `;

        proponentsContainer.appendChild(newProponent);

        const removeButton = newProponent.querySelector('.remove-proponent');
        removeButton.addEventListener('click', () => {
            newProponent.remove();
        });
    }

        function attachAddProponentListener() {
            const addButton = document.querySelector('.add-proponent');
            addButton.addEventListener('click', addProponent);
        }

    document.getElementById('reset-button').addEventListener('click', () => {
    document.getElementById('twform2').reset();

    proponentsContainer.innerHTML = `
        <label>Proponents and receipt details</label>
        <div class="form-row mt-2 align-items-center">
            <div class="form-group col-md-4">
                <input type="text" name="student_firstnames[]" class="form-control mb-1 proponent" placeholder="Enter firstname" required>
            </div>
            <div class="form-group col-md-4">
                <input type="text" name="student_lastnames[]" class="form-control mb-1 proponent" placeholder="Enter lastname" required>
            </div>
            <div class="form-group col-md-4">
                <input type="text" name="receipt_number[]" class="form-control" placeholder="Enter receipt #" required>   
            </div>
            <div class="form-group col-md-4">
                <input type="file" name="receipt_img[]" class="form-control" required>   
            </div>
            <div class="form-group col-md-2">
                <input type="date" name="receipt_date[]" class="form-control" required>   
            </div>
        </div>
        <button type="button" class="btn btn-success btn-sm add-proponent">Add</button>
    `;

        attachAddProponentListener();
    });

    attachAddProponentListener();

    departmentSelect.addEventListener('change', () => {
        const departmentId = departmentSelect.value;

        courseSelect.innerHTML = '<option value="">Select Course</option>';
        adviserSelect.innerHTML = '<option value="">Select Adviser</option>';

        if (departmentId) {
            fetch(`form.php?action=get_courses_and_advisers&department_id=${departmentId}`)
                .then(response => response.json())
                .then(data => {
                    data.courses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.course_id;
                        option.textContent = course.course_name;
                        courseSelect.appendChild(option);
                    });

                    data.advisers.forEach(adviser => {
                        const option = document.createElement('option');
                        option.value = adviser.user_id;
                        option.textContent = `${adviser.firstname} ${adviser.lastname}`;
                        adviserSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching courses and advisers:', error));
        }
    });

});

</script>


<?php
    $content = ob_get_clean();
    include('student-master.php');
?>

<style>
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh; 
    }

    .form-container {
        width: 100%;
        max-width: 1000px; 
        margin: 0 20px; 
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: white;
    }
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
</style>