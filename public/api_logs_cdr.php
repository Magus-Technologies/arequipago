<?php
/**
 * API de Logs CDR
 * Retorna logs en formato JSON para monitoreo externo
 */

header('Content-Type: application/json');

// Seguridad: Solo usuarios autenticados
session_start();
if (!isset($_SESSION['usuario_fac'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Solo administradores
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] > 3) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

$log_file = __DIR__ . '/../error_log.log';
$lines = isset($_GET['lines']) ? intval($_GET['lines']) : 50;
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'CDR';

$response = [
    'success' => false,
    'timestamp' => date('Y-m-d H:i:s'),
    'logs' => [],
    'stats' => [
        'total' => 0,
        'success' => 0,
        'errors' => 0,
        'debug' => 0
    ]
];

if (!file_exists($log_file)) {
    $response['error'] = 'Archivo de log no encontrado';
    echo json_encode($response);
    exit;
}

try {
    $file_lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $file_lines = array_slice($file_lines, -$lines);
    
    foreach ($file_lines as $line) {
        if ($filter && stripos($line, $filter) === false) {
            continue;
        }
        
        $type = 'info';
        $level = 'INFO';
        
        if (stripos($line, '[CDR ERROR]') !== false) {
            $type = 'error';
            $level = 'ERROR';
            $response['stats']['errors']++;
        } elseif (stripos($line, '[CDR DEBUG]') !== false) {
            $type = 'debug';
            $level = 'DEBUG';
            $response['stats']['debug']++;
            
            if (stripos($line, 'guardado exitosamente') !== false) {
                $type = 'success';
                $level = 'SUCCESS';
                $response['stats']['success']++;
            }
        }
        
        // Extraer timestamp si existe
        $timestamp = null;
        if (preg_match('/\[(.*?)\]/', $line, $matches)) {
            $timestamp = $matches[1];
        }
        
        $response['stats']['total']++;
        $response['logs'][] = [
            'message' => $line,
            'type' => $type,
            'level' => $level,
            'timestamp' => $timestamp
        ];
    }
    
    $response['success'] = true;
    $response['logs'] = array_reverse($response['logs']);
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
