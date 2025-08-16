<?php
/**
 * Script de inicialización de puntajes crediticios - VERSIÓN CON SALIDA EN PANTALLA
 * Este script debe ejecutarse una sola vez para poblar la tabla puntaje_crediticio
 * con todos los clientes y conductores existentes
 */

require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../utils/config.php";
require_once dirname(__FILE__) . '/../app/models/PuntajeCrediticioModel.php';

class InicializadorPuntajes
{
    private $conexion;
    private $puntajeModel;
    private $logFile;
    private $isWeb;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->puntajeModel = new PuntajeCrediticioModel();
        $this->isWeb = (php_sapi_name() !== 'cli');
        
        // Solo crear logs si estamos en CLI
        if (!$this->isWeb) {
            $this->logFile = dirname(__FILE__) . '/../../logs/inicializacion_puntajes_' . date('Y-m-d_H-i-s') . '.log';
            $logDir = dirname($this->logFile);
            if (!file_exists($logDir)) {
                mkdir($logDir, 0755, true);
            }
        }
    }

    public function ejecutar()
    {
        $this->mostrar("=== INICIANDO PROCESO DE INICIALIZACIÓN DE PUNTAJES CREDITICIOS ===", 'h2');
        $this->mostrar("Fecha y hora: " . date('Y-m-d H:i:s'), 'info');
        
        $totalProcesados = 0;
        $totalErrores = 0;

        try {
            // Procesar clientes
            $this->mostrar("--- PROCESANDO CLIENTES ---", 'h3');
            $resultadoClientes = $this->procesarClientes();
            $totalProcesados += $resultadoClientes['procesados'];
            $totalErrores += $resultadoClientes['errores'];

            // Mostrar resumen de clientes
            $this->mostrar("✅ Clientes completados: {$resultadoClientes['procesados']} procesados, {$resultadoClientes['errores']} errores", 'success');

            // Procesar conductores
            $this->mostrar("--- PROCESANDO CONDUCTORES ---", 'h3');
            $resultadoConductores = $this->procesarConductores();
            $totalProcesados += $resultadoConductores['procesados'];
            $totalErrores += $resultadoConductores['errores'];

            // Mostrar resumen de conductores
            $this->mostrar("✅ Conductores completados: {$resultadoConductores['procesados']} procesados, {$resultadoConductores['errores']} errores", 'success');

            // Generar historial inicial
            $this->mostrar("--- GENERANDO HISTORIAL INICIAL ---", 'h3');
            $this->generarHistorialInicial();

            $this->mostrar("=== PROCESO COMPLETADO ===", 'h2');
            $this->mostrar("Total procesados: $totalProcesados", 'success');
            $this->mostrar("Total errores: $totalErrores", ($totalErrores > 0 ? 'error' : 'success'));
            
            // Mostrar estadísticas finales
            $this->mostrarEstadisticasFinales();

        } catch (Exception $e) {
            $this->mostrar("ERROR CRÍTICO: " . $e->getMessage(), 'error');
            throw $e;
        }

        return [
            'procesados' => $totalProcesados,
            'errores' => $totalErrores,
            'log_file' => $this->logFile ?? 'N/A (ejecución web)'
        ];
    }

    private function procesarClientes()
    {
        $procesados = 0;
        $errores = 0;

        try {
            // Obtener todos los clientes
            $sql = "SELECT id, nombres, apellido_paterno, apellido_materno FROM clientes_financiar ORDER BY id";
            $result = mysqli_query($this->conexion, $sql);
            
            $totalClientes = mysqli_num_rows($result);
            $this->mostrar("Total clientes a procesar: $totalClientes", 'info');

            while ($cliente = mysqli_fetch_assoc($result)) {
                try {
                    $this->mostrar("Procesando cliente ID {$cliente['id']}: {$cliente['nombres']} {$cliente['apellido_paterno']}", 'processing');

                    // Calcular puntaje
                    $puntajeData = $this->puntajeModel->calcularPuntajeIndividual('cliente', $cliente['id']);
                    
                    // Mostrar datos calculados
                    $this->mostrar("  📊 Datos calculados: Puntaje={$puntajeData['puntaje']}, Financiamientos={$puntajeData['total_financiamientos']}, Retrasos={$puntajeData['total_retrasos']}", 'data');
                    
                    // Crear registro de puntaje crediticio
                    $puntajeCrediticioId = $this->puntajeModel->actualizarPuntajeCrediticio('cliente', $cliente['id'], $puntajeData);
                    $this->mostrar("  💾 Registro creado con ID: $puntajeCrediticioId", 'data');

                    // Registrar en historial como inicialización
                    $this->puntajeModel->registrarHistorialPuntaje(
                        $puntajeCrediticioId,
                        100, // Puntaje inicial por defecto
                        $puntajeData['puntaje'],
                        0,
                        "Inicialización del sistema de puntaje crediticio"
                    );

                    $procesados++;
                    $this->mostrar("  ✅ COMPLETADO", 'success');

                } catch (Exception $e) {
                    $errores++;
                    $this->mostrar("  ❌ ERROR: " . $e->getMessage(), 'error');
                }
                
                // Flush para mostrar en tiempo real
                if ($this->isWeb) {
                    echo str_repeat(' ', 4096); // Buffer para algunos navegadores
                    flush();
                    ob_flush();
                }
            }

        } catch (Exception $e) {
            $this->mostrar("ERROR en procesamiento de clientes: " . $e->getMessage(), 'error');
            throw $e;
        }

        return ['procesados' => $procesados, 'errores' => $errores];
    }

    private function procesarConductores()
    {
        $procesados = 0;
        $errores = 0;

        try {
            // Obtener todos los conductores activos
            $sql = "SELECT id_conductor, nombres, apellido_paterno, apellido_materno 
                    FROM conductores 
                    WHERE desvinculado = 0 
                    ORDER BY id_conductor";
            $result = mysqli_query($this->conexion, $sql);
            
            $totalConductores = mysqli_num_rows($result);
            $this->mostrar("Total conductores a procesar: $totalConductores", 'info');

            while ($conductor = mysqli_fetch_assoc($result)) {
                try {
                    $this->mostrar("Procesando conductor ID {$conductor['id_conductor']}: {$conductor['nombres']} {$conductor['apellido_paterno']}", 'processing');

                    // Calcular puntaje
                    $puntajeData = $this->puntajeModel->calcularPuntajeIndividual('conductor', $conductor['id_conductor']);
                    
                    // Mostrar datos calculados
                    $this->mostrar("  📊 Datos calculados: Puntaje={$puntajeData['puntaje']}, Financiamientos={$puntajeData['total_financiamientos']}, Retrasos={$puntajeData['total_retrasos']}", 'data');
                    
                    // Crear registro de puntaje crediticio
                    $puntajeCrediticioId = $this->puntajeModel->actualizarPuntajeCrediticio('conductor', $conductor['id_conductor'], $puntajeData);
                    $this->mostrar("  💾 Registro creado con ID: $puntajeCrediticioId", 'data');

                    // Registrar en historial como inicialización
                    $this->puntajeModel->registrarHistorialPuntaje(
                        $puntajeCrediticioId,
                        100, // Puntaje inicial por defecto
                        $puntajeData['puntaje'],
                        0,
                        "Inicialización del sistema de puntaje crediticio"
                    );

                    $procesados++;
                    $this->mostrar("  ✅ COMPLETADO", 'success');

                } catch (Exception $e) {
                    $errores++;
                    $this->mostrar("  ❌ ERROR: " . $e->getMessage(), 'error');
                }
                
                // Flush para mostrar en tiempo real
                if ($this->isWeb) {
                    echo str_repeat(' ', 4096);
                    flush();
                    ob_flush();
                }
            }

        } catch (Exception $e) {
            $this->mostrar("ERROR en procesamiento de conductores: " . $e->getMessage(), 'error');
            throw $e;
        }

        return ['procesados' => $procesados, 'errores' => $errores];
    }

    // Encuentra esta función y reemplázala completa:
    // private function generarHistorialInicial()

    private function generarHistorialInicial()
    {
        try {
            $this->mostrar("Generando historial detallado para cuotas pagadas...", 'info');

            // Obtener todas las cuotas pagadas ordenadas por fecha de pago
            $sql = "SELECT 
                        cf.idcuotas_financiamiento,
                        cf.numero_cuota,
                        cf.monto,
                        cf.fecha_vencimiento,
                        cf.fecha_pago,
                        f.id_cliente,
                        f.id_conductor,
                        CASE 
                            WHEN f.id_cliente IS NOT NULL THEN 'cliente'
                            ELSE 'conductor'
                        END as tipo_cliente,
                        CASE 
                            WHEN f.id_cliente IS NOT NULL THEN f.id_cliente
                            ELSE f.id_conductor
                        END as id_referencia
                    FROM cuotas_financiamiento cf
                    INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                    WHERE cf.fecha_pago IS NOT NULL
                    ORDER BY cf.fecha_pago, cf.idcuotas_financiamiento";

            $result = mysqli_query($this->conexion, $sql);
            $totalCuotas = mysqli_num_rows($result);
            $this->mostrar("Total cuotas con historial de pago: $totalCuotas", 'info');

            // Agrupar cuotas por cliente/conductor para calcular puntaje acumulativo
            $cuotasPorCliente = [];
            while ($cuota = mysqli_fetch_assoc($result)) {
                $key = $cuota['tipo_cliente'] . '_' . $cuota['id_referencia'];
                if (!isset($cuotasPorCliente[$key])) {
                    $cuotasPorCliente[$key] = [
                        'tipo_cliente' => $cuota['tipo_cliente'],
                        'id_referencia' => $cuota['id_referencia'],
                        'puntaje_actual' => 100, // Empezar con 100 puntos
                        'cuotas' => []
                    ];
                }
                $cuotasPorCliente[$key]['cuotas'][] = $cuota;
            }

            $procesadas = 0;
            
            // Procesar cada cliente/conductor
            foreach ($cuotasPorCliente as $clienteData) {
                // Obtener ID de puntaje crediticio
                $campoId = ($clienteData['tipo_cliente'] === 'cliente') ? 'id_cliente' : 'id_conductor';
                $sqlPuntajeId = "SELECT id FROM puntaje_crediticio WHERE tipo_cliente = ? AND $campoId = ?";
                $stmt = mysqli_prepare($this->conexion, $sqlPuntajeId);
                mysqli_stmt_bind_param($stmt, 'si', $clienteData['tipo_cliente'], $clienteData['id_referencia']);
                mysqli_stmt_execute($stmt);
                $result2 = mysqli_stmt_get_result($stmt);
                $puntajeRow = mysqli_fetch_assoc($result2);
                mysqli_stmt_close($stmt);

                if (!$puntajeRow) continue;

                $puntajeActual = $clienteData['puntaje_actual'];
                
                // Procesar cuotas ordenadas por fecha
                foreach ($clienteData['cuotas'] as $cuota) {
                    try {
                        $puntajeAnterior = $puntajeActual;
                        $puntosPerdidos = 0;
                        
                        // Solo penalizar si hay retraso
                        if ($cuota['fecha_pago'] > $cuota['fecha_vencimiento']) {
                            $puntosPerdidos = $this->calcularPuntosPerdidos($cuota['id_referencia'], $cuota['tipo_cliente']);
                            $puntajeActual = max(0, $puntajeActual - $puntosPerdidos);
                        }

                        $motivo = $this->determinarMotivo($cuota);

                        // Registrar evento histórico con puntajes correctos
                        $this->puntajeModel->registrarHistorialPuntaje(
                            $puntajeRow['id'],
                            $puntajeAnterior, // Puntaje real anterior
                            $puntajeActual,   // Puntaje real nuevo
                            $puntosPerdidos,
                            $motivo,
                            $cuota['idcuotas_financiamiento']
                        );

                        $procesadas++;

                    } catch (Exception $e) {
                        $this->mostrar("Error procesando cuota {$cuota['idcuotas_financiamiento']}: " . $e->getMessage(), 'error');
                    }
                }
                
                // Mostrar progreso cada 10 clientes
                if (($procesadas / 10) % 1 == 0) {
                    $this->mostrar("Procesadas $procesadas/$totalCuotas cuotas...", 'progress');
                }
            }

            $this->mostrar("✅ Historial generado para $procesadas cuotas", 'success');

        } catch (Exception $e) {
            $this->mostrar("ERROR generando historial inicial: " . $e->getMessage(), 'error');
        }
    }

    private function determinarMotivo($cuota)
    {
        if ($cuota['fecha_pago'] <= $cuota['fecha_vencimiento']) {
            return "Cuota #{$cuota['numero_cuota']} pagada puntualmente";
        } else {
            $diasRetraso = (strtotime($cuota['fecha_pago']) - strtotime($cuota['fecha_vencimiento'])) / (60 * 60 * 24);
            return "Cuota #{$cuota['numero_cuota']} pagada con {$diasRetraso} días de retraso";
        }
    }

    private function calcularPuntosPerdidos($idReferencia, $tipoCliente)
    {
        // Obtener número total de financiamientos para determinar puntos por retraso
        $campoId = ($tipoCliente === 'cliente') ? 'id_cliente' : 'id_conductor';
        $sql = "SELECT COUNT(*) as total FROM financiamiento WHERE $campoId = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $idReferencia);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return ($row['total'] == 1) ? 5 : 3; // Reglas de negocio
    }

    private function mostrarEstadisticasFinales()
    {
        try {
            $this->mostrar("=== ESTADÍSTICAS FINALES ===", 'h3');
            
            // Estadísticas básicas
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN tipo_cliente = 'cliente' THEN 1 ELSE 0 END) as total_clientes,
                        SUM(CASE WHEN tipo_cliente = 'conductor' THEN 1 ELSE 0 END) as total_conductores,
                        AVG(puntaje_actual) as promedio_general,
                        SUM(CASE WHEN puntaje_actual < 50 THEN 1 ELSE 0 END) as en_riesgo
                    FROM puntaje_crediticio";
            $result = mysqli_query($this->conexion, $sql);
            $stats = mysqli_fetch_assoc($result);
            
            $this->mostrar("📊 Total registros creados: {$stats['total']}", 'info');
            $this->mostrar("👥 Total clientes: {$stats['total_clientes']}", 'info');
            $this->mostrar("🚗 Total conductores: {$stats['total_conductores']}", 'info');
            $this->mostrar("📈 Promedio general: " . round($stats['promedio_general'], 2), 'info');
            $this->mostrar("⚠️ En riesgo (<50): {$stats['en_riesgo']}", ($stats['en_riesgo'] > 0 ? 'warning' : 'info'));

        } catch (Exception $e) {
            $this->mostrar("Error obteniendo estadísticas finales: " . $e->getMessage(), 'error');
        }
    }

    private function mostrar($mensaje, $tipo = 'normal')
    {
        $timestamp = date('H:i:s');
        
        if ($this->isWeb) {
            // Salida para navegador con colores
            switch ($tipo) {
                case 'h2':
                    echo "<h2 style='color: #0066cc; margin: 20px 0 10px 0;'>$mensaje</h2>";
                    break;
                case 'h3':
                    echo "<h3 style='color: #0088cc; margin: 15px 0 8px 0;'>$mensaje</h3>";
                    break;
                case 'success':
                    echo "<div style='color: #28a745; font-weight: bold;'>[$timestamp] ✅ $mensaje</div>";
                    break;
                case 'error':
                    echo "<div style='color: #dc3545; font-weight: bold;'>[$timestamp] ❌ $mensaje</div>";
                    break;
                case 'warning':
                    echo "<div style='color: #ffc107; font-weight: bold;'>[$timestamp] ⚠️ $mensaje</div>";
                    break;
                case 'processing':
                    echo "<div style='color: #6f42c1; margin-left: 20px;'>[$timestamp] 🔄 $mensaje</div>";
                    break;
                case 'data':
                    echo "<div style='color: #17a2b8; margin-left: 40px; font-size: 0.9em;'>$mensaje</div>";
                    break;
                case 'info':
                    echo "<div style='color: #17a2b8; font-weight: bold;'>[$timestamp] ℹ️ $mensaje</div>";
                    break;
                case 'progress':
                    echo "<div style='color: #28a745;'>[$timestamp] 📋 $mensaje</div>";
                    break;
                default:
                    echo "<div>[$timestamp] $mensaje</div>";
            }
            echo "<br>";
        } else {
            // Salida para CLI
            echo "[$timestamp] $mensaje\n";
        }
        
        // También escribir a log si existe
        if (!$this->isWeb && $this->logFile) {
            $logMessage = "[$timestamp] $mensaje\n";
            file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
        }
    }
}

