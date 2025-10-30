// js/View.js

export const View = {
    elements: {}, 
    charts: {
        weeklySalesChart: null,
        topProductsChart: null,
        monthlyReportChart: null,
    },
    
    initializeDOM() {
        this.elements = {
            loginScreen: document.getElementById('login-screen'), 
            appContainer: document.getElementById('app-container'),
            sidebarNav: document.getElementById('sidebar-nav'),
            pages: document.querySelectorAll('.page-section'),
            roleSwitcher: document.getElementById('role-switcher'),
            tablesGrid: document.getElementById('tables-grid'),
            tableStatusModal: document.getElementById('table-status-modal'),
            modalTableTitle: document.getElementById('modal-table-title'),
            tableStatusSelect: document.getElementById('table-status-select'),
            sidebar: document.getElementById('sidebar'),
            modalSaveBtn: document.getElementById('modal-save-btn'),
            modalCancelBtn: document.getElementById('modal-cancel-btn'),
            loginForm: document.getElementById('login-form'),
            logoutButton: document.getElementById('logout-button'),
            menuToggle: document.getElementById('menu-toggle')
        };
    },

    toggleAppVisibility(isLoggedIn) {
        if (this.elements.loginScreen) {
             this.elements.loginScreen.style.display = 'none'; 
        }
        if (this.elements.appContainer) {
            this.elements.appContainer.style.display = 'block';
        }
        
        if (!isLoggedIn) {
            Object.values(this.charts).forEach(chart => chart && chart.destroy());
        }
    },

    /** 🛑 FUNCIÓN CORREGIDA: Fuerza el display: block/none. */
    setActivePage(pageId) {
        // 1. Desactivar todos los enlaces de la barra lateral (visual)
        this.elements.sidebarNav.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
        const link = this.elements.sidebarNav.querySelector(`[data-page="${pageId}"]`);
        if (link) {
            link.classList.add('active');
        }

        // 2. Ocultar todas las secciones de contenido
        this.elements.pages.forEach(p => {
            p.classList.remove('active');
            p.style.display = 'none'; // <-- Fuerza la ocultación
        });

        // 3. Activar y mostrar la página actual
        const activePageElement = document.getElementById(`${pageId}-page`);

        if (activePageElement) {
            activePageElement.classList.add('active');
            activePageElement.style.display = 'block'; // <-- Fuerza la visualización
        }
    },

    renderSidebarForRole(role, currentPage) {
        const links = this.elements.sidebarNav.querySelectorAll('[data-role]');
        let isCurrentPageVisible = false;

        links.forEach(link => {
            const roles = link.dataset.role ? link.dataset.role.split(' ') : [];
            const pageId = link.dataset.page;
            
            if (!roles.length || roles.includes(role)) {
                link.style.display = 'flex';
                if (pageId === currentPage) isCurrentPageVisible = true;
            } else {
                link.style.display = 'none';
            }
        });
        return isCurrentPageVisible; 
    },

    renderTables(tablesData) {
        if (!this.elements.tablesGrid) return;
        this.elements.tablesGrid.innerHTML = '';
        const statusClasses = { free: 'table-status-free', occupied: 'table-status-occupied', reserved: 'table-status-reserved' };
        const statusText = { free: 'Libre', occupied: 'Ocupada', reserved: 'Reservada' };

        tablesData.forEach(table => {
            const tableEl = document.createElement('div');
            tableEl.className = `table-item ${statusClasses[table.status]}`;
            tableEl.innerHTML = `Mesa ${table.id}<br><span style="font-size:0.9em; font-weight:normal;">${statusText[table.status]}</span>`;
            tableEl.dataset.tableId = table.id;
            
            tableEl.addEventListener('click', () => {
                document.dispatchEvent(new CustomEvent('tableClick', { detail: { tableId: table.id } }));
            });
            
            this.elements.tablesGrid.appendChild(tableEl);
        });
    },
    
    showTableModal(tableId, currentStatus) {
        this.elements.modalTableTitle.textContent = `Mesa ${tableId}`;
        this.elements.tableStatusSelect.value = currentStatus;
        this.elements.modalTableTitle.style.display = 'flex';
    },

    hideTableModal() {
        this.elements.modalTableTitle.style.display = 'none';
    },
    
    toggleSidebar() {
        // Implementación básica del toggle sin clases de Tailwind
        const currentTransform = this.elements.sidebar.style.transform;
        this.elements.sidebar.style.transform = (currentTransform === 'translateX(0px)' || currentTransform === '') ? 'translateX(-100%)' : 'translateX(0px)';
    },

    closeSidebar() {
        // Implementación básica para cerrar
        this.elements.sidebar.style.transform = 'translateX(-100%)';
    },
    
    // NOTA: Se requiere la librería Chart.js para que esta función opere correctamente.
    renderCharts() { 
        if(typeof Chart === 'undefined') {
            console.error("Chart.js no está cargado. Los gráficos no se renderizarán.");
            return;
        }

        if(this.charts.weeklySalesChart) this.charts.weeklySalesChart.destroy();
        const weeklyCtx = document.getElementById('weekly-sales-chart').getContext('2d');
        this.charts.weeklySalesChart = new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [{ label: 'Ventas', data: [1200, 1900, 3000, 5000, 2300, 3100, 4500], backgroundColor: '#3b82f6' }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // (Otros gráficos omitidos por brevedad)
    }
};