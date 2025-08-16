<?php

class Cupon
{
    private $id;
    private $titulo;
    private $descripcion;
    private $tipo_descuento;
    private $valor;
    private $imagen_banner;
    private $fecha_inicio;
    private $fecha_fin;
    private $limite_usos_conductor;
    private $limite_usos_total;
    private $activo;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * Crear un nuevo cupón
     */
    public function crear($datos)
    {
        try {
            $sql = "INSERT INTO cupones (titulo, descripcion, tipo_descuento, valor, imagen_banner, fecha_inicio, fecha_fin, limite_usos_conductor, limite_usos_total, activo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param(
                'sssdsssiis',
                $datos['titulo'],
                $datos['descripcion'],
                $datos['tipo_descuento'],
                $datos['valor'],
                $datos['imagen_banner'],
                $datos['fecha_inicio'],
                $datos['fecha_fin'],
                $datos['limite_usos_conductor'],
                $datos['limite_usos_total'],
                $datos['activo']
            );

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $insertId = $stmt->insert_id;
            $stmt->close();

            return $insertId;
        } catch (Exception $e) {
            error_log('Error en Cupon::crear(): ' . $e->getMessage());
            return false;
        }
    }

    public function asignarAUsuarios($idCupon, $usuarios)
    {
        try {
            $sql = "INSERT INTO cupones_asignados (id_cupon, tipo_usuario, id_usuario) VALUES (?, ?, ?)";
            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $this->conectar->autocommit(false);

            foreach ($usuarios as $usuario) {
                $stmt->bind_param('isi', $idCupon, $usuario['tipo'], $usuario['id']);
                if (!$stmt->execute()) {
                    $this->conectar->rollback();
                    $stmt->close();
                    throw new Exception('Error al asignar cupón: ' . $stmt->error);
                }
            }

            $this->conectar->commit();
            $this->conectar->autocommit(true);
            $stmt->close();

            return true;
        } catch (Exception $e) {
            $this->conectar->rollback();
            $this->conectar->autocommit(true);
            error_log('Error en Cupon::asignarAUsuarios(): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * CORREGIDO: Obtener todos los cupones con información de usuarios asignados
     */
    public function obtenerTodos()
    {
        try {
            $sql = "SELECT c.*, 
                           COUNT(DISTINCT ca.id) as usuarios_asignados,
                           COUNT(DISTINCT cut.id) as total_usos,
                           COUNT(CASE WHEN ca.tipo_usuario = 'conductor' THEN 1 END) as conductores_asignados,
                           COUNT(CASE WHEN ca.tipo_usuario = 'cliente' THEN 1 END) as clientes_asignados
                    FROM cupones c 
                    LEFT JOIN cupones_asignados ca ON c.id = ca.id_cupon AND ca.activo = 1
                    LEFT JOIN cupones_uso_tracking cut ON c.id = cut.id_cupon
                    GROUP BY c.id
                    ORDER BY c.created_at DESC";

            $result = $this->conectar->query($sql);

            if (!$result) {
                throw new Exception('Error al obtener cupones: ' . $this->conectar->error);
            }

            $cupones = [];
            while ($row = $result->fetch_assoc()) {
                // Corregir para mostrar el total de usuarios correctamente
                $row['conductores_asignados'] = $row['usuarios_asignados']; // Para mantener compatibilidad
                $cupones[] = $row;
            }

            return $cupones;
        } catch (Exception $e) {
            error_log('Error en Cupon::obtenerTodos(): ' . $e->getMessage());
            return [];
        }
    }

    public function obtenerUsuariosPorCupon($idCupon)
    {
        try {
            $sql = "SELECT 
                        ca.tipo_usuario,
                        ca.id_usuario,
                        ca.fecha_asignacion,
                        ca.activo as asignacion_activa,
                        COUNT(cut.id) as veces_usado,
                        MAX(cut.fecha_uso) as ultimo_uso,
                        SUM(cut.monto_descuento) as total_descontado,
                        -- Datos del conductor
                        c.foto as conductor_foto,
                        c.nro_documento as conductor_documento,
                        c.nombres as conductor_nombres,
                        c.apellido_paterno as conductor_apellido_paterno,
                        c.apellido_materno as conductor_apellido_materno,
                        v.placa,
                        -- Datos del cliente
                        cl.n_documento as cliente_documento,
                        cl.nombres as cliente_nombres,
                        cl.apellido_paterno as cliente_apellido_paterno,
                        cl.apellido_materno as cliente_apellido_materno,
                        cl.telefono as cliente_telefono
                    FROM cupones_asignados ca
                    LEFT JOIN conductores c ON ca.tipo_usuario = 'conductor' AND ca.id_usuario = c.id_conductor
                    LEFT JOIN vehiculos v ON c.id_conductor = v.id_conductor
                    LEFT JOIN clientes_financiar cl ON ca.tipo_usuario = 'cliente' AND ca.id_usuario = cl.id
                    LEFT JOIN cupones_uso_tracking cut ON ca.id_cupon = cut.id_cupon 
                        AND ((ca.tipo_usuario = 'conductor' AND cut.id_conductor = ca.id_usuario)
                             OR (ca.tipo_usuario = 'cliente' AND cut.id_cliente = ca.id_usuario))
                    WHERE ca.id_cupon = ? AND ca.activo = 1
                    GROUP BY ca.tipo_usuario, ca.id_usuario, ca.fecha_asignacion, ca.activo
                    ORDER BY ca.fecha_asignacion ASC";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('i', $idCupon);
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $usuarios = [];

            while ($row = $result->fetch_assoc()) {
                // NUEVO FORMATO: Siempre retornar id_cliente e id_conductor
                $usuarioFormateado = [
                    'tipo_usuario' => $row['tipo_usuario'], // Mantener para compatibilidad
                    'fecha_asignacion' => $row['fecha_asignacion'],
                    'asignacion_activa' => $row['asignacion_activa'],
                    'veces_usado' => (int) $row['veces_usado'],
                    'ultimo_uso' => $row['ultimo_uso'],
                    'total_descontado' => $row['total_descontado'] ? (float) $row['total_descontado'] : 0,
                    'ha_usado_cupon' => $row['veces_usado'] > 0,
                    'placa' => null,
                    'telefono' => null
                ];

                if ($row['tipo_usuario'] === 'conductor') {
                    // $usuarioFormateado['tipo_cliente'] = 'conductor';
                    $usuarioFormateado['id_cliente'] = null;
                    $usuarioFormateado['id_conductor'] = (int) $row['id_usuario'];
                    $usuarioFormateado['foto'] = $row['conductor_foto'] ? '/arequipago/public/' . $row['conductor_foto'] : '/arequipago/public/img/default-user.png';
                    $usuarioFormateado['nro_documento'] = $row['conductor_documento'];
                    $usuarioFormateado['nombres'] = $row['conductor_nombres'];
                    $usuarioFormateado['apellido_paterno'] = $row['conductor_apellido_paterno'];
                    $usuarioFormateado['apellido_materno'] = $row['conductor_apellido_materno'];
                    $usuarioFormateado['placa'] = $row['placa'];
                } else {
                    // $usuarioFormateado['tipo_cliente'] = 'cliente';
                    $usuarioFormateado['id_cliente'] = (int) $row['id_usuario'];
                    $usuarioFormateado['id_conductor'] = null;
                    $usuarioFormateado['foto'] = '/arequipago/public/img/default-user.png';
                    $usuarioFormateado['nro_documento'] = $row['cliente_documento'];
                    $usuarioFormateado['nombres'] = $row['cliente_nombres'];
                    $usuarioFormateado['apellido_paterno'] = $row['cliente_apellido_paterno'];
                    $usuarioFormateado['apellido_materno'] = $row['cliente_apellido_materno'];
                    $usuarioFormateado['telefono'] = $row['cliente_telefono'];
                }

                $usuarios[] = $usuarioFormateado;

            }

            $stmt->close();
            return $usuarios;
        } catch (Exception $e) {
            error_log('Error en Cupon::obtenerUsuariosPorCupon(): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * NUEVO: Verificar si usuarios tienen cupones activos (tanto conductores como clientes)
     */
    public function verificarUsuariosConCuponesActivos($usuarios)
    {
        try {
            if (empty($usuarios)) {
                return [];
            }

            $usuariosConCupones = [];

            // Verificar conductores
            $conductores = array_filter($usuarios, function ($u) {
                return $u['tipo'] === 'conductor'; });
            if (!empty($conductores)) {
                $idsConductores = array_column($conductores, 'id');
                $placeholders = implode(',', array_fill(0, count($idsConductores), '?'));

                $sql = "SELECT DISTINCT ca.id_usuario
                        FROM cupones_asignados ca
                        INNER JOIN cupones c ON ca.id_cupon = c.id
                        WHERE ca.id_usuario IN ($placeholders)
                        AND ca.tipo_usuario = 'conductor'
                        AND ca.activo = 1
                        AND c.activo = 1
                        AND c.fecha_fin >= CURDATE()";

                $stmt = $this->conectar->prepare($sql);
                if ($stmt) {
                    $types = str_repeat('i', count($idsConductores));
                    $stmt->bind_param($types, ...$idsConductores);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        $usuariosConCupones[] = ['tipo' => 'conductor', 'id' => $row['id_usuario']];
                    }
                    $stmt->close();
                }
            }

            // Verificar clientes
            $clientes = array_filter($usuarios, function ($u) {
                return $u['tipo'] === 'cliente'; });
            if (!empty($clientes)) {
                $idsClientes = array_column($clientes, 'id');
                $placeholders = implode(',', array_fill(0, count($idsClientes), '?'));

                $sql = "SELECT DISTINCT ca.id_usuario
                        FROM cupones_asignados ca
                        INNER JOIN cupones c ON ca.id_cupon = c.id
                        WHERE ca.id_usuario IN ($placeholders)
                        AND ca.tipo_usuario = 'cliente'
                        AND ca.activo = 1
                        AND c.activo = 1
                        AND c.fecha_fin >= CURDATE()";

                $stmt = $this->conectar->prepare($sql);
                if ($stmt) {
                    $types = str_repeat('i', count($idsClientes));
                    $stmt->bind_param($types, ...$idsClientes);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        $usuariosConCupones[] = ['tipo' => 'cliente', 'id' => $row['id_usuario']];
                    }
                    $stmt->close();
                }
            }

            return $usuariosConCupones;
        } catch (Exception $e) {
            error_log('Error en Cupon::verificarUsuariosConCuponesActivos(): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * CORREGIDO: Verificar si conductores tienen cupones activos
     */
    public function verificarConductoresConCuponesActivos($idConductores)
    {
        try {
            if (empty($idConductores)) {
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($idConductores), '?'));

            $sql = "SELECT DISTINCT ca.id_usuario
                    FROM cupones_asignados ca
                    INNER JOIN cupones c ON ca.id_cupon = c.id
                    WHERE ca.id_usuario IN ($placeholders)
                    AND ca.tipo_usuario = 'conductor'
                    AND ca.activo = 1
                    AND c.activo = 1
                    AND c.fecha_fin >= CURDATE()";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $types = str_repeat('i', count($idConductores));
            $stmt->bind_param($types, ...$idConductores);

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $conductoresConCupones = [];

            while ($row = $result->fetch_assoc()) {
                $conductoresConCupones[] = $row['id_usuario'];
            }

            $stmt->close();
            return $conductoresConCupones;
        } catch (Exception $e) {
            error_log('Error en Cupon::verificarConductoresConCuponesActivos(): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * CORREGIDO: Verificar si un conductor específico tiene cupones activos
     */
    public function verificarConductorTieneCupon($idConductor)
    {
        try {
            $sql = "SELECT c.id, c.titulo, c.tipo_descuento, c.valor, c.fecha_fin, c.limite_usos_conductor, c.imagen_banner,
                           ca.fecha_asignacion,
                           COUNT(cut.id) as usos_realizados
                    FROM cupones c
                    INNER JOIN cupones_asignados ca ON c.id = ca.id_cupon
                    LEFT JOIN cupones_uso_tracking cut ON c.id = cut.id_cupon AND cut.id_conductor = ?
                    WHERE ca.id_usuario = ?
                    AND ca.tipo_usuario = 'conductor'
                    AND ca.activo = 1
                    AND c.activo = 1
                    AND c.fecha_fin >= CURDATE()
                    GROUP BY c.id, ca.fecha_asignacion
                    ORDER BY c.fecha_fin ASC";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('ii', $idConductor, $idConductor);
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $cupones = [];

            while ($row = $result->fetch_assoc()) {
                // Verificar si el cupón aún puede ser usado
                $puedeUsar = true;
                if (!empty($row['limite_usos_conductor']) && $row['usos_realizados'] >= $row['limite_usos_conductor']) {
                    $puedeUsar = false;
                }

                $row['puede_usar'] = $puedeUsar;
                $row['estado'] = $puedeUsar ? 'activo' : 'agotado';
                $cupones[] = $row;
            }

            $stmt->close();
            return $cupones;
        } catch (Exception $e) {
            error_log('Error en Cupon::verificarConductorTieneCupon(): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * NUEVO: Verificar si un cliente específico tiene cupones activos
     */
    public function verificarClienteTieneCupon($idCliente)
    {
        try {
            $sql = "SELECT c.id, c.titulo, c.tipo_descuento, c.valor, c.fecha_fin, c.limite_usos_conductor, c.imagen_banner,
                           ca.fecha_asignacion,
                           COUNT(cut.id) as usos_realizados
                    FROM cupones c
                    INNER JOIN cupones_asignados ca ON c.id = ca.id_cupon
                    LEFT JOIN cupones_uso_tracking cut ON c.id = cut.id_cupon AND cut.id_cliente = ?
                    WHERE ca.id_usuario = ?
                    AND ca.tipo_usuario = 'cliente'
                    AND ca.activo = 1
                    AND c.activo = 1
                    AND c.fecha_fin >= CURDATE()
                    GROUP BY c.id, ca.fecha_asignacion
                    ORDER BY c.fecha_fin ASC";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('ii', $idCliente, $idCliente);
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $cupones = [];

            while ($row = $result->fetch_assoc()) {
                // Verificar si el cupón aún puede ser usado
                $puedeUsar = true;
                if (!empty($row['limite_usos_conductor']) && $row['usos_realizados'] >= $row['limite_usos_conductor']) {
                    $puedeUsar = false;
                }

                $row['puede_usar'] = $puedeUsar;
                $row['estado'] = $puedeUsar ? 'activo' : 'agotado';
                $cupones[] = $row;
            }

            $stmt->close();
            return $cupones;
        } catch (Exception $e) {
            error_log('Error en Cupon::verificarClienteTieneCupon(): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Registrar uso de cupón
     */
    public function registrarUso($idCupon, $idConductor, $montoDescuento, $idCliente = null)
    {
        try {
            if ($idCliente) {
                $sql = "INSERT INTO cupones_uso_tracking (id_cupon, id_cliente, monto_descuento, tipo_usuario) 
                        VALUES (?, ?, ?, 'cliente')";
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param('iid', $idCupon, $idCliente, $montoDescuento);
            } else {
                $sql = "INSERT INTO cupones_uso_tracking (id_cupon, id_conductor, monto_descuento, tipo_usuario) 
                        VALUES (?, ?, ?, 'conductor')";
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param('iid', $idCupon, $idConductor, $montoDescuento);
            }

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $insertId = $stmt->insert_id;
            $stmt->close();

            return $insertId;
        } catch (Exception $e) {
            error_log('Error en Cupon::registrarUso(): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * CORREGIDO: Obtener estadísticas de uso de cupones
     */
    public function obtenerEstadisticasUso($idCupon = null)
    {
        try {
            $whereClause = $idCupon ? "WHERE c.id = ?" : "";

            $sql = "SELECT c.id, c.titulo,
                           COUNT(DISTINCT ca.id) as usuarios_asignados,
                           COUNT(cut.id) as total_usos,
                           SUM(cut.monto_descuento) as monto_total_descontado,
                           AVG(cut.monto_descuento) as promedio_descuento
                    FROM cupones c
                    LEFT JOIN cupones_asignados ca ON c.id = ca.id_cupon AND ca.activo = 1
                    LEFT JOIN cupones_uso_tracking cut ON c.id = cut.id_cupon
                    $whereClause
                    GROUP BY c.id, c.titulo
                    ORDER BY c.created_at DESC";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            if ($idCupon) {
                $stmt->bind_param('i', $idCupon);
            }

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $estadisticas = [];

            while ($row = $result->fetch_assoc()) {
                $estadisticas[] = $row;
            }

            $stmt->close();
            return $idCupon ? ($estadisticas[0] ?? null) : $estadisticas;
        } catch (Exception $e) {
            error_log('Error en Cupon::obtenerEstadisticasUso(): ' . $e->getMessage());
            return $idCupon ? null : [];
        }
    }

    /**
     * Verificar uso específico de un conductor en un cupón
     */
    public function verificarUsoCuponEspecifico($idConductor, $idCupon)
    {
        try {
            $sql = "SELECT 
                        COUNT(cut.id) as veces_usado,
                        MAX(cut.fecha_uso) as ultimo_uso,
                        SUM(cut.monto_descuento) as total_descontado,
                        c.limite_usos_conductor
                    FROM cupones c
                    LEFT JOIN cupones_uso_tracking cut ON c.id = cut.id_cupon AND cut.id_conductor = ?
                    WHERE c.id = ?
                    GROUP BY c.id, c.limite_usos_conductor";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('ii', $idConductor, $idCupon);
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $vecesUsado = (int) ($row['veces_usado'] ?? 0);
            $limiteUsos = $row['limite_usos_conductor'] ? (int) $row['limite_usos_conductor'] : null;
            $totalDescontado = $row['total_descontado'] ? (float) $row['total_descontado'] : 0;

            // Obtener historial detallado
            $historialUsos = $this->obtenerHistorialUsosCupon($idConductor, $idCupon);

            $stmt->close();

            return [
                'ha_usado' => $vecesUsado > 0,
                'veces_usado' => $vecesUsado,
                'ultimo_uso' => $row['ultimo_uso'],
                'total_descontado' => $totalDescontado,
                'limite_usos_conductor' => $limiteUsos,
                'puede_usar_mas' => $limiteUsos ? ($vecesUsado < $limiteUsos) : true,
                'limite_alcanzado' => $limiteUsos ? ($vecesUsado >= $limiteUsos) : false,
                'historial_usos' => $historialUsos
            ];
        } catch (Exception $e) {
            error_log('Error en Cupon::verificarUsoCuponEspecifico(): ' . $e->getMessage());
            return [
                'ha_usado' => false,
                'veces_usado' => 0,
                'ultimo_uso' => null,
                'total_descontado' => 0,
                'puede_usar_mas' => true,
                'limite_alcanzado' => false,
                'historial_usos' => []
            ];
        }
    }

    /**
     * Verificar uso específico de un cliente en un cupón
     */
    public function verificarUsoCuponEspecificoCliente($idCliente, $idCupon)
    {
        try {
            $sql = "SELECT 
                        COUNT(cut.id) as veces_usado,
                        MAX(cut.fecha_uso) as ultimo_uso,
                        SUM(cut.monto_descuento) as total_descontado,
                        c.limite_usos_conductor as limite_usos_usuario -- Se asume que el límite es por usuario
                    FROM cupones c
                    LEFT JOIN cupones_uso_tracking cut ON c.id = cut.id_cupon AND cut.id_cliente = ?
                    WHERE c.id = ?
                    GROUP BY c.id, c.limite_usos_conductor";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('ii', $idCliente, $idCupon);
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $vecesUsado = (int) ($row['veces_usado'] ?? 0);
            $limiteUsos = $row['limite_usos_usuario'] ? (int) $row['limite_usos_usuario'] : null;
            $totalDescontado = $row['total_descontado'] ? (float) $row['total_descontado'] : 0;

            // Obtener historial detallado
            $historialUsos = $this->obtenerHistorialUsosCuponCliente($idCliente, $idCupon);

            $stmt->close();

            return [
                'ha_usado' => $vecesUsado > 0,
                'veces_usado' => $vecesUsado,
                'ultimo_uso' => $row['ultimo_uso'],
                'total_descontado' => $totalDescontado,
                'limite_usos_usuario' => $limiteUsos,
                'puede_usar_mas' => $limiteUsos ? ($vecesUsado < $limiteUsos) : true,
                'limite_alcanzado' => $limiteUsos ? ($vecesUsado >= $limiteUsos) : false,
                'historial_usos' => $historialUsos
            ];
        } catch (Exception $e) {
            error_log('Error en Cupon::verificarUsoCuponEspecificoCliente(): ' . $e->getMessage());
            return [
                'ha_usado' => false,
                'veces_usado' => 0,
                'ultimo_uso' => null,
                'total_descontado' => 0,
                'puede_usar_mas' => true,
                'limite_alcanzado' => false,
                'historial_usos' => []
            ];
        }
    }

    /**
     * Obtener historial detallado de usos
     */
    public function obtenerHistorialUsosCupon($idConductor, $idCupon)
    {
        try {
            $sql = "SELECT fecha_uso, monto_descuento
                    FROM cupones_uso_tracking 
                    WHERE id_conductor = ? AND id_cupon = ?
                    ORDER BY fecha_uso DESC";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('ii', $idConductor, $idCupon);
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $historial = [];

            while ($row = $result->fetch_assoc()) {
                $historial[] = $row;
            }

            $stmt->close();
            return $historial;
        } catch (Exception $e) {
            error_log('Error en Cupon::obtenerHistorialUsosCupon(): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener historial detallado de usos de un cliente
     */
    public function obtenerHistorialUsosCuponCliente($idCliente, $idCupon)
    {
        try {
            $sql = "SELECT fecha_uso, monto_descuento
                    FROM cupones_uso_tracking 
                    WHERE id_cliente = ? AND id_cupon = ?
                    ORDER BY fecha_uso DESC";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('ii', $idCliente, $idCupon);
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $historial = [];

            while ($row = $result->fetch_assoc()) {
                $historial[] = $row;
            }

            $stmt->close();
            return $historial;
        } catch (Exception $e) {
            error_log('Error en Cupon::obtenerHistorialUsosCuponCliente(): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener información de un cupón por ID
     */
    public function obtenerCuponPorId($idCupon)
    {
        try {
            $sql = "SELECT id, titulo, descripcion, tipo_descuento, valor, imagen_banner, fecha_inicio, fecha_fin, limite_usos_conductor, limite_usos_total, activo
                    FROM cupones WHERE id = ?";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('i', $idCupon);
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $cupon = $result->fetch_assoc();

            $stmt->close();
            return $cupon;
        } catch (Exception $e) {
            error_log('Error en Cupon::obtenerCuponPorId(): ' . $e->getMessage());
            return null;
        }
    }

    public function buscarClientes($term)
    {
        try {
            $term = "%{$term}%";
            $sql = "SELECT id, n_documento, nombres, apellido_paterno, apellido_materno, telefono
                    FROM clientes_financiar
                    WHERE nombres LIKE ? 
                    OR apellido_paterno LIKE ? 
                    OR n_documento LIKE ?
                    ORDER BY nombres ASC
                    LIMIT 20";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error preparing statement: " . $this->conectar->error);
            }

            $stmt->bind_param("sss", $term, $term, $term);

            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $clientes = [];

            while ($row = $result->fetch_assoc()) {
                $row['foto'] = '/arequipago/public/img/default-user.png'; // Imagen por defecto
                $clientes[] = $row;
            }

            $stmt->close();
            return $clientes;
        } catch (Exception $e) {
            error_log("Error in Cupon::buscarClientes(): " . $e->getMessage());
            return [];
        }
    }

    // Getters y Setters
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function getTipoDescuento()
    {
        return $this->tipo_descuento;
    }
    public function setTipoDescuento($tipo_descuento)
    {
        $this->tipo_descuento = $tipo_descuento;
    }

    public function getValor()
    {
        return $this->valor;
    }
    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    public function getImagenBanner()
    {
        return $this->imagen_banner;
    }
    public function setImagenBanner($imagen_banner)
    {
        $this->imagen_banner = $imagen_banner;
    }

    public function getFechaInicio()
    {
        return $this->fecha_inicio;
    }
    public function setFechaInicio($fecha_inicio)
    {
        $this->fecha_inicio = $fecha_inicio;
    }

    public function getFechaFin()
    {
        return $this->fecha_fin;
    }
    public function setFechaFin($fecha_fin)
    {
        $this->fecha_fin = $fecha_fin;
    }

    public function getLimiteUsosConductor()
    {
        return $this->limite_usos_conductor;
    }
    public function setLimiteUsosConductor($limite_usos_conductor)
    {
        $this->limite_usos_conductor = $limite_usos_conductor;
    }

    public function getLimiteUsosTotal()
    {
        return $this->limite_usos_total;
    }
    public function setLimiteUsosTotal($limite_usos_total)
    {
        $this->limite_usos_total = $limite_usos_total;
    }

    public function getActivo()
    {
        return $this->activo;
    }
    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    /**
     * Generar código único para cupón
     */
    public function generarCodigoUnico($titulo)
    {
        try {
            // Limpiar título y tomar primeras palabras
            $palabras = explode(' ', strtoupper(trim($titulo)));
            $base = '';

            // Tomar hasta 2 palabras y máximo 8 caracteres
            foreach ($palabras as $palabra) {
                $palabra = preg_replace('/[^A-Z0-9]/', '', $palabra);
                if (strlen($base . $palabra) <= 8) {
                    $base .= $palabra;
                } else {
                    $base .= substr($palabra, 0, 8 - strlen($base));
                    break;
                }
            }

            // Si queda muy corto, agregar caracteres
            if (strlen($base) < 4) {
                $base = strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $titulo), 0, 6));
            }

            // Agregar número aleatorio
            $codigo = $base . rand(10, 99);

            // Verificar que no exista
            $contador = 1;
            $codigoOriginal = $codigo;
            while ($this->existeCodigo($codigo)) {
                $codigo = $codigoOriginal . $contador;
                $contador++;
            }

            return $codigo;
        } catch (Exception $e) {
            error_log('Error en generarCodigoUnico: ' . $e->getMessage());
            return 'CUPON' . rand(1000, 9999);
        }
    }

    /**
     * Verificar si existe un código
     */
    public function existeCodigo($codigo)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM cupones WHERE codigo = ?";
            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                return false;
            }

            $stmt->bind_param('s', $codigo);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            return $row['total'] > 0;
        } catch (Exception $e) {
            error_log('Error en existeCodigo: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener cupón por código
     */
    public function obtenerPorCodigo($codigo)
    {
        try {
            $sql = "SELECT * FROM cupones WHERE codigo = ? AND activo = 1 AND fecha_fin >= CURDATE()";
            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('s', $codigo);
            $stmt->execute();
            $result = $stmt->get_result();
            $cupon = $result->fetch_assoc();
            $stmt->close();

            return $cupon;
        } catch (Exception $e) {
            error_log('Error en obtenerPorCodigo: ' . $e->getMessage());
            return null;
        }
    }
}