<?php
require_once __DIR__ . '/../config/database.php';

class DoctorController
{
    public function index()
    {
        $db = new Database();
        $conn = $db->getConnection();

        // Traemos médicos con el nombre de su especialidad (El "with" de Laravel hecho a mano)
        $sql = "SELECT doctors.*, specialties.name as specialty_name 
                FROM doctors 
                INNER JOIN specialties ON doctors.specialty_id = specialties.id 
                ORDER BY doctors.name ASC";
        
        $stmt = $conn->query($sql);
        $doctors = $stmt->fetchAll();

    require_once 'views/doctors/index.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $specialty_id = $_POST['specialty_id'];

            $db = new Database();
            $conn = $db->getConnection();

            // 1. VALIDACIÓN
            if (empty($name) || empty($specialty_id)) {
                $_SESSION['error'] = 'Todos los campos son obligatorios.';
                header("Location: index.php?action=medicos");
                exit();
            }

            // 2. GUARDAR
            $stmt = $conn->prepare("INSERT INTO doctors (name, specialty_id) VALUES (?, ?)");
            $stmt->execute([$name, $specialty_id]);

            $_SESSION['success'] = 'Médico registrado correctamente en el sistema.';
            header("Location: index.php?action=medicos");
            exit();
        }
    }
    public function create() {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT * FROM specialties ORDER BY name ASC");
        $specialties = $stmt->fetchAll();
    
        require_once 'views/doctors/create.php';
    }
}