<?php require_once 'views/layouts/header.php'; ?>

<style>
    .form-container { background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); padding: 40px; max-width: 700px; margin: 0 auto; border-top: 4px solid #10b981; }
    .form-title { color: #1e1b4b; font-size: 20px; font-weight: 600; margin-bottom: 25px; text-align: center; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-group.full-width { grid-column: span 2; }
    .form-label { display: block; color: #475569; font-size: 13px; font-weight: 600; margin-bottom: 8px; }
    .form-control { width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; color: #1e293b; background-color: #f8fafc; transition: border-color 0.2s; }
    .form-control:focus { outline: none; border-color: #10b981; background-color: #ffffff; }
    .form-control:disabled { background-color: #e2e8f0; cursor: not-allowed; }
    .alert-error { background-color: #fee2e2; color: #ef4444; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center; }
    .form-actions { display: flex; justify-content: space-between; margin-top: 20px; }
    .btn-cancel { color: #64748b; text-decoration: none; padding: 12px 20px; font-size: 14px; font-weight: 500; }
    .btn-submit { background-color: #10b981; color: #ffffff; border: none; padding: 12px 25px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
    .btn-submit:hover { background-color: #059669; }
</style>

<div class="form-container">
    <h2 class="form-title">Agendamiento de Cita</h2>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert-error">
            <?= $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=guardar_cita" method="POST">
        <div class="form-grid">
            <div class="form-group full-width">
                <label class="form-label">Paciente</label>
                <select name="patient_id" class="form-control" required>
                    <option value="" disabled selected>Seleccione el paciente...</option>
                    <?php foreach($patients as $patient): ?>
                        <option value="<?= $patient['id'] ?>"><?= htmlspecialchars($patient['document']) ?> - <?= htmlspecialchars($patient['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Especialidad</label>
                <select name="specialty_id" id="specialty_select" class="form-control" required>
                    <option value="" disabled selected>Seleccione el área médica...</option>
                    <?php foreach($specialties as $specialty): ?>
                        <option value="<?= $specialty['id'] ?>"><?= htmlspecialchars($specialty['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Fecha y Hora</label>
                <input type="datetime-local" name="appointment_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Médico Asignado</label>
                <select name="doctor_id" id="doctor_select" class="form-control" required disabled>
                    <option value="" disabled selected>Esperando especialidad...</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Consultorio</label>
                <select name="consulting_room_id" id="room_select" class="form-control" required disabled>
                    <option value="" disabled selected>Esperando especialidad...</option>
                </select>
            </div>

            <div class="form-group full-width">
                <label class="form-label">Observaciones Adicionales (Opcional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Síntomas, recomendaciones previas, etc."></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?action=citas" class="btn-cancel">Cancelar</a>
            <button type="submit" class="btn-submit">Confirmar Agendamiento</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('specialty_select').addEventListener('change', function() {
        let specialtyId = this.value;
        let doctorSelect = document.getElementById('doctor_select');
        let roomSelect = document.getElementById('room_select');

        doctorSelect.innerHTML = '<option value="" disabled selected>Cargando médicos...</option>';
        roomSelect.innerHTML = '<option value="" disabled selected>Cargando consultorios...</option>';
        doctorSelect.disabled = true;
        roomSelect.disabled = true;

        // AJAX ajustado a la ruta de PHP puro
        fetch(`index.php?action=citas_ajax&specialty_id=${specialtyId}`)
            .then(response => response.json())
            .then(data => {
                doctorSelect.innerHTML = '<option value="" disabled selected>Seleccione el médico...</option>';
                data.doctors.forEach(doctor => {
                    doctorSelect.innerHTML += `<option value="${doctor.id}">Dr./Dra. ${doctor.name}</option>`;
                });

                roomSelect.innerHTML = '<option value="" disabled selected>Seleccione el consultorio...</option>';
                data.rooms.forEach(room => {
                    roomSelect.innerHTML += `<option value="${room.id}">${room.name}</option>`;
                });

                if(data.doctors.length > 0) doctorSelect.disabled = false;
                else doctorSelect.innerHTML = '<option value="" disabled selected>No hay médicos disponibles</option>';

                if(data.rooms.length > 0) roomSelect.disabled = false;
                else roomSelect.innerHTML = '<option value="" disabled selected>No hay salas disponibles</option>';
            })
            .catch(error => {
                console.error("Error cargando los datos:", error);
            });
    });
</script>

<?php require_once 'views/layouts/footer.php'; ?>