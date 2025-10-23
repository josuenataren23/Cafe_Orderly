<?php
class Cliente {
    private $db;

    public function __construct() {
        require 'config/database.php';
        $this->db = $conn;
    }

    public function registrarCliente($nombre, $apellidos, $correo, $idUsuario) {
        $query = "INSERT INTO Clientes (Nombre, Apellidos, Correo, ID_Usuario)
                  VALUES (:nombre, :apellidos, :correo, :idUsuario)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':idUsuario', $idUsuario);
        $stmt->execute();
    }
}
?>
