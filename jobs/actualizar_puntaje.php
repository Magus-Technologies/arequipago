<?php
/**
 * Script de actualización masiva de puntajes crediticios
 * Ejecuta la lógica del modelo PuntajeCrediticioModel
 */

require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../utils/config.php";
require_once dirname(__FILE__) . '/../app/models/PuntajeCrediticioModel.php';

try {
    echo "Iniciando actualización de puntajes crediticios...\n";
    
    // Crear instancia del modelo
    $puntajeModel = new PuntajeCrediticioModel();
    
    // Ejecutar actualización masiva (esto llama internamente a todos los métodos necesarios)
    $totalActualizados = $puntajeModel->actualizarTodosPuntajes();
    
    echo "✓ Actualización completada\n";
    echo "Total de puntajes actualizados: {$totalActualizados}\n";
    echo "Fecha: " . date('Y-m-d H:i:s') . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

?>