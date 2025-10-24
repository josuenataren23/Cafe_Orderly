<?php
require_once 'models/Reservar.php';
class ReservarController {
    private $modelo;

    public function __construct() {
        $this->modelo = new ReservaModel();
    }

    public function reservar()
    {
         $horarios = $this->modelo->obtenerHorarios();
        $css_file = '.\views\assets\css\reservar.css'; // Specify CSS file for reservar page
        // Load the reservar view
        require_once 'views/layout/header.php';
        require_once 'views/Reservas/index.php';
        require_once 'views/layout/footer.php';
    }


    public function mesasDisponibles() {
        $fecha = $_GET['fecha'];
        $idHorario = $_GET['horario'];
        $mesas = $this->modelo->obtenerMesasDisponibles($fecha, $idHorario);
        echo json_encode($mesas);
    }

    public function guardar() {
        $this->modelo->guardarReserva($_POST);
        header("Location: index.php?c=Reservas&a=index&msg=ok");
    }
}

?>

