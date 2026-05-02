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
    .badge-room { background-color: #e0e7ff; color: #4338ca; padding: 5px 10px; border-radius: 4px; font-size: 13px; font-weight: 600; border: 1px solid #c7d2fe; }
    .empty-state { text-align: center; padding: 40px; color: #64748b; font-style: italic; }
</style>

<div class="module-container">
    <div class="module-header">
        <h2 class="module-title">Gestión de Consultorios</h2>
        <a href="index.php?action=consultorios_crear" class="btn-primary">Añadir Consultorio</a>
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
                <th style="width: 15%;">ID</th>
                <th style="width: 45%;">Identificador de Sala</th>
                <th style="width: 40%;">Área Médica Asignada</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($rooms)): ?>
                <tr>
                    <td colspan="3" class="empty-state">No hay consultorios registrados en el sistema.</td>
                </tr>
            <?php else: ?>
                <?php foreach($rooms as $room): ?>
                    <tr>
                        <td><?= $room['id'] ?></td>
                        <td><strong><?= htmlspecialchars($room['name']) ?></strong></td>
                        <td>
                            <span class="badge-room">
                                <?= !empty($room['specialty_name']) ? htmlspecialchars($room['specialty_name']) : 'Sin asignar' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'views/layouts/footer.php'; ?>