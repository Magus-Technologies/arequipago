<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../utils/config.php";

class ActualizarPasswordsClientes {
    private $conexion;
    
    public function __construct() {
        $this->conexion = (new Conexion())->getConexion();
        $this->conexion->select_db("magusqao_arequipa");
    }
    
    // Actualizar contraseñas de clientes que no las tienen
    public function actualizarPasswords() {
        echo "<h2>🔐 Actualización de Contraseñas para Clientes Financiar</h2>";

        // Primero, consultar clientes sin contraseña
        $queryConsulta = "SELECT id, n_documento, nombres, apellido_paterno, apellido_materno, password 
                         FROM clientes_financiar 
                         WHERE password IS NULL OR password = '' OR password = '0'";

        echo "<div style='background-color: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #2196F3;'>";
        echo "<strong>📋 Consulta para encontrar clientes sin contraseña:</strong><br>";
        echo "<code style='background-color: #e8e8e8; padding: 5px; border-radius: 3px; font-family: monospace;'>" . htmlspecialchars($queryConsulta) . "</code>";
        echo "</div>";

        $resultado = $this->conexion->query($queryConsulta);

        if (!$resultado) {
            echo "<div style='color: red; padding: 15px; background-color: #ffe6e6; border: 1px solid #ff0000; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ <strong>Error al consultar clientes:</strong><br>";
            echo "Código de error: " . $this->conexion->errno . "<br>";
            echo "Mensaje de error: " . $this->conexion->error;
            echo "</div>";
            return;
        }

        $totalClientes = $resultado->num_rows;
        
        echo "<div style='background-color: #e6f3ff; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>📊 Total de clientes sin contraseña encontrados: {$totalClientes}</strong>";
        echo "</div>";

        if ($totalClientes == 0) {
            echo "<div style='color: orange; padding: 15px; background-color: #fff3e0; border: 1px solid #ff9800; border-radius: 5px; margin: 10px 0;'>";
            echo "⚠️ <strong>No se encontraron clientes sin contraseña</strong><br>";
            echo "🎉 Todos los clientes ya tienen contraseña asignada";
            echo "</div>";
            return;
        }

        // Mostrar los clientes que se van a actualizar
        echo "<div style='background-color: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ff9800;'>";
        echo "<strong>👥 Clientes que se actualizarán:</strong><br>";
        echo "<div style='overflow-x: auto; margin-top: 10px;'>";
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 12px;'>";
        echo "<thead>";
        echo "<tr style='background-color: #ff9800; color: white;'>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>ID</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Documento</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Nombres</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Apellidos</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Password Actual</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        $clientes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $clientes[] = $fila;
            $passwordActual = empty($fila['password']) ? '<em style="color: #999;">VACÍO/NULL</em>' : '<em style="color: #999;">EXISTE</em>';
            echo "<tr style='background-color: #fff;'>";
            echo "<td style='padding: 6px; border: 1px solid #ddd;'>" . htmlspecialchars($fila['id']) . "</td>";
            echo "<td style='padding: 6px; border: 1px solid #ddd;'><strong>" . htmlspecialchars($fila['n_documento']) . "</strong></td>";
            echo "<td style='padding: 6px; border: 1px solid #ddd;'>" . htmlspecialchars($fila['nombres']) . "</td>";
            echo "<td style='padding: 6px; border: 1px solid #ddd;'>" . htmlspecialchars($fila['apellido_paterno'] . ' ' . $fila['apellido_materno']) . "</td>";
            echo "<td style='padding: 6px; border: 1px solid #ddd;'>{$passwordActual}</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";

        // Proceder con la actualización
        echo "<div style='background-color: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #4caf50;'>";
        echo "<strong>🔄 Iniciando proceso de actualización...</strong>";
        echo "</div>";

        $clientesActualizados = 0;
        $errores = [];

        foreach ($clientes as $cliente) {
            $id = $cliente['id'];
            $documento = $cliente['n_documento'];
            $nombreCompleto = $cliente['nombres'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno'];

            // Hashear la contraseña usando bcrypt
            $passwordHash = password_hash($documento, PASSWORD_BCRYPT);

            // Actualizar la contraseña
            $queryUpdate = "UPDATE clientes_financiar 
                           SET password = ?, fecha_actualizacion = NOW() 
                           WHERE id = ?";

            $stmt = $this->conexion->prepare($queryUpdate);
            
            if (!$stmt) {
                $errores[] = "Error preparando consulta para cliente ID {$id}: " . $this->conexion->error;
                continue;
            }

            $stmt->bind_param("si", $passwordHash, $id);
            
            if ($stmt->execute()) {
                $clientesActualizados++;
                echo "<div style='color: green; padding: 5px; font-size: 12px;'>";
                echo "✅ Cliente ID {$id} - {$nombreCompleto} (Doc: {$documento}) - Contraseña actualizada";
                echo "</div>";
            } else {
                $errores[] = "Error actualizando cliente ID {$id}: " . $stmt->error;
                echo "<div style='color: red; padding: 5px; font-size: 12px;'>";
                echo "❌ Error al actualizar cliente ID {$id} - {$nombreCompleto}";
                echo "</div>";
            }

            $stmt->close();
        }

        // Mostrar resumen final
        echo "<div style='background-color: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0; border: 2px solid #4caf50;'>";
        echo "<h3 style='margin: 0 0 10px 0; color: #2e7d32;'>📊 Resumen de Actualización</h3>";
        echo "<strong style='color: #2e7d32;'>✅ Clientes actualizados exitosamente: {$clientesActualizados}</strong><br>";
        
        if (count($errores) > 0) {
            echo "<strong style='color: #d32f2f;'>❌ Errores encontrados: " . count($errores) . "</strong><br>";
            echo "<div style='margin-top: 10px; background-color: #ffebee; padding: 10px; border-radius: 3px;'>";
            foreach ($errores as $error) {
                echo "• " . htmlspecialchars($error) . "<br>";
            }
            echo "</div>";
        }
        
        echo "<br><strong>🔐 Método de encriptación:</strong> bcrypt (PASSWORD_BCRYPT)<br>";
        echo "<strong>🔑 Contraseña base:</strong> Número de documento del cliente<br>";
        echo "<strong>📅 Fecha de actualización:</strong> " . date('Y-m-d H:i:s');
        echo "</div>";

        // Verificación adicional
        if ($clientesActualizados > 0) {
            echo "<div style='background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #2196f3;'>";
            echo "<strong>🔍 Verificación final:</strong><br>";
            
            $queryVerificacion = "SELECT COUNT(*) as total_sin_password 
                                 FROM clientes_financiar 
                                 WHERE password IS NULL OR password = '' OR password = '0'";
            
            $resultVerificacion = $this->conexion->query($queryVerificacion);
            $totalRestante = $resultVerificacion->fetch_assoc()['total_sin_password'];
            
            echo "Clientes que aún no tienen contraseña: <strong>{$totalRestante}</strong><br>";
            
            if ($totalRestante == 0) {
                echo "<span style='color: green;'>🎉 ¡Todos los clientes ahora tienen contraseña asignada!</span>";
            }
            echo "</div>";
        }
    }

    // Cerrar conexión
    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}

// Ejecutar el script
echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<meta charset='UTF-8'>";
echo "<title>Actualizar Contraseñas - Clientes Financiar</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
echo ".container { max-width: 100%; background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo "code { background-color: #e8e8e8; padding: 2px 4px; border-radius: 3px; font-family: monospace; }";
echo "</style>";
echo "</head><body>";
echo "<div class='container'>";

try {
    $script = new ActualizarPasswordsClientes();
    $script->actualizarPasswords();
} catch (Exception $e) {
    echo "<div style='color: red; padding: 15px; background-color: #ffe6e6; border: 1px solid #ff0000; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ <strong>Error crítico:</strong> " . $e->getMessage();
    echo "</div>";
}

echo "</div>";
echo "</body></html>";
?>