// Ejecutar script
if (php_sapi_name() === 'cli') {
    // Ejecución desde línea de comandos
    try {
        $inicializador = new InicializadorPuntajes();
        $resultado = $inicializador->ejecutar();
        
        echo "\n=== PROCESO COMPLETADO ===\n";
        echo "Procesados: {$resultado['procesados']}\n";
        echo "Errores: {$resultado['errores']}\n";
        echo "Log guardado en: {$resultado['log_file']}\n";
        
        exit(0);
        
    } catch (Exception $e) {
        echo "ERROR CRÍTICO: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    // Ejecución desde web (con protección)
    if (!isset($_GET['execute']) || $_GET['execute'] !== 'true') {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Inicialización de Puntajes Crediticios</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
                .btn:hover { background: #0056b3; }
                .danger { background: #f8d7da; border: 1px solid #f5c6cb; }
            </style>
        </head>
        <body>
            <h1>Script de Inicialización de Puntajes Crediticios - VERSIÓN MEJORADA</h1>
            
            <div class="warning danger">
                <h3>⚠️ ADVERTENCIA IMPORTANTE</h3>
                <p>Este script debe ejecutarse <strong>UNA SOLA VEZ</strong> para inicializar el sistema de puntajes crediticios.</p>
                <p>El proceso puede tomar varios minutos dependiendo de la cantidad de clientes y conductores.</p>
                <p><strong>NUEVA CARACTERÍSTICA:</strong> Verás el progreso detallado en tiempo real en el navegador.</p>
            </div>

            <div class="warning">
                <h3>✨ MEJORAS EN ESTA VERSIÓN:</h3>
                <ul>
                    <li>✅ <strong>Salida en tiempo real</strong> - Verás cada cliente/conductor procesándose</li>
                    <li>📊 <strong>Datos detallados</strong> - Muestra puntajes y financiamientos calculados</li>
                    <li>🎨 <strong>Colores y emojis</strong> - Fácil identificación de éxitos/errores</li>
                    <li>📈 <strong>Estadísticas finales</strong> - Resumen completo al terminar</li>
                    <li>⚡ <strong>Mejor depuración</strong> - Podrás ver exactamente dónde falla</li>
                </ul>
            </div>

            <div style="margin: 30px 0;">
                <button class="btn" onclick="if(confirm('¿Está seguro que desea ejecutar la inicialización?\n\n¡IMPORTANTE! Asegúrese de haber vaciado las tablas puntaje_crediticio e historial_puntaje antes de continuar.')) window.location.href='?execute=true'">
                    🚀 Ejecutar Inicialización Mejorada
                </button>
            </div>

            <h3>Ejecución desde línea de comandos:</h3>
            <code>php <?php echo basename(__FILE__); ?></code>
        </body>
        </html>
        <?php
        exit;
    }

    // Ejecutar desde web con salida mejorada
    try {
        set_time_limit(0); // Sin límite de tiempo
        ini_set('memory_limit', '512M'); // Aumentar memoria
        
        // Configurar para salida en tiempo real
        if (ob_get_level()) {
            ob_end_clean();
        }
        ob_implicit_flush(true);

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Ejecutando Inicialización...</title>
            <meta charset="UTF-8">
            <style>
                body { 
                    font-family: 'Courier New', monospace; 
                    margin: 20px; 
                    background: #f8f9fa; 
                    line-height: 1.4;
                }
                .container { 
                    background: white; 
                    padding: 20px; 
                    border-radius: 8px; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1 style="color: #0066cc;">🚀 Ejecutando Inicialización de Puntajes Crediticios</h1>
                <div id="output">
        <?php
        
        $inicializador = new InicializadorPuntajes();
        $resultado = $inicializador->ejecutar();
        
        ?>
                </div>
                <hr>
                <h2 style="color: #28a745;">✅ PROCESO COMPLETADO EXITOSAMENTE</h2>
                <p><strong>Procesados:</strong> <?php echo $resultado['procesados']; ?></p>
                <p><strong>Errores:</strong> <?php echo $resultado['errores']; ?></p>
                <p><a href="?">🔄 Volver</a></p>
            </div>
        </body>
        </html>
        <?php
        
    } catch (Exception $e) {
        echo "<h2 style='color: #dc3545;'>❌ ERROR CRÍTICO:</h2>";
        echo "<p style='color: #dc3545; font-weight: bold;'>" . $e->getMessage() . "</p>";
        echo "<p><a href='?'>🔄 Volver</a></p>";
        echo "</div></body></html>";
    }
}
?>