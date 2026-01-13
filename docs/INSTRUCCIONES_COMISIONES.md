# 🎯 SISTEMA DE COMISIONES CONFIGURABLES - INSTRUCCIONES COMPLETAS

## ✅ RESUMEN DE LO QUE HICE

He modificado el sistema para que las comisiones **NO estén hardcodeadas** y el director pueda configurarlas desde la vista de grupos de financiamiento.

---

## 📋 PASO 1: EJECUTAR ESTOS COMANDOS SQL

Copia y ejecuta estos comandos en tu base de datos:

```sql
-- Agregar campos de comisión a la tabla planes_financiamiento
ALTER TABLE `planes_financiamiento` 
ADD COLUMN `aplica_comision` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Sí aplica comisión, 0=No aplica',
ADD COLUMN `monto_comision` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Monto de comisión para asesores',
ADD COLUMN `moneda_comision` VARCHAR(5) NULL DEFAULT 'S/.' COMMENT 'Moneda de la comisión (S/. o $)';

-- También agregar a la tabla grupos_variantes (para variantes específicas)
ALTER TABLE `grupos_variantes`
ADD COLUMN `monto_comision` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Monto de comisión para esta variante',
ADD COLUMN `moneda_comision` VARCHAR(5) NULL DEFAULT 'S/.' COMMENT 'Moneda de la comisión';

-- Actualizar planes existentes con sus comisiones actuales (opcional - para mantener compatibilidad)
UPDATE planes_financiamiento SET monto_comision = 30.00, moneda_comision = '$' WHERE idplan_financiamiento = 19;
UPDATE planes_financiamiento SET monto_comision = 50.00, moneda_comision = 'S/.' WHERE idplan_financiamiento = 22;
UPDATE planes_financiamiento SET monto_comision = 50.00, moneda_comision = 'S/.' WHERE idplan_financiamiento IN (2, 3, 4);
UPDATE planes_financiamiento SET monto_comision = 15.00, moneda_comision = 'S/.' WHERE idplan_financiamiento IN (14, 15, 16);
UPDATE planes_financiamiento SET monto_comision = 100.00, moneda_comision = 'S/.' WHERE idplan_financiamiento = 33;
UPDATE planes_financiamiento SET monto_comision = 30.00, moneda_comision = '$' WHERE idplan_financiamiento = 38;
```

---

## 📝 PASO 2: REEMPLAZAR LA FUNCIÓN EN Comision.php

Abre el archivo `app/models/Comision.php` y **REEMPLAZA** la función `calcularComisionFinanciamiento` completa (línea 68 aproximadamente) con esta nueva versión:

