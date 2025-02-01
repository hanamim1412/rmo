<?php 
// dean/twform-1-details.php
session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
        header("Location: ../login.php");
        exit();
    }
    include('dean-master.php');
    require '../config/connect.php';
    include '../messages.php';
    $title = "TW form1 Details";
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
        if (!$stmt) {
            die("Database query failed: " . $conn->error);
        }
    
        $stmt->bind_param("i", $tw_form_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function GetTitles($tw_form_id) {
        global $conn;
        $query = "
            SELECT
                title.proposed_title_id,
                title.tw_form_id,
                title.title_name,
                title.rationale,
                title.is_selected,
                title.remarks
            FROM PROPOSED_TITLE title
            LEFT JOIN TW_FORMS tw ON title.tw_form_id = tw.tw_form_id
            WHERE title.tw_form_id = ?
            ORDER BY title.date_created DESC
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
    

    $tw_form_id = $_GET['tw_form_id']; 
    $twform_details = getTWFormDetails($tw_form_id); 
    $twform1_details = getTWForm1Details($tw_form_id);  
    $proponents = GetProponents($tw_form_id);  
    $titles = GetTitles($tw_form_id);  
    $panelists = GetAssignedPanelist($tw_form_id);  
    $chairman = GetAssignedChairman($tw_form_id);  
?>


<section id="twform-1-details" class="pt-4">
    <div class="header-container pt-4">
        <h4 class="text-left">TW form 1: Approval of Proposed Titles</h4>
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
                <?php foreach ($twform1_details as $detail) {
                        echo '<div><strong>Year Level:</strong> ' . htmlspecialchars($detail['year_level']) . '</div>';
                    }
                ?>
                
                <div><strong>Submitted On:</strong> <?= date("Y-m-d", strtotime($twform_details['submission_date'])) ?></div>
                <div><strong>Last Updated:</strong> <?= date("Y-m-d", strtotime($twform_details['last_updated'])) ?></div>
                <div>
                    <?php if (!empty($twform_details['comments'])): ?>
                        <div id="remarks-display-comment">
                            <strong>Comments:</strong> 
                            <?= htmlspecialchars($twform_details['comments']); ?>
                            <button class="btn btn-sm btn-secondary" onclick="toggleCommentEdit()">Edit</button>
                        </div>

                        <form action="submit-remarks.php" method="POST" id="edit-remarks-form-comment" style="display: none;">
                            <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($twform_details['tw_form_id']); ?>">
                            <input type="hidden" name="form_type" value="<?= htmlspecialchars($twform_details['form_type'] ?? ''); ?>">

                            <textarea name="comments" rows="2" class="form-control form-control-sm w-50" required><?= htmlspecialchars($twform_details['comments']); ?></textarea>
                            <button type="submit" class="btn btn-primary btn-sm mt-1">Save</button>
                            <button type="button" class="btn btn-secondary btn-sm mt-1" onclick="toggleCommentEdit()">Cancel</button>
                        </form>
                    <?php else: ?>
                        <form action="submit-remarks.php" method="POST">
                            <label for="remarks"><strong>Comments:</strong></label>
                            <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($twform_details['tw_form_id']); ?>">
                            <input type="hidden" name="form_type" value="<?= htmlspecialchars($twform_details['form_type']); ?>">
                            
                            <textarea name="comments" rows="2" class="form-control form-control-sm w-50" placeholder="Enter comments here..." required></textarea>
                            <button type="submit" class="btn btn-primary btn-sm mt-1">Send</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div>
                    <strong>Attachment</strong><br>

                    <?php if (!empty($twform_details['attachment'])): ?>
                        <?php 
                            $filePath = "../uploads/documents/" . htmlspecialchars($twform_details['attachment']);
                            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                        ?>
                        
                        <?php if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp'])): ?>
                            <a href="<?= $filePath ?>" target="_blank">
                                <img src="<?= $filePath ?>" alt="Attachment" class="img-fluid" style="max-width: 500px; max-height: 500px;">
                            </a>
                        <?php else: ?>
                            <a href="<?= $filePath ?>" target="_blank" class="btn btn-sm btn-primary">Download Attachment (<?= strtoupper($fileExtension) ?>)</a>
                        <?php endif; ?>

                    <?php else: ?>
                        <span>No attachment available.</span>
                    <?php endif; ?>
                </div>


 
                <div>
                    <strong>Proponents:</strong> 
                    <?php if (!empty($proponents)): ?>
                        <ul>
                            <?php foreach ($proponents as $proponent): ?>
                                <li><?= ucwords(htmlspecialchars($proponent['firstname'] . ' ' . $proponent['lastname'])) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No proponents found for this form.</p>
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
                                
                        <?php else: ?>
                            <p>No panelists and chairman found for this form.</p>
                            <a href="assign-panelists.php?tw_form_id=<?= $twform_details['tw_form_id'] ?>" 
                                class="btn btn-primary btn-sm">Assign Panel examiners
                            </a>
                        <?php endif; ?>
                </div>
        </div>

            <div class="table-container mt-4">
                    <?php if (!empty($titles)): ?>
                        <table id="items-table" class="table table-bordered display">
                            <thead class="thead-background">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Rationale</th>
                                    <th scope="col">Selection</th>
                                    <th scope="col">remarks</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($titles as $index => $title): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($title['title_name']) ?></td>
                                        <td><?= htmlspecialchars($title['rationale']) ?></td>
                                        <td>
                                            <?php if ($title['is_selected'] == 1): ?>
                                                <span class="badge btn-success">Selected</span>
                                            <?php else: ?>
                                                <span class="badge btn-danger">Not Selected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($title['proposed_title_id']) && $title['is_selected']): ?>
                                                <form method="post" action="update-title-status.php" onsubmit="return confirm('Are you sure you want to deselect this title?');" style="display:inline;">
                                                    <input type="hidden" name="proposed_title_id" value="<?= htmlspecialchars($title['proposed_title_id']); ?>">
                                                    <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($twform_details['tw_form_id']); ?>">
                                                    <input type="hidden" name="form_type" value="<?= htmlspecialchars($twform_details['form_type']); ?>">
                                                    <input type="hidden" name="new_status" value="0">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Deselected</button>
                                                </form>
                                            <?php elseif (isset($title['proposed_title_id'])): ?>
                                                <form method="post" action="update-title-status.php" onsubmit="return confirm('Are you sure you want to select this title?');" style="display:inline;">
                                                    <input type="hidden" name="proposed_title_id" value="<?= htmlspecialchars($title['proposed_title_id']); ?>">
                                                    <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($twform_details['tw_form_id']); ?>">
                                                    <input type="hidden" name="form_type" value="<?= htmlspecialchars($twform_details['form_type']); ?>">
                                                    <input type="hidden" name="new_status" value="1">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">Selected</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div id="remarks-display-<?= $title['proposed_title_id'] ?>">
                                                <?= !empty($title['remarks']) ? htmlspecialchars($title['remarks']) : 'No remarks yet' ?>
                                                <button class="btn btn-sm btn-secondary" onclick="toggleTitleEdit(<?= $title['proposed_title_id'] ?>)">Edit</button>
                                            </div>

                                            <form action="submit-title-remarks.php" method="POST" id="edit-remarks-form-<?= $title['proposed_title_id'] ?>" style="display: none;">
                                                <input type="hidden" name="proposed_title_id" value="<?= htmlspecialchars($title['proposed_title_id']) ?>">
                                                <input type="hidden" name="tw_form_id" value="<?= htmlspecialchars($twform_details['tw_form_id']); ?>">
                                                <input type="hidden" name="form_type" value="<?= htmlspecialchars($twform_details['form_type']); ?>">

                                                <textarea name="remarks" rows="2" class="form-control form-control-sm" required><?= htmlspecialchars($title['remarks']); ?></textarea>
                                                <button type="submit" class="btn btn-primary btn-sm mt-1">Save</button>
                                                <button type="button" class="btn btn-secondary btn-sm mt-1" onclick="toggleTitleEdit(<?= $title['proposed_title_id'] ?>)">Cancel</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="text-center text-muted">No proposed titles available.</p>
                            </div>
                        <?php endif; ?>
                <div id="loadingOverlay" class="d-none">
                    <div id="loadingSpinnerContainer" class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
         
</section>

<script>
$(document).ready(function () {
    $('#load').on('click', function () {
        
        $('#loadingOverlay').removeClass('d-none');

    });
});

function toggleTitleEdit(proposedTitleId) {
    const displayDiv = document.getElementById(`remarks-display-${proposedTitleId}`);
    const form = document.getElementById(`edit-remarks-form-${proposedTitleId}`);

    if (displayDiv.style.display === "none") {
        displayDiv.style.display = "block";
        form.style.display = "none";
    } else {
        displayDiv.style.display = "none";
        form.style.display = "block";
    }
}

function toggleCommentEdit() {
    const displayDiv = document.getElementById("remarks-display-comment");
    const form = document.getElementById("edit-remarks-form-comment");

    if (displayDiv.style.display === "none") {
        displayDiv.style.display = "block";
        form.style.display = "none";
    } else {
        displayDiv.style.display = "none";
        form.style.display = "block";
    }
}

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
<style>
    #items-table .thead-background {
    background-color:rgb(56, 120, 193);
    color: white;
}
#loadingOverlay {
    position: fixed; 
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5); 
    display: flex; 
    justify-content: center;
    align-items: center;
    z-index: 1050; 
}

#loadingSpinnerContainer {
    width: 5rem;
    height: 5rem;
    color: #007bff; 
}
</style>