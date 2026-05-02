<?php require_once 'views/layouts/header.php'; ?>

<style>
    .module-container { background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); padding: 30px; border-top: 4px solid #4f46e5; }
    .module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; }
    .module-title { color: #1e1b4b; font-size: 20px; font-weight: 600; }
    .btn-primary { background-color: #4f46e5; color: #ffffff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; transition: background-color 0.2s; }
    .btn-primary:hover { background-color: #4338ca; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background-color: #f8fafc; color: #475569; font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 15px; text-align: left; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 16px 15px; color: #334155; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
    
    .role-badge { padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .role-admin { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .role-recepcionista { background-color: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
    
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
    .alert-success { background-color: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
    .alert-error { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    
    .btn-action { text-decoration: none; font-weight: 600; font-size: 13px; margin-right: 10px; padding: 6px 12px; border-radius: 4px; transition: 0.2s; display: inline-block; }
    .btn-edit { background-color: #e0e7ff; color: #4338ca; }
    .btn-edit:hover { background-color: #c7d2fe; }
    .btn-delete { background-color: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background-color: #fecaca; }
</style>

<div class="module-container">
    <div class="module-header">
        <h2 class="module-title">Directorio de Accesos</h2>
        <a href="index.php?action=usuarios_crear" class="btn-primary">Crear Nuevo Usuario</a>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo Electrónico</th>
                <th>Perfil / Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <span class="role-badge role-<?= htmlspecialchars($user['role']) ?>">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="index.php?action=usuarios_editar&id=<?= $user['id'] ?>" class="btn-action btn-edit">Editar</a>
                        
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user['id']): ?>
                            <a href="index.php?action=eliminar_usuario&id=<?= $user['id'] ?>" class="btn-action btn-delete" onclick="return confirm('¿Está seguro de eliminar este usuario del sistema?');">Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'views/layouts/footer.php'; ?>