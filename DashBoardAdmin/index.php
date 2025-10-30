<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Cafe Orderly</title>
    <link rel="stylesheet" href="DashBoardAdmin\views\css\dashboardStyle.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    
</head>
<body class="text-gray-800">

    <div id="app-container" class="app-layout"> 
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h1 style="font-size: 1.5em; color: #3b82f6; font-weight: bold;">Cafe Orderly</h1>
            </div>
            <nav id="sidebar-nav">
                <a href="#" class="sidebar-link active" data-page="dashboard"><span>📈</span><span class="ml-3">Dashboard</span></a>
                <a href="#" class="sidebar-link" data-page="ordenes" data-role="Administrador Mesero Cocina"><span>🧾</span><span class="ml-3">Órdenes</span></a>
                <a href="#" class="sidebar-link" data-page="reservaciones" data-role="Administrador Recepcion"><span>🗓️</span><span class="ml-3">Reservaciones</span></a>
                <a href="#" class="sidebar-link" data-page="mesas" data-role="Administrador Mesero Recepcion"><span>🪑</span><span class="ml-3">Mesas</span></a>
                <a href="#" class="sidebar-link" data-page="menu" data-role="Administrador"><span>📖</span><span class="ml-3">Menú</span></a>
                <a href="#" class="sidebar-link" data-page="clientes" data-role="Administrador"><span>👥</span><span class="ml-3">Clientes</span></a>
                <a href="#" class="sidebar-link" data-page="personal" data-role="Administrador"><span>🧑‍💼</span><span class="ml-3">Personal</span></a>
                <a href="#" class="sidebar-link" data-page="reportes" data-role="Administrador"><span>📊</span><span class="ml-3">Reportes</span></a>
                <a href="#" class="sidebar-link" data-page="configuracion" data-role="Administrador"><span>⚙️</span><span class="ml-3">Configuración</span></a>
            </nav>
        </aside>

        <div class="main-content">
            <header class="header">
                <button id="menu-toggle" style="display:none;">☰</button>
                <div class="header-actions">
                    <span style="font-size: 0.9em; font-weight: 500;">Rol:</span>
                    <select id="role-switcher" style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.9em;">
                        <option value="Administrador">Administrador</option>
                        <option value="Mesero">Mesero</option>
                        <option value="Recepcion">Recepción</option>
                        <option value="Cocina">Cocina</option>
                    </select>
                    <a href="?controller=Auth&action=logout" id="logout-button" style="color: #dc2626; font-weight: bold; background: none; border: none; cursor: pointer;">Cerrar Sesión</a>
                </div>
            </header>

            <main style="padding-top: 32px; ">
                
                <section id="dashboard-page" class="page-section active">
                    <h1 style="font-size: 2em; font-weight: bold; margin-bottom: 20px;">Dashboard</h1>
                    <div class="stat-cards-grid">
                        <div class="stat-card">
                            <h3 style="color: #6b7280; font-weight: 500;">Reservaciones para Hoy</h3>
                            <p>12</p>
                        </div>
                        <div class="stat-card">
                            <h3 style="color: #6b7280; font-weight: 500;">Órdenes Activas</h3>
                            <p>8</p>
                        </div>
                        <div class="stat-card">
                            <h3 style="color: #6b7280; font-weight: 500;">Ingresos del Día</h3>
                            <p>$3,450.00</p>
                        </div>
                    </div>
                    <div class="charts-grid">
                        <div class="stat-card">
                            <h3 style="font-weight: 600; margin-bottom: 15px;">Ventas de la Semana</h3>
                            <div class="chart-container" style="position: relative; height:300px; width:100%">
                                <canvas id="weekly-sales-chart" style="background-color: #eee;"></canvas>
                            </div>
                        </div>
                        <div class="stat-card">
                            <h3 style="font-weight: 600; margin-bottom: 15px;">Productos Más Vendidos</h3>
                            <div class="chart-container" style="position: relative; height:300px; width:100%">
                                <canvas id="top-products-chart" style="background-color: #eee;"></canvas>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="ordenes-page" class="page-section">
                    <h1 style="font-size: 2em; font-weight: bold; margin-bottom: 20px;">Gestión de Órdenes</h1>
                    <div class="kanban-column">
                        <div>
                            <h3 style="font-weight: 600; font-size: 1.2em; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px;">Pendiente (4)</h3>
                            <div class="order-card" style="background-color: #f5f5f5;">
                                <p style="font-weight: bold; margin: 0;">Mesa 5 - Orden #102</p>
                                <p style="font-size: 0.9em; margin: 5px 0;">- 2x Chilaquiles Rojos</p>
                                <p style="font-size: 0.9em; margin: 5px 0;">- 1x Jugo de Naranja</p>
                            </div>
                            <div class="order-card" style="background-color: #f5f5f5;">
                                <p style="font-weight: bold; margin: 0;">Mesa 2 - Orden #103</p>
                                <p style="font-size: 0.9em; margin: 5px 0;">- 1x Club Sándwich</p>
                            </div>
                        </div>
                        <div>
                            <h3 style="font-weight: 600; font-size: 1.2em; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px;">En Preparación (2)</h3>
                            <div class="order-card" style="background-color: #fffbe6;">
                                <p style="font-weight: bold; margin: 0;">Mesa 8 - Orden #101</p>
                                <p style="font-size: 0.9em; margin: 5px 0;">- 1x Enchiladas Suizas</p>
                                <p style="font-size: 0.9em; margin: 5px 0;">- 1x Latte</p>
                            </div>
                        </div>
                        <div>
                            <h3 style="font-weight: 600; font-size: 1.2em; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px;">Listo para Servir (1)</h3>
                            <div class="order-card" style="background-color: #dcfce7;">
                                <p style="font-weight: bold; margin: 0;">Mesa 3 - Orden #100</p>
                                <p style="font-size: 0.9em; margin: 5px 0;">- 2x Tostada de Aguacate</p>
                            </div>
                        </div>
                    </div>
                </section>
                
                <section id="reservaciones-page" class="page-section">
                    <h1 style="font-size: 2em; font-weight: bold; margin-bottom: 20px;">Gestión de Reservaciones</h1>
                    <table style="width: 100%;">
                        <thead><tr><th>Cliente</th><th>Fecha y Hora</th><th>Personas</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <tr><td>Carlos Sánchez</td><td>01 Oct 2025, 8:00 PM</td><td>4</td><td><button style="color: #10b981;">Confirmar</button></td></tr>
                            <tr><td>Ana García</td><td>01 Oct 2025, 8:30 PM</td><td>2</td><td><button style="color: #dc2626;">Cancelar</button></td></tr>
                        </tbody>
                    </table>
                </section>
                
                <section id="mesas-page" class="page-section">
                    <h1 style="font-size: 2em; font-weight: bold; margin-bottom: 20px;">Estado de Mesas</h1>
                    <div class="tables-grid" id="tables-grid">
                    </div>
                </section>

