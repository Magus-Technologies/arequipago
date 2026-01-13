# Cambios Realizados - Incluir Mora en Pagos de Caja Arequipa

**Fecha:** 27 de Noviembre 2025
**Archivos modificados:**
- `app/Http/Controllers/CajaArequipaController.php`
- `app/Models/PagoCajaArequipa.php`
- Base de datos: `pagos_caja_arequipa` (nuevos campos)

---

## Problema Resuelto

Cuando una cuota tenía mora, el sistema **NO estaba sumando la mora al monto** que se enviaba a Caja Arequipa.

### Ejemplo del Problema

```
Cuota: S/. 210.00
Mora: S/. 10.00
───────────────────
Total que debería pagar: S/. 220.00

ANTES:
  Sistema enviaba: S/. 210.00 ❌
  Cliente pagaba: S/. 210.50 (con comisión)
  Faltaban: S/. 10.00 de mora

AHORA:
  Sistema envía: S/. 220.00 ✅
  Cliente paga: S/. 220.50 (con comisión)
  Mora incluida correctamente
```

---

## Solución Implementada

Se modificó el método `buildCuotasData()` para que:

1. **Sume la mora al monto de la cuota**
2. **Aplique el descuento de comisión solo a la cuota, NO a la mora**

---

## Lógica del Cálculo

### Caso 1: Sin descuento de comisión

```php
Cuota: S/. 210.00
Mora: S/. 10.00
───────────────────
Monto enviado: S/. 220.00
```

### Caso 2: Con descuento de comisión (Canal 6 + Producto con descuento_cuota)

```php
Cuota: S/. 20.00
Mora: S/. 10.00
Descuento comisión: S/. 0.50
────────────────────────────
Monto enviado: S/. 29.50  (20 - 0.50 + 10)

Caja Arequipa suma: S/. 0.50
Total que paga cliente: S/. 30.00
```

**Nota importante:** El descuento solo se aplica al monto de la cuota, la mora siempre se suma completa.

---

## Código Modificado

### Método `buildCuotasData()` (líneas 298-314)

```php
foreach ($cuotas as $cuota) {
    // Calcular monto base (cuota + mora)
    $montoBase = $cuota->monto;
    $mora = $cuota->mora ?? 0;

    // Si tiene mora, sumarla al monto
    if ($mora > 0) {
        $montoBase = $montoBase + $mora;
    }

    // Aplicar descuento de comisión si corresponde (solo al monto de la cuota, no a la mora)
    $montoFinal = $montoBase;
    if ($descuento > 0) {
        // El descuento solo se aplica al monto de la cuota, no a la mora
        $montoFinal = $cuota->monto - $descuento + $mora;
    }

    $data[] = [
        'FechaCreacion' => $financiamiento->fecha_creacion ? date('Y-m-d', strtotime($financiamiento->fecha_creacion)) : null,
        'FechaVencimiento' => $cuota->fecha_vencimiento ? date('Y-m-d', strtotime($cuota->fecha_vencimiento)) : null,
        'Moneda' => $monedaCode,
        'Monto' => number_format($montoFinal, 2, '.', ''),
        'NumeroDocumento' => $this->getDocumentNumber($cliente),
        'Periodo' => $cuota->fecha_vencimiento ? date('Ymd', strtotime($cuota->fecha_vencimiento)) : '',
    ];
}
```

---

## Cambios en la Base de Datos

### Nuevos campos en `pagos_caja_arequipa`:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `monto_original` | DECIMAL(10,2) | Monto original de la cuota (sin mora ni descuentos) |
| `mora` | DECIMAL(10,2) | Monto de mora incluido en el pago |
| `comision_asumida` | DECIMAL(10,2) | Comisión que asumió la empresa (0.50 PEN o 0.20 USD) |
| `monto_recibido` | DECIMAL(10,2) | Monto real que recibió la empresa |

### SQL para agregar campos:

```sql
ALTER TABLE pagos_caja_arequipa
ADD COLUMN monto_original DECIMAL(10,2) NULL COMMENT 'Monto original de la cuota sin descuento' AFTER monto,
ADD COLUMN mora DECIMAL(10,2) NULL DEFAULT 0.00 COMMENT 'Monto de mora incluido en el pago' AFTER monto_original,
ADD COLUMN comision_asumida DECIMAL(10,2) NULL DEFAULT 0.00 COMMENT 'Comisión que asumió la empresa (0.50 PEN o 0.20 USD)' AFTER mora,
ADD COLUMN monto_recibido DECIMAL(10,2) NULL COMMENT 'Monto real que recibió la empresa (monto - comision_asumida)' AFTER comision_asumida;
```

---

## Pruebas en Postman

### Caso de Prueba: Financiamiento 307 (Cuota con Mora)

**Datos:**
- Contrato: 307
- Cliente: AGSEL GUILLERMO GONZALES ARCE
- Cuota 12: S/. 210.00
- Mora: S/. 10.00
- Fecha vencimiento: 20/11/2025 (vencida)

---

### Prueba 1: Consultar Cuotas (con mora)

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/consulta`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "307",
    "Moneda": "PEN",
    "Canal": 6
}
```

