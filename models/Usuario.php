<?php
class Usuario {
    private $db;
    private $tableName = 'Usuarios'; // Nombre confirmado de tu tabla

    public function __construct() {
        // Carga la conexión PDO desde el archivo de configuración
        require 'config/database.php'; 
        $this->db = $conn; 
    }

    /**
     * Busca usuario por correo. Retorna todos los datos.
     */
    public function obtenerPorCorreo($correo) {
        $query = "SELECT ID_Usuario, Usuario, ContrasenaHash, ID_Rol, Estado, Email, is_verified 
                  FROM {$this->tableName} WHERE Email = :correo";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // =========================================================
    // 🚀 NUEVO MÉTODO: REGISTRO CON GOOGLE 
    // =========================================================

    /**
     * Registra un nuevo usuario proveniente de Google.
     * La cuenta se crea como verificada y con contraseña aleatoria.
     * @param array $data Los datos del usuario (nombre, apellidos, correo, usuario).
     * @param Cliente $clienteModel Instancia del modelo Cliente.
     * @return array Los datos del usuario registrado.
     */
    public function registrarUsuarioGoogle(array $data, Cliente $clienteModel): array {
        $rol = 4;       // Rol de Cliente
        $estado = 1;    // Activo

        // Generar hash de contraseña aleatorio
        $contrasenaHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);

        // Campos opcionales que pueden ser NULL
        $verification_code = null;
        $code_expires_at = null;

        try {
            // Inserción en la tabla Usuarios
            $sql_user = "INSERT INTO {$this->tableName} 
                         (Usuario, ContrasenaHash, ID_Rol, Estado, Email, is_verified, verification_code, code_expires_at)
                         VALUES (:usuario, :contrasena, :rol, :estado, :correo, 1, :code, :expires)";
            
            $stmt = $this->db->prepare($sql_user);
            $stmt->bindParam(':usuario', $data['usuario']);
            $stmt->bindParam(':contrasena', $contrasenaHash);
            $stmt->bindParam(':rol', $rol);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':correo', $data['correo']);
            $stmt->bindParam(':code', $verification_code);
            $stmt->bindParam(':expires', $code_expires_at);
            $stmt->execute();

            $idUsuario = $this->db->lastInsertId();

            // Crear registro en la tabla Cliente
            $clienteModel->registrarCliente($data['nombre'], $data['apellidos'], $data['correo'], $idUsuario);

            return [
                'ID_Usuario' => $idUsuario,
                'Usuario' => $data['usuario'],
                'Email' => $data['correo'],
                'ID_Rol' => $rol
            ];

        } catch (\PDOException $e) {
            error_log("[registrarUsuarioGoogle] " . $e->getMessage());
            throw $e;
        }
    }

    // =========================================================
    // 🛑 MÉTODOS DE VERIFICACIÓN (MANUAL)
    // =========================================================

    public function cleanExpiredAccounts() {
        try {
            $this->db->exec("EXEC dbo.sp_CleanExpiredVerification;"); 
            return true;
        } catch (\PDOException $e) {
            error_log("DB Error al ejecutar SP de limpieza: " . $e->getMessage());
            return false;
        }
    }

    public function registrarUsuarioPendiente($data, $clienteModel) {
        $hashed_password = password_hash($data['contrasena'], PASSWORD_BCRYPT); 
        $expiration_time = date('Y-m-d H:i:s', strtotime('+3 minutes')); 
        $verification_code = strval(rand(100000, 999999));
        $rol = 4; 
        $estado = 1; 
        
        try {
            $sql_user = "INSERT INTO {$this->tableName} 
                         (Usuario, ContrasenaHash, ID_Rol, Estado, Email, is_verified, verification_code, code_expires_at) 
                         VALUES (:usuario, :contrasena, :rol, :estado, :correo, 0, :code, :expires)";
            
            $stmt = $this->db->prepare($sql_user);
            $stmt->bindParam(':usuario', $data['usuario']);
            $stmt->bindParam(':contrasena', $hashed_password);
            $stmt->bindParam(':rol', $rol);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':correo', $data['correo']);
            $stmt->bindParam(':code', $verification_code);
            $stmt->bindParam(':expires', $expiration_time);
            $stmt->execute();

            $idUsuario = $this->db->lastInsertId(); 
            $clienteModel->registrarCliente($data['nombre'], $data['apellidos'], $data['correo'], $idUsuario);

            return [
                'id' => $idUsuario, 
                'code' => $verification_code, 
                'expires' => strtotime($expiration_time)
            ];

        } catch (\PDOException $e) {
            throw $e; 
        }
    }

    public function verifyAndActivate($email, $code) {
        $stmt = $this->db->prepare("SELECT ID_Usuario, code_expires_at 
                                     FROM {$this->tableName} 
                                     WHERE Email = ? AND verification_code = ? AND is_verified = 0");
        $stmt->execute([$email, $code]);
        $user = $stmt->fetch();

        if ($user) {
            $current_time = new DateTime();
            $expiry_time = new DateTime($user['code_expires_at']);
            
            if ($current_time > $expiry_time) {
                $delete_stmt = $this->db->prepare("DELETE FROM {$this->tableName} WHERE ID_Usuario = ?");
                $delete_stmt->execute([$user['ID_Usuario']]);
                return ['status' => 'expired'];
            }
            
            $update_sql = "UPDATE {$this->tableName} 
                           SET is_verified = 1, verification_code = NULL, code_expires_at = NULL 
                           WHERE ID_Usuario = ?";
            $this->db->prepare($update_sql)->execute([$user['ID_Usuario']]);
            return ['status' => 'success'];

        } else {
            return ['status' => 'invalid'];
        }
    }

    public function eliminarUsuarioPendiente($idUsuario) {
        try {
            $delete_user_sql = "DELETE FROM {$this->tableName} WHERE ID_Usuario = ?";
            $this->db->prepare($delete_user_sql)->execute([$idUsuario]);
        } catch (\PDOException $e) {
            error_log("Fallo al revertir registro: " . $e->getMessage());
        }
    }

    // =========================================================
    // MÉTODOS EXISTENTES
    // =========================================================
    
    public function obtenerPermisosPorRol(int $id_rol): array {
        $permisos = [];
        $stmt = $this->db->prepare("{CALL SP_ObtenerFuncionalidadesPorRol(?)}");
        $stmt->bindParam(1, $id_rol, PDO::PARAM_INT);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $permisos[] = $row['Funcionalidad']; 
        }

        return $permisos;
    }
}
