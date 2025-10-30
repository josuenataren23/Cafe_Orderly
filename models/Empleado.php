<?php
// app/models/Empleado.php
class Empleado {
    private $pdo;

    public function __construct() {
        $this->pdo = getPDO();
    }

    public function all() {
        $stmt = $this->pdo->query("SELECT * FROM empleados ORDER BY nombre");
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM empleados WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO empleados (nombre, dni, cargo, salario, estado, id_usuario, id_puesto)
                VALUES (:nombre, :dni, :cargo, :salario, :estado, :id_usuario, :id_puesto)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':dni' => $data['dni'],
            ':cargo' => $data['cargo'],
            ':salario' => $data['salario'],
            ':estado' => $data['estado'],
            ':id_usuario' => $data['id_usuario'],
            ':id_puesto' => $data['id_puesto'],
        ]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE empleados SET nombre=:nombre, dni=:dni, cargo=:cargo, salario=:salario, estado=:estado, id_puesto=:id_puesto WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':dni' => $data['dni'],
            ':cargo' => $data['cargo'],
            ':salario' => $data['salario'],
            ':estado' => $data['estado'],
            ':id_puesto' => $data['id_puesto'],
            ':id' => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM empleados WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
