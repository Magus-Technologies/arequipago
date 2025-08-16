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

            // DESPUÉS:
            if (!empty($filtros['fechaInicio']) && !empty($filtros['fechaFin'])) {
                $whereConditions[] = "DATE(f.fecha_creacion) BETWEEN ? AND ?";
                $whereValues[] = $filtros['fechaInicio'];
                $whereValues[] = $filtros['fechaFin'];
            } elseif (!empty($filtros['fechaInicio'])) {
                $whereConditions[] = "DATE(f.fecha_creacion) >= ?";
                $whereValues[] = $filtros['fechaInicio'];
            } elseif (!empty($filtros['fechaFin'])) {
                $whereConditions[] = "DATE(f.fecha_creacion) <= ?";
                $whereValues[] = $filtros['fechaFin'];
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            // Query principal
            $sql = "SELECT DISTINCT
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
            LEFT JOIN financiamiento f ON (pc.id_cliente = f.id_cliente OR pc.id_conductor = f.id_conductor)
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

            // DESPUÉS:
            $sqlCount = "SELECT COUNT(DISTINCT pc.id) as total 
                        FROM puntaje_crediticio pc
                        LEFT JOIN clientes_financiar cf ON pc.id_cliente = cf.id AND pc.tipo_cliente = 'cliente'
                        LEFT JOIN conductores c ON pc.id_conductor = c.id_conductor AND pc.tipo_cliente = 'conductor'
                        LEFT JOIN financiamiento f ON (pc.id_cliente = f.id_cliente OR pc.id_conductor = f.id_conductor)
                        $whereClause";
            
            // Remover los últimos dos elementos (limite y offset) de los valores
            $countValues = array_slice($whereValues, 0, -2);

            // DESPUÉS:
            if (!empty($countValues)) {
                $stmtCount = mysqli_prepare($this->conexion, $sqlCount);
                if ($stmtCount) {
                    $types = str_repeat('s', count($countValues));
                    mysqli_stmt_bind_param($stmtCount, $types, ...$countValues);
                    mysqli_stmt_execute($stmtCount);
                    $resultCount = mysqli_stmt_get_result($stmtCount);
                    $rowCount = mysqli_fetch_assoc($resultCount);
                    $totalRegistros = $rowCount['total'];
                    mysqli_stmt_close($stmtCount);
                } else {
                    throw new Exception("Error al preparar consulta COUNT: " . mysqli_error($this->conexion));
                }
            } else {
                $resultCount = mysqli_query($this->conexion, $sqlCount);
                if ($resultCount) {
                    $rowCount = mysqli_fetch_assoc($resultCount);
                    $totalRegistros = $rowCount['total'];
                } else {
                    throw new Exception("Error en consulta COUNT: " . mysqli_error($this->conexion));
                }
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
                      AND f.estado IN ('En Progreso', 'En progreso', 'Finalizado')
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

            // *** VALIDACION ADICIONAL: Verificar que realmente tenga financiamientos ***
            $campoId = ($tipo === 'cliente') ? 'id_cliente' : 'id_conductor';
            $sqlValidarFinanciamientos = "SELECT COUNT(*) as total FROM financiamiento WHERE $campoId = ?";
            $stmt = mysqli_prepare($this->conexion, $sqlValidarFinanciamientos);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $financeRow = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            // Si no tiene financiamientos, no debería tener historial real
            if ($financeRow['total'] == 0) {
                return ['historial' => []];
            }

            // Construir condiciones WHERE para filtros
            $whereConditions = [];
            $whereValues = [];

            if (!empty($filtros['mes'])) {
                $whereConditions[] = "DATE_FORMAT(fecha_referencia, '%Y-%m') = ?";
                $whereValues[] = $filtros['mes'];
            }

            if (!empty($filtros['estado'])) {
                switch ($filtros['estado']) {
                    case 'puntual':
                        $whereConditions[] = "estado_cuota = 'puntual'";
                        break;
                    case 'retraso':
                        $whereConditions[] = "estado_cuota = 'retraso'";
                        break;
                    case 'vencido':
                        $whereConditions[] = "estado_cuota = 'vencido'";
                        break;
                }
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            // Consulta unificada que combina historial existente + simulación con lógica corregida
            $sqlHistorial = "SELECT * FROM (
                                -- Historial existente (esta parte no cambia)
                                SELECT
                                    hp.id,
                                    hp.puntaje_anterior,
                                    hp.puntaje_nuevo,
                                    hp.puntos_perdidos,
                                    hp.motivo,
                                    cf.fecha_vencimiento as fecha_referencia,
                                    cf.numero_cuota,
                                    cf.monto as monto_cuota,
                                    cf.fecha_vencimiento,
                                    cf.fecha_pago,
                                    f.idfinanciamiento,
                                    p.nombre as nombre_producto,
                                    CASE
                                        WHEN hp.puntos_perdidos = 0 THEN 'puntual'
                                        WHEN hp.motivo LIKE '%vencida%' THEN 'vencido'
                                        ELSE 'retraso'
                                    END as estado_cuota,
                                    'historial' as origen
                                FROM historial_puntaje hp
                                INNER JOIN cuotas_financiamiento cf ON hp.id_cuota = cf.idcuotas_financiamiento
                                INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                                LEFT JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                                WHERE hp.id_puntaje_crediticio = ?

                                UNION ALL

                                -- Simulación de cuotas sin historial (LÓGICA CORREGIDA)
                                SELECT
                                    NULL as id,
                                    NULL as puntaje_anterior,
                                    NULL as puntaje_nuevo,
                                    -- INICIO DE LA LÓGICA CORREGIDA --
                                    CASE
                                        WHEN (cf.fecha_pago > cf.fecha_vencimiento) OR (cf.fecha_vencimiento < CURDATE() AND cf.fecha_pago IS NULL)
                                        THEN
                                            -- Subconsulta para contar financiamientos y aplicar la regla
                                            IF(
                                                (SELECT COUNT(*) FROM financiamiento f2 WHERE f2.$campoId = f.$campoId) > 1,
                                                3, -- Si tiene más de 1 financiamiento, resta 3 puntos
                                                5  -- Si tiene solo 1, resta 5 puntos
                                            )
                                        ELSE 0
                                    END as puntos_perdidos,
                                    -- FIN DE LA LÓGICA CORREGIDA --
                                    CASE
                                        WHEN cf.fecha_vencimiento < CURDATE() AND cf.fecha_pago IS NULL AND cf.estado = 'En Progreso'
                                        THEN CONCAT('Cuota #', cf.numero_cuota, ' vencida (simulado)')
                                        WHEN cf.fecha_pago IS NOT NULL AND cf.fecha_pago <= cf.fecha_vencimiento
                                        THEN CONCAT('Cuota #', cf.numero_cuota, ' pagada a tiempo')
                                        WHEN cf.fecha_pago IS NOT NULL AND cf.fecha_pago > cf.fecha_vencimiento
                                        THEN CONCAT('Cuota #', cf.numero_cuota, ' pagada con retraso (simulado)')
                                        ELSE CONCAT('Cuota #', cf.numero_cuota, ' pendiente')
                                    END as motivo,
                                    cf.fecha_vencimiento as fecha_referencia,
                                    cf.numero_cuota,
                                    cf.monto as monto_cuota,
                                    cf.fecha_vencimiento,
                                    cf.fecha_pago,
                                    f.idfinanciamiento,
                                    p.nombre as nombre_producto,
                                    CASE
                                        WHEN cf.fecha_vencimiento < CURDATE() AND cf.fecha_pago IS NULL AND cf.estado = 'En Progreso'
                                        THEN 'vencido'
                                        WHEN cf.fecha_pago IS NOT NULL AND cf.fecha_pago <= cf.fecha_vencimiento THEN 'puntual'
                                        WHEN cf.fecha_pago IS NOT NULL AND cf.fecha_pago > cf.fecha_vencimiento THEN 'retraso'
                                        ELSE 'pendiente'
                                    END as estado_cuota,
                                    'simulado' as origen
                                FROM cuotas_financiamiento cf
                                INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                                LEFT JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                                WHERE f.$campoId = ?
                                AND NOT EXISTS (
                                    SELECT 1 FROM historial_puntaje hp2
                                    WHERE hp2.id_cuota = cf.idcuotas_financiamiento
                                )
                            ) as historial_completo
                            $whereClause
                            ORDER BY fecha_referencia DESC";

            $queryValues = [$puntajeId, $id];
            if (!empty($whereValues)) {
                $queryValues = array_merge($queryValues, $whereValues);
            }

            $stmt = mysqli_prepare($this->conexion, $sqlHistorial);
            if ($stmt) {
                $types = 'ii' . str_repeat('s', count($whereValues));
                mysqli_stmt_bind_param($stmt, $types, ...$queryValues);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $historial = mysqli_fetch_all($result, MYSQLI_ASSOC);
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error al preparar consulta de historial: " . mysqli_error($this->conexion));
            }

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
                                WHERE $campoId = ? AND estado IN ('En Progreso', 'En progreso', 'Finalizado')";
            
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

            // Obtener cuotas con retraso (YA PAGADAS CON RETRASO)
            $sqlRetrasosPagados = "SELECT COUNT(*) as retrasos_pagados
                                FROM cuotas_financiamiento cf
                                INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                                WHERE f.$campoId = ?
                                AND cf.fecha_pago > cf.fecha_vencimiento
                                AND cf.fecha_pago IS NOT NULL";

            $stmt = mysqli_prepare($this->conexion, $sqlRetrasosPagados);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $retrasoInfo = mysqli_fetch_assoc($result);
            $retrasosPagados = $retrasoInfo['retrasos_pagados'];
            mysqli_stmt_close($stmt);

            // *** NUEVA LÓGICA: Obtener cuotas VENCIDAS PENDIENTES ***
            $sqlCuotasVencidas = "SELECT COUNT(*) as cuotas_vencidas
                                FROM cuotas_financiamiento cf
                                INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                                WHERE f.$campoId = ?
                                AND cf.fecha_vencimiento < CURDATE()
                                AND cf.estado = 'En progreso'
                                AND cf.fecha_pago IS NULL";

            $stmt = mysqli_prepare($this->conexion, $sqlCuotasVencidas);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $vencidoInfo = mysqli_fetch_assoc($result);
            $cuotasVencidas = $vencidoInfo['cuotas_vencidas'];
            mysqli_stmt_close($stmt);

            // Total de retrasos = cuotas pagadas con retraso + cuotas vencidas pendientes
            $totalRetrasos = $retrasosPagados + $cuotasVencidas;

            // Calcular puntaje según las reglas: 5 puntos si tiene 1 financiamiento, 3 si tiene 2 o más
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
            // *** NUEVA LÓGICA: Procesar cuotas vencidas sin historial ***
            $this->procesarCuotasVencidasSinHistorial();
            
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

    // Procesar cuotas vencidas que no tienen historial registrado
    private function procesarCuotasVencidasSinHistorial()
    {
        try {
            // Buscar cuotas vencidas sin historial de 'vencido'
            // Buscar cuotas vencidas sin historial de 'vencido' (incluye las que vencen hoy)
            $sqlCuotasVencidas = "SELECT DISTINCT
                                    cf.idcuotas_financiamiento,
                                    cf.numero_cuota,
                                    cf.fecha_vencimiento,
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
                                WHERE cf.fecha_vencimiento <= CURDATE()
                                AND cf.fecha_pago IS NULL
                                AND cf.estado = 'En progreso'
                                AND f.estado IN ('En Progreso', 'En progreso', 'Finalizado')
                                AND NOT EXISTS (
                                    SELECT 1 FROM historial_puntaje hp 
                                    WHERE hp.id_cuota = cf.idcuotas_financiamiento 
                                    AND hp.motivo LIKE '%vencido%'
                                )";

            $result = mysqli_query($this->conexion, $sqlCuotasVencidas);
            
            while ($cuota = mysqli_fetch_assoc($result)) {
                try {
                    // Obtener ID de puntaje crediticio
                    $campoId = ($cuota['tipo_cliente'] === 'cliente') ? 'id_cliente' : 'id_conductor';
                    $sqlPuntajeId = "SELECT id FROM puntaje_crediticio WHERE tipo_cliente = ? AND $campoId = ?";
                    $stmt = mysqli_prepare($this->conexion, $sqlPuntajeId);
                    mysqli_stmt_bind_param($stmt, 'si', $cuota['tipo_cliente'], $cuota['id_referencia']);
                    mysqli_stmt_execute($stmt);
                    $result2 = mysqli_stmt_get_result($stmt);
                    $puntajeRow = mysqli_fetch_assoc($result2);
                    mysqli_stmt_close($stmt);

                    if (!$puntajeRow) continue;

                    // Obtener puntaje anterior
                    $puntajeAnterior = $this->obtenerPuntajeActual($cuota['tipo_cliente'], $cuota['id_referencia']);
                    
                    // Calcular nuevo puntaje usando la lógica existente
                    $nuevoPuntajeData = $this->calcularPuntajeIndividual($cuota['tipo_cliente'], $cuota['id_referencia']);
                    $puntajeNuevo = $nuevoPuntajeData['puntaje'];
                    
                    // Calcular puntos perdidos reales
                    $puntosPerdidos = max(0, $puntajeAnterior - $puntajeNuevo);
                    
                    // Crear motivo claro
                    $diasVencido = (strtotime(date('Y-m-d')) - strtotime($cuota['fecha_vencimiento'])) / (60 * 60 * 24);
                    $motivo = "Cuota #{$cuota['numero_cuota']} vencida hace {$diasVencido} días";

                    // Registrar en historial solo si hay puntos perdidos
                    if ($puntosPerdidos > 0) {
                        $this->registrarHistorialPuntaje(
                            $puntajeRow['id'],
                            $puntajeAnterior,
                            $puntajeNuevo,
                            $puntosPerdidos,
                            $motivo,
                            $cuota['idcuotas_financiamiento']
                        );
                    }

                } catch (Exception $e) {
                    // Continuar con la siguiente cuota si hay error
                    error_log("Error procesando cuota vencida {$cuota['idcuotas_financiamiento']}: " . $e->getMessage());
                    continue;
                }
            }

        } catch (Exception $e) {
            throw new Exception("Error al procesar cuotas vencidas sin historial: " . $e->getMessage());
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
    // Procesamiento diario de puntajes
    public function procesarPuntajesDiarios()
    {
        try {
            $procesados = 0;
            $errores = 0;
            $log = [];

            // *** PRIMERO: Procesar cuotas vencidas sin historial (incluye las de hoy) ***
            $this->procesarCuotasVencidasSinHistorial();
            $log[] = "Procesadas cuotas vencidas sin historial";

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
                            AND f.estado IN ('En Progreso', 'En progreso', 'Finalizado')";

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

                    $procesados++;
                    $log[] = "Actualizado: {$cuota['tipo_cliente']} ID {$cuota['id_referencia']} - Puntaje: {$puntajeAnterior} → {$nuevoPuntaje['puntaje']}";

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

    public function obtenerDatosCompletos($tipo, $id)
    {
    try {
    $data = [];
    // Obtener información del cliente/conductor
    if ($tipo === 'cliente') {
    $sqlPersona = "SELECT * FROM clientes_financiar WHERE id = ?";
    } else {
    $sqlPersona = "SELECT * FROM conductores WHERE id_conductor = ?";
    }
    $stmt = mysqli_prepare($this->conexion, $sqlPersona);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data['persona'] = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    if (!$data['persona']) {
    throw new Exception("No se encontró el $tipo con ID $id");
    }
    // Obtener puntaje crediticio
    $sqlPuntaje = "SELECT * FROM puntaje_crediticio WHERE tipo_cliente = ? AND " .
    ($tipo === 'cliente' ? 'id_cliente' : 'id_conductor') . " = ?";
    $stmt = mysqli_prepare($this->conexion, $sqlPuntaje);
    mysqli_stmt_bind_param($stmt, 'si', $tipo, $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data['puntaje'] = mysqli_fetch_assoc($result) ?: ['puntaje_actual' => 100, 'total_financiamientos' => 0, 'total_retrasos' => 0]; // Default si no existe
    mysqli_stmt_close($stmt);
    // Obtener financiamientos con detalles de producto, grupo y variante
    $sqlFinanciamientos = "SELECT f.*, p.nombre as nombre_producto,
    CASE
    WHEN f.id_variante IS NOT NULL THEN gv.nombre_variante
    WHEN f.grupo_financiamiento IS NOT NULL THEN pf.nombre_plan
    ELSE NULL
    END as grupo_nombre
    FROM financiamiento f
    LEFT JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
    LEFT JOIN grupos_variantes gv ON f.id_variante = gv.idgrupos_variantes
    LEFT JOIN planes_financiamiento pf ON f.grupo_financiamiento = pf.idplan_financiamiento
    WHERE " . ($tipo === 'cliente' ? 'f.id_cliente' : 'f.id_conductor') . " = ?
    AND f.estado IN ('En Progreso', 'En progreso', 'Finalizado')
    ORDER BY f.fecha_inicio DESC";
    $stmt = mysqli_prepare($this->conexion, $sqlFinanciamientos);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data['financiamientos'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    // Obtener historial (usando la función existente, sin filtros)
    $historialData = $this->obtenerHistorialPuntaje($tipo, $id, []);
    $data['historial'] = $historialData['historial'];
    return $data;
    } catch (Exception $e) {
    throw new Exception("Error al obtener datos completos: " . $e->getMessage());
    }
    }

}