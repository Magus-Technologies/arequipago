<?php
require_once 'app/models/Cupon.php';
require_once 'app/models/Cliente.php';
require_once 'app/models/Conductor.php';

class CuponController
{
    private $request;

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function buscarUsuarios()
    {
        try {
            $term = $_POST['term'] ?? '';
            $tipo = $_POST['tipo'] ?? 'conductor'; // Por defecto conductor para compatibilidad

            if ($tipo === 'cliente') {
                $cuponModel = new Cupon();
                $usuarios = $cuponModel->buscarClientes($term);
                
                // Agregar información de cupones existentes
                foreach ($usuarios as &$usuario) {
                    $cupones = $cuponModel->verificarClienteTieneCupon($usuario['id']);
                    $usuario['tiene_cupones'] = !empty($cupones);
                    $usuario['total_cupones'] = count($cupones);
                }
            } else {
                $conductorModel = new Conductor();
                $usuarios = $conductorModel->buscarConductoresParaCupon($term);
                
                // Agregar información de cupones existentes
                $cuponModel = new Cupon();
                foreach ($usuarios as &$usuario) {
                    $cupones = $cuponModel->verificarConductorTieneCupon($usuario['id_conductor']);
                    $usuario['tiene_cupones'] = !empty($cupones);
                    $usuario['total_cupones'] = count($cupones);
                }
            }

            header('Content-Type: application/json');
            echo json_encode($usuarios);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al buscar usuarios: ' . $e->getMessage()]);
        }
    }

