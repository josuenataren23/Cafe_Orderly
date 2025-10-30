// js/Controller.js

import { Model } from './Model.js';
import { View } from './View.js';

export const Controller = {
  currentEditingTableId: null,

  // referencia al controlador de empleados (puede venir como window.PersonalController o window.EmployeesController)
  employeesController: null,

  init() {
    View.initializeDOM();
    View.toggleAppVisibility(Model.appState.loggedIn);
    View.renderSidebarForRole(Model.appState.currentUserRole, Model.appState.currentPage);
    View.renderTables(Model.tablesData);
    View.renderCharts();

    this.setupEventListeners();

    // intentar enlazar con el controlador de empleados si ya está expuesto globalmente
    this.bindEmployeesController();

    // Si la página activa al cargar es personal, inicializamos comportamiento
    const personalSection = document.getElementById('personal-page');
    if (personalSection && personalSection.classList.contains('active')) {
      this.enterPersonalPage();
    }
  },

  setupEventListeners() {
    // Sidebar
    const sidebarLinks = View.elements.sidebarNav.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(link => link.addEventListener('click', Controller.handleNavigation.bind(Controller)));

    // Cabecera
    if (View.elements.roleSwitcher) View.elements.roleSwitcher.addEventListener('change', this.handleRoleChange.bind(this));
    if (View.elements.logoutButton) View.elements.logoutButton.addEventListener('click', this.handleLogout.bind(this));

    // Modal de mesas
    if (View.elements.modalSaveBtn) {
      View.elements.modalSaveBtn.addEventListener('click', this.handleTableModalSave.bind(this));
      View.elements.modalCancelBtn.addEventListener('click', View.hideTableModal);
    }

    // Toggle móvil
    if (View.elements.menuToggle) View.elements.menuToggle.addEventListener('click', () => View.toggleSidebar());

    document.addEventListener('tableClick', this.handleTableClick.bind(this));
  },

  bindEmployeesController() {
    // soporte para distintas nombres globales: PersonalController o EmployeesController
    if (this.employeesController) return; // ya enlazado
    if (window.PersonalController) {
      this.employeesController = window.PersonalController;
      return;
    }
    if (window.EmployeesController) {
      this.employeesController = window.EmployeesController;
      return;
    }
    // Si no está aún, no es error crítico: lo intentaremos enlazar más tarde al entrar en la página.
    console.info('No se encontró PersonalController/EmployeesController global al iniciar. Se intentará enlazar cuando se entre en la página personal.');
  },

  handleLogout() {
    Model.logout();
  },

  handleNavigation(e) {
    e.preventDefault();
    const link = e.currentTarget;
    if (link && link.dataset.page) Controller.navigateTo(link.dataset.page);
  },

  handleRoleChange(e) {
    const newRole = e.target.value;
    Model.setCurrentRole(newRole);
    const visible = View.renderSidebarForRole(newRole, Model.appState.currentPage);
    if (!visible) Controller.navigateTo('dashboard');
  },

  navigateTo(pageId) {
    Model.setCurrentPage(pageId);
    View.setActivePage(pageId);

    if (pageId === 'personal') {
      this.enterPersonalPage();
    } else {
      this.leavePersonalPage();
    }

    if (pageId === 'mesas') View.renderTables(Model.tablesData);
    if (pageId === 'dashboard' || pageId === 'reportes') View.renderCharts();
  },

  handleTableClick(e) {
    const tableId = parseInt(e.detail.tableId);
    const table = Model.tablesData.find(t => t.id === tableId);
    if (table) {
      Controller.currentEditingTableId = tableId;
      View.showTableModal(tableId, table.status);
    }
  },

  handleTableModalSave() {
    if (Controller.currentEditingTableId !== null) {
      const newStatus = View.elements.tableStatusSelect.value;
      Model.updateTableStatus(Controller.currentEditingTableId, newStatus);
      View.renderTables(Model.tablesData);
      Controller.currentEditingTableId = null;
      View.hideTableModal();
    }
  },

  // -------------------------
  // Entrar / Salir página Personal
  // -------------------------
  enterPersonalPage() {
    // enlazar con el módulo de empleados si no lo hemos hecho
    this.bindEmployeesController();

    // si existe el controlador, inicializar y mostrar botón
    if (this.employeesController) {
      try {
        if (typeof this.employeesController.init === 'function') this.employeesController.init();
        if (typeof this.employeesController.createAddButton === 'function') this.employeesController.createAddButton();
        if (typeof this.employeesController.load === 'function') this.employeesController.load();
      } catch (err) {
        console.error('Error inicializando controlador de empleados:', err);
      }
    } else {
      // mostrar mensaje en consola y en la UI (no crítico)
      console.warn('No se encontró EmployeesController; la página personal quedará sin comportamiento dinámico.');
      const container = document.getElementById('personal-container');
      if (container && container.innerHTML.trim() === '') {
        container.innerHTML = '<p style="text-align:center; padding:20px; color:#666;">El módulo de personal no está disponible (controller no cargado).</p>';
      }
    }
  },

  leavePersonalPage() {
    // quitar botón flotante y ocultar modal si el controlador existe
    if (this.employeesController) {
      try {
        if (typeof this.employeesController.removeAddButton === 'function') this.employeesController.removeAddButton();
        // si el controlador tiene método para ocultar modal, podríamos llamarlo aquí. Si no, ocultamos por id.
      } catch (err) {
        console.error('Error al limpiar controlador de empleados:', err);
      }
    } else {
      // intentar limpiar cualquier elemento que el personal controller pudiera haber creado directamente
      const addBtn = document.getElementById('pc-add-personal-btn') || document.getElementById('add-personal-btn');
      if (addBtn) addBtn.remove();
      const formContainer = document.getElementById('pc-personal-form-container') || document.getElementById('personal-form-container');
      if (formContainer) formContainer.style.display = 'none';
    }
  }
};

// Inicialización global
document.addEventListener('DOMContentLoaded', () => Controller.init());

// -------------------------
// Sidebar Clientes (sin tocar)
// -------------------------
document.querySelectorAll('.sidebar-link').forEach(link => {
  link.addEventListener('click', async e => {
    e.preventDefault();
    const page = link.getAttribute('data-page');
    document.querySelectorAll('.page-section').forEach(sec => sec.classList.remove('active'));
    const section = document.getElementById(`${page}-page`);
    if (section) section.classList.add('active');

    // cargar clientes solo en su página
    if (page === 'clientes') {
      const container = document.getElementById('clientes-container');
      if (!container) return;
      container.innerHTML = '<p>Cargando...</p>';
      try {
        const res = await fetch('DashBoardAdmin/views/php/clientes.php');
        const html = await res.text();
        container.innerHTML = html;
      } catch (err) {
        container.innerHTML = '<p style="color:red;">Error al cargar clientes.</p>';
      }
    }

    // si navegamos a personal, delegamos a enterPersonalPage
    if (page === 'personal') {
      Controller.enterPersonalPage();
    } else {
      Controller.leavePersonalPage();
    }
  });
});
