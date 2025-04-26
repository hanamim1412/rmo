<?php
// print_twform2.php
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

function getTWForm2Details($tw_form_id) {
    global $conn;
    $query = "
        SELECT 
            tw2.form2_id, 
            tw.tw_form_id,
            tw2.thesis_title,
            tw2.defense_date,
            tw2.time,
            tw2.place,
            tw2.comments,
            tw2.date_created,
            tw2.last_updated
        FROM TWFORM_2 tw2
        LEFT JOIN TW_FORMS tw ON tw2.tw_form_id = tw.tw_form_id
        WHERE tw2.tw_form_id = ?
        ORDER BY tw2.last_updated DESC
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
$twform2_details = getTWForm2Details($tw_form_id);
$proponents = GetProponents($tw_form_id);
$dean = getCollegeDean($tw_form_id);
$dean_name = $dean ? htmlspecialchars($dean['firstname'] . ' ' . $dean['lastname']) : '_________________________';


if (!$twform || !$twform2_details) {
    die("Error: Form details not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TW Form 2 Details</title>
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
                Document Code: SMCII.RMO.<?php echo strtoupper(str_replace(' ', '', $twform['form_type'])); ?>.<?php echo htmlspecialchars($twform['tw_form_id']); ?><br>
                REV: 0<br>
                Effective Date: <?php echo date('Y-m-d', strtotime($twform['submission_date'])); ?>
            </div>
        </div>

        <div style="display: table-row;">
            <!-- Empty left cell -->
            <div style="display: table-cell; width: 15%;"></div>

            <!-- Form Type Title Center -->
            <div style="display: table-cell; width: 60%; text-align: center; border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000;">
                <h6 style="margin: 5px 0;"><?php echo htmlspecialchars($twform['form_type']); ?></h6>
                <?php
                    $formDescriptions = [
                        'twform_1' => 'Approval of Thesis Title',
                        'twform_2' => 'Approval for Proposal Hearing',
                        'twform_3' => 'Rating for Proposal Hearing',
                        'twform_4' => 'Approval for Oral Examination',
                        'twform_5' => 'Rating for Final Defense',
                        'twform_6' => 'Approval for Binding',
                    ];

                    $formType = $twform['form_type'] ?? '';
                    if (isset($formDescriptions[$formType])) {
                        echo '<p class="small-text" style="margin: 0;">' . htmlspecialchars($formDescriptions[$formType]) . '</p>';
                    }
                    ?>
            </div>

            <!-- Empty right cell -->
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
                <th>Form Type</th>
                <td>TW Form 2: Approval for Proposal Hearing</td>
            </tr>
            <tr>
                <th>Institutional Agenda</th>
                <td><?php echo htmlspecialchars($twform['ir_agenda_name']); ?></td>
            </tr>
            <tr>
                <th>College Agenda</th>
                <td><?php echo htmlspecialchars($twform['college_agenda_name']); ?></td>
            </tr>
            <tr>
                <th>Department</th>
                <td><?php echo htmlspecialchars($twform['department_name']); ?></td>
            </tr>
            <tr>
                <th>Course</th>
                <td><?php echo htmlspecialchars($twform['course_name']); ?></td>
            </tr>
            <tr>
                <th>Research Adviser</th>
                <td><?php echo htmlspecialchars($twform['adviser_firstname'] . ' ' . $twform['adviser_lastname']); ?></td>
            </tr>
            <tr>
                <th>Overall Status</th>
                <td><?php echo htmlspecialchars($twform['overall_status']); ?></td>
            </tr>
            <tr>
                <th>Comments</th>
                <td><?php echo htmlspecialchars($twform['comments']); ?></td>
            </tr>
            <tr>
                <th>Proponents</th>
                <td>
                    <?php 
                        $proponentNames = array_map(function($proponent) {
                            return htmlspecialchars($proponent['firstname'] . ' ' . $proponent['lastname']);
                        }, $proponents);
                        echo implode(', ', $proponentNames);
                    ?>
                </td>
            </tr>
            <?php foreach ($proponents as $proponent): ?>
            <tr>
                <th>Receipt Number (<?php echo htmlspecialchars($proponent['firstname'] . ' ' . $proponent['lastname']); ?>)</th>
                <td><?php echo htmlspecialchars($proponent['receipt_num']); ?></td>
            </tr>
            <tr>
                <th>Date Paid</th>
                <td><?php echo date("Y-m-d", strtotime($proponent['date_paid'])); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th>Thesis Title</th>
                <td><?php echo htmlspecialchars($twform2_details['thesis_title']); ?></td>
            </tr>
            <tr>
                <th>Defense Date</th>
                <td><?php echo date("F j, Y", strtotime($twform2_details['defense_date'])); ?></td>
            </tr>
            <tr>
                <th>Time</th>
                <td><?php echo date("g:i A", strtotime($twform2_details['time'])); ?></td>
            </tr>
            <tr>
                <th>Venue</th>
                <td><?php echo htmlspecialchars($twform2_details['place']); ?></td>
            </tr>
            <tr>
                <th>Submission Date</th>
                <td><?php echo htmlspecialchars($twform['submission_date']); ?></td>
            </tr>
            <tr>
                <th>Last Updated</th>
                <td><?php echo htmlspecialchars($twform['last_updated']); ?></td>
            </tr>
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
