<?php require_once 'views/layouts/header.php'; ?>

<style>
    .export-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); max-width: 800px; margin: 0 auto; border-top: 4px solid #4f46e5; }
    .export-title { color: #1e1b4b; font-size: 22px; font-weight: 700; margin-bottom: 30px; text-align: center; }
    
    .filter-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
    .filter-group { display: flex; flex-direction: column; gap: 8px; }
    .filter-group label { font-size: 13px; font-weight: 600; color: #475569; }
    .filter-control { padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; }
    
    .btn-generate { width: 100%; background: #4f46e5; color: white; border: none; padding: 15px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s; }
    .btn-generate:hover { background: #4338ca; transform: translateY(-2px); }
</style>

<div class="export-card">
    <h2 class="export-title">Generador de Reportes Globales</h2>
    
    <form action="index.php?action=generar_reporte" method="POST">
        <input type="hidden" name="format" value="csv">
        
        <div class="filter-grid">
            <div class="filter-group">
                <label>Fecha Inicio</label>
                <input type="date" name="start_date" class="filter-control">
            </div>
            <div class="filter-group">
                <label>Fecha Fin</label>
                <input type="date" name="end_date" class="filter-control">
            </div>
            <div class="filter-group">
                <label>Filtrar por Médico</label>
                <select name="doctor_id" class="filter-control">
                    <option value="">Todos los médicos</option>
                    <?php foreach($doctors as $d): ?>
                        <option value="<?= $d['id'] ?>">Dr./Dra. <?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Filtrar por Especialidad</label>
                <select name="specialty_id" class="filter-control">
                    <option value="">Todas las especialidades</option>
                    <?php foreach($specialties as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit" class="btn-generate">
            📊 Generar y Descargar Reporte (CSV)
        </button>
    </form>
    
    <p style="text-align: center; margin-top: 20px; color: #94a3b8; font-size: 13px;">
        El reporte se descargará automáticamente en formato compatible con Excel.
    </p>
</div>

<?php require_once 'views/layouts/footer.php'; ?>