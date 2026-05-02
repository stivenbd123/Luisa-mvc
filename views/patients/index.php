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
    .empty-state { text-align: center; padding: 40px; color: #64748b; font-style: italic; }
</style>

<div class="module-container">
    <div class="module-header">
        <h2 class="module-title">Directorio de Pacientes</h2>
        <a href="index.php?action=pacientes_crear" class="btn-primary">Registrar Paciente</a>
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
                <th>Documento</th>
                <th>Nombre Completo</th>
                <th>Correo Electrónico</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($patients)): ?>
                <tr>
                    <td colspan="4" class="empty-state">No hay pacientes registrados en el sistema.</td>
                </tr>
            <?php else: ?>
                <?php foreach($patients as $patient): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($patient['document']) ?></strong></td>
                        <td><?= htmlspecialchars($patient['name']) ?></td>
                        <td><?= htmlspecialchars($patient['email']) ?></td>
                        <td><?= !empty($patient['phone']) ? htmlspecialchars($patient['phone']) : 'No registrado' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'views/layouts/footer.php'; ?>