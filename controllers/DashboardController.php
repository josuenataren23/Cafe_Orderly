<?php
class DashboardController {
    public function index() {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1️⃣ Verificar si el usuario NO está logueado
        if (!isset($_SESSION['id_rol'])) {
            header('Location: ?controller=Auth&action=login');
            exit;
        }

        // 2️⃣ Verificar si el usuario es Cliente (id_rol = 4)
        if ($_SESSION['id_rol'] == 4) {
            header('Location: ?controller=Home&action=index');
            exit;
        }

        // 3️⃣ Si pasa las validaciones, mostrar el dashboard
        require 'DashBoardAdmin/index.php';
    }
}
?>
