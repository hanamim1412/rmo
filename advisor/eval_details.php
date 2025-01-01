<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
    header("Location: ../login.php");
    exit();
}
include('advisor-master.php');
require '../config/connect.php';
include '../messages.php';
$title = "Evaluation Ratings";
ob_start();

$tw_form_id = isset($_GET['tw_form_id']) ? (int) $_GET['tw_form_id'] : 0;

function getEvalCriteria($tw_form_id) {
    global $conn;
    $query = "
        SELECT
            ev.eval_id,
            ev.tw_form_id,
            ev.evaluator_id,
            acc.firstname as eval_firstname,
            acc.lastname as eval_lastname,
            ev.presentation,
            ev.content,
            ev.organization,
            ev.mastery,
            ev.ability,
            ev.openness,
            ev.overall_rating,
            ev.percentage,
            ev.remarks,
            ev.date_created
        FROM eval_criteria ev
        LEFT JOIN TW_FORMS tw ON ev.tw_form_id = tw.tw_form_id
        LEFT JOIN ACCOUNTS acc ON ev.evaluator_id = acc.user_id
        WHERE ev.tw_form_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $eval_criteria = [];
    while ($row = $result->fetch_assoc()) {
        $eval_criteria[] = $row;
    }
    
    return $eval_criteria;
}
 
$eval_criteria_list = getEvalCriteria($tw_form_id);
?>
<section id="evaluation-lists">
    <div class="header-container">
        <h4 class="text-left">Evaluation Ratings</h4>
    </div>
    <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
        <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
        Back
    </a>

    <div class="table-container mt-4">
    <?php if ($eval_criteria_list): ?>
        <?php foreach ($eval_criteria_list as $eval_criteria): ?>        
        <table id="items-table" class="table table-bordered display">
            <thead class="thead-background">
                <tr>
                    <th colspan="2" class="text-center">Evaluation Criteria</th>
                    <th class="text-center">Score</th>
                </tr>
            </thead>
            <tbody>
                
                        <tr>
                            <td colspan="2"><h5>Evaluator</h5></td>
                            <td><h5><?= ucwords(htmlspecialchars($eval_criteria['eval_firstname'])).' '. ucwords(htmlspecialchars($eval_criteria['eval_lastname'])) ?></h5></td>
                        </tr>
                        <tr>
                            <td rowspan="3" class="align-middle">Presentation of the Paper (50 pts.)</td>
                            <td>Presentation (15 pts.)</td>
                            <td><input type="number" name="presentation" class="form-control" value="<?= htmlspecialchars($eval_criteria['presentation']) ?>" max="15" min="0" readonly></td>
                        </tr>
                        <tr>
                            <td>Content (25 pts.)</td>
                            <td><input type="number" name="content" class="form-control" value="<?= htmlspecialchars($eval_criteria['content']) ?>" max="25" min="0" readonly></td>
                        </tr>
                        <tr>
                            <td>Organization (10 pts.)</td>
                            <td><input type="number" name="organization" class="form-control" value="<?= htmlspecialchars($eval_criteria['organization']) ?>" max="10" min="0" readonly></td>
                        </tr>
                        <tr>
                            <td colspan="2">Mastery of the Subject Matter (20 pts.)</td>
                            <td><input type="number" name="mastery" class="form-control" value="<?= htmlspecialchars($eval_criteria['mastery']) ?>" max="20" min="0" readonly></td>
                        </tr>
                        <tr>
                            <td colspan="2">Ability to Respond to Questions (20 pts.)</td>
                            <td><input type="number" name="ability" class="form-control" value="<?= htmlspecialchars($eval_criteria['ability']) ?>" max="20" min="0" readonly></td>
                        </tr>
                        <tr>
                            <td colspan="2">Openness Towards the Given Suggestions (10 pts.)</td>
                            <td><input type="number" name="openness" class="form-control" value="<?= htmlspecialchars($eval_criteria['openness']) ?>" max="10" min="0" readonly></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-weight-bold">Overall Rating (Sum of Scores)</td>
                            <td>
                                <input type="number" name="overall_rating" class="form-control" value="<?= htmlspecialchars($eval_criteria['overall_rating']) ?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-weight-bold">Percentage</td>
                            <td>
                                <?php 
                                $percentage = htmlspecialchars($eval_criteria['percentage']);
                                $remarks = $eval_criteria['percentage'] < 75 ? "(Failed)" : "(Passed)";
                                ?>
                                <input type="text" name="percentage" class="form-control" value="<?= $percentage ?> <?= $remarks ?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="font-weight-bold">Remarks</td>
                            <td>
                                <textarea name="remarks" class="form-control" rows="3" maxlength="500" readonly><?= htmlspecialchars($eval_criteria['remarks']) ?></textarea>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-danger">
                            No evaluation made for this form.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>                                
    </div>
</section>

<?php
$content = ob_get_clean();
include('advisor-master.php');
?>
<style>
.thead-background {
    background-color:rgb(56, 120, 193);
    color: white;
}
</style>
