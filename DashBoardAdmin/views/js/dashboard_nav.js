document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.dashboard-seccion');

    function hideAllSections() {
        sections.forEach(s => s.style.display = 'none');
    }

    function showSection(name) {
        hideAllSections();
        const el = document.getElementById('seccion-' + name);
        if (el) el.style.display = 'block';
    }

    // Al cargar, muestra la sección Inicio
    showSection('inicio');

    // Maneja clicks en los botones del menú
    document.querySelectorAll('[data-section]').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const sec = link.getAttribute('data-section');
            showSection(sec);
        });
    });
});
