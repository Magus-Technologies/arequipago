<?php
// Configurar idioma español para fechas
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');

$empresa = $_SESSION['id_empresa'];
$sucursal = $_SESSION['sucursal'];

// Configuración de fechas por defecto (mes actual)
$fecha_actual = new DateTime();
$primer_dia_mes = new DateTime($fecha_actual->format('Y-m-01'));
$ultimo_dia_mes = new DateTime($fecha_actual->format('Y-m-t'));

// Procesar filtros de fecha si se envían
$modo_filtro = isset($_GET['modo_filtro']) ? $_GET['modo_filtro'] : 'mes';
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : $primer_dia_mes->format('Y-m-d');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : $ultimo_dia_mes->format('Y-m-d');

// Si es modo día, la fecha fin es igual a la fecha inicio
if ($modo_filtro == 'dia') {
    $fecha_fin = $fecha_inicio;
}

// Incluir funciones de filtro
include_once ('filtro-calendario.php');

$conexion = (new Conexion())->getConexion();

// Obtener datos filtrados
$producto_mas_vendido = obtenerProductoMasVendido($conexion, $empresa, $sucursal, $fecha_inicio, $fecha_fin);
$totales_ventas = obtenerTotalVentas($conexion, $empresa, $sucursal, $fecha_inicio, $fecha_fin);

// Resto del código original para otras consultas...
// (mantener el código existente para cumpleaños, etc.)

// Formatear el título del período para mostrar
$titulo_periodo = '';
if ($modo_filtro == 'dia') {
    $fecha_mostrar = new DateTime($fecha_inicio);
    $titulo_periodo = 'Día: ' . $fecha_mostrar->format('d/m/Y');
} else {
    $fecha_inicio_mostrar = new DateTime($fecha_inicio);
    $fecha_fin_mostrar = new DateTime($fecha_fin);
    $titulo_periodo = 'Período: ' . $fecha_inicio_mostrar->format('d/m/Y') . ' - ' . $fecha_fin_mostrar->format('d/m/Y');
}

$anio1 = date('Y');
$mes1 = date('m');
$anio2 = '';
$mes2 = '';
if ($mes1 == 1) {
    $mes2 = '12';
    $anio2 = $anio1 - 1;
} else {
    $anio2 = $anio1;
    $mes2 = $mes1 - 1;
}

