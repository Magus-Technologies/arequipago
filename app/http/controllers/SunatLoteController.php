<?php

require_once "app/models/Venta.php";
require_once "app/clases/SunatApi.php";

class SunatLoteController extends Controller
{
    private $venta;
    private $sunatApi;
    private $conexion;

    public function __construct()
    {
        $this->venta = new Venta();
        $this->sunatApi = new SunatApi();
        $this->conexion = (new Conexion())->getConexion();
    }

    /**
     * Obtiene los comprobantes pendientes de envío a SUNAT
     */
    public function obtenerComprobantesPendientes()
    {
        try {
            // Validar permisos (solo Director=3 y Contador=4)
            if (!isset($_SESSION["id_rol"]) || !in_array($_SESSION["id_rol"], [3, 4])) {
                return json_encode([
                    "res" => false,
                    "mensaje" => "No tiene permisos para realizar esta acción"
                ]);
            }

            $filtros = [
                "fecha_desde" => $_POST["fecha_desde"] ?? null,
                "fecha_hasta" => $_POST["fecha_hasta"] ?? null,
                "tipo_doc" => $_POST["tipo_doc"] ?? null, // 1=Boleta, 2=Factura
                "sucursal" => $_SESSION["sucursal"] ?? null
            ];

            $sql = "SELECT 
                        v.id_venta,
                        v.fecha_emision,
                        ds.abreviatura as tipo_doc,
                        v.serie,
                        v.numero,
                        CONCAT(v.serie, '-', v.numero) as comprobante,
                        c.documento as doc_cliente,
                        c.datos as nombre_cliente,
                        v.total,
                        vs.nombre_xml,
                        v.id_empresa
                    FROM ventas v
                    LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
                    LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                    LEFT JOIN ventas_sunat vs ON v.id_venta = vs.id_venta
                    WHERE v.enviado_sunat = 0 
                    AND v.estado = 1
                    AND v.id_tido IN (1, 2)"; // Solo Boletas y Facturas

            // Aplicar filtros
            if ($filtros["fecha_desde"]) {
                $sql .= " AND v.fecha_emision >= '{$filtros["fecha_desde"]}'";
            }
            if ($filtros["fecha_hasta"]) {
                $sql .= " AND v.fecha_emision <= '{$filtros["fecha_hasta"]}'";
            }
            if ($filtros["tipo_doc"]) {
                $sql .= " AND v.id_tido = '{$filtros["tipo_doc"]}'";
            }
            if ($filtros["sucursal"]) {
                $sql .= " AND v.sucursal = '{$filtros["sucursal"]}'";
            }

            $sql .= " ORDER BY v.fecha_emision ASC, v.id_venta ASC LIMIT 100";

            $result = $this->conexion->query($sql);
            $pendientes = [];

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $pendientes[] = $row;
                }
            }