**Respuesta Esperada:**
```json
{
    "Mensaje": "Proceso correcto",
    "Codigo": "00000",
    "CodigoContrato": 307,
    "Cliente": "AGSEL GUILLERMO GONZALES ARCE",
    "Datos": [
        {
            "FechaCreacion": "2025-08-28",
            "FechaVencimiento": "2025-11-20",
            "Moneda": "PEN",
            "Monto": "220.00",    ← 210.00 + 10.00 de mora
            "NumeroDocumento": "75594783",
            "Periodo": "20251120"
        },
        ...más cuotas sin mora (210.00 cada una)
    ]
}
```

**✅ Verificación:**
- La primera cuota debe mostrar **220.00** (210 + 10 de mora)
- Las demás cuotas sin mora deben mostrar **210.00**

---

### Prueba 2: Pagar Cuota con Mora

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/pago`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "307",
    "NumeroTrace": "T307",
    "Fecha": "2025-11-27",
    "Canal": 6,
    "Datos": [
        {
            "Moneda": "PEN",
            "Monto": 220.00,
            "NumeroDocumento": "75594783",
            "Periodo": "20251120"
        }
    ]
}
```

**Respuesta Esperada:**
```json
{
    "Mensaje": "Proceso correcto",
    "Codigo": "00000",
    "CodigoContrato": "307",
    "NumeroOperacion": "TEST307"
}
```

---

### Prueba 3: Verificar el Pago Guardado

**Consulta SQL:**
```sql
SELECT
    numero_trace,
    canal,
    moneda,
    monto as monto_pagado,
    monto_original as cuota_base,
    mora,
    comision_asumida,
    monto_recibido,
    periodo,
    fecha_pago
FROM pagos_caja_arequipa
WHERE numero_trace = 'T307';
```

**Resultado Esperado:**

| Campo | Valor |
|-------|-------|
| monto_pagado | 220.00 |
| cuota_base | 210.00 |
| mora | 10.00 |
| comision_asumida | 0.00 |
| monto_recibido | 220.00 |
| periodo | 20251120 |

---

### Prueba 4: Extornar el Pago

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/extorno`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "307",
    "NumeroTrace": "T307",
    "Fecha": "2025-11-27"
}
```

**Respuesta Esperada:**
```json
{
    "Mensaje": "Proceso correcto",
    "Codigo": "00000",
    "CodigoContrato": "307",
    "NumeroOperacion": "..."
}
```

---

## Casos de Prueba con Canasta Navideña (con descuento)

### Ejemplo: Contrato 443 con Mora

Supongamos:
- Cuota: S/. 10.00
- Mora: S/. 5.00
- Producto: CANASTA + PAVO (descuento_cuota = 0.50)
- Canal: 6 (app móvil)

**Cálculo:**
```
Cuota base: S/. 10.00
Descuento: S/. 0.50
Mora: S/. 5.00
────────────────────
Monto enviado: S/. 14.50  (10 - 0.50 + 5)

Caja Arequipa suma: S/. 0.50
Total que paga cliente: S/. 15.00
```

**Prueba en Postman:**
```json
{
    "CodigoContrato": "443",
    "Moneda": "PEN",
    "Canal": 6
}
```

---

## Resumen de Cambios

### Antes ❌
- Solo enviaba el monto de la cuota
- La mora no se incluía
- Cliente pagaba de menos

### Ahora ✅
- Envía cuota + mora
- Aplica descuento solo a la cuota (si corresponde)
- Cliente paga el monto correcto

---

## Notas Importantes

1. **La mora siempre se suma completa** - No se le aplica descuento
2. **El descuento solo aplica a la cuota base** - Si hay mora, se suma después
3. **Canal 6 (app móvil)** - Solo en este canal se aplica el descuento de comisión
4. **Los campos nuevos en pagos_caja_arequipa** - Permiten hacer seguimiento de comisiones asumidas

---

## Archivos Modificados

1. **`CajaArequipaController.php`**
   - Método `buildCuotasData()` - Incluye mora en el cálculo
   - Método `pagarContrato()` - Guarda nuevos campos

2. **`PagoCajaArequipa.php`**
   - Agregados campos al `$fillable`
   - Agregados campos al `$casts`

3. **Base de datos**
   - Tabla `pagos_caja_arequipa` - 3 campos nuevos

---

## Para Producción

1. Ejecutar migración SQL en producción:
   ```sql
   -- Archivo: migracion_comision_recaudacion.sql
   ALTER TABLE pagos_caja_arequipa
   ADD COLUMN monto_original DECIMAL(10,2) NULL,
   ADD COLUMN comision_asumida DECIMAL(10,2) NULL DEFAULT 0.00,
   ADD COLUMN monto_recibido DECIMAL(10,2) NULL;
   ```

2. Subir archivos modificados:
   - `app/Http/Controllers/CajaArequipaController.php`
   - `app/Models/PagoCajaArequipa.php`

3. Verificar que `.env` tenga la conexión correcta a MySQL

---

## Contacto

Para dudas sobre esta implementación, contactar al equipo de desarrollo.
