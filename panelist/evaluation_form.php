<?php 
// panelist/evaluation_form.php
session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
        header("Location: ../login.php");
        exit();
    }
    include('panelist-master.php');
    require '../config/connect.php';
    include '../messages.php';
    $title = "Evaluation Form";
    ob_start();

    $tw_form_id = isset($_GET['tw_form_id']) ? (int) $_GET['tw_form_id'] : 0;
function getFormTypeByTwFormId($tw_form_id) {
    global $conn; 

    $query = "SELECT form_type FROM tw_forms WHERE tw_form_id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Database query failed: " . $conn->error);
    }

    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['form_type'];
    }

    return null;
}
$form_type = getFormTypeByTwFormId($tw_form_id);
?>
<section id="evaluation-form">
    <div class="header-container">
        <h4 class="text-left">Evaluation Form</h4>
    </div>
    <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
        <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
        Back
    </a>

    <div class="container">
        <div class="register-box" id="view">
            <form action="submit_evaluation.php" method="POST" class="form-control">
            <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($tw_form_id) ?>">
            <input type="hidden" name="evaluator_id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">
            
                <table id="items-table" class="table table-bordered display">
                    <thead class="thead-background">
                        <tr>
                            <th colspan="2" class="text-center">Evaluation Criteria</th>
                            <th class="text-center">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="3" class="align-middle">Presentation of the Paper (50 pts.)</td>
                            <td>Presentation (15 pts.)</td>
                            <td><input type="number" name="presentation" class="form-control" style="border: 1px solid rgb(10, 62, 140); padding: 5px 10px;" max="15" min="0" required></td>
                        </tr>
                        <tr>
                            <td>Content (25 pts.)</td>
                            <td><input type="number" name="content" class="form-control" style="border: 1px solid rgb(10, 62, 140); padding: 5px 10px;" max="25" min="0" required></td>
                        </tr>
                        <tr>
                            <td>Organization (10 pts.)</td>
                            <td><input type="number" name="organization" class="form-control" style="border: 1px solid rgb(10, 62, 140); padding: 5px 10px;" max="10" min="0" required></td>
                        </tr>
                        <tr>
                            <td colspan="2">Mastery of the Subject Matter (20 pts.)</td>
                            <td><input type="number" name="mastery" class="form-control" style="border: 1px solid rgb(10, 62, 140); padding: 5px 10px;" max="20" min="0" required></td>
                        </tr>
                        <tr>
                            <td colspan="2">Ability to Respond to Questions (20 pts.)</td>
                            <td><input type="number" name="ability" class="form-control" style="border: 1px solid rgb(10, 62, 140); padding: 5px 10px;" max="20" min="0" required></td>
                        </tr>
                        <tr>
                            <td colspan="2">Openness Towards the Given Suggestions (10 pts.)</td>
                            <td><input type="number" name="openness" class="form-control" style="border: 1px solid rgb(10, 62, 140); padding: 5px 10px;" max="10" min="0" required></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-weight-bold">Overall Rating (Sum of Scores)</td>
                            <td>
                                <input type="number" name="overall_rating" class="form-control" style="border: 1px solid rgb(10, 62, 140); padding: 5px 10px;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-weight-bold">Percentage</td>
                            <td>
                                <input type="text" name="percentage" class="form-control" style="border: 1px solid rgb(10, 62, 140); padding: 5px 10px;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-weight-bold">Remarks</td>
                            <td>
                                <textarea name="remarks" class="form-control" style="border: 1px solid rgb(10, 62, 140); padding: 5px 10px;" rows="3" maxlength="500"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">Submit Evaluation</button>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', updateEvaluation);
});

function updateEvaluation() {
    const presentation = parseInt(document.querySelector('input[name="presentation"]').value) || 0;
    const content = parseInt(document.querySelector('input[name="content"]').value) || 0;
    const organization = parseInt(document.querySelector('input[name="organization"]').value) || 0;
    const mastery = parseInt(document.querySelector('input[name="mastery"]').value) || 0;
    const ability = parseInt(document.querySelector('input[name="ability"]').value) || 0;
    const openness = parseInt(document.querySelector('input[name="openness"]').value) || 0;

    const overall = presentation + content + organization + mastery + ability + openness;

    console.log("Overall Score:", overall);

    let percentage = 0;
    let remarks = "";

    if (overall >= 0 && overall <= 59) {
        percentage = 70;
        remarks = "Failed";
    } else if (overall === 60) {
        percentage = 75;
        remarks = "Passed";
    } else if (overall >= 61 && overall <= 62) {
        percentage = 76;
        remarks = "Passed";
    } else if (overall === 63) {
        percentage = 77;
        remarks = "Passed";
    } else if (overall >= 64 && overall <= 65) {
        percentage = 78;
        remarks = "Passed";
    } else if (overall >= 66 && overall <= 67) {
        percentage = 79;
        remarks = "Passed";
    } else if (overall === 68) {
        percentage = 80;
        remarks = "Passed";
    } else if (overall >= 69 && overall <= 70) {
        percentage = 81;
        remarks = "Passed";
    } else if (overall === 71) {
        percentage = 82;
        remarks = "Passed";
    } else if (overall >= 72 && overall <= 73) {
        percentage = 83;
        remarks = "Passed";
    } else if (overall >= 74 && overall <= 75) {
        percentage = 84;
        remarks = "Passed";
    } else if (overall === 76) {
        percentage = 85;
        remarks = "Passed";
    } else if (overall >= 77 && overall <= 78) {
        percentage = 86;
        remarks = "Passed";
    } else if (overall === 79) {
        percentage = 87;
        remarks = "Passed";
    } else if (overall >= 80 && overall <= 81) {
        percentage = 88;
        remarks = "Passed";
    } else if (overall >= 82 && overall <= 83) {
        percentage = 89;
        remarks = "Passed";
    } else if (overall === 84) {
        percentage = 90;
        remarks = "Passed";
    } else if (overall >= 85 && overall <= 86) {
        percentage = 91;
        remarks = "Passed";
    } else if (overall === 87) {
        percentage = 92;
        remarks = "Passed";
    } else if (overall >= 88 && overall <= 89) {
        percentage = 93;
        remarks = "Passed";
    } else if (overall >= 90 && overall <= 91) {
        percentage = 94;
        remarks = "Passed";
    } else if (overall === 92) {
        percentage = 95;
        remarks = "Passed";
    } else if (overall >= 93 && overall <= 94) {
        percentage = 96;
        remarks = "Passed";
    } else if (overall === 95) {
        percentage = 97;
        remarks = "Passed";
    } else if (overall >= 96 && overall <= 97) {
        percentage = 98;
        remarks = "Passed";
    } else if (overall >= 98 && overall <= 99) {
        percentage = 99;
        remarks = "Passed";
    } else if (overall === 100) {
        percentage = 100;
        remarks = "Passed";
    }


    document.querySelector('input[name="overall_rating"]').value = overall;

    const percentageInput = document.querySelector('input[name="percentage"]');
    if (percentageInput) {
        percentageInput.value = `${percentage}% (${remarks})`;
    }
}

window.onload = updateEvaluation;

$(document).ready(function () {
    $('#view').on('submit', function () {
        
        $('#loadingOverlay').removeClass('d-none');

    });
});
</script>
<?php
$content = ob_get_clean();
include('panelist-master.php');
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