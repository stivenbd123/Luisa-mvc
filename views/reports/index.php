<?php require_once 'views/layouts/header.php'; ?>

<style>
    .module-container { background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); padding: 30px; border-top: 4px solid #4f46e5; }
    .module-header { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; }
    .module-title { color: #1e1b4b; font-size: 20px; font-weight: 600; margin-bottom: 10px; }
    
    .search-box { display: flex; gap: 10px; margin-bottom: 25px; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; }
    .search-input { flex: 1; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; }
    .btn-search { background: #4f46e5; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background-color: #f8fafc; color: #475569; font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 15px; text-align: left; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 16px 15px; color: #334155; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
    .btn-view { background: #10b981; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; }
</style>

<div class="module-container">
    <div class="module-header">
        <h2 class="module-title">Búsqueda de Historial Clínico</h2>
        <p style="color: #64748b; font-size: 14px;">Busca un paciente por nombre o documento para ver su expediente.</p>
    </div>

    <form action="index.php" method="GET" class="search-box">
        <input type="hidden" name="action" value="historial_pacientes">
        <input type="text" name="search" class="search-input" placeholder="Nombre o número de documento..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit" class="btn-search">Buscar Paciente</button>
    </form>

    <table class="data-table">
        <thead>
            <tr>
                <th>Documento</th>
                <th>Nombre del Paciente</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($patients)): ?>
                <tr><td colspan="3" style="text-align:center; padding: 30px; color: #94a3b8;">No se encontraron pacientes.</td></tr>
            <?php else: ?>
                <?php foreach($patients as $p): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['document']) ?></strong></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td>
                            <a href="index.php?action=ver_historial&id=<?= $p['id'] ?>" class="btn-view">👁 Ver Historial</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'views/layouts/footer.php'; ?>