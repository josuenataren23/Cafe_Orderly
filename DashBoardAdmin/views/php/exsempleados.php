<?php

require 'db.php';

try {

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

    $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

    switch ($accion) {
        case 'agregar':
            agregarEmpleado($conn);
            break;
            
        case 'actualizar':
            actualizarEmpleado($conn);
            break;
            
        case 'eliminar':
            eliminarEmpleado($conn);
            break;
            
        case 'buscar':
            buscarEmpleados($conn);
            break;
            
        case 'obtener':
            obtenerEmpleado($conn);
            break;
            
        default:
            throw new Exception("Acción no válida");
    }

} catch(Exception $e) {
    responderError($e->getMessage());
}

// agregar un empleado
function agregarEmpleado($conn) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    // Validar y obtener datos del formulario

    $nombre = validarCampo($_POST['nombre'] ?? '', 'Nombre');
    $apellidos = validarCampo($_POST['apellidos'] ?? '', 'Apellidos');
    $id_puesto = validarCampo($_POST['id_puesto'] ?? '', 'ID_PuestoTrabajo');
    $salario = validarCampo($_POST['salario'] ?? '', 'Salario');
    $estado = validarCampo($_POST['estado'] ?? '', 'Estado_Empleado');
    $id_usuario = validarCampo($_POST['id_usuario'] ?? '', 'ID_Usuario');
    $telefono = validarCampo($_POST['telefono'] ?? '', 'Telefono');

    // Insertar en la base de datos
    $sql = "INSERT INTO Empleado (Nombre, Apellidos, ID_PuestoTrabajo, Salario, Estado_Empleado, ID_Usuario, Telefono) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nombre, $apellidos, $id_puesto, $salario, $estado, $id_usuario, $telefono]);

    responderExito("Empleado agregado correctamente", ["id" => $conn->lastInsertId()]);
}

// actualizar un empleado
function actualizarEmpleado($conn) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $id = validarCampo($_POST['empleado_id'] ?? '', 'ID');
    $nombre = validarCampo($_POST['nombre'] ?? '', 'Nombre');
    $apellidos = validarCampo($_POST['apellidos'] ?? '', 'Apellidos');
    $id_puesto = validarCampo($_POST['id_puesto'] ?? '', 'ID_PuestoTrabajo');
    $salario = validarCampo($_POST['salario'] ?? '', 'Salario');
    $estado = validarCampo($_POST['estado'] ?? '', 'Estado_Empleado');
    $id_usuario = validarCampo($_POST['id_usuario'] ?? '', 'ID_Usuario');
    $telefono = validarCampo($_POST['telefono'] ?? '', 'Telefono');

    $sql = "UPDATE Empleado SET Nombre = ?, Apellidos = ?, ID_PuestoTrabajo = ?, Salario = ?, Estado_Empleado = ?, ID_Usuario = ?, Telefono = ? WHERE ID_Empleado = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nombre, $apellidos, $id_puesto, $salario, $estado, $id_usuario, $telefono, $id]);

    responderExito("Empleado actualizado correctamente");
}

// eliminar un empleado
function eliminarEmpleado($conn) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $id = validarCampo($data['id'] ?? '', 'ID');

    $stmt = $conn->prepare("DELETE FROM Empleado WHERE ID_Empleado = ?");
    $stmt->execute([$id]);

    responderExito("Empleado eliminado correctamente");
}

// buscar empleados
function buscarEmpleados($conn) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("Método no permitido");
    }

    $busqueda = '%' . ($_GET['q'] ?? '') . '%';
    $sql = "SELECT ID_Empleado, Nombre, Apellidos, ID_PuestoTrabajo, Salario, Estado_Empleado, ID_Usuario, Telefono 
            FROM Empleado 
            WHERE Nombre LIKE ? OR Apellidos LIKE ?
            ORDER BY Nombre";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$busqueda, $busqueda]);
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    responderExito("Búsqueda exitosa", $empleados);
}

// obtener un empleado específico
function obtenerEmpleado($conn) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("Método no permitido");
    }

    $id = validarCampo($_GET['id'] ?? '', 'ID');
    $sql = "SELECT ID_Empleado, Nombre, Apellidos, ID_PuestoTrabajo, Salario, Estado_Empleado, ID_Usuario, Telefono 
            FROM Empleado 
            WHERE ID_Empleado = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empleado) {
        throw new Exception("Empleado no encontrado");
    }

    responderExito("Empleado encontrado", $empleado);
}

// Funciones de utilidad
function validarCampo($valor, $campo) {
    $valor = trim($valor);
    if (empty($valor)) {
        throw new Exception("El campo $campo es requerido");
    }
    return $valor;
}

function validarEmail($email) {
    $email = trim($email);
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email no válido");
    }
    return $email;
}

function responderExito($mensaje, $datos = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'mensaje' => $mensaje,
        'datos' => $datos
    ]);
    exit;
}

function responderError($mensaje) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'mensaje' => $mensaje
    ]);
    exit;
}
?>
