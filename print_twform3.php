<?php
// generate_twform3_pdf.php
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
$is_advisor = ($user_type === 'chairman');

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
    return $stmt->get_result()->fetch_assoc(); // Fetch a single row
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
        LEFT JOIN ACCOUNTS acc ON panelist.user_id = acc.user_id AND acc.user_type = 'panelist'
        WHERE panelist.tw_form_id = ?
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database query failed: " . $conn->error);
    }

    $stmt->bind_param("i", $tw_form_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function GetAssignedChairman($tw_form_id) {
    global $conn;
    $query = "
        SELECT
            cm.chairman_id,
            cm.tw_form_id,
            acc.firstname AS cm_firstname,
            acc.lastname AS cm_lastname
        FROM assigned_chairman cm
        LEFT JOIN ACCOUNTS acc ON cm.user_id = acc.user_id
        WHERE cm.tw_form_id = ?
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
$twform3_details = getTWForm3Details($tw_form_id);
$panelists = GetAssignedPanelist($tw_form_id);  
$chairman = GetAssignedChairman($tw_form_id);  
$eval_criteria_list = getEvalCriteria($tw_form_id, $is_panelist ? $user_id : null);
$dean = getCollegeDean($tw_form_id);
$dean_name = $dean ? htmlspecialchars($dean['firstname'] . ' ' . $dean['lastname']) : '_________________________';

if (!$twform || !$twform3_details) {
    die("Error: Form details not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TW Form 3 Details</title>
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
            width: 100%;
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
        <h3>TW Form 3 Details</h3>

        <!-- General Information -->
        <h4>General Information</h4>
        <table>
            <tr><th colspan="2" style="background-color: #f2f2f2; text-align: center;">General Information</th></tr>
            <tr><td><strong>Research Agenda (IR):</strong></td><td><?php echo htmlspecialchars($twform['ir_agenda_name']); ?></td></tr>
            <tr><td><strong>Research Agenda (College):</strong></td><td><?php echo htmlspecialchars($twform['college_agenda_name']); ?></td></tr>
            <tr><td><strong>Department:</strong></td><td><?php echo htmlspecialchars($twform['department_name']); ?></td></tr>
            <tr><td><strong>Course:</strong></td><td><?php echo htmlspecialchars($twform['course_name']); ?></td></tr>
            <tr><td><strong>Research Adviser:</strong></td><td><?php echo htmlspecialchars($twform['adviser_firstname'] . ' ' . $twform['adviser_lastname']); ?></td></tr>
            <tr><td><strong>Assigned Panelists:</strong></td><td>
                <?php
                if (!empty($panelists)) {
                    $panelistNames = array_map(function ($panelist) {
                        return htmlspecialchars($panelist['panelist_firstname'] . ' ' . $panelist['panelist_lastname']);
                    }, $panelists);
                    echo implode(', ', $panelistNames);
                } else {
                    echo 'No assigned panelists yet';
                }
                ?>
            </td></tr>
            <tr><td><strong>Assigned Chairman:</strong></td><td>
                <?php
                if (!empty($chairman)) {
                    echo htmlspecialchars($chairman['cm_firstname'] . ' ' . $chairman['cm_lastname']);
                } else {
                    echo 'No assigned chairman yet';
                }
                ?>
            </td></tr>
            <tr><td><strong>Status:</strong></td><td><?php echo htmlspecialchars($twform['overall_status']); ?></td></tr>
            <tr><td><strong>Comments:</strong></td><td><?php echo htmlspecialchars($twform['comments']); ?></td></tr>
            <tr><td><strong>Submission Date:</strong></td><td><?php echo date("Y-m-d", strtotime($twform['submission_date'])); ?></td></tr>
        </table>

        <!-- Form Information -->
        <h4>Form Information</h4>
        <table>
            <tr><th colspan="2" style="background-color: #f2f2f2; text-align: center;">Form Information</th></tr>
            <tr><td><strong>Student Name:</strong></td><td><?php echo htmlspecialchars($twform3_details['firstname'] . ' ' . $twform3_details['lastname']); ?></td></tr>
            <tr><td><strong>Thesis Title:</strong></td><td><?php echo htmlspecialchars($twform3_details['thesis_title']); ?></td></tr>
            <tr><td><strong>Defense Date:</strong></td><td><?php echo date("F j, Y", strtotime($twform3_details['defense_date'])); ?></td></tr>
            <tr><td><strong>Time:</strong></td><td><?php echo date("g:i A", strtotime($twform3_details['time'])); ?></td></tr>
            <tr><td><strong>Venue:</strong></td><td><?php echo htmlspecialchars($twform3_details['place']); ?></td></tr>
            <tr><td><strong>Rating Status:</strong></td><td><?php echo htmlspecialchars($twform3_details['status']); ?></td></tr>
        </table>

        <!-- Evaluation Criteria -->
        <h4>Evaluation Criteria</h4>
        <?php if ($eval_criteria_list): ?>
            <?php foreach ($eval_criteria_list as $eval_criteria): ?>
                <?php if ($is_dean || $is_advisor || ($is_panelist && $eval_criteria['evaluator_id'] == $user_id)): ?>
                    <table border="1" cellpadding="4" cellspacing="0" style="width: 100%;">
                        <thead>
                            <tr><th colspan="2" class="text-center"><strong>Evaluation Criteria</strong></th><th class="text-center"><strong>Score</strong></th></tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="2"><strong>Evaluator</strong></td><td><?php echo ucwords(htmlspecialchars($eval_criteria['eval_firstname'] . ' ' . $eval_criteria['eval_lastname'])); ?></td></tr>
                            <tr><td rowspan="3" class="align-middle"><strong>Presentation of the Paper (50 pts.)</strong></td><td>Presentation (15 pts.)</td><td><?php echo htmlspecialchars($eval_criteria['presentation']); ?></td></tr>
                            <tr><td>Content (25 pts.)</td><td><?php echo htmlspecialchars($eval_criteria['content']); ?></td></tr>
                            <tr><td>Organization (10 pts.)</td><td><?php echo htmlspecialchars($eval_criteria['organization']); ?></td></tr>
                            <tr><td colspan="2"><strong>Mastery of the Subject Matter (20 pts.)</strong></td><td><?php echo htmlspecialchars($eval_criteria['mastery']); ?></td></tr>
                            <tr><td colspan="2"><strong>Ability to Respond to Questions (20 pts.)</strong></td><td><?php echo htmlspecialchars($eval_criteria['ability']); ?></td></tr>
                            <tr><td colspan="2"><strong>Openness Towards the Given Suggestions (10 pts.)</strong></td><td><?php echo htmlspecialchars($eval_criteria['openness']); ?></td></tr>
                            <tr><td colspan="2"><strong>Overall Rating (Sum of Scores)</strong></td><td><?php echo htmlspecialchars($eval_criteria['overall_rating']); ?></td></tr>
                            <?php
                            $percentage = htmlspecialchars($eval_criteria['percentage']);
                            $remarks = $eval_criteria['percentage'] < 75 ? "(Failed)" : "(Passed)";
                            ?>
                            <tr><td colspan="2"><strong>Percentage</strong></td><td><?php echo $percentage . ' ' . $remarks; ?></td></tr>
                            <tr><td colspan="2"><strong>Remarks</strong></td><td><?php echo htmlspecialchars($eval_criteria['remarks']); ?></td></tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No evaluation made for this form.</p>
        <?php endif; ?>

        <div style="margin-top: 50px; text-align: center;">
            <table style="width: 100%; text-align: center;">
                <tr>
                    <td style="width: 50%; border-top: 1px solid #000; padding-top: 10px;">
                        <strong>Approved By:</strong><br><br><u><?php echo $dean_name; ?></u><br>College Dean
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 20px;">
                        <strong>Noted By:</strong><br><br>
                        <u>ANICETO B. NAVAL</u><br>Director, Research<br><br>
                        <u>RITZCEN A. DURANGO, PhD EL</u><br>Vice President, Academic Affairs
                    </td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 10px; text-align: center;">
            <table style="width: 100%; text-align: center; border: none;">
                <tr>
                    <td style="width: 20%; padding-top: 10px; border: none;">
                        <strong>Approved By:</strong><br><br><u><?php echo $dean_name; ?></u><br>College Dean
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 10px; border: none; justify-content: between;">
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