// Consulta SQL para obtener datos de ventas y ganancias
$sql = "SELECT 
    (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' AND estado = '1' and sucursal='{$_SESSION['sucursal']}' AND YEAR(fecha_emision)='$anio1' AND MONTH(fecha_emision)='$mes1') totalv,
    (SELECT COUNT(*) FROM clientes WHERE id_empresa = '$empresa') cnt_cli,
    (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='{$_SESSION['sucursal']}' and id_tido = 2 AND estado = '1' AND YEAR(fecha_emision)='$anio1' AND MONTH(fecha_emision)='$mes1') totalvF,
    (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='{$_SESSION['sucursal']}' and id_tido = 1 AND estado = '1' AND YEAR(fecha_emision)='$anio1' AND MONTH(fecha_emision)='$mes1') totalvB,
    (SELECT SUM(total) FROM ventas WHERE id_empresa='$empresa' and sucursal='{$_SESSION['sucursal']}' AND estado = '1' AND YEAR(fecha_emision)='$anio2' AND MONTH(fecha_emision)='$mes2') totalvMA,
    
  COALESCE(
    (SELECT pv2.nombre 
    FROM productos_ventas pv
    INNER JOIN productosv2 pv2 ON pv.id_producto = pv2.idproductosv2
    WHERE pv.id_venta IN (SELECT id_venta FROM ventas WHERE estado = '1' and id_empresa='$empresa' and sucursal='{$_SESSION['sucursal']}')
    GROUP BY pv.id_producto 
    ORDER BY SUM(pv.cantidad) DESC 
    LIMIT 1),
    'No hay productos vendidos'
) prodVen,


(SELECT SUM(cantidad) 
FROM productos_ventas 
WHERE id_venta IN (SELECT id_venta FROM ventas WHERE estado = '1' and id_empresa='$empresa' and sucursal='{$_SESSION['sucursal']}')
GROUP BY id_producto 
ORDER BY SUM(cantidad) DESC 
LIMIT 1) prodVenCan
";

$data = $conexion->query($sql)->fetch_assoc();

// Consulta para obtener datos de ventas mensuales para el gráfico
$dataListVen = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

$sql = "SELECT 
    MONTH(fecha_emision) mes,
    SUM(total) total
  FROM
    ventas 
  WHERE id_empresa = '$empresa' 
    AND estado = '1' 
    and sucursal='{$_SESSION['sucursal']}'
    AND YEAR(fecha_emision) = '$anio1'
    GROUP BY mes";
$resultList = $conexion->query($sql);

foreach ($resultList as $dtTemp) {
    $tempValue = 0;
    if (doubleval($dtTemp['total']) > 0) {
        $tempValue = doubleval($dtTemp['total']);
    }
    $dataListVen[intval($dtTemp['mes']) - 1] = $tempValue;
}

// Consulta SQL para contar los registros de conductores
$sql = 'SELECT COUNT(*) AS total_conductores FROM conductores';  // Consulta para contar los conductores
$result = $conexion->query($sql);  // Ejecutar la consulta
$row = $result->fetch_assoc();  // Obtener el resultado

// Obtener el número de conductores
$cantidad_conductores = $row['total_conductores'];

// Consulta SQL para contar los registros de conductores
$sql = 'SELECT COUNT(*) AS total_conductores FROM conductores';
$result = $conexion->query($sql);
$row = $result->fetch_assoc();

// Obtener el número de conductores
$cantidad_conductores = $row['total_conductores'];

// 🆕 NUEVA CONSULTA: Contar clientes de la tabla clientes_financiar
$sql_clientes = 'SELECT COUNT(*) AS total_clientes FROM clientes_financiar';
$result_clientes = $conexion->query($sql_clientes);
$row_clientes = $result_clientes->fetch_assoc();

// Obtener el número de clientes
$cantidad_clientes = $row_clientes['total_clientes'];

// Consulta para obtener el total de pagos de inscripción en el período seleccionado
$sql_total_pagos_inscripcion = "SELECT SUM(monto) as total 
                               FROM pagos_inscripcion 
                               WHERE fecha_pago BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59'";
$result_pagos_inscripcion = $conexion->query($sql_total_pagos_inscripcion);
$total_pagos_inscripcion = 0;
if ($result_pagos_inscripcion && $row = $result_pagos_inscripcion->fetch_assoc()) {
    $total_pagos_inscripcion = $row['total'] ?: 0;
}

// Consulta para obtener el total de pagos de financiamiento en el período seleccionado
$sql_total_pagos_financiamiento = "SELECT SUM(monto) as total 
                                  FROM pagos_financiamiento 
                                  WHERE fecha_pago BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59'";
$result_pagos_financiamiento = $conexion->query($sql_total_pagos_financiamiento);
$total_pagos_financiamiento = 0;
if ($result_pagos_financiamiento && $row = $result_pagos_financiamiento->fetch_assoc()) {
    $total_pagos_financiamiento = $row['total'] ?: 0;
}

// ========== CONSULTAS PARA DASHBOARD CREDIGO ==========

// 1. Total de ventas CrediGo en el período seleccionado
$sql_ventas_credigo = "
    SELECT SUM(monto_total) as total, COUNT(*) as cantidad
    FROM financiamiento
    WHERE fecha_creacion BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59'
    AND estado_eliminado = 0
    AND incobrable = 0
";
$result_ventas_credigo = $conexion->query($sql_ventas_credigo);
$total_ventas_credigo = 0;
$cantidad_financiamientos = 0;
if ($result_ventas_credigo && $row = $result_ventas_credigo->fetch_assoc()) {
    $total_ventas_credigo = $row['total'] ?: 0;
    $cantidad_financiamientos = $row['cantidad'] ?: 0;
}

// 2. Ganancias CrediGo (estimado 10% del monto de pagos)
$sql_ganancias_credigo = "
    SELECT SUM(pf.monto * 0.1) as ganancias
    FROM pagos_financiamiento pf
    INNER JOIN financiamiento f ON pf.id_financiamiento = f.idfinanciamiento
    WHERE pf.fecha_pago BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59'
    AND f.estado_eliminado = 0
    AND f.incobrable = 0
";
$result_ganancias_credigo = $conexion->query($sql_ganancias_credigo);
$ganancias_credigo = 0;
if ($result_ganancias_credigo && $row = $result_ganancias_credigo->fetch_assoc()) {
    $ganancias_credigo = $row['ganancias'] ?: 0;
}

// 3. Comparativa mes actual vs mes anterior
$mes_actual = date('Y-m');
$mes_anterior = date('Y-m', strtotime('-1 month'));

$sql_mes_actual = "
    SELECT SUM(monto_total) as total
    FROM financiamiento
    WHERE DATE_FORMAT(fecha_creacion, '%Y-%m') = '$mes_actual'
    AND estado_eliminado = 0
    AND incobrable = 0
";
$result_mes_actual = $conexion->query($sql_mes_actual);
$ventas_mes_actual = 0;
if ($result_mes_actual && $row = $result_mes_actual->fetch_assoc()) {
    $ventas_mes_actual = $row['total'] ?: 0;
}

$sql_mes_anterior = "
    SELECT SUM(monto_total) as total
    FROM financiamiento
    WHERE DATE_FORMAT(fecha_creacion, '%Y-%m') = '$mes_anterior'
    AND estado_eliminado = 0
    AND incobrable = 0
";
$result_mes_anterior = $conexion->query($sql_mes_anterior);
$ventas_mes_anterior = 0;
if ($result_mes_anterior && $row = $result_mes_anterior->fetch_assoc()) {
    $ventas_mes_anterior = $row['total'] ?: 0;
}

// Calcular porcentaje de cambio
$porcentaje_cambio = 0;
if ($ventas_mes_anterior > 0) {
    $porcentaje_cambio = (($ventas_mes_actual - $ventas_mes_anterior) / $ventas_mes_anterior) * 100;
}

// ========== FIN CONSULTAS CREDIGO ==========

// Función para obtener los cumpleaños de la semana actual
function obtenerCumpleanosSemanales($conexion)
{
    // Obtener fecha actual y calcular inicio y fin de semana
    $hoy = new DateTime();
    $inicioSemana = clone $hoy;
    $inicioSemana->modify('this week monday');  // Lunes de esta semana
    $finSemana = clone $inicioSemana;
    $finSemana->modify('+6 days');  // Domingo de esta semana

    // Formatear fechas para la consulta SQL
    $inicioSemanaStr = $inicioSemana->format('m-d');
    $finSemanaStr = $finSemana->format('m-d');

    // Consulta SQL para obtener conductores con cumpleaños en esta semana
    // Comparamos solo mes y día, ignorando el año
    $sql = "SELECT 
                id_conductor,
                nombres,
                apellido_paterno,
                apellido_materno,
                fech_nac,
                foto
            FROM 
                conductores 
            WHERE 
                DATE_FORMAT(fech_nac, '%m-%d') BETWEEN '$inicioSemanaStr' AND '$finSemanaStr'
            ORDER BY 
                DATE_FORMAT(fech_nac, '%m-%d') ASC
            LIMIT 5";

    $resultado = mysqli_query($conexion, $sql);

    if (!$resultado) {
        return [];  // Retornar array vacío si hay error
    }

    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}

// Obtener los cumpleaños de la semana para usarlos en el dashboard
$cumpleanosSemana = obtenerCumpleanosSemanales($conexion);

// Ordenar los cumpleaños: primero los de hoy, luego los próximos, luego los pasados
$hoy = new DateTime();
$cumpleanosPorCategoria = [
    'hoy' => [],
    'proximos' => [],
    'pasados' => []
];

foreach ($cumpleanosSemana as $conductor) {
    $fechaNac = new DateTime($conductor['fech_nac']);
    $fechaCumpleEsteAnio = new DateTime($hoy->format('Y') . '-' . $fechaNac->format('m-d'));

    // Determinar si el cumpleaños es hoy
    if ($hoy->format('m-d') === $fechaNac->format('m-d')) {
        $cumpleanosPorCategoria['hoy'][] = $conductor;
    }
    // Determinar si el cumpleaños es en el futuro (esta semana)
    elseif ($fechaCumpleEsteAnio > $hoy && $fechaCumpleEsteAnio->diff($hoy)->days < 7) {
        $cumpleanosPorCategoria['proximos'][] = $conductor;
    }
    // Determinar si el cumpleaños ya pasó (esta semana)
    else {
        $cumpleanosPorCategoria['pasados'][] = $conductor;
    }
}

// Ordenar los próximos por fecha más cercana
usort($cumpleanosPorCategoria['proximos'], function ($a, $b) use ($hoy) {
    $fechaNacA = new DateTime($a['fech_nac']);
    $fechaNacB = new DateTime($b['fech_nac']);

    $fechaCumpleA = new DateTime($hoy->format('Y') . '-' . $fechaNacA->format('m-d'));
    $fechaCumpleB = new DateTime($hoy->format('Y') . '-' . $fechaNacB->format('m-d'));

    return $fechaCumpleA <=> $fechaCumpleB;
});

// Ordenar los pasados por fecha más reciente
usort($cumpleanosPorCategoria['pasados'], function ($a, $b) use ($hoy) {
    $fechaNacA = new DateTime($a['fech_nac']);
    $fechaNacB = new DateTime($b['fech_nac']);

    $fechaCumpleA = new DateTime($hoy->format('Y') . '-' . $fechaNacA->format('m-d'));
    $fechaCumpleB = new DateTime($hoy->format('Y') . '-' . $fechaNacB->format('m-d'));

    // Si la fecha ya pasó este año, ajustar para comparación
    if ($fechaCumpleA < $hoy) {
        $fechaCumpleA->modify('-1 day');
    }
    if ($fechaCumpleB < $hoy) {
        $fechaCumpleB->modify('-1 day');
    }

    // Orden inverso para que los más recientes aparezcan primero
    return $fechaCumpleB <=> $fechaCumpleA;
});

// Combinar las categorías en el orden deseado
$cumpleanosOrdenados = array_merge(
    $cumpleanosPorCategoria['hoy'],
    $cumpleanosPorCategoria['proximos'],
    $cumpleanosPorCategoria['pasados']
);

// Colores para los avatares
$colores = ['#7852a2', '#4a6bdf', '#df4a94', '#4adfb4', '#dfb44a'];

// 1. Conductores registrados por semana y por mes
$sql_conductores_semanal = 'SELECT YEARWEEK(fecha_inscripcion, 1) AS semana, COUNT(*) AS total 
                             FROM inscripciones 
                             GROUP BY semana 
                             ORDER BY semana DESC';

$sql_conductores_mensual = "SELECT DATE_FORMAT(fecha_inscripcion, '%Y-%m') AS mes, COUNT(*) AS total 
                            FROM inscripciones 
                            GROUP BY mes 
                            ORDER BY mes DESC";

$result_conductores_semanal = $conexion->query($sql_conductores_semanal);
$result_conductores_mensual = $conexion->query($sql_conductores_mensual);

// 2. Pagos por inscripción (id_tipopago != 2) por semana y por mes
$sql_pagos_semanal = 'SELECT YEARWEEK(fecha_pago, 1) AS semana, COUNT(*) AS total 
                      FROM conductor_pago 
                      WHERE id_tipopago != 2
                      GROUP BY semana 
                      ORDER BY semana DESC';

$sql_pagos_mensual = "SELECT DATE_FORMAT(fecha_pago, '%Y-%m') AS mes, COUNT(*) AS total 
                      FROM conductor_pago 
                      WHERE id_tipopago != 2
                      GROUP BY mes 
                      ORDER BY mes DESC";

$result_pagos_semanal = $conexion->query($sql_pagos_semanal);
$result_pagos_mensual = $conexion->query($sql_pagos_mensual);

// 3. Financiamientos de inscripciones (id_tipopago = 2) por semana y por mes
$sql_financiamiento_ins_semanal = 'SELECT YEARWEEK(fecha_pago, 1) AS semana, COUNT(*) AS total 
                                   FROM conductor_pago 
                                   WHERE id_tipopago = 2
                                   GROUP BY semana 
                                   ORDER BY semana DESC';

$sql_financiamiento_ins_mensual = "SELECT DATE_FORMAT(fecha_pago, '%Y-%m') AS mes, COUNT(*) AS total 
                                   FROM conductor_pago 
                                   WHERE id_tipopago = 2
                                   GROUP BY mes 
                                   ORDER BY mes DESC";

$result_financiamiento_ins_semanal = $conexion->query($sql_financiamiento_ins_semanal);
$result_financiamiento_ins_mensual = $conexion->query($sql_financiamiento_ins_mensual);

// 4. Financiamientos de productos (fecha_creacion) por semana y por mes
$sql_financiamiento_prod_semanal = 'SELECT YEARWEEK(fecha_creacion, 1) AS semana, COUNT(*) AS total 
                                    FROM financiamiento 
                                    GROUP BY semana 
                                    ORDER BY semana DESC';

$sql_financiamiento_prod_mensual = "SELECT DATE_FORMAT(fecha_creacion, '%Y-%m') AS mes, COUNT(*) AS total 
                                    FROM financiamiento 
                                    GROUP BY mes 
                                    ORDER BY mes DESC";

// Ejecutar las consultas de financiamiento de productos
$result_financiamiento_prod_semanal = $conexion->query($sql_financiamiento_prod_semanal);
$result_financiamiento_prod_mensual = $conexion->query($sql_financiamiento_prod_mensual);

// Obtener resultados
$conductoresSemanal = $result_conductores_semanal ? $result_conductores_semanal->fetch_all(MYSQLI_ASSOC) : [];
$conductoresMensual = $result_conductores_mensual ? $result_conductores_mensual->fetch_all(MYSQLI_ASSOC) : [];

$pagosSemanal = $result_pagos_semanal ? $result_pagos_semanal->fetch_all(MYSQLI_ASSOC) : [];
$pagosMensual = $result_pagos_mensual ? $result_pagos_mensual->fetch_all(MYSQLI_ASSOC) : [];

$financiamientoInsSemanal = $result_financiamiento_ins_semanal ? $result_financiamiento_ins_semanal->fetch_all(MYSQLI_ASSOC) : [];
$financiamientoInsMensual = $result_financiamiento_ins_mensual ? $result_financiamiento_ins_mensual->fetch_all(MYSQLI_ASSOC) : [];

$financiamientoProdSemanal = $result_financiamiento_prod_semanal ? $result_financiamiento_prod_semanal->fetch_all(MYSQLI_ASSOC) : [];
$financiamientoProdMensual = $result_financiamiento_prod_mensual ? $result_financiamiento_prod_mensual->fetch_all(MYSQLI_ASSOC) : [];

// Obtener el mes y año actual para los selectores
$mesActual = (int) date('n');
$anioActual = (int) date('Y');

// Si hay fecha de inicio, extraer mes y año
if (!empty($fecha_inicio)) {
    $fechaTemp = new DateTime($fecha_inicio);
    $mesSeleccionado = (int) $fechaTemp->format('n');
    $anioSeleccionado = (int) $fechaTemp->format('Y');
} else {
    $mesSeleccionado = $mesActual;
    $anioSeleccionado = $anioActual;
}

// Preparar array de meses para el selector
$meses = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre'
];