    /**
     * Crear un nuevo cupón - CORREGIDO
     */
    public function crearCupon()
    {
        try {
            // Validaciones básicas
            if (empty($_POST['titulo']) || empty($_POST['tipoDescuento']) || empty($_POST['valor'])) {
                echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
                return;
            }

            if (empty($_POST['fechaInicio']) || empty($_POST['fechaFin'])) {
                echo json_encode(['success' => false, 'message' => 'Las fechas de inicio y fin son obligatorias.']);
                return;
            }

            // Validar fechas
            $fechaInicio = new DateTime($_POST['fechaInicio']);
            $fechaFin = new DateTime($_POST['fechaFin']);

            if ($fechaFin <= $fechaInicio) {
                echo json_encode(['success' => false, 'message' => 'La fecha de fin debe ser posterior a la fecha de inicio.']);
                return;
            }

            // Procesar conductores y clientes
            $conductoresJson = $_POST['conductores'] ?? '[]';
            $idConductores = json_decode($conductoresJson, true);
            
            $clientesJson = $_POST['clientes'] ?? '[]';
            $idClientes = json_decode($clientesJson, true);

            // Validar que al menos se seleccione un usuario
            if (empty($idConductores) && empty($idClientes)) {
                echo json_encode(['success' => false, 'message' => 'Debe seleccionar al menos un conductor o cliente.']);
                return;
            }

            // Preparar usuarios para verificación
            $usuariosParaVerificar = [];
            if (!empty($idConductores)) {
                foreach ($idConductores as $id) {
                    $usuariosParaVerificar[] = ['tipo' => 'conductor', 'id' => $id];
                }
            }
            if (!empty($idClientes)) {
                foreach ($idClientes as $id) {
                    $usuariosParaVerificar[] = ['tipo' => 'cliente', 'id' => $id];
                }
            }

            // Verificar usuarios con cupones activos
            $cuponModel = new Cupon();
            $usuariosConCupones = $cuponModel->verificarUsuariosConCuponesActivos($usuariosParaVerificar);

            if (!empty($usuariosConCupones)) {
                $nombresConCupones = [];
                foreach ($usuariosConCupones as $usuario) {
                    if ($usuario['tipo'] === 'conductor') {
                        $conductorModel = new Conductor();
                        $conductor = $conductorModel->obtenerDatosConductor($usuario['id']);
                        if ($conductor) {
                            $nombresConCupones[] = 'Conductor: ' . $conductor->getNombres() . ' ' . $conductor->getApellidoPaterno();
                        }
                    } else {
                        // Para clientes necesitarías crear un método similar en el modelo Cliente
                        $nombresConCupones[] = 'Cliente ID: ' . $usuario['id'];
                    }
                }

                echo json_encode([
                    'success' => false,
                    'message' => 'Los siguientes usuarios ya tienen cupones activos: ' . implode(', ', $nombresConCupones)
                ]);
                return;
            }

            // Procesar imagen banner
            $imagenBanner = null;
            if (isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
                $imagenBanner = $this->procesarImagenBanner($_FILES['banner']);
                if (!$imagenBanner) {
                    echo json_encode(['success' => false, 'message' => 'Error al procesar la imagen del banner.']);
                    return;
                }
            }

            // Preparar datos del cupón
            $datosCupon = [
                'titulo' => trim($_POST['titulo']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'tipo_descuento' => $_POST['tipoDescuento'],
                'valor' => floatval($_POST['valor']),
                'imagen_banner' => $imagenBanner,
                'fecha_inicio' => $_POST['fechaInicio'],
                'fecha_fin' => $_POST['fechaFin'],
                'limite_usos_conductor' => !empty($_POST['limitePorConductor']) ? intval($_POST['limitePorConductor']) : 1,
                'limite_usos_total' => !empty($_POST['limiteTotal']) ? intval($_POST['limiteTotal']) : null,
                'activo' => isset($_POST['activo']) ? 1 : 0,
            ];

            // CORREGIDO: Crear cupón UNA SOLA VEZ
            $idCupon = $cuponModel->crear($datosCupon);

            if ($idCupon) {
                // Procesar usuarios (conductores y/o clientes)
                $usuarios = [];

                // Procesar conductores si existen
                if (!empty($idConductores)) {
                    foreach ($idConductores as $idConductor) {
                        $usuarios[] = [
                            'tipo' => 'conductor',
                            'id' => $idConductor
                        ];
                    }
                }

                // Procesar clientes si existen
                if (!empty($idClientes)) {
                    foreach ($idClientes as $idCliente) {
                        $usuarios[] = [
                            'tipo' => 'cliente',
                            'id' => $idCliente
                        ];
                    }
                }

                // Asignar a usuarios
                $resultado = $cuponModel->asignarAUsuarios($idCupon, $usuarios);

                if ($resultado) {
                    $totalConductores = count($idConductores);
                    $totalClientes = count($idClientes);
                    $mensaje = "Cupón creado y asignado correctamente a {$totalConductores} conductor(es) y {$totalClientes} cliente(s).";
                    
                    echo json_encode([
                        'success' => true,
                        'message' => $mensaje,
                        'cupon_id' => $idCupon
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cupón creado pero error al asignar a usuarios.']);
                }
            } else {
                // Limpiar imagen si falló la creación
                if ($imagenBanner && file_exists($imagenBanner)) {
                    unlink($imagenBanner);
                }
                echo json_encode(['success' => false, 'message' => 'Error al crear el cupón.']);
            }
        } catch (Exception $e) {
            error_log('Error en CuponController::crearCupon(): ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
        }
    }

    /**
     * Listar todos los cupones
     */
    public function listarCupones()
    {
        try {
            $cuponModel = new Cupon();
            $cupones = $cuponModel->obtenerTodos();

            header('Content-Type: application/json');
            echo json_encode($cupones);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al obtener cupones: ' . $e->getMessage()]);
        }
    }

    /**
     * CORREGIDO: Obtener usuarios asignados a un cupón específico
     */
    public function obtenerUsuariosCupon()
    {
        try {
            $idCupon = $_POST['id_cupon'] ?? '';

            if (empty($idCupon)) {
                echo json_encode(['error' => 'ID de cupón requerido']);
                return;
            }

            $cuponModel = new Cupon();
            $usuarios = $cuponModel->obtenerUsuariosPorCupon($idCupon);

            header('Content-Type: application/json');
            echo json_encode($usuarios);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al obtener usuarios del cupón: ' . $e->getMessage()]);
        }
    }

    /**
     * MANTENER COMPATIBILIDAD: Buscar conductores (método anterior)
     */
    public function buscarConductores()
    {
        try {
            $term = $_POST['term'] ?? '';
            $conductorModel = new Conductor();
            $conductores = $conductorModel->buscarConductoresParaCupon($term);
            
            // Agregar información de cupones existentes
            $cuponModel = new Cupon();
            foreach ($conductores as &$conductor) {
                $cupones = $cuponModel->verificarConductorTieneCupon($conductor['id_conductor']);
                $conductor['tiene_cupones'] = !empty($cupones);
                $conductor['total_cupones'] = count($cupones);
            }

            header('Content-Type: application/json');
            echo json_encode($conductores);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al buscar conductores: ' . $e->getMessage()]);
        }
    }

    /**
     * MANTENER COMPATIBILIDAD: Obtener conductores del cupón (método anterior)
     */
    public function obtenerConductoresCupon()
    {
        // Redirigir al nuevo método
        $this->obtenerUsuariosCupon();
    }

    /**
     * Verificar si conductores tienen cupones activos
     */
    public function verificarConductoresConCupones()
    {
        try {
            $conductoresIds = json_decode($_POST['conductores_ids'] ?? '[]', true);

            if (empty($conductoresIds)) {
                echo json_encode(['conductores_con_cupones' => []]);
                return;
            }

            $cuponModel = new Cupon();
            $conductoresConCupones = $cuponModel->verificarConductoresConCuponesActivos($conductoresIds);

            header('Content-Type: application/json');
            echo json_encode(['conductores_con_cupones' => $conductoresConCupones]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al verificar conductores: ' . $e->getMessage()]);
        }
    }

    /**
     * Verificar cupones de un conductor específico
     */
    public function verificarCuponConductor($idConductor = null, $request = null)
    {
        try {
            // Obtener ID del conductor desde diferentes fuentes
            if (empty($idConductor)) {
                $idConductor = $_POST['id_conductor'] ?? $_GET['id_conductor'] ?? '';
            }

            if (empty($idConductor)) {
                echo json_encode(['error' => 'ID de conductor requerido']);
                return;
            }

            $cuponModel = new Cupon();
            $cupones = $cuponModel->verificarConductorTieneCupon($idConductor);

            // Obtener datos del conductor
            $conductorModel = new Conductor();
            $conductor = $conductorModel->obtenerDatosConductor($idConductor);

            $response = [
                'conductor' => $conductor ? [
                    'id' => $conductor->getIdConductor(),
                    'nombres' => $conductor->getNombres(),
                    'apellido_paterno' => $conductor->getApellidoPaterno(),
                    'apellido_materno' => $conductor->getApellidoMaterno(),
                    'foto' => $conductor->getFoto()
                ] : null,
                'cupones' => $cupones,
                'tiene_cupones' => !empty($cupones),
                'total_cupones' => count($cupones)
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al verificar cupones del conductor: ' . $e->getMessage()]);
        }
    }

    /**
     * CORREGIDO: Listar cupones con sus usuarios asignados
     */
    public function listarCuponesConConductores()
    {
        try {
            $cuponModel = new Cupon();
            $cupones = $cuponModel->obtenerTodos();

            // Enriquecer cada cupón con la lista de usuarios
            foreach ($cupones as &$cupon) {
                $cupon['usuarios'] = $cuponModel->obtenerUsuariosPorCupon($cupon['id']);
                // Mantener compatibilidad
                $cupon['conductores'] = array_filter($cupon['usuarios'], function($u) {
                    return $u['tipo_usuario'] === 'conductor';
                });
            }

            header('Content-Type: application/json');
            echo json_encode($cupones);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al obtener cupones con usuarios: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtener estadísticas de uso de cupones
     */
    public function obtenerEstadisticasUso($id = null, $request = null)
    {
        try {
            // Obtener ID del cupón desde diferentes fuentes
            $idCupon = $id ?? $_GET['id_cupon'] ?? $_GET['id'] ?? null;

            $cuponModel = new Cupon();
            $estadisticas = $cuponModel->obtenerEstadisticasUso($idCupon);

            header('Content-Type: application/json');
            echo json_encode($estadisticas);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al obtener estadísticas: ' . $e->getMessage()]);
        }
    }

    public function registrarUsoCupon()
    {
        try {
            $idCupon = $_POST['id_cupon'] ?? '';
            $idConductor = $_POST['id_conductor'] ?? '';
            $idCliente = $_POST['id_cliente'] ?? '';
            $montoDescuento = $_POST['monto_descuento'] ?? 0;

            if (empty($idCupon) || (empty($idConductor) && empty($idCliente))) {
                echo json_encode(['success' => false, 'message' => 'ID de cupón e ID de usuario son requeridos']);
                return;
            }

            $cuponModel = new Cupon();
            $resultado = $cuponModel->registrarUso($idCupon, $idConductor, $montoDescuento, $idCliente);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Uso de cupón registrado correctamente',
                    'uso_id' => $resultado
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al registrar el uso del cupón']);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al registrar uso: ' . $e->getMessage()]);
        }
    }

    /**
     * Procesar imagen del banner
     */
    private function procesarImagenBanner($archivo)
    {
        try {
            // Validar tamaño (máximo 2MB)
            if ($archivo['size'] > 2 * 1024 * 1024) {
                return false;
            }

            // Validar tipo de archivo
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($archivo['type'], $tiposPermitidos)) {
                return false;
            }

            // Crear directorio si no existe
            $rutaDirectorio = 'public/img/cupones/';
            if (!file_exists($rutaDirectorio)) {
                mkdir($rutaDirectorio, 0777, true);
            }

            // Generar nombre único
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid('cupon_', true) . '.' . $extension;
            $rutaCompleta = $rutaDirectorio . $nombreArchivo;

            // Mover archivo
            if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                return 'img/cupones/' . $nombreArchivo; // Ruta relativa para la BD
            }

            return false;
        } catch (Exception $e) {
            error_log('Error en procesarImagenBanner: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si un usuario específico (conductor o cliente) ya usó un cupón específico
     */
    public function verificarUsoCupon($tipo = null, $idUsuario = null, $idCupon = null)
    {
        try {
            // Obtener parámetros desde la URL o POST/GET
            $tipo = $tipo ?? $_POST['tipo'] ?? $_GET['tipo'] ?? '';
            $idUsuario = $idUsuario ?? $_POST['id_usuario'] ?? $_GET['id_usuario'] ?? '';
            $idCupon = $idCupon ?? $_POST['id_cupon'] ?? $_GET['id_cupon'] ?? '';

            // Validaciones
            if (empty($tipo) || empty($idUsuario) || empty($idCupon)) {
                echo json_encode(['error' => 'Tipo, ID de usuario e ID de cupón son requeridos']);
                return;
            }

            if (!in_array($tipo, ['conductor', 'cliente'])) {
                echo json_encode(['error' => 'Tipo de usuario inválido. Debe ser "conductor" o "cliente"']);
                return;
            }

            $cuponModel = new Cupon();
            $usuario = null;
            $usoInfo = null;

            // Lógica según el tipo de usuario
            if ($tipo === 'conductor') {
                $usoInfo = $cuponModel->verificarUsoCuponEspecifico($idUsuario, $idCupon);
                $conductorModel = new Conductor();
                $conductor = $conductorModel->obtenerDatosConductor($idUsuario);
                if ($conductor) {
                    $usuario = [
                        'id' => $conductor->getIdConductor(),
                        'nombres' => $conductor->getNombres(),
                        'apellido_paterno' => $conductor->getApellidoPaterno(),
                        'apellido_materno' => $conductor->getApellidoMaterno(),
                        'foto' => $conductor->getFoto()
                    ];
                }
            } else { // cliente
                $usoInfo = $cuponModel->verificarUsoCuponEspecificoCliente($idUsuario, $idCupon);
                $clienteModel = new Cliente();
                $clienteData = $clienteModel->getClienteById($idUsuario); // Se usa el método que encontramos
                if ($clienteData) {
                    $usuario = [
                        'id' => $clienteData['id'],
                        'nombres' => $clienteData['nombres'],
                        'apellido_paterno' => $clienteData['apellido_paterno'],
                        'apellido_materno' => $clienteData['apellido_materno'],
                        'foto' => '/arequipago/public/img/default-user.png' // Foto por defecto para clientes
                    ];
                }
            }

            // Obtener datos del cupón
            $cuponInfo = $cuponModel->obtenerCuponPorId($idCupon);
$response = [
    'usuario' => $usuario,
    'cupon' => $cuponInfo,
    'tipo_usuario' => $tipo,
    'id_cliente' => $tipo === 'cliente' ? (int)$idUsuario : null,
    'id_conductor' => $tipo === 'conductor' ? (int)$idUsuario : null,
    'ha_usado' => $usoInfo['ha_usado'] ?? false,
    'veces_usado' => $usoInfo['veces_usado'] ?? 0,
    'ultimo_uso' => $usoInfo['ultimo_uso'] ?? null,
    'total_descontado' => $usoInfo['total_descontado'] ?? 0,
    'puede_usar_mas' => $usoInfo['puede_usar_mas'] ?? true,
    'limite_alcanzado' => $usoInfo['limite_alcanzado'] ?? false,
    'historial_usos' => $usoInfo['historial_usos'] ?? []
];



            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al verificar uso del cupón: ' . $e->getMessage()]);
        }
    }

    /**
     * Usar cupón por código
     */
    public function usarCuponPorCodigo($tipo = null, $idUsuario = null, $idCupon = null)
    {
        try {
            // Obtener parámetros desde diferentes fuentes
            $tipo = $tipo ?? $_POST['tipo'] ?? $_GET['tipo'] ?? 'conductor';
            $idUsuario = $idUsuario ?? $_POST['id_usuario'] ?? $_GET['id_usuario'] ?? '';
            $idCupon = $idCupon ?? $_POST['id_cupon'] ?? $_GET['id_cupon'] ?? '';
            
            // Para compatibilidad hacia atrás
            if (empty($idUsuario)) {
                $idUsuario = $_POST['id_conductor'] ?? $_GET['id_conductor'] ?? '';
                $tipo = 'conductor';
            }

            if (empty($idCupon) || empty($idUsuario)) {
                echo json_encode(['success' => false, 'message' => 'ID de cupón e ID de usuario son requeridos']);
                return;
            }

            // Validar tipo de usuario
            if (!in_array($tipo, ['conductor', 'cliente'])) {
                echo json_encode(['success' => false, 'message' => 'Tipo de usuario inválido. Debe ser "conductor" o "cliente"']);
                return;
            }

            $cuponModel = new Cupon();

            // Obtener cupón por ID
            $cuponInfo = $cuponModel->obtenerCuponPorId($idCupon);
            if (!$cuponInfo) {
                echo json_encode(['success' => false, 'message' => 'Cupón inválido o expirado']);
                return;
            }

            // Verificar límites de uso
            if ($tipo === 'conductor') {
                $usoInfo = $cuponModel->verificarUsoCuponEspecifico($idUsuario, $idCupon);
            } else {
                // Para clientes, implementar verificación similar
                $usoInfo = ['limite_alcanzado' => false]; // Simplificado por ahora
            }
            
            if ($usoInfo['limite_alcanzado']) {
                echo json_encode(['success' => false, 'message' => 'Ya has alcanzado el límite de usos para este cupón']);
                return;
            }

            // Registrar uso según el tipo de usuario
            if ($tipo === 'conductor') {
                $resultado = $cuponModel->registrarUso($idCupon, $idUsuario, 0, null);
            } else {
                $resultado = $cuponModel->registrarUso($idCupon, null, 0, $idUsuario);
            }
            
            
            if ($resultado) {
      echo json_encode([
    'success' => true,
    'message' => 'Cupón aplicado correctamente',
    'cupon' => [
        'tipo_descuento' => $cuponInfo['tipo_descuento'],
        'valor' => $cuponInfo['valor']
    ],
    'tipo_usuario' => $tipo,
    'id_cliente' => $tipo === 'cliente' ? (int)$idUsuario : null,
    'id_conductor' => $tipo === 'conductor' ? (int)$idUsuario : null,
    'uso_id' => $resultado
]);


            } else {
                echo json_encode(['success' => false, 'message' => 'Error al aplicar el cupón']);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al usar cupón: ' . $e->getMessage()]);
        }
    }

    /**
     * Calcular descuento basado en el tipo de cupón
     */
    private function calcularDescuento($cuponInfo, $montoTotal)
    {
        if ($cuponInfo['tipo_descuento'] === 'porcentaje') {
            return round(($montoTotal * $cuponInfo['valor']) / 100, 2);
        } else { // monto_fijo
            return min($cuponInfo['valor'], $montoTotal); // No puede ser mayor al total
        }
    }
}