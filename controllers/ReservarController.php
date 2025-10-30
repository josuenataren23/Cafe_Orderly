<?php
// 1. REVISIÓN DE ARCHIVO:
// Basado en tu constructor, el archivo que contiene la clase "ReservaModel"
// debería llamarse "ReservaModel.php" para seguir un estándar.
// Si tu archivo se llama "Reservar.php" pero la clase es "ReservaModel",
// cámbialo a "ReservaModel.php".
require_once 'models/Reservar.php'; // <-- Asumiendo que el archivo se llama como la clase

class ReservarController {
    private $modelo;

    public function __construct() {
        $this->modelo = new ReservaModel();
    }

    // Página principal del formulario (Esta función estaba bien)
    public function reservar() {
        $horarios = $this->modelo->obtenerHorarios();
        $css_file = './views/assets/css/reservar.css';
        require_once 'views/layout/header.php';
        require_once 'views/Reservas/index.php';
        require_once 'views/layout/footer.php';
    }

    // -----------------------------------------------------------------
    // Obtener mesas disponibles vía AJAX (AQUÍ ESTÁ LA CORRECCIÓN)
    // -----------------------------------------------------------------
    public function obtenerMesas()
    {
        header('Content-Type: application/json');

        if (!isset($_POST['fecha']) || !isset($_POST['idHorario'])) {
            echo json_encode(['error' => 'Datos incompletos']);
            return;
        }

        $fecha = $_POST['fecha'];
        $idHorario = $_POST['idHorario'];

        // 2. ERROR CORREGIDO:
        // No necesitas el 'require_once' aquí (ya está arriba).
        // Y no crees 'new Reservar()'.
        // Simplemente usa el modelo que ya existe en el constructor: $this->modelo

        try {
            // Usa la instancia existente
            $mesas = $this->modelo->obtenerMesasDisponibles($fecha, $idHorario);
            
            // 3. MEJORA DE JSON:
            // Tu JavaScript solo busca 'mesas' o 'error'.
            // Enviar 'success: true' no es necesario.
            echo json_encode(['mesas' => $mesas]);

        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Guardar la reserva (Esta función estaba bien)
    public function guardar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // 1. Aseguramos que el usuario esté logueado
        if (!isset($_SESSION['usuario']['id'])) { // <-- Ajusta esto a tu variable de sesión
            header("Location: index.php?c=Usuario&a=login");
            return;
        }

        // 2. Obtenemos todos los datos del formulario
        $id_mesa = $_POST['mesa'];
        $id_horario = $_POST['idHorario']; // <-- ¡Este faltaba!
        $fecha_reserva = $_POST['fecha'];
        $personas = $_POST['personas'];
        $id_cliente = $_SESSION['usuario']['id']; // <-- El ID del usuario logueado

        try {
            // 3. ¡PASO CLAVE!
            // Buscamos el ID_HorarioMesa usando los IDs que nos dio el formulario.
            // Necesitarás crear esta función en tu ReservaModel.
            $id_horario_mesa = $this->modelo->obtenerIdHorarioMesa($id_mesa, $id_horario);

            if (!$id_horario_mesa) {
                // No se encontró una combinación, es un error
                throw new Exception("Combinación de mesa y horario no válida.");
            }

            // 4. Creamos el array de datos para la BD
            // ¡Esto ahora coincide con tu tabla!
            $data = [
                'id_horario_mesa' => $id_horario_mesa,
                'id_cliente' => $id_cliente,
                'fecha_reserva' => $fecha_reserva,
                'personas' => $personas
                // 'id_transaccion' es probable que sea NULL o tenga un default
            ];

            // 5. Guardamos la reserva
            $resultado = $this->modelo->guardarReserva($data);

            if ($resultado) {
                header("Location: index.php?c=Reservar&a=reservar&msg=ok");
            } else {
                header("Location: index.php?c=Reservar&a=reservar&msg=error_guardar");
            }

        } catch (Exception $e) {
            // Puedes ser más específico con los mensajes de error
            header("Location: index.php?c=Reservar&a=reservar&msg=error_exception");
            }
        }
    }
}
?>