<!-- panelist/panelist-sidebar.php  -->

<?php
require '../config/connect.php';

$user_id = $_SESSION['user_id']; 
$query = "SELECT firstname FROM accounts WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $firstname);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>

<div class="header-bar">
    <div class="menu-container">
        <a href="#"><img src="../images/src/portal-logo.png" alt="logo"></a>
        <div class="hamburger" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    <div class="user-profile">
        <i class="fas fa-user-circle"></i>
        <span class="user-name"><?php echo ucwords(htmlspecialchars($firstname)); ?></span>
    </div>
</div>

<div class="sidebar d-flex flex-column p-3">
    <ul class="nav flex-column mt-3" id="sidebar-nav">
    <a href="#" class="d-flex align-items-center mb-md-0 me-md-auto text-white text-decoration-none mt-3">
        <span class="fs-5 d-none d-sm-inline text-dark">Navigation</span>
    </a>
    <br>
        <li class="section-title text-dark">Research</li>
        <li class="nav-item">
            <a href="tw-forms.php" class="nav-link">
            <i class="fa-regular fa-file"></i> <span>TW forms</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="reports.php" class="nav-link">
                <i class="fa-regular fa-folder-open"></i> <span>Reports</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../logout.php" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
            <form id="logout-form" action="../logout.php" method="POST" style="display: none;">
            </form>
        </li>
    </ul>
</div>

<script>
    function toggleSidebar() {
    console.log('Sidebar toggle clicked');
    
        const sidebars = document.querySelectorAll('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        sidebars.forEach(sidebar => sidebar.classList.toggle('collapsed')); 
        mainContent.classList.toggle('collapsed');
        

            if (sidebars[0].classList.contains('collapsed')) {
                console.log('Sidebar is collapsed');
                logoText.style.display = 'none';
                logoImg.style.display = 'block';  
            } else {
                console.log('Sidebar is expanded');
                logoText.style.display = 'block';
                logoImg.style.display = 'none';   
            }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const currentPath = window.location.pathname.split('/').pop();
        const navItems = document.querySelectorAll('#sidebar-nav .nav-link'); 

        navItems.forEach(function(item) {
            const linkPath = item.getAttribute('href').split('/').pop();

            if (currentPath === linkPath) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    });
</script>
<style>
.header-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    display: flex;
    align-items: center;
    justify-content: space-between; 
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    background-color: #4a90e2;
    padding: 5px 10px;
    z-index: 1000;
}

.menu-container {
    display: flex;
    align-items: center;
    gap: 5.5rem;
}

.header-bar img {
    width: 100px;
    cursor: pointer;
}

.hamburger {
    font-size: 20px;
    color: white;
    cursor: pointer;
}

.hamburger i {
    display: inline-block;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
    font-size: 16px;
    font-weight: bold;
}

.user-profile i {
    font-size: 24px;
}

</style>