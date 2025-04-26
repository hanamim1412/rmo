<?php
   session_start();
   if (!isset($_SESSION['user_id'])) {
       $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
       header("Location: ../login.php");
       exit();
   }
   include('dean-master.php');
   require '../config/connect.php';
   include '../messages.php';
   $title = "Settings";
   ob_start();
   $user_id = $_SESSION['user_id'];



    $query = "SELECT department_id FROM ACCOUNTS WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            die("Database Query Failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (!$result || mysqli_num_rows($result) === 0) {
            die("Unable to fetch department information for the logged-in user.");
        }
        $dean_data = mysqli_fetch_assoc($result);
        $dean_department_id = $dean_data['department_id'];
    function getAgenda($dean_department_id) {
        global $conn;
        $query = "
            SELECT agenda_id, agenda_name, description 
            FROM college_research_agenda
            WHERE department_id = ?
        ";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            die("Database Query Failed: " . mysqli_error($conn));
        }
    
        mysqli_stmt_bind_param($stmt, 'i', $dean_department_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    
        if (!$result) {
            die("Database Query Failed: " . mysqli_error($conn));
        }
    
        $agenda_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $agenda_list[] = $row;
        }
    
        return $agenda_list;
    }
    
    $agenda_list = getAgenda($dean_department_id);

    function getResearchAdvisers() {
        global $conn;
        $user_id = $_SESSION['user_id']; // Get the current user's ID
    
        $query = "
            SELECT 
                acc.user_id, 
                acc.firstname, 
                acc.lastname, 
                acc.smc_email, 
                acc.contact, 
                dept.department_name as department, 
                acc.is_active, 
                acc.date_created
            FROM ACCOUNTS acc
            LEFT JOIN DEPARTMENTS dept ON acc.department_id = dept.department_id
            WHERE acc.user_type = 'research_adviser'
              AND acc.created_by = $user_id
            ORDER BY acc.date_created
        ";
        $result = mysqli_query($conn, $query);
    
        if (!$result) {
            die("Database Query Failed: " . mysqli_error($conn));
        }
    
        $research_advisers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $research_advisers[] = $row;
        }
    
        return $research_advisers;
    }
    function getPanelists() {
        global $conn;
        $user_id = $_SESSION['user_id']; // Get the current user's ID
    
        $query = "
            SELECT 
                ACCOUNTS.user_id, 
                ACCOUNTS.firstname, 
                ACCOUNTS.lastname, 
                ACCOUNTS.smc_email, 
                ACCOUNTS.contact, 
                ACCOUNTS.department_id, 
                dept.department_name as department, 
                ACCOUNTS.is_active, 
                ACCOUNTS.date_created, 
                ACCOUNTS.user_type
            FROM ACCOUNTS 
            LEFT JOIN DEPARTMENTS dept ON ACCOUNTS.department_id = dept.department_id
            WHERE ACCOUNTS.user_type = 'panelist'
              AND ACCOUNTS.created_by = $user_id
            ORDER BY ACCOUNTS.date_created
        ";
        $result = mysqli_query($conn, $query);
    
        if (!$result) {
            die("Database Query Failed: " . mysqli_error($conn));
        }
    
        $panelists = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $panelists[] = $row;
        }
    
        return $panelists;
    }
    
    function getChairmen() {
        global $conn;
        $user_id = $_SESSION['user_id']; // Get the current user's ID
    
        $query = "
            SELECT 
                ACCOUNTS.user_id, 
                ACCOUNTS.firstname, 
                ACCOUNTS.lastname, 
                ACCOUNTS.smc_email, 
                ACCOUNTS.contact, 
                ACCOUNTS.department_id, 
                dept.department_name as department, 
                ACCOUNTS.is_active, 
                ACCOUNTS.date_created, 
                ACCOUNTS.user_type
            FROM ACCOUNTS 
            LEFT JOIN DEPARTMENTS dept ON ACCOUNTS.department_id = dept.department_id
            WHERE ACCOUNTS.user_type = 'chairman'
              AND ACCOUNTS.created_by = $user_id
            ORDER BY ACCOUNTS.date_created
        ";
        $result = mysqli_query($conn, $query);
    
        if (!$result) {
            die("Database Query Failed: " . mysqli_error($conn));
        }
    
        $chairmen = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $chairmen[] = $row;
        }
    
        return $chairmen;
    }
    
    $panelists = getPanelists();
    $chairmen = getChairmen();
    
