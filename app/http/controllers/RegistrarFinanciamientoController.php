<?php
require_once "app/models/Financiamiento.php";
require_once "app/models/CuotaFinanciamiento.php";
require_once "app/models/Conductor.php";
require_once "app/models/Productov2.php";
require_once "app/models/Cliente.php";
require_once "app/http/controllers/FinanciamientoController.php";

class RegistrarFinanciamientoController extends Controller
{
    private $conexion;
    
    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();

    }

    public function guardarFinanciamiento()
    {
        try {
            // Obtener los datos recibidos por POST
            $datos = $_POST;
            // Obtener valor de cobrar mora con valor por defecto
            $datos['cobrar_mora'] = isset($datos['cobrar_mora']) ? intval($datos['cobrar_mora']) : 1;

            $fechasVencimiento = $datos['fechas_vencimiento'];
            

            $camposRequeridos = ['id_producto', 'monto_total', 'grupo_financiamiento', 'cuotas', 'estado', 'fecha_inicio', 'fecha_fin', 'fecha_creacion', 'cantidad_producto'];
            foreach ($camposRequeridos as $campo) {
                if (empty($datos[$campo])) {
                    throw new Exception("Falta el campo obligatorio: $campo");
                }
            }


            // NUEVO: Verificar que al menos uno de id_conductor o id_cliente esté presente
            if (empty($datos['id_conductor']) && empty($datos['id_cliente'])) {
                throw new Exception("Debe especificar al menos un id_conductor o id_cliente");
            }

            // Obtener usuario_id de la sesión 🔹 Agregado para obtener el usuario
            $usuario_id = $_SESSION['usuario_id'] ?? null;
            if (!$usuario_id) {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo obtener el ID del usuario.']);
                return;
            }

            $rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;

            // Instanciar el modelo ProductoV2 para actualizar el stock
            $productoModel = new ProductoV2(); // 🔹 Agregué esta línea para instanciar el modelo ProductoV2
            
            // 💥 Modificado: Obtener los datos del producto antes de actualizar el stock
            $producto = $productoModel->obtenerProductoPorId($datos['id_producto']); 
            if (!$producto) {
                throw new Exception("No se encontró el producto con ID: " . $datos['id_producto']);
            }
            
            // 💥 Modificado: Determinar si el producto pertenece a una categoría especial
            $categoria_producto = strtolower(trim($producto['CATEGORIA']));
            $categorias_especiales = ['celular', 'celulares', 'llantas', 'llanta', 'aceites', 'aceite', 'baterias', 'batería', 'baterías', 'bateria'];
            $es_categoria_especial = false;
            
            foreach ($categorias_especiales as $categoria) {
                if (strpos($categoria_producto, $categoria) !== false) {
                    $es_categoria_especial = true;
                    break;
                }
            }

            // 💥 Modificado: Determinar si se debe registrar el movimiento en almacén
            $registrar_movimiento = true;
            $aprobado = 1; // Por defecto, aprobado

            // 💥 Modificado: Si es categoría especial y rol 2 (Asesor), no registrar movimiento
            if ($es_categoria_especial && $rol_usuario == 2) {
                $registrar_movimiento = false;
                $aprobado = 0; // No aprobado para asesores con productos especiales
            }

            // 💥 Modificado: Solo actualizar stock si se debe registrar el movimiento
            if ($registrar_movimiento) {
                $productoModel->actualizarStock($datos['id_producto'], $datos['cantidad_producto']);
            }

            // Determinar el código del producto 🔹 Agregado
            $codigo_producto = $producto['CODIGO'] ?? $producto['CODIGO_BARRA'];

            // Determinar la razón social 🔹 Agregado
            $razon_social = $producto['RAZON_SOCIAL'] ?? null;

            // Determinar el nombre del producto 🔹 Agregado
            $nombre_producto = $producto['NOMBRE'];



            $montoTotal = floatval(str_replace(['S/. ', 'US$ ', '$'], '', $datos['monto_total']));

            // 💥 Modificado: Añadir aprobado y usuario_id a los datos
            $datos['aprobado'] = $aprobado;   

            // Siempre pasar usuario_id para todos los financiamientos
            $datos['usuario_id'] = $usuario_id;

            $financiamientoModel = new Financiamiento();
            $idFinanciamiento = $financiamientoModel->guardarFinanciamiento($datos);

            // Después de obtener $idFinanciamiento
            $this->registrarComisionAutomatica($idFinanciamiento);

            $cuotas = $datos['cuotas'];
            $valorCuota = $datos['valorCuota'];

            // Iterar sobre las fechas de vencimiento y guardar cada cuota
            for ($i = 0; $i < count($fechasVencimiento); $i++) {
                $cuotaModel = new CuotaFinanciamiento();
                // Convertir la fecha de vencimiento a formato 'Y-m-d'
                $fechaVencimiento = date('Y-m-d', strtotime($fechasVencimiento[$i]));
                $cuotaModel->guardarCuota($idFinanciamiento, $i + 1, $valorCuota, $fechaVencimiento);
            }

            // 💥 Modificado: Solo registrar el movimiento si corresponde
            if ($registrar_movimiento) {
                // Registrar el movimiento en el almacén
                $reportesModel = new Reportes(); 
                $tipo_movimiento = "Salida";
                $subtipo_movimiento = "financiamiento";
                $cantidad_producto = $datos['cantidad_producto'];

                $reportesModel->registrarMovimiento(
                    $usuario_id, 
                    $datos['id_producto'], 
                    $codigo_producto, 
                    $nombre_producto, 
                    $tipo_movimiento, 
                    $subtipo_movimiento, 
                    $cantidad_producto, 
                    $razon_social
                );
            }


            $this->enviarRespuesta(true, 'Registro completado con éxito.', $idFinanciamiento);
        } catch (Exception $e) {
            $this->enviarRespuesta(false, 'Error: ' . $e->getMessage());
        }
    }

    // Método para buscar conductor
    public function buscarConductor() {
        header('Content-Type: application/json');
        // Recibimos el número de documento del front-end
        $nroDocumento = $_GET['nro_documento'];

        // Llamamos al modelo para buscar el conductor
        $conductorModel = new Conductor();
        $idConductor = $conductorModel->buscarPorDocumento($nroDocumento);

        // Verificamos si encontramos al conductor
        if ($idConductor) {
            echo json_encode(['success' => true, 'id_conductor' => $idConductor]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Conductor no encontrado']);
        }
    }

    private function enviarRespuesta($success, $message, $idFinanciamiento = null)
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($idFinanciamiento !== null) { // 🔹 Si hay un ID, lo agregamos a la respuesta
            $response['id_financiamiento'] = $idFinanciamiento;
        }

        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }


    function SaveFinanciamientoVehicular()
    {
        // 💥 Modificado: Obtenemos el rol del usuario desde la sesión
        $rol_usuario = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;
        // 💥 Modificado: Obtenemos el ID del usuario desde la sesión
        $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

        if (!$usuario_id) {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo obtener el ID del usuario.']);
            return;
        }
            
        if (!isset($_POST['fechasVencimiento']) || empty($_POST['fechasVencimiento'])) {  
            echo json_encode(['status' => 'error', 'message' => 'Las fechas de vencimiento son obligatorias.']); // Mensaje de error si no existen fechas
            return; // Detener la ejecución de la función si no hay fechas de vencimiento
        }
        
        // Recibir datos del POST
        $cliente = $_POST['cliente'];
        $idProducto = $_POST['idProducto'];
        $codigoAsociado = $_POST['codigoAsociado'];
        $grupo_financiamiento = $_POST['grupo_financiamiento'];
        $monto_total = $_POST['monto_total'];
        $cuota_inicial = $_POST['cuota_inicial'];
        $cuotas = $_POST['cuotas'];
        $valor_cuota = $_POST['valor_cuota'];
        $estado = $_POST['estado'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $fecha_creacion = $_POST['fecha_creacion'];
        $frecuencia_pago = $_POST['frecuencia_pago'];
        $second_product = $_POST['second_product'];
        $monto_inscrip = $_POST['monto_inscrip'];
        $moneda = $_POST['moneda'];
        $fechasVencimiento = $_POST['fechasVencimiento'];
        $monto_recalculado = $_POST['monto_recalculado'];
       
        $monto_sin_intereses = $_POST['monto_sin_intereses'];
        $tasa = isset($_POST['tasa']) && !empty($_POST['tasa']) ? $_POST['tasa'] : null; 
      
        // 🙂 Recibir el ID de la variante (puede ser null)
        $id_variante = (isset($_POST['id_variante']) && $_POST['id_variante'] !== '' && $_POST['id_variante'] != 0) ? intval($_POST['id_variante']) : null;
        // Recibir valor de cobrar mora
        $cobrar_mora = isset($_POST['cobrar_mora']) ? intval($_POST['cobrar_mora']) : 1;


        // Recibir id_conductor e id_cliente del POST - MODIFICADO: Ahora recibimos ambos IDs
        $idConductor = isset($_POST['id_conductor']) ? (intval($_POST['id_conductor']) !== 0 ? intval($_POST['id_conductor']) : null) : null; // ✅ MODIFICADO: Si id_conductor es 0, lo convertimos en null
    
        $idCliente = (isset($_POST['id_cliente']) && $_POST['id_cliente'] !== '' && $_POST['id_cliente'] != 0) ? intval($_POST['id_cliente']) : null;

        // Validar el idProducto
        $cantidad_producto = ($idProducto === "No disponible") ? 0 : 1;
        if ($idProducto === "No disponible") {
            $idProducto = 37;
        }
    
        // 💥 Modificado: Determinar el valor de aprobado según el rol
        $aprobado = ($rol_usuario == 2) ? 0 : 1;
    
        /*
        // Instanciar modelo Conductor y obtener el id_conductor
        $conductorModel = new Conductor();
        $idConductor = $conductorModel->buscarPorDocumento($cliente);
        */        
        
        // Preparar datos para insertar en la base de datos
        $datos = [
            'id_conductor' => $idConductor, // MODIFICADO: Ahora usamos el id_conductor del POST (puede ser null)
            'id_cliente' => $idCliente, 
            'id_producto' => intval($idProducto),
            'id_coti' => 0,
            'codigo_asociado' => intval($codigoAsociado),
            'grupo_financiamiento' => strval($grupo_financiamiento),
            'id_variante' => $id_variante,
            'cantidad_producto' => strval($cantidad_producto),
            'monto_total' => floatval($monto_total),
            'cuota_inicial' => floatval($cuota_inicial),
            'cuotas' => intval($cuotas),
            'estado' => strval($estado),
            'fecha_inicio' => strval($fecha_inicio),
            'fecha_fin' => strval($fecha_fin),
            'fecha_creacion' => strval($fecha_creacion),
            'frecuencia' => strval($frecuencia_pago),
            'second_product' => strval($second_product ?: ""),
            'monto_inscrip' => floatval($monto_inscrip),
            'moneda' => strval($moneda),
            'monto_recalculado' => floatval($monto_recalculado),
            'monto_sin_interes' => floatval($monto_sin_intereses),
            'tasa' => $tasa !== null ? floatval($tasa) : null, 
            // 💥 Modificado: Agregar usuario_id al array de datos si el rol es 2
            // Siempre agregar usuario_id al array de datos
            'usuario_id' => $usuario_id,
            // 💥 Modificado: Agregar aprobado al array de datos
            'aprobado' => $aprobado,
            'cobrar_mora' => $cobrar_mora
        ];
    
        // ⏩ Modificado: Comprobar si conexión está disponible
        if (!isset($this->conexion) || !$this->conexion) {
            echo "Error: No hay conexión a la base de datos disponible.";
            return;
        }
        
        $conexion = $this->conexion; // ⏩ Asegurar que la conexión esté disponible
        
        // 🙂 Modificar la consulta SQL para incluir id_variante
        $query = "INSERT INTO financiamiento 
        (id_conductor, id_cliente, idproductosv2, id_coti, codigo_asociado, grupo_financiamiento, id_variante, cantidad_producto, 
        monto_total, cuota_inicial, cuotas, estado, fecha_inicio, fecha_fin, fecha_creacion, 
        frecuencia, second_product, monto_inscrip, moneda, monto_recalculado, monto_sin_interes, tasa, usuario_id, aprobado, cobrar_mora)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($query);
    
        // ⏩ Añadido: Verificación de éxito en la preparación
        if ($stmt === false) {
            echo "Error en la preparación de la consulta: " . $conexion->error;
            return;
        }
    
        // ⭐ Modificado: Manejo adecuado de valores nulos
        $tipos = '';
        $params = [];
        
        // ⭐ Preparar los tipos y parámetros dinámicamente
        // Para cada valor, determinamos su tipo y lo añadimos a los arreglos
        
        // id_conductor (puede ser null)
        if ($datos['id_conductor'] === null) {
            $tipos .= 'i';
            $params[] = NULL;
        } else {
            $tipos .= 'i';
            $params[] = $datos['id_conductor'];
        }
        
        // id_cliente (puede ser null)
        if ($datos['id_cliente'] === null) {
            $tipos .= 'i';
            $params[] = NULL;
        } else {
            $tipos .= 'i';
            $params[] = $datos['id_cliente'];
        }
        
        // Resto de parámetros
        // id_producto
        $tipos .= 'i';
        $params[] = $datos['id_producto'];
        
        // id_coti
        $tipos .= 'i';
        $params[] = $datos['id_coti'];
        
        // codigo_asociado
        $tipos .= 's';
        $params[] = $datos['codigo_asociado'];
        
        // grupo_financiamiento
        $tipos .= 's';
        $params[] = $datos['grupo_financiamiento'];
        
        // id_variante (puede ser null)
        if ($datos['id_variante'] === null) {
            $tipos .= 'i';
            $params[] = NULL;
        } else {
            $tipos .= 'i';
            $params[] = $datos['id_variante'];
        }

        // cantidad_producto
        $tipos .= 's';
        $params[] = $datos['cantidad_producto'];
        
        // monto_total
        $tipos .= 'd';
        $params[] = $datos['monto_total'];
        
        // cuota_inicial
        $tipos .= 'd';
        $params[] = $datos['cuota_inicial'];
        
        // cuotas
        $tipos .= 'i';
        $params[] = $datos['cuotas'];
        
        // estado
        $tipos .= 's';
        $params[] = $datos['estado'];
        
        // fecha_inicio
        $tipos .= 's';
        $params[] = $datos['fecha_inicio'];
        
        // fecha_fin
        $tipos .= 's';
        $params[] = $datos['fecha_fin'];
        
        // fecha_creacion
        $tipos .= 's';
        $params[] = $datos['fecha_creacion'];
        
        // frecuencia
        $tipos .= 's';
        $params[] = $datos['frecuencia'];
        
        // second_product
        $tipos .= 's';
        $params[] = $datos['second_product'];
        
        // monto_inscrip
        $tipos .= 'd';
        $params[] = $datos['monto_inscrip'];
        
        // moneda
        $tipos .= 's';
        $params[] = $datos['moneda'];
        
        // monto_recalculado
        $tipos .= 'd';
        $params[] = $datos['monto_recalculado'];
        
        // monto_sin_interes
        $tipos .= 'd';
        $params[] = $datos['monto_sin_interes'];
        
        // tasa (puede ser null)
        if ($datos['tasa'] === null) {
            $tipos .= 'd';
            $params[] = NULL;
        } else {
            $tipos .= 'd';
            $params[] = $datos['tasa'];
        }
        
        // usuario_id (puede ser null)
        if ($datos['usuario_id'] === null) {
            $tipos .= 'i';
            $params[] = NULL;
        } else {
            $tipos .= 'i';
            $params[] = $datos['usuario_id'];
        }
        
        // aprobado (puede ser null)
        if ($datos['aprobado'] === null) {
            $tipos .= 'i';
            $params[] = NULL;
        } else {
            $tipos .= 'i';
            $params[] = $datos['aprobado'];
        }

        // cobrar_mora
        $tipos .= 'i';
        $params[] = $datos['cobrar_mora'];

        // ⭐ Modificado: Vinculación dinámica de parámetros
        $stmt->bind_param($tipos, ...$params);
    
        // AÑADIDO: Validación para asegurar que al menos uno de los IDs esté presente
        if ($idConductor === null && $idCliente === null) {
            echo json_encode(['status' => 'error', 'message' => 'Se requiere al menos un id_conductor o un id_cliente.']);
            return;
        }
    
        // Ejecutar el INSERT y obtener el id generado
        if ($stmt->execute()) {
            $idFinanciamiento = $conexion->insert_id;
          
        } else {
            echo "Error al registrar financiamiento: " . $stmt->error;
            return;
        }
    
        $stmt->close();
    
        // Después de obtener $idFinanciamiento
        $this->registrarComisionAutomatica($idFinanciamiento);

        $numeroCuotaInicial = isset($_POST['numeroCuotaInicial']) ? (int)$_POST['numeroCuotaInicial'] : 1; 
       
        // Guardar cuotas de financiamiento
        for ($i = 0; $i < count($fechasVencimiento); $i++) {
            $cuotaModel = new CuotaFinanciamiento();
            $fechaVencimiento = date('Y-m-d', strtotime($fechasVencimiento[$i]));
            $numeroCuota = $numeroCuotaInicial + $i;
            $cuotaModel->guardarCuota($idFinanciamiento, $numeroCuota, $valor_cuota, $fechaVencimiento);
        }
    
    
        // Registrar movimiento en el almacén solo si hay un id_producto válido
        if ($idProducto !== 37 && $rol_usuario != 2) {
    
            // Instanciar el modelo ProductoV2 para actualizar el stock
            $productoModel = new ProductoV2(); // 🔹 Agregué esta línea para instanciar el modelo ProductoV2
            $productoModel->actualizarStock($datos['id_producto'], $datos['cantidad_producto']); // 🔹 Llamo al método para actualizar el stock del producto
    
            // Obtener los datos del producto para registrar el movimiento 🔹 Agregado
            $producto = $productoModel->obtenerProductoPorId($datos['id_producto']); 
            if (!$producto) {
                throw new Exception("No se encontró el producto con ID: " . $datos['id_producto']);
            }
    
            // Determinar el código del producto 🔹 Agregado
            $codigo_producto = $producto['CODIGO'] ?? $producto['CODIGO_BARRA'];
    
            // Determinar la razón social 🔹 Agregado
            $razon_social = $producto['RAZON_SOCIAL'] ?? null;
    
            // Determinar el nombre del producto 🔹 Agregado
            $nombre_producto = $producto['NOMBRE'];
    
            $reportesModel = new Reportes();
            $tipo_movimiento = "Salida";
            $subtipo_movimiento = "financiamiento";
    
            $reportesModel->registrarMovimiento(
                $usuario_id, // Este valor debe estar definido en el controlador
                $idProducto,
                $codigo_producto, // Suponiendo que es el código del producto
                $nombre_producto, // Usando el nombre del grupo como nombre del producto
                $tipo_movimiento,
                $subtipo_movimiento,
                $cantidad_producto,
                $razon_social // Suponiendo que este valor también está definido
            );
        }
        
        echo json_encode(['status' => 'success', 'idFinanciamiento' => $idFinanciamiento]); // ← MODIFICADO: Agregado 'status' para consistencia
        exit; // ← AGREGADO: Asegurar que no haya más output después de enviar la respuesta JSON
    }

    public function buscarClienteExiste()
    {
        // Obtener el documento desde POST
        $dni = $_POST['dni'] ?? '';
        
        if (empty($dni)) {
            echo json_encode(['success' => false, 'message' => 'Documento no proporcionado']);
            return;
        }
        
        $conexion = (new Conexion())->getConexion();
        $sql = "SELECT id_cliente, datos, email, telefono, direccion FROM clientes WHERE documento = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            echo json_encode([
                'success' => true, 
                'existe' => true, 
                'id_cliente' => $fila['id_cliente'],
                'datos' => $fila['datos'],
                'email' => $fila['email'],
                'telefono' => $fila['telefono'],
                'direccion' => $fila['direccion']
            ]);
        } else {
            echo json_encode(['success' => true, 'existe' => false]);
        }
    }

    public function buscarOCrearCliente()
    {
        header('Content-Type: application/json');

        try {
            // Obtener datos del formulario
            $documento = $_POST['documento'] ?? '';
                      

            // Validar datos obligatorios
            if (empty($documento)) {                                     // MODIFICADO: Solo validamos documento como obligatorio
                throw new Exception("El número de documento es obligatorio");
            }
            
            // Instanciar el modelo Cliente
            $clienteModel = new Cliente();
            
            // Buscar si el cliente existe en clientes_financiar                  
            $clienteExistente = $clienteModel->buscarClienteFinanciar($documento); // MODIFICADO: Llamada al nuevo método
            
            if ($clienteExistente) {
                // Si existe, retornar su ID
                echo json_encode(['success' => true, 'id_cliente' => $clienteExistente['id'], 'message' => 'Cliente existente']); // MODIFICADO: Cambiado 'id_cliente' a 'id' según estructura de la tabla
                return;
            } 

            // Si no existe, retornar error                                       // MODIFICADO: Ya no crea cliente automáticamente
            throw new Exception("El cliente no está registrado en el sistema");

            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }

        private function registrarComisionAutomatica($idFinanciamiento) {
            // Verificar si el usuario puede tener comisión automática

            if (!in_array($_SESSION['id_rol'], [1,2, 3])) {
                return; // Solo roles 1 y 3 tienen comisión automática
            }
            
            // Obtener el financiamiento recién registrado
            $financiamientoModel = new Financiamiento();
            $financiamiento = $financiamientoModel->getFinanciamientoById($idFinanciamiento);

            
            if ($financiamiento) {
                // REUTILIZAR la misma lógica de comisiones que ya existe
                $financiamientoController = new FinanciamientoController();
                $financiamientoController->registrarComisionFinanciamiento($financiamiento);
            }
        }
}

