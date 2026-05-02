<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediSys | Panel Médico</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; background-color: #f8fafc; min-height: 100vh; }
        
        /* Sidebar - Índigo Oscuro */
        .sidebar { width: 260px; background-color: #1e1b4b; color: white; position: fixed; height: 100%; z-index: 100; box-shadow: 4px 0 10px rgba(0,0,0,0.1); }
        .sidebar-header { padding: 25px 20px; background-color: #312e81; text-align: center; border-bottom: 1px solid #4338ca; }
        .sidebar-header h2 { font-weight: 700; letter-spacing: 2px; color: #10b981; }
        
        .sidebar-menu { list-style: none; padding: 20px 0; }
        .sidebar-menu li a { display: block; color: #cbd5e1; text-decoration: none; padding: 15px 25px; font-size: 15px; border-left: 4px solid transparent; transition: all 0.3s ease; }
        .sidebar-menu li a:hover { background-color: #3730a3; color: #ffffff; border-left: 4px solid #10b981; padding-left: 30px; }
        
        /* Contenido Principal */
        .main-content { flex: 1; margin-left: 260px; display: flex; flex-direction: column; width: calc(100% - 260px); }
        
        /* Barra Superior */
        .topbar { background-color: #ffffff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); z-index: 10; }
        .user-info { color: #334155; font-size: 15px; display: flex; align-items: center; gap: 10px;}
        .role-badge { background: #e0e7ff; color: #4338ca; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        
        /* Botón Cerrar Sesión */
        .logout-btn { background-color: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: background 0.2s; text-decoration: none; display: inline-block; box-shadow: 0 2px 4px rgba(239,68,68,0.3); }
        .logout-btn:hover { background-color: #dc2626; transform: translateY(-1px); }
        
        /* Contenedor dinámico */
        .content-wrapper { padding: 30px; overflow-y: auto; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>MediSys</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php?action=home">Inicio</a></li>
            <li><a href="index.php?action=pacientes">Pacientes</a></li>
            <li><a href="index.php?action=especialidades">Especialidades</a></li>
            <li><a href="index.php?action=medicos">Cuerpo Médico</a></li>
            <li><a href="index.php?action=consultorios">Consultorios</a></li>
            <li><a href="index.php?action=citas">Citas Médicas</a></li>
            <li><a href="index.php?action=historial_pacientes">Historial Clínico</a></li>
            <li><a href="index.php?action=reportes_globales">Exportar Reportes</a></li>
            
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li style="border-top: 1px solid #4338ca; margin-top: 15px; padding-top: 10px;">
                    <a href="index.php?action=usuarios" style="color: #fbbf24; font-weight: 600;">Gestionar Usuarios</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <main class="main-content">
        <header class="topbar">
            <div class="user-info">
                <span>Hola, <strong><?= isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Staff' ?></strong></span>
                <span class="role-badge"><?= strtoupper($_SESSION['role'] ?? 'INVITADO') ?></span>
            </div>
            <a href="index.php?action=logout" class="logout-btn">Cerrar Sesión</a>
        </header>
        
        <div class="content-wrapper">