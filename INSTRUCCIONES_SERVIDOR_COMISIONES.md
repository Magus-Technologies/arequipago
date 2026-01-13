# 🚀 INSTRUCCIONES PARA CORRECCIÓN DE COMISIONES EN SERVIDOR

## 📊 RESUMEN DEL PROBLEMA

- **Total financiamientos desde noviembre:** 91
- **Deberían tener comisión:** 50
- **Tenían comisión antes:** 8
- **❌ FALTABAN:** 42 comisiones
- **✅ AHORA CREADAS:** 50 comisiones totales

---

## 🔧 CAMBIOS A REALIZAR EN PRODUCCIÓN

### 1️⃣ ACTUALIZAR CÓDIGO PHP

**Archivo:** `app/http/controllers/RegistrarFinanciamientoController.php`
**Línea:** ~1117-1134

**BUSCAR:**
```php
private function registrarComisionAutomatica($idFinanciamiento)
{
    // Verificar si el usuario puede tener comisión automática

    if (!in_array($_SESSION['id_rol'], [1, 2, 3])) {
        return;  // Solo roles 1 y 3 tienen comisión automática
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
```

**REEMPLAZAR POR:**
```php
private function registrarComisionAutomatica($idFinanciamiento)
{
    // Verificar si el usuario puede tener comisión automática

    if (!in_array($_SESSION['id_rol'], [1, 2, 3])) {
        return;  // Solo roles 1, 2 y 3 tienen comisión automática
    }

    // ✅ CORREGIDO: Usar getFinanciamientoByIdParaAprobacion en lugar de getFinanciamientoById
    // Esto permite obtener financiamientos incluso cuando aprobado = 0 (pendientes de aprobación)
    $financiamientoModel = new Financiamiento();
    $financiamiento = $financiamientoModel->getFinanciamientoByIdParaAprobacion($idFinanciamiento);

    if ($financiamiento) {
        // REUTILIZAR la misma lógica de comisiones que ya existe
        $financiamientoController = new FinanciamientoController();
        $financiamientoController->registrarComisionFinanciamiento($financiamiento);
    } else {
        error_log("⚠️ No se pudo obtener financiamiento ID $idFinanciamiento para registrar comisión");
    }
}
```

---

### 2️⃣ INSERTAR COMISIONES FALTANTES EN BASE DE DATOS

**Conectar a MySQL y ejecutar:**

