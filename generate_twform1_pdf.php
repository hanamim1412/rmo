<?php
// generate_twform1_pdf.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config/connect.php';
require_once('TCPDF-main/tcpdf.php');

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
            tw.last_updated,
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

function getTWForm1Details($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            tw1.form1_id, 
            tw1.year_level
        FROM TWFORM_1 tw1
        LEFT JOIN TW_FORMS tw ON tw1.tw_form_id = tw.tw_form_id
        WHERE tw1.tw_form_id = ?
        ORDER BY tw1.last_updated DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);  
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function GetProponents($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            pro.proponent_id,
            pro.tw_form_id,
            pro.firstname,
            pro.lastname
        FROM PROPONENTS pro
        LEFT JOIN TW_FORMS tw ON pro.tw_form_id = tw.tw_form_id
        WHERE pro.tw_form_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function GetTitles($tw_form_id) {
    global $conn;
    $query = "
        SELECT
            title.proposed_title_id,
            title.tw_form_id,
            title.title_name,
            title.rationale,
            title.is_selected
        FROM PROPOSED_TITLE title
        LEFT JOIN TW_FORMS tw ON title.tw_form_id = tw.tw_form_id
        WHERE title.tw_form_id = ?
        ORDER BY title.date_created DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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

if (!isset($_GET['tw_form_id']) || !is_numeric($_GET['tw_form_id'])) {
    die("Error: Invalid or missing tw_form_id.");
}
$tw_form_id = (int) $_GET['tw_form_id'];
$tw_form = getTWFormDetails($tw_form_id);
$twform1_details = getTWForm1Details($tw_form_id);  
$proponents = GetProponents($tw_form_id);  
$titles = GetTitles($tw_form_id);  
$panelists = GetAssignedPanelist($tw_form_id);

if (!$tw_form) {
    die("Error: Form details not found.");
}

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($tw_form['adviser_firstname'] . ' ' . $tw_form['adviser_lastname']);
$pdf->SetTitle('TW Form 1 Details');
$pdf->SetHeaderData('', 0, 'Research Management Office', date('Y-m-d'), array(0,0,0), array(0,0,0));

$pdf->AddPage();

$html = '<h3 style="text-align: center;">TW Form 1 Details</h3>';

$html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">';
$html .= '<tr><th colspan="2" style="background-color: #f2f2f2; text-align: center;">General Information</th></tr>';
$html .= '<tr><td><strong>Form Type:</strong></td><td>' . htmlspecialchars($tw_form['form_type']) . '</td></tr>';
$html .= '<tr><td><strong>Institutional Agenda:</strong></td><td>' . htmlspecialchars($tw_form['ir_agenda_name']) . '</td></tr>';
$html .= '<tr><td><strong>College Agenda:</strong></td><td>' . htmlspecialchars($tw_form['college_agenda_name']) . '</td></tr>';
$html .= '<tr><td><strong>Department:</strong></td><td>' . htmlspecialchars($tw_form['department_name']) . '</td></tr>';
$html .= '<tr><td><strong>Course:</strong></td><td>' . htmlspecialchars($tw_form['course_name']) . '</td></tr>';
$html .= '<tr><td><strong>Research Adviser:</strong></td><td>' . htmlspecialchars($tw_form['adviser_firstname'] . ' ' . $tw_form['adviser_lastname']) . '</td></tr>';
$html .= '<tr><td><strong>Overall Status:</strong></td><td>' . htmlspecialchars($tw_form['overall_status']) . '</td></tr>';
$html .= '<tr><td><strong>Comments:</strong></td><td>' . htmlspecialchars($tw_form['comments']) . '</td></tr>';
$html .= '<tr><td><strong>Submission Date:</strong></td><td>' . htmlspecialchars($tw_form['submission_date']) . '</td></tr>';
$html .= '<tr><td><strong>Last Updated:</strong></td><td>' . htmlspecialchars($tw_form['last_updated']) . '</td></tr>';
$html .= '</table>';

$html .= '<h3>Proponents</h3>';
$html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">';
$html .= '<thead><tr><th>First Name</th><th>Last Name</th></tr></thead><tbody>';
foreach ($proponents as $proponent) {
    $html .= '<tr><td>' . htmlspecialchars($proponent['firstname']) . '</td>';
    $html .= '<td>' . htmlspecialchars($proponent['lastname']) . '</td></tr>';
}
$html .= '</tbody></table>';

$html .= '<h3>Proposed Titles</h3>';
$html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">';
$html .= '<thead><tr><th>Title</th><th>Rationale</th><th>Selected</th></tr></thead><tbody>';
foreach ($titles as $title) {
    $html .= '<tr><td>' . htmlspecialchars($title['title_name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($title['rationale']) . '</td>';
    $html .= '<td>' . ($title['is_selected'] ? 'Yes' : 'No') . '</td></tr>';
}
$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

$action = isset($_GET['action']) ? $_GET['action'] : 'I';

if ($action == 'D') {
    $pdf->Output('tw_form1_' . $tw_form_id . '.pdf', 'D');
} else {
    $pdf->Output('tw_form1_' . $tw_form_id . '.pdf', 'I');
}
?>