function obtenerConductoresYClientesConCuotasVencidas()
{
    $conexion = (new Conexion())->getConexion();  // Conexión a la base de datos
    $fecha_actual = date('Y-m-d');  // Fecha actual
    $conductores_vencidos = [];  // Arreglo para guardar los conductores con cuotas vencidas

    // 1. Obtener cuotas de inscripción vencidas
    $query_inscripcion = "
        SELECT 
            c.id_conductor, 
            CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
            COUNT(cc.id_conductorcuota) AS num_cuotas,
            SUM(cc.monto_cuota) AS deuda_total,
            'Financiamiento de Inscripción' AS tipo_financiamiento
        FROM 
            conductor_cuotas cc
        INNER JOIN 
            conductor_regfinanciamiento crf ON cc.idconductor_Financiamiento = crf.idconductor_regfinanciamiento
        INNER JOIN 
            conductores c ON crf.id_conductor = c.id_conductor
        WHERE
            cc.fecha_vencimiento < '$fecha_actual'
            AND cc.estado_cuota != 'pagado'
            AND crf.incobrable = 0
        GROUP BY
            c.id_conductor
    ";

    $result_inscripcion = $conexion->query($query_inscripcion);

    if (!$result_inscripcion) {
        die('Error al ejecutar la consulta de inscripción: ' . $conexion->error);
    }

    // Procesar los resultados de la consulta de inscripción
    while ($row = $result_inscripcion->fetch_assoc()) {
        $registros_vencidos[] = [
            'id_conductor' => $row['id_conductor'],
            'nombre' => $row['nombre_completo'],
            'num_cuotas' => $row['num_cuotas'],
            'deuda_total' => $row['deuda_total'],
            'tipo_financiamiento' => $row['tipo_financiamiento'],
            'moneda' => 'S/'
        ];
    }

    // 2. Obtener cuotas de financiamiento de productos vencidas
    $query_productos_conductores = "
        SELECT 
            c.id_conductor, 
            CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
            COUNT(cf.idcuotas_financiamiento) AS num_cuotas,
            SUM(cf.monto) AS deuda_total,
            p.nombre AS tipo_financiamiento,
            f.moneda  
        FROM 
            cuotas_financiamiento cf
        INNER JOIN 
            financiamiento f ON cf.id_financiamiento = f.idfinanciamiento
        INNER JOIN 
            conductores c ON f.id_conductor = c.id_conductor
        INNER JOIN 
            productosv2 p ON f.idproductosv2 = p.idproductosv2
        WHERE
            cf.fecha_vencimiento < '$fecha_actual'
            AND cf.estado = 'En Progreso'
            AND f.incobrable = 0
            AND f.estado_eliminado = 0
            AND NOT (f.idproductosv2 IN (37, 312) AND f.estado_entrega = 'pendiente')
        GROUP BY
            c.id_conductor, p.nombre, f.moneda  /* MODIFICADO: Agrupamos también por moneda */
    ";

    $result_productos_conductores = $conexion->query($query_productos_conductores);

    if (!$result_productos_conductores) {
        die('Error al ejecutar la consulta de productos para conductores: ' . $conexion->error);
    }

    while ($row = $result_productos_conductores->fetch_assoc()) {
        $registros_vencidos[] = [
            'id_conductor' => $row['id_conductor'],
            'nombre' => $row['nombre_completo'],
            'num_cuotas' => $row['num_cuotas'],
            'deuda_total' => $row['deuda_total'],
            'tipo_financiamiento' => $row['tipo_financiamiento'],
            'moneda' => $row['moneda']
        ];
    }

    // 3. Obtener cuotas de financiamiento de productos vencidas para clientes
    $query_productos_clientes = "
            SELECT 
                NULL as id_conductor, 
                 cf.id as id_cliente,
                CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno) AS nombre_completo,
                COUNT(cfc.idcuotas_financiamiento) AS num_cuotas,
                SUM(cfc.monto) AS deuda_total,
                p.nombre AS tipo_financiamiento,
                f.moneda  
            FROM 
                cuotas_financiamiento cfc
            INNER JOIN 
                financiamiento f ON cfc.id_financiamiento = f.idfinanciamiento
            INNER JOIN 
                clientes_financiar cf ON f.id_cliente = cf.id
            INNER JOIN 
                productosv2 p ON f.idproductosv2 = p.idproductosv2
            WHERE
                cfc.fecha_vencimiento < '$fecha_actual'
                AND cfc.estado = 'En Progreso'
                AND f.incobrable = 0
                AND f.estado_eliminado = 0
                AND NOT (f.idproductosv2 IN (37, 312) AND f.estado_entrega = 'pendiente')
            GROUP BY
                cf.id, p.nombre, f.moneda  
        ";

    $result_productos_clientes = $conexion->query($query_productos_clientes);

    if (!$result_productos_clientes) {
        die('Error al ejecutar la consulta de productos para clientes: ' . $conexion->error);
    }

    // Procesar los resultados de las cuotas de productos para clientes // código
    while ($row = $result_productos_clientes->fetch_assoc()) {
        $registros_vencidos[] = [
            'id_conductor' => $row['id_conductor'],
            'id_cliente' => $row['id_cliente'],
            'nombre' => $row['nombre_completo'],
            'num_cuotas' => $row['num_cuotas'],
            'deuda_total' => $row['deuda_total'],
            'tipo_financiamiento' => $row['tipo_financiamiento'],
            'moneda' => $row['moneda']
        ];  // código
    }

    // Ordenar por número de cuotas (mayor a menor)
    usort($registros_vencidos, function ($a, $b) {
        return $b['num_cuotas'] - $a['num_cuotas'];
    });

    return $registros_vencidos;
}

// Esta función genera el HTML para la lista de conductores
function generarListaRegistrosVencidos($registros_vencidos)
{
    $html = '';
    $contador = 0;

    // Limitar a mostrar solo 3 conductores
    foreach ($registros_vencidos as $index => $registro) {
        if ($contador >= 3)
            break;

        // Determinar el color según el número de cuotas
        $color_class = $registro['num_cuotas'] > 2 ? 'rgba(255, 86, 48, 0.1)' : 'rgba(255, 163, 48, 0.1)';
        $text_color = $registro['num_cuotas'] > 2 ? '#FF5630' : '#FF9F1A';

        // Modificar HTML con los datos dinámicos
        $html .= '
        <div id="vencidos-item-' . ($index + 1) . '" class="mb-3 p-3" style="background-color: rgba(247, 213, 74, 0.08); border-radius: 12px; transition: all 0.3s ease;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 id="vencidos-nombre-' . ($index + 1) . '" style="font-weight: 600; margin-bottom: 5px; color: #333;">' . $registro['nombre'] . '</h6>
                    <span id="vencidos-financiamiento-' . ($index + 1) . '" style="font-size: 0.85rem; color: #8b8c64; display: block; margin-bottom: 8px;">Financiamiento: ' . $registro['tipo_financiamiento'] . '</span>
                </div>
                <div class="text-end">
                    <span id="vencidos-cuotas-' . ($index + 1) . '" class="px-2 py-1" style="background-color: ' . $color_class . '; color: ' . $text_color . '; font-weight: 600; border-radius: 6px; font-size: 0.85rem;">' . $registro['num_cuotas'] . ' cuota' . ($registro['num_cuotas'] > 1 ? 's' : '') . '</span>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <span style="font-size: 0.85rem; color: #666;">Deuda total:</span>
                <h5 id="vencidos-monto-' . ($index + 1) . '" style="font-weight: 700; color: #333; margin-bottom: 0;">' . $registro['moneda'] . ' ' . number_format($registro['deuda_total'], 2, '.', ',') . '</h5>  
            </div>
        </div>';

        $contador++;
    }

    return $html;
}

$registros_vencidos = obtenerConductoresYClientesConCuotasVencidas();
$total_registros = count($registros_vencidos);
$html_registros = generarListaRegistrosVencidos($registros_vencidos);
?>


<!-- start page title -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- llamamos al css de home -->
<link rel="stylesheet" href="public/css/home.css?v=2.2">

<style>
.welcome-banner {
    background: #2e217a;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    color: white;
    position: relative;
    overflow: hidden;
}

.welcome-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.welcome-content {
    position: relative;
    z-index: 1;
}

.welcome-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.welcome-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
}

.user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
}
</style>

<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title">Dashboard</h6>
            <ol class="breadcrumb m-10">
                <li class="breadcrumb-item active">Bienvenido al Sistema de <strong>AREQUIPAGO ERP</strong></li>
            </ol>
        </div>
        <?php if ($_SESSION['id_rol'] != 2): // Ocultar para ASESORES ?>
        <!-- Modificar la sección del filtro de calendario -->
        <div class="col-md-4">
            <!-- Filtro de calendario -->
            <div class="filtro-calendario-container d-flex justify-content-end">
                <div class="card p-2 shadow-sm">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="form-check form-check-inline me-3">
                            <input class="form-check-input" type="radio" name="modo_filtro" id="modoDia" value="dia"
                                <?= $modo_filtro == 'dia' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="modoDia">Día</label>
                        </div>
                        <div class="form-check form-check-inline me-3">
                            <input class="form-check-input" type="radio" name="modo_filtro" id="modoMes" value="mes"
                                <?= $modo_filtro == 'mes' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="modoMes">Mes</label>
                        </div>

                        <!-- Selector de día (se muestra cuando modo_filtro es "dia") -->
                        <div id="selector-dia" class="input-group input-group-sm me-2"
                            style="width: auto; <?= $modo_filtro == 'dia' ? '' : 'display: none;' ?>">
                            <input type="text" class="form-control" id="fecha_selector" value="<?= $fecha_inicio ?>"
                                placeholder="YYYY-MM-DD">
                        </div>

                        <!-- Selectores de mes y año (se muestran cuando modo_filtro es "mes") -->
                        <div id="selector-mes" class="d-flex"
                            style="<?= $modo_filtro == 'mes' ? '' : 'display: none;' ?>">
                            <select id="mes_selector" class="form-select form-select-sm me-2" style="width: auto;">
                                <?php foreach ($meses as $num => $nombre): ?>
                                    <option value="<?= $num ?>" <?= $num == $mesSeleccionado ? 'selected' : '' ?>>
                                        <?= $nombre ?></option>
                                <?php endforeach; ?>
                            </select>

                            <select id="anio_selector" class="form-select form-select-sm me-2" style="width: auto;">
                                <?php for ($anio = $anioActual - 5; $anio <= $anioActual + 1; $anio++): ?>
                                    <option value="<?= $anio ?>" <?= $anio == $anioSeleccionado ? 'selected' : '' ?>>
                                        <?= $anio ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <button id="btn_filtrar" class="btn btn-primary btn-sm">Filtrar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<!-- end page title -->