```php
/**
 * Calcula la comisión según las reglas de negocio
 * ✅ MODIFICADO: Ahora lee los valores de la base de datos en lugar de hardcodear
 */
public function calcularComisionFinanciamiento($grupo_financiamiento, $id_variante = null) {
    $comision = ['monto' => 0, 'moneda' => 'S/.', 'aplica' => false];
    
    $planId = is_numeric($grupo_financiamiento) ? intval($grupo_financiamiento) : null;
    
    if (!$planId) {
        return $comision;
    }
    
    // ✅ NUEVO: Primero intentar obtener comisión de la variante (si existe)
    if ($id_variante) {
        $sqlVariante = "SELECT monto_comision, moneda_comision 
                       FROM grupos_variantes 
                       WHERE idgrupos_variantes = ? 
                       AND monto_comision IS NOT NULL";
        
        $stmtVariante = $this->conectar->prepare($sqlVariante);
        $stmtVariante->bind_param("i", $id_variante);
        $stmtVariante->execute();
        $resultVariante = $stmtVariante->get_result();
        
        if ($rowVariante = $resultVariante->fetch_assoc()) {
            // La variante tiene comisión específica
            return [
                'monto' => floatval($rowVariante['monto_comision']),
                'moneda' => $rowVariante['moneda_comision'] ?? 'S/.',
                'aplica' => true
            ];
        }
    }
    
    // ✅ NUEVO: Si no hay comisión en variante, obtener del plan principal
    $sqlPlan = "SELECT aplica_comision, monto_comision, moneda_comision 
               FROM planes_financiamiento 
               WHERE idplan_financiamiento = ?";
    
    $stmtPlan = $this->conectar->prepare($sqlPlan);
    $stmtPlan->bind_param("i", $planId);
    $stmtPlan->execute();
    $resultPlan = $stmtPlan->get_result();
    
    if ($rowPlan = $resultPlan->fetch_assoc()) {
        // Verificar si aplica comisión
        if ($rowPlan['aplica_comision'] == 1 && $rowPlan['monto_comision'] !== null) {
            return [
                'monto' => floatval($rowPlan['monto_comision']),
                'moneda' => $rowPlan['moneda_comision'] ?? 'S/.',
                'aplica' => true
            ];
        } else if ($rowPlan['aplica_comision'] == 0) {
            // El plan tiene desactivada la comisión
            return ['monto' => 0, 'moneda' => 'S/.', 'aplica' => false];
        }
    }
    
    // ⚠️ FALLBACK: Si no hay configuración en BD, usar valores por defecto (legacy)
    // Esto mantiene compatibilidad con planes antiguos que no tienen comisión configurada
    error_log("⚠️ Plan ID $planId no tiene comisión configurada en BD, usando valores por defecto");
    
    switch ($planId) {
        case 19: // CREDI GO AUTOS - Grupo 3
            if ($id_variante) {
                switch (intval($id_variante)) {
                    case 4: return ['monto' => 30.00, 'moneda' => '$', 'aplica' => true];
                    case 5: return ['monto' => 40.00, 'moneda' => '$', 'aplica' => true];
                    case 6: return ['monto' => 50.00, 'moneda' => '$', 'aplica' => true];
                }
            }
            break;
        case 38: // CREDI GO AUTOS - Grupo 4
            if ($id_variante) {
                switch (intval($id_variante)) {
                    case 21: return ['monto' => 30.00, 'moneda' => '$', 'aplica' => true];
                    case 22: return ['monto' => 40.00, 'moneda' => '$', 'aplica' => true];
                    case 23: return ['monto' => 50.00, 'moneda' => '$', 'aplica' => true];
                }
            }
            break;
        case 22: return ['monto' => 50.00, 'moneda' => 'S/.', 'aplica' => true];
        case 2:
        case 3:
        case 4: return ['monto' => 50.00, 'moneda' => 'S/.', 'aplica' => true];
        case 14: return ['monto' => 15.00, 'moneda' => 'S/.', 'aplica' => true];
        case 15: return ['monto' => 15.00, 'moneda' => 'S/.', 'aplica' => true];
        case 16: return ['monto' => 15.00, 'moneda' => 'S/.', 'aplica' => true];
        case 33: return ['monto' => 100.00, 'moneda' => 'S/.', 'aplica' => true];
    }
    
    return $comision;
}
```

---

## 🎯 CÓMO FUNCIONA AHORA

### **Para el Director:**

1. Va a **Grupos de Financiamiento**
2. Al crear o editar un grupo, verá una nueva sección verde: **"Configuración de Comisiones para Asesores"**
3. Puede:
   - ✅ Activar/desactivar si el plan genera comisión
   - 💰 Establecer el monto de comisión (ej: 50.00)
   - 💵 Elegir la moneda (Soles o Dólares)
4. Al agregar **variantes**, también puede configurar comisiones específicas para cada variante

### **Para el Sistema:**

1. Cuando un asesor registra un financiamiento
2. El sistema busca la comisión en este orden:
   - **Primero**: ¿La variante tiene comisión configurada? → Usa esa
   - **Segundo**: ¿El plan tiene comisión configurada? → Usa esa
   - **Tercero**: ¿No hay nada en BD? → Usa valores hardcodeados (fallback)

---

## 📊 VENTAJAS

✅ **Flexible**: El director puede cambiar comisiones sin tocar código
✅ **Granular**: Puede configurar comisiones diferentes por variante
✅ **Seguro**: Mantiene compatibilidad con planes antiguos
✅ **Auditable**: Todo queda registrado en la base de datos
✅ **Fácil**: Interfaz visual simple para el director

---

## 🔍 ARCHIVOS MODIFICADOS

1. ✅ `resources/views/fragment-views/cliente/grupos-financiamiento.php` - Vista con campos de comisión
2. ✅ `app/http/controllers/GruposFinanciamientoController.php` - Controlador actualizado
3. ✅ `app/models/GrupoFinanciamientoModel.php` - Modelo actualizado
4. ⏳ `app/models/Comision.php` - **PENDIENTE: Reemplazar función manualmente**

---

## ⚠️ IMPORTANTE

- Los **directores (rol 3) NO reciben comisiones** (esto no cambió)
- Si un plan NO tiene comisión configurada, usa los valores hardcodeados como fallback
- Las comisiones se generan automáticamente al registrar financiamientos

---

¿Necesitas ayuda con algún paso? 🚀
