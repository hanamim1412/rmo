<?php 
// rmo_staff/twform-6-details.php
session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
        header("Location: ../login.php");
        exit();
    }
    include('rmo-master.php');
    require '../config/connect.php';
    include '../messages.php';
    $title = "TW form 6 Details";
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
                u.firstname AS firstname, 
                u.lastname AS lastname,
                dep.department_name AS department_name,
                cou.course_name AS course_name,
                advisor.firstname AS adviser_firstname,
                advisor.lastname AS adviser_lastname,
                ira.ir_agenda_name AS ir_agenda_name,
                col_agenda.agenda_name AS col_agenda_name,
                tw6.thesis_title,
                tw6.statistician,
                tw6.editor
            FROM TW_FORMS tw
            LEFT JOIN ACCOUNTS u ON tw.user_id = u.user_id
            LEFT JOIN DEPARTMENTS dep ON tw.department_id = dep.department_id
            LEFT JOIN COURSES cou ON tw.course_id = cou.course_id
            LEFT JOIN institutional_research_agenda ira ON tw.ir_agenda_id = ira.ir_agenda_id
            LEFT JOIN college_research_agenda col_agenda ON tw.col_agenda_id = col_agenda.agenda_id
            LEFT JOIN TWFORM_6 tw6 ON tw.tw_form_id = tw6.tw_form_id
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
        if (!$stmt) {
            die("Database query failed: " . $conn->error);
        }
    
        $stmt->bind_param("i", $tw_form_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function manuscript($tw_form_id) {
        global $conn;
        $query = "SELECT * FROM ATTACHMENTS WHERE tw_form_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $tw_form_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function GetPanelistsAndChairmanFromTwForm5($tw_form_id) {
        global $conn;
        
        $query = "
            SELECT 
                p.assigned_panelist_id AS id, 
                p.tw_form_id, 
                acc.firstname AS panelist_firstname, 
                acc.lastname AS panelist_lastname, 
                NULL AS chairman_firstname, 
                NULL AS chairman_lastname, 
                'panelist' AS role
            FROM assigned_panelists p
            LEFT JOIN ACCOUNTS acc ON p.user_id = acc.user_id
            WHERE p.tw_form_id = (
                SELECT twf5.tw_form_id 
                FROM twform_5 twf5
                JOIN tw_forms tf ON twf5.tw_form_id = tf.tw_form_id
                WHERE tf.user_id = (
                    SELECT user_id FROM tw_forms WHERE tw_form_id = ? LIMIT 1
                )
                LIMIT 1
            )
    
            UNION ALL
    
            SELECT 
                c.chairman_id AS id, 
                c.tw_form_id, 
                NULL AS panelist_firstname, 
                NULL AS panelist_lastname, 
                acc.firstname AS chairman_firstname, 
                acc.lastname AS chairman_lastname, 
                'chairman' AS role
            FROM assigned_chairman c
            LEFT JOIN ACCOUNTS acc ON c.user_id = acc.user_id
            WHERE c.tw_form_id = (
                SELECT twf5.tw_form_id 
                FROM twform_5 twf5
                JOIN tw_forms tf ON twf5.tw_form_id = tf.tw_form_id
                WHERE tf.user_id = (
                    SELECT user_id FROM tw_forms WHERE tw_form_id = ? LIMIT 1
                )
                LIMIT 1
            )
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
    

    $tw_form_id = $_GET['tw_form_id'] ?? null; 
    $twform_details = getTWFormDetails($tw_form_id); 
    $proponents = GetProponents($tw_form_id);   
    $manuscript = manuscript($tw_form_id);
    if ($tw_form_id) {
        $assigned_users = GetPanelistsAndChairmanFromTwForm5($tw_form_id);
    } else {
        die("Invalid TW Form ID");
    }
?>


<section id="twform-6-details" class="pt-4">
    <div class="header-container pt-4">
        <h4 class="text-left">
        <a href="javascript:history.back()" class="btn btn-link">
            <i class="fas fa-arrow-left" style="margin-right: 5px;text-decoration: none; color: black; font-size: 1.2rem;"></i>
         </a>
            TW form 6: Approval of Binding
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
                <div><strong>Submitted On:</strong> <?= date("Y-m-d", strtotime($twform_details['submission_date'])) ?></div>
                <div><strong>Last Updated:</strong> <?= date("Y-m-d", strtotime($twform_details['last_updated'])) ?></div>
                <div>
                    <?php if (!empty($twform_details['comments'])): ?>
                        <div>
                            <strong>Comments:</strong> 
                            <span id="remarks-display"><?= htmlspecialchars($twform_details['comments']); ?></span>
                            <button class="btn btn-sm btn-secondary" id="edit-remarks-btn" onclick="toggleEdit()">Edit</button>
                        </div>
                        <form action="submit-remarks.php" method="POST" id="edit-remarks-form" style="display: none;">
                            <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($twform_details['tw_form_id']); ?>">
                            <input type="hidden" name="form_type" value="<?= htmlspecialchars($twform_details['form_type'] ?? ''); ?>">
                            <textarea name="comments" rows="2" class="form-control form-control-sm w-50" required><?= htmlspecialchars($twform_details['comments']); ?></textarea>
                            <button type="submit" class="btn btn-primary btn-sm mt-1">Save</button>
                            <button type="button" class="btn btn-secondary btn-sm mt-1" onclick="toggleEdit()">Cancel</button>
                        </form>
                    <?php else: ?>
                        <form action="submit-remarks.php" method="POST" style="display: inline;">
                            <label for="remarks"><strong>Comments:</strong></label>
                            <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($twform_details['tw_form_id']); ?>">
                            <input type="hidden" name="form_type" value="<?= htmlspecialchars($twform_details['form_type']); ?>">
                            <textarea name="comments" rows="2" class="form-control form-control-sm w-50" placeholder="Enter comments here..." required></textarea>
                            <button type="submit" class="btn btn-primary btn-sm mt-1">Send</button>
                        </form>
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
               <div>
                    <strong>Proponents Details:</strong> 
                    <?php if (!empty($proponents)): ?>
                        <ul>
                            <?php foreach ($proponents as $proponent): ?>
                                <li>
                                    <?= ucwords(htmlspecialchars($proponent['firstname'] . ' ' . $proponent['lastname'])) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No proponents found for this form.</p>
                    <?php endif; ?>
                </div>
                <div><strong>Research Title:</strong> <?= ucwords(htmlspecialchars($twform_details['thesis_title'])) ?></div>
                <div><strong>Thesis File:</strong>
                            <?php if ($manuscript->num_rows > 0): ?>
                                   <?php while ($file = $manuscript->fetch_assoc()): ?>
                                     <a href="../uploads/<?= htmlspecialchars($file['file_path']) ?>"
                                      target="_blank" class="btn btn-sm btn-primary">
                                    View Manuscript
                                    </a>
                                    <a href="../uploads/<?= htmlspecialchars($file['file_path']) ?>"
                                        download class="btn btn-sm btn-warning">
                                        Download
                                    </a>
                                <?php endwhile; ?>
                            <?php else: ?>
                                No manuscript available
                            <?php endif; ?>
                </div>
                
        </div>
        <div class="details-section">
            <form action="update-twform6-compliance.php" method="POST">
                <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($twform_details['tw_form_id']) ?>">

                <div class="mb-3">
                    <label><strong>Supporting Documents:</strong></label><br>

                    <?php
                    $query = "SELECT * FROM twform_6_compliance WHERE tw_form_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $twform_details['tw_form_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $document_types = [
                        'Certificate of Conformity',
                        'Certificate of Data Gathering',
                        'Certificate of Similarity',
                        'Article Repository'
                    ];
                    $existing_documents = [];
                    while ($row = $result->fetch_assoc()) {
                        $existing_documents[$row['document_name']] = $row['is_checked'];
                    }

                    foreach ($document_types as $doc) {
                        $isChecked = isset($existing_documents[$doc]) && $existing_documents[$doc] == 1;
                        echo '<div class="form-check">';
                        echo '<input class="form-check-input border border-2 border-dark" type="checkbox" name="documents[]" value="' . htmlspecialchars($doc) . '" ' . ($isChecked ? 'checked' : '') . '>';
                        echo '<label class="form-check-label">' . htmlspecialchars($doc) . '</label>';
                        echo '</div>';
                    }
                    ?>
                </div>

                <button type="submit" class="btn btn-success">Update Compliance</button>
            </form>

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
include('rmo-master.php');
?>