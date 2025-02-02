<?php
   session_start();
   if (!isset($_SESSION['user_id'])) {
       $_SESSION['messages'][] = ['tags' => 'warning', 'content' => "You need to log in"];
       header("Location: ../login.php");
       exit();
   }
   include('rmo-master.php');
   require '../config/connect.php';
   include '../messages.php';
   $title = "Settings";
   ob_start();

    $query = "SELECT ir_agenda_id, ir_agenda_name, sub_areas FROM institutional_research_agenda";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Database Query Failed: " . mysqli_error($conn));
    }
?>
<section id="settings" class="pt-4">
    <div class="header-container pt-4">
        <h4>
        <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
                    <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
                </a>
        Institutional Research Agenda</h4>
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
                        <a href="add-ins_agenda.php" class="btn btn-add-item btn-success mb-1"><i class="fas fa-plus"></i></a>
                    </div>
                    <table id="items-table" class="table table-bordered table-sm display">
                        <thead class="thead-background">
                            <tr>
                                <th>Agenda Name</th>
                                <th>Sub Areas</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['ir_agenda_name']) ?></td>
                                    <td><?= htmlspecialchars($row['sub_areas']) ?></td>
                                    <td class="d-flex justify-content-between align-items-center" style="gap: 5px;">
                                            <a href="edit-ins_agenda.php?ir_agenda_id=<?= $row['ir_agenda_id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="delete-ins_agenda.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this agenda?');" style="display: inline;">
                                                <input type="hidden" name="ir_agenda_id" value="<?= htmlspecialchars($row['ir_agenda_id']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
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
$(document).ready(function () {
    $('#items-table').DataTable({
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
</script>

<?php
$content = ob_get_clean();
include('rmo-master.php');
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