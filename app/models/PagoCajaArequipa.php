<?php

class PagoCajaArequipa
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * Obtener todas las recaudaciones de Caja Arequipa
     * @param array $filtros - Array con filtros opcionales (fecha_inicio, fecha_fin, estado, moneda)
     * @return array - Lista de recaudaciones
     */
    public function obtenerRecaudaciones($filtros = [])
    {
        try {
            $sql = "SELECT
                        pca.id,
                        pca.id_financiamiento,
                        pca.id_cuota,
                        pca.numero_trace,
                        pca.fecha_pago,
                        pca.fecha_extorno,
                        pca.canal,
                        pca.moneda,
                        pca.monto,
                        pca.monto_original,
                        pca.mora,
                        pca.comision_asumida,
                        pca.monto_recibido,
                        pca.numero_documento,
                        pca.periodo,
                        pca.estado,
                        pca.id_venta,
                        pca.facturado,
                        pca.fecha_facturacion,
                        pca.created_at,
                        pca.updated_at,
                        CASE
                            WHEN f.id_cliente IS NOT NULL AND f.id_cliente > 0 THEN CONCAT(cf.nombres, ' ', cf.apellido_paterno)
                            WHEN f.id_conductor IS NOT NULL AND f.id_conductor > 0 THEN CONCAT(cond.nombres, ' ', cond.apellido_paterno)
                            ELSE 'N/A'
                        END as nombre_cliente,
                        CASE
                            WHEN f.id_cliente IS NOT NULL AND f.id_cliente > 0 THEN cf.n_documento
                            WHEN f.id_conductor IS NOT NULL AND f.id_conductor > 0 THEN cond.nro_documento
                            ELSE 'N/A'
                        END as dni_cliente,
                        CASE
                            WHEN f.id_cliente IS NOT NULL AND f.id_cliente > 0 THEN cf.telefono
                            WHEN f.id_conductor IS NOT NULL AND f.id_conductor > 0 THEN cond.telefono
                            ELSE 'N/A'
                        END as celular_cliente,
                        f.codigo_asociado,
                        f.grupo_financiamiento as nombre_plan
                    FROM pagos_caja_arequipa pca
                    LEFT JOIN financiamiento f ON pca.id_financiamiento = f.idfinanciamiento
                    LEFT JOIN clientes_financiar cf ON f.id_cliente = cf.id
                    LEFT JOIN conductores cond ON f.id_conductor = cond.id_conductor
                    WHERE 1=1";

            // Filtro por fecha
            if (!empty($filtros['fecha_inicio'])) {
                $fecha_inicio = $this->conectar->real_escape_string($filtros['fecha_inicio']);
                $sql .= " AND pca.fecha_pago >= '$fecha_inicio'";
            }

            if (!empty($filtros['fecha_fin'])) {
                $fecha_fin = $this->conectar->real_escape_string($filtros['fecha_fin']);
                $sql .= " AND pca.fecha_pago <= '$fecha_fin'";
            }

            // Filtro por estado
            if (!empty($filtros['estado'])) {
                $estado = $this->conectar->real_escape_string($filtros['estado']);
                $sql .= " AND pca.estado = '$estado'";
            }

            // Filtro por moneda
            if (!empty($filtros['moneda'])) {
                $moneda = $this->conectar->real_escape_string($filtros['moneda']);
                $sql .= " AND pca.moneda = '$moneda'";
            }

            // Filtro por comisión asumida
            if (!empty($filtros['comision_asumida'])) {
                if ($filtros['comision_asumida'] === 'con') {
                    $sql .= " AND pca.comision_asumida > 0";
                } elseif ($filtros['comision_asumida'] === 'sin') {
                    $sql .= " AND pca.comision_asumida = 0";
                }
            }

            // Filtro por búsqueda general (nombre, dni, código)
            if (!empty($filtros['busqueda'])) {
                $busqueda = $this->conectar->real_escape_string($filtros['busqueda']);
                $sql .= " AND (
                    cf.nombres LIKE '%$busqueda%'
                    OR cf.apellido_paterno LIKE '%$busqueda%'
                    OR cf.n_documento LIKE '%$busqueda%'
                    OR cond.nombres LIKE '%$busqueda%'
                    OR cond.apellido_paterno LIKE '%$busqueda%'
                    OR cond.nro_documento LIKE '%$busqueda%'
                    OR f.codigo_asociado LIKE '%$busqueda%'
                    OR pca.numero_trace LIKE '%$busqueda%'
                )";
            }

            $sql .= " ORDER BY pca.fecha_pago DESC, pca.created_at DESC";

            $result = mysqli_query($this->conectar, $sql);

            if (!$result) {
                error_log("Error en consulta SQL: " . mysqli_error($this->conectar));
                return [];
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerRecaudaciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener resumen de recaudaciones
     * @param array $filtros - Array con filtros opcionales
     * @return array - Resumen con totales por moneda
     */
    public function obtenerResumenRecaudaciones($filtros = [])
    {
        try {
            $sql = "SELECT
                        pca.moneda,
                        COUNT(*) as total_transacciones,
                        SUM(CASE WHEN pca.estado = 'PAGADO' THEN 1 ELSE 0 END) as total_pagados,
                        SUM(CASE WHEN pca.estado = 'EXTORNADO' THEN 1 ELSE 0 END) as total_extornados,
                        -- Total pagado por clientes
                        SUM(CASE WHEN pca.estado = 'PAGADO' THEN pca.monto ELSE 0 END) as total_monto,
                        -- Comisión que asumió Arequipago (solo algunos productos)
                        SUM(CASE WHEN pca.estado = 'PAGADO' THEN pca.comision_asumida ELSE 0 END) as total_comision_asumida,
                        -- Comisión total que cobra Caja Arequipa (0.50 PEN o 0.20 USD por transacción)
                        SUM(CASE
                            WHEN pca.estado = 'PAGADO' AND pca.moneda = 'PEN' THEN 0.50
                            WHEN pca.estado = 'PAGADO' AND pca.moneda = 'USD' THEN 0.20
                            ELSE 0
                        END) as total_comision_caja_arequipa,
                        -- Monto neto recibido por Arequipago (calculado si es NULL)
                        SUM(CASE
                            WHEN pca.estado = 'PAGADO' THEN
                                COALESCE(pca.monto_recibido,
                                    CASE
                                        WHEN pca.moneda = 'PEN' THEN pca.monto - 0.50
                                        WHEN pca.moneda = 'USD' THEN pca.monto - 0.20
                                        ELSE pca.monto
                                    END
                                )
                            ELSE 0
                        END) as total_recibido
                    FROM pagos_caja_arequipa pca
                    WHERE 1=1";

            if (!empty($filtros['fecha_inicio'])) {
                $fecha_inicio = $this->conectar->real_escape_string($filtros['fecha_inicio']);
                $sql .= " AND pca.fecha_pago >= '$fecha_inicio'";
            }

            if (!empty($filtros['fecha_fin'])) {
                $fecha_fin = $this->conectar->real_escape_string($filtros['fecha_fin']);
                $sql .= " AND pca.fecha_pago <= '$fecha_fin'";
            }

            if (!empty($filtros['estado'])) {
                $estado = $this->conectar->real_escape_string($filtros['estado']);
                $sql .= " AND pca.estado = '$estado'";
            }

            // Filtro por comisión asumida
            if (!empty($filtros['comision_asumida'])) {
                if ($filtros['comision_asumida'] === 'con') {
                    $sql .= " AND pca.comision_asumida > 0";
                } elseif ($filtros['comision_asumida'] === 'sin') {
                    $sql .= " AND pca.comision_asumida = 0";
                }
            }

            $sql .= " GROUP BY pca.moneda";

            $result = mysqli_query($this->conectar, $sql);

            if (!$result) {
                error_log("Error en consulta SQL resumen: " . mysqli_error($this->conectar));
                return [];
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerResumenRecaudaciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener detalle de una recaudación específica
     * @param int $id - ID de la recaudación
     * @return array|null - Detalle de la recaudación
     */
    public function obtenerDetallePorId($id)
    {
        try {
            $id = $this->conectar->real_escape_string($id);

            $sql = "SELECT
                        pca.*,
                        -- Información del cliente
                        CASE
                            WHEN f.id_cliente IS NOT NULL AND f.id_cliente > 0 THEN CONCAT(cf.nombres, ' ', cf.apellido_paterno)
                            WHEN f.id_conductor IS NOT NULL AND f.id_conductor > 0 THEN CONCAT(cond.nombres, ' ', cond.apellido_paterno)
                            ELSE 'N/A'
                        END as nombre_cliente,
                        CASE
                            WHEN f.id_cliente IS NOT NULL AND f.id_cliente > 0 THEN cf.n_documento
                            WHEN f.id_conductor IS NOT NULL AND f.id_conductor > 0 THEN cond.nro_documento
                            ELSE 'N/A'
                        END as dni_cliente,
                        CASE
                            WHEN f.id_cliente IS NOT NULL AND f.id_cliente > 0 THEN cf.telefono
                            WHEN f.id_conductor IS NOT NULL AND f.id_conductor > 0 THEN cond.telefono
                            ELSE 'N/A'
                        END as celular_cliente,
                        -- Información del financiamiento
                        f.idfinanciamiento as id_financiamiento,
                        f.codigo_asociado,
                        f.monto_total as monto_total_financiamiento,
                        (SELECT COUNT(*) FROM cuotas_financiamiento WHERE id_financiamiento = f.idfinanciamiento) as total_cuotas_financiamiento,
                        f.fecha_inicio as fecha_inicio_financiamiento,
                        -- Información del plan
                        pf.nombre_plan,
                        pf.idplan_financiamiento,
                        -- Información de la cuota
                        cuota.numero_cuota,
                        cuota.monto as monto_cuota,
                        cuota.fecha_vencimiento as fecha_vencimiento_cuota,
                        cuota.estado as estado_cuota,
                        -- Información de la factura (si existe)
                        v.id_venta,
                        v.serie as factura_serie,
                        v.numero as factura_numero,
                        v.fecha_emision as factura_fecha,
                        v.total as factura_total,
                        v.enviado_sunat as factura_enviado_sunat,
                        ds.nombre as factura_tipo_nombre,
                        ds.abreviatura as factura_tipo_abrev
                    FROM pagos_caja_arequipa pca
                    LEFT JOIN financiamiento f ON pca.id_financiamiento = f.idfinanciamiento
                    LEFT JOIN clientes_financiar cf ON f.id_cliente = cf.id
                    LEFT JOIN conductores cond ON f.id_conductor = cond.id_conductor
                    LEFT JOIN planes_financiamiento pf ON f.grupo_financiamiento = pf.idplan_financiamiento
                    LEFT JOIN cuotas_financiamiento cuota ON pca.id_cuota = cuota.idcuotas_financiamiento
                    LEFT JOIN ventas v ON pca.id_venta = v.id_venta
                    LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
                    WHERE pca.id = '$id'";

            $result = mysqli_query($this->conectar, $sql);

            if (!$result) {
                error_log("Error en consulta SQL detalle: " . mysqli_error($this->conectar));
                return null;
            }

            return mysqli_fetch_assoc($result);
        } catch (Exception $e) {
            error_log("Error en obtenerDetallePorId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Marcar pago como facturado
     * @param int $id - ID del pago
     * @param int $id_venta - ID de la venta generada
     * @return bool
     */
    public function marcarComoFacturado($id, $id_venta)
    {
        try {
            $id = $this->conectar->real_escape_string($id);
            $id_venta = $this->conectar->real_escape_string($id_venta);
            
            $sql = "UPDATE pagos_caja_arequipa 
                    SET id_venta = '$id_venta',
                        facturado = 1,
                        fecha_facturacion = NOW()
                    WHERE id = '$id'";
            
            $result = mysqli_query($this->conectar, $sql);
            
            if (!$result) {
                error_log("Error al marcar como facturado: " . mysqli_error($this->conectar));
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error en marcarComoFacturado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Desmarcar pago como facturado (en caso de anulación)
     * @param int $id - ID del pago
     * @return bool
     */
    public function desmarcarFacturado($id)
    {
        try {
            $id = $this->conectar->real_escape_string($id);
            
            $sql = "UPDATE pagos_caja_arequipa 
                    SET id_venta = NULL,
                        facturado = 0,
                        fecha_facturacion = NULL
                    WHERE id = '$id'";
            
            $result = mysqli_query($this->conectar, $sql);
            
            if (!$result) {
                error_log("Error al desmarcar facturado: " . mysqli_error($this->conectar));
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error en desmarcarFacturado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si un pago ya está facturado
     * @param int $id - ID del pago
     * @return bool
     */
    public function estaFacturado($id)
    {
        try {
            $id = $this->conectar->real_escape_string($id);
            
            $sql = "SELECT facturado FROM pagos_caja_arequipa WHERE id = '$id'";
            $result = mysqli_query($this->conectar, $sql);
            
            if ($result && $row = mysqli_fetch_assoc($result)) {
                return (bool)$row['facturado'];
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error en estaFacturado: " . $e->getMessage());
            return false;
        }
    }
}
