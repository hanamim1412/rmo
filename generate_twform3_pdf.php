<?php
// generate_twform3_pdf.php
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

function getTWForm3Details($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            tw3.form3_id, 
            tw3.tw_form_id,
            tw3.student_id,
            acc.firstname,
            acc.lastname,
            tw3.thesis_title,
            tw3.defense_date,
            tw3.time,
            tw3.place,
            tw3.comments,
            tw3.status
        FROM TWFORM_3 tw3
        LEFT JOIN ACCOUNTS acc ON tw3.student_id = acc.user_id
        WHERE tw3.tw_form_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);  
    $stmt->execute();
    return $stmt->get_result();
}
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Research Management Office');
$pdf->SetTitle('TW Form 3 Details');
$pdf->SetHeaderData('', 5, 'Research Management Office', date('Y-m-d'), array(0,0,0), array(0,0,0));
$pdf->SetFooterData(array(0, 0, 0), array(0, 0, 0));
$pdf->SetFont('helvetica', '', 10);

$pdf->AddPage();

$twform = getTWFormDetails($tw_form_id);
$twform3_details = getTWForm3Details($tw_form_id);

$html = '<h3 style="text-align: center;">TW Form 3 Details</h3>';

$html .= '<h4>TW Form Details</h4>';
$html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">';
$html .= '<tr><th colspan="2" style="background-color: #f2f2f2; text-align: center;">General Information</th></tr>';
$html .= '<tr><td><strong>Research Agenda (IR):</strong></td><td>' . htmlspecialchars($twform['ir_agenda_name']) . '</td></tr>';
$html .= '<tr><td><strong>Research Agenda (College):</strong></td><td>' . htmlspecialchars($twform['college_agenda_name']) . '</td></tr>';
$html .= '<tr><td><strong>Department:</strong></td><td>' . htmlspecialchars($twform['department_name']) . '</td></tr>';
$html .= '<tr><td><strong>Course:</strong></td><td>' . htmlspecialchars($twform['course_name']) . '</td></tr>';
$html .= '<tr><td><strong>Research Adviser:</strong></td><td>' . htmlspecialchars($twform['adviser_firstname'] . ' ' . $twform['adviser_lastname']) . '</td></tr>';
$html .= '<tr><td><strong>Status:</strong></td><td>' . htmlspecialchars($twform['overall_status']) . '</td></tr>';
$html .= '<tr><td><strong>Comments:</strong></td><td>' . htmlspecialchars($twform['comments']) . '</td></tr>';
$html .= '<tr><td><strong>Submission Date:</strong></td><td>' . date("Y-m-d", strtotime($twform['submission_date'])) . '</td></tr>';
$html .= '</table>';

$html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">';
$html .= '<tr><th colspan="2" style="background-color: #f2f2f2; text-align: center;">Form Information</th></tr>';

foreach ($twform3_details as $twform3) {
    $html .= '<tr><td><strong>Student Name:</strong></td><td>' . htmlspecialchars($twform3['firstname'] . ' ' . $twform3['lastname']) . '</td></tr>';
    $html .= '<tr><td><strong>Thesis Title:</strong></td><td>' . htmlspecialchars($twform3['thesis_title']) . '</td></tr>';
    $html .= '<tr><td><strong>Defense Date:</strong></td><td>';
    
        $defense_date = DateTime::createFromFormat('Y-m-d', $twform3['defense_date']);
        if ($defense_date) {
            $html .= $defense_date->format('F j, Y');
        } else {
            $html .= 'Invalid date';
        }
        $html .= '</td></tr>';
    
    $formatted_time = DateTime::createFromFormat('H:i:s', $twform3['time']);
    if ($formatted_time) {
        $html .= '<tr><td><strong>Time:</strong></td><td>' . $formatted_time->format('g:i A') . '</td></tr>';
    } else {
        $html .= '<tr><td><strong>Time:</strong></td><td>Invalid time</td></tr>';
    }

    $html .= '<tr><td><strong>Venue:</strong></td><td>' . htmlspecialchars($twform3['place']) . '</td></tr>';
    $html .= '<tr><td><strong>Status:</strong></td><td>' . htmlspecialchars($twform3['status']) . '</td></tr>';
}

$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

$action = isset($_GET['action']) ? $_GET['action'] : 'I';

if ($action == 'D') {
    $pdf->Output('tw_form2_' . $tw_form_id . rand(1000,9999).'.pdf', 'D');
} else {
    $pdf->Output('tw_form2_' . $tw_form_id . rand(1000,9999). '.pdf', 'I');
}
?>