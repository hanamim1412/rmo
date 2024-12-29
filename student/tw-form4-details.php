<?php 
// student/twform-4-details.php
session_start();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
        header("Location: ../login.php");
        exit();
    }
    include('student-master.php');
    require '../config/connect.php';
    include '../messages.php';
    $title = "TW form 4 Details";
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
    function getTWForm4Details($tw_form_id){
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
            return $stmt->get_result();
    }
    function GetProponents($tw_form_id) {
        global $conn;
        $query = "
            SELECT 
                pro.proponent_id,
                pro.tw_form_id,
                pro.firstname,
                pro.lastname,
                pro.receipt_id,
                rp.receipt_num,
                rp.receipt_img,
                rp.date_paid
                FROM PROPONENTS pro
                LEFT JOIN TW_FORMS tw ON pro.tw_form_id = tw.tw_form_id
                LEFT JOIN RECEIPTS rp ON pro.receipt_id = rp.receipt_id
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

    $tw_form_id = $_GET['tw_form_id']; 
    $twform_details = getTWFormDetails($tw_form_id); 
    $twform4_details = getTWForm4Details($tw_form_id);  
    $proponents = GetProponents($tw_form_id);  
?>


<section id="twform-4-details" class="pt-4">
    <div class="header-container pt-4">
        <h4 class="text-left">
        <a href="javascript:history.back()" class="btn btn-link">
            <i class="fas fa-arrow-left" style="margin-right: 5px;text-decoration: none; color: black; font-size: 1.2rem;"></i>
         </a>
            TW form 4: Approval for Oral Examination/Final Defense
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
                <div><strong>Comments:</strong> <?= htmlspecialchars($twform_details['comments']) ?></div> 
                
                <div><strong>Submitted On:</strong> <?= date("Y-m-d", strtotime($twform_details['submission_date'])) ?></div>
                <div><strong>Last Updated:</strong> <?= date("Y-m-d", strtotime($twform_details['last_updated'])) ?></div>
                <div>
                    <strong>Proponents and Receipt Details:</strong> 
                    <?php if (!empty($proponents)): ?>
                        <ul>
                            <?php foreach ($proponents as $proponent): ?>
                                <li>
                                    <?= ucwords(htmlspecialchars($proponent['firstname'] . ' ' . $proponent['lastname'])) ?>
                                    <ul>
                                        <li>Receipt Number: <?= htmlspecialchars($proponent['receipt_num']) ?></li>
                                        <li>Date Paid: <?= htmlspecialchars($proponent['date_paid']) ?></li>
                                        <li>Receipt Image: 
                                            <?php if (!empty($proponent['receipt_img'])): ?>
                                                <?php 
                                                    $file_extension = strtolower(pathinfo($proponent['receipt_img'], PATHINFO_EXTENSION)); 
                                                    $is_image = in_array($file_extension, ['jpg', 'jpeg', 'png']);
                                                ?>
                                                <?php if ($is_image): ?>
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" class="badge btn-primary text-decoration-none" data-bs-target="#receiptImageModal-<?= $proponent['proponent_id'] ?>">View Image</a>
                                                    <a href="../uploads/receipts/<?= htmlspecialchars($proponent['receipt_img']) ?>" download class="badge btn-success text-decoration-none">Download File</a>
                                                <?php else: ?>
                                                    <a href="../uploads/receipts/<?= htmlspecialchars($proponent['receipt_img']) ?>" target="_blank" class="badge btn-primary text-decoration-none">View PDF</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                No image available
                                            <?php endif; ?>
                                        </li>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No proponents found for this form.</p>
                    <?php endif; ?>
                </div>

            <?php foreach ($proponents as $proponent): ?>
                <?php if (!empty($proponent['receipt_img'])): ?>
                    <?php 
                        $file_extension = strtolower(pathinfo($proponent['receipt_img'], PATHINFO_EXTENSION)); 
                        $is_image = in_array($file_extension, ['jpg', 'jpeg', 'png']);
                    ?>
                    <?php if ($is_image): ?>
                        <div class="modal fade" id="receiptImageModal-<?= $proponent['proponent_id'] ?>" tabindex="-1" aria-labelledby="receiptImageModalLabel-<?= $proponent['proponent_id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="receiptImageModalLabel-<?= $proponent['proponent_id'] ?>">Receipt Image</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <img src="../uploads/receipts/<?= htmlspecialchars($proponent['receipt_img']) ?>" alt="Receipt Image" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

            <div class="table-container mt-4">
                        <table id="items-table" class="table table-bordered display">
                            <thead class="thead-background">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Thesis Title</th>
                                    <th scope="col">Date of Defense</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Venue</th>
                                    <th scope="col">Date Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($twform4_details as $index => $twform4): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($twform4['thesis_title']) ?></td>
                                        <td><?= htmlspecialchars($twform4['defense_date']) ?></td>
                                        <td>
                                            <?php 
                                            $time_str = trim($twform4['time']);  
                                            $formatted_time = DateTime::createFromFormat('H:i:s', $time_str);

                                            if ($formatted_time) {
                                                echo htmlspecialchars($formatted_time->format('g:i A')); 
                                            } else {
                                                echo "Invalid time"; 
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($twform4['place']) ?></td>
                                        <td><?= htmlspecialchars($twform4['date_submitted']) ?></td>
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
<style>
    #items-table .thead-background {
    background-color:rgb(56, 120, 193);
    color: white;
}
</style>