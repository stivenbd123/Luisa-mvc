<?php require_once 'views/layouts/header.php'; ?>

<style>
    .patient-header { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-top: 4px solid #10b981; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
    .patient-info h2 { color: #1e1b4b; margin-bottom: 5px; }
    .patient-info p { color: #64748b; font-size: 14px; }
    
    .btn-csv { background: #059669; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
    
    .timeline { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    .appointment-item { border-left: 3px solid #e2e8f0; padding-left: 20px; margin-bottom: 30px; position: relative; }
    .appointment-item::before { content: ''; width: 12px; height: 12px; background: #10b981; border-radius: 50%; position: absolute; left: -8px; top: 0; }
    .appt-date { font-weight: 700; color: #1e1b4b; font-size: 15px; margin-bottom: 5px; }
    .appt-details { font-size: 14px; color: #475569; margin-bottom: 10px; }
    .appt-notes { background: #f8fafc; padding: 10px; border-radius: 6px; font-size: 13px; color: #64748b; border-left: 3px solid #cbd5e1; }
</style>

<div class="patient-header">
    <div class="patient-info">
        <h2><?= htmlspecialchars($patient['name']) ?></h2>
        <p>Documento: <strong><?= htmlspecialchars($patient['document']) ?></strong> | Correo: <?= htmlspecialchars($patient['email']) ?></p>
    </div>
    <a href="index.php?action=exportar_historial&id=<?= $patient['id'] ?>&format=csv" class="btn-csv">
        📥 Descargar Excel (CSV)
    </a>
</div>

<div class="timeline">
    <h3 style="margin-bottom: 25px; color: #1e1b4b;">Historial de Atenciones</h3>

    <?php if(empty($appointments)): ?>
        <p style="color: #94a3b8; font-style: italic;">Este paciente no tiene citas registradas aún.</p>
    <?php else: ?>
        <?php foreach($appointments as $a): ?>
            <div class="appointment-item">
                <div class="appt-date"><?= date('d/m/Y - h:i A', strtotime($a['appointment_date'])) ?></div>
                <div class="appt-details">
                    Atendido por: <strong>Dr./Dra. <?= htmlspecialchars($a['doctor_name']) ?></strong><br>
                    Especialidad: <span style="color: #4f46e5;"><?= htmlspecialchars($a['specialty_name']) ?></span> | 
                    Consultorio: <?= htmlspecialchars($a['room_name']) ?>
                </div>
                <div class="appt-notes">
                    <strong>Estado:</strong> <?= htmlspecialchars($a['status']) ?><br>
                    <strong>Observaciones:</strong> <?= !empty($a['notes']) ? htmlspecialchars($a['notes']) : 'Sin observaciones registradas.' ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'views/layouts/footer.php'; ?>