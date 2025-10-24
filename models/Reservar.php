<?php
class ReservaModel {
    private $db;

    public function __construct() {
        // Carga la conexión PDO desde el archivo de configuración
        require 'config/database.php'; 
        $this->db = $conn; 
    }

    public function obtenerHorarios() {
        $sql = "SELECT ID_Horario, Hora FROM Horarios";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerMesasDisponibles($fecha, $idHorario) {
        $sql = "
            SELECT M.ID_Mesa, M.NumeroMesa, M.Capacidad
            FROM Mesas M
            INNER JOIN HorarioMesa HM ON M.ID_Mesa = HM.ID_Mesa
            WHERE HM.ID_Horario = ?
            AND M.ID_Mesa NOT IN (
                SELECT R.ID_HorarioMesa
                FROM Reservaciones R
                INNER JOIN HorarioMesa H2 ON R.ID_HorarioMesa = H2.ID_HorarioMesa
                WHERE R.FechaReserva = ? AND H2.ID_Horario = ?
            )
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idHorario, $fecha, $idHorario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardarReserva($data) {
        $sql = "INSERT INTO Reservaciones (ID_HorarioMesa, ID_Cliente, FechaReserva, Personas)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['mesa'], $_SESSION['id_cliente'], $data['fecha'], $data['personas']]);
    }
}

?>