<?php
class UserModel {
    private $pdo; 
    private $tableName = 'users'; // Asegúrate de que este sea el nombre correcto de tu tabla

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // 1. LIMPIEZA POR DEMANDA
    public function cleanExpiredAccounts() {
        try {
            // Ejecuta el procedimiento almacenado en SQL Server para la limpieza
            $this->pdo->exec("EXEC dbo.sp_CleanExpiredVerification;");
            return true;
        } catch (\PDOException $e) {
            error_log("SQL Server Error al ejecutar limpieza: " . $e->getMessage());
            return false;
        }
    }

    // 2. CREACIÓN DE USUARIO PENDIENTE
    public function createPendingUser($data) {
        $hashed_password = password_hash($data['contrasena'], PASSWORD_DEFAULT);
        
        // CÁLCULO DE LA EXPIRACIÓN: AHORA + 3 minutos
        $expiration_time = date('Y-m-d H:i:s', strtotime('+3 minutes'));
        $verification_code = strval(rand(100000, 999999));

        $sql = "INSERT INTO {$this->tableName} 
                (nombre, apellidos, correo, usuario, contrasena, is_verified, verification_code, code_expires_at) 
                VALUES (:nombre, :apellidos, :correo, :usuario, :contrasena, 0, :code, :expires)";
        
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':apellidos', $data['apellidos']);
        $stmt->bindParam(':correo', $data['correo']);
        $stmt->bindParam(':usuario', $data['usuario']);
        $stmt->bindParam(':contrasena', $hashed_password);
        $stmt->bindParam(':code', $verification_code);
        $stmt->bindParam(':expires', $expiration_time);

        $stmt->execute();
        
        return [
            'code' => $verification_code, 
            'expires' => strtotime($expiration_time)
        ];
    }
    
    // 3. VERIFICAR Y ACTIVAR CUENTA
    public function verifyAndActivate($email, $code) {
        // Busca al usuario no verificado
        $stmt = $this->pdo->prepare("SELECT id, code_expires_at FROM {$this->tableName} WHERE correo = ? AND verification_code = ? AND is_verified = 0");
        $stmt->execute([$email, $code]);
        $user = $stmt->fetch();

        if ($user) {
            $current_time = new DateTime();
            $expiry_time = new DateTime($user['code_expires_at']);
            
            if ($current_time > $expiry_time) {
                // El código expiró, eliminar el registro
                $delete_stmt = $this->pdo->prepare("DELETE FROM {$this->tableName} WHERE id = ?");
                $delete_stmt->execute([$user['id']]);
                return ['status' => 'expired'];
            }
            
            // Activar la cuenta
            $update_stmt = $this->pdo->prepare("UPDATE {$this->tableName} SET is_verified = 1, verification_code = NULL, code_expires_at = NULL WHERE id = ?");
            $update_stmt->execute([$user['id']]);
            return ['status' => 'success'];

        } else {
            return ['status' => 'invalid'];
        }
    }
}