<?php if ($_SESSION['id_rol'] == 2): // SOLO para ASESORES ?>
<!-- Banner de Bienvenida para Asesores -->
<?php
    // DEBUG: Verificar qué hay en la sesión
    error_log('DEBUG HOME - SESSION NOMBRES: ' . (isset($_SESSION['nombres']) ? $_SESSION['nombres'] : 'NO EXISTE'));
    error_log('DEBUG HOME - SESSION APELLIDOS: ' . (isset($_SESSION['apellidos']) ? $_SESSION['apellidos'] : 'NO EXISTE'));
    error_log('DEBUG HOME - SESSION USUARIO_ID: ' . (isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'NO EXISTE'));

    // Si no hay nombres en sesión, obtenerlos de la base de datos
    if (!isset($_SESSION['nombres']) || empty($_SESSION['nombres'])) {
        try {
            $conectar = (new Conexion())->getConexion();
            $usuario_id = $_SESSION['usuario_id'];

            $sql = 'SELECT nombres, apellidos FROM usuarios WHERE usuario_id = ?';
            $stmt = $conectar->prepare($sql);
            $stmt->bind_param('i', $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $_SESSION['nombres'] = $row['nombres'];
                $_SESSION['apellidos'] = $row['apellidos'];
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log('Error al obtener datos de usuario: ' . $e->getMessage());
        }
    }

    // Obtener el nombre completo del usuario desde la sesión
    $nombre_usuario = isset($_SESSION['nombres']) ? $_SESSION['nombres'] : 'Usuario';
    $apellido_usuario = isset($_SESSION['apellidos']) ? $_SESSION['apellidos'] : '';

    // Si hay apellido, agregarlo al nombre
    if (!empty($apellido_usuario)) {
        $nombre_completo_usuario = $nombre_usuario . ' ' . $apellido_usuario;
    } else {
        $nombre_completo_usuario = $nombre_usuario;
    }

    // Obtener iniciales para el avatar
    $iniciales = mb_substr($nombre_usuario, 0, 1);
    if (!empty($apellido_usuario)) {
        $iniciales .= mb_substr($apellido_usuario, 0, 1);
    }
    $iniciales = strtoupper($iniciales);

    // Formatear fecha en español
    if (class_exists('IntlDateFormatter')) {
        try {
            $formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
            $fecha_formateada = ucfirst($formatter->format(new DateTime()));
        } catch (Exception $e) {
            // Fallback si hay error al formatear
            $dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
            $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            $fecha_formateada = ucfirst($dias[date('w')]) . ', ' . date('j') . ' de ' . $meses[date('n') - 1] . ' de ' . date('Y');
        }
    } else {
        // Fallback si IntlDateFormatter no está disponible
        $dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $fecha_formateada = ucfirst($dias[date('w')]) . ', ' . date('j') . ' de ' . $meses[date('n') - 1] . ' de ' . date('Y');
    }
?>
<div class="welcome-banner">
    <div class="welcome-content">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="user-avatar">
                    <?= $iniciales ?>
                </div>
            </div>
            <div class="col">
                <h1 class="welcome-title">¡Bienvenido, <?= htmlspecialchars($nombre_completo_usuario) ?>! 👋</h1>
                <p class="welcome-subtitle mb-0">
                    <i class="fas fa-calendar-day me-2"></i><?= $fecha_formateada ?>
                    <span class="ms-3"><i class="fas fa-clock me-2"></i><?= date('h:i A') ?></span>
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($_SESSION['id_rol'] != 2): // Ocultar para ASESORES ?>

<!-- Indicador de período filtrado -->
<div class="alert alert-info mb-4">
    <strong><i class="mdi mdi-calendar-search"></i> <?= $titulo_periodo ?></strong>
    <button type="button" class="btn btn-sm btn-outline-info float-end" id="btn_restablecer">
        <i class="mdi mdi-refresh"></i> Restablecer
    </button>
</div>
<?php endif; ?>

<!-- Notificación de ventas -->
<?php if ($totales_ventas['cantidad_ventas'] == 0): ?>
    <div id="notificacion-ventas" class="alert alert-warning mb-4 notification-fade-in">
        <div class="d-flex align-items-center">
            <i class="mdi mdi-alert-circle-outline me-2" style="font-size: 24px;"></i>
            <div class="flex-grow-1">
                <strong>No hay ventas registradas</strong>
                <p class="mb-0">No se encontraron ventas para <?= $modo_filtro == 'dia' ? 'el día' : 'el período' ?>
                    seleccionado.</p>
            </div>
            <button type="button" class="btn-close" onclick="this.parentElement.parentElement.style.display='none';"
                aria-label="Cerrar"></button>
        </div>
    </div>
<?php endif; ?>

<div class="row">
     <?php if ($_SESSION['id_rol'] != 2): // Ocultar para ASESORES ?>

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-white text-dark"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <div class="mb-4">
                    <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50"
                        style="border-radius: 20px;background-color: #eed8fc;">
                        <img class="mt-3 mr-5" src="<?= URL::to('public/assets/images/services-icon/01.png') ?>">
                    </div>
                    <h5 class="text-uppercase fw-light text-dark text-end">Monto Vendido</h5>
                    <h1 class="fw-bolder text-end">S/
                        <?= number_format($totales_ventas['total_ventas'] ?? 0.0, 2, '.', ',') ?></h1>
                    <!-- <div class="mini-stat-label bg-success">
                        <p class="mb-0">Mes</p>
                    </div> -->
                </div>
                <div class="pt-2">
                    <div class="float-end" hidden>
                        <a href="javascript:void(0)" class="text-black-50"><i class="mdi mdi-arrow-right h5"></i></a>
                    </div>

                    <p class="text-dark-50 mb-0 mt-1 text-end">Facturas y Boletas</p>
                </div>
            </div>
        </div>
    </div>
     

    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-white text-dark"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <div class="mb-4">
                    <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50"
                        style="border-radius: 20px; background-color: #eed8fc;">
                        <img class="mt-3 mr-5" src="<?= URL::to('public/assets/images/services-icon/02.png') ?>" alt="">
                    </div>
                    <h6 class="fw-light text-uppercase text-black text-end"
                        style="margin-bottom: 20px; margin-right: -12px;">Producto mas Vendido</h6>
                    <p class="fw-bolder text-end" style="font-size: 17px;">
                        <?= $producto_mas_vendido['nombre'] ?? 'No hay datos' ?></p>
                    <div hidden class="mini-stat-label bg-danger">
                        <p class="mb-0">Total</p>
                    </div>
                </div>
                <div class="pt-2">
                    <div hidden class="float-end">
                        <a href="javascript:void(0)" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                    </div>

                    <p class="text-dark-50 mb-0 mt-1 text-end">Cantidad Vendidas
                        (<?= $producto_mas_vendido['total_vendido'] ?? '0' ?>)</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-white text-dark"
            style="border-radius:20px;box-shadow:0 5px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <div class="mb-4">
                    <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50"
                        style="border-radius: 20px; background-color: #eed8fc;">
                        <img class="mt-3 mr-5" src="<?= URL::to('public/assets/images/services-icon/03.png') ?>" alt="">
                    </div>
                    <h5 class="fw-light text-uppercase text-black text-end">Total en Facturas</h5>
                    <h1 class="fw-bolder text-end">S/
                        <?= number_format($totales_ventas['total_facturas'] ?? 0.0, 2, '.', ',') ?></h1>
                    <!-- <div class="mini-stat-label bg-info">
                        <p class="mb-0"> Mes</p>
                    </div> -->
                </div>
                <div class="pt-2">
                    <div class="float-end">
                        <a href="javascript:void(0)" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                    </div>

                    <p class="text-white-50 mb-0 mt-1"> </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-white text-dark"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <div class="mb-4">
                    <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50"
                        style="border-radius: 20px; background-color: #eed8fc;">
                        <img class="mt-3 mr-5" src="<?= URL::to('public/assets/images/services-icon/04.png') ?>" alt="">
                    </div>
                    <h5 class="fw-light text-uppercase text-black text-end">Total en Boletas</h5>
                    <h1 class="fw-bolder text-end">S/
                        <?= number_format($totales_ventas['total_boletas'] ?? 0.0, 2, '.', ',') ?></h1>
                    <!-- <div class="mini-stat-label bg-warning">
                        <p class="mb-0">Mes</p>
                    </div> -->
                </div>
                <div class="pt-2">
                    <div class="float-end">
                        <a href="javascript:void(0)" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                    </div>

                    <p class="text-white-50 mb-0 mt-1"> </p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reemplazar el componente de Cumpleaños de la Semana actual con este nuevo diseño -->
    <!-- Eliminar el código actual de Cumpleaños de la Semana que está en un solo div col-xl-3 col-md-6 -->

    <!-- Componente de Cumpleaños de la Semana (versión mejorada) -->
    <div class="col-xl-6 col-md-12">
        <div class="card border-0"
            style="border-radius:20px;box-shadow:0 10px 15px -3px rgba(120,82,162,0.2),0 4px 6px -2px rgba(120,82,162,0.1); background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%); max-height: 400px;">
            <div class="card-body p-0">
                <div class="row g-0">
                    <!-- Columna izquierda con título y decoración -->
                    <div class="col-md-4 position-relative overflow-hidden">
                        <div class="h-100 d-flex flex-column justify-content-center p-4"
                            style="border-radius: 20px 0 0 20px; background: linear-gradient(135deg, #7852a2 0%, #5e3d82 100%);">
                            <div class="position-absolute top-0 right-0 w-100 h-100 overflow-hidden opacity-20">
                                <div class="position-absolute"
                                    style="top: -20px; right: -20px; width: 140px; height: 140px; border-radius: 50%; background: rgba(255,255,255,0.2);">
                                </div>
                                <div class="position-absolute"
                                    style="bottom: -30px; left: -30px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,0.15);">
                                </div>
                            </div>
                            <div class="position-relative">
                                <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3"
                                    style="width: 60px; height: 60px;">
                                    <img src="<?= URL::to('public/assets/images/services-icon/gift-9.gif') ?>"
                                        alt="Cumpleaños" class="w-75 h-75 object-fit-contain"
                                        style="background: transparent;">
                                </div>
                                <h4 class="text-white fw-bold mb-2">Cumpleaños de la Semana</h4>
                                <p class="text-white-50 mb-0">Celebremos juntos</p>
                                <div class="mt-4">
                                    <div class="d-flex align-items-center">
                                        <div class=" bg-opacity-25 rounded-pill px-3 py-1"
                                            style="background-color: rgba(230, 59, 59, 0.2);">
                                            <span class="text-white small fw-bold">
                                                <?php
                                                $inicioSemana = new DateTime();
                                                $inicioSemana->modify('this week monday');
                                                $finSemana = clone $inicioSemana;
                                                $finSemana->modify('+6 days');
                                                echo $inicioSemana->format('d M') . ' - ' . $finSemana->format('d M');
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha con la lista de cumpleaños -->
                    <div class="col-md-8">
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-purple-800 mb-0" style="color: #5e3d82;">Próximos cumpleaños
                                </h6>
                                <span class="badge rounded-pill px-3 py-2"
                                    style="background-color: #eed8fc; color: #5e3d82; font-weight: bold;">
                                    <?= count($cumpleanosSemana) ?> personas
                                </span>
                            </div>

                            <div class="cumpleanos-lista" style="max-height: 280px; overflow-y: auto;">
                                <?php if (empty($cumpleanosSemana)): ?>
                                    <div class="text-center py-5">
                                        <div class="mb-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="text-muted opacity-50">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                        </div>
                                        <p class="text-muted mb-0">No hay cumpleaños esta semana</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($cumpleanosOrdenados as $index => $conductor): ?>
                                        <?php
                                        $fechaNac = new DateTime($conductor['fech_nac']);
                                        $hoy = new DateTime();
                                        $edad = $hoy->format('Y') - $fechaNac->format('Y');
                                        if ($hoy->format('m-d') < $fechaNac->format('m-d')) {
                                            $edad--;
                                        }

                                        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                                        $mes = $meses[$fechaNac->format('n') - 1];
                                        $fechaFormateada = $fechaNac->format('d') . ' de ' . $mes;

                                        $nombreCompleto = $conductor['nombres'] . ' ' . $conductor['apellido_paterno'];
                                        $iniciales = mb_substr($conductor['nombres'], 0, 1) . mb_substr($conductor['apellido_paterno'], 0, 1);

                                        // Calcular días hasta/desde el cumpleaños
                                        $fechaCumpleEsteAnio = new DateTime($hoy->format('Y') . '-' . $fechaNac->format('m-d'));
                                        $esHoy = $hoy->format('m-d') === $fechaNac->format('m-d');

                                        // Determinar si el cumpleaños ya pasó este año
                                        $cumpleYaPaso = false;
                                        $diasDesde = 0;
                                        $diasHasta = 0;

                                        if ($fechaCumpleEsteAnio < $hoy) {
                                            $cumpleYaPaso = true;
                                            $diasDesde = $hoy->diff($fechaCumpleEsteAnio)->days;

                                            // Si pasaron más de 300 días, probablemente es del año pasado
                                            if ($diasDesde > 300) {
                                                $diasDesde = $diasDesde - 365;
                                            }

                                            // Para el próximo cumpleaños
                                            $fechaProximoCumple = clone $fechaCumpleEsteAnio;
                                            $fechaProximoCumple->modify('+1 year');
                                            $diasHasta = $hoy->diff($fechaProximoCumple)->days;
                                        } else {
                                            $diasHasta = $hoy->diff($fechaCumpleEsteAnio)->days;
                                        }

                                        // Colores para el avatar basados en el índice
                                        $colorIndex = $index % count($colores);
                                        $color = $colores[$colorIndex];

                                        // Determinar el estado y color del badge
                                        $badgeText = '';
                                        $badgeColor = '';
                                        $badgeBgColor = '';

                                        if ($esHoy) {
                                            $badgeText = 'Hoy';
                                            $badgeColor = 'white';
                                            $badgeBgColor = '#00c389';  // Verde
                                        } elseif ($cumpleYaPaso) {
                                            if ($diasDesde == 1) {
                                                $badgeText = 'Ayer';
                                                $badgeColor = 'white';
                                                $badgeBgColor = '#ff9500';  // Naranja
                                            } elseif ($diasDesde <= 7) {
                                                $badgeText = 'Hace ' . $diasDesde . ' días';
                                                $badgeColor = 'white';
                                                $badgeBgColor = '#ff9500';  // Naranja
                                            } else {
                                                $badgeText = 'En ' . $diasHasta . ' días';
                                                $badgeColor = '#6c757d';
                                                $badgeBgColor = '#e9ecef';  // Gris claro
                                            }
                                        } else {
                                            if ($diasHasta == 1) {
                                                $badgeText = 'Mañana';
                                                $badgeColor = 'white';
                                                $badgeBgColor = '#007bff';  // Azul
                                            } else {
                                                $badgeText = 'En ' . $diasHasta . ' días';
                                                $badgeColor = 'white';
                                                $badgeBgColor = '#007bff';  // Azul
                                            }
                                        }
                                        ?>
                                        <div
                                            class="d-flex align-items-center p-3 <?= $index < count($cumpleanosOrdenados) - 1 ? 'border-bottom' : '' ?> <?= $esHoy ? 'bg-light rounded-3' : '' ?>">
                                            <div class="flex-shrink-0 position-relative">
                                                <?php if (!empty($conductor['foto'])): ?>
                                                    <div class="rounded-circle overflow-hidden"
                                                        style="width: 50px; height: 50px; border: 3px solid <?= $color ?>;">
                                                        <img src="<?= URL::to('public/' . $conductor['foto']) ?>"
                                                            alt="<?= $nombreCompleto ?>" class="w-100 h-100 object-fit-cover">
                                                    </div>
                                                <?php else: ?>
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                                                        style="width: 50px; height: 50px; background-color: <?= $color ?>; border: 3px solid rgba(255,255,255,0.3); font-weight: bold;">
                                                        <?= $iniciales ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($esHoy): ?>
                                                    <div class="position-absolute bottom-0 end-0 bg-success rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 20px; height: 20px; border: 2px solid white;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                                            stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                                            <path d="M20 6L9 17l-5-5"></path>
                                                        </svg>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ms-3 flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 fw-semibold">
                                                        <?= mb_strimwidth($nombreCompleto, 0, 20, '...') ?></h6>
                                                    <span class="badge rounded-pill px-2 py-1"
                                                        style="background-color: <?= $badgeBgColor ?>; color: <?= $badgeColor ?>;">
                                                        <?= $badgeText ?>
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center mt-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" style="color: #7852a2;"
                                                        class="me-1">
                                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                                    </svg>
                                                    <span class="text-muted small"><?= $fechaFormateada ?></span>
                                                    <div class="ms-2 d-flex align-items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                            style="color: #7852a2;" class="me-1">
                                                            <path
                                                                d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z">
                                                            </path>
                                                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                                        </svg>
                                                        <span class="text-muted small">
                                                            <?= $cumpleYaPaso ? $edad + 1 : $edad ?> años
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <div class="col-xl-6 col-md-12">

        <!-- Conductores con Cuotas Vencidas -->
        <div class="card mini-stat bg-white text-dark" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06); max-height: 400px; overflow: hidden;">
                <div class="card-body d-flex flex-column" style="height: 100%; padding-bottom: 20px;">
                    <!-- Header con icono izquierda, título centro, contador y botón derecha -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <!-- Icono a la izquierda -->
                        <div style="width: 50px; height: 50px; border-radius: 15px; background-color: #eed8fc; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <img src="<?= URL::to('public/assets/images/services-icon/debt-icons-in-full-hd.png') ?>" alt="" style="width: 35px; height: 35px;">
                        </div>
                        
                        <!-- Título centrado -->
                        <div class="flex-grow-1 text-center px-2">
                            <h5 class="fw-bold text-uppercase mb-0" style="color: #333; font-size: 0.9rem;">
                                CLIENTES CON CUOTAS VENCIDAS
                            </h5>
                        </div>
                        
                        <!-- Contador y botón a la derecha -->
                        <div class="d-flex align-items-center gap-2" style="flex-shrink: 0;">
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #eed8fc; color: #5e3d82; font-weight: bold; font-size: 0.95rem;">
                                <?= $total_registros ?> registros
                            </span>
                            <button id="vencidos-ver-todos" class="btn" onclick="allConductoresCuotasVencidas()" style="background-color: #f7d54a; color: #333; font-weight: 500; padding: 8px 20px; border-radius: 8px; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s ease; white-space: nowrap;">Ver Todos</button>
                        </div>
                    </div>
                    
                    <!-- Lista de conductores con cuotas vencidas -->
                    <div id="vencidos-lista" class="flex-grow-1 cumpleanos-lista" style="max-height: 280px; overflow-y: auto; overflow-x: hidden;">
                        <?= $html_registros ?>

                        <?php if (empty($registros_vencidos)): ?>
                        <div class="text-center p-3">
                            <p style="color: #8b8c64;">No hay clientes con cuotas vencidas actualmente.</p>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

    </div>

