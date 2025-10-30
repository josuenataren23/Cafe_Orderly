// js/Model.js

export const Model = {
    appState: {
        loggedIn: true, 
        currentPage: 'dashboard',
        currentUserRole: 'Administrador',
    },

    tablesData: [
        { id: 1, status: 'occupied' }, { id: 2, status: 'free' }, { id: 3, status: 'reserved' },
        { id: 4, status: 'free' }, { id: 5, status: 'free' }, { id: 6, status: 'occupied' },
        { id: 7, status: 'occupied' }, { id: 8, status: 'free' }, { id: 9, status: 'reserved' },
        { id: 10, status: 'free' }, { id: 11, status: 'free' }, { id: 12, status: 'free' },
    ],
    
    login(username, password) {
        return true; 
    },

    logout() {
        window.location.reload(); 
    },

    setCurrentPage(pageId) {
        this.appState.currentPage = pageId;
    },

    setCurrentRole(role) {
        this.appState.currentUserRole = role;
    },

    updateTableStatus(tableId, newStatus) {
        const table = this.tablesData.find(t => t.id === tableId);
        if (table) {
            table.status = newStatus;
        }
    },
};