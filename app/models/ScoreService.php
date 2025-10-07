<?php
require_once "app/models/PuntajeCrediticioModel.php";

class ScoreService
{
    private $conexion;
    private $puntajeModel;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->puntajeModel = new PuntajeCrediticioModel();
    }

    /**
     * Aplica puntos por pago directo (usuario con permisos) - OPTIMIZADO
     */
    public function aplicarPuntosEnRegistroDirecto($idPago, $idConductor, $idCliente, $cuotasSeleccionadas)
    {
        try {
            $tiempoInicio = microtime(true);
            error_log("🔍 [ScoreService] Inicio aplicarPuntosEnRegistroDirecto");

            // ✅ NO iniciamos transacción porque ya estamos dentro de una desde el controlador
            // mysqli_begin_transaction($this->conexion);

            // Determinar tipo de cliente
            $tipo = $idCliente ? 'cliente' : 'conductor';
            $idReferencia = $idCliente ?? $idConductor;

            error_log("🔍 [ScoreService] Tipo: $tipo, ID: $idReferencia");

            // ✅ OPTIMIZACIÓN 1: Calcular puntos UNA SOLA VEZ (antes del loop)
            $puntosASumar = $this->calcularPuntosPorPago($tipo, $idReferencia);
            error_log("🔍 [ScoreService] Puntos a sumar: $puntosASumar - Tiempo: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

            // ✅ OPTIMIZACIÓN 2: Obtener puntaje actual UNA SOLA VEZ
            $puntajeAnterior = $this->obtenerPuntajeActual($tipo, $idReferencia);
            error_log("🔍 [ScoreService] Puntaje anterior: $puntajeAnterior - Tiempo: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

            // ✅ OPTIMIZACIÓN 3: Filtrar solo cuotas puntuales y no procesadas
            $cuotasPuntuales = [];
            foreach ($cuotasSeleccionadas as $cuota) {
                $fechaPago = $cuota['fechaPago'] ?? date('Y-m-d');
                $fechaVencimiento = $cuota['fechaVencimiento'];

                if ($fechaPago <= $fechaVencimiento) {
                    $cuotasPuntuales[] = $cuota['idCuota'];
                }
            }

            // Si no hay cuotas puntuales, salir
            if (empty($cuotasPuntuales)) {
                mysqli_commit($this->conexion);
                return ['success' => true];
            }

            // ✅ OPTIMIZACIÓN 4: Verificar cuotas ya procesadas en BATCH
            error_log("🔍 [ScoreService] Verificando " . count($cuotasPuntuales) . " cuotas puntuales");

            $placeholders = implode(',', array_fill(0, count($cuotasPuntuales), '?'));
            // ✅ ARREGLO: Quitamos FOR UPDATE para evitar deadlock con FK constraint
            $sqlCheck = "SELECT idcuotas_financiamiento FROM cuotas_financiamiento
                         WHERE idcuotas_financiamiento IN ($placeholders)
                         AND puntos_aplicados = 0";

            error_log("🔍 [ScoreService] ANTES de SELECT - Tiempo: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

            $stmt = mysqli_prepare($this->conexion, $sqlCheck);
            $types = str_repeat('i', count($cuotasPuntuales));
            mysqli_stmt_bind_param($stmt, $types, ...$cuotasPuntuales);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            error_log("🔍 [ScoreService] DESPUÉS de SELECT - Tiempo: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

            $cuotasPendientes = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $cuotasPendientes[] = $row['idcuotas_financiamiento'];
            }
            mysqli_stmt_close($stmt);

            // Si no hay cuotas pendientes, salir
            if (empty($cuotasPendientes)) {
                mysqli_commit($this->conexion);
                return ['success' => true];
            }

            // ✅ OPTIMIZACIÓN 5: Calcular puntaje nuevo acumulado
            $puntosTotal = $puntosASumar * count($cuotasPendientes);
            $puntajeNuevo = min(100, $puntajeAnterior + $puntosTotal);

            // ✅ OPTIMIZACIÓN 6: Actualizar puntaje UNA SOLA VEZ
            error_log("🔍 [ScoreService] ANTES de actualizarPuntaje - Tiempo: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");
            $puntajeCrediticioId = $this->actualizarPuntaje($tipo, $idReferencia, $puntajeNuevo);
            error_log("🔍 [ScoreService] DESPUÉS de actualizarPuntaje - Tiempo: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

            // ✅ OPTIMIZACIÓN 7: Marcar todas las cuotas como procesadas en BATCH
            $placeholdersCuotas = implode(',', array_fill(0, count($cuotasPendientes), '?'));
            $sqlUpdate = "UPDATE cuotas_financiamiento
                          SET puntos_aplicados = 1
                          WHERE idcuotas_financiamiento IN ($placeholdersCuotas)";

            $stmtUpdate = mysqli_prepare($this->conexion, $sqlUpdate);
            $typesUpdate = str_repeat('i', count($cuotasPendientes));
            mysqli_stmt_bind_param($stmtUpdate, $typesUpdate, ...$cuotasPendientes);
            mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);

            // ✅ OPTIMIZACIÓN 8: Registrar historial en BATCH (UNA SOLA CONSULTA)
            $historialRegistros = [];
            $puntajeActualTemp = $puntajeAnterior;

            foreach ($cuotasPendientes as $idCuota) {
                $motivo = "Pago puntual de cuota (+" . $puntosASumar . " puntos)";
                $historialRegistros[] = [
                    'puntajeCrediticioId' => $puntajeCrediticioId,
                    'idCuota' => $idCuota,
                    'puntajeAnterior' => $puntajeActualTemp,
                    'puntajeNuevo' => $puntajeActualTemp + $puntosASumar,
                    'puntosPerdidos' => 0,
                    'motivo' => $motivo
                ];
                $puntajeActualTemp += $puntosASumar;
            }

            // Insertar todos los historiales de una vez
            error_log("🔍 [ScoreService] ANTES de registrarHistorialPuntajeBatch (" . count($historialRegistros) . " registros) - Tiempo: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");
            $this->puntajeModel->registrarHistorialPuntajeBatch($historialRegistros);
            error_log("🔍 [ScoreService] DESPUÉS de registrarHistorialPuntajeBatch - Tiempo: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

            // ✅ NO hacemos commit porque la transacción la maneja el controlador
            // error_log("🔍 [ScoreService] ANTES de commit - Tiempo: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");
            // mysqli_commit($this->conexion);
            // error_log("🔍 [ScoreService] DESPUÉS de commit - Tiempo: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

            $tiempoTotal = round((microtime(true) - $tiempoInicio) * 1000, 2);
            error_log("🔍 [ScoreService] TOTAL aplicarPuntosEnRegistroDirecto: {$tiempoTotal}ms");

            return ['success' => true];

        } catch (Exception $e) {
            // ✅ NO hacemos rollback, lo maneja el controlador
            // mysqli_rollback($this->conexion);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Aplica puntos al aprobar un pago pendiente
     */
    public function aplicarPuntosEnAprobacion($idPago)
    {
        try {
            mysqli_begin_transaction($this->conexion);

            // Obtener cuotas del pago
            $sqlCuotas = "SELECT dp.id_cuota, cf.fecha_pago, cf.fecha_vencimiento, cf.puntos_aplicados,
                                 f.id_conductor, f.id_cliente
                          FROM detalle_pago_financiamiento dp
                          INNER JOIN cuotas_financiamiento cf ON dp.id_cuota = cf.idcuotas_financiamiento
                          INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                          WHERE dp.idfinanciamiento = ?";
            
            $stmt = mysqli_prepare($this->conexion, $sqlCuotas);
            mysqli_stmt_bind_param($stmt, 'i', $idPago);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($cuota = mysqli_fetch_assoc($result)) {
                // Solo aplicar si aún no se aplicaron puntos y el pago fue puntual
                if ($cuota['puntos_aplicados'] == 0 && $cuota['fecha_pago'] <= $cuota['fecha_vencimiento']) {
                    $this->aplicarPuntosPorPagoPuntual(
                        $cuota['id_cuota'],
                        $cuota['id_conductor'],
                        $cuota['id_cliente']
                    );
                }
            }

            mysqli_stmt_close($stmt);
            mysqli_commit($this->conexion);
            return ['success' => true];

        } catch (Exception $e) {
            mysqli_rollback($this->conexion);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Lógica central para aplicar puntos por pago puntual
     */
    private function aplicarPuntosPorPagoPuntual($idCuota, $idConductor, $idCliente)
    {
        // Determinar tipo de cliente
        $tipo = $idCliente ? 'cliente' : 'conductor';
        $idReferencia = $idCliente ?? $idConductor;

        // Verificar que no se hayan aplicado puntos ya
        $sqlCheck = "SELECT puntos_aplicados FROM cuotas_financiamiento WHERE idcuotas_financiamiento = ? FOR UPDATE";
        $stmt = mysqli_prepare($this->conexion, $sqlCheck);
        mysqli_stmt_bind_param($stmt, 'i', $idCuota);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($row['puntos_aplicados'] == 1) {
            return; // Ya se aplicaron puntos, evitar duplicados
        }

        // Calcular puntos según financiamientos activos
        $puntosASumar = $this->calcularPuntosPorPago($tipo, $idReferencia);

        // Obtener puntaje actual
        $puntajeAnterior = $this->obtenerPuntajeActual($tipo, $idReferencia);
        $puntajeNuevo = min(100, $puntajeAnterior + $puntosASumar);

        // Actualizar puntaje
        $puntajeCrediticioId = $this->actualizarPuntaje($tipo, $idReferencia, $puntajeNuevo);

        // Registrar en historial
        $motivo = "Pago puntual de cuota (+" . $puntosASumar . " puntos)";
        $this->puntajeModel->registrarHistorialPuntaje(
            $puntajeCrediticioId,
            $puntajeAnterior,
            $puntajeNuevo,
            0, // No hay puntos perdidos
            $motivo,
            $idCuota
        );

        // Marcar cuota como procesada
        $sqlUpdate = "UPDATE cuotas_financiamiento SET puntos_aplicados = 1 WHERE idcuotas_financiamiento = ?";
        $stmt = mysqli_prepare($this->conexion, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'i', $idCuota);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /**
     * Calcula cuántos puntos sumar según número de financiamientos activos
     */
    public function calcularPuntosPorPago($tipo, $idReferencia)
    {
        $campoId = ($tipo === 'cliente') ? 'id_cliente' : 'id_conductor';
        
        $sqlCount = "SELECT COUNT(*) as total 
                FROM financiamiento 
                WHERE $campoId = ?";
        
        $stmt = mysqli_prepare($this->conexion, $sqlCount);
        mysqli_stmt_bind_param($stmt, 'i', $idReferencia);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $totalFinanciamientos = $row['total'];

        // Regla: 1 financiamiento = +5 puntos, más de 1 = +3 puntos
        return ($totalFinanciamientos == 1) ? 5 : 3;
    }

    private function obtenerPuntajeActual($tipo, $idReferencia)
    {
        $campoId = ($tipo === 'cliente') ? 'id_cliente' : 'id_conductor';
        
        $sql = "SELECT puntaje_actual FROM puntaje_crediticio WHERE tipo_cliente = ? AND $campoId = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $tipo, $idReferencia);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $row ? $row['puntaje_actual'] : 100;
    }

    private function actualizarPuntaje($tipo, $idReferencia, $puntajeNuevo)
    {
        $campoId = ($tipo === 'cliente') ? 'id_cliente' : 'id_conductor';
        
        $sqlUpdate = "UPDATE puntaje_crediticio 
                      SET puntaje_actual = ?, fecha_actualizacion = CURRENT_TIMESTAMP 
                      WHERE tipo_cliente = ? AND $campoId = ?";
        
        $stmt = mysqli_prepare($this->conexion, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'isi', $puntajeNuevo, $tipo, $idReferencia);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Obtener ID del registro
        $sqlId = "SELECT id FROM puntaje_crediticio WHERE tipo_cliente = ? AND $campoId = ?";
        $stmt = mysqli_prepare($this->conexion, $sqlId);
        mysqli_stmt_bind_param($stmt, 'si', $tipo, $idReferencia);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $row['id'];
    }
}