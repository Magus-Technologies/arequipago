<?php

class Adjudicacion
{
    private $conectar;

  public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * Obtener indicadores del dashboard
     */
    public function obtenerIndicadores()
    {
        try {
            $indicadores = [];

            // Total de adjudicados
            $sql = "SELECT COUNT(DISTINCT f.idfinanciamiento) as total
                    FROM financiamiento f
                    INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                    WHERE f.estado_eliminado = 0
                      AND p.categoria IN ('Vehiculo', 'Moto')
                      AND f.estado_entrega IN ('entregado', 'pendiente')";
            $result = $this->conectar->query($sql);
            $indicadores["total_adjudicaciones"] =
                $result->fetch_assoc()["total"] ?? 0;

            // Vehículos disponibles en stock
            $sql = "SELECT COUNT(*) as total
                    FROM productosv2
                    WHERE categoria IN ('Vehiculo', 'Moto')
                      AND cantidad > 0
                      AND estado = 'Activo'";
            $result = $this->conectar->query($sql);
            $indicadores["total_en_stock"] =
                $result->fetch_assoc()["total"] ?? 0;

            // Vehículos entregados
            $sql = "SELECT COUNT(DISTINCT f.idfinanciamiento) as total
                    FROM financiamiento f
                    INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                    WHERE f.estado_eliminado = 0
                      AND p.categoria IN ('Vehiculo', 'Moto')
                      AND f.estado_entrega = 'entregado'";
            $result = $this->conectar->query($sql);
            $indicadores["total_entregados"] =
                $result->fetch_assoc()["total"] ?? 0;

            // SOATS por vencer (próximos 30 días)
            $sql = "SELECT COUNT(DISTINCT p.idproductosv2) as total
                    FROM productosv2 p
                    INNER JOIN caracteristicas_producto cp ON p.idproductosv2 = cp.idproductosv2
                    INNER JOIN financiamiento f ON p.idproductosv2 = f.idproductosv2
                    WHERE cp.nombre_caracteristicas = 'fecha_venc_soat'
                      AND cp.valor_caracteristica IS NOT NULL
                      AND STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                      AND f.estado_entrega = 'entregado'
                      AND f.estado_eliminado = 0";
            $result = $this->conectar->query($sql);
            $indicadores["total_soat_por_vencer"] =
                $result->fetch_assoc()["total"] ?? 0;

            // Seguros por vencer (próximos 30 días)
            $sql = "SELECT COUNT(DISTINCT p.idproductosv2) as total
                    FROM productosv2 p
                    INNER JOIN caracteristicas_producto cp ON p.idproductosv2 = cp.idproductosv2
                    INNER JOIN financiamiento f ON p.idproductosv2 = f.idproductosv2
                    WHERE cp.nombre_caracteristicas = 'fecha_venc_seguro'
                      AND cp.valor_caracteristica IS NOT NULL
                      AND STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                      AND f.estado_entrega = 'entregado'
                      AND f.estado_eliminado = 0";
            $result = $this->conectar->query($sql);
            $indicadores["total_seguro_por_vencer"] =
                $result->fetch_assoc()["total"] ?? 0;

            return $indicadores;
        } catch (Exception $e) {
            error_log(
                "Error en Adjudicacion::obtenerIndicadores(): " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Obtener todos los adjudicados con información completa
     */
    public function obtenerTodosAdjudicados($filtros = [])
    {
        try {
            $whereConditions = [
                "f.estado_eliminado = 0",
                "p.categoria IN ('Vehiculo', 'Moto')",
                "(f.estado_entrega = 'entregado' OR f.estado_entrega = 'pendiente')",
            ];

            // Aplicar filtros
            if (!empty($filtros["tipo_adjudicacion"])) {
                $tipoFiltro = $this->conectar->real_escape_string($filtros["tipo_adjudicacion"]);
                if ($tipoFiltro === 'crediyango') {
                    $whereConditions[] = "(a.tipo_adjudicacion = 'crediyango' OR pf.es_yango = 1)";
                } else {
                    $whereConditions[] = "a.tipo_adjudicacion = '" . $tipoFiltro . "'";
                }
            }
            if (!empty($filtros["estado_entrega"])) {
                $whereConditions[] =
                    "f.estado_entrega = '" .
                    $this->conectar->real_escape_string(
                        $filtros["estado_entrega"],
                    ) .
                    "'";
            }
            if (!empty($filtros["fecha_entrega"])) {
                $fechaEntrega = $this->conectar->real_escape_string($filtros["fecha_entrega"]);
                $whereConditions[] = "DATE(f.fecha_entrega) = '" . $fechaEntrega . "'";
            }

            $whereClause = implode(" AND ", $whereConditions);

            $sql = "SELECT
                        f.idfinanciamiento,
                        f.id_conductor,
                        f.id_cliente,
                        f.codigo_asociado,
                        f.grupo_financiamiento,
                        f.monto_total,
                        f.cuota_inicial,
                        f.cuotas,
                        f.estado,
                        f.fecha_inicio,
                        f.fecha_fin,
                        f.fecha_creacion,
                        f.fecha_entrega,
                        f.estado_entrega,
                        f.moneda,
                        p.idproductosv2,
                        p.nombre as producto_nombre,
                        p.codigo as producto_codigo,
                        p.categoria as producto_categoria,
                        p.precio_venta,
                        c.nombres as conductor_nombres,
                        c.apellido_paterno as conductor_apellido_paterno,
                        c.apellido_materno as conductor_apellido_materno,
                        c.nro_documento as conductor_documento,
                        c.foto as conductor_foto,
                        cl.nombres as cliente_nombres,
                        cl.apellido_paterno as cliente_apellido_paterno,
                        cl.apellido_materno as cliente_apellido_materno,
                        cl.n_documento as cliente_documento,
                        pf.nombre_plan,
                        pf.tipo_vehicular,
                        pf.es_yango,
                        COALESCE(a.tipo_adjudicacion, IF(pf.es_yango = 1, 'crediyango', NULL)) as tipo_adjudicacion,
                        a.fecha_adjudicacion,
                        a.fecha_entrega_programada,
                        a.dias_demora,
                        a.mes_sorteo,
                        (SELECT COUNT(*) FROM cuotas_financiamiento cf WHERE cf.id_financiamiento = f.idfinanciamiento) as total_cuotas,
                        (SELECT COUNT(*) FROM cuotas_financiamiento cf WHERE cf.id_financiamiento = f.idfinanciamiento AND cf.estado = 'pagado') as cuotas_pagadas,
                        (SELECT COUNT(*) FROM cuotas_financiamiento cf WHERE cf.id_financiamiento = f.idfinanciamiento AND cf.estado = 'pendiente') as cuotas_pendientes,
                        (SELECT COUNT(*) FROM cuotas_financiamiento cf WHERE cf.id_financiamiento = f.idfinanciamiento AND cf.fecha_vencimiento < CURDATE() AND cf.estado != 'pagado') as cuotas_vencidas,
                        (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'fecha_venc_soat' LIMIT 1) as fecha_venc_soat,
                        (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'fecha_venc_seguro' LIMIT 1) as fecha_venc_seguro,
                        (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'chasis' LIMIT 1) as chasis,
                        (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'vin' LIMIT 1) as vin,
                        (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'color' LIMIT 1) as color,
                        (SELECT valor_caracteristica FROM caracteristicas_producto WHERE idproductosv2 = p.idproductosv2 AND nombre_caracteristicas = 'anio' LIMIT 1) as anio
                    FROM financiamiento f
                    INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                    LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                    LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                    LEFT JOIN planes_financiamiento pf ON f.grupo_financiamiento = pf.idplan_financiamiento
                    LEFT JOIN adjudicaciones a ON f.idfinanciamiento = a.id_financiamiento
                    WHERE $whereClause
                    ORDER BY f.fecha_creacion DESC";

            $result = $this->conectar->query($sql);
            if (!$result) {
                throw new Exception(
                    "Error al obtener adjudicados: " . $this->conectar->error,
                );
            }

            $adjudicados = [];
            while ($row = $result->fetch_assoc()) {
                $adjudicados[] = $row;
            }

            return $adjudicados;
        } catch (Exception $e) {
            error_log(
                "Error en Adjudicacion::obtenerTodosAdjudicados(): " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Obtener detalle de un adjudicado por ID de financiamiento
     */
    public function obtenerAdjudicadoPorId($idFinanciamiento)
    {
        try {
            $sql = "SELECT
                        f.idfinanciamiento,
                        f.id_conductor,
                        f.id_cliente,
                        f.codigo_asociado,
                        f.grupo_financiamiento,
                        f.monto_total,
                        f.cuota_inicial,
                        f.estado,
                        f.fecha_entrega,
                        f.estado_entrega,
                        f.moneda,
                        p.nombre as producto_nombre,
                        p.codigo as producto_codigo,
                        c.nombres as conductor_nombres,
                        c.apellido_paterno as conductor_apellido_paterno,
                        c.apellido_materno as conductor_apellido_materno,
                        c.nro_documento as conductor_documento,
                        cl.nombres as cliente_nombres,
                        cl.apellido_paterno as cliente_apellido_paterno,
                        cl.apellido_materno as cliente_apellido_materno,
                        cl.n_documento as cliente_documento,
                        pf.nombre_plan,
                        COALESCE(a.tipo_adjudicacion, IF(pf.es_yango = 1, 'crediyango', NULL)) as tipo_adjudicacion,
                        (SELECT COUNT(*) FROM cuotas_financiamiento cf WHERE cf.id_financiamiento = f.idfinanciamiento) as total_cuotas,
                        (SELECT COUNT(*) FROM cuotas_financiamiento cf WHERE cf.id_financiamiento = f.idfinanciamiento AND cf.estado = 'pagado') as cuotas_pagadas
                    FROM financiamiento f
                    INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                    LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                    LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                    LEFT JOIN planes_financiamiento pf ON f.grupo_financiamiento = pf.idplan_financiamiento
                    LEFT JOIN adjudicaciones a ON f.idfinanciamiento = a.id_financiamiento
                    WHERE f.idfinanciamiento = ? AND f.estado_eliminado = 0
                    LIMIT 1";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param('i', $idFinanciamiento);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error en obtenerAdjudicadoPorId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener cuotas vencidas de adjudicados
     */
    public function obtenerCuotasVencidas()
    {
        try {
            $sql = "SELECT
                        f.idfinanciamiento,
                        f.id_conductor,
                        f.id_cliente,
                        f.codigo_asociado,
                        COALESCE(c.nombres, cl.nombres) as nombres,
                        COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
                        COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
                        p.nombre as producto_nombre,
                        cf.idcuotas_financiamiento,
                        cf.numero_cuota,
                        cf.monto,
                        cf.fecha_vencimiento,
                        cf.estado,
                        DATEDIFF(CURDATE(), cf.fecha_vencimiento) as dias_vencidos,
                        f.moneda
                    FROM cuotas_financiamiento cf
                    INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                    INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                    LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                    LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                    WHERE cf.fecha_vencimiento < CURDATE()
                      AND cf.estado != 'pagado'
                      AND f.estado_entrega = 'entregado'
                      AND f.estado_eliminado = 0
                      AND (f.aprobado = 1 OR f.aprobado IS NULL)
                      AND f.incobrable = 0
                      AND p.categoria IN ('Vehiculo', 'Moto')
                    ORDER BY cf.fecha_vencimiento ASC";

            $result = $this->conectar->query($sql);
            if (!$result) {
                throw new Exception(
                    "Error al obtener cuotas vencidas: " .
                        $this->conectar->error,
                );
            }

            $cuotasVencidas = [];
            while ($row = $result->fetch_assoc()) {
                $cuotasVencidas[] = $row;
            }

            return $cuotasVencidas;
        } catch (Exception $e) {
            error_log(
                "Error en Adjudicacion::obtenerCuotasVencidas(): " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Obtener adjudicados morosos (más de 30 días de mora)
     */
    public function obtenerAdjudicadosMorosos()
    {
        try {
            $sql = "SELECT
                        f.idfinanciamiento,
                        f.id_conductor,
                        f.id_cliente,
                        f.codigo_asociado,
                        COALESCE(c.nombres, cl.nombres) as nombres,
                        COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
                        COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
                        COALESCE(c.nro_documento, cl.n_documento) as documento,
                        p.nombre as producto_nombre,
                        COUNT(cf.idcuotas_financiamiento) as total_cuotas_vencidas,
                        SUM(cf.monto) as monto_total_vencido,
                        MIN(cf.fecha_vencimiento) as primera_cuota_vencida,
                        MAX(DATEDIFF(CURDATE(), cf.fecha_vencimiento)) as dias_mora_maxima,
                        f.moneda
                    FROM financiamiento f
                    INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                    INNER JOIN cuotas_financiamiento cf ON f.idfinanciamiento = cf.id_financiamiento
                    LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                    LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                    WHERE cf.fecha_vencimiento < CURDATE()
                      AND cf.estado != 'pagado'
                      AND f.estado_entrega = 'entregado'
                      AND f.estado_eliminado = 0
                      AND f.incobrable = 0
                      AND p.categoria IN ('Vehiculo', 'Moto')
                      AND DATEDIFF(CURDATE(), cf.fecha_vencimiento) > 30
                    GROUP BY f.idfinanciamiento
                    ORDER BY dias_mora_maxima DESC";

            $result = $this->conectar->query($sql);
            if (!$result) {
                throw new Exception(
                    "Error al obtener morosos: " . $this->conectar->error,
                );
            }

            $morosos = [];
            while ($row = $result->fetch_assoc()) {
                $morosos[] = $row;
            }

            return $morosos;
        } catch (Exception $e) {
            error_log(
                "Error en Adjudicacion::obtenerAdjudicadosMorosos(): " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Obtener adjudicados próximos a terminar de pagar (10 cuotas o menos pendientes)
     */
    public function obtenerProximosTerminar()
    {
        try {
            $sql = "SELECT
                        f.idfinanciamiento,
                        f.id_conductor,
                        f.id_cliente,
                        f.codigo_asociado,
                        COALESCE(c.nombres, cl.nombres) as nombres,
                        COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
                        COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
                        p.nombre as producto_nombre,
                        f.cuotas as total_cuotas,
                        COUNT(CASE WHEN cf.estado = 'pagado' THEN 1 END) as cuotas_pagadas,
                        COUNT(CASE WHEN cf.estado != 'pagado' THEN 1 END) as cuotas_pendientes,
                        MAX(cf.fecha_vencimiento) as fecha_ultima_cuota,
                        f.moneda,
                        f.monto_total
                    FROM financiamiento f
                    INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                    INNER JOIN cuotas_financiamiento cf ON f.idfinanciamiento = cf.id_financiamiento
                    LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                    LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                    WHERE f.estado_entrega = 'entregado'
                      AND f.estado_eliminado = 0
                      AND p.categoria IN ('Vehiculo', 'Moto')
                    GROUP BY f.idfinanciamiento
                    HAVING cuotas_pendientes > 0 AND cuotas_pendientes <= 10
                    ORDER BY cuotas_pendientes ASC";

            $result = $this->conectar->query($sql);
            if (!$result) {
                throw new Exception(
                    "Error al obtener próximos a terminar: " .
                        $this->conectar->error,
                );
            }

            $proximosTerminar = [];
            while ($row = $result->fetch_assoc()) {
                $proximosTerminar[] = $row;
            }

            return $proximosTerminar;
        } catch (Exception $e) {
            error_log(
                "Error en Adjudicacion::obtenerProximosTerminar(): " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Obtener ganadores por mes
     */
    public function obtenerGanadoresPorMes($mesSorteo)
    {
        try {
            $sql = "SELECT
                        f.idfinanciamiento,
                        f.id_conductor,
                        f.id_cliente,
                        f.codigo_asociado,
                        COALESCE(c.nombres, cl.nombres) as nombres,
                        COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
                        COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
                        p.nombre as producto_nombre,
                        a.tipo_adjudicacion,
                        a.fecha_adjudicacion,
                        a.fecha_entrega_real,
                        a.dias_demora,
                        f.cuota_inicial,
                        f.monto_total,
                        f.moneda
                    FROM adjudicaciones a
                    INNER JOIN financiamiento f ON a.id_financiamiento = f.idfinanciamiento
                    INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                    LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                    LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                    WHERE a.mes_sorteo = ?
                      AND f.estado_eliminado = 0
                    ORDER BY a.tipo_adjudicacion, a.fecha_adjudicacion DESC";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception(
                    "Error al preparar consulta: " . $this->conectar->error,
                );
            }

            $stmt->bind_param("s", $mesSorteo);
            if (!$stmt->execute()) {
                throw new Exception(
                    "Error al ejecutar consulta: " . $stmt->error,
                );
            }

            $result = $stmt->get_result();
            $ganadores = [];
            while ($row = $result->fetch_assoc()) {
                $ganadores[] = $row;
            }

            $stmt->close();
            return $ganadores;
        } catch (Exception $e) {
            error_log(
                "Error en Adjudicacion::obtenerGanadoresPorMes(): " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Obtener reporte de velocidad de entrega (últimos 12 meses)
     */
    public function obtenerVelocidadEntrega()
    {
        try {
            $sql = "SELECT
                        DATE_FORMAT(a.fecha_adjudicacion, '%Y-%m') as mes,
                        COUNT(*) as total_adjudicaciones,
                        AVG(a.dias_demora) as promedio_dias_demora,
                        MIN(a.dias_demora) as minimo_dias_demora,
                        MAX(a.dias_demora) as maximo_dias_demora,
                        COUNT(CASE WHEN a.dias_demora <= 7 THEN 1 END) as entregas_rapidas,
                        COUNT(CASE WHEN a.dias_demora > 30 THEN 1 END) as entregas_lentas
                    FROM adjudicaciones a
                    WHERE a.fecha_entrega_real IS NOT NULL
                      AND a.dias_demora IS NOT NULL
                    GROUP BY DATE_FORMAT(a.fecha_adjudicacion, '%Y-%m')
                    ORDER BY mes DESC
                    LIMIT 12";

            $result = $this->conectar->query($sql);
            if (!$result) {
                throw new Exception(
                    "Error al obtener velocidad de entrega: " .
                        $this->conectar->error,
                );
            }

            $velocidad = [];
            while ($row = $result->fetch_assoc()) {
                $velocidad[] = $row;
            }

            return $velocidad;
        } catch (Exception $e) {
            error_log(
                "Error en Adjudicacion::obtenerVelocidadEntrega(): " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Obtener vehículos con SOAT por vencer (próximos 60 días)
     */
    public function obtenerSoatPorVencer()
    {
        try {
            $sql = "SELECT
                        p.idproductosv2,
                        p.nombre as producto_nombre,
                        p.codigo as producto_codigo,
                        f.idfinanciamiento,
                        f.id_conductor,
                        f.id_cliente,
                        COALESCE(c.nombres, cl.nombres) as nombres,
                        COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
                        COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
                        cp.valor_caracteristica as fecha_venc_soat,
                        DATEDIFF(STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d'), CURDATE()) as dias_para_vencer
                    FROM productosv2 p
                    INNER JOIN caracteristicas_producto cp ON p.idproductosv2 = cp.idproductosv2
                    INNER JOIN financiamiento f ON p.idproductosv2 = f.idproductosv2
                    LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                    LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                    WHERE cp.nombre_caracteristicas = 'fecha_venc_soat'
                      AND cp.valor_caracteristica IS NOT NULL
                      AND STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)
                      AND f.estado_entrega = 'entregado'
                      AND f.estado_eliminado = 0
                    ORDER BY STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') ASC";

            $result = $this->conectar->query($sql);
            if (!$result) {
                throw new Exception(
                    "Error al obtener SOAT por vencer: " .
                        $this->conectar->error,
                );
            }

            $soats = [];
            while ($row = $result->fetch_assoc()) {
                $soats[] = $row;
            }

            return $soats;
        } catch (Exception $e) {
            error_log(
                "Error en Adjudicacion::obtenerSoatPorVencer(): " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Obtener vehículos con Seguro por vencer (próximos 60 días)
     */
    public function obtenerSeguroPorVencer()
    {
        try {
            $sql = "SELECT
                        p.idproductosv2,
                        p.nombre as producto_nombre,
                        p.codigo as producto_codigo,
                        f.idfinanciamiento,
                        f.id_conductor,
                        f.id_cliente,
                        COALESCE(c.nombres, cl.nombres) as nombres,
                        COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
                        COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
                        cp.valor_caracteristica as fecha_venc_seguro,
                        DATEDIFF(STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d'), CURDATE()) as dias_para_vencer
                    FROM productosv2 p
                    INNER JOIN caracteristicas_producto cp ON p.idproductosv2 = cp.idproductosv2
                    INNER JOIN financiamiento f ON p.idproductosv2 = f.idproductosv2
                    LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                    LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                    WHERE cp.nombre_caracteristicas = 'fecha_venc_seguro'
                      AND cp.valor_caracteristica IS NOT NULL
                      AND STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)
                      AND f.estado_entrega = 'entregado'
                      AND f.estado_eliminado = 0
                    ORDER BY STR_TO_DATE(cp.valor_caracteristica, '%Y-%m-%d') ASC";

            $result = $this->conectar->query($sql);
            if (!$result) {
                throw new Exception(
                    "Error al obtener Seguro por vencer: " .
                        $this->conectar->error,
                );
            }

            $seguros = [];
            while ($row = $result->fetch_assoc()) {
                $seguros[] = $row;
            }

            return $seguros;
        } catch (Exception $e) {
            error_log(
                "Error en Adjudicacion::obtenerSeguroPorVencer(): " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Crear registro de adjudicación
     */
    public function crear($datos)
    {
        try {
            $sql = 'INSERT INTO adjudicaciones (id_financiamiento, tipo_adjudicacion, fecha_adjudicacion, fecha_entrega_programada, fecha_entrega_real, dias_demora, mes_sorteo, observaciones)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception(
                    "Error al preparar la consulta: " . $this->conectar->error,
                );
            }

            $stmt->bind_param(
                "isssssss",
                $datos["id_financiamiento"],
                $datos["tipo_adjudicacion"],
                $datos["fecha_adjudicacion"],
                $datos["fecha_entrega_programada"],
                $datos["fecha_entrega_real"],
                $datos["dias_demora"],
                $datos["mes_sorteo"],
                $datos["observaciones"],
            );

            if (!$stmt->execute()) {
                throw new Exception(
                    "Error al ejecutar la consulta: " . $stmt->error,
                );
            }

            $insertId = $stmt->insert_id;
            $stmt->close();

            return $insertId;
        } catch (Exception $e) {
            error_log("Error en Adjudicacion::crear(): " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar registro de adjudicación
     */
    public function actualizar($idAdjudicacion, $datos)
    {
        try {
            $sql = 'UPDATE adjudicaciones
                    SET tipo_adjudicacion = ?,
                        fecha_adjudicacion = ?,
                        fecha_entrega_programada = ?,
                        fecha_entrega_real = ?,
                        dias_demora = ?,
                        mes_sorteo = ?,
                        observaciones = ?
                    WHERE id_adjudicacion = ?';

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception(
                    "Error al preparar la consulta: " . $this->conectar->error,
                );
            }

            $stmt->bind_param(
                "sssssssi",
                $datos["tipo_adjudicacion"],
                $datos["fecha_adjudicacion"],
                $datos["fecha_entrega_programada"],
                $datos["fecha_entrega_real"],
                $datos["dias_demora"],
                $datos["mes_sorteo"],
                $datos["observaciones"],
                $idAdjudicacion,
            );

            if (!$stmt->execute()) {
                throw new Exception(
                    "Error al ejecutar la consulta: " . $stmt->error,
                );
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            error_log(
                "Error en Adjudicacion::actualizar(): " . $e->getMessage(),
            );
            return false;
        }
    }

    /**
     * Obtener información de cuota vencida para notificación
     */
    public function obtenerInfoCuotaVencida($idCuota)
    {
        try {
            error_log("obtenerInfoCuotaVencida - Buscando cuota ID: $idCuota");

            $sql = "SELECT
                    cf.idcuotas_financiamiento,
                    cf.numero_cuota,
                    cf.monto,
                    cf.fecha_vencimiento,
                    COALESCE(cf.moneda_cuota, f.moneda, 'S/.') as moneda,
                    DATEDIFF(CURDATE(), cf.fecha_vencimiento) as dias_vencidos,
                    COALESCE(c.nombres, cl.nombres) as nombres,
                    COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
                    COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
                    COALESCE(c.correo, cl.correo) as email,
                    COALESCE(c.telefono, cl.telefono) as celular,
                    COALESCE(c.telefono, cl.telefono) as telefono,
                    p.nombre as producto_nombre
                FROM cuotas_financiamiento cf
                INNER JOIN financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
                INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                WHERE cf.idcuotas_financiamiento = ?
                LIMIT 1";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                error_log("Error al preparar query: " . $this->conectar->error);
                return null;
            }

            $stmt->bind_param("i", $idCuota);

            if (!$stmt->execute()) {
                error_log("Error al ejecutar query: " . $stmt->error);
                $stmt->close();
                return null;
            }

            $result = $stmt->get_result();
            $info = $result->fetch_assoc();

            error_log(
                "Resultado query: " . ($info ? "ENCONTRADO" : "NO ENCONTRADO"),
            );
            if (!$info) {
                error_log(
                    "Query no devolvió resultados para id_cuota: $idCuota",
                );
            }

            $stmt->close();

            return $info;
        } catch (Exception $e) {
            error_log("Error en obtenerInfoCuotaVencida: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener información para notificación de vencimiento SOAT/Seguro
     */
    public function obtenerInfoVencimiento($idFinanciamiento, $tipo)
    {
        try {
            $campoFecha =
                $tipo === "soat" ? "p.fecha_venc_soat" : "p.fecha_venc_seguro";

            $sql = "SELECT
                    f.idfinanciamiento,
                    COALESCE(c.nombres, cl.nombres) as nombres,
                    COALESCE(c.apellido_paterno, cl.apellido_paterno) as apellido_paterno,
                    COALESCE(c.apellido_materno, cl.apellido_materno) as apellido_materno,
                    COALESCE(c.correo, cl.correo) as email,
                    COALESCE(c.telefono, cl.telefono) as celular,
                    COALESCE(c.telefono, cl.telefono) as telefono,
                    p.nombre as producto_nombre,
                    p.codigo as producto_codigo,
                    p.fecha_venc_soat,
                    p.fecha_venc_seguro,
                    DATEDIFF({$campoFecha}, CURDATE()) as dias_para_vencer
                FROM financiamiento f
                INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                LEFT JOIN conductores c ON f.id_conductor = c.id_conductor
                LEFT JOIN clientes_financiar cl ON f.id_cliente = cl.id
                WHERE f.idfinanciamiento = ?
                LIMIT 1";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $idFinanciamiento);
            $stmt->execute();
            $result = $stmt->get_result();
            $info = $result->fetch_assoc();
            $stmt->close();

            return $info;
        } catch (Exception $e) {
            error_log("Error en obtenerInfoVencimiento: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener fechas de entrega distintas para el filtro
     */
    public function obtenerFechasEntregaDistintas()
    {
        try {
            $sql = "SELECT DISTINCT DATE(f.fecha_entrega) as fecha_entrega
                    FROM financiamiento f
                    INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                    WHERE f.estado_eliminado = 0
                      AND p.categoria IN ('Vehiculo', 'Moto')
                      AND f.estado_entrega IN ('entregado', 'pendiente')
                      AND f.fecha_entrega IS NOT NULL
                    ORDER BY f.fecha_entrega DESC";

            $result = $this->conectar->query($sql);
            if (!$result) {
                throw new Exception("Error: " . $this->conectar->error);
            }

            $fechas = [];
            while ($row = $result->fetch_assoc()) {
                if (!empty($row['fecha_entrega'])) {
                    $fechas[] = $row['fecha_entrega'];
                }
            }

            return $fechas;
        } catch (Exception $e) {
            error_log("Error en obtenerFechasEntregaDistintas: " . $e->getMessage());
            return [];
        }
    }
}
