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

    // Acción POST: Iniciar sesión
    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 🔹 Validación Turnstile (Tu código original)
            $turnstile_response = $_POST['cf-turnstile-response'] ?? '';
            $secret_key = "0x4AAAAAAB0SGMQQdYhRnaPNeZVs97eWUUk"; 

            $response = file_get_contents(
                "https://challenges.cloudflare.com/turnstile/v0/siteverify",
                false,
                stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                        'content' => http_build_query([
                            'secret' => $secret_key,
                            'response' => $turnstile_response,
                            'remoteip' => $_SERVER['REMOTE_ADDR']
                        ])
                    ]
                ])
            );

            $result = json_decode($response, true);

            if (!$result['success']) {
                echo "<script>alert('No se pasó la verificación de seguridad.');</script>";
                return $this->login();
            }

            // 🔹 Continuar con autenticación normal
            $correo = $_POST['correo'];
            $contrasena = $_POST['contrasena'];

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerPorCorreo($correo);

            if ($usuario && password_verify($contrasena, $usuario['ContrasenaHash'])) {
                
                session_start();
                
                // 1. Guardar datos básicos
                $_SESSION['usuario'] = $usuario['Usuario'];
                $_SESSION['id_rol'] = $usuario['ID_Rol'];
                
                // 2. Cargar permisos (Necesario para control interno en vistas)
                $permisos = $usuarioModel->obtenerPermisosPorRol($usuario['ID_Rol']);
                $_SESSION['permisos'] = $permisos; 
                
                // 3. 🔑 LÓGICA DE REDIRECCIÓN CONDICIONAL POR ID_ROL
                // Redirigir a todos los roles que NO sean Cliente (ID 4) al Dashboard.
                if ($usuario['ID_Rol'] != 4) { 
                    header('Location: ?controller=Dashboard&action=index'); 
                } else {
                    // Rol Cliente (ID 4)
                    header('Location: ?controller=Home&action=index'); 
                }

                exit; 
            } else {
                echo "<script>alert('Correo o contraseña incorrectos');</script>";
                $this->login();
            }
        }
    }

    // Acción POST: Registrar nuevo usuario y cliente
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
