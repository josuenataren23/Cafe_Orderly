<?php
include '../../../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

try {
    switch ($data['action']) {

        // ----------------------
        // Crear usuario + empleado
        // ----------------------
        case 'add_full':
            $conn->beginTransaction();

            // Hashear contraseña
            $hash = password_hash($data['contrasena'], PASSWORD_DEFAULT);

            // Crear usuario
            $stmtUser = $conn->prepare("
                INSERT INTO Usuarios (Usuario, ContrasenaHash, ID_Rol, Estado, Email, is_verified, verification_code, code_expires_at)
                VALUES (?, ?, ?, ?, ?, 1, NULL, NULL)
            ");
            $stmtUser->execute([
                $data['usuario'],
                $hash,
                $data['rol'],
                1, // activo por defecto
                $data['email']
            ]);
            $idUsuario = $conn->lastInsertId();

            // Crear empleado
            $stmtEmp = $conn->prepare("
                INSERT INTO Empleado (Nombre, Apellidos, Salario, Estado_Empleado, ID_Usuario, Telefono)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmtEmp->execute([
                $data['nombre'],
                $data['apellidos'],
                $data['salario'],
                $data['estadoEmpleado'],
                $idUsuario,
                $data['telefono']
            ]);

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Empleado creado correctamente']);
            break;

        // ----------------------
        // Actualización parcial empleado y opcional usuario
        // ----------------------
        case 'update_partial':
            // Actualizar Empleado
            $stmtEmp = $conn->prepare("
                UPDATE Empleado SET Salario=?, Telefono=?, Estado_Empleado=?
                WHERE ID_Empleado=?
            ");
            $stmtEmp->execute([
                $data['salario'],
                $data['telefono'],
                $data['estadoEmpleado'],
                $data['idEmpleado']
            ]);

            // Actualizar Usuario si vienen datos
            if (!empty($data['rol']) || !empty($data['email']) || !empty($data['contrasena'])) {
                // Obtener ID_Usuario
                $stmt = $conn->prepare("SELECT ID_Usuario FROM Empleado WHERE ID_Empleado=?");
                $stmt->execute([$data['idEmpleado']]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($usuario) {
                    $fields = [];
                    $values = [];

                    if (!empty($data['rol'])) { $fields[] = "ID_Rol=?"; $values[] = $data['rol']; }
                    if (!empty($data['email'])) { $fields[] = "Email=?"; $values[] = $data['email']; }
                    if (!empty($data['contrasena'])) {
                        $fields[] = "ContrasenaHash=?";
                        $values[] = password_hash($data['contrasena'], PASSWORD_DEFAULT);
                    }

                    if (!empty($fields)) {
                        $values[] = $usuario['ID_Usuario'];
                        $sql = "UPDATE Usuarios SET ".implode(',', $fields)." WHERE ID_Usuario=?";
                        $stmtUpdate = $conn->prepare($sql);
                        $stmtUpdate->execute($values);
                    }
                }
            }

            echo json_encode(['success' => true, 'message' => 'Empleado actualizado correctamente']);
            break;

        // ----------------------
        // Eliminar empleado + usuario
        // ----------------------
        case 'delete':
            $stmt = $conn->prepare("SELECT ID_Usuario FROM Empleado WHERE ID_Empleado=?");
            $stmt->execute([$data['idEmpleado']]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                $conn->beginTransaction();

                $stmtEmp = $conn->prepare("DELETE FROM Empleado WHERE ID_Empleado=?");
                $stmtEmp->execute([$data['idEmpleado']]);

                $stmtUser = $conn->prepare("DELETE FROM Usuarios WHERE ID_Usuario=?");
                $stmtUser->execute([$usuario['ID_Usuario']]);

                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Empleado y usuario eliminados correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Empleado no encontrado']);
            }
            break;

        // ----------------------
        // Obtener info de un empleado
        // ----------------------
        case 'get':
            $stmt = $conn->prepare("
                SELECT u.Usuario, u.ID_Rol, u.Email
                FROM Empleado e
                JOIN Usuarios u ON u.ID_Usuario = e.ID_Usuario
                WHERE e.ID_Empleado=?
            ");
            $stmt->execute([$data['idEmpleado']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                echo json_encode([
                    'success' => true,
                    'usuario' => $row['Usuario'],
                    'rol' => $row['ID_Rol'],
                    'email' => $row['Email']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Empleado no encontrado']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
            break;
    }

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: '.$e->getMessage()]);
}
