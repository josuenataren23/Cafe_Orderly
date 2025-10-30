document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-btn');
    const mainContent = document.getElementById('main-content');


    toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('shifted');
    });


    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parentLi = this.closest('.has-submenu');
            

            document.querySelectorAll('.has-submenu').forEach(item => {
                if (item !== parentLi) {
                    item.classList.remove('active');
                }
            });
            
            parentLi.classList.toggle('active');
        });
    });

    // Mostrar sección inicial
    const hash = window.location.hash;
    if (hash) {
        const formType = hash.replace('#', '').replace('-form', '');
        showForm(formType);
    } else {
        showForm('bebidas');
    }


    document.querySelectorAll('.submenu a').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = link.getAttribute('href');
            if (href && href.startsWith('#')) {
                e.preventDefault();
                const formType = href.replace('#', '');
                showForm(formType);


                history.pushState(null, null, href);
            }
        });
    });


    window.addEventListener('popstate', function() {
        const hash = window.location.hash;
        if (hash) {
            const formType = hash.replace('#', '').replace('-form', '');
            showForm(formType);
        }
    });
});

function showForm(formType) {

    const forms = document.querySelectorAll('.form-section');
    forms.forEach(form => form.classList.remove('active'));
    

    const selectedForm = document.getElementById(formType + '-form');
    if (selectedForm) {
        selectedForm.classList.add('active');
        

        document.querySelectorAll('.submenu a').forEach(link => {
            const href = link.getAttribute('href');
            if (href === '#' + formType) {
                link.closest('.has-submenu').classList.add('active');
            }
        });
    }
}


function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}


document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');

    if(toggleBtn && sidebar){
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
    }
});