            return json_encode([
                "res" => true,
                "data" => $pendientes,
                "total" => count($pendientes)
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerComprobantesPendientes: " . $e->getMessage());
            return json_encode([
                "res" => false,
                "mensaje" => "Error al obtener comprobantes pendientes: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Procesa el envío masivo de comprobantes a SUNAT
     */
    public function procesarLote()
    {
        try {
            // Validar permisos (solo Director=3 y Contador=4)
            if (!isset($_SESSION["id_rol"]) || !in_array($_SESSION["id_rol"], [3, 4])) {
                return json_encode([
                    "res" => false,
                    "mensaje" => "No tiene permisos para realizar esta acción"
                ]);
            }

            $comprobantes = json_decode($_POST["comprobantes"], true);
            
            if (empty($comprobantes)) {
                return json_encode([
                    "res" => false,
                    "mensaje" => "No se recibieron comprobantes para procesar"
                ]);
            }

            $resultados = [
                "exitosos" => 0,
                "fallidos" => 0,
                "detalles" => []
            ];

            foreach ($comprobantes as $comprobante) {
                $id_venta = $comprobante["id_venta"];
                $nombre_xml = $comprobante["nombre_xml"];
                $id_empresa = $comprobante["id_empresa"];

                try {
                    // Verificar que existe el XML
                    if (empty($nombre_xml)) {
                        throw new Exception("No se encontró XML generado");
                    }

                    // Enviar a SUNAT
                    $envioExitoso = $this->sunatApi->envioIndividualDocumentoVPorEmpresa(
                        $nombre_xml,
                        $id_empresa
                    );

                    if ($envioExitoso) {
                        // Actualizar estado en BD
                        $sql = "UPDATE ventas SET enviado_sunat = 1 WHERE id_venta = $id_venta";
                        $this->conexion->query($sql);

                        $resultados["exitosos"]++;
                        $resultados["detalles"][] = [
                            "id_venta" => $id_venta,
                            "comprobante" => $comprobante["comprobante"],
                            "estado" => "exitoso",
                            "mensaje" => "Enviado correctamente"
                        ];
                    } else {
                        throw new Exception($this->sunatApi->getMensaje() ?? "Error desconocido");
                    }

                } catch (Exception $e) {
                    $resultados["fallidos"]++;
                    $resultados["detalles"][] = [
                        "id_venta" => $id_venta,
                        "comprobante" => $comprobante["comprobante"],
                        "estado" => "error",
                        "mensaje" => $e->getMessage()
                    ];

                    error_log("Error al enviar comprobante $id_venta: " . $e->getMessage());
                }

                // Pequeña pausa entre envíos para no saturar SUNAT
                usleep(500000); // 0.5 segundos
            }

            // Registrar en log
            $this->registrarLog($resultados);

            return json_encode([
                "res" => true,
                "resultados" => $resultados,
                "mensaje" => "Proceso completado: {$resultados["exitosos"]} enviados, {$resultados["fallidos"]} fallidos"
            ]);

        } catch (Exception $e) {
            error_log("Error en procesarLote: " . $e->getMessage());
            return json_encode([
                "res" => false,
                "mensaje" => "Error al procesar lote: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Actualiza la fecha de emisión de una venta y regenera el XML
     * COMENTADO: No se usa actualmente para evitar problemas con XMLs
     */
    /*
    private function actualizarFechaYRegenerarXML($id_venta, $nueva_fecha)
    {
        // Actualizar fecha en tabla ventas
        $sql = "UPDATE ventas SET fecha_emision = '$nueva_fecha', fecha_vencimiento = '$nueva_fecha' WHERE id_venta = $id_venta";
        if (!$this->conexion->query($sql)) {
            throw new Exception("Error al actualizar fecha de emisión");
        }

        // Regenerar XML usando el método regenerarXML del controlador de ventas
        require_once "app/http/controllers/VentasController.php";
        $ventasController = new VentasController();
        
        $_POST["venta"] = $id_venta;
        $resultado = json_decode($ventasController->regenerarXML(), true);
        
        if (!$resultado["res"]) {
            throw new Exception("Error al regenerar XML");
        }
    }
    */

    /**
     * Actualiza fechas de emisión de múltiples comprobantes SIN enviar a SUNAT
     * COMENTADO: No se usa actualmente para evitar problemas con XMLs
     */
    /*
    public function actualizarFechasLote()
    {
        try {
            // Validar permisos (solo Director=3 y Contador=4)
            if (!isset($_SESSION["id_rol"]) || !in_array($_SESSION["id_rol"], [3, 4])) {
                return json_encode([
                    "res" => false,
                    "mensaje" => "No tiene permisos para realizar esta acción"
                ]);
            }

            $comprobantes = json_decode($_POST["comprobantes"], true);
            $nueva_fecha = $_POST["nueva_fecha"] ?? null;
            
            if (empty($comprobantes)) {
                return json_encode([
                    "res" => false,
                    "mensaje" => "No se recibieron comprobantes para procesar"
                ]);
            }

            if (empty($nueva_fecha)) {
                return json_encode([
                    "res" => false,
                    "mensaje" => "Debe especificar una nueva fecha de emisión"
                ]);
            }

            // Validar nueva fecha de emisión
            $fecha_minima = date('Y-m-d', strtotime('-5 days'));
            $fecha_actual = date('Y-m-d');

            if ($nueva_fecha < $fecha_minima) {
                return json_encode([
                    "res" => false,
                    "mensaje" => "La fecha de emisión no puede ser mayor a 5 días atrás"
                ]);
            }

            if ($nueva_fecha > $fecha_actual) {
                return json_encode([
                    "res" => false,
                    "mensaje" => "La fecha de emisión no puede ser futura"
                ]);
            }

            $resultados = [
                "exitosos" => 0,
                "fallidos" => 0,
                "detalles" => []
            ];

            foreach ($comprobantes as $comprobante) {
                $id_venta = $comprobante["id_venta"];

                try {
                    // Actualizar fecha y regenerar XML
                    $this->actualizarFechaYRegenerarXML($id_venta, $nueva_fecha);

                    $resultados["exitosos"]++;
                    $resultados["detalles"][] = [
                        "id_venta" => $id_venta,
                        "comprobante" => $comprobante["comprobante"],
                        "estado" => "exitoso",
                        "mensaje" => "Fecha actualizada a $nueva_fecha y XML regenerado correctamente"
                    ];

                } catch (Exception $e) {
                    $resultados["fallidos"]++;
                    $resultados["detalles"][] = [
                        "id_venta" => $id_venta,
                        "comprobante" => $comprobante["comprobante"],
                        "estado" => "error",
                        "mensaje" => $e->getMessage()
                    ];

                    error_log("Error al actualizar fecha del comprobante $id_venta: " . $e->getMessage());
                }
            }

            return json_encode([
                "res" => true,
                "resultados" => $resultados,
                "mensaje" => "Actualización completada: {$resultados["exitosos"]} exitosos, {$resultados["fallidos"]} fallidos"
            ]);

        } catch (Exception $e) {
            error_log("Error en actualizarFechasLote: " . $e->getMessage());
            return json_encode([
                "res" => false,
                "mensaje" => "Error al actualizar fechas: " . $e->getMessage()
            ]);
        }
    }
    */

    /**
     * Obtiene estadísticas de envíos a SUNAT
     */
    public function obtenerEstadisticasEnvio()
    {
        try {
            // Validar permisos
            if (!isset($_SESSION["id_rol"]) || !in_array($_SESSION["id_rol"], [3, 4])) {
                return json_encode([
                    "res" => false,
                    "mensaje" => "No tiene permisos para realizar esta acción"
                ]);
            }

            $sucursal = $_SESSION["sucursal"] ?? null;

            // Total pendientes
            $sql = "SELECT COUNT(*) as total FROM ventas 
                    WHERE enviado_sunat = 0 AND estado = 1 AND id_tido IN (1,2)";
            if ($sucursal) {
                $sql .= " AND sucursal = $sucursal";
            }
            $pendientes = $this->conexion->query($sql)->fetch_assoc()["total"];

            // Total enviados hoy
            $sql = "SELECT COUNT(*) as total FROM ventas 
                    WHERE enviado_sunat = 1 AND estado = 1 AND id_tido IN (1,2)
                    AND DATE(fecha_emision) = CURDATE()";
            if ($sucursal) {
                $sql .= " AND sucursal = $sucursal";
            }
            $enviadosHoy = $this->conexion->query($sql)->fetch_assoc()["total"];

            // Total enviados este mes
            $sql = "SELECT COUNT(*) as total FROM ventas 
                    WHERE enviado_sunat = 1 AND estado = 1 AND id_tido IN (1,2)
                    AND MONTH(fecha_emision) = MONTH(CURDATE())
                    AND YEAR(fecha_emision) = YEAR(CURDATE())";
            if ($sucursal) {
                $sql .= " AND sucursal = $sucursal";
            }
            $enviadosMes = $this->conexion->query($sql)->fetch_assoc()["total"];

            return json_encode([
                "res" => true,
                "estadisticas" => [
                    "pendientes" => $pendientes,
                    "enviados_hoy" => $enviadosHoy,
                    "enviados_mes" => $enviadosMes
                ]
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasEnvio: " . $e->getMessage());
            return json_encode([
                "res" => false,
                "mensaje" => "Error al obtener estadísticas"
            ]);
        }
    }

    /**
     * Registra el resultado del proceso en log
     */
    private function registrarLog($resultados)
    {
        try {
            $usuario_id = $_SESSION["usuario_fac"] ?? 0;
            $fecha = date("Y-m-d H:i:s");
            $resumen = json_encode($resultados);

            $sql = "INSERT INTO sunat_lote_log (usuario_id, fecha_proceso, exitosos, fallidos, resumen) 
                    VALUES ($usuario_id, '$fecha', {$resultados["exitosos"]}, {$resultados["fallidos"]}, '$resumen')";
            
            $this->conexion->query($sql);
        } catch (Exception $e) {
            error_log("Error al registrar log: " . $e->getMessage());
        }
    }
}
