<?php

class Beneficio
{
    private $id;
    private $nombre;
    private $categoria;
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
            if (!isset($datos['categoria']) || $datos['categoria'] <= 0) {
                throw new Exception('La categoría es obligatoria');
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
                        nombre, categoria, descripcion, cuota_inicial, cantidad_cuotas, cuota_mensual,
                        imagen, disponible
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            // Preparar datos básicos
            $nombre = $datos['nombre'];
            $categoria = $datos['categoria'];
            $descripcion = isset($datos['descripcion']) && $datos['descripcion'] !== null ? $datos['descripcion'] : '';
            $cuota_inicial = $datos['cuota_inicial'];
            $cantidad_cuotas = $datos['cantidad_cuotas'];
            $cuota_mensual = $datos['cuota_mensual'];
            $imagen = isset($datos['imagen']) && $datos['imagen'] !== null && $datos['imagen'] !== '' ? $datos['imagen'] : null;
            $disponible = $datos['disponible'];

            $stmt->bind_param(
                'sisdidsi',
                $nombre,           // s - string
                $categoria,        // i - integer
                $descripcion,      // s - string
                $cuota_inicial,    // d - decimal
                $cantidad_cuotas,  // i - integer
                $cuota_mensual,    // d - decimal
                $imagen,           // s - string
                $disponible        // i - integer
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
            $sql = "SELECT * FROM beneficios";
            $params = [];
            $types = "";

            // Aplicar filtros
            if (!empty($filtros['categoria'])) {
                $sql .= " AND categoria = ?";
                $params[] = $filtros['categoria'];
                $types .= "s";
            }

            if (!empty($filtros['disponible'])) {
                $sql .= " AND disponible = ?";
                $params[] = $filtros['disponible'];
                $types .= "i";
            }

            if (!empty($filtros['busqueda'])) {
                $sql .= " AND (nombre LIKE ? OR descripcion LIKE ? OR marca LIKE ? OR modelo LIKE ?)";
                $busqueda = '%' . $filtros['busqueda'] . '%';
                $params[] = $busqueda;
                $params[] = $busqueda;
                $params[] = $busqueda;
                $params[] = $busqueda;
                $types .= "ssss";
            }

            $sql .= " ORDER BY fecha_creacion DESC";

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
                // Procesar datos adicionales
                $row['especificaciones_array'] = !empty($row['especificaciones']) ? json_decode($row['especificaciones'], true) : [];
                $row['galeria_array'] = !empty($row['galeria_imagenes']) ? json_decode($row['galeria_imagenes'], true) : [];
                $row['cuotas_array'] = !empty($row['cuotas_disponibles']) ? explode(',', $row['cuotas_disponibles']) : [];
                
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

            if ($beneficio) {
                // Procesar datos adicionales
                $beneficio['especificaciones_array'] = !empty($beneficio['especificaciones']) ? json_decode($beneficio['especificaciones'], true) : [];
                $beneficio['galeria_array'] = !empty($beneficio['galeria_imagenes']) ? json_decode($beneficio['galeria_imagenes'], true) : [];
                $beneficio['cuotas_array'] = !empty($beneficio['cuotas_disponibles']) ? explode(',', $beneficio['cuotas_disponibles']) : [];
            }

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
                        nombre = ?, categoria = ?, descripcion = ?, cuota_inicial = ?,
                        cantidad_cuotas = ?, cuota_mensual = ?, imagen = ?,
                        disponible = ?, fecha_actualizacion = CURRENT_TIMESTAMP
                    WHERE id = ?";

            $stmt = $this->conectar->prepare($sql);

            if (!$stmt) {
                throw new Exception('Error al preparar la consulta: ' . $this->conectar->error);
            }

            // Preparar datos básicos
            $nombre = $datos['nombre'];
            $categoria = $datos['categoria'];
            $descripcion = isset($datos['descripcion']) && $datos['descripcion'] !== null ? $datos['descripcion'] : '';
            $cuota_inicial = $datos['cuota_inicial'];
            $cantidad_cuotas = $datos['cantidad_cuotas'];
            $cuota_mensual = $datos['cuota_mensual'];
            $imagen = isset($datos['imagen']) && $datos['imagen'] !== null && $datos['imagen'] !== '' ? $datos['imagen'] : null;
            $disponible = $datos['disponible'];

            $stmt->bind_param(
                'sisdidsii',
                $nombre,           // s - string
                $categoria,        // i - integer
                $descripcion,      // s - string
                $cuota_inicial,    // d - decimal
                $cantidad_cuotas,  // i - integer
                $cuota_mensual,    // d - decimal
                $imagen,           // s - string
                $disponible,       // i - integer
                $id                // i - integer
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
    
}