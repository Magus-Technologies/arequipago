<?php
require_once "app/models/CaracteristicaProducto.php";
require_once "app/http/controllers/ProductosController.php";

class Productov2
{
    private $idproductosv2;
    private $nombre;
    private $codigo;
    private $cantidad;
    private $categoria;
    private $ruc;
    private $razon_social;
    private $fecha_vencimiento; // Nueva propiedad
    private $tipo_producto; // Nueva propiedad
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function obtenerTodos()
    {
        try {
            // Asegúrate de que se seleccionen todos los campos, incluyendo tipo_producto y fecha_vencimiento
            $sql = "SELECT idproductosv2, nombre, codigo, cantidad, categoria, ruc, razon_social, fecha_vencimiento, tipo_producto, estado 
                FROM productosv2 ORDER BY nombre ASC"; // Agregado "estado" en la consulta  
            $result = $this->conectar->query($sql);
            
            if (!$result) {
                throw new Exception("Error al obtener los productos");
            }
            
            $productos = [];
            
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
            
            return $productos;
        } catch (Exception $e) {
            error_log("Error en Productov2::obtenerTodos(): " . $e->getMessage());
            throw $e;
        }
    }

        
 public function insertar(
        $nombre, 
        $codigo, 
        $cantidad, 
        $categoria, 
        $ruc, 
        $razon_social, 
        $fecha_vencimiento = null, 
        $fecha_registro = null,
        $tipo_producto, 
        $cantidad_unidad = null, 
        $unidad_medida = null,
        $precio = null,
        $guia_remision = null,
        $codigo_barra = null,
        $precio_venta = null
    ) {
        try {
            // Si ambos est�n vac�os, dejarlos en NULL
        if (empty($codigo) && empty($codigo_barra)) {
            $codigo = null;
            $codigo_barra = null;
        } 
        // Si $codigo tiene un valor, $codigo_barra se vuelve NULL
        elseif (!empty($codigo)) {
            $codigo_barra = null;
        } 
        // Si $codigo est� vac�o pero $codigo_barra tiene valor, $codigo se vuelve NULL
        else {
            $codigo = null;
        }

        // ?? NUEVO: Convertir '' en NULL para fecha_vencimiento
        if ($fecha_vencimiento === '') { // ? L�nea agregada
            $fecha_vencimiento = null;    // ? L�nea agregada
        }

        // ?? NUEVO: Convertir '' en NULL para fecha_registro
        if ($fecha_registro === '') {    // ? L�nea agregada
            $fecha_registro = null;      // ? L�nea agregada
        }

         // ?? NUEVO: Convertir '' en NULL para cantidad_unidad
        if ($cantidad_unidad === '' || $cantidad_unidad === 'null') {   // ? L�nea agregada
            $cantidad_unidad = null;                                    // ? L�nea agregada
        }

        // ?? NUEVO: Convertir '' en NULL para precio
        if ($precio === '' || $precio === 'null') {     // ? L�nea agregada
            $precio = null;                             // ? L�nea agregada
        }

        // ?? NUEVO: Convertir '' en NULL para precio_venta
        if ($precio_venta === '' || $precio_venta === 'null') { // ? L�nea agregada
            $precio_venta = null;                                // ? L�nea agregada
        }

            // Modificaci�n en la consulta SQL: Se incluyen todos los campos
            $sql = "INSERT INTO productosv2 (nombre, codigo, cantidad, categoria, ruc, razon_social, fecha_vencimiento, tipo_producto, cantidad_unidad, unidad_medida, precio, fecha_registro, guia_remision, codigo_barra, precio_venta, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1')";
    
            // Preparar la consulta
            $stmt = $this->conectar->prepare($sql);
    
            // Enlazar par�metros en el orden correcto
            $stmt->bind_param(
                "ssissssssssssss",
                

                $nombre, 
                $codigo, 
                $cantidad, 
                $categoria, 
                $ruc, 
                $razon_social, 
                $fecha_vencimiento, 
                $tipo_producto, 
                $cantidad_unidad, 
                $unidad_medida,
                $precio,
                $fecha_registro,
                $guia_remision,
                $codigo_barra,
                $precio_venta
            );
    
            // Ejecutar la consulta
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar el producto: " . $stmt->error); // Mantener el manejo de errores
            }
    
            return $this->conectar->insert_id; 
    
        } catch (Exception $e) {
            error_log("Error en Producto::insertar(): " . $e->getMessage()); // Loguear el error
            throw $e; // Re-lanzar la excepci�n para manejo en el controlador
        }
    }  
 
        public function existeCodigoBarras($codigo)
        {
            $sql = "SELECT COUNT(*) as total FROM productosv2 WHERE codigo_barra = ?"; // OJO: "codigo_barra" en singular
        
            // Verificar la conexión
            if (!$this->conectar) {
                die("Error de conexión: " . $this->conectar->connect_error);
            }
        
            $stmt = $this->conectar->prepare($sql);
        
            if (!$stmt) {
                die("Error en la consulta SQL: " . $this->conectar->error); // Muestra el error exacto
            }
        
            $stmt->bind_param("s", $codigo);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row['total'] > 0;
        }              

    public function obtenerProductos($pagina = 1, $productosPorPagina = 5)
        {
            try {
                // Calcular el offset para la paginación
                $offset = ($pagina - 1) * $productosPorPagina;
                
                // Consulta SQL para obtener los productos y excluir los que tienen estado 0
                $sql = "SELECT idproductosv2, nombre, codigo, cantidad, unidad_medida, precio_venta
                FROM productosv2 
                WHERE categoria != 'Chip (Linea corporativa)' 
                AND estado != '0'  -- Modificado: Se excluyen productos con estado 0
                LIMIT $productosPorPagina OFFSET $offset";  // Se mantiene la paginación

                $result = $this->conectar->query($sql);

                if (!$result) {
                    throw new Exception("Error al obtener los productos");
                }

                // Almacenar los productos en un array
                $productos = [];
                while ($row = $result->fetch_assoc()) {
                    $productos[] = $row;
                }

                $caracteristicaModel = new CaracteristicaProducto();
                // Iterar sobre los productos para añadir las características "aro" y "perfil"
                foreach ($productos as &$producto) { // Usamos referencia (&) para modificar directamente el array
                    $caracteristicas = $caracteristicaModel->obtenerCaracteristicas($producto['idproductosv2']); // Obtener características por producto
                    //var_dump(['caracteristicas' => $caracteristicas]);

                    foreach ($caracteristicas as $caracteristica) {
                        if ($caracteristica['nombre_caracteristicas'] === 'aro') {
                            $producto['aro'] = $caracteristica['valor_caracteristica']; // Añadido: Asignar aro
                        }
                        if ($caracteristica['nombre_caracteristicas'] === 'perfil') {
                            $producto['perfil'] = $caracteristica['valor_caracteristica']; // Añadido: Asignar perfil
                        }
                    }
                }

                $sqlTotal = "SELECT COUNT(*) as total 
                     FROM productosv2 
                     WHERE categoria != 'Chip (Linea corporativa)'";

                $resultTotal = $this->conectar->query($sqlTotal);
                $total = $resultTotal->fetch_assoc()['total'];
                
                return [
                    'productos' => $productos,
                    'totalPages' => ceil($total / $productosPorPagina)
                ];
            } catch (Exception $e) {
                error_log("Error en ProductoV2::obtenerProductos(): " . $e->getMessage());
                throw $e;
            }
        }

    //Usado para el módulo Financiamiento y ventas.
    public function buscarProductosPorNombreOCodigo($searchTerm) {
        try {

            $productos = [];
        
            $sql = "SELECT idproductosv2, nombre, codigo, cantidad, unidad_medida, precio_venta, codigo_barra
                FROM productosv2
                WHERE (nombre LIKE ? OR codigo LIKE ? OR codigo_barra LIKE ?)
                AND estado != '0'";

            $stmt = $this->conectar->prepare($sql);
            $likeTerm = "%" . $searchTerm . "%";
            $stmt->bind_param("sss", $likeTerm, $likeTerm, $likeTerm);
    
            $stmt->execute();
            $result = $stmt->get_result();
    
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }

            // Nueva lógica para buscar por IMEI en la tabla caracteristicas_producto
            $sqlIMEI = "SELECT idproductosv2 FROM caracteristicas_producto 
            WHERE nombre_caracteristicas = 'Nro IMEI' 
            AND valor_caracteristica LIKE ?";// Buscar IMEI exacto

            $stmtIMEI = $this->conectar->prepare($sqlIMEI);
            $stmtIMEI->bind_param("s", $likeTerm); // Usar LIKE para búsqueda 
            $stmtIMEI->execute();
            $resultIMEI = $stmtIMEI->get_result();

            while ($rowIMEI = $resultIMEI->fetch_assoc()) {
            $idProducto = $rowIMEI['idproductosv2'];

            // Obtener el producto con el ID encontrado
            $sqlProducto = "SELECT idproductosv2, nombre, codigo, cantidad, unidad_medida, precio_venta, codigo_barra 
                            FROM productosv2 WHERE idproductosv2 = ?";

            $stmtProducto = $this->conectar->prepare($sqlProducto);
            $stmtProducto->bind_param("i", $idProducto);
            $stmtProducto->execute();
            $resultProducto = $stmtProducto->get_result();

            if ($rowProducto = $resultProducto->fetch_assoc()) {
                $productos[] = $rowProducto;
            }
            }

            $caracteristicaModel = new CaracteristicaProducto();
            // Iterar sobre los productos para añadir las características "aro" y "perfil"
            foreach ($productos as &$producto) { // Usamos referencia (&) para modificar directamente el array
                $caracteristicas = $caracteristicaModel->obtenerCaracteristicas($producto['idproductosv2']); // Obtener características por producto
                //var_dump(['caracteristicas' => $caracteristicas]);

                foreach ($caracteristicas as $caracteristica) {
                    if ($caracteristica['nombre_caracteristicas'] === 'aro') {
                        $producto['aro'] = $caracteristica['valor_caracteristica']; // Añadido: Asignar aro
                    }
                    if ($caracteristica['nombre_caracteristicas'] === 'perfil') {
                        $producto['perfil'] = $caracteristica['valor_caracteristica']; // Añadido: Asignar perfil
                    }
                }
            }
    
            return $productos;
        } catch (Exception $e) {
            error_log("Error en ProductoV2::buscarProductosPorNombreOCodigo(): " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerTipoProductoPorId($idProducto)
    {
        $query = "SELECT categoria FROM productosv2 WHERE idproductosv2 = ?";
        $stmt = $this->conectar->prepare($query);
        $stmt->bind_param("i", $idProducto);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['categoria'];
        }

        return null;
    }

    public function getPlanesMensuales() {  // Cambié el método a no ser estático para usar la conexión de la clase
        // Consulta SQL para obtener los productos con sus características
        $query = "
            SELECT p.idproductosv2, p.precio, 
                   MAX(CASE WHEN c.nombre_caracteristicas = 'operadora' THEN c.valor_caracteristica END) AS operadora,
                   MAX(CASE WHEN c.nombre_caracteristicas = 'plan_mensual' THEN c.valor_caracteristica END) AS plan_mensual
            FROM productosv2 p
            LEFT JOIN caracteristicas_producto c ON p.idproductosv2 = c.idproductosv2
            WHERE p.categoria = 'Chip (Linea corporativa)'
            GROUP BY p.idproductosv2, p.precio
        ";

        
        // Ejecutamos la consulta
        $result = $this->conectar->query($query);  // Usamos $this->conectar para la conexión

        

        


        // Verificamos si hay resultados
        $productos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $productos[] = [
                    'idproductosv2' => $row['idproductosv2'],
                    'precio' => $row['precio'],
                    'operadora' => $row['operadora'],
                    'plan_mensual' => $row['plan_mensual']
                ];
            }
        } 
    
        // Devolvemos los productos con sus características
        return $productos;
    }

    public function guardarProductosMasivos(array $productos)
    {
        try {
            $this->conectar->begin_transaction();
            
            $idsProductos = []; // Inicializar array para almacenar IDs
            
            foreach ($productos as $producto) {
                
                //var_dump("Producto recibido:", $producto);
                
                // Configurar valores predeterminados para campos opcionales
                $producto['nombre'] = $producto['nombre'] ?? '';
                $producto['codigo'] = $producto['codigo'] ?? null;
                $producto['cantidad'] = $producto['cantidad'] ?? 0;
                $producto['cantidad_unidad'] = $producto['cantidad_unidad'] ?? 0;
                $producto['unidad_medida'] = $producto['unidad_medida'] ?? '';
                $producto['tipo_producto'] = $producto['tipo_producto'] ?? '';
                $producto['categoria'] = $producto['categoria'] ?? '';
                $producto['precio_venta'] = $producto['precio_venta'] ?? 0.0;
                $producto['precio'] = $producto['precio'] ?? 0.0;
                $producto['estado'] = 1;
                
                if (!empty($producto['fecha_vencimiento'])) {
                    $baseDate = new DateTime('1900-01-01');
                    $baseDate->modify("+{$producto['fecha_vencimiento']} days");
                    $producto['fecha_vencimiento'] = $baseDate->format('Y-m-d');
                } else {
                    $producto['fecha_vencimiento'] = null;
                }
                
                $producto['ruc'] = $producto['ruc'] ?? '';
                $producto['razon_social'] = $producto['razon_social'] ?? '';
                
                if (!empty($producto['fecha_registro'])) {
                    $baseDate = new DateTime('1900-01-01');
                    $baseDate->modify("+{$producto['fecha_registro']} days");
                    $producto['fecha_registro'] = $baseDate->format('Y-m-d');
                } else {
                    $producto['fecha_registro'] = date('Y-m-d');
                }
                
                $producto['guia_remision'] = $producto['guia_remision'] ?? '';
                
                // Generar código de barras si no hay código
                if (is_null($producto['codigo'])) { // Si no hay código, generar código de barras
                    $producto['codigo_barra'] = $this->generarCodigoBarrasUnico();
                } else {
                    $producto['codigo_barra'] = null; // No se usa si hay código
                }
                
                //var_dump("Código de barras generado:", $producto['codigo_barra']);
                
                $stmt = $this->conectar->prepare(
                    "INSERT INTO productosv2 
                    (nombre, codigo, cantidad, cantidad_unidad, unidad_medida, tipo_producto, categoria, fecha_vencimiento, ruc, razon_social, precio_venta, precio, fecha_registro, guia_remision, codigo_barra, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                
                if (!$stmt) {
                    throw new \Exception("Error en la preparación de la consulta: " . $this->conectar->error);
                }
                
                //var_dump("Statement preparado con éxito");
                
                $stmt->bind_param(
                    'ssddssssssddssss',
                    $producto['nombre'],
                    $producto['codigo'],
                    $producto['cantidad'],
                    $producto['cantidad_unidad'],
                    $producto['unidad_medida'],
                    $producto['tipo_producto'],
                    $producto['categoria'],
                    $producto['fecha_vencimiento'],
                    $producto['ruc'],
                    $producto['razon_social'],
                    $producto['precio_venta'],
                    $producto['precio'],
                    $producto['fecha_registro'],
                    $producto['guia_remision'],
                    $producto['codigo_barra'],
                    $producto['estado']
                );
                
                if (!$stmt->execute()) {
                    //var_dump("Error en execute():", $stmt->error);
                    $this->conectar->rollback();
                    throw new \Exception("Error al insertar producto: " . $stmt->error);
                }
                
                // Usar código si existe, de lo contrario, código de barras
                $clave = !empty($producto['codigo']) ? $producto['codigo'] : $producto['codigo_barra']; // Cambio aquí
                $idsProductos[$clave] = $stmt->insert_id; // Guardar ID asociado a clave
                
                //var_dump("Producto insertado:", $clave, "ID:", $stmt->insert_id);
            }
            
            $this->conectar->commit();
            return $idsProductos;
            
        } catch (\Exception $e) {
            $this->conectar->rollback();
            //var_dump("Error capturado:", $e->getMessage());
            return false;
        }
    }

    private function generarCodigoBarrasUnico()
    {
        do {
            $codigo = rand(100000000000, 999999999999); // Generar un número aleatorio de 12 dígitos
            $productoModel = new Productov2();
        } while ($productoModel->existeCodigoBarras($codigo)); // Verificar si el código ya existe en la BD
        
        return $codigo;
    }
    

    public function reporteProducts()
    {
        // Preparar la consulta SQL para obtener todos los datos de la tabla productosv2
        $sql = "SELECT 
                    idproductosv2, 
                    nombre, 
                    codigo, 
                    cantidad, 
                    cantidad_unidad, 
                    unidad_medida, 
                    tipo_producto, 
                    categoria, 
                    fecha_vencimiento, 
                    ruc, 
                    razon_social, 
                    precio, 
                    precio_venta,
                    fecha_registro, 
                    guia_remision
                FROM productosv2
                WHERE estado != '0'";

        $resultado = $this->conectar->query($sql);

        // Verificar si la consulta fue exitosa
        if ($resultado === false) {
            throw new Exception("Error al ejecutar la consulta: " . $this->conectar->error);
        }

        // Arreglo para almacenar los productos
        $productos = [];

        // Recorrer los resultados y calcular el precio total
        while ($fila = $resultado->fetch_assoc()) {
            $fila['precio_total'] = $fila['precio'] * $fila['cantidad']; // Calcular el precio total
            $fila['texto_cabecera'] = ''; // Dejar "Texto de Cabecera" vacío
            $productos[] = $fila; // Agregar al arreglo de productos
        }

        // Liberar el resultado
        $resultado->free();

        // Devolver los datos
        return $productos;
    }

    public function buscarPorTermino($termino)
    {
        try {
            $sql = "SELECT * FROM productosv2 
                    WHERE nombre LIKE ? 
                    OR codigo LIKE ? 
                    OR razon_social LIKE ? 
                    OR categoria LIKE ? 
                    OR tipo_producto LIKE ?
                    OR codigo_barra LIKE ?";

            $stmt = $this->conectar->prepare($sql);
            if (!$stmt) {
                //var_dump("Error en prepare(): " . $this->conectar->error); // Cambio de error_log() a var_dump()
                return [];
            }
            $param = "%{$termino}%";
            $stmt->bind_param("ssssss", $param, $param, $param, $param, $param, $param); // Cambiado a 6 parámetros debido al nuevo campo codigo_ba
            $stmt->execute();
            
            $result = $stmt->get_result();

            if (!$result) {
                //var_dump("Error en execute(): " . $stmt->error); // Cambio de error_log() a var_dump()
                return [];
            }

            $productos = $result->fetch_all(MYSQLI_ASSOC);

            //var_dump("Productos encontrados:", $productos); 
            return $productos;
        } catch (Exception $e) {
            //var_dump("Excepción: " . $e->getMessage());
            return [];
        }
    }

    public function getProductsList($id_producto) {
        $sql = "SELECT * FROM productosv2 WHERE idproductosv2 = ?"; 
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param('i', $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Retorna un solo producto
    }

    public function getCodeBar($id_producto)
    {
        $sql = "SELECT codigo_barra FROM productosv2 WHERE idproductosv2 = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['codigo_barra']; // Devuelve el código de barras si existe
        }
        
        return null; // Devuelve null si no se encuentra
    }

    public function getdataForBarcode($codigo)
    {
        $codigo = trim((string) $codigo); // Convertimos a string y eliminamos espacios en blanco
        $codigo = str_replace("Código: ", "", $codigo); // Eliminamos el prefijo "Código: "
    
        if (empty($codigo)) { 
            return null;
        }
    
        $sql = "SELECT * FROM productosv2 WHERE BINARY codigo = ? OR BINARY codigo_barra = ?";
        $stmt = $this->conectar->prepare($sql);
    
        if (!$stmt) { 
            return null;
        }
    
        $stmt->bind_param("ss", $codigo, $codigo);
    
        if (!$stmt->execute()) { 
            return null;
        }
    
        $result = $stmt->get_result();
    
        if (!$result) { 
            return null;
        }
    
        return $result->fetch_assoc();
    }

    public function eliminarProducts($ids) {
        if (empty($ids)) {
            return false;
        }
        
        // Escapar los valores y convertirlos en una cadena separada por comas
        $ids = array_map('intval', $ids);
        $idsString = implode(',', $ids);
        
        // Consulta para actualizar el estado de los productos a 0
        $sql = "UPDATE productosv2 SET estado = '0' WHERE idproductosv2 IN ($idsString)";
        
        $query = $this->conectar->query($sql);
        
        return $query ? true : false;
    }

    public function obtenerProductoPorId($id_producto)
    {
        $sql = "SELECT 
                    idproductosv2 AS ID_PRODUCTO,
                    nombre AS NOMBRE,
                    codigo AS CODIGO,
                    cantidad AS CANTIDAD,
                    cantidad_unidad AS CANTIDAD_UNIDAD,
                    unidad_medida AS UNIDAD_MEDIDA,
                    tipo_producto AS TIPO_PRODUCTO,
                    categoria AS CATEGORIA,
                    fecha_vencimiento AS FECHA_VENCIMIENTO,
                    ruc AS RUC,
                    razon_social AS RAZON_SOCIAL,
                    precio AS PRECIO,
                    precio_venta AS PRECIO_VENTA,
                    fecha_registro AS FECHA_REGISTRO,
                    guia_remision AS GUIA_REMISION,
                    codigo_barra AS CODIGO_BARRA,
                    estado AS ESTADO
                FROM productosv2
                WHERE idproductosv2 = ?";

        $stmt = $this->conectar->prepare($sql); // Preparar la consulta
        $stmt->bind_param("i", $id_producto); // Enlazar el parámetro
        $stmt->execute(); // Ejecutar la consulta
        $resultado = $stmt->get_result(); // Obtener el resultado

        if ($resultado->num_rows > 0) {
            return $resultado->fetch_assoc(); // Devolver el registro como array asociativo
        } else {
            return null; // Retornar null si no se encuentra el producto
        }
    }

    public function actualizarStock($idProducto, $cantidadReducir)
    {
        // Obtener la cantidad actual del producto
        $sqlSelect = "SELECT cantidad FROM productosv2 WHERE idproductosv2 = ?"; // 🔹 Consulta para obtener la cantidad actual del producto
        $stmtSelect = $this->conectar->prepare($sqlSelect);
        $stmtSelect->bind_param("i", $idProducto);
        $stmtSelect->execute();
        $resultado = $stmtSelect->get_result();
        
        if ($resultado->num_rows === 0) {
            throw new Exception("Producto no encontrado."); // 🔹 Si el producto no existe, lanzamos una excepción
        }

        $fila = $resultado->fetch_assoc();
        $cantidadActual = intval($fila['cantidad']);

        // Calcular la nueva cantidad
        $nuevaCantidad = $cantidadActual - intval($cantidadReducir); // 🔹 Restamos la cantidad enviada

        if ($nuevaCantidad < 0) {
            throw new Exception("Stock insuficiente. No se puede reducir más de lo disponible."); // 🔹 Validamos que el stock no sea negativo
        }

        // Actualizar la cantidad en la base de datos
        $sqlUpdate = "UPDATE productosv2 SET cantidad = ? WHERE idproductosv2 = ?"; // 🔹 Consulta para actualizar el stock
        $stmtUpdate = $this->conectar->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ii", $nuevaCantidad, $idProducto);
        $stmtUpdate->execute();
    }
    public function actualizar(array $producto)
    {
        try {
            // Iniciar una transacción
            $this->conectar->begin_transaction();
            
            $sql = "UPDATE productosv2 SET 
                    nombre = ?, 
                    codigo = ?, 
                    cantidad = ?, 
                    cantidad_unidad = ?, 
                    unidad_medida = ?, 
                    tipo_producto = ?, 
                    categoria = ?, 
                    fecha_vencimiento = ?, 
                    ruc = ?, 
                    razon_social = ?, 
                    precio = ?, 
                    precio_venta = ?,
                    fecha_registro = ?, 
                    guia_remision = ?, 
                    codigo_barra = ? 
                    WHERE idproductosv2 = ?";
            
            $stmt = $this->conectar->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error en prepare: " . $this->conectar->error);
            }
            
            // Debug: Imprimir los valores antes de bind_param
            error_log("Valores a actualizar: " . print_r($producto, true));
            
            $stmt->bind_param(
                'ssidssssssddsssi',
                $producto['nombre'],
                $producto['codigo'],
                $producto['cantidad'],
                $producto['cantidad_unidad'],
                $producto['unidad_medida'],
                $producto['tipo_producto'],
                $producto['categoria'],
                $producto['fecha_vencimiento'],
                $producto['ruc'],
                $producto['razon_social'],
                $producto['precio'],
                $producto['precio_venta'],
                $producto['fecha_registro'],
                $producto['guia_remision'],
                $producto['codigo_barra'],
                $producto['idproductosv2']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Error en execute: " . $stmt->error);
            }
            
            // Si no hubo errores, confirmar la transacción
            $this->conectar->commit();
            return true;
            
        } catch (Exception $e) {
            // En caso de error, revertir la transacción
            $this->conectar->rollback();
            
            // Registrar el error detallado
            error_log("Error en Productov2::actualizar(): " . $e->getMessage());
            error_log("SQL State: " . $stmt->sqlstate ?? 'N/A');
            error_log("Error No: " . $stmt->errno ?? 'N/A');
            error_log("Error: " . $stmt->error ?? 'N/A');
            
            throw $e; // Relanzar la excepción para manejarla en el controlador
        }
    }
    public function obtenerProductoDetallado($idProducto)
    {
        try {
            // Primero obtenemos los datos básicos del producto
            $sqlProducto = "SELECT * FROM productosv2 WHERE idproductosv2 = ?";
            
            $stmtProducto = $this->conectar->prepare($sqlProducto);
            
            if (!$stmtProducto) {
                throw new Exception("Error en prepare: " . $this->conectar->error);
            }
            
            $stmtProducto->bind_param("i", $idProducto);
            
            if (!$stmtProducto->execute()) {
                throw new Exception("Error en execute: " . $stmtProducto->error);
            }
            
            $resultProducto = $stmtProducto->get_result();
            
            if ($resultProducto->num_rows === 0) {
                return null;
            }
            
            $producto = $resultProducto->fetch_assoc();

            // MODIFICACIÓN: VERIFICAR SI ES CELULAR PARA USAR TABLA ESPECIAL
            $categoria = strtolower(trim($producto['categoria']));
            $esCelular = (strpos($categoria, 'celular') !== false || $categoria === 'celulares');

        
            if ($esCelular) {
                // CONSULTAR DATOS DE TABLA CELULARES
                $sqlCelular = "SELECT * FROM celulares WHERE idproductosv2 = ?";
                $stmtCelular = $this->conectar->prepare($sqlCelular);
                
                if (!$stmtCelular) {
                    var_dump("Error preparando consulta de celulares: " . $this->conectar->error);
                } else {
                    $stmtCelular->bind_param("i", $idProducto);
                    
                    if (!$stmtCelular->execute()) {
                        var_dump("Error ejecutando consulta de celulares: " . $stmtCelular->error);
                    } else {
                        $resultCelular = $stmtCelular->get_result();
                        
                        if ($resultCelular->num_rows > 0) {
                            $datosCelular = $resultCelular->fetch_assoc();
                            
                            // Agregar datos del celular al producto
                            $producto['chip_linea'] = $datosCelular['chip_linea'];
                            $producto['marca_equipo'] = $datosCelular['marca'];
                            $producto['modelo'] = $datosCelular['modelo'];
                            $producto['nro_imei'] = $datosCelular['imei'];
                            $producto['nro_serie'] = $datosCelular['imei2'];
                            $producto['colorc'] = $datosCelular['color'];
                            $producto['cargador'] = $datosCelular['cargador'];
                            $producto['cable_usb'] = $datosCelular['cable_usb'];
                            $producto['manual_usuario'] = $datosCelular['manual_usuario'];
                            $producto['estuche'] = $datosCelular['estuche'];
                        }
                        
                        $stmtCelular->close();
                    }
                }
                
                return $producto;
            }
            
            // Ahora obtenemos las características del producto
            $sqlCaracteristicas = "SELECT nombre_caracteristicas, valor_caracteristica 
                                FROM caracteristicas_producto 
                                WHERE idproductosv2 = ?";
            
            $stmtCaracteristicas = $this->conectar->prepare($sqlCaracteristicas);
            
            if (!$stmtCaracteristicas) {
                // Si hay error, devolvemos solo los datos básicos
                return $producto;
            }
            
            $stmtCaracteristicas->bind_param("i", $idProducto);
            
            if (!$stmtCaracteristicas->execute()) {
                // Si hay error, devolvemos solo los datos básicos
                return $producto;
            }
            
            $resultCaracteristicas = $stmtCaracteristicas->get_result();
            
            // MODIFICACIÓN: VERIFICAR SI ES VEHÍCULO PARA TRATAR DIFERENTE
            $esVehiculo = (strpos(strtolower(trim($producto['categoria'])), 'vehiculo') !== false || 
            strpos(strtolower(trim($producto['categoria'])), 'vehículo') !== false);

            if ($esVehiculo) {
                // AGREGAR CARACTERÍSTICAS COMO ARRAY PARA VEHÍCULOS
                $producto['caracteristicas'] = [];
                while ($caracteristica = $resultCaracteristicas->fetch_assoc()) {
                    $producto['caracteristicas'][] = [
                        'nombre' => $caracteristica['nombre_caracteristicas'],
                        'valor' => $caracteristica['valor_caracteristica']
                    ];
                }
            } else {
                // AGREGAR CARACTERÍSTICAS COMO PROPIEDADES PARA OTROS PRODUCTOS
                $producto['caracteristicas'] = [];
                while ($caracteristica = $resultCaracteristicas->fetch_assoc()) {
                    $nombreCaracteristica = $caracteristica['nombre_caracteristicas'];
                    $valorCaracteristica = $caracteristica['valor_caracteristica'];
                    
                    // Añadimos la característica al array de características
                    $producto['caracteristicas'][] = [
                        'nombre' => $nombreCaracteristica,
                        'valor' => $valorCaracteristica
                    ];
                    
                    // También añadimos la característica como propiedad individual
                    // Convertimos el nombre a un formato adecuado para la clave
                    $nombreClave = strtolower(str_replace(' ', '_', $nombreCaracteristica));
                    $producto[$nombreClave] = $valorCaracteristica;
                }
            }

            $stmtCaracteristicas->close();
            return $producto;
            
        } catch (Exception $e) {
            error_log("Error en obtenerProductoDetallado: " . $e->getMessage());
            throw $e;
        }
    }
    
    
}