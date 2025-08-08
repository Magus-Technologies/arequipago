<?php

require_once 'app/models/Financiamiento.php';

class PagosController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar=(new Conexion())->getConexion();
    }

    public function render(){
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
    public function getAllByIdCompra(){


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
    
        $sql = "SELECT COUNT(*) AS total FROM pagos_financiamiento WHERE estado = 0";
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
        
        try {
            // Consulta base para obtener pagos pendientes (estado = 0)
            $query = "SELECT p.*, 
                    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS conductor, 
                    CONCAT(COALESCE(u.nombres, ''), ' ', COALESCE(u.apellidos, '')) AS asesor,
                    c.numUnidad AS numUnidad 
                FROM pagos_financiamiento p
                LEFT JOIN conductores c ON p.id_conductor = c.id_conductor
                LEFT JOIN usuarios u ON p.id_asesor = u.usuario_id
                LEFT JOIN clientes_financiar cf ON p.id_cliente = cf.id
                WHERE p.estado = 0";
                
            // Verificamos si hay clientes (no conductores)
            $query .= " ORDER BY p.fecha_pago DESC";
            
            // Preparamos la consulta
            $stmt = mysqli_prepare($this->conectar, $query);
            
            // Ejecutamos la consulta
            mysqli_stmt_execute($stmt);
            
            // Obtenemos el resultado
            $result = mysqli_stmt_get_result($stmt);
            
            // Inicializamos array para almacenar los datos
            $pagos = [];
            
            // Procesamos cada registro
            while ($row = mysqli_fetch_assoc($result)) {
                // Si no hay conductor, buscamos el cliente
                if (empty($row['conductor']) && !empty($row['id_cliente'])) {
                    // Consulta para obtener datos del cliente
                    $queryCliente = "SELECT CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) AS cliente 
                                     FROM clientes_financiar 
                                     WHERE id = ?";
                    
                    $stmtCliente = mysqli_prepare($this->conectar, $queryCliente);
                    mysqli_stmt_bind_param($stmtCliente, "i", $row['id_cliente']);
                    mysqli_stmt_execute($stmtCliente);
                    
                    $resultCliente = mysqli_stmt_get_result($stmtCliente);
                    if ($cliente = mysqli_fetch_assoc($resultCliente)) {
                        $row['cliente'] = $cliente['cliente'];
                    }
                    
                    mysqli_stmt_close($stmtCliente);
                }
                
                $pagos[] = $row;
            }
            
            // Cerramos el statement
            mysqli_stmt_close($stmt);
            
            // Devolvemos los resultados
            echo json_encode([
                'success' => true,
                'data' => $pagos
            ]);
            
        } catch (Exception $e) {
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
        
        try {
            // Consulta base para obtener pagos rechazados (estado = 2)
            $query = "SELECT p.*, 
                    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS conductor, 
                    CONCAT(u.nombres, ' ', u.apellidos) AS asesor,
                    c.numUnidad AS numUnidad 
                FROM pagos_financiamiento p
                LEFT JOIN conductores c ON p.id_conductor = c.id_conductor
                LEFT JOIN usuarios u ON p.id_asesor = u.usuario_id
                WHERE p.estado = 2";
                
            $query .= " ORDER BY p.fecha_pago DESC";
            
            // Preparamos la consulta
            $stmt = mysqli_prepare($this->conectar, $query);
            
            // Ejecutamos la consulta
            mysqli_stmt_execute($stmt);
            
            // Obtenemos el resultado
            $result = mysqli_stmt_get_result($stmt);
            
            // Inicializamos array para almacenar los datos
            $pagos = [];
            
            // Procesamos cada registro
            while ($row = mysqli_fetch_assoc($result)) {
                // Si no hay conductor, buscamos el cliente
                if (empty($row['conductor']) && !empty($row['id_cliente'])) {
                    // Consulta para obtener datos del cliente
                    $queryCliente = "SELECT CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) AS cliente 
                                     FROM clientes_financiar 
                                     WHERE id = ?";
                    
                    $stmtCliente = mysqli_prepare($this->conectar, $queryCliente);
                    mysqli_stmt_bind_param($stmtCliente, "i", $row['id_cliente']);
                    mysqli_stmt_execute($stmtCliente);
                    
                    $resultCliente = mysqli_stmt_get_result($stmtCliente);
                    if ($cliente = mysqli_fetch_assoc($resultCliente)) {
                        $row['cliente'] = $cliente['cliente'];
                    }
                    
                    mysqli_stmt_close($stmtCliente);
                }
                
                $pagos[] = $row;
            }
            
            // Cerramos el statement
            mysqli_stmt_close($stmt);
            
            // Devolvemos los resultados
            echo json_encode([
                'success' => true,
                'data' => $pagos
            ]);
            
        } catch (Exception $e) {
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
            mysqli_stmt_bind_param($stmtPago, "i", $idPago);
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
            $queryPendientes = "SELECT cuotas_json 
                                FROM pagos_pendientes_financiamientos 
                                WHERE idpagos_financiamiento = ?";
            
            $stmtPendientes = mysqli_prepare($this->conectar, $queryPendientes);
            mysqli_stmt_bind_param($stmtPendientes, "i", $idPago);
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
            
            // 4. Para cada cuota, obtenemos su información completa
            foreach ($cuotasSeleccionadas as $cuota) {
                $idCuota = $cuota['idCuota'];
                
                $queryCuota = "SELECT cf.*, f.idproductosv2, f.id_variante, f.grupo_financiamiento 
                               FROM cuotas_financiamiento cf
                               INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento 
                               WHERE cf.idcuotas_financiamiento = ?";
                
                $stmtCuota = mysqli_prepare($this->conectar, $queryCuota);
                mysqli_stmt_bind_param($stmtCuota, "i", $idCuota);
                mysqli_stmt_execute($stmtCuota);
                
                $resultCuota = mysqli_stmt_get_result($stmtCuota);
                $cuotaInfo = mysqli_fetch_assoc($resultCuota);
                
                mysqli_stmt_close($stmtCuota);
                
                if ($cuotaInfo) {
                    // Agregamos al arreglo de cuotas completas
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
                    
                    // Guardamos los IDs para buscar producto y grupo de financiamiento
                    $idProducto = $cuotaInfo['idproductosv2'];
                    $idVariante = $cuotaInfo['id_variante'];
                    $grupoFinanciamiento = $cuotaInfo['grupo_financiamiento'];
                }
            }
            
            // 5. Obtenemos información del producto
            $nombreProducto = 'Producto no especificado';
            if (!empty($idProducto)) {
                $queryProducto = "SELECT nombre FROM productosv2 WHERE idproductosv2 = ?";
                
                $stmtProducto = mysqli_prepare($this->conectar, $queryProducto);
                mysqli_stmt_bind_param($stmtProducto, "i", $idProducto);
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
                $queryGrupo = "SELECT nombre_variante FROM grupos_variantes WHERE idgrupos_variantes = ?";
                
                $stmtGrupo = mysqli_prepare($this->conectar, $queryGrupo);
                mysqli_stmt_bind_param($stmtGrupo, "i", $idVariante);
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
                $queryGrupo = "SELECT nombre_plan FROM planes_financiamiento WHERE idplan_financiamiento = ?";
                
                $stmtGrupo = mysqli_prepare($this->conectar, $queryGrupo);
                mysqli_stmt_bind_param($stmtGrupo, "i", $grupoFinanciamiento);
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
                    'moneda' => $pago['moneda']
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
         
         try {
             // Iniciamos una transacción
             mysqli_begin_transaction($this->conectar);
             
             // 1. Actualizamos el estado del pago a aprobado (1)
             $queryPago = "UPDATE pagos_financiamiento SET estado = 1 WHERE idpagos_financiamiento = ?";
             
             $stmtPago = mysqli_prepare($this->conectar, $queryPago);
             mysqli_stmt_bind_param($stmtPago, "i", $idPago);
             $resultPago = mysqli_stmt_execute($stmtPago);
             
             mysqli_stmt_close($stmtPago);
             
             if (!$resultPago) {
                 throw new Exception("Error al actualizar el estado del pago");
             }
             
             // 2. Obtenemos las cuotas seleccionadas de pagos_pendientes_financiamientos
             $queryPendientes = "SELECT cuotas_json FROM pagos_pendientes_financiamientos WHERE idpagos_financiamiento = ?";
             
             $stmtPendientes = mysqli_prepare($this->conectar, $queryPendientes);
             mysqli_stmt_bind_param($stmtPendientes, "i", $idPago);
             mysqli_stmt_execute($stmtPendientes);
             
             $resultPendientes = mysqli_stmt_get_result($stmtPendientes);
             $pendiente = mysqli_fetch_assoc($resultPendientes);
             
             mysqli_stmt_close($stmtPendientes);
             
             if (!$pendiente) {
                 throw new Exception("No se encontraron detalles de cuotas para este pago");
             }
             
             // 3. Decodificamos el JSON de cuotas
             $cuotasJson = $pendiente['cuotas_json'];
             $cuotasSeleccionadas = json_decode($cuotasJson, true);
             
             // 4. Actualizamos el usuario que aprobó el pago
             $queryUsuario = "UPDATE pagos_pendientes_financiamientos SET id_usuario_aprobacion = ? WHERE idpagos_financiamiento = ?";
             
             $stmtUsuario = mysqli_prepare($this->conectar, $queryUsuario);
             mysqli_stmt_bind_param($stmtUsuario, "ii", $idUsuario, $idPago);
             $resultUsuario = mysqli_stmt_execute($stmtUsuario);
             
             mysqli_stmt_close($stmtUsuario);
             
             if (!$resultUsuario) {
                 throw new Exception("Error al actualizar el usuario de aprobación");
             }
             
         
             $financiamientoModel = new Financiamiento();
             $resultado = $financiamientoModel->actualizarCuotas($cuotasSeleccionadas);
             
             if (!$resultado) {
                 throw new Exception("Error al actualizar las cuotas en el modelo");
             }
             
             // Confirmamos la transacción
             mysqli_commit($this->conectar);
             
             echo json_encode([
                 'success' => true,
                 'message' => 'Pago aprobado correctamente'
             ]);
             
         } catch (Exception $e) {
             // Revertimos la transacción en caso de error
             mysqli_rollback($this->conectar);
             
             echo json_encode([
                 'success' => false,
                 'message' => 'Error al aprobar el pago: ' . $e->getMessage()
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
            $query = "UPDATE pagos_financiamiento SET estado = 2 WHERE idpagos_financiamiento = ?";
            
            $stmt = mysqli_prepare($this->conectar, $query);
            mysqli_stmt_bind_param($stmt, "i", $idPago);
            $result = mysqli_stmt_execute($stmt);
            
            mysqli_stmt_close($stmt);
            
            if (!$result) {
                throw new Exception("Error al actualizar el estado del pago");
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
            $query = "UPDATE pagos_financiamiento SET estado = 0 WHERE idpagos_financiamiento = ?";
            
            $stmt = mysqli_prepare($this->conectar, $query);
            mysqli_stmt_bind_param($stmt, "i", $idPago);
            $result = mysqli_stmt_execute($stmt);
            
            mysqli_stmt_close($stmt);
            
            if (!$result) {
                throw new Exception("Error al actualizar el estado del pago");
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
        
        try {
            // Eliminamos el pago (las tablas relacionadas se eliminarán por cascade)
            $query = "DELETE FROM pagos_financiamiento WHERE idpagos_financiamiento = ?";
            
            $stmt = mysqli_prepare($this->conectar, $query);
            mysqli_stmt_bind_param($stmt, "i", $idPago);
            $result = mysqli_stmt_execute($stmt);
            
            mysqli_stmt_close($stmt);
            
            if (!$result) {
                throw new Exception("Error al eliminar el pago");
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Pago eliminado correctamente'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar el pago: ' . $e->getMessage()
            ]);
        }
    }
}