```sql
-- ========================================
-- PASO 1: Verificar estado actual
-- ========================================
SELECT COUNT(*) as total_comisiones_actuales
FROM comisiones c
INNER JOIN financiamiento f ON c.referencia_id = f.idfinanciamiento
WHERE c.tipo_comision = 'financiamiento'
AND f.fecha_creacion >= '2025-11-01 00:00:00';
-- Si devuelve menos de 50, continuar con el script

-- ========================================
-- PASO 2: INSERTAR TODAS LAS COMISIONES FALTANTES
-- ========================================

INSERT INTO comisiones
(usuario_id, tipo_comision, referencia_id, monto_comision, fecha_comision, tipo_vehiculo, observaciones, moneda, estado_comision)
VALUES
-- SEBASTIAN (18 comisiones)
(105, 'financiamiento', 750, 50.00, '2025-12-30 18:09:00', 'vehiculo', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$', 'pendiente'),
(105, 'financiamiento', 741, 15.00, '2025-12-19 16:56:00', NULL, 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.', 'pendiente'),
(105, 'financiamiento', 735, 50.00, '2025-12-17 20:15:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente'),
(105, 'financiamiento', 734, 50.00, '2025-12-17 20:15:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente'),
(105, 'financiamiento', 733, 15.00, '2025-12-17 19:33:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO LLANTAS', 'S/.', 'pendiente'),
(105, 'financiamiento', 729, 50.00, '2025-12-16 18:27:00', 'vehiculo', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$', 'pendiente'),
(105, 'financiamiento', 727, 15.00, '2025-12-15 09:40:00', NULL, 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.', 'pendiente'),
(105, 'financiamiento', 719, 25.00, '2025-12-13 10:00:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),
(105, 'financiamiento', 706, 50.00, '2025-12-04 17:46:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente'),
(105, 'financiamiento', 696, 25.00, '2025-11-27 17:32:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),
(105, 'financiamiento', 695, 25.00, '2025-11-26 17:20:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),
(105, 'financiamiento', 694, 40.00, '2025-11-26 15:48:00', 'vehiculo', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$', 'pendiente'),
(105, 'financiamiento', 680, 15.00, '2025-11-19 19:35:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO LLANTAS', 'S/.', 'pendiente'),
(105, 'financiamiento', 677, 15.00, '2025-11-19 16:11:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO BATERIAS', 'S/.', 'pendiente'),
(105, 'financiamiento', 674, 50.00, '2025-11-18 18:56:00', 'vehiculo', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$', 'pendiente'),
(105, 'financiamiento', 664, 15.00, '2025-11-13 21:47:00', NULL, 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.', 'pendiente'),
(105, 'financiamiento', 663, 15.00, '2025-11-13 21:16:00', NULL, 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.', 'pendiente'),
(105, 'financiamiento', 631, 25.00, '2025-11-08 14:57:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),

-- LESLIE (12 comisiones)
(82, 'financiamiento', 744, 50.00, '2025-12-24 12:08:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente'),
(82, 'financiamiento', 737, 50.00, '2025-12-18 14:48:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente'),
(82, 'financiamiento', 731, 50.00, '2025-12-17 12:05:00', 'vehiculo', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$', 'pendiente'),
(82, 'financiamiento', 711, 15.00, '2025-12-11 13:49:00', NULL, 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.', 'pendiente'),
(82, 'financiamiento', 707, 50.00, '2025-12-05 12:22:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente'),
(82, 'financiamiento', 704, 50.00, '2025-12-04 16:20:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente'),
(82, 'financiamiento', 676, 50.00, '2025-11-19 11:47:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente'),
(82, 'financiamiento', 668, 15.00, '2025-11-18 14:45:00', NULL, 'Comisión por financiamiento - Mantenimiento IncaMotors', 'S/.', 'pendiente'),
(82, 'financiamiento', 648, 25.00, '2025-11-10 15:48:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),
(82, 'financiamiento', 646, 25.00, '2025-11-10 15:10:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),
(82, 'financiamiento', 633, 25.00, '2025-11-10 10:54:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),

-- MARIA FERNANDA OFIC 2 (8 comisiones)
(106, 'financiamiento', 745, 15.00, '2025-12-27 11:07:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO BATERIAS', 'S/.', 'pendiente'),
(106, 'financiamiento', 743, 15.00, '2025-12-22 13:58:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO BATERIAS', 'S/.', 'pendiente'),
(106, 'financiamiento', 697, 15.00, '2025-11-28 14:04:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO LLANTAS', 'S/.', 'pendiente'),
(106, 'financiamiento', 636, 15.00, '2025-11-10 11:41:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO BATERIAS', 'S/.', 'pendiente'),
(106, 'financiamiento', 627, 50.00, '2025-11-07 14:10:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente'),
(106, 'financiamiento', 647, 25.00, '2025-11-10 15:54:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),
(106, 'financiamiento', 634, 25.00, '2025-11-10 10:52:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),
(106, 'financiamiento', 621, 50.00, '2025-11-04 14:25:00', 'vehiculo', 'Comisión por financiamiento - CrediGo auto (Grupo 3)', '$', 'pendiente'),

-- JOEL OJEDA (4 comisiones)
(104, 'financiamiento', 730, 50.00, '2025-12-17 11:03:00', 'vehiculo', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$', 'pendiente'),
(104, 'financiamiento', 728, 50.00, '2025-12-16 10:13:00', 'vehiculo', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$', 'pendiente'),
(104, 'financiamiento', 718, 50.00, '2025-12-13 11:32:00', 'vehiculo', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$', 'pendiente'),
(104, 'financiamiento', 717, 50.00, '2025-12-13 10:42:00', 'vehiculo', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$', 'pendiente'),

-- JEFERSON CHOPITA (2 comisiones)
(92, 'financiamiento', 639, 25.00, '2025-11-10 13:42:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),
(92, 'financiamiento', 640, 25.00, '2025-11-10 13:42:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),

-- EMER (2 comisiones)
(124, 'financiamiento', 725, 50.00, '2025-12-13 13:52:00', 'vehiculo', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$', 'pendiente'),
(124, 'financiamiento', 724, 50.00, '2025-12-13 13:52:00', 'vehiculo', 'Comisión por financiamiento - CrediGo Autos Grupo 4', '$', 'pendiente'),

-- OTROS (1 comisión cada uno)
(82, 'financiamiento', 613, 15.00, '2025-11-03 10:22:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO ACEITE', 'S/.', 'pendiente'),
(85, 'financiamiento', 619, 50.00, '2025-11-04 12:03:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente'),
(103, 'financiamiento', 673, 25.00, '2025-11-18 16:59:00', NULL, 'Comisión por financiamiento - Credit Yango', '$', 'pendiente'),
(111, 'financiamiento', 685, 50.00, '2025-11-22 13:41:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente'),
(125, 'financiamiento', 736, 50.00, '2025-12-18 10:49:00', NULL, 'Comisión por financiamiento - FINANCIAMIENTO CELULARES', 'S/.', 'pendiente')
-- IMPORTANTE: No agregar punto y coma aquí, lo haremos después de verificar duplicados
ON DUPLICATE KEY UPDATE id_comision = id_comision;
-- Esta cláusula evita errores si alguna comisión ya existe

-- ========================================
-- PASO 3: VERIFICACIÓN
-- ========================================
SELECT
    u.nombres as usuario,
    COUNT(*) as total_comisiones,
    ROUND(SUM(CASE WHEN c.moneda = 'S/.' THEN c.monto_comision ELSE 0 END), 2) as total_soles,
    ROUND(SUM(CASE WHEN c.moneda = '$' THEN c.monto_comision ELSE 0 END), 2) as total_dolares
FROM comisiones c
INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
INNER JOIN financiamiento f ON c.referencia_id = f.idfinanciamiento
WHERE c.tipo_comision = 'financiamiento' AND f.fecha_creacion >= '2025-11-01 00:00:00'
GROUP BY u.usuario_id, u.nombres
ORDER BY total_comisiones DESC;

-- Debe mostrar:
-- Sebastian: 18 comisiones - S/.255 + $290
-- Leslie: 12 comisiones - S/.295 + $125
-- Maria Fernanda: 8 comisiones - S/.110 + $100
-- Joel Ojeda: 4 comisiones - $200
-- Y otros usuarios...
```

