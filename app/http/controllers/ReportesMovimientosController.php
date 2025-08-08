<?php


require_once "app/models/Reportes.php";
require_once "app/models/Usuario.php";
require_once "app/models/Productov2.php";

class ReportesMovimientosController extends Controller
{
    public function chargedReportAlmacen()
    {
        // Verificar si se recibe la página y el límite, si no, usar valores por defecto
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;

        // Instanciar el modelo
        $reporteModel = new Reportes();

        // Obtener los movimientos
        $movimientos = $reporteModel->obtenerMovimientos($pagina, $limite);

        $totalRegistros = $reporteModel->contarTotalMovimientos(); // 💡 Nuevo: Obtener el total de registros
        $totalPaginas = ceil($totalRegistros / $limite); // 💡 Calcular el total de páginas

        header('Content-Type: application/json');
        echo json_encode([
            'movimientos' => $movimientos, // Datos de la tabla
            'totalPaginas' => $totalPaginas // Total de páginas para el paginador
        ]);
    }

    public function chargedUsuarios() {
        $usuarioModel = new Usuario(); // Instancia del modelo
        $usuarios = $usuarioModel->getAll(); // Obtener usuarios desde la BD

        if (!empty($usuarios)) {
            echo json_encode(["success" => true, "usuarios" => $usuarios]);
        } else {
            echo json_encode(["success" => false, "usuarios" => []]);
        }
    }

