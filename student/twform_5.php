<?php
//twform_5.php
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
$title = "TW Form 5: Rating for Oral Examination/Final Defense";

function currentUser() {
    global $conn;
    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT firstname, lastname FROM ACCOUNTS WHERE user_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id); 
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $currentUser = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $currentUser;
}

$department_id = isset($_GET['department_id']) ? (int) $_GET['department_id'] : 0;

function getDepartments() {
    global $conn;
    $query = "SELECT department_id, department_name FROM departments";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$result) {
            die("Database Query Failed: " . mysqli_error($conn));
        }
        
        $departments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $departments[] = $row;
        }

        return $departments;
    } else {
        die("Prepared Statement Failed: " . mysqli_error($conn));
    }
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
    $query = "SELECT agenda_id, agenda_name FROM college_research_agenda WHERE department_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        die("Database Query Failed: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $department_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

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
$currentUser = currentUser();
$departments = getDepartments();
$courses = getCourses($department_id);
$col_agendas = getCollegeAgenda($department_id);
$advisers = getAdvisers($department_id);
$ir_agendas = getInstitutionalAgenda();
?>
<section id="request-form">
        <div class="header-container">
            <h4 class="text-left">Tw Form 5</h4>
        </div>
                <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
                    <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
                    Back
                </a>
    
    <div class="container">
        <form id="twform5" method="POST" action="submit-twform5.php" enctype="multipart/form-data" class="form-container">
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

            <h2>TW Form 5: Rating for Oral Examination/Final Defense</h2>
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
                        <input type="text" class="form-control" id="adviser" name="adviser" placeholder="Type adviser name..." required>
                         <input type="hidden" class="form-control" id="adviser_id" name="adviser_id" required>
                    <div id="adviser-suggestions" class="autocomplete-suggestions"></div>
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
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="student_name">Student Name</label>
                    <input type="text" class="form-control" name="student" id="student_name" 
                    value="<?= ucwords(htmlspecialchars($currentUser['firstname'])).' '. 
                    ucwords(htmlspecialchars($currentUser['lastname'])); ?>" readonly>
                </div>
                <div id="attachment" class="form-group col-md-4">
                    <label for="attachment"> Attach scanned TW form 5 </label>
                    <input type="file" name="attachment" id="document" class="form-control" required>
                </div>
            </div>
                
            <div id="titles-container">
                <h5>Thesis Title</h5>
                <div class="form-group">
                    <textarea name="thesis_title" class="form-control mb-1" rows="2" placeholder="Enter Thesis title" required></textarea>
                </div>
                <h5>Attach Manuscript</h5>
                <div class="form-group col-md-6">
                     <input type="file" name="manuscript" class="form-control" required>
                     <span>file type: pdf</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Submit</button>      

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
    $('#twform5').on('submit', function () {
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
    const colAgendaSelect = document.querySelector('select[name="col_agenda_id"]');
    
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

    departmentSelect.addEventListener('change', () => {
        const departmentId = departmentSelect.value;

        courseSelect.innerHTML = '<option value="">Select Course</option>';
        colAgendaSelect.innerHTML = '<option value="">Select Agenda</option>';

        if (departmentId) {
            fetch(`form.php?action=get_courses_and_agenda&department_id=${departmentId}`)
                .then(response => response.json())
                .then(data => {
                    data.courses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.course_id;
                        option.textContent = course.course_name;
                        courseSelect.appendChild(option);
                    });

                    data.col_agendas.forEach(agenda => {
                        const option = document.createElement('option');
                        option.value = agenda.agenda_id;
                        option.textContent = agenda.agenda_name;
                        colAgendaSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching courses:', error));
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
</style>