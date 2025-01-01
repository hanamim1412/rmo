<?php
// generate_twform5_pdf.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config/connect.php';
require_once('TCPDF-main/tcpdf.php');

if (!isset($_GET['tw_form_id']) || !is_numeric($_GET['tw_form_id'])) {
    die("Error: Invalid or missing tw_form_id.");
}

$tw_form_id = (int) $_GET['tw_form_id'];
$user_id = $_SESSION['user_id'];

$query = "SELECT user_type FROM ACCOUNTS WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_type = $user['user_type'];
$is_dean = ($user_type === 'dean');
$is_panelist = ($user_type === 'panelist');
function getTWFormDetails($id) {
    global $conn;
    $query = "
        SELECT 
            tw.tw_form_id,
            tw.form_type,
            tw.ir_agenda_id,
            tw.col_agenda_id,
            tw.department_id,
            tw.course_id,
            tw.research_adviser_id,
            tw.overall_status,
            tw.comments,
            tw.submission_date,
            ira.ir_agenda_name,
            col_agenda.agenda_name AS college_agenda_name,
            dep.department_name,
            cou.course_name,
            u.firstname AS adviser_firstname,
            u.lastname AS adviser_lastname
        FROM TW_FORMS tw
        LEFT JOIN institutional_research_agenda ira ON tw.ir_agenda_id = ira.ir_agenda_id
        LEFT JOIN college_research_agenda col_agenda ON tw.col_agenda_id = col_agenda.agenda_id
        LEFT JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
        LEFT JOIN COURSES cou ON tw.course_id = cou.course_id
        LEFT JOIN ACCOUNTS u ON tw.research_adviser_id = u.user_id
        WHERE tw.tw_form_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getTWForm5Details($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            tw5.form5_id, 
            tw5.tw_form_id,
            tw5.student_id,
            acc.firstname,
            acc.lastname,
            tw5.thesis_title,
            tw5.defense_date,
            tw5.time,
            tw5.place,
            tw5.status
        FROM TWFORM_5 tw5
        LEFT JOIN ACCOUNTS acc ON tw5.student_id = acc.user_id
        WHERE tw5.tw_form_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);  
    $stmt->execute();
    return $stmt->get_result();
}
function GetAssignedPanelist($tw_form_id) {
    global $conn;
    $query = "
        SELECT
            panelist.assigned_panelist_id,
            panelist.tw_form_id,
            acc.firstname AS panelist_firstname,
            acc.lastname AS panelist_lastname
        FROM assigned_panelists panelist
        LEFT JOIN TW_FORMS tw ON panelist.tw_form_id = tw.tw_form_id
        LEFT JOIN ACCOUNTS acc ON panelist.user_id = acc.user_id
        WHERE panelist.tw_form_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);  
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function getEvalCriteria($tw_form_id, $evaluator_id = null) {
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

    if ($evaluator_id !== null) {
        $query .= " AND ev.evaluator_id = ?";
    }

    $stmt = $conn->prepare($query);

    if ($evaluator_id !== null) {
        $stmt->bind_param("ii", $tw_form_id, $evaluator_id);
    } else {
        $stmt->bind_param("i", $tw_form_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $eval_criteria = [];
    while ($row = $result->fetch_assoc()) {
        $eval_criteria[] = $row;
    }

    return $eval_criteria;
}
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Research Management Office');
$pdf->SetTitle('TW Form 5 Details');
$pdf->SetHeaderData('', 5, 'Research Management Office', date('Y-m-d'), array(0,0,0), array(0,0,0));
$pdf->SetFooterData(array(0, 0, 0), array(0, 0, 0));
$pdf->SetFont('helvetica', '', 10);

$pdf->AddPage();

$twform = getTWFormDetails($tw_form_id);
$twform5_details = getTWForm5Details($tw_form_id);
$panelists = GetAssignedPanelist($tw_form_id);
$eval_criteria_list = getEvalCriteria($tw_form_id, $is_panelist ? $user_id : null);

$html = '<h3 style="text-align: center;">TW Form 5 Details</h3>';

$html .= '<h4>TW Form Details</h4>';
$html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">';
$html .= '<tr><th colspan="2" style="background-color: #f2f2f2; text-align: center;">General Information</th></tr>';
$html .= '<tr><td><strong>Research Agenda (IR):</strong></td><td>' . htmlspecialchars($twform['ir_agenda_name']) . '</td></tr>';
$html .= '<tr><td><strong>Research Agenda (College):</strong></td><td>' . htmlspecialchars($twform['college_agenda_name']) . '</td></tr>';
$html .= '<tr><td><strong>Department:</strong></td><td>' . htmlspecialchars($twform['department_name']) . '</td></tr>';
$html .= '<tr><td><strong>Course:</strong></td><td>' . htmlspecialchars($twform['course_name']) . '</td></tr>';
$html .= '<tr><td><strong>Research Adviser:</strong></td><td>' . htmlspecialchars($twform['adviser_firstname'] . ' ' . $twform['adviser_lastname']) . '</td></tr>';
$html .= '<tr><td><strong>Assigned Panelists:</strong></td><td>';
    if (count($panelists) > 0) {
        $panelistNames = [];
        foreach ($panelists as $panelist) {
            $panelistNames[] = htmlspecialchars($panelist['panelist_firstname'] . ' ' . $panelist['panelist_lastname']);
        }
        $html .= implode(', ', $panelistNames);
    } else {
        $html .= 'No assigned panelists yet';
    }
$html .= '</td></tr>';
$html .= '<tr><td><strong>Status:</strong></td><td>' . htmlspecialchars($twform['overall_status']) . '</td></tr>';
$html .= '<tr><td><strong>Comments:</strong></td><td>' . htmlspecialchars($twform['comments']) . '</td></tr>';
$html .= '<tr><td><strong>Submission Date:</strong></td><td>' . date("Y-m-d", strtotime($twform['submission_date'])) . '</td></tr>';
$html .= '</table>';

$html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">';
$html .= '<tr><th colspan="2" style="background-color: #f2f2f2; text-align: center;">Form Information</th></tr>';

foreach ($twform5_details as $twform5) {
    $html .= '<tr><td><strong>Student Name:</strong></td><td>' . htmlspecialchars($twform5['firstname'] . ' ' . $twform5['lastname']) . '</td></tr>';
    $html .= '<tr><td><strong>Thesis Title:</strong></td><td>' . htmlspecialchars($twform5['thesis_title']) . '</td></tr>';
    $html .= '<tr><td><strong>Defense Date:</strong></td><td>';
    
        $defense_date = DateTime::createFromFormat('Y-m-d', $twform5['defense_date']);
        if ($defense_date) {
            $html .= $defense_date->format('F j, Y');
        } else {
            $html .= 'Invalid date';
        }
        $html .= '</td></tr>';
    
    $formatted_time = DateTime::createFromFormat('H:i:s', $twform5['time']);
    if ($formatted_time) {
        $html .= '<tr><td><strong>Time:</strong></td><td>' . $formatted_time->format('g:i A') . '</td></tr>';
    } else {
        $html .= '<tr><td><strong>Time:</strong></td><td>Invalid time</td></tr>';
    }

    $html .= '<tr><td><strong>Venue:</strong></td><td>' . htmlspecialchars($twform5['place']) . '</td></tr>';
    $html .= '<tr><td><strong>Rating Status:</strong></td><td>' . htmlspecialchars($twform5['status']) . '</td></tr>';
}

$html .= '</table>';
$html .= '<h4>Evaluation Criteria</h4>';

if ($eval_criteria_list) {
    foreach ($eval_criteria_list as $eval_criteria) {
        
        if ($is_dean || ($is_panelist && $eval_criteria['evaluator_id'] == $user_id)) {
            $html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">';
            $html .= '<thead><tr><th colspan="2" class="text-center"><strong>Evaluation Criteria</strong></th><th class="text-center"><strong>Score</strong></th></tr></thead>';
            $html .= '<tbody>';
            $html .= '<tr><td colspan="2"><strong>Evaluator</strong></td><td>' . ucwords(htmlspecialchars($eval_criteria['eval_firstname'])) . ' ' . ucwords(htmlspecialchars($eval_criteria['eval_lastname'])) . '</td></tr>';
            $html .= '<tr><td rowspan="3" class="align-middle"><strong>Presentation of the Paper (50 pts.)</strong></td><td>Presentation (15 pts.)</td><td>' . htmlspecialchars($eval_criteria['presentation']) . '</td></tr>';
            $html .= '<tr><td>Content (25 pts.)</td><td>' . htmlspecialchars($eval_criteria['content']) . '</td></tr>';
            $html .= '<tr><td>Organization (10 pts.)</td><td>' . htmlspecialchars($eval_criteria['organization']) . '</td></tr>';
            $html .= '<tr><td colspan="2"><strong>Mastery of the Subject Matter (20 pts.)</strong></td><td>' . htmlspecialchars($eval_criteria['mastery']) . '</td></tr>';
            $html .= '<tr><td colspan="2"><strong>Ability to Respond to Questions (20 pts.)</strong></td><td>' . htmlspecialchars($eval_criteria['ability']) . '</td></tr>';
            $html .= '<tr><td colspan="2"><strong>Openness Towards the Given Suggestions (10 pts.)</strong></td><td>' . htmlspecialchars($eval_criteria['openness']) . '</td></tr>';
            $html .= '<tr><td colspan="2"><strong>Overall Rating (Sum of Scores)</strong></td><td>' . htmlspecialchars($eval_criteria['overall_rating']) . '</td></tr>';
            $percentage = htmlspecialchars($eval_criteria['percentage']);
            $remarks = $eval_criteria['percentage'] < 75 ? "(Failed)" : "(Passed)";
            $html .= '<tr><td colspan="2"><strong>Percentage</strong></td><td>' . $percentage . ' ' . $remarks . '</td></tr>';
            $html .= '<tr><td colspan="2"><strong>Remarks</strong></td><td>' . htmlspecialchars($eval_criteria['remarks']) . '</td></tr>';
            $html .= '</tbody>';
            $html .= '</table>';
        }
    }

} else {
    $html .= '<p>No evaluation made for this form.</p>';
}

$pdf->writeHTML($html, true, false, true, false, '');

$action = isset($_GET['action']) ? $_GET['action'] : 'I';

if ($action == 'D') {
    $pdf->Output('tw_form5_' . $tw_form_id . rand(1000,9999).'.pdf', 'D');
} else {
    $pdf->Output('tw_form5_' . $tw_form_id . rand(1000,9999). '.pdf', 'I');
}
?>