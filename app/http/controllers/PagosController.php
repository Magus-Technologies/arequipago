<?php

require_once 'app/models/Financiamiento.php';

class PagosController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function render()
    {
        try {
            $sql = "SELECT com.id_compra,CONCAT(com.serie, ' | ' , com.numero) AS factura,com.moneda ,com.fecha_emision,com.fecha_vencimiento,CONCAT(pro.ruc,' | ' ,pro.razon_social) AS cliente,
            com.total,            
                CASE 
                WHEN dc.estado = '1'  AND dc.id_compra = dc.id_compra THEN SUM(dc.monto)
                WHEN dc.estadO= '0' THEN '0'
                END AS pagado,
               (com.total - SUM(dc.monto) ) AS saldo
                FROM compras AS com
                INNER JOIN dias_compras AS dc ON  com.id_compra=dc.id_compra 
                INNER JOIN proveedores AS pro ON com.id_proveedor=pro.proveedor_id
                WHERE com.id_tipo_pago = 2  and com.id_empresa='{$_SESSION['id_empresa']}'
                and com.sucursal='{$_SESSION['sucursal']}'
                GROUP BY dc.id_compra,dc.estado  
            ";
            $fila = mysqli_query($this->conectar, $sql);
            return json_encode(mysqli_fetch_all($fila, MYSQLI_ASSOC));
        } catch (Exception $e) {
            return json_encode([]);
        }
    }

    public function getAllByIdCompra()
    {
        try {
            $sql = "SELECT * FROM dias_compras WHERE id_compra = '{$_POST['id']}'";
            $fila = mysqli_query($this->conectar, $sql);
            return json_encode(mysqli_fetch_all($fila, MYSQLI_ASSOC));
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function validarLista()
    {
        $listaPagos = json_decode($_POST['dias_lista'], true);
        echo json_encode($listaPagos);
    }

    public function pagarCuota()
    {
        $sql = "UPDATE dias_compras set estado = '1' where dias_compra_id='{$_POST['id']}'";
        $result = $this->conectar->query($sql);

        echo json_encode($result);
    }

    public function pagarCuotaVentas()
    {
        $sql = "UPDATE dias_ventas set estado = '1' where dias_venta_id='{$_POST['id']}'";
        $result = $this->conectar->query($sql);

        echo json_encode($result);
    }

    public function contarPagosPendientes()
    {
        header('Content-Type: application/json');

        $cantidad = 0;

        $sql = 'SELECT COUNT(*) AS total FROM pagos_financiamiento WHERE estado = 0';
        $stmt = $this->conectar->query($sql);

        if ($stmt) {
            $fila = $stmt->fetch_assoc();
            $cantidad = intval($fila['total']);
        }

        echo json_encode(['cantidad' => $cantidad]);
    }

    /**
     * Obtiene los pagos pendientes de aprobación
     */
    public function getPagosFinancePendiente()
    {
        // Verificamos si hay una sesión activa
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Sesión no iniciada'
            ]);
            return;
        }

        // ⚡ Cerrar la sesión inmediatamente para no bloquear otros requests
        session_write_close();

        try {
            $startTime = microtime(true);

            // Consulta optimizada: obtener conductor o cliente en una sola query
            $query = "SELECT p.*, 
                p.moneda as moneda_pago,
                COALESCE(
                    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno),
                    CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno)
                ) AS conductor,
                CONCAT(COALESCE(u.nombres, ''), ' ', COALESCE(u.apellidos, '')) AS asesor,
                c.numUnidad AS numUnidad 
            FROM pagos_financiamiento p
            LEFT JOIN conductores c ON p.id_conductor = c.id_conductor
            LEFT JOIN clientes_financiar cf ON p.id_cliente = cf.id
            LEFT JOIN usuarios u ON p.id_asesor = u.usuario_id
            WHERE p.estado = 0
            ORDER BY p.fecha_pago DESC";

            // Preparamos la consulta
            $stmt = mysqli_prepare($this->conectar, $query);

            if (!$stmt) {
                throw new Exception('Error al preparar consulta: ' . mysqli_error($this->conectar));
            }

            // Ejecutamos la consulta
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Error al ejecutar consulta: ' . mysqli_stmt_error($stmt));
            }

            // Obtenemos el resultado
            $result = mysqli_stmt_get_result($stmt);

            // Inicializamos array para almacenar los datos
            $pagos = [];

            // Procesamos cada registro (ya no necesitamos queries adicionales)
            while ($row = mysqli_fetch_assoc($result)) {
                $pagos[] = $row;
            }

            // Cerramos el statement
            mysqli_stmt_close($stmt);

            $executionTime = round(microtime(true) - $startTime, 3);
            error_log('getPagosFinancePendiente: ' . count($pagos) . " pagos obtenidos en {$executionTime}s");

            // Devolvemos los resultados
            echo json_encode([
                'success' => true,
                'data' => $pagos
            ]);
        } catch (Exception $e) {
            error_log('Error en getPagosFinancePendiente: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener pagos pendientes: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene los pagos rechazados
     */
    public function getPagosFinanceRechazados()
    {
        // Verificamos si hay una sesión activa
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Sesión no iniciada'
            ]);
            return;
        }

        // ⚡ Cerrar la sesión inmediatamente para no bloquear otros requests
        session_write_close();

        try {
            $startTime = microtime(true);

            // Consulta optimizada: obtener conductor o cliente en una sola query
            $query = "SELECT p.*, 
                COALESCE(
                    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno),
                    CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno)
                ) AS conductor,
                CONCAT(COALESCE(u.nombres, ''), ' ', COALESCE(u.apellidos, '')) AS asesor,
                c.numUnidad AS numUnidad 
            FROM pagos_financiamiento p
            LEFT JOIN conductores c ON p.id_conductor = c.id_conductor
            LEFT JOIN clientes_financiar cf ON p.id_cliente = cf.id
            LEFT JOIN usuarios u ON p.id_asesor = u.usuario_id
            WHERE p.estado = 2
            ORDER BY p.fecha_pago DESC";

            // Preparamos la consulta
            $stmt = mysqli_prepare($this->conectar, $query);

            if (!$stmt) {
                throw new Exception('Error al preparar consulta: ' . mysqli_error($this->conectar));
            }

            // Ejecutamos la consulta
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Error al ejecutar consulta: ' . mysqli_stmt_error($stmt));
            }

            // Obtenemos el resultado
            $result = mysqli_stmt_get_result($stmt);

            // Inicializamos array para almacenar los datos
            $pagos = [];

            // Procesamos cada registro (ya no necesitamos queries adicionales)
            while ($row = mysqli_fetch_assoc($result)) {
                $pagos[] = $row;
            }

            // Cerramos el statement
            mysqli_stmt_close($stmt);

            $executionTime = round(microtime(true) - $startTime, 3);
            error_log('getPagosFinanceRechazados: ' . count($pagos) . " pagos obtenidos en {$executionTime}s");

            // Devolvemos los resultados
            echo json_encode([
                'success' => true,
                'data' => $pagos
            ]);
        } catch (Exception $e) {
            error_log('Error en getPagosFinanceRechazados: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener pagos rechazados: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Ver detalle de un pago pendiente
     */
    public function verDetallePagoPendiente()
    {
        // Verificamos si hay una sesión activa
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Sesión no iniciada'
            ]);
            return;
        }

        // Verificamos si se recibió el ID del pago
        if (!isset($_POST['idPago']) || empty($_POST['idPago'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de pago no especificado'
            ]);
            return;
        }

        $idPago = $_POST['idPago'];

        try {
            // 1. Obtenemos datos del pago
            $queryPago = "SELECT p.*, 
                          CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS conductor,
                          CONCAT(u.nombres, ' ', u.apellidos) AS asesor
                          FROM pagos_financiamiento p
                          LEFT JOIN conductores c ON p.id_conductor = c.id_conductor
                          LEFT JOIN usuarios u ON p.id_asesor = u.usuario_id
                          WHERE p.idpagos_financiamiento = ?";

            $stmtPago = mysqli_prepare($this->conectar, $queryPago);
            mysqli_stmt_bind_param($stmtPago, 'i', $idPago);
            mysqli_stmt_execute($stmtPago);

            $resultPago = mysqli_stmt_get_result($stmtPago);
            $pago = mysqli_fetch_assoc($resultPago);

            if (!$pago) {
                mysqli_stmt_close($stmtPago);
                echo json_encode([
                    'success' => false,
                    'message' => 'Pago no encontrado'
                ]);
                return;
            }

            mysqli_stmt_close($stmtPago);

            // 2. Obtenemos las cuotas del pago desde pagos_pendientes_financiamientos
            $queryPendientes = 'SELECT cuotas_json 
                                FROM pagos_pendientes_financiamientos 
                                WHERE idpagos_financiamiento = ?';

            $stmtPendientes = mysqli_prepare($this->conectar, $queryPendientes);
            mysqli_stmt_bind_param($stmtPendientes, 'i', $idPago);
            mysqli_stmt_execute($stmtPendientes);

            $resultPendientes = mysqli_stmt_get_result($stmtPendientes);
            $pendiente = mysqli_fetch_assoc($resultPendientes);

            mysqli_stmt_close($stmtPendientes);

            if (!$pendiente) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontraron detalles de cuotas para este pago'
                ]);
                return;
            }

            // 3. Decodificamos el JSON de cuotas
            $cuotasJson = $pendiente['cuotas_json'];
            $cuotasSeleccionadas = json_decode($cuotasJson, true);

            // Arreglo para almacenar toda la información de cuotas
            $infoCuotas = [];
            
            // 4. OPTIMIZACIÓN: Obtener todas las cuotas en una sola query
            $idsCuotas = array_column($cuotasSeleccionadas, 'idCuota');
            
            if (!empty($idsCuotas)) {
                $placeholders = implode(',', array_fill(0, count($idsCuotas), '?'));
                
                $queryCuotas = "SELECT cf.*, f.idproductosv2, f.id_variante, f.grupo_financiamiento, f.moneda as moneda_financiamiento
                    FROM cuotas_financiamiento cf
                    INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento 
                    WHERE cf.idcuotas_financiamiento IN ($placeholders)";
                
                $stmtCuotas = mysqli_prepare($this->conectar, $queryCuotas);
                
                if (!$stmtCuotas) {
                    throw new Exception("Error al preparar consulta de cuotas: " . mysqli_error($this->conectar));
                }
                
                $types = str_repeat('i', count($idsCuotas));
                mysqli_stmt_bind_param($stmtCuotas, $types, ...$idsCuotas);
                mysqli_stmt_execute($stmtCuotas);
                
                $resultCuotas = mysqli_stmt_get_result($stmtCuotas);
                
                // Crear un mapa de cuotas por ID para acceso rápido
                $cuotasMap = [];
                while ($cuotaInfo = mysqli_fetch_assoc($resultCuotas)) {
                    $cuotasMap[$cuotaInfo['idcuotas_financiamiento']] = $cuotaInfo;
                }
                
                mysqli_stmt_close($stmtCuotas);
                
                // Construir el array de información de cuotas
                foreach ($cuotasSeleccionadas as $cuota) {
                    $idCuota = $cuota['idCuota'];
                    
                    if (isset($cuotasMap[$idCuota])) {
                        $cuotaInfo = $cuotasMap[$idCuota];
                        
                        $infoCuotas[] = [
                            'idCuota' => $idCuota,
                            'numero_cuota' => $cuotaInfo['numero_cuota'],
                            'monto' => $cuota['monto'],
                            'mora' => $cuota['mora'] ?? 0,
                            'fechaVencimiento' => $cuota['fechaVencimiento'],
                            'id_financiamiento' => $cuotaInfo['id_financiamiento'],
                            'idproductosv2' => $cuotaInfo['idproductosv2'],
                            'id_variante' => $cuotaInfo['id_variante'],
                            'grupo_financiamiento' => $cuotaInfo['grupo_financiamiento']
                        ];
                        
                        // Guardamos la moneda del financiamiento
                        $monedaFinanciamiento = $cuotaInfo['moneda_financiamiento'] ?? 'S/.';
                        // Guardamos los IDs para buscar producto y grupo de financiamiento
                        $idProducto = $cuotaInfo['idproductosv2'];
                        $idVariante = $cuotaInfo['id_variante'];
                        $grupoFinanciamiento = $cuotaInfo['grupo_financiamiento'];
                    }
                }
            }

            // 5. Obtenemos información del producto
            $nombreProducto = 'Producto no especificado';
            if (!empty($idProducto)) {
                $queryProducto = 'SELECT nombre FROM productosv2 WHERE idproductosv2 = ?';

                $stmtProducto = mysqli_prepare($this->conectar, $queryProducto);
                mysqli_stmt_bind_param($stmtProducto, 'i', $idProducto);
                mysqli_stmt_execute($stmtProducto);

                $resultProducto = mysqli_stmt_get_result($stmtProducto);
                $producto = mysqli_fetch_assoc($resultProducto);

                mysqli_stmt_close($stmtProducto);

                if ($producto) {
                    $nombreProducto = $producto['nombre'];
                }
            }

            // 6. Obtenemos información del grupo de financiamiento
            $nombreGrupo = 'Sin Grupo';

            // Si tenemos una variante válida
            if (!empty($idVariante) && $idVariante != 0) {
                $queryGrupo = 'SELECT nombre_variante FROM grupos_variantes WHERE idgrupos_variantes = ?';

                $stmtGrupo = mysqli_prepare($this->conectar, $queryGrupo);
                mysqli_stmt_bind_param($stmtGrupo, 'i', $idVariante);
                mysqli_stmt_execute($stmtGrupo);

                $resultGrupo = mysqli_stmt_get_result($stmtGrupo);
                $grupo = mysqli_fetch_assoc($resultGrupo);

                mysqli_stmt_close($stmtGrupo);

                if ($grupo) {
                    $nombreGrupo = $grupo['nombre_variante'];
                }
            }
            // Si no hay variante pero hay grupo
            elseif (!empty($grupoFinanciamiento) && is_numeric($grupoFinanciamiento)) {
                $queryGrupo = 'SELECT nombre_plan FROM planes_financiamiento WHERE idplan_financiamiento = ?';

                $stmtGrupo = mysqli_prepare($this->conectar, $queryGrupo);
                mysqli_stmt_bind_param($stmtGrupo, 'i', $grupoFinanciamiento);
                mysqli_stmt_execute($stmtGrupo);

                $resultGrupo = mysqli_stmt_get_result($stmtGrupo);
                $grupo = mysqli_fetch_assoc($resultGrupo);

                mysqli_stmt_close($stmtGrupo);

                if ($grupo) {
                    $nombreGrupo = $grupo['nombre_plan'];
                }
            }
            // Si es un texto, lo mostramos directamente
            elseif (!empty($grupoFinanciamiento) && !is_numeric($grupoFinanciamiento)) {
                $nombreGrupo = $grupoFinanciamiento;
            }

            // 7. Preparamos la respuesta
            $respuesta = [
                'success' => true,
                'data' => [
                    'producto' => $nombreProducto,
                    'grupo' => $nombreGrupo,
                    'cuotas' => $infoCuotas,
                    'moneda' => $monedaFinanciamiento ?? $pago['moneda']
                ]
            ];

            echo json_encode($respuesta);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener detalles del pago: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Aprobar un pago pendiente
     */
    public function aprobarPagoPendiente()
    {
        $startTime = microtime(true);

        // Verificamos si hay una sesión activa
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Sesión no iniciada'
            ]);
            return;
        }

        // Verificamos si se recibió el ID del pago
        if (!isset($_POST['idPago']) || empty($_POST['idPago'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de pago no especificado'
            ]);
            return;
        }

        $idPago = $_POST['idPago'];
        $idUsuario = $_SESSION['usuario_id'];
        
        // ⚡ CRÍTICO: Cerrar la sesión inmediatamente para no bloquear otros requests
        session_write_close();

        error_log("Iniciando aprobación de pago {$idPago} por usuario {$idUsuario}");

        try {
            // Iniciamos una transacción
            mysqli_begin_transaction($this->conectar);

            // ⭐ NUEVO: Verificar que el pago NO esté ya aprobado (prevenir duplicados)
            $queryVerificar = 'SELECT estado FROM pagos_financiamiento WHERE idpagos_financiamiento = ? FOR UPDATE';
            $stmtVerificar = mysqli_prepare($this->conectar, $queryVerificar);
            
            if (!$stmtVerificar) {
                throw new Exception('Error al verificar estado del pago: ' . mysqli_error($this->conectar));
            }
            
            mysqli_stmt_bind_param($stmtVerificar, 'i', $idPago);
            mysqli_stmt_execute($stmtVerificar);
            $resultVerificar = mysqli_stmt_get_result($stmtVerificar);
            $pagoActual = mysqli_fetch_assoc($resultVerificar);
            mysqli_stmt_close($stmtVerificar);
            
            if (!$pagoActual) {
                throw new Exception('Pago no encontrado');
            }
            
            if ($pagoActual['estado'] == 1) {
                // El pago ya fue aprobado, no hacer nada
                mysqli_rollback($this->conectar);
                error_log("⚠️ Intento de aprobar pago {$idPago} que ya está aprobado");
                echo json_encode([
                    'success' => false,
                    'message' => 'Este pago ya fue aprobado anteriormente'
                ]);
                return;
            }

            // 1. Actualizamos el estado del pago a aprobado (1)
            $queryPago = 'UPDATE pagos_financiamiento SET estado = 1 WHERE idpagos_financiamiento = ? AND estado = 0';

            $stmtPago = mysqli_prepare($this->conectar, $queryPago);

            if (!$stmtPago) {
                throw new Exception('Error al preparar actualización de pago: ' . mysqli_error($this->conectar));
            }

            mysqli_stmt_bind_param($stmtPago, 'i', $idPago);
            $resultPago = mysqli_stmt_execute($stmtPago);
            $filasAfectadas = mysqli_stmt_affected_rows($stmtPago);

            mysqli_stmt_close($stmtPago);

            if (!$resultPago || $filasAfectadas === 0) {
                throw new Exception('No se pudo actualizar el estado del pago (posiblemente ya fue procesado)');
            }

            // 2. Obtenemos las cuotas seleccionadas de pagos_pendientes_financiamientos
            $queryPendientes = 'SELECT cuotas_json FROM pagos_pendientes_financiamientos WHERE idpagos_financiamiento = ?';

            $stmtPendientes = mysqli_prepare($this->conectar, $queryPendientes);

            if (!$stmtPendientes) {
                throw new Exception('Error al preparar consulta de cuotas: ' . mysqli_error($this->conectar));
            }

            mysqli_stmt_bind_param($stmtPendientes, 'i', $idPago);
            mysqli_stmt_execute($stmtPendientes);

            $resultPendientes = mysqli_stmt_get_result($stmtPendientes);
            $pendiente = mysqli_fetch_assoc($resultPendientes);

            mysqli_stmt_close($stmtPendientes);

            if (!$pendiente) {
                throw new Exception('No se encontraron detalles de cuotas para este pago');
            }

            // 3. Decodificamos el JSON de cuotas
            $cuotasJson = $pendiente['cuotas_json'];
            $cuotasSeleccionadas = json_decode($cuotasJson, true);

            if (!$cuotasSeleccionadas || !is_array($cuotasSeleccionadas)) {
                throw new Exception('Error al decodificar las cuotas del pago');
            }

            error_log('Cuotas a procesar: ' . count($cuotasSeleccionadas));

            // 4. Actualizamos el usuario que aprobó el pago
            $queryUsuario = 'UPDATE pagos_pendientes_financiamientos SET id_usuario_aprobacion = ? WHERE idpagos_financiamiento = ?';

            $stmtUsuario = mysqli_prepare($this->conectar, $queryUsuario);

            if (!$stmtUsuario) {
                throw new Exception('Error al preparar actualización de usuario: ' . mysqli_error($this->conectar));
            }

            mysqli_stmt_bind_param($stmtUsuario, 'ii', $idUsuario, $idPago);
            $resultUsuario = mysqli_stmt_execute($stmtUsuario);

            mysqli_stmt_close($stmtUsuario);

            if (!$resultUsuario) {
                throw new Exception('Error al actualizar el usuario de aprobación');
            }

            // 5. Obtener la fecha real del pago original
            $queryFechaPago = 'SELECT DATE(fecha_pago) as fecha_pago_real FROM pagos_financiamiento WHERE idpagos_financiamiento = ?';
            $stmtFecha = mysqli_prepare($this->conectar, $queryFechaPago);

            if (!$stmtFecha) {
                throw new Exception('Error al preparar consulta de fecha: ' . mysqli_error($this->conectar));
            }

            mysqli_stmt_bind_param($stmtFecha, 'i', $idPago);
            mysqli_stmt_execute($stmtFecha);
            $resultFecha = mysqli_stmt_get_result($stmtFecha);
            $pagoData = mysqli_fetch_assoc($resultFecha);
            mysqli_stmt_close($stmtFecha);

            if (!$pagoData) {
                throw new Exception('No se pudo obtener la fecha del pago original');
            }

            $fechaPagoReal = $pagoData['fecha_pago_real'];

            // 6. Actualizar las cuotas
            $financiamientoModel = new Financiamiento();
            $resultado = $financiamientoModel->actualizarCuotas($cuotasSeleccionadas, $fechaPagoReal);

            if (!$resultado || !$resultado['success']) {
                $errorMsg = isset($resultado['message']) ? $resultado['message'] : 'Error desconocido';
                throw new Exception('Error al actualizar las cuotas: ' . $errorMsg);
            }

            // Confirmamos la transacción
            mysqli_commit($this->conectar);

            $executionTime = round(microtime(true) - $startTime, 3);
            error_log("Pago {$idPago} aprobado exitosamente en {$executionTime}s");

            // Aplicar puntos al aprobar el pago (fuera de la transacción principal)
            try {
                require_once 'app/models/ScoreService.php';
                $scoreService = new ScoreService();
                $scoreService->aplicarPuntosEnAprobacion($idPago);
            } catch (Exception $e) {
                // Si falla la aplicación de puntos, lo registramos pero no revertimos el pago
                // ya que la transacción principal ya fue confirmada
                error_log("Error al aplicar puntos en aprobación de pago {$idPago}: " . $e->getMessage());
            }

            echo json_encode([
                'success' => true,
                'message' => 'Pago aprobado correctamente',
                'execution_time' => $executionTime
            ]);
        } catch (Exception $e) {
            // Revertimos la transacción en caso de error
            mysqli_rollback($this->conectar);

            $executionTime = round(microtime(true) - $startTime, 3);
            error_log("Error al aprobar pago {$idPago}: " . $e->getMessage() . " (tiempo: {$executionTime}s)");

            echo json_encode([
                'success' => false,
                'message' => 'Error al aprobar el pago: ' . $e->getMessage(),
                'execution_time' => $executionTime
            ]);
        }
    }

    /**
     * Rechazar un pago pendiente
     */
    public function rechazarPagoPendiente()
    {
        // Verificamos si hay una sesión activa
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Sesión no iniciada'
            ]);
            return;
        }

        // Verificamos si se recibió el ID del pago
        if (!isset($_POST['idPago']) || empty($_POST['idPago'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de pago no especificado'
            ]);
            return;
        }

        $idPago = $_POST['idPago'];

        try {
            // Actualizamos el estado del pago a rechazado (2)
            $query = 'UPDATE pagos_financiamiento SET estado = 2 WHERE idpagos_financiamiento = ?';

            $stmt = mysqli_prepare($this->conectar, $query);
            mysqli_stmt_bind_param($stmt, 'i', $idPago);
            $result = mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);

            if (!$result) {
                throw new Exception('Error al actualizar el estado del pago');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Pago rechazado correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al rechazar el pago: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reactivar un pago rechazado
     */
    public function reactivarPagoPendiente()
    {
        // Verificamos si hay una sesión activa
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Sesión no iniciada'
            ]);
            return;
        }

        // Verificamos si se recibió el ID del pago
        if (!isset($_POST['idPago']) || empty($_POST['idPago'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de pago no especificado'
            ]);
            return;
        }

        $idPago = $_POST['idPago'];

        try {
            // Actualizamos el estado del pago a pendiente (0)
            $query = 'UPDATE pagos_financiamiento SET estado = 0 WHERE idpagos_financiamiento = ?';

            $stmt = mysqli_prepare($this->conectar, $query);
            mysqli_stmt_bind_param($stmt, 'i', $idPago);
            $result = mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);

            if (!$result) {
                throw new Exception('Error al actualizar el estado del pago');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Pago reactivado correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al reactivar el pago: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar un pago rechazado
     */
    public function eliminarPagoPendiente()
    {
        // Verificamos si hay una sesión activa
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Sesión no iniciada'
            ]);
            return;
        }

        // Verificamos si se recibió el ID del pago
        if (!isset($_POST['idPago']) || empty($_POST['idPago'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de pago no especificado'
            ]);
            return;
        }

        $idPago = $_POST['idPago'];
        $dbConnection = $this->conectar;

        // Iniciar transacción
        mysqli_begin_transaction($dbConnection);

        try {
            // 1. Obtener los id_cuota del detalle del pago antes de eliminarlo
            $cuotasIds = [];
            $querySelect = 'SELECT id_cuota FROM detalle_pago_financiamiento WHERE idfinanciamiento = ?';
            $stmtSelect = mysqli_prepare($dbConnection, $querySelect);
            mysqli_stmt_bind_param($stmtSelect, 'i', $idPago);
            mysqli_stmt_execute($stmtSelect);
            $resultSelect = mysqli_stmt_get_result($stmtSelect);
            while ($row = mysqli_fetch_assoc($resultSelect)) {
                $cuotasIds[] = $row['id_cuota'];
            }
            mysqli_stmt_close($stmtSelect);

            // 2. Eliminar de la tabla pagos_pendientes_financiamientos (si aplica)
            $queryDeletePendiente = 'DELETE FROM pagos_pendientes_financiamientos WHERE idpagos_financiamiento = ?';
            $stmtDeletePendiente = mysqli_prepare($dbConnection, $queryDeletePendiente);
            mysqli_stmt_bind_param($stmtDeletePendiente, 'i', $idPago);
            mysqli_stmt_execute($stmtDeletePendiente);
            mysqli_stmt_close($stmtDeletePendiente);

            // 3. Eliminar de la tabla detalle_pago_financiamiento
            $queryDeleteDetalle = 'DELETE FROM detalle_pago_financiamiento WHERE idfinanciamiento = ?';
            $stmtDeleteDetalle = mysqli_prepare($dbConnection, $queryDeleteDetalle);
            mysqli_stmt_bind_param($stmtDeleteDetalle, 'i', $idPago);
            mysqli_stmt_execute($stmtDeleteDetalle);
            mysqli_stmt_close($stmtDeleteDetalle);

            // 4. Eliminar de la tabla pagos_financiamiento
            $queryDeletePago = 'DELETE FROM pagos_financiamiento WHERE idpagos_financiamiento = ?';
            $stmtDeletePago = mysqli_prepare($dbConnection, $queryDeletePago);
            mysqli_stmt_bind_param($stmtDeletePago, 'i', $idPago);
            mysqli_stmt_execute($stmtDeletePago);
            mysqli_stmt_close($stmtDeletePago);

            // 5. Actualizar el estado de las cuotas a 'En Progreso'
            if (!empty($cuotasIds)) {
                $placeholders = implode(',', array_fill(0, count($cuotasIds), '?'));
                $query_update_cuotas = "UPDATE cuotas_financiamiento SET estado = 'En Progreso', fecha_pago = NULL WHERE idcuotas_financiamiento IN (
 $placeholders)";

                $stmtUpdate = mysqli_prepare($dbConnection, $query_update_cuotas);

                $types = str_repeat('i', count($cuotasIds));
                mysqli_stmt_bind_param($stmtUpdate, $types, ...$cuotasIds);

                mysqli_stmt_execute($stmtUpdate);
                mysqli_stmt_close($stmtUpdate);
            }

            // Si todo fue exitoso, confirma los cambios
            mysqli_commit($dbConnection);

            echo json_encode([
                'success' => true,
                'message' => 'Pago eliminado y cuotas revertidas correctamente'
            ]);
        } catch (Exception $e) {
            // Si algo falló, revierte todos los cambios
            mysqli_rollback($dbConnection);
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar el pago: ' . $e->getMessage()
            ]);
        }
    }
}
