<?php require_once 'views/layouts/header.php'; ?>

<style>
    .form-container { background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); padding: 40px; max-width: 600px; margin: 0 auto; border-top: 4px solid #1e1b4b; }
    .form-title { color: #1e1b4b; font-size: 20px; font-weight: 600; margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
    .info-card { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin-bottom: 25px; }
    .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
    .info-label { color: #64748b; font-weight: 600; }
    .info-value { color: #1e1b4b; font-weight: 500; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; color: #475569; font-size: 13px; font-weight: 600; margin-bottom: 8px; }
    .form-control { width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; color: #1e293b; background-color: #ffffff; transition: border-color 0.2s; }
    .form-control:focus { outline: none; border-color: #4f46e5; }
    .form-actions { display: flex; justify-content: space-between; margin-top: 30px; }
    .btn-cancel { color: #64748b; text-decoration: none; padding: 12px 20px; font-size: 14px; font-weight: 500; }
    .btn-submit { background-color: #4f46e5; color: #ffffff; border: none; padding: 12px 25px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
    .btn-submit:hover { background-color: #4338ca; }
</style>

<div class="form-container">
    <h2 class="form-title">Gestión de Cita Médica</h2>

    <div class="info-card">
        <div class="info-row">
            <span class="info-label">Paciente:</span>
            <span class="info-value"><?= htmlspecialchars($appointment['patient_name']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Médico:</span>
            <span class="info-value">Dr./Dra. <?= htmlspecialchars($appointment['doctor_name']) ?> (<?= htmlspecialchars($appointment['specialty_name']) ?>)</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha y Hora:</span>
            <span class="info-value"><?= date('d/m/Y H:i', strtotime($appointment['appointment_date'])) ?></span>
        </div>
        <div class="info-row" style="margin-bottom: 0;">
            <span class="info-label">Consultorio:</span>
            <span class="info-value"><?= htmlspecialchars($appointment['room_name']) ?></span>
        </div>
    </div>

    <form action="index.php?action=actualizar_cita&id=<?= $appointment['id'] ?>" method="POST">
        <div class="form-group">
            <label class="form-label">Estado de la Cita</label>
            <select name="status" class="form-control" required>
                <option value="Agendada" <?= $appointment['status'] === 'Agendada' ? 'selected' : '' ?>>Agendada</option>
                <option value="Confirmada" <?= $appointment['status'] === 'Confirmada' ? 'selected' : '' ?>>Confirmada</option>
                <option value="Atendida" <?= $appointment['status'] === 'Atendida' ? 'selected' : '' ?>>Atendida</option>
                <option value="Cancelada" <?= $appointment['status'] === 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Observaciones / Motivo de Cancelación</label>
            <textarea name="notes" class="form-control" rows="4" placeholder="Registre detalles adicionales..."><?= htmlspecialchars($appointment['notes'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="index.php?action=citas" class="btn-cancel">Volver</a>
            <button type="submit" class="btn-submit">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php require_once 'views/layouts/footer.php'; ?>