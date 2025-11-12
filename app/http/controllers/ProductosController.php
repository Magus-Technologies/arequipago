<?php

require_once 'utils/lib/vendor/autoload.php';
require_once 'utils/lib/exel/vendor/autoload.php';
require_once 'utils/lib/code/vendor/autoload.php'; // 🔄 Ajustado para la ubicación correcta de Picqer


require_once 'app/models/TipoProductoModel.php';

// require_once "app/models/Producto.php";

require_once "app/models/Productov2.php";

require_once "app/models/CategoriaProductoModel.php";

require_once "app/models/CaracteristicaProducto.php";

require_once "app/models/Reportes.php";

require_once "app/models/Celular.php";

use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductosController extends Controller
{
    private $conexion;
    private $c_producto;
    private $tipoProductoModel;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();

    }

    public function getTiposProducto() {
        $tipos = new TipoProductoModel();
        $tipos = $tipos->obtenerTiposProducto();
        return json_encode($tipos);
    }

    public function guardarTipoProducto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipoProducto = isset($_POST['tipo_producto']) ? trim($_POST['tipo_producto']) : '';
            $tipoVenta = isset($_POST['tipo_venta']) ? trim($_POST['tipo_venta']) : '';
    
            if (empty($tipoProducto)) {
                echo json_encode(['success' => false, 'message' => 'El campo tipo de producto está vacío.']);
                return;
            }

            if (empty($tipoVenta) || !in_array($tipoVenta, ['volumen', 'unidad'])) {
                echo json_encode(['success' => false, 'message' => 'El tipo de venta es inválido.']);
                return;
            }
    
            $model = new TipoProductoModel();

            $model->setTipoVenta($tipoVenta);

            $resultado = $model->guardarTipoProducto($tipoProducto);
    
            if ($resultado) {
                // Obtener el ID del nuevo tipo de producto
                $nuevoId = $model->getUltimoIdInsertado();
                echo json_encode([
                    'success' => true, 
                    'nuevoTipoProducto' => [
                        'idtipo_producto' => $nuevoId,
                        'tipo_productocol' => $tipoProducto,
                        'tipo_venta' => $tipoVenta
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo guardar el tipo de producto.']);
            }
        }
    }

    public function guardarCategoriaProducto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaProducto = isset($_POST['categoria_producto']) ? trim($_POST['categoria_producto']) : '';
    
            if (empty($categoriaProducto)) {
                echo json_encode(['success' => false, 'message' => 'El campo categoría de producto está vacío.']);
                return;
            }
    
            $model = new CategoriaProductoModel(); // Instanciar el modelo
    
            $resultado = $model->guardarCategoriaProducto($categoriaProducto); // Guardar la categoría
    
            if ($resultado) {
                // Obtener el ID de la nueva categoría
                $nuevoId = $model->getUltimoIdInsertado();
                echo json_encode([
                    'success' => true,
                    'nuevaCategoriaProducto' => [
                        'idcategoria_producto' => $nuevoId,
                        'nombre' => $categoriaProducto
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo guardar la categoría de producto.']);
            }
        }
    }

    public function getCategoriasProducto() {
        $categoriaProducto = new CategoriaProductoModel();
        $categorias = $categoriaProducto->obtenerCategoriasProducto();
        echo json_encode($categorias);
    }
    
    public function obtenerTipoProducto() {
        // Recibir el tipo de producto desde la solicitud GET
        $tipoProducto = $_GET['tipoProducto'] ?? '';
    
        // Si el tipo de producto está vacío, devolver un error
        if (empty($tipoProducto)) {
            echo json_encode(['error' => 'Tipo de producto no especificado']);
            return;
        }
    
        // Crear una instancia del modelo TipoProductoModel
        $tipoProductoModel = new TipoProductoModel();
    
        // Obtener el tipo de venta basado en el tipo_productocol
        $tipoVenta = $tipoProductoModel->obtenerTipoVentaPorTipoProducto($tipoProducto);
    
        // Devolver la respuesta en formato JSON
        if ($tipoVenta) {
            echo json_encode(['tipo_venta' => $tipoVenta]);
        } else {
            echo json_encode(['error' => 'Tipo de producto no encontrado']);
        }
    }
    


    public function obtenerTodosProductos()
    {
        try {
            $productov2 = new Productov2();
            $productos = $productov2->obtenerTodos();

            // Filtrar productos para excluir aquellos con estado 0  
            $productos = array_filter($productos, function ($producto) {
                return $producto['estado'] != '0'; // Si el estado es 0, no se incluirá en la respuesta  
            });

            echo json_encode(array_values($productos)); 
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function guardarProducto()
    {
        // Obtener los datos del POST
        $nombre = $_POST['nombre_producto'] ?? '';
        $tipo_producto = $_POST['tipo_producto'] ?? ''; // Obtener tipo_producto
        $codigo = $_POST['codigo_producto'] ?? null;
        $cantidad = $_POST['cantidad_producto'] ?? 0;
        $categoria = $_POST['categoria_producto'] ?? '';
        $ruc = $_POST['ruc'] ?? '';
        $razon_social = $_POST['razonsocial'] ?? '';
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null; // Obtener fecha_vencimiento
        // Nuevos campos
        $cantidad_unidad = $_POST['cantidad_unidad'] ?? null;
        $unidad_medida = $_POST['unidad_medida'] ?? null;
        $precio = $_POST['precio'] ?? null; // Nuevo campo: precio
        $fecha_registro = $_POST['fecha_registro'] ?? null; // Obtener fecha_registro desde el formulario
        $guia_remision = $_POST['guia_remision']?? null;
        $precio_venta = $_POST['precio_venta']?? null;
        $moneda = $_POST['moneda'] ?? 'S/.';
        $descuento_cuota = $_POST['descuento_cuota'] ?? null;

        $aro = $_POST['aro'] ?? null; // Nuevo campo: aro
        $perfil = $_POST['perfil'] ?? null; // Nuevo campo: perfil

        // Campos opcionales: marca y modelo (pueden venir de producto general o de vehículo)
        $marca = $_POST['marca_producto'] ?? $_POST['marca_vehiculo'] ?? null;
        $modelo = $_POST['modelo_producto'] ?? $_POST['modelo_vehiculo'] ?? null;

        // Campos específicos para vehículos (opcionales)
        $vin = $_POST['vin_vehiculo'] ?? $_POST['vin'] ?? null;
        $chasis = $_POST['chasis_vehiculo'] ?? $_POST['chasis'] ?? null;
        $color = $_POST['color_vehiculo'] ?? $_POST['color'] ?? null;
        $anio = $_POST['anio_vehiculo'] ?? $_POST['anio'] ?? null;
        $placa = $_POST['placa_vehiculo'] ?? null;
        $transmision = $_POST['transmision_vehiculo'] ?? null;

        // Generar código de barras automáticamente si no se proporciona uno (Nuevo cambio)
        $codigo_barra = null;
        if (empty($codigo)) { 
            $codigo_barra = $this->generarCodigoBarrasUnico();
        }

        // Validar que no esté vacío
        if (empty($nombre) || empty($cantidad) || empty($categoria) || empty($ruc) || empty($razon_social) || empty($tipo_producto)) {
            echo json_encode(['status' => 'error', 'message' => 'Todos los campos obligatorios deben estar completos.']);
            return;
        }

        // Crear instancia del modelo
        $productoModel = new Productov2();

        // Insertar datos en la base de datos
    
        $productoData = [
            'nombre' => $nombre,
            'marca' => $marca,
            'modelo' => $modelo,
            'tipo_producto' => $tipo_producto,
            'codigo' => $codigo,
            'cantidad_unidad' => $cantidad_unidad,
            'unidad_medida' => $unidad_medida,
            'cantidad' => $cantidad,
            'categoria' => $categoria,
            'ruc' => $ruc,
            'razon_social' => $razon_social,
            'precio' => $precio,
            'fecha_vencimiento' => $fecha_vencimiento,
            'fecha_registro' => $fecha_registro,
            'guia_remision' => $guia_remision,
            'codigo_barra' => $codigo_barra, // Código de barras generado si no se proporcionó uno
            'precio_venta' => $precio_venta,
            'moneda' => $moneda,
            'descuento_cuota' => $descuento_cuota,
        ];

        // Ajustar para pasar datos en el orden correcto
        $idProducto = $productoModel->insertar( // Cambiado para capturar el ID del producto insertado
            $productoData['nombre'],
            $productoData['marca'],
            $productoData['modelo'],
            $productoData['codigo'],
            $productoData['cantidad'],
            $productoData['categoria'],
            $productoData['ruc'],
            $productoData['razon_social'],
            $productoData['fecha_vencimiento'],
            $productoData['fecha_registro'],
            $productoData['tipo_producto'],
            $productoData['cantidad_unidad'],
            $productoData['unidad_medida'],
            $productoData['precio'],
            $productoData['guia_remision'],
            $productoData['codigo_barra'],
            $productoData['precio_venta'],
            $productoData['moneda'],
            $productoData['descuento_cuota'],
            $vin,
            $chasis,
            $color,
            $anio,
            $placa,
            $transmision
        );

        // MODIFICADO: Verificar si la categoría es "celular" o "celulares" (sin importar mayúsculas, tildes, espacios o plural)
        $categoriaLimpia = $this->normalizarTexto($categoria); // Función auxiliar para normalizar el texto
        $esCelular = ($categoriaLimpia === 'celular' || $categoriaLimpia === 'celulares');

        if ($esCelular) {
            // AÑADIDO: Si es un celular, usar el modelo Celular para guardar en la tabla específica
            $celularModel = new Celular();
            
            // Preparar los datos para el celular
            $celularData = [
                'idproductosv2' => $idProducto,
                'chip_linea' => $_POST['chip_linea'] ?? null,
                'marca' => $_POST['marca_equipo'] ?? null,
                'modelo' => $_POST['modelo'] ?? null,
                'imei' => $_POST['nro_imei'] ?? null,
                'imei2' => $_POST['nro_serie'] ?? null, // nro_serie del frontend corresponde a imei2 en la tabla
                'color' => $_POST['colorc'] ?? null,
                'cargador' => $_POST['cargador'] ?? null,
                'cable_usb' => $_POST['cable_usb'] ?? null,
                'manual_usuario' => $_POST['manual_usuario'] ?? null,
                'estuche' => $_POST['estuche'] ?? null
            ];
            
            // Guardar en la tabla celulares
            $celularModel->saveCelular($celularData);

        } else {
            // Procesar características adicionales según la categoría (comportamiento original)
            // Procesar características adicionales según la categoría
            $caracteristicas = [];

            if ($categoria === 'Llantas') {
                $aro = $_POST['aro'] ?? null;
                $perfil = $_POST['perfil'] ?? null;
                if ($aro) {
                    $caracteristicas[] = ['nombre_caracteristica' => 'aro', 'valor_caracteristica' => $aro];
                }
                if ($perfil) {
                    $caracteristicas[] = ['nombre_caracteristica' => 'perfil', 'valor_caracteristica' => $perfil];
                }
            } elseif ($categoria === 'Chip (Linea corporativa)') {
                $plan_mensual = $_POST['plan_mensual'] ?? null;
                $operator = $_POST['operator']?? null;
                if ($plan_mensual) {
                    $caracteristicas[] = ['nombre_caracteristica' => 'plan_mensual', 'valor_caracteristica' => $plan_mensual];
                }
                if ($operator){
                    $caracteristicas[] = ['nombre_caracteristica' => 'operadora', 'valor_caracteristica' => $operator];
                }
            }
             elseif (preg_match('/vehículo|vehiculos|vehiculos/i', $categoria)) { // Validar categoría 'Vehículo' (independiente de mayúsculas, tildes o plural)
                $fecha_venc_soat = $_POST['fecha_venc_soat'] ?? null;
                $fecha_venc_seguro = $_POST['fecha_venc_seguro'] ?? null;
                $chasis = $_POST['chasis'] ?? null;
                $vin = $_POST['vin'] ?? null;
                $color = $_POST['color'] ?? null; // Agregado: Color para categoría Vehículo
                $anio = $_POST['anio'] ?? null;
                $placa = $_POST['placa_vehiculo'] ?? null; // Agregado: Placa del vehículo
                $transmision = $_POST['transmision_vehiculo'] ?? null; // Agregado: Transmisión del vehículo

                if ($fecha_venc_soat) {
                    $caracteristicas[] = ['nombre_caracteristica' => 'fecha_venc_soat', 'valor_caracteristica' => $fecha_venc_soat];
                }
                if ($fecha_venc_seguro) {
                    $caracteristicas[] = ['nombre_caracteristica' => 'fecha_venc_seguro', 'valor_caracteristica' => $fecha_venc_seguro];
                }
                if ($chasis) {
                    $caracteristicas[] = ['nombre_caracteristica' => 'chasis', 'valor_caracteristica' => $chasis];
                }
                if ($vin) {
                    $caracteristicas[] = ['nombre_caracteristica' => 'vin', 'valor_caracteristica' => $vin];
                }
                if ($color) {
                    $caracteristicas[] = ['nombre_caracteristica' => 'color', 'valor_caracteristica' => $color]; // Agregado: Guardar color
                }
                if ($anio) {
                    $caracteristicas[] = ['nombre_caracteristica' => 'anio', 'valor_caracteristica' => $anio]; // Agregado: Guardar año
                }
                if ($placa) {
                    $caracteristicas[] = ['nombre_caracteristica' => 'placa', 'valor_caracteristica' => $placa]; // Agregado: Guardar placa
                }
                if ($transmision) {
                    $caracteristicas[] = ['nombre_caracteristica' => 'transmision', 'valor_caracteristica' => $transmision]; // Agregado: Guardar transmisión
                }
            }
        
            if (!empty($caracteristicas)) { // Cambiado: Validar si hay características para insertar
            
                $caracteristicaModel = new CaracteristicaProducto();
                foreach ($caracteristicas as $caracteristica) {
                    $caracteristica['idproductosv2'] = $idProducto; 
                    $caracteristicaModel->insertarCaracteristica($caracteristica);
                }
            }

        }   
        
        // Obtener usuario_id de la sesión
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id) {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo obtener el ID del usuario.']);
            return;
        }

        // Determinar qué código usar para el movimiento
        $codigo_producto = !empty($codigo) ? $codigo : $codigo_barra; // Se usa el código ingresado o el generado automáticamente

        // Registrar movimiento
        $reportesModel = new Reportes();
        $tipo_movimiento = 'Entrada';
        $subtipo_movimiento = 'Individual';

        
        
        $reportesModel->registrarMovimiento(
            $usuario_id,
            $idProducto, // ID del producto insertado
            $codigo_producto,
            $nombre, // Nombre del producto
            $tipo_movimiento,
            $subtipo_movimiento,
            $cantidad, // Cantidad de productos
            $razon_social // Proveedor (RUC)
        );

        if ($idProducto) { 
            // Enviar respuesta exitosa con tipo de contenido json
            header('Content-Type: application/json'); // Asegurarse de que el tipo de contenido es JSON
            echo json_encode(['status' => 'success', 'message' => 'Producto guardado exitosamente.']); 
            exit;// Enviar respuesta de éxito
        } else {
            header('Content-Type: application/json'); // Asegurarse de que el tipo de contenido es JSON
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar el producto.']); // Enviar respuesta de error
            exit;
        }

    }

    public function normalizarTexto($texto){
        // Convertir a minúsculas
        $texto = strtolower($texto);
                
        // Eliminar acentos
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $texto
        );

        // Eliminar espacios
        $texto = str_replace(' ', '', $texto);

        return $texto;
    }

    private function normalizarTextoActualizacion($texto) {
        // Convertir a minúsculas
        $texto = strtolower($texto);
        
        // Eliminar acentos
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $texto
        );
        
        // Eliminar espacios
        $texto = str_replace(' ', '', $texto);
        
        return $texto;
    }

    // Nueva función para generar un código de barras único sin generar imagen
    private function generarCodigoBarrasUnico()
    {
        do {
            $codigo = rand(100000000000, 999999999999); // Generar un número aleatorio de 12 dígitos
            $productoModel = new Productov2();
        } while ($productoModel->existeCodigoBarras($codigo)); // Verificar si el código ya existe en la BD
        
        return $codigo;
    }

    public function listaProducto()
    {
        $a_productos = $this->c_producto->obtenerProductos();

        // Se prepara un array con los datos de los productos
        $productos = [];
        while ($producto = $a_productos->fetch_assoc()) {
            $productos[] = [
                'id_producto'   => $producto['id_producto'],
                'nombre'        => $producto['nombre'],
                'cantidad'      => $producto['cantidad'],
                'razon_social'  => $producto['razon_social'],
                'ruc'           => $producto['ruc'],
                'codigo'        => $producto['codigo'],
                'tipo_producto' => $producto['tipo_producto'],
                'categoria'     => $producto['categoria'],
            ];
        }

        // Devolver los productos como un JSON
        echo json_encode($productos);
    }

    public function buscarProductos()
    {
        try {
            if (!isset($_GET['search']) || empty($_GET['search'])) {
                echo json_encode([]); // Si no hay término de búsqueda, devuelve un array vacío
                return;
            }

            $search = trim($_GET['search']);
            $productov2 = new Productov2();
            $productos = $productov2->buscarPorTermino($search); // Llamar al método del modelo
            
            // Filtrar productos para excluir aquellos con estado 0  
            $productos = array_filter($productos, function ($producto) {
                return $producto['estado'] == '1'; // Solo incluir productos con estado 1  
            });

            echo json_encode(array_values($productos)); // Devolver los productos encontrados
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function agregarPorLista()
    {
        $lista = json_decode($_POST['lista'], true);
        $respuesta = ["res" => false];
        foreach ($lista as $item) {
            $afect = $item['afecto'] ? '1' : '0';

            $descripcion = $item['descripcicon'];
            $codigoProd = $item['codigoProd'];

            $sqlProducto = "SELECT * FROM productos where codigo = '$codigoProd' ";
            $producto =  $this->conexion->query($sqlProducto)->fetch_assoc();
            if ($producto) {
                $updateProducto = "UPDATE productos set descripcion= '$descripcion',
                                            precio='{$item['precio']}',
                                            precio2='{$item['precio2']}',
                                            precio3='{$item['precio3']}',
                                            precio4='{$item['precio4']}',
                                            almacen='{$item['almacen']}',
                                            precio_unidad='{$item['precio_unidad']}',
                                            costo='{$item['costo']}',
                                            cantidad='{$item['cantidad']}',
                                            codsunat='{$item['codSunat']}'
                                    where 
                                    codigo='$codigoProd' ";
                $this->conexion->query($updateProducto);
                $respuesta["res"] = true;
            } else {
                $sql = "insert into productos set descripcion=?,
                precio='{$item['precio']}',
                precio2='{$item['precio2']}',
                precio3='{$item['precio3']}',
                precio4='{$item['precio4']}',
                almacen='{$item['almacen']}',
                precio_unidad='{$item['precio_unidad']}',
                costo='{$item['costo']}',
                cantidad='{$item['cantidad']}',
                iscbp='$afect',
                id_empresa='{$_SESSION['id_empresa']}',
                ultima_salida='1000-01-01',
                sucursal='{$_SESSION['sucursal']}',
                codsunat='{$item['codSunat']}',
                codigo=?";

                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param('ss', $descripcion, $codigoProd);
                /*   $stmt->bind_param('s', $codigoProd); */

                if ($stmt->execute()) {
                    $respuesta["res"] = true;
                }
            }
        }
        return json_encode($respuesta);
    }

    public function importarExel()
    {
        $respuesta = ["res" => false];
        $filename = $_FILES['file']['name'];

        $path_parts = pathinfo($filename, PATHINFO_EXTENSION);
        $newName = Tools::getToken(80);
        /* Location */
        $loc_ruta = "files/temp";
        if (!file_exists($loc_ruta)) {
            mkdir($loc_ruta, 0777, true);
        }
        $location = $loc_ruta . "/" . $newName . '.' . $path_parts;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
            $nombre_logo = $newName . "." . $path_parts;

            $respuesta["res"] = true;
            $type = $path_parts;

            // Selección del lector según el tipo de archivo
            if ($type == "xlsx") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } elseif ($type == "xls") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } elseif ($type == "csv") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            }

            // Lectura de datos del archivo
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load("files/temp/" . $nombre_logo);
            $data = $spreadsheet->getActiveSheet()->toArray();

            array_shift($data); // Esta línea elimina la primera fila (cabecera) del archivo Excel

            // Procesamiento de los datos
            $productos = [];
            foreach ($data as $row) {

                $fecha_vencimiento = $row[9];
                if ($fecha_vencimiento) {
                    // Convertir la fecha a formato YYYY-MM-DD si es necesario
                    // Excel almacena las fechas como números (ej. 41609), así que convertimos a una fecha válida
                    if (is_numeric($fecha_vencimiento)) {
                        // Convertir el número de Excel a una fecha en formato 'Y-m-d'
                        $fecha_vencimiento = date('Y-m-d', ($fecha_vencimiento - 25569) * 86400); // Excel fecha base: 1900-01-01
                    }
                }

                error_log("Fecha de vencimiento convertida: " . $fecha_vencimiento); // Esto 
                
                $productos[] = [
                    'nombre' => $row[0],           // Nombre del producto
                    'codigo' => $row[1],           // Código del producto
                    'cantidad' => $row[2],         // Cantidad
                    'cantidad_unidad' => $row[3],  // Cantidad por unidad
                    'unidad_medida' => $row[4],    // Unidad de medida
                    'tipo_producto' => $row[5],    // Tipo de producto
                    'perfil' => $row[6],        // Categoría
                    'aro' => $row[7], // Fecha de vencimiento
                    'categoria' => $row[8],              // RUC
                    'fecha_vencimiento' => $fecha_vencimiento,      // Razón social
                    'ruc' => $row[10] ?? null,  // Perfil (nuevo campo, puede ser null)
                    'razon_social' => $row[11] ?? null,
                    'precio' => $row[12] ?? null
                ];
            }

            // Llamar a la función para insertar los productos en la base de datos
            $this->insertarProductos($productos);

            // Eliminar archivo temporal
            unlink($location);

            $respuesta["data"] = $productos; // Respuesta con los productos procesados
        }

        return json_encode($respuesta);
    }

    // Función para insertar productos en la base de datos
    private function insertarProductos($productos)
    {
        // Crear una instancia del modelo Productov2
        $productoModel = new Productov2();

        // Insertar cada producto en la base de datos
        foreach ($productos as $producto) {
            // Aquí pasamos los datos a la función insertar del modelo
            $productoModel->insertar(
                $producto['nombre'],
                $producto['codigo'],
                $producto['cantidad'],
                $producto['categoria'],
                $producto['ruc'],
                $producto['razon_social'],
                $producto['fecha_vencimiento'],
                $producto['tipo_producto'],
                $producto['cantidad_unidad'],
                $producto['unidad_medida'],
                $producto['perfil'],  // Nuevo campo
                $producto['aro'],      // Nuevo campo
                $producto['precio']
            );
        }
    }

    public function restock()
    {
        $respuesta = ["res" => false];
        $sql = "update productos set cantidad=cantidad+{$_POST['cantidad']} where id_producto='{$_POST['cod']}'";
        //echo $sql;
        if ($this->conexion->query($sql)) {
            $respuesta["res"] = true;
        }
        return json_encode($respuesta);
    }
    public function informacionPorCodigo()
    {
        $respuesta = ["res" => false];
        $sql = "SELECT * FROM productos where trim(codigo)='{$_POST['code']}' AND almacen = '{$_POST['almacen']}' and sucursal='{$_SESSION['sucursal']}'";

        if ($row = $this->conexion->query($sql)->fetch_assoc()) {
            $respuesta["res"] = true;
            $respuesta["data"] = $row;
        }
        return json_encode($respuesta);
    }
    public function informacion()
    {
        $respuesta = ["res" => false];
        $sql = "SELECT * FROM productos where id_producto='{$_POST['cod']}'";
        if ($row = $this->conexion->query($sql)->fetch_assoc()) {
            $respuesta["res"] = true;
            $respuesta["data"] = $row;
        }
        return json_encode($respuesta);
    }
    public function agregar()
    {
        $respuesta = ["res" => false];
        $descripcion = $_POST['descripcicon'];
        $codigoProd = $_POST['codigo'];
        for ($i=1; $i < 3; $i++) { 
            $sql = "insert into productos set descripcion=?,
            precio='{$_POST['precio']}',
            costo='{$_POST['costo']}',
            almacen='{$i}',
            cantidad='{$_POST['cantidad']}',
            iscbp='{$_POST['afecto']}',
              sucursal='{$_SESSION['sucursal']}',
            id_empresa='{$_SESSION['id_empresa']}',
            ultima_salida='1000-01-01',
            codsunat='{$_POST['codSunat']}',
            precio_mayor={$_POST['precioMayor']},precio_menor={$_POST['precioMenor']},razon_social='{$_POST['razon']}',ruc='{$_POST['ruc']}',codigo=?
            ";
          
                  $stmt = $this->conexion->prepare($sql);
                  $stmt->bind_param('ss', $descripcion, $codigoProd);
                  /*   $stmt->bind_param('s', $codigoProd); */
          
                  if ($stmt->execute()) {
                      $respuesta["res"] = true;
                  }
        }
      
        return json_encode($respuesta);
    }

    public function actualizarPrecios()
    {
        $respuesta = ["res" => false];
        $sql = "update productos set precio='{$_POST['precio']}',precio_unidad='{$_POST['precio_unidad']}', precio2='{$_POST['precio2']}', precio3='{$_POST['precio3']}', precio4='{$_POST['precio4']}' where id_producto='{$_POST['cod_prod']}'";
        if ($this->conexion->query($sql)) {
            $respuesta["res"] = true;
            $sql="select * from productos where id_producto='{$_POST['cod_prod']}'";
            $result = $this->conexion->query($sql);
            if ($row= $result->fetch_assoc()){
                $almacenTemp = $row["almacen"]=="1"?2:1;
                $sql = "update productos set 
                     precio='{$_POST['precio']}',precio_unidad='{$_POST['precio_unidad']}', 
                     precio2='{$_POST['precio2']}', precio3='{$_POST['precio3']}', 
                     precio4='{$_POST['precio4']}'
                  where descripcion=? and almacen='$almacenTemp'";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param('s', $row['descripcion']);
                /*   $stmt->bind_param('s', $codigoProd); */

                if(!$stmt->execute()){
                }


            }
        }
        return json_encode($respuesta);
    }
    public function confirmarTraslado()
    {
        $respuesta['res'] = false;
        $sql = "SELECT id_producto,almacen_ingreso,almacen_egreso,cantidad FROM ingreso_egreso WHERE intercambio_id ='{$_POST['cod']}'";
        $result = $this->conexion->query($sql)->fetch_assoc();

        $almacen = $result['almacen_ingreso'];
        $id_producto = $result['id_producto'];
        $cantidad = $result['cantidad'];

        $sql = "SELECT * FROM productos WHERE id_producto = '{$result['id_producto']}'";
        $result = $this->conexion->query($sql)->fetch_assoc();


        $sql = "SELECT * FROM productos WHERE descripcion = '{$result['descripcion']}' AND almacen = '$almacen'";
        $result2 = $this->conexion->query($sql)->fetch_assoc();


        if (is_null($result2)) {
            $sql = "INSERT INTO productos 
            (cod_barra, descripcion, precio, costo,cantidad,iscbp,id_empresa,sucursal,ultima_salida,codsunat,usar_barra,precio_mayor,precio_menor,razon_social,ruc,estado,almacen,precio2,precio3)
            SELECT cod_barra, descripcion, precio, costo,$cantidad,iscbp,id_empresa,sucursal,ultima_salida,codsunat,usar_barra,precio_mayor,precio_menor,razon_social,ruc,estado, $almacen,precio2,precio3
            FROM productos
            WHERE id_producto = $id_producto";
            if ($this->conexion->query($sql)) {
                $sql = "UPDATE productos set cantidad = cantidad - $cantidad   WHERE id_producto = $id_producto";
                if ($this->conexion->query($sql)) {
                    $respuesta['res'] = true;
                }
            }
        } else {
            $idExistente = $result2['id_producto'];
            $sql2 = "UPDATE  productos set cantidad =  cantidad - $cantidad  WHERE id_producto = $id_producto";
            if ($this->conexion->query($sql2)) {
                $sql = "UPDATE  productos set cantidad = cantidad + $cantidad   WHERE id_producto = $idExistente";
                if ($this->conexion->query($sql)) {
                    $respuesta['res'] = true;
                }
            }
        }
        if ($respuesta['res']) {
            $sql = "UPDATE  ingreso_egreso set estado = 1   WHERE intercambio_id = '{$_POST['cod']}'";
            if ($this->conexion->query($sql)) {
                $respuesta['res'] = true;
            }
        }
        echo json_encode($respuesta);
    }

    public function delete()
    {
        $respuesta["res"] = true;
        $respuesta["data"] = $_POST;
        $sql = '';
        foreach ($respuesta["data"]['arrayId'] as $ids) {
            /*   $sql .= $ids; */

            $sql = "UPDATE   productos set estado=0 where id_producto = '{$ids['id']}'";
            if ($this->conexion->query($sql)) {
                $respuesta["res"] = true;
            }
        }
        return json_encode($respuesta);
    }

    private function enviarRespuesta($success, $message, $data = []) // Función reutilizable para enviar respuestas JSON
    {
        $response = [
            'success' => $success, // Indicar si la operación fue exitosa
            'message' => $message, // Mensaje de la operación
            'data' => $data // Datos adicionales (opcional)
        ];

        if (!$success) {
            error_log("Error en la respuesta: " . $message); // Registrar errores en el log
        }

        header('Content-Type: application/json'); // Establecer el encabezado como JSON
        echo json_encode($response); // Codificar la respuesta como JSON
        exit; // Terminar la ejecución
    }

    public function saveProductsMassive()
    {
        try {
            $this->validateInputs();
            $spreadsheet = $this->loadSpreadsheet();
            $sheetProductos = $this->getProductSheet($spreadsheet);
            
            $deletedProducts = json_decode($_POST['deletedProducts'], true);
            $this->removeDeletedProducts($sheetProductos, $deletedProducts);
    
            $this->validateProductData($sheetProductos);
    
            $productosRestantes = $this->processRemainingProducts($sheetProductos);
            
            if (empty($productosRestantes)) {
                throw new \Exception('No hay productos restantes para procesar.');
            }
    
            // MODIFICADO: Separar productos nuevos y existentes
            $productosNuevos = [];
            $productosActualizados = [];
            $hayProductosActualizados = false;
            
            // Verificar qué productos ya existen en la base de datos
            foreach ($productosRestantes as $index => $producto) {
                $codigo = $producto['codigo'];
                $query = "SELECT idproductosv2, cantidad FROM productosv2 WHERE codigo = ?";
                $stmt = $this->conexion->prepare($query);
                $stmt->bind_param("s", $codigo);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // El producto existe, lo actualizamos directamente
                    $row = $result->fetch_assoc();
                    $idProducto = $row['idproductosv2'];
                    $cantidadActual = (int)$row['cantidad'];
                    $cantidadNueva = (int)$producto['cantidad'];
                    
                    // Actualizar el producto
                    $this->actualizarProductom($producto, $idProducto);
                    
                    // Guardar el ID para usarlo después
                    $productosActualizados[$codigo] = $idProducto;
                    
                    // Registrar movimiento si hay cambio en la cantidad
                    if ($cantidadActual != $cantidadNueva) {
                        $this->registrarMovimientoAjuste(
                            $idProducto, 
                            $codigo, 
                            $producto['nombre'], 
                            $cantidadActual, 
                            $cantidadNueva, 
                            $producto['razon_social'] ?? ''
                        );
                    }
                    
                    $hayProductosActualizados = true;
                } else {
                    // El producto es nuevo, lo agregamos a la lista de nuevos
                    $productosNuevos[] = $producto;
                }
            }
            
            // Guardar solo los productos nuevos usando el método existente
            $idsProductosNuevos = [];
            if (!empty($productosNuevos)) {
                $idsProductosNuevos = $this->saveProducts($productosNuevos);
                
                if ($idsProductosNuevos === false) {
                    throw new \Exception('Error al guardar los productos nuevos en la base de datos.');
                }
                
                // Registrar movimientos para productos nuevos
                $this->registrarMovimientosNuevos($idsProductosNuevos, $productosNuevos);
            }
            
            // Combinar IDs de productos nuevos y actualizados
            $idsProductos = array_merge($productosActualizados, $idsProductosNuevos ?: []);
            
            // Procesar características
            $sheetCaracteristicas = $spreadsheet->getSheetByName('Características');
            if ($sheetCaracteristicas) {
                $this->removeDeletedCharacteristics($sheetCaracteristicas, $deletedProducts);
                $this->validateCharacteristicsData($sheetCaracteristicas);
                $this->saveCharacteristics($sheetCaracteristicas, $idsProductos, $productosRestantes);
            } else {
                throw new \Exception('La hoja "Características" no existe en el archivo Excel.');
            }
            
            // MODIFICADO: Mensaje personalizado según si hay productos actualizados
            if ($hayProductosActualizados) {
                $this->enviarRespuesta(true, 'Productos y características importados exitosamente. Las características de los productos actualizados no se guardaron por seguridad. Si desea actualizar características, use el sistema manualmente.');
            } else {
                $this->enviarRespuesta(true, 'Productos y características importados exitosamente.');
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    private function actualizarProductom($producto, $idProducto) {
        $query = "UPDATE productosv2 SET 
            nombre = ?,
            cantidad = ?,
            cantidad_unidad = ?,
            unidad_medida = ?,
            tipo_producto = ?,
            categoria = ?,
            fecha_vencimiento = ?,
            ruc = ?,
            razon_social = ?,
            precio = ?,
            fecha_registro = ?,
            guia_remision = ?,
            precio_venta = ?,
            moneda = ?,
            descuento_cuota = ?
            WHERE idproductosv2 = ?";
        
        // Convertir fechas al formato correcto
        $fecha_vencimiento = !empty($producto['fecha_vencimiento']) ? date('Y-m-d', strtotime($producto['fecha_vencimiento'])) : null;
        $fecha_registro = !empty($producto['fecha_registro']) ? date('Y-m-d', strtotime($producto['fecha_registro'])) : date('Y-m-d');
        
        // Asignar valores a variables para poder pasarlas por referencia
        $nombre = $producto['nombre'];
        $cantidad = $producto['cantidad'];
        $cantidad_unidad = $producto['cantidad_unidad'];
        $unidad_medida = $producto['unidad_medida'];
        $tipo_producto = $producto['tipo_producto'];
        $categoria = $producto['categoria'];
        $ruc = $producto['ruc'];
        $razon_social = $producto['razon_social'];
        $precio = $producto['precio'];
        $guia_remision = $producto['guia_remision'];
        $precio_venta = $producto['precio_venta'];
        $moneda = $producto['moneda'] ?? 'S/.'; // Ahora asignado a variable
        $descuento_cuota = $producto['descuento_cuota'];
        
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param(
            "sidssssssdsssssi",
            $nombre,
            $cantidad,
            $cantidad_unidad,
            $unidad_medida,
            $tipo_producto,
            $categoria,
            $fecha_vencimiento,
            $ruc,
            $razon_social,
            $precio,
            $fecha_registro,
            $guia_remision,
            $precio_venta,
            $moneda,
            $descuento_cuota,
            $idProducto
        );
        
        if (!$stmt->execute()) {
            throw new \Exception("Error al actualizar producto: " . $this->conexion->error);
        }
        
        return true;
    }
    
    // AÑADIDO: Método para registrar movimiento de ajuste
    private function registrarMovimientoAjuste($idProducto, $codigo, $nombre, $cantidadActual, $cantidadNueva, $razonSocial) {
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id) {
            return false;
        }
        
        $reportesModel = new Reportes();
        
        // Determinar tipo y subtipo de movimiento
        if ($cantidadNueva > $cantidadActual) {
            $tipo_movimiento = 'Entrada';
            $subtipo_movimiento = 'Masivo'; // Entrada por Excel
            $cantidad_movimiento = $cantidadNueva - $cantidadActual;
        } else if ($cantidadNueva < $cantidadActual) {
            $tipo_movimiento = 'Salida';
            $subtipo_movimiento = 'Ajuste'; // Ajuste de inventario
            $cantidad_movimiento = $cantidadActual - $cantidadNueva;
        } else {
            // Si son iguales, no registrar movimiento
            return true;
        }
        
        // Convertir a string para el método registrarMovimiento
        $codigo = (string)$codigo;
        $cantidad_movimiento = (string)$cantidad_movimiento;
        
        return $reportesModel->registrarMovimiento(
            $usuario_id,
            $idProducto,
            $codigo,
            $nombre,
            $tipo_movimiento,
            $subtipo_movimiento,
            $cantidad_movimiento,
            $razonSocial
        );
    }
    
    // AÑADIDO: Método para registrar movimientos de productos nuevos
    private function registrarMovimientosNuevos($idsProductos, $productos) {
        if (!is_array($idsProductos) || empty($idsProductos)) {
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        if (!$usuario_id) {
            return;
        }
        
        $reportesModel = new Reportes();
        
        foreach ($productos as $producto) {
            $codigo = $producto['codigo'];
            
            if (isset($idsProductos[$codigo])) {
                $idProducto = $idsProductos[$codigo];
                $nombre = $producto['nombre'];
                $cantidad = (string)$producto['cantidad'];
                $razon_social = $producto['razon_social'] ?? '';
                
                $reportesModel->registrarMovimiento(
                    $usuario_id,
                    $idProducto,
                    (string)$codigo,
                    $nombre,
                    'Entrada',
                    'Masivo',
                    $cantidad,
                    $razon_social
                );
            }
        }
    }
    
        private function saveCharacteristics($sheet, $productosRestantes = [])
    {
    
        // Si el array está vacío, no hay nada que procesar
        if (empty($productosRestantes)) {
          
            return;
        }

        // 👉 Obtener los productos completos desde la base de datos
        $ids = implode(',', array_map('intval', $productosRestantes));
        $query = "SELECT idproductosv2, codigo, categoria FROM productosv2 WHERE idproductosv2 IN ($ids)";
        
        $resultado = $this->conexion->query($query);
        if (!$resultado) {
            throw new \Exception("Error al obtener productos de la base de datos: " . $this->conexion->error);
        }
        
        // Crear mapeo de productos por ID y por código
        $productosPorId = [];
        $productosPorCodigo = [];
        
        while ($fila = $resultado->fetch_assoc()) {
            $productosPorId[$fila['idproductosv2']] = $fila;
            $productosPorCodigo[$fila['codigo']] = $fila;
        }
        
        
        // Identificar productos celulares
        $productosCelulares = [];
        
        foreach ($productosPorId as $idProducto => $producto) {
            // Normalizar y verificar la categoría
            $categoria = $this->normalizarCategoria($producto['categoria']);
            
            
            if ($this->esCategoríaCelular($categoria)) {
                $productosCelulares[$producto['codigo']] = $idProducto;
                
            }
        }
        
        
        // Preparar arreglos para características
        $caracteristicas = [];
        $caracteristicasCelulares = [];
        $highestRow = $sheet->getHighestRow();
        
        // Procesar la hoja de características
        for ($row = 2; $row <= $highestRow; $row++) {
            $codigoProducto = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $nombreCaracteristica = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $valorCaracteristica = $sheet->getCellByColumnAndRow(4, $row)->getValue();
            
            
            if (empty($codigoProducto)) {
                
                continue;
            }
            
            // Verificar si el código existe en nuestra base de datos
            if (isset($productosPorCodigo[$codigoProducto])) {
                $idProducto = $productosPorCodigo[$codigoProducto]['idproductosv2'];
                
                // Verificar si es un celular según la clasificación previa
                if (isset($productosCelulares[$codigoProducto])) {
                    // Procesar como característica de celular
                    if (!isset($caracteristicasCelulares[$codigoProducto])) {
                        $caracteristicasCelulares[$codigoProducto] = [
                            'idProducto' => $idProducto,
                            'caracteristicas' => []
                        ];
                    }
                    
                    $caracteristicasCelulares[$codigoProducto]['caracteristicas'][$nombreCaracteristica] = $valorCaracteristica;
                    
                } else {
                    // Procesar como característica normal
                    $caracteristicas[] = [
                        'idproductosv2' => $idProducto,
                        'nombre_caracteristicas' => $nombreCaracteristica,
                        'valor_caracteristica' => $valorCaracteristica,
                    ];
                
                }
            } else {
            
            }
        }
        
        // Procesar características de celulares
        $celularesData = [];
        foreach ($caracteristicasCelulares as $codigo => $datos) {
            $idProducto = $datos['idProducto'];

            $celularData = [
                'idproductosv2' => $idProducto,
                'chip_linea' => null,
                'marca' => null,
                'modelo' => null,
                'imei' => null,
                'imei2' => null,
                'color' => null,
                'cargador' => null,
                'cable_usb' => null,
                'manual_usuario' => null,
                'estuche' => null
            ];

            $mapeo = [
                'Chip Linea' => 'chip_linea',
                'Marca' => 'marca',
                'Modelo' => 'modelo',
                'Nro IMEI' => 'imei',
                'Nro Serie' => 'imei2',
                'Color' => 'color',
                'Cargador' => 'cargador',
                'Cable USB' => 'cable_usb',
                'Manual Usuario' => 'manual_usuario',
                'Estuche' => 'estuche'
            ];

            $variaciones = [
                'chip_linea' => ['Chip Linea', 'Chip Línea', 'Chip', 'Linea', 'Línea'],
                'marca' => ['Marca', 'Marca Equipo'],
                'modelo' => ['Modelo'],
                'imei' => ['Nro IMEI', 'IMEI', 'Número IMEI'],
                'imei2' => ['Nro Serie', 'Serie', 'Número Serie'],
                'color' => ['Color', 'Colorc'],
                'cargador' => ['Cargador'],
                'cable_usb' => ['Cable USB', 'Cable'],
                'manual_usuario' => ['Manual Usuario', 'Manual'],
                'estuche' => ['Estuche', 'Funda']
            ];

            foreach ($datos['caracteristicas'] as $nombre => $valor) {
                // Primero verificar coincidencia directa
                if (isset($mapeo[$nombre])) {
                    $columna = $mapeo[$nombre];
                    $celularData[$columna] = $valor;
                
                    continue;
                }

                // Verificar variaciones 
                foreach ($variaciones as $columna => $posiblesNombres) {
                    foreach ($posiblesNombres as $posibleNombre) {
                        if (stripos($nombre, $posibleNombre) !== false) {
                            $celularData[$columna] = $valor;
                        
                            break 2;
                        }
                    }
                }
            }

            $celularesData[] = $celularData;
        
        }

        // Guardar datos de celulares
        if (!empty($celularesData)) {
        

            $celularModel = new Celular();
            $result = $celularModel->guardarCelularesMasivos($celularesData);
        
            if ($result === false) {
                throw new \Exception('Error al guardar las características de celulares en la base de datos.');
            }
        } else {
        
        }

        // Guardar características normales
        if (!empty($caracteristicas)) {
        

            $caracteristicaModel = new CaracteristicaProducto();
            $result = $caracteristicaModel->guardarCaracteristicasMasivas($caracteristicas);
            
            if ($result === false) {
                throw new \Exception('Error al guardar las características en la base de datos.');
            }
        } else {
        
        }

    
    }

/**
 * Normaliza el texto de categoría para facilitar comparaciones
 */
private function normalizarCategoria($categoria) {
    // Convertir a minúsculas
    $normalizada = mb_strtolower(trim($categoria), 'UTF-8');
    
    // Eliminar acentos/diacríticos
    $normalizada = preg_replace('~\p{Mn}~u', '', normalizer_normalize($normalizada, Normalizer::FORM_D));
    
    // Eliminar espacios adicionales
    $normalizada = preg_replace('/\s+/', '', $normalizada);
    
    
    return $normalizada;
}

/**
 * Verifica si la categoría normalizada corresponde a un celular
 */
private function esCategoríaCelular($categoriaNormalizada) {
    $categoriasCelular = ['celular', 'celulares', 'cel', 'movil', 'smartphone'];
    
    foreach ($categoriasCelular as $categoriaValida) {
        if ($categoriaNormalizada === $categoriaValida || 
            strpos($categoriaNormalizada, $categoriaValida) !== false) {
            return true;
        }
    }
    
    return false;
}
    


    

    private function validateInputs()
    {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Archivo inválido o no recibido.');
        }
        if (!isset($_POST['deletedProducts']) || empty($_POST['deletedProducts'])) {
            throw new \Exception('No se recibieron códigos de productos para eliminar.');
        }
    }

    private function loadSpreadsheet()
    {
        $file = $_FILES['file']['tmp_name'];
        return \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    }

    private function getProductSheet($spreadsheet)
    {
        $sheetProductos = $spreadsheet->getSheetByName('Productos');
        if (!$sheetProductos) {
            throw new \Exception('La hoja "Productos" no existe en el archivo Excel.');
        }
        return $sheetProductos;
    }

    private function removeDeletedProducts($sheet, $deletedProducts)
    {
        $highestRow = $sheet->getHighestRow();
        for ($row = $highestRow; $row >= 2; $row--) {
            $codigo = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            if (in_array($codigo, $deletedProducts)) {
                $sheet->removeRow($row);
            }
        }
    }

    private function processRemainingProducts($sheet)
    {
        $productosRestantes = [];
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $producto = [
                'nombre' => $sheet->getCellByColumnAndRow(1, $row)->getValue(),
                'codigo' => $sheet->getCellByColumnAndRow(2, $row)->getValue(),
                'cantidad' => $sheet->getCellByColumnAndRow(3, $row)->getValue(),
                'cantidad_unidad' => $sheet->getCellByColumnAndRow(4, $row)->getValue(),
                'unidad_medida' => $sheet->getCellByColumnAndRow(5, $row)->getValue(), // Cambiado de 'unidadMedida' a 'unidad_medida'
                'tipo_producto' => $sheet->getCellByColumnAndRow(6, $row)->getValue(),
                'categoria' => $sheet->getCellByColumnAndRow(7, $row)->getValue(),
                'fecha_vencimiento' => $sheet->getCellByColumnAndRow(8, $row)->getValue(),
                'ruc' => $sheet->getCellByColumnAndRow(9, $row)->getValue(),
                'razon_social' => $sheet->getCellByColumnAndRow(10, $row)->getValue(),
                'precio' => $sheet->getCellByColumnAndRow(11, $row)->getValue(),
                'precio_venta' =>$sheet->getCellByColumnAndRow(12, $row)->getValue(),
                'fecha_registro' => $sheet->getCellByColumnAndRow(13, $row)->getValue(),
                'guia_remision' => $sheet->getCellByColumnAndRow(14, $row)->getValue(),
                'moneda' => $sheet->getCellByColumnAndRow(15, $row)->getValue(),
                'descuento_cuota' => $sheet->getCellByColumnAndRow(16, $row)->getValue()
            ];
            $productosRestantes[] = $producto; 
            
           // var_dump($productosRestantes);
            //var_dump($producto);
            
            // Eliminada la línea duplicada
        }
        return $productosRestantes;
    }
    
    private function saveProducts($productos)
    {
        $productoModel = new ProductoV2();
        $result = $productoModel->guardarProductosMasivos($productos);

        
        if ($result === false) {
            throw new \Exception('Error al guardar los productos en la base de datos.');
        }
       
        return $result;
    }

    private function removeDeletedCharacteristics($sheet, $deletedProducts)
    {
        $highestRow = $sheet->getHighestRow();
        for ($row = $highestRow; $row >= 2; $row--) {
            $codigoCaracteristica = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            if (in_array($codigoCaracteristica, $deletedProducts)) {
                $sheet->removeRow($row);
            }
        }
    }

    private function validateProductData($sheet)
    {
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $this->validateProductRow($sheet, $row);
        }
    }

    private function validateProductRow($sheet, $row)
    {
        $cantidad = $sheet->getCellByColumnAndRow(3, $row)->getValue();
        $cantidadUnidad = $sheet->getCellByColumnAndRow(4, $row)->getValue();
        $fechaVencimiento = $sheet->getCellByColumnAndRow(8, $row)->getValue();
        $ruc = $sheet->getCellByColumnAndRow(9, $row)->getValue();
        $precioVenta = $sheet->getCellByColumnAndRow(11, $row)->getValue();
        $fechaRegistro = $sheet->getCellByColumnAndRow(12, $row)->getValue();

        if (!is_numeric($cantidad)) {
            throw new \Exception("Cantidad inválida en la fila $row.");
        }
        if (!is_numeric($cantidadUnidad) && !empty($cantidadUnidad)) {
            throw new \Exception("Cantidad por unidad inválida en la fila $row.");
        }
        $this->validateDate($fechaVencimiento, "Fecha de vencimiento", $row);
        
        if (!empty($ruc) && (!is_numeric($ruc) || strlen($ruc) !== 11)) { // Modificado: Permitir RUC vacío y validar solo si tiene valor
            throw new \Exception("RUC inválido en la fila $row.");
        }
        
        if (empty($ruc)) { // Modificado: Si el RUC está vacío, guardarlo como null
            $ruc = null;
        }

        if (!empty($precioVenta) && !is_numeric($precioVenta)) { // Modificado: Validar precio solo si tiene valor
            throw new \Exception("Precio de venta inválido en la fila $row.");
        }
        
        if (empty($precioVenta)) { // Modificado: Si el precio está vacío, guardarlo como null
            $precioVenta = null;
        }
    
        $this->validateRegistrationDate($fechaRegistro, $row);

        $moneda = $sheet->getCellByColumnAndRow(15, $row)->getValue();
        $descuentoCuota = $sheet->getCellByColumnAndRow(16, $row)->getValue();

        // Validar moneda
        if (empty($moneda) || !in_array($moneda, ['S/.', '$'])) {
            throw new \Exception("Moneda inválida en la fila $row. Debe ser 'S/.' o '$'.");
        }

        // Validar descuento por cuota (opcional, pero si tiene valor debe ser numérico)
        if (!empty($descuentoCuota) && !is_numeric($descuentoCuota)) {
            throw new \Exception("Descuento por cuota inválido en la fila $row.");
        }

        if (!empty($descuentoCuota) && $descuentoCuota < 0) {
            throw new \Exception("Descuento por cuota no puede ser negativo en la fila $row.");
        }
    }

    private function validateDate($date, $fieldName, $row)
    {
        if (!empty($date)) {
            if (is_numeric($date)) {
                $date = \DateTime::createFromFormat('!d-m-Y', gmdate('d-m-Y', ($date - 25569) * 86400));
            } else {
                $date = \DateTime::createFromFormat('Y-m-d', $date);
            }
            if (!$date) {
                throw new \Exception("$fieldName inválida en la fila $row.");
            }
        }
    }

    private function validateRegistrationDate($date, $row)
    {
        if (empty($date)) {
            throw new \Exception("La fecha de registro es obligatoria en la fila $row.");
        }
        $this->validateDate($date, "Fecha de registro", $row);
        $fechaRegistro = is_numeric($date) 
            ? \DateTime::createFromFormat('!d-m-Y', gmdate('d-m-Y', ($date - 25569) * 86400))
            : \DateTime::createFromFormat('Y-m-d', $date);
        
        $fechaActual = new \DateTime();
        $fechaDosDiasFuturo = (clone $fechaActual)->modify('+2 days');
        
        if ($fechaRegistro > $fechaDosDiasFuturo) {
            throw new \Exception("Fecha de registro no puede ser mayor a dos días en el futuro en la fila $row.");
        }
    }

    private function validateCharacteristicsData($sheet)
    {
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $caracteristica = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            if (empty($caracteristica)) {
                throw new \Exception("Característica vacía en la fila $row de la hoja 'Características'.");
            }
        }
    }

    

    public function downloadReport()
    {

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
     
        $sheet = $spreadsheet->getActiveSheet();

        // Definir las cabeceras del archivo Excel
        $headers = [
            'ID Producto', 'Nombre', 'Código', 'Cantidad', 'Cantidad Unidad', 
            'Unidad de Medida', 'Tipo de Producto', 'Categoría', 'Fecha de Vencimiento',
            'RUC', 'Razón Social', 'Precio', 'Precio Total', 'Fecha de Registro', 
            'Guía de Remisión', 'Texto de Cabecera'
        ];

        // Escribir las cabeceras en la primera fila
        $columnIndex = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $header);
            $columnIndex++;
        }

        $sheet->getColumnDimension('A')->setWidth(15); // ID Producto
        $sheet->getColumnDimension('B')->setWidth(30); // Nombre
        $sheet->getColumnDimension('C')->setWidth(20); // Código
        $sheet->getColumnDimension('D')->setWidth(15); // Cantidad
        $sheet->getColumnDimension('E')->setWidth(20); // Cantidad Unidad
        $sheet->getColumnDimension('F')->setWidth(20); // Unidad de Medida
        $sheet->getColumnDimension('G')->setWidth(20); // Tipo de Producto
        $sheet->getColumnDimension('H')->setWidth(20); // Categoría
        $sheet->getColumnDimension('I')->setWidth(20); // Fecha de Vencimiento
        $sheet->getColumnDimension('J')->setWidth(20); // RUC
        $sheet->getColumnDimension('K')->setWidth(30); // Razón Social
        $sheet->getColumnDimension('L')->setWidth(15); // Precio
        $sheet->getColumnDimension('M')->setWidth(20); // Precio Total
        $sheet->getColumnDimension('N')->setWidth(20); // Fecha de Registro
        $sheet->getColumnDimension('O')->setWidth(20); // Guía de Remisión
        $sheet->getColumnDimension('P')->setWidth(30); // Texto de Cabecera

       

        // Llamar al modelo para obtener los datos
        $productoModel = new Productov2();
        $productos = $productoModel->reporteProducts();
        
        // Escribir los datos en el archivo Excel
        $rowIndex = 2; // Comenzar desde la segunda fila
        foreach ($productos as $producto) {
            $sheet->setCellValue('A' . $rowIndex, $producto['idproductosv2']);
            $sheet->setCellValue('B' . $rowIndex, $producto['nombre']);
            $sheet->setCellValue('C' . $rowIndex, $producto['codigo']);
            $sheet->setCellValue('D' . $rowIndex, $producto['cantidad']);
            $sheet->setCellValue('E' . $rowIndex, $producto['cantidad_unidad']);
            $sheet->setCellValue('F' . $rowIndex, $producto['unidad_medida']);
            $sheet->setCellValue('G' . $rowIndex, $producto['tipo_producto']);
            $sheet->setCellValue('H' . $rowIndex, $producto['categoria']);
            $sheet->setCellValue('I' . $rowIndex, $producto['fecha_vencimiento']);
            $sheet->setCellValue('J' . $rowIndex, $producto['ruc']);
            $sheet->setCellValue('K' . $rowIndex, $producto['razon_social']);
            $sheet->setCellValue('L' . $rowIndex, $producto['precio']);
            $sheet->setCellValue('M' . $rowIndex, $producto['cantidad'] * $producto['precio']); // Precio Total
            $sheet->setCellValue('N' . $rowIndex, $producto['fecha_registro']);
            $sheet->setCellValue('O' . $rowIndex, $producto['guia_remision']);
            $sheet->setCellValue('P' . $rowIndex, $producto['texto_cabecera']); // Texto de Cabecera
            $rowIndex++;
        }

        // Alinear la columna "ID Producto" (columna A) a la izquierda
        $sheet->getStyle('A2:A' . ($rowIndex - 1)) // Solo la columna "ID Producto" desde la fila 2 hasta la última
        ->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT); // Alinear a la izquierda

        // Alinear todo el contenido a partir de la fila 2 a centrado
        for ($row = 2; $row <= $rowIndex - 1; $row++) {
            $sheet->getStyle('A' . $row . ':P' . $row) // Desde la columna A hasta la P
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Alinear a la centrado
        }

        // Preparar el archivo para la descarga
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $fileName = 'reporte_inventario.xlsx';

        // Limpiar cualquier salida previa
        ob_end_clean(); // Añadido: Limpiar el buffer de salida
    
        // Configurar las cabeceras de la respuesta
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"'); // Modificado: Añadido espacio después de 'attachment;'
        header('Cache-Control: max-age=0');

        // Enviar el archivo Excel al navegador
        $writer->save('php://output'); // Guardar el archivo en el buffer de salida

        exit; // Se asegura de que no haya más salida posterior

    }

    public function generateBarCode($codigo)
    {
        // ✅ Verificar si se recibió el código por GET
        if (!isset($_GET['codigo']) || empty($_GET['codigo'])) { 
            echo json_encode(["error" => "Código no proporcionado"]); // ✅ Reemplaza response()->json() por json_encode()
            return;
        }

        $codigo = $_GET['codigo']; // ✅ Obtiene el código desde la petición GET

        $generator = new BarcodeGeneratorPNG(); // Inicializa el generador de códigos de barras
        $barcode = $generator->getBarcode($codigo, $generator::TYPE_CODE_128); // Genera el código de barras tipo 128
        
        $barcodeBase64 = base64_encode($barcode); // ✅ Convierte la imagen en base64
        $imageUrl = 'data:image/png;base64,' . $barcodeBase64;
    
        echo json_encode(['image' => $imageUrl]); // ✅ Reemplaza response()->json() por json_encode()

    }

    public function getBarCode()
    {
        // Verificar si se recibió el ID del producto
        if (!isset($_GET['id_producto']) || empty($_GET['id_producto'])) {
            echo json_encode(["error" => "ID de producto no proporcionado"]);
            return;
        }

        $id_producto = $_GET['id_producto'];

        $productoModel = new Productov2();

        // Obtener el código de barras del producto
        $codigo = $productoModel->getCodeBar($id_producto);

        // Devolver la respuesta en formato JSON
        echo json_encode(["codigo" => $codigo]);
    }

    public function getdataForBarcode()
    {
        if (!isset($_GET["codigo"])) {
            echo json_encode(["success" => false, "message" => "Código no recibido"]);
            return;
        }

        $codigo = trim($_GET["codigo"]); 
        //var_dump($codigo);
        $productoModel = new Productov2();
        $producto = $productoModel->getdataForBarcode($codigo);

        if ($producto) {
            // Solo enviar los datos necesarios al frontend
            echo json_encode([
                "success" => true,
                "nombre" => $producto["nombre"],
                "precio_venta" => $producto["precio_venta"]
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Producto no encontrado"]);
        }
    }
    
    public function deleteProducts() {
        // Verificar si se recibieron datos mediante POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener el JSON enviado desde la petición AJAX
            $data = json_decode(file_get_contents("php://input"), true);
            
            // Verificar si el array 'ids' está presente y no está vacío
            if (!isset($data['ids']) || empty($data['ids'])) {
                echo json_encode(["status" => "error", "message" => "No se recibieron productos para eliminar."]);
                return;
            }

            $productoModel = new Productov2();

            // Llamar al método eliminarProducts() y pasarle los IDs
            $resultado = $productoModel->eliminarProducts($data['ids']);

            // Verificar si la eliminación fue exitosa
            if ($resultado) {
                echo json_encode(["status" => "success", "message" => "Productos eliminados correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Hubo un problema al eliminar los productos."]);
            }
        } else {
            // Respuesta en caso de que no sea una petición POST válida
            echo json_encode(["status" => "error", "message" => "Método no permitido."]);
        }
    }

    public function obtenerDatosProducto() {
        // Verificar si se recibió el ID por POST
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            echo json_encode(["error" => "ID de producto no proporcionado"]);
            return;
        }

        $id_producto = intval($_POST['id']); // Sanitizar el ID

        // Instanciar el modelo
        $productoModel = new Productov2();

        // Obtener los datos del producto
        $producto = $productoModel->obtenerProductoPorId($id_producto);

        if (!$producto) {
            echo json_encode(["error" => "Producto no encontrado"]);
            return;
        }

        // Verificar si el producto es un celular
        $categoria = isset($producto['CATEGORIA']) ? $producto['CATEGORIA'] : '';
            
        // Normalizar la categoría para comparación (quitar acentos, espacios extras y convertir a minúsculas)
        $categoriaNormalizada = $this->normalizarCategorie($categoria);
        $esCelular = (strpos($categoriaNormalizada, 'celular') === 0);

        $caracteristicas = [];

        if ($esCelular) {
            // Si es un celular, obtener características de la tabla celulares
            $caracteristicas = $this->obtenerCaracteristicasCelular($id_producto);
        } else {
            // Para otros productos, usar el método estándar
            $caracteristicasModel = new CaracteristicaProducto();
            $caracteristicas = $caracteristicasModel->obtenerCaracteristicas($id_producto);
        }

        // Retornar los datos del producto junto con sus características
        echo json_encode([
            "producto" => $producto,
            "caracteristicas" => $caracteristicas
        ]);
    }

    private function normalizarCategorie($texto) {
        // Convertir a minúsculas
        $texto = mb_strtolower($texto, 'UTF-8');
        
        // Reemplazar tildes
        $buscar = ['á', 'é', 'í', 'ó', 'ú', 'ü'];
        $reemplazar = ['a', 'e', 'i', 'o', 'u', 'u'];
        $texto = str_replace($buscar, $reemplazar, $texto);
        
        // Eliminar espacios extras
        $texto = trim($texto);
        
        return $texto;
    }

    private function obtenerCaracteristicasCelular($idProducto) {
        $caracteristicas = [];
        
        try {
            // Crear conexión usando el constructor existente
            $conexion = (new Conexion())->getConexion();
            
            // Consultar datos del celular por idproductosv2
            $query = "SELECT * FROM celulares WHERE idproductosv2 = $idProducto";
            $resultado = $conexion->query($query);
            
            if ($resultado && $resultado->num_rows > 0) {
                // Obtener datos del celular
                $celular = $resultado->fetch_assoc();
                
                // Mapeo de campos de la tabla celulares a los nombres de características esperados por el frontend
                $mapeoCaracteristicas = [
                    'chip_linea' => 'chip_linea',
                    'marca' => 'marca_equipo',
                    'modelo' => 'modelo',
                    'imei' => 'nro_imei',
                    'imei2' => 'nro_imei 2',  // Asumimos que imei2 corresponde a nro_serie
                    'color' => 'color',
                    'cargador' => 'cargador',
                    'cable_usb' => 'cable_usb',
                    'manual_usuario' => 'manual_usuario',
                    'estuche' => 'estuche'
                ];
                
                // Contador para asignar IDs únicos a cada característica
                $idCaracteristica = 1;
                
                // Crear array de características en el formato esperado por el frontend
                foreach ($mapeoCaracteristicas as $campoDB => $nombreCaracteristica) {
                    $caracteristicas[] = [
                        'idcaracteristica' => $idCaracteristica++,
                        'nombre_caracteristicas' => $nombreCaracteristica,
                        'valor_caracteristica' => isset($celular[$campoDB]) ? $celular[$campoDB] : ''
                    ];
                }
            }
        } catch (Exception $e) {
            // Manejar error (como no queremos detener la ejecución, simplemente registramos el error)
            error_log("Error al obtener características del celular (ID: $idProducto): " . $e->getMessage());
        }
        
        return $caracteristicas;
    }

    public function getEditsSeletProducto()
    {
        // Instanciar los modelos
        $tipoProductoModel = new TipoProductoModel();
        $categoriaProductoModel = new CategoriaProductoModel();

        // Obtener datos desde los modelos
        $tiposProducto = $tipoProductoModel->obtenerTiposProducto();
        $categoriasProducto = $categoriaProductoModel->obtenerCategoriasProducto();

        // Estructurar la respuesta como JSON
        $response = [
            'tiposProducto' => $tiposProducto,
            'categorias' => $categoriasProducto
        ];

        // Devolver respuesta JSON
        echo json_encode($response);
    }

    // Reemplaza toda la función actualizarProducto() en ProductosController.php
    public function actualizarProducto()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                return;
            }

            $idProducto = isset($_POST['ID_PRODUCTO']) ? intval($_POST['ID_PRODUCTO']) : 0;
            
            if ($idProducto <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de producto no válido']);
                return;
            }
            
            $usuario_id = $_SESSION['usuario_id'] ?? null;
            if (!$usuario_id) {
                echo json_encode(['success' => false, 'message' => 'No se pudo obtener el ID del usuario.']);
                return;
            }

            $productoModel = new Productov2();
            $productoExistente = $productoModel->obtenerProductoPorId($idProducto);
            
            if (!$productoExistente) {
                echo json_encode(['success' => false, 'message' => 'El producto no existe']);
                return;
            }

            // Iniciar transacción
            $this->conexion = (new Conexion())->getConexion();
            $this->conexion->begin_transaction();

            try {
                // Recopilar datos del formulario
                $datosProducto = $this->recopilarDatosProducto($_POST, $productoExistente);
                
                // Validar datos obligatorios
                if (empty($datosProducto['nombre']) || empty($datosProducto['categoria'])) {
                    throw new Exception('El nombre y la categoría son obligatorios');
                }

                // Registrar movimiento si cambió la cantidad
                $this->registrarMovimientoCantidad($productoExistente, $datosProducto, $usuario_id);

                // Actualizar producto principal
                if (!$productoModel->actualizar($datosProducto)) {
                    throw new Exception('Error al actualizar el producto');
                }

                // Manejar características según la categoría
                $this->procesarCaracteristicas($idProducto, $datosProducto['categoria'], $_POST);

                $this->conexion->commit();
                echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
                
            } catch (Exception $e) {
                $this->conexion->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("Error en actualizarProducto: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Normaliza una cadena eliminando acentos, espacios adicionales y convirtiéndola a minúsculas
     * @param string $texto Texto a normalizar
     * @return string Texto normalizado
     */
    private function normalizarTextoEditar($texto) {
        // Convertir a minúsculas
        $normalizado = mb_strtolower(trim($texto), 'UTF-8');
        
        // Eliminar acentos/diacríticos
        $normalizado = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalizado);
        
        // Eliminar caracteres no alfanuméricos y espacios adicionales
        $normalizado = preg_replace('/[^a-z0-9]/', '', $normalizado);
        
        return $normalizado;
    }

    /**
     * Verifica si una categoría corresponde a celular
     * @param string $categoria Categoría a verificar
     * @return bool True si es celular, false en caso contrario
     */
    private function esCategoríaCelularedit($categoria) {
        // Lista de posibles valores para categoría celular
        $categoriasCelular = ['celular', 'celulares', 'movil', 'smartphone'];
        
        // Normalizar la categoría para comparación
        $categoriaNorm = $this->normalizarTextoEditar($categoria);
        
        // Verificar si coincide con alguna de las opciones
        foreach ($categoriasCelular as $opcion) {
            $opcionNorm = $this->normalizarTextoEditar($opcion);
            if ($categoriaNorm === $opcionNorm || strpos($categoriaNorm, $opcionNorm) !== false) {
                return true;
            }
        }
        
        return false;
    }

    public function actualizar(array $producto)
    {
        try {
            // Iniciar una transacción para asegurar la integridad de los datos
            $this->conexion->begin_transaction();
            
            // Verificar que el producto existe antes de actualizarlo
            $sqlVerificar = "SELECT idproductosv2 FROM productosv2 WHERE idproductosv2 = ?";
            $stmtVerificar = $this->conexion->prepare($sqlVerificar);
            $stmtVerificar->bind_param('i', $producto['idproductosv2']);
            $stmtVerificar->execute();
            $resultVerificar = $stmtVerificar->get_result();
            
            if ($resultVerificar->num_rows === 0) {
                $this->conexion->rollback();
                error_log("Error: Producto con ID {$producto['idproductosv2']} no encontrado");
                return false;
            }
            
            // Preparar la consulta SQL para actualizar el producto
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
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                $this->conexion->rollback();
                error_log("Error al preparar la consulta: " . $this->conexion->error);
                return false;
            }
            
            // Vincular los parámetros a la consulta
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
            
            // Ejecutar la consulta
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $this->conexion->rollback();
                error_log("Error al ejecutar la consulta: " . $stmt->error);
                return false;
            }
            
            // Confirmar la transacción
            $this->conexion->commit();
            return true;
            
        } catch (\Exception $e) {
            // En caso de error, revertir la transacción
            $this->conexion->rollback();
            error_log("Error en Productov2::actualizar(): " . $e->getMessage());
            return false;
        }
    }
    public function obtenerDetallesProducto()
    {
        try {
            // Obtener el ID del producto de la solicitud GET
            $idProducto = isset($_GET['id']) ? intval($_GET['id']) : 0;
            
            if ($idProducto <= 0) {
                throw new Exception('ID de producto no válido');
            }

            // Instanciar el modelo
            $productoModel = new Productov2();
            
            // Obtener los detalles del producto
            $producto = $productoModel->obtenerProductoDetallado($idProducto);
            
            if (!$producto) {
                throw new Exception('Producto no encontrado');
            }

            // MODIFICACIÓN: ASEGURAR QUE LOS DATOS ESTÉN CORRECTAMENTE FORMATEADOS
            // Para vehículos o celulares que pueden tener formato especial
            if (!isset($producto['caracteristicas'])) {
                $producto['caracteristicas'] = [];
            }

            // Devolver la respuesta en formato JSON
            echo json_encode([
                'success' => true,
                'producto' => $producto
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerDetallesProducto: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function verificarStockLogo()
    {
        try {
            $id_producto = $_POST['id_producto'] ?? 27;
            
            $producto = new Productov2();
            $logoYango = $producto->getProductsList($id_producto);
            
            if ($logoYango) {
                $tiene_stock = $logoYango['cantidad'] > 0;
                
                echo json_encode([
                    'success' => true,
                    'tiene_stock' => $tiene_stock,
                    'cantidad' => $logoYango['cantidad']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }


    // Agrega estas funciones nuevas al final de la clase ProductosController
    private function recopilarDatosProducto($post, $productoExistente) 
    {
        // Obtener y procesar tipo de producto
        $tipoProducto = $post['TIPO_PRODUCTO'] ?? '';
        if (is_numeric($tipoProducto)) {
            $tipoProductoModel = new TipoProductoModel();
            $tipoData = $tipoProductoModel->getdataForId($tipoProducto);
            $tipoProducto = $tipoData ? $tipoData['tipo_productocol'] : $tipoProducto;
        }

        // Obtener y procesar categoría
        $categoria = $post['CATEGORIA'] ?? '';
        if (is_numeric($categoria)) {
            $categoriaModel = new CategoriaProductoModel();
            $categoriaData = $categoriaModel->getCategoriesforId(intval($categoria));
            $categoria = $categoriaData ? $categoriaData['nombre'] : $categoria;
        }

        // Función auxiliar para convertir cadenas vacías a NULL
        $convertirVacioANull = function($valor) {
            return (empty($valor) || $valor === '' || $valor === 'null') ? null : $valor;
        };

        return [
            'idproductosv2' => $productoExistente['ID_PRODUCTO'],
            'nombre' => $post['NOMBRE'] ?? '',
            'marca' => $convertirVacioANull($post['MARCA'] ?? null),
            'modelo' => $convertirVacioANull($post['MODELO'] ?? null),
            'codigo' => $convertirVacioANull($post['CODIGO'] ?? null),
            'cantidad' => floatval($post['CANTIDAD'] ?? 0),
            'cantidad_unidad' => isset($post['CANTIDAD_UNIDAD']) && $post['CANTIDAD_UNIDAD'] !== '' ? floatval($post['CANTIDAD_UNIDAD']) : null,
            'unidad_medida' => $convertirVacioANull($post['UNIDAD_MEDIDA'] ?? null),
            'tipo_producto' => $tipoProducto,
            'categoria' => $categoria,
            'fecha_vencimiento' => $convertirVacioANull($post['FECHA_VENCIMIENTO'] ?? null),
            'ruc' => $post['RUC'] ?? '',
            'razon_social' => $post['RAZON_SOCIAL'] ?? '',
            'precio' => floatval($post['PRECIO'] ?? 0),
            'precio_venta' => floatval($post['PRECIO_VENTA'] ?? 0),
            'fecha_registro' => $convertirVacioANull($post['FECHA_REGISTRO'] ?? null),
            'guia_remision' => $convertirVacioANull($post['GUIA_REMISION'] ?? null),
            'codigo_barra' => $convertirVacioANull($post['CODIGO_BARRA'] ?? null),
            'descuento_cuota' => isset($post['DESCUENTO_CUOTA']) && $post['DESCUENTO_CUOTA'] !== '' ? floatval($post['DESCUENTO_CUOTA']) : null,
            'moneda' => $post['MONEDA'] ?? 'S/.'
        ];
    }

    private function registrarMovimientoCantidad($productoExistente, $datosProducto, $usuario_id) 
    {
        $cantidadActual = floatval($productoExistente['CANTIDAD']);
        $cantidadNueva = $datosProducto['cantidad'];

        if ($cantidadNueva !== $cantidadActual) {
            $tipoMovimiento = ($cantidadNueva > $cantidadActual) ? "Entrada" : "Salida";
            $diferenciaCantidad = abs($cantidadNueva - $cantidadActual);

            $reportesModel = new Reportes();
            $reportesModel->registrarMovimiento(
                $usuario_id,
                $productoExistente['ID_PRODUCTO'],
                $productoExistente['CODIGO'],
                $productoExistente['NOMBRE'],
                $tipoMovimiento,
                "Ajuste",
                $diferenciaCantidad,
                $productoExistente['RAZON_SOCIAL']
            );
        }
    }

    private function procesarCaracteristicas($idProducto, $categoria, $post) 
    {
        $categoriaNorm = $this->normalizarTextoEditar($categoria);
        
        if ($this->esCategoríaCelularedit($categoria)) {
            $this->procesarCaracteristicasCelular($idProducto, $post);
        } else {
            $this->procesarCaracteristicasGenerales($idProducto, $categoria, $post);
        }
    }

    private function procesarCaracteristicasCelular($idProducto, $post) 
    {
        $celularModel = new Celular();
        
        $datosCelular = [
            'idproductosv2' => $idProducto,
            'chip_linea' => $post['chip_linea'] ?? null,
            'marca' => $post['marca'] ?? null,
            'modelo' => $post['modelo'] ?? null,
            'imei' => $post['imei'] ?? null,
            'imei2' => $post['imei2'] ?? null,
            'color' => $post['color'] ?? null,
            'cargador' => $post['cargador'] ?? null,
            'cable_usb' => $post['cable_usb'] ?? null,
            'manual_usuario' => $post['manual_usuario'] ?? null,
            'estuche' => $post['estuche'] ?? null
        ];

        $celularModel->actualizarCaracteristicasCelular($datosCelular);
    }

    private function procesarCaracteristicasGenerales($idProducto, $categoria, $post)
    {
        $caracteristicaModel = new CaracteristicaProducto();

        // Eliminar características existentes
        $caracteristicaModel->eliminarCaracteristicasPorProducto($idProducto);

        // Procesar nuevas características desde JSON
        if (isset($post['caracteristicas'])) {
            $caracteristicas = json_decode($post['caracteristicas'], true);
            if (is_array($caracteristicas)) {
                foreach ($caracteristicas as $caracteristica) {
                    // CAMBIO: Guardar todas las características, incluso las vacías
                    // Esto evita que se pierdan características al editar el producto
                    if (!empty($caracteristica['nombre_caracteristica'])) {
                        $caracteristica['idproductosv2'] = $idProducto;
                        // Asegurar que valor_caracteristica existe, aunque sea vacío
                        if (!isset($caracteristica['valor_caracteristica'])) {
                            $caracteristica['valor_caracteristica'] = '';
                        }
                        $caracteristicaModel->insertarCaracteristica($caracteristica);
                    }
                }
            }
        }
    }

    /**
     * Verificar si un código de producto ya existe en la base de datos
     * @return JSON con productos que tienen el mismo código
     */
    public function verificarCodigoDuplicado()
    {
        header('Content-Type: application/json');

        if (!isset($_POST['codigo']) || empty(trim($_POST['codigo']))) {
            echo json_encode([
                'existe' => false,
                'productos' => []
            ]);
            return;
        }

        $codigo = trim($_POST['codigo']);

        try {
            // Buscar productos con el mismo código o código de barras
            $sql = "SELECT idproductosv2, nombre, codigo, codigo_barra, cantidad
                    FROM productosv2
                    WHERE codigo = ? OR codigo_barra = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ss", $codigo, $codigo);
            $stmt->execute();
            $result = $stmt->get_result();

            $productos = [];
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }

            echo json_encode([
                'existe' => count($productos) > 0,
                'productos' => $productos
            ]);

        } catch (Exception $e) {
            error_log("Error en verificarCodigoDuplicado: " . $e->getMessage());
            echo json_encode([
                'existe' => false,
                'productos' => [],
                'error' => 'Error al verificar código'
            ]);
        }
    }

    /**
     * Obtener solo productos con categoría "Vehículos"
     */
    public function obtenerVehiculos()
    {
        try {
            $productoModel = new Productov2();
            
            // Buscar tanto por nombre como por ID de categoría para compatibilidad con datos antiguos
            $vehiculosPorNombre = $productoModel->obtenerProductosPorCategoria('Vehículo');
            $vehiculosPorId = $productoModel->obtenerProductosPorCategoria('15');
            
            // Combinar ambos resultados y eliminar duplicados
            $vehiculos = array_merge($vehiculosPorNombre, $vehiculosPorId);
            
            // Eliminar duplicados basándose en el ID del producto
            $vehiculosUnicos = [];
            $idsVistos = [];
            
            foreach ($vehiculos as $vehiculo) {
                if (!in_array($vehiculo['idproductosv2'], $idsVistos)) {
                    $vehiculosUnicos[] = $vehiculo;
                    $idsVistos[] = $vehiculo['idproductosv2'];
                }
            }

            echo json_encode([
                'success' => true,
                'vehiculos' => $vehiculosUnicos
            ]);

        } catch (Exception $e) {
            error_log("Error en obtenerVehiculos: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener vehículos',
                'vehiculos' => []
            ]);
        }
    }

    /**
     * Obtener el nombre de la categoría por su ID
     */
    private function obtenerNombreCategoria($categoriaId)
    {
        try {
            // Si ya es un nombre (no es numérico), devolverlo tal como está
            if (!is_numeric($categoriaId)) {
                return $categoriaId;
            }

            // Conectar a la base de datos usando el mismo método que el modelo
            $conexion = new Conexion();
            $conn = $conexion->getConexion();

            $sql = "SELECT nombre FROM categoria_producto WHERE idcategoria_producto = ?";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Error al preparar consulta de categoría: " . $conn->error);
                return $categoriaId; // Devolver el ID si hay error
            }

            $stmt->bind_param('i', $categoriaId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $stmt->close();
                return $row['nombre'];
            } else {
                $stmt->close();
                return $categoriaId; // Devolver el ID si no se encuentra
            }

        } catch (Exception $e) {
            error_log("Error en obtenerNombreCategoria: " . $e->getMessage());
            return $categoriaId; // Devolver el ID si hay error
        }
    }

}