</div>

<!-- Nueva fila para los 3 cards de pagos -->
<div class="row mt-4">
<!-- Pagos Inscripción -->
 <?php if ($_SESSION['id_rol'] != 2): // Ocultar para ASESORES ?>
<div class="col-xl-3 col-md-6">
    <div class="card mini-stat bg-white text-dark card-pagos-height" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);">
        <div class="card-body">
            <div class="mb-4">
                <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50" style="border-radius: 20px; background-color: #eed8fc;">
                    <img class="mt-3 mr-5" src="<?= URL::to('public/assets/images/services-icon/2488749.png') ?>" alt="">
                </div>
                <h5 class="fw-light text-uppercase text-black text-end">Pagos Inscripción</h5>
                <h1 class="fw-bolder text-end">S/ <?= number_format($total_pagos_inscripcion, 2, '.', ',') ?></h1>
            </div>
            <div class="pt-2">
                <p class="text-dark-50 mb-0 mt-1 text-end">
                    <?= $modo_filtro == 'dia' ? 'Día: ' . date('d/m/Y', strtotime($fecha_inicio)) : 'Período: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)) ?>
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php if ($_SESSION['id_rol'] != 2): // Ocultar para ASESORES ?>
<!-- Pagos Financiamiento -->
<div class="col-xl-3 col-md-6">
    <div class="card mini-stat bg-white text-dark card-pagos-height" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);">
        <div class="card-body">
            <div class="mb-4">
                <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50" style="border-radius: 20px; background-color: #eed8fc;">
                    <img class="mt-3 mr-5" src="<?= URL::to('public/assets/images/services-icon/financiar.png') ?>" alt="">
                </div>
                <h5 class="fw-light text-uppercase text-black text-end">Pagos Financiamiento</h5>
                <h1 class="fw-bolder text-end">S/ <?= number_format($total_pagos_financiamiento, 2, '.', ',') ?></h1>
            </div>
            <div class="pt-2">
                <p class="text-dark-50 mb-0 mt-1 text-end">
                    <?= $modo_filtro == 'dia' ? 'Día: ' . date('d/m/Y', strtotime($fecha_inicio)) : 'Período: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)) ?>
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($_SESSION['id_rol'] == 3): // Solo para Director ?>
<!-- Card Descuentos por Cuota -->
<div class="col-xl-3 col-md-6">
    <div class="card mini-stat bg-white text-dark card-pagos-height" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);">
        <div class="card-body">
            <div class="mb-2">
                <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50" style="border-radius: 20px; background-color: #eed8fc;">
                    <i class="fas fa-percentage" style="font-size: 2rem; margin: 20px 0 0 18px; color: #667eea;"></i>
                </div>
                <h5 class="fw-light text-uppercase text-black text-end">Descuentos por Cuota</h5>
                <h4 class="fw-bolder text-end mb-0" id="total-productos-descuento" style="font-size: 1.5rem;">
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                </h4>
            </div>
            <div class="pt-1">
                <p class="text-dark-50 mb-2 text-end" style="font-size: 0.8rem; line-height: 1.4;">
                    <i class="fas fa-coins text-warning"></i>
                    <span id="total-subsidiado-soles">S/ 0.00</span>
                    <br>
                    <i class="fas fa-dollar-sign text-success"></i>
                    <span id="total-subsidiado-dolares">$ 0.00</span>
                </p>
                <div class="text-center">
                    <button class="btn btn-primary btn-sm" onclick="abrirGestionDescuentos()" style="font-size: 0.75rem; padding: 4px 12px;">
                        <i class="fas fa-cog me-1"></i> Ver Todos
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (in_array($_SESSION['id_rol'], [1, 3,2])): // Admin, Asesor y Director ?>
<!-- Card Constataciones Domiciliarias -->
<div class="col-xl-3 col-md-6">
    <div class="card mini-stat bg-white text-dark card-pagos-height" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);">
        <div class="card-body">
            <div class="mb-2">
                <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50" style="border-radius: 20px; background-color: #d4f5e9;">
                    <i class="fas fa-home" style="font-size: 2rem; margin: 20px 0 0 18px; color: #28a745;"></i>
                </div>
                <h5 class="fw-light text-uppercase text-black text-end">Constataciones</h5>
                <h4 class="fw-bolder text-end mb-0" id="total-vehiculos-entregados" style="font-size: 1.5rem;">
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                </h4>
            </div>
            <div class="pt-1">
                <p class="text-dark-50 mb-2 text-end" style="font-size: 0.8rem; line-height: 1.4;">
                    <i class="fas fa-check-circle text-success"></i>
                    <span id="constataciones-realizadas">0</span> verificados
                    <br>
                    <i class="fas fa-clock text-warning"></i>
                    <span id="constataciones-pendientes">0</span> pendientes
                </p>
                <div class="text-center">
                    <button class="btn btn-success btn-sm" onclick="irAConstataciones()" style="font-size: 0.75rem; padding: 4px 12px;">
                        <i class="fas fa-map-marker-alt me-1"></i> Ver Todos
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($_SESSION['id_rol'] == 3): // Solo para Director ?>
<!-- Card Recaudaciones Caja Arequipa -->
<div class="col-xl-3 col-md-6">
    <div class="card mini-stat bg-white text-dark card-pagos-height" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);">
        <div class="card-body">
            <div class="mb-2">
                <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50" style="border-radius: 20px; background-color: #fef3c7;">
                    <i class="fas fa-money-bill-wave" style="font-size: 2rem; margin: 20px 0 0 18px; color: #f59e0b;"></i>
                </div>
                <h5 class="fw-light text-uppercase text-black text-end">Recaudaciones</h5>
                <h4 class="fw-bolder text-end mb-0" style="font-size: 1.5rem;">
                    Caja Arequipa
                </h4>
            </div>
            <div class="pt-1">
                <p class="text-dark-50 mb-2 text-end" style="font-size: 0.8rem; line-height: 1.4;">
                    <i class="fas fa-university text-warning"></i> Pagos recibidos
                    <br>
                    <i class="fas fa-chart-line text-info"></i> Ver estadísticas
                </p>
                <div class="text-center">
                    <button class="btn btn-warning btn-sm" onclick="irARecaudaciones()" style="font-size: 0.75rem; padding: 4px 12px;">
                        <i class="fas fa-eye me-1"></i> Ver Recaudaciones
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

    <!-- Tarjeta de Total de Conductores -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= URL::to('/conductores') ?>" style="text-decoration: none; color: inherit;">
            <div class="card mini-stat bg-white text-dark card-pagos-height"
                 style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
                 onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 12px -2px rgba(0,0,0,.2)';"
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,.1)';">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50"
                            style="border-radius: 20px; background-color: #eed8fc;">
                            <img class="mt-3 mr-5" src="<?= URL::to('public/assets/images/services-icon/173-512.png') ?>" alt="">
                        </div>
                        <h5 class="fw-light text-uppercase text-black text-end">Total de Conductores</h5>
                        <h1 class="fw-bolder text-end"><?= $cantidad_conductores ?></h1>
                    </div>
                    <div class="pt-2">
                        <p class="text-dark-50 mb-0 mt-1 text-end"></p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Tarjeta de Total de Clientes -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= URL::to('/ver-clientes') ?>" style="text-decoration: none; color: inherit;">
            <div class="card mini-stat bg-white text-dark card-pagos-height"
                 style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
                 onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 12px -2px rgba(0,0,0,.2)';"
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,.1)';">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50"
                             style="border-radius: 20px; background-color: #eed8fc;">
                            <img class="mt-3 mr-5" src="<?= URL::to('public/assets/images/services-icon/user-icon.png') ?>" alt="">
                        </div>
                        <h5 class="fw-light text-uppercase text-black text-end">Total de Clientes</h5>
                        <h1 class="fw-bolder text-end"><?= $cantidad_clientes ?></h1>
                    </div>
                    <div class="pt-2">
                        <p class="text-dark-50 mb-0 mt-1 text-end"></p>
                    </div>
                </div>
            </div>
        </a>
    </div>

</div>

<?php if ($_SESSION['id_rol'] != 2): // Ocultar para ASESORES ?>
<!-- ========== SECCIÓN DASHBOARD CREDIGO ========== -->
<div class="row mt-4">
    <div class="col-12">
        <h5 class="mb-5" style="color: #7852a2; font-weight: 600;">
            <i class="fas fa-chart-line me-2"></i>Analíticas CrediGo
        </h5>
    </div>
</div>

<div class="row">
    <!-- Tarjeta 1: Ventas Totales CrediGo -->
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-white text-dark"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <div class="mb-4">
                    <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50"
                        style="border-radius: 20px; background-color: #d4efdf;">
                        <img class="mt-3 mr-5" src="<?= URL::to('public/assets/images/services-icon/financiar.png') ?>" alt="">
                    </div>
                    <h5 class="fw-light text-uppercase text-black text-end">Ventas CrediGo</h5>
                    <h1 class="fw-bolder text-end" style="color: #1d8348;">
                        S/ <?= number_format($total_ventas_credigo, 2, '.', ',') ?>
                    </h1>
                </div>
                <div class="pt-2">
                    <p class="text-dark-50 mb-0 mt-1 text-end">
                        <?= $cantidad_financiamientos ?> financiamiento<?= $cantidad_financiamientos != 1 ? 's' : '' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta 2: Ganancias CrediGo -->
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-white text-dark"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <div class="mb-4">
                    <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50"
                        style="border-radius: 20px; background-color: #fcf3cf;">
                        <img class="mt-3 mr-5" src="<?= URL::to('public/assets/images/services-icon/02.png') ?>" alt="">
                    </div>
                    <h5 class="fw-light text-uppercase text-black text-end">Ganancias CrediGo</h5>
                    <h1 class="fw-bolder text-end" style="color: #f39c12;">
                        S/ <?= number_format($ganancias_credigo, 2, '.', ',') ?>
                    </h1>
                </div>
                <div class="pt-2">
                    <p class="text-dark-50 mb-0 mt-1 text-end">
                        Intereses cobrados
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta 3: Comparativa Mensual -->
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-white text-dark"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <div class="mb-4">
                    <div class="position-absolute top-0 start-15 translate-middle border-radius-xl mini-stat-img mt-3 w-25 h-50"
                        style="border-radius: 20px; background-color: <?= $porcentaje_cambio >= 0 ? '#d4efdf' : '#fadbd8' ?>;">
                        <img class="mt-3 mr-5" src="<?= URL::to('public/assets/images/services-icon/03.png') ?>" alt="">
                    </div>
                    <h5 class="fw-light text-uppercase text-black text-end">Mes Actual vs Anterior</h5>
                    <h1 class="fw-bolder text-end" style="color: <?= $porcentaje_cambio >= 0 ? '#1d8348' : '#c0392b' ?>;">
                        <?= $porcentaje_cambio >= 0 ? '+' : '' ?><?= number_format($porcentaje_cambio, 1) ?>%
                    </h1>
                </div>
                <div class="pt-2">
                    <p class="text-dark-50 mb-0 mt-1 text-end">
                        Actual: S/ <?= number_format($ventas_mes_actual, 2, '.', ',') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjeta 4: Botón Ver Reportes -->
    <div class="col-xl-3 col-md-6">
        <div class="card mini-stat bg-white text-dark"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
            onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 12px -2px rgba(0,0,0,.2)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,.1)';"
            onclick="window.location.href='<?= URL::to('/reportes-credigo') ?>'">
            <div class="card-body d-flex flex-column justify-content-center align-items-center" style="min-height: 200px;">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-chart-bar" style="font-size: 3rem; color: #7852a2;"></i>
                    </div>
                    <h5 class="fw-bold" style="color: #7852a2;">Ver Reportes Detallados</h5>
                    <p class="text-muted mb-0">Gráficas y análisis completo</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ========== FIN SECCIÓN CREDIGO ========== -->
<?php endif; ?>

<?php if ($_SESSION['id_rol'] != 2): // Ocultar para ASESORES ?>
<!-- end row -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body h-50">
                <h4 class="card-title mb-4">Estadísticas</h4>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Filtrar por:</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="switchPeriodo">
                        <label class="form-check-label" for="switchPeriodo">Semana / Mes</label>
                        <button id="btnPrev"></button> <!-- 🔹 Eliminado el texto del botón -->
                        <button id="btnNext"></button> <!-- 🔹 Eliminado el texto del botón -->
                    </div>
                </div>
                <canvas id="chart-estadisticas"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body h-50">
                <h4 class="card-title mb-4">Venta Anual</h4>
                <div class="row">
                    <div class="col-lg-7">
                        <div>
                            <canvas id="chart-with-area">
                            </canvas>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <p class="text-muted mb-4">Este Mes</p>
                                    <h3>S/ <?= number_format($data['totalv'] ?? 0.0, 2, '.', ',') ?></h3>
                                    <p class="text-muted mb-5">Ganancias Totales.</p>
                                    <span class="peity-donut"
                                        data-peity='{ "fill": ["#02a499", "#f2f2f2"], "innerRadius": 28, "radius": 32 }'
                                        data-width="72" data-height="72"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <p class="text-muted mb-4">Mes Anterior</p>
                                    <h3>S/ <?= number_format($data['totalvMA'] ?? 0.0, 2, '.', ',') ?></h3>
                                    <p class="text-muted mb-5">Comparativa Ganancias Totales.</p>
                                    <span class="peity-donut"
                                        data-peity='{ "fill": ["#02a499", "#f2f2f2"], "innerRadius": 28, "radius": 32 }'
                                        data-width="72" data-height="72"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
        </div>
        <!-- end card -->
    </div>
</div>
 <?php endif; ?>

<!-- end row -->

<textarea style="display: none" id="listatempdata"><?= json_encode($dataListVen) ?></textarea>

<textarea style="display: none" id="data-conductores-semanal"><?= json_encode($conductoresSemanal) ?></textarea>
<textarea style="display: none" id="data-conductores-mensual"><?= json_encode($conductoresMensual) ?></textarea>

<textarea style="display: none" id="data-pagos-semanal"><?= json_encode($pagosSemanal) ?></textarea>
<textarea style="display: none" id="data-pagos-mensual"><?= json_encode($pagosMensual) ?></textarea>

<textarea style="display: none"
    id="data-financiamiento-ins-semanal"><?= json_encode($financiamientoInsSemanal) ?></textarea>
<textarea style="display: none"
    id="data-financiamiento-ins-mensual"><?= json_encode($financiamientoInsMensual) ?></textarea>

<textarea style="display: none"
    id="data-financiamiento-prod-semanal"><?= json_encode($financiamientoProdSemanal) ?></textarea>
<textarea style="display: none"
    id="data-financiamiento-prod-mensual"><?= json_encode($financiamientoProdMensual) ?></textarea>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>


<script>
    $(document).ready(function () {

        let miGrafico = null;
        let datosSemanales = [];

        function cambiarGrafico(modo) {
            if (modo === 'mensual') {
                cargarGraficoMensual();
            } else if (modo === 'semanal') {
                if (miGrafico) {
                    miGrafico.destroy();
                }
                datosSemanales = [];
                cargarGraficoSemanal();
            }
        }

        console.log("Depuración de datos desde PHP:");

        function printJson(id, data) {
            let jsonData = JSON.stringify(data);
            console.log(id, jsonData);
            document.getElementById(id).value = jsonData;
        }

        printJson("data-conductores-semanal", <?php echo json_encode($conductoresSemanal); ?>);
        printJson("data-conductores-mensual", <?php echo json_encode($conductoresMensual); ?>);
        printJson("data-pagos-semanal", <?php echo json_encode($pagosSemanal); ?>);
        printJson("data-pagos-mensual", <?php echo json_encode($pagosMensual); ?>);
        printJson("data-financiamiento-ins-semanal", <?php echo json_encode($financiamientoInsSemanal); ?>);
        printJson("data-financiamiento-ins-mensual", <?php echo json_encode($financiamientoInsMensual); ?>);
        printJson("data-financiamiento-prod-semanal", <?php echo json_encode($financiamientoProdSemanal); ?>);
        printJson("data-financiamiento-prod-mensual", <?php echo json_encode($financiamientoProdMensual); ?>);

        let ctx = document.getElementById("chart-estadisticas").getContext("2d");

        function parseData(id) {
            let el = document.getElementById(id);
            return el && el.value ? JSON.parse(el.value) : [];
        }

        let datosSemanal = {
            conductores: parseData("data-conductores-semanal").reverse(),
            pagos: parseData("data-pagos-semanal").reverse(),
            financiamientoIns: parseData("data-financiamiento-ins-semanal").reverse(),
            financiamientoProd: parseData("data-financiamiento-prod-semanal").reverse()
        };

        let datosMensual = {
            conductores: parseData("data-conductores-mensual").reverse(),
            pagos: parseData("data-pagos-mensual").reverse(),
            financiamientoIns: parseData("data-financiamiento-ins-mensual").reverse(),
            financiamientoProd: parseData("data-financiamiento-prod-mensual").reverse()
        };

        const ELEMENTOS_POR_PAGINA = 4;
        let estadoActual = "semanal";
        let paginaActual = 0;

        const nombresMeses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

        function formatearSemana(semana) {
            if (!semana || semana.length < 6) return "Sin fecha";
            return semana.slice(0, 4) + "-" + semana.slice(4);
        }

        function formatearMes(mes) {
            let anio = mes.slice(0, 4);
            let mesNum = parseInt(mes.slice(4, 6)) - 1;
            return nombresMeses[mesNum] ? nombresMeses[mesNum] + " " + anio : "Sin fecha";
        }

        function obtenerDatos(estado, pagina = 0) {
            let datos = estado === "semanal" ? datosSemanal : datosMensual;
            if (!datos.conductores || datos.conductores.length === 0) {
                console.warn(`⚠️ No hay datos disponibles para ${estado}`);
                return { labels: [], datasets: [] };
            }

            let totalElementos = datos.conductores.length;
            let totalPaginas = Math.ceil(totalElementos / ELEMENTOS_POR_PAGINA);

            pagina = Math.max(0, Math.min(pagina, totalPaginas - 1));
            paginaActual = pagina;

            let inicio = pagina * ELEMENTOS_POR_PAGINA;
            let fin = Math.min(inicio + ELEMENTOS_POR_PAGINA, totalElementos);

            console.log(`Paginación: Página ${pagina}, Mostrando elementos ${inicio} a ${fin - 1} de ${totalElementos}`);

            let conductoresSubset = datos.conductores.slice(inicio, fin);
            let pagosSubset = datos.pagos.slice(inicio, fin);
            let financiamientoInsSubset = datos.financiamientoIns.slice(inicio, fin);
            let financiamientoProdSubset = datos.financiamientoProd.slice(inicio, fin);

            let labels = conductoresSubset.map(item => {
                if (item.semana) {
                    return formatearSemana(item.semana);
                } else if (item.mes) {
                    return formatearMes(item.mes);
                } else {
                    return "Sin fecha";
                }
            });

            let conductoresData = conductoresSubset.map(item => item.total || 0);
            let pagosData = pagosSubset.map(item => item.total || 0);
            let financiamientoInsData = financiamientoInsSubset.map(item => item.total || 0);
            let financiamientoProdData = financiamientoProdSubset.map(item => item.total || 0);

            return {
                labels: labels,
                datasets: [
                    {
                        label: "Conductores Registrados",
                        data: conductoresData,
                        backgroundColor: "rgba(75, 192, 192, 0.5)",
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1
                    },
                    {
                        label: "Pagos por Inscripción",
                        data: pagosData,
                        backgroundColor: "rgba(238, 216, 252, 0.5)",
                        borderColor: "rgba(238, 216, 252, 1)",
                        borderWidth: 1
                    },
                    {
                        label: "Financiamientos de Inscripciones",
                        data: financiamientoInsData,
                        backgroundColor: "rgba(252, 243, 75, 0.5)",
                        borderColor: "rgba(252, 243, 75, 1)",
                        borderWidth: 1
                    },
                    {
                        label: "Financiamiento de Productos",
                        data: financiamientoProdData,
                        backgroundColor: "rgba(255, 99, 132, 0.5)",
                        borderColor: "rgba(255, 99, 132, 1)",
                        borderWidth: 1
                    }
                ]
            };
        }

        function cambiarPagina(delta) {
            let totalPaginas = Math.ceil(datosSemanal.conductores.length / ELEMENTOS_POR_PAGINA);
            paginaActual = Math.max(0, Math.min(paginaActual + delta, totalPaginas - 1));
            cargarGrafico();
        }

        function cargarGrafico() {
            if (miGrafico) {
                miGrafico.destroy();
            }
            let datos = obtenerDatos(estadoActual, paginaActual);
            miGrafico = new Chart(ctx, { type: 'bar', data: datos });
        }


        // Crear el gráfico
        let chart = new Chart(ctx, {
            type: "bar",
            data: obtenerDatos(estadoActual, paginaActual),
            options: {
                plugins: {
                    zoom: {
                        pan: {
                            enabled: true,
                            mode: 'x',
                        },
                        zoom: {
                            wheel: {
                                enabled: true,
                            },
                            pinch: {
                                enabled: true
                            },
                            mode: 'x'
                        }
                    },
                    legend: {
                        labels: {
                            font: { size: 14, weight: "bold" }, // 🔹 Letras más nítidas en la leyenda
                            color: "white", // 🔹 Color blanco para visibilidad
                            textShadow: "0px 0px 5px #ffffff" // 🔹 Efecto de brillo sutil
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 14, weight: "bold" }, // 🔹 Letras más nítidas en negrita
                            color: "#00FFFF", // 🔹 Color azul neón para contraste futurista
                            textShadow: "0px 0px 8px #00FFFF" // 🔹 Efecto de brillo en el texto
                        },
                        grid: {
                            color: "rgba(0, 255, 255, 0.3)" // 🔹 Líneas azules neón semi-transparente en X
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 14, weight: "bold" }, // 🔹 Letras más nítidas en negrita
                            color: "#00FFFF", // 🔹 Color azul neón para contraste futurista
                            textShadow: "0px 0px 8px #00FFFF" // 🔹 Efecto de brillo en el texto
                        },
                        grid: {
                            color: "rgba(0, 255, 255, 0.3)" // 🔹 Líneas azules neón semi-transparente en Y
                        }
                    }
                },
                layout: {
                    padding: 20 // 🔹 Margen interno para evitar que las etiquetas queden pegadas
                },
                animation: {
                    duration: 1500, // 🔹 Animación de entrada más fluida
                    easing: "easeInOutQuart" // 🔹 Suaviza la animación para que se vea más profesional
                },
                backgroundColor: "rgba(10, 10, 20, 0.95)", // 🔹 Fondo negro con leve transparencia para efecto "holográfico"
                elements: {
                    bar: {
                        backgroundColor: [
                            "rgba(0, 255, 255, 0.6)", // 🔹 Barras con colores neón (cyan)
                            "rgba(255, 0, 255, 0.6)", // 🔹 Magenta
                            "rgba(255, 255, 0, 0.6)", // 🔹 Amarillo
                            "rgba(0, 255, 0, 0.6)"  // 🔹 Verde neón
                        ],
                        borderColor: [
                            "rgba(0, 255, 255, 1)", // 🔹 Borde más brillante para el efecto futurista
                            "rgba(255, 0, 255, 1)",
                            "rgba(255, 255, 0, 1)",
                            "rgba(0, 255, 0, 1)"
                        ],
                        borderWidth: 2, // 🔹 Bordes más marcados para un estilo elegante
                        borderRadius: 8 // 🔹 Esquinas redondeadas para que se vea más moderno
                    }
                }
            }
        });

        // Función para cambiar de página (navegar entre grupos de semanas/meses)
        function cambiarPagina(incremento) {
            // Determinar qué conjunto de datos usar
            let datos = estadoActual === "semanal" ? datosSemanal : datosMensual;

            // Verificar que hay datos disponibles
            if (!datos.conductores || datos.conductores.length === 0) {
                console.error(`⚠️ No hay datos disponibles para ${estadoActual}`);
                return;
            }

            // Calcular el total de páginas
            let totalElementos = datos.conductores.length;
            let totalPaginas = Math.ceil(totalElementos / ELEMENTOS_POR_PAGINA);

            // Guardar la página anterior para comparar
            let paginaAnterior = paginaActual;

            // Calcular nueva página
            paginaActual += incremento;

            // Validar límites
            if (paginaActual < 0) {
                paginaActual = 0;
                console.log("⚠️ Ya estás en la página más reciente");
            }
            if (paginaActual >= totalPaginas) {
                paginaActual = totalPaginas - 1;
                console.log("⚠️ Ya estás en la página más antigua");
            }

            // Verificar si realmente cambió
            if (paginaAnterior !== paginaActual) {
                console.log(`📅 Cambiando de página ${paginaAnterior} a ${paginaActual} (de ${totalPaginas} páginas)`);

                // Obtener los nuevos datos
                let datosActualizados = obtenerDatos(estadoActual, paginaActual);

                // Actualizar el gráfico
                chart.data = datosActualizados;
                chart.update();

                // Actualizar información de navegación
                actualizarInfoNavegacion();
            } else {
                console.log(`⚠️ No se cambió la página: ${paginaActual} (de ${totalPaginas} páginas)`);
            }
        }

        // Función para cambiar entre semanal y mensual
        function cambiarPeriodo(esMensual) {
            let nuevoEstado = esMensual ? "mensual" : "semanal";

            if (nuevoEstado === estadoActual) {
                return; // No hacer nada si ya estamos en ese estado
            }

            console.log(`📊 Cambiando de ${estadoActual} a ${nuevoEstado}`);

            // Cambiar el estado
            estadoActual = nuevoEstado;

            // Resetear a la primera página (más reciente)
            paginaActual = 0;

            // Obtener los datos para el nuevo estado
            let datosActualizados = obtenerDatos(estadoActual, paginaActual);

            // Actualizar el gráfico
            chart.data = datosActualizados;
            chart.update();

            // Actualizar información de navegación
            actualizarInfoNavegacion();
        }

        // Función para actualizar la información de navegación en la UI
        function actualizarInfoNavegacion() {
            let infoNavegacion = document.getElementById("info-navegacion");
            if (infoNavegacion) {
                let datos = estadoActual === "semanal" ? datosSemanal : datosMensual;
                let totalElementos = datos.conductores.length;

                let inicio = Math.max(0, totalElementos - (paginaActual + 1) * ELEMENTOS_POR_PAGINA) + 1;
                let fin = Math.min(totalElementos, totalElementos - paginaActual * ELEMENTOS_POR_PAGINA);

                infoNavegacion.textContent = `Mostrando ${estadoActual === "semanal" ? "semanas" : "meses"} ${inicio} a ${fin} de ${totalElementos} (más recientes a la derecha)`;
            }
        }

        // Configurar eventos cuando el DOM esté listo
        $(document).ready(function () {
            console.log("DOM listo, configurando eventos...");

            // Botones de navegación
            $("#btnNext").on("click", function () {
                console.log("Botón Izquierdo clickeado (hacia atrás en el tiempo)");
                cambiarPagina(1); // Incremento positivo para ir hacia atrás en el tiempo (datos más antiguos)
            });

            $("#btnPrev").on("click", function () {
                console.log("Botón Derecho clickeado (hacia adelante en el tiempo)");
                cambiarPagina(-1); // Incremento negativo para ir hacia adelante en el tiempo (datos más recientes)
            });

            // Switch de período
            $("#switchPeriodo").on("change", function () {
                let esMensual = $(this).is(":checked");
                console.log(`Switch cambiado: ${esMensual ? "Mensual" : "Semanal"}`);
                cambiarPeriodo(esMensual);
            });

            // Crear un elemento para mostrar información de navegación
            let contenedorGrafico = $("#chart-estadisticas").parent();
            if (contenedorGrafico.length > 0) {
                let infoNavegacion = $("<div>")
                    .attr("id", "info-navegacion")
                    .css({
                        "text-align": "center",
                        "margin-top": "10px",
                        "font-weight": "bold",
                        "color": "#00FFFF"
                    });

                contenedorGrafico.append(infoNavegacion);

                // Mostrar información inicial
                actualizarInfoNavegacion();
            }

            // Agregar etiquetas a los botones para mayor claridad
            $("#btnPrev").html("");
            $("#btnNext").html("");
        });

        // Inicializar el gráfico de ventas anuales
        new Chart("chart-with-area", {
            type: "line",
            data: {
                labels: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                datasets: [{
                    label: 'Ventas',
                    data: JSON.parse($("#listatempdata").val()),
                    borderColor: "#626ed4",
                    backgroundColor: "rgba(98,110,212,0.36)",
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'S/ ' + value.toLocaleString('es-PE')
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'S/ ' + context.parsed.y.toLocaleString('es-PE');
                            }
                        }
                    }
                }
            }
        });

    });
</script>

<!-- Agregar el script de Flatpickr para el calendario -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Configuración inicial del calendario
        let modoActual = document.querySelector('input[name="modo_filtro"]:checked').value;
        configurarCalendario(modoActual);

        // Cambiar configuración cuando cambia el modo
        document.querySelectorAll('input[name="modo_filtro"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                configurarCalendario(this.value);
            });
        });

        function configurarCalendario(modo) {
            let opciones = {
                locale: 'es',
                dateFormat: 'Y-m-d',
                altInput: true
            };

            if (modo === 'dia') {
                // Configuración para seleccionar un día específico
                opciones.altFormat = 'd/m/Y';
                opciones.mode = 'single';
            } else {
                // Configuración para seleccionar un mes
                opciones.altFormat = 'F Y';
                opciones.plugins = [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: 'Y-m-d',
                        altFormat: 'F Y'
                    })
                ];
            }

            // Destruir instancia anterior si existe
            if (calendarioInstance) {
                calendarioInstance.destroy();
            }

            // Crear nueva instancia
            let calendarioInstance = flatpickr('#fecha_selector', opciones);

            // Actualizar fecha fin cuando cambia la fecha inicio en modo mes
            if (modo === 'mes') {
                calendarioInstance.config.onChange = function (selectedDates) {
                    if (selectedDates.length > 0) {
                        let primerDia = new Date(selectedDates[0]);
                        primerDia.setDate(1);

                        let ultimoDia = new Date(selectedDates[0].getFullYear(), selectedDates[0].getMonth() + 1, 0);

                        document.getElementById('fecha_selector').value = primerDia.toISOString().split('T')[0];
                        document.getElementById('fecha_fin').value = ultimoDia.toISOString().split('T')[0];
                    }
                };
            } else {
                // En modo día, la fecha fin es igual a la fecha inicio
                calendarioInstance.config.onChange = function (selectedDates) {
                    if (selectedDates.length > 0) {
                        document.getElementById('fecha_fin').value = selectedDates[0].toISOString().split('T')[0];
                    }
                };
            }
        }
    });

    function allConductoresCuotasVencidas() {
        window.location.href = "<?= URL::to('/conductores-cuotas-vencidas') ?>";
    }
</script>

<script src="<?= URL::to('public/js/filtro-calendario.js') ?>?v=<?= time() ?>"></script>

<script>
    $(document).ready(function () {
        // Manejador de evento para el botón de filtrar
        $('#btn_filtrar').on('click', function (e) {
            e.preventDefault(); // Evita la recarga de la página

            let modoFiltro = $('input[name="modo_filtro"]:checked').val();
            let fechaInicio = '';
            let fechaFin = '';

            if (modoFiltro === 'dia') {
                fechaInicio = $('#fecha_selector').val();
                fechaFin = $('#fecha_selector').val();
            } else {
                let mes = $('#mes_selector').val();
                let anio = $('#anio_selector').val();

                // Formatear la fecha de inicio y fin para el mes seleccionado
                fechaInicio = anio + '-' + String(mes).padStart(2, '0') + '-01';
                fechaFin = anio + '-' + String(mes).padStart(2, '0') + '-' + new Date(anio, mes, 0).getDate();
            }

            // Construir la URL con los parámetros
            let url = '<?= URL::to('/') ?>?modo_filtro=' + modoFiltro + '&fecha_inicio=' + fechaInicio + '&fecha_fin=' + fechaFin;

            // Redirigir a la URL construida
            window.location.href = url;
        });

        // Manejador de evento para el botón de restablecer
        $('#btn_restablecer').on('click', function () {
            window.location.href = '<?= URL::to('/') ?>'; // Redirige a la página principal
        });

        // Cargar datos del card de Descuentos por Cuota
        <?php if ($_SESSION['id_rol'] == 3): ?>
        cargarResumenDescuentos();
        <?php endif; ?>
    });

    // Función para cargar resumen de descuentos
    function cargarResumenDescuentos() {
        $.ajax({
            url: _URL +'/ajs/obtenerResumenDescuentos',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#total-productos-descuento').html(
                        '<i class="fas fa-box text-primary"></i> ' + 
                        response.total_productos + ' productos'
                    );
                    $('#total-subsidiado-soles').text('S/ ' + parseFloat(response.total_soles).toFixed(2));
                    $('#total-subsidiado-dolares').text('$ ' + parseFloat(response.total_dolares).toFixed(2));
                } else {
                    $('#total-productos-descuento').html('<span class="text-muted">Sin datos</span>');
                }
            },
            error: function() {
                $('#total-productos-descuento').html('<span class="text-danger">Error al cargar</span>');
            }
        });
    }

    // Función para abrir la gestión de descuentos
    function abrirGestionDescuentos() {
        window.location.href = '<?= URL::to('/gestion-descuentos') ?>';
    }

    // Función para cargar estadísticas de constataciones domiciliarias
    <?php if (in_array($_SESSION['id_rol'], [1, 3, 2])): ?>
    function cargarConstatacionesStats() {
        $.ajax({
            url: _URL + '/ajs/constataciones/contador',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#total-vehiculos-entregados').html(
                        '<i class="fas fa-car text-success"></i> ' +
                        response.total + ' vehículos'
                    );
                    $('#constataciones-realizadas').text(response.realizadas);
                    $('#constataciones-pendientes').text(response.pendientes);
                } else {
                    $('#total-vehiculos-entregados').html('<span class="text-muted">Sin datos</span>');
                }
            },
            error: function() {
                $('#total-vehiculos-entregados').html('<span class="text-danger">Error</span>');
            }
        });
    }

    // Función para ir a constataciones domiciliarias
    function irAConstataciones() {
        window.location.href = '<?= URL::to('/constataciones-domiciliarias') ?>';
    }

    // Función para ir a recaudaciones
    function irARecaudaciones() {
        window.location.href = '<?= URL::to('/recaudaciones') ?>';
    }

    // Cargar al iniciar
    $(document).ready(function() {
        cargarConstatacionesStats();
    });
    <?php endif; ?>
</script>