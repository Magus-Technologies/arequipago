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
     * Aplica puntos por pago directo (usuario con permisos)
     */
    public function aplicarPuntosEnRegistroDirecto($idPago, $idConductor, $idCliente, $cuotasSeleccionadas)
    {
        try {
            mysqli_begin_transaction($this->conexion);

            foreach ($cuotasSeleccionadas as $cuota) {
                $idCuota = $cuota['idCuota'];
                $fechaPago = $cuota['fechaPago'] ?? date('Y-m-d');
                $fechaVencimiento = $cuota['fechaVencimiento'];

                // Verificar si el pago es puntual
                if ($fechaPago <= $fechaVencimiento) {
                    $this->aplicarPuntosPorPagoPuntual($idCuota, $idConductor, $idCliente);
                }
            }

            mysqli_commit($this->conexion);
            return ['success' => true];

        } catch (Exception $e) {
            mysqli_rollback($this->conexion);
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