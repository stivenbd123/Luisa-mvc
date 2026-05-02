<?php
require_once __DIR__ . '/../config/database.php';

class PatientController
{
    public function index()
    {
        $db = new Database();
        $conn = $db->getConnection();

        // En nuestra BD manual no creamos 'created_at', así que ordenamos por ID descendente 
        // para que los más recientes salgan primero (igual que en tu Laravel)
        $stmt = $conn->query("SELECT * FROM patients ORDER BY id DESC");
        $patients = $stmt->fetchAll();

        // Muestra la vista de la lista
        require_once 'views/patients/index.php';
    }

    public function create()
    {
        // Muestra el formulario vacío
        require_once 'views/patients/create.php';
    }

    public function store()
    {
        // Verificamos que los datos vengan del formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Capturamos los datos en lugar de usar $request->all()
            $document = trim($_POST['document']);
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            // Validamos si enviaron teléfono (nullable en tu Laravel)
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

            $db = new Database();
            $conn = $db->getConnection();

            // 1. VALIDACIONES MANUALES
            if (empty($document) || empty($name) || empty($email)) {
                $_SESSION['error'] = 'El documento, nombre y correo son obligatorios.';
                header("Location: index.php?action=pacientes_crear");
                exit();
            }

            // Validar unique:patients
            $stmtCheck = $conn->prepare("SELECT id FROM patients WHERE document = ? OR email = ?");
            $stmtCheck->execute([$document, $email]);
            if ($stmtCheck->fetch()) {
                $_SESSION['error'] = 'Este documento o correo ya está registrado.';
                header("Location: index.php?action=pacientes_crear");
                exit();
            }

            // 2. GUARDAR EN LA BASE DE DATOS
            $stmt = $conn->prepare("INSERT INTO patients (document, name, email, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$document, $name, $email, $phone]);

            // 3. REDIRECCIÓN CON MENSAJE DE ÉXITO
            $_SESSION['success'] = 'Paciente registrado exitosamente.';
            header("Location: index.php?action=pacientes");
            exit();
        }
    }
}
?>