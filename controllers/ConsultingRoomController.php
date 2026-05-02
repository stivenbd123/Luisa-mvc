<?php
require_once __DIR__ . '/../config/database.php';

class ConsultingRoomController
{
    public function index()
    {
        $db = new Database();
        $conn = $db->getConnection();

        // 1. Traemos los consultorios con su especialidad (Eager Loading manual)
        $sql = "SELECT consulting_rooms.*, specialties.name as specialty_name 
                FROM consulting_rooms 
                INNER JOIN specialties ON consulting_rooms.specialty_id = specialties.id 
                ORDER BY consulting_rooms.name ASC";
        
        $stmt = $conn->query($sql);
        $rooms = $stmt->fetchAll();

        // 2. Traemos las especialidades por si el formulario de "Crear" está en la misma pantalla
        $stmtSpec = $conn->query("SELECT * FROM specialties ORDER BY name ASC");
        $specialties = $stmtSpec->fetchAll();

        // Llamamos a la vista que tenías en tu estructura
        require_once 'views/consulting_rooms/index.php';
    }

    public function create() 
    {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->query("SELECT * FROM specialties ORDER BY name ASC");
        $specialties = $stmt->fetchAll();
        
        require_once 'views/consulting_rooms/create.php';
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
                $_SESSION['error'] = 'El identificador del consultorio y la especialidad son obligatorios.';
                header("Location: index.php?action=consultorios");
                exit();
            }

            // 2. GUARDAR
            $stmt = $conn->prepare("INSERT INTO consulting_rooms (name, specialty_id) VALUES (?, ?)");
            $stmt->execute([$name, $specialty_id]);

            $_SESSION['success'] = 'Consultorio registrado y habilitado en el sistema.';
            header("Location: index.php?action=consultorios");
            exit();
        }
    }
}
?>