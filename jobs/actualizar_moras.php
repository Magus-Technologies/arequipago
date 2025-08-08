<?php
require_once __DIR__ . "/../config/Conexion.php";
require_once __DIR__ . "/../utils/config.php";

class ScriptBD {
    private $conexion;
    
    public function __construct() {
        $this->conexion = (new Conexion())->getConexion();
        
        // Asegurarse de usar la base de datos correcta
        $this->conexion->select_db("magusqao_arequipa");
    }
    
    public function ejecutar() {
        echo "<h3>🔍 Procesando cuotas vencidas y aplicando mora</h3>";
        
        // Buscar cuotas vencidas (estado 'En Progreso' y fecha_vencimiento menor a hoy)
        $query_cuotas_vencidas = "
            SELECT idcuotas_financiamiento, id_financiamiento, numero_cuota, monto, mora, fecha_vencimiento
            FROM cuotas_financiamiento 
            WHERE estado = 'En Progreso' 
            AND fecha_vencimiento < CURDATE()
        ";
        
        $resultado_cuotas = $this->conexion->query($query_cuotas_vencidas);
        
        if (!$resultado_cuotas) {
            echo "<pre>❌ Error al buscar cuotas vencidas: " . $this->conexion->error . "</pre>";
            return;
        }
        
        $cuotas_procesadas = 0;
        $cuotas_actualizadas = 0;
        
        while ($cuota = $resultado_cuotas->fetch_assoc()) {
            $cuotas_procesadas++;
            
            // Obtener datos del financiamiento
            $query_financiamiento = "
                SELECT idproductosv2, moneda 
                FROM financiamiento 
                WHERE idfinanciamiento = " . $cuota['id_financiamiento'];
            
            $resultado_financiamiento = $this->conexion->query($query_financiamiento);
            
            if (!$resultado_financiamiento) {
                echo "<pre>❌ Error al obtener financiamiento ID " . $cuota['id_financiamiento'] . ": " . $this->conexion->error . "</pre>";
                continue;
            }
            
            $financiamiento = $resultado_financiamiento->fetch_assoc();
            
            if (!$financiamiento) {
                echo "<pre>⚠️ No se encontró financiamiento con ID " . $cuota['id_financiamiento'] . "</pre>";
                continue;
            }
            
            // Si idproductosv2 es 37, no aplicar mora
            if ($financiamiento['idproductosv2'] == 37) {
                echo "<pre>ℹ️ Cuota ID " . $cuota['idcuotas_financiamiento'] . " - Producto ID 37, no aplica mora</pre>";
                continue;
            }
            
            // Calcular mora según las reglas
            $nueva_mora = 0;
            $moneda = $financiamiento['moneda'];
            $monto_cuota = floatval($cuota['monto']);
            
            if ($moneda == 'S/.') {
                // Soles
                if ($monto_cuota >= 100) {
                    $nueva_mora = 10;
                } else {
                    $nueva_mora = 5;
                }
            } elseif ($moneda == '$') {
                // Dólares
                $nueva_mora = 5;
            } else {
                echo "<pre>⚠️ Moneda no reconocida para cuota ID " . $cuota['idcuotas_financiamiento'] . ": " . $moneda . "</pre>";
                continue;
            }
            
            // Actualizar la mora en la cuota
            $query_update = "
                UPDATE cuotas_financiamiento 
                SET mora = " . $nueva_mora . " 
                WHERE idcuotas_financiamiento = " . $cuota['idcuotas_financiamiento'];
            
            if ($this->conexion->query($query_update)) {
                $cuotas_actualizadas++;
                echo "<pre>✅ Cuota ID " . $cuota['idcuotas_financiamiento'] . " - Mora actualizada a " . $nueva_mora . " (" . $moneda . ")</pre>";
            } else {
                echo "<pre>❌ Error al actualizar cuota ID " . $cuota['idcuotas_financiamiento'] . ": " . $this->conexion->error . "</pre>";
            }
        }
        
        echo "<h4>📊 Resumen del procesamiento:</h4>";
        echo "<pre>Total de cuotas vencidas procesadas: " . $cuotas_procesadas . "</pre>";
        echo "<pre>Total de cuotas con mora actualizada: " . $cuotas_actualizadas . "</pre>";
    }
}

$script = new ScriptBD();
$script->ejecutar();
?>