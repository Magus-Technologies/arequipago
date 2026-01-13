# 🚀 INSTRUCCIONES FINALES PARA EL SERVIDOR - SISTEMA DE COMISIONES

## 📊 RESUMEN DE PROBLEMAS ENCONTRADOS Y SOLUCIONADOS

### ❌ **Problemas detectados:**

1. **42 comisiones de financiamientos FALTABAN** (desde noviembre)
2. **3 comisiones de financiamientos ELIMINADOS** (estado_eliminado = 1)
3. **4 comisiones de financiamientos RECHAZADOS** (aprobado = 2)

### ✅ **Estado actual (base de datos local):**

- **Total comisiones válidas:** 127
- **Pendientes:** 66
- **Pagadas:** 61
- **Total en Soles:** S/. 3,850.00
- **Total en Dólares:** $ 1,175.00

---

## 🔧 CAMBIOS A REALIZAR EN PRODUCCIÓN

### 1️⃣ **ACTUALIZAR ARCHIVO PHP**

**Archivo:** `app/models/Comision.php`

Necesitas actualizar **3 métodos** para agregar los filtros correctos:

#### **Método 1: `obtenerComisiones()`** (línea ~230)

**BUSCAR:**
```php
WHERE 1=1";
```

**REEMPLAZAR POR:**
```php
WHERE 1=1
AND (c.tipo_comision = 'inscripcion' OR (c.tipo_comision = 'financiamiento' AND COALESCE(f.estado_eliminado, 0) = 0 AND f.aprobado != 2))";
```

#### **Método 2: `obtenerEstadisticasComisiones()`** (línea ~297)

**BUSCAR:**
```php
FROM comisiones c
LEFT JOIN financiamiento f ON (c.tipo_comision = 'financiamiento' AND c.referencia_id = f.idfinanciamiento)
WHERE 1=1";
```

**REEMPLAZAR POR:**
```php
FROM comisiones c
LEFT JOIN financiamiento f ON (c.tipo_comision = 'financiamiento' AND c.referencia_id = f.idfinanciamiento)
WHERE 1=1
AND (c.tipo_comision = 'inscripcion' OR (c.tipo_comision = 'financiamiento' AND COALESCE(f.estado_eliminado, 0) = 0 AND f.aprobado != 2))";
```

#### **Método 3: `obtenerDetalleComision()`** (línea ~380)

**BUSCAR:**
```php
WHERE c.id_comision = ?";
```

**REEMPLAZAR POR:**
```php
WHERE c.id_comision = ?
AND (c.tipo_comision = 'inscripcion' OR (c.tipo_comision = 'financiamiento' AND COALESCE(f.estado_eliminado, 0) = 0 AND f.aprobado != 2))";
```

---

### 2️⃣ **EJECUTAR SCRIPTS SQL EN BASE DE DATOS**

Conecta a MySQL en el servidor y ejecuta estos comandos:

```sql
-- ========================================
-- PASO 1: LIMPIAR COMISIONES INVÁLIDAS
-- ========================================

-- Eliminar comisiones de financiamientos ELIMINADOS
DELETE c FROM comisiones c
INNER JOIN financiamiento f ON c.referencia_id = f.idfinanciamiento
WHERE c.tipo_comision = 'financiamiento'
AND f.estado_eliminado = 1;

-- Eliminar comisiones de financiamientos RECHAZADOS
DELETE c FROM comisiones c
INNER JOIN financiamiento f ON c.referencia_id = f.idfinanciamiento
WHERE c.tipo_comision = 'financiamiento'
AND f.aprobado = 2;

-- ========================================
-- PASO 2: VERIFICAR QUE SE ELIMINARON
-- ========================================

-- Debe devolver 0
SELECT COUNT(*) as comisiones_invalidas
FROM comisiones c
INNER JOIN financiamiento f ON c.referencia_id = f.idfinanciamiento
WHERE c.tipo_comision = 'financiamiento'
AND (f.estado_eliminado = 1 OR f.aprobado = 2);

-- ========================================
-- PASO 3: INSERTAR COMISIONES FALTANTES (SI NO LO HICISTE)
-- ========================================
-- NOTA: Si ya insertaste las 42 comisiones faltantes en tu servidor,
-- NO ejecutes este paso. Si no lo hiciste, ejecuta el script completo
-- del archivo INSTRUCCIONES_SERVIDOR_COMISIONES.md
```

