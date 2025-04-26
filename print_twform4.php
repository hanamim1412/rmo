<?php
// print_twform4.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config/connect.php';

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

function getTWForm4Details($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            tw4.form4_id, 
            tw4.tw_form_id,
            tw4.thesis_title,
            tw4.defense_date,
            tw4.time,
            tw4.place,
            tw4.date_submitted,
            tw4.last_updated
        FROM TWFORM_4 tw4
        LEFT JOIN TW_FORMS tw ON tw4.tw_form_id = tw.tw_form_id
        WHERE tw4.tw_form_id = ?
        ORDER BY tw4.last_updated DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);  
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc(); // Fetch a single row
}

function GetProponents($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            pro.proponent_id,
            pro.tw_form_id,
            pro.firstname,
            pro.lastname,
            rp.receipt_num,
            rp.date_paid
        FROM PROPONENTS pro
        LEFT JOIN TW_FORMS tw ON pro.tw_form_id = tw.tw_form_id
        LEFT JOIN RECEIPTS rp ON pro.receipt_id = rp.receipt_id
        WHERE pro.tw_form_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getCollegeDean($tw_form_id) {
    global $conn;
    $query = "
        SELECT acc.firstname, acc.lastname 
        FROM ACCOUNTS acc
        JOIN TW_FORMS tw ON acc.department_id = tw.department_id
        WHERE acc.user_type = 'dean' AND tw.tw_form_id = ?
        LIMIT 1";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database query failed: " . $conn->error);
    }

    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();  
}

$twform = getTWFormDetails($tw_form_id);
$twform4_details = getTWForm4Details($tw_form_id);
$proponents = GetProponents($tw_form_id);
$dean = getCollegeDean($tw_form_id);
$dean_name = $dean ? htmlspecialchars($dean['firstname'] . ' ' . $dean['lastname']) : '_________________________';


if (!$twform || !$twform4_details) {
    die("Error: Form details not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TW Form 4 Details</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #000;
        }

        /* Page Size for Printing */
        @page {
            size: letter portrait;
            margin: 1cm;
        }

        .container {
            width: 100%;
            max-width: 21.59cm; /* Letter width */
            margin: 0 auto;
            padding: 1cm;
        }

        h3, h4 {
            text-align: center;
            margin: 0.5em 0;
        }

        table {
            width: 90%;
            border-collapse: collapse;
            margin-bottom: 1em;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .print-button {
            display: none; /* Hide the print button when printing */
        }

        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">
    <div class="container">
        <h3>TW Form 4 Details</h3>

        <!-- General Information -->
        <h4>General Information</h4>
        <table>
            <tr><th colspan="2" style="background-color: #f2f2f2; text-align: center;">General Information</th></tr>
            <tr><td><strong>Research Agenda (IR):</strong></td><td><?php echo htmlspecialchars($twform['ir_agenda_name']); ?></td></tr>
            <tr><td><strong>Research Agenda (College):</strong></td><td><?php echo htmlspecialchars($twform['college_agenda_name']); ?></td></tr>
            <tr><td><strong>Department:</strong></td><td><?php echo htmlspecialchars($twform['department_name']); ?></td></tr>
            <tr><td><strong>Course:</strong></td><td><?php echo htmlspecialchars($twform['course_name']); ?></td></tr>
            <tr><td><strong>Research Adviser:</strong></td><td><?php echo htmlspecialchars($twform['adviser_firstname'] . ' ' . $twform['adviser_lastname']); ?></td></tr>
            <tr><td><strong>Status:</strong></td><td><?php echo htmlspecialchars($twform['overall_status']); ?></td></tr>
            <tr><td><strong>Comments:</strong></td><td><?php echo htmlspecialchars($twform['comments']); ?></td></tr>
            <tr><td><strong>Submission Date:</strong></td><td><?php echo date("Y-m-d", strtotime($twform['submission_date'])); ?></td></tr>
        </table>

        <!-- Form Information -->
        <h4>Form Information</h4>
        <table>
            <tr><th colspan="2" style="background-color: #f2f2f2; text-align: center;">Form Information</th></tr>
            <tr><td><strong>TW Form Type:</strong></td><td>TW Form 4: Approval of Oral Examination/Final Defense</td></tr>
            <tr><td><strong>Proponents:</strong></td><td>
                <?php
                $proponentNames = array_map(function ($proponent) {
                    return htmlspecialchars($proponent['firstname'] . ' ' . $proponent['lastname']);
                }, $proponents);
                echo implode(', ', $proponentNames);
                ?>
            </td></tr>
            <?php foreach ($proponents as $proponent): ?>
                <tr><td><strong>Receipt Number (<?php echo htmlspecialchars($proponent['firstname'] . ' ' . $proponent['lastname']); ?>):</strong></td><td><?php echo htmlspecialchars($proponent['receipt_num']); ?></td></tr>
                <tr><td><strong>Date Paid:</strong></td><td><?php echo date("Y-m-d", strtotime($proponent['date_paid'])); ?></td></tr>
            <?php endforeach; ?>
            <tr><td><strong>Thesis Title:</strong></td><td><?php echo htmlspecialchars($twform4_details['thesis_title']); ?></td></tr>
            <tr><td><strong>Defense Date:</strong></td><td><?php echo date("F j, Y", strtotime($twform4_details['defense_date'])); ?></td></tr>
            <tr><td><strong>Time:</strong></td><td><?php echo date("g:i A", strtotime($twform4_details['time'])); ?></td></tr>
            <tr><td><strong>Venue:</strong></td><td><?php echo htmlspecialchars($twform4_details['place']); ?></td></tr>
        </table>
        <div style="margin-top: 10px; text-align: center;">
            <table style="width: 100%; text-align: center; border: none;">
                <tr>
                    <td style="width: 100%; padding-top: 10px; border: none;">
                        <strong>Approved By:</strong><br><br><u><?php echo $dean_name; ?></u><br>College Dean
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 5px; border: none; justify-content: between;">
                        <strong>Noted By:</strong><br><br>
                        <div>
                            <u>ANICETO B. NAVAL</u><br>Director, Research Management Office<br><br>
                            <u>RITZCEN A. DURANGO, PhD.</u><br>Vice President, Academic Affairs
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>