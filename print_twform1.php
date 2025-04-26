<?php
// print_twform1.php
session_start();
require 'config/connect.php';
require_once('TCPDF-main/tcpdf.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['tw_form_id']) || !is_numeric($_GET['tw_form_id'])) {
    die("Error: Invalid or missing tw_form_id.");
}

function getTWFormDetails($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            tw.tw_form_id, tw.form_type, tw.ir_agenda_id, tw.col_agenda_id, 
            tw.department_id, tw.course_id, tw.research_adviser_id, 
            tw.overall_status, tw.comments, tw.submission_date, tw.last_updated,
            ira.ir_agenda_name, col_agenda.agenda_name AS college_agenda_name,
            dep.department_name, cou.course_name, 
            COALESCE(u.firstname, 'Not Assigned') AS adviser_firstname,
            COALESCE(u.lastname, '') AS adviser_lastname
        FROM TW_FORMS tw
        LEFT JOIN institutional_research_agenda ira ON tw.ir_agenda_id = ira.ir_agenda_id
        LEFT JOIN college_research_agenda col_agenda ON tw.col_agenda_id = col_agenda.agenda_id
        LEFT JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
        LEFT JOIN COURSES cou ON tw.course_id = cou.course_id
        LEFT JOIN ACCOUNTS u ON tw.research_adviser_id = u.user_id AND u.user_type = 'research_adviser'
        WHERE tw.tw_form_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tw_form_id);
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
    $result = $stmt->get_result(); 
    $details = [];
    
    while ($row = $result->fetch_assoc()) {
        $details[] = $row; 
    }
    return $details;
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

$tw_form_id = $_GET['tw_form_id'] ?? null;
$tw_form = getTWFormDetails($tw_form_id);
$twform1_details = getTWForm1Details($tw_form_id);  
$proponents = GetProponents($tw_form_id);  
$titles = GetTitles($tw_form_id);  
$panelists = GetAssignedPanelist($tw_form_id);  
$chairman = GetAssignedChairman($tw_form_id);  
$dean = getCollegeDean($tw_form_id);
$dean_name = $dean ? htmlspecialchars($dean['firstname'] . ' ' . $dean['lastname']) : '_________________________';

if (!$tw_form) {
    die("Error: Form details not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TW Form 1 Details</title>
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
        h3 {
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
        }
        th {
            background-color: #f2f2f2;
        }
        .btn-print {
            display: block;
            width: 150px;
            margin: 20px auto;
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-align: center;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }
        .btn-print:hover {
            background-color: #0056b3;
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
        th, td {
            vertical-align: middle;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #000;
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">
    <!-- HEADER -->
        <div class="border-table mb-4" style="display: table; width: 97%; table-layout: fixed;">
            <div style="display: table-row;">
                <!-- Logo Column -->
                <div style="display: table-cell; width: 15%; text-align: center; horizontal-align: middle;">
                    <img src="uploads/dept_logo/rmo-logo.jpg" alt="Logo" class="header-logo" style="max-height: 80px;">
                </div>
                
                <!-- School Name Column -->
                <div style="display: table-cell; width: 60%; text-align: center; vertical-align: middle; border-left: 1px solid black;">
                    <h5 style="margin: 0;">ST. MICHAEL'S COLLEGE OF ILIGAN, INC</h5>
                </div>

                <!-- Document Code Column -->
                <div style="display: table-cell; width: 25%; text-align: left;border-left: 1px solid black; vertical-align: top; font-size: 0.9rem; margin: 5px 0; padding-left: 5px;">
                    Document Code: SMCII.RMO.<?php echo strtoupper(str_replace(' ', '', $tw_form['form_type'])); ?>.<?php echo htmlspecialchars($tw_form['tw_form_id']); ?><br>
                    REV: 0<br>
                    Effective Date: <?php echo date('Y-m-d', strtotime($tw_form['submission_date'])); ?>
                </div>
            </div>

            <div style="display: table-row;">
                <!-- Empty left cell under logo -->
                <div style="display: table-cell; width: 15%;"></div>

                <!-- Form Type Title Center -->
                <div style="display: table-cell; width: 60%; text-align: center; border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000;">
                    <h6 style="margin: 5px 0;"><?php echo htmlspecialchars($tw_form['form_type']); ?></h6>
                    <?php
                    $formDescriptions = [
                        'twform_1' => 'Approval of Thesis Title',
                        'twform_2' => 'Approval for Proposal Hearing',
                        'twform_3' => 'Rating for Proposal Hearing',
                        'twform_4' => 'Approval for Oral Examination',
                        'twform_5' => 'Rating for Final Defense',
                        'twform_6' => 'Approval for Binding',
                    ];

                    $formType = $tw_form['form_type'] ?? '';
                    if (isset($formDescriptions[$formType])) {
                        echo '<p class="small-text" style="margin: 0;">' . htmlspecialchars($formDescriptions[$formType]) . '</p>';
                    }
                    ?>
                </div>

                <!-- Empty right cell under Doc Code -->
                <div style="display: table-cell; width: 25%;"></div>
            </div>
        </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th colspan="2" style="text-align: center;">TW Form Information</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th width="30%">Form Type</th>
                <td><?php echo htmlspecialchars($tw_form['form_type']); ?></td>
            </tr>
            <tr>
                <th>Institutional Agenda</th>
                <td><?php echo htmlspecialchars($tw_form['ir_agenda_name']); ?></td>
            </tr>
            <tr>
                <th>College Agenda</th>
                <td><?php echo htmlspecialchars($tw_form['college_agenda_name']); ?></td>
            </tr>
            <tr>
                <th>Department</th>
                <td><?php echo htmlspecialchars($tw_form['department_name']); ?></td>
            </tr>
            <tr>
                <th>Course</th>
                <td><?php echo htmlspecialchars($tw_form['course_name']); ?></td>
            </tr>
            <tr>
                <th>Year Level</th>
                <td><?php echo htmlspecialchars($twform1_details[0]['year_level']); ?></td>
            </tr>
            <tr>
                <th>Research Adviser</th>
                <td><?php echo htmlspecialchars($tw_form['adviser_firstname'] . ' ' . $tw_form['adviser_lastname']); ?></td>
            </tr>
            <tr>
                <th>Overall Status</th>
                <td><?php echo htmlspecialchars($tw_form['overall_status']); ?></td>
            </tr>
            <tr>
                <th>Comments</th>
                <td><?php echo htmlspecialchars($tw_form['comments']); ?></td>
            </tr>
            <tr>
                <th>Proponents</th>
                <td>
                    <?php 
                        $proponent_names = array_map(function($proponent) {
                            return htmlspecialchars($proponent['firstname'] . ' ' . $proponent['lastname']);
                        }, $proponents);
                        echo implode(", ", $proponent_names);
                    ?>
                </td>
            </tr>
            <tr>
                <th>Submission Date</th>
                <td><?php echo htmlspecialchars($tw_form['submission_date']); ?></td>
            </tr>
            <tr>
                <th>Last Updated</th>
                <td><?php echo htmlspecialchars($tw_form['last_updated']); ?></td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th colspan="3" style="text-align: center;">Proposed Titles</th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th>Title</th>
                <th>Rationale</th>
                <th>Selected</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($titles as $title): ?>
                <tr>
                    <td><?php echo htmlspecialchars($title['title_name']); ?></td>
                    <td><?php echo htmlspecialchars($title['rationale']); ?></td>
                    <td><?php echo $title['is_selected'] ? 'Yes' : 'No'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
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