---

### 3️⃣ **ACTUALIZAR CONTROLADOR (YA LO HICISTE)**

**Archivo:** `app/http/controllers/RegistrarFinanciamientoController.php`

✅ Ya actualizaste este archivo (línea 1128):
```php
$financiamiento = $financiamientoModel->getFinanciamientoByIdParaAprobacion($idFinanciamiento);
```

---

## 📋 **RESUMEN DE FILTROS APLICADOS**

Las comisiones ahora se filtran para **EXCLUIR:**

| Condición | Descripción | Campo |
|-----------|-------------|-------|
| **Financiamientos eliminados** | No mostrar comisiones de financiamientos en papelera | `estado_eliminado = 1` |
| **Financiamientos rechazados** | No mostrar comisiones de financiamientos rechazados | `aprobado = 2` |
| **Directores** | No generar comisiones para directores al crear | `id_rol = 3` |

---

## ✅ **VERIFICACIÓN POST-IMPLEMENTACIÓN**

Después de aplicar los cambios, ejecuta este SQL para verificar:

```sql
-- Verificar total de comisiones válidas
SELECT
    COUNT(*) as total_comisiones,
    SUM(CASE WHEN c.estado_comision = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
    SUM(CASE WHEN c.estado_comision = 'pagada' THEN 1 ELSE 0 END) as pagadas,
    ROUND(SUM(CASE WHEN c.moneda = 'S/.' THEN c.monto_comision ELSE 0 END), 2) as total_soles,
    ROUND(SUM(CASE WHEN c.moneda = '$' THEN c.monto_comision ELSE 0 END), 2) as total_dolares
FROM comisiones c
LEFT JOIN financiamiento f ON (c.tipo_comision = 'financiamiento' AND c.referencia_id = f.idfinanciamiento)
WHERE c.tipo_comision = 'inscripcion'
   OR (c.tipo_comision = 'financiamiento' AND COALESCE(f.estado_eliminado, 0) = 0 AND f.aprobado != 2);

-- Verificar que NO hay comisiones inválidas
SELECT
    'Comisiones de eliminados' as tipo,
    COUNT(*) as cantidad
FROM comisiones c
INNER JOIN financiamiento f ON c.referencia_id = f.idfinanciamiento
WHERE c.tipo_comision = 'financiamiento' AND f.estado_eliminado = 1
UNION ALL
SELECT
    'Comisiones de rechazados' as tipo,
    COUNT(*) as cantidad
FROM comisiones c
INNER JOIN financiamiento f ON c.referencia_id = f.idfinanciamiento
WHERE c.tipo_comision = 'financiamiento' AND f.aprobado = 2;

-- Ambos deben devolver 0
```

---

## 📊 **SOBRE LAS INSCRIPCIONES**

**IMPORTANTE:**
- ✅ **TODAS las inscripciones de asesores tienen comisión** (39/39 = 100%)
- Las inscripciones desde noviembre fueron hechas por **directores**, por eso no generan comisión (comportamiento correcto)
- Si te reportan inscripciones sin comisión, verifica:
  1. ¿Fue registrada por un director?
  2. ¿El `usuario_registro` es NULL?
  3. ¿Es muy reciente y no ha refrescado?

---

## 🎯 **ARCHIVOS MODIFICADOS**

| Archivo | Acción | Estado |
|---------|--------|--------|
| `app/models/Comision.php` | Agregar filtros (3 métodos) | ⚠️ **PENDIENTE** |
| `app/http/controllers/RegistrarFinanciamientoController.php` | Fix método comisión | ✅ **HECHO** |
| **Base de datos** | Ejecutar 2 DELETE | ⚠️ **PENDIENTE** |

---

## 🚨 **ORDEN DE EJECUCIÓN**

1. **Primero:** Subir `app/models/Comision.php` actualizado
2. **Segundo:** Ejecutar los DELETE en la base de datos
3. **Tercero:** Verificar con los queries de validación
4. **Cuarto:** Probar en la vista de comisiones

---

**Fecha:** 31/12/2025
**Total comisiones limpias:** 127
**Total eliminadas:** 7 (3 de eliminados + 4 de rechazados)
