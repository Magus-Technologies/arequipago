<?php

class PuntajeCrediticioModel
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    // Obtener estadísticas generales
    public function obtenerEstadisticasGenerales()
    {
        try {
            $stats = [];

            // Total clientes
            $sqlClientes = "SELECT COUNT(*) as total FROM puntaje_crediticio WHERE tipo_cliente = 'cliente'";
            $result = mysqli_query($this->conexion, $sqlClientes);
            $stats['totalClientes'] = mysqli_fetch_assoc($result)['total'];

            // Total conductores
            $sqlConductores = "SELECT COUNT(*) as total FROM puntaje_crediticio WHERE tipo_cliente = 'conductor'";
            $result = mysqli_query($this->conexion, $sqlConductores);
            $stats['totalConductores'] = mysqli_fetch_assoc($result)['total'];

            // Promedio general
            $sqlPromedio = "SELECT AVG(puntaje_actual) as promedio FROM puntaje_crediticio";
            $result = mysqli_query($this->conexion, $sqlPromedio);
            $stats['promedioGeneral'] = round(mysqli_fetch_assoc($result)['promedio'], 0);

            // Clientes en riesgo (menos de 50 puntos)
            $sqlRiesgo = "SELECT COUNT(*) as total FROM puntaje_crediticio WHERE puntaje_actual < 50";
            $result = mysqli_query($this->conexion, $sqlRiesgo);
            $stats['clientesRiesgo'] = mysqli_fetch_assoc($result)['total'];

            return $stats;
        } catch (Exception $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }

    public function obtenerClientesPuntaje($filtros, $pagina, $limite)
    {
        try {
            $offset = ($pagina - 1) * $limite;
            
            $whereConditions = [];
            $whereValues = [];

            // Construir condiciones WHERE
            if (!empty($filtros['tipo']) && $filtros['tipo'] !== 'todos') {
                $whereConditions[] = "pc.tipo_cliente = ?";
                $whereValues[] = $filtros['tipo'];
            }

            if (!empty($filtros['busqueda'])) {
                $whereConditions[] = "(
                    (pc.tipo_cliente = 'cliente' AND CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno) LIKE ?) OR
                    (pc.tipo_cliente = 'conductor' AND CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) LIKE ?)
                )";
                $searchTerm = '%' . $filtros['busqueda'] . '%';
                $whereValues[] = $searchTerm;
                $whereValues[] = $searchTerm;
            }

            if (!empty($filtros['rango'])) {
                switch ($filtros['rango']) {
                    case 'excelente':
                        $whereConditions[] = "pc.puntaje_actual BETWEEN 76 AND 100";
                        break;
                    case 'bueno':
                        $whereConditions[] = "pc.puntaje_actual BETWEEN 51 AND 75";
                        break;
                    case 'regular':
                        $whereConditions[] = "pc.puntaje_actual BETWEEN 26 AND 50";
                        break;
                    case 'malo':
                        $whereConditions[] = "pc.puntaje_actual BETWEEN 0 AND 25";
                        break;
                }
            }

            if (!empty($filtros['fecha'])) {
                $whereConditions[] = "DATE(pc.fecha_actualizacion) = ?";
                $whereValues[] = $filtros['fecha'];
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            // Query principal
            $sql = "SELECT 
                        pc.*,
                        CASE 
                            WHEN pc.tipo_cliente = 'cliente' THEN cf.nombres
                            ELSE c.nombres 
                        END as nombres,
                        CASE 
                            WHEN pc.tipo_cliente = 'cliente' THEN cf.apellido_paterno
                            ELSE c.apellido_paterno 
                        END as apellido_paterno,
                        CASE 
                            WHEN pc.tipo_cliente = 'cliente' THEN cf.apellido_materno
                            ELSE c.apellido_materno 
                        END as apellido_materno,
                        CASE 
                            WHEN pc.tipo_cliente = 'cliente' THEN cf.tipo_doc
                            ELSE c.tipo_doc 
                        END as tipo_doc,
                        CASE 
                            WHEN pc.tipo_cliente = 'cliente' THEN cf.n_documento
                            ELSE c.nro_documento 
                        END as numero_documento,
                        CASE 
                            WHEN pc.tipo_cliente = 'cliente' THEN cf.telefono
                            ELSE c.telefono 
                        END as telefono,
                        CASE 
                            WHEN pc.tipo_cliente = 'cliente' THEN pc.id_cliente
                            ELSE pc.id_conductor 
                        END as id_referencia
                    FROM puntaje_crediticio pc
                    LEFT JOIN clientes_financiar cf ON pc.id_cliente = cf.id AND pc.tipo_cliente = 'cliente'
                    LEFT JOIN conductores c ON pc.id_conductor = c.id_conductor AND pc.tipo_cliente = 'conductor'
                    $whereClause
                    ORDER BY pc.puntaje_actual DESC, pc.fecha_actualizacion DESC
                    LIMIT ? OFFSET ?";

            $whereValues[] = $limite;
            $whereValues[] = $offset;

            $stmt = mysqli_prepare($this->conexion, $sql);
            if ($stmt) {
                if (!empty($whereValues)) {
                    $types = str_repeat('s', count($whereValues) - 2) . 'ii';
                    mysqli_stmt_bind_param($stmt, $types, ...$whereValues);
                }
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $clientes = mysqli_fetch_all($result, MYSQLI_ASSOC);
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error al preparar consulta");
            }

            // REEMPLÁZALAS POR:
            $sqlCount = "SELECT COUNT(*) as total 
                        FROM puntaje_crediticio pc
                        LEFT JOIN clientes_financiar cf ON pc.id_cliente = cf.id AND pc.tipo_cliente = 'cliente'
                        LEFT JOIN conductores c ON pc.id_conductor = c.id_conductor AND pc.tipo_cliente = 'conductor'
                        $whereClause";
            
            // Remover los últimos dos elementos (limite y offset) de los valores
            $countValues = array_slice($whereValues, 0, -2);

            // REEMPLAZA POR:
            if (!empty($countValues)) {
                $stmtCount = mysqli_prepare($this->conexion, $sqlCount);
                $types = str_repeat('s', count($countValues));
                mysqli_stmt_bind_param($stmtCount, $types, ...$countValues);
                mysqli_stmt_execute($stmtCount);
                $resultCount = mysqli_stmt_get_result($stmtCount);
                $rowCount = mysqli_fetch_assoc($resultCount);
                $totalRegistros = $rowCount['total'];
                mysqli_stmt_close($stmtCount);
            } else {
                $resultCount = mysqli_query($this->conexion, $sqlCount);
                $rowCount = mysqli_fetch_assoc($resultCount);
                $totalRegistros = $rowCount['total'];
            }

            $totalPaginas = ceil($totalRegistros / $limite);

            return [
                'clientes' => $clientes,
                'totalPaginas' => $totalPaginas,
                'totalRegistros' => $totalRegistros
            ];

        } catch (Exception $e) {
            throw new Exception("Error al obtener clientes: " . $e->getMessage());
        }
    }

    // Obtener detalle de un cliente específico
    public function obtenerDetalleCliente($tipo, $id)
    {
        try {
            $data = [];

            // Obtener información del cliente
            if ($tipo === 'cliente') {
                $sqlCliente = "SELECT * FROM clientes_financiar WHERE id = ?";
            } else {
                $sqlCliente = "SELECT * FROM conductores WHERE id_conductor = ?";
            }

            $stmt = mysqli_prepare($this->conexion, $sqlCliente);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $data['cliente'] = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            // Obtener puntaje crediticio
            $sqlPuntaje = "SELECT * FROM puntaje_crediticio WHERE tipo_cliente = ? AND " . 
                         ($tipo === 'cliente' ? 'id_cliente' : 'id_conductor') . " = ?";
            
            $stmt = mysqli_prepare($this->conexion, $sqlPuntaje);
            mysqli_stmt_bind_param($stmt, 'si', $tipo, $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $data['puntaje'] = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            // Obtener financiamientos activos
            $sqlFinanciamientos = "SELECT f.*, p.nombre as nombre_producto 
                                  FROM financiamiento f 
                                  LEFT JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                                  WHERE " . ($tipo === 'cliente' ? 'f.id_cliente' : 'f.id_conductor') . " = ?
                                  AND f.estado IN ('activo', 'vigente', 'en_proceso')
                                  ORDER BY f.fecha_inicio DESC";

            $stmt = mysqli_prepare($this->conexion, $sqlFinanciamientos);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $data['financiamientos'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_stmt_close($stmt);

            return $data;

        } catch (Exception $e) {
            throw new Exception("Error al obtener detalle del cliente: " . $e->getMessage());
        }
    }

    // Obtener historial de puntaje crediticio
    public function obtenerHistorialPuntaje($tipo, $id, $filtros = [])
    {
        try {
            // Primero obtener el ID del puntaje crediticio
            $sqlPuntajeId = "SELECT id FROM puntaje_crediticio WHERE tipo_cliente = ? AND " . 
                           ($tipo === 'cliente' ? 'id_cliente' : 'id_conductor') . " = ?";
            
            $stmt = mysqli_prepare($this->conexion, $sqlPuntajeId);
            mysqli_stmt_bind_param($stmt, 'si', $tipo, $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $puntajeRow = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (!$puntajeRow) {
                return ['historial' => []];
            }

            $puntajeId = $puntajeRow['id'];

            // Construir condiciones para el historial
            $whereConditions = ["hp.id_puntaje_crediticio = ?"];
            $whereValues = [$puntajeId];

            if (!empty($filtros['mes'])) {
                $whereConditions[] = "DATE_FORMAT(hp.fecha_evento, '%Y-%m') = ?";
                $whereValues[] = $filtros['mes'];
            }

            if (!empty($filtros['estado'])) {
                // Determinar estado basado en los puntos perdidos y motivo
                switch ($filtros['estado']) {
                    case 'puntual':
                        $whereConditions[] = "hp.puntos_perdidos = 0";
                        break;
                    case 'retraso':
                        $whereConditions[] = "hp.puntos_perdidos > 0 AND hp.motivo LIKE '%retraso%'";
                        break;
                    case 'vencido':
                        $whereConditions[] = "hp.motivo LIKE '%vencido%'";
                        break;
                }
            }

            $whereClause = implode(' AND ', $whereConditions);

            $sqlHistorial = "SELECT 
                                hp.*,
                                cf.numero_cuota,
                                cf.monto as monto_cuota,
                                CASE 
                                    WHEN hp.puntos_perdidos = 0 THEN 'puntual'
                                    WHEN hp.motivo LIKE '%vencido%' THEN 'vencido'
                                    ELSE 'retraso'
                                END as estado_cuota
                            FROM historial_puntaje hp
                            LEFT JOIN cuotas_financiamiento cf ON hp.id_cuota = cf.idcuotas_financiamiento
                            WHERE $whereClause
                            ORDER BY hp.fecha_evento DESC";

            $stmt = mysqli_prepare($this->conexion, $sqlHistorial);
            if (!empty($whereValues)) {
                $types = str_repeat('s', count($whereValues));
                mysqli_stmt_bind_param($stmt, $types, ...$whereValues);
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $historial = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_stmt_close($stmt);

            return ['historial' => $historial];

        } catch (Exception $e) {
            throw new Exception("Error al obtener historial: " . $e->getMessage());
        }
    }

    // Calcular puntaje crediticio para un cliente/conductor específico
    public function calcularPuntajeIndividual($tipo, $id)
    {
        try {
            // Obtener todos los financiamientos del cliente/conductor
            $campoId = ($tipo === 'cliente') ? 'id_cliente' : 'id_conductor';
            
            $sqlFinanciamientos = "SELECT COUNT(*) as total_financiamientos 
                                  FROM financiamiento 
                                  WHERE $campoId = ? AND estado IN ('En progreso', 'Finalizado')";
            
            $stmt = mysqli_prepare($this->conexion, $sqlFinanciamientos);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $financiamientoInfo = mysqli_fetch_assoc($result);
            $totalFinanciamientos = $financiamientoInfo['total_financiamientos'];
            mysqli_stmt_close($stmt);

            if ($totalFinanciamientos == 0) {
                return [
                    'puntaje' => 100,
                    'total_financiamientos' => 0,
                    'total_retrasos' => 0
                ];
            }

            // Obtener cuotas con retraso
            $sqlRetrasos = "SELECT COUNT(*) as total_retrasos
                           FROM cuotas_financiamiento cf
                           INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                           WHERE f.$campoId = ?
                           AND cf.fecha_pago > cf.fecha_vencimiento
                           AND cf.fecha_pago IS NOT NULL";

            $stmt = mysqli_prepare($this->conexion, $sqlRetrasos);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $retrasoInfo = mysqli_fetch_assoc($result);
            $totalRetrasos = $retrasoInfo['total_retrasos'];
            mysqli_stmt_close($stmt);

            // Calcular puntaje según las reglas
            $puntajeBase = 100;
            $puntosPorRetraso = ($totalFinanciamientos == 1) ? 5 : 3;
            $puntosPerdidos = $totalRetrasos * $puntosPorRetraso;
            $puntajeFinal = max(0, $puntajeBase - $puntosPerdidos);

            return [
                'puntaje' => $puntajeFinal,
                'total_financiamientos' => $totalFinanciamientos,
                'total_retrasos' => $totalRetrasos
            ];

        } catch (Exception $e) {
            throw new Exception("Error al calcular puntaje individual: " . $e->getMessage());
        }
    }

    // Actualizar o crear puntaje crediticio
    public function actualizarPuntajeCrediticio($tipo, $id, $puntajeData)
    {
        try {
            $campoId = ($tipo === 'cliente') ? 'id_cliente' : 'id_conductor';
            
            // Verificar si ya existe un registro
            $sqlExiste = "SELECT id, puntaje_actual FROM puntaje_crediticio WHERE tipo_cliente = ? AND $campoId = ?";
            $stmt = mysqli_prepare($this->conexion, $sqlExiste);
            mysqli_stmt_bind_param($stmt, 'si', $tipo, $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $existeRegistro = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($existeRegistro) {
                // Actualizar registro existente
                $sqlUpdate = "UPDATE puntaje_crediticio 
                             SET puntaje_actual = ?, 
                                 total_financiamientos = ?, 
                                 total_retrasos = ?,
                                 fecha_actualizacion = CURRENT_TIMESTAMP
                             WHERE id = ?";
                
                $stmt = mysqli_prepare($this->conexion, $sqlUpdate);
                mysqli_stmt_bind_param($stmt, 'iiii', 
                    $puntajeData['puntaje'], 
                    $puntajeData['total_financiamientos'], 
                    $puntajeData['total_retrasos'],
                    $existeRegistro['id']
                );
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                return $existeRegistro['id'];
            } else {
                // Crear nuevo registro
                $sqlInsert = "INSERT INTO puntaje_crediticio 
                             (tipo_cliente, $campoId, puntaje_actual, total_financiamientos, total_retrasos) 
                             VALUES (?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($this->conexion, $sqlInsert);
                mysqli_stmt_bind_param($stmt, 'siiii', 
                    $tipo, 
                    $id, 
                    $puntajeData['puntaje'], 
                    $puntajeData['total_financiamientos'], 
                    $puntajeData['total_retrasos']
                );
                mysqli_stmt_execute($stmt);
                $nuevoId = mysqli_insert_id($this->conexion);
                mysqli_stmt_close($stmt);

                return $nuevoId;
            }

        } catch (Exception $e) {
            throw new Exception("Error al actualizar puntaje crediticio: " . $e->getMessage());
        }
    }

    // Registrar cambio en el historial
    public function registrarHistorialPuntaje($puntajeCrediticioId, $puntajeAnterior, $puntajeNuevo, $puntosPerdidos, $motivo, $idCuota = null)
    {
        try {
            $sqlHistorial = "INSERT INTO historial_puntaje 
                            (id_puntaje_crediticio, id_cuota, puntaje_anterior, puntaje_nuevo, puntos_perdidos, motivo) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($this->conexion, $sqlHistorial);
            mysqli_stmt_bind_param($stmt, 'iiiisi', 
                $puntajeCrediticioId, 
                $idCuota, 
                $puntajeAnterior, 
                $puntajeNuevo, 
                $puntosPerdidos, 
                $motivo
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            return true;

        } catch (Exception $e) {
            throw new Exception("Error al registrar historial: " . $e->getMessage());
        }
    }

    // Actualizar todos los puntajes crediticios (script masivo)
    public function actualizarTodosPuntajes()
    {
        try {
            $actualizados = 0;

            // Obtener todos los clientes
            $sqlClientes = "SELECT id FROM clientes_financiar";
            $result = mysqli_query($this->conexion, $sqlClientes);
            
            while ($cliente = mysqli_fetch_assoc($result)) {
                $puntajeData = $this->calcularPuntajeIndividual('cliente', $cliente['id']);
                $this->actualizarPuntajeCrediticio('cliente', $cliente['id'], $puntajeData);
                $actualizados++;
            }

            // Obtener todos los conductores
            $sqlConductores = "SELECT id_conductor FROM conductores WHERE desvinculado = 0";
            $result = mysqli_query($this->conexion, $sqlConductores);
            
            while ($conductor = mysqli_fetch_assoc($result)) {
                $puntajeData = $this->calcularPuntajeIndividual('conductor', $conductor['id_conductor']);
                $this->actualizarPuntajeCrediticio('conductor', $conductor['id_conductor'], $puntajeData);
                $actualizados++;
            }

            return $actualizados;

        } catch (Exception $e) {
            throw new Exception("Error al actualizar todos los puntajes: " . $e->getMessage());
        }
    }

    // Obtener clientes/conductores que necesitan actualización de puntaje
    public function obtenerClientesParaActualizar()
    {
        try {
            $clientesActualizar = [];

            // Buscar cuotas que han cambiado de estado recientemente
            $sqlCuotasCambiadas = "SELECT DISTINCT 
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
                                   AND DATE(cf.fecha_pago) = CURDATE()";

            $result = mysqli_query($this->conexion, $sqlCuotasCambiadas);
            while ($row = mysqli_fetch_assoc($result)) {
                $clientesActualizar[] = [
                    'tipo' => $row['tipo_cliente'],
                    'id' => $row['id_referencia']
                ];
            }

            return $clientesActualizar;

        } catch (Exception $e) {
            throw new Exception("Error al obtener clientes para actualizar: " . $e->getMessage());
        }
    }

    // Procesamiento diario de puntajes
    public function procesarPuntajesDiarios()
    {
        try {
            $procesados = 0;
            $errores = 0;
            $log = [];

            // Obtener cuotas que vencieron hoy o fueron pagadas hoy
            $sqlCuotasHoy = "SELECT 
                                cf.*,
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
                            WHERE (DATE(cf.fecha_vencimiento) = CURDATE() OR DATE(cf.fecha_pago) = CURDATE())
                            AND f.estado IN ('activo', 'vigente', 'en_proceso')";

            $result = mysqli_query($this->conexion, $sqlCuotasHoy);

            while ($cuota = mysqli_fetch_assoc($result)) {
                try {
                    // Calcular puntaje actualizado
                    $puntajeAnterior = $this->obtenerPuntajeActual($cuota['tipo_cliente'], $cuota['id_referencia']);
                    $nuevoPuntaje = $this->calcularPuntajeIndividual($cuota['tipo_cliente'], $cuota['id_referencia']);
                    
                    // Actualizar puntaje
                    $puntajeCrediticioId = $this->actualizarPuntajeCrediticio(
                        $cuota['tipo_cliente'], 
                        $cuota['id_referencia'], 
                        $nuevoPuntaje
                    );

                    // Determinar motivo y puntos perdidos
                    $puntosPerdidos = max(0, $puntajeAnterior - $nuevoPuntaje['puntaje']);
                    $motivo = $this->determinarMotivoCambio($cuota);

                    // Registrar en historial si hubo cambio
                    if ($puntajeAnterior != $nuevoPuntaje['puntaje']) {
                        $this->registrarHistorialPuntaje(
                            $puntajeCrediticioId,
                            $puntajeAnterior,
                            $nuevoPuntaje['puntaje'],
                            $puntosPerdidos,
                            $motivo,
                            $cuota['idcuotas_financiamiento']
                        );
                    }

                    $procesados++;
                    $log[] = "Procesado: {$cuota['tipo_cliente']} ID {$cuota['id_referencia']} - Puntaje: {$puntajeAnterior} → {$nuevoPuntaje['puntaje']}";

                } catch (Exception $e) {
                    $errores++;
                    $log[] = "Error: {$cuota['tipo_cliente']} ID {$cuota['id_referencia']} - " . $e->getMessage();
                }
            }

            return [
                'procesados' => $procesados,
                'errores' => $errores,
                'log' => $log
            ];

        } catch (Exception $e) {
            throw new Exception("Error en procesamiento diario: " . $e->getMessage());
        }
    }

    // Obtener puntaje actual de un cliente/conductor
    private function obtenerPuntajeActual($tipo, $id)
    {
        $campoId = ($tipo === 'cliente') ? 'id_cliente' : 'id_conductor';
        
        $sql = "SELECT puntaje_actual FROM puntaje_crediticio WHERE tipo_cliente = ? AND $campoId = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $tipo, $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $row ? $row['puntaje_actual'] : 100; // Puntaje por defecto si no existe
    }

    // Determinar motivo del cambio de puntaje
    private function determinarMotivoCambio($cuota)
    {
        $hoy = date('Y-m-d');
        $fechaVencimiento = $cuota['fecha_vencimiento'];
        $fechaPago = $cuota['fecha_pago'];

        if ($fechaPago) {
            if ($fechaPago <= $fechaVencimiento) {
                return "Cuota #{$cuota['numero_cuota']} pagada puntualmente";
            } else {
                $diasRetraso = (strtotime($fechaPago) - strtotime($fechaVencimiento)) / (60 * 60 * 24);
                return "Cuota #{$cuota['numero_cuota']} pagada con {$diasRetraso} días de retraso";
            }
        } else {
            if ($hoy > $fechaVencimiento) {
                $diasVencido = (strtotime($hoy) - strtotime($fechaVencimiento)) / (60 * 60 * 24);
                return "Cuota #{$cuota['numero_cuota']} vencida hace {$diasVencido} días";
            } else {
                return "Cuota #{$cuota['numero_cuota']} pendiente de pago";
            }
        }
    }

    // Obtener resumen de puntajes por rango
    public function obtenerResumenPorRangos()
    {
        try {
            $sql = "SELECT 
                        CASE 
                            WHEN puntaje_actual >= 76 THEN 'Excelente'
                            WHEN puntaje_actual >= 51 THEN 'Bueno'
                            WHEN puntaje_actual >= 26 THEN 'Regular'
                            ELSE 'Malo'
                        END as rango,
                        COUNT(*) as cantidad,
                        tipo_cliente
                    FROM puntaje_crediticio
                    GROUP BY rango, tipo_cliente
                    ORDER BY 
                        CASE 
                            WHEN puntaje_actual >= 76 THEN 1
                            WHEN puntaje_actual >= 51 THEN 2
                            WHEN puntaje_actual >= 26 THEN 3
                            ELSE 4
                        END, tipo_cliente";

            $result = mysqli_query($this->conexion, $sql);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);

        } catch (Exception $e) {
            throw new Exception("Error al obtener resumen por rangos: " . $e->getMessage());
        }
    }

    // Obtener alertas de clientes en riesgo
    public function obtenerAlertasRiesgo()
    {
        try {
            $sql = "SELECT 
                        pc.*,
                        CASE 
                            WHEN pc.tipo_cliente = 'cliente' THEN CONCAT(cf.nombres, ' ', cf.apellido_paterno)
                            ELSE CONCAT(c.nombres, ' ', c.apellido_paterno)
                        END as nombre_completo,
                        CASE 
                            WHEN pc.tipo_cliente = 'cliente' THEN cf.telefono
                            ELSE c.telefono
                        END as telefono
                    FROM puntaje_crediticio pc
                    LEFT JOIN clientes_financiar cf ON pc.id_cliente = cf.id AND pc.tipo_cliente = 'cliente'
                    LEFT JOIN conductores c ON pc.id_conductor = c.id_conductor AND pc.tipo_cliente = 'conductor'
                    WHERE pc.puntaje_actual < 50
                    ORDER BY pc.puntaje_actual ASC";

            $result = mysqli_query($this->conexion, $sql);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);

        } catch (Exception $e) {
            throw new Exception("Error al obtener alertas de riesgo: " . $e->getMessage());
        }
    }
}