<?php
class Menu {
    private $db;

    public function __construct() {
        // Incluir tu archivo de conexión existente
        require 'config/database.php';
        $this->db = $conn; // Usa la conexión creada ahí
    }

    // 🔹 Obtener todos los menús o filtrados por categoría
    public function obtenerMenus($idCategoria = null) {
        if ($idCategoria) {
            $query = "SELECT ID_Menu, ID_Categoria, Nombre, Descripcion, Precio, ImagenURL
                      FROM Menus
                      WHERE ID_Categoria = :idCategoria";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idCategoria', $idCategoria, PDO::PARAM_INT);
        } else {
            $query = "SELECT ID_Menu, ID_Categoria, Nombre, Descripcion, Precio, ImagenURL
                      FROM Menus";
            $stmt = $this->db->prepare($query);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Obtener todas las categorías
    public function obtenerCategorias() {
        $query = "SELECT ID_Categoria, Nombre FROM Categorias";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
