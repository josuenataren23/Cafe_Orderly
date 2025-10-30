<?php
// -------------------------
// Configurar PHP para mostrar errores
// -------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// -------------------------
// Conexión a base de datos
// -------------------------
require '../../../config/database.php'; // Ajusta ruta si es necesario

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Acción no reconocida'];

try {
    // -------------------------
    // Detectar método POST
    // -------------------------
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // -------------------------
    // Leer datos
    // -------------------------
    $input = $_POST;
    $action = $input['action'] ?? '';

    // -------------------------
    // Rutas de imágenes
    // -------------------------
    $uploadDir = __DIR__ . '/img/productos/'; // Carpeta física
    $baseURL = 'DashBoardAdmin/views/img/productos/'; // URL para guardar en DB

    // -------------------------
    // CRUD
    // -------------------------
    switch ($action) {

        case 'add':
            // campos requeridos
            $nombre = $input['nombre'] ?? '';
            $descripcion = $input['descripcion'] ?? '';
            $precio = $input['precio'] ?? 0;
            $categoria = $input['categoria'] ?? 1;

            if (!$nombre || !$precio) throw new Exception('Nombre y precio son obligatorios');

            // Manejar imagen
            $imagenURL = '';
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['imagen']['tmp_name'];
                $filename = time() . '_' . basename($_FILES['imagen']['name']);
                $dest = $uploadDir . $filename;

                if (!move_uploaded_file($tmpName, $dest)) {
                    throw new Exception('No se pudo guardar la imagen');
                }
                $imagenURL = $baseURL . $filename;
            }

            // Insertar en BD
            $stmt = $conn->prepare("INSERT INTO Menus (ID_Categoria, Nombre, Descripcion, Precio, ImagenURL) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$categoria, $nombre, $descripcion, $precio, $imagenURL]);

            $response = ['success' => true, 'message' => 'Producto agregado correctamente'];
            break;

        case 'update':
            $id = $input['id'] ?? 0;
            if (!$id) throw new Exception('ID no especificado');

            $nombre = $input['nombre'] ?? '';
            $descripcion = $input['descripcion'] ?? '';
            $precio = $input['precio'] ?? 0;
            $categoria = $input['categoria'] ?? 1;

            // Manejar imagen
            $imagenURL = $input['imagen_actual'] ?? ''; // mantener la actual
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['imagen']['tmp_name'];
                $filename = time() . '_' . basename($_FILES['imagen']['name']);
                $dest = $uploadDir . $filename;

                if (!move_uploaded_file($tmpName, $dest)) {
                    throw new Exception('No se pudo guardar la imagen');
                }
                $imagenURL = $baseURL . $filename;
            }

            // Actualizar BD
            $stmt = $conn->prepare("UPDATE Menus SET ID_Categoria=?, Nombre=?, Descripcion=?, Precio=?, ImagenURL=? WHERE ID_Menu=?");
            $stmt->execute([$categoria, $nombre, $descripcion, $precio, $imagenURL, $id]);

            $response = ['success' => true, 'message' => 'Producto actualizado correctamente'];
            break;

        case 'delete':
            $id = $input['id'] ?? 0;
            if (!$id) throw new Exception('ID no especificado');

            // eliminar imagen física
            $stmt = $conn->prepare("SELECT ImagenURL FROM Menus WHERE ID_Menu=?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && $row['ImagenURL']) {
                $imgPath = __DIR__ . '/' . $row['ImagenURL'];
                if (file_exists($imgPath)) unlink($imgPath);
            }

            // eliminar BD
            $stmt = $conn->prepare("DELETE FROM Menus WHERE ID_Menu=?");
            $stmt->execute([$id]);

            $response = ['success' => true, 'message' => 'Producto eliminado correctamente'];
            break;

        case 'get':
            // obtener todos los productos
            $stmt = $conn->query("SELECT * FROM Menus");
            $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = ['success' => true, 'menus' => $menus];
            break;

        default:
            throw new Exception('Acción no reconocida');
    }

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