    public function filtrarMovimientos()
    {
        $reporte = new Reportes();
        
        // Recibir parámetros del AJAX
        $fechaInicio = $_GET['fechaInicio'] ?? null;
        $fechaFin = $_GET['fechaFin'] ?? null;
        $tipoMovimiento = $_GET['tipoMovimiento'] ?? null;
        $subtipoMovimiento = $_GET['subtipoMovimiento'] ?? null;
        $buscarProducto = $_GET['buscarProducto'] ?? null;
        $filtroUsuario = $_GET['filtroUsuario'] ?? null;
    
        // Ajuste para la nueva opción "Ajuste de Inventario"
        if ($subtipoMovimiento === "Ajuste de Inventario") { 
            $subtipoMovimiento = "Ajuste"; // Cambiar para que coincida con la base de datos
        }
    
        // Inicializar resultados
        $resultados = [];
    
        // Aplicar filtros en el orden correcto, priorizando la base de datos
        if (!empty($fechaInicio) && !empty($fechaFin)) {
            // Si hay filtro de fechas, comienza con este
            $resultados = $reporte->filtrarPorFecha($fechaInicio, $fechaFin);
            
            // Aplicar filtros adicionales en PHP si es necesario
            if (!empty($tipoMovimiento)) {
                $resultados = array_filter($resultados, function ($mov) use ($tipoMovimiento, $subtipoMovimiento) {
                    $tipoCoincide = $mov['tipo_movimiento'] === $tipoMovimiento;
                    $subtipoCoincide = empty($subtipoMovimiento) || $mov['subtipo_movimiento'] === $subtipoMovimiento;
                    return $tipoCoincide && $subtipoCoincide;
                });
            }
            
            if (!empty($buscarProducto)) {
                $resultados = array_filter($resultados, function ($mov) use ($buscarProducto) {
                    return stripos($mov['nombre_producto'], $buscarProducto) !== false || 
                           stripos($mov['codigo_producto'], $buscarProducto) !== false;
                });
            }
            
            if (!empty($filtroUsuario)) {
                $resultados = array_filter($resultados, function ($mov) use ($filtroUsuario) {
                    return $mov['usuario_id'] == $filtroUsuario;
                });
            }
        }
        elseif (!empty($buscarProducto)) {
            // Si no hay filtro de fechas pero hay búsqueda de producto
            $resultados = $reporte->buscarPorProducto($buscarProducto);
            
            // Aplicar filtros adicionales en PHP
            if (!empty($tipoMovimiento)) {
                $resultados = array_filter($resultados, function ($mov) use ($tipoMovimiento, $subtipoMovimiento) {
                    $tipoCoincide = $mov['tipo_movimiento'] === $tipoMovimiento;
                    $subtipoCoincide = empty($subtipoMovimiento) || $mov['subtipo_movimiento'] === $subtipoMovimiento;
                    return $tipoCoincide && $subtipoCoincide;
                });
            }
            
            if (!empty($filtroUsuario)) {
                $resultados = array_filter($resultados, function ($mov) use ($filtroUsuario) {
                    return $mov['usuario_id'] == $filtroUsuario;
                });
            }
            
            if (!empty($fechaInicio) || !empty($fechaFin)) {
                // Si hay solo una de las fechas
                $resultados = array_filter($resultados, function ($mov) use ($fechaInicio, $fechaFin) {
                    $fechaMov = substr($mov['fecha'], 0, 10); // Obtener solo la fecha sin hora
                    
                    if (!empty($fechaInicio) && !empty($fechaFin)) {
                        return $fechaMov >= $fechaInicio && $fechaMov <= $fechaFin;
                    } elseif (!empty($fechaInicio)) {
                        return $fechaMov >= $fechaInicio;
                    } elseif (!empty($fechaFin)) {
                        return $fechaMov <= $fechaFin;
                    }
                    
                    return true;
                });
            }
        }
        elseif (!empty($tipoMovimiento)) {
            // Si no hay fechas ni búsqueda de producto pero hay tipo de movimiento
            $resultados = $reporte->filtrarPorTipoMovimiento($tipoMovimiento, $subtipoMovimiento);
            
            // Aplicar filtros adicionales en PHP
            if (!empty($filtroUsuario)) {
                $resultados = array_filter($resultados, function ($mov) use ($filtroUsuario) {
                    return $mov['usuario_id'] == $filtroUsuario;
                });
            }
            
            if (!empty($fechaInicio) || !empty($fechaFin)) {
                // Si hay solo una de las fechas
                $resultados = array_filter($resultados, function ($mov) use ($fechaInicio, $fechaFin) {
                    $fechaMov = substr($mov['fecha'], 0, 10); // Obtener solo la fecha sin hora
                    
                    if (!empty($fechaInicio) && !empty($fechaFin)) {
                        return $fechaMov >= $fechaInicio && $fechaMov <= $fechaFin;
                    } elseif (!empty($fechaInicio)) {
                        return $fechaMov >= $fechaInicio;
                    } elseif (!empty($fechaFin)) {
                        return $fechaMov <= $fechaFin;
                    }
                    
                    return true;
                });
            }
        }
        elseif (!empty($filtroUsuario)) {
            // Si solo hay filtro de usuario
            $resultados = $reporte->filtrarPorUsuario($filtroUsuario);
            
            // Aplicar filtros adicionales en PHP si es necesario
            if (!empty($fechaInicio) || !empty($fechaFin)) {
                $resultados = array_filter($resultados, function ($mov) use ($fechaInicio, $fechaFin) {
                    $fechaMov = substr($mov['fecha'], 0, 10); // Obtener solo la fecha sin hora
                    
                    if (!empty($fechaInicio) && !empty($fechaFin)) {
                        return $fechaMov >= $fechaInicio && $fechaMov <= $fechaFin;
                    } elseif (!empty($fechaInicio)) {
                        return $fechaMov >= $fechaInicio;
                    } elseif (!empty($fechaFin)) {
                        return $fechaMov <= $fechaFin;
                    }
                    
                    return true;
                });
            }
        }
        else {
            // Si no hay ningún filtro, obtener todos los movimientos
            $resultados = $reporte->obtenerMovimientos();
        }
    
        // Reindexar resultados para JSON
        $resultados = array_values($resultados);
    
        echo json_encode($resultados);
    }
    
    public function verProductoReporte() {
        if (isset($_GET['id'])) {
            $codigo = trim($_GET['id']);
            
            $modelo = new Productov2();
    
            // Obtener los datos del producto
            $producto = $modelo->getdataForBarcode($codigo);
    
            // Preparar la respuesta
            if ($producto) {
                $response = [
                    'success' => true,
                    'producto' => [
                        'nombre' => $producto['nombre'] ?? 'No disponible',
                        'codigo' => $producto['codigo'] ?? 'No disponible',
                        'cantidad' => $producto['cantidad'] ?? '0',
                        'ruc' => $producto['ruc'] ?? 'No disponible',
                        'razon_social' => $producto['razon_social'] ?? 'No disponible',
                        'fecha_registro' => $producto['fecha_registro'] ?? 'No disponible',
                        'fecha_vencimiento' => $producto['fecha_vencimiento'] ?? 'No disponible',
                    ]
                ];
            } else {
                $response = ['success' => false];
            }
    
            // Devolver respuesta como JSON
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false]);
        }
    }
   

}