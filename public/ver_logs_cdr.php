<?php
/**
 * Visor de Logs CDR - Interfaz Web
 * Permite ver los logs del CDR desde el navegador
 */

// Seguridad: Solo usuarios autenticados
session_start();
if (!isset($_SESSION['usuario_fac'])) {
    die('Acceso denegado. Debes iniciar sesión.');
}

// Solo administradores pueden ver logs
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] > 3) {
    die('Acceso denegado. Solo administradores pueden ver los logs.');
}

$log_file = __DIR__ . '/../error_log.log';
$lines = isset($_GET['lines']) ? intval($_GET['lines']) : 100;
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'CDR';
$refresh = isset($_GET['refresh']) ? intval($_GET['refresh']) : 0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs CDR - Sistema de Facturación</title>
    <?php if ($refresh > 0): ?>
    <meta http-equiv="refresh" content="<?= $refresh ?>">
    <?php endif; ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            background: #252526;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        h1 {
            color: #4ec9b0;
            margin-bottom: 15px;
            font-size: 24px;
        }
        .controls {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        .control-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        label {
            color: #9cdcfe;
            font-size: 14px;
        }
        input, select, button {
            padding: 8px 12px;
            border: 1px solid #3c3c3c;
            background: #2d2d30;
            color: #d4d4d4;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
        }
        button {
            background: #0e639c;
            color: white;
            cursor: pointer;
            border: none;
            transition: background 0.2s;
        }
        button:hover {
            background: #1177bb;
        }
        .log-container {
            background: #1e1e1e;
            border: 1px solid #3c3c3c;
            border-radius: 8px;
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .log-line {
            padding: 6px 10px;
            margin: 2px 0;
            border-radius: 4px;
            font-size: 13px;
            line-height: 1.5;
            word-wrap: break-word;
        }
        .log-debug {
            background: #1e3a5f;
            border-left: 3px solid #4ec9b0;
        }
        .log-error {
            background: #5a1e1e;
            border-left: 3px solid #f48771;
            color: #f48771;
        }
        .log-success {
            background: #1e5a1e;
            border-left: 3px solid #4ec9b0;
            color: #4ec9b0;
        }
        .log-info {
            background: #2d2d30;
            border-left: 3px solid #9cdcfe;
        }
        .timestamp {
            color: #858585;
            margin-right: 10px;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat-card {
            background: #252526;
            padding: 15px 20px;
            border-radius: 8px;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .stat-label {
            color: #858585;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
        }
        .stat-success { color: #4ec9b0; }
        .stat-error { color: #f48771; }
        .stat-info { color: #9cdcfe; }
        .no-logs {
            text-align: center;
            padding: 40px;
            color: #858585;
        }
        .refresh-info {
            color: #858585;
            font-size: 12px;
            margin-left: auto;
        }
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #1e1e1e;
        }
        ::-webkit-scrollbar-thumb {
            background: #3c3c3c;
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4c4c4c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Logs del Sistema CDR</h1>
            <form method="GET" class="controls">
                <div class="control-group">
                    <label>Líneas:</label>
                    <input type="number" name="lines" value="<?= $lines ?>" min="10" max="1000" style="width: 80px;">
                </div>
                <div class="control-group">
                    <label>Filtro:</label>
                    <select name="filter">
                        <option value="CDR" <?= $filter === 'CDR' ? 'selected' : '' ?>>Solo CDR</option>
                        <option value="ERROR" <?= $filter === 'ERROR' ? 'selected' : '' ?>>Solo Errores</option>
                        <option value="" <?= $filter === '' ? 'selected' : '' ?>>Todos</option>
                    </select>
                </div>
                <div class="control-group">
                    <label>Auto-refresh:</label>
                    <select name="refresh">
                        <option value="0" <?= $refresh === 0 ? 'selected' : '' ?>>Desactivado</option>
                        <option value="5" <?= $refresh === 5 ? 'selected' : '' ?>>5 segundos</option>
                        <option value="10" <?= $refresh === 10 ? 'selected' : '' ?>>10 segundos</option>
                        <option value="30" <?= $refresh === 30 ? 'selected' : '' ?>>30 segundos</option>
                    </select>
                </div>
                <button type="submit">🔄 Actualizar</button>
                <button type="button" onclick="window.location.href='?lines=<?= $lines ?>&filter=<?= $filter ?>&refresh=<?= $refresh ?>'">🔃 Refrescar</button>
                <?php if ($refresh > 0): ?>
                <span class="refresh-info">⏱️ Actualizando cada <?= $refresh ?>s</span>
                <?php endif; ?>
            </form>
        </div>

        <?php
        // Leer y procesar logs
        $logs = [];
        $stats = [
            'total' => 0,
            'success' => 0,
            'errors' => 0,
            'debug' => 0
        ];

        if (file_exists($log_file)) {
            $file_lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $file_lines = array_slice($file_lines, -$lines);
            
            foreach ($file_lines as $line) {
                if ($filter && stripos($line, $filter) === false) {
                    continue;
                }
                
                $type = 'info';
                if (stripos($line, '[CDR ERROR]') !== false) {
                    $type = 'error';
                    $stats['errors']++;
                } elseif (stripos($line, '[CDR DEBUG]') !== false) {
                    $type = 'debug';
                    $stats['debug']++;
                    if (stripos($line, 'guardado exitosamente') !== false) {
                        $type = 'success';
                        $stats['success']++;
                    }
                }
                
                $stats['total']++;
                $logs[] = ['line' => $line, 'type' => $type];
            }
        }
        ?>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">Total de Logs</div>
                <div class="stat-value stat-info"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">CDRs Guardados</div>
                <div class="stat-value stat-success"><?= $stats['success'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Errores</div>
                <div class="stat-value stat-error"><?= $stats['errors'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Debug</div>
                <div class="stat-value stat-info"><?= $stats['debug'] ?></div>
            </div>
        </div>

        <div class="log-container">
            <?php if (empty($logs)): ?>
                <div class="no-logs">
                    <p>📭 No se encontraron logs</p>
                    <p style="margin-top: 10px; font-size: 12px;">
                        <?php if ($filter): ?>
                        Intenta cambiar el filtro o aumentar el número de líneas.
                        <?php else: ?>
                        El archivo de log está vacío o no existe.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <?php foreach (array_reverse($logs) as $log): ?>
                    <div class="log-line log-<?= $log['type'] ?>">
                        <?= htmlspecialchars($log['line']) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="margin-top: 20px; text-align: center; color: #858585; font-size: 12px;">
            <p>Archivo: <?= $log_file ?></p>
            <p>Última actualización: <?= date('Y-m-d H:i:s') ?></p>
        </div>
    </div>
</body>
</html>
