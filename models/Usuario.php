<?php
class Usuario {
    private $db;

    public function __construct() {
        require 'config/database.php';
        $this->db = $conn;
    }

    // 🔹 Buscar usuario por correo
    public function obtenerPorCorreo($correo) {
        $query = "SELECT * FROM Usuarios WHERE Email = :correo";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Registrar nuevo usuario
    public function registrarUsuario($usuario, $correo, $contrasena, $rol = 4) {
        $hash = password_hash($contrasena, PASSWORD_BCRYPT);

        $query = "INSERT INTO Usuarios (Usuario, ContrasenaHash, ID_Rol, Estado, Email)
                  VALUES (:usuario, :hash, :rol, 1, :correo)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':hash', $hash);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        return $this->db->lastInsertId();
    }
}
?>
