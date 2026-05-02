<?php
require_once __DIR__ . '/../config/database.php';

class ReportController
{
    public function index()
    {
        $db = new Database();
        $conn = $db->getConnection();
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        if (!empty($search)) {
            $stmt = $conn->prepare("SELECT * FROM patients WHERE name LIKE ? OR document LIKE ? ORDER BY name ASC");
            $stmt->execute(["%$search%", "%$search%"]);
        } else {
            $stmt = $conn->query("SELECT * FROM patients ORDER BY name ASC LIMIT 50"); 
        }
        $patients = $stmt->fetchAll();
        require_once 'views/reports/index.php';
    }

    public function show()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $db = new Database();
            $conn = $db->getConnection();

            $stmtPat = $conn->prepare("SELECT * FROM patients WHERE id = ?");
            $stmtPat->execute([$id]);
            $patient = $stmtPat->fetch();

            $sql = "SELECT a.*, d.name as doctor_name, s.name as specialty_name, c.name as room_name 
                    FROM appointments a
                    JOIN doctors d ON a.doctor_id = d.id
                    JOIN specialties s ON d.specialty_id = s.id
                    JOIN consulting_rooms c ON a.consulting_room_id = c.id
                    WHERE a.patient_id = ? 
                    ORDER BY a.appointment_date DESC";
            $stmtAppt = $conn->prepare($sql);
            $stmtAppt->execute([$id]);
            $appointments = $stmtAppt->fetchAll();

