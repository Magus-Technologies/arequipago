<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../utils/config.php";

class ActualizarPasswordConductorEspecifico {
    private $conexion;
    
    public function __construct() {
        $this->conexion = (new Conexion())->getConexion();
        $this->conexion->select_db("magusqao_arequipa");
    }
    
    // Actualizar contraseña del conductor específico con documento 47391287
    public function actualizarPasswordConductorEspecifico($numeroDocumento = '47391287') {
        echo "<h2>🔐 Actualización de Contraseña para Conductor Específico</h2>";
        echo "<div style='background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #2196f3;'>";
        echo "<strong>🎯 Conductor objetivo:</strong> Documento N° {$numeroDocumento}";
        echo "</div>";

        // Consultar el conductor específico
        $queryConsulta = "SELECT id_conductor, nro_documento, nombres, apellido_paterno, apellido_materno, password 
                         FROM conductores 
                         WHERE nro_documento = ?";

        echo "<div style='background-color: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #2196F3;'>";
        echo "<strong>📋 Consulta para encontrar el conductor específico:</strong><br>";
        echo "<code style='background-color: #e8e8e8; padding: 5px; border-radius: 3px; font-family: monospace;'>" . htmlspecialchars($queryConsulta) . "</code>";
        echo "<br><strong>Parámetro:</strong> nro_documento = '{$numeroDocumento}'";
        echo "</div>";

        $stmt = $this->conexion->prepare($queryConsulta);
        
        if (!$stmt) {
            echo "<div style='color: red; padding: 15px; background-color: #ffe6e6; border: 1px solid #ff0000; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ <strong>Error al preparar la consulta:</strong><br>";
            echo "Código de error: " . $this->conexion->errno . "<br>";
            echo "Mensaje de error: " . $this->conexion->error;
            echo "</div>";
            return;
        }

        $stmt->bind_param("s", $numeroDocumento);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if (!$resultado) {
            echo "<div style='color: red; padding: 15px; background-color: #ffe6e6; border: 1px solid #ff0000; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ <strong>Error al ejecutar la consulta:</strong><br>";
            echo "Mensaje de error: " . $stmt->error;
            echo "</div>";
            $stmt->close();
            return;
        }

        $totalConductores = $resultado->num_rows;
        
        echo "<div style='background-color: #e6f3ff; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>📊 Conductores encontrados con documento {$numeroDocumento}: {$totalConductores}</strong>";
        echo "</div>";

        if ($totalConductores == 0) {
            echo "<div style='color: orange; padding: 15px; background-color: #fff3e0; border: 1px solid #ff9800; border-radius: 5px; margin: 10px 0;'>";
            echo "⚠️ <strong>No se encontró ningún conductor con el documento {$numeroDocumento}</strong><br>";
            echo "🔍 Verifique que el número de documento sea correcto";
            echo "</div>";
            $stmt->close();
            return;
        }

        // Obtener los datos del conductor
        $conductor = $resultado->fetch_assoc();
        $stmt->close();

        // Mostrar información del conductor encontrado
        echo "<div style='background-color: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ff9800;'>";
        echo "<strong>🚗 Conductor encontrado:</strong><br>";
        echo "<div style='overflow-x: auto; margin-top: 10px;'>";
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 12px;'>";
        echo "<thead>";
        echo "<tr style='background-color: #ff9800; color: white;'>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>ID Conductor</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Documento</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Nombres</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Apellidos</th>";
        echo "<th style='padding: 8px; text-align: left; border: 1px solid #ddd;'>Password Actual</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        $passwordActual = empty($conductor['password']) ? '<em style="color: #999;">VACÍO/NULL</em>' : '<em style="color: #2e7d32;">EXISTE</em>';
        echo "<tr style='background-color: #fff;'>";
        echo "<td style='padding: 6px; border: 1px solid #ddd;'>" . htmlspecialchars($conductor['id_conductor']) . "</td>";
        echo "<td style='padding: 6px; border: 1px solid #ddd;'><strong>" . htmlspecialchars($conductor['nro_documento']) . "</strong></td>";
        echo "<td style='padding: 6px; border: 1px solid #ddd;'>" . htmlspecialchars($conductor['nombres']) . "</td>";
        echo "<td style='padding: 6px; border: 1px solid #ddd;'>" . htmlspecialchars($conductor['apellido_paterno'] . ' ' . $conductor['apellido_materno']) . "</td>";
        echo "<td style='padding: 6px; border: 1px solid #ddd;'>{$passwordActual}</td>";
        echo "</tr>";
        
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";

        // Proceder con la actualización
        echo "<div style='background-color: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #4caf50;'>";
        echo "<strong>🔄 Iniciando proceso de actualización de contraseña...</strong>";
        echo "</div>";

        $idConductor = $conductor['id_conductor'];
        $documento = $conductor['nro_documento'];
        $nombreCompleto = $conductor['nombres'] . ' ' . $conductor['apellido_paterno'] . ' ' . $conductor['apellido_materno'];

        // Hashear la contraseña usando bcrypt (el número de documento)
        $passwordHash = password_hash($documento, PASSWORD_BCRYPT);

        // Actualizar la contraseña
        $queryUpdate = "UPDATE conductores 
                       SET password = ? 
                       WHERE id_conductor = ?";

        $stmtUpdate = $this->conexion->prepare($queryUpdate);
        
        if (!$stmtUpdate) {
            echo "<div style='color: red; padding: 15px; background-color: #ffe6e6; border: 1px solid #ff0000; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ <strong>Error preparando consulta de actualización:</strong><br>";
            echo "Mensaje de error: " . $this->conexion->error;
            echo "</div>";
            return;
        }

        $stmtUpdate->bind_param("si", $passwordHash, $idConductor);
        
        if ($stmtUpdate->execute()) {
            echo "<div style='color: green; padding: 10px; background-color: #e8f5e8; border: 1px solid #4caf50; border-radius: 5px; margin: 10px 0;'>";
            echo "✅ <strong>Conductor actualizado exitosamente:</strong><br>";
            echo "• ID Conductor: {$idConductor}<br>";
            echo "• Nombre: {$nombreCompleto}<br>";
            echo "• Documento: {$documento}<br>";
            echo "• Nueva contraseña: *** (cifrada con bcrypt)<br>";
            echo "• Contraseña base: {$documento} (número de documento)";
            echo "</div>";
            
            $conductorActualizado = true;
        } else {
            echo "<div style='color: red; padding: 15px; background-color: #ffe6e6; border: 1px solid #ff0000; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ <strong>Error al actualizar el conductor:</strong><br>";
            echo "Mensaje de error: " . $stmtUpdate->error;
            echo "</div>";
            
            $conductorActualizado = false;
        }

        $stmtUpdate->close();

        // Mostrar resumen final
        echo "<div style='background-color: " . ($conductorActualizado ? "#e8f5e8" : "#ffe6e6") . "; padding: 15px; border-radius: 5px; margin: 20px 0; border: 2px solid " . ($conductorActualizado ? "#4caf50" : "#f44336") . ";'>";
        echo "<h3 style='margin: 0 0 10px 0; color: " . ($conductorActualizado ? "#2e7d32" : "#d32f2f") . ";'>📊 Resumen de Actualización</h3>";
        
        if ($conductorActualizado) {
            echo "<strong style='color: #2e7d32;'>✅ Conductor actualizado exitosamente: 1</strong><br>";
            echo "<strong style='color: #d32f2f;'>❌ Errores encontrados: 0</strong><br>";
        } else {
            echo "<strong style='color: #2e7d32;'>✅ Conductores actualizados exitosamente: 0</strong><br>";
            echo "<strong style='color: #d32f2f;'>❌ Errores encontrados: 1</strong><br>";
        }
        
        echo "<br><strong>🎯 Conductor objetivo:</strong> Documento N° {$numeroDocumento}<br>";
        echo "<strong>🔐 Método de encriptación:</strong> bcrypt (PASSWORD_BCRYPT)<br>";
        echo "<strong>🔑 Contraseña base:</strong> Número de documento del conductor ({$documento})<br>";
        echo "<strong>📅 Fecha de actualización:</strong> " . date('Y-m-d H:i:s');
        echo "</div>";

        // Verificación final
        if ($conductorActualizado) {
            echo "<div style='background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #2196f3;'>";
            echo "<strong>🔍 Verificación final:</strong><br>";
            
            $queryVerificacion = "SELECT password 
                                 FROM conductores 
                                 WHERE nro_documento = ?";
            
            $stmtVerif = $this->conexion->prepare($queryVerificacion);
            $stmtVerif->bind_param("s", $numeroDocumento);
            $stmtVerif->execute();
            $resultVerificacion = $stmtVerif->get_result();
            $datosVerif = $resultVerificacion->fetch_assoc();
            $stmtVerif->close();
            
            if (!empty($datosVerif['password'])) {
                echo "<span style='color: green;'>🎉 ¡Contraseña confirmada! El conductor ahora tiene contraseña asignada.</span><br>";
                echo "<strong>Hash generado:</strong> <code style='font-size: 10px; word-break: break-all;'>" . substr($datosVerif['password'], 0, 50) . "...</code>";
            } else {
                echo "<span style='color: red;'>❌ Error: La contraseña no se guardó correctamente.</span>";
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
echo "<title>Actualizar Contraseña Conductor Específico - Conductores</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
echo ".container { max-width: 100%; background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo "code { background-color: #e8e8e8; padding: 2px 4px; border-radius: 3px; font-family: monospace; }";
echo "</style>";
echo "</head><body>";
echo "<div class='container'>";

try {
    $script = new ActualizarPasswordConductorEspecifico();
    $script->actualizarPasswordConductorEspecifico('47391287');
} catch (Exception $e) {
    echo "<div style='color: red; padding: 15px; background-color: #ffe6e6; border: 1px solid #ff0000; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ <strong>Error crítico:</strong> " . $e->getMessage();
    echo "</div>";
}

echo "</div>";
echo "</body></html>";
?>