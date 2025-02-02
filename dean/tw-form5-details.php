<?php 
// dean/twform-5-details.php
session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
        header("Location: ../login.php");
        exit();
    }
    include('dean-master.php');
    require '../config/connect.php';
    include '../messages.php';
    $title = "TW form 5 Details";
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
                tw.attachment,
                tw.last_updated,
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
            LEFT JOIN ACCOUNTS advisor ON tw.research_adviser_id = advisor.user_id AND advisor.user_type = 'research_adviser'
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
    function getTWForm5Details($tw_form_id){
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
                    tw5.status,
                    tw5.last_updated
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

    function manuscript($tw_form_id) {
        global $conn;
        $query = "SELECT * FROM ATTACHMENTS WHERE tw_form_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $tw_form_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getEvalCriteria($tw_form_id) {
        global $conn;
        $query = "
            SELECT
                ev.eval_id,
                ev.tw_form_id,
                ev.evaluator_id,
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
            WHERE ev.tw_form_id = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $tw_form_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $eval_criteria = $result->fetch_assoc();
        
        return $eval_criteria;
    }

    $tw_form_id = $_GET['tw_form_id']; 
    $twform_details = getTWFormDetails($tw_form_id); 
    $twform5_details = getTWForm5Details($tw_form_id);  
    $manuscript = manuscript($tw_form_id);
    $panelists = GetAssignedPanelist($tw_form_id);  
    $chairman = GetAssignedChairman($tw_form_id); 
    $eval_criteria = getEvalCriteria($tw_form_id);
?>
<section id="twform-5-details" class="pt-4">
    <div class="header-container pt-4">
        <h4 class="text-left">
            TW form 5: Rating for Oral Examination/Final Defense
        </h4>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
            <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
            Back
        </a>
        <div class="actions">
            <?php if ($twform_details['user_id'] != $user_id): ?>
                <form action="update_status.php" method="POST" style="display: inline; margin-left: 10px;">
                    <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($twform_details['tw_form_id']) ?>">
                    <input type="hidden" name="form_type" value="<?= htmlspecialchars($twform_details['form_type'] ?? ''); ?>">
                    <label for="overall_status">Update Status:</label>
                    <select name="overall_status" id="overall_status" class="form-select form-select-sm d-inline" style="width: auto;" required>
                        <option value="pending" <?= $twform_details['overall_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                         <option value="approved" <?= $twform_details['overall_status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $twform_details['overall_status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                    <button type="submit" class="btn btn-success btn-sm">Update Status</button>
                </form>
            <?php endif; ?>
        </div>
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
                    <?php if (!empty($panelists)): ?>
                        <ul>
                            <strong>Assigned Panel examiners:</strong> 
                            <?php foreach ($panelists as $panelist): ?>
                                <li><?= ucwords(htmlspecialchars($panelist['panelist_firstname'] . ' ' . $panelist['panelist_lastname'])) ?></li>
                                <?php endforeach; ?>
                                <strong>Assigned Chairman:</strong> 
                                <li><?= ucwords(htmlspecialchars($chairman['cm_firstname'] . ' ' . $chairman['cm_lastname'])) ?></li>
                                
                            </ul>
                            <a href="edit-assign-panelists.php?tw_form_id=<?= $twform_details['tw_form_id'] ?>" 
                            class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit
                        </a>    
                        <?php else: ?>
                            <p>No panelists and chairman found for this form.</p>
                            <a href="assign-panelists.php?tw_form_id=<?= $twform_details['tw_form_id'] ?>" 
                            class="btn btn-primary btn-sm">Assign Panel examiners
                        </a>
                        <?php endif; ?>
                    </div>
                    
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
                
        </div>

            <div class="table-container mt-4">
            <form action="tw5_update_defense_schedule.php" method="POST">
                <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($twform_details['tw_form_id']) ?>">
                <input type="hidden" name="form_type" value="<?= htmlspecialchars($twform_details['form_type'] ?? ''); ?>">
                    
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
                                    <th scope="col">Last Updated</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($twform5_details as $twform5): ?>
                                    <tr>
                                        <td><?= $i++;?></td>
                                        <td><?= ucwords(htmlspecialchars($twform5['firstname'])).' '.ucwords(htmlspecialchars($twform5['lastname'])) ?></td>
                                        <td><?= htmlspecialchars($twform5['thesis_title']) ?></td>
                                        <td>
                                            <?php if ($manuscript->num_rows > 0): ?>
                                                <?php while ($file = $manuscript->fetch_assoc()): ?>
                                                    <a href="../uploads/<?= htmlspecialchars($file['file_path']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                                        View Manuscript
                                                    </a>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                No manuscript available
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input type="date" name="defense_date" value="<?= htmlspecialchars($twform5['defense_date'] ?? ''); ?>" class="form-control form-control-sm" required>
                                        </td>

                                        <td>
                                            <input type="time" name="time" value="<?= htmlspecialchars($twform5['time'] ?? ''); ?>" class="form-control form-control-sm" required>
                                        </td>

                                        <td>
                                            <textarea name="place" class="form-control form-control-sm w-auto" rows="2" required><?= htmlspecialchars($twform5['place'] ?? 'No assigned place yet'); ?></textarea>
                                        </td>

                                        <td><?= htmlspecialchars($twform5['last_updated']) ?></td>
                                        <td><?php 
                                            $form_status = isset($twform5['status']) ? strtoupper(htmlspecialchars($twform5['status'])) : 'UNKNOWN';
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
                                        <td>
                                            <form action="update_form5_status.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($twform_details['tw_form_id']) ?>">
                                                <input type="hidden" name="form_type" value="<?= htmlspecialchars($twform_details['form_type'] ?? ''); ?>">
                                                <select name="status" id="status" class="form-select form-select-sm" style="width: auto;" required>
                                                    <option value="pending" <?= $twform5['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="graded" <?= $twform5['status'] === 'graded' ? 'selected' : '' ?>>Graded</option>
                                                </select>
                                                <button type="submit" name="update_status" class="btn btn-success btn-sm">Update Status</button>
                                            </form>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <?php if ($eval_criteria): ?>
                                                    <a href="eval_details.php?tw_form_id=<?= htmlspecialchars($twform_details['tw_form_id']) ?>"
                                                        class="btn btn-warning btn-sm mt-2">View Scores</a>
                                                <?php else: ?>
                                                    <span class="badge btn-danger btn-sm mt-2"> No Scores Available </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="text-right mt-3">
                        <button type="submit" name="update_schedule" class="btn btn-success btn-sm">Update Schedule</button>
                    </div>
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
include('dean-master.php');
?>