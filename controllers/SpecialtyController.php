<?php
require_once __DIR__ . '/../config/database.php';

class SpecialtyController
{
    // Muestra la lista de especialidades
    public function index()
    {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->query("SELECT * FROM specialties ORDER BY name ASC");
        $specialties = $stmt->fetchAll();

        // Según tu captura, esta es la vista donde se gestiona esto
        require_once 'views/specialties/index.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);

            $db = new Database();
            $conn = $db->getConnection();

            // 1. VALIDACIÓN: Requerido
            if (empty($name)) {
                $_SESSION['error'] = 'El nombre de la especialidad es obligatorio.';
                header("Location: index.php?action=medicos");
                exit();
            }

            // 2. VALIDACIÓN: Unique (que no exista ya)
            $stmtCheck = $conn->prepare("SELECT id FROM specialties WHERE name = ?");
            $stmtCheck->execute([$name]);
            if ($stmtCheck->fetch()) {
                $_SESSION['error'] = 'Esta especialidad ya existe en el sistema.';
                header("Location: index.php?action=medicos");
                exit();
            }

            // 3. GUARDAR
            $stmt = $conn->prepare("INSERT INTO specialties (name) VALUES (?)");
            $stmt->execute([$name]);

            $_SESSION['success'] = 'Especialidad médica registrada correctamente.';
            header("Location: index.php?action=medicos");
            exit();
        }
    }

    public function create() {
require_once 'views/specialties/create.php';    }
}