<?php require_once 'views/layouts/header.php'; ?>

<style>
    .module-container { background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); padding: 30px; border-top: 4px solid #4f46e5; }
    .module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; }
    .module-title { color: #1e1b4b; font-size: 20px; font-weight: 600; }
    .btn-primary { background-color: #4f46e5; color: #ffffff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; transition: background-color 0.2s; }
    .btn-primary:hover { background-color: #4338ca; }
    .alert-success { background-color: #d1fae5; color: #059669; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background-color: #f8fafc; color: #475569; font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 15px; text-align: left; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 16px 15px; color: #334155; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
    .badge-status { background-color: #e0e7ff; color: #4338ca; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
    
    /* BOTONES ARREGLADOS (FLEXBOX Y ESPACIADO) */
    .action-buttons { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .btn-reminder { background: #1e1b4b; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; transition: background 0.2s; white-space: nowrap; }
    .btn-reminder:hover { background: #312e81; }
    .btn-edit { background: #10b981; color: white; text-decoration: none; padding: 8px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; transition: background 0.2s; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; }
    .btn-edit:hover { background: #059669; }
    
    .empty-state { text-align: center; padding: 40px; color: #64748b; font-style: italic; }
</style>

<div class="module-container">
    <div class="module-header">
        <h2 class="module-title">Agenda General de Citas</h2>
        <a href="index.php?action=citas_crear" class="btn-primary">Nueva Cita</a>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert-success">
            <?= $_SESSION['success']; ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>Fecha y Hora</th>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($appointments)): ?>
                <tr>
                    <td colspan="5" class="empty-state">No hay citas registradas.</td>
                </tr>
            <?php else: ?>
                <?php foreach($appointments as $appt): ?>
                    <tr>
                        <td><strong><?= date('d/m/Y H:i', strtotime($appt['appointment_date'])) ?></strong></td>
                        <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                        <td>Dr./Dra. <?= htmlspecialchars($appt['doctor_name']) ?></td>
                        <td><span class="badge-status"><?= htmlspecialchars($appt['status']) ?></span></td>
                        <td>
                            <div class="action-buttons">
                                <button onclick="enviarRecordatorio(<?= $appt['id'] ?>)" id="btn-rem-<?= $appt['id'] ?>" class="btn-reminder">
                                    <span>📧</span> Recordatorio
                                </button>
                                <a href="index.php?action=citas_editar&id=<?= $appt['id'] ?>" class="btn-edit">✎ Gestionar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function enviarRecordatorio(appointmentId) {
    const btn = document.getElementById('btn-rem-' + appointmentId);
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = '<span>⏳</span> Enviando...';
    btn.style.opacity = '0.7';
    btn.disabled = true;

    fetch(`index.php?action=enviar_recordatorio`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: appointmentId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Éxito: ' + data.message);
            btn.innerHTML = '<span>✅</span> Enviado';
            btn.style.background = '#10b981';
        } else {
            alert('❌ Error: ' + data.message);
            btn.innerHTML = originalContent;
            btn.disabled = false;
            btn.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error de conexión con el servidor.');
        btn.innerHTML = originalContent;
        btn.disabled = false;
        btn.style.opacity = '1';
    });
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>