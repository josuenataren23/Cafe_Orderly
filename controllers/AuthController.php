<?php
require_once 'models/Usuario.php';
require_once 'models/Cliente.php';
require_once 'smtp/MailerService.php'; 

class AuthController {

    private $mailerService;

    public function __construct() {
        // Inicializar el servicio de correo
        $this->mailerService = new MailerService();
    }
    
    // VISTAS EXISTENTES
    public function login() {
        $css_file = './views/assets/css/login.css';
        $message = $_GET['message'] ?? null;
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

    // ACCIÓN POST: Iniciar sesión (AUTENTICAR)
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
                echo "<script>alert('No se paso la verificacion de seguridad.');</script>";
                return $this->login();
            }

            // 🔹 Continuar con autenticacion normal
            $correo = $_POST['correo'];
            $contrasena = $_POST['contrasena'];

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerPorCorreo($correo); 

            if ($usuario && password_verify($contrasena, $usuario['ContrasenaHash'])) {
                
                // 🛑 VERIFICACIÓN AÑADIDA: Revisar si la cuenta está activa
                if (!isset($usuario['is_verified']) || $usuario['is_verified'] != 1) { 
                    echo "<script>alert('Error: Tu cuenta aun no ha sido verificada. Revisa tu correo o registrate de nuevo.');</script>";
                    return $this->login();
                }
                
                session_start();
                
                // 1. Guardar datos basicos
                $_SESSION['usuario'] = $usuario['Usuario'];
                $_SESSION['id_rol'] = $usuario['ID_Rol'];
                
                // 2. Cargar permisos
                $permisos = $usuarioModel->obtenerPermisosPorRol($usuario['ID_Rol']);
                $_SESSION['permisos'] = $permisos; 
                
                // 3. LÓGICA DE REDIRECCIÓN
                if ($usuario['ID_Rol'] != 4) { 
                    header('Location: ?controller=Dashboard&action=index'); 
                } else {
                    header('Location: ?controller=Home&action=index'); 
                }

                exit; 
            } else {
                echo "<script>alert('Correo o contraseña incorrectos');</script>";
                $this->login();
            }
        }
    }

    // ACCIÓN POST: Registrar nuevo usuario y cliente (GUARDAR REGISTRO)
    public function guardarRegistro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $data = [
                'nombre' => $_POST['nombre'],
                'apellidos' => $_POST['apellidos'],
                'usuario' => $_POST['usuario'],
                'correo' => $_POST['correo'],
                'contrasena' => $_POST['contrasena']
            ];

            $usuarioModel = new Usuario(); 
            $clienteModel = new Cliente();
            
            // 🛑 1. LIMPIEZA POR DEMANDA: Ejecuta el SP para eliminar registros expirados.
            $usuarioModel->cleanExpiredAccounts(); 

            try {
                // 2. Registrar usuario y cliente con estado PENDIENTE (is_verified=0)
                // Este método debe manejar ambas inserciones (usuario y cliente)
                $result = $usuarioModel->registrarUsuarioPendiente($data, $clienteModel);
                
                $idUsuario = $result['id'];
                $code = $result['code'];
                $expiration_timestamp = $result['expires'];
                $recipientName = $data['nombre'] . ' ' . $data['apellidos'];

                // 3. Enviar Correo
                if ($this->mailerService->sendVerificationEmail($data['correo'], $recipientName, $code)) {
    // Limpiar buffer (por si hubo algún echo previo)
    if (ob_get_length()) ob_end_clean();

    header("Location: ?controller=Auth&action=showVerificationPage&email=" . urlencode($data['correo']) . "&expires=" . $expiration_timestamp);
    exit;
} else {
                    // Si el correo falla, se revierte la inserción del usuario y cliente
                    $usuarioModel->eliminarUsuarioPendiente($idUsuario); 
                    echo "<script>alert('Registro fallido: No se pudo enviar el correo de verificación. Inténtalo de nuevo.');</script>";
                    return $this->registrar();
                }

            } catch (Exception $e) {
                // Manejo de errores de DB (duplicado de usuario/correo)
                echo "<script>alert('Error al registrar: " . $e->getMessage() . "');</script>";
                return $this->registrar();
            }
        }
    }

    // ACCIÓN NUEVA: MOSTRAR PÁGINA DE VERIFICACIÓN
    public function showVerificationPage() {
        $css_file = './views/assets/css/verification.css'; 
        $email = $_GET['email'] ?? '';
        $expires = $_GET['expires'] ?? time() + 180;
        
        // Cálculo del tiempo restante para JS (Persistencia)
        $time_start_js = $expires - time();
        if ($time_start_js < 0) {
            $time_start_js = 0;
        }
        
        
        require_once 'views/Auth/verification.php'; // Usa la nueva vista
        require_once 'views/layout/footer.php';
    }
    
    // ACCIÓN NUEVA: VERIFICAR CÓDIGO (POST desde verification.php)
    public function verifyCode() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /"); 
            exit();
        }
        
        $email = $_POST['email'] ?? '';
        $code = $_POST['code'] ?? '';
        
        if (empty($email) || empty($code) || strlen($code) != 6) {
             die("Datos invalidos."); 
        }

        $usuarioModel = new Usuario();
        $result = $usuarioModel->verifyAndActivate($email, $code); 

        if ($result['status'] === 'success') {
            echo "<script>alert('¡Cuenta verificada con éxito! Ya puedes iniciar sesion.');</script>";
            header("Location: ?controller=Auth&action=login&message=verified");
        } elseif ($result['status'] === 'expired') {
            echo "<script>alert('El código ha expirado (limite de 3 minutos). Por favor, registrate de nuevo.');</script>";
            return $this->registrar(); // Redirige al formulario de registro
        } else {
            echo "<script>alert('Codigo de verificacion incorrecto. Por favor, intenta de nuevo.');</script>";
            // Redirige a la verificación, manteniendo la persistencia del contador (tiempo)
            header("Location: ?controller=Auth&action=showVerificationPage&email=" . urlencode($email) . "&expires=" . time()); 
        }
    }

    // Cerrar sesión
    public function logout() {
        session_start();
        session_destroy();
        header('Location: ?controller=Auth&action=login');
    }

    public function googleAuth() {
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        return;
    }

    // Recibir id_token
    $idtoken = $_POST['idtoken'] ?? '';
    if (empty($idtoken)) {
        echo json_encode(['success' => false, 'message' => 'No se recibió id_token']);
        return;
    }

    // 🔹 Verificar token con Google y depuración
    $tokeninfo = @file_get_contents('https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idtoken));
    if ($tokeninfo === false) {
        $error = error_get_last(); // Captura el error real de PHP
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo verificar el token con Google',
            'php_error' => $error
        ]);
        return;
    }

    $info = json_decode($tokeninfo, true);
    if (!$info || !isset($info['aud'])) {
        echo json_encode(['success' => false, 'message' => 'Respuesta inválida de Google', 'raw' => $tokeninfo]);
        return;
    }

    // Verificar que el token sea para nuestro client_id
    $expected_aud = '400097942545-kptqbpot1akcv7kgd4een3e8m24q3d06.apps.googleusercontent.com';
    if ($info['aud'] !== $expected_aud) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'El token no pertenece a esta aplicación (aud mismatch)',
            'aud_recibido' => $info['aud']
        ]);
        return;
    }

    // Verificar email
    $email = $info['email'] ?? '';
    $email_verified = $info['email_verified'] ?? false;
    if (empty($email) || !$email_verified) {
        echo json_encode(['success' => false, 'message' => 'Correo no verificado por Google']);
        return;
    }

    $given_name = $info['given_name'] ?? '';
    $family_name = $info['family_name'] ?? '';

    try {
        $usuarioModel = new Usuario();
        $clienteModel = new Cliente();

        $existing = $usuarioModel->obtenerPorCorreo($email);
        if ($existing) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['usuario'] = $existing['Usuario'];
            $_SESSION['id_rol'] = $existing['ID_Rol'];
            $_SESSION['permisos'] = $usuarioModel->obtenerPermisosPorRol($existing['ID_Rol']);

            $redirect = ($existing['ID_Rol'] != 4) ? '?controller=Dashboard&action=index' : '?controller=Home&action=index';
            echo json_encode(['success' => true, 'redirect' => $redirect]);
            return;
        }

        // Registrar nuevo usuario con datos de Google
        $username = explode('@', $email)[0] ?? $email;
        $data = [
            'nombre' => $given_name,
            'apellidos' => $family_name,
            'usuario' => $username,
            'correo' => $email
        ];

        $newUser = $usuarioModel->registrarUsuarioGoogle($data, $clienteModel);

        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['usuario'] = $newUser['Usuario'];
        $_SESSION['id_rol'] = $newUser['ID_Rol'];
        $_SESSION['permisos'] = $usuarioModel->obtenerPermisosPorRol($newUser['ID_Rol']);

        $redirect = ($newUser['ID_Rol'] != 4) ? '?controller=Dashboard&action=index' : '?controller=Home&action=index';
        echo json_encode(['success' => true, 'redirect' => $redirect]);
        return;

    } catch (Exception $e) {
        // 🔹 Captura el error real en logs y también en JSON
        error_log('[Auth::googleAuth] ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error interno al procesar autenticación con Google',
            'exception' => $e->getMessage()
        ]);
        return;
    }
}

}
?>
