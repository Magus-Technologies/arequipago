<?php

class Reportes
{
    
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function registrarMovimiento($usuario_id, $id_producto, $codigo_producto, $nombre_producto, $tipo_movimiento, $subtipo_movimiento, $cantidad_producto, $razon_social = null)
    {
        // Fecha actual en formato DATETIME (YYYY-MM-DD HH:MM:SS)
        $fecha_actual = date('Y-m-d H:i:s');
        
        // Preparar la consulta
        $sql = "INSERT INTO movimientos_almacen (usuario_id, id_producto, codigo_producto, nombre_producto, tipo_movimiento, subtipo_movimiento, cantidad_producto, proveedor, fecha) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar la sentencia
        $stmt = $this->conectar->prepare($sql);
        
        // Verificar si la preparación fue exitosa
        if ($stmt) {
            // Enlazar parámetros
            $stmt->bind_param("iisssssss", $usuario_id, $id_producto, $codigo_producto, $nombre_producto, $tipo_movimiento, $subtipo_movimiento, $cantidad_producto, $razon_social, $fecha_actual);

            // Ejecutar la consulta
            $resultado = $stmt->execute();

            // Cerrar la sentencia
            $stmt->close();

            return $resultado; // Retorna true si se insertó correctamente, false si hubo un error
        } else {
            return false; // Error al preparar la consulta
        }
    }

    public function obtenerMovimientos($pagina = 1, $limite = 10)
    {
        // Calcular el inicio para la paginación
        $inicio = ($pagina - 1) * $limite;

        $sql = "SELECT m.*, 
        CONCAT(u.nombres, ' ', IFNULL(u.apellidos, '')) AS nombre_usuario -- 🔧 Evita que NULL en apellidos anule el CONCAT
        FROM movimientos_almacen m
        LEFT JOIN usuarios u ON m.usuario_id = u.usuario_id 
        ORDER BY m.fecha DESC
        LIMIT ?, ?";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ii", $inicio, $limite);
        $stmt->execute();
        $resultado = $stmt->get_result();

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Método para contar el total de registros en movimientos_almacen
    public function contarTotalMovimientos()
    {
        $sql = "SELECT COUNT(*) AS total FROM movimientos_almacen"; // Contar todos los registros
        $stmt = $this->conectar->prepare($sql); // Preparar la consulta
        $stmt->execute(); // Ejecutar la consulta
        $resultado = $stmt->get_result(); // Obtener el resultado

        $fila = $resultado->fetch_assoc(); // Obtener la fila con el conteo
        return (int)$fila['total']; // Devolver el total como entero
    }

    public function filtrarPorFecha($fecha_inicio, $fecha_fin)
    {
        // MODIFICADO: Convertir las fechas para incluir todo el rango del día
        $fecha_inicio = $fecha_inicio . " 00:00:00";  // MODIFICADO: Agregar tiempo de inicio del día
        $fecha_fin = $fecha_fin . " 23:59:59";        // MODIFICADO: Agregar tiempo de fin del día
        
        $sql = "SELECT m.*, 
                   CONCAT(u.nombres, ' ', COALESCE(u.apellidos, '')) AS nombre_usuario
            FROM movimientos_almacen m
            INNER JOIN usuarios u ON m.usuario_id = u.usuario_id
            WHERE m.fecha BETWEEN ? AND ?           /* MODIFICADO: Comparar fecha+hora correctamente */
            ORDER BY m.fecha DESC";
    
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $resultado = $stmt->get_result();
    
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    public function filtrarPorTipoMovimiento($tipo_movimiento, $subtipo_movimiento)
    {
        // Reemplazo de nombres elegantes para subtipos
        $subtipos = [
            "normal" => "Individual",
            "venta" => "Venta",
            "financiamiento" => "Financiamiento",
            "ajuste" => "Ajuste"
        ];
        
        $subtipo_movimiento = $subtipos[$subtipo_movimiento] ?? $subtipo_movimiento;

        // ✅ Se ajustó la consulta para que `subtipo_movimiento` solo se filtre si tiene valor
        $sql = "SELECT m.*, CONCAT(u.nombres, ' ', COALESCE(u.apellidos, '')) AS nombre_usuario 
                FROM movimientos_almacen m
                INNER JOIN usuarios u ON m.usuario_id = u.usuario_id
                WHERE m.tipo_movimiento = ?"; // ✅ Eliminado `AND m.subtipo_movimiento = ?` para que no obligue su presencia

        if (!empty($subtipo_movimiento)) { // ✅ Si hay subtipo, se agrega a la consulta
            $sql .= " AND m.subtipo_movimiento = ?";
        }

        $sql .= " ORDER BY m.fecha DESC";

        $stmt = $this->conectar->prepare($sql);

        if (!empty($subtipo_movimiento)) {
            $stmt->bind_param("ss", $tipo_movimiento, $subtipo_movimiento); // ✅ Se agregan ambos parámetros solo si `subtipo_movimiento` tiene valor
        } else {
            $stmt->bind_param("s", $tipo_movimiento); // ✅ Solo se filtra por `tipo_movimiento` si `subtipo_movimiento` es nulo
        }

        $stmt->execute();
        $resultado = $stmt->get_result();

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function buscarPorProducto($busqueda)
    {
        $busqueda = "%$busqueda%";
        
        $sql = "SELECT m.*, CONCAT(u.nombres, ' ', u.apellidos) AS nombre_usuario 
                FROM movimientos_almacen m
                INNER JOIN usuarios u ON m.usuario_id = u.usuario_id
                WHERE m.nombre_producto LIKE ? OR m.codigo_producto LIKE ? 
                ORDER BY m.fecha DESC";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("ss", $busqueda, $busqueda);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function filtrarPorUsuario($usuario_id)
    {
        $sql = "SELECT m.*, CONCAT(u.nombres, ' ', u.apellidos) AS nombre_usuario
                FROM movimientos_almacen m
                INNER JOIN usuarios u ON m.usuario_id = u.usuario_id
                WHERE m.usuario_id = ?
                ORDER BY m.fecha DESC";

        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

}