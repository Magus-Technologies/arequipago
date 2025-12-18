# Pruebas en Postman - API Caja Arequipa

---

## Configuración General

**URL Base:** `http://arequipago-api.test/api/v1`

**Headers para todas las peticiones:**
| Key | Value |
|-----|-------|
| Content-Type | application/json |

---

## 1. CONSULTA - Endpoint de Consulta

### 1.1 Consulta con Canal 6 (App Móvil) - CON DESCUENTO

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/consulta`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "443",
    "Moneda": "PEN",
    "Canal": 6
}
```

**Respuesta Esperada:**
- Monto: **9.50** (se descuenta 0.50 de comisión)

---

### 1.2 Consulta con Canal 1 (Ventanilla) - SIN DESCUENTO

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/consulta`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "443",
    "Moneda": "PEN",
    "Canal": 1
}
```

**Respuesta Esperada:**
- Monto: **10.00** (sin descuento)

---

### 1.3 Consulta Producto SIN descuento_cuota

Para probar un producto que NO tiene descuento configurado, usa cualquier contrato que no sea de Canasta Navideña.

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/consulta`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "[ID_OTRO_CONTRATO]",
    "Moneda": "PEN",
    "Canal": 6
}
```

**Respuesta Esperada:**
- Monto: El monto original de la cuota (sin descuento)

---

### 1.4 Consulta con Moneda USD (Dólares)

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/consulta`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "[ID_CONTRATO_EN_DOLARES]",
    "Moneda": "USD",
    "Canal": 6
}
```

---

### 1.5 Consulta con Contrato Inexistente

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/consulta`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "999999",
    "Moneda": "PEN",
    "Canal": 6
}
```

**Respuesta Esperada:**
```json
{
    "Mensaje": "Contrato no encontrado o no está activo",
    "Codigo": "00099",
    "CodigoContrato": "999999",
    "Cliente": null,
    "Datos": null
}
```

---

## 2. PAGO - Endpoint de Pago

### 2.1 Pago de una Cuota

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/pago`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "443",
    "NumeroTrace": "TEST01",
    "Fecha": "2025-11-20",
    "Canal": 6,
    "Datos": [
        {
            "Moneda": "PEN",
            "Monto": 9.50,
            "NumeroDocumento": "005261451",
            "Periodo": "20250930"
        }
    ]
}
```

**Respuesta Esperada:**
```json
{
    "Mensaje": "Proceso correcto",
    "Codigo": "00000",
    "CodigoContrato": "443",
    "NumeroOperacion": "TEST01"
}
```

---

### 2.2 Pago de Múltiples Cuotas

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/pago`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "443",
    "NumeroTrace": "TEST02",
    "Fecha": "2025-11-20",
    "Canal": 6,
    "Datos": [
        {
            "Moneda": "PEN",
            "Monto": 9.50,
            "NumeroDocumento": "005261451",
            "Periodo": "20251007"
        },
        {
            "Moneda": "PEN",
            "Monto": 9.50,
            "NumeroDocumento": "005261451",
            "Periodo": "20251014"
        }
    ]
}
```

---

### 2.3 Pago Duplicado (debe fallar)

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/pago`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "443",
    "NumeroTrace": "TEST01",
    "Fecha": "2025-11-20",
    "Canal": 6,
    "Datos": [
        {
            "Moneda": "PEN",
            "Monto": 9.50,
            "NumeroDocumento": "005261451",
            "Periodo": "20250930"
        }
    ]
}
```

**Respuesta Esperada:**
```json
{
    "Mensaje": "El número de trace ya fue procesado",
    "Codigo": "00099",
    "CodigoContrato": "443",
    "NumeroOperacion": "TEST01"
}
```

---

## 3. EXTORNO - Endpoint de Extorno

### 3.1 Extornar un Pago

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/extorno`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "443",
    "NumeroTrace": "TEST01",
    "Fecha": "2025-11-20"
}
```

**Respuesta Esperada:**
```json
{
    "Mensaje": "Proceso correcto",
    "Codigo": "00000",
    "CodigoContrato": "443",
    "NumeroOperacion": "..."
}
```

---

### 3.2 Extornar Pago Inexistente

**Método:** POST
**URL:** `http://arequipago-api.test/api/v1/extorno`

**Body (raw - JSON):**
```json
{
    "CodigoContrato": "443",
    "NumeroTrace": "NOEXISTE",
    "Fecha": "2025-11-20"
}
```

**Respuesta Esperada:**
```json
{
    "Mensaje": "Pago no encontrado o ya fue extornado",
    "Codigo": "00099",
    "CodigoContrato": "443",
    "NumeroOperacion": "NOEXISTE"
}
```

---

## 4. Casos de Prueba por Canal

### Tabla de Canales

| Canal | Descripción | Aplica Descuento |
|-------|-------------|-----------------|
| 1 | Ventanilla | NO |
| 2 | Cajeros automáticos | NO |
| 3 | Home banking | NO |
| 4 | Corresponsal | NO |
| 5 | Débito automático | NO |
| **6** | **Banca móvil (App)** | **SÍ** |

### Prueba por cada Canal

Usa el mismo JSON cambiando solo el valor de `Canal`:

```json
{
    "CodigoContrato": "443",
    "Moneda": "PEN",
    "Canal": 1
}
```

**Resultados esperados para contrato 443:**

| Canal | Monto Esperado |
|-------|----------------|
| 1 | 10.00 |
| 2 | 10.00 |
| 3 | 10.00 |
| 4 | 10.00 |
| 5 | 10.00 |
| **6** | **9.50** |

---

## 5. Verificar Productos Sin Descuento

Los productos que **NO tienen** el campo `descuento_cuota` configurado (o es 0 o NULL) funcionan normalmente sin ningún descuento.

### Consulta SQL para ver productos con descuento:
```sql
SELECT
    idproductosv2,
    nombre,
    descuento_cuota
FROM productosv2
WHERE descuento_cuota > 0;
```

### Para agregar descuento a un producto:
```sql
-- Para soles (comisión de S/. 0.50)
UPDATE productosv2
SET descuento_cuota = 0.50
WHERE idproductosv2 = [ID];

-- Para dólares (comisión de $ 0.20)
UPDATE productosv2
SET descuento_cuota = 0.20
WHERE idproductosv2 = [ID];
```

---

## 6. Flujo Completo de Prueba

### Paso 1: Consultar
```json
POST /api/v1/consulta
{
    "CodigoContrato": "443",
    "Moneda": "PEN",
    "Canal": 6
}
```
→ Ver las cuotas disponibles

### Paso 2: Pagar
```json
POST /api/v1/pago
{
    "CodigoContrato": "443",
    "NumeroTrace": "TEST01",
    "Fecha": "2025-11-20",
    "Canal": 6,
    "Datos": [...]
}
```
→ Registrar el pago

### Paso 3: Consultar de nuevo
```json
POST /api/v1/consulta
{
    "CodigoContrato": "443",
    "Moneda": "PEN",
    "Canal": 6
}
```
→ Verificar que la cuota pagada ya no aparece

### Paso 4: Extornar (opcional)
```json
POST /api/v1/extorno
{
    "CodigoContrato": "443",
    "NumeroTrace": "TEST01",
    "Fecha": "2025-11-20"
}
```
→ Revertir el pago

### Paso 5: Consultar de nuevo
```json
POST /api/v1/consulta
{
    "CodigoContrato": "443",
    "Moneda": "PEN",
    "Canal": 6
}
```
→ Verificar que la cuota vuelve a aparecer

---

## 7. Códigos de Respuesta

| Código | Significado |
|--------|-------------|
| 00000 | Proceso exitoso |
| 00099 | Error de negocio |
| 99 | Error interno del servidor |

---

## Notas Importantes

1. **NumeroTrace debe ser único** - Cada pago debe tener un NumeroTrace diferente
2. **Periodo formato YYYYMMDD** - Ejemplo: 20251120 para 20 de noviembre 2025
3. **El descuento solo aplica en consulta** - El pago recibe el monto que envía Caja Arequipa
4. **Productos sin descuento_cuota** - Funcionan normalmente sin ningún cambio