<section id="menu-page" class="page-section">
    <h1 style="font-size: 2em; font-weight: bold; margin-bottom: 20px;">Menú</h1>
    
    <div style="margin-bottom:20px; display:flex; gap:10px;">
        <button id="add-drink-btn" style="background:#3b82f6;color:white;padding:8px 16px;border:none;border-radius:6px;cursor:pointer;">Agregar Bebida</button>
        <button id="add-main-btn" style="background:#10b981;color:white;padding:8px 16px;border:none;border-radius:6px;cursor:pointer;">Agregar Plato Fuerte</button>
        <button id="add-dessert-btn" style="background:#f59e0b;color:white;padding:8px 16px;border:none;border-radius:6px;cursor:pointer;">Agregar Postre</button>
    </div>

    <div id="menu-container" style="display:flex; flex-wrap:wrap; gap:15px;"></div>
</section>


                
                
<section id="clientes-page" class="page-section">
    <h1 style="font-size: 2em; font-weight: bold; margin-bottom: 20px;">Clientes</h1>
    <div id="clientes-container"></div>
</section>


<section id="personal-page" class="page-section">
    <h1 style="font-size: 2em; font-weight: bold; margin-bottom: 20px;">Personal</h1>
    <div id="personal-container"></div>
</section>


                <section id="reportes-page" class="page-section">
                    <h1 style="font-size: 2em; font-weight: bold; margin-bottom: 20px;">Reportes</h1>
                    <div class="stat-card">
                        <h3 style="font-weight: 600; margin-bottom: 15px;">Reporte de Ventas Mensuales</h3>
                        <div class="chart-container" style="position: relative; height:400px; width:100%">
                            <canvas id="monthly-report-chart" style="background-color: #eee;"></canvas>
                        </div>
                    </div>
                </section>

                <section id="configuracion-page" class="page-section">
                    <h1 style="font-size: 2em; font-weight: bold; margin-bottom: 20px;">Configuración</h1>
                </section>
                
            </main>
        </div>
    </div>
    
    <div class="modal-overlay" id="table-status-modal">
        <div class="modal-content">
            <h2 style="font-size: 1.5em; font-weight: bold; margin-bottom: 15px;" id="modal-table-title">Mesa 1</h2>
            <label for="table-status-select" style="display: block; margin-bottom: 8px; font-weight: 500;">Cambiar estado:</label>
            <select id="table-status-select" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <option value="free">Libre</option>
                <option value="occupied">Ocupada</option>
                <option value="reserved">Reservada</option>
            </select>
            <div class="modal-actions">
                <button id="modal-cancel-btn" class="modal-button">Cancelar</button>
                <button id="modal-save-btn" class="modal-button">Guardar</button>
            </div>
        </div>
    </div>

    <script type="module" src="DashBoardAdmin/views/js/Controller.js"></script>
    <script type="module" src="DashBoardAdmin/views/js/PersonalController.js"></script>
    <script type="module" src="DashBoardAdmin/views/js/MenuController.js"></script>

</body>
</html>