<!-- student/student-sidebar.php  -->

<div class="sidebar d-flex flex-column p-3">
    <div class="logo" id="logo">
        <img id="logo-img" src="../images/src/SMC-logo.png" alt="Logo"> 
    </div>
    <style>
        #logo-text {
            display: block; 
        }
        
        #logo-img {
            display: none; 
            width: 50px;
            height: auto;
        }
        .sidebar.collapsed .logo {
            justify-content: center;
            padding: 0;
            margin: 0;
        }
        
        .sidebar.collapsed #logo-text {
            display: none; 
        }
        
        .sidebar.collapsed #logo-img {
            display: block; 
        }
    </style>
     <div class="hamburger" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <a href="#" class="d-flex align-items-center mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-5 d-none d-sm-inline text-dark">Navigation</span>
    </a>
    <br>
    <ul class="nav flex-column mt-3" id="sidebar-nav">
        <li class="section-title text-dark">Research</li>
        <li class="nav-item">
            <a href="tw-forms.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> <span>TW forms</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="reports.php" class="nav-link">
                <i class="fas fa-chart-line"></i> <span>Analytics</span>
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
        
        const logoText = document.getElementById('logo-text');
        const logoImg = document.getElementById('logo-img');

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
