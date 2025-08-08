<?php
/**
 * Funciones para el filtro de calendario del dashboard
 */

/**
 * Obtiene el producto más vendido en un período
 */
function obtenerProductoMasVendido($conexion, $empresa, $sucursal, $fecha_inicio, $fecha_fin)
{
    $sql = "SELECT 
            pv2.idproductosv2,
            pv2.nombre,
            SUM(pv.cantidad) as total_vendido
        FROM 
            productos_ventas pv
            INNER JOIN productosv2 pv2 ON pv.id_producto = pv2.idproductosv2
            INNER JOIN ventas v ON pv.id_venta = v.id_venta
        WHERE 
            v.estado = '1' 
            AND v.id_empresa = ?
            AND v.sucursal = ?
            AND v.fecha_emision BETWEEN ? AND ?
        GROUP BY 
            pv.id_producto, pv2.nombre
        ORDER BY 
            total_vendido DESC
        LIMIT 1";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('ssss', $empresa, $sucursal, $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado && $resultado->num_rows > 0) {
        return $resultado->fetch_assoc();
    } else {
        return [
            'nombre' => 'No hay productos vendidos',
            'total_vendido' => 0
        ];
    }
}

/**
 * Obtiene el total de ventas en un período
 */
function obtenerTotalVentas($conexion, $empresa, $sucursal, $fecha_inicio, $fecha_fin)
{
    $sql = "SELECT 
            COALESCE(SUM(total), 0) as total_ventas,
            COALESCE(SUM(CASE WHEN id_tido = 1 THEN total ELSE 0 END), 0) as total_boletas,
            COALESCE(SUM(CASE WHEN id_tido = 2 THEN total ELSE 0 END), 0) as total_facturas,
            COUNT(*) as cantidad_ventas
        FROM 
            ventas 
        WHERE 
            estado = '1' 
            AND id_empresa = ?
            AND sucursal = ?
            AND fecha_emision BETWEEN ? AND ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('ssss', $empresa, $sucursal, $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado && $resultado->num_rows > 0) {
        return $resultado->fetch_assoc();
    } else {
        return [
            'total_ventas' => 0,
            'total_boletas' => 0,
            'total_facturas' => 0,
            'cantidad_ventas' => 0
        ];
    }
}
?>

