document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-btn');
    const mainContent = document.getElementById('main-content');
    
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('shifted');
    });

    const submenuToggles = document.querySelectorAll('.submenu-toggle');

    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const parentLi = this.closest('.has-submenu');
            
            parentLi.classList.toggle('active');
            
            submenuToggles.forEach(otherToggle => {
                const otherParentLi = otherToggle.closest('.has-submenu');
                if (otherParentLi !== parentLi && otherParentLi.classList.contains('active')) {
                    otherParentLi.classList.remove('active');
                }
            });
        });
    });
});