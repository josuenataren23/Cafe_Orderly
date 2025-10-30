<?php
// DashBoardAdmin/views/php/roles.php
include '../../../config/database.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT ID_Rol, Nombre FROM Roles");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($roles);
} catch (Exception $e) {
    echo json_encode([]);
}
