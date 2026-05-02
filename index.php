<?php
session_start();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = 'login';
}

// ==========================================
// 1. AUTENTICACIÓN Y VISTAS DIRECTAS
// ==========================================

if ($action == 'login') {
    require_once 'views/auth/login.php';
} 
elseif ($action == 'register') {
    require_once 'views/auth/register.php';
} 
elseif ($action == 'procesar_registro') {
    require_once 'Controllers/AuthController.php';
    $controller = new AuthController();
    $controller->register();
}
elseif ($action == 'procesar_login') {
    require_once 'Controllers/AuthController.php';
    $controller = new AuthController();
    $controller->login();
}
elseif ($action == 'logout') {
    require_once 'Controllers/AuthController.php';
    $controller = new AuthController();
    $controller->logout();
}
elseif ($action == 'home') {
    // Actualizado a la carpeta users
    require_once 'views/home.php';
} 

// ==========================================
// 2. MÓDULO DE PACIENTES
// ==========================================

elseif ($action == 'pacientes') {
    require_once 'Controllers/PatientController.php';
    $controller = new PatientController();
    $controller->index();
} 
elseif ($action == 'pacientes_crear') {
    require_once 'Controllers/PatientController.php';
    $controller = new PatientController();
    $controller->create();
}
elseif ($action == 'guardar_paciente') {
    require_once 'Controllers/PatientController.php';
    $controller = new PatientController();
    $controller->store();
} 

// ==========================================
// 3. MÓDULO DE MÉDICOS Y ESPECIALIDADES
// ==========================================

// ==========================================
// 3. MÓDULO DE ESPECIALIDADES Y MÉDICOS
// ==========================================
elseif ($action == 'especialidades') {
    require_once 'Controllers/SpecialtyController.php';
    $controller = new SpecialtyController();
    $controller->index();
}
elseif ($action == 'especialidades_crear') {
    require_once 'Controllers/SpecialtyController.php';
    $controller = new SpecialtyController();
    $controller->create();
}
elseif ($action == 'guardar_especialidad') {
    require_once 'Controllers/SpecialtyController.php';
    $controller = new SpecialtyController();
    $controller->store();
}
elseif ($action == 'medicos') {
    require_once 'Controllers/DoctorController.php';
    $controller = new DoctorController();
    $controller->index();
}
elseif ($action == 'medicos_crear') {
    require_once 'Controllers/DoctorController.php';
    $controller = new DoctorController();
    $controller->create();
}
elseif ($action == 'guardar_medico') {
    require_once 'Controllers/DoctorController.php';
    $controller = new DoctorController();
    $controller->store();
}
// ==========================================
// 4. MÓDULO DE CONSULTORIOS
// ==========================================

elseif ($action == 'consultorios') {
    require_once 'Controllers/ConsultingRoomController.php';
    $controller = new ConsultingRoomController();
    $controller->index();
}
elseif ($action == 'guardar_consultorio') {
    require_once 'Controllers/ConsultingRoomController.php';
    $controller = new ConsultingRoomController();
    $controller->store();
}
elseif ($action == 'consultorios_crear') {
    require_once 'Controllers/ConsultingRoomController.php';
    $controller = new ConsultingRoomController();
    $controller->create();
}

// ==========================================
// 5. MÓDULO DE CITAS (INCLUYE AJAX Y CORREOS)
// ==========================================

elseif ($action == 'citas') {
    require_once 'Controllers/AppointmentController.php';
    $controller = new AppointmentController();
    $controller->index();
}
elseif ($action == 'citas_editar') {
    require_once 'Controllers/AppointmentController.php';
    $controller = new AppointmentController();
    $controller->edit();
}
elseif ($action == 'actualizar_cita') {
    require_once 'Controllers/AppointmentController.php';
    $controller = new AppointmentController();
    $controller->update();
}
elseif ($action == 'citas_crear') {
    require_once 'Controllers/AppointmentController.php';
    $controller = new AppointmentController();
    $controller->create();
}
elseif ($action == 'guardar_cita') {
    require_once 'Controllers/AppointmentController.php';
    $controller = new AppointmentController();
    $controller->store();
}
elseif ($action == 'citas_ajax') {
    require_once 'Controllers/AppointmentController.php';
    $controller = new AppointmentController();
    $controller->getDetailsBySpecialty();
}
elseif ($action == 'enviar_recordatorio') {
    require_once 'Controllers/AppointmentController.php';
    $controller = new AppointmentController();
    $controller->sendReminder();
}

// ==========================================
// 6. MÓDULO DE USUARIOS (ADMIN)
// ==========================================

elseif ($action == 'usuarios') {
    require_once 'Controllers/UserController.php';
    $controller = new UserController();
    $controller->index();
}
elseif ($action == 'usuarios_crear') {
    require_once 'Controllers/UserController.php';
    $controller = new UserController();
    $controller->create();
}
elseif ($action == 'usuarios_editar') {
    require_once 'Controllers/UserController.php';
    $controller = new UserController();
    $controller->edit();
}
elseif ($action == 'guardar_usuario') {
    require_once 'Controllers/UserController.php';
    $controller = new UserController();
    $controller->store();
}
elseif ($action == 'actualizar_usuario') {
    require_once 'Controllers/UserController.php';
    $controller = new UserController();
    $controller->update();
}
elseif ($action == 'eliminar_usuario') {
    require_once 'Controllers/UserController.php';
    $controller = new UserController();
    $controller->destroy();
}

// ==========================================
// 7. MÓDULO DE REPORTES E HISTORIALES
// ==========================================

elseif ($action == 'historial_pacientes') {
    require_once 'Controllers/ReportController.php';
    $controller = new ReportController();
    $controller->index();
}
elseif ($action == 'ver_historial') {
    require_once 'Controllers/ReportController.php';
    $controller = new ReportController();
    $controller->show();
}
elseif ($action == 'exportar_historial') {
    require_once 'Controllers/ReportController.php';
    $controller = new ReportController();
    $controller->exportPatientHistory();
}
elseif ($action == 'reportes_globales') {
    require_once 'Controllers/ReportController.php';
    $controller = new ReportController();
    $controller->exportsView();
}
elseif ($action == 'generar_reporte') {
    require_once 'Controllers/ReportController.php';
    $controller = new ReportController();
    $controller->generateReport();
}

// ==========================================
// RUTA NO ENCONTRADA (404)
// ==========================================
else {
    echo "<div style='text-align:center; padding: 50px; font-family: sans-serif;'>";
    echo "<h1>Error 404</h1>";
    echo "<p>La página que buscas no existe en MediSys.</p>";
    echo "<a href='index.php?action=login' style='color: #4f46e5; text-decoration: none; font-weight: bold;'>Volver al inicio</a>";
    echo "</div>";
}
?>