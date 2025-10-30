<?php
require '../../../config/database.php';
header('Content-Type: application/json');

// ----------------------
// Detectar acción
// ----------------------
$action = $_POST['action'] ?? '';

if(!$action){
    echo json_encode(['success'=>false,'message'=>'Acción no especificada']);
    exit;
}

try {
    switch($action){

        case 'list':
            $stmt = $conn->query("SELECT m.ID_Menu, m.ID_Categoria, m.Nombre, m.Descripcion, m.Precio, m.ImagenURL, c.Nombre AS Categoria 
                                  FROM Menus m
                                  JOIN Categorias c ON m.ID_Categoria = c.ID_Categoria");
            $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Ajustar ruta de la imagen
            foreach($menus as &$m){
                if(!empty($m['ImagenURL'])){
                    $m['ImagenURL'] = 'DashBoardAdmin/views/img/productos/' . basename($m['ImagenURL']);
                }
            }

            echo json_encode(['success'=>true,'data'=>$menus]);
            break;

        case 'add':
            if(!isset($_POST['nombre'], $_POST['precio'], $_POST['categoria'])){
                throw new Exception('Campos incompletos');
            }

            $imagenPath = null;
            if(!empty($_FILES['imagen']) && $_FILES['imagen']['error']===0){
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $ext;
                $target = __DIR__ . '/../img/productos/' . $filename;

                if(move_uploaded_file($_FILES['imagen']['tmp_name'], $target)){
                    $imagenPath = $filename;
                }
            }

            $stmt = $conn->prepare("INSERT INTO Menus (ID_Categoria, Nombre, Descripcion, Precio, ImagenURL) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['categoria'], $_POST['nombre'], $_POST['descripcion'] ?? '', $_POST['precio'], $imagenPath]);

            echo json_encode(['success'=>true,'message'=>'Producto agregado correctamente']);
            break;

        case 'update':
            if(!isset($_POST['idMenu'], $_POST['nombre'], $_POST['precio'], $_POST['categoria'])){
                throw new Exception('Campos incompletos');
            }

            $imagenPath = $_POST['imagenActual'] ?? null;

            if(!empty($_FILES['imagen']) && $_FILES['imagen']['error']===0){
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $ext;
                $target = __DIR__ . '/../img/productos/' . $filename;

                if(move_uploaded_file($_FILES['imagen']['tmp_name'], $target)){
                    $imagenPath = $filename;
                }
            }

            $stmt = $conn->prepare("UPDATE Menus SET ID_Categoria=?, Nombre=?, Descripcion=?, Precio=?, ImagenURL=? WHERE ID_Menu=?");
            $stmt->execute([$_POST['categoria'], $_POST['nombre'], $_POST['descripcion'] ?? '', $_POST['precio'], $imagenPath, $_POST['idMenu']]);

            echo json_encode(['success'=>true,'message'=>'Producto actualizado correctamente']);
            break;

        case 'delete':
            if(!isset($_POST['idMenu'])) throw new Exception('ID no especificado');

            // eliminar imagen
            $stmt = $conn->prepare("SELECT ImagenURL FROM Menus WHERE ID_Menu=?");
            $stmt->execute([$_POST['idMenu']]);
            $img = $stmt->fetch(PDO::FETCH_ASSOC);
            if($img && !empty($img['ImagenURL'])){
                $file = __DIR__ . '/../img/productos/' . basename($img['ImagenURL']);
                if(file_exists($file)) unlink($file);
            }

            $stmt = $conn->prepare("DELETE FROM Menus WHERE ID_Menu=?");
            $stmt->execute([$_POST['idMenu']]);

            echo json_encode(['success'=>true,'message'=>'Producto eliminado']);
            break;

        default:
            throw new Exception('Acción no reconocida');
    }
} catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
