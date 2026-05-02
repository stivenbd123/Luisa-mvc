<?php require_once 'views/layouts/header.php'; ?>

<style>
    .form-container { background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); padding: 40px; max-width: 600px; margin: 0 auto; border-top: 4px solid #10b981; }
    .form-title { color: #1e1b4b; font-size: 20px; font-weight: 600; margin-bottom: 25px; text-align: center; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-group.full-width { grid-column: span 2; }
    .form-label { display: block; color: #475569; font-size: 13px; font-weight: 600; margin-bottom: 8px; }
    .form-control { width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; background-color: #f8fafc; transition: border-color 0.2s; }
    .form-control:focus { outline: none; border-color: #10b981; background-color: #ffffff; }
    .alert-error { background-color: #fee2e2; color: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; text-align: center; }
    .form-actions { display: flex; justify-content: space-between; margin-top: 20px; }
    .btn-cancel { color: #64748b; text-decoration: none; padding: 12px 20px; font-size: 14px; font-weight: 500; }
    .btn-submit { background-color: #10b981; color: #ffffff; border: none; padding: 12px 25px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
    .btn-submit:hover { background-color: #059669; }
</style>

<div class="form-container">
    <h2 class="form-title">Datos del Nuevo Paciente</h2>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert-error">
            <?= $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=guardar_paciente" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Número de Documento</label>
                <input type="text" name="document" class="form-control" required>
            </div>

            <div class="form-group full-width">
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Teléfono de Contacto</label>
                <input type="text" name="phone" class="form-control">
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?action=pacientes" class="btn-cancel">Cancelar</a>
            <button type="submit" class="btn-submit">Guardar Paciente</button>
        </div>
    </form>
</div>

<?php require_once 'views/layouts/footer.php'; ?>