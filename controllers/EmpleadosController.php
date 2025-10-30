<?php
// app/controllers/EmpleadosController.php
class EmpleadosController {
    private $model;

    public function __construct() {
        $this->model = new Empleado();
    }

    public function index() {
        $empleados = $this->model->all();
        require __DIR__ . '/../views/empleados.php';
    }

    public function listJson() {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->model->all());
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); echo json_encode(['error'=>'Method not allowed']); return;
        }
        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'dni' => $_POST['dni'] ?? '',
            'cargo' => $_POST['cargo'] ?? '',
            'salario' => $_POST['salario'] ?? 0,
            'estado' => $_POST['estado'] ?? '',
            'id_usuario' => $_POST['id_usuario'] ?? null,
            'id_puesto' => $_POST['id_puesto'] ?? null,
        ];
        $id = $this->model->create($data);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success'=>true, 'id'=>$id]);
    }

    public function delete() {
        $id = $_POST['id'] ?? null;
        if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); return; }
        $ok = $this->model->delete($id);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => (bool)$ok]);
    }
}
