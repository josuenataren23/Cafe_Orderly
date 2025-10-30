<?php
// index.php — Enrutador principal

require_once 'vendor/autoload.php';

// Obtiene el controlador y la acción desde la URL, con valores por defecto
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Construye el nombre de la clase del controlador
$controllerName = ucfirst($controller) . 'Controller';

// Ruta del archivo del controlador
$controllerFile = "controllers/{$controllerName}.php";

// Verifica si el archivo del controlador existe
if (!file_exists($controllerFile)) {
    die("Error: El controlador <b>{$controllerName}</b> no existe.");
}

// Carga el controlador
require_once $controllerFile;

// Verifica si la clase del controlador existe
if (!class_exists($controllerName)) {
    die("Error: La clase <b>{$controllerName}</b> no está definida.");
}

// Crea la instancia del controlador
$controllerObj = new $controllerName();

// Verifica si el método (acción) existe
if (!method_exists($controllerObj, $action)) {
    die("Error: La acción <b>{$action}</b> no existe en el controlador <b>{$controllerName}</b>.");
}

// Ejecuta la acción
$controllerObj->$action();
?>