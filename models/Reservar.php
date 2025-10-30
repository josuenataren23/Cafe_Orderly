<?php
class ReservaModel {
    private $db;

    public function __construct() {
        // Carga la conexión PDO desde config/database.php
        require 'config/database.php';
        $this->db = $conn;
    }

    // 🔹 Obtener todos los horarios disponibles
    public function obtenerHorarios() {
        $sql = "SELECT ID_Horario, Hora FROM Horarios";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Obtener mesas disponibles para un horario y fecha específica
    // (Esta función estaba perfecta)
    public function obtenerMesasDisponibles($fecha, $idHorario)
    {
        $sql = "SELECT M.ID_Mesa, M.NumeroMesa, M.Capacidad
                FROM Mesas M
                INNER JOIN HorarioMesa HM ON M.ID_Mesa = HM.ID_Mesa
                WHERE HM.ID_Horario = ?
                AND M.ID_Mesa NOT IN (
                    SELECT H2.ID_Mesa
                    FROM Reservaciones R
                    INNER JOIN HorarioMesa H2 ON R.ID_HorarioMesa = H2.ID_HorarioMesa
                    WHERE R.FechaReserva = ? AND H2.ID_Horario = ?
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idHorario, $fecha, $idHorario]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -----------------------------------------------------------------
    // 🔹 FUNCIÓN AÑADIDA (El controlador la necesita)
    // -----------------------------------------------------------------
    /**
     * Busca el ID único de la tabla 'HorarioMesa' 
     * basado en el ID_Mesa y el ID_Horario.
     */
    public function obtenerIdHorarioMesa($id_mesa, $id_horario) {
        $sql = "SELECT ID_HorarioMesa FROM HorarioMesa WHERE ID_Mesa = ? AND ID_Horario = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_mesa, $id_horario]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Devuelve el ID si lo encuentra, o 'false' si no
        return $resultado ? $resultado['ID_HorarioMesa'] : false;
    }


    // -----------------------------------------------------------------
    // 🔹 FUNCIÓN CORREGIDA (Ahora coincide con el controlador)
    // -----------------------------------------------------------------
    public function guardarReserva($data) {
        try {
            // El controlador ya buscó el 'ID_HorarioMesa' y lo pasó en $data
            
            $sql = "DECLARE @BloquesReserva AS TIPO_BLOQUES_RESERVA;
                    INSERT INTO @BloquesReserva (ID_HorarioMesa) VALUES (:id_horario_mesa);

                    EXEC SP_CrearReserva
                        @ID_Cliente_Param = :id_cliente,
                        @Fecha_Param = :fecha,
                        @Personas_Param = :personas,
                        @BloquesReservar = @BloquesReserva,
                        @ID_Usuario_Log = NULL;";

            $stmt = $this->db->prepare($sql);

            // ¡CORREGIDO! Usamos los nombres de variables que el controlador nos envía
            $stmt->bindParam(':id_horario_mesa', $data['id_horario_mesa']); 
            $stmt->bindParam(':id_cliente', $data['id_cliente']);
            $stmt->bindParam(':fecha', $data['fecha_reserva']);
            $stmt->bindParam(':personas', $data['personas']);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al crear reserva: " . $e->getMessage());
            return false;
        }
    }
}
?>