# ARCHIVOS MODIFICADOS - SISTEMA DE APROBACIÓN DE PAGOS DE INSCRIPCIÓN

## Resumen de Implementación
Se ha implementado un sistema de aprobación de pagos de inscripción para usuarios con rol 1 (Administrador) y rol 2 (Asesor). Los pagos realizados por estos usuarios quedan en estado "pendiente" hasta que un usuario con rol 3 (Director) los apruebe.

---

## ARCHIVOS CREADOS

### 1. `app/models/PagosPendientesInscripcion.php`
**Descripción:** Modelo para gestionar los pagos pendientes de inscripción.

**Funciones principales:**
- `registrarPagoPendiente()` - Registra un nuevo pago pendiente
- `obtenerPagosPendientes()` - Lista todos los pagos pendientes
- `obtenerPagosRechazados()` - Lista todos los pagos rechazados
- `obtenerPagoPendientePorId()` - Obtiene un pago específico
- `actualizarEstado()` - Actualiza el estado del pago (pendiente/aprobado/rechazado)
- `eliminarPagoPendiente()` - Elimina un pago pendiente
- `contarPagosPendientes()` - Cuenta los pagos pendientes

---

### 2. `app/http/controllers/PagosPendientesInscripcionController.php`
**Descripción:** Controlador para manejar las operaciones de pagos pendientes.

**Métodos principales:**
- `listarPagosPendientes()` - Endpoint para listar pagos pendientes
- `listarPagosRechazados()` - Endpoint para listar pagos rechazados
- `contarPagosPendientes()` - Endpoint para contar pagos pendientes
- `aprobarPago()` - Endpoint para aprobar un pago pendiente
- `rechazarPago()` - Endpoint para rechazar un pago
- `reactivarPago()` - Endpoint para reactivar un pago rechazado
- `eliminarPago()` - Endpoint para eliminar un pago

---

## ARCHIVOS MODIFICADOS

### 3. `app/http/controllers/RegistroPagoController.php`
**Cambios realizados:**
- Se agregó `require_once "app/models/PagosPendientesInscripcion.php"`
- Se modificó el método `guardarRegistroPago()` para:
  - Verificar el rol del usuario (`$_SESSION['id_rol']`)
  - Si el rol es 1 o 2, registrar el pago como pendiente en lugar de procesarlo directamente
  - Preparar los datos del pago (incluyendo cuotas si es financiado) en formato JSON
  - Retornar una respuesta indicando que el pago requiere aprobación

**Líneas modificadas:** ~25-60

---

### 4. `routes/web.php`
**Cambios realizados:**
- Se agregaron 7 nuevas rutas para gestionar pagos pendientes:
  - `GET /listarPagosPendientesInscripcion`
  - `GET /listarPagosRechazadosInscripcion`
  - `GET /contarPagosPendientesInscripcion`
  - `POST /aprobarPagoInscripcion`
  - `POST /rechazarPagoInscripcion`
  - `POST /reactivarPagoInscripcion`
  - `POST /eliminarPagoInscripcion`

**Líneas agregadas:** Después de la línea 36

---

### 5. `resources/views/fragment-views/cliente/pago-inscrip-conductor.php`
**Cambios realizados:**

#### a) Modificación en saveRegPagoConductor() (línea ~530)
- Se modificó para detectar si el pago requiere aprobación
- Muestra un mensaje diferente según el caso

---

### 6. `resources/views/fragment-views/cliente/pagos-inscripcion.php`
**Cambios realizados:**

#### a) Botón de Pagos Pendientes de Inscripción (línea ~35)
- Se agregó un botón flotante "Pagos Pendientes" con badge de notificación
- El botón muestra el número de pagos pendientes de inscripción
- Mismo estilo que el botón de pagos-financiamiento.php

#### b) Modal de Gestión de Pagos de Inscripción (final del archivo)
- Se agregó un modal completo con 2 tabs:
  - **Tab 1:** Pagos Pendientes
  - **Tab 2:** Pagos Rechazados
- Cada tab tiene su propia tabla con información del pago
- Botones de acción según el rol del usuario:
  - **Rol 3 (Director):** Puede aprobar, rechazar y eliminar
  - **Rol 1 y 2:** Solo pueden ver

#### c) Funciones JavaScript (final del archivo)
- `verPagosPendientesInscripcion()` - Abre el modal y carga los datos
- `cargarPagosPendientesInscripcion()` - Carga pagos pendientes vía AJAX
- `cargarPagosRechazadosInscripcion()` - Carga pagos rechazados vía AJAX
- `mostrarPagosPendientesInscripcion()` - Renderiza la tabla de pendientes
- `mostrarPagosRechazadosInscripcion()` - Renderiza la tabla de rechazados
- `aprobarPagoInscripcion()` - Aprueba un pago
- `rechazarPagoInscripcion()` - Rechaza un pago con motivo
- `reactivarPagoInscripcion()` - Reactiva un pago rechazado
- `eliminarPagoInscripcion()` - Elimina un pago
- `actualizarContadorPendientesInscripcion()` - Actualiza el badge de notificaciones

