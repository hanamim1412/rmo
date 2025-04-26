<?php
// generate_twform5_pdf.php
session_start();
require 'config/connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


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
$twform5_details = getTWForm5Details($tw_form_id);
$panelists = GetAssignedPanelist($tw_form_id);  
$chairman = GetAssignedChairman($tw_form_id);  
$eval_criteria_list = getEvalCriteria($tw_form_id, $is_panelist ? $user_id : null);
$dean = getCollegeDean($tw_form_id);
$dean_name = $dean ? htmlspecialchars($dean['firstname'] . ' ' . $dean['lastname']) : '_________________________';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TW Form 5 Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        @page {
            size: letter portrait;
            margin: 1cm;
        }
        .container {
            width: 90%;
            margin: auto;
        }
        h3, h4 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #f2f2f2;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
            }
        }
        .border-table {
            border: 1px solid #000;
            padding: 10px;
        }
        .header-logo {
            max-height: 80px;
        }
        .small-text {
            font-size: 0.9rem;
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">
    <!-- HEADER -->
    <div class="border-table mb-4" style="display: table; width: 97%; table-layout: fixed;">
        <div style="display: table-row;">
            <!-- Logo Column -->
            <div style="display: table-cell; width: 15%; text-align: center; vertical-align: middle;">
                <img src="uploads/dept_logo/rmo-logo.jpg" alt="Logo" class="header-logo">
            </div>
            
            <!-- School Name Column -->
            <div style="display: table-cell; width: 60%; text-align: center; vertical-align: middle; border-left: 1px solid black;">
                <h5 style="margin: 0;">ST. MICHAEL'S COLLEGE OF ILIGAN, INC</h5>
            </div>

            <!-- Document Code Column -->
            <div style="display: table-cell; width: 25%; text-align: left; border-left: 1px solid black; vertical-align: top; font-size: 0.9rem; padding-left: 5px;">
                Document Code: SMCII.RMO.TWFORM.<?php echo htmlspecialchars($twform['tw_form_id']); ?><br>
                REV: 0<br>
                Effective Date: <?php echo date('Y-m-d', strtotime($twform['submission_date'])); ?>
            </div>
        </div>

        <div style="display: table-row;">
            <!-- Empty left cell -->
            <div style="display: table-cell; width: 15%;"></div>

            <!-- Form Type Title Center -->
            <div style="display: table-cell; width: 60%; text-align: center; border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000;">
                <h6 style="margin: 5px 0;">TW Form 5: Approval for Final Defense</h6>
                <p class="small-text" style="margin: 0;">Approval for Final Defense of Thesis</p>
            </div>

            <!-- Empty right cell -->
            <div style="display: table-cell; width: 25%;"></div>
        </div>
    </div>
    <table>
        <tr><th colspan="2" style="text-align: center;">General Information</th></tr>
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

    <table>
        <tr><th colspan="2" style="text-align: center;">Form Information</th></tr>
        <?php foreach ($twform5_details as $twform5): ?>
            <tr><td><strong>Student Name:</strong></td><td><?php echo htmlspecialchars($twform5['firstname'] . ' ' . $twform5['lastname']); ?></td></tr>
            <tr><td><strong>Thesis Title:</strong></td><td><?php echo htmlspecialchars($twform5['thesis_title']); ?></td></tr>
            <tr><td><strong>Defense Date:</strong></td><td><?php echo date("F j, Y", strtotime($twform5['defense_date'])); ?></td></tr>
            <tr><td><strong>Time:</strong></td><td><?php echo date("g:i A", strtotime($twform5['time'])); ?></td></tr>
            <tr><td><strong>Venue:</strong></td><td><?php echo htmlspecialchars($twform5['place']); ?></td></tr>
            <tr><td><strong>Rating Status:</strong></td><td><?php echo htmlspecialchars($twform5['status']); ?></td></tr>
        <?php endforeach; ?>
    </table>

    <!-- APPROVED BY / NOTED BY -->
    <div class="row text-center mt-5">
            <div class="col-4">
                <p><strong>Approved By:</strong></p>
                <p><u class="text-center" style="text-transform: uppercase;"><?php echo $dean_name; ?></u><br>College Dean</p>
            </div>
            <p><strong>Noted By:</strong></p>
            <div style="margin-top: 50px; width: 100%; text-align: center;">
                <table style="width: 100%; border: none; margin-top: 20px;">
                    <tr>
                        <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                            <u>STEPHANIE L. COLORADA, MAEd, TESOL</u><br>
                            Coordinator, Research Management Office
                        </td>
                        <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
                            <u>RITZCEN A. DURANGO, Ph.D.</u><br>
                            Vice President, Academic Affairs
                        </td>
                    </tr>
                </table>
            </div>
        </div>
</body>
</html>