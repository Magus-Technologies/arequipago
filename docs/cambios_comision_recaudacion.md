# Cambios Realizados - Descuento de Comisión de Recaudación

**Fecha:** 20 de Noviembre 2025
**Archivo modificado:** `app/Http/Controllers/CajaArequipaController.php`

---

## Problema

Caja Arequipa cobra una comisión de recaudación cuando el cliente paga por la app móvil (Canal 6):

| Moneda | Comisión |
|--------|----------|
| Soles (PEN) | S/. 0.50 |
| Dólares (USD) | $ 0.20 |

Para ciertos productos (como Canastas Navideñas), la empresa quiere asumir esta comisión para que el cliente pague exactamente el monto de su cuota.

---

## Solución Implementada

Se modificó el método `consultarContrato()` para que descuente automáticamente la comisión del monto enviado a Caja Arequipa, **solo cuando el canal es 6 (Banca Móvil/App)**.

### Lógica del Descuento

```
Si Canal = 6 (App Móvil) Y el producto tiene descuento_cuota > 0:
    Monto enviado = Cuota original - descuento_cuota
Si no:
    Monto enviado = Cuota original
```

---

## Ejemplo Práctico

### Contrato 443 - Canasta Navideña

**Producto:** CANASTA + PAVO (idproductosv2 = 258)
**descuento_cuota:** 0.50
**Cuota original:** S/. 10.00

#### Con Canal 6 (App Móvil):
```
Cuota original:        S/. 10.00
Descuento aplicado:    S/.  0.50
Monto enviado:         S/.  9.50

Caja Arequipa suma:    S/.  0.50 (comisión)
Cliente paga:          S/. 10.00 ✓
```

#### Con Canal 1 (Ventanilla):
```
Cuota original:        S/. 10.00
Descuento aplicado:    S/.  0.00
Monto enviado:         S/. 10.00

Cliente paga:          S/. 10.00 ✓
```

---

## Cambios en el Código

### 1. Import agregado (línea 12)
```php
use Illuminate\Support\Facades\DB;
```

### 2. Modificación en consultarContrato() (línea 44)
Se agregó el parámetro `Canal` a la llamada de `buildCuotasData`:
```php
$data = $this->buildCuotasData($financiamiento, $cuotas, $cliente, $validated['Moneda'], $validated['Canal']);
```

### 3. Método buildCuotasData modificado (líneas 281-314)
```php
private function buildCuotasData($financiamiento, $cuotas, $cliente, $monedaCode, $canal)
{
    $data = [];

    // Obtener el descuento del producto solo si es canal 6 (banca móvil/app)
    $descuento = 0;
    if ($canal == 6 && $financiamiento->idproductosv2) {
        $producto = DB::table('productosv2')
            ->where('idproductosv2', $financiamiento->idproductosv2)
            ->first();

        if ($producto && $producto->descuento_cuota > 0) {
            $descuento = $producto->descuento_cuota;
        }
    }

    foreach ($cuotas as $cuota) {
        // Aplicar descuento si corresponde
        $montoFinal = $cuota->monto;
        if ($descuento > 0) {
            $montoFinal = $cuota->monto - $descuento;
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
    return $data;
}
```

---

## Configuración del Descuento

El descuento se configura en la tabla `productosv2` en el campo `descuento_cuota`.

### Productos con descuento actualmente:

| Producto | Código | descuento_cuota |
|----------|--------|-----------------|
| Producto Falso prueba | 01244457575748 | 0.50 |
| CANASTA + PAVO | (NULL) | 0.50 |

### Cómo agregar descuento a un nuevo producto:

```sql
UPDATE productosv2
SET descuento_cuota = 0.50
WHERE idproductosv2 = [ID_DEL_PRODUCTO];
```

Para dólares usar 0.20:
```sql
UPDATE productosv2
SET descuento_cuota = 0.20
WHERE idproductosv2 = [ID_DEL_PRODUCTO];
```

---

## Canales de Caja Arequipa

| Canal | Descripción | ¿Aplica descuento? |
|-------|-------------|-------------------|
| 1 | Ventanilla | NO |
| 2 | Cajeros automáticos | NO |
| 3 | Home banking | NO |
| 4 | Corresponsal | NO |
| 5 | Débito automático | NO |
| **6** | **Banca móvil (App)** | **SÍ** |

---

## Pruebas Realizadas

### Prueba 1: Canal 6 (App Móvil)
```bash
curl -X POST http://arequipago-api.test/api/v1/consulta \
  -H "Content-Type: application/json" \
  -d '{
    "CodigoContrato": "443",
    "Moneda": "PEN",
    "Canal": 6
  }'
```

**Resultado:** Monto = 9.50 ✓

### Prueba 2: Canal 1 (Ventanilla)
```bash
curl -X POST http://arequipago-api.test/api/v1/consulta \
  -H "Content-Type: application/json" \
  -d '{
    "CodigoContrato": "443",
    "Moneda": "PEN",
    "Canal": 1
  }'
```

**Resultado:** Monto = 10.00 ✓

---

## Notas Importantes

1. **Solo afecta al endpoint de Consulta** - El pago y extorno no se modificaron.

2. **El descuento se aplica por producto** - Si quieres que un producto tenga descuento, debes configurar el campo `descuento_cuota` en la tabla `productosv2`.

3. **Solo aplica para Canal 6** - Si el cliente paga por ventanilla u otro canal, no se aplica descuento.

4. **El monto real de la cuota no cambia** - Solo se modifica lo que se envía a Caja Arequipa.

---

## Contacto

Para dudas sobre esta implementación, contactar al equipo de desarrollo.