$advisers = getResearchAdvisers();
?>
<section id="settings" class="pt-4">
    <div class="header-container pt-4">
        <h4>
        <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
                    <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
                </a>
        Settings</h4>
    </div>
        <?php if (!empty($messages)): ?>
            <div class="container mt-3">
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= htmlspecialchars($message['tags']) ?> alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i><?= htmlspecialchars($message['content']) ?>
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

                <div class="table-responsive p-3 border">
                    <div class="d-flex justify-content-between align-items-center" style="gap: 10px;">
                        <h5 class="mb-1">College Research Agenda</h5>
                        <a href="add-agenda.php" class="btn btn-success mb-1"><i class="fas fa-plus"></i></a>
                    </div>
                    <table id="items-table" class="table table-bordered table-sm display">
                        <thead class="thead-background">
                            <tr>
                                <th>#</th>
                                <th>Agenda Name</th>
                                <th>Descriptions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach ($agenda_list as $row): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars($row['agenda_name']) ?></td>
                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                    <td class="d-flex justify-content-between align-items-center" style="gap: 5px;">
                                        <a href="edit-agenda.php?id=<?= $row['agenda_id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <form action="delete-agenda.php?" method="POST" 
                                            onsubmit="return confirm('Are you sure you want to delete this College Research Agenda?');" 
                                            style="display: inline;">
                                            <input type="hidden" name="agenda_id" value="<?= htmlspecialchars($row['agenda_id']) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="table-responsive p-3 border mt-2">
                    <div class="d-flex justify-content-between align-items-center" style="gap: 10px;">
                        <h5 class="mb-1">Research Advisers</h5>
                        <a href="add-adviser.php" class="btn btn-success mb-1"><i class="fas fa-plus"></i></a>
                    </div>
                    <table id="advisers-table" class="table table-bordered table-sm display">
                        <thead class="thead-background">
                            <tr>
                                <th>#</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Department</th>
                                <th>Date Added</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($advisers)):?>
                            <?php $i=1; foreach ($advisers as $row): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars($row['firstname']) ?></td>
                                    <td><?= htmlspecialchars($row['lastname']) ?></td>
                                    <td><?= htmlspecialchars($row['smc_email']) ?></td>
                                    <td><?= htmlspecialchars($row['contact']) ?></td>
                                    <td><?= htmlspecialchars($row['department']) ?></td>
                                    <td><?= htmlspecialchars($row['date_created']) ?></td>
                                    <td>
                                        <?php if (isset($row['user_id']) && $row['is_active']): ?>
                                            <form method="post" action="update-user-status.php" onsubmit="return confirm('Are you sure you want to deactivate this research adviser?');" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']); ?>">
                                                <input type="hidden" name="firstname" value="<?= htmlspecialchars($row['firstname']); ?>">
                                                <input type="hidden" name="lastname" value="<?= htmlspecialchars($row['lastname']); ?>">
                                                <input type="hidden" name="new_status" value="0">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Deactivate account for <?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?>">Deactivate</button>
                                            </form>
                                        <?php elseif (isset($row['user_id'])): ?>
                                            <form method="post" action="update-user-status.php" onsubmit="return confirm('Are you sure you want to activate this research adviser?');" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']); ?>">
                                                <input type="hidden" name="firstname" value="<?= htmlspecialchars($row['firstname']); ?>">
                                                <input type="hidden" name="lastname" value="<?= htmlspecialchars($row['lastname']); ?>">
                                                <input type="hidden" name="new_status" value="1">
                                                <button type="submit" class="btn btn-sm btn-success" title="Activate account for <?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?>">Activate</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td class="d-flex justify-content-between align-items-center" style="gap: 5px;">
                                        <a href="edit-adviser.php?id=<?= $row['user_id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <form action="delete-user.php?" method="POST" onsubmit="return confirm('Are you sure you want to delete this research adviser?');" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="text-center text-bg-alert"><td colspan="9">No Research Advisers Found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>      
                </div>

                <div class="table-responsive p-3 border mt-2">
                    <div class="d-flex justify-content-between align-items-end" style="gap: 10px;">
                        <h5 class="mb-1">Panelists</h5>
                        <a href="add-panelist.php" class="btn btn-success mb-1"><i class="fas fa-plus"></i></a>
                    </div>
                    <table id="panelists-table" class="table table-bordered table-sm display">
                        <thead class="thead-background">
                            <tr>
                                <th>#</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Department</th>
                                <th>Date Added</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($panelists)): ?>
                                <?php $i = 1; foreach ($panelists as $row): ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= htmlspecialchars($row['firstname']) ?></td>
                                        <td><?= htmlspecialchars($row['lastname']) ?></td>
                                        <td><?= htmlspecialchars($row['smc_email']) ?></td>
                                        <td><?= htmlspecialchars($row['contact']) ?></td>
                                        <td><?= htmlspecialchars($row['department']) ?></td>
                                        <td><?= htmlspecialchars($row['date_created']) ?></td>
                                        <td>
                                            <?php if ($row['is_active']): ?>
                                                <form method="post" action="update-user-status.php" onsubmit="return confirm('Are you sure you want to deactivate this user?');" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']); ?>">
                                                    <input type="hidden" name="new_status" value="0">
                                                    <button type="submit" class="btn btn-sm btn-danger">Deactivate</button>
                                                </form>
                                            <?php else: ?>
                                                <form method="post" action="update-user-status.php" onsubmit="return confirm('Are you sure you want to activate this user?');" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']); ?>">
                                                    <input type="hidden" name="new_status" value="1">
                                                    <button type="submit" class="btn btn-sm btn-success">Activate</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                        <td class="d-flex justify-content-between align-items-center" style="gap: 5px;">
                                            <a href="edit-user.php?id=<?= $row['user_id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="delete-user.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="text-center text-bg-alert"><td colspan="9">No Panelists Found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive p-4 border mt-2">
                    <div class="d-flex justify-content-between align-items-center" style="gap: 10px;">
                        <h5 class="mb-1">Chairmen</h5>
                        <a href="add-chairman.php" class="btn btn-success mb-1"><i class="fas fa-plus"></i></a>
                    </div>
                    <table id="chairman-table" class="table table-bordered table-sm display">
                        <thead class="thead-background">
                            <tr>
                                <th>#</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Department</th>
                                <th>Date Added</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($chairmen)): ?>
                                <?php $i = 1; foreach ($chairmen as $row): ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= htmlspecialchars($row['firstname']) ?></td>
                                        <td><?= htmlspecialchars($row['lastname']) ?></td>
                                        <td><?= htmlspecialchars($row['smc_email']) ?></td>
                                        <td><?= htmlspecialchars($row['contact']) ?></td>
                                        <td><?= htmlspecialchars($row['department']) ?></td>
                                        <td><?= htmlspecialchars($row['date_created']) ?></td>
                                        <td>
                                            <?php if ($row['is_active']): ?>
                                                <form method="post" action="update-user-status.php" onsubmit="return confirm('Are you sure you want to deactivate this user?');" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']); ?>">
                                                    <input type="hidden" name="new_status" value="0">
                                                    <button type="submit" class="btn btn-sm btn-danger">Deactivate</button>
                                                </form>
                                            <?php else: ?>
                                                <form method="post" action="update-user-status.php" onsubmit="return confirm('Are you sure you want to activate this user?');" style="display:inline;">
                                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']); ?>">
                                                    <input type="hidden" name="new_status" value="1">
                                                    <button type="submit" class="btn btn-sm btn-success">Activate</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                        <td class="d-flex justify-content-between align-items-center" style="gap: 5px;">
                                            <a href="edit-user.php?id=<?= $row['user_id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="delete-user.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['user_id']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="text-center text-bg-alert"><td colspan="9">No Chairmen Found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

</section>

<script>
$(document).ready(function () {
    var table1 = $('#items-table').DataTable({
        scrollX: true,
        autoWidth: false,
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 5,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: "Prev",
                next: "Next"
            }
        }
    });
    var table2 = $('#advisers-table').DataTable({
        scrollX: true,
        autoWidth: false,
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 5,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: "Prev",
                next: "Next"
            }
        }
    });
    var table3 = $('#panelists-table').DataTable({
        scrollX: true,
        autoWidth: false,
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 5,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: "Prev",
                next: "Next"
            }
        }
    });
    var table4 = $('#chairman-table').DataTable({
        scrollX: true,
        autoWidth: false,
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 5,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: "Prev",
                next: "Next"
            }
        }
    });
});
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
.thead-background {
    background-color:rgb(56, 120, 193);
    color: white;
}
</style>