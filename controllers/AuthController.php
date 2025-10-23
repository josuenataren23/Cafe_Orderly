<?php
require_once 'models/Usuario.php';
require_once 'models/Cliente.php';

class AuthController {

    public function login() {
        $css_file = './views/assets/css/login.css';
        require_once 'views/layout/header.php';
        require_once 'views/Auth/login.php';
        require_once 'views/layout/footer.php';
    }

    public function registrar() {
        $css_file = './views/assets/css/register.css';
        require_once 'views/layout/header.php';
        require_once 'views/Auth/register.php';
        require_once 'views/layout/footer.php';
    }

    //  Acción POST: Iniciar sesión
    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = $_POST['correo'];
            $contrasena = $_POST['contrasena'];

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerPorCorreo($correo);

            if ($usuario && password_verify($contrasena, $usuario['ContrasenaHash'])) {
                session_start();
                $_SESSION['usuario'] = $usuario['Usuario'];
                $_SESSION['rol'] = $usuario['ID_Rol'];
                header('Location: ?controller=Menu&action=menu');
            } else {
                echo "<script>alert('Correo o contraseña incorrectos');</script>";
                $this->login();
            }
        }
    }

    //  Acción POST: Registrar nuevo usuario y cliente
    public function guardarRegistro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $apellidos = $_POST['apellidos'];
            $usuario = $_POST['usuario'];
            $correo = $_POST['correo'];
            $contrasena = $_POST['contrasena'];

            $usuarioModel = new Usuario();
            $clienteModel = new Cliente();

            $idUsuario = $usuarioModel->registrarUsuario($usuario, $correo, $contrasena);
            $clienteModel->registrarCliente($nombre, $apellidos, $correo, $idUsuario);

            echo "<script>alert('Registro exitoso, ahora puedes iniciar sesión');</script>";
            header('Location: ?controller=Auth&action=login');
        }
    }

    // 🔹 Cerrar sesión
    public function logout() {
        session_start();
        session_destroy();
        header('Location: ?controller=Auth&action=login');
    }
}
?>
