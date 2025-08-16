<?php

    class Financiamiento
    {
        private $id_cliente;
        private $codigo_asociado;
        private $grupo_financiamiento;
        private $estado;
        private $monto_total;
        private $cuotas;
        private $fecha_inicio;
        private $fecha_fin;
        private $conectar;

        public function __construct()
        {
            $this->conectar = (new Conexion())->getConexion();
        }

        public function guardarFinanciamiento($datos) {

           
            // Limpiar el valor de monto_total y monto_inscrip eliminando prefijos no numéricos
            $datos['monto_total'] = preg_replace('/[^\d.]/', '', $datos['monto_total']);
            
            // Corregir el posible punto inicial erróneo
            $datos['monto_total'] = ltrim($datos['monto_total'], '.');
            
            // Si monto_inscrip está vacío, lo convertimos a NULL
            $datos['monto_inscrip'] = $datos['monto_inscrip'] !== '' ? $datos['monto_inscrip'] : null;

            $datos['tasa'] = $datos['tasa'] !== '' ? $datos['tasa'] : null;
            
            // Si id_coti no está en los datos, asignamos NULL
            $idCoti = isset($datos['id_coti']) && $datos['id_coti'] !== '' ? $datos['id_coti'] : null;
            
            // Si second_product no está en los datos, asignamos NULL
            $secondProduct = isset($datos['second_product']) && $datos['second_product'] !== '' ? $datos['second_product'] : null;
            
            // Convertir monto_sin_interes a número y asegurar que no sea NULL
            $montoSinInteres = isset($datos['monto_sin_intereses']) && is_numeric($datos['monto_sin_intereses']) 
                ? (float)$datos['monto_sin_intereses'] 
                : null;
            
            $montoRecalculado = isset($datos['monto_recalculado']) && $datos['monto_recalculado'] !== '' 
                ? $datos['monto_recalculado'] 
                : null;
            
            // Obtener fecha de creación actual si no se proporciona
            $fechaCreacion = isset($datos['fecha_creacion']) ? $datos['fecha_creacion'] : date('Y-m-d H:i:s');
            
            $estado = isset($datos['estado']) && $datos['estado'] !== '' 
                ? $datos['estado'] 
                : 'En progreso';
            
            // NUEVO: Manejar id_cliente que puede ser NULL
            $idCliente = isset($datos['id_cliente']) && $datos['id_cliente'] !== '' ? $datos['id_cliente'] : null;
            
            // MODIFICADO: id_conductor puede ser NULL si hay id_cliente
            $idConductor = isset($datos['id_conductor']) && $datos['id_conductor'] !== '' ? $datos['id_conductor'] : null;
            
            // 💥 Modificado: Obtener el usuario_id si existe en los datos
            $usuario_id = isset($datos['usuario_id']) ? $datos['usuario_id'] : null;
            
            // 💥 Modificado: Obtener el valor de aprobado con valor por defecto 1
            $aprobado = isset($datos['aprobado']) ? $datos['aprobado'] : 1;
            

            
               // 💥 Modificado: Preparar la consulta SQL, ahora incluye usuario_id y aprobado
            $query = "INSERT INTO financiamiento (
                id_conductor, 
                id_cliente,
                idproductosv2, 
                id_coti,       
                codigo_asociado, 
                grupo_financiamiento, 
                cantidad_producto, 
                monto_total, 
                cuota_inicial, 
                cuotas, 
                estado, 
                fecha_inicio, 
                fecha_fin, 
                fecha_creacion, 
                frecuencia, 
                second_product,
                monto_inscrip,
                moneda,
                monto_sin_interes,
                monto_recalculado,
                tasa,
                usuario_id,           -- 💥 Modificado: Campo añadido
                aprobado              -- 💥 Modificado: Campo añadido
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 


            $stmt = $this->conectar->prepare($query);

            if (!$stmt) {
                die("Error al preparar la consulta: " . $this->conectar->error);
            }

            // 💥 Modificado: Actualizado bind_param para incluir usuario_id y aprobado
            $stmt->bind_param(
                'iiiiissddissssssdsdddii', 
                $idConductor,
                $idCliente,
                $datos['id_producto'],
                $idCoti,
                $datos['codigo_asociado'],
                $datos['grupo_financiamiento'],
                $datos['cantidad_producto'],
                $datos['monto_total'],
                $datos['cuota_inicial'],
                $datos['cuotas'],
                $estado,
                $datos['fecha_inicio'],
                $datos['fecha_fin'],
                $fechaCreacion,
                $datos['frecuencia'],
                $secondProduct,
                $datos['monto_inscrip'],
                $datos['tipo_moneda'],
                $montoSinInteres,
                $montoRecalculado,
                $datos['tasa'],
                $usuario_id,         
                $aprobado            
            );

            $resultado = $stmt->execute();

            if (!$resultado) {
                die("Error al ejecutar la consulta: " . $stmt->error);
            }

            return $this->conectar->insert_id; // Devuelve el ID del nuevo financiamiento
        }

            
    private function crearCuotas($id_financiamiento, $cantidad_cuotas, $valor_cuota, $fecha_inicio)
    {
        $fecha_vencimiento = $this->calcularFechaVencimiento($fecha_inicio);
        $estado = "Pendiente";  // Estado por defecto

        for ($i = 1; $i <= $cantidad_cuotas; $i++) {
            // Insertar cada cuota
            $sql = "INSERT INTO cuotas_financiamiento (id_financiamiento, numero_cuota, monto, fecha_vencimiento, estado) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("iiiss", $id_financiamiento, $i, $valor_cuota, $fecha_vencimiento, $estado);
            $stmt->execute();
            
            // Incrementar fecha de vencimiento para la siguiente cuota
            $fecha_vencimiento = $this->calcularFechaVencimiento($fecha_vencimiento);
        }
    }

    private function calcularFechaVencimiento($fecha_inicio)
    {
        $fecha = new DateTime($fecha_inicio);
        $fecha->modify('+1 month');  // Añadir un mes a la fecha de inicio para la fecha de vencimiento
        return $fecha->format('Y-m-d');
    }

    

public function obtenerTotalClientes($searchTerm = '')
{
    try {
        // Consulta para obtener el total de clientes filtrados
        $sql = "SELECT COUNT(*) as total
                FROM clientes c
                LEFT JOIN financiamiento f ON c.id_cliente = f.id_cliente
                WHERE c.datos LIKE ? OR f.codigo_asociado LIKE ?";

        $stmt = $this->conectar->prepare($sql);
        $searchTermLike = "%$searchTerm%";
        $stmt->bind_param("ss", $searchTermLike, $searchTermLike);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['total'];
    } catch (Exception $e) {
        error_log("Error en Cliente::obtenerTotalClientes(): " . $e->getMessage());
        throw $e;
    }
}

public function obtenerPorConductor($id_conductor)
{
    try {
        $sql = "SELECT c.tipo_doc, c.nro_documento, 
                       CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo,
                       c.numeroCodFi, 
                       c.numUnidad 
                FROM conductores c
                WHERE c.id_conductor = ?";
        
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_conductor);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $conductor = $result->fetch_assoc();
        
        return $conductor ? $conductor : [];
    } catch (Exception $e) {
        error_log("Error en Financiamiento::obtenerPorConductor(): " . $e->getMessage());
        throw $e;
    }
}

public function obtenerFinanciamientoPorCliente($id_cliente)
    {
        try {
            $sql = "SELECT * FROM financiamiento WHERE id_cliente = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_cliente);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            return ['error' => 'Error al obtener financiamiento: ' . $e->getMessage()];
        }
    }

        
    public function ObtenerFinanciamientoPorConductor ($id_conductor){
        try {
            $sql = "SELECT * FROM financiamiento WHERE id_conductor = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_conductor);
            $stmt->execute();
            $result = $stmt->get_result();
    
            return $result->fetch_assoc();
        } catch (Exception $e) {
            return ['error' => 'Error al obtener el financiamiento: ' . $e->getMessage()];
        }     
    }

    public function buscarFinanciamientos($query)
    {
        $query = strtolower($query);
        $query = "%$query%";

        $sql = "
            SELECT 
                f.idfinanciamiento AS id,
                -- Usamos IFNULL para elegir el nombre del conductor o cliente según corresponda 👇
                IFNULL(
                    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno),
                    CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno)
                ) AS cliente, -- 🔄 Modificado: ahora puede ser conductor o cliente
                f.fecha_creacion AS fecha,
                f.monto_total AS monto,
                f.estado AS estado
            FROM financiamiento f
            LEFT JOIN conductores c ON f.id_conductor = c.id_conductor -- 🔄 Cambiado de INNER JOIN a LEFT JOIN
            LEFT JOIN clientes_financiar cf ON f.id_cliente = cf.id
            WHERE 
                LOWER(f.idfinanciamiento) LIKE ? OR
                LOWER(CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno)) LIKE ? OR
                LOWER(CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno)) LIKE ? OR
                LOWER(f.fecha_creacion) LIKE ? OR
                LOWER(f.monto_total) LIKE ? OR
                LOWER(f.estado) LIKE ? OR
                LOWER(c.nro_documento) LIKE ? OR -- 👈 sigue buscando en nro_documento de conductores
                LOWER(cf.n_documento) LIKE ?
        ";

        // 🔄 Cambiamos a 8 parámetros ahora (antes eran 6)
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('ssssssss', $query, $query, $query, $query, $query, $query, $query, $query); 
        $stmt->execute();

        $result = $stmt->get_result();
        $resultados = [];

        while ($row = $result->fetch_assoc()) {
            $resultados[] = $row;
        }

        $stmt->close();
        return $resultados;
    }

    public function getFinanciamientoById($id) {
        $sql = "SELECT * FROM financiamiento WHERE idfinanciamiento = ?";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            die('Error al preparar la consulta financiamiento: ' . $this->conectar->error);
        }
        
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        // Verificamos si el resultado es null
        if ($result === null) {
            return []; // Retornamos un array vacío si no se encuentra el financiamiento
        }
        
        return $result;
    }
    
    public function getConductorById($id) {
        $sql = "SELECT * FROM conductores WHERE id_conductor = ?";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            die('Error al preparar la consulta conductor: ' . $this->conectar->error);
        }
        
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        // Verificamos si el resultado es null
        if ($result === null) {
            return []; // Retornamos un array vacío si no se encuentra el conductor
        }
        
        return $result;
    }
    
    public function getDireccionCompleta($idConductor) {
        $sql = "
            SELECT 
                dc.direccion_detalle,
                dep.nombre AS departamento,
                prov.nombre AS provincia,
                dist.nombre AS distrito
            FROM direccion_conductor dc
            LEFT JOIN depast dep ON dc.departamento = dep.iddepast
            LEFT JOIN provincet prov ON dc.provincia = prov.idprovincet
            LEFT JOIN distritot dist ON dc.distrito = dist.iddistritot
            WHERE dc.id_conductor = ?
        ";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            die('Error al preparar la consulta direccion conductor: ' . $this->conectar->error);
        }
        
        $stmt->bind_param('i', $idConductor);
        $stmt->execute();
        $direccion = $stmt->get_result()->fetch_assoc();
        
        // Verificamos si la dirección es null
        if ($direccion === null) {
            return ''; // Retornamos una cadena vacía si no se encuentra la dirección
        }
        
        // Verificamos si los valores son null antes de devolverlos
        $direccionDetalle = isset($direccion['direccion_detalle']) ? $direccion['direccion_detalle'] : '';
        $departamento = isset($direccion['departamento']) ? $direccion['departamento'] : '';
        $provincia = isset($direccion['provincia']) ? $direccion['provincia'] : '';
        $distrito = isset($direccion['distrito']) ? $direccion['distrito'] : '';
        
        return "{$direccionDetalle}, {$departamento}, {$provincia}, {$distrito}";
    }
    
    public function getProductoById($id) {
        $sql = "SELECT codigo, nombre FROM productosv2 WHERE idproductosv2 = ?";
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            die('Error al preparar la consulta producto: ' . $this->conectar->error);
        }
        
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $producto = $stmt->get_result()->fetch_assoc();
        
        // Verificamos si el producto es null
        if ($producto === null) {
            return ['codigo' => 'N/A', 'nombre' => 'Producto no disponible']; // Retornamos valores por defecto
        }
        
        // Verificamos si los valores son null antes de devolverlos
        $codigo = isset($producto['codigo']) ? $producto['codigo'] : 'N/A';
        $nombre = isset($producto['nombre']) ? $producto['nombre'] : 'Producto no disponible';
        
        return ['codigo' => $codigo, 'nombre' => $nombre];
    }

    public function buscarFinanciamientosPorFecha($fechaInicio, $fechaFin)
    {
        // Consulta SQL para obtener los financiamientos dentro del rango de fechas
        $sql = "
            SELECT 
                f.idfinanciamiento AS id,
                CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS cliente,
                f.fecha_creacion AS fecha,
                f.monto_total AS monto,
                f.estado AS estado
            FROM financiamiento f
            INNER JOIN conductores c ON f.id_conductor = c.id_conductor
            WHERE f.fecha_creacion BETWEEN ? AND ?
        ";

        // Preparar y ejecutar la consulta
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('ss', $fechaInicio, $fechaFin);
        $stmt->execute();

        $result = $stmt->get_result();
        $resultados = [];

        // Obtener los resultados
        while ($row = $result->fetch_assoc()) {
            $resultados[] = $row;
        }

        $stmt->close();
        return $resultados;
    }
    
    public function obtenerTipoProducto($idFinanciamiento)
    {
        $query = "SELECT p.tipo_producto 
                  FROM financiamiento f
                  INNER JOIN productosv2 p ON f.idproductosv2 = p.idproductosv2
                  WHERE f.idfinanciamiento = ?";
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param('i', $idFinanciamiento);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['tipo_producto'];
        }

        return null; // Si no se encuentra el producto
    }

    public function obtenerProductoConCategoria($idProducto)
    {
        $sql = "SELECT codigo, nombre, categoria, cantidad, cantidad_unidad, unidad_medida, tipo_producto, fecha_vencimiento, ruc, razon_social, precio, fecha_registro, guia_remision 
            FROM productosv2 
            WHERE idproductosv2 = ?";
        $stmt = $this->conectar->prepare($sql);

        if (!$stmt) {
            die('Error al preparar la consulta producto: ' . $this->conectar->error);
        }

        $stmt->bind_param('i', $idProducto);
        $stmt->execute();
        $producto = $stmt->get_result()->fetch_assoc();

        if ($producto === null) {
            return [
                'codigo' => 'N/A',
                'nombre' => 'Producto no disponible',
                'categoria' => 'N/A',
                'cantidad' => 0,
                'cantidad_unidad' => 0,
                'unidad_medida' => '',
                'tipo_producto' => 'N/A', // Modificado: Nueva clave añadida
                'fecha_vencimiento' => null, // Modificado: Nueva clave añadida
                'ruc' => 'N/A', // Modificado: Nueva clave añadida
                'razon_social' => 'N/A', // Modificado: Nueva clave añadida
                'precio' => 0.00, // Modificado: Nueva clave añadida
                'fecha_registro' => null, // Modificado: Nueva clave añadida
                'guia_remision' => 'N/A',
            ]; 
        }

        return $producto;
    }

    public function getFinanciamientoList($id_conductor) {

        $sql = "SELECT f.*, pf.nombre_plan 

                FROM financiamiento f 

                LEFT JOIN planes_financiamiento pf ON f.grupo_financiamiento = pf.idplan_financiamiento 

                WHERE f.id_conductor = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('i', $id_conductor);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);

    }


    public function getPlanChecker($idProducto) {
        // Consulta para obtener el id_plan del producto seleccionado
        $sql = "SELECT id_plan FROM productosv2 WHERE idproductosv2 = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $idProducto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $producto = $resultado->fetch_assoc();

        // Si no hay producto o no tiene un plan asociado, retornamos null
        if (!$producto || empty($producto['id_plan'])) {
            return null;
        }

        $idPlan = $producto['id_plan'];

        // Consulta para obtener los detalles del plan de financiamiento
        $sqlPlan = "SELECT * FROM planes_financiamiento WHERE idplan_financiamiento = ?";
        $stmtPlan = $this->conectar->prepare($sqlPlan);
        $stmtPlan->bind_param("i", $idPlan);
        $stmtPlan->execute();
        $resultadoPlan = $stmtPlan->get_result();

        return $resultadoPlan->fetch_assoc() ?: null; // Retorna el plan o null si no se encuentra
    }

    public function eliminarFinanciamiento($id_financiamiento) {
        try {
            $this->conectar->begin_transaction(); // Iniciar transacción

            // --- NUEVO BLOQUE: Restaurar stock del producto asociado al financiamiento ---
            // 1. Obtener idproductosv2 y cantidad_producto del financiamiento
            $sqlBuscarProducto = "SELECT idproductosv2, cantidad_producto FROM financiamiento WHERE idfinanciamiento = ?"; // <-- Línea agregada
            $stmtBuscar = $this->conectar->prepare($sqlBuscarProducto); // <-- Línea agregada
            $stmtBuscar->bind_param("i", $id_financiamiento); // <-- Línea agregada
            $stmtBuscar->execute(); // <-- Línea agregada
            $stmtBuscar->bind_result($idProducto, $cantidadProductoStr); // <-- Línea modificada (variable renombrada)
            if ($stmtBuscar->fetch()) { // <-- Línea agregada
                $stmtBuscar->close(); // <-- Línea agregada

                $cantidadProducto = (int) $cantidadProductoStr; // <-- Línea agregada: conversión de VARCHAR a int

                // 2. Actualizar stock en productosv2 sumando la cantidad del financiamiento
                $sqlActualizarStock = "UPDATE productosv2 SET cantidad = cantidad + ? WHERE idproductosv2 = ?"; // <-- Línea agregada
                $stmtUpdateStock = $this->conectar->prepare($sqlActualizarStock); // <-- Línea agregada
                $stmtUpdateStock->bind_param("ii", $cantidadProducto, $idProducto); // <-- Línea modificada: ahora cantidadProducto es int
                $stmtUpdateStock->execute(); // <-- Línea agregada
                if ($stmtUpdateStock->affected_rows === -1) { // <-- Línea agregada
                    throw new Exception("Error al actualizar el stock del producto."); // <-- Línea agregada
                }
                $stmtUpdateStock->close(); // <-- Línea agregada
            } else { // <-- Línea agregada
                $stmtBuscar->close(); // <-- Línea agregada
                throw new Exception("No se encontró el financiamiento con el ID proporcionado."); // <-- Línea agregada
            }

            // 1. Eliminar las cuotas asociadas
            $sqlCuotas = "DELETE FROM cuotas_financiamiento WHERE id_financiamiento = ?";
            $stmtCuotas = $this->conectar->prepare($sqlCuotas);
            $stmtCuotas->bind_param("i", $id_financiamiento);
            $stmtCuotas->execute();

            if ($stmtCuotas->affected_rows === -1) {
                throw new Exception("Error al eliminar cuotas.");
            }
            $stmtCuotas->close();

            // 2. Eliminar el financiamiento
            $sqlFinanciamiento = "DELETE FROM financiamiento WHERE idfinanciamiento = ?";
            $stmtFinanciamiento = $this->conectar->prepare($sqlFinanciamiento);
            $stmtFinanciamiento->bind_param("i", $id_financiamiento);
            $stmtFinanciamiento->execute();

            if ($stmtFinanciamiento->affected_rows === -1) {
                throw new Exception("Error al eliminar financiamiento.");
            }
            $stmtFinanciamiento->close();

            $this->conectar->commit(); // Confirmar transacción
            return true;
        } catch (Exception $e) {
            $this->conectar->rollback(); // Revertir cambios si hay error
            return false;
        }
    }

    public function getPlan($idPlan) {
        $sqlPlan = "SELECT * FROM planes_financiamiento WHERE idplan_financiamiento = ?";
        $stmtPlan = $this->conectar->prepare($sqlPlan);
        $stmtPlan->bind_param("i", $idPlan);
        $stmtPlan->execute();
        $resultadoPlan = $stmtPlan->get_result();
        return $resultadoPlan->fetch_assoc() ?: null;
    }

    public function actualizarCuotas($cuotasSeleccionadas)
    {
        $cuotasPagadas = 0;
        $totalCuotas = count($cuotasSeleccionadas);
        $idFinanciamiento = null;

            // ✅ Obtener el rol del usuario desde la sesión
        $rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;

        // Verificar si hay cuotas para procesar
        if (empty($cuotasSeleccionadas)) {
            return ['success' => true]; // No hay cuotas para actualizar
        }

        // Tomar el ID de la primera cuota para obtener el ID del financiamiento
        $primeraCuotaId = $cuotasSeleccionadas[0]['idCuota'];

        // Consultar el id_financiamiento de la primera cuota
        $queryFinanciamientoId = "SELECT id_financiamiento FROM cuotas_financiamiento WHERE idcuotas_financiamiento = ?";
        $stmtFinanciamientoId = $this->conectar->prepare($queryFinanciamientoId);
        $stmtFinanciamientoId->bind_param('i', $primeraCuotaId);
        $stmtFinanciamientoId->execute();
        $resultFinanciamientoId = $stmtFinanciamientoId->get_result();
        $rowFinanciamientoId = $resultFinanciamientoId->fetch_assoc();
        $stmtFinanciamientoId->close();

        if ($rowFinanciamientoId) {
            $idFinanciamiento = $rowFinanciamientoId['id_financiamiento'];
           
            // Consultar el idproductosv2 del financiamiento
            $queryProductoId = "SELECT idproductosv2 FROM financiamiento WHERE idfinanciamiento = ?";
            $stmtProductoId = $this->conectar->prepare($queryProductoId);
            $stmtProductoId->bind_param('i', $idFinanciamiento);
            $stmtProductoId->execute();
            $resultProductoId = $stmtProductoId->get_result();
            $rowProductoId = $resultProductoId->fetch_assoc();
            $stmtProductoId->close();

            if ($rowProductoId) {
                $idProducto = $rowProductoId['idproductosv2'];
                
                // Consultar la categoría del producto
                $queryCategoria = "SELECT categoria FROM productosv2 WHERE idproductosv2 = ?";
                $stmtCategoria = $this->conectar->prepare($queryCategoria);
                $stmtCategoria->bind_param('i', $idProducto);
                $stmtCategoria->execute();
                $resultCategoria = $stmtCategoria->get_result();
                $rowCategoria = $resultCategoria->fetch_assoc();
                $stmtCategoria->close();

                if ($rowCategoria) {
                    $categoria = $rowCategoria['categoria'];

                    $categoriaNormalizada = trim(strtolower(str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], $categoria)));

                    // Verificar si la categoría es "celular" o "vehículo" y el rol del usuario es 2
                    if (($categoriaNormalizada === 'celular' || $categoriaNormalizada === 'celulares' || $categoriaNormalizada === 'vehiculo' || $categoriaNormalizada === 'vehiculos') && $rol_usuario == 2) {
                        return ['success' => false, 'message' => 'No está autorizado para esta operación'];
                    }
                }
            }
        }

        foreach ($cuotasSeleccionadas as $cuota) {
            $idCuota = $cuota['idCuota'];
            $monto = $cuota['monto'];
            $mora = $cuota['mora'] ?? 0;

            $sql = "UPDATE cuotas_financiamiento 
                    SET mora = ?, estado = 'pagado', fecha_pago = CURDATE() 
                    WHERE idcuotas_financiamiento = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param('di', $mora, $idCuota);

            if ($stmt->execute()) {
                $cuotasPagadas++;

                // Obtenemos el id_financiamiento solo una vez
                if (!$idFinanciamiento) {
                    $queryFinanciamiento = "SELECT id_financiamiento FROM cuotas_financiamiento WHERE idcuotas_financiamiento = ?";
                    $stmt2 = $this->conectar->prepare($queryFinanciamiento);
                    $stmt2->bind_param('i', $idCuota);
                    $stmt2->execute();
                    $result = $stmt2->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $idFinanciamiento = $row['id_financiamiento'];
                    }
                }
            } else {
                return ['success' => false, 'message' => 'Error al actualizar la cuota'];
            }
        }

        // Si todas las cuotas fueron marcadas como pagadas
        if ($cuotasPagadas === $totalCuotas && $idFinanciamiento) {
            $sqlUpdateFinanciamiento = "UPDATE financiamiento SET estado = 'Finalizado' WHERE idfinanciamiento = ?";
            $stmtFinal = $this->conectar->prepare($sqlUpdateFinanciamiento);
            $stmtFinal->bind_param('i', $idFinanciamiento);

            if ($stmtFinal->execute()) {
                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Error al finalizar el financiamiento'];
            }
        }

        return ['success' => true];
    }

    public function newPago($idConductor, $idAsesor, $monto, $concepto = null, $efectivoRecibido, $vuelto, $monedaEfectivo, $idFinanciamiento = null, $idCliente = null, $metodoPago = null, $estado = 1)
    {
        try {
            $fechaPago = date('Y-m-d H:i:s'); // ✅ OK
    
            // ✅ Limpieza de montos
            $monto = (float) str_replace([',', 'S/. ', '$ '], '', $monto); 
            $efectivoRecibido = (float) str_replace([',', 'S/. ', '$ '], '', $efectivoRecibido); 
            $vuelto = (float) str_replace([',', 'S/. ', '$ '], '', $vuelto);
            $monedaEfectivo = $monedaEfectivo ?? ''; // ✅ OK
    
            // ✅ Agregamos variable que faltaba
            $idFinanciamientoParam = $idFinanciamiento > 0 ? $idFinanciamiento : null; // ⚠️ NUEVO
    
            $idConductorParam = $idConductor > 0 ? $idConductor : null; // ✅ OK
            $idClienteParam = $idCliente > 0 ? $idCliente : null; // ✅ OK
    
            $metodoPagoParam = $metodoPago ?? ''; // ✅ OK
            $conceptoParam = $concepto ?? ''; // ✅ OK
                            
            // ✅ QUERY corregida con el nuevo campo estado 🌎
            $query = "INSERT INTO pagos_financiamiento 
            (id_financiamiento, id_conductor, id_cliente, id_asesor, monto, metodo_pago, concepto, fecha_pago, efectivo_recibido, vuelto, moneda, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
            $stmt = $this->conectar->prepare($query); // ✅ OK
    
            // ✅ TIPO DE DATOS CORREGIDOS: 12 variables = 12 letras 
            $stmt->bind_param(
                "iiiidsssddsi", // ⚠️ CORREGIDO: 12 variables, 12 tipos 
                $idFinanciamientoParam, 
                $idConductorParam,     
                $idClienteParam,       
                $idAsesor,             
                $monto,                
                $metodoPagoParam,      
                $conceptoParam,        
                $fechaPago,            
                $efectivoRecibido,     
                $vuelto,               
                $monedaEfectivo,
                $estado   
            );
    
            if ($stmt->execute()) {
                $idPago = $stmt->insert_id;
                return ['success' => true, 'message' => 'Pago registrado con éxito', 'id_pago' => $idPago]; // ✅ OK
            } else {
                var_dump($stmt->error); // ⚠️ DEBUG: Mostrar error SQL
                return ['success' => false, 'message' => 'Error al registrar el pago en la base de datos'];
            }
    
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    public function guardarPagoPendiente($idPagoFinanciamiento, $cuotasJson) 
    { 
        try { 
            $query = "INSERT INTO pagos_pendientes_financiamientos 
                    (idpagos_financiamiento, cuotas_json, id_usuario_aprobacion) 
                    VALUES (?, ?, NULL)"; 
            
            $stmt = $this->conectar->prepare($query); 
            $stmt->bind_param("is", $idPagoFinanciamiento, $cuotasJson); 
            
            if ($stmt->execute()) { 
                return ['success' => true, 'message' => 'Pago pendiente guardado con éxito']; 
            } else { 
                return ['success' => false, 'message' => 'Error al guardar el pago pendiente']; 
            } 
        } catch (Exception $e) { 
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; 
        } 
    } 
  
    public function newDetallePago($cuotasSeleccionadas, $idPago) // Modificación: añadido $idPago como parámetro
    {
        foreach ($cuotasSeleccionadas as $cuota) {
            $idCuota = $cuota['idCuota'];
            $mora = $cuota['mora'];

            // Insertamos en la tabla detalle_pago_financiamiento con el idPago en lugar de idFinanciamiento
            $insertSql = "INSERT INTO detalle_pago_financiamiento (idfinanciamiento, id_cuota, mora) VALUES (?, ?, ?)";
            $insertStmt = $this->conectar->prepare($insertSql); // Usamos $this->conectar

            if (!$insertStmt) { // Verificamos si $insertStmt es falso
                die("Error en la preparación de la consulta SQL de inserción: " . $this->conectar->error); // Mostramos el error específico
            }

            $insertStmt->bind_param("iid", $idPago, $idCuota, $mora); // CORREGIDO: Usamos $idPago en lugar de $idFinanciamiento
            $insertStmt->execute(); // Ejecutamos el insert

            // Comprobamos errores (opcional, puedes agregar logs aquí si lo prefieres)
            if ($insertStmt->affected_rows > 0) {
                error_log("Cuota $idCuota registrada en detalle_pago_financiamiento con ID de pago $idPago y mora $mora."); // Mensaje de éxito
            } else {
                error_log("Error al registrar cuota $idCuota en detalle_pago_financiamiento."); // Mensaje de error si no se guarda
            }

            $insertStmt->close(); // Cerramos el statement del insert
        }
    }
    
    public function obtenerReportesPagos($offset, $limit, $search, $fechaInicio = '', $fechaFin = '') {
        // MODIFICADO: Base de la consulta - Cambiado INNER JOIN a LEFT JOIN para conductores
        $query = "SELECT p.*, 
            CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS conductor, 
            CONCAT(u.nombres, ' ', u.apellidos) AS asesor,
            c.numUnidad AS numUnidad,
            CONCAT(cf.nombres, ' ', cf.apellido_paterno, ' ', cf.apellido_materno) AS cliente  
        FROM pagos_financiamiento p
        LEFT JOIN conductores c ON p.id_conductor = c.id_conductor  
        LEFT JOIN clientes_financiar cf ON p.id_cliente = cf.id
        LEFT JOIN usuarios u ON p.id_asesor = u.usuario_id
        WHERE (c.nombres LIKE ? OR c.apellido_paterno LIKE ? OR u.nombres LIKE ? OR p.monto LIKE ?
        OR c.numUnidad LIKE ? OR
            cf.nombres LIKE ? OR cf.apellido_paterno LIKE ?  
        )";

        // NUEVO: Añadir condición de fechas si están presentes
        $params = [];
        $types = "sssssss"; 
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;  /* AÑADIDO: para búsqueda en cf.nombres */
        $params[] = $searchTerm; 

        // NUEVO: Si hay fechas, añadir al WHERE
        if (!empty($fechaInicio) && !empty($fechaFin)) {
        $query .= " AND DATE(p.fecha_pago) BETWEEN ? AND ?";
        $types .= "ss";
        $params[] = $fechaInicio;
        $params[] = $fechaFin;
        }

        // MODIFICACIÓN SOLICITADA: Filtrar por estado 1 o NULL
        $query .= " AND (p.estado = 1 OR p.estado IS NULL)";

        $query .= " ORDER BY p.fecha_pago DESC LIMIT ?, ?";
        $types .= "ii";
        $params[] = $offset;
        $params[] = $limit;

        $stmt = $this->conectar->prepare($query);

        if (!$stmt) {
            var_dump("🔴 Error en prepare():", $this->conectar->error);  // 🛒
            var_dump("📄 Consulta SQL:", $query);  // 🛒
            return [];  // 🛒
        }


        // MODIFICADO: Usar call_user_func_array para bind_param dinámico
        $bindParams = array_merge([$types], $params);
        $ref = [];
        foreach($bindParams as $key => $value) {
            $ref[$key] = &$bindParams[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $ref);
        
        // 3. Ejecutar el statement 🛒
        if (!$stmt->execute()) {  // 🛒
            var_dump("🔴 Error en execute():", $stmt->error);  // 🛒
            return [];  // 🛒
        }

        $result = $stmt->get_result();

        if ($result === false) {
            var_dump($stmt->error);
            return []; // O maneja el error
        }
        
        $reportes = [];
        while ($row = $result->fetch_assoc()) {
            /* AÑADIDO: Usar cliente como nombre cuando conductor está vacío */
            if (empty($row['conductor']) && !empty($row['cliente'])) {
                $row['conductor'] = $row['cliente'];  /* Asignar nombre del cliente al campo conductor para mantener compatibilidad */
            }
            $reportes[] = $row;
        }
        $stmt->close();

        return $reportes;
    }

    public function contarReportes($search, $fechaInicio = '', $fechaFin = '') {
        // MODIFICADO: Base de la consulta - Cambiado INNER JOIN a LEFT JOIN para conductores
        $query = "SELECT COUNT(*) AS total FROM pagos_financiamiento p
                  LEFT JOIN conductores c ON p.id_conductor = c.id_conductor  
                  LEFT JOIN clientes_financiar cf ON p.id_cliente = cf.id  
                  INNER JOIN usuarios u ON p.id_asesor = u.usuario_id
                  WHERE (c.nombres LIKE ? OR c.apellido_paterno LIKE ? OR u.nombres LIKE ? OR p.monto LIKE ?
                    OR c.numUnidad LIKE ? OR cf.nombres LIKE ? OR cf.apellido_paterno LIKE ?  
                  )";

    
        // NUEVO: Preparar parámetros
        $params = [];
        $types = "sssssss";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;  // Corregido: ahora sí agregamos para c.numUnidad LIKE 🛒
        $params[] = $searchTerm;
        $params[] = $searchTerm;  /* AÑADIDO: para búsqueda en cf.nombres */
       
        
        // NUEVO: Si hay fechas, añadir al WHERE
        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $query .= " AND DATE(p.fecha_pago) BETWEEN ? AND ?";
            $types .= "ss";
            $params[] = $fechaInicio;
            $params[] = $fechaFin;
        }
        
        // MODIFICACIÓN SOLICITADA: Filtrar por estado 1 o NULL
        $query .= " AND (p.estado = 1 OR p.estado IS NULL)"; // NUEVA LÍNEA agregada para filtrar registros con estado 1 o NULL
        
        $stmt = $this->conectar->prepare($query);
        
        if (!$stmt) {  // 🛒 Validar que prepare no haya fallado
            var_dump("🔴 Error en prepare():", $this->conectar->error);
            var_dump("📄 Consulta SQL:", $query);
            return false;  // Detener ejecución para evitar errores posteriores
        }

        // MODIFICADO: Usar call_user_func_array para bind_param dinámico
        $bindParams = array_merge([$types], $params);
        $ref = [];
        foreach($bindParams as $key => $value) {
            $ref[$key] = &$bindParams[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $ref);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    public function deleteReportFinance($idPago)
    {
        try {
            // Eliminar de la tabla detalle_pago_financiamiento (si existe relación)
            $stmtDetalle = $this->conectar->prepare("DELETE FROM detalle_pago_financiamiento WHERE idfinanciamiento = ?");
            $stmtDetalle->bind_param("i", $idPago);
            $stmtDetalle->execute();

            // Eliminar de la tabla pagos_financiamiento
            $stmt = $this->conectar->prepare("DELETE FROM pagos_financiamiento WHERE idpagos_financiamiento = ?");
            $stmt->bind_param("i", $idPago);
            $result = $stmt->execute();

            return $result; // Retorna true si se eliminó exitosamente
        } catch (Exception $e) {
            return false; // Manejo básico de errores
        }
    }

    public function getDataPago($idPago) {
        $queryPago = "SELECT * FROM pagos_financiamiento WHERE idpagos_financiamiento = ?";
        $stmtPago = $this->conectar->prepare($queryPago);
        $stmtPago->bind_param("i", $idPago);
        $stmtPago->execute();
        $resultadoPago = $stmtPago->get_result();
        $pago = $resultadoPago->fetch_assoc();
    
        if ($pago) {
            // Obtener las cuotas relacionadas a este pago
            $queryCuotas = "SELECT id_cuota, mora FROM detalle_pago_financiamiento WHERE idfinanciamiento = ?";
            $stmtCuotas = $this->conectar->prepare($queryCuotas);
            $stmtCuotas->bind_param("i", $idPago);
            $stmtCuotas->execute();
            $resultadoCuotas = $stmtCuotas->get_result();
            $cuotas = [];
    
            // Recorremos cada cuota y obtenemos los detalles completos desde la tabla cuotas_financiamiento
            while ($filaCuota = $resultadoCuotas->fetch_assoc()) {
                $idCuota = $filaCuota['id_cuota'];

                // Realizar una consulta adicional para obtener el monto de la cuota desde la tabla cuotas_financiamiento
                $queryMontoCuota = "SELECT monto, mora FROM cuotas_financiamiento WHERE idcuotas_financiamiento = ?";
                $stmtMontoCuota = $this->conectar->prepare($queryMontoCuota); // Crear una nueva consulta
                $stmtMontoCuota->bind_param("i", $idCuota); // Asignar el id de la cuota
                $stmtMontoCuota->execute();
                $resultadoMontoCuota = $stmtMontoCuota->get_result();
                $detalleMontoCuota = $resultadoMontoCuota->fetch_assoc();

                if ($detalleMontoCuota) {
                    // Sumar el monto y la mora para obtener el valor completo y agregar al array
                    $cuotas[] = [
                        'id_cuota' => $idCuota,                                     // Mantener el ID de la cuota
                        'monto' => $detalleMontoCuota['monto'],                     // Agregar el monto de la cuota
                        'mora' => $filaCuota['mora'] + $detalleMontoCuota['mora'],  // Sumar la mora existente y la nueva
                    ];
                } else {
                    error_log("No se encontró el detalle de la cuota con id $idCuota"); // Manejar el caso de error
                }
            }

            // Devolver toda la información relevante con cuotas completas
            return [
                'id_conductor' => $pago['id_conductor'],
                'id_asesor' => $pago['id_asesor'],
                'moneda' => $pago['moneda'],
                'cuotas' => $cuotas, // Ahora con 'monto' y mora sumados correctamente
            ];
        }
    
        return null; // Si no hay datos del pago
    }    


    // Método para actualizar un financiamiento

public function actualizarFinanciamiento($idFinanciamiento, $codigoAsociado, $grupoFinanciamiento, $estado)

{

    $sql = "UPDATE financiamiento 

            SET codigo_asociado = ?, grupo_financiamiento = ?, estado = ? 

            WHERE idfinanciamiento = ?";

    $stmt = $this->conectar->prepare($sql);

    $stmt->bind_param('sssi', $codigoAsociado, $grupoFinanciamiento, $estado, $idFinanciamiento);

    return $stmt->execute();

}


    // Método para obtener un financiamiento por su ID

    public function obtenerFinanciamientoPorId($idFinanciamiento)

    {
    
        $sql = "SELECT * FROM financiamiento WHERE idfinanciamiento = ?";
    
        $stmt = $this->conectar->prepare($sql);
    
        $stmt->bind_param('i', $idFinanciamiento);
    
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        return $result->fetch_assoc();
    
    }

    public function obtenerGruposFinanciamiento() {

        // Modificar para usar la tabla planes_financiamiento en lugar de grupovehicular_financiamiento

        $sql = "SELECT idplan_financiamiento, nombre_plan FROM planes_financiamiento";

        $stmt = $this->conectar->prepare($sql);



        if ($stmt->execute()) {

            $result = $stmt->get_result();

            $grupos = $result->fetch_all(MYSQLI_ASSOC);

            return $grupos;

        } else {

            return [];

        }

    }

    // Busca esta función y reemplázala completamente:
    public function getVariante($idPlan) {
        // MODIFICADO: Agregado JOIN para obtener tipo_vehicular del grupo padre
        $sql = "SELECT gv.*, pf.tipo_vehicular 
                FROM grupos_variantes gv 
                INNER JOIN planes_financiamiento pf ON gv.idplan_financiamiento = pf.idplan_financiamiento 
                WHERE gv.idplan_financiamiento = ?";
        
        $stmt = $this->conectar->prepare($sql);
        
        if (!$stmt) {
            return false;
        }
        
        // Vincular el parámetro
        $stmt->bind_param("i", $idPlan);
        
        // Ejecutar la consulta
        if (!$stmt->execute()) {
            return false;
        }
        
        // Obtener el resultado
        $resultado = $stmt->get_result();
        
        // Array para almacenar todas las variantes
        $variantes = [];
        
        // Obtener cada variante con su tipo_vehicular heredado
        while ($variante = $resultado->fetch_assoc()) {
            $variantes[] = $variante;
        }
        
        return $variantes;
    }
    
    public function getFinanciamientoByCliente($id_cliente) {
        $sql = "SELECT f.*, pf.nombre_plan 
                FROM financiamiento f 
                LEFT JOIN planes_financiamiento pf ON f.grupo_financiamiento = pf.idplan_financiamiento 
                WHERE f.id_cliente = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('i', $id_cliente);
        
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorCliente($id_cliente)
    {
        try {
            // ✅ Usamos la tabla correcta y seleccionamos los campos necesarios
            $sql = "SELECT tipo_doc, n_documento, 
                        CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno) AS nombre_completo,
                        num_cod_finan
                    FROM clientes_financiar 
                    WHERE id = ?";

            $stmt = $this->conectar->prepare($sql); // ✅ Preparar con mysqli, no PDO
            $stmt->bind_param("i", $id_cliente); // ✅ Asegurar que el ID sea entero
            $stmt->execute();

            $result = $stmt->get_result(); // ✅ Obtener resultados como objeto MySQLi_Result
            $cliente = $result->fetch_assoc();

            if ($cliente) {
                $cliente['numUnidad'] = null; // ✅ Agregar manualmente numUnidad como null
            }

            return $cliente ? $cliente : []; // ✅ Si no hay resultado, retornar arreglo vacío
        } catch (Exception $e) {
            error_log("Error en Financiamiento::obtenerPorCliente(): " . $e->getMessage()); // ✅ Log del error
            throw $e; // ✅ Relanzar el error para manejo externo
        }
    }

    public function getFinanciamientoListCliente($id_cliente) {
        $sql = "SELECT f.*, pf.nombre_plan 
                FROM financiamiento f 
                LEFT JOIN planes_financiamiento pf ON f.grupo_financiamiento = pf.idplan_financiamiento 
                WHERE f.id_cliente = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('i', $id_cliente);
    
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllFinanciamientos()
    {
        $sql = "SELECT * FROM financiamiento";
        $result = $this->conectar->query($sql);
        $financiamientos = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $financiamientos[] = $row;
            }
        }

        return $financiamientos;
    }

    public function obtenerUsuarioRegistro($id_financiamiento)
    {
        try {
            $sql = "SELECT u.nombres, u.apellidos 
                    FROM financiamiento f 
                    LEFT JOIN usuarios u ON f.usuario_id = u.usuario_id 
                    WHERE f.idfinanciamiento = ?";
            
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $id_financiamiento);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $usuario = $result->fetch_assoc();
            
            if ($usuario) {
                // Construir el nombre completo con validaciones
                $nombre = trim($usuario['nombres'] ?? '');
                $apellidos = trim($usuario['apellidos'] ?? '');
                
                if (!empty($nombre) && !empty($apellidos)) {
                    return $nombre . ' ' . $apellidos;
                } elseif (!empty($nombre)) {
                    return $nombre;
                } elseif (!empty($apellidos)) {
                    return $apellidos;
                }
            }
            
            return 'No identificado';
        } catch (Exception $e) {
            error_log("Error en Financiamiento::obtenerUsuarioRegistro(): " . $e->getMessage());
            return 'No identificado';
        }
    }

    public function verificarCodigoAsociadoDuplicado($codigoAsociado, $grupoFinanciamiento) {
        try {
            // Primero verificar si el plan es vehicular
            $sqlPlan = "SELECT tipo_vehicular FROM planes_financiamiento WHERE idplan_financiamiento = ?";
            $stmtPlan = mysqli_prepare($this->conectar, $sqlPlan);
            mysqli_stmt_bind_param($stmtPlan, "s", $grupoFinanciamiento);
            mysqli_stmt_execute($stmtPlan);
            $resultPlan = mysqli_stmt_get_result($stmtPlan);
            
            if ($rowPlan = mysqli_fetch_assoc($resultPlan)) {
                $tipoVehicular = $rowPlan['tipo_vehicular'];
                
                // Solo validar si es vehicular (vehiculo o moto)
                if ($tipoVehicular === 'vehiculo' || $tipoVehicular === 'moto') {
                    // Verificar si existe código duplicado
                    $sqlFinanciamiento = "SELECT COUNT(*) as total FROM financiamiento 
                                        WHERE codigo_asociado = ? AND grupo_financiamiento = ? AND codigo_asociado IS NOT NULL";
                    $stmtFinanciamiento = mysqli_prepare($this->conectar, $sqlFinanciamiento);
                    mysqli_stmt_bind_param($stmtFinanciamiento, "ss", $codigoAsociado, $grupoFinanciamiento);
                    mysqli_stmt_execute($stmtFinanciamiento);
                    $resultFinanciamiento = mysqli_stmt_get_result($stmtFinanciamiento);
                    
                    if ($rowFinanciamiento = mysqli_fetch_assoc($resultFinanciamiento)) {
                        return $rowFinanciamiento['total'] > 0;
                    }
                }
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error en verificarCodigoAsociadoDuplicado: " . $e->getMessage());
            return false;
        }
    }

    public function contarCreditosActivos($tipo, $id)
    {
        $campo = ($tipo === 'cliente') ? 'id_cliente' : 'id_conductor';
        $sql = "SELECT COUNT(*) as total FROM financiamiento WHERE {$campo} = ? AND (estado = 'En Progreso' OR estado = 'En progreso')";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $fila = $result->fetch_assoc();
        return (int)$fila['total'];
    }
    
}
