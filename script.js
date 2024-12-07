document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        const closeButton = alert.querySelector('.close');
        closeButton.addEventListener('click', function () {
            alert.style.display = 'none';  
        });

        setTimeout(function () {
            alert.style.display = 'none'; 
        }, 5000);
    });
});

function handleBackButton() {
    const backButton = document.querySelector('.btn-link.mb-4');
    
    const isAuthenticated = backButton.getAttribute('data-authenticated') === 'true';
    const isStudent = backButton.getAttribute('data-student') === 'true';
    const isDean = backButton.getAttribute('data-dean') === 'true';
    const isRmo = backButton.getAttribute('data-rmo_staff') === 'true';
    const isPanel = backButton.getAttribute('data-panelist') === 'true';
    const isAdvisor = backButton.getAttribute('data-advisor') === 'true';

    if (isAuthenticated) {
        history.replaceState(null, null, location.href);

        if (isStudent) {
            window.location.href = '/student/';
        } else if (isDean) {
            window.location.href = '/dean/';
        } else if (isRmo) {
            window.location.href = '/rmo_staff/';
        } else if (isPanel) {    
            window.location.href = '/panelist/';
        } else if (isAdvisor) {
            window.location.href = '/advisor/';
        }
    } else {
        window.history.back();
    }
}