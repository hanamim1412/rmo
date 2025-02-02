<?php 
// student/twform-3-details.php
session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
        header("Location: ../login.php");
        exit();
    }
    include('student-master.php');
    require '../config/connect.php';
    include '../messages.php';
    $title = "TW form 3 Details";
    ob_start();
    function getUserRoleAndID() {
        global $conn;
        
        echo "Session User ID: " . $_SESSION['user_id'];
        
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        if (!$user_id) {
            echo "User ID is not set!";
        }
        
        $query = "SELECT user_type, user_id FROM ACCOUNTS WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    function getTWFormDetails($id) {
        global $conn, $user_id, $user_type;
        $query = "
            SELECT 
                tw.tw_form_id, 
                tw.form_type,
                tw.user_id,
                tw.ir_agenda_id,
                tw.col_agenda_id,
                tw.department_id AS department,
                tw.course_id AS course,
                tw.research_adviser_id AS adviser,
                tw.comments,
                tw.overall_status,
                tw.submission_date,
                tw.last_updated,
                tw.attachment,
                u.firstname AS firstname, 
                u.lastname AS lastname,
                dep.department_name AS department_name,
                cou.course_name AS course_name,
                advisor.firstname AS adviser_firstname,
                advisor.lastname AS adviser_lastname,
                ira.ir_agenda_name AS ir_agenda_name,
                col_agenda.agenda_name AS col_agenda_name
            FROM TW_FORMS tw
            LEFT JOIN ACCOUNTS u ON tw.user_id = u.user_id
            LEFT JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
            LEFT JOIN COURSES cou ON tw.course_id = cou.course_id
            LEFT JOIN institutional_research_agenda ira ON tw.ir_agenda_id = ira.ir_agenda_id
            LEFT JOIN college_research_agenda col_agenda ON tw.col_agenda_id = col_agenda.agenda_id
            LEFT JOIN ACCOUNTS advisor ON tw.research_adviser_id = advisor.user_id AND advisor.user_type = 'panelist'
            WHERE tw.tw_form_id = ?
            " . ($user_type === 'student' ? "AND u.user_type = 'student' AND tw.user_id = ?" : "") . "
            ORDER BY tw.last_updated DESC
        ";
        $stmt = $conn->prepare($query);
            if ($user_type === 'student') {
                $stmt->bind_param("ii", $id, $user_id);  
            } else {
                $stmt->bind_param("i", $id);  
            }
            $stmt->execute();
            $result = $stmt->get_result();

        return $result->num_rows > 0 ? $result->fetch_assoc() : null;

    }
    function getTWForm3Details($tw_form_id){
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
                    tw3.status,
                    tw3.last_updated
                FROM TWFORM_3 tw3
                LEFT JOIN ACCOUNTS acc ON tw3.student_id = acc.user_id
                WHERE tw3.tw_form_id = ?
            ";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $tw_form_id);  
            $stmt->execute();
            return $stmt->get_result();
    }
    
    function manuscript($tw_form_id) {
        global $conn;
        $query = "SELECT * FROM ATTACHMENTS WHERE tw_form_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $tw_form_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function GetAssignedPanelistsAndChairman($tw_form_id) {
        global $conn;
        $query = "
            SELECT
                panelist.assigned_panelist_id,
                panelist.tw_form_id,
                acc.firstname AS panelist_firstname,
                acc.lastname AS panelist_lastname,
                NULL AS chairman_firstname,
                NULL AS chairman_lastname,
                'panelist' AS role
            FROM assigned_panelists panelist
            LEFT JOIN ACCOUNTS acc ON panelist.user_id = acc.user_id AND acc.user_type = 'panelist'
            WHERE panelist.tw_form_id = ?
            
            UNION ALL
            
            SELECT
                chairman.chairman_id,
                chairman.tw_form_id,
                NULL AS panelist_firstname,
                NULL AS panelist_lastname,
                acc.firstname AS chairman_firstname,
                acc.lastname AS chairman_lastname,
                'chairman' AS role
            FROM assigned_chairman chairman
            LEFT JOIN ACCOUNTS acc ON chairman.user_id = acc.user_id AND acc.user_type = 'chairman'
            WHERE chairman.tw_form_id = ?
        ";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Database query failed: " . $conn->error);
        }
    
        $stmt->bind_param("ii", $tw_form_id, $tw_form_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


    $tw_form_id = $_GET['tw_form_id']; 
    $twform_details = getTWFormDetails($tw_form_id); 
    $twform3_details = getTWForm3Details($tw_form_id); 
    $assigned_users = GetAssignedPanelistsAndChairman($tw_form_id); 
    $manuscript = manuscript($tw_form_id);
?>
<section id="twform-3-details" class="pt-4">
    <div class="header-container pt-4">
        <h4 class="text-left">
        <a href="javascript:history.back()" class="btn btn-link">
            <i class="fas fa-arrow-left" style="margin-right: 5px;text-decoration: none; color: black; font-size: 1.2rem;"></i>
         </a>
            TW form 3: Rating of Proposal Hearing
        </h4>
    </div>
    
        <?php if (!empty($messages)): ?>
            <div class="container mt-3">
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= htmlspecialchars($message['tags']) ?> alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($message['content']) ?>
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="details-section">
            <div><strong>TW Form Type: </strong><?= htmlspecialchars($twform_details['form_type']) ?></div>
            <div><strong>Status: </strong><?php 
                    $status = strtoupper(htmlspecialchars($twform_details['overall_status']));
                    $badgeClass = '';
                    if ($status === 'PENDING') {
                        $badgeClass = 'badge bg-warning text-dark'; 
                    } elseif ($status === 'APPROVED') {
                        $badgeClass = 'badge bg-success'; 
                    } elseif ($status === 'REJECTED') {
                        $badgeClass = 'badge bg-danger'; 
                    } else {
                        $badgeClass = 'badge bg-secondary'; 
                    }
                    ?>
                <span class="<?= $badgeClass ?>"><?= $status ?></span></div>
                <div><strong>Department:</strong> <?= htmlspecialchars($twform_details['department_name']) ?></div>
                <div><strong>Course:</strong> <?= ucwords(htmlspecialchars($twform_details['course_name']))?></div>
                <div><strong>Institutional Research Agenda:</strong> <?= htmlspecialchars($twform_details['ir_agenda_name']) ?></div> 
                <div><strong>College Research Agenda:</strong> <?= htmlspecialchars($twform_details['col_agenda_name']) ?></div> 
                <div><strong>Comments:</strong> <?= htmlspecialchars($twform_details['comments']) ?></div> 
                <div><strong>Submitted On:</strong> <?= date("Y-m-d", strtotime($twform_details['submission_date'])) ?></div>
                <div><strong>Last Updated:</strong> <?= date("Y-m-d", strtotime($twform_details['last_updated'])) ?></div>
                <div>
                    <strong>Attachment</strong><br>

                    <?php if (!empty($twform_details['attachment'])): ?>
                        <?php 
                            $filePath = "../uploads/documents/" . htmlspecialchars($twform_details['attachment']);
                            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                        ?>
                        
                        <?php if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp'])): ?>
                            <a href="<?= $filePath ?>" target="_blank" class="btn btn-sm btn-primary">Download Attachment (<?= strtoupper($fileExtension) ?>)</a>
                            
                        <?php else: ?>
                            <a href="<?= $filePath ?>" target="_blank" class="btn btn-sm btn-primary">Download Attachment (<?= strtoupper($fileExtension) ?>)</a>
                        <?php endif; ?>

                    <?php else: ?>
                        <span>No attachment available.</span>
                    <?php endif; ?>
                </div>
                <div>
                    <strong>Assigned Panelists and Chairman:</strong>
                    <?php if (!empty($assigned_users)): ?>
                        <ul>
                            <?php foreach ($assigned_users as $user): ?>
                                <li>
                                    <?= ucwords(htmlspecialchars(
                                        $user['role'] === 'panelist' 
                                            ? $user['panelist_firstname'] . ' ' . $user['panelist_lastname'] 
                                            : $user['chairman_firstname'] . ' ' . $user['chairman_lastname']
                                    )) ?>
                                    (<?= ucfirst($user['role']) ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No panelists or chairman assigned yet.</p>
                    <?php endif; ?>
                </div>
                
        </div>

            <div class="table-container mt-4">
                        <table id="items-table" class="table table-bordered display">
                            <thead class="thead-background">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Student</th>
                                    <th scope="col">Thesis Title</th>
                                    <th scope="col">Attachment</th>
                                    <th scope="col">Date of Defense</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Venue</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($twform3_details as $index => $twform3): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= ucwords(htmlspecialchars($twform3['firstname'])).' '.ucwords(htmlspecialchars($twform3['lastname'])) ?></td>
                                        <td><?= htmlspecialchars($twform3['thesis_title']) ?></td>
                                        <td>
                                            <?php if ($manuscript->num_rows > 0): ?>
                                                <?php while ($file = $manuscript->fetch_assoc()): ?>
                                                    <a href="../uploads/<?= htmlspecialchars($file['file_path']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                                        View Manuscript
                                                    </a>
                                                    <a href="../uploads/<?= htmlspecialchars($file['file_path']) ?>"
                                                        download class="btn btn-sm btn-success mt-1">
                                                        Download
                                                    </a>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                No manuscript available
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($twform3['defense_date'] ?? 'No assigned date yet') ?></td>
                                        <td>
                                            <?php 
                                            $time_str = trim($twform3['time'] ?? 'No assigned Time Yet');  
                                            $formatted_time = DateTime::createFromFormat('H:i:s', $time_str);

                                            if ($formatted_time) {
                                                echo htmlspecialchars($formatted_time->format('g:i A')); 
                                            } else {
                                                echo "Invalid time"; 
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($twform3['place'] ?? 'No assigned venue yet') ?></td>
                                        <td><?php 
                                            $form_status = isset($twform3['status']) ? strtoupper(htmlspecialchars($twform3['status'])) : 'UNKNOWN';
                                            $badgeClass = '';
                                            if ($form_status === 'PENDING') {
                                                $badgeClass = 'badge bg-warning text-dark'; 
                                            } elseif ($form_status === 'GRADED') {
                                                $badgeClass = 'badge bg-success'; 
                                            } else {
                                                $badgeClass = 'badge bg-secondary'; 
                                            }
                                            ?>
                                            <span class="<?= $badgeClass ?>"><?= $form_status ?></span>
                                            </td>
                                        <td><?= htmlspecialchars($twform3['last_updated']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
            </div>
         
</section>

<script>

    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500); 
            });
        }, 5000);
    });
</script>

<?php
$content = ob_get_clean();
include('student-master.php');
?>