            require_once 'views/reports/show.php';
        }
    }

    public function exportsView()
    {
        $db = new Database();
        $conn = $db->getConnection();
        $doctors = $conn->query("SELECT * FROM doctors ORDER BY name ASC")->fetchAll();
        $specialties = $conn->query("SELECT * FROM specialties ORDER BY name ASC")->fetchAll();
        require_once 'views/reports/exports.php';
    }

    public function exportPatientHistory()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $db = new Database();
            $conn = $db->getConnection();

            $stmtPat = $conn->prepare("SELECT name FROM patients WHERE id = ?");
            $stmtPat->execute([$id]);
            $patient = $stmtPat->fetch();

            $sql = "SELECT a.appointment_date, d.name as doctor_name, s.name as specialty_name, c.name as room_name, a.status 
                    FROM appointments a
                    JOIN doctors d ON a.doctor_id = d.id
                    JOIN specialties s ON d.specialty_id = s.id
                    JOIN consulting_rooms c ON a.consulting_room_id = c.id
                    WHERE a.patient_id = ? ORDER BY a.appointment_date DESC";
            $stmtAppt = $conn->prepare($sql);
            $stmtAppt->execute([$id]);
            $appointments = $stmtAppt->fetchAll();

            $filename = "historial_" . str_replace(' ', '_', $patient['name']) . ".csv";
            
            header("Content-type: text/csv; charset=UTF-8");
            header("Content-Disposition: attachment; filename=$filename");

            $output = fopen("php://output", "w");
            fputs($output, "\xEF\xBB\xBF"); 
            // ARREGLO: Añadimos el parámetro de escape "\\" al final para evitar el error Deprecated
            fputcsv($output, ['Fecha', 'Medico', 'Especialidad', 'Consultorio', 'Estado'], ';', '"', '\\');

            foreach ($appointments as $appt) {
                fputcsv($output, [
                    date('d/m/Y H:i', strtotime($appt['appointment_date'])),
                    "Dr. " . $appt['doctor_name'],
                    $appt['specialty_name'],
                    $appt['room_name'],
                    strtoupper($appt['status'])
                ], ';', '"', '\\');
            }
            fclose($output);
            exit();
        }
    }

    public function generateReport()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new Database();
            $conn = $db->getConnection();

            $sql = "SELECT a.appointment_date, p.name as patient_name, d.name as doctor_name, s.name as specialty_name, a.status 
                    FROM appointments a
                    JOIN patients p ON a.patient_id = p.id
                    JOIN doctors d ON a.doctor_id = d.id
                    JOIN specialties s ON d.specialty_id = s.id
                    WHERE 1=1"; 
            
            $params = [];

            if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
                $sql .= " AND a.appointment_date BETWEEN ? AND ?";
                $params[] = $_POST['start_date'] . ' 00:00:00';
                $params[] = $_POST['end_date'] . ' 23:59:59';
            }

            if (!empty($_POST['doctor_id'])) {
                $sql .= " AND a.doctor_id = ?";
                $params[] = $_POST['doctor_id'];
            }

            if (!empty($_POST['specialty_id'])) {
                $sql .= " AND d.specialty_id = ?";
                $params[] = $_POST['specialty_id'];
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll();

            header("Content-type: text/csv; charset=UTF-8");
            header("Content-Disposition: attachment; filename=reporte_general.csv");

            $output = fopen("php://output", "w");
            fputs($output, "\xEF\xBB\xBF");
            // ARREGLO: Añadimos escape "\\"
            fputcsv($output, ['Fecha', 'Paciente', 'Medico', 'Especialidad', 'Estado'], ';', '"', '\\');

            foreach ($data as $row) {
                fputcsv($output, [
                    date('d/m/Y H:i', strtotime($row['appointment_date'])),
                    $row['patient_name'],
                    "Dr. " . $row['doctor_name'],
                    $row['specialty_name'],
                    strtoupper($row['status'])
                ], ';', '"', '\\');
            }
            fclose($output);
            exit();
        }
    }
    // Función para enviar el correo de recordatorio vía AJAX
    public function sendReminder()
    {
        // 1. Recibimos el ID que viene por el fetch de JavaScript
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['id'])) {
            $id = $data['id'];
            $db = new Database();
            $conn = $db->getConnection();

            // 2. Consultamos los datos necesarios (JOIN a pacientes para el correo y médicos para el nombre)
            // IMPORTANTE: Quitamos 'notes' de la consulta para evitar errores
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
                    // CONFIGURACIÓN DEL SERVIDOR SMTP (GMAIL)
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    
                    // ========================================================
                    // REEMPLAZA ESTOS DATOS CON TUS CREDENCIALES REALES
                    // ========================================================
                    $mail->Username   = 'stivenbd123@gmail.com'; 
                    $mail->Password   = 'rvslmtgthaaguhos'; // La clave de 16 letras de Google
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port       = 465;

                    // REMITENTE Y DESTINATARIO
                    $mail->setFrom('stivenbd123@gmail.com', 'MediSys Clinic');
                    $mail->addAddress($appt['email'], $appt['patient_name']);
                    
                    // CONTENIDO DEL CORREO
                    $mail->isHTML(true);
                    $mail->Subject = 'Recordatorio de Cita Medica - MediSys';

                    $fecha_formateada = date('d/m/Y h:i A', strtotime($appt['appointment_date']));

                    $mail->Body = "
                        <div style='font-family: sans-serif; color: #334155; max-width: 600px; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px;'>
                            <h2 style='color: #4f46e5;'>Recordatorio de Cita</h2>
                            <p>Hola <strong>{$appt['patient_name']}</strong>,</p>
                            <p>Te escribimos de <strong>MediSys Clinic</strong> para recordarte tu próxima cita programada:</p>
                            <div style='background: #f8fafc; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #4f46e5;'>
                                <p style='margin: 5px 0;'><strong>📅 Fecha y Hora:</strong> {$fecha_formateada}</p>
                                <p style='margin: 5px 0;'><strong>👨‍⚕️ Médico:</strong> Dr./Dra. {$appt['doctor_name']}</p>
                                <p style='margin: 5px 0;'><strong>🏥 Consultorio:</strong> {$appt['room_name']}</p>
                            </div>
                            <p style='font-size: 13px; color: #64748b;'>Si no puedes asistir, por favor infórmanos con al menos 24 horas de antelación.</p>
                            <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                            <p style='font-size: 11px; color: #94a3b8; text-align: center;'>Este es un mensaje automático, por favor no respondas a este correo.</p>
                        </div>
                    ";

                    $mail->send();
                    // Respuesta para el JavaScript
                    echo json_encode(['success' => true, 'message' => 'El recordatorio ha sido enviado con éxito al correo del paciente.']);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Cita no encontrada en la base de datos.']);
            }
            exit(); // Finalizamos para que no cargue nada más de PHP
        }
    }
}