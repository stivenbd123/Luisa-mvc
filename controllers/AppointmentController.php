<?php
require_once __DIR__ . '/../config/database.php';

// Importamos PHPMailer manualmente
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AppointmentController
{
    public function index()
    {
        $db = new Database();
        $conn = $db->getConnection();

        $sql = "SELECT a.*, p.name as patient_name, p.email, d.name as doctor_name, c.name as room_name 
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN consulting_rooms c ON a.consulting_room_id = c.id
                ORDER BY a.appointment_date DESC";
        $stmt = $conn->query($sql);
        $appointments = $stmt->fetchAll();

        require_once 'views/appointments/index.php';
    }

    public function create()
    {
        $db = new Database();
        $conn = $db->getConnection();

        $stmtPat = $conn->query("SELECT * FROM patients ORDER BY name ASC");
        $patients = $stmtPat->fetchAll();

        $stmtSpec = $conn->query("SELECT * FROM specialties ORDER BY name ASC");
        $specialties = $stmtSpec->fetchAll();

        require_once 'views/appointments/create.php'; 
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $patient_id = $_POST['patient_id'];
            $doctor_id = $_POST['doctor_id'];
            $room_id = $_POST['consulting_room_id'];
            $date = $_POST['appointment_date'];

            $db = new Database();
            $conn = $db->getConnection();

            // CORRECCIÓN: Le quitamos la columna 'notes' a la consulta
            $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, consulting_room_id, appointment_date, status) VALUES (?, ?, ?, ?, 'Agendada')");
            $stmt->execute([$patient_id, $doctor_id, $room_id, $date]);

            $_SESSION['success'] = 'Cita médica agendada y registrada en el sistema.';
            header("Location: index.php?action=citas");
            exit();
        }
    }

    public function getDetailsBySpecialty()
    {
        if (isset($_GET['specialty_id'])) {
            $specialty_id = $_GET['specialty_id'];
            $db = new Database();
            $conn = $db->getConnection();

            $stmtDocs = $conn->prepare("SELECT id, name FROM doctors WHERE specialty_id = ?");
            $stmtDocs->execute([$specialty_id]);
            
            $stmtRooms = $conn->prepare("SELECT id, name FROM consulting_rooms WHERE specialty_id = ?");
            $stmtRooms->execute([$specialty_id]);

            echo json_encode([
                'doctors' => $stmtDocs->fetchAll(),
                'rooms' => $stmtRooms->fetchAll()
            ]);
            exit();
        }
    }

    public function edit()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $db = new Database();
            $conn = $db->getConnection();

            $sql = "SELECT a.*, p.name as patient_name, d.name as doctor_name, c.name as room_name, s.name as specialty_name
                    FROM appointments a
                    JOIN patients p ON a.patient_id = p.id
                    JOIN doctors d ON a.doctor_id = d.id
                    JOIN specialties s ON d.specialty_id = s.id
                    JOIN consulting_rooms c ON a.consulting_room_id = c.id
                    WHERE a.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $appointment = $stmt->fetch();

            require_once 'views/appointments/edit.php';
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $status = $_POST['status'];

            $db = new Database();
            $conn = $db->getConnection();

            // CORRECCIÓN: Le quitamos la columna 'notes' al UPDATE
            $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);

            $_SESSION['success'] = 'El estado de la cita ha sido actualizado.';
            header("Location: index.php?action=citas");
            exit();
        }
    }

    public function sendReminder()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['id'])) {
            $id = $data['id'];
            $db = new Database();
            $conn = $db->getConnection();

            $sql = "SELECT a.appointment_date, p.name as patient_name, p.email, d.name as doctor_name, c.name as room_name 
                    FROM appointments a
                    JOIN patients p ON a.patient_id = p.id
                    JOIN doctors d ON a.doctor_id = d.id
                    JOIN consulting_rooms c ON a.consulting_room_id = c.id
                    WHERE a.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            $appt = $stmt->fetch();

            if ($appt) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    // RECUERDA PONER TU CORREO REAL AQUÍ
                    $mail->Username   = 'TU_CORREO@gmail.com'; 
                    $mail->Password   = 'rvslmtgthaaguhos'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port       = 465;

                    $mail->setFrom('TU_CORREO@gmail.com', 'MediSys Clinic');
                    $mail->addAddress($appt['email'], $appt['patient_name']);
                    $mail->isHTML(true);
                    $mail->Subject = 'Recordatorio de Cita Medica - MediSys';

                    $fecha_formateada = date('d/m/Y h:i A', strtotime($appt['appointment_date']));

                    $mail->Body = "
                        <div style='font-family: sans-serif; color: #334155; max-width: 600px; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px;'>
                            <h2 style='color: #0284c7;'>Recordatorio de Cita</h2>
                            <p>Hola <strong>{$appt['patient_name']}</strong>,</p>
                            <p>Te escribimos de <strong>MediSys Clinic</strong> para recordarte tu próxima cita programada:</p>
                            <div style='background: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                                <p style='margin: 5px 0;'><strong>📅 Fecha y Hora:</strong> {$fecha_formateada}</p>
                                <p style='margin: 5px 0;'><strong>👨‍⚕️ Médico:</strong> Dr./Dra. {$appt['doctor_name']}</p>
                                <p style='margin: 5px 0;'><strong>🏥 Consultorio:</strong> {$appt['room_name']}</p>
                            </div>
                            <p style='font-size: 13px; color: #64748b;'>Si no puedes asistir, por favor infórmanos con al menos 24 horas de antelación.</p>
                        </div>
                    ";

                    $mail->send();
                    echo json_encode(['success' => true, 'message' => 'El recordatorio ha sido enviado con éxito al correo del paciente.']);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Cita no encontrada.']);
            }
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de cita no proporcionado.']);
            exit();
        }
    }
}
?>