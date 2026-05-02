<?php require_once 'views/layouts/header.php'; ?>

<style>
    .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; }
    .card { background: #ffffff; padding: 30px 25px; border-radius: 8px; border: 1px solid #e2e8f0; text-decoration: none; transition: all 0.2s ease-in-out; display: flex; flex-direction: column; align-items: center; text-align: center; }
    .card:hover { border-color: #4f46e5; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15); transform: translateY(-2px); }
    .icon-box { font-size: 22px; font-weight: 700; margin-bottom: 15px; background: #e0e7ff; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #4338ca; border: 2px solid #c7d2fe; }
    .card-title { color: #1e1b4b; font-size: 18px; font-weight: 700; margin-bottom: 12px; }
    .card-description { color: #64748b; font-size: 14px; line-height: 1.5; }
</style>

<div style="margin-bottom: 40px;">
    <h1 style="color: #1e1b4b; font-size: 26px; font-weight: 700;">Panel de Control</h1>
    <p style="color: #64748b;">Gestión integral de servicios médicos.</p>
</div>

<div class="dashboard-grid">
    <a href="index.php?action=pacientes" class="card">
        <div class="icon-box">PA</div>
        <h3 class="card-title">Pacientes</h3>
        <span class="card-description">Directorio completo y registro de nuevos ingresos.</span>
    </a>

    <a href="index.php?action=especialidades" class="card">
        <div class="icon-box">ES</div>
        <h3 class="card-title">Especialidades</h3>
        <span class="card-description">Configuración de áreas y ramas médicas.</span>
    </a>

    <a href="index.php?action=medicos" class="card">
        <div class="icon-box">CM</div>
        <h3 class="card-title">Cuerpo Médico</h3>
        <span class="card-description">Gestión de profesionales, especialidades y turnos.</span>
    </a>

    <a href="index.php?action=consultorios" class="card">
        <div class="icon-box">CO</div>
        <h3 class="card-title">Consultorios</h3>
        <span class="card-description">Administración de salas por especialidad.</span>
    </a>

    <a href="index.php?action=citas" class="card">
        <div class="icon-box">CI</div>
        <h3 class="card-title">Agenda de Citas</h3>
        <span class="card-description">Programación y seguimiento de consultas diarias.</span>
    </a>

    <a href="index.php?action=historial_pacientes" class="card">
        <div class="icon-box">HC</div>
        <h3 class="card-title">Historial Clínico</h3>
        <span class="card-description">Consulta de expedientes y atenciones previas.</span>
    </a>

    <a href="index.php?action=reportes_globales" class="card">
        <div class="icon-box">EX</div>
        <h3 class="card-title">Exportar Reportes</h3>
        <span class="card-description">Generación de documentos en PDF y Excel.</span>
    </a>

    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <a href="index.php?action=usuarios" class="card" style="border-color: #10b981; background-color: #ecfdf5;">
        <div class="icon-box" style="background: #d1fae5; color: #059669; border-color: #a7f3d0;">US</div>
        <h3 class="card-title" style="color: #065f46;">Usuarios</h3>
        <span class="card-description">Control de accesos y perfiles del personal.</span>
    </a>
    <?php endif; ?>
</div>

<?php require_once 'views/layouts/footer.php'; ?>