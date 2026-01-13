<?php

class GrupoFinanciamientoModel
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getUltimoIdInsertado()
    {
        return $this->conectar->insert_id;  // Obtener el último ID insertado
    }

    public function obtenerGruposFinanciamiento()
    {
        $sql = 'SELECT idgrupoVehicular_financiamiento, nombre FROM grupovehicular_financiamiento';
        $stmt = $this->conectar->prepare($sql);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $grupos = $result->fetch_all(MYSQLI_ASSOC);
            return $grupos;
        } else {
            return [];
        }
    }

    public function insertarPlan($nombrePlan, $cuotaInicial, $montoCuota, $cantidadCuotas, $frecuenciaPago, $moneda, $tasaInteres, $monto, $montoSinInteres, $fechaInicio, $fechaFin, $tipoVehicular, $estado = 'activo', $cobrarMora = 1, $esYango = 0, $aplicaComision = 1, $montoComision = null, $monedaComision = 'S/.')
    {
        // 🔹 Convertir valores vacíos a null para evitar errores
        $cuotaInicial = $cuotaInicial !== '' ? $cuotaInicial : null;
        $montoCuota = $montoCuota !== '' ? $montoCuota : null;
        $cantidadCuotas = $cantidadCuotas !== '' ? $cantidadCuotas : null;
        $tasaInteres = $tasaInteres !== '' ? $tasaInteres : null;
        $monto = $monto !== '' ? $monto : null;
        $montoSinInteres = $montoSinInteres !== '' ? $montoSinInteres : null;
        $fechaInicio = $fechaInicio !== '' ? $fechaInicio : null;
        $fechaFin = $fechaFin !== '' ? $fechaFin : null;
        $montoComision = $montoComision !== '' ? $montoComision : null;  // ✅ NUEVO

        if ($tipoVehicular === 'auto') {
            $tipoVehicular = 'vehiculo';
        } elseif ($tipoVehicular === 'moto') {
            $tipoVehicular = 'moto';
        } else {
            $tipoVehicular = null;
        }

        $sql = 'INSERT INTO planes_financiamiento
        (nombre_plan, cuota_inicial, monto_cuota, cantidad_cuotas, frecuencia_pago, moneda, tasa_interes, monto, monto_sin_interes, fecha_inicio, fecha_fin, tipo_vehicular, cobrar_mora, estado, es_yango, aplica_comision, monto_comision, moneda_comision)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = $this->conectar->prepare($sql);

        if (!$stmt) {
            die('Error en la preparación de la consulta: ' . $this->conectar->error);
        }

        $stmt->bind_param('ssssssssssssisiiss',  // ✅ Agregado: aplica_comision (i), monto_comision (s), moneda_comision (s)
            $nombrePlan,
            $cuotaInicial,
            $montoCuota,
            $cantidadCuotas,
            $frecuenciaPago,
            $moneda,
            $tasaInteres,
            $monto,
            $montoSinInteres,
            $fechaInicio,
            $fechaFin,
            $tipoVehicular,
            $cobrarMora,  // i - integer
            $estado,  // s - string
            $esYango,  // i - integer
            $aplicaComision,  // i - integer (✅ NUEVO)
            $montoComision,  // s - string/decimal (✅ NUEVO)
            $monedaComision  // s - string (✅ NUEVO)
        );

        // 🔹 Ejecutar la consulta y verificar si fue exitosa
        if ($stmt->execute()) {
            // 🔹 Retornar el ID del plan insertado
            return $this->conectar->insert_id;
        }
    }

    public function insertVariante($idPlan, $variantes)
    {
        $sql = 'INSERT INTO grupos_variantes (
            idplan_financiamiento, 
            nombre_variante, 
            cuota_inicial,
            monto_inscripcion,
            monto_cuota, 
            cantidad_cuotas,
            penalizacion_mora,
            frecuencia_pago, 
            moneda, 
            tasa_interes, 
            monto, 
            monto_sin_interes, 
            fecha_inicio, 
            fecha_fin
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = $this->conectar->prepare($sql);

        if (!$stmt) {
            return false;
        }

        // Iterar sobre cada variante
        foreach ($variantes as $variante) {
            // Convertir valores vacíos a null
            $cuotaInicial = $variante['cuota_inicial'] !== '' ? $variante['cuota_inicial'] : null;
            $montoInscripcion = isset($variante['monto_inscripcion']) && $variante['monto_inscripcion'] !== '' ? $variante['monto_inscripcion'] : null;
            $montoCuota = $variante['monto_cuota'] !== '' ? $variante['monto_cuota'] : null;
            $cantidadCuotas = $variante['cantidad_cuotas'] !== '' ? $variante['cantidad_cuotas'] : null;
            $penalizacionMora = isset($variante['penalizacion_mora']) ? $variante['penalizacion_mora'] : null;
            $tasaInteres = $variante['tasa_interes'] !== '' ? $variante['tasa_interes'] : null;
            $monto = $variante['monto'] !== '' ? $variante['monto'] : null;
            $montoSinInteres = $variante['monto_sin_interes'] !== '' ? $variante['monto_sin_interes'] : null;
            $fechaInicio = $variante['fecha_inicio'] !== '' ? $variante['fecha_inicio'] : null;
            $fechaFin = $variante['fecha_fin'] !== '' ? $variante['fecha_fin'] : null;

            // 14 variables, 14 tipos (agregado monto_inscripcion)
            $stmt->bind_param('isddddsssdddss',
                $idPlan,
                $variante['nombre_variante'],
                $cuotaInicial,
                $montoInscripcion,
                $montoCuota,
                $cantidadCuotas,
                $penalizacionMora,
                $variante['frecuencia_pago'],
                $variante['moneda'],
                $tasaInteres,
                $monto,
                $montoSinInteres,
                $fechaInicio,
                $fechaFin);

            if (!$stmt->execute()) {
                return false;
            }
        }

        return true;
    }

    public function getAllPlanes()
    {
        $sql = 'SELECT * FROM planes_financiamiento';
        $result = $this->conectar->query($sql);  // Modificado: Ahora usamos query() en MySQLi en lugar de prepare/execute de PDO

        $planes = [];
        while ($row = $result->fetch_assoc()) {  // Modificado: Usamos fetch_assoc() en lugar de fetchAll(PDO::FETCH_ASSOC)
            $planes[] = $row;
        }

        return $planes;
    }

    public function saveAsociation($codigo, $idPlan)
    {
        // Buscar el producto por código o código de barra
        $sql = 'SELECT idproductosv2 FROM productosv2 WHERE codigo = ? OR codigo_barra = ? LIMIT 1';  // Modificado: Se usa ? para prevenir SQL Injection
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('ss', $codigo, $codigo);  // Modificado: Usamos bind_param() para vincular valores
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $idProducto = $row['idproductosv2'];

            // Actualizar el id_plan en el producto encontrado
            $updateSql = 'UPDATE productosv2 SET id_plan = ? WHERE idproductosv2 = ?';  // Modificado: Se usa ? para seguridad
            $stmt = $this->conectar->prepare($updateSql);
            $stmt->bind_param('ii', $idPlan, $idProducto);  // Modificado: Se pasan los valores con bind_param()
            return $stmt->execute();  // Ejecutar la actualización
        }
        return false;
    }

    public function editarGrupo($id, $nombrePlan, $cuotaInicial, $montoCuota, $cantidadCuotas,
        $frecuenciaPago, $moneda, $monto, $montoSinInteres, $tasaInteres,
        $fechaInicio, $fechaFin, $tipoVehicular = null, $estado = 'activo', $cobrarMora = 1, $esYango = 0,
        $aplicaComision = 1, $montoComision = null, $monedaComision = 'S/.')
    {
        // CORREGIDO: Validar y limpiar tipoVehicular
        if ($tipoVehicular === '' || $tipoVehicular === 'null' || $tipoVehicular === null) {
            $tipoVehicular = null;
        } elseif ($tipoVehicular === 'auto') {
            $tipoVehicular = 'vehiculo';
        } elseif ($tipoVehicular === 'moto') {
            $tipoVehicular = 'moto';
        } else {
            $tipoVehicular = null;  // Forzar a null si el valor no es válido
        }

        $sql = 'UPDATE planes_financiamiento SET 
            nombre_plan = ?,  
            cuota_inicial = ?,  
            monto_cuota = ?,  
            cantidad_cuotas = ?,  
            frecuencia_pago = ?,  
            moneda = ?,  
            monto = ?,  
            monto_sin_interes = ?,  
            tasa_interes = ?,  
            fecha_inicio = ?,  
            fecha_fin = ?,
            tipo_vehicular = ?,
            cobrar_mora = ?,
            estado = ?,
            es_yango = ?,
            aplica_comision = ?,
            monto_comision = ?,
            moneda_comision = ?
        WHERE idplan_financiamiento = ?';

        $stmt = $this->conectar->prepare($sql);

        if (!$stmt) {
            throw new Exception('Error en la preparación de la consulta: ' . $this->conectar->error);
        }

        // Convertir valores vacíos a NULL para evitar problemas en bind_param
        $cuotaInicial = ($cuotaInicial !== null && $cuotaInicial !== '') ? $cuotaInicial : null;
        $montoCuota = ($montoCuota !== null && $montoCuota !== '') ? $montoCuota : null;
        $cantidadCuotas = ($cantidadCuotas !== null && $cantidadCuotas !== '') ? $cantidadCuotas : null;
        $monto = ($monto !== null && $monto !== '') ? $monto : null;
        $montoSinInteres = ($montoSinInteres !== null && $montoSinInteres !== '') ? $montoSinInteres : null;
        $tasaInteres = ($tasaInteres !== null && $tasaInteres !== '') ? $tasaInteres : null;
        $fechaInicio = ($fechaInicio !== null && $fechaInicio !== '') ? $fechaInicio : null;
        $fechaFin = ($fechaFin !== null && $fechaFin !== '') ? $fechaFin : null;
        $frecuenciaPago = ($frecuenciaPago !== null && $frecuenciaPago !== '') ? $frecuenciaPago : null;
        $montoComision = ($montoComision !== null && $montoComision !== '') ? $montoComision : null;

        $stmt->bind_param('ssssssssssssisiissi',  // ✅ Agregado: aplica_comision (i), monto_comision (s), moneda_comision (s)
            $nombrePlan,
            $cuotaInicial,
            $montoCuota,
            $cantidadCuotas,
            $frecuenciaPago,
            $moneda,
            $monto,
            $montoSinInteres,
            $tasaInteres,
            $fechaInicio,
            $fechaFin,
            $tipoVehicular,
            $cobrarMora,  // i - integer
            $estado,  // s - string
            $esYango,  // i - integer
            $aplicaComision,  // i - integer (✅ NUEVO)
            $montoComision,  // s - string/decimal (✅ NUEVO)
            $monedaComision,  // s - string (✅ NUEVO)
            $id  // i - integer
        );

        if (!$stmt->execute()) {
            throw new Exception('Error en la ejecución: ' . $stmt->error);
        }

        $stmt->close();
        return true;
    }

    // Modificación para variantes: Método para obtener variantes de un grupo
    public function getVariantesGrupo($idPlan)
    {
        $sql = 'SELECT * FROM grupos_variantes WHERE idplan_financiamiento = ?';
        $stmt = $this->conectar->prepare($sql);

        if (!$stmt) {
            throw new Exception('Error en la preparación de la consulta: ' . $this->conectar->error);
        }

        $stmt->bind_param('i', $idPlan);

        if (!$stmt->execute()) {
            throw new Exception('Error en la ejecución: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $variantes = [];

        while ($row = $result->fetch_assoc()) {
            $variantes[] = $row;
        }

        $stmt->close();

        return $variantes;
    }

    // Modificación para variantes: Método para actualizar una variante
    public function actualizarVariante($id, $idPlanFinanciamiento, $nombreVariante, $cuotaInicial,
        $montoInscripcion, $montoCuota, $cantidadCuotas, $frecuenciaPago, $moneda,
        $monto, $montoSinInteres, $tasaInteres, $fechaInicio, $fechaFin)
    {
        $sql = 'UPDATE grupos_variantes SET 
                idplan_financiamiento = ?,
                nombre_variante = ?,
                cuota_inicial = ?,
                monto_inscripcion = ?,
                monto_cuota = ?,
                cantidad_cuotas = ?,
                frecuencia_pago = ?,
                moneda = ?,
                monto = ?,
                monto_sin_interes = ?,
                tasa_interes = ?,
                fecha_inicio = ?,
                fecha_fin = ?
            WHERE idgrupos_variantes = ?';

        $stmt = $this->conectar->prepare($sql);

        if (!$stmt) {
            throw new Exception('Error en la preparación de la consulta: ' . $this->conectar->error);
        }

        // Convertir valores vacíos a NULL
        $cuotaInicial = ($cuotaInicial !== null && $cuotaInicial !== '') ? $cuotaInicial : null;
        $montoInscripcion = ($montoInscripcion !== null && $montoInscripcion !== '') ? $montoInscripcion : null;
        $montoCuota = ($montoCuota !== null && $montoCuota !== '') ? $montoCuota : null;
        $cantidadCuotas = ($cantidadCuotas !== null && $cantidadCuotas !== '') ? $cantidadCuotas : null;
        $frecuenciaPago = ($frecuenciaPago !== null && $frecuenciaPago !== '') ? $frecuenciaPago : null;
        $monto = ($monto !== null && $monto !== '') ? $monto : null;
        $montoSinInteres = ($montoSinInteres !== null && $montoSinInteres !== '') ? $montoSinInteres : null;
        $tasaInteres = ($tasaInteres !== null && $tasaInteres !== '') ? $tasaInteres : null;
        $fechaInicio = ($fechaInicio !== null && $fechaInicio !== '') ? $fechaInicio : null;
        $fechaFin = ($fechaFin !== null && $fechaFin !== '') ? $fechaFin : null;

        $stmt->bind_param('isdddissddsssi',
            $idPlanFinanciamiento,
            $nombreVariante,
            $cuotaInicial,
            $montoInscripcion,
            $montoCuota,
            $cantidadCuotas,
            $frecuenciaPago,
            $moneda,
            $monto,
            $montoSinInteres,
            $tasaInteres,
            $fechaInicio,
            $fechaFin,
            $id);

        if (!$stmt->execute()) {
            throw new Exception('Error en la ejecución: ' . $stmt->error);
        }

        $stmt->close();

        return true;
    }

    public function deleteGroup($id)
    {
        $sql = 'DELETE FROM planes_financiamiento WHERE idplan_financiamiento = ?';
        $stmt = $this->conectar->prepare($sql);
        if (!$stmt) {  // 🆕 Verificar si la preparación falló
            die('Error en la consulta: ' . $this->conectar->error);
        }
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    // 😊 Nuevo método para obtener datos del grupo de financiamiento
    public function obtenerDatosGrupoFinanciamiento($financiamiento)
    {
        $resultado = [
            'nombre' => '',
            'duracion' => '',
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'frecuencia' => '',
            'monto_sin_interes' => '',
            'moneda' => ''
        ];

        // 🔍 DEBUG: Ver qué datos tiene el financiamiento
        error_log('🔍 [MODELO] Financiamiento recibido - grupo_financiamiento: ' . ($financiamiento['grupo_financiamiento'] ?? 'NULL'));
        error_log('🔍 [MODELO] Financiamiento recibido - id_variante: ' . ($financiamiento['id_variante'] ?? 'NULL'));
        error_log('🔍 [MODELO] Financiamiento recibido - nombre_personalizado: ' . ($financiamiento['nombre_personalizado'] ?? 'NULL'));

        // Primero intentamos con id_variante
        if (!empty($financiamiento['id_variante'])) {
            error_log('🔍 [MODELO] Entrando por rama de ID_VARIANTE');
            $sql = 'SELECT gv.nombre_variante, gv.fecha_inicio, gv.fecha_fin, gv.frecuencia_pago, 
                           gv.monto_sin_interes, gv.moneda, gv.idplan_financiamiento,
                           pf.nombre_plan
                   FROM grupos_variantes gv
                   INNER JOIN planes_financiamiento pf ON gv.idplan_financiamiento = pf.idplan_financiamiento
                   WHERE gv.idgrupos_variantes = ?';

            $stmt = $this->conectar->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('i', $financiamiento['id_variante']);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();

                if ($result) {
                    error_log('🔍 [MODELO] Variante encontrada - nombre_variante: ' . ($result['nombre_variante'] ?? 'NULL'));
                    error_log('🔍 [MODELO] Variante encontrada - nombre_plan: ' . ($result['nombre_plan'] ?? 'NULL'));

                    // ✅ CORREGIDO: Usar nombre_plan del plan base, no nombre_variante
                    $resultado['nombre'] = $result['nombre_plan'];
                    $resultado['fecha_inicio'] = $result['fecha_inicio'];
                    $resultado['fecha_fin'] = $result['fecha_fin'];
                    $resultado['frecuencia'] = $result['frecuencia_pago'];
                    $resultado['monto_sin_interes'] = $result['monto_sin_interes'];
                    $resultado['moneda'] = $result['moneda'];
                    $resultado['duracion'] = $this->calcularDuracion($result['fecha_inicio'], $result['fecha_fin']);
                } else {
                    error_log('🔍 [MODELO] No se encontró variante con id: ' . $financiamiento['id_variante']);
                }
            }
        }
        // Si no hay id_variante o no se encontró, buscamos por grupo_financiamiento
        elseif (!empty($financiamiento['grupo_financiamiento'])) {
            error_log('🔍 [MODELO] Entrando por rama de GRUPO_FINANCIAMIENTO');
            $grupo = $this->getGroupById($financiamiento['grupo_financiamiento']);

            // 🔍 DEBUG: Ver qué devuelve getGroupById
            error_log('🔍 [MODELO] getGroupById para plan ' . $financiamiento['grupo_financiamiento'] . ': ' . print_r($grupo, true));

            if ($grupo) {
                $resultado['nombre'] = $grupo['nombre_plan'];
                $resultado['fecha_inicio'] = $grupo['fecha_inicio'];
                $resultado['fecha_fin'] = $grupo['fecha_fin'];
                $resultado['frecuencia'] = $grupo['frecuencia_pago'];
                $resultado['monto_sin_interes'] = $grupo['monto_sin_interes'];
                $resultado['moneda'] = $grupo['moneda'];
                $resultado['duracion'] = $this->calcularDuracion($grupo['fecha_inicio'], $grupo['fecha_fin']);

                // 🔍 DEBUG: Ver qué se asigna a resultado['nombre']
                error_log("🔍 [MODELO] nombre_plan del grupo: '" . ($grupo['nombre_plan'] ?? 'NULL') . "'");
                error_log("🔍 [MODELO] resultado['nombre'] asignado: '" . $resultado['nombre'] . "'");
            }
        }

        return $resultado;
    }

    // 😊 Nuevo método para calcular duración
    private function calcularDuracion($fechaInicio, $fechaFin)
    {
        if (!$fechaInicio || !$fechaFin) {
            return '';
        }

        $inicio = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);
        $diff = $inicio->diff($fin);

        // Si es más de 30 días, mostrar en meses
        if ($diff->days > 30) {
            $meses = floor($diff->days / 30);
            return $meses . ' meses';
        } else {
            return $diff->days . ' días';
        }
    }

    public function getGroupById($id)
    {
        $sql = 'SELECT                     
             idplan_financiamiento,
             nombre_plan,
             cuota_inicial,
             monto_cuota,
             cantidad_cuotas,
             penalizacion_mora,
             frecuencia_pago,
             moneda,
             tasa_interes,
             monto,
             monto_sin_interes,
             fecha_inicio,
             fecha_fin,
             estado,
             es_yango
                FROM planes_financiamiento 
                WHERE idplan_financiamiento = ?';

        $stmt = $this->conectar->prepare($sql);

        if (!$stmt) {
            die('Error al preparar la consulta plan financiamiento: ' . $this->conectar->error);
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $plan = $result->fetch_assoc();

        if (!$plan) {
            return [
                'nombre_plan' => '',
                'cuota_inicial' => 0,
                'monto_cuota' => 0,
                'cantidad_cuotas' => 0,
                'penalizacion_mora' => 0,
                'frecuencia_pago' => '',
                'moneda' => '',
                'tasa_interes' => 0,
                'monto' => 0,
                'monto_sin_interes' => 0,
                'fecha_inicio' => null,
                'fecha_fin' => null,
                'estado' => 'activo',
                'es_yango' => 0
            ];
        }

        return $plan;
    }

    // Agregar al final del archivo del modelo, antes del cierre de la clase
    public function getTipoVehicular($idPlan)
    {
        $sql = 'SELECT tipo_vehicular FROM planes_financiamiento WHERE idplan_financiamiento = ?';
        $stmt = $this->conectar->prepare($sql);

        if (!$stmt) {
            throw new Exception('Error en la preparación de la consulta: ' . $this->conectar->error);
        }

        $stmt->bind_param('i', $idPlan);

        if (!$stmt->execute()) {
            throw new Exception('Error en la ejecución: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $tipoVehicular = null;

        if ($row = $result->fetch_assoc()) {
            $tipoVehicular = $row['tipo_vehicular'];
        }

        $stmt->close();

        return $tipoVehicular;
    }

    public function obtenerEstadoPlan($idPlan)
    {
        $sql = 'SELECT estado FROM planes_financiamiento WHERE idplan_financiamiento = ?';
        $stmt = $this->conectar->prepare($sql);

        if (!$stmt) {
            throw new Exception('Error en la preparación de la consulta');
        }

        $stmt->bind_param('i', $idPlan);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['estado'];
        }

        throw new Exception('Plan no encontrado');
    }
}
