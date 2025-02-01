<?php
// generate_twform6_pdf.php
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

function getTWForm6Details($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            tw6.form6_id, 
            tw.tw_form_id,
            tw6.thesis_title,
            tw6.statistician,
            tw6.editor,
            tw6.comments,
            tw6.date_created,
            tw6.last_updated
        FROM TWFORM_6 tw6
        LEFT JOIN TW_FORMS tw ON tw6.tw_form_id = tw.tw_form_id
        WHERE tw6.tw_form_id = ?
        ORDER BY tw6.last_updated DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);  
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getProponents($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            pro.proponent_id,
            pro.tw_form_id,
            pro.firstname,
            pro.lastname
        FROM PROPONENTS pro
        WHERE pro.tw_form_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAssignedPanelists($tw_form_id) {
    global $conn;
    $query = "
        SELECT
            panelist.assigned_panelist_id,
            panelist.tw_form_id,
            acc.firstname AS panelist_firstname,
            acc.lastname AS panelist_lastname
        FROM assigned_panelists panelist
        LEFT JOIN ACCOUNTS acc ON panelist.user_id = acc.user_id
        WHERE panelist.tw_form_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);  
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Research Management Office');
$pdf->SetTitle('TW Form 6 Details');
$pdf->SetHeaderData('', 0, 'Research Management Office', date('Y-m-d'), array(0,0,0), array(0,0,0));
$pdf->SetFooterData(array(0, 0, 0), array(0, 0, 0));
$pdf->SetFont('helvetica', '', 10);

$pdf->AddPage();

$twform = getTWFormDetails($tw_form_id);
$twform6_details = getTWForm6Details($tw_form_id);
$proponents = getProponents($tw_form_id);
$panelists = getAssignedPanelists($tw_form_id);

$html = '<h3 style="text-align: center;">TW Form Details</h3>';

$html .= '<h4>General Information</h4>';
$html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">';
$html .= '<thead>';
$html .= '<tr><th colspan="2" style="background-color: #f2f2f2; text-align: center;">General Information</th></tr>';
$html .= '</thead>';
$html .= '<tbody>';
$html .= '<tr><td><strong>Research Agenda (IR):</strong></td><td>' . htmlspecialchars($twform['ir_agenda_name'] ?? 'N/A') . '</td></tr>';
$html .= '<tr><td><strong>Research Agenda (College):</strong></td><td>' . htmlspecialchars($twform['college_agenda_name'] ?? 'N/A') . '</td></tr>';
$html .= '<tr><td><strong>Department:</strong></td><td>' . htmlspecialchars($twform['department_name'] ?? 'N/A') . '</td></tr>';
$html .= '<tr><td><strong>Course:</strong></td><td>' . htmlspecialchars($twform['course_name'] ?? 'N/A') . '</td></tr>';
$html .= '<tr><td><strong>Research Adviser:</strong></td><td>' . htmlspecialchars(($twform['adviser_firstname'] ?? '') . ' ' . ($twform['adviser_lastname'] ?? '')) . '</td></tr>';
$html .= '<tr><td><strong>Status:</strong></td><td>' . htmlspecialchars($twform['overall_status'] ?? 'N/A') . '</td></tr>';
$html .= '<tr><td><strong>Comments:</strong></td><td>' . htmlspecialchars($twform['comments'] ?? 'N/A') . '</td></tr>';
$html .= '<tr><td><strong>Submission Date:</strong></td><td>' . (!empty($twform['submission_date']) ? date("Y-m-d", strtotime($twform['submission_date'])) : 'N/A') . '</td></tr>';
$html .= '</tbody>';
$html .= '</table>';

$html .= '<h4>Form Information</h4>';
$html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">';
$html .= '<tr><th colspan="2" style="background-color: #f2f2f2; text-align: center;">Form Details</th></tr>';
$html .= '<tr><td><strong>TW Form Type:</strong></td><td>TW Form 6: Approval of Binding</td></tr>';
$html .= '<tr><td><strong>Proponents:</strong></td><td>';

$proponentNames = array_map(function($proponent) {
    return htmlspecialchars($proponent['firstname'] . ' ' . $proponent['lastname']);
}, $proponents);
$html .= implode(', ', $proponentNames);
$html .= '</td></tr>';

$html .= '<tr><td><strong>Assigned Panelists:</strong></td><td>';
if (!empty($panelists)) {
    $panelistNames = array_map(function($panelist) {
        return htmlspecialchars($panelist['panelist_firstname'] . ' ' . $panelist['panelist_lastname']);
    }, $panelists);
    $html .= implode(', ', $panelistNames);
} else {
    $html .= 'No assigned panelists yet';
}
$html .= '</td></tr>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

$action = $_GET['action'] ?? 'I';

if ($action === 'D') {
    $pdf->Output('tw_form6_' . $tw_form_id . rand(1000, 9999) . '.pdf', 'D');
} else {
    $pdf->Output('tw_form6_' . $tw_form_id . rand(1000, 9999) . '.pdf', 'I');
}
?>
