<?php
class Usuario {
    private $db;

    public function __construct() {
        // Carga la conexión PDO desde el archivo de configuración
        require 'config/database.php'; 
        $this->db = $conn; 
    }

    /**
     * Busca usuario por correo. Retorna todos los datos (incluyendo ID_Rol).
     */
    public function obtenerPorCorreo($correo) {
        $query = "SELECT ID_Usuario, Usuario, ContrasenaHash, ID_Rol, Estado, Email FROM Usuarios WHERE Email = :correo";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Llama al SP y retorna un array de nombres de funcionalidades.
     */
    public function obtenerPermisosPorRol(int $id_rol): array {
        $permisos = [];
        
        // Llama al procedimiento almacenado de SQL Server
        $stmt = $this->db->prepare("{CALL SP_ObtenerFuncionalidadesPorRol(?)}");
        $stmt->bindParam(1, $id_rol, PDO::PARAM_INT);
        $stmt->execute();
        
        // Llena el array con los nombres de las funcionalidades
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $permisos[] = $row['Funcionalidad']; 
        }

        return $permisos;
    }

    /**
     * Registra un nuevo usuario con rol por defecto (4).
     */
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