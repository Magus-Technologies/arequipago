<?php

class Beneficio
{
    private $id;
    private $nombre;
    private $categoria;
    private $plan_financiamiento_id;
    private $descripcion;
    // Nuevos campos de financiamiento
    private $cuota_inicial;
    private $cantidad_cuotas;
    private $cuota_mensual;

    // Campos anteriores (mantenidos por compatibilidad)
    private $precio_contado;
    private $precio_financiado;
    private $cuotas_disponibles;
    private $tasa_interes;
    private $requisitos;
    private $imagen_principal;
    private $galeria_imagenes;
    private $stock_disponible;
    private $disponible;
    private $codigo_producto;
    private $marca;
    private $modelo;
    private $especificaciones;
    private $peso;
    private $dimensiones;
    private $garantia_meses;
    private $proveedor;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // ============ GETTERS Y SETTERS ============
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }

    public function getCategoria() { return $this->categoria; }
    public function setCategoria($categoria) { $this->categoria = $categoria; }

    public function getPlanFinanciamientoId() { return $this->plan_financiamiento_id; }
    public function setPlanFinanciamientoId($plan_financiamiento_id) { $this->plan_financiamiento_id = $plan_financiamiento_id; }

    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }

    // Getters y Setters para nuevos campos de financiamiento
    public function getCuotaInicial() { return $this->cuota_inicial; }
    public function setCuotaInicial($cuota_inicial) { $this->cuota_inicial = $cuota_inicial; }

    public function getCantidadCuotas() { return $this->cantidad_cuotas; }
    public function setCantidadCuotas($cantidad_cuotas) { $this->cantidad_cuotas = $cantidad_cuotas; }

    public function getCuotaMensual() { return $this->cuota_mensual; }
    public function setCuotaMensual($cuota_mensual) { $this->cuota_mensual = $cuota_mensual; }

    public function getPrecioContado() { return $this->precio_contado; }
    public function setPrecioContado($precio_contado) { $this->precio_contado = $precio_contado; }

    public function getPrecioFinanciado() { return $this->precio_financiado; }
    public function setPrecioFinanciado($precio_financiado) { $this->precio_financiado = $precio_financiado; }

    public function getCuotasDisponibles() { return $this->cuotas_disponibles; }
    public function setCuotasDisponibles($cuotas_disponibles) { $this->cuotas_disponibles = $cuotas_disponibles; }

    public function getTasaInteres() { return $this->tasa_interes; }
    public function setTasaInteres($tasa_interes) { $this->tasa_interes = $tasa_interes; }

    public function getRequisitos() { return $this->requisitos; }
    public function setRequisitos($requisitos) { $this->requisitos = $requisitos; }

    public function getImagenPrincipal() { return $this->imagen_principal; }
    public function setImagenPrincipal($imagen_principal) { $this->imagen_principal = $imagen_principal; }

    public function getGaleriaImagenes() { return $this->galeria_imagenes; }
    public function setGaleriaImagenes($galeria_imagenes) { $this->galeria_imagenes = $galeria_imagenes; }

    public function getStockDisponible() { return $this->stock_disponible; }
    public function setStockDisponible($stock_disponible) { $this->stock_disponible = $stock_disponible; }

    public function getDisponible() { return $this->disponible; }
    public function setDisponible($disponible) { $this->disponible = $disponible; }


    public function getCodigoProducto() { return $this->codigo_producto; }
    public function setCodigoProducto($codigo_producto) { $this->codigo_producto = $codigo_producto; }

    public function getMarca() { return $this->marca; }
    public function setMarca($marca) { $this->marca = $marca; }

    public function getModelo() { return $this->modelo; }
    public function setModelo($modelo) { $this->modelo = $modelo; }

    public function getEspecificaciones() { return $this->especificaciones; }
    public function setEspecificaciones($especificaciones) { $this->especificaciones = $especificaciones; }

    public function getPeso() { return $this->peso; }
    public function setPeso($peso) { $this->peso = $peso; }

    public function getDimensiones() { return $this->dimensiones; }
    public function setDimensiones($dimensiones) { $this->dimensiones = $dimensiones; }

    public function getGarantiaMeses() { return $this->garantia_meses; }
    public function setGarantiaMeses($garantia_meses) { $this->garantia_meses = $garantia_meses; }

    public function getProveedor() { return $this->proveedor; }
    public function setProveedor($proveedor) { $this->proveedor = $proveedor; }

    public function getConexion() { 
    return $this->conectar; 
}
    // ============ MÉTODOS PRINCIPALES ============

    /**
     * Crear un nuevo beneficio
     */
    public function crear($datos)
    {
        try {
            // Debug: Log de datos recibidos
            error_log('Beneficio::crear() - Datos recibidos: ' . json_encode($datos));
            error_log('Beneficio::crear() - Imagen específica: ' . ($datos['imagen'] ?? 'NULL'));

            // Validar campos obligatorios
            if (empty($datos['nombre'])) {
                throw new Exception('El nombre es obligatorio');
            }
            if (!isset($datos['plan_financiamiento_id']) || $datos['plan_financiamiento_id'] <= 0) {
                throw new Exception('El plan de financiamiento es obligatorio');
            }
            if (!isset($datos['cuota_inicial']) || $datos['cuota_inicial'] <= 0) {
                throw new Exception('La cuota inicial es obligatoria');
            }
            if (!isset($datos['cantidad_cuotas']) || $datos['cantidad_cuotas'] <= 0) {
                throw new Exception('La cantidad de cuotas es obligatoria');
            }
            if (!isset($datos['cuota_mensual']) || $datos['cuota_mensual'] <= 0) {
                throw new Exception('La cuota mensual es obligatoria');
            }

            $sql = "INSERT INTO beneficios (
                        nombre, plan_financiamiento_id, categoria, descripcion, cuota_inicial, cantidad_cuotas, cuota_mensual,
                        moneda, nombre_plan_personalizado, frecuencia_pago, imagen, disponible, departamento_id
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            // Preparar datos básicos
            $nombre = $datos['nombre'];
            $plan_financiamiento_id = $datos['plan_financiamiento_id'];
            $categoria = isset($datos['categoria']) && $datos['categoria'] !== null ? $datos['categoria'] : null;
            $descripcion = isset($datos['descripcion']) && $datos['descripcion'] !== null ? $datos['descripcion'] : '';
            $cuota_inicial = $datos['cuota_inicial'];
            $cantidad_cuotas = $datos['cantidad_cuotas'];
            $cuota_mensual = $datos['cuota_mensual'];
            $moneda = isset($datos['moneda']) && !empty($datos['moneda']) ? $datos['moneda'] : null;
            $nombre_plan_personalizado = isset($datos['nombre_plan_personalizado']) && !empty($datos['nombre_plan_personalizado']) ? $datos['nombre_plan_personalizado'] : null;
            $frecuencia_pago = isset($datos['frecuencia_pago']) && !empty($datos['frecuencia_pago']) ? $datos['frecuencia_pago'] : null;
            $imagen = isset($datos['imagen']) && $datos['imagen'] !== null && $datos['imagen'] !== '' ? $datos['imagen'] : null;
            $disponible = $datos['disponible'];
            $departamento_id = isset($datos['departamento_id']) && $datos['departamento_id'] !== null && $datos['departamento_id'] !== '' ? (int)$datos['departamento_id'] : null;

            $stmt->bind_param(
                'siisdidssssii',
                $nombre,                       // s - string
                $plan_financiamiento_id,       // i - integer
                $categoria,                    // i - integer (nullable)
                $descripcion,                  // s - string
                $cuota_inicial,                // d - decimal
                $cantidad_cuotas,              // i - integer
                $cuota_mensual,                // d - decimal
                $moneda,                       // s - string (nullable)
                $nombre_plan_personalizado,    // s - string (nullable)
                $frecuencia_pago,              // s - string (nullable)
                $imagen,                       // s - string
                $disponible,                   // i - integer
                $departamento_id               // i - integer (nullable)
            );

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $insertId = $stmt->insert_id;
            $stmt->close();

            return $insertId;
        } catch (Exception $e) {
            error_log('Error en Beneficio::crear(): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todos los beneficios
     */
    public function obtenerTodos($filtros = [])
    {
        try {
            $sql = "SELECT b.*,
                    COALESCE(b.moneda, p.moneda) as moneda,
                    COALESCE(b.nombre_plan_personalizado, p.nombre_plan) as nombre_plan_display
                    FROM beneficios b
                    LEFT JOIN planes_financiamiento p ON b.plan_financiamiento_id = p.idplan_financiamiento
                    WHERE 1=1";
            $params = [];
            $types = "";

            // Aplicar filtros
            if (!empty($filtros['categoria'])) {
                $sql .= " AND b.categoria = ?";
                $params[] = $filtros['categoria'];
                $types .= "i";
            }

            if (!empty($filtros['plan_financiamiento_id'])) {
                $sql .= " AND b.plan_financiamiento_id = ?";
                $params[] = $filtros['plan_financiamiento_id'];
                $types .= "i";
            }

            if (!empty($filtros['disponible'])) {
                $sql .= " AND b.disponible = ?";
                $params[] = $filtros['disponible'];
                $types .= "i";
            }

            // Filtro por departamento
            if (isset($filtros['departamento_id'])) {
                if ($filtros['departamento_id'] === 'nacional') {
                    // Beneficios nacionales (sin departamento específico)
                    $sql .= " AND b.departamento_id IS NULL";
                } else if (!empty($filtros['departamento_id'])) {
                    // Beneficios de un departamento específico O nacionales
                    $sql .= " AND (b.departamento_id = ? OR b.departamento_id IS NULL)";
                    $params[] = $filtros['departamento_id'];
                    $types .= "i";
                }
            }

            if (!empty($filtros['busqueda'])) {
                $sql .= " AND (b.nombre LIKE ? OR b.descripcion LIKE ? OR b.marca LIKE ? OR b.modelo LIKE ?)";
                $busqueda = '%' . $filtros['busqueda'] . '%';
                $params[] = $busqueda;
                $params[] = $busqueda;
                $params[] = $busqueda;
                $params[] = $busqueda;
                $types .= "ssss";
            }

            $sql .= " ORDER BY b.fecha_creacion DESC";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $beneficios = [];

            while ($row = $result->fetch_assoc()) {
                $beneficios[] = $row;
            }

            $stmt->close();
            return $beneficios;
        } catch (Exception $e) {
            error_log('Error en Beneficio::obtenerTodos(): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un beneficio por ID
     */
    public function obtenerPorId($id)
    {
        try {
            $sql = "SELECT * FROM beneficios WHERE id = ?";
            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('i', $id);

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $beneficio = $result->fetch_assoc();

            // $beneficio ya viene limpio desde la base de datos

            $stmt->close();
            return $beneficio;
        } catch (Exception $e) {
            error_log('Error en Beneficio::obtenerPorId(): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar un beneficio
     */
    public function actualizar($id, $datos)
    {
        try {
            $sql = "UPDATE beneficios SET
                        nombre = ?, plan_financiamiento_id = ?, categoria = ?, descripcion = ?, cuota_inicial = ?,
                        cantidad_cuotas = ?, cuota_mensual = ?, moneda = ?, nombre_plan_personalizado = ?, frecuencia_pago = ?, imagen = ?,
                        disponible = ?, departamento_id = ?, fecha_actualizacion = CURRENT_TIMESTAMP
                    WHERE id = ?";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            // Validar campos obligatorios
            if (empty($datos['nombre'])) {
                throw new Exception('El nombre es obligatorio');
            }
            if (!isset($datos['plan_financiamiento_id']) || $datos['plan_financiamiento_id'] <= 0) {
                throw new Exception('El plan de financiamiento es obligatorio');
            }

            // Preparar datos básicos
            $nombre = $datos['nombre'];
            $plan_financiamiento_id = $datos['plan_financiamiento_id'];
            $categoria = isset($datos['categoria']) && $datos['categoria'] !== null ? $datos['categoria'] : null;
            $descripcion = isset($datos['descripcion']) && $datos['descripcion'] !== null ? $datos['descripcion'] : '';
            $cuota_inicial = $datos['cuota_inicial'];
            $cantidad_cuotas = $datos['cantidad_cuotas'];
            $cuota_mensual = $datos['cuota_mensual'];
            $moneda = isset($datos['moneda']) && !empty($datos['moneda']) ? $datos['moneda'] : null;
            $nombre_plan_personalizado = isset($datos['nombre_plan_personalizado']) && !empty($datos['nombre_plan_personalizado']) ? $datos['nombre_plan_personalizado'] : null;
            $frecuencia_pago = isset($datos['frecuencia_pago']) && !empty($datos['frecuencia_pago']) ? $datos['frecuencia_pago'] : null;
            $imagen = isset($datos['imagen']) && $datos['imagen'] !== null && $datos['imagen'] !== '' ? $datos['imagen'] : null;
            $disponible = $datos['disponible'];
            $departamento_id = isset($datos['departamento_id']) && $datos['departamento_id'] !== null && $datos['departamento_id'] !== '' ? (int)$datos['departamento_id'] : null;

            $stmt->bind_param(
                'siisdidssssiii',
                $nombre,                       // s - string
                $plan_financiamiento_id,       // i - integer
                $categoria,                    // i - integer (nullable)
                $descripcion,                  // s - string
                $cuota_inicial,                // d - decimal
                $cantidad_cuotas,              // i - integer
                $cuota_mensual,                // d - decimal
                $moneda,                       // s - string (nullable)
                $nombre_plan_personalizado,    // s - string (nullable)
                $frecuencia_pago,              // s - string (nullable)
                $imagen,                       // s - string
                $disponible,                   // i - integer
                $departamento_id,              // i - integer (nullable)
                $id                            // i - integer
            );

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $affected = $stmt->affected_rows;
            $stmt->close();

            return $affected > 0;
        } catch (Exception $e) {
            error_log('Error en Beneficio::actualizar(): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar un beneficio (eliminación real)
     */
    public function eliminar($id)
    {
        try {
            $sql = "DELETE FROM beneficios WHERE id = ?";
            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('i', $id);

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $affected = $stmt->affected_rows;
            $stmt->close();

            return $affected > 0;
        } catch (Exception $e) {
            error_log('Error en Beneficio::eliminar(): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar disponibilidad de un beneficio
     */
    public function cambiarDisponibilidad($id, $disponible)
    {
        try {
            $sql = "UPDATE beneficios SET disponible = ?, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param('ii', $disponible, $id);

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $affected = $stmt->affected_rows;
            $stmt->close();

            return $affected > 0;
        } catch (Exception $e) {
            error_log('Error en Beneficio::cambiarDisponibilidad(): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener beneficios por categoría
     */
    public function obtenerPorCategoria($categoria)
    {
        return $this->obtenerTodos(['categoria' => $categoria, 'disponible' => 1]);
    }

    /**
     * Obtener beneficios por plan de financiamiento
     */
    public function obtenerPorPlan($planId)
    {
        return $this->obtenerTodos(['plan_financiamiento_id' => $planId, 'disponible' => 1]);
    }

    /**
     * Buscar beneficios
     */
    public function buscar($termino, $categoria = null)
    {
        $filtros = ['busqueda' => $termino, 'disponible' => 1];
        if ($categoria) {
            $filtros['categoria'] = $categoria;
        }
        return $this->obtenerTodos($filtros);
    }

    /**
     * Obtener estadísticas generales
     */
    public function obtenerEstadisticas()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN disponible = 1 THEN 1 ELSE 0 END) as disponibles,
                        SUM(CASE WHEN categoria = 'llantas' THEN 1 ELSE 0 END) as llantas,
                        SUM(CASE WHEN categoria = 'baterias' THEN 1 ELSE 0 END) as baterias,
                        SUM(CASE WHEN categoria = 'aceites' THEN 1 ELSE 0 END) as aceites,
                        SUM(CASE WHEN categoria = 'celulares' THEN 1 ELSE 0 END) as celulares,
                        SUM(CASE WHEN categoria = 'vehiculos' THEN 1 ELSE 0 END) as vehiculos,
                        AVG(precio_contado) as precio_promedio,
                        MIN(precio_contado) as precio_minimo,
                        MAX(precio_contado) as precio_maximo
                    FROM beneficios";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $estadisticas = $result->fetch_assoc();
            $stmt->close();

            return $estadisticas;
        } catch (Exception $e) {
            error_log('Error en Beneficio::obtenerEstadisticas(): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener categorías disponibles
     */
public function obtenerCategorias()
{
    try {
        $sql = "SELECT cp.idcategoria_producto, cp.nombre, 
                       COUNT(b.id) as total_productos
                FROM categoria_producto cp
                LEFT JOIN beneficios b ON b.categoria = cp.idcategoria_producto
                                        AND b.disponible = 1
                GROUP BY cp.idcategoria_producto, cp.nombre
                ORDER BY cp.nombre";

        $stmt = $this->conectar->prepare($sql);

        if (!$stmt) {
            throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
        }

        if (!$stmt->execute()) {
            throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $categorias = [];

        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row;
        }

        $stmt->close();
        return $categorias;
    } catch (Exception $e) {
        error_log('Error en Beneficio::obtenerCategorias(): ' . $e->getMessage());
        return [];
    }
}


    /**
     * Verificar si existe un código de producto
     */
    public function existeCodigoProducto($codigo, $excluirId = null)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM beneficios WHERE codigo_producto = ?";
            $params = [$codigo];
            $types = "s";

            if ($excluirId) {
                $sql .= " AND id != ?";
                $params[] = $excluirId;
                $types .= "i";
            }

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            $stmt->bind_param($types, ...$params);

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            return $row['total'] > 0;
        } catch (Exception $e) {
            error_log('Error en Beneficio::existeCodigoProducto(): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener departamentos habilitados para el registro de beneficios
     */
    public function obtenerDepartamentosHabilitados()
    {
        try {
            $sql = "SELECT
                        d.iddepast,
                        d.nombre
                    FROM depast d
                    INNER JOIN departamentos_habilitados dh ON d.iddepast = dh.iddepast
                    WHERE dh.habilitado = 1
                    ORDER BY d.nombre ASC";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $departamentos = [];

            while ($row = $result->fetch_assoc()) {
                $departamentos[] = $row;
            }

            $stmt->close();
            return $departamentos;
        } catch (Exception $e) {
            error_log('Error en Beneficio::obtenerDepartamentosHabilitados(): ' . $e->getMessage());
            return [];
        }
    }

}