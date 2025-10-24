<?php
// Cafe_Orderly/index.php

// Define la ruta raíz para referencias estables
define('ROOT_PATH', __DIR__);

// 1. Estabilizar la Sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Cargar Autoload y Permisos (mínimo esencial)
require_once ROOT_PATH . 'helpers\PermisosHelper.php';
// Asume que tu autoload ya está correctamente configurado o lo añades aquí.
// require_once ROOT_PATH . '/config/autoload.php'; 

// 3. Determinar el Controlador y la Acción
$controller = $_GET['controller'] ?? 'Menu'; 
$action = $_GET['action'] ?? 'index';

// 4. LÓGICA DE REDIRECCIÓN A DASHBOARD (Básico)
// Si está logueado y el ID_Rol NO es 4 (Cliente), lo forzamos al Dashboard.
if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] != 4) {
    // Si no ha pedido una acción específica o está en el menú de cliente,
    // lo enviamos a la acción principal del Dashboard.
    if (!isset($_GET['controller']) || $controller === 'Menu') {
        $controller = 'Dashboard';
        $action = 'index'; 
    }
}


// 5. Ejecutar el Controlador (Lógica de ejecución limpia)
$controllerClass = ucfirst($controller) . 'Controller';
$controllerFile = ROOT_PATH . '/controllers/' . $controllerClass . '.php';

if (is_file($controllerFile)) {
    // Aquí asumo que tu autoload ya cargó la clase.
    $ctrl = new $controllerClass();
    $ctrl->$action();
} else {
    // Fallback simple si el controlador no existe
    http_response_code(404);
    echo "Error 404.";
}