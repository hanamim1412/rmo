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
?>
<section id="settings" class="pt-4">
    <div class="header-container pt-4">
        <h4>
        <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
                    <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
                </a>
        College Research Agenda</h4>
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

                <div class="table-responsive">
                    <div class="d-flex justify-content-end align-items-center" style="gap: 10px;">
                        <a href="add-agenda.php" class="btn btn-add-item btn-success">Add</a>
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
                                        <a href="edit-agenda.php?id=<?= $row['agenda_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $row['agenda_id'] ?>">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
</section>

<script>
    $(document).ready(function () {
    var table = $('#items-table').DataTable({
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

        const deleteBtns = document.querySelectorAll('.delete-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                
                if (confirm('Are you sure you want to delete this agenda?')) {
                    window.location.href = 'delete-agenda.php?id=' + id;
                }
            });
        });
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