---

## FLUJO DE TRABAJO

### Para Usuarios con Rol 1 y 2 (Administrador y Asesor):
1. Registran un pago de inscripción normalmente
2. El sistema detecta su rol y guarda el pago como "pendiente"
3. Se muestra un mensaje: "Pago registrado como pendiente. Debe ser aprobado por un director."
4. El pago NO se procesa ni se genera nota de venta
5. El contador de pagos pendientes se actualiza

### Para Usuarios con Rol 3 (Director):
1. Ven el botón "Pagos Pendientes" con el número de pagos
2. Al hacer clic, se abre el modal con 2 tabs
3. Pueden:
   - **Aprobar:** El pago se procesa normalmente y se genera la nota de venta
   - **Rechazar:** El pago pasa a "rechazado" con un motivo
   - **Eliminar:** Se elimina el registro permanentemente
4. En pagos rechazados pueden:
   - **Reactivar:** El pago vuelve a estado "pendiente"
   - **Eliminar:** Se elimina el registro permanentemente

---

## PERMISOS POR ROL

| Acción | Rol 1 (Admin) | Rol 2 (Asesor) | Rol 3 (Director) |
|--------|---------------|----------------|------------------|
| Registrar pago | ✅ (Pendiente) | ✅ (Pendiente) | ✅ (Directo) |
| Ver pagos pendientes | ✅ | ✅ | ✅ |
| Aprobar pago | ❌ | ❌ | ✅ |
| Rechazar pago | ❌ | ❌ | ✅ |
| Reactivar pago | ❌ | ❌ | ✅ |
| Eliminar pago | ❌ | ❌ | ✅ |

---

## TABLA DE BASE DE DATOS

La tabla `pagos_pendientes_inscripcion` debe existir con la siguiente estructura:

```sql
CREATE TABLE `pagos_pendientes_inscripcion` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tipo_inscripcion` ENUM('conductor', 'cliente') NOT NULL,
  `id_pago_inscripcion` INT(11) NULL,
  `id_cliente_pago` INT(11) NULL,
  `cuotas_json` TEXT NULL,
  `id_usuario_registro` INT(11) NOT NULL,
  `id_usuario_aprobacion` INT(11) NULL,
  `fecha_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_aprobacion` TIMESTAMP NULL,
  `estado` ENUM('pendiente', 'aprobado', 'rechazado') NOT NULL DEFAULT 'pendiente',
  `observaciones` TEXT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## NOTAS IMPORTANTES

1. **El rol 3 (Director) NO requiere aprobación:** Sus pagos se procesan directamente como antes.

2. **Los datos del pago se guardan en el campo `observaciones`** en formato JSON para poder recuperarlos al aprobar.

3. **El sistema es compatible con pagos al contado y financiados** para conductores.

4. **Para clientes, solo se maneja pago al contado** (según la estructura actual).

5. **El contador de pagos pendientes se actualiza automáticamente** al cargar la página y después de cada acción.

6. **Los estilos del modal son consistentes** con el modal de pagos-financiamiento.php.

---

## ARCHIVOS LISTADOS

1. ✅ `app/models/PagosPendientesInscripcion.php` (CREADO)
2. ✅ `app/http/controllers/PagosPendientesInscripcionController.php` (CREADO)
3. ✅ `app/http/controllers/RegistroPagoController.php` (MODIFICADO)
4. ✅ `routes/web.php` (MODIFICADO)
5. ✅ `resources/views/fragment-views/cliente/pago-inscrip-conductor.php` (MODIFICADO)
6. ✅ `resources/views/fragment-views/cliente/pagos-inscripcion.php` (MODIFICADO)

---

## PRÓXIMOS PASOS (PENDIENTES)

Para completar la implementación, se debe:

1. **Implementar la lógica de aprobación completa** en `PagosPendientesInscripcionController.php`:
   - Método `aprobarPagoConductor()` - Insertar en todas las tablas necesarias
   - Método `aprobarPagoCliente()` - Insertar en cliente_pago
   - Generar notas de venta al aprobar

2. **Agregar el mismo sistema en la vista de registro de clientes** (`regis-cliente.php`):
   - Modificar el controlador que maneja el registro de pagos de clientes
   - Agregar el botón y modal similar

3. **Pruebas exhaustivas** con diferentes escenarios:
   - Pago al contado con rol 1, 2 y 3
   - Pago financiado con rol 1, 2 y 3
   - Aprobación y rechazo de pagos
   - Reactivación de pagos rechazados

---

**Fecha de implementación:** 16 de octubre de 2025
**Desarrollado por:** Kiro AI Assistant