---

## ✅ RESULTADO ESPERADO

Después de aplicar los cambios, deberías tener:

| Usuario | Comisiones | Soles | Dólares |
|---------|------------|-------|---------|
| Sebastian | 18 | S/.255.00 | $290.00 |
| Leslie | 12 | S/.295.00 | $125.00 |
| Maria Fernanda | 8 | S/.110.00 | $100.00 |
| Joel Ojeda | 4 | - | $200.00 |
| jeferson chopita | 2 | - | $50.00 |
| emer | 2 | - | $100.00 |
| yessenia avalos | 1 | S/.50.00 | - |
| Kristopher Salgado | 1 | - | $25.00 |
| YONNY | 1 | S/.50.00 | - |
| Leones Go | 1 | S/.50.00 | - |

**Total: 50 comisiones desde noviembre 2025**

---

## 📝 NOTAS IMPORTANTES

1. El código corregido evita que se repita el problema en el futuro
2. Las comisiones se crean automáticamente al registrar un financiamiento
3. Los directores (rol 3) NO generan comisiones
4. Solo se generan comisiones para planes con `aplica_comision = 1` y monto configurado

---

**Fecha de corrección:** 31/12/2025
**Archivos modificados:** 1 (RegistrarFinanciamientoController.php)
**Registros insertados:** 42 comisiones faltantes
