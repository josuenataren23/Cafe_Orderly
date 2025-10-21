<?php
require_once 'models/Menu.php';

class MenuController {
    public function menu() {
        $menuModel = new Menu();

        // Cargamos todas las categorías (para los botones)
        $categorias = $menuModel->obtenerCategorias();

        // Cargamos todos los menús inicialmente
        $menus = $menuModel->obtenerMenus();

        $css_file = './views/assets/css/menu.css'; // Tu hoja de estilos

        require_once 'views/layout/header.php';
        require_once 'views/Menu/index.php';
        require_once 'views/layout/footer.php';
    }

    // 🔹 Acción para responder a AJAX con los platillos filtrados
    public function filtrarPorCategoria() {
        if (isset($_POST['idCategoria'])) {
            $idCategoria = $_POST['idCategoria'] == 'todos' ? null : $_POST['idCategoria'];

            $menuModel = new Menu();
            $menus = $menuModel->obtenerMenus($idCategoria);

            // Retornar en formato JSON
            header('Content-Type: application/json');
            echo json_encode($menus);
        }
    }
}
?>
