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
    $title = "Edit Agenda";
    ob_start();

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "SELECT ir_agenda_name, sub_areas FROM institutional_research_agenda WHERE ir_agenda_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $ir_agenda_name = $row['ir_agenda_name'];
            $description = $row['sub_areas'];
        } else {
            $_SESSION['messages'][] = ['tags' => 'danger', 'content' => 'Agenda not found!'];
            header("Location: settings.php");
            exit();
        }
    }
?>
<section id="settings" class="pt-4">
    <div class="header-container pt-4">
        <h4>
        <a href="javascript:history.back()" class="btn btn-link" style="font-size: 1rem; text-decoration: none; color: black;">
                    <i class="fas fa-arrow-left" style="margin-right: 10px; font-size: 1.2rem;"></i>
                    Back
                </a>
        Edit Institutional Research Agenda</h4>
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
        <div class="container">
            <form id="editform" method="POST" action="submit-edit-agenda.php" class="form-container">
                <div class="mb-3">
                    <label for="ir_agenda_name" class="form-label">Agenda Name</label>
                    <input type="text" id="ir_agenda_name" name="ir_agenda_name" class="form-control" value="<?= htmlspecialchars($ir_agenda_name) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="8" required><?= htmlspecialchars($description) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Agenda</button>
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