# Correcciones Realizadas - Sistema de Pagos Pendientes de Inscripción

## Problemas Corregidos

### 1. Error "Class Vehiculo not found"
**Problema:** Había un espacio extra en el nombre de la clase en la línea 137 del controlador.
**Solución:** Se corrigió el nombre de la clase de `new Vehiculo ()` a `new Vehiculo()`.

### 2. Pagos rechazados no aparecen en la pestaña de rechazados
**Problema:** Al rechazar un pago, se sobrescribía la columna `observaciones` (que contiene los datos del pago) con el motivo del rechazo, perdiendo la información necesaria para mostrar el pago.

**Solución:**
- Se modificó el método `actualizarEstado()` para agregar el motivo de rechazo DENTRO del JSON de observaciones, sin sobrescribir los datos originales
- Se actualizó la vista para leer el `motivo_rechazo` desde el JSON de observaciones
- No se requieren cambios en la base de datos

## Archivos Modificados

### 1. `app/http/controllers/PagosPendientesInscripcionController.php`
- Corregido el espacio extra en `new Vehiculo()`
- Actualizado el método `rechazarPago()` para pasar el motivo de rechazo
- Actualizado el método `reactivarPago()` para no sobrescribir observaciones

### 2. `app/models/PagosPendientesInscripcion.php`
- Modificado el método `actualizarEstado()` para agregar el motivo de rechazo dentro del JSON de observaciones
- Actualizado el método `obtenerPagosRechazados()` para validar que las observaciones sean un array antes de procesarlas

### 3. `resources/views/fragment-views/cliente/pagos-inscripcion.php`
- Actualizada la tabla de rechazados para mostrar `observaciones.motivo_rechazo`
- Actualizada la función de detalles para mostrar el motivo de rechazo desde el JSON

## Instrucciones para Aplicar los Cambios

**No se requieren cambios en la base de datos.** Los archivos PHP ya están actualizados y listos para usar.

Para probar:
1. Rechazar un pago → debería aparecer en la pestaña "Rechazados"
2. Aprobar un pago → debería procesarse sin errores

## Estructura del JSON de Observaciones

```json
{
  "id_conductor": 123,
  "tipo_pago": "financiado",
  "monto_pago": 500.00,
  "monto_inicial": 100.00,
  "numero_cuotas": 4,
  "frecuencia_pago": "mensual",
  "fecha_inicio": "2025-01-01",
  "fecha_fin": "2025-04-01",
  "monto_cuota": 100.00,
  "tasa_interes": 0,
  "motivo_rechazo": "Pago rechazado" // ← Se agrega cuando se rechaza
}
```

## Notas Importantes

- La columna `observaciones` contiene un JSON con todos los datos del pago
- Cuando se rechaza un pago, se agrega la clave `motivo_rechazo` al JSON sin perder los datos originales
- Al reactivar un pago, el `motivo_rechazo` permanece en el JSON para historial
- No se requieren migraciones ni cambios